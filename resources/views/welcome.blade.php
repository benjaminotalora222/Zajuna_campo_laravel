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
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&family=Work+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
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
    --body:'Fredoka', sans-serif;
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
    font-family:var(--display);
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:0.12em;
    font-size:0.82rem;
  }
  button, .btn{
    font-family:var(--display);
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
    font-family:var(--display);font-size:0.9rem;text-transform:uppercase;letter-spacing:0.08em;
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

  /* ---------- catalogo ---------- */
  .catalogo-section{
    background:#faf7ee;
    border-bottom:1px solid var(--line);
    position:relative;
    padding:96px 0;
  }
  .catalogo-section .wrap{
    max-width:1240px;
  }
  .catalogo-toolbar{
    display:flex;
    flex-wrap:wrap;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    margin-bottom:20px;
    background:var(--paper);
    padding:16px 20px;
    border-radius:var(--radius);
    border:1px solid var(--line);
    box-shadow:0 6px 20px -10px rgba(34,48,28,0.12);
  }
  .catalogo-search-box{
    position:relative;
    flex:1;
    min-width:260px;
    max-width:460px;
  }
  .catalogo-search-box input{
    width:100%;
    padding:12px 18px 12px 44px;
    border-radius:999px;
    border:1.5px solid var(--line);
    background:#faf8f2;
    font-family:var(--body);
    font-size:0.92rem;
    color:var(--ink);
    outline:none;
    transition:border-color .2s, box-shadow .2s, background .2s;
  }
  .catalogo-search-box input:focus{
    background:#fff;
    border-color:var(--green);
    box-shadow:0 0 0 3px rgba(57,169,0,0.15);
  }
  .catalogo-search-box svg{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:#7c8774;
    pointer-events:none;
  }
  .catalogo-count{
    font-size:0.9rem;
    color:var(--ink-soft);
    font-weight:600;
  }
  .catalogo-categories-wrap{
    margin-bottom:28px;
  }
  .catalogo-filters{
    display:flex;
    flex-wrap:wrap;
    gap:10px;
    align-items:center;
  }
  .filter-pill{
    background:var(--paper);
    color:var(--ink);
    border:1.5px solid var(--line);
    padding:8px 18px;
    border-radius:999px;
    font-size:0.86rem;
    font-weight:600;
    cursor:pointer;
    transition:all .18s ease;
    font-family:var(--body);
  }
  .filter-pill:hover{
    background:#f4efe0;
    border-color:#d5cbb3;
  }
  .filter-pill.active{
    background:var(--purple);
    color:#fff;
    border-color:var(--purple);
    box-shadow:0 4px 14px -3px rgba(113,39,122,0.45);
  }

  .catalogo-grid{
    display:grid;
    grid-template-columns:repeat(4, minmax(0, 1fr));
    gap:20px;
  }
  @media (max-width:1120px){
    .catalogo-grid{grid-template-columns:repeat(3, minmax(0, 1fr));}
  }
  @media (max-width:820px){
    .catalogo-grid{grid-template-columns:repeat(2, minmax(0, 1fr));gap:16px;}
  }
  @media (max-width:540px){
    .catalogo-grid{grid-template-columns:1fr;}
  }
  .producto-card{
    background:var(--paper);
    border:1px solid var(--line);
    border-radius:var(--radius);
    overflow:hidden;
    box-shadow:0 10px 25px -12px rgba(34,48,28,0.18);
    display:flex;
    flex-direction:column;
    transition:transform .22s ease, box-shadow .22s ease, border-color .22s ease;
  }
  .producto-card:hover{
    transform:translateY(-6px);
    box-shadow:0 20px 35px -16px rgba(34,48,28,0.28);
    border-color:#d5cbb3;
  }
  .producto-img-wrap{
    position:relative;
    width:100%;
    height:190px;
    background:#f2ecdb;
    overflow:hidden;
  }
  .producto-img-wrap img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:transform .35s ease;
  }
  .producto-card:hover .producto-img-wrap img{
    transform:scale(1.06);
  }
  .producto-img-placeholder{
    width:100%;
    height:100%;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg, #f7f1df 0%, #ebe2ca 100%);
    color:var(--green-ink);
    opacity:0.85;
    gap:8px;
  }
  .producto-tag-cat{
    position:absolute;
    top:12px;
    left:12px;
    background:rgba(113,39,122,0.92);
    backdrop-filter:blur(4px);
    color:#fff;
    padding:4px 12px;
    border-radius:999px;
    font-size:0.72rem;
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:0.04em;
    box-shadow:0 4px 8px rgba(0,0,0,0.15);
  }
  .producto-tag-stock{
    position:absolute;
    top:12px;
    right:12px;
    padding:4px 12px;
    border-radius:999px;
    font-size:0.72rem;
    font-weight:700;
    letter-spacing:0.02em;
    box-shadow:0 4px 8px rgba(0,0,0,0.15);
  }
  .stock-disponible{background:#39a900;color:#fff;}
  .stock-agotado{background:#e11d48;color:#fff;}

  .producto-body{
    padding:18px 16px;
    display:flex;
    flex-direction:column;
    flex:1;
  }
  .producto-prov{
    font-size:0.78rem;
    color:var(--green-dark);
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:0.04em;
    margin-bottom:6px;
    display:flex;
    align-items:center;
    gap:5px;
  }
  .producto-title{
    font-size:1.12rem;
    color:var(--green-ink);
    margin-bottom:6px;
    font-weight:700;
    line-height:1.2;
  }
  .producto-desc{
    font-size:0.85rem;
    color:var(--ink-soft);
    line-height:1.45;
    margin-bottom:14px;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
  }
  .producto-footer{
    margin-top:auto;
    padding-top:14px;
    border-top:1px dashed var(--line);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
  }
  .producto-precio-wrap{
    display:flex;
    flex-direction:column;
  }
  .producto-precio-label{
    font-size:0.72rem;
    text-transform:uppercase;
    letter-spacing:0.05em;
    color:var(--ink-soft);
    font-weight:600;
  }
  .producto-precio{
    font-size:1.25rem;
    font-weight:700;
    color:var(--purple-deep);
    font-family:var(--display);
    line-height:1.1;
  }
  .producto-unidad{
    font-size:0.75rem;
    color:var(--ink-soft);
    font-weight:500;
  }
  .btn-ver-producto{
    background:#f4efe0;
    color:var(--purple-deep);
    border:1px solid var(--line);
    padding:7px 12px;
    border-radius:999px;
    font-size:0.78rem;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:4px;
    cursor:pointer;
    transition:all .18s ease;
  }
  .btn-ver-producto:hover{
    background:var(--purple);
    color:#fff;
    border-color:var(--purple);
  }

  /* Empty State */
  .catalogo-vacio{
    grid-column:1/-1;
    text-align:center;
    padding:60px 20px;
    background:var(--paper);
    border-radius:var(--radius);
    border:2px dashed var(--line);
  }
  .catalogo-vacio svg{
    margin:0 auto 16px;
    color:#a39f91;
  }
  .catalogo-vacio h3{
    font-size:1.3rem;
    color:var(--ink);
    margin-bottom:8px;
  }
  .catalogo-vacio p{
    color:var(--ink-soft);
    font-size:0.95rem;
  }

  /* Modal de Detalle de Producto */
  .modal-overlay{
    position:fixed;
    inset:0;
    background:rgba(18,28,14,0.65);
    backdrop-filter:blur(6px);
    z-index:999;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
    opacity:0;
    pointer-events:none;
    transition:opacity .25s ease;
  }
  .modal-overlay.active{
    opacity:1;
    pointer-events:auto;
  }
  .modal-card{
    background:var(--cream);
    max-width:620px;
    width:100%;
    border-radius:24px;
    overflow:hidden;
    box-shadow:0 25px 60px -15px rgba(0,0,0,0.4);
    border:1px solid var(--line);
    transform:scale(0.95) translateY(10px);
    transition:transform .25s ease;
    max-height:90vh;
    display:flex;
    flex-direction:column;
  }
  .modal-overlay.active .modal-card{
    transform:scale(1) translateY(0);
  }
  .modal-header-img{
    position:relative;
    height:260px;
    background:#eee6d2;
    overflow:hidden;
    flex-shrink:0;
  }
  .modal-header-img img{
    width:100%;
    height:100%;
    object-fit:cover;
  }
  .modal-close-btn{
    position:absolute;
    top:16px;
    right:16px;
    width:38px;
    height:38px;
    border-radius:50%;
    background:rgba(255,255,255,0.92);
    border:none;
    color:var(--ink);
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    box-shadow:0 4px 12px rgba(0,0,0,0.15);
    transition:background .18s, transform .18s;
  }
  .modal-close-btn:hover{
    background:#fff;
    transform:scale(1.06);
  }
  .modal-content{
    padding:28px;
    overflow-y:auto;
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
      <li><a href="#catalogo">Catálogo</a></li>
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
          <a class="btn btn-primary" href="#catalogo">Explorar catálogo</a>
          <a class="btn btn-ghost" href="#programas">Conocer programas</a>
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

  @php
    $productos = $productos ?? collect();
    $categorias = $categorias ?? collect();
  @endphp

  <!-- ================= CATÁLOGO DE PRODUCTOS ================= -->
  <section class="catalogo-section" id="catalogo">
    <div class="wrap">
      <div class="section-head" data-reveal>
        <p class="eyebrow" style="color:var(--green-dark);">Minimarket Zajuna Campo</p>
        <h2>Catálogo de Productos</h2>
        <p>Conoce los productos registrados de nuestros proveedores locales y del Centro Agroindustrial La Angostura. Precios directos, calidad del campo y trazabilidad garantizada sin necesidad de iniciar sesión.</p>
      </div>

      {{-- Barra de búsqueda y contador --}}
      <div class="catalogo-toolbar" data-reveal>
        <div class="catalogo-search-box">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
          </svg>
          <input type="text" id="catalogo-search-input" placeholder="Buscar por producto, categoría o productor..." autocomplete="off">
        </div>

        <div class="catalogo-count" id="catalogo-counter">
          <span>Mostrando {{ $productos->count() }} {{ $productos->count() === 1 ? 'producto' : 'productos' }}</span>
        </div>
      </div>

      @if($categorias->isNotEmpty())
      <div class="catalogo-categories-wrap" data-reveal>
        <div class="catalogo-filters" id="category-filters">
          <button type="button" class="filter-pill active" data-category="all">
            Todos ({{ $productos->count() }})
          </button>
          @foreach($categorias as $cat)
            @php
              $conteoCat = $productos->where('categoria', $cat)->count();
            @endphp
            <button type="button" class="filter-pill" data-category="{{ Str::slug($cat) }}">
              {{ $cat }} ({{ $conteoCat }})
            </button>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Grid de productos --}}
      <div class="catalogo-grid" id="catalogo-grid" data-reveal>
        @forelse($productos as $producto)
          @php
            $tieneStock = ($producto->stock_calculado ?? 0) > 0;
            $catSlug = $producto->categoria ? Str::slug($producto->categoria) : 'general';
            $imgUrl = $producto->imagen ? asset('storage/' . $producto->imagen) : null;
            $searchString = strtolower($producto->nombre . ' ' . ($producto->categoria ?? '') . ' ' . ($producto->proveedor->nombre ?? '') . ' ' . ($producto->descripcion ?? ''));
          @endphp
          <article class="producto-card"
                   data-card="producto"
                   data-nombre="{{ strtolower($producto->nombre) }}"
                   data-categoria="{{ $catSlug }}"
                   data-search="{{ $searchString }}"
                   data-nombre-display="{{ $producto->nombre }}"
                   data-categoria-display="{{ $producto->categoria ?? 'General' }}"
                   data-proveedor-display="{{ $producto->proveedor->nombre ?? 'Centro Agroindustrial La Angostura' }}"
                   data-precio-display="{{ number_format($producto->precio ?? 0, 0, ',', '.') }}"
                   data-unidad-display="{{ $producto->unidad ?? 'ud' }}"
                   data-descripcion-display="{{ $producto->descripcion ?? 'Producto campesino de calidad garantizada disponible en Minimarket.' }}"
                   data-imagen-display="{{ $imgUrl ?? '' }}"
                   data-disponible-display="{{ $tieneStock ? '1' : '0' }}">

            <div class="producto-img-wrap">
              @if($imgUrl)
                <img src="{{ $imgUrl }}" alt="{{ $producto->nombre }}" loading="lazy"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="producto-img-placeholder" style="display:none;">
                  <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                  </svg>
                  <span style="font-size:0.75rem;font-weight:600;">Zajuna Campo</span>
                </div>
              @else
                <div class="producto-img-placeholder">
                  <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                  </svg>
                  <span style="font-size:0.75rem;font-weight:600;">Zajuna Campo</span>
                </div>
              @endif

              @if($producto->categoria)
                <span class="producto-tag-cat">{{ $producto->categoria }}</span>
              @endif

              @if($tieneStock)
                <span class="producto-tag-stock stock-disponible">Disponible</span>
              @else
                <span class="producto-tag-stock stock-agotado">Agotado</span>
              @endif
            </div>

            <div class="producto-body">
              <span class="producto-prov">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-3"/></svg>
                {{ $producto->proveedor->nombre ?? 'Centro La Angostura' }}
              </span>

              <h3 class="producto-title">{{ $producto->nombre }}</h3>

              @if($producto->descripcion)
                <p class="producto-desc">{{ $producto->descripcion }}</p>
              @else
                <p class="producto-desc" style="opacity:0.65;font-style:italic;">Producto campesino de calidad garantizada disponible en Minimarket.</p>
              @endif

              <div class="producto-footer">
                <div class="producto-precio-wrap">
                  <span class="producto-precio-label">Precio</span>
                  <div style="display:flex;align-items:baseline;gap:4px;">
                    <span class="producto-precio">${{ number_format($producto->precio ?? 0, 0, ',', '.') }}</span>
                    <span class="producto-unidad">/ {{ $producto->unidad ?? 'ud' }}</span>
                  </div>
                </div>

                <button type="button" class="btn-ver-producto" onclick="abrirModalCatalogo(this)">
                  <span>Ver detalle</span>
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                  </svg>
                </button>
              </div>
            </div>

          </article>
        @empty
          <div class="catalogo-vacio">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            <h3>Aún no hay productos registrados</h3>
            <p>Pronto estarán disponibles los productos de nuestros proveedores campesinos.</p>
          </div>
        @endforelse

        {{-- Estado cuando búsqueda no arroja resultados --}}
        <div class="catalogo-vacio" id="catalogo-no-results" style="display:none;">
          <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            <line x1="8" y1="11" x2="14" y2="11"></line>
          </svg>
          <h3>No encontramos productos coincidentes</h3>
          <p>Prueba buscando con otra palabra o revisa otra categoría.</p>
          <button type="button" class="btn btn-ghost" style="margin-top:16px;" onclick="resetCatalogoFiltros()">
            Ver todos los productos
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= MODAL DETALLE PRODUCTO ================= -->
  <div class="modal-overlay" id="modal-producto" onclick="cerrarModalCatalogo(event)">
    <div class="modal-card" onclick="event.stopPropagation()">
      <div class="modal-header-img" id="modal-img-container">
        <img id="modal-img" src="" alt="" style="display:none;">
        <div class="producto-img-placeholder" id="modal-img-placeholder" style="display:flex;">
          <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <path d="M16 10a4 4 0 0 1-8 0"></path>
          </svg>
          <span style="font-size:0.85rem;font-weight:600;">Zajuna Campo</span>
        </div>
        <button type="button" class="modal-close-btn" onclick="cerrarModalCatalogoDirecto()" aria-label="Cerrar ventana">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        <span class="producto-tag-cat" id="modal-categoria"></span>
      </div>

      <div class="modal-content">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:8px;">
          <span class="producto-prov" id="modal-proveedor"></span>
          <span class="producto-tag-stock" id="modal-stock-badge"></span>
        </div>

        <h2 id="modal-nombre" style="font-size:1.6rem;color:var(--green-ink);margin-bottom:12px;line-height:1.2;"></h2>

        <div style="background:#f4efe0;padding:14px 18px;border-radius:14px;border:1px solid var(--line);display:flex;align-items:baseline;justify-content:space-between;margin-bottom:18px;">
          <span style="font-size:0.85rem;font-weight:600;color:var(--ink-soft);text-transform:uppercase;">Precio al público</span>
          <div style="display:flex;align-items:baseline;gap:6px;">
            <span id="modal-precio" style="font-size:1.75rem;font-weight:700;color:var(--purple-deep);font-family:var(--display);">$0</span>
            <span id="modal-unidad" style="font-size:0.9rem;color:var(--ink-soft);font-weight:500;"></span>
          </div>
        </div>

        <h4 style="font-size:0.95rem;color:var(--ink);margin-bottom:6px;font-family:var(--display);">Descripción del producto</h4>
        <p id="modal-descripcion" style="font-size:0.95rem;color:var(--ink-soft);line-height:1.6;margin-bottom:20px;white-space:pre-line;"></p>

        <div style="background:#eaf6de;border:1px solid #cce8b5;border-radius:14px;padding:14px 18px;display:flex;align-items:flex-start;gap:12px;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#28790a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:2px;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="16" x2="12" y2="12"></line>
            <line x1="12" y1="8" x2="12.01" y2="8"></line>
          </svg>
          <div style="font-size:0.86rem;color:#1f3410;line-height:1.45;">
            <strong>Disponible en el Minimarket La Angostura</strong><br>
            Puedes adquirir este producto directamente en las instalaciones del Centro Agroindustrial La Angostura sin necesidad de registro previo.
          </div>
        </div>

        <div style="display:flex;justify-content:flex-end;margin-top:20px;">
          <button type="button" class="btn btn-ghost" onclick="cerrarModalCatalogoDirecto()">Cerrar</button>
        </div>
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
          <li><a href="#catalogo">Catálogo de productos</a></li>
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

  // Cerrar menú móvil al hacer clic en un enlace
  document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 720 && navLinks) {
        navLinks.style.display = 'none';
      }
    });
  });

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

  // ================= CATÁLOGO: BÚSQUEDA Y FILTRADO =================
  let activeCat = 'all';
  let searchTerm = '';

  const searchInput = document.getElementById('catalogo-search-input');
  const filterPills = document.querySelectorAll('.filter-pill');
  const cards = document.querySelectorAll('.producto-card');
  const counterEl = document.getElementById('catalogo-counter');
  const noResultsEl = document.getElementById('catalogo-no-results');

  function filtrarCatalogo() {
    let visibles = 0;
    const term = (searchTerm || '').trim().toLowerCase();

    cards.forEach(card => {
      const cardCat = card.getAttribute('data-categoria') || '';
      const cardSearch = (card.getAttribute('data-search') || '').toLowerCase();

      const matchCat = (activeCat === 'all') || (cardCat === activeCat);
      const matchSearch = !term || cardSearch.includes(term);

      if (matchCat && matchSearch) {
        card.style.display = 'flex';
        visibles++;
      } else {
        card.style.display = 'none';
      }
    });

    if (counterEl) {
      counterEl.innerHTML = `<span>Mostrando ${visibles} de ${cards.length} ${cards.length === 1 ? 'producto' : 'productos'}</span>`;
    }

    if (noResultsEl) {
      noResultsEl.style.display = (visibles === 0 && cards.length > 0) ? 'block' : 'none';
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchTerm = e.target.value;
      filtrarCatalogo();
    });
  }

  filterPills.forEach(pill => {
    pill.addEventListener('click', () => {
      filterPills.forEach(p => p.classList.remove('active'));
      pill.classList.add('active');
      activeCat = pill.getAttribute('data-category') || 'all';
      filtrarCatalogo();
    });
  });

  window.resetCatalogoFiltros = function() {
    if (searchInput) searchInput.value = '';
    searchTerm = '';
    activeCat = 'all';
    filterPills.forEach(p => {
      p.classList.toggle('active', p.getAttribute('data-category') === 'all');
    });
    filtrarCatalogo();
  };

  // ================= CATÁLOGO: MODAL DETALLE =================
  const modal = document.getElementById('modal-producto');
  const modalImg = document.getElementById('modal-img');
  const modalImgPlaceholder = document.getElementById('modal-img-placeholder');
  const modalCat = document.getElementById('modal-categoria');
  const modalStockBadge = document.getElementById('modal-stock-badge');
  const modalProv = document.getElementById('modal-proveedor');
  const modalNombre = document.getElementById('modal-nombre');
  const modalPrecio = document.getElementById('modal-precio');
  const modalUnidad = document.getElementById('modal-unidad');
  const modalDesc = document.getElementById('modal-descripcion');

  window.abrirModalCatalogo = function(btn) {
    const card = btn.closest('.producto-card');
    if (!card) return;

    const nombre = card.getAttribute('data-nombre-display') || 'Producto';
    const categoria = card.getAttribute('data-categoria-display') || 'General';
    const proveedor = card.getAttribute('data-proveedor-display') || 'Centro Agroindustrial La Angostura';
    const precio = card.getAttribute('data-precio-display') || '0';
    const unidad = card.getAttribute('data-unidad-display') || 'ud';
    const descripcion = card.getAttribute('data-descripcion-display') || 'Producto campesino de calidad garantizada disponible en Minimarket.';
    const imagen = card.getAttribute('data-imagen-display') || '';
    const disponible = card.getAttribute('data-disponible-display') === '1';

    if (modalNombre) modalNombre.textContent = nombre;
    if (modalCat) modalCat.textContent = categoria;
    if (modalProv) {
      modalProv.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-3"/></svg> ` + proveedor;
    }
    if (modalPrecio) modalPrecio.textContent = '$' + precio;
    if (modalUnidad) modalUnidad.textContent = '/ ' + unidad;
    if (modalDesc) modalDesc.textContent = descripcion;

    if (modalStockBadge) {
      modalStockBadge.className = 'producto-tag-stock ' + (disponible ? 'stock-disponible' : 'stock-agotado');
      modalStockBadge.textContent = disponible ? 'Disponible' : 'Agotado';
    }

    if (imagen) {
      modalImg.src = imagen;
      modalImg.style.display = 'block';
      modalImgPlaceholder.style.display = 'none';
    } else {
      modalImg.src = '';
      modalImg.style.display = 'none';
      modalImgPlaceholder.style.display = 'flex';
    }

    if (modal) {
      modal.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  };

  window.cerrarModalCatalogo = function(e) {
    if (e.target === modal) {
      cerrarModalCatalogoDirecto();
    }
  };

  window.cerrarModalCatalogoDirecto = function() {
    if (modal) {
      modal.classList.remove('active');
      document.body.style.overflow = '';
    }
  };

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal && modal.classList.contains('active')) {
      cerrarModalCatalogoDirecto();
    }
  });
</script>

</body>
</html>