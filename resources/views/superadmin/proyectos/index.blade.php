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
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Gestión de Proyectos</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Administra y da seguimiento a los proyectos agrícolas de tu organización.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            {{-- Búsqueda inline --}}
            <form method="GET" action="{{ route('superadmin.proyectos.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar proyectos..."
                           class="pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none w-52"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <select name="estado" onchange="this.form.submit()"
                        class="px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    @foreach($estadoConfig as $key => $ec)
                        <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>{{ $ec['label'] }}</option>
                    @endforeach
                </select>
                <select name="orden" onchange="this.form.submit()"
                        class="px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="recientes" {{ request('orden','recientes') === 'recientes' ? 'selected' : '' }}>Más recientes</option>
                    <option value="nombre"    {{ request('orden') === 'nombre'    ? 'selected' : '' }}>Nombre A-Z</option>
                    <option value="avance_desc" {{ request('orden') === 'avance_desc' ? 'selected' : '' }}>Mayor avance</option>
                    <option value="avance_asc"  {{ request('orden') === 'avance_asc'  ? 'selected' : '' }}>Menor avance</option>
                </select>
            </form>
            <button onclick="abrirModalCrear()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90"
                    style="background:#71277a; color:white;">
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

    <p class="text-sm mb-5" style="color:#9a9a8a;">{{ $total }} proyecto{{ $total != 1 ? 's' : '' }} encontrado{{ $total != 1 ? 's' : '' }}</p>

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
    abrirModal('modalForm');
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

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
@endif
</script>

</x-superadmin-layout>
