@extends('layouts.app')

@section('title','Encontrá tu cancha — TuCancha')
@section('meta_description', 'Explorá todos los complejos deportivos disponibles en TuCancha. Filtrá por deporte, zona y horario. Reservá tu cancha online en segundos.')

@push('styles')
<style>
  /* ── AOS override ─────────────────────────────── */
  [data-aos] { pointer-events: auto !important; }

  /* ── Reset / base ─────────────────────────────── */
  .vi-wrap { display: flex; flex-direction: column; gap: 0; }

  /* ── Scroll progress bar ──────────────────────── */
  .vi-scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    height: 3px;
    width: 0%;
    background: #22c55e;
    z-index: 9999;
    transition: width 0.1s linear;
    box-shadow: 0 0 8px rgba(34,197,94,.6);
  }

  /* ── Hero ─────────────────────────────────────── */
  .vi-hero {
    position: relative;
    border-radius: 24px;
    padding: 60px 52px 48px;
    color: #fff;
    margin-bottom: 28px;
    overflow: hidden;
    min-height: 340px;
  }

  .vi-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/hero-cancha.webp');
    background-size: cover;
    background-position: center;
    opacity: 0.4;
    z-index: 0;
  }

  .vi-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(0,0,0,.88) 0%, rgba(0,0,0,.6) 60%, rgba(10,30,10,.5) 100%);
    z-index: 1;
  }

  /* Líneas decorativas de campo — solo desktop */
  @media (min-width: 769px) {
    .vi-hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 50%;
      width: 1px;
      height: 100%;
      border-left: 1px solid rgba(255,255,255,.04);
      z-index: 1;
      pointer-events: none;
    }
    .vi-hero-field-circle {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 180px;
      height: 180px;
      border-radius: 50%;
      border: 1px solid rgba(255,255,255,.04);
      z-index: 1;
      pointer-events: none;
    }
  }
  @media (max-width: 768px) {
    .vi-hero-field-circle { display: none; }
    .vi-hero { padding: 40px 20px 32px; }
    .vi-hero-text h1 { font-size: 36px; }
  }

  /* Partículas */
  .vi-particle {
    position: absolute;
    border-radius: 50%;
    z-index: 1;
    pointer-events: none;
    animation: vi-float linear infinite;
  }

  @keyframes vi-float {
    0%   { transform: translateY(0px) rotate(0deg); opacity: 0; }
    10%  { opacity: 1; }
    90%  { opacity: 1; }
    100% { transform: translateY(-120px) rotate(360deg); opacity: 0; }
  }

  @media (max-width: 768px) {
    .vi-particle { display: none; }
  }

  .vi-hero-content {
    position: relative;
    z-index: 2;
  }

  /* Badge pulsante */
  .vi-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 999px;
    background: rgba(110,234,160,.12);
    border: 1px solid rgba(110,234,160,.3);
    font-size: 13px;
    font-weight: 700;
    color: #6eeaa0;
    margin-bottom: 20px;
  }

  .vi-hero-badge-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #22c55e;
    flex-shrink: 0;
    animation: vi-pulse 1.8s ease-in-out infinite;
  }

  @keyframes vi-pulse {
    0%, 100% { opacity: 1; transform: scale(1); box-shadow: 0 0 0 0 rgba(34,197,94,.5); }
    50% { opacity: .8; transform: scale(1.1); box-shadow: 0 0 0 6px rgba(34,197,94,.0); }
  }

  /* H1 con efecto de aparición por palabras */
  .vi-hero-text h1 {
    margin: 0 0 14px 0;
    font-size: 60px;
    font-weight: 900;
    letter-spacing: -0.04em;
    line-height: 1.02;
  }

  .vi-hero-text h1 em {
    font-style: normal;
    color: #6eeaa0;
  }

  .vi-word-reveal {
    display: inline-block;
    clip-path: inset(0 100% 0 0);
    opacity: 0;
    animation: vi-char-reveal 0.6s cubic-bezier(0.22,1,0.36,1) forwards;
  }

  @keyframes vi-char-reveal {
    0%   { clip-path: inset(0 100% 0 0); opacity: 0; }
    1%   { opacity: 1; }
    100% { clip-path: inset(0 0% 0 0); opacity: 1; }
  }

  .vi-hero-text p {
    margin: 0 0 20px 0;
    color: rgba(255,255,255,.72);
    font-size: 17px;
    line-height: 1.6;
    max-width: 520px;
  }

  /* Microstats */
  .vi-hero-microstats {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
    margin-bottom: 28px;
  }

  .vi-hero-microstat {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .vi-hero-microstat-val {
    font-size: 22px;
    font-weight: 800;
    color: #6eeaa0;
    letter-spacing: -0.03em;
    line-height: 1;
  }

  .vi-hero-microstat-label {
    font-size: 11px;
    color: rgba(255,255,255,.5);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .vi-hero-microstat-sep {
    width: 1px;
    height: 32px;
    background: rgba(255,255,255,.12);
    align-self: center;
  }

  /* ── Search bar ───────────────────────────────── */
  .vi-search-bar {
    position: relative;
    display: flex;
    align-items: center;
    background: rgba(255,255,255,.09);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 16px;
    padding: 6px 6px 6px 18px;
    gap: 8px;
    backdrop-filter: blur(8px);
    transition: border-color .2s, background .2s;
    overflow: hidden;
  }

  /* Shimmer one-shot en load */
  .vi-search-bar::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, transparent 30%, rgba(255,255,255,.18) 50%, transparent 70%);
    background-size: 200% 100%;
    animation: vi-shimmer 1.2s ease 0.8s 1 forwards;
    pointer-events: none;
    z-index: 3;
    opacity: 0;
  }

  @keyframes vi-shimmer {
    0%   { background-position: 200% 0; opacity: 1; }
    100% { background-position: -200% 0; opacity: 0; }
  }

  .vi-search-bar:focus-within {
    border-color: rgba(110,234,160,.55);
    background: rgba(255,255,255,.12);
    box-shadow: 0 0 0 3px rgba(110,234,160,.1);
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
    position: relative;
    z-index: 2;
  }

  .vi-search-bar input::placeholder { color: rgba(255,255,255,.45); }

  .vi-search-btn {
    background: #22c55e;
    color: #052e16;
    border: none;
    border-radius: 12px;
    padding: 11px 22px;
    font-size: 14px;
    font-weight: 800;
    cursor: pointer;
    font-family: inherit;
    white-space: nowrap;
    transition: background .15s, transform .15s, box-shadow .15s;
    position: relative;
    z-index: 2;
  }

  .vi-search-btn:hover {
    background: #16a34a;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(34,197,94,.35);
  }

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
    background: rgba(110,234,160,.15);
    border: 1px solid rgba(110,234,160,.4);
    color: #6eeaa0;
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
    text-decoration: none;
  }

  .vi-clear-btn:hover { color: rgba(255,255,255,.85); }

  /* Advanced filter panel — glass effect */
  .vi-adv-panel {
    max-height: 0;
    overflow: hidden;
    margin-top: 0;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1), margin-top 0.3s ease, opacity 0.3s ease;
    opacity: 0;
  }

  .vi-adv-panel.open {
    max-height: 400px;
    margin-top: 12px;
    opacity: 1;
  }

  .vi-adv-panel-inner {
    background: rgba(0,0,0,.6);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(110,234,160,.18);
    border-radius: 16px;
    padding: 18px 20px;
  }

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
  .vi-adv-field select:focus { border-color: rgba(110,234,160,.5); }

  .vi-adv-field select option { background: #111; color: #fff; }

  .vi-adv-actions {
    display: flex;
    gap: 8px;
    margin-top: 14px;
  }

  /* ── Active filter tags ───────────────────────── */
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
    background: #052e16;
    color: #4ade80;
    font-size: 12px;
    font-weight: 700;
    border: 1px solid #16a34a;
  }

  /* ── Search results panel ─────────────────────── */
  .vi-search-results-panel {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
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
    color: #111;
  }

  .vi-search-results-count {
    font-size: 13px;
    color: #4ade80;
    font-weight: 700;
    background: rgba(74,222,128,.1);
    border: 1px solid rgba(74,222,128,.2);
    padding: 4px 14px;
    border-radius: 999px;
  }

  /* ── Featured section ─────────────────────────── */
  .vi-featured {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    padding: 32px 32px 28px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 16px rgba(0,0,0,.06);
  }

  /* Número decorativo grande en el fondo */
  .vi-featured-bg-num {
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 180px;
    font-weight: 900;
    color: rgba(0,0,0,.04);
    line-height: 1;
    pointer-events: none;
    user-select: none;
    transition: opacity 0.4s ease;
    z-index: 0;
  }

  .vi-featured-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
  }

  .vi-featured-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #111;
  }

  .vi-featured-header .carousel-subtitle {
    font-size: 13px;
    color: #888;
    margin-top: 3px;
  }

  .feature-tabs {
    display: flex;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 3px;
    gap: 2px;
  }

  .feature-tab {
    padding: 7px 18px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    color: #666;
    cursor: pointer;
    transition: background .15s, color .15s;
    white-space: nowrap;
    user-select: none;
  }

  .feature-tab.active {
    background: #22c55e;
    color: #052e16;
  }

  .featured-nav-arrows {
    display: flex;
    gap: 6px;
  }

  .featured-nav-arrow {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s, border-color .15s, color .15s;
    color: #555;
  }

  .featured-nav-arrow:hover {
    background: #22c55e;
    border-color: #22c55e;
    color: #052e16;
  }

  /* Barra de progreso del autoplay */
  .vi-featured-progress {
    height: 3px;
    background: #e5e7eb;
    border-radius: 999px;
    margin-bottom: 16px;
    overflow: hidden;
    position: relative;
    z-index: 1;
  }

  .vi-featured-progress-bar {
    height: 100%;
    background: #22c55e;
    border-radius: 999px;
    width: 0%;
    animation: vi-progress 3.5s linear forwards;
    box-shadow: 0 0 6px rgba(34,197,94,.5);
  }

  @keyframes vi-progress {
    from { width: 0%; }
    to   { width: 100%; }
  }

  .feature-carousel { display: none; position: relative; z-index: 1; }
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
    flex: 0 0 300px;
    scroll-snap-align: start;
    border-radius: 18px;
    overflow: hidden;
    position: relative;
    height: 260px;
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,.25);
    transition: transform .3s ease, box-shadow .3s ease, opacity .3s ease;
  }

  .featured-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 32px rgba(34,197,94,.2);
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
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
  }

  .featured-card-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.88) 0%, rgba(0,0,0,.2) 55%, transparent 100%);
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
    background: #22c55e;
    color: #052e16;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    transition: background .15s, box-shadow .15s;
  }

  .featured-card-btn:hover {
    background: #16a34a;
    box-shadow: 0 4px 14px rgba(34,197,94,.4);
  }

  /* ── Favorites ────────────────────────────────── */
  .vi-favorites {
    background: #f0fdf4;
    border-left: 4px solid #22c55e;
    border-top: 1px solid #bbf7d0;
    border-right: 1px solid #bbf7d0;
    border-bottom: 1px solid #bbf7d0;
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 28px;
  }

  .vi-favorites h2 {
    margin: 0 0 14px 0;
    font-size: 17px;
    font-weight: 800;
    color: #14532d;
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
    background: #fff;
    border: 1px solid #bbf7d0;
    font-size: 13px;
    font-weight: 700;
    color: #15803d;
    text-decoration: none;
    transition: background .15s, border-color .15s, color .15s;
  }

  .vi-fav-chip:hover {
    background: #22c55e;
    color: #052e16;
    border-color: #22c55e;
  }

  /* ── Map ──────────────────────────────────────── */
  .vi-map-wrap {
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(74,222,94,.2);
    margin-bottom: 28px;
    position: relative;
  }

  .vi-map-label {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
    font-size: 13px;
    font-weight: 700;
    color: #111;
  }

  /* Mapa skeleton antes de carga */
  .vi-map-skeleton {
    width: 100%;
    height: 380px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: vi-skeleton-pulse 1.6s ease-in-out infinite;
  }

  .vi-map-skeleton-icon {
    font-size: 40px;
    opacity: 0.4;
  }

  @keyframes vi-skeleton-pulse {
    0%, 100% { background: #f1f5f9; }
    50%       { background: #e8f0e8; }
  }

  #map {
    height: 380px;
    opacity: 0;
    transition: opacity 0.6s ease;
  }

  #map.vi-map-loaded {
    opacity: 1;
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

  /* Section title con línea animada */
  .vi-section-title {
    position: relative;
    display: inline-block;
  }

  .vi-section-title::after {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 0;
    height: 3px;
    width: 0;
    background: #22c55e;
    border-radius: 999px;
    transition: width 0.5s cubic-bezier(0.22,1,0.36,1);
    box-shadow: 0 0 8px rgba(34,197,94,.4);
  }

  .vi-section-title.vi-title-visible::after {
    width: 60px;
  }

  /* Contador de complejos pill */
  .vi-count-pill {
    font-size: 13px;
    font-weight: 800;
    background: #22c55e;
    color: #052e16;
    padding: 4px 14px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  /* ── Venue cards grid ─────────────────────────── */
  .vi-venues-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 18px;
    margin-bottom: 32px;
  }

  .vi-venue-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 22px;
    overflow: hidden;
    transition: transform .3s ease, box-shadow .3s ease;
    display: flex;
    flex-direction: column;
    position: relative;
    transform-style: preserve-3d;
    will-change: transform;
    cursor: none;
  }

  @media (max-width: 768px) {
    .vi-venue-card {
      cursor: auto;
    }
  }

  .vi-venue-card:hover {
    box-shadow: 0 16px 48px rgba(34,197,94,.18);
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
    transform: scale(1.06);
  }

  /* Skeleton de imagen lazy */
  .vi-venue-img-wrap img.vi-img-loading {
    background: linear-gradient(90deg, #e0e0e0 25%, #d0d0d0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    animation: vi-img-skeleton 1.4s linear infinite;
  }

  @keyframes vi-img-skeleton {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
  }

  /* Shine / glare overlay dentro de la imagen */
  .vi-card-shine {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 2;
    border-radius: 0;
    opacity: 0;
    transition: opacity 0.3s ease;
    background: radial-gradient(circle at 50% 50%, rgba(255,255,255,.15) 0%, transparent 60%);
  }

  .vi-venue-card:hover .vi-card-shine {
    opacity: 1;
  }

  .vi-venue-img-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
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
    z-index: 3;
  }

  .vi-venue-fav-btn:hover { transform: scale(1.15); background: #fff; }
  .vi-venue-fav-btn.saved { background: #fee2e2; }

  /* Animación de corazón al clickear */
  @keyframes vi-heart-pop {
    0%   { transform: scale(1); }
    30%  { transform: scale(1.4); }
    60%  { transform: scale(0.9); }
    80%  { transform: scale(1.15); }
    100% { transform: scale(1); }
  }

  .vi-venue-fav-btn.vi-heart-animating {
    animation: vi-heart-pop 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97);
  }

  /* Badge zona (bottom-left) */
  .vi-venue-zone-badge {
    position: absolute;
    bottom: 12px;
    left: 12px;
    padding: 4px 10px;
    border-radius: 999px;
    background: #052e16;
    color: #4ade80;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    border: 1px solid rgba(74,222,128,.2);
    z-index: 3;
  }

  /* Badge Falta Uno con punto pulsante y contador */
  .vi-venue-faltauno-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #22c55e;
    color: #052e16;
    font-size: 11px;
    font-weight: 800;
    padding: 3px 10px 3px 8px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    gap: 5px;
    z-index: 3;
  }

  .vi-faltauno-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #052e16;
    flex-shrink: 0;
    animation: vi-pulse-dot 1.4s ease-in-out infinite;
  }

  @keyframes vi-pulse-dot {
    0%, 100% { transform: scale(1); opacity: 1; }
    50%       { transform: scale(1.5); opacity: 0.7; }
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

  /* Estrellas con animación en hover de la card */
  .vi-venue-stars {
    color: #f59e0b;
    font-size: 13px;
    letter-spacing: 1px;
    display: inline-flex;
    gap: 1px;
  }

  .vi-star {
    display: inline-block;
    transition: filter 0.2s ease, transform 0.2s ease;
  }

  .vi-venue-card:hover .vi-star:nth-child(1) { animation: vi-star-glow 0.3s ease 0ms forwards; }
  .vi-venue-card:hover .vi-star:nth-child(2) { animation: vi-star-glow 0.3s ease 50ms forwards; }
  .vi-venue-card:hover .vi-star:nth-child(3) { animation: vi-star-glow 0.3s ease 100ms forwards; }
  .vi-venue-card:hover .vi-star:nth-child(4) { animation: vi-star-glow 0.3s ease 150ms forwards; }
  .vi-venue-card:hover .vi-star:nth-child(5) { animation: vi-star-glow 0.3s ease 200ms forwards; }

  @keyframes vi-star-glow {
    0%   { filter: none; transform: scale(1); }
    50%  { filter: drop-shadow(0 0 4px #f59e0b); transform: scale(1.2); }
    100% { filter: drop-shadow(0 0 4px #f59e0b); transform: scale(1); }
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

  /* Pills deportes con colores por deporte */
  .vi-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }

  .vi-tag-football {
    background: rgba(34,197,94,.12);
    border: 1px solid rgba(34,197,94,.3);
    color: #16a34a;
  }
  .vi-tag-padel {
    background: rgba(59,130,246,.12);
    border: 1px solid rgba(59,130,246,.3);
    color: #2563eb;
  }
  .vi-tag-tennis {
    background: rgba(245,158,11,.12);
    border: 1px solid rgba(245,158,11,.3);
    color: #d97706;
  }
  .vi-tag-basketball {
    background: rgba(249,115,22,.12);
    border: 1px solid rgba(249,115,22,.3);
    color: #ea580c;
  }
  .vi-tag-volleyball {
    background: rgba(139,92,246,.12);
    border: 1px solid rgba(139,92,246,.3);
    color: #7c3aed;
  }
  .vi-tag-default {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #15803d;
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

  /* Botón "Ver complejo" */
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
    transition: background .15s, box-shadow .15s, transform .15s;
    display: inline-block;
  }

  .vi-btn-primary:hover {
    background: #22c55e;
    color: #052e16;
    box-shadow: 0 4px 18px rgba(34,197,94,.35);
    transform: translateY(-1px);
  }

  /* ── Plan badges (Destacado / Premium) ─────────── */
  .vi-plan-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    z-index: 4;
    backdrop-filter: blur(4px);
  }

  .vi-plan-badge-pro {
    background: rgba(34,197,94,.9);
    color: #052e16;
    border: 1px solid rgba(34,197,94,.6);
  }

  .vi-plan-badge-full {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1a1a1a;
    border: 1px solid rgba(251,191,36,.6);
    font-weight: 800;
    text-shadow: 0 1px 0 rgba(255,255,255,.2);
    box-shadow: 0 2px 8px rgba(251,191,36,.35);
  }

  /* Card diferenciada por plan */
  .vi-venue-card.vi-card-pro {
    border-top: 3px solid #22c55e;
    box-shadow: 0 4px 20px rgba(34,197,94,.12);
  }

  .vi-venue-card.vi-card-pro:hover {
    box-shadow: 0 16px 48px rgba(34,197,94,.22);
  }

  /* ── FULL / PREMIUM card ─────────────────────── */
  .vi-venue-card.vi-card-full {
    background: linear-gradient(160deg, #1a1a1a 0%, #111111 50%, #1a1a1a 100%);
    border: 1.5px solid rgba(251,191,36,.3);
    box-shadow:
      0 6px 24px rgba(0,0,0,.35),
      0 0 0 1px rgba(251,191,36,.08),
      0 2px 20px rgba(251,191,36,.1);
    position: relative;
  }

  /* Borde dorado sutil brillante en top */
  .vi-venue-card.vi-card-full::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent 0%, #fbbf24 20%, #f59e0b 50%, #fbbf24 80%, transparent 100%);
    z-index: 5;
    border-radius: 22px 22px 0 0;
  }

  .vi-venue-card.vi-card-full:hover {
    box-shadow:
      0 20px 56px rgba(0,0,0,.45),
      0 0 0 1px rgba(251,191,36,.15),
      0 4px 30px rgba(251,191,36,.18);
    border-color: rgba(251,191,36,.45);
  }

  /* Textos claros dentro de card Full */
  .vi-venue-card.vi-card-full .vi-venue-name {
    color: #fff;
  }

  .vi-venue-card.vi-card-full .vi-venue-desc {
    color: #a1a1aa;
  }

  .vi-venue-card.vi-card-full .vi-venue-rating-text {
    color: #fbbf24;
  }

  .vi-venue-card.vi-card-full .vi-venue-rating-count {
    color: #71717a;
  }

  /* Tags deportes en card Full */
  .vi-venue-card.vi-card-full .vi-tag {
    background: rgba(255,255,255,.07);
    border-color: rgba(255,255,255,.12);
    color: #d4d4d8;
  }

  /* Boton "Ver complejo" en card Full */
  .vi-venue-card.vi-card-full .vi-btn-primary {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1a1a1a;
    font-weight: 800;
  }

  .vi-venue-card.vi-card-full .vi-btn-primary:hover {
    background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
  }

  /* Placeholder de imagen en card Full */
  .vi-venue-card.vi-card-full .vi-venue-img-placeholder {
    background: linear-gradient(135deg, #1a1a1a 0%, #262626 100%);
  }

  /* Shine overlay en card Full: tono dorado */
  .vi-venue-card.vi-card-full .vi-card-shine {
    background: radial-gradient(circle at 50% 50%, rgba(251,191,36,.1) 0%, transparent 60%);
  }

  /* Hover shadow en card Full */
  .vi-venue-card.vi-card-full:hover .vi-card-shine {
    opacity: 1;
  }

  /* Falta Uno badge se mueve a la derecha cuando hay plan badge */
  .vi-venue-card.vi-card-pro .vi-venue-faltauno-badge,
  .vi-venue-card.vi-card-full .vi-venue-faltauno-badge {
    left: auto;
    right: 50px;
  }

  /* Plan badge en featured cards del carousel */
  .featured-card .vi-plan-badge {
    top: 10px;
    left: 10px;
    z-index: 4;
  }

  /* ── Empty state ──────────────────────────────── */
  .vi-empty {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    padding: 56px 32px;
    text-align: center;
    color: #111;
  }

  .vi-empty-svg {
    margin-bottom: 20px;
  }

  .vi-empty h3 {
    margin: 0 0 8px 0;
    font-size: 20px;
    font-weight: 800;
    color: #111;
  }

  .vi-empty p {
    margin: 0 0 24px 0;
    color: #666;
    font-size: 15px;
  }

  .vi-empty-clear-btn {
    display: inline-block;
    padding: 11px 24px;
    border-radius: 12px;
    background: #22c55e;
    color: #052e16;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
    transition: background .15s, box-shadow .15s;
  }

  .vi-empty-clear-btn:hover {
    background: #16a34a;
    box-shadow: 0 4px 18px rgba(34,197,94,.35);
  }

  /* ── Custom cursor dot ────────────────────────── */
  .vi-cursor-dot {
    position: fixed;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #22c55e;
    pointer-events: none;
    z-index: 99999;
    transform: translate(-50%, -50%) scale(0);
    transition: transform 0.15s ease, opacity 0.15s ease;
    opacity: 0;
    box-shadow: 0 0 12px rgba(34,197,94,.6);
    mix-blend-mode: normal;
  }

  .vi-cursor-dot.vi-cursor-active {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
  }

  @media (max-width: 768px) {
    .vi-cursor-dot { display: none; }
  }

  /* ── Floating Action Button ───────────────────── */
  .vi-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #22c55e;
    color: #052e16;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 8px 32px rgba(34,197,94,.4);
    z-index: 9000;
    transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1), opacity 0.3s ease, box-shadow 0.2s ease;
    opacity: 0;
    transform: translateY(20px) scale(0.8);
  }

  .vi-fab.vi-fab-visible {
    opacity: 1;
    transform: translateY(0) scale(1);
  }

  .vi-fab:hover {
    background: #16a34a;
    box-shadow: 0 12px 40px rgba(34,197,94,.55);
    transform: scale(1.1);
  }

  .vi-fab-tooltip {
    position: absolute;
    right: calc(100% + 10px);
    top: 50%;
    transform: translateY(-50%);
    background: #111;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    border: 1px solid #e2e8f0;
  }

  .vi-fab:hover .vi-fab-tooltip {
    opacity: 1;
  }

  /* ── Responsive ───────────────────────────────── */
  @media (max-width: 900px) {
    .vi-hero { padding: 40px 28px 32px; }
    .vi-hero-text h1 { font-size: 40px; }
    .vi-hero-microstats { gap: 16px; }
    .feature-tabs { display: none; }
    .vi-featured { padding: 24px 20px; }
  }

  @media (max-width: 640px) {
    .vi-hero-text h1 { font-size: 30px; }
    .vi-hero-badge { font-size: 12px; }
    .vi-venues-grid { grid-template-columns: 1fr; }
    .vi-adv-grid { grid-template-columns: 1fr 1fr; }
    .vi-adv-field input,
    .vi-adv-field select { font-size: 16px; }
    .vi-search-bar input { font-size: 16px; }
    .vi-filter-chip { font-size: 12px; padding: 6px 11px; }
    .vi-filters-row { gap: 6px; }
  }

  @media (max-width: 400px) {
    .vi-adv-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')

{{-- Scroll progress bar --}}
<div class="vi-scroll-progress" id="viScrollProgress"></div>

{{-- Custom cursor dot --}}
<div class="vi-cursor-dot" id="viCursorDot"></div>

{{-- Floating Action Button --}}
<button class="vi-fab" id="viFab" onclick="document.querySelector('.vi-hero').scrollIntoView({behavior:'smooth'})" title="Buscar cancha">
  <span class="vi-fab-tooltip">Buscar cancha</span>
  <i data-lucide="search" style="width:20px;height:20px;stroke:currentColor;"></i>
</button>

<div class="vi-wrap">

  {{-- ── HERO + SEARCH ─────────────────────────────────────────────────── --}}
  <form method="GET" action="{{ route('venues.index') }}" id="venueSearchForm">
    <input type="hidden" name="user_lat" id="userLat" value="{{ $userLat ?? '' }}">
    <input type="hidden" name="user_lng" id="userLng" value="{{ $userLng ?? '' }}">
    <div class="vi-hero">
      {{-- Background image + overlay --}}
      <div class="vi-hero-bg"></div>
      <div class="vi-hero-overlay"></div>

      {{-- Línea del campo de fútbol --}}
      <div class="vi-hero-field-circle"></div>

      {{-- Partículas flotantes (12) --}}
      <div class="vi-particle" style="width:8px;height:8px;background:rgba(34,197,94,.4);left:8%;bottom:20%;animation-duration:9s;animation-delay:0s;"></div>
      <div class="vi-particle" style="width:5px;height:5px;background:rgba(110,234,160,.5);left:15%;bottom:35%;animation-duration:12s;animation-delay:1.2s;"></div>
      <div class="vi-particle" style="width:10px;height:10px;background:rgba(34,197,94,.25);left:25%;bottom:15%;animation-duration:7s;animation-delay:2.5s;"></div>
      <div class="vi-particle" style="width:6px;height:6px;background:rgba(110,234,160,.6);left:40%;bottom:28%;animation-duration:11s;animation-delay:0.8s;"></div>
      <div class="vi-particle" style="width:4px;height:4px;background:rgba(34,197,94,.5);left:55%;bottom:10%;animation-duration:8s;animation-delay:3.1s;"></div>
      <div class="vi-particle" style="width:12px;height:12px;background:rgba(110,234,160,.3);left:62%;bottom:40%;animation-duration:14s;animation-delay:1.7s;"></div>
      <div class="vi-particle" style="width:7px;height:7px;background:rgba(34,197,94,.35);left:72%;bottom:22%;animation-duration:10s;animation-delay:4.0s;"></div>
      <div class="vi-particle" style="width:5px;height:5px;background:rgba(110,234,160,.55);left:80%;bottom:48%;animation-duration:6s;animation-delay:0.4s;"></div>
      <div class="vi-particle" style="width:9px;height:9px;background:rgba(34,197,94,.3);left:88%;bottom:18%;animation-duration:13s;animation-delay:2.2s;"></div>
      <div class="vi-particle" style="width:4px;height:4px;background:rgba(110,234,160,.6);left:32%;bottom:55%;animation-duration:9s;animation-delay:5.5s;"></div>
      <div class="vi-particle" style="width:11px;height:11px;background:rgba(34,197,94,.2);left:48%;bottom:62%;animation-duration:11s;animation-delay:3.8s;"></div>
      <div class="vi-particle" style="width:6px;height:6px;background:rgba(110,234,160,.45);left:92%;bottom:38%;animation-duration:8s;animation-delay:1.0s;"></div>

      <div class="vi-hero-content">

        {{-- Badge pulsante --}}
        <div class="vi-hero-badge" data-aos="fade-up" data-aos-delay="0">
          <span class="vi-hero-badge-dot"></span>
          {{ $allVenues->count() }} complejos disponibles
        </div>

        {{-- H1 con efecto de aparición por palabras --}}
        <div class="vi-hero-text" data-aos="fade-up" data-aos-delay="100">
          <h1>
            <span class="vi-word-reveal" style="animation-delay:0.2s;">Encontrá</span>&nbsp;<span class="vi-word-reveal" style="animation-delay:0.35s;">tu</span><br>
            <span class="vi-word-reveal" style="animation-delay:0.5s;">cancha</span>&nbsp;<em><span class="vi-word-reveal" style="animation-delay:0.65s;">perfecta</span></em>
          </h1>
          <p>Filtrá por zona, deporte, precio y fecha. Reservá online en segundos.</p>
        </div>

        {{-- Microstats con contador animado --}}
        <div class="vi-hero-microstats" data-aos="fade-up" data-aos-delay="200">
          <div class="vi-hero-microstat">
            <span class="vi-hero-microstat-val vi-count-anim" data-target="{{ $allVenues->count() }}">{{ $allVenues->count() }}</span>
            <span class="vi-hero-microstat-label">Complejos</span>
          </div>
          <div class="vi-hero-microstat-sep"></div>
          <div class="vi-hero-microstat">
            <span class="vi-hero-microstat-val vi-count-anim" data-target="5">5</span>
            <span class="vi-hero-microstat-label">Deportes</span>
          </div>
          <div class="vi-hero-microstat-sep"></div>
          <div class="vi-hero-microstat">
            <span class="vi-hero-microstat-val">24/7</span>
            <span class="vi-hero-microstat-label">Reservas online</span>
          </div>
        </div>

        {{-- Search bar con shimmer --}}
        <div data-aos="fade-up" data-aos-delay="300">
          <div class="vi-search-bar" id="viSearchBar">
            <span style="flex-shrink:0; position:relative; z-index:2;display:flex;align-items:center;"><i data-lucide="search" style="width:18px;height:18px;stroke:#888;"></i></span>
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
              <i data-lucide="map-pin" style="width:13px;height:13px;stroke:currentColor;vertical-align:middle;margin-right:3px;"></i> {{ ($zone ?? '') ?: 'Zona' }}
              <select name="zone" onchange="document.getElementById('venueSearchForm').submit()">
                <option value="">Todas las zonas</option>
                @foreach($zones as $z)
                  <option value="{{ $z }}" {{ ($zone ?? '') === $z ? 'selected' : '' }}>{{ $z }}</option>
                @endforeach
              </select>
            </label>

            {{-- Deporte --}}
            <label class="vi-filter-chip {{ ($sport ?? '') ? 'active' : '' }}" style="display:inline-flex;align-items:center;gap:4px;">
              <i data-lucide="activity" style="width:13px;height:13px;stroke:currentColor;"></i> {{ match($sport ?? '') { 'football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis', 'basketball' => 'Básquet', 'volleyball' => 'Vóley', default => 'Deporte' } }}
              <select name="sport" onchange="document.getElementById('venueSearchForm').submit()">
                <option value="">Todos los deportes</option>
                <option value="football" {{ ($sport ?? '') === 'football' ? 'selected' : '' }}>Fútbol</option>
                <option value="padel" {{ ($sport ?? '') === 'padel' ? 'selected' : '' }}>Pádel</option>
                <option value="tennis" {{ ($sport ?? '') === 'tennis' ? 'selected' : '' }}>Tenis</option>
                <option value="basketball" {{ ($sport ?? '') === 'basketball' ? 'selected' : '' }}>Básquet</option>
                <option value="volleyball" {{ ($sport ?? '') === 'volleyball' ? 'selected' : '' }}>Vóley</option>
              </select>
            </label>

            {{-- Fecha --}}
            <label class="vi-filter-chip {{ ($date ?? '') ? 'active' : '' }}" onclick="event.preventDefault(); document.getElementById('dateFilterInput').showPicker()">
              <i data-lucide="calendar" style="width:13px;height:13px;stroke:currentColor;vertical-align:middle;margin-right:3px;"></i> {{ ($date ?? '') ? \Carbon\Carbon::parse($date)->format('d/m') : 'Fecha' }}
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
              <i data-lucide="sliders-horizontal" style="width:13px;height:13px;stroke:currentColor;vertical-align:middle;margin-right:3px;"></i> Más filtros
              @if(($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? ''))
                <span style="background:#22c55e; color:#052e16; border-radius:999px; width:18px; height:18px; display:inline-flex; align-items:center; justify-content:center; font-size:10px;">●</span>
              @endif
            </button>

            @if(($q ?? '') || ($zone ?? '') || ($sport ?? '') || ($date ?? '') || ($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? ''))
              <a href="{{ route('venues.index') }}" class="vi-clear-btn" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="x" style="width:12px;height:12px;stroke:currentColor;"></i> Limpiar filtros</a>
            @endif
          </div>

          {{-- Advanced filter panel — glass effect --}}
          <div class="vi-adv-panel {{ (($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? '')) ? 'open' : '' }}" id="advPanel">
            <div class="vi-adv-panel-inner">
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
              <div class="vi-adv-actions" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <button type="submit" style="padding:9px 20px; background:#22c55e; color:#052e16; border:none; border-radius:10px; font-size:13px; font-weight:800; cursor:pointer; font-family:inherit;">
                  Aplicar filtros
                </button>
                <button type="button" id="geoBtn" onclick="requestGeolocation()"
                  style="padding:9px 16px; background:{{ $sortByDistance ? '#052e16' : 'transparent' }}; color:{{ $sortByDistance ? '#22c55e' : '#555' }}; border:1px solid {{ $sortByDistance ? '#22c55e' : '#ccc' }}; border-radius:10px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; gap:6px;">
                  <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v3m0 14v3M2 12h3m14 0h3"/></svg>
                  {{ $sortByDistance ? 'Ordenando por cercanía' : 'Ordenar por cercanía' }}
                </button>
                @if($sortByDistance)
                  <a href="{{ request()->fullUrlWithQuery(['user_lat' => '', 'user_lng' => '']) }}"
                     style="font-size:12px; color:#888; text-decoration:underline;">Quitar</a>
                @endif
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </form>

  {{-- ── ACTIVE FILTER TAGS ─────────────────────────────────────────────── --}}
  @if(($q ?? '') || ($zone ?? '') || ($sport ?? '') || ($date ?? '') || ($minPrice ?? '') || ($maxPrice ?? '') || ($availableAt ?? ''))
    <div class="vi-active-filters">
      <span style="font-size:13px; color:#666; font-weight:600;">Filtros activos:</span>
      @if($q ?? '')
        <span class="vi-active-filter-tag" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="search" style="width:12px;height:12px;stroke:currentColor;"></i> "{{ $q }}"</span>
      @endif
      @if($zone ?? '')
        <span class="vi-active-filter-tag" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="map-pin" style="width:12px;height:12px;stroke:currentColor;"></i> {{ $zone }}</span>
      @endif
      @if($sport ?? '')
        <span class="vi-active-filter-tag" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="activity" style="width:12px;height:12px;stroke:currentColor;"></i> {{ match($sport) { 'football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis', 'basketball' => 'Básquet', 'volleyball' => 'Vóley', default => $sport } }}</span>
      @endif
      @if($date ?? '')
        <span class="vi-active-filter-tag" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="calendar" style="width:12px;height:12px;stroke:currentColor;"></i> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
      @endif
      @if($minPrice ?? '')
        <span class="vi-active-filter-tag" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="dollar-sign" style="width:12px;height:12px;stroke:currentColor;"></i> Desde ${{ number_format($minPrice, 0, ',', '.') }}</span>
      @endif
      @if($maxPrice ?? '')
        <span class="vi-active-filter-tag" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="dollar-sign" style="width:12px;height:12px;stroke:currentColor;"></i> Hasta ${{ number_format($maxPrice, 0, ',', '.') }}</span>
      @endif
      @if($availableAt ?? '')
        <span class="vi-active-filter-tag" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="clock" style="width:12px;height:12px;stroke:currentColor;"></i> {{ $availableAt }}</span>
      @endif
    </div>
  @endif

  {{-- ── SEARCH RESULTS PANEL (solo cuando hay filtros activos) ─────────── --}}
  @if($hasFilters)
    @if(($faltaUno ?? false))
      <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1px solid #bbf7d0; border-radius:16px; padding:14px 18px; margin-bottom:18px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <span><i data-lucide="zap" style="width:22px;height:22px;stroke:#15803d;"></i></span>
        <div style="flex:1;">
          <div style="font-size:14px; font-weight:800; color:#15803d;">Complejos con Falta Uno habilitado</div>
          <div style="font-size:12px; color:#16a34a; margin-top:2px;">Estos complejos tienen al menos una cancha donde podés crear partidos Falta Uno.</div>
        </div>
        <a href="{{ route('falta-uno.index') }}" style="font-size:13px; color:#15803d; font-weight:700; text-decoration:underline; display:inline-flex; align-items:center; gap:4px;">Ver partidos disponibles <i data-lucide="arrow-right" style="width:13px;height:13px;stroke:currentColor;"></i></a>
      </div>
    @endif
    <div class="vi-search-results-panel">
      <div class="vi-search-results-header">
        <h2 style="display:inline-flex;align-items:center;gap:8px;"><i data-lucide="search" style="width:18px;height:18px;stroke:currentColor;"></i> Resultados de búsqueda</h2>
        <span class="vi-search-results-count">{{ $venues->count() }} resultado{{ $venues->count() !== 1 ? 's' : '' }}</span>
      </div>

      @if($venues->isEmpty())
        <div class="vi-empty">
          <div class="vi-empty-svg">
            <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect x="4" y="8" width="56" height="48" rx="6" stroke="#22c55e" stroke-width="2.5" fill="none"/>
              <line x1="4" y1="24" x2="60" y2="24" stroke="#22c55e" stroke-width="2"/>
              <line x1="4" y1="40" x2="60" y2="40" stroke="#22c55e" stroke-width="2"/>
              <line x1="32" y1="8" x2="32" y2="56" stroke="#22c55e" stroke-width="2"/>
              <circle cx="32" cy="32" r="8" stroke="#22c55e" stroke-width="2" fill="none"/>
            </svg>
          </div>
          <h3>Sin resultados</h3>
          <p>No encontramos complejos con esos filtros. Probá ajustando la búsqueda.</p>
          <a href="{{ route('venues.index') }}" class="vi-empty-clear-btn">Limpiar filtros</a>
        </div>
      @else
        <div class="vi-venues-grid">
          @foreach($venues as $index => $venue)
            @php
              $delay = min($index * 50, 300);
              $planSlug = $venue->owner_plan_slug ?? 'starter';
              $cardClass = match($planSlug) { 'pro' => 'vi-card-pro', 'full' => 'vi-card-full', default => '' };
            @endphp
            <article class="vi-venue-card {{ $cardClass }}" data-aos="fade-up" data-aos-delay="{{ $delay }}">
              <div class="vi-venue-img-wrap">
                @if($venue->cover_image_path)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}" loading="lazy" class="vi-img-loading" onload="this.classList.remove('vi-img-loading')">
                @else
                  <div class="vi-venue-img-placeholder"><i data-lucide="building-2" style="width:32px;height:32px;stroke:#ccc;stroke-width:1.5;"></i></div>
                @endif
                <div class="vi-card-shine"></div>
                @if($planSlug === 'pro')
                  <div class="vi-plan-badge vi-plan-badge-pro">
                    <i data-lucide="star" style="width:12px;height:12px;stroke:currentColor;fill:currentColor;"></i> Destacado
                  </div>
                @elseif($planSlug === 'full')
                  <div class="vi-plan-badge vi-plan-badge-full">
                    <i data-lucide="shield-check" style="width:12px;height:12px;stroke:currentColor;"></i> Premium
                  </div>
                @endif
                @auth
                  @if(in_array($venue->id, $favoriteVenueIds ?? []))
                    <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
                      @csrf
                      <button type="submit" class="vi-venue-fav-btn saved" title="Quitar de favoritos"><i data-lucide="heart" style="width:16px;height:16px;stroke:#ef4444;fill:#ef4444;"></i></button>
                    </form>
                  @else
                    <form method="POST" action="{{ route('venues.favorite', $venue) }}" style="margin:0;">
                      @csrf
                      <button type="submit" class="vi-venue-fav-btn" title="Guardar en favoritos"><i data-lucide="heart" style="width:16px;height:16px;stroke:#999;"></i></button>
                    </form>
                  @endif
                @endauth
                @if(($venue->falta_uno_count ?? 0) > 0)
                  <div class="vi-venue-faltauno-badge" style="display:inline-flex;align-items:center;gap:4px;">
                    <span class="vi-faltauno-dot"></span>
                    <i data-lucide="zap" style="width:12px;height:12px;stroke:currentColor;"></i> Falta Uno · {{ $venue->falta_uno_count }} partido{{ $venue->falta_uno_count > 1 ? 's' : '' }}
                  </div>
                @endif
                @if($venue->zone)
                  <div class="vi-venue-zone-badge" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="map-pin" style="width:11px;height:11px;stroke:currentColor;"></i> {{ $venue->zone }}</div>
                @endif
              </div>
              <div class="vi-venue-body">
                <h3 class="vi-venue-name">{{ $venue->name }}</h3>
                @if($venue->reviews_count > 0)
                  <div class="vi-venue-rating">
                    @php $rounded = round($venue->reviews_avg_rating); @endphp
                    <span class="vi-venue-stars">
                      @for($i = 1; $i <= 5; $i++)<span class="vi-star">{{ $i <= $rounded ? '★' : '☆' }}</span>@endfor
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
                  <a href="{{ route('venues.show', $venue) }}" class="vi-btn-primary" style="display:inline-flex;align-items:center;gap:5px;">Ver complejo <i data-lucide="arrow-right" style="width:14px;height:14px;stroke:currentColor;"></i></a>
                </div>
              </div>
            </article>
          @endforeach
        </div>
      @endif
    </div>
  @endif

  {{-- ── FEATURED CAROUSEL ──────────────────────────────────────────────── --}}
  <div class="vi-featured" id="viFeatured">
    {{-- Número decorativo de fondo --}}
    <div class="vi-featured-bg-num" id="viFeaturedBgNum">01</div>

    <div class="vi-featured-header">
      <div>
        <h2 class="vi-section-title">Destacados</h2>
        <div class="carousel-subtitle">Los complejos más activos, con descuentos y mejor valorados.</div>
      </div>
      <div style="display:flex; align-items:center; gap:10px;">
        <div class="feature-tabs">
          <div class="feature-tab active" data-tab="top" data-tab-num="01" style="display:inline-flex;align-items:center;gap:5px;"><i data-lucide="flame" style="width:13px;height:13px;stroke:currentColor;"></i> Más reservados</div>
          <div class="feature-tab" data-tab="discounts" data-tab-num="02" style="display:inline-flex;align-items:center;gap:5px;"><i data-lucide="tag" style="width:13px;height:13px;stroke:currentColor;"></i> Descuentos</div>
          <div class="feature-tab" data-tab="rated" data-tab-num="03" style="display:inline-flex;align-items:center;gap:5px;"><i data-lucide="star" style="width:13px;height:13px;stroke:currentColor;"></i> Mejor valorados</div>
        </div>
        <div class="featured-nav-arrows">
          <button type="button" class="featured-nav-arrow" data-carousel-move="prev" aria-label="Anterior">&#8249;</button>
          <button type="button" class="featured-nav-arrow" data-carousel-move="next" aria-label="Siguiente">&#8250;</button>
        </div>
      </div>
    </div>

    {{-- Barra de progreso del autoplay --}}
    <div class="vi-featured-progress">
      <div class="vi-featured-progress-bar" id="viFeaturedProgressBar"></div>
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
                <div class="featured-card-placeholder"><i data-lucide="building-2" style="width:28px;height:28px;stroke:#aaa;stroke-width:1.5;"></i></div>
              @endif
              <div class="featured-card-overlay"></div>
              <div class="featured-card-body">
                <h3>{{ $venue->name }}</h3>
                <div class="featured-card-meta">
                  @if($venue->zone)
                    <span class="featured-card-badge" style="display:inline-flex;align-items:center;gap:3px;"><i data-lucide="map-pin" style="width:11px;height:11px;stroke:currentColor;"></i> {{ $venue->zone }}</span>
                  @endif
                  <span style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="flame" style="width:13px;height:13px;stroke:currentColor;"></i> {{ $venue->weekly_reservations_count }} esta semana</span>
                </div>
                <a href="{{ route('venues.show', $venue) }}" class="featured-card-btn" style="display:inline-flex;align-items:center;gap:5px;">Ver complejo <i data-lucide="arrow-right" style="width:14px;height:14px;stroke:currentColor;"></i></a>
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
                <div class="featured-card-placeholder"><i data-lucide="tag" style="width:28px;height:28px;stroke:#aaa;stroke-width:1.5;"></i></div>
              @endif
              <div class="featured-card-overlay"></div>
              <div class="featured-card-body">
                <h3>{{ $venue->name }}</h3>
                <div class="featured-card-meta">
                  @if($venue->zone)
                    <span class="featured-card-badge" style="display:inline-flex;align-items:center;gap:3px;"><i data-lucide="map-pin" style="width:11px;height:11px;stroke:currentColor;"></i> {{ $venue->zone }}</span>
                  @endif
                  <span style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="tag" style="width:13px;height:13px;stroke:currentColor;"></i> Descuentos activos</span>
                </div>
                <a href="{{ route('venues.show', $venue) }}" class="featured-card-btn" style="display:inline-flex;align-items:center;gap:5px;">Ver complejo <i data-lucide="arrow-right" style="width:14px;height:14px;stroke:currentColor;"></i></a>
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
                <div class="featured-card-placeholder"><i data-lucide="star" style="width:28px;height:28px;stroke:#aaa;stroke-width:1.5;"></i></div>
              @endif
              <div class="featured-card-overlay"></div>
              <div class="featured-card-body">
                <h3>{{ $venue->name }}</h3>
                <div class="featured-card-meta">
                  @if($venue->zone)
                    <span class="featured-card-badge" style="display:inline-flex;align-items:center;gap:3px;"><i data-lucide="map-pin" style="width:11px;height:11px;stroke:currentColor;"></i> {{ $venue->zone }}</span>
                  @endif
                  <span style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="star" style="width:13px;height:13px;stroke:currentColor;"></i> {{ number_format($venue->reviews_avg_rating, 1) }} / 5 ({{ $venue->reviews_count }} reseña{{ $venue->reviews_count > 1 ? 's' : '' }})</span>
                </div>
                <a href="{{ route('venues.show', $venue) }}" class="featured-card-btn" style="display:inline-flex;align-items:center;gap:5px;">Ver complejo <i data-lucide="arrow-right" style="width:14px;height:14px;stroke:currentColor;"></i></a>
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
        <h2 class="vi-section-title" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="star" style="width:16px;height:16px;stroke:currentColor;"></i> Tus favoritos</h2>
        <div class="vi-fav-scroll" style="margin-top:14px;">
          @foreach($favorites as $fav)
            <a href="{{ route('venues.show', $fav) }}" class="vi-fav-chip" style="display:inline-flex;align-items:center;gap:5px;">
              <i data-lucide="building-2" style="width:13px;height:13px;stroke:currentColor;"></i> {{ $fav->name }}
            </a>
          @endforeach
        </div>
      </div>
    @endif
  @endauth

  {{-- ── MAP ───────────────────────────────────────────────────────────── --}}
  <div class="vi-map-label" style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="map" style="width:16px;height:16px;stroke:currentColor;"></i> <span class="vi-section-title">Mapa de complejos</span></div>
  <div class="vi-map-wrap" id="viMapWrap">
    {{-- Skeleton visible hasta que cargue el mapa --}}
    <div class="vi-map-skeleton" id="viMapSkeleton">
      <span class="vi-map-skeleton-icon"><i data-lucide="map" style="width:32px;height:32px;stroke:#ccc;stroke-width:1.5;"></i></span>
    </div>
    <div id="map" style="height: 380px; display:none;"></div>
  </div>

  {{-- ── COMPLEJOS PREMIUM CAROUSEL ──────────────────────────────────────── --}}
  @if(($premiumVenues ?? collect())->isNotEmpty())
    <div class="vi-featured" style="margin-bottom:28px;">
      <div class="vi-featured-header">
        <div>
          <h2 class="vi-section-title" style="display:inline-flex;align-items:center;gap:8px;">
            <i data-lucide="crown" style="width:20px;height:20px;stroke:#fbbf24;"></i> Complejos Premium
          </h2>
          <div class="carousel-subtitle">Los mejores complejos deportivos de la plataforma.</div>
        </div>
        <div class="featured-nav-arrows">
          <button type="button" class="featured-nav-arrow" onclick="document.getElementById('premiumTrack').scrollBy({left:-314,behavior:'smooth'})" aria-label="Anterior">&#8249;</button>
          <button type="button" class="featured-nav-arrow" onclick="document.getElementById('premiumTrack').scrollBy({left:314,behavior:'smooth'})" aria-label="Siguiente">&#8250;</button>
        </div>
      </div>
      <div class="feature-carousel-shell">
        <div class="carousel-track featured-track" id="premiumTrack">
          @foreach($premiumVenues as $venue)
            @php $planSlug = $venue->owner_plan_slug ?? 'starter'; @endphp
            <article class="featured-card" style="{{ $planSlug === 'full' ? 'border:2px solid #22c55e;' : 'border-top:3px solid #22c55e;' }}">
              @if($venue->cover_image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
              @else
                <div class="featured-card-placeholder"><i data-lucide="crown" style="width:28px;height:28px;stroke:#fbbf24;stroke-width:1.5;"></i></div>
              @endif
              <div class="featured-card-overlay"></div>
              @if($planSlug === 'pro')
                <div class="vi-plan-badge vi-plan-badge-pro">
                  <i data-lucide="star" style="width:12px;height:12px;stroke:currentColor;fill:currentColor;"></i> Destacado
                </div>
              @elseif($planSlug === 'full')
                <div class="vi-plan-badge vi-plan-badge-full">
                  <i data-lucide="shield-check" style="width:12px;height:12px;stroke:currentColor;"></i> Premium
                </div>
              @endif
              <div class="featured-card-body">
                <h3>{{ $venue->name }}</h3>
                <div class="featured-card-meta">
                  @if($venue->zone)
                    <span class="featured-card-badge" style="display:inline-flex;align-items:center;gap:3px;"><i data-lucide="map-pin" style="width:11px;height:11px;stroke:currentColor;"></i> {{ $venue->zone }}</span>
                  @endif
                  @if(($venue->reviews_count ?? 0) > 0)
                    <span style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="star" style="width:13px;height:13px;stroke:currentColor;"></i> {{ number_format($venue->reviews_avg_rating, 1) }}</span>
                  @endif
                </div>
                <a href="{{ route('venues.show', $venue) }}" class="featured-card-btn" style="display:inline-flex;align-items:center;gap:5px;">Ver complejo <i data-lucide="arrow-right" style="width:14px;height:14px;stroke:currentColor;"></i></a>
              </div>
            </article>
          @endforeach
        </div>
      </div>
    </div>
  @endif

  {{-- ── ALL VENUES ───────────────────────────────────────────────────────── --}}
  <div class="vi-results-header" id="complejos">
    <h2 class="vi-section-title">
      Todos los complejos
    </h2>
    <span class="vi-count-pill">
      <span class="vi-count-anim-pill" data-target="{{ $allVenues->count() }}">{{ $allVenues->count() }}</span>
      complejo{{ $allVenues->count() !== 1 ? 's' : '' }}
    </span>
  </div>

  @if($allVenues->isEmpty())
    <div class="vi-empty">
      <div class="vi-empty-svg">
        <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="4" y="8" width="56" height="48" rx="6" stroke="#22c55e" stroke-width="2.5" fill="none"/>
          <line x1="4" y1="24" x2="60" y2="24" stroke="#22c55e" stroke-width="2"/>
          <line x1="4" y1="40" x2="60" y2="40" stroke="#22c55e" stroke-width="2"/>
          <line x1="32" y1="8" x2="32" y2="56" stroke="#22c55e" stroke-width="2"/>
          <circle cx="32" cy="32" r="8" stroke="#22c55e" stroke-width="2" fill="none"/>
        </svg>
      </div>
      <h3>No hay complejos todavía</h3>
      <p>Pronto habrá complejos disponibles para reservar.</p>
    </div>
  @else
    <div class="vi-venues-grid">
      @foreach($allVenues as $index => $venue)
        @php
          $delay = min($index * 50, 300);
          $planSlug = $venue->owner_plan_slug ?? 'starter';
          $cardClass = match($planSlug) { 'pro' => 'vi-card-pro', 'full' => 'vi-card-full', default => '' };
        @endphp
        <article class="vi-venue-card {{ $cardClass }}" data-aos="fade-up" data-aos-delay="{{ $delay }}">

          {{-- Image --}}
          <div class="vi-venue-img-wrap">
            @if($venue->cover_image_path)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}" loading="lazy" class="vi-img-loading" onload="this.classList.remove('vi-img-loading')">
            @else
              <div class="vi-venue-img-placeholder"><i data-lucide="building-2" style="width:32px;height:32px;stroke:#ccc;stroke-width:1.5;"></i></div>
            @endif

            {{-- Shine overlay --}}
            <div class="vi-card-shine"></div>

            {{-- Plan badge --}}
            @if($planSlug === 'pro')
              <div class="vi-plan-badge vi-plan-badge-pro">
                <i data-lucide="star" style="width:12px;height:12px;stroke:currentColor;fill:currentColor;"></i> Destacado
              </div>
            @elseif($planSlug === 'full')
              <div class="vi-plan-badge vi-plan-badge-full">
                <i data-lucide="shield-check" style="width:12px;height:12px;stroke:currentColor;"></i> Premium
              </div>
            @endif

            {{-- Favorite button --}}
            @auth
              @if(in_array($venue->id, $favoriteVenueIds ?? []))
                <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
                  @csrf
                  <button type="submit" class="vi-venue-fav-btn saved" title="Quitar de favoritos"><i data-lucide="heart" style="width:16px;height:16px;stroke:#ef4444;fill:#ef4444;"></i></button>
                </form>
              @else
                <form method="POST" action="{{ route('venues.favorite', $venue) }}" style="margin:0;">
                  @csrf
                  <button type="submit" class="vi-venue-fav-btn" title="Guardar en favoritos"><i data-lucide="heart" style="width:16px;height:16px;stroke:#999;"></i></button>
                </form>
              @endif
            @endauth

            @if(($venue->falta_uno_count ?? 0) > 0)
              <div class="vi-venue-faltauno-badge" style="display:inline-flex;align-items:center;gap:4px;">
                <span class="vi-faltauno-dot"></span>
                <i data-lucide="zap" style="width:12px;height:12px;stroke:currentColor;"></i> Falta Uno · {{ $venue->falta_uno_count }} partido{{ $venue->falta_uno_count > 1 ? 's' : '' }}
              </div>
            @endif
            @if($venue->zone)
              <div class="vi-venue-zone-badge" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="map-pin" style="width:11px;height:11px;stroke:currentColor;"></i> {{ $venue->zone }}</div>
            @endif
          </div>

          {{-- Body --}}
          <div class="vi-venue-body">
            <h3 class="vi-venue-name">{{ $venue->name }}</h3>

            @if($venue->reviews_count > 0)
              <div class="vi-venue-rating">
                @php $rounded = round($venue->reviews_avg_rating); @endphp
                <span class="vi-venue-stars">
                  @for($i = 1; $i <= 5; $i++)<span class="vi-star">{{ $i <= $rounded ? '★' : '☆' }}</span>@endfor
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
    if (!track) return 314;
    const firstCard = track.querySelector('.featured-card');
    if (!firstCard) return 314;
    const cardWidth = firstCard.getBoundingClientRect().width;
    const styles = window.getComputedStyle(track);
    const gap = parseFloat(styles.columnGap || styles.gap || 14);
    return cardWidth + gap;
  }

  function resetProgressBar() {
    const bar = document.getElementById('viFeaturedProgressBar');
    if (!bar) return;
    bar.style.animation = 'none';
    bar.offsetHeight; // reflow
    bar.style.animation = 'vi-progress 3.5s linear forwards';
  }

  function updateBgNum(tab) {
    const bgNum = document.getElementById('viFeaturedBgNum');
    if (!bgNum) return;
    const num = tab.dataset.tabNum || '01';
    bgNum.textContent = num;
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
    updateBgNum(tab);
    resetProgressBar();
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
  // Drag para el carousel premium
  const premiumTrack = document.getElementById('premiumTrack');
  if (premiumTrack) attachDragToTrack(premiumTrack);

  if (featuredSection) {
    featuredSection.addEventListener('mouseenter', stopAutoplay);
    featuredSection.addEventListener('mouseleave', startAutoplay);
    featuredSection.addEventListener('touchstart', stopAutoplay, { passive: true });
    featuredSection.addEventListener('touchend', startAutoplay);
  }

  startAutoplay();

  // ── Google Maps con lazy load ────────────────────
  const VENUES = [
    @foreach($allVenues as $v)
      { id: {{ $v->id }}, name: @json($v->name), lat: {{ $v->lat ?? 'null' }}, lng: {{ $v->lng ?? 'null' }}, url: @json(route('venues.show', $v)) }@if(!$loop->last),@endif
    @endforeach
  ];

  const DEFAULT_CENTER = { lat: -34.6037, lng: -58.3816 };

  function initMap() {
    const mapEl = document.getElementById('map');
    const skeleton = document.getElementById('viMapSkeleton');
    if (!mapEl) return;
    mapEl.style.display = 'block';
    if (skeleton) skeleton.style.display = 'none';

    const first = VENUES.find(v => v.lat !== null && v.lng !== null);
    const map = new google.maps.Map(mapEl, {
      zoom: first ? 13 : 12,
      center: first ? { lat: Number(first.lat), lng: Number(first.lng) } : DEFAULT_CENTER,
    });
    VENUES.forEach(v => {
      if (v.lat === null || v.lng === null) return;
      const marker = new google.maps.Marker({ map, position: { lat: Number(v.lat), lng: Number(v.lng) }, title: v.name });
      const info = new google.maps.InfoWindow({ content: `<div style="font-family:system-ui;"><strong>${v.name}</strong><br><a href="${v.url}" style="color:#166534;font-weight:700;">Ver complejo →</a></div>` });
      marker.addListener('click', () => info.open({ map, anchor: marker }));
    });

    setTimeout(() => mapEl.classList.add('vi-map-loaded'), 100);
  }

  // Lazy load del mapa — carga el script solo cuando el wrapper entra en viewport
  let mapScriptLoaded = false;
  const mapWrapObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !mapScriptLoaded) {
        mapScriptLoaded = true;
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config("services.google_maps.key") }}&callback=initMap';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
        mapWrapObserver.disconnect();
      }
    });
  }, { threshold: 0.1 });

  const mapWrap = document.getElementById('viMapWrap');
  if (mapWrap) mapWrapObserver.observe(mapWrap);
