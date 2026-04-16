@extends('layouts.app')

@section('title', 'Torneos — Competencias deportivas')
@section('meta_description', 'Torneos de futbol, padel, tenis y mas deportes. Inscribi a tu equipo y competi en eliminacion directa. Organiza tu propio torneo gratis en TuCancha.')

@push('head')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
@endpush

@push('styles')
<style>
  /* ── Scroll progress ───────────────────────────── */
  .tt-scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: var(--color-primary);
    z-index: 9999;
    transition: width .1s linear;
  }

  /* ── Hero ──────────────────────────────────────── */
  .tt-hero {
    position: relative;
    height: 340px;
    border-radius: 28px;
    overflow: hidden;
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .tt-hero-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #111 0%, #1a1a2e 50%, #16213e 100%);
  }
  .tt-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.35);
  }
  .tt-hero-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.7) 0%, transparent 55%);
  }
  .tt-hero-pattern {
    position: absolute;
    inset: 0;
    background-image:
      radial-gradient(circle at 20% 50%, rgba(34,197,94,.12) 0%, transparent 50%),
      radial-gradient(circle at 80% 30%, rgba(124,58,237,.1) 0%, transparent 50%),
      radial-gradient(circle at 50% 80%, rgba(249,115,22,.08) 0%, transparent 40%);
    pointer-events: none;
  }
  .tt-hero-content {
    position: relative;
    z-index: 2;
    padding: 32px 36px;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 14px;
  }
  .tt-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(34,197,94,.18);
    border: 1px solid rgba(34,197,94,.4);
    color: #4ade80;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: var(--radius-full);
  }
  .tt-hero-badge-dot {
    width: 7px;
    height: 7px;
    background: var(--color-primary);
    border-radius: 50%;
    animation: tt-dot-pulse 1.6s ease-in-out infinite;
  }
  @keyframes tt-dot-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.65); }
  }
  .tt-hero-h1 {
    margin: 0;
    font-size: 48px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.03em;
    line-height: 1.05;
  }
  .tt-hero-sub {
    margin: 0;
    font-size: 16px;
    color: rgba(255,255,255,.7);
    max-width: 520px;
  }

  /* ── CTA flotante ──────────────────────────────── */
  .tt-hero-cta-wrap {
    position: relative;
    z-index: 3;
    text-align: center;
    margin-top: -52px;
    margin-bottom: 20px;
  }
  .tt-hero-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--color-primary);
    color: #052e16;
    border-radius: 14px;
    padding: 14px 32px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, transform .15s, box-shadow .15s;
    box-shadow: 0 6px 20px rgba(34,197,94,.35);
  }
  .tt-hero-cta:hover {
    background: var(--color-primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(34,197,94,.45);
  }
  .tt-hero-cta svg { width: 18px; height: 18px; }

  /* ── Filtros ───────────────────────────────────── */
  .tt-filters-wrap {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
  }
  .tt-filter-row {
    display: flex;
    gap: 8px;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: center;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 4px;
  }
  .tt-filter-row::-webkit-scrollbar { display: none; }
  .tt-filter-divider {
    border: none;
    border-top: 1px solid var(--color-border);
    margin: 12px 0;
  }
  .tt-pill {
    padding: 7px 16px;
    border: 1.5px solid var(--color-border);
    background: var(--color-bg);
    border-radius: var(--radius-full);
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: border-color .15s, background .15s, color .15s;
    color: #a0a0a0;
    text-decoration: none;
    display: inline-block;
    flex-shrink: 0;
    white-space: nowrap;
  }
  .tt-pill:hover { border-color: var(--color-primary); color: var(--color-primary-hover); }
  .tt-pill.active { background: var(--color-primary); color: var(--color-text-inverse); border-color: var(--color-primary); }
  .tt-pill-sm { font-size: 12px; padding: 5px 13px; }
  .tt-filter-label {
    font-size: 12px;
    color: var(--color-text-muted);
    font-weight: 700;
    flex-shrink: 0;
  }
  .tt-filter-sep {
    color: var(--color-border);
    font-size: 16px;
    line-height: 1;
    flex-shrink: 0;
  }
  .tt-search-input {
    flex: 1;
    min-width: 180px;
    max-width: 320px;
    padding: 7px 14px 7px 36px;
    border: 1.5px solid var(--color-border);
    border-radius: var(--radius-full);
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text);
    outline: none;
    transition: border-color .15s;
    background: var(--color-bg) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.3-4.3'/%3E%3C/svg%3E") 12px center no-repeat;
  }
  .tt-search-input:focus { border-color: var(--color-primary); }
  .tt-search-input::placeholder { color: var(--color-text-muted); font-weight: 500; }

  /* ── Section wrapper ───────────────────────────── */
  .tt-section {
    position: relative;
    background: #0a0a0a;
    border-radius: 24px;
    padding: 28px 24px;
    overflow: hidden;
  }
  .tt-section-deco {
    position: absolute;
    top: -80px;
    right: -60px;
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, rgba(34,197,94,.06) 0%, rgba(34,197,94,.02) 100%);
    border-radius: 50%;
    pointer-events: none;
  }
  .tt-section::after {
    content: '';
    position: absolute;
    bottom: -40px;
    left: -30px;
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, rgba(124,58,237,.04) 0%, transparent 100%);
    border-radius: 50%;
    pointer-events: none;
  }

  /* ── Cards grid ────────────────────────────────── */
  .tt-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    position: relative;
    z-index: 1;
  }
  @media (max-width: 1024px) { .tt-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 640px) { .tt-grid { grid-template-columns: 1fr; } }

  /* ── Card ──────────────────────────────────────── */
  .tt-card {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    position: relative;
    box-shadow: var(--shadow-sm);
  }
  .tt-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,.30);
    border-color: var(--color-primary);
  }
  .tt-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 16px 16px 0 0;
    opacity: 0;
    transition: opacity .18s ease;
    z-index: 2;
  }
  .tt-card:hover::before { opacity: 1; }

  /* Sport-color top accent */
  .tt-card[data-sport="football"]::before   { background: linear-gradient(90deg, #22c55e, #4ade80); }
  .tt-card[data-sport="padel"]::before      { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
  .tt-card[data-sport="tennis"]::before     { background: linear-gradient(90deg, #d4b896, #e8d5b7); }
  .tt-card[data-sport="basketball"]::before { background: linear-gradient(90deg, #f97316, #fb923c); }
  .tt-card[data-sport="volleyball"]::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }

  /* Card cover */
  .tt-card-cover {
    position: relative;
    height: 160px;
    overflow: hidden;
  }
  .tt-card-cover-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .3s ease;
  }
  .tt-card:hover .tt-card-cover-img { transform: scale(1.04); }
  .tt-card-cover-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.5) 0%, transparent 60%);
  }

  /* Sport-themed gradient placeholders */
  .tt-cover-football   { background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #22c55e 100%); }
  .tt-cover-padel      { background: linear-gradient(135deg, #2e1065 0%, #4c1d95 40%, #7c3aed 100%); }
  .tt-cover-tennis     { background: linear-gradient(135deg, #78350f 0%, #92400e 40%, #d4b896 100%); }
  .tt-cover-basketball { background: linear-gradient(135deg, #7c2d12 0%, #9a3412 40%, #f97316 100%); }
  .tt-cover-volleyball { background: linear-gradient(135deg, #1e3a5f 0%, #1e40af 40%, #3b82f6 100%); }

  /* Cover badges */
  .tt-card-badges {
    position: absolute;
    top: 12px;
    left: 12px;
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    z-index: 2;
  }
  .tt-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: var(--radius-full);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .03em;
    text-transform: uppercase;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
  }

  /* Sport badges */
  .tt-badge-football   { background: rgba(34,197,94,.2); border: 1px solid rgba(34,197,94,.4); color: #4ade80; }
  .tt-badge-padel      { background: rgba(124,58,237,.2); border: 1px solid rgba(124,58,237,.4); color: #a78bfa; }
  .tt-badge-tennis     { background: rgba(212,184,150,.25); border: 1px solid rgba(212,184,150,.5); color: #e8d5b7; }
  .tt-badge-basketball { background: rgba(249,115,22,.2); border: 1px solid rgba(249,115,22,.4); color: #fb923c; }
  .tt-badge-volleyball { background: rgba(59,130,246,.2); border: 1px solid rgba(59,130,246,.4); color: #93c5fd; }

  /* Status badges */
  .tt-badge-open       { background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.35); color: #22c55e; }
  .tt-badge-inprogress { background: rgba(245,179,1,.15); border: 1px solid rgba(245,179,1,.35); color: #f59e0b; }
  .tt-badge-finished   { background: rgba(107,114,128,.15); border: 1px solid rgba(107,114,128,.35); color: #9ca3af; }
  .tt-badge-closed     { background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.3); color: #f87171; }

  /* Card body */
  .tt-card-body {
    padding: 16px 18px 18px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex: 1;
  }
  .tt-card-title {
    margin: 0;
    font-size: 17px;
    font-weight: 800;
    color: var(--color-text);
    line-height: 1.2;
    letter-spacing: -.01em;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* Card meta rows */
  .tt-card-meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .tt-card-meta-row {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    color: var(--color-text-secondary);
    font-weight: 500;
  }
  .tt-card-meta-row svg {
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    stroke: var(--color-text-muted);
  }
  .tt-card-meta-row strong {
    font-weight: 700;
    color: var(--color-text);
  }

  /* Teams progress */
  .tt-teams-progress {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .tt-progress-bar {
    flex: 1;
    height: 6px;
    background: var(--color-bg-hover);
    border-radius: var(--radius-full);
    overflow: hidden;
    position: relative;
  }
  .tt-progress-fill {
    height: 100%;
    border-radius: var(--radius-full);
    background: var(--color-primary);
    transition: width .6s var(--ease-out-expo);
  }
  .tt-progress-fill.full { background: #f59e0b; }
  .tt-progress-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--color-text-secondary);
    flex-shrink: 0;
    white-space: nowrap;
  }

  /* Card footer */
  .tt-card-footer {
    padding: 0 18px 18px;
    margin-top: auto;
  }
  .tt-card-btn {
    display: block;
    width: 100%;
    text-align: center;
    background: var(--color-bg-dark);
    color: var(--color-text-inverse);
    border-radius: 10px;
    padding: 10px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s, transform .15s;
  }
  .tt-card-btn:hover {
    background: var(--color-primary);
    color: #052e16;
    transform: translateY(-1px);
  }

  /* ── Empty state ───────────────────────────────── */
  .tt-empty {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 20px;
    padding: 64px 24px;
    text-align: center;
  }
  .tt-empty-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, rgba(34,197,94,.1), rgba(124,58,237,.1));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .tt-empty-icon svg { width: 32px; height: 32px; stroke: var(--color-primary); }
  .tt-empty h3 {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 800;
  }
  .tt-empty p { color: var(--color-text-muted); font-size: 14px; margin: 0 0 24px; }
  .tt-empty-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }

  /* ── Pagination ────────────────────────────────── */
  .tt-pagination {
    margin-top: 28px;
    display: flex;
    justify-content: center;
  }
  .tt-pagination nav { display: flex; gap: 4px; align-items: center; }
  .tt-pagination .page-link {
    padding: 8px 14px;
    border: 1px solid var(--color-border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--color-text-secondary);
    text-decoration: none;
    transition: all .15s;
  }
  .tt-pagination .page-link:hover { border-color: var(--color-primary); color: var(--color-primary); }
  .tt-pagination .active .page-link { background: var(--color-primary); color: #fff; border-color: var(--color-primary); }
  .tt-pagination .disabled .page-link { opacity: .4; pointer-events: none; }

  /* ── Mobile ────────────────────────────────────── */
  @media (max-width: 640px) {
    .tt-hero { height: 260px; border-radius: 20px; }
    .tt-hero-h1 { font-size: 32px; }
    .tt-hero-content { padding: 20px; }
    .tt-hero-cta-wrap { margin-top: -44px; }
    .tt-filters-wrap { padding: 14px 16px; }
    .tt-section { padding: 20px 16px; border-radius: 20px; }
  }
</style>
@endpush

@section('content')

{{-- Scroll progress bar --}}
<div class="tt-scroll-progress" id="ttScrollProgress"></div>

{{-- Hero --}}
<div class="tt-hero" data-aos="fade-up">
  <div class="tt-hero-bg"></div>
  <div class="tt-hero-overlay"></div>
  <div class="tt-hero-gradient"></div>
  <div class="tt-hero-pattern" aria-hidden="true"></div>
  <div class="tt-hero-content">
    <div class="tt-hero-badge" data-aos="fade-up" data-aos-delay="100">
      <span class="tt-hero-badge-dot"></span>
      Competencias activas
    </div>
    <h1 class="tt-hero-h1" data-aos="fade-up" data-aos-delay="150">Torneos</h1>
    <p class="tt-hero-sub" data-aos="fade-up" data-aos-delay="200">Arma tu equipo, inscribite y competi. Eliminacion directa, bracket en vivo.</p>
  </div>
</div>

{{-- CTA Crear torneo --}}
@auth
<div class="tt-hero-cta-wrap" data-aos="fade-up" data-aos-delay="260">
  <div style="display:flex;align-items:center;justify-content:center;gap:10px;flex-wrap:wrap;">
    <a href="{{ route('torneos.create') }}" class="tt-hero-cta">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Crear torneo
    </a>
    @if(($myTournamentsCount ?? 0) > 0)
      <a href="{{ route('torneos.my') }}" style="display:inline-flex;align-items:center;gap:7px;background:#111;color:#e8e8e8;border:1.5px solid rgba(255,255,255,.1);border-radius:14px;padding:14px 24px;font-size:15px;font-weight:700;text-decoration:none;font-family:inherit;transition:border-color .15s,transform .15s,box-shadow .15s;box-shadow:0 2px 8px rgba(0,0,0,.2);"
         onmouseover="this.style.borderColor='var(--color-primary)';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(0,0,0,.3)'"
         onmouseout="this.style.borderColor='rgba(255,255,255,.1)';this.style.transform='none';this.style.boxShadow='0 2px 8px rgba(0,0,0,.2)'">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
        Mis torneos
      </a>
    @endif
  </div>
  <div style="text-align:center; margin-top:10px;">
    <a href="{{ route('organizador.planes') }}" style="font-size:var(--text-sm); color:var(--color-text-secondary); text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:color .15s;">
      Conoce los planes para organizadores
      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
    </a>
  </div>
</div>
@endauth

{{-- Filtros --}}
<form method="GET" action="{{ route('torneos.index') }}" class="tt-filters-wrap" data-aos="fade-up" data-aos-delay="100"
      x-data="{ autoSubmit() { this.$el.closest('form').submit(); } }">

  {{-- Fila 1: Deportes --}}
  <div class="tt-filter-row">
    @php
      $sportPills = ['football'=>'Futbol','padel'=>'Padel','tennis'=>'Tenis','basketball'=>'Basquet','volleyball'=>'Voley'];
      $pi = 0;
    @endphp
    <a href="{{ route('torneos.index', array_filter(['status' => $status ?? null, 'q' => $q ?? null])) }}"
       class="tt-pill {{ !$sport ? 'active' : '' }}"
       data-aos="fade-up" data-aos-delay="0">Todos</a>
    @foreach($sportPills as $val => $label)
      @php $pi++ @endphp
      <a href="{{ route('torneos.index', array_filter(['sport' => $val, 'status' => $status ?? null, 'q' => $q ?? null])) }}"
         class="tt-pill {{ ($sport ?? '') === $val ? 'active' : '' }}"
         data-aos="fade-up" data-aos-delay="{{ $pi * 50 }}">{{ $label }}</a>
    @endforeach
  </div>

  <hr class="tt-filter-divider">

  {{-- Fila 2: Estado + Busqueda --}}
  <div class="tt-filter-row">
    <span class="tt-filter-label">Estado:</span>
    @php
      $statusPills = [
        '' => 'Todos',
        'open_registration' => 'Inscripcion abierta',
        'in_progress' => 'En curso',
        'finished' => 'Finalizados',
      ];
      $si = 0;
    @endphp
    @foreach($statusPills as $val => $label)
      @php $si++ @endphp
      <a href="{{ route('torneos.index', array_filter(['sport' => $sport ?? null, 'status' => $val ?: null, 'q' => $q ?? null])) }}"
         class="tt-pill tt-pill-sm {{ ($status ?? '') === $val ? 'active' : '' }}"
         data-aos="fade-up" data-aos-delay="{{ $si * 50 }}">{{ $label }}</a>
    @endforeach

    <span class="tt-filter-sep">|</span>

    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Buscar torneo..."
           class="tt-search-input"
           onkeydown="if(event.key==='Enter'){this.closest('form').submit();}">
    @if($sport)<input type="hidden" name="sport" value="{{ $sport }}">@endif
    @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
  </div>
</form>

{{-- Listado de torneos --}}
<div class="tt-section">
  <div class="tt-section-deco" aria-hidden="true"></div>

  @if($tournaments->isEmpty())
    <div class="tt-empty" data-aos="zoom-in">
      <div class="tt-empty-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
          <path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
          <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
          <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
        </svg>
      </div>
      <h3>No hay torneos disponibles</h3>
      <p>Todavia no hay torneos que coincidan con tu busqueda. Crea el tuyo y empeza a competir.</p>
      <div class="tt-empty-actions">
        @auth
          <a href="{{ route('torneos.create') }}" class="tt-hero-cta" style="font-size:14px; padding:12px 24px;">Crear torneo</a>
        @endauth
        <a href="{{ route('torneos.index') }}" class="tt-pill" style="padding:12px 20px;">Ver todos</a>
      </div>
    </div>
  @else
    <div class="tt-grid">
      @foreach($tournaments as $idx => $tournament)
        @php
          $confirmedCount = $tournament->confirmedTeams->count();
          $pct = $tournament->max_teams > 0 ? min(100, round(($confirmedCount / $tournament->max_teams) * 100)) : 0;
          $isFull = $confirmedCount >= $tournament->max_teams;

          $sportLabel = match($tournament->sport) {
            'football'   => 'Futbol',
            'padel'      => 'Padel',
            'tennis'     => 'Tenis',
            'basketball' => 'Basquet',
            'volleyball' => 'Voley',
            default      => ucfirst($tournament->sport ?? ''),
          };

          $statusLabel = match($tournament->status) {
            'open_registration'   => 'Inscripcion abierta',
            'registration_closed' => 'Inscripcion cerrada',
            'in_progress'         => 'En curso',
            'finished'            => 'Finalizado',
            'cancelled'           => 'Cancelado',
            default               => 'Borrador',
          };

          $statusClass = match($tournament->status) {
            'open_registration'   => 'tt-badge-open',
            'in_progress'         => 'tt-badge-inprogress',
            'finished'            => 'tt-badge-finished',
            'registration_closed' => 'tt-badge-closed',
            default               => 'tt-badge-finished',
          };

          $formatLabel = match($tournament->format ?? 'single_elimination') {
            'single_elimination' => 'Eliminacion directa',
            'round_robin'        => 'Todos contra todos',
            default              => ucfirst($tournament->format ?? ''),
          };

          $cardDelay = min($idx * 80, 400);
        @endphp

        <div class="tt-card" data-sport="{{ $tournament->sport }}"
             data-aos="fade-up" data-aos-delay="{{ $cardDelay }}">

          {{-- Cover --}}
          <div class="tt-card-cover">
            @if($tournament->cover_image_path)
              <img src="{{ asset('storage/' . $tournament->cover_image_path) }}"
                   alt="{{ $tournament->name }}"
                   class="tt-card-cover-img"
                   loading="lazy">
            @else
              <div class="tt-card-cover-img tt-cover-{{ $tournament->sport }}" style="display:flex;align-items:center;justify-content:center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.25)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/>
                  <path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/>
                  <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/>
                  <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/>
                </svg>
              </div>
            @endif
            <div class="tt-card-cover-overlay"></div>

            {{-- Badges sobre el cover --}}
            <div class="tt-card-badges">
              <span class="tt-badge tt-badge-{{ $tournament->sport }}">{{ $sportLabel }}</span>
              <span class="tt-badge {{ $statusClass }}">
                @if($tournament->status === 'open_registration')
                  <span style="width:6px;height:6px;background:currentColor;border-radius:50;display:inline-block;animation:tt-dot-pulse 1.6s ease-in-out infinite;"></span>
                @endif
                {{ $statusLabel }}
              </span>
            </div>
          </div>

          {{-- Body --}}
          <div class="tt-card-body">
            <h3 class="tt-card-title">{{ $tournament->name }}</h3>

            <div class="tt-card-meta">
              {{-- Formato --}}
              <div class="tt-card-meta-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/>
                  <polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/>
                  <line x1="4" y1="4" x2="9" y2="9"/>
                </svg>
                {{ $formatLabel }}
              </div>

              {{-- Equipos con progress bar --}}
              <div class="tt-teams-progress">
                <div class="tt-progress-bar">
                  <div class="tt-progress-fill {{ $isFull ? 'full' : '' }}" style="width: {{ $pct }}%"></div>
                </div>
                <span class="tt-progress-label">{{ $confirmedCount }}/{{ $tournament->max_teams }}</span>
              </div>

              {{-- Fecha --}}
              @if($tournament->estimated_start_date)
              <div class="tt-card-meta-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ $tournament->estimated_start_date->translatedFormat('d M Y') }}
              </div>
              @endif

              {{-- Precio --}}
              @if($tournament->inscription_price && $tournament->inscription_price > 0)
              <div class="tt-card-meta-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                <strong>${{ number_format($tournament->inscription_price, 0, ',', '.') }}</strong> inscripcion
              </div>
              @else
              <div class="tt-card-meta-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                <span style="color:var(--color-primary);font-weight:700;">Gratis</span>
              </div>
              @endif

              {{-- Complejo --}}
              @if($tournament->external_venue_name)
              <div class="tt-card-meta-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                {{ Str::limit($tournament->external_venue_name, 30) }}
              </div>
              @endif

              {{-- Organizador --}}
              <div class="tt-card-meta-row">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
                {{ $tournament->organizer->name ?? 'Organizador' }}
              </div>
            </div>
          </div>

          {{-- Footer --}}
          <div class="tt-card-footer">
            <a href="{{ route('torneos.show', $tournament) }}" class="tt-card-btn">Ver torneo</a>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Paginacion --}}
    @if($tournaments->hasPages())
      <div class="tt-pagination">
        {{ $tournaments->links() }}
      </div>
    @endif
  @endif
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 600, once: true, offset: 60 });

  // Scroll progress bar
  window.addEventListener('scroll', function() {
    const el = document.getElementById('ttScrollProgress');
    if (!el) return;
    const h = document.documentElement;
    const pct = (h.scrollTop / (h.scrollHeight - h.clientHeight)) * 100;
    el.style.width = Math.min(pct, 100) + '%';
  });
</script>
@endpush
