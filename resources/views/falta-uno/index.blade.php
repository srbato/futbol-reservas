@extends('layouts.app')

@section('title', 'Falta Uno — Partidos buscando jugadores')

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

<div class="fu-header">
  <h1>⚡ Falta Uno</h1>
  <p>Partidos armándose. Anotate y presentate en el complejo el día del partido.</p>
</div>

{{-- Filtros de deporte --}}
<div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:20px;">
  <a href="{{ route('falta-uno.index') }}"
     class="sport-filter-btn {{ !$sport ? 'active' : '' }}">Todos</a>
  @foreach(['football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley'] as $val => $label)
    <a href="{{ route('falta-uno.index', ['sport' => $val]) }}"
       class="sport-filter-btn {{ $sport === $val ? 'active' : '' }}">{{ $label }}</a>
  @endforeach
</div>

@if($games->isEmpty())
  <div class="page-card" style="text-align:center; padding:48px 24px;">
    <div style="font-size:48px; margin-bottom:12px;">⚡</div>
    <p style="font-size:16px; font-weight:700; margin:0 0 6px 0;">No hay partidos disponibles ahora</p>
    <p style="color:#888; font-size:14px; margin:0;">Volvé más tarde o buscá una cancha para iniciar uno vos.</p>
    <a href="{{ route('venues.index') }}" class="btn btn-primary" style="margin-top:20px; display:inline-block;">Ver complejos</a>
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

    <div class="game-card">

      {{-- Círculo animado --}}
      <div style="flex-shrink:0; text-align:center; width:110px;">
        <svg width="90" height="90" viewBox="0 0 100 100" style="transform:rotate(-90deg);">
          <circle cx="50" cy="50" r="45" fill="none" stroke="#f0f0f0" stroke-width="8"/>
          <circle cx="50" cy="50" r="45" fill="none"
                  stroke="{{ $game->status === 'full' ? '#22c55e' : '#111' }}" stroke-width="8"
                  stroke-dasharray="{{ $filled }} {{ $empty }}"
                  stroke-linecap="round">
          </circle>
        </svg>
        <div style="margin-top:-10px; font-size:13px; font-weight:700;">
          @if($game->status === 'full')
            <span style="color:#22c55e;">¡Completo!</span>
          @else
            <span style="color:#111;">Faltan {{ $needed - $joined }}</span>
          @endif
        </div>
        <div style="font-size:11px; color:#aaa; margin-top:2px;">{{ $joined }}/{{ $needed }} anotados</div>
      </div>

      {{-- Info --}}
      <div style="flex:1; min-width:180px;">
        <a href="{{ route('venues.show', $game->field->venue) }}" class="game-venue-link">
          📍 {{ $game->field->venue->name }}
        </a>
        <div style="font-weight:800; font-size:16px; margin:3px 0 6px 0;">{{ $game->field->name }}</div>

        <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:8px;">
          <span style="font-size:11px; background:#dbeafe; color:#1d4ed8; padding:2px 9px; border-radius:999px; font-weight:700;">{{ $sportLabel }}</span>
          <span style="font-size:11px; background:#f4f4f4; color:#555; padding:2px 9px; border-radius:999px; font-weight:600;">
            📅 {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y') }}
          </span>
          <span style="font-size:11px; background:#f4f4f4; color:#555; padding:2px 9px; border-radius:999px; font-weight:600;">
            🕐 {{ \Carbon\Carbon::parse($game->start_at)->format('H:i') }} hs
          </span>
        </div>

        <div style="font-size:13px; color:#666; margin-bottom:8px;">
          👥 {{ $game->total_players }} jugadores · {{ $game->initiator_players }} ya confirmados
        </div>

        @if($game->activeParticipants->isNotEmpty())
        <div style="display:flex; gap:4px; flex-wrap:wrap; margin-bottom:10px;">
          @foreach($game->activeParticipants->take(5) as $p)
            <span style="font-size:12px; background:#f4f4f4; padding:2px 9px; border-radius:999px; color:#555;">
              {{ $p->user->name }}
            </span>
          @endforeach
          @if($game->activeParticipants->count() > 5)
            <span style="font-size:12px; color:#aaa;">+{{ $game->activeParticipants->count() - 5 }} más</span>
          @endif
        </div>
        @endif

        {{-- Acciones --}}
        @auth
          @if($isInitiator)
            <form method="POST" action="{{ route('falta-uno.cancel', $game) }}"
                  onsubmit="return confirm('¿Cancelar el partido? {{ $game->canRefund() ? 'Recibirás un reembolso.' : 'No se devuelve el dinero.' }}')">
              @csrf
              <button type="submit" class="btn btn-ghost" style="font-size:12px; padding:5px 14px; color:#dc2626; border-color:#fecaca;">
                Cancelar partido
              </button>
            </form>
          @elseif($isAuthJoined)
            <span style="font-size:13px; color:#22c55e; font-weight:700;">✓ Ya estás anotado</span>
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

@endsection
