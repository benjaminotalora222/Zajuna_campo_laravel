@php $title = 'Usuarios y Roles'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Usuarios y Roles</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Gestiona los usuarios que tienen acceso a la plataforma y sus permisos.</p>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0"
                style="background:#fdc300; color:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
            </svg>
            Nuevo Usuario
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

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('superadmin.usuarios.index') }}"
              class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Buscar usuario</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nombre o correo..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Rol</label>
                <select name="role_id" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ request('role_id') == $rol->id ? 'selected' : '' }}>
                            {{ $rol->nombre }}
                        </option>
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
                    style="background:#39a900;">
                Filtrar
            </button>
            @if(request()->hasAny(['search','role_id','estado']))
            <a href="{{ route('superadmin.usuarios.index') }}"
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
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Nombre</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Correo electrónico</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Rol</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                    <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                @php
                    $rolColors = [
                        1 => ['bg'=>'#f3e8ff','color'=>'#71277a'],
                        2 => ['bg'=>'#f0fdf4','color'=>'#166534'],
                        3 => ['bg'=>'#fffbeb','color'=>'#92400e'],
                    ];
                    $rc = $rolColors[$usuario->role_id] ?? ['bg'=>'#f1f5f9','color'=>'#475569'];
                    $initials = strtoupper(substr(explode(' ',$usuario->name)[0],0,1)) . strtoupper(substr(explode(' ',$usuario->name)[1] ?? 'U',0,1));
                @endphp
                <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-extrabold text-xs text-white shrink-0"
                                 style="background:#71277a;">{{ $initials }}</div>
                            <span class="font-semibold" style="color:#1c2b16;">{{ $usuario->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4" style="color:#5a5a4f;">{{ $usuario->email }}</td>
                    <td class="px-4 py-4">
                        @if($usuario->rol)
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold"
                              style="background:{{ $rc['bg'] }}; color:{{ $rc['color'] }};">
                            {{ $usuario->rol->nombre }}
                        </span>
                        @else
                        <span class="text-xs" style="color:#9a9a8a;">Sin rol</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                              style="{{ $usuario->activo ? 'background:#f0fdf4;color:#166534;' : 'background:#fef2f2;color:#991b1b;' }}">
                            <span class="w-1.5 h-1.5 rounded-full"
                                  style="background:{{ $usuario->activo ? '#22c55e' : '#ef4444' }};"></span>
                            {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">

                            {{-- Editar --}}
                            <button onclick='abrirModalEditar(@json($usuario), @json($usuario->rol))'
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#f0fdf4; color:#39a900;" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                </svg>
                            </button>

                            {{-- Toggle activo --}}
                            <button onclick="abrirModalToggle({{ $usuario->id }}, '{{ addslashes($usuario->name) }}', {{ $usuario->activo ? 'true' : 'false' }})"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="{{ $usuario->activo ? 'background:#fef2f2;color:#ef4444;' : 'background:#f0fdf4;color:#22c55e;' }}"
                                    title="{{ $usuario->activo ? 'Desactivar' : 'Activar' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    @if($usuario->activo)
                                        <line x1="1" y1="1" x2="23" y2="23"/>
                                        <path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"/>
                                        <path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"/>
                                    @else
                                        <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
                                    @endif
                                </svg>
                            </button>

                            {{-- Eliminar --}}
                            @if($usuario->id !== auth()->id())
                            <button onclick="abrirModalEliminar({{ $usuario->id }}, '{{ addslashes($usuario->name) }}')"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#fef2f2; color:#ef4444;" title="Eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center" style="color:#9a9a8a;">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2"/><circle cx="8.5" cy="7" r="3"/>
                        </svg>
                        <p class="text-sm font-semibold">No se encontraron usuarios</p>
                        <p class="text-xs mt-1">Intenta con otros filtros o crea un nuevo usuario.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($usuarios->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t" style="border-color:#f3f0e8;">
            <p class="text-xs" style="color:#9a9a8a;">
                Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuarios
            </p>
            <div class="flex items-center gap-1">
                @if($usuarios->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </span>
                @else
                    <a href="{{ $usuarios->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                    </a>
                @endif
                @foreach($usuarios->getUrlRange(max(1,$usuarios->currentPage()-2), min($usuarios->lastPage(),$usuarios->currentPage()+2)) as $page => $url)
                    @if($page == $usuarios->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white" style="background:#71277a;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">{{ $page }}</a>
                    @endif
                @endforeach
                @if($usuarios->hasMorePages())
                    <a href="{{ $usuarios->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">
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
            Mostrando {{ $usuarios->count() }} usuario{{ $usuarios->count() != 1 ? 's' : '' }}
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     MODAL CREAR / EDITAR
═══════════════════════════════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">

        {{-- Header --}}
        <div class="flex items-center justify-between px-7 py-5 border-b" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 17v-2a4 4 0 0 1 4-4h3a4 4 0 0 1 4 4v2"/><circle cx="8.5" cy="7" r="3"/>
                    </svg>
                </div>
                <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nuevo Usuario</h2>
            </div>
            <button onclick="cerrarModal('modalForm')"
                    class="w-8 h-8 rounded-lg flex items-center justify-center transition hover:bg-gray-100"
                    style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Body --}}
        <form id="formUsuario" method="POST" class="px-7 py-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            {{-- Nombre --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                    Nombre completo <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="name" id="inputName" placeholder="Ej. María Rodríguez" required
                       class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                       style="border-color:#e7e0cc; background:#fafafa;"
                       onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                       onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                @error('name')<p class="text-xs mt-1 font-semibold" style="color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                    Correo electrónico <span style="color:#ef4444;">*</span>
                </label>
                <input type="email" name="email" id="inputEmail" placeholder="correo@ejemplo.com" required
                       class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                       style="border-color:#e7e0cc; background:#fafafa;"
                       onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                       onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                @error('email')<p class="text-xs mt-1 font-semibold" style="color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            {{-- Contraseñas --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                        Contraseña <span id="passRequired" style="color:#ef4444;">*</span>
                    </label>
                    <p id="passHint" class="text-xs mb-1.5 hidden" style="color:#9a9a8a;">Deja en blanco para no cambiar.</p>
                    <input type="password" name="password" id="inputPassword" placeholder="Mínimo 8 caracteres"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Confirmar</label>
                    <p id="passHint2" class="text-xs mb-1.5 hidden" style="color:#9a9a8a;">-</p>
                    <input type="password" name="password_confirmation" id="inputPasswordConfirm" placeholder="Repite la contraseña"
                           class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                </div>
            </div>

            {{-- Rol --}}
            <div>
                <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">
                    Rol <span style="color:#ef4444;">*</span>
                </label>
                <select name="role_id" id="inputRole" required
                        class="w-full px-4 py-3 rounded-xl border text-sm outline-none transition"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Selecciona un rol...</option>
                    @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                    @endforeach
                </select>
                @error('role_id')<p class="text-xs mt-1 font-semibold" style="color:#ef4444;">{{ $message }}</p>@enderror
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-semibold mb-2" style="color:#1c2b16;">Estado</label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="activo" value="0">
                    <input type="checkbox" name="activo" id="inputActivo" value="1" checked
                           class="w-4 h-4 rounded cursor-pointer" style="accent-color:#39a900;">
                    <span class="text-sm" style="color:#5a5a4f;">Usuario activo</span>
                </label>
            </div>

            {{-- Footer botones --}}
            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90"
                        style="background:#39a900;" id="btnGuardar">
                    Crear usuario
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

{{-- ══════════════════════════════════════════════════════════
     MODAL ELIMINAR
═══════════════════════════════════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">

        <div class="px-7 py-7 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2;">
                <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar usuario?</h3>
            <p class="text-sm mb-6" style="color:#5a5a4f;">
                Estás a punto de eliminar a <strong id="eliminarNombre"></strong>. Esta acción no se puede deshacer.
            </p>

            <form id="formEliminar" method="POST">
                @csrf
                @method('DELETE')
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

{{-- ══════════════════════════════════════════════════════════
     MODAL TOGGLE ACTIVO
═══════════════════════════════════════════════════════════ --}}
<div id="modalToggle" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div id="toggleIcon" class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"></div>
            <h3 id="toggleTitle" class="text-lg font-extrabold mb-2" style="color:#1c2b16;"></h3>
            <p id="toggleMsg" class="text-sm mb-6" style="color:#5a5a4f;"></p>
            <form id="formToggle" method="POST">
                @csrf
                @method('PATCH')
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
// ── Helpers ──────────────────────────────────────────────────
function abrirModal(id) {
    const m = document.getElementById(id);
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModal(id) {
    const m = document.getElementById(id);
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}
// Cerrar al hacer clic en el fondo
['modalForm','modalEliminar','modalToggle'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) cerrarModal(id);
    });
});

// ── Modal Crear ───────────────────────────────────────────────
function abrirModalCrear() {
    document.getElementById('modalFormTitle').textContent = 'Nuevo Usuario';
    document.getElementById('btnGuardar').textContent = 'Crear usuario';
    document.getElementById('formUsuario').action = '{{ route("superadmin.usuarios.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inputName').value = '';
    document.getElementById('inputEmail').value = '';
    document.getElementById('inputPassword').value = '';
    document.getElementById('inputPasswordConfirm').value = '';
    document.getElementById('inputRole').value = '';
    document.getElementById('inputActivo').checked = true;
    document.getElementById('inputPassword').required = true;
    document.getElementById('passRequired').classList.remove('hidden');
    document.getElementById('passHint').classList.add('hidden');
    abrirModal('modalForm');
}

// ── Modal Editar ──────────────────────────────────────────────
function abrirModalEditar(usuario, rol) {
    document.getElementById('modalFormTitle').textContent = 'Editar Usuario';
    document.getElementById('btnGuardar').textContent = 'Guardar cambios';
    document.getElementById('formUsuario').action = '/superadmin/usuarios/' + usuario.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputName').value = usuario.name;
    document.getElementById('inputEmail').value = usuario.email;
    document.getElementById('inputPassword').value = '';
    document.getElementById('inputPasswordConfirm').value = '';
    document.getElementById('inputRole').value = usuario.role_id;
    document.getElementById('inputActivo').checked = usuario.activo == 1 || usuario.activo === true;
    document.getElementById('inputPassword').required = false;
    document.getElementById('passRequired').classList.add('hidden');
    document.getElementById('passHint').classList.remove('hidden');
    abrirModal('modalForm');
}

// ── Modal Eliminar ────────────────────────────────────────────
function abrirModalEliminar(id, nombre) {
    document.getElementById('eliminarNombre').textContent = nombre;
    document.getElementById('formEliminar').action = '/superadmin/usuarios/' + id;
    abrirModal('modalEliminar');
}

// ── Modal Toggle ──────────────────────────────────────────────
function abrirModalToggle(id, nombre, activo) {
    document.getElementById('formToggle').action = '/superadmin/usuarios/' + id + '/toggle';
    if (activo) {
        document.getElementById('toggleIcon').innerHTML = '<svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="1" y1="1" x2="23" y2="23"/><path d="M9 9v3a3 3 0 0 0 5.12 2.12M15 9.34V4a3 3 0 0 0-5.94-.6"/><path d="M17 16.95A7 7 0 0 1 5 12v-2m14 0v2a7 7 0 0 1-.11 1.23"/></svg>';
        document.getElementById('toggleIcon').style.background = '#fef2f2';
        document.getElementById('toggleTitle').textContent = '¿Desactivar usuario?';
        document.getElementById('toggleMsg').innerHTML = 'El usuario <strong>' + nombre + '</strong> no podrá iniciar sesión mientras esté inactivo.';
        document.getElementById('toggleBtn').style.background = '#ef4444';
        document.getElementById('toggleBtn').textContent = 'Sí, desactivar';
    } else {
        document.getElementById('toggleIcon').innerHTML = '<svg class="w-7 h-7" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>';
        document.getElementById('toggleIcon').style.background = '#f0fdf4';
        document.getElementById('toggleTitle').textContent = '¿Activar usuario?';
        document.getElementById('toggleMsg').innerHTML = 'El usuario <strong>' + nombre + '</strong> podrá iniciar sesión nuevamente.';
        document.getElementById('toggleBtn').style.background = '#39a900';
        document.getElementById('toggleBtn').textContent = 'Sí, activar';
    }
    abrirModal('modalToggle');
}

// Abrir modal si hay errores de validación
@if($errors->any())
    @if(old('_method') === 'PUT')
        // editar — reabrir con datos anteriores
        document.addEventListener('DOMContentLoaded', () => {
            abrirModalEditar({
                id: {{ old('_usuario_id', 0) }},
                name: '{{ addslashes(old("name","")) }}',
                email: '{{ addslashes(old("email","")) }}',
                role_id: {{ old('role_id', 0) }},
                activo: {{ old('activo', 1) }},
            }, null);
        });
    @else
        document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
    @endif
@endif
</script>

</x-superadmin-layout>
