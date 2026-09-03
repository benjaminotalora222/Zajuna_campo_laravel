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
    .kpi { flex:1; border:1.5px solid #d4d4d4; border-radius:8px; padding:14px 16px; background:#fff; }
    .kpi .lbl { font-size:8px; font-weight:bold; text-transform:uppercase; color:#888; letter-spacing:0.5px; margin-bottom:6px; }
    .kpi .val       { font-size:22px; font-weight:bold; color:#39a900; }
    .kpi .val.warn  { color:#d97706; }
    .kpi .val.purple{ color:#71277a; }
    .kpi .val.dark  { color:#1c2b16; }

    .section-title { font-size:13px; font-weight:bold; color:#1c2b16;
                     border-left:4px solid #39a900; padding-left:9px; margin-bottom:12px; }

    /* Chips por proyecto */
    .chips { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:22px; }
    .chip { border:1.5px solid #d4d4d4; border-radius:8px; padding:8px 14px; background:#fff; min-width:90px; }
    .chip .chip-lbl { font-size:8px; color:#888; margin-bottom:3px; }
    .chip .chip-val { font-size:13px; font-weight:bold; color:#39a900; }
    .chip .chip-sub { font-size:8px; color:#aaa; margin-top:2px; }

    table { width:100%; border-collapse:collapse; margin-bottom:22px; }
    th { text-align:left; padding:8px 10px; font-size:8.5px; text-transform:uppercase;
         color:#888; font-weight:bold; letter-spacing:0.4px; border-bottom:1.5px solid #e5e5e5; }
    td { padding:9px 10px; font-size:9.5px; border-bottom:1px solid #f0f0f0; vertical-align:top; }
    .td-right  { text-align:right; }
    .td-center { text-align:center; }

    .badge { display:inline-block; padding:2px 8px; border-radius:12px; font-size:8px; font-weight:bold; }
    .en_ejecucion { background:#fffbeb; color:#d97706; }
    .finalizado   { background:#f0fdf4; color:#166534; }
    .pendiente    { background:#f3e8ff; color:#71277a; }
    .pausado      { background:#f3f4f6; color:#6b7280; }

    .bar-wrap { background:#e5e5e5; border-radius:3px; height:6px; width:70px; display:inline-block; vertical-align:middle; }
    .bar-fill  { border-radius:3px; height:6px; display:block; }

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

<div class="report-title">Reporte de Ejecución de Actividades</div>
<div class="report-sub">Período: {{ $inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} — {{ $fin->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; {{ ucfirst($periodo) }}</div>

<div class="kpi-row">
    <div class="kpi">
        <div class="lbl">Total actividades</div>
        <div class="val dark">{{ $stats['total'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">En ejecución</div>
        <div class="val warn">{{ $stats['en_ejecucion'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Finalizadas</div>
        <div class="val">{{ $stats['finalizado'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Pendientes</div>
        <div class="val purple">{{ $stats['pendiente'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Tasa de cierre</div>
        <div class="val">{{ $stats['total'] > 0 ? round(($stats['finalizado'] / $stats['total']) * 100) : 0 }}%</div>
    </div>
</div>

{{-- Por proyecto --}}
<div class="section-title">Por Proyecto</div>
<div class="chips">
    @foreach($actividades->groupBy(fn($a) => $a->proyecto->nombre ?? 'Sin proyecto') as $proj => $acts)
    <div class="chip">
        <div class="chip-lbl">{{ Str::limit($proj, 22) }}</div>
        <div class="chip-val">{{ $acts->count() }} act.</div>
        <div class="chip-sub">
            ✓ {{ $acts->where('estado','finalizado')->count() }}
            &nbsp;● {{ $acts->where('estado','en_ejecucion')->count() }}
            &nbsp;○ {{ $acts->where('estado','pendiente')->count() }}
        </div>
    </div>
    @endforeach
</div>

<div class="section-title">Detalle de Actividades</div>
<table>
    <thead>
        <tr>
            <th>Actividad</th>
            <th>Tipo</th>
            <th>Proyecto</th>
            <th>Responsable</th>
            <th>Progreso</th>
            <th>Estado</th>
            <th>Fecha límite</th>
        </tr>
    </thead>
    <tbody>
    @forelse($actividades as $act)
    @php
        $prog = $act->progreso ?? 0;
        $barColor = $act->estado === 'finalizado' ? '#39a900' : ($act->estado === 'en_ejecucion' ? '#d97706' : '#c4b5fd');
    @endphp
    <tr>
        <td>
            <strong>{{ $act->tema }}</strong>
            @if($act->descripcion)
            <br><span style="font-size:8px; color:#aaa;">{{ Str::limit($act->descripcion, 50) }}</span>
            @endif
            @if($act->espacio_fisico)
            <br><span style="font-size:8px; color:#aaa;">📍 {{ $act->espacio_fisico }}</span>
            @endif
        </td>
        <td style="font-size:9px; color:#888;">{{ $act->tipo ?: '—' }}</td>
        <td style="font-size:9px;">{{ $act->proyecto->nombre ?? '—' }}</td>
        <td style="font-size:9px;">{{ $act->responsableUser->name ?? $act->responsable ?? '—' }}</td>
        <td>
            <div class="bar-wrap"><div class="bar-fill" style="width:{{ $prog }}%; background:{{ $barColor }};"></div></div>
            <span style="font-size:8px; color:#555; margin-left:4px;">{{ $prog }}%</span>
        </td>
        <td><span class="badge {{ $act->estado }}">{{ ucfirst(str_replace('_',' ',$act->estado)) }}</span></td>
        <td style="white-space:nowrap; font-size:9px;">
            {{ $act->fecha_limite ? $act->fecha_limite->format('d/m/Y') : '—' }}
            @if($act->fecha_limite && $act->estado !== 'finalizado' && $act->fecha_limite->isPast())
            <br><span style="color:#ef4444; font-size:8px;">Vencida</span>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="7" style="text-align:center; color:#aaa; padding:20px;">Sin actividades en este período</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    <span>Zajuna Campo · Sistema de Gestión Agropecuaria</span>
    <span>Reporte generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</span>
</div>
</body>
</html>
