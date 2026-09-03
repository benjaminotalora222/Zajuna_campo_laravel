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
    .divider-top { border:none; border-top:2.5px solid #71277a; margin-bottom:20px; }

    .report-title { font-size:20px; font-weight:bold; color:#1c2b16; margin-bottom:3px; }
    .report-sub   { font-size:9px; color:#888; margin-bottom:20px; }

    /* KPIs */
    .kpi-row { display:flex; gap:14px; margin-bottom:22px; }
    .kpi { flex:1; border:1.5px solid #d4d4d4; border-radius:8px; padding:14px 16px; background:#fff; }
    .kpi .lbl { font-size:8px; font-weight:bold; text-transform:uppercase; color:#888; letter-spacing:0.5px; margin-bottom:6px; }
    .kpi .val        { font-size:22px; font-weight:bold; color:#71277a; }
    .kpi .val.green  { color:#39a900; }
    .kpi .val.warn   { color:#d97706; }
    .kpi .val.danger { color:#ef4444; }
    .kpi .val.dark   { color:#1c2b16; }

    /* Sección */
    .section-title { font-size:13px; font-weight:bold; color:#1c2b16;
                     border-left:4px solid #71277a; padding-left:9px; margin-bottom:12px; }

    /* Alerta */
    .alert-box { border:1.5px solid #fecaca; border-radius:8px; background:#fef2f2;
                 padding:10px 14px; margin-bottom:22px; }
    .alert-title { font-size:10px; font-weight:bold; color:#dc2626; margin-bottom:6px; }
    .alert-row { font-size:8.5px; color:#7f1d1d; padding:3px 0; border-bottom:1px dashed #fecaca; }
    .alert-row:last-child { border-bottom:none; }

    /* Chips ubicaciones */
    .chips { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:22px; }
    .chip { border:1.5px solid #d4d4d4; border-radius:8px; padding:8px 14px; background:#fff; }
    .chip .chip-lbl { font-size:8px; color:#888; margin-bottom:3px; }
    .chip .chip-val { font-size:13px; font-weight:bold; color:#71277a; }

    /* Tablas */
    table { width:100%; border-collapse:collapse; margin-bottom:22px; }
    th { text-align:left; padding:8px 10px; font-size:8.5px; text-transform:uppercase;
         color:#888; font-weight:bold; letter-spacing:0.4px; border-bottom:1.5px solid #e5e5e5; }
    td { padding:9px 10px; font-size:9.5px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
    .td-right  { text-align:right; }
    .td-center { text-align:center; }

    /* Badges */
    .badge { display:inline-block; padding:2px 8px; border-radius:12px; font-size:8px; font-weight:bold; }
    .badge-ok      { background:#f0fdf4; color:#166534; }
    .badge-bajo    { background:#fffbeb; color:#d97706; }
    .badge-agotado { background:#fef2f2; color:#ef4444; }
    .badge-inactivo{ background:#f3f4f6; color:#6b7280; }

    /* Barra stock */
    .bar-wrap { background:#e5e5e5; border-radius:3px; height:5px; width:55px; display:inline-block; vertical-align:middle; }
    .bar-fill  { border-radius:3px; height:5px; display:block; }

    .footer { margin-top:24px; padding-top:10px; border-top:1px solid #e5e5e5;
              display:flex; justify-content:space-between; font-size:8px; color:#aaa; }
</style>
</head>
<body>

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

<div class="report-title">Reporte de Inventario</div>
<div class="report-sub">Estado actual del inventario · Generado el {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }}</div>

{{-- KPIs --}}
<div class="kpi-row">
    <div class="kpi">
        <div class="lbl">Registros inventario</div>
        <div class="val dark">{{ $stats['total_items'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Productos totales</div>
        <div class="val">{{ $stats['total_prod'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Stock bajo</div>
        <div class="val warn">{{ $stats['bajo'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Agotados</div>
        <div class="val danger">{{ $stats['agotado'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Productos activos</div>
        <div class="val green">{{ $productos->where('activo', true)->count() }}</div>
    </div>
</div>

{{-- Alertas --}}
@php
    $criticos = $productos->filter(fn($p) => $p->stockActual !== null && $p->stockActual <= 0);
    $bajos    = $productos->filter(fn($p) => $p->stockMinimo && $p->stockActual > 0 && $p->stockActual <= $p->stockMinimo);
@endphp
@if($criticos->count() || $bajos->count())
<div class="alert-box">
    <div class="alert-title">Productos que requieren atención</div>
    @foreach($criticos as $p)
    <div class="alert-row"><strong>AGOTADO</strong> — {{ $p->nombre }} (stock: {{ $p->stockActual }} {{ $p->unidad }})</div>
    @endforeach
    @foreach($bajos as $p)
    <div class="alert-row"><strong>STOCK BAJO</strong> — {{ $p->nombre }} (actual: {{ $p->stockActual }} / mín: {{ $p->stockMinimo }} {{ $p->unidad }})</div>
    @endforeach
</div>
@endif

{{-- Por ubicación --}}
{{-- La tabla de inventario no tiene datos de movimientos aún --}}

{{-- Resumen basado en productos --}}
<div class="section-title">Estado del Stock por Producto</div>
<table>
    <thead>
        <tr>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Cód. Barras</th>
            <th class="td-right">Stock actual</th>
            <th class="td-right">Stock mín.</th>
            <th>Nivel</th>
            <th>Estado</th>
            <th>Precio</th>
        </tr>
    </thead>
    <tbody>
    @forelse($productos as $p)
    @php
        $est   = $p->stockActual <= 0 ? 'agotado' : ($p->stockMinimo && $p->stockActual <= $p->stockMinimo ? 'bajo' : 'ok');
        $label = match($est) { 'agotado' => 'Agotado', 'bajo' => 'Stock bajo', default => 'Disponible' };
        $pct   = ($p->stockMinimo && $p->stockMinimo > 0) ? min(round(($p->stockActual / ($p->stockMinimo * 2)) * 100), 100) : 100;
        $barColor = match($est) { 'agotado' => '#ef4444', 'bajo' => '#f59e0b', default => '#71277a' };
    @endphp
    <tr>
        <td>
            <strong>{{ $p->nombre }}</strong>
            @if($p->descripcion)
            <br><span style="font-size:8px; color:#aaa;">{{ Str::limit($p->descripcion, 40) }}</span>
            @endif
        </td>
        <td style="color:#888; font-size:9px;">{{ $p->categoria ?: '—' }}</td>
        <td style="font-size:8.5px; color:#888;">{{ $p->codigoBarras ?: '—' }}</td>
        <td class="td-right" style="font-weight:bold;">
            {{ $p->stockActual ?? 0 }}
            <span style="font-size:8px; color:#aaa;">{{ $p->unidad }}</span>
        </td>
        <td class="td-right" style="color:#888;">{{ $p->stockMinimo ?? '—' }}</td>
        <td>
            <div class="bar-wrap"><div class="bar-fill" style="width:{{ $pct }}%; background:{{ $barColor }};"></div></div>
        </td>
        <td>
            @if(!$p->activo)
                <span class="badge badge-inactivo">Inactivo</span>
            @else
                <span class="badge badge-{{ $est }}">{{ $label }}</span>
            @endif
        </td>
        <td class="td-right">${{ number_format($p->precio ?? 0, 0, ',', '.') }}</td>
    </tr>
    @empty
    <tr><td colspan="8" style="text-align:center; color:#aaa; padding:18px;">Sin productos registrados</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    <span>Zajuna Campo · Sistema de Gestión Agropecuaria</span>
    <span>Reporte generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</span>
</div>
</body>
</html>
