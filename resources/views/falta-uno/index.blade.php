@extends('layouts.app')

@section('title', 'Falta Uno — Partidos buscando jugadores')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
@endpush

@push('styles')
<style>
  .fu-header {
    background: #111;
    border-radius: 20px;
    padding: 32px 28px;
    margin-bottom: 24px;
    color: #fff;
  }
  .fu-header h1 {
    font-size: 28px;
    font-weight: 800;
    margin: 0 0 6px 0;
    letter-spacing: -.02em;
  }
  .fu-header p {
    font-size: 15px;
    color: rgba(255,255,255,.65);
    margin: 0;
  }

  .sport-filter-btn {
    padding: 7px 16px;
    border: 1.5px solid #e0e0e0;
    background: #fff;
    border-radius: 999px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: all .15s;
    color: #555;
    text-decoration: none;
    display: inline-block;
  }
  .sport-filter-btn:hover { border-color: #bbb; color: #333; }
  .sport-filter-btn.active { background: #111; color: #fff; border-color: #111; }

  .game-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    padding: 18px 20px;
    display: flex;
    gap: 20px;
    align-items: center;
    flex-wrap: wrap;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
    transition: box-shadow .2s, transform .2s;
  }
  .game-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.08);
    transform: translateY(-2px);
  }

  .game-venue-link {
    font-size: 12px;
    color: #888;
    text-decoration: none;
    font-weight: 600;
  }
  .game-venue-link:hover { color: #111; }
</style>
@endpush

@section('content')

<div class="fu-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
  <div>
    <h1 style="margin:0 0 6px;">⚡ Falta Uno</h1>
    <p style="margin:0;">Partidos armándose. Anotate y presentate en el complejo el día del partido.</p>
  </div>
  <a href="{{ route('venues.index', ['falta_uno' => '1']) }}"
     style="display:inline-block; background:#4ade80; color:#052e16; border-radius:12px; padding:10px 22px; font-size:14px; font-weight:800; text-decoration:none; white-space:nowrap; flex-shrink:0;">
    + Crear partido
  </a>
</div>

{{-- Banner sin perfil deportivo --}}
@auth
  @if(auth()->user()->faltaUnoSportProfiles()->doesntExist())
    <div style="background:linear-gradient(135deg,#fffbeb,#fef3c7); border:1px solid #fde68a; border-radius:14px; padding:14px 18px; margin-bottom:18px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
      <div style="font-size:20px;">🎯</div>
      <div style="flex:1;">
        <div style="font-size:14px; font-weight:700; color:#92400e;">Para unirte a partidos necesitás completar tu perfil deportivo</div>
        <div style="font-size:12px; color:#b45309; margin-top:2px;">Tu categoría y género determinan a qué partidos podés unirte.</div>
      </div>
      <a href="/profile#sport-profile"
         style="display:inline-block; background:#111; color:#fff; border-radius:10px; padding:8px 18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap;">
        Completar perfil
      </a>
    </div>
  @endif
@endauth

{{-- Filtros de deporte --}}
<div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
  <a href="{{ route('falta-uno.index', array_filter(['gender' => $gender ?? null])) }}"
     class="sport-filter-btn {{ !$sport ? 'active' : '' }}">Todos</a>
  @foreach(['football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley'] as $val => $label)
    <a href="{{ route('falta-uno.index', array_filter(['sport' => $val, 'gender' => $gender ?? null])) }}"
       class="sport-filter-btn {{ $sport === $val ? 'active' : '' }}">{{ $label }}</a>
  @endforeach
</div>

{{-- Filtros de género y categoría --}}
@php
  $genericCategories = ['recreativo' => 'Recreativo', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado', 'competitivo' => 'Competitivo'];
  $padelCategories   = ['primera' => 'Primera', 'segunda' => 'Segunda', 'tercera' => 'Tercera', 'cuarta' => 'Cuarta', 'quinta' => 'Quinta', 'sexta' => 'Sexta', 'septima' => 'Séptima', 'octava' => 'Octava'];
  $visibleCategories = match($sport) {
    'padel'                                         => $padelCategories,
    'football','tennis','basketball','volleyball'   => $genericCategories,
    default                                         => array_merge($genericCategories, $padelCategories),
  };
@endphp
<div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px; align-items:center;">
  <span style="font-size:12px; color:#888; font-weight:600;">Filtrar:</span>
  @foreach([''=>'Cualquier género','male'=>'Masculino','female'=>'Femenino'] as $val => $label)
    <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$val ?: null,'category'=>$category ?? null])) }}"
       class="sport-filter-btn {{ ($gender ?? '') === $val ? 'active' : '' }}"
       style="font-size:12px;">{{ $label }}</a>
  @endforeach
  <span style="color:#ddd;">|</span>
  <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$gender ?? null])) }}"
     class="sport-filter-btn {{ ($category ?? '') === '' ? 'active' : '' }}"
     style="font-size:12px;">Cualquier cat.</a>
  @foreach($visibleCategories as $val => $label)
    <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$gender ?? null,'category'=>$val])) }}"
       class="sport-filter-btn {{ ($category ?? '') === $val ? 'active' : '' }}"
       style="font-size:12px;">{{ $label }}</a>
  @endforeach
