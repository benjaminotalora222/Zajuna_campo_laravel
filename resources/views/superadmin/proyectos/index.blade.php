@php
$title = 'Proyectos';

$estadoConfig = [
    'en_progreso' => ['label' => 'En Progreso', 'bg' => '#fdc300', 'color' => '#1c2b16'],
    'planificado' => ['label' => 'Planificado',  'bg' => '#71277a', 'color' => 'white'],
    'completado'  => ['label' => 'Completado',   'bg' => '#39a900', 'color' => 'white'],
    'pausado'     => ['label' => 'Pausado',      'bg' => '#9ca3af', 'color' => 'white'],
];
@endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Gestión de Proyectos</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Administra y da seguimiento a los proyectos agrícolas de tu organización.</p>
        </div>
        {{-- BOTONES DE ACCIÓN PRINCIPAL --}}
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('superadmin.proyectos.aprendices.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-bold bg-white hover:bg-gray-50 transition shadow-sm"
               style="border-color:#e7e0cc; color:#1c2b16;" title="Ver listado de aprendices vinculados">
                <svg class="w-4 h-4" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>Aprendices</span>
                <span class="px-2 py-0.5 rounded-full text-xs font-black text-white" style="background:#71277a;">{{ $totalAprendices ?? 0 }}</span>
            </a>
            <button onclick="abrirModalAprendiz()"
                    id="btnRegistroAprendices"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-95 shadow-sm text-white"
                    style="background:#39a900;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M19 8v6M22 11h-6"/>
                </svg>
                Registro de aprendices
            </button>
            <button onclick="abrirModalCrear()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-95 shadow-sm text-white"
                    style="background:#71277a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                </svg>
                Nuevo Proyecto
            </button>
        </div>
    </div>

    {{-- ALERTA --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold"
         style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- BARRA DE FILTROS Y BÚSQUEDA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('superadmin.proyectos.index') }}" class="flex items-center gap-2.5 flex-wrap flex-1">
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar proyectos por nombre o descripción..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none transition focus:border-purple-600 bg-gray-50"
                       style="border-color:#e7e0cc;">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                </svg>
            </div>
            <select name="estado" onchange="this.form.submit()"
                    class="px-3.5 py-2.5 rounded-xl border text-sm font-medium outline-none transition focus:border-purple-600 bg-gray-50 cursor-pointer"
                    style="border-color:#e7e0cc;">
                <option value="">Todos los estados</option>
                @foreach($estadoConfig as $key => $ec)
                    <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>{{ $ec['label'] }}</option>
                @endforeach
            </select>
            <select name="orden" onchange="this.form.submit()"
                    class="px-3.5 py-2.5 rounded-xl border text-sm font-medium outline-none transition focus:border-purple-600 bg-gray-50 cursor-pointer"
                    style="border-color:#e7e0cc;">
                <option value="recientes" {{ request('orden','recientes') === 'recientes' ? 'selected' : '' }}>Más recientes</option>
                <option value="nombre"    {{ request('orden') === 'nombre'    ? 'selected' : '' }}>Nombre A-Z</option>
                <option value="avance_desc" {{ request('orden') === 'avance_desc' ? 'selected' : '' }}>Mayor avance</option>
                <option value="avance_asc"  {{ request('orden') === 'avance_asc'  ? 'selected' : '' }}>Menor avance</option>
            </select>
            @if(request()->anyFilled(['search', 'estado', 'orden']))
                <a href="{{ route('superadmin.proyectos.index') }}" class="px-2.5 py-2 text-xs font-bold text-gray-500 hover:text-gray-800">Limpiar</a>
            @endif
        </form>
        <div class="text-xs font-bold px-3.5 py-2 rounded-full shrink-0" style="background:#fdf9ee; color:#71277a; border: 1px solid #e7e0cc;">
            {{ $total }} proyecto{{ $total != 1 ? 's' : '' }} encontrado{{ $total != 1 ? 's' : '' }}
        </div>
    </div>

    {{-- GRID --}}
    @if($proyectos->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($proyectos as $p)
        @php
            $ec  = $estadoConfig[$p->estado] ?? $estadoConfig['planificado'];
            $pct = (int) ($p->porcentajeAvance ?? 0);
            $barColor = match($p->estado) {
                'completado'  => '#39a900',
                'en_progreso' => '#fdc300',
                'pausado'     => '#9ca3af',
                default       => '#71277a',
            };
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group">

            {{-- Imagen --}}
            <div class="relative h-44 overflow-hidden" style="background:#f3f0e8;">
                @if($p->imagen)
                    <img src="{{ Storage::url($p->imagen) }}" alt="{{ $p->nombre }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                            <path d="m21 15-5-5L5 21"/>
                        </svg>
                    </div>
                @endif

                {{-- Badge estado --}}
                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[11px] font-extrabold"
                      style="background:{{ $ec['bg'] }}; color:{{ $ec['color'] }};">
                    {{ $ec['label'] }}
                </span>

                {{-- Acciones hover --}}
                <div class="absolute top-2 right-2 flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick='abrirModalEditar(@json($p))'
                            class="w-7 h-7 rounded-lg flex items-center justify-center shadow-md"
                            style="background:white; color:#39a900;" title="Editar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                        </svg>
                    </button>
                    <button onclick="confirmarEliminar({{ $p->id }}, '{{ addslashes($p->nombre) }}')"
                            class="w-7 h-7 rounded-lg flex items-center justify-center shadow-md"
                            style="background:white; color:#ef4444;" title="Eliminar">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Info --}}
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-extrabold text-sm leading-snug mb-1" style="color:#1c2b16;">{{ $p->nombre }}</h3>

                @if($p->descripcion)
                <p class="text-xs leading-relaxed mb-3 line-clamp-2" style="color:#5a5a4f;">{{ $p->descripcion }}</p>
                @endif

                {{-- Barra de avance --}}
                <div class="mt-auto">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold" style="color:#1c2b16;">{{ $pct }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full overflow-hidden" style="background:#f3f0e8;">
                        <div class="h-full rounded-full transition-all duration-500"
                             style="width:{{ $pct }}%; background:{{ $barColor }};"></div>
                    </div>
                </div>

                {{-- Aprendices asignados badge --}}
                @if($p->aprendices->count() > 0)
                <div class="flex items-center gap-1.5 mt-2.5 text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 w-fit">
                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>{{ $p->aprendices->count() }} aprendiz{{ $p->aprendices->count() != 1 ? 'es' : '' }}</span>
                </div>
                @endif

                {{-- Pie: fecha + responsable --}}
                <div class="flex items-center justify-between mt-3 text-xs" style="color:#9a9a8a;">
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                        </svg>
                        {{ $p->fecha_inicio ? $p->fecha_inicio->locale('es')->isoFormat('D MMM YYYY') : '—' }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold text-white shrink-0"
                             style="background:#71277a;">
                            {{ strtoupper(substr($p->responsable->name ?? '?', 0, 1)) }}
                        </div>
                        <span class="truncate max-w-[80px]">{{ $p->responsable->name ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Botones footer --}}
            <div class="px-4 pb-4 flex gap-2">
                <button onclick='abrirModalEditar(@json($p))'
                        class="flex-1 py-2 rounded-xl text-xs font-bold transition hover:opacity-90"
                        style="background:#fdc300; color:#71277a;">
                    Ver / Editar
                </button>
                <button onclick="confirmarEliminar({{ $p->id }}, '{{ addslashes($p->nombre) }}')"
                        class="w-9 py-2 rounded-xl flex items-center justify-center transition hover:opacity-90"
                        style="background:#fef2f2; color:#ef4444;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    </svg>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-20 text-center" style="color:#9a9a8a;">
        <svg class="w-14 h-14 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M9.5 3h5l1 5-3.5 9h0l-3.5-9 1-5Z"/><path d="M6 21h12"/>
        </svg>
        <p class="text-sm font-semibold">No hay proyectos registrados</p>
        <p class="text-xs mt-1">Crea el primer proyecto con el botón "Nuevo Proyecto".</p>
    </div>
    @endif
</div>

{{-- ══ MODAL CREAR / EDITAR ══════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl max-h-[95vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-7 py-5 border-b sticky top-0 bg-white z-10" style="border-color:#f3f0e8;">
            <h2 id="modalTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nuevo Proyecto</h2>
            <button onclick="cerrarModal()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formProyecto" method="POST" enctype="multipart/form-data" class="px-7 py-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            @if($errors->any())
            <div class="px-4 py-3 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Nombre <span style="color:#ef4444;">*</span></label>
                <input type="text" name="nombre" id="inpNombre" required placeholder="Ej: Renovación de Cafetales"
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                       style="border-color:#e7e0cc; background:#fafafa;">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Descripción</label>
                <textarea name="descripcion" id="inpDescripcion" rows="2"
                          class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none resize-none"
                          style="border-color:#e7e0cc; background:#fafafa;"></textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Objetivos</label>
                <textarea name="objetivos" id="inpObjetivos" rows="2"
                          class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none resize-none"
                          style="border-color:#e7e0cc; background:#fafafa;"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Estado <span style="color:#ef4444;">*</span></label>
                    <select name="estado" id="inpEstado" required
                            class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                            style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="planificado">Planificado</option>
                        <option value="en_progreso">En Progreso</option>
                        <option value="completado">Completado</option>
                        <option value="pausado">Pausado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Avance (%)</label>
                    <input type="number" name="porcentajeAvance" id="inpAvance" min="0" max="100" value="0"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha inicio</label>
                    <input type="date" name="fecha_inicio" id="inpFechaInicio"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha fin</label>
                    <input type="date" name="fecha_fin" id="inpFechaFin"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Responsable</label>
                    <select name="responsable_id" id="inpResponsable"
                            class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                            style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Sin asignar</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Aprendices vinculados al proyecto (Múltiples) --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-sm font-semibold" style="color:#1c2b16;">
                        Aprendices vinculados al proyecto
                    </label>
                    <span id="aprendicesCountBadge" class="text-[11px] font-bold px-2 py-0.5 rounded-full" style="background:#f3e8ff; color:#71277a;">
                        0 seleccionados
                    </span>
                </div>
                <p class="text-xs text-gray-500 mb-2">Selecciona los aprendices registrados que participarán en este proyecto (puedes agregar todos los que desees):</p>

                <div class="border rounded-2xl p-3 bg-gray-50/70" style="border-color:#e7e0cc;">
                    {{-- Buscador rápido de aprendices --}}
                    <div class="relative mb-2.5">
                        <input type="text" id="busquedaAprendizModal" oninput="filtrarAprendicesModal(this.value)"
                               placeholder="Filtrar por nombre, documento o ficha..."
                               class="w-full pl-8 pr-3 py-1.5 rounded-xl border text-xs outline-none bg-white transition focus:border-green-600"
                               style="border-color:#e7e0cc;">
                        <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>

                    {{-- Botones selección rápida --}}
                    <div class="flex items-center justify-between text-[11px] mb-2 px-1">
                        <button type="button" onclick="seleccionarTodosAprendices(true)" class="font-bold text-emerald-700 hover:underline">
                            Seleccionar todos
                        </button>
                        <button type="button" onclick="seleccionarTodosAprendices(false)" class="font-bold text-gray-500 hover:underline">
                            Deseleccionar todos
                        </button>
                    </div>

                    {{-- Lista con scroll --}}
                    <div id="listaAprendicesCheckboxes" class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                        @forelse($aprendices as $ap)
                        <label class="item-aprendiz flex items-start gap-2.5 p-2 rounded-xl bg-white border border-transparent hover:border-emerald-300 hover:bg-emerald-50/30 transition cursor-pointer has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/60"
                               data-search="{{ strtolower($ap->nombre_completo . ' ' . $ap->numero_documento . ' ' . $ap->ficha_programa . ' ' . $ap->programa_formacion) }}">
                            <input type="checkbox" name="aprendices[]" value="{{ $ap->id }}"
                                   id="chk_aprendiz_{{ $ap->id }}"
                                   onchange="actualizarContadorAprendices()"
                                   class="chk-aprendiz mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 border-gray-300">
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-xs truncate" style="color:#1c2b16;">
                                    {{ $ap->nombre_completo }}
                                </div>
                                <div class="text-[10.5px] text-gray-500 flex items-center gap-1.5 flex-wrap mt-0.5">
                                    <span class="font-semibold">{{ $ap->tipo_documento }}: {{ $ap->numero_documento }}</span>
                                    <span>•</span>
                                    <span>Ficha: <strong>{{ $ap->ficha_programa }}</strong></span>
                                    <span>•</span>
                                    <span class="text-emerald-700 font-semibold truncate max-w-[180px]">{{ $ap->programa_completo }}</span>
                                </div>
                            </div>
                        </label>
                        @empty
                        <div class="text-center py-5 text-xs text-gray-400">
                            <p class="font-bold text-gray-500">No hay aprendices registrados aún</p>
                            <p class="text-[11px] mt-0.5">Usa el botón "Registro de aprendices" para agregar aprendices al sistema.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Imagen</label>
                <div id="imgPreviewWrap" class="hidden mb-2">
                    <img id="imgPreview" src="" alt="" class="h-24 rounded-xl object-cover border" style="border-color:#e7e0cc;">
                </div>
                <label class="flex items-center gap-3 cursor-pointer px-4 py-3 rounded-xl border border-dashed hover:border-green-500 transition"
                       style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="w-5 h-5 shrink-0" style="color:#9a9a8a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <span class="text-sm" style="color:#9a9a8a;" id="imgLabel">Selecciona una imagen</span>
                    <input type="file" name="imagen" id="inpImagen" accept="image/*" class="hidden"
                           onchange="previewImg(this)">
                </label>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit" id="btnGuardar"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90"
                        style="background:#71277a;">Crear proyecto</button>
                <button type="button" onclick="cerrarModal()"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL CONFIRMAR ELIMINAR ══════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-[60] hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.6);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-7 text-center" onclick="event.stopPropagation()">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2;">
            <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
            </svg>
        </div>
        <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar proyecto?</h3>
        <p id="eliminarNombre" class="text-sm font-semibold mb-1" style="color:#71277a;"></p>
        <p class="text-sm mb-6" style="color:#5a5a4f;">Esta acción no se puede deshacer.</p>
        <form id="formEliminar" method="POST">
            @csrf @method('DELETE')
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90" style="background:#ef4444;">Sí, eliminar</button>
                <button type="button" onclick="cerrarEliminar()" class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal() {
    document.getElementById('modalForm').classList.add('hidden');
    document.getElementById('modalForm').classList.remove('flex');
    document.body.style.overflow = '';
}
function cerrarEliminar() {
    document.getElementById('modalEliminar').classList.add('hidden');
    document.getElementById('modalEliminar').classList.remove('flex');
    document.body.style.overflow = '';
}
document.getElementById('modalForm').addEventListener('click', function(e) { if (e.target === this) cerrarModal(); });
document.getElementById('modalEliminar').addEventListener('click', function(e) { if (e.target === this) cerrarEliminar(); });

function abrirModalCrear() {
    document.getElementById('modalTitle').textContent     = 'Nuevo Proyecto';
    document.getElementById('btnGuardar').textContent     = 'Crear proyecto';
    document.getElementById('formProyecto').action        = '{{ route("superadmin.proyectos.store") }}';
    document.getElementById('formMethod').value           = 'POST';
    document.getElementById('inpNombre').value            = '';
    document.getElementById('inpDescripcion').value       = '';
    document.getElementById('inpObjetivos').value         = '';
    document.getElementById('inpEstado').value            = 'planificado';
    document.getElementById('inpAvance').value            = '0';
    document.getElementById('inpFechaInicio').value       = '';
    document.getElementById('inpFechaFin').value          = '';
    document.getElementById('inpResponsable').value       = '';
    document.getElementById('imgPreviewWrap').classList.add('hidden');
    document.querySelectorAll('.chk-aprendiz').forEach(cb => cb.checked = false);
    actualizarContadorAprendices();
    if (document.getElementById('busquedaAprendizModal')) {
        document.getElementById('busquedaAprendizModal').value = '';
        filtrarAprendicesModal('');
    }
    abrirModal('modalForm');
}

function abrirModalEditar(p) {
    document.getElementById('modalTitle').textContent     = 'Editar Proyecto';
    document.getElementById('btnGuardar').textContent     = 'Guardar cambios';
    document.getElementById('formProyecto').action        = '/superadmin/proyectos/' + p.id;
    document.getElementById('formMethod').value           = 'PUT';
    document.getElementById('inpNombre').value            = p.nombre || '';
    document.getElementById('inpDescripcion').value       = p.descripcion || '';
    document.getElementById('inpObjetivos').value         = p.objetivos || '';
    document.getElementById('inpEstado').value            = p.estado || 'planificado';
    document.getElementById('inpAvance').value            = p.porcentajeAvance || 0;
    document.getElementById('inpFechaInicio').value       = p.fecha_inicio ? p.fecha_inicio.substring(0,10) : '';
    document.getElementById('inpFechaFin').value          = p.fecha_fin    ? p.fecha_fin.substring(0,10)    : '';
    document.getElementById('inpResponsable').value       = p.responsable_id || '';
    if (p.imagen) {
        document.getElementById('imgPreview').src = '/storage/' + p.imagen;
        document.getElementById('imgPreviewWrap').classList.remove('hidden');
    } else {
        document.getElementById('imgPreviewWrap').classList.add('hidden');
    }
    const aprendicesAsignados = (p.aprendices || []).map(a => a.id);
    document.querySelectorAll('.chk-aprendiz').forEach(cb => {
        cb.checked = aprendicesAsignados.includes(parseInt(cb.value));
    });
    actualizarContadorAprendices();
    if (document.getElementById('busquedaAprendizModal')) {
        document.getElementById('busquedaAprendizModal').value = '';
        filtrarAprendicesModal('');
    }
    abrirModal('modalForm');
}

function actualizarContadorAprendices() {
    const total = document.querySelectorAll('.chk-aprendiz:checked').length;
    const badge = document.getElementById('aprendicesCountBadge');
    if (badge) {
        badge.textContent = total + ' seleccionado' + (total !== 1 ? 's' : '');
    }
}

function seleccionarTodosAprendices(marcar) {
    document.querySelectorAll('.item-aprendiz:not(.hidden) .chk-aprendiz').forEach(cb => {
        cb.checked = marcar;
    });
    actualizarContadorAprendices();
}

function filtrarAprendicesModal(q) {
    const query = q.toLowerCase().trim();
    document.querySelectorAll('.item-aprendiz').forEach(item => {
        const searchData = item.getAttribute('data-search') || '';
        if (!query || searchData.includes(query)) {
            item.classList.remove('hidden');
        } else {
            item.classList.add('hidden');
        }
    });
}

function confirmarEliminar(id, nombre) {
    document.getElementById('formEliminar').action    = '/superadmin/proyectos/' + id;
    document.getElementById('eliminarNombre').textContent = nombre;
    abrirModal('modalEliminar');
}

function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imgPreviewWrap').classList.remove('hidden');
            document.getElementById('imgLabel').textContent = input.files[0].name;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

@if($errors->hasAny(['nombre_completo', 'tipo_documento', 'numero_documento', 'genero', 'rh', 'eps', 'telefono', 'correo_electronico', 'tipo_programa', 'programa_formacion', 'ficha_programa', 'semillero', 'nombre_proyecto', 'instructor_lider_asesores']))
    document.addEventListener('DOMContentLoaded', () => abrirModalAprendiz());
@elseif($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
@endif
</script>

<x-modal-registro-aprendiz :proyectos="$proyectos" route="{{ route('superadmin.proyectos.aprendices.store') }}" />

</x-superadmin-layout>
