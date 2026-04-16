@extends('layouts.marketing')

@section('title', 'Cómo funciona — TuCancha')
@section('meta_description', 'Aprendé cómo reservar canchas en TuCancha. Elegí el deporte, seleccioná el horario disponible y pagá online. Simple, rápido y sin llamadas.')

@push('jsonld')
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Cómo funciona', 'item' => url('/como-funciona')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

@push('styles')
<style>
  /* ── Hero ────────────────────────────────────────── */
  .cf-hero {
    padding: 40px 0 0 0;
  }

  .cf-hero-inner {
    position: relative;
    border-radius: 28px;
    overflow: hidden;
    min-height: 420px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: #fff;
  }

  .cf-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/hero-cancha.webp');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    opacity: 0.35;
    z-index: 0;
  }

  @media (max-width: 768px) {
    .cf-hero-bg {
      background-attachment: scroll;
    }
  }

  .cf-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(10,30,18,0.82) 0%, rgba(15,47,26,0.88) 60%, rgba(10,20,14,0.92) 100%);
    z-index: 1;
  }

  .cf-hero-blob-1 {
    position: absolute;
    top: -60px;
    left: -80px;
    width: 340px;
    height: 340px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.12) 0%, transparent 70%);
    z-index: 2;
    pointer-events: none;
  }

  .cf-hero-blob-2 {
    position: absolute;
    bottom: -80px;
    right: -60px;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.1) 0%, transparent 70%);
    z-index: 2;
    pointer-events: none;
  }

  .cf-hero-content {
    position: relative;
    z-index: 3;
    padding: 72px 48px;
    max-width: 800px;
  }

  .cf-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    border-radius: 999px;
    background: rgba(74,222,128,0.15);
    border: 1px solid rgba(74,222,128,0.35);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #6eeaa0;
    margin-bottom: 28px;
  }

  .cf-hero-badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #6eeaa0;
    animation: cf-pulse 2s ease-in-out infinite;
  }

  @keyframes cf-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.7); }
  }

  .cf-hero-content h1 {
    margin: 0 0 20px 0;
    font-size: 68px;
    line-height: 1.02;
    letter-spacing: -0.04em;
  }

  .cf-hero-content h1 em {
    font-style: normal;
    color: #6eeaa0;
  }

  .cf-hero-content p {
    margin: 0 auto 32px auto;
    color: rgba(255,255,255,0.76);
    font-size: 19px;
    line-height: 1.65;
    max-width: 560px;
  }

  .cf-hero-pills {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
  }

  .cf-hero-pill {
    padding: 10px 20px;
    border-radius: 14px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.14);
    backdrop-filter: blur(8px);
    font-size: 13px;
    font-weight: 700;
    color: rgba(255,255,255,0.88);
    display: flex;
    align-items: center;
    gap: 8px;
  }

  @media (max-width: 768px) {
    .cf-hero-content { padding: 52px 24px; }
    .cf-hero-content h1 { font-size: 38px; }
    .cf-hero-content p { font-size: 16px; }
  }

  @media (max-width: 480px) {
    .cf-hero { padding: 16px 0 0 0; }
    .cf-hero-content h1 { font-size: 30px; }
  }

  /* ── Flow sections ───────────────────────────────── */
  .flow-section { padding: 64px 0; }

  .flow-section + .flow-section {
    padding-top: 0;
  }

  /* ── Section fondo jugadores ─────────────────────── */
  .cf-players-section {
    padding: 72px 0;
    background: #0a0a0a;
  }

  /* ── Steps grid ──────────────────────────────────── */
  .steps-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    position: relative;
  }

  .steps-grid::before {
    content: '';
    position: absolute;
    top: 48px;
    left: calc(25% + 8px);
    right: calc(25% + 8px);
    height: 2px;
    background: linear-gradient(to right, #22c55e33, #22c55e, #22c55e33);
    z-index: 0;
    pointer-events: none;
  }

  .step-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-left: 4px solid #22c55e;
    border-radius: 20px;
    padding: 28px 22px 24px 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.15);
    transition: transform .2s, box-shadow .2s;
    position: relative;
    z-index: 1;
    overflow: hidden;
  }

  .step-card-ghost-num {
    position: absolute;
    top: -10px;
    right: 12px;
    font-size: 80px;
    font-weight: 900;
    line-height: 1;
    color: rgba(34,197,94,0.07);
    pointer-events: none;
    user-select: none;
    letter-spacing: -0.04em;
  }

  .step-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 32px rgba(34,197,94,0.15);
  }

  .step-number {
    font-size: 13px;
    font-weight: 800;
    letter-spacing: .06em;
    color: #22c55e;
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
    color: #e8e8e8;
  }

  .step-card p {
    margin: 0;
    color: #a0a0a0;
    font-size: 14px;
    line-height: 1.65;
  }

  /* ── Divider ─────────────────────────────────────── */
  .flow-divider {
    border: none;
    border-top: 1px solid rgba(255,255,255,.06);
    margin: 0;
  }

  /* ── Historial section ────────────────────────────── */
  .cf-historial-section {
    padding: 72px 0;
    background: #050505;
  }

  /* ── Feature highlight cards ─────────────────────── */
  .feature-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }

  .feature-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    padding: 28px 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.15);
    transition: transform .2s, box-shadow .2s;
  }

  .feature-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.25);
  }

  .feature-card-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: rgba(34,197,94,.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 16px;
  }

  .feature-card-icon {
    font-size: 36px;
    margin-bottom: 14px;
    display: block;
  }

  .feature-card h3 {
    margin: 0 0 10px 0;
    font-size: 17px;
    font-weight: 800;
    color: #e8e8e8;
  }

  .feature-card p {
    margin: 0;
    color: #a0a0a0;
    font-size: 14px;
    line-height: 1.7;
  }

  .feature-card ul {
    margin: 12px 0 0 0;
    padding: 0;
    list-style: none;
    color: #a0a0a0;
    font-size: 14px;
    line-height: 1.9;
  }

  .feature-card ul li {
    display: flex;
    align-items: flex-start;
    gap: 8px;
  }

  .feature-card ul li::before {
    content: '✓';
    color: #22c55e;
    font-weight: 800;
    flex-shrink: 0;
    margin-top: 1px;
  }

  @media (max-width: 900px) {
    .feature-cards { grid-template-columns: 1fr; }
  }

  /* ── Sección dueños ──────────────────────────────── */
  .cf-owners-section {
    padding: 72px 0;
    background: #0f1712;
    position: relative;
    overflow: hidden;
  }

  .cf-owners-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/admin-viendo-telefono-y-complejo.webp');
    background-size: cover;
    background-position: center;
    opacity: 0.08;
    z-index: 0;
  }

  .cf-owners-section .container {
    position: relative;
    z-index: 1;
  }

  .cf-owners-section .section-head .section-label {
    background: rgba(74,222,128,0.15) !important;
    color: #6eeaa0 !important;
    border: 1px solid rgba(74,222,128,0.25);
  }

  .cf-owners-section .section-head .section-title {
    color: #fff;
  }

  .cf-owners-section .section-head .section-subtitle {
    color: rgba(255,255,255,0.62);
  }

  .owners-steps-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    position: relative;
  }

  .owner-step-card {
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-left: 4px solid #22c55e;
    border-radius: 20px;
    padding: 28px 22px 24px 22px;
    backdrop-filter: blur(6px);
    transition: transform .2s, box-shadow .2s, background .2s;
    position: relative;
    overflow: hidden;
  }

  .owner-step-card-ghost-num {
    position: absolute;
    top: -10px;
    right: 12px;
    font-size: 80px;
    font-weight: 900;
    line-height: 1;
    color: rgba(110,234,160,0.08);
    pointer-events: none;
    user-select: none;
    letter-spacing: -0.04em;
  }

  .owner-step-card:hover {
    transform: translateY(-4px);
    background: rgba(255,255,255,0.1);
    box-shadow: 0 10px 32px rgba(34,197,94,0.2);
  }

  .owner-step-card .step-number {
    color: #6eeaa0;
  }

  .owner-step-card h3 {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 800;
    color: #fff;
    line-height: 1.2;
  }

  .owner-step-card p {
    margin: 0;
    color: rgba(255,255,255,0.62);
    font-size: 14px;
    line-height: 1.65;
  }

  /* ── CTA final ───────────────────────────────────── */
  .final-cta {
    padding: 72px 0;
    background: #111;
  }

  .final-cta-inner {
    background: transparent;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 28px;
    padding: 56px 48px;
    text-align: center;
  }

  .final-cta-inner h2 {
    margin: 0 0 14px 0;
    font-size: 40px;
    letter-spacing: -0.03em;
    color: #fff;
  }

  .final-cta-inner p {
    margin: 0 0 32px 0;
    color: rgba(255,255,255,0.62);
    font-size: 16px;
    line-height: 1.65;
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

  .btn-cf-primary {
    padding: 14px 32px;
    background: #22c55e;
    color: #052e14;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    transition: background .15s, transform .15s;
    display: inline-block;
    border: none;
  }

  .btn-cf-primary:hover {
    background: #16a34a;
    transform: translateY(-2px);
    color: #fff;
  }

  .btn-cf-ghost {
    padding: 14px 24px;
    background: rgba(255,255,255,0.07);
    color: rgba(255,255,255,0.88);
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.2);
    transition: background .15s, transform .15s;
    display: inline-block;
  }

  .btn-cf-ghost:hover {
    background: rgba(255,255,255,0.14);
    transform: translateY(-2px);
  }

  /* ── Responsive ──────────────────────────────────── */
  @media (max-width: 1024px) {
    .steps-grid,
    .owners-steps-grid { grid-template-columns: repeat(2, 1fr); }
    .steps-grid::before { display: none; }
  }

  @media (max-width: 768px) {
    .steps-grid,
    .owners-steps-grid { grid-template-columns: 1fr; }
    .final-cta-inner { padding: 36px 24px; }
    .final-cta-inner h2 { font-size: 28px; }
  }
