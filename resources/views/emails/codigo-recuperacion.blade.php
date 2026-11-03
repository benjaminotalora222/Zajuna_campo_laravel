<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Código de recuperación · Zajuna Campo</title>
<style>
  body {
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: #fffaec;
    color: #22301c;
  }
  .container {
    max-width: 580px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    border: 1px solid #e7e0cc;
  }
  .header {
    background: #4c1a53;
    padding: 32px 24px;
    text-align: center;
  }
  .header h1 {
    margin: 0;
    font-size: 24px;
    color: #fdc300;
    font-weight: 700;
    letter-spacing: 0.02em;
  }
  .header p {
    margin: 6px 0 0;
    font-size: 13px;
    color: #e9dcea;
    text-transform: uppercase;
    letter-spacing: 0.1em;
  }
  .content {
    padding: 36px 32px;
  }
  .greeting {
    font-size: 18px;
    font-weight: 700;
    color: #1f3410;
    margin-bottom: 12px;
  }
  .message {
    font-size: 15px;
    line-height: 1.6;
    color: #4d5a45;
    margin-bottom: 26px;
  }
  .code-box {
    background: #f7f3e8;
    border: 2px dashed #39a900;
    border-radius: 14px;
    padding: 22px;
    text-align: center;
    margin: 26px 0;
  }
  .code-label {
    font-size: 12px;
    font-weight: 700;
    color: #28790a;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    margin-bottom: 8px;
  }
  .code-value {
    font-size: 38px;
    font-weight: 800;
    letter-spacing: 10px;
    color: #4c1a53;
    font-family: 'Courier New', Courier, monospace;
    display: inline-block;
  }
  .expiry {
    font-size: 13px;
    color: #7a6e5a;
    margin-top: 8px;
  }
  .notice {
    background: #fff8db;
    border-left: 4px solid #fdc300;
    padding: 12px 16px;
    border-radius: 6px;
    font-size: 13px;
    color: #634b00;
    line-height: 1.5;
    margin: 24px 0 10px;
  }
  .footer {
    background: #f9f6ed;
    border-top: 1px solid #e7e0cc;
    padding: 20px 24px;
    text-align: center;
    font-size: 12px;
    color: #8c8577;
    line-height: 1.5;
  }
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <p>Zajuna Campo · La Angostura</p>
    <h1>Recuperación de Contraseña</h1>
  </div>

  <div class="content">
    <div class="greeting">¡Hola, {{ $nombreUsuario ?: 'Usuario' }}!</div>
    <p class="message">
      Recibimos una solicitud para restablecer la contraseña de tu cuenta en el <strong>Sistema Zajuna Campo</strong>.
    </p>

    <div class="code-box">
      <div class="code-label">Tu código de verificación es</div>
      <div class="code-value">{{ $codigo }}</div>
      <div class="expiry">Este código expirará en <strong>15 minutos</strong>.</div>
    </div>

    <p class="message" style="margin-bottom: 0;">
      Ingresa este código de 6 dígitos en la pantalla de recuperación para definir tu nueva contraseña.
    </p>

    <div class="notice">
      <strong>¿No solicitaste este cambio?</strong> Si no realizaste esta solicitud, puedes ignorar este mensaje de forma segura. Tu contraseña actual no cambiará.
    </div>
  </div>

  <div class="footer">
    &copy; {{ date('Y') }} Zajuna Campo · Servicio Nacional de Aprendizaje (SENA)<br>
    Centro Agroindustrial La Angostura · Economía Popular y Campesina
  </div>
</div>

</body>
</html>

