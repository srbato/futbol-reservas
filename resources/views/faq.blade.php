@extends('layouts.marketing')

@section('title', 'Preguntas frecuentes — TuCancha')
@section('meta_description', 'Respuestas a las preguntas mas frecuentes sobre TuCancha: como reservar, cuanto cuesta, que es Falta Uno, como cancelar y mas.')

@push('jsonld')
<script type="application/ld+json">
@php
$faqs = [
    ['q' => 'Como reservo una cancha en TuCancha?', 'a' => 'Busca complejos en la seccion Complejos, elegi una cancha y horario disponible, y paga online con MercadoPago. En menos de un minuto tenes tu reserva confirmada.'],
    ['q' => 'Cuanto cuesta usar TuCancha?', 'a' => 'Para jugadores es gratis. Para duenos de complejos hay planes de suscripcion mensual con precio fijo. No cobramos comisiones sobre las reservas.'],
    ['q' => 'Que es Falta Uno?', 'a' => 'Falta Uno es una funcion que te permite publicar un partido que necesita jugadores. Otros jugadores de tu zona pueden sumarse al partido directamente desde la plataforma.'],
    ['q' => 'Como cancelo una reserva?', 'a' => 'Podes cancelar desde la seccion Mis Reservas. El plazo de cancelacion depende de la politica de cada complejo y se muestra al momento de reservar.'],
    ['q' => 'Que deportes estan disponibles?', 'a' => 'Futbol (5, 7 y 11), padel, tenis, basquet y voley. Depende de las canchas disponibles en cada complejo.'],
    ['q' => 'Como funciona el pago?', 'a' => 'Todos los pagos se procesan a traves de MercadoPago. Podes pagar con tarjeta de credito, debito, o dinero en cuenta de MercadoPago.'],
    ['q' => 'Que es el perfil deportivo?', 'a' => 'Es tu perfil como jugador donde se registran tus partidos jugados, victorias, rating y categoria. Te ayuda a encontrar partidos de tu nivel.'],
    ['q' => 'Soy dueno de un complejo. Como me registro?', 'a' => 'Registrate en TuCancha, activa el plan de complejo desde la seccion Planes, y carga tus canchas con horarios y precios. Tenes {{ $trialDays }} dias de prueba gratis.'],
    ['q' => 'TuCancha cobra comision sobre mis ingresos?', 'a' => 'No. TuCancha cobra una suscripcion mensual fija. El 100% de lo que pagan los jugadores va directo a tu cuenta de MercadoPago.'],
    ['q' => 'Puedo reservar una cancha sin crear cuenta?', 'a' => 'No, necesitas crear una cuenta gratuita para reservar. El registro toma menos de 30 segundos y podes hacerlo con Google.'],
];

echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(fn($f) => [
        '@type' => 'Question',
        'name' => $f['q'],
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => $f['a'],
        ],
    ], $faqs),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
@endphp
</script>
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Preguntas frecuentes', 'item' => url('/preguntas-frecuentes')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

