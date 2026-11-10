@php $title = 'Inventario'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Inventario</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Stock por lotes con trazabilidad completa, vencimientos y código de barras.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.ordenes.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-white text-sm shadow-sm transition hover:opacity-95"
               style="background:#71277a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                </svg>
                Órdenes de Pedido
            </a>

            <button type="button" onclick="abrirModalEscaner()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-white text-sm shadow-sm transition hover:opacity-95"
                    style="background:#39a900;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 8h10M7 12h10M7 16h10"/>
                </svg>
                Escanear / Salida Rápida
            </button>
        </div>
    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('superadmin.inventario.index') }}"
          class="flex flex-wrap gap-3 mb-6 items-end">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por nombre o código de barras..."
                   class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                   style="border-color:#e7e0cc; background:#fafafa;">
        </div>
        <div class="min-w-[180px]">
            <select name="proveedor_id" class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                    style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Todos los proveedores</option>
                @foreach($proveedores as $prov)
                    <option value="{{ $prov->id }}" @selected(request('proveedor_id') == $prov->id)>{{ $prov->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[170px]">
            <select name="stock" class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                    style="border-color:#e7e0cc; background:#fafafa;">
                <option value="">Todos los estados</option>
                <option value="bajo"    @selected(request('stock') === 'bajo')>Stock bajo</option>
                <option value="agotado" @selected(request('stock') === 'agotado')>Agotado</option>
                <option value="vencido" @selected(request('stock') === 'vencido')>⚠️ Con productos vencidos</option>
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white shadow-xs" style="background:#39a900;">Filtrar</button>
        @if(request()->hasAny(['search','proveedor_id','stock']))
            <a href="{{ route('superadmin.inventario.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-bold border hover:bg-gray-50"
               style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
        @endif
    </form>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($productos->isEmpty())
            <div class="py-16 text-center" style="color:#9a9a8a;">
                <p class="text-sm font-medium">No hay productos en inventario con los filtros aplicados.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#fafafa; border-bottom:1px solid #f0f0f0;">
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Producto</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Proveedor</th>
                            <th class="text-right px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Stock total</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Lote próx. vencer</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Estado / Alerta</th>
                            <th class="text-center px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($productos as $p)
                            @php
                                $stock = $p->stock_calculado;
                                $stockVencido = $p->stock_vencido;
                                $estado = $p->stock_estado;
                                $loteProximo = $p->lote_proximo_vencer;
                                $badgeColor = match($estado) {
                                    'agotado' => ['bg:#fee2e2','color:#991b1b','Agotado'],
                                    'bajo'    => ['bg:#fef9c3','color:#854d0e','Stock bajo'],
                                    default   => ['bg:#dcfce7','color:#166534','Disponible'],
                                };

                                $prodJson = json_encode([
                                    'id' => $p->id,
                                    'nombre' => $p->nombre,
                                    'categoria' => $p->categoria,
                                    'unidad' => $p->unidad,
                                    'precio' => (float)$p->precio,
                                    'precio_formateado' => '$' . number_format($p->precio, 0, ',', '.'),
                                    'codigoBarras' => $p->codigoBarras,
                                    'proveedor' => $p->proveedor?->nombre ?? '',
                                    'stock_total' => $stock,
                                    'stock_vencido' => $stockVencido,
                                ]);
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-gray-900">{{ $p->nombre }}</div>
                                    @if($p->codigoBarras)
                                        <div class="text-xs font-mono flex items-center gap-1 text-gray-500">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h2M4 10h1M4 14h2M4 18h1M9 6h1M9 10h2M9 14h1M9 18h2M14 6h2M14 10h1M14 14h2M14 18h1M19 6h1M19 10h2M19 14h1M19 18h2"/></svg>
                                            {{ $p->codigoBarras }}
                                        </div>
                                    @else
                                        <div class="text-[11px] text-gray-400 italic">Sin código asignado</div>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-sm" style="color:#5a5a4f;">
                                    {{ $p->proveedor?->nombre ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold text-base">
                                    {{ number_format($stock) }}
                                    <span class="text-xs font-normal" style="color:#9a9a8a;">{{ $p->unidad }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($loteProximo)
                                        <div class="text-sm">
                                            {{ $loteProximo->fecha_vencimiento?->locale('es')->isoFormat('D MMM YYYY') }}
                                        </div>
                                        <div class="text-xs" style="color:#9a9a8a;">
                                            {{ $loteProximo->cantidad_disponible }} {{ $p->unidad }} disponibles
                                        </div>
                                        @if($loteProximo->vencido)
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                  style="background:#fee2e2; color:#991b1b;">Lote vencido</span>
                                        @elseif($loteProximo->proximo_vencer)
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                  style="background:#fef9c3; color:#854d0e;">⚠ Próximo a vencer</span>
                                        @endif
                                    @else
                                        <span style="color:#9a9a8a;">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col gap-1 items-start">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                              style="background:{{ str_replace('bg:','',$badgeColor[0]) }}; color:{{ str_replace('color:','',$badgeColor[1]) }};">
                                            {{ $badgeColor[2] }}
                                        </span>
                                        @if($stockVencido > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-red-100 text-red-700 animate-pulse"
                                                  title="Requiere baja de inventario">
                                                ⚠️ {{ number_format($stockVencido) }} {{ $p->unidad }} vencidos
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        {{-- Ver Lotes --}}
                                        <a href="{{ route('superadmin.inventario.show', $p) }}"
                                           class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-white transition hover:opacity-90"
                                           style="background:#71277a;" title="Ver lotes y movimientos">
                                            Ver lotes
                                        </a>

                                        @if($estado === 'bajo' || $estado === 'agotado')
                                        {{-- Pedir al Proveedor --}}
                                        <a href="{{ route('superadmin.ordenes.index', ['producto_id' => $p->id, 'modal' => 'nueva']) }}"
                                           class="p-1.5 rounded-lg border border-amber-300 text-amber-800 bg-amber-50 hover:bg-amber-100 transition"
                                           title="Generar orden de pedido al proveedor por correo">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                                            </svg>
                                        </a>
                                        @endif

                                        {{-- Ver/Imprimir Código de Barras --}}
                                        <button type="button" onclick="abrirModalCodigoBarras({{ $prodJson }})"
                                                class="p-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 transition"
                                                title="Ver e imprimir código de barras">
                                            <svg class="w-4 h-4 text-purple-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h10M3 4h18M3 20h18"/>
                                            </svg>
                                        </button>

                                        {{-- Escanear / Salida Directa --}}
                                        <button type="button" onclick="abrirModalEscaner('{{ $p->codigoBarras ?: $p->id }}')"
                                                class="p-1.5 rounded-lg border border-green-200 text-green-700 hover:bg-green-50 transition"
                                                title="Registrar salida rápida">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($productos->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $productos->links() }}
                </div>
            @endif
        @endif
    </div>

</div>

{{-- MODALES DE ESCÁNER Y CÓDIGO DE BARRAS --}}
@include('inventario.partials.scanner-modal')
@include('inventario.partials.barcode-modal')

</x-superadmin-layout>
