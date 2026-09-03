@php $title = 'Panel general'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ── ENCABEZADO ────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-2"
                  style="background:#fdc300; color:#71277a;">
                Superadministrador
            </span>
            <h1 class="text-2xl font-extrabold leading-tight">Panel general</h1>
            <p class="text-sm mt-0.5" style="color:#5a5a4f;">
                Bienvenido, <span class="font-semibold">{{ auth()->user()->name }}</span>.
                Hoy es {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.
            </p>
        </div>

        @if(($stats['alertas'] ?? 0) > 0)
        <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-semibold"
             style="background:#fff7ed; border-color:#fed7aa; color:#c2410c;">
            <svg class="w-4 h-4 shrink-0 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            {{ $stats['alertas'] }} alerta{{ $stats['alertas'] != 1 ? 's' : '' }} sin leer
        </div>
        @endif
    </div>

    {{-- ── KPI CARDS ──────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Proveedores --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Proveedores</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 6h18l-2 12H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['proveedores'] ?? '—' }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Activos registrados</p>
        </div>

        {{-- Ventas del mes --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Ventas del mes</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                        <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['ventas_mes'] ?? '—' }}</p>
            <p class="text-xs" style="color:#9a9a8a;">
                @if(($stats['ventas_total'] ?? 0) > 0)
                    ${{ number_format($stats['ventas_total'], 0, ',', '.') }} acumulado
                @else
                    Sin ventas este mes
                @endif
            </p>
        </div>

        {{-- Proyectos --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Proyectos</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['proyectos'] ?? '—' }}</p>
            <p class="text-xs" style="color:#9a9a8a;">En investigación</p>
        </div>

        {{-- Usuarios --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Usuarios</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2"/>
                        <circle cx="8.5" cy="7" r="3"/>
                        <path d="M16 3.5a3 3 0 0 1 0 6"/><path d="M17.5 17v-1.5a3.5 3.5 0 0 0-2-3.2"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['usuarios'] ?? '—' }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Cuentas activas</p>
        </div>

    </div>

    {{-- ── FILA 2: Inventario + Proyectos + Alertas ───────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

        {{-- Inventario --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold" style="color:#1c2b16;">Estado del inventario</h2>
                <a href="{{ route('superadmin.productos.index') }}" class="text-xs font-semibold hover:underline" style="color:#71277a;">Ver todo →</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#39a900;"></span>
                        <span class="text-sm font-medium">Productos totales</span>
                    </div>
                    <span class="text-sm font-extrabold" style="color:#1c2b16;">{{ $stats['productos'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#ef4444;"></span>
                        <span class="text-sm font-medium">Stock bajo mínimo</span>
                    </div>
                    <span class="text-sm font-extrabold" style="color:{{ ($stats['stock_bajo'] ?? 0) > 0 ? '#ef4444' : '#1c2b16' }};">
                        {{ $stats['stock_bajo'] ?? 0 }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background:#fdc300;"></span>
                        <span class="text-sm font-medium">Alertas activas</span>
                    </div>
                    <span class="text-sm font-extrabold" style="color:{{ ($stats['alertas'] ?? 0) > 0 ? '#c2410c' : '#1c2b16' }};">
                        {{ $stats['alertas'] ?? 0 }}
                    </span>
                </div>
            </div>

            @if(($stats['productos'] ?? 0) > 0)
            <div class="mt-5 pt-4 border-t border-gray-100">
                @php
                    $pct = ($stats['productos'] > 0)
                        ? round((($stats['productos'] - ($stats['stock_bajo'] ?? 0)) / $stats['productos']) * 100)
                        : 100;
                @endphp
                <div class="flex justify-between text-xs mb-1.5" style="color:#9a9a8a;">
                    <span>Productos en buen estado</span>
                    <span class="font-bold" style="color:#39a900;">{{ $pct }}%</span>
                </div>
                <div class="w-full h-2 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500"
                         style="width:{{ $pct }}%; background:#39a900;"></div>
                </div>
            </div>
            @endif
        </div>

        {{-- Proyectos de investigación --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold" style="color:#1c2b16;">Proyectos de investigación</h2>
                <a href="{{ route('superadmin.proyectos.index') }}" class="text-xs font-semibold hover:underline" style="color:#71277a;">Ver todos →</a>
            </div>
            @forelse($proyectos ?? [] as $p)
            <div class="mb-4">
                <div class="flex justify-between text-xs mb-1">
                    <span class="font-semibold truncate max-w-[70%]" title="{{ $p->nombre }}">{{ $p->nombre }}</span>
                    <span class="font-bold shrink-0" style="color:#39a900;">{{ number_format($p->porcentajeAvance ?? 0, 0) }}%</span>
                </div>
                <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full"
                         style="width:{{ min($p->porcentajeAvance ?? 0, 100) }}%;
                                background: {{ ($p->porcentajeAvance ?? 0) >= 75 ? '#39a900' : (($p->porcentajeAvance ?? 0) >= 40 ? '#fdc300' : '#71277a') }};"></div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-6 text-center" style="color:#9a9a8a;">
                <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
                </svg>
                <p class="text-xs">Sin proyectos registrados</p>
            </div>
            @endforelse
        </div>

        {{-- Alertas recientes --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold" style="color:#1c2b16;">Alertas recientes</h2>
                @if(($stats['alertas'] ?? 0) > 0)
                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:#fef3c7; color:#c2410c;">
                    {{ $stats['alertas'] }} nuevas
                </span>
                @endif
            </div>
            @forelse($alertas ?? [] as $a)
            <div class="flex gap-3 mb-3.5 last:mb-0">
                <span class="mt-0.5 w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                      style="background:{{ $a->tipo === 'stock' ? '#fef3c7' : ($a->tipo === 'vencimiento' ? '#fee2e2' : '#f3e8ff') }};">
                    <svg class="w-3.5 h-3.5" style="color:{{ $a->tipo === 'stock' ? '#d97706' : ($a->tipo === 'vencimiento' ? '#ef4444' : '#71277a') }};"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold leading-snug truncate">{{ $a->mensaje }}</p>
                    <p class="text-[11px] mt-0.5" style="color:#9a9a8a;">
                        {{ $a->fechaGeneracion ? \Carbon\Carbon::parse($a->fechaGeneracion)->diffForHumans() : '—' }}
                    </p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-6 text-center" style="color:#9a9a8a;">
                <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
                <p class="text-xs">Sin alertas pendientes</p>
            </div>
            @endforelse
        </div>

    </div>

    {{-- ── FILA 3: Accesos rápidos + Log de auditoría ─────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        {{-- Accesos rápidos --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-sm font-bold mb-5" style="color:#1c2b16;">Accesos rápidos</h2>
            <div class="grid grid-cols-2 gap-3">

                @php
                $accesos = [
                    ['icono' => 'M3 6h18l-2 12H5L3 6Z|M8 6V4a4 4 0 0 1 8 0v2', 'label' => 'Proveedores',  'bg' => '#f0fdf4', 'color' => '#39a900', 'route' => route('superadmin.proveedores.index')],
                    ['icono' => 'M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z|M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2', 'label' => 'Productos',    'bg' => '#faf5ff', 'color' => '#71277a', 'route' => route('superadmin.productos.index')],
                    ['icono' => 'M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6|circle cx=9 cy=20 r=1.5|circle cx=18 cy=20 r=1.5', 'label' => 'Ventas',       'bg' => '#fffbeb', 'color' => '#d97706', 'route' => route('superadmin.ventas.index')],
                    ['icono' => 'M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2|circle cx=8.5 cy=7 r=3', 'label' => 'Usuarios',     'bg' => '#eff6ff', 'color' => '#2563eb', 'route' => route('superadmin.usuarios.index')],
                    ['icono' => 'M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z|M6 21h12', 'label' => 'Proyectos',    'bg' => '#f0fdf4', 'color' => '#39a900', 'route' => route('superadmin.proyectos.index')],
                    ['icono' => 'M4 6h16M4 6l1 13a2 2 0 0 0 2 1.8h10A2 2 0 0 0 19 19L20 6M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2', 'label' => 'Auditoría',    'bg' => '#fff1f2', 'color' => '#e11d48', 'route' => route('superadmin.auditoria.index')],
                ];
                @endphp

                @foreach($accesos as $acc)
                <a href="{{ $acc['route'] }}"
                   class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-gray-100 hover:border-gray-200 hover:shadow-sm transition text-center group">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform"
                          style="background:{{ $acc['bg'] }};">
                        <svg class="w-5 h-5" style="color:{{ $acc['color'] }};" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            @foreach(explode('|', $acc['icono']) as $path)
                                @if(str_starts_with(trim($path), 'circle'))
                                    @php preg_match('/cx=(\S+)\s+cy=(\S+)\s+r=(\S+)/', $path, $m); @endphp
                                    <circle cx="{{ $m[1] }}" cy="{{ $m[2] }}" r="{{ $m[3] }}"/>
                                @else
                                    <path d="{{ trim($path) }}"/>
                                @endif
                            @endforeach
                        </svg>
                    </span>
                    <span class="text-xs font-semibold" style="color:#1c2b16;">{{ $acc['label'] }}</span>
                </a>
                @endforeach

            </div>
        </div>

        {{-- Log de auditoría reciente --}}
        <div class="lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold" style="color:#1c2b16;">Actividad reciente</h2>
                <a href="{{ route('superadmin.auditoria.index') }}" class="text-xs font-semibold hover:underline" style="color:#71277a;">Ver registro →</a>
            </div>

            @forelse($logs ?? [] as $log)
            <div class="flex gap-3 py-3 border-b border-gray-50 last:border-0 last:pb-0 first:pt-0">
                <div class="w-7 h-7 rounded-lg shrink-0 flex items-center justify-center mt-0.5"
                     style="background:#f0fdf4;">
                    <svg class="w-3.5 h-3.5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold leading-snug">{{ $log->descripcionOperacion ?? 'Operación registrada' }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        @if($log->modulo)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded" style="background:#f3e8ff; color:#71277a;">
                            {{ $log->modulo }}
                        </span>
                        @endif
                        <p class="text-[11px]" style="color:#9a9a8a;">
                            {{ $log->fechaHora ? \Carbon\Carbon::parse($log->fechaHora)->diffForHumans() : '—' }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-8 text-center" style="color:#9a9a8a;">
                <svg class="w-10 h-10 mb-2 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect x="4" y="4" width="16" height="16" rx="2"/><path d="M8 4v16M16 4v4"/>
                </svg>
                <p class="text-xs">Sin registros de auditoría todavía</p>
            </div>
            @endforelse
        </div>

    </div>

</div>
</x-superadmin-layout>
