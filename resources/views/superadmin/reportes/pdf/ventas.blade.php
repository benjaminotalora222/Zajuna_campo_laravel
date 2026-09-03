<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:10px; color:#1c2b16; background:#fff; padding:28px 32px; }

    /* ── ENCABEZADO SUPERIOR ── */
    .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; }
    .logo-area img { height:70px; }
    .meta { text-align:right; font-size:9px; color:#555; line-height:1.7; }
    .meta strong { color:#1c2b16; }
    .divider-top { border:none; border-top:2.5px solid #39a900; margin-bottom:20px; }

    /* ── TÍTULO DE SECCIÓN ── */
    .report-title { font-size:20px; font-weight:bold; color:#1c2b16; margin-bottom:3px; }
    .report-sub   { font-size:9px; color:#888; margin-bottom:20px; }

    /* ── KPI CARDS ── */
    .kpi-row { display:flex; gap:14px; margin-bottom:22px; }
    .kpi { flex:1; border:1.5px solid #d4d4d4; border-radius:8px; padding:14px 16px; background:#fff; }
    .kpi .lbl { font-size:8px; font-weight:bold; text-transform:uppercase; color:#888; letter-spacing:0.5px; margin-bottom:6px; }
    .kpi .val { font-size:22px; font-weight:bold; color:#39a900; }
    .kpi .val.dark { color:#1c2b16; }

    /* ── SECCIÓN ── */
    .section-title { font-size:13px; font-weight:bold; color:#1c2b16;
                     border-left:4px solid #39a900; padding-left:9px; margin-bottom:12px; }

    /* ── CHIPS (método pago / categoría) ── */
    .chips { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:22px; }
    .chip { border:1.5px solid #d4d4d4; border-radius:8px; padding:8px 14px; background:#fff; min-width:80px; }
    .chip .chip-lbl { font-size:8px; color:#888; margin-bottom:3px; }
    .chip .chip-val { font-size:13px; font-weight:bold; color:#39a900; }

    /* ── TABLAS ── */
    table { width:100%; border-collapse:collapse; margin-bottom:22px; }
    th { text-align:left; padding:8px 10px; font-size:8.5px; text-transform:uppercase;
         color:#888; font-weight:bold; letter-spacing:0.4px; border-bottom:1.5px solid #e5e5e5; }
    td { padding:9px 10px; font-size:9.5px; border-bottom:1px solid #f0f0f0; vertical-align:top; }
    .td-right  { text-align:right; }
    .td-center { text-align:center; }
    .id-link  { font-weight:bold; color:#39a900; }
    .total-row td { font-weight:bold; border-top:1.5px solid #e5e5e5; border-bottom:none; padding-top:10px; }

    /* ── ITEMS SUB ── */
    .item-line { font-size:8.5px; color:#555; padding:1px 0; }

    /* ── BADGES ── */
    .badge { display:inline-block; padding:2px 8px; border-radius:12px; font-size:8px; font-weight:bold; }
    .badge-ok      { background:#f0fdf4; color:#166534; }
    .badge-warn    { background:#fffbeb; color:#d97706; }
    .badge-danger  { background:#fef2f2; color:#ef4444; }
    .badge-purple  { background:#f3e8ff; color:#71277a; }
    .badge-grey    { background:#f3f4f6; color:#6b7280; }

    /* ── FOOTER ── */
    .footer { margin-top:24px; padding-top:10px; border-top:1px solid #e5e5e5;
              display:flex; justify-content:space-between; font-size:8px; color:#aaa; }
</style>
</head>
<body>

{{-- ══ ENCABEZADO ══ --}}
<div class="top-bar">
    <div class="logo-area">
        <img src="{{ $logoPath }}" alt="Zajuna Campo">
    </div>
    <div class="meta">
        <div><strong>Generado por:</strong> {{ $usuario->name }}</div>
        <div><strong>Rol:</strong> Superadmin</div>
        <div><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</div>
    </div>
</div>
<hr class="divider-top">

{{-- ══ TÍTULO ══ --}}
<div class="report-title">Reporte de Ventas</div>
<div class="report-sub">Período: {{ $inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} — {{ $fin->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; {{ ucfirst($periodo) }}</div>

{{-- ══ KPIs ══ --}}
<div class="kpi-row">
    <div class="kpi">
        <div class="lbl">Ventas completadas</div>
        <div class="val dark">{{ $totalOrdenes }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Total ingresos</div>
        <div class="val">${{ number_format($totalIngresos, 0, ',', '.') }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Promedio por venta</div>
        <div class="val">${{ $totalOrdenes > 0 ? number_format($totalIngresos / $totalOrdenes, 0, ',', '.') : '0' }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Clientes distintos</div>
        <div class="val dark">{{ $ventas->whereNotNull('cliente')->unique('cliente')->count() }}</div>
    </div>
</div>

{{-- ══ TOP PRODUCTOS ══ --}}
@if($topProductos->count())
<div class="section-title">Productos más vendidos</div>
<div class="chips">
    @foreach($topProductos->take(6) as $tp)
    <div class="chip">
        <div class="chip-lbl">{{ Str::limit($tp->nombre, 20) }}</div>
        <div class="chip-val">${{ number_format($tp->total_ventas, 0, ',', '.') }}</div>
    </div>
    @endforeach
</div>
@endif

{{-- ══ DETALLE ══ --}}
<div class="section-title">Detalle de Ventas</div>
<table>
    <thead>
        <tr>
            <th># Venta</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Atendido por</th>
            <th>Productos</th>
            <th class="td-right">Total</th>
        </tr>
    </thead>
    <tbody>
    @forelse($ventas as $v)
    <tr>
        <td><span class="id-link">#{{ str_pad($v->id, 4, '0', STR_PAD_LEFT) }}</span></td>
        <td style="white-space:nowrap;">
            {{ $v->fechaVenta ? $v->fechaVenta->format('d/m/Y') : '—' }}
            <br><span style="color:#aaa; font-size:8px;">{{ $v->fechaVenta ? $v->fechaVenta->format('H:i') : '' }}</span>
        </td>
        <td>{{ $v->cliente ?: '—' }}</td>
        <td>{{ $v->usuario->name ?? '—' }}</td>
        <td>
            @foreach($v->items as $item)
            <div class="item-line">
                {{ $item->producto->nombre ?? 'Producto eliminado' }} × {{ $item->cantidad }}
                <span style="float:right; color:#39a900;">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </td>
        <td class="td-right" style="font-weight:bold;">${{ number_format($v->total, 0, ',', '.') }}</td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center; color:#aaa; padding:20px;">Sin ventas en este período</td></tr>
    @endforelse
    @if($ventas->count())
    <tr class="total-row">
        <td colspan="5" class="td-right">TOTAL</td>
        <td class="td-right" style="color:#39a900;">${{ number_format($totalIngresos, 0, ',', '.') }}</td>
    </tr>
    @endif
    </tbody>
</table>

<div class="footer">
    <span>Zajuna Campo · Sistema de Gestión Agropecuaria</span>
    <span>Reporte generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</span>
</div>
</body>
</html>
