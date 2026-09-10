@php $title = 'Mis ventas'; @endphp

<x-proveedor-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-2"
                  style="background:#fdc300; color:#71277a;">
                Proveedor
            </span>
            <h1 class="text-2xl font-extrabold leading-tight">Mis ventas</h1>
            <p class="text-sm mt-0.5" style="color:#5a5a4f;">
                Ventas registradas de tus productos
            </p>
        </div>
    </div>

    {{-- STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:#9a9a8a;">Ventas este mes</p>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['total_mes'] }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:#9a9a8a;">Ingresos este mes</p>
            <p class="text-3xl font-extrabold" style="color:#39a900;">
                ${{ number_format($stats['ingresos_mes'], 2) }}
            </p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-xs font-semibold uppercase tracking-wide mb-1" style="color:#9a9a8a;">Ventas hoy</p>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['total_hoy'] }}</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <form method="GET" action="{{ route('proveedor.ventas.index') }}"
          class="flex flex-col sm:flex-row gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Buscar por cliente..."
               class="flex-1 rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2"
               style="focus-ring-color:#71277a;">
        <select name="mes"
                class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none">
            <option value="">Todos los meses</option>
            @foreach(range(1,12) as $m)
                <option value="{{ $m }}" @selected(request('mes') == $m)>
                    {{ \Carbon\Carbon::create()->month($m)->locale('es')->isoFormat('MMMM') }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white transition"
                style="background:#71277a;">
            Filtrar
        </button>
        @if(request('search') || request('mes'))
            <a href="{{ route('proveedor.ventas.index') }}"
               class="px-5 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 hover:bg-gray-50 transition text-center">
                Limpiar
            </a>
        @endif
    </form>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($ventas->isEmpty())
            <div class="py-16 text-center" style="color:#9a9a8a;">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                    <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                </svg>
                <p class="text-sm font-medium">No hay ventas que coincidan con los filtros.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#fafafa; border-bottom:1px solid #f0f0f0;">
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">#</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Fecha</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Cliente</th>
                            <th class="text-left px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Productos</th>
                            <th class="text-right px-5 py-3.5 font-semibold" style="color:#5a5a4f;">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($ventas as $venta)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3.5 font-mono text-xs" style="color:#9a9a8a;">#{{ $venta->id }}</td>
                                <td class="px-5 py-3.5">
                                    {{ $venta->fechaVenta?->locale('es')->isoFormat('D MMM YYYY, HH:mm') ?? '—' }}
                                </td>
                                <td class="px-5 py-3.5 font-medium">{{ $venta->cliente ?: '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($venta->items as $item)
                                            @if($item->producto && $item->producto->proveedor_id == auth()->user()->proveedor_id)
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium"
                                                      style="background:#f3e8ff; color:#71277a;">
                                                    {{ $item->producto->nombre }}
                                                    <span class="opacity-60">×{{ $item->cantidad }}</span>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-right font-bold" style="color:#39a900;">
                                    ${{ number_format($venta->total, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}
            @if($ventas->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $ventas->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
</x-proveedor-layout>