</div>

@if($games->isEmpty())
  <div class="page-card" style="text-align:center; padding:48px 24px;">
    <div style="font-size:48px; margin-bottom:12px;">⚡</div>
    <p style="font-size:16px; font-weight:700; margin:0 0 6px 0;">No hay partidos disponibles ahora</p>
    <p style="color:#888; font-size:14px; margin:0;">¿Por qué no iniciás uno vos? Elegí una cancha con Falta Uno habilitado.</p>
    <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-top:20px;">
      <a href="{{ route('venues.index', ['falta_uno' => '1']) }}" class="btn btn-primary">
        Crear partido →
      </a>
      <a href="{{ route('venues.index') }}" class="btn">Ver todos los complejos</a>
    </div>
  </div>
@else
  <div style="display:grid; gap:14px;">
    @foreach($games as $game)
    @php
      $joined  = $game->activeParticipants->count();
      $needed  = $game->players_needed;
      $pct     = $needed > 0 ? min(100, round(($joined / $needed) * 100)) : 100;
      $dash    = 283;
      $filled  = round($dash * $pct / 100);
      $empty   = $dash - $filled;
      $isAuthJoined = auth()->check() && $game->activeParticipants->contains('user_id', auth()->id());
      $isInitiator  = auth()->check() && $game->initiator_user_id === auth()->id();
      $sportLabel   = match($game->field->sport ?? '') {
        'football'   => '⚽ Fútbol',
        'padel'      => '🏓 Pádel',
        'tennis'     => '🎾 Tenis',
        'basketball' => '🏀 Básquet',
        'volleyball' => '🏐 Vóley',
        default      => ucfirst($game->field->sport ?? 'Cancha'),
      };
    @endphp

    <div class="game-card" data-game-id="{{ $game->id }}">

      {{-- Círculo animado --}}
      <div style="flex-shrink:0; text-align:center; width:110px;">
        <svg width="90" height="90" viewBox="0 0 100 100" style="transform:rotate(-90deg);">
          <circle cx="50" cy="50" r="45" fill="none" stroke="#f0f0f0" stroke-width="8"/>
          <circle cx="50" cy="50" r="45" fill="none"
                  stroke="{{ $game->status === 'full' ? '#22c55e' : '#111' }}" stroke-width="8"
                  stroke-dasharray="{{ $filled }} {{ $empty }}"
                  stroke-linecap="round"
                  class="game-arc">
          </circle>
        </svg>
        <div style="margin-top:-10px; font-size:13px; font-weight:700;" class="game-status-text">
          @if($game->status === 'full')
            <span style="color:#22c55e;">¡Completo!</span>
          @else
            <span style="color:#111;">Faltan {{ $needed - $joined }}</span>
          @endif
        </div>
        <div style="font-size:11px; color:#aaa; margin-top:2px;" class="game-counter-text">{{ $joined }}/{{ $needed }} anotados</div>
      </div>

      {{-- Info --}}
      <div style="flex:1; min-width:180px;">
        <a href="{{ route('venues.show', $game->field->venue) }}" class="game-venue-link">
          📍 {{ $game->field->venue->name }}
        </a>
        <div style="font-weight:800; font-size:16px; margin:3px 0 6px 0;">
          <a href="{{ route('falta-uno.show', $game) }}" style="text-decoration:none; color:#111;">{{ $game->field->name }}</a>
        </div>

        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
          <span style="font-size:11px; background:#dbeafe; color:#1d4ed8; padding:2px 9px; border-radius:999px; font-weight:700;">{{ $sportLabel }}</span>
          <span style="font-size:11px; background:#f4f4f4; color:#555; padding:2px 9px; border-radius:999px; font-weight:600;">
            📅 {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y') }}
          </span>
          <span style="font-size:11px; background:#f4f4f4; color:#555; padding:2px 9px; border-radius:999px; font-weight:600;">
            🕐 {{ \Carbon\Carbon::parse($game->start_at)->format('H:i') }} hs
          </span>
          @if($game->gender_filter !== 'mixed')
            <span style="font-size:11px; background:#fce7f3; color:#db2777; padding:2px 9px; border-radius:999px; font-weight:700;">
              {{ $game->gender_filter === 'male' ? 'Masculino' : 'Femenino' }}
            </span>
          @endif
          @if($game->category_min || $game->category_max)
            <span style="font-size:11px; background:#f0fdf4; color:#15803d; padding:2px 9px; border-radius:999px; font-weight:700; text-transform:capitalize;">
              @if($game->category_min && $game->category_max && $game->category_min === $game->category_max)
                {{ ucfirst($game->category_min) }}
              @elseif($game->category_min && $game->category_max)
                {{ ucfirst($game->category_min) }} – {{ ucfirst($game->category_max) }}
              @elseif($game->category_min)
                Desde {{ ucfirst($game->category_min) }}
              @else
                Hasta {{ ucfirst($game->category_max) }}
              @endif
            </span>
          @endif
        </div>

        <div style="font-size:13px; color:#666; margin-bottom:8px;">
          👥 {{ $game->total_players }} jugadores · {{ $game->initiator_players }} ya confirmados
        </div>

        @if($game->activeParticipants->isNotEmpty())
        <div style="display:flex; gap:4px; flex-wrap:wrap; margin-bottom:10px;">
          @foreach($game->activeParticipants->take(5) as $p)
            <a href="{{ route('sport-profile.public', $p->user) }}"
               style="font-size:12px; background:#f4f4f4; padding:2px 9px; border-radius:999px; color:#555; text-decoration:none; font-weight:600;">
              {{ $p->user->name }}
            </a>
          @endforeach
          @if($game->activeParticipants->count() > 5)
            <span style="font-size:12px; color:#aaa;">+{{ $game->activeParticipants->count() - 5 }} más</span>
          @endif
        </div>
        @endif

        {{-- Acciones --}}
        @auth
          @if($isInitiator)
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
              <a href="{{ route('falta-uno.chat', $game) }}"
                 style="font-size:12px; padding:5px 12px; border:1.5px solid #e0e0e0; border-radius:8px; color:#333; text-decoration:none; font-weight:600;">
                💬 Chat
              </a>
              <form method="POST" action="{{ route('falta-uno.cancel', $game) }}"
                    onsubmit="return confirm('¿Cancelar el partido? {{ $game->canRefund() ? 'Recibirás un reembolso.' : 'No se devuelve el dinero.' }}')">
                @csrf
                <button type="submit" class="btn btn-ghost" style="font-size:12px; padding:5px 14px; color:#dc2626; border-color:#fecaca;">
                  Cancelar partido
                </button>
              </form>
            </div>
          @elseif($isAuthJoined)
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
              <span style="font-size:13px; color:#22c55e; font-weight:700;">✓ Ya estás anotado</span>
              <a href="{{ route('falta-uno.chat', $game) }}"
                 style="font-size:12px; padding:5px 12px; border:1.5px solid #e0e0e0; border-radius:8px; color:#333; text-decoration:none; font-weight:600;">
                💬 Chat
              </a>
              @if($game->isFinished())
                <a href="{{ route('falta-uno.stats', $game) }}"
                   style="font-size:12px; padding:5px 12px; background:#f4f4f4; border-radius:8px; color:#333; text-decoration:none; font-weight:600;">
                  📊 Mis stats
                </a>
                <a href="{{ route('falta-uno.rate', $game) }}"
                   style="font-size:12px; padding:5px 12px; background:#fef3c7; border-radius:8px; color:#92400e; text-decoration:none; font-weight:600;">
                  ★ Calificar
                </a>
              @endif
            </div>
          @elseif($game->status !== 'full')
            <form method="POST" action="{{ route('falta-uno.join', $game) }}">
              @csrf
              <button type="submit" class="btn btn-primary" style="font-size:13px; padding:7px 18px;">
                Unirme al partido
              </button>
            </form>
          @endif
        @else
          <a href="{{ route('login') }}" class="btn btn-primary" style="font-size:13px; padding:7px 18px;">
            Iniciá sesión para unirte
          </a>
        @endauth
      </div>
    </div>
    @endforeach
  </div>
@endif

@auth
<div id="fuToast" style="display:none; position:fixed; bottom:24px; left:50%; transform:translateX(-50%); z-index:9999; min-width:300px; max-width:420px; padding:14px 20px; border-radius:14px; font-size:14px; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,.15); text-align:center; transition:opacity .3s;"></div>

<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.js"></script>
<script>
  function showFuToast(msg, type) {
    const el = document.getElementById('fuToast');
    el.textContent = msg;
    el.style.background = type === 'up' ? '#111' : '#fff';
    el.style.color       = type === 'up' ? '#fff' : '#111';
    el.style.border      = type === 'up' ? 'none' : '1px solid #e0e0e0';
    el.style.display     = 'block';
    el.style.opacity     = '1';
    setTimeout(() => {
      el.style.opacity = '0';
      setTimeout(() => { el.style.display = 'none'; }, 400);
    }, 5000);
  }

  const echoFu = new Echo({
    broadcaster:       'reverb',
    key:               '{{ config('broadcasting.connections.reverb.key') }}',
    wsHost:            '{{ env('REVERB_CLIENT_HOST', config('broadcasting.connections.reverb.options.host')) }}',
    wsPort:            {{ env('REVERB_CLIENT_PORT', 443) }},
    wssPort:           {{ env('REVERB_CLIENT_PORT', 443) }},
    forceTLS:          true,
    enabledTransports: ['ws'],
    authEndpoint:      '/broadcasting/auth',
  });

  echoFu.private('user.{{ auth()->id() }}')
    .listen('.category.changed', (e) => {
      if (e.direction === 'up') {
        confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
        showFuToast('¡Subiste a ' + e.new_category + '! ¡Seguí así!', 'up');
      } else {
        showFuToast('Seguí intentándolo, bajaste a ' + e.new_category + '. ¡El próximo partido será mejor!', 'down');
      }
    });

  // Actualizar contador de participantes en tiempo real
  echoFu.channel('falta-uno-games')
    .listen('.participant.joined', (e) => {
      const card = document.querySelector(`.game-card[data-game-id="${e.game_id}"]`);
      if (!card) return;

      const dash   = 283;
      const pct    = e.needed > 0 ? Math.min(100, Math.round((e.joined / e.needed) * 100)) : 100;
      const filled = Math.round(dash * pct / 100);
      const empty  = dash - filled;
      const isFull = e.status === 'full';

      const arc         = card.querySelector('.game-arc');
      const statusText  = card.querySelector('.game-status-text');
      const counterText = card.querySelector('.game-counter-text');

      if (arc) {
        arc.setAttribute('stroke-dasharray', `${filled} ${empty}`);
        arc.setAttribute('stroke', isFull ? '#22c55e' : '#111');
      }
      if (statusText) {
        statusText.innerHTML = isFull
          ? '<span style="color:#22c55e;">¡Completo!</span>'
          : `<span style="color:#111;">Faltan ${e.needed - e.joined}</span>`;
      }
      if (counterText) {
        counterText.textContent = `${e.joined}/${e.needed} anotados`;
      }
    });
</script>
@endauth

@endsection
