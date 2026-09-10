@php $title = 'Panel general'; @endphp

<x-proveedor-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <span class="inline-block text-xs font-bold px-3 py-1 rounded-full mb-2"
                  style="background:#fdc300; color:#71277a;">
                Proveedor
            </span>
            <h1 class="text-2xl font-extrabold leading-tight">Panel general</h1>
            <p class="text-sm mt-0.5" style="color:#5a5a4f;">
                Bienvenido, <span class="font-semibold">{{ auth()->user()->name }}</span>.
                Hoy es {{ \Carbon\Carbon::now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}.
            </p>
        </div>
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold"
             style="background:#fdf3ff; border:1px solid #e9d5ff; color:#71277a;">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            Acceso de solo lectura
        </div>
    </div>

    @isset($sinProveedor)
    {{-- Sin proveedor asociado --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-yellow-100 text-center">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
        </svg>
        <p class="text-sm font-semibold" style="color:#5a5a4f;">Tu cuenta aún no tiene un proveedor asociado. Contacta al administrador.</p>
    </div>
    @else

    {{-- ── MÉTRICAS PRINCIPALES ───────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- Mis productos --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:#9a9a8a;">Mis productos</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['total_productos'] }}</p>
            <p class="text-xs mt-1" style="color:#9a9a8a;">productos activos</p>
        </div>

        {{-- Stock total --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:#9a9a8a;">Stock total</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#eff6ff;">
                    <svg class="w-5 h-5" style="color:#3b82f6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73L13 2.27a2 2 0 0 0-2 0L4 6.27A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73L11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ number_format($stats['stock_total']) }}</p>
            <p class="text-xs mt-1" style="color:#9a9a8a;">unidades disponibles</p>
        </div>

        {{-- Ventas este mes --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:#9a9a8a;">Ventas este mes</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#fffbeb;">
                    <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                        <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                    </svg>
                </span>
            </div>
            <p class="text-3xl font-extrabold" style="color:#1c2b16;">{{ $stats['ventas_mes'] }}</p>
            <p class="text-xs mt-1" style="color:#9a9a8a;">
                ${{ number_format($stats['ingresos_mes'], 0, ',', '.') }} en ingresos
            </p>
        </div>

        {{-- Alertas --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color:#9a9a8a;">Alertas</p>
                <span class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#fef2f2;">
                    <svg class="w-5 h-5" style="color:#ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    </svg>
                </span>
            </div>
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs" style="color:#5a5a4f;">Próx. a vencer</span>
                    <span class="text-sm font-bold" style="color:#d97706;">{{ $stats['proximo_vencer'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs" style="color:#5a5a4f;">Agotados</span>
                    <span class="text-sm font-bold" style="color:#ef4444;">{{ $stats['agotados'] }}</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ── FILA PRINCIPAL ─────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Gráfico ventas por mes --}}
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-sm font-bold mb-5" style="color:#1c2b16;">Ventas últimos 6 meses</h2>
            @php
                $maxVentas = $ventasMes->max('cantidad') ?: 1;
            @endphp
            <div class="flex items-end gap-3 h-36">
                @foreach($ventasMes as $vm)
                    @php
                        $altura = max(8, round(($vm['cantidad'] / $maxVentas) * 100));
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-xs font-bold" style="color:#39a900;">
                            {{ $vm['cantidad'] > 0 ? $vm['cantidad'] : '' }}
                        </span>
                        <div class="w-full rounded-t-lg transition-all"
                             style="height:{{ $altura }}%; background:#39a900; opacity:{{ $vm['cantidad'] > 0 ? '1' : '0.15' }}; min-height:8px;">
                        </div>
                        <span class="text-xs capitalize" style="color:#9a9a8a;">{{ $vm['mes'] }}</span>
                    </div>
                @endforeach
            </div>
            @if($ventasMes->sum('cantidad') === 0)
                <p class="text-xs text-center mt-4" style="color:#9a9a8a;">Sin ventas en los últimos 6 meses</p>
            @endif
        </div>

        {{-- Accesos rápidos --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-sm font-bold mb-5" style="color:#1c2b16;">Accesos rápidos</h2>
            <div class="flex flex-col gap-3">
                <a href="{{ route('proveedor.ventas.index') }}"
                   class="flex items-center gap-4 p-4 rounded-xl border transition hover:shadow-sm hover:border-yellow-200 group"
                   style="border-color:#f0ece0;">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform"
                          style="background:#fffbeb;">
                        <svg class="w-5 h-5" style="color:#d97706;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4h2l2.4 12.4a2 2 0 0 0 2 1.6h7.4a2 2 0 0 0 2-1.6L21 8H6"/>
                            <circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold" style="color:#1c2b16;">Mis ventas</p>
                        <p class="text-xs" style="color:#9a9a8a;">{{ $stats['ventas_hoy'] }} venta(s) hoy</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto" style="color:#9a9a8a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </a>

                <a href="{{ route('proveedor.inventario.index') }}"
                   class="flex items-center gap-4 p-4 rounded-xl border transition hover:shadow-sm hover:border-green-200 group"
                   style="border-color:#f0ece0;">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform"
                          style="background:#f0fdf4;">
                        <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="3" y="7" width="18" height="14" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold" style="color:#1c2b16;">Mi inventario</p>
                        <p class="text-xs" style="color:#9a9a8a;">{{ number_format($stats['stock_total']) }} unidades en stock</p>
                    </div>
                    <svg class="w-4 h-4 ml-auto" style="color:#9a9a8a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 18l6-6-6-6"/>
                    </svg>
                </a>
            </div>
        </div>

    </div>

    {{-- ── FILA INFERIOR ──────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Últimas ventas --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold" style="color:#1c2b16;">Últimas ventas</h2>
                <a href="{{ route('proveedor.ventas.index') }}"
                   class="text-xs font-semibold transition hover:underline"
                   style="color:#71277a;">Ver todas →</a>
            </div>
            @if($ultimasVentas->isEmpty())
                <div class="py-8 text-center" style="color:#9a9a8a;">
                    <p class="text-xs">Sin ventas registradas aún</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($ultimasVentas as $venta)
                        @php
                            $misItems = $venta->items->filter(fn($i) => $i->producto?->proveedor_id == auth()->user()->proveedor_id);
                            $misIngresos = $misItems->sum('subtotal');
                        @endphp
                        <div class="flex items-center justify-between py-2 border-b last:border-0" style="border-color:#f3f0e8;">
                            <div>
                                <p class="text-sm font-semibold">{{ $venta->cliente ?: 'Sin nombre' }}</p>
                                <p class="text-xs" style="color:#9a9a8a;">
                                    {{ $venta->fechaVenta->locale('es')->isoFormat('D MMM YYYY') }}
                                    · {{ $misItems->count() }} producto(s)
                                </p>
                            </div>
                            <span class="text-sm font-bold" style="color:#39a900;">
                                ${{ number_format($misIngresos, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Productos que necesitan atención --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-sm font-bold" style="color:#1c2b16;">Productos que necesitan atención</h2>
                <a href="{{ route('proveedor.inventario.index') }}"
                   class="text-xs font-semibold transition hover:underline"
                   style="color:#71277a;">Ver inventario →</a>
            </div>
            @if($productosAlerta->isEmpty())
                <div class="py-8 text-center" style="color:#9a9a8a;">
                    <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                    <p class="text-xs">Todo el inventario está en buen estado</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($productosAlerta as $p)
                        @php
                            $stock  = $p->stock_calculado;
                            $estado = $p->stock_estado;
                            $lote   = $p->lote_proximo_vencer;

                            if ($estado === 'agotado') {
                                [$bg, $txt, $etiqueta] = ['#fee2e2', '#991b1b', 'Agotado'];
                            } elseif ($lote && $lote->proximo_vencer) {
                                [$bg, $txt, $etiqueta] = ['#fef9c3', '#854d0e', 'Próx. a vencer'];
                            } else {
                                [$bg, $txt, $etiqueta] = ['#fef9c3', '#854d0e', 'Stock bajo'];
                            }
                        @endphp
                        <div class="flex items-center justify-between py-2 border-b last:border-0" style="border-color:#f3f0e8;">
                            <div>
                                <p class="text-sm font-semibold">{{ $p->nombre }}</p>
                                <p class="text-xs" style="color:#9a9a8a;">
                                    Stock: {{ number_format($stock) }} {{ $p->unidad }}
                                    @if($lote && $lote->proximo_vencer)
                                        · Vence {{ $lote->fecha_vencimiento->locale('es')->isoFormat('D MMM') }}
                                    @endif
                                </p>
                            </div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background:{{ $bg }}; color:{{ $txt }};">
                                {{ $etiqueta }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    @endisset

</div>
</x-proveedor-layout>
