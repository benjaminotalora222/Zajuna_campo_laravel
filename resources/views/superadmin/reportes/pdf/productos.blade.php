<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family: DejaVu Sans, sans-serif; }
    body { font-size:11px; color:#1c2b16; padding:20px; }
    .header { background:#71277a; color:white; padding:18px 20px; border-radius:8px; margin-bottom:20px; }
    .header h1 { font-size:18px; font-weight:bold; }
    .header p { font-size:10px; opacity:0.85; margin-top:3px; }
    .stats { display:flex; gap:12px; margin-bottom:20px; }
    .stat { flex:1; border:1px solid #e7e0cc; border-radius:8px; padding:12px; text-align:center; }
    .stat .val { font-size:20px; font-weight:bold; color:#71277a; }
    .stat .lbl { font-size:9px; color:#9a9a8a; margin-top:2px; text-transform:uppercase; }
    h2 { font-size:13px; font-weight:bold; margin-bottom:10px; color:#1c2b16; border-bottom:2px solid #71277a; padding-bottom:4px; }
    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    thead tr { background:#faf5ff; }
    th { text-align:left; padding:7px 8px; font-size:9px; text-transform:uppercase; color:#71277a; font-weight:bold; }
    td { padding:7px 8px; border-bottom:1px solid #f3f0e8; font-size:10px; }
    .badge-ok { background:#f0fdf4; color:#166534; padding:2px 7px; border-radius:10px; font-size:9px; font-weight:bold; }
    .badge-bajo { background:#fffbeb; color:#d97706; padding:2px 7px; border-radius:10px; font-size:9px; font-weight:bold; }
    .badge-agotado { background:#fef2f2; color:#ef4444; padding:2px 7px; border-radius:10px; font-size:9px; font-weight:bold; }
    .footer { margin-top:20px; padding-top:10px; border-top:1px solid #e7e0cc; font-size:9px; color:#9a9a8a; text-align:center; }
</style>
</head>
<body>

<div class="header">
    <h1>Reporte de Productos — Zajuna Campo</h1>
    <p>Generado: {{ now()->locale('es')->isoFormat('D MMM YYYY, HH:mm') }}</p>
</div>

<div class="stats">
    <div class="stat"><div class="val">{{ $stats['total'] }}</div><div class="lbl">Total</div></div>
    <div class="stat"><div class="val">{{ $stats['activos'] }}</div><div class="lbl">Activos</div></div>
    <div class="stat"><div class="val">{{ $stats['bajo'] }}</div><div class="lbl">Stock Bajo</div></div>
    <div class="stat"><div class="val">{{ $stats['agotado'] }}</div><div class="lbl">Agotados</div></div>
</div>

<h2>Listado de Productos</h2>
<table>
    <thead><tr>
        <th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Stock Mín.</th><th>Estado</th>
    </tr></thead>
    <tbody>
    @forelse($productos as $p)
    @php
        $estado = $p->stockActual <= 0 ? 'agotado' : ($p->stockMinimo && $p->stockActual <= $p->stockMinimo ? 'bajo' : 'ok');
        $label  = $estado === 'agotado' ? 'Agotado' : ($estado === 'bajo' ? 'Stock bajo' : 'Disponible');
    @endphp
    <tr>
        <td>{{ $p->nombre }}</td>
        <td>{{ $p->categoria ?: '—' }}</td>
        <td>${{ number_format($p->precio ?? 0, 0, ',', '.') }}</td>
        <td>{{ $p->stockActual ?? 0 }} {{ $p->unidad }}</td>
        <td>{{ $p->stockMinimo ?? '—' }}</td>
        <td><span class="badge-{{ $estado }}">{{ $label }}</span></td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center; color:#9a9a8a; padding:20px;">Sin productos</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">Zajuna Campo · Módulo de Productos · {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
