@extends('layouts.app')

@section('title', 'Editar Perfil Deportivo')

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
    background: var(--color-bg-page, #f7f7f8);
  }

  .sp-page-inner {
    max-width: 560px;
    width: 100%;
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
  .sp-back:focus-visible { outline: 2px solid #22c55e; outline-offset: 2px; border-radius: 6px; }

  /* ── Error Box ──────────────────────────────────── */
  .sp-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 16px;
    padding: 14px 18px;
    font-size: 13px;
    color: #dc2626;
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
    background: rgba(0,0,0,0.025);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(0,0,0,0.04);
  }

  .sp-form-card {
    background: #fff;
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
    color: #111;
    margin-bottom: 12px;
    letter-spacing: -0.01em;
  }

  .sp-section-hint {
    font-size: 12px;
    color: var(--color-text-muted, #999);
    margin: 8px 0 0;
    line-height: 1.5;
  }

  .sp-section-hint.warning {
    color: #b45309;
  }

  .sp-field {
    margin-bottom: 28px;
  }

  /* ── Sport Badge (read-only) ────────────────────── */
  .sp-sport-display {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    border-radius: 14px;
    background: #f0fdf4;
    border: 1px solid #dcfce7;
  }

  .sp-sport-display-icon {
    color: #22c55e;
  }

  .sp-sport-display-name {
    font-size: 15px;
    font-weight: 800;
    color: #166534;
    letter-spacing: -0.01em;
  }

  /* ── Category Pills ─────────────────────────────── */
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
    border: 2px solid #ececec;
    background: #fff;
    font-size: 14px;
    font-weight: 700;
    color: #555;
    transition: border-color 350ms var(--ease-out-expo), background 350ms var(--ease-out-expo), color 350ms var(--ease-out-expo), transform 350ms var(--ease-out-expo), box-shadow 350ms var(--ease-out-expo);
    user-select: none;
  }

  .sp-cat-pill:hover {
    border-color: #d0d0d0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
  }

  .sp-cat-pill:active {
    transform: scale(0.96);
  }

  .sp-cat-pill.selected {
    border-color: #22c55e;
    background: #111;
    color: #fff;
    box-shadow: 0 4px 16px rgba(17,17,17,0.15);
  }

  .sp-cat-pill input { display: none; }

  .sp-cat-pill:focus-within {
    outline: 2px solid #22c55e;
    outline-offset: 2px;
  }

  /* Locked category badge */
  .sp-cat-locked {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 14px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    font-size: 15px;
    font-weight: 800;
    color: #15803d;
  }

  .sp-cat-locked svg {
    color: #16a34a;
  }

  /* ── Gender Toggle ──────────────────────────────── */
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
    border: 2px solid #ececec;
    background: #fff;
    text-align: center;
    transition: border-color 350ms var(--ease-out-expo), background 350ms var(--ease-out-expo), color 350ms var(--ease-out-expo), transform 350ms var(--ease-out-expo), box-shadow 350ms var(--ease-out-expo);
    user-select: none;
  }

  .sp-gender-btn:hover {
    border-color: #d0d0d0;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.05);
  }

  .sp-gender-btn:active {
    transform: translateY(0) scale(0.97);
  }

  .sp-gender-btn.selected {
    border-color: #22c55e;
    background: #111;
    color: #fff;
    box-shadow: 0 6px 20px rgba(17,17,17,0.12);
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
  }

  .sp-gender-btn:focus-within {
    outline: 2px solid #22c55e;
    outline-offset: 2px;
  }

  /* ── Select ─────────────────────────────────────── */
  .sp-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #ececec;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    color: #111;
    background: #fff;
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
    transition: background 300ms var(--ease-out-expo), color 300ms var(--ease-out-expo), transform 300ms var(--ease-out-expo), box-shadow 300ms var(--ease-out-expo);
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

  .sp-submit svg {
    transition: transform 250ms var(--ease-out-expo);
  }

  .sp-submit:hover svg {
    transform: translateX(3px);
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

  /* ── Responsive ─────────────────────────────────── */
  @media (max-width: 480px) {
    .sp-page { padding: 24px 16px 60px; }
    .sp-form-header { padding: 24px 20px 22px; }
    .sp-form-header h1 { font-size: 22px; }
    .sp-form-body { padding: 24px 20px 12px; }
    .sp-form-footer { padding: 16px 20px 24px; }
  }

  /* ── Reduced Motion ─────────────────────────────── */
  @media (prefers-reduced-motion: reduce) {
    .sp-cat-pill,
    .sp-gender-btn,
    .sp-submit,
    .sp-back svg {
      transition-duration: 0ms !important;
    }
    .sp-error { animation: none; }
    .sp-submit.loading svg { animation: none; }
  }
</style>
@endpush

@section('content')

@php
  $sportIcons = [
    'football'   => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20M2 12h20"/></svg>',
    'padel'      => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="14" rx="6"/><line x1="12" y1="16" x2="12" y2="22"/><circle cx="10" cy="8" r="1" fill="currentColor" stroke="none"/><circle cx="14" cy="8" r="1" fill="currentColor" stroke="none"/><circle cx="10" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="14" cy="12" r="1" fill="currentColor" stroke="none"/></svg>',
    'tennis'     => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M18.4 5.6a16.5 16.5 0 0 1-12.8 12.8"/><path d="M5.6 5.6a16.5 16.5 0 0 0 12.8 12.8"/></svg>',
    'basketball' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2v20"/><path d="M5.2 5.2c2.6 2 4.8 5 4.8 6.8s-2.2 4.8-4.8 6.8"/><path d="M18.8 5.2c-2.6 2-4.8 5-4.8 6.8s2.2 4.8 4.8 6.8"/></svg>',
    'volleyball' => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2c3 3 4.5 6.5 4.5 10S15 19 12 22"/><path d="M12 2c-3 3-4.5 6.5-4.5 10S9 19 12 22"/><path d="M2.6 8.5h18.8"/><path d="M2.6 15.5h18.8"/></svg>',
  ];

  $sportNames = [
    'football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis',
    'basketball' => 'Básquet', 'volleyball' => 'Vóley',
  ];
@endphp

<div class="sp-page">
  <div class="sp-page-inner">

    <a href="{{ url('/profile#sport-profile') }}" class="sp-back">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
      Volver a mi perfil
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
          <h1>Editar Perfil Deportivo</h1>
          <p>Actualizá tu información para este deporte.</p>
        </div>

        <form method="POST" action="{{ route('sport-profile.update', $profile->sport) }}" id="editProfileForm">
          @csrf
          @method('PUT')

          <div class="sp-form-body">

            {{-- Deporte (read-only) --}}
            <div class="sp-field">
              <div class="sp-section-label">Deporte</div>
              <div class="sp-sport-display">
                <span class="sp-sport-display-icon">{!! $sportIcons[$profile->sport] ?? '' !!}</span>
                <span class="sp-sport-display-name">{{ $sportNames[$profile->sport] ?? ucfirst($profile->sport) }}</span>
              </div>
            </div>

            {{-- Categoría --}}
            <div class="sp-field">
              <div class="sp-section-label">Categoría</div>
              @if($profile->games_played >= 3)
                <div class="sp-cat-locked">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  {{ ucfirst($profile->category) }}
                </div>
                <p class="sp-section-hint warning">
                  Jugaste {{ $profile->games_played }} partidos. Tu categoría se actualiza automáticamente según tu desempeño.
                </p>
                <input type="hidden" name="category" value="{{ $profile->category }}">
              @else
                <div class="sp-categories-grid">
                  @foreach($categories as $cat)
                    <label class="sp-cat-pill {{ old('category', $profile->category) === $cat ? 'selected' : '' }}">
                      <input type="radio" name="category" value="{{ $cat }}"
                             {{ old('category', $profile->category) === $cat ? 'checked' : '' }} required>
                      {{ ucfirst($cat) }}
                    </label>
                  @endforeach
                </div>
                <p class="sp-section-hint">Elegí honestamente. Después de 3 partidos, la categoría se actualiza sola.</p>
              @endif
            </div>

            {{-- Género --}}
            <div class="sp-field">
              <div class="sp-section-label">Género</div>
              <div class="sp-gender-group">
                <label class="sp-gender-btn {{ old('gender', $profile->gender) === 'male' ? 'selected' : '' }}">
                  <input type="radio" name="gender" value="male"
                         {{ old('gender', $profile->gender) === 'male' ? 'checked' : '' }} required>
                  <span class="sp-gender-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                  <span class="sp-gender-label">Masculino</span>
                </label>
                <label class="sp-gender-btn {{ old('gender', $profile->gender) === 'female' ? 'selected' : '' }}">
                  <input type="radio" name="gender" value="female"
                         {{ old('gender', $profile->gender) === 'female' ? 'checked' : '' }}>
                  <span class="sp-gender-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                  <span class="sp-gender-label">Femenino</span>
                </label>
              </div>
            </div>


          </div>

          <div class="sp-form-footer">
            <button type="submit" class="sp-submit">
              Guardar cambios
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<script>
  // Gender toggle
  document.querySelectorAll('.sp-gender-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.sp-gender-btn').forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
    });
  });

  // Category pill toggle (only if not locked)
  document.querySelectorAll('.sp-cat-pill').forEach(pill => {
    pill.addEventListener('click', () => {
      document.querySelectorAll('.sp-cat-pill').forEach(p => p.classList.remove('selected'));
      pill.classList.add('selected');
    });
  });

  // Submit loading state
  document.getElementById('editProfileForm').addEventListener('submit', function() {
    const btn = document.querySelector('.sp-submit');
    btn.classList.add('loading');
    btn.innerHTML = 'Guardando... <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>';
  });
</script>

@endsection
