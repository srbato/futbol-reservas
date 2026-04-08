@extends('layouts.app')

@section('title', 'Perfil de ' . $user->name)

@push('head')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
@endpush

@push('styles')
<style>
  /* Easings from design-tokens.css */

  /* ── Scroll progress ───────────────────────────── */
  .fpp-scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: linear-gradient(90deg, #22c55e, #6eeaa0);
    z-index: 9999;
    transition: width 80ms linear;
    border-radius: 0 2px 2px 0;
  }

  /* ── Page Container ────────────────────────────── */
  .fpp-page {
    max-width: 760px;
    margin: 0 auto;
    padding: 0 24px 80px;
  }

  /* ── Hero (Double-Bezel) ───────────────────────── */
  .fpp-hero-shell {
    background: rgba(0,0,0,0.03);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(0,0,0,0.04);
    margin-bottom: 32px;
    margin-top: 32px;
  }

  .fpp-hero {
    position: relative;
    min-height: 260px;
    border-radius: calc(2rem - 5px);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .fpp-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/jugadores-dandose-la-mano-post-partido.webp');
    background-size: cover;
    background-position: center;
    opacity: 0.35;
  }

  .fpp-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, rgba(4,18,10,0.88) 0%, rgba(10,38,22,0.82) 40%, rgba(6,24,14,0.90) 100%);
  }

  .fpp-hero-orb {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
  }

  .fpp-hero-orb-1 {
    top: -80px;
    right: -60px;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(34,197,94,0.10) 0%, transparent 60%);
  }

  .fpp-hero-orb-2 {
    bottom: -60px;
    left: -40px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(74,222,128,0.06) 0%, transparent 60%);
  }

  .fpp-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 48px 24px;
  }

  .fpp-hero-avatar {
    width: 88px;
    height: 88px;
    border-radius: 22px;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.15);
    box-shadow: 0 0 0 4px rgba(34,197,94,0.2);
    margin: 0 auto 16px;
    display: block;
  }

  .fpp-hero-avatar-initial {
    width: 88px;
    height: 88px;
    border-radius: 22px;
    background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.04));
    border: 3px solid rgba(255,255,255,0.15);
    box-shadow: 0 0 0 4px rgba(34,197,94,0.2);
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 900;
    color: #fff;
  }

  .fpp-hero-name {
    margin: 0 0 6px;
    font-size: clamp(24px, 4vw, 32px);
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.03em;
  }

  .fpp-hero-sub {
    margin: 0;
    font-size: 13px;
    color: rgba(255,255,255,0.45);
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  /* ── Section Headers ───────────────────────────── */
  .fpp-section-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 18px;
  }

  .fpp-section-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--color-text-muted, #999);
    flex-shrink: 0;
  }

  .fpp-section-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, #ececec, transparent);
  }

  /* ── Sport Cards (Double-Bezel) ────────────────── */
  .fpp-sports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
    margin-bottom: 40px;
  }

  .fpp-sport-shell {
    background: rgba(0,0,0,0.02);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(0,0,0,0.03);
    transition: transform 400ms var(--ease-out-expo), box-shadow 400ms var(--ease-out-expo);
  }

  .fpp-sport-shell:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0,0,0,0.06);
  }

  .fpp-sport-card {
    background: #fff;
    border-radius: calc(1.5rem - 4px);
    padding: 24px 20px;
    position: relative;
    overflow: hidden;
  }

  .fpp-sport-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(to bottom, #22c55e, #16a34a);
    border-radius: 0 2px 2px 0;
  }

  .fpp-sport-icon {
    width: 28px;
    height: 28px;
    color: #22c55e;
    margin-bottom: 12px;
    display: block;
  }

  .fpp-sport-name {
    font-size: 18px;
    font-weight: 900;
    color: #111;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
  }

  .fpp-cat-badge {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: capitalize;
    margin-bottom: 16px;
  }

  .fpp-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 14px;
  }

  .fpp-stat-cell {
    text-align: center;
    border-radius: 10px;
    padding: 8px 4px;
  }

  .fpp-stat-num {
    font-size: 20px;
    font-weight: 900;
    line-height: 1;
    font-variant-numeric: tabular-nums;
  }

  .fpp-stat-label {
    font-size: 9px;
    color: #888;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-top: 3px;
  }

  .fpp-rating {
    display: flex;
    align-items: center;
    gap: 2px;
  }

  .fpp-star { color: #e5e5e5; }
  .fpp-star.filled { color: #f59e0b; }

  .fpp-rating-num {
    font-size: 12px;
    color: #999;
    margin-left: 6px;
    font-weight: 700;
    font-variant-numeric: tabular-nums;
  }

  .fpp-attendance {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    margin-top: 10px;
  }

  /* ── Match History (Double-Bezel) ───────────────── */
  .fpp-matches-shell {
    background: rgba(0,0,0,0.02);
    border-radius: 1.25rem;
    padding: 4px;
    border: 1px solid rgba(0,0,0,0.03);
    margin-bottom: 40px;
  }

  .fpp-matches-wrap {
    background: #fff;
    border-radius: calc(1.25rem - 4px);
    overflow: hidden;
  }

  .fpp-match-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid #f4f4f4;
    transition: background 200ms var(--ease-out-expo);
  }

  .fpp-match-row:last-child { border-bottom: none; }
  .fpp-match-row:hover { background: #fafafa; }

  .fpp-result-pill {
    padding: 4px 14px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    min-width: 72px;
    text-align: center;
    flex-shrink: 0;
  }

  .fpp-result-win  { background: #f0fdf4; color: #15803d; }
  .fpp-result-draw { background: #fffbeb; color: #b45309; }
  .fpp-result-loss { background: #fef2f2; color: #dc2626; }
  .fpp-result-none { background: #f4f4f4; color: #888; }

  .fpp-match-info { flex: 1; min-width: 0; }

  .fpp-match-name {
    font-size: 14px;
    font-weight: 800;
    color: #111;
    letter-spacing: -0.01em;
  }

  .fpp-match-meta {
    font-size: 12px;
    color: #999;
    margin-top: 2px;
    font-weight: 500;
  }

  .fpp-match-goals {
    font-size: 12px;
    color: #5a5a5a;
    font-weight: 700;
    flex-shrink: 0;
  }

  /* ── Chart Card (Double-Bezel) ──────────────────── */
  .fpp-chart-shell {
    background: rgba(0,0,0,0.02);
    border-radius: 1.25rem;
    padding: 4px;
    border: 1px solid rgba(0,0,0,0.03);
    margin-bottom: 40px;
  }

  .fpp-chart-card {
    background: #fff;
    border-radius: calc(1.25rem - 4px);
    padding: 24px;
  }

  /* ── Conventional Reservations (Double-Bezel) ──── */
  .fpp-conv-shell {
    background: rgba(0,0,0,0.02);
    border-radius: 1.25rem;
    padding: 4px;
    border: 1px solid rgba(0,0,0,0.03);
    margin-bottom: 40px;
  }

  .fpp-conv-card {
    background: #fff;
    border-radius: calc(1.25rem - 4px);
    padding: 24px;
  }

  .fpp-conv-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 20px;
  }

  .fpp-conv-tile {
    text-align: center;
    border-radius: 12px;
    padding: 16px 10px;
  }

  .fpp-conv-num {
    font-size: 24px;
    font-weight: 900;
    line-height: 1;
    font-variant-numeric: tabular-nums;
  }

  .fpp-conv-label {
    font-size: 9px;
    color: #888;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-top: 4px;
  }

  .fpp-conv-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 0;
    border-top: 1px solid #f4f4f4;
  }

  .fpp-conv-row:first-of-type { border-top: none; }

  .fpp-conv-name {
    font-size: 13px;
    font-weight: 800;
    color: #111;
  }

  .fpp-conv-score {
    font-weight: 400;
    color: #999;
    font-size: 12px;
    margin-left: 4px;
  }

  .fpp-conv-meta {
    font-size: 11px;
    color: #999;
    margin-top: 2px;
  }

  /* ── Empty State ───────────────────────────────── */
  .fpp-empty-shell {
    background: rgba(0,0,0,0.02);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(0,0,0,0.03);
    margin-bottom: 32px;
  }

  .fpp-empty {
    background: #fff;
    border-radius: calc(1.5rem - 4px);
    text-align: center;
    padding: 56px 24px;
  }

  .fpp-empty-icon {
    width: 40px;
    height: 40px;
    color: #ccc;
    margin: 0 auto 14px;
    display: block;
  }

  .fpp-empty-title {
    font-size: 16px;
    font-weight: 800;
    margin: 0 0 6px;
    color: #111;
  }

  .fpp-empty-text {
    color: #999;
    font-size: 13px;
    margin: 0;
  }

  /* ── Responsive ─────────────────────────────────── */
  @media (max-width: 600px) {
    .fpp-page { padding: 0 16px 60px; }
    .fpp-hero-shell { margin-top: 16px; border-radius: 1.25rem; }
    .fpp-hero { border-radius: calc(1.25rem - 5px); min-height: 220px; }
    .fpp-hero-content { padding: 36px 20px; }
    .fpp-sports-grid { grid-template-columns: 1fr; }
    .fpp-conv-stats { grid-template-columns: repeat(2, 1fr); }
    .fpp-match-row { padding: 12px 16px; }
  }

  /* ── Reduced Motion ─────────────────────────────── */
  @media (prefers-reduced-motion: reduce) {
    .fpp-sport-shell,
    .fpp-match-row {
      transition-duration: 0ms !important;
    }
    .fpp-scroll-progress {
      transition: none;
    }
  }
</style>
@endpush

@section('content')

{{-- Scroll progress bar --}}
<div class="fpp-scroll-progress" id="fppScrollProgress"></div>

@php
  $sportSvgs = [
    'football'   => '<svg class="fpp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20M2 12h20"/></svg>',
    'padel'      => '<svg class="fpp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="14" rx="6"/><line x1="12" y1="16" x2="12" y2="22"/><circle cx="10" cy="8" r="1" fill="currentColor" stroke="none"/><circle cx="14" cy="8" r="1" fill="currentColor" stroke="none"/><circle cx="10" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="14" cy="12" r="1" fill="currentColor" stroke="none"/></svg>',
    'tennis'     => '<svg class="fpp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M18.4 5.6a16.5 16.5 0 0 1-12.8 12.8"/><path d="M5.6 5.6a16.5 16.5 0 0 0 12.8 12.8"/></svg>',
    'basketball' => '<svg class="fpp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2v20"/><path d="M5.2 5.2c2.6 2 4.8 5 4.8 6.8s-2.2 4.8-4.8 6.8"/><path d="M18.8 5.2c-2.6 2-4.8 5-4.8 6.8s2.2 4.8 4.8 6.8"/></svg>',
    'volleyball' => '<svg class="fpp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2c3 3 4.5 6.5 4.5 10S15 19 12 22"/><path d="M12 2c-3 3-4.5 6.5-4.5 10S9 19 12 22"/><path d="M2.6 8.5h18.8"/><path d="M2.6 15.5h18.8"/></svg>',
  ];

  $sportNames = [
    'football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis',
    'basketball' => 'Básquet', 'volleyball' => 'Vóley',
  ];
@endphp

<div class="fpp-page">

  {{-- Hero --}}
  <div class="fpp-hero-shell">
    <div class="fpp-hero">
      <div class="fpp-hero-bg"></div>
      <div class="fpp-hero-overlay"></div>
      <div class="fpp-hero-orb fpp-hero-orb-1"></div>
      <div class="fpp-hero-orb fpp-hero-orb-2"></div>
      <div class="fpp-hero-content" data-aos="fade-up" data-aos-duration="600">
        @if($user->avatar_path)
          <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}"
               class="fpp-hero-avatar" alt="{{ $user->name }}">
        @else
          <div class="fpp-hero-avatar-initial">
            {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
          </div>
        @endif
        <h1 class="fpp-hero-name">{{ $user->name }}</h1>
        <p class="fpp-hero-sub">Perfil deportivo</p>
      </div>
    </div>
  </div>

  {{-- Sport Profiles --}}
  @if($profiles->isEmpty())
    <div class="fpp-empty-shell" data-aos="fade-up">
      <div class="fpp-empty">
        <svg class="fpp-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
        <p class="fpp-empty-title">Sin perfiles deportivos</p>
        <p class="fpp-empty-text">Este jugador todavía no completó su perfil deportivo.</p>
      </div>
    </div>
  @else
    <div class="fpp-section-header">
      <span class="fpp-section-label">Deportes</span>
      <div class="fpp-section-line"></div>
    </div>
    <div class="fpp-sports-grid">
      @foreach($profiles as $pi => $profile)
      @php
        $catColors = match($profile->category) {
          'recreativo', 'octava', 'septima'   => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
          'intermedio', 'sexta', 'quinta'      => ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
          'avanzado', 'cuarta', 'tercera'      => ['bg' => '#fef3c7', 'color' => '#d97706'],
          'competitivo', 'segunda', 'primera'  => ['bg' => '#fce7f3', 'color' => '#db2777'],
          default                              => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
        };
        $stars = round($profile->average_rating);
      @endphp
      <div class="fpp-sport-shell" data-aos="fade-up" data-aos-delay="{{ min($pi * 80, 320) }}" data-aos-duration="600" aria-label="Perfil de {{ $sportNames[$profile->sport] ?? ucfirst($profile->sport) }}">
        <div class="fpp-sport-card">
          {!! $sportSvgs[$profile->sport] ?? '' !!}
          <div class="fpp-sport-name">{{ $sportNames[$profile->sport] ?? ucfirst($profile->sport) }}</div>
          <span class="fpp-cat-badge" style="background:{{ $catColors['bg'] }};color:{{ $catColors['color'] }};">
            {{ $profile->category }}
          </span>

          <div class="fpp-stats-grid">
            <div class="fpp-stat-cell" style="background:#f8f8f8;">
              <div class="fpp-stat-num" style="color:#111;">{{ $profile->games_played }}</div>
              <div class="fpp-stat-label">PJ</div>
            </div>
            <div class="fpp-stat-cell" style="background:#f0fdf4;">
              <div class="fpp-stat-num" style="color:#22c55e;">{{ $profile->wins }}</div>
              <div class="fpp-stat-label">PG</div>
            </div>
            <div class="fpp-stat-cell" style="background:#fffbeb;">
              <div class="fpp-stat-num" style="color:#f59e0b;">{{ $profile->draws }}</div>
              <div class="fpp-stat-label">PE</div>
            </div>
            <div class="fpp-stat-cell" style="background:#fef2f2;">
              <div class="fpp-stat-num" style="color:#ef4444;">{{ $profile->losses }}</div>
              <div class="fpp-stat-label">PP</div>
            </div>
          </div>

          <div class="fpp-rating" role="img" aria-label="Rating: {{ number_format($profile->average_rating, 1) }} de 5 estrellas">
            @for($i = 1; $i <= 5; $i++)
              <svg class="fpp-star {{ $i <= $stars ? 'filled' : '' }}" width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= $stars ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            @endfor
            <span class="fpp-rating-num">{{ number_format($profile->average_rating, 1) }}</span>
          </div>

          @if($profile->attendance_rate < 100)
            @php
              $attColor = $profile->attendance_rate >= 90 ? '#15803d' : ($profile->attendance_rate >= 70 ? '#b45309' : '#dc2626');
              $attBg    = $profile->attendance_rate >= 90 ? '#f0fdf4' : ($profile->attendance_rate >= 70 ? '#fffbeb' : '#fef2f2');
            @endphp
            <div class="fpp-attendance" style="background:{{ $attBg }};color:{{ $attColor }};">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
              Asistencia: {{ number_format($profile->attendance_rate, 0) }}%
            </div>
          @endif
        </div>
      </div>
      @endforeach
    </div>
  @endif

  {{-- Recent Matches --}}
  @if($recentParticipations->isNotEmpty())
    <div class="fpp-section-header">
      <span class="fpp-section-label">Últimos partidos</span>
      <div class="fpp-section-line"></div>
    </div>
    <div class="fpp-matches-shell">
      <div class="fpp-matches-wrap">
        @foreach($recentParticipations as $mi => $p)
        @php
          $resultCfg = match($p->result) {
            'win'  => ['Victoria', 'fpp-result-win'],
            'draw' => ['Empate',   'fpp-result-draw'],
            'loss' => ['Derrota',  'fpp-result-loss'],
            default=> ['—',        'fpp-result-none'],
          };
        @endphp
        <div class="fpp-match-row" data-aos="fade-right" data-aos-delay="{{ min($mi * 50, 300) }}" data-aos-duration="500">
          <span class="fpp-result-pill {{ $resultCfg[1] }}">{{ $resultCfg[0] }}</span>
          <div class="fpp-match-info">
            <div class="fpp-match-name">{{ $p->game->field->name ?? '—' }}</div>
            <div class="fpp-match-meta">
              {{ $p->game->field->venue->name ?? '' }} · {{ \Carbon\Carbon::parse($p->game->start_at)->format('d/m/Y') }}
            </div>
          </div>
          @if($p->goals !== null)
            <div class="fpp-match-goals">{{ $p->goals }} gol{{ $p->goals !== 1 ? 'es' : '' }}</div>
          @endif
        </div>
        @endforeach
      </div>
    </div>

    {{-- Performance Chart --}}
    <div class="fpp-section-header">
      <span class="fpp-section-label">Rendimiento reciente</span>
      <div class="fpp-section-line"></div>
    </div>
    <div class="fpp-chart-shell" data-aos="fade-up" data-aos-duration="600">
      <div class="fpp-chart-card">
        <div id="chart"></div>
      </div>
    </div>
  @endif

  {{-- Conventional Reservations --}}
  @if($conventionalStats['total'] > 0)
    <div class="fpp-section-header">
      <span class="fpp-section-label">Reservas convencionales</span>
      <div class="fpp-section-line"></div>
    </div>
    <div class="fpp-conv-shell" data-aos="fade-up" data-aos-duration="600">
      <div class="fpp-conv-card">
        <div class="fpp-conv-stats">
          <div class="fpp-conv-tile" style="background:#f8f8f8;">
            <div class="fpp-conv-num" style="color:#111;">{{ $conventionalStats['total'] }}</div>
            <div class="fpp-conv-label">Jugados</div>
          </div>
          <div class="fpp-conv-tile" style="background:#f0fdf4;">
            <div class="fpp-conv-num" style="color:#22c55e;">{{ $conventionalStats['wins'] }}</div>
            <div class="fpp-conv-label">Victorias</div>
          </div>
          <div class="fpp-conv-tile" style="background:#fffbeb;">
            <div class="fpp-conv-num" style="color:#f59e0b;">{{ $conventionalStats['draws'] }}</div>
            <div class="fpp-conv-label">Empates</div>
          </div>
          <div class="fpp-conv-tile" style="background:#fef2f2;">
            <div class="fpp-conv-num" style="color:#ef4444;">{{ $conventionalStats['losses'] }}</div>
            <div class="fpp-conv-label">Derrotas</div>
          </div>
        </div>

        @foreach($conventionalHistory as $ci => $r)
        @php
          $rCfg = match($r->outcome) {
            'W'    => ['Victoria', 'fpp-result-win'],
            'D'    => ['Empate',   'fpp-result-draw'],
            'L'    => ['Derrota',  'fpp-result-loss'],
            default=> ['—',        'fpp-result-none'],
          };
        @endphp
        <div class="fpp-conv-row" data-aos="fade-up" data-aos-delay="{{ min($ci * 40, 240) }}" data-aos-duration="500">
          <span class="fpp-result-pill {{ $rCfg[1] }}">{{ $rCfg[0] }}</span>
          <div style="flex:1;min-width:0;">
            <div class="fpp-conv-name">
              {{ $r->field->name ?? '—' }}
              @if($r->score)<span class="fpp-conv-score">{{ $r->score }}</span>@endif
            </div>
            <div class="fpp-conv-meta">{{ $r->venue->name ?? '' }} · {{ $r->date?->format('d/m/Y') }}</div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  @endif

</div>

@if($recentParticipations->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  const results = {!! json_encode($chartData) !!};

  const options = {
    chart: {
      type: 'line',
      height: 180,
      toolbar: { show: false },
      fontFamily: 'inherit',
    },
    series: [{ name: 'Puntos', data: results.map(r => r.result) }],
    xaxis: {
      categories: results.map(r => r.date),
      labels: { style: { fontSize: '11px', colors: '#999' } },
    },
    yaxis: {
      min: 0,
      max: 3,
      tickAmount: 3,
      labels: {
        formatter: v => v === 3 ? 'Victoria' : v === 1 ? 'Empate' : v === 0 ? 'Derrota' : '',
        style: { fontSize: '10px', colors: '#999' },
      },
    },
    stroke: { curve: 'smooth', width: 3 },
    markers: { size: 5, colors: ['#22c55e'], strokeColors: '#fff', strokeWidth: 2 },
    colors: ['#111'],
    grid: { borderColor: '#f0f0f0', strokeDashArray: 4 },
    theme: { mode: 'light' },
    tooltip: {
      y: { formatter: v => v === 3 ? 'Victoria' : v === 1 ? 'Empate' : 'Derrota' },
    },
  };

  new ApexCharts(document.getElementById('chart'), options).render();
</script>
@endif

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 500, easing: 'ease-out' });

  window.addEventListener('scroll', () => {
    const el = document.getElementById('fppScrollProgress');
    if (!el) return;
    const max = document.body.scrollHeight - window.innerHeight;
    el.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
  }, { passive: true });
</script>
@endpush

@endsection
