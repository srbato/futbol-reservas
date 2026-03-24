@extends('layouts.marketing')

@section('title', 'TuCancha — Reservá tu cancha al instante')

@push('styles')
/* ── Smooth scroll global ────────────────────────── */
html { scroll-behavior: smooth; }

/* ── Hero full-height ─────────────────────────────── */
.hero-full {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  overflow: hidden;
  background: #0a0a0a;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background-image: url('/Images/hero-cancha.webp');
  background-size: cover;
  background-position: center 30%;
  background-attachment: fixed;
  opacity: .55;
}

/* ── Parallax floating blobs ─────────────────────── */
.parallax-blob {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  will-change: transform;
  z-index: 1;
}

.parallax-blob-1 {
  width: 300px;
  height: 300px;
  background: rgba(34,197,94,.08);
  top: -60px;
  right: -80px;
}

.parallax-blob-2 {
  width: 200px;
  height: 200px;
  background: rgba(34,197,94,.12);
  bottom: 80px;
  left: -50px;
}

.parallax-blob-3 {
  width: 150px;
  height: 150px;
  background: rgba(34,197,94,.08);
  top: 40%;
  left: 45%;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(0,0,0,.82) 0%, rgba(0,0,0,.55) 60%, rgba(0,0,0,.45) 100%);
}

.hero-content {
  position: relative;
  z-index: 2;
  width: 100%;
  padding: 100px 0 72px 0;
}

.hero-grid {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 56px;
  align-items: center;
}

/* Badge pulsante */
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 16px 6px 8px;
  border-radius: 999px;
  background: rgba(110, 234, 160, .12);
  border: 1px solid rgba(110, 234, 160, .25);
  font-size: 13px;
  font-weight: 700;
  color: #6eeaa0;
  margin-bottom: 28px;
  letter-spacing: .02em;
}

.hero-badge-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #6eeaa0;
  animation: pulse-dot 1.8s ease-in-out infinite;
}

@keyframes pulse-dot {
  0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(110,234,160,.4); }
  50% { transform: scale(1.2); opacity: .9; box-shadow: 0 0 0 6px rgba(110,234,160,0); }
}

.hero-copy h1 {
  margin: 0 0 20px 0;
  font-size: 68px;
  line-height: 1.0;
  letter-spacing: -0.035em;
  color: #fff;
  font-weight: 900;
}

.hero-copy h1 em {
  font-style: normal;
  color: #6eeaa0;
}

.hero-copy > p {
  margin: 0 0 36px 0;
  color: rgba(255,255,255,.78);
  font-size: 19px;
  line-height: 1.65;
  max-width: 560px;
}

.hero-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 52px;
}

/* Micro stats */
.hero-stats {
  display: flex;
  gap: 40px;
  flex-wrap: wrap;
}

.hero-stat-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.hero-stat-num {
  font-size: 28px;
  font-weight: 900;
  color: #fff;
  letter-spacing: -0.03em;
  line-height: 1;
}

.hero-stat-label {
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,.5);
  text-transform: uppercase;
  letter-spacing: .07em;
}

/* Search card */
.search-card {
  background: rgba(255,255,255,.97);
  border-radius: 24px;
  padding: 28px 28px 24px 28px;
  box-shadow: 0 20px 60px rgba(0,0,0,.35), 0 4px 16px rgba(0,0,0,.15);
}

.search-card-title {
  font-size: 15px;
  font-weight: 800;
  color: #111;
  margin: 0 0 20px 0;
  letter-spacing: -0.01em;
}

.search-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 14px;
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
  padding: 12px 14px;
  border: 1.5px solid #e8e8e8;
  border-radius: 12px;
  font: inherit;
  font-size: 14px;
  background: #fafafa;
  color: #111;
  outline: none;
  transition: border-color .15s, background .15s;
  width: 100%;
}

.search-field select:focus,
.search-field input:focus {
  border-color: #22c55e;
  background: #fff;
}

