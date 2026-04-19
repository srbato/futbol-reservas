@extends('layouts.app')

@section('title', 'Complejos · TuCancha')
@section('meta_description', 'Explorá todos los complejos deportivos disponibles en TuCancha. Filtrá por zona, deporte y horario. Reservá online en segundos.')
@section('og_title', 'Complejos — TuCancha')
@section('og_description', 'Encontrá canchas de fútbol, pádel, tenis y más. Reservá online en segundos.')

@push('styles')
<style>
  /* ═══════════════════════════════════════════════════════════════
     VENUES INDEX V2 — Editorial Dark Listing
     Palette: TuCancha #22c55e · Font: Sora bold (from layout)
     Scoped with .vi2 prefix to avoid collisions
     ═══════════════════════════════════════════════════════════════ */
  .vi2 {
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
    --accent-dim: rgba(34,197,94,.08);
    --accent-bd: rgba(34,197,94,.25);
    --gold: #d4b878;
  }
  .vi2 { background: var(--bg); color: var(--tx); font-family: 'Sora', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
  .vi2 * { box-sizing: border-box; }
  .vi2 a { color: inherit; text-decoration: none; }
  .vi2 button, .vi2 select, .vi2 input { font-family: inherit; }
  .vi2 ::selection { background: var(--accent); color: var(--accent-ink); }

  /* Break out of layout's .site-main constraints */
  .vi2 {
    margin-inline: calc(50% - 50vw);
    width: 100vw;
    max-width: 100vw;
    overflow-x: clip;
    margin-top: -24px;
    margin-bottom: -40px;
  }
  @media (max-width: 639px) {
    .vi2 { margin-top: -16px; margin-bottom: -32px; }
  }

  /* ── Scroll reveal ── */
  .vi2-rv { opacity: 0; transform: translateY(14px); transition: opacity .7s cubic-bezier(.2,.6,.2,1), transform .7s cubic-bezier(.2,.6,.2,1); }
  .vi2-rv.in { opacity: 1; transform: translateY(0); }
  .vi2-rv.d1 { transition-delay: .06s; }
  .vi2-rv.d2 { transition-delay: .12s; }
  .vi2-rv.d3 { transition-delay: .18s; }
  .vi2-rv.d4 { transition-delay: .24s; }

  /* ── HERO ── */
  .vi2-hero {
    position: relative;
    padding: 140px 40px 80px;
    min-height: 620px;
    overflow: hidden;
  }
  .vi2-hero-bg {
    position: absolute; inset: 0;
    background: url('/images/hero-cancha.webp') center 40% / cover no-repeat;
    opacity: .35;
    animation: vi2-kenburns 22s ease-in-out infinite alternate;
  }
  @keyframes vi2-kenburns {
    0% { transform: scale(1.04) translate(0, 0); }
    100% { transform: scale(1.10) translate(-1.5%, -1%); }
  }
  .vi2-hero-grad {
    position: absolute; inset: 0;
    background:
      radial-gradient(ellipse 70% 50% at 30% 100%, rgba(0,0,0,.8), transparent 65%),
      linear-gradient(180deg, rgba(5,5,5,.75) 0%, rgba(5,5,5,.4) 35%, rgba(5,5,5,.92) 100%);
  }
  .vi2-hero-inner { position: relative; z-index: 2; max-width: 1360px; margin: 0 auto; }
  .vi2-eyebrow {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase;
    color: var(--tx-3); margin-bottom: 16px;
  }
  .vi2-eyebrow::before { content: ''; display: inline-block; width: 20px; height: 1px; background: var(--tx-3); }
  .vi2-hero-h {
    font-size: clamp(44px, 6.5vw, 80px);
    font-weight: 800;
    letter-spacing: -0.045em;
    line-height: .98;
    margin: 0 0 18px;
    color: var(--tx);
  }
  .vi2-hero-h b { font-weight: 900; }
  .vi2-hero-h i { font-style: italic; font-weight: 700; color: var(--accent); letter-spacing: -0.045em; }
  .vi2-hero-sub {
    font-size: 18px; font-weight: 500;
    color: var(--tx-2); line-height: 1.55;
    max-width: 560px; margin: 0 0 28px;
  }
  .vi2-hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 6px 14px 6px 10px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 999px;
    font-size: 12px; font-weight: 600; color: var(--tx);
    margin-bottom: 18px;
    backdrop-filter: blur(12px);
  }
  .vi2-hero-badge-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--accent);
    box-shadow: 0 0 0 3px rgba(34,197,94,.25);
    animation: vi2-pulse 1.6s ease-in-out infinite;
  }
  @keyframes vi2-pulse { 0%,100% { opacity: 1; } 50% { opacity: .5; } }

  /* ── Search bar ── */
  .vi2-search {
    display: flex; gap: 1px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 16px;
    padding: 5px;
    backdrop-filter: blur(16px);
    max-width: 720px;
    align-items: center;
    transition: border-color .2s, box-shadow .2s;
    margin-top: 16px;
  }
  .vi2-search:focus-within {
    border-color: rgba(34,197,94,.4);
    box-shadow: 0 0 0 3px rgba(34,197,94,.08);
  }
  .vi2-search-ico { padding: 0 12px 0 16px; display: flex; align-items: center; color: var(--tx-3); }
  .vi2-search-input {
    flex: 1; padding: 13px 8px;
    background: none; border: none;
    color: var(--tx); font-size: 15px; font-weight: 500;
    outline: none; min-width: 0;
  }
  .vi2-search-input::placeholder { color: var(--tx-4); }
  .vi2-search-btn {
    padding: 12px 22px;
    background: var(--accent); color: var(--accent-ink);
    border: none; border-radius: 12px;
    font-size: 14px; font-weight: 800;
    cursor: pointer;
    transition: background .15s, transform .15s;
    white-space: nowrap;
  }
  .vi2-search-btn:hover { background: var(--accent-hover); transform: translateY(-1px); }

  /* ── Filter chips ── */
  .vi2-filters {
    display: flex; gap: 8px; flex-wrap: wrap;
    margin-top: 16px; align-items: center;
  }
  .vi2-chip {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--bd-2);
    border-radius: 999px;
    font-size: 12px; font-weight: 600;
    color: var(--tx-2);
    cursor: pointer;
    transition: background .15s, border-color .15s, color .15s, transform .15s;
    position: relative;
    white-space: nowrap;
  }
  .vi2-chip:hover {
    background: rgba(255,255,255,.08);
    border-color: rgba(255,255,255,.2);
    color: var(--tx);
    transform: translateY(-1px);
  }
  .vi2-chip.active {
    background: var(--accent-dim);
    border-color: var(--accent-bd);
    color: var(--accent);
  }
  .vi2-chip svg { width: 13px; height: 13px; }
  .vi2-chip select,
  .vi2-chip input[type="date"],
  .vi2-chip input[type="time"] {
    position: absolute; inset: 0;
    opacity: 0; width: 100%; height: 100%;
    cursor: pointer;
    font-family: inherit;
    background: transparent;
    border: none;
    color: transparent;
  }
  .vi2-filter-sep { width: 1px; height: 20px; background: rgba(255,255,255,.12); }
  .vi2-chip-clear {
    background: none; border: none;
    color: var(--tx-3); font-size: 12px;
    cursor: pointer; padding: 8px 10px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 4px;
  }
  .vi2-chip-clear:hover { color: var(--tx); }
  .vi2-chip-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--accent);
    margin-left: 2px;
  }
  .vi2-chip-disabled { opacity: .4; cursor: not-allowed; }
  .vi2-chip-disabled * { pointer-events: none; }

  /* ── Advanced filter panel ── */
  .vi2-adv {
    max-width: 720px; margin-top: 12px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    border-radius: 16px;
    padding: 0 20px;
    max-height: 0; overflow: hidden;
    transition: max-height .35s cubic-bezier(.2,.6,.2,1), padding .3s, margin-top .3s;
  }
  .vi2-adv.open { max-height: 280px; padding: 20px; margin-top: 14px; }
  .vi2-adv-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 14px;
    margin-bottom: 14px;
  }
  .vi2-adv label {
    display: block;
    font-size: 11px; font-weight: 700; color: var(--tx-3);
    letter-spacing: .08em; text-transform: uppercase;
    margin-bottom: 6px;
  }
  .vi2-adv input[type="number"] {
    width: 100%;
    padding: 10px 14px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd-2);
    border-radius: 10px;
    color: var(--tx);
    font-size: 14px; font-weight: 600;
    outline: none;
    transition: border-color .15s;
  }
  .vi2-adv input[type="number"]:focus { border-color: var(--accent); }
  .vi2-adv-actions { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
  .vi2-btn-apply {
    padding: 9px 20px;
    background: var(--accent); color: var(--accent-ink);
    border: none; border-radius: 10px;
    font-size: 13px; font-weight: 800;
    cursor: pointer;
    transition: background .15s;
  }
  .vi2-btn-apply:hover { background: var(--accent-hover); }
  .vi2-btn-geo {
    padding: 9px 16px;
    background: transparent;
    border: 1px solid var(--bd-2);
    color: var(--tx-2);
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    cursor: pointer;
    display: inline-flex; align-items: center; gap: 6px;
    transition: border-color .15s, color .15s, background .15s;
  }
  .vi2-btn-geo:hover { border-color: var(--tx-3); color: var(--tx); }
  .vi2-btn-geo.on {
    background: var(--accent-dim); color: var(--accent);
    border-color: var(--accent-bd);
  }

  /* ── Active filter tags ── */
  .vi2-active-tags {
    max-width: 1360px; margin: 0 auto; padding: 20px 40px 0;
    display: flex; gap: 8px; flex-wrap: wrap; align-items: center;
  }
  .vi2-active-tags-label {
    font-size: 12px; font-weight: 700; color: var(--tx-3);
    letter-spacing: .06em; text-transform: uppercase;
  }
  .vi2-active-tag {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px;
    background: var(--accent-dim);
    border: 1px solid var(--accent-bd);
    color: var(--accent);
    border-radius: 999px;
    font-size: 12px; font-weight: 600;
  }
  .vi2-active-tag svg { width: 11px; height: 11px; }

  /* ── Availability banner ── */
  .vi2-avail-banner {
    max-width: 1360px; margin: 14px auto 0; padding: 0 40px;
  }
  .vi2-avail-banner-inner {
    background: var(--accent-dim);
    border: 1px solid var(--accent-bd);
    border-radius: 14px;
    padding: 12px 18px;
    display: flex; align-items: center; gap: 10px;
    color: var(--accent);
    font-size: 13px; font-weight: 600;
  }
  .vi2-avail-banner-inner strong { color: var(--tx); font-weight: 800; }

  /* ── Falta Uno banner (when ?falta_uno=1) ── */
  .vi2-falta-banner {
    max-width: 1360px; margin: 14px auto 0; padding: 0 40px;
  }
  .vi2-falta-banner-inner {
    background: var(--accent-dim);
    border: 1px solid var(--accent-bd);
    border-radius: 14px;
    padding: 14px 20px;
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
  }
  .vi2-falta-banner-ico {
    width: 38px; height: 38px; border-radius: 10px;
    background: rgba(34,197,94,.18);
    display: flex; align-items: center; justify-content: center;
    color: var(--accent); flex-shrink: 0;
  }
  .vi2-falta-banner-text { flex: 1; min-width: 220px; }
  .vi2-falta-banner-text b { display: block; color: var(--accent); font-size: 14px; font-weight: 800; }
  .vi2-falta-banner-text span { color: var(--tx-2); font-size: 12px; }
  .vi2-falta-banner-link {
    color: var(--accent); font-weight: 700; font-size: 13px;
    text-decoration: underline;
    display: inline-flex; align-items: center; gap: 4px;
  }

  /* ── Sections ── */
  .vi2-sec-w { max-width: 1360px; margin: 0 auto; padding: 0 40px; }
  .vi2-sec-head {
    display: flex; justify-content: space-between; align-items: flex-end;
    gap: 20px; flex-wrap: wrap;
    margin-bottom: 28px;
    padding-top: 64px;
  }
  .vi2-sec-title {
    font-size: 28px; font-weight: 700;
    letter-spacing: -0.03em; margin: 0;
    color: var(--tx);
  }
  .vi2-sec-title b { font-weight: 900; }
  .vi2-sec-sub { font-size: 13px; color: var(--tx-3); margin: 4px 0 0; font-weight: 500; }
  .vi2-sec-count { font-size: 12px; color: var(--tx-3); font-weight: 600; }

  /* ── Featured tabs ── */
  .vi2-feat-tabs {
    display: flex; gap: 4px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    border-radius: 12px;
    padding: 3px;
  }
  .vi2-feat-tab {
    padding: 8px 16px;
    font-size: 12px; font-weight: 600;
    color: var(--tx-3);
    border-radius: 10px;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex; align-items: center; gap: 6px;
    white-space: nowrap;
    border: none; background: none;
  }
  .vi2-feat-tab:hover { color: var(--tx-2); }
  .vi2-feat-tab.active {
    background: rgba(255,255,255,.08);
    color: var(--tx);
  }
  .vi2-feat-tab svg { width: 12px; height: 12px; }

  /* ── Featured carousel ── */
  .vi2-feat-track-wrap {
    position: relative;
    overflow: hidden;
    padding-bottom: 8px;
  }
  .vi2-feat-track {
    display: flex; gap: 16px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
    -ms-overflow-style: none;
    padding: 8px 0 12px;
  }
  .vi2-feat-track::-webkit-scrollbar { display: none; }
  .vi2-feat-pane { display: none; }
  .vi2-feat-pane.active { display: block; }

  .vi2-feat-card {
    flex: 0 0 320px;
    scroll-snap-align: start;
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    aspect-ratio: 4/3;
    cursor: pointer;
    border: 1px solid var(--bd);
    transition: border-color .3s, transform .3s;
    text-decoration: none;
  }
  .vi2-feat-card:hover { border-color: var(--accent-bd); transform: translateY(-3px); }
  .vi2-feat-img {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    transition: transform .5s, filter .3s;
    filter: brightness(.55);
  }
  .vi2-feat-img-placeholder {
    position: absolute; inset: 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.2);
  }
  .vi2-feat-card:hover .vi2-feat-img { transform: scale(1.05); filter: brightness(.7); }
  .vi2-feat-img::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(0deg, rgba(5,5,5,.92) 0%, rgba(5,5,5,.3) 50%, transparent);
  }
  .vi2-feat-body {
    position: relative; z-index: 2;
    height: 100%;
    display: flex; flex-direction: column; justify-content: flex-end;
    padding: 22px;
  }
  .vi2-feat-name {
    font-size: 18px; font-weight: 800;
    letter-spacing: -0.02em;
    color: var(--tx);
    margin: 0 0 8px;
  }
  .vi2-feat-meta {
    display: flex; gap: 10px;
    font-size: 12px; color: var(--tx-2);
    flex-wrap: wrap; align-items: center;
    margin-bottom: 14px;
    font-weight: 500;
  }
  .vi2-feat-meta svg { width: 12px; height: 12px; }
  .vi2-feat-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px;
    background: rgba(255,255,255,.08);
    border-radius: 999px;
    font-size: 11px;
    backdrop-filter: blur(6px);
  }
  .vi2-feat-cta {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 16px;
    background: var(--accent); color: var(--accent-ink);
    border-radius: 999px;
    font-size: 12px; font-weight: 800;
    width: fit-content;
    transition: background .15s;
  }
  .vi2-feat-cta:hover { background: var(--accent-hover); }

  .vi2-feat-empty {
    padding: 32px; text-align: center; width: 100%;
    border: 1px dashed var(--bd-2);
    border-radius: 16px;
    color: var(--tx-3);
  }
  .vi2-feat-empty svg { margin-bottom: 10px; color: var(--tx-4); }
  .vi2-feat-empty b { display: block; font-weight: 700; color: var(--tx-2); font-size: 14px; margin-bottom: 4px; }
  .vi2-feat-empty span { font-size: 13px; color: var(--tx-3); }

  /* ── Venues grid ── */
  .vi2-venues-grid {
    display: grid; grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    padding-bottom: 40px;
  }

  .vi2-card {
    border: 1px solid var(--bd);
    border-radius: 18px;
    overflow: hidden;
    background: var(--bg-1);
    transition: border-color .3s, transform .3s, box-shadow .3s;
    display: block;
  }
  .vi2-card:hover {
    border-color: rgba(255,255,255,.15);
    transform: translateY(-3px);
    box-shadow: 0 12px 40px rgba(0,0,0,.4);
  }
  .vi2-card.pro { border-color: rgba(212,184,120,.16); }
  .vi2-card.pro:hover { border-color: rgba(212,184,120,.36); }
  .vi2-card.full { border-color: rgba(34,197,94,.18); }
  .vi2-card.full:hover { border-color: rgba(34,197,94,.38); }

  .vi2-card-img {
    position: relative;
    aspect-ratio: 16/10;
    overflow: hidden;
    background: var(--bg-2);
    display: block;
  }
  .vi2-card-img img {
    position: absolute; inset: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s;
  }
  .vi2-card:hover .vi2-card-img img { transform: scale(1.05); }
  .vi2-card-img-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--tx-4);
    background: linear-gradient(135deg, #141414, #0a0a0a);
  }

  .vi2-badges {
    position: absolute; top: 12px; left: 12px;
    display: flex; gap: 6px; flex-wrap: wrap;
    max-width: calc(100% - 80px);
  }
  .vi2-badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 10px; font-weight: 700;
    letter-spacing: .04em;
    backdrop-filter: blur(12px);
    display: inline-flex; align-items: center; gap: 4px;
  }
  .vi2-badge svg { width: 10px; height: 10px; }
  .vi2-badge-zone {
    background: rgba(0,0,0,.6);
    color: var(--tx-2);
    border: 1px solid rgba(255,255,255,.1);
  }
  .vi2-badge-falta {
    background: rgba(34,197,94,.15);
    color: var(--accent);
    border: 1px solid rgba(34,197,94,.3);
  }
  .vi2-falta-dot {
    width: 5px; height: 5px; border-radius: 50%;
    background: var(--accent);
    animation: vi2-pulse 1.4s ease-in-out infinite;
  }

  .vi2-card-plan { position: absolute; top: 12px; right: 54px; }
  .vi2-badge-pro {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 10px; font-weight: 700;
    background: rgba(212,184,120,.15);
    color: #d4b878;
    border: 1px solid rgba(212,184,120,.3);
    display: inline-flex; align-items: center; gap: 4px;
    backdrop-filter: blur(12px);
  }
  .vi2-badge-pro svg { width: 10px; height: 10px; }
  .vi2-badge-full {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 10px; font-weight: 700;
    background: rgba(34,197,94,.15);
    color: var(--accent);
    border: 1px solid rgba(34,197,94,.3);
    display: inline-flex; align-items: center; gap: 4px;
    backdrop-filter: blur(12px);
  }

  .vi2-card-fav {
    position: absolute; top: 12px; right: 12px;
    width: 34px; height: 34px;
    border-radius: 50%;
    background: rgba(0,0,0,.55);
    border: 1px solid rgba(255,255,255,.1);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
    transition: background .15s, transform .15s;
    padding: 0;
  }
  .vi2-card-fav:hover { background: rgba(0,0,0,.85); transform: scale(1.08); }
  .vi2-card-fav svg { width: 14px; height: 14px; stroke: var(--tx-3); fill: none; stroke-width: 2; transition: stroke .15s, fill .15s; }
  .vi2-card-fav.saved svg { stroke: #ef4444; fill: #ef4444; }

  .vi2-card-body { padding: 18px 20px 20px; }
  .vi2-card-name {
    font-size: 17px; font-weight: 800;
    letter-spacing: -0.015em;
    color: var(--tx);
    margin: 0 0 8px;
    white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
  }
  .vi2-card-rating {
    display: flex; align-items: center; gap: 6px;
    margin-bottom: 10px;
  }
  .vi2-card-stars { color: var(--accent); font-size: 12px; letter-spacing: 1px; }
  .vi2-card-rating-num { font-size: 13px; font-weight: 800; color: var(--tx); }
  .vi2-card-rating-count { font-size: 12px; color: var(--tx-3); font-weight: 500; }
  .vi2-card-no-reviews { font-size: 12px; color: var(--tx-4); margin-bottom: 10px; font-weight: 500; }
  .vi2-card-desc {
    font-size: 13px; color: var(--tx-3);
    line-height: 1.55; font-weight: 400;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin: 0 0 16px;
    min-height: 40px;
  }
  .vi2-card-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
  .vi2-card-tag {
    padding: 4px 10px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    border-radius: 999px;
    font-size: 11px;
    color: var(--tx-3);
    font-weight: 600;
  }
  .vi2-card-cta {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    width: 100%;
    padding: 10px 16px;
    background: var(--accent); color: var(--accent-ink);
    border: none; border-radius: 12px;
    font-size: 13px; font-weight: 800;
    cursor: pointer;
    transition: background .15s, transform .15s;
    text-decoration: none;
  }
  .vi2-card-cta:hover { background: var(--accent-hover); transform: translateY(-1px); color: var(--accent-ink); }
  .vi2-card-cta svg { width: 13px; height: 13px; }

  /* ── Empty state ── */
  .vi2-empty {
    padding: 80px 40px;
    text-align: center;
    border: 1px dashed var(--bd-2);
    border-radius: 20px;
    margin-bottom: 40px;
  }
  .vi2-empty-ico {
    width: 64px; height: 64px; margin: 0 auto 16px;
    border-radius: 18px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    display: flex; align-items: center; justify-content: center;
    color: var(--tx-4);
  }
  .vi2-empty h4 { font-size: 18px; font-weight: 800; margin: 0 0 6px; color: var(--tx); }
  .vi2-empty p { font-size: 13px; color: var(--tx-3); font-weight: 500; margin: 0 0 18px; }
  .vi2-empty-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 20px;
    background: var(--accent); color: var(--accent-ink);
    border-radius: 12px;
    font-size: 13px; font-weight: 800;
    text-decoration: none;
  }
  .vi2-empty-btn:hover { background: var(--accent-hover); }

  /* ── Map ── */
  .vi2-map-sec { padding-bottom: 80px; }
  .vi2-map-wrap {
    position: relative;
    border: 1px solid var(--bd);
    border-radius: 20px;
    overflow: hidden;
    background: var(--bg-1);
  }
  #vi2Map { height: 440px; display: none; }
  .vi2-map-skeleton {
    height: 440px;
    display: flex; align-items: center; justify-content: center;
    color: var(--tx-4);
    background:
      radial-gradient(circle at 30% 40%, rgba(34,197,94,.04), transparent 60%),
      linear-gradient(135deg, #0e1412 0%, #0a0f0a 100%);
  }

  /* ── Responsive ── */
  @media (max-width: 1024px) {
    .vi2-venues-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 720px) {
    .vi2-hero { padding: 120px 20px 56px; min-height: 540px; }
    .vi2-sec-w { padding: 0 20px; }
    .vi2-sec-head { padding-top: 48px; margin-bottom: 20px; }
    .vi2-venues-grid { grid-template-columns: 1fr; }
    .vi2-feat-card { flex: 0 0 280px; }
    .vi2-active-tags { padding: 20px 20px 0; }
    .vi2-avail-banner, .vi2-falta-banner { padding: 0 20px; }
    .vi2-adv-grid { grid-template-columns: 1fr; }
    .vi2-adv.open { max-height: 400px; }
    .vi2-search-btn { padding: 10px 18px; font-size: 13px; }
  }
</style>
@endpush

@section('content')
<div class="vi2">

{{-- ═══════════════════════════════════════════════════════
     HERO + SEARCH FORM
     ═══════════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('venues.index') }}" id="vi2Form">
  <input type="hidden" name="user_lat" id="vi2UserLat" value="{{ $userLat ?? '' }}">
  <input type="hidden" name="user_lng" id="vi2UserLng" value="{{ $userLng ?? '' }}">

  <section class="vi2-hero">
    <div class="vi2-hero-bg"></div>
    <div class="vi2-hero-grad"></div>

    <div class="vi2-hero-inner">
      <span class="vi2-hero-badge vi2-rv">
        <span class="vi2-hero-badge-dot"></span>
        {{ $allVenues->count() }} {{ $allVenues->count() === 1 ? 'complejo disponible' : 'complejos disponibles' }}
      </span>

      <div class="vi2-eyebrow vi2-rv">Explorar complejos</div>
      <h1 class="vi2-hero-h vi2-rv d1">Encontrá <i>tu cancha</i></h1>
      <p class="vi2-hero-sub vi2-rv d2">Filtrá por zona, deporte, fecha y precio. Reservá online en segundos, sin llamar.</p>

      {{-- Search bar --}}
      <div class="vi2-search vi2-rv d3">
        <span class="vi2-search-ico">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        </span>
        <input class="vi2-search-input" name="q" value="{{ $q ?? '' }}" placeholder="Buscá por nombre, zona o descripción…" autocomplete="off">
        <button type="submit" class="vi2-search-btn">Buscar</button>
      </div>

      {{-- Filter chips --}}
      <div class="vi2-filters vi2-rv d4">
        {{-- Zona --}}
        <div class="vi2-chip {{ ($zone ?? '') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          {{ ($zone ?? '') ?: 'Zona' }}
          <select name="zone" onchange="document.getElementById('vi2Form').submit()">
            <option value="">Todas las zonas</option>
            @foreach($zones as $z)
              <option value="{{ $z }}" {{ ($zone ?? '') === $z ? 'selected' : '' }}>{{ $z }}</option>
            @endforeach
          </select>
        </div>

        {{-- Deporte --}}
        <div class="vi2-chip {{ ($sport ?? '') ? 'active' : '' }}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M2 12h20"/></svg>
          {{ match($sport ?? '') { 'football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis', 'basketball' => 'Básquet', 'volleyball' => 'Vóley', default => 'Deporte' } }}
          <select name="sport" onchange="document.getElementById('vi2Form').submit()">
            <option value="">Todos los deportes</option>
            <option value="football" {{ ($sport ?? '') === 'football' ? 'selected' : '' }}>Fútbol</option>
            <option value="padel" {{ ($sport ?? '') === 'padel' ? 'selected' : '' }}>Pádel</option>
            <option value="tennis" {{ ($sport ?? '') === 'tennis' ? 'selected' : '' }}>Tenis</option>
            <option value="basketball" {{ ($sport ?? '') === 'basketball' ? 'selected' : '' }}>Básquet</option>
            <option value="volleyball" {{ ($sport ?? '') === 'volleyball' ? 'selected' : '' }}>Vóley</option>
          </select>
        </div>

        {{-- Fecha --}}
        <div class="vi2-chip {{ ($date ?? '') ? 'active' : '' }}" role="button" tabindex="0" onclick="vi2OpenDate()" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();vi2OpenDate();}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          {{ ($date ?? '') ? \Carbon\Carbon::parse($date)->format('d/m') : 'Fecha' }}
          <input type="date" id="vi2DateInput" name="date" value="{{ $date ?? '' }}" min="{{ date('Y-m-d') }}" onchange="document.getElementById('vi2Form').submit()">
        </div>

        {{-- Horario (requires date) --}}
        <div class="vi2-chip {{ ($availableAt ?? '') ? 'active' : '' }} {{ !($date ?? '') ? 'vi2-chip-disabled' : '' }}" role="button" tabindex="0" onclick="if(document.getElementById('vi2DateInput').value) vi2OpenTime()" onkeydown="if((event.key==='Enter'||event.key===' ')&&document.getElementById('vi2DateInput').value){event.preventDefault();vi2OpenTime();}">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          {{ ($availableAt ?? '') ?: 'Horario' }}
          <input type="time" id="vi2TimeInput" name="available_at" value="{{ $availableAt ?? '' }}" {{ !($date ?? '') ? 'disabled' : '' }} onchange="document.getElementById('vi2Form').submit()">
        </div>

        <div class="vi2-filter-sep"></div>

        {{-- Más filtros --}}
        <button type="button" class="vi2-chip" id="vi2AdvToggle" onclick="vi2ToggleAdv()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 21V14M4 10V3M12 21V12M12 8V3M20 21V16M20 12V3M1 14h6M9 8h6M17 16h6"/></svg>
          Más filtros
          @if(($minPrice ?? '') || ($maxPrice ?? '') || ($sortByDistance ?? false))
            <span class="vi2-chip-dot"></span>
          @endif
        </button>

        {{-- Clear filters --}}
        @if(($q ?? '') || ($zone ?? '') || ($sport ?? '') || ($date ?? '') || ($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? ''))
          <a href="{{ route('venues.index') }}" class="vi2-chip-clear">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6 6 18M6 6l12 12"/></svg>
            Limpiar
          </a>
        @endif
      </div>

      {{-- Advanced panel --}}
      <div class="vi2-adv {{ (($minPrice ?? '') || ($maxPrice ?? '')) ? 'open' : '' }}" id="vi2AdvPanel">
        <div class="vi2-adv-grid">
          <div>
            <label>Precio mínimo (ARS)</label>
            <input type="number" name="min_price" min="0" step="1" value="{{ $minPrice ?? '' }}" placeholder="Ej: 5000">
          </div>
          <div>
            <label>Precio máximo (ARS)</label>
            <input type="number" name="max_price" min="0" step="1" value="{{ $maxPrice ?? '' }}" placeholder="Ej: 20000">
          </div>
        </div>
        <div class="vi2-adv-actions">
          <button type="submit" class="vi2-btn-apply">Aplicar filtros</button>
          <button type="button" id="vi2GeoBtn" onclick="vi2RequestGeo()" class="vi2-btn-geo {{ ($sortByDistance ?? false) ? 'on' : '' }}">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
            {{ ($sortByDistance ?? false) ? 'Ordenando por cercanía' : 'Ordenar por cercanía' }}
          </button>
          @if($sortByDistance ?? false)
            <a href="{{ request()->fullUrlWithQuery(['user_lat' => '', 'user_lng' => '']) }}" style="font-size:12px; color:var(--tx-3); text-decoration:underline;">Quitar</a>
          @endif
        </div>
      </div>
    </div>
  </section>
