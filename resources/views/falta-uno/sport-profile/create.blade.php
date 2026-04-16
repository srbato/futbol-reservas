@extends('layouts.app')

@section('title', 'Crear Perfil Deportivo')

@push('styles')
<style>
  /* Easings from design-tokens.css */

  /* ── Page Layout ────────────────────────────────── */
  .sp-page {
    min-height: calc(100vh - var(--header-height, 64px));
    padding: 48px 24px 80px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    background: var(--color-bg-page, #050505);
  }

  .sp-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 32px;
    max-width: 920px;
    width: 100%;
    align-items: start;
  }

  /* ── Back Link ──────────────────────────────────── */
  .sp-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--color-text-muted, #999);
    text-decoration: none;
    font-weight: 600;
    margin-bottom: 20px;
    transition: color 250ms var(--ease-out-expo);
  }
  .sp-back:hover { color: var(--color-text, #111); }
  .sp-back svg { transition: transform 250ms var(--ease-out-expo); }
  .sp-back:hover svg { transform: translateX(-3px); }

  /* ── Error Box ──────────────────────────────────── */
  .sp-error {
    background: rgba(229,57,53,.1);
    border: 1px solid rgba(229,57,53,.25);
    border-radius: 16px;
    padding: 14px 18px;
    font-size: 13px;
    color: #f87171;
    margin-bottom: 16px;
    animation: sp-shake 400ms var(--ease-out-expo);
  }

  @keyframes sp-shake {
    0%, 100% { transform: translateX(0); }
    20% { transform: translateX(-6px); }
    40% { transform: translateX(5px); }
    60% { transform: translateX(-3px); }
    80% { transform: translateX(2px); }
  }

  /* ── Form Card (Double-Bezel) ───────────────────── */
  .sp-form-shell {
    background: rgba(255,255,255,0.025);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .sp-form-card {
    background: #111;
    border-radius: calc(2rem - 5px);
    overflow: hidden;
  }

  .sp-form-header {
    background: #111;
    padding: 32px 32px 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
  }

  .sp-form-header::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -40px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.12) 0%, transparent 65%);
    pointer-events: none;
  }

  .sp-form-header-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 999px;
    background: rgba(74,222,128,0.10);
    border: 1px solid rgba(74,222,128,0.22);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: #6eeaa0;
    margin-bottom: 14px;
  }

  .sp-form-header h1 {
    margin: 0;
    font-size: 26px;
    font-weight: 900;
    letter-spacing: -0.03em;
    position: relative;
  }

  .sp-form-header p {
    margin: 8px 0 0;
    font-size: 14px;
    color: rgba(255,255,255,0.55);
    line-height: 1.5;
    position: relative;
  }

  .sp-form-body {
    padding: 28px 32px 12px;
  }

  .sp-form-footer {
    padding: 20px 32px 28px;
  }

  /* ── Section Labels ─────────────────────────────── */
  .sp-section-label {
    font-size: 13px;
    font-weight: 800;
    color: #e8e8e8;
    margin-bottom: 12px;
    letter-spacing: -0.01em;
  }

  .sp-section-hint {
    font-size: 12px;
    color: var(--color-text-muted, #999);
    margin: -4px 0 14px;
    line-height: 1.5;
  }

  /* ── Sport Cards ────────────────────────────────── */
  .sp-sports-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 10px;
    margin-bottom: 28px;
  }

  .sp-sport-card {
    position: relative;
    cursor: pointer;
    text-align: center;
    padding: 18px 8px 14px;
    border-radius: 16px;
    border: 2px solid rgba(255,255,255,.08);
    background: #0a0a0a;
    transition: border-color 350ms var(--ease-out-expo), background 350ms var(--ease-out-expo), transform 350ms var(--ease-out-expo), box-shadow 350ms var(--ease-out-expo);
    user-select: none;
  }

  .sp-sport-card:hover {
    border-color: rgba(255,255,255,.15);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  }

  .sp-sport-card:active {
    transform: translateY(-1px) scale(0.97);
  }

  .sp-sport-card.selected {
    border-color: #22c55e;
    background: rgba(34,197,94,.1);
    box-shadow: 0 0 0 3px rgba(34,197,94,0.15);
  }

  .sp-sport-card.selected .sp-sport-icon {
    transform: scale(1.15);
  }

  .sp-sport-card input { display: none; }

  .sp-sport-icon {
    width: 28px;
    height: 28px;
    margin: 0 auto 8px;
    display: block;
    color: #888;
    transition: transform 400ms var(--ease-out-expo), color 300ms var(--ease-out-expo);
  }

  .sp-sport-card.selected .sp-sport-icon {
    color: #22c55e;
  }

  .sp-sport-name {
    font-size: 12px;
    font-weight: 700;
    color: #a0a0a0;
    letter-spacing: -0.01em;
    transition: color 250ms var(--ease-out-expo);
  }

  .sp-sport-card.selected .sp-sport-name {
    color: #4ade80;
  }

  /* Check indicator */
  .sp-sport-check {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #22c55e;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transform: scale(0.5);
    transition: all 300ms var(--ease-out-expo);
  }

  .sp-sport-card.selected .sp-sport-check {
    opacity: 1;
    transform: scale(1);
  }

  @media (max-width: 480px) {
    .sp-sports-grid { grid-template-columns: repeat(3, 1fr); }
  }

  /* ── Category Pills ─────────────────────────────── */
  .sp-categories-wrap {
    margin-bottom: 28px;
    min-height: 52px;
  }

  .sp-categories-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .sp-cat-pill {
    position: relative;
    cursor: pointer;
    padding: 12px 20px;
    border-radius: 12px;
    border: 2px solid rgba(255,255,255,.08);
    background: #0a0a0a;
    font-size: 14px;
    font-weight: 700;
    color: #a0a0a0;
    transition: border-color 350ms var(--ease-out-expo), background 350ms var(--ease-out-expo), color 350ms var(--ease-out-expo), transform 350ms var(--ease-out-expo), box-shadow 350ms var(--ease-out-expo), opacity 400ms var(--ease-out-expo);
    user-select: none;
    opacity: 0;
    transform: translateY(8px);
  }

  .sp-cat-pill.entered {
    opacity: 1;
    transform: translateY(0);
  }

  .sp-cat-pill:hover {
    border-color: rgba(255,255,255,.15);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .sp-cat-pill:active {
    transform: scale(0.96);
  }

  .sp-cat-pill.selected {
    border-color: #22c55e;
    background: #22c55e;
    color: #050505;
    box-shadow: 0 4px 16px rgba(34,197,94,0.25);
  }

  .sp-cat-pill input { display: none; }

  .sp-cat-placeholder {
    font-size: 13px;
    color: #555;
    padding: 12px 0;
    font-weight: 500;
  }

  /* ── Gender Toggle ──────────────────────────────── */
  .sp-gender-wrap {
    margin-bottom: 28px;
  }

  .sp-gender-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }

  .sp-gender-btn {
    position: relative;
    cursor: pointer;
    padding: 14px 16px;
    border-radius: 14px;
    border: 2px solid rgba(255,255,255,.08);
    background: #0a0a0a;
    text-align: center;
    transition: border-color 350ms var(--ease-out-expo), background 350ms var(--ease-out-expo), color 350ms var(--ease-out-expo), transform 350ms var(--ease-out-expo), box-shadow 350ms var(--ease-out-expo);
    user-select: none;
    overflow: hidden;
  }

  .sp-gender-btn:hover {
    border-color: rgba(255,255,255,.15);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
  }

  .sp-gender-btn:active {
    transform: translateY(0) scale(0.97);
  }

  .sp-gender-btn.selected {
    border-color: #22c55e;
    background: #22c55e;
    color: #050505;
    box-shadow: 0 6px 20px rgba(34,197,94,0.25);
  }

  .sp-gender-btn input { display: none; }

  .sp-gender-icon {
    display: flex;
    justify-content: center;
    margin-bottom: 6px;
    color: #888;
    transition: transform 400ms var(--ease-out-expo), color 300ms var(--ease-out-expo);
  }

  .sp-gender-btn.selected .sp-gender-icon {
    transform: scale(1.15);
    color: #6eeaa0;
  }

  .sp-gender-label {
    font-size: 14px;
    font-weight: 700;
    transition: color 250ms var(--ease-out-expo);
  }

  .sp-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid rgba(255,255,255,.1);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #e8e8e8;
    background: #0a0a0a;
    outline: none;
    transition: border-color 250ms var(--ease-out-expo), box-shadow 250ms var(--ease-out-expo);
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
  }

  .sp-select:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
  }

  /* ── Submit Button ──────────────────────────────── */
  .sp-submit {
    width: 100%;
    padding: 16px;
    font-size: 15px;
    font-weight: 800;
    background: #22c55e;
    color: #052e14;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: all 300ms var(--ease-out-expo);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 4px 16px rgba(34,197,94,0.25);
    letter-spacing: -0.01em;
  }

  .sp-submit:hover {
    background: #16a34a;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(34,197,94,0.35);
  }

  .sp-submit:active {
    transform: translateY(0) scale(0.97);
    box-shadow: 0 2px 8px rgba(34,197,94,0.2);
  }

  .sp-submit:focus-visible {
    outline: 2px solid #22c55e;
    outline-offset: 3px;
  }

  .sp-submit:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }

  .sp-submit.loading {
    pointer-events: none;
    opacity: 0.7;
  }

  .sp-submit.loading svg {
    animation: sp-spin 800ms linear infinite;
  }

  @keyframes sp-spin {
    to { transform: rotate(360deg); }
  }

  .sp-submit svg {
    transition: transform 250ms var(--ease-out-expo);
  }

  .sp-submit:hover svg {
    transform: translateX(3px);
  }

  /* ── Live Preview Card ──────────────────────────── */
  .sp-preview-sticky {
    position: sticky;
    top: calc(var(--header-height, 64px) + 24px);
  }

  .sp-preview-label {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    color: var(--color-text-muted, #999);
    margin-bottom: 12px;
  }

  .sp-preview-shell {
    background: rgba(255,255,255,0.025);
    border-radius: 1.75rem;
    padding: 5px;
    border: 1px solid rgba(255,255,255,0.06);
    perspective: 800px;
  }

  .sp-preview-card {
    background: #111;
    border-radius: calc(1.75rem - 5px);
    padding: 32px 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    transition: transform 600ms var(--ease-out-expo);
    transform-style: preserve-3d;
  }

  .sp-preview-card::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -40px;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(34,197,94,0.10) 0%, transparent 60%);
    pointer-events: none;
  }

  .sp-preview-card::after {
    content: '';
    position: absolute;
    bottom: -40px;
    left: -20px;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(74,222,128,0.06) 0%, transparent 60%);
    pointer-events: none;
  }

  .sp-preview-avatar {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    background: linear-gradient(135deg, #1a1a1a, #2a2a2a);
    border: 2px solid rgba(255,255,255,0.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 18px;
    position: relative;
    transition: all 400ms var(--ease-out-expo);
  }

  .sp-preview-avatar.has-sport {
    background: linear-gradient(135deg, #0a3d21, #166534);
    border-color: rgba(34,197,94,0.25);
  }

  .sp-preview-name {
    font-size: 20px;
    font-weight: 900;
    letter-spacing: -0.03em;
    margin-bottom: 4px;
    position: relative;
  }

  .sp-preview-meta {
    font-size: 13px;
    color: rgba(255,255,255,0.45);
    font-weight: 600;
    margin-bottom: 24px;
    position: relative;
    transition: color 300ms var(--ease-out-expo);
  }

  .sp-preview-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
    position: relative;
  }

  .sp-preview-stat {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 12px;
    padding: 14px 10px;
    text-align: center;
    transition: background 400ms var(--ease-out-expo), border-color 400ms var(--ease-out-expo);
  }

  .sp-preview-stat-value {
    font-size: 20px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.02em;
    margin-bottom: 2px;
    font-variant-numeric: tabular-nums;
  }

  .sp-preview-stat-label {
    font-size: 10px;
    font-weight: 700;
    color: rgba(255,255,255,0.35);
    text-transform: uppercase;
    letter-spacing: .06em;
  }

  .sp-preview-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 16px;
    position: relative;
    min-height: 28px;
  }

  .sp-preview-tag {
    padding: 5px 12px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(34,197,94,0.12);
    color: #6eeaa0;
    border: 1px solid rgba(34,197,94,0.18);
    transition: opacity 350ms var(--ease-out-expo), transform 350ms var(--ease-out-expo);
    opacity: 0;
    transform: translateY(6px) scale(0.95);
  }

  .sp-preview-tag.visible {
    opacity: 1;
    transform: translateY(0) scale(1);
  }

  /* ── Responsive ─────────────────────────────────── */
  @media (max-width: 768px) {
    .sp-page { padding: 24px 16px 60px; }

    .sp-layout {
      grid-template-columns: 1fr;
      max-width: 560px;
    }

    .sp-preview-sticky {
      position: relative;
      top: 0;
    }

    /* Tooltips don't work on touch — hide them */
    .sp-cat-pill[data-tooltip]:hover::after,
    .sp-cat-pill[data-tooltip]:hover::before {
      display: none;
    }
  }

  @media (max-width: 480px) {
    .sp-form-header { padding: 24px 20px 22px; }
    .sp-form-header h1 { font-size: 22px; }
    .sp-form-body { padding: 24px 20px 8px; }
    .sp-form-footer { padding: 16px 20px 24px; }
    .sp-progress { padding: 0 20px 16px; }
  }

  @media (max-width: 360px) {
    .sp-sports-grid { grid-template-columns: repeat(2, 1fr); }
  }

  /* ── Reduced Motion ─────────────────────────────── */
  @media (prefers-reduced-motion: reduce) {
    .sp-sport-card,
    .sp-cat-pill,
    .sp-gender-btn,
    .sp-submit,
    .sp-preview-card,
    .sp-preview-tag,
    .sp-preview-avatar,
    .sp-back svg {
      transition-duration: 0ms !important;
      animation-duration: 0ms !important;
    }

    .sp-cat-pill {
      opacity: 1;
      transform: none;
      transition-duration: 0ms !important;
    }

    .sp-error { animation: none; }

    .sp-submit.loading svg { animation: none; }
  }

  /* ── Focus Accessibility ────────────────────────── */
  .sp-sport-card:focus-within,
  .sp-cat-pill:focus-within,
  .sp-gender-btn:focus-within {
    outline: 2px solid #22c55e;
    outline-offset: 2px;
    box-shadow: 0 0 0 4px rgba(34,197,94,0.15);
  }

  .sp-select:focus-visible {
    outline: none;
  }

  .sp-back:focus-visible {
    outline: 2px solid #22c55e;
    outline-offset: 2px;
    border-radius: 6px;
  }

  /* ── Progress Indicator ─────────────────────────── */
  .sp-progress {
    display: flex;
    gap: 6px;
    padding: 0 32px 20px;
    background: #111;
  }

  .sp-progress-dot {
    flex: 1;
    height: 3px;
    border-radius: 3px;
    background: rgba(255,255,255,0.1);
    transition: background 400ms var(--ease-out-expo);
  }

  .sp-progress-dot.filled {
    background: #22c55e;
  }

  /* ── Validation Highlight ───────────────────────── */
  .sp-field-error {
    animation: sp-pulse-red 600ms var(--ease-out-expo);
  }

  @keyframes sp-pulse-red {
    0% { box-shadow: 0 0 0 0 rgba(220,38,38,0.3); }
    50% { box-shadow: 0 0 0 6px rgba(220,38,38,0.1); }
    100% { box-shadow: 0 0 0 0 rgba(220,38,38,0); }
  }

  /* ── Category Tooltip ───────────────────────────── */
  .sp-cat-pill[data-tooltip]:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    padding: 6px 12px;
    border-radius: 8px;
    background: #111;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    pointer-events: none;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
  }

  .sp-cat-pill[data-tooltip]:hover::before {
    content: '';
    position: absolute;
    bottom: calc(100% + 4px);
    left: 50%;
    transform: translateX(-50%);
    border: 4px solid transparent;
    border-top-color: #333;
    pointer-events: none;
    z-index: 10;
  }

  /* ── Select font fix ────────────────────────────── */
  .sp-select {
    font-family: inherit;
  }
