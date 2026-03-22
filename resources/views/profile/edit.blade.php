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

    {{-- Perfil deportivo --}}
    <div class="page-card">
      <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
        <h2 style="margin:0; font-size:24px;">Perfil deportivo</h2>
        <a href="{{ route('sport-profile.create') }}"
           style="display:inline-block; background:#111; color:#fff; border-radius:10px; padding:7px 16px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap;">
          + Nuevo deporte
        </a>
      </div>
      @php $sportProfiles = auth()->user()->faltaUnoSportProfiles()->get(); @endphp
      @if($sportProfiles->isEmpty())
        <p style="color:#888; font-size:14px; margin:0;">
          Aún no tenés perfiles deportivos. Creá uno para poder unirte a partidos Falta Uno.
        </p>
      @else
        <div style="display:grid; gap:10px;">
          @foreach($sportProfiles as $sp)
          @php
            $spLabel = match($sp->sport) {
              'football'   => '⚽ Fútbol',
              'padel'      => '🏓 Pádel',
              'tennis'     => '🎾 Tenis',
              'basketball' => '🏀 Básquet',
              'volleyball' => '🏐 Vóley',
              default      => ucfirst($sp->sport),
            };
          @endphp
          <div style="display:flex; align-items:center; gap:14px; padding:12px 14px; background:#f8f8f8; border-radius:12px; flex-wrap:wrap;">
            <div style="font-size:22px;">{{ explode(' ', $spLabel)[0] }}</div>
            <div style="flex:1; min-width:120px;">
              <div style="font-weight:700; font-size:14px;">{{ ltrim(strstr($spLabel, ' ')) }}</div>
              <div style="font-size:12px; color:#888; text-transform:capitalize; margin-top:2px;">
                {{ $sp->category }}
                @if($sp->age_group) · {{ $sp->age_group }} @endif
              </div>
            </div>
            <div style="font-size:12px; color:#888;">
              {{ $sp->games_played }} PJ · {{ number_format($sp->average_rating, 1) }} ★
            </div>
            <a href="{{ route('sport-profile.edit', $sp->sport) }}"
               style="font-size:12px; padding:5px 14px; border:1.5px solid #e0e0e0; border-radius:8px; color:#333; text-decoration:none; font-weight:600; white-space:nowrap;">
              Editar
            </a>
          </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Mis partidos Falta Uno --}}
    @php
      $userId = auth()->id();

      $misPartidos = App\Models\FaltaUnoGame::with(['field.venue', 'activeParticipants'])
        ->where(function($q) use ($userId) {
          $q->where('initiator_user_id', $userId)
            ->orWhereHas('participants', fn($q2) => $q2->where('user_id', $userId)->where('status', 'confirmed'));
        })
        ->whereIn('status', ['open', 'full', 'expired', 'cancelled'])
        ->orderByDesc('start_at')
        ->limit(15)
        ->get();

      $proximosPartidos = $misPartidos->filter(fn($g) => $g->start_at->isFuture());
      $partidosPasados  = $misPartidos->filter(fn($g) => $g->start_at->isPast());
    @endphp

    @if($misPartidos->isNotEmpty())
    <div class="page-card">
      <h2 style="margin:0 0 18px 0; font-size:24px;">Mis partidos ⚡</h2>

      {{-- Próximos --}}
      @if($proximosPartidos->isNotEmpty())
        <h3 style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#aaa; margin:0 0 10px;">Próximos</h3>
        <div style="display:grid; gap:10px; margin-bottom:20px;">
          @foreach($proximosPartidos as $pg)
          @php
            $esIniciador = $pg->initiator_user_id === $userId;
            $sportLabel  = match($pg->field->sport ?? '') {
              'football'   => '⚽ Fútbol', 'padel' => '🏓 Pádel',
              'tennis'     => '🎾 Tenis',  'basketball' => '🏀 Básquet',
              'volleyball' => '🏐 Vóley',  default => ucfirst($pg->field->sport ?? ''),
            };
          @endphp
          <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; background:#f8f8f8; border-radius:12px; flex-wrap:wrap;">
            <div style="font-size:22px;">{{ explode(' ', $sportLabel)[0] }}</div>
            <div style="flex:1; min-width:140px;">
              <div style="font-weight:700; font-size:14px;">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</div>
              <div style="font-size:12px; color:#888; margin-top:2px;">
                {{ $pg->start_at->format('d/m/Y H:i') }} hs
                @if($esIniciador) · <span style="color:#2563eb;">Organizador</span> @endif
              </div>
            </div>
            <span style="font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px;
                         background:{{ $pg->status === 'full' ? '#dcfce7' : '#fef9c3' }};
                         color:{{ $pg->status === 'full' ? '#15803d' : '#854d0e' }};">
              {{ $pg->status === 'full' ? 'Completo' : 'Buscando jugadores' }}
            </span>
            <a href="{{ route('falta-uno.show', $pg) }}"
               style="font-size:12px; padding:5px 14px; border:1.5px solid #e0e0e0; border-radius:8px; color:#333; text-decoration:none; font-weight:600; white-space:nowrap;">
              Ver partido
            </a>
            <a href="{{ route('falta-uno.chat', $pg) }}"
               style="font-size:12px; padding:5px 14px; border:1.5px solid #e0e0e0; border-radius:8px; color:#333; text-decoration:none; font-weight:600; white-space:nowrap;">
              💬 Chat
            </a>
          </div>
          @endforeach
        </div>
      @endif

      {{-- Pasados --}}
      @if($partidosPasados->isNotEmpty())
        <h3 style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#aaa; margin:0 0 10px;">Historial</h3>
        <div style="display:grid; gap:10px;">
          @foreach($partidosPasados as $pg)
          @php
            $esIniciador = $pg->initiator_user_id === $userId;
            $sportLabel  = match($pg->field->sport ?? '') {
              'football'   => '⚽ Fútbol', 'padel' => '🏓 Pádel',
              'tennis'     => '🎾 Tenis',  'basketball' => '🏀 Básquet',
              'volleyball' => '🏐 Vóley',  default => ucfirst($pg->field->sport ?? ''),
            };
            $yaCalifico = App\Models\FaltaUnoRating::where('game_id', $pg->id)
              ->where('rater_user_id', $userId)->exists();
            $hayOtros = $pg->activeParticipants->where('user_id', '!=', $userId)->isNotEmpty()
              || ($esIniciador && $pg->activeParticipants->isNotEmpty())
              || (!$esIniciador);
          @endphp
          <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; background:#f8f8f8; border-radius:12px; flex-wrap:wrap;">
            <div style="font-size:22px; opacity:.6;">{{ explode(' ', $sportLabel)[0] }}</div>
            <div style="flex:1; min-width:140px;">
              <div style="font-weight:700; font-size:14px; color:#444;">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</div>
              <div style="font-size:12px; color:#aaa; margin-top:2px;">
                {{ $pg->start_at->format('d/m/Y H:i') }} hs
                @if($esIniciador) · <span style="color:#888;">Organizador</span> @endif
              </div>
            </div>
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
              @if(!$yaCalifico && $hayOtros && in_array($pg->status, ['open','full','expired']))
                <a href="{{ route('falta-uno.rate', $pg) }}"
                   style="font-size:12px; padding:5px 14px; background:#fef3c7; border-radius:8px; color:#92400e; text-decoration:none; font-weight:700; white-space:nowrap;">
                  ★ Calificar
                </a>
              @elseif($yaCalifico)
                <span style="font-size:12px; color:#aaa; font-weight:600;">✓ Calificado</span>
              @endif
              <a href="{{ route('falta-uno.chat', $pg) }}"
                 style="font-size:12px; padding:5px 14px; background:#f4f4f4; border-radius:8px; color:#555; text-decoration:none; font-weight:600; white-space:nowrap;">
                💬 Chat
              </a>
              <a href="{{ route('falta-uno.stats', $pg) }}"
                 style="font-size:12px; padding:5px 14px; background:#f4f4f4; border-radius:8px; color:#555; text-decoration:none; font-weight:600; white-space:nowrap;">
                📊 Stats
              </a>
            </div>
          </div>
          @endforeach
        </div>
      @endif

    </div>
    @endif

    <div class="page-card" style="border-color:#f1b9c0;">
      <h2 style="margin:0 0 14px 0; font-size:24px; color:#842029;">Zona peligrosa</h2>
      @include('profile.partials.delete-user-form')
    </div>
  </div>
@endsection
