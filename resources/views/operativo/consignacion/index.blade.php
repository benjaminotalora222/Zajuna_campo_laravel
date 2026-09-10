@php $title = 'Inventario y Consignación'; @endphp

<x-operativo-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-extrabold leading-tight">Inventario en Consignación</h1>
            <p class="text-sm mt-1" style="color:#5a5a4f;">Stock por lotes con trazabilidad completa y vencimientos.</p>
        </div>
        <button onclick="abrirModalIngreso()"
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
                <p class="text-xs" style="color:#9a9a8a;">Productos activos</p>
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
        <form method="GET" action="{{ route('operativo.consignacion.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Buscar producto o proveedor</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, código o proveedor..."
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
                    <option value="">Todos los proveedores</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->id }}" @selected(request('proveedor_id') == $prov->id)>{{ $prov->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <label class="block text-xs font-semibold mb-1.5" style="color:#5a5a4f;">Estado</label>
                <select name="estado" class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none" style="border-color:#e7e0cc; background:#fafafa;">
                    <option value="">Todos</option>
                    <option value="proximo_vencer" @selected(request('estado') === 'proximo_vencer')>Próximo a vencer</option>
                    <option value="bajo"           @selected(request('estado') === 'bajo')>Stock bajo</option>
                    <option value="agotado"        @selected(request('estado') === 'agotado')>Agotado</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white hover:opacity-90" style="background:#39a900;">Filtrar</button>
            @if(request()->hasAny(['search','proveedor_id','estado']))
                <a href="{{ route('operativo.consignacion.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-sm border hover:bg-gray-50" style="border-color:#e7e0cc; color:#5a5a4f;">Limpiar</a>
            @endif
        </form>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($productos->isEmpty())
            <div class="py-16 text-center" style="color:#9a9a8a;">
                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                </svg>
                <p class="text-sm font-semibold">No hay productos en inventario.</p>
                <p class="text-xs mt-1">Registra un nuevo ingreso con el botón "Nuevo Ingreso".</p>
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:2px solid #f3f0e8;">
                        <th class="text-left px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Producto</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Proveedor</th>
                        <th class="text-right px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Stock Total</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Lote próx. vencer</th>
                        <th class="text-left px-4 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Estado</th>
                        <th class="text-right px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color:#9a9a8a;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productos as $p)
                    @php
                        $stock       = $p->stock_calculado;
                        $estado      = $p->stock_estado;
                        $loteProximo = $p->lote_proximo_vencer;
                        $badgeMap    = [
                            'agotado' => ['#fef2f2', '#ef4444', 'Agotado'],
                            'bajo'    => ['#fef9c3', '#d97706', 'Stock bajo'],
                            'ok'      => ['#f0fdf4', '#166534', 'Disponible'],
                        ];
                        [$bgBadge, $colBadge, $labelBadge] = $badgeMap[$estado] ?? $badgeMap['ok'];

                        if ($loteProximo && $loteProximo->proximo_vencer) {
                            $bgBadge    = '#fffbeb';
                            $colBadge   = '#d97706';
                            $labelBadge = 'Próximo a vencer';
                        }
                    @endphp
                    <tr class="border-b last:border-0 hover:bg-gray-50 transition" style="border-color:#f3f0e8;">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-sm" style="color:#1c2b16;">{{ $p->nombre }}</p>
                            @if($p->codigoBarras)
                                <p class="text-xs mt-0.5 font-mono" style="color:#9a9a8a;">{{ $p->codigoBarras }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0" style="background:#faf5ff;">
                                    <svg class="w-3.5 h-3.5" style="color:#71277a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <span class="text-sm" style="color:#5a5a4f;">{{ $p->proveedor?->nombre ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <span class="font-extrabold text-sm" style="color:#1c2b16;">{{ number_format($stock) }}</span>
                            <span class="text-xs ml-1" style="color:#9a9a8a;">{{ $p->unidad }}</span>
                        </td>
                        <td class="px-4 py-4">
                            @if($loteProximo)
                                <p class="text-sm font-semibold">{{ $loteProximo->fecha_vencimiento->locale('es')->isoFormat('D MMM YYYY') }}</p>
                                <p class="text-xs" style="color:#9a9a8a;">{{ number_format($loteProximo->cantidad_disponible) }} {{ $p->unidad }} disponibles</p>
                                @if($loteProximo->proximo_vencer)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                          style="background:#fef9c3; color:#854d0e;">⚠ Próx. vencer</span>
                                @elseif($loteProximo->vencido)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                          style="background:#fee2e2; color:#991b1b;">Vencido</span>
                                @endif
                            @else
                                <span style="color:#9a9a8a;">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                  style="background:{{ $bgBadge }}; color:{{ $colBadge }};">
                                <span class="w-1.5 h-1.5 rounded-full" style="background:{{ $colBadge }};"></span>
                                {{ $labelBadge }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('operativo.inventario.show', $p) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white transition hover:opacity-90"
                                   style="background:#39a900;">
                                    Ver lotes
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($productos->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t" style="border-color:#f3f0e8;">
            <p class="text-xs" style="color:#9a9a8a;">
                Mostrando {{ $productos->firstItem() }} a {{ $productos->lastItem() }} de {{ $productos->total() }} productos
            </p>
            <div>{{ $productos->links() }}</div>
        </div>
        @else
        <div class="px-6 py-4 border-t text-xs" style="border-color:#f3f0e8; color:#9a9a8a;">
            Mostrando {{ $productos->count() }} producto{{ $productos->count() != 1 ? 's' : '' }}
        </div>
        @endif
        @endif
    </div>
</div>

{{-- ══ MODAL NUEVO INGRESO ══════════════════════════════════ --}}
<div id="modalIngreso" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[95vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-7 py-5 border-b sticky top-0 bg-white z-10" style="border-color:#f3f0e8;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:#f0fdf4;">
                    <svg class="w-5 h-5" style="color:#39a900;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                    </svg>
                </div>
                <h2 class="text-lg font-extrabold" style="color:#1c2b16;">Nuevo Ingreso de Lote</h2>
            </div>
            <button onclick="cerrarModalIngreso()" class="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100" style="color:#9a9a8a;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('operativo.consignacion.store') }}" class="px-7 py-6 space-y-4"
              onsubmit="this.querySelector('button[type=submit]').disabled=true;">
            @csrf

            @if($errors->any())
            <div class="px-4 py-3 rounded-xl text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Producto <span style="color:#ef4444;">*</span></label>
                    <select name="producto_id" required
                            class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                            style="border-color:#e7e0cc; background:#fafafa;">
                        <option value="">Selecciona un producto...</option>
                        @foreach(\App\Models\Producto::where('activo', true)->orderBy('nombre')->get() as $prod)
                            <option value="{{ $prod->id }}" @selected(old('producto_id') == $prod->id)>
                                {{ $prod->nombre }} ({{ $prod->proveedor?->nombre ?? 'Sin proveedor' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Cantidad <span style="color:#ef4444;">*</span></label>
                    <input type="number" name="cantidad" min="1" required value="{{ old('cantidad') }}"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha entrada <span style="color:#ef4444;">*</span></label>
                    <input type="date" name="fecha_entrada" required value="{{ old('fecha_entrada', now()->toDateString()) }}"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Fecha vencimiento <span style="color:#9a9a8a;">(opcional)</span></label>
                    <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold mb-1.5" style="color:#1c2b16;">Notas</label>
                    <input type="text" name="notas" placeholder="Ej: factura #123, proveedor X..."
                           value="{{ old('notas') }}"
                           class="w-full px-4 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t" style="border-color:#f3f0e8;">
                <button type="submit"
                        class="flex-1 py-3 rounded-xl font-bold text-sm text-white hover:opacity-90"
                        style="background:#39a900;">Registrar ingreso</button>
                <button type="button" onclick="cerrarModalIngreso()"
                        class="flex-1 py-3 rounded-xl font-bold text-sm border hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalIngreso() {
    const m = document.getElementById('modalIngreso');
    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function cerrarModalIngreso() {
    const m = document.getElementById('modalIngreso');
    m.classList.add('hidden');
    m.classList.remove('flex');
    document.body.style.overflow = '';
}
document.getElementById('modalIngreso').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalIngreso();
});

@if($errors->any())
    document.addEventListener('DOMContentLoaded', () => abrirModalIngreso());
@endif
</script>

</x-operativo-layout>