</style>
@endpush

@section('content')

<div class="sp-page">
  <div class="sp-layout">

    {{-- ── Left: Form ──────────────────────────────── --}}
    <div>
      <a href="{{ route('sport-profile.index') }}" class="sp-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Volver al perfil
      </a>

      @if($errors->any())
        <div class="sp-error">
          @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      @endif

      <div class="sp-form-shell">
        <div class="sp-form-card">
          <div class="sp-form-header">
            <div class="sp-form-header-badge">Falta Uno</div>
            <h1>Crear Perfil Deportivo</h1>
            <p>Elegí tu deporte y nivel para sumarte a partidos.</p>
          </div>

          <div class="sp-progress" id="progressBar">
            <div class="sp-progress-dot" data-step="sport"></div>
            <div class="sp-progress-dot" data-step="category"></div>
            <div class="sp-progress-dot" data-step="gender"></div>
          </div>

          <form method="POST" action="{{ route('sport-profile.store') }}" id="sportProfileForm">
            @csrf

            <div class="sp-form-body">

              {{-- Deporte --}}
              <div>
                <div class="sp-section-label">Deporte</div>
                <div class="sp-sports-grid">
                  {{-- Fútbol --}}
                  <label class="sp-sport-card {{ old('sport', $sport) === 'football' ? 'selected' : '' }}" data-sport="football" aria-label="Fútbol">
                    <input type="radio" name="sport" value="football" {{ old('sport', $sport) === 'football' ? 'checked' : '' }} required>
                    <svg class="sp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20M2 12h20"/></svg>
                    <span class="sp-sport-name">Fútbol</span>
                    <span class="sp-sport-check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
                  </label>
                  {{-- Pádel --}}
                  <label class="sp-sport-card {{ old('sport', $sport) === 'padel' ? 'selected' : '' }}" data-sport="padel" aria-label="Pádel">
                    <input type="radio" name="sport" value="padel" {{ old('sport', $sport) === 'padel' ? 'checked' : '' }}>
                    <svg class="sp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="14" rx="6"/><line x1="12" y1="16" x2="12" y2="22"/><circle cx="10" cy="8" r="1" fill="currentColor" stroke="none"/><circle cx="14" cy="8" r="1" fill="currentColor" stroke="none"/><circle cx="10" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="14" cy="12" r="1" fill="currentColor" stroke="none"/></svg>
                    <span class="sp-sport-name">Pádel</span>
                    <span class="sp-sport-check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
                  </label>
                  {{-- Tenis --}}
                  <label class="sp-sport-card {{ old('sport', $sport) === 'tennis' ? 'selected' : '' }}" data-sport="tennis" aria-label="Tenis">
                    <input type="radio" name="sport" value="tennis" {{ old('sport', $sport) === 'tennis' ? 'checked' : '' }}>
                    <svg class="sp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M18.4 5.6a16.5 16.5 0 0 1-12.8 12.8"/><path d="M5.6 5.6a16.5 16.5 0 0 0 12.8 12.8"/></svg>
                    <span class="sp-sport-name">Tenis</span>
                    <span class="sp-sport-check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
                  </label>
                  {{-- Básquet --}}
                  <label class="sp-sport-card {{ old('sport', $sport) === 'basketball' ? 'selected' : '' }}" data-sport="basketball" aria-label="Básquet">
                    <input type="radio" name="sport" value="basketball" {{ old('sport', $sport) === 'basketball' ? 'checked' : '' }}>
                    <svg class="sp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2v20"/><path d="M5.2 5.2c2.6 2 4.8 5 4.8 6.8s-2.2 4.8-4.8 6.8"/><path d="M18.8 5.2c-2.6 2-4.8 5-4.8 6.8s2.2 4.8 4.8 6.8"/></svg>
                    <span class="sp-sport-name">Básquet</span>
                    <span class="sp-sport-check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
                  </label>
                  {{-- Vóley --}}
                  <label class="sp-sport-card {{ old('sport', $sport) === 'volleyball' ? 'selected' : '' }}" data-sport="volleyball" aria-label="Vóley">
                    <input type="radio" name="sport" value="volleyball" {{ old('sport', $sport) === 'volleyball' ? 'checked' : '' }}>
                    <svg class="sp-sport-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2c3 3 4.5 6.5 4.5 10S15 19 12 22"/><path d="M12 2c-3 3-4.5 6.5-4.5 10S9 19 12 22"/><path d="M2.6 8.5h18.8"/><path d="M2.6 15.5h18.8"/></svg>
                    <span class="sp-sport-name">Vóley</span>
                    <span class="sp-sport-check"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg></span>
                  </label>
                </div>
              </div>

              {{-- Categoria --}}
              <div class="sp-categories-wrap">
                <div class="sp-section-label">Nivel de juego</div>
                <div class="sp-section-hint">Elegí honestamente para que los partidos sean parejos.</div>
                <div class="sp-categories-grid" id="categoriesContainer">
                  <span class="sp-cat-placeholder" id="catPlaceholder">Primero elegí un deporte</span>
                </div>
                <input type="hidden" name="category" id="categoryInput" value="{{ old('category') }}" required>
              </div>

              {{-- Genero --}}
              <div class="sp-gender-wrap">
                <div class="sp-section-label">Género</div>
                <div class="sp-gender-group">
                  <label class="sp-gender-btn {{ old('gender') === 'male' ? 'selected' : '' }}">
                    <input type="radio" name="gender" value="male"
                           {{ old('gender') === 'male' ? 'checked' : '' }} required>
                    <span class="sp-gender-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                    <span class="sp-gender-label">Masculino</span>
                  </label>
                  <label class="sp-gender-btn {{ old('gender') === 'female' ? 'selected' : '' }}">
                    <input type="radio" name="gender" value="female"
                           {{ old('gender') === 'female' ? 'checked' : '' }}>
                    <span class="sp-gender-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                    <span class="sp-gender-label">Femenino</span>
                  </label>
                </div>
              </div>


            </div>

            <div class="sp-form-footer">
              <button type="submit" class="sp-submit">
                Crear perfil
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- ── Right: Live Preview ─────────────────────── --}}
    <div class="sp-preview-sticky">
      <div class="sp-preview-label">Vista previa</div>
      <div class="sp-preview-shell">
        <div class="sp-preview-card" id="previewCard">

          <div class="sp-preview-avatar" id="previewAvatar">
            <span id="previewAvatarIcon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
          </div>

          <div class="sp-preview-name">{{ auth()->user()->name }}</div>
          <div class="sp-preview-meta" id="previewMeta">Completá el formulario</div>

          <div class="sp-preview-stats">
            <div class="sp-preview-stat">
              <div class="sp-preview-stat-value">0</div>
              <div class="sp-preview-stat-label">Partidos</div>
            </div>
            <div class="sp-preview-stat">
              <div class="sp-preview-stat-value">0</div>
              <div class="sp-preview-stat-label">Victorias</div>
            </div>
            <div class="sp-preview-stat">
              <div class="sp-preview-stat-value">&mdash;</div>
              <div class="sp-preview-stat-label">Rating</div>
            </div>
          </div>

          <div class="sp-preview-tags" id="previewTags"></div>

        </div>
      </div>
    </div>

  </div>
