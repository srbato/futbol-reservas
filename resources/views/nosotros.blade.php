@extends('layouts.marketing')

@section('title', 'Quiénes somos — TuCancha')
@section('meta_description', 'Conocé el equipo detrás de TuCancha, la plataforma argentina de reservas deportivas online. Nuestra misión es conectar jugadores con los mejores complejos.')

@push('styles')
  /* ── Hero ────────────────────────────────────────── */
  .ns-hero {
    padding: 40px 0 0 0;
  }

  .ns-hero-inner {
    position: relative;
    border-radius: 28px;
    overflow: hidden;
    min-height: 480px;
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
    opacity: 0.30;
    z-index: 0;
  }

  @media (max-width: 768px) {
    .ns-hero-bg {
      background-attachment: scroll;
    }
  }

  .ns-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(8,24,14,0.86) 0%, rgba(15,47,26,0.88) 55%, rgba(10,20,16,0.93) 100%);
    z-index: 1;
  }

  .ns-hero-blob-1 {
    position: absolute;
    top: -80px;
    left: -100px;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.10) 0%, transparent 70%);
    z-index: 2;
    pointer-events: none;
  }

  .ns-hero-blob-2 {
    position: absolute;
    bottom: -100px;
    right: -80px;
    width: 360px;
    height: 360px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.09) 0%, transparent 70%);
    z-index: 2;
    pointer-events: none;
  }

  .ns-hero-content {
    position: relative;
    z-index: 3;
    padding: 80px 48px;
    max-width: 860px;
  }

  .ns-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 18px;
    border-radius: 999px;
    background: rgba(74,222,128,0.13);
    border: 1px solid rgba(74,222,128,0.32);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #6eeaa0;
    margin-bottom: 28px;
  }

  .ns-hero-content h1 {
    margin: 0 0 24px 0;
    font-size: 76px;
    line-height: 1.0;
    letter-spacing: -0.04em;
  }

  .ns-hero-content h1 em {
    font-style: normal;
    color: #4ade80;
  }

  .ns-hero-content p {
    margin: 0 auto 40px auto;
    max-width: 580px;
    color: rgba(255,255,255,0.72);
    font-size: 19px;
    line-height: 1.65;
  }

  .ns-hero-micro-stats {
    display: flex;
    gap: 32px;
    justify-content: center;
    flex-wrap: wrap;
  }

  .ns-hero-micro-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
  }

  .ns-hero-micro-stat-value {
    font-size: 22px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.02em;
  }

  .ns-hero-micro-stat-label {
    font-size: 12px;
    color: rgba(255,255,255,0.52);
    font-weight: 600;
    letter-spacing: .03em;
    text-transform: uppercase;
  }

  .ns-hero-micro-stat-divider {
    width: 1px;
    height: 36px;
    background: rgba(255,255,255,0.15);
    align-self: center;
  }

  @media (max-width: 768px) {
    .ns-hero-content { padding: 56px 24px; }
    .ns-hero-content h1 { font-size: 42px; }
    .ns-hero-content p { font-size: 16px; }
    .ns-hero-micro-stats { gap: 20px; }
    .ns-hero-micro-stat-divider { display: none; }
  }

  @media (max-width: 480px) {
    .ns-hero { padding: 16px 0 0 0; }
    .ns-hero-content h1 { font-size: 32px; }
  }

  /* ── Manifiesto ───────────────────────────────────── */
  .manifesto-section {
    padding: 80px 0;
    background: #fff;
  }

  .manifesto-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 52px;
    align-items: center;
  }

  .manifesto-text .label-pill {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 999px;
    background: #f0fdf4;
    color: #166534;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: 20px;
    border: 1px solid #bbf7d0;
  }

  .manifesto-text h2 {
    margin: 0 0 20px 0;
    font-size: 42px;
    line-height: 1.1;
    letter-spacing: -0.03em;
  }

  .manifesto-text p {
    margin: 0 0 16px 0;
    font-size: 17px;
    line-height: 1.75;
    color: #444;
  }

  .manifesto-text p:last-child { margin: 0; }

  .manifesto-quote {
    background: #111;
    border-radius: 24px;
    border-top: 4px solid #22c55e;
    border-left: 4px solid #22c55e;
    padding: 52px 44px;
    color: #fff;
    position: relative;
    overflow: hidden;
  }

  .manifesto-quote-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/hero-cancha.webp');
    background-size: cover;
    background-position: center;
    opacity: 0.08;
    border-radius: 20px;
    pointer-events: none;
  }

  .manifesto-quote::before {
    content: '\201C';
    position: absolute;
    top: 16px;
    left: 32px;
    font-size: 120px;
    line-height: 1;
    color: rgba(74,222,128,0.4);
    font-family: Georgia, serif;
    pointer-events: none;
    z-index: 1;
  }

  .manifesto-quote blockquote {
    margin: 0;
    padding-top: 40px;
    font-size: 22px;
    line-height: 1.55;
    font-style: italic;
    color: rgba(255,255,255,0.92);
    position: relative;
    z-index: 2;
  }

  .manifesto-quote cite {
    display: block;
    margin-top: 24px;
    font-size: 14px;
    font-style: normal;
    color: rgba(255,255,255,0.48);
    font-weight: 700;
    letter-spacing: .04em;
  }

  @media (max-width: 900px) {
    .manifesto-grid { grid-template-columns: 1fr; gap: 32px; }
  }

  @media (max-width: 480px) {
    .manifesto-text h2 { font-size: 30px; }
  }

  /* ── Stats band ──────────────────────────────────── */
  .ns-stats-section {
    padding: 0 0 72px 0;
    background: #fff;
  }

  .ns-stats-band {
    background: #111;
    border-radius: 24px;
    padding: 52px 48px;
  }

  .ns-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0;
    text-align: center;
  }

  .ns-stat-item {
    padding: 20px 16px;
    border-right: 1px solid rgba(255,255,255,0.08);
    transition: border-color .2s;
    cursor: default;
  }

  .ns-stat-item:last-child {
    border-right: none;
  }

  .ns-stat-item:hover {
    border-color: #22c55e;
  }

  .ns-stat-icon {
    font-size: 34px;
    line-height: 1;
    margin-bottom: 14px;
    display: block;
  }

  .ns-stat-value {
    font-size: 15px;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
  }

  .ns-stat-label {
    font-size: 13px;
    color: rgba(255,255,255,0.48);
    font-weight: 500;
    line-height: 1.4;
  }

  @media (max-width: 900px) {
    .ns-stats-grid { grid-template-columns: repeat(2, 1fr); }
    .ns-stat-item:nth-child(2) { border-right: none; }
    .ns-stat-item:nth-child(3) { border-right: 1px solid rgba(255,255,255,0.08); }
  }

  @media (max-width: 768px) {
    .ns-stats-band { padding: 36px 24px; }
  }

  @media (max-width: 480px) {
    .ns-stats-grid { grid-template-columns: 1fr; }
    .ns-stat-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.08); }
    .ns-stat-item:last-child { border-bottom: none; }
  }

  /* ── Timeline ────────────────────────────────────── */
  .ns-history-section {
    padding: 80px 0;
    background: #fff;
  }

  .ns-history-section .section-head {
    text-align: center;
    margin-bottom: 56px;
  }

  .ns-timeline {
    display: flex;
    flex-direction: column;
    gap: 0;
    position: relative;
    max-width: 760px;
    margin: 0 auto;
  }

  .ns-timeline::before {
    content: '';
    position: absolute;
    left: 28px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #16a34a, rgba(22,163,74,0.3), #16a34a);
    border-radius: 2px;
  }

  .ns-tl-item {
    display: flex;
    gap: 32px;
    padding-bottom: 48px;
    position: relative;
  }

  .ns-tl-item:last-child { padding-bottom: 0; }

  .ns-tl-dot {
    flex-shrink: 0;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: #16a34a;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    position: relative;
    z-index: 1;
    box-shadow: 0 0 0 6px rgba(22,163,74,0.15);
  }

  .ns-tl-content {
    padding-top: 10px;
    flex: 1;
  }

  .ns-tl-year {
    display: inline-block;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #166534;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    padding: 3px 12px;
    margin-bottom: 10px;
  }

  .ns-tl-content h3 {
    margin: 0 0 8px 0;
    font-size: 20px;
    letter-spacing: -0.02em;
  }

  .ns-tl-content p {
    margin: 0;
    color: #555;
    font-size: 15px;
    line-height: 1.7;
  }

  @media (max-width: 768px) {
    .ns-timeline::before { left: 24px; }
    .ns-tl-dot { width: 50px; height: 50px; font-size: 18px; }
  }

  /* ── Values ──────────────────────────────────────── */
  .ns-values-section {
    padding: 0 0 80px 0;
    background: #fff;
  }

  .ns-values-section .section-head {
    text-align: center;
    margin-bottom: 48px;
  }

  .ns-values-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }

  .ns-value-card {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-left: 4px solid #22c55e;
    border-radius: 24px;
    padding: 36px 32px;
    transition: transform .2s, box-shadow .2s;
  }

  .ns-value-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(34,197,94,0.12);
  }

  .ns-value-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: #f0fdf4;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin-bottom: 20px;
  }

  .ns-value-card h3 {
    margin: 0 0 10px 0;
    font-size: 19px;
    letter-spacing: -0.02em;
  }

  .ns-value-card p {
    margin: 0;
    color: #555;
    font-size: 15px;
    line-height: 1.7;
  }

  .ns-value-card.ns-accent {
    background: #111;
    border-color: #111;
    border-left-color: #22c55e;
    color: #fff;
  }

  .ns-value-card.ns-accent h3 { color: #fff; }
  .ns-value-card.ns-accent p { color: rgba(255,255,255,0.62); }

  .ns-value-card.ns-accent .ns-value-icon-wrap {
    background: rgba(74,222,128,0.12);
  }

  @media (max-width: 900px) {
    .ns-values-grid { grid-template-columns: 1fr; max-width: 480px; margin: 0 auto; }
  }

  /* ── Culture ─────────────────────────────────────── */
  .ns-culture-section {
    padding: 0 0 80px 0;
  }

  .ns-culture-inner {
    background: #111;
    border-radius: 28px;
    padding: 64px 56px;
    color: #fff;
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 56px;
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
    opacity: 0.14;
    border-radius: 28px;
    pointer-events: none;
  }

  .ns-culture-inner > *:not(.ns-culture-bg) {
    position: relative;
    z-index: 1;
  }

  .ns-culture-left {
    text-align: left;
  }

  .ns-culture-left h2 {
    margin: 0 0 20px 0;
    font-size: 38px;
    line-height: 1.1;
    letter-spacing: -0.03em;
    color: #fff;
  }

  .ns-culture-left p {
    color: rgba(255,255,255,0.65);
    font-size: 16px;
    line-height: 1.78;
    margin: 0;
  }

  .ns-culture-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }

  .ns-culture-pill {
    padding: 12px 20px;
    border-radius: 16px;
    background: rgba(255,255,255,0.07);
    border: 1px solid rgba(255,255,255,0.1);
    font-size: 14px;
    font-weight: 600;
    color: rgba(255,255,255,0.85);
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background .2s, border-color .2s;
    cursor: default;
  }

  .ns-culture-pill:hover {
    background: rgba(255,255,255,0.12);
    border-color: rgba(34,197,94,0.45);
  }

  .ns-culture-pill span {
    font-size: 20px;
  }

  @media (max-width: 900px) {
    .ns-culture-inner { grid-template-columns: 1fr; gap: 32px; }
  }

  @media (max-width: 768px) {
    .ns-culture-inner { padding: 40px 28px; }
    .ns-culture-left h2 { font-size: 30px; }
  }

  /* ── CTA final ───────────────────────────────────── */
  .ns-cta {
    padding: 0 0 80px 0;
  }

  .ns-cta-inner {
    background: linear-gradient(135deg, #0f4c2a 0%, #1a7a45 100%);
    border-radius: 28px;
    padding: 72px 48px;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .ns-cta-inner::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 320px; height: 320px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    pointer-events: none;
  }

  .ns-cta-inner::after {
    content: '';
    position: absolute;
    bottom: -60px; left: 40px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    pointer-events: none;
  }

  .ns-cta-inner h2 {
    margin: 0 0 16px 0;
    font-size: 44px;
    letter-spacing: -0.03em;
    color: #fff;
    position: relative;
    z-index: 1;
  }

  .ns-cta-inner > p {
    margin: 0 auto 36px auto;
    max-width: 520px;
    font-size: 17px;
    color: rgba(255,255,255,0.75);
    line-height: 1.65;
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

  .btn-ns-primary:hover {
    background: #16a34a;
    transform: translateY(-2px);
    color: #fff;
  }

  .btn-ns-ghost {
    padding: 14px 24px;
    background: transparent;
    color: rgba(255,255,255,0.88);
    border-radius: 14px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.5);
    transition: background .15s, border-color .15s, transform .15s;
    display: inline-block;
  }

  .btn-ns-ghost:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(255,255,255,0.8);
    transform: translateY(-2px);
  }

  @media (max-width: 768px) {
    .ns-cta-inner { padding: 48px 28px; }
    .ns-cta-inner h2 { font-size: 30px; }
  }