.search-btn-full {
  display: block;
  width: 100%;
  padding: 14px;
  border-radius: 14px;
  background: #111;
  color: #fff;
  font-size: 15px;
  font-weight: 800;
  border: none;
  cursor: pointer;
  transition: background .15s, transform .15s;
  font-family: inherit;
  letter-spacing: -0.01em;
  margin-top: 6px;
}

.search-btn-full:hover { background: #1a7a45; transform: translateY(-1px); }

/* ── Sports strip ────────────────────────────────── */
.sports-strip {
  background: #fff;
  border-top: 1px solid #ececec;
  border-bottom: 1px solid #ececec;
  padding: 28px 0;
}

.sports-strip-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
}

.sport-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: 999px;
  border: 1.5px solid #e8e8e8;
  font-size: 14px;
  font-weight: 700;
  color: #333;
  background: #fff;
  transition: border-color .15s, color .15s, background .15s, transform .15s;
  text-decoration: none;
}

.sport-pill:hover {
  border-color: #22c55e;
  color: #15803d;
  background: #f0fdf4;
  transform: translateY(-2px);
}

.sport-pill-icon { font-size: 18px; line-height: 1; }

/* ── Why TuCancha ────────────────────────────────── */
.why-section {
  padding: 80px 0;
  background: #f7f7f8;
}

.why-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.why-card {
  background: #fff;
  border: 1px solid #ececec;
  border-left: 4px solid #22c55e;
  border-radius: 20px;
  padding: 28px 26px;
  box-shadow: 0 2px 12px rgba(0,0,0,.03);
  transition: transform .2s, box-shadow .2s, border-left-color .2s;
  position: relative;
  overflow: hidden;
}

.why-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 36px rgba(34,197,94,.1), 0 4px 16px rgba(0,0,0,.06);
  border-left-color: #16a34a;
}

.why-card-num {
  position: absolute;
  top: 18px;
  right: 22px;
  font-size: 52px;
  font-weight: 900;
  color: rgba(0,0,0,.04);
  line-height: 1;
  letter-spacing: -0.04em;
  user-select: none;
  pointer-events: none;
}

.why-icon {
  font-size: 32px;
  display: block;
  margin-bottom: 16px;
  line-height: 1;
}

.why-card h3 {
  margin: 0 0 8px 0;
  font-size: 17px;
  font-weight: 800;
  letter-spacing: -0.01em;
}

.why-card p {
  margin: 0;
  color: #666;
  font-size: 14px;
  line-height: 1.65;
}

/* ── Split section (50/50) ───────────────────────── */
.split-section {
  padding: 80px 0;
  background: #fff;
}

.split-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 64px;
  align-items: center;
}

.split-image-wrap {
  border-radius: 28px;
  overflow: hidden;
  position: relative;
  aspect-ratio: 4/3;
  box-shadow: 0 24px 64px rgba(0,0,0,.14);
}

.split-image-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.split-image-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(15,76,42,.15) 0%, transparent 60%);
}

.split-copy-label {
  display: inline-block;
  padding: 4px 12px;
  border-radius: 999px;
  background: #dcfce7;
  font-size: 12px;
  font-weight: 700;
  color: #166534;
  text-transform: uppercase;
  letter-spacing: .07em;
  margin-bottom: 18px;
}

.split-copy h2 {
  font-size: 40px;
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.05;
  margin: 0 0 18px 0;
  color: #111;
}

.split-copy h2 em {
  font-style: normal;
  color: #16a34a;
}

.split-copy > p {
  color: #555;
  font-size: 16px;
  line-height: 1.7;
  margin: 0 0 28px 0;
}

