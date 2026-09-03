@php $title = 'Panel general'; @endphp

<x-operativo-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ── ENCABEZADO ──────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-2"
                  style="background:#fdc300; color:#71277a;">
                Operativo
            </span>
            <h1 class="text-2xl font-extrabold leading-tight">Panel general</h1>
            <p class="text-sm mt-0.5" style="color:#5a5a4f;">
                Bienvenido, <span class="font-semibold">{{ auth()->user()->name }}</span>.
                Hoy es {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.
            </p>
        </div>
    </div>

    @php
        // KPIs
        $ventasMes    = \Illuminate\Support\Facades\DB::table('venta')
                            ->whereMonth('fechaVenta', now()->month)
                            ->whereYear('fechaVenta', now()->year)
                            ->count();
        $totalMes     = \Illuminate\Support\Facades\DB::table('venta')
                            ->whereMonth('fechaVenta', now()->month)
                            ->whereYear('fechaVenta', now()->year)
                            ->sum('total');
        $consignaciones = \App\Models\Consignacion::where('estado', 'proximo_vencer')->count();
        $proyectos    = \Illuminate\Support\Facades\DB::table('proyectoinvestigacion')->count();
        $tareasPendientes = \Illuminate\Support\Facades\DB::table('tarea')
                            ->where('estado', '!=', 'completado')
                            ->count();
        $eventosHoy   = \Illuminate\Support\Facades\DB::table('evento')
                            ->whereDate('fecha', now()->toDateString())
                            ->count();

        // Últimas ventas
        $ultimasVentas = \Illuminate\Support\Facades\DB::table('venta')
                            ->orderByDesc('fechaVenta')
                            ->limit(5)
                            ->get();

        // Proyectos con avance
        $proyectosAvance = \Illuminate\Support\Facades\DB::table('proyectoinvestigacion')
                            ->select('nombre', 'porcentajeAvance', 'estado')
                            ->orderByDesc('porcentajeAvance')
                            ->limit(4)
                            ->get();
    @endphp

    {{-- ── KPI CARDS ──────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Ventas del mes</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#fffbeb;">
                    <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                        <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold">{{ $ventasMes }}</p>
            <p class="text-xs" style="color:#9a9a8a;">
                @if($totalMes > 0) ${{ number_format($totalMes, 0, ',', '.') }} acumulado
                @else Sin ventas este mes @endif
            </p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Consignaciones</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:{{ $consignaciones > 0 ? '#fff7ed' : '#f0fdf4' }};">
                    <svg class="w-5 h-5" style="color:{{ $consignaciones > 0 ? '#c2410c' : '#39a900' }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold">{{ $consignaciones }}</p>
            <p class="text-xs" style="color:{{ $consignaciones > 0 ? '#c2410c' : '#9a9a8a' }};">
                {{ $consignaciones > 0 ? 'Próximas a vencer' : 'Sin alertas de vencimiento' }}
            </p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Proyectos</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold">{{ $proyectos }}</p>
            <p class="text-xs" style="color:#9a9a8a;">En investigación</p>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Tareas pendientes</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#faf5ff;">
                    <svg class="w-5 h-5" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold">{{ $tareasPendientes }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Sin completar</p>
        </div>

    </div>

    {{-- ── FILA 2: Proyectos + Últimas ventas ─────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

        {{-- Proyectos de investigación --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold">Proyectos de investigación</h2>
                <a href="{{ route('operativo.proyectos.index') }}" class="text-xs font-semibold hover:underline" style="color:#71277a;">Ver todos →</a>
            </div>
            @forelse($proyectosAvance as $p)
            @php
                $pct = min($p->porcentajeAvance ?? 0, 100);
                $barColor = $pct >= 75 ? '#39a900' : ($pct >= 40 ? '#fdc300' : '#71277a');
                $estadoColors = [
                    'en_progreso' => ['bg'=>'#f0fdf4','color'=>'#166534','label'=>'En progreso'],
                    'completado'  => ['bg'=>'#eff6ff','color'=>'#1d4ed8','label'=>'Completado'],
                    'planificado' => ['bg'=>'#faf5ff','color'=>'#71277a','label'=>'Planificado'],
                    'pausado'     => ['bg'=>'#fef9c3','color'=>'#854d0e','label'=>'Pausado'],
                ];
                $ec = $estadoColors[$p->estado] ?? ['bg'=>'#f1f5f9','color'=>'#475569','label'=>ucfirst($p->estado ?? '')];
            @endphp
            <div class="mb-4 last:mb-0">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-xs font-semibold truncate max-w-[60%]" title="{{ $p->nombre }}">{{ $p->nombre }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                              style="background:{{ $ec['bg'] }}; color:{{ $ec['color'] }};">{{ $ec['label'] }}</span>
                        <span class="text-xs font-bold" style="color:{{ $barColor }};">{{ number_format($pct, 0) }}%</span>
                    </div>
                </div>
                <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500"
                         style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-8 text-center" style="color:#9a9a8a;">
                <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
                </svg>
                <p class="text-xs">Sin proyectos registrados</p>
            </div>
            @endforelse
        </div>

        {{-- Últimas ventas --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold">Últimas ventas</h2>
                <a href="{{ route('operativo.ventas.index') }}" class="text-xs font-semibold hover:underline" style="color:#71277a;">Ver todas →</a>
            </div>
            @forelse($ultimasVentas as $v)
            <div class="flex items-center gap-3 py-3 border-b border-gray-50 last:border-0 last:pb-0 first:pt-0">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;">
                    <svg class="w-4 h-4" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                        <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold truncate">{{ $v->cliente ?? 'Cliente sin nombre' }}</p>
                    <p class="text-[11px]" style="color:#9a9a8a;">
                        {{ $v->fechaVenta ? \Carbon\Carbon::parse($v->fechaVenta)->diffForHumans() : '—' }}
                    </p>
                </div>
                @if($v->total)
                <span class="text-xs font-bold shrink-0" style="color:#39a900;">
                    ${{ number_format($v->total, 0, ',', '.') }}
                </span>
                @endif
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-8 text-center" style="color:#9a9a8a;">
                <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                    <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                </svg>
                <p class="text-xs">Sin ventas registradas</p>
            </div>
            @endforelse
        </div>

    </div>

    {{-- ── ACCESOS RÁPIDOS ─────────────────────────────────── --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-sm font-bold mb-5">Accesos rápidos</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
            $accesos = [
                ['label'=>'Inventario y consignación','route'=>'operativo.consignacion.index','bg'=>'#f0fdf4','color'=>'#39a900',
                 'icon'=>'M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z|M8 5V3a4 4 0 0 1 8 0v2'],
                ['label'=>'Ventas','route'=>'operativo.ventas.index','bg'=>'#fffbeb','color'=>'#d97706',
                 'icon'=>'M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6|circle cx=9 cy=20 r=1.5|circle cx=18 cy=20 r=1.5'],
                ['label'=>'Cronograma','route'=>'operativo.cronograma.index','bg'=>'#eff6ff','color'=>'#2563eb',
                 'icon'=>'M3 4a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v17H3V4Z|M3 9h18|M8 2v4|M16 2v4'],
                ['label'=>'Actividades','route'=>'operativo.actividades.index','bg'=>'#f3e8ff','color'=>'#71277a',
                 'icon'=>'M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z'],
                ['label'=>'Proyectos','route'=>'operativo.proyectos.index','bg'=>'#f0fdf4','color'=>'#39a900',
                 'icon'=>'M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z|M6 21h12'],
                ['label'=>'Tareas','route'=>'operativo.tareas.index','bg'=>'#faf5ff','color'=>'#71277a',
                 'icon'=>'M4 3h16a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z|M8 8h8|M8 12h8|M8 16h5'],
            ];
            @endphp
            @foreach($accesos as $acc)
            <a href="{{ route($acc['route']) }}"
               class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:shadow-sm transition group text-center">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                      style="background:{{ $acc['bg'] }};">
                    <svg class="w-5 h-5" style="color:{{ $acc['color'] }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        @foreach(explode('|', $acc['icon']) as $path)
                            @if(str_starts_with(trim($path), 'circle'))
                                @php preg_match('/cx=(\S+)\s+cy=(\S+)\s+r=(\S+)/', $path, $mm); @endphp
                                <circle cx="{{ $mm[1] }}" cy="{{ $mm[2] }}" r="{{ $mm[3] }}"/>
                            @else
                                <path d="{{ trim($path) }}"/>
                            @endif
                        @endforeach
                    </svg>
                </span>
                <span class="text-xs font-semibold leading-tight" style="color:#1c2b16;">{{ $acc['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>

</div>
</x-operativo-layout>
