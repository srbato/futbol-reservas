@extends('layouts.app')

@section('title', $venue->name)

@section('content')
  <div class="page-card" style="padding:0; overflow:hidden; margin-bottom:22px;">
    @if($venue->cover_image_path)
      <img
        src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
        alt="{{ $venue->name }}"
        style="width:100%; height:360px; object-fit:cover; display:block;"
      >
    @else
      <div style="height:260px; background:#efefef; display:flex; align-items:center; justify-content:center; color:#777;">
        Sin imagen del complejo
      </div>
    @endif

    <div style="padding:24px;">
      <p style="margin:0 0 12px 0;">
        <a href="{{ route('venues.index') }}" class="btn">← Volver a complejos</a>
      </p>

      <div style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap; align-items:flex-start;">
        <div style="max-width:780px;">
          <h1 style="margin:0 0 10px 0; font-size:40px; letter-spacing:-0.02em;">
            {{ $venue->name }}
          </h1>

          <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:14px;">
            @if($venue->zone)
              <span class="badge">Zona: {{ $venue->zone }}</span>
            @endif

            @if($venue->address)
              <span class="badge">Dirección: {{ $venue->address }}</span>
            @endif
          </div>

          <p class="muted" style="margin:0; font-size:16px; line-height:1.6;">
            {{ $venue->description ?? 'Consultá las canchas disponibles y reservá online en pocos pasos.' }}
          </p>
        </div>

        <div class="page-card" style="min-width:240px; padding:18px;">
          <div style="font-size:12px; color:#666; margin-bottom:6px;">Canchas disponibles</div>
          <div style="font-size:36px; font-weight:800; line-height:1;">
              {{ $venue->fields->where('is_active', true)->count() }}
          </div>
        </div>
      </div>
    </div>
  </div>

