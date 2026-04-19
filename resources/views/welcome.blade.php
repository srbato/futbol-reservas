@extends('layouts.marketing')

@section('title', 'TuCancha — Reservá, jugá, encontrá jugadores')
@section('meta_description', 'Reservá canchas de fútbol, tenis, pádel y más en Argentina. Encontrá el complejo más cercano, elegí el horario y confirmá tu turno online al instante.')
@section('og_title', 'TuCancha — Reservá, jugá, encontrá jugadores')
@section('og_description', 'La plataforma para reservar canchas, organizar torneos y encontrar jugadores en Argentina.')

@push('styles')
<style>
  /* ═══════════════════════════════════════════════════════════════
     WELCOME V2 — Editorial Dark Landing (based on Claude Design)
     Palette: TuCancha green #22c55e
     Font: Sora (from layout) — bold/heavy weights preserved
     Scoped with .wv2 prefix to avoid collisions
     ═══════════════════════════════════════════════════════════════ */
  .wv2 {
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
    --gold: #d4b878;
    --silver: #c7ccd1;
    --bronze: #c68a5a;
  }
  .wv2 { background: var(--bg); color: var(--tx); font-family: 'Sora', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
  .wv2 * { box-sizing: border-box; }
  .wv2 a { color: inherit; text-decoration: none; }
  .wv2 button { font-family: inherit; cursor: pointer; }
  .wv2 ::selection { background: var(--accent); color: var(--accent-ink); }

  /* Marketing layout's <main> is full-bleed by default — no hack needed */
  .wv2 { overflow-x: clip; }

  /* ── HERO ───────────────────────────────────────── */
  .wv2-hero {
    position: relative;
    min-height: 100vh;
    display: flex; align-items: flex-end;
    overflow: hidden;
    padding: 140px 40px 72px;
  }
  .wv2-hero-photo {
    position: absolute; inset: 0;
    background: url('/images/hero-futbol.webp') center 40% / cover no-repeat;
    animation: wv2-kenburns 20s ease-in-out infinite alternate;
  }
  @keyframes wv2-kenburns {
    0% { transform: scale(1.05) translate(0, 0); }
    100% { transform: scale(1.12) translate(-2%, -1%); }
  }
  .wv2-hero-grad {
    position: absolute; inset: 0;
    background:
      radial-gradient(ellipse 70% 60% at 30% 100%, rgba(0,0,0,.9), transparent 70%),
      linear-gradient(180deg, rgba(5,5,5,.7) 0%, rgba(5,5,5,.35) 30%, rgba(5,5,5,.75) 75%, rgba(5,5,5,.98) 100%);
  }
  .wv2-hero-noise {
    position: absolute; inset: 0; opacity: .35; pointer-events: none; mix-blend-mode: overlay;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='240' height='240'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' seed='3'/><feColorMatrix values='0 0 0 0 1  0 0 0 0 1  0 0 0 0 1  0 0 0 .5 0'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
  }
  .wv2-hero-inner {
    position: relative; z-index: 2;
    max-width: 1360px; margin: 0 auto; width: 100%;
    display: grid; grid-template-columns: 1.2fr auto; align-items: end; gap: 48px;
  }
  .wv2-hero-copy { max-width: 720px; }
  .wv2-hero-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    padding: 6px 14px 6px 10px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 999px;
    font-size: 12px; font-weight: 600; color: var(--tx);
    margin-bottom: 28px;
    backdrop-filter: blur(12px);
  }
  .wv2-hero-eyebrow-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 0 3px rgba(34,197,94,.25); animation: wv2-pulse-dot 1.6s ease-in-out infinite; }
  @keyframes wv2-pulse-dot { 0%,100% { opacity: 1; } 50% { opacity: .5; } }

  .wv2-hero-h1 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(56px, 8vw, 112px);
    font-weight: 800;
    letter-spacing: -0.045em;
    line-height: .96;
    margin: 0 0 24px;
    color: #fff;
  }
  .wv2-hero-h1 b { font-weight: 900; }
  .wv2-hero-h1 i {
    font-style: italic;
    font-weight: 700;
    letter-spacing: -0.045em;
    color: var(--accent);
  }
  .wv2-hero-sub {
    font-size: 18px; font-weight: 400;
    color: var(--tx-2); line-height: 1.55;
    max-width: 520px;
    margin: 0 0 36px;
    text-wrap: pretty;
  }

  /* Search widget */
  .wv2-search {
    display: flex; gap: 1px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 999px;
    padding: 4px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    max-width: 640px;
    align-items: center;
  }
  .wv2-search-select {
    position: relative;
    display: flex; align-items: center; gap: 8px;
    padding: 12px 20px 12px 18px;
    font-size: 14px; font-weight: 600; color: var(--tx);
    white-space: nowrap;
  }
  .wv2-search-select select {
    background: transparent; border: none; outline: none; color: var(--tx);
    font-family: inherit; font-weight: 600; font-size: 14px;
    appearance: none; -webkit-appearance: none; -moz-appearance: none;
    padding-right: 22px; cursor: pointer;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23c8c8c8' stroke-width='2.5'><path d='m6 9 6 6 6-6'/></svg>");
    background-repeat: no-repeat;
    background-position: right center;
  }
  .wv2-search-select select option { background: var(--bg-2); color: var(--tx); }
  .wv2-search-divider { width: 1px; height: 28px; background: rgba(255,255,255,.12); }
  .wv2-search-field {
    flex: 1; padding: 12px 22px;
    background: transparent;
    border: none; color: var(--tx);
    font-family: inherit; font-size: 14px; font-weight: 500;
    outline: none;
    min-width: 0;
  }
  .wv2-search-field::placeholder { color: var(--tx-3); }
  .wv2-search-cta {
    padding: 14px 24px;
    background: var(--accent); color: var(--accent-ink);
    border: none; border-radius: 999px;
    font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px;
    transition: background .15s;
    white-space: nowrap;
  }
  .wv2-search-cta:hover { background: var(--accent-hover); }
  .wv2-search-quicks {
    display: flex; gap: 6px; margin-top: 16px; flex-wrap: wrap;
  }
  .wv2-quick-chip {
    padding: 6px 14px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 999px;
    font-size: 12px; font-weight: 500; color: var(--tx-2);
    transition: background .15s, color .15s;
    backdrop-filter: blur(10px);
    cursor: pointer; text-decoration: none;
  }
  .wv2-quick-chip:hover { background: rgba(255,255,255,.1); color: var(--tx); }

  .wv2-hero-side { display: flex; flex-direction: column; gap: 12px; min-width: 280px; }
  .wv2-live-card {
    padding: 20px 22px;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 20px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
  }
  .wv2-live-head { display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--tx-3); margin-bottom: 12px; }
  .wv2-live-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent); animation: wv2-pulse-dot 1.2s ease-in-out infinite; }
  .wv2-live-stat {
    font-size: 30px; font-weight: 700;
    letter-spacing: -0.03em; line-height: 1;
    color: #fff;
  }
  .wv2-live-label { font-size: 13px; color: var(--tx-2); margin-top: 8px; line-height: 1.5; font-weight: 400; }

  .wv2-hero-scroll {
    position: absolute; left: 50%; bottom: 28px; transform: translateX(-50%); z-index: 2;
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    font-size: 10px; font-weight: 700; letter-spacing: .2em; text-transform: uppercase; color: var(--tx-3);
    animation: wv2-bob 2.4s ease-in-out infinite;
  }
  @keyframes wv2-bob { 0%,100% { transform: translateX(-50%) translateY(0); } 50% { transform: translateX(-50%) translateY(6px); } }
  .wv2-hero-scroll-line { width: 1px; height: 28px; background: linear-gradient(to bottom, transparent, var(--tx-3)); }

  /* ── TRUST BAR ── */
  .wv2-trust {
    padding: 40px 40px;
    border-top: 1px solid var(--bd);
    border-bottom: 1px solid var(--bd);
    background: var(--bg-1);
  }
  .wv2-trust-inner {
    max-width: 1360px; margin: 0 auto;
    display: flex; align-items: center; gap: 48px; flex-wrap: wrap;
  }
  .wv2-trust-label {
    max-width: 340px;
  }
  .wv2-trust-label b { display: block; font-size: 20px; font-weight: 800; color: var(--tx); letter-spacing: -0.02em; margin-bottom: 6px; }
  .wv2-trust-label span { font-size: 12px; font-weight: 400; color: var(--tx-3); line-height: 1.5; }
  .wv2-trust-items {
    display: flex; gap: 32px; flex-wrap: wrap;
    flex: 1;
  }
  .wv2-trust-item {
    display: flex; align-items: center; gap: 10px;
    color: var(--tx-2); font-size: 13px; font-weight: 600;
  }

  /* ── SEC WRAPPER ── */
  .wv2-sec-w { max-width: 1360px; margin: 0 auto; padding: 120px 40px; }
  .wv2-eyebrow {
    display: inline-flex; align-items: center; gap: 10px;
    font-size: 11px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
    color: var(--tx-3); margin-bottom: 20px;
  }
  .wv2-eyebrow::before { content: ''; display: inline-block; width: 24px; height: 1px; background: var(--tx-3); }
  .wv2-sec-h {
    font-size: clamp(38px, 5vw, 64px);
    font-weight: 700;
    letter-spacing: -0.035em; line-height: 1.02;
    margin: 0 0 20px;
    color: var(--tx);
  }
  .wv2-sec-h b { font-weight: 900; }
  .wv2-sec-h i {
    font-style: italic;
    font-weight: 700;
    letter-spacing: -0.035em;
    color: var(--accent);
  }
  .wv2-sec-sub { font-size: 17px; font-weight: 400; color: var(--tx-2); line-height: 1.55; max-width: 580px; margin: 0 0 56px; text-wrap: pretty; }

  /* ── SPORTS ── */
  .wv2-sports {
    border-bottom: 1px solid var(--bd);
    padding: 72px 40px;
    background: var(--bg);
  }
  .wv2-sports-head {
    max-width: 1360px; margin: 0 auto 40px; padding: 0 0 0 0;
    display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; flex-wrap: wrap;
  }
  .wv2-sports-h3 { font-size: 34px; font-weight: 700; letter-spacing: -0.03em; margin: 0; color: var(--tx); }
  .wv2-sports-h3 i { font-style: italic; font-weight: 700; color: var(--accent); }
  .wv2-sports-link { font-size: 13px; color: var(--tx-2); font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: color .15s; }
  .wv2-sports-link:hover { color: var(--accent); }
  .wv2-sports-grid { max-width: 1360px; margin: 0 auto; display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
  .wv2-sport-card {
    position: relative;
    height: 280px;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid var(--bd);
    display: flex; flex-direction: column; justify-content: flex-end;
    padding: 22px 24px;
    cursor: pointer;
    transition: border-color .3s, transform .3s;
    text-decoration: none;
  }
  .wv2-sport-card:hover { border-color: rgba(34,197,94,.3); transform: translateY(-3px); }
  .wv2-sport-bg { position: absolute; inset: 0; background-size: cover; background-position: center; transition: transform .5s, filter .3s; filter: brightness(.5) saturate(.9); }
  .wv2-sport-card:hover .wv2-sport-bg { transform: scale(1.05); filter: brightness(.65) saturate(1); }
  .wv2-sport-bg::after { content: ''; position: absolute; inset: 0; background: linear-gradient(0deg, rgba(5,5,5,.95), rgba(5,5,5,.35) 50%, transparent); }
  .wv2-sport-content { position: relative; z-index: 2; }
  .wv2-sport-count { font-size: 11px; color: var(--tx-3); letter-spacing: .1em; text-transform: uppercase; font-weight: 600; margin-bottom: 8px; }
  .wv2-sport-name { font-size: 24px; font-weight: 800; letter-spacing: -0.02em; color: #fff; display: flex; align-items: center; gap: 8px; }
  .wv2-sport-arrow { opacity: 0; transform: translateX(-6px); transition: all .25s; }
  .wv2-sport-card:hover .wv2-sport-arrow { opacity: 1; transform: translateX(0); }

  /* ── VALUES ── */
  .wv2-values-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: start;
  }
  .wv2-values-h { position: sticky; top: 110px; }
  .wv2-values-list { display: flex; flex-direction: column; }
  .wv2-value {
    padding: 40px 0;
    border-top: 1px solid var(--bd);
    display: grid; grid-template-columns: 80px 1fr auto; gap: 28px; align-items: start;
  }
  .wv2-value:first-child { border-top: 0; padding-top: 0; }
  .wv2-value-num {
    font-size: 36px; font-weight: 700; color: var(--tx-3);
    letter-spacing: -0.04em; line-height: 1;
    font-variant-numeric: tabular-nums;
  }
  .wv2-value-body h4 { margin: 0 0 8px; font-size: 22px; font-weight: 800; letter-spacing: -0.015em; color: var(--tx); }
  .wv2-value-body p { margin: 0; color: var(--tx-2); font-size: 14px; line-height: 1.7; font-weight: 400; max-width: 420px; }
  .wv2-value-ico {
    width: 48px; height: 48px; border-radius: 12px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    display: flex; align-items: center; justify-content: center;
    color: var(--tx-2);
  }

  /* Big stats */
  .wv2-bstats {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 0;
    border-top: 1px solid var(--bd);
    border-bottom: 1px solid var(--bd);
    margin-top: 80px;
  }
  .wv2-bstat { padding: 40px 32px; position: relative; }
  .wv2-bstat + .wv2-bstat { border-left: 1px solid var(--bd); }
  .wv2-bstat-num {
    font-weight: 800;
    font-size: 36px; letter-spacing: -0.035em; line-height: 1;
    color: var(--tx);
  }
  .wv2-bstat-label { font-size: 12px; font-weight: 700; color: var(--tx-3); letter-spacing: .1em; text-transform: uppercase; margin-top: 14px; }
  .wv2-bstat-sub { font-size: 13px; color: var(--tx-2); margin-top: 6px; font-weight: 400; line-height: 1.6; }

  /* ── STEPS ── */
  .wv2-steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1px;
    background: var(--bd);
    border: 1px solid var(--bd);
    border-radius: 24px;
    overflow: hidden;
    margin-top: 56px;
  }
  .wv2-step {
    background: var(--bg);
    padding: 44px 36px 40px;
    position: relative;
    transition: background .3s;
  }
  .wv2-step:hover { background: var(--bg-2); }
  .wv2-step-num {
    font-weight: 800;
    font-size: 64px;
    color: var(--tx-4);
    line-height: 1;
    letter-spacing: -0.05em;
    margin-bottom: 28px;
    transition: color .3s;
    font-variant-numeric: tabular-nums;
  }
  .wv2-step:hover .wv2-step-num { color: var(--accent); }
  .wv2-step h4 { margin: 0 0 10px; font-size: 22px; font-weight: 800; letter-spacing: -0.015em; color: var(--tx); }
  .wv2-step p { margin: 0; color: var(--tx-2); font-size: 14px; line-height: 1.7; font-weight: 400; }
  .wv2-step-visual {
    margin-top: 28px; padding-top: 28px;
    border-top: 1px solid var(--bd);
    font-size: 12px; color: var(--tx-3); display: flex; align-items: center; gap: 10px;
  }
  .wv2-step-visual b { color: var(--tx); font-weight: 700; font-size: 13px; }

  /* ── RANKING SECTION ── */
  .wv2-rank-grid {
    display: grid; grid-template-columns: 1fr 1.15fr; gap: 72px; align-items: center;
  }
  .wv2-rank-copy { max-width: 480px; }
  .wv2-rank-tag {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
    color: var(--gold);
    margin-bottom: 18px;
    padding: 5px 12px 5px 10px;
    background: rgba(212,184,120,.08);
    border: 1px solid rgba(212,184,120,.2);
    border-radius: 999px;
  }
  .wv2-rank-tag-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--gold); }
  .wv2-rank-list { margin-top: 32px; display: flex; flex-direction: column; gap: 14px; }
  .wv2-rank-li { display: flex; gap: 14px; align-items: flex-start; font-size: 14px; color: var(--tx-2); line-height: 1.6; font-weight: 400; }
  .wv2-rank-li svg { flex-shrink: 0; margin-top: 2px; color: var(--gold); }
  .wv2-rank-cta-row { display: flex; gap: 10px; margin-top: 36px; flex-wrap: wrap; }

  /* Mock leaderboard */
  .wv2-rank-visual {
    position: relative;
    padding: 28px;
    border: 1px solid var(--bd);
    border-radius: 24px;
    background: linear-gradient(180deg, var(--bg-2) 0%, var(--bg-1) 100%);
    overflow: hidden;
  }
  .wv2-rank-visual::before {
    content: '';
    position: absolute; top: -120px; left: -80px;
    width: 320px; height: 320px;
    background: radial-gradient(circle, rgba(212,184,120,.12), transparent 70%);
    pointer-events: none;
  }
  .wv2-rank-visual-head {
    display: flex; align-items: center; justify-content: space-between; gap: 10px;
    margin-bottom: 18px; position: relative; z-index: 1;
    flex-wrap: wrap;
  }
  .wv2-rank-visual-title {
    font-size: 11px; color: var(--tx-3); font-weight: 800;
    letter-spacing: .12em; text-transform: uppercase;
  }
  .wv2-rank-visual-filter {
    font-size: 11px; color: var(--tx-2); font-weight: 700;
    padding: 5px 12px; border-radius: 999px;
    background: rgba(255,255,255,.05); border: 1px solid var(--bd);
    display: inline-flex; align-items: center; gap: 6px;
  }
  .wv2-rank-visual-filter-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); }

  .wv2-rank-row {
    display: grid;
    grid-template-columns: 32px 44px 1fr auto auto;
    gap: 14px; align-items: center;
    padding: 12px 14px;
    border-radius: 12px;
    position: relative; z-index: 1;
    transition: background .2s;
  }
  .wv2-rank-row + .wv2-rank-row { border-top: 1px solid var(--bd); }
  .wv2-rank-row:hover { background: rgba(255,255,255,.02); }
  .wv2-rank-row.you {
    background: rgba(34,197,94,.06);
    border: 1px solid rgba(34,197,94,.2);
    margin: 4px 0;
  }
  .wv2-rank-row.you + .wv2-rank-row { border-top: none; }

  .wv2-rank-pos {
    font-size: 15px; font-weight: 800; color: var(--tx-3);
    letter-spacing: -0.02em; font-variant-numeric: tabular-nums;
    text-align: center;
  }
  .wv2-rank-pos.p1 { color: var(--gold); }
  .wv2-rank-pos.p2 { color: var(--silver); }
  .wv2-rank-pos.p3 { color: var(--bronze); }

  .wv2-rank-avatar {
    width: 34px; height: 34px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 12px; color: #000;
    background: linear-gradient(135deg, #3a3a3a, #1a1a1a);
    color: var(--gold);
    border: 2px solid rgba(255,255,255,.08);
  }
  .wv2-rank-avatar.p1 { background: linear-gradient(135deg, var(--gold), #a88844); color: #000; border-color: rgba(212,184,120,.35); }
  .wv2-rank-avatar.p2 { background: linear-gradient(135deg, #dbe0e5, #8c9199); color: #000; border-color: rgba(199,204,209,.35); }
  .wv2-rank-avatar.p3 { background: linear-gradient(135deg, #d79566, #7a4e32); color: #000; border-color: rgba(198,138,90,.35); }
  .wv2-rank-avatar.you { background: linear-gradient(135deg, var(--accent), #16a34a); color: var(--accent-ink); border-color: rgba(34,197,94,.35); }

  .wv2-rank-info { min-width: 0; }
  .wv2-rank-name {
    font-size: 13px; font-weight: 700; color: var(--tx);
    letter-spacing: -0.01em;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
  .wv2-rank-sub {
    font-size: 11px; color: var(--tx-3); margin-top: 2px;
    font-weight: 500;
  }

  .wv2-rank-rating {
    font-size: 12px; font-weight: 800; color: var(--accent);
    display: inline-flex; align-items: center; gap: 4px;
  }
  .wv2-rank-score {
    font-size: 15px; font-weight: 800; color: var(--tx);
    letter-spacing: -0.02em; font-variant-numeric: tabular-nums;
    text-align: right;
  }
  .wv2-rank-medals {
    position: relative; z-index: 1;
    margin-top: 16px; padding-top: 16px;
    border-top: 1px solid var(--bd);
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
  }
  .wv2-rank-medals-label {
    font-size: 10px; font-weight: 800; letter-spacing: .12em;
    text-transform: uppercase; color: var(--tx-3); margin-right: 4px;
  }
  .wv2-rank-medal {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px; font-weight: 700;
  }
  .wv2-rank-medal.m-green { background: rgba(34,197,94,.1); color: var(--accent); border: 1px solid rgba(34,197,94,.2); }
  .wv2-rank-medal.m-blue { background: rgba(122,190,245,.08); color: #7abef5; border: 1px solid rgba(122,190,245,.2); }
  .wv2-rank-medal.m-orange { background: rgba(255,154,75,.08); color: #ff9a4b; border: 1px solid rgba(255,154,75,.2); }
  .wv2-rank-medal.m-gold { background: rgba(212,184,120,.08); color: var(--gold); border: 1px solid rgba(212,184,120,.2); }

  /* ── FALTA UNO ── */
  .wv2-falta {
    background: var(--bg-1);
    border-top: 1px solid var(--bd); border-bottom: 1px solid var(--bd);
  }
  .wv2-falta-grid {
    display: grid; grid-template-columns: 1fr 1.15fr; gap: 72px; align-items: center;
  }
  .wv2-falta-copy { max-width: 480px; }
  .wv2-falta-tag {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; font-weight: 700; letter-spacing: .16em; text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 18px;
    padding: 5px 12px 5px 10px;
    background: rgba(34,197,94,.08);
    border: 1px solid rgba(34,197,94,.25);
    border-radius: 999px;
  }
  .wv2-falta-tag-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); animation: wv2-pulse-dot 1.2s ease-in-out infinite; }
  .wv2-falta-list { margin-top: 32px; display: flex; flex-direction: column; gap: 14px; }
  .wv2-falta-li { display: flex; gap: 14px; align-items: flex-start; font-size: 14px; color: var(--tx-2); line-height: 1.6; font-weight: 400; }
  .wv2-falta-li svg { flex-shrink: 0; margin-top: 2px; color: var(--accent); }
  .wv2-falta-cta-row { display: flex; gap: 10px; margin-top: 36px; flex-wrap: wrap; }

  .wv2-btn-primary {
    padding: 13px 22px;
    background: var(--accent); color: var(--accent-ink);
    border: none; border-radius: 999px;
    font-size: 14px; font-weight: 800;
    display: inline-flex; align-items: center; gap: 8px;
    transition: background .15s, transform .15s;
    text-decoration: none;
  }
  .wv2-btn-primary:hover { background: var(--accent-hover); transform: translateY(-1px); color: var(--accent-ink); }
  .wv2-btn-outline {
    padding: 13px 22px;
    background: transparent;
    border: 1px solid var(--bd-2);
    color: var(--tx);
    border-radius: 999px;
    font-size: 14px; font-weight: 700;
    display: inline-flex; align-items: center; gap: 8px;
    transition: border-color .15s, background .15s;
    text-decoration: none;
  }
  .wv2-btn-outline:hover { border-color: var(--tx); background: rgba(255,255,255,.04); color: var(--tx); }

  /* Match visual */
  .wv2-match-visual {
    position: relative;
    padding: 32px;
    border: 1px solid var(--bd);
    border-radius: 24px;
    background: linear-gradient(180deg, var(--bg-2) 0%, var(--bg-1) 100%);
    overflow: hidden;
  }
  .wv2-match-visual::before {
    content: '';
    position: absolute; top: -100px; right: -100px;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(34,197,94,.1), transparent 70%);
    pointer-events: none;
  }
  .wv2-match-h {
    font-size: 11px; color: var(--tx-3); font-weight: 700;
    letter-spacing: .12em; text-transform: uppercase; margin-bottom: 18px;
  }
  .wv2-match-card {
    padding: 20px;
    background: var(--bg);
    border: 1px solid var(--bd);
    border-radius: 16px;
    margin-bottom: 14px;
    transition: border-color .2s, transform .2s;
  }
  .wv2-match-card:hover { border-color: var(--bd-2); transform: translateY(-2px); }
  .wv2-match-card:last-child { margin-bottom: 0; }
  .wv2-match-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 16px; }
  .wv2-match-sport { font-size: 13px; font-weight: 700; color: var(--accent); letter-spacing: .02em; }
  .wv2-match-venue { font-size: 12px; color: var(--tx-3); margin-top: 2px; font-weight: 400; }
  .wv2-match-time { font-size: 22px; font-weight: 800; letter-spacing: -0.03em; line-height: 1; text-align: right; color: var(--tx); font-variant-numeric: tabular-nums; }
  .wv2-match-time small { display: block; font-size: 10px; font-weight: 700; color: var(--tx-3); margin-top: 4px; letter-spacing: .12em; text-transform: uppercase; }
  .wv2-match-players { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
  .wv2-avatars { display: flex; }
  .wv2-av {
    width: 28px; height: 28px; border-radius: 50%;
    border: 2px solid var(--bg);
    background: linear-gradient(135deg, #2a2a2a, #0f0f0f);
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; color: var(--gold);
    margin-left: -8px;
  }
  .wv2-av:first-child { margin-left: 0; }
  .wv2-av-empty { background: transparent; border: 2px dashed var(--bd-2); color: var(--tx-3); }
  .wv2-match-spots { font-size: 12px; color: var(--tx-2); font-weight: 500; }
  .wv2-match-spots b { color: var(--accent); font-weight: 700; }
  .wv2-match-join {
    padding: 8px 16px; background: rgba(34,197,94,.1);
    border: 1px solid rgba(34,197,94,.3);
    color: var(--accent);
    border-radius: 999px;
    font-size: 12px; font-weight: 700;
    transition: background .15s;
    text-decoration: none;
    display: inline-block;
  }
  .wv2-match-join:hover { background: rgba(34,197,94,.2); color: var(--accent-hover); }

  /* ── OWNER ── */
  .wv2-owner {
    position: relative;
    overflow: hidden;
    background: var(--bg);
  }
  .wv2-owner-inner {
    max-width: 1360px; margin: 0 auto;
    padding: 120px 40px;
    position: relative;
    display: grid;
    grid-template-columns: 1.1fr .9fr;
    gap: 72px;
    align-items: center;
  }
  .wv2-owner-bg {
    position: absolute; inset: 0;
    background: url('/images/owner-bg.webp') center / cover;
    opacity: .18;
    filter: saturate(.6);
  }
  .wv2-owner-bg::after {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(90deg, var(--bg) 20%, transparent 60%, var(--bg) 95%);
  }
  .wv2-owner-copy { position: relative; z-index: 2; }
  .wv2-owner-side {
    position: relative; z-index: 2;
    padding: 36px;
    border: 1px solid var(--bd);
    border-radius: 24px;
    background: rgba(10,10,10,.8);
    backdrop-filter: blur(20px);
  }
  .wv2-owner-side-h { font-size: 11px; color: var(--tx-3); font-weight: 700; letter-spacing: .14em; text-transform: uppercase; margin-bottom: 22px; }
  .wv2-owner-perks { display: flex; flex-direction: column; gap: 0; }
  .wv2-owner-perk { display: flex; gap: 14px; align-items: center; padding: 14px 0; border-top: 1px solid var(--bd); font-size: 14px; color: var(--tx-2); font-weight: 400; }
  .wv2-owner-perk:first-child { border-top: 0; padding-top: 0; }
  .wv2-owner-perk b { color: var(--tx); font-size: 18px; letter-spacing: -0.02em; min-width: 88px; font-weight: 800; }
  .wv2-owner-cta-row { display: flex; gap: 10px; align-items: center; margin-top: 36px; flex-wrap: wrap; }

  /* ── FAQ ── */
  .wv2-faq-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 72px; align-items: start; }
  .wv2-faq-list { display: flex; flex-direction: column; }
  .wv2-faq-item {
    border-top: 1px solid var(--bd);
    padding: 22px 0;
    cursor: pointer;
  }
  .wv2-faq-item:last-child { border-bottom: 1px solid var(--bd); }
  .wv2-faq-q { display: flex; justify-content: space-between; align-items: center; gap: 16px; font-size: 18px; font-weight: 700; color: var(--tx); letter-spacing: -0.01em; }
  .wv2-faq-q svg { flex-shrink: 0; transition: transform .3s; color: var(--tx-3); }
  .wv2-faq-item.open .wv2-faq-q svg { transform: rotate(45deg); color: var(--accent); }
  .wv2-faq-a { max-height: 0; overflow: hidden; transition: max-height .4s cubic-bezier(.2,.6,.2,1), margin .3s; color: var(--tx-2); font-size: 14px; line-height: 1.7; font-weight: 400; }
  .wv2-faq-item.open .wv2-faq-a { max-height: 320px; margin-top: 16px; }

  /* ── CLOSING CTA ── */
  .wv2-closing {
    border-top: 1px solid var(--bd);
    padding: 100px 40px 80px;
    background: var(--bg);
  }
  .wv2-closing-inner { max-width: 1360px; margin: 0 auto; }
  .wv2-big-closing {
    font-family: 'Sora'; font-size: clamp(48px, 8vw, 96px);
    font-weight: 800; letter-spacing: -0.05em; line-height: .98;
    color: var(--tx);
    margin: 0 0 40px;
  }
  .wv2-big-closing i { font-style: italic; font-weight: 700; color: var(--accent); }
  .wv2-closing-ctas { display: flex; gap: 12px; flex-wrap: wrap; }

  /* ── Scroll reveal ── */
  .wv2-reveal { opacity: 0; transform: translateY(18px); transition: opacity .9s cubic-bezier(.2,.6,.2,1), transform .9s cubic-bezier(.2,.6,.2,1); }
  .wv2-reveal.in { opacity: 1; transform: translateY(0); }
  .wv2-reveal.d1 { transition-delay: .08s; }
  .wv2-reveal.d2 { transition-delay: .16s; }
  .wv2-reveal.d3 { transition-delay: .24s; }
  .wv2-reveal.d4 { transition-delay: .32s; }

  /* ── Responsive ── */
  @media (max-width: 1024px) {
    .wv2-values-grid { grid-template-columns: 1fr; gap: 40px; }
    .wv2-values-h { position: static; }
    .wv2-falta-grid { grid-template-columns: 1fr; gap: 48px; }
    .wv2-rank-grid { grid-template-columns: 1fr; gap: 48px; }
    .wv2-owner-inner { grid-template-columns: 1fr; gap: 48px; padding: 80px 32px; }
    .wv2-faq-grid { grid-template-columns: 1fr; gap: 40px; }
    .wv2-hero-inner { grid-template-columns: 1fr; gap: 40px; }
    .wv2-sports-grid { grid-template-columns: repeat(2, 1fr); }
  }
  @media (max-width: 640px) {
    .wv2-rank-visual { padding: 20px; }
    .wv2-rank-row { grid-template-columns: 28px 36px 1fr auto; gap: 10px; padding: 10px 8px; }
    .wv2-rank-rating { display: none; }
  }
  @media (max-width: 900px) {
    .wv2-steps { grid-template-columns: 1fr; }
    .wv2-bstats { grid-template-columns: repeat(2, 1fr); }
    .wv2-bstat + .wv2-bstat { border-left: 0; }
    .wv2-bstat:nth-child(3), .wv2-bstat:nth-child(4) { border-top: 1px solid var(--bd); }
    .wv2-bstat:nth-child(3) { border-left: 0; }
    .wv2-bstat:nth-child(4) { border-left: 1px solid var(--bd); }
  }
  @media (max-width: 720px) {
    .wv2-sec-w { padding: 72px 24px; }
    .wv2-hero { padding: 120px 24px 72px; }
    .wv2-trust { padding: 32px 24px; }
    .wv2-trust-inner { gap: 24px; }
    .wv2-sports { padding: 56px 24px; }
    .wv2-sports-head { margin-bottom: 28px; padding: 0 0 0 0; }
    .wv2-sports-grid { grid-template-columns: 1fr; gap: 10px; }
    .wv2-owner-inner { padding: 64px 24px; }
    .wv2-closing { padding: 72px 24px; }
    .wv2-search { flex-wrap: wrap; padding: 8px; }
    .wv2-search-divider { display: none; }
    .wv2-search-field { padding: 10px 16px; width: 100%; }
    .wv2-search-cta { width: 100%; justify-content: center; }
    .wv2-hero-side { width: 100%; }
    .wv2-value { grid-template-columns: 60px 1fr; gap: 16px; }
    .wv2-value-ico { display: none; }
    .wv2-hero-scroll { display: none; }
  }
  @media (max-width: 480px) {
    .wv2-bstats { grid-template-columns: 1fr; }
    .wv2-bstat + .wv2-bstat { border-left: 0; border-top: 1px solid var(--bd); }
  }
</style>
@endpush

@section('content')
<div class="wv2">

{{-- ═══════════════════════════════════════════════════════
     HERO
     ═══════════════════════════════════════════════════════ --}}
<section class="wv2-hero">
  <div class="wv2-hero-photo"></div>
  <div class="wv2-hero-grad"></div>
  <div class="wv2-hero-noise"></div>

  <div class="wv2-hero-inner">
    <div class="wv2-hero-copy">
      <span class="wv2-hero-eyebrow wv2-reveal">
        <span class="wv2-hero-eyebrow-dot"></span>
        <span>Nueva forma de reservar canchas en Argentina</span>
      </span>
      <h1 class="wv2-hero-h1 wv2-reveal d1">Reservá tu cancha<br><i>en minutos.</i></h1>
      <p class="wv2-hero-sub wv2-reveal d2">Sin llamadas, sin WhatsApp, sin esperas. Elegí deporte, zona y horario, confirmá online y jugá. Así de simple.</p>

      <form method="GET" action="{{ route('venues.index') }}" class="wv2-search wv2-reveal d3">
        <div class="wv2-search-select">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M2 12h20"/></svg>
          <select name="deporte" aria-label="Deporte">
            <option value="">Cualquier deporte</option>
            <option value="futbol">Fútbol</option>
            <option value="futbol5">Fútbol 5</option>
            <option value="padel">Pádel</option>
            <option value="tenis">Tenis</option>
            <option value="basquet">Básquet</option>
          </select>
        </div>
        <div class="wv2-search-divider"></div>
        <input class="wv2-search-field" name="q" placeholder="Palermo, Belgrano, Núñez…" aria-label="Zona o complejo" />
        <button type="submit" class="wv2-search-cta">
          Buscar
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        </button>
      </form>

      <div class="wv2-search-quicks wv2-reveal d4">
        <a href="{{ route('venues.index') }}" class="wv2-quick-chip">Esta noche</a>
        <a href="{{ route('venues.index') }}" class="wv2-quick-chip">Este fin de semana</a>
        <a href="{{ route('venues.index') }}" class="wv2-quick-chip">Cerca mío</a>
        <a href="{{ route('venues.index', ['deporte' => 'padel']) }}" class="wv2-quick-chip">Pádel cubierto</a>
      </div>
    </div>

    <div class="wv2-hero-side wv2-reveal d3">
      <div class="wv2-live-card">
        <div class="wv2-live-head"><span class="wv2-live-dot"></span>Disponibilidad en tiempo real</div>
        <div class="wv2-live-stat">Al instante</div>
        <div class="wv2-live-label">Ves qué canchas hay libres, al toque. Sin WhatsApp, sin esperar que te respondan.</div>
      </div>
      <div class="wv2-live-card">
        <div class="wv2-live-head">Pagá online</div>
        <div class="wv2-live-stat" style="font-size:24px;">Mercado Pago · Tarjeta</div>
        <div class="wv2-live-label">Confirmás con un click y listo. También podés pagar al llegar al club.</div>
      </div>
    </div>
  </div>

  <div class="wv2-hero-scroll">
    <span>Scroll</span>
    <div class="wv2-hero-scroll-line"></div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     TRUST BAR
     ═══════════════════════════════════════════════════════ --}}
<section class="wv2-trust">
  <div class="wv2-trust-inner">
    <div class="wv2-trust-label">
      <b>Hecha para jugadores y para clubes.</b>
      <span>Una plataforma pensada desde cero para que reservar sea tan simple como mandar un mensaje — pero más rápido.</span>
    </div>
    <div class="wv2-trust-items">
      <div class="wv2-trust-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>Sin llamadas ni WhatsApp</div>
      <div class="wv2-trust-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>Disponibilidad al instante</div>
      <div class="wv2-trust-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>Pago online seguro</div>
      <div class="wv2-trust-item"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--accent)" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>Confirmación inmediata</div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     SPORTS
     ═══════════════════════════════════════════════════════ --}}
<section class="wv2-sports">
  <div class="wv2-sports-head">
    <div>
      <span class="wv2-eyebrow wv2-reveal">Deportes</span>
      <h3 class="wv2-sports-h3 wv2-reveal d1">Jugá lo que quieras, <i>donde quieras</i>.</h3>
    </div>
    <a href="{{ route('venues.index') }}" class="wv2-sports-link wv2-reveal d2">Ver todos los complejos <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
  </div>

  <div class="wv2-sports-grid">
    <a href="{{ route('venues.index', ['deporte' => 'futbol']) }}" class="wv2-sport-card wv2-reveal d1">
      <div class="wv2-sport-bg" style="background-image:url('/images/sport-futbol.webp')"></div>
      <div class="wv2-sport-content">
        <div class="wv2-sport-count">F5 · F7 · F11</div>
        <div class="wv2-sport-name">Fútbol <span class="wv2-sport-arrow">→</span></div>
      </div>
    </a>
    <a href="{{ route('venues.index', ['deporte' => 'padel']) }}" class="wv2-sport-card wv2-reveal d2">
      <div class="wv2-sport-bg" style="background-image:url('/images/sport-padel.webp')"></div>
      <div class="wv2-sport-content">
        <div class="wv2-sport-count">Cubierto & Panorámico</div>
        <div class="wv2-sport-name">Pádel <span class="wv2-sport-arrow">→</span></div>
      </div>
    </a>
    <a href="{{ route('venues.index', ['deporte' => 'tenis']) }}" class="wv2-sport-card wv2-reveal d3">
      <div class="wv2-sport-bg" style="background-image:url('/images/sport-tenis.webp')"></div>
      <div class="wv2-sport-content">
        <div class="wv2-sport-count">Polvo & cemento</div>
        <div class="wv2-sport-name">Tenis <span class="wv2-sport-arrow">→</span></div>
      </div>
    </a>
    <a href="{{ route('venues.index') }}" class="wv2-sport-card wv2-reveal d4">
      <div class="wv2-sport-bg" style="background-image:url('/images/sport-hockey.webp')"></div>
      <div class="wv2-sport-content">
        <div class="wv2-sport-count">Hockey · Vóley · Básquet</div>
        <div class="wv2-sport-name">Otros deportes <span class="wv2-sport-arrow">→</span></div>
      </div>
    </a>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     VALUES (Por qué)
     ═══════════════════════════════════════════════════════ --}}
<section id="por-que">
  <div class="wv2-sec-w">
    <div class="wv2-values-grid">
      <div class="wv2-values-h">
        <span class="wv2-eyebrow wv2-reveal">Por qué TuCancha</span>
        <h2 class="wv2-sec-h wv2-reveal d1">La forma más<br><b>fluida</b> de <i>jugar</i>.</h2>
        <p class="wv2-sec-sub wv2-reveal d2">Nada de llamadas, esperar respuestas o pagar señas por WhatsApp. Todo confirmado al instante, con una red de complejos en crecimiento.</p>
      </div>

      <div class="wv2-values-list">
        <div class="wv2-value wv2-reveal">
          <div class="wv2-value-num">01</div>
          <div class="wv2-value-body">
            <h4>Confirmación instantánea</h4>
            <p>Reservás, pagás y listo. Sin esperas, sin "te confirmo más tarde". Si la cancha está libre, es tuya en 30 segundos.</p>
          </div>
          <div class="wv2-value-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg></div>
        </div>

        <div class="wv2-value wv2-reveal">
          <div class="wv2-value-num">02</div>
          <div class="wv2-value-body">
            <h4>Complejos verificados</h4>
            <p>Cada club pasa por un control antes de entrar. Fotos reales, horarios al día, canchas en condiciones. Sin sorpresas al llegar.</p>
          </div>
          <div class="wv2-value-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Z"/><path d="m9 12 2 2 4-4"/></svg></div>
        </div>

        <div class="wv2-value wv2-reveal">
          <div class="wv2-value-num">03</div>
          <div class="wv2-value-body">
            <h4>Modificás sin drama</h4>
            <p>¿Te llueve? ¿Se te cae un jugador? Cambiás el horario o cancelás con anticipación, sin llamar a nadie.</p>
          </div>
          <div class="wv2-value-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 2v6h-6"/><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M3 22v-6h6"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/></svg></div>
        </div>

        <div class="wv2-value wv2-reveal">
          <div class="wv2-value-num">04</div>
          <div class="wv2-value-body">
            <h4>Pagá como quieras</h4>
            <p>Mercado Pago, tarjeta o efectivo al llegar al complejo. Vos elegís cómo arreglás con el club.</p>
          </div>
          <div class="wv2-value-ico"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M2 10h20"/></svg></div>
        </div>
      </div>
    </div>

    {{-- Honest promise strip (no fake numbers) --}}
    <div class="wv2-bstats">
      <div class="wv2-bstat wv2-reveal">
        <div class="wv2-bstat-num">Sin llamadas</div>
        <div class="wv2-bstat-label">Todo online</div>
        <div class="wv2-bstat-sub">Olvidate del "te confirmo más tarde" y de esperar respuestas por WhatsApp.</div>
      </div>
      <div class="wv2-bstat wv2-reveal d1">
        <div class="wv2-bstat-num">Tiempo real</div>
        <div class="wv2-bstat-label">Disponibilidad</div>
        <div class="wv2-bstat-sub">Ves qué cancha está libre ahora mismo, con precios actualizados al minuto.</div>
      </div>
      <div class="wv2-bstat wv2-reveal d2">
        <div class="wv2-bstat-num">Al instante</div>
        <div class="wv2-bstat-label">Confirmación</div>
        <div class="wv2-bstat-sub">Reservás, pagás y tu turno queda guardado. Sin trámites ni seguimientos.</div>
      </div>
      <div class="wv2-bstat wv2-reveal d3">
        <div class="wv2-bstat-num">En crecimiento</div>
        <div class="wv2-bstat-label">Red de clubes</div>
        <div class="wv2-bstat-sub">Sumamos complejos cada semana. Mirá los que ya están disponibles en tu zona.</div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     HOW IT WORKS
     ═══════════════════════════════════════════════════════ --}}
