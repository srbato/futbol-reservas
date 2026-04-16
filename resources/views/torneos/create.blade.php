@extends('layouts.app')

@section('title', 'Crear Torneo · TuCancha')

@push('styles')
<style>
  .tc-wrap {
    max-width: 680px;
    margin: 0 auto;
    padding-bottom: 40px;
  }

  .tc-back {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: #666;
    text-decoration: none;
    font-weight: 600;
    transition: color .15s;
    margin-bottom: 14px;
  }
  .tc-back:hover { color: #e8e8e8; }

  .tc-page-title {
    font-size: 26px;
    font-weight: 800;
    color: #e8e8e8;
    letter-spacing: -.02em;
    margin: 0 0 6px 0;
  }

  .tc-page-sub {
    font-size: 14px;
    color: #666;
    margin: 0 0 24px 0;
  }

  .tc-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.12);
    overflow: hidden;
    margin-bottom: 20px;
  }

  .tc-card-header {
    padding: 20px 24px 0;
    border-bottom: 1px solid rgba(255,255,255,.06);
    padding-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .tc-step-num {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #22c55e;
    color: #050505;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .tc-card-header-text h2 {
    font-size: 16px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.01em;
  }

  .tc-card-header-text p {
    font-size: 13px;
    color: #666;
    margin: 1px 0 0 0;
  }

  .tc-card-body {
    padding: 22px 24px;
    display: grid;
    gap: 18px;
  }

  .tc-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
  }

  .tc-input-group {
    display: grid;
    gap: 6px;
  }

  .tc-label {
    font-size: 14px;
    font-weight: 600;
    color: #a0a0a0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .tc-label .req {
    color: #f87171;
    font-weight: 700;
  }

  .tc-hint {
    font-size: 12px;
    color: #555;
    margin: 0;
    line-height: 1.5;
  }

  .tc-input,
  .tc-select,
  .tc-textarea {
    width: 100%;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 15px;
    color: #e8e8e8;
    background: #0a0a0a;
    transition: border-color .15s, box-shadow .15s;
    outline: none;
    font-family: inherit;
    box-sizing: border-box;
  }

  .tc-input:focus,
  .tc-select:focus,
  .tc-textarea:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.12);
  }

  .tc-input:disabled,
  .tc-select:disabled {
    background: #0a0a0a;
    color: #555;
    cursor: not-allowed;
  }

  .tc-textarea {
    min-height: 100px;
    resize: vertical;
  }

  .tc-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M2 4l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 36px;
  }

  .tc-radio-group {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .tc-radio-label {
    display: flex;
    align-items: center;
    gap: 7px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #a0a0a0;
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 10px;
    padding: 10px 16px;
    transition: border-color .15s, background .15s;
  }

  .tc-radio-label:hover {
    border-color: #22c55e;
    background: rgba(34,197,94,.1);
  }

  .tc-radio-label input[type="radio"] {
    accent-color: #22c55e;
  }

  .tc-radio-label input[type="radio"]:checked ~ span {
    color: #e8e8e8;
    font-weight: 600;
  }

  .tc-error-text {
    font-size: 12px;
    color: #f87171;
    margin: 0;
  }

  .tc-error-box {
    background: rgba(229,57,53,.1);
    border: 1px solid rgba(239,68,68,.3);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    color: #f87171;
    margin-bottom: 16px;
  }

  .tc-divider {
    height: 1px;
    background: rgba(255,255,255,.06);
    margin: 0 -24px;
  }

  /* Image upload */
  .tc-upload-zone {
    border: 2px dashed rgba(255,255,255,.1);
    border-radius: 14px;
    padding: 28px 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    position: relative;
  }

  .tc-upload-zone:hover {
    border-color: #22c55e;
    background: rgba(34,197,94,.1);
  }

  .tc-upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
  }

  .tc-upload-icon {
    margin-bottom: 8px;
    color: #555;
  }

  .tc-upload-text {
    font-size: 14px;
    font-weight: 600;
    color: #a0a0a0;
    margin: 0;
  }

  .tc-upload-hint {
    font-size: 12px;
    color: #555;
    margin: 4px 0 0 0;
  }

  .tc-image-preview {
    margin-top: 12px;
    border-radius: 12px;
    overflow: hidden;
    display: none;
  }

  .tc-image-preview img {
    width: 100%;
    max-height: 240px;
    object-fit: cover;
    border-radius: 12px;
  }

  /* Field picker */
  .tc-field-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .tc-field-card {
    border: 2px solid rgba(255,255,255,.08);
    border-radius: 16px;
    padding: 16px 18px;
    cursor: pointer;
    transition: border-color .15s, background .15s, box-shadow .15s;
    position: relative;
  }

  .tc-field-card:hover {
    border-color: #22c55e;
    background: rgba(34,197,94,.1);
  }

  .tc-field-card.selected {
    border-color: #22c55e;
    background: rgba(34,197,94,.1);
    box-shadow: 0 0 0 3px rgba(34,197,94,.15);
  }

  .tc-field-card-check {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #22c55e;
    display: none;
    align-items: center;
    justify-content: center;
  }

  .tc-field-card.selected .tc-field-card-check {
    display: flex;
  }

  .tc-field-card-venue {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #666;
    margin: 0 0 4px 0;
  }

  .tc-field-card-name {
    font-size: 15px;
    font-weight: 700;
    color: #e8e8e8;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .tc-field-card-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 8px;
  }

  .tc-field-card-tag {
    font-size: 12px;
    font-weight: 600;
    color: #a0a0a0;
    background: rgba(255,255,255,.06);
    border-radius: 8px;
    padding: 4px 10px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .tc-field-card-address {
    font-size: 12px;
    color: #666;
    margin: 6px 0 0 0;
  }

  .tc-field-empty {
    text-align: center;
    padding: 28px 16px;
    color: #999;
    font-size: 14px;
  }

  .tc-field-empty i {
    display: block;
    margin: 0 auto 8px;
  }

  /* Tier upsell */
  .tc-upsell-banner {
    background: rgba(245,158,11,.08);
    border: 1px solid rgba(245,158,11,.25);
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .tc-upsell-banner-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: #fbbf24;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .tc-upsell-banner-text {
    flex: 1;
    font-size: 13px;
    color: #fbbf24;
    line-height: 1.5;
  }

  .tc-upsell-banner-text strong {
    font-weight: 700;
  }

  .tc-upsell-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    font-size: 12px;
    font-weight: 700;
    color: #050505;
    background: #22c55e;
    border-radius: 10px;
    text-decoration: none;
    white-space: nowrap;
    transition: background .15s;
    flex-shrink: 0;
  }

  .tc-upsell-btn:hover {
    background: #16a34a;
    color: #fff;
  }

  .tc-upsell-inline {
    font-size: 12px;
    color: #fbbf24;
    background: rgba(245,158,11,.08);
    border: 1px solid rgba(245,158,11,.25);
    border-radius: 10px;
    padding: 8px 12px;
    margin-top: 6px;
    line-height: 1.5;
  }

  .tc-upsell-inline a {
    color: #fbbf24;
    font-weight: 700;
    text-decoration: underline;
  }

  .tc-upsell-inline a:hover {
    color: #fbbf24;
  }

  /* Sport icon badge */
  .tc-sport-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
    padding: 3px 8px;
    border-radius: 6px;
    background: rgba(59,130,246,.15);
    color: #60a5fa;
  }

  /* Submit */
  .tc-card-footer {
    padding: 18px 24px;
    border-top: 1px solid #f4f4f4;
  }

  .tc-submit {
    width: 100%;
    padding: 16px;
    font-size: 15px;
    font-weight: 700;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: background .15s, transform .1s;
    letter-spacing: -.01em;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
  }

  .tc-submit:hover { background: #16a34a; }
  .tc-submit:active { transform: scale(.98); }

  @media (max-width: 600px) {
    .tc-card-body { padding: 18px 18px; }
    .tc-card-header { padding: 16px 18px 14px; }
    .tc-card-footer { padding: 14px 18px; }
    .tc-row { grid-template-columns: 1fr; }
    .tc-divider { margin: 0 -18px; }
  }
</style>
@endpush

@section('content')

<div class="tc-wrap">

  {{-- Back --}}
  <a href="{{ route('torneos.index') }}" class="tc-back">
    <i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;"></i> Volver a torneos
  </a>

  <h1 class="tc-page-title">Crear Torneo</h1>
  <p class="tc-page-sub">Configurá tu torneo paso a paso. Podrás publicarlo cuando esté listo.</p>

  {{-- Errores globales --}}
  @if($errors->any())
    <div class="tc-error-box">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  @if($tier === 'free')
  <div class="tc-upsell-banner">
    <div class="tc-upsell-banner-icon">
      <i data-lucide="crown" style="width:20px;height:20px;stroke:#fff;stroke-width:2;"></i>
    </div>
    <div class="tc-upsell-banner-text">
      Estas usando el <strong>plan gratuito</strong>. Suscribite a Pro para torneos ilimitados, mas equipos y formatos avanzados.
    </div>
    <a href="{{ route('organizador.planes') }}" class="tc-upsell-btn">
      <i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;"></i>
      Ver planes
    </a>
  </div>
  @endif

  <form method="POST" action="{{ route('torneos.store') }}" enctype="multipart/form-data"
        x-data="{ imagePreview: null }">
    @csrf

    {{-- ═══ PASO 1: Info básica ═══ --}}
    <div class="tc-card">
      <div class="tc-card-header">
        <div class="tc-step-num">
          <i data-lucide="trophy" style="width:18px;height:18px;stroke:#22c55e;stroke-width:2;"></i>
        </div>
        <div class="tc-card-header-text">
          <h2>Informacion basica</h2>
          <p>Nombre, deporte y formato del torneo</p>
        </div>
      </div>

      <div class="tc-card-body">
        {{-- Nombre --}}
        <div class="tc-input-group">
          <label class="tc-label" for="name">
            Nombre del torneo <span class="req">*</span>
          </label>
          <input type="text" id="name" name="name" class="tc-input"
                 placeholder="Ej: Copa Verano 2026"
                 required maxlength="150"
                 value="{{ old('name') }}">
          @error('name')
            <p class="tc-error-text">{{ $message }}</p>
          @enderror
        </div>

        <div class="tc-row">
          {{-- Deporte --}}
          <div class="tc-input-group">
            <label class="tc-label" for="sport">
              Deporte <span class="req">*</span>
            </label>
            <select id="sport" name="sport" class="tc-select" required onchange="resetTcField()">
              <option value="" disabled>Elegir deporte</option>
              <option value="football">Futbol</option>
              <option value="padel">Padel</option>
              <option value="tennis">Tenis</option>
              <option value="basketball">Basquet</option>
              <option value="volleyball">Voley</option>
            </select>
            @error('sport')
              <p class="tc-error-text">{{ $message }}</p>
            @enderror
          </div>

          {{-- Formato --}}
          <div class="tc-input-group">
            <label class="tc-label" for="format">
              Formato
            </label>
            @if(count($formats) > 1)
              <select id="format" name="format" class="tc-select" required>
                @foreach($formats as $fmt)
                  <option value="{{ $fmt }}" {{ old('format', 'single_elimination') === $fmt ? 'selected' : '' }}>
                    {{ match($fmt) { 'single_elimination' => 'Eliminacion directa', 'round_robin' => 'Liga (todos contra todos)', 'groups_elimination' => 'Fase de grupos + eliminacion', default => ucfirst(str_replace('_', ' ', $fmt)) } }}
                  </option>
                @endforeach
              </select>
            @else
              <select id="format" class="tc-select" disabled>
                <option value="single_elimination" selected>Eliminacion directa</option>
              </select>
              <input type="hidden" name="format" value="single_elimination">
              @if($tier === 'free')
                <div class="tc-upsell-inline">
                  Liga y Grupos disponibles con <a href="{{ route('organizador.planes') }}">Plan Pro</a>
                </div>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- ═══ PASO 2: Configuracion ═══ --}}
    <div class="tc-card">
      <div class="tc-card-header">
        <div class="tc-step-num">
          <i data-lucide="settings" style="width:18px;height:18px;stroke:#22c55e;stroke-width:2;"></i>
        </div>
        <div class="tc-card-header-text">
          <h2>Configuracion</h2>
          <p>Equipos, jugadores y genero</p>
        </div>
      </div>

      <div class="tc-card-body">
        <div class="tc-row">
          {{-- Max equipos --}}
          <div class="tc-input-group">
            <label class="tc-label" for="max_teams">
              Cantidad de equipos <span class="req">*</span>
            </label>
            <select id="max_teams" name="max_teams" class="tc-select" required>
              <option value="4" {{ old('max_teams') == '4' ? 'selected' : '' }}>4 equipos</option>
              <option value="8" {{ old('max_teams', '8') == '8' ? 'selected' : '' }}>8 equipos</option>
              @if($tier !== 'free')
                <option value="16" {{ old('max_teams') == '16' ? 'selected' : '' }}>16 equipos</option>
                <option value="32" {{ old('max_teams') == '32' ? 'selected' : '' }}>32 equipos</option>
              @else
                <option disabled>16, 32, 64... &rarr; Plan Pro</option>
              @endif
            </select>
            @if($tier === 'free')
              <div class="tc-upsell-inline">
                Necesitas mas equipos? <a href="{{ route('organizador.planes') }}">Suscribite a Pro</a>
              </div>
            @endif
            @error('max_teams')
              <p class="tc-error-text">{{ $message }}</p>
            @enderror
          </div>

          {{-- Minimo de jugadores por equipo --}}
          <div class="tc-input-group">
            <label class="tc-label" for="players_per_team">
              Minimo de jugadores por equipo <span class="req">*</span>
            </label>
            <input type="number" id="players_per_team" name="players_per_team" class="tc-input"
                   min="1" max="30" required
                   placeholder="Ej: 5"
                   value="{{ old('players_per_team') }}">
            @error('players_per_team')
              <p class="tc-error-text">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div class="tc-divider"></div>

        {{-- Genero --}}
        <div class="tc-input-group">
          <label class="tc-label">Genero</label>
          <div class="tc-radio-group">
            @foreach(['mixed' => 'Mixto', 'male' => 'Masculino', 'female' => 'Femenino'] as $val => $gLabel)
              <label class="tc-radio-label">
                <input type="radio" name="gender_filter" value="{{ $val }}"
                       {{ old('gender_filter', 'mixed') === $val ? 'checked' : '' }}>
                <span>{{ $gLabel }}</span>
              </label>
            @endforeach
          </div>
          @error('gender_filter')
            <p class="tc-error-text">{{ $message }}</p>
          @enderror
        </div>

      </div>
    </div>

    {{-- ═══ PASO 3: Lugar ═══ --}}
    <div class="tc-card">
      <div class="tc-card-header">
        <div class="tc-step-num">
          <i data-lucide="map-pin" style="width:18px;height:18px;stroke:#22c55e;stroke-width:2;"></i>
        </div>
        <div class="tc-card-header-text">
          <h2>Canchas del torneo</h2>
          <p>Elegi una o mas canchas donde se va a jugar</p>
        </div>
      </div>

      <div class="tc-card-body">
        {{-- Container for hidden inputs --}}
        <div id="tcFieldInputs"></div>

        {{-- Selected fields list --}}
        <div id="tcFieldsSelected" style="display:none;flex-direction:column;gap:8px;margin-bottom:12px;"></div>

        {{-- Add field button --}}
        <button type="button" id="tcFieldBtn" style="display:flex;align-items:center;gap:12px;width:100%;padding:16px;border:2px dashed rgba(255,255,255,.1);border-radius:14px;background:#0a0a0a;cursor:pointer;transition:all .15s;font-family:inherit;" onclick="openTcFieldModal()" onmouseover="this.style.borderColor='#22c55e';this.style.background='rgba(34,197,94,.1)'" onmouseout="this.style.borderColor='rgba(255,255,255,.1)';this.style.background='#0a0a0a'"
          <div style="width:40px;height:40px;border-radius:10px;background:#1a1a1a;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i data-lucide="plus" style="width:18px;height:18px;stroke:#666;"></i>
          </div>
          <div style="text-align:left;">
            <div style="font-weight:700;font-size:14px;color:#e8e8e8;">Agregar cancha</div>
            <div style="font-size:12px;color:#666;">Elegi canchas de TuCancha con torneos habilitados</div>
          </div>
        </button>

        @error('field_ids')
          <p class="tc-error-text">{{ $message }}</p>
        @enderror
        @error('field_ids.*')
          <p class="tc-error-text">{{ $message }}</p>
        @enderror
      </div>
    </div>

    {{-- ═══ PASO 4: Detalles ═══ --}}
    <div class="tc-card">
      <div class="tc-card-header">
        <div class="tc-step-num">
          <i data-lucide="calendar" style="width:18px;height:18px;stroke:#22c55e;stroke-width:2;"></i>
        </div>
        <div class="tc-card-header-text">
          <h2>Detalles</h2>
          <p>Fechas, precio e inscripcion</p>
        </div>
      </div>

      <div class="tc-card-body">
        {{-- Precio inscripcion --}}
        <div class="tc-input-group">
          <label class="tc-label" for="inscription_price">
            Precio inscripcion por equipo
          </label>
          <input type="number" id="inscription_price" name="inscription_price" class="tc-input"
                 min="0" step="0.01"
                 placeholder="Ej: 5000"
                 value="{{ old('inscription_price') }}"
                 {{ !$canChargeMp ? 'disabled' : '' }}>
          @if($canChargeMp)
            <p class="tc-hint">Los equipos pagaran la inscripcion por MercadoPago al anotarse.</p>
          @else
            <p class="tc-hint">Necesitas el plan Pro para cobrar inscripcion por MercadoPago. <a href="{{ route('organizador.planes') }}" style="color:var(--color-primary);font-weight:700;text-decoration:underline;">Ver planes</a></p>
          @endif
          @error('inscription_price')
            <p class="tc-error-text">{{ $message }}</p>
          @enderror
        </div>

        <div class="tc-divider"></div>

        <div class="tc-row">
          {{-- Fecha estimada de inicio --}}
          <div class="tc-input-group">
            <label class="tc-label" for="estimated_start_date">
              Fecha estimada de inicio
            </label>
            <input type="date" id="estimated_start_date" name="estimated_start_date" class="tc-input"
                   min="{{ now()->format('Y-m-d') }}"
                   value="{{ old('estimated_start_date') }}">
            @error('estimated_start_date')
              <p class="tc-error-text">{{ $message }}</p>
            @enderror
          </div>

          {{-- Cierre de inscripcion --}}
          <div class="tc-input-group">
            <label class="tc-label" for="registration_deadline">
              Cierre de inscripcion
            </label>
            <input type="datetime-local" id="registration_deadline" name="registration_deadline" class="tc-input"
                   min="{{ now()->format('Y-m-d\TH:i') }}"
                   value="{{ old('registration_deadline') }}">
            @error('registration_deadline')
              <p class="tc-error-text">{{ $message }}</p>
            @enderror
          </div>
        </div>
      </div>
    </div>

    {{-- ═══ PASO 5: Descripcion ═══ --}}
    <div class="tc-card">
      <div class="tc-card-header">
        <div class="tc-step-num">
          <i data-lucide="file-text" style="width:18px;height:18px;stroke:#22c55e;stroke-width:2;"></i>
        </div>
        <div class="tc-card-header-text">
          <h2>Descripcion</h2>
          <p>Conta de que se trata el torneo</p>
        </div>
      </div>

      <div class="tc-card-body">
        {{-- Descripcion --}}
        <div class="tc-input-group">
          <label class="tc-label" for="description">
            Descripcion del torneo
          </label>
          <textarea id="description" name="description" class="tc-textarea"
                    placeholder="Conta los detalles del torneo: premios, dinamica, etc.">{{ old('description') }}</textarea>
          @error('description')
            <p class="tc-error-text">{{ $message }}</p>
          @enderror
        </div>

        <div class="tc-divider"></div>

        {{-- Reglas --}}
        <div class="tc-input-group">
          <label class="tc-label" for="rules">
            Reglas
          </label>
          <textarea id="rules" name="rules" class="tc-textarea"
                    placeholder="Reglas especificas del torneo (opcional)">{{ old('rules') }}</textarea>
          <p class="tc-hint">Cantidad de jugadores, duracion de partidos, tarjetas, etc.</p>
          @error('rules')
            <p class="tc-error-text">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </div>

    {{-- ═══ PASO 6: Imagen ═══ --}}
    <div class="tc-card">
      <div class="tc-card-header">
        <div class="tc-step-num">
          <i data-lucide="image" style="width:18px;height:18px;stroke:#22c55e;stroke-width:2;"></i>
        </div>
        <div class="tc-card-header-text">
          <h2>Imagen de portada</h2>
          <p>Una buena imagen atrae mas equipos</p>
        </div>
      </div>

      <div class="tc-card-body">
        <div class="tc-input-group">
          <div class="tc-upload-zone">
            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                   id="tcCoverInput"
                   onchange="previewTcCover(this)">
            <div class="tc-upload-icon" id="tcUploadIcon">
              <i data-lucide="upload-cloud" style="width:32px;height:32px;stroke:currentColor;"></i>
            </div>
            <p class="tc-upload-text" id="tcUploadText">Hace clic para subir una imagen</p>
            <p class="tc-upload-hint" id="tcUploadHint">JPG, PNG o WebP. Maximo 5 MB.</p>
          </div>

          <div id="tcImagePreview" style="display:none;margin-top:12px;border-radius:14px;overflow:hidden;border:1px solid rgba(255,255,255,.1);">
            <img id="tcImagePreviewImg" src="" alt="Preview" style="width:100%;display:block;">
          </div>

          @error('cover_image')
            <p class="tc-error-text">{{ $message }}</p>
          @enderror
        </div>
      </div>
    </div>

    {{-- ═══ PASO 7: Branding (Pro) ═══ --}}
    <div class="tc-card">
      <div class="tc-card-header">
        <div class="tc-step-num">
          <i data-lucide="palette" style="width:18px;height:18px;stroke:#22c55e;stroke-width:2;"></i>
        </div>
        <div class="tc-card-header-text">
          <h2>Personalizar colores</h2>
          <p>Dale identidad a tu torneo</p>
        </div>
        @if(!$canBrand)
          <span style="margin-left:auto;font-size:11px;font-weight:700;background:rgba(255,255,255,.06);color:#a0a0a0;padding:3px 10px;border-radius:99px;">PRO</span>
        @endif
      </div>

      <div class="tc-card-body">
        @if($canBrand)
          {{-- Color pickers --}}
          <div class="tc-row">
            <div class="tc-input-group">
              <label class="tc-label" for="primary_color">Color principal</label>
              <div style="display:flex;align-items:center;gap:10px;">
                <input type="color" id="primary_color" name="primary_color"
                       value="{{ old('primary_color', '#22c55e') }}"
                       style="width:48px;height:40px;border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:2px;cursor:pointer;background:#0a0a0a;">
                <input type="text" id="primary_color_text"
                       value="{{ old('primary_color', '#22c55e') }}"
                       style="width:90px;font-family:monospace;font-size:14px;padding:8px 12px;border:1px solid rgba(255,255,255,.1);border-radius:10px;text-transform:uppercase;background:#0a0a0a;color:#e8e8e8;"
                       maxlength="7"
                       oninput="if(/^#[0-9a-fA-F]{6}$/.test(this.value))document.getElementById('primary_color').value=this.value">
              </div>
              <p class="tc-hint">Hero, badges, botones y acentos.</p>
              @error('primary_color') <p class="tc-error-text">{{ $message }}</p> @enderror
            </div>
            <div class="tc-input-group">
              <label class="tc-label" for="secondary_color">Color secundario</label>
              <div style="display:flex;align-items:center;gap:10px;">
                <input type="color" id="secondary_color" name="secondary_color"
                       value="{{ old('secondary_color', '#111111') }}"
                       style="width:48px;height:40px;border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:2px;cursor:pointer;background:#0a0a0a;">
                <input type="text" id="secondary_color_text"
                       value="{{ old('secondary_color', '#111111') }}"
                       style="width:90px;font-family:monospace;font-size:14px;padding:8px 12px;border:1px solid rgba(255,255,255,.1);border-radius:10px;text-transform:uppercase;background:#0a0a0a;color:#e8e8e8;"
                       maxlength="7"
                       oninput="if(/^#[0-9a-fA-F]{6}$/.test(this.value))document.getElementById('secondary_color').value=this.value">
              </div>
              <p class="tc-hint">Fondo del hero, tarjetas y degradados.</p>
              @error('secondary_color') <p class="tc-error-text">{{ $message }}</p> @enderror
            </div>
          </div>

          {{-- Logo --}}
          <div class="tc-divider"></div>
          <div class="tc-input-group">
            <label class="tc-label">Logo del torneo</label>
            <div class="tc-upload-zone" style="padding:16px 14px;">
              <input type="file" name="logo_image" accept="image/jpeg,image/png,image/webp"
                     onchange="var p=document.getElementById('tcLogoPreview'),i=document.getElementById('tcLogoImg');if(this.files[0]){var r=new FileReader();r.onload=function(e){i.src=e.target.result;p.style.display='block';};r.readAsDataURL(this.files[0]);}">
              <div class="tc-upload-icon">
                <i data-lucide="image-plus" style="width:24px;height:24px;stroke:currentColor;"></i>
              </div>
              <p class="tc-upload-text" style="font-size:13px;">Subir logo</p>
              <p class="tc-upload-hint">PNG o JPG. Max 2 MB.</p>
            </div>
            <div id="tcLogoPreview" style="display:none;margin-top:8px;text-align:center;">
              <img id="tcLogoImg" src="" alt="Logo preview" style="max-width:80px;max-height:80px;border-radius:12px;border:1px solid rgba(255,255,255,.1);">
            </div>
            @error('logo_image') <p class="tc-error-text">{{ $message }}</p> @enderror
          </div>
        @else
          <div style="padding:12px 16px;background:#0a0a0a;border-radius:12px;border:1px solid rgba(255,255,255,.08);text-align:center;">
            <p style="margin:0 0 6px;font-size:14px;font-weight:600;color:#e8e8e8;">Personalizacion de colores y logo</p>
            <p style="margin:0 0 10px;font-size:13px;color:#a0a0a0;">Disponible con el plan Pro. Dale identidad propia a tu torneo.</p>
            <a href="{{ route('organizador.planes') }}" style="font-size:13px;font-weight:700;color:#22c55e;text-decoration:none;">Ver planes &rarr;</a>
          </div>
        @endif
      </div>
    </div>

    {{-- ═══ Submit ═══ --}}
    <div class="tc-card">
      <div class="tc-card-footer" style="border-top:none;">
        <button type="submit" class="tc-submit">
          <i data-lucide="plus-circle" style="width:18px;height:18px;stroke:currentColor;"></i>
          Crear Torneo
        </button>
        <p style="text-align:center; font-size:12px; color:#555; margin:10px 0 0 0;">
          El torneo se crea en borrador. Publicalo cuando estes listo.
        </p>
      </div>
    </div>

  </form>

