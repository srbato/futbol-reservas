@extends('layouts.app')

@section('title', $team->name . ' — ' . $tournament->name)

@push('styles')
<style>
  /* ── Layout ─────────────────────────────────────── */
  .ts-wrap {
    max-width: 800px;
    margin: 0 auto;
    padding: 32px 16px 64px;
  }

  /* ── Back link ──────────────────────────────────── */
  .ts-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #a0a0a0;
    text-decoration: none;
    margin-bottom: 20px;
    transition: color .15s;
  }
  .ts-back:hover { color: #22c55e; }
  .ts-back svg { width: 16px; height: 16px; }

  /* ── Card ────────────────────────────────────────── */
  .ts-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.2);
    padding: 32px;
    margin-bottom: 20px;
  }

  /* ── Team header ────────────────────────────────── */
  .ts-team-header {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 8px;
  }
  .ts-team-logo {
    width: 80px;
    height: 80px;
    border-radius: 18px;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid rgba(255,255,255,.06);
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0a0a0a;
  }
  .ts-team-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .ts-team-logo-placeholder {
    color: rgba(255,255,255,.2);
  }
  .ts-team-logo-placeholder svg { width: 36px; height: 36px; }
  .ts-team-info { flex: 1; }
  .ts-team-name {
    font-size: 26px;
    font-weight: 800;
    color: #e8e8e8;
    margin: 0 0 4px;
    letter-spacing: -.02em;
  }
  .ts-team-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .ts-team-captain {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
    color: #a0a0a0;
  }
  .ts-team-captain svg { width: 14px; height: 14px; color: #d97706; }
  .ts-team-captain strong { color: #e8e8e8; font-weight: 600; }
  .ts-team-tournament {
    font-size: 13px;
    color: #666;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }
  .ts-team-tournament svg { width: 14px; height: 14px; }

  /* ── Status badge ───────────────────────────────── */
  .ts-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .03em;
    text-transform: uppercase;
  }
  .ts-badge svg { width: 12px; height: 12px; }
  .ts-badge-confirmed {
    background: rgba(34,197,94,.12);
    border: 1px solid rgba(34,197,94,.3);
    color: #22c55e;
  }
  .ts-badge-disqualified {
    background: rgba(239,68,68,.1);
    border: 1px solid rgba(239,68,68,.25);
    color: #f87171;
  }
  .ts-badge-withdrawn {
    background: rgba(107,114,128,.1);
    border: 1px solid rgba(107,114,128,.25);
    color: #a0a0a0;
  }

  /* ── Section title ──────────────────────────────── */
  .ts-section-title {
    font-size: 16px;
    font-weight: 700;
    color: #e8e8e8;
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .ts-section-title svg { width: 18px; height: 18px; color: #22c55e; }

  /* ── Players list ───────────────────────────────── */
  .ts-players-list {
    list-style: none;
    margin: 0;
    padding: 0;
  }
  .ts-player-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    border-radius: 12px;
    transition: background .15s;
  }
  .ts-player-item:hover { background: #0a0a0a; }
  .ts-player-item + .ts-player-item {
    border-top: 1px solid rgba(255,255,255,.06);
  }
  .ts-player-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .ts-player-avatar {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    background: linear-gradient(135deg, rgba(34,197,94,.12), rgba(34,197,94,.2));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 700;
    color: #22c55e;
    flex-shrink: 0;
  }
  .ts-player-name {
    font-size: 15px;
    font-weight: 600;
    color: #e8e8e8;
  }
  .ts-role-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }
  .ts-role-badge svg { width: 12px; height: 12px; }
  .ts-role-capitan {
    background: rgba(217,119,6,.1);
    color: #d97706;
    border: 1px solid rgba(217,119,6,.25);
  }
  .ts-role-arquero {
    background: rgba(59,130,246,.1);
    color: #60a5fa;
    border: 1px solid rgba(59,130,246,.25);
  }
  .ts-role-jugador {
    background: rgba(34,197,94,.1);
    color: #22c55e;
    border: 1px solid rgba(34,197,94,.25);
  }
  .ts-role-suplente {
    background: rgba(107,114,128,.1);
    color: #a0a0a0;
    border: 1px solid rgba(107,114,128,.25);
  }

  /* ── Matches ────────────────────────────────────── */
  .ts-matches-empty {
    text-align: center;
    padding: 28px 16px;
    color: #666;
    font-size: 14px;
  }
  .ts-matches-empty svg { width: 40px; height: 40px; margin-bottom: 8px; color: rgba(255,255,255,.2); }
  .ts-match-item {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    border-radius: 12px;
    transition: background .15s;
    gap: 12px;
  }
  .ts-match-item:hover { background: #0a0a0a; }
  .ts-match-item + .ts-match-item {
    border-top: 1px solid rgba(255,255,255,.06);
  }
  .ts-match-round {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    color: #666;
    letter-spacing: .05em;
    width: 80px;
    flex-shrink: 0;
  }
  .ts-match-teams {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #e8e8e8;
  }
  .ts-match-vs {
    color: #666;
    font-weight: 400;
    font-size: 12px;
  }
  .ts-match-self { color: #e8e8e8; font-weight: 700; }
  .ts-match-score {
    font-size: 15px;
    font-weight: 700;
    color: #e8e8e8;
    min-width: 60px;
    text-align: center;
  }
  .ts-match-result {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    flex-shrink: 0;
  }
  .ts-match-win {
    background: rgba(34,197,94,.12);
    color: #22c55e;
  }
  .ts-match-loss {
    background: rgba(239,68,68,.1);
    color: #f87171;
  }
  .ts-match-pending {
    background: rgba(107,114,128,.08);
    color: #666;
  }

  /* ── Withdraw button ────────────────────────────── */
  .ts-withdraw-wrap {
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,.06);
  }
  .ts-withdraw-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #111;
    border: 1.5px solid rgba(239,68,68,.25);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #f87171;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, border-color .15s;
  }
  .ts-withdraw-btn:hover {
    background: rgba(239,68,68,.1);
    border-color: #ef4444;
  }
  .ts-withdraw-btn svg { width: 16px; height: 16px; }

  /* ── Responsive ─────────────────────────────────── */
  @media (max-width: 640px) {
    .ts-card { padding: 24px 20px; }
    .ts-team-header { flex-direction: column; align-items: flex-start; gap: 14px; }
    .ts-team-name { font-size: 22px; }
    .ts-match-item { flex-wrap: wrap; }
    .ts-match-round { width: 100%; }
  }
</style>
@endpush

@section('content')
<div class="ts-wrap">

  {{-- Back --}}
  <a href="{{ route('torneos.show', $tournament) }}" class="ts-back">
    <i data-lucide="arrow-left"></i>
    Volver al torneo
  </a>

  {{-- Team header card --}}
  <div class="ts-card">
    <div class="ts-team-header">
      <div class="ts-team-logo">
        @if($team->logo_path)
          <img src="{{ asset('storage/' . $team->logo_path) }}" alt="{{ $team->name }}">
        @else
          <div class="ts-team-logo-placeholder">
            <i data-lucide="shield"></i>
          </div>
        @endif
      </div>
      <div class="ts-team-info">
        <h1 class="ts-team-name">{{ $team->name }}</h1>
        <div class="ts-team-meta">
          <span class="ts-team-captain">
            <i data-lucide="crown"></i>
            <strong>{{ $team->captain->name ?? 'Sin capitan' }}</strong>
          </span>
          <span class="ts-team-tournament">
            <i data-lucide="trophy"></i>
            {{ $tournament->name }}
          </span>
          @php
            $badgeClass = match($team->status) {
              'confirmed'    => 'ts-badge-confirmed',
              'disqualified' => 'ts-badge-disqualified',
              'withdrawn'    => 'ts-badge-withdrawn',
              default        => 'ts-badge-confirmed',
            };
            $badgeLabel = match($team->status) {
              'confirmed'    => 'Confirmado',
              'disqualified' => 'Descalificado',
              'withdrawn'    => 'Retirado',
              default        => $team->status,
            };
            $badgeIcon = match($team->status) {
              'confirmed'    => 'check-circle',
              'disqualified' => 'x-circle',
              'withdrawn'    => 'log-out',
              default        => 'circle',
            };
          @endphp
          <span class="ts-badge {{ $badgeClass }}">
            <i data-lucide="{{ $badgeIcon }}"></i>
            {{ $badgeLabel }}
          </span>
        </div>
      </div>
    </div>
  </div>

  {{-- Payment status --}}
  @if($tournament->inscription_price > 0)
    <div class="ts-card">
      @if($team->payment_confirmed)
        <div style="display: flex; align-items: center; gap: 10px;">
          <span class="ts-badge ts-badge-confirmed">
            <i data-lucide="check-circle"></i>
            Inscripcion paga
          </span>
          @if($team->payment_confirmed_at)
            <span style="font-size: 13px; color: #666;">
              {{ $team->payment_confirmed_at->format('d/m/Y') }}
            </span>
          @endif
        </div>
      @else
        @auth
          @if($team->isCaptain(auth()->user()))
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <i data-lucide="alert-circle" style="width: 18px; height: 18px; color: #f59e0b;"></i>
                <span style="font-size: 14px; font-weight: 600; color: #fbbf24;">Pago pendiente</span>
              </div>
              <a href="{{ route('torneos.teams.checkout', [$tournament, $team]) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #22c55e; color: #052e16; border-radius: 12px; font-size: 14px; font-weight: 700; text-decoration: none; transition: background .15s;">
                <i data-lucide="credit-card" style="width: 16px; height: 16px;"></i>
                Pagar inscripcion
              </a>
            </div>
          @else
            <div style="display: flex; align-items: center; gap: 8px;">
              <i data-lucide="clock" style="width: 16px; height: 16px; color: #666;"></i>
              <span style="font-size: 14px; color: #a0a0a0;">Pendiente de pago</span>
            </div>
          @endif
        @else
          <div style="display: flex; align-items: center; gap: 8px;">
            <i data-lucide="clock" style="width: 16px; height: 16px; color: #666;"></i>
            <span style="font-size: 14px; color: #a0a0a0;">Pendiente de pago</span>
          </div>
        @endauth
      @endif
    </div>
  @endif

  {{-- Players card --}}
  <div class="ts-card">
    <h2 class="ts-section-title">
      <i data-lucide="users"></i>
      Jugadores ({{ $team->players->count() }})
    </h2>

    <ul class="ts-players-list">
      @foreach($team->players as $player)
        @php
          $roleClass = match($player->role) {
            'capitan'  => 'ts-role-capitan',
            'arquero'  => 'ts-role-arquero',
            'suplente' => 'ts-role-suplente',
            default    => 'ts-role-jugador',
          };
          $roleLabel = match($player->role) {
            'capitan'  => 'Capitan',
            'arquero'  => 'Arquero',
            'suplente' => 'Suplente',
            default    => 'Jugador',
          };
          $roleIcon = match($player->role) {
            'capitan'  => 'crown',
            'arquero'  => 'shield',
            'suplente' => 'arrow-right-left',
            default    => 'user',
          };
          $initials = collect(explode(' ', $player->name))
            ->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->take(2)
            ->join('');
        @endphp
        <li class="ts-player-item">
          <div class="ts-player-info">
            <div class="ts-player-avatar">{{ $initials }}</div>
            <span class="ts-player-name">{{ $player->name }}</span>
          </div>
          <span class="ts-role-badge {{ $roleClass }}">
            <i data-lucide="{{ $roleIcon }}"></i>
            {{ $roleLabel }}
          </span>
        </li>
      @endforeach
    </ul>
  </div>

  {{-- Matches card --}}
  <div class="ts-card">
    <h2 class="ts-section-title">
      <i data-lucide="swords"></i>
      Historial de partidos
    </h2>

    @php
      $matches = $team->homeMatches->merge($team->awayMatches)->sortBy('round');
    @endphp

    @if($matches->isEmpty())
      <div class="ts-matches-empty">
        <i data-lucide="calendar-x"></i>
        <p>Todavia no hay partidos registrados para este equipo.</p>
      </div>
    @else
      @foreach($matches as $match)
        @php
          $isHome = $match->home_team_id === $team->id;
          $opponent = $isHome ? $match->awayTeam : $match->homeTeam;
          $isFinished = $match->status === 'finished';
          $isWinner = $isFinished && $match->winner_team_id === $team->id;
          $isLoser = $isFinished && $match->winner_team_id !== null && $match->winner_team_id !== $team->id;
        @endphp
        <div class="ts-match-item">
          <div class="ts-match-round">
            {{ $match->round_name ?? ('Ronda ' . $match->round) }}
          </div>
          <div class="ts-match-teams">
            <span class="ts-match-self">{{ $team->name }}</span>
            <span class="ts-match-vs">vs</span>
            <span>{{ $opponent->name ?? 'Por definir' }}</span>
          </div>
          @if($isFinished)
            <div class="ts-match-score">
              {{ $match->scoreDisplay() }}
            </div>
            <div class="ts-match-result {{ $isWinner ? 'ts-match-win' : 'ts-match-loss' }}">
              {{ $isWinner ? 'W' : 'L' }}
            </div>
          @else
            <div class="ts-match-score" style="color: #666;">
              {{ $match->scheduled_at ? $match->scheduled_at->format('d/m H:i') : '—' }}
            </div>
            <div class="ts-match-result ts-match-pending">—</div>
          @endif
        </div>
      @endforeach
    @endif
  </div>

  {{-- Withdraw action --}}
  @auth
    @if($team->isCaptain(auth()->user()) && $tournament->status === 'open_registration' && $team->status === 'confirmed')
      <div class="ts-card" x-data="{ showConfirm: false }">
        <div class="ts-withdraw-wrap" style="border-top: none; margin-top: 0; padding-top: 0;">
          <template x-if="!showConfirm">
            <button type="button" class="ts-withdraw-btn" @click="showConfirm = true">
              <i data-lucide="log-out"></i>
              Retirar equipo del torneo
            </button>
          </template>
          <template x-if="showConfirm">
            <div>
              <p style="font-size: 14px; color: #e8e8e8; margin: 0 0 14px; font-weight: 500;">
                Estas seguro de que queres retirar a <strong>{{ $team->name }}</strong> del torneo?
                Esta accion no se puede deshacer.
              </p>
              <div style="display: flex; gap: 10px;">
                <form action="{{ route('torneos.teams.withdraw', [$tournament, $team]) }}" method="POST">
                  @csrf
                  <button type="submit" class="ts-withdraw-btn" style="background: rgba(239,68,68,.1);">
                    <i data-lucide="alert-triangle"></i>
                    Si, retirar equipo
                  </button>
                </form>
                <button
                  type="button"
                  @click="showConfirm = false"
                  style="padding: 12px 24px; background: rgba(255,255,255,.06); border: none; border-radius: 12px; font-size: 14px; font-weight: 600; color: #e8e8e8; cursor: pointer; font-family: inherit;"
                >
                  Cancelar
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>
    @endif
  @endauth

</div>
@endsection
