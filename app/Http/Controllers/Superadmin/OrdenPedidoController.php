<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\NotificacionProveedor;
use App\Models\OrdenPedido;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Services\OrdenPedidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrdenPedidoProveedorMail;

class OrdenPedidoController extends Controller
{
    private function verificarAcceso(): void
    {
        $roleId = (int) auth()->user()->role_id;
        if (!in_array($roleId, [1, 2])) {
            abort(403, 'Acceso denegado al módulo de órdenes de pedido.');
        }
    }

    public function index(Request $request)
    {
        $this->verificarAcceso();

        $query = OrdenPedido::with(['proveedor', 'producto', 'user'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_orden', 'like', "%{$search}%")
                  ->orWhere('correo_destino', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', fn($p) => $p->where('nombre', 'like', "%{$search}%"))
                  ->orWhereHas('producto', fn($pr) => $pr->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        if ($proveedorId = $request->input('proveedor_id')) {
            $query->where('proveedor_id', $proveedorId);
        }

        $ordenes = $query->paginate(12)->withQueryString();
        $proveedores = Proveedor::orderBy('nombre')->get();

        // Productos actualmente con stock bajo o agotado para sugerir generación rápida
        $productosStockBajo = Producto::with(['lotes', 'proveedor'])
            ->where('activo', true)
            ->whereNotNull('stockMinimo')
            ->where('stockMinimo', '>', 0)
            ->get()
            ->filter(fn($p) => $p->stock_calculado <= $p->stockMinimo)
            ->values();

        $stats = [
            'total_ordenes'   => OrdenPedido::count(),
            'enviadas'        => OrdenPedido::where('estado', 'enviada')->count(),
            'recibidas'       => OrdenPedido::where('estado', 'recibida')->count(),
            'productos_alerta'=> $productosStockBajo->count(),
        ];

        return view('superadmin.ordenes.index', compact('ordenes', 'proveedores', 'productosStockBajo', 'stats'));
    }

    public function store(Request $request)
    {
        $this->verificarAcceso();

        $request->validate([
            'producto_id'         => 'required|exists:producto,id',
            'cantidad_solicitada' => 'required|integer|min:1|max:99999',
            'observaciones'       => 'nullable|string|max:500',
        ], [
            'producto_id.required'         => 'Debes seleccionar un producto.',
            'cantidad_solicitada.required' => 'La cantidad a pedir es obligatoria.',
            'cantidad_solicitada.min'      => 'La cantidad mínima a pedir es 1 unidad.',
        ]);

        $producto = Producto::with(['lotes', 'proveedor'])->findOrFail($request->producto_id);

        if (!$producto->proveedor) {
            return back()->with('error', "El producto '{$producto->nombre}' no tiene un proveedor asignado.");
        }

        $orden = OrdenPedidoService::generarOrdenStockMinimo(
            $producto,
            (int) $request->cantidad_solicitada,
            $request->observaciones,
            auth()->id()
        );

        $msg = "Orden de pedido #{$orden->numero_orden} generada correctamente.";
        if ($orden->correo_destino) {
            $msg .= " Se envió la notificación por correo a: {$orden->correo_destino}.";
        } else {
            $msg .= " (El proveedor no cuenta con correo electrónico registrado).";
        }

        $rutaRedireccion = (int) auth()->user()->role_id === 2 ? 'operativo.ordenes.index' : 'superadmin.ordenes.index';
        return redirect()->route($rutaRedireccion)->with('success', $msg);
    }

    public function reenviar(OrdenPedido $orden)
    {
        $this->verificarAcceso();
        $orden->loadMissing(['proveedor', 'producto']);

        $proveedor = $orden->proveedor;
        $correo = trim((string) ($orden->correo_destino ?: ($proveedor->correo ?? '')));

        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return back()->with('error', "El proveedor no tiene un correo electrónico válido configurado.");
        }

        try {
            Mail::to($correo)->send(new OrdenPedidoProveedorMail($orden, $proveedor, $orden->producto, $orden->observaciones));

            $orden->update([
                'fecha_envio'    => now(),
                'correo_destino' => $correo,
                'estado'         => 'enviada',
            ]);

            NotificacionProveedor::create([
                'orden_pedido_id' => $orden->id,
                'proveedor_id'    => $proveedor->id ?? null,
                'asunto'          => "Reenvío Orden de Pedido #{$orden->numero_orden}",
                'correo_destino'  => $correo,
                'mensaje'         => "Reenvío manual de la orden de pedido #{$orden->numero_orden}.",
                'estado_envio'    => 'enviado',
                'fecha_envio'     => now(),
            ]);

            LogAuditoria::registrar(
                'Inventario',
                'Reenvío Orden',
                "Se reenvió la orden de pedido #{$orden->numero_orden} al correo {$correo}."
            );

            return back()->with('success', "Orden de pedido #{$orden->numero_orden} reenviada con éxito a {$correo}.");
        } catch (\Throwable $e) {
            return back()->with('error', "No se pudo reenviar el correo: " . $e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, OrdenPedido $orden)
    {
        $this->verificarAcceso();

        $request->validate([
            'estado' => 'required|in:generada,enviada,recibida,cancelada',
        ]);

        $anterior = $orden->estado;
        $orden->update(['estado' => $request->estado]);

        LogAuditoria::registrar(
            'Inventario',
            'Estado Orden',
            "Orden de pedido #{$orden->numero_orden} cambió de estado '{$anterior}' a '{$request->estado}'."
        );

        return back()->with('success', "El estado de la orden #{$orden->numero_orden} se actualizó a '{$request->estado}'.");
    }

    public function revisarStockMinimo()
    {
        $this->verificarAcceso();

        $ordenes = OrdenPedidoService::revisarTodosProductosStockMinimo(auth()->id());
        $count = count($ordenes);

        if ($count === 0) {
            return back()->with('info', "No se encontraron productos en nivel crítico de stock que requieran órdenes nuevas.");
        }

        return back()->with('success', "Se generaron y enviaron {$count} órdenes de pedido a los proveedores por stock mínimo.");
    }
}

