@php $title = 'Proveedores'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Proveedores</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Gestiona los proveedores registrados en la plataforma y sus cuentas de acceso.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('superadmin.ventas.dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm border transition hover:bg-gray-50 bg-white shadow-sm"
               style="border-color:#e7e0cc; color:#71277a;">
                <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                    <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                    <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                    <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                </svg>
                <span>Dashboard de Ventas</span>
            </a>
            <button onclick="abrirModalCrear()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0 shadow-sm"
                    style="background:#fdc300; color:#71277a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
                </svg>
                Nuevo Proveedor
            </button>
        </div>
    </div>

    {{-- ALERTAS --}}
    @if(session('success'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold shadow-sm"
         style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 rounded-xl mb-6 text-sm font-semibold shadow-sm"
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
            $total       = \App\Models\Proveedor::count();
            $activos     = \App\Models\Proveedor::where('estado', true)->count();
            $inactivos   = \App\Models\Proveedor::where('estado', false)->count();
            $tipos_count = \App\Models\Proveedor::whereNotNull('tipo')->distinct()->count('tipo');
        @endphp
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 flex flex-col gap-1">
            <p class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Total</p>
            <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $total }}</p>
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
                           placeholder="Nombre, código, correo o teléfono..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Tipo</label>
                <select name="tipo" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none transition"
                        style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t }}" {{ request('tipo') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Estado</label>
                <select name="estado" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none transition"
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
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:2px solid #f3f0e8; background:#faf9f5;">
                        <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Proveedor</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Código</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Tipo</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Cuenta / Correo</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Teléfono</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                        <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $p)
                    @php
                        $tipoColors = [
                            'Local'          => ['bg'=>'#fffbeb','color'=>'#92400e', 'border'=>'#fde68a'],
                            'Regional'       => ['bg'=>'#f3e8ff','color'=>'#71277a', 'border'=>'#e9d5ff'],
                            'Nacional'       => ['bg'=>'#f0fdf4','color'=>'#166534', 'border'=>'#bbf7d0'],
                            'Internacional'  => ['bg'=>'#eff6ff','color'=>'#1d4ed8', 'border'=>'#bfdbfe'],
                            'Proveedor'      => ['bg'=>'#e0f2fe','color'=>'#0369a1', 'border'=>'#bae6fd'],
                            'externo'        => ['bg'=>'#f1f5f9','color'=>'#475569', 'border'=>'#e2e8f0'],
                        ];
                        $tc = $tipoColors[$p->tipo] ?? ['bg'=>'#f1f5f9','color'=>'#475569', 'border'=>'#e2e8f0'];
                        $initials = strtoupper(substr($p->nombre ?? 'P', 0, 2));
                    @endphp
                    <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">

                        {{-- Nombre + avatar --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-extrabold text-xs text-white shrink-0 shadow-sm"
                                     style="background:#39a900;">{{ $initials }}</div>
                                <div>
                                    <span class="font-bold block" style="color:#1c2b16;">{{ $p->nombre ?? '—' }}</span>
                                    @if($p->user && $p->user->name && $p->user->name !== $p->nombre)
                                        <span class="text-xs block" style="color:#9a9a8a;">Contacto: {{ $p->user->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Código --}}
                        <td class="px-4 py-4">
                            @if($p->codigo)
                            <span class="inline-block font-mono text-xs font-bold px-2.5 py-1 rounded-lg border"
                                  style="background:#f3e8ff; color:#71277a; border-color:#e9d5ff;">
                                {{ $p->codigo }}
                            </span>
                            @else <span style="color:#9a9a8a;">—</span> @endif
                        </td>

                        {{-- Tipo --}}
                        <td class="px-4 py-4">
                            @if($p->tipo)
                            <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold border"
                                  style="background:{{ $tc['bg'] }}; color:{{ $tc['color'] }}; border-color:{{ $tc['border'] }};">
                                {{ $p->tipo }}
                            </span>
                            @else <span style="color:#9a9a8a;">—</span> @endif
                        </td>

                        {{-- Correo / Cuenta --}}
                        <td class="px-4 py-4" style="color:#5a5a4f;">
                            @if($p->correo)
                                <a href="mailto:{{ $p->correo }}" class="hover:underline font-medium" style="color:#39a900;">{{ $p->correo }}</a>
                                @if($p->user)
                                    <span class="block text-[11px] text-gray-400">Usuario vinculado</span>
                                @else
                                    <span class="block text-[11px] text-amber-600 font-semibold">Sin usuario asignado</span>
                                @endif
                            @else <span style="color:#9a9a8a;">—</span> @endif
                        </td>

                        {{-- Teléfono --}}
                        <td class="px-4 py-4 font-mono text-xs" style="color:#5a5a4f;">{{ $p->telefono ?? '—' }}</td>

                        {{-- Estado --}}
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border"
                                  style="{{ $p->estado ? 'background:#f0fdf4;color:#166534;border-color:#bbf7d0;' : 'background:#fef2f2;color:#991b1b;border-color:#fecaca;' }}">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      style="background:{{ $p->estado ? '#22c55e' : '#ef4444' }};"></span>
                                {{ $p->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">

                                {{-- Dashboard de ventas --}}
                                <a href="{{ route('superadmin.ventas.dashboard', ['proveedor_id' => $p->id]) }}"
                                   class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105 border"
                                   style="background:#faf5ff; color:#71277a; border-color:#e9d5ff;" title="Ver Dashboard de Ventas">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="7" height="9" rx="1.5"/>
                                        <rect x="14" y="3" width="7" height="5" rx="1.5"/>
                                        <rect x="14" y="12" width="7" height="9" rx="1.5"/>
                                        <rect x="3" y="16" width="7" height="5" rx="1.5"/>
                                    </svg>
                                </a>

                                {{-- Editar --}}
                                <button onclick='abrirModalEditar(@json($p))'
                                        class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105 border"
                                        style="background:#f0fdf4; color:#39a900; border-color:#bbf7d0;" title="Editar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                    </svg>
                                </button>

                                {{-- Toggle estado --}}
                                <button onclick="abrirModalToggle({{ $p->id }}, '{{ addslashes($p->nombre) }}', {{ $p->estado ? 'true' : 'false' }})"
                                        class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105 border"
                                        style="{{ $p->estado ? 'background:#fef2f2;color:#ef4444;border-color:#fecaca;' : 'background:#f0fdf4;color:#22c55e;border-color:#bbf7d0;' }}"
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
                                        class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105 border"
                                        style="background:#fef2f2; color:#ef4444; border-color:#fecaca;" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center" style="color:#9a9a8a;">
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
        </div>

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
     MODAL CREAR / EDITAR PROVEEDOR
═══════════════════════════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-sm" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[92vh] flex flex-col overflow-hidden border border-gray-100" onclick="event.stopPropagation()">

        {{-- Cabecera modal --}}
        <div class="flex items-center justify-between px-7 py-5 border-b shrink-0" style="border-color:#f3f0e8; background:#fffcf6;">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center shadow-sm" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 6h18l-2 12H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/>
                    </svg>
                </div>
                <div>
                    <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nuevo Proveedor</h2>
                    <p id="modalFormSubtitle" class="text-xs text-gray-500">Ingresa los datos del proveedor y configura su cuenta de acceso.</p>
                </div>
            </div>
            <button onclick="cerrarModal('modalForm')"
                    class="w-8 h-8 rounded-xl flex items-center justify-center transition hover:bg-gray-100"
                    style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Formulario con scroll --}}
        <form id="formProveedor" method="POST" action="{{ route('superadmin.proveedores.store') }}" class="px-7 py-5 space-y-4 overflow-y-auto flex-1">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="{{ old('_method', 'POST') }}">
            <input type="hidden" name="form_mode" id="formMode" value="{{ old('form_mode', 'create') }}">
            <input type="hidden" name="form_action_url" id="formActionUrl" value="{{ old('form_action_url', route('superadmin.proveedores.store')) }}">
            <input type="hidden" name="proveedor_id" id="formProveedorId" value="{{ old('proveedor_id') }}">

            {{-- Alerta de errores de validación --}}
            @if($errors->any())
            <div class="p-3.5 rounded-2xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700">
                <div class="flex items-center gap-2 mb-1.5 font-bold">
                    <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/>
                    </svg>
                    <span>Por favor corrige los siguientes inconvenientes:</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5 text-red-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Nombre del proveedor --}}
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:#1c2b16;">
                    Nombre del proveedor o razón social <span style="color:#ef4444;">*</span>
                </label>
                <input type="text" name="nombre" id="inputNombre" value="{{ old('nombre') }}" placeholder="Ej. Distribuidora Andina S.A.S. o Camilo Millán" required
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition"
                       style="border-color:#e7e0cc; background:#fafafa;"
                       onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                       onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                @error('nombre')<p class="text-xs mt-1 font-semibold text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Fila 2 columnas: Código y Tipo --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:#1c2b16;">
                        Código único
                        <span class="text-xs font-normal text-gray-400">(autogenerado si vacío)</span>
                    </label>
                    <input type="text" name="codigo" id="inputCodigo" value="{{ old('codigo') }}" placeholder="Ej. PRV-001"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm font-mono uppercase outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    @error('codigo')<p class="text-xs mt-1 font-semibold text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:#1c2b16;">
                        Tipo de proveedor <span style="color:#ef4444;">*</span>
                    </label>
                    <select name="tipo" id="inputTipo" required
                            class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition"
                            style="border-color:#e7e0cc; background:#fafafa;"
                            onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                            onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                        <option value="Local" {{ old('tipo', 'Local') === 'Local' ? 'selected' : '' }}>Local</option>
                        <option value="Regional" {{ old('tipo') === 'Regional' ? 'selected' : '' }}>Regional</option>
                        <option value="Nacional" {{ old('tipo') === 'Nacional' ? 'selected' : '' }}>Nacional</option>
                        <option value="Internacional" {{ old('tipo') === 'Internacional' ? 'selected' : '' }}>Internacional</option>
                    </select>
                    @error('tipo')<p class="text-xs mt-1 font-semibold text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Fila 2 columnas: Teléfono y Nombre de contacto --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:#1c2b16;">Teléfono de contacto</label>
                    <input type="text" name="telefono" id="inputTelefono" value="{{ old('telefono') }}" placeholder="Ej. 300 000 0000" maxlength="25"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    @error('telefono')<p class="text-xs mt-1 font-semibold text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:#1c2b16;">
                        Persona de contacto
                        <span class="text-xs font-normal text-gray-400">(opcional)</span>
                    </label>
                    <input type="text" name="user_name" id="inputUserName" value="{{ old('user_name') }}" placeholder="Ej. Carlos Pérez" maxlength="255"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition"
                           style="border-color:#e7e0cc; background:#fafafa;"
                           onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                           onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                    <p class="text-[11px] text-gray-400 mt-0.5">Si se deja vacío, tomará el nombre del proveedor.</p>
                    @error('user_name')<p class="text-xs mt-1 font-semibold text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- SEPARADOR CUENTA DE ACCESO --}}
            <div class="pt-2">
                <div class="flex items-center gap-2 pb-2 border-b" style="border-color:#f3f0e8;">
                    <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="text-xs font-bold uppercase tracking-wider" style="color:#71277a;">Cuenta de Acceso al Portal</span>
                </div>
                <p class="text-[11px] text-gray-500 mt-1">Credenciales con las que el proveedor iniciará sesión en la plataforma.</p>
            </div>

            {{-- Correo electrónico --}}
            <div>
                <label class="block text-sm font-semibold mb-1" style="color:#1c2b16;">
                    Correo electrónico de acceso <span style="color:#ef4444;">*</span>
                </label>
                <input type="email" name="correo" id="inputCorreo" value="{{ old('correo') }}" placeholder="proveedor@ejemplo.com" required
                       class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition"
                       style="border-color:#e7e0cc; background:#fafafa;"
                       onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                       onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                @error('correo')<p class="text-xs mt-1 font-semibold text-red-500">{{ $message }}</p>@enderror
            </div>

            {{-- Fila 2 columnas: Contraseñas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:#1c2b16;">
                        Contraseña <span id="passReq" style="color:#ef4444;">*</span>
                    </label>
                    <p id="passHintProveedor" class="text-[11px] text-gray-400 mb-1 hidden">Deja en blanco para no cambiar.</p>
                    <div class="relative">
                        <input type="password" name="password" id="inputPassword" placeholder="Mínimo 8 caracteres"
                               class="w-full px-4 py-2.5 pr-10 rounded-xl border text-sm outline-none transition"
                               style="border-color:#e7e0cc; background:#fafafa;"
                               onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                               onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                        <button type="button" onclick="togglePass('inputPassword')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="text-xs mt-1 font-semibold text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1" style="color:#1c2b16;">Confirmar contraseña</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="inputPasswordConfirm" placeholder="Repite la contraseña"
                               class="w-full px-4 py-2.5 pr-10 rounded-xl border text-sm outline-none transition"
                               style="border-color:#e7e0cc; background:#fafafa;"
                               onfocus="this.style.borderColor='#39a900';this.style.boxShadow='0 0 0 3px rgba(57,169,0,0.1)'"
                               onblur="this.style.borderColor='#e7e0cc';this.style.boxShadow='none'">
                        <button type="button" onclick="togglePass('inputPasswordConfirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Estado --}}
            <div class="pt-1">
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <input type="hidden" name="estado" value="0">
                    <input type="checkbox" name="estado" id="inputEstado" value="1" {{ old('estado', '1') == '1' ? 'checked' : '' }}
                           class="w-4 h-4 rounded cursor-pointer" style="accent-color:#39a900;">
                    <span class="text-sm font-semibold" style="color:#1c2b16;">Proveedor activo y habilitado para operar</span>
                </label>
            </div>

            {{-- Botones de acción modal --}}
            <div class="flex items-center gap-3 pt-3 border-t shrink-0" style="border-color:#f3f0e8;">
                <button type="submit" id="btnGuardar"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90 shadow-sm flex items-center justify-center gap-2"
                        style="background:#39a900;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg>
                    <span>Crear proveedor</span>
                </button>
                <button type="button" onclick="cerrarModal('modalForm')"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50 text-gray-600"
                        style="border-color:#e7e0cc;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL ELIMINAR PROVEEDOR
═══════════════════════════════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-sm" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md border border-gray-100 overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2; border:1px solid #fecaca;">
                <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar proveedor?</h3>
            <p class="text-sm mb-6" style="color:#5a5a4f;">
                Vas a eliminar a <strong id="eliminarNombre"></strong> y su cuenta de acceso asociada. Esta acción no se puede deshacer.
            </p>
            <form id="formEliminar" method="POST">
                @csrf @method('DELETE')
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90 shadow-sm"
                            style="background:#ef4444;">
                        Sí, eliminar
                    </button>
                    <button type="button" onclick="cerrarModal('modalEliminar')"
                            class="flex-1 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50 text-gray-600"
                            style="border-color:#e7e0cc;">
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
<div id="modalToggle" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-sm" style="background:rgba(0,0,0,0.45);">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md border border-gray-100 overflow-hidden" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div id="toggleIcon" class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4"></div>
            <h3 id="toggleTitle" class="text-lg font-extrabold mb-2" style="color:#1c2b16;"></h3>
            <p id="toggleMsg" class="text-sm mb-6" style="color:#5a5a4f;"></p>
            <form id="formToggle" method="POST">
                @csrf @method('PATCH')
                <div class="flex gap-3">
                    <button type="submit" id="toggleBtn"
                            class="flex-1 py-3 rounded-xl font-bold text-sm text-white transition hover:opacity-90 shadow-sm">
                        Confirmar
                    </button>
                    <button type="button" onclick="cerrarModal('modalToggle')"
                            class="flex-1 py-3 rounded-xl font-bold text-sm border transition hover:bg-gray-50 text-gray-600"
                            style="border-color:#e7e0cc;">
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
    if (!m) return;
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function cerrarModal(id) {
    const m = document.getElementById(id);
    if (!m) return;
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}

function togglePass(inputId) {
    const inp = document.getElementById(inputId);
    if (inp) {
        inp.type = inp.type === 'password' ? 'text' : 'password';
    }
}

['modalForm','modalEliminar','modalToggle'].forEach(id => {
    const el = document.getElementById(id);
    if (el) {
        el.addEventListener('click', function(e) {
            if (e.target === this) cerrarModal(id);
        });
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        ['modalForm','modalEliminar','modalToggle'].forEach(id => cerrarModal(id));
    }
});

function abrirModalCrear() {
    document.getElementById('modalFormTitle').textContent = 'Nuevo Proveedor';
    document.getElementById('modalFormSubtitle').textContent = 'Ingresa los datos del proveedor y configura su cuenta de acceso.';
    document.getElementById('btnGuardar').innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg><span>Crear proveedor</span>';
    
    const storeUrl = '{{ route("superadmin.proveedores.store") }}';
    document.getElementById('formProveedor').action = storeUrl;
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('formMode').value = 'create';
    document.getElementById('formActionUrl').value = storeUrl;
    document.getElementById('formProveedorId').value = '';

    // Limpiar campos para nuevo registro
    document.getElementById('inputNombre').value = '';
    document.getElementById('inputCodigo').value = '';
    document.getElementById('inputTipo').value = 'Local';
    document.getElementById('inputCorreo').value = '';
    document.getElementById('inputTelefono').value = '';
    document.getElementById('inputUserName').value = '';
    document.getElementById('inputPassword').value = '';
    document.getElementById('inputPasswordConfirm').value = '';
    document.getElementById('inputEstado').checked = true;

    // Reglas de contraseña
    document.getElementById('passReq').classList.remove('hidden');
    document.getElementById('inputPassword').required = true;
    document.getElementById('passHintProveedor').classList.add('hidden');

    abrirModal('modalForm');
}

function abrirModalEditar(p) {
    document.getElementById('modalFormTitle').textContent = 'Editar Proveedor';
    document.getElementById('modalFormSubtitle').textContent = 'Modifica los datos del proveedor y credenciales asociadas.';
    document.getElementById('btnGuardar').innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg><span>Guardar cambios</span>';

    const updateUrl = '/superadmin/proveedores/' + p.id;
    document.getElementById('formProveedor').action = updateUrl;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('formMode').value = 'edit';
    document.getElementById('formActionUrl').value = updateUrl;
    document.getElementById('formProveedorId').value = p.id;

    // Poblar datos
    document.getElementById('inputNombre').value = p.nombre || '';
    document.getElementById('inputCodigo').value = p.codigo || '';
    document.getElementById('inputTipo').value = p.tipo || 'Local';
    document.getElementById('inputCorreo').value = p.correo || '';
    document.getElementById('inputTelefono').value = p.telefono || '';
    document.getElementById('inputUserName').value = (p.user && p.user.name) ? p.user.name : (p.nombre || '');
    document.getElementById('inputPassword').value = '';
    document.getElementById('inputPasswordConfirm').value = '';
    document.getElementById('inputEstado').checked = (p.estado == 1 || p.estado === true);

    // Contraseña opcional en modo edición
    document.getElementById('passReq').classList.add('hidden');
    document.getElementById('inputPassword').required = false;
    document.getElementById('passHintProveedor').classList.remove('hidden');

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
        document.getElementById('toggleIcon').style.border = '1px solid #fecaca';
        document.getElementById('toggleTitle').textContent = '¿Desactivar proveedor?';
        document.getElementById('toggleMsg').innerHTML = 'El proveedor <strong>' + nombre + '</strong> quedará inactivo y su cuenta de acceso será suspendida temporalmente.';
        document.getElementById('toggleBtn').style.background = '#ef4444';
        document.getElementById('toggleBtn').textContent = 'Sí, desactivar';
    } else {
        document.getElementById('toggleIcon').innerHTML = '<svg class="w-7 h-7" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>';
        document.getElementById('toggleIcon').style.background = '#f0fdf4';
        document.getElementById('toggleIcon').style.border = '1px solid #bbf7d0';
        document.getElementById('toggleTitle').textContent = '¿Activar proveedor?';
        document.getElementById('toggleMsg').innerHTML = 'El proveedor <strong>' + nombre + '</strong> quedará activo y su cuenta podrá iniciar sesión normalmente.';
        document.getElementById('toggleBtn').style.background = '#39a900';
        document.getElementById('toggleBtn').textContent = 'Sí, activar';
    }
    abrirModal('modalToggle');
}

{{-- En caso de errores de validación, reabrir manteniendo los datos y el modo correspondiente --}}
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    const mode = "{{ old('form_mode', 'create') }}";
    if (mode === 'edit') {
        document.getElementById('modalFormTitle').textContent = 'Editar Proveedor';
        document.getElementById('modalFormSubtitle').textContent = 'Modifica los datos del proveedor y credenciales asociadas.';
        document.getElementById('btnGuardar').innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg><span>Guardar cambios</span>';
        document.getElementById('formProveedor').action = "{{ old('form_action_url', '') }}";
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('passReq').classList.add('hidden');
        document.getElementById('inputPassword').required = false;
        document.getElementById('passHintProveedor').classList.remove('hidden');
    } else {
        document.getElementById('modalFormTitle').textContent = 'Nuevo Proveedor';
        document.getElementById('modalFormSubtitle').textContent = 'Ingresa los datos del proveedor y configura su cuenta de acceso.';
        document.getElementById('btnGuardar').innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg><span>Crear proveedor</span>';
        document.getElementById('formProveedor').action = "{{ old('form_action_url', route('superadmin.proveedores.store')) }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('passReq').classList.remove('hidden');
        document.getElementById('inputPassword').required = true;
        document.getElementById('passHintProveedor').classList.add('hidden');
    }
    abrirModal('modalForm');
});
@endif
</script>

</x-superadmin-layout>
