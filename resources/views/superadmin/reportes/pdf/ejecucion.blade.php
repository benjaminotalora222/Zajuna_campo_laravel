<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family: DejaVu Sans, sans-serif; }
    body { font-size:11px; color:#1c2b16; padding:20px; }
    .header { background:#39a900; color:white; padding:18px 20px; border-radius:8px; margin-bottom:20px; }
    .header h1 { font-size:18px; font-weight:bold; }
    .header p { font-size:10px; opacity:0.85; margin-top:3px; }
    .stats { display:flex; gap:12px; margin-bottom:20px; }
    .stat { flex:1; border:1px solid #e7e0cc; border-radius:8px; padding:12px; text-align:center; }
    .stat .val { font-size:20px; font-weight:bold; color:#39a900; }
    .stat .lbl { font-size:9px; color:#9a9a8a; margin-top:2px; text-transform:uppercase; }
    h2 { font-size:13px; font-weight:bold; margin-bottom:10px; color:#1c2b16; border-bottom:2px solid #39a900; padding-bottom:4px; }
    table { width:100%; border-collapse:collapse; margin-bottom:20px; }
    thead tr { background:#f0fdf4; }
    th { text-align:left; padding:7px 8px; font-size:9px; text-transform:uppercase; color:#39a900; font-weight:bold; }
    td { padding:7px 8px; border-bottom:1px solid #f3f0e8; font-size:10px; }
    .progress-bar { background:#f3f0e8; border-radius:4px; height:6px; width:80px; display:inline-block; vertical-align:middle; }
    .progress-fill { background:#fdc300; border-radius:4px; height:6px; display:block; }
    .badge { padding:2px 7px; border-radius:10px; font-size:9px; font-weight:bold; }
    .en_ejecucion { background:#fffbeb; color:#d97706; }
    .finalizado   { background:#f0fdf4; color:#166534; }
    .pendiente    { background:#faf5ff; color:#71277a; }
    .pausado      { background:#f3f4f6; color:#6b7280; }
    .footer { margin-top:20px; padding-top:10px; border-top:1px solid #e7e0cc; font-size:9px; color:#9a9a8a; text-align:center; }
</style>
</head>
<body>

<div class="header">
    <h1>Reporte de Ejecución de Actividades — Zajuna Campo</h1>
    <p>{{ $inicio->locale('es')->isoFormat('D MMM YYYY') }} al {{ $fin->locale('es')->isoFormat('D MMM YYYY') }} · {{ ucfirst($periodo) }} · Generado: {{ now()->format('d/m/Y H:i') }}</p>
</div>

<div class="stats">
    <div class="stat"><div class="val">{{ $stats['total'] }}</div><div class="lbl">Total</div></div>
    <div class="stat"><div class="val">{{ $stats['en_ejecucion'] }}</div><div class="lbl">En Ejecución</div></div>
    <div class="stat"><div class="val">{{ $stats['finalizado'] }}</div><div class="lbl">Finalizadas</div></div>
    <div class="stat"><div class="val">{{ $stats['pendiente'] }}</div><div class="lbl">Pendientes</div></div>
</div>

<h2>Actividades del Período</h2>
<table>
    <thead><tr>
        <th>Actividad</th><th>Proyecto</th><th>Progreso</th><th>Estado</th><th>Responsable</th><th>Fecha Límite</th>
    </tr></thead>
    <tbody>
    @forelse($actividades as $act)
    <tr>
        <td><strong>{{ $act->tema }}</strong><br><span style="color:#9a9a8a;font-size:9px;">{{ Str::limit($act->descripcion ?? '', 50) }}</span></td>
        <td>{{ $act->proyecto->nombre ?? '—' }}</td>
        <td>
            <div class="progress-bar"><div class="progress-fill" style="width:{{ $act->progreso ?? 0 }}%;"></div></div>
            <span style="font-size:9px; margin-left:4px;">{{ $act->progreso ?? 0 }}%</span>
        </td>
        <td><span class="badge {{ $act->estado }}">{{ ucfirst(str_replace('_',' ',$act->estado)) }}</span></td>
        <td>{{ $act->responsableUser->name ?? $act->responsable ?? '—' }}</td>
        <td>{{ $act->fecha_limite ? $act->fecha_limite->format('d/m/Y') : '—' }}</td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center; color:#9a9a8a; padding:20px;">Sin actividades en este período</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">Zajuna Campo · Módulo de Ejecución de Actividades · {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