@endpush

@section('content')

  {{-- ── Hero ─────────────────────────────────────────────────────────── --}}
  <section class="ns-hero">
    <div class="container">
      <div class="ns-hero-inner">
        <div class="ns-hero-bg"></div>
        <div class="ns-hero-overlay"></div>
        <div class="ns-hero-blob-1"></div>
        <div class="ns-hero-blob-2"></div>
        <div class="ns-hero-content">
          <div class="ns-hero-badge" data-aos="fade-down">
            Quiénes somos
          </div>
          <h1 data-aos="fade-up" data-aos-delay="80">
            Nacimos para que<br>jugar sea <em>fácil</em>
          </h1>
          <p data-aos="fade-up" data-aos-delay="160">
            Somos un equipo argentino con una misión simple: que reservar una cancha
            sea tan fácil como pedir una pizza. Sin llamadas, sin grupos de WhatsApp,
            sin "preguntale al encargado".
          </p>
          <div class="ns-hero-micro-stats" data-aos="fade-up" data-aos-delay="260">
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

        <div class="manifesto-text" data-aos="fade-right">
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

        <div class="manifesto-quote" data-aos="fade-left">
          <div class="manifesto-quote-bg"></div>
          <blockquote>
            "El fútbol no debería tener fricción. La cancha tiene que estar a un par de clics, no a veinte llamadas perdidas."
            <cite>— El equipo de TuCancha</cite>
          </blockquote>
        </div>

      </div>
    </div>
  </section>

  {{-- ── Stats band ────────────────────────────────────────────────────── --}}
  <section class="ns-stats-section">
    <div class="container">
      <div class="ns-stats-band">
        <div class="ns-stats-grid">
          <div class="ns-stat-item" data-aos="zoom-in" data-aos-delay="0">
            <span class="ns-stat-icon"><i data-lucide="zap" style="width:28px;height:28px;stroke:#22c55e;stroke-width:2;"></i></span>
            <div class="ns-stat-value">Reservas al instante</div>
            <div class="ns-stat-label">Sin llamadas ni esperas</div>
          </div>
          <div class="ns-stat-item" data-aos="zoom-in" data-aos-delay="80">
            <span class="ns-stat-icon"><i data-lucide="credit-card" style="width:28px;height:28px;stroke:#22c55e;stroke-width:2;"></i></span>
            <div class="ns-stat-value">Cobro automático</div>
            <div class="ns-stat-label">Directo a tu cuenta de MP</div>
          </div>
          <div class="ns-stat-item" data-aos="zoom-in" data-aos-delay="160">
            <span class="ns-stat-icon"><i data-lucide="bar-chart-2" style="width:28px;height:28px;stroke:#22c55e;stroke-width:2;"></i></span>
            <div class="ns-stat-value">Panel completo</div>
            <div class="ns-stat-label">Reservas, agenda y reportes</div>
          </div>
          <div class="ns-stat-item" data-aos="zoom-in" data-aos-delay="240">
            <span class="ns-stat-icon"><i data-lucide="trending-up" style="width:28px;height:28px;stroke:#22c55e;stroke-width:2;"></i></span>
            <div class="ns-stat-value">Sin comisiones</div>
            <div class="ns-stat-label">Precio fijo, sin sorpresas</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Timeline ──────────────────────────────────────────────────────── --}}
  <section class="ns-history-section">
    <div class="container">

      <div class="section-head" data-aos="fade-up">
        <span class="section-label">Cómo llegamos acá</span>
        <h2 class="section-title">El camino de TuCancha</h2>
      </div>

      <div class="ns-timeline">

        <div class="ns-tl-item" data-aos="fade-up" data-aos-delay="0">
          <div class="ns-tl-dot"><i data-lucide="lightbulb" style="width:20px;height:20px;stroke:#22c55e;stroke-width:2;"></i></div>
          <div class="ns-tl-content">
            <span class="ns-tl-year">El origen</span>
            <h3>La idea que no nos dejaba dormir</h3>
            <p>
              Un grupo de amigos frustrado por no poder reservar una cancha online.
              Nadie lo estaba resolviendo bien en Argentina. Decidimos hacerlo nosotros.
            </p>
          </div>
        </div>

        <div class="ns-tl-item" data-aos="fade-up" data-aos-delay="150">
          <div class="ns-tl-dot"><i data-lucide="wrench" style="width:20px;height:20px;stroke:#22c55e;stroke-width:2;"></i></div>
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

        <div class="ns-tl-item" data-aos="fade-up" data-aos-delay="300">
          <div class="ns-tl-dot"><i data-lucide="rocket" style="width:20px;height:20px;stroke:#22c55e;stroke-width:2;"></i></div>
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

        <div class="ns-tl-item" data-aos="fade-up" data-aos-delay="450">
          <div class="ns-tl-dot"><i data-lucide="trending-up" style="width:20px;height:20px;stroke:#22c55e;stroke-width:2;"></i></div>
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

      <div class="section-head" data-aos="fade-up">
        <span class="section-label">Lo que nos mueve</span>
        <h2 class="section-title">Nuestros valores</h2>
      </div>

      <div class="ns-values-grid">

        <div class="ns-value-card ns-accent" data-aos="fade-up" data-aos-delay="50">
          <div class="ns-value-icon-wrap"><i data-lucide="zap" style="width:22px;height:22px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>Simplicidad ante todo</h3>
          <p>
            Si algo se puede hacer en dos clics, no lo hacemos en cuatro.
            Cada pantalla, cada formulario y cada mail existe para hacer
            la vida más fácil, no más complicada.
          </p>
        </div>

        <div class="ns-value-card" data-aos="fade-up" data-aos-delay="100">
          <div class="ns-value-icon-wrap"><i data-lucide="users" style="width:22px;height:22px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>El dueño del complejo es nuestro socio</h3>
          <p>
            No somos un competidor ni un intermediario. Somos la herramienta
            que hace crecer su negocio. Su éxito es nuestro éxito.
          </p>
        </div>

        <div class="ns-value-card" data-aos="fade-up" data-aos-delay="150">
          <div class="ns-value-icon-wrap"><i data-lucide="target" style="width:22px;height:22px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>Foco en lo que importa</h3>
          <p>
            No construimos funcionalidades por construirlas. Cada mejora
            resuelve un problema real que alguien nos contó. Escuchamos
            mucho antes de escribir código.
          </p>
        </div>

        <div class="ns-value-card" data-aos="fade-up" data-aos-delay="200">
          <div class="ns-value-icon-wrap"><i data-lucide="lock" style="width:22px;height:22px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>Confianza y transparencia</h3>
          <p>
            Los pagos se procesan de forma segura, los datos son de los
            usuarios y nunca ocultamos costos. Precio plano, sin sorpresas.
          </p>
        </div>

        <div class="ns-value-card" data-aos="fade-up" data-aos-delay="250">
          <div class="ns-value-icon-wrap"><i data-lucide="activity" style="width:22px;height:22px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>Velocidad sin excusas</h3>
          <p>
            Iteramos rápido. Si algo no funciona lo arreglamos, si algo
            puede mejorar lo mejoramos. No esperamos al lanzamiento perfecto
            porque el perfecto es enemigo del bueno.
          </p>
        </div>

        <div class="ns-value-card" data-aos="fade-up" data-aos-delay="300">
          <div class="ns-value-icon-wrap"><i data-lucide="shield" style="width:22px;height:22px;stroke:#22c55e;stroke-width:2;"></i></div>
          <h3>Somos jugadores también</h3>
          <p>
            Construimos para nosotros mismos. Usamos TuCancha para reservar
            nuestras propias canchas. Esa perspectiva no se puede comprar
            en ningún lado.
          </p>
        </div>

      </div>
    </div>
  </section>

  {{-- ── Culture ───────────────────────────────────────────────────────── --}}
  <section class="ns-culture-section">
    <div class="container">
      <div class="ns-culture-inner">
        <div class="ns-culture-bg"></div>

        <div class="ns-culture-left" data-aos="fade-right">
          <h2>Un equipo chico con ideas grandes</h2>
          <p>
            Somos pocos pero nos movemos rápido. Creemos que los mejores productos
            los hacen equipos que se comunican bien, se retroalimentan sin ego
            y comparten la obsesión por el detalle.
          </p>
        </div>

        <div class="ns-culture-pills" data-aos="fade-left" data-aos-delay="120">
          <div class="ns-culture-pill"><span>🇦🇷</span> 100% argentinos</div>
          <div class="ns-culture-pill"><span>☕</span> Mucho café, poco reunionismo</div>
          <div class="ns-culture-pill"><span><i data-lucide="package" style="width:14px;height:14px;stroke:#111;stroke-width:2;vertical-align:middle;"></i></span> Enviamos a producción seguido</div>
          <div class="ns-culture-pill"><span><i data-lucide="message-circle" style="width:14px;height:14px;stroke:#111;stroke-width:2;vertical-align:middle;"></i></span> Escuchamos a cada usuario</div>
          <div class="ns-culture-pill"><span>🐛</span> Los bugs nos quitan el sueño</div>
          <div class="ns-culture-pill"><span>⚽</span> Jugamos los miércoles</div>
          <div class="ns-culture-pill"><span><i data-lucide="refresh-cw" style="width:14px;height:14px;stroke:#111;stroke-width:2;vertical-align:middle;"></i></span> Iteramos, no planificamos ad infinitum</div>
          <div class="ns-culture-pill"><span><i data-lucide="trending-up" style="width:14px;height:14px;stroke:#111;stroke-width:2;vertical-align:middle;"></i></span> Crecimiento sin perder el alma</div>
        </div>

      </div>
    </div>
  </section>

  {{-- ── CTA Final ─────────────────────────────────────────────────────── --}}
  <section class="ns-cta">
    <div class="container">
      <div class="ns-cta-inner" data-aos="zoom-in">
        <h2>¿Tenés un complejo y querés sumarte?</h2>
        <p>
          Unite a los complejos que ya gestionan sus reservas online con TuCancha.
          Setup en minutos, soporte real y sin comisiones sobre tus ingresos.
        </p>
        <div class="ns-cta-actions">
          <a href="{{ route('planes') }}" class="btn-ns-primary" style="display:inline-flex;align-items:center;gap:8px;">
            Ver planes <i data-lucide="arrow-right" style="width:16px;height:16px;stroke:currentColor;"></i>
          </a>
          <a href="{{ route('como-funciona') }}" class="btn-ns-ghost">
            Cómo funciona
          </a>
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
