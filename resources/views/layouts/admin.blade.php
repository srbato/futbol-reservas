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

    /* ── Buttons ── */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      padding: 9px 16px;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      border: 1px solid transparent;
      text-decoration: none;
      transition: opacity .15s, background .15s;
      line-height: 1;
      white-space: nowrap;
    }
    .btn:hover { opacity: .82; }
    .btn-primary { background: #111; color: #fff; border-color: #111; }
    .btn-ghost   { background: #fff; color: #111; border-color: #ddd; }
    .btn-ghost:hover { background: #f6f6f6; opacity: 1; }
    .btn-danger  { background: #842029; color: #fff; border-color: #842029; }
    .btn-warning { background: #856404; color: #fff; border-color: #856404; }
    .btn-success { background: #157347; color: #fff; border-color: #157347; }
    .btn-sm { padding: 6px 12px; font-size: 13px; border-radius: 8px; }

    /* ── Badges ── */
    .badge {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
    }
    .badge-default { background: #f1f1f1; color: #444; }
    .badge-success { background: #d1e7dd; color: #0f5132; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-danger  { background: #f8d7da; color: #842029; }
    .badge-info    { background: #cfe2ff; color: #084298; }
    .badge-dark    { background: #111; color: #fff; }

    /* ── Form controls ── */
    .form-label {
      display: block;
      font-size: 12px;
      font-weight: 600;
      color: #555;
      margin-bottom: 5px;
    }
    .form-control {
      padding: 9px 12px;
      border: 1px solid #ddd;
      border-radius: 10px;
      font-size: 14px;
      background: #fff;
      transition: border-color .15s;
      box-sizing: border-box;
    }
    .form-control:focus {
      outline: none;
      border-color: #111;
    }
    .form-row {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      align-items: flex-end;
    }
    .form-group {
      display: flex;
      flex-direction: column;
    }

    /* ── Data table ── */
    .data-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }
    .data-table th {
      text-align: left;
      padding: 11px 16px;
      background: #fafafa;
      border-bottom: 2px solid #ececec;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em;
      color: #888;
      white-space: nowrap;
    }
    .data-table td {
      padding: 12px 16px;
      border-bottom: 1px solid #f3f3f3;
      vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #fafafa; }

    /* ── Section ── */
    .section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 18px;
    }
    .section-title {
      font-size: 16px;
      font-weight: 700;
      margin: 0;
    }
    .section-subtitle {
      font-size: 13px;
      color: #666;
      margin: 4px 0 0 0;
    }

    /* ── Filter bar ── */
    .filter-bar {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      align-items: flex-end;
      padding: 16px 18px;
      background: #fff;
      border: 1px solid #ececec;
      border-radius: 14px;
      margin-bottom: 20px;
    }

    /* ── Empty state ── */
    .empty-state {
      text-align: center;
      padding: 48px 24px;
      color: #bbb;
      font-size: 15px;
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
  @stack('styles')
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
        @unless(auth()->user()->isVenueStaff())
          <a href="{{ route('va.reports') }}" class="{{ request()->routeIs('va.reports') ? 'active' : '' }}">
            Reportes
          </a>
        @endunless
        <a href="{{ route('va.blocks.index') }}" class="{{ request()->routeIs('va.blocks.*') ? 'active' : '' }}">
          Bloqueos
        </a>
        <a href="{{ route('va.discounts.index') }}" class="{{ request()->routeIs('va.discounts.*') ? 'active' : '' }}">
          Descuentos
        </a>
        @if(auth()->user()->role === 'venue_admin')
          <a href="{{ route('va.staff.index') }}" class="{{ request()->routeIs('va.staff.*') ? 'active' : '' }}">
            Empleados
          </a>
        @endif
      </nav>

      @if(auth()->user()->role === 'super_admin')
        <div class="admin-section-title">Super Admin</div>
        <nav class="admin-nav">
          <a href="{{ route('sa.users.index') }}" class="{{ request()->routeIs('sa.users.*') ? 'active' : '' }}">
            Usuarios
          </a>
          <a href="{{ route('sa.messages.index') }}" class="{{ request()->routeIs('sa.messages.*') ? 'active' : '' }}">
            Mensajes
          </a>
          <a href="{{ route('sa.plans.index') }}" class="{{ request()->routeIs('sa.plans.*') ? 'active' : '' }}">
            Planes
          </a>
        </nav>
      @endif

      @php
        $userHasVenue = auth()->user()->role === 'super_admin'
            || \App\Models\Venue::where('owner_user_id', auth()->id())->exists();
      @endphp

      @if(!$userHasVenue && !auth()->user()->isVenueStaff())
        <div class="admin-section-title">Gestión</div>
        <nav class="admin-nav">
          <a href="{{ route('va.venues.create') }}" class="{{ request()->routeIs('va.venues.create') ? 'active' : '' }}">
            Crear complejo
          </a>
        </nav>
      @endif

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