@push('styles')
<style>
  /* Easings from design-tokens.css */

  /* ── Hero ────────────────────────────────────────── */
  .faq-hero {
    padding: 48px 0 0;
  }

  .faq-hero-shell {
    background: rgba(0,0,0,0.03);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(0,0,0,0.04);
  }

  .faq-hero-inner {
    background: #111;
    border-radius: calc(2rem - 5px);
    padding: 64px 48px;
    position: relative;
    overflow: hidden;
    text-align: center;
  }

  .faq-hero-orb-1 {
    position: absolute;
    top: -80px;
    left: -60px;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.10) 0%, transparent 60%);
    pointer-events: none;
  }

  .faq-hero-orb-2 {
    position: absolute;
    bottom: -60px;
    right: -40px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.06) 0%, transparent 60%);
    pointer-events: none;
  }

  .faq-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 16px;
    border-radius: 999px;
    background: rgba(74,222,128,0.10);
    border: 1px solid rgba(74,222,128,0.22);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #6eeaa0;
    margin-bottom: 18px;
    position: relative;
  }

  .faq-hero-inner h1 {
    margin: 0 0 12px;
    font-size: clamp(32px, 4.5vw, 48px);
    font-weight: 900;
    letter-spacing: -0.04em;
    color: #fff;
    line-height: 1;
    position: relative;
    text-wrap: balance;
  }

  .faq-hero-inner > p {
    margin: 0;
    font-size: 16px;
    color: rgba(255,255,255,0.45);
    line-height: 1.6;
    position: relative;
  }

  @media (max-width: 768px) {
    .faq-hero-inner { padding: 48px 24px; }
  }

  @media (max-width: 480px) {
    .faq-hero { padding: 24px 0 0; }
    .faq-hero-shell { border-radius: 1.25rem; }
    .faq-hero-inner { border-radius: calc(1.25rem - 5px); padding: 40px 20px; }
  }

  /* ── FAQ Section ────────────────────────────────── */
  .faq-section {
    padding: 48px 0 100px;
    max-width: 760px;
    margin: 0 auto;
  }

  .faq-category-label {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 18px;
    margin-top: 40px;
  }

  .faq-category-label:first-child {
    margin-top: 0;
  }

  .faq-category-text {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--color-text-muted, #999);
    flex-shrink: 0;
  }

  .faq-category-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, var(--color-border, #ececec), transparent);
  }

  /* Accordion item (double-bezel) */
  .faq-item-shell {
    background: rgba(0,0,0,0.02);
    border-radius: 1.25rem;
    padding: 3px;
    border: 1px solid rgba(0,0,0,0.03);
    margin-bottom: 10px;
    transition: box-shadow 400ms var(--ease-out-expo);
  }

  .faq-item-shell.open {
    box-shadow: 0 8px 24px rgba(0,0,0,0.04);
  }

  .faq-item {
    background: #fff;
    border-radius: calc(1.25rem - 3px);
    overflow: hidden;
  }

  .faq-question {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 20px 24px;
    cursor: pointer;
    user-select: none;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    font-family: inherit;
    transition: background 200ms var(--ease-out-expo);
  }

  .faq-question:hover {
    background: var(--color-bg-hover, #f4f4f4);
  }

  .faq-question:active {
    transform: scale(0.995);
  }

  .faq-question-text {
    font-size: 16px;
    font-weight: 800;
    color: var(--color-text, #111);
    letter-spacing: -0.01em;
    line-height: 1.35;
  }

  .faq-item-shell.open .faq-question-text {
    color: #22c55e;
  }

  .faq-chevron {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    color: var(--color-text-muted, #999);
    transition: transform 400ms var(--ease-out-expo), color 300ms var(--ease-out-expo);
  }

  .faq-item-shell.open .faq-chevron {
    transform: rotate(180deg);
    color: #22c55e;
  }

  .faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 500ms var(--ease-out-expo), padding 400ms var(--ease-out-expo);
    padding: 0 24px;
  }

  .faq-answer.open {
    max-height: 300px;
    padding: 0 24px 22px;
  }

  .faq-answer p {
    font-size: 15px;
    line-height: 1.7;
    color: #5a5a5a;
    margin: 0;
  }

  .faq-answer a {
    color: #22c55e;
    font-weight: 700;
    text-decoration: none;
  }

  .faq-answer a:hover {
    text-decoration: underline;
  }

  /* ── CTA Bottom ─────────────────────────────────── */
  .faq-cta-shell {
    background: rgba(15,76,42,0.05);
    border-radius: 1.5rem;
    padding: 5px;
    border: 1px solid rgba(34,197,94,0.08);
    margin-top: 48px;
  }

  .faq-cta {
    background: linear-gradient(145deg, #0a3d21 0%, #0f5c32 40%, #147a42 100%);
    border-radius: calc(1.5rem - 5px);
    padding: 48px 36px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .faq-cta::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -40px;
    width: 240px;
    height: 240px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.12) 0%, transparent 60%);
    pointer-events: none;
  }

  .faq-cta h2 {
    margin: 0 0 10px;
    font-size: 24px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.03em;
    position: relative;
  }

  .faq-cta p {
    margin: 0 auto 24px;
    max-width: 400px;
    font-size: 14px;
    color: rgba(255,255,255,0.55);
    line-height: 1.6;
    position: relative;
  }

  .faq-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 28px;
    background: #22c55e;
    color: #052e14;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    transition: background 300ms var(--ease-out-expo), color 300ms var(--ease-out-expo), transform 300ms var(--ease-out-expo);
    box-shadow: 0 4px 16px rgba(34,197,94,0.25);
    position: relative;
  }

  .faq-cta-btn:hover {
    background: #16a34a;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(34,197,94,0.35);
  }

  .faq-cta-btn:active {
    transform: translateY(0) scale(0.97);
  }

  .faq-cta-btn svg {
    transition: transform 250ms var(--ease-out-expo);
  }

  .faq-cta-btn:hover svg {
    transform: translateX(3px);
  }

  /* ── Focus + Reduced Motion ─────────────────────── */
  .faq-question:focus-visible {
    outline: 2px solid #22c55e;
    outline-offset: -2px;
    border-radius: calc(1.25rem - 3px);
  }

  .faq-cta-btn:focus-visible {
    outline: 2px solid #22c55e;
    outline-offset: 3px;
  }

  @media (prefers-reduced-motion: reduce) {
    .faq-chevron,
    .faq-answer,
    .faq-item-shell,
    .faq-cta-btn {
      transition-duration: 0ms !important;
    }
  }
</style>
@endpush

