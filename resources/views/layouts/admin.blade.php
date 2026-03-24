<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>@yield('title', 'Panel admin') · TuCancha</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>
<body class="bg-slate-100 text-slate-900 antialiased">

@php
  $adminSubscription = null;
  if (auth()->check() && auth()->user()->role === 'venue_admin') {
      $adminSubscription = auth()->user()->activeVenueAdminSubscription()->first();
  }
@endphp

<div class="flex min-h-screen">

  {{-- ── SIDEBAR ── --}}
  <aside class="w-64 flex-shrink-0 bg-slate-900 flex flex-col">

    {{-- Brand --}}
    <div class="px-6 pt-6 pb-4 border-b border-slate-800">
      <a href="{{ route('home') ?? url('/') }}" class="flex items-center gap-2">
        <span class="text-white font-extrabold text-xl tracking-tight">TuCancha</span>
        <span class="text-xs font-semibold text-indigo-400 uppercase tracking-widest">Admin</span>
      </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-5 space-y-1 overflow-y-auto">

      {{-- Panel venueadmin --}}
      <p class="px-2 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-widest">Panel</p>

      <a href="{{ route('va.dashboard') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                {{ request()->routeIs('va.dashboard') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Dashboard
      </a>

      <a href="{{ route('va.reservations.index') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                {{ request()->routeIs('va.reservations.index') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Reservas
      </a>

      <a href="{{ route('va.reservations.agenda') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                {{ request()->routeIs('va.reservations.agenda') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Agenda
      </a>

      @unless(auth()->user()->isVenueStaff())
      <a href="{{ route('va.reports') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                {{ request()->routeIs('va.reports') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        Reportes
      </a>
      @endunless

      <a href="{{ route('va.blocks.index') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                {{ request()->routeIs('va.blocks.*') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
        Bloqueos
      </a>

      <a href="{{ route('va.discounts.index') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                {{ request()->routeIs('va.discounts.*') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/>
        </svg>
        Descuentos
      </a>

      @if(auth()->user()->role === 'venue_admin')
      <a href="{{ route('va.staff.index') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                {{ request()->routeIs('va.staff.*') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Empleados
      </a>
      @endif

      {{-- Superadmin section --}}
      @if(auth()->user()->role === 'super_admin')
        <div class="pt-4">
          <p class="px-2 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-widest">Superadmin</p>
        </div>

        <a href="{{ route('sa.users.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('sa.users.*') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          Usuarios
        </a>

        <a href="{{ route('sa.messages.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('sa.messages.*') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
          </svg>
          Mensajes
        </a>

        <a href="{{ route('sa.plans.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('sa.plans.*') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
          </svg>
          Planes
        </a>

        {{-- Pagos a venues oculto hasta que se reactive el sistema de referidos --}}
      @endif

      {{-- Gestión --}}
      @php
        $userHasVenue = auth()->user()->role === 'super_admin'
            || \App\Models\Venue::where('owner_user_id', auth()->id())->exists();
      @endphp

      @if(!$userHasVenue && !auth()->user()->isVenueStaff())
        <div class="pt-4">
          <p class="px-2 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-widest">Gestión</p>
        </div>
        <a href="{{ route('va.venues.create') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                  {{ request()->routeIs('va.venues.create') ? 'bg-white text-slate-900' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          Crear complejo
        </a>
      @endif

      {{-- Navegación --}}
      <div class="pt-4">
        <p class="px-2 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-widest">Navegación</p>
      </div>

      <a href="{{ route('venues.index') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
        Ver sitio público
      </a>

      <a href="{{ route('home') ?? url('/') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Ir al inicio
      </a>

      @if(auth()->user()->role === 'venue_admin')
        <a href="{{ route('membership.become') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition-colors">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
          </svg>
          Mi membresía
        </a>
      @endif

    </nav>

    {{-- Mini perfil al pie --}}
    <div class="border-t border-slate-800 px-4 py-4">
      <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
          <span class="text-xs font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
        </div>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
          <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" title="Cerrar sesión"
                  class="text-slate-500 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </form>
      </div>
    </div>

  </aside>

  {{-- ── MAIN ── --}}
  <div class="flex-1 flex flex-col min-w-0">

    {{-- Topbar --}}
    <header class="bg-white border-b border-slate-200 px-8 py-4 flex items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 mb-0.5">
          <span>Panel</span>
          <span>/</span>
          <span class="text-slate-600 font-medium">@yield('page_title', 'Dashboard')</span>
        </div>
        <h1 class="text-xl font-bold text-slate-900 leading-tight">@yield('page_title', 'Panel admin')</h1>
      </div>
      <div class="flex items-center gap-3">
        @if(auth()->user()->role === 'super_admin')
          <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 uppercase tracking-widest">
            Superadmin
          </span>
        @endif
        <span class="text-xs text-slate-400">{{ now()->format('d/m/Y') }}</span>
      </div>
    </header>

    {{-- Content area --}}
    <main class="flex-1 p-8">

      {{-- Flash: membresía activa --}}
      @if(auth()->check() && auth()->user()->role === 'venue_admin' && $adminSubscription)
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
          <svg class="w-4 h-4 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>
            Tu membresía está activa hasta <strong>{{ $adminSubscription->expires_at?->format('d/m/Y H:i') }}</strong>.
            <a href="{{ route('membership.become') }}" class="ml-2 font-bold underline">Ver membresía</a>
          </span>
        </div>
      @endif

      {{-- Flash success --}}
      @if(session('success'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
          <svg class="w-4 h-4 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ session('success') }}</span>
        </div>
      @endif

      {{-- Flash error --}}
      @if(session('error'))
        <div class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
          <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>{{ session('error') }}</span>
        </div>
      @endif

      {{-- Validation errors --}}
      @if($errors->any())
        <div class="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
          <p class="font-bold mb-1">Hay errores en el formulario:</p>
          <ul class="list-disc pl-5 space-y-0.5">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')

    </main>
  </div>

</div>

</body>
</html>
