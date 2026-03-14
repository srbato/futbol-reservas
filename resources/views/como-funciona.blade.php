@extends('layouts.marketing')

@section('title', 'Cómo funciona — TuCancha')

@push('styles')
  /* ── Hero ────────────────────────────────────────── */
  .cf-hero {
    padding: 40px 0 0 0;
  }

  .cf-hero-inner {
    background: linear-gradient(135deg, #111 0%, #1f1f1f 100%);
    border-radius: 28px;
    padding: 52px 48px;
    color: #fff;
    text-align: center;
  }

  .cf-hero-inner h1 {
    margin: 0 0 14px 0;
    font-size: 54px;
    line-height: 1.04;
    letter-spacing: -0.03em;
  }

  .cf-hero-inner p {
    margin: 0;
    color: rgba(255,255,255,.78);
    font-size: 19px;
    line-height: 1.6;
  }

  /* ── Flow sections ───────────────────────────────── */
  .flow-section { padding: 56px 0; }

  .flow-section + .flow-section {
    padding-top: 0;
  }

  /* ── Audience toggle ─────────────────────────────── */
  .audience-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 40px;
  }

  .audience-tab {
    padding: 10px 22px;
    border-radius: 999px;
    border: 1px solid #e0e0e0;
    background: #fff;
    font-size: 14px;
    font-weight: 700;
    color: #555;
    cursor: default;
  }

  .audience-tab.active {
    background: #111;
    color: #fff;
    border-color: #111;
  }

  .audience-tab.owner.active {
    background: #1a3a2a;
    border-color: #1a3a2a;
  }

  /* ── Steps grid ──────────────────────────────────── */
  .steps-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    position: relative;
  }

  /* Connecting line between cards */
  .steps-grid::before {
    content: '';
    position: absolute;
    top: 48px;
    left: calc(25% + 8px);
    right: calc(25% + 8px);
    height: 2px;
    background: linear-gradient(to right, #e0e0e0, #e0e0e0);
    z-index: 0;
    pointer-events: none;
  }

  .step-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    padding: 28px 22px 24px 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
    transition: transform .2s, box-shadow .2s;
    position: relative;
    z-index: 1;
  }

  .step-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(0,0,0,.09);
  }

  .step-number {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: .06em;
    color: #bbb;
    margin-bottom: 16px;
    display: block;
    text-transform: uppercase;
  }

  .step-icon {
    font-size: 38px;
    display: block;
    margin-bottom: 14px;
    line-height: 1;
  }

  .step-card h3 {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 800;
    line-height: 1.2;
  }

  .step-card p {
    margin: 0;
    color: #666;
    font-size: 14px;
    line-height: 1.65;
  }

  /* Owner variant — subtle green accent */
  .step-card.owner-step {
    border-color: #d4ede0;
    background: #fcfffe;
  }

  .step-card.owner-step .step-number {
    color: #4ade80;
  }

  /* ── Divider ─────────────────────────────────────── */
  .flow-divider {
    border: none;
    border-top: 1px solid #e8e8e8;
    margin: 0;
  }

  /* ── Final CTA ───────────────────────────────────── */
  .final-cta {
    padding: 56px 0;
  }

  .final-cta-inner {
    background: #f7f7f8;
    border: 1px solid #ececec;
    border-radius: 28px;
    padding: 48px;
    text-align: center;
  }

  .final-cta-inner h2 {
    margin: 0 0 12px 0;
    font-size: 36px;
    letter-spacing: -0.02em;
  }

  .final-cta-inner p {
    margin: 0 0 28px 0;
    color: #666;
    font-size: 16px;
    line-height: 1.6;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
  }

  .final-cta-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
  }

  /* ── Responsive ──────────────────────────────────── */
  @media (max-width: 1024px) {
    .steps-grid { grid-template-columns: repeat(2, 1fr); }
    .steps-grid::before { display: none; }
  }

  @media (max-width: 768px) {
    .cf-hero-inner { padding: 36px 24px; }
    .cf-hero-inner h1 { font-size: 34px; }
    .cf-hero-inner p { font-size: 16px; }
    .steps-grid { grid-template-columns: 1fr; }
    .final-cta-inner { padding: 36px 24px; }
    .final-cta-inner h2 { font-size: 26px; }
  }

  @media (max-width: 480px) {
    .cf-hero { padding: 16px 0 0 0; }
    .cf-hero-inner h1 { font-size: 28px; }
  }
@endpush

