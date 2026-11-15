<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura POS #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }} - {{ $empresa['nombre'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                background: #fff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .ticket-container {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 4px 6px !important;
                width: 100% !important;
                max-width: 80mm !important;
            }
            @page {
                size: 80mm auto;
                margin: 2mm 3mm;
            }
        }

        .ticket-font {
            font-family: 'Courier New', Courier, monospace, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .dashed-line {
            border-top: 1px dashed #6b7280;
            margin: 8px 0;
        }

        .double-dashed-line {
            border-top: 2px dashed #374151;
            margin: 10px 0;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8 text-gray-900 font-sans antialiased">

    {{-- BARRA DE ACCIONES (SOLO PANTALLA) --}}
    <div class="no-print max-w-md mx-auto mb-6 px-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <div>
                    <h1 class="text-sm font-extrabold text-gray-900">Factura POS #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</h1>
                    <p class="text-[11px] text-gray-500">Ticket de venta térmica (80mm)</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white shadow-sm transition hover:opacity-90 cursor-pointer"
                        style="background:#39a900;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    Imprimir Ticket
                </button>

                <a href="{{ request()->fullUrlWithQuery(['pdf' => 1]) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition border border-purple-200 text-purple-700 bg-purple-50 hover:bg-purple-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Descargar PDF
                </a>

                @php
                    $volverUrl = auth()->user()->role_id === 1 ? route('superadmin.ventas.index') : route('operativo.ventas.index');
                @endphp
                <a href="{{ $volverUrl }}"
                   class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition"
                   title="Volver al listado de ventas">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- TICKET TÉRMICO POS --}}
    <div class="ticket-container bg-white mx-auto max-w-[320px] p-5 rounded-2xl shadow-md border border-gray-200 ticket-font text-[12px] leading-tight">

        {{-- Encabezado de la Factura --}}
        <div class="text-center pb-2">
            @if($logoZajuna || $logoSena)
            <div class="flex items-center justify-center gap-3 mb-2">
                @if($logoZajuna)
                <img src="{{ $logoZajuna }}" alt="Zajuna Campo" class="h-8 max-w-[90px] object-contain">
                @endif
                @if($logoSena)
                <img src="{{ $logoSena }}" alt="SENA" class="h-8 max-w-[45px] object-contain">
                @endif
            </div>
            @endif

            <h2 class="text-base font-extrabold tracking-tight uppercase" style="color:#1c2b16;">{{ $empresa['nombre'] }}</h2>
            <p class="text-[11px] font-semibold text-gray-600">{{ $empresa['subtitulo'] }}</p>
            <p class="text-[11px] text-gray-500">{{ $empresa['nit'] }}</p>
            <p class="text-[10px] text-gray-500">{{ $empresa['direccion'] }}</p>
            <p class="text-[10px] text-gray-500">{{ $empresa['telefono'] }}</p>
            <p class="text-[10px] text-gray-500">{{ $empresa['ciudad'] }}</p>
            <p class="text-[9px] uppercase tracking-wider text-gray-400 mt-1">{{ $empresa['regimen'] }}</p>
        </div>

        <div class="double-dashed-line"></div>

        {{-- Datos de la Venta --}}
        <div class="space-y-1 text-[11px]">
            <div class="flex justify-between items-center">
                <span class="font-bold text-gray-800">FACTURA POS:</span>
                <span class="font-extrabold text-sm" style="color:#71277a;">#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Fecha y Hora:</span>
                <span class="font-semibold text-gray-800">
                    {{ \Carbon\Carbon::parse($venta->fechaVenta)->format('d/m/Y') }}
                    {{ $venta->created_at ? \Carbon\Carbon::parse($venta->created_at)->format('H:i') : now()->format('H:i') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Atendido por:</span>
                <span class="font-semibold text-gray-800 truncate max-w-[170px] text-right">{{ $venta->usuario->name ?? 'Usuario del Sistema' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Cliente:</span>
                <span class="font-semibold text-gray-800 truncate max-w-[170px] text-right">{{ $venta->cliente ?: 'Consumidor Final' }}</span>
            </div>
        </div>

        <div class="dashed-line"></div>

        {{-- Cabecera de la tabla de productos --}}
        <div class="grid grid-cols-12 gap-1 font-bold text-[10px] uppercase text-gray-600 pb-1 border-b border-gray-300">
            <div class="col-span-2 text-center">Cant</div>
            <div class="col-span-5">Descripción</div>
            <div class="col-span-2 text-right">Precio</div>
            <div class="col-span-3 text-right">Total</div>
        </div>

        {{-- Lista de Ítems --}}
        <div class="py-1 space-y-1.5 text-[11px]">
            @foreach($venta->items as $item)
            <div>
                <div class="grid grid-cols-12 gap-1 items-start">
                    <div class="col-span-2 text-center font-bold text-gray-800">
                        {{ $item->cantidad }}
                    </div>
                    <div class="col-span-5 font-semibold text-gray-900 leading-tight">
                        {{ $item->producto->nombre ?? 'Producto #' . $item->idProducto }}
                        @if(!empty($item->producto->unidad))
                            <span class="text-[9px] text-gray-500 font-normal">({{ $item->producto->unidad }})</span>
                        @endif
                    </div>
                    <div class="col-span-2 text-right text-gray-600">
                        ${{ number_format($item->precioUnitario, 0, ',', '.') }}
                    </div>
                    <div class="col-span-3 text-right font-bold text-gray-900">
                        ${{ number_format($item->subtotal, 0, ',', '.') }}
                    </div>
                </div>

                {{-- Detalle de IVA por ítem si aplica --}}
                @if(isset($item->porcentaje_iva) && $item->porcentaje_iva > 0)
                <div class="text-[9px] text-purple-700 pl-6 flex justify-between">
                    <span>+ IVA {{ round($item->porcentaje_iva) }}%</span>
                    <span>${{ number_format($item->valor_iva, 0, ',', '.') }}</span>
                </div>
                @else
                <div class="text-[9px] text-gray-400 pl-6">
                    <span>IVA 0% (Exento)</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="dashed-line"></div>

        {{-- Totales y Liquidación --}}
        @php
            $subtotalBase = $venta->subtotal ?? ($venta->total - ($venta->iva ?? 0));
            $ivaTotal     = $venta->iva ?? 0;
            $totalVenta   = $venta->total;
        @endphp
        <div class="space-y-1 text-[11px]">
            <div class="flex justify-between text-gray-600">
                <span>SUBTOTAL (Base sin IVA):</span>
                <span class="font-semibold text-gray-800">${{ number_format($subtotalBase, 0, ',', '.') }}</span>
            </div>

            <div class="flex justify-between text-purple-700 font-medium">
                <span>IVA TOTAL (Impuesto):</span>
                <span class="font-semibold">+${{ number_format($ivaTotal, 0, ',', '.') }}</span>
            </div>

            <div class="double-dashed-line my-1.5"></div>

            <div class="flex justify-between items-center text-sm font-extrabold text-gray-900 py-1">
                <span class="text-xs uppercase tracking-wider">TOTAL A PAGAR:</span>
                <span class="text-base" style="color:#166534;">${{ number_format($totalVenta, 0, ',', '.') }}</span>
            </div>

            <div class="double-dashed-line my-1.5"></div>

            <div class="flex justify-between text-[10px] text-gray-500 pt-0.5">
                <span>Total de artículos:</span>
                <span class="font-bold text-gray-700">{{ $venta->items->sum('cantidad') }} {{ $venta->items->sum('cantidad') == 1 ? 'unidad' : 'unidades' }}</span>
            </div>

            <div class="flex justify-between text-[10px] text-gray-500">
                <span>Forma de pago:</span>
                <span class="font-bold text-gray-700">Contado / Efectivo</span>
            </div>

            <div class="flex justify-between text-[10px] text-gray-500">
                <span>Estado de pago:</span>
                <span class="font-bold text-emerald-700">PAGADO</span>
            </div>
        </div>

        @if($venta->observaciones)
        <div class="dashed-line"></div>
        <div class="text-[10px] text-gray-600">
            <span class="font-bold text-gray-700">Observaciones:</span>
            <p class="italic mt-0.5 leading-tight">{{ $venta->observaciones }}</p>
        </div>
        @endif

        {{-- Pie del Ticket POS --}}
        <div class="dashed-line"></div>

        <div class="text-center pt-1 pb-2 space-y-1 text-gray-500">
            {{-- Código de barras simulado para estilo POS --}}
            <div class="py-1">
                <svg class="mx-auto h-8 w-44" viewBox="0 0 160 30" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0" y="0" width="3" height="26" fill="#1f2937"/>
                    <rect x="5" y="0" width="1" height="26" fill="#1f2937"/>
                    <rect x="8" y="0" width="4" height="26" fill="#1f2937"/>
                    <rect x="14" y="0" width="2" height="26" fill="#1f2937"/>
                    <rect x="18" y="0" width="1" height="26" fill="#1f2937"/>
                    <rect x="22" y="0" width="3" height="26" fill="#1f2937"/>
                    <rect x="28" y="0" width="2" height="26" fill="#1f2937"/>
                    <rect x="33" y="0" width="4" height="26" fill="#1f2937"/>
                    <rect x="39" y="0" width="1" height="26" fill="#1f2937"/>
                    <rect x="42" y="0" width="3" height="26" fill="#1f2937"/>
                    <rect x="48" y="0" width="2" height="26" fill="#1f2937"/>
                    <rect x="53" y="0" width="4" height="26" fill="#1f2937"/>
                    <rect x="60" y="0" width="1" height="26" fill="#1f2937"/>
                    <rect x="64" y="0" width="3" height="26" fill="#1f2937"/>
                    <rect x="70" y="0" width="2" height="26" fill="#1f2937"/>
                    <rect x="74" y="0" width="4" height="26" fill="#1f2937"/>
                    <rect x="80" y="0" width="1" height="26" fill="#1f2937"/>
                    <rect x="84" y="0" width="3" height="26" fill="#1f2937"/>
                    <rect x="90" y="0" width="2" height="26" fill="#1f2937"/>
                    <rect x="94" y="0" width="4" height="26" fill="#1f2937"/>
                    <rect x="101" y="0" width="1" height="26" fill="#1f2937"/>
                    <rect x="105" y="0" width="3" height="26" fill="#1f2937"/>
                    <rect x="111" y="0" width="2" height="26" fill="#1f2937"/>
                    <rect x="115" y="0" width="4" height="26" fill="#1f2937"/>
                    <rect x="122" y="0" width="1" height="26" fill="#1f2937"/>
                    <rect x="126" y="0" width="3" height="26" fill="#1f2937"/>
                    <rect x="132" y="0" width="2" height="26" fill="#1f2937"/>
                    <rect x="136" y="0" width="4" height="26" fill="#1f2937"/>
                    <rect x="143" y="0" width="1" height="26" fill="#1f2937"/>
                    <rect x="147" y="0" width="3" height="26" fill="#1f2937"/>
                    <rect x="153" y="0" width="2" height="26" fill="#1f2937"/>
                    <rect x="157" y="0" width="3" height="26" fill="#1f2937"/>
                </svg>
                <p class="text-[9px] tracking-widest text-gray-500 font-mono">*{{ str_pad($venta->id, 8, '0', STR_PAD_LEFT) }}*</p>
            </div>

            <p class="text-[11px] font-extrabold text-gray-800">¡GRACIAS POR SU COMPRA!</p>
            <p class="text-[10px]">Apoyando el campo y la producción agropecuaria</p>
            <p class="text-[9px] text-gray-400">Software: Zajuna Campo · SENA</p>
        </div>

    </div>

    {{-- Script de impresión automática opcional con ?autoprint=1 --}}
    @if(request()->has('autoprint'))
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 300);
        });
    </script>
    @endif

</body>
</html>

