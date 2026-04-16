@extends('layouts.marketing')

@section('title', 'Para complejos deportivos — TuCancha')
@section('meta_description', 'Digitalizá tu complejo deportivo con TuCancha. Agenda online, cobros con MercadoPago, cero comisiones. Primeros complejos: 3 meses gratis.')

@push('jsonld')
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Para complejos', 'item' => url('/para-complejos')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

@push('styles')
<style>
  /* ── Hero ────────────────────────────────────────── */
  .pc-hero {
    padding: 48px 0 0;
  }

  .pc-hero-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .pc-hero-inner {
    background: #111;
    border-radius: calc(2rem - 5px);
    padding: 80px 48px;
    position: relative;
    overflow: hidden;
    text-align: center;
  }

  .pc-hero-orb-1 {
    position: absolute;
    top: -80px;
    right: -60px;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.10) 0%, transparent 60%);
    pointer-events: none;
  }

  .pc-hero-orb-2 {
    position: absolute;
    bottom: -60px;
    left: -40px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.06) 0%, transparent 60%);
    pointer-events: none;
  }

  .pc-hero-badge {
    display: inline-flex;
    padding: 5px 16px;
    border-radius: 999px;
    background: rgba(74,222,128,0.10);
    border: 1px solid rgba(74,222,128,0.22);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #6eeaa0;
    margin-bottom: 20px;
    position: relative;
  }

  .pc-hero-inner h1 {
    margin: 0 0 14px;
    font-size: clamp(32px, 4.5vw, 52px);
    font-weight: 900;
    letter-spacing: -0.04em;
    color: #fff;
    line-height: 1.05;
    position: relative;
    text-wrap: balance;
  }

  .pc-hero-inner h1 em {
    font-style: normal;
    color: #4ade80;
  }

  .pc-hero-subtitle {
    margin: 0 auto 32px;
    max-width: 540px;
    font-size: 16px;
    color: rgba(255,255,255,0.50);
    line-height: 1.6;
    position: relative;
  }

  .pc-hero-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
  }

  .pc-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 15px 30px;
    background: #22c55e;
    color: #052e14;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    transition: background 300ms var(--ease-out-expo), color 300ms var(--ease-out-expo), transform 300ms var(--ease-out-expo);
    box-shadow: 0 4px 16px rgba(34,197,94,0.25);
  }

  .pc-btn-primary:hover {
    background: #16a34a;
    color: #fff;
    transform: translateY(-2px);
  }

  .pc-btn-primary:active {
    transform: translateY(0) scale(0.97);
  }

  .pc-btn-primary svg {
    transition: transform 250ms var(--ease-out-expo);
  }

  .pc-btn-primary:hover svg {
    transform: translateX(3px);
  }

  .pc-btn-wa {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 15px 30px;
    background: #25d366;
    color: #fff;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    transition: background 300ms var(--ease-out-expo), transform 300ms var(--ease-out-expo);
    box-shadow: 0 4px 16px rgba(37,211,102,0.25);
  }

  .pc-btn-wa:hover {
    background: #1da851;
    transform: translateY(-2px);
  }

  .pc-btn-wa:active {
    transform: translateY(0) scale(0.97);
  }

  .pc-btn-ghost {
    display: inline-flex;
    align-items: center;
    padding: 15px 24px;
    color: rgba(255,255,255,0.75);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: background 300ms var(--ease-out-expo), border-color 300ms var(--ease-out-expo), transform 300ms var(--ease-out-expo);
  }

  .pc-btn-ghost:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.4);
    transform: translateY(-2px);
  }

  @media (max-width: 768px) {
    .pc-hero-inner { padding: 56px 24px; }
    .pc-hero-actions { flex-direction: column; align-items: center; }
  }

  @media (max-width: 480px) {
    .pc-hero { padding: 24px 0 0; }
    .pc-hero-shell { border-radius: 1.25rem; }
    .pc-hero-inner { border-radius: calc(1.25rem - 5px); padding: 40px 20px; }
  }

  /* ── Launch Offer ──────────────────────────────── */
  .pc-launch {
    padding: 64px 0;
  }

  .pc-launch-shell {
    background: rgba(34,197,94,0.04);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(34,197,94,0.10);
  }

  .pc-launch-inner {
    background: linear-gradient(145deg, #0a3d21 0%, #0f5c32 40%, #147a42 100%);
    border-radius: calc(2rem - 5px);
    padding: 56px 48px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .pc-launch-inner::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -30px;
    width: 250px;
    height: 250px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.12) 0%, transparent 60%);
    pointer-events: none;
  }

  .pc-launch-badge {
    display: inline-flex;
    padding: 6px 18px;
    border-radius: 999px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.20);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .10em;
    text-transform: uppercase;
    color: #fbbf24;
    margin-bottom: 20px;
    position: relative;
    animation: pc-launch-pulse 2.5s ease-in-out infinite;
  }

  @keyframes pc-launch-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(251,191,36,0.15); }
    50% { box-shadow: 0 0 0 10px rgba(251,191,36,0); }
  }

  .pc-launch-inner h2 {
    margin: 0 0 12px;
    font-size: clamp(26px, 3.5vw, 40px);
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.03em;
    position: relative;
    text-wrap: balance;
  }

  .pc-launch-inner h2 em {
    font-style: normal;
    color: #4ade80;
  }

  .pc-launch-inner > p {
    margin: 0 auto 32px;
    max-width: 500px;
    font-size: 16px;
    color: rgba(255,255,255,0.60);
    line-height: 1.6;
    position: relative;
  }

  .pc-launch-perks {
    display: flex;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
    margin-bottom: 32px;
    position: relative;
  }

  .pc-launch-perk {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
  }

  .pc-launch-perk svg {
    color: #4ade80;
    flex-shrink: 0;
  }

  .pc-launch-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
  }

  @media (max-width: 768px) {
    .pc-launch-inner { padding: 40px 24px; }
    .pc-launch-perks { flex-direction: column; align-items: center; gap: 14px; }
    .pc-launch-actions { flex-direction: column; align-items: center; }
  }

  /* ── Antes vs Ahora ────────────────────────────── */
  .pc-compare {
    padding: 64px 0;
  }

  .pc-compare .section-head {
    text-align: center;
    margin-bottom: 40px;
  }

  .pc-compare-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }

  .pc-compare-shell {
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .pc-compare-shell--antes {
    background: rgba(255,255,255,0.04);
  }

  .pc-compare-shell--ahora {
    background: rgba(34,197,94,0.04);
    border-color: rgba(34,197,94,0.08);
  }

  .pc-compare-card {
    border-radius: calc(1.5rem - 4px);
    padding: 36px 28px;
    height: 100%;
  }

  .pc-compare-shell--antes .pc-compare-card {
    background: #111;
  }

  .pc-compare-shell--ahora .pc-compare-card {
    background: rgba(34,197,94,.06);
  }

  .pc-compare-label {
    display: inline-flex;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-bottom: 20px;
  }

  .pc-compare-shell--antes .pc-compare-label {
    background: rgba(229,57,53,.1);
    color: #f87171;
  }

  .pc-compare-shell--ahora .pc-compare-label {
    background: rgba(34,197,94,0.12);
    color: #6eeaa0;
  }

  .pc-compare-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .pc-compare-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 15px;
    line-height: 1.5;
    color: var(--color-text);
  }

  .pc-compare-list .pc-icon-wrap {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1px;
  }

  .pc-compare-shell--antes .pc-icon-wrap {
    background: rgba(229,57,53,.1);
    color: #f87171;
  }

  .pc-compare-shell--ahora .pc-icon-wrap {
    background: rgba(34,197,94,.1);
    color: #22c55e;
  }

  @media (max-width: 768px) {
    .pc-compare-grid {
      grid-template-columns: 1fr;
      max-width: 480px;
      margin: 0 auto;
    }
  }

  /* ── Panel Preview ─────────────────────────────── */
  .pc-panel {
    padding: 0 0 64px;
  }

  .pc-panel .section-head {
    text-align: center;
    margin-bottom: 40px;
  }

  .pc-panel-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 16px;
  }

  .pc-panel-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .pc-panel-card {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    padding: 32px 24px;
    text-align: center;
    height: 100%;
  }

  .pc-panel-mockup {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    min-height: 160px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .pc-mockup-bar {
    display: flex;
    gap: 6px;
    margin-bottom: 8px;
  }

  .pc-mockup-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: rgba(255,255,255,0.15);
  }

  .pc-mockup-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: rgba(255,255,255,0.04);
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .pc-mockup-row-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
  }

  .pc-mockup-row-line {
    height: 6px;
    border-radius: 3px;
    background: rgba(255,255,255,0.10);
    flex: 1;
  }

  .pc-mockup-row-badge {
    padding: 3px 10px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 700;
    flex-shrink: 0;
  }

  .pc-panel-card h3 {
    font-size: 17px;
    font-weight: 900;
    letter-spacing: -0.02em;
    margin: 0 0 8px;
    color: #e8e8e8;
  }

  .pc-panel-card p {
    font-size: 13px;
    line-height: 1.6;
    color: #888;
    margin: 0;
  }

  @media (max-width: 768px) {
    .pc-panel-grid {
      grid-template-columns: 1fr;
      max-width: 400px;
      margin: 0 auto;
    }
  }

  /* ── Features Grid ─────────────────────────────── */
  .pc-features {
    padding: 0 0 64px;
  }

  .pc-features .section-head {
    text-align: center;
    margin-bottom: 40px;
  }

  .pc-features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }

  .pc-feat-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
    transition: transform 500ms var(--ease-out-expo), box-shadow 500ms var(--ease-out-expo);
  }

  .pc-feat-shell:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 48px rgba(34,197,94,0.12);
  }

  .pc-feat-card {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    padding: 36px 28px;
    text-align: center;
    height: 100%;
  }

  .pc-feat-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: rgba(34,197,94,.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
    color: #22c55e;
    transition: transform 400ms var(--ease-out-expo), background 400ms var(--ease-out-expo);
  }

  .pc-feat-shell:hover .pc-feat-icon {
    transform: scale(1.1);
    background: rgba(74,222,128,.18);
  }

  .pc-feat-card h3 {
    font-size: 18px;
    font-weight: 900;
    letter-spacing: -0.02em;
    margin: 0 0 10px;
    color: var(--color-text);
  }

  .pc-feat-card p {
    font-size: 14px;
    line-height: 1.65;
    color: #a0a0a0;
    margin: 0;
  }

  @media (max-width: 768px) {
    .pc-features-grid {
      grid-template-columns: 1fr 1fr;
      max-width: 560px;
      margin: 0 auto;
    }
  }

  @media (max-width: 480px) {
    .pc-features-grid {
      grid-template-columns: 1fr;
      max-width: 420px;
    }
  }

  /* ── How it works ──────────────────────────────── */
  .pc-steps {
    padding: 0 0 64px;
  }

  .pc-steps .section-head {
    text-align: center;
    margin-bottom: 40px;
  }

  .pc-steps-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    counter-reset: step-counter;
  }

  .pc-step-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .pc-step-card {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    padding: 32px 24px;
    text-align: center;
    height: 100%;
    counter-increment: step-counter;
  }

  .pc-step-num {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: rgba(34,197,94,.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 18px;
    font-weight: 900;
    color: #4ade80;
  }

  .pc-step-card h3 {
    font-size: 16px;
    font-weight: 800;
    margin: 0 0 8px;
    color: #e8e8e8;
  }

  .pc-step-card p {
    font-size: 13px;
    line-height: 1.6;
    color: #888;
    margin: 0;
  }

  @media (max-width: 768px) {
    .pc-steps-grid {
      grid-template-columns: 1fr 1fr;
      max-width: 500px;
      margin: 0 auto;
    }
  }

  @media (max-width: 480px) {
    .pc-steps-grid {
      grid-template-columns: 1fr;
      max-width: 360px;
    }
  }

  /* ── Comparison Table ──────────────────────────── */
  .pc-table-section {
    padding: 0 0 64px;
  }

  .pc-table-section .section-head {
    text-align: center;
    margin-bottom: 40px;
  }

  .pc-table-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
    overflow: hidden;
  }

  .pc-table-wrap {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    overflow-x: auto;
  }

  .pc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
  }

  .pc-table thead th {
    padding: 20px 24px;
    text-align: left;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: -0.01em;
    border-bottom: 2px solid var(--color-border-light);
  }

  .pc-table thead th:first-child {
    color: var(--color-text-muted);
  }

  .pc-table thead th:nth-child(2) {
    color: #22c55e;
    background: rgba(34,197,94,.06);
  }

  .pc-table thead th:nth-child(3) {
    color: var(--color-text-muted);
  }

  .pc-table tbody td {
    padding: 16px 24px;
    border-bottom: 1px solid var(--color-border-light);
    vertical-align: middle;
  }

  .pc-table tbody tr:last-child td {
    border-bottom: none;
  }

  .pc-table tbody tr:hover {
    background: var(--color-bg-hover);
  }

  .pc-table tbody td:first-child {
    font-weight: 700;
    color: var(--color-text);
  }

  .pc-table tbody td:nth-child(2) {
    background: rgba(34,197,94,.04);
  }

  .pc-check {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(34,197,94,.1);
    color: #22c55e;
  }

  .pc-cross {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(229,57,53,.1);
    color: #f87171;
  }

  .pc-partial {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(245,158,11,.08);
    color: #fbbf24;
  }

  @media (max-width: 600px) {
    .pc-table { font-size: 13px; }
    .pc-table thead th,
    .pc-table tbody td { padding: 12px 14px; }
  }

  /* ── CTA Final ─────────────────────────────────── */
  .pc-cta-shell {
    background: rgba(15,76,42,0.05);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(34,197,94,0.08);
    margin-bottom: 80px;
  }

  .pc-cta {
    background: linear-gradient(145deg, #0a3d21 0%, #0f5c32 40%, #147a42 100%);
    border-radius: calc(2rem - 5px);
    padding: 64px 48px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .pc-cta::before {
    content: '';
    position: absolute;
    top: -80px;
    right: -40px;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.12) 0%, transparent 60%);
    pointer-events: none;
  }

  .pc-cta h2 {
    margin: 0 0 12px;
    font-size: clamp(24px, 3vw, 36px);
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.03em;
    position: relative;
    text-wrap: balance;
  }

  .pc-cta > p {
    margin: 0 auto 28px;
    max-width: 440px;
    font-size: 15px;
    color: rgba(255,255,255,0.55);
    line-height: 1.6;
    position: relative;
  }

  .pc-cta-actions {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    position: relative;
  }

  @media (max-width: 768px) {
    .pc-cta { padding: 48px 24px; }
    .pc-cta-actions { flex-direction: column; align-items: center; }
  }

  /* ── Reduced Motion ────────────────────────────── */
  @media (prefers-reduced-motion: reduce) {
    .pc-feat-shell,
    .pc-feat-icon,
    .pc-btn-primary,
    .pc-btn-wa,
    .pc-btn-ghost,
    .pc-compare-shell,
    .pc-launch-badge {
      transition-duration: 0ms !important;
      animation: none !important;
    }
  }