.split-features {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.split-feature {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.split-feature-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #dcfce7;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  flex-shrink: 0;
  margin-top: 1px;
}

.split-feature-text {
  font-size: 14px;
  color: #444;
  line-height: 1.55;
  font-weight: 500;
}

/* ── Owner section ───────────────────────────────── */
.owner-section {
  position: relative;
  padding: 100px 0;
  overflow: hidden;
  background: #0a0a0a;
}

.owner-bg {
  position: absolute;
  inset: 0;
  background-image: url('/Images/admin-viendo-tableta.webp');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  opacity: .40;
}

.owner-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(10,61,31,.92) 0%, rgba(0,0,0,.80) 100%);
}

.owner-content {
  position: relative;
  z-index: 2;
}

.owner-grid {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 48px;
  align-items: center;
}

.owner-tag {
  display: inline-block;
  padding: 5px 14px;
  border-radius: 999px;
  background: rgba(110, 234, 160, .15);
  border: 1px solid rgba(110, 234, 160, .25);
  font-size: 12px;
  font-weight: 700;
  color: #6eeaa0;
  text-transform: uppercase;
  letter-spacing: .07em;
  margin-bottom: 18px;
}

.owner-content h2 {
  font-size: 44px;
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.05;
  color: #fff;
  margin: 0 0 16px 0;
  max-width: 620px;
}

.owner-content h2 em {
  font-style: normal;
  color: #6eeaa0;
}

.owner-copy p {
  color: rgba(255,255,255,.85);
  font-size: 17px;
  line-height: 1.65;
  margin: 0 0 32px 0;
  max-width: 560px;
}

.owner-stats {
  display: flex;
  gap: 32px;
  margin-bottom: 36px;
  flex-wrap: wrap;
}

.owner-stat {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.owner-stat-num {
  font-size: 30px;
  font-weight: 900;
  color: #6eeaa0;
  letter-spacing: -0.03em;
  line-height: 1;
}

.owner-stat-label {
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,.5);
  text-transform: uppercase;
  letter-spacing: .07em;
}

.owner-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.btn-owner-primary {
  padding: 14px 32px;
  background: #6eeaa0;
  color: #0a3d1f;
  border-radius: 14px;
  font-size: 15px;
  font-weight: 800;
  text-decoration: none;
  transition: background .15s, transform .15s;
  border: none;
  cursor: pointer;
  display: inline-block;
}

.btn-owner-primary:hover { background: #4dd882; transform: translateY(-2px); }

.btn-owner-ghost {
  padding: 14px 24px;
  background: rgba(255,255,255,.1);
  color: rgba(255,255,255,.9) !important;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  border: 1px solid rgba(255,255,255,.2);
  transition: background .15s, transform .15s;
  display: inline-block;
}

.btn-owner-ghost:hover { background: rgba(255,255,255,.18); transform: translateY(-2px); }

.owner-visual-card {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 24px;
  padding: 28px 26px;
  backdrop-filter: blur(8px);
  min-width: 240px;
  text-align: center;
}

.owner-visual-icon { font-size: 48px; margin-bottom: 12px; display: block; }

.owner-visual-card p {
  margin: 0;
  color: rgba(255,255,255,.8);
  font-size: 14px;
  line-height: 1.6;
  font-weight: 600;
}

/* ── FAQ ─────────────────────────────────────────── */
.faq-section {
  padding: 80px 0;
  background: #f7f7f8;
}

.faq-list { display: flex; flex-direction: column; gap: 10px; }

.faq-item {
  background: #fff;
  border: 1px solid #ececec;
  border-radius: 16px;
  overflow: hidden;
  transition: box-shadow .2s, border-color .2s;
}

.faq-item.open {
  box-shadow: 0 4px 24px rgba(34,197,94,.08);
  border-color: #bbf7d0;
}

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
  transition: transform .2s, background .2s, color .2s;
}

.faq-item.open .faq-icon {
  transform: rotate(45deg);
  background: #22c55e;
  color: #fff;
}

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
@media (max-width: 1100px) {
  .hero-grid { grid-template-columns: 1fr 380px; gap: 40px; }
  .hero-copy h1 { font-size: 52px; }
  .why-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 900px) {
  .hero-grid { grid-template-columns: 1fr; }
  .search-card { max-width: 480px; }
  .hero-copy h1 { font-size: 46px; }
  .split-grid { grid-template-columns: 1fr; gap: 40px; }
  .owner-grid { grid-template-columns: 1fr; }
  .owner-visual-card { display: none; }
  .owner-content h2 { font-size: 34px; }
}

