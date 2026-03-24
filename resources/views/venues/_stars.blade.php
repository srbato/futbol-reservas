{{--
  Partial: SVG de 5 estrellas con relleno proporcional al rating.
  Uso: @include('venues._stars', ['rating' => $avgRating])
  $rating: float 0-5
--}}
@php
  $rating  = floatval($rating ?? 0);
  $full    = floor($rating);
  $partial = $rating - $full;   // fracción 0-1 para la estrella parcial
  $empty   = 5 - $full - ($partial > 0 ? 1 : 0);
  $uid     = 'vsg-' . rand(1000, 9999);
@endphp

<svg class="vi-stars-svg" viewBox="0 0 90 18" width="90" height="18" xmlns="http://www.w3.org/2000/svg" aria-label="{{ number_format($rating, 1) }} de 5 estrellas">
  <defs>
    {{-- Gradiente para la estrella parcial --}}
    <linearGradient id="{{ $uid }}" x1="0" y1="0" x2="1" y2="0">
      <stop offset="{{ round($partial * 100) }}%" stop-color="#f59e0b"/>
      <stop offset="{{ round($partial * 100) }}%" stop-color="#d1d5db"/>
    </linearGradient>
  </defs>

  @for($i = 0; $i < 5; $i++)
    @php
      $x       = $i * 18 + 9;   // centro x de cada estrella (18px de ancho)
      $starPath = "M{$x},3 l2.2,4.5 5,0.7 -3.6,3.5 0.8,5 -4.4,-2.3 -4.4,2.3 0.8,-5 -3.6,-3.5 5,-0.7z";
      if ($i < $full) {
        $fill = '#f59e0b';   // estrella llena
      } elseif ($i === $full && $partial > 0) {
        $fill = "url(#{$uid})";  // estrella parcial
      } else {
        $fill = '#d1d5db';   // estrella vacía
      }
    @endphp
    <path d="{{ $starPath }}" fill="{{ $fill }}" stroke="{{ $fill === '#d1d5db' ? '#d1d5db' : 'none' }}" stroke-width="0"/>
  @endfor
</svg>
