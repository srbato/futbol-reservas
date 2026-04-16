<!doctype html>
<html lang="es-AR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TuCancha — Design Inspiration</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    /* ================================================================
       RESET & BASE
       ================================================================ */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }

    body {
      font-family: 'Sora', system-ui, -apple-system, sans-serif;
      background: #050505;
      color: #e8e8e8;
      overflow-x: hidden;
      line-height: 1.6;
    }

    /* ================================================================
       NOISE TEXTURE OVERLAY
       ================================================================ */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      z-index: 9999;
      pointer-events: none;
      opacity: 0.025;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
      background-repeat: repeat;
      background-size: 256px 256px;
    }

    /* ================================================================
       CUSTOM CURSOR GLOW (desktop only)
       ================================================================ */
    .cursor-glow {
      position: fixed;
      width: 600px;
      height: 600px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(34, 197, 94, 0.06) 0%, transparent 70%);
      pointer-events: none;
      z-index: 1;
      transform: translate(-50%, -50%);
      transition: opacity 0.4s;
    }

    /* ================================================================
       SECTION 1 — HERO (full viewport, centered type)
       ================================================================ */
    .hero {
      position: relative;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 40px 24px;
      text-align: center;
      overflow: hidden;
    }

    .hero::before {
      content: '';
      position: absolute;
      top: -40%;
      left: 50%;
      transform: translateX(-50%);
      width: 900px;
      height: 900px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(34, 197, 94, 0.12) 0%, transparent 65%);
      filter: blur(80px);
      pointer-events: none;
    }

    .hero-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 18px 6px 8px;
      border-radius: 999px;
      border: 1px solid rgba(34, 197, 94, 0.25);
      background: rgba(34, 197, 94, 0.08);
      font-size: 13px;
      font-weight: 600;
      color: #6ee7a0;
      margin-bottom: 32px;
      animation: fadeDown 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .hero-badge-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #22c55e;
      box-shadow: 0 0 12px #22c55e;
      animation: pulse-dot 2s ease infinite;
    }

    @keyframes pulse-dot {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.5; transform: scale(0.8); }
    }

    .hero h1 {
      font-size: clamp(48px, 8vw, 96px);
      font-weight: 800;
      letter-spacing: -0.04em;
      line-height: 1;
      color: #fff;
      margin-bottom: 24px;
      animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
    }

    .hero h1 span {
      background: linear-gradient(135deg, #22c55e 0%, #6ee7a0 50%, #22c55e 100%);
      background-size: 200% 200%;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: shimmer 6s ease infinite;
    }

    @keyframes shimmer {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .hero-sub {
      font-size: clamp(16px, 2vw, 20px);
      font-weight: 400;
      color: #888;
      max-width: 560px;
      line-height: 1.7;
      margin-bottom: 40px;
      animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.2s both;
    }

    .hero-actions {
      display: flex;
      gap: 14px;
      flex-wrap: wrap;
      justify-content: center;
      animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
    }

    /* ── Buttons ──────────────────────────────────────── */
    .btn-glow {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 32px;
      border-radius: 14px;
      font-family: 'Sora', sans-serif;
      font-size: 15px;
      font-weight: 700;
      border: none;
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      text-decoration: none;
    }

    .btn-glow-primary {
      background: #22c55e;
      color: #fff;
      box-shadow: 0 0 0 0 rgba(34, 197, 94, 0), 0 4px 16px rgba(34, 197, 94, 0.3);
    }

    .btn-glow-primary:hover {
      background: #16a34a;
      transform: translateY(-2px);
      box-shadow: 0 0 30px rgba(34, 197, 94, 0.2), 0 8px 24px rgba(34, 197, 94, 0.4);
    }

    .btn-glow-ghost {
      background: rgba(255,255,255, 0.06);
      color: #ccc;
      border: 1px solid rgba(255,255,255, 0.1);
    }

    .btn-glow-ghost:hover {
      background: rgba(255,255,255, 0.1);
      border-color: rgba(255,255,255, 0.2);
      color: #fff;
      transform: translateY(-2px);
    }

    /* ── Scroll indicator ─────────────────────────────── */
    .scroll-hint {
      position: absolute;
      bottom: 40px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
      color: #555;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      animation: fadeUp 1s cubic-bezier(0.16, 1, 0.3, 1) 0.6s both;
    }

    .scroll-hint-line {
      width: 1px;
      height: 40px;
      background: linear-gradient(to bottom, rgba(34,197,94,0.5), transparent);
      animation: scroll-pulse 2s ease infinite;
    }

    @keyframes scroll-pulse {
      0%, 100% { opacity: 1; transform: scaleY(1); }
      50% { opacity: 0.3; transform: scaleY(0.6); }
    }

    /* ================================================================
       SECTION 2 — STATS RIBBON
       ================================================================ */
    .stats-ribbon {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1px;
      background: rgba(255,255,255,0.04);
      border-top: 1px solid rgba(255,255,255,0.06);
      border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .stat-cell {
      padding: 48px 24px;
      text-align: center;
      background: #050505;
      transition: background 0.3s;
    }

    .stat-cell:hover {
      background: rgba(34, 197, 94, 0.03);
    }

    .stat-number {
      font-size: clamp(36px, 5vw, 56px);
      font-weight: 800;
      letter-spacing: -0.03em;
      color: #fff;
      line-height: 1;
      margin-bottom: 8px;
    }

    .stat-number .accent { color: #22c55e; }

    .stat-label {
      font-size: 13px;
      font-weight: 500;
      color: #666;
      text-transform: uppercase;
      letter-spacing: 0.1em;
    }

    /* ================================================================
       SECTION 3 — BENTO GRID
       ================================================================ */
    .bento-section {
      padding: 120px 24px;
      max-width: 1200px;
      margin: 0 auto;
    }

    .section-eyebrow {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      color: #22c55e;
      margin-bottom: 16px;
    }

    .section-eyebrow::before {
      content: '';
      width: 24px;
      height: 2px;
      background: #22c55e;
      border-radius: 1px;
    }

    .section-title {
      font-size: clamp(32px, 5vw, 52px);
      font-weight: 800;
      letter-spacing: -0.03em;
      color: #fff;
      margin-bottom: 12px;
      line-height: 1.1;
    }

    .section-desc {
      font-size: 17px;
      color: #777;
      max-width: 520px;
      margin-bottom: 56px;
      line-height: 1.7;
    }

    .bento-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      grid-auto-rows: minmax(240px, auto);
      gap: 16px;
    }

    .bento-card {
      position: relative;
      border-radius: 24px;
      border: 1px solid rgba(255,255,255,0.06);
      background: rgba(255,255,255,0.02);
      padding: 36px;
      overflow: hidden;
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
    }

    .bento-card::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 24px;
      background: linear-gradient(135deg, rgba(34,197,94,0.05) 0%, transparent 60%);
      opacity: 0;
      transition: opacity 0.4s;
    }

    .bento-card:hover {
      border-color: rgba(34,197,94,0.2);
      transform: translateY(-4px);
      box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 40px rgba(34,197,94,0.05);
    }

    .bento-card:hover::before { opacity: 1; }

    .bento-span-2 { grid-column: span 2; }
    .bento-row-2 { grid-row: span 2; }

    .bento-icon {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      background: rgba(34,197,94,0.1);
      border: 1px solid rgba(34,197,94,0.15);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      position: relative;
      z-index: 2;
    }

    .bento-card h3 {
      font-size: 20px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 8px;
      position: relative;
      z-index: 2;
    }

    .bento-card p {
      font-size: 14px;
      color: #777;
      line-height: 1.6;
      position: relative;
      z-index: 2;
    }

    /* Featured card (the big one) */
    .bento-featured {
      background: linear-gradient(165deg, rgba(34,197,94,0.08) 0%, rgba(34,197,94,0.02) 40%, rgba(0,0,0,0) 100%);
      border-color: rgba(34,197,94,0.12);
    }

    .bento-featured h3 {
      font-size: 28px;
      letter-spacing: -0.02em;
    }

    /* Mini graph inside a card */
    .bento-graph {
      display: flex;
      align-items: flex-end;
      gap: 4px;
      height: 80px;
      margin-top: auto;
      margin-bottom: 20px;
      position: relative;
      z-index: 2;
    }

    .bento-bar {
      flex: 1;
      border-radius: 4px 4px 0 0;
      background: rgba(34,197,94,0.2);
      transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
      min-height: 8px;
    }

    .bento-card:hover .bento-bar { background: rgba(34,197,94,0.4); }

    /* ================================================================
       SECTION 4 — GLASS CARDS (horizontal scroll)
       ================================================================ */
    .glass-section {
      padding: 120px 0;
      position: relative;
    }

    .glass-section .section-eyebrow,
    .glass-section .section-title,
    .glass-section .section-desc {
      padding-left: 24px;
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
    }

    .glass-section .section-desc { margin-bottom: 48px; }

    .glass-scroll {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      padding: 0 24px 24px;
      scroll-snap-type: x mandatory;
      scrollbar-width: none;
      -webkit-overflow-scrolling: touch;
    }

    .glass-scroll::-webkit-scrollbar { display: none; }

    .glass-card {
      flex: 0 0 340px;
      scroll-snap-align: start;
      border-radius: 28px;
      border: 1px solid rgba(255,255,255,0.08);
      background: rgba(255,255,255,0.03);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      padding: 32px;
      transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      overflow: hidden;
    }

    .glass-card::after {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: conic-gradient(from 0deg, transparent 0%, rgba(34,197,94,0.05) 10%, transparent 20%);
      animation: rotate-bg 12s linear infinite;
      opacity: 0;
      transition: opacity 0.4s;
    }

    .glass-card:hover::after { opacity: 1; }

    @keyframes rotate-bg {
      to { transform: rotate(360deg); }
    }

    .glass-card:hover {
      border-color: rgba(34,197,94,0.2);
      transform: translateY(-6px) scale(1.01);
      box-shadow: 0 24px 48px rgba(0,0,0,0.4);
    }

    .glass-avatar {
      width: 56px;
      height: 56px;
      border-radius: 16px;
      background: linear-gradient(135deg, #22c55e, #16a34a);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      font-weight: 800;
      color: #fff;
      margin-bottom: 20px;
      position: relative;
      z-index: 2;
    }

    .glass-card-title {
      font-size: 18px;
      font-weight: 700;
      color: #fff;
      margin-bottom: 6px;
      position: relative;
      z-index: 2;
    }

    .glass-card-meta {
      font-size: 13px;
      color: #666;
      margin-bottom: 16px;
      position: relative;
      z-index: 2;
    }

    .glass-card-body {
      font-size: 14px;
      color: #999;
      line-height: 1.7;
      position: relative;
      z-index: 2;
    }

    .glass-card-tags {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
      margin-top: 18px;
      position: relative;
      z-index: 2;
    }

    .glass-tag {
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 600;
      border: 1px solid rgba(255,255,255,0.08);
      color: #aaa;
      background: rgba(255,255,255,0.04);
    }

    .glass-tag-green {
      border-color: rgba(34,197,94,0.2);
      color: #6ee7a0;
      background: rgba(34,197,94,0.08);
    }

    /* Star rating */
    .glass-stars {
      display: flex;
      gap: 2px;
      margin-bottom: 14px;
      position: relative;
      z-index: 2;
    }

    .glass-star {
      width: 16px;
      height: 16px;
      color: #22c55e;
    }

    /* ================================================================
       SECTION 5 — FEATURE SPLIT (text + visual)
       ================================================================ */
    .split-section {
      max-width: 1200px;
      margin: 0 auto;
      padding: 120px 24px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 80px;
      align-items: center;
    }

    .split-text { max-width: 480px; }

    .split-visual {
      position: relative;
      border-radius: 32px;
      overflow: hidden;
      background: rgba(255,255,255,0.02);
      border: 1px solid rgba(255,255,255,0.06);
      padding: 40px;
      min-height: 420px;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    /* Animated mockup inside split */
    .mockup-slot {
      display: flex;
      align-items: center;
      gap: 16px;
      padding: 16px 20px;
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,0.06);
      background: rgba(255,255,255,0.03);
      margin-bottom: 12px;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .mockup-slot:hover {
      border-color: rgba(34,197,94,0.2);
      background: rgba(34,197,94,0.04);
      transform: translateX(4px);
    }

    .mockup-time {
      font-size: 20px;
      font-weight: 800;
      color: #fff;
      min-width: 64px;
      letter-spacing: -0.02em;
    }

    .mockup-info { flex: 1; }

    .mockup-field {
      font-size: 14px;
      font-weight: 600;
      color: #ccc;
      margin-bottom: 2px;
    }

    .mockup-venue {
      font-size: 12px;
      color: #666;
    }

    .mockup-price {
      font-size: 15px;
      font-weight: 700;
      color: #22c55e;
    }

    .mockup-status {
      padding: 4px 12px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
    }

    .status-available {
      background: rgba(34,197,94,0.12);
      color: #6ee7a0;
      border: 1px solid rgba(34,197,94,0.2);
    }

    .status-few {
      background: rgba(245,179,1,0.12);
      color: #fbbf24;
      border: 1px solid rgba(245,179,1,0.2);
    }

    .status-taken {
      background: rgba(239,68,68,0.1);
      color: #f87171;
      border: 1px solid rgba(239,68,68,0.15);
    }

    /* Check list for split section */
    .check-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 14px;
      margin-top: 28px;
    }

    .check-list li {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 15px;
      color: #bbb;
    }

    .check-icon {
      width: 24px;
      height: 24px;
      border-radius: 8px;
      background: rgba(34,197,94,0.1);
      border: 1px solid rgba(34,197,94,0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    /* ================================================================
       SECTION 6 — CTA BANNER
       ================================================================ */
    .cta-section {
      padding: 120px 24px;
    }

    .cta-card {
      max-width: 900px;
      margin: 0 auto;
      position: relative;
      border-radius: 32px;
      overflow: hidden;
      padding: 80px 48px;
      text-align: center;
      background: linear-gradient(165deg, rgba(34,197,94,0.12) 0%, rgba(34,197,94,0.03) 50%, rgba(0,0,0,0) 100%);
      border: 1px solid rgba(34,197,94,0.12);
    }

    .cta-card::before {
      content: '';
      position: absolute;
      top: -200px;
      right: -200px;
      width: 500px;
      height: 500px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(34,197,94,0.1) 0%, transparent 65%);
      filter: blur(60px);
    }

    .cta-card::after {
      content: '';
      position: absolute;
      bottom: -200px;
      left: -200px;
      width: 500px;
      height: 500px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(34,197,94,0.06) 0%, transparent 65%);
      filter: blur(60px);
    }

    .cta-card h2 {
      font-size: clamp(28px, 4vw, 44px);
      font-weight: 800;
      letter-spacing: -0.03em;
      color: #fff;
      margin-bottom: 16px;
      position: relative;
      z-index: 2;
    }

    .cta-card p {
      font-size: 17px;
      color: #888;
      max-width: 480px;
      margin: 0 auto 36px;
      line-height: 1.7;
      position: relative;
      z-index: 2;
    }

    .cta-card .hero-actions {
      position: relative;
      z-index: 2;
    }

    /* ================================================================
       FOOTER
       ================================================================ */
    .footer {
      border-top: 1px solid rgba(255,255,255,0.06);
      padding: 48px 24px;
      text-align: center;
    }

    .footer-brand {
      font-size: 20px;
      font-weight: 800;
      color: #fff;
      letter-spacing: -0.02em;
      margin-bottom: 8px;
    }

    .footer-brand span { color: #22c55e; }

    .footer-sub {
      font-size: 13px;
      color: #555;
    }

    /* ================================================================
       ANIMATIONS
       ================================================================ */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(30px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeDown {
      from { opacity: 0; transform: translateY(-20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    /* Scroll-triggered reveal */
    .reveal {
      opacity: 0;
      transform: translateY(40px);
      transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .reveal.visible {
      opacity: 1;
      transform: translateY(0);
    }

    .reveal-delay-1 { transition-delay: 0.1s; }
    .reveal-delay-2 { transition-delay: 0.2s; }
    .reveal-delay-3 { transition-delay: 0.3s; }
    .reveal-delay-4 { transition-delay: 0.4s; }

    /* ================================================================
       RESPONSIVE
       ================================================================ */
    @media (max-width: 900px) {
      .stats-ribbon { grid-template-columns: repeat(2, 1fr); }
      .bento-grid { grid-template-columns: 1fr; }
      .bento-span-2 { grid-column: span 1; }
      .bento-row-2 { grid-row: span 1; }
      .split-section { grid-template-columns: 1fr; gap: 48px; }
      .split-visual { min-height: 320px; }
      .glass-card { flex: 0 0 280px; }
    }

    @media (max-width: 600px) {
      .stats-ribbon { grid-template-columns: 1fr; }
      .stat-cell { padding: 32px 20px; }
      .bento-section, .cta-section { padding: 80px 16px; }
      .split-section { padding: 80px 16px; }
      .cta-card { padding: 48px 24px; }
    }
  </style>
</head>
<body>

<!-- Cursor glow -->
<div class="cursor-glow" id="cursorGlow"></div>

<!-- ═══════════════════════════════════════════════════════════════
     SECTION 1 — HERO
     ═══════════════════════════════════════════════════════════════ -->
<section class="hero">
  <div class="hero-badge">
    <span class="hero-badge-dot"></span>
    Temporada 2026 activa
  </div>

  <h1>Reserva tu<br><span>cancha perfecta</span></h1>

  <p class="hero-sub">
    La plataforma que conecta jugadores con los mejores complejos deportivos de Argentina. En segundos.
  </p>

  <div class="hero-actions">
    <a href="#" class="btn-glow btn-glow-primary">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      Explorar complejos
    </a>
    <a href="#" class="btn-glow btn-glow-ghost">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      Falta Uno
    </a>
  </div>

  <div class="scroll-hint">
    <span>Scroll</span>
    <span class="scroll-hint-line"></span>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     SECTION 2 — STATS RIBBON
     ═══════════════════════════════════════════════════════════════ -->
<section class="stats-ribbon">
  <div class="stat-cell reveal">
    <div class="stat-number">2<span class="accent">.</span>4k</div>
    <div class="stat-label">Partidos jugados</div>
  </div>
  <div class="stat-cell reveal reveal-delay-1">
    <div class="stat-number">180<span class="accent">+</span></div>
    <div class="stat-label">Complejos activos</div>
  </div>
  <div class="stat-cell reveal reveal-delay-2">
    <div class="stat-number">98<span class="accent">%</span></div>
    <div class="stat-label">Satisfaccion</div>
  </div>
  <div class="stat-cell reveal reveal-delay-3">
    <div class="stat-number"><span class="accent">&lt;</span>30s</div>
    <div class="stat-label">Tiempo de reserva</div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     SECTION 3 — BENTO GRID
     ═══════════════════════════════════════════════════════════════ -->
<section class="bento-section">
  <div class="reveal">
    <div class="section-eyebrow">Funcionalidades</div>
    <h2 class="section-title">Todo lo que necesitas<br>en un solo lugar</h2>
    <p class="section-desc">Desde la reserva hasta el resultado del partido. Una experiencia completa para jugadores y complejos.</p>
  </div>

  <div class="bento-grid">
    <!-- Card 1: Featured (span 2) -->
    <div class="bento-card bento-featured bento-span-2 reveal">
      <div class="bento-graph">
        <div class="bento-bar" style="height:35%"></div>
        <div class="bento-bar" style="height:55%"></div>
        <div class="bento-bar" style="height:45%"></div>
        <div class="bento-bar" style="height:70%"></div>
        <div class="bento-bar" style="height:60%"></div>
        <div class="bento-bar" style="height:85%"></div>
        <div class="bento-bar" style="height:75%"></div>
        <div class="bento-bar" style="height:90%"></div>
        <div class="bento-bar" style="height:65%"></div>
        <div class="bento-bar" style="height:95%"></div>
        <div class="bento-bar" style="height:80%"></div>
        <div class="bento-bar" style="height:100%"></div>
      </div>
      <div class="bento-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
      </div>
      <h3>Estadisticas en tiempo real</h3>
      <p>Visualiza tu rendimiento, seguimiento de victorias, empates y derrotas. Todo actualizado automaticamente despues de cada partido.</p>
    </div>

    <!-- Card 2 -->
    <div class="bento-card bento-row-2 reveal reveal-delay-1">
      <div class="bento-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      </div>
      <h3>Falta Uno</h3>
      <p>Busca jugadores para completar tu equipo o unite a partidos cerca tuyo. Chat integrado y notificaciones en vivo.</p>
      <div style="margin-top:24px; display:flex; gap:8px; flex-wrap:wrap;">
        <span style="padding:6px 14px; border-radius:999px; font-size:12px; font-weight:600; background:rgba(34,197,94,0.1); color:#6ee7a0; border:1px solid rgba(34,197,94,0.15);">3/5 jugadores</span>
        <span style="padding:6px 14px; border-radius:999px; font-size:12px; font-weight:600; background:rgba(245,179,1,0.1); color:#fbbf24; border:1px solid rgba(245,179,1,0.15);">En vivo</span>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="bento-card reveal reveal-delay-2">
      <div class="bento-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
      </div>
      <h3>Reserva inteligente</h3>
      <p>Slots en tiempo real, multiples formas de pago, y turnos recurrentes automaticos.</p>
    </div>

    <!-- Card 4 -->
    <div class="bento-card reveal reveal-delay-3">
      <div class="bento-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
      </div>
      <h3>Torneos</h3>
      <p>Organiza tu propio campeonato con fixture automatico, resultados en vivo y tabla de posiciones.</p>
    </div>

    <!-- Card 5 (span 2) -->
    <div class="bento-card bento-span-2 reveal reveal-delay-4">
      <div style="display:flex; gap:20px; align-items:center; margin-bottom:20px; flex-wrap:wrap;">
        <div style="display:flex; gap:-8px;">
          <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#22c55e,#16a34a);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;border:2px solid #050505;">M</div>
          <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;border:2px solid #050505;margin-left:-8px;">J</div>
          <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:800;color:#fff;border:2px solid #050505;margin-left:-8px;">L</div>
          <div style="width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,0.06);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:600;color:#666;border:2px solid #050505;margin-left:-8px;">+4</div>
        </div>
        <div>
          <div style="font-size:13px; font-weight:700; color:#fff;">Fulbo los martes</div>
          <div style="font-size:12px; color:#666;">7 miembros activos</div>
        </div>
      </div>
      <div class="bento-icon">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <h3>Grupos de amigos</h3>
      <p>Arma tu grupo, organiza partidos recurrentes y juga siempre con la misma gente. Chat persistente y confirmacion rapida.</p>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     SECTION 4 — GLASS CARDS (testimonials / reviews)
     ═══════════════════════════════════════════════════════════════ -->
<section class="glass-section">
  <div class="reveal">
    <div class="section-eyebrow">Comunidad</div>
    <h2 class="section-title">Lo que dicen los jugadores</h2>
    <p class="section-desc">Miles de jugadores ya reservan con TuCancha cada semana.</p>
  </div>

  <div class="glass-scroll">
    <!-- Card 1 -->
    <div class="glass-card">
      <div class="glass-avatar">MR</div>
      <div class="glass-card-title">Martin R.</div>
      <div class="glass-card-meta">Zona Sur &middot; Futbol</div>
      <div class="glass-stars">
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div class="glass-card-body">Reservo todos los martes en 10 segundos. Antes llamaba por telefono y nunca habia turno.</div>
      <div class="glass-card-tags">
        <span class="glass-tag glass-tag-green">Futbol 5</span>
        <span class="glass-tag">Lomas de Zamora</span>
      </div>
    </div>

    <!-- Card 2 -->
    <div class="glass-card">
      <div class="glass-avatar" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">LC</div>
      <div class="glass-card-title">Lucia C.</div>
      <div class="glass-card-meta">CABA &middot; Padel</div>
      <div class="glass-stars">
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div class="glass-card-body">El Falta Uno me salvo la vida. Siempre encuentro gente para jugar padel los fines de semana.</div>
      <div class="glass-card-tags">
        <span class="glass-tag glass-tag-green">Padel</span>
        <span class="glass-tag">Palermo</span>
      </div>
    </div>

    <!-- Card 3 -->
    <div class="glass-card">
      <div class="glass-avatar" style="background:linear-gradient(135deg,#f59e0b,#d97706);">DP</div>
      <div class="glass-card-title">Diego P.</div>
      <div class="glass-card-meta">Cordoba &middot; Futbol</div>
      <div class="glass-stars">
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div class="glass-card-body">Administro 3 complejos desde el panel. Las reservas se duplicaron desde que nos sumamos.</div>
      <div class="glass-card-tags">
        <span class="glass-tag glass-tag-green">Complejo</span>
        <span class="glass-tag">Cordoba Capital</span>
      </div>
    </div>

    <!-- Card 4 -->
    <div class="glass-card">
      <div class="glass-avatar" style="background:linear-gradient(135deg,#ec4899,#be185d);">VS</div>
      <div class="glass-card-title">Valentina S.</div>
      <div class="glass-card-meta">Rosario &middot; Tennis</div>
      <div class="glass-stars">
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <svg class="glass-star" viewBox="0 0 24 24" fill="#22c55e"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      </div>
      <div class="glass-card-body">Me encanta el ranking y las estadisticas. Le da una competitividad re copada a los partidos entre amigas.</div>
      <div class="glass-card-tags">
        <span class="glass-tag glass-tag-green">Tenis</span>
        <span class="glass-tag">Fisherton</span>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     SECTION 5 — SPLIT (text + interactive mockup)
     ═══════════════════════════════════════════════════════════════ -->
<section class="split-section">
  <div class="split-text reveal">
    <div class="section-eyebrow">Reservas</div>
    <h2 class="section-title" style="max-width:420px;">Elegir cancha nunca fue tan simple</h2>
    <p class="section-desc" style="margin-bottom:0;">Horarios actualizados al instante, precios claros, y confirmacion inmediata. Sin llamar, sin esperar.</p>
    <ul class="check-list">
      <li>
        <span class="check-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
        Disponibilidad en tiempo real
      </li>
      <li>
        <span class="check-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
        MercadoPago o pago en el complejo
      </li>
      <li>
        <span class="check-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
        Codigo de verificacion unico
      </li>
      <li>
        <span class="check-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
        Notificaciones push 30 min antes
      </li>
    </ul>
  </div>

  <div class="split-visual reveal reveal-delay-2">
    <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:#555; margin-bottom:20px;">Hoy &middot; Cancha F5 Pro</div>

    <div class="mockup-slot">
      <div class="mockup-time">18:00</div>
      <div class="mockup-info">
        <div class="mockup-field">Cancha 1 &middot; Sintetico</div>
        <div class="mockup-venue">Club El Trebol</div>
      </div>
      <span class="mockup-status status-available">Disponible</span>
      <div class="mockup-price">$8.500</div>
    </div>

    <div class="mockup-slot">
      <div class="mockup-time">19:00</div>
      <div class="mockup-info">
        <div class="mockup-field">Cancha 2 &middot; Cesped</div>
        <div class="mockup-venue">Club El Trebol</div>
      </div>
      <span class="mockup-status status-few">Ultimos 2</span>
      <div class="mockup-price">$9.200</div>
    </div>

    <div class="mockup-slot" style="opacity:0.5;">
      <div class="mockup-time">20:00</div>
      <div class="mockup-info">
        <div class="mockup-field">Cancha 1 &middot; Sintetico</div>
        <div class="mockup-venue">Club El Trebol</div>
      </div>
      <span class="mockup-status status-taken">Reservado</span>
      <div class="mockup-price" style="color:#555; text-decoration:line-through;">$8.500</div>
    </div>

    <div class="mockup-slot">
      <div class="mockup-time">21:00</div>
      <div class="mockup-info">
        <div class="mockup-field">Cancha 3 &middot; Techada</div>
        <div class="mockup-venue">Club El Trebol</div>
      </div>
      <span class="mockup-status status-available">Disponible</span>
      <div class="mockup-price">$12.000</div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     SECTION 6 — CTA
     ═══════════════════════════════════════════════════════════════ -->
<section class="cta-section">
  <div class="cta-card reveal">
    <h2>Empeza a jugar hoy</h2>
    <p>Crea tu cuenta gratis en 30 segundos y reserva tu primera cancha. Sin compromisos.</p>
    <div class="hero-actions">
      <a href="#" class="btn-glow btn-glow-primary">
        Crear cuenta gratis
      </a>
      <a href="#" class="btn-glow btn-glow-ghost">
        Soy un complejo
      </a>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════════
     FOOTER
     ═══════════════════════════════════════════════════════════════ -->
<footer class="footer">
  <div class="footer-brand">Tu<span>Cancha</span></div>
  <div class="footer-sub">La cancha perfecta te esta esperando.</div>
</footer>

<!-- ═══════════════════════════════════════════════════════════════
     SCRIPTS
     ═══════════════════════════════════════════════════════════════ -->
<script>
  // ── Cursor glow (desktop) ──
  const glow = document.getElementById('cursorGlow');
  if (window.matchMedia('(pointer: fine)').matches) {
    document.addEventListener('mousemove', e => {
      glow.style.left = e.clientX + 'px';
      glow.style.top = e.clientY + 'px';
    });
  } else {
    glow.style.display = 'none';
  }

  // ── Scroll reveal ──
  const reveals = document.querySelectorAll('.reveal');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.15, rootMargin: '0px 0px -40px 0px' });

  reveals.forEach(el => observer.observe(el));

  // ── Stat counter animation ──
  document.querySelectorAll('.stat-number').forEach(el => {
    const obs = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting) {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '1';
        obs.unobserve(el);
      }
    }, { threshold: 0.3 });
    obs.observe(el);
  });
</script>

</body>
</html>
