<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Panel admin')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: #f7f7f8;
      color: #111;
    }

    .admin-shell {
      display: flex;
      min-height: 100vh;
    }

    .admin-sidebar {
      width: 250px;
      background: #111;
      color: #fff;
      padding: 20px 16px;
      flex-shrink: 0;
    }

    .admin-brand {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 24px;
    }

    .admin-section-title {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: .08em;
      color: rgba(255,255,255,.55);
      margin: 18px 0 8px 0;
    }

    .admin-nav {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .admin-nav a {
      color: #fff;
      text-decoration: none;
      padding: 10px 12px;
      border-radius: 10px;
      display: block;
      transition: .15s ease;
    }

    .admin-nav a:hover {
      background: rgba(255,255,255,.08);
    }

    .admin-nav a.active {
      background: #fff;
      color: #111;
      font-weight: 600;
    }

    .admin-main {
      flex: 1;
      padding: 24px;
      min-width: 0;
    }

    .admin-topbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .admin-topbar h1 {
      margin: 0;
      font-size: 28px;
    }

    .admin-topbar .muted {
      color: #666;
      font-size: 14px;
    }

    .admin-content {
      background: transparent;
    }

    .admin-card {
      background: #fff;
      border: 1px solid #ececec;
      border-radius: 16px;
      padding: 18px;
      box-shadow: 0 2px 12px rgba(0,0,0,.03);
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

    @media (max-width: 900px) {
      .admin-shell {
        flex-direction: column;
      }

      .admin-sidebar {
        width: 100%;
      }

      .admin-main {
        padding: 16px;
      }
    }
  </style>
</head>
<body>
  @php
    $adminSubscription = null;

    if (auth()->check() && auth()->user()->role === 'venue_admin') {
        $adminSubscription = auth()->user()->activeVenueAdminSubscription()->first();
    }
  @endphp

  <div class="admin-shell">
    <aside class="admin-sidebar">
      <div class="admin-brand">Admin Canchas</div>

      <div class="admin-section-title">Panel</div>
      <nav class="admin-nav">
        <a href="{{ route('va.dashboard') }}" class="{{ request()->routeIs('va.dashboard') ? 'active' : '' }}">
          Dashboard
        </a>
        <a href="{{ route('va.reservations.index') }}" class="{{ request()->routeIs('va.reservations.index') ? 'active' : '' }}">
          Reservas
        </a>
        <a href="{{ route('va.reservations.agenda') }}" class="{{ request()->routeIs('va.reservations.agenda') ? 'active' : '' }}">
          Agenda
        </a>
        <a href="{{ route('va.reports') }}">
          Reportes
        </a>
        <a href="{{ route('va.blocks.index') }}" class="{{ request()->routeIs('va.blocks.*') ? 'active' : '' }}">
          Bloqueos
        </a>
        <a href="{{ route('va.discounts.index') }}" class="{{ request()->routeIs('va.discounts.*') ? 'active' : '' }}">
          Descuentos
        </a>

        @if(auth()->user()->role === 'super_admin')
          <a href="{{ route('sa.users.index') }}" class="{{ request()->routeIs('sa.users.*') ? 'active' : '' }}">
            Usuarios
          </a>
        @endif

        @if(auth()->user()->role === 'super_admin')
          <a href="{{ route('sa.messages.index') }}" class="{{ request()->routeIs('sa.messages.*') ? 'active' : '' }}">
            Mensajes
          </a>
        @endif
      </nav>

      <div class="admin-section-title">Gestión</div>
      <nav class="admin-nav">
        <a href="{{ route('va.venues.create') }}" class="{{ request()->routeIs('va.venues.create') ? 'active' : '' }}">
          Crear complejo
        </a>
      </nav>

      <div class="admin-section-title">Navegación</div>
      <nav class="admin-nav">
        <a href="{{ route('venues.index') }}">
          Ver sitio público
        </a>
        <a href="{{ route('home') ?? url('/') }}">
          Ir al inicio
        </a>
        @if(auth()->user()->role === 'venue_admin')
          <a href="{{ route('membership.become') }}">
            Mi membresía
          </a>
        @endif
      </nav>
    </aside>

    <main class="admin-main">
      <div class="admin-topbar">
        <div>
          <h1>@yield('page_title', 'Panel admin')</h1>
          <div class="muted">@yield('page_subtitle', 'Gestioná tus complejos, canchas y reservas')</div>
        </div>
      </div>

      @if(auth()->check() && auth()->user()->role === 'venue_admin' && $adminSubscription)
        <div class="flash success">
          Tu membresía está activa hasta
          <strong>{{ $adminSubscription->expires_at?->format('d/m/Y H:i') }}</strong>.
          <a href="{{ route('membership.become') }}" style="margin-left:8px; font-weight:700;">Ver membresía</a>
        </div>
      @endif

      @if(session('success'))
        <div class="flash success">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="flash error">{{ session('error') }}</div>
      @endif

      @if($errors->any())
        <div class="flash error">
          <strong>Hay errores en el formulario:</strong>
          <ul style="margin:8px 0 0 18px;">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="admin-content">
        @yield('content')
      </div>
    </main>
  </div>
</body>
</html>