<section id="como-funciona" style="border-top:1px solid var(--bd)">
  <div class="wv2-sec-w">
    <div style="max-width:720px">
      <span class="wv2-eyebrow wv2-reveal">Cómo funciona</span>
      <h2 class="wv2-sec-h wv2-reveal d1">De <i>buscar cancha</i> a <b>pisar pasto</b>.<br>Tres pasos.</h2>
    </div>

    <div class="wv2-steps">
      <div class="wv2-step wv2-reveal">
        <div class="wv2-step-num">01</div>
        <h4>Buscá tu cancha</h4>
        <p>Elegí deporte, zona y horario. Ves disponibilidad real, precios y fotos de cada cancha, sin tener que llamar.</p>
        <div class="wv2-step-visual"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg> <b>Filtrá</b> y elegí</div>
      </div>
      <div class="wv2-step wv2-reveal d1">
        <div class="wv2-step-num">02</div>
        <h4>Elegí el horario</h4>
        <p>Mirás la grilla del club en tiempo real y seleccionás el turno que te quede bien. Todo claro, sin sorpresas.</p>
        <div class="wv2-step-visual"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> <b>Elegí</b> el turno</div>
      </div>
      <div class="wv2-step wv2-reveal d2">
        <div class="wv2-step-num">03</div>
        <h4>Reservá y jugá</h4>
        <p>Pagás online o al llegar al club, según lo que prefieras. Recibís la confirmación al toque y a la cancha.</p>
        <div class="wv2-step-visual"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m5 12 5 5L20 7"/></svg> <b>Confirmá</b> y listo</div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     RANKING
     ═══════════════════════════════════════════════════════ --}}