@media (max-width: 640px) {
  .hero-content { padding: 80px 0 56px 0; }
  .hero-copy h1 { font-size: 36px; }
  .hero-copy > p { font-size: 16px; }
  .hero-stats { gap: 24px; }
  .why-grid { grid-template-columns: 1fr; }
  .split-copy h2 { font-size: 30px; }
  .owner-content h2 { font-size: 28px; }
  .owner-section { padding: 72px 0; }
  .faq-section { padding: 56px 0; }
  .why-section { padding: 56px 0; }
  .split-section { padding: 56px 0; }
}

/* ── Desactivar parallax en mobile (iOS bug) ─────── */
@media (max-width: 768px) {
  .hero-bg  { background-attachment: scroll; }
  .owner-bg { background-attachment: scroll; }
  .parallax-blob { display: none; }
}

/* ── Falta Uno section ───────────────────────────── */
.faltauno-section {
  position: relative;
  padding: 100px 0;
  overflow: hidden;
  background: #0a0a0a;
}

.faltauno-bg {
  position: absolute;
  inset: 0;
  background-image: url('/Images/jugadores-falta-uno.webp');
  background-size: cover;
  background-position: center 40%;
  background-attachment: fixed;
  opacity: .38;
}

.faltauno-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(160deg, rgba(5,32,16,.88) 0%, rgba(0,0,0,.82) 55%, rgba(5,32,16,.75) 100%);
}

.faltauno-content {
  position: relative;
  z-index: 2;
}

.faltauno-head {
  text-align: center;
  margin-bottom: 52px;
}

.faltauno-tag {
  display: inline-block;
  padding: 5px 14px;
  border-radius: 999px;
  background: rgba(110, 234, 160, .15);
  border: 1px solid rgba(110, 234, 160, .25);
  font-size: 12px;
  font-weight: 700;
  color: #6eeaa0;
  text-transform: uppercase;
  letter-spacing: .07em;
  margin-bottom: 18px;
}

.faltauno-head h2 {
  font-size: 46px;
  font-weight: 900;
  letter-spacing: -0.035em;
  line-height: 1.06;
  color: #fff;
  margin: 0 0 16px 0;
}

.faltauno-head h2 em {
  font-style: normal;
  color: #6eeaa0;
}

.faltauno-head p {
  color: rgba(255,255,255,.72);
  font-size: 17px;
  line-height: 1.65;
  max-width: 560px;
  margin: 0 auto;
}

.faltauno-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  max-width: 840px;
  margin: 0 auto 48px auto;
}

.faltauno-card {
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 24px;
  padding: 36px 32px;
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  transition: background .2s, border-color .2s, transform .2s;
}

.faltauno-card:hover {
  background: rgba(110,234,160,.08);
  border-color: rgba(110,234,160,.28);
  transform: translateY(-4px);
}

.faltauno-card-icon {
  font-size: 44px;
  display: block;
  margin-bottom: 20px;
  line-height: 1;
}

.faltauno-card h3 {
  font-size: 22px;
  font-weight: 900;
  color: #fff;
  letter-spacing: -0.02em;
  margin: 0 0 12px 0;
}

.faltauno-card p {
  font-size: 14px;
  color: rgba(255,255,255,.68);
  line-height: 1.65;
  margin: 0 0 24px 0;
}

.faltauno-card-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.faltauno-card-list-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  font-weight: 600;
  color: rgba(255,255,255,.8);
}

.faltauno-card-list-item::before {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #6eeaa0;
  flex-shrink: 0;
}

.faltauno-cta {
  text-align: center;
}

