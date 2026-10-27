<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Tarea;
use App\Models\Actividad;
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

    private function calcularRango(string $periodo, int $anio, int $mes, ?string $fechaInicio = null, ?string $fechaFin = null, ?string $fecha = null): array
    {
        $hoy = Carbon::today();

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
                $ini = $fn->copy()->startOfDay();
                $fn = $temp->copy()->endOfDay();
            }

            $texto = $ini->isSameDay($fn)
                ? 'Día ' . $ini->format('d/m/Y')
                : 'Del ' . $ini->format('d/m/Y') . ' al ' . $fn->format('d/m/Y');

            return [$ini, $fn, $texto];
        }

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

        if ($periodo === 'hoy') {
            $ini = $hoy->copy()->startOfDay();
            $fn  = $hoy->copy()->endOfDay();
            $texto = 'Hoy (' . $ini->format('d/m/Y') . ')';
            return [$ini, $fn, $texto];
        }

        // Mensual por defecto
        $mesVal  = max(1, min(12, $mes));
        $anioVal = max(2000, min(2100, $anio));
        $ini = Carbon::create($anioVal, $mesVal, 1)->startOfMonth();
        $fn  = Carbon::create($anioVal, $mesVal, 1)->endOfMonth();
        $nombres = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                    7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
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

        $nombresMes = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                       7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];

        return view('superadmin.reportes.index', compact(
            'ventasTotales','varVentas','ordenesVenta','varOrdenes',
            'clientesAtendidos','varClientes','ventasMensuales',
            'topProductos','totalTopProductos','maxUnidadesTop','ventasCategoria','totalCategoria',
            'stockBajo','anio','mes','periodo','nombresMes','inicio','fin','periodoTexto',
            'fechaInicio','fechaFin','fecha'
        ));
    }

    public function exportarPdf(Request $request)
    {
        $this->soloSuperadmin();

        $modulo      = $request->input('modulo', 'ventas');
        $periodo     = $request->input('periodo', 'rango');
        $anio        = (int) $request->input('anio', now()->year);
        $mes         = (int) $request->input('mes',  now()->month);
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin    = $request->input('fecha_fin');
        $fecha       = $request->input('fecha');

        [$inicio, $fin, $periodoTexto] = $this->calcularRango($periodo, $anio, $mes, $fechaInicio, $fechaFin, $fecha);

        $datos = match ($modulo) {
            'productos'    => $this->datosProductos(),
            'mas_vendidos' => $this->datosMasVendidos($inicio, $fin),
            'ejecucion'    => $this->datosActividades($inicio, $fin),
            'tareas'       => $this->dareasTareas($inicio, $fin),
            'proyectos'    => $this->datosProyectos(),
            'inventario'   => $this->datosInventario(),
            default        => $this->datosVentas($inicio, $fin),
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

        $pdf = Pdf::loadView("superadmin.reportes.pdf.{$modulo}", array_merge($datos, [
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
        $ventas = Venta::with('items.producto')
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
}
