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

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Tareas y Avances</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Gestiona las tareas de tus proyectos y da seguimiento a los avances del equipo.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button onclick="abrirModalCrear()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90"
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
        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90" style="background:#39a900;">Filtrar</button>
        @if(request()->hasAny(['search','proyecto_id','prioridad']))
            <a href="{{ route('superadmin.ejecucion.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
        @endif
    </form>

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:#f3f0e8;">
                <svg class="w-5 h-5" style="color:#5a5a4f;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-extrabold" style="color:#1c2b16;">{{ $stats['total'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Total de tareas</p>
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
                <p class="text-xs" style="color:#9a9a8a;">Completadas · {{ $stats['total'] > 0 ? round($stats['completadas']/$stats['total']*100) : 0 }}%</p>
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
                <p class="text-xs" style="color:#9a9a8a;">En progreso · {{ $stats['total'] > 0 ? round($stats['en_progreso']/$stats['total']*100) : 0 }}%</p>
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
                <p class="text-xs" style="color:#9a9a8a;">Pendientes · {{ $stats['total'] > 0 ? round($stats['pendientes']/$stats['total']*100) : 0 }}%</p>
            </div>
        </div>
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
                <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 cursor-grab hover:shadow-md transition group select-none"
                     draggable="true"
                     data-id="{{ $tarea->id }}"
                     ondragstart="onDragStart(event, {{ $tarea->id }})"
                     ondragend="onDragEnd(event)"
                     onclick="cargarTarea({{ $tarea->id }})">

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
                            <span>{{ $tarea->responsable->name ?? '—' }}</span>
                        </div>
                        @if($tarea->fecha_entrega)
                        <div class="flex items-center gap-1">
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
