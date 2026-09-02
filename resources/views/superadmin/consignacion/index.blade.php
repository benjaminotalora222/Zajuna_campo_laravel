@php $title = 'Inventario y Consignación'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Inventario en Consignación</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Consulta el stock disponible en consignación con tus proveedores.</p>
        </div>
        <button onclick="abrirModalCrear()"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm transition hover:opacity-90 shrink-0"
                style="background:#fdc300; color:#71277a;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/>
            </svg>
            Nuevo Ingreso
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                <svg class="w-6 h-6" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#39a900;">Total Productos</p>
                <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $stats['total_productos'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">SKU registrados</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#faf5ff;">
                <svg class="w-6 h-6" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#71277a;">Stock Disponible</p>
                <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ number_format($stats['stock_total']) }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Unidades disponibles</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;">
                <svg class="w-6 h-6" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#d97706;">Próximos a Vencer</p>
                <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $stats['proximo_vencer'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Requieren atención</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                <svg class="w-6 h-6" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#39a900;">Proveedores</p>
                <p class="text-2xl font-extrabold" style="color:#1c2b16;">{{ $stats['proveedores'] }}</p>
                <p class="text-xs" style="color:#9a9a8a;">Activos</p>
            </div>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('superadmin.consignacion.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Buscar producto o proveedor</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, SKU o proveedor..."
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4" style="color:#9a9a8a;"
                         fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>
            <div class="min-w-[160px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Proveedor</label>
                <select name="proveedor_id" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    @foreach($proveedores as $p)
                        <option value="{{ $p->id }}" {{ request('proveedor_id') == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Estado</label>
                <select name="estado" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    <option value="disponible"     {{ request('estado') === 'disponible'     ? 'selected' : '' }}>Disponible</option>
                    <option value="proximo_vencer" {{ request('estado') === 'proximo_vencer' ? 'selected' : '' }}>Próximo a vencer</option>
                    <option value="agotado"        {{ request('estado') === 'agotado'        ? 'selected' : '' }}>Agotado</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90" style="background:#39a900;">Filtrar</button>
            @if(request()->hasAny(['search','proveedor_id','estado']))
                <a href="{{ route('superadmin.consignacion.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr style="border-bottom:2px solid #f3f0e8;">
                    <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Producto</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Proveedor</th>
                    <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Stock Disponible</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Fecha de Vencimiento</th>
                    <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                    <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consignaciones as $c)
                <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-sm" style="color:#1c2b16;">{{ $c->nombre_producto }}</p>
                        @if($c->sku)
                            <p class="text-xs mt-0.5" style="color:#9a9a8a;">SKU: {{ $c->sku }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0" style="background:#faf5ff;">
                                <svg class="w-3.5 h-3.5" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <span class="text-sm" style="color:#5a5a4f;">{{ $c->proveedor->nombre ?? '—' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-right">
                        <span class="font-extrabold text-sm" style="color:#1c2b16;">{{ number_format($c->stock_disponible) }}</span>
                        @if($c->unidad)
                            <span class="text-xs ml-1" style="color:#9a9a8a;">{{ $c->unidad }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @if($c->fecha_vencimiento)
                            <p class="text-sm font-semibold" style="color:#1c2b16;">{{ $c->fecha_vencimiento->locale('es')->isoFormat('D MMM YYYY') }}</p>
                            <p class="text-xs" style="color:#9a9a8a;">{{ $c->fecha_vencimiento->locale('es')->diffForHumans() }}</p>
                        @else
                            <span style="color:#9a9a8a;">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-4">
                        @php
                            $estadoConfig = match($c->estado) {
                                'proximo_vencer' => ['bg' => '#fffbeb', 'color' => '#d97706', 'label' => 'Próximo a vencer'],
                                'agotado'        => ['bg' => '#fef2f2', 'color' => '#ef4444', 'label' => 'Agotado'],
                                default          => ['bg' => '#f0fdf4', 'color' => '#166534', 'label' => 'Disponible'],
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                              style="background:{{ $estadoConfig['bg'] }}; color:{{ $estadoConfig['color'] }};">
                            <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $estadoConfig['color'] }};"></span>
                            {{ $estadoConfig['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick='abrirModalEditar(@json($c))'
                                    class="w-9 h-9 rounded-xl flex items-center justify-center transition hover:scale-105"
                                    style="background:#f0fdf4; color:#39a900;" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                </svg>
                            </button>
                            <button onclick="abrirModalEliminar({{ $c->id }})"
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
                            <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                        </svg>
                        <p class="text-sm font-semibold">No hay registros de consignación</p>
                        <p class="text-xs mt-1">Registra el primer ingreso con el botón "Nuevo Ingreso".</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($consignaciones->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t" style="border-color:#f3f0e8;">
            <p class="text-xs" style="color:#9a9a8a;">Mostrando {{ $consignaciones->firstItem() }} a {{ $consignaciones->lastItem() }} de {{ $consignaciones->total() }} productos</p>
            <div class="flex items-center gap-1">
                @if($consignaciones->onFirstPage())
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></span>
                @else
                    <a href="{{ $consignaciones->previousPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></a>
                @endif
                @foreach($consignaciones->getUrlRange(max(1,$consignaciones->currentPage()-2), min($consignaciones->lastPage(),$consignaciones->currentPage()+2)) as $page => $url)
                    @if($page == $consignaciones->currentPage())
                        <span class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold text-white" style="background:#71277a;">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;">{{ $page }}</a>
                    @endif
                @endforeach
                @if($consignaciones->hasMorePages())
                    <a href="{{ $consignaciones->nextPageUrl() }}" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-50" style="border:1px solid #e7e0cc; color:#5a5a4f;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></a>
                @else
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center opacity-30 cursor-not-allowed" style="border:1px solid #e7e0cc;"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6"/></svg></span>
                @endif
            </div>
        </div>
        @else
        <div class="px-6 py-4 border-t text-xs" style="border-color:#f3f0e8; color:#9a9a8a;">Mostrando {{ $consignaciones->count() }} producto{{ $consignaciones->count() != 1 ? 's' : '' }}</div>
        @endif
    </div>
</div>

{{-- ══ MODAL CREAR / EDITAR ══════════════════════════════════ --}}
<div id="modalForm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[95vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-7 py-5 border-b sticky top-0 bg-white z-10" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    </svg>
                </div>
                <h2 id="modalFormTitle" class="text-lg font-extrabold" style="color:#1c2b16;">Nuevo Ingreso</h2>
            </div>
            <button onclick="cerrarModal('modalForm')" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="formConsignacion" method="POST" class="px-7 py-6 space-y-4">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            @if($errors->any())
            <div class="px-4 py-3 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Proveedor <span style="color:#ef4444;">*</span></label>
                    <select name="proveedor_id" id="inputProveedor" required
                            class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                            style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Selecciona proveedor...</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Nombre del producto <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="nombre_producto" id="inputNombre" required placeholder="Ej: Fertilizante Orgánico 50kg"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">SKU</label>
                    <input type="text" name="sku" id="inputSku" placeholder="Ej: FERT-ORG-50"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Unidad</label>
                    <input type="text" name="unidad" id="inputUnidad" placeholder="Ej: sacos, unidades, kg"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Stock disponible <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="stock_disponible" id="inputStock" required min="0" value="0"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha de vencimiento</label>
                    <input type="date" name="fecha_vencimiento" id="inputFecha"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Notas</label>
                    <textarea name="notas" id="inputNotas" rows="2" placeholder="Observaciones adicionales..."
                              class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none resize-none"
                              style="border-color:#e7e0cc; background:#fafafa;"></textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit" id="btnGuardar"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90"
                        style="background:#39a900;">Registrar ingreso</button>
                <button type="button" onclick="cerrarModal('modalForm')"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

{{-- ══ MODAL ELIMINAR ══════════════════════════════════════════ --}}
<div id="modalEliminar" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        <div class="px-7 py-7 text-center">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background:#fef2f2;">
                <svg class="w-7 h-7" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                </svg>
            </div>
            <h3 class="text-lg font-extrabold mb-2" style="color:#1c2b16;">¿Eliminar registro?</h3>
            <p class="text-sm mb-6" style="color:#5a5a4f;">Esta acción no se puede deshacer.</p>
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
    document.getElementById('modalFormTitle').textContent = 'Nuevo Ingreso';
    document.getElementById('btnGuardar').textContent = 'Registrar ingreso';
    document.getElementById('formConsignacion').action = '{{ route("superadmin.consignacion.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('inputProveedor').value = '';
    document.getElementById('inputNombre').value = '';
    document.getElementById('inputSku').value = '';
    document.getElementById('inputUnidad').value = '';
    document.getElementById('inputStock').value = '0';
    document.getElementById('inputFecha').value = '';
    document.getElementById('inputNotas').value = '';
    abrirModal('modalForm');
}

function abrirModalEditar(c) {
    document.getElementById('modalFormTitle').textContent = 'Editar Consignación';
    document.getElementById('btnGuardar').textContent = 'Guardar cambios';
    document.getElementById('formConsignacion').action = '/superadmin/consignacion/' + c.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('inputProveedor').value = c.proveedor_id;
    document.getElementById('inputNombre').value = c.nombre_producto;
    document.getElementById('inputSku').value = c.sku || '';
    document.getElementById('inputUnidad').value = c.unidad || '';
    document.getElementById('inputStock').value = c.stock_disponible;
    document.getElementById('inputFecha').value = c.fecha_vencimiento ? c.fecha_vencimiento.substring(0, 10) : '';
    document.getElementById('inputNotas').value = c.notas || '';
    abrirModal('modalForm');
}

function abrirModalEliminar(id) {
    document.getElementById('formEliminar').action = '/superadmin/consignacion/' + id;
    abrirModal('modalEliminar');
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalCrear());
@endif
</script>

</x-superadmin-layout>
