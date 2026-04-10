@extends('layouts.app')

@section('title', $tournament->name . ' — Torneo en TuCancha')
@section('meta_description', $tournament->max_teams . ' equipos | Eliminacion directa | ' . $tournament->external_venue_name)

@push('head')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
{{-- Open Graph para compartir por WhatsApp --}}
<meta property="og:title" content="{{ $tournament->name }}">
<meta property="og:description" content="{{ $tournament->max_teams }} equipos | Eliminacion directa | {{ $tournament->external_venue_name }}">
<meta property="og:image" content="{{ $tournament->cover_image_path ? asset('storage/' . $tournament->cover_image_path) : asset('images/og-tournament.png') }}">
<meta property="og:url" content="{{ route('torneos.show', $tournament) }}">
<meta property="og:type" content="website">
@endpush

@push('styles')
<style>
  /* ── Scroll progress ───────────────────────────── */
  .tt-scroll-progress {
    position: fixed;
    top: 0; left: 0;
    width: 0%;
    height: 3px;
    background: var(--color-primary);
    z-index: 9999;
    transition: width .1s linear;
  }

  /* ── Back link ─────────────────────────────────── */
  .tt-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #888;
    text-decoration: none;
    font-weight: 600;
    transition: color .15s, transform .15s;
    margin-bottom: 16px;
  }
  .tt-back:hover { color: #111; transform: translateX(-2px); }
  .tt-back svg { width: 16px; height: 16px; }

  /* ── Sport color variables ─────────────────────── */
  .tt-sport-football   { --sport-color: #22c55e; --sport-color-light: #4ade80; --sport-color-bg: rgba(34,197,94,.08); --sport-gradient: linear-gradient(135deg, #064e3b, #065f46, #22c55e); }
  .tt-sport-padel      { --sport-color: #7c3aed; --sport-color-light: #a78bfa; --sport-color-bg: rgba(124,58,237,.08); --sport-gradient: linear-gradient(135deg, #2e1065, #4c1d95, #7c3aed); }
  .tt-sport-tennis     { --sport-color: #d4b896; --sport-color-light: #e8d5b7; --sport-color-bg: rgba(212,184,150,.12); --sport-gradient: linear-gradient(135deg, #78350f, #92400e, #d4b896); }
  .tt-sport-basketball { --sport-color: #f97316; --sport-color-light: #fb923c; --sport-color-bg: rgba(249,115,22,.08); --sport-gradient: linear-gradient(135deg, #7c2d12, #9a3412, #f97316); }
  .tt-sport-volleyball { --sport-color: #3b82f6; --sport-color-light: #60a5fa; --sport-color-bg: rgba(59,130,246,.08); --sport-gradient: linear-gradient(135deg, #1e3a5f, #1e40af, #3b82f6); }

  /* ── Hero ──────────────────────────────────────── */
  .tt-hero {
    position: relative;
    min-height: 380px;
    border-radius: 28px;
    overflow: hidden;
    display: flex;
    align-items: flex-end;
    margin-bottom: 28px;
  }
  .tt-hero-bg {
    position: absolute; inset: 0;
    background: var(--sport-gradient, linear-gradient(135deg, #111 0%, #1a1a2e 100%));
    background-size: cover;
    background-position: center;
  }
  .tt-hero-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.5);
  }
  .tt-hero-gradient {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.85) 0%, rgba(0,0,0,.3) 40%, transparent 70%);
  }
  .tt-hero-pattern {
    position: absolute; inset: 0;
    background-image:
      radial-gradient(circle at 15% 85%, var(--sport-color-bg, rgba(34,197,94,.1)) 0%, transparent 50%),
      radial-gradient(circle at 85% 20%, rgba(255,255,255,.03) 0%, transparent 40%);
    pointer-events: none;
  }
  .tt-hero-content {
    position: relative;
    z-index: 2;
    padding: 36px;
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 14px;
  }
  .tt-hero-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .tt-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: var(--radius-full);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .04em;
    text-transform: uppercase;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }
  .tt-badge-sport    { background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.25); color: var(--sport-color-light, #4ade80); }
  .tt-badge-open     { background: rgba(34,197,94,.18); border: 1px solid rgba(34,197,94,.4); color: #4ade80; }
  .tt-badge-inprogress { background: rgba(245,179,1,.18); border: 1px solid rgba(245,179,1,.4); color: #fbbf24; }
  .tt-badge-finished { background: rgba(107,114,128,.2); border: 1px solid rgba(107,114,128,.4); color: #d1d5db; }
  .tt-badge-closed   { background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.3); color: #fca5a5; }
  .tt-badge-format   { background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2); color: rgba(255,255,255,.85); }
  .tt-badge-dot {
    width: 6px; height: 6px;
    background: currentColor;
    border-radius: 50%;
    animation: tt-dot-pulse 1.6s ease-in-out infinite;
  }
  @keyframes tt-dot-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.65); }
  }

  .tt-hero-h1 {
    margin: 0;
    font-size: 42px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.03em;
    line-height: 1.05;
  }
  .tt-hero-branding {
    font-size: 12px;
    color: rgba(255,255,255,.5);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .tt-hero-branding svg { width: 14px; height: 14px; opacity: .6; }

  /* ── Glass stats in hero ───────────────────────── */
  .tt-hero-stats {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 4px;
  }
  .tt-glass-stat {
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 12px;
    padding: 12px 16px;
    text-align: center;
    min-width: 90px;
  }
  .tt-glass-stat-num {
    font-size: 22px;
    font-weight: 900;
    color: #fff;
    line-height: 1;
    margin-bottom: 2px;
  }
  .tt-glass-stat-label {
    font-size: 11px;
    color: rgba(255,255,255,.7);
    font-weight: 600;
  }

  /* ── Layout grid ───────────────────────────────── */
  .tt-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 24px;
    align-items: start;
  }
  @media (max-width: 960px) {
    .tt-layout { grid-template-columns: 1fr; }
  }

  /* ── Cards ─────────────────────────────────────── */
  .tt-card {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 16px;
    padding: 24px;
    box-shadow: var(--shadow-sm);
  }
  .tt-card + .tt-card { margin-top: 20px; }
  .tt-card-accent {
    border-top: 4px solid var(--sport-color, var(--color-primary));
  }
  .tt-section-title {
    margin: 0 0 16px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--color-text-muted);
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .tt-section-title svg { width: 16px; height: 16px; stroke: var(--sport-color, var(--color-primary)); }

  /* ── Info tiles ────────────────────────────────── */
  .tt-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 10px;
  }
  .tt-info-tile {
    background: var(--color-bg-page);
    border-radius: 12px;
    padding: 14px;
  }
  .tt-info-tile-label {
    font-size: 11px;
    color: var(--color-text-muted);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    margin-bottom: 4px;
  }
  .tt-info-tile-val {
    font-size: 18px;
    font-weight: 800;
    color: var(--color-text);
    line-height: 1.2;
  }

  /* ── Description ───────────────────────────────── */
  .tt-description {
    font-size: 14px;
    line-height: 1.6;
    color: var(--color-text-secondary);
  }
  .tt-description p { margin: 0 0 12px; }
  .tt-description p:last-child { margin-bottom: 0; }

  /* ── Rules ─────────────────────────────────────── */
  .tt-rules {
    background: var(--sport-color-bg, #f0fdf4);
    border: 1px solid rgba(0,0,0,.05);
    border-radius: 12px;
    padding: 16px;
    font-size: 13px;
    line-height: 1.6;
    color: var(--color-text-secondary);
    white-space: pre-line;
  }

  /* ── Teams grid ────────────────────────────────── */
  .tt-teams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
  }
  .tt-team-card {
    background: var(--color-bg-page);
    border: 1px solid var(--color-border-light);
    border-radius: 14px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: transform .15s, box-shadow .15s, border-color .15s;
  }
  .tt-team-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--sport-color, var(--color-primary));
  }
  .tt-team-card.is-user-team {
    border-color: var(--sport-color, var(--color-primary));
    background: var(--sport-color-bg, rgba(34,197,94,.06));
  }
  .tt-team-avatar {
    width: 44px; height: 44px;
    border-radius: 12px;
    background: var(--color-bg-dark);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 900;
    flex-shrink: 0;
    overflow: hidden;
  }
  .tt-team-avatar img {
    width: 100%; height: 100%;
    object-fit: cover;
  }
  .tt-team-info { flex: 1; min-width: 0; }
  .tt-team-name {
    font-size: 14px;
    font-weight: 800;
    color: var(--color-text);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .tt-team-captain {
    font-size: 12px;
    color: var(--color-text-muted);
    font-weight: 500;
  }
  .tt-team-players-count {
    font-size: 11px;
    font-weight: 700;
    color: var(--sport-color, var(--color-primary));
    background: var(--sport-color-bg, rgba(34,197,94,.1));
    padding: 2px 8px;
    border-radius: var(--radius-full);
    flex-shrink: 0;
  }

  /* Empty team slot */
  .tt-team-slot {
    background: transparent;
    border: 2px dashed var(--color-border);
    border-radius: 14px;
    padding: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: var(--color-text-muted);
    font-size: 13px;
    font-weight: 600;
    min-height: 76px;
  }
  .tt-team-slot svg { width: 18px; height: 18px; opacity: .4; }

  /* ── CTA card ──────────────────────────────────── */
  .tt-cta-card {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-top: 4px solid var(--sport-color, var(--color-primary));
    border-radius: 16px;
    padding: 28px;
    text-align: center;
  }
  .tt-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border-radius: 12px;
    padding: 12px 24px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    cursor: pointer;
    border: none;
    font-family: inherit;
    transition: transform .15s, box-shadow .15s, background .15s;
  }
  .tt-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,0,0,.12); }

  .tt-btn-primary {
    background: var(--color-bg-dark);
    color: var(--color-text-inverse);
    width: 100%;
  }
  .tt-btn-primary:hover { background: var(--sport-color, var(--color-primary)); color: #fff; }

  .tt-btn-outline {
    background: transparent;
    border: 1.5px solid var(--color-border);
    color: var(--color-text-secondary);
    width: 100%;
  }
  .tt-btn-outline:hover { border-color: var(--color-text); color: var(--color-text); }

  .tt-btn-manage {
    background: var(--sport-color-bg, #f0fdf4);
    color: var(--sport-color, var(--color-primary));
    border: 1px solid var(--sport-color, var(--color-primary));
    font-size: 13px;
    padding: 9px 18px;
  }
  .tt-btn-manage:hover {
    background: var(--sport-color, var(--color-primary));
    color: #fff;
  }

  .tt-cta-sub {
    font-size: 13px;
    color: var(--color-text-muted);
    margin-top: 10px;
  }

  /* ── Share row ─────────────────────────────────── */
  .tt-share-row {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-top: 14px;
  }
  .tt-share-btn {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--color-border);
    background: var(--color-bg);
    cursor: pointer;
    transition: all .15s;
    text-decoration: none;
  }
  .tt-share-btn:hover { border-color: var(--sport-color, var(--color-primary)); transform: translateY(-1px); }
  .tt-share-btn svg { width: 18px; height: 18px; }
  .tt-share-whatsapp { color: #25d366; }
  .tt-share-copy { color: var(--color-text-secondary); }

  /* ── Organizer actions ─────────────────────────── */
  .tt-organizer-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 12px;
  }

  /* ══════════════════════════════════════════════════
     BRACKET — Eliminacion directa
     ══════════════════════════════════════════════════ */
  .tt-bracket-wrap {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 8px;
  }
  .tt-bracket-wrap::-webkit-scrollbar { height: 6px; }
  .tt-bracket-wrap::-webkit-scrollbar-track { background: var(--color-bg-page); border-radius: 3px; }
  .tt-bracket-wrap::-webkit-scrollbar-thumb { background: var(--color-border); border-radius: 3px; }

  .tt-bracket {
    display: flex;
    gap: 0;
    min-width: max-content;
    padding: 8px 0;
  }

  /* Round column */
  .tt-round {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-width: 240px;
    position: relative;
  }
  .tt-round-title {
    text-align: center;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--color-text-muted);
    margin-bottom: 16px;
    padding: 0 16px;
  }
  .tt-round-matches {
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    flex: 1;
    gap: 12px;
  }

  /* Match card in bracket */
  .tt-match {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 10px;
    margin: 0 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: border-color .15s, box-shadow .15s;
    position: relative;
  }
  .tt-match:hover {
    border-color: var(--sport-color, var(--color-primary));
    box-shadow: var(--shadow-md);
  }
  .tt-match.is-finished {
    border-color: var(--color-border);
  }

  .tt-match-team {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text);
    transition: background .15s;
  }
  .tt-match-team + .tt-match-team {
    border-top: 1px solid var(--color-border-light);
  }
  .tt-match-team.is-winner {
    background: var(--sport-color-bg, rgba(34,197,94,.06));
    font-weight: 800;
  }
  .tt-match-team.is-loser {
    opacity: .5;
  }
  .tt-match-team-name {
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .tt-match-team-seed {
    font-size: 10px;
    font-weight: 700;
    color: var(--color-text-muted);
    background: var(--color-bg-hover);
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .tt-match-team-score {
    font-size: 14px;
    font-weight: 900;
    color: var(--color-text);
    min-width: 20px;
    text-align: center;
    flex-shrink: 0;
  }
  .tt-match-team.is-winner .tt-match-team-score {
    color: var(--sport-color, var(--color-primary));
  }
  .tt-match-tbd {
    color: var(--color-text-muted);
    font-style: italic;
    font-weight: 500;
  }
  .tt-match-bye {
    color: var(--color-text-muted);
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .05em;
  }
  .tt-match-penalties {
    font-size: 10px;
    color: var(--color-text-muted);
    text-align: center;
    padding: 2px 0;
    background: var(--color-bg-page);
    font-weight: 600;
  }

  /* Connector lines between rounds */
  .tt-connector {
    width: 32px;
    flex-shrink: 0;
    position: relative;
  }

  /* ── Recent results ────────────────────────────── */
  .tt-results-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .tt-result-row {
    background: var(--color-bg-page);
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .tt-result-round {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: var(--color-text-muted);
    min-width: 60px;
    flex-shrink: 0;
  }
  .tt-result-teams {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
  }
  .tt-result-winner { color: var(--sport-color, var(--color-primary)); }
  .tt-result-loser { color: var(--color-text-muted); }
  .tt-result-score {
    font-size: 16px;
    font-weight: 900;
    color: var(--color-text);
    min-width: 50px;
    text-align: center;
    flex-shrink: 0;
  }
  .tt-result-vs {
    font-size: 11px;
    color: var(--color-text-muted);
    font-weight: 600;
  }

  /* ── Venue card ────────────────────────────────── */
  .tt-venue-info {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .tt-venue-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 14px;
    color: var(--color-text-secondary);
  }
  .tt-venue-row svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; stroke: var(--sport-color, var(--color-primary)); }
  .tt-venue-row strong { font-weight: 700; color: var(--color-text); }

  /* ── Mobile ────────────────────────────────────── */
  @media (max-width: 640px) {
    .tt-hero { min-height: 320px; border-radius: 20px; }
    .tt-hero-h1 { font-size: 30px; }
    .tt-hero-content { padding: 24px 20px; }
    .tt-hero-stats { gap: 8px; }
    .tt-glass-stat { padding: 10px 12px; min-width: 70px; }
    .tt-glass-stat-num { font-size: 18px; }
    .tt-info-grid { grid-template-columns: repeat(2, 1fr); }
    .tt-teams-grid { grid-template-columns: 1fr; }
    .tt-round { min-width: 200px; }
    .tt-match { margin: 0 8px; }
  }
</style>
@endpush

@section('content')

@php
  $confirmedTeams = $tournament->confirmedTeams;
  $confirmedCount = $confirmedTeams->count();
  $pct = $tournament->max_teams > 0 ? min(100, round(($confirmedCount / $tournament->max_teams) * 100)) : 0;
  $emptySlots = max(0, $tournament->max_teams - $confirmedCount);

  $sportLabel = match($tournament->sport) {
    'football'   => 'Futbol',
    'padel'      => 'Padel',
    'tennis'     => 'Tenis',
    'basketball' => 'Basquet',
    'volleyball' => 'Voley',
    default      => ucfirst($tournament->sport ?? ''),
  };

  $statusLabel = match($tournament->status) {
    'open_registration'   => 'Inscripcion abierta',
    'registration_closed' => 'Inscripcion cerrada',
    'in_progress'         => 'En curso',
    'finished'            => 'Finalizado',
    'cancelled'           => 'Cancelado',
    default               => 'Borrador',
  };

  $statusClass = match($tournament->status) {
    'open_registration'   => 'tt-badge-open',
    'in_progress'         => 'tt-badge-inprogress',
    'finished'            => 'tt-badge-finished',
    'registration_closed' => 'tt-badge-closed',
    default               => 'tt-badge-finished',
  };

  $formatLabel = match($tournament->format ?? 'single_elimination') {
    'single_elimination' => 'Eliminacion directa',
    'round_robin'        => 'Todos contra todos',
    default              => ucfirst($tournament->format ?? ''),
  };

  $genderLabel = match($tournament->gender_filter) {
    'male'   => 'Masculino',
    'female' => 'Femenino',
    'mixed'  => 'Mixto',
    default  => 'Libre',
  };

  $finishedMatches = $tournament->matches->where('status', 'finished')->sortByDesc('played_at');
@endphp

{{-- Scroll progress --}}
<div class="tt-scroll-progress" id="ttScrollProgress"></div>

{{-- Back --}}
<a href="{{ route('torneos.index') }}" class="tt-back" data-aos="fade-right">
  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
  Volver a torneos
</a>

{{-- Hero --}}
<div class="tt-hero tt-sport-{{ $tournament->sport }}" data-aos="fade-up">
  @if($tournament->cover_image_path)
    <div class="tt-hero-bg" style="background-image: url('{{ asset('storage/' . $tournament->cover_image_path) }}'); background-size: cover; background-position: center;"></div>
  @else
    <div class="tt-hero-bg"></div>
  @endif
  <div class="tt-hero-overlay"></div>
  <div class="tt-hero-gradient"></div>
  <div class="tt-hero-pattern" aria-hidden="true"></div>

  <div class="tt-hero-content">
    {{-- Badges --}}
    <div class="tt-hero-badges" data-aos="fade-up" data-aos-delay="100">
      <span class="tt-badge tt-badge-sport">{{ $sportLabel }}</span>
      <span class="tt-badge {{ $statusClass }}">
        @if($tournament->status === 'open_registration')
          <span class="tt-badge-dot"></span>
        @endif
        {{ $statusLabel }}
      </span>
      <span class="tt-badge tt-badge-format">{{ $formatLabel }}</span>
    </div>

    {{-- Title --}}
    <h1 class="tt-hero-h1" data-aos="fade-up" data-aos-delay="150">{{ $tournament->name }}</h1>

    {{-- Branding --}}
    <div class="tt-hero-branding" data-aos="fade-up" data-aos-delay="200">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
        <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
      </svg>
      Organizado en TuCancha
    </div>

    {{-- Glass stats --}}
    <div class="tt-hero-stats" data-aos="fade-up" data-aos-delay="250">
      <div class="tt-glass-stat">
        <div class="tt-glass-stat-num">{{ $confirmedCount }}/{{ $tournament->max_teams }}</div>
        <div class="tt-glass-stat-label">Equipos</div>
      </div>
      @if($tournament->estimated_start_date)
      <div class="tt-glass-stat">
        <div class="tt-glass-stat-num">{{ $tournament->estimated_start_date->translatedFormat('d M') }}</div>
        <div class="tt-glass-stat-label">Inicio</div>
      </div>
      @endif
      @if($tournament->players_per_team)
      <div class="tt-glass-stat">
        <div class="tt-glass-stat-num">{{ $tournament->players_per_team }}</div>
        <div class="tt-glass-stat-label">x equipo</div>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Main layout --}}
