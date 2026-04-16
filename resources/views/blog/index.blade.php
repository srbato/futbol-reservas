@extends('layouts.marketing')

@section('title', 'Blog — TuCancha')
@section('meta_description', 'Guias, consejos y novedades sobre futbol, padel, tenis y deportes en Argentina. Todo lo que necesitas saber para reservar, jugar y mejorar.')

@push('jsonld')
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => url('/blog')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

@push('styles')
<style>
  /* Easings from design-tokens.css */

  /* ── Hero ────────────────────────────────────────── */
  .blog-hero {
    padding: 48px 0 0;
  }

  .blog-hero-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .blog-hero-inner {
    background: #111;
    border-radius: calc(2rem - 5px);
    padding: 72px 48px 64px;
    position: relative;
    overflow: hidden;
    text-align: center;
  }

  .blog-hero-orb-1 {
    position: absolute;
    top: -100px;
    right: -60px;
    width: 340px;
    height: 340px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.10) 0%, transparent 60%);
    pointer-events: none;
  }

  .blog-hero-orb-2 {
    position: absolute;
    bottom: -80px;
    left: -40px;
    width: 240px;
    height: 240px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.06) 0%, transparent 60%);
    pointer-events: none;
  }

  .blog-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 16px;
    border-radius: 999px;
    background: rgba(74,222,128,0.10);
    border: 1px solid rgba(74,222,128,0.22);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: #6eeaa0;
    margin-bottom: 20px;
    position: relative;
  }

  .blog-hero-inner h1 {
    margin: 0 0 14px;
    font-size: clamp(36px, 5vw, 56px);
    font-weight: 900;
    letter-spacing: -0.04em;
    color: #fff;
    line-height: 1;
    position: relative;
  }

  .blog-hero-inner > p {
    margin: 0 auto 32px;
    max-width: 480px;
    font-size: 16px;
    color: rgba(255,255,255,0.50);
    line-height: 1.6;
    position: relative;
  }

  /* Search */
  .blog-search-form {
    max-width: 400px;
    margin: 0 auto;
    position: relative;
  }

  .blog-search-input {
    width: 100%;
    padding: 14px 20px 14px 44px;
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 14px;
    background: rgba(255,255,255,0.06);
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    outline: none;
    transition: border-color 300ms var(--ease-out-expo), background 300ms var(--ease-out-expo);
  }

  .blog-search-input::placeholder { color: rgba(255,255,255,0.30); }

  .blog-search-input:focus {
    border-color: rgba(34,197,94,0.4);
    background: rgba(255,255,255,0.09);
  }

  .blog-search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255,255,255,0.30);
    pointer-events: none;
  }

  @media (max-width: 768px) {
    .blog-hero-inner { padding: 48px 24px 40px; }
  }

  @media (max-width: 480px) {
    .blog-hero { padding: 24px 0 0; }
    .blog-hero-shell { border-radius: 1.25rem; }
    .blog-hero-inner { border-radius: calc(1.25rem - 5px); padding: 40px 20px 32px; }
  }

  /* ── Posts Grid (Bento) ─────────────────────────── */
  .blog-grid-section {
    padding: 48px 0 80px;
  }

  .blog-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }

  /* Featured (first post) spans full width */
  .blog-card-shell:first-child {
    grid-column: span 2;
  }

  .blog-card-shell:first-child .blog-card {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0;
  }

  .blog-card-shell:first-child .blog-card-img {
    height: 100%;
    min-height: 280px;
    border-radius: calc(1.5rem - 4px) 0 0 calc(1.5rem - 4px);
  }

  .blog-card-shell:first-child .blog-card-body {
    padding: 40px 36px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .blog-card-shell:first-child .blog-card-title {
    font-size: 26px;
  }

  /* Card shell (double-bezel) */
  .blog-card-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
    transition: transform 500ms var(--ease-out-expo), box-shadow 500ms var(--ease-out-expo);
  }

  .blog-card-shell:hover {
    transform: translateY(-6px);
    box-shadow: 0 24px 48px rgba(0,0,0,0.25), 0 8px 16px rgba(34,197,94,0.08);
  }

  .blog-card-shell:active {
    transform: translateY(-2px) scale(0.99);
  }

  .blog-card {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    display: block;
    height: 100%;
  }

  .blog-card-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    display: block;
    transition: transform 600ms var(--ease-out-expo);
  }

  .blog-card-shell:hover .blog-card-img {
    transform: scale(1.03);
  }

  .blog-card-img-wrap {
    overflow: hidden;
    border-radius: calc(1.5rem - 4px) calc(1.5rem - 4px) 0 0;
  }

  .blog-card-shell:first-child .blog-card-img-wrap {
    border-radius: calc(1.5rem - 4px) 0 0 calc(1.5rem - 4px);
  }

  .blog-card-img-placeholder {
    width: 100%;
    height: 200px;
    background: linear-gradient(135deg, rgba(34,197,94,.06), #0a0a0a);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #22c55e;
    opacity: 0.5;
  }

  .blog-card-body {
    padding: 24px 24px 28px;
  }

  .blog-card-date {
    font-size: 12px;
    font-weight: 700;
    color: var(--color-text-muted, #999);
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .blog-card-reading {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: var(--color-text-muted, #999);
  }

  .blog-card-title {
    font-size: 20px;
    font-weight: 900;
    letter-spacing: -0.025em;
    color: var(--color-text, #111);
    margin: 0 0 10px;
    line-height: 1.25;
    transition: color 250ms var(--ease-out-expo);
  }

  .blog-card-shell:hover .blog-card-title {
    color: #22c55e;
  }

  .blog-card-excerpt {
    font-size: 14px;
    color: #a0a0a0;
    line-height: 1.65;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .blog-card-shell:first-child .blog-card-excerpt {
    -webkit-line-clamp: 4;
  }

  .blog-card-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 16px;
    font-size: 13px;
    font-weight: 800;
    color: #22c55e;
    letter-spacing: -0.01em;
  }

  .blog-card-cta svg {
    transition: transform 250ms var(--ease-out-expo);
  }

  .blog-card-shell:hover .blog-card-cta svg {
    transform: translateX(4px);
  }

  /* Stagger */
  .blog-card-shell:nth-child(1) { transition-delay: 0ms; }
  .blog-card-shell:nth-child(2) { transition-delay: 60ms; }
  .blog-card-shell:nth-child(3) { transition-delay: 120ms; }
  .blog-card-shell:nth-child(4) { transition-delay: 60ms; }
  .blog-card-shell:nth-child(5) { transition-delay: 120ms; }

  @media (max-width: 768px) {
    .blog-grid { grid-template-columns: 1fr; }
    .blog-card-shell:first-child { grid-column: span 1; }
    .blog-card-shell:first-child .blog-card { grid-template-columns: 1fr; }
    .blog-card-shell:first-child .blog-card-img {
      min-height: 200px;
      border-radius: calc(1.5rem - 4px) calc(1.5rem - 4px) 0 0;
    }
    .blog-card-shell:first-child .blog-card-img-wrap {
      border-radius: calc(1.5rem - 4px) calc(1.5rem - 4px) 0 0;
    }
    .blog-card-shell:first-child .blog-card-body { padding: 24px; }
    .blog-card-shell:first-child .blog-card-title { font-size: 22px; }
    .blog-card-shell { transition-delay: 0ms !important; }
  }

  /* ── Pagination ─────────────────────────────────── */
  .blog-pagination {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 40px;
  }

  .blog-pagination a,
  .blog-pagination span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
    padding: 0 12px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: background 250ms var(--ease-out-expo), color 250ms var(--ease-out-expo);
  }

  .blog-pagination a {
    color: var(--color-text-secondary, #666);
    background: var(--color-bg, #fff);
    border: 1px solid var(--color-border, #ececec);
  }

  .blog-pagination a:hover {
    background: var(--color-bg-dark, #111);
    color: var(--color-text-inverse, #fff);
    border-color: var(--color-bg-dark, #111);
  }

  .blog-pagination span.current {
    background: var(--color-bg-dark, #111);
    color: var(--color-text-inverse, #fff);
  }

  .blog-pagination span.dots {
    background: none;
    border: none;
    color: var(--color-text-muted, #999);
  }

  /* ── Empty State ────────────────────────────────── */
  .blog-empty-shell {
    background: rgba(255,255,255,0.04);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .blog-empty {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    text-align: center;
    padding: 72px 24px;
  }

  .blog-empty-icon {
    width: 48px;
    height: 48px;
    color: #444;
    margin: 0 auto 16px;
  }

  .blog-empty-title {
    font-size: 18px;
    font-weight: 800;
    color: var(--color-text, #111);
    margin: 0 0 8px;
  }

  .blog-empty-text {
    font-size: 14px;
    color: var(--color-text-muted, #999);
    margin: 0;
  }

  /* ── Focus & Reduced Motion ─────────────────────── */
  .blog-card-shell:focus-within {
    outline: 2px solid #22c55e;
    outline-offset: 3px;
  }

  .blog-search-input:focus-visible {
    outline: none;
  }

  @media (prefers-reduced-motion: reduce) {
    .blog-card-shell,
    .blog-card-img,
    .blog-card-cta svg {
      transition-duration: 0ms !important;
    }
  }
</style>
@endpush

@section('content')

  {{-- Hero --}}
  <section class="blog-hero">
    <div class="container">
      <div class="blog-hero-shell">
        <div class="blog-hero-inner">
          <div class="blog-hero-orb-1"></div>
          <div class="blog-hero-orb-2"></div>
          <div class="blog-hero-badge" data-aos="fade-down" data-aos-duration="600">TuCancha Blog</div>
          <h1 data-aos="fade-up" data-aos-delay="80" data-aos-duration="700">Guias, consejos<br>y novedades</h1>
          <p data-aos="fade-up" data-aos-delay="160" data-aos-duration="700">Todo lo que necesitas saber para reservar, jugar y mejorar en tu deporte.</p>

          <form action="{{ route('blog.index') }}" method="GET" class="blog-search-form" data-aos="fade-up" data-aos-delay="240" data-aos-duration="700">
            <svg class="blog-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" name="q" class="blog-search-input" placeholder="Buscar articulos..." value="{{ $search ?? '' }}" autocomplete="off" aria-label="Buscar artículos">
          </form>
        </div>
      </div>
    </div>
  </section>

  {{-- Posts --}}
  <section class="blog-grid-section">
    <div class="container">

      @if($posts->isEmpty())
        <div class="blog-empty-shell" data-aos="fade-up">
          <div class="blog-empty">
            <svg class="blog-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
            @if($search)
              <p class="blog-empty-title">Sin resultados para "{{ $search }}"</p>
              <p class="blog-empty-text">Intenta con otra busqueda o <a href="{{ route('blog.index') }}" style="color:#22c55e;font-weight:700;text-decoration:none;">ve todos los articulos</a>.</p>
            @else
              <p class="blog-empty-title">Proximamente</p>
              <p class="blog-empty-text">Estamos preparando contenido para vos. Volve pronto.</p>
            @endif
          </div>
        </div>
      @else
        <div class="blog-grid">
          @foreach($posts as $i => $post)
            <div class="blog-card-shell" data-aos="fade-up" data-aos-delay="{{ min($i * 60, 300) }}" data-aos-duration="600">
              <a href="{{ route('blog.show', $post->slug) }}" class="blog-card">
                @if($post->cover_image_path)
                  <div class="blog-card-img-wrap">
                    <img src="{{ Storage::url($post->cover_image_path) }}"
                         alt="{{ $post->title }}"
                         class="blog-card-img"
                         loading="{{ $i === 0 ? 'eager' : 'lazy' }}">
                  </div>
                @else
                  <div class="blog-card-img-wrap">
                    <div class="blog-card-img-placeholder">
                      <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                    </div>
                  </div>
                @endif

                <div class="blog-card-body">
                  <div class="blog-card-date">
                    {{ $post->published_at->translatedFormat('d M Y') }}
                    <span class="blog-card-reading">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                      {{ $post->readingTime() }} min
                    </span>
                  </div>
                  <h2 class="blog-card-title">{{ $post->title }}</h2>
                  @if($post->excerpt)
                    <p class="blog-card-excerpt">{{ $post->excerpt }}</p>
                  @endif
                  <span class="blog-card-cta">
                    Leer mas
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                  </span>
                </div>
              </a>
            </div>
          @endforeach
        </div>

        @if($posts->hasPages())
          <div class="blog-pagination">
            @if($posts->onFirstPage())
              <span class="dots"></span>
            @else
              <a href="{{ $posts->previousPageUrl() }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
              </a>
            @endif

            @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
              @if($page == $posts->currentPage())
                <span class="current">{{ $page }}</span>
              @else
                <a href="{{ $url }}">{{ $page }}</a>
              @endif
            @endforeach

            @if($posts->hasMorePages())
              <a href="{{ $posts->nextPageUrl() }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
              </a>
            @endif
          </div>
        @endif
      @endif

    </div>
  </section>

@endsection
