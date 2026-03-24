<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Reservas de canchas')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  * { box-sizing: border-box; }

  body {
    margin: 0;
    font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    background: #f7f7f8;
    color: #111;
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
    border-bottom: 1px solid #ececec;
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
    font-size: 20px;
    font-weight: 800;
    text-decoration: none;
  }

  .site-nav {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
  }

  .site-nav a,
  .site-nav button {
    text-decoration: none;
    border: 1px solid #ddd;
    background: #fff;
    padding: 9px 14px;
    border-radius: 999px;
    cursor: pointer;
    font-size: 14px;
  }

  .site-nav a.primary {
    background: #111;
    color: #fff;
    border-color: #111;
  }

  .user-menu-wrap {
    position: relative;
  }

  .user-menu-button {
    text-decoration: none;
    border: 1px solid #ddd;
    background: #fff;
    padding: 9px 14px;
    border-radius: 999px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
  }

  .user-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    min-width: 210px;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    box-shadow: 0 10px 28px rgba(0,0,0,.10);
    padding: 8px;
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
    color: #111;
  }

  .user-dropdown a:hover,
  .user-dropdown-logout:hover {
    background: #f5f5f5;
  }

  .user-dropdown-admin {
    background: #111 !important;
    color: #fff !important;
    font-weight: 600 !important;
    margin-bottom: 4px;
  }

  .user-dropdown-admin:hover {
    background: #222 !important;
  }

  /* ── Notificaciones ────────────────────────────────── */
  .notif-wrap {
    position: relative;
  }

  .notif-bell {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 999px;
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
    background: #e53935;
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
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    box-shadow: 0 10px 28px rgba(0,0,0,.10);
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
    border-bottom: 1px solid #f0f0f0;
  }

  .notif-dropdown-header span {
    font-size: 13px;
    font-weight: 700;
    color: #111;
  }

  .notif-mark-all {
    font-size: 12px;
    color: #555;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    text-decoration: underline;
  }

  .notif-mark-all:hover {
    color: #111;
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
    color: #111;
  }

  .notif-item:hover {
    background: #f9f9f9;
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
    color: #666;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .notif-time {
    font-size: 11px;
    color: #aaa;
    margin-top: 3px;
  }

  .notif-empty {
    padding: 24px 16px;
    text-align: center;
    color: #aaa;
    font-size: 13px;
  }

  .site-main {
    max-width: 1500px;
    margin: 0 auto;
    padding: 24px 24px 40px 24px;
  }

  .page-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    padding: 18px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
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
    color: #f5b301;
    font-weight: 700;
  }

  .stars-text {
    color: #444;
    font-size: 14px;
    font-weight: 600;
    margin-left: 6px;
  }

  .muted {
    color: #666;
  }

  .grid {
    display: grid;
    gap: 16px;
  }

  .grid-venues {
    grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
  }

  .venue-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
    display: flex;
    flex-direction: column;
    transition: all .18s ease;
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
    background: #f4f4f4;
    font-size: 12px;
    color: #555;
    font-weight: 700;
  }

  .btn {
    display: inline-block;
    text-decoration: none;
    border: 1px solid #ddd;
    background: #fff;
    color: #111;
    padding: 10px 16px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    font-family: inherit;
    transition: all .15s ease;
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
    background: #111;
    color: #fff;
    border-color: #111;
  }

  .toolbar {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: end;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 22px;
    padding: 18px;
    margin-bottom: 20px;
    box-shadow: 0 4px 18px rgba(0,0,0,.04);
  }

  .toolbar input,
  .toolbar select {
    padding: 11px 12px;
    border: 1px solid #ddd;
    border-radius: 14px;
    min-width: 190px;
    background: #fff;
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
    color: #f5b301;
  }

  .review-stars-picker button:hover {
    transform: scale(1.05);
  }

  .review-rating-text {
    font-size: 14px;
    color: #666;
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
    color: #666;
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
    border-radius: 999px;
    border: 1px solid #ddd;
    cursor: pointer;
    font-weight: 600;
    background: #fff;
    transition: all .15s ease;
    user-select: none;
  }

  .feature-tab:hover {
    transform: translateY(-1px);
  }

  .feature-tab.active {
    background: #111;
    color: #fff;
    border-color: #111;
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
    background: #ddd;
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
    border-radius: 999px;
    border: 1px solid #d9d9d9;
    background: #fff;
    color: #111;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all .15s ease;
    box-shadow: 0 6px 18px rgba(0,0,0,.14);
  }

  .featured-nav-arrow:hover {
    transform: translateY(-1px);
    border-color: #111;
  }

  .carousel-card {
    min-width: calc((100% - 36px) / 3);
    max-width: calc((100% - 36px) / 3);
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0,0,0,.04);
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
    color: #444;
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

  /* ── Mobile ────────────────────────────────────── */
  @media (max-width: 600px) {
    .site-header-inner {
      padding: 10px 16px;
      gap: 10px;
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
<header class="site-header">
    <div class="site-header-inner">
      <a href="{{ route('home') }}" class="brand">TuCancha</a>

      <nav class="site-nav">
        <a href="{{ route('home') }}"
           @if(request()->routeIs('home')) style="background:#f4f4f4; font-weight:700;" @endif>Inicio</a>
        <a href="{{ route('venues.index') }}"
           @if(request()->routeIs('venues.*')) style="background:#f4f4f4; font-weight:700;" @endif>Complejos</a>
        <a href="{{ route('falta-uno.index') }}"
           @if(request()->routeIs('falta-uno.*')) style="background:#111; color:#fff; border-color:#111; font-weight:700;" @endif>⚡ Falta Uno</a>

        @auth
          @if(auth()->user()->role === 'user')
            <a href="{{ route('planes') }}">Hacete socio</a>
          @endif

        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
        <div class="notif-wrap">
            <button type="button" class="notif-bell" id="notifBellBtn" onclick="toggleNotifDropdown()" aria-label="Notificaciones">
                🔔
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
                                <span class="notif-icon">{{ $notif->data['icon'] ?? '🔔' }}</span>
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
                       style="font-size:13px; color:#555; text-decoration:none; font-weight:700;">
                        Ver todas las notificaciones →
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
                    <div style="width:32px; height:32px; border-radius:999px; background:#f1f1f1; display:flex; align-items:center; justify-content:center; font-size:14px; color:#999; border:1px solid #eee;">
                        👤
                    </div>
                @endif
                <span>{{ auth()->user()->name }}</span> ▾
            </button>

            <div id="userDropdown" class="user-dropdown">
              <a href="{{ route('profile.edit') }}">Perfil</a>
              <a href="{{ route('my_reservations') }}">Mi actividad</a>
              <a href="{{ route('venues.favorites') }}">Favoritos</a>

@if(in_array(auth()->user()->role, ['venue_admin', 'super_admin']) || auth()->user()->isVenueStaff())
                <a href="{{ route('va.dashboard') }}" class="user-dropdown-admin">⚡ Panel admin</a>
              @endif

              <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="user-dropdown-logout">Salir</button>
              </form>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}">Ingresar</a>
          <a href="{{ route('register') }}" class="primary">Crear cuenta</a>
        @endauth
      </nav>
    </div>
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

  <script>
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

      const echo = new Echo({
        broadcaster: 'reverb',
        key: reverbKey,
        wsHost: wsHost,
        wsPort: wsPort,
        wssPort: wsPort,
        forceTLS: true,
        enabledTransports: ['ws'],
        authEndpoint: '/broadcasting/auth',
        auth: {
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
          },
        },
      });

      echo.private('user.{{ auth()->id() }}')
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
</body>
</html>