</div>

{{-- Modal selector de cancha para torneo --}}
<div id="tcFieldModal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#111;border-radius:18px;max-width:520px;width:100%;max-height:80vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.5);">
    <div style="padding:20px 24px 0;display:flex;justify-content:space-between;align-items:center;">
      <h3 style="margin:0;font-size:20px;font-weight:800;color:#e8e8e8;">Agregar cancha</h3>
      <button onclick="document.getElementById('tcFieldModal').style.display='none'" style="background:none;border:none;cursor:pointer;padding:4px;">
        <i data-lucide="x" style="width:20px;height:20px;stroke:#999;"></i>
      </button>
    </div>
    <p style="padding:0 24px;margin:8px 0 16px;font-size:14px;color:#a0a0a0;">Podes elegir varias canchas, incluso de complejos distintos.</p>

    <div style="padding:0 24px 12px;">
      <input type="text" id="tcFieldSearchInput" placeholder="Buscar por cancha o complejo..." aria-label="Buscar cancha" oninput="filterTcFields()" style="width:100%;padding:10px 14px;border:1.5px solid rgba(255,255,255,.1);border-radius:10px;font-size:14px;outline:none;box-sizing:border-box;background:#0a0a0a;color:#e8e8e8;" onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='rgba(255,255,255,.1)'">
    </div>

    <div id="tcFieldList" style="padding:0 24px 20px;display:grid;gap:8px;">
      @php
        $tournamentFields = \App\Models\Field::whereHas('tournamentSetting', fn($q) => $q->where('tournament_enabled', true))
            ->where('is_active', true)
            ->with(['venue', 'tournamentSetting'])
            ->get();
      @endphp

      @forelse($tournamentFields as $f)
        @php
          $sportLabel = match($f->sport) { 'football'=>'Futbol','padel'=>'Padel','tennis'=>'Tenis','basketball'=>'Basquet','volleyball'=>'Voley', default=>ucfirst($f->sport) };
          $priceDisplay = $f->tournamentSetting->tournament_price_per_match ? '$' . number_format($f->tournamentSetting->tournament_price_per_match, 0, ',', '.') . '/partido' : 'A convenir';
          $isAutoApprove = $f->tournamentSetting->auto_approve ?? false;
          $contactMethod = $f->tournamentSetting->contact_method ?? 'whatsapp';
          $contactLabel = match($contactMethod) { 'whatsapp'=>'WhatsApp','phone'=>'Telefono','email'=>'Email', default=>'Plataforma' };
        @endphp
        <a href="#" class="tc-field-option"
           data-search="{{ strtolower($f->name . ' ' . $f->venue->name . ' ' . $f->sport) }}"
           data-field-id="{{ $f->id }}"
           data-field-name="{{ $f->name }}"
           data-venue-name="{{ $f->venue->name }}"
           data-sport="{{ $f->sport }}"
           onclick="event.preventDefault();toggleTcField(this)"
           style="display:flex;align-items:center;gap:12px;padding:14px;border:1.5px solid rgba(255,255,255,.08);border-radius:12px;text-decoration:none;color:inherit;transition:all .15s;"
           onmouseover="if(!this.classList.contains('tc-field-selected'))this.style.borderColor='#16a34a',this.style.background='rgba(34,197,94,.1)'"
           onmouseout="if(!this.classList.contains('tc-field-selected'))this.style.borderColor='rgba(255,255,255,.08)',this.style.background='transparent'">
          <div style="width:40px;height:40px;border-radius:10px;background:{{ $isAutoApprove ? 'rgba(34,197,94,.15)' : '#1a1a1a' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i data-lucide="{{ $isAutoApprove ? 'zap' : 'map-pin' }}" style="width:18px;height:18px;stroke:{{ $isAutoApprove ? '#16a34a' : '#666' }};stroke-width:2;"></i>
          </div>
          <div style="flex:1;min-width:0;">
            <div style="font-weight:700;font-size:14px;color:#e8e8e8;">{{ $f->name }}</div>
            <div style="font-size:12px;color:#a0a0a0;">{{ $f->venue->name }} · {{ $sportLabel }} · {{ $priceDisplay }}</div>
            <div style="font-size:11px;margin-top:3px;">
              @if($isAutoApprove)
                <span style="color:#22c55e;font-weight:600;">Se aprueba automaticamente</span>
              @else
                <span style="color:#b45309;font-weight:600;">Requiere aprobacion del complejo (via {{ $contactLabel }})</span>
              @endif
            </div>
          </div>
          <div class="tc-field-check" style="width:24px;height:24px;border-radius:50%;border:2px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s;">
          </div>
        </a>
      @empty
        <div style="text-align:center;padding:28px 16px;">
          <i data-lucide="map-pin-off" style="width:32px;height:32px;stroke:#444;stroke-width:1.5;margin-bottom:8px;"></i>
          <div style="font-weight:700;font-size:14px;color:#a0a0a0;">No hay canchas con torneos habilitados</div>
          <div style="font-size:13px;color:#666;margin-top:4px;">Cuando un complejo active torneos en sus canchas, apareceran aca.</div>
        </div>
      @endforelse
    </div>

    <div style="padding:0 24px 20px;">
      <button type="button" onclick="closeTcFieldModal()" style="width:100%;padding:12px;background:#22c55e;color:#052e16;border:none;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;font-family:inherit;transition:background .15s;" onmouseover="this.style.background='#16a34a'" onmouseout="this.style.background='#22c55e'">
        Listo
      </button>
    </div>
  </div>