@section('content')

  {{-- ── Hero ─────────────────────────────────────────────────────── --}}
  <section class="cf-hero">
    <div class="container">
      <div class="cf-hero-inner">
        <h1>¿Cómo funciona TuCancha?</h1>
        <p>Reservar nunca fue tan fácil.</p>
      </div>
    </div>
  </section>

  {{-- ── Pasos para el usuario ────────────────────────────────────── --}}
  <section class="flow-section">
    <div class="container">
      <div class="section-head">
        <span class="section-label" style="background:#e8f7ee; color:#157347;">Para usuarios</span>
        <h2 class="section-title">Reservá en 4 pasos</h2>
        <p class="section-subtitle">
          Todo el proceso desde que entrás a TuCancha hasta que llegás a la cancha.
        </p>
      </div>

      <div class="steps-grid">
        <div class="step-card">
          <span class="step-number">Paso 01</span>
          <span class="step-icon">🔍</span>
          <h3>Buscá un complejo</h3>
          <p>Explorá los complejos disponibles en tu ciudad. Filtrá por deporte, fecha y horario para encontrar el que mejor te quede.</p>
        </div>

        <div class="step-card">
          <span class="step-number">Paso 02</span>
          <span class="step-icon">📅</span>
          <h3>Elegí cancha, día y horario</h3>
          <p>Revisá la disponibilidad en tiempo real. Ves qué turnos están libres, cuáles tienen descuento y el precio de cada uno.</p>
        </div>

        <div class="step-card">
          <span class="step-number">Paso 03</span>
          <span class="step-icon">💳</span>
          <h3>Pagá con Mercado Pago</h3>
          <p>Usá tarjeta de crédito, débito, transferencia o efectivo. El pago es 100% seguro y tu turno se confirma al instante.</p>
        </div>

        <div class="step-card">
          <span class="step-number">Paso 04</span>
          <span class="step-icon">✅</span>
          <h3>¡Listo! Recibís confirmación por mail</h3>
          <p>Te llega un mail con todos los datos de tu reserva y un código de verificación para presentar en el complejo el día del turno.</p>
        </div>
      </div>
    </div>
  </section>

  <hr class="flow-divider">

  {{-- ── Pasos para el dueño ─────────────────────────────────────── --}}
  <section class="flow-section">
    <div class="container">
      <div class="section-head">
        <span class="section-label" style="background:#f0fdf4; color:#166534;">Para dueños de complejos</span>
        <h2 class="section-title">Empezá a recibir reservas</h2>
        <p class="section-subtitle">
          Configurá tu complejo una vez y empezá a recibir turnos online las 24 horas, cobrado automáticamente.
        </p>
      </div>

      <div class="steps-grid">
        <div class="step-card owner-step">
          <span class="step-number">Paso 01</span>
          <span class="step-icon">👤</span>
          <h3>Creá tu cuenta</h3>
          <p>Registrate en TuCancha con tu mail. En menos de un minuto tenés acceso a tu panel de administración.</p>
        </div>

        <div class="step-card owner-step">
          <span class="step-number">Paso 02</span>
          <span class="step-icon">⭐</span>
          <h3>Suscribite al plan</h3>
          <p>Activá tu cuenta como administrador de complejo. Un único pago mensual para acceder a todas las funciones del sistema.</p>
        </div>

        <div class="step-card owner-step">
          <span class="step-number">Paso 03</span>
          <span class="step-icon">🏟️</span>
          <h3>Cargá tus canchas y horarios</h3>
          <p>Configurá cada cancha: deporte, formato, precio por turno, días y horarios de atención. También podés cargar fotos y aplicar descuentos.</p>
        </div>

        <div class="step-card owner-step">
          <span class="step-number">Paso 04</span>
          <span class="step-icon">🚀</span>
          <h3>Empezá a recibir reservas</h3>
          <p>Tu complejo queda publicado y cualquier usuario puede reservar online. Vos recibís el cobro directo en tu cuenta de Mercado Pago y una notificación por mail.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── CTA final ────────────────────────────────────────────────── --}}
  <section class="final-cta">
    <div class="container">
      <div class="final-cta-inner">
        <h2>¿Listo para empezar?</h2>
        <p>Tanto si querés reservar una cancha como si querés sumar tu complejo, estás a un clic de distancia.</p>

        <div class="final-cta-actions">
          <a href="{{ route('venues.index') }}" class="btn btn-primary" style="padding:14px 32px; font-size:15px; border-radius:14px;">
            Ver complejos
          </a>
          <a href="{{ route('planes') }}" class="btn" style="padding:14px 32px; font-size:15px; border-radius:14px;">
            Sumar mi complejo
          </a>
        </div>
      </div>
    </div>
  </section>

@endsection
