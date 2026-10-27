<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:10px; color:#1c2b16; background:#fff; padding:28px 32px; }

    .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
    .logo-area img { height:70px; }
    .meta { text-align:right; font-size:9px; color:#555; line-height:1.7; }
    .meta strong { color:#1c2b16; }
    .divider-top { border:none; border-top:2.5px solid #39a900; margin-bottom:20px; }

    .report-title { font-size:20px; font-weight:bold; color:#1c2b16; margin-bottom:3px; }
    .report-sub   { font-size:9px; color:#888; margin-bottom:20px; }

    .kpi-row { display:flex; gap:14px; margin-bottom:22px; }
    .kpi { flex:1; border:1.5px solid #d4d4d4; border-radius:8px; padding:12px 14px; background:#fff; }
    .kpi .lbl { font-size:8px; font-weight:bold; text-transform:uppercase; color:#888; letter-spacing:0.5px; margin-bottom:4px; }
    .kpi .val { font-size:20px; font-weight:bold; color:#39a900; }
    .kpi .val.dark { color:#1c2b16; }
    .kpi .val.purple { color:#71277a; }

    .section-title { font-size:13px; font-weight:bold; color:#1c2b16;
                     border-left:4px solid #39a900; padding-left:9px; margin-bottom:12px; }

    table { width:100%; border-collapse:collapse; margin-bottom:22px; }
    th { text-align:left; padding:8px 10px; font-size:8.5px; text-transform:uppercase;
         color:#888; font-weight:bold; letter-spacing:0.4px; border-bottom:1.5px solid #e5e5e5; }
    td { padding:8px 10px; font-size:9px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
    .td-right  { text-align:right; }
    .td-center { text-align:center; }
    .total-row td { font-weight:bold; border-top:1.5px solid #e5e5e5; border-bottom:none; padding-top:10px; }

    .badge { display:inline-block; padding:2px 7px; border-radius:10px; font-size:8px; font-weight:bold; }
    .badge-gold   { background:#fef3c7; color:#b45309; }
    .badge-silver { background:#f1f5f9; color:#475569; }
    .badge-bronze { background:#ffedd5; color:#c2410c; }
    .badge-gray   { background:#f3f4f6; color:#6b7280; }

    .footer { margin-top:24px; padding-top:10px; border-top:1px solid #e5e5e5;
              display:flex; justify-content:space-between; font-size:8px; color:#aaa; }
</style>
</head>
<body>

{{-- ══ ENCABEZADO ══ --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
    <tr>
        <td style="width:50%; vertical-align:middle; border:none; padding:0;">
            <img src="{{ $logoZajunaPath }}" alt="Zajuna Campo" style="height:48px; display:block;">
        </td>
        <td style="width:50%; text-align:right; vertical-align:middle; border:none; padding:0;">
            <table style="border-collapse:collapse; border:none; margin:0 0 0 auto;">
                <tr>
                    <td style="text-align:right; vertical-align:middle; border:none; padding:0 16px 0 0; font-size:9px; color:#555; line-height:1.7;">
                        <div><strong>Generado por:</strong> {{ $usuario->name }}</div>
                        <div><strong>Rol:</strong> Superadmin</div>
                        <div><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                    </td>
                    <td style="vertical-align:middle; border:none; padding:0 0 0 16px; border-left:1.5px solid #d4d4d4; width:65px;">
                        <img src="{{ $logoSenaPath ?? $logoPath }}" alt="SENA" style="height:62px; display:block;">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<hr class="divider-top">

{{-- ══ TÍTULO ══ --}}
<div class="report-title">Reporte de Productos Más Vendidos</div>
@if($inicio->isSameDay($fin))
<div class="report-sub">Fecha: {{ $inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; {{ $periodoTexto ?? ucfirst($periodo) }}</div>
@else
<div class="report-sub">Período: {{ $inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} — {{ $fin->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; {{ $periodoTexto ?? ucfirst($periodo) }}</div>
@endif

{{-- ══ KPIs ══ --}}
<div class="kpi-row">
    <div class="kpi">
        <div class="lbl">Unidades vendidas</div>
        <div class="val">{{ number_format($totalUnidades, 0, ',', '.') }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Ingresos totales</div>
        <div class="val dark">${{ number_format($totalIngresos, 0, ',', '.') }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Productos distintos</div>
        <div class="val purple">{{ $totalProductos }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Producto estrella</div>
        <div class="val dark" style="font-size:12px; line-height:1.3; margin-top:4px;">
            {{ $productoEstrella ? Str::limit($productoEstrella->nombre, 22) : '—' }}
            @if($productoEstrella)
            <div style="font-size:9px; font-weight:normal; color:#71277a;">({{ $productoEstrella->unidades }} uds.)</div>
            @endif
        </div>
    </div>
</div>

{{-- ══ TABLA DE RANKING ══ --}}
<div class="section-title">Ranking de Productos Más Vendidos</div>
<table>
    <thead>
        <tr>
            <th class="td-center" style="width:36px;">#</th>
            <th>Producto</th>
            <th>Categoría</th>
            <th class="td-right">Precio Ref.</th>
            <th class="td-center">Uds. Vendidas</th>
            <th class="td-center">Órdenes</th>
            <th class="td-right">Total Ingresos</th>
            <th class="td-right">Participación</th>
        </tr>
    </thead>
    <tbody>
    @php $totalBase = $totalIngresos > 0 ? $totalIngresos : 1; @endphp
    @forelse($productos as $idx => $p)
    @php
        $pct = round(($p->total_ventas / $totalBase) * 100, 1);
        $badgeClass = match($idx) {
            0 => 'badge-gold',
            1 => 'badge-silver',
            2 => 'badge-bronze',
            default => 'badge-gray',
        };
    @endphp
    <tr>
        <td class="td-center">
            <span class="badge {{ $badgeClass }}">
                {{ $idx === 0 ? '1°' : ($idx === 1 ? '2°' : ($idx === 2 ? '3°' : ($idx + 1) . '°')) }}
            </span>
        </td>
        <td>
            <strong>{{ $p->nombre }}</strong>
        </td>
        <td style="color:#666;">
            {{ $p->categoria ?? 'General' }}
        </td>
        <td class="td-right" style="color:#666;">
            ${{ number_format($p->precio ?? 0, 0, ',', '.') }}
        </td>
        <td class="td-center">
            <strong>{{ $p->unidades }}</strong>
        </td>
        <td class="td-center" style="color:#666;">
            {{ $p->total_ordenes }}
        </td>
        <td class="td-right" style="font-weight:bold; color:#39a900;">
            ${{ number_format($p->total_ventas, 0, ',', '.') }}
        </td>
        <td class="td-right" style="font-weight:bold; color:#71277a;">
            {{ $pct }}%
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="8" class="td-center" style="padding:24px; color:#999;">
            No se registraron ventas de productos en el período seleccionado.
        </td>
    </tr>
    @endforelse

    @if($productos->count() > 0)
    <tr class="total-row">
        <td colspan="4">TOTALES ACUMULADOS</td>
        <td class="td-center">{{ number_format($totalUnidades, 0, ',', '.') }}</td>
        <td class="td-center">—</td>
        <td class="td-right" style="color:#39a900;">${{ number_format($totalIngresos, 0, ',', '.') }}</td>
        <td class="td-right" style="color:#71277a;">100%</td>
    </tr>
    @endif
    </tbody>
</table>

{{-- ══ FOOTER ══ --}}
<table style="width:100%; border:none; margin-top:20px; font-size:8px; color:#aaa;">
    <tr>
        <td style="border:none; padding:0;">Zajuna Campo · Sistema de Gestión de Fincas y Unidades Productivas</td>
        <td style="border:none; padding:0; text-align:right;">Documento oficial generado el {{ now()->format('d/m/Y H:i:s') }}</td>
    </tr>
</table>

</body>
</html>
