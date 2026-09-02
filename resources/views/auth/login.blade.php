<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Iniciar sesión · {{ config('app.name', 'Zajuna Campo') }}</title>
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
  .btn-register {
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
    cursor: pointer;
  }
  .btn-register:hover { background: var(--purple); color: #fff; }

  /* ── Main ── */
  main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 48px 20px;
  }

  .login-shell {
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 900px;
    width: 100%;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px -20px rgba(34,48,28,0.3);
  }

  /* ── Left panel ── */
  .login-left {
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
  .login-left h2 {
    font-family: 'Fredoka', sans-serif;
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--gold);
    line-height: 1.2;
    margin-bottom: 18px;
  }
  .login-left p {
    font-size: 0.95rem;
    color: #eadcec;
    line-height: 1.6;
    margin-bottom: 30px;
    max-width: 30ch;
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
  .left-feature svg { flex-shrink: 0; }

  /* Wave art at bottom of left panel */
  .left-art {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    line-height: 0;
  }

  /* ── Right panel ── */
  .login-right {
    background: var(--paper);
    padding: 48px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .login-right h1 {
    font-family: 'Fredoka', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    color: var(--green-ink);
    margin-bottom: 8px;
  }
  .login-right .subtitle {
    font-size: 0.93rem;
    color: var(--ink-soft);
    line-height: 1.55;
    margin-bottom: 30px;
    max-width: 38ch;
  }

  /* Status message */
  .status-msg {
    background: #e9f6dd;
    border: 1px solid var(--green);
    border-radius: 10px;
    padding: 11px 15px;
    font-size: 0.88rem;
    color: var(--green-dark);
    font-weight: 600;
    margin-bottom: 20px;
  }

  /* Fields */
  .field { margin-bottom: 18px; }
  .field label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 7px;
  }
  .field input {
    width: 100%;
    padding: 13px 16px;
    border: 1.5px solid var(--line);
    border-radius: 10px;
    font-family: 'Work Sans', sans-serif;
    font-size: 0.97rem;
    color: var(--ink);
    background: #fff;
    outline: none;
    transition: border-color .18s, box-shadow .18s;
  }
  .field input::placeholder { color: #b5b0a0; }
  .field input:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 4px rgba(57,169,0,0.12);
  }
  .field-error {
    font-size: 0.82rem;
    color: #b3261e;
    font-weight: 600;
    margin-top: 5px;
    display: block;
  }

  /* Remember + forgot row */
  .field-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
    flex-wrap: wrap;
    gap: 8px;
  }
  .remember {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    color: var(--ink-soft);
    cursor: pointer;
    font-weight: 500;
  }
  .remember input[type=checkbox] {
    width: 16px;
    height: 16px;
    accent-color: var(--green);
    cursor: pointer;
  }
  .forgot-link {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--purple);
    text-decoration: none;
  }
  .forgot-link:hover { text-decoration: underline; }

  /* Submit button */
  .btn-submit {
    width: 100%;
    padding: 15px;
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
  }
  .btn-submit:hover { background: var(--green-dark); transform: translateY(-2px); }

  /* Divider */
  .divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 22px 0;
    font-size: 0.82rem;
    color: #b5b0a0;
  }
  .divider::before, .divider::after {
    content: "";
    flex: 1;
    height: 1px;
    background: var(--line);
  }

  /* Register note */
  .register-note {
    font-size: 0.91rem;
    color: var(--ink-soft);
    text-align: center;
    margin-bottom: 18px;
  }
  .register-note a {
    color: var(--green-dark);
    font-weight: 700;
    text-decoration: none;
  }
  .register-note a:hover { text-decoration: underline; }

  /* Back link */
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

  /* Responsive */
  @media (max-width: 700px) {
    .login-shell { grid-template-columns: 1fr; }
    .login-left  { display: none; }
    .login-right { padding: 40px 28px; }
    .nav { padding: 12px 20px; }
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
      <span>¿No tienes cuenta?</span>
      @if (Route::has('register'))
        <a class="btn-register" href="{{ route('register') }}">Regístrate</a>
      @endif
    </div>
  </nav>
</header>

<main>
  <div class="login-shell">

    {{-- Panel izquierdo --}}
    <div class="login-left">
      <p class="left-eyebrow">Zajuna Campo</p>
      <h2>Un solo lugar para acompañar tu proceso en el campo.</h2>
      <p>Ingresa con tu cuenta para seguir tus cursos, tu asistencia técnica y tus rutas de acceso a mercado.</p>

      <div class="left-features">
        <div class="left-feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fdc300" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 21c-5-3-8-6-8-11a8 8 0 0 1 16 0c0 5-3 8-8 11z"/>
            <path d="M12 12v4"/>
          </svg>
          Formación técnica en campo
        </div>
        <div class="left-feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fdc300" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="9" cy="7" r="3"/>
            <circle cx="17" cy="9" r="2.4"/>
            <path d="M2 21c0-4 3-6 7-6s7 2 7 6"/>
            <path d="M15 15c3 0 5 2 5 6"/>
          </svg>
          Fortalecimiento asociativo
        </div>
        <div class="left-feature">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fdc300" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M3 3h2l2.6 12.6a2 2 0 0 0 2 1.6h8a2 2 0 0 0 2-1.6L21 8H6"/>
            <circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/>
          </svg>
          Acceso a mercados
        </div>
      </div>

      {{-- Wave decoration --}}
      <div class="left-art" aria-hidden="true">
        <svg viewBox="0 0 460 120" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
          <path d="M0 60 Q115 20 230 60 T460 60 V120 H0 Z" fill="#39a900"/>
          <path d="M0 80 Q115 45 230 80 T460 80 V120 H0 Z" fill="#2c7f04" opacity="0.85"/>
          <path d="M0 98 Q115 68 230 98 T460 98 V120 H0 Z" fill="#fdc300" opacity="0.6"/>
        </svg>
      </div>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="login-right">
      <h1>Bienvenido de nuevo</h1>
      <p class="subtitle">Ingresa tus datos para continuar con tu proceso de formación y acompañamiento.</p>

      @if (session('status'))
        <div class="status-msg">{{ session('status') }}</div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
          <label for="email">Correo electrónico</label>
          <input
            id="email" type="email" name="email"
            value="{{ old('email') }}"
            placeholder="tucorreo@ejemplo.com"
            required autofocus autocomplete="username"
          >
          @error('email')
            <span class="field-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="field">
          <label for="password">Contraseña</label>
          <input
            id="password" type="password" name="password"
            placeholder="••••••••"
            required autocomplete="current-password"
          >
          @error('password')
            <span class="field-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="field-row">
          <label class="remember">
            <input type="checkbox" name="remember" id="remember">
            Recordarme
          </label>
          @if (Route::has('password.request'))
            <a class="forgot-link" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
          @endif
        </div>

        <button type="submit" class="btn-submit">Iniciar sesión</button>
      </form>

      <div class="divider">o</div>

      <p class="register-note">
        ¿Aún no tienes cuenta?
        @if (Route::has('register'))
          <a href="{{ route('register') }}">Regístrate aquí</a>
        @endif
      </p>

      <a class="back-link" href="{{ url('/') }}">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
          <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
        </svg>
        Volver al inicio
      </a>
    </div>

  </div>
</main>

</body>
</html>