.btn-faltauno {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 16px 40px;
  background: #6eeaa0;
  color: #0a3d1f;
  border-radius: 16px;
  font-size: 16px;
  font-weight: 800;
  text-decoration: none;
  letter-spacing: -0.01em;
  transition: background .15s, transform .15s, box-shadow .15s;
  box-shadow: 0 8px 32px rgba(110,234,160,.25);
}

.btn-faltauno:hover {
  background: #4dd882;
  transform: translateY(-2px);
  box-shadow: 0 12px 40px rgba(110,234,160,.35);
}

.faltauno-cta-sub {
  margin-top: 12px;
  font-size: 13px;
  color: rgba(255,255,255,.45);
  font-weight: 500;
}

@media (max-width: 900px) {
  .faltauno-cards { grid-template-columns: 1fr; max-width: 480px; }
  .faltauno-head h2 { font-size: 34px; }
  .faltauno-bg { background-attachment: scroll; }
}

@media (max-width: 640px) {
  .faltauno-section { padding: 72px 0; }
  .faltauno-head h2 { font-size: 28px; }
  .faltauno-head p { font-size: 15px; }
  .faltauno-card { padding: 28px 24px; }
}
@endpush

@section('content')

{{-- ── Hero full-height ──────────────────────────────────────────── --}}
<section class="hero-full">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  {{-- Blobs decorativos con parallax JS --}}
  <div class="parallax-blob parallax-blob-1 parallax-slow"></div>
  <div class="parallax-blob parallax-blob-2 parallax-fast"></div>
  <div class="parallax-blob parallax-blob-3 parallax-slow"></div>
  <div class="hero-content">
    <div class="container">
      <div class="hero-grid">

        {{-- Copy izquierda --}}
        <div class="hero-copy" data-aos="fade-right" data-aos-duration="900">
          <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Plataforma de reservas deportivas
          </div>

          <h1>Reservá tu cancha<br><em>al instante.</em></h1>

          <p>
            Explorá los complejos disponibles en tu ciudad y confirmá tu turno en segundos.
            Sin llamadas, sin filas, sin complicaciones.
          </p>

          <div class="hero-actions">
            <a href="{{ route('venues.index') }}" class="btn btn-primary">Ver complejos</a>
            <a href="{{ route('planes') }}" class="btn btn-ghost">Sumá tu complejo</a>
          </div>

          <div class="hero-stats">
            <div class="hero-stat-item">
              <span class="hero-stat-num">24/7</span>
              <span class="hero-stat-label">Disponible</span>
            </div>
            <div class="hero-stat-item">
              <span class="hero-stat-num">+10</span>
              <span class="hero-stat-label">Deportes</span>
            </div>
            <div class="hero-stat-item">
              <span class="hero-stat-num">100%</span>
              <span class="hero-stat-label">Online</span>
            </div>
          </div>
        </div>

        {{-- Search card derecha --}}
        <div data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
          <div class="search-card">
            <p class="search-card-title">Busca una cancha ahora</p>

            <form method="GET" action="{{ route('venues.index') }}">
              <div class="search-field">
                <label for="qs-deporte">Deporte</label>
                <select id="qs-deporte" name="deporte">
                  <option value="">Cualquier deporte</option>
                  <option value="futbol">Futbol</option>
                  <option value="padel">Padel</option>
                  <option value="tenis">Tenis</option>
                  <option value="basquet">Basquet</option>
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

              <button type="submit" class="search-btn-full">Buscar canchas &rarr;</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