<section id="ranking" style="border-top:1px solid var(--bd);">
  <div class="wv2-sec-w">
    <div class="wv2-rank-grid">
      <div class="wv2-rank-copy">
        <span class="wv2-rank-tag wv2-reveal">
          <span class="wv2-rank-tag-dot"></span>
          Ranking y reputación
        </span>
        <h2 class="wv2-sec-h wv2-reveal d1">Jugá. <b>Sumá puntos</b>.<br><i>Ganate respeto</i>.</h2>
        <p class="wv2-sec-sub wv2-reveal d2">Cada partido cuenta. Ganás puntos por victorias, asistencia y buenas calificaciones de tus compañeros. Mientras más jugás, más subís.</p>

        <div class="wv2-rank-list">
          <div class="wv2-rank-li wv2-reveal d3">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>
            Tu rating se actualiza después de cada partido calificado
          </div>
          <div class="wv2-rank-li wv2-reveal d3">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>
            Ganá medallas por logros (Puntual, En racha, Confiable, Veterano...)
          </div>
          <div class="wv2-rank-li wv2-reveal d3">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>
            Ranking por deporte y nivel — compará con jugadores reales
          </div>
          <div class="wv2-rank-li wv2-reveal d3">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>
            Perfil público con tus stats, historial y reseñas
          </div>
        </div>

        <div class="wv2-rank-cta-row wv2-reveal d4">
          <a href="{{ route('ranking.index') }}" class="wv2-btn-primary">Ver ranking completo</a>
          <a href="{{ route('falta-uno.index') }}" class="wv2-btn-outline">Empezá a jugar</a>
        </div>
      </div>

      {{-- Mock leaderboard visual --}}
      <div class="wv2-rank-visual wv2-reveal d2">
        <div class="wv2-rank-visual-head">
          <span class="wv2-rank-visual-title">Así se ve el ranking</span>
          <span class="wv2-rank-visual-filter">
            <span class="wv2-rank-visual-filter-dot"></span>
            Fútbol · Mensual
          </span>
        </div>

        <div class="wv2-rank-row">
          <span class="wv2-rank-pos p1">1</span>
          <span class="wv2-rank-avatar p1">MM</span>
          <div class="wv2-rank-info">
            <div class="wv2-rank-name">Matías M.</div>
            <div class="wv2-rank-sub">41 PJ · 82% WR</div>
          </div>
          <span class="wv2-rank-rating">★ 4.8</span>
          <span class="wv2-rank-score">1845</span>
        </div>

        <div class="wv2-rank-row">
          <span class="wv2-rank-pos p2">2</span>
          <span class="wv2-rank-avatar p2">LF</span>
          <div class="wv2-rank-info">
            <div class="wv2-rank-name">Lucas F.</div>
            <div class="wv2-rank-sub">32 PJ · 78% WR</div>
          </div>
          <span class="wv2-rank-rating">★ 4.6</span>
          <span class="wv2-rank-score">1712</span>
        </div>

        <div class="wv2-rank-row">
          <span class="wv2-rank-pos p3">3</span>
          <span class="wv2-rank-avatar p3">JC</span>
          <div class="wv2-rank-info">
            <div class="wv2-rank-name">Juan C.</div>
            <div class="wv2-rank-sub">38 PJ · 74% WR</div>
          </div>
          <span class="wv2-rank-rating">★ 4.5</span>
          <span class="wv2-rank-score">1680</span>
        </div>

        <div class="wv2-rank-row you">
          <span class="wv2-rank-pos">#142</span>
          <span class="wv2-rank-avatar you">VO</span>
          <div class="wv2-rank-info">
            <div class="wv2-rank-name">Vos · tu posición</div>
            <div class="wv2-rank-sub">Top 7% · jugando</div>
          </div>
          <span class="wv2-rank-rating">★ 4.2</span>
          <span class="wv2-rank-score">1245</span>
        </div>

        <div class="wv2-rank-row">
          <span class="wv2-rank-pos">148</span>
          <span class="wv2-rank-avatar">FG</span>
          <div class="wv2-rank-info">
            <div class="wv2-rank-name">Franco G.</div>
            <div class="wv2-rank-sub">21 PJ · 58% WR</div>
          </div>
          <span class="wv2-rank-rating">★ 4.1</span>
          <span class="wv2-rank-score">1201</span>
        </div>

        <div class="wv2-rank-medals">
          <span class="wv2-rank-medals-label">Medallas</span>
          <span class="wv2-rank-medal m-green">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 4 5v6c0 5 3.5 9.3 8 11 4.5-1.7 8-6 8-11V5l-8-3z"/></svg>
            Confiable
          </span>
          <span class="wv2-rank-medal m-blue">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            Puntual
          </span>
          <span class="wv2-rank-medal m-orange">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2s5 4 5 10a6 6 0 0 1-12 0c0-2 1-4 2-5 0 2 1 3 2 3 0-4 3-8 3-8z"/></svg>
            En racha
          </span>
          <span class="wv2-rank-medal m-gold">
            <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 7 7 .5-5.5 4.7L18 22l-6-4-6 4 1.5-7.8L2 9.5 9 9z"/></svg>
            Top 10%
          </span>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FALTA UNO
     ═══════════════════════════════════════════════════════ --}}
