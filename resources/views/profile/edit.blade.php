@extends('layouts.app')

@section('title', 'Mi perfil')

@push('styles')
<style>
  html { scroll-behavior: smooth; }

  /* ── Scroll progress bar ── */
  .pf-scroll-progress {
    position: fixed;
    top: 0; left: 0;
    height: 3px;
    width: 0%;
    background: #22c55e;
    z-index: 9999;
    transition: width .1s linear;
  }

  /* ── Hero ── */
  .pf-hero {
    position: relative;
    height: 280px;
    border-radius: 28px;
    overflow: hidden;
    margin-bottom: 28px;
    background: #111;
  }

  .pf-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/amigos-post-futbol.webp');
    background-size: cover;
    background-position: center;
  }

  .pf-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.6);
  }

  .pf-hero-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.55) 0%, transparent 60%);
  }

  .pf-hero-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 28px 24px;
    text-align: center;
  }

  .pf-avatar-hero {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #22c55e;
    box-shadow: 0 0 0 6px rgba(34,197,94,.2);
    background: #111;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: #fff;
    font-weight: 900;
    flex-shrink: 0;
  }

  .pf-hero-name {
    font-size: 32px;
    font-weight: 900;
    color: #fff;
    margin: 0;
    line-height: 1.1;
    letter-spacing: -.02em;
  }

  .pf-hero-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .pf-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 999px;
    background: rgba(255,255,255,.13);
    border: 1px solid rgba(255,255,255,.22);
    color: rgba(255,255,255,.9);
    font-size: 12px;
    font-weight: 600;
    backdrop-filter: blur(4px);
  }

  .pf-chip-verified {
    background: rgba(34,197,94,.2);
    border-color: rgba(34,197,94,.5);
    color: #86efac;
    animation: pf-pulse-chip 2.2s infinite;
  }

  @keyframes pf-pulse-chip {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,.35); }
    50%       { box-shadow: 0 0 0 5px rgba(34,197,94,0); }
  }

  /* ── Layout dos columnas ── */
  .pf-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 28px;
    align-items: start;
  }

  /* ── Sidebar ── */
  .pf-sidebar {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    padding: 20px;
    position: sticky;
    top: 80px;
  }

  .pf-nav-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 12px 16px;
    border-radius: 14px;
    text-decoration: none;
    color: #374151;
    font-size: 14px;
    font-weight: 600;
    transition: background .15s ease, transform .15s ease, color .15s ease;
    border: 1px solid transparent;
  }

  .pf-nav-item:hover {
    background: #f3f4f6;
    transform: translateX(2px);
  }

  .pf-nav-item.pf-nav-active {
    background: #111;
    color: #fff;
    border-color: #111;
  }

  .pf-nav-item.pf-nav-active svg {
    stroke: #fff;
  }

  .pf-nav-item.pf-nav-danger {
    color: #dc2626;
  }

  .pf-nav-item.pf-nav-danger:hover {
    background: #fef2f2;
  }

  .pf-nav-icon {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }

  .pf-sidebar-divider {
    height: 1px;
    background: #e5e7eb;
    margin: 16px 0;
  }

  .pf-sidebar-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }

  .pf-stat-box {
    text-align: center;
    padding: 10px 6px;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
  }

  .pf-stat-number {
    font-size: 20px;
    font-weight: 900;
    color: #22c55e;
    line-height: 1.1;
  }

  .pf-stat-label {
    font-size: 10px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-top: 3px;
  }

  /* ── Secciones principales ── */
  .pf-section {
    scroll-margin-top: 100px;
  }

  /* ── Cards ── */
  .pf-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    padding: 32px;
  }

  .pf-card-danger {
    background: #fff5f5;
    border: 1.5px solid #fecaca;
  }

  .pf-card-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 24px;
  }

  .pf-card-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .pf-card-title {
    font-size: 18px;
    font-weight: 800;
    color: #111;
    margin: 0 0 3px 0;
  }

  .pf-card-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
  }

  /* ── Input estilo unificado ── */
  .pf-label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: 6px;
  }

  .pf-input {
    width: 100%;
    padding: 12px 16px;
    border: 1.5px solid #e5e7eb;
    border-radius: 14px;
    background: #fff;
    font-size: 14px;
    font-family: inherit;
    color: #111;
    transition: border-color .2s ease, box-shadow .2s ease;
    outline: none;
    -webkit-appearance: none;
    appearance: none;
  }

  .pf-input:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.1);
  }

  /* ── Botón guardar ── */
  .pf-btn-save {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    background: #111827;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .2s ease, color .2s ease, transform .2s ease, box-shadow .2s ease;
  }

  .pf-btn-save:hover {
    background: #22c55e;
    color: #052e16;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(34,197,94,.3);
  }

  /* ── Botón peligro ── */
  .pf-btn-danger {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    background: transparent;
    color: #dc2626;
    border: 2px solid #dc2626;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .2s ease, color .2s ease, transform .2s ease;
  }

  .pf-btn-danger:hover {
    background: #dc2626;
    color: #fff;
    transform: scale(1.02);
  }

  /* ── Toast éxito ── */
  .pf-toast-success {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #dcfce7;
    border: 1px solid #bbf7d0;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #15803d;
    margin-bottom: 20px;
  }

  /* ── Avatar section ── */
  .pf-avatar-wrap {
    position: relative;
    width: 96px;
    height: 96px;
    cursor: pointer;
    margin: 0 auto 20px auto;
  }

  .pf-avatar-img {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #22c55e;
    display: block;
  }

  .pf-avatar-initial {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: #111;
    border: 3px solid #22c55e;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 900;
    color: #fff;
  }

  .pf-avatar-overlay {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: rgba(0,0,0,.45);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    opacity: 0;
    transition: opacity .2s ease;
  }

  .pf-avatar-wrap:hover .pf-avatar-overlay {
    opacity: 1;
  }

  .pf-avatar-overlay span {
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    line-height: 1;
  }

  .pf-avatar-file-label {
    position: absolute;
    inset: 0;
    cursor: pointer;
    border-radius: 50%;
    z-index: 2;
  }

  .pf-avatar-file-input {
    display: none;
  }

  /* ── Password toggle ── */
  .pf-pass-wrap {
    position: relative;
  }

  .pf-pass-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px;
    color: #9ca3af;
    display: flex;
    align-items: center;
    transition: color .15s ease;
  }

  .pf-pass-toggle:hover {
    color: #374151;
  }

  .pf-pass-wrap .pf-input {
    padding-right: 44px;
  }

  /* ── Barra fortaleza ── */
  .pf-strength-bar {
    display: flex;
    gap: 4px;
    margin-top: 8px;
  }

  .pf-strength-seg {
    flex: 1;
    height: 4px;
    border-radius: 2px;
    background: #e5e7eb;
    transition: background .25s ease;
  }

  .pf-strength-label {
    font-size: 12px;
    font-weight: 600;
    margin-top: 5px;
    min-height: 16px;
    color: #9ca3af;
    transition: color .25s ease;
  }

  /* ── Modal eliminación ── */
  .pf-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 1000;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .pf-modal-overlay.pf-modal-open {
    display: flex;
  }

  .pf-modal-card {
    background: #fff;
    border-radius: 24px;
    padding: 0;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
    overflow: hidden;
  }

  .pf-modal-header {
    background: linear-gradient(180deg, #fff5f5 0%, #fff 100%);
    padding: 32px 32px 24px 32px;
    text-align: center;
    border-bottom: 1px solid #fecaca;
  }

  .pf-modal-body {
    padding: 28px 32px 32px 32px;
  }

  .pf-modal-title {
    font-size: 22px;
    font-weight: 900;
    color: #111;
    margin: 12px 0 6px 0;
  }

  .pf-modal-sub {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
    line-height: 1.5;
  }

  .pf-btn-cancel {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    background: transparent;
    color: #374151;
    border: 1.5px solid #d1d5db;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .15s ease, transform .15s ease;
  }

  .pf-btn-cancel:hover {
    background: #f3f4f6;
    transform: translateY(-1px);
  }

  .pf-btn-destroy {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: #dc2626;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .2s ease, transform .2s ease, box-shadow .2s ease;
  }

  .pf-btn-destroy:hover {
    background: #b91c1c;
    transform: scale(1.02);
    box-shadow: 0 4px 14px rgba(220,38,38,.35);
  }

  /* ── Tabs partidos ── */
  .pf-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
  }

  .pf-tab {
    padding: 7px 18px;
    border-radius: 999px;
    border: 1.5px solid #e5e7eb;
    background: #fff;
    font-size: 13px;
    font-weight: 700;
    color: #374151;
    cursor: pointer;
    transition: all .15s ease;
    font-family: inherit;
  }

  .pf-tab:hover {
    border-color: #111;
  }

  .pf-tab.pf-tab-active {
    background: #111;
    color: #fff;
    border-color: #111;
  }

  /* ── Partido card ── */
  .pf-game-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    flex-wrap: wrap;
    transition: transform .18s ease, box-shadow .18s ease;
  }

  .pf-game-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,.07);
  }

  .pf-game-emoji {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
  }

  .pf-game-info {
    flex: 1;
    min-width: 140px;
  }

  .pf-game-name {
    font-weight: 700;
    font-size: 14px;
    color: #111;
    margin: 0 0 3px 0;
  }

  .pf-game-meta {
    font-size: 12px;
    color: #9ca3af;
    margin: 0;
  }

  .pf-game-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    flex-shrink: 0;
  }

  .pf-game-btn {
    font-size: 12px;
    padding: 6px 14px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    color: #374151;
    text-decoration: none;
    font-weight: 600;
    transition: all .15s ease;
    white-space: nowrap;
    background: #fff;
  }

  .pf-game-btn:hover {
    border-color: #111;
    background: #f9fafb;
  }

  .pf-game-btn-rate {
    background: #fef3c7;
    border-color: #fde68a;
    color: #92400e;
  }

  .pf-game-btn-rate:hover {
    background: #fde68a;
  }

  /* ── Sport profile cards ── */
  .pf-sports-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 14px;
  }

  .pf-sport-card {
    padding: 20px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
  }

  .pf-sport-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,.07);
    border-color: #111;
  }

  .pf-sport-emoji-circle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 12px;
  }

  .pf-sport-name {
    font-size: 16px;
    font-weight: 800;
    color: #111;
    margin: 0 0 6px 0;
  }

  .pf-sport-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }

  .pf-sport-badge {
    padding: 3px 10px;
    border-radius: 999px;
    background: #f3f4f6;
    font-size: 11px;
    font-weight: 600;
    color: #374151;
    text-transform: capitalize;
  }

  .pf-sport-stats {
    font-size: 13px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .pf-sport-rating {
    color: #f59e0b;
    font-weight: 700;
  }

  .pf-sport-games {
    color: #22c55e;
    font-weight: 600;
    font-size: 12px;
  }

  .pf-sport-edit-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border: 1.5px solid #e5e7eb;
    border-radius: 10px;
    color: #374151;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    transition: all .15s ease;
  }

  .pf-sport-edit-btn:hover {
    background: #111;
    color: #fff;
    border-color: #111;
  }

  /* ── Empty state ── */
  .pf-empty {
    text-align: center;
    padding: 40px 20px;
  }

  .pf-empty-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px auto;
    color: #d1d5db;
  }

  .pf-empty-title {
    font-size: 16px;
    font-weight: 700;
    color: #374151;
    margin: 0 0 6px 0;
  }

  .pf-empty-sub {
    font-size: 14px;
    color: #9ca3af;
    margin: 0 0 20px 0;
  }

  .pf-btn-cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 24px;
    background: #22c55e;
    color: #052e16;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    font-family: inherit;
    transition: background .2s ease, transform .2s ease;
  }

  .pf-btn-cta:hover {
    background: #16a34a;
    transform: translateY(-1px);
  }

  /* ── Partidos tabs panel ── */
  .pf-tab-panel {
    display: none;
  }

  .pf-tab-panel.pf-tab-panel-active {
    display: block;
  }

  /* ── Responsive ── */
  @media (max-width: 768px) {
    .pf-layout {
      grid-template-columns: 1fr;
    }

    .pf-sidebar {
      position: static;
    }

    .pf-sports-grid {
      grid-template-columns: 1fr;
    }

    .pf-card {
      padding: 20px;
    }

    .pf-hero-name {
      font-size: 24px;
    }

    .pf-avatar-hero {
      width: 80px;
      height: 80px;
      font-size: 30px;
    }

    .pf-modal-header {
      padding: 24px 20px 18px 20px;
    }

    .pf-modal-body {
      padding: 20px;
    }
  }