{{-- ── Franja de deportes ───────────────────────────────────────── --}}
<section class="sports-strip" data-aos="fade-up">
  <div class="container">
    <div class="sports-strip-inner">
      <a href="{{ route('venues.index', ['deporte' => 'futbol']) }}" class="sport-pill">
        <span class="sport-pill-icon">⚽</span> Futbol
      </a>
      <a href="{{ route('venues.index', ['deporte' => 'futbol5']) }}" class="sport-pill">
        <span class="sport-pill-icon">🥅</span> Futbol 5
      </a>
      <a href="{{ route('venues.index', ['deporte' => 'padel']) }}" class="sport-pill">
        <span class="sport-pill-icon">🎾</span> Padel
      </a>
      <a href="{{ route('venues.index', ['deporte' => 'tenis']) }}" class="sport-pill">
        <span class="sport-pill-icon">🏸</span> Tenis
      </a>
      <a href="{{ route('venues.index', ['deporte' => 'basquet']) }}" class="sport-pill">
        <span class="sport-pill-icon">🏀</span> Basquet
      </a>
      <a href="{{ route('venues.index') }}" class="sport-pill">
        <span class="sport-pill-icon">+</span> Ver todos
      </a>
    </div>
  </div>
</section>

{{-- ── Por que TuCancha ─────────────────────────────────────────── --}}
<section id="por-que" class="why-section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <span class="section-label">Beneficios</span>
      <h2 class="section-title">Por que TuCancha?</h2>
      <p class="section-subtitle">
        Cada detalle esta disenado para que reservar una cancha sea tan simple como mandar un mensaje.
      </p>
    </div>

    <div class="why-grid">
      <div class="why-card" data-aos="fade-up" data-aos-delay="0">
        <span class="why-card-num">01</span>
        <span class="why-icon">⚡</span>
        <h3>Reservas al instante</h3>
        <p>Elegí complejo, horario y confirmá tu turno en segundos. Sin esperas ni llamadas.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="100">
        <span class="why-card-num">02</span>
        <span class="why-icon">💳</span>
        <h3>Pago 100% seguro</h3>
        <p>Paga online con tarjeta, transferencia o efectivo via Mercado Pago. Tu reserva se confirma automaticamente.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="200">
        <span class="why-card-num">03</span>
        <span class="why-icon">📱</span>
        <h3>Desde cualquier dispositivo</h3>
        <p>Reserva desde el celular, la tablet o la computadora. La plataforma se adapta a tu pantalla.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="0">
        <span class="why-card-num">04</span>
        <span class="why-icon">🔔</span>
        <h3>Recordatorios por mail</h3>
        <p>Confirmacion inmediata y recordatorios para que no se te escape ningun turno.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="100">
        <span class="why-card-num">05</span>
        <span class="why-icon">📊</span>
        <h3>Historial de partidos</h3>
        <p>Lleva el registro de todos tus partidos: estadisticas, resultados, deporte favorito y plata gastada en un solo lugar.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="200">
        <span class="why-card-num">06</span>
        <span class="why-icon">👥</span>
        <h3>Etiqueta a tus companeros</h3>
        <p>Agregá a los jugadores del partido. Ellos pueden ver el encuentro en su historial y cargar su resultado.</p>
      </div>
    </div>
  </div>
</section>

{{-- ── Split 50/50 imagen + texto ──────────────────────────────── --}}
<section class="split-section">
  <div class="container">
    <div class="split-grid">
      <div class="split-image-wrap" data-aos="fade-right">
        <img src="/Images/amigos-post-futbol.webp" alt="Amigos después del partido" loading="lazy">
        <div class="split-image-overlay"></div>
      </div>

      <div class="split-copy" data-aos="fade-left" data-aos-delay="150">
        <span class="split-copy-label">Para jugadores</span>
        <h2>Jugar es facil.<br><em>Encontrar la cancha</em> tambien.</h2>
        <p>
          TuCancha reune los mejores complejos de tu ciudad en un solo lugar.
          Filtra por deporte, fecha y horario, y confirma tu reserva en segundos.
        </p>

        <div class="split-features">
          <div class="split-feature">
            <div class="split-feature-icon">🗓️</div>
            <span class="split-feature-text">Calendario en tiempo real — ves exactamente que horarios estan libres.</span>
          </div>
          <div class="split-feature">
            <div class="split-feature-icon">✅</div>
            <span class="split-feature-text">Confirmacion instantanea por mail en cuanto el pago se procesa.</span>
          </div>
          <div class="split-feature">
            <div class="split-feature-icon">👟</div>
            <span class="split-feature-text">Multiples deportes: futbol, padel, tenis, basquet y mas.</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── Falta Uno ─────────────────────────────────────────────────── --}}