<section class="wv2-falta">
  <div class="wv2-sec-w">
    <div class="wv2-falta-grid">
      <div class="wv2-falta-copy">
        <span class="wv2-falta-tag wv2-reveal"><span class="wv2-falta-tag-dot"></span>Falta Uno — Completá tu equipo</span>
        <h2 class="wv2-sec-h wv2-reveal d1">¿Te <b>faltan</b> jugadores?<br><i>Acá los encontrás.</i></h2>
        <p class="wv2-sec-sub wv2-reveal d2">Abrí tu partido a la comunidad. Otros jugadores cerca tuyo pueden sumarse, pagar su parte y completar tu equipo sin vueltas.</p>

        <div class="wv2-falta-list">
          <div class="wv2-falta-li wv2-reveal d3"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>Creás tu partido y elegís cuántos faltan</div>
          <div class="wv2-falta-li wv2-reveal d3"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>Aparece en el feed de jugadores cerca tuyo</div>
          <div class="wv2-falta-li wv2-reveal d3"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>Cada jugador paga su parte por la app</div>
          <div class="wv2-falta-li wv2-reveal d3"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 5 5L20 7"/></svg>Calificás después del partido — transparente y seguro</div>
        </div>

        <div class="wv2-falta-cta-row wv2-reveal d4">
          <a href="{{ route('falta-uno.index') }}" class="wv2-btn-primary">Ver partidos abiertos</a>
          <a href="{{ route('falta-uno.index') }}" class="wv2-btn-outline">Cómo crear uno</a>
        </div>
      </div>

      {{-- Mock visual match cards (static / informative) --}}
      <div class="wv2-match-visual wv2-reveal d2">
        <div class="wv2-match-h">Así se ven los partidos abiertos</div>

        <div class="wv2-match-card">
          <div class="wv2-match-head">
            <div>
              <div class="wv2-match-sport">⚽ Fútbol 5 · Villa Crespo</div>
              <div class="wv2-match-venue">Un complejo cerca tuyo · Cancha 2</div>
            </div>
            <div class="wv2-match-time">21:00<small>hoy</small></div>
          </div>
          <div class="wv2-match-players">
            <div style="display:flex;align-items:center;gap:12px">
              <div class="wv2-avatars">
                <div class="wv2-av">M</div>
                <div class="wv2-av" style="color:#86efac">S</div>
                <div class="wv2-av" style="color:#e6b8d8">L</div>
                <div class="wv2-av" style="color:#ffc278">D</div>
                <div class="wv2-av wv2-av-empty">+1</div>
              </div>
              <span class="wv2-match-spots"><b>Falta 1</b> · $6.200 c/u</span>
            </div>
            <span class="wv2-match-join">Sumarme</span>
          </div>
        </div>

        <div class="wv2-match-card">
          <div class="wv2-match-head">
            <div>
              <div class="wv2-match-sport">🎾 Pádel · Belgrano</div>
              <div class="wv2-match-venue">Club ejemplo · Pádel 1</div>
            </div>
            <div class="wv2-match-time">19:30<small>mañ</small></div>
          </div>
          <div class="wv2-match-players">
            <div style="display:flex;align-items:center;gap:12px">
              <div class="wv2-avatars">
                <div class="wv2-av">J</div>
                <div class="wv2-av" style="color:#86efac">N</div>
                <div class="wv2-av wv2-av-empty">+2</div>
              </div>
              <span class="wv2-match-spots"><b>Faltan 2</b> · $3.200 c/u</span>
            </div>
            <span class="wv2-match-join">Sumarme</span>
          </div>
        </div>

        <div class="wv2-match-card">
          <div class="wv2-match-head">
            <div>
              <div class="wv2-match-sport">⚽ Fútbol 7 · Núñez</div>
              <div class="wv2-match-venue">Complejo de la zona · Cancha A</div>
            </div>
            <div class="wv2-match-time">20:00<small>jue</small></div>
          </div>
          <div class="wv2-match-players">
            <div style="display:flex;align-items:center;gap:12px">
              <div class="wv2-avatars">
                <div class="wv2-av">F</div>
                <div class="wv2-av" style="color:#ffc278">P</div>
                <div class="wv2-av" style="color:#86efac">M</div>
                <div class="wv2-av wv2-av-empty">+3</div>
              </div>
              <span class="wv2-match-spots"><b>Faltan 3</b> · $4.800 c/u</span>
            </div>
            <span class="wv2-match-join">Sumarme</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     OWNER CTA
     ═══════════════════════════════════════════════════════ --}}