</style>
@endpush

@section('content')

{{-- Scroll progress bar --}}
<div class="pf-scroll-progress" id="pfScrollProgress"></div>

@php
  $user = auth()->user();
  $sportProfiles = $user->faltaUnoSportProfiles()->get();
  $userId = $user->id;
  $misPartidos = App\Models\FaltaUnoGame::with(['field.venue', 'activeParticipants'])
    ->where(function($q) use ($userId) {
      $q->where('initiator_user_id', $userId)
        ->orWhereHas('participants', fn($q2) => $q2->where('user_id', $userId)->where('status', 'confirmed'));
    })
    ->whereIn('status', ['open', 'full', 'expired', 'cancelled'])
    ->orderByDesc('start_at')
    ->limit(15)
    ->get();
  $proximosPartidos = $misPartidos->filter(fn($g) => $g->start_at->isFuture());
  $partidosPasados  = $misPartidos->filter(fn($g) => $g->start_at->isPast());
  $totalPartidos = $misPartidos->count();
  $totalDeportes = $sportProfiles->count();
  $avgRating = $sportProfiles->isNotEmpty() ? number_format($sportProfiles->avg('average_rating'), 1) : '0.0';
@endphp

{{-- Hero --}}
<div class="pf-hero">
  <div class="pf-hero-bg"></div>
  <div class="pf-hero-overlay"></div>
  <div class="pf-hero-gradient"></div>
  <div class="pf-hero-content">
    <div data-aos="fade-up" data-aos-delay="0">
      @if($user->avatar_path)
        <img
          src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}"
          alt="Avatar"
          class="pf-avatar-hero"
        >
      @else
        <div class="pf-avatar-hero">
          {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
      @endif
    </div>

    <h1 class="pf-hero-name" data-aos="fade-up" data-aos-delay="80">
      {{ $user->name }}
    </h1>

    <div class="pf-hero-chips" data-aos="fade-up" data-aos-delay="160">
      <span class="pf-chip">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        {{ $totalPartidos }} partido{{ $totalPartidos !== 1 ? 's' : '' }}
      </span>
      <span class="pf-chip">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
        {{ $totalDeportes }} deporte{{ $totalDeportes !== 1 ? 's' : '' }}
      </span>
      @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && $user->hasVerifiedEmail())
        <span class="pf-chip pf-chip-verified">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          Email verificado
        </span>
      @endif
    </div>
  </div>