<section class="faltauno-section">
  <div class="faltauno-bg"></div>
  <div class="faltauno-overlay"></div>
  <div class="faltauno-content">
    <div class="container">

      {{-- Encabezado centrado --}}
      <div class="faltauno-head" data-aos="fade-up">
        <span class="faltauno-tag">Falta Uno</span>
        <h2>Completa el equipo.<br><em>Hoy jugás.</em></h2>
        <p>
          ¿Te falta un jugador o querés sumarte a una partida? Falta Uno conecta
          jugadores y equipos en tiempo real para que ningun partido se cancele.
        </p>
      </div>

      {{-- 2 cards glass --}}
      <div class="faltauno-cards">

        {{-- Card izquierda: tengo cancha, me falta jugador --}}
        <div class="faltauno-card" data-aos="fade-right" data-aos-delay="100">
          <span class="faltauno-card-icon">🥅</span>
          <h3>Me falta un jugador</h3>
          <p>
            Tenes la cancha reservada pero el equipo esta incompleto. Publicá tu partida
            y encontrá el jugador que falta en minutos.
          </p>
          <div class="faltauno-card-list">
            <span class="faltauno-card-list-item">Publicá deporte, horario y posicion</span>
            <span class="faltauno-card-list-item">Recibis solicitudes de jugadores cercanos</span>
            <span class="faltauno-card-list-item">Confirmás al candidato desde la app</span>
          </div>
        </div>

        {{-- Card derecha: quiero sumarme a un partido --}}
        <div class="faltauno-card" data-aos="fade-left" data-aos-delay="200">
          <span class="faltauno-card-icon">⚡</span>
          <h3>Quiero jugar</h3>
          <p>
            Tenes ganas de jugar pero no tenes equipo o cancha. Explorá las partidas
            abiertas cerca tuyo y sumate al instante.
          </p>
          <div class="faltauno-card-list">
            <span class="faltauno-card-list-item">Filtrá por deporte, zona y horario</span>
            <span class="faltauno-card-list-item">Solicitá unirte con un toque</span>
            <span class="faltauno-card-list-item">El organizador te confirma y listo</span>
          </div>
        </div>

      </div>

      {{-- CTA centrado --}}
      <div class="faltauno-cta" data-aos="fade-up" data-aos-delay="300">
        <a href="{{ route('falta-uno.index') }}" class="btn-faltauno">
          <span>Ver partidas disponibles</span>
          <span style="font-size:18px;">→</span>
        </a>
        <p class="faltauno-cta-sub">Sin costo. Solo necesitas una cuenta en TuCancha.</p>
      </div>

    </div>
  </div>
</section>

{{-- ── Duenos / Owner CTA ───────────────────────────────────────── --}}
<section id="para-duenos" class="owner-section">
  <div class="owner-bg"></div>
  <div class="owner-overlay"></div>
  <div class="owner-content">
    <div class="container">
      <div class="owner-grid">
        <div class="owner-copy" data-aos="fade-right">
          <span class="owner-tag">Para dueños de complejos</span>
          <h2>Suma tu complejo<br>a <em>TuCancha</em></h2>
          <p>
            Recibí reservas online 24/7, cobra automaticamente y gestioná todo desde un panel simple.
            Sin complicaciones tecnicas.
          </p>

          <div class="owner-stats">
            <div class="owner-stat">
              <span class="owner-stat-num">24/7</span>
              <span class="owner-stat-label">Reservas online</span>
            </div>
            <div class="owner-stat">
              <span class="owner-stat-num">0%</span>
              <span class="owner-stat-label">Comision en plan base</span>
            </div>
            <div class="owner-stat">
              <span class="owner-stat-num">5min</span>
              <span class="owner-stat-label">Para empezar</span>
            </div>
          </div>

          <div class="owner-actions">
            <a href="{{ route('planes') }}" class="btn-owner-primary">Quiero sumarme &rarr;</a>
            <a href="{{ url('/como-funciona') }}" class="btn-owner-ghost">Ver como funciona</a>
          </div>
        </div>

        <div data-aos="zoom-in" data-aos-delay="200">
          <div class="owner-visual-card">
            <span class="owner-visual-icon">🏟️</span>
            <p>Panel de gestion<br>simple e intuitivo</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── FAQ ──────────────────────────────────────────────────────── --}}
