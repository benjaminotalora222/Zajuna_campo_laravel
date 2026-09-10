<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Superadmin\ActividadController;
use App\Http\Controllers\Superadmin\AuditoriaController;
use App\Http\Controllers\Superadmin\CategoriaController;
use App\Http\Controllers\Superadmin\ReporteController;
use App\Http\Controllers\Superadmin\ConsignacionController;
use App\Http\Controllers\Superadmin\CronogramaController;
use App\Http\Controllers\Superadmin\EjecucionController;
use App\Http\Controllers\Superadmin\InventarioController as SuperadminInventarioController;
use App\Http\Controllers\Superadmin\ProyectoController;
use App\Http\Controllers\Superadmin\ProductoController;
use App\Http\Controllers\Superadmin\ProveedorController;
use App\Http\Controllers\Superadmin\UsuarioController;
use App\Http\Controllers\Operativo\ActividadController as OperativoActividadController;
use App\Http\Controllers\Operativo\ConsignacionController as OperativoConsignacionController;
use App\Http\Controllers\Operativo\CronogramaController as OperativoCronogramaController;
use App\Http\Controllers\Operativo\EjecucionController as OperativoEjecucionController;
use App\Http\Controllers\Operativo\InventarioController as OperativoInventarioController;
use App\Http\Controllers\Operativo\ProyectoController as OperativoProyectoController;
use App\Http\Controllers\Operativo\VentaController as OperativoVentaController;
use App\Http\Controllers\Superadmin\ConfiguracionController;
use App\Http\Controllers\Superadmin\VentaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta legacy dashboard — redirige según rol
Route::get('/dashboard', function () {
    $roleId = auth()->user()->role_id;
    return match($roleId) {
        1 => redirect()->route('superadmin.dashboard'),
        2 => redirect()->route('operativo.dashboard'),
        3 => redirect()->route('proveedor.dashboard'),
        default => redirect()->route('superadmin.dashboard'),
    };
})->middleware(['auth'])->name('dashboard');

