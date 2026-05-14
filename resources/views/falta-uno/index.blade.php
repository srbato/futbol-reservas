@extends('layouts.app')

@section('title', 'Falta Uno — Encontrá tu partido')
@section('meta_description', 'Encontrá partidos de fútbol, pádel y tenis que buscan jugadores cerca tuyo. Unite a un equipo, jugá hoy mismo. Sin grupos de WhatsApp, sin complicaciones.')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@200;300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
@endpush

@php
  use Carbon\Carbon;

  $sportLabel = fn($s) => match($s) {
    'football'   => 'Fútbol',
    'padel'      => 'Pádel',
    'tennis'     => 'Tenis',
    'basketball' => 'Básquet',
    'volleyball' => 'Vóley',
    default      => ucfirst($s ?? ''),
  };
  $genderLabel = fn($g) => match($g) {
    'male'   => 'Masculino',
    'female' => 'Femenino',
    'mixed'  => 'Mixto',
    default  => 'Mixto',
  };
  $categoryClass = fn($cat) => match(strtolower($cat ?? '')) {
    'recreativo', 'principiante', 'octava', 'septima' => 'lvl-rec',
    'intermedio', 'sexta', 'quinta', 'cuarta'         => 'lvl-int',
    'avanzado', 'tercera', 'segunda'                  => 'lvl-adv',
    'competitivo', 'primera'                          => 'lvl-comp',
    default                                            => 'lvl-int',
  };

  // Avatar gradient determinista por nombre
  $avatarGradient = function($name) {
    $palette = [
      ['#4ade80', '#22a55a'], ['#7abef5', '#2a6aaa'], ['#fda4af', '#be123c'],
      ['#a78bfa', '#5a3da8'], ['#f5c17a', '#a88844'], ['#94e8c4', '#33996c'],
      ['#fcb46e', '#c0712a'], ['#82e0e5', '#319196'], ['#f9a8d4', '#9d174d'],
    ];
    $i = abs(crc32((string) $name)) % count($palette);
    return $palette[$i];
  };

  // Urgent games: empiezan en menos de 4hs y necesitan jugadores
  $urgentGames = $games->filter(fn($g) => $g->status === 'open'
      && $g->players_needed > 0
      && $g->start_at->lte(now()->addHours(4))
  )->take(3);

  // Mostrar urgent strip sólo si no hay filtro time activo y hay games urgentes
  $showUrgent = $urgentGames->isNotEmpty() && !$time;
@endphp

