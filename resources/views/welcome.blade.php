<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>TuCancha</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    * { box-sizing: border-box; }

    html {
      scroll-behavior: smooth;
    }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      background: #f7f7f8;
      color: #111;
    }

    a {
      color: inherit;
      text-decoration: none;
    }

    .header {
      position: sticky;
      top: 0;
      z-index: 50;
      background: rgba(255,255,255,.95);
      backdrop-filter: blur(8px);
      border-bottom: 1px solid #ececec;
    }

    .header-inner,
    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 24px;
    }

    .header-inner {
      min-height: 76px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }

    .brand {
      font-size: 24px;
      font-weight: 800;
      letter-spacing: -0.02em;
    }

    .nav {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .nav a,
    .nav button,
    .btn {
      display: inline-block;
      border: 1px solid #ddd;
      background: #fff;
      padding: 10px 16px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
    }

    .btn-primary,
    .nav .btn-primary {
      background: #111;
      color: #fff;
      border-color: #111;
    }

    .hero {
      padding: 56px 0 36px 0;
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.2fr .8fr;
      gap: 24px;
      align-items: stretch;
    }

    .hero-main,
    .hero-side,
    .section-card,
    .contact-card {
      background: #fff;
      border: 1px solid #ececec;
      border-radius: 28px;
      box-shadow: 0 6px 24px rgba(0,0,0,.04);
    }

    .hero-main {
      padding: 34px;
      background: linear-gradient(135deg, #111 0%, #1f1f1f 100%);
      color: #fff;
    }

    .hero-main h1 {
      margin: 0 0 14px 0;
      font-size: 58px;
      line-height: 1.02;
      letter-spacing: -0.03em;
      max-width: 760px;
    }

    .hero-main p {
      margin: 0;
      color: rgba(255,255,255,.84);
      font-size: 18px;
      line-height: 1.6;
      max-width: 720px;
    }

    .hero-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 24px;
    }

    .hero-actions .btn {
      border-color: rgba(255,255,255,.18);
      background: rgba(255,255,255,.08);
      color: #fff;
    }

    .hero-actions .btn-primary {
      background: #fff;
      color: #111;
      border-color: #fff;
    }

    .hero-side {
      padding: 26px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      gap: 16px;
    }

    .mini-stat {
      padding: 16px;
      border-radius: 18px;
      background: #f7f7f8;
      border: 1px solid #eee;
    }

    .mini-stat .label {
      font-size: 12px;
      color: #666;
      margin-bottom: 8px;
    }

    .mini-stat .value {
      font-size: 32px;
      font-weight: 800;
      line-height: 1;
    }

    .section {
      padding: 18px 0 8px 0;
    }

    .section-title {
      font-size: 34px;
      letter-spacing: -0.02em;
      margin: 0 0 10px 0;
    }

    .section-subtitle {
      margin: 0 0 22px 0;
      color: #666;
      font-size: 16px;
      max-width: 760px;
      line-height: 1.6;
    }

    .grid-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
    }

    .section-card {
      padding: 22px;
    }

    .section-card h3 {
      margin: 0 0 10px 0;
      font-size: 22px;
    }

    .section-card p {
      margin: 0;
      color: #666;
      line-height: 1.6;
    }

    .partners {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
    }

    .partner {
      background: #fff;
      border: 1px solid #ececec;
      border-radius: 18px;
      padding: 18px;
      text-align: center;
      font-weight: 700;
      box-shadow: 0 4px 16px rgba(0,0,0,.03);
    }

    .cta {
      padding: 26px;
      border-radius: 28px;
      background: #111;
      color: #fff;
      margin-top: 18px;
    }

    .cta h2 {
      margin: 0 0 10px 0;
      font-size: 34px;
    }

    .cta p {
      margin: 0;
      color: rgba(255,255,255,.82);
      line-height: 1.6;
      max-width: 720px;
    }

    .cta-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 20px;
    }

    .cta-actions .btn {
      background: rgba(255,255,255,.08);
      color: #fff;
      border-color: rgba(255,255,255,.16);
    }

    .cta-actions .btn-primary {
      background: #fff;
      color: #111;
      border-color: #fff;
    }

    .contact {
      padding: 28px 0 56px 0;
    }

    .contact-card {
      padding: 26px;
    }

    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 24px;
      align-items: start;
    }

    .contact-box {
      padding: 18px;
      border-radius: 18px;
      background: #f7f7f8;
      border: 1px solid #eee;
    }

    .contact-box h3 {
      margin-top: 0;
      margin-bottom: 8px;
    }

    .contact-box p {
      margin: 0;
      color: #666;
      line-height: 1.6;
    }

    .contact-form {
      display: grid;
      gap: 12px;
    }

    .contact-form input,
    .contact-form textarea {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #ddd;
      border-radius: 14px;
      background: #fff;
      font: inherit;
    }

    .footer {
      padding: 18px 0 30px 0;
      color: #666;
      font-size: 14px;
    }

    @media (max-width: 980px) {
      .hero-grid,
      .grid-3,
      .partners,
      .contact-grid {
        grid-template-columns: 1fr;
      }

      .hero-main h1 {
        font-size: 40px;
      }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="header-inner">
      <a href="{{ route('home') }}" class="brand">TuCancha</a>

      <nav class="nav">
        <a href="#como-funciona">Cómo funciona</a>
        <a href="#socios">Socios</a>
        <a href="#contacto">Contacto</a>

        @auth
          <a href="{{ route('venues.index') }}" class="btn-primary">Entrar al sistema</a>
        @else
          <a href="{{ route('login') }}">Ingresar</a>
          <a href="{{ route('register') }}" class="btn-primary">Crear cuenta</a>
        @endauth
      </nav>
    </div>
  </header>

  <main>
    <section class="hero">
      <div class="container">
        <div class="hero-grid">
          <div class="hero-main">
            <h1>Reservas deportivas simples, rápidas y profesionales.</h1>
            <p>
              TuCancha conecta jugadores con complejos deportivos en una sola plataforma.
              Reservá online, pagá en minutos y gestioná tus turnos desde cualquier lugar.
            </p>

            <div class="hero-actions">
              @auth
                <a href="{{ route('venues.index') }}" class="btn btn-primary">Ir a reservar</a>
              @else
                <a href="{{ route('register') }}" class="btn btn-primary">Empezar ahora</a>
                <a href="{{ route('login') }}" class="btn">Ya tengo cuenta</a>
              @endauth
            </div>
          </div>

          <div class="hero-side">
            <div class="mini-stat">
              <div class="label">Qué ofrece el sistema</div>
              <div class="value">Reservá, pagá y jugá</div>
            </div>

            <div class="mini-stat">
              <div class="label">Para usuarios</div>
              <div class="value">Búsqueda + favoritos + reseñas</div>
            </div>

            <div class="mini-stat">
              <div class="label">Para complejos</div>
              <div class="value">Panel admin + pagos + control</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="como-funciona" class="section">
      <div class="container">
        <h2 class="section-title">Qué hace TuCancha</h2>
        <p class="section-subtitle">
          Diseñamos una plataforma para que reservar una cancha sea tan fácil como pedir un turno online.
          También ayudamos a los complejos a ordenar sus reservas, cobros y operación diaria.
        </p>

        <div class="grid-3">
          <div class="section-card">
            <h3>Reservas online</h3>
            <p>
              Los usuarios pueden buscar complejos, revisar disponibilidad, elegir horario y confirmar su turno
              desde una interfaz clara y rápida.
            </p>
          </div>

          <div class="section-card">
            <h3>Pagos integrados</h3>
            <p>
              El sistema permite pagar online y confirmar automáticamente la reserva, reduciendo errores y
              mejorando la experiencia del cliente.
            </p>
          </div>

          <div class="section-card">
            <h3>Gestión para complejos</h3>
            <p>
              Los administradores pueden gestionar canchas, bloquear horarios, ver reservas, hacer check-in
              y analizar el movimiento de su negocio.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section id="socios" class="section">
      <div class="container">
        <h2 class="section-title">Nuestros socios</h2>
        <p class="section-subtitle">
          Trabajamos con complejos y aliados estratégicos para ofrecer una experiencia confiable, moderna y escalable.
          Acá podés mostrar los nombres reales de tus socios cuando los tengas definidos.
        </p>

        <div class="partners">
          <div class="partner">Socio 1</div>
          <div class="partner">Socio 2</div>
          <div class="partner">Socio 3</div>
          <div class="partner">Socio 4</div>
        </div>
      </div>
    </section>

    <section class="section">
      <div class="container">
        <div class="cta">
          <h2>Entrá al sistema y empezá a reservar</h2>
          <p>
            Si ya tenés una cuenta, podés ingresar y acceder al listado de complejos, reservar canchas,
            guardar favoritos y administrar tus turnos.
          </p>

          <div class="cta-actions">
            @auth
              <a href="{{ route('venues.index') }}" class="btn btn-primary">Ir a complejos</a>
            @else
              <a href="{{ route('login') }}" class="btn btn-primary">Iniciar sesión</a>
              <a href="{{ route('register') }}" class="btn">Crear cuenta</a>
            @endauth
          </div>
        </div>
      </div>
    </section>

    <section id="contacto" class="contact">
      <div class="container">
        <div class="contact-card">
          <h2 class="section-title" style="margin-bottom:10px;">Contactanos</h2>
          <p class="section-subtitle" style="margin-bottom:22px;">
            Si querés sumar tu complejo a la plataforma o tenés dudas sobre el sistema, escribinos.
          </p>

          @if(session('success'))
            <div style="margin-bottom:14px; padding:12px 14px; border-radius:12px; background:#e8f7ee; color:#157347; border:1px solid #cfe9d7;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="margin-bottom:14px; padding:12px 14px; border-radius:12px; background:#f8d7da; color:#842029; border:1px solid #f1b9c0;">
                Revisá los campos del formulario.
            </div>
        @endif

          <div class="contact-grid">
            <div class="contact-box">
              <h3>¿Querés unirte a TuCancha?</h3>
              <p>
                Si administrás un complejo deportivo, podemos ayudarte a digitalizar tus reservas,
                pagos y control diario.
              </p>
            </div>

            <form method="POST" action="{{ route('contact.send') }}" class="contact-form">
            @csrf

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Tu nombre"
                required
            >

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Tu email"
                required
            >

            <input
                type="text"
                name="reason"
                value="{{ old('reason') }}"
                placeholder="Motivo de contacto"
                required
            >

            <textarea
                name="message"
                rows="5"
                placeholder="Contanos tu consulta"
                required
            >{{ old('message') }}</textarea>

            <button type="submit" class="btn btn-primary">Enviar consulta</button>
            </form>
          </div>
        </div>

        <div class="footer">
          © {{ date('Y') }} TuCancha — Sistema de reservas deportivas.
        </div>
      </div>
    </section>
  </main>
</body>
</html>
