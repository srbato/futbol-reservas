@extends('layouts.app')

@section('title', 'Ranking de jugadores — TuCancha')
@section('meta_description', 'Ranking de jugadores en TuCancha. Jugá, calificá y subí posiciones en tu deporte favorito.')

@push('styles')
<style>
  /* ═══════════════════════════════════════════════════════════════
     RANKING V2 — Editorial Dark Leaderboard
     Palette: TuCancha #22c55e · Font: Sora bold
     Scoped with .rk2 prefix
     ═══════════════════════════════════════════════════════════════ */
  .rk2 {
    --bg: #050505;
    --bg-1: #0a0a0a;
    --bg-2: #111;
    --bg-3: #161616;
    --bd: rgba(255,255,255,.07);
    --bd-2: rgba(255,255,255,.14);
    --tx: #f2f2f2;
    --tx-2: #c8c8c8;
    --tx-3: #8a8a8a;
    --tx-4: #555;
    --accent: #22c55e;
    --accent-ink: #052010;
    --accent-hover: #4ade80;
    --accent-dim: rgba(34,197,94,.1);
    --accent-bd: rgba(34,197,94,.2);
    --gold: #d4b878;
    --silver: #c7ccd1;
    --bronze: #c68a5a;
    --danger: #f87171;
    --warn: #f5c17a;
  }
  .rk2 { background: var(--bg); color: var(--tx); font-family: 'Sora', system-ui, sans-serif; }
  .rk2 * { box-sizing: border-box; }
  .rk2 a { color: inherit; text-decoration: none; }
  .rk2 button, .rk2 select, .rk2 input { font-family: inherit; }

  /* Break out of layout's .site-main padding */
  .rk2 {
    margin-inline: calc(50% - 50vw);
    width: 100vw;
    max-width: 100vw;
    overflow-x: clip;
    margin-top: -24px;
    margin-bottom: -40px;
  }
  @media (max-width: 639px) {
    .rk2 { margin-top: -16px; margin-bottom: -32px; }
  }

  .rk2-page { max-width: 1360px; margin: 0 auto; padding: 0 40px 80px; }

  /* ── HERO ── */
  .rk2-hero {
    position: relative;
    padding: 140px 40px 72px;
    min-height: 540px;
    overflow: hidden;
    margin-bottom: 32px;
  }
  .rk2-hero-bg {
    position: absolute; inset: 0;
    background: url('/images/hero-cancha.webp') center 40% / cover no-repeat;
    opacity: .28;
    animation: rk2-kenburns 22s ease-in-out infinite alternate;
  }
  @keyframes rk2-kenburns {
    0% { transform: scale(1.04) translate(0, 0); }
    100% { transform: scale(1.10) translate(-1.5%, -1%); }
  }
  .rk2-hero-grad {
    position: absolute; inset: 0;
    background:
      radial-gradient(ellipse 70% 50% at 30% 100%, rgba(0,0,0,.8), transparent 65%),
      linear-gradient(180deg, rgba(5,5,5,.75) 0%, rgba(5,5,5,.4) 35%, rgba(5,5,5,.92) 100%);
  }
  /* Trophy iconography overlay en el fondo (sutil) */
  .rk2-hero-glow {
    position: absolute; top: 10%; right: 5%;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(212,184,120,.08), transparent 60%);
    pointer-events: none; filter: blur(40px);
  }
  .rk2-hero-inner {
    position: relative; z-index: 2;
    max-width: 1280px; margin: 0 auto;
    display: grid; grid-template-columns: 1fr auto;
    gap: 40px; align-items: end;
  }
  .rk2-crumbs {
    display: flex; align-items: center; gap: 10px;
    font-size: 12px; color: var(--tx-3); font-weight: 600;
    margin-bottom: 18px;
  }
  .rk2-crumbs a { color: var(--tx-3); transition: color .15s; }
  .rk2-crumbs a:hover { color: var(--tx); }
  .rk2-crumbs .curr { color: var(--tx); }
  .rk2-hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 6px 14px 6px 10px;
    background: rgba(212,184,120,.08);
    border: 1px solid rgba(212,184,120,.2);
    border-radius: 999px;
    font-size: 12px; font-weight: 700; color: var(--gold);
    margin-bottom: 18px;
    backdrop-filter: blur(12px);
  }
  .rk2-hero-badge-ico {
    width: 14px; height: 14px;
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--gold);
  }
  .rk2-h1 {
    font-size: clamp(44px, 6.5vw, 80px);
    font-weight: 800;
    letter-spacing: -0.045em; line-height: .98;
    margin: 0; color: #fff;
  }
  .rk2-h1 b { font-weight: 900; }
  .rk2-h1 i { font-style: italic; font-weight: 700; color: var(--accent); letter-spacing: -0.045em; }
  .rk2-sub {
    font-size: 17px; font-weight: 500;
    color: var(--tx-2); line-height: 1.55;
    margin: 18px 0 0; max-width: 580px;
  }

  /* Right hero card: total players */
  .rk2-totals {
    display: inline-flex; flex-direction: column; align-items: flex-end;
    padding: 18px 24px;
    background: rgba(10,10,10,.55);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 16px;
    min-width: 220px;
    backdrop-filter: blur(16px);
  }
  .rk2-totals-label { font-size: 10px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--tx-3); margin-bottom: 6px; }
  .rk2-totals-num { font-size: 32px; font-weight: 800; color: #fff; letter-spacing: -0.035em; line-height: 1; }
  .rk2-totals-meta { font-size: 12px; color: var(--tx-2); margin-top: 8px; display: flex; gap: 8px; align-items: center; font-weight: 600; }
  .rk2-live-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 0 3px rgba(34,197,94,.25); animation: rk2-pulse 1.8s ease-in-out infinite; }
  @keyframes rk2-pulse { 0%,100% { opacity: 1; } 50% { opacity: .5; } }

  /* ── SPORT TABS ── */
  .rk2-sport-tabs {
    display: flex; gap: 6px; margin-bottom: 28px; flex-wrap: wrap;
  }
  .rk2-sport-tab {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 16px;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--bd);
    border-radius: 999px;
    font-size: 13px; font-weight: 600;
    color: var(--tx-2);
    text-decoration: none;
    transition: all .15s;
  }
  .rk2-sport-tab:hover { background: rgba(255,255,255,.06); color: var(--tx); border-color: var(--bd-2); }
  .rk2-sport-tab.active { background: var(--tx); color: var(--bg); border-color: var(--tx); }
  .rk2-sport-tab-count { font-size: 11px; color: var(--tx-3); font-weight: 600; }
  .rk2-sport-tab.active .rk2-sport-tab-count { color: rgba(0,0,0,.4); }

  /* ── GRID LAYOUT ── */
  .rk2-grid {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 24px;
    align-items: start;
  }
  .rk2-grid.no-self { grid-template-columns: 1fr; }

  /* ── SELF CARD ── */
  .rk2-self {
    position: sticky; top: 96px;
    padding: 24px;
    background: linear-gradient(180deg, rgba(34,197,94,.06), rgba(34,197,94,.02) 60%, transparent);
    border: 1px solid rgba(34,197,94,.2);
    border-radius: 20px;
    display: flex; flex-direction: column; gap: 20px;
  }
  .rk2-self-eyebrow {
    font-size: 10px; font-weight: 800; letter-spacing: .16em;
    text-transform: uppercase; color: var(--accent);
  }
  .rk2-self-identity { display: flex; align-items: center; gap: 14px; }
  .rk2-self-avatar {
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), #16a34a);
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 20px; color: var(--accent-ink);
    flex: none;
    overflow: hidden;
  }
  .rk2-self-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .rk2-self-name { font-size: 20px; font-weight: 800; letter-spacing: -0.02em; color: var(--tx); margin: 0; }
  .rk2-self-meta {
    font-size: 12px; color: var(--tx-3); margin-top: 2px;
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    font-weight: 500;
  }
  .rk2-self-meta .dot { width: 2px; height: 2px; background: var(--tx-4); border-radius: 50%; }

  .rk2-self-big {
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px; align-items: end;
    padding-top: 20px; border-top: 1px solid rgba(34,197,94,.12);
  }
  .rk2-self-rank {
    font-size: 48px; font-weight: 800; color: var(--tx);
    letter-spacing: -0.04em; line-height: .9;
    font-variant-numeric: tabular-nums;
    display: inline-flex; align-items: baseline;
  }
  .rk2-self-rank small { font-size: 16px; color: var(--tx-3); font-weight: 600; margin-left: 4px; }
  .rk2-self-rank-caption {
    font-size: 11px; font-weight: 700; letter-spacing: .12em;
    text-transform: uppercase; color: var(--tx-3); margin-top: 6px;
  }
  .rk2-self-rating-block { text-align: right; }
  .rk2-self-rating {
    font-size: 26px; font-weight: 800; color: var(--accent);
    letter-spacing: -0.03em; line-height: 1;
    font-variant-numeric: tabular-nums;
  }
  .rk2-self-rating small { font-size: 14px; font-weight: 600; color: rgba(34,197,94,.6); }
  .rk2-self-rating-label {
    font-size: 10px; font-weight: 700; letter-spacing: .14em;
    text-transform: uppercase; color: var(--tx-3); margin-top: 8px;
  }

  .rk2-self-stats {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;
    padding-top: 18px; border-top: 1px solid rgba(34,197,94,.12);
  }
  .rk2-self-stat-k {
    font-size: 10px; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: var(--tx-3);
  }
  .rk2-self-stat-v {
    font-size: 18px; font-weight: 700; color: var(--tx);
    letter-spacing: -0.02em; margin-top: 4px;
    font-variant-numeric: tabular-nums;
  }

  .rk2-self-badges {
    padding-top: 18px; border-top: 1px solid rgba(34,197,94,.12);
  }
  .rk2-self-badges-label {
    font-size: 10px; font-weight: 800; letter-spacing: .14em;
    text-transform: uppercase; color: var(--tx-3); margin-bottom: 10px;
  }
  .rk2-self-badges-row { display: flex; gap: 6px; flex-wrap: wrap; }

  .rk2-self-cta {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    padding: 11px 14px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd-2);
    border-radius: 12px;
    font-size: 13px; font-weight: 700; color: var(--tx);
    transition: background .15s, border-color .15s;
    text-decoration: none;
  }
  .rk2-self-cta:hover { background: rgba(255,255,255,.08); border-color: var(--bd-2); color: var(--tx); }

  /* Empty states in self card */
  .rk2-self-empty { text-align: center; padding: 12px 0 8px; }
  .rk2-self-empty-ico {
    width: 48px; height: 48px; border-radius: 14px;
    background: rgba(34,197,94,.1);
    color: var(--accent);
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 14px;
  }
  .rk2-self-empty-title {
    font-size: 15px; font-weight: 800; color: var(--tx);
    margin: 0 0 6px; letter-spacing: -0.01em;
  }
  .rk2-self-empty-msg {
    font-size: 13px; color: var(--tx-3); font-weight: 500;
    line-height: 1.55; margin: 0 0 18px;
  }

  /* ── RIGHT COLUMN ── */
  .rk2-right { display: flex; flex-direction: column; gap: 24px; min-width: 0; }

  /* ── PODIUM ── */
  .rk2-podium {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;
  }
  .rk2-pod {
    position: relative;
    padding: 24px 22px 22px;
    border-radius: 20px;
    border: 1px solid var(--bd);
    background: linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.01) 70%);
    overflow: hidden;
    transition: border-color .2s, transform .2s;
  }
  .rk2-pod:hover { border-color: var(--bd-2); transform: translateY(-2px); }
  .rk2-pod-glow {
    position: absolute; pointer-events: none;
    width: 280px; height: 280px; border-radius: 50%;
    top: -160px; right: -80px; filter: blur(60px); opacity: .4;
  }
  .rk2-pod.gold .rk2-pod-glow { background: radial-gradient(circle, rgba(212,184,120,.5), transparent 70%); opacity: .6; }
  .rk2-pod.silver .rk2-pod-glow { background: radial-gradient(circle, rgba(199,204,209,.28), transparent 70%); }
  .rk2-pod.bronze .rk2-pod-glow { background: radial-gradient(circle, rgba(198,138,90,.3), transparent 70%); }

  .rk2-pod-top {
    display: flex; align-items: center; justify-content: space-between;
    position: relative; z-index: 1;
  }
  .rk2-pod-place {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; font-weight: 800; letter-spacing: .14em;
    text-transform: uppercase; color: var(--tx-3);
  }
  .rk2-pod.gold .rk2-pod-place { color: var(--gold); }
  .rk2-pod.silver .rk2-pod-place { color: var(--silver); }
  .rk2-pod.bronze .rk2-pod-place { color: var(--bronze); }
  .rk2-pod-medal {
    width: 22px; height: 22px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 900; color: #000;
  }
  .rk2-pod.gold .rk2-pod-medal { background: var(--gold); }
  .rk2-pod.silver .rk2-pod-medal { background: var(--silver); }
  .rk2-pod.bronze .rk2-pod-medal { background: var(--bronze); }
  .rk2-pod-score {
    font-size: 11px; color: var(--tx-3); font-weight: 500;
  }
  .rk2-pod-score b { color: var(--tx); font-weight: 800; font-size: 13px; }

  .rk2-pod-identity {
    display: flex; align-items: center; gap: 14px;
    margin-top: 28px; position: relative; z-index: 1;
  }
  .rk2-pod-avatar {
    width: 56px; height: 56px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 18px; color: #000;
    flex: none;
    border: 2px solid rgba(255,255,255,.08);
    overflow: hidden;
  }
  .rk2-pod-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .rk2-pod-name {
    font-size: 18px; font-weight: 800; letter-spacing: -0.015em;
    color: var(--tx); line-height: 1.15;
  }
  .rk2-pod-sub { font-size: 12px; color: var(--tx-3); margin-top: 4px; font-weight: 500; }

  .rk2-pod-stats {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 4px;
    margin-top: 22px; padding-top: 18px;
    border-top: 1px solid var(--bd);
    position: relative; z-index: 1;
  }
  .rk2-pod-stat-k {
    font-size: 10px; letter-spacing: .12em; text-transform: uppercase;
    color: var(--tx-3); font-weight: 700;
  }
  .rk2-pod-stat-v {
    font-size: 15px; color: var(--tx); font-weight: 700;
    letter-spacing: -0.01em; margin-top: 2px;
    font-variant-numeric: tabular-nums;
  }

  /* ── FILTERS ── */
  .rk2-filters {
    position: sticky; top: 80px; z-index: 20;
    background: rgba(5,5,5,.88);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
    border: 1px solid var(--bd);
    border-radius: 16px;
    padding: 8px;
    display: grid;
    grid-template-columns: minmax(220px, 1fr) auto auto auto;
    gap: 6px;
    align-items: stretch;
  }
  .rk2-f-search {
    display: flex; align-items: center; gap: 10px;
    padding: 0 14px;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--bd);
    border-radius: 10px;
  }
  .rk2-f-search svg { color: var(--tx-3); flex-shrink: 0; }
  .rk2-f-search input {
    flex: 1; padding: 11px 0;
    background: transparent; border: none; outline: none;
    color: var(--tx); font-size: 13px; font-weight: 500;
    min-width: 0;
  }
  .rk2-f-search input::placeholder { color: var(--tx-3); }
  .rk2-f-search-btn {
    padding: 7px 14px;
    background: var(--accent); color: var(--accent-ink);
    border: none; border-radius: 8px;
    font-size: 12px; font-weight: 800;
    cursor: pointer;
    transition: background .15s;
  }
  .rk2-f-search-btn:hover { background: var(--accent-hover); }

  .rk2-f-dd {
    position: relative;
    display: inline-flex; align-items: center; gap: 8px;
    padding: 0 14px;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--bd);
    border-radius: 10px;
    font-size: 12px; font-weight: 600; color: var(--tx-2);
    white-space: nowrap;
    transition: background .15s, color .15s, border-color .15s;
  }
  .rk2-f-dd:hover { color: var(--tx); background: rgba(255,255,255,.06); }
  .rk2-f-dd.has-value { color: var(--tx); border-color: var(--bd-2); background: rgba(255,255,255,.06); }
  .rk2-f-dd-label {
    font-size: 10px; letter-spacing: .1em;
    text-transform: uppercase; color: var(--tx-3); font-weight: 700;
  }
  .rk2-f-dd-val { font-weight: 700; }
  .rk2-f-dd select {
    position: absolute; inset: 0;
    opacity: 0; width: 100%; height: 100%;
    cursor: pointer; appearance: none;
  }
  .rk2-f-clear {
    padding: 0 14px;
    font-size: 12px; color: var(--tx-3); font-weight: 600;
    border-radius: 10px; text-decoration: none;
    display: inline-flex; align-items: center;
    transition: color .15s, background .15s;
  }
  .rk2-f-clear:hover { color: var(--tx); background: rgba(255,255,255,.04); }

  /* ── LEADERBOARD ── */
  .rk2-lb {
    background: var(--bg-1);
    border: 1px solid var(--bd);
    border-radius: 20px;
    overflow: hidden;
  }
  .rk2-lb-head {
    display: grid;
    grid-template-columns: 52px 1.8fr 80px 60px 1fr 1fr 70px 1.1fr 70px 40px;
    gap: 12px;
    padding: 14px 22px;
    border-bottom: 1px solid var(--bd);
    background: rgba(255,255,255,.015);
    font-size: 10px; font-weight: 800; letter-spacing: .14em;
    text-transform: uppercase; color: var(--tx-3);
    align-items: center;
  }
  .rk2-lb-head span.num { text-align: right; }

  .rk2-lb-row {
    display: grid;
    grid-template-columns: 52px 1.8fr 80px 60px 1fr 1fr 70px 1.1fr 70px 40px;
    gap: 12px;
    padding: 14px 22px;
    border-bottom: 1px solid var(--bd);
    align-items: center;
    transition: background .15s;
    text-decoration: none;
  }
  .rk2-lb-row:last-child { border-bottom: none; }
  .rk2-lb-row:hover { background: rgba(255,255,255,.025); }
  .rk2-lb-row.you {
    background: linear-gradient(90deg, rgba(34,197,94,.08), rgba(34,197,94,.015) 40%, transparent);
    border-left: 2px solid var(--accent);
    padding-left: 20px;
  }
  .rk2-lb-row.you:hover {
    background: linear-gradient(90deg, rgba(34,197,94,.12), rgba(34,197,94,.03) 40%, transparent);
  }

  .rk2-lb-pos-num {
    font-size: 16px; font-weight: 800; color: var(--tx);
    letter-spacing: -0.02em; font-variant-numeric: tabular-nums;
  }
  .rk2-lb-pos-num.p1 { color: var(--gold); }
  .rk2-lb-pos-num.p2 { color: var(--silver); }
  .rk2-lb-pos-num.p3 { color: var(--bronze); }

  .rk2-lb-player { display: flex; align-items: center; gap: 12px; min-width: 0; }
  .rk2-lb-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 13px; color: #000;
    flex: none;
    overflow: hidden;
    background: linear-gradient(135deg, #3a3a3a, #1a1a1a);
    color: var(--gold);
  }
  .rk2-lb-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .rk2-lb-avatar.p1 { background: linear-gradient(135deg, var(--gold), #a88844); color: #000; border: 2px solid rgba(212,184,120,.35); }
  .rk2-lb-avatar.p2 { background: linear-gradient(135deg, #dbe0e5, #8c9199); color: #000; border: 2px solid rgba(199,204,209,.35); }
  .rk2-lb-avatar.p3 { background: linear-gradient(135deg, #d79566, #7a4e32); color: #000; border: 2px solid rgba(198,138,90,.35); }
  .rk2-lb-player-info { min-width: 0; }
  .rk2-lb-name {
    font-size: 14px; font-weight: 700; color: var(--tx);
    letter-spacing: -0.01em;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .rk2-lb-sub {
    font-size: 11px; color: var(--tx-3); margin-top: 3px;
    display: flex; align-items: center; gap: 6px; white-space: nowrap;
    font-weight: 500;
  }
  .rk2-lb-sub .dot { width: 2px; height: 2px; background: var(--tx-4); border-radius: 50%; }

  .rk2-level-chip {
    display: inline-flex; align-items: center;
    padding: 3px 9px; border-radius: 999px;
    font-size: 10px; font-weight: 700; letter-spacing: .04em;
    background: rgba(255,255,255,.05);
    color: var(--tx-2);
    border: 1px solid var(--bd);
    text-transform: capitalize;
    white-space: nowrap;
  }
  .rk2-level-chip.competitivo, .rk2-level-chip.primera, .rk2-level-chip.segunda {
    background: rgba(34,197,94,.1); color: var(--accent); border-color: rgba(34,197,94,.2);
  }
  .rk2-level-chip.avanzado, .rk2-level-chip.tercera, .rk2-level-chip.cuarta {
    background: rgba(245,193,122,.1); color: var(--warn); border-color: rgba(245,193,122,.2);
  }

  .rk2-lb-pj, .rk2-lb-rating, .rk2-lb-score {
    text-align: right; font-variant-numeric: tabular-nums;
    color: var(--tx); font-size: 14px; font-weight: 700;
    letter-spacing: -0.01em;
  }
  .rk2-lb-score {
    font-weight: 800; color: var(--accent); font-size: 16px;
  }
  .rk2-lb-rating-stars {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 12px; color: var(--accent);
    font-weight: 700;
    justify-content: flex-end;
  }

  .rk2-lb-record {
    display: flex; gap: 4px; align-items: center;
    font-size: 11px; color: var(--tx-3);
    font-variant-numeric: tabular-nums;
    font-weight: 600;
  }
  .rk2-lb-record b { color: var(--tx); font-weight: 800; font-size: 12px; }

  .rk2-lb-winrate { display: flex; align-items: center; gap: 8px; }
  .rk2-lb-winrate-bar {
    flex: 1; height: 4px;
    background: rgba(255,255,255,.06);
    border-radius: 999px; overflow: hidden; min-width: 40px;
  }
  .rk2-lb-winrate-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--accent), var(--accent-hover));
    border-radius: 999px;
  }
  .rk2-lb-winrate-pct {
    font-size: 11px; color: var(--tx-2); font-weight: 800;
    font-variant-numeric: tabular-nums; min-width: 30px; text-align: right;
  }

  .rk2-lb-badges { display: flex; gap: 4px; flex-wrap: wrap; }
  .rk2-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 10px; font-weight: 700;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    color: var(--tx-2);
    white-space: nowrap;
  }
  .rk2-badge.badge-green { background: rgba(34,197,94,.08); color: var(--accent); border-color: rgba(34,197,94,.2); }
  .rk2-badge.badge-gold { background: rgba(212,184,120,.08); color: var(--gold); border-color: rgba(212,184,120,.2); }
  .rk2-badge.badge-blue { background: rgba(122,190,245,.08); color: #7abef5; border-color: rgba(122,190,245,.2); }
  .rk2-badge.badge-red { background: rgba(248,113,113,.08); color: var(--danger); border-color: rgba(248,113,113,.2); }
  .rk2-badge.badge-orange { background: rgba(255,154,75,.08); color: #ff9a4b; border-color: rgba(255,154,75,.2); }
  .rk2-badge.badge-silver { background: rgba(199,204,209,.05); color: var(--silver); border-color: rgba(199,204,209,.14); }
  .rk2-badge.badge-more { color: var(--tx-3); background: rgba(255,255,255,.02); }

  .rk2-lb-action {
    display: inline-flex; align-items: center; justify-content: center;
    width: 30px; height: 30px; border-radius: 10px;
    color: var(--tx-3);
    opacity: 0; transition: opacity .15s, background .15s, color .15s;
  }
  .rk2-lb-row:hover .rk2-lb-action { opacity: 1; }
  .rk2-lb-row.you .rk2-lb-action { opacity: 1; }
  .rk2-lb-action:hover { background: rgba(255,255,255,.06); color: var(--tx); }

  /* ── EMPTY STATE (no players) ── */
  .rk2-empty {
    padding: 64px 32px; text-align: center;
    background: var(--bg-1);
    border: 1px dashed var(--bd-2);
    border-radius: 20px;
  }
  .rk2-empty-ico {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--tx-3); margin-bottom: 14px;
  }
  .rk2-empty h4 { font-size: 17px; font-weight: 800; color: var(--tx); margin: 0 0 6px; }
  .rk2-empty p { font-size: 13px; color: var(--tx-3); margin: 0; font-weight: 500; }

  /* ── PAGINATION ── */
  .rk2-pager {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px;
    background: rgba(255,255,255,.015);
    border-top: 1px solid var(--bd);
  }
  .rk2-pager-info { font-size: 12px; color: var(--tx-3); font-weight: 600; }
  .rk2-pager-info b { color: var(--tx); font-weight: 800; }
  .rk2-pager-nav { display: flex; gap: 4px; }
  .rk2-pager-btn {
    min-width: 34px; height: 34px; padding: 0 12px;
    border-radius: 9px;
    font-size: 13px; font-weight: 700; color: var(--tx-2);
    background: transparent; border: none;
    display: inline-flex; align-items: center; justify-content: center;
    cursor: pointer; text-decoration: none;
    transition: background .15s, color .15s;
  }
  .rk2-pager-btn:hover { background: rgba(255,255,255,.06); color: var(--tx); }
  .rk2-pager-btn.active { background: var(--tx); color: var(--bg); }
  .rk2-pager-btn:disabled { opacity: .3; cursor: default; }
  .rk2-pager-btn:disabled:hover { background: transparent; }

  /* ── NOTE ── */
  .rk2-note {
    margin-top: 32px;
    display: flex; gap: 14px; align-items: flex-start;
    padding: 18px 20px;
    background: rgba(255,255,255,.02);
    border: 1px solid var(--bd);
    border-radius: 14px;
  }
  .rk2-note-ico {
    flex: none; width: 34px; height: 34px; border-radius: 10px;
    background: rgba(34,197,94,.1); color: var(--accent);
    display: inline-flex; align-items: center; justify-content: center;
  }
  .rk2-note h5 {
    margin: 0 0 4px; font-size: 13px; font-weight: 800;
    color: var(--tx); letter-spacing: -0.005em;
  }
  .rk2-note p {
    margin: 0; font-size: 12px; color: var(--tx-3);
    line-height: 1.6; max-width: 720px; font-weight: 500;
  }
  .rk2-note p strong { color: var(--tx-2); font-weight: 700; }

  /* ── Responsive ── */
  @media (max-width: 1100px) {
    .rk2-lb-head, .rk2-lb-row {
      grid-template-columns: 44px 1.4fr 70px 52px 1fr 70px 1fr 60px 36px;
    }
    .rk2-lb-winrate-bar { display: none; }
    .rk2-lb-record { display: none; }
  }
  @media (max-width: 900px) {
    .rk2-grid { grid-template-columns: 1fr; }
    .rk2-self { position: static; top: 0; }
    .rk2-podium { grid-template-columns: 1fr; }
    .rk2-filters { grid-template-columns: 1fr; }
    .rk2-lb-head { display: none; }
    .rk2-lb-row {
      grid-template-columns: 44px 1fr auto;
      grid-template-areas: 'pos player score' 'pos badges badges';
      gap: 8px;
    }
    .rk2-lb-pj, .rk2-lb-rating, .rk2-lb-record, .rk2-lb-winrate { display: none; }
    .rk2-lb-badges { grid-area: badges; }
    .rk2-lb-score { grid-area: score; }
    .rk2-hero { padding: 120px 20px 56px; min-height: 440px; }
    .rk2-hero-inner { grid-template-columns: 1fr; gap: 20px; align-items: flex-start; }
    .rk2-totals { align-self: flex-start; align-items: flex-start; }
    .rk2-head { grid-template-columns: 1fr; gap: 20px; }
    .rk2-totals { align-self: flex-start; align-items: flex-start; }
  }
  @media (max-width: 640px) {
    .rk2-page { padding: 0 20px 60px; }
    .rk2-hero { padding: 100px 20px 48px; min-height: 380px; }
  }
</style>
@endpush

@section('content')
@php
  $sportIcons = [
    'football'   => '<circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M2 12h20"/>',
    'padel'      => '<rect x="4" y="6" width="16" height="12" rx="2"/><path d="M4 12h16"/>',
    'tennis'     => '<circle cx="12" cy="12" r="10"/><path d="M4.5 9h15M4.5 15h15"/>',
    'basketball' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a14 14 0 0 1 0 20M12 2a14 14 0 0 0 0 20"/>',
    'volleyball' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M8 4v16M16 4v16"/>',
  ];
  $zoneLabel = fn($u) => '';
  $initials = function($name) {
    $parts = preg_split('/\s+/', trim($name ?? ''));
    $initials = '';
    foreach ($parts as $p) { if ($p) $initials .= strtoupper(mb_substr($p, 0, 1)); if (strlen($initials) >= 2) break; }
    return $initials ?: 'U';
  };
  $categoryMap = [
    // football & others
    'recreativo'   => ['label' => 'Recreativo',   'cls' => ''],
    'intermedio'   => ['label' => 'Intermedio',   'cls' => 'avanzado'],
    'avanzado'     => ['label' => 'Avanzado',     'cls' => 'avanzado'],
    'competitivo'  => ['label' => 'Competitivo',  'cls' => 'competitivo'],
    // padel
    'primera'      => ['label' => '1ra',  'cls' => 'competitivo'],
    'segunda'      => ['label' => '2da',  'cls' => 'competitivo'],
    'tercera'      => ['label' => '3ra',  'cls' => 'avanzado'],
    'cuarta'       => ['label' => '4ta',  'cls' => 'avanzado'],
    'quinta'       => ['label' => '5ta',  'cls' => ''],
    'sexta'        => ['label' => '6ta',  'cls' => ''],
    'septima'      => ['label' => '7ma',  'cls' => ''],
    'octava'       => ['label' => '8va',  'cls' => ''],
  ];
  $renderBadge = function($badge) {
    $color = $badge['color'] ?? 'neutral';
    $cls   = match($color) {
      'green', 'emerald' => 'badge-green',
      'gold', 'yellow'   => 'badge-gold',
      'blue'             => 'badge-blue',
      'red'              => 'badge-red',
      'orange'           => 'badge-orange',
      'silver'           => 'badge-silver',
      default            => '',
    };
    $label = $badge['label'] ?? $badge['name'] ?? '';
    return '<span class="rk2-badge ' . $cls . '" title="' . htmlspecialchars($badge['description'] ?? $label, ENT_QUOTES) . '">' . htmlspecialchars($label, ENT_QUOTES) . '</span>';
  };
@endphp

<div class="rk2">

  {{-- ─── HERO ─── --}}
  <section class="rk2-hero">
    <div class="rk2-hero-bg"></div>
    <div class="rk2-hero-grad"></div>
    <div class="rk2-hero-glow"></div>

    <div class="rk2-hero-inner">
      <div>
        <div class="rk2-crumbs">
          <a href="{{ route('home') }}">Inicio</a>
          <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
          <span class="curr">Ranking</span>
        </div>
        <span class="rk2-hero-badge">
          <span class="rk2-hero-badge-ico">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 7 7 .5-5.5 4.7L18 22l-6-4-6 4 1.5-7.8L2 9.5 9 9z"/></svg>
          </span>
          Ranking · Temporada activa
        </span>
        <h1 class="rk2-h1"><b>Ranking</b> de <i>jugadores</i></h1>
        <p class="rk2-sub">Sumá puntos jugando, ganando partidos y manteniendo buena reputación. El score combina resultados, asistencia y calificación de compañeros.</p>
      </div>
      <div class="rk2-totals">
        <span class="rk2-totals-label">Jugadores rankeados</span>
        <span class="rk2-totals-num">{{ number_format($players->total(), 0, ',', '.') }}</span>
        <span class="rk2-totals-meta"><span class="rk2-live-dot"></span>Se actualiza en vivo</span>
      </div>
    </div>
  </section>

  <main class="rk2-page">

    {{-- ─── SPORT TABS ─── --}}
    <div class="rk2-sport-tabs">
      <a href="{{ route('ranking.index') }}" class="rk2-sport-tab {{ !$sportFilter ? 'active' : '' }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
        Todos <span class="rk2-sport-tab-count">{{ number_format($totalProfiles, 0, ',', '.') }}</span>
      </a>
      @foreach($sportLabels as $sportKey => $sportLabel)
        <a href="{{ route('ranking.index', ['sport' => $sportKey]) }}" class="rk2-sport-tab {{ $sportFilter === $sportKey ? 'active' : '' }}">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $sportIcons[$sportKey] ?? '' !!}</svg>
          {{ $sportLabel }} <span class="rk2-sport-tab-count">{{ $sportCounts[$sportKey] ?? 0 }}</span>
        </a>
      @endforeach
    </div>

    {{-- ─── GRID (self + main) ─── --}}
    <div class="rk2-grid {{ !$selfData ? 'no-self' : '' }}">

      {{-- ─── SELF CARD ─── --}}
      @if($selfData)
        <aside class="rk2-self">
          <span class="rk2-self-eyebrow">Tu posición</span>

          @if($selfData->state === 'no_profile')
            {{-- Has no profile in this sport (or any) --}}
            <div class="rk2-self-empty">
              <div class="rk2-self-empty-ico">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
              </div>
              <h4 class="rk2-self-empty-title">Todavía no tenés perfil</h4>
              <p class="rk2-self-empty-msg">
                @if($sportFilter)
                  Jugá partidos de {{ strtolower($sportLabels[$sportFilter]) }} y calificá a tus compañeros para aparecer acá.
                @else
                  Jugá partidos y calificá a tus compañeros para aparecer en el ranking.
                @endif
              </p>
              <a href="{{ route('falta-uno.index') }}" class="rk2-self-cta">
                Ver partidos abiertos
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
              </a>
            </div>
          @else
            {{-- Has profile (ranked or insufficient games) --}}
            @php $s = $selfData->stats; @endphp
            <div class="rk2-self-identity">
              <span class="rk2-self-avatar">
                @if(auth()->user()->avatar_path ?? false)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar_path) }}" alt="{{ auth()->user()->name }}">
                @else
                  {{ $initials(auth()->user()->name) }}
                @endif
              </span>
              <div>
                <h3 class="rk2-self-name">{{ auth()->user()->name }}</h3>
                <div class="rk2-self-meta">
                  @if($selfData->best_sport_label)
                    <span>Mejor en: <strong style="color:var(--tx-2);">{{ $selfData->best_sport_label }}</strong></span>
                  @elseif($sportFilter)
                    <span>{{ $sportLabels[$sportFilter] }}</span>
                  @elseif($s->sport)
                    <span>{{ $sportLabels[$s->sport] ?? ucfirst($s->sport) }}</span>
                  @endif
                  @if($s->category)
                    <span class="dot"></span>
                    <span>{{ $categoryMap[$s->category]['label'] ?? ucfirst($s->category) }}</span>
                  @endif
                </div>
              </div>
            </div>

            <div class="rk2-self-big">
              <div>
                @if($selfData->state === 'ranked' && $selfData->position)
                  <div class="rk2-self-rank">#{{ $selfData->position }}<small>/ {{ $selfData->total }}</small></div>
                  @php $topPct = $selfData->total > 0 ? ceil(($selfData->position / $selfData->total) * 100) : null; @endphp
                  @if($topPct !== null)
                    <div class="rk2-self-rank-caption">Top {{ $topPct }}%</div>
                  @endif
                @else
                  <div class="rk2-self-rank">—</div>
                  <div class="rk2-self-rank-caption">Sin ranking aún</div>
                @endif
              </div>
              <div class="rk2-self-rating-block">
                <div class="rk2-self-rating">{{ number_format($s->rating, 1) }}<small>/5</small></div>
                <div class="rk2-self-rating-label">Rating</div>
              </div>
            </div>

            <div class="rk2-self-stats">
              <div>
                <div class="rk2-self-stat-k">PJ</div>
                <div class="rk2-self-stat-v">{{ $s->games_played }}</div>
              </div>
              <div>
                <div class="rk2-self-stat-k">Win rate</div>
                <div class="rk2-self-stat-v">{{ $s->win_rate }}%</div>
              </div>
              <div>
                <div class="rk2-self-stat-k">Score</div>
                <div class="rk2-self-stat-v">{{ number_format($s->score, 0) }}</div>
              </div>
            </div>

            @if($selfData->state === 'insufficient_games')
              <div style="padding:12px 14px; background:rgba(245,193,122,.08); border:1px solid rgba(245,193,122,.2); border-radius:10px; font-size:12px; color:var(--warn); font-weight:600; line-height:1.5;">
                Jugá {{ max(0, 3 - $s->games_played) }} partido{{ (3 - $s->games_played) === 1 ? '' : 's' }} más con tu calificación registrada para aparecer en el ranking público.
              </div>
            @endif

            @if(!empty($s->badges))
              <div class="rk2-self-badges">
                <div class="rk2-self-badges-label">Tus medallas · {{ count($s->badges) }}</div>
                <div class="rk2-self-badges-row">
                  @foreach(array_slice($s->badges, 0, 4) as $b)
                    {!! $renderBadge($b) !!}
                  @endforeach
                  @if(count($s->badges) > 4)
                    <span class="rk2-badge badge-more">+{{ count($s->badges) - 4 }}</span>
                  @endif
                </div>
              </div>
            @endif

            <a href="{{ route('sport-profile.public', auth()->user()) }}" class="rk2-self-cta">
              Ver mi perfil completo
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
          @endif
        </aside>
      @endif

      {{-- ─── RIGHT COLUMN ─── --}}
      <div class="rk2-right">

        {{-- ─── PODIUM ─── --}}
        @if($top3->count() > 0)
          <section class="rk2-podium">
            @php
              // Order: 2nd, 1st, 3rd for the visual
              $podiumOrder = [];
              if (isset($top3[1])) $podiumOrder[] = ['data' => $top3[1], 'cls' => 'silver', 'medal' => '2', 'place' => 'Segundo'];
              if (isset($top3[0])) $podiumOrder[] = ['data' => $top3[0], 'cls' => 'gold', 'medal' => '1', 'place' => 'Primero'];
              if (isset($top3[2])) $podiumOrder[] = ['data' => $top3[2], 'cls' => 'bronze', 'medal' => '3', 'place' => 'Tercero'];
            @endphp
            @foreach($podiumOrder as $item)
              @php $d = $item['data']; @endphp
              <a href="{{ route('sport-profile.public', $d->user) }}" class="rk2-pod {{ $item['cls'] }}" style="display:block;">
                <div class="rk2-pod-glow"></div>
                <div class="rk2-pod-top">
                  <span class="rk2-pod-place">
                    <span class="rk2-pod-medal">{{ $item['medal'] }}</span>
                    {{ $item['place'] }}
                  </span>
                  <span class="rk2-pod-score"><b>{{ number_format($d->score, 0) }}</b> pts</span>
                </div>
                <div class="rk2-pod-identity">
                  <span class="rk2-pod-avatar">
                    @if($d->user->avatar_path ?? false)
                      <img src="{{ \Illuminate\Support\Facades\Storage::url($d->user->avatar_path) }}" alt="{{ $d->user->name }}">
                    @else
                      {{ $initials($d->user->name) }}
                    @endif
                  </span>
                  <div>
                    <div class="rk2-pod-name">{{ $d->user->name }}</div>
                    <div class="rk2-pod-sub">
                      {{ $sportLabels[$d->sport] ?? ucfirst($d->sport) }}
                      @if($d->category) · {{ $categoryMap[$d->category]['label'] ?? ucfirst($d->category) }} @endif
                      · {{ $d->games_played }} PJ
                    </div>
                  </div>
                </div>
                <div class="rk2-pod-stats">
                  <div>
                    <div class="rk2-pod-stat-k">Win rate</div>
                    <div class="rk2-pod-stat-v">{{ $d->win_rate }}%</div>
                  </div>
                  <div>
                    <div class="rk2-pod-stat-k">Rating</div>
                    <div class="rk2-pod-stat-v">{{ number_format($d->rating, 1) }}</div>
                  </div>
                  <div>
                    <div class="rk2-pod-stat-k">PJ</div>
                    <div class="rk2-pod-stat-v">{{ $d->games_played }}</div>
                  </div>
                </div>
              </a>
            @endforeach
          </section>
        @endif

        {{-- ─── FILTERS ─── --}}
        <form method="GET" action="{{ route('ranking.index') }}" class="rk2-filters" id="rk2Filters">
          @if($sportFilter)
            <input type="hidden" name="sport" value="{{ $sportFilter }}">
          @endif

          <div class="rk2-f-search">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="text" name="q" value="{{ $searchQuery }}" placeholder="Buscar jugador por nombre…" autocomplete="off">
            <button type="submit" class="rk2-f-search-btn">Buscar</button>
          </div>

          @if($sportFilter && count($availableCategories) > 0)
            <div class="rk2-f-dd {{ $categoryFilter ? 'has-value' : '' }}">
              <span class="rk2-f-dd-label">Nivel</span>
              <span class="rk2-f-dd-val">{{ $categoryFilter ? ($categoryMap[$categoryFilter]['label'] ?? ucfirst($categoryFilter)) : 'Todos' }}</span>
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
              <select name="category" onchange="this.form.submit()">
                <option value="">Todos</option>
                @foreach($availableCategories as $cat)
                  <option value="{{ $cat }}" {{ $categoryFilter === $cat ? 'selected' : '' }}>{{ $categoryMap[$cat]['label'] ?? ucfirst($cat) }}</option>
                @endforeach
              </select>
            </div>
          @endif

          @if($searchQuery || $categoryFilter)
            <a href="{{ route('ranking.index', $sportFilter ? ['sport' => $sportFilter] : []) }}" class="rk2-f-clear">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" style="margin-right:4px;"><path d="M18 6 6 18M6 6l12 12"/></svg>
              Limpiar
            </a>
          @endif
        </form>

        {{-- ─── LEADERBOARD ─── --}}
        @if($players->isEmpty())
          <div class="rk2-empty">
            <div class="rk2-empty-ico">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            </div>
            <h4>No encontramos jugadores</h4>
            <p>
              @if($searchQuery || $categoryFilter)
                Probá ajustando los filtros o limpiando la búsqueda.
              @else
                Todavía no hay jugadores rankeados en {{ $sportFilter ? strtolower($sportLabels[$sportFilter]) : 'este ranking' }}.
              @endif
            </p>
          </div>
        @else
          <section class="rk2-lb">
            <div class="rk2-lb-head">
              <span>Pos.</span>
              <span>Jugador</span>
              <span>Nivel</span>
              <span class="num">PJ</span>
              <span>Record</span>
              <span>Win rate</span>
              <span class="num">Rating</span>
              <span>Medallas</span>
              <span class="num">Score</span>
              <span></span>
            </div>

            @foreach($players as $i => $p)
              @php
                $globalPos  = ($players->currentPage() - 1) * $players->perPage() + $i + 1;
                $posClass   = $globalPos === 1 ? 'p1' : ($globalPos === 2 ? 'p2' : ($globalPos === 3 ? 'p3' : ''));
                $isYou      = auth()->check() && $p->user->id === auth()->id();
                $catInfo    = $categoryMap[$p->category] ?? ['label' => ucfirst($p->category ?? '—'), 'cls' => ''];
              @endphp
              <a class="rk2-lb-row {{ $isYou ? 'you' : '' }}" href="{{ route('sport-profile.public', $p->user) }}">
                <span class="rk2-lb-pos-num {{ $posClass }}">{{ $globalPos }}</span>

                <div class="rk2-lb-player">
                  <span class="rk2-lb-avatar {{ $posClass }}">
                    @if($p->user->avatar_path ?? false)
                      <img src="{{ \Illuminate\Support\Facades\Storage::url($p->user->avatar_path) }}" alt="{{ $p->user->name }}">
                    @else
                      {{ $initials($p->user->name) }}
                    @endif
                  </span>
                  <div class="rk2-lb-player-info">
                    <div class="rk2-lb-name">{{ $p->user->name }}{{ $isYou ? ' · vos' : '' }}</div>
                    <div class="rk2-lb-sub">
                      {{ $sportLabels[$p->sport] ?? ucfirst($p->sport) }}
                      @if(!$isYou && !empty($p->badges))<span class="dot"></span><span>{{ count($p->badges) }} medallas</span>@endif
                    </div>
                  </div>
                </div>

                <span class="rk2-level-chip {{ $catInfo['cls'] }}">{{ $catInfo['label'] }}</span>

                <span class="rk2-lb-pj">{{ $p->games_played }}</span>

                <span class="rk2-lb-record">
                  <b>{{ $p->wins }}</b>·<span>{{ $p->draws }}</span>·<span>{{ $p->losses }}</span>
                </span>

                <div class="rk2-lb-winrate">
                  <div class="rk2-lb-winrate-bar">
                    <div class="rk2-lb-winrate-fill" style="width:{{ $p->win_rate }}%"></div>
                  </div>
                  <span class="rk2-lb-winrate-pct">{{ $p->win_rate }}%</span>
                </div>

                <span class="rk2-lb-rating">
                  <span class="rk2-lb-rating-stars">★ {{ number_format($p->rating, 1) }}</span>
                </span>

                <div class="rk2-lb-badges">
                  @foreach(array_slice($p->badges, 0, 2) as $b)
                    {!! $renderBadge($b) !!}
                  @endforeach
                  @if(count($p->badges) > 2)
                    <span class="rk2-badge badge-more">+{{ count($p->badges) - 2 }}</span>
                  @endif
                </div>

                <span class="rk2-lb-score">{{ number_format($p->score, 0) }}</span>

                <span class="rk2-lb-action">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </span>
              </a>
            @endforeach

            {{-- Pagination --}}
            @if($players->hasPages())
              <div class="rk2-pager">
                <div class="rk2-pager-info">
                  Mostrando <b>{{ $players->firstItem() }}–{{ $players->lastItem() }}</b> de <b>{{ $players->total() }}</b>
                </div>
                <div class="rk2-pager-nav">
                  @if($players->onFirstPage())
                    <button class="rk2-pager-btn" disabled>‹</button>
                  @else
                    <a class="rk2-pager-btn" href="{{ $players->previousPageUrl() }}">‹</a>
                  @endif

                  @foreach($players->getUrlRange(max(1, $players->currentPage() - 2), min($players->lastPage(), $players->currentPage() + 2)) as $num => $url)
                    <a class="rk2-pager-btn {{ $num == $players->currentPage() ? 'active' : '' }}" href="{{ $url }}">{{ $num }}</a>
                  @endforeach

                  @if($players->hasMorePages())
                    <a class="rk2-pager-btn" href="{{ $players->nextPageUrl() }}">›</a>
                  @else
                    <button class="rk2-pager-btn" disabled>›</button>
                  @endif
                </div>
              </div>
            @endif
          </section>
        @endif

        {{-- ─── NOTE ─── --}}
        <div class="rk2-note">
          <div class="rk2-note-ico">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          </div>
          <div>
            <h5>¿Cómo se calcula el ranking?</h5>
            <p>
              El <strong>Score</strong> combina tres factores: <strong>resultados</strong> (3 puntos por victoria, 1 por empate), <strong>asistencia</strong> (hasta +10 por no faltar) y <strong>rating</strong> (hasta +10 por buenas calificaciones de compañeros). Se necesita un mínimo de <strong>3 partidos confirmados</strong> para aparecer en el ranking público.
            </p>
          </div>
        </div>

      </div>
    </div>

  </main>
</div>
@endsection
