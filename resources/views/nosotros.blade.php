@extends('layouts.marketing')

@section('title', 'Quiénes somos — TuCancha')
@section('meta_description', 'Conocé el equipo detrás de TuCancha, la plataforma argentina de reservas deportivas online. Nuestra misión es conectar jugadores con los mejores complejos.')

@push('jsonld')
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Nosotros', 'item' => url('/nosotros')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

@push('styles')
  /* Easings from design-tokens.css: --ease-out-expo, --ease-out-quart, --ease-in-out-expo, --ease-drawer */

  /* ── Focus Accessibility ────────────────────────── */
  .btn-ns-primary:focus-visible,
  .btn-ns-ghost:focus-visible,
  .ns-culture-pill:focus-visible,
  .ns-value-shell:focus-visible {
    outline: 2px solid #22c55e;
    outline-offset: 3px;
    box-shadow: 0 0 0 4px rgba(34,197,94,0.18);
  }

  /* ── Noise Texture Overlay (dark sections) ──────── */
  .ns-noise::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
    background-repeat: repeat;
    background-size: 128px 128px;
    pointer-events: none;
    z-index: 0;
    border-radius: inherit;
    mix-blend-mode: overlay;
  }

  /* ── Hero ────────────────────────────────────────── */
  .ns-hero {
    padding: 48px 0 0 0;
  }

  .ns-hero-inner {
    position: relative;
    border-radius: 2rem;
    overflow: hidden;
    min-height: 520px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #fff;
  }

  .ns-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/jugadores-falta-uno.webp');
    background-size: cover;
    background-position: center top;
    background-attachment: fixed;
    opacity: 0.22;
    z-index: 0;
    transition: opacity 600ms var(--ease-out-expo);
  }

  @media (max-width: 768px) {
    .ns-hero-bg { background-attachment: scroll; }
  }

  .ns-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, rgba(4,18,10,0.92) 0%, rgba(10,38,22,0.88) 40%, rgba(6,24,14,0.94) 100%);
    z-index: 1;
  }

  .ns-hero-blob-1 {
    position: absolute;
    top: -120px;
    left: -140px;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.08) 0%, transparent 65%);
    z-index: 2;
    pointer-events: none;
    animation: ns-blob-drift 18s var(--ease-out-quart) infinite alternate;
  }

  .ns-hero-blob-2 {
    position: absolute;
    bottom: -140px;
    right: -100px;
    width: 440px;
    height: 440px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.07) 0%, transparent 65%);
    z-index: 2;
    pointer-events: none;
    animation: ns-blob-drift 22s var(--ease-out-quart) infinite alternate-reverse;
  }

  @keyframes ns-blob-drift {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(30px, -20px) scale(1.08); }
  }

  .ns-hero-content {
    position: relative;
    z-index: 3;
    padding: 96px 48px;
    max-width: 880px;
  }

  .ns-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 20px;
    border-radius: 999px;
    background: rgba(74,222,128,0.10);
    border: 1px solid rgba(74,222,128,0.25);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #6eeaa0;
    margin-bottom: 32px;
    backdrop-filter: blur(8px);
  }

  .ns-hero-content h1 {
    margin: 0 0 28px 0;
    font-size: clamp(40px, 6vw, 80px);
    line-height: 0.95;
    letter-spacing: -0.045em;
    font-weight: 900;
    text-wrap: balance;
  }

  .ns-hero-content h1 em {
    font-style: normal;
    color: #4ade80;
    position: relative;
  }

  .ns-hero-content h1 em::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 0;
    right: 0;
    height: 3px;
    background: #4ade80;
    border-radius: 2px;
    opacity: 0.4;
  }

  .ns-hero-content > p {
    margin: 0 auto 48px auto;
    max-width: 560px;
    color: rgba(255,255,255,0.62);
    font-size: 18px;
    line-height: 1.7;
    letter-spacing: 0.01em;
  }

  .ns-hero-micro-stats {
    display: flex;
    gap: 36px;
    justify-content: center;
    flex-wrap: wrap;
  }

  .ns-hero-micro-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
  }

  .ns-hero-micro-stat-value {
    font-size: 24px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.02em;
  }

  .ns-hero-micro-stat-label {
    font-size: 11px;
    color: rgba(255,255,255,0.44);
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .ns-hero-micro-stat-divider {
    width: 1px;
    height: 40px;
    background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.15), transparent);
    align-self: center;
  }

  @media (max-width: 768px) {
    .ns-hero { padding: 24px 0 0 0; }
    .ns-hero-content { padding: 64px 24px; }
    .ns-hero-content > p { font-size: 16px; }
    .ns-hero-micro-stats { gap: 24px; }
    .ns-hero-micro-stat-divider { display: none; }
  }

  @media (max-width: 480px) {
    .ns-hero { padding: 16px 0 0 0; }
    .ns-hero-inner { border-radius: 1.25rem; min-height: 420px; }
    .ns-hero-content { padding: 48px 20px; }
  }

  /* ── Manifiesto ───────────────────────────────────── */
  .manifesto-section {
    padding: 100px 0;
    background: #fff;
  }

  .manifesto-grid {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 64px;
    align-items: center;
  }

  .manifesto-text .label-pill {
    display: inline-block;
    padding: 5px 16px;
    border-radius: 999px;
    background: #f0fdf4;
    color: #166534;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-bottom: 24px;
    border: 1px solid #bbf7d0;
  }

  .manifesto-text h2 {
    margin: 0 0 24px 0;
    font-size: clamp(30px, 3.5vw, 44px);
    line-height: 1.08;
    letter-spacing: -0.035em;
    font-weight: 900;
    text-wrap: balance;
  }

  .manifesto-text p {
    margin: 0 0 18px 0;
    font-size: 16px;
    line-height: 1.8;
    color: #4a4a4a;
  }

  .manifesto-text p:last-child { margin: 0; }

  /* Double-bezel quote card */
  .manifesto-quote-shell {
    background: rgba(17,17,17,0.04);
    border-radius: 2rem;
    padding: 6px;
    border: 1px solid rgba(0,0,0,0.04);
  }

  .manifesto-quote {
    background: #111;
    border-radius: calc(2rem - 6px);
    padding: 56px 48px;
    color: #fff;
    position: relative;
    overflow: hidden;
    border-top: 3px solid #22c55e;
  }

  .manifesto-quote-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/hero-cancha.webp');
    background-size: cover;
    background-position: center;
    opacity: 0.06;
    pointer-events: none;
  }

  .manifesto-quote::before {
    content: '\201C';
    position: absolute;
    top: 12px;
    left: 36px;
    font-size: 140px;
    line-height: 1;
    color: rgba(74,222,128,0.18);
    font-family: var(--font-family);
    pointer-events: none;
    z-index: 1;
  }

  .manifesto-quote blockquote {
    margin: 0;
    padding-top: 48px;
    font-size: 21px;
    line-height: 1.6;
    font-style: italic;
    color: rgba(255,255,255,0.88);
    position: relative;
    z-index: 2;
  }

  .manifesto-quote cite {
    display: block;
    margin-top: 28px;
    font-size: 13px;
    font-style: normal;
    color: rgba(255,255,255,0.38);
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  @media (max-width: 900px) {
    .manifesto-grid { grid-template-columns: 1fr; gap: 40px; }
    .manifesto-section { padding: 72px 0; }
  }

  @media (max-width: 480px) {
    .manifesto-text h2 { font-size: 28px; }
    .manifesto-quote { padding: 40px 28px; }
    .manifesto-quote blockquote { font-size: 18px; padding-top: 36px; }
  }

  /* ── Stats band ──────────────────────────────────── */
  .ns-stats-section {
    padding: 0 0 80px 0;
    background: #fff;
  }

  /* Double-bezel stats */
  .ns-stats-shell {
    background: rgba(17,17,17,0.03);
    border-radius: 2rem;
    padding: 6px;
    border: 1px solid rgba(0,0,0,0.03);
  }

  .ns-stats-band {
    background: #111;
    border-radius: calc(2rem - 6px);
    padding: 56px 52px;
    position: relative;
    overflow: hidden;
  }

  .ns-stats-band::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(34,197,94,0.3), transparent);
  }

  .ns-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    text-align: center;
  }

  .ns-stat-item {
    padding: 24px 20px;
    border-right: 1px solid rgba(255,255,255,0.06);
    transition: all 400ms var(--ease-out-expo);
    cursor: default;
    position: relative;
  }

  .ns-stat-item:last-child { border-right: none; }

  .ns-stat-item::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%) scaleX(0);
    width: 40px;
    height: 2px;
    background: #22c55e;
    border-radius: 2px;
    transition: transform 400ms var(--ease-out-expo);
  }

  .ns-stat-item:hover::after {
    transform: translateX(-50%) scaleX(1);
  }

  .ns-stat-icon {
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .ns-stat-value {
    font-size: 15px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 8px;
    letter-spacing: -0.01em;
  }

  .ns-stat-label {
    font-size: 13px;
    color: rgba(255,255,255,0.40);
    font-weight: 500;
    line-height: 1.5;
  }

  @media (max-width: 900px) {
    .ns-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .ns-stat-item:nth-child(2) { border-right: none; }
    .ns-stat-item:nth-child(3) { border-right: 1px solid rgba(255,255,255,0.06); }
  }

  @media (max-width: 768px) {
    .ns-stats-band { padding: 40px 28px; }
  }

  @media (max-width: 480px) {
    .ns-stats-grid { grid-template-columns: 1fr; }
    .ns-stat-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.06); padding: 20px 16px; }
    .ns-stat-item:last-child { border-bottom: none; }
  }

  /* ── Timeline ────────────────────────────────────── */
  .ns-history-section {
    padding: 100px 0;
    background: #fff;
  }

  .ns-history-section .section-head {
    text-align: center;
    margin-bottom: 64px;
  }

  .ns-history-section .section-title {
    font-size: clamp(28px, 3vw, 40px);
    letter-spacing: -0.035em;
    font-weight: 900;
  }

  .ns-timeline {
    display: flex;
    flex-direction: column;
    gap: 0;
    position: relative;
    max-width: 740px;
    margin: 0 auto;
  }

  .ns-timeline::before {
    content: '';
    position: absolute;
    left: 28px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #22c55e, rgba(34,197,94,0.15), #22c55e);
    border-radius: 2px;
  }

  .ns-tl-item {
    display: flex;
    gap: 36px;
    padding-bottom: 52px;
    position: relative;
  }

  .ns-tl-item:last-child { padding-bottom: 0; }

  .ns-tl-dot {
    flex-shrink: 0;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: #111;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    z-index: 1;
    box-shadow: 0 0 0 4px #fff, 0 0 0 6px rgba(34,197,94,0.2);
    transition: box-shadow 400ms var(--ease-out-expo);
  }

  .ns-tl-item:hover .ns-tl-dot {
    box-shadow: 0 0 0 4px #fff, 0 0 0 8px rgba(34,197,94,0.35);
  }

  .ns-tl-content {
    padding-top: 10px;
    flex: 1;
  }

  .ns-tl-year {
    display: inline-block;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #166534;
    background: #f0fdf4;
    border: 1px solid #dcfce7;
    border-radius: 999px;
    padding: 4px 14px;
    margin-bottom: 12px;
  }

  .ns-tl-content h3 {
    margin: 0 0 10px 0;
    font-size: 20px;
    letter-spacing: -0.025em;
    font-weight: 800;
  }

  .ns-tl-content p {
    margin: 0;
    color: #5a5a5a;
    font-size: 15px;
    line-height: 1.75;
  }

  /* Active/current timeline dot pulse */
  .ns-tl-dot-active {
    background: #22c55e;
    box-shadow: 0 0 0 4px #fff, 0 0 0 8px rgba(34,197,94,0.3);
    animation: ns-tl-pulse 2.4s var(--ease-out-quart) infinite;
  }

  @keyframes ns-tl-pulse {
    0%, 100% { box-shadow: 0 0 0 4px #fff, 0 0 0 8px rgba(34,197,94,0.25); }
    50% { box-shadow: 0 0 0 4px #fff, 0 0 0 12px rgba(34,197,94,0.45); }
  }

  @media (max-width: 768px) {
    .ns-history-section { padding: 72px 0; }
    .ns-timeline::before { left: 24px; }
    .ns-tl-dot { width: 50px; height: 50px; }
  }

  /* ── Values ──────────────────────────────────────── */
  .ns-values-section {
    padding: 0 0 100px 0;
    background: #fff;
  }

  .ns-values-section .section-head {
    text-align: center;
    margin-bottom: 56px;
  }

  .ns-values-section .section-title {
    font-size: clamp(28px, 3vw, 40px);
    letter-spacing: -0.035em;
    font-weight: 900;
  }

  .ns-values-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: auto auto;
    gap: 16px;
  }

  /* Accent card spans 2 columns for asymmetric bento */
  .ns-value-shell.ns-accent-shell {
    grid-column: span 2;
  }

  .ns-value-shell.ns-accent-shell .ns-value-card {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 0 28px;
    align-items: start;
  }

  .ns-value-shell.ns-accent-shell .ns-value-icon-wrap {
    grid-row: 1 / 3;
    width: 60px;
    height: 60px;
    border-radius: 18px;
    align-self: center;
  }

  .ns-value-shell.ns-accent-shell .ns-value-card h3 {
    margin-bottom: 8px;
    font-size: 20px;
  }

  /* Double-bezel value card */
  .ns-value-shell {
    background: rgba(0,0,0,0.02);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(0,0,0,0.03);
    transition: transform 500ms var(--ease-out-expo), box-shadow 500ms var(--ease-out-expo);
  }

  .ns-value-shell:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 48px rgba(34,197,94,0.08), 0 8px 16px rgba(0,0,0,0.03);
  }

  .ns-value-shell:active {
    transform: translateY(-2px) scale(0.985);
  }

  .ns-value-card {
    background: #fff;
    border-radius: calc(1.5rem - 4px);
    padding: 40px 32px;
    height: 100%;
    position: relative;
    overflow: hidden;
  }

  .ns-value-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 3px;
    height: 100%;
    background: linear-gradient(to bottom, #22c55e, #16a34a);
    border-radius: 0 2px 2px 0;
  }

  .ns-value-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: #f0fdf4;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 24px;
    transition: transform 400ms var(--ease-out-expo), background 400ms var(--ease-out-expo);
  }

  .ns-value-shell:hover .ns-value-icon-wrap {
    transform: scale(1.08);
    background: #dcfce7;
  }

  .ns-value-card h3 {
    margin: 0 0 12px 0;
    font-size: 18px;
    letter-spacing: -0.025em;
    font-weight: 800;
    color: #111;
  }

  .ns-value-card p {
    margin: 0;
    color: #5a5a5a;
    font-size: 15px;
    line-height: 1.75;
  }

  /* Accent card (dark variant) */
  .ns-value-shell.ns-accent-shell {
    background: rgba(17,17,17,0.06);
    border-color: rgba(0,0,0,0.05);
  }

  .ns-value-shell.ns-accent-shell:hover {
    box-shadow: 0 20px 48px rgba(0,0,0,0.12), 0 8px 16px rgba(0,0,0,0.06);
  }

  .ns-value-shell.ns-accent-shell .ns-value-card {
    background: #111;
  }

  .ns-value-shell.ns-accent-shell .ns-value-card::before {
    background: linear-gradient(to bottom, #4ade80, #22c55e);
  }

  .ns-value-shell.ns-accent-shell .ns-value-card h3 { color: #fff; }
  .ns-value-shell.ns-accent-shell .ns-value-card p { color: rgba(255,255,255,0.55); }

  .ns-value-shell.ns-accent-shell .ns-value-icon-wrap {
    background: rgba(74,222,128,0.10);
  }

  .ns-value-shell.ns-accent-shell:hover .ns-value-icon-wrap {
    background: rgba(74,222,128,0.18);
  }

  /* Stagger animation for values */
  .ns-value-shell:nth-child(1) { transition-delay: 0ms; }
  .ns-value-shell:nth-child(2) { transition-delay: 50ms; }
  .ns-value-shell:nth-child(3) { transition-delay: 100ms; }
  .ns-value-shell:nth-child(4) { transition-delay: 150ms; }
  .ns-value-shell:nth-child(5) { transition-delay: 200ms; }
  .ns-value-shell:nth-child(6) { transition-delay: 250ms; }

  @media (max-width: 900px) {
    .ns-values-grid { grid-template-columns: 1fr; max-width: 520px; margin: 0 auto; }
    .ns-value-shell { transition-delay: 0ms !important; }
    .ns-value-shell.ns-accent-shell { grid-column: span 1; }
    .ns-value-shell.ns-accent-shell .ns-value-card {
      display: block;
    }
    .ns-value-shell.ns-accent-shell .ns-value-icon-wrap {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      margin-bottom: 24px;
    }
  }

  /* ── Culture ─────────────────────────────────────── */
  .ns-culture-section {
    padding: 0 0 100px 0;
  }

  .ns-culture-shell {
    background: rgba(17,17,17,0.03);
    border-radius: 2.25rem;
    padding: 6px;
    border: 1px solid rgba(0,0,0,0.03);
  }

  .ns-culture-inner {
    background: #111;
    border-radius: calc(2.25rem - 6px);
    padding: 72px 64px;
    color: #fff;
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    gap: 64px;
    align-items: center;
    position: relative;
    overflow: hidden;
  }

  .ns-culture-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/jugadores-dandose-la-mano-post-partido.webp');
    background-size: cover;
    background-position: center;
    opacity: 0.10;
    pointer-events: none;
  }

  .ns-culture-inner > *:not(.ns-culture-bg) {
    position: relative;
    z-index: 1;
  }

  .ns-culture-left h2 {
    margin: 0 0 20px 0;
    font-size: clamp(28px, 3vw, 40px);
    line-height: 1.08;
    letter-spacing: -0.035em;
    font-weight: 900;
    color: #fff;
  }

  .ns-culture-left p {
    color: rgba(255,255,255,0.55);
    font-size: 16px;
    line-height: 1.8;
    margin: 0;
  }

  .ns-culture-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .ns-culture-pill {
    padding: 13px 22px;
    border-radius: 1rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    font-size: 14px;
    font-weight: 600;
    color: rgba(255,255,255,0.78);
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 400ms var(--ease-out-expo);
    cursor: default;
  }

  .ns-culture-pill:hover {
    background: rgba(255,255,255,0.10);
    border-color: rgba(34,197,94,0.35);
    transform: translateY(-2px);
    color: rgba(255,255,255,0.95);
  }

  .ns-culture-pill:active {
    transform: translateY(0) scale(0.97);
  }

  .ns-culture-pill span {
    font-size: 18px;
    line-height: 1;
  }

  @media (max-width: 900px) {
    .ns-culture-inner { grid-template-columns: 1fr; gap: 36px; }
    .ns-culture-section { padding: 0 0 72px 0; }
  }

  @media (max-width: 768px) {
    .ns-culture-inner { padding: 48px 32px; }
  }

  @media (max-width: 480px) {
    .ns-culture-inner { padding: 40px 24px; border-radius: 1.25rem; }
    .ns-culture-shell { border-radius: calc(1.25rem + 6px); }
  }

  /* ── CTA final ───────────────────────────────────── */
  .ns-cta {
    padding: 0 0 100px 0;
  }

  .ns-cta-shell {
    background: rgba(15,76,42,0.05);
    border-radius: 2.25rem;
    padding: 6px;
    border: 1px solid rgba(34,197,94,0.08);
  }

  .ns-cta-inner {
    background: linear-gradient(145deg, #0a3d21 0%, #0f5c32 40%, #147a42 100%);
    border-radius: calc(2.25rem - 6px);
    padding: 80px 56px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  /* Mesh gradient orbs */
  .ns-cta-inner::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -60px;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.12) 0%, transparent 60%);
    pointer-events: none;
  }

  .ns-cta-inner::after {
    content: '';
    position: absolute;
    bottom: -80px;
    left: -40px;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 60%);
    pointer-events: none;
  }

  .ns-cta-inner h2 {
    margin: 0 0 18px 0;
    font-size: clamp(28px, 3.5vw, 46px);
    letter-spacing: -0.035em;
    color: #fff;
    position: relative;
    z-index: 1;
    font-weight: 900;
    text-wrap: balance;
  }

  .ns-cta-inner > p {
    margin: 0 auto 40px auto;
    max-width: 520px;
    font-size: 17px;
    color: rgba(255,255,255,0.65);
    line-height: 1.7;
    position: relative;
    z-index: 1;
  }

  .ns-cta-actions {
    display: flex;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
  }

  .btn-ns-primary {
    padding: 15px 34px;
    background: #22c55e;
    color: #052e14;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    transition: all 300ms var(--ease-out-expo);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    box-shadow: 0 4px 16px rgba(34,197,94,0.25);
  }

  .btn-ns-primary:hover {
    background: #16a34a;
    transform: translateY(-3px);
    color: #fff;
    box-shadow: 0 8px 24px rgba(34,197,94,0.35);
  }

  .btn-ns-primary:active {
    transform: translateY(-1px) scale(0.97);
    box-shadow: 0 2px 8px rgba(34,197,94,0.2);
  }

  .btn-ns-ghost {
    padding: 15px 28px;
    background: transparent;
    color: rgba(255,255,255,0.82);
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 300ms var(--ease-out-expo);
    display: inline-block;
  }

  .btn-ns-ghost:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.4);
    transform: translateY(-3px);
    color: #fff;
  }

  .btn-ns-ghost:active {
    transform: translateY(-1px) scale(0.97);
  }

  @media (max-width: 768px) {
    .ns-cta-inner { padding: 56px 32px; }
    .ns-cta { padding: 0 0 72px 0; }
  }

  @media (max-width: 480px) {
    .ns-cta-inner { padding: 48px 24px; border-radius: 1.25rem; }
    .ns-cta-shell { border-radius: calc(1.25rem + 6px); }
    .ns-cta-actions { flex-direction: column; align-items: center; }
    .btn-ns-primary, .btn-ns-ghost { width: 100%; text-align: center; justify-content: center; }
  }

  /* ── Reduced Motion ─────────────────────────────── */
  @media (prefers-reduced-motion: reduce) {
    .ns-hero-blob-1,
    .ns-hero-blob-2 { animation: none; }

    .ns-tl-dot-active { animation: none; }

    .ns-value-shell,
    .ns-culture-pill,
    .btn-ns-primary,
    .btn-ns-ghost,
    .ns-stat-item,
    .ns-tl-dot,
    .ns-value-icon-wrap {
      transition-duration: 0ms !important;
    }

    .ns-value-shell:hover { transform: none; }
    .ns-culture-pill:hover { transform: none; }
  }
