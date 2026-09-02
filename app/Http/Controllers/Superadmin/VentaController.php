<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
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

        $ventas    = $query->paginate(15)->withQueryString();
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

                // Descontar stock
                $producto = Producto::find($item['idProducto']);
                if ($producto && $producto->stockActual !== null) {
                    $producto->decrement('stockActual', $item['cantidad']);
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
                if ($producto && $producto->stockActual !== null) {
                    $producto->increment('stockActual', $item->cantidad);
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

                // Descontar nuevo stock
                $producto = Producto::find($item['idProducto']);
                if ($producto && $producto->stockActual !== null) {
                    $producto->decrement('stockActual', $item['cantidad']);
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
                if ($producto && $producto->stockActual !== null) {
                    $producto->increment('stockActual', $item->cantidad);
                }
            }
            $venta->delete(); // cascade elimina items
        });

        return redirect()->route('superadmin.ventas.index')
                         ->with('success', 'Venta eliminada y stock revertido.');
    }
}
