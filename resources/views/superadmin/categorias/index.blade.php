@php $title = 'Categorías'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Gestión de Categorías</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Organiza y administra las categorías de productos de la plataforma.</p>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0"
                style="background:#fdc300; color:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
            </svg>
            Nueva Categoría
        </button>
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

    {{-- STAT --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 mb-6 flex items-center gap-5">
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl shrink-0" style="background:#faf5ff;">
            🗂️
        </div>
        <div>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $total }}</p>
            <p class="text-sm" style="color:#9a9a8a;">Categorías registradas</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('superadmin.categorias.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar categorías..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[130px]">
                <select name="estado" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50"
                    style="border-color:#e7e0cc; color:#5a5a4f;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                </svg>
                Filtrar
            </button>
            @if(request()->hasAny(['search','estado']))
                <a href="{{ route('superadmin.categorias.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:2px solid #f3f0e8;">
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Nombre</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Descripción</th>
                    <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Productos</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                    <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $cat)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xl shrink-0" style="background:#faf5ff;">
                                {{ $cat->icono ?? '📦' }}
                            </div>
                            <a href="{{ route('superadmin.categorias.show', $cat) }}"
                               class="font-bold hover:underline" style="color:#71277a;">{{ $cat->nombre }}</a>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm" style="color:#5a5a4f; max-width:300px;">
                        {{ $cat->descripcion ?: '—' }}
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center justify-center w-10 h-7 rounded-lg text-xs font-bold"
                              style="background:#f0fdf4; color:#166534;">
                            {{ $productosCount[$cat->nombre] ?? 0 }}
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        @if($cat->activo)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background:#f0fdf4; color:#166534;">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Activo
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background:#fffbeb; color:#d97706;">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Inactivo
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick='abrirModalEditar(@json($cat))'
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#f0fdf4; color:#39a900;" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                </svg>
                            </button>

                            {{-- Menú extra: toggle + eliminar --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                        style="background:#f3f0e8; color:#5a5a4f;">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/>
                                    </svg>
                                </button>
                                <div x-show="open" @click.outside="open = false"
                                     class="absolute right-0 mt-1 w-40 bg-white rounded-xl shadow-lg border z-10 py-1"
                                     style="border-color:#f3f0e8;">
                                    <form method="POST" action="{{ route('superadmin.categorias.toggle', $cat) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-gray-50" style="color:#5a5a4f;">
                                            {{ $cat->activo ? '⏸ Desactivar' : '▶ Activar' }}
                                        </button>
                                    </form>
                                    <button onclick="abrirModalEliminar({{ $cat->id }})"
                                            class="w-full text-left px-4 py-2 text-xs font-semibold hover:bg-gray-50" style="color:#ef4444;">
                                        🗑 Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center" style="color:#9a9a8a;">
                        <div class="text-4xl mb-3">🗂️</div>
                        <p class="text-sm font-semibold">No hay categorías registradas</p>
                        <p class="text-xs mt-1">Crea la primera con el botón "Nueva Categoría".</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t text-xs" style="border-color:#f3f0e8; color:#9a9a8a;">
            Mostrando {{ $categorias->count() }} categoría{{ $categorias->count() != 1 ? 's' : '' }}
        </div>
    </div>
</div>

{{-- ══ MODAL CREAR / EDITAR ══════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-7 py-5 border-b" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xl" style="background:#faf5ff;" id="previewIcono">📦</div>
                <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nueva Categoría</h2>
            </div>
            <button onclick="cerrarModal('modalForm')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formCategoria" method="POST" class="px-7 py-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            @if($errors->any())
            <div class="px-4 py-3 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-4 gap-3">
                <div class="col-span-1">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Icono</label>
                    <input type="text" name="icono" id="inputIcono" maxlength="4" placeholder="📦"
                           class="w-full px-3 py-2.5 rounded-xl border text-center text-xl outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           oninput="document.getElementById('previewIcono').textContent = this.value || '📦'">
                </div>
                <div class="col-span-3">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Nombre <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nombre" id="inputNombre" required placeholder="Ej: Granos, Frutas..."
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Descripción</label>
                <textarea name="descripcion" id="inputDescripcion" rows="2" placeholder="Describe brevemente la categoría..."
                          class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none resize-none"
                          style="border-color:#e7e0cc; background:#fafafa;"></textarea>
            </div>

            <div class="flex items-center gap-3">
                <input type="hidden" name="activo" value="0">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="activo" id="inputActivo" value="1" checked
                           class="w-4 h-4 rounded accent-green-600">
                    <span class="text-sm font-semibold" style="color:#1c2b16;">Activo</span>
                </label>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit" id="btnGuardar"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90"
                        style="background:#39a900;">Crear categoría</button>
                <button type="button" onclick="cerrarModal('modalForm')"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL ELIMINAR ══════════════════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div class="text-4xl mb-4">🗑️</div>
            <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar categoría?</h3>
            <p class="text-sm mb-6" style="color:#5a5a4f;">Los productos asignados a esta categoría no serán eliminados.</p>
            <form id="formEliminar" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm text-white" style="background:#ef4444;">Sí, eliminar</button>
                    <button type="button" onclick="cerrarModal('modalEliminar')" class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Alpine.js para el menú desplegable --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
function abrirModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    document.body.style.overflow = '';
}
['modalForm','modalEliminar'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(id);
    });
});

function abrirModalCrear() {
    document.getElementById('modalFormTitle').textContent = 'Nueva Categoría';
    document.getElementById('btnGuardar').textContent = 'Crear categoría';
    document.getElementById('formCategoria').action = '{{ route("superadmin.categorias.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inputIcono').value = '';
    document.getElementById('inputNombre').value = '';
    document.getElementById('inputDescripcion').value = '';
    document.getElementById('inputActivo').checked = true;
    document.getElementById('previewIcono').textContent = '📦';
    abrirModal('modalForm');
}

function abrirModalEditar(cat) {
    document.getElementById('modalFormTitle').textContent = 'Editar Categoría';
    document.getElementById('btnGuardar').textContent = 'Guardar cambios';
    document.getElementById('formCategoria').action = '/superadmin/categorias/' + cat.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputIcono').value = cat.icono || '';
    document.getElementById('inputNombre').value = cat.nombre;
    document.getElementById('inputDescripcion').value = cat.descripcion || '';
    document.getElementById('inputActivo').checked = !!cat.activo;
    document.getElementById('previewIcono').textContent = cat.icono || '📦';
    abrirModal('modalForm');
}

function abrirModalEliminar(id) {
    document.getElementById('formEliminar').action = '/superadmin/categorias/' + id;
    abrirModal('modalEliminar');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
@endif
</script>

</x-superadmin-layout>