</form>

{{-- ═══════════════════════════════════════════════════════
     ACTIVE FILTER TAGS
     ═══════════════════════════════════════════════════════ --}}
@if(($q ?? '') || ($zone ?? '') || ($sport ?? '') || ($date ?? '') || ($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? ''))
  <div class="vi2-active-tags">
    <span class="vi2-active-tags-label">Filtros activos</span>
    @if($q ?? '')
      <span class="vi2-active-tag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        "{{ $q }}"
      </span>
    @endif
    @if($zone ?? '')
      <span class="vi2-active-tag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
        {{ $zone }}
      </span>
    @endif
    @if($sport ?? '')
      <span class="vi2-active-tag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M2 12h20"/></svg>
        {{ match($sport) { 'football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis', 'basketball' => 'Básquet', 'volleyball' => 'Vóley', default => $sport } }}
      </span>
    @endif
    @if($date ?? '')
      <span class="vi2-active-tag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
      </span>
    @endif
    @if($availableAt ?? '')
      <span class="vi2-active-tag">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        {{ $availableAt }}
      </span>
    @endif
    @if($minPrice ?? '')
      <span class="vi2-active-tag">Desde ${{ number_format($minPrice, 0, ',', '.') }}</span>
    @endif
    @if($maxPrice ?? '')
      <span class="vi2-active-tag">Hasta ${{ number_format($maxPrice, 0, ',', '.') }}</span>
    @endif
  </div>