@push('styles')
<style>
  /* ── Falta Uno (FU) — design v2 ───────────────────────────────────── */
  .fu-scope {
    --fu-bg:#050505; --fu-bg-1:#0a0a0a; --fu-bg-2:#111; --fu-bg-3:#161616;
    --fu-bd:rgba(255,255,255,.07); --fu-bd-2:rgba(255,255,255,.14);
    --fu-tx:#f2f2f2; --fu-tx-2:#c8c8c8; --fu-tx-3:#8a8a8a; --fu-tx-4:#555;
    --fu-accent:#4ade80; --fu-accent-ink:#052010; --fu-accent-hover:#6ee7a0;
    --fu-accent-soft:rgba(74,222,128,.08);
    --fu-warn:#f5c17a; --fu-danger:#f87171; --fu-blue:#7abef5; --fu-purple:#a78bfa;
    --fu-mono:'JetBrains Mono', ui-monospace, monospace;
    background: var(--fu-bg); color: var(--fu-tx);
    font-family: 'Sora', system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
    margin: 0; padding: 0;
  }
  .fu-scope a { color: inherit; text-decoration: none; }
  .fu-scope button { font-family: inherit; cursor: pointer; }
  .fu-scope ::selection { background: var(--fu-accent); color: var(--fu-accent-ink); }

  /* ── HERO ── */
  .fu-hero {
    position: relative;
    padding: 24px 40px 0;
    max-width: 1440px;
    margin: 0 auto;
    overflow: hidden;
  }
  .fu-hero-grid {
    display: grid;
    grid-template-columns: 1.05fr 1fr;
    gap: 60px;
    align-items: center;
    min-height: 480px;
  }
  .fu-hero-left { padding: 0 0 40px; }
  .fu-hero-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    padding: 6px 14px 6px 8px;
    background: rgba(74,222,128,.06);
    border: 1px solid rgba(74,222,128,.18);
    border-radius: 999px;
    font-size: 11px; font-weight: 500; color: var(--fu-accent);
    letter-spacing: .04em;
    margin-bottom: 28px;
  }
  .fu-hero-eyebrow-pulse {
    width: 8px; height: 8px; border-radius: 50%;
    background: var(--fu-accent);
    box-shadow: 0 0 0 0 rgba(74,222,128,.5);
    animation: fu-pulse 2s infinite;
  }
  @keyframes fu-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(74,222,128,.5); }
    70%  { box-shadow: 0 0 0 8px rgba(74,222,128,0); }
    100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); }
  }
  .fu-hero-title {
    font-size: clamp(40px, 6.2vw, 76px);
    font-weight: 200;
    letter-spacing: -0.045em;
    line-height: 0.95;
    margin: 0 0 24px;
    color: var(--fu-tx);
  }
  .fu-hero-title b {
    font-weight: 600;
    background: linear-gradient(135deg, #4ade80, #6ee7a0);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
  }
  .fu-hero-title em {
    font-style: italic;
    font-weight: 300;
    color: var(--fu-tx-2);
  }
  .fu-hero-sub {
    font-size: 16px;
    color: var(--fu-tx-3);
    line-height: 1.5;
    max-width: 480px;
    margin: 0 0 32px;
    font-weight: 400;
  }
  .fu-hero-search {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 0;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--fu-bd-2);
    border-radius: 18px;
    padding: 6px;
    max-width: 580px;
    backdrop-filter: blur(12px);
    box-shadow: 0 16px 50px rgba(0,0,0,.4);
  }
  .fu-hero-search-field {
    padding: 12px 16px;
    border-right: 1px solid var(--fu-bd);
    cursor: pointer;
    transition: background .15s;
    border-radius: 12px;
    min-width: 0;
    background: transparent;
    border-top: 0; border-bottom: 0; border-left: 0;
    color: inherit; text-align: left;
    appearance: none;
    -webkit-appearance: none;
  }
  .fu-hero-search-field:hover { background: rgba(255,255,255,.04); }
  .fu-hero-search-field:last-of-type { border-right: 0; }
  .fu-hsf-k { font-size: 10px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--fu-tx-3); margin-bottom: 4px; }
  .fu-hsf-v { font-size: 13px; font-weight: 500; color: var(--fu-tx); display: inline-flex; align-items: center; gap: 6px; }
  .fu-hsf-v svg { color: var(--fu-tx-3); }
  .fu-hero-search-btn {
    background: var(--fu-accent); color: var(--fu-accent-ink);
    padding: 0 22px; border-radius: 14px; border: 0;
    font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 8px;
    transition: background .15s;
    cursor: pointer;
  }
  .fu-hero-search-btn:hover { background: var(--fu-accent-hover); }

  .fu-hero-quick {
    display: flex; align-items: center; gap: 16px;
    margin-top: 22px;
    font-size: 12px; color: var(--fu-tx-3);
  }
  .fu-hero-quick a { color: var(--fu-tx-2); border-bottom: 1px dashed var(--fu-bd-2); padding-bottom: 1px; transition: color .15s, border-color .15s; }
  .fu-hero-quick a:hover { color: var(--fu-accent); border-color: var(--fu-accent); }

  /* ── HERO MAP ── */
  .fu-hero-map {
    position: relative;
    aspect-ratio: 0.78 / 1;
    width: 100%;
    max-width: 540px;
    justify-self: center;
    align-self: center;
  }
  .fu-hero-map svg.fu-ar-map {
    width: 100%; height: auto; display: block;
    /* Fade agresivo del shape de Argentina hacia los bordes */
    -webkit-mask-image: radial-gradient(ellipse 65% 65% at 50% 50%, #000 15%, rgba(0,0,0,.6) 45%, rgba(0,0,0,.2) 75%, transparent 100%);
            mask-image: radial-gradient(ellipse 65% 65% at 50% 50%, #000 15%, rgba(0,0,0,.6) 45%, rgba(0,0,0,.2) 75%, transparent 100%);
  }
  .fu-ar-fill { fill: rgba(74,222,128,.04); stroke: rgba(74,222,128,.35); stroke-width: 0.5; }
  .fu-ar-fill-inner { fill: rgba(74,222,128,.02); stroke: rgba(74,222,128,.18); stroke-width: 0.3; stroke-dasharray: 1.2 1.2; }
  .fu-ar-glow {
    position: absolute; inset: -25%;
    background:
      radial-gradient(ellipse 40% 35% at 38% 30%, rgba(74,222,128,.12), transparent 70%),
      radial-gradient(ellipse 50% 45% at 60% 70%, rgba(74,222,128,.08), transparent 75%);
    pointer-events: none;
    filter: blur(36px);
    -webkit-mask-image: radial-gradient(ellipse 55% 55% at 50% 50%, #000 10%, rgba(0,0,0,.5) 50%, transparent 95%);
            mask-image: radial-gradient(ellipse 55% 55% at 50% 50%, #000 10%, rgba(0,0,0,.5) 50%, transparent 95%);
  }
  .fu-ping { position: absolute; width: 14px; height: 14px; transform: translate(-50%, -50%); pointer-events: none; }
  .fu-ping-dot {
    position: absolute; inset: 0;
    background: var(--fu-accent); border-radius: 50%;
    box-shadow: 0 0 12px rgba(74,222,128,.7);
    animation: fu-ping-dot 3s ease-out infinite;
  }
  .fu-ping-ring {
    position: absolute; inset: 0;
    border: 1.5px solid var(--fu-accent);
    border-radius: 50%;
    animation: fu-ping-ring 3s ease-out infinite;
  }
  @keyframes fu-ping-dot { 0%,100%{transform:scale(.7);opacity:.8;} 50%{transform:scale(1);opacity:1;} }
  @keyframes fu-ping-ring { 0%{transform:scale(.5);opacity:.9;} 100%{transform:scale(3.4);opacity:0;} }
  .fu-ping-label {
    position: absolute; top: 50%; left: 22px; transform: translateY(-50%);
    background: rgba(15,15,15,.92); border: 1px solid var(--fu-bd-2);
    border-radius: 8px; padding: 5px 9px;
    font-size: 10px; font-weight: 500; color: var(--fu-tx);
    white-space: nowrap; backdrop-filter: blur(8px);
    box-shadow: 0 4px 14px rgba(0,0,0,.4);
    opacity: 0; animation: fu-label-fade 8s infinite;
  }
  .fu-ping-label .city { color: var(--fu-tx-3); font-size: 9px; margin-left: 4px; }
  .fu-ping-label .sport { color: var(--fu-accent); font-weight: 600; }
  .fu-ping-label::before {
    content: ''; position: absolute; left: -4px; top: 50%; transform: translateY(-50%) rotate(45deg);
    width: 8px; height: 8px;
    background: rgba(15,15,15,.92);
    border-left: 1px solid var(--fu-bd-2);
    border-bottom: 1px solid var(--fu-bd-2);
  }
  @keyframes fu-label-fade { 0%,100%{opacity:0;} 8%,18%{opacity:1;} }

  .fu-ar-lines line { stroke: rgba(74,222,128,.18); stroke-width: 0.4; stroke-dasharray: 1.5 1.5; }
  .fu-ar-lines line.active { stroke: rgba(74,222,128,.5); animation: fu-line-pulse 2s ease-in-out infinite; }
  @keyframes fu-line-pulse { 0%,100%{stroke-opacity:.3;} 50%{stroke-opacity:.7;} }

  .fu-map-badge {
    position: absolute; top: 18px; left: 18px;
    background: rgba(10,10,10,.85); border: 1px solid var(--fu-bd-2);
    border-radius: 10px; padding: 8px 12px 8px 10px;
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; color: var(--fu-tx-2);
    backdrop-filter: blur(8px); z-index: 5;
  }
  .fu-map-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--fu-accent); box-shadow: 0 0 0 3px rgba(74,222,128,.2); }
  .fu-map-meter {
    position: absolute; bottom: 18px; right: 18px;
    background: rgba(10,10,10,.85); border: 1px solid var(--fu-bd-2);
    border-radius: 10px; padding: 10px 14px;
    backdrop-filter: blur(8px); z-index: 5;
    font-family: var(--fu-mono);
  }
  .fu-map-meter-k { font-size: 9px; letter-spacing: .14em; text-transform: uppercase; color: var(--fu-tx-3); margin-bottom: 4px; }
  .fu-map-meter-v { font-size: 11px; color: var(--fu-tx); display: inline-flex; align-items: center; gap: 6px; }
  .fu-map-meter-v::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--fu-accent); animation: fu-pulse 2s infinite; }
  .fu-map-compass {
    position: absolute; top: 18px; right: 18px;
    width: 38px; height: 38px;
    border: 1px solid var(--fu-bd-2); border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--fu-tx-3); font-size: 9px; letter-spacing: .1em;
    background: rgba(10,10,10,.85); z-index: 5;
  }
  .fu-map-compass::before { content: 'N'; position: absolute; top: 4px; color: var(--fu-accent); font-weight: 600; font-size: 9px; }

  /* ── FILTERS BAR ── */
  .fu-filters-section {
    position: sticky;
    top: 64px; z-index: 40;
    background: rgba(5,5,5,.92);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border-bottom: 1px solid var(--fu-bd);
    border-top: 1px solid var(--fu-bd);
  }
  .fu-filters-inner {
    max-width: 1440px; margin: 0 auto;
    padding: 14px 40px;
  }
  .fu-filters-row {
    display: flex; align-items: center; gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
  }
  .fu-filters-row::-webkit-scrollbar { display: none; }
  .fu-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 12px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--fu-bd);
    border-radius: 10px;
    font-size: 12px; font-weight: 500; color: var(--fu-tx-2);
    white-space: nowrap; flex: none;
    transition: background .15s, border-color .15s, color .15s;
    text-decoration: none;
    cursor: pointer;
  }
  .fu-chip:hover { background: rgba(255,255,255,.08); color: var(--fu-tx); }
  .fu-chip.active { background: rgba(74,222,128,.08); border-color: rgba(74,222,128,.25); color: var(--fu-accent); }
  .fu-chip svg { opacity: .7; }
  .fu-chip-divider { width: 1px; height: 22px; background: var(--fu-bd); flex: none; margin: 0 4px; }
  .fu-chip-clear {
    color: var(--fu-tx-3); font-size: 11px; font-weight: 500;
    padding: 8px 4px; flex: none; margin-left: auto;
    transition: color .15s;
    text-decoration: none;
  }
  .fu-chip-clear:hover { color: var(--fu-tx); }

  /* ── PAGE BODY ── */
  .fu-page {
    max-width: 1440px; margin: 0 auto;
    padding: 32px 40px 80px;
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 40px;
  }
  .fu-sec { margin-bottom: 48px; }
  .fu-sec-head {
    display: flex; align-items: flex-end; justify-content: space-between;
    margin-bottom: 18px; gap: 16px;
  }
  .fu-sec-eyebrow {
    font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase;
    color: var(--fu-tx-3); margin-bottom: 6px;
    display: inline-flex; align-items: center; gap: 8px;
  }
  .fu-sec-eyebrow .fu-live-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--fu-accent); animation: fu-pulse 2s infinite;
  }
  .fu-sec-title {
    font-size: 26px; font-weight: 300;
    letter-spacing: -0.025em; color: var(--fu-tx);
    margin: 0;
  }
  .fu-sec-title b { font-weight: 600; }
  .fu-sec-link {
    font-size: 12px; color: var(--fu-tx-3); font-weight: 500;
    display: inline-flex; align-items: center; gap: 4px;
    transition: color .15s; text-decoration: none;
  }
  .fu-sec-link:hover { color: var(--fu-accent); }

  /* ── URGENT STRIP ── */
  .fu-urgent-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
  }
  .fu-urgent {
    position: relative; padding: 18px;
    background: linear-gradient(135deg, rgba(245,193,122,.04), rgba(74,222,128,.04));
    border: 1px solid rgba(245,193,122,.2);
    border-radius: 14px; overflow: hidden;
    transition: transform .2s, border-color .2s;
    text-decoration: none; color: inherit; display: block;
  }
  .fu-urgent:hover { transform: translateY(-2px); border-color: rgba(245,193,122,.4); }
  .fu-urgent::before {
    content: ''; position: absolute; left: 0; top: 0; bottom: 0;
    width: 3px;
    background: linear-gradient(to bottom, var(--fu-warn), var(--fu-accent));
  }
  .fu-urgent-countdown {
    font-family: var(--fu-mono);
    font-size: 11px; font-weight: 500;
    color: var(--fu-warn); margin-bottom: 8px;
    display: inline-flex; align-items: center; gap: 6px;
  }
  .fu-urgent-countdown::before {
    content: ''; width: 6px; height: 6px;
    background: var(--fu-warn); border-radius: 50%;
    animation: fu-pulse 1.2s infinite;
  }
  .fu-urgent-title { font-size: 15px; font-weight: 500; color: var(--fu-tx); margin-bottom: 4px; letter-spacing: -0.01em; }
  .fu-urgent-meta { font-size: 12px; color: var(--fu-tx-3); display: flex; align-items: center; gap: 8px; }
  .fu-urgent-meta .sep { width: 2px; height: 2px; background: var(--fu-tx-4); border-radius: 50%; }
  .fu-urgent-bottom { margin-top: 14px; display: flex; align-items: center; justify-content: space-between; }
  .fu-urgent-slots { font-size: 11px; color: var(--fu-tx-2); font-weight: 500; }
  .fu-urgent-slots b { color: var(--fu-warn); }
  .fu-urgent-cta { font-size: 11px; font-weight: 600; color: var(--fu-accent); display: inline-flex; align-items: center; gap: 4px; }

  /* ── MATCH CARDS (main feed) ── */
  .fu-feed { display: grid; gap: 14px; }
  .fu-match {
    display: grid;
    grid-template-columns: 80px 1.4fr 1fr auto;
    gap: 22px;
    align-items: stretch;
    padding: 22px 24px;
    background: var(--fu-bg-1);
    border: 1px solid var(--fu-bd);
    border-radius: 16px;
    transition: border-color .2s, transform .15s;
    position: relative;
    text-decoration: none; color: inherit;
  }
  .fu-match:hover { border-color: var(--fu-bd-2); transform: translateY(-1px); }
  .fu-match-overlay-link {
    position: absolute; inset: 0; z-index: 1; border-radius: inherit;
  }
  .fu-match > *:not(.fu-match-overlay-link) { position: relative; z-index: 2; }

  .fu-match-date {
    text-align: center; padding-right: 22px;
    border-right: 1px solid var(--fu-bd);
    display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 2px;
  }
  .fu-match-day { font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--fu-tx-3); }
  .fu-match-num { font-size: 32px; font-weight: 200; color: var(--fu-tx); letter-spacing: -0.03em; line-height: 1; font-variant-numeric: tabular-nums; }
  .fu-match-month { font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--fu-tx-3); margin-top: 2px; }
  .fu-match-time { font-size: 13px; font-weight: 500; color: var(--fu-accent); margin-top: 6px; font-variant-numeric: tabular-nums; }

  .fu-match-main { display: flex; flex-direction: column; gap: 10px; min-width: 0; padding-top: 2px; }
  .fu-match-title-row { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .fu-match-sport-ico {
    width: 26px; height: 26px; border-radius: 7px;
    background: var(--fu-accent-soft); color: var(--fu-accent);
    display: inline-flex; align-items: center; justify-content: center; flex: none;
  }
  .fu-match-title { font-size: 16px; font-weight: 500; color: var(--fu-tx); letter-spacing: -0.01em; line-height: 1.2; }
  .fu-tag {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px; border-radius: 999px;
    font-size: 10px; font-weight: 500;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--fu-bd);
    color: var(--fu-tx-2);
    letter-spacing: .04em;
  }
  .fu-tag.lvl-rec  { background: rgba(148,232,196,.06); color: #94e8c4; border-color: rgba(148,232,196,.16); }
  .fu-tag.lvl-int  { background: rgba(245,193,122,.08); color: var(--fu-warn); border-color: rgba(245,193,122,.18); }
  .fu-tag.lvl-adv  { background: rgba(122,190,245,.08); color: var(--fu-blue); border-color: rgba(122,190,245,.18); }
  .fu-tag.lvl-comp { background: rgba(248,113,113,.08); color: var(--fu-danger); border-color: rgba(248,113,113,.18); }
  .fu-tag.cat      { background: rgba(167,139,250,.06); color: var(--fu-purple); border-color: rgba(167,139,250,.16); }

  .fu-match-venue {
    font-size: 13px; color: var(--fu-tx-2);
    display: inline-flex; align-items: center; gap: 6px;
  }
  .fu-match-venue svg { opacity: .6; flex: none; }
  .fu-match-venue .zone { color: var(--fu-tx-3); }

  .fu-match-organizer {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 12px; color: var(--fu-tx-3);
    margin-top: auto; padding-top: 4px;
  }
  .fu-organizer-ava {
    width: 22px; height: 22px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 700; color: #000;
  }
  .fu-match-organizer b { color: var(--fu-tx-2); font-weight: 500; }

  .fu-match-roster {
    display: flex; flex-direction: column; gap: 10px;
    padding-left: 22px; border-left: 1px solid var(--fu-bd);
    min-width: 0;
  }
  .fu-roster-head {
    font-size: 10px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase;
    color: var(--fu-tx-3); display: flex; align-items: center; justify-content: space-between;
  }
  .fu-roster-count { color: var(--fu-tx); font-weight: 600; font-family: var(--fu-mono); }
  .fu-roster-count.urgent { color: var(--fu-warn); }
  .fu-roster-count.full { color: var(--fu-accent); }
  .fu-roster-avatars { display: flex; align-items: center; flex-wrap: wrap; row-gap: 4px; }
  .fu-roster-ava {
    width: 30px; height: 30px; border-radius: 50%;
    border: 2px solid var(--fu-bg-1);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 10px; font-weight: 700; color: #000;
    margin-left: -8px; flex: none;
  }
  .fu-roster-ava:first-child { margin-left: 0; }
  .fu-roster-ava.empty {
    background: transparent;
    border: 2px dashed var(--fu-bd-2);
    color: var(--fu-tx-3);
    font-size: 14px; font-weight: 300;
  }
  .fu-roster-ava.extra {
    background: rgba(255,255,255,.06);
    color: var(--fu-tx-2);
    font-size: 11px;
  }
  .fu-roster-bar {
    height: 4px;
    background: rgba(255,255,255,.04);
    border-radius: 2px; overflow: hidden;
  }
  .fu-roster-bar > span {
    display: block; height: 100%;
    background: linear-gradient(90deg, var(--fu-accent), var(--fu-warn));
    border-radius: 2px;
  }

  .fu-match-cta {
    display: flex; flex-direction: column; gap: 8px; align-items: stretch; justify-content: center;
    min-width: 140px;
  }
  .fu-match-price { font-size: 11px; color: var(--fu-tx-3); font-weight: 500; text-align: center; }
  .fu-match-price b { color: var(--fu-tx); font-size: 18px; font-weight: 500; letter-spacing: -0.02em; display: block; margin-bottom: 1px; font-variant-numeric: tabular-nums; }
  .fu-btn {
    border: 0; cursor: pointer; font-family: inherit;
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    text-decoration: none;
    transition: background .15s, color .15s;
  }
  .fu-btn-join {
    background: var(--fu-accent); color: var(--fu-accent-ink);
    padding: 11px 16px; border-radius: 11px;
    font-size: 13px; font-weight: 600;
  }
  .fu-btn-join:hover { background: var(--fu-accent-hover); }
  .fu-btn-chat {
    padding: 9px 14px; border-radius: 11px;
    font-size: 12px; font-weight: 500; color: var(--fu-tx-2);
    background: rgba(255,255,255,.03);
    border: 1px solid var(--fu-bd);
  }
  .fu-btn-chat:hover { background: rgba(255,255,255,.08); color: var(--fu-tx); }
  .fu-btn-cancel {
    padding: 9px 14px; border-radius: 11px;
    font-size: 12px; font-weight: 500; color: var(--fu-danger);
    background: rgba(248,113,113,.05);
    border: 1px solid rgba(248,113,113,.18);
  }
  .fu-btn-cancel:hover { background: rgba(248,113,113,.12); }
  .fu-badge-joined {
    padding: 11px 16px; border-radius: 11px;
    font-size: 13px; font-weight: 600;
    color: var(--fu-accent); background: rgba(74,222,128,.08);
    border: 1px solid rgba(74,222,128,.22);
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
  }
  .fu-btn-disabled {
    background: rgba(255,255,255,.04); color: var(--fu-tx-3);
    border: 1px solid var(--fu-bd); cursor: not-allowed;
    padding: 11px 16px; border-radius: 11px; font-size: 13px; font-weight: 600;
    text-align: center;
  }

  .fu-match.full { opacity: .92; }

  /* ── SIDEBAR ── */
  .fu-side {
    position: sticky; top: 132px; height: fit-content;
    display: flex; flex-direction: column; gap: 22px;
  }
  .fu-side-card {
    background: var(--fu-bg-1);
    border: 1px solid var(--fu-bd);
    border-radius: 16px;
    padding: 22px;
  }

  .fu-side-create {
    background: linear-gradient(160deg, rgba(74,222,128,.06), rgba(74,222,128,.02));
    border: 1px solid rgba(74,222,128,.22);
    position: relative; overflow: hidden;
  }
  .fu-side-create::before {
    content: ''; position: absolute; inset: -50%;
    background: radial-gradient(circle at 70% 30%, rgba(74,222,128,.1), transparent 50%);
    pointer-events: none;
  }
  .fu-side-create-eye { font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--fu-accent); margin-bottom: 10px; position: relative; }
  .fu-side-create-title { font-size: 22px; font-weight: 300; letter-spacing: -0.025em; margin: 0 0 8px; color: var(--fu-tx); position: relative; }
  .fu-side-create-title b { font-weight: 600; }
  .fu-side-create-sub { font-size: 13px; color: var(--fu-tx-3); line-height: 1.5; margin: 0 0 16px; position: relative; }
  .fu-side-create-btn {
    width: 100%; padding: 12px; border: 0;
    background: var(--fu-accent); color: var(--fu-accent-ink);
    border-radius: 12px; font-size: 13px; font-weight: 600;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    transition: background .15s; position: relative;
    cursor: pointer; font-family: inherit;
  }
  .fu-side-create-btn:hover { background: var(--fu-accent-hover); }

  .fu-side-steps { display: flex; flex-direction: column; gap: 16px; }
  .fu-step-row { display: grid; grid-template-columns: 28px 1fr; gap: 12px; }
  .fu-step-num {
    font-family: var(--fu-mono);
    font-size: 11px; font-weight: 500; color: var(--fu-accent);
    display: inline-flex; align-items: center; justify-content: center;
    width: 26px; height: 26px;
    background: var(--fu-accent-soft);
    border: 1px solid rgba(74,222,128,.2);
    border-radius: 8px;
  }
  .fu-step-title { font-size: 13px; font-weight: 500; color: var(--fu-tx); margin: 4px 0 4px; letter-spacing: -0.005em; }
  .fu-step-desc { font-size: 12px; color: var(--fu-tx-3); line-height: 1.5; margin: 0; }

  .fu-mymatch {
    display: grid; grid-template-columns: 36px 1fr;
    gap: 10px; padding: 10px 0;
    border-bottom: 1px solid var(--fu-bd);
    text-decoration: none; color: inherit;
  }
  .fu-mymatch:last-child { border-bottom: 0; padding-bottom: 0; }
  .fu-mymatch:first-child { padding-top: 0; }
  .fu-mymatch:hover .fu-mymatch-info .t { color: var(--fu-accent); }
  .fu-mymatch-date {
    text-align: center; background: rgba(255,255,255,.04);
    border-radius: 8px; padding: 6px 0;
    height: 36px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
  }
  .fu-mymatch-date .d { font-size: 13px; color: var(--fu-tx); font-weight: 500; line-height: 1; font-variant-numeric: tabular-nums; }
  .fu-mymatch-date .m { font-size: 8px; color: var(--fu-tx-3); letter-spacing: .12em; text-transform: uppercase; margin-top: 2px; }
  .fu-mymatch-info { min-width: 0; }
  .fu-mymatch-info .t { font-size: 12px; font-weight: 500; color: var(--fu-tx); transition: color .15s; }
  .fu-mymatch-info .v { font-size: 11px; color: var(--fu-tx-3); margin-top: 1px; display: inline-flex; align-items: center; gap: 5px; }

  .fu-side-title {
    font-size: 14px; font-weight: 600; letter-spacing: -0.005em; color: var(--fu-tx);
    display: flex; align-items: center; justify-content: space-between;
    margin: 0 0 14px;
  }
  .fu-side-title .count {
    font-size: 11px; color: var(--fu-tx-3); font-weight: 500;
    background: rgba(255,255,255,.04);
    padding: 2px 8px; border-radius: 999px;
  }

  /* Empty state */
  .fu-empty {
    background: var(--fu-bg-1); border: 1px dashed var(--fu-bd-2);
    border-radius: 16px; padding: 60px 30px; text-align: center;
  }
  .fu-empty-ico {
    width: 56px; height: 56px; border-radius: 14px;
    background: var(--fu-accent-soft); color: var(--fu-accent);
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 18px;
  }
  .fu-empty-title { font-size: 20px; font-weight: 300; letter-spacing: -0.02em; margin: 0 0 8px; }
  .fu-empty-sub { font-size: 14px; color: var(--fu-tx-3); margin: 0 0 22px; max-width: 380px; margin-inline: auto; }
  .fu-empty-cta {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: 12px;
    background: var(--fu-accent); color: var(--fu-accent-ink);
    font-size: 13px; font-weight: 600; border: 0;
    cursor: pointer;
  }
  .fu-empty-cta:hover { background: var(--fu-accent-hover); }

  /* Profile banner */
  .fu-profile-banner {
    margin-bottom: 28px;
    padding: 18px 22px;
    background: linear-gradient(135deg, rgba(167,139,250,.08), rgba(74,222,128,.05));
    border: 1px solid rgba(167,139,250,.22);
    border-radius: 14px;
    display: flex; align-items: center; gap: 18px; flex-wrap: wrap;
  }
  .fu-profile-banner-text { flex: 1; min-width: 200px; }
  .fu-profile-banner-text h4 { margin: 0 0 4px; font-size: 14px; font-weight: 600; color: var(--fu-tx); }
  .fu-profile-banner-text p { margin: 0; font-size: 12px; color: var(--fu-tx-3); }
  .fu-profile-banner-cta {
    padding: 9px 18px; border-radius: 10px; border: 0;
    background: var(--fu-purple); color: #fff;
    font-size: 12px; font-weight: 600; text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
  }
  .fu-profile-banner-cta:hover { background: #8b5cf6; }

  /* ── Responsive ── */
  @media (max-width: 1100px) {
    .fu-page { grid-template-columns: 1fr; }
    .fu-side { position: static; flex-direction: row; flex-wrap: wrap; gap: 14px; }
    .fu-side-card { flex: 1; min-width: 280px; }
    .fu-urgent-strip { grid-template-columns: 1fr 1fr; }
    .fu-hero-grid { grid-template-columns: 1fr; gap: 30px; min-height: auto; }
    .fu-hero-left { padding: 20px 0; }
    .fu-hero-map { max-width: 360px; }
  }
  @media (max-width: 760px) {
    .fu-hero { padding: 16px 20px 0; }
    .fu-filters-inner { padding: 14px 20px; }
    .fu-page { padding: 24px 20px 60px; }
    .fu-urgent-strip { grid-template-columns: 1fr; }
    .fu-match { grid-template-columns: 1fr; gap: 16px; }
    .fu-match-date { flex-direction: row; gap: 12px; padding-right: 0; padding-bottom: 14px; border-right: 0; border-bottom: 1px solid var(--fu-bd); }
    .fu-match-roster { padding-left: 0; border-left: 0; padding-top: 14px; border-top: 1px solid var(--fu-bd); }
    .fu-match-cta { min-width: auto; flex-direction: row; flex-wrap: wrap; }
    .fu-match-cta .fu-btn-join, .fu-match-cta .fu-btn-chat, .fu-match-cta .fu-btn-cancel { flex: 1; }
    .fu-match-cta .fu-match-price { display: none; }
    .fu-hero-search { grid-template-columns: 1fr 1fr; }
    .fu-hero-search-btn { grid-column: 1 / -1; padding: 12px; }
  }
</style>
@endpush

@section('content')
<div class="fu-scope">

  {{-- ═══ HERO ═══ --}}
  <header class="fu-hero">
    <div class="fu-hero-grid">
      <div class="fu-hero-left">
        @php
          // Sólo contar partidos que realmente están buscando jugadores
          // (status='open' Y todavía les faltan slots).
          $openCount = $games->filter(fn($g) =>
              $g->status === 'open' &&
              ($g->players_needed - $g->activeParticipants->count()) > 0
          )->count();
        @endphp
        <span class="fu-hero-eyebrow">
          <span class="fu-hero-eyebrow-pulse"></span>
          @if($openCount === 0)
            Sin partidos buscando jugadores
          @elseif($openCount === 1)
            1 partido buscando jugadores · ahora mismo
          @else
            {{ $openCount }} partidos buscando jugadores · ahora mismo
          @endif
        </span>
        <h1 class="fu-hero-title">
          Encontrá <b>tu partido</b><br>
          <em>en segundos</em>
        </h1>
        <p class="fu-hero-sub">
          Sumate a un equipo cerca tuyo, conocé jugadores nuevos y dejá de buscar gente por WhatsApp.
        </p>

        <form method="GET" action="{{ route('falta-uno.index') }}" class="fu-hero-search">
          <label class="fu-hero-search-field" style="display:block; cursor:pointer;">
            <div class="fu-hsf-k">Deporte</div>
            <div class="fu-hsf-v" style="position:relative;">
              <span id="fuSportLabel">{{ $sport ? $sportLabel($sport) : 'Cualquiera' }}</span>
              <select name="sport" onchange="this.form.submit()" style="position:absolute;inset:0;opacity:0;cursor:pointer;color:#000;">
                <option value="">Cualquiera</option>
                @foreach(['football','padel','tennis','basketball','volleyball'] as $sp)
                  <option value="{{ $sp }}" {{ $sport === $sp ? 'selected' : '' }}>{{ $sportLabel($sp) }}</option>
                @endforeach
              </select>
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </div>
          </label>
          <label class="fu-hero-search-field" style="display:block; cursor:pointer;">
            <div class="fu-hsf-k">Cuándo</div>
            <div class="fu-hsf-v" style="position:relative;">
              <span>{{ ['urgent'=>'Próximas 4hs','today'=>'Hoy','tomorrow'=>'Mañana','week'=>'Esta semana'][$time] ?? 'Cualquiera' }}</span>
              <select name="time" onchange="this.form.submit()" style="position:absolute;inset:0;opacity:0;cursor:pointer;color:#000;">
                <option value="">Cualquiera</option>
                <option value="urgent"   {{ $time === 'urgent'   ? 'selected' : '' }}>Próximas 4hs</option>
                <option value="today"    {{ $time === 'today'    ? 'selected' : '' }}>Hoy</option>
                <option value="tomorrow" {{ $time === 'tomorrow' ? 'selected' : '' }}>Mañana</option>
                <option value="week"     {{ $time === 'week'     ? 'selected' : '' }}>Esta semana</option>
              </select>
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </div>
          </label>
          <label class="fu-hero-search-field" style="display:block; cursor:pointer;">
            <div class="fu-hsf-k">Zona</div>
            <div class="fu-hsf-v" style="position:relative;">
              <span>{{ $zone ?: 'Cualquiera' }}</span>
              <select name="zone" onchange="this.form.submit()" style="position:absolute;inset:0;opacity:0;cursor:pointer;color:#000;">
                <option value="">Cualquiera</option>
                @foreach($zones as $z)
                  <option value="{{ $z }}" {{ $zone === $z ? 'selected' : '' }}>{{ $z }}</option>
                @endforeach
              </select>
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
            </div>
          </label>
          <button type="submit" class="fu-hero-search-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Buscar
          </button>
        </form>

        <div class="fu-hero-quick">
          <span>Tendencia:</span>
          <a href="{{ route('falta-uno.index', ['sport' => 'football']) }}">Fútbol 5</a>
          <a href="{{ route('falta-uno.index', ['sport' => 'padel']) }}">Pádel doble</a>
          <a href="{{ route('falta-uno.index', ['time' => 'today']) }}">Esta noche</a>
        </div>
      </div>

      {{-- AR map decorativo --}}
      <div class="fu-hero-map" aria-hidden="true">
        <div class="fu-ar-glow"></div>
        <div class="fu-map-badge">
          <span class="fu-map-badge-dot"></span>
          Buscando jugadores en tiempo real
        </div>
        <div class="fu-map-compass"></div>
        <div class="fu-map-meter">
          <div class="fu-map-meter-k">SEÑAL</div>
          <div class="fu-map-meter-v">conectada</div>
        </div>

        <svg class="fu-ar-map" viewBox="0 0 280 420" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <radialGradient id="fu-ar-bg" cx="40%" cy="35%" r="80%">
              <stop offset="0%" stop-color="#4ade80" stop-opacity="0.06"/>
              <stop offset="100%" stop-color="#4ade80" stop-opacity="0"/>
            </radialGradient>
          </defs>
          <rect width="280" height="420" fill="url(#fu-ar-bg)"/>
          <g class="fu-ar-fill-inner">
            <path d="M70 30 L130 25 L160 50 L155 80 L180 110 L175 140 L195 165 L200 200 L185 235 L190 270 L160 295 L155 330 L130 360 L110 380 L100 400 L80 405 L75 380 L85 360 L70 330 L60 290 L65 250 L55 210 L50 170 L60 130 L55 95 L65 60 Z" />
          </g>
          <g class="fu-ar-fill">
            <path d="M75 35 L135 30 L162 55 L158 85 L182 115 L178 145 L196 168 L202 202 L188 238 L192 274 L162 298 L158 332 L132 362 L112 382 L102 402 L82 408 L78 382 L88 362 L72 332 L62 292 L67 252 L57 212 L52 172 L62 132 L58 97 L68 62 Z" />
          </g>
          <g class="fu-ar-lines">
            <line x1="120" y1="115" x2="135" y2="180" class="active"/>
            <line x1="120" y1="115" x2="105" y2="200" class="active"/>
            <line x1="120" y1="115" x2="155" y2="65" />
            <line x1="135" y1="180" x2="155" y2="245" />
            <line x1="105" y1="200" x2="135" y2="180" />
            <line x1="135" y1="180" x2="170" y2="145" />
          </g>
        </svg>

        <div class="fu-ping" style="left:43%; top:42%;">
          <div class="fu-ping-ring"></div><div class="fu-ping-dot"></div>
          <div class="fu-ping-label"><span class="sport">F5</span><span class="city">Buenos Aires · 21:30</span></div>
        </div>
        <div class="fu-ping" style="left:48%; top:27%;">
          <div class="fu-ping-ring" style="animation-delay:1.2s;"></div><div class="fu-ping-dot" style="animation-delay:1.2s;"></div>
          <div class="fu-ping-label" style="animation-delay:2s;"><span class="sport">Tenis</span><span class="city">Córdoba · 19:00</span></div>
        </div>
        <div class="fu-ping" style="left:38%; top:48%;">
          <div class="fu-ping-ring" style="animation-delay:2.5s;"></div><div class="fu-ping-dot" style="animation-delay:2.5s;"></div>
          <div class="fu-ping-label" style="animation-delay:4s;"><span class="sport">Pádel</span><span class="city">Mendoza · 20:00</span></div>
        </div>
        <div class="fu-ping" style="left:56%; top:17%;"><div class="fu-ping-ring" style="animation-delay:1.8s;"></div><div class="fu-ping-dot" style="animation-delay:1.8s;"></div></div>
        <div class="fu-ping" style="left:47%; top:60%;"><div class="fu-ping-ring" style="animation-delay:3.2s;"></div><div class="fu-ping-dot" style="animation-delay:3.2s;"></div></div>
        <div class="fu-ping" style="left:56%; top:35%;"><div class="fu-ping-ring" style="animation-delay:.6s;"></div><div class="fu-ping-dot" style="animation-delay:.6s;"></div></div>
        <div class="fu-ping" style="left:39%; top:75%;"><div class="fu-ping-ring" style="animation-delay:4.1s;"></div><div class="fu-ping-dot" style="animation-delay:4.1s;"></div></div>
        <div class="fu-ping" style="left:50%; top:88%;"><div class="fu-ping-ring" style="animation-delay:2.9s;"></div><div class="fu-ping-dot" style="animation-delay:2.9s;"></div></div>
      </div>
    </div>
  </header>

  {{-- ═══ FILTERS BAR ═══ --}}
  <section class="fu-filters-section">
    <div class="fu-filters-inner">
      <div class="fu-filters-row">
        @php
          $baseQuery = collect(request()->only(['sport','gender','category','zone','time']))->all();
          $queryWithout = fn($key) => array_diff_key($baseQuery, [$key => true]);
        @endphp

        {{-- Deporte chips --}}
        <a href="{{ route('falta-uno.index', $queryWithout('sport')) }}" class="fu-chip {{ !$sport ? 'active' : '' }}">Todos</a>
        @foreach(['football'=>'Fútbol','padel'=>'Pádel','tennis'=>'Tenis','basketball'=>'Básquet','volleyball'=>'Vóley'] as $key=>$lbl)
          <a href="{{ route('falta-uno.index', array_merge($baseQuery, ['sport'=>$key])) }}" class="fu-chip {{ $sport === $key ? 'active' : '' }}">{{ $lbl }}</a>
        @endforeach

        <span class="fu-chip-divider"></span>

        {{-- Tiempo chips --}}
        @foreach(['urgent'=>'Próximas 4hs','today'=>'Hoy','tomorrow'=>'Mañana','week'=>'Esta semana'] as $key=>$lbl)
          <a href="{{ route('falta-uno.index', array_merge($baseQuery, ['time'=>$key])) }}" class="fu-chip {{ $time === $key ? 'active' : '' }}">{{ $lbl }}</a>
        @endforeach

        @if($zones->count())
          <span class="fu-chip-divider"></span>
          @foreach($zones as $z)
            <a href="{{ route('falta-uno.index', array_merge($baseQuery, ['zone'=>$z])) }}" class="fu-chip {{ $zone === $z ? 'active' : '' }}">{{ $z }}</a>
          @endforeach
        @endif

        @if($sport || $gender || $category || $zone || $time)
          <a href="{{ route('falta-uno.index') }}" class="fu-chip-clear">Limpiar todo</a>
        @endif
      </div>
    </div>
  </section>

  {{-- ═══ PAGE BODY ═══ --}}
  <main class="fu-page">
    <div>

      {{-- Banner: partido FU pendiente de pago --}}
      @auth
        @if($pendingPaymentGame)
          <div class="fu-profile-banner" style="background:linear-gradient(135deg, rgba(245,193,122,.10), rgba(245,193,122,.04)); border-color:rgba(245,193,122,.30);">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(245,193,122,.15); color:var(--fu-warn); display:flex; align-items:center; justify-content:center; flex:none;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            </div>
            <div class="fu-profile-banner-text">
              <h4>Tu partido está pendiente de pago</h4>
              <p>
                {{ $sportLabel($pendingPaymentGame->field->sport) }} · {{ $pendingPaymentGame->start_at->isoFormat('ddd D [de] MMM, HH:mm') }} · {{ $pendingPaymentGame->field->venue->name }}.
                Hasta que no pagues, no aparece en el listado y nadie puede sumarse.
              </p>
            </div>
            <a href="{{ route('reservations.checkout', $pendingPaymentGame->reservation) }}" class="fu-profile-banner-cta" style="background:var(--fu-warn); color:var(--fu-accent-ink);">
              Pagar ahora
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
          </div>
        @endif

        {{-- Banner sin perfil deportivo --}}
        @if(!auth()->user()->faltaUnoSportProfiles()->exists())
          <div class="fu-profile-banner">
            <div style="width:42px; height:42px; border-radius:12px; background:rgba(167,139,250,.15); color:var(--fu-purple); display:flex; align-items:center; justify-content:center; flex:none;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="fu-profile-banner-text">
              <h4>Creá tu perfil deportivo para sumarte a partidos</h4>
              <p>Decinos qué deportes jugás y tu nivel. Solo te tomamos 1 minuto.</p>
            </div>
            <a href="{{ route('sport-profile.create') }}" class="fu-profile-banner-cta">
              Crear perfil
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
          </div>
        @endif
      @endauth

      {{-- ═══ URGENT STRIP ═══ --}}
      @if($showUrgent)
        <section class="fu-sec">
          <div class="fu-sec-head">
            <div>
              <div class="fu-sec-eyebrow"><span class="fu-live-dot"></span>EMPIEZA YA · NECESITA JUGADORES</div>
              <h2 class="fu-sec-title">Cerca tuyo, <b>en menos de 4 horas</b></h2>
            </div>
            <a class="fu-sec-link" href="{{ route('falta-uno.index', ['time' => 'urgent']) }}">Ver todos →</a>
          </div>

          <div class="fu-urgent-strip">
            @foreach($urgentGames as $u)
              @php
                $diffMin = max(0, now()->diffInMinutes($u->start_at, false));
                $h = intdiv((int) $diffMin, 60); $m = (int) $diffMin % 60;
                $countdown = $h > 0 ? "EN {$h}H " . str_pad($m, 2, '0', STR_PAD_LEFT) . 'M' : "EN {$m}M";
                $remaining = $u->players_needed - $u->activeParticipants->count();
              @endphp
              <a class="fu-urgent" href="{{ route('falta-uno.show', $u) }}">
                <div class="fu-urgent-countdown">{{ $countdown }}</div>
                <div class="fu-urgent-title">{{ $sportLabel($u->field->sport) }} · {{ $genderLabel($u->gender_filter) }}</div>
                <div class="fu-urgent-meta">
                  {{ $u->field->venue->name }}
                  @if($u->field->venue->zone)<span class="sep"></span>{{ $u->field->venue->zone }}@endif
                </div>
                <div class="fu-urgent-bottom">
                  <div class="fu-urgent-slots">{{ $remaining > 1 ? "Faltan" : "Falta" }} <b>{{ $remaining }}</b></div>
                  <div class="fu-urgent-cta">Sumarme →</div>
                </div>
              </a>
            @endforeach
          </div>
        </section>
      @endif

      {{-- ═══ MAIN FEED ═══ --}}
      <section class="fu-sec">
        <div class="fu-sec-head">
          <div>
            <div class="fu-sec-eyebrow">PARTIDOS{{ $zone ? ' · '.strtoupper($zone) : '' }}</div>
            <h2 class="fu-sec-title"><b>Partidos abiertos</b></h2>
          </div>
        </div>

        @if($games->isEmpty())
          <div class="fu-empty">
            <div class="fu-empty-ico">
              <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 21l-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16z"/></svg>
            </div>
            <h3 class="fu-empty-title">No hay partidos con esos filtros</h3>
            <p class="fu-empty-sub">Probá cambiar el deporte o la zona, o creá tu propio partido y dejá que TuCancha te complete los lugares.</p>
            <button class="fu-empty-cta" onclick="document.getElementById('fuFieldModal').style.display='flex'">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
              Crear partido
            </button>
          </div>
        @else
          <div class="fu-feed">
            @foreach($games as $game)
              @php
                // Counts: el organizador trae $initiator_players (incluido él mismo + amigos),
                // y $activeParticipants son los que se anotaron por la app.
                // El "joined real" es la suma de ambos para mostrar X/Y intuitivo.
                $extraJoined  = $game->activeParticipants->count();
                $needed       = $game->players_needed;        // cuántos extras hacen falta
                $total        = $game->total_players;         // total de jugadores del partido
                $initiatorN   = (int) ($game->initiator_players ?? 1);
                $joined       = $extraJoined + $initiatorN;   // gente que ya está
                $remaining    = $needed - $extraJoined;       // cuántos faltan anotarse
                $pct          = $total > 0 ? min(100, round(($joined / $total) * 100)) : 0;

                $isAuthJoined = auth()->check() && $game->activeParticipants->contains('user_id', auth()->id());
                $isInitiator  = auth()->check() && $game->initiator_user_id === auth()->id();
                $isFull       = $game->status === 'full' || $remaining <= 0;

                $pricePerPerson = $total > 0 ? round(($game->amount_paid ?? 0) / $total) : 0;

                // Category label (first if range)
                $catLabel = $game->category_min ? ucfirst($game->category_min) : null;
                if ($game->category_min && $game->category_max && $game->category_min !== $game->category_max) {
                  $catLabel = ucfirst($game->category_min) . '–' . ucfirst($game->category_max);
                }

                // Initiator avatar
                $initName = $game->initiator->name ?? '?';
                [$gFrom, $gTo] = $avatarGradient($initName);
              @endphp

              <article class="fu-match {{ $isFull ? 'full' : '' }}" data-game-id="{{ $game->id }}">
                {{-- Overlay link a detalle --}}
                <a href="{{ route('falta-uno.show', $game) }}" class="fu-match-overlay-link" aria-label="Ver detalle"></a>

                <div class="fu-match-date">
                  <span class="fu-match-day">{{ ucfirst($game->start_at->locale('es')->isoFormat('ddd')) }}</span>
                  <span class="fu-match-num">{{ $game->start_at->format('d') }}</span>
                  <span class="fu-match-month">{{ $game->start_at->locale('es')->isoFormat('MMM') }}</span>
                  <span class="fu-match-time">{{ $game->start_at->format('H:i') }}</span>
                </div>

                <div class="fu-match-main">
                  <div class="fu-match-title-row">
                    <span class="fu-match-sport-ico">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M2 12h20M12 2a14 14 0 0 1 0 20M12 2a14 14 0 0 0 0 20"/>
                      </svg>
                    </span>
                    <span class="fu-match-title">
                      {{ $sportLabel($game->field->sport) }}
                      @if($game->field->format) · {{ $game->field->format }}v{{ $game->field->format }} @endif
                    </span>
                    <span class="fu-tag cat">{{ $genderLabel($game->gender_filter) }}</span>
                    @if($catLabel)
                      <span class="fu-tag {{ $categoryClass($game->category_min) }}">{{ $catLabel }}</span>
                    @endif
                  </div>

                  <div class="fu-match-venue">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-7 8-13a8 8 0 0 0-16 0c0 6 8 13 8 13z"/><circle cx="12" cy="9" r="3"/></svg>
                    <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                      {{ $game->field->venue->name }} · {{ $game->field->name }}
                    </span>
                    @if($game->field->venue->zone)<span class="zone">· {{ $game->field->venue->zone }}</span>@endif
                  </div>

                  @if($game->initiator)
                    <div class="fu-match-organizer">
                      <span class="fu-organizer-ava" style="background:linear-gradient(135deg, {{ $gFrom }}, {{ $gTo }});">{{ strtoupper(substr($initName, 0, 1)) }}</span>
                      Organiza <b>{{ $initName }}</b>
                    </div>
                  @endif
                </div>

                <div class="fu-match-roster">
                  <div class="fu-roster-head">
                    <span>JUGADORES</span>
                    <span class="fu-roster-count {{ $isFull ? 'full' : ($remaining <= 2 ? 'urgent' : '') }}">
                      {{ $joined }}/{{ $total }} {{ $isFull ? '· completo' : '· '.($remaining > 1 ? 'faltan '.$remaining : 'falta 1') }}
                    </span>
                  </div>
                  <div class="fu-roster-avatars">
                    @php
                      // Avatares: 1 organizador + (initiator_players-1) amigos anónimos + activeParticipants + slots vacíos
                      $maxShownAvatars = 8;
                      $organizerCount  = 1;                          // el organizador (1 avatar real)
                      $friendsCount    = max(0, $initiatorN - 1);    // amigos del organizador (anónimos)
                      $extras          = $game->activeParticipants;  // anotados por la app

                      $shownExtras   = $extras->take(max(0, $maxShownAvatars - $organizerCount - $friendsCount));
                      $hiddenCount   = max(0, $extras->count() - $shownExtras->count());
                      $usedAvatars   = $organizerCount + $friendsCount + $shownExtras->count() + ($hiddenCount > 0 ? 1 : 0);
                      $emptySlotsAva = max(0, min(4, $total - $joined));
                    @endphp

                    {{-- Organizador --}}
                    <span class="fu-roster-ava" title="{{ $initName }} (organizador)" style="background:linear-gradient(135deg, {{ $gFrom }}, {{ $gTo }});">
                      {{ strtoupper(substr($initName, 0, 1)) }}
                    </span>

                    {{-- Amigos del organizador (no tienen identidad propia, se ven como anónimos) --}}
                    @for($i = 0; $i < min($friendsCount, $maxShownAvatars - 1); $i++)
                      <span class="fu-roster-ava extra" title="Amigo del organizador">·</span>
                    @endfor

                    {{-- Anotados por la app --}}
                    @foreach($shownExtras as $p)
                      @php
                        $pName = $p->user->name ?? '?';
                        [$pFrom, $pTo] = $avatarGradient($pName);
                      @endphp
                      <span class="fu-roster-ava" title="{{ $pName }}" style="background:linear-gradient(135deg, {{ $pFrom }}, {{ $pTo }});">
                        {{ strtoupper(substr($pName, 0, 1)) }}
                      </span>
                    @endforeach

                    {{-- "+N" si hay más anotados que no entran --}}
                    @if($hiddenCount > 0)
                      <span class="fu-roster-ava extra">+{{ $hiddenCount }}</span>
                    @endif

                    {{-- Slots vacíos --}}
                    @for($i = 0; $i < $emptySlotsAva; $i++)
                      <span class="fu-roster-ava empty">+</span>
                    @endfor
                  </div>
                  <div class="fu-roster-bar"><span style="width:{{ $pct }}%;"></span></div>
                </div>

                <div class="fu-match-cta">
                  @if($pricePerPerson > 0)
                    <div class="fu-match-price"><b>${{ number_format($pricePerPerson, 0, ',', '.') }}</b><span>por persona</span></div>
                  @endif

                  @auth
                    @if($isInitiator)
                      <a href="{{ route('falta-uno.chat', $game) }}" class="fu-btn fu-btn-chat">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Chat
                      </a>
                      <form method="POST" action="{{ route('falta-uno.cancel', $game) }}"
                            onsubmit="return confirm('¿Cancelar el partido? {{ $game->canRefund() ? 'Recibirás un reembolso.' : 'No se devuelve el dinero.' }}')">
                        @csrf
                        <button type="submit" class="fu-btn fu-btn-cancel">Cancelar</button>
                      </form>
                    @elseif($isAuthJoined)
                      <span class="fu-badge-joined">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6 9 17l-5-5"/></svg>
                        Anotado
                      </span>
                      <a href="{{ route('falta-uno.chat', $game) }}" class="fu-btn fu-btn-chat">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Chat
                      </a>
                    @elseif(!$isFull)
                      <form method="POST" action="{{ route('falta-uno.join', $game) }}">
                        @csrf
                        <button type="submit" class="fu-btn fu-btn-join">
                          Sumarme
                          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </button>
                      </form>
                    @else
                      <span class="fu-btn-disabled">Completo</span>
                    @endif
                  @else
                    <a href="{{ route('login') }}" class="fu-btn fu-btn-join">
                      Sumarme
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                  @endauth
                </div>
              </article>
            @endforeach
          </div>
        @endif
      </section>
    </div>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="fu-side">

      <div class="fu-side-card fu-side-create">
        <div class="fu-side-create-eye">¿NO ENCONTRÁS?</div>
        <h3 class="fu-side-create-title">Armá <b>tu propio partido</b></h3>
        <p class="fu-side-create-sub">Reservá la cancha, definí el nivel, y dejá que TuCancha te complete los lugares.</p>
        <button class="fu-side-create-btn" onclick="document.getElementById('fuFieldModal').style.display='flex'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          Crear partido
        </button>
      </div>

      <div class="fu-side-card">
        <h3 class="fu-side-title">Cómo funciona</h3>
        <div class="fu-side-steps">
          <div class="fu-step-row">
            <span class="fu-step-num">01</span>
            <div>
              <div class="fu-step-title">Buscá un partido</div>
              <p class="fu-step-desc">Filtrá por deporte, día y zona. Sumate al que te quede cómodo.</p>
            </div>
          </div>
          <div class="fu-step-row">
            <span class="fu-step-num">02</span>
            <div>
              <div class="fu-step-title">Pagá tu lugar</div>
              <p class="fu-step-desc">Solo lo que te toca. Si el partido se cae, te devolvemos.</p>
            </div>
          </div>
          <div class="fu-step-row">
            <span class="fu-step-num">03</span>
            <div>
              <div class="fu-step-title">Jugá</div>
              <p class="fu-step-desc">Conocés gente nueva, sumás partidos al ranking, calificás.</p>
            </div>
          </div>
        </div>
      </div>

      @auth
        @if($myUpcomingGames->count())
          <div class="fu-side-card">
            <h3 class="fu-side-title">
              Tus partidos
              <span class="count">{{ $myUpcomingGames->count() }} próximo{{ $myUpcomingGames->count() === 1 ? '' : 's' }}</span>
            </h3>
            @foreach($myUpcomingGames as $mg)
              <a href="{{ route('falta-uno.show', $mg) }}" class="fu-mymatch">
                <div class="fu-mymatch-date">
                  <div class="d">{{ $mg->start_at->format('d') }}</div>
                  <div class="m">{{ $mg->start_at->locale('es')->isoFormat('MMM') }}</div>
                </div>
                <div class="fu-mymatch-info">
                  <div class="t">{{ $sportLabel($mg->field->sport) }} · {{ $genderLabel($mg->gender_filter) }}</div>
                  <div class="v">
                    <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    {{ $mg->start_at->format('H:i') }} · {{ $mg->field->venue->name }}
                  </div>
                </div>
              </a>
            @endforeach
          </div>
        @endif
      @endauth

    </aside>
  </main>

  {{-- ═══ Modal selector de cancha para crear partido ═══ --}}
  <div id="fuFieldModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.6); align-items:center; justify-content:center; padding:20px;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:var(--fu-bg-2); border:1px solid var(--fu-bd-2); border-radius:18px; max-width:520px; width:100%; max-height:80vh; overflow-y:auto; box-shadow:0 30px 80px rgba(0,0,0,.6);">
      <div style="padding:20px 24px 0; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0; font-size:18px; font-weight:600; color:var(--fu-tx);">Elegir cancha</h3>
        <button onclick="document.getElementById('fuFieldModal').style.display='none'" style="background:none; border:none; cursor:pointer; padding:4px; color:var(--fu-tx-3);">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <p style="padding:0 24px; margin:8px 0 16px; font-size:13px; color:var(--fu-tx-3);">Selecciona una cancha con Falta Uno habilitado para crear tu partido.</p>

      <div style="padding:0 24px 12px;">
        <input type="text" id="fuFieldSearchInput" placeholder="Buscar por cancha o complejo..." oninput="fuFilterFields()" style="width:100%; padding:10px 14px; border:1px solid var(--fu-bd-2); border-radius:10px; font-size:13px; outline:none; box-sizing:border-box; background:var(--fu-bg-1); color:var(--fu-tx); font-family:inherit;">
      </div>

      <div style="padding:0 24px 20px; display:grid; gap:8px;">
        @foreach($faltaUnoFields as $f)
          <a href="{{ route('falta-uno.create', $f) }}" class="fu-field-option" data-search="{{ strtolower($f->name . ' ' . $f->venue->name) }}" style="display:flex; align-items:center; gap:12px; padding:12px 14px; border:1px solid var(--fu-bd); border-radius:12px; text-decoration:none; color:var(--fu-tx); transition:all .15s;" onmouseover="this.style.borderColor='var(--fu-accent)';this.style.background='rgba(74,222,128,.06)'" onmouseout="this.style.borderColor='var(--fu-bd)';this.style.background='transparent'">
            <div style="width:38px; height:38px; border-radius:10px; background:var(--fu-accent-soft); color:var(--fu-accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
            </div>
            <div style="flex:1; min-width:0;">
              <div style="font-weight:500; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $f->name }}</div>
              <div style="font-size:11px; color:var(--fu-tx-3); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $f->venue->name }} · {{ $sportLabel($f->sport) }}</div>
            </div>
          </a>
        @endforeach

        @if($faltaUnoFields->isEmpty())
          <div style="text-align:center; padding:28px 16px;">
            <div style="font-weight:500; font-size:13px; color:var(--fu-tx-2);">Aún no hay canchas con Falta Uno habilitado</div>
            <div style="font-size:12px; color:var(--fu-tx-3); margin-top:4px;">Cuando un complejo active esta función, sus canchas aparecerán acá</div>
          </div>
        @endif
      </div>
    </div>
  </div>

</div>{{-- /.fu-scope --}}

@push('scripts')
<script>
  function fuFilterFields() {
    const q = document.getElementById('fuFieldSearchInput').value.toLowerCase();
    document.querySelectorAll('.fu-field-option').forEach(function(el) {
      el.style.display = el.dataset.search.includes(q) ? 'flex' : 'none';
    });
  }
</script>
@endpush

@endsection
