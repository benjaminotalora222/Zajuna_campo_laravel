<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family: DejaVu Sans, sans-serif; }
    body { font-size:11px; color:#1c2b16; background:white; padding:20px; }
    .header { background:#39a900; color:white; padding:18px 20px; border-radius:8px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; }
    .header h1 { font-size:18px; font-weight:bold; }
    .header p { font-size:10px; opacity:0.85; margin-top:3px; }
    .badge { background:rgba(255,255,255,0.2); padding:4px 10px; border-radius:20px; font-size:10px; font-weight:bold; }
    .stats { display:flex; gap:12px; margin-bottom:20px; }
    .stat { flex:1; border:1px solid #e7e0cc; border-radius:8px; padding:12px; text-align:center; }
    .stat .val { font-size:20px; font-weight:bold; color:#39a900; }
    .stat .lbl { font-size:9px; color:#9a9a8a; margin-top:2px; text-transform:uppercase; }
    h2 { font-size:13px; font-weight:bold; margin-bottom:10px; color:#1c2b16; border-bottom:2px solid #39a900; padding-bottom:4px; }
    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    thead tr { background:#f0fdf4; }
    th { text-align:left; padding:7px 8px; font-size:9px; text-transform:uppercase; color:#39a900; font-weight:bold; }
    td { padding:7px 8px; border-bottom:1px solid #f3f0e8; font-size:10px; }
    tr:last-child td { border-bottom:none; }
    .total-row td { font-weight:bold; background:#f0fdf4; }
    .footer { margin-top:20px; padding-top:10px; border-top:1px solid #e7e0cc; font-size:9px; color:#9a9a8a; text-align:center; }
</style>
</head>
<body>

<div class="header">
    <div>
        <h1>Reporte de Ventas — Zajuna Campo</h1>
        <p>{{ $inicio->locale('es')->isoFormat('D MMM YYYY') }} al {{ $fin->locale('es')->isoFormat('D MMM YYYY') }}</p>
    </div>
    <div>
        <div class="badge">{{ ucfirst($periodo) }}</div>
        <p style="font-size:9px; opacity:0.7; margin-top:4px; text-align:right;">Generado: {{ now()->locale('es')->isoFormat('D MMM YYYY, HH:mm') }}</p>
    </div>
</div>

<div class="stats">
    <div class="stat">
        <div class="val">${{ number_format($totalIngresos, 0, ',', '.') }}</div>
        <div class="lbl">Ingresos Totales</div>
    </div>
    <div class="stat">
        <div class="val">{{ $totalOrdenes }}</div>
        <div class="lbl">Órdenes de Venta</div>
    </div>
    <div class="stat">
        <div class="val">${{ $totalOrdenes > 0 ? number_format($totalIngresos / $totalOrdenes, 0, ',', '.') : '0' }}</div>
        <div class="lbl">Promedio por Venta</div>
    </div>
</div>

@if($topProductos->count())
<h2>Top Productos del Período</h2>
<table>
    <thead><tr>
        <th>#</th><th>Producto</th><th>Unidades</th><th>Total Ventas</th><th>Participación</th>
    </tr></thead>
    <tbody>
    @php $totalTop = $topProductos->sum('total_ventas') ?: 1; @endphp
    @foreach($topProductos as $i => $tp)
    <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $tp->nombre }}</td>
        <td>{{ number_format($tp->unidades) }}</td>
        <td>${{ number_format($tp->total_ventas, 0, ',', '.') }}</td>
        <td>{{ round(($tp->total_ventas / $totalTop) * 100, 1) }}%</td>
    </tr>
    @endforeach
    </tbody>
</table>
@endif

<h2>Detalle de Ventas</h2>
<table>
    <thead><tr>
        <th>#</th><th>Fecha</th><th>Cliente</th><th>Ítems</th><th>Total</th>
    </tr></thead>
    <tbody>
    @forelse($ventas as $v)
    <tr>
        <td>{{ $v->id }}</td>
        <td>{{ $v->fechaVenta ? \Carbon\Carbon::parse($v->fechaVenta)->format('d/m/Y') : '—' }}</td>
        <td>{{ $v->cliente ?: 'Sin nombre' }}</td>
        <td>{{ $v->items->count() }}</td>
        <td>${{ number_format($v->total, 0, ',', '.') }}</td>
    </tr>
    @empty
    <tr><td colspan="5" style="text-align:center; color:#9a9a8a; padding:20px;">Sin ventas en este período</td></tr>
    @endforelse
    @if($ventas->count())
    <tr class="total-row">
        <td colspan="4" style="text-align:right;">TOTAL</td>
        <td>${{ number_format($ventas->sum('total'), 0, ',', '.') }}</td>
    </tr>
    @endif
    </tbody>
</table>

<div class="footer">Zajuna Campo · Módulo de Ventas · {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
