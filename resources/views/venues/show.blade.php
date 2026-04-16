@extends('layouts.app')

@section('title', $venue->name . ' — TuCancha')
@section('meta_description', ($venue->description ? Str::limit($venue->description, 155) : 'Reserva canchas en ' . $venue->name . '. ' . ($venue->address ?? '') . ' Consulta disponibilidad y confirma tu turno online en TuCancha.'))
@section('og_title', $venue->name . ' — Reservas en TuCancha')
@section('og_description', ($venue->description ? Str::limit($venue->description, 155) : 'Reserva canchas en ' . $venue->name . '. Consulta disponibilidad y confirma tu turno online.'))
@if($venue->cover_image_path)
  @section('og_image', Storage::url($venue->cover_image_path))
@endif

@push('styles')
<style>
  /* ================================================================
     VENUE SHOW — Dark Theme
     Font: Sora (loaded in layout)
     Palette: #e8e8e8 (text), #a0a0a0/#666 (muted), #22c55e (accent), #050505 (bg)
     ================================================================ */

  /* ── HERO ──────────────────────────────────────────── */
  .vs-hero {
    position: relative;
    height: 480px;
    overflow: hidden;
    border-radius: 24px;
    margin-bottom: 24px;
  }

  .vs-hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .vs-hero-bg-placeholder {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  }

  .vs-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,.15) 0%, rgba(0,0,0,.7) 100%);
    z-index: 1;
  }

  .vs-hero-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 32px 36px;
    z-index: 5;
    gap: 12px;
  }

  /* Top bar: back + fav */
  .vs-top-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 6;
    padding: 20px 36px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .vs-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: rgba(255,255,255,.85);
    text-decoration: none;
    background: rgba(0,0,0,.25);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 10px;
    padding: 8px 16px;
    transition: background .2s;
  }
  .vs-back:hover { background: rgba(0,0,0,.4); color: #fff; }

  .vs-fav-btn {
    width: 40px;
    height: 40px;
    background: rgba(0,0,0,.25);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background .2s, transform .15s;
  }
  .vs-fav-btn:hover { background: rgba(0,0,0,.4); transform: scale(1.05); }

  /* Hero text */
  .vs-hero-title {
    font-size: 44px;
    font-weight: 800;
    letter-spacing: -0.035em;
    line-height: 1.05;
    color: #fff;
    margin: 0;
  }

  .vs-hero-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .vs-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 13px;
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.12);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,.9);
  }

  .vs-chip-accent {
    background: rgba(34,197,94,.15);
    border-color: rgba(34,197,94,.25);
    color: #86efac;
  }

  .vs-chip-warn {
    background: rgba(245,158,11,.1);
    border-color: rgba(245,158,11,.2);
    color: #fcd34d;
  }

  /* ── INFO SECTION ──────────────────────────────────── */
  .vs-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
  }

  .vs-info-main {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    padding: 28px 32px;
  }

  .vs-info-map {
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    min-height: 280px;
  }

  .vs-info-map iframe {
    display: block;
    border: 0;
    width: 100%;
    height: 100%;
    min-height: 280px;
  }

  .vs-map-actions {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 14px 16px;
    background: linear-gradient(to top, rgba(0,0,0,.7) 0%, transparent 100%);
    display: flex;
    gap: 8px;
    z-index: 2;
  }

  .vs-map-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    border: none;
    font-family: 'Sora', sans-serif;
    transition: opacity .15s;
  }
  .vs-map-btn:hover { opacity: .85; }
  .vs-map-btn-primary { background: #22c55e; color: #050505; }
  .vs-map-btn-ghost { background: rgba(255,255,255,.1); color: #fff; border: 1px solid rgba(255,255,255,.2); }

  /* Stats row */
  .vs-stats {
    display: flex;
    gap: 0;
    margin-bottom: 24px;
  }

  .vs-stat {
    flex: 1;
    text-align: center;
    padding: 16px 12px;
    position: relative;
  }

  .vs-stat + .vs-stat::before {
    content: '';
    position: absolute;
    left: 0;
    top: 20%;
    height: 60%;
    width: 1px;
    background: rgba(255,255,255,.08);
  }

  .vs-stat-value {
    font-size: 28px;
    font-weight: 800;
    color: #e8e8e8;
    letter-spacing: -0.02em;
    line-height: 1;
  }

  .vs-stat-label {
    font-size: 11px;
    font-weight: 600;
    color: #666;
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  /* Description */
  .vs-desc {
    font-size: 14px;
    color: #a0a0a0;
    line-height: 1.7;
    margin: 0 0 20px 0;
  }

  /* Amenities */
  .vs-amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .vs-amenity {
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #a0a0a0;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.08);
  }

  /* Share row */
  .vs-share {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid rgba(255,255,255,.06);
  }

  .vs-share-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: opacity .15s;
    border: none;
    cursor: pointer;
    font-family: 'Sora', sans-serif;
  }
  .vs-share-btn:hover { opacity: .85; }

  /* ── FIELDS SECTION ────────────────────────────────── */
  .vs-section {
    margin-bottom: 24px;
  }

  .vs-section-header {
    margin-bottom: 20px;
  }

  .vs-section-title {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #e8e8e8;
    margin: 0 0 4px 0;
  }

  .vs-section-sub {
    font-size: 13px;
    color: #666;
    margin: 0;
  }

  /* Calendar CTA */
  .vs-cal-cta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    padding: 18px 24px;
    background: #161616;
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 14px;
    margin-bottom: 20px;
  }

  .vs-cal-cta-text {
    font-size: 14px;
    font-weight: 700;
    color: #e8e8e8;
  }

  .vs-cal-cta-sub {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
  }

  .vs-cal-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: #22c55e;
    color: #050505;
    font-weight: 700;
    font-size: 13px;
    border-radius: 8px;
    text-decoration: none;
    transition: background .15s;
    white-space: nowrap;
    font-family: 'Sora', sans-serif;
  }
  .vs-cal-btn:hover { background: #16a34a; color: #050505; }

  /* Filters */
  .vs-filters {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .vs-filter-btn {
    padding: 6px 14px;
    border: 1px solid rgba(255,255,255,.1);
    background: #111;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 12px;
    color: #888;
    transition: all .15s;
    font-family: 'Sora', sans-serif;
  }
  .vs-filter-btn:hover { border-color: rgba(255,255,255,.25); color: #e8e8e8; }
  .vs-filter-btn.active { border-color: #22c55e; background: #22c55e; color: #050505; }

  /* Field cards */
  .vs-fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
  }

  .vs-field-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: border-color .2s, box-shadow .2s;
  }
  .vs-field-card:hover {
    border-color: rgba(34,197,94,.3);
    box-shadow: 0 8px 24px rgba(0,0,0,.3);
  }

  .vs-field-img-wrap {
    overflow: hidden;
    height: 150px;
  }

  .vs-field-img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    display: block;
    transition: transform .3s;
  }
  .vs-field-card:hover .vs-field-img { transform: scale(1.04); }

  .vs-field-img-placeholder {
    width: 100%;
    height: 150px;
    background: #1a1a1a;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .vs-field-body {
    padding: 16px 18px 18px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
  }

  .vs-field-name {
    font-size: 16px;
    font-weight: 700;
    color: #e8e8e8;
    margin: 0;
    letter-spacing: -0.01em;
  }

  .vs-field-badges {
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
  }

  .vs-badge {
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(255,255,255,.04);
    color: #a0a0a0;
    border: 1px solid rgba(255,255,255,.08);
  }

  .vs-badge-fu {
    background: rgba(34,197,94,.1);
    color: #6ee7a0;
    border-color: rgba(34,197,94,.2);
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .vs-fu-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #22c55e;
    animation: vs-dot-pulse 1.8s ease infinite;
  }

  @keyframes vs-dot-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .4; }
  }

  .vs-field-price {
    font-size: 20px;
    font-weight: 800;
    color: #e8e8e8;
    letter-spacing: -0.02em;
    line-height: 1;
  }

  .vs-field-price-unit {
    font-size: 12px;
    color: #666;
    font-weight: 400;
  }

  .vs-field-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: auto;
  }

  .vs-btn-primary {
    display: block;
    text-align: center;
    text-decoration: none;
    background: #22c55e;
    color: #050505;
    font-weight: 700;
    font-size: 13px;
    padding: 10px 16px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: background .15s;
    font-family: 'Sora', sans-serif;
  }
  .vs-btn-primary:hover { background: #16a34a; color: #050505; }

  .vs-btn-outline {
    display: block;
    text-align: center;
    text-decoration: none;
    background: transparent;
    color: #a0a0a0;
    font-weight: 600;
    font-size: 12px;
    padding: 8px 16px;
    border-radius: 10px;
    border: 1px solid rgba(255,255,255,.1);
    cursor: pointer;
    transition: border-color .15s, color .15s;
    font-family: 'Sora', sans-serif;
  }
  .vs-btn-outline:hover { border-color: rgba(255,255,255,.25); color: #e8e8e8; }

  /* ── REVIEWS ───────────────────────────────────────── */
  .vs-reviews {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    padding: 28px 32px;
  }

  .vs-reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 24px;
  }

  .vs-reviews-score {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .vs-reviews-avg {
    font-size: 40px;
    font-weight: 800;
    color: #e8e8e8;
    letter-spacing: -0.03em;
    line-height: 1;
  }

  .vs-reviews-stars {
    color: #e8e8e8;
    font-size: 16px;
    letter-spacing: 2px;
    line-height: 1;
  }

  .vs-reviews-count {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
  }

  .vs-review-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #22c55e;
    color: #050505;
    font-weight: 700;
    font-size: 13px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    transition: background .15s;
    font-family: 'Sora', sans-serif;
  }
  .vs-review-btn:hover { background: #16a34a; }

  /* Review form */
  .vs-review-form-wrap {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 14px;
    padding: 22px;
    margin-bottom: 20px;
  }

  .vs-form-label {
    display: block;
    font-size: 11px;
    color: #666;
    margin-bottom: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
  }

  .vs-review-textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 10px;
    background: #111;
    color: #e8e8e8;
    font-size: 14px;
    resize: vertical;
    font-family: 'Sora', sans-serif;
    outline: none;
    transition: border-color .15s;
    box-sizing: border-box;
  }
  .vs-review-textarea:focus { border-color: #22c55e; }

  /* Star picker */
  .vs-star-picker {
    display: flex;
    gap: 4px;
  }

  .vs-star-picker button {
    background: none;
    border: none;
    font-size: 22px;
    color: #555;
    cursor: pointer;
    padding: 0;
    transition: color .1s, transform .1s;
    line-height: 1;
  }
  .vs-star-picker button:hover { transform: scale(1.15); }
  .vs-star-picker button.active { color: #22c55e; }

  /* Review items */
  .vs-review-list {
    display: grid;
    gap: 10px;
  }

  .vs-review-item {
    padding: 16px 18px;
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 12px;
    transition: border-color .15s;
  }
  .vs-review-item:hover { border-color: rgba(255,255,255,.12); }

  .vs-review-head {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 8px;
  }

  .vs-review-user {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .vs-review-avatar {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
  }

  .vs-review-avatar-placeholder {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #1a1a1a;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .vs-review-name {
    font-weight: 700;
    font-size: 13px;
    color: #e8e8e8;
  }

  .vs-review-date {
    font-size: 11px;
    color: #555;
  }

  .vs-review-rating {
    font-size: 13px;
    color: #e8e8e8;
    letter-spacing: 1px;
  }

  .vs-review-comment {
    margin: 0;
    font-size: 13px;
    color: #a0a0a0;
    line-height: 1.6;
  }

  /* ── EMPTY STATE ───────────────────────────────────── */
  .vs-empty {
    text-align: center;
    padding: 40px 20px;
  }

  .vs-empty-title {
    font-weight: 700;
    font-size: 14px;
    color: #666;
    margin: 0 0 4px;
  }

  .vs-empty-sub {
    font-size: 13px;
    color: #555;
    margin: 0;
  }

  /* ── RESPONSIVE ────────────────────────────────────── */
  @media (max-width: 768px) {
    .vs-hero { height: 360px; border-radius: 18px; }
    .vs-hero-content { padding: 24px 20px; }
    .vs-top-bar { padding: 16px 20px; }
    .vs-hero-title { font-size: 30px; }
    .vs-info { grid-template-columns: 1fr; }
    .vs-info-main { padding: 22px; }
    .vs-stats { flex-wrap: wrap; }
    .vs-stat { min-width: 50%; }
    .vs-stat + .vs-stat::before { display: none; }
    .vs-fields-grid { grid-template-columns: 1fr; }
    .vs-reviews { padding: 22px; }
    .vs-cal-cta { flex-direction: column; align-items: stretch; text-align: center; }
  }
</style>
@endpush

@section('content')
@php
  use App\Http\Controllers\VenueAdmin\VenueController;
  $allAmenities   = VenueController::amenitiesList();
  $venueAmenities = $venue->amenities ?? [];
  $activeFields   = $venue->fields->where('is_active', true);
  $sports         = $activeFields->pluck('sport')->unique()->filter()->values();
  $roundedAverage = round($averageRating);
  $sportLabel = fn($s) => match($s) { 'football'=>'Futbol','padel'=>'Padel','tennis'=>'Tenis','basketball'=>'Basquet','volleyball'=>'Voley', default=>ucfirst($s) };
@endphp

<div class="vs">

{{-- ════════════════════════════════════════════════════════
     HERO
     ════════════════════════════════════════════════════════ --}}
<section class="vs-hero">
  @if($venue->cover_image_path)
    <img class="vs-hero-bg"
         src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
         alt="{{ $venue->name }}"
         loading="eager">
  @else
    <div class="vs-hero-bg-placeholder"></div>
  @endif

  <div class="vs-top-bar">
    <a href="{{ route('venues.index') }}" class="vs-back">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
      Volver
    </a>

    @auth
      @if(auth()->user()->favoriteVenues()->where('venues.id', $venue->id)->exists())
        <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
          @csrf
          <button type="submit" class="vs-fav-btn" title="Quitar de favoritos">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#ef4444" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          </button>
        </form>
      @else
        <form method="POST" action="{{ route('venues.favorite', $venue) }}" style="margin:0;">
          @csrf
          <button type="submit" class="vs-fav-btn" title="Guardar en favoritos">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          </button>
        </form>
      @endif
    @endauth
  </div>

  <div class="vs-hero-content">
    <h1 class="vs-hero-title">{{ $venue->name }}</h1>

    <div class="vs-hero-chips">
      @if($venue->zone)
        <span class="vs-chip">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          {{ $venue->zone }}
        </span>
      @endif
      @if($venue->address)
        <span class="vs-chip">{{ $venue->address }}</span>
      @endif
      @if($venue->phone)
        <a href="tel:{{ $venue->phone }}" class="vs-chip" style="text-decoration:none;">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          {{ $venue->phone }}
        </a>
      @endif

      @foreach($sports as $sport)
        <span class="vs-chip-accent vs-chip">{{ $sportLabel($sport) }}</span>
      @endforeach

      <span class="vs-chip">{{ $activeFields->count() }} {{ $activeFields->count() === 1 ? 'cancha' : 'canchas' }}</span>

      @if($venue->cancellation_hours)
        <span class="vs-chip vs-chip-warn">Cancelación hasta {{ $venue->cancellation_hours }}h antes</span>
      @endif
    </div>
  </div>
</section>

{{-- ════════════════════════════════════════════════════════
     INFO + MAP (side by side)
     ════════════════════════════════════════════════════════ --}}
<div class="vs-info">
  {{-- Left: stats + description + amenities --}}
  <div class="vs-info-main">
    {{-- Stats --}}
    <div class="vs-stats">
      <div class="vs-stat">
        <div class="vs-stat-value">{{ $activeFields->count() }}</div>
        <div class="vs-stat-label">Canchas</div>
      </div>
      @if($venue->reviews->count() > 0)
      <div class="vs-stat">
        <div class="vs-stat-value">{{ $averageRating }}</div>
        <div class="vs-stat-label">{{ $venue->reviews->count() }} {{ $venue->reviews->count() === 1 ? 'reseña' : 'reseñas' }}</div>
      </div>
      @endif
      @if(!empty($venueAmenities))
      <div class="vs-stat">
        <div class="vs-stat-value">{{ count($venueAmenities) }}</div>
        <div class="vs-stat-label">Servicios</div>
      </div>
      @endif
    </div>

    {{-- Description --}}
    @if($venue->description)
      <p class="vs-desc">{{ $venue->description }}</p>
    @endif

    {{-- Amenities --}}
    @if(!empty($venueAmenities))
      <div class="vs-amenities">
        @foreach($venueAmenities as $key)
          @if(isset($allAmenities[$key]))
            <span class="vs-amenity">{{ $allAmenities[$key]['label'] }}</span>
          @endif
        @endforeach
      </div>
    @endif

    {{-- Share --}}
    @php
      $waMsg = $venue->name . "\n"
             . ($venue->address ? $venue->address . "\n" : '')
             . "Deportes: " . $sports->map(fn($s) => $sportLabel($s))->implode(', ') . "\n\n"
             . "Reserva en " . route('venues.show', $venue);
    @endphp
    <div class="vs-share">
      <a href="https://wa.me/?text={{ urlencode($waMsg) }}" target="_blank" rel="noopener"
         class="vs-share-btn" style="background:#25D366; color:#fff;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="#fff"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.611.611l4.458-1.495A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.34 0-4.508-.654-6.363-1.787l-.362-.222-3.75 1.257 1.257-3.75-.222-.362A9.935 9.935 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
        Compartir
      </a>
    </div>
  </div>

  {{-- Right: map --}}
  <div class="vs-info-map">
    @if($venue->address)
      <iframe
        src="https://maps.google.com/maps?q={{ urlencode($venue->address) }}&z=16&output=embed"
        width="100%" height="100%" allowfullscreen loading="lazy">
      </iframe>
      <div class="vs-map-actions">
        <button onclick="openDirections()" class="vs-map-btn vs-map-btn-primary">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
          Como llegar
        </button>
        <a href="https://maps.google.com/?q={{ urlencode($venue->address) }}" target="_blank" rel="noopener" class="vs-map-btn vs-map-btn-ghost">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"/><line x1="8" x2="8" y1="2" y2="18"/><line x1="16" x2="16" y1="6" y2="22"/></svg>
          Ver en Maps
        </a>
      </div>
    @else
      <div style="display:flex;align-items:center;justify-content:center;height:100%;background:#0a0a0a;">
        <div style="text-align:center;color:#555;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 8px;display:block;"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          <div style="font-size:13px;font-weight:600;">Ubicación no disponible</div>
        </div>
      </div>
    @endif
  </div>
</div>

{{-- ════════════════════════════════════════════════════════
     FIELDS
     ════════════════════════════════════════════════════════ --}}
<div id="canchas" class="vs-section">
  <div class="vs-section-header">
    <h2 class="vs-section-title">Canchas</h2>
    <p class="vs-section-sub">Elegí una cancha y revisa la disponibilidad en tiempo real</p>
  </div>

  {{-- Calendar CTA --}}
  @if($activeFields->count() > 0)
    <div class="vs-cal-cta">
      <div>
        <div class="vs-cal-cta-text">Disponibilidad semanal</div>
        <div class="vs-cal-cta-sub">Todas las canchas y horarios de la semana</div>
      </div>
      <a href="{{ route('venues.weekly-calendar', $venue) }}" class="vs-cal-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
        Ver calendario
      </a>
    </div>
  @endif

  @if($activeFields->isEmpty())
    <div class="vs-empty">
      <div class="vs-empty-title">Este complejo todavía no tiene canchas cargadas</div>
      <div class="vs-empty-sub">Volvé más tarde o explorá otros complejos</div>
      <a href="{{ route('venues.index') }}" class="vs-btn-primary" style="display:inline-block;margin-top:14px;padding:10px 20px;">Ver otros complejos</a>
    </div>
  @else
    {{-- Filters --}}
    @if($sports->count() > 1)
      <div class="vs-filters">
        <button onclick="vsFilterSport(null)" class="vs-filter-btn active" data-sport="">Todos</button>
        @foreach($sports as $sport)
          <button onclick="vsFilterSport('{{ $sport }}')" class="vs-filter-btn" data-sport="{{ $sport }}">
            {{ $sportLabel($sport) }}
          </button>
        @endforeach
      </div>
    @endif

    <div class="vs-fields-grid">
      @foreach($activeFields as $i => $field)
        <article class="vs-field-card" data-sport="{{ $field->sport }}">
          <div class="vs-field-img-wrap">
            @if($field->cover_image_path)
              <img class="vs-field-img"
                   src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
                   alt="{{ $field->name }}"
                   loading="lazy">
            @else
              <div class="vs-field-img-placeholder">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
              </div>
            @endif
          </div>

          <div class="vs-field-body">
            <h3 class="vs-field-name">{{ $field->name }}</h3>

            <div class="vs-field-badges">
              <span class="vs-badge">{{ $sportLabel($field->sport ?? '') }}</span>
              <span class="vs-badge">{{ $field->format ?? '?' }}v{{ $field->format ?? '?' }}</span>
              <span class="vs-badge">{{ $field->slot_minutes }} min</span>
              @if($field->faltaUnoSetting?->enabled)
                <span class="vs-badge vs-badge-fu">
                  <span class="vs-fu-dot"></span> Falta Uno
                </span>
              @endif
            </div>

            <div class="vs-field-price">
              {{ $field->price->currency ?? 'ARS' }} {{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }}
              <span class="vs-field-price-unit">/ turno</span>
            </div>

            <div class="vs-field-actions">
              <a href="{{ route('fields.show', $field) }}" class="vs-btn-primary">Ver disponibilidad</a>
              @if($field->faltaUnoSetting?->enabled)
                <a href="{{ route('falta-uno.create', $field) }}" class="vs-btn-outline">
                  Iniciar partido Falta Uno
                </a>
              @endif
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif
</div>

{{-- ════════════════════════════════════════════════════════
     REVIEWS
     ════════════════════════════════════════════════════════ --}}
<div class="vs-reviews">
  <div class="vs-reviews-header">
    <div>
      <h2 class="vs-section-title" style="margin-bottom:12px;">Reseñas</h2>
      @if($venue->reviews->count() > 0)
        <div class="vs-reviews-score">
          <span class="vs-reviews-avg">{{ $averageRating }}</span>
          <div>
            <div class="vs-reviews-stars">
              @for($i = 1; $i <= 5; $i++){!! $i <= $roundedAverage ? '&#9733;' : '&#9734;' !!}@endfor
            </div>
            <div class="vs-reviews-count">{{ $venue->reviews->count() }} {{ $venue->reviews->count() === 1 ? 'reseña' : 'reseñas' }}</div>
          </div>
        </div>
      @endif
    </div>
    @auth
      <button type="button" class="vs-review-btn" onclick="vsToggleReviewForm()">Escribir reseña</button>
    @endauth
  </div>

  {{-- Review form --}}
  @auth
    <div id="vsReviewFormWrap" style="display:none;">
      <div class="vs-review-form-wrap">
        <form method="POST" action="{{ route('venues.reviews.store', $venue) }}" style="display:grid; gap:14px; max-width:480px;">
          @csrf
          <div>
            <label class="vs-form-label">Puntuación</label>
            <input type="hidden" name="rating" id="vsRatingInput" value="{{ old('rating', '') }}" required>
            <div class="vs-star-picker" id="vsReviewStars">
              @for($i = 1; $i <= 5; $i++)
                <button type="button" data-value="{{ $i }}" aria-label="Puntuar con {{ $i }} estrella{{ $i > 1 ? 's' : '' }}">{!! '&#9733;' !!}</button>
              @endfor
            </div>
            <div id="vsRatingText" style="color:#555; font-size:12px; margin-top:6px;">Seleccioná una puntuación</div>
            @error('rating')<div style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="vs-form-label">Comentario</label>
            <textarea name="comment" rows="3" class="vs-review-textarea">{{ old('comment', '') }}</textarea>
            @error('comment')<div style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</div>@enderror
          </div>
          <div style="display:flex; gap:8px;">
            <button type="submit" class="vs-review-btn">Publicar</button>
            <button type="button" onclick="vsToggleReviewForm(false)" style="padding:8px 16px; background:transparent; border:1px solid rgba(255,255,255,.1); border-radius:8px; font-size:13px; font-weight:600; color:#888; cursor:pointer; font-family:'Sora',sans-serif;">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  @endauth

  {{-- Reviews list --}}
  <div class="vs-review-list">
    @forelse($venue->reviews->sortByDesc('created_at') as $review)
      <div class="vs-review-item">
        <div class="vs-review-head">
          <div class="vs-review-user">
            @if($review->user->avatar_path)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($review->user->avatar_path) }}"
                   alt="{{ $review->user->name }}"
                   class="vs-review-avatar">
            @else
              <div class="vs-review-avatar-placeholder">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
            @endif
            <div>
              <div class="vs-review-name">{{ $review->user->name }}</div>
              <div class="vs-review-date">{{ $review->created_at->format('d/m/Y') }}</div>
            </div>
          </div>
          <div class="vs-review-rating">
            @for($i = 1; $i <= 5; $i++){!! $i <= $review->rating ? '&#9733;' : '&#9734;' !!}@endfor
          </div>
        </div>
        @if($review->comment)
          <p class="vs-review-comment">{{ $review->comment }}</p>
        @endif
      </div>
    @empty
      <div class="vs-empty">
        <div class="vs-empty-title">Todavía no hay reseñas</div>
        <div class="vs-empty-sub">Sé el primero en dejar tu opinión sobre este complejo</div>
      </div>
    @endforelse
  </div>
</div>

</div>{{-- /.vs --}}

@push('scripts')
<script>
  // Directions
  function openDirections() {
    var dest = encodeURIComponent('{{ addslashes($venue->address ?? '') }}');
    if (!dest) return;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(pos) {
          window.open('https://www.google.com/maps/dir/?api=1&origin=' + pos.coords.latitude + ',' + pos.coords.longitude + '&destination=' + dest + '&travelmode=driving', '_blank');
        },
        function() {
          window.open('https://www.google.com/maps/dir/?api=1&destination=' + dest + '&travelmode=driving', '_blank');
        }
      );
    } else {
      window.open('https://www.google.com/maps/dir/?api=1&destination=' + dest + '&travelmode=driving', '_blank');
    }
  }

  // Sport filter
  function vsFilterSport(sport) {
    document.querySelectorAll('.vs-field-card[data-sport]').forEach(function(c) {
      c.style.display = (!sport || c.dataset.sport === sport) ? '' : 'none';
    });
    document.querySelectorAll('.vs-filter-btn').forEach(function(b) {
      b.classList.toggle('active', b.dataset.sport === (sport || ''));
    });
  }

  // Review form toggle
  function vsToggleReviewForm(forceState) {
    var wrap = document.getElementById('vsReviewFormWrap');
    if (!wrap) return;
    if (forceState === false) { wrap.style.display = 'none'; return; }
    wrap.style.display = wrap.style.display === 'none' || wrap.style.display === '' ? 'block' : 'none';
  }

  // Star picker
  (function () {
    var input = document.getElementById('vsRatingInput');
    var starsWrap = document.getElementById('vsReviewStars');
    var ratingText = document.getElementById('vsRatingText');
    if (!input || !starsWrap || !ratingText) return;
    var buttons = Array.from(starsWrap.querySelectorAll('button'));

    function paint(v) {
      buttons.forEach(function(b, i) { b.classList.toggle('active', i < v); });
      ratingText.innerText = v ? v + (v > 1 ? ' estrellas' : ' estrella') : 'Seleccioná una puntuación';
    }

    buttons.forEach(function(b) {
      b.addEventListener('click', function() {
        input.value = Number(b.dataset.value);
        paint(Number(b.dataset.value));
      });
    });

    paint(Number(input.value || 0));

    @if($errors->has('rating') || $errors->has('comment'))
      document.getElementById('vsReviewFormWrap').style.display = 'block';
    @endif
  })();
</script>
@endpush

@endsection
