@php $title = 'Ejecución de Actividades'; @endphp

<x-superadmin-layout :title="$title">
<style>
.drag-ghost {
    display: none !important;
}
.kanban-col {
    border-radius: 0.75rem;
    border: 2px dashed transparent;
    transition: border-color 0.15s, background-color 0.15s;
}
.kanban-col.drag-over {
    border-color: #39a900 !important;
    background-color: #f0fdf4 !important;
}
</style>
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ALERTAS DE SESIÓN --}}
    @if(session('success'))
        <div class="mb-5 p-4 rounded-xl flex items-center gap-3 border shadow-sm" style="background:#f0fdf4; border-color:#bbf7d0; color:#166534;">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-5 p-4 rounded-xl flex items-center gap-3 border shadow-sm" style="background:#fffbeb; border-color:#fde68a; color:#92400e;">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span class="text-sm font-semibold">{{ session('info') }}</span>
        </div>
    @endif

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Tareas y Avances</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Gestiona las tareas de tus proyectos y da seguimiento a los avances del equipo.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Botón Notificar por Correo --}}
            <form id="formNotificarPendientes" method="POST" action="{{ route('superadmin.tareas.notificar-pendientes') }}" class="inline-block">
                @csrf
                <input type="hidden" name="destinatario" value="todos">
                <button type="button"
                        onclick="confirmarNotificarPendientes()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white transition hover:opacity-90 shadow-sm cursor-pointer"
                        style="background:#39a900;" title="Enviar recordatorio por correo electrónico">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                    <span>Notificar por Correo</span>
                </button>
            </form>

            <button onclick="abrirModalAlertas()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 border relative shadow-sm"
                    style="background:#ffffff; border-color:{{ ($stats['vencidas'] + $stats['proximas']) > 0 ? '#fca5a5' : '#e7e0cc' }}; color:{{ ($stats['vencidas'] + $stats['proximas']) > 0 ? '#dc2626' : '#1c2b16' }};">
                <svg class="w-4 h-4 {{ ($stats['vencidas'] + $stats['proximas']) > 0 ? 'animate-pulse text-red-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                <span>Centro de Alertas</span>
                @if(($stats['vencidas'] + $stats['proximas']) > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-extrabold text-white bg-red-600">
                        {{ $stats['vencidas'] + $stats['proximas'] }}
                    </span>
                @endif
            </button>
            <button onclick="abrirModalCrear()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shadow-sm"
                    style="background:#fdc300; color:#71277a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                </svg>
                Nueva Tarea
            </button>
        </div>
    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('superadmin.ejecucion.index') }}"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar tarea o proyecto..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
        </div>
        <div class="min-w-[160px]">
            <select name="proyecto_id" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Todos los proyectos</option>
                @foreach($proyectos as $proy)
                    <option value="{{ $proy->id }}" {{ request('proyecto_id') == $proy->id ? 'selected' : '' }}>{{ $proy->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[130px]">
            <select name="prioridad" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Toda prioridad</option>
                <option value="alta"  {{ request('prioridad') === 'alta'  ? 'selected' : '' }}>Alta</option>
                <option value="media" {{ request('prioridad') === 'media' ? 'selected' : '' }}>Media</option>
                <option value="baja"  {{ request('prioridad') === 'baja'  ? 'selected' : '' }}>Baja</option>
            </select>
        </div>
        <div class="min-w-[160px]">
            <select name="alerta" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none font-medium" style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Todas las alertas</option>
                <option value="vencidas" {{ request('alerta') === 'vencidas' ? 'selected' : '' }}>⚠️ Vencidas ({{ $stats['vencidas'] }})</option>
                <option value="proximas" {{ request('alerta') === 'proximas' ? 'selected' : '' }}>⏰ Próx. a vencer ({{ $stats['proximas'] }})</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90" style="background:#39a900;">Filtrar</button>
        @if(request()->hasAny(['search','proyecto_id','prioridad','alerta']))
            <a href="{{ route('superadmin.ejecucion.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
        @endif
    </form>

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#f3f0e8;">
                <svg class="w-5 h-5" style="color:#5a5a4f;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold" style="color:#1c2b16;">{{ $stats['total'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Total</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold" style="color:#1c2b16;">{{ $stats['completadas'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Completadas</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;">
                <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold" style="color:#1c2b16;">{{ $stats['en_progreso'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">En progreso</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#faf5ff;">
                <svg class="w-5 h-5" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold" style="color:#1c2b16;">{{ $stats['pendientes'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Pendientes</p>
            </div>
        </div>
        <a href="{{ route('superadmin.ejecucion.index', ['alerta' => 'vencidas']) }}"
           class="bg-white rounded-2xl p-4 shadow-sm border transition hover:shadow-md flex items-center gap-3 {{ request('alerta') === 'vencidas' ? 'ring-2 ring-red-500 border-red-200' : 'border-gray-100' }}"
           title="Ver tareas vencidas">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#fef2f2;">
                <svg class="w-5 h-5" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold" style="color:#ef4444;">{{ $stats['vencidas'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Vencidas</p>
            </div>
        </a>
        <a href="{{ route('superadmin.ejecucion.index', ['alerta' => 'proximas']) }}"
           class="bg-white rounded-2xl p-4 shadow-sm border transition hover:shadow-md flex items-center gap-3 {{ request('alerta') === 'proximas' ? 'ring-2 ring-amber-500 border-amber-200' : 'border-gray-100' }}"
           title="Ver tareas próximas a vencer">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;">
                <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold" style="color:#d97706;">{{ $stats['proximas'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Próx. a vencer</p>
            </div>
        </a>
    </div>

    {{-- KANBAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        @php
        $columnaConfig = [
            'por_iniciar' => ['label' => 'Por iniciar',  'color' => '#71277a', 'bg' => '#faf5ff', 'dot' => '#71277a'],
            'en_progreso' => ['label' => 'En progreso',  'color' => '#d97706', 'bg' => '#fffbeb', 'dot' => '#fdc300'],
            'completada'  => ['label' => 'Completadas',  'color' => '#166534', 'bg' => '#f0fdf4', 'dot' => '#39a900'],
        ];
        $prioridadConfig = [
            'alta'  => ['label' => 'Alta',  'bg' => '#fef2f2', 'color' => '#ef4444'],
            'media' => ['label' => 'Media', 'bg' => '#fffbeb', 'color' => '#d97706'],
            'baja'  => ['label' => 'Baja',  'bg' => '#f0fdf4', 'color' => '#39a900'],
        ];
        @endphp

        @foreach($columnaConfig as $estado => $cc)
        <div class="flex flex-col rounded-2xl overflow-hidden border border-gray-100 shadow-sm" style="background:#f9f9f6;">

            {{-- Cabecera columna --}}
            <div class="flex items-center justify-between px-5 py-4 bg-white border-b" style="border-color:#f3f0e8;">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $cc['dot'] }};"></span>
                    <h3 class="font-extrabold text-sm" style="color:#1c2b16;">{{ $cc['label'] }}</h3>
                    <span class="ml-1 px-2 py-0.5 rounded-full text-xs font-bold"
                          style="background:{{ $cc['bg'] }}; color:{{ $cc['color'] }};"
                          id="badge-{{ $estado }}">
                        {{ $columnas[$estado]->count() }}
                    </span>
                </div>
            </div>

            {{-- Tarjetas --}}
            <div class="flex-1 p-3 space-y-3 min-h-[200px] kanban-col transition-colors duration-150"
                 id="col-{{ $estado }}"
                 data-estado="{{ $estado }}"
                 ondragover="event.preventDefault(); this.classList.add('drag-over')"
                 ondragleave="this.classList.remove('drag-over')"
                 ondrop="onDrop(event, '{{ $estado }}')">
                @forelse($columnas[$estado] as $tarea)
                @php $pc = $prioridadConfig[$tarea->prioridad] ?? $prioridadConfig['media']; @endphp
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-grab hover:shadow-md transition group select-none relative {{ $tarea->es_vencida ? 'border-l-4 border-l-red-500' : ($tarea->es_proxima_vencer ? 'border-l-4 border-l-amber-500' : '') }}"
                     draggable="true"
                     data-id="{{ $tarea->id }}"
                     ondragstart="onDragStart(event, {{ $tarea->id }})"
                     ondragend="onDragEnd(event)"
                     onclick="cargarTarea({{ $tarea->id }})">

                    {{-- Badges de alerta si está vencida o por vencer --}}
                    @if($tarea->es_vencida)
                        <div class="mb-2 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-bold bg-red-100 text-red-700 border border-red-200">
                            <svg class="w-3 h-3 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>{{ $tarea->dias_restantes_texto }}</span>
                        </div>
                    @elseif($tarea->es_proxima_vencer)
                        <div class="mb-2 inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            <svg class="w-3 h-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span>{{ $tarea->dias_restantes_texto }}</span>
                        </div>
                    @endif

                    {{-- Proyecto --}}
                    @if($tarea->proyecto)
                    <p class="text-[11px] font-bold mb-1.5" style="color:#71277a;">{{ $tarea->proyecto->nombre }}</p>
                    @endif

                    {{-- Nombre --}}
                    <p class="text-sm font-bold leading-snug mb-2" style="color:#1c2b16;">{{ $tarea->nombre }}</p>

                    {{-- Responsable + fecha --}}
                    <div class="flex items-center justify-between text-xs mb-3" style="color:#9a9a8a;">
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white shrink-0"
                                 style="background:#71277a;">
                                {{ strtoupper(substr($tarea->responsable->name ?? '?', 0, 1)) }}
                            </div>
                            <span class="truncate max-w-[90px]">{{ $tarea->responsable->name ?? '—' }}</span>
                        </div>
                        @if($tarea->fecha_entrega)
                        <div class="flex items-center gap-1 {{ $tarea->es_vencida ? 'text-red-600 font-bold' : ($tarea->es_proxima_vencer ? 'text-amber-600 font-bold' : '') }}">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                            </svg>
                            {{ $tarea->fecha_entrega->locale('es')->isoFormat('D MMM YYYY') }}
                        </div>
                        @endif
                    </div>

                    {{-- Prioridad + estado --}}
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold"
                              style="background:{{ $pc['bg'] }}; color:{{ $pc['color'] }};">
                            {{ $pc['label'] }}
                        </span>
                        @if($estado === 'completada')
                        <div class="flex items-center gap-1">
                            <div class="w-full h-1.5 rounded-full bg-gray-100 overflow-hidden w-16">
                                <div class="h-full rounded-full" style="width:100%; background:#39a900;"></div>
                            </div>
                            <span class="text-[11px] font-bold" style="color:#39a900;">100%</span>
                        </div>
                        @elseif($estado === 'en_progreso')
                        <div class="flex items-center gap-1">
                            <div class="w-16 h-1.5 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full" style="width:50%; background:#fdc300;"></div>
                            </div>
                            <span class="text-[11px] font-bold" style="color:#d97706;">50%</span>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="kanban-empty flex flex-col items-center justify-center py-10 text-center" style="color:#d1d5db;">
                    <svg class="w-8 h-8 mb-2 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                    </svg>
                    <p class="text-xs">Sin tareas</p>
                </div>
                @endforelse
            </div>

            {{-- Agregar tarea rápido --}}
            <div class="px-3 pb-3">
                <button onclick="abrirModalCrearEstado('{{ $estado }}')"
                        class="w-full py-2.5 rounded-xl text-sm font-bold border border-dashed hover:bg-white transition"
                        style="border-color:#d1d5db; color:#9a9a8a;">
                    + Agregar tarea
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ══ MODAL CREAR / EDITAR ══════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[95vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-7 py-5 border-b sticky top-0 bg-white z-10" style="border-color:#f3f0e8;">
            <h2 id="modalTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nueva Tarea</h2>
            <button onclick="cerrarModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-7 py-6 space-y-4">
            <div id="modalAlerta" class="hidden px-4 py-3 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;"></div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Nombre <span style="color:#ef4444;">*</span></label>
                <input type="text" id="inpNombre" placeholder="Ej: Análisis de suelo - Lote 7B"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Descripción</label>
                <textarea id="inpDescripcion" rows="2" placeholder="Detalles de la tarea..."
                          class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none resize-none"
                          style="border-color:#e7e0cc; background:#fafafa;"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Proyecto</label>
                    <select id="inpProyecto" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Sin proyecto</option>
                        @foreach($proyectos as $proy)
                            <option value="{{ $proy->id }}">{{ $proy->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Responsable</label>
                    <select id="inpResponsable" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Sin asignar</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha de entrega</label>
                    <input type="date" id="inpFecha"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Prioridad</label>
                    <select id="inpPrioridad" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="alta">Alta</option>
                        <option value="media" selected>Media</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Estado</label>
                    <select id="inpEstado" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="por_iniciar">Por iniciar</option>
                        <option value="en_progreso">En progreso</option>
                        <option value="completada">Completada</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button id="btnEliminar" onclick="eliminarTarea()" type="button"
                        class="hidden px-4 py-3 rounded-xl font-bold text-sm border hover:bg-red-50 transition"
                        style="border-color:#fca5a5; color:#ef4444;">Eliminar</button>
                <button onclick="guardarTarea()"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90"
                        style="background:#71277a;" id="btnGuardar">Crear tarea</button>
                <button type="button" onclick="cerrarModal()"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<script>
let tareaActualId = null;

const R = {
    store:   '{{ route("superadmin.ejecucion.store") }}',
    base:    '/superadmin/ejecucion/',
    csrf:    '{{ csrf_token() }}',
};

// ── Drag & Drop ───────────────────────────────────────────────
let draggingId = null;

function onDragStart(event, id) {
    draggingId = id;
    event.dataTransfer.effectAllowed = 'move';
    // Pequeño delay para que el navegador tome el snapshot antes de ocultar
    setTimeout(() => {
        event.target.classList.add('drag-ghost');
    }, 0);
}

function onDragEnd(event) {
    event.currentTarget.classList.remove('drag-ghost');
    document.querySelectorAll('.kanban-col').forEach(c => c.classList.remove('drag-over'));
}

async function onDrop(event, nuevoEstado) {
    event.preventDefault();
    const col = event.currentTarget;
    col.classList.remove('drag-over');

    if (!draggingId) return;

    const card = document.querySelector(`[data-id="${draggingId}"]`);
    if (!card) return;

    const origenCol = card.closest('.kanban-col');

    // Quitar el mensaje "Sin tareas" de la columna destino
    col.querySelector('.kanban-empty')?.remove();

    // Mover la tarjeta al final de la columna destino
    col.appendChild(card);

    // Si la columna origen quedó vacía, mostrar "Sin tareas"
    if (origenCol && origenCol !== col) {
        if (origenCol.querySelectorAll('[data-id]').length === 0) {
            origenCol.insertAdjacentHTML('afterbegin', `
                <div class="kanban-empty flex flex-col items-center justify-center py-10 text-center" style="color:#d1d5db;">
                    <svg class="w-8 h-8 mb-2 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                    </svg>
                    <p class="text-xs">Sin tareas</p>
                </div>
            `);
        }
    }

    // Llamar al backend
    const res = await fetch(`/superadmin/ejecucion/${draggingId}/estado`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': R.csrf,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ estado: nuevoEstado }),
    });

    const data = await res.json();
    if (!data.ok) {
        location.reload();
    } else {
        document.querySelectorAll('.kanban-col').forEach(c => {
            const estado = c.dataset.estado;
            const count = c.querySelectorAll('[data-id]').length;
            const badge = document.querySelector(`#badge-${estado}`);
            if (badge) badge.textContent = count;
        });
    }

    draggingId = null;
}

function abrirModal() {
    document.getElementById('modalForm').classList.remove('hidden');
    document.getElementById('modalForm').classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal() {
    document.getElementById('modalForm').classList.add('hidden');
    document.getElementById('modalForm').classList.remove('flex');
    document.body.style.overflow = '';
    tareaActualId = null;
}
document.getElementById('modalForm').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

function limpiar() {
    document.getElementById('inpNombre').value      = '';
    document.getElementById('inpDescripcion').value = '';
    document.getElementById('inpProyecto').value    = '';
    document.getElementById('inpResponsable').value = '';
    document.getElementById('inpFecha').value       = '';
    document.getElementById('inpPrioridad').value   = 'media';
    document.getElementById('inpEstado').value      = 'por_iniciar';
    document.getElementById('modalAlerta').classList.add('hidden');
    document.getElementById('btnEliminar').classList.add('hidden');
}

function abrirModalCrear() {
    tareaActualId = null;
    limpiar();
    document.getElementById('modalTitle').textContent   = 'Nueva Tarea';
    document.getElementById('btnGuardar').textContent   = 'Crear tarea';
    abrirModal();
}

function abrirModalCrearEstado(estado) {
    abrirModalCrear();
    document.getElementById('inpEstado').value = estado;
}

async function cargarTarea(id) {
    const res = await fetch(R.base + id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const t   = await res.json();
    tareaActualId = t.id;
    limpiar();
    document.getElementById('modalTitle').textContent      = 'Editar Tarea';
    document.getElementById('btnGuardar').textContent      = 'Guardar cambios';
    document.getElementById('inpNombre').value             = t.nombre || '';
    document.getElementById('inpDescripcion').value        = t.descripcion || '';
    document.getElementById('inpProyecto').value           = t.proyecto_id || '';
    document.getElementById('inpResponsable').value        = t.responsable_id || '';
    document.getElementById('inpFecha').value              = t.fecha_entrega ? t.fecha_entrega.substring(0,10) : '';
    document.getElementById('inpPrioridad').value          = t.prioridad || 'media';
    document.getElementById('inpEstado').value             = t.estado || 'por_iniciar';
    document.getElementById('btnEliminar').classList.remove('hidden');
    abrirModal();
}

async function guardarTarea() {
    const body = {
        nombre:         document.getElementById('inpNombre').value.trim(),
        descripcion:    document.getElementById('inpDescripcion').value || null,
        proyecto_id:    document.getElementById('inpProyecto').value    || null,
        responsable_id: document.getElementById('inpResponsable').value || null,
        fecha_entrega:  document.getElementById('inpFecha').value       || null,
        prioridad:      document.getElementById('inpPrioridad').value,
        estado:         document.getElementById('inpEstado').value,
        _token:         R.csrf,
    };

    if (!body.nombre) {
        const al = document.getElementById('modalAlerta');
        al.textContent = 'El nombre es obligatorio.';
        al.classList.remove('hidden');
        return;
    }

    const url    = tareaActualId ? R.base + tareaActualId : R.store;
    const method = tareaActualId ? 'PUT' : 'POST';

    const res  = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': R.csrf, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(body),
    });
    const data = await res.json();

    if (data.ok) { cerrarModal(); location.reload(); }
    else {
        const al = document.getElementById('modalAlerta');
        al.textContent = data.message || 'Error al guardar.';
        al.classList.remove('hidden');
    }
}

async function eliminarTarea() {
    if (!tareaActualId) return;
    document.getElementById('modalConfirm').classList.remove('hidden');
    document.getElementById('modalConfirm').classList.add('flex');
}
async function eliminarTareaConfirmado() {
    document.getElementById('modalConfirm').classList.add('hidden');
    document.getElementById('modalConfirm').classList.remove('flex');
    const res  = await fetch(R.base + tareaActualId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': R.csrf, 'X-Requested-With': 'XMLHttpRequest' },
    });
    const data = await res.json();
    if (data.ok) { cerrarModal(); location.reload(); }
}

function cancelarEliminar() {
    document.getElementById('modalConfirm').classList.add('hidden');
    document.getElementById('modalConfirm').classList.remove('flex');
}

// ── Centro de Alertas ──────────────────────────────────────────
function abrirModalAlertas() {
    document.getElementById('modalCentroAlertas').classList.remove('hidden');
    document.getElementById('modalCentroAlertas').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function cerrarModalAlertas() {
    document.getElementById('modalCentroAlertas').classList.add('hidden');
    document.getElementById('modalCentroAlertas').classList.remove('flex');
    document.body.style.overflow = '';
}

function cambiarTabAlertas(tab) {
    const tabV = document.getElementById('tabContentVencidas');
    const tabP = document.getElementById('tabContentProximas');
    const btnV = document.getElementById('tabBtnVencidas');
    const btnP = document.getElementById('tabBtnProximas');

    if (tab === 'vencidas') {
        tabV.classList.remove('hidden');
        tabP.classList.add('hidden');
        btnV.className = 'pb-3 text-sm font-extrabold flex items-center gap-2 border-b-2 border-red-600 text-red-600 transition';
        btnP.className = 'pb-3 text-sm font-extrabold flex items-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-800 transition';
    } else {
        tabP.classList.remove('hidden');
        tabV.classList.add('hidden');
        btnP.className = 'pb-3 text-sm font-extrabold flex items-center gap-2 border-b-2 border-amber-500 text-amber-700 transition';
        btnV.className = 'pb-3 text-sm font-extrabold flex items-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-800 transition';
    }
}

async function marcarCompletadaRapido(id) {
    const res = await fetch(`/superadmin/ejecucion/${id}/estado`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': R.csrf,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ estado: 'completada' }),
    });
    const data = await res.json();
    if (data.ok) {
        location.reload();
    } else {
        alert(data.message || 'Error al actualizar estado.');
    }
}
</script>

{{-- ══ MODAL CENTRO DE ALERTAS ══════════════════════════════════ --}}
<div id="modalCentroAlertas" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.55);" onclick="if(event.target===this) cerrarModalAlertas()">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">
        
        {{-- Header del modal --}}
        <div class="px-7 py-5 border-b flex items-center justify-between bg-white shrink-0" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0" style="background:#fef2f2;">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-extrabold" style="color:#1c2b16;">Centro de Alertas de Tareas</h2>
                    <p class="text-xs" style="color:#5a5a4f;">Monitorea y atiende las tareas vencidas y próximas a vencer.</p>
                </div>
            </div>
            <button onclick="cerrarModalAlertas()" class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 text-gray-400 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Tabs --}}
        <div class="px-7 pt-4 bg-[#fbfbfa] border-b flex gap-4 shrink-0" style="border-color:#f3f0e8;">
            <button type="button" onclick="cambiarTabAlertas('vencidas')" id="tabBtnVencidas"
                    class="pb-3 text-sm font-extrabold flex items-center gap-2 border-b-2 border-red-600 text-red-600 transition">
                <span>⚠️ Tareas Vencidas</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $alertasVencidas->count() }}</span>
            </button>
            <button type="button" onclick="cambiarTabAlertas('proximas')" id="tabBtnProximas"
                    class="pb-3 text-sm font-extrabold flex items-center gap-2 border-b-2 border-transparent text-gray-500 hover:text-gray-800 transition">
                <span>⏰ Próximas a Vencer (3 días)</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">{{ $alertasProximas->count() }}</span>
            </button>
        </div>

        {{-- Cuerpo con Scroll --}}
        <div class="p-7 overflow-y-auto flex-1 space-y-4">
            
            {{-- Panel Vencidas --}}
            <div id="tabContentVencidas" class="space-y-3">
                @forelse($alertasVencidas as $t)
                <div class="p-4 rounded-2xl border border-red-100 bg-red-50/40 hover:bg-red-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-red-600 text-white">
                                {{ $t->dias_restantes_texto }}
                            </span>
                            @if($t->proyecto)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-white border border-purple-100" style="color:#71277a;">
                                    📁 {{ $t->proyecto->nombre }}
                                </span>
                            @endif
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-white text-gray-600 border border-gray-200 capitalize">
                                Estado: {{ str_replace('_', ' ', $t->estado) }}
                            </span>
                        </div>
                        <h4 class="font-extrabold text-base text-gray-900">{{ $t->nombre }}</h4>
                        @if($t->descripcion)
                            <p class="text-xs text-gray-600 line-clamp-1">{{ $t->descripcion }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-xs text-gray-500 pt-1 flex-wrap">
                            <span>👤 <strong>Responsable:</strong> {{ $t->responsable->name ?? 'Sin asignar' }}</span>
                            <span>📅 <strong>Fecha entrega:</strong> {{ $t->fecha_entrega ? $t->fecha_entrega->format('d/m/Y') : '—' }}</span>
                            <span>⚡ <strong>Prioridad:</strong> <span class="capitalize font-semibold {{ $t->prioridad === 'alta' ? 'text-red-600' : 'text-amber-600' }}">{{ $t->prioridad }}</span></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button onclick="cerrarModalAlertas(); cargarTarea({{ $t->id }});"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 shadow-sm transition">
                            Ver / Editar
                        </button>
                        <button onclick="marcarCompletadaRapido({{ $t->id }})"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold text-white shadow-sm transition hover:opacity-90"
                                style="background:#39a900;">
                            ✓ Completar
                        </button>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4 12 14.01l-3-3"/>
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-gray-900 text-base">¡No hay tareas vencidas!</h4>
                    <p class="text-sm text-gray-500 mt-1">Todas las tareas se encuentran al día o completadas.</p>
                </div>
                @endforelse
            </div>

            {{-- Panel Próximas a Vencer --}}
            <div id="tabContentProximas" class="space-y-3 hidden">
                @forelse($alertasProximas as $t)
                <div class="p-4 rounded-2xl border border-amber-100 bg-amber-50/40 hover:bg-amber-50/70 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-1.5 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-500 text-white">
                                {{ $t->dias_restantes_texto }}
                            </span>
                            @if($t->proyecto)
                                <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-white border border-purple-100" style="color:#71277a;">
                                    📁 {{ $t->proyecto->nombre }}
                                </span>
                            @endif
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-white text-gray-600 border border-gray-200 capitalize">
                                Estado: {{ str_replace('_', ' ', $t->estado) }}
                            </span>
                        </div>
                        <h4 class="font-extrabold text-base text-gray-900">{{ $t->nombre }}</h4>
                        @if($t->descripcion)
                            <p class="text-xs text-gray-600 line-clamp-1">{{ $t->descripcion }}</p>
                        @endif
                        <div class="flex items-center gap-4 text-xs text-gray-500 pt-1 flex-wrap">
                            <span>👤 <strong>Responsable:</strong> {{ $t->responsable->name ?? 'Sin asignar' }}</span>
                            <span>📅 <strong>Fecha entrega:</strong> {{ $t->fecha_entrega ? $t->fecha_entrega->format('d/m/Y') : '—' }}</span>
                            <span>⚡ <strong>Prioridad:</strong> <span class="capitalize font-semibold text-amber-600">{{ $t->prioridad }}</span></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <button onclick="cerrarModalAlertas(); cargarTarea({{ $t->id }});"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 shadow-sm transition">
                            Ver / Editar
                        </button>
                        <button onclick="marcarCompletadaRapido({{ $t->id }})"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold text-white shadow-sm transition hover:opacity-90"
                                style="background:#39a900;">
                            ✓ Completar
                        </button>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <h4 class="font-extrabold text-gray-900 text-base">Sin tareas próximas a vencer</h4>
                    <p class="text-sm text-gray-500 mt-1">No hay tareas programadas para vencer en los próximos 3 días.</p>
                </div>
                @endforelse
            </div>

        </div>

        {{-- Footer modal --}}
        <div class="px-7 py-4 bg-gray-50 border-t flex items-center justify-between shrink-0" style="border-color:#f3f0e8;">
            <span class="text-xs text-gray-500 font-medium">Total de alertas activas: {{ $alertasVencidas->count() + $alertasProximas->count() }}</span>
            <div class="flex items-center gap-3">
                <a href="{{ route('superadmin.ejecucion.index', ['alerta' => 'vencidas']) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold border border-red-200 text-red-700 bg-white hover:bg-red-50 transition">
                    Ver vencidas en tablero
                </a>
                <button type="button" onclick="cerrarModalAlertas()"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white transition hover:opacity-90"
                        style="background:#71277a;">
                    Cerrar
                </button>
            </div>
        </div>

    </div>
</div>
</script>

{{-- ══ MODAL CONFIRMACIÓN NOTIFICAR PENDIENTES ════════════════ --}}
<div id="modalConfirmNotificar" class="fixed inset-0 z-[60] hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-all" onclick="cerrarModalNotificar()">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-7 text-center border animate-in fade-in zoom-in-95 duration-150" style="border-color:#e7e0cc;" onclick="event.stopPropagation()">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xs" style="background:#f0fdf4;">
            <svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
            </svg>
        </div>
        <h3 class="text-lg font-black text-gray-900 mb-2">¿Notificar Tareas y Requerimientos?</h3>
        <p class="text-sm text-gray-600 mb-6 leading-relaxed">
            Se enviará un correo electrónico con el resumen detallado de tareas y requerimientos pendientes a cada uno de los responsables asignados y a los administradores.
        </p>
        <div class="flex gap-3">
            <button type="button" onclick="cerrarModalNotificar()"
                    class="flex-1 py-2.5 rounded-xl font-bold text-xs border transition hover:bg-gray-50 cursor-pointer"
                    style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            <button type="button" onclick="document.getElementById('formNotificarPendientes').submit()"
                    class="flex-1 py-2.5 rounded-xl font-bold text-xs text-white transition hover:opacity-90 shadow-sm cursor-pointer"
                    style="background:#39a900;">Sí, enviar correos</button>
        </div>
    </div>
</div>

<script>
function confirmarNotificarPendientes() {
    const modal = document.getElementById('modalConfirmNotificar');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}
function cerrarModalNotificar() {
    const modal = document.getElementById('modalConfirmNotificar');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}
</script>

{{-- ══ MODAL CONFIRMACIÓN ELIMINAR ══════════════════════════ --}}
<div id="modalConfirm" class="fixed inset-0 z-[60] hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.6);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-7 text-center" onclick="event.stopPropagation()">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2;">
            <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
            </svg>
        </div>
        <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar tarea?</h3>
        <p class="text-sm mb-6" style="color:#5a5a4f;">Esta acción no se puede deshacer.</p>
        <div class="flex gap-3">
            <button onclick="eliminarTareaConfirmado()"
                    class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90"
                    style="background:#ef4444;">Sí, eliminar</button>
            <button onclick="cancelarEliminar()"
                    class="flex-1 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50"
                    style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
        </div>
    </div>
</div>

</x-superadmin-layout>