</style>
@endpush

@section('content')

  {{-- ── Hero ─────────────────────────────────────────────────────── --}}
  <section class="cf-hero">
    <div class="container">
      <div class="cf-hero-inner">
        <div class="cf-hero-bg"></div>
        <div class="cf-hero-overlay"></div>
        <div class="cf-hero-blob-1"></div>
        <div class="cf-hero-blob-2"></div>
        <div class="cf-hero-content">
          <div class="cf-hero-badge" data-aos="fade-down">
            <span class="cf-hero-badge-dot"></span>
            Guía paso a paso
          </div>
          <h1 data-aos="fade-up" data-aos-delay="80">
            ¿Cómo funciona<br><em>TuCancha?</em>
          </h1>
          <p data-aos="fade-up" data-aos-delay="160">
            Reservar nunca fue tan fácil. Desde que entrás hasta que pisás la cancha, todo en unos pocos clics.
          </p>
          <div class="cf-hero-pills" data-aos="fade-up" data-aos-delay="240">
            <div class="cf-hero-pill"><i data-lucide="zap" style="width:14px;height:14px;stroke:#6eeaa0;"></i> Sin llamadas</div>
            <div class="cf-hero-pill"><i data-lucide="credit-card" style="width:14px;height:14px;stroke:#6eeaa0;"></i> Pago seguro</div>
            <div class="cf-hero-pill"><i data-lucide="smartphone" style="width:14px;height:14px;stroke:#6eeaa0;"></i> Confirmación al instante</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Pasos para el usuario ────────────────────────────────────── --}}
  <section class="cf-players-section">
    <div class="container">
      <div class="section-head" data-aos="fade-up">
        <span class="section-label" style="background:rgba(34,197,94,.1); color:#6eeaa0;">Para usuarios</span>
        <h2 class="section-title">Reservá en 4 pasos</h2>
        <p class="section-subtitle">
          Todo el proceso desde que entrás a TuCancha hasta que llegás a la cancha.
        </p>
      </div>

      <div class="steps-grid">
        <div class="step-card" data-aos="fade-up" data-aos-delay="0">
          <span class="step-card-ghost-num">01</span>
          <span class="step-number">Paso 01</span>
          <span class="step-icon"><i data-lucide="search" style="width:36px;height:36px;stroke:#22c55e;stroke-width:1.5;"></i></span>
          <h3>Buscá un complejo</h3>
          <p>Explorá los complejos disponibles en tu ciudad. Filtrá por deporte, fecha y horario para encontrar el que mejor te quede.</p>
        </div>

        <div class="step-card" data-aos="fade-up" data-aos-delay="100">
          <span class="step-card-ghost-num">02</span>
          <span class="step-number">Paso 02</span>
          <span class="step-icon"><i data-lucide="calendar" style="width:36px;height:36px;stroke:#22c55e;stroke-width:1.5;"></i></span>
          <h3>Elegí cancha, día y horario</h3>
          <p>Revisá la disponibilidad en tiempo real. Ves qué turnos están libres, cuáles tienen descuento y el precio de cada uno.</p>
        </div>

        <div class="step-card" data-aos="fade-up" data-aos-delay="200">
          <span class="step-card-ghost-num">03</span>
          <span class="step-number">Paso 03</span>
          <span class="step-icon"><i data-lucide="credit-card" style="width:36px;height:36px;stroke:#22c55e;stroke-width:1.5;"></i></span>
          <h3>Pagá de forma segura</h3>
          <p>Elegí el método que prefieras: tarjeta de crédito, débito, transferencia o efectivo. El pago se procesa a través de Mercado Pago y tu turno se confirma al instante.</p>
        </div>

        <div class="step-card" data-aos="fade-up" data-aos-delay="300">
          <span class="step-card-ghost-num">04</span>
          <span class="step-number">Paso 04</span>
          <span class="step-icon"><i data-lucide="check-circle" style="width:36px;height:36px;stroke:#22c55e;stroke-width:1.5;"></i></span>
          <h3>¡Listo! Recibís confirmación por mail</h3>
          <p>Te llega un mail con todos los datos de tu reserva: complejo, cancha, día y horario. Presentate el día del turno y a jugar.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Funciones extra para usuarios ────────────────────────────── --}}
  <section class="cf-historial-section">
    <div class="container">
      <div class="section-head" data-aos="fade-up">
        <span class="section-label" style="background:rgba(34,197,94,.1); color:#6eeaa0;">Más que reservas</span>
        <h2 class="section-title">Tu historial deportivo</h2>
        <p class="section-subtitle">
          TuCancha no es solo para reservar. También te ayuda a llevar el registro de tu actividad deportiva y compartirla con tus compañeros.
        </p>
      </div>

      <div class="feature-cards">
        <div class="feature-card" data-aos="fade-left">
          <div class="feature-card-icon-wrap"><i data-lucide="bar-chart-2" style="width:26px;height:26px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>Historial de partidos</h3>
          <p>Después de cada partido pagado o validado en el complejo, el encuentro queda registrado en tu historial. Ahí podés ver:</p>
          <ul>
            <li>Total de partidos jugados</li>
            <li>Tu deporte y complejo favorito</li>
            <li>Total gastado en reservas</li>
            <li>Gráfico de resultados (ganados / empatados / perdidos)</li>
            <li>Estadísticas desglosadas por deporte</li>
          </ul>
        </div>

        <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
          <div class="feature-card-icon-wrap"><i data-lucide="users" style="width:26px;height:26px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>Etiquetá a tus compañeros</h3>
          <p>
            ¿Fuiste con amigos? Desde el detalle de tu reserva podés agregarlos buscándolos por nombre
            o mail. Ellos ven el partido en su propio historial aunque no hayan reservado.
          </p>
          <p style="margin-top:10px; color:#a0a0a0; font-size:14px; line-height:1.7;">
            Ideal para cuando uno del grupo reserva por todos: nadie pierde el registro del partido.
          </p>
        </div>

        <div class="feature-card" data-aos="fade-right">
          <div class="feature-card-icon-wrap"><i data-lucide="trophy" style="width:26px;height:26px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>Resultados independientes</h3>
          <p>
            Cada jugador carga su propio resultado: si ganaron, empataron o perdieron, y el marcador si quieren.
            Como cada uno puede haber estado en un equipo diferente, los resultados son completamente independientes.
          </p>
          <p style="margin-top:10px; color:#a0a0a0; font-size:14px; line-height:1.7;">
            El gráfico de torta en tu historial refleja solo tus propios resultados.
          </p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Pasos para el dueño ─────────────────────────────────────── --}}
  <section class="cf-owners-section">
    <div class="cf-owners-bg"></div>
    <div class="container">
      <div class="section-head" data-aos="fade-up">
        <span class="section-label">Para dueños de complejos</span>
        <h2 class="section-title">Empezá a recibir reservas</h2>
        <p class="section-subtitle">
          Configurá tu complejo una vez y empezá a recibir turnos online las 24 horas, cobrado automáticamente.
        </p>
      </div>

      <div class="owners-steps-grid">
        <div class="owner-step-card" data-aos="fade-up" data-aos-delay="0">
          <span class="owner-step-card-ghost-num">01</span>
          <span class="step-number">Paso 01</span>
          <span class="step-icon"><i data-lucide="user" style="width:36px;height:36px;stroke:#6eeaa0;stroke-width:1.5;"></i></span>
          <h3>Creá tu cuenta</h3>
          <p>Registrate en TuCancha con tu mail. En menos de un minuto tenés acceso a tu panel de administración.</p>
        </div>

        <div class="owner-step-card" data-aos="fade-up" data-aos-delay="100">
          <span class="owner-step-card-ghost-num">02</span>
          <span class="step-number">Paso 02</span>
          <span class="step-icon"><i data-lucide="star" style="width:36px;height:36px;stroke:#6eeaa0;stroke-width:1.5;"></i></span>
          <h3>Elegí tu plan</h3>
          <p>Ahora los primeros complejos arrancan con 3 meses gratis. Después elegís el plan que mejor te quede.</p>
        </div>

        <div class="owner-step-card" data-aos="fade-up" data-aos-delay="200">
          <span class="owner-step-card-ghost-num">03</span>
          <span class="step-number">Paso 03</span>
          <span class="step-icon"><i data-lucide="building-2" style="width:36px;height:36px;stroke:#6eeaa0;stroke-width:1.5;"></i></span>
          <h3>Cargá tus canchas y horarios</h3>
          <p>Configurá cada cancha: deporte, formato, precio por turno, días y horarios de atención. También podés cargar fotos y aplicar descuentos.</p>
        </div>

        <div class="owner-step-card" data-aos="fade-up" data-aos-delay="300">
          <span class="owner-step-card-ghost-num">04</span>
          <span class="step-number">Paso 04</span>
          <span class="step-icon"><i data-lucide="trending-up" style="width:36px;height:36px;stroke:#6eeaa0;stroke-width:1.5;"></i></span>
          <h3>Empezá a recibir reservas</h3>
          <p>Tu complejo queda publicado y cualquier usuario puede reservar online. Vos recibís el cobro directo en tu cuenta de Mercado Pago y una notificación por mail.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── CTA final ────────────────────────────────────────────────── --}}
  <section class="final-cta">
    <div class="container">
      <div class="final-cta-inner" data-aos="fade-up">
        <h2>¿Listo para empezar?</h2>
        <p>Tanto si querés reservar una cancha como si querés sumar tu complejo, estás a un clic de distancia.</p>

        <div class="final-cta-actions">
          <a href="https://wa.me/5491127279757?text=Hola%2C%20quiero%20sumar%20mi%20complejo%20a%20TuCancha" target="_blank" rel="noopener" class="btn-cf-primary">
            Hablanos por WhatsApp
          </a>
          <a href="{{ route('venues.index') }}" class="btn-cf-ghost">
            Ver complejos
          </a>
        </div>
      </div>
    </div>
  </section>

@endsection