// ── Superadmin (rol 1) ──────────────────────────────────────
Route::middleware(['auth'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role_id !== 1) abort(403);

        $stats = [
            'proveedores'  => \Illuminate\Support\Facades\DB::table('proveedor')->where('estado', true)->count(),
            'productos'    => \Illuminate\Support\Facades\DB::table('producto')->count(),
            'stock_bajo'   => \App\Models\Producto::whereNotNull('stockMinimo')
                                ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) <= stockMinimo')
                                ->whereRaw('(SELECT COALESCE(SUM(cantidad_disponible),0) FROM lotes WHERE lotes.producto_id = producto.id) > 0')
                                ->count(),
            'ventas_mes'   => \Illuminate\Support\Facades\DB::table('venta')
                                ->whereMonth('fechaVenta', now()->month)
                                ->whereYear('fechaVenta', now()->year)
                                ->count(),
            'ventas_total' => \Illuminate\Support\Facades\DB::table('venta')
                                ->whereMonth('fechaVenta', now()->month)
                                ->whereYear('fechaVenta', now()->year)
                                ->sum('total'),
            'proyectos'    => \Illuminate\Support\Facades\DB::table('proyectoinvestigacion')->count(),
            'usuarios'     => \App\Models\User::where('activo', true)->count(),
            'alertas'      => \Illuminate\Support\Facades\DB::table('alerta')->where('leida', false)->count(),
        ];

        $alertas = \Illuminate\Support\Facades\DB::table('alerta')
            ->where('leida', false)
            ->orderByDesc('fechaGeneracion')
            ->limit(5)
            ->get();

        $logs = \Illuminate\Support\Facades\DB::table('logauditoria')
            ->orderByDesc('fechaHora')
            ->limit(6)
            ->get();

        $proyectos = \Illuminate\Support\Facades\DB::table('proyectoinvestigacion')
            ->select('nombre', 'porcentajeAvance')
            ->orderByDesc('porcentajeAvance')
            ->limit(4)
            ->get();

        return view('superadmin.dashboard', compact('stats', 'alertas', 'logs', 'proyectos'));
    })->name('dashboard');

    // ── Módulo Usuarios y Roles ──
    Route::resource('usuarios', UsuarioController::class);
    Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle');

    // ── Módulo Proveedores ──
    Route::resource('proveedores', ProveedorController::class)->except(['show', 'create', 'edit'])->parameters(['proveedores' => 'proveedor']);
    Route::patch('proveedores/{proveedor}/toggle', [ProveedorController::class, 'toggleEstado'])->name('proveedores.toggle');

    // ── Módulo Productos ──
    Route::resource('productos', ProductoController::class)->except(['show', 'create', 'edit'])->parameters(['productos' => 'producto']);

    // ── Módulo Categorías ──
    Route::resource('categorias', CategoriaController::class)->except(['create', 'edit']);
    Route::patch('categorias/{categoria}/toggle', [CategoriaController::class, 'toggleActivo'])->name('categorias.toggle');

    // ── Módulo Ventas ──
    Route::resource('ventas', VentaController::class)->except(['show', 'create', 'edit'])->parameters(['ventas' => 'venta']);
    Route::get('ventas/producto/{producto}/precio', [VentaController::class, 'precioProducto'])->name('ventas.precio');

    // ── Módulo Inventario y Consignación ──
    Route::resource('consignacion', ConsignacionController::class)->except(['show', 'create', 'edit']);

    // ── Módulo Inventario (lotes + movimientos) ──
    Route::get('inventario', [SuperadminInventarioController::class, 'index'])->name('inventario.index');
    Route::get('inventario/{producto}', [SuperadminInventarioController::class, 'show'])->name('inventario.show');
    Route::post('inventario/{producto}/entrada', [SuperadminInventarioController::class, 'storeEntrada'])->name('inventario.entrada');
    Route::post('inventario/{producto}/salida', [SuperadminInventarioController::class, 'storeSalida'])->name('inventario.salida');
    Route::put('inventario/movimiento/{movimiento}', [SuperadminInventarioController::class, 'updateMovimiento'])->name('inventario.movimiento.update');

    // ── Módulo Cronograma de Espacios ──
    Route::get('cronograma', [CronogramaController::class, 'index'])->name('cronograma.index');
    Route::post('cronograma', [CronogramaController::class, 'store'])->name('cronograma.store');
    Route::get('cronograma/{evento}', [CronogramaController::class, 'show'])->name('cronograma.show');
    Route::put('cronograma/{evento}', [CronogramaController::class, 'update'])->name('cronograma.update');
    Route::delete('cronograma/{evento}', [CronogramaController::class, 'destroy'])->name('cronograma.destroy');

    // ── Módulo Auditoría ──
    Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');

    // ── Módulo Configuración ──
    Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');

    // ── Módulo Reportes ──
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/pdf', [ReporteController::class, 'exportarPdf'])->name('reportes.pdf');

    // ── Módulo Proyectos ──
    Route::resource('proyectos', ProyectoController::class)->except(['show', 'create', 'edit']);

    // ── Módulo Ejecución de Actividades ──
    Route::resource('actividades', ActividadController::class)->except(['show', 'create', 'edit']);

    // ── Módulo Ejecución de Actividades (Kanban) ──
    // ── Módulo Tareas y Avances (Kanban) ──
    Route::get('ejecucion', [EjecucionController::class, 'index'])->name('ejecucion.index');
    Route::get('tareas', [EjecucionController::class, 'index'])->name('tareas.index'); // alias para sidebar
    Route::post('ejecuci    on', [EjecucionController::class, 'store'])->name('ejecucion.store');
    Route::get('ejecucion/{tarea}', [EjecucionController::class, 'show'])->name('ejecucion.show');
    Route::put('ejecucion/{tarea}', [EjecucionController::class, 'update'])->name('ejecucion.update');
    Route::patch('ejecucion/{tarea}/estado', [EjecucionController::class, 'moverEstado'])->name('ejecucion.estado');
    Route::delete('ejecucion/{tarea}', [EjecucionController::class, 'destroy'])->name('ejecucion.destroy');
});

