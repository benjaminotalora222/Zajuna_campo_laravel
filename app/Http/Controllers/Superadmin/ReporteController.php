<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Tarea;
use App\Models\Actividad;
use App\Models\Evento;
use App\Models\Inventario;
use App\Models\ProyectoInvestigacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    private function soloSuperadmin()
    {
        if (auth()->user()->role_id !== 1) abort(403);
    }

    private function verificarPermisoReporte()
    {
        if (!in_array(auth()->user()->role_id, [1, 2])) abort(403);
    }

    private function calcularRango(string $periodo, int $anio, int $mes, ?string $fechaInicio = null, ?string $fechaFin = null, ?string $fecha = null): array
    {
        $hoy = Carbon::today();
        $nombres = [
            1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
            7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'
        ];

        // 1. Rango personalizado por calendario
        if ($periodo === 'rango' || $periodo === 'personalizado') {
            try {
                $ini = $fechaInicio ? Carbon::parse($fechaInicio)->startOfDay() : $hoy->copy()->startOfMonth();
            } catch (\Exception $e) {
                $ini = $hoy->copy()->startOfMonth();
            }

            try {
                $fn = $fechaFin ? Carbon::parse($fechaFin)->endOfDay() : $hoy->copy()->endOfDay();
            } catch (\Exception $e) {
                $fn = $hoy->copy()->endOfDay();
            }

            if ($ini->gt($fn)) {
                $temp = $ini;
                $ini  = $fn->copy()->startOfDay();
                $fn   = $temp->copy()->endOfDay();
            }

            $texto = $ini->isSameDay($fn)
                ? 'Día ' . $ini->format('d/m/Y')
                : 'Del ' . $ini->format('d/m/Y') . ' al ' . $fn->format('d/m/Y');

            return [$ini, $fn, $texto];
        }

        // 2. Día específico (calendario)
        if ($periodo === 'diario') {
            $target = $fecha ?: ($fechaInicio ?: null);
            try {
                $dateObj = $target ? Carbon::parse($target) : $hoy;
            } catch (\Exception $e) {
                $dateObj = $hoy;
            }
            $ini = $dateObj->copy()->startOfDay();
            $fn  = $dateObj->copy()->endOfDay();
            $texto = 'Día ' . $ini->format('d/m/Y');
            return [$ini, $fn, $texto];
        }

        // 3. Hoy
        if ($periodo === 'hoy') {
            $ini = $hoy->copy()->startOfDay();
            $fn  = $hoy->copy()->endOfDay();
            $texto = 'Hoy (' . $ini->format('d/m/Y') . ')';
            return [$ini, $fn, $texto];
        }

        // 4. Ayer
        if ($periodo === 'ayer') {
            $ini = $hoy->copy()->subDay()->startOfDay();
            $fn  = $hoy->copy()->subDay()->endOfDay();
            $texto = 'Ayer (' . $ini->format('d/m/Y') . ')';
            return [$ini, $fn, $texto];
        }

        // 5. Últimos 7 días
        if ($periodo === '7dias') {
            $ini = $hoy->copy()->subDays(6)->startOfDay();
            $fn  = $hoy->copy()->endOfDay();
            $texto = 'Últimos 7 días (' . $ini->format('d/m/Y') . ' al ' . $fn->format('d/m/Y') . ')';
            return [$ini, $fn, $texto];
        }

        // 6. Últimos 15 días
        if ($periodo === '15dias') {
            $ini = $hoy->copy()->subDays(14)->startOfDay();
            $fn  = $hoy->copy()->endOfDay();
            $texto = 'Últimos 15 días (' . $ini->format('d/m/Y') . ' al ' . $fn->format('d/m/Y') . ')';
            return [$ini, $fn, $texto];
        }

        // 7. Últimos 30 días
        if ($periodo === '30dias') {
            $ini = $hoy->copy()->subDays(29)->startOfDay();
            $fn  = $hoy->copy()->endOfDay();
            $texto = 'Últimos 30 días (' . $ini->format('d/m/Y') . ' al ' . $fn->format('d/m/Y') . ')';
            return [$ini, $fn, $texto];
        }

        // 8. Este mes
        if ($periodo === 'este_mes') {
            $ini = Carbon::now()->startOfMonth();
            $fn  = Carbon::now()->endOfMonth();
            $texto = 'Este mes (' . ($nombres[$ini->month] ?? $ini->format('m')) . ' ' . $ini->year . ')';
            return [$ini, $fn, $texto];
        }

        // 9. Mes anterior
        if ($periodo === 'mes_anterior') {
            $ini = Carbon::now()->subMonth()->startOfMonth();
            $fn  = Carbon::now()->subMonth()->endOfMonth();
            $texto = 'Mes anterior (' . ($nombres[$ini->month] ?? $ini->format('m')) . ' ' . $ini->year . ')';
            return [$ini, $fn, $texto];
        }

        // 10. Año actual o año específico
        if ($periodo === 'anio_actual' || $periodo === 'anual' || $periodo === 'anio') {
            $anioVal = ($anio >= 2000 && $anio <= 2100) ? $anio : Carbon::now()->year;
            $ini = Carbon::create($anioVal, 1, 1)->startOfYear();
            $fn  = Carbon::create($anioVal, 12, 31)->endOfYear();
            $texto = 'Año ' . $anioVal;
            return [$ini, $fn, $texto];
        }

        // 11. Semanal (esta semana)
        if ($periodo === 'semanal') {
            $target = $fecha ?: ($fechaInicio ?: null);
            try {
                $dateObj = $target ? Carbon::parse($target) : $hoy;
            } catch (\Exception $e) {
                $dateObj = $hoy;
            }
            $ini = $dateObj->copy()->startOfWeek();
            $fn  = $dateObj->copy()->endOfWeek();
            $texto = 'Semana del ' . $ini->format('d/m/Y') . ' al ' . $fn->format('d/m/Y');
            return [$ini, $fn, $texto];
        }

        // 12. Mensual (mes y año específicos)
        $mesVal  = max(1, min(12, $mes ?: Carbon::now()->month));
        $anioVal = max(2000, min(2100, $anio ?: Carbon::now()->year));
        $ini = Carbon::create($anioVal, $mesVal, 1)->startOfMonth();
        $fn  = Carbon::create($anioVal, $mesVal, 1)->endOfMonth();
        $texto = ($nombres[$mesVal] ?? 'Mes ' . $mesVal) . ' ' . $anioVal;
        return [$ini, $fn, $texto];
    }

    public function index(Request $request)
    {
        $this->soloSuperadmin();

        $anio        = (int) $request->input('anio', now()->year);
        $mes         = (int) $request->input('mes',  now()->month);
        $periodo     = $request->input('periodo', 'mensual');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');
        $fecha       = $request->input('fecha');

        [$inicio, $fin, $periodoTexto] = $this->calcularRango($periodo, $anio, $mes, $fechaInicio, $fechaFin, $fecha);

        if ($periodo === 'mensual') {
            $inicioAnterior = $inicio->copy()->subMonth()->startOfMonth();
            $finAnterior    = $inicio->copy()->subMonth()->endOfMonth();
        } else {
            $dias = max(1, $inicio->diffInDays($fin) + 1);
            $inicioAnterior = $inicio->copy()->subDays($dias)->startOfDay();
            $finAnterior    = $inicio->copy()->subSecond();
        }

        // KPIs
        $ventasTotales     = Venta::whereBetween('fechaVenta', [$inicio, $fin])->sum('total');
        $ventasAnteriores  = Venta::whereBetween('fechaVenta', [$inicioAnterior, $finAnterior])->sum('total');
        $ordenesVenta      = Venta::whereBetween('fechaVenta', [$inicio, $fin])->count();
        $ordenesAnteriores = Venta::whereBetween('fechaVenta', [$inicioAnterior, $finAnterior])->count();
        $clientesAtendidos  = Venta::whereBetween('fechaVenta', [$inicio, $fin])->whereNotNull('cliente')->distinct('cliente')->count('cliente');
        $clientesAnteriores = Venta::whereBetween('fechaVenta', [$inicioAnterior, $finAnterior])->whereNotNull('cliente')->distinct('cliente')->count('cliente');

        $varVentas   = $ventasAnteriores  > 0 ? round((($ventasTotales - $ventasAnteriores) / $ventasAnteriores) * 100, 1) : 0;
        $varOrdenes  = $ordenesAnteriores > 0 ? round((($ordenesVenta - $ordenesAnteriores) / $ordenesAnteriores) * 100, 1) : 0;
        $varClientes = $clientesAnteriores > 0 ? round((($clientesAtendidos - $clientesAnteriores) / $clientesAnteriores) * 100, 1) : 0;

        // Gráfica mensual
        $ventasMensuales = collect(range(5, 0))->map(function ($i) use ($inicio) {
            $fecha = $inicio->copy()->subMonths($i);
            return [
                'mes'      => $fecha->locale('es')->isoFormat('MMM'),
                'total'    => (float) Venta::whereYear('fechaVenta', $fecha->year)->whereMonth('fechaVenta', $fecha->month)->sum('total'),
                'cantidad' => Venta::whereYear('fechaVenta', $fecha->year)->whereMonth('fechaVenta', $fecha->month)->count(),
            ];
        });

        $topProductos = DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->join('producto', 'venta_items.idProducto', '=', 'producto.id')
            ->whereBetween('venta.fechaVenta', [$inicio, $fin])
            ->select(
                'producto.id',
                'producto.nombre',
                'producto.categoria',
                DB::raw('SUM(venta_items.subtotal) as total_ventas'),
                DB::raw('SUM(venta_items.cantidad) as unidades')
            )
            ->groupBy('producto.id', 'producto.nombre', 'producto.categoria')
            ->orderByDesc('unidades')
            ->orderByDesc('total_ventas')
            ->limit(8)
            ->get();

        $totalTopProductos = $topProductos->sum('total_ventas') ?: 1;
        $maxUnidadesTop    = $topProductos->max('unidades') ?: 1;

        $ventasCategoria = DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->join('producto', 'venta_items.idProducto', '=', 'producto.id')
            ->whereBetween('venta.fechaVenta', [$inicio, $fin])
            ->select('producto.categoria', DB::raw('SUM(venta_items.subtotal) as total'))
            ->groupBy('producto.categoria')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $totalCategoria = $ventasCategoria->sum('total') ?: 1;
        $stockBajo      = Producto::whereRaw('stockActual <= stockMinimo')->whereNotNull('stockMinimo')->count();
        $proveedores    = Proveedor::orderBy('nombre')->get();

        $nombresMes = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                       7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];

        return view('superadmin.reportes.index', compact(
            'ventasTotales','varVentas','ordenesVenta','varOrdenes',
            'clientesAtendidos','varClientes','ventasMensuales',
            'topProductos','totalTopProductos','maxUnidadesTop','ventasCategoria','totalCategoria',
            'stockBajo','anio','mes','periodo','nombresMes','inicio','fin','periodoTexto',
            'fechaInicio','fechaFin','fecha','proveedores'
        ));
    }

    public function exportarPdf(Request $request)
    {
        $this->verificarPermisoReporte();

        $modulo      = $request->input('modulo', 'ventas');
        $proveedorId = $request->input('proveedor_id');
        $periodo     = $request->input('periodo', 'rango');
        $anio        = (int) $request->input('anio', now()->year);
        $mes         = (int) $request->input('mes',  now()->month);
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');
        $fecha       = $request->input('fecha');

        [$inicio, $fin, $periodoTexto] = $this->calcularRango($periodo, $anio, $mes, $fechaInicio, $fechaFin, $fecha);

        $datos = match ($modulo) {
            'productos'            => $this->datosProductos(),
            'mas_vendidos'         => $this->datosMasVendidos($inicio, $fin),
            'ejecucion'            => $this->datosActividades($inicio, $fin),
            'transferencias_shows' => $this->datosTransferenciasShows($inicio, $fin, $request->input('tipo'), $request->input('espacio')),
            'tareas'               => $this->dareasTareas($inicio, $fin),
            'proyectos'            => $this->datosProyectos(),
            'inventario'           => $this->datosInventario(),
            'ventas_proveedor'     => $this->datosVentasProveedor($inicio, $fin, $proveedorId),
            default                => $proveedorId ? $this->datosVentasProveedor($inicio, $fin, $proveedorId) : $this->datosVentas($inicio, $fin),
        };

        $nombresMes = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                       7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];

        $usuario = auth()->user();

        $logoSenaFile = public_path('img/logo-sena.png');
        $logoZajunaFile = public_path('img/logo-zajuna-campo.png');

        $logoSena = file_exists($logoSenaFile)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoSenaFile))
            : $logoSenaFile;

        $logoZajuna = file_exists($logoZajunaFile)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoZajunaFile))
            : $logoZajunaFile;

        $viewName = match ($modulo) {
            'ventas_proveedor'     => 'superadmin.reportes.pdf.ventas',
            'transferencias_shows' => 'superadmin.reportes.pdf.transferencias_shows',
            default                => "superadmin.reportes.pdf.{$modulo}",
        };

        $pdf = Pdf::loadView($viewName, array_merge($datos, [
            'inicio'         => $inicio,
            'fin'            => $fin,
            'periodo'        => $periodo,
            'periodoTexto'   => $periodoTexto,
            'anio'           => $anio,
            'mes'            => $mes,
            'nombresMes'     => $nombresMes,
            'usuario'        => $usuario,
            'logoPath'       => $logoSena,
            'logoSenaPath'   => $logoSena,
            'logoZajunaPath' => $logoZajuna,
        ]))->setPaper('a4', 'portrait');

        $fechasArchivo = $inicio->isSameDay($fin)
            ? $inicio->format('Y-m-d')
            : "{$inicio->format('Y-m-d')}-a-{$fin->format('Y-m-d')}";

        $nombreArchivo = "reporte-{$modulo}-{$periodo}-{$fechasArchivo}.pdf";

        return $pdf->download($nombreArchivo);
    }

    public function exportarExcel(Request $request)
    {
        $this->verificarPermisoReporte();

        $modulo      = $request->input('modulo', 'transferencias_shows');
        $proveedorId = $request->input('proveedor_id');
        $periodo     = $request->input('periodo', 'rango');
        $anio        = (int) $request->input('anio', now()->year);
        $mes         = (int) $request->input('mes',  now()->month);
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');
        $fecha       = $request->input('fecha');
        $tipo        = $request->input('tipo');
        $espacio     = $request->input('espacio');

        [$inicio, $fin, $periodoTexto] = $this->calcularRango($periodo, $anio, $mes, $fechaInicio, $fechaFin, $fecha);

        $fechasArchivo = $inicio->isSameDay($fin)
            ? $inicio->format('Y-m-d')
            : "{$inicio->format('Y-m-d')}-a-{$fin->format('Y-m-d')}";

        if ($modulo === 'transferencias_shows') {
            $datos = $this->datosTransferenciasShows($inicio, $fin, $tipo, $espacio);
            $content = $this->generarExcelTransferenciasShows($datos, $periodoTexto, $inicio, $fin);
            $nombreArchivo = "informe-transferencias-y-shows-{$periodo}-{$fechasArchivo}.xls";
        } elseif ($modulo === 'ventas' || $modulo === 'ventas_proveedor') {
            $datos = $proveedorId ? $this->datosVentasProveedor($inicio, $fin, $proveedorId) : $this->datosVentas($inicio, $fin);
            $content = $this->generarExcelVentas($datos, $periodoTexto, $inicio, $fin);
            $nombreArchivo = "informe-ventas-{$periodo}-{$fechasArchivo}.xls";
        } else {
            $datos = $this->datosTransferenciasShows($inicio, $fin, $tipo, $espacio);
            $content = $this->generarExcelTransferenciasShows($datos, $periodoTexto, $inicio, $fin);
            $nombreArchivo = "informe-{$modulo}-{$periodo}-{$fechasArchivo}.xls";
        }

        return response("\xEF\xBB\xBF" . $content, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
            'Cache-Control'       => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
            'Pragma'              => 'public',
        ]);
    }

    private function datosMasVendidos($inicio, $fin): array
    {
        $productos = DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->join('producto', 'venta_items.idProducto', '=', 'producto.id')
            ->whereBetween('venta.fechaVenta', [$inicio, $fin])
            ->select(
                'producto.id',
                'producto.nombre',
                'producto.categoria',
                'producto.precio',
                DB::raw('SUM(venta_items.cantidad) as unidades'),
                DB::raw('SUM(venta_items.subtotal) as total_ventas'),
                DB::raw('COUNT(DISTINCT venta.id) as total_ordenes')
            )
            ->groupBy('producto.id', 'producto.nombre', 'producto.categoria', 'producto.precio')
            ->orderByDesc('unidades')
            ->orderByDesc('total_ventas')
            ->get();

        $totalUnidades    = $productos->sum('unidades');
        $totalIngresos    = $productos->sum('total_ventas');
        $totalProductos   = $productos->count();
        $productoEstrella = $productos->first();

        return compact('productos', 'totalUnidades', 'totalIngresos', 'totalProductos', 'productoEstrella');
    }

    private function datosVentas($inicio, $fin): array
    {
        $ventas = Venta::with(['items.producto', 'usuario'])
            ->whereBetween('fechaVenta', [$inicio, $fin])
            ->orderByDesc('fechaVenta')
            ->get();

        $totalIngresos  = $ventas->sum('total');
        $totalOrdenes   = $ventas->count();

        $topProductos = DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->join('producto', 'venta_items.idProducto', '=', 'producto.id')
            ->whereBetween('venta.fechaVenta', [$inicio, $fin])
            ->select('producto.id', 'producto.nombre', 'producto.categoria', DB::raw('SUM(venta_items.subtotal) as total_ventas'), DB::raw('SUM(venta_items.cantidad) as unidades'))
            ->groupBy('producto.id', 'producto.nombre', 'producto.categoria')
            ->orderByDesc('unidades')
            ->orderByDesc('total_ventas')
            ->limit(10)
            ->get();

        return compact('ventas','totalIngresos','totalOrdenes','topProductos');
    }

    private function datosVentasProveedor($inicio, $fin, $proveedorId = null): array
    {
        $proveedor = $proveedorId ? Proveedor::find($proveedorId) : null;

        $ventasQuery = Venta::with(['items.producto.proveedor', 'usuario'])
            ->whereBetween('fechaVenta', [$inicio, $fin]);

        if ($proveedorId) {
            $ventasQuery->whereHas('items.producto', fn($p) => $p->where('proveedor_id', $proveedorId));
        }

        $ventas = $ventasQuery->orderByDesc('fechaVenta')->get();

        $itemsQuery = DB::table('venta_items')
            ->join('venta', 'venta_items.venta_id', '=', 'venta.id')
            ->join('producto', 'venta_items.idProducto', '=', 'producto.id')
            ->whereBetween('venta.fechaVenta', [$inicio, $fin]);

        if ($proveedorId) {
            $itemsQuery->where('producto.proveedor_id', $proveedorId);
        }

        $totalIngresos = (float) (clone $itemsQuery)->sum('venta_items.subtotal');
        $totalUnidades = (int) (clone $itemsQuery)->sum('venta_items.cantidad');
        $totalOrdenes  = (int) (clone $itemsQuery)->distinct('venta.id')->count('venta.id');

        $topProductos = (clone $itemsQuery)
            ->select('producto.id', 'producto.nombre', 'producto.categoria', DB::raw('SUM(venta_items.subtotal) as total_ventas'), DB::raw('SUM(venta_items.cantidad) as unidades'))
            ->groupBy('producto.id', 'producto.nombre', 'producto.categoria')
            ->orderByDesc('unidades')
            ->orderByDesc('total_ventas')
            ->limit(10)
            ->get();

        return compact('ventas', 'totalIngresos', 'totalUnidades', 'totalOrdenes', 'topProductos', 'proveedor');
    }

    private function datosProductos(): array
    {
        $productos = Producto::orderBy('nombre')->get();
        $stats = [
            'total'   => $productos->count(),
            'activos' => $productos->where('activo', true)->count(),
            'bajo'    => $productos->filter(fn($p) => $p->stockMinimo && $p->stockActual <= $p->stockMinimo)->count(),
            'agotado' => $productos->where('stockActual', '<=', 0)->count(),
        ];
        return compact('productos','stats');
    }

    private function datosActividades($inicio, $fin): array
    {
        $actividades = Actividad::with(['proyecto','responsableUser'])
            ->whereBetween('created_at', [$inicio, $fin])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'        => $actividades->count(),
            'en_ejecucion' => $actividades->where('estado','en_ejecucion')->count(),
            'finalizado'   => $actividades->where('estado','finalizado')->count(),
            'pendiente'    => $actividades->where('estado','pendiente')->count(),
        ];
        return compact('actividades','stats');
    }

    private function datosProyectos(): array
    {
        $proyectos = ProyectoInvestigacion::with(['responsable', 'tareas'])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'       => $proyectos->count(),
            'en_progreso' => $proyectos->where('estado', 'en_progreso')->count(),
            'finalizado'  => $proyectos->where('estado', 'finalizado')->count(),
            'pendiente'   => $proyectos->where('estado', 'pendiente')->count(),
        ];

        return compact('proyectos', 'stats');
    }

    private function datosInventario(): array
    {
        $inventario = \App\Models\Inventario::with('producto')->get();

        $productos = Producto::orderBy('nombre')->get();

        $stats = [
            'total_items' => $inventario->count(),
            'total_prod'  => $productos->count(),
            'bajo'        => $productos->filter(fn($p) => $p->stockMinimo && $p->stockActual <= $p->stockMinimo)->count(),
            'agotado'     => $productos->where('stockActual', '<=', 0)->count(),
        ];

        return compact('inventario', 'productos', 'stats');
    }

    private function dareasTareas($inicio, $fin): array
    {
        $tareas = Tarea::with(['proyecto','responsable'])
            ->whereBetween('created_at', [$inicio, $fin])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total'       => $tareas->count(),
            'completada'  => $tareas->where('estado','completada')->count(),
            'en_progreso' => $tareas->where('estado','en_progreso')->count(),
            'por_iniciar' => $tareas->where('estado','por_iniciar')->count(),
        ];
        return compact('tareas','stats');
    }

    private function datosTransferenciasShows(Carbon $inicio, Carbon $fin, ?string $filtroTipo = null, ?string $filtroEspacio = null): array
    {
        $eventosQuery = Evento::with(['creadoPor', 'actividad'])
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()]);

        if ($filtroTipo) {
            $eventosQuery->where('tipo', $filtroTipo);
        }
        if ($filtroEspacio) {
            $eventosQuery->where('espacio', 'like', "%{$filtroEspacio}%");
        }

        $eventos = $eventosQuery->orderBy('fecha')->orderBy('hora_inicio')->get();

        $actividadesQuery = Actividad::with(['proyecto', 'responsableUser', 'evento'])
            ->where(function ($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_limite', [$inicio->toDateString(), $fin->toDateString()])
                  ->orWhereBetween('fecha_hora', [$inicio, $fin])
                  ->orWhereBetween('created_at', [$inicio, $fin]);
            });

        if ($filtroTipo) {
            $actividadesQuery->where('tipo', $filtroTipo);
        }
        if ($filtroEspacio) {
            $actividadesQuery->where('espacio_fisico', 'like', "%{$filtroEspacio}%");
        }

        $actividades = $actividadesQuery->orderBy('fecha_limite')->orderByDesc('created_at')->get();

        $totalEventos     = $eventos->count();
        $totalActividades = $actividades->count();
        $finalizadas      = $actividades->where('estado', 'finalizado')->count();
        $enEjecucion      = $actividades->where('estado', 'en_ejecucion')->count();
        $pendientes       = $actividades->where('estado', 'pendiente')->count();
        $tasaCierre       = $totalActividades > 0 ? round(($finalizadas / $totalActividades) * 100, 1) : 0;

        $espacios = $eventos->pluck('espacio')->concat($actividades->pluck('espacio_fisico'))
            ->filter()->countBy()->sortDesc()->take(8);

        $porTipo = $eventos->groupBy('tipo')->map(fn($g) => $g->count());

        $stats = compact(
            'totalEventos', 'totalActividades', 'finalizadas',
            'enEjecucion', 'pendientes', 'tasaCierre', 'espacios', 'porTipo'
        );

        return compact('eventos', 'actividades', 'stats');
    }

    private function generarExcelTransferenciasShows(array $datos, string $periodoTexto, Carbon $inicio, Carbon $fin): string
    {
        $eventos     = $datos['eventos'];
        $actividades = $datos['actividades'];
        $stats       = $datos['stats'];
        $usuario     = auth()->user();

        ob_start();
        ?>
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            body { font-family: Arial, sans-serif; font-size: 11px; color: #1c2b16; }
            .header-title { font-size: 16px; font-weight: bold; color: #71277a; }
            .header-meta { font-size: 10px; color: #555555; }
            .section-header { font-size: 12px; font-weight: bold; color: #ffffff; background-color: #71277a; padding: 6px 10px; }
            .section-header-green { font-size: 12px; font-weight: bold; color: #ffffff; background-color: #39a900; padding: 6px 10px; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
            th { background-color: #39a900; color: #ffffff; font-weight: bold; font-size: 10.5px; padding: 7px 9px; border: 1px solid #2d8500; text-align: left; }
            td { font-size: 10px; padding: 6px 9px; border: 1px solid #d4d4d4; }
            .kpi-title { background-color: #faf5ff; color: #71277a; font-weight: bold; font-size: 9px; text-transform: uppercase; border: 1px solid #e9d5ff; text-align: center; }
            .kpi-value { font-size: 15px; font-weight: bold; color: #1c2b16; text-align: center; border: 1px solid #e9d5ff; padding: 8px; }
            .td-center { text-align: center; }
        </style>
        </head>
        <body>
        <table>
            <tr><td colspan="8" class="header-title">ZAJUNA CAMPO — INFORME DE TRANSFERENCIAS Y SHOWS</td></tr>
            <tr><td colspan="8" class="header-meta">Generado por: <?= htmlspecialchars($usuario->name ?? 'Usuario') ?> | Fecha: <?= now()->format('d/m/Y H:i') ?> | Período: <?= htmlspecialchars($periodoTexto) ?> (<?= $inicio->format('d/m/Y') ?> al <?= $fin->format('d/m/Y') ?>)</td></tr>
            <tr><td colspan="8"></td></tr>
        </table>

        <!-- Resumen KPIs -->
        <table>
            <tr>
                <td class="kpi-title" colspan="2">EVENTOS PROGRAMADOS</td>
                <td class="kpi-title">TOTAL ACTIVIDADES</td>
                <td class="kpi-title">FINALIZADAS</td>
                <td class="kpi-title">EN EJECUCIÓN</td>
                <td class="kpi-title">PENDIENTES</td>
                <td class="kpi-title" colspan="2">TASA DE CUMPLIMIENTO</td>
            </tr>
            <tr>
                <td class="kpi-value" colspan="2"><?= $stats['totalEventos'] ?></td>
                <td class="kpi-value"><?= $stats['totalActividades'] ?></td>
                <td class="kpi-value" style="color:#15803d;"><?= $stats['finalizadas'] ?></td>
                <td class="kpi-value" style="color:#b45309;"><?= $stats['enEjecucion'] ?></td>
                <td class="kpi-value" style="color:#71277a;"><?= $stats['pendientes'] ?></td>
                <td class="kpi-value" style="color:#2563eb;" colspan="2"><?= $stats['tasaCierre'] ?>%</td>
            </tr>
            <tr><td colspan="8"></td></tr>
        </table>

        <!-- 1. Cronograma de Espacios y Shows -->
        <table>
            <tr><td colspan="8" class="section-header">1. CRONOGRAMA DE ESPACIOS Y SHOWS (EVENTOS PROGRAMADOS)</td></tr>
            <tr>
                <th style="width:35px;" class="td-center">#</th>
                <th style="width:85px;" class="td-center">Fecha</th>
                <th style="width:95px;" class="td-center">Horario</th>
                <th>Título del Evento / Show / Transferencia</th>
                <th style="width:110px;">Tipo</th>
                <th style="width:130px;">Espacio Físico</th>
                <th style="width:130px;">Programado por</th>
                <th style="width:100px;" class="td-center">Estado</th>
            </tr>
            <?php if ($eventos->isEmpty()): ?>
                <tr><td colspan="8" class="td-center" style="color:#888; padding:12px;">No se encontraron eventos ni shows programados en este período.</td></tr>
            <?php else: ?>
                <?php foreach($eventos as $i => $ev): ?>
                <tr>
                    <td class="td-center"><?= $i + 1 ?></td>
                    <td class="td-center"><?= $ev->fecha ? $ev->fecha->format('d/m/Y') : '—' ?></td>
                    <td class="td-center"><?= $ev->hora_inicio ? substr($ev->hora_inicio, 0, 5) : '—' ?> a <?= $ev->hora_fin ? substr($ev->hora_fin, 0, 5) : '—' ?></td>
                    <td><strong><?= htmlspecialchars($ev->titulo) ?></strong><?= $ev->descripcion ? ' - ' . htmlspecialchars($ev->descripcion) : '' ?></td>
                    <td><?= ucfirst(str_replace('_', ' ', $ev->tipo)) ?></td>
                    <td><?= htmlspecialchars($ev->espacio ?: 'Sin espacio asignado') ?></td>
                    <td><?= htmlspecialchars($ev->creadoPor->name ?? 'Sistema') ?></td>
                    <td class="td-center"><?= $ev->actividad ? ucfirst(str_replace('_', ' ', $ev->actividad->estado)) : 'Programado' ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr><td colspan="8"></td></tr>
        </table>

        <!-- 2. Ejecución de Actividades de Transferencias y Shows -->
        <table>
            <tr><td colspan="8" class="section-header-green">2. EJECUCIÓN DE ACTIVIDADES DE TRANSFERENCIAS Y SHOWS</td></tr>
            <tr>
                <th style="width:35px;" class="td-center">#</th>
                <th>Actividad / Tema</th>
                <th style="width:110px;">Tipo</th>
                <th style="width:130px;">Espacio Físico</th>
                <th style="width:130px;">Responsable</th>
                <th style="width:80px;" class="td-center">Progreso</th>
                <th style="width:100px;" class="td-center">Estado</th>
                <th style="width:85px;" class="td-center">Fecha Límite</th>
            </tr>
            <?php if ($actividades->isEmpty()): ?>
                <tr><td colspan="8" class="td-center" style="color:#888; padding:12px;">No se registraron actividades de ejecución en este período.</td></tr>
            <?php else: ?>
                <?php foreach($actividades as $i => $act): ?>
                <tr>
                    <td class="td-center"><?= $i + 1 ?></td>
                    <td>
                        <strong><?= htmlspecialchars($act->tema) ?></strong>
                        <?= $act->descripcion ? ' - ' . htmlspecialchars($act->descripcion) : '' ?>
                        <?= $act->proyecto ? ' [Proyecto: ' . htmlspecialchars($act->proyecto->nombre) . ']' : '' ?>
                    </td>
                    <td><?= ucfirst(str_replace('_', ' ', $act->tipo ?: 'Actividad')) ?></td>
                    <td><?= htmlspecialchars($act->espacio_fisico ?: '—') ?></td>
                    <td><?= htmlspecialchars($act->responsableUser->name ?? ($act->responsable ?: '—')) ?></td>
                    <td class="td-center"><?= $act->progreso ?? 0 ?>%</td>
                    <td class="td-center"><?= ucfirst(str_replace('_', ' ', $act->estado)) ?></td>
                    <td class="td-center"><?= $act->fecha_limite ? $act->fecha_limite->format('d/m/Y') : ($act->fecha_hora ? $act->fecha_hora->format('d/m/Y') : '—') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <tr><td colspan="8"></td></tr>
        </table>

        <!-- 3. Resumen de Ocupación por Espacio -->
        <?php if ($stats['espacios']->isNotEmpty()): ?>
        <table>
            <tr><td colspan="3" class="section-header">3. RESUMEN DE ESPACIOS FÍSICOS UTILIZADOS</td></tr>
            <tr>
                <th style="width:35px;" class="td-center">#</th>
                <th>Espacio Físico / Ubicación</th>
                <th style="width:140px;" class="td-center">Total Eventos / Actividades</th>
            </tr>
            <?php $espIdx = 1; foreach($stats['espacios'] as $espNombre => $cant): ?>
            <tr>
                <td class="td-center"><?= $espIdx++ ?></td>
                <td><strong><?= htmlspecialchars($espNombre) ?></strong></td>
                <td class="td-center"><?= $cant ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php endif; ?>

        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    private function generarExcelVentas(array $datos, string $periodoTexto, Carbon $inicio, Carbon $fin): string
    {
        $ventas        = $datos['ventas'];
        $totalIngresos = $datos['totalIngresos'];
        $totalOrdenes  = $datos['totalOrdenes'];
        $proveedor     = $datos['proveedor'] ?? null;
        $usuario       = auth()->user();

        ob_start();
        ?>
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            body { font-family: Arial, sans-serif; font-size: 11px; color: #1c2b16; }
            .header-title { font-size: 16px; font-weight: bold; color: #71277a; }
            .header-meta { font-size: 10px; color: #555555; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
            th { background-color: #39a900; color: #ffffff; font-weight: bold; font-size: 10.5px; padding: 7px 9px; border: 1px solid #2d8500; text-align: left; }
            td { font-size: 10px; padding: 6px 9px; border: 1px solid #d4d4d4; }
            .kpi-title { background-color: #faf5ff; color: #71277a; font-weight: bold; font-size: 9px; text-transform: uppercase; border: 1px solid #e9d5ff; text-align: center; }
            .kpi-value { font-size: 15px; font-weight: bold; color: #1c2b16; text-align: center; border: 1px solid #e9d5ff; padding: 8px; }
            .td-center { text-align: center; }
            .td-right { text-align: right; }
        </style>
        </head>
        <body>
        <table>
            <tr><td colspan="6" class="header-title">ZAJUNA CAMPO — REPORTE DE VENTAS <?= $proveedor ? '— ' . htmlspecialchars($proveedor->nombre) : '' ?></td></tr>
            <tr><td colspan="6" class="header-meta">Generado por: <?= htmlspecialchars($usuario->name ?? 'Usuario') ?> | Fecha: <?= now()->format('d/m/Y H:i') ?> | Período: <?= htmlspecialchars($periodoTexto) ?></td></tr>
            <tr><td colspan="6"></td></tr>
        </table>

        <table>
            <tr>
                <td class="kpi-title" colspan="2">ÓRDENES COMPLETADAS</td>
                <td class="kpi-title" colspan="2">TOTAL INGRESOS</td>
                <td class="kpi-title" colspan="2">PROMEDIO POR VENTA</td>
            </tr>
            <tr>
                <td class="kpi-value" colspan="2"><?= $totalOrdenes ?></td>
                <td class="kpi-value" colspan="2" style="color:#15803d;">$<?= number_format($totalIngresos, 0, ',', '.') ?></td>
                <td class="kpi-value" colspan="2">$<?= $totalOrdenes > 0 ? number_format($totalIngresos / $totalOrdenes, 0, ',', '.') : 0 ?></td>
            </tr>
            <tr><td colspan="6"></td></tr>
        </table>

        <table>
            <tr>
                <th style="width:40px;" class="td-center"># Venta</th>
                <th style="width:85px;" class="td-center">Fecha</th>
                <th>Cliente</th>
                <th>Atendido por</th>
                <th>Productos</th>
                <th style="width:100px;" class="td-right">Total</th>
            </tr>
            <?php foreach($ventas as $v): ?>
            <?php
                $itemsFiltrados = $proveedor ? $v->items->filter(fn($it) => $it->producto && $it->producto->proveedor_id == $proveedor->id) : $v->items;
                $totRow = $proveedor ? $itemsFiltrados->sum('subtotal') : $v->total;
                $prodsStr = $itemsFiltrados->map(fn($it) => ($it->producto->nombre ?? 'Producto') . ' x' . $it->cantidad . ' ($' . number_format($it->subtotal, 0, ',', '.') . ')')->implode(' | ');
            ?>
            <tr>
                <td class="td-center">#<?= str_pad($v->id, 4, '0', STR_PAD_LEFT) ?></td>
                <td class="td-center"><?= $v->fechaVenta ? \Carbon\Carbon::parse($v->fechaVenta)->format('d/m/Y H:i') : '—' ?></td>
                <td><?= htmlspecialchars($v->cliente ?: '—') ?></td>
                <td><?= htmlspecialchars($v->usuario->name ?? '—') ?></td>
                <td><?= htmlspecialchars($prodsStr) ?></td>
                <td class="td-right"><strong>$<?= number_format($totRow, 0, ',', '.') ?></strong></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="5" class="td-right"><strong>TOTAL</strong></td>
                <td class="td-right" style="color:#15803d;"><strong>$<?= number_format($totalIngresos, 0, ',', '.') ?></strong></td>
            </tr>
        </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
