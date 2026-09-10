<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    /**
     * Descuenta stock de los lotes usando FEFO (primero en vencer, primero en salir).
     * Registra un MovimientoInventario por cada lote afectado.
     * Retorna true si había stock suficiente, false si no.
     */
    private function descontarStockFefo(Producto $producto, int $cantidad, string $motivo = 'Venta'): bool
    {
        $stockTotal = $producto->lotes()->where('cantidad_disponible', '>', 0)->sum('cantidad_disponible');

        if ($cantidad > $stockTotal) {
            return false;
        }

        $pendiente = $cantidad;
        $lotes = $producto->lotes()
            ->where('cantidad_disponible', '>', 0)
            ->orderByRaw('ISNULL(fecha_vencimiento), fecha_vencimiento ASC')
            ->get();

        foreach ($lotes as $lote) {
            if ($pendiente <= 0) break;
            $descuento = min($lote->cantidad_disponible, $pendiente);
            $lote->decrement('cantidad_disponible', $descuento);
            MovimientoInventario::create([
                'producto_id'     => $producto->id,
                'lote_id'         => $lote->id,
                'user_id'         => auth()->id(),
                'tipo'            => 'salida',
                'cantidad'        => $descuento,
                'motivo'          => $motivo,
                'fechaMovimiento' => now(),
            ]);
            $pendiente -= $descuento;
        }

        return true;
    }

    /**
     * Devuelve el stock descontado de los lotes al revertir una venta.
     */
    private function revertirStockFefo(Producto $producto, int $cantidad, string $motivo = 'Reverso de venta'): void
    {
        // Buscar los movimientos de salida más recientes de esta venta y revertirlos
        // usando la misma cantidad total como una entrada genérica al lote más reciente
        $lote = $producto->lotes()->orderByDesc('id')->first();

        if ($lote) {
            $lote->increment('cantidad_disponible', $cantidad);
            MovimientoInventario::create([
                'producto_id'     => $producto->id,
                'lote_id'         => $lote->id,
                'user_id'         => auth()->id(),
                'tipo'            => 'entrada',
                'cantidad'        => $cantidad,
                'motivo'          => $motivo,
                'fechaMovimiento' => now(),
            ]);
        }
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $query = Venta::with(['items.producto', 'usuario'])->orderByDesc('fechaVenta')->orderByDesc('id');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('cliente', 'like', "%{$search}%")
                  ->orWhereHas('items.producto', fn($p) => $p->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($mes = $request->input('mes')) {
            $query->whereMonth('fechaVenta', $mes);
        }

        if ($anio = $request->input('anio')) {
            $query->whereYear('fechaVenta', $anio);
        }

        $ventas    = $query->paginate(10)->withQueryString();
        $productos = Producto::where('activo', true)->orderBy('nombre')->get();

        $hoy   = now();
        $stats = [
            'total_mes'    => Venta::whereMonth('fechaVenta', $hoy->month)->whereYear('fechaVenta', $hoy->year)->count(),
            'ingresos_mes' => Venta::whereMonth('fechaVenta', $hoy->month)->whereYear('fechaVenta', $hoy->year)->sum('total'),
            'total_hoy'    => Venta::whereDate('fechaVenta', $hoy->toDateString())->count(),
            'ingresos_hoy' => Venta::whereDate('fechaVenta', $hoy->toDateString())->sum('total'),
        ];

        $ventasMes = collect(range(5, 0))->map(function ($i) use ($hoy) {
            $fecha = $hoy->copy()->subMonths($i);
            return [
                'mes'      => $fecha->locale('es')->isoFormat('MMM'),
                'cantidad' => Venta::whereMonth('fechaVenta', $fecha->month)->whereYear('fechaVenta', $fecha->year)->count(),
                'ingresos' => (float) Venta::whereMonth('fechaVenta', $fecha->month)->whereYear('fechaVenta', $fecha->year)->sum('total'),
            ];
        });

        $anios = Venta::selectRaw('YEAR(fechaVenta) as anio')
                      ->whereNotNull('fechaVenta')->distinct()->orderByDesc('anio')->pluck('anio');

        return view('superadmin.ventas.index', compact('ventas', 'productos', 'stats', 'ventasMes', 'anios'));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $request->validate([
            'fechaVenta'              => 'required|date',
            'cliente'                 => 'nullable|string|max:255',
            'observaciones'           => 'nullable|string|max:500',
            'items'                   => 'required|array|min:1',
            'items.*.idProducto'      => 'required|exists:producto,id',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.precioUnitario'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $total = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $subtotal    = $item['cantidad'] * $item['precioUnitario'];
                $total      += $subtotal;
                $itemsData[] = [
                    'idProducto'     => $item['idProducto'],
                    'cantidad'       => $item['cantidad'],
                    'precioUnitario' => $item['precioUnitario'],
                    'subtotal'       => $subtotal,
                ];

                // Descontar stock FEFO en lotes
                $producto = Producto::find($item['idProducto']);
                if ($producto) {
                    $this->descontarStockFefo($producto, $item['cantidad'], 'Venta - ' . ($request->cliente ?: 'Sin nombre'));
                }
            }

            $venta = Venta::create([
                'fechaVenta'    => $request->fechaVenta,
                'cliente'       => $request->cliente,
                'observaciones' => $request->observaciones,
                'total'         => $total,
                'idUsuario'     => auth()->id(),
            ]);

            foreach ($itemsData as $item) {
                $item['venta_id'] = $venta->id;
                VentaItem::create($item);
            }
        });

        LogAuditoria::registrar('Ventas', 'Creación', "Se registró una nueva venta al cliente: " . ($request->cliente ?: 'Sin nombre'));

        return redirect()->route('superadmin.ventas.index')
                         ->with('success', 'Venta registrada correctamente.');
    }

    public function update(Request $request, Venta $venta)
    {
        $this->soloSuperadmin();

        $request->validate([
            'fechaVenta'              => 'required|date',
            'cliente'                 => 'nullable|string|max:255',
            'observaciones'           => 'nullable|string|max:500',
            'items'                   => 'required|array|min:1',
            'items.*.idProducto'      => 'required|exists:producto,id',
            'items.*.cantidad'        => 'required|integer|min:1',
            'items.*.precioUnitario'  => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $venta) {
            // Revertir stock de ítems anteriores
            foreach ($venta->items as $item) {
                $producto = Producto::find($item->idProducto);
                if ($producto) {
                    $this->revertirStockFefo($producto, $item->cantidad, 'Reverso de venta (edición)');
                }
            }

            // Eliminar ítems anteriores
            $venta->items()->delete();

            $total = 0;
            foreach ($request->items as $item) {
                $subtotal = $item['cantidad'] * $item['precioUnitario'];
                $total   += $subtotal;

                VentaItem::create([
                    'venta_id'       => $venta->id,
                    'idProducto'     => $item['idProducto'],
                    'cantidad'       => $item['cantidad'],
                    'precioUnitario' => $item['precioUnitario'],
                    'subtotal'       => $subtotal,
                ]);

                // Descontar nuevo stock FEFO en lotes
                $producto = Producto::find($item['idProducto']);
                if ($producto) {
                    $this->descontarStockFefo($producto, $item['cantidad'], 'Venta (edición) - ' . ($request->cliente ?: 'Sin nombre'));
                }
            }

            $venta->update([
                'fechaVenta'    => $request->fechaVenta,
                'cliente'       => $request->cliente,
                'observaciones' => $request->observaciones,
                'total'         => $total,
            ]);
        });

        return redirect()->route('superadmin.ventas.index')
                         ->with('success', 'Venta actualizada correctamente.');
    }

    public function destroy(Venta $venta)
    {
        $this->soloSuperadmin();

        DB::transaction(function () use ($venta) {
            foreach ($venta->items as $item) {
                $producto = Producto::find($item->idProducto);
                if ($producto) {
                    $this->revertirStockFefo($producto, $item->cantidad, 'Reverso de venta (eliminación)');
                }
            }
            $venta->delete(); // cascade elimina items
        });

        return redirect()->route('superadmin.ventas.index')
                         ->with('success', 'Venta eliminada y stock revertido.');
    }
}