<div class="tt-layout">

  {{-- ═══ Left column ═══ --}}
  <div>

    {{-- Description --}}
    @if($tournament->description)
    <div class="tt-card" data-aos="fade-up">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        Descripcion
      </div>
      <div class="tt-description">{!! nl2br(e($tournament->description)) !!}</div>
    </div>
    @endif

    {{-- Info tiles --}}
    <div class="tt-card" data-aos="fade-up" data-aos-delay="50">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
        Informacion del torneo
      </div>
      <div class="tt-info-grid">
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Deporte</div>
          <div class="tt-info-tile-val">{{ $sportLabel }}</div>
        </div>
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Formato</div>
          <div class="tt-info-tile-val" style="font-size:14px;">{{ $formatLabel }}</div>
        </div>
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Equipos</div>
          <div class="tt-info-tile-val">{{ $confirmedCount }}/{{ $tournament->max_teams }}</div>
        </div>
        @if($tournament->players_per_team)
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Jugadores</div>
          <div class="tt-info-tile-val">{{ $tournament->players_per_team }} x equipo</div>
        </div>
        @endif
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Genero</div>
          <div class="tt-info-tile-val">{{ $genderLabel }}</div>
        </div>
        @if($tournament->category)
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Categoria</div>
          <div class="tt-info-tile-val">{{ ucfirst($tournament->category) }}</div>
        </div>
        @endif
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Inscripcion</div>
          <div class="tt-info-tile-val">
            @if($tournament->inscription_price && $tournament->inscription_price > 0)
              ${{ number_format($tournament->inscription_price, 0, ',', '.') }}
            @else
              <span style="color:var(--sport-color, var(--color-primary));">Gratis</span>
            @endif
          </div>
        </div>
        @if($tournament->estimated_start_date)
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Inicio estimado</div>
          <div class="tt-info-tile-val" style="font-size:14px;">{{ $tournament->estimated_start_date->translatedFormat('d M Y') }}</div>
        </div>
        @endif
        @if($tournament->registration_deadline)
        <div class="tt-info-tile">
          <div class="tt-info-tile-label">Cierre inscripcion</div>
          <div class="tt-info-tile-val" style="font-size:14px;">{{ $tournament->registration_deadline->translatedFormat('d M Y H:i') }}</div>
        </div>
        @endif
      </div>
    </div>

    {{-- Rules --}}
    @if($tournament->rules)
    <div class="tt-card" data-aos="fade-up" data-aos-delay="100">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>
        Reglas
      </div>
      <div class="tt-rules">{{ $tournament->rules }}</div>
    </div>
    @endif

    {{-- Teams --}}
    <div class="tt-card" data-aos="fade-up" data-aos-delay="150">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Equipos inscriptos
        <span style="margin-left:auto; font-size:13px; font-weight:800; color:var(--sport-color, var(--color-primary));">{{ $confirmedCount }}/{{ $tournament->max_teams }}</span>
      </div>
      <div class="tt-teams-grid">
        @foreach($confirmedTeams as $team)
          @php
            $initials = collect(explode(' ', $team->name))->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->join('');
            $isUserTeam = isset($userTeam) && $userTeam && $userTeam->id === $team->id;
          @endphp
          <div class="tt-team-card {{ $isUserTeam ? 'is-user-team' : '' }}" data-aos="fade-up" data-aos-delay="{{ min($loop->index * 60, 300) }}">
            <div class="tt-team-avatar">
              @if($team->logo_path)
                <img src="{{ asset('storage/' . $team->logo_path) }}" alt="{{ $team->name }}" loading="lazy">
              @else
                {{ $initials }}
              @endif
            </div>
            <div class="tt-team-info">
              <div class="tt-team-name">
                {{ $team->name }}
                @if($isUserTeam)
                  <span style="font-size:10px;color:var(--sport-color);font-weight:700;"> (tu equipo)</span>
                @endif
              </div>
              <div class="tt-team-captain">
                @if($team->captain)
                  Cap: {{ $team->captain->name }}
                @endif
              </div>
            </div>
            @if($team->players)
              <span class="tt-team-players-count">{{ $team->players->count() }} jug.</span>
            @endif
          </div>
        @endforeach

        {{-- Empty slots --}}
        @for($i = 0; $i < $emptySlots; $i++)
          <div class="tt-team-slot" data-aos="fade-up" data-aos-delay="{{ min(($confirmedCount + $i) * 60, 500) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
            </svg>
            Lugar disponible
          </div>
        @endfor
      </div>
    </div>

    {{-- ═══ Bracket ═══ --}}
    @if(isset($rounds) && count($rounds) > 0)
    <div class="tt-card" data-aos="fade-up" data-aos-delay="200" style="overflow:visible;">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/>
          <polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/>
          <line x1="4" y1="4" x2="9" y2="9"/>
        </svg>
        Bracket
      </div>

      <div class="tt-bracket-wrap">
        <div class="tt-bracket">
          @for($r = 1; $r <= ($totalRounds ?? count($rounds)); $r++)
            @php
              $roundMatches = $rounds[$r] ?? collect();
              $roundName = match(true) {
                ($totalRounds ?? count($rounds)) === $r     => 'Final',
                ($totalRounds ?? count($rounds)) === $r + 1 => 'Semifinal',
                ($totalRounds ?? count($rounds)) === $r + 2 => 'Cuartos',
                default => 'Ronda ' . $r,
              };
            @endphp
            <div class="tt-round">
              <div class="tt-round-title">{{ $roundName }}</div>
              <div class="tt-round-matches">
                @forelse($roundMatches as $match)
                  <div class="tt-match {{ $match->isFinished() ? 'is-finished' : '' }}">
                    {{-- Home team --}}
                    <div class="tt-match-team {{ $match->winner_team_id && $match->winner_team_id === $match->home_team_id ? 'is-winner' : '' }} {{ $match->isFinished() && $match->winner_team_id && $match->winner_team_id !== $match->home_team_id ? 'is-loser' : '' }}">
                      @if($match->homeTeam)
                        @if($match->homeTeam->seed)
                          <span class="tt-match-team-seed">{{ $match->homeTeam->seed }}</span>
                        @endif
                        <span class="tt-match-team-name">{{ $match->homeTeam->name }}</span>
                      @elseif($match->home_team_id === null && $match->away_team_id !== null)
                        <span class="tt-match-team-name tt-match-bye">BYE</span>
                      @else
                        <span class="tt-match-team-name tt-match-tbd">Por definir</span>
                      @endif
                      @if($match->isFinished() || $match->status === 'in_progress')
                        <span class="tt-match-team-score">{{ $match->home_score ?? '-' }}</span>
                      @endif
                    </div>

                    {{-- Away team --}}
                    <div class="tt-match-team {{ $match->winner_team_id && $match->winner_team_id === $match->away_team_id ? 'is-winner' : '' }} {{ $match->isFinished() && $match->winner_team_id && $match->winner_team_id !== $match->away_team_id ? 'is-loser' : '' }}">
                      @if($match->awayTeam)
                        @if($match->awayTeam->seed)
                          <span class="tt-match-team-seed">{{ $match->awayTeam->seed }}</span>
                        @endif
                        <span class="tt-match-team-name">{{ $match->awayTeam->name }}</span>
                      @elseif($match->away_team_id === null && $match->home_team_id !== null)
                        <span class="tt-match-team-name tt-match-bye">BYE</span>
                      @else
                        <span class="tt-match-team-name tt-match-tbd">Por definir</span>
                      @endif
                      @if($match->isFinished() || $match->status === 'in_progress')
                        <span class="tt-match-team-score">{{ $match->away_score ?? '-' }}</span>
                      @endif
                    </div>

                    {{-- Penalties --}}
                    @if($match->hasPenalties())
                      <div class="tt-match-penalties">
                        Pen: {{ $match->home_penalties }} - {{ $match->away_penalties }}
                      </div>
                    @endif
                  </div>
                @empty
                  <div class="tt-match" style="opacity:.4;">
                    <div class="tt-match-team">
                      <span class="tt-match-team-name tt-match-tbd">Por definir</span>
                    </div>
                    <div class="tt-match-team">
                      <span class="tt-match-team-name tt-match-tbd">Por definir</span>
                    </div>
                  </div>
                @endforelse
              </div>
            </div>

            {{-- Connector between rounds --}}
            @if($r < ($totalRounds ?? count($rounds)))
              <div class="tt-connector" aria-hidden="true"></div>
            @endif
          @endfor
        </div>
      </div>
    </div>
    @endif

    {{-- Recent results --}}
    @if($finishedMatches->isNotEmpty())
    <div class="tt-card" data-aos="fade-up" data-aos-delay="250">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        Resultados recientes
      </div>
      <div class="tt-results-list">
        @foreach($finishedMatches->take(6) as $match)
          <div class="tt-result-row">
            <span class="tt-result-round">{{ $match->round_name ?? 'Ronda ' . $match->round }}</span>
            <div class="tt-result-teams">
              <span class="{{ $match->winner_team_id === $match->home_team_id ? 'tt-result-winner' : 'tt-result-loser' }}">
                {{ $match->homeTeam->name ?? '?' }}
              </span>
              <span class="tt-result-score">{{ $match->scoreDisplay() }}</span>
              <span class="{{ $match->winner_team_id === $match->away_team_id ? 'tt-result-winner' : 'tt-result-loser' }}">
                {{ $match->awayTeam->name ?? '?' }}
              </span>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

  </div>

  {{-- ═══ Right sidebar ═══ --}}
  <div>

    {{-- CTA --}}
    <div class="tt-cta-card" data-aos="fade-left" data-aos-delay="100">
      @if($canRegister)
        @auth
          @if($tournament->inscription_price && $tournament->inscription_price > 0)
            @php
              $userPayment = \App\Models\TournamentPayment::where('tournament_id', $tournament->id)
                  ->where('user_id', auth()->id())
                  ->where('status', 'approved')
                  ->whereNull('team_id')
                  ->first();
            @endphp
            @if($userPayment)
              <a href="{{ route('torneos.teams.create', $tournament) }}" class="tt-btn tt-btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Inscribir equipo
              </a>
              <p class="tt-cta-sub" style="color:#15803d;">Pago confirmado — completa tu inscripcion</p>
            @else
              <a href="{{ route('torneos.teams.checkout', $tournament) }}" class="tt-btn tt-btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Pagar inscripcion — ${{ number_format($tournament->inscription_price, 0, ',', '.') }}
              </a>
              <p class="tt-cta-sub">Quedan {{ $emptySlots }} {{ $emptySlots === 1 ? 'lugar' : 'lugares' }}</p>
            @endif
          @else
            <a href="{{ route('torneos.teams.create', $tournament) }}" class="tt-btn tt-btn-primary">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
              Inscribir equipo
            </a>
            <p class="tt-cta-sub">Quedan {{ $emptySlots }} {{ $emptySlots === 1 ? 'lugar' : 'lugares' }}</p>
          @endif
        @else
          <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="tt-btn tt-btn-primary">
            Inicia sesion para inscribirte
          </a>
          <p class="tt-cta-sub">Necesitas una cuenta para inscribir a tu equipo.</p>
        @endauth
      @elseif($tournament->status === 'open_registration' && $tournament->isFull())
        <div style="padding:12px; background:var(--color-bg-page); border-radius:10px; font-size:14px; font-weight:700; color:var(--color-text-muted);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display:inline-block;vertical-align:-3px;margin-right:4px;"><path d="M18 6 6 18M6 6l12 12"/></svg>
          Inscripcion completa
        </div>
      @elseif(isset($userTeam) && $userTeam)
        <div style="padding:12px; background:var(--sport-color-bg, #f0fdf4); border-radius:10px; font-size:14px; font-weight:700; color:var(--sport-color, var(--color-primary)); display:flex; align-items:center; gap:8px; justify-content:center;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
          Ya estas inscripto con {{ $userTeam->name }}
        </div>
      @else
        <div style="padding:12px; background:var(--color-bg-page); border-radius:10px; font-size:14px; font-weight:600; color:var(--color-text-muted); text-align:center;">
          {{ $statusLabel }}
        </div>
      @endif

      {{-- Share --}}
      <div class="tt-share-row">
        <a href="https://wa.me/?text={{ urlencode($tournament->name . ' - Torneo en TuCancha ' . route('torneos.show', $tournament)) }}"
           target="_blank" rel="noopener" class="tt-share-btn" title="Compartir por WhatsApp">
          <svg viewBox="0 0 24 24" fill="currentColor" class="tt-share-whatsapp">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
        </a>
        <button onclick="navigator.clipboard.writeText('{{ route('torneos.show', $tournament) }}'); this.querySelector('svg').style.stroke='var(--sport-color, var(--color-primary))'; setTimeout(() => this.querySelector('svg').style.stroke='', 1500);"
                class="tt-share-btn tt-share-copy" title="Copiar link">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
          </svg>
        </button>
      </div>
    </div>

    {{-- Venue info --}}
    @if($tournament->external_venue_name)
    <div class="tt-card tt-card-accent" data-aos="fade-left" data-aos-delay="150" style="margin-top:20px;">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        Complejo
      </div>
      <div class="tt-venue-info">
        <div class="tt-venue-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
          <div><strong>{{ $tournament->external_venue_name }}</strong></div>
        </div>
        @if($tournament->external_venue_address)
        <div class="tt-venue-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          <div>{{ $tournament->external_venue_address }}</div>
        </div>
        @endif
        @if($tournament->external_venue_contact)
        <div class="tt-venue-row">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <div>{{ $tournament->external_venue_contact }}</div>
        </div>
        @endif
      </div>
    </div>
    @endif

    {{-- Organizer info --}}
    <div class="tt-card" data-aos="fade-left" data-aos-delay="200" style="margin-top:20px;">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Organizador
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:40px;height:40px;border-radius:50%;background:var(--color-bg-dark);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:16px;flex-shrink:0;">
          {{ mb_strtoupper(mb_substr($tournament->organizer->name ?? 'O', 0, 1)) }}
        </div>
        <div>
          <div style="font-weight:800;font-size:15px;">{{ $tournament->organizer->name ?? 'Organizador' }}</div>
        </div>
      </div>
    </div>

    {{-- Organizer actions --}}
    @if($isOrganizer)
    <div class="tt-card tt-card-accent" data-aos="fade-left" data-aos-delay="250" style="margin-top:20px;">
      <div class="tt-section-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
        Acciones del organizador
      </div>
      <div class="tt-organizer-actions">
        <a href="{{ route('torneos.manage', $tournament) }}" class="tt-btn tt-btn-manage">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
          Gestionar torneo
        </a>

        @if($tournament->status === 'draft')
          @if($tournament->venue_approval_status === 'pending')
            <span class="tt-btn tt-btn-manage" style="background:rgba(245,179,1,.1);color:#92400e;border-color:#fbbf24;cursor:default;">
              Esperando aprobacion
            </span>
          @elseif($tournament->venue_approval_status === 'rejected')
            <span class="tt-btn tt-btn-manage" style="background:rgba(239,68,68,.1);color:#991b1b;border-color:#f87171;cursor:default;">
              Cancha rechazada
            </span>
          @else
            <form method="POST" action="{{ route('torneos.publish', $tournament) }}" style="display:inline;">
              @csrf
              <button type="submit" class="tt-btn tt-btn-manage" style="background:rgba(34,197,94,.1);color:#16a34a;border-color:#22c55e;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                Publicar
              </button>
            </form>
          @endif
        @endif

        @if($tournament->status === 'open_registration')
          <form method="POST" action="{{ route('torneos.close_registration', $tournament) }}" style="display:inline;">
            @csrf
            <button type="submit" class="tt-btn tt-btn-manage" style="background:rgba(245,179,1,.1);color:#b45309;border-color:#f59e0b;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Cerrar inscripcion
            </button>
          </form>
        @endif
      </div>
    </div>
    @endif

  </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 600, once: true, offset: 60 });

  // Scroll progress bar
  window.addEventListener('scroll', function() {
    const el = document.getElementById('ttScrollProgress');
    if (!el) return;
    const h = document.documentElement;
    const pct = (h.scrollTop / (h.scrollHeight - h.clientHeight)) * 100;
    el.style.width = Math.min(pct, 100) + '%';
  });
</script>
@endpush
