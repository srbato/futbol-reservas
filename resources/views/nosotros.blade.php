@extends('layouts.marketing')

@section('title', 'Quiénes somos — TuCancha')

@push('styles')
  /* ── Hero ────────────────────────────────────────── */
  .ns-hero {
    padding: 48px 0 0 0;
  }

  .ns-hero-inner {
    background: linear-gradient(135deg, #111 0%, #1a1a2e 60%, #16213e 100%);
    border-radius: 28px;
    padding: 80px 48px;
    color: #fff;
    text-align: center;
    position: relative;
    overflow: hidden;
  }

  .ns-hero-inner::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      radial-gradient(ellipse 60% 50% at 20% 50%, rgba(74,222,128,.08) 0%, transparent 70%),
      radial-gradient(ellipse 50% 60% at 80% 30%, rgba(99,102,241,.1) 0%, transparent 70%);
    pointer-events: none;
  }

  .ns-hero-label {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 999px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: rgba(255,255,255,.8);
    margin-bottom: 24px;
  }

  .ns-hero-inner h1 {
    margin: 0 0 20px 0;
    font-size: 64px;
    line-height: 1.02;
    letter-spacing: -0.04em;
    position: relative;
  }

  .ns-hero-inner h1 em {
    font-style: normal;
    color: #4ade80;
  }

  .ns-hero-inner p {
    margin: 0 auto;
    max-width: 620px;
    color: rgba(255,255,255,.7);
    font-size: 19px;
    line-height: 1.65;
    position: relative;
  }

  /* ── Manifesto ───────────────────────────────────── */
  .manifesto-section {
    padding: 72px 0;
  }

  .manifesto-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 48px;
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
    padding: 48px 40px;
    color: #fff;
    position: relative;
  }

  .manifesto-quote::before {
    content: '"';
    position: absolute;
    top: 24px;
    left: 36px;
    font-size: 100px;
    line-height: 1;
    color: rgba(74,222,128,.3);
    font-family: Georgia, serif;
    pointer-events: none;
  }

  .manifesto-quote blockquote {
    margin: 0;
    padding-top: 32px;
    font-size: 22px;
    line-height: 1.55;
    font-style: italic;
    color: rgba(255,255,255,.9);
    position: relative;
  }

  .manifesto-quote cite {
    display: block;
    margin-top: 24px;
    font-size: 14px;
    font-style: normal;
    color: rgba(255,255,255,.5);
    font-weight: 700;
    letter-spacing: .04em;
  }

  /* ── Stats ───────────────────────────────────────── */
  .stats-section {
    padding: 0 0 72px 0;
  }

  .stats-band {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border: 1px solid #bbf7d0;
    border-radius: 24px;
    padding: 52px 48px;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 32px;
    text-align: center;
  }

  .stat-item {}

  .stat-number {
    font-size: 52px;
    font-weight: 800;
    letter-spacing: -0.04em;
    line-height: 1;
    color: #111;
  }

  .stat-label {
    margin-top: 8px;
    font-size: 14px;
    color: #555;
    font-weight: 600;
    line-height: 1.4;
  }

  /* ── Timeline / Historia ─────────────────────────── */
  .history-section {
    padding: 0 0 80px 0;
  }

  .history-section .section-head {
    text-align: center;
    margin-bottom: 56px;
  }

  .timeline {
    display: flex;
    flex-direction: column;
    gap: 0;
    position: relative;
    max-width: 760px;
    margin: 0 auto;
  }

  .timeline::before {
    content: '';
    position: absolute;
    left: 28px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #4ade80, #111, #4ade80);
    border-radius: 2px;
  }

  .tl-item {
    display: flex;
    gap: 32px;
    padding-bottom: 48px;
    position: relative;
  }

  .tl-item:last-child { padding-bottom: 0; }

  .tl-dot {
    flex-shrink: 0;
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: #111;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    position: relative;
    z-index: 1;
    box-shadow: 0 0 0 6px #f7f7f8;
  }

  .tl-content {
    padding-top: 10px;
    flex: 1;
  }

  .tl-year {
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

  .tl-content h3 {
    margin: 0 0 8px 0;
    font-size: 20px;
    letter-spacing: -0.02em;
  }

  .tl-content p {
    margin: 0;
    color: #555;
    font-size: 15px;
    line-height: 1.7;
  }

  /* ── Values ──────────────────────────────────────── */
  .values-section {
    padding: 0 0 80px 0;
  }

  .values-section .section-head {
    text-align: center;
    margin-bottom: 48px;
  }

  .values-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
  }

  .value-card {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 24px;
    padding: 36px 32px;
    transition: transform .2s, box-shadow .2s;
  }

  .value-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(0,0,0,.08);
  }

  .value-icon {
    font-size: 36px;
    margin-bottom: 20px;
    display: block;
  }

  .value-card h3 {
    margin: 0 0 10px 0;
    font-size: 19px;
    letter-spacing: -0.02em;
  }

  .value-card p {
    margin: 0;
    color: #555;
    font-size: 15px;
    line-height: 1.7;
  }

  .value-card.accent {
    background: #111;
    border-color: #111;
    color: #fff;
  }

  .value-card.accent p { color: rgba(255,255,255,.65); }

  /* ── Team culture ────────────────────────────────── */
  .culture-section {
    padding: 0 0 80px 0;
  }

  .culture-inner {
    background: linear-gradient(135deg, #111 0%, #1f1f1f 100%);
    border-radius: 28px;
    padding: 64px 48px;
    color: #fff;
    display: grid;
    grid-template-columns: 1fr 1.4fr;
    gap: 64px;
    align-items: center;
  }

  .culture-left h2 {
    margin: 0 0 16px 0;
    font-size: 40px;
    line-height: 1.1;
    letter-spacing: -0.03em;
  }

  .culture-left p {
    color: rgba(255,255,255,.65);
    font-size: 16px;
    line-height: 1.75;
    margin: 0;
  }

  .culture-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }

  .culture-pill {
    padding: 12px 20px;
    border-radius: 16px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.1);
    font-size: 14px;
    font-weight: 600;
    color: rgba(255,255,255,.85);
    display: flex;
    align-items: center;
    gap: 10px;
    transition: background .2s;
  }

  .culture-pill:hover { background: rgba(255,255,255,.13); }

  .culture-pill span {
    font-size: 20px;
  }

  /* ── CTA final ───────────────────────────────────── */
  .ns-cta {
    padding: 0 0 80px 0;
  }

  .ns-cta-inner {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 28px;
    padding: 64px 48px;
    text-align: center;
  }

  .ns-cta-inner h2 {
    margin: 0 0 16px 0;
    font-size: 42px;
    letter-spacing: -0.03em;
  }

  .ns-cta-inner p {
    margin: 0 auto 32px auto;
    max-width: 520px;
    font-size: 17px;
    color: #555;
    line-height: 1.65;
  }

  .ns-cta-actions {
    display: flex;
    justify-content: center;
    gap: 14px;
    flex-wrap: wrap;
  }

  /* ── Responsive ──────────────────────────────────── */
  @media (max-width: 900px) {
    .manifesto-grid,
    .culture-inner {
      grid-template-columns: 1fr;
      gap: 32px;
    }

    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .values-grid {
      grid-template-columns: 1fr;
      max-width: 480px;
      margin: 0 auto;
    }
  }

  @media (max-width: 768px) {
    .ns-hero-inner { padding: 52px 24px; }
    .ns-hero-inner h1 { font-size: 40px; }
    .stats-band { padding: 36px 24px; }
    .culture-inner { padding: 40px 28px; }
    .ns-cta-inner { padding: 48px 28px; }
    .timeline::before { left: 24px; }
    .tl-dot { width: 50px; height: 50px; font-size: 18px; }
  }

  @media (max-width: 480px) {
    .ns-hero-inner h1 { font-size: 32px; }
    .stat-number { font-size: 40px; }
    .manifesto-text h2 { font-size: 32px; }
    .culture-left h2 { font-size: 30px; }
    .ns-cta-inner h2 { font-size: 30px; }
  }
