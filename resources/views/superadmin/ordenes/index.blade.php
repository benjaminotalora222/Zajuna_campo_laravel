@php $title = 'Órdenes de Pedido a Proveedores'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ALERTAS DE SESIÓN --}}
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl flex items-center gap-3 border shadow-sm" style="background:#f0fdf4; border-color:#bbf7d0; color:#166534;">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 rounded-xl flex items-center gap-3 border shadow-sm" style="background:#fef2f2; border-color:#fecaca; color:#991b1b;">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span class="text-sm font-semibold">{{ session('error') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="mb-6 p-4 rounded-xl flex items-center gap-3 border shadow-sm" style="background:#fffbeb; border-color:#fde68a; color:#92400e;">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            <span class="text-sm font-semibold">{{ session('info') }}</span>
        </div>
    @endif

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider" style="background:#f3e8ff; color:#71277a;">
                    Minimarket / Abastecimiento
                </span>
            </div>
            <h1 class="text-2xl font-extrabold leading-tight mt-1">Órdenes de Pedido a Proveedores</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">
                Generación y despacho automático por correo electrónico al proveedor cuando el stock llega a su nivel mínimo.
            </p>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            {{-- Botón para escanear y generar órdenes masivas para productos en stock mínimo --}}
            <form id="formRevisarStockMinimo" method="POST" action="{{ route('superadmin.ordenes.revisar-stock-minimo') }}">
                @csrf
                <button type="button"
                        onclick="confirmarRevisarStock()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-amber-900 border transition hover:opacity-95 shadow-sm cursor-pointer"
                        style="background:#fffbeb; border-color:#fde68a;">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                    <span>Revisar y Notificar Stock Mínimo</span>
                </button>
            </form>

            <button type="button" onclick="abrirModalNueva()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-sm text-white shadow-sm transition hover:opacity-95"
                    style="background:#71277a;">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                <span>Nueva Orden de Pedido</span>
            </button>
        </div>
    </div>

    {{-- TARJETAS KPIS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Órdenes --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border flex items-center gap-4" style="border-color:#e7e0cc;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#faf5ff;">
                <svg class="w-6 h-6" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#9a9a8a;">Total Órdenes</p>
                <p class="text-2xl font-black" style="color:#1c2b16;">{{ $stats['total_ordenes'] }}</p>
            </div>
        </div>

        {{-- Enviadas --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border flex items-center gap-4" style="border-color:#e7e0cc;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;">
                <svg class="w-6 h-6" style="color:#166534;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#9a9a8a;">Enviadas por Correo</p>
                <p class="text-2xl font-black" style="color:#166534;">{{ $stats['enviadas'] }}</p>
            </div>
        </div>

        {{-- Recibidas --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border flex items-center gap-4" style="border-color:#e7e0cc;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#eff6ff;">
                <svg class="w-6 h-6" style="color:#2563eb;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#9a9a8a;">Mercancía Recibida</p>
                <p class="text-2xl font-black" style="color:#2563eb;">{{ $stats['recibidas'] }}</p>
            </div>
        </div>

        {{-- Productos en Alerta --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border flex items-center gap-4" style="border-color:#e7e0cc;">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:#fef2f2;">
                <svg class="w-6 h-6" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-wider mb-0.5" style="color:#9a9a8a;">En Stock Crítico</p>
                <p class="text-2xl font-black" style="color:#ef4444;">{{ $stats['productos_alerta'] }}</p>
            </div>
        </div>
    </div>

    {{-- LISTADO DE PRODUCTOS EN STOCK MÍNIMO QUE REQUIEREN ORDEN --}}
    @if($productosStockBajo->count())
    <div class="bg-white rounded-2xl p-5 mb-8 border shadow-sm" style="border-color:#fde68a; background:#fffdf5;">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                <h3 class="text-sm font-extrabold text-amber-900">
                    Productos que han alcanzado su stock mínimo y requieren reabastecimiento:
                </h3>
            </div>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                {{ $productosStockBajo->count() }} producto(s)
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($productosStockBajo as $pb)
            @php
                $stockActualPb = $pb->stock_calculado;
                $sugerido = max(1, ($pb->stockMinimo * 2) - $stockActualPb);
            @endphp
            <div class="p-3 bg-white rounded-xl border flex items-center justify-between gap-3 shadow-2xs" style="border-color:#e7e0cc;">
                <div class="min-w-0">
                    <p class="text-xs font-bold truncate text-gray-900">{{ $pb->nombre }}</p>
                    <p class="text-[11px] text-gray-500 truncate">
                        Prov: {{ $pb->proveedor?->nombre ?? 'Sin proveedor' }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-1">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-800">
                            Stock: {{ $stockActualPb }}/{{ $pb->stockMinimo }} {{ $pb->unidad }}
                        </span>
                        <span class="text-[10px] text-gray-400">Sugerido: +{{ $sugerido }}</span>
                    </div>
                </div>
                <form id="formPedirProd{{ $pb->id }}" method="POST" action="{{ route('superadmin.ordenes.store') }}" class="shrink-0">
                    @csrf
                    <input type="hidden" name="producto_id" value="{{ $pb->id }}">
                    <input type="hidden" name="cantidad_solicitada" value="{{ $sugerido }}">
                    <input type="hidden" name="observaciones" value="Reposición automática generada por alcanzar stock mínimo de seguridad.">
                    <button type="button"
                            onclick="confirmarPedirProducto('formPedirProd{{ $pb->id }}', '{{ addslashes($pb->nombre) }}', {{ $sugerido }}, '{{ addslashes($pb->proveedor?->nombre ?? 'Proveedor') }}', '{{ addslashes($pb->proveedor?->correo ?? '') }}')"
                            title="Generar y enviar orden por correo a {{ $pb->proveedor?->correo ?? 'proveedor' }}"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-white transition hover:opacity-90 flex items-center gap-1 cursor-pointer"
                            style="background:#39a900;">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        Pedir
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- FILTROS Y BÚSQUEDA --}}
    <form method="GET" action="{{ route('superadmin.ordenes.index') }}" class="flex flex-wrap gap-3 mb-6 items-end">
        <div class="flex-1 min-w-[220px]">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por # de orden, proveedor, correo o producto..."
                   class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition focus:border-[#71277a]"
                   style="border-color:#e7e0cc; background:#fafafa;">
        </div>
        <div class="min-w-[180px]">
            <select name="proveedor_id" class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition focus:border-[#71277a]"
                    style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Todos los proveedores</option>
                @foreach($proveedores as $prv)
                    <option value="{{ $prv->id }}" @selected(request('proveedor_id') == $prv->id)>
                        {{ $prv->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[150px]">
            <select name="estado" class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none transition focus:border-[#71277a]"
                    style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Todos los estados</option>
                <option value="enviada"   @selected(request('estado') === 'enviada')>Enviadas</option>
                <option value="recibida"  @selected(request('estado') === 'recibida')>Recibidas</option>
                <option value="cancelada" @selected(request('estado') === 'cancelada')>Canceladas</option>
            </select>
        </div>
        <button type="submit"
                class="px-5 py-2.5 rounded-xl font-bold text-white text-sm shadow-sm transition hover:opacity-90"
                style="background:#39a900;">
            Filtrar
        </button>
        @if(request()->hasAny(['search','proveedor_id','estado']))
            <a href="{{ route('superadmin.ordenes.index') }}"
               class="px-4 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50 transition"
               style="border-color:#e7e0cc; color:#5a5a4f;">
                Limpiar
            </a>
        @endif
    </form>

    {{-- TABLA DE ÓRDENES --}}
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden" style="border-color:#e7e0cc;">
        @if($ordenes->count())
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b text-xs font-bold uppercase tracking-wider" style="background:#faf8f2; border-color:#e7e0cc; color:#71277a;">
                        <th class="px-5 py-3.5"># Orden</th>
                        <th class="px-5 py-3.5">Proveedor & Correo</th>
                        <th class="px-5 py-3.5">Producto & Cantidad</th>
                        <th class="px-5 py-3.5">Fecha Emisión</th>
                        <th class="px-5 py-3.5">Estado</th>
                        <th class="px-5 py-3.5 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y" style="divide-color:#f1eee4;">
                    @foreach($ordenes as $ord)
                    @php
                        $estadoStyle = match($ord->estado) {
                            'enviada'   => 'background:#f0fdf4; color:#166534;',
                            'recibida'  => 'background:#eff6ff; color:#1e40af;',
                            'cancelada' => 'background:#fef2f2; color:#991b1b;',
                            default     => 'background:#faf5ff; color:#71277a;',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="px-5 py-4">
                            <span class="font-extrabold text-sm block" style="color:#71277a;">
                                {{ $ord->numero_orden }}
                            </span>
                            <span class="text-[11px] text-gray-400">ID #{{ $ord->id }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-gray-900">{{ $ord->proveedor?->nombre ?? 'Proveedor no asignado' }}</div>
                            <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <span>{{ $ord->correo_destino ?: ($ord->proveedor?->correo ?? 'Sin correo') }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-900">
                                {{ $ord->producto?->nombre ?? ($ord->detalles[0]['nombre'] ?? 'Producto') }}
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded text-xs font-black bg-emerald-100 text-emerald-800">
                                    + {{ $ord->cantidad_solicitada }} {{ $ord->producto?->unidad ?? 'uds' }}
                                </span>
                                <span class="text-[11px] text-gray-500">
                                    (Stock al pedir: {{ $ord->stock_actual }} / mín: {{ $ord->stock_minimo }})
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-xs" style="color:#5a5a4f;">
                            <div>{{ ($ord->fecha_generacion ?? $ord->created_at)?->format('d/m/Y') }}</div>
                            <div class="text-[11px] text-gray-400">{{ ($ord->fecha_generacion ?? $ord->created_at)?->format('H:i A') }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider" style="{{ $estadoStyle }}">
                                {{ $ord->estado }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                {{-- Reenviar Correo --}}
                                <form id="formReenviar{{ $ord->id }}" method="POST" action="{{ route('superadmin.ordenes.reenviar', $ord) }}">
                                    @csrf
                                    <button type="button"
                                            onclick="confirmarReenvioOrden('formReenviar{{ $ord->id }}', '{{ $ord->numero_orden }}', '{{ addslashes($ord->correo_destino ?: ($ord->proveedor?->correo ?? '')) }}')"
                                            class="p-2 rounded-xl border hover:bg-purple-50 text-purple-700 transition cursor-pointer"
                                            style="border-color:#e7e0cc;" title="Reenviar orden por correo al proveedor">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                                    </button>
                                </form>

                                {{-- Cambiar estado a Recibida --}}
                                @if($ord->estado !== 'recibida')
                                <form id="formRecibida{{ $ord->id }}" method="POST" action="{{ route('superadmin.ordenes.estado', $ord) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="estado" value="recibida">
                                    <button type="button"
                                            onclick="confirmarMarcarRecibida('formRecibida{{ $ord->id }}', '{{ $ord->numero_orden }}')"
                                            class="p-2 rounded-xl border hover:bg-emerald-50 text-emerald-700 transition cursor-pointer"
                                            style="border-color:#bbf7d0;" title="Marcar como mercancía recibida">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t" style="border-color:#f1eee4;">
            {{ $ordenes->links() }}
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center mb-3" style="background:#f3e8ff;">
                <svg class="w-8 h-8" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            <h3 class="text-base font-extrabold" style="color:#1c2b16;">No hay órdenes de pedido registradas</h3>
            <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                Las órdenes se generarán y enviarán por correo automáticamente cuando los productos lleguen al stock mínimo, o puedes crear una ahora mismo.
            </p>
            <button type="button" onclick="abrirModalNueva()"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-xs text-white shadow-sm transition hover:opacity-90"
                    style="background:#71277a;">
                + Crear primera orden de pedido
            </button>
        </div>
        @endif
    </div>

    {{-- MODAL NUEVA ORDEN DE PEDIDO --}}
    <div id="modalNuevaOrden"
         class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-all"
         onclick="cerrarModalNuevaAlClickeo(event)">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border overflow-hidden animate-in fade-in zoom-in-95 duration-150"
             onclick="event.stopPropagation()"
             style="border-color:#e7e0cc;">
            
            <div class="flex items-center justify-between p-5 border-b" style="background:#faf8f2; border-color:#e7e0cc;">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-xs" style="background:#f3e8ff;">
                        <svg class="w-4 h-4" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-gray-900 leading-tight">Nueva Orden de Pedido por Correo</h3>
                        <p class="text-[11px] text-gray-500">Se notificará automáticamente a la dirección de correo del proveedor.</p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalNueva()"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition text-xl font-black">
                    ✕
                </button>
            </div>

            <form method="POST" action="{{ route('superadmin.ordenes.store') }}" class="p-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold mb-1" style="color:#5a5a4f;">Producto a solicitar *</label>
                    <select name="producto_id" id="modalSelectProducto" required
                            onchange="actualizarInfoProducto(this)"
                            class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition focus:border-[#71277a]"
                            style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Selecciona un producto...</option>
                        @php
                            $todosProductos = \App\Models\Producto::with(['proveedor', 'lotes'])->where('activo', true)->orderBy('nombre')->get();
                        @endphp
                        @foreach($todosProductos as $prd)
                        @php
                            $stk = $prd->stock_calculado;
                            $sug = max(1, ($prd->stockMinimo * 2) - $stk);
                        @endphp
                        <option value="{{ $prd->id }}"
                                data-proveedor="{{ $prd->proveedor?->nombre ?? 'Sin proveedor' }}"
                                data-correo="{{ $prd->proveedor?->correo ?? 'Sin correo' }}"
                                data-stock="{{ $stk }}"
                                data-minimo="{{ $prd->stockMinimo }}"
                                data-unidad="{{ $prd->unidad }}"
                                data-sugerido="{{ $sug }}"
                                @selected(request('producto_id') == $prd->id)>
                            {{ $prd->nombre }} (Stock: {{ $stk }} / Mín: {{ $prd->stockMinimo }}) — Prov: {{ $prd->proveedor?->nombre ?? 'N/A' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Banner dinámico del proveedor --}}
                <div id="infoProveedorBox" class="p-3 rounded-xl border text-xs" style="background:#faf8f2; border-color:#e7e0cc; display:none;">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-bold text-gray-700">Proveedor de destino:</span>
                        <span id="txtProveedorNombre" class="font-extrabold text-purple-900"></span>
                    </div>
                    <div class="flex justify-between items-center text-gray-500">
                        <span>Correo electrónico:</span>
                        <span id="txtProveedorCorreo" class="font-semibold text-emerald-800"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color:#5a5a4f;">Cantidad a solicitar *</label>
                    <input type="number" name="cantidad_solicitada" id="modalInputCantidad" required min="1" max="99999" value="10"
                           class="w-full px-3.5 py-2.5 rounded-xl border text-sm outline-none transition focus:border-[#71277a]"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <p class="text-[11px] text-gray-400 mt-1">Cantidad de unidades requeridas para reabastecimiento.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold mb-1" style="color:#5a5a4f;">Instrucciones / Observaciones (opcional)</label>
                    <textarea name="observaciones" rows="3"
                              placeholder="Ej: Entrega prioritaria en el almacén central del Centro Agropecuario..."
                              class="w-full px-3.5 py-2 rounded-xl border text-sm outline-none transition focus:border-[#71277a]"
                              style="border-color:#e7e0cc; background:#fafafa;"></textarea>
                </div>

                <div class="pt-3 border-t flex items-center justify-end gap-2.5" style="border-color:#f1eee4;">
                    <button type="button" onclick="cerrarModalNueva()"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold border hover:bg-gray-50 transition cursor-pointer"
                            style="border-color:#e7e0cc; color:#5a5a4f;">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition hover:opacity-90 cursor-pointer"
                            style="background:#39a900;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                        <span>Generar y Enviar Orden por Correo</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DE CONFIRMACIÓN CON DISEÑO PROFESIONAL --}}
    <div id="modalConfirmacion"
         class="fixed inset-0 z-60 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-all"
         onclick="cerrarModalConfirmacionAlClickeo(event)">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md border overflow-hidden text-center p-7 animate-in fade-in zoom-in-95 duration-150"
             onclick="event.stopPropagation()"
             style="border-color:#e7e0cc;">
            
            {{-- Ícono superior grande y estilizado --}}
            <div id="confirmIconWrapper" class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center mb-4 shadow-sm" style="background:#fffbeb;">
                <svg id="confirmIconSvg" class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
            </div>

            {{-- Título y mensaje --}}
            <h3 id="confirmModalTitulo" class="text-lg font-black text-gray-900 mb-2 leading-snug">
                ¿Confirmar acción?
            </h3>
            <p id="confirmModalMensaje" class="text-sm text-gray-600 leading-relaxed mb-4">
                Mensaje de confirmación detallado.
            </p>

            {{-- Detalle adicional --}}
            <div id="confirmModalDetalle" class="p-3.5 rounded-2xl border text-xs text-left mb-6 hidden" style="background:#faf8f2; border-color:#e7e0cc;">
            </div>

            {{-- Botones de acción --}}
            <div class="flex items-center justify-center gap-3">
                <button type="button" onclick="cerrarModalConfirmacion()"
                        class="flex-1 px-4 py-2.5 rounded-xl text-xs font-bold border hover:bg-gray-50 transition cursor-pointer"
                        style="border-color:#e7e0cc; color:#5a5a4f;">
                    Cancelar
                </button>
                <button type="button" id="btnConfirmarAccion"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-sm transition hover:opacity-95 cursor-pointer"
                        style="background:#39a900;">
                    <span id="btnConfirmarTexto">Aceptar</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
let callbackConfirmacion = null;

function mostrarConfirmacion({
    titulo = '¿Confirmar acción?',
    mensaje = '¿Estás seguro de continuar?',
    detalleHtml = '',
    icono = 'rayo', // 'rayo' | 'correo' | 'check' | 'alerta'
    textoConfirmar = 'Aceptar',
    colorConfirmar = '#39a900',
    onConfirm = null
}) {
    const modal = document.getElementById('modalConfirmacion');
    const txtTitulo = document.getElementById('confirmModalTitulo');
    const txtMensaje = document.getElementById('confirmModalMensaje');
    const boxDetalle = document.getElementById('confirmModalDetalle');
    const btnTexto = document.getElementById('btnConfirmarTexto');
    const btnConfirm = document.getElementById('btnConfirmarAccion');
    const iconWrapper = document.getElementById('confirmIconWrapper');

    if (!modal) return;

    if (txtTitulo) txtTitulo.textContent = titulo;
    if (txtMensaje) txtMensaje.textContent = mensaje;
    if (btnTexto) btnTexto.textContent = textoConfirmar;
    if (btnConfirm) btnConfirm.style.background = colorConfirmar;

    if (boxDetalle) {
        if (detalleHtml) {
            boxDetalle.innerHTML = detalleHtml;
            boxDetalle.classList.remove('hidden');
        } else {
            boxDetalle.innerHTML = '';
            boxDetalle.classList.add('hidden');
        }
    }

    if (iconWrapper) {
        if (icono === 'correo') {
            iconWrapper.style.background = '#f3e8ff';
            iconWrapper.innerHTML = `<svg class="w-8 h-8 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>`;
        } else if (icono === 'check') {
            iconWrapper.style.background = '#f0fdf4';
            iconWrapper.innerHTML = `<svg class="w-8 h-8 text-green-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>`;
        } else if (icono === 'alerta') {
            iconWrapper.style.background = '#fef2f2';
            iconWrapper.innerHTML = `<svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;
        } else {
            iconWrapper.style.background = '#fffbeb';
            iconWrapper.innerHTML = `<svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>`;
        }
    }

    callbackConfirmacion = onConfirm;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModalConfirmacion() {
    const modal = document.getElementById('modalConfirmacion');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
    callbackConfirmacion = null;
}

function cerrarModalConfirmacionAlClickeo(e) {
    const modal = document.getElementById('modalConfirmacion');
    if (e.target === modal) {
        cerrarModalConfirmacion();
    }
}

document.getElementById('btnConfirmarAccion')?.addEventListener('click', () => {
    const cb = callbackConfirmacion;
    cerrarModalConfirmacion();
    if (typeof cb === 'function') {
        cb();
    }
});

function confirmarRevisarStock() {
    mostrarConfirmacion({
        titulo: '¿Revisar y Notificar Stock Mínimo?',
        mensaje: '¿Deseas verificar todo el catálogo y generar órdenes automáticas por correo para los productos que estén en nivel de stock mínimo?',
        detalleHtml: `
            <div class="flex items-center gap-2 text-amber-900 font-bold mb-1">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Acción automática de abastecimiento
            </div>
            <p class="text-gray-600 text-[11px] leading-relaxed">
                El sistema detectará los productos con stock bajo o agotado, calculará la reposición sugerida y enviará un correo con la orden de compra formal a cada proveedor.
            </p>
        `,
        icono: 'rayo',
        textoConfirmar: 'Sí, verificar y generar',
        colorConfirmar: '#d97706',
        onConfirm: () => {
            document.getElementById('formRevisarStockMinimo')?.submit();
        }
    });
}

function confirmarReenvioOrden(formId, numeroOrden, correo) {
    mostrarConfirmacion({
        titulo: `¿Reenviar Orden #${numeroOrden}?`,
        mensaje: `Se enviará nuevamente la notificación formal por correo electrónico al proveedor.`,
        detalleHtml: `
            <div class="text-gray-700 text-xs">
                <strong>Destinatario:</strong> <span class="text-purple-900 font-bold">${correo || 'Correo del proveedor'}</span>
            </div>
        `,
        icono: 'correo',
        textoConfirmar: 'Reenviar Correo',
        colorConfirmar: '#71277a',
        onConfirm: () => {
            document.getElementById(formId)?.submit();
        }
    });
}

function confirmarMarcarRecibida(formId, numeroOrden) {
    mostrarConfirmacion({
        titulo: `¿Marcar Orden #${numeroOrden} como Recibida?`,
        mensaje: 'Esta acción actualizará el estado de la orden indicando que la mercancía ha sido recibida físicamente en las instalaciones de Zajuna Campo.',
        icono: 'check',
        textoConfirmar: 'Sí, Marcar Recibida',
        colorConfirmar: '#16a34a',
        onConfirm: () => {
            document.getElementById(formId)?.submit();
        }
    });
}

function confirmarPedirProducto(formId, nombreProd, cantidad, nombreProv, correoProv) {
    mostrarConfirmacion({
        titulo: `¿Generar Orden para ${nombreProd}?`,
        mensaje: `Se emitirá una orden por ${cantidad} unidades y se notificará de inmediato al proveedor por correo electrónico.`,
        detalleHtml: `
            <div class="space-y-1 text-xs">
                <div><strong>Proveedor:</strong> <span class="text-gray-900">${nombreProv}</span></div>
                <div><strong>Correo:</strong> <span class="text-emerald-800 font-semibold">${correoProv || 'Sin correo registrado'}</span></div>
                <div><strong>Cantidad a pedir:</strong> <span class="text-purple-900 font-black">+${cantidad} unidades</span></div>
            </div>
        `,
        icono: 'correo',
        textoConfirmar: 'Generar y Enviar',
        colorConfirmar: '#39a900',
        onConfirm: () => {
            document.getElementById(formId)?.submit();
        }
    });
}
function abrirModalNueva(productoId = null) {
    const modal = document.getElementById('modalNuevaOrden');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    if (productoId) {
        const sel = document.getElementById('modalSelectProducto');
        if (sel) {
            sel.value = productoId;
            actualizarInfoProducto(sel);
        }
    }
}

function cerrarModalNueva() {
    const modal = document.getElementById('modalNuevaOrden');
    if (modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
    // Limpiar parámetros query de la URL para que no vuelva a abrirse tras recargar
    try {
        const url = new URL(window.location.href);
        if (url.searchParams.has('modal') || url.searchParams.has('producto_id')) {
            url.searchParams.delete('modal');
            url.searchParams.delete('producto_id');
            window.history.replaceState({}, document.title, url.pathname + (url.search ? url.search : ''));
        }
    } catch (e) {}
}

function cerrarModalNuevaAlClickeo(e) {
    const modal = document.getElementById('modalNuevaOrden');
    if (e.target === modal) {
        cerrarModalNueva();
    }
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        cerrarModalNueva();
    }
});

function actualizarInfoProducto(sel) {
    const opt = sel.options[sel.selectedIndex];
    const box = document.getElementById('infoProveedorBox');
    const txtProv = document.getElementById('txtProveedorNombre');
    const txtEmail = document.getElementById('txtProveedorCorreo');
    const inpCant = document.getElementById('modalInputCantidad');

    if (!opt || !opt.value) {
        if (box) box.style.display = 'none';
        return;
    }

    if (box) box.style.display = 'block';
    if (txtProv) txtProv.textContent = opt.dataset.proveedor || 'Sin proveedor';
    if (txtEmail) txtEmail.textContent = opt.dataset.correo || 'Sin correo registrado';

    if (inpCant && opt.dataset.sugerido) {
        inpCant.value = opt.dataset.sugerido;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Si viene ?modal=nueva en la URL, abrir modal automáticamente
    const params = new URLSearchParams(window.location.search);
    if (params.get('modal') === 'nueva') {
        abrirModalNueva(params.get('producto_id'));
    }
    const sel = document.getElementById('modalSelectProducto');
    if (sel && sel.value) {
        actualizarInfoProducto(sel);
    }
});
</script>
</x-superadmin-layout>