</div>

{{-- Layout dos columnas --}}
<div class="pf-layout">

  {{-- Sidebar --}}
  <aside class="pf-sidebar" data-aos="fade-right" data-aos-delay="0">
    <nav style="display:grid; gap:4px;">
      <a href="#info" class="pf-nav-item" data-pf-nav="info">
        <svg class="pf-nav-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Información personal
      </a>
      <a href="#seguridad" class="pf-nav-item" data-pf-nav="seguridad">
        <svg class="pf-nav-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        Seguridad
      </a>
      <a href="#deportivo" class="pf-nav-item" data-pf-nav="deportivo">
        <svg class="pf-nav-icon" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>
        Perfil deportivo
      </a>
      <a href="#partidos" class="pf-nav-item" data-pf-nav="partidos">
        <svg class="pf-nav-icon" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        Mis partidos
      </a>
      <a href="#peligro" class="pf-nav-item pf-nav-danger" data-pf-nav="peligro">
        <svg class="pf-nav-icon" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        Zona peligrosa
      </a>
    </nav>

    <div class="pf-sidebar-divider"></div>

    <div class="pf-sidebar-stats">
      <div class="pf-stat-box" data-aos="fade-right" data-aos-delay="100">
        <div class="pf-stat-number">{{ $totalPartidos }}</div>
        <div class="pf-stat-label">Partidos</div>
      </div>
      <div class="pf-stat-box" data-aos="fade-right" data-aos-delay="150">
        <div class="pf-stat-number">{{ $totalDeportes }}</div>
        <div class="pf-stat-label">Deportes</div>
      </div>
      <div class="pf-stat-box" data-aos="fade-right" data-aos-delay="200">
        <div class="pf-stat-number">{{ $avgRating }}</div>
        <div class="pf-stat-label">Rating</div>
      </div>
    </div>
  </aside>

  {{-- Contenido principal --}}
  <div style="display:grid; gap:24px; min-width:0;">

    {{-- Sección: Información personal --}}
    <div id="info" class="pf-section" data-aos="fade-up" data-aos-delay="100">
      @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Sección: Seguridad --}}
    <div id="seguridad" class="pf-section" data-aos="fade-up" data-aos-delay="150">
      @include('profile.partials.update-password-form')
    </div>

    {{-- Sección: Perfil deportivo --}}
    <section id="deportivo" class="pf-section pf-card" data-aos="fade-up" data-aos-delay="180">
      <div class="pf-card-header">
        <div class="pf-card-icon-wrap">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>
        </div>
        <div style="flex:1;">
          <h2 class="pf-card-title">Perfil deportivo</h2>
          <p class="pf-card-subtitle">Tus deportes y estadísticas para Falta Uno</p>
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
          @if($sportProfiles->isNotEmpty())
            <a href="{{ route('sport-profile.public', auth()->user()) }}" style="padding:9px 18px; font-size:13px; font-weight:700; color:#16a34a; border:1.5px solid #dcfce7; border-radius:10px; text-decoration:none; background:#f0fdf4; transition:all .15s;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
              Ver mi perfil publico
            </a>
          @endif
          <a href="{{ route('sport-profile.create') }}" class="pf-btn-cta" style="padding:9px 18px; font-size:13px;">
            + Agregar deporte
          </a>
        </div>
      </div>

      @if($sportProfiles->isEmpty())
        <div class="pf-empty">
          <svg class="pf-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/></svg>
          <p class="pf-empty-title">Sin perfiles deportivos</p>
          <p class="pf-empty-sub">Creá tu perfil para poder unirte a partidos Falta Uno.</p>
          <a href="{{ route('sport-profile.create') }}" class="pf-btn-cta">+ Crear perfil deportivo</a>
        </div>
      @else
        <div class="pf-sports-grid">
          @foreach($sportProfiles as $index => $sp)
          @php
            $spEmoji = match($sp->sport) {
              'football'   => '⚽',
              'padel'      => '🏓',
              'tennis'     => '🎾',
              'basketball' => '🏀',
              'volleyball' => '🏐',
              default      => '🏃',
            };
            $spName = match($sp->sport) {
              'football'   => 'Fútbol',
              'padel'      => 'Pádel',
              'tennis'     => 'Tenis',
              'basketball' => 'Básquet',
              'volleyball' => 'Vóley',
              default      => ucfirst($sp->sport),
            };
            $stars = round($sp->average_rating);
          @endphp
          <div class="pf-sport-card" data-aos="zoom-in" data-aos-delay="{{ 100 + $index * 60 }}">
            <div class="pf-sport-emoji-circle">{{ $spEmoji }}</div>
            <h3 class="pf-sport-name">{{ $spName }}</h3>
            <div class="pf-sport-badges">
              <span class="pf-sport-badge">{{ $sp->category }}</span>
              @if($sp->age_group)
                <span class="pf-sport-badge">{{ $sp->age_group }}</span>
              @endif
            </div>
            <div class="pf-sport-stats">
              <span class="pf-sport-rating">
                @for($s = 1; $s <= 5; $s++)
                  {{ $s <= $stars ? '★' : '☆' }}
                @endfor
                {{ number_format($sp->average_rating, 1) }}
              </span>
              <span class="pf-sport-games">{{ $sp->games_played }} partidos</span>
            </div>
            <a href="{{ route('sport-profile.edit', $sp->sport) }}" class="pf-sport-edit-btn">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              Editar
            </a>
          </div>
          @endforeach
        </div>
      @endif
    </section>

    {{-- Sección: Mis partidos --}}
    <section id="partidos" class="pf-section pf-card" data-aos="fade-up" data-aos-delay="200">
      <div class="pf-card-header">
        <div class="pf-card-icon-wrap">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
        <div>
          <h2 class="pf-card-title">Mis partidos</h2>
          <p class="pf-card-subtitle">Tus partidos Falta Uno</p>
        </div>
      </div>

      @if($misPartidos->isNotEmpty())
        <div class="pf-tabs">
          <button class="pf-tab pf-tab-active" onclick="pfSwitchTab('proximos', this)">
            Proximos ({{ $proximosPartidos->count() }})
          </button>
          <button class="pf-tab" onclick="pfSwitchTab('historial', this)">
            Historial ({{ $partidosPasados->count() }})
          </button>
        </div>

        {{-- Panel próximos --}}
        <div id="pf-tab-proximos" class="pf-tab-panel pf-tab-panel-active">
          @if($proximosPartidos->isNotEmpty())
            <div style="display:grid; gap:10px;">
              @foreach($proximosPartidos as $index => $pg)
              @php
                $esIniciador = $pg->initiator_user_id === $userId;
                $sportEmoji = match($pg->field->sport ?? '') {
                  'football' => '⚽', 'padel' => '🏓',
                  'tennis' => '🎾', 'basketball' => '🏀',
                  'volleyball' => '🏐', default => '🏃',
                };
              @endphp
              <div class="pf-game-card" data-aos="fade-left" data-aos-delay="{{ $index * 50 }}">
                <div class="pf-game-emoji">{{ $sportEmoji }}</div>
                <div class="pf-game-info">
                  <p class="pf-game-name">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</p>
                  <p class="pf-game-meta">
                    {{ $pg->start_at->format('d/m/Y H:i') }} hs
                    @if($esIniciador) &nbsp;·&nbsp; <span style="color:#2563eb; font-weight:700;">Organizador</span> @endif
                  </p>
                </div>
                <div class="pf-game-actions">
                  <span style="font-size:12px; font-weight:700; padding:4px 10px; border-radius:999px;
                    background:{{ $pg->status === 'full' ? '#dcfce7' : '#fef9c3' }};
                    color:{{ $pg->status === 'full' ? '#15803d' : '#854d0e' }};">
                    {{ $pg->status === 'full' ? 'Completo' : 'Buscando' }}
                  </span>
                  <a href="{{ route('falta-uno.show', $pg) }}" class="pf-game-btn">Ver partido</a>
                  <a href="{{ route('falta-uno.chat', $pg) }}" class="pf-game-btn">Chat</a>
                </div>
              </div>
              @endforeach
            </div>
          @else
            <div class="pf-empty" style="padding:28px 20px;">
              <p class="pf-empty-title">No tenés partidos próximos</p>
              <p class="pf-empty-sub">Unite a uno en Falta Uno.</p>
              <a href="{{ route('falta-uno.index') }}" class="pf-btn-cta">Ver partidos disponibles</a>
            </div>
          @endif
        </div>

        {{-- Panel historial --}}
        <div id="pf-tab-historial" class="pf-tab-panel">
          @if($partidosPasados->isNotEmpty())
            <div style="display:grid; gap:10px;">
              @foreach($partidosPasados as $index => $pg)
              @php
                $esIniciador = $pg->initiator_user_id === $userId;
                $sportEmoji = match($pg->field->sport ?? '') {
                  'football' => '⚽', 'padel' => '🏓',
                  'tennis' => '🎾', 'basketball' => '🏀',
                  'volleyball' => '🏐', default => '🏃',
                };
                $yaCalifico = App\Models\FaltaUnoRating::where('game_id', $pg->id)
                  ->where('rater_user_id', $userId)->exists();
                $hayOtros = $pg->activeParticipants->where('user_id', '!=', $userId)->isNotEmpty()
                  || ($esIniciador && $pg->activeParticipants->isNotEmpty())
                  || (!$esIniciador);
              @endphp
              <div class="pf-game-card" data-aos="fade-left" data-aos-delay="{{ $index * 50 }}" style="opacity:.85;">
                <div class="pf-game-emoji" style="opacity:.7;">{{ $sportEmoji }}</div>
                <div class="pf-game-info">
                  <p class="pf-game-name" style="color:#444;">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</p>
                  <p class="pf-game-meta">
                    {{ $pg->start_at->format('d/m/Y H:i') }} hs
                    @if($esIniciador) &nbsp;·&nbsp; Organizador @endif
                  </p>
                </div>
                <div class="pf-game-actions">
                  @if(!$yaCalifico && $hayOtros && in_array($pg->status, ['open','full','expired']))
                    <a href="{{ route('falta-uno.rate', $pg) }}" class="pf-game-btn pf-game-btn-rate" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="star" style="width:12px;height:12px;stroke:currentColor;"></i> Calificar</a>
                  @elseif($yaCalifico)
                    <span style="font-size:12px; color:#9ca3af; font-weight:600;">Calificado</span>
                  @endif
                  <a href="{{ route('falta-uno.chat', $pg) }}" class="pf-game-btn">Chat</a>
                  <a href="{{ route('falta-uno.stats', $pg) }}" class="pf-game-btn">Stats</a>
                </div>
              </div>
              @endforeach
            </div>
          @else
            <div class="pf-empty" style="padding:28px 20px;">
              <p class="pf-empty-title">Sin historial de partidos aún</p>
              <p class="pf-empty-sub">Tus partidos pasados aparecerán aquí.</p>
            </div>
          @endif
        </div>

      @else
        <div class="pf-empty">
          <svg class="pf-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          <p class="pf-empty-title">Sin partidos registrados</p>
          <p class="pf-empty-sub">Unite a tu primer partido Falta Uno y empezá a jugar.</p>
          <a href="{{ route('falta-uno.index') }}" class="pf-btn-cta">Explorar partidos</a>
        </div>
      @endif
    </section>

    {{-- Sección: Zona peligrosa --}}
    <div id="peligro" class="pf-section" data-aos="fade-up" data-aos-delay="220">
      @include('profile.partials.delete-user-form')
    </div>

  </div>{{-- fin contenido principal --}}
