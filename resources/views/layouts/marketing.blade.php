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

  <footer class="footer">
    <div class="container">
      <div class="footer-inner">
        <span class="footer-brand"><img src="/images/logo-fondonegro-multicolor.svg" alt="TuCancha"></span>
        <div class="footer-links">
          <a href="{{ route('home') }}">Inicio</a>
          <a href="{{ url('/como-funciona') }}">Como funciona</a>
          <a href="{{ url('/planes') }}">Planes</a>
          <a href="{{ route('venues.index') }}">Complejos</a>
          <a href="{{ route('nosotros') }}">Nosotros</a>
          <a href="{{ route('blog.index') }}">Blog</a>
          <a href="{{ route('para-complejos') }}">Para complejos</a>
          <a href="{{ route('faq') }}">FAQ</a>
          <a href="mailto:tucancha10@gmail.com">tucancha10@gmail.com</a>
        </div>
        <span class="footer-copy">&copy; {{ date('Y') }} TuCancha</span>
      </div>

      {{-- Bloque de feedback publico --}}
      <div id="feedback-section" style="margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,.06);">
        <p style="font-size:14px; color:#a0a0a0; margin:0 0 14px 0; font-weight:600;">
          Tenes alguna sugerencia o comentario?
        </p>

        @if(session('feedback_sent'))
          <div style="background:rgba(34,197,94,.1); color:#6ee7a0; border:1px solid rgba(34,197,94,.2); border-radius:10px; padding:12px 16px; font-size:14px; font-weight:600; max-width:560px;">
            Gracias por tu feedback. Lo revisaremos pronto.
          </div>
        @else
          <form method="POST" action="{{ route('feedback.store') }}" style="max-width:560px;">
            @csrf
            <input type="text" name="website_url" style="position:absolute;left:-9999px;opacity:0;height:0;width:0;" tabindex="-1" autocomplete="off">
            <div style="display:flex; flex-direction:column; gap:10px;">
              @auth
                <p style="font-size:13px; color:#666; margin:0;">
                  Se enviara desde tu cuenta: <strong style="color:#a0a0a0;">{{ auth()->user()->email }}</strong>
                </p>
              @else
                <input
                  type="email"
                  name="feedback_email"
                  placeholder="Tu email (opcional)"
                  style="padding:10px 14px; border:1px solid rgba(255,255,255,.1); border-radius:10px; font-size:14px; font-family:inherit; background:#0a0a0a; color:#e8e8e8; outline:none;"
                >
              @endauth
              <textarea
                name="feedback_message"
                rows="3"
                placeholder="Escribi tu sugerencia, error o comentario..."
                required
                minlength="10"
                style="padding:10px 14px; border:1px solid rgba(255,255,255,.1); border-radius:10px; font-size:14px; font-family:inherit; resize:vertical; background:#0a0a0a; color:#e8e8e8; outline:none;"
              ></textarea>
              @error('feedback_message')
                <span style="font-size:13px; color:#f87171;">{{ $message }}</span>
              @enderror
              <div>
                <button
                  type="submit"
                  style="padding:10px 20px; background:#22c55e; color:#050505; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; transition:background .15s;"
                  onmouseover="this.style.background='#16a34a'"
                  onmouseout="this.style.background='#22c55e'"
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
