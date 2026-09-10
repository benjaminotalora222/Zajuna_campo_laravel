@php $title = 'Inventario'; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Inventario</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Stock por lotes con trazabilidad completa y vencimientos.</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('superadmin.inventario.index') }}"
          class="flex flex-wrap gap-3 mb-6 items-end">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por nombre o código..."
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
        <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-bold text-white" style="background:#39a900;">Filtrar</button>
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
                <p class="text-sm font-medium">No hay productos en inventario.</p>
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
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Estado</th>
                            <th class="text-center px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($productos as $p)
                            @php
                                $stock = $p->stock_calculado;
                                $estado = $p->stock_estado;
                                $loteProximo = $p->lote_proximo_vencer;
                                $badgeColor = match($estado) {
                                    'agotado' => ['bg:#fee2e2','color:#991b1b','Agotado'],
                                    'bajo'    => ['bg:#fef9c3','color:#854d0e','Stock bajo'],
                                    default   => ['bg:#dcfce7','color:#166534','Disponible'],
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold">{{ $p->nombre }}</div>
                                    @if($p->codigoBarras)
                                        <div class="text-xs font-mono" style="color:#9a9a8a;">{{ $p->codigoBarras }}</div>
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
                                        @if($loteProximo->proximo_vencer)
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                  style="background:#fef9c3; color:#854d0e;">⚠ Próximo a vencer</span>
                                        @elseif($loteProximo->vencido)
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                                  style="background:#fee2e2; color:#991b1b;">Vencido</span>
                                        @endif
                                    @else
                                        <span style="color:#9a9a8a;">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background:{{ str_replace('bg:','',$badgeColor[0]) }}; color:{{ str_replace('color:','',$badgeColor[1]) }};">
                                        {{ $badgeColor[2] }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('superadmin.inventario.show', $p) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white transition hover:opacity-90"
                                       style="background:#71277a;">
                                        Ver lotes
                                    </a>
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
</x-superadmin-layout>