<section class="wv2-owner">
  <div class="wv2-owner-bg"></div>
  <div class="wv2-owner-inner">
    <div class="wv2-owner-copy">
      <span class="wv2-eyebrow wv2-reveal">Para complejos</span>
      <h2 class="wv2-sec-h wv2-reveal d1">Llená tu agenda.<br><i>Nosotros nos ocupamos</i> del resto.</h2>
      <p class="wv2-sec-sub wv2-reveal d2">Sumá tu complejo a una plataforma hecha para digitalizar las reservas deportivas en Argentina. Gestioná canchas, horarios y pagos desde un panel simple.</p>

      <div class="wv2-owner-cta-row wv2-reveal d3">
        <a href="{{ route('planes') }}" class="wv2-btn-primary">
          Registrá tu complejo
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="{{ route('para-complejos') }}" class="wv2-btn-outline">Conocé cómo funciona</a>
      </div>
    </div>

    <div class="wv2-owner-side wv2-reveal d2">
      <div class="wv2-owner-side-h">Qué te ofrecemos</div>
      <div class="wv2-owner-perks">
        <div class="wv2-owner-perk"><b>Panel</b>Gestioná reservas, canchas y pagos desde un solo lugar</div>
        <div class="wv2-owner-perk"><b>Online</b>Tu cancha disponible 24/7, sin tener que contestar el teléfono</div>
        <div class="wv2-owner-perk"><b>Prueba</b>Sumá tu complejo con días gratis. Sin permanencia.</div>
        <div class="wv2-owner-perk"><b>Soporte</b>Te acompañamos para que arranques tranquilo desde el día uno</div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     FAQ
     ═══════════════════════════════════════════════════════ --}}
