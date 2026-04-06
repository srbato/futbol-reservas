<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'TuCancha')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
  <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
  <meta name="description" content="@yield('meta_description', 'TuCancha — Reservá canchas de fútbol, tenis y más deportes online. Encontrá el complejo más cercano y confirmá tu turno al instante.')">
  <meta name="keywords" content="reservar cancha, alquilar cancha, cancha de fútbol, cancha de pádel, cancha de tenis, turnos online, reservas deportivas, complejos deportivos, Argentina">
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
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
  <link rel="stylesheet" href="/css/design-tokens.css">
  <style>
    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: var(--color-bg-page);
      color: var(--color-text);
    }

    a { color: inherit; text-decoration: none; }

    /* ── Header ─────────────────────────────────────── */
    .header {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(255,255,255,.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid var(--color-border);
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
      color: #444;
      transition: background .15s;
    }

    .nav a:hover { background: #f3f3f3; }
    .nav a.active { background: #111; color: #fff; }

    .nav-divider {
      width: 1px;
      height: 18px;
      background: #e0e0e0;
      margin: 0 4px;
    }

    /* ── Buttons ─────────────────────────────────────── */
    .btn {
      display: inline-block;
      padding: 10px 20px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 14px;
      border: 1px solid #ddd;
      background: #fff;
      cursor: pointer;
      transition: transform .15s, box-shadow .15s;
      font-family: inherit;
    }

    .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,.08); }

    .btn-primary {
      background: #111;
      color: #fff !important;
      border-color: #111;
    }

    .btn-primary:hover { background: #222; }

    .btn-ghost {
      background: rgba(255,255,255,.1);
      color: #fff !important;
      border-color: rgba(255,255,255,.2);
    }

    .btn-ghost:hover { background: rgba(255,255,255,.18); }

    /* ── Shared section styles ───────────────────────── */
    .section { padding: 52px 0; }

    .section-head { margin-bottom: 36px; }

    .section-label {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 999px;
      background: #f0f0f0;
      font-size: 12px;
      font-weight: 700;
      color: #555;
      text-transform: uppercase;
      letter-spacing: .07em;
      margin-bottom: 12px;
    }

    .section-title {
      font-size: 36px;
      letter-spacing: -0.02em;
      margin: 0 0 10px 0;
      line-height: 1.1;
    }

    .section-subtitle {
      color: #666;
      font-size: 16px;
      line-height: 1.6;
      max-width: 680px;
      margin: 0;
    }

    /* ── Footer ──────────────────────────────────────── */
    .footer {
      background: #fff;
      border-top: 1px solid #ececec;
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
      color: #444;
      font-weight: 600;
      transition: color .15s;
    }

    .footer-links a:hover { color: #111; }

    .footer-copy { font-size: 13px; color: #aaa; }

    /* ── Hamburger ───────────────────────────────────── */
    .hamburger {
      display: none;
      background: none;
      border: 1px solid #e0e0e0;
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
      background: #111;
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
      background: #fff;
      border-bottom: 1px solid #ececec;
      padding: 12px 16px 16px;
      z-index: 200;
      box-shadow: 0 8px 24px rgba(0,0,0,.08);
    }

    .mobile-nav.open { display: flex; }

    .mobile-nav a {
      padding: 12px 14px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      color: #333;
      display: block;
    }

    .mobile-nav a:hover { background: #f5f5f5; }
    .mobile-nav a.active { background: #111; color: #fff; }

    .mobile-nav-divider { border-top: 1px solid #ececec; margin: 8px 0; }

    .mobile-nav .mobile-cta {
      display: block;
      padding: 13px 14px;
      border-radius: 12px;
      background: #111;
      color: #fff !important;
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

    @stack('styles')
  </style>
</head>
<body>

  <header class="header">
    <div class="header-inner">
      <a href="{{ route('home') }}" class="brand">
        <img src="/images/logo-multicolor.svg" alt="TuCancha" class="brand-full">
        <img src="/images/logo-multicolor-responsive.svg" alt="TuCancha" class="brand-icon">
      </a>

      <nav class="nav">
        <a href="{{ url('/como-funciona') }}" class="{{ request()->routeIs('como-funciona') ? 'active' : '' }}">Cómo funciona</a>
        <a href="{{ url('/planes') }}" class="{{ request()->routeIs('planes') ? 'active' : '' }}">Planes</a>
        <a href="{{ route('venues.index') }}" class="{{ request()->routeIs('venues.index') ? 'active' : '' }}">Complejos</a>
        <a href="{{ route('nosotros') }}" class="{{ request()->routeIs('nosotros') ? 'active' : '' }}">Nosotros</a>
        <div class="nav-divider"></div>
        @auth
          <a href="{{ route('venues.index') }}" class="btn btn-primary" style="margin-left:4px;">Ver complejos</a>
        @else
          <a href="{{ route('login') }}">Ingresar</a>
          <a href="{{ route('register') }}" class="btn btn-primary" style="margin-left:4px;">Crear cuenta</a>
        @endauth
      </nav>

      <button class="hamburger" id="hamburgerBtn" aria-label="Menú">
        <span></span><span></span><span></span>
      </button>
    </div>

    <nav class="mobile-nav" id="mobileNav">
      <a href="{{ url('/como-funciona') }}" class="{{ request()->routeIs('como-funciona') ? 'active' : '' }}">Cómo funciona</a>
      <a href="{{ url('/planes') }}" class="{{ request()->routeIs('planes') ? 'active' : '' }}">Planes</a>
      <a href="{{ route('venues.index') }}" class="{{ request()->routeIs('venues.index') ? 'active' : '' }}">Complejos</a>
      <a href="{{ route('nosotros') }}" class="{{ request()->routeIs('nosotros') ? 'active' : '' }}">Nosotros</a>
      <div class="mobile-nav-divider"></div>
      @auth
        <a href="{{ route('venues.index') }}" class="mobile-cta">Ver complejos</a>
      @else
        <a href="{{ route('login') }}" style="padding:12px 14px; border-radius:12px; font-weight:600; color:#333;">Ingresar</a>
        <a href="{{ route('register') }}" class="mobile-cta">Crear cuenta</a>
      @endauth
    </nav>
  </header>

  <main>
    @yield('content')
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer-inner">
        <span class="footer-brand"><img src="/images/logo-multicolor.svg" alt="TuCancha"></span>
        <div class="footer-links">
          <a href="{{ route('home') }}">Inicio</a>
          <a href="{{ url('/como-funciona') }}">Cómo funciona</a>
          <a href="{{ url('/planes') }}">Planes</a>
          <a href="{{ route('venues.index') }}">Complejos</a>
          <a href="{{ route('nosotros') }}">Nosotros</a>
          <a href="mailto:tucancha10@gmail.com">tucancha10@gmail.com</a>
        </div>
        <span class="footer-copy">© {{ date('Y') }} TuCancha</span>
      </div>

      {{-- Bloque de feedback público --}}
      <div style="margin-top: 28px; padding-top: 24px; border-top: 1px solid #ececec;">
        <p style="font-size:14px; color:#555; margin:0 0 14px 0; font-weight:600;">
          ¿Tenés alguna sugerencia o comentario?
        </p>

        @if(session('feedback_sent'))
          <div style="background:#e8f7ee; color:#157347; border:1px solid #cfe9d7; border-radius:10px; padding:12px 16px; font-size:14px; font-weight:600; max-width:560px;">
            Gracias por tu feedback. Lo revisaremos pronto.
          </div>
        @else
          <form method="POST" action="{{ route('feedback.store') }}" style="max-width:560px;">
            @csrf
            <div style="display:flex; flex-direction:column; gap:10px;">
              @auth
                <p style="font-size:13px; color:#888; margin:0;">
                  Se enviará desde tu cuenta: <strong>{{ auth()->user()->email }}</strong>
                </p>
              @else
                <input
                  type="email"
                  name="feedback_email"
                  placeholder="Tu email (opcional)"
                  style="padding:10px 14px; border:1px solid #ddd; border-radius:10px; font-size:14px; font-family:inherit; background:#fff; color:#111; outline:none;"
                >
              @endauth
              <textarea
                name="feedback_message"
                rows="3"
                placeholder="Escribí tu sugerencia, error o comentario..."
                required
                minlength="10"
                style="padding:10px 14px; border:1px solid #ddd; border-radius:10px; font-size:14px; font-family:inherit; resize:vertical; background:#fff; color:#111; outline:none;"
              ></textarea>
              @error('feedback_message')
                <span style="font-size:13px; color:#b91c1c;">{{ $message }}</span>
              @enderror
              <div>
                <button
                  type="submit"
                  style="padding:10px 20px; background:#111; color:#fff; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; transition:background .15s;"
                  onmouseover="this.style.background='#222'"
                  onmouseout="this.style.background='#111'"
                >
                  Enviar feedback
                </button>
              </div>
            </div>
          </form>
        @endif
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
