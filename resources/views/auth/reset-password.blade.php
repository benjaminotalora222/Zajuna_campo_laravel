<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Restablecer contraseña · {{ config('app.name', 'Zajuna Campo') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  html { scroll-behavior: smooth; }

  :root {
    --green:       #39a900;
    --green-dark:  #28790a;
    --green-ink:   #1f3410;
    --purple:      #71277a;
    --purple-deep: #4c1a53;
    --gold:        #fdc300;
    --cream:       #fffaec;
    --paper:       #ffffff;
    --ink:         #22301c;
    --ink-soft:    #4d5a45;
    --line:        #e7e0cc;
  }

  body {
    font-family: 'Work Sans', sans-serif;
    background: var(--cream);
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    -webkit-font-smoothing: antialiased;
  }

  /* ── Navbar ── */
  .nav {
    background: var(--cream);
    border-bottom: 1px solid var(--line);
    padding: 14px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .nav-logo { height: 90px; width: auto; display: block; }
  .nav-right {
    display: flex;
    align-items: center;
    gap: 16px;
    font-size: 0.92rem;
    color: var(--ink-soft);
  }
  .btn-outline {
    font-family: 'Work Sans', sans-serif;
    font-weight: 700;
    font-size: 0.92rem;
    color: var(--purple);
    border: 2px solid var(--purple);
    background: transparent;
    padding: 10px 22px;
    border-radius: 999px;
    text-decoration: none;
    transition: background .18s, color .18s;
  }
  .btn-outline:hover { background: var(--purple); color: #fff; }

  /* ── Main ── */
  main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 20px;
  }

  .shell {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 900px;
    width: 100%;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px -20px rgba(34,48,28,0.3);
  }

  /* ── Left panel ── */
  .left {
    background: var(--purple-deep);
    padding: 48px 40px 0;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    min-height: 480px;
  }
  .left-eyebrow {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    color: var(--gold);
    margin-bottom: 20px;
  }
  .left-eyebrow::before {
    content: "";
    width: 22px;
    height: 3px;
    background: var(--gold);
    border-radius: 2px;
    flex-shrink: 0;
  }
  .left h2 {
    font-family: 'Fredoka', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gold);
    line-height: 1.2;
    margin-bottom: 18px;
  }
  .left p {
    font-size: 0.95rem;
    color: #eadcec;
    line-height: 1.6;
    margin-bottom: 28px;
    max-width: 32ch;
  }
  .left-features {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }
  .left-feature {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.93rem;
    color: #f2e6f3;
    font-weight: 500;
  }

  /* Wave art at bottom */
  .left-art {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    line-height: 0;
  }

  /* ── Right panel ── */
  .right {
    background: var(--paper);
    padding: 44px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .right h1 {
    font-family: 'Fredoka', sans-serif;
    font-size: 1.95rem;
    font-weight: 700;
    color: var(--green-ink);
    margin-bottom: 8px;
  }
  .right .subtitle {
    font-size: 0.92rem;
    color: var(--ink-soft);
    line-height: 1.5;
    margin-bottom: 24px;
  }

  .status-msg {
    background: #e9f6dd;
    border: 1px solid var(--green);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.88rem;
    color: var(--green-dark);
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    line-height: 1.4;
  }

  .dev-notice {
    background: #fefce8;
    border: 1px dashed #ca8a04;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 0.85rem;
    color: #854d0e;
    margin-bottom: 20px;
    line-height: 1.45;
  }
  .dev-notice strong { color: #713f12; }

  /* Fields */
  .field { margin-bottom: 16px; }
  .field label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 6px;
  }
  .field input {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid var(--line);
    border-radius: 10px;
    font-family: 'Work Sans', sans-serif;
    font-size: 0.97rem;
    color: var(--ink);
    background: #fff;
    outline: none;
    transition: border-color .18s, box-shadow .18s;
  }
  .field input:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 4px rgba(57,169,0,0.12);
  }
  .field input.codigo-input {
    font-size: 1.4rem;
    font-weight: 700;
    letter-spacing: 6px;
    text-align: center;
    color: var(--purple-deep);
    background: #faf7ee;
  }
  .field input::placeholder { color: #b5b0a0; }
  .field-error {
    font-size: 0.82rem;
    color: #b3261e;
    font-weight: 600;
    margin-top: 5px;
    display: block;
  }

  /* Password field wrapper & toggle */
  .password-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
  }
  .password-input-wrap input {
    width: 100%;
    padding-right: 48px;
  }
  .btn-toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    padding: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    color: var(--ink-soft);
    border-radius: 8px;
    transition: color .18s, background .18s;
  }
  .btn-toggle-password:hover {
    color: var(--purple);
    background: rgba(113, 39, 122, 0.08);
  }
  .btn-toggle-password:focus-visible {
    outline: 2px solid var(--purple);
    outline-offset: 1px;
  }

  /* Submit button */
  .btn-submit {
    width: 100%;
    padding: 14px;
    background: var(--green);
    color: #fff;
    font-family: 'Work Sans', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    border: none;
    border-radius: 999px;
    cursor: pointer;
    box-shadow: 0 10px 22px -8px rgba(57,169,0,0.55);
    transition: background .18s, transform .18s;
    margin-top: 6px;
  }
  .btn-submit:hover { background: var(--green-dark); transform: translateY(-2px); }

  .divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 20px 0 16px;
    font-size: 0.82rem;
    color: #b5b0a0;
  }
  .divider::before, .divider::after {
    content: ""; flex: 1;
    height: 1px;
    background: var(--line);
  }

  .login-note {
    font-size: 0.91rem;
    color: var(--ink-soft);
    text-align: center;
    margin-bottom: 14px;
  }
  .login-note a {
    color: var(--green-dark);
    font-weight: 700;
    text-decoration: none;
  }
  .login-note a:hover { text-decoration: underline; }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--ink-soft);
    text-decoration: none;
    transition: color .18s;
  }
  .back-link:hover { color: var(--purple); }

  @media (max-width: 700px) {
    .shell { grid-template-columns: 1fr; }
    .left  { display: none; }
    .right { padding: 36px 24px; }
    .nav   { padding: 12px 20px; }
  }
