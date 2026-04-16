@extends('layouts.app')

@section('title', 'Mis Stats · ' . $game->field->name)

@push('head')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
@endpush

@push('styles')
<style>
  /* ── Page ──────────────────────────────────────── */
  .fst-page {
    max-width: 540px;
    margin: 0 auto;
    padding: 0 20px 80px;
    display: grid;
    gap: 20px;
  }

  /* ── Back ──────────────────────────────────────── */
  .fst-back {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: #888;
    text-decoration: none;
    font-weight: 600;
    transition: color .15s, transform .15s;
  }
  .fst-back:hover { color: #e8e8e8; transform: translateX(-2px); }

  /* ── Hero (Double-Bezel) ───────────────────────── */
  .fst-hero-shell {
    background: rgba(255,255,255,0.03);
    border-radius: 2rem;
    padding: 5px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .fst-hero {
    position: relative;
    border-radius: calc(2rem - 5px);
    overflow: hidden;
    min-height: 200px;
    display: flex;
    align-items: center;
  }

  .fst-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/jugadores-falta-uno.webp');
    background-size: cover;
    background-position: center;
    opacity: 0.3;
  }

  .fst-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(160deg, rgba(4,18,10,0.92) 0%, rgba(10,38,22,0.85) 50%, rgba(6,24,14,0.92) 100%);
  }

  .fst-hero-orb {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
  }
  .fst-hero-orb-1 {
    top: -60px; right: -40px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, rgba(34,197,94,0.12) 0%, transparent 60%);
  }
  .fst-hero-orb-2 {
    bottom: -40px; left: -30px;
    width: 160px; height: 160px;
    background: radial-gradient(circle, rgba(74,222,128,0.06) 0%, transparent 60%);
  }

  .fst-hero-content {
    position: relative;
    z-index: 2;
    padding: 40px 32px;
    width: 100%;
  }

  .fst-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(34,197,94,0.15);
    border: 1px solid rgba(34,197,94,0.3);
    color: #4ade80;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .12em;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 999px;
    margin-bottom: 14px;
  }

  .fst-hero-title {
    margin: 0 0 8px;
    font-size: clamp(24px, 5vw, 32px);
    font-weight: 900;
    color: #fff;
    letter-spacing: -0.03em;
    line-height: 1.1;
  }

  .fst-hero-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin: 0;
  }

  .fst-hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 12px;
    font-weight: 700;
    color: rgba(255,255,255,0.75);
  }

  /* ── Form Card (Double-Bezel) ──────────────────── */
  .fst-form-shell {
    background: rgba(255,255,255,0.02);
    border-radius: 1.5rem;
    padding: 4px;
    border: 1px solid rgba(255,255,255,0.06);
  }

  .fst-form-card {
    background: #111;
    border-radius: calc(1.5rem - 4px);
    overflow: hidden;
  }

  .fst-form-body {
    padding: 28px 24px;
    display: grid;
    gap: 24px;
  }

  /* ── Stat Inputs ───────────────────────────────── */
  .fst-field-group {
    display: grid;
    gap: 8px;
  }

  .fst-field-label {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #666;
  }

  .fst-counter {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 14px;
    overflow: hidden;
    background: #0a0a0a;
    transition: border-color .2s;
  }
  .fst-counter:focus-within { border-color: #22c55e; }

  .fst-counter-btn {
    width: 52px;
    height: 52px;
    border: none;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #888;
    font-size: 20px;
    font-weight: 700;
    transition: background .15s, color .15s;
    flex-shrink: 0;
    font-family: inherit;
  }
  .fst-counter-btn:hover { background: rgba(34,197,94,.1); color: #22c55e; }
  .fst-counter-btn:active { background: rgba(34,197,94,.15); }

  .fst-counter-input {
    flex: 1;
    text-align: center;
    border: none;
    background: transparent;
    font-size: 24px;
    font-weight: 900;
    color: #e8e8e8;
    outline: none;
    font-variant-numeric: tabular-nums;
    padding: 8px 0;
    -moz-appearance: textfield;
    font-family: inherit;
  }
  .fst-counter-input::-webkit-inner-spin-button,
  .fst-counter-input::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  .fst-sport-note {
    font-size: 13px;
    color: #555;
    text-align: center;
    padding: 8px 0;
    font-style: italic;
  }

  /* ── Result Selector ───────────────────────────── */
  .fst-result-grid {
    display: grid;
    gap: 10px;
  }
  .fst-result-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
  .fst-result-grid.cols-2 { grid-template-columns: 1fr 1fr; }

  .fst-result-option {
    position: relative;
    cursor: pointer;
  }
  .fst-result-option input { position: absolute; opacity: 0; pointer-events: none; }

  .fst-result-tile {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 18px 8px;
    border: 2px solid rgba(255,255,255,.1);
    border-radius: 16px;
    background: #0a0a0a;
    transition: all .2s ease;
  }
  .fst-result-tile:hover { border-color: rgba(255,255,255,.15); }

  .fst-result-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .2s ease;
  }

  .fst-result-name {
    font-size: 13px;
    font-weight: 700;
    color: #a0a0a0;
    transition: color .2s;
  }

  /* Win */
  .fst-result-option.win .fst-result-icon { background: rgba(34,197,94,.1); color: #22c55e; }
  .fst-result-option.win input:checked ~ .fst-result-tile {
    border-color: #22c55e;
    background: rgba(34,197,94,.1);
    box-shadow: 0 0 0 3px rgba(34,197,94,0.12);
  }
  .fst-result-option.win input:checked ~ .fst-result-tile .fst-result-icon { background: #22c55e; color: #050505; }
  .fst-result-option.win input:checked ~ .fst-result-tile .fst-result-name { color: #4ade80; }

  /* Draw */
  .fst-result-option.draw .fst-result-icon { background: rgba(245,158,11,.08); color: #f59e0b; }
  .fst-result-option.draw input:checked ~ .fst-result-tile {
    border-color: #f59e0b;
    background: rgba(245,158,11,.08);
    box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
  }
  .fst-result-option.draw input:checked ~ .fst-result-tile .fst-result-icon { background: #f59e0b; color: #050505; }
  .fst-result-option.draw input:checked ~ .fst-result-tile .fst-result-name { color: #fbbf24; }

  /* Loss */
  .fst-result-option.loss .fst-result-icon { background: rgba(229,57,53,.1); color: #ef4444; }
  .fst-result-option.loss input:checked ~ .fst-result-tile {
    border-color: #ef4444;
    background: rgba(229,57,53,.1);
    box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
  }
  .fst-result-option.loss input:checked ~ .fst-result-tile .fst-result-icon { background: #ef4444; color: #050505; }
  .fst-result-option.loss input:checked ~ .fst-result-tile .fst-result-name { color: #f87171; }

  /* ── Submit ────────────────────────────────────── */
  .fst-form-footer {
    padding: 0 24px 24px;
  }

  .fst-submit {
    width: 100%;
    padding: 15px;
    background: #22c55e;
    color: #050505;
    border: none;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 800;
    cursor: pointer;
    font-family: inherit;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background .2s, transform .15s;
  }
  .fst-submit:hover { background: #22c55e; color: #052e16; transform: translateY(-1px); }
  .fst-submit:active { transform: translateY(0); }

  /* ── Alerts ────────────────────────────────────── */
  .fst-alert {
    border-radius: 14px;
    padding: 14px 18px;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .fst-alert-success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.2); color: #4ade80; }
  .fst-alert-info    { background: rgba(37,99,235,.08); border: 1px solid rgba(37,99,235,.2); color: #60a5fa; }
  .fst-alert-error   { background: rgba(229,57,53,.1); border: 1px solid rgba(229,57,53,.25); color: #f87171; }

  /* ── Responsive ────────────────────────────────── */
  @media (max-width: 480px) {
    .fst-page { padding: 0 16px 60px; }
    .fst-hero-content { padding: 28px 20px; }
    .fst-hero-title { font-size: 22px; }
    .fst-result-grid.cols-3 { grid-template-columns: 1fr; }
    .fst-result-grid.cols-2 { grid-template-columns: 1fr; }
    .fst-counter-btn { width: 44px; height: 44px; }
    .fst-counter-input { font-size: 20px; }
  }

  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { transition-duration: 0ms !important; animation-duration: 0ms !important; }
  }
</style>
@endpush

@section('content')

@php
  $sport = $game->field->sport;
  $sportLabel = match($sport) {
    'football'   => 'Fútbol',
    'padel'      => 'Pádel',
    'tennis'     => 'Tenis',
    'basketball' => 'Básquet',
    'volleyball' => 'Vóley',
    default      => ucfirst($sport),
  };
  $statsFields = match($sport) {
    'football'   => [['name'=>'goals', 'label'=>'Goles', 'icon'=>'goal'], ['name'=>'assists', 'label'=>'Asistencias', 'icon'=>'assist']],
    'basketball' => [['name'=>'goals', 'label'=>'Puntos anotados', 'icon'=>'goal'], ['name'=>'assists', 'label'=>'Asistencias', 'icon'=>'assist']],
    'volleyball' => [['name'=>'goals', 'label'=>'Puntos anotados', 'icon'=>'goal'], ['name'=>'assists', 'label'=>'Aces', 'icon'=>'assist']],
    'padel', 'tennis' => [],
    default      => [['name'=>'goals', 'label'=>'Puntos anotados', 'icon'=>'goal']],
  };
  $hasDrawOption = !in_array($sport, ['padel', 'tennis']);
@endphp

<div class="fst-page">

  {{-- Back --}}
  <div>
    <a href="{{ route('falta-uno.show', $game) }}" class="fst-back">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      Volver al partido
    </a>
  </div>

  {{-- Hero --}}
  <div class="fst-hero-shell" data-aos="fade-up">
    <div class="fst-hero">
      <div class="fst-hero-bg"></div>
      <div class="fst-hero-overlay"></div>
      <div class="fst-hero-orb fst-hero-orb-1"></div>
      <div class="fst-hero-orb fst-hero-orb-2"></div>
      <div class="fst-hero-content">
        <div class="fst-hero-badge">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          Estadísticas
        </div>
        <h1 class="fst-hero-title">¿Cómo te fue?</h1>
        <div class="fst-hero-meta">
          <span class="fst-hero-chip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ $game->field->venue->name }}
          </span>
          <span class="fst-hero-chip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ $game->start_at->format('d/m/Y') }}
          </span>
          <span class="fst-hero-chip">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ $game->start_at->format('H:i') }} hs
          </span>
          <span class="fst-hero-chip">{{ $sportLabel }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="fst-alert fst-alert-success" data-aos="fade-up">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      {{ session('success') }}
    </div>
  @endif
  @if(session('info'))
    <div class="fst-alert fst-alert-info" data-aos="fade-up">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      {{ session('info') }}
    </div>
  @endif
  @if($errors->any())
    <div class="fst-alert fst-alert-error" data-aos="fade-up">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      <div>@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
    </div>
  @endif

  {{-- Form --}}
  <form method="POST" action="{{ route('falta-uno.stats.store', $game) }}" id="fstForm">
    @csrf
    <div class="fst-form-shell" data-aos="fade-up" data-aos-delay="100">
      <div class="fst-form-card">
        <div class="fst-form-body">

          {{-- Stat counters --}}
          @if(!empty($statsFields))
            @foreach($statsFields as $field)
            <div class="fst-field-group">
              <label class="fst-field-label" for="{{ $field['name'] }}">{{ $field['label'] }}</label>
              <div class="fst-counter">
                <button type="button" class="fst-counter-btn" data-action="decrement" data-target="{{ $field['name'] }}">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
                <input type="number" id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="fst-counter-input"
                       min="0" value="{{ old($field['name'], 0) }}">
                <button type="button" class="fst-counter-btn" data-action="increment" data-target="{{ $field['name'] }}">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </button>
              </div>
            </div>
            @endforeach
          @else
            <p class="fst-sport-note">
              Para {{ $sport === 'padel' ? 'pádel' : 'tenis' }} solo registramos el resultado.
            </p>
          @endif

          {{-- Result --}}
          <div class="fst-field-group">
            <div class="fst-field-label">Resultado</div>
            <div class="fst-result-grid {{ $hasDrawOption ? 'cols-3' : 'cols-2' }}">

              <label class="fst-result-option win">
                <input type="radio" name="result" value="win" {{ old('result') === 'win' ? 'checked' : '' }} required>
                <div class="fst-result-tile">
                  <div class="fst-result-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5C7 4 6 9 6 9z"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5C17 4 18 9 18 9z"/><path d="M4 22h16"/><path d="M10 22V14a2 2 0 0 0-2-2H6"/><path d="M14 22V14a2 2 0 0 1 2-2h2"/><path d="M12 2v7"/><path d="M8 5h8"/></svg>
                  </div>
                  <span class="fst-result-name">Victoria</span>
                </div>
              </label>

              @if($hasDrawOption)
              <label class="fst-result-option draw">
                <input type="radio" name="result" value="draw" {{ old('result') === 'draw' ? 'checked' : '' }}>
                <div class="fst-result-tile">
                  <div class="fst-result-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                  </div>
                  <span class="fst-result-name">Empate</span>
                </div>
              </label>
              @endif

              <label class="fst-result-option loss">
                <input type="radio" name="result" value="loss" {{ old('result') === 'loss' ? 'checked' : '' }}>
                <div class="fst-result-tile">
                  <div class="fst-result-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2l4 4-4 4"/><path d="M3 11v-1a4 4 0 0 1 4-4h14"/><path d="M7 22l-4-4 4-4"/><path d="M21 13v1a4 4 0 0 1-4 4H3"/></svg>
                  </div>
                  <span class="fst-result-name">Derrota</span>
                </div>
              </label>

            </div>
          </div>

        </div>

        <div class="fst-form-footer">
          <button type="submit" class="fst-submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Guardar estadísticas
          </button>
        </div>
      </div>
    </div>
  </form>

</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 500, easing: 'ease-out' });

  // Counter +/- buttons
  document.querySelectorAll('.fst-counter-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      let val = parseInt(input.value) || 0;
      if (btn.dataset.action === 'increment') {
        input.value = val + 1;
      } else {
        input.value = Math.max(0, val - 1);
      }
      input.dispatchEvent(new Event('change'));
    });
  });

  // Validate result selected
  document.getElementById('fstForm').addEventListener('submit', function(e) {
    const checked = this.querySelector('input[name="result"]:checked');
    if (!checked) {
      e.preventDefault();
      alert('Seleccioná el resultado del partido.');
    }
  });
</script>
@endpush

@endsection
