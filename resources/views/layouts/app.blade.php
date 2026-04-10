<!doctype html>
<html lang="es-AR">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="alternate" hreflang="es-AR" href="{{ url()->current() }}">
  <title>@yield('title', 'Reservas de canchas')</title>
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
  {{-- JSON-LD Organization (all pages) --}}
  <script type="application/ld+json">
  @php
  echo json_encode([
      '@context' => 'https://schema.org',
      '@type' => 'Organization',
      'name' => 'TuCancha',
      'url' => url('/'),
      'logo' => asset('images/logo-multicolor.svg'),
  ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  @endphp
  </script>
@stack('head')
<link rel="stylesheet" href="/css/design-tokens.css">
<style>
  [x-cloak] { display: none !important; }
  * { box-sizing: border-box; }

  body {
    margin: 0;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    background: var(--color-bg-page);
    color: var(--color-text);
  }

  a {
    color: inherit;
  }

  .site-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(255,255,255,.95);
    backdrop-filter: blur(8px);
    border-bottom: 1px solid var(--color-border);
  }

  .site-header-inner {
    max-width: 1500px;
    margin: 0 auto;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
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

  @media (max-width: 639px) {
    .brand-full { display: none; }
    .brand-icon { display: block; }
  }

  .site-nav {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
  }

  .site-nav-center {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
  }

  .site-nav-right {
    display: flex;
    gap: 10px;
    align-items: center;
  }

  .site-nav a,
  .site-nav button {
    text-decoration: none;
    border: 1px solid var(--color-border);
    background: var(--color-bg);
    padding: 9px 14px;
    border-radius: var(--radius-full);
    cursor: pointer;
    font-size: var(--text-sm);
  }

  .site-nav a.primary {
    background: var(--color-bg-dark);
    color: var(--color-text-inverse);
    border-color: var(--color-bg-dark);
  }

  .user-menu-wrap {
    position: relative;
  }

  .user-menu-button {
    text-decoration: none;
    border: 1px solid var(--color-border);
    background: var(--color-bg);
    padding: 9px 14px;
    border-radius: var(--radius-full);
    cursor: pointer;
    font-size: var(--text-sm);
    font-weight: 600;
  }

  .user-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    min-width: 210px;
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    padding: var(--space-sm);
    display: none;
    z-index: 100;
  }

  .user-dropdown.open {
    display: block;
  }

  .user-dropdown a,
  .user-dropdown-logout {
    display: block;
    width: 100%;
    text-align: left;
    text-decoration: none;
    border: 0;
    background: transparent;
    padding: 10px 12px;
    border-radius: 10px;
    cursor: pointer;
    font-size: 14px;
    color: var(--color-text);
  }

  .user-dropdown a:hover,
  .user-dropdown-logout:hover {
    background: var(--color-bg-hover);
  }

  .user-dropdown-admin {
    background: var(--color-bg-dark) !important;
    color: var(--color-text-inverse) !important;
    font-weight: 600 !important;
    margin-bottom: 4px;
  }

  .user-dropdown-admin:hover {
    background: #222 !important; /* hover state for dark bg */
  }

  /* ── Notificaciones ────────────────────────────────── */
  .notif-wrap {
    position: relative;
  }

  .notif-bell {
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-full);
    padding: 9px 12px;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    position: relative;
    gap: 0;
  }

  .notif-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: var(--color-error);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    border-radius: 999px;
    min-width: 17px;
    height: 17px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    border: 2px solid #fff;
    line-height: 1;
  }

  .notif-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 340px;
    max-width: calc(100vw - 16px);
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    display: none;
    z-index: 200;
    overflow: hidden;
  }

  .notif-dropdown.open {
    display: block;
  }

  .notif-dropdown-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 14px 8px 14px;
    border-bottom: 1px solid var(--color-border-light);
  }

  .notif-dropdown-header span {
    font-size: 13px;
    font-weight: 700;
    color: var(--color-text);
  }

  .notif-mark-all {
    font-size: 12px;
    color: var(--color-text-secondary);
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    text-decoration: underline;
  }

  .notif-mark-all:hover {
    color: var(--color-text);
  }

  .notif-list {
    max-height: 340px;
    overflow-y: auto;
  }

  .notif-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f7f7f7;
    transition: background .12s;
    text-decoration: none;
    color: var(--color-text);
  }

  .notif-item:hover {
    background: var(--color-bg-hover);
  }

  .notif-item.unread {
    background: #f0f7ff;
  }

  .notif-item.unread:hover {
    background: #e4f0fd;
  }

  .notif-icon {
    font-size: 20px;
    line-height: 1;
    flex-shrink: 0;
    margin-top: 1px;
  }

  .notif-content {
    flex: 1;
    min-width: 0;
  }

  .notif-title {
    font-size: 13px;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .notif-body {
    font-size: 12px;
    color: var(--color-text-secondary);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .notif-time {
    font-size: 11px;
    color: var(--color-text-muted);
    margin-top: 3px;
  }

  .notif-empty {
    padding: 24px 16px;
    text-align: center;
    color: var(--color-text-muted);
    font-size: 13px;
  }

  .site-main {
    max-width: 1500px;
    margin: 0 auto;
    padding: 24px 24px 40px 24px;
  }

  .page-card {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    padding: var(--radius-lg);
    box-shadow: var(--shadow-sm);
  }

  .hero {
    display: grid;
    grid-template-columns: 1.25fr .75fr;
    gap: 24px;
    align-items: stretch;
    padding: 34px;
    border-radius: 28px;
    background: linear-gradient(135deg, #111 0%, #1f1f1f 100%);
    color: #fff;
    margin-bottom: 26px;
    min-height: 280px;
  }

  .hero h1 {
    margin: 0 0 12px 0;
    font-size: 52px;
    line-height: 1.02;
    letter-spacing: -0.02em;
  }

  .hero p {
    margin: 0;
    color: rgba(255,255,255,.82);
    font-size: 18px;
    max-width: 720px;
  }

  .hero-box {
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 24px;
    padding: 22px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .hero-box strong {
    display: block;
    font-size: 42px;
    margin-bottom: 8px;
  }

  .section-title {
    margin: 0 0 16px 0;
    font-size: 30px;
    letter-spacing: -0.02em;
  }

  .stars {
    display: inline-flex;
    align-items: center;
    gap: 2px;
    font-size: 16px;
    line-height: 1;
    color: var(--color-warning);
    font-weight: 700;
  }

  .stars-text {
    color: var(--color-text-secondary);
    font-size: 14px;
    font-weight: 600;
    margin-left: 6px;
  }

  .muted {
    color: var(--color-text-secondary);
  }

  .grid {
    display: grid;
    gap: 16px;
  }

  .grid-venues {
    grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
  }

  .venue-card {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
  }

  .venue-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,.12);
  }

  .venue-card-image {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
    background: #f1f1f1;
    transition: transform .25s ease;
  }

  .venue-card:hover .venue-card-image {
    transform: scale(1.05);
  }

  .venue-card-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex: 1;
  }

  .venue-card h3 {
    margin: 0;
    font-size: 24px;
  }

  .badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 11px;
    border-radius: 999px;
    background: var(--color-bg-hover);
    font-size: 12px;
    color: var(--color-text-secondary);
    font-weight: 700;
  }

  .btn {
    display: inline-block;
    text-decoration: none;
    border: 1px solid var(--color-border);
    background: var(--color-bg);
    color: var(--color-text);
    padding: 10px 16px;
    border-radius: var(--radius-md);
    font-weight: 700;
    font-size: var(--text-sm);
    font-family: inherit;
    transition: background var(--transition-fast), color var(--transition-fast), border-color var(--transition-fast), transform var(--transition-fast);
    width: auto;
    cursor: pointer;
    line-height: 1.4;
    -webkit-appearance: none;
    appearance: none;
  }

  .btn:hover {
    transform: translateY(-1px);
  }

  .btn-primary {
    background: var(--color-bg-dark);
    color: var(--color-text-inverse);
    border-color: var(--color-bg-dark);
  }

  .toolbar {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: end;
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
    padding: var(--radius-lg);
    margin-bottom: 20px;
    box-shadow: var(--shadow-md);
  }

  .toolbar input,
  .toolbar select {
    padding: 11px 12px;
    border: 1px solid var(--color-border);
    border-radius: 14px;
    min-width: 190px;
    background: var(--color-bg);
  }

  .flash {
    margin-bottom: 16px;
    padding: 12px 14px;
    border-radius: 12px;
    border: 1px solid #eee;
    background: #fff;
  }

  .flash.success {
    background: #e8f7ee;
    color: #157347;
    border-color: #cfe9d7;
  }

  .flash.error {
    background: #f8d7da;
    color: #842029;
    border-color: #f1b9c0;
  }

  .venue-btn {
    align-self: flex-start;
    width: auto;
    display: inline-block;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 700;
    margin-top: 10px;
  }

  .venue-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
  }

  .card-actions {
    margin-top: auto;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
  }

  .card-actions .btn,
  .card-actions form {
    margin: 0;
    width: auto;
    align-self: flex-start;
  }

  .review-stars-picker {
    display: flex;
    gap: 6px;
    align-items: center;
    flex-wrap: wrap;
  }

  .review-stars-picker button {
    border: 0;
    background: transparent;
    font-size: 28px;
    line-height: 1;
    cursor: pointer;
    color: #d1d5db;
    padding: 0;
  }

  .review-stars-picker button.active {
    color: var(--color-warning);
  }

  .review-stars-picker button:hover {
    transform: scale(1.05);
  }

  .review-rating-text {
    font-size: 14px;
    color: var(--color-text-secondary);
    margin-top: 6px;
  }

  .carousel-section {
    margin-bottom: 0;
  }

  .carousel-header {
    display: flex;
    justify-content: space-between;
    align-items: end;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }

  .carousel-subtitle {
    color: var(--color-text-secondary);
    font-size: 14px;
    max-width: 760px;
    line-height: 1.5;
  }

  .feature-tabs {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .feature-tabs-wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }

  .feature-tab {
    padding: 8px 14px;
    border-radius: var(--radius-full);
    border: 1px solid var(--color-border);
    cursor: pointer;
    font-weight: 600;
    background: var(--color-bg);
    transition: background var(--transition-fast), color var(--transition-fast), border-color var(--transition-fast), transform var(--transition-fast);
    user-select: none;
  }

  .feature-tab:hover {
    transform: translateY(-1px);
  }

  .feature-tab.active {
    background: var(--color-bg-dark);
    color: var(--color-text-inverse);
    border-color: var(--color-bg-dark);
  }

  .feature-carousel {
    display: none;
  }

  .feature-carousel.active {
    display: block;
  }

  .feature-carousel-shell {
    position: relative;
  }

  .carousel-track {
    display: flex;
    gap: 18px;
    overflow-x: auto;
    padding: 8px 6px 12px 6px;
    scroll-snap-type: x mandatory;
    scroll-behavior: smooth;
    -webkit-overflow-scrolling: touch;
    cursor: grab;
    user-select: none;
    scrollbar-width: thin;
    scrollbar-color: #d8d8d8 transparent;
  }

  .featured-track {
    gap: 0;
    padding: 8px 0 12px 0;
  }

  .featured-track .carousel-card {
    min-width: 100% !important;
    max-width: 100% !important;
  }

  .carousel-track.dragging {
    cursor: grabbing;
    scroll-behavior: auto;
  }

  .carousel-track::-webkit-scrollbar {
    height: 10px;
  }

  .carousel-track::-webkit-scrollbar-thumb {
    background: var(--color-border);
    border-radius: 999px;
  }

  .featured-nav-arrows {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .featured-nav-arrow {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-full);
    border: 1px solid var(--color-border);
    background: var(--color-bg);
    color: var(--color-text);
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform var(--transition-fast), box-shadow var(--transition-fast);
    box-shadow: 0 6px 18px rgba(0,0,0,.14);
  }

  .featured-nav-arrow:hover {
    transform: translateY(-1px);
    border-color: var(--color-text);
  }

  .carousel-card {
    min-width: calc((100% - 36px) / 3);
    max-width: calc((100% - 36px) / 3);
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    scroll-snap-align: start;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .carousel-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0,0,0,.10);
  }

  .carousel-card img,
  .carousel-card .carousel-image-placeholder {
    width: 100%;
    height: 220px;
    object-fit: cover;
    display: block;
    background: #f1f1f1;
  }

  .carousel-card-body {
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex: 1;
  }

  .carousel-card h3 {
    margin: 0;
    font-size: 24px;
    line-height: 1.2;
  }

  .carousel-meta {
    font-size: 14px;
    color: var(--color-text-secondary);
    font-weight: 600;
    line-height: 1.5;
  }

  @media (max-width: 1100px) {
    .carousel-card {
      min-width: calc((100% - 18px) / 2);
      max-width: calc((100% - 18px) / 2);
    }
  }

  @media (max-width: 900px) {
    .featured-nav-arrow {
      width: 36px;
      height: 36px;
      font-size: 20px;
    }

    .carousel-card img,
    .carousel-card .carousel-image-placeholder {
      height: 190px;
    }
  }

  @media (max-width: 680px) {
    .carousel-card {
      min-width: 88%;
      max-width: 88%;
    }
  }

  /* ── Hamburger (authed mobile nav) ─────────────── */
  .app-hamburger {
    display: none;
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-sm);
    cursor: pointer;
    padding: 8px 10px;
    flex-direction: column;
    gap: 5px;
    align-items: center;
    justify-content: center;
  }

  .app-hamburger span {
    display: block;
    width: 20px;
    height: 2px;
    background: var(--color-text);
    border-radius: 2px;
    transition: transform .2s, opacity .2s;
  }

  .app-mobile-nav {
    display: none;
    flex-direction: column;
    gap: 2px;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--color-bg);
    border-bottom: 1px solid var(--color-border);
    padding: 12px 16px 16px;
    z-index: 200;
    box-shadow: var(--shadow-lg);
  }

  .app-mobile-nav.open { display: flex; }

  .app-mobile-nav a,
  .app-mobile-nav button {
    padding: 12px 14px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    color: var(--color-text-secondary);
    display: block;
    text-decoration: none;
    background: transparent;
    border: 0;
    text-align: left;
    width: 100%;
    cursor: pointer;
    font-family: inherit;
  }

  .app-mobile-nav a:hover,
  .app-mobile-nav button:hover { background: var(--color-bg-hover); }

  .app-mobile-nav .app-mobile-cta {
    background: var(--color-bg-dark);
    color: var(--color-text-inverse) !important;
    text-align: center;
    margin-top: 4px;
  }

  .app-mobile-divider { border-top: 1px solid var(--color-border); margin: 8px 0; }

  /* ── Mobile ────────────────────────────────────── */
  @media (max-width: 600px) {
    .site-header-inner {
      padding: 10px 16px;
      gap: 10px;
    }

    /* Ocultar nav completa en mobile */
    .site-nav,
    .site-nav-right {
      display: none;
    }

    .app-hamburger {
      display: flex;
    }

    .site-nav {
      gap: 6px;
    }

    .site-nav a,
    .site-nav button {
      padding: 7px 11px;
      font-size: 13px;
    }

    .site-main {
      padding: 16px 14px 32px 14px;
    }

    .page-card {
      padding: 14px;
    }

    .hero {
      grid-template-columns: 1fr;
      padding: 22px 18px;
      min-height: unset;
      margin-bottom: 18px;
    }

    .hero h1 {
      font-size: 32px;
    }

    .hero p {
      font-size: 15px;
    }

    .hero-box {
      padding: 16px;
    }

    .hero-box strong {
      font-size: 30px;
    }

    .section-title {
      font-size: 22px;
    }

    .grid-venues {
      grid-template-columns: 1fr;
    }

    .toolbar {
      padding: 14px;
      gap: 10px;
    }

    .toolbar input,
    .toolbar select {
      min-width: 0;
      width: 100%;
    }
  }

  @stack('styles')
