@extends('layouts.marketing')

@section('title', 'Para complejos deportivos — TuCancha')
@section('meta_description', 'Digitalizá tu complejo deportivo con TuCancha. Agenda online, cobros automáticos con MercadoPago, sin comisiones. Probá {{ $trialDays }} días gratis.')

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

  /* ── Pricing Preview ───────────────────────────── */
  .pc-pricing {
    padding: 64px 0;
  }

  .pc-pricing .section-head {
    text-align: center;
    margin-bottom: 40px;
  }

  .pc-pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    align-items: start;
  }

  .pc-price-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
    transition: transform 500ms var(--ease-out-expo), box-shadow 500ms var(--ease-out-expo);
  }

  .pc-price-shell:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 48px rgba(0,0,0,0.25);
  }

  .pc-price-shell--featured {
    background: rgba(34,197,94,0.06);
    border-color: rgba(34,197,94,0.15);
  }

  .pc-price-shell--featured:hover {
    box-shadow: 0 20px 48px rgba(34,197,94,0.10);
  }

  .pc-price-card {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    padding: 36px 28px;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .pc-price-badge {
    display: inline-flex;
    align-self: flex-start;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: 16px;
    background: var(--color-border-light);
    color: var(--color-text-secondary);
  }

  .pc-price-badge--accent {
    background: rgba(34,197,94,0.12);
    color: #6eeaa0;
  }

  .pc-price-name {
    font-size: 22px;
    font-weight: 900;
    letter-spacing: -0.02em;
    margin: 0 0 4px;
    color: var(--color-text);
  }

  .pc-price-tagline {
    font-size: 13px;
    color: var(--color-text-muted);
    margin: 0 0 20px;
  }

  .pc-price-amount {
    font-size: 36px;
    font-weight: 900;
    letter-spacing: -0.03em;
    color: var(--color-text);
    margin: 0 0 4px;
  }

  .pc-price-amount span {
    font-size: 15px;
    font-weight: 600;
    color: var(--color-text-secondary);
    letter-spacing: 0;
  }

  .pc-price-features {
    list-style: none;
    padding: 0;
    margin: 20px 0 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
  }

  .pc-price-features li {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    color: var(--color-text-secondary);
    line-height: 1.4;
  }

  .pc-price-features li svg {
    flex-shrink: 0;
    color: #22c55e;
  }

  .pc-pricing-link {
    text-align: center;
    margin-top: 32px;
  }

  .pc-pricing-link a {
    font-size: 15px;
    font-weight: 700;
    color: var(--color-primary);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: border-color var(--transition-fast);
  }

  .pc-pricing-link a:hover {
    border-color: var(--color-primary);
  }

  @media (max-width: 768px) {
    .pc-pricing-grid {
      grid-template-columns: 1fr;
      max-width: 400px;
      margin: 0 auto;
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

  .pc-cta-link {
    font-size: 14px;
    font-weight: 600;
    color: rgba(255,255,255,0.55);
    text-decoration: none;
    transition: color var(--transition-fast);
  }

  .pc-cta-link:hover {
    color: rgba(255,255,255,0.85);
  }

  @media (max-width: 768px) {
    .pc-cta { padding: 48px 24px; }
    .pc-cta-actions { flex-direction: column; align-items: center; }
  }

  /* ── Reduced Motion ────────────────────────────── */
  @media (prefers-reduced-motion: reduce) {
    .pc-feat-shell,
    .pc-feat-icon,
    .pc-price-shell,
    .pc-btn-primary,
    .pc-btn-ghost,
    .pc-compare-shell {
      transition-duration: 0ms !important;
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
          <div class="pc-hero-badge" data-aos="fade-down" data-aos-duration="600">Para complejos deportivos</div>
          <h1 data-aos="fade-up" data-aos-delay="80" data-aos-duration="700">Digitalizá tu complejo.<br><em>Sin comisiones.</em></h1>
          <p class="pc-hero-subtitle" data-aos="fade-up" data-aos-delay="160" data-aos-duration="700">Agenda online, cobros automáticos con MercadoPago y visibilidad ante miles de jugadores. Probá gratis {{ $trialDays }} días.</p>
          <div class="pc-hero-actions" data-aos="fade-up" data-aos-delay="240" data-aos-duration="700">
            <a href="{{ route('register') }}" class="pc-btn-primary">
              Registrá tu complejo
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </a>
            <a href="{{ route('planes') }}" class="pc-btn-ghost">Ver planes</a>
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
                <span>Canchas vacías sin visibilidad</span>
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
                <span>Miles de jugadores te encuentran en TuCancha</span>
              </li>
            </ul>
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
            <p>El pago va directo a tu cuenta. Sin intermediarios, sin comisiones de TuCancha.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="160" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <h3>Visibilidad</h3>
            <p>Tu complejo aparece en TuCancha donde miles de jugadores buscan cancha cada semana.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
            </div>
            <h3>Panel de gestión</h3>
            <p>Reservas, ingresos, ocupación. Todo en tiempo real desde cualquier dispositivo.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="80" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h3>Cero comisiones</h3>
            <p>Suscripción fija mensual. No tocamos ni un peso de tus reservas.</p>
          </div>
        </div>

        <div class="pc-feat-shell" data-aos="fade-up" data-aos-delay="160" data-aos-duration="600">
          <div class="pc-feat-card">
            <div class="pc-feat-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <h3>Soporte dedicado</h3>
            <p>Te ayudamos a configurar todo y a sacarle el máximo a la plataforma.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Pricing Preview --}}
  <section class="pc-pricing">
    <div class="container">
      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">Planes</span>
        <h2 class="section-title" style="text-align:center;">Precios simples, sin sorpresas</h2>
        <p class="section-subtitle" style="text-align:center; margin:10px auto 0;">Todos los planes incluyen {{ $trialDays }} días de prueba gratis. Sin tarjeta, sin compromiso.</p>
      </div>

      <div class="pc-pricing-grid">
        @foreach($plans as $index => $plan)
        @php
          $taglines = [
              'starter' => 'Ideal para empezar',
              'pro' => 'Para complejos en crecimiento',
              'full' => 'Para complejos grandes',
          ];
          $featuresByPlan = [
              'starter' => [
                  $plan->maxFieldsLabel(),
                  'Reservas online 24/7',
                  'Cobro por MercadoPago',
                  'Panel de administración',
              ],
              'pro' => [
                  $plan->maxFieldsLabel(),
                  'Todo de Starter',
                  'Soporte prioritario',
                  'Posicionamiento prioritario',
              ],
              'full' => [
                  $plan->maxFieldsLabel(),
                  'Todo de Pro',
                  'Badge "Premium" exclusivo',
                  'Máxima visibilidad y prioridad',
              ],
          ];
          $features = $featuresByPlan[$plan->slug] ?? [$plan->maxFieldsLabel(), 'Reservas online 24/7'];
          $tagline = $taglines[$plan->slug] ?? '';
          $price = number_format($plan->monthly_price, 0, ',', '.');
        @endphp
        <div class="pc-price-shell {{ $plan->is_featured ? 'pc-price-shell--featured' : '' }}" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}" data-aos-duration="600">
          <div class="pc-price-card">
            @if($plan->is_featured)
              <span class="pc-price-badge pc-price-badge--accent">Más popular</span>
            @elseif($plan->trial_days > 0)
              <span class="pc-price-badge">{{ $plan->trial_days }} días gratis</span>
            @else
              <span class="pc-price-badge">{{ $plan->name }}</span>
            @endif
            <h3 class="pc-price-name">{{ $plan->name }}</h3>
            <p class="pc-price-tagline">{{ $tagline }}</p>
            <p class="pc-price-amount">${{ $price }} <span>/mes</span></p>
            <ul class="pc-price-features">
              @foreach($features as $feature)
              <li>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                {{ $feature }}
              </li>
              @endforeach
            </ul>
          </div>
        </div>
        @endforeach
      </div>

      <div class="pc-pricing-link" data-aos="fade-up" data-aos-delay="200" data-aos-duration="600">
        <a href="{{ route('planes') }}">Ver todos los detalles</a>
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
                <td>Visibilidad online</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Reportes de ocupación</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Costo</td>
                <td><strong style="color:#22c55e;">Fijo desde ${{ number_format($plans->min('monthly_price'), 0, ',', '.') }}/mes</strong></td>
                <td><span style="color:#a0a0a0;">Tiempo, errores, ingresos perdidos</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  {{-- Comparativa vs Competidores --}}
  <section class="pc-table-section">
    <div class="container">
      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">vs otras plataformas</span>
        <h2 class="section-title" style="text-align:center;">TuCancha vs la competencia</h2>
      </div>

      <div class="pc-table-shell" data-aos="fade-up" data-aos-duration="600">
        <div class="pc-table-wrap">
          <table class="pc-table">
            <thead>
              <tr>
                <th></th>
                <th>TuCancha</th>
                <th>Otras plataformas</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Comisiones sobre reservas</td>
                <td><strong style="color:#22c55e;">0%</strong></td>
                <td><span style="color:#ef4444; font-weight:700;">3% o más</span></td>
              </tr>
              <tr>
                <td>Buscar jugadores (Falta Uno)</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Perfil deportivo con stats</td>
                <td><span class="pc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pc-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Precio transparente</td>
                <td><strong style="color:#22c55e;">Fijo, sin sorpresas</strong></td>
                <td><span style="color:#a0a0a0;">Variable o no público</span></td>
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
        <h2>Sumá tu complejo hoy</h2>
        <p>{{ $trialDays }} días gratis. Sin tarjeta. Sin compromiso.</p>
        <div class="pc-cta-actions">
          <a href="{{ route('register') }}" class="pc-btn-primary">
            Crear cuenta gratis
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
          </a>
          <a href="mailto:tucancha10@gmail.com" class="pc-cta-link">Escribinos</a>
        </div>
      </div>
    </div>
  </div>

@endsection