</div>

<script>
var tcSelectedFields = [];

function previewTcCover(input) {
  var preview = document.getElementById('tcImagePreview');
  var img = document.getElementById('tcImagePreviewImg');
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      img.src = e.target.result;
      preview.style.display = 'block';
      document.getElementById('tcUploadText').textContent = input.files[0].name;
      document.getElementById('tcUploadHint').textContent = 'Hace clic para cambiar la imagen';
    };
    reader.readAsDataURL(input.files[0]);
  } else {
    preview.style.display = 'none';
    img.src = '';
    document.getElementById('tcUploadText').textContent = 'Hace clic para subir una imagen';
    document.getElementById('tcUploadHint').textContent = 'JPG, PNG o WebP. Maximo 5 MB.';
  }
}

function openTcFieldModal() {
  var sport = document.getElementById('sport').value;
  if (!sport) {
    alert('Primero elegi un deporte');
    return;
  }
  // Filter fields by selected sport, mark already selected
  document.querySelectorAll('.tc-field-option').forEach(function(el) {
    if (el.dataset.sport !== sport) {
      el.style.display = 'none';
      return;
    }
    el.style.display = 'flex';
    var isSelected = tcSelectedFields.some(function(f) { return f.id === el.dataset.fieldId; });
    updateFieldOptionStyle(el, isSelected);
  });
  var searchInput = document.getElementById('tcFieldSearchInput');
  if (searchInput) searchInput.value = '';
  document.getElementById('tcFieldModal').style.display = 'flex';
}