</style>
</head>
<body>
<header class="site-header" style="position:relative;">
    <div class="site-header-inner">
      <a href="{{ route('home') }}" class="brand">
        <img src="/images/logo-multicolor.svg" alt="TuCancha" class="brand-full brand-light">
        <img src="/images/logo-multicolor-responsive.svg" alt="TuCancha" class="brand-icon brand-light">
        <img src="/images/logo-blanco.svg" alt="TuCancha" class="brand-full brand-dark" style="display:none;">
      </a>

      @auth
      {{-- Nav centrada --}}
      <nav class="site-nav site-nav-center auth-nav">
        <a href="{{ route('home') }}"
           @if(request()->routeIs('home')) style="background:var(--color-bg-hover); font-weight:700;" @endif>Inicio</a>
        <a href="{{ route('venues.index') }}"
           @if(request()->routeIs('venues.*')) style="background:var(--color-bg-hover); font-weight:700;" @endif>Complejos</a>
        <a href="{{ route('falta-uno.index') }}"
           @if(request()->routeIs('falta-uno.*')) style="background:var(--color-bg-dark); color:var(--color-text-inverse); border-color:var(--color-bg-dark); font-weight:700;" @endif><i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;margin-right:4px;"></i> Falta Uno</a>
        <a href="{{ route('torneos.index') }}"
           @if(request()->routeIs('torneos.*')) style="background:var(--color-bg-hover); font-weight:700;" @endif><i data-lucide="trophy" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;margin-right:4px;"></i> Torneos</a>
      </nav>

      {{-- Acciones derecha: notificaciones + user --}}
      <div class="site-nav-right auth-nav">
        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
        <div class="notif-wrap">
            <button type="button" class="notif-bell" id="notifBellBtn" onclick="toggleNotifDropdown()" aria-label="Notificaciones">
                <i data-lucide="bell" style="width:18px;height:18px;stroke:currentColor;vertical-align:middle;"></i>
                @if($unreadCount > 0)
                    <span class="notif-badge" id="notifBadge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @else
                    <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
                @endif
            </button>

            <div id="notifDropdown" class="notif-dropdown">
                <div class="notif-dropdown-header">
                    <span>Notificaciones</span>
                    <form method="POST" action="{{ route('notifications.mark_all_read') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="notif-mark-all">Marcar todas como leídas</button>
                    </form>
                </div>

                <div class="notif-list" id="notifList">
                    @php
                        $notifications = auth()->user()->notifications()->latest()->limit(5)->get();
                    @endphp

                    @forelse($notifications as $notif)
                        <form method="POST" action="{{ route('notifications.read', $notif->id) }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="notif-item {{ $notif->read_at ? '' : 'unread' }}" style="width:100%; text-align:left; border:0; background:transparent; font-family:inherit;">
                                <span class="notif-icon">{{ $notif->data['icon'] ?? '' }}</span>
                                <span class="notif-content">
                                    <span class="notif-title">{{ $notif->data['title'] ?? '' }}</span>
                                    <span class="notif-body">{{ $notif->data['body'] ?? '' }}</span>
                                    <span class="notif-time">{{ $notif->created_at->diffForHumans() }}</span>
                                </span>
                            </button>
                        </form>
                    @empty
                        <div class="notif-empty">Sin notificaciones</div>
                    @endforelse
                </div>
                <div style="border-top:1px solid #f0f0f0; padding:10px 14px; text-align:center;">
                    <a href="{{ route('notifications.index') }}"
                       style="font-size:13px; color:#555; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:4px;">
                        Ver todas las notificaciones <i data-lucide="arrow-right" style="width:13px;height:13px;stroke:currentColor;"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="user-menu-wrap">
            <button type="button" class="user-menu-button" onclick="toggleUserMenu()"
                    style="display:flex; align-items:center; gap:8px; padding:6px 12px 6px 6px;">
                @if(auth()->user()->avatar_path)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar_path) }}"
                        alt="Avatar"
                        style="width:32px; height:32px; border-radius:999px; object-fit:cover; border:1px solid #eee;"
                    >
                @else
                    <div style="width:32px; height:32px; border-radius:999px; background:#f1f1f1; display:flex; align-items:center; justify-content:center; border:1px solid #eee;">
                        <i data-lucide="user" style="width:16px;height:16px;stroke:#999;stroke-width:2;"></i>
                    </div>
                @endif
                <span>{{ auth()->user()->name }}</span> ▾
            </button>

            <div id="userDropdown" class="user-dropdown">
              <a href="{{ route('sport-profile.public', auth()->user()) }}">Perfil</a>
              <a href="{{ route('my_reservations') }}">Mi actividad</a>
              <a href="{{ route('venues.favorites') }}">Favoritos</a>
