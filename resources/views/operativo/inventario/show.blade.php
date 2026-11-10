@php
    $title = 'Inventario · ' . $producto->nombre;
    $stockTotal = $producto->stock_calculado;
    $stockVencido = $producto->stock_vencido;
    $prodJson = json_encode([
        'id' => $producto->id,
        'nombre' => $producto->nombre,
        'categoria' => $producto->categoria,
        'unidad' => $producto->unidad,
        'precio' => (float)$producto->precio,
        'precio_formateado' => '$' . number_format($producto->precio, 0, ',', '.'),
        'codigoBarras' => $producto->codigoBarras,
        'proveedor' => $producto->proveedor?->nombre ?? '',
        'stock_total' => $stockTotal,
        'stock_vencido' => $stockVencido,
    ]);
@endphp

<x-operativo-layout :title="$title">
<div class="px-8 py-8 max-w-screen-xl mx-auto" style="color:#1c2b16;">

    {{-- ENCABEZADO --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('operativo.inventario.index') }}"
           class="text-sm font-semibold hover:underline" style="color:#39a900;">← Inventario</a>
        <span style="color:#9a9a8a;">/</span>
        <span class="text-sm font-semibold">{{ $producto->nombre }}</span>
    </div>

    @if(session('success'))
        <div class="px-4 py-3 rounded-xl mb-6 text-sm font-semibold flex items-center justify-between"
             style="background:#f0fdf4; border:1px solid #86efac; color:#166534;">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="px-4 py-3 rounded-xl mb-6 text-sm" style="background:#fef2f2; border:1px solid #fca5a5; color:#991b1b;">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    {{-- ALERTA DE STOCK VENCIDO --}}
    @if($stockVencido > 0)
        <div class="px-5 py-4 rounded-2xl mb-6 bg-red-50 border border-red-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 animate-in fade-in">
            <div class="flex items-center gap-3 text-red-900">
                <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center text-red-700 text-lg flex-shrink-0">
                    ⚠️
                </div>
                <div>
                    <h4 class="font-extrabold text-sm text-red-900 leading-tight">Stock vencido detectado</h4>
                    <p class="text-xs text-red-700 mt-0.5">
                        Hay <strong>{{ number_format($stockVencido) }} {{ $producto->unidad }}</strong> en lotes vencidos que deben ser dados de baja del inventario.
                    </p>
                </div>
            </div>
            <button type="button" onclick="prepararSalidaVencida({{ $stockVencido }})"
                    class="px-4 py-2 rounded-xl text-xs font-extrabold text-white bg-red-600 hover:bg-red-700 shadow-xs transition whitespace-nowrap">
                Dar de baja vencidos
            </button>
        </div>
    @endif

    {{-- INFO PRODUCTO + STOCK + CÓDIGO DE BARRAS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-extrabold text-gray-900">{{ $producto->nombre }}</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">{{ $producto->categoria }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-y-1 gap-x-3 text-sm mt-1" style="color:#5a5a4f;">
                    <span>Proveedor: <strong>{{ $producto->proveedor?->nombre ?? '—' }}</strong></span>
                    <span>·</span>
                    @if($producto->codigoBarras)
                        <span class="flex items-center gap-1 font-mono text-xs bg-gray-100 px-2.5 py-0.5 rounded-md text-gray-800">
                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h2M4 10h1M4 14h2M4 18h1M9 6h1M9 10h2M9 14h1M9 18h2M14 6h2M14 10h1M14 14h2M14 18h1M19 6h1M19 10h2M19 14h1M19 18h2"/></svg>
                            {{ $producto->codigoBarras }}
                        </span>
                    @else
                        <span class="text-xs italic text-gray-400">Sin código de barras</span>
                    @endif
                </div>
            </div>

            <!-- Botones de acción del producto -->
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="abrirModalCodigoBarras({{ $prodJson }})"
                        class="px-4 py-2.5 rounded-xl border border-green-200 text-green-800 text-xs font-bold hover:bg-green-50 transition flex items-center gap-1.5 shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h10M3 4h18M3 20h18"/></svg>
                    <span>{{ $producto->codigoBarras ? 'Ver / Imprimir Etiqueta' : 'Generar Código de Barras' }}</span>
                </button>

                <div class="text-right pl-3 border-l border-gray-100">
                    <p class="text-3xl font-extrabold" style="color:#39a900;">
                        {{ number_format($stockTotal) }}
                        <span class="text-base font-normal" style="color:#9a9a8a;">{{ $producto->unidad }}</span>
                    </p>
                    <p class="text-xs" style="color:#9a9a8a;">Stock disponible total</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- REGISTRAR ENTRADA --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-extrabold mb-4 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#39a900;">+</span>
                Registrar entrada (Nuevo lote)
            </h2>
            <form method="POST" action="{{ route('operativo.inventario.entrada', $producto) }}">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-semibold mb-1">Cantidad *</label>
                        <input type="number" name="cantidad" min="1" max="9999999" required
                               onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                               oninput="this.value = this.value.replace(/\D/g, '')"
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                        <p class="text-[11px] mt-1 text-gray-400">Solo números enteros.</p>
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
                        <input type="text" name="notas" placeholder="Ej: Factura #123, proveedor..."
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                    </div>
                </div>
                <button type="submit" class="mt-4 w-full py-2.5 rounded-xl text-sm font-bold text-white shadow-xs"
                        style="background:#39a900;">Registrar entrada</button>
            </form>
        </div>

        {{-- REGISTRAR SALIDA --}}
        <div id="seccionRegistrarSalida" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-base font-extrabold mb-4 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-bold" style="background:#ef4444;">−</span>
                    Registrar salida
                </span>
                <span class="text-xs font-normal" style="color:#9a9a8a;">(FEFO automático o por lote)</span>
            </h2>
            <form method="POST" action="{{ route('operativo.inventario.salida', $producto) }}">
                @csrf
                <div class="grid gap-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Motivo -->
                        <div>
                            <label class="block text-xs font-semibold mb-1">Motivo de salida *</label>
                            <select name="motivo" id="showSalidaMotivo" required onchange="manejarCambioMotivoShow(this.value)"
                                    class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                                    style="border-color:#e7e0cc; background:#fafafa;">
                                <option value="venta" @selected(old('motivo') === 'venta')>Venta</option>
                                <option value="vencido" @selected(old('motivo') === 'vencido')>Producto Vencido / Caducado</option>
                                <option value="merma" @selected(old('motivo') === 'merma')>Merma / Daño</option>
                                <option value="consumo_interno" @selected(old('motivo') === 'consumo_interno')>Consumo Interno</option>
                                <option value="ajuste" @selected(old('motivo') === 'ajuste')>Ajuste de Inventario</option>
                                <option value="otro" @selected(old('motivo') === 'otro')>Otro motivo</option>
                            </select>
                        </div>

                        <!-- Lote específico opcional -->
                        <div>
                            <label class="block text-xs font-semibold mb-1">Lote a descargar</label>
                            <select name="lote_id" id="showSalidaLoteId"
                                    class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                                    style="border-color:#e7e0cc; background:#fafafa;">
                                <option value="">Automático FEFO (vencidos primero)</option>
                                @foreach($producto->lotes()->where('cantidad_disponible', '>', 0)->orderByRaw('ISNULL(fecha_vencimiento), fecha_vencimiento ASC')->get() as $lt)
                                    <option value="{{ $lt->id }}" data-disponible="{{ $lt->cantidad_disponible }}" @selected(old('lote_id') == $lt->id)>
                                        Lote #{{ $lt->id }} ({{ $lt->cantidad_disponible }} {{ $producto->unidad }})
                                        @if($lt->vencido) ⚠️ [VENCIDO] @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold">Cantidad a descargar *</label>
                            <span class="text-xs" style="color:#9a9a8a;">
                                Disponible total: {{ number_format($stockTotal) }} {{ $producto->unidad }}
                            </span>
                        </div>
                        <input type="number" name="cantidad" id="showSalidaCantidad" min="1"
                               max="{{ $stockTotal }}" value="{{ old('cantidad', 1) }}" required
                               onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                               oninput="this.value = this.value.replace(/\D/g, '')"
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                    </div>

                    <!-- Detalle / Observación -->
                    <div>
                        <label class="block text-xs font-semibold mb-1">Detalle / Observaciones <span class="font-normal text-gray-400">(opcional)</span></label>
                        <input type="text" name="motivo_detalle" id="showSalidaDetalle"
                               value="{{ old('motivo_detalle') }}"
                               placeholder="Ej: desecho por caducidad, merma en transporte, etc."
                               class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                               style="border-color:#e7e0cc; background:#fafafa;">
                    </div>
                </div>

                <button type="submit" id="btnShowRegistrarSalida" class="mt-4 w-full py-2.5 rounded-xl text-sm font-bold text-white shadow-xs transition"
                        style="background:#ef4444;">
                    Registrar salida
                </button>
            </form>
        </div>
    </div>

    {{-- LOTES ACTIVOS --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-base">Lotes del producto</h2>
                <p class="text-xs mt-0.5" style="color:#9a9a8a;">Trazabilidad por lote con fecha de ingreso y caducidad</p>
            </div>
            @if($stockVencido > 0)
                <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-3 py-1 rounded-full">
                    ⚠️ {{ number_format($stockVencido) }} {{ $producto->unidad }} vencidos
                </span>
            @endif
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
                            <th class="text-center px-5 py-3 font-semibold" style="color:#5a5a4f;">Acción</th>
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
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-5 py-3 font-mono text-xs text-gray-500 font-bold">#{{ $lote->id }}</td>
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
                                    style="color:{{ $lote->cantidad_disponible > 0 ? ($lote->vencido ? '#dc2626' : '#39a900') : '#9a9a8a' }};">
                                    {{ number_format($lote->cantidad_disponible) }}
                                </td>
                                <td class="px-5 py-3">
                                    @if($lote->cantidad_disponible <= 0)
                                        <span class="text-xs font-semibold text-gray-400">Agotado</span>
                                    @elseif($lote->vencido)
                                        <span class="text-xs font-extrabold text-red-600">Vencido</span>
                                    @else
                                        <span class="text-xs font-semibold text-green-700">Disponible</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($lote->cantidad_disponible > 0)
                                        @if($lote->vencido)
                                            <button type="button" onclick="abrirModalBajaLote({{ $lote->id }}, {{ $lote->cantidad_disponible }}, '{{ $lote->fecha_vencimiento?->format('d/m/Y') }}')"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-extrabold text-white bg-red-600 hover:bg-red-700 shadow-xs transition"
                                                    title="Dar de baja este lote vencido">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Dar de baja
                                            </button>
                                        @else
                                            <button type="button" onclick="seleccionarLoteParaSalida({{ $lote->id }}, {{ $lote->cantidad_disponible }})"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold border border-gray-200 text-gray-700 hover:bg-gray-100 transition">
                                                Salida
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
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
            <h2 class="font-extrabold text-base">Historial de movimientos</h2>
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
                                <td class="px-5 py-3 font-medium" style="color:#333;">
                                    {{ $mov->motivo ?: '—' }}
                                </td>
                                <td class="px-5 py-3 text-xs">{{ $mov->usuario?->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <button onclick="abrirEditarMov({{ $mov->id }}, {{ $mov->cantidad }}, '{{ addslashes($mov->motivo ?? '') }}')"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center mx-auto hover:bg-gray-100"
                                            style="color:#39a900;" title="Editar movimiento">
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

{{-- MODAL DAR DE BAJA LOTE VENCIDO --}}
<div id="modalBajaLote" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60 backdrop-blur-sm" onclick="cerrarModalBajaLote(event)">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 p-6 animate-in fade-in zoom-in-95" onclick="event.stopPropagation()">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center text-lg flex-shrink-0">
                ⚠️
            </div>
            <div>
                <h3 class="font-extrabold text-lg text-gray-900 leading-tight">Dar de baja lote vencido</h3>
                <p class="text-xs text-gray-500">Lote <span id="bajaLoteNum" class="font-bold font-mono">#0</span> · Venció el <span id="bajaLoteFechaVenc">--</span></p>
            </div>
        </div>

        <form id="formBajaLote" method="POST" action="">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Cantidad a dar de baja *</label>
                    <input type="number" name="cantidad" id="bajaLoteCantidad" min="1" required
                           onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
                           oninput="this.value = this.value.replace(/\D/g, '')"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm font-bold outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                    <p class="text-[11px] text-gray-400 mt-1">Disponible en este lote: <strong id="bajaLoteDisp">0</strong> {{ $producto->unidad }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Motivo / Observación</label>
                    <input type="text" name="motivo" id="bajaLoteMotivo"
                           value="Baja por vencimiento"
                           class="w-full px-3 py-2.5 rounded-xl border text-sm outline-none"
                           style="border-color:#e7e0cc; background:#fafafa;">
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 py-3 rounded-xl text-sm font-extrabold text-white bg-red-600 hover:bg-red-700 shadow-sm transition">
                    Confirmar baja
                </button>
                <button type="button" onclick="cerrarModalBajaLote()"
                        class="flex-1 py-3 rounded-xl text-sm font-bold border hover:bg-gray-50 transition"
                        style="border-color:#e7e0cc; color:#5a5a4f;">
                    Cancelar
                </button>
            </div>
        </form>
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
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white" style="background:#39a900;">
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

{{-- MODAL DE CÓDIGO DE BARRAS --}}
@include('inventario.partials.barcode-modal')

<script>
function abrirEditarMov(id, cantidad, motivo) {
    document.getElementById('formEditarMov').action = '/operativo/inventario/movimiento/' + id;
    document.getElementById('editMovCantidad').value = cantidad;
    document.getElementById('editMovMotivo').value = motivo;
    const m = document.getElementById('modalEditarMov');
    m.classList.remove('hidden'); m.classList.add('flex');
}
document.getElementById('modalEditarMov').addEventListener('click', function(e) {
    if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); }
});

function manejarCambioMotivoShow(motivo) {
    const btn = document.getElementById('btnShowRegistrarSalida');
    const detalle = document.getElementById('showSalidaDetalle');

    if (motivo === 'vencido') {
        btn.innerText = 'Dar de baja producto vencido';
        btn.style.background = '#dc2626';
        if (!detalle.value) detalle.value = 'Baja por caducidad';
    } else {
        btn.innerText = 'Registrar salida';
        btn.style.background = '#ef4444';
        if (detalle.value === 'Baja por caducidad') detalle.value = '';
    }
}

function prepararSalidaVencida(cantidadVencida) {
    const motivoSelect = document.getElementById('showSalidaMotivo');
    motivoSelect.value = 'vencido';
    manejarCambioMotivoShow('vencido');
    document.getElementById('showSalidaCantidad').value = cantidadVencida;
    document.getElementById('showSalidaDetalle').value = 'Baja total de lotes vencidos';
    
    document.getElementById('seccionRegistrarSalida').scrollIntoView({ behavior: 'smooth' });
}

function seleccionarLoteParaSalida(loteId, disponible) {
    document.getElementById('showSalidaLoteId').value = loteId;
    document.getElementById('showSalidaCantidad').value = Math.min(disponible, parseInt(document.getElementById('showSalidaCantidad').value) || 1);
    document.getElementById('seccionRegistrarSalida').scrollIntoView({ behavior: 'smooth' });
}

function abrirModalBajaLote(loteId, disponible, fechaVenc) {
    document.getElementById('formBajaLote').action = '/operativo/inventario/lotes/' + loteId + '/baja';
    document.getElementById('bajaLoteNum').innerText = '#' + loteId;
    document.getElementById('bajaLoteFechaVenc').innerText = fechaVenc || 'Desconocida';
    document.getElementById('bajaLoteDisp').innerText = disponible;
    const inputCant = document.getElementById('bajaLoteCantidad');
    inputCant.max = disponible;
    inputCant.value = disponible;

    const m = document.getElementById('modalBajaLote');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function cerrarModalBajaLote(e = null) {
    if (e && e.target.id !== 'modalBajaLote') return;
    const m = document.getElementById('modalBajaLote');
    m.classList.add('hidden');
    m.classList.remove('flex');
}
</script>

</x-operativo-layout>
