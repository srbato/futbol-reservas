@extends('layouts.app')

@section('title', 'Mis favoritos — TuCancha')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<style>
  /* ── Reset AOS pointer-events ── */
  [data-aos] { pointer-events: auto !important; }

  /* ── Hero ── */
  .fav-hero {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    min-height: 280px;
    display: flex;
    align-items: flex-end;
    margin-bottom: 32px;
    color: #fff;
  }

  .fav-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/Images/ambiente-cancha-noche.webp');
    background-size: cover;
    background-position: center 40%;
    z-index: 0;
  }

  .fav-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
      to top,
      rgba(0,0,0,.85) 0%,
      rgba(0,0,0,.55) 55%,
      rgba(0,0,0,.20) 100%
    );
    z-index: 1;
  }

  .fav-hero-content {
    position: relative;
    z-index: 2;
    padding: 36px 40px;
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
  }

  .fav-hero-title {
    margin: 0 0 6px 0;
    font-size: 42px;
    font-weight: 900;
    letter-spacing: -0.03em;
    line-height: 1;
  }

  .fav-hero-subtitle {
    margin: 0;
    font-size: 15px;
    color: rgba(255,255,255,.65);
    font-weight: 400;
  }

  .fav-count-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(22,163,74,.18);
    border: 1px solid rgba(22,163,74,.4);
    color: #86efac;
    font-size: 13px;
    font-weight: 700;
    padding: 6px 14px;
    border-radius: 999px;
    margin-bottom: 12px;
    letter-spacing: .03em;
    text-transform: uppercase;
  }

  .fav-count-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #22c55e;
    flex-shrink: 0;
    animation: fav-pulse 1.8s ease-in-out infinite;
  }

  @keyframes fav-pulse {
    0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(34,197,94,.5); }
    50%       { opacity: .8; transform: scale(1.15); box-shadow: 0 0 0 5px rgba(34,197,94,.0); }
  }

  @media (max-width: 600px) {
    .fav-hero { min-height: 220px; }
    .fav-hero-title { font-size: 30px; }
    .fav-hero-content { padding: 24px 20px; }
  }

  /* ── Grid de cards ── */
  .fav-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
  }

  @media (max-width: 1024px) {
    .fav-grid { grid-template-columns: repeat(2, 1fr); }
  }

  @media (max-width: 640px) {
    .fav-grid { grid-template-columns: 1fr; }
  }

  /* ── Card ── */
  .fav-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
  }

  .fav-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,.10);
    border-color: #cbd5e1;
  }

  .fav-card-img-wrap {
    position: relative;
    width: 100%;
    height: 190px;
    overflow: hidden;
    background: #f1f5f9;
    flex-shrink: 0;
  }

  .fav-card-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .35s ease;
  }

  .fav-card:hover .fav-card-img-wrap img {
    transform: scale(1.04);
  }

  /* Fallback sin imagen */
  .fav-card-img-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #94a3b8;
    font-size: 13px;
    font-weight: 500;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  }

  .fav-card-img-fallback svg {
    opacity: .4;
  }

  /* Badge de zona */
  .fav-zone-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(6px);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 999px;
    letter-spacing: .04em;
    text-transform: uppercase;
    border: 1px solid rgba(255,255,255,.15);
  }

  /* Botón quitar (flotante sobre imagen) */
  .fav-remove-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: rgba(255,255,255,.92);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ef4444;
    transition: background .18s, transform .18s, color .18s;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
    padding: 0;
  }

  .fav-remove-btn:hover {
    background: #fef2f2;
    transform: scale(1.1);
    color: #dc2626;
  }

  /* Cuerpo de la card */
  .fav-card-body {
    padding: 18px 20px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
  }

  .fav-card-name {
    margin: 0;
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -0.01em;
    line-height: 1.25;
  }

  .fav-card-address {
    display: flex;
    align-items: flex-start;
    gap: 5px;
    font-size: 13px;
    color: #64748b;
    line-height: 1.45;
    margin: 0;
  }

  .fav-card-address svg {
    flex-shrink: 0;
    margin-top: 1px;
  }

  /* Rating */
  .fav-rating-row {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .fav-stars {
    color: #f59e0b;
    font-size: 14px;
    letter-spacing: 1px;
  }

  .fav-rating-val {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
  }

  .fav-rating-count {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 400;
  }

  .fav-no-reviews {
    font-size: 12px;
    color: #94a3b8;
    font-style: italic;
  }

  /* Divisor */
  .fav-card-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 2px 0;
  }

  /* Acciones */
  .fav-card-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
  }

  .fav-btn-primary {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #16a34a;
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    padding: 10px 16px;
    border-radius: 12px;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background .18s, transform .14s;
    white-space: nowrap;
  }

  .fav-btn-primary:hover {
    background: #15803d;
    transform: translateY(-1px);
    color: #fff;
    text-decoration: none;
  }

  .fav-btn-ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    background: #fff;
    color: #ef4444;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid #fecaca;
    cursor: pointer;
    transition: background .18s, border-color .18s, transform .14s;
    white-space: nowrap;
  }

  .fav-btn-ghost:hover {
    background: #fef2f2;
    border-color: #fca5a5;
    transform: translateY(-1px);
  }

  /* ── Estado vacío ── */
  .fav-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 64px 24px 80px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
  }

  .fav-empty-img {
    width: 280px;
    max-width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 16px;
    margin-bottom: 28px;
    opacity: .9;
    box-shadow: 0 8px 28px rgba(0,0,0,.10);
  }

  .fav-empty-title {
    margin: 0 0 10px 0;
    font-size: 26px;
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -0.02em;
  }

  .fav-empty-sub {
    margin: 0 0 28px 0;
    font-size: 15px;
    color: #64748b;
    max-width: 360px;
    line-height: 1.6;
  }

  .fav-empty-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #16a34a;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    padding: 13px 28px;
    border-radius: 14px;
    text-decoration: none;
    transition: background .18s, transform .14s;
  }

  .fav-empty-cta:hover {
    background: #15803d;
    transform: translateY(-2px);
    color: #fff;
    text-decoration: none;
  }