@section('content')

  {{-- Hero --}}
  <section class="faq-hero">
    <div class="container">
      <div class="faq-hero-shell">
        <div class="faq-hero-inner">
          <div class="faq-hero-orb-1"></div>
          <div class="faq-hero-orb-2"></div>
          <div class="faq-hero-badge" data-aos="fade-down" data-aos-duration="600">Ayuda</div>
          <h1 data-aos="fade-up" data-aos-delay="80" data-aos-duration="700">Preguntas frecuentes</h1>
          <p data-aos="fade-up" data-aos-delay="160" data-aos-duration="700">Todo lo que necesitas saber sobre TuCancha.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- FAQ --}}
  <div class="container">
    <section class="faq-section">

      {{-- Jugadores --}}
      <div class="faq-category-label" data-aos="fade-up" data-aos-duration="500">
        <span class="faq-category-text">Para jugadores</span>
        <div class="faq-category-line"></div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Como reservo una cancha en TuCancha?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>Busca complejos en la seccion <a href="/venues">Complejos</a>, elegi una cancha y horario disponible, y paga online con MercadoPago. En menos de un minuto tenes tu reserva confirmada.</p>
          </div>
        </div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="40" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Que es Falta Uno?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>Falta Uno es una funcion que te permite publicar un partido que necesita jugadores. Otros jugadores de tu zona pueden sumarse directamente desde la plataforma. Sin mandar mensajes, sin coordinar.</p>
          </div>
        </div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="80" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Como cancelo una reserva?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>Podes cancelar desde la seccion Mis Reservas. El plazo de cancelacion depende de la politica de cada complejo y se muestra al momento de reservar.</p>
          </div>
        </div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="120" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Que deportes estan disponibles?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>Futbol (5, 7 y 11), padel, tenis, basquet y voley. Depende de las canchas disponibles en cada complejo.</p>
          </div>
        </div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="160" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Como funciona el pago?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>Todos los pagos se procesan a traves de MercadoPago. Podes pagar con tarjeta de credito, debito, o dinero en cuenta de MercadoPago. El pago es instantaneo y seguro.</p>
          </div>
        </div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="200" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Que es el perfil deportivo?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>Es tu perfil como jugador donde se registran tus partidos jugados, victorias, rating y categoria. Te ayuda a encontrar partidos de tu nivel y construir tu reputacion deportiva.</p>
          </div>
        </div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="240" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Puedo reservar sin crear cuenta?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>No, necesitas una cuenta gratuita para reservar. El registro toma menos de 30 segundos y podes hacerlo con Google.</p>
          </div>
        </div>
      </div>

      {{-- Complejos --}}
      <div class="faq-category-label" data-aos="fade-up" data-aos-duration="500">
        <span class="faq-category-text">Para complejos</span>
        <div class="faq-category-line"></div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="0" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Cuanto cuesta usar TuCancha para mi complejo?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>Hay planes de suscripcion mensual con precio fijo. No cobramos comisiones sobre tus reservas: el 100% va directo a tu MercadoPago. Consulta los <a href="/planes">planes disponibles</a>.</p>
          </div>
        </div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="40" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">Como registro mi complejo?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>Registrate en TuCancha, activa el plan de complejo desde la seccion <a href="/planes">Planes</a>, conecta tu MercadoPago y carga tus canchas con horarios y precios. Tenes {{ $trialDays }} dias de prueba gratis.</p>
          </div>
        </div>
      </div>

      <div class="faq-item-shell" data-aos="fade-up" data-aos-delay="80" data-aos-duration="500">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFaq(this)">
            <span class="faq-question-text">TuCancha cobra comision sobre mis ingresos?</span>
            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
          </button>
          <div class="faq-answer">
            <p>No. TuCancha cobra una suscripcion mensual fija. Tu plata es tu plata: el total de cada reserva va directo a tu cuenta de MercadoPago, sin retenciones ni comisiones.</p>
          </div>
        </div>
      </div>

      {{-- CTA --}}
      <div class="faq-cta-shell" data-aos="fade-up" data-aos-duration="600">
        <div class="faq-cta">
          <h2>No encontraste lo que buscabas?</h2>
          <p>Escribinos directo y te respondemos a la brevedad.</p>
          <a href="#feedback-section" class="faq-cta-btn" onclick="document.getElementById('feedback-section').scrollIntoView({behavior:'smooth',block:'center'})">
            Contactanos
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
          </a>
        </div>
      </div>

    </section>
  </div>

<script>
function toggleFaq(btn) {
  const shell = btn.closest('.faq-item-shell');
  const answer = shell.querySelector('.faq-answer');
  const isOpen = shell.classList.contains('open');

  // Close all others
  document.querySelectorAll('.faq-item-shell.open').forEach(s => {
    if (s !== shell) {
      s.classList.remove('open');
      s.querySelector('.faq-answer').classList.remove('open');
    }
  });

  // Toggle this one
  shell.classList.toggle('open', !isOpen);
  answer.classList.toggle('open', !isOpen);
}
</script>

@endsection
