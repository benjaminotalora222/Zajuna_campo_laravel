<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size:10px; color:#1c2b16; background:#fff; padding:28px 32px; }

    .divider-top { border:none; border-top:2.5px solid #39a900; margin-bottom:18px; }

    .report-title { font-size:19px; font-weight:bold; color:#1c2b16; margin-bottom:3px; }
    .report-sub   { font-size:9px; color:#777; margin-bottom:18px; }

    /* KPI CARDS */
    .kpi-row { display:flex; gap:10px; margin-bottom:18px; }
    .kpi { flex:1; border:1.5px solid #d4d4d4; border-radius:8px; padding:10px 12px; background:#fff; }
    .kpi .lbl { font-size:7.5px; font-weight:bold; text-transform:uppercase; color:#888; letter-spacing:0.5px; margin-bottom:4px; }
    .kpi .val { font-size:18px; font-weight:bold; color:#1c2b16; }
    .kpi .val.green  { color:#39a900; }
    .kpi .val.orange { color:#d97706; }
    .kpi .val.purple { color:#71277a; }
    .kpi .val.blue   { color:#2563eb; }

    .section-title { font-size:12px; font-weight:bold; color:#1c2b16;
                     border-left:4px solid #71277a; padding-left:8px; margin-bottom:10px; margin-top:6px; }

    /* Chips */
    .chips { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:18px; }
    .chip { border:1px solid #d4d4d4; border-radius:6px; padding:6px 10px; background:#fafafa; min-width:80px; }
    .chip .chip-lbl { font-size:7.5px; color:#888; margin-bottom:2px; }
    .chip .chip-val { font-size:11px; font-weight:bold; color:#71277a; }

    /* Tablas */
    table { width:100%; border-collapse:collapse; margin-bottom:18px; }
    th { text-align:left; padding:7px 8px; font-size:8px; text-transform:uppercase;
         color:#888; font-weight:bold; letter-spacing:0.3px; border-bottom:1.5px solid #e5e5e5; }
    td { padding:7px 8px; font-size:8.5px; border-bottom:1px solid #f0f0f0; vertical-align:top; }
    .td-center { text-align:center; }
    .td-right  { text-align:right; }

    .badge { display:inline-block; padding:2px 6px; border-radius:10px; font-size:7.5px; font-weight:bold; }
    .badge-finalizado   { background:#f0fdf4; color:#166534; }
    .badge-en_ejecucion { background:#fffbeb; color:#d97706; }
    .badge-pendiente    { background:#faf5ff; color:#71277a; }
    .badge-tipo         { background:#f3f4f6; color:#374151; }

    .bar-wrap { background:#e5e5e5; border-radius:3px; height:5px; width:55px; display:inline-block; vertical-align:middle; }
    .bar-fill  { border-radius:3px; height:5px; display:block; }

    .footer { margin-top:20px; padding-top:8px; border-top:1px solid #e5e5e5;
              display:flex; justify-content:space-between; font-size:8px; color:#aaa; }
</style>
</head>
<body>

{{-- ══ ENCABEZADO ══ --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:6px;">
    <tr>
        <td style="width:50%; vertical-align:middle; border:none; padding:0;">
            <img src="{{ $logoZajunaPath }}" alt="Zajuna Campo" style="height:46px; display:block;">
        </td>
        <td style="width:50%; text-align:right; vertical-align:middle; border:none; padding:0;">
            <table style="border-collapse:collapse; border:none; margin:0 0 0 auto;">
                <tr>
                    <td style="text-align:right; vertical-align:middle; border:none; padding:0 14px 0 0; font-size:8.5px; color:#555; line-height:1.6;">
                        <div><strong>Generado por:</strong> {{ $usuario->name }}</div>
                        <div><strong>Rol:</strong> {{ auth()->user()->role->nombre ?? 'Administrador' }}</div>
                        <div><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                    </td>
                    <td style="vertical-align:middle; border:none; padding:0 0 0 14px; border-left:1.5px solid #d4d4d4; width:60px;">
                        <img src="{{ $logoSenaPath ?? $logoPath }}" alt="SENA" style="height:58px; display:block;">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<hr class="divider-top">

{{-- ══ TÍTULO ══ --}}
<div class="report-title">Informe de Transferencias y Shows</div>
@if($inicio->isSameDay($fin))
<div class="report-sub">Fecha: {{ $inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; {{ $periodoTexto ?? ucfirst($periodo) }}</div>
@else
<div class="report-sub">Período: {{ $inicio->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} — {{ $fin->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }} &nbsp;·&nbsp; {{ $periodoTexto ?? ucfirst($periodo) }}</div>
@endif

{{-- ══ RESUMEN KPI ══ --}}
<div class="kpi-row">
    <div class="kpi">
        <div class="lbl">Eventos Programados</div>
        <div class="val purple">{{ $stats['totalEventos'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Actividades Totales</div>
        <div class="val">{{ $stats['totalActividades'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Finalizadas</div>
        <div class="val green">{{ $stats['finalizadas'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">En Ejecución</div>
        <div class="val orange">{{ $stats['enEjecucion'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Pendientes</div>
        <div class="val purple">{{ $stats['pendientes'] }}</div>
    </div>
    <div class="kpi">
        <div class="lbl">Cumplimiento</div>
        <div class="val blue">{{ $stats['tasaCierre'] }}%</div>
    </div>
</div>

{{-- ══ RESUMEN ESPACIOS ══ --}}
@if($stats['espacios']->count())
<div class="section-title">Espacios Físicos Más Ocupados</div>
<div class="chips">
    @foreach($stats['espacios'] as $esp => $cant)
    <div class="chip">
        <div class="chip-lbl">{{ Str::limit($esp, 20) }}</div>
        <div class="chip-val">{{ $cant }} {{ $cant == 1 ? 'evento/act.' : 'eventos/act.' }}</div>
    </div>
    @endforeach
</div>
@endif

{{-- ══ SECCIÓN 1: CRONOGRAMA DE ESPACIOS Y SHOWS ══ --}}
<div class="section-title">1. Cronograma de Espacios y Shows (Eventos)</div>
<table>
    <thead>
        <tr>
            <th style="width:25px;" class="td-center">#</th>
            <th style="width:65px;">Fecha</th>
            <th style="width:75px;">Horario</th>
            <th>Título del Evento / Show / Transferencia</th>
            <th style="width:75px;">Tipo</th>
            <th>Espacio Físico</th>
            <th>Programado por</th>
            <th style="width:65px;">Estado</th>
        </tr>
    </thead>
    <tbody>
    @forelse($eventos as $idx => $ev)
    <tr>
        <td class="td-center" style="font-weight:bold; color:#71277a;">{{ $idx + 1 }}</td>
        <td style="white-space:nowrap;">{{ $ev->fecha ? $ev->fecha->format('d/m/Y') : '—' }}</td>
        <td style="white-space:nowrap; font-size:8px; color:#555;">
            {{ $ev->hora_inicio ? substr($ev->hora_inicio, 0, 5) : '—' }} - {{ $ev->hora_fin ? substr($ev->hora_fin, 0, 5) : '—' }}
        </td>
        <td>
            <strong>{{ $ev->titulo }}</strong>
            @if($ev->descripcion)
                <br><span style="font-size:7.5px; color:#777;">{{ Str::limit($ev->descripcion, 60) }}</span>
            @endif
        </td>
        <td><span class="badge badge-tipo">{{ ucfirst(str_replace('_',' ', $ev->tipo)) }}</span></td>
        <td>{{ $ev->espacio ?: 'Sin espacio' }}</td>
        <td>{{ $ev->creadoPor->name ?? 'Sistema' }}</td>
        <td>
            @if($ev->actividad)
                <span class="badge badge-{{ $ev->actividad->estado }}">
                    {{ ucfirst(str_replace('_',' ', $ev->actividad->estado)) }}
                </span>
            @else
                <span style="color:#aaa; font-size:8px;">Programado</span>
            @endif
        </td>
    </tr>
    @empty
    <tr><td colspan="8" class="td-center" style="color:#aaa; padding:14px;">No hay eventos ni shows programados en este período</td></tr>
    @endforelse
    </tbody>
</table>

{{-- ══ SECCIÓN 2: EJECUCIÓN DE ACTIVIDADES ══ --}}
<div class="section-title">2. Ejecución de Actividades de Transferencias y Shows</div>
<table>
    <thead>
        <tr>
            <th style="width:25px;" class="td-center">#</th>
            <th>Actividad / Tema</th>
            <th style="width:75px;">Tipo</th>
            <th>Espacio Físico</th>
            <th>Responsable</th>
            <th style="width:70px;">Progreso</th>
            <th style="width:65px;">Estado</th>
            <th style="width:65px;">Fecha Límite</th>
        </tr>
    </thead>
    <tbody>
    @forelse($actividades as $idx => $act)
    @php
        $prog = $act->progreso ?? 0;
        $barColor = $act->estado === 'finalizado' ? '#39a900' : ($act->estado === 'en_ejecucion' ? '#d97706' : '#71277a');
    @endphp
    <tr>
        <td class="td-center" style="font-weight:bold; color:#39a900;">{{ $idx + 1 }}</td>
        <td>
            <strong>{{ $act->tema }}</strong>
            @if($act->descripcion)
                <br><span style="font-size:7.5px; color:#777;">{{ Str::limit($act->descripcion, 55) }}</span>
            @endif
            @if($act->proyecto)
                <br><span style="font-size:7.5px; color:#2563eb;">📂 {{ $act->proyecto->nombre }}</span>
            @endif
        </td>
        <td><span class="badge badge-tipo">{{ ucfirst(str_replace('_',' ', $act->tipo ?: 'Actividad')) }}</span></td>
        <td>{{ $act->espacio_fisico ?: '—' }}</td>
        <td>{{ $act->responsableUser->name ?? ($act->responsable ?: '—') }}</td>
        <td style="white-space:nowrap;">
            <div class="bar-wrap"><div class="bar-fill" style="width:{{ $prog }}%; background:{{ $barColor }};"></div></div>
            <span style="font-size:7.5px; margin-left:3px; font-weight:bold;">{{ $prog }}%</span>
        </td>
        <td>
            <span class="badge badge-{{ $act->estado }}">
                {{ ucfirst(str_replace('_',' ', $act->estado)) }}
            </span>
        </td>
        <td style="white-space:nowrap;">
            {{ $act->fecha_limite ? $act->fecha_limite->format('d/m/Y') : ($act->fecha_hora ? $act->fecha_hora->format('d/m/Y') : '—') }}
        </td>
    </tr>
    @empty
    <tr><td colspan="8" class="td-center" style="color:#aaa; padding:14px;">No se registraron actividades en este período</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    <span>Zajuna Campo · Sistema de Gestión Agropecuaria — SENA</span>
    <span>Informe generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</span>
</div>

</body>
</html>