@if(in_array(auth()->user()->role, ['venue_admin', 'super_admin']) || auth()->user()->isVenueStaff())
                <a href="{{ route('va.dashboard') }}" class="user-dropdown-admin" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;"></i> Panel admin</a>
              @endif

              <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="user-dropdown-logout">Salir</button>
              </form>
            </div>
          </div>
      </div>

      {{-- Botón hamburguesa para mobile (solo autenticados) --}}
      <button class="app-hamburger" id="appHamburgerBtn" onclick="toggleAppMobileNav()" aria-label="Menú">
        <span></span><span></span><span></span>
      </button>

      @else
      {{-- Nav centrada (no autenticada) --}}
      <nav class="site-nav site-nav-center">
        <a href="{{ route('home') }}"
           @if(request()->routeIs('home')) style="background:var(--color-bg-hover); font-weight:700;" @endif>Inicio</a>
        <a href="{{ route('venues.index') }}"
           @if(request()->routeIs('venues.*')) style="background:var(--color-bg-hover); font-weight:700;" @endif>Complejos</a>
        <a href="{{ route('falta-uno.index') }}"
           @if(request()->routeIs('falta-uno.*')) style="background:var(--color-bg-dark); color:var(--color-text-inverse); border-color:var(--color-bg-dark); font-weight:700;" @endif><i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;margin-right:4px;"></i> Falta Uno</a>
        <a href="{{ route('torneos.index') }}"
           @if(request()->routeIs('torneos.*')) style="background:var(--color-bg-hover); font-weight:700;" @endif><i data-lucide="trophy" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;margin-right:4px;"></i> Torneos</a>
      </nav>
      <div class="site-nav-right">
          <a href="{{ route('login') }}" class="btn" style="font-size:14px;">Ingresar</a>
          <a href="{{ route('register') }}" class="btn btn-primary" style="font-size:14px;">Crear cuenta</a>
      </div>

      {{-- Botón hamburguesa para mobile (invitados) --}}
      <button class="app-hamburger" id="guestHamburgerBtn" onclick="toggleGuestMobileNav()" aria-label="Menú">
        <span></span><span></span><span></span>
      </button>
      @endauth
    </div>

    @auth
    {{-- Menú mobile desplegable (autenticados) --}}
    <nav class="app-mobile-nav" id="appMobileNav">
      <a href="{{ route('home') }}">Inicio</a>
      <a href="{{ route('venues.index') }}">Complejos</a>
      <a href="{{ route('falta-uno.index') }}" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;"></i> Falta Uno</a>
      <a href="{{ route('torneos.index') }}" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="trophy" style="width:14px;height:14px;stroke:currentColor;"></i> Torneos</a>
      <div class="app-mobile-divider"></div>
      <a href="{{ route('sport-profile.public', auth()->user()) }}">Perfil</a>
      <a href="{{ route('my_reservations') }}">Mi actividad</a>
      <a href="{{ route('venues.favorites') }}">Favoritos</a>
      <a href="{{ route('notifications.index') }}">Notificaciones</a>
      @if(in_array(auth()->user()->role, ['venue_admin', 'super_admin']) || auth()->user()->isVenueStaff())
        <a href="{{ route('va.dashboard') }}" class="app-mobile-cta" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;"></i> Panel admin</a>
      @endif
      <div class="app-mobile-divider"></div>
      <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button type="submit">Salir</button>
      </form>
    </nav>
    @else
    {{-- Menú mobile desplegable (invitados) --}}
    <nav class="app-mobile-nav" id="guestMobileNav">
      <a href="{{ route('home') }}">Inicio</a>
      <a href="{{ route('venues.index') }}">Complejos</a>
      <a href="{{ route('falta-uno.index') }}" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;"></i> Falta Uno</a>
      <a href="{{ route('torneos.index') }}" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="trophy" style="width:14px;height:14px;stroke:currentColor;"></i> Torneos</a>
      <div class="app-mobile-divider"></div>
      <a href="{{ route('login') }}">Ingresar</a>
      <a href="{{ route('register') }}" class="app-mobile-cta">Crear cuenta</a>
    </nav>
    @endauth

  </header>

  <main class="site-main">
    @if(session('success'))
      <div class="flash success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="flash error">{{ session('error') }}</div>
    @endif

    @yield('content')
  </main>

  <footer style="border-top:1px solid #e5e7eb; padding:24px 0; margin-top:48px; text-align:center; font-size:13px; color:#9ca3af;">
    <div style="max-width:1200px; margin:0 auto; padding:0 20px; display:flex; justify-content:center; align-items:center; gap:16px; flex-wrap:wrap;">
      <span>&copy; {{ date('Y') }} TuCancha</span>
      <span style="color:#d1d5db;">|</span>
      <a href="{{ url('/como-funciona') }}" style="color:#6b7280; text-decoration:none; transition:color .2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#6b7280'">Como funciona</a>
      <span style="color:#d1d5db;">|</span>
      <a href="{{ url('/nosotros') }}" style="color:#6b7280; text-decoration:none; transition:color .2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#6b7280'">Nosotros</a>
      <span style="color:#d1d5db;">|</span>
      <a href="{{ route('blog.index') }}" style="color:#6b7280; text-decoration:none; transition:color .2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#6b7280'">Blog</a>
      <span style="color:#d1d5db;">|</span>
      <a href="{{ route('planes') }}" style="color:#6b7280; text-decoration:none; transition:color .2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#6b7280'">Sos dueno de un complejo? Registralo aca</a>
    </div>
  </footer>

  <script>
    function toggleAppMobileNav() {
      const nav = document.getElementById('appMobileNav');
      if (!nav) return;
      nav.classList.toggle('open');
    }

    function toggleGuestMobileNav() {
      const nav = document.getElementById('guestMobileNav');
      if (!nav) return;
      nav.classList.toggle('open');
    }

    function toggleUserMenu() {
      const menu = document.getElementById('userDropdown');
      if (!menu) return;
      menu.classList.toggle('open');
      // cerrar notificaciones si está abierto
      document.getElementById('notifDropdown')?.classList.remove('open');
    }

    function toggleNotifDropdown() {
      const dropdown = document.getElementById('notifDropdown');
      if (!dropdown) return;
      dropdown.classList.toggle('open');
      // cerrar user menu si está abierto
      document.getElementById('userDropdown')?.classList.remove('open');
    }

    document.addEventListener('click', function (event) {
      const userWrap  = document.querySelector('.user-menu-wrap');
      const userMenu  = document.getElementById('userDropdown');
      const notifWrap = document.querySelector('.notif-wrap');
      const notifMenu = document.getElementById('notifDropdown');

      if (userWrap && userMenu && !userWrap.contains(event.target)) {
        userMenu.classList.remove('open');
      }
      if (notifWrap && notifMenu && !notifWrap.contains(event.target)) {
        notifMenu.classList.remove('open');
      }
    });
  </script>