@endif

{{-- Availability banner --}}
@if(($date ?? '') && ($availableAt ?? ''))
  <div class="vi2-avail-banner">
    <div class="vi2-avail-banner-inner">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10M12 6v6l4 2M22 6l-10 10-3-3"/></svg>
      Mostrando solo canchas disponibles el <strong>{{ \Carbon\Carbon::parse($date)->format('d/m') }}</strong> a las <strong>{{ $availableAt }}hs</strong>
    </div>
  </div>
@endif

{{-- Falta Uno banner --}}
@if($faltaUno ?? false)
  <div class="vi2-falta-banner">
    <div class="vi2-falta-banner-inner">
      <div class="vi2-falta-banner-ico">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      </div>
      <div class="vi2-falta-banner-text">
        <b>Complejos con Falta Uno habilitado</b>
        <span>Estos complejos tienen al menos una cancha donde podés crear partidos Falta Uno.</span>
      </div>
      <a href="{{ route('falta-uno.index') }}" class="vi2-falta-banner-link">
        Ver partidos disponibles
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>
  </div>
@endif

{{-- ═══════════════════════════════════════════════════════
     FEATURED (Destacados)
     ═══════════════════════════════════════════════════════ --}}
<section style="border-top:1px solid var(--bd); margin-top: 40px;">
  <div class="vi2-sec-w">
    <div class="vi2-sec-head">
      <div>
        <h2 class="vi2-sec-title vi2-rv"><b>Destacados</b></h2>
        <p class="vi2-sec-sub vi2-rv d1">Los complejos con mejor actividad y valoraciones.</p>
      </div>
      <div class="vi2-feat-tabs vi2-rv d2">
        <button type="button" class="vi2-feat-tab active" data-tab="top">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.07-2.14 0-5.5 3-7 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.15.5-2.85 2.5-3.5Z"/></svg>
          Más reservados
        </button>
        <button type="button" class="vi2-feat-tab" data-tab="disc">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          Descuentos
        </button>
        <button type="button" class="vi2-feat-tab" data-tab="rated">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          Mejor valorados
        </button>
      </div>
    </div>

    <div class="vi2-feat-track-wrap">
      {{-- Pane: Más reservados --}}
      <div class="vi2-feat-pane active" data-pane="top">
        <div class="vi2-feat-track">
          @forelse($topReservedVenues as $venue)
            <a href="{{ route('venues.show', $venue) }}" class="vi2-feat-card vi2-rv">
              @if($venue->cover_image_path)
                <div class="vi2-feat-img" style="background-image:url('{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}')"></div>
              @else
                <div class="vi2-feat-img-placeholder">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
                </div>
              @endif
              <div class="vi2-feat-body">
                <h3 class="vi2-feat-name">{{ $venue->name }}</h3>
                <div class="vi2-feat-meta">
                  @if($venue->zone)
                    <span class="vi2-feat-badge">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                      {{ $venue->zone }}
                    </span>
                  @endif
                  <span style="display:inline-flex; align-items:center; gap:4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.07-2.14 0-5.5 3-7 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.15.5-2.85 2.5-3.5Z"/></svg>
                    {{ $venue->weekly_reservations_count ?? 0 }} esta semana
                  </span>
                </div>
                <span class="vi2-feat-cta">Ver complejo <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
              </div>
            </a>
          @empty
            <div class="vi2-feat-empty">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
              <b>Sin datos esta semana</b>
              <span>Las reservas de la semana aparecerán acá</span>
            </div>
          @endforelse
        </div>
      </div>

      {{-- Pane: Descuentos --}}
      <div class="vi2-feat-pane" data-pane="disc">
        <div class="vi2-feat-track">
          @forelse($discountedVenues as $venue)
            <a href="{{ route('venues.show', $venue) }}" class="vi2-feat-card vi2-rv">
              @if($venue->cover_image_path)
                <div class="vi2-feat-img" style="background-image:url('{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}')"></div>
              @else
                <div class="vi2-feat-img-placeholder">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/></svg>
                </div>
              @endif
              <div class="vi2-feat-body">
                <h3 class="vi2-feat-name">{{ $venue->name }}</h3>
                <div class="vi2-feat-meta">
                  @if($venue->zone)
                    <span class="vi2-feat-badge">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                      {{ $venue->zone }}
                    </span>
                  @endif
                  <span style="display:inline-flex; align-items:center; gap:4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/></svg>
                    Descuentos activos
                  </span>
                </div>
                <span class="vi2-feat-cta">Ver complejo <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
              </div>
            </a>
          @empty
            <div class="vi2-feat-empty">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/></svg>
              <b>No hay descuentos activos</b>
              <span>Cuando un complejo tenga promociones, aparecerán acá</span>
            </div>
          @endforelse
        </div>
      </div>

      {{-- Pane: Mejor valorados --}}
      <div class="vi2-feat-pane" data-pane="rated">
        <div class="vi2-feat-track">
          @forelse($bestRatedVenues as $venue)
            <a href="{{ route('venues.show', $venue) }}" class="vi2-feat-card vi2-rv">
              @if($venue->cover_image_path)
                <div class="vi2-feat-img" style="background-image:url('{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}')"></div>
              @else
                <div class="vi2-feat-img-placeholder">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
              @endif
              <div class="vi2-feat-body">
                <h3 class="vi2-feat-name">{{ $venue->name }}</h3>
                <div class="vi2-feat-meta">
                  @if($venue->zone)
                    <span class="vi2-feat-badge">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                      {{ $venue->zone }}
                    </span>
                  @endif
                  <span style="display:inline-flex; align-items:center; gap:4px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    {{ number_format($venue->reviews_avg_rating ?? 0, 1) }} / 5 ({{ $venue->reviews_count ?? 0 }} reseña{{ ($venue->reviews_count ?? 0) !== 1 ? 's' : '' }})
                  </span>
                </div>
                <span class="vi2-feat-cta">Ver complejo <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
              </div>
            </a>
          @empty
            <div class="vi2-feat-empty">
              <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
              <b>Todavía no hay reseñas</b>
              <span>Reservá y dejá tu opinión para ayudar a otros jugadores</span>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     ALL VENUES GRID
     ═══════════════════════════════════════════════════════ --}}
