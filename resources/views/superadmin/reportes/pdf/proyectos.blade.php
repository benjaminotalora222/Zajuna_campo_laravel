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

    /* KPIs */
    .kpi-row { display:flex; gap:14px; margin-bottom:22px; }
    .kpi { flex:1; border:1.5px solid #d4d4d4; border-radius:8px; padding:14px 16px; background:#fff; }
    .kpi .lbl { font-size:8px; font-weight:bold; text-transform:uppercase; color:#888; letter-spacing:0.5px; margin-bottom:6px; }
    .kpi .val       { font-size:22px; font-weight:bold; color:#39a900; }
    .kpi .val.warn  { color:#d97706; }
    .kpi .val.purple{ color:#71277a; }
    .kpi .val.dark  { color:#1c2b16; }

    /* Sección */
    .section-title { font-size:13px; font-weight:bold; color:#1c2b16;
                     border-left:4px solid #39a900; padding-left:9px; margin-bottom:12px; }

    /* Tarjeta por proyecto */
    .project-card { border:1.5px solid #e5e5e5; border-radius:8px; padding:14px 16px;
                    margin-bottom:14px; background:#fff; }
    .project-card-header { display:flex; justify-content:space-between; align-items:flex-start;
                           margin-bottom:8px; }
    .project-name { font-size:12px; font-weight:bold; color:#1c2b16; }
    .project-dates { font-size:8.5px; color:#888; margin-top:2px; }

    /* Barra de progreso */
    .prog-label { font-size:8.5px; color:#555; margin-bottom:3px; }
    .bar-wrap  { background:#e5e5e5; border-radius:3px; height:7px; width:100%; display:block; }
    .bar-fill  { border-radius:3px; height:7px; display:block; background:#39a900; }

    /* Grilla de datos internos */
    .proj-meta { display:flex; gap:20px; margin-top:8px; flex-wrap:wrap; }
    .proj-meta-item .pm-lbl { font-size:8px; color:#aaa; text-transform:uppercase; letter-spacing:0.4px; }
    .proj-meta-item .pm-val { font-size:9.5px; font-weight:bold; color:#1c2b16; margin-top:1px; }

    /* Badges */
    .badge { display:inline-block; padding:2px 9px; border-radius:12px; font-size:8px; font-weight:bold; }
    .en_progreso  { background:#f0fdf4; color:#166534; }
    .finalizado   { background:#e0f2fe; color:#0369a1; }
    .pendiente    { background:#f3e8ff; color:#71277a; }
    .pausado      { background:#f3f4f6; color:#6b7280; }
    .cancelado    { background:#fef2f2; color:#ef4444; }

    /* Tabla resumen tareas */
    table { width:100%; border-collapse:collapse; margin-top:8px; }
    th { text-align:left; padding:5px 8px; font-size:8px; text-transform:uppercase; color:#888;
         font-weight:bold; letter-spacing:0.3px; border-bottom:1.5px solid #e5e5e5; }
    td { padding:6px 8px; font-size:9px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
    .td-right { text-align:right; }

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

<div class="report-title">Reporte de Proyectos de Investigación</div>
<div class="report-sub">Estado general de todos los proyectos · Generado el {{ now()->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }}</div>

{{-- KPIs --}}
<div class="kpi-row">
    <div class="kpi">
        <div class="lbl">Total proyectos</div>
        <div class="val dark">{{ $stats['total'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">En progreso</div>
        <div class="val">{{ $stats['en_progreso'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Finalizados</div>
        <div class="val warn">{{ $stats['finalizado'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Pendientes</div>
        <div class="val purple">{{ $stats['pendiente'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Avance promedio</div>
        <div class="val">{{ $stats['total'] > 0 ? round($proyectos->avg('porcentajeAvance') ?? 0) : 0 }}%</div>
    </div>
</div>

{{-- Detalle por proyecto --}}
<div class="section-title">Detalle de Proyectos</div>

@forelse($proyectos as $p)
@php
    $estadoClass = match($p->estado ?? 'pendiente') {
        'en_progreso' => 'en_progreso',
        'finalizado'  => 'finalizado',
        'pausado'     => 'pausado',
        'cancelado'   => 'cancelado',
        default       => 'pendiente',
    };
    $estadoLabel = ucfirst(str_replace('_', ' ', $p->estado ?? 'pendiente'));
    $avance      = $p->porcentajeAvance ?? 0;
    $tareasTotal = $p->tareas->count();
    $tareasComp  = $p->tareas->where('estado', 'completada')->count();
    $tareasEnP   = $p->tareas->where('estado', 'en_progreso')->count();
    $tareasPend  = $p->tareas->where('estado', 'por_iniciar')->count();
@endphp
<div class="project-card">
    <div class="project-card-header">
        <div>
            <div class="project-name">{{ $p->nombre }}</div>
            <div class="project-dates">
                @if($p->fecha_inicio) Inicio: {{ $p->fecha_inicio->format('d/m/Y') }} @endif
                @if($p->fecha_fin) &nbsp;·&nbsp; Fin: {{ $p->fecha_fin->format('d/m/Y') }} @endif
                @if($p->responsable) &nbsp;·&nbsp; Responsable: {{ $p->responsable->name }} @endif
            </div>
        </div>
        <span class="badge {{ $estadoClass }}">{{ $estadoLabel }}</span>
    </div>

    {{-- Descripción --}}
    @if($p->descripcion)
    <div style="font-size:9px; color:#555; margin-bottom:8px;">{{ Str::limit($p->descripcion, 120) }}</div>
    @endif

    {{-- Barra de avance --}}
    <div class="prog-label">Avance del proyecto: {{ $avance }}%</div>
    <div class="bar-wrap">
        <div class="bar-fill" style="width:{{ min($avance, 100) }}%;
            background:{{ $avance >= 75 ? '#39a900' : ($avance >= 40 ? '#d97706' : '#ef4444') }};"></div>
    </div>

    {{-- Datos meta --}}
    <div class="proj-meta">
        @if($p->instructores)
        <div class="proj-meta-item">
            <div class="pm-lbl">Instructores</div>
            <div class="pm-val">{{ Str::limit($p->instructores, 35) }}</div>
        </div>
        @endif
        <div class="proj-meta-item">
            <div class="pm-lbl">Tareas totales</div>
            <div class="pm-val">{{ $tareasTotal }}</div>
        </div>
        <div class="proj-meta-item">
            <div class="pm-lbl">Completadas</div>
            <div class="pm-val" style="color:#166534;">{{ $tareasComp }}</div>
        </div>
        <div class="proj-meta-item">
            <div class="pm-lbl">En progreso</div>
            <div class="pm-val" style="color:#d97706;">{{ $tareasEnP }}</div>
        </div>
        <div class="proj-meta-item">
            <div class="pm-lbl">Por iniciar</div>
            <div class="pm-val" style="color:#71277a;">{{ $tareasPend }}</div>
        </div>
    </div>

    {{-- Tareas del proyecto --}}
    @if($p->tareas->count())
    <table style="margin-top:10px;">
        <thead>
            <tr>
                <th>Tarea</th>
                <th>Responsable</th>
                <th>Estado</th>
                <th>Prioridad</th>
                <th class="td-right">Fecha entrega</th>
            </tr>
        </thead>
        <tbody>
        @foreach($p->tareas as $t)
        @php
            $tEstado = match($t->estado) {
                'completada'  => ['bg'=>'#f0fdf4','cl'=>'#166534'],
                'en_progreso' => ['bg'=>'#fffbeb','cl'=>'#d97706'],
                default       => ['bg'=>'#f3e8ff','cl'=>'#71277a'],
            };
            $tPrioridad = match($t->prioridad ?? 'baja') {
                'alta'  => ['bg'=>'#fef2f2','cl'=>'#ef4444'],
                'media' => ['bg'=>'#fffbeb','cl'=>'#d97706'],
                default => ['bg'=>'#f0fdf4','cl'=>'#166534'],
            };
        @endphp
        <tr>
            <td>{{ $t->nombre }}</td>
            <td>{{ $t->responsable->name ?? '—' }}</td>
            <td>
                <span style="display:inline-block; padding:1px 7px; border-radius:10px; font-size:7.5px; font-weight:bold;
                      background:{{ $tEstado['bg'] }}; color:{{ $tEstado['cl'] }};">
                    {{ ucfirst(str_replace('_',' ',$t->estado)) }}
                </span>
            </td>
            <td>
                <span style="display:inline-block; padding:1px 7px; border-radius:10px; font-size:7.5px; font-weight:bold;
                      background:{{ $tPrioridad['bg'] }}; color:{{ $tPrioridad['cl'] }};">
                    {{ ucfirst($t->prioridad ?? '—') }}
                </span>
            </td>
            <td class="td-right" style="white-space:nowrap;">
                {{ $t->fecha_entrega ? $t->fecha_entrega->format('d/m/Y') : '—' }}
                @if($t->fecha_entrega && $t->fecha_entrega->isPast() && $t->estado !== 'completada')
                <br><span style="color:#ef4444; font-size:7.5px;">Vencida</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>
@empty
<p style="text-align:center; color:#aaa; padding:20px;">Sin proyectos registrados</p>
@endforelse

<div class="footer">
    <span>Zajuna Campo · Sistema de Gestión Agropecuaria</span>
    <span>Reporte generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</span>
</div>
</body>
</html>
