@extends('layouts.app')

@section('title','Encontrá tu cancha — TuCancha')

@push('styles')
  /* ── Reset / base ─────────────────────────────── */
  .vi-wrap { display: flex; flex-direction: column; gap: 0; }

  /* ── Hero ─────────────────────────────────────── */
  .vi-hero {
    background: linear-gradient(135deg, #0f0f0f 0%, #1a1a2e 60%, #16213e 100%);
    border-radius: 24px;
    padding: 52px 48px 40px;
    color: #fff;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
  }

  .vi-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      radial-gradient(ellipse 55% 70% at 10% 60%, rgba(74,222,128,.07) 0%, transparent 65%),
      radial-gradient(ellipse 45% 50% at 85% 20%, rgba(99,102,241,.09) 0%, transparent 65%);
    pointer-events: none;
  }

  .vi-hero-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    flex-wrap: wrap;
    margin-bottom: 36px;
    position: relative;
  }

  .vi-hero-text h1 {
    margin: 0 0 10px 0;
    font-size: 52px;
    font-weight: 800;
    letter-spacing: -0.04em;
    line-height: 1.04;
  }

  .vi-hero-text h1 em {
    font-style: normal;
    color: #4ade80;
  }

  .vi-hero-text p {
    margin: 0;
    color: rgba(255,255,255,.65);
    font-size: 17px;
    line-height: 1.6;
    max-width: 480px;
  }

  .vi-hero-stat {
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 20px;
    padding: 20px 28px;
    text-align: center;
    flex-shrink: 0;
    backdrop-filter: blur(6px);
  }

  .vi-hero-stat-num {
    font-size: 48px;
    font-weight: 800;
    letter-spacing: -0.04em;
    line-height: 1;
    color: #4ade80;
  }

  .vi-hero-stat-label {
    font-size: 13px;
    color: rgba(255,255,255,.6);
    margin-top: 4px;
    font-weight: 600;
  }

  /* ── Search bar ───────────────────────────────── */
  .vi-search-bar {
    position: relative;
    display: flex;
    align-items: center;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 16px;
    padding: 6px 6px 6px 18px;
    gap: 8px;
    backdrop-filter: blur(8px);
    transition: border-color .2s, background .2s;
  }

  .vi-search-bar:focus-within {
    border-color: rgba(74,222,128,.5);
    background: rgba(255,255,255,.11);
  }

  .vi-search-bar input {
    flex: 1;
    background: none;
    border: none;
    outline: none;
    color: #fff;
    font-size: 16px;
    font-family: inherit;
    min-width: 0;
  }

  .vi-search-bar input::placeholder { color: rgba(255,255,255,.45); }

  .vi-search-btn {
    background: #4ade80;
    color: #052e16;
    border: none;
    border-radius: 12px;
    padding: 11px 22px;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    font-family: inherit;
    white-space: nowrap;
    transition: background .15s, transform .15s;
  }

  .vi-search-btn:hover { background: #22c55e; transform: translateY(-1px); }

  /* ── Quick filters row ────────────────────────── */
  .vi-filters-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    margin-top: 14px;
    position: relative;
  }

  .vi-filter-chip {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.14);
    color: rgba(255,255,255,.8);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, border-color .15s;
    position: relative;
  }

  .vi-filter-chip select,
  .vi-filter-chip input {
    position: absolute;
    inset: 0;
    opacity: 0;
    width: 100%;
    cursor: pointer;
  }

  .vi-filter-chip:hover,
  .vi-filter-chip:focus-within {
    background: rgba(255,255,255,.13);
    border-color: rgba(255,255,255,.25);
  }

  .vi-filter-chip.active {
    background: rgba(74,222,128,.15);
    border-color: rgba(74,222,128,.4);
    color: #4ade80;
  }

  .vi-filter-sep {
    width: 1px;
    height: 20px;
    background: rgba(255,255,255,.15);
  }

  .vi-clear-btn {
    background: none;
    border: none;
    color: rgba(255,255,255,.5);
    font-size: 13px;
    cursor: pointer;
    font-family: inherit;
    padding: 7px 10px;
    border-radius: 999px;
    transition: color .15s;
  }

  .vi-clear-btn:hover { color: rgba(255,255,255,.85); }

  /* Advanced filter panel */
  .vi-adv-panel {
    display: none;
    margin-top: 12px;
    background: rgba(255,255,255,.06);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 16px;
    padding: 18px 20px;
    position: relative;
  }

  .vi-adv-panel.open { display: block; }

  .vi-adv-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 12px;
  }

  .vi-adv-field label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: rgba(255,255,255,.5);
    margin-bottom: 6px;
  }

  .vi-adv-field input,
  .vi-adv-field select {
    width: 100%;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 10px;
    padding: 9px 12px;
    color: #fff;
    font-size: 14px;
    font-family: inherit;
    outline: none;
    transition: border-color .15s;
  }

  .vi-adv-field input:focus,
  .vi-adv-field select:focus { border-color: rgba(74,222,128,.5); }

  .vi-adv-field select option { background: #111; color: #fff; }

  .vi-adv-actions {
    display: flex;
    gap: 8px;
    margin-top: 14px;
  }

  /* ── Active filters summary ───────────────────── */
  .vi-active-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 20px;
  }

  .vi-active-filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    background: #111;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid #333;
  }

  /* ── Featured section ─────────────────────────── */
  .vi-featured {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 24px;
    padding: 24px 24px 20px;
    margin-bottom: 28px;
    box-shadow: 0 2px 16px rgba(0,0,0,.04);
  }

  .vi-featured-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 18px;
  }

  .vi-featured-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.02em;
  }

  .vi-featured-header .carousel-subtitle {
    font-size: 13px;
    color: #888;
    margin-top: 3px;
  }

  .feature-tabs {
    display: flex;
    background: #f3f3f3;
    border-radius: 999px;
    padding: 3px;
    gap: 2px;
  }

  .feature-tab {
    padding: 7px 18px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    color: #888;
    cursor: pointer;
    transition: background .15s, color .15s;
    white-space: nowrap;
    user-select: none;
  }

  .feature-tab.active {
    background: #111;
    color: #fff;
  }

  .featured-nav-arrows {
    display: flex;
    gap: 6px;
  }

  .featured-nav-arrow {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: 1px solid #e0e0e0;
    background: #fff;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s, border-color .15s;
    color: #333;
  }

  .featured-nav-arrow:hover {
    background: #111;
    border-color: #111;
    color: #fff;
  }

  .feature-carousel { display: none; }
  .feature-carousel.active { display: block; }

  .feature-carousel-shell {
    overflow: hidden;
    border-radius: 16px;
  }

  .carousel-track {
    display: flex;
    gap: 14px;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
    cursor: grab;
  }

  .carousel-track::-webkit-scrollbar { display: none; }
  .carousel-track.dragging { cursor: grabbing; }

  /* Featured card — tall with image overlay */
  .featured-card {
    flex: 0 0 260px;
    scroll-snap-align: start;
    border-radius: 18px;
    overflow: hidden;
    position: relative;
    height: 220px;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,.1);
    transition: transform .2s, box-shadow .2s;
  }

  .featured-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,.15);
  }

  .featured-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    inset: 0;
  }

  .featured-card-placeholder {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #1a1a2e, #2d2d44);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
  }

  .featured-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.85) 0%, rgba(0,0,0,.2) 55%, transparent 100%);
  }

  .featured-card-body {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 16px;
    color: #fff;
  }

  .featured-card-body h3 {
    margin: 0 0 6px 0;
    font-size: 15px;
    font-weight: 800;
    line-height: 1.3;
    text-shadow: 0 1px 4px rgba(0,0,0,.4);
  }

  .featured-card-meta {
    font-size: 12px;
    color: rgba(255,255,255,.8);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
  }

  .featured-card-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.2);
    font-size: 11px;
    font-weight: 700;
  }

  .featured-card-btn {
    display: inline-block;
    padding: 7px 14px;
    border-radius: 10px;
    background: #4ade80;
    color: #052e16;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    transition: background .15s;
  }

  .featured-card-btn:hover { background: #22c55e; }

  /* ── Favorites ────────────────────────────────── */
  .vi-favorites {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 24px;
    padding: 20px 24px;
    margin-bottom: 28px;
    box-shadow: 0 2px 16px rgba(0,0,0,.04);
  }

  .vi-favorites h2 {
    margin: 0 0 14px 0;
    font-size: 17px;
    font-weight: 800;
  }

  .vi-fav-scroll {
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
  }

  .vi-fav-scroll::-webkit-scrollbar { display: none; }

  .vi-fav-chip {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 999px;
    background: #f3f3f3;
    border: 1px solid #e8e8e8;
    font-size: 13px;
    font-weight: 700;
    color: #111;
    text-decoration: none;
    transition: background .15s, border-color .15s;
  }

  .vi-fav-chip:hover {
    background: #111;
    color: #fff;
    border-color: #111;
  }

  /* ── Map ──────────────────────────────────────── */
  .vi-map-wrap {
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid #e8e8e8;
    margin-bottom: 28px;
    box-shadow: 0 2px 16px rgba(0,0,0,.04);
    position: relative;
  }

  .vi-map-label {
    position: absolute;
    top: 14px;
    left: 14px;
    z-index: 10;
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 12px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 700;
    color: #111;
    box-shadow: 0 2px 10px rgba(0,0,0,.08);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  /* ── Results header ───────────────────────────── */
  .vi-results-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 18px;
  }

  .vi-results-header h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -0.02em;
  }

  .vi-results-count {
    font-size: 14px;
    color: #888;
    font-weight: 600;
  }

  /* ── Venue cards ──────────────────────────────── */
  .vi-venues-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 18px;
    margin-bottom: 32px;
  }

  .vi-venue-card {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 22px;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    display: flex;
    flex-direction: column;
  }

  .vi-venue-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(0,0,0,.1);
  }

  .vi-venue-img-wrap {
    position: relative;
    height: 190px;
    overflow: hidden;
    flex-shrink: 0;
  }

  .vi-venue-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
  }

  .vi-venue-card:hover .vi-venue-img-wrap img {
    transform: scale(1.04);
  }

  .vi-venue-img-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #1a1a2e 0%, #2d2d44 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
  }

  .vi-venue-fav-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(4px);
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,.15);
    transition: transform .15s, background .15s;
    line-height: 1;
  }

  .vi-venue-fav-btn:hover { transform: scale(1.15); background: #fff; }
  .vi-venue-fav-btn.saved { background: #fee2e2; }

  .vi-venue-sport-badge {
    position: absolute;
    bottom: 12px;
    left: 12px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(0,0,0,.6);
    backdrop-filter: blur(4px);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border: 1px solid rgba(255,255,255,.15);
  }

  .vi-venue-body {
    padding: 18px 20px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .vi-venue-name {
    margin: 0;
    font-size: 17px;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.3;
  }

  .vi-venue-rating {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .vi-venue-stars {
    color: #f59e0b;
    font-size: 13px;
    letter-spacing: 1px;
  }

  .vi-venue-rating-text {
    font-size: 13px;
    font-weight: 700;
    color: #111;
  }

  .vi-venue-rating-count {
    font-size: 12px;
    color: #999;
  }

  .vi-venue-meta {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  .vi-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    background: #f3f3f3;
    border: 1px solid #e8e8e8;
    font-size: 12px;
    font-weight: 600;
    color: #555;
  }

  .vi-venue-desc {
    font-size: 13px;
    color: #666;
    line-height: 1.55;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .vi-venue-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
    padding-top: 4px;
  }

  .vi-btn-primary {
    flex: 1;
    padding: 10px 16px;
    border-radius: 12px;
    background: #111;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    border: none;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    transition: background .15s;
    display: inline-block;
  }

  .vi-btn-primary:hover { background: #222; }

  /* ── Empty state ──────────────────────────────── */
  .vi-empty {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 24px;
    padding: 56px 32px;
    text-align: center;
  }

  .vi-empty-icon { font-size: 48px; margin-bottom: 16px; }

  .vi-empty h3 {
    margin: 0 0 8px 0;
    font-size: 20px;
    font-weight: 800;
  }

  .vi-empty p {
    margin: 0;
    color: #666;
    font-size: 15px;
  }

  /* ── Search results panel ─────────────────────── */
  .vi-search-results-panel {
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 24px;
    padding: 28px 28px 32px;
    margin-bottom: 28px;
  }

  .vi-search-results-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
  }

  .vi-search-results-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.02em;
  }

  .vi-search-results-count {
    font-size: 13px;
    color: #888;
    font-weight: 600;
    background: #f3f3f3;
    padding: 4px 12px;
    border-radius: 999px;
  }

  /* ── Responsive ───────────────────────────────── */
  @media (max-width: 900px) {
    .vi-hero { padding: 36px 24px 28px; }
    .vi-hero-text h1 { font-size: 36px; }
    .vi-hero-top { flex-direction: column; gap: 16px; }
    .vi-hero-stat { align-self: flex-start; }
    .feature-tabs { display: none; }
  }

  @media (max-width: 640px) {
    .vi-hero-text h1 { font-size: 28px; }
    .vi-venues-grid { grid-template-columns: 1fr; }
    .vi-adv-grid { grid-template-columns: 1fr 1fr; }
  }
@endpush

@section('content')
<div class="vi-wrap">

  {{-- ── HERO + SEARCH ─────────────────────────────────────────────────── --}}
  <form method="GET" action="{{ route('venues.index') }}" id="venueSearchForm">
    <div class="vi-hero">
      <div class="vi-hero-top">
        <div class="vi-hero-text">
          <h1>Encontrá tu<br>cancha <em>perfecta</em></h1>
          <p>Filtrá por zona, deporte, precio y fecha. Reservá online en segundos.</p>
        </div>
        <div class="vi-hero-stat">
          <div class="vi-hero-stat-num">{{ $allVenues->count() }}</div>
          <div class="vi-hero-stat-label">complejos activos</div>
        </div>
      </div>

      {{-- Search bar --}}
      <div class="vi-search-bar">
        <span style="font-size:18px; flex-shrink:0;">🔍</span>
        <input
          type="text"
          name="q"
          value="{{ $q ?? '' }}"
          placeholder="Buscá por nombre, zona o descripción..."
          autocomplete="off"
        >
        <button type="submit" class="vi-search-btn">Buscar</button>
      </div>

      {{-- Quick filter chips --}}
      <div class="vi-filters-row">

        {{-- Zona --}}
        <label class="vi-filter-chip {{ ($zone ?? '') ? 'active' : '' }}">
          📍 {{ ($zone ?? '') ?: 'Zona' }}
          <select name="zone" onchange="document.getElementById('venueSearchForm').submit()">
            <option value="">Todas las zonas</option>
            @foreach($zones as $z)
              <option value="{{ $z }}" {{ ($zone ?? '') === $z ? 'selected' : '' }}>{{ $z }}</option>
            @endforeach
          </select>
        </label>

        {{-- Deporte --}}
        <label class="vi-filter-chip {{ ($sport ?? '') ? 'active' : '' }}">
          ⚽ {{ match($sport ?? '') { 'football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis', 'basketball' => 'Básquet', 'volleyball' => 'Vóley', default => 'Deporte' } }}
          <select name="sport" onchange="document.getElementById('venueSearchForm').submit()">
            <option value="">Todos los deportes</option>
            <option value="football" {{ ($sport ?? '') === 'football' ? 'selected' : '' }}>⚽ Fútbol</option>
            <option value="padel" {{ ($sport ?? '') === 'padel' ? 'selected' : '' }}>🏓 Pádel</option>
            <option value="tennis" {{ ($sport ?? '') === 'tennis' ? 'selected' : '' }}>🎾 Tenis</option>
            <option value="basketball" {{ ($sport ?? '') === 'basketball' ? 'selected' : '' }}>🏀 Básquet</option>
            <option value="volleyball" {{ ($sport ?? '') === 'volleyball' ? 'selected' : '' }}>🏐 Vóley</option>
          </select>
        </label>

        {{-- Fecha --}}
        <label class="vi-filter-chip {{ ($date ?? '') ? 'active' : '' }}" onclick="event.preventDefault(); document.getElementById('dateFilterInput').showPicker()">
          📅 {{ ($date ?? '') ? \Carbon\Carbon::parse($date)->format('d/m') : 'Fecha' }}
          <input
            id="dateFilterInput"
            type="date"
            name="date"
            value="{{ $date ?? '' }}"
            min="{{ date('Y-m-d') }}"
            onchange="document.getElementById('venueSearchForm').submit()"
          >
        </label>

        <div class="vi-filter-sep"></div>

        {{-- Más filtros --}}
        <button type="button" class="vi-filter-chip" id="advToggleBtn" onclick="toggleAdv()">
          ⚙️ Más filtros
          @if(($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? ''))
            <span style="background:#4ade80; color:#052e16; border-radius:999px; width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center; font-size:10px;">●</span>
          @endif
        </button>

        @if(($q ?? '') || ($zone ?? '') || ($sport ?? '') || ($date ?? '') || ($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? ''))
          <a href="{{ route('venues.index') }}" class="vi-clear-btn">✕ Limpiar filtros</a>
        @endif
      </div>

      {{-- Advanced filter panel --}}
      <div class="vi-adv-panel {{ (($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? '')) ? 'open' : '' }}" id="advPanel">
        <div class="vi-adv-grid">
          <div class="vi-adv-field">
            <label>Precio mínimo (ARS)</label>
            <input type="number" name="min_price" min="0" step="1" value="{{ $minPrice ?? '' }}" placeholder="Ej: 5000">
          </div>
          <div class="vi-adv-field">
            <label>Precio máximo (ARS)</label>
            <input type="number" name="max_price" min="0" step="1" value="{{ $maxPrice ?? '' }}" placeholder="Ej: 20000">
          </div>
          <div class="vi-adv-field">
            <label>Horario disponible</label>
            <input type="time" name="available_at" value="{{ $availableAt ?? '' }}">
          </div>
        </div>
        <div class="vi-adv-actions">
          <button type="submit" style="padding:9px 20px; background:#4ade80; color:#052e16; border:none; border-radius:10px; font-size:13px; font-weight:800; cursor:pointer; font-family:inherit;">
            Aplicar filtros
          </button>
        </div>
      </div>
    </div>
  </form>

  {{-- ── ACTIVE FILTER TAGS ─────────────────────────────────────────────── --}}
  @if(($q ?? '') || ($zone ?? '') || ($sport ?? '') || ($date ?? '') || ($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? ''))
    <div class="vi-active-filters">
      <span style="font-size:13px; color:#666; font-weight:600;">Filtros activos:</span>
      @if($q ?? '')
        <span class="vi-active-filter-tag">🔍 "{{ $q }}"</span>
      @endif
      @if($zone ?? '')
        <span class="vi-active-filter-tag">📍 {{ $zone }}</span>
      @endif
      @if($sport ?? '')
        <span class="vi-active-filter-tag">⚽ {{ match($sport) { 'football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis', 'basketball' => 'Básquet', 'volleyball' => 'Vóley', default => $sport } }}</span>
      @endif
      @if($date ?? '')
        <span class="vi-active-filter-tag">📅 {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
      @endif
      @if($minPrice ?? '')
        <span class="vi-active-filter-tag">💰 Desde ${{ number_format($minPrice, 0, ',', '.') }}</span>
      @endif
      @if($maxPrice ?? '')
        <span class="vi-active-filter-tag">💰 Hasta ${{ number_format($maxPrice, 0, ',', '.') }}</span>
      @endif
      @if($availableAt ?? '')
        <span class="vi-active-filter-tag">🕐 {{ $availableAt }}</span>
      @endif
    </div>
  @endif

  {{-- ── SEARCH RESULTS PANEL (solo cuando hay filtros activos) ─────────── --}}
  @if($hasFilters)
    <div class="vi-search-results-panel">
      <div class="vi-search-results-header">
        <h2>🔍 Resultados de búsqueda</h2>
        <span class="vi-search-results-count">{{ $venues->count() }} resultado{{ $venues->count() !== 1 ? 's' : '' }}</span>
      </div>

      @if($venues->isEmpty())
        <div class="vi-empty">
          <div class="vi-empty-icon">🔍</div>
          <h3>Sin resultados</h3>
          <p>No encontramos complejos con esos filtros. Probá ajustando la búsqueda.</p>
        </div>
      @else
        <div class="vi-venues-grid">
          @foreach($venues as $venue)
            <article class="vi-venue-card">
              <div class="vi-venue-img-wrap">
                @if($venue->cover_image_path)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
                @else
                  <div class="vi-venue-img-placeholder">⚽</div>
                @endif
                @auth
                  @if(in_array($venue->id, $favoriteVenueIds ?? []))
                    <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
                      @csrf
                      <button type="submit" class="vi-venue-fav-btn saved" title="Quitar de favoritos">❤️</button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('venues.favorite', $venue) }}" style="margin:0;">
                      @csrf
                      <button type="submit" class="vi-venue-fav-btn" title="Guardar en favoritos">🤍</button>
                    </form>
                  @endif
                @endauth
                @if($venue->zone)
                  <div class="vi-venue-sport-badge">📍 {{ $venue->zone }}</div>
                @endif
              </div>
              <div class="vi-venue-body">
                <h3 class="vi-venue-name">{{ $venue->name }}</h3>
                @if($venue->reviews_count > 0)
                  <div class="vi-venue-rating">
                    @php $rounded = round($venue->reviews_avg_rating); @endphp
                    <span class="vi-venue-stars">
                      @for($i = 1; $i <= 5; $i++){{ $i <= $rounded ? '★' : '☆' }}@endfor
                    </span>
                    <span class="vi-venue-rating-text">{{ number_format($venue->reviews_avg_rating, 1) }}</span>
                    <span class="vi-venue-rating-count">({{ $venue->reviews_count }} reseña{{ $venue->reviews_count > 1 ? 's' : '' }})</span>
                  </div>
                @else
                  <div style="font-size:13px; color:#aaa;">Sin reseñas todavía</div>
                @endif
                <p class="vi-venue-desc">
                  {{ $venue->description ?? 'Reservá online y encontrá disponibilidad en pocos pasos.' }}
                </p>
                <div class="vi-venue-actions">
                  <a href="{{ route('venues.show', $venue) }}" class="vi-btn-primary">Ver complejo →</a>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </div>
  @endif

  {{-- ── FEATURED CAROUSEL ──────────────────────────────────────────────── --}}
  <div class="vi-featured">
    <div class="vi-featured-header">
      <div>
        <h2>Destacados</h2>
        <div class="carousel-subtitle">Los complejos más activos, con descuentos y mejor valorados.</div>
      </div>
      <div style="display:flex; align-items:center; gap:10px;">
        <div class="feature-tabs">
          <div class="feature-tab active" data-tab="top">🔥 Más reservados</div>
          <div class="feature-tab" data-tab="discounts">💸 Descuentos</div>
          <div class="feature-tab" data-tab="rated">⭐ Mejor valorados</div>
        </div>
        <div class="featured-nav-arrows">
          <button type="button" class="featured-nav-arrow" data-carousel-move="prev" aria-label="Anterior">&#8249;</button>
          <button type="button" class="featured-nav-arrow" data-carousel-move="next" aria-label="Siguiente">&#8250;</button>
        </div>
      </div>
    </div>

    {{-- Tab: Más reservados --}}
    <div class="feature-carousel active" id="tab-top">
      <div class="feature-carousel-shell">
        <div class="carousel-track featured-track" data-carousel-track>
          @forelse($topReservedVenues as $venue)
            <article class="featured-card">
              @if($venue->cover_image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
              @else
                <div class="featured-card-placeholder">⚽</div>
              @endif
              <div class="featured-card-overlay"></div>
              <div class="featured-card-body">
                <h3>{{ $venue->name }}</h3>
                <div class="featured-card-meta">
                  @if($venue->zone)
                    <span class="featured-card-badge">📍 {{ $venue->zone }}</span>
                  @endif
                  <span>🔥 {{ $venue->weekly_reservations_count }} esta semana</span>
                </div>
                <a href="{{ route('venues.show', $venue) }}" class="featured-card-btn">Ver complejo →</a>
              </div>
            </article>
          @empty
            <div style="padding:24px; color:#888; font-size:14px;">No hay datos esta semana todavía.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Tab: Descuentos --}}
    <div class="feature-carousel" id="tab-discounts">
      <div class="feature-carousel-shell">
        <div class="carousel-track featured-track" data-carousel-track>
          @forelse($discountedVenues as $venue)
            <article class="featured-card">
              @if($venue->cover_image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
              @else
                <div class="featured-card-placeholder">💸</div>
              @endif
              <div class="featured-card-overlay"></div>
              <div class="featured-card-body">
                <h3>{{ $venue->name }}</h3>
                <div class="featured-card-meta">
                  @if($venue->zone)
                    <span class="featured-card-badge">📍 {{ $venue->zone }}</span>
                  @endif
                  <span>💸 Descuentos activos</span>
                </div>
                <a href="{{ route('venues.show', $venue) }}" class="featured-card-btn">Ver complejo →</a>
              </div>
            </article>
          @empty
            <div style="padding:24px; color:#888; font-size:14px;">No hay complejos con descuentos activos.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Tab: Mejor valorados --}}
    <div class="feature-carousel" id="tab-rated">
      <div class="feature-carousel-shell">
        <div class="carousel-track featured-track" data-carousel-track>
          @forelse($bestRatedVenues as $venue)
            <article class="featured-card">
              @if($venue->cover_image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
              @else
                <div class="featured-card-placeholder">⭐</div>
              @endif
              <div class="featured-card-overlay"></div>
              <div class="featured-card-body">
                <h3>{{ $venue->name }}</h3>
                <div class="featured-card-meta">
                  @if($venue->zone)
                    <span class="featured-card-badge">📍 {{ $venue->zone }}</span>
                  @endif
                  <span>⭐ {{ number_format($venue->reviews_avg_rating, 1) }} / 5 ({{ $venue->reviews_count }} reseña{{ $venue->reviews_count > 1 ? 's' : '' }})</span>
                </div>
                <a href="{{ route('venues.show', $venue) }}" class="featured-card-btn">Ver complejo →</a>
              </div>
            </article>
          @empty
            <div style="padding:24px; color:#888; font-size:14px;">Todavía no hay reseñas.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  {{-- ── FAVORITES ───────────────────────────────────────────────────────── --}}
  @auth
    @if(($favorites ?? collect())->isNotEmpty())
      <div class="vi-favorites">
        <h2>⭐ Tus favoritos</h2>
        <div class="vi-fav-scroll">
          @foreach($favorites as $fav)
            <a href="{{ route('venues.show', $fav) }}" class="vi-fav-chip">
              ⚽ {{ $fav->name }}
            </a>
          @endforeach
        </div>
      </div>
    @endif
  @endauth

  {{-- ── MAP ───────────────────────────────────────────────────────────── --}}
  <div class="vi-map-wrap">
    <div class="vi-map-label">🗺️ Mapa de complejos</div>
    <div id="map" style="height: 380px;"></div>
  </div>

  {{-- ── ALL VENUES ───────────────────────────────────────────────────────── --}}
  <div class="vi-results-header" id="complejos">
    <h2>Todos los complejos</h2>
    <span class="vi-results-count">{{ $allVenues->count() }} complejo{{ $allVenues->count() !== 1 ? 's' : '' }}</span>
  </div>

  @if($allVenues->isEmpty())
    <div class="vi-empty">
      <div class="vi-empty-icon">⚽</div>
      <h3>No hay complejos todavía</h3>
      <p>Pronto habrá complejos disponibles para reservar.</p>
    </div>
  @else
    <div class="vi-venues-grid">
      @foreach($allVenues as $venue)
        <article class="vi-venue-card">

          {{-- Image --}}
          <div class="vi-venue-img-wrap">
            @if($venue->cover_image_path)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
            @else
              <div class="vi-venue-img-placeholder">⚽</div>
            @endif

            {{-- Favorite button --}}
            @auth
              @if(in_array($venue->id, $favoriteVenueIds ?? []))
                <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
                  @csrf
                  <button type="submit" class="vi-venue-fav-btn saved" title="Quitar de favoritos">❤️</button>
                </form>
              @else
                <form method="POST" action="{{ route('venues.favorite', $venue) }}" style="margin:0;">
                  @csrf
                  <button type="submit" class="vi-venue-fav-btn" title="Guardar en favoritos">🤍</button>
                </form>
              @endif
            @endauth

            @if($venue->zone)
              <div class="vi-venue-sport-badge">📍 {{ $venue->zone }}</div>
            @endif
          </div>

          {{-- Body --}}
          <div class="vi-venue-body">
            <h3 class="vi-venue-name">{{ $venue->name }}</h3>

            @if($venue->reviews_count > 0)
              <div class="vi-venue-rating">
                @php $rounded = round($venue->reviews_avg_rating); @endphp
                <span class="vi-venue-stars">
                  @for($i = 1; $i <= 5; $i++){{ $i <= $rounded ? '★' : '☆' }}@endfor
                </span>
                <span class="vi-venue-rating-text">{{ number_format($venue->reviews_avg_rating, 1) }}</span>
                <span class="vi-venue-rating-count">({{ $venue->reviews_count }} reseña{{ $venue->reviews_count > 1 ? 's' : '' }})</span>
              </div>
            @else
              <div style="font-size:13px; color:#aaa;">Sin reseñas todavía</div>
            @endif

            <p class="vi-venue-desc">
              {{ $venue->description ?? 'Reservá online y encontrá disponibilidad en pocos pasos.' }}
            </p>

            <div class="vi-venue-actions">
              <a href="{{ route('venues.show', $venue) }}" class="vi-btn-primary">Ver complejo →</a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif

</div>

{{-- ── SCRIPTS ──────────────────────────────────────────────────────────── --}}
<script>
  // ── Advanced filters toggle ─────────────────────
  function toggleAdv() {
    const panel = document.getElementById('advPanel');
    panel.classList.toggle('open');
  }

  // ── Carousel + tabs ─────────────────────────────
  const featureTabs     = Array.from(document.querySelectorAll('.feature-tab'));
  const featureCarousels = Array.from(document.querySelectorAll('.feature-carousel'));
  const featuredSection  = document.querySelector('.vi-featured');
  const carouselMovePrevBtn = document.querySelector('[data-carousel-move="prev"]');
  const carouselMoveNextBtn = document.querySelector('[data-carousel-move="next"]');

  let autoplayInterval = null;

  function getActiveCarousel() {
    return document.querySelector('.feature-carousel.active');
  }

  function getActiveTrack() {
    return getActiveCarousel()?.querySelector('[data-carousel-track]') ?? null;
  }

  function getTrackStep(track) {
    if (!track) return 280;
    const firstCard = track.querySelector('.featured-card');
    if (!firstCard) return 280;
    const cardWidth = firstCard.getBoundingClientRect().width;
    const styles = window.getComputedStyle(track);
    const gap = parseFloat(styles.columnGap || styles.gap || 14);
    return cardWidth + gap;
  }

  function activateFeatureTab(index) {
    if (!featureTabs.length) return;
    const safeIndex = (index + featureTabs.length) % featureTabs.length;
    const tab = featureTabs[safeIndex];
    const target = tab.dataset.tab;
    featureTabs.forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    featureCarousels.forEach(c => c.classList.remove('active'));
    const activeCarousel = document.getElementById('tab-' + target);
    if (activeCarousel) activeCarousel.classList.add('active');
    restartAutoplay();
  }

  function moveActiveTrack(direction) {
    const track = getActiveTrack();
    if (!track) return;
    const step = getTrackStep(track);
    track.scrollBy({ left: step * direction, behavior: 'smooth' });
  }

  function attachDragToTrack(track) {
    if (!track) return;
    let isDragging = false, startX = 0, startScrollLeft = 0;
    const stop = () => { isDragging = false; track.classList.remove('dragging'); };
    track.addEventListener('mousedown', e => { isDragging = true; startX = e.pageX; startScrollLeft = track.scrollLeft; track.classList.add('dragging'); });
    track.addEventListener('mouseleave', stop);
    track.addEventListener('mouseup', stop);
    track.addEventListener('mousemove', e => { if (!isDragging) return; e.preventDefault(); track.scrollLeft = startScrollLeft - (e.pageX - startX); });
  }

  function getActiveFeatureIndex() {
    const i = featureTabs.findIndex(t => t.classList.contains('active'));
    return i >= 0 ? i : 0;
  }

  function startAutoplay() {
    stopAutoplay();
    autoplayInterval = setInterval(() => activateFeatureTab(getActiveFeatureIndex() + 1), 3500);
  }

  function stopAutoplay() { if (autoplayInterval) { clearInterval(autoplayInterval); autoplayInterval = null; } }
  function restartAutoplay() { startAutoplay(); }

  featureTabs.forEach((tab, index) => tab.addEventListener('click', () => activateFeatureTab(index)));
  carouselMovePrevBtn?.addEventListener('click', () => { activateFeatureTab(getActiveFeatureIndex() - 1); restartAutoplay(); });
  carouselMoveNextBtn?.addEventListener('click', () => { activateFeatureTab(getActiveFeatureIndex() + 1); restartAutoplay(); });
  document.querySelectorAll('[data-carousel-track]').forEach(attachDragToTrack);

  if (featuredSection) {
    featuredSection.addEventListener('mouseenter', stopAutoplay);
    featuredSection.addEventListener('mouseleave', startAutoplay);
    featuredSection.addEventListener('touchstart', stopAutoplay, { passive: true });
    featuredSection.addEventListener('touchend', startAutoplay);
  }

  startAutoplay();

  // ── Google Maps ──────────────────────────────────
  const VENUES = [
    @foreach($allVenues as $v)
      { id: {{ $v->id }}, name: @json($v->name), lat: {{ $v->lat ?? 'null' }}, lng: {{ $v->lng ?? 'null' }}, url: @json(route('venues.show', $v)) }@if(!$loop->last),@endif
    @endforeach
  ];

  const DEFAULT_CENTER = { lat: -34.6037, lng: -58.3816 };

  function initMap() {
    const first = VENUES.find(v => v.lat !== null && v.lng !== null);
    const map = new google.maps.Map(document.getElementById('map'), {
      zoom: first ? 13 : 12,
      center: first ? { lat: Number(first.lat), lng: Number(first.lng) } : DEFAULT_CENTER,
    });
    VENUES.forEach(v => {
      if (v.lat === null || v.lng === null) return;
      const marker = new google.maps.Marker({ map, position: { lat: Number(v.lat), lng: Number(v.lng) }, title: v.name });
      const info = new google.maps.InfoWindow({ content: `<div style="font-family:system-ui;"><strong>${v.name}</strong><br><a href="${v.url}" style="color:#166534;font-weight:700;">Ver complejo →</a></div>` });
      marker.addListener('click', () => info.open({ map, anchor: marker }));
    });
  }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap" async defer></script>

@endsection