function closeTcFieldModal() {
  document.getElementById('tcFieldModal').style.display = 'none';
}

function toggleTcField(el) {
  var fieldId = el.dataset.fieldId;
  var idx = tcSelectedFields.findIndex(function(f) { return f.id === fieldId; });

  if (idx >= 0) {
    tcSelectedFields.splice(idx, 1);
    updateFieldOptionStyle(el, false);
  } else {
    tcSelectedFields.push({
      id: fieldId,
      name: el.dataset.fieldName,
      venue: el.dataset.venueName
    });
    updateFieldOptionStyle(el, true);
  }

  renderSelectedFields();
}

function updateFieldOptionStyle(el, selected) {
  var check = el.querySelector('.tc-field-check');
  if (selected) {
    el.classList.add('tc-field-selected');
    el.style.borderColor = '#22c55e';
    el.style.background = 'rgba(34,197,94,.1)';
    check.style.borderColor = '#22c55e';
    check.style.background = '#22c55e';
    check.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
  } else {
    el.classList.remove('tc-field-selected');
    el.style.borderColor = 'rgba(255,255,255,.08)';
    el.style.background = 'transparent';
    check.style.borderColor = 'rgba(255,255,255,.15)';
    check.style.background = 'transparent';
    check.innerHTML = '';
  }
}

