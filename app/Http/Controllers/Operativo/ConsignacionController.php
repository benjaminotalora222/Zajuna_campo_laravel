<?php

namespace App\Http\Controllers\Operativo;

use App\Http\Controllers\Controller;
use App\Models\Lote;
use App\Models\LogAuditoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ConsignacionController extends Controller
{
    private function soloOperativo()
    {
        if (!in_array(auth()->user()->role_id, [1, 2])) abort(403);
    }

    public function index(Request $request)
    {
        $this->soloOperativo();

        $query = Producto::with([
            'proveedor',
            'lotes' => fn($q) => $q->where('cantidad_disponible', '>', 0)->orderBy('fecha_vencimiento'),
        ])->where('activo', true);

        if ($search = $request->input('search')) {
            $query->where(fn($q) => $q
                ->where('nombre', 'like', "%{$search}%")
                ->orWhere('codigoBarras', 'like', "%{$search}%")
                ->orWhereHas('proveedor', fn($p) => $p->where('nombre', 'like', "%{$search}%"))
            );
        }

        if ($proveedorId = $request->input('proveedor_id')) {
            $query->where('proveedor_id', $proveedorId);
        }

        if ($request->input('estado') === 'bajo') {
            $query->whereNotNull('stockMinimo')
                  ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) <= stockMinimo')
                  ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) > 0');
        } elseif ($request->input('estado') === 'agotado') {
            $query->whereDoesntHave('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0));
        } elseif ($request->input('estado') === 'proximo_vencer') {
            $query->whereHas('lotes', fn($q) =>
                $q->where('cantidad_disponible', '>', 0)
                  ->whereNotNull('fecha_vencimiento')
                  ->whereRaw('DATEDIFF(fecha_vencimiento, NOW()) <= 30')
                  ->whereRaw('fecha_vencimiento > NOW()')
            );
        }

        $productos   = $query->paginate(10)->withQueryString();
        $proveedores = Proveedor::orderBy('nombre')->get(['id', 'nombre']);

        $stats = [
            'total_productos' => Producto::where('activo', true)->count(),
            'stock_total'     => (int) \DB::table('lotes')->sum('cantidad_disponible'),
            'proximo_vencer'  => Producto::whereHas('lotes', fn($q) =>
                                    $q->where('cantidad_disponible', '>', 0)
                                      ->whereNotNull('fecha_vencimiento')
                                      ->whereRaw('DATEDIFF(fecha_vencimiento, NOW()) <= 30')
                                      ->whereRaw('fecha_vencimiento > NOW()')
                                  )->count(),
            'proveedores'     => Proveedor::where('estado', true)->count(),
        ];

        return view('operativo.consignacion.index', compact('productos', 'proveedores', 'stats'));
    }

    // ── Registrar ENTRADA (crea un lote nuevo) ──────────────────

    public function store(Request $request)
    {
        $this->soloOperativo();

        $data = $request->validate([
            'producto_id'       => 'required|exists:producto,id',
            'cantidad'          => 'required|integer|min:1',
            'fecha_entrada'     => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha_entrada',
            'notas'             => 'nullable|string|max:500',
        ]);

        $producto = Producto::findOrFail($data['producto_id']);

        $lote = Lote::create([
            'producto_id'         => $producto->id,
            'cantidad_inicial'    => $data['cantidad'],
            'cantidad_disponible' => $data['cantidad'],
            'fecha_entrada'       => $data['fecha_entrada'],
            'fecha_vencimiento'   => $data['fecha_vencimiento'] ?? null,
            'notas'               => $data['notas'] ?? null,
        ]);

        MovimientoInventario::create([
            'producto_id'     => $producto->id,
            'lote_id'         => $lote->id,
            'user_id'         => auth()->id(),
            'tipo'            => 'entrada',
            'cantidad'        => $data['cantidad'],
            'motivo'          => $data['notas'] ?? 'Entrada de lote',
            'fechaMovimiento' => now(),
        ]);

        LogAuditoria::registrar('Inventario', 'Creación',
            "Entrada de {$data['cantidad']} unidades de {$producto->nombre} (lote #{$lote->id})");

        return redirect()->route('operativo.consignacion.index')
            ->with('success', "Entrada de {$data['cantidad']} {$producto->unidad} de {$producto->nombre} registrada correctamente.");
    }

    // ── update/destroy no aplican (redirigir al inventario real) ──

    public function update(Request $request, $id)
    {
        return redirect()->route('operativo.consignacion.index');
    }

    public function destroy($id)
    {
        return redirect()->route('operativo.consignacion.index');
    }
}