</script>

@push('scripts')
  {{-- AOS --}}
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 520,
      easing: 'ease-out-quad',
      once: true,
      offset: 40,
    });

    // ── 1. Scroll progress bar ──────────────────────
    const viScrollProgressBar = document.getElementById('viScrollProgress');
    window.addEventListener('scroll', () => {
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const docHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
      const pct = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
      if (viScrollProgressBar) viScrollProgressBar.style.width = pct + '%';
    }, { passive: true });

    // ── 2. FAB — aparece después de 200px de scroll ─
    const viFab = document.getElementById('viFab');
    window.addEventListener('scroll', () => {
      if (!viFab) return;
      if (window.scrollY > 200) {
        viFab.classList.add('vi-fab-visible');
      } else {
        viFab.classList.remove('vi-fab-visible');
      }
    }, { passive: true });

    // ── 3. Contador animado en microstats ───────────
    function animateCount(el) {
      const target = parseInt(el.dataset.target, 10);
      if (isNaN(target)) return;
      let start = 0;
      const duration = 1200;
      const startTime = performance.now();
      function update(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // easeOutExpo
        const eased = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const current = Math.floor(eased * target);
        el.textContent = current;
        if (progress < 1) requestAnimationFrame(update);
        else el.textContent = target;
      }
      requestAnimationFrame(update);
    }

    const countObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          countObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    document.querySelectorAll('.vi-count-anim, .vi-count-anim-pill').forEach(el => {
      el.textContent = '0';
      countObserver.observe(el);
    });

    // ── 4. Section titles con línea animada ─────────
    const titleObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('vi-title-visible');
        }
      });
    }, { threshold: 0.3 });

    document.querySelectorAll('.vi-section-title').forEach(el => titleObserver.observe(el));

    // ── 5. Tilt 3D en venue cards (solo desktop) ────
    const isMobile = window.matchMedia('(max-width: 768px)').matches;

    if (!isMobile) {
      document.querySelectorAll('.vi-venue-card').forEach(card => {
        const shineEl = card.querySelector('.vi-card-shine');

        card.addEventListener('mousemove', (e) => {
          const rect = card.getBoundingClientRect();
          const x = e.clientX - rect.left;
          const y = e.clientY - rect.top;
          const cx = rect.width / 2;
          const cy = rect.height / 2;
          const rotY = ((x - cx) / cx) * 8;
          const rotX = -((y - cy) / cy) * 8;
          card.style.transition = 'transform 0.1s ease, box-shadow 0.3s ease';
          card.style.transform = `perspective(800px) rotateX(${rotX}deg) rotateY(${rotY}deg) translateY(-6px)`;

          // Shine/glare
          if (shineEl) {
            const imgWrap = card.querySelector('.vi-venue-img-wrap');
            if (imgWrap) {
              const ir = imgWrap.getBoundingClientRect();
              const ix = ((e.clientX - ir.left) / ir.width) * 100;
              const iy = ((e.clientY - ir.top) / ir.height) * 100;
              shineEl.style.background = `radial-gradient(circle at ${ix}% ${iy}%, rgba(255,255,255,.18) 0%, transparent 60%)`;
            }
          }
        });

        card.addEventListener('mouseleave', () => {
          card.style.transition = 'transform 0.5s ease, box-shadow 0.3s ease';
          card.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) translateY(0px)';
        });
      });
    }

    // ── 6. Custom cursor dot ─────────────────────────
    if (!isMobile) {
      const cursorDot = document.getElementById('viCursorDot');
      let cursorX = 0, cursorY = 0;
      let dotX = 0, dotY = 0;
      let isOnCard = false;

      document.addEventListener('mousemove', (e) => {
        cursorX = e.clientX;
        cursorY = e.clientY;
      });

      function animateCursor() {
        dotX += (cursorX - dotX) * 0.18;
        dotY += (cursorY - dotY) * 0.18;
        if (cursorDot) {
          cursorDot.style.left = dotX + 'px';
          cursorDot.style.top  = dotY + 'px';
        }
        requestAnimationFrame(animateCursor);
      }
      animateCursor();

      document.querySelectorAll('.vi-venue-card').forEach(card => {
        card.addEventListener('mouseenter', () => {
          if (cursorDot) cursorDot.classList.add('vi-cursor-active');
        });
        card.addEventListener('mouseleave', () => {
          if (cursorDot) cursorDot.classList.remove('vi-cursor-active');
        });
      });
    }

    // ── 7. Favorito heart pop animation ─────────────
    document.querySelectorAll('.vi-venue-fav-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        this.classList.remove('vi-heart-animating');
        void this.offsetWidth;
        this.classList.add('vi-heart-animating');
        this.addEventListener('animationend', () => {
          this.classList.remove('vi-heart-animating');
        }, { once: true });
      });
    });

  </script>

  <script>
    function requestGeolocation() {
      const btn = document.getElementById('geoBtn');
      if (!navigator.geolocation) {
        alert('Tu navegador no soporta geolocalización.');
        return;
      }
      btn.textContent = 'Obteniendo ubicación...';
      btn.disabled = true;
      navigator.geolocation.getCurrentPosition(
        function(pos) {
          document.getElementById('userLat').value = pos.coords.latitude;
          document.getElementById('userLng').value = pos.coords.longitude;
          document.getElementById('venueSearchForm').submit();
        },
        function(err) {
          const msgs = {
            1: 'Permiso denegado. Permitile al navegador acceder a tu ubicación.',
            2: 'Ubicación no disponible en este dispositivo.',
            3: 'Tiempo de espera agotado. Intentá de nuevo.',
          };
          btn.textContent = msgs[err.code] || 'No se pudo obtener la ubicación';
          btn.disabled = false;
        },
        { timeout: 8000 }
      );
    }
  </script>
@endpush

@endsection