@auth
  <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
  <script>
    (function () {
      const wsHost    = '{{ config("broadcasting.connections.reverb.client_host") }}';
      const wsPort    = {{ config("broadcasting.connections.reverb.client_port") }};
      const reverbKey = '{{ config("broadcasting.connections.reverb.key") }}';

      if (!reverbKey) return;

      window.Echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: wsHost,
        wsPort: wsPort,
        wssPort: wsPort,
        forceTLS: wsPort === 443,
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
        },
      });

      window.Echo.private('user.{{ auth()->id() }}')
        .listen('.notification.created', function (data) {
          const count = data.unread_count ?? 0;
          const badge = document.getElementById('notifBadge');
          if (!badge) return;

          if (count > 0) {
            badge.textContent = count > 9 ? '9+' : count;
            badge.style.display = 'flex';
          } else {
            badge.style.display = 'none';
          }
        });
    })();
  </script>
@endauth


@auth
  @php
    $activeSystemMessage = \App\Models\SystemMessage::query()
      ->where('is_active', true)
      ->where(function ($q) {
          $q->whereNull('target_user_id')
            ->orWhere('target_user_id', auth()->id());
      })
      ->whereDoesntHave('dismissedByUsers', function ($q) {
          $q->where('users.id', auth()->id());
      })
      ->latest()
      ->first();
  @endphp

  @if($activeSystemMessage)
    <div id="systemMessageModal" style="position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px;">
      <div style="width:100%; max-width:520px; background:#fff; border-radius:18px; padding:22px; box-shadow:0 12px 40px rgba(0,0,0,.18);">
        <h3 style="margin-top:0; margin-bottom:10px;">{{ $activeSystemMessage->title }}</h3>

        <div class="muted" style="line-height:1.7; white-space:pre-wrap;">
          {{ $activeSystemMessage->body }}
        </div>

        <div style="display:flex; justify-content:flex-end; margin-top:18px;">
          <button
            type="button"
            onclick="dismissSystemMessage({{ $activeSystemMessage->id }})"
            style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>

    <script>
      function dismissSystemMessage(messageId) {
        const modal = document.getElementById('systemMessageModal');
        if (modal) modal.remove();

        fetch(`/system-messages/${messageId}/dismiss`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        });
      }
    </script>
  @endif
