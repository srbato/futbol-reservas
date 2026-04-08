@extends('layouts.marketing')

@section('title', ($post->meta_title ?: $post->title) . ' — TuCancha Blog')
@section('meta_description', $post->meta_description ?: $post->excerpt ?: 'Lee este articulo en el blog de TuCancha.')
@section('og_title', $post->meta_title ?: $post->title)
@section('og_description', $post->meta_description ?: $post->excerpt)
@if($post->cover_image_path)
  @section('og_image', Storage::url($post->cover_image_path))
@endif

@push('jsonld')
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $post->title,
    'description' => $post->meta_description ?: $post->excerpt,
    'image' => $post->cover_image_path ? Storage::url($post->cover_image_path) : null,
    'datePublished' => $post->published_at->toIso8601String(),
    'dateModified' => $post->updated_at->toIso8601String(),
    'author' => ['@type' => 'Person', 'name' => $post->author->name ?? 'TuCancha'],
    'publisher' => [
        '@type' => 'Organization',
        'name' => 'TuCancha',
        'logo' => ['@type' => 'ImageObject', 'url' => asset('images/logo-multicolor.svg')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
@endphp
</script>
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => url('/blog')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $post->title, 'item' => url()->current()],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

@push('styles')
<style>
  /* ── Scroll Progress ────────────────────────────── */
  .bp-scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: linear-gradient(90deg, #22c55e, #6eeaa0);
    z-index: 9999;
    border-radius: 0 2px 2px 0;
    transition: width 80ms linear;
  }

  /* ── Hero ────────────────────────────────────────── */
  .bp-hero {
    padding: 32px 0 0;
  }

  .bp-hero-shell {
    background: rgba(0,0,0,0.03);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(0,0,0,0.04);
  }

  .bp-hero-inner {
    position: relative;
    min-height: 320px;
    border-radius: calc(2rem - 5px);
    overflow: hidden;
    display: flex;
    align-items: flex-end;
  }

  .bp-hero-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .bp-hero-img-placeholder {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #0a3d21, #111);
  }

  .bp-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.2) 60%, rgba(0,0,0,0.1) 100%);
  }

  .bp-hero-content {
    position: relative;
    z-index: 2;
    padding: 48px 48px 40px;
    width: 100%;
    max-width: 760px;
  }

  .bp-hero-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: rgba(255,255,255,0.55);
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 20px;
    transition: color 250ms var(--ease-out-expo);
  }

  .bp-hero-back:hover { color: #fff; }
  .bp-hero-back svg { transition: transform 250ms var(--ease-out-expo); }
  .bp-hero-back:hover svg { transform: translateX(-3px); }

  .bp-hero-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    font-size: 13px;
    color: rgba(255,255,255,0.50);
    font-weight: 600;
  }

  .bp-hero-meta-dot {
    width: 3px;
    height: 3px;
    border-radius: 50%;
    background: rgba(255,255,255,0.30);
  }

  .bp-hero-content h1 {
    margin: 0;
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 900;
    letter-spacing: -0.035em;
    color: #fff;
    line-height: 1.1;
    text-wrap: balance;
  }

  @media (max-width: 768px) {
    .bp-hero-inner { min-height: 260px; }
    .bp-hero-content { padding: 32px 24px; }
  }

  @media (max-width: 480px) {
    .bp-hero-shell { border-radius: 1.25rem; }
    .bp-hero-inner { border-radius: calc(1.25rem - 5px); min-height: 220px; }
  }

  /* ── Article Body ───────────────────────────────── */
  .bp-article-wrap {
    display: grid;
    grid-template-columns: 1fr 240px;
    gap: 48px;
    max-width: 1000px;
    margin: 0 auto;
    padding: 48px 24px 80px;
    align-items: start;
  }

  .bp-article {
    max-width: var(--max-width-narrow, 720px);
  }

  /* Prose styling */
  .bp-prose {
    font-size: 17px;
    line-height: 1.8;
    color: #333;
  }

  .bp-prose h2 {
    font-size: 26px;
    font-weight: 900;
    letter-spacing: -0.025em;
    margin: 48px 0 16px;
    color: var(--color-text, #111);
  }

  .bp-prose h3 {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.02em;
    margin: 36px 0 12px;
    color: var(--color-text, #111);
  }

  .bp-prose p {
    margin: 0 0 20px;
  }

  .bp-prose ul, .bp-prose ol {
    margin: 0 0 20px;
    padding-left: 24px;
  }

  .bp-prose li {
    margin-bottom: 8px;
  }

  .bp-prose strong {
    font-weight: 800;
    color: var(--color-text, #111);
  }

  .bp-prose a {
    color: #22c55e;
    font-weight: 700;
    text-decoration: underline;
    text-decoration-color: rgba(34,197,94,0.3);
    text-underline-offset: 3px;
    transition: text-decoration-color 200ms;
  }

  .bp-prose a:hover {
    text-decoration-color: #22c55e;
  }

  .bp-prose blockquote {
    margin: 32px 0;
    padding: 20px 24px;
    border-left: 3px solid #22c55e;
    background: #f0fdf4;
    border-radius: 0 12px 12px 0;
    font-style: italic;
    color: #166534;
  }

  .bp-prose img {
    max-width: 100%;
    height: auto;
    border-radius: 16px;
    margin: 24px 0;
  }

  /* ── Sidebar ────────────────────────────────────── */
  .bp-sidebar {
    position: sticky;
    top: calc(var(--header-height, 64px) + 24px);
  }

  .bp-sidebar-cta-shell {
    background: rgba(0,0,0,0.03);
    border-radius: 1.25rem;
    padding: 4px;
    border: 1px solid rgba(0,0,0,0.03);
  }

  .bp-sidebar-cta {
    background: #111;
    border-radius: calc(1.25rem - 4px);
    padding: 28px 22px;
    color: #fff;
    text-align: center;
  }

  .bp-sidebar-cta h3 {
    font-size: 16px;
    font-weight: 800;
    margin: 0 0 8px;
    letter-spacing: -0.02em;
  }

  .bp-sidebar-cta p {
    font-size: 13px;
    color: rgba(255,255,255,0.50);
    margin: 0 0 18px;
    line-height: 1.5;
  }

  .bp-sidebar-cta a {
    display: block;
    padding: 12px;
    background: #22c55e;
    color: #052e14;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
    transition: background 300ms var(--ease-out-expo), transform 300ms var(--ease-out-expo);
  }

  .bp-sidebar-cta a:hover {
    background: #16a34a;
    color: #fff;
    transform: translateY(-2px);
  }

  .bp-sidebar-cta a:active {
    transform: translateY(0) scale(0.97);
  }

  /* ── Share Buttons ──────────────────────────────── */
  .bp-share {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 40px;
    padding-top: 32px;
    border-top: 1px solid var(--color-border, #ececec);
  }

  .bp-share-label {
    font-size: 13px;
    font-weight: 800;
    color: var(--color-text-muted, #999);
  }

  .bp-share-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    border: 1px solid var(--color-border, #ececec);
    background: var(--color-bg, #fff);
    color: var(--color-text-secondary, #666);
    text-decoration: none;
    transition: background 250ms var(--ease-out-expo), border-color 250ms var(--ease-out-expo), transform 250ms var(--ease-out-expo);
    cursor: pointer;
  }

  .bp-share-btn:hover {
    background: var(--color-bg-dark, #111);
    border-color: var(--color-bg-dark, #111);
    color: var(--color-text-inverse, #fff);
    transform: translateY(-2px);
  }

  .bp-share-btn:active {
    transform: translateY(0) scale(0.97);
  }

  /* ── Related Posts ──────────────────────────────── */
  .bp-related {
    padding: 0 24px 80px;
    max-width: 1000px;
    margin: 0 auto;
  }

  .bp-related-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
  }

  .bp-related-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--color-text-muted, #999);
  }

  .bp-related-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, var(--color-border, #ececec), transparent);
  }

  .bp-related-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
  }

  .bp-related-card {
    background: rgba(0,0,0,0.02);
    border-radius: 1.25rem;
    padding: 4px;
    border: 1px solid rgba(0,0,0,0.03);
    transition: transform 400ms var(--ease-out-expo), box-shadow 400ms var(--ease-out-expo);
  }

  .bp-related-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.06);
  }

  .bp-related-card a {
    display: block;
    background: #fff;
    border-radius: calc(1.25rem - 4px);
    padding: 20px;
    text-decoration: none;
    color: inherit;
  }

  .bp-related-card-date {
    font-size: 11px;
    font-weight: 700;
    color: var(--color-text-muted, #999);
    margin-bottom: 8px;
  }

  .bp-related-card-title {
    font-size: 16px;
    font-weight: 800;
    color: var(--color-text, #111);
    letter-spacing: -0.02em;
    line-height: 1.3;
    transition: color 250ms var(--ease-out-expo);
  }

  .bp-related-card:hover .bp-related-card-title {
    color: #22c55e;
  }

  @media (max-width: 900px) {
    .bp-article-wrap { grid-template-columns: 1fr; }
    .bp-sidebar { position: relative; top: 0; }
  }

  @media (max-width: 768px) {
    .bp-article-wrap { padding: 32px 16px 60px; }
    .bp-related { padding: 0 16px 60px; }
    .bp-related-grid { grid-template-columns: 1fr; }
    .bp-prose { font-size: 16px; }
  }

  @media (prefers-reduced-motion: reduce) {
    .bp-scroll-progress { transition: none; }
    .bp-related-card,
    .bp-share-btn,
    .bp-sidebar-cta a { transition-duration: 0ms !important; }
  }
</style>
@endpush

@section('content')

<div class="bp-scroll-progress" id="bpScrollProgress"></div>

{{-- Hero --}}
<section class="bp-hero">
  <div class="container">
    <div class="bp-hero-shell">
      <div class="bp-hero-inner">
        @if($post->cover_image_path)
          <img src="{{ Storage::url($post->cover_image_path) }}" alt="{{ $post->title }}" class="bp-hero-img">
        @else
          <div class="bp-hero-img-placeholder"></div>
        @endif
        <div class="bp-hero-overlay"></div>
        <div class="bp-hero-content" data-aos="fade-up" data-aos-duration="600">
          <a href="{{ route('blog.index') }}" class="bp-hero-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
            Blog
          </a>
          <div class="bp-hero-meta">
            <span>{{ $post->published_at->translatedFormat('d M Y') }}</span>
            <span class="bp-hero-meta-dot"></span>
            <span>{{ $post->readingTime() }} min de lectura</span>
            @if($post->author)
              <span class="bp-hero-meta-dot"></span>
              <span>{{ $post->author->name }}</span>
            @endif
          </div>
          <h1>{{ $post->title }}</h1>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Article --}}
<div class="bp-article-wrap">
  <article class="bp-article">
    <div class="bp-prose">
      {!! $post->body !!}
    </div>

    {{-- Share --}}
    <div class="bp-share">
      <span class="bp-share-label">Compartir</span>
      <a href="https://wa.me/?text={{ urlencode($post->title . ' — ' . url()->current()) }}"
         target="_blank" rel="noopener" class="bp-share-btn" aria-label="Compartir por WhatsApp">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a8 8 0 01-4.243-1.212l-.29-.173-3.03.795.81-2.957-.19-.3A8 8 0 1112 20z"/></svg>
      </a>
      <button class="bp-share-btn" aria-label="Copiar link" onclick="navigator.clipboard.writeText(window.location.href);this.innerHTML='<svg width=18 height=18 viewBox=&quot;0 0 24 24&quot; fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round><path d=&quot;M20 6L9 17l-5-5&quot;/></svg>';setTimeout(()=>{this.innerHTML='<svg width=18 height=18 viewBox=&quot;0 0 24 24&quot; fill=none stroke=currentColor stroke-width=2 stroke-linecap=round stroke-linejoin=round><rect x=9 y=9 width=13 height=13 rx=2 ry=2/><path d=&quot;M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1&quot;/></svg>'},2000)">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
      </button>
    </div>
  </article>

  {{-- Sidebar --}}
  <aside class="bp-sidebar">
    <div class="bp-sidebar-cta-shell">
      <div class="bp-sidebar-cta">
        <h3>Reserva tu cancha</h3>
        <p>Encontra complejos cerca tuyo y reserva al instante.</p>
        <a href="{{ route('venues.index') }}">Ver complejos</a>
      </div>
    </div>
  </aside>
</div>

{{-- Related Posts --}}
@if($related->isNotEmpty())
  <section class="bp-related">
    <div class="bp-related-header">
      <span class="bp-related-label">Seguir leyendo</span>
      <div class="bp-related-line"></div>
    </div>
    <div class="bp-related-grid">
      @foreach($related as $ri => $r)
        <div class="bp-related-card" data-aos="fade-up" data-aos-delay="{{ $ri * 60 }}" data-aos-duration="500">
          <a href="{{ route('blog.show', $r->slug) }}">
            <div class="bp-related-card-date">{{ $r->published_at->translatedFormat('d M Y') }}</div>
            <div class="bp-related-card-title">{{ $r->title }}</div>
          </a>
        </div>
      @endforeach
    </div>
  </section>
@endif

@push('scripts')
<script>
  window.addEventListener('scroll', () => {
    const el = document.getElementById('bpScrollProgress');
    if (!el) return;
    const max = document.body.scrollHeight - window.innerHeight;
    el.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
  }, { passive: true });
</script>
@endpush

@endsection