<section id="faq" class="faq-section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <span class="section-label">Dudas frecuentes</span>
      <h2 class="section-title">Preguntas frecuentes</h2>
      <p class="section-subtitle">Todo lo que necesitas saber antes de hacer tu primera reserva.</p>
    </div>

    <div class="faq-list" data-aos="fade-up" data-aos-delay="100">
      <div class="faq-item">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-trigger-text">Como reservo una cancha?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-body">
          Crea una cuenta, explora los complejos, elegí la cancha y el horario.
          Confirmas la reserva, pagas online y recibes la confirmacion por mail en segundos.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-trigger-text">Como se que mi reserva esta confirmada?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-body">
          Una vez aprobado el pago tu reserva pasa a estado <strong>Confirmada</strong> automaticamente.
          Recibes un mail con todos los detalles: complejo, cancha, dia y horario reservado.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-trigger-text">Puedo cancelar una reserva?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-body">
          Si. Podes cancelar desde "Mis reservas" dentro del periodo de cancelacion que establece cada complejo.
          Si el pago fue procesado, se inicia el reintegro automaticamente a traves de Mercado Pago.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-trigger-text">Que metodos de pago aceptan?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-body">
          Aceptamos todos los metodos de Mercado Pago: tarjetas de credito y debito, transferencia bancaria
          y efectivo en puntos de pago. El proceso es 100% seguro.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-trigger-text">Como me registro?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-body">
          Haz clic en "Crear cuenta", completa tu nombre, mail y contrasena.
          En menos de un minuto tenes tu cuenta lista. No se requiere tarjeta para registrarse.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-trigger-text">Que es el historial de partidos?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-body">
          Es una seccion de tu perfil donde podes ver todos los partidos que jugaste: cuantos fueron,
          que deporte jugaste mas, tu complejo favorito y el total gastado. Tambien podes cargar el resultado
          de cada partido y ver estadisticas con graficos.
        </div>
      </div>

      <div class="faq-item">
        <button class="faq-trigger" onclick="toggleFaq(this)">
          <span class="faq-trigger-text">Puedo agregar a mis companeros de partido?</span>
          <span class="faq-icon">+</span>
        </button>
        <div class="faq-body">
          Si. Cuando reservas y pagas una cancha, desde el detalle de la reserva podes buscar a otros usuarios
          de TuCancha por nombre o mail y agregarlos como jugadores del partido. Ellos podran ver ese encuentro
          en su propio historial y cargar su resultado de forma independiente al tuyo.
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

  /* ── Parallax JS — solo desktop ─────────────────── */
  (function () {
    if (window.innerWidth <= 768) return;

    var slowEls = document.querySelectorAll('.parallax-slow');
    var fastEls = document.querySelectorAll('.parallax-fast');

    function onScroll() {
      var scrollY = window.scrollY;
      slowEls.forEach(function (el) {
        el.style.transform = 'translateY(' + (scrollY * 0.3) + 'px)';
      });
      fastEls.forEach(function (el) {
        el.style.transform = 'translateY(' + (scrollY * 0.6) + 'px)';
      });
    }

    window.addEventListener('scroll', onScroll, { passive: true });
  })();
</script>
@endpush