<section id="faq" style="border-top:1px solid var(--bd)">
  <div class="wv2-sec-w">
    <div class="wv2-faq-grid">
      <div>
        <span class="wv2-eyebrow wv2-reveal">FAQ</span>
        <h2 class="wv2-sec-h wv2-reveal d1">Preguntas<br><i>frecuentes</i>.</h2>
        <p class="wv2-sec-sub wv2-reveal d2">Todo lo que necesitás saber antes de reservar. Si te queda alguna duda, escribinos.</p>
        <a href="https://wa.me/5491127279757?text={{ urlencode('Hola! Tengo una consulta sobre TuCancha.') }}" target="_blank" rel="noopener" class="wv2-btn-outline wv2-reveal d3" style="margin-top:12px;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 0 0 .611.611l4.458-1.495A11.943 11.943 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.34 0-4.508-.654-6.363-1.787l-.362-.222-3.75 1.257 1.257-3.75-.222-.362A9.935 9.935 0 0 1 2 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
          Escribinos por WhatsApp
        </a>
      </div>

      <div class="wv2-faq-list">
        <div class="wv2-faq-item wv2-reveal">
          <div class="wv2-faq-q">¿Tengo que pagar para usar TuCancha? <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg></div>
          <div class="wv2-faq-a">No. Usar la app es gratis. Solo pagás el valor de la cancha que reservás, igual que si llamaras al complejo directamente — sin recargos ocultos.</div>
        </div>
        <div class="wv2-faq-item open wv2-reveal">
          <div class="wv2-faq-q">¿Puedo cancelar una reserva si no puedo ir? <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg></div>
          <div class="wv2-faq-a">Sí. Cada complejo define su política de cancelación, pero la mayoría permite cancelar sin costo hasta horas antes del turno. Lo ves claramente en la página de la cancha antes de pagar.</div>
        </div>
        <div class="wv2-faq-item wv2-reveal">
          <div class="wv2-faq-q">¿Qué pasa si llueve el día de mi partido? <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg></div>
          <div class="wv2-faq-a">Si el complejo suspende por lluvia, se gestiona el reintegro o podés cambiar de fecha según la política del club. Si es cancha cubierta, siempre se juega.</div>
        </div>
        <div class="wv2-faq-item wv2-reveal">
          <div class="wv2-faq-q">¿Cómo funciona Falta Uno? <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg></div>
          <div class="wv2-faq-a">Si te faltan jugadores, abrís tu partido a la comunidad. Otros usuarios pueden sumarse y pagar su parte. Al final del partido, se califican entre todos para mantener la comunidad sana.</div>
        </div>
        <div class="wv2-faq-item wv2-reveal">
          <div class="wv2-faq-q">¿Puedo jugar aunque no tenga equipo? <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg></div>
          <div class="wv2-faq-a">Sí. Con Falta Uno te podés sumar a partidos que buscan jugadores. Ideal para conocer gente nueva y jugar todas las semanas.</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════════════════════════════════════════
     CLOSING BIG CTA
     ═══════════════════════════════════════════════════════ --}}