@endpush

@section('content')

  {{-- ── Hero ─────────────────────────────────────────────────────────── --}}
  <section class="ns-hero">
    <div class="container">
      <div class="ns-hero-inner ns-noise">
        <div class="ns-hero-bg"></div>
        <div class="ns-hero-overlay"></div>
        <div class="ns-hero-blob-1"></div>
        <div class="ns-hero-blob-2"></div>
        <div class="ns-hero-content">
          <div class="ns-hero-badge" data-aos="fade-down" data-aos-duration="600">
            Quiénes somos
          </div>
          <h1 data-aos="fade-up" data-aos-delay="80" data-aos-duration="700">
            Nacimos para que<br>jugar sea <em>fácil</em>
          </h1>
          <p data-aos="fade-up" data-aos-delay="160" data-aos-duration="700">
            Somos un equipo argentino con una misión simple: que reservar una cancha
            sea tan fácil como pedir una pizza. Sin llamadas, sin grupos de WhatsApp,
            sin "preguntale al encargado".
          </p>
          <div class="ns-hero-micro-stats" data-aos="fade-up" data-aos-delay="260" data-aos-duration="700">
            <div class="ns-hero-micro-stat">
              <span class="ns-hero-micro-stat-value">🇦🇷</span>
              <span class="ns-hero-micro-stat-label">Hecho en Argentina</span>
            </div>
            <div class="ns-hero-micro-stat-divider"></div>
            <div class="ns-hero-micro-stat">
              <span class="ns-hero-micro-stat-value">24/7</span>
              <span class="ns-hero-micro-stat-label">Reservas online</span>
            </div>
            <div class="ns-hero-micro-stat-divider"></div>
            <div class="ns-hero-micro-stat">
              <span class="ns-hero-micro-stat-value">$0</span>
              <span class="ns-hero-micro-stat-label">Sin comisiones</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Manifiesto ────────────────────────────────────────────────────── --}}
  <section class="manifesto-section">
    <div class="container">
      <div class="manifesto-grid">

        <div class="manifesto-text" data-aos="fade-right" data-aos-duration="700">
          <span class="label-pill">Nuestra historia</span>
          <h2>Empezó con un grupo que no podía reservar una cancha</h2>
          <p>
            Todo comenzó un jueves a la noche. Queríamos jugar el fútbol de siempre,
            el de los amigos de toda la vida. Pero entre llamadas que no contestaban,
            grupos de WhatsApp que explotaban y complejos que no tenían sistema online,
            terminamos jugando al truco.
          </p>
          <p>
            Ahí nació la pregunta: ¿por qué algo tan simple es tan complicado?
            TuCancha es la respuesta. Una plataforma hecha por jugadores, para jugadores,
            y para los dueños de complejos que merecen una herramienta moderna.
          </p>
          <p>
            Hoy conectamos complejos de todo el país con miles de jugadores que solo
            quieren reservar su cancha y aparecer a patear la pelota.
          </p>
        </div>

        <div class="manifesto-quote-shell" data-aos="fade-left" data-aos-duration="700">
          <div class="manifesto-quote">
            <div class="manifesto-quote-bg"></div>
            <blockquote>
              "El fútbol no debería tener fricción. La cancha tiene que estar a un par de clics, no a veinte llamadas perdidas."
              <cite>— El equipo de TuCancha</cite>
            </blockquote>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ── Stats band ────────────────────────────────────────────────────── --}}
  <section class="ns-stats-section">
    <div class="container">
      <div class="ns-stats-shell">
        <div class="ns-stats-band ns-noise">
          <div class="ns-stats-grid">
            <div class="ns-stat-item" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
              <span class="ns-stat-icon"><i data-lucide="zap" style="width:26px;height:26px;stroke:#22c55e;stroke-width:1.5;"></i></span>
              <div class="ns-stat-value">Reservas al instante</div>
              <div class="ns-stat-label">Sin llamadas ni esperas</div>
            </div>
            <div class="ns-stat-item" data-aos="fade-up" data-aos-delay="80" data-aos-duration="600">
              <span class="ns-stat-icon"><i data-lucide="credit-card" style="width:26px;height:26px;stroke:#22c55e;stroke-width:1.5;"></i></span>
              <div class="ns-stat-value">Cobro automático</div>
              <div class="ns-stat-label">Directo a tu cuenta de MP</div>
            </div>
            <div class="ns-stat-item" data-aos="fade-up" data-aos-delay="160" data-aos-duration="600">
              <span class="ns-stat-icon"><i data-lucide="bar-chart-2" style="width:26px;height:26px;stroke:#22c55e;stroke-width:1.5;"></i></span>
              <div class="ns-stat-value">Panel completo</div>
              <div class="ns-stat-label">Reservas, agenda y reportes</div>
            </div>
            <div class="ns-stat-item" data-aos="fade-up" data-aos-delay="240" data-aos-duration="600">
              <span class="ns-stat-icon"><i data-lucide="trending-up" style="width:26px;height:26px;stroke:#22c55e;stroke-width:1.5;"></i></span>
              <div class="ns-stat-value">Sin comisiones</div>
              <div class="ns-stat-label">Precio fijo, sin sorpresas</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Timeline ──────────────────────────────────────────────────────── --}}
  <section class="ns-history-section">
    <div class="container">

      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">Cómo llegamos acá</span>
        <h2 class="section-title">El camino de TuCancha</h2>
      </div>

      <div class="ns-timeline">

        <div class="ns-tl-item" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
          <div class="ns-tl-dot"><i data-lucide="lightbulb" style="width:20px;height:20px;stroke:#22c55e;stroke-width:1.5;"></i></div>
          <div class="ns-tl-content">
            <span class="ns-tl-year">El origen</span>
            <h3>La idea que no nos dejaba dormir</h3>
            <p>
              Un grupo de amigos frustrado por no poder reservar una cancha online.
              Nadie lo estaba resolviendo bien en Argentina. Decidimos hacerlo nosotros.
            </p>
          </div>
        </div>

        <div class="ns-tl-item" data-aos="fade-up" data-aos-delay="120" data-aos-duration="600">
          <div class="ns-tl-dot"><i data-lucide="wrench" style="width:20px;height:20px;stroke:#22c55e;stroke-width:1.5;"></i></div>
          <div class="ns-tl-content">
            <span class="ns-tl-year">Construcción</span>
            <h3>Primeras líneas de código</h3>
            <p>
              Empezamos a construir la plataforma desde cero. Diseño, backend, pagos,
              mails automáticos. Cada detalle pensado para que la experiencia sea
              simple tanto para el jugador como para el dueño del complejo.
            </p>
          </div>
        </div>

        <div class="ns-tl-item" data-aos="fade-up" data-aos-delay="240" data-aos-duration="600">
          <div class="ns-tl-dot"><i data-lucide="rocket" style="width:20px;height:20px;stroke:#22c55e;stroke-width:1.5;"></i></div>
          <div class="ns-tl-content">
            <span class="ns-tl-year">Lanzamiento</span>
            <h3>Los primeros complejos se suman</h3>
            <p>
              Los primeros complejos empiezan a usar TuCancha. Las reservas llegan
              solas, los cobros se procesan automáticamente y los dueños
              pueden enfocarse en lo que importa: el negocio.
            </p>
          </div>
        </div>

        <div class="ns-tl-item" data-aos="fade-up" data-aos-delay="360" data-aos-duration="600">
          <div class="ns-tl-dot ns-tl-dot-active"><i data-lucide="trending-up" style="width:20px;height:20px;stroke:#fff;stroke-width:1.5;"></i></div>
          <div class="ns-tl-content">
            <span class="ns-tl-year">Hoy</span>
            <h3>Creciendo junto a la comunidad</h3>
            <p>
              Seguimos sumando complejos, escuchando a los usuarios y lanzando
              funcionalidades nuevas. Reservas recurrentes, descuentos, historial
              de partidos, reportes. Y hay mucho más por venir.
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ── Values ────────────────────────────────────────────────────────── --}}
  <section class="ns-values-section">
    <div class="container">

      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">Lo que nos mueve</span>
        <h2 class="section-title">Nuestros valores</h2>
      </div>

      <div class="ns-values-grid">

        <div class="ns-value-shell ns-accent-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
          <div class="ns-value-card">
            <div class="ns-value-icon-wrap"><i data-lucide="zap" style="width:22px;height:22px;stroke:#22c55e;stroke-width:1.5;"></i></div>
            <h3>Simplicidad ante todo</h3>
            <p>
              Si algo se puede hacer en dos clics, no lo hacemos en cuatro.
              Cada pantalla, cada formulario y cada mail existe para hacer
              la vida más fácil, no más complicada.
            </p>
          </div>
        </div>

        <div class="ns-value-shell" data-aos="fade-up" data-aos-delay="60" data-aos-duration="600">
          <div class="ns-value-card">
            <div class="ns-value-icon-wrap"><i data-lucide="users" style="width:22px;height:22px;stroke:#22c55e;stroke-width:1.5;"></i></div>
            <h3>El dueño del complejo es nuestro socio</h3>
            <p>
              No somos un competidor ni un intermediario. Somos la herramienta
              que hace crecer su negocio. Su éxito es nuestro éxito.
            </p>
          </div>
        </div>

        <div class="ns-value-shell" data-aos="fade-up" data-aos-delay="120" data-aos-duration="600">
          <div class="ns-value-card">
            <div class="ns-value-icon-wrap"><i data-lucide="target" style="width:22px;height:22px;stroke:#22c55e;stroke-width:1.5;"></i></div>
            <h3>Foco en lo que importa</h3>
            <p>
              No construimos funcionalidades por construirlas. Cada mejora
              resuelve un problema real que alguien nos contó. Escuchamos
              mucho antes de escribir código.
            </p>
          </div>
        </div>

        <div class="ns-value-shell" data-aos="fade-up" data-aos-delay="180" data-aos-duration="600">
          <div class="ns-value-card">
            <div class="ns-value-icon-wrap"><i data-lucide="lock" style="width:22px;height:22px;stroke:#22c55e;stroke-width:1.5;"></i></div>
            <h3>Confianza y transparencia</h3>
            <p>
              Los pagos se procesan de forma segura, los datos son de los
              usuarios y nunca ocultamos costos. Precio plano, sin sorpresas.
            </p>
          </div>
        </div>

        <div class="ns-value-shell" data-aos="fade-up" data-aos-delay="240" data-aos-duration="600">
          <div class="ns-value-card">
            <div class="ns-value-icon-wrap"><i data-lucide="activity" style="width:22px;height:22px;stroke:#22c55e;stroke-width:1.5;"></i></div>
            <h3>Velocidad sin excusas</h3>
            <p>
              Iteramos rápido. Si algo no funciona lo arreglamos, si algo
              puede mejorar lo mejoramos. No esperamos al lanzamiento perfecto
              porque el perfecto es enemigo del bueno.
            </p>
          </div>
        </div>

        <div class="ns-value-shell" data-aos="fade-up" data-aos-delay="300" data-aos-duration="600">
          <div class="ns-value-card">
            <div class="ns-value-icon-wrap"><i data-lucide="shield" style="width:22px;height:22px;stroke:#22c55e;stroke-width:1.5;"></i></div>
            <h3>Somos jugadores también</h3>
            <p>
              Construimos para nosotros mismos. Usamos TuCancha para reservar
              nuestras propias canchas. Esa perspectiva no se puede comprar
              en ningún lado.
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ── Culture ───────────────────────────────────────────────────────── --}}
  <section class="ns-culture-section">
    <div class="container">
      <div class="ns-culture-shell">
        <div class="ns-culture-inner ns-noise">
          <div class="ns-culture-bg"></div>

          <div class="ns-culture-left" data-aos="fade-right" data-aos-duration="700">
            <h2>Un equipo chico con ideas grandes</h2>
            <p>
              Somos pocos pero nos movemos rápido. Creemos que los mejores productos
              los hacen equipos que se comunican bien, se retroalimentan sin ego
              y comparten la obsesión por el detalle.
            </p>
          </div>

          <div class="ns-culture-pills" data-aos="fade-left" data-aos-delay="120" data-aos-duration="700">
            <div class="ns-culture-pill"><span>🇦🇷</span> 100% argentinos</div>
            <div class="ns-culture-pill"><span>☕</span> Mucho café, poco reunionismo</div>
            <div class="ns-culture-pill"><span><i data-lucide="package" style="width:14px;height:14px;stroke:currentColor;stroke-width:1.5;vertical-align:middle;"></i></span> Enviamos a producción seguido</div>
            <div class="ns-culture-pill"><span><i data-lucide="message-circle" style="width:14px;height:14px;stroke:currentColor;stroke-width:1.5;vertical-align:middle;"></i></span> Escuchamos a cada usuario</div>
            <div class="ns-culture-pill"><span>🐛</span> Los bugs nos quitan el sueño</div>
            <div class="ns-culture-pill"><span>⚽</span> Jugamos los miércoles</div>
            <div class="ns-culture-pill"><span><i data-lucide="refresh-cw" style="width:14px;height:14px;stroke:currentColor;stroke-width:1.5;vertical-align:middle;"></i></span> Iteramos, no planificamos ad infinitum</div>
            <div class="ns-culture-pill"><span><i data-lucide="trending-up" style="width:14px;height:14px;stroke:currentColor;stroke-width:1.5;vertical-align:middle;"></i></span> Crecimiento sin perder el alma</div>
          </div>

        </div>
      </div>
    </div>
  </section>

  {{-- ── CTA Final ─────────────────────────────────────────────────────── --}}
  <section class="ns-cta">
    <div class="container">
      <div class="ns-cta-shell">
        <div class="ns-cta-inner ns-noise" data-aos="fade-up" data-aos-duration="700">
          <h2>¿Tenés un complejo y querés sumarte?</h2>
          <p>
            Unite a los complejos que ya gestionan sus reservas online con TuCancha.
            Setup en minutos, soporte real y sin comisiones sobre tus ingresos.
          </p>
          <div class="ns-cta-actions">
            <a href="{{ route('planes') }}" class="btn-ns-primary">
              Ver planes <i data-lucide="arrow-right" style="width:16px;height:16px;stroke:currentColor;stroke-width:2;"></i>
            </a>
            <a href="{{ route('como-funciona') }}" class="btn-ns-ghost">
              Cómo funciona
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  // Counter animation (conservado por si se activan stats con data-target en el futuro)
  function animateCounter(el, target, duration) {
    const start = performance.now();
    const update = (now) => {
      const elapsed = now - start;
      const progress = Math.min(elapsed / duration, 1);
      const ease = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.round(ease * target).toLocaleString('es-AR');
      if (progress < 1) requestAnimationFrame(update);
    };
    requestAnimationFrame(update);
  }

  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.dataset.target);
        animateCounter(el, target, 1400);
        counterObserver.unobserve(el);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-target]').forEach(el => counterObserver.observe(el));
</script>
@endpush