@endauth



@stack('scripts')

<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>lucide.createIcons();</script>

{{-- Botón flotante de feedback --}}
<style>
  #feedbackWidget { position:fixed; bottom:90px; right:24px; z-index:9990; }
  #feedbackPanel  { display:none; position:absolute; bottom:60px; right:0; width:300px; background:#fff; border:1px solid #ececec; border-radius:16px; box-shadow:0 12px 36px rgba(0,0,0,.14); padding:20px; }
  @media (max-width: 400px) {
    #feedbackWidget { bottom:80px; right:16px; }
    #feedbackPanel  { width: calc(100vw - 32px); right: auto; left: 50%; transform: translateX(-50%); bottom: 60px; }
  }
</style>
<div id="feedbackWidget">
  <button
    id="feedbackToggle"
    title="Enviar feedback"
    style="width:48px; height:48px; border-radius:999px; background:#111; color:#fff; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(0,0,0,.22); transition:transform .15s, box-shadow .15s;"
    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(0,0,0,.28)'"
    onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 16px rgba(0,0,0,.22)'"
    aria-label="Feedback"
  >
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
  </button>

  <div id="feedbackPanel">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
      <span style="font-size:15px; font-weight:700; color:#111;">Envianos tu feedback</span>
      <button id="feedbackClose" style="background:none; border:none; cursor:pointer; color:#aaa; font-size:18px; line-height:1; padding:0;" aria-label="Cerrar">&times;</button>
    </div>

    <div id="feedbackSuccess" style="display:none; background:#e8f7ee; color:#157347; border:1px solid #cfe9d7; border-radius:10px; padding:12px 14px; font-size:14px; font-weight:600; text-align:center;">
      Gracias por tu feedback.
    </div>

    <div id="feedbackForm">
      <textarea
        id="feedbackMessage"
        rows="4"
        placeholder="Sugerencia, error o comentario..."
        style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px; font-size:14px; font-family:inherit; resize:vertical; color:#111; background:#fff; outline:none; box-sizing:border-box;"
      ></textarea>
      <div id="feedbackError" style="display:none; color:#b91c1c; font-size:13px; margin-top:6px;"></div>
      <button
        id="feedbackSubmit"
        style="margin-top:10px; width:100%; padding:10px; background:#111; color:#fff; border:none; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; font-family:inherit; transition:background .15s;"
        onmouseover="if(!this.disabled) this.style.background='#222'"
        onmouseout="this.style.background='#111'"
      >Enviar</button>
    </div>
  </div>
