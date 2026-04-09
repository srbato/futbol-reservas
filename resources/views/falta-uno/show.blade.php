@extends('layouts.app')

@section('title', 'Partido · ' . ($game->field->name ?? 'Falta Uno'))

@push('head')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
@endpush

@push('styles')
<style>
  /* ── Scroll progress ───────────────────────────── */
  .fus-scroll-progress {
    position: fixed;
    top: 0; left: 0;
    width: 0%;
    height: 3px;
    background: #22c55e;
    z-index: 9999;
    transition: width .1s linear;
  }

  /* ── Back link ─────────────────────────────────── */
  .fus-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #888;
    text-decoration: none;
    font-weight: 600;
    transition: color .15s, transform .15s;
  }
  .fus-back:hover { color: #111; transform: translateX(-2px); }

  /* ── Hero ──────────────────────────────────────── */
  .fus-hero {
    position: relative;
    height: 380px;
    border-radius: 28px;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
  }
  .fus-hero-bg {
    position: absolute; inset: 0;
    background-image: url('/images/jugadores-falta-uno.webp');
    background-size: cover;
    background-position: center;
  }
  .fus-hero-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.58);
  }
  .fus-hero-gradient {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.8) 0%, transparent 55%);
  }
  .fus-hero-content {
    position: relative;
    z-index: 2;
    padding: 32px 36px;
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 24px;
    flex-wrap: wrap;
  }

  /* Badge pulsante */
  .fus-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(34,197,94,.18);
    border: 1px solid rgba(34,197,94,.4);
    color: #4ade80;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 999px;
    margin-bottom: 10px;
  }
  .fus-badge-dot {
    width: 7px; height: 7px;
    background: #22c55e;
    border-radius: 50%;
    animation: fus-dot-pulse 1.6s ease-in-out infinite;
  }
  @keyframes fus-dot-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.65); }
  }

  /* Hero izquierda */
  .fus-hero-left { flex: 1; min-width: 0; }
  .fus-hero-h1 {
    margin: 0 0 6px;
    font-size: 42px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.03em;
    line-height: 1.05;
  }
  .fus-hero-venue {
    font-size: 14px;
    color: rgba(255,255,255,.7);
    margin-bottom: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .fus-hero-venue:hover { color: #fff; }

  /* Chips glassmorphism */
  .fus-glass-chips { display: flex; gap: 8px; flex-wrap: wrap; }
  .fus-glass-chip {
    background: rgba(255,255,255,.12);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  /* Hero derecha: glassmorphism card */
  .fus-glass-card {
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    flex-shrink: 0;
    min-width: 140px;
  }
  .fus-glass-card-num {
    font-size: 28px;
    font-weight: 900;
    color: #fff;
    margin: 8px 0 4px;
    line-height: 1;
  }
  .fus-glass-card-sub {
    font-size: 12px;
    color: rgba(255,255,255,.75);
    font-weight: 600;
  }

  /* ── Panel de acciones ─────────────────────────── */
  .fus-actions-card {
    background: #fff;
    border: 1px solid #ececec;
    border-top: 4px solid #22c55e;
    border-radius: 16px;
    padding: 24px;
  }
  .fus-actions-title {
    margin: 0 0 16px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #aaa;
  }
  .fus-actions-row { display: flex; gap: 10px; flex-wrap: wrap; }

  .fus-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    border-radius: 12px;
    padding: 10px 18px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    border: none;
    font-family: inherit;
    transition: transform .15s, box-shadow .15s, background .15s;
  }
  .fus-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.1); }

  .fus-btn-chat   { background: #111; color: #fff; }
  .fus-btn-chat:hover { background: #22c55e; color: #052e16; }
  .fus-btn-stats  { background: #f4f4f4; color: #333; }
  .fus-btn-stats:hover { background: #e5e5e5; }
  .fus-btn-rate   { background: #fef3c7; color: #92400e; }
  .fus-btn-rate:hover  { background: #fde68a; }
  .fus-btn-cancel { background: transparent; border: 1.5px solid #fecaca; color: #dc2626; }
  .fus-btn-cancel:hover { background: #fef2f2; }
  .fus-btn-leave  { background: transparent; border: 1.5px solid #fed7aa; color: #ea580c; }
  .fus-btn-leave:hover  { background: #fff7ed; }
  .fus-badge-rated {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f0fdf4; color: #15803d;
    border-radius: 12px; padding: 10px 18px;
    font-size: 14px; font-weight: 700;
  }

  /* ── CTA unirse / guest ────────────────────────── */
  .fus-join-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 28px;
    text-align: center;
  }
  .fus-join-card.needs-profile {
    background: #fffbeb;
    border-color: #fde68a;
  }
  .fus-btn-join {
    display: block;
    width: 100%;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 14px;
    font-size: 16px;
    font-weight: 800;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, transform .15s;
  }
  .fus-btn-join:hover { background: #22c55e; color: #052e16; transform: translateY(-1px); }
  .fus-join-sub { font-size: 13px; color: #888; margin-top: 10px; }
  .fus-btn-outline {
    display: inline-flex; align-items: center;
    border: 1.5px solid #d1d5db; background: transparent; color: #555;
    border-radius: 12px; padding: 10px 22px;
    font-size: 14px; font-weight: 700; cursor: pointer;
    font-family: inherit; text-decoration: none;
    transition: border-color .15s, color .15s;
  }
  .fus-btn-outline:hover { border-color: #111; color: #111; }

  /* ── Detalles del partido ──────────────────────── */
  .fus-details-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 24px;
  }
  .fus-section-title {
    margin: 0 0 16px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #aaa;
  }
  .fus-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
  }
  .fus-detail-tile {
    background: #f8f8f8;
    border-radius: 12px;
    padding: 16px;
  }
  .fus-detail-tile.gender-male   { background: #eff6ff; }
  .fus-detail-tile.gender-female { background: #fdf2f8; }
  .fus-detail-tile.cat    { background: #f0fdf4; }
  .fus-tile-label {
    font-size: 11px;
    color: #888;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 6px;
  }
  .fus-tile-val {
    font-size: 20px;
    font-weight: 800;
    color: #111;
    line-height: 1.1;
  }
  .fus-detail-tile.gender-male   .fus-tile-val { color: #2563eb; }
  .fus-detail-tile.gender-female .fus-tile-val { color: #db2777; }
  .fus-detail-tile.cat    .fus-tile-val { color: #15803d; }

  /* ── Jugadores ─────────────────────────────────── */
  .fus-players-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 24px;
  }
  .fus-players-title { margin: 0 0 16px; }
  .fus-players-count { color: #22c55e; }
  .fus-player-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid #f4f4f4;
  }
  .fus-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
    transition: transform .18s;
  }
  .fus-avatar:hover { transform: scale(1.08); }
  .fus-avatar-initial {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: #111;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 800;
    flex-shrink: 0;
    transition: transform .18s;
  }
  .fus-avatar-initial:hover { transform: scale(1.08); }
  .fus-avatar-empty {
    width: 44px; height: 44px;
    border-radius: 50%;
    border: 2px dashed #d1d5db;
    flex-shrink: 0;
  }
  .fus-player-name {
    font-size: 14px;
    font-weight: 700;
    color: #111;
    text-decoration: none;
  }
  .fus-player-name:hover { color: #555; }
  .fus-badge-org {
    margin-left: 8px;
    font-size: 11px;
    background: #eff6ff;
    color: #1d4ed8;
    padding: 2px 9px;
    border-radius: 999px;
    font-weight: 700;
  }
  .fus-result-badge {
    font-size: 12px;
    padding: 2px 10px;
    border-radius: 999px;
    font-weight: 700;
    margin-left: auto;
    flex-shrink: 0;
  }
  .fus-result-win  { background: #f0fdf4; color: #16a34a; }
  .fus-result-draw { background: #fffbeb; color: #b45309; }
  .fus-result-loss { background: #fef2f2; color: #dc2626; }
  .fus-slot-text {
    font-size: 14px;
    color: #aaa;
    font-style: italic;
  }

  /* ── Badges de reputacion ─────────────────────── */
  .fus-rep-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    margin-left: 6px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }
  .fus-rep-good   { background: #f0fdf4; color: #15803d; }
  .fus-rep-warn   { background: #fffbeb; color: #b45309; }
  .fus-rep-bad    { background: #fef2f2; color: #dc2626; }
  .fus-late-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
    background: #fff7ed;
    color: #ea580c;
    margin-left: 6px;
  }
  .fus-btn-kick {
    background: transparent;
    border: 1px solid #fecaca;
    color: #dc2626;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
    margin-left: auto;
    flex-shrink: 0;
    transition: background .15s, color .15s;
  }
  .fus-btn-kick:hover { background: #fef2f2; }
  .fus-warning-org {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 10px;
    padding: 6px 12px;
    font-size: 12px;
    color: #92400e;
    font-weight: 600;
    margin-left: 6px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .fus-late-warning {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 13px;
    color: #9a3412;
    font-weight: 600;
    margin-top: 8px;
  }
  .fus-penalty-block {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 16px;
    text-align: center;
    font-size: 14px;
    color: #991b1b;
    font-weight: 600;
  }

  /* ── Resultados ────────────────────────────────── */
  .fus-results-card {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 16px;
    padding: 24px;
  }
  .fus-results-title {
    margin: 0 0 16px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #15803d;
  }
  .fus-results-tiles { display: flex; gap: 16px; flex-wrap: wrap; }
  .fus-result-tile {
    background: #fff;
    border-radius: 12px;
    padding: 16px 24px;
    text-align: center;
    flex: 1;
    min-width: 90px;
  }
  .fus-result-tile-num {
    font-size: 32px;
    font-weight: 900;
    line-height: 1;
    margin-bottom: 4px;
  }
  .fus-result-tile-label {
    font-size: 11px;
    color: #888;
    font-weight: 700;
    text-transform: uppercase;
  }

  /* ── Rating inline ─────────────────────────────── */
  .fus-rate-card {
    background: #fff;
    border: 1px solid #ececec;
    border-top: 4px solid #f59e0b;
    border-radius: 16px;
    padding: 24px;
  }
  .fus-rate-player {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 14px;
    padding: 16px 18px;
    margin-bottom: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,.03);
  }
  .fus-rate-player-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
  }
  .fus-rate-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: #111; display: flex; align-items: center; justify-content: center;
    font-size: 15px; font-weight: 800; color: #fff; overflow: hidden; flex-shrink: 0;
  }
  .fus-rate-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .fus-rate-name { font-size: 15px; font-weight: 800; color: #111; }
  .fus-rate-cat { font-size: 12px; color: #888; font-weight: 600; }

  .fus-assess-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    margin-bottom: 10px;
  }
  .fus-assess-btn {
    padding: 10px 6px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
    text-align: center;
    transition: all .15s;
    font-family: inherit;
  }
  .fus-assess-btn:hover { border-color: #9ca3af; }
  .fus-assess-btn .fus-assess-icon { font-size: 18px; line-height: 1; display: block; margin-bottom: 3px; }
  .fus-assess-btn .fus-assess-label { font-size: 12px; font-weight: 700; color: #555; display: block; }
  .fus-assess-btn .fus-assess-desc { font-size: 10px; color: #aaa; line-height: 1.3; display: block; }

  .fus-assess-btn.sel-below  { border-color: #ef4444; background: #fef2f2; }
  .fus-assess-btn.sel-below .fus-assess-label  { color: #dc2626; }
  .fus-assess-btn.sel-match  { border-color: #6b7280; background: #f3f4f6; }
  .fus-assess-btn.sel-match .fus-assess-label  { color: #374151; }
  .fus-assess-btn.sel-above  { border-color: #22c55e; background: #f0fdf4; }
  .fus-assess-btn.sel-above .fus-assess-label  { color: #15803d; }

  .fus-rate-comment {
    width: 100%; padding: 8px 12px;
    border: 1.5px solid #e0e0e0; border-radius: 10px;
    font-size: 13px; resize: vertical; min-height: 48px;
    outline: none; transition: border-color .15s;
    box-sizing: border-box; font-family: inherit;
  }
  .fus-rate-comment:focus { border-color: #111; }

  .fus-rate-submit {
    width: 100%; padding: 14px;
    background: #111; color: #fff; border: none; border-radius: 12px;
    font-size: 15px; font-weight: 700; cursor: pointer;
    margin-top: 8px; transition: background .15s; font-family: inherit;
  }
  .fus-rate-submit:hover { background: #22c55e; color: #052e16; }

  .fus-rated-done {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 14px;
    padding: 20px;
    text-align: center;
  }

  @media (max-width: 480px) {
    .fus-assess-row { grid-template-columns: 1fr; }
  }

  /* ── Responsive ────────────────────────────────── */
  @media (max-width: 640px) {
    .fus-hero { height: 300px; }
    .fus-hero-h1 { font-size: 28px; }
    .fus-hero-content { padding: 20px; }
    .fus-glass-card { display: none; }
    .fus-details-grid { grid-template-columns: 1fr 1fr; }
  }
</style>
@endpush

@section('content')

@php
  $joined  = $game->activeParticipants->count();
  $needed  = $game->players_needed;
  $pct     = $needed > 0 ? min(100, round(($joined / $needed) * 100)) : 100;
  $dash    = 283;
  $filled  = round($dash * $pct / 100);
  $empty   = $dash - $filled;
  $sportLabel = match($game->field->sport ?? '') {
    'football'   => 'Fútbol',
    'padel'      => 'Pádel',
    'tennis'     => 'Tenis',
    'basketball' => 'Básquet',
    'volleyball' => 'Vóley',
    default      => ucfirst($game->field->sport ?? 'Cancha'),
  };
  $statusLabel = match($game->status) {
    'open'      => 'Abierto',
    'full'      => 'Completo',
    'cancelled' => 'Cancelado',
    'expired'   => 'Expirado',
    'finished'  => 'Finalizado',
    default     => ucfirst($game->status),
  };
@endphp

{{-- Scroll progress bar --}}
<div class="fus-scroll-progress" id="fusScrollProgress"></div>

<div style="max-width:760px; margin:0 auto; display:grid; gap:20px;">

  {{-- Back --}}
  <div>
    <a href="{{ route('falta-uno.index') }}" class="fus-back" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;"></i> Volver a Falta Uno</a>
  </div>

  {{-- Hero --}}
  <div class="fus-hero">
    <div class="fus-hero-bg"></div>
    <div class="fus-hero-overlay"></div>
    <div class="fus-hero-gradient"></div>
    <div class="fus-hero-content">

      {{-- Izquierda --}}
      <div class="fus-hero-left" data-aos="fade-up" data-aos-delay="0">
        <span class="fus-badge">
          <span class="fus-badge-dot"></span>
          Falta Uno
        </span>
        <h1 class="fus-hero-h1">{{ $game->field->name }}</h1>
        <a href="{{ route('venues.show', $game->field->venue) }}" class="fus-hero-venue" style="display:inline-flex;align-items:center;gap:5px;">
          <i data-lucide="map-pin" style="width:14px;height:14px;stroke:currentColor;"></i> {{ $game->field->venue->name }}
        </a>
        <div class="fus-glass-chips">
          <span class="fus-glass-chip" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="calendar" style="width:13px;height:13px;stroke:currentColor;"></i> {{ $game->start_at->format('d/m/Y') }}</span>
          <span class="fus-glass-chip" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="clock" style="width:13px;height:13px;stroke:currentColor;"></i> {{ $game->start_at->format('H:i') }} hs</span>
          <span class="fus-glass-chip">{{ $sportLabel }}</span>
          <span class="fus-glass-chip">{{ $statusLabel }}</span>
        </div>
      </div>

      {{-- Derecha: glassmorphism card --}}
      <div class="fus-glass-card" data-aos="fade-up" data-aos-delay="120">
        <svg width="70" height="70" viewBox="0 0 100 100" style="transform:rotate(-90deg); display:block; margin:0 auto;">
          <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,.2)" stroke-width="8"/>
          <circle cx="50" cy="50" r="45" fill="none"
                  stroke="{{ $game->status === 'full' ? '#22c55e' : '#fff' }}" stroke-width="8"
                  stroke-dasharray="{{ $filled }} {{ $empty }}"
                  stroke-linecap="round"/>
        </svg>
        <div class="fus-glass-card-num">{{ $joined }}/{{ $game->total_players }}</div>
        <div class="fus-glass-card-sub">
          @if($game->status === 'full')
            ¡Completo!
          @else
            Faltan {{ max(0, $needed - $joined) }}
          @endif
        </div>
      </div>

    </div>
  </div>

  {{-- Mensaje de no-show --}}
  @auth
  @if($wasNoShow)
    <div class="fus-penalty-block" data-aos="fade-up" style="background:#fef2f2; border:1px solid #fecaca; border-radius:14px; padding:20px; text-align:center;">
      <div style="margin-bottom:8px;">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      </div>
      <div style="font-size:15px; font-weight:700; color:#991b1b; margin-bottom:4px;">Fuiste marcado como ausente</div>
      <div style="font-size:13px; color:#b91c1c;">El organizador registró que no te presentaste a este partido. Se aplicó una penalización a tu cuenta.</div>
    </div>
  @endif
  @endauth

  {{-- Panel de acciones (solo participantes, no no-shows) --}}
  @auth
  @if($isParticipant && !$wasNoShow)
  <div class="fus-actions-card" data-aos="fade-up">
    <p class="fus-actions-title">Acciones</p>
    <div class="fus-actions-row">

      <a href="{{ route('falta-uno.chat', $game) }}" class="fus-btn fus-btn-chat" style="display:inline-flex;align-items:center;gap:5px;"><i data-lucide="message-circle" style="width:14px;height:14px;stroke:currentColor;"></i> Chat del partido</a>

      @if($game->isFinished())
        <a href="{{ route('falta-uno.stats', $game) }}" class="fus-btn fus-btn-stats" style="display:inline-flex;align-items:center;gap:5px;"><i data-lucide="bar-chart-2" style="width:14px;height:14px;stroke:currentColor;"></i> Mis estadísticas</a>
        @if($yaCalifico)
          <span class="fus-badge-rated" style="display:inline-flex;align-items:center;gap:5px;"><i data-lucide="check-circle" style="width:13px;height:13px;stroke:currentColor;"></i> Ya calificaste</span>
        @else
          <a href="#calificar" class="fus-btn fus-btn-rate" style="display:inline-flex;align-items:center;gap:5px;"><i data-lucide="star" style="width:13px;height:13px;stroke:currentColor;"></i> Calificar compañeros</a>
        @endif
      @endif

      @if($isInitiator && in_array($game->status, ['open', 'full']) && !$game->isFinished())
        <form method="POST" action="{{ route('falta-uno.cancel', $game) }}"
              onsubmit="return confirm('¿Cancelar el partido?{{ $game->canRefund() ? ' Recibirás un reembolso.' : ' No se devuelve el dinero.' }}')">
          @csrf
          <button type="submit" class="fus-btn fus-btn-cancel">Cancelar partido</button>
        </form>
      @endif

      @if($isJoined && in_array($game->status, ['open', 'full']) && !$game->isFinished())
        <form method="POST" action="{{ route('falta-uno.leave', $game) }}"
              onsubmit="return {{ $wouldBeLateLeave ? "confirm('ATENCION: Esta bajada es tardia y recibiras una penalizacion (cooldown). ¿Seguro que queres salirte?')" : "confirm('¿Salirte del partido? Tu lugar quedara disponible para otros jugadores.')" }}">
          @csrf
          <button type="submit" class="fus-btn fus-btn-leave" style="display:inline-flex;align-items:center;gap:5px;">
            <i data-lucide="log-out" style="width:14px;height:14px;stroke:currentColor;"></i>
            Salirme del partido
          </button>
        </form>
        @if($wouldBeLateLeave)
          <div class="fus-late-warning">
            Bajarse ahora es una bajada tardia. Se aplicara una penalizacion a tu cuenta.
          </div>
        @endif
      @endif

    </div>
  </div>
  @endif
  @endauth

  {{-- CTA unirse --}}
  @auth
  @if(!$isParticipant && !$wasNoShow && $game->status === 'open' && !$game->isFinished())
    @if($wasKicked)
      <div class="fus-penalty-block" data-aos="fade-up" style="background:#fef2f2; border:1px solid #fecaca; border-radius:14px; padding:20px; text-align:center;">
        <div style="margin-bottom:8px;"><i data-lucide="user-x" style="width:32px;height:32px;stroke:#dc2626;stroke-width:1.5;"></i></div>
        <div style="font-size:15px; font-weight:700; color:#991b1b; margin-bottom:4px;">Fuiste removido de este partido</div>
        <div style="font-size:13px; color:#b91c1c;">El organizador te removio del partido. No podes volver a unirte.</div>
      </div>
    @elseif(!$joinCheck['allowed'])
      <div class="fus-penalty-block" data-aos="fade-up">
        {{ $joinCheck['reason'] }}
      </div>
    @elseif(auth()->user()->faltaUnoSportProfiles()->doesntExist())
      <div class="fus-join-card needs-profile" data-aos="fade-up">
        <div style="margin-bottom:12px;"><i data-lucide="alert-triangle" style="width:32px;height:32px;stroke:#92400e;stroke-width:1.5;"></i></div>
        <div style="font-size:15px; font-weight:700; color:#92400e; margin-bottom:6px;">Necesitas completar tu perfil deportivo</div>
        <div style="font-size:13px; color:#b45309; margin-bottom:18px;">Tu categoria y genero determinan a que partidos podes unirte.</div>
        <a href="{{ route('profile.edit') }}#sport-profile" class="fus-btn fus-btn-chat" style="display:inline-flex; border-radius:12px; padding:10px 22px;">Completar perfil</a>
      </div>
    @else
      <div class="fus-join-card" data-aos="fade-up">
        @if(!empty($joinCheck['warnings']))
          <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:10px 14px; margin-bottom:14px; font-size:13px; color:#92400e; font-weight:600; text-align:left;">
            @foreach($joinCheck['warnings'] as $w)
              <div>{{ $w }}</div>
            @endforeach
          </div>
        @endif
        <form method="POST" action="{{ route('falta-uno.join', $game) }}">
          @csrf
          <button type="submit" class="fus-btn-join">Unirme a este partido</button>
        </form>
        <p class="fus-join-sub">Confirmas tu lugar al unirte. Presentate en el complejo el dia del partido.</p>
      </div>
    @endif
  @endif
  @endauth
  @guest
    <div class="fus-join-card" data-aos="fade-up" style="text-align:center;">
      <p style="margin:0 0 16px; color:#666; font-size:15px;">Iniciá sesión para unirte al partido</p>
      <a href="{{ route('login') }}" class="fus-btn-outline">Iniciar sesión</a>
    </div>
  @endguest

  {{-- Detalles del partido --}}
  <div class="fus-details-card" data-aos="fade-up">
    <p class="fus-section-title">Detalles del partido</p>
    <div class="fus-details-grid">
      <div class="fus-detail-tile">
        <div class="fus-tile-label">Deporte</div>
        <div class="fus-tile-val">{{ $sportLabel }}</div>
      </div>
      <div class="fus-detail-tile">
        <div class="fus-tile-label">Total jugadores</div>
        <div class="fus-tile-val">{{ $game->total_players }}</div>
      </div>
      @if($game->gender_filter && $game->gender_filter !== 'mixed')
      <div class="fus-detail-tile gender-{{ $game->gender_filter }}">
        <div class="fus-tile-label" style="color:{{ $game->gender_filter === 'male' ? '#2563eb' : '#db2777' }};">Género</div>
        <div class="fus-tile-val">{{ $game->gender_filter === 'male' ? 'Masculino' : 'Femenino' }}</div>
      </div>
      @endif
      @if($game->category_min || $game->category_max)
      <div class="fus-detail-tile cat">
        <div class="fus-tile-label" style="color:#15803d;">Categoría</div>
        <div class="fus-tile-val" style="text-transform:capitalize;">
          @if($game->category_min && $game->category_max && $game->category_min === $game->category_max)
            {{ ucfirst($game->category_min) }}
          @elseif($game->category_min && $game->category_max)
            {{ ucfirst($game->category_min) }} – {{ ucfirst($game->category_max) }}
          @elseif($game->category_min)
            Desde {{ ucfirst($game->category_min) }}
          @else
            Hasta {{ ucfirst($game->category_max) }}
          @endif
        </div>
      </div>
      @endif
      @if($game->age_group_min || $game->age_group_max)
      @php
        $agLabels = [
          'sub10' => 'Sub 10', 'sub12' => 'Sub 12', 'sub14' => 'Sub 14',
          'sub16' => 'Sub 16', 'sub18' => 'Sub 18', '19a25' => '19 a 25',
          '26a34' => '26 a 34', 'open'  => 'Open',   'mas35' => '+35',
          'mas40' => '+40',     'mas45' => '+45',     'mas50' => '+50',
          'mas55' => '+55',     'mas60' => '+60',
        ];
      @endphp
      <div class="fus-detail-tile" style="border-left:3px solid #8b5cf6;">
        <div class="fus-tile-label" style="color:#7c3aed;">Edad</div>
        <div class="fus-tile-val">
          @if($game->age_group_min && $game->age_group_max && $game->age_group_min === $game->age_group_max)
            {{ $agLabels[$game->age_group_min] ?? $game->age_group_min }}
          @elseif($game->age_group_min && $game->age_group_max)
            {{ $agLabels[$game->age_group_min] ?? $game->age_group_min }} – {{ $agLabels[$game->age_group_max] ?? $game->age_group_max }}
          @elseif($game->age_group_min)
            Desde {{ $agLabels[$game->age_group_min] ?? $game->age_group_min }}
          @else
            Hasta {{ $agLabels[$game->age_group_max] ?? $game->age_group_max }}
          @endif
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- Jugadores --}}
  <div class="fus-players-card" data-aos="fade-up">
    <h2 class="fus-section-title fus-players-title" style="font-size:16px; font-weight:800; text-transform:none; letter-spacing:0; color:#111;">
      Jugadores (<span class="fus-players-count">{{ $joined + $game->initiator_players }}</span>/{{ $game->total_players }})
    </h2>

    {{-- Iniciador --}}
    @auth
    <div class="fus-player-row" data-aos="fade-up" data-aos-delay="0">
      @if($game->initiator->avatar_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($game->initiator->avatar_path) }}"
             class="fus-avatar" alt="{{ $game->initiator->name }}" title="{{ $game->initiator->name }}">
      @else
        <div class="fus-avatar-initial" title="{{ $game->initiator->name }}">
          {{ mb_strtoupper(mb_substr($game->initiator->name, 0, 1)) }}
        </div>
      @endif
      <div style="flex:1; min-width:0;">
        <a href="{{ route('sport-profile.public', $game->initiator) }}" class="fus-player-name">
          {{ $game->initiator->name }}
        </a>
        <span class="fus-badge-org">Organizador</span>
      </div>
      <span style="font-size:12px; color:#888; font-weight:600; flex-shrink:0;">
        {{ $game->initiator_players }} lugar{{ $game->initiator_players > 1 ? 'es' : '' }}
      </span>
    </div>
    @else
    <div class="fus-player-row" data-aos="fade-up" data-aos-delay="0">
      <div class="fus-avatar-empty"></div>
      <div style="flex:1; min-width:0;">
        <span class="fus-slot-text">Organizador (iniciá sesión para ver)</span>
        <span class="fus-badge-org">Organizador</span>
      </div>
      <span style="font-size:12px; color:#888; font-weight:600; flex-shrink:0;">
        {{ $game->initiator_players }} lugar{{ $game->initiator_players > 1 ? 'es' : '' }}
      </span>
    </div>
    @endauth

    {{-- Participantes --}}
    @foreach($game->activeParticipants as $pi => $p)
    <div class="fus-player-row" data-aos="fade-up" data-aos-delay="{{ min(($pi + 1) * 60, 360) }}">
      @auth
      @if($p->user->avatar_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($p->user->avatar_path) }}"
             class="fus-avatar" alt="{{ $p->user->name }}" title="{{ $p->user->name }}">
      @else
        <div class="fus-avatar-initial" title="{{ $p->user->name }}">
          {{ mb_strtoupper(mb_substr($p->user->name, 0, 1)) }}
        </div>
      @endif
      <div style="flex:1; min-width:0;">
        <a href="{{ route('sport-profile.public', $p->user) }}" class="fus-player-name">
          {{ $p->user->name }}
        </a>
        @php $rep = $reputationData[$p->user_id] ?? null; @endphp
        @if($rep && $rep['attendance_rate'] < 100)
          @php
            $repClass = $rep['attendance_rate'] >= 90 ? 'fus-rep-good' : ($rep['attendance_rate'] >= 70 ? 'fus-rep-warn' : 'fus-rep-bad');
          @endphp
          <span class="fus-rep-badge {{ $repClass }}">{{ number_format($rep['attendance_rate'], 0) }}%</span>
        @endif
        @if($rep && $rep['has_badge'])
          <span class="fus-late-badge">Se bajo {{ $rep['late_leaves_30_days'] }}x</span>
        @endif
        @if($isInitiator && $rep && $rep['attendance_rate'] < 80)
          <span class="fus-warning-org">Baja asistencia</span>
        @endif
      </div>
      @if($game->isFinished() && $p->stats_submitted_at)
        <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
          @if($p->goals !== null || $p->assists !== null)
            <span style="font-size:11px; color:#888; white-space:nowrap;">
              @if($p->goals !== null){{ $p->goals }}G @endif
              @if($p->assists !== null){{ $p->assists }}A @endif
            </span>
          @endif
          @if($p->result)
            @php $rMap = ['win'=>['Victoria','fus-result-win'],'draw'=>['Empate','fus-result-draw'],'loss'=>['Derrota','fus-result-loss']]; @endphp
            <span class="fus-result-badge {{ $rMap[$p->result][1] ?? '' }}">
              {{ $rMap[$p->result][0] ?? '-' }}
            </span>
          @endif
        </div>
      @endif
      @if($isInitiator && $p->user_id !== auth()->id() && in_array($game->status, ['open', 'full']) && !$game->isFinished())
        <form method="POST" action="{{ route('falta-uno.kick', [$game, $p->user]) }}"
              onsubmit="return confirm('¿Seguro que queres remover a {{ $p->user->name }} del partido?')"
              style="margin-left:auto; flex-shrink:0;">
          @csrf
          <button type="submit" class="fus-btn-kick">Remover</button>
        </form>
      @endif
      @else
      <div class="fus-avatar-empty"></div>
      <span class="fus-slot-text">Jugador confirmado</span>
      @endauth
    </div>
    @endforeach

    {{-- Slots vacíos --}}
    @php $slotsVacios = max(0, $needed - $joined); @endphp
    @for($i = 0; $i < $slotsVacios; $i++)
    <div class="fus-player-row" style="opacity:.45;">
      <div class="fus-avatar-empty"></div>
      <span class="fus-slot-text">Lugar disponible</span>
    </div>
    @endfor

  </div>

  {{-- Resultados --}}
  @if($game->isFinished() && $game->activeParticipants->whereNotNull('result')->isNotEmpty())
  @php
    $wins       = $game->activeParticipants->where('result', 'win')->count();
    $draws      = $game->activeParticipants->where('result', 'draw')->count();
    $losses     = $game->activeParticipants->where('result', 'loss')->count();
    $totalGoals = $game->activeParticipants->sum('goals');
    $totalAssists = $game->activeParticipants->sum('assists');
  @endphp
  <div class="fus-results-card" data-aos="zoom-in">
    <p class="fus-results-title">Resultado del partido</p>
    <div class="fus-results-tiles">
      <div class="fus-result-tile">
        <div class="fus-result-tile-num" style="color:#22c55e;">{{ $wins }}</div>
        <div class="fus-result-tile-label">Victorias</div>
      </div>
      <div class="fus-result-tile">
        <div class="fus-result-tile-num" style="color:#f59e0b;">{{ $draws }}</div>
        <div class="fus-result-tile-label">Empates</div>
      </div>
      <div class="fus-result-tile">
        <div class="fus-result-tile-num" style="color:#dc2626;">{{ $losses }}</div>
        <div class="fus-result-tile-label">Derrotas</div>
      </div>
    </div>
    @if($totalGoals > 0 || $totalAssists > 0)
      <div style="display:flex; justify-content:center; gap:20px; margin-top:12px; padding-top:12px; border-top:1px solid rgba(0,0,0,.06);">
        @if($totalGoals > 0)
          <div style="text-align:center;">
            <div style="font-size:20px; font-weight:800; color:#111;">{{ $totalGoals }}</div>
            <div style="font-size:11px; color:#888; font-weight:600;">Goles</div>
          </div>
        @endif
        @if($totalAssists > 0)
          <div style="text-align:center;">
            <div style="font-size:20px; font-weight:800; color:#111;">{{ $totalAssists }}</div>
            <div style="font-size:11px; color:#888; font-weight:600;">Asistencias</div>
          </div>
        @endif
      </div>
    @endif
  </div>
  @endif

  {{-- ═══ SECCIÓN POST-PARTIDO ═══ --}}
  @auth
  @if($game->isFinished() || ($game->start_at->lte(now()) && in_array($game->status, ['full', 'played'])))

    {{-- Panel de no-shows (solo organizador) --}}
    @if($isInitiator)
    <div class="fus-actions-card" data-aos="fade-up" style="border-top-color:#dc2626;">
      <p class="fus-actions-title" style="color:#dc2626;">Control de asistencia</p>

      @if($noShowParticipants->isNotEmpty())
        <div style="margin-bottom:16px;">
          <p style="font-size:13px; font-weight:700; color:#991b1b; margin:0 0 8px;">Jugadores marcados como ausentes:</p>
          @foreach($noShowParticipants as $ns)
            <div style="display:flex; align-items:center; gap:8px; padding:6px 0; border-bottom:1px solid #fef2f2;">
              @if($ns->user->avatar_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($ns->user->avatar_path) }}" class="fus-avatar" style="width:32px; height:32px;" alt="{{ $ns->user->name }}">
              @else
                <div class="fus-avatar-initial" style="width:32px; height:32px; font-size:13px;">{{ mb_strtoupper(mb_substr($ns->user->name, 0, 1)) }}</div>
              @endif
              <span style="font-size:13px; font-weight:600; color:#111;">{{ $ns->user->name }}</span>
              <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:999px; background:#fef2f2; color:#dc2626; margin-left:auto;">Ausente</span>
            </div>
          @endforeach
        </div>
      @endif

      @php
        $confirmedParticipants = $game->activeParticipants->filter(fn($p) => $p->user_id !== auth()->id());
      @endphp

      @if($confirmedParticipants->isNotEmpty())
        <form method="POST" action="{{ route('falta-uno.no-shows', $game) }}" onsubmit="return confirm('¿Marcar a los seleccionados como ausentes? Se les aplicará una penalización.')">
          @csrf
          <p style="font-size:13px; color:#666; margin:0 0 12px;">Seleccioná a quienes no vinieron al partido:</p>
          @foreach($confirmedParticipants as $p)
            <label style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #f4f4f4; cursor:pointer;">
              <input type="checkbox" name="no_show_user_ids[]" value="{{ $p->user_id }}" style="width:18px; height:18px; accent-color:#dc2626;">
              @if($p->user->avatar_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($p->user->avatar_path) }}" class="fus-avatar" style="width:32px; height:32px;" alt="{{ $p->user->name }}">
              @else
                <div class="fus-avatar-initial" style="width:32px; height:32px; font-size:13px;">{{ mb_strtoupper(mb_substr($p->user->name, 0, 1)) }}</div>
              @endif
              <span style="font-size:14px; font-weight:600; color:#111;">{{ $p->user->name }}</span>
            </label>
          @endforeach
          <button type="submit" class="fus-btn" style="margin-top:14px; background:#dc2626; color:#fff; width:100%; justify-content:center;">
            Marcar como ausentes
          </button>
        </form>
      @elseif($noShowParticipants->isEmpty())
        <p style="font-size:13px; color:#888;">No hay participantes para marcar.</p>
      @else
        <p style="font-size:13px; color:#15803d; font-weight:600;">Todos los ausentes ya fueron registrados.</p>
      @endif
    </div>
    @endif

    {{-- Calificar compañeros (inline, no disponible para no-shows) --}}
    @if($isParticipant && !$wasNoShow)
    <div id="calificar" data-aos="fade-up">
      @if($yaCalifico)
        <div class="fus-rated-done">
          <div style="margin-bottom:8px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div style="font-size:16px; font-weight:800; color:#15803d; margin-bottom:4px;">¡Ya calificaste!</div>
          <div style="font-size:13px; color:#666;">Gracias por tu feedback. Ayuda a mejorar la experiencia para todos.</div>
        </div>
      @else
        <div class="fus-rate-card">
          <p class="fus-actions-title" style="color:#f59e0b;">Calificá a tus compañeros</p>
          <p style="font-size:13px; color:#666; margin:0 0 18px;">¿Cómo jugaron? Tu opinión ayuda a ajustar las categorías.</p>

          <form method="POST" action="{{ route('falta-uno.rate.store', $game) }}" id="fusRateForm">
            @csrf

            @foreach($otherUsers as $i => $player)
            <div class="fus-rate-player">
              <div class="fus-rate-player-header">
                <div class="fus-rate-avatar">
                  @if($player->avatar_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($player->avatar_path) }}" alt="{{ $player->name }}">
                  @else
                    {{ mb_strtoupper(mb_substr($player->name, 0, 1)) }}
                  @endif
                </div>
                <div>
                  <div class="fus-rate-name">{{ $player->name }}</div>
                  @php
                    $playerProfile = $player->sportProfileFor($game->field->sport);
                  @endphp
                  @if($playerProfile)
                    <div class="fus-rate-cat">Categoría: <strong style="color:#111; text-transform:capitalize;">{{ $playerProfile->category }}</strong></div>
                  @endif
                </div>
              </div>

              <input type="hidden" name="ratings[{{ $i }}][user_id]" value="{{ $player->id }}">

              <div class="fus-assess-row" data-rate-index="{{ $i }}">
                <button type="button" class="fus-assess-btn" data-value="below">
                  <span class="fus-assess-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
                  </span>
                  <span class="fus-assess-label">Por debajo</span>
                  <span class="fus-assess-desc">Categoría menor</span>
                </button>
                <button type="button" class="fus-assess-btn" data-value="match">
                  <span class="fus-assess-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                  </span>
                  <span class="fus-assess-label">A la altura</span>
                  <span class="fus-assess-desc">Bien en su categoría</span>
                </button>
                <button type="button" class="fus-assess-btn" data-value="above">
                  <span class="fus-assess-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                  </span>
                  <span class="fus-assess-label">Por encima</span>
                  <span class="fus-assess-desc">Categoría mayor</span>
                </button>
                <input type="hidden" name="ratings[{{ $i }}][assessment]" class="fus-assess-score" value="">
              </div>

              <textarea class="fus-rate-comment" name="ratings[{{ $i }}][comment]" placeholder="Comentario opcional..." maxlength="500"></textarea>
            </div>
            @endforeach

            <button type="submit" class="fus-rate-submit">
              Enviar calificaciones
            </button>
          </form>
        </div>
      @endif
    </div>
    @endif

  @endif
  @endauth

</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 500, easing: 'ease-out' });

  window.addEventListener('scroll', () => {
    const el  = document.getElementById('fusScrollProgress');
    if (!el) return;
    const max = document.body.scrollHeight - window.innerHeight;
    el.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
  });

  // Rating inline
  document.querySelectorAll('.fus-assess-row').forEach(function(row) {
    const btns   = row.querySelectorAll('.fus-assess-btn');
    const hidden = row.querySelector('.fus-assess-score');
    btns.forEach(function(btn) {
      btn.addEventListener('click', function() {
        btns.forEach(b => b.classList.remove('sel-below', 'sel-match', 'sel-above'));
        btn.classList.add('sel-' + btn.dataset.value);
        if (hidden) hidden.value = btn.dataset.value;
      });
    });
  });

  const rateForm = document.getElementById('fusRateForm');
  if (rateForm) {
    rateForm.addEventListener('submit', function(e) {
      const inputs = rateForm.querySelectorAll('.fus-assess-score');
      for (const input of inputs) {
        if (!input.value) {
          e.preventDefault();
          alert('Por favor evaluá a todos los jugadores antes de enviar.');
          return;
        }
      }
    });
  }
</script>
@endpush

@endsection
