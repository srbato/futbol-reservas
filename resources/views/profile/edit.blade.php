@extends('layouts.app')

@section('title', 'Mi perfil')

@section('content')
  <div class="page-card" style="margin-bottom:22px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 8px 0; font-size:34px; letter-spacing:-0.02em;">Mi perfil</h1>
        <p class="muted" style="margin:0;">
          Actualizá tus datos personales, cambiá tu contraseña o eliminá tu cuenta.
        </p>
      </div>

      <div>
        <a href="{{ route('venues.index') }}" class="btn">Volver a complejos</a>
      </div>
    </div>
  </div>

  <div class="grid" style="grid-template-columns:1fr; gap:18px;">
    <div class="page-card">
      <h2 style="margin:0 0 14px 0; font-size:24px;">Información de perfil</h2>
      @include('profile.partials.update-profile-information-form')
    </div>

    <div class="page-card">
      <h2 style="margin:0 0 14px 0; font-size:24px;">Cambiar contraseña</h2>
      @include('profile.partials.update-password-form')
    </div>

    <div class="page-card" style="border-color:#f1b9c0;">
      <h2 style="margin:0 0 14px 0; font-size:24px; color:#842029;">Zona peligrosa</h2>
      @include('profile.partials.delete-user-form')
    </div>
  </div>
@endsection
