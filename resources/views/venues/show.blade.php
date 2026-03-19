@extends('layouts.app')

@section('title', $venue->name)

@push('styles')
<style>
  .venue-header-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
  }

  .venue-hero {
    position: relative;
    height: 300px;
    overflow: hidden;
  }

  .venue-thumb {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .venue-thumb-placeholder {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #2a2a2a, #444);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 56px;
  }

  .venue-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,.0) 0%, rgba(0,0,0,.75) 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 18px 22px;
    gap: 6px;
  }

  .venue-header-back {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: rgba(255,255,255,.75);
    text-decoration: none;
    font-weight: 600;
    transition: color .15s;
    align-self: flex-start;
  }
  .venue-header-back:hover { color: #fff; }

  .venue-header-title {
    font-size: 26px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.02em;
    line-height: 1.2;
    color: #fff;
    text-shadow: 0 1px 4px rgba(0,0,0,.3);
  }

  .venue-header-body {
    padding: 16px 22px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .venue-meta-row {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .venue-meta-chip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 11px;
    background: #f4f4f4;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
    color: #555;
  }

  .venue-meta-chip.amber {
    background: #fef3c7;
    color: #92400e;
  }

  .venue-description {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
    margin: 0;
  }

  .amenity-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    background: #f5f5f5;
    border: 1px solid #eaeaea;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
    color: #444;
  }

  /* Stats */
  .venue-stats-bar {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .venue-stat {
    flex: 1;
    min-width: 110px;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 14px;
    padding: 14px 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
  }

  .venue-stat-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #aaa;
    margin-bottom: 4px;
  }

  .venue-stat-value {
    font-size: 26px;
    font-weight: 800;
    color: #111;
    line-height: 1;
  }

  .venue-stat-sub {
    font-size: 12px;
    color: #aaa;
    margin-top: 3px;
  }

  /* Field cards */
  .fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 14px;
    margin-bottom: 8px;
  }

  .field-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    display: flex;
    flex-direction: column;
    transition: box-shadow .2s, transform .2s;
  }

  .field-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.09);
    transform: translateY(-2px);
  }

  .field-card-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    display: block;
  }

  .field-card-img-placeholder {
    width: 100%;
    height: 160px;
    background: linear-gradient(135deg, #f5f5f5, #ebebeb);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
  }

  .field-card-body {
    padding: 16px 18px 18px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
  }

  .field-card-name {
    font-size: 17px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.01em;
  }

  .field-card-badges {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
  }

  .field-badge {
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
  }

  .field-badge.sport  { background: #dbeafe; color: #1d4ed8; }
  .field-badge.format { background: #dcfce7; color: #166534; }
  .field-badge.time   { background: #fef3c7; color: #92400e; }

  .field-card-price {
    font-size: 20px;
    font-weight: 800;
    color: #111;
  }

  .field-card-price span {
    font-size: 12px;
    color: #aaa;
    font-weight: 400;
  }

  /* Sport filter */
  .sport-filter-btn {
    padding: 7px 16px;
    border: 1.5px solid #e0e0e0;
    background: #fff;
    border-radius: 999px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: all .15s;
    color: #555;
  }
  .sport-filter-btn:hover  { border-color: #bbb; }
  .sport-filter-btn.active { background: #111; color: #fff; border-color: #111; }

  /* Reviews */
  .reviews-section {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    padding: 22px 24px;
    margin-top: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
  }

  .review-item {
    padding: 14px;
    border: 1px solid #f0f0f0;
    border-radius: 14px;
  }

  @media (max-width: 640px) {
    .venue-hero { height: 160px; }
    .venue-header-title { font-size: 20px; }
    /* Stats: una fila horizontal, scroll si no entran */
    .venue-stats-bar { flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .venue-stat { min-width: 90px; flex-shrink: 0; padding: 10px 12px; }
    .venue-stat-value { font-size: 22px; }
    /* Meta chips: en fila, scroll si no entran */
    .venue-meta-row { flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .venue-meta-chip { font-size: 11px; padding: 3px 9px; white-space: nowrap; flex-shrink: 0; }
    /* Amenity pills: grid de 2 columnas */
    .amenity-pills-grid { grid-template-columns: 1fr 1fr !important; }
    .fields-grid { grid-template-columns: 1fr; }
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
@endphp

{{-- Header card --}}
<div class="venue-header-card">
  <div class="venue-hero">
    @if($venue->cover_image_path)
      <img class="venue-thumb"
           src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
           alt="{{ $venue->name }}">
    @else
      <div class="venue-thumb-placeholder">🏟️</div>
    @endif

    <div class="venue-hero-overlay">
      <a href="{{ route('venues.index') }}" class="venue-header-back">← Volver a complejos</a>
      <h1 class="venue-header-title">{{ $venue->name }}</h1>
    </div>
  </div>

  <div class="venue-header-body">
    <div class="venue-meta-row">
      @if($venue->zone)
        <span class="venue-meta-chip">📍 {{ $venue->zone }}</span>
      @endif
      @if($venue->address)
        <span class="venue-meta-chip">🏠 {{ $venue->address }}</span>
      @endif
      @if($venue->cancellation_hours)
        <span class="venue-meta-chip amber">⏱ Cancelaciones hasta {{ $venue->cancellation_hours }}h antes</span>
      @endif
    </div>

    @if($venue->description)
      <p class="venue-description">{{ $venue->description }}</p>
    @endif

    @if(!empty($venueAmenities))
      <div class="amenity-pills-grid" style="display:grid; grid-template-columns: repeat(3, auto); gap:6px; justify-content:start;">
        @foreach($venueAmenities as $key)
          @if(isset($allAmenities[$key]))
            <span class="amenity-pill">{{ $allAmenities[$key]['emoji'] }} {{ $allAmenities[$key]['label'] }}</span>
          @endif
        @endforeach
      </div>
    @endif
  </div>
</div>

{{-- Stats --}}
<div class="venue-stats-bar">
  <div class="venue-stat">
    <div class="venue-stat-label">⚽ Canchas</div>
    <div class="venue-stat-value">{{ $activeFields->count() }}</div>
    <div class="venue-stat-sub">disponibles</div>
  </div>
  @if($venue->reviews->count() > 0)
  <div class="venue-stat">
    <div class="venue-stat-label">⭐ Calificación</div>
    <div class="venue-stat-value">{{ $averageRating }}</div>
    <div class="venue-stat-sub">{{ $venue->reviews->count() }} reseñas</div>
  </div>
  @endif
  @if(!empty($venueAmenities))
  <div class="venue-stat">
    <div class="venue-stat-label">✅ Servicios</div>
    <div class="venue-stat-value">{{ count($venueAmenities) }}</div>
    <div class="venue-stat-sub">instalaciones</div>
  </div>
  @endif
</div>

{{-- Fields --}}
<h2 style="font-size:20px; font-weight:800; margin:0 0 14px 0; letter-spacing:-.01em;">Canchas del complejo</h2>

@if($activeFields->isEmpty())
  <div class="page-card">
    <p class="muted" style="margin:0; text-align:center; padding:24px 0;">Este complejo todavía no tiene canchas cargadas.</p>
  </div>
@else
  @if($sports->count() > 1)
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px;">
      <button onclick="filterSport(null)" class="sport-filter-btn active" data-sport="">Todos</button>
      @foreach($sports as $sport)
        <button onclick="filterSport('{{ $sport }}')" class="sport-filter-btn" data-sport="{{ $sport }}">{{ ucfirst($sport) }}</button>
      @endforeach
    </div>
  @endif

  <div class="fields-grid">
    @foreach($activeFields as $field)
      <article class="field-card" data-sport="{{ $field->sport }}">
        @if($field->cover_image_path)
          <img class="field-card-img"
               src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
               alt="{{ $field->name }}">
        @else
          <div class="field-card-img-placeholder">⚽</div>
        @endif

        <div class="field-card-body">
          <h3 class="field-card-name">{{ $field->name }}</h3>

          <div class="field-card-badges">
            <span class="field-badge sport">{{ match($field->sport ?? '') { 'football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley', default=>ucfirst($field->sport ?? 'Cancha') } }}</span>
            <span class="field-badge format">{{ $field->format ?? '?' }}v{{ $field->format ?? '?' }}</span>
            <span class="field-badge time">{{ $field->slot_minutes }} min</span>
          </div>

          <div class="field-card-price">
            {{ $field->price->currency ?? 'ARS' }} {{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }}
            <span>/ turno</span>
          </div>

          <a href="{{ route('fields.show', $field) }}" class="btn btn-primary" style="margin-top:4px; text-align:center;">
            Ver disponibilidad →
          </a>
        </div>
      </article>
    @endforeach
  </div>
@endif

{{-- Reviews --}}
<div class="reviews-section">
  <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:18px;">
    <div>
      <h2 style="margin:0 0 6px 0; font-size:20px; font-weight:800;">Reseñas</h2>
      <div style="display:flex; align-items:center; gap:8px;">
        <span style="color:#f59e0b; font-size:18px;">
          @for($i = 1; $i <= 5; $i++){{ $i <= $roundedAverage ? '★' : '☆' }}@endfor
        </span>
        <span style="font-weight:700;">{{ $averageRating }}/5</span>
        <span class="muted" style="font-size:13px;">({{ $venue->reviews->count() }} reseñas)</span>
      </div>
    </div>
    @auth
      <button type="button" class="btn btn-primary" onclick="toggleReviewForm()">✏️ Escribir reseña</button>
    @endauth
  </div>

  @auth
    <div id="reviewFormWrap" style="display:none; margin-bottom:20px; padding:18px; background:#f9f9f9; border-radius:14px;">
      <form method="POST" action="{{ route('venues.reviews.store', $venue) }}" style="display:grid; gap:12px; max-width:540px;">
        @csrf
        <div>
          <label style="display:block; font-size:13px; color:#666; margin-bottom:8px; font-weight:600;">Puntuación</label>
          <input type="hidden" name="rating" id="ratingInput" value="{{ old('rating', '') }}" required>
          <div class="review-stars-picker" id="reviewStars">
            @for($i = 1; $i <= 5; $i++)
              <button type="button" data-value="{{ $i }}" aria-label="Puntuar con {{ $i }} estrella{{ $i > 1 ? 's' : '' }}">★</button>
            @endfor
          </div>
          <div id="ratingText" class="review-rating-text">Seleccioná una puntuación</div>
          @error('rating')<div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
        </div>
        <div>
          <label style="display:block; font-size:13px; color:#666; margin-bottom:6px; font-weight:600;">Comentario</label>
          <textarea name="comment" rows="3" style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:12px; font-size:14px; resize:vertical;">{{ old('comment', '') }}</textarea>
          @error('comment')<div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
        </div>
        <div style="display:flex; gap:10px;">
          <button type="submit" class="btn btn-primary">Publicar reseña</button>
          <button type="button" class="btn" onclick="toggleReviewForm(false)">Cancelar</button>
        </div>
      </form>
    </div>
  @endauth

  <div style="display:grid; gap:10px;">
    @forelse($venue->reviews->sortByDesc('created_at') as $review)
      <div class="review-item">
        <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:8px;">
          <div style="display:flex; align-items:center; gap:10px;">
            @if($review->user->avatar_path)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($review->user->avatar_path) }}" alt="{{ $review->user->name }}"
                   style="width:34px; height:34px; border-radius:999px; object-fit:cover; border:2px solid #eee;">
            @else
              <div style="width:34px; height:34px; border-radius:999px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0;">👤</div>
            @endif
            <div>
              <div style="font-weight:700; font-size:14px;">{{ $review->user->name }}</div>
              <div style="font-size:12px; color:#aaa;">{{ $review->created_at->format('d/m/Y') }}</div>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:5px;">
            <span style="color:#f59e0b; font-size:14px;">
              @for($i = 1; $i <= 5; $i++){{ $i <= $review->rating ? '★' : '☆' }}@endfor
            </span>
            <span style="font-size:13px; font-weight:700;">{{ $review->rating }}/5</span>
          </div>
        </div>
        @if($review->comment)
          <p style="margin:0; font-size:14px; color:#555; line-height:1.6;">{{ $review->comment }}</p>
        @endif
      </div>
    @empty
      <div style="text-align:center; padding:28px 0; color:#bbb;">
        <div style="font-size:32px; margin-bottom:6px;">💬</div>
        <p style="margin:0; font-size:14px;">Todavía no hay reseñas.</p>
      </div>
    @endforelse
  </div>
</div>

<script>
  function toggleReviewForm(forceState = null) {
    const wrap = document.getElementById('reviewFormWrap');
    if (!wrap) return;
    if (forceState === false) { wrap.style.display = 'none'; return; }
    wrap.style.display = wrap.style.display === 'none' || wrap.style.display === '' ? 'block' : 'none';
  }

  (function () {
    const input = document.getElementById('ratingInput');
    const starsWrap = document.getElementById('reviewStars');
    const ratingText = document.getElementById('ratingText');
    if (!input || !starsWrap || !ratingText) return;
    const buttons = Array.from(starsWrap.querySelectorAll('button'));
    function getLabel(v) { return v ? v + ' estrella' + (v > 1 ? 's' : '') : 'Seleccioná una puntuación'; }
    function paint(v) { buttons.forEach((b, i) => b.classList.toggle('active', i < v)); ratingText.innerText = getLabel(v); }
    buttons.forEach(b => b.addEventListener('click', () => { input.value = Number(b.dataset.value); paint(Number(b.dataset.value)); }));
    paint(Number(input.value || 0));
    @if($errors->has('rating') || $errors->has('comment'))
      document.getElementById('reviewFormWrap').style.display = 'block';
    @endif
  })();

  function filterSport(sport) {
    document.querySelectorAll('.field-card[data-sport]').forEach(c => { c.style.display = (!sport || c.dataset.sport === sport) ? '' : 'none'; });
    document.querySelectorAll('.sport-filter-btn').forEach(b => { b.classList.toggle('active', b.dataset.sport === (sport ?? '')); });
  }
</script>
@endsection
