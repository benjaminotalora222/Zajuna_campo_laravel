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

    .kpi-row { display:flex; gap:14px; margin-bottom:22px; }
    .kpi { flex:1; border:1.5px solid #d4d4d4; border-radius:8px; padding:14px 16px; background:#fff; }
    .kpi .lbl { font-size:8px; font-weight:bold; text-transform:uppercase; color:#888; letter-spacing:0.5px; margin-bottom:6px; }
    .kpi .val        { font-size:22px; font-weight:bold; color:#71277a; }
    .kpi .val.green  { color:#39a900; }
    .kpi .val.warn   { color:#d97706; }
    .kpi .val.danger { color:#ef4444; }
    .kpi .val.dark   { color:#1c2b16; }

    .section-title { font-size:13px; font-weight:bold; color:#1c2b16;
                     border-left:4px solid #71277a; padding-left:9px; margin-bottom:12px; }

    /* Alerta vencidas */
    .alert-box { border:1.5px solid #fecaca; border-radius:8px; background:#fef2f2;
                 padding:10px 14px; margin-bottom:22px; }
    .alert-title { font-size:10px; font-weight:bold; color:#dc2626; margin-bottom:6px; }
    .alert-row { font-size:8.5px; color:#7f1d1d; padding:3px 0; border-bottom:1px dashed #fecaca; }
    .alert-row:last-child { border-bottom:none; }

    /* Chips por proyecto */
    .chips { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:22px; }
    .chip { border:1.5px solid #d4d4d4; border-radius:8px; padding:8px 14px; background:#fff; min-width:90px; }
    .chip .chip-lbl { font-size:8px; color:#888; margin-bottom:3px; }
    .chip .chip-val { font-size:13px; font-weight:bold; color:#71277a; }
    .chip .chip-sub { font-size:8px; color:#aaa; margin-top:2px; }

    table { width:100%; border-collapse:collapse; margin-bottom:22px; }
    th { text-align:left; padding:8px 10px; font-size:8.5px; text-transform:uppercase;
         color:#888; font-weight:bold; letter-spacing:0.4px; border-bottom:1.5px solid #e5e5e5; }
    td { padding:9px 10px; font-size:9.5px; border-bottom:1px solid #f0f0f0; vertical-align:top; }
    .td-right  { text-align:right; }

    .badge { display:inline-block; padding:2px 8px; border-radius:12px; font-size:8px; font-weight:bold; }
    .completada  { background:#f0fdf4; color:#166534; }
    .en_progreso { background:#fffbeb; color:#d97706; }
    .por_iniciar { background:#f3e8ff; color:#71277a; }
    .cancelada   { background:#fef2f2; color:#ef4444; }
    .p-alta  { background:#fef2f2; color:#ef4444; }
    .p-media { background:#fffbeb; color:#d97706; }
    .p-baja  { background:#f0fdf4; color:#166534; }

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

<div class="report-title">Reporte de Tareas y Avances</div>
<div class="report-sub">Período: {{ $inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} — {{ $fin->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; {{ ucfirst($periodo) }}</div>

<div class="kpi-row">
    <div class="kpi">
        <div class="lbl">Total tareas</div>
        <div class="val dark">{{ $stats['total'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Completadas</div>
        <div class="val green">{{ $stats['completada'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">En progreso</div>
        <div class="val warn">{{ $stats['en_progreso'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Por iniciar</div>
        <div class="val">{{ $stats['por_iniciar'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">% Completado</div>
        <div class="val green">{{ $stats['total'] > 0 ? round(($stats['completada'] / $stats['total']) * 100) : 0 }}%</div>
    </div>
</div>

{{-- Tareas vencidas --}}
@php
    $vencidas = $tareas->filter(fn($t) => $t->fecha_entrega && $t->fecha_entrega->isPast() && $t->estado !== 'completada');
@endphp
@if($vencidas->count())
<div class="alert-box">
    <div class="alert-title">Tareas vencidas sin completar ({{ $vencidas->count() }})</div>
    @foreach($vencidas as $t)
    <div class="alert-row">
        <strong>{{ $t->nombre }}</strong> — {{ $t->proyecto->nombre ?? 'Sin proyecto' }}
        — Responsable: {{ $t->responsable->name ?? '—' }}
        — Venció: {{ $t->fecha_entrega->format('d/m/Y') }}
    </div>
    @endforeach
</div>
@endif

{{-- Por proyecto --}}
<div class="section-title">Por Proyecto</div>
<div class="chips">
    @foreach($tareas->groupBy(fn($t) => $t->proyecto->nombre ?? 'Sin proyecto') as $proj => $items)
    <div class="chip">
        <div class="chip-lbl">{{ Str::limit($proj, 22) }}</div>
        <div class="chip-val">{{ $items->count() }} tarea{{ $items->count() !== 1 ? 's' : '' }}</div>
        <div class="chip-sub">
            ✓ {{ $items->where('estado','completada')->count() }}
            &nbsp;● {{ $items->where('estado','en_progreso')->count() }}
            &nbsp;○ {{ $items->where('estado','por_iniciar')->count() }}
        </div>
    </div>
    @endforeach
</div>

<div class="section-title">Detalle de Tareas</div>
<table>
    <thead>
        <tr>
            <th>Tarea</th>
            <th>Proyecto</th>
            <th>Responsable</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Fecha entrega</th>
        </tr>
    </thead>
    <tbody>
    @forelse($tareas as $t)
    <tr>
        <td>
            <strong>{{ $t->nombre }}</strong>
            @if($t->descripcion)
            <br><span style="font-size:8px; color:#aaa;">{{ Str::limit($t->descripcion, 55) }}</span>
            @endif
        </td>
        <td style="font-size:9px;">{{ $t->proyecto->nombre ?? '—' }}</td>
        <td style="font-size:9px;">{{ $t->responsable->name ?? '—' }}</td>
        <td><span class="badge {{ $t->estado }}">{{ ucfirst(str_replace('_',' ',$t->estado)) }}</span></td>
        <td><span class="badge p-{{ $t->prioridad ?? 'baja' }}">{{ ucfirst($t->prioridad ?? '—') }}</span></td>
        <td style="white-space:nowrap; font-size:9px;">
            {{ $t->fecha_entrega ? $t->fecha_entrega->format('d/m/Y') : '—' }}
            @if($t->fecha_entrega && $t->fecha_entrega->isPast() && $t->estado !== 'completada')
            <br><span style="color:#ef4444; font-size:8px;">Vencida</span>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="6" style="text-align:center; color:#aaa; padding:20px;">Sin tareas en este período</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    <span>Zajuna Campo · Sistema de Gestión Agropecuaria</span>
    <span>Reporte generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</span>
</div>
</body>
</html>