<h2 class="section-title">Canchas del complejo</h2>

  @php
      $activeFields = $venue->fields->where('is_active', true);
      $sports = $activeFields->pluck('sport')->unique()->filter()->values();
  @endphp

  @if($activeFields->isEmpty())
      <div class="page-card">
          <p class="muted" style="margin:0;">Este complejo todavía no tiene canchas cargadas.</p>
      </div>
  @else
      @if($sports->count() > 1)
          <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px;">
              <button onclick="filterSport(null)" class="sport-filter-btn active" data-sport="">
                  Todos
              </button>
              @foreach($sports as $sport)
                  <button onclick="filterSport('{{ $sport }}')" class="sport-filter-btn" data-sport="{{ $sport }}">
                      {{ ucfirst($sport) }}
                  </button>
              @endforeach
          </div>
      @endif

      <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));">
      @foreach ($activeFields as $field)
        <article class="venue-card" data-sport="{{ $field->sport }}">
          @if($field->cover_image_path)
            <img
              src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
              alt="{{ $field->name }}"
              class="venue-card-image"
            >
          @else
            <div class="venue-card-image" style="display:flex; align-items:center; justify-content:center; color:#777;">
              Sin imagen
            </div>
          @endif

          <div class="venue-card-body">
            <div>
              <h3>{{ $field->name }}</h3>
            </div>

            <div style="display:flex; gap:8px; flex-wrap:wrap;">
              <span class="badge">
                {{ ucfirst($field->sport ?? 'cancha') }}
              </span>

              <span class="badge">
                Formato: {{ $field->format ?? '?' }}
              </span>

              <span class="badge">
                {{ $field->slot_minutes }} min
              </span>
            </div>

            <div class="muted" style="line-height:1.5;">
              Precio por turno:
              <strong style="color:#111;">
                {{ $field->price->currency ?? 'ARS' }} {{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }}
              </strong>
            </div>

            <div style="margin-top:auto;">
              <a href="{{ route('fields.show', $field) }}" class="btn btn-primary">
                Ver disponibilidad
              </a>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif

  <hr style="margin:28px 0; border:none; border-top:1px solid #eee;">

  <div class="page-card">
    <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-start;">
      <div>
        <h2 style="margin:0 0 8px 0;">Reseñas</h2>

        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
          <span class="stars">
            @php $roundedAverage = round($averageRating); @endphp
            @for($i = 1; $i <= 5; $i++)
              {{ $i <= $roundedAverage ? '★' : '☆' }}
            @endfor
          </span>

          <span class="muted">
            <strong style="color:#111;">{{ $averageRating }}/5</strong>
            ({{ $venue->reviews->count() }} reseñas)
          </span>
        </div>
      </div>

      @auth
        <button type="button" class="btn btn-primary" onclick="toggleReviewForm()">
          Escribir reseña
        </button>
      @endauth
    </div>

    @auth
      <div id="reviewFormWrap" style="display:none; margin-top:18px; padding-top:18px; border-top:1px solid #eee;">
        <h3 style="margin-top:0;">Publicar reseña</h3>

        <form method="POST" action="{{ route('venues.reviews.store', $venue) }}" style="display:grid; gap:12px; max-width:600px;">
          @csrf

          <div>
            <label style="display:block; font-size:13px; color:#666; margin-bottom:8px;">Puntuación</label>

            <input
              type="hidden"
              name="rating"
              id="ratingInput"
              value="{{ old('rating', '') }}"
              required
            >

            <div class="review-stars-picker" id="reviewStars">
              @for($i = 1; $i <= 5; $i++)
                <button type="button" data-value="{{ $i }}" aria-label="Puntuar con {{ $i }} estrella{{ $i > 1 ? 's' : '' }}">
                  ★
                </button>
              @endfor
            </div>

            <div id="ratingText" class="review-rating-text">
              Seleccioná una puntuación
            </div>

            @error('rating')
              <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
          </div>

          <div>
            <label style="display:block; font-size:13px; color:#666; margin-bottom:6px;">Comentario</label>
            <textarea
              name="comment"
              rows="4"
              style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:12px;"
            >{{ old('comment', '') }}</textarea>

            @error('comment')
              <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
          </div>

          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <button type="submit" class="btn btn-primary">Publicar reseña</button>
            <button type="button" class="btn" onclick="toggleReviewForm(false)">Cancelar</button>
          </div>
        </form>
      </div>
    @endauth

    <div style="margin-top:24px; display:grid; gap:14px;">
      @forelse($venue->reviews->sortByDesc('created_at') as $review)
        <div style="padding:14px; border:1px solid #eee; border-radius:14px;">
          <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:6px;">
            <div style="display:flex; align-items:center; gap:8px;">
              @if($review->user->avatar_path)
                  <img
                      src="{{ \Illuminate\Support\Facades\Storage::url($review->user->avatar_path) }}"
                      alt="{{ $review->user->name }}"
                      style="width:32px; height:32px; border-radius:999px; object-fit:cover; border:1px solid #eee;"
                  >
              @else
                  <div style="width:32px; height:32px; border-radius:999px; background:#f1f1f1; display:flex; align-items:center; justify-content:center; font-size:13px; color:#999; border:1px solid #eee; flex-shrink:0;">
                      👤
                  </div>
              @endif
              <strong>{{ $review->user->name }}</strong>
          </div>

            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
              <span class="stars">
                @for($i = 1; $i <= 5; $i++)
                  {{ $i <= $review->rating ? '★' : '☆' }}
                @endfor
              </span>
              <span class="muted">{{ $review->rating }}/5</span>
            </div>
          </div>

          @if($review->comment)
            <div class="muted" style="line-height:1.6;">
              {{ $review->comment }}
            </div>
          @endif

          <div style="margin-top:8px; font-size:12px; color:#777;">
            {{ $review->created_at->format('d/m/Y H:i') }}
          </div>
        </div>
      @empty
        <p class="muted" style="margin:0;">Todavía no hay reseñas para este complejo.</p>
      @endforelse
    </div>
  </div>

  <style>
      .sport-filter-btn {
          padding: 8px 16px;
          border: 1px solid #ddd;
          background: #fff;
          border-radius: 999px;
          cursor: pointer;
          font-weight: 600;
          font-size: 13px;
          transition: .2s ease;
      }
      .sport-filter-btn:hover {
          background: #f7f7f7;
      }
      .sport-filter-btn.active {
          background: #111;
          color: #fff;
          border-color: #111;
      }
  </style>


  <script>
    function toggleReviewForm(forceState = null) {
      const wrap = document.getElementById('reviewFormWrap');
      if (!wrap) return;

      if (forceState === false) {
        wrap.style.display = 'none';
        return;
      }

      wrap.style.display = wrap.style.display === 'none' || wrap.style.display === '' ? 'block' : 'none';
    }

    (function () {
      const input = document.getElementById('ratingInput');
      const starsWrap = document.getElementById('reviewStars');
      const ratingText = document.getElementById('ratingText');

      if (!input || !starsWrap || !ratingText) return;

      const buttons = Array.from(starsWrap.querySelectorAll('button'));

      function getLabel(value) {
        if (!value) return 'Seleccioná una puntuación';
        return value + ' estrella' + (value > 1 ? 's' : '');
      }

      function paint(value) {
        buttons.forEach((btn, index) => {
          btn.classList.toggle('active', index < value);
        });
        ratingText.innerText = getLabel(value);
      }

      buttons.forEach(btn => {
        btn.addEventListener('click', () => {
          const value = Number(btn.dataset.value);
          input.value = value;
          paint(value);
        });
      });

      paint(Number(input.value || 0));

      @if($errors->has('rating') || $errors->has('comment'))
        document.getElementById('reviewFormWrap').style.display = 'block';
      @endif
    })();


    function filterSport(sport) {
        const cards = document.querySelectorAll('.venue-card[data-sport]');
        const btns  = document.querySelectorAll('.sport-filter-btn');

        cards.forEach(card => {
            card.style.display = (!sport || card.dataset.sport === sport) ? '' : 'none';
        });

        btns.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.sport === (sport ?? ''));
        });
    }

  </script>
@endsection
