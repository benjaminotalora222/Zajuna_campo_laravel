<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Zajuna Campo') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --green: #39a900;
            --green-dark: #28790a;
            --green-ink: #1f3410;
            --purple: #71277a;
            --purple-deep: #4c1a53;
            --gold: #fdc300;
            --cream: #fffaec;
            --line: #e7e0cc;
        }
        *, body { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Work Sans', sans-serif;
            background: var(--cream);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Navbar ── */
        .auth-nav {
            background: rgba(255,250,236,0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--line);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .auth-nav a.back {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--green-ink);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            opacity: 0.7;
            transition: opacity .2s;
        }
        .auth-nav a.back:hover { opacity: 1; }

        /* ── Main layout: split ── */
        .auth-wrap {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: calc(100vh - 64px);
        }

        /* ── Left panel: illustration ── */
        .auth-panel-left {
            background: var(--purple-deep);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
        }
        .auth-panel-left::before {
            content: "";
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 30% 40%, rgba(253,195,0,0.18) 0%, transparent 65%),
                        radial-gradient(ellipse at 80% 80%, rgba(57,169,0,0.22) 0%, transparent 55%);
        }
        .auth-panel-left > * { position: relative; z-index: 1; }
        .auth-left-logo { margin-bottom: 40px; }
        .auth-left-logo img { height: 80px; width: auto; }
        .auth-left-title {
            font-family: 'Fredoka', sans-serif;
            font-size: clamp(1.6rem, 2.8vw, 2.2rem);
            font-weight: 700;
            color: #fff;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .auth-left-title em { font-style: normal; color: var(--gold); }
        .auth-left-sub {
            font-size: 1rem;
            color: #cdb8cf;
            text-align: center;
            line-height: 1.6;
            max-width: 34ch;
        }
        .auth-left-art {
            margin-top: 44px;
            width: 100%;
            max-width: 320px;
        }
        .auth-left-art svg { width: 100%; height: auto; }

        /* ── Right panel: form ── */
        .auth-panel-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            background: var(--cream);
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
        }

        @media (max-width: 820px) {
            .auth-wrap { grid-template-columns: 1fr; }
            .auth-panel-left { display: none; }
            .auth-panel-right { padding: 40px 24px; }
        }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="auth-nav">
        <a href="{{ url('/') }}" class="back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Volver al inicio
        </a>
        <a href="{{ url('/') }}" style="text-decoration:none;">
            <img src="{{ asset('img/logo-zajuna-campo.png') }}" alt="Zajuna Campo" style="height:56px;width:auto;">
        </a>
    </nav>

    {{-- Split layout --}}
    <div class="auth-wrap">

        {{-- Left: illustration --}}
        <div class="auth-panel-left">
            <p class="auth-left-title">
                Bienvenido de vuelta al<br><em>campo colombiano</em>
            </p>
            <p class="auth-left-sub">
                Accede a tu cuenta para continuar acompañando el fortalecimiento de la economía popular y campesina.
            </p>
            <div class="auth-left-art" aria-hidden="true">
                <svg viewBox="0 0 320 260" xmlns="http://www.w3.org/2000/svg">
                    {{-- Sky background --}}
                    <rect width="320" height="260" rx="20" fill="#3d1245"/>
                    {{-- Stars --}}
                    <circle cx="40"  cy="30"  r="2" fill="#fdc300" opacity="0.7"/>
                    <circle cx="90"  cy="18"  r="1.5" fill="#fdc300" opacity="0.5"/>
                    <circle cx="160" cy="25"  r="2.5" fill="#fdc300" opacity="0.8"/>
                    <circle cx="240" cy="15"  r="1.5" fill="#fdc300" opacity="0.6"/>
                    <circle cx="285" cy="40"  r="2"   fill="#fdc300" opacity="0.5"/>
                    <circle cx="210" cy="50"  r="1.5" fill="#fdc300" opacity="0.4"/>
                    <circle cx="70"  cy="60"  r="1"   fill="#fdc300" opacity="0.4"/>
                    {{-- Moon --}}
                    <circle cx="260" cy="55" r="28" fill="#fdc300" opacity="0.9"/>
                    <circle cx="272" cy="48" r="22" fill="#3d1245"/>
                    {{-- Hills --}}
                    <ellipse cx="160" cy="230" rx="200" ry="70" fill="#39a900"/>
                    <ellipse cx="160" cy="215" rx="160" ry="55" fill="#2c7f04"/>
                    <ellipse cx="160" cy="200" rx="120" ry="42" fill="#71277a"/>
                    {{-- Plants --}}
                    <line x1="70"  y1="195" x2="70"  y2="165" stroke="#fffaec" stroke-width="3.5" stroke-linecap="round"/>
                    <line x1="70"  y1="175" x2="55"  y2="163" stroke="#fffaec" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="70"  y1="182" x2="85"  y2="170" stroke="#fffaec" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="130" y1="190" x2="130" y2="160" stroke="#fffaec" stroke-width="3.5" stroke-linecap="round"/>
                    <line x1="130" y1="170" x2="115" y2="158" stroke="#fffaec" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="130" y1="178" x2="145" y2="166" stroke="#fffaec" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="200" y1="192" x2="200" y2="162" stroke="#fffaec" stroke-width="3.5" stroke-linecap="round"/>
                    <line x1="200" y1="172" x2="185" y2="160" stroke="#fffaec" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="200" y1="180" x2="215" y2="168" stroke="#fffaec" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="258" y1="195" x2="258" y2="168" stroke="#fffaec" stroke-width="3.5" stroke-linecap="round"/>
                    <line x1="258" y1="178" x2="244" y2="166" stroke="#fffaec" stroke-width="2.5" stroke-linecap="round"/>
                    <line x1="258" y1="185" x2="272" y2="173" stroke="#fffaec" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        {{-- Right: slot content --}}
        <div class="auth-panel-right">
            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>

    </div>

</body>
</html>