</div>

<script>
  const CATEGORIES = {
    padel:      ['primera', 'segunda', 'tercera', 'cuarta', 'quinta', 'sexta', 'septima', 'octava'],
    football:   ['recreativo', 'intermedio', 'avanzado', 'competitivo'],
    tennis:     ['recreativo', 'intermedio', 'avanzado', 'competitivo'],
    basketball: ['recreativo', 'intermedio', 'avanzado', 'competitivo'],
    volleyball: ['recreativo', 'intermedio', 'avanzado', 'competitivo'],
  };

  const SPORT_NAMES = {
    football: 'F\u00FAtbol', padel: 'P\u00E1del', tennis: 'Tenis',
    basketball: 'B\u00E1squet', volleyball: 'V\u00F3ley'
  };

  const SPORT_SVGS = {
    football:   '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6eeaa0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20M2 12h20"/></svg>',
    padel:      '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6eeaa0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="14" rx="6"/><line x1="12" y1="16" x2="12" y2="22"/><circle cx="10" cy="8" r="1" fill="#6eeaa0" stroke="none"/><circle cx="14" cy="8" r="1" fill="#6eeaa0" stroke="none"/><circle cx="10" cy="12" r="1" fill="#6eeaa0" stroke="none"/><circle cx="14" cy="12" r="1" fill="#6eeaa0" stroke="none"/></svg>',
    tennis:     '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6eeaa0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M18.4 5.6a16.5 16.5 0 0 1-12.8 12.8"/><path d="M5.6 5.6a16.5 16.5 0 0 0 12.8 12.8"/></svg>',
    basketball: '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6eeaa0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2v20"/><path d="M5.2 5.2c2.6 2 4.8 5 4.8 6.8s-2.2 4.8-4.8 6.8"/><path d="M18.8 5.2c-2.6 2-4.8 5-4.8 6.8s2.2 4.8 4.8 6.8"/></svg>',
    volleyball: '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#6eeaa0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2c3 3 4.5 6.5 4.5 10S15 19 12 22"/><path d="M12 2c-3 3-4.5 6.5-4.5 10S9 19 12 22"/><path d="M2.6 8.5h18.8"/><path d="M2.6 15.5h18.8"/></svg>',
  };

  const CAT_TOOLTIPS = {
    recreativo:   'Juego por diversion, sin presion',
    intermedio:   'Manejo lo basico, juego seguido',
    avanzado:     'Buen nivel tecnico y tactico',
    competitivo:  'Juego en liga o torneo regularmente',
    primera:  '1ra — Nivel mas alto',
    segunda:  '2da — Muy competitivo',
    tercera:  '3ra — Competitivo alto',
    cuarta:   '4ta — Intermedio-alto',
    quinta:   '5ta — Intermedio',
    sexta:    '6ta — Intermedio-bajo',
    septima:  '7ma — Principiante avanzado',
    octava:   '8va — Recien empezando',
  };

  const container     = document.getElementById('categoriesContainer');
  const catInput      = document.getElementById('categoryInput');
  const previewCard   = document.getElementById('previewCard');
  const previewAvatar = document.getElementById('previewAvatar');
  const previewIcon   = document.getElementById('previewAvatarIcon');
  const previewMeta   = document.getElementById('previewMeta');
  const previewTags   = document.getElementById('previewTags');

  // ── Sport Card Selection ──────────────────────────
  document.querySelectorAll('.sp-sport-card').forEach(card => {
    card.addEventListener('click', () => {
      document.querySelectorAll('.sp-sport-card').forEach(c => c.classList.remove('selected'));
      card.classList.add('selected');
      card.querySelector('input').checked = true;
      updateCategories(card.dataset.sport);
      updatePreview();
    });
  });

  // ── Category Population ───────────────────────────
  function updateCategories(sport) {
    const cats = CATEGORIES[sport] || [];
    container.innerHTML = '';

    cats.forEach((c, i) => {
      const label = document.createElement('label');
      label.className = 'sp-cat-pill';
      if (CAT_TOOLTIPS[c]) label.setAttribute('data-tooltip', CAT_TOOLTIPS[c]);
      label.innerHTML = '<input type="radio" name="category_radio" value="' + c + '">' +
                         c.charAt(0).toUpperCase() + c.slice(1);

      label.addEventListener('click', () => {
        document.querySelectorAll('.sp-cat-pill').forEach(p => p.classList.remove('selected'));
        label.classList.add('selected');
        catInput.value = c;
        updatePreview();
      });

      container.appendChild(label);

      // Stagger entrance: force reflow then add class
      setTimeout(() => label.classList.add('entered'), i * 40 + 10);
    });

    // Restore old category
    const oldCat = catInput.value;
    if (oldCat && cats.includes(oldCat)) {
      const match = container.querySelector('input[value="' + oldCat + '"]');
      if (match) match.closest('.sp-cat-pill').classList.add('selected');
    } else {
      catInput.value = '';
    }

    updatePreview();
  }

  // ── Gender Selection ──────────────────────────────
  document.querySelectorAll('.sp-gender-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.sp-gender-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      updatePreview();
    });
  });

  // ── Live Preview Update ───────────────────────────
  function updatePreview() {
    const sport    = document.querySelector('input[name="sport"]:checked');
    const category = catInput.value;
    const gender   = document.querySelector('input[name="gender"]:checked');

    // Avatar icon
    if (sport && SPORT_SVGS[sport.value]) {
      previewIcon.innerHTML = SPORT_SVGS[sport.value];
      previewAvatar.classList.add('has-sport');
    } else {
      previewIcon.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
      previewAvatar.classList.remove('has-sport');
    }

    // Meta
    const parts = [];
    if (sport) parts.push(SPORT_NAMES[sport.value]);
    if (category) parts.push(category.charAt(0).toUpperCase() + category.slice(1));
    if (gender) parts.push(gender.value === 'male' ? 'Masculino' : 'Femenino');
    previewMeta.textContent = parts.length ? parts.join(' \u00B7 ') : 'Completá el formulario';

    // Tags
    previewTags.innerHTML = '';
    if (sport) addTag(SPORT_NAMES[sport.value], 0);
    if (category) addTag(category.charAt(0).toUpperCase() + category.slice(1), 80);
    if (gender) addTag(gender.value === 'male' ? 'Masc' : 'Fem', 160);
  }

  function addTag(text, delay) {
    const tag = document.createElement('span');
    tag.className = 'sp-preview-tag';
    tag.textContent = text;
    previewTags.appendChild(tag);
    requestAnimationFrame(() => {
      setTimeout(() => tag.classList.add('visible'), delay);
    });
  }

  // ── 3D Tilt Effect (desktop only) ─────────────────
  const shell = document.querySelector('.sp-preview-shell');
  if (shell && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    shell.addEventListener('mousemove', (e) => {
      const rect = shell.getBoundingClientRect();
      const x = (e.clientX - rect.left) / rect.width - 0.5;
      const y = (e.clientY - rect.top) / rect.height - 0.5;
      previewCard.style.transform =
        'rotateY(' + (x * 8) + 'deg) rotateX(' + (-y * 6) + 'deg)';
    });

    shell.addEventListener('mouseleave', () => {
      previewCard.style.transform = 'rotateY(0deg) rotateX(0deg)';
    });
  }

  // ── Progress Bar ───────────────────────────────────
  function updateProgress() {
    const sport    = document.querySelector('input[name="sport"]:checked');
    const category = catInput.value;
    const gender   = document.querySelector('input[name="gender"]:checked');

    document.querySelector('[data-step="sport"]').classList.toggle('filled', !!sport);
    document.querySelector('[data-step="category"]').classList.toggle('filled', !!category);
    document.querySelector('[data-step="gender"]').classList.toggle('filled', !!gender);
  }

  // Patch updatePreview to also update progress
  const _origUpdatePreview = updatePreview;
  updatePreview = function() {
    _origUpdatePreview();
    updateProgress();
  };

  // ── Client-side Validation ────────────────────────
  document.getElementById('sportProfileForm').addEventListener('submit', function(e) {
    const sport    = document.querySelector('input[name="sport"]:checked');
    const category = catInput.value;
    const gender   = document.querySelector('input[name="gender"]:checked');
    const missing  = [];

    if (!sport) missing.push('.sp-sports-grid');
    if (!category) missing.push('.sp-categories-wrap');
    if (!gender) missing.push('.sp-gender-wrap');

    if (missing.length) {
      e.preventDefault();
      const first = document.querySelector(missing[0]);
      if (first) {
        first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        first.classList.add('sp-field-error');
        setTimeout(() => first.classList.remove('sp-field-error'), 700);
      }
      return;
    }

    // Show loading state
    const btn = document.querySelector('.sp-submit');
    btn.classList.add('loading');
    btn.innerHTML = 'Creando perfil... <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>';
  });

  // ── Init ──────────────────────────────────────────
  const initSport = document.querySelector('input[name="sport"]:checked');
  if (initSport) updateCategories(initSport.value);
  updatePreview();
</script>

@endsection