// ── Operativo (rol 2) ───────────────────────────────────────
Route::middleware(['auth'])->prefix('operativo')->name('operativo.')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role_id !== 2) abort(403);
        return view('operativo.dashboard');
    })->name('dashboard');

    // Minimarket
    Route::resource('consignacion', OperativoConsignacionController::class)->except(['show', 'create', 'edit']);
    Route::resource('ventas', OperativoVentaController::class)->except(['show', 'create', 'edit'])->parameters(['ventas' => 'venta']);
    Route::get('ventas/producto/{producto}/precio', [OperativoVentaController::class, 'precioProducto'])->name('ventas.precio');

    // Inventario operativo (solo movimientos, sin editar ficha)
    Route::get('inventario', [OperativoInventarioController::class, 'index'])->name('inventario.index');
    Route::get('inventario/{producto}', [OperativoInventarioController::class, 'show'])->name('inventario.show');
    Route::post('inventario/{producto}/entrada', [OperativoInventarioController::class, 'storeEntrada'])->name('inventario.entrada');
    Route::post('inventario/{producto}/salida', [OperativoInventarioController::class, 'storeSalida'])->name('inventario.salida');
    Route::put('inventario/movimiento/{movimiento}', [OperativoInventarioController::class, 'updateMovimiento'])->name('inventario.movimiento.update');

    // Transferencias y shows
    Route::get('cronograma', [OperativoCronogramaController::class, 'index'])->name('cronograma.index');
    Route::post('cronograma', [OperativoCronogramaController::class, 'store'])->name('cronograma.store');
    Route::get('cronograma/{evento}', [OperativoCronogramaController::class, 'show'])->name('cronograma.show');
    Route::put('cronograma/{evento}', [OperativoCronogramaController::class, 'update'])->name('cronograma.update');
    Route::delete('cronograma/{evento}', [OperativoCronogramaController::class, 'destroy'])->name('cronograma.destroy');

    Route::resource('actividades', OperativoActividadController::class)->except(['show', 'create', 'edit']);

    // Investigación
    Route::resource('proyectos', OperativoProyectoController::class)->except(['show', 'create', 'edit']);

    Route::get('ejecucion', [OperativoEjecucionController::class, 'index'])->name('ejecucion.index');
    Route::get('tareas', [OperativoEjecucionController::class, 'index'])->name('tareas.index');
    Route::post('ejecucion', [OperativoEjecucionController::class, 'store'])->name('ejecucion.store');
    Route::get('ejecucion/{tarea}', [OperativoEjecucionController::class, 'show'])->name('ejecucion.show');
    Route::put('ejecucion/{tarea}', [OperativoEjecucionController::class, 'update'])->name('ejecucion.update');
    Route::patch('ejecucion/{tarea}/estado', [OperativoEjecucionController::class, 'moverEstado'])->name('ejecucion.estado');
    Route::delete('ejecucion/{tarea}', [OperativoEjecucionController::class, 'destroy'])->name('ejecucion.destroy');
});

// ── Proveedor (rol 3) ───────────────────────────────────────
Route::middleware(['auth'])->prefix('proveedor')->name('proveedor.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Proveedor\DashboardController::class, 'index'])->name('dashboard');

    // Solo lectura
    Route::get('ventas', [\App\Http\Controllers\Proveedor\VentaController::class, 'index'])->name('ventas.index');
    Route::get('inventario', [\App\Http\Controllers\Proveedor\InventarioController::class, 'index'])->name('inventario.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
