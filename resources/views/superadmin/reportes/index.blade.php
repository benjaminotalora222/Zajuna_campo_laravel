@php
$title = 'Reportes';
$catColors = ['#fdc300','#71277a','#39a900','#ef4444','#3b82f6','#f97316','#8b5cf6','#06b6d4'];
$maxVentas = $ventasMensuales->max('total') ?: 1;
$nombresMesArr = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                  7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
@endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Reportes</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Análisis detallado del desempeño comercial y operativo.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Selector mes/año --}}
            <form method="GET" action="{{ route('superadmin.reportes.index') }}" class="flex items-center gap-2">
                <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border bg-white text-sm"
                     style="border-color:#e7e0cc;">
                    <svg class="w-4 h-4 shrink-0" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                    </svg>
                    <select name="mes" onchange="this.form.submit()" class="outline-none bg-transparent text-sm font-semibold" style="color:#1c2b16;">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $nombresMesArr[$m] }}</option>
                        @endfor
                    </select>
                    <select name="anio" onchange="this.form.submit()" class="outline-none bg-transparent text-sm font-semibold" style="color:#1c2b16;">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        {{-- Ventas Totales --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;">
                <svg class="w-6 h-6" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                    <path d="M9 12h3M12 9v3"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#9a9a8a;">Ventas Totales</p>
                <p class="text-xl font-extrabold" style="color:#1c2b16;">${{ number_format($ventasTotales, 0, ',', '.') }}</p>
                <p class="text-xs font-semibold mt-0.5" style="color:{{ $varVentas >= 0 ? '#39a900' : '#ef4444' }};">
                    {{ $varVentas >= 0 ? '↑' : '↓' }} {{ abs($varVentas) }}% vs. mes anterior
                </p>
            </div>
        </div>

        {{-- Órdenes de Venta --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#faf5ff;">
                <svg class="w-6 h-6" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                    <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#9a9a8a;">Órdenes de Venta</p>
                <p class="text-xl font-extrabold" style="color:#1c2b16;">{{ $ordenesVenta }}</p>
                <p class="text-xs font-semibold mt-0.5" style="color:{{ $varOrdenes >= 0 ? '#39a900' : '#ef4444' }};">
                    {{ $varOrdenes >= 0 ? '↑' : '↓' }} {{ abs($varOrdenes) }}% vs. mes anterior
                </p>
            </div>
        </div>

        {{-- Clientes Atendidos --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                <svg class="w-6 h-6" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2"/>
                    <circle cx="8.5" cy="7" r="3"/>
                    <path d="M16 3.5a3 3 0 0 1 0 6"/><path d="M17.5 17v-1.5a3.5 3.5 0 0 0-2-3.2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#9a9a8a;">Clientes Atendidos</p>
                <p class="text-xl font-extrabold" style="color:#1c2b16;">{{ $clientesAtendidos }}</p>
                <p class="text-xs font-semibold mt-0.5" style="color:{{ $varClientes >= 0 ? '#39a900' : '#ef4444' }};">
                    {{ $varClientes >= 0 ? '↑' : '↓' }} {{ abs($varClientes) }}% vs. mes anterior
                </p>
            </div>
        </div>

        {{-- Stock bajo --}}
        @php $stockBajo = \App\Models\Producto::whereRaw('stockActual <= stockMinimo')->whereNotNull('stockMinimo')->count(); @endphp
        <div class="rounded-2xl p-5 shadow-sm border flex items-center gap-4"
             style="background:#fffbeb; border-color:#fde68a;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#fef3c7;">
                <svg class="w-6 h-6" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#92400e;">Alertas Críticas</p>
                <p class="text-xl font-extrabold" style="color:#92400e;">{{ $stockBajo }}</p>
                <a href="{{ route('superadmin.productos.index', ['stock' => 'bajo']) }}"
                   class="text-xs font-semibold hover:underline" style="color:#d97706;">Ver alertas →</a>
            </div>
        </div>
    </div>

    {{-- GRÁFICAS FILA 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- Ventas Mensuales --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-extrabold" style="color:#1c2b16;">Ventas Mensuales</h3>
                <span class="text-xs px-3 py-1 rounded-full font-bold" style="background:#f3e8ff; color:#71277a;">Últimos 6 meses</span>
            </div>
            <div class="flex items-end gap-2" style="height:160px;">
                @foreach($ventasMensuales as $vm)
                @php
                    $pct    = round(($vm['total'] / $maxVentas) * 100);
                    $height = max(4, $pct * 1.4);
                    $isMax  = $vm['total'] == $ventasMensuales->max('total');
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1" style="height:160px; justify-content:flex-end;">
                    @if($isMax && $vm['total'] > 0)
                    <div class="text-[10px] font-bold px-1.5 py-0.5 rounded text-white mb-0.5"
                         style="background:#71277a; white-space:nowrap;">
                        ${{ number_format($vm['total'], 0, ',', '.') }}
                    </div>
                    @endif
                    <div class="w-full rounded-t-lg transition-all cursor-pointer hover:opacity-80"
                         style="height:{{ $height }}px; background:{{ $isMax ? '#fdc300' : '#e9d5ff' }};">
                    </div>
                    <span class="text-[10px] font-semibold" style="color:#9a9a8a;">{{ $vm['mes'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tendencia de Ventas (línea) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-sm font-extrabold" style="color:#1c2b16;">Tendencia de Ventas</h3>
                <span class="text-xs px-3 py-1 rounded-full font-bold" style="background:#f0fdf4; color:#166534;">6 meses</span>
            </div>
            @php
                $maxT = $ventasMensuales->max('total') ?: 1;
                $puntos = $ventasMensuales->map(fn($v) => round(($v['total'] / $maxT) * 120))->values();
                $svgWidth  = 100;
                $svgHeight = 120;
                $n = $puntos->count();
                $coords = $puntos->map(fn($v, $i) => [
                    'x' => round(($i / max($n-1, 1)) * $svgWidth),
                    'y' => $svgHeight - $v,
                ])->values();
                $polyline = $coords->map(fn($c) => "{$c['x']},{$c['y']}")->implode(' ');
                $areaPath = "M{$coords[0]['x']},{$svgHeight} " . $coords->map(fn($c) => "L{$c['x']},{$c['y']}")->implode(' ') . " L{$coords->last()['x']},{$svgHeight} Z";
            @endphp
            <div class="relative" style="height:160px;">
                <svg viewBox="0 0 100 120" preserveAspectRatio="none" class="w-full h-full">
                    <defs>
                        <linearGradient id="gradTendencia" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#fdc300" stop-opacity="0.3"/>
                            <stop offset="100%" stop-color="#fdc300" stop-opacity="0.02"/>
                        </linearGradient>
                    </defs>
                    <path d="{{ $areaPath }}" fill="url(#gradTendencia)"/>
                    <polyline points="{{ $polyline }}" fill="none" stroke="#fdc300" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                    @foreach($coords as $c)
                    <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="2" fill="#fdc300"/>
                    @endforeach
                </svg>
                {{-- Etiquetas meses --}}
                <div class="absolute bottom-0 left-0 right-0 flex justify-between px-1">
                    @foreach($ventasMensuales as $vm)
                    <span class="text-[10px]" style="color:#9a9a8a;">{{ $vm['mes'] }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- GENERADOR DE REPORTES PDF --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
        <h3 class="text-sm font-extrabold mb-4" style="color:#1c2b16;">Generar Reporte PDF</h3>
        <form method="GET" action="{{ route('superadmin.reportes.pdf') }}" target="_blank"
              class="flex flex-wrap gap-3 items-end">

            {{-- Módulo --}}
            <div class="flex-1 min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Módulo</label>
                <select name="modulo" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="ventas">Ventas</option>
                    <option value="productos">Productos</option>
                    <option value="ejecucion">Ejecución de Actividades</option>
                    <option value="tareas">Tareas y Avances</option>
                    <option value="proyectos">Proyectos de Investigación</option>
                    <option value="inventario">Inventario</option>
                </select>
            </div>

            {{-- Período --}}
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Período</label>
                <select name="periodo" id="selectPeriodo" onchange="toggleFechas(this.value)"
                        class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="diario">Diario (hoy)</option>
                    <option value="semanal">Semanal (esta semana)</option>
                    <option value="mensual" selected>Mensual</option>
                </select>
            </div>

            {{-- Mes/Año (solo mensual) --}}
            <div id="divMes" class="min-w-[130px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Mes</label>
                <select name="mes" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                            {{ $nombresMes[$m] }}
                        </option>
                    @endfor
                </select>
            </div>
            <div id="divAnio" class="min-w-[100px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Año</label>
                <select name="anio" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90 transition"
                    style="background:#71277a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Descargar PDF
            </button>
        </form>
    </div>

    {{-- FILA 2 --}}    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Ventas por Categoría --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-extrabold mb-5" style="color:#1c2b16;">Ventas por Categoría</h3>
            @if($ventasCategoria->count())
            <div class="space-y-3 mb-4">
                @foreach($ventasCategoria as $idx => $vc)
                @php $pct = round(($vc->total / $totalCategoria) * 100); @endphp
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:{{ $catColors[$idx % count($catColors)] }};"></span>
                            <span class="font-semibold" style="color:#1c2b16;">{{ $vc->categoria }}</span>
                        </div>
                        <span class="font-bold" style="color:#9a9a8a;">{{ $pct }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full overflow-hidden" style="background:#f3f0e8;">
                        <div class="h-full rounded-full" style="width:{{ $pct }}%; background:{{ $catColors[$idx % count($catColors)] }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="pt-3 border-t flex items-center justify-between" style="border-color:#f3f0e8;">
                <span class="text-xs" style="color:#9a9a8a;">Total</span>
                <span class="text-sm font-extrabold" style="color:#1c2b16;">${{ number_format($ventasCategoria->sum('total'), 0, ',', '.') }}</span>
            </div>
            @else
            <p class="text-xs text-center py-8" style="color:#9a9a8a;">Sin datos para este período</p>
            @endif
        </div>

        {{-- Top Productos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-extrabold mb-5" style="color:#1c2b16;">Top Productos</h3>
            @if($topProductos->count())
            <div class="space-y-0">
                <div class="grid grid-cols-3 gap-2 pb-2 border-b mb-2" style="border-color:#f3f0e8;">
                    <span class="text-[11px] font-bold uppercase tracking-wider" style="color:#9a9a8a;">Producto</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-right" style="color:#9a9a8a;">Ventas</span>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-right" style="color:#9a9a8a;">Part.</span>
                </div>
                @foreach($topProductos as $tp)
                @php $part = round(($tp->total_ventas / $totalTopProductos) * 100, 1); @endphp
                <div class="grid grid-cols-3 gap-2 py-2 border-b last:border-0" style="border-color:#f3f0e8;">
                    <span class="text-xs font-semibold truncate" style="color:#1c2b16;">{{ $tp->nombre }}</span>
                    <span class="text-xs text-right font-bold" style="color:#39a900;">${{ number_format($tp->total_ventas, 0, ',', '.') }}</span>
                    <span class="text-xs text-right" style="color:#9a9a8a;">{{ $part }}%</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('superadmin.productos.index') }}"
               class="block mt-3 text-xs font-bold hover:underline" style="color:#71277a;">
                Ver todos los productos →
            </a>
            @else
            <p class="text-xs text-center py-8" style="color:#9a9a8a;">Sin ventas en este período</p>
            @endif
        </div>

        {{-- Resumen operativo --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-extrabold mb-5" style="color:#1c2b16;">Resumen Operativo</h3>
            @php
                $totalProductos   = \App\Models\Producto::count();
                $productosActivos = \App\Models\Producto::where('activo', true)->count();
                $totalProveedores = \App\Models\Proveedor::count();
                $provActivos      = \App\Models\Proveedor::where('estado', true)->count();
                $totalProyectos   = \App\Models\ProyectoInvestigacion::count();
                $proyectosActivos = \App\Models\ProyectoInvestigacion::where('estado', 'en_progreso')->count();
            @endphp
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#faf5ff;">
                            <svg class="w-4 h-4" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold" style="color:#1c2b16;">Productos</p>
                            <p class="text-[11px]" style="color:#9a9a8a;">{{ $productosActivos }} activos</p>
                        </div>
                    </div>
                    <span class="text-lg font-extrabold" style="color:#71277a;">{{ $totalProductos }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f0fdf4;">
                            <svg class="w-4 h-4" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M3 6h18l-2 12H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold" style="color:#1c2b16;">Proveedores</p>
                            <p class="text-[11px]" style="color:#9a9a8a;">{{ $provActivos }} activos</p>
                        </div>
                    </div>
                    <span class="text-lg font-extrabold" style="color:#39a900;">{{ $totalProveedores }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#fffbeb;">
                            <svg class="w-4 h-4" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold" style="color:#1c2b16;">Proyectos</p>
                            <p class="text-[11px]" style="color:#9a9a8a;">{{ $proyectosActivos }} en progreso</p>
                        </div>
                    </div>
                    <span class="text-lg font-extrabold" style="color:#d97706;">{{ $totalProyectos }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#fef2f2;">
                            <svg class="w-4 h-4" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2"/>
                                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold" style="color:#1c2b16;">Stock bajo mínimo</p>
                            <p class="text-[11px]" style="color:#9a9a8a;">Requieren reposición</p>
                        </div>
                    </div>
                    <span class="text-lg font-extrabold" style="color:#ef4444;">{{ $stockBajo }}</span>
                </div>
            </div>
            <p class="text-[10px] mt-5 pt-3 border-t" style="border-color:#f3f0e8; color:#d1d5db;">
                Los datos se actualizan automáticamente cada 24 horas.
            </p>
        </div>
    </div>

<script>
function toggleFechas(val) {
    const show = val === 'mensual';
    document.getElementById('divMes').style.display  = show ? '' : 'none';
    document.getElementById('divAnio').style.display = show ? '' : 'none';
}
document.addEventListener('DOMContentLoaded', () => toggleFechas(document.getElementById('selectPeriodo').value));
</script>

</div>
</x-superadmin-layout>
