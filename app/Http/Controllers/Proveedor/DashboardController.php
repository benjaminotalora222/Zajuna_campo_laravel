<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaItem;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role_id !== 3) abort(403);

        $proveedorId = auth()->user()->proveedor_id;

        if (! $proveedorId) {
            return view('proveedor.dashboard', [
                'stats'          => [],
                'productosAlerta'=> collect(),
                'ultimasVentas'  => collect(),
                'ventasMes'      => collect(),
                'sinProveedor'   => true,
            ]);
        }

        $hoy = now();

        // ── Métricas principales ───────────────────────────────
        $totalProductos = Producto::where('proveedor_id', $proveedorId)->where('activo', true)->count();

        $stockTotal = Producto::where('proveedor_id', $proveedorId)
            ->where('activo', true)
            ->withSum('lotes as stock', 'cantidad_disponible')
            ->get()->sum('stock');

        $proximosVencer = Producto::where('proveedor_id', $proveedorId)
            ->whereHas('lotes', fn($q) =>
                $q->where('cantidad_disponible', '>', 0)
                  ->whereNotNull('fecha_vencimiento')
                  ->whereRaw('DATEDIFF(fecha_vencimiento, NOW()) <= 30')
                  ->whereRaw('fecha_vencimiento > NOW()')
            )->count();

        $agotados = Producto::where('proveedor_id', $proveedorId)
            ->where('activo', true)
            ->whereDoesntHave('lotes', fn($q) => $q->where('cantidad_disponible', '>', 0))
            ->count();

        $ventasMesCount = Venta::whereHas('items.producto', fn($q) => $q->where('proveedor_id', $proveedorId))
            ->whereMonth('fechaVenta', $hoy->month)->whereYear('fechaVenta', $hoy->year)->count();

        $ingresosMes = VentaItem::whereHas('producto', fn($q) => $q->where('proveedor_id', $proveedorId))
            ->whereHas('venta', fn($q) => $q->whereMonth('fechaVenta', $hoy->month)->whereYear('fechaVenta', $hoy->year))
            ->sum('subtotal');

        $ventasHoy = Venta::whereHas('items.producto', fn($q) => $q->where('proveedor_id', $proveedorId))
            ->whereDate('fechaVenta', $hoy->toDateString())->count();

        $stats = [
            'total_productos'  => $totalProductos,
            'stock_total'      => (int) $stockTotal,
            'proximo_vencer'   => $proximosVencer,
            'agotados'         => $agotados,
            'ventas_mes'       => $ventasMesCount,
            'ingresos_mes'     => (float) $ingresosMes,
            'ventas_hoy'       => $ventasHoy,
        ];

        // ── Productos que necesitan atención ───────────────────
        $productosAlerta = Producto::with(['lotes' => fn($q) => $q->orderBy('fecha_vencimiento')])
            ->where('proveedor_id', $proveedorId)
            ->where('activo', true)
            ->whereHas('lotes', fn($q) =>
                $q->where('cantidad_disponible', '>', 0)
                  ->whereNotNull('fecha_vencimiento')
                  ->whereRaw('DATEDIFF(fecha_vencimiento, NOW()) <= 30')
                  ->whereRaw('fecha_vencimiento > NOW()')
            )
            ->orWhere(fn($q) =>
                $q->where('proveedor_id', $proveedorId)
                  ->where('activo', true)
                  ->whereDoesntHave('lotes', fn($l) => $l->where('cantidad_disponible', '>', 0))
            )
            ->orderBy('nombre')
            ->limit(5)
            ->get();

        // ── Últimas ventas ─────────────────────────────────────
        $ultimasVentas = Venta::with(['items.producto'])
            ->whereHas('items.producto', fn($q) => $q->where('proveedor_id', $proveedorId))
            ->orderByDesc('fechaVenta')
            ->limit(5)
            ->get();

        // ── Ventas por mes (últimos 6 meses) ───────────────────
        $ventasMes = collect(range(5, 0))->map(function ($i) use ($hoy, $proveedorId) {
            $fecha = $hoy->copy()->subMonths($i);
            return [
                'mes'      => $fecha->locale('es')->isoFormat('MMM'),
                'cantidad' => Venta::whereHas('items.producto', fn($q) => $q->where('proveedor_id', $proveedorId))
                                ->whereMonth('fechaVenta', $fecha->month)
                                ->whereYear('fechaVenta', $fecha->year)
                                ->count(),
                'ingresos' => (float) VentaItem::whereHas('producto', fn($q) => $q->where('proveedor_id', $proveedorId))
                                ->whereHas('venta', fn($q) => $q->whereMonth('fechaVenta', $fecha->month)->whereYear('fechaVenta', $fecha->year))
                                ->sum('subtotal'),
            ];
        });

        return view('proveedor.dashboard', compact('stats', 'productosAlerta', 'ultimasVentas', 'ventasMes'));
    }
}
