<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notificación de Tareas y Requerimientos · Zajuna Campo</title>
<style>
  body {
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: #fffaec;
    color: #22301c;
  }
  .container {
    max-width: 640px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    border: 1px solid #e7e0cc;
  }
  .header {
    background: #4c1a53;
    padding: 30px 24px;
    text-align: center;
  }
  .header h1 {
    margin: 0;
    font-size: 22px;
    color: #fdc300;
    font-weight: 800;
    letter-spacing: 0.02em;
  }
  .header p {
    margin: 6px 0 0;
    font-size: 12px;
    color: #e9dcea;
    text-transform: uppercase;
    letter-spacing: 0.1em;
  }
  .content {
    padding: 32px 28px;
  }
  .greeting {
    font-size: 18px;
    font-weight: 700;
    color: #1f3410;
    margin-bottom: 8px;
  }
  .intro {
    font-size: 14px;
    color: #4a4f44;
    line-height: 1.6;
    margin-bottom: 24px;
  }
  /* METRIC CARDS */
  .metrics-grid {
    display: table;
    width: 100%;
    margin-bottom: 24px;
  }
  .metric-col {
    display: table-cell;
    width: 25%;
    padding: 0 4px;
    vertical-align: top;
  }
  .metric-card {
    border-radius: 12px;
    padding: 12px 10px;
    text-align: center;
    border: 1px solid transparent;
  }
  .metric-num {
    font-size: 20px;
    font-weight: 800;
    margin-bottom: 2px;
  }
  .metric-lbl {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }
  /* SECTION HEADERS */
  .section-title {
    font-size: 15px;
    font-weight: 800;
    color: #4c1a53;
    margin: 24px 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    border-bottom: 2px solid #f3e8ff;
    padding-bottom: 6px;
  }
  /* TABLES */
  .table-wrapper {
    border: 1px solid #e7e0cc;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 16px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    text-align: left;
  }
  thead {
    background: #f7f5ed;
  }
  th {
    padding: 10px 12px;
    font-weight: 700;
    color: #4c1a53;
    border-bottom: 1px solid #e7e0cc;
  }
  td {
    padding: 10px 12px;
    border-bottom: 1px solid #f1ece0;
    color: #22301c;
  }
  tr:last-child td {
    border-bottom: none;
  }
  /* BADGES */
  .badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
  }
  .badge-danger {
    background: #fef2f2;
    color: #991b1b;
  }
  .badge-warning {
    background: #fffbeb;
    color: #92400e;
  }
  .badge-info {
    background: #eff6ff;
    color: #1e40af;
  }
  .badge-success {
    background: #f0fdf4;
    color: #166534;
  }
  .badge-purple {
    background: #f3e8ff;
    color: #6b21a8;
  }
  /* CTA BUTTON */
  .cta-container {
    text-align: center;
    margin: 32px 0 16px;
  }
  .cta-button {
    display: inline-block;
    background: #39a900;
    color: #ffffff !important;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    padding: 12px 28px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(57,169,0,0.25);
  }
  /* FOOTER */
  .footer {
    background: #f7f5ed;
    padding: 24px;
    text-align: center;
    border-top: 1px solid #e7e0cc;
  }
  .footer p {
    margin: 4px 0;
    font-size: 12px;
    color: #7a8272;
  }
