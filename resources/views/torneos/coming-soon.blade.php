@extends('layouts.app')

@section('title', 'Torneos — Proximamente')

@section('content')
<div style="max-width:560px; margin:80px auto; text-align:center; padding:0 24px;">

  <div style="width:80px; height:80px; border-radius:20px; background:rgba(34,197,94,.08); border:1px solid rgba(34,197,94,.15); display:flex; align-items:center; justify-content:center; margin:0 auto 28px;">
    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
      <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 7 7 7 7"/>
      <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 17 7 17 7"/>
      <path d="M4 22h16"/>
      <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
      <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
      <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
    </svg>
  </div>

  <h1 style="font-size:36px; font-weight:800; letter-spacing:-0.03em; margin:0 0 12px; color:#e8e8e8;">
    Torneos
  </h1>

  <p style="font-size:17px; color:#a0a0a0; line-height:1.6; margin:0 0 12px;">
    Estamos preparando algo increible.
  </p>

  <p style="font-size:15px; color:#666; line-height:1.6; margin:0 0 36px;">
    Muy pronto vas a poder crear torneos, armar fixtures, inscribir equipos y gestionar todo desde TuCancha.
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