</div>

<script>
(function() {
  var toggle  = document.getElementById('feedbackToggle');
  var panel   = document.getElementById('feedbackPanel');
  var close   = document.getElementById('feedbackClose');
  var submit  = document.getElementById('feedbackSubmit');
  var msg     = document.getElementById('feedbackMessage');
  var err     = document.getElementById('feedbackError');
  var success = document.getElementById('feedbackSuccess');
  var form    = document.getElementById('feedbackForm');

  function openPanel()  { panel.style.display = 'block'; }
  function closePanel() { panel.style.display = 'none'; success.style.display = 'none'; form.style.display = 'block'; msg.value = ''; err.style.display = 'none'; }

  toggle.addEventListener('click', function(e) {
    e.stopPropagation();
    panel.style.display === 'none' ? openPanel() : closePanel();
  });

  close.addEventListener('click', closePanel);

  document.addEventListener('click', function(e) {
    if (panel.style.display !== 'none' && !document.getElementById('feedbackWidget').contains(e.target)) {
      closePanel();
    }
  });

  submit.addEventListener('click', function() {
    var message = msg.value.trim();
    if (message.length < 10) {
      err.textContent = 'El mensaje debe tener al menos 10 caracteres.';
      err.style.display = 'block';
      return;
    }
    err.style.display = 'none';
    submit.disabled = true;
    submit.textContent = 'Enviando...';

    fetch('{{ route('feedback.store') }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ feedback_message: message, website_url: '' }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      submit.disabled = false;
      submit.textContent = 'Enviar';
      if (data.ok) {
        form.style.display = 'none';
        success.style.display = 'block';
        setTimeout(closePanel, 2000);
      } else {
        err.textContent = 'Ocurrió un error. Intentá de nuevo.';
        err.style.display = 'block';
      }
    })
    .catch(function() {
      submit.disabled = false;
      submit.textContent = 'Enviar';
      err.textContent = 'Ocurrió un error. Intentá de nuevo.';
      err.style.display = 'block';
    });
  });
})();
</script>