</style>
</head>
<body>
<div class="container">

  {{-- ENCABEZADO --}}
  <div class="header">
    <p>Centro Agropecuario · SENA</p>
    <h1>ZAJUNA CAMPO</h1>
    <p>Gestión de Tareas y Requerimientos</p>
  </div>

  {{-- CONTENIDO --}}
  <div class="content">

    <div class="greeting">Hola, {{ $destinatario->name }}</div>
    <div class="intro">
      @if($esConsolidado)
        A continuación te presentamos el resumen consolidado de las <strong>tareas y requerimientos pendientes</strong> en la plataforma Zajuna Campo:
      @else
        Este es un recordatorio de las <strong>tareas y requerimientos asignados a tu cargo</strong> que se encuentran pendientes o próximos a vencer:
      @endif
    </div>

    @php
      $vencidasCount = $tareas->filter(fn($t) => $t->es_vencida)->count();
      $proximasCount = $tareas->filter(fn($t) => $t->es_proxima_vencer)->count();
      $pendientesTareas = $tareas->count() - $vencidasCount - $proximasCount;
      $actividadesCount = $actividades->count();
    @endphp

    {{-- TARJETAS DE CONTEO --}}
    <div class="metrics-grid">
      <div class="metric-col">
        <div class="metric-card" style="background:#fef2f2; border-color:#fecaca;">
          <div class="metric-num" style="color:#b91c1c;">{{ $vencidasCount }}</div>
          <div class="metric-lbl" style="color:#991b1b;">Tareas Vencidas</div>
        </div>
      </div>
      <div class="metric-col">
        <div class="metric-card" style="background:#fffbeb; border-color:#fde68a;">
          <div class="metric-num" style="color:#d97706;">{{ $proximasCount }}</div>
          <div class="metric-lbl" style="color:#92400e;">Próx. a Vencer</div>
        </div>
      </div>
      <div class="metric-col">
        <div class="metric-card" style="background:#faf5ff; border-color:#e9d5ff;">
          <div class="metric-num" style="color:#71277a;">{{ $pendientesTareas }}</div>
          <div class="metric-lbl" style="color:#71277a;">En Progreso</div>
        </div>
      </div>
      <div class="metric-col">
        <div class="metric-card" style="background:#f0fdf4; border-color:#bbf7d0;">
          <div class="metric-num" style="color:#15803d;">{{ $actividadesCount }}</div>
          <div class="metric-lbl" style="color:#166534;">Requerimientos</div>
        </div>
      </div>
    </div>

    {{-- SECCIÓN 1: TAREAS PENDIENTES --}}
    @if($tareas->count())
      <div class="section-title">
        <span>📌 Tareas Pendientes ({{ $tareas->count() }})</span>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Tarea / Proyecto</th>
              <th>Prioridad</th>
              <th>Fecha Entrega</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tareas as $t)
            <tr>
              <td>
                <strong>{{ $t->nombre }}</strong>
                @if($t->proyecto)
                  <br><span style="color:#6b7280; font-size:11px;">📁 {{ $t->proyecto->nombre }}</span>
                @endif
                @if($esConsolidado && $t->responsable)
                  <br><span style="color:#71277a; font-size:10px;">👤 Resp: {{ $t->responsable->name }}</span>
                @endif
              </td>
              <td>
                @php
                  $prioStyle = match($t->prioridad) {
                    'alta' => 'badge-danger',
                    'media' => 'badge-warning',
                    default => 'badge-info',
                  };
                @endphp
                <span class="badge {{ $prioStyle }}">{{ ucfirst($t->prioridad ?? 'Media') }}</span>
              </td>
              <td style="white-space:nowrap;">
                {{ $t->fecha_entrega ? $t->fecha_entrega->format('d/m/Y') : 'Sin fecha' }}
              </td>
              <td>
                @if($t->es_vencida)
                  <span class="badge badge-danger">⚠️ Vencida ({{ $t->dias_restantes_texto }})</span>
                @elseif($t->es_proxima_vencer)
                  <span class="badge badge-warning">⏰ {{ $t->dias_restantes_texto }}</span>
                @else
                  <span class="badge badge-purple">{{ ucfirst(str_replace('_', ' ', $t->estado)) }}</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    {{-- SECCIÓN 2: REQUERIMIENTOS Y ACTIVIDADES PENDIENTES --}}
    @if($actividades->count())
      <div class="section-title">
        <span>📋 Requerimientos y Actividades Pendientes ({{ $actividades->count() }})</span>
      </div>
      <div class="table-wrapper">
        <table>
          <thead>
            <tr>
              <th>Tema / Requerimiento</th>
              <th>Tipo</th>
              <th>Espacio Físico</th>
              <th>Fecha Límite</th>
            </tr>
          </thead>
          <tbody>
            @foreach($actividades as $act)
            <tr>
              <td>
                <strong>{{ $act->tema }}</strong>
                @if($act->proyecto)
                  <br><span style="color:#6b7280; font-size:11px;">📁 {{ $act->proyecto->nombre }}</span>
                @endif
                @if($esConsolidado && ($act->responsableUser || $act->responsable))
                  <br><span style="color:#71277a; font-size:10px;">👤 Resp: {{ $act->responsableUser->name ?? $act->responsable }}</span>
                @endif
              </td>
              <td>
                <span class="badge badge-info">{{ ucfirst($act->tipo ?? 'Actividad') }}</span>
              </td>
              <td style="color:#4b5563;">
                {{ $act->espacio_fisico ?: 'Sin espacio' }}
              </td>
              <td style="white-space:nowrap;">
                @if($act->fecha_limite)
                  {{ $act->fecha_limite->format('d/m/Y') }}
                @elseif($act->fecha_hora)
                  {{ $act->fecha_hora->format('d/m/Y H:i') }}
                @else
                  Sin fecha
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif

    {{-- BOTÓN DE ACCIÓN --}}
    <div class="cta-container">
      <a href="{{ config('app.url') }}" class="cta-button">
        Ingresar a Zajuna Campo →
      </a>
    </div>

  </div>

  {{-- FOOTER --}}
  <div class="footer">
    <p><strong>Zajuna Campo · Sistema Integrado de Gestión</strong></p>
    <p>Servicio Nacional de Aprendizaje - SENA</p>
    <p style="font-size: 11px; color: #9aa193; margin-top: 8px;">
      Este correo es un recordatorio automático de tareas y requerimientos pendientes del sistema.
    </p>
  </div>

</div>
</body>
</html>