</style>
@endpush

@section('content')

  {{-- Hero --}}
  <section class="pc-hero">
    <div class="container">
      <div class="pc-hero-shell">
        <div class="pc-hero-inner">
          <div class="pc-hero-orb-1"></div>
          <div class="pc-hero-orb-2"></div>
          <div class="pc-hero-badge" data-aos="fade-down" data-aos-duration="600">Oferta de lanzamiento</div>
          <h1 data-aos="fade-up" data-aos-delay="80" data-aos-duration="700">Tu complejo merece<br>una <em>agenda digital.</em></h1>
          <p class="pc-hero-subtitle" data-aos="fade-up" data-aos-delay="160" data-aos-duration="700">Reservas online, cobros con MercadoPago directo a tu cuenta y cero comisiones sobre tus ingresos. Estamos arrancando y buscamos los primeros complejos.</p>
          <div class="pc-hero-actions" data-aos="fade-up" data-aos-delay="240" data-aos-duration="700">
            <a href="https://wa.me/5491100000000?text=Hola%2C%20tengo%20un%20complejo%20y%20me%20interesa%20TuCancha" target="_blank" rel="noopener" class="pc-btn-wa">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Hablemos por WhatsApp
            </a>
            <a href="{{ route('register') }}" class="pc-btn-ghost">Crear cuenta</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Oferta de Lanzamiento --}}
  <section class="pc-launch">
    <div class="container">
      <div class="pc-launch-shell" data-aos="fade-up" data-aos-duration="700">
        <div class="pc-launch-inner">
          <div class="pc-launch-badge">Cupos limitados</div>
          <h2>Primeros complejos:<br><em>3 meses gratis.</em></h2>
          <p>Estamos en etapa de lanzamiento. Los primeros complejos que se sumen arrancan con 3 meses gratis de cualquier plan, sin tarjeta, sin letra chica. Queremos que pruebes la plataforma y nos ayudes a mejorarla.</p>

          <div class="pc-launch-perks">
            <div class="pc-launch-perk">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
              3 meses gratis
            </div>
            <div class="pc-launch-perk">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
              Sin tarjeta de credito
            </div>
            <div class="pc-launch-perk">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
              Soporte personalizado
            </div>
            <div class="pc-launch-perk">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
              Te ayudamos a cargar todo
            </div>
          </div>

          <div class="pc-launch-actions">
            <a href="https://wa.me/5491100000000?text=Hola%2C%20quiero%20sumar%20mi%20complejo%20a%20TuCancha%20con%20la%20oferta%20de%20lanzamiento" target="_blank" rel="noopener" class="pc-btn-wa">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
              Quiero los 3 meses gratis
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Antes vs Ahora --}}
  <section class="pc-compare">
    <div class="container">
      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">El cambio</span>
        <h2 class="section-title" style="text-align:center;">Antes vs Ahora con TuCancha</h2>
      </div>

      <div class="pc-compare-grid">
        {{-- Antes --}}
        <div class="pc-compare-shell pc-compare-shell--antes" data-aos="fade-right" data-aos-duration="600">
          <div class="pc-compare-card">
            <span class="pc-compare-label">Antes</span>
            <ul class="pc-compare-list">
              <li>
                <span class="pc-icon-wrap">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                </span>
                <span>Agenda en papel o WhatsApp</span>
              </li>
              <li>
                <span class="pc-icon-wrap">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                </span>
                <span>Llamadas para confirmar turnos</span>
              </li>
              <li>
                <span class="pc-icon-wrap">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                </span>
                <span>Cobrar en efectivo y perseguir morosos</span>
              </li>
              <li>
                <span class="pc-icon-wrap">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
                </span>
                <span>Canchas vacías y sin presencia online</span>
              </li>
            </ul>
          </div>
        </div>

        {{-- Ahora --}}
        <div class="pc-compare-shell pc-compare-shell--ahora" data-aos="fade-left" data-aos-delay="100" data-aos-duration="600">
          <div class="pc-compare-card">
            <span class="pc-compare-label">Ahora con TuCancha</span>
            <ul class="pc-compare-list">
              <li>
                <span class="pc-icon-wrap">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                <span>Reservas online 24/7 automáticas</span>
              </li>
              <li>
                <span class="pc-icon-wrap">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                <span>Los jugadores reservan y pagan solos</span>
              </li>
              <li>
                <span class="pc-icon-wrap">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                <span>MercadoPago directo a tu cuenta</span>
              </li>
              <li>
                <span class="pc-icon-wrap">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                <span>Tu complejo visible en Google y en la plataforma</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Panel Preview --}}
  <section class="pc-panel">
    <div class="container">
      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">Tu panel</span>
        <h2 class="section-title" style="text-align:center;">Esto es lo que vas a ver</h2>
        <p class="section-subtitle" style="text-align:center; margin:10px auto 0; max-width:480px;">Un panel simple donde gestionás tus canchas, reservas e ingresos desde cualquier dispositivo.</p>
      </div>

      <div class="pc-panel-grid">
        {{-- Mockup 1: Agenda --}}
        <div class="pc-panel-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
          <div class="pc-panel-card">
            <div class="pc-panel-mockup">
              <div class="pc-mockup-bar">
                <div class="pc-mockup-dot"></div>
                <div class="pc-mockup-dot"></div>
                <div class="pc-mockup-dot"></div>
              </div>
              <div class="pc-mockup-row">
                <div class="pc-mockup-row-dot" style="background:#22c55e;"></div>
                <div class="pc-mockup-row-line" style="width:60%;"></div>
                <div class="pc-mockup-row-badge" style="background:rgba(34,197,94,.15); color:#4ade80;">Confirmada</div>
              </div>
              <div class="pc-mockup-row">
                <div class="pc-mockup-row-dot" style="background:#fbbf24;"></div>
                <div class="pc-mockup-row-line" style="width:50%;"></div>
                <div class="pc-mockup-row-badge" style="background:rgba(251,191,36,.12); color:#fbbf24;">Pendiente</div>
              </div>
              <div class="pc-mockup-row">
                <div class="pc-mockup-row-dot" style="background:#22c55e;"></div>
                <div class="pc-mockup-row-line" style="width:70%;"></div>
                <div class="pc-mockup-row-badge" style="background:rgba(34,197,94,.15); color:#4ade80;">Confirmada</div>
              </div>
            </div>
            <h3>Agenda de reservas</h3>
            <p>Ves todas las reservas del día, la semana o el mes. Filtrá por cancha y estado.</p>
          </div>
        </div>

        {{-- Mockup 2: Ingresos --}}
        <div class="pc-panel-shell" data-aos="fade-up" data-aos-delay="80" data-aos-duration="600">
          <div class="pc-panel-card">
            <div class="pc-panel-mockup">
              <div class="pc-mockup-bar">
                <div class="pc-mockup-dot"></div>
                <div class="pc-mockup-dot"></div>
                <div class="pc-mockup-dot"></div>
              </div>
              <div style="text-align:center; padding: 8px 0 4px;">
                <div style="font-size:11px; color:#666; font-weight:600; text-transform:uppercase; letter-spacing:.06em;">Ingresos del mes</div>
                <div style="font-size:28px; font-weight:900; color:#4ade80; letter-spacing:-0.03em; margin:4px 0;">$185.000</div>
                <div style="font-size:11px; color:#666;">24 reservas confirmadas</div>
              </div>
              <div style="display:flex; gap:4px; align-items:flex-end; justify-content:center; height:32px; margin-top:4px;">
                <div style="width:12px; background:rgba(34,197,94,.3); border-radius:3px; height:40%;"></div>
                <div style="width:12px; background:rgba(34,197,94,.3); border-radius:3px; height:60%;"></div>
                <div style="width:12px; background:rgba(34,197,94,.3); border-radius:3px; height:45%;"></div>
                <div style="width:12px; background:rgba(34,197,94,.4); border-radius:3px; height:75%;"></div>
                <div style="width:12px; background:rgba(34,197,94,.4); border-radius:3px; height:55%;"></div>
                <div style="width:12px; background:rgba(34,197,94,.5); border-radius:3px; height:85%;"></div>
                <div style="width:12px; background:#22c55e; border-radius:3px; height:100%;"></div>
              </div>
            </div>
            <h3>Ingresos en tiempo real</h3>
            <p>Cuánto facturaste, cuántas reservas, todo actualizado al instante.</p>
          </div>
        </div>

        {{-- Mockup 3: Canchas --}}
        <div class="pc-panel-shell" data-aos="fade-up" data-aos-delay="160" data-aos-duration="600">
          <div class="pc-panel-card">
            <div class="pc-panel-mockup">
              <div class="pc-mockup-bar">
                <div class="pc-mockup-dot"></div>
                <div class="pc-mockup-dot"></div>
                <div class="pc-mockup-dot"></div>
              </div>
              <div class="pc-mockup-row">
                <div style="width:24px; height:24px; border-radius:6px; background:rgba(34,197,94,.15); flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                </div>
                <div class="pc-mockup-row-line" style="width:40%;"></div>
                <div style="font-size:10px; color:#888; flex-shrink:0;">Futbol 5</div>
              </div>
              <div class="pc-mockup-row">
                <div style="width:24px; height:24px; border-radius:6px; background:rgba(124,58,237,.15); flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                </div>
                <div class="pc-mockup-row-line" style="width:35%;"></div>
                <div style="font-size:10px; color:#888; flex-shrink:0;">Padel</div>
              </div>
              <div class="pc-mockup-row">
                <div style="width:24px; height:24px; border-radius:6px; background:rgba(34,197,94,.15); flex-shrink:0; display:flex; align-items:center; justify-content:center;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
                </div>
                <div class="pc-mockup-row-line" style="width:50%;"></div>
                <div style="font-size:10px; color:#888; flex-shrink:0;">Futbol 7</div>
              </div>
            </div>
            <h3>Tus canchas</h3>
            <p>Configurá cada cancha con su deporte, precios por horario, fotos y disponibilidad.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Features Grid --}}
  <section class="pc-features">
    <div class="container">
      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">Funcionalidades</span>
        <h2 class="section-title" style="text-align:center;">Todo lo que necesitás para gestionar tu complejo</h2>
      </div>

      <div class="pc-features-grid">
        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <h3>Agenda inteligente</h3>
            <p>Tus canchas, horarios y precios en un panel simple. Los jugadores reservan sin llamarte.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="80" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3>Cobros con MercadoPago</h3>
            <p>Conectás tu cuenta de MercadoPago y el dinero va directo a vos. Sin intermediarios.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="160" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3>Cero comisiones</h3>
            <p>Suscripción fija mensual. No tocamos ni un peso de tus reservas. Nunca.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
            </div>
            <h3>Panel de gestión</h3>
            <p>Reservas, ingresos, ocupación. Todo en tiempo real desde el celular o la compu.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="80" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <h3>Presencia online</h3>
            <p>Tu complejo con perfil propio, fotos, reseñas de jugadores y posicionamiento en Google.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="160" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <h3>Soporte dedicado</h3>
            <p>Te ayudamos a configurar todo. Nos sentamos con vos hasta que quede andando.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Cómo funciona --}}
  <section class="pc-steps">
    <div class="container">
      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">Proceso</span>
        <h2 class="section-title" style="text-align:center;">En 4 pasos ya estás funcionando</h2>
      </div>

      <div class="pc-steps-grid">
        <div class="pc-step-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
          <div class="pc-step-card">
            <div class="pc-step-num">1</div>
            <h3>Hablamos</h3>
            <p>Nos contactás por WhatsApp y te contamos cómo funciona. Sin compromiso.</p>
          </div>
        </div>

        <div class="pc-step-shell" data-aos="fade-up" data-aos-delay="80" data-aos-duration="600">
          <div class="pc-step-card">
            <div class="pc-step-num">2</div>
            <h3>Te creamos la cuenta</h3>
            <p>Cargamos tus canchas, horarios y precios. Vos no tenés que hacer nada.</p>
          </div>
        </div>

        <div class="pc-step-shell" data-aos="fade-up" data-aos-delay="160" data-aos-duration="600">
          <div class="pc-step-card">
            <div class="pc-step-num">3</div>
            <h3>Conectás MercadoPago</h3>
            <p>En 2 clicks conectás tu cuenta. Los pagos van directo a vos.</p>
          </div>
        </div>

        <div class="pc-step-shell" data-aos="fade-up" data-aos-delay="240" data-aos-duration="600">
          <div class="pc-step-card">
            <div class="pc-step-num">4</div>
            <h3>Empezás a recibir reservas</h3>
            <p>Tu complejo queda online y los jugadores pueden reservar al instante.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Comparison Table --}}
  <section class="pc-table-section">
    <div class="container">
      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">Comparativa</span>
        <h2 class="section-title" style="text-align:center;">TuCancha vs gestión tradicional</h2>
      </div>

      <div class="pc-table-shell" data-aos="fade-up" data-aos-duration="600">
        <div class="pc-table-wrap">
          <table class="pc-table">
            <thead>
              <tr>
                <th>Funcionalidad</th>
                <th>TuCancha</th>
                <th>Gestión tradicional</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Reservas online</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Cobro automático</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Agenda siempre actualizada</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-partial"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4"/><path d="M12 16h.01"/></svg></span></td>
              </tr>
              <tr>
                <td>Presencia en Google</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Reportes de ocupación</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Comisiones</td>
                <td><strong style="color:#22c55e;">0% — siempre</strong></td>
                <td><span style="color:#a0a0a0;">No aplica</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  {{-- CTA Final --}}
  <div class="container">
    <div class="pc-cta-shell" data-aos="fade-up" data-aos-duration="700">
      <div class="pc-cta">
        <h2>Sumate ahora y arrancá gratis</h2>
        <p>Escribinos por WhatsApp, te ayudamos a cargar todo y en el día ya estás recibiendo reservas.</p>
        <div class="pc-cta-actions">
          <a href="https://wa.me/5491100000000?text=Hola%2C%20quiero%20sumar%20mi%20complejo%20a%20TuCancha" target="_blank" rel="noopener" class="pc-btn-wa">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Hablemos por WhatsApp
          </a>
          <a href="{{ route('register') }}" class="pc-btn-ghost">Crear cuenta</a>
        </div>
      </div>
    </div>
  </div>

@endsection