@auth
<script>
(function() {
  if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

  const VAPID_PUBLIC_KEY = '{{ config("webpush.vapid.public_key") }}';

  function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = atob(base64);
    return Uint8Array.from([...rawData].map(c => c.charCodeAt(0)));
  }

  function savePushSubscription(sub) {
    const data = sub.toJSON();
    fetch('{{ route("push.subscribe") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({
        endpoint: data.endpoint,
        keys: data.keys,
        content_encoding: (PushManager.supportedContentEncodings || ['aesgcm'])[0],
      }),
    });
  }

  navigator.serviceWorker.register('/sw.js').then(function(reg) {
    return reg.pushManager.getSubscription().then(function(sub) {
      if (Notification.permission === 'denied') return;

      if (sub) {
        // Ya suscripto en el browser → guardar en el servidor por si no estaba
        savePushSubscription(sub);
        return;
      }

      const askPush = function() {
        reg.pushManager.subscribe({
          userVisibleOnly: true,
          applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
        }).then(savePushSubscription).catch(function() {});
      };

      if (Notification.permission === 'granted') {
        askPush();
      } else {
        document.addEventListener('click', function onFirstClick() {
          document.removeEventListener('click', onFirstClick);
          Notification.requestPermission().then(function(perm) {
            if (perm === 'granted') askPush();
          });
        }, { once: true });
      }
    });
  }).catch(function() {});
})();
</script>
@endauth
</body>
</html>
