<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name', 'Zajuna Campo') }} · Fortalecimiento y desarrollo de la economía popular y campesina</title>
<meta name="description" content="Zajuna Campo acompaña a comunidades rurales y campesinas de Colombia con formación, asistencia técnica y acceso a mercados.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Work+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
<style>
  :root{
    --green:#39a900;
    --green-dark:#28790a;
    --green-ink:#1f3410;
    --purple:#71277a;
    --purple-deep:#4c1a53;
    --gold:#fdc300;
    --gold-deep:#e0aa00;
    --cream:#fffaec;
    --paper:#ffffff;
    --ink:#22301c;
    --ink-soft:#4d5a45;
    --line:#e7e0cc;
    --radius:20px;
    --shadow:0 18px 40px -22px rgba(34,48,28,0.35);
    --display:'Fredoka', sans-serif;
    --body:'Work Sans', sans-serif;
  }

  *{box-sizing:border-box;}
  html{scroll-behavior:smooth;}
  body{
    margin:0;
    font-family:var(--body);
    color:var(--ink);
    background:var(--cream);
    -webkit-font-smoothing:antialiased;
  }
  img{max-width:100%;display:block;}
  a{color:inherit;}
  h1,h2,h3{font-family:var(--display);margin:0;line-height:1.05;}
  p{margin:0;}
  .wrap{max-width:1180px;margin:0 auto;padding:0 28px;}
  .eyebrow{
    font-family:var(--body);
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.14em;
    font-size:0.78rem;
  }
  button, .btn{
    font-family:var(--body);
    font-weight:600;
    cursor:pointer;
    border:none;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    text-decoration:none;
    transition:transform .18s ease, box-shadow .18s ease, background .18s ease;
  }
  .btn:focus-visible, a:focus-visible, button:focus-visible{
    outline:3px solid var(--purple);
    outline-offset:3px;
  }
  .btn-primary{
    background:var(--green);
    color:#fff;
    padding:15px 28px;
    font-size:0.98rem;
    box-shadow:0 12px 24px -10px rgba(57,169,0,0.55);
  }
  .btn-primary:hover{background:var(--green-dark);transform:translateY(-2px);}
  .btn-ghost{
    background:transparent;
    color:var(--purple);
    padding:14px 22px;
    border:2px solid var(--purple);
  }
  .btn-ghost:hover{background:var(--purple);color:#fff;transform:translateY(-2px);}
  .btn-on-dark{
    background:var(--gold);
    color:var(--purple-deep);
    padding:15px 28px;
  }
  .btn-on-dark:hover{background:#fff;transform:translateY(-2px);}

  /* ---------- wave signature ---------- */
  .wave-divider{width:100%;line-height:0;display:block;}
  .wave-divider svg{width:100%;height:auto;display:block;}

  /* ---------- header ---------- */
  header{
    position:sticky;top:0;z-index:50;
    background:rgba(255,250,236,0.92);
    backdrop-filter:blur(8px);
    border-bottom:1px solid var(--line);
  }
  .nav{
    display:flex;align-items:center;justify-content:space-between;
    padding:6px 28px;max-width:1180px;margin:0 auto;
  }
  .logo-mark{height:110px;width:auto;}
  .nav-links{
    display:flex;align-items:center;gap:34px;
    list-style:none;margin:0;padding:0;
  }
  .nav-links a{
    text-decoration:none;font-weight:600;font-size:0.95rem;color:var(--ink);
    position:relative;padding:4px 0;
  }
  .nav-links a::after{
    content:"";position:absolute;left:0;right:0;bottom:-3px;height:3px;
    background:var(--gold);transform:scaleX(0);transform-origin:left;
    transition:transform .2s ease;
  }
  .nav-links a:hover::after{transform:scaleX(1);}
  .nav-cta{display:flex;align-items:center;gap:14px;}
  .menu-btn{
    display:none;background:none;border:none;padding:8px;border-radius:10px;
  }
  .menu-btn span{display:block;width:24px;height:3px;background:var(--ink);margin:5px 0;border-radius:2px;}

  /* ---------- hero ---------- */
  .hero{
    position:relative;overflow:hidden;
    padding:68px 0 0;
  }
  .hero-grid{
    display:grid;grid-template-columns:1.05fr 0.95fr;gap:48px;align-items:center;
    padding-bottom:64px;
  }
  .hero .eyebrow{color:var(--green-dark);margin-bottom:18px;display:flex;align-items:center;gap:10px;}
  .hero .eyebrow::before{
    content:"";width:26px;height:3px;background:var(--gold);border-radius:2px;
  }
  .hero h1{
    font-size:clamp(2.3rem, 4.2vw, 3.6rem);
    font-weight:700;
    color:var(--green-ink);
    letter-spacing:-0.01em;
  }
  .hero h1 em{
    font-style:normal;color:var(--purple);
  }
  .hero p.lead{
    margin-top:22px;font-size:1.15rem;line-height:1.6;color:var(--ink-soft);max-width:46ch;
  }
  .hero-actions{display:flex;gap:16px;margin-top:34px;flex-wrap:wrap;}
  .hero-stats{
    display:flex;gap:28px;margin-top:46px;flex-wrap:wrap;
  }
  .hero-stats div{min-width:110px;}
  .hero-stats strong{
    font-family:var(--display);font-size:1.7rem;color:var(--purple);display:block;
  }
  .hero-stats span{font-size:0.85rem;color:var(--ink-soft);}

  .hero-art{position:relative;aspect-ratio:1/1;max-width:460px;margin:0 auto;}
  .hero-art svg{width:100%;height:100%;}

  /* ---------- impact band ---------- */
  .impact{
    background:var(--purple-deep);
    color:#fff;
    padding:52px 0;
  }
  .impact .wrap{
    display:grid;grid-template-columns:repeat(4,1fr);gap:24px;
  }
  .impact-item strong{
    font-family:var(--display);font-size:2.3rem;color:var(--gold);display:block;
  }
  .impact-item p{margin-top:6px;font-size:0.92rem;color:#eadcec;}

  /* ---------- section shell ---------- */
  section{padding:96px 0;}
  .section-head{max-width:680px;margin-bottom:56px;}
  .section-head .eyebrow{color:var(--purple);}
  .section-head h2{
    font-size:clamp(1.8rem,3vw,2.5rem);
    color:var(--green-ink);
    margin-top:12px;
  }
  .section-head p{
    margin-top:16px;font-size:1.05rem;color:var(--ink-soft);line-height:1.6;
  }

  /* ---------- programs ---------- */
  .programs-grid{
    display:grid;grid-template-columns:repeat(3,1fr);gap:24px;
  }
  .program-card{
    background:var(--paper);
    border:1px solid var(--line);
    border-radius:var(--radius);
    padding:32px 26px;
    box-shadow:var(--shadow);
    transition:transform .2s ease, box-shadow .2s ease;
  }
  .program-card:hover{transform:translateY(-6px);}
  .program-icon{
    width:52px;height:52px;border-radius:14px;
    display:flex;align-items:center;justify-content:center;
    margin-bottom:20px;
  }
  .program-card:nth-child(1) .program-icon{background:#e9f6dd;}
  .program-card:nth-child(2) .program-icon{background:#f4e6f5;}
  .program-card:nth-child(3) .program-icon{background:#fff3cc;}
  .program-card:nth-child(4) .program-icon{background:#e9f6dd;}
  .program-card:nth-child(5) .program-icon{background:#f4e6f5;}
  .program-card:nth-child(6) .program-icon{background:#fff3cc;}
  .program-card h3{font-size:1.15rem;color:var(--ink);margin-bottom:10px;}
  .program-card p{color:var(--ink-soft);font-size:0.96rem;line-height:1.55;}

  /* ---------- process ---------- */
  .process{background:var(--paper);border-top:1px solid var(--line);border-bottom:1px solid var(--line);}
  .process-steps{
    display:grid;grid-template-columns:repeat(4,1fr);gap:0;position:relative;
  }
  .process-step{
    position:relative;padding:0 22px 0 0;
  }
  .process-step .num{
    font-family:var(--display);font-size:1rem;font-weight:600;
    width:38px;height:38px;border-radius:50%;
    background:var(--green);color:#fff;
    display:flex;align-items:center;justify-content:center;
    margin-bottom:20px;
  }
  .process-step:nth-child(2) .num{background:var(--purple);}
  .process-step:nth-child(3) .num{background:var(--gold);color:var(--purple-deep);}
  .process-step:nth-child(4) .num{background:var(--green-dark);}
  .process-step h3{font-size:1.05rem;margin-bottom:8px;}
  .process-step p{color:var(--ink-soft);font-size:0.92rem;line-height:1.55;}
  .process-track{
    grid-column:1/-1;height:2px;background:repeating-linear-gradient(90deg,var(--line) 0 8px, transparent 8px 14px);
    margin-bottom:36px;
  }

  /* ---------- testimonial ---------- */
  .testimonial{
    background:var(--gold);
    position:relative;overflow:hidden;
  }
  .testimonial .wrap{
    display:grid;grid-template-columns:auto 1fr;gap:40px;align-items:center;
  }
  .quote-mark{
    font-family:var(--display);font-size:5.5rem;color:var(--purple-deep);opacity:0.35;line-height:1;
  }
  .testimonial blockquote{
    margin:0;font-family:var(--display);font-weight:500;
    font-size:clamp(1.3rem,2.4vw,1.9rem);color:var(--green-ink);line-height:1.35;
  }
  .testimonial cite{
    display:block;margin-top:20px;font-style:normal;font-weight:700;color:var(--purple-deep);font-size:0.95rem;
  }
  .testimonial cite span{
    display:block;font-weight:500;color:var(--ink);opacity:0.75;font-size:0.85rem;margin-top:2px;
  }

  /* ---------- cta ---------- */
  .cta-band{
    background:var(--green-ink);
    color:#fff;
    text-align:center;
    padding:88px 0;
  }
  .cta-band h2{font-size:clamp(1.8rem,3.4vw,2.6rem);color:#fff;}
  .cta-band p{margin-top:16px;color:#cfe3c2;font-size:1.05rem;max-width:52ch;margin-left:auto;margin-right:auto;}
  .cta-actions{display:flex;justify-content:center;gap:16px;margin-top:34px;flex-wrap:wrap;}

  /* ---------- footer ---------- */
  footer{background:var(--purple-deep);color:#e9dcea;padding:64px 0 28px;}
  .footer-grid{
    display:grid;grid-template-columns:1.4fr 1fr 1fr 1fr;gap:36px;padding-bottom:44px;
  }
  .footer-logo{height:34px;filter:brightness(0) invert(1);margin-bottom:16px;}
  .footer-grid p{color:#cdb8cf;font-size:0.92rem;line-height:1.6;max-width:32ch;}
  .footer-col h4{
    font-family:var(--body);font-size:0.85rem;text-transform:uppercase;letter-spacing:0.1em;
    color:#f2d9f4;margin-bottom:16px;
  }
  .footer-col ul{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:11px;}
  .footer-col a{text-decoration:none;color:#e9dcea;font-size:0.94rem;opacity:0.85;}
  .footer-col a:hover{opacity:1;text-decoration:underline;}
  .footer-bottom{
    border-top:1px solid rgba(255,255,255,0.14);
    padding-top:24px;
    display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;
    font-size:0.85rem;color:#c9b6cb;
  }
  .socials{display:flex;gap:12px;}
  .socials a{
    width:36px;height:36px;border-radius:50%;border:1px solid rgba(255,255,255,0.3);
    display:flex;align-items:center;justify-content:center;
  }

  /* ---------- reveal ---------- */
  [data-reveal]{opacity:0;transform:translateY(18px);transition:opacity .6s ease, transform .6s ease;}
  [data-reveal].in{opacity:1;transform:translateY(0);}

  @media (max-width:960px){
    .hero-grid{grid-template-columns:1fr;}
    .hero-art{margin-top:12px;max-width:340px;}
    .impact .wrap{grid-template-columns:repeat(2,1fr);}
    .programs-grid{grid-template-columns:repeat(2,1fr);}
    .process-steps{grid-template-columns:repeat(2,1fr);row-gap:32px;}
    .footer-grid{grid-template-columns:1fr 1fr;}
  }
  @media (max-width:720px){
    .nav-links, .nav-cta .btn-ghost{display:none;}
    .menu-btn{display:block;}
    .programs-grid{grid-template-columns:1fr;}
    .process-steps{grid-template-columns:1fr;}
    .impact .wrap{grid-template-columns:1fr 1fr;}
    .testimonial .wrap{grid-template-columns:1fr;text-align:left;}
    .quote-mark{display:none;}
    .footer-grid{grid-template-columns:1fr;}
  }
  @media (prefers-reduced-motion:reduce){
    html{scroll-behavior:auto;}
    [data-reveal]{opacity:1;transform:none;transition:none;}
    *{transition:none !important;}
  }
</style>
</head>
<body>

<header>
  <nav class="nav">
    <a href="#inicio" aria-label="Zajuna Campo, inicio">
      <img class="logo-mark" src="{{ asset('img/logo-zajuna-campo.png') }}" alt="Zajuna Campo">
    </a>
    <ul class="nav-links">
      <li><a href="#programas">Programas</a></li>
      <li><a href="#como-funciona">Cómo funciona</a></li>
      <li><a href="#comunidad">Comunidad</a></li>
      <li><a href="#contacto">Contacto</a></li>
    </ul>
    <div class="nav-cta">
      <a class="btn btn-ghost" href="{{ route('login') }}">Iniciar Sesion</a>
      <a class="btn btn-primary" href="{{ route('register') }}">Registrarme</a>
    </div>
    <button class="menu-btn" aria-label="Abrir menú">
      <span></span><span></span><span></span>
    </button>
  </nav>
</header>

<main>

  <!-- ================= HERO ================= -->
  <section class="hero" id="inicio" style="padding-top:68px;">
    <div class="wrap hero-grid">
      <div data-reveal>
        <p class="eyebrow">Economía popular y campesina</p>
        <h1>Sembramos tecnología para que el <em>campo colombiano</em> crezca por su propia mano.</h1>
        <p class="lead">Zajuna Campo centraliza la gestión del Minimarket, las Transferencias 
Tecnológicas, los Shows y los Proyectos de Investigación del Centro 
Agroindustrial La Angostura, dando trazabilidad a las ventas de nuestros 
proveedores y visibilidad a la economía popular y campesina.  </p>
        <div class="hero-actions">
          <a class="btn btn-primary" href="#programas">Ver programas</a>
          <a class="btn btn-ghost" href="#como-funciona">Cómo funciona</a>
        </div>
        <div class="hero-stats">
          <div><strong>1.240</strong><span>Ventas registradas</span></div>
          <div><strong>32</strong><span>Proveedores activos</span></div>
          <div><strong>96</strong><span>Actividades realizadas</span></div>
        </div>
      </div>

      <div class="hero-art" data-reveal>
        <svg viewBox="0 0 460 460" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Ilustración de un sol sobre un paisaje de cultivos en los colores de Zajuna Campo">
          <circle cx="230" cy="230" r="230" fill="#fff3cc"/>
          <g stroke="#fdc300" stroke-width="6" stroke-linecap="round">
            <line x1="230" y1="55" x2="230" y2="85"/>
            <line x1="340" y1="90" x2="320" y2="112"/>
            <line x1="382" y1="180" x2="352" y2="188"/>
            <line x1="120" y1="90" x2="140" y2="112"/>
            <line x1="78" y1="180" x2="108" y2="188"/>
          </g>
          <circle cx="230" cy="205" r="78" fill="#fdc300"/>
          <path d="M195 195 q35 -30 70 0" stroke="#71277a" stroke-width="5" fill="none" stroke-linecap="round"/>
          <path d="M215 235 v-30 q0 -14 14 -14" stroke="#39a900" stroke-width="6" fill="none" stroke-linecap="round"/>
          <path d="M0 300 Q 115 260 230 300 T 460 300 V460 H0 Z" fill="#39a900"/>
          <path d="M0 335 Q 115 300 230 335 T 460 335 V460 H0 Z" fill="#2c7f04" opacity="0.85"/>
          <path d="M0 372 Q 115 345 230 372 T 460 372 V460 H0 Z" fill="#71277a"/>
          <g stroke="#fffaec" stroke-width="5" stroke-linecap="round">
            <path d="M90 330 v-26 M90 304 q10 0 10 -14" fill="none"/>
            <path d="M150 345 v-30 M150 315 q12 0 12 -16" fill="none"/>
            <path d="M330 340 v-28 M330 312 q-11 0 -11 -15" fill="none"/>
          </g>
        </svg>
      </div>
    </div>

    <div class="wave-divider" aria-hidden="true">
      <svg viewBox="0 0 1200 90" preserveAspectRatio="none">
        <path d="M0,40 C200,90 400,0 600,40 C800,80 1000,10 1200,40 L1200,90 L0,90 Z" fill="#39a900"/>
        <path d="M0,55 C200,100 400,20 600,55 C800,90 1000,25 1200,55 L1200,90 L0,90 Z" fill="#fdc300" opacity="0.9"/>
        <path d="M0,68 C200,105 400,35 600,68 C800,100 1000,40 1200,68 L1200,90 L0,90 Z" fill="#71277a"/>
      </svg>
    </div>
  </section>

  <!-- ================= IMPACT ================= -->
  <div class="impact">
    <div class="wrap">
      <div class="impact-item" data-reveal>
        <strong>4</strong>
        <p>4
módulos integrados: Minimarket, Transferencias Tecnológicas, Shows 
e Investigación</p>
      </div>
      <div class="impact-item" data-reveal>
        <strong>+100%</strong>
        <p>trazabilidad de ventas, inventario y proyectos en un solo sistema</p>
      </div>
      <div class="impact-item" data-reveal>
        <strong>3</strong>
        <p>roles de acceso: superadministrador, equipo operativo y proveedores</p>
      </div>
      <div class="impact-item" data-reveal>
        <strong>100%</strong>
        <p>seguro y conforme a la Ley 1581 de 2012 de Protección de Datos</p>
      </div>
    </div>
  </div>

  <!-- ================= PROGRAMAS ================= -->
  <section id="programas">
    <div class="wrap">
      <div class="section-head" data-reveal>
        <p class="eyebrow">Lo que ofrecemos</p>
        <h2>Un sistema para cada actividad de Zajuna Campo</h2>
        <p>El Sistema de Información Zajuna Campo (SISC) centraliza el registro, 
seguimiento y reporte de las cuatro actividades de la estrategia. 
Elige el módulo que quieres conocer.</p>
      </div>

      <div class="programs-grid">
        <article class="program-card" data-reveal>
          <div class="program-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#39a900" stroke-width="2" stroke-linecap="round"><path d="M12 21c-5-3-8-6-8-11a8 8 0 0 1 16 0c0 5-3 8-8 11z"/><path d="M12 12v6"/></svg>
          </div>
          <h3>Minimarket: ventas e inventario</h3>
          <p>Registro de proveedores, control de inventario en consignación 
y ventas vinculadas a cada proveedor, con descuento automático 
de existencias.</p>
        </article>

        <article class="program-card" data-reveal>
          <div class="program-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#71277a" stroke-width="2" stroke-linecap="round"><circle cx="9" cy="7" r="3"/><circle cx="17" cy="9" r="2.4"/><path d="M2 21c0-4 3-6 7-6s7 2 7 6"/><path d="M15 15c3 0 5 2 5 6"/></svg>
          </div>
          <h3>Transferencias tecnológicas y shows</h3>
          <p>Planeación y cronograma visual del auditorio y el showroom, 
registro de ejecución, asistencia y participantes de cada evento.</p>
        </article>

        <article class="program-card" data-reveal>
          <div class="program-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#e0aa00" stroke-width="2" stroke-linecap="round"><path d="M3 3h2l2.6 12.6a2 2 0 0 0 2 1.6h8a2 2 0 0 0 2-1.6L21 8H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg>
          </div>
          <h3>Acceso a mercados</h3>
          <p>Visualización de productos en la plataforma de ventas, 
conectando la producción de los proveedores con la comunidad.</p>
        </article>

        <article class="program-card" data-reveal>
          <div class="program-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#39a900" stroke-width="2" stroke-linecap="round"><path d="M4 19h16"/><path d="M6 19V9l4-4 4 4v10"/><path d="M14 19v-6l4-2v8"/></svg>
          </div>
          <h3>Proyectos de investigación</h3>
          <p>Registro de proyectos, vinculación de aprendices e instructores, 
asignación de tareas y seguimiento del avance.</p>
        </article>

        <article class="program-card" data-reveal>
          <div class="program-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#71277a" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/><path d="M8 3v4M16 3v4"/></svg>
          </div>
          <h3>Alertas y reportes automáticos</h3>
          <p>Alertas de stock mínimo, órdenes de pedido automáticas y reportes 
en PDF y Excel con dashboards por módulo.</p>
        </article>

        <article class="program-card" data-reveal>
          <div class="program-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#e0aa00" stroke-width="2" stroke-linecap="round"><path d="M12 2 4 6v6c0 5 3.4 8.4 8 10 4.6-1.6 8-5 8-10V6l-8-4z"/></svg>
          </div>
          <h3>Roles y seguridad</h3>
          <p>Acceso diferenciado por roles (superadministrador, equipo operativo 
y proveedores) con auditoría de operaciones y protección de datos.</p>
        </article>
      </div>
    </div>
  </section>

  <!-- ================= COMO FUNCIONA ================= -->
  <section class="process" id="como-funciona">
    <div class="wrap">
      <div class="section-head" data-reveal>
        <p class="eyebrow">Cómo funciona</p>
        <h2>Cuatro pasos, del registro al reporte</h2>
        <p>El sistema sigue un flujo claro para que cada actividad de Zajuna 
Campo quede registrada y sea fácil de consultar.  </p>
      </div>
      <div class="process-track"></div>
      <div class="process-steps">
        <div class="process-step" data-reveal>
          <div class="num">1</div>
          <h3>Registro y acceso</h3>
          <p>Cada usuario ingresa con su cuenta y rol asignado: 
superadministrador, equipo operativo o proveedor.</p>
        </div>
        <div class="process-step" data-reveal>
          <div class="num">2</div>
          <h3>Gestión por módulo</h3>
          <p>Se registran ventas, inventario, transferencias tecnológicas, 
shows o avances de proyectos de investigación, según el módulo.</p>
        </div>
        <div class="process-step" data-reveal>
          <div class="num">3</div>
          <h3>Alertas y seguimiento</h3>
          <p>El sistema genera alertas automáticas de stock mínimo y de 
tareas próximas a vencer, sin necesidad de revisión manual.</p>
        </div>
        <div class="process-step" data-reveal>
          <div class="num">4</div>
          <h3>Reportes y consulta</h3>
          <p>Se generan reportes y dashboards en PDF y Excel, y los 
proveedores consultan sus propias ventas en modo solo lectura.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= TESTIMONIO ================= -->
  <section class="testimonial" id="comunidad">
    <div class="wrap">
      <div class="quote-mark" aria-hidden="true">&ldquo;</div>
      <div data-reveal>
        <blockquote>Antes no sabíamos cuánto se vendía de nuestros productos en el Minimarket. Ahora entramos al sistema y vemos las ventas, el inventario y hasta las alertas cuando algo se está agotando.&rdquo;</blockquote>
        <cite>Proveedor del Minimarket Zajuna Campo<span>Café y productos agroindustriales</span></cite>
      </div>
    </div>
  </section>

  <!-- ================= CTA ================= -->
  <section class="cta-band">
    <div class="wrap">
      <h2>Su producto también puede tener visibilidad en el Minimarket</h2>
      <p>El registro es gratuito y está abierto a proveedores internos y 
externos de la estrategia Zajuna Campo.</p>
      <div class="cta-actions">
        <a class="btn btn-on-dark" href="{{ route('register') }}">Registrarme</a>
        <a class="btn btn-ghost" style="border-color:#fff;color:#fff;" href="#programas">Explorar programas</a>
      </div>
    </div>
  </section>

</main>

<footer id="contacto">
  <div class="wrap">
    <div class="footer-grid">
      <div>
        <img class="footer-logo" src="{{ asset('img/logo-zajuna-campo.png') }}" alt="Zajuna Campo">
        <p>Fortalecimiento y desarrollo de la economía popular y campesina. Un programa del Servicio Nacional de Aprendizaje para el territorio rural colombiano.</p>
      </div>
      <div class="footer-col">
        <h4>Programas</h4>
        <ul>
          <li><a href="#programas">Formación técnica</a></li>
          <li><a href="#programas">Fortalecimiento asociativo</a></li>
          <li><a href="#programas">Acceso a mercados</a></li>
          <li><a href="#programas">Créditos y capital</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Zajuna Campo</h4>
        <ul>
          <li><a href="#inicio">Inicio</a></li>
          <li><a href="#como-funciona">Cómo funciona</a></li>
          <li><a href="#comunidad">Comunidad</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contacto</h4>
        <ul>
          <li><a href="mailto:contacto@zajunacampo.gov.co">contacto@zajunacampo.gov.co</a></li>
          <li><a href="tel:018000000000">01 8000 00 00 00</a></li>
          <li><a href="#">Puntos de atención regional</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; 2026 Zajuna Campo · Servicio Nacional de Aprendizaje</span>
      <div class="socials" aria-hidden="true">
        <a href="#" aria-label="Facebook">f</a>
        <a href="#" aria-label="Instagram">ig</a>
        <a href="#" aria-label="YouTube">yt</a>
      </div>
    </div>
  </div>
</footer>

<script>
  // Menú móvil simple
  const menuBtn = document.querySelector('.menu-btn');
  const navLinks = document.querySelector('.nav-links');
  if (menuBtn) {
    menuBtn.addEventListener('click', () => {
      const open = navLinks.style.display === 'flex';
      navLinks.style.display = open ? 'none' : 'flex';
      navLinks.style.flexDirection = 'column';
      navLinks.style.position = 'absolute';
      navLinks.style.top = '68px';
      navLinks.style.left = '0';
      navLinks.style.right = '0';
      navLinks.style.background = '#fffaec';
      navLinks.style.padding = '20px 28px';
      navLinks.style.gap = '18px';
      navLinks.style.borderBottom = '1px solid #e7e0cc';
    });
  }

  // Revelado suave al hacer scroll
  const revealEls = document.querySelectorAll('[data-reveal]');
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('in');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach(el => io.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('in'));
  }
</script>

</body>
</html>