<section style="border-top:1px solid var(--bd); margin-top: 40px;">
  <div class="vi2-sec-w">
    <div class="vi2-sec-head">
      <div>
        <h2 class="vi2-sec-title vi2-rv">
          @if($hasFilters ?? false)
            Resultados de <b>búsqueda</b>
          @else
            Todos los <b>complejos</b>
          @endif
        </h2>
        @if($hasFilters ?? false)
          <p class="vi2-sec-sub vi2-rv d1">
            @if($zone ?? '') Mostrando resultados en {{ $zone }} @else Según tus filtros @endif
          </p>
        @else
          <p class="vi2-sec-sub vi2-rv d1">Explorá la red completa de complejos disponibles.</p>
        @endif
      </div>
      <div class="vi2-sec-count vi2-rv d2">{{ $venues->count() }} resultado{{ $venues->count() !== 1 ? 's' : '' }}</div>
    </div>

    @if($venues->isEmpty())
      <div class="vi2-empty">
        <div class="vi2-empty-ico">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="8" width="56" height="48" rx="6" style="transform: scale(.4); transform-origin: center;"/><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        </div>
        <h4>No encontramos complejos</h4>
        <p>Probá ajustando los filtros o limpiando la búsqueda.</p>
        <a href="{{ route('venues.index') }}" class="vi2-empty-btn">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 6 6 18M6 6l12 12"/></svg>
          Limpiar filtros
        </a>
      </div>
    @else
      <div class="vi2-venues-grid">
        @foreach($venues as $venue)
          @php
            $planSlug = $venue->owner_plan_slug ?? 'starter';
            $cardClass = match($planSlug) { 'pro' => 'pro', 'full' => 'full', default => '' };
          @endphp
          <article class="vi2-card {{ $cardClass }} vi2-rv">
            <a href="{{ route('venues.show', $venue) }}" class="vi2-card-img" aria-label="Ver {{ $venue->name }}">
              @if($venue->cover_image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}" loading="lazy">
              @else
                <div class="vi2-card-img-placeholder">
                  <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
                </div>
              @endif

              <div class="vi2-badges">
                @if($venue->zone)
                  <span class="vi2-badge vi2-badge-zone">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    {{ $venue->zone }}
                  </span>
                @endif
                @if(($venue->falta_uno_count ?? 0) > 0)
                  <span class="vi2-badge vi2-badge-falta">
                    <span class="vi2-falta-dot"></span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    Falta Uno
                  </span>
                @endif
              </div>

              @if($planSlug === 'pro')
                <span class="vi2-card-plan">
                  <span class="vi2-badge-pro">
                    <svg viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Destacado
                  </span>
                </span>
              @elseif($planSlug === 'full')
                <span class="vi2-card-plan">
                  <span class="vi2-badge-full">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Premium
                  </span>
                </span>
              @endif
            </a>

            @auth
              @if(in_array($venue->id, $favoriteVenueIds ?? []))
                <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0; position: absolute; top: 12px; right: 12px;">
                  @csrf
                  <button type="submit" class="vi2-card-fav saved" title="Quitar de favoritos">
                    <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78Z"/></svg>
                  </button>
                </form>
              @else
                <form method="POST" action="{{ route('venues.favorite', $venue) }}" style="margin:0; position: absolute; top: 12px; right: 12px;">
                  @csrf
                  <button type="submit" class="vi2-card-fav" title="Guardar en favoritos">
                    <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78Z"/></svg>
                  </button>
                </form>
              @endif
            @endauth

            <div class="vi2-card-body">
              <h3 class="vi2-card-name">{{ $venue->name }}</h3>
              @if(($venue->reviews_count ?? 0) > 0)
                <div class="vi2-card-rating">
                  @php $rounded = round($venue->reviews_avg_rating); @endphp
                  <span class="vi2-card-stars">
                    @for($i = 1; $i <= 5; $i++){!! $i <= $rounded ? '&#9733;' : '&#9734;' !!}@endfor
                  </span>
                  <span class="vi2-card-rating-num">{{ number_format($venue->reviews_avg_rating, 1) }}</span>
                  <span class="vi2-card-rating-count">({{ $venue->reviews_count }} reseña{{ $venue->reviews_count !== 1 ? 's' : '' }})</span>
                </div>
              @else
                <div class="vi2-card-no-reviews">Sin reseñas todavía</div>
              @endif

              <p class="vi2-card-desc">
                {{ $venue->description ?? 'Reservá online y encontrá disponibilidad en pocos pasos.' }}
              </p>

              <a href="{{ route('venues.show', $venue) }}" class="vi2-card-cta">
                Ver complejo
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
              </a>
            </div>
          </article>
        @endforeach
      </div>
    @endif
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     MAP
     ═══════════════════════════════════════════════════════ --}}
