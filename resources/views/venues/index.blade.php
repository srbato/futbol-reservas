@extends('layouts.app')

@section('title','Complejos')

@section('content')
  <section class="hero">
    <div>
      <h1>Reservá tu cancha en segundos</h1>
      <p>
        Encontrá complejos, filtrá por zona o deporte y reservá online con pago integrado.
      </p>
    </div>

    <div class="hero-box">
      <strong>{{ $venues->count() }}</strong>
      <div style="font-size:16px; color:rgba(255,255,255,.82);">
        complejos disponibles para reservar
      </div>

      <div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap;">
        <a href="#complejos" class="btn btn-primary" style="background:#fff; color:#111; border-color:#fff;">
          Explorar complejos
        </a>
      </div>
    </div>
  </section>

  <div class="page-card" style="margin-bottom:26px; padding:22px;">
    <section class="carousel-section" style="margin-bottom:0;">

      <div class="carousel-header" style="margin-bottom:16px;">
        <div>
          <h2 class="section-title" style="margin:0 0 6px 0;">Destacados</h2>
          <div class="carousel-subtitle">
            Descubrí los complejos con más movimiento, promociones activas y mejores reseñas.
          </div>
        </div>
      </div>

      <div class="feature-tabs-wrap">
        <div class="feature-tabs">
          <div class="feature-tab active" data-tab="top">Más reservados</div>
          <div class="feature-tab" data-tab="discounts">Descuentos</div>
          <div class="feature-tab" data-tab="rated">Mejor valorados</div>
        </div>

        <div class="featured-nav-arrows">
          <button type="button" class="featured-nav-arrow" data-carousel-move="prev" aria-label="Mover carrusel a la izquierda">&lsaquo;</button>
          <button type="button" class="featured-nav-arrow" data-carousel-move="next" aria-label="Mover carrusel a la derecha">&rsaquo;</button>
        </div>
      </div>

      <div class="feature-carousel active" id="tab-top">
        <div class="feature-carousel-shell">
          <div class="carousel-track featured-track" data-carousel-track>
            @foreach($topReservedVenues as $venue)
              <article class="carousel-card featured-card">
                @if($venue->cover_image_path)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
                @else
                  <div class="carousel-image-placeholder" style="display:flex; align-items:center; justify-content:center; color:#777;">
                    Sin imagen
                  </div>
                @endif

                <div class="carousel-card-body">
                  <h3>{{ $venue->name }}</h3>

                  @if($venue->zone)
                    <div class="badge">Zona: {{ $venue->zone }}</div>
                  @endif

                  <div class="carousel-meta">
                    🔥 {{ $venue->weekly_reservations_count }} reserva{{ $venue->weekly_reservations_count > 1 ? 's' : '' }} esta semana
                  </div>

                  <a href="{{ route('venues.show', $venue) }}" class="btn btn-primary venue-btn">
                    Ver complejo
                  </a>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </div>

      <div class="feature-carousel" id="tab-discounts">
        <div class="feature-carousel-shell">
          <div class="carousel-track featured-track" data-carousel-track>
            @foreach($discountedVenues as $venue)
              <article class="carousel-card featured-card">
                @if($venue->cover_image_path)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
                @else
                  <div class="carousel-image-placeholder" style="display:flex; align-items:center; justify-content:center; color:#777;">
                    Sin imagen
                  </div>
                @endif

                <div class="carousel-card-body">
                  <h3>{{ $venue->name }}</h3>

                  @if($venue->zone)
                    <div class="badge">Zona: {{ $venue->zone }}</div>
                  @endif

                  <div class="carousel-meta">
                    💸 Descuentos activos
                  </div>

                  <a href="{{ route('venues.show', $venue) }}" class="btn btn-primary venue-btn">
                    Ver complejo
                  </a>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </div>

      <div class="feature-carousel" id="tab-rated">
        <div class="feature-carousel-shell">
          <div class="carousel-track featured-track" data-carousel-track>
            @foreach($bestRatedVenues as $venue)
              <article class="carousel-card featured-card">
                @if($venue->cover_image_path)
                  <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}" alt="{{ $venue->name }}">
                @else
                  <div class="carousel-image-placeholder" style="display:flex; align-items:center; justify-content:center; color:#777;">
                    Sin imagen
                  </div>
                @endif

                <div class="carousel-card-body">
                  <h3>{{ $venue->name }}</h3>

                  @if($venue->zone)
                    <div class="badge">Zona: {{ $venue->zone }}</div>
                  @endif

                  <div class="carousel-meta">
                    ⭐ {{ number_format($venue->reviews_avg_rating, 1) }} / 5
                    <span class="muted">({{ $venue->reviews_count }} reseña{{ $venue->reviews_count > 1 ? 's' : '' }})</span>
                  </div>

                  <a href="{{ route('venues.show', $venue) }}" class="btn btn-primary venue-btn">
                    Ver complejo
                  </a>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </div>

    </section>
  </div>

  <script>
    const featureTabs = Array.from(document.querySelectorAll('.feature-tab'));
    const featureCarousels = Array.from(document.querySelectorAll('.feature-carousel'));
    const featuredSection = document.querySelector('.carousel-section');
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
      if (!track) return 320;

      const firstCard = track.querySelector('.carousel-card');
      if (!firstCard) return 320;

      const cardWidth = firstCard.getBoundingClientRect().width;
      const styles = window.getComputedStyle(track);
      const gap = parseFloat(styles.columnGap || styles.gap || 18);

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
      if (activeCarousel) {
        activeCarousel.classList.add('active');
      }

      restartAutoplay();
    }

    function moveActiveTrack(direction = 1) {
      const track = getActiveTrack();
      if (!track) return;

      const step = getTrackStep(track);
      track.scrollBy({
        left: step * direction,
        behavior: 'smooth'
      });
    }

    function attachDragToTrack(track) {
      if (!track) return;

      let isDragging = false;
      let startX = 0;
      let startScrollLeft = 0;

      const stopDragging = () => {
        isDragging = false;
        track.classList.remove('dragging');
      };

      track.addEventListener('mousedown', (event) => {
        isDragging = true;
        startX = event.pageX;
        startScrollLeft = track.scrollLeft;
        track.classList.add('dragging');
      });

      track.addEventListener('mouseleave', stopDragging);
      track.addEventListener('mouseup', stopDragging);

      track.addEventListener('mousemove', (event) => {
        if (!isDragging) return;

        event.preventDefault();
        const distance = event.pageX - startX;
        track.scrollLeft = startScrollLeft - distance;
      });
    }

    function startAutoplay() {
      stopAutoplay();

      autoplayInterval = setInterval(() => {
        activateFeatureTab(getActiveFeatureIndex() + 1);
      }, 3500);
    }

    function stopAutoplay() {
      if (autoplayInterval) {
        clearInterval(autoplayInterval);
        autoplayInterval = null;
      }
    }

    function restartAutoplay() {
      startAutoplay();
    }

    function getActiveFeatureIndex() {
      const current = featureTabs.findIndex(tab => tab.classList.contains('active'));
      return current >= 0 ? current : 0;
    }

    featureTabs.forEach((tab, index) => {
      tab.addEventListener('click', () => activateFeatureTab(index));
    });

    if (carouselMovePrevBtn) {
      carouselMovePrevBtn.addEventListener('click', () => {
        activateFeatureTab(getActiveFeatureIndex() - 1);
        restartAutoplay();
      });
    }

    if (carouselMoveNextBtn) {
      carouselMoveNextBtn.addEventListener('click', () => {
        activateFeatureTab(getActiveFeatureIndex() + 1);
        restartAutoplay();
      });
    }

    document.querySelectorAll('[data-carousel-track]').forEach(track => {
      attachDragToTrack(track);
    });

    if (featuredSection) {
      featuredSection.addEventListener('mouseenter', stopAutoplay);
      featuredSection.addEventListener('mouseleave', startAutoplay);
      featuredSection.addEventListener('touchstart', stopAutoplay, { passive: true });
      featuredSection.addEventListener('touchend', startAutoplay);
    }

    startAutoplay();
  </script>

  @auth
    @if(($favorites ?? collect())->isNotEmpty())
      <div class="page-card" style="margin-bottom:20px;">
        <h2 style="margin:0 0 12px 0; font-size:22px;">Tus favoritos</h2>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          @foreach($favorites as $fav)
            <a href="{{ route('venues.show', $fav) }}" class="btn">
              ★ {{ $fav->name }}
            </a>
          @endforeach
        </div>
      </div>
    @endif
  @endauth

  <div id="map" style="height:420px; border:1px solid #eee; border-radius:20px; margin:14px 0 22px 0; overflow:hidden;"></div>


  <form method="GET" action="{{ route('venues.index') }}" class="toolbar">
    <div>
      <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Buscar</label>
      <input
        type="text"
        name="q"
        value="{{ $q ?? '' }}"
        placeholder="Nombre, zona o descripción"
      >
    </div>

    <div>
      <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Zona</label>
      <select name="zone">
        <option value="">Todas</option>
        @foreach($zones as $z)
          <option value="{{ $z }}" {{ ($zone ?? '') === $z ? 'selected' : '' }}>
            {{ $z }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Deporte</label>
      <select name="sport">
        <option value="">Todos</option>
        <option value="football" {{ ($sport ?? '') === 'football' ? 'selected' : '' }}>Futbol</option>
        <option value="padel" {{ ($sport ?? '') === 'padel' ? 'selected' : '' }}>Padel</option>
        <option value="tennis" {{ ($sport ?? '') === 'tennis' ? 'selected' : '' }}>Tenis</option>
      </select>
    </div>

    <div>
      <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Precio mínimo</label>
      <input
        type="number"
        name="min_price"
        min="0"
        step="0.01"
        value="{{ $minPrice ?? '' }}"
        placeholder="Ej: 8000"
      >
    </div>

    <div>
      <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Precio máximo</label>
      <input
        type="number"
        name="max_price"
        min="0"
        step="0.01"
        value="{{ $maxPrice ?? '' }}"
        placeholder="Ej: 15000"
      >
    </div>

    <div>
      <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Fecha</label>
      <input
        type="date"
        name="date"
        value="{{ $date ?? '' }}"
      >
    </div>

    <div>
      <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Horario disponible</label>
      <input
        type="time"
        name="available_at"
        value="{{ $availableAt ?? '' }}"
      >
    </div>

    <button type="submit" class="btn btn-primary">Filtrar</button>
    <a href="{{ route('venues.index') }}" class="btn">Limpiar</a>
  </form>

  <h2 id="complejos" class="section-title">Complejos disponibles</h2>

  @if($venues->isEmpty())
    <div class="page-card">
      <p class="muted" style="margin:0;">No se encontraron complejos con esos filtros.</p>
    </div>
  @else
    <div class="grid grid-venues">
      @foreach ($venues as $venue)
        <article class="venue-card">
          @if($venue->cover_image_path)
            <img
              src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
              alt="{{ $venue->name }}"
              class="venue-card-image"
            >
          @else
            <div class="venue-card-image" style="display:flex; align-items:center; justify-content:center; color:#777;">
              Sin imagen
            </div>
          @endif

          <div class="venue-card-body">
            <div>
              <h3>{{ $venue->name }}</h3>

            @if($venue->reviews_count > 0)
              <div style="margin-top:6px; display:flex; align-items:center; flex-wrap:wrap;">
                <span class="stars">
                  @php $rounded = round($venue->reviews_avg_rating); @endphp
                  @for($i = 1; $i <= 5; $i++)
                    {{ $i <= $rounded ? '★' : '☆' }}
                  @endfor
                </span>

                <span class="stars-text">
                  {{ number_format($venue->reviews_avg_rating, 1) }}
                  <span class="muted" style="font-weight:400;">
                    ({{ $venue->reviews_count }} reseña{{ $venue->reviews_count > 1 ? 's' : '' }})
                  </span>
                </span>
              </div>
            @else
              <div style="margin-top:6px; font-size:14px; color:#777;">
                Sin reservas todavía
              </div>
            @endif

            </div>

            @if($venue->zone)
              <div class="badge">Zona: {{ $venue->zone }}</div>
            @endif

            <div class="muted" style="line-height:1.5;">
              {{ $venue->description ?? 'Reservá online y encontrá disponibilidad en pocos pasos.' }}
            </div>

            <div class="card-actions">
              <a href="{{ route('venues.show', $venue) }}" class="btn btn-primary venue-btn">
                Ver complejo
              </a>

              @auth
                @if(in_array($venue->id, $favoriteVenueIds ?? []))
                  <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn">Quitar de Favoritos</button>
                  </form>
                @else
                  <form method="POST" action="{{ route('venues.favorite', $venue) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn">Guardar</button>
                  </form>
                @endif
              @endauth
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif

  <script>
    const VENUES = [
      @foreach($venues as $v)
        {
          id: {{ $v->id }},
          name: @json($v->name),
          lat: {{ $v->lat ?? 'null' }},
          lng: {{ $v->lng ?? 'null' }},
          url: @json(route('venues.show', $v))
        }@if(!$loop->last),@endif
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

        const marker = new google.maps.Marker({
          map: map,
          position: { lat: Number(v.lat), lng: Number(v.lng) },
          title: v.name,
        });

        const info = new google.maps.InfoWindow({
          content: `<div><strong>${v.name}</strong><br><a href="${v.url}">Ver complejo</a></div>`
        });

        marker.addListener('click', () => info.open({ map, anchor: marker }));
      });
    }
  </script>

  <script
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initMap"
    async defer>
  </script>
@endsection