<section class="wv2-closing">
  <div class="wv2-closing-inner">
    <div class="wv2-big-closing wv2-reveal">
      Ponete los botines.<br><i>Nosotros nos encargamos del resto.</i>
    </div>
    <div class="wv2-closing-ctas wv2-reveal d1">
      <a href="{{ route('venues.index') }}" class="wv2-btn-primary">
        Buscar cancha
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
      <a href="{{ route('ranking.index') }}" class="wv2-btn-outline">Ver ranking de jugadores</a>
    </div>
  </div>
</section>

</div>{{-- /.wv2 --}}
@endsection

@push('scripts')
<script>
  (function() {
    // ── Scroll reveal ──
    var io = new IntersectionObserver(function(entries) {
      entries.forEach(function(e) {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
      });
    }, { threshold: 0.01 });
    document.querySelectorAll('.wv2-reveal').forEach(function(el) {
      var r = el.getBoundingClientRect();
      if (r.top < window.innerHeight && r.bottom > 0) {
        requestAnimationFrame(function() { el.classList.add('in'); });
      } else {
        io.observe(el);
      }
    });

    // ── FAQ toggles ──
    document.querySelectorAll('.wv2-faq-item').forEach(function(item) {
      item.querySelector('.wv2-faq-q').addEventListener('click', function() {
        item.classList.toggle('open');
      });
    });

    // ── Quick chip highlight feedback ──
    document.querySelectorAll('.wv2-quick-chip').forEach(function(c) {
      c.addEventListener('click', function() {
        c.style.background = 'var(--accent)';
        c.style.color = 'var(--accent-ink)';
        c.style.borderColor = 'var(--accent)';
      });
    });
  })();
</script>
@endpush