@endpush

@section('content')

  {{-- ── Hero ─────────────────────────────────────────────────────────── --}}
  <section class="ns-hero">
    <div class="container">
      <div class="ns-hero-inner">
        <div class="ns-hero-label">Quiénes somos</div>
        <h1>Nacimos para que<br>jugar sea <em>fácil</em></h1>
        <p>
          Somos un equipo argentino con una misión simple: que reservar una cancha
          sea tan fácil como pedir una pizza. Sin llamadas, sin grupos de WhatsApp,
          sin "preguntale al encargado".
        </p>
      </div>
    </div>
  </section>

  {{-- ── Manifesto ───────────────────────────────────────────────────── --}}
  <section class="manifesto-section">
    <div class="container">
      <div class="manifesto-grid">

        <div class="manifesto-text">
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

        <div class="manifesto-quote">
          <blockquote>
            "El fútbol no debería tener fricción. La cancha tiene que estar a un par de clics, no a veinte llamadas perdidas."
            <cite>— El equipo de TuCancha</cite>
          </blockquote>
        </div>

      </div>
    </div>
  </section>

  {{-- ── Stats ───────────────────────────────────────────────────────── --}}
  <section class="stats-section">
    <div class="container">
      <div class="stats-band">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-number" data-target="500">0</div>
            <div class="stat-label">Reservas procesadas</div>
          </div>
          <div class="stat-item">
            <div class="stat-number" data-target="30">0</div>
            <div class="stat-label">Complejos activos</div>
          </div>
          <div class="stat-item">
            <div class="stat-number" data-target="800">0</div>
            <div class="stat-label">Jugadores registrados</div>
          </div>
          <div class="stat-item">
            <div class="stat-number" data-target="100">0</div>
            <div class="stat-label">Canchas disponibles</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Timeline ─────────────────────────────────────────────────────── --}}
  <section class="history-section">
    <div class="container">

      <div class="section-head">
        <span class="section-label">Cómo llegamos acá</span>
        <h2 class="section-title">El camino de TuCancha</h2>
      </div>

      <div class="timeline">

        <div class="tl-item">
          <div class="tl-dot">💡</div>
          <div class="tl-content">
            <span class="tl-year">El origen</span>
            <h3>La idea que no nos dejaba dormir</h3>
            <p>
              Un grupo de amigos frustrado por no poder reservar una cancha online.
              Nadie lo estaba resolviendo bien en Argentina. Decidimos hacerlo nosotros.
            </p>
          </div>
        </div>

        <div class="tl-item">
          <div class="tl-dot">🔨</div>
          <div class="tl-content">
            <span class="tl-year">Construcción</span>
            <h3>Primeras líneas de código</h3>
            <p>
              Empezamos a construir la plataforma desde cero. Diseño, backend, pagos,
              mails automáticos. Cada detalle pensado para que la experiencia sea
              simple tanto para el jugador como para el dueño del complejo.
            </p>
          </div>
        </div>

        <div class="tl-item">
          <div class="tl-dot">🚀</div>
          <div class="tl-content">
            <span class="tl-year">Lanzamiento</span>
            <h3>Los primeros complejos se suman</h3>
            <p>
              Los primeros complejos empiezan a usar TuCancha. Las reservas llegan
              solas, los cobros se procesan automáticamente y los dueños
              pueden enfocarse en lo que importa: el negocio.
            </p>
          </div>
        </div>

        <div class="tl-item">
          <div class="tl-dot">📈</div>
          <div class="tl-content">
            <span class="tl-year">Hoy</span>
            <h3>Creciendo junto a la comunidad</h3>
            <p>
              Seguimos sumando complejos, escuchando a los usuarios y lanzando
              funcionalidades nuevas. Reservas recurrentes, descuentos, check-in
              con código, reportes. Y hay mucho más por venir.
            </p>
          </div>
        </div>

      </div>
    </div>
  </section>

  {{-- ── Values ───────────────────────────────────────────────────────── --}}
  <section class="values-section">
    <div class="container">

      <div class="section-head">
        <span class="section-label">Lo que nos mueve</span>
        <h2 class="section-title">Nuestros valores</h2>
      </div>

      <div class="values-grid">

        <div class="value-card accent">
          <span class="value-icon">⚡</span>
          <h3>Simplicidad ante todo</h3>
          <p>
            Si algo se puede hacer en dos clics, no lo hacemos en cuatro.
            Cada pantalla, cada formulario y cada mail existe para hacer
            la vida más fácil, no más complicada.
          </p>
        </div>

        <div class="value-card">
          <span class="value-icon">🤝</span>
          <h3>El dueño del complejo es nuestro socio</h3>
          <p>
            No somos un competidor ni un intermediario. Somos la herramienta
            que hace crecer su negocio. Su éxito es nuestro éxito.
          </p>
        </div>

        <div class="value-card">
          <span class="value-icon">🎯</span>
          <h3>Foco en lo que importa</h3>
          <p>
            No construimos funcionalidades por construirlas. Cada mejora
            resuelve un problema real que alguien nos contó. Escuchamos
            mucho antes de escribir código.
          </p>
        </div>

        <div class="value-card">
          <span class="value-icon">🔒</span>
          <h3>Confianza y transparencia</h3>
          <p>
            Los pagos se procesan de forma segura, los datos son de los
            usuarios y nunca ocultamos costos. Precio plano, sin sorpresas.
          </p>
        </div>

        <div class="value-card">
          <span class="value-icon">🏃</span>
          <h3>Velocidad sin excusas</h3>
          <p>
            Iteramos rápido. Si algo no funciona lo arreglamos, si algo
            puede mejorar lo mejoramos. No esperamos al lanzamiento perfecto
            porque el perfecto es enemigo del bueno.
          </p>
        </div>

        <div class="value-card">
          <span class="value-icon">⚽</span>
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

  {{-- ── Culture ──────────────────────────────────────────────────────── --}}
  <section class="culture-section">
    <div class="container">
      <div class="culture-inner">

        <div class="culture-left">
          <h2>Un equipo chico con ideas grandes</h2>
          <p>
            Somos pocos pero nos movemos rápido. Creemos que los mejores productos
            los hacen equipos que se comunican bien, se retroalimentan sin ego
            y comparten la obsesión por el detalle.
          </p>
        </div>

        <div class="culture-pills">
          <div class="culture-pill"><span>🇦🇷</span> 100% argentinos</div>
          <div class="culture-pill"><span>☕</span> Mucho café, poco reunionismo</div>
          <div class="culture-pill"><span>📦</span> Enviamos a producción seguido</div>
          <div class="culture-pill"><span>💬</span> Escuchamos a cada usuario</div>
          <div class="culture-pill"><span>🐛</span> Los bugs nos quitan el sueño</div>
          <div class="culture-pill"><span>⚽</span> Jugamos los miércoles</div>
          <div class="culture-pill"><span>🔁</span> Iteramos, no planificamos ad infinitum</div>
          <div class="culture-pill"><span>📈</span> Crecimiento sin perder el alma</div>
        </div>

      </div>
    </div>
  </section>

  {{-- ── CTA Final ────────────────────────────────────────────────────── --}}
  <section class="ns-cta">
    <div class="container">
      <div class="ns-cta-inner">
        <h2>¿Tenés un complejo y querés sumarte?</h2>
        <p>
          Unite a los complejos que ya gestionan sus reservas online con TuCancha.
          Setup en minutos, soporte real y sin comisiones sobre tus ingresos.
        </p>
        <div class="ns-cta-actions">
          <a href="{{ route('planes') }}" class="btn btn-primary" style="padding:14px 32px; font-size:15px; border-radius:14px;">
            Ver planes →
          </a>
          <a href="{{ route('como-funciona') }}" class="btn" style="padding:14px 32px; font-size:15px; border-radius:14px; background:#fff; border:1px solid #ddd;">
            Cómo funciona
          </a>
        </div>
      </div>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  // Counter animation
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

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseInt(el.dataset.target);
        animateCounter(el, target, 1400);
        observer.unobserve(el);
      }
    });
  }, { threshold: 0.5 });

  document.querySelectorAll('[data-target]').forEach(el => observer.observe(el));
</script>
@endpush