</div>{{-- fin pf-layout --}}

@endsection

@push('scripts')
{{-- AOS --}}
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 500, easing: 'ease-out-quad' });

  // ── Scroll progress bar ──────────────────────────────
  window.addEventListener('scroll', function () {
    var doc = document.documentElement;
    var scrolled = doc.scrollTop || document.body.scrollTop;
    var total = doc.scrollHeight - doc.clientHeight;
    var pct = total > 0 ? (scrolled / total) * 100 : 0;
    var bar = document.getElementById('pfScrollProgress');
    if (bar) bar.style.width = pct + '%';
  }, { passive: true });

  // ── Sidebar nav activa con IntersectionObserver ──────
  (function () {
    var sections = ['info', 'seguridad', 'deportivo', 'partidos', 'peligro'];
    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          sections.forEach(function (id) {
            var navItem = document.querySelector('[data-pf-nav="' + id + '"]');
            if (navItem) navItem.classList.remove('pf-nav-active');
          });
          var active = document.querySelector('[data-pf-nav="' + entry.target.id + '"]');
          if (active) active.classList.add('pf-nav-active');
        }
      });
    }, { rootMargin: '-30% 0px -60% 0px', threshold: 0 });

    sections.forEach(function (id) {
      var el = document.getElementById(id);
      if (el) observer.observe(el);
    });
  })();

  // ── Tabs partidos ────────────────────────────────────
  function pfSwitchTab(name, btn) {
    document.querySelectorAll('.pf-tab').forEach(function (t) {
      t.classList.remove('pf-tab-active');
    });
    document.querySelectorAll('.pf-tab-panel').forEach(function (p) {
      p.classList.remove('pf-tab-panel-active');
    });
    btn.classList.add('pf-tab-active');
    var panel = document.getElementById('pf-tab-' + name);
    if (panel) panel.classList.add('pf-tab-panel-active');
  }
</script>
@endpush
