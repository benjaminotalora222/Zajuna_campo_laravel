@php $title = 'Proveedores'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Proveedores</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Gestiona los proveedores registrados en la plataforma.</p>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0"
                style="background:#fdc300; color:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
            </svg>
            Nuevo Proveedor
        </button>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold"
         style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold"
         style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
        </svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- STATS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        @php
            $total    = $proveedores->total();
            $activos  = \App\Models\Proveedor::where('estado', true)->count();
            $inactivos= \App\Models\Proveedor::where('estado', false)->count();
            $tipos_count = \App\Models\Proveedor::whereNotNull('tipo')->distinct()->count('tipo');
        @endphp
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col gap-1">
            <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Total</p>
            <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ \App\Models\Proveedor::count() }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Registrados</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col gap-1">
            <p class="text-xs font-bold uppercase tracking-wider" style="color:#39a900;">Activos</p>
            <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $activos }}</p>
            <p class="text-xs" style="color:#9a9a8a;">En operación</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col gap-1">
            <p class="text-xs font-bold uppercase tracking-wider" style="color:#ef4444;">Inactivos</p>
            <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $inactivos }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Suspendidos</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col gap-1">
            <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Tipos</p>
            <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $tipos_count }}</p>
            <p class="text-xs" style="color:#9a9a8a;">Categorías</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('superadmin.proveedores.index') }}"
              class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Buscar proveedor</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nombre, correo o teléfono..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Tipo</label>
                <select name="tipo" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t }}" {{ request('tipo') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Estado</label>
                <select name="estado" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <button type="submit"
                    class="px-5 py-2.5 rounded-xl font-bold text-sm text-white transition hover:opacity-90"
                    style="background:#39a900;">Filtrar</button>
            @if(request()->hasAny(['search','tipo','estado']))
            <a href="{{ route('superadmin.proveedores.index') }}"
               class="px-5 py-2.5 rounded-xl font-bold text-sm border transition hover:bg-gray-50"
               style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:2px solid #f3f0e8;">
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Proveedor</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Tipo</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Correo</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Teléfono</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                    <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $p)
                @php
                    $tipoColors = [
                        'Nacional'       => ['bg'=>'#f0fdf4','color'=>'#166534'],
                        'Internacional'  => ['bg'=>'#eff6ff','color'=>'#1d4ed8'],
                        'Local'          => ['bg'=>'#fffbeb','color'=>'#92400e'],
                        'Regional'       => ['bg'=>'#f3e8ff','color'=>'#71277a'],
                    ];
                    $tc = $tipoColors[$p->tipo] ?? ['bg'=>'#f1f5f9','color'=>'#475569'];
                    $initials = strtoupper(substr($p->nombre ?? 'P', 0, 2));
                @endphp
                <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">

                    {{-- Nombre + avatar --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-extrabold text-xs text-white shrink-0"
                                 style="background:#39a900;">{{ $initials }}</div>
                            <span class="font-semibold" style="color:#1c2b16;">{{ $p->nombre ?? '—' }}</span>
                        </div>
                    </td>

                    {{-- Tipo --}}
                    <td class="px-4 py-4">
                        @if($p->tipo)
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold"
                              style="background:{{ $tc['bg'] }}; color:{{ $tc['color'] }};">
                            {{ $p->tipo }}
                        </span>
                        @else <span style="color:#9a9a8a;">—</span> @endif
                    </td>

                    {{-- Correo --}}
                    <td class="px-4 py-4" style="color:#5a5a4f;">
                        @if($p->correo)
                            <a href="mailto:{{ $p->correo }}" class="hover:underline" style="color:#39a900;">{{ $p->correo }}</a>
                        @else <span style="color:#9a9a8a;">—</span> @endif
                    </td>

                    {{-- Teléfono --}}
                    <td class="px-4 py-4" style="color:#5a5a4f;">{{ $p->telefono ?? '—' }}</td>

                    {{-- Estado --}}
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                              style="{{ $p->estado ? 'background:#f0fdf4;color:#166534;' : 'background:#fef2f2;color:#991b1b;' }}">
                            <span class="w-1.5 h-1.5 rounded-full"
                                  style="background:{{ $p->estado ? '#22c55e' : '#ef4444' }};"></span>
                            {{ $p->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>

                    {{-- Acciones --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">

                            {{-- Editar --}}
                            <button onclick='abrirModalEditar(@json($p))'
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#f0fdf4; color:#39a900;" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                </svg>
                            </button>

                            {{-- Toggle estado --}}
                            <button onclick="abrirModalToggle({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->estado ? 'true' : 'false' }})"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="{{ $p->estado ? 'background:#fef2f2;color:#ef4444;' : 'background:#f0fdf4;color:#22c55e;' }}"
                                    title="{{ $p->estado ? 'Desactivar' : 'Activar' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    @if($p->estado)
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                        <path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"/>
                                        <path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"/>
                                    @else
                                        <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
                                    @endif
                                </svg>
                            </button>

                            {{-- Eliminar --}}
                            <button onclick="abrirModalEliminar({{ $p->id }}, '{{ addslashes($p->nombre) }}')"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#fef2f2; color:#ef4444;" title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center" style="color:#9a9a8a;">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M3 6h18l-2 12H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/>
                        </svg>
                        <p class="text-sm font-semibold">No se encontraron proveedores</p>
                        <p class="text-xs mt-1">Intenta con otros filtros o crea un nuevo proveedor.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($proveedores->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t" style="border-color:#f3f0e8;">
            <p class="text-xs" style="color:#9a9a8a;">
                Mostrando {{ $proveedores->firstItem() }} a {{ $proveedores->lastItem() }} de {{ $proveedores->total() }} proveedores
            </p>
            <div class="flex items-center gap-1">
                @if($proveedores->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </span>
                @else
                    <a href="{{ $proveedores->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </a>
                @endif
                @foreach($proveedores->getUrlRange(max(1,$proveedores->currentPage()-2), min($proveedores->lastPage(),$proveedores->currentPage()+2)) as $page => $url)
                    @if($page == $proveedores->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white" style="background:#71277a;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">{{ $page }}</a>
                    @endif
                @endforeach
                @if($proveedores->hasMorePages())
                    <a href="{{ $proveedores->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </a>
                @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg>
                    </span>
                @endif
            </div>
        </div>
        @else
        <div class="px-6 py-4 border-t text-xs" style="border-color:#f3f0e8; color:#9a9a8a;">
            Mostrando {{ $proveedores->count() }} proveedor{{ $proveedores->count() != 1 ? 'es' : '' }}
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL CREAR / EDITAR
═══════════════════════════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between px-7 py-5 border-b" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 6h18l-2 12H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/>
                    </svg>
                </div>
                <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nuevo Proveedor</h2>
            </div>
            <button onclick="cerrarModal('modalForm')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-100"
                    style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="formProveedor" method="POST" class="px-7 py-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            {{-- Nombre --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                    Nombre <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="nombre" id="inputNombre" placeholder="Ej. Distribuidora Andina" required
                       class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                       style="border-color:#e7e0cc; background:#fafafa;"
                       onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                       onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                @error('nombre')<p class="text-xs mt-1 font-semibold" style="color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            {{-- Tipo --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                    Tipo <span style="color:#ef4444;">*</span>
                </label>
                <div class="flex gap-2">
                    <select name="tipo" id="inputTipo" required
                            class="flex-1 px-4 py-3 rounded-xl border text-sm outline-none transition"
                            style="border-color:#e7e0cc; background:#fafafa;"
                            onchange="if(this.value==='__otro__'){document.getElementById('inputTipoCustom').classList.remove('hidden');this.name=''}else{document.getElementById('inputTipoCustom').classList.add('hidden');this.name='tipo'}">
                        <option value="">Selecciona tipo...</option>
                        <option value="Nacional">Nacional</option>
                        <option value="Internacional">Internacional</option>
                        <option value="Regional">Regional</option>
                        <option value="Local">Local</option>
                        @foreach($tipos as $t)
                            @if(!in_array($t, ['Nacional','Internacional','Regional','Local']))
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endif
                        @endforeach
                        <option value="__otro__">Otro (especificar)...</option>
                    </select>
                    <input type="text" name="tipo" id="inputTipoCustom" placeholder="Especifica el tipo"
                           class="hidden flex-1 px-4 py-3 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>
                @error('tipo')<p class="text-xs mt-1 font-semibold" style="color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            {{-- Correo + Teléfono --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Correo</label>
                    <input type="email" name="correo" id="inputCorreo" placeholder="proveedor@ejemplo.com"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    @error('correo')<p class="text-xs mt-1 font-semibold" style="color:#ef4444;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Teléfono</label>
                    <input type="text" name="telefono" id="inputTelefono" placeholder="+57 300 000 0000"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    @error('telefono')<p class="text-xs mt-1 font-semibold" style="color:#ef4444;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-semibold mb-2" style="color:#1c2b16;">Estado</label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="estado" value="0">
                    <input type="checkbox" name="estado" id="inputEstado" value="1" checked
                           class="w-4 h-4 rounded cursor-pointer" style="accent-color:#39a900;">
                    <span class="text-sm" style="color:#5a5a4f;">Proveedor activo</span>
                </label>
            </div>

            {{-- Botones --}}
            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit" id="btnGuardar"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90"
                        style="background:#39a900;">
                    Crear proveedor
                </button>
                <button type="button" onclick="cerrarModal('modalForm')"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL ELIMINAR
═══════════════════════════════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2;">
                <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar proveedor?</h3>
            <p class="text-sm mb-6" style="color:#5a5a4f;">
                Vas a eliminar a <strong id="eliminarNombre"></strong>. Esta acción no se puede deshacer.
            </p>
            <form id="formEliminar" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90"
                            style="background:#ef4444;">
                        Sí, eliminar
                    </button>
                    <button type="button" onclick="cerrarModal('modalEliminar')"
                            class="flex-1 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50"
                            style="border-color:#e7e0cc; color:#5a5a4f;">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL TOGGLE ESTADO
═══════════════════════════════════════════════════════ --}}
<div id="modalToggle" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div id="toggleIcon" class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"></div>
            <h3 id="toggleTitle" class="text-lg font-extrabold mb-2" style="color:#1c2b16;"></h3>
            <p id="toggleMsg" class="text-sm mb-6" style="color:#5a5a4f;"></p>
            <form id="formToggle" method="POST">
                @csrf @method('PATCH')
                <div class="flex gap-3">
                    <button type="submit" id="toggleBtn"
                            class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90">
                        Confirmar
                    </button>
                    <button type="button" onclick="cerrarModal('modalToggle')"
                            class="flex-1 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50"
                            style="border-color:#e7e0cc; color:#5a5a4f;">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).classList.remove('flex');
    document.body.style.overflow = '';
}
['modalForm','modalEliminar','modalToggle'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(id);
    });
});

function abrirModalCrear() {
    document.getElementById('modalFormTitle').textContent = 'Nuevo Proveedor';
    document.getElementById('btnGuardar').textContent = 'Crear proveedor';
    document.getElementById('formProveedor').action = '{{ route("superadmin.proveedores.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inputNombre').value = '';
    document.getElementById('inputTipo').value = '';
    document.getElementById('inputTipoCustom').value = '';
    document.getElementById('inputTipoCustom').classList.add('hidden');
    document.getElementById('inputCorreo').value = '';
    document.getElementById('inputTelefono').value = '';
    document.getElementById('inputEstado').checked = true;
    abrirModal('modalForm');
}

function abrirModalEditar(p) {
    document.getElementById('modalFormTitle').textContent = 'Editar Proveedor';
    document.getElementById('btnGuardar').textContent = 'Guardar cambios';
    document.getElementById('formProveedor').action = '/superadmin/proveedores/' + p.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputNombre').value = p.nombre || '';
    // Tipo
    const sel = document.getElementById('inputTipo');
    let tipoEncontrado = false;
    for (let opt of sel.options) {
        if (opt.value === p.tipo) { sel.value = p.tipo; tipoEncontrado = true; break; }
    }
    if (!tipoEncontrado && p.tipo) {
        sel.value = '__otro__';
        document.getElementById('inputTipoCustom').value = p.tipo;
        document.getElementById('inputTipoCustom').classList.remove('hidden');
        sel.name = '';
    } else {
        document.getElementById('inputTipoCustom').classList.add('hidden');
        sel.name = 'tipo';
    }
    document.getElementById('inputCorreo').value = p.correo || '';
    document.getElementById('inputTelefono').value = p.telefono || '';
    document.getElementById('inputEstado').checked = p.estado == 1 || p.estado === true;
    abrirModal('modalForm');
}

function abrirModalEliminar(id, nombre) {
    document.getElementById('eliminarNombre').textContent = nombre;
    document.getElementById('formEliminar').action = '/superadmin/proveedores/' + id;
    abrirModal('modalEliminar');
}

function abrirModalToggle(id, nombre, activo) {
    document.getElementById('formToggle').action = '/superadmin/proveedores/' + id + '/toggle';
    if (activo) {
        document.getElementById('toggleIcon').innerHTML = '<svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="1" y1="1" x2="23" y2="23"/><path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"/><path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"/></svg>';
        document.getElementById('toggleIcon').style.background = '#fef2f2';
        document.getElementById('toggleTitle').textContent = '¿Desactivar proveedor?';
        document.getElementById('toggleMsg').innerHTML = 'El proveedor <strong>' + nombre + '</strong> quedará como inactivo.';
        document.getElementById('toggleBtn').style.background = '#ef4444';
        document.getElementById('toggleBtn').textContent = 'Sí, desactivar';
    } else {
        document.getElementById('toggleIcon').innerHTML = '<svg class="w-7 h-7" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>';
        document.getElementById('toggleIcon').style.background = '#f0fdf4';
        document.getElementById('toggleTitle').textContent = '¿Activar proveedor?';
        document.getElementById('toggleMsg').innerHTML = 'El proveedor <strong>' + nombre + '</strong> quedará como activo.';
        document.getElementById('toggleBtn').style.background = '#39a900';
        document.getElementById('toggleBtn').textContent = 'Sí, activar';
    }
    abrirModal('modalToggle');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
@endif
</script>

</x-superadmin-layout>
