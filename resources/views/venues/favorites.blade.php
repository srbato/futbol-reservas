@extends('layouts.app')

@section('title','Mis favoritos')

@section('content')
  <div class="page-card" style="margin-bottom:22px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 8px 0; font-size:34px; letter-spacing:-0.02em;">Mis favoritos</h1>
        <p class="muted" style="margin:0;">
          Guardá tus complejos preferidos para encontrarlos más rápido.
        </p>
      </div>

      <div>
        <a href="{{ route('venues.index') }}" class="btn btn-primary">Explorar complejos</a>
      </div>
    </div>
  </div>

  @if($venues->isEmpty())
    <div class="page-card">
      <h3 style="margin-top:0;">Todavía no tenés favoritos</h3>
      <p class="muted" style="margin-bottom:14px;">
        Cuando guardes complejos como favoritos, los vas a ver acá.
      </p>

      <a href="{{ route('venues.index') }}" class="btn btn-primary">Ver complejos</a>
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
                  Sin reseñas todavía
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

              <form method="POST" action="{{ route('venues.unfavorite', $venue) }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn">★ Quitar</button>
              </form>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif
@endsection