</style>
@endpush

@section('content')

{{-- ── Hero ── --}}
<div class="fav-hero" data-aos="fade-up" data-aos-duration="600">
  <div class="fav-hero-bg"></div>
  <div class="fav-hero-overlay"></div>
  <div class="fav-hero-content">
    <div>
      @if(!$venues->isEmpty())
        <div class="fav-count-badge">
          <span class="fav-count-dot"></span>
          {{ $venues->count() }} {{ $venues->count() === 1 ? 'complejo guardado' : 'complejos guardados' }}
        </div>
      @endif
      <h1 class="fav-hero-title">Mis favoritos</h1>
      <p class="fav-hero-subtitle">Tus complejos preferidos, siempre a mano.</p>
    </div>
    <a href="{{ route('venues.index') }}" class="fav-btn-primary" style="flex: 0 0 auto;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
      Explorar complejos
    </a>
  </div>
</div>

{{-- ── Estado vacío ── --}}
@if($venues->isEmpty())
  <div class="fav-empty" data-aos="fade-up" data-aos-duration="600" data-aos-delay="100">
    <img src="/Images/complejo-vacio.webp" alt="Sin favoritos" class="fav-empty-img">
    <h2 class="fav-empty-title">Todavía no tenés favoritos</h2>
    <p class="fav-empty-sub">
      Explorá los complejos disponibles y guardá los que más te gusten tocando el corazón. Los vas a encontrar acá, siempre listos para reservar.
    </p>
    <a href="{{ route('venues.index') }}" class="fav-empty-cta">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
      </svg>
      Ver complejos
    </a>
  </div>

{{-- ── Grid de favoritos ── --}}
@else
  <div class="fav-grid">
    @foreach ($venues as $index => $venue)
      <article
        class="fav-card"
        data-aos="fade-up"
        data-aos-duration="550"
        data-aos-delay="{{ min($index * 80, 400) }}"
      >
        {{-- Imagen --}}
        <div class="fav-card-img-wrap">
          @if($venue->cover_image_path)
            <img
              src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
              alt="{{ $venue->name }}"
              loading="lazy"
            >
          @else
            <div class="fav-card-img-fallback">
              <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                <polyline points="21 15 16 10 5 21"/>
              </svg>
              Sin imagen
            </div>
          @endif

          {{-- Badge zona --}}
          @if($venue->zone)
            <span class="fav-zone-badge">{{ $venue->zone }}</span>
          @endif

          {{-- Botón quitar (flotante) --}}
          <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
            @csrf
            <button
              type="submit"
              class="fav-remove-btn"
              title="Quitar de favoritos"
              onclick="return confirm('¿Querés quitar este complejo de tus favoritos?')"
            >
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
              </svg>
            </button>
          </form>
        </div>

        {{-- Cuerpo --}}
        <div class="fav-card-body">
          <h3 class="fav-card-name">{{ $venue->name }}</h3>

          @if($venue->address)
            <p class="fav-card-address">
              <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
              </svg>
              {{ $venue->address }}
            </p>
          @endif

          {{-- Rating --}}
          @if($venue->reviews_count > 0)
            <div class="fav-rating-row">
              @php $rounded = round($venue->reviews_avg_rating); @endphp
              <span class="fav-stars">
                @for($i = 1; $i <= 5; $i++)
                  {{ $i <= $rounded ? '★' : '☆' }}
                @endfor
              </span>
              <span class="fav-rating-val">{{ number_format($venue->reviews_avg_rating, 1) }}</span>
              <span class="fav-rating-count">({{ $venue->reviews_count }} {{ $venue->reviews_count === 1 ? 'reseña' : 'reseñas' }})</span>
            </div>
          @else
            <span class="fav-no-reviews">Sin reseñas todavía</span>
          @endif

          <div class="fav-card-divider"></div>

          {{-- Acciones --}}
          <div class="fav-card-actions">
            <a href="{{ route('venues.show', $venue) }}" class="fav-btn-primary">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
              </svg>
              Ver complejo
            </a>

            <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
              @csrf
              <button
                type="submit"
                class="fav-btn-ghost"
                onclick="return confirm('¿Querés quitar este complejo de tus favoritos?')"
              >
                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" stroke="none">
                  <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
                Quitar
              </button>
            </form>
          </div>
        </div>
      </article>
    @endforeach
  </div>
@endif

@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({
    once: true,
    offset: 60,
    easing: 'ease-out-cubic',
  });
</script>
@endpush
