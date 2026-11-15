<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura POS #{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 4mm 4mm;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px;
            color: #111;
            line-height: 1.25;
            padding: 4px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        .dashed-line {
            border-top: 1px dashed #555;
            margin: 5px 0;
        }
        .double-line {
            border-top: 2px dashed #111;
            margin: 6px 0;
        }

        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
        }
        table.items-table th {
            font-size: 7.5px;
            text-transform: uppercase;
            border-bottom: 1px dashed #777;
            padding: 3px 1px;
            color: #333;
        }
        table.items-table td {
            font-size: 8px;
            padding: 2.5px 1px;
            vertical-align: top;
        }

        .table-totales {
            width: 100%;
            border-collapse: collapse;
        }
        .table-totales td {
            padding: 1.5px 0;
            font-size: 8.5px;
        }

        .logo-img {
            max-height: 28px;
            max-width: 70px;
            display: inline-block;
            margin: 0 4px 4px 4px;
        }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="text-center">
        @if($logoZajuna || $logoSena)
        <div style="margin-bottom: 3px;">
            @if($logoZajuna)
            <img src="{{ $logoZajuna }}" class="logo-img">
            @endif
            @if($logoSena)
            <img src="{{ $logoSena }}" class="logo-img">
            @endif
        </div>
        @endif

        <div style="font-size: 11px; font-weight: bold; color: #1c2b16;">{{ $empresa['nombre'] }}</div>
        <div style="font-size: 8px; color: #444;">{{ $empresa['subtitulo'] }}</div>
        <div style="font-size: 8px; color: #444;">{{ $empresa['nit'] }}</div>
        <div style="font-size: 7.5px; color: #555;">{{ $empresa['direccion'] }}</div>
        <div style="font-size: 7.5px; color: #555;">{{ $empresa['telefono'] }} - {{ $empresa['ciudad'] }}</div>
        <div style="font-size: 7px; color: #777; margin-top: 2px;">{{ $empresa['regimen'] }}</div>
    </div>

    <div class="double-line"></div>

    {{-- Metadatos de la Venta --}}
    <table style="width: 100%; font-size: 8px;">
        <tr>
            <td class="font-bold">FACTURA POS:</td>
            <td class="text-right font-bold" style="font-size: 9.5px; color: #71277a;">#{{ str_pad($venta->id, 6, '0', STR_PAD_LEFT) }}</td>
        </tr>
        <tr>
            <td style="color:#555;">Fecha y Hora:</td>
            <td class="text-right">
                {{ \Carbon\Carbon::parse($venta->fechaVenta)->format('d/m/Y') }}
                {{ $venta->created_at ? \Carbon\Carbon::parse($venta->created_at)->format('H:i') : '' }}
            </td>
        </tr>
        <tr>
            <td style="color:#555;">Cajero / Atiende:</td>
            <td class="text-right">{{ Str::limit($venta->usuario->name ?? 'Usuario', 20) }}</td>
        </tr>
        <tr>
            <td style="color:#555;">Cliente:</td>
            <td class="text-right font-bold">{{ Str::limit($venta->cliente ?: 'Consumidor Final', 20) }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    {{-- Tabla de Productos --}}
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 12%;" class="text-center">Cant</th>
                <th style="width: 48%;" class="text-left">Producto</th>
                <th style="width: 18%;" class="text-right">Precio</th>
                <th style="width: 22%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->items as $item)
            <tr>
                <td class="text-center font-bold">{{ $item->cantidad }}</td>
                <td class="text-left">
                    {{ $item->producto->nombre ?? 'Producto #' . $item->idProducto }}
                    @if(isset($item->porcentaje_iva) && $item->porcentaje_iva > 0)
                        <br><span style="font-size: 6.5px; color: #71277a;">IVA: {{ round($item->porcentaje_iva) }}% (${{ number_format($item->valor_iva, 0, ',', '.') }})</span>
                    @endif
                </td>
                <td class="text-right">${{ number_format($item->precioUnitario, 0, ',', '.') }}</td>
                <td class="text-right font-bold">${{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="dashed-line"></div>

    {{-- Totales --}}
    @php
        $subtotalBase = $venta->subtotal ?? ($venta->total - ($venta->iva ?? 0));
        $ivaTotal     = $venta->iva ?? 0;
    @endphp
    <table class="table-totales">
        <tr>
            <td style="color:#555;">SUBTOTAL (Sin IVA):</td>
            <td class="text-right font-bold">${{ number_format($subtotalBase, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="color:#71277a;">IVA TOTAL:</td>
            <td class="text-right font-bold" style="color:#71277a;">+${{ number_format($ivaTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"><div class="double-line"></div></td>
        </tr>
        <tr>
            <td style="font-size: 10px; font-weight: bold; text-transform: uppercase;">TOTAL A PAGAR:</td>
            <td class="text-right font-bold" style="font-size: 11px; color: #166534;">${{ number_format($venta->total, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="2"><div class="double-line"></div></td>
        </tr>
        <tr>
            <td style="font-size: 7.5px; color:#555;">Total artículos:</td>
            <td class="text-right" style="font-size: 7.5px;">{{ $venta->items->sum('cantidad') }} uds.</td>
        </tr>
        <tr>
            <td style="font-size: 7.5px; color:#555;">Forma de pago:</td>
            <td class="text-right font-bold" style="font-size: 7.5px;">Contado / Efectivo</td>
        </tr>
        <tr>
            <td style="font-size: 7.5px; color:#555;">Estado:</td>
            <td class="text-right font-bold" style="font-size: 7.5px; color:#166534;">PAGADO</td>
        </tr>
    </table>

    @if($venta->observaciones)
    <div class="dashed-line"></div>
    <div style="font-size: 7.5px; color: #555;">
        <strong>Observaciones:</strong> {{ $venta->observaciones }}
    </div>
    @endif

    <div class="dashed-line"></div>

    {{-- Pie --}}
    <div class="text-center" style="font-size: 7.5px; color: #555; margin-top: 4px;">
        <div style="font-weight: bold; font-size: 8.5px; color: #111;">¡GRACIAS POR SU COMPRA!</div>
        <div>Apoyando el campo y la formación SENA</div>
        <div style="font-size: 6.5px; color: #888; margin-top: 2px;">Documento de control interno POS · Zajuna Campo</div>
    </div>

</body>
</html>

