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
    .badge { padding:2px 7px; border-radius:10px; font-size:9px; font-weight:bold; }
    .completada  { background:#f0fdf4; color:#166534; }
    .en_progreso { background:#fffbeb; color:#d97706; }
    .por_iniciar { background:#faf5ff; color:#71277a; }
    .p-alta  { background:#fef2f2; color:#ef4444; }
    .p-media { background:#fffbeb; color:#d97706; }
    .p-baja  { background:#f0fdf4; color:#39a900; }
    .footer { margin-top:20px; padding-top:10px; border-top:1px solid #e7e0cc; font-size:9px; color:#9a9a8a; text-align:center; }
</style>
</head>
<body>

<div class="header">
    <h1>Reporte de Tareas y Avances — Zajuna Campo</h1>
    <p>{{ $inicio->locale('es')->isoFormat('D MMM YYYY') }} al {{ $fin->locale('es')->isoFormat('D MMM YYYY') }} · {{ ucfirst($periodo) }} · Generado: {{ now()->format('d/m/Y H:i') }}</p>
</div>

<div class="stats">
    <div class="stat"><div class="val">{{ $stats['total'] }}</div><div class="lbl">Total</div></div>
    <div class="stat"><div class="val">{{ $stats['completada'] }}</div><div class="lbl">Completadas</div></div>
    <div class="stat"><div class="val">{{ $stats['en_progreso'] }}</div><div class="lbl">En Progreso</div></div>
    <div class="stat"><div class="val">{{ $stats['por_iniciar'] }}</div><div class="lbl">Por Iniciar</div></div>
</div>

<h2>Tareas del Período</h2>
<table>
    <thead><tr>
        <th>Tarea</th><th>Proyecto</th><th>Estado</th><th>Prioridad</th><th>Responsable</th><th>Fecha Entrega</th>
    </tr></thead>
    <tbody>
    @forelse($tareas as $t)
    <tr>
        <td><strong>{{ $t->nombre }}</strong><br><span style="color:#9a9a8a;font-size:9px;">{{ Str::limit($t->descripcion ?? '', 50) }}</span></td>
        <td>{{ $t->proyecto->nombre ?? '—' }}</td>
        <td><span class="badge {{ $t->estado }}">{{ ucfirst(str_replace('_',' ',$t->estado)) }}</span></td>
        <td><span class="badge p-{{ $t->prioridad }}">{{ ucfirst($t->prioridad) }}</span></td>
        <td>{{ $t->responsable->name ?? '—' }}</td>
        <td>{{ $t->fecha_entrega ? $t->fecha_entrega->format('d/m/Y') : '—' }}</td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center; color:#9a9a8a; padding:20px;">Sin tareas en este período</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">Zajuna Campo · Módulo de Tareas y Avances · {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
