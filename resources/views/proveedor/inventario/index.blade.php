@php $title = 'Mi inventario'; @endphp

<x-proveedor-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-2"
                  style="background:#fdc300; color:#71277a;">
                Proveedor
            </span>
            <h1 class="text-2xl font-extrabold leading-tight">Mi inventario</h1>
            <p class="text-sm mt-0.5" style="color:#5a5a4f;">
                Productos y stock disponible registrados a tu nombre
            </p>
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:#9a9a8a;">Total productos</p>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:#9a9a8a;">Stock total</p>
            <p class="text-3xl font-extrabold" style="color:#39a900;">{{ number_format($stats['stock_total']) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:#9a9a8a;">Próximos a vencer</p>
            <p class="text-3xl font-extrabold" style="color:#d97706;">{{ $stats['proximo_vencer'] }}</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('proveedor.inventario.index') }}"
          class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Buscar por producto o código..."
               class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2">
        <select name="estado"
                class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none">
            <option value="">Todos los estados</option>
            <option value="disponible"     @selected(request('estado') === 'disponible')>Disponible</option>
            <option value="proximo_vencer" @selected(request('estado') === 'proximo_vencer')>Próximo a vencer</option>
            <option value="agotado"        @selected(request('estado') === 'agotado')>Agotado</option>
        </select>
        <button type="submit"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition"
                style="background:#71277a;">
            Filtrar
        </button>
        @if(request('search') || request('estado'))
            <a href="{{ route('proveedor.inventario.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 hover:bg-gray-50 transition text-center">
                Limpiar
            </a>
        @endif
    </form>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($productos->isEmpty())
            <div class="py-16 text-center" style="color:#9a9a8a;">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>
                </svg>
                <p class="text-sm font-medium">No hay productos registrados a tu nombre.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#fafafa; border-bottom:1px solid #f0f0f0;">
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Producto</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Código</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Unidad</th>
                            <th class="text-right px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Stock total</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Próx. vencimiento</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($productos as $producto)
                            @php
                                $stock        = $producto->stock_calculado;
                                $loteVencer   = $producto->lote_proximo_vencer;
                                $estado       = $producto->stock_estado;

                                $badgeMap = [
                                    'ok'     => ['#dcfce7', '#166534', 'Disponible'],
                                    'bajo'   => ['#fef9c3', '#854d0e', 'Stock bajo'],
                                    'agotado'=> ['#fee2e2', '#991b1b', 'Agotado'],
                                ];
                                [$bgColor, $textColor, $label] = $badgeMap[$estado] ?? ['#f3f4f6', '#374151', ucfirst($estado)];

                                // Marcar próximo a vencer si el lote más cercano vence en ≤ 30 días
                                if ($loteVencer && $loteVencer->proximo_vencer) {
                                    $bgColor   = '#fef9c3';
                                    $textColor = '#854d0e';
                                    $label     = 'Próximo a vencer';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3.5 font-medium">{{ $producto->nombre }}</td>
                                <td class="px-5 py-3.5 font-mono text-xs" style="color:#9a9a8a;">
                                    {{ $producto->codigoBarras ?: '—' }}
                                </td>
                                <td class="px-5 py-3.5">{{ $producto->unidad ?: '—' }}</td>
                                <td class="px-5 py-3.5 text-right font-bold">
                                    {{ number_format($stock) }}
                                </td>
                                <td class="px-5 py-3.5">
                                    @if($loteVencer)
                                        {{ $loteVencer->fecha_vencimiento->locale('es')->isoFormat('D MMM YYYY') }}
                                    @else
                                        <span style="color:#9a9a8a;">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                          style="background:{{ $bgColor }}; color:{{ $textColor }};">
                                        {{ $label }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            @if($productos->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $productos->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
</x-proveedor-layout>