</style>
</head>
<body>

{{-- Navbar --}}
<header>
  <nav class="nav">
    <a href="{{ url('/') }}">
      <img class="nav-logo" src="{{ asset('img/logo-zajuna-campo.png') }}" alt="Zajuna Campo">
    </a>
    <div class="nav-right">
      <span>¿Recordaste tu clave?</span>
      <a class="btn-outline" href="{{ route('login') }}">Ingresar</a>
    </div>
  </nav>
</header>

<main>
  <div class="shell">

    {{-- Panel izquierdo --}}
    <div class="left">
      <p class="left-eyebrow">Zajuna Campo</p>
      <h2>Crea tu nueva contraseña.</h2>
      <p>Ingresa el código que recibiste en tu correo y establece una contraseña segura para tu cuenta.</p>

      <div class="left-features">
        <div class="left-feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fdc300" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          Código seguro de 6 dígitos
        </div>
        <div class="left-feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fdc300" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          Mínimo 8 caracteres
        </div>
        <div class="left-feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fdc300" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
          </svg>
          Acceso inmediato a tu cuenta
        </div>
      </div>

      {{-- Wave decoration --}}
      <div class="left-art" aria-hidden="true">
        <svg viewBox="0 0 460 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
          <path d="M0 60 Q115 20 230 60 T460 60 V120 H0 Z" fill="#39a900"/>
          <path d="M0 78 Q115 42 230 78 T460 78 V120 H0 Z" fill="#2c7f04"/>
          <path d="M0 96 Q115 65 230 96 T460 96 V120 H0 Z" fill="#fdc300"/>
        </svg>
      </div>
    </div>

    {{-- Panel derecho --}}
    <div class="right">
      <h1>Restablecer contraseña</h1>
      <p class="subtitle">Ingresa el código de 6 dígitos que enviamos a tu correo y tu nueva clave.</p>

      @if (session('status'))
        <div class="status-msg">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
          </svg>
          <span>{{ session('status') }}</span>
        </div>
      @endif

      @if (!empty($dev_code))
        <div class="dev-notice">
          <strong>Modo desarrollo (Laragon):</strong> Tu código de verificación generado es:
          <span style="font-size:1.15rem;font-weight:800;letter-spacing:2px;color:#4c1a53;display:block;margin-top:4px;">{{ $dev_code }}</span>
          <span style="font-size:0.78rem;opacity:0.85;">(Este aviso solo es visible en entorno local para facilitar pruebas).</span>
        </div>
      @endif

      <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Email --}}
        <div class="field">
          <label for="email">Correo electrónico</label>
          <input
            id="email" type="email" name="email"
            value="{{ old('email', $email) }}"
            placeholder="tucorreo@ejemplo.com"
            required autocomplete="username"
          >
          @error('email')
            <span class="field-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- Código de 6 dígitos --}}
        <div class="field">
          <label for="codigo">Código de recuperación (6 dígitos)</label>
          <input
            id="codigo" type="text" name="codigo"
            value="{{ old('codigo', $dev_code ?? '') }}"
            placeholder="123456"
            maxlength="6"
            class="codigo-input"
            required autofocus autocomplete="one-time-code"
          >
          @error('codigo')
            <span class="field-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- Nueva Contraseña con ojito --}}
        <div class="field">
          <label for="password">Nueva contraseña</label>
          <div class="password-input-wrap">
            <input
              id="password" type="password" name="password"
              placeholder="Mínimo 8 caracteres"
              required autocomplete="new-password"
            >
            <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password', this)" aria-label="Mostrar u ocultar contraseña">
              <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg class="eye-off-icon" style="display:none;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="23" x2="23" y2="1"></line>
              </svg>
            </button>
          </div>
          @error('password')
            <span class="field-error">{{ $message }}</span>
          @enderror
        </div>

        {{-- Confirmar Contraseña con ojito --}}
        <div class="field">
          <label for="password_confirmation">Confirmar nueva contraseña</label>
          <div class="password-input-wrap">
            <input
              id="password_confirmation" type="password" name="password_confirmation"
              placeholder="Repite tu nueva contraseña"
              required autocomplete="new-password"
            >
            <button type="button" class="btn-toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Mostrar u ocultar confirmación de contraseña">
              <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
              <svg class="eye-off-icon" style="display:none;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="23" x2="23" y2="1"></line>
              </svg>
            </button>
          </div>
          @error('password_confirmation')
            <span class="field-error">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit" class="btn-submit">
          Restablecer contraseña
        </button>
      </form>

      <div class="divider">o</div>

      <p class="login-note">
        ¿No recibiste el código?
        <a href="{{ route('password.request') }}">Solicitar otro código</a>
      </p>

      <a class="back-link" href="{{ url('/') }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
        </svg>
        Volver al inicio
      </a>
    </div>

  </div>
</main>

<script>
  function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const eyeIcon = btn.querySelector('.eye-icon');
    const eyeOffIcon = btn.querySelector('.eye-off-icon');

    if (input.type === 'password') {
      input.type = 'text';
      if (eyeIcon) eyeIcon.style.display = 'none';
      if (eyeOffIcon) eyeOffIcon.style.display = 'block';
      btn.setAttribute('aria-label', 'Ocultar contraseña');
    } else {
      input.type = 'password';
      if (eyeIcon) eyeIcon.style.display = 'block';
      if (eyeOffIcon) eyeOffIcon.style.display = 'none';
      btn.setAttribute('aria-label', 'Mostrar contraseña');
    }
  }
</script>

</body>
</html>
