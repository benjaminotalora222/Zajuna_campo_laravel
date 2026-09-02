<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Recuperar contraseña · {{ config('app.name', 'Zajuna Campo') }}</title>
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
    min-height: 460px;
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
    width: 22px; height: 3px;
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
    margin-bottom: 30px;
    max-width: 30ch;
  }
  .left-steps {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .left-step {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 0.9rem;
    color: #f2e6f3;
    font-weight: 500;
  }
  .step-num {
    width: 24px; height: 24px;
    border-radius: 50%;
    background: var(--gold);
    color: var(--purple-deep);
    font-weight: 800;
    font-size: 0.78rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
  }
  .left-art {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    line-height: 0;
  }

  /* ── Right panel ── */
  .right {
    background: var(--paper);
    padding: 48px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .right h1 {
    font-family: 'Fredoka', sans-serif;
    font-size: 2rem;
    font-weight: 700;
    color: var(--green-ink);
    margin-bottom: 8px;
  }
  .right .subtitle {
    font-size: 0.93rem;
    color: var(--ink-soft);
    line-height: 1.55;
    margin-bottom: 30px;
    max-width: 38ch;
  }

  /* Status */
  .status-msg {
    background: #e9f6dd;
    border: 1px solid var(--green);
    border-radius: 10px;
    padding: 11px 15px;
    font-size: 0.9rem;
    color: var(--green-dark);
    font-weight: 600;
    margin-bottom: 20px;
  }

  /* Fields */
  .field { margin-bottom: 22px; }
  .field label {
    display: block;
    font-size: 0.84rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 6px;
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

  /* Submit */
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
    content: ""; flex: 1;
    height: 1px;
    background: var(--line);
  }

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

  .login-note {
    font-size: 0.91rem;
    color: var(--ink-soft);
    text-align: center;
    margin-bottom: 16px;
  }
  .login-note a {
    color: var(--green-dark);
    font-weight: 700;
    text-decoration: none;
  }
  .login-note a:hover { text-decoration: underline; }

  @media (max-width: 700px) {
    .shell { grid-template-columns: 1fr; }
    .left  { display: none; }
    .right { padding: 40px 28px; }
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
      <span>¿Ya tienes cuenta?</span>
      <a class="btn-outline" href="{{ route('login') }}">Ingresar</a>
    </div>
  </nav>
</header>

<main>
  <div class="shell">

    {{-- Panel izquierdo --}}
    <div class="left">
      <p class="left-eyebrow">Zajuna Campo</p>
      <h2>Recupera el acceso a tu cuenta.</h2>
      <p>Te enviaremos un enlace seguro a tu correo para que puedas restablecer tu contraseña.</p>

      <div class="left-steps">
        <div class="left-step">
          <div class="step-num">1</div>
          Ingresa tu correo electrónico registrado
        </div>
        <div class="left-step">
          <div class="step-num">2</div>
          Revisa tu bandeja de entrada
        </div>
        <div class="left-step">
          <div class="step-num">3</div>
          Haz clic en el enlace y crea una nueva contraseña
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
      <h1>¿Olvidaste tu contraseña?</h1>
      <p class="subtitle">No hay problema. Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

      @if (session('status'))
        <div class="status-msg">{{ session('status') }}</div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
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

        <button type="submit" class="btn-submit">
          Enviar enlace de recuperación
        </button>
      </form>

      <div class="divider">o</div>

      <p class="login-note">
        ¿Recordaste tu contraseña?
        <a href="{{ route('login') }}">Inicia sesión aquí</a>
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

</body>
</html>
