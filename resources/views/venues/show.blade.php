@extends('layouts.app')

@section('title', $venue->name . ' — TuCancha')
@section('meta_description', ($venue->description ? Str::limit($venue->description, 155) : 'Reserva canchas en ' . $venue->name . '. ' . ($venue->address ?? '') . ' Consulta disponibilidad y confirma tu turno online en TuCancha.'))
@section('og_title', $venue->name . ' — Reservas en TuCancha')
@section('og_description', ($venue->description ? Str::limit($venue->description, 155) : 'Reserva canchas en ' . $venue->name . '. Consulta disponibilidad y confirma tu turno online.'))
@if($venue->cover_image_path)
  @section('og_image', Storage::url($venue->cover_image_path))
@endif

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet">
<style>
  /* ================================================================
     VENUE SHOW — Dark Editorial Theme
     Font: Sora (layout) + Instrument Serif (titles/numbers italic)
     Accent: #22c55e (app-wide brand green)
     ================================================================ */
  .vs-scope {
    --vs-bg: #050505;
    --vs-bg-1: #0a0a0a;
    --vs-bg-2: #111;
    --vs-bg-3: #161616;
    --vs-bd: rgba(255,255,255,.07);
    --vs-bd-2: rgba(255,255,255,.12);
    --vs-tx: #f2f2f2;
    --vs-tx-2: #c8c8c8;
    --vs-tx-3: #8a8a8a;
    --vs-tx-4: #555;
    --vs-accent: #22c55e;
    --vs-accent-ink: #052010;
    --vs-accent-hover: #4ade80;
    --vs-accent-soft: rgba(34,197,94,.12);
    --vs-gold: #d4b878;
  }
  .vs-scope { color: var(--vs-tx); }
  .vs-scope * { box-sizing: border-box; }

  /* ── Breadcrumb ───────────────────────────────────── */
  .vs-crumb {
    display: flex; align-items: center; gap: 8px;
    font-size: 12px; color: var(--vs-tx-3);
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  .vs-crumb a { color: var(--vs-tx-3); text-decoration: none; transition: color .15s; }
  .vs-crumb a:hover { color: var(--vs-tx); }
  .vs-crumb-sep { opacity: .4; }
  .vs-crumb .current { color: var(--vs-tx-2); }

  /* ── Hero head ────────────────────────────────────── */
  .vs-hero-head {
    display: flex; justify-content: space-between; align-items: flex-end;
    gap: 24px; margin-bottom: 20px; flex-wrap: wrap;
  }
  .vs-hero-title-block { min-width: 0; flex: 1; }
  .vs-verified {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 12px; color: var(--vs-gold); font-weight: 500;
    letter-spacing: .02em; margin-bottom: 12px;
    padding: 5px 12px 5px 10px;
    background: rgba(212,184,120,.07);
    border: 1px solid rgba(212,184,120,.18);
    border-radius: 999px;
  }
  .vs-hero-title {
    font-size: 56px; font-weight: 700;
    letter-spacing: -0.04em; line-height: .98;
    margin: 0 0 14px; color: var(--vs-tx);
  }
  .vs-hero-title .italic {
    font-family: 'Instrument Serif', 'Sora', serif;
    font-weight: 300; font-style: italic;
    letter-spacing: -0.02em;
  }
  .vs-hero-meta {
    display: flex; align-items: center; gap: 14px;
    font-size: 13px; color: var(--vs-tx-2);
    flex-wrap: wrap;
  }
  .vs-hero-meta-dot { width: 3px; height: 3px; border-radius: 50%; background: var(--vs-tx-4); }
  .vs-hero-meta .star { color: var(--vs-accent); }
  .vs-hero-meta u {
    text-decoration: underline; text-underline-offset: 3px;
    text-decoration-thickness: 1px; text-decoration-color: var(--vs-tx-4);
    cursor: pointer;
  }
  .vs-hero-meta u:hover { text-decoration-color: var(--vs-tx); }
  .vs-hero-meta b { color: var(--vs-tx); font-weight: 600; }

  .vs-hero-actions { display: flex; gap: 8px; flex-shrink: 0; }
  .vs-ghost {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px; border-radius: 999px;
    border: 1px solid var(--vs-bd-2);
    background: transparent; color: var(--vs-tx-2);
    font-size: 13px; font-weight: 500;
    cursor: pointer; text-decoration: none;
    transition: border-color .15s, color .15s, background .15s;
    font-family: 'Sora', sans-serif;
  }
  .vs-ghost:hover { border-color: var(--vs-tx-3); color: var(--vs-tx); background: rgba(255,255,255,.02); }

  /* ── Gallery (Airbnb style) ───────────────────────── */
  .vs-gallery {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    grid-template-rows: 260px 260px;
    gap: 6px;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 56px;
    position: relative;
    height: 526px; /* 260 + 6 + 260 */
  }
  .vs-gallery-img {
    position: relative;
    overflow: hidden;
    background: #1a1a1a;
    cursor: pointer;
    width: 100%;
    height: 100%;
    min-width: 0;
    min-height: 0;
  }
  .vs-gallery-img img {
    position: absolute;
    inset: 0;
    width: 100% !important;
    height: 100% !important;
    max-width: none !important;
    max-height: none !important;
    object-fit: cover;
    display: block;
    transition: transform .6s cubic-bezier(.2,.6,.2,1), filter .2s;
  }
  .vs-gallery-img:hover img { transform: scale(1.03); filter: brightness(1.05); }
  .vs-gallery-img.hero {
    grid-row: 1 / 3;
    grid-column: 1;
    height: 100%;
  }
  .vs-gallery-placeholder {
    width: 100%; height: 100%;
    background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.15);
  }
  .vs-gallery-badge {
    position: absolute; bottom: 16px; right: 16px;
    padding: 8px 14px; border-radius: 999px;
    background: rgba(5,5,5,.75);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.15);
    font-size: 12px; font-weight: 500;
    display: inline-flex; align-items: center; gap: 7px;
    color: var(--vs-tx);
  }

  /* ── Main grid ────────────────────────────────────── */
  .vs-main-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 64px;
    align-items: start;
  }

  /* ── Sections ─────────────────────────────────────── */
  .vs-sec { padding: 36px 0; border-top: 1px solid var(--vs-bd); }
  .vs-sec:first-child { border-top: 0; padding-top: 0; }
  .vs-eyebrow {
    display: inline-block;
    font-size: 11px; font-weight: 600;
    letter-spacing: .16em; text-transform: uppercase;
    color: var(--vs-tx-3);
    margin-bottom: 14px;
  }
  .vs-sec-title {
    font-size: 28px; font-weight: 700;
    letter-spacing: -0.03em; line-height: 1.1;
    margin: 0 0 8px; color: var(--vs-tx);
  }
  .vs-sec-title .italic {
    font-family: 'Instrument Serif', 'Sora', serif;
    font-weight: 300; font-style: italic; letter-spacing: -0.02em;
  }
  .vs-sec-sub {
    font-size: 14px; color: var(--vs-tx-3);
    margin: 0 0 28px; max-width: 520px; line-height: 1.6;
  }

  /* ── About (description + map) ────────────────────── */
  .vs-about {
    display: grid;
    grid-template-columns: 1.1fr .9fr;
    gap: 32px;
    align-items: stretch;
  }
  .vs-about-copy p {
    font-size: 15px; line-height: 1.75;
    color: var(--vs-tx-2);
    margin: 0 0 16px;
    text-wrap: pretty;
  }
  .vs-about-copy p:first-child::first-letter {
    font-family: 'Instrument Serif', serif;
    font-style: italic;
    font-size: 56px;
    float: left;
    line-height: .85;
    padding: 6px 10px 0 0;
    color: var(--vs-accent);
  }

  /* Illustrated map */
  .vs-map-card {
    border: 1px solid var(--vs-bd);
    border-radius: 18px;
    overflow: hidden;
    background: var(--vs-bg-2);
    display: flex; flex-direction: column;
    min-height: 320px;
  }
  .vs-map-canvas {
    position: relative; flex: 1;
    background:
      radial-gradient(ellipse at 30% 40%, rgba(34,197,94,.06), transparent 60%),
      linear-gradient(135deg, #0e1412 0%, #0a0f0a 100%);
    overflow: hidden;
    min-height: 220px;
  }
  .vs-map-grid {
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 40px 40px;
  }
  .vs-map-street { position: absolute; background: rgba(255,255,255,.07); }
  .vs-map-block {
    position: absolute;
    background: rgba(255,255,255,.025);
    border-radius: 2px;
  }
  .vs-map-pin {
    position: absolute; left: 50%; top: 50%;
    transform: translate(-50%, -100%);
    width: 40px; height: 40px;
    border-radius: 50% 50% 50% 0;
    background: var(--vs-accent);
    rotate: -45deg;
    box-shadow: 0 8px 24px rgba(34,197,94,.35), 0 0 0 6px rgba(34,197,94,.12);
    display: flex; align-items: center; justify-content: center;
    z-index: 2;
  }
  .vs-map-pin::after {
    content: '';
    width: 14px; height: 14px; border-radius: 50%;
    background: var(--vs-bg);
    rotate: 45deg;
  }
  .vs-map-pulse {
    position: absolute; left: 50%; top: 50%;
    transform: translate(-50%, -50%);
    width: 90px; height: 90px;
    border-radius: 50%;
    border: 1px solid var(--vs-accent);
    opacity: 0;
    animation: vs-pulse 2.4s ease-out infinite;
  }
  @keyframes vs-pulse {
    0% { transform: translate(-50%, -50%) scale(.4); opacity: .8; }
    100% { transform: translate(-50%, -50%) scale(1.8); opacity: 0; }
  }
  .vs-map-foot {
    padding: 18px 22px;
    border-top: 1px solid var(--vs-bd);
    display: flex; justify-content: space-between; align-items: flex-end;
    gap: 16px;
  }
  .vs-map-addr { font-size: 13px; color: var(--vs-tx); font-weight: 500; line-height: 1.5; }
  .vs-map-addr small { display: block; color: var(--vs-tx-3); font-weight: 400; margin-top: 2px; font-size: 12px; }
  .vs-map-links { display: flex; flex-direction: column; gap: 4px; align-items: flex-end; }
  .vs-map-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: var(--vs-accent); font-size: 12px; font-weight: 600;
    white-space: nowrap; text-decoration: none; cursor: pointer;
    background: none; border: none; padding: 0; font-family: 'Sora', sans-serif;
  }
  .vs-map-link:hover { color: var(--vs-accent-hover); }

  /* ── Stats ────────────────────────────────────────── */
  .vs-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    margin: 32px 0 40px;
    border-top: 1px solid var(--vs-bd);
    border-bottom: 1px solid var(--vs-bd);
  }
  .vs-stat {
    padding: 22px 4px 22px 24px;
    position: relative;
  }
  .vs-stat + .vs-stat { border-left: 1px solid var(--vs-bd); }
  .vs-stat-num {
    font-size: 34px; font-weight: 700;
    letter-spacing: -0.035em; line-height: 1;
    color: var(--vs-tx);
    display: flex; align-items: baseline; gap: 6px;
    font-family: 'Sora', sans-serif;
  }
  .vs-stat-num .unit {
    font-size: 14px; color: var(--vs-accent);
    font-weight: 600;
  }
  .vs-stat-label {
    font-size: 11px; font-weight: 500;
    color: var(--vs-tx-3);
    letter-spacing: .12em;
    text-transform: uppercase;
    margin-top: 8px;
  }
  .vs-stat-sub { font-size: 12px; color: var(--vs-tx-3); margin-top: 4px; font-weight: 400; }

  /* ── Amenities ────────────────────────────────────── */
  .vs-amen-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    background: var(--vs-bd);
    border: 1px solid var(--vs-bd);
    border-radius: 16px;
    overflow: hidden;
  }
  .vs-amen {
    padding: 20px;
    background: var(--vs-bg);
    display: flex; align-items: center; gap: 14px;
    font-size: 13px; font-weight: 500;
    color: var(--vs-tx);
    transition: background .15s;
  }
  .vs-amen:hover { background: var(--vs-bg-2); }
  .vs-amen-filler { pointer-events: none; }
  .vs-amen-filler:hover { background: var(--vs-bg); }
  .vs-amen-ico {
    width: 40px; height: 40px; border-radius: 10px;
    background: var(--vs-bg-2);
    border: 1px solid var(--vs-bd-2);
    display: flex; align-items: center; justify-content: center;
    color: var(--vs-tx-2);
    flex-shrink: 0;
  }
  .vs-amen-txt { min-width: 0; }
  .vs-amen-txt small { display: block; font-size: 11px; color: var(--vs-tx-3); font-weight: 400; margin-top: 2px; }

  /* ── Fields section ───────────────────────────────── */
  .vs-fields-head {
    display: flex; justify-content: space-between; align-items: flex-end;
    gap: 20px; margin-bottom: 24px; flex-wrap: wrap;
  }
  .vs-filters { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 20px; }
  .vs-chip-f {
    padding: 7px 14px; border-radius: 999px;
    border: 1px solid var(--vs-bd);
    background: transparent;
    color: var(--vs-tx-2);
    font-size: 12px; font-weight: 500;
    cursor: pointer;
    transition: all .15s;
    font-family: 'Sora', sans-serif;
  }
  .vs-chip-f:hover { border-color: var(--vs-bd-2); color: var(--vs-tx); }
  .vs-chip-f.active { background: var(--vs-tx); color: var(--vs-bg); border-color: var(--vs-tx); }

  .vs-field-list { display: flex; flex-direction: column; gap: 12px; }
  .vs-field-card {
    display: grid;
    grid-template-columns: 220px minmax(0, 1fr) minmax(170px, auto);
    gap: 28px;
    align-items: center;
    padding: 16px;
    border: 1px solid var(--vs-bd);
    border-radius: 20px;
    background: var(--vs-bg-1);
    transition: border-color .2s, background .2s;
  }
  .vs-field-card:hover {
    border-color: rgba(34,197,94,.25);
    background: var(--vs-bg-2);
  }
  .vs-field-img {
    display: block;
    width: 220px; height: 140px; border-radius: 12px;
    overflow: hidden;
    background: #1a1a1a;
    position: relative;
    text-decoration: none;
  }
  .vs-field-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s; }
  .vs-field-card:hover .vs-field-img img { transform: scale(1.05); }
  .vs-field-img-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: #333;
    background: linear-gradient(135deg, #151515, #0d0d0d);
  }
  .vs-field-img-tag {
    position: absolute; top: 10px; left: 10px;
    padding: 4px 9px;
    background: rgba(5,5,5,.7);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 999px;
    font-size: 10px; font-weight: 600;
    letter-spacing: .06em; text-transform: uppercase;
    color: var(--vs-tx);
  }

  .vs-field-body { min-width: 0; }
  .vs-field-name {
    font-size: 20px; font-weight: 600;
    letter-spacing: -0.02em;
    margin: 0 0 4px;
    color: var(--vs-tx);
  }
  .vs-field-name a {
    color: inherit;
    text-decoration: none;
    transition: color .15s;
  }
  .vs-field-name a:hover { color: var(--vs-accent); }
  .vs-field-kind {
    font-size: 13px; color: var(--vs-tx-3);
    margin-bottom: 14px;
    display: flex; align-items: center; gap: 10px;
  }
  .vs-field-kind .dot { width: 3px; height: 3px; border-radius: 50%; background: var(--vs-tx-4); }
  .vs-field-tags { display: flex; gap: 6px; flex-wrap: wrap; }
  .vs-tag {
    padding: 4px 10px; border-radius: 999px;
    font-size: 11px; font-weight: 500;
    background: rgba(255,255,255,.04);
    color: var(--vs-tx-2);
    border: 1px solid var(--vs-bd);
    display: inline-flex; align-items: center; gap: 5px;
  }
  .vs-tag-fu {
    background: rgba(34,197,94,.1);
    color: #6ee7a0;
    border-color: rgba(34,197,94,.25);
  }
  .vs-fu-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--vs-accent);
    animation: vs-blink 1.8s ease infinite;
  }
  @keyframes vs-blink { 0%,100% { opacity: 1; } 50% { opacity: .4; } }

  .vs-field-cta {
    display: flex; flex-direction: column; align-items: flex-end;
    gap: 10px;
    padding-right: 8px;
    min-width: 160px;
  }
  .vs-price { font-size: 13px; color: var(--vs-tx-3); text-align: right; }
  .vs-price b {
    display: block;
    font-size: 24px; font-weight: 700;
    letter-spacing: -0.02em;
    color: var(--vs-tx);
    margin-bottom: 1px;
  }
  .vs-price b span { color: var(--vs-tx-3); font-weight: 400; font-size: 13px; margin-right: 1px; }
  .vs-price small { font-size: 11px; color: var(--vs-tx-3); }

  .vs-reserve-btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 8px;
    padding: 11px 20px;
    background: var(--vs-accent);
    color: var(--vs-accent-ink);
    border-radius: 999px;
    font-weight: 600;
    font-size: 13px;
    border: none;
    cursor: pointer;
    transition: background .15s, transform .15s;
    white-space: nowrap;
    text-decoration: none;
    font-family: 'Sora', sans-serif;
  }
  .vs-reserve-btn:hover { background: var(--vs-accent-hover); color: var(--vs-accent-ink); transform: translateY(-1px); }

  .vs-fu-btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 6px; padding: 7px 14px;
    border: 1px solid rgba(34,197,94,.25);
    background: rgba(34,197,94,.08);
    color: #6ee7a0;
    border-radius: 999px;
    font-size: 11px; font-weight: 600;
    text-decoration: none;
    transition: background .15s, color .15s;
    font-family: 'Sora', sans-serif;
  }
  .vs-fu-btn:hover { background: rgba(34,197,94,.15); color: #86efac; }

  /* ── Reviews ──────────────────────────────────────── */
  .vs-review-wrap {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 48px;
    align-items: start;
  }
  .vs-big-score {
    display: flex; align-items: baseline; gap: 8px;
    margin-bottom: 16px;
  }
  .vs-big-score-num {
    font-size: 80px; font-weight: 700;
    line-height: .9;
    letter-spacing: -0.05em;
    color: var(--vs-tx);
    font-family: 'Sora', sans-serif;
  }
  .vs-big-score-out { font-size: 18px; color: var(--vs-tx-3); font-weight: 400; }
  .vs-big-score-stars { font-size: 16px; letter-spacing: 2px; color: var(--vs-accent); margin-bottom: 6px; }
  .vs-big-score-count { font-size: 13px; color: var(--vs-tx-3); margin-bottom: 24px; }
  .vs-big-score-count b { color: var(--vs-tx); font-weight: 600; }

  .vs-dist-row {
    display: grid;
    grid-template-columns: 24px 1fr 32px;
    gap: 12px;
    align-items: center;
    padding: 6px 0;
    font-size: 12px;
    color: var(--vs-tx-3);
  }
  .vs-dist-bar { height: 4px; background: rgba(255,255,255,.06); border-radius: 999px; overflow: hidden; }
  .vs-dist-bar-fill { height: 100%; background: var(--vs-accent); border-radius: 999px; transition: width .6s cubic-bezier(.2,.6,.2,1); }
  .vs-dist-num { text-align: right; font-variant-numeric: tabular-nums; color: var(--vs-tx-2); }

  .vs-write-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 18px; border-radius: 999px;
    border: 1px solid var(--vs-bd-2);
    background: transparent;
    color: var(--vs-tx);
    font-size: 13px; font-weight: 500;
    cursor: pointer;
    margin-top: 24px;
    font-family: 'Sora', sans-serif;
  }
  .vs-write-btn:hover { border-color: var(--vs-accent); color: var(--vs-accent); }

  .vs-review-items { display: flex; flex-direction: column; gap: 0; }
  .vs-review-item {
    padding: 24px 0;
    border-top: 1px solid var(--vs-bd);
  }
  .vs-review-item:first-child { border-top: 0; padding-top: 0; }
  .vs-rev-head { display: flex; align-items: center; gap: 14px; margin-bottom: 12px; }
  .vs-rev-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: linear-gradient(135deg, #222, #111);
    display: flex; align-items: center; justify-content: center;
    color: var(--vs-gold);
    font-weight: 600; font-size: 15px;
    flex-shrink: 0;
    border: 1px solid var(--vs-bd);
    overflow: hidden;
  }
  .vs-rev-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .vs-rev-name { font-size: 14px; font-weight: 600; color: var(--vs-tx); }
  .vs-rev-date { font-size: 12px; color: var(--vs-tx-3); margin-top: 2px; }
  .vs-rev-stars { margin-left: auto; color: var(--vs-accent); font-size: 13px; letter-spacing: 2px; white-space: nowrap; }
  .vs-rev-body {
    font-size: 14px; line-height: 1.7;
    color: var(--vs-tx-2);
    margin: 0;
    text-wrap: pretty;
  }

  /* Review form */
  .vs-review-form-wrap {
    background: var(--vs-bg-1);
    border: 1px solid var(--vs-bd);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 24px;
  }
  .vs-form-label {
    display: block;
    font-size: 11px; color: var(--vs-tx-3);
    margin-bottom: 8px; font-weight: 600;
    text-transform: uppercase; letter-spacing: .08em;
  }
  .vs-textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid var(--vs-bd-2);
    border-radius: 10px;
    background: var(--vs-bg-2);
    color: var(--vs-tx);
    font-size: 14px;
    resize: vertical;
    font-family: 'Sora', sans-serif;
    outline: none;
    transition: border-color .15s;
    box-sizing: border-box;
  }
  .vs-textarea:focus { border-color: var(--vs-accent); }
  .vs-star-picker { display: flex; gap: 4px; }
  .vs-star-picker button {
    background: none; border: none;
    font-size: 24px; color: #3a3a3a;
    cursor: pointer; padding: 0;
    transition: color .1s, transform .1s;
    line-height: 1;
  }
  .vs-star-picker button:hover { transform: scale(1.15); }
  .vs-star-picker button.active { color: var(--vs-accent); }

  /* Empty review state */
  .vs-no-reviews {
    padding: 48px 32px;
    border: 1px dashed var(--vs-bd-2);
    border-radius: 16px;
    text-align: center;
  }
  .vs-no-reviews-icon {
    width: 56px; height: 56px;
    border-radius: 50%;
    background: var(--vs-accent-soft);
    color: var(--vs-accent);
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
  }
  .vs-no-reviews h4 {
    margin: 0 0 6px;
    font-size: 17px; font-weight: 600;
    color: var(--vs-tx); letter-spacing: -0.01em;
  }
  .vs-no-reviews p {
    margin: 0; font-size: 13px; color: var(--vs-tx-3);
    max-width: 340px; margin-inline: auto; line-height: 1.6;
  }

  /* ── Sticky rail ──────────────────────────────────── */
  .vs-rail {
    position: sticky;
    top: 88px;
    border: 1px solid var(--vs-bd);
    border-radius: 20px;
    padding: 24px;
    background: linear-gradient(180deg, var(--vs-bg-2) 0%, var(--vs-bg-1) 100%);
  }
  .vs-rail-head {
    display: flex; justify-content: space-between; align-items: baseline;
    margin-bottom: 18px;
  }
  .vs-rail-price { font-size: 13px; color: var(--vs-tx-3); }
  .vs-rail-price b {
    display: inline; font-weight: 700;
    color: var(--vs-tx); font-size: 22px;
    letter-spacing: -0.02em; margin-right: 4px;
  }
  .vs-rail-price b span { font-weight: 400; font-size: 12px; color: var(--vs-tx-3); margin-right: 1px; }
  .vs-rail-rating { font-size: 12px; color: var(--vs-tx-2); display: inline-flex; align-items: center; gap: 5px; }
  .vs-rail-rating b { color: var(--vs-tx); font-weight: 600; }
  .vs-rail-new { font-size: 11px; color: var(--vs-accent); font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }

  .vs-rail-cta {
    width: 100%;
    padding: 14px 16px;
    background: var(--vs-accent);
    color: var(--vs-accent-ink);
    border: none;
    border-radius: 12px;
    font-size: 14px; font-weight: 700;
    cursor: pointer;
    transition: background .15s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    text-decoration: none;
    font-family: 'Sora', sans-serif;
    margin-bottom: 8px;
  }
  .vs-rail-cta:hover { background: var(--vs-accent-hover); color: var(--vs-accent-ink); }
  .vs-rail-cta-alt {
    width: 100%;
    padding: 11px 16px;
    background: transparent;
    color: var(--vs-tx-2);
    border: 1px solid var(--vs-bd-2);
    border-radius: 12px;
    font-size: 13px; font-weight: 600;
    cursor: pointer;
    transition: border-color .15s, color .15s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    text-decoration: none;
    font-family: 'Sora', sans-serif;
  }
  .vs-rail-cta-alt:hover { border-color: var(--vs-tx-3); color: var(--vs-tx); }

  .vs-rail-bullets {
    margin-top: 22px;
    padding-top: 22px;
    border-top: 1px solid var(--vs-bd);
    display: flex; flex-direction: column; gap: 11px;
  }
  .vs-rail-bullet {
    display: flex; align-items: flex-start; gap: 10px;
    font-size: 12px; color: var(--vs-tx-2); line-height: 1.5;
  }
  .vs-rail-bullet svg { flex-shrink: 0; margin-top: 1px; color: var(--vs-accent); }

  /* ── Responsive ───────────────────────────────────── */
  @media (max-width: 1100px) {
    .vs-main-grid { grid-template-columns: 1fr; gap: 40px; }
    .vs-rail { position: static; }
    .vs-review-wrap { grid-template-columns: 1fr; gap: 32px; }
    .vs-about { grid-template-columns: 1fr; gap: 24px; }
    .vs-amen-grid { grid-template-columns: repeat(2, 1fr); }
    .vs-stats { grid-template-columns: repeat(2, 1fr); }
    .vs-stat:nth-child(3) { border-left: 0; border-top: 1px solid var(--vs-bd); }
  }
  @media (max-width: 768px) {
    .vs-hero-title { font-size: 38px; }
    .vs-hero-actions { width: 100%; justify-content: flex-start; }
    .vs-gallery { grid-template-columns: 1fr 1fr; grid-template-rows: 180px 180px; }
    .vs-gallery-img.hero { grid-row: 1; grid-column: 1 / 3; }
    .vs-gallery-img:nth-child(4), .vs-gallery-img:nth-child(5) { display: none; }
    .vs-field-card { grid-template-columns: 1fr; gap: 14px; padding: 14px; }
    .vs-field-img { width: 100%; height: 180px; }
    .vs-field-cta { align-items: stretch; min-width: 0; padding-right: 0; }
    .vs-price { text-align: left; }
    .vs-reserve-btn { width: 100%; }
    .vs-sec-title { font-size: 22px; }
    .vs-big-score-num { font-size: 64px; }
  }
  @media (max-width: 480px) {
    .vs-stats { grid-template-columns: 1fr 1fr; }
    .vs-amen-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
@php
  use App\Http\Controllers\VenueAdmin\VenueController;
  $allAmenities   = VenueController::amenitiesList();
  $venueAmenities = $venue->amenities ?? [];
  $activeFields   = $venue->fields->where('is_active', true);
  $sports         = $activeFields->pluck('sport')->unique()->filter()->values();
  $roundedAverage = round($averageRating);
  $reviewsCount   = $venue->reviews->count();
  $sportLabel = fn($s) => match($s) { 'football'=>'Fútbol','padel'=>'Pádel','tennis'=>'Tenis','basketball'=>'Básquet','volleyball'=>'Vóley', default=>ucfirst($s) };
  $sportIcon  = fn($s) => match($s) { 'football'=>'⚽','padel'=>'🎾','tennis'=>'🎾','basketball'=>'🏀','volleyball'=>'🏐', default=>'🏟️' };

  // Amenity SVG icons (stroke-based, consistent weight)
  $amenityIcons = [
    'wifi'            => '<path d="M5 12.55a11 11 0 0 1 14 0"/><path d="M8.5 16.05a6 6 0 0 1 7 0"/><path d="M2 8.82a15 15 0 0 1 20 0"/><line x1="12" x2="12.01" y1="20" y2="20"/>',
    'estacionamiento' => '<rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>',
    'vestuarios'      => '<path d="M7 10V7a5 5 0 0 1 10 0v3"/><rect x="3" y="10" width="18" height="11" rx="2"/>',
    'parrilla'        => '<path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>',
    'bar'             => '<path d="M8 22h8"/><path d="M7 10h10"/><path d="M12 15v7"/><path d="M12 15a5 5 0 0 0 5-5c0-2-.5-4-2-8H9c-1.5 4-2 6-2 8a5 5 0 0 0 5 5z"/>',
    'techado'         => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
    'iluminacion'     => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>',
    'gradas'          => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    'escuelita'       => '<circle cx="12" cy="12" r="10"/><path d="m12 2 2 7h7l-5.5 4.5L17 21l-5-4-5 4 1.5-7.5L3 9h7Z"/>',
    'beelup'          => '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/>',
    'alquiler_pelota' => '<circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20"/><path d="M12 2a14.5 14.5 0 0 1 0 20"/><path d="M2 12h20"/>',
    'accesible'       => '<circle cx="12" cy="4" r="2"/><path d="M19 13v-2a7 7 0 0 0-14 0v2"/><path d="m7 17 2-3h6l2 5"/><circle cx="12" cy="18" r="4"/>',
  ];

  // Price range
  $prices = $activeFields->map(fn($f) => (float)($f->price->price_per_slot ?? 0))->filter(fn($p) => $p > 0);
  $minPrice = $prices->min();

  // Gallery images: venue cover (hero) + venue gallery_paths (up to 4 thumbs)
  $galleryImages = collect();
  if ($venue->cover_image_path) {
    $galleryImages->push(\Illuminate\Support\Facades\Storage::url($venue->cover_image_path));
  }
  if (is_array($venue->gallery_paths)) {
    foreach ($venue->gallery_paths as $gp) {
      if ($gp) {
        $galleryImages->push(\Illuminate\Support\Facades\Storage::url($gp));
      }
      if ($galleryImages->count() >= 5) break;
    }
  }
  // Fallback: si no hay cover ni gallery, completamos con field covers para no dejar la galería vacía
  if ($galleryImages->count() < 5) {
    foreach ($activeFields as $f) {
      if ($f->cover_image_path) {
        $galleryImages->push(\Illuminate\Support\Facades\Storage::url($f->cover_image_path));
      }
      if ($galleryImages->count() >= 5) break;
    }
  }

  // Rating distribution
  $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
  foreach ($venue->reviews as $r) {
    $star = (int) round($r->rating);
    if (isset($distribution[$star])) $distribution[$star]++;
  }

  // Breadcrumb zone
  $zoneForCrumb = $venue->zone ?: ($venue->address ?? 'Complejos');

  // WhatsApp share
  $waMsg = $venue->name . "\n"
         . ($venue->address ? $venue->address . "\n" : '')
         . "Deportes: " . $sports->map(fn($s) => $sportLabel($s))->implode(', ') . "\n\n"
         . "Reserva en " . route('venues.show', $venue);
@endphp

<div class="vs-scope">

{{-- ════════════════════════════════════════════════════════
     BREADCRUMB
     ════════════════════════════════════════════════════════ --}}
<nav class="vs-crumb" aria-label="Ruta">
  <a href="{{ route('venues.index') }}">Complejos</a>
  <span class="vs-crumb-sep">›</span>
  @if($venue->zone)
    <a href="{{ route('venues.index', ['zone' => $venue->zone]) }}">{{ $venue->zone }}</a>
    <span class="vs-crumb-sep">›</span>
  @endif
  <span class="current">{{ $venue->name }}</span>
</nav>

{{-- ════════════════════════════════════════════════════════
     HERO HEAD
     ════════════════════════════════════════════════════════ --}}
<div class="vs-hero-head">
  <div class="vs-hero-title-block">
    <span class="vs-verified">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2 3 6v6c0 5 4 9 9 10 5-1 9-5 9-10V6l-9-4Zm-1 14-4-4 1.4-1.4L11 13.2l5.6-5.6L18 9l-7 7Z"/></svg>
      Complejo verificado · Partner TuCancha
    </span>
    @php
      // Split name to render last word italic for a premium editorial feel
      $parts = explode(' ', trim($venue->name));
      $lastWord = array_pop($parts);
      $firstWords = implode(' ', $parts);
    @endphp
    <h1 class="vs-hero-title">
      @if($firstWords !== '')
        {{ $firstWords }} <span class="italic">{{ $lastWord }}</span>
      @else
        <span class="italic">{{ $lastWord }}</span>
      @endif
    </h1>
    <div class="vs-hero-meta">
      @if($reviewsCount > 0)
        <span class="star">★</span>
        <span><b>{{ $averageRating }}</b> · <u onclick="vsScrollTo('reviews')">{{ $reviewsCount }} {{ $reviewsCount === 1 ? 'reseña' : 'reseñas' }}</u></span>
        <span class="vs-hero-meta-dot"></span>
      @else
        <span style="color: var(--vs-accent); font-weight: 600; letter-spacing: .06em; text-transform: uppercase; font-size: 11px;">✦ Nuevo en TuCancha</span>
        <span class="vs-hero-meta-dot"></span>
      @endif
      @if($venue->address)
        <span><u onclick="vsScrollTo('map')">{{ $venue->address }}</u></span>
      @endif
      @if($venue->zone)
        <span class="vs-hero-meta-dot"></span>
        <span style="color: var(--vs-tx-3);">{{ $venue->zone }}</span>
      @endif
    </div>
  </div>
  <div class="vs-hero-actions">
    <a href="https://wa.me/?text={{ urlencode($waMsg) }}" target="_blank" rel="noopener" class="vs-ghost">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/></svg>
      Compartir
    </a>
    @auth
      @if(auth()->user()->favoriteVenues()->where('venues.id', $venue->id)->exists())
        <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
          @csrf
          <button type="submit" class="vs-ghost">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="#ef4444" stroke="#ef4444" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            Guardado
          </button>
        </form>
      @else
        <form method="POST" action="{{ route('venues.favorite', $venue) }}" style="margin:0;">
          @csrf
          <button type="submit" class="vs-ghost">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            Guardar
          </button>
        </form>
      @endif
    @endauth
  </div>
</div>

{{-- ════════════════════════════════════════════════════════
     GALLERY (Airbnb style)
     ════════════════════════════════════════════════════════ --}}
<div class="vs-gallery">
  {{-- Hero image --}}
  <div class="vs-gallery-img hero">
    @if($galleryImages->count() > 0)
      <img src="{{ $galleryImages->first() }}" alt="{{ $venue->name }}" loading="eager">
    @else
      <div class="vs-gallery-placeholder">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
      </div>
    @endif
  </div>

  {{-- 4 thumbnails --}}
  @for($i = 1; $i <= 4; $i++)
    <div class="vs-gallery-img">
      @if($galleryImages->count() > $i)
        <img src="{{ $galleryImages[$i] }}" alt="{{ $venue->name }} - foto {{ $i + 1 }}" loading="lazy">
      @else
        <div class="vs-gallery-placeholder">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
        </div>
      @endif
      @if($i === 4 && $galleryImages->count() > 5)
        <div class="vs-gallery-badge">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
          Ver todas las fotos
        </div>
      @endif
    </div>
  @endfor
</div>

{{-- ════════════════════════════════════════════════════════
     MAIN GRID (content + sticky rail)
     ════════════════════════════════════════════════════════ --}}
<div class="vs-main-grid">

  {{-- ═══ LEFT CONTENT ═══ --}}
  <div>

    {{-- ABOUT --}}
    <section class="vs-sec" style="padding-top: 0; border-top: 0;">
      <span class="vs-eyebrow">El complejo</span>
      <h2 class="vs-sec-title"><span class="italic">Sobre</span> {{ $venue->name }}</h2>
      @if($venue->description)
        <p class="vs-sec-sub">Todo lo que necesitás saber antes de reservar.</p>
      @endif

      <div class="vs-about">
        <div class="vs-about-copy">
          @if($venue->description)
            <p>{{ $venue->description }}</p>
          @else
            <p style="color: var(--vs-tx-3); font-style: italic;">Este complejo todavía no agregó una descripción.</p>
          @endif
        </div>

        {{-- Illustrated map card --}}
        <div class="vs-map-card" id="map">
          <div class="vs-map-canvas">
            <div class="vs-map-grid"></div>
            <div class="vs-map-street" style="top:30%; left:0; right:0; height:2px;"></div>
            <div class="vs-map-street" style="top:65%; left:0; right:0; height:1px;"></div>
            <div class="vs-map-street" style="top:0; bottom:0; left:40%; width:2px;"></div>
            <div class="vs-map-street" style="top:0; bottom:0; left:75%; width:1px;"></div>
            <div class="vs-map-block" style="top:6%; left:6%; width:28%; height:18%;"></div>
            <div class="vs-map-block" style="top:6%; left:44%; width:26%; height:18%;"></div>
            <div class="vs-map-block" style="top:36%; left:6%; width:28%; height:24%;"></div>
            <div class="vs-map-block" style="top:70%; left:6%; width:28%; height:22%;"></div>
            <div class="vs-map-block" style="top:70%; left:44%; width:26%; height:22%;"></div>
            <div class="vs-map-block" style="top:36%; left:79%; width:16%; height:24%;"></div>
            <div class="vs-map-pulse"></div>
            <div class="vs-map-pin"></div>
          </div>
          <div class="vs-map-foot">
            <div class="vs-map-addr">
              {{ $venue->address ?: 'Ubicación' }}
              @if($venue->zone)
                <small>{{ $venue->zone }}</small>
              @endif
            </div>
            @if($venue->address)
              <div class="vs-map-links">
                <button onclick="vsOpenDirections()" class="vs-map-link">
                  Cómo llegar
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 17 17 7M9 7h8v8"/></svg>
                </button>
                <a href="https://maps.google.com/?q={{ urlencode($venue->address) }}" target="_blank" rel="noopener" class="vs-map-link">
                  Ver en Maps
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- STATS --}}
      <div class="vs-stats">
        <div class="vs-stat">
          <div class="vs-stat-num">{{ $activeFields->count() }}</div>
          <div class="vs-stat-label">Canchas</div>
          @if($sports->count() > 0)
            <div class="vs-stat-sub">{{ $sports->map(fn($s) => $sportLabel($s))->implode(' · ') }}</div>
          @endif
        </div>
        <div class="vs-stat">
          @if($reviewsCount > 0)
            <div class="vs-stat-num">{{ $averageRating }}<span class="unit">★</span></div>
            <div class="vs-stat-label">Rating</div>
            <div class="vs-stat-sub">{{ $reviewsCount }} {{ $reviewsCount === 1 ? 'reseña' : 'reseñas' }}</div>
          @else
            <div class="vs-stat-num"><span class="unit" style="font-size:22px;">✦</span></div>
            <div class="vs-stat-label">Nuevo</div>
            <div class="vs-stat-sub">Todavía sin reseñas</div>
          @endif
        </div>
        <div class="vs-stat">
          <div class="vs-stat-num">{{ $sports->count() ?: 0 }}</div>
          <div class="vs-stat-label">Deportes</div>
          <div class="vs-stat-sub">
            @if($sports->count() > 0)
              Disponibles hoy
            @else
              Sin canchas cargadas
            @endif
          </div>
        </div>
        <div class="vs-stat">
          @if($venue->cancellation_hours)
            <div class="vs-stat-num">{{ $venue->cancellation_hours }}<span class="unit">h</span></div>
            <div class="vs-stat-label">Cancelación</div>
            <div class="vs-stat-sub">Antes del turno</div>
          @else
            <div class="vs-stat-num">24<span class="unit">/7</span></div>
            <div class="vs-stat-label">Reservas</div>
            <div class="vs-stat-sub">Online en segundos</div>
          @endif
        </div>
      </div>

      {{-- AMENITIES --}}
      @php
        $validAmenities = collect($venueAmenities)->filter(fn($k) => isset($allAmenities[$k]))->values();
        $amenCount = $validAmenities->count();
        $fillerCount = $amenCount > 0 ? ((4 - ($amenCount % 4)) % 4) : 0;
      @endphp
      @if($amenCount > 0)
        <div>
          <h3 style="font-size:16px; font-weight:600; margin:0 0 18px; letter-spacing:-0.01em; color: var(--vs-tx);">Servicios del complejo</h3>
          <div class="vs-amen-grid">
            @foreach($validAmenities as $key)
              <div class="vs-amen">
                <div class="vs-amen-ico">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    {!! $amenityIcons[$key] ?? '<circle cx="12" cy="12" r="10"/>' !!}
                  </svg>
                </div>
                <div class="vs-amen-txt">{{ $allAmenities[$key]['label'] }}</div>
              </div>
            @endforeach
            @for($i = 0; $i < $fillerCount; $i++)
              <div class="vs-amen vs-amen-filler" aria-hidden="true"></div>
            @endfor
          </div>
        </div>
      @endif
    </section>

    {{-- FIELDS --}}
    <section class="vs-sec" id="canchas">
      <span class="vs-eyebrow">Reservas</span>
      <div class="vs-fields-head">
        <div>
          <h2 class="vs-sec-title"><span class="italic">Canchas</span> disponibles</h2>
          <p class="vs-sec-sub">Elegí una cancha y revisá la disponibilidad en tiempo real.</p>
        </div>
        @if($activeFields->count() > 0)
          <a href="{{ route('venues.weekly-calendar', $venue) }}" class="vs-ghost" style="padding:8px 14px; font-size:12px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            Ver agenda semanal
          </a>
        @endif
      </div>

      @if($activeFields->isEmpty())
        <div class="vs-no-reviews">
          <div class="vs-no-reviews-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><path d="M3 9h18M9 21V9"/></svg>
          </div>
          <h4>Todavía no hay canchas cargadas</h4>
          <p>Este complejo está preparando su espacio en TuCancha. Volvé pronto o explorá otros complejos.</p>
          <a href="{{ route('venues.index') }}" class="vs-reserve-btn" style="margin-top: 18px; display: inline-flex;">Ver otros complejos</a>
        </div>
      @else
        {{-- Sport filters --}}
        @if($sports->count() > 1)
          <div class="vs-filters">
            <button onclick="vsFilterSport(null)" class="vs-chip-f active" data-sport="">Todas <span style="opacity:.5; margin-left:4px;">{{ $activeFields->count() }}</span></button>
            @foreach($sports as $sport)
              @php $count = $activeFields->where('sport', $sport)->count(); @endphp
              <button onclick="vsFilterSport('{{ $sport }}')" class="vs-chip-f" data-sport="{{ $sport }}">
                {{ $sportIcon($sport) }} {{ $sportLabel($sport) }} <span style="opacity:.5; margin-left:4px;">{{ $count }}</span>
              </button>
            @endforeach
          </div>
        @endif

        <div class="vs-field-list">
          @foreach($activeFields as $field)
            <article class="vs-field-card" data-sport="{{ $field->sport }}">
              <a href="{{ route('fields.show', $field) }}" class="vs-field-img" aria-label="Ver {{ $field->name }}">
                @if($field->cover_image_path)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
                       alt="{{ $field->name }}"
                       loading="lazy">
                @else
                  <div class="vs-field-img-placeholder">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                  </div>
                @endif
                <span class="vs-field-img-tag">{{ $sportLabel($field->sport ?? '') }}</span>
              </a>

              <div class="vs-field-body">
                <h3 class="vs-field-name">
                  <a href="{{ route('fields.show', $field) }}">{{ $field->name }}</a>
                </h3>
                <div class="vs-field-kind">
                  @if($field->format)
                    <span>{{ $field->format }}v{{ $field->format }}</span>
                    <span class="dot"></span>
                  @endif
                  <span>{{ $field->slot_minutes }} min</span>
                </div>
                <div class="vs-field-tags">
                  @if($field->faltaUnoSetting?->enabled)
                    <span class="vs-tag vs-tag-fu">
                      <span class="vs-fu-dot"></span> Falta Uno
                    </span>
                  @endif
                  @if($venue->cancellation_hours)
                    <span class="vs-tag">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                      Cancela {{ $venue->cancellation_hours }}h antes
                    </span>
                  @endif
                </div>
              </div>

              <div class="vs-field-cta">
                <div class="vs-price">
                  <b><span>{{ $field->price->currency ?? 'ARS' }} </span>{{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }}</b>
                  <small>por turno</small>
                </div>
                <a href="{{ route('fields.show', $field) }}" class="vs-reserve-btn">
                  Ver disponibilidad
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </a>
                @if($field->faltaUnoSetting?->enabled)
                  <a href="{{ route('falta-uno.create', $field) }}" class="vs-fu-btn">
                    <span class="vs-fu-dot"></span> Iniciar Falta Uno
                  </a>
                @endif
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </section>

    {{-- REVIEWS --}}
    <section class="vs-sec" id="reviews">
      <span class="vs-eyebrow">Opiniones</span>
      <h2 class="vs-sec-title"><span class="italic">Reseñas</span> de la comunidad</h2>
      <p class="vs-sec-sub">Lo que dicen los jugadores que ya pasaron por este complejo.</p>

      {{-- Review form --}}
      @auth
        <div id="vsReviewFormWrap" style="display:none;">
          <div class="vs-review-form-wrap">
            <form method="POST" action="{{ route('venues.reviews.store', $venue) }}" style="display:grid; gap:16px; max-width:560px;">
              @csrf
              <div>
                <label class="vs-form-label">Puntuación</label>
                <input type="hidden" name="rating" id="vsRatingInput" value="{{ old('rating', '') }}" required>
                <div class="vs-star-picker" id="vsReviewStars">
                  @for($i = 1; $i <= 5; $i++)
                    <button type="button" data-value="{{ $i }}" aria-label="Puntuar con {{ $i }} estrella{{ $i > 1 ? 's' : '' }}">&#9733;</button>
                  @endfor
                </div>
                <div id="vsRatingText" style="color: var(--vs-tx-3); font-size:12px; margin-top:6px;">Seleccioná una puntuación</div>
                @error('rating')<div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
              </div>
              <div>
                <label class="vs-form-label">Comentario</label>
                <textarea name="comment" rows="3" class="vs-textarea" placeholder="Contá cómo fue tu experiencia...">{{ old('comment', '') }}</textarea>
                @error('comment')<div style="color:#ef4444; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
              </div>
              <div style="display:flex; gap:8px;">
                <button type="submit" class="vs-reserve-btn">Publicar reseña</button>
                <button type="button" onclick="vsToggleReviewForm(false)" class="vs-ghost">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      @endauth

      @if($reviewsCount > 0)
        <div class="vs-review-wrap">
          {{-- Left: big score + distribution --}}
          <div class="vs-review-summary">
            <div class="vs-big-score">
              <span class="vs-big-score-num">{{ number_format($averageRating, 2) }}</span>
              <span class="vs-big-score-out">/ 5</span>
            </div>
            <div class="vs-big-score-stars">
              @for($i = 1; $i <= 5; $i++){!! $i <= $roundedAverage ? '&#9733;' : '&#9734;' !!}@endfor
            </div>
            <div class="vs-big-score-count">Basado en <b>{{ $reviewsCount }}</b> {{ $reviewsCount === 1 ? 'reseña' : 'reseñas' }}</div>

            @foreach([5, 4, 3, 2, 1] as $star)
              @php
                $count = $distribution[$star];
                $pct = $reviewsCount > 0 ? ($count / $reviewsCount) * 100 : 0;
              @endphp
              <div class="vs-dist-row">
                <span>{{ $star }}★</span>
                <div class="vs-dist-bar"><div class="vs-dist-bar-fill" style="width: {{ $pct }}%;"></div></div>
                <span class="vs-dist-num">{{ $count }}</span>
              </div>
            @endforeach

            @auth
              <button type="button" class="vs-write-btn" onclick="vsToggleReviewForm()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                Escribir reseña
              </button>
            @endauth
          </div>

          {{-- Right: list --}}
          <div class="vs-review-items">
            @foreach($venue->reviews->sortByDesc('created_at') as $review)
              <div class="vs-review-item">
                <div class="vs-rev-head">
                  <div class="vs-rev-avatar">
                    @if($review->user->avatar_path)
                      <img src="{{ \Illuminate\Support\Facades\Storage::url($review->user->avatar_path) }}" alt="{{ $review->user->name }}">
                    @else
                      {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    @endif
                  </div>
                  <div>
                    <div class="vs-rev-name">{{ $review->user->name }}</div>
                    <div class="vs-rev-date">{{ $review->created_at->isoFormat('D [de] MMM, YYYY') }}</div>
                  </div>
                  <span class="vs-rev-stars">
                    @for($i = 1; $i <= 5; $i++){!! $i <= $review->rating ? '&#9733;' : '&#9734;' !!}@endfor
                  </span>
                </div>
                @if($review->comment)
                  <p class="vs-rev-body">{{ $review->comment }}</p>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @else
        <div class="vs-no-reviews">
          <div class="vs-no-reviews-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
          </div>
          <h4>Sé el primero en reseñar</h4>
          <p>Todavía nadie dejó su opinión sobre este complejo. Si jugaste acá, contanos cómo fue tu experiencia.</p>
          @auth
            <button type="button" class="vs-write-btn" onclick="vsToggleReviewForm()" style="margin-top: 20px;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
              Escribir la primera reseña
            </button>
          @else
            <a href="{{ route('login') }}" class="vs-write-btn" style="margin-top: 20px; text-decoration: none;">Iniciá sesión para reseñar</a>
          @endauth
        </div>
      @endif
    </section>
  </div>

  {{-- ═══ RIGHT STICKY RAIL ═══ --}}
  <aside>
    <div class="vs-rail">
      <div class="vs-rail-head">
        <div class="vs-rail-price">
          @if($minPrice)
            <b><span>ARS </span>{{ number_format($minPrice, 0, ',', '.') }}</b>
            <span style="display:block; margin-top:2px; font-size:11px;">desde · por turno</span>
          @else
            <b style="font-size:16px;">Próximamente</b>
          @endif
        </div>
        <div style="text-align: right;">
          @if($reviewsCount > 0)
            <span class="vs-rail-rating">
              <span style="color: var(--vs-accent);">★</span>
              <b>{{ $averageRating }}</b>
              <span style="color: var(--vs-tx-3);">({{ $reviewsCount }})</span>
            </span>
          @else
            <span class="vs-rail-new">✦ Nuevo</span>
          @endif
        </div>
      </div>

      @if($activeFields->count() > 0)
        <button type="button" onclick="vsScrollTo('canchas')" class="vs-rail-cta">
          Ver disponibilidad
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
        </button>
        <a href="{{ route('venues.weekly-calendar', $venue) }}" class="vs-rail-cta-alt">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
          Agenda semanal
        </a>
      @else
        <a href="{{ route('venues.index') }}" class="vs-rail-cta">Explorar otros complejos</a>
      @endif

      <div class="vs-rail-bullets">
        <div class="vs-rail-bullet">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
          Confirmación inmediata al pagar
        </div>
        <div class="vs-rail-bullet">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/></svg>
          Pago seguro con Mercado Pago
        </div>
        @if($venue->cancellation_hours)
          <div class="vs-rail-bullet">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
            Cancelación hasta {{ $venue->cancellation_hours }}h antes
          </div>
        @else
          <div class="vs-rail-bullet">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg>
            Política de cancelación flexible
          </div>
        @endif
      </div>
    </div>
  </aside>

</div>{{-- /.vs-main-grid --}}

</div>{{-- /.vs-scope --}}

@push('scripts')
<script>
  // ── Smooth scroll to section ──
  function vsScrollTo(id) {
    var el = document.getElementById(id);
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  // ── Directions ──
  function vsOpenDirections() {
    var dest = encodeURIComponent('{{ addslashes($venue->address ?? '') }}');
    if (!dest) return;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(pos) {
          window.open('https://www.google.com/maps/dir/?api=1&origin=' + pos.coords.latitude + ',' + pos.coords.longitude + '&destination=' + dest + '&travelmode=driving', '_blank');
        },
        function() {
          window.open('https://www.google.com/maps/dir/?api=1&destination=' + dest + '&travelmode=driving', '_blank');
        }
      );
    } else {
      window.open('https://www.google.com/maps/dir/?api=1&destination=' + dest + '&travelmode=driving', '_blank');
    }
  }

  // ── Sport filter ──
  function vsFilterSport(sport) {
    document.querySelectorAll('.vs-field-card[data-sport]').forEach(function(c) {
      c.style.display = (!sport || c.dataset.sport === sport) ? '' : 'none';
    });
    document.querySelectorAll('.vs-chip-f').forEach(function(b) {
      b.classList.toggle('active', b.dataset.sport === (sport || ''));
    });
  }

  // ── Review form toggle ──
  function vsToggleReviewForm(forceState) {
    var wrap = document.getElementById('vsReviewFormWrap');
    if (!wrap) return;
    if (forceState === false) { wrap.style.display = 'none'; return; }
    var isVisible = wrap.style.display === 'block';
    wrap.style.display = isVisible ? 'none' : 'block';
    if (!isVisible) {
      wrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
  }

  // ── Star picker ──
  (function () {
    var input = document.getElementById('vsRatingInput');
    var starsWrap = document.getElementById('vsReviewStars');
    var ratingText = document.getElementById('vsRatingText');
    if (!input || !starsWrap || !ratingText) return;
    var buttons = Array.from(starsWrap.querySelectorAll('button'));

    function paint(v) {
      buttons.forEach(function(b, i) { b.classList.toggle('active', i < v); });
      ratingText.innerText = v ? v + (v > 1 ? ' estrellas' : ' estrella') : 'Seleccioná una puntuación';
    }

    buttons.forEach(function(b) {
      b.addEventListener('click', function() {
        input.value = Number(b.dataset.value);
        paint(Number(b.dataset.value));
      });
    });

    paint(Number(input.value || 0));

    @if($errors->has('rating') || $errors->has('comment'))
      var wrap = document.getElementById('vsReviewFormWrap');
      if (wrap) wrap.style.display = 'block';
    @endif
  })();
</script>
@endpush

@endsection
