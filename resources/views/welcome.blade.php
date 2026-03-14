@extends('layouts.marketing')

@section('title', 'TuCancha — Reservá tu cancha al instante')

@push('styles')
  /* ── Hero ────────────────────────────────────────── */
  .hero { padding: 40px 0 0 0; }

  .hero-inner {
    background: linear-gradient(135deg, #111 0%, #1f1f1f 100%);
    border-radius: 28px;
    padding: 52px 48px 48px 48px;
    color: #fff;
  }

  .hero-label {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    font-size: 13px;
    font-weight: 600;
    color: rgba(255,255,255,.85);
    margin-bottom: 22px;
    letter-spacing: .02em;
  }

  .hero-inner h1 {
    margin: 0 0 16px 0;
    font-size: 62px;
    line-height: 1.02;
    letter-spacing: -0.03em;
    max-width: 780px;
  }

  .hero-inner > p {
    margin: 0 0 30px 0;
    color: rgba(255,255,255,.8);
    font-size: 19px;
    line-height: 1.6;
    max-width: 640px;
  }

  .hero-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
  }

  /* ── Quick search ────────────────────────────────── */
  .search-bar {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,.06);
    padding: 20px 24px;
    display: flex;
    gap: 12px;
    align-items: flex-end;
    flex-wrap: wrap;
  }

  .search-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
    min-width: 150px;
  }

  .search-field label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #888;
  }

  .search-field select,
  .search-field input {
    padding: 11px 14px;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    font: inherit;
    font-size: 14px;
    background: #fafafa;
    color: #111;
    outline: none;
    transition: border-color .15s;
  }

  .search-field select:focus,
  .search-field input:focus { border-color: #111; background: #fff; }

  .search-btn {
    padding: 12px 28px;
    border-radius: 12px;
    background: #111;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    transition: background .15s, transform .15s;
    white-space: nowrap;
    font-family: inherit;
  }

  .search-btn:hover { background: #333; transform: translateY(-1px); }

  /* ── Why cards ───────────────────────────────────── */
  .why-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  }

  .why-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    padding: 24px 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
    transition: transform .2s, box-shadow .2s;
  }

  .why-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.08); }

  .why-icon { font-size: 34px; margin-bottom: 14px; display: block; }

  .why-card h3 { margin: 0 0 8px 0; font-size: 17px; font-weight: 800; }

  .why-card p { margin: 0; color: #666; font-size: 14px; line-height: 1.6; }

  /* ── Owner CTA (simplified) ──────────────────────── */
  .owner-cta {
    background: #111;
    border-radius: 28px;
    padding: 48px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 32px;
    flex-wrap: wrap;
  }

  .owner-cta h2 {
    margin: 0 0 10px 0;
    font-size: 34px;
    letter-spacing: -0.02em;
    line-height: 1.1;
  }

  .owner-cta p {
    margin: 0;
    color: rgba(255,255,255,.72);
    font-size: 16px;
    line-height: 1.6;
    max-width: 560px;
  }

  .owner-cta-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 20px;
  }

  /* ── FAQ ─────────────────────────────────────────── */
  .faq-list { display: flex; flex-direction: column; gap: 10px; }

  .faq-item {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    overflow: hidden;
    transition: box-shadow .2s;
  }

  .faq-item.open { box-shadow: 0 4px 20px rgba(0,0,0,.07); }

  .faq-trigger {
    width: 100%;
    background: none;
    border: none;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    cursor: pointer;
    text-align: left;
    font: inherit;
  }

  .faq-trigger-text { font-size: 16px; font-weight: 700; color: #111; }

  .faq-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f3f3f3;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    color: #555;
    flex-shrink: 0;
    transition: transform .2s, background .2s;
  }

  .faq-item.open .faq-icon { transform: rotate(45deg); background: #111; color: #fff; }

  .faq-body {
    display: none;
    padding: 16px 24px 20px 24px;
    color: #555;
    font-size: 15px;
    line-height: 1.7;
    border-top: 1px solid #f0f0f0;
  }

  .faq-item.open .faq-body { display: block; }

  /* ── Responsive ──────────────────────────────────── */
  @media (max-width: 1024px) {
    .why-grid { grid-template-columns: repeat(2, 1fr); }
  }

  @media (max-width: 768px) {
    .hero-inner { padding: 36px 24px 32px 24px; }
    .hero-inner h1 { font-size: 38px; }
    .hero-inner > p { font-size: 16px; }
    .why-grid { grid-template-columns: 1fr; }
    .owner-cta { padding: 36px 24px; flex-direction: column; align-items: flex-start; }
    .owner-cta h2 { font-size: 26px; }
    .search-bar { flex-direction: column; }
    .search-field { min-width: 100%; }
  }

  @media (max-width: 480px) {
    .hero { padding: 16px 0 0 0; }
    .hero-inner h1 { font-size: 30px; }
  }
@endpush

@section('content')

  {{-- ── Hero ─────────────────────────────────────────────────────── --}}
  <section class="hero">
    <div class="container">
      <div class="hero-inner">
        <span class="hero-label">🏟️ Plataforma de reservas deportivas</span>
        <h1>Reservá tu cancha al instante.</h1>
        <p>
          Explorá los complejos disponibles en tu ciudad y reservá en segundos.
          Sin llamadas, sin filas, sin complicaciones.
        </p>

        <div class="hero-actions">
          <a href="{{ route('venues.index') }}" class="btn btn-primary">Ver complejos</a>
          <a href="{{ route('planes') }}" class="btn btn-ghost">Sumá tu complejo</a>
        </div>
      </div>

      {{-- Quick search --}}
      <form method="GET" action="{{ route('venues.index') }}" class="search-bar">
        <div class="search-field">
          <label for="qs-deporte">Deporte</label>
          <select id="qs-deporte" name="deporte">
            <option value="">Cualquier deporte</option>
            <option value="futbol">Fútbol</option>
            <option value="padel">Pádel</option>
            <option value="tenis">Tenis</option>
            <option value="basquet">Básquet</option>
          </select>
        </div>

        <div class="search-field">
          <label for="qs-fecha">Fecha</label>
          <input
            type="date"
            id="qs-fecha"
            name="fecha"
            value="{{ now()->toDateString() }}"
            min="{{ now()->toDateString() }}"
          >
        </div>

        <button type="submit" class="search-btn">Buscar →</button>
      </form>
    </div>
  </section>

  {{-- ── ¿Por qué TuCancha? ────────────────────────────────────────── --}}
  <section id="por-que" class="section">
    <div class="container">
      <div class="section-head">
        <span class="section-label">Beneficios</span>
        <h2 class="section-title">¿Por qué TuCancha?</h2>
        <p class="section-subtitle">
          Diseñamos cada detalle para que reservar una cancha sea tan simple como mandar un mensaje.
        </p>
      </div>

      <div class="why-grid">
        <div class="why-card">
          <span class="why-icon">⚡</span>
          <h3>Reservas al instante</h3>
          <p>Elegí complejo, horario y confirmá tu turno en segundos. Sin esperas ni llamadas.</p>
        </div>

        <div class="why-card">
          <span class="why-icon">💳</span>
          <h3>Pago seguro con Mercado Pago</h3>
          <p>Pagá online con tarjeta o efectivo. Tu reserva se confirma automáticamente.</p>
        </div>

        <div class="why-card">
          <span class="why-icon">📱</span>
          <h3>Desde cualquier dispositivo</h3>
          <p>Reservá desde el celular, la tablet o la computadora. La plataforma se adapta a tu pantalla.</p>
        </div>

        <div class="why-card">
          <span class="why-icon">🔔</span>
          <h3>Recordatorios por mail</h3>
          <p>Confirmación inmediata y recordatorios para que no se te escape ningún turno.</p>
        </div>
      </div>
    </div>
  </section>

  {{-- ── Dueños (simplificado — los detalles están en /como-funciona) ── --}}
  <section id="para-duenos" style="padding: 0 0 52px 0;">
    <div class="container">
      <div class="owner-cta">
        <div>
          <h2>Sumá tu complejo a TuCancha</h2>
          <p>
            Recibí reservas online 24/7, cobrá automáticamente y gestioná todo desde un panel simple.
            Sin complicaciones técnicas.
          </p>

          <div class="owner-cta-actions">
            <a href="{{ route('planes') }}" class="btn" style="background:#fff; color:#111; border-radius:14px; padding:12px 28px; font-size:15px;">
              Quiero sumarme →
            </a>
            <a href="{{ url('/como-funciona') }}" class="btn btn-ghost" style="border-radius:14px; padding:12px 24px; font-size:14px;">
              Ver cómo funciona
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ── FAQ ──────────────────────────────────────────────────────── --}}
  <section id="faq" class="section" style="padding-top: 0;">
    <div class="container">
      <div class="section-head">
        <span class="section-label">Dudas frecuentes</span>
        <h2 class="section-title">Preguntas frecuentes</h2>
        <p class="section-subtitle">Todo lo que necesitás saber antes de hacer tu primera reserva.</p>
      </div>

      <div class="faq-list">
        <div class="faq-item">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Cómo reservo una cancha?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-body">
            Creá una cuenta, explorá los complejos, elegí la cancha y el horario.
            Confirmás la reserva, pagás online y recibís la confirmación por mail en segundos.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Cómo sé si mi reserva está confirmada?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-body">
            Una vez aprobado el pago tu reserva pasa a estado <strong>Confirmada</strong> automáticamente.
            Recibís un mail con los detalles y un código de verificación para presentar en el complejo.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Puedo cancelar una reserva?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-body">
            Sí. Podés cancelar desde "Mis reservas" antes de que empiece el turno.
            Si el pago fue procesado, se inicia el reintegro automáticamente a través de Mercado Pago.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Qué métodos de pago aceptan?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-body">
            Aceptamos todos los métodos de Mercado Pago: tarjetas de crédito y débito, transferencia bancaria
            y efectivo en puntos de pago. El proceso es 100% seguro.
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Cómo me registro?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-body">
            Hacé clic en "Crear cuenta", completá tu nombre, mail y contraseña.
            En menos de un minuto tenés tu cuenta lista. No se requiere tarjeta para registrarse.
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  function toggleFaq(trigger) {
    const item = trigger.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  }
</script>
@endpush
