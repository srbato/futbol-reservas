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

  /* ── Feature highlight cards ─────────────────────── */
  .feature-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }

  .feature-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    padding: 28px 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
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
  }

  .feature-card p {
    margin: 0;
    color: #666;
    font-size: 14px;
    line-height: 1.65;
  }

  .feature-card ul {
    margin: 10px 0 0 0;
    padding-left: 18px;
    color: #666;
    font-size: 14px;
    line-height: 1.8;
  }

  @media (max-width: 900px) {
    .feature-cards { grid-template-columns: 1fr; }
  }

  /* ── Referral highlight ──────────────────────────── */
  .cf-referral {
    padding: 56px 0 0 0;
  }

  .cf-referral-inner {
    background: linear-gradient(135deg, #0f4c2a 0%, #1a7a45 100%);
    border-radius: 28px;
    padding: 52px 48px;
    color: #fff;
    position: relative;
    overflow: hidden;
  }

  .cf-referral-inner::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 320px; height: 320px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
    pointer-events: none;
  }

  .cf-referral-inner::after {
    content: '';
    position: absolute;
    bottom: -60px; left: 40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
    pointer-events: none;
  }

  .cf-referral-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    border-radius: 999px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.22);
    font-size: 13px;
    font-weight: 800;
    color: #a3f0c0;
    margin-bottom: 20px;
    letter-spacing: .04em;
    text-transform: uppercase;
  }

  .cf-referral-inner h2 {
    margin: 0 0 16px 0;
    font-size: 52px;
    line-height: 1.02;
    letter-spacing: -0.03em;
    max-width: 700px;
  }

  .cf-referral-inner h2 em {
    font-style: normal;
    color: #6eeaa0;
  }

  .cf-referral-inner > p {
    margin: 0 0 36px 0;
    color: rgba(255,255,255,.82);
    font-size: 18px;
    line-height: 1.65;
    max-width: 620px;
  }

  .cf-referral-steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    margin-bottom: 36px;
    position: relative;
    z-index: 1;
  }

  .cf-referral-step {
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.14);
    border-radius: 18px;
    padding: 24px 20px;
    backdrop-filter: blur(4px);
  }

  .cf-referral-step-num {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #6eeaa0;
    margin-bottom: 10px;
    display: block;
  }

  .cf-referral-step-icon {
    font-size: 30px;
    display: block;
    margin-bottom: 10px;
    line-height: 1;
  }

  .cf-referral-step h3 {
    margin: 0 0 8px 0;
    font-size: 16px;
    font-weight: 800;
    color: #fff;
  }

  .cf-referral-step p {
    margin: 0;
    font-size: 13px;
    color: rgba(255,255,255,.72);
    line-height: 1.6;
  }

  .cf-referral-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    position: relative;
    z-index: 1;
  }

  .btn-cf-referral {
    padding: 14px 32px;
    background: #6eeaa0;
    color: #0a3d1f;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    transition: background .15s, transform .15s;
    display: inline-block;
  }

  .btn-cf-referral:hover {
    background: #4dd882;
    transform: translateY(-2px);
  }

  .btn-cf-ghost {
    padding: 14px 24px;
    background: rgba(255,255,255,.1);
    color: rgba(255,255,255,.9);
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,.2);
    transition: background .15s, transform .15s;
    display: inline-block;
  }

  .btn-cf-ghost:hover {
    background: rgba(255,255,255,.18);
    transform: translateY(-2px);
  }

  @media (max-width: 900px) {
    .cf-referral-steps { grid-template-columns: 1fr; }
  }

  @media (max-width: 768px) {
    .cf-referral-inner { padding: 36px 24px; }
    .cf-referral-inner h2 { font-size: 34px; }
    .cf-referral-inner > p { font-size: 16px; }
  }

  @media (max-width: 480px) {
    .cf-referral-inner h2 { font-size: 28px; }
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

  {{-- ── Funciones extra para usuarios ────────────────────────────── --}}
  <section class="flow-section">
    <div class="container">
      <div class="section-head">
        <span class="section-label" style="background:#e8f0ff; color:#1a4a9a;">Más que reservas</span>
        <h2 class="section-title">Tu historial deportivo</h2>
        <p class="section-subtitle">
          TuCancha no es solo para reservar. También te ayuda a llevar el registro de tu actividad deportiva y compartirla con tus compañeros.
        </p>
      </div>

      <div class="feature-cards">
        <div class="feature-card">
          <span class="feature-card-icon">📊</span>
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

        <div class="feature-card">
          <span class="feature-card-icon">👥</span>
          <h3>Etiquetá a tus compañeros</h3>
          <p>
            ¿Fuiste con amigos? Desde el detalle de tu reserva podés agregarlos buscándolos por nombre
            o mail. Ellos ven el partido en su propio historial aunque no hayan reservado.
          </p>
          <p style="margin-top:10px;">
            Ideal para cuando uno del grupo reserva por todos: nadie pierde el registro del partido.
          </p>
        </div>

        <div class="feature-card">
          <span class="feature-card-icon">🏆</span>
          <h3>Resultados independientes</h3>
          <p>
            Cada jugador carga su propio resultado: si ganaron, empataron o perdieron, y el marcador si quieren.
            Como cada uno puede haber estado en un equipo diferente, los resultados son completamente independientes.
          </p>
          <p style="margin-top:10px;">
            El gráfico de torta en tu historial refleja solo tus propios resultados.
          </p>
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

  {{-- ── Referral — el beneficio más importante ──────────────────── --}}
  <section class="cf-referral">
    <div class="container">
      <div class="cf-referral-inner">
        <span class="cf-referral-tag">⭐ El beneficio más importante</span>
        <h2>Invitá un complejo.<br><em>Jugá gratis.</em></h2>
        <p>
          Cada usuario de TuCancha tiene un <strong style="color:#a3f0c0;">código de referido único</strong>.
          Si compartís ese código con el dueño de un complejo y se suma a la plataforma usándolo,
          vos recibís una reserva 100% gratuita para usar cuando quieras. Sin vencimiento.
        </p>

        <div class="cf-referral-steps">
          <div class="cf-referral-step">
            <span class="cf-referral-step-num">Paso 01</span>
            <span class="cf-referral-step-icon">🔗</span>
            <h3>Obtené tu código</h3>
            <p>
              Iniciá sesión y andá a la sección "Programa de referidos" en tu perfil.
              Ahí encontrás tu código único y el link directo para compartir.
            </p>
          </div>

          <div class="cf-referral-step">
            <span class="cf-referral-step-num">Paso 02</span>
            <span class="cf-referral-step-icon">🏟️</span>
            <h3>El complejo se registra con tu código</h3>
            <p>
              El dueño del complejo crea su cuenta en TuCancha e ingresa tu código al contratar su plan de administrador.
              ¡Así de simple!
            </p>
          </div>

          <div class="cf-referral-step">
            <span class="cf-referral-step-num">Paso 03</span>
            <span class="cf-referral-step-icon">🎉</span>
            <h3>Recibís tu recompensa</h3>
            <p>
              Automáticamente se acredita en tu cuenta una recompensa para pagar tu próxima reserva.
              La recompensa se aplica al momento de confirmar el pago.
            </p>
          </div>
        </div>

        <div class="cf-referral-actions">
          @auth
            <a href="{{ route('referral.index') }}" class="btn-cf-referral">
              Ver mi código de referido →
            </a>
          @else
            <a href="{{ route('register') }}" class="btn-cf-referral">
              Crear cuenta y empezar →
            </a>
          @endauth
          <a href="{{ route('venues.index') }}" class="btn-cf-ghost">
            Explorar complejos
          </a>
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
