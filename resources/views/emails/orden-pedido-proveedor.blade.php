<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orden de Pedido · Zajuna Campo</title>
<style>
  body {
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: #fffaec;
    color: #22301c;
  }
  .container {
    max-width: 620px;
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
  .badge-orden {
    display: inline-block;
    background: #fdc300;
    color: #3b1042;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 800;
    margin-top: 12px;
  }
  .content {
    padding: 32px 28px;
  }
  .alert-box {
    background: #fffbeb;
    border-left: 4px solid #f59e0b;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 24px;
    font-size: 13px;
    color: #92400e;
    line-height: 1.5;
  }
  .greeting {
    font-size: 17px;
    font-weight: 700;
    color: #1f3410;
    margin-bottom: 10px;
  }
  .paragraph {
    font-size: 14px;
    line-height: 1.6;
    color: #4a4f44;
    margin-bottom: 20px;
  }
  .table-wrapper {
    margin: 20px 0;
    border: 1px solid #e7e0cc;
    border-radius: 12px;
    overflow: hidden;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
    text-align: left;
  }
  thead {
    background: #f7f5ed;
  }
  th {
    padding: 12px 14px;
    font-weight: 700;
    color: #4c1a53;
    border-bottom: 1px solid #e7e0cc;
  }
  td {
    padding: 12px 14px;
    border-bottom: 1px solid #f1ece0;
    color: #22301c;
  }
  tr:last-child td {
    border-bottom: none;
  }
  .highlight-qty {
    background: #f0fdf4;
    color: #166534;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 6px;
    display: inline-block;
  }
  .stock-min-badge {
    background: #fef2f2;
    color: #991b1b;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 12px;
  }
  .info-grid {
    background: #faf8f2;
    border: 1px solid #eee8d8;
    border-radius: 12px;
    padding: 18px;
    margin-top: 24px;
    font-size: 13px;
  }
  .info-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px dashed #e2dcce;
  }
  .info-row:last-child {
    border-bottom: none;
  }
  .info-label {
    font-weight: 600;
    color: #636b5c;
  }
  .info-value {
    font-weight: 700;
    color: #1f3410;
  }
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
    <p>Módulo de Inventario y Abastecimiento</p>
    <div class="badge-orden">ORDEN DE PEDIDO #{{ $ordenPedido->numero_orden }}</div>
  </div>

  {{-- CUERPO --}}
  <div class="content">

    <div class="alert-box">
      <strong>⚠️ Notificación Automática de Stock Mínimo:</strong><br>
      El inventario disponible ha alcanzado o superado el umbral mínimo fijado. Se requiere reposición de productos.
    </div>

    <div class="greeting">Estimado(a) {{ $proveedor->nombre }},</div>
    <div class="paragraph">
      Le informamos que en la plataforma <strong>Zajuna Campo</strong> se ha generado una orden de pedido urgente para reabastecer inventario con su despacho correspondiente. A continuación encontrará los detalles:
    </div>

    {{-- TABLA DE PRODUCTOS --}}
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Producto</th>
            <th>Stock Actual</th>
            <th>Stock Mínimo</th>
            <th>Cantidad Requerida</th>
          </tr>
        </thead>
        <tbody>
          @if($ordenPedido->detalles && count($ordenPedido->detalles))
            @foreach($ordenPedido->detalles as $item)
            <tr>
              <td>
                <strong>{{ $item['nombre'] ?? 'Producto' }}</strong>
                @if(!empty($item['unidad']))
                  <span style="color:#7a8272; font-size:11px;">({{ $item['unidad'] }})</span>
                @endif
              </td>
              <td>
                <span class="stock-min-badge">{{ $item['stock_actual'] ?? 0 }}</span>
              </td>
              <td style="color:#636b5c;">
                {{ $item['stock_minimo'] ?? 0 }}
              </td>
              <td>
                <span class="highlight-qty">+ {{ $item['cantidad'] ?? 0 }}</span>
              </td>
            </tr>
            @endforeach
          @elseif($producto)
            <tr>
              <td>
                <strong>{{ $producto->nombre }}</strong>
                @if($producto->unidad)
                  <span style="color:#7a8272; font-size:11px;">({{ $producto->unidad }})</span>
                @endif
                @if($producto->codigoBarras)
                  <br><span style="color:#8b9183; font-size:10px;">Cód: {{ $producto->codigoBarras }}</span>
                @endif
              </td>
              <td>
                <span class="stock-min-badge">{{ $ordenPedido->stock_actual }}</span>
              </td>
              <td style="color:#636b5c;">
                {{ $ordenPedido->stock_minimo }}
              </td>
              <td>
                <span class="highlight-qty">+ {{ $ordenPedido->cantidad_solicitada }}</span>
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    {{-- DETALLES DE LA ORDEN --}}
    <div class="info-grid">
      <div class="info-row">
        <span class="info-label">Fecha y Hora de Emisión:</span>
        <span class="info-value">{{ ($ordenPedido->fecha_generacion ?? now())->format('d/m/Y H:i') }}</span>
      </div>
      <div class="info-row">
        <span class="info-label">Proveedor:</span>
        <span class="info-value">{{ $proveedor->nombre }} ({{ $proveedor->codigo ?? 'PRV-'.$proveedor->id }})</span>
      </div>
      <div class="info-row">
        <span class="info-label">Correo de Notificación:</span>
        <span class="info-value">{{ $proveedor->correo }}</span>
      </div>
      @if($proveedor->telefono)
      <div class="info-row">
        <span class="info-label">Teléfono de Contacto:</span>
        <span class="info-value">{{ $proveedor->telefono }}</span>
      </div>
      @endif
      @if(!empty($ordenPedido->observaciones))
      <div class="info-row" style="flex-direction: column; gap: 4px;">
        <span class="info-label">Instrucciones u Observaciones:</span>
        <span class="info-value" style="font-weight: normal; color: #374151;">{{ $ordenPedido->observaciones }}</span>
      </div>
      @endif
    </div>

    <div class="paragraph" style="margin-top: 24px; font-size: 13px;">
      Por favor confirme la recepción de esta orden y el tiempo estimado de entrega respondiendo a este mensaje o contactando a la administración del Minimarket Zajuna Campo.
    </div>

  </div>

  {{-- FOOTER --}}
  <div class="footer">
    <p><strong>Zajuna Campo · Sistema Integrado de Gestión</strong></p>
    <p>Servicio Nacional de Aprendizaje - SENA</p>
    <p style="font-size: 11px; color: #9aa193; margin-top: 8px;">
      Este correo electrónico ha sido generado automáticamente por el sistema cuando el stock de productos llega a su umbral mínimo de seguridad.
    </p>
  </div>

</div>
</body>
</html>

