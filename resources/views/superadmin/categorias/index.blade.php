@php $title = 'Categorías'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;" x-data="{ vista: 'grid' }">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Gestión de Categorías</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Organiza y administra las categorías de productos de la plataforma.</p>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0 shadow-sm"
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

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0" style="background:#faf5ff;">
                🗂️
            </div>
            <div>
                <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $total }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Categorías registradas</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0 bg-green-50 text-green-600">
                <svg class="w-5 h-5 stroke-current fill-none stroke-2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-green-700">{{ $categorias->where('activo', true)->count() }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Activas</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0 bg-amber-50 text-amber-600">
                <svg class="w-5 h-5 stroke-current fill-none stroke-2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-amber-600">{{ $categorias->where('activo', false)->count() }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Inactivas</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0 bg-purple-50 text-zmorado">
                <svg class="w-5 h-5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-zmorado">{{ array_sum($productosCount->toArray()) }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Productos clasificados</p>
            </div>
        </div>
    </div>

    {{-- FILTROS Y SELECTOR DE VISTA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('superadmin.categorias.index') }}" class="flex flex-wrap gap-3 items-center flex-1">
                <div class="flex-1 min-w-[200px]">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar categorías..."
                               class="w-full pl-9 pr-4 py-2 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                </div>
                <div class="min-w-[130px]">
                    <select name="estado" class="w-full px-3 py-2 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Todos los estados</option>
                        <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                        <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl font-bold text-sm border hover:bg-gray-50 transition"
                        style="border-color:#e7e0cc; color:#5a5a4f;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Filtrar
                </button>
                @if(request()->hasAny(['search','estado']))
                    <a href="{{ route('superadmin.categorias.index') }}" class="px-4 py-2 rounded-xl font-bold text-sm border hover:bg-gray-50 transition" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
                @endif
            </form>

            {{-- Selector de vista Cuadrícula / Tabla --}}
            <div class="flex items-center gap-1 p-1 bg-gray-100 rounded-xl shrink-0 self-end md:self-auto">
                <button type="button"
                        @click="vista = 'grid'"
                        :class="vista === 'grid' ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    <span>Cuadrícula (5 por fila)</span>
                </button>
                <button type="button"
                        @click="vista = 'table'"
                        :class="vista === 'table' ? 'bg-white text-gray-900 shadow-xs font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'"
                        class="px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 transition">
                    <svg class="w-3.5 h-3.5 stroke-current fill-none stroke-2" viewBox="0 0 24 24">
                        <line x1="8" y1="6" x2="21" y2="6"/>
                        <line x1="8" y1="12" x2="21" y2="12"/>
                        <line x1="8" y1="18" x2="21" y2="18"/>
                        <line x1="3" y1="6" x2="3.01" y2="6"/>
                        <line x1="3" y1="12" x2="3.01" y2="12"/>
                        <line x1="3" y1="18" x2="3.01" y2="18"/>
                    </svg>
                    <span>Tabla</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ══ VISTA CUADRÍCULA (5 por fila en desktop) ═══════════════ --}}
    <div x-show="vista === 'grid'">
        @if($categorias->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
            @foreach($categorias as $cat)
            @php $pCount = $productosCount[$cat->nombre] ?? 0; @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group hover:shadow-md transition-all duration-200">

                {{-- Imagen compacta --}}
                <div class="relative h-32 overflow-hidden bg-[#f3f0e8] shrink-0">
                    @if($cat->imagen)
                        <img src="{{ Storage::url($cat->imagen) }}" alt="{{ $cat->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-purple-50/70 via-emerald-50/30 to-amber-50/60">
                            <span class="text-4xl filter drop-shadow-xs group-hover:scale-110 transition-transform duration-300">
                                {{ $cat->icono ?? '📦' }}
                            </span>
                        </div>
                    @endif

                    {{-- Badge Estado --}}
                    @if($cat->activo)
                        <span class="absolute top-2.5 left-2.5 text-[10px] font-bold px-2 py-0.5 rounded-full text-white bg-green-600/90 backdrop-blur-xs shadow-xs flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> Activo
                        </span>
                    @else
                        <span class="absolute top-2.5 left-2.5 text-[10px] font-bold px-2 py-0.5 rounded-full text-white bg-amber-500/90 backdrop-blur-xs shadow-xs flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span> Inactivo
                        </span>
                    @endif

                    {{-- Botones de Acción rápidos --}}
                    <div class="absolute top-2 right-2 flex gap-1 opacity-90 group-hover:opacity-100 transition-opacity">
                        <button onclick='abrirModalEditar(@json($cat))'
                                class="w-7 h-7 rounded-lg flex items-center justify-center bg-white/95 text-emerald-600 shadow-sm hover:bg-emerald-50 transition"
                                title="Editar categoría">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                            </svg>
                        </button>
                        <button onclick="abrirModalEliminar({{ $cat->id }})"
                                class="w-7 h-7 rounded-lg flex items-center justify-center bg-white/95 text-red-500 shadow-sm hover:bg-red-50 transition"
                                title="Eliminar categoría">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Info de la categoría --}}
                <div class="p-3.5 flex flex-col flex-1 justify-between">
                    <div>
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="text-sm shrink-0">{{ $cat->icono ?? '📦' }}</span>
                            <a href="{{ route('superadmin.categorias.show', $cat) }}"
                               class="font-extrabold text-sm text-gray-900 hover:text-zmorado transition truncate leading-tight"
                               title="{{ $cat->nombre }}">
                                {{ $cat->nombre }}
                            </a>
                        </div>
                        <p class="text-xs text-gray-500 line-clamp-2 min-h-[32px] leading-relaxed">
                            {{ $cat->descripcion ?: 'Sin descripción registrada' }}
                        </p>
                    </div>

                    {{-- Footer de tarjeta --}}
                    <div class="flex items-center justify-between gap-2 pt-3 mt-2 border-t border-gray-100">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-[#f0fdf4] text-[#166534]">
                            <svg class="w-3 h-3 stroke-current fill-none stroke-2 shrink-0" viewBox="0 0 24 24">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                            </svg>
                            {{ $pCount }} prod.
                        </span>

                        <form method="POST" action="{{ route('superadmin.categorias.toggle', $cat) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="text-[11px] font-bold px-2 py-0.5 rounded-md transition {{ $cat->activo ? 'text-gray-500 hover:bg-gray-100' : 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100' }}"
                                    title="{{ $cat->activo ? 'Pausar categoría' : 'Activar categoría' }}">
                                {{ $cat->activo ? 'Pausar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 mb-8" style="color:#9a9a8a;">
            <div class="text-4xl mb-3">🗂️</div>
            <p class="text-sm font-semibold">No se encontraron categorías</p>
            <p class="text-xs mt-1">Intenta con otros filtros o crea una nueva categoría.</p>
        </div>
        @endif
    </div>

    {{-- ══ VISTA TABLA ════════════════════════════════════════════ --}}
    <div x-show="vista === 'table'">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:2px solid #f3f0e8;">
                        <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Categoría</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Descripción</th>
                        <th class="text-center px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Productos</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                        <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categorias as $cat)
                    @php $pCount = $productosCount[$cat->nombre] ?? 0; @endphp
                    <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center shrink-0 border border-gray-100" style="background:#faf5ff;">
                                    @if($cat->imagen)
                                        <img src="{{ Storage::url($cat->imagen) }}" alt="{{ $cat->nombre }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl">{{ $cat->icono ?? '📦' }}</span>
                                    @endif
                                </div>
                                <div>
                                    <a href="{{ route('superadmin.categorias.show', $cat) }}"
                                       class="font-bold hover:underline block leading-tight" style="color:#71277a;">{{ $cat->nombre }}</a>
                                    <span class="text-[11px] text-gray-400">Icono: {{ $cat->icono ?? '📦' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm" style="color:#5a5a4f; max-width:300px;">
                            {{ $cat->descripcion ?: '—' }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold"
                                  style="background:#f0fdf4; color:#166534;">
                                {{ $pCount }}
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
</div>

{{-- ══ MODAL CREAR / EDITAR ══════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-7 py-5 border-b" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xl" style="background:#faf5ff;" id="previewIcono">📦</div>
                <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nueva Categoría</h2>
            </div>
            <button onclick="cerrarModal('modalForm')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formCategoria" method="POST" enctype="multipart/form-data" class="px-7 py-6 space-y-4">
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
                    <input type="text" name="nombre" id="inputNombre" required maxlength="100" placeholder="Ej: Granos, Frutas..."
                           pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]+$"
                           title="Solo letras y espacios (no se permiten números)"
                           oninput="this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\-]/g, '')"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <p class="text-[11px] mt-1 text-gray-400">Solo letras y espacios (sin números).</p>
                </div>
            </div>

            {{-- Campo Imagen --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Imagen de la categoría</label>
                <div class="flex items-center gap-3 p-3 rounded-xl border" style="border-color:#e7e0cc; background:#fafafa;">
                    <div id="imagePreviewContainer" class="w-16 h-16 rounded-xl border border-dashed border-gray-300 bg-white overflow-hidden flex items-center justify-center shrink-0 relative">
                        <img id="imagePreview" src="" alt="Vista previa" class="w-full h-full object-cover hidden">
                        <div id="imagePlaceholder" class="text-gray-400 flex flex-col items-center">
                            <svg class="w-6 h-6 opacity-60" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>
                            </svg>
                            <span class="text-[9px] mt-0.5">Sin imagen</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <input type="file" name="imagen" id="inputImagen" accept="image/png,image/jpeg,image/webp" class="hidden" onchange="previewImage(this)">
                        <button type="button" onclick="document.getElementById('inputImagen').click()"
                                class="px-3.5 py-1.5 rounded-xl border border-gray-300 text-xs font-semibold hover:bg-white transition text-gray-700 bg-white shadow-2xs">
                            Seleccionar imagen
                        </button>
                        <p class="text-[11px] text-gray-400 mt-1">Formatos: JPG, PNG, WEBP (máx. 2MB)</p>
                        <div id="removeImageContainer" class="hidden mt-1.5">
                            <label class="inline-flex items-center gap-1.5 text-xs text-red-600 cursor-pointer">
                                <input type="checkbox" name="eliminar_imagen" id="checkEliminarImagen" value="1" class="rounded accent-red-600">
                                <span>Quitar imagen actual</span>
                            </label>
                        </div>
                    </div>
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
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90 transition"
                        style="background:#39a900;">Crear categoría</button>
                <button type="button" onclick="cerrarModal('modalForm')"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50 transition"
                        style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL ELIMINAR ══════════════════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div class="text-4xl mb-4">🗑️</div>
            <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar categoría?</h3>
            <p class="text-sm mb-6" style="color:#5a5a4f;">Los productos asignados a esta categoría no serán eliminados.</p>
            <form id="formEliminar" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90 transition" style="background:#ef4444;">Sí, eliminar</button>
                    <button type="button" onclick="cerrarModal('modalEliminar')" class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50 transition" style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const STORAGE_URL = "{{ Storage::url('') }}";

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('imagePlaceholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

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

    // Reset imagen
    const inputImg = document.getElementById('inputImagen');
    if (inputImg) inputImg.value = '';
    const imgPreview = document.getElementById('imagePreview');
    const imgPlaceholder = document.getElementById('imagePlaceholder');
    imgPreview.src = '';
    imgPreview.classList.add('hidden');
    imgPlaceholder.classList.remove('hidden');
    document.getElementById('removeImageContainer').classList.add('hidden');
    document.getElementById('checkEliminarImagen').checked = false;

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

    // Imagen
    const inputImg = document.getElementById('inputImagen');
    if (inputImg) inputImg.value = '';
    const imgPreview = document.getElementById('imagePreview');
    const imgPlaceholder = document.getElementById('imagePlaceholder');
    const removeContainer = document.getElementById('removeImageContainer');
    document.getElementById('checkEliminarImagen').checked = false;

    if (cat.imagen) {
        imgPreview.src = STORAGE_URL + cat.imagen;
        imgPreview.classList.remove('hidden');
        imgPlaceholder.classList.add('hidden');
        removeContainer.classList.remove('hidden');
    } else {
        imgPreview.src = '';
        imgPreview.classList.add('hidden');
        imgPlaceholder.classList.remove('hidden');
        removeContainer.classList.add('hidden');
    }

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
