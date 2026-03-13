@extends('layouts.app')

@section('title', 'Página no encontrada')

@section('content')
  <div class="page-card" style="text-align:center; padding:60px 24px;">
    <div style="font-size:80px; line-height:1; margin-bottom:16px;">🏟️</div>

    <h1 style="font-size:48px; font-weight:800; margin:0 0 12px 0;">404</h1>

    <p style="font-size:20px; color:#666; margin:0 0 8px 0;">
      Esta página no existe o fue eliminada.
    </p>

    <p style="color:#999; margin:0 0 32px 0;">
      Puede que la URL esté mal escrita o que el contenido ya no esté disponible.
    </p>

    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
      <a href="{{ route('home') }}" class="btn btn-primary">
        Volver al inicio
      </a>
      <a href="{{ route('venues.index') }}" class="btn">
        Ver complejos
      </a>
    </div>
  </div>
@endsection