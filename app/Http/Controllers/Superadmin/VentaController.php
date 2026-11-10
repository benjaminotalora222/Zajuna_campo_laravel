<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Venta;
use App\Models\VentaItem;
use Carbon\Carbon;
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

        if ($proveedorId = $request->input('proveedor_id')) {
            $query->whereHas('items.producto', fn($p) => $p->where('proveedor_id', $proveedorId));
        }

        if ($mes = $request->input('mes')) {
            $query->whereMonth('fechaVenta', $mes);
        }

        if ($anio = $request->input('anio')) {
            $query->whereYear('fechaVenta', $anio);
        }

        $ventas      = $query->paginate(10)->withQueryString();
        $productos   = Producto::where('activo', true)->orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre')->get();

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

        // Top productos más vendidos (respeta filtros de mes y año)
        $topProductosQuery = DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->join('producto', 'venta_items.idProducto', '=', 'producto.id');

        if ($proveedorId) {
            $topProductosQuery->where('producto.proveedor_id', $proveedorId);
        }
        if ($mes) {
            $topProductosQuery->whereMonth('venta.fechaVenta', $mes);
        }
        if ($anio) {
            $topProductosQuery->whereYear('venta.fechaVenta', $anio);
        }

        $topProductos = $topProductosQuery
            ->select(
                'producto.id',
                'producto.nombre',
                'producto.categoria',
                'producto.precio',
                DB::raw('SUM(venta_items.cantidad) as total_unidades'),
                DB::raw('SUM(venta_items.subtotal) as total_ventas'),
                DB::raw('COUNT(DISTINCT venta.id) as total_ordenes')
            )
            ->groupBy('producto.id', 'producto.nombre', 'producto.categoria', 'producto.precio')
            ->orderByDesc('total_unidades')
            ->orderByDesc('total_ventas')
            ->limit(5)
            ->get();

        $maxUnidadesTop = $topProductos->max('total_unidades') ?: 1;
        $totalVentasTop = $topProductos->sum('total_ventas') ?: 1;

        return view('superadmin.ventas.index', compact('ventas', 'productos', 'proveedores', 'stats', 'ventasMes', 'anios', 'topProductos', 'maxUnidadesTop', 'totalVentasTop'));
    }

    /**
     * Dashboard de ventas por proveedor y rango de fechas
     */
    public function dashboard(Request $request)
    {
        $this->soloSuperadmin();

        $proveedorId = $request->input('proveedor_id');
        $periodo     = $request->input('periodo', 'este_mes');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');

        $hoy = Carbon::today();

        // 1. Calcular rango de fechas
        if ($periodo === 'hoy') {
            $inicio = $hoy->copy()->startOfDay();
            $fin    = $hoy->copy()->endOfDay();
            $periodoTexto = 'Hoy (' . $inicio->format('d/m/Y') . ')';
        } elseif ($periodo === 'ayer') {
            $inicio = $hoy->copy()->subDay()->startOfDay();
            $fin    = $hoy->copy()->subDay()->endOfDay();
            $periodoTexto = 'Ayer (' . $inicio->format('d/m/Y') . ')';
        } elseif ($periodo === '7dias') {
            $inicio = $hoy->copy()->subDays(6)->startOfDay();
            $fin    = $hoy->copy()->endOfDay();
            $periodoTexto = 'Últimos 7 días (' . $inicio->format('d/m') . ' - ' . $fin->format('d/m/Y') . ')';
        } elseif ($periodo === '15dias') {
            $inicio = $hoy->copy()->subDays(14)->startOfDay();
            $fin    = $hoy->copy()->endOfDay();
            $periodoTexto = 'Últimos 15 días (' . $inicio->format('d/m') . ' - ' . $fin->format('d/m/Y') . ')';
        } elseif ($periodo === '30dias') {
            $inicio = $hoy->copy()->subDays(29)->startOfDay();
            $fin    = $hoy->copy()->endOfDay();
            $periodoTexto = 'Últimos 30 días (' . $inicio->format('d/m') . ' - ' . $fin->format('d/m/Y') . ')';
        } elseif ($periodo === 'este_mes') {
            $inicio = Carbon::now()->startOfMonth();
            $fin    = Carbon::now()->endOfMonth();
            $periodoTexto = 'Este mes (' . $inicio->locale('es')->isoFormat('MMMM YYYY') . ')';
        } elseif ($periodo === 'mes_anterior') {
            $inicio = Carbon::now()->subMonth()->startOfMonth();
            $fin    = Carbon::now()->subMonth()->endOfMonth();
            $periodoTexto = 'Mes anterior (' . $inicio->locale('es')->isoFormat('MMMM YYYY') . ')';
        } elseif ($periodo === 'anio_actual') {
            $inicio = Carbon::now()->startOfYear();
            $fin    = Carbon::now()->endOfYear();
            $periodoTexto = 'Año actual (' . $inicio->year . ')';
        } else {
            $periodo = 'rango';
            try {
                $inicio = $fechaInicio ? Carbon::parse($fechaInicio)->startOfDay() : Carbon::now()->startOfMonth();
            } catch (\Exception $e) {
                $inicio = Carbon::now()->startOfMonth();
            }
            try {
                $fin = $fechaFin ? Carbon::parse($fechaFin)->endOfDay() : Carbon::now()->endOfDay();
            } catch (\Exception $e) {
                $fin = Carbon::now()->endOfDay();
            }
            if ($inicio->gt($fin)) {
                $temp = $inicio;
                $inicio = $fin->copy()->startOfDay();
                $fin = $temp->copy()->endOfDay();
            }
            $periodoTexto = $inicio->isSameDay($fin)
                ? 'Día ' . $inicio->format('d/m/Y')
                : 'Del ' . $inicio->format('d/m/Y') . ' al ' . $fin->format('d/m/Y');
        }

        // Período anterior para variaciones
        $diasDiferencia = max(1, $inicio->diffInDays($fin) + 1);
        $inicioAnterior = $inicio->copy()->subDays($diasDiferencia)->startOfDay();
        $finAnterior    = $inicio->copy()->subSecond();

        // 2. Proveedores
        $proveedores = Proveedor::orderBy('nombre')->get();
        $proveedorActual = $proveedorId ? Proveedor::find($proveedorId) : null;

        // 3. Query base
        $baseItemsQuery = DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->join('producto', 'venta_items.idProducto', '=', 'producto.id')
            ->leftJoin('proveedor', 'producto.proveedor_id', '=', 'proveedor.id');

        if ($proveedorId) {
            $baseItemsQuery->where('producto.proveedor_id', $proveedorId);
        }

        // 4. KPIs
        $itemsPeriodo  = (clone $baseItemsQuery)->whereBetween('venta.fechaVenta', [$inicio, $fin]);
        $totalIngresos = (float) (clone $itemsPeriodo)->sum('venta_items.subtotal');
        $totalUnidades = (int) (clone $itemsPeriodo)->sum('venta_items.cantidad');
        $totalOrdenes  = (int) (clone $itemsPeriodo)->distinct('venta.id')->count('venta.id');
        $promedioVenta = $totalOrdenes > 0 ? round($totalIngresos / $totalOrdenes, 2) : 0;

        $itemsAnterior    = (clone $baseItemsQuery)->whereBetween('venta.fechaVenta', [$inicioAnterior, $finAnterior]);
        $ingresosAnterior = (float) (clone $itemsAnterior)->sum('venta_items.subtotal');
        $unidadesAnterior = (int) (clone $itemsAnterior)->sum('venta_items.cantidad');
        $ordenesAnterior  = (int) (clone $itemsAnterior)->distinct('venta.id')->count('venta.id');

        $varIngresos = $ingresosAnterior > 0 ? round((($totalIngresos - $ingresosAnterior) / $ingresosAnterior) * 100, 1) : 0;
        $varUnidades = $unidadesAnterior > 0 ? round((($totalUnidades - $unidadesAnterior) / $unidadesAnterior) * 100, 1) : 0;
        $varOrdenes  = $ordenesAnterior > 0 ? round((($totalOrdenes - $ordenesAnterior) / $ordenesAnterior) * 100, 1) : 0;

        // Participación en total tienda
        $totalTiendaIngresos = (float) DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->whereBetween('venta.fechaVenta', [$inicio, $fin])
            ->sum('venta_items.subtotal');
        $porcentajeParticipacion = $totalTiendaIngresos > 0 ? round(($totalIngresos / $totalTiendaIngresos) * 100, 1) : 0;

        // 5. Puntos de gráfico temporal
        $esPorDia = $diasDiferencia <= 31;
        $puntosGrafico = collect();

        if ($esPorDia) {
            $diasMap = (clone $itemsPeriodo)
                ->selectRaw('DATE(venta.fechaVenta) as fecha, SUM(venta_items.subtotal) as total, SUM(venta_items.cantidad) as cantidad, COUNT(DISTINCT venta.id) as ordenes')
                ->groupByRaw('DATE(venta.fechaVenta)')
                ->get()
                ->keyBy('fecha');

            $cursor = $inicio->copy();
            while ($cursor->lte($fin)) {
                $fStr = $cursor->format('Y-m-d');
                $d = $diasMap->get($fStr);
                $puntosGrafico->push([
                    'fecha'    => $fStr,
                    'label'    => $cursor->locale('es')->isoFormat('D MMM'),
                    'total'    => $d ? (float) $d->total : 0.0,
                    'cantidad' => $d ? (int) $d->cantidad : 0,
                    'ordenes'  => $d ? (int) $d->ordenes : 0,
                ]);
                $cursor->addDay();
            }
        } else {
            $mesesMap = (clone $itemsPeriodo)
                ->selectRaw('YEAR(venta.fechaVenta) as anio, MONTH(venta.fechaVenta) as mes, SUM(venta_items.subtotal) as total, SUM(venta_items.cantidad) as cantidad, COUNT(DISTINCT venta.id) as ordenes')
                ->groupByRaw('YEAR(venta.fechaVenta), MONTH(venta.fechaVenta)')
                ->get()
                ->keyBy(fn($row) => sprintf('%04d-%02d', $row->anio, $row->mes));

            $cursor = $inicio->copy()->startOfMonth();
            while ($cursor->lte($fin)) {
                $fKey = $cursor->format('Y-m');
                $d = $mesesMap->get($fKey);
                $puntosGrafico->push([
                    'fecha'    => $fKey,
                    'label'    => $cursor->locale('es')->isoFormat('MMM YYYY'),
                    'total'    => $d ? (float) $d->total : 0.0,
                    'cantidad' => $d ? (int) $d->cantidad : 0,
                    'ordenes'  => $d ? (int) $d->ordenes : 0,
                ]);
                $cursor->addMonth();
            }
        }

        $maxGraficoTotal = $puntosGrafico->max('total') ?: 1;

        // 6. Ranking de proveedores en este rango
        $rankingProveedores = DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->join('producto', 'venta_items.idProducto', '=', 'producto.id')
            ->leftJoin('proveedor', 'producto.proveedor_id', '=', 'proveedor.id')
            ->whereBetween('venta.fechaVenta', [$inicio, $fin])
            ->select(
                'producto.proveedor_id',
                DB::raw('COALESCE(proveedor.nombre, "Sin Proveedor") as proveedor_nombre'),
                'proveedor.codigo',
                'proveedor.tipo',
                'proveedor.estado',
                DB::raw('SUM(venta_items.subtotal) as total_ventas'),
                DB::raw('SUM(venta_items.cantidad) as total_unidades'),
                DB::raw('COUNT(DISTINCT venta.id) as total_ordenes')
            )
            ->groupBy('producto.proveedor_id', 'proveedor.nombre', 'proveedor.codigo', 'proveedor.tipo', 'proveedor.estado')
            ->orderByDesc('total_ventas')
            ->get();

        $maxVentasProveedor = $rankingProveedores->max('total_ventas') ?: 1;

        // 7. Top Productos más vendidos en el rango
        $topProductos = (clone $itemsPeriodo)
            ->select(
                'producto.id',
                'producto.nombre',
                'producto.categoria',
                'producto.precio',
                'producto.proveedor_id',
                DB::raw('COALESCE(proveedor.nombre, "Sin Proveedor") as proveedor_nombre'),
                DB::raw('SUM(venta_items.cantidad) as total_unidades'),
                DB::raw('SUM(venta_items.subtotal) as total_ventas'),
                DB::raw('COUNT(DISTINCT venta.id) as total_ordenes')
            )
            ->groupBy('producto.id', 'producto.nombre', 'producto.categoria', 'producto.precio', 'producto.proveedor_id', 'proveedor.nombre')
            ->orderByDesc('total_ventas')
            ->limit(8)
            ->get();

        $maxTopUnidades = $topProductos->max('total_unidades') ?: 1;

        // 8. Categorías
        $distribucionCategorias = (clone $itemsPeriodo)
            ->select(
                'producto.categoria',
                DB::raw('SUM(venta_items.subtotal) as total'),
                DB::raw('SUM(venta_items.cantidad) as unidades')
            )
            ->whereNotNull('producto.categoria')
            ->groupBy('producto.categoria')
            ->orderByDesc('total')
            ->get();

        $totalCategorias = $distribucionCategorias->sum('total') ?: 1;

        // 9. Ventas recientes
        $ventasRecientesQuery = Venta::with(['items.producto.proveedor', 'usuario'])
            ->whereBetween('fechaVenta', [$inicio, $fin]);

        if ($proveedorId) {
            $ventasRecientesQuery->whereHas('items.producto', fn($p) => $p->where('proveedor_id', $proveedorId));
        }

        $ventasRecientes = $ventasRecientesQuery
            ->orderByDesc('fechaVenta')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('superadmin.ventas.dashboard', compact(
            'proveedores', 'proveedorActual', 'proveedorId', 'periodo',
            'fechaInicio', 'fechaFin', 'inicio', 'fin', 'periodoTexto',
            'totalIngresos', 'totalUnidades', 'totalOrdenes', 'promedioVenta',
            'varIngresos', 'varUnidades', 'varOrdenes', 'porcentajeParticipacion', 'totalTiendaIngresos',
            'puntosGrafico', 'maxGraficoTotal', 'esPorDia',
            'rankingProveedores', 'maxVentasProveedor',
            'topProductos', 'maxTopUnidades',
            'distribucionCategorias', 'totalCategorias',
            'ventasRecientes'
        ));
    }

    public function store(Request $request)
    {
        $this->soloSuperadmin();

        $messages = [
            'fechaVenta.required'            => 'La fecha de la venta es obligatoria.',
            'cliente.regex'                  => 'El nombre del cliente solo puede contener letras y espacios (no se permiten números).',
            'items.required'                 => 'Debes agregar al menos un producto a la venta.',
            'items.min'                      => 'Debes agregar al menos un producto a la venta.',
            'items.*.cantidad.required'      => 'La cantidad es obligatoria.',
            'items.*.cantidad.integer'       => 'La cantidad debe ser un número entero.',
            'items.*.cantidad.min'           => 'La cantidad debe ser mayor a 0.',
            'items.*.cantidad.max'           => 'La cantidad no puede superar 999.999.',
            'items.*.precioUnitario.required'=> 'El precio unitario es obligatorio.',
            'items.*.precioUnitario.numeric' => 'El precio debe ser un número válido.',
            'items.*.precioUnitario.min'     => 'El precio no puede ser negativo.',
            'items.*.precioUnitario.max'     => 'El precio no puede exceder $99.999.999,99.',
        ];

        $request->validate([
            'fechaVenta'              => 'required|date',
            'cliente'                 => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\-\.\'\(\)]+$/u'],
            'observaciones'           => 'nullable|string|max:500',
            'items'                   => 'required|array|min:1',
            'items.*.idProducto'      => 'required|exists:producto,id',
            'items.*.cantidad'        => 'required|integer|min:1|max:999999',
            'items.*.precioUnitario'  => 'required|numeric|min:0|max:99999999.99',
        ], $messages);

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
                    \App\Services\OrdenPedidoService::verificarYGenerarAutomaticamente($producto, auth()->id());
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
