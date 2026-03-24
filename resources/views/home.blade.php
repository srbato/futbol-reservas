@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
  <h1 style="margin:0 0 8px 0;">Reservá tu cancha en minutos</h1>
  <p style="margin:0 0 16px 0; color:#555;">
    Elegí complejo, mirá horarios disponibles, reservá y pagá desde la web.
  </p>

  <div style="display:flex; gap:12px; flex-wrap:wrap;">
    <div style="border:1px solid #eee; border-radius:12px; padding:12px; width:100%; max-width:260px; flex:1 1 220px;">
      <div style="font-weight:700;">Para jugadores</div>
      <div style="color:#666; font-size:14px;">Buscá disponibilidad y reservá.</div>
      <div style="margin-top:10px;">
        <a href="{{ url('/venues') }}">Ver canchas →</a>
      </div>
    </div>

    <div style="border:1px solid #eee; border-radius:12px; padding:12px; width:100%; max-width:260px; flex:1 1 220px;">
      <div style="font-weight:700;">Para administradores</div>
      <div style="color:#666; font-size:14px;">Publicá canchas, precios y horarios.</div>
      <div style="margin-top:10px;">
        @auth
          <a href="{{ route('va.dashboard') }}">Ir a Mis canchas →</a>
        @else
          <a href="{{ route('login') }}">Iniciar sesión →</a>
        @endauth
      </div>
    </div>
  </div>
@endsection