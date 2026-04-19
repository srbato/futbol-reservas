<!doctype html>
<html lang="es-AR">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'TuCancha')</title>
  <link rel="alternate" hreflang="es-AR" href="{{ url()->current() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
  <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta name="description" content="@yield('meta_description', 'TuCancha — Reservá canchas de fútbol, tenis y más deportes online. Encontrá el complejo más cercano y confirmá tu turno al instante.')">

  <link rel="canonical" href="{{ url()->current() }}">
  {{-- Open Graph --}}
  @php
    $ogTitle = trim($__env->yieldContent('og_title')) ?: trim($__env->yieldContent('title')) ?: 'TuCancha';
    $ogDesc  = trim($__env->yieldContent('og_description')) ?: trim($__env->yieldContent('meta_description')) ?: 'TuCancha — Reservá canchas de fútbol, tenis y más deportes online.';
    $ogImage = trim($__env->yieldContent('og_image')) ?: asset('images/og-default.png');
  @endphp
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="TuCancha">
  <meta property="og:title" content="{{ $ogTitle }}">
  <meta property="og:description" content="{{ $ogDesc }}">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:image" content="{{ $ogImage }}">
  {{-- Twitter Card --}}
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $ogTitle }}">
  <meta name="twitter:description" content="{{ $ogDesc }}">
  <meta name="twitter:image" content="{{ $ogImage }}">
  {{-- JSON-LD Structured Data --}}
  <script type="application/ld+json">
  @php
  echo json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'Organization',
      'name' => 'TuCancha',
      'url' => url('/'),
      'logo' => asset('images/logo-multicolor.svg'),
      'description' => 'Plataforma argentina de reservas de canchas deportivas online.',
      'contactPoint' => [
          '@type' => 'ContactPoint',
          'email' => 'tucancha10@gmail.com',
          'contactType' => 'customer service',
          'availableLanguage' => 'Spanish',
      ],
      'areaServed' => ['@type' => 'Country', 'name' => 'Argentina'],
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  @endphp
  </script>
  <script type="application/ld+json">
  @php
  echo json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'WebSite',
      'name' => 'TuCancha',
      'url' => url('/'),
      'potentialAction' => [
          '@type' => 'SearchAction',
          'target' => url('/venues') . '?search={search_term_string}',
          'query-input' => 'required name=search_term_string',
      ],
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  @endphp
  </script>
  @stack('jsonld')

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
  <link rel="stylesheet" href="/css/design-tokens.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }

    body {
      margin: 0;
      font-family: 'Sora', system-ui, -apple-system, sans-serif;
      background: #050505;
      color: #e8e8e8;
      line-height: 1.6;

      /* Dark theme variable overrides */
      --color-text: #e8e8e8;
      --color-text-secondary: #a0a0a0;
      --color-text-muted: #666;
      --color-text-inverse: #050505;
      --color-bg: #111;
      --color-bg-page: #050505;
      --color-bg-card: #111;
      --color-bg-hover: #1a1a1a;
      --color-bg-dark: #22c55e;
      --color-border: rgba(255,255,255,.1);
      --color-border-light: rgba(255,255,255,.06);
      --shadow-sm: 0 2px 12px rgba(0,0,0,.3);
      --shadow-md: 0 4px 16px rgba(0,0,0,.4);
      --shadow-lg: 0 8px 24px rgba(0,0,0,.5);
    }

    /* Noise texture */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      z-index: 9999;
      pointer-events: none;
      opacity: 0.02;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
      background-repeat: repeat;
      background-size: 256px 256px;
    }

    a { color: inherit; text-decoration: none; }

    /* ── Header ─────────────────────────────────────── */
    .header {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(10,10,10,.92);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid rgba(255,255,255,.06);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .nav-hidden {
      transform: translateY(-100%);
      box-shadow: none;
    }

    .header-inner,
    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 24px;
    }

    .header-inner {
      min-height: 72px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .brand {
      display: flex;
      align-items: center;
      text-decoration: none;
    }
    .brand-full {
      height: 64px;
      width: auto;
      display: block;
    }
    .brand-icon {
      height: 64px;
      width: 64px;
      display: none;
    }

    .nav {
      display: flex;
      align-items: center;
      gap: 4px;
      flex-wrap: wrap;
    }

    .nav a {
      display: inline-block;
      padding: 8px 14px;
      border-radius: 999px;
      font-weight: 600;
      font-size: 14px;
      color: #a0a0a0;
      transition: background .15s, color .15s;
    }

    .nav a:hover { background: #1a1a1a; color: #e8e8e8; }
    .nav a.active { background: #22c55e; color: #050505; }

    .nav-divider {
      width: 1px;
      height: 18px;
      background: rgba(255,255,255,.1);
      margin: 0 4px;
    }

    /* ── Buttons ─────────────────────────────────────── */
    .btn {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 14px;
      border: 1px solid rgba(255,255,255,.15);
      background: #111;
      color: #e8e8e8;
      cursor: pointer;
      transition: transform .15s, box-shadow .15s;
      font-family: inherit;
    }

    .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,.3); }

    .btn-primary {
      background: #22c55e;
      color: #050505 !important;
      border-color: #22c55e;
    }

    .btn-primary:hover { background: #16a34a; }

    .btn-ghost {
      background: rgba(255,255,255,.06);
      color: #e8e8e8 !important;
      border-color: rgba(255,255,255,.12);
    }

    .btn-ghost:hover { background: rgba(255,255,255,.12); }

    /* ── Shared section styles ───────────────────────── */
    .section { padding: 52px 0; }

    .section-head { margin-bottom: 36px; }

    .section-label {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 999px;
      background: rgba(34,197,94,.08);
      border: 1px solid rgba(34,197,94,.2);
      font-size: 12px;
      font-weight: 700;
      color: #6ee7a0;
      text-transform: uppercase;
      letter-spacing: .07em;
      margin-bottom: 12px;
    }

    .section-title {
      font-size: 36px;
      letter-spacing: -0.02em;
      margin: 0 0 10px 0;
      line-height: 1.1;
      color: #fff;
    }

    .section-subtitle {
      color: #a0a0a0;
      font-size: 16px;
      line-height: 1.6;
      max-width: 680px;
      margin: 0;
    }

    /* ── Footer ──────────────────────────────────────── */
    .footer {
      background: #0a0a0a;
      border-top: 1px solid rgba(255,255,255,.06);
      padding: 32px 0;
    }

    .footer-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 20px;
      flex-wrap: wrap;
    }

    .footer-brand img {
      height: 30px;
      width: auto;
      display: block;
    }

    .footer-links {
      display: flex;
      align-items: center;
      gap: 24px;
      font-size: 14px;
      flex-wrap: wrap;
    }

    .footer-links a {
      color: #888;
      font-weight: 600;
      transition: color .15s;
    }

    .footer-links a:hover { color: #22c55e; }

    .footer-copy { font-size: 13px; color: #666; }

    /* ── Hamburger ───────────────────────────────────── */
    .hamburger {
      display: none;
      background: none;
      color: #e8e8e8;
      border: 1px solid rgba(255,255,255,.12);
      border-radius: 10px;
      cursor: pointer;
      padding: 8px 10px;
      flex-direction: column;
      gap: 5px;
      align-items: center;
      justify-content: center;
    }

    .hamburger span {
      display: block;
      width: 20px;
      height: 2px;
      background: #e8e8e8;
      border-radius: 2px;
      transition: transform .2s, opacity .2s;
    }

    .mobile-nav {
      display: none;
      flex-direction: column;
      gap: 2px;
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      background: #111;
      border-bottom: 1px solid rgba(255,255,255,.08);
      padding: 12px 16px 16px;
      z-index: 200;
      box-shadow: 0 8px 24px rgba(0,0,0,.4);
    }

    .mobile-nav.open { display: flex; }

    .mobile-nav a {
      padding: 12px 14px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      color: #a0a0a0;
      display: block;
    }

    .mobile-nav a:hover { background: #1a1a1a; color: #e8e8e8; }
    .mobile-nav a.active { background: #22c55e; color: #050505; }

    .mobile-nav-divider { border-top: 1px solid rgba(255,255,255,.08); margin: 8px 0; }

    .mobile-nav .mobile-cta {
      display: block;
      padding: 13px 14px;
      border-radius: 12px;
      background: #22c55e;
      color: #050505 !important;
      font-size: 15px;
      font-weight: 700;
      text-align: center;
      margin-top: 4px;
    }

    /* ── Responsive ──────────────────────────────────── */
    @media (max-width: 768px) {
      .section { padding: 36px 0; }
      .section-title { font-size: 28px; }
      .footer-inner { flex-direction: column; align-items: flex-start; gap: 16px; }
      .footer-links { gap: 12px; }
    }

    @media (max-width: 639px) {
      .brand-full { display: none; }
      .brand-icon { display: block; }
    }

    @media (max-width: 640px) {
      .header { position: relative; }
      .header.nav-hidden { transform: none; }
      .nav { display: none; }
      .hamburger { display: flex; }
    }

    @media (max-width: 480px) {
      .container { padding: 0 16px; }
      .footer-links { gap: 8px; font-size: 13px; }
    }

  </style>
  @stack('styles')
</head>
<body>

  <header class="header">
    <div class="header-inner">
      <a href="{{ route('home') }}" class="brand">
        <img src="/images/logo-fondonegro-multicolor.svg" alt="TuCancha" class="brand-full">
        <img src="/images/logo-fondonegro-multicolor-responsive.svg" alt="TuCancha" class="brand-icon">
      </a>

      <nav class="nav">
        <a href="{{ url('/como-funciona') }}" class="{{ request()->routeIs('como-funciona') ? 'active' : '' }}">Como funciona</a>
        <a href="{{ url('/planes') }}" class="{{ request()->routeIs('planes') ? 'active' : '' }}">Planes</a>
        <a href="{{ route('para-complejos') }}" class="{{ request()->routeIs('para-complejos') ? 'active' : '' }}">Para complejos</a>
        <a href="{{ route('venues.index') }}" class="{{ request()->routeIs('venues.index') ? 'active' : '' }}">Complejos</a>
        <a href="{{ route('nosotros') }}" class="{{ request()->routeIs('nosotros') ? 'active' : '' }}">Nosotros</a>
        <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a>
        <div class="nav-divider"></div>
        @auth
          <a href="{{ route('venues.index') }}" class="btn btn-primary" style="margin-left:4px;">Ver complejos</a>
        @else
          <a href="{{ route('login') }}">Ingresar</a>
          <a href="{{ route('register') }}" class="btn btn-primary" style="margin-left:4px;">Crear cuenta</a>
        @endauth
      </nav>

      <button class="hamburger" id="hamburgerBtn" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>

    <nav class="mobile-nav" id="mobileNav">
      <a href="{{ url('/como-funciona') }}" class="{{ request()->routeIs('como-funciona') ? 'active' : '' }}">Como funciona</a>
      <a href="{{ url('/planes') }}" class="{{ request()->routeIs('planes') ? 'active' : '' }}">Planes</a>
      <a href="{{ route('para-complejos') }}" class="{{ request()->routeIs('para-complejos') ? 'active' : '' }}">Para complejos</a>
      <a href="{{ route('venues.index') }}" class="{{ request()->routeIs('venues.index') ? 'active' : '' }}">Complejos</a>
      <a href="{{ route('nosotros') }}" class="{{ request()->routeIs('nosotros') ? 'active' : '' }}">Nosotros</a>
      <a href="{{ route('blog.index') }}" class="{{ request()->routeIs('blog.*') ? 'active' : '' }}">Blog</a>
      <div class="mobile-nav-divider"></div>
      @auth
        <a href="{{ route('venues.index') }}" class="mobile-cta">Ver complejos</a>
      @else
        <a href="{{ route('login') }}" style="padding:12px 14px; border-radius:12px; font-weight:600; color:#a0a0a0;">Ingresar</a>
        <a href="{{ route('register') }}" class="mobile-cta">Crear cuenta</a>
      @endauth
    </nav>
  </header>

  <main>
    @yield('content')
  </main>

  <style>
    .tc-foot { border-top: 1px solid rgba(255,255,255,.06); background: #050505; margin-top: 64px; padding: 56px 24px 28px; color: #a0a0a0; }
    .tc-foot-inner { max-width: 1360px; margin: 0 auto; }
    .tc-foot-cols { display: grid; grid-template-columns: 1.6fr repeat(3, 1fr); gap: 48px; padding-bottom: 36px; border-bottom: 1px solid rgba(255,255,255,.06); }
    .tc-foot-brand { display: flex; flex-direction: column; gap: 14px; max-width: 340px; }
    .tc-foot-logo { display: inline-flex; align-items: center; text-decoration: none; }
    .tc-foot-logo img { height: 56px; width: auto; display: block; }
    .tc-foot-brand p { font-size: 13px; line-height: 1.6; color: #8a8a8a; margin: 0; font-weight: 400; }
    .tc-foot-socials { display: flex; gap: 8px; margin-top: 6px; }
    .tc-foot-social { width: 34px; height: 34px; border-radius: 10px; border: 1px solid rgba(255,255,255,.08); background: rgba(255,255,255,.02); display: flex; align-items: center; justify-content: center; color: #8a8a8a; text-decoration: none; transition: border-color .15s, color .15s, background .15s; }
    .tc-foot-social:hover { border-color: rgba(34,197,94,.35); color: #22c55e; background: rgba(34,197,94,.06); }
    .tc-foot-col h6 { font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; color: #666; margin: 0 0 16px; }
    .tc-foot-col ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
    .tc-foot-col li a { font-size: 13px; color: #a0a0a0; text-decoration: none; font-weight: 500; transition: color .15s; display: inline-flex; align-items: center; gap: 6px; }
    .tc-foot-col li a:hover { color: #22c55e; }
    /* Pre-footer feedback wrapper */
    .tc-foot-fb-wrap { padding: 40px 24px 0; background: #050505; }
    .tc-foot-fb-inner { max-width: 1360px; margin: 0 auto; display: flex; justify-content: flex-end; }
    .tc-foot-fb-thanks {
      padding: 12px 16px;
      background: rgba(34,197,94,.1);
      color: #6ee7a0;
      border: 1px solid rgba(34,197,94,.2);
      border-radius: 10px;
      font-size: 13px; font-weight: 600;
      max-width: 520px;
      display: inline-flex; align-items: center; gap: 8px;
    }
    @media (max-width: 860px) {
      .tc-foot-fb-inner { justify-content: stretch; }
    }

    /* Compact feedback (details/summary) */
    .tc-foot-fb { max-width: 420px; width: 100%; }
    .tc-foot-fb > summary {
      list-style: none; cursor: pointer;
      display: flex; align-items: center; gap: 12px;
      padding: 12px 16px;
      background: rgba(255,255,255,.03);
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 12px;
      transition: background .15s, border-color .15s;
    }
    .tc-foot-fb > summary::-webkit-details-marker { display: none; }
    .tc-foot-fb > summary::marker { content: ''; }
    .tc-foot-fb > summary:hover { background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.14); }
    .tc-foot-fb[open] > summary { border-radius: 12px 12px 0 0; border-bottom-color: transparent; }
    .tc-foot-fb-ico {
      width: 32px; height: 32px; border-radius: 9px;
      background: rgba(34,197,94,.1); color: #22c55e;
      display: flex; align-items: center; justify-content: center;
      flex-shrink: 0;
    }
    .tc-foot-fb-text { flex: 1; min-width: 0; line-height: 1.3; }
    .tc-foot-fb-text { font-size: 13px; font-weight: 700; color: #e8e8e8; }
    .tc-foot-fb-text small { display: block; font-size: 11px; color: #666; font-weight: 400; margin-top: 2px; }
    .tc-foot-fb-chev { color: #666; transition: transform .25s, color .15s; flex-shrink: 0; }
    .tc-foot-fb[open] .tc-foot-fb-chev { transform: rotate(180deg); color: #22c55e; }
    .tc-foot-fb-form {
      padding: 14px 16px 16px;
      background: rgba(255,255,255,.02);
      border: 1px solid rgba(255,255,255,.08);
      border-top: 0;
      border-radius: 0 0 12px 12px;
      display: flex; flex-direction: column; gap: 8px;
    }
    .tc-foot-fb-from { font-size: 11px; color: #666; margin: 0; font-weight: 500; }
    .tc-foot-fb-from strong { color: #a0a0a0; font-weight: 600; }
    .tc-foot-fb-input, .tc-foot-fb-textarea {
      padding: 9px 12px;
      border: 1px solid rgba(255,255,255,.1);
      border-radius: 9px;
      font-size: 13px; font-family: inherit;
      background: #0a0a0a; color: #e8e8e8;
      outline: none; resize: vertical;
      transition: border-color .15s;
    }
    .tc-foot-fb-input:focus, .tc-foot-fb-textarea:focus { border-color: #22c55e; }
    .tc-foot-fb-err { font-size: 12px; color: #f87171; }
    .tc-foot-fb-btn {
      padding: 9px 18px;
      background: #22c55e; color: #050505;
      border: none; border-radius: 9px;
      font-size: 13px; font-weight: 800;
      cursor: pointer; font-family: inherit;
      transition: background .15s;
      align-self: flex-start;
    }
    .tc-foot-fb-btn:hover { background: #4ade80; }
    .tc-foot-base { padding-top: 24px; display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; font-size: 12px; color: #666; margin-top: 32px; border-top: 1px solid rgba(255,255,255,.06); }
    .tc-foot-base-links { display: flex; gap: 18px; flex-wrap: wrap; }
    .tc-foot-base-links a { color: #666; text-decoration: none; transition: color .15s; }
    .tc-foot-base-links a:hover { color: #c8c8c8; }
    @media (max-width: 860px) {
      .tc-foot-cols { grid-template-columns: 1fr 1fr; gap: 32px; }
      .tc-foot-brand { grid-column: 1 / -1; max-width: none; }
    }
    @media (max-width: 480px) {
      .tc-foot-cols { grid-template-columns: 1fr; gap: 28px; }
      .tc-foot { padding: 44px 20px 24px; }
      .tc-foot-base { justify-content: center; text-align: center; }
    }
  </style>

  {{-- Feedback pre-footer (compacto, colapsable) --}}
  <section class="tc-foot-fb-wrap">
    <div class="tc-foot-fb-inner">
      @if(session('feedback_sent'))
        <div id="feedback-section" class="tc-foot-fb-thanks">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
          Gracias por tu feedback. Lo revisaremos pronto.
        </div>
      @else
        <details id="feedback-section" class="tc-foot-fb">
          <summary>
            <span class="tc-foot-fb-ico">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </span>
            <span class="tc-foot-fb-text">
              ¿Sugerencia o comentario?
              <small>Contanos qué podemos mejorar</small>
            </span>
            <span class="tc-foot-fb-chev">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </span>
          </summary>
          <form method="POST" action="{{ route('feedback.store') }}" class="tc-foot-fb-form">
            @csrf
            <input type="text" name="website_url" style="position:absolute;left:-9999px;opacity:0;height:0;width:0;" tabindex="-1" autocomplete="off">
            @auth
              <p class="tc-foot-fb-from">Desde <strong>{{ auth()->user()->email }}</strong></p>
            @else
              <input type="email" name="feedback_email" placeholder="Tu email (opcional)" class="tc-foot-fb-input">
            @endauth
            <textarea name="feedback_message" rows="2" placeholder="Tu comentario, sugerencia o error..." required minlength="10" class="tc-foot-fb-textarea"></textarea>
            @error('feedback_message')
              <span class="tc-foot-fb-err">{{ $message }}</span>
            @enderror
            <button type="submit" class="tc-foot-fb-btn">Enviar</button>
          </form>
        </details>
      @endif
    </div>
  </section>

  <footer class="tc-foot">
    <div class="tc-foot-inner">
      <div class="tc-foot-cols">
        <div class="tc-foot-brand">
          <a href="{{ route('home') }}" class="tc-foot-logo" aria-label="TuCancha">
            <img src="/images/logo-blanco.svg" alt="TuCancha">
          </a>
          <p>La plataforma para reservar canchas y completar equipos en Argentina. Nueva, en crecimiento, hecha por jugadores para jugadores.</p>
          <div class="tc-foot-socials">
            <a href="https://wa.me/5491127279757" target="_blank" rel="noopener" class="tc-foot-social" aria-label="WhatsApp">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.611.611l4.458-1.495A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.34 0-4.508-.654-6.363-1.787l-.362-.222-3.75 1.257 1.257-3.75-.222-.362A9.935 9.935 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
            </a>
            <a href="mailto:tucancha10@gmail.com" class="tc-foot-social" aria-label="Email">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg>
            </a>
            <a href="https://instagram.com/tucancha.web" target="_blank" rel="noopener" class="tc-foot-social" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
            </a>
          </div>
        </div>

        <div class="tc-foot-col">
          <h6>Jugadores</h6>
          <ul>
            <li><a href="{{ route('venues.index') }}">Buscar complejos</a></li>
            <li><a href="{{ route('falta-uno.index') }}">Falta Uno</a></li>
            <li><a href="{{ route('ranking.index') }}">Ranking</a></li>
            <li><a href="{{ url('/como-funciona') }}">Cómo funciona</a></li>
            @auth
              <li><a href="{{ url('/dashboard') }}">Mi panel</a></li>
            @else
              <li><a href="{{ route('register') }}">Crear cuenta</a></li>
            @endauth
          </ul>
        </div>

        <div class="tc-foot-col">
          <h6>Complejos</h6>
          <ul>
            <li><a href="{{ route('para-complejos') }}">Sumá tu complejo</a></li>
            <li><a href="{{ route('planes') }}">Planes</a></li>
            <li><a href="{{ route('por-que-tucancha') }}">Por qué TuCancha</a></li>
            <li><a href="https://wa.me/5491127279757?text={{ urlencode('Hola! Tengo una consulta sobre TuCancha para mi complejo.') }}" target="_blank" rel="noopener">Contactar ventas</a></li>
          </ul>
        </div>

        <div class="tc-foot-col">
          <h6>Empresa</h6>
          <ul>
            <li><a href="{{ url('/nosotros') }}">Nosotros</a></li>
            <li><a href="{{ route('blog.index') }}">Blog</a></li>
            <li><a href="{{ route('faq') }}">Preguntas frecuentes</a></li>
            <li><a href="mailto:tucancha10@gmail.com">tucancha10@gmail.com</a></li>
          </ul>
        </div>
      </div>

      <div class="tc-foot-base">
        <span>&copy; {{ date('Y') }} TuCancha &middot; Hecho en Argentina &middot; Con pasión por el juego</span>
        <div class="tc-foot-base-links">
          <a href="{{ route('faq') }}">FAQ</a>
          <a href="https://wa.me/5491127279757" target="_blank" rel="noopener">Soporte</a>
        </div>
      </div>
    </div>
  </footer>

  @stack('scripts')

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({ duration: 800, once: true, offset: 80 });
  </script>

  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <script>lucide.createIcons();</script>

<script>
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const mobileNav    = document.getElementById('mobileNav');
  if (hamburgerBtn) {
    hamburgerBtn.addEventListener('click', () => mobileNav.classList.toggle('open'));
    document.addEventListener('click', (e) => {
      if (!hamburgerBtn.contains(e.target) && !mobileNav.contains(e.target)) {
        mobileNav.classList.remove('open');
      }
    });
  }
</script>

<script>
  (function() {
    const nav = document.querySelector('.header');
    let lastScroll = 0;
    window.addEventListener('scroll', function() {
      const current = window.scrollY;
      if (current > lastScroll && current > 80) {
        nav.classList.add('nav-hidden');
      } else {
        nav.classList.remove('nav-hidden');
      }
      lastScroll = current;
    }, { passive: true });
  })();
</script>

</body>
</html>