<section class="vi2-map-sec" style="border-top:1px solid var(--bd); margin-top: 40px;">
  <div class="vi2-sec-w">
    <div class="vi2-sec-head">
      <div>
        <h2 class="vi2-sec-title vi2-rv">Mapa de <b>complejos</b></h2>
        <p class="vi2-sec-sub vi2-rv d1">Visualizá todos los complejos en el mapa.</p>
      </div>
    </div>
    <div class="vi2-map-wrap" id="vi2MapWrap">
      <div class="vi2-map-skeleton" id="vi2MapSkeleton">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" y1="2" x2="8" y2="18"/><line x1="16" y1="6" x2="16" y2="22"/></svg>
      </div>
      <div id="vi2Map"></div>
    </div>
  </div>
</section>

</div>{{-- /.vi2 --}}

@push('scripts')
<script>
  (function() {
    // ── Scroll reveal ──
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(e) {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.01 });
    document.querySelectorAll('.vi2-rv').forEach(function(el) {
      var r = el.getBoundingClientRect();
      if (r.top < window.innerHeight && r.bottom > 0) {
        requestAnimationFrame(function() { el.classList.add('in'); });
      } else {
        io.observe(el);
      }
    });

    // ── Featured tab switching ──
    document.querySelectorAll('.vi2-feat-tab').forEach(function(tab) {
      tab.addEventListener('click', function() {
        var name = tab.dataset.tab;
        document.querySelectorAll('.vi2-feat-tab').forEach(function(t) { t.classList.remove('active'); });
        tab.classList.add('active');
        document.querySelectorAll('.vi2-feat-pane').forEach(function(p) {
          p.classList.toggle('active', p.dataset.pane === name);
        });
      });
    });

    // ── Advanced filters toggle ──
    window.vi2ToggleAdv = function() {
      var panel = document.getElementById('vi2AdvPanel');
      if (panel) panel.classList.toggle('open');
    };

    // ── Date/time pickers (robust) ──
    window.vi2OpenDate = function() {
      var input = document.getElementById('vi2DateInput');
      if (!input) return;
      if (typeof input.showPicker === 'function') { try { input.showPicker(); return; } catch(e) {} }
      try { input.focus(); } catch(e) {}
      try { input.click(); } catch(e) {}
    };
    window.vi2OpenTime = function() {
      var input = document.getElementById('vi2TimeInput');
      if (!input) return;
      if (typeof input.showPicker === 'function') { try { input.showPicker(); return; } catch(e) {} }
      try { input.focus(); } catch(e) {}
      try { input.click(); } catch(e) {}
    };

    // ── Geolocation ──
    window.vi2RequestGeo = function() {
      if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización.');
        return;
      }
      var btn = document.getElementById('vi2GeoBtn');
      if (btn) btn.textContent = 'Obteniendo ubicación…';
      navigator.geolocation.getCurrentPosition(function(pos) {
        document.getElementById('vi2UserLat').value = pos.coords.latitude;
        document.getElementById('vi2UserLng').value = pos.coords.longitude;
        document.getElementById('vi2Form').submit();
      }, function() {
        if (btn) btn.textContent = 'No se pudo obtener tu ubicación';
      });
    };

    // ── Google Maps lazy load ──
    var VENUES = [
      @foreach($allVenues as $v)
        { id: {{ $v->id }}, name: @json($v->name), lat: {{ $v->lat ?? 'null' }}, lng: {{ $v->lng ?? 'null' }}, url: @json(route('venues.show', $v)) }@if(!$loop->last),@endif
      @endforeach
    ];
    var DEFAULT_CENTER = { lat: -34.6037, lng: -58.3816 };

    window.vi2InitMap = function() {
      var mapEl = document.getElementById('vi2Map');
      var skel  = document.getElementById('vi2MapSkeleton');
      if (!mapEl) return;
      mapEl.style.display = 'block';
      mapEl.style.height = '440px';
      if (skel) skel.style.display = 'none';

      var first = VENUES.find(function(v) { return v.lat !== null && v.lng !== null; });
      var map = new google.maps.Map(mapEl, {
        zoom: first ? 13 : 12,
        center: first ? { lat: Number(first.lat), lng: Number(first.lng) } : DEFAULT_CENTER,
        styles: [
          { elementType: 'geometry', stylers: [{ color: '#1d1d1d' }] },
          { elementType: 'labels.text.stroke', stylers: [{ color: '#050505' }] },
          { elementType: 'labels.text.fill', stylers: [{ color: '#8a8a8a' }] },
          { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#2a2a2a' }] },
          { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#0a0f14' }] },
          { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] }
        ]
      });
      function escHtml(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
      VENUES.forEach(function(v) {
        if (v.lat === null || v.lng === null) return;
        var marker = new google.maps.Marker({ map: map, position: { lat: Number(v.lat), lng: Number(v.lng) }, title: v.name });
        var info = new google.maps.InfoWindow({
          content: '<div style="font-family:system-ui;color:#111;"><strong>' + escHtml(v.name) + '</strong><br><a href="' + escHtml(v.url) + '" style="color:#166534;font-weight:700;">Ver complejo →</a></div>'
        });
        marker.addListener('click', function() { info.open({ map: map, anchor: marker }); });
      });
    };

    var mapLoaded = false;
    var mapObserver = new IntersectionObserver(function(entries) {
      entries.forEach(function(entry) {
        if (entry.isIntersecting && !mapLoaded) {
          mapLoaded = true;
          var script = document.createElement('script');
          script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config("services.google_maps.key") }}&callback=vi2InitMap';
          script.async = true; script.defer = true;
          document.head.appendChild(script);
          mapObserver.disconnect();
        }
      });
    }, { threshold: 0.1 });
    var mapWrap = document.getElementById('vi2MapWrap');
    if (mapWrap) mapObserver.observe(mapWrap);
  })();
</script>
@endpush

@endsection