function removeTcField(fieldId) {
  tcSelectedFields = tcSelectedFields.filter(function(f) { return f.id !== fieldId; });
  renderSelectedFields();
  // Update modal option if open
  document.querySelectorAll('.tc-field-option').forEach(function(el) {
    if (el.dataset.fieldId === fieldId) updateFieldOptionStyle(el, false);
  });
}

function renderSelectedFields() {
  var container = document.getElementById('tcFieldsSelected');
  var inputsContainer = document.getElementById('tcFieldInputs');
  var btn = document.getElementById('tcFieldBtn');

  // Clear hidden inputs
  inputsContainer.innerHTML = '';

  if (tcSelectedFields.length === 0) {
    container.style.display = 'none';
    btn.querySelector('div > div:first-child').textContent = 'Agregar cancha';
    return;
  }

  container.style.display = 'flex';
  btn.querySelector('div > div:first-child').textContent = 'Agregar otra cancha';
  container.innerHTML = '';

  tcSelectedFields.forEach(function(f) {
    // Hidden input
    var input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'field_ids[]';
    input.value = f.id;
    inputsContainer.appendChild(input);

    // Visual chip
    var chip = document.createElement('div');
    chip.style.cssText = 'display:flex;align-items:center;gap:10px;padding:10px 14px;border:1.5px solid #22c55e;border-radius:12px;background:rgba(34,197,94,.1);';
    chip.innerHTML =
      '<div style="width:32px;height:32px;border-radius:8px;background:rgba(34,197,94,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>' +
      '</div>' +
      '<div style="flex:1;min-width:0;">' +
        '<div style="font-weight:700;font-size:13px;">' + f.name + '</div>' +
        '<div style="font-size:12px;color:#666;">' + f.venue + '</div>' +
      '</div>' +
      '<button type="button" onclick="removeTcField(\'' + f.id + '\')" style="background:none;border:none;cursor:pointer;padding:4px;color:#999;transition:color .15s;" onmouseover="this.style.color=\'#ef4444\'" onmouseout="this.style.color=\'#999\'">' +
        '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' +
      '</button>';
    container.appendChild(chip);
  });
}

function filterTcFields() {
  var q = document.getElementById('tcFieldSearchInput').value.toLowerCase();
  var sport = document.getElementById('sport').value;
  document.querySelectorAll('.tc-field-option').forEach(function(el) {
    var matchesSport = !sport || el.dataset.sport === sport;
    var matchesSearch = el.dataset.search.includes(q);
    el.style.display = (matchesSport && matchesSearch) ? 'flex' : 'none';
  });
}

// Sync color pickers <-> text inputs
['primary_color', 'secondary_color'].forEach(function(id) {
  var cp = document.getElementById(id);
  var txt = document.getElementById(id + '_text');
  if (cp && txt) cp.addEventListener('input', function() { txt.value = cp.value; });
});
</script>

@endsection
