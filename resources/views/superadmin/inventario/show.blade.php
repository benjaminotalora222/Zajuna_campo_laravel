@php $title = 'Inventario · ' . $producto->nombre; @endphp

<x-superadmin-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('superadmin.inventario.index') }}"
           class="text-sm font-semibold hover:underline" style="color:#71277a;">← Inventario</a>
        <span style="color:#9a9a8a;">/</span>
        <span class="text-sm font-semibold">{{ $producto->nombre }}</span>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl mb-6 text-sm font-semibold"
             style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="px-4 py-3 rounded-xl mb-6 text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- INFO PRODUCTO + STOCK --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold">{{ $producto->nombre }}</h1>
                <p class="text-sm mt-0.5" style="color:#5a5a4f;">
                    {{ $producto->categoria }} · {{ $producto->proveedor?->nombre ?? '—' }}
                    @if($producto->codigoBarras)
                        · <span class="font-mono">{{ $producto->codigoBarras }}</span>
                    @endif
                </p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-extrabold" style="color:#39a900;">
                    {{ number_format($producto->stock_calculado) }}
                    <span class="text-base font-normal" style="color:#9a9a8a;">{{ $producto->unidad }}</span>
                </p>
                <p class="text-xs" style="color:#9a9a8a;">Stock disponible total</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- REGISTRAR ENTRADA --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-extrabold mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#39a900;">+</span>
                Registrar entrada
            </h2>
            <form method="POST" action="{{ route('superadmin.inventario.entrada', $producto) }}">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-semibold mb-1">Cantidad *</label>
                        <input type="number" name="cantidad" min="1" required
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-semibold mb-1">Fecha entrada *</label>
                        <input type="date" name="fecha_entrada" value="{{ now()->toDateString() }}" required
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold mb-1">Fecha vencimiento <span style="color:#9a9a8a;">(opcional)</span></label>
                        <input type="date" name="fecha_vencimiento"
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold mb-1">Notas</label>
                        <input type="text" name="notas" placeholder="Ej: factura #123..."
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background:#39a900;">Registrar entrada</button>
            </form>
        </div>

        {{-- REGISTRAR SALIDA --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-extrabold mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#ef4444;">−</span>
                Registrar salida
                <span class="text-xs font-normal ml-1" style="color:#9a9a8a;">(FEFO automático)</span>
            </h2>
            <form method="POST" action="{{ route('superadmin.inventario.salida', $producto) }}">
                @csrf
                <div class="grid gap-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1">Cantidad *</label>
                        <input type="number" name="cantidad" min="1"
                               max="{{ $producto->stock_calculado }}" required
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                        <p class="text-xs mt-1" style="color:#9a9a8a;">
                            Disponible: {{ number_format($producto->stock_calculado) }} {{ $producto->unidad }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">Motivo</label>
                        <input type="text" name="motivo" placeholder="Ej: venta, consumo interno..."
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full py-2.5 rounded-xl text-sm font-bold text-white"
                        style="background:#ef4444;">Registrar salida</button>
            </form>
        </div>
    </div>

    {{-- LOTES --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-extrabold">Lotes activos</h2>
            <p class="text-xs mt-0.5" style="color:#9a9a8a;">Ordenados por fecha de vencimiento (FEFO)</p>
        </div>
        @if($lotes->isEmpty())
            <p class="px-6 py-8 text-sm text-center" style="color:#9a9a8a;">No hay lotes registrados.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#fafafa; border-bottom:1px solid #f0f0f0;">
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">#Lote</th>
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">Entrada</th>
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">Vencimiento</th>
                            <th class="text-right px-5 py-3 font-semibold" style="color:#5a5a4f;">Inicial</th>
                            <th class="text-right px-5 py-3 font-semibold" style="color:#5a5a4f;">Disponible</th>
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($lotes as $lote)
                            @php
                                $venc = null;
                                if ($lote->fecha_vencimiento) {
                                    if ($lote->vencido) $venc = ['bg:#fee2e2','color:#991b1b','Vencido'];
                                    elseif ($lote->proximo_vencer) $venc = ['bg:#fef9c3','color:#854d0e','Próx. vencer'];
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 font-mono text-xs" style="color:#9a9a8a;">#{{ $lote->id }}</td>
                                <td class="px-5 py-3">{{ $lote->fecha_entrada?->locale('es')->isoFormat('D MMM YYYY') }}</td>
                                <td class="px-5 py-3">
                                    {{ $lote->fecha_vencimiento?->locale('es')->isoFormat('D MMM YYYY') ?? '—' }}
                                    @if($venc)
                                        <span class="ml-1 inline-block px-2 py-0.5 rounded-full text-[10px] font-bold"
                                              style="background:{{ str_replace('bg:','',$venc[0]) }}; color:{{ str_replace('color:','',$venc[1]) }};">
                                            {{ $venc[2] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">{{ number_format($lote->cantidad_inicial) }}</td>
                                <td class="px-5 py-3 text-right font-bold"
                                    style="color:{{ $lote->cantidad_disponible > 0 ? '#39a900' : '#ef4444' }};">
                                    {{ number_format($lote->cantidad_disponible) }}
                                </td>
                                <td class="px-5 py-3">
                                    @if($lote->cantidad_disponible <= 0)
                                        <span class="text-xs font-semibold" style="color:#9a9a8a;">Agotado</span>
                                    @else
                                        <span class="text-xs font-semibold" style="color:#39a900;">Activo</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($lotes->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $lotes->links() }}</div>
            @endif
        @endif
    </div>

    {{-- MOVIMIENTOS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-extrabold">Historial de movimientos</h2>
        </div>
        @if($movimientos->isEmpty())
            <p class="px-6 py-8 text-sm text-center" style="color:#9a9a8a;">Sin movimientos registrados.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr style="background:#fafafa; border-bottom:1px solid #f0f0f0;">
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">Fecha</th>
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">Tipo</th>
                            <th class="text-right px-5 py-3 font-semibold" style="color:#5a5a4f;">Cantidad</th>
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">Lote</th>
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">Motivo</th>
                            <th class="text-left px-5 py-3 font-semibold" style="color:#5a5a4f;">Usuario</th>
                            <th class="text-center px-5 py-3 font-semibold" style="color:#5a5a4f;">Editar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($movimientos as $mov)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3 text-xs">
                                    {{ $mov->fechaMovimiento?->locale('es')->isoFormat('D MMM YYYY, HH:mm') ?? '—' }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold"
                                          style="{{ $mov->tipo === 'entrada' ? 'background:#dcfce7;color:#166534;' : 'background:#fee2e2;color:#991b1b;' }}">
                                        {{ ucfirst($mov->tipo) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right font-bold">{{ number_format($mov->cantidad) }}</td>
                                <td class="px-5 py-3 font-mono text-xs" style="color:#9a9a8a;">
                                    {{ $mov->lote_id ? '#'.$mov->lote_id : '—' }}
                                </td>
                                <td class="px-5 py-3" style="color:#5a5a4f;">{{ $mov->motivo ?: '—' }}</td>
                                <td class="px-5 py-3 text-xs">{{ $mov->usuario?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <button onclick="abrirEditarMov({{ $mov->id }}, {{ $mov->cantidad }}, '{{ addslashes($mov->motivo ?? '') }}')"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto hover:bg-gray-100"
                                            style="color:#71277a;" title="Editar movimiento">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($movimientos->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">{{ $movimientos->links() }}</div>
            @endif
        @endif
    </div>

</div>

{{-- MODAL EDITAR MOVIMIENTO --}}
<div id="modalEditarMov" class="fixed inset-0 z-50 hidden items-center justify-center p-4" style="background:rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" onclick="event.stopPropagation()">
        <h3 class="font-extrabold text-lg mb-4">Editar movimiento</h3>
        <form id="formEditarMov" method="POST">
            @csrf @method('PUT')
            <div class="grid gap-3">
                <div>
                    <label class="block text-xs font-semibold mb-1">Cantidad *</label>
                    <input type="number" name="cantidad" id="editMovCantidad" min="1" required
                           class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
                <div>
                    <label class="block text-xs font-semibold mb-1">Motivo</label>
                    <input type="text" name="motivo" id="editMovMotivo"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
            </div>
            <div class="flex gap-3 mt-5">
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white" style="background:#71277a;">
                    Guardar
                </button>
                <button type="button" onclick="document.getElementById('modalEditarMov').classList.add('hidden');document.getElementById('modalEditarMov').classList.remove('flex');"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold border hover:bg-gray-50"
                        style="border-color:#e7e0cc; color:#5a5a4f;">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirEditarMov(id, cantidad, motivo) {
    document.getElementById('formEditarMov').action = '/superadmin/inventario/movimiento/' + id;
    document.getElementById('editMovCantidad').value = cantidad;
    document.getElementById('editMovMotivo').value = motivo;
    const m = document.getElementById('modalEditarMov');
    m.classList.remove('hidden'); m.classList.add('flex');
}
document.getElementById('modalEditarMov').addEventListener('click', function(e) {
    if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); }
});
</script>

</x-superadmin-layout>
