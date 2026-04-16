@extends('layouts.marketing')

@section('title', 'Por qué TuCancha — Lo que nos hace diferentes')
@section('meta_description', 'TuCancha vs otras plataformas de reservas deportivas. Sin comisiones, Falta Uno para encontrar jugadores, perfil deportivo y mas. Compará y elegí.')

@push('jsonld')
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Por qué TuCancha', 'item' => url('/por-que-tucancha')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

@push('styles')
<style>
  /* ── Hero ────────────────────────────────────────── */
  .pqt-hero {
    padding: 48px 0 0;
  }

  .pqt-hero-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .pqt-hero-inner {
    background: #111;
    border-radius: calc(2rem - 5px);
    padding: 72px 48px;
    position: relative;
    overflow: hidden;
    text-align: center;
  }

  .pqt-hero-orb-1 {
    position: absolute;
    top: -80px;
    right: -60px;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.10) 0%, transparent 60%);
    pointer-events: none;
  }

  .pqt-hero-orb-2 {
    position: absolute;
    bottom: -60px;
    left: -40px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.06) 0%, transparent 60%);
    pointer-events: none;
  }

  .pqt-hero-badge {
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

  .pqt-hero-inner h1 {
    margin: 0 0 14px;
    font-size: clamp(32px, 4.5vw, 52px);
    font-weight: 900;
    letter-spacing: -0.04em;
    color: #fff;
    line-height: 1.05;
    position: relative;
    text-wrap: balance;
  }

  .pqt-hero-inner h1 em {
    font-style: normal;
    color: #4ade80;
  }

  .pqt-hero-inner > p {
    margin: 0 auto;
    max-width: 500px;
    font-size: 16px;
    color: rgba(255,255,255,0.50);
    line-height: 1.6;
    position: relative;
  }

  @media (max-width: 768px) {
    .pqt-hero-inner { padding: 48px 24px; }
  }

  @media (max-width: 480px) {
    .pqt-hero { padding: 24px 0 0; }
    .pqt-hero-shell { border-radius: 1.25rem; }
    .pqt-hero-inner { border-radius: calc(1.25rem - 5px); padding: 40px 20px; }
  }

  /* ── Comparison Table ───────────────────────────── */
  .pqt-table-section {
    padding: 64px 0;
  }

  .pqt-table-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
    overflow: hidden;
  }

  .pqt-table-wrap {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    overflow-x: auto;
  }

  .pqt-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
  }

  .pqt-table thead th {
    padding: 20px 24px;
    text-align: left;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: -0.01em;
    border-bottom: 2px solid var(--color-border-light, #f0f0f0);
  }

  .pqt-table thead th:first-child {
    color: var(--color-text-muted, #999);
  }

  .pqt-table thead th:nth-child(2) {
    color: #22c55e;
    background: rgba(34,197,94,.06);
  }

  .pqt-table thead th:nth-child(3) {
    color: var(--color-text-muted, #999);
  }

  .pqt-table tbody td {
    padding: 16px 24px;
    border-bottom: 1px solid var(--color-border-light, #f0f0f0);
    vertical-align: middle;
  }

  .pqt-table tbody tr:last-child td {
    border-bottom: none;
  }

  .pqt-table tbody tr:hover {
    background: var(--color-bg-hover, #f4f4f4);
  }

  .pqt-table tbody td:first-child {
    font-weight: 700;
    color: var(--color-text, #111);
  }

  .pqt-table tbody td:nth-child(2) {
    background: rgba(34,197,94,.04);
  }

  .pqt-check {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(34,197,94,.1);
    color: #22c55e;
  }

  .pqt-cross {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: rgba(229,57,53,.1);
    color: #f87171;
  }

  .pqt-partial {
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
    .pqt-table { font-size: 13px; }
    .pqt-table thead th,
    .pqt-table tbody td { padding: 12px 14px; }
  }

  /* ── Differentiators ────────────────────────────── */
  .pqt-diff-section {
    padding: 0 0 80px;
  }

  .pqt-diff-section .section-head {
    text-align: center;
    margin-bottom: 40px;
  }

  .pqt-diff-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }

  .pqt-diff-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
    transition: transform 500ms var(--ease-out-expo), box-shadow 500ms var(--ease-out-expo);
  }

  .pqt-diff-shell:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 48px rgba(34,197,94,0.12);
  }

  .pqt-diff-card {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    padding: 36px 28px;
    text-align: center;
    height: 100%;
  }

  .pqt-diff-icon {
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

  .pqt-diff-shell:hover .pqt-diff-icon {
    transform: scale(1.1);
    background: rgba(74,222,128,.18);
  }

  .pqt-diff-card h3 {
    font-size: 18px;
    font-weight: 900;
    letter-spacing: -0.02em;
    margin: 0 0 10px;
    color: var(--color-text, #111);
  }

  .pqt-diff-card p {
    font-size: 14px;
    line-height: 1.65;
    color: #a0a0a0;
    margin: 0;
  }

  @media (max-width: 768px) {
    .pqt-diff-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto; }
  }

  /* ── CTA ─────────────────────────────────────────── */
  .pqt-cta-shell {
    background: rgba(15,76,42,0.05);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(34,197,94,0.08);
    margin-bottom: 80px;
  }

  .pqt-cta {
    background: linear-gradient(145deg, #0a3d21 0%, #0f5c32 40%, #147a42 100%);
    border-radius: calc(2rem - 5px);
    padding: 64px 48px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .pqt-cta::before {
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

  .pqt-cta h2 {
    margin: 0 0 12px;
    font-size: clamp(24px, 3vw, 36px);
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.03em;
    position: relative;
    text-wrap: balance;
  }

  .pqt-cta > p {
    margin: 0 auto 28px;
    max-width: 440px;
    font-size: 15px;
    color: rgba(255,255,255,0.55);
    line-height: 1.6;
    position: relative;
  }

  .pqt-cta-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
  }

  .pqt-cta-btn {
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

  .pqt-cta-btn:hover {
    background: #16a34a;
    color: #fff;
    transform: translateY(-2px);
  }

  .pqt-cta-btn:active {
    transform: translateY(0) scale(0.97);
  }

  .pqt-cta-btn svg {
    transition: transform 250ms var(--ease-out-expo);
  }

  .pqt-cta-btn:hover svg {
    transform: translateX(3px);
  }

  .pqt-cta-ghost {
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

  .pqt-cta-ghost:hover {
    background: rgba(255,255,255,0.08);
    border-color: rgba(255,255,255,0.4);
    transform: translateY(-2px);
  }

  @media (max-width: 768px) {
    .pqt-cta { padding: 48px 24px; }
    .pqt-cta-actions { flex-direction: column; align-items: center; }
  }

  /* ── Reduced Motion ─────────────────────────────── */
  @media (prefers-reduced-motion: reduce) {
    .pqt-diff-shell,
    .pqt-diff-icon,
    .pqt-cta-btn,
    .pqt-cta-ghost {
      transition-duration: 0ms !important;
    }
  }
</style>
@endpush

@section('content')

  {{-- Hero --}}
  <section class="pqt-hero">
    <div class="container">
      <div class="pqt-hero-shell">
        <div class="pqt-hero-inner">
          <div class="pqt-hero-orb-1"></div>
          <div class="pqt-hero-orb-2"></div>
          <div class="pqt-hero-badge" data-aos="fade-down" data-aos-duration="600">Comparativa</div>
          <h1 data-aos="fade-up" data-aos-delay="80" data-aos-duration="700">Por que elegir<br><em>TuCancha</em></h1>
          <p data-aos="fade-up" data-aos-delay="160" data-aos-duration="700">No somos solo otra plataforma de reservas. Esto es lo que nos hace diferentes.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- Comparison Table --}}
  <section class="pqt-table-section">
    <div class="container">
      <div class="pqt-table-shell" data-aos="fade-up" data-aos-duration="600">
        <div class="pqt-table-wrap">
          <table class="pqt-table">
            <thead>
              <tr>
                <th>Funcionalidad</th>
                <th>TuCancha</th>
                <th>Otras plataformas</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Reservas online</td>
                <td><span class="pqt-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pqt-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
              </tr>
              <tr>
                <td>Comisiones sobre reservas</td>
                <td><strong style="color:#22c55e;">0%</strong></td>
                <td><span style="color:#ef4444; font-weight:700;">3% o mas</span></td>
              </tr>
              <tr>
                <td>Buscar jugadores (Falta Uno)</td>
                <td><span class="pqt-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pqt-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Perfil deportivo con stats</td>
                <td><span class="pqt-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pqt-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Rating y reputacion</td>
                <td><span class="pqt-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pqt-cross"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg></span></td>
              </tr>
              <tr>
                <td>Cobro automatico al complejo</td>
                <td><span class="pqt-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pqt-partial"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4"/><path d="M12 16h.01"/></svg></span></td>
              </tr>
              <tr>
                <td>Panel de gestion completo</td>
                <td><span class="pqt-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span class="pqt-partial"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4"/><path d="M12 16h.01"/></svg></span></td>
              </tr>
              <tr>
                <td>Precio transparente</td>
                <td><strong style="color:#22c55e;">Fijo, sin sorpresas</strong></td>
                <td><span style="color:#a0a0a0;">Variable o no publico</span></td>
              </tr>
              <tr>
                <td>Identidad argentina</td>
                <td><span class="pqt-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span></td>
                <td><span style="color:#a0a0a0; font-size:13px;">LATAM generico</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  {{-- Differentiators --}}
  <section class="pqt-diff-section">
    <div class="container">
      <div class="section-head" data-aos="fade-up" data-aos-duration="600">
        <span class="section-label">Lo que nos hace unicos</span>
        <h2 class="section-title" style="text-align:center;">Funciones que solo encontrás en TuCancha</h2>
      </div>

      <div class="pqt-diff-grid">
        <div class="pqt-diff-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="600">
          <div class="pqt-diff-card">
            <div class="pqt-diff-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h3>Falta Uno</h3>
            <p>Publicá tu partido y jugadores de tu zona se suman solos. Sin mandar mensajes, sin coordinar, sin rogar.</p>
          </div>
        </div>

        <div class="pqt-diff-shell" data-aos="fade-up" data-aos-delay="80" data-aos-duration="600">
          <div class="pqt-diff-card">
            <div class="pqt-diff-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
            </div>
            <h3>Perfil deportivo</h3>
            <p>Stats, rating, historial de partidos y categoria por deporte. Tu reputacion como jugador, en un solo lugar.</p>
          </div>
        </div>

        <div class="pqt-diff-shell" data-aos="fade-up" data-aos-delay="160" data-aos-duration="600">
          <div class="pqt-diff-card">
            <div class="pqt-diff-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h3>Cero comisiones</h3>
            <p>Suscripcion fija para complejos. No tocamos ni un peso de tus reservas. Tu plata va directo a tu MercadoPago.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- CTA --}}
  <div class="container">
    <div class="pqt-cta-shell" data-aos="fade-up" data-aos-duration="700">
      <div class="pqt-cta">
        <h2>Probalo vos mismo</h2>
        <p>Reservá una cancha, publicá un partido, o registrá tu complejo. Sin compromiso.</p>
        <div class="pqt-cta-actions">
          <a href="{{ route('venues.index') }}" class="pqt-cta-btn">
            Ver complejos
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
          </a>
          <a href="{{ route('planes') }}" class="pqt-cta-ghost">Planes para complejos</a>
        </div>
      </div>
    </div>
  </div>

@endsection
