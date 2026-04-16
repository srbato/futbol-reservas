@extends('layouts.app')

@section('title', 'Editar Torneo · ' . $tournament->name)

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

  .tc-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    margin-bottom: 16px;
  }

  .tc-status-draft {
    background: rgba(255,255,255,.06);
    color: #a0a0a0;
  }

  .tc-status-open {
    background: rgba(34,197,94,.15);
    color: #22c55e;
  }

  .tc-status-other {
    background: rgba(245,158,11,.08);
    color: #fbbf24;
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

  .tc-radio-label.tc-disabled {
    opacity: .6;
    cursor: not-allowed;
    pointer-events: none;
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

  .tc-info-box {
    background: rgba(59,130,246,.08);
    border: 1px solid rgba(59,130,246,.25);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    color: #60a5fa;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
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
  }

  .tc-image-preview img {
    width: 100%;
    max-height: 240px;
    object-fit: cover;
    border-radius: 12px;
  }

  .tc-existing-image {
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 10px;
  }

  .tc-existing-image img {
    width: 100%;
    max-height: 240px;
    object-fit: cover;
    border-radius: 12px;
  }

  .tc-existing-label {
    font-size: 12px;
    color: #a0a0a0;
    margin: 0 0 8px 0;
    font-weight: 600;
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
    background: #22c55e;
    color: #050505;
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

@php
  $isDraft = $tournament->status === \App\Models\Tournament::STATUS_DRAFT;
  $locked = !$isDraft;
@endphp

<div class="tc-wrap">

  {{-- Back --}}
  <a href="{{ route('torneos.manage', $tournament) }}" class="tc-back">
    <i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;"></i> Volver a gestion
  </a>

  <h1 class="tc-page-title">Editar Torneo</h1>

  {{-- Status badge --}}
  @if($isDraft)
    <div class="tc-status-badge tc-status-draft">
      <i data-lucide="file-edit" style="width:13px;height:13px;stroke:currentColor;"></i> Borrador
    </div>
  @elseif($tournament->status === \App\Models\Tournament::STATUS_OPEN_REGISTRATION)
    <div class="tc-status-badge tc-status-open">
      <i data-lucide="users" style="width:13px;height:13px;stroke:currentColor;"></i> Inscripcion abierta
    </div>
  @else
    <div class="tc-status-badge tc-status-other">
      <i data-lucide="info" style="width:13px;height:13px;stroke:currentColor;"></i> {{ ucfirst(str_replace('_', ' ', $tournament->status)) }}
    </div>
  @endif

  @if($locked)
    <div class="tc-info-box">
      <i data-lucide="lock" style="width:16px;height:16px;stroke:currentColor;flex-shrink:0;"></i>
      Algunos campos no se pueden modificar porque el torneo ya no esta en borrador.
    </div>
  @endif

  {{-- Errores globales --}}
  @if($errors->any())
    <div class="tc-error-box">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('torneos.update', $tournament) }}" enctype="multipart/form-data"
        x-data="{ imagePreview: null }">
    @csrf
    @method('PUT')

    {{-- ═══ PASO 1: Info basica ═══ --}}
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
                 value="{{ old('name', $tournament->name) }}">
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
            <select id="sport" name="sport" class="tc-select" required {{ $locked ? 'disabled' : '' }}>
              <option value="" disabled>Elegir deporte</option>
              <option value="football" {{ old('sport', $tournament->sport) === 'football' ? 'selected' : '' }}>Futbol</option>
              <option value="padel" {{ old('sport', $tournament->sport) === 'padel' ? 'selected' : '' }}>Padel</option>
              <option value="tennis" {{ old('sport', $tournament->sport) === 'tennis' ? 'selected' : '' }}>Tenis</option>
              <option value="basketball" {{ old('sport', $tournament->sport) === 'basketball' ? 'selected' : '' }}>Basquet</option>
              <option value="volleyball" {{ old('sport', $tournament->sport) === 'volleyball' ? 'selected' : '' }}>Voley</option>
            </select>
            @if($locked)
              <input type="hidden" name="sport" value="{{ $tournament->sport }}">
            @endif
            @error('sport')
              <p class="tc-error-text">{{ $message }}</p>
            @enderror
          </div>

          {{-- Formato --}}
          <div class="tc-input-group">
            <label class="tc-label" for="format">
              Formato
            </label>
            <select id="format" class="tc-select" disabled>
              <option value="single_elimination" selected>Eliminacion directa</option>
            </select>
            <input type="hidden" name="format" value="single_elimination">
            <p class="tc-hint">Mas formatos proximamente</p>
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
              <option value="4" {{ old('max_teams', $tournament->max_teams) == 4 ? 'selected' : '' }}>4 equipos</option>
              <option value="8" {{ old('max_teams', $tournament->max_teams) == 8 ? 'selected' : '' }}>8 equipos</option>
              <option value="16" {{ old('max_teams', $tournament->max_teams) == 16 ? 'selected' : '' }}>16 equipos</option>
              <option value="32" {{ old('max_teams', $tournament->max_teams) == 32 ? 'selected' : '' }}>32 equipos</option>
            </select>
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
                   value="{{ old('players_per_team', $tournament->players_per_team) }}">
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
                       {{ old('gender_filter', $tournament->gender_filter) === $val ? 'checked' : '' }}>
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

    {{-- ═══ PASO 3: Canchas ═══ --}}
    <div class="tc-card">
      <div class="tc-card-header">
        <div class="tc-step-num">
          <i data-lucide="map-pin" style="width:18px;height:18px;stroke:#22c55e;stroke-width:2;"></i>
        </div>
        <div class="tc-card-header-text">
          <h2>Canchas del torneo</h2>
          <p>Las canchas se eligen al crear el torneo</p>
        </div>
      </div>

      <div class="tc-card-body">
        @php $tournamentFields = $tournament->fields()->with('venue')->get(); @endphp
        @if($tournamentFields->isNotEmpty())
          <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach($tournamentFields as $tf)
              @php
                $vr = $tournament->venueRequests()->where('field_id', $tf->id)->first();
                $vrStatus = $vr?->status ?? 'pending';
              @endphp
              <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1.5px solid {{ $vrStatus === 'approved' ? '#22c55e' : ($vrStatus === 'rejected' ? '#f87171' : '#fbbf24') }};border-radius:12px;background:{{ $vrStatus === 'approved' ? 'rgba(34,197,94,.1)' : ($vrStatus === 'rejected' ? 'rgba(229,57,53,.1)' : 'rgba(245,158,11,.08)') }};">
                @if($vrStatus === 'approved')
                  <svg width="16" height="16" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif($vrStatus === 'rejected')
                  <svg width="16" height="16" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                @else
                  <svg width="16" height="16" fill="none" stroke="#f59e0b" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                @endif
                <div style="flex:1;min-width:0;">
                  <div style="font-weight:700;font-size:13px;color:#e8e8e8;">{{ $tf->name }}</div>
                  <div style="font-size:12px;color:#a0a0a0;">{{ $tf->venue->name }}</div>
                </div>
                <span style="font-size:11px;font-weight:700;color:{{ $vrStatus === 'approved' ? '#22c55e' : ($vrStatus === 'rejected' ? '#f87171' : '#d97706') }};">
                  {{ $vrStatus === 'approved' ? 'Aprobada' : ($vrStatus === 'rejected' ? 'Rechazada' : 'Pendiente') }}
                </span>
              </div>
            @endforeach
          </div>
        @else
          <div style="padding:16px;text-align:center;color:#666;font-size:14px;">
            No hay canchas vinculadas a este torneo.
          </div>
        @endif
        <p class="tc-hint">Para cambiar las canchas, crea un nuevo torneo.</p>
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
            Precio inscripcion por equipo (informativo)
          </label>
          <input type="number" id="inscription_price" name="inscription_price" class="tc-input"
                 min="0" step="0.01"
                 placeholder="Ej: 5000"
                 value="{{ old('inscription_price', $tournament->inscription_price) }}">
          <p class="tc-hint">Solo informativo. El cobro se gestiona por fuera de la plataforma.</p>
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
                   value="{{ old('estimated_start_date', $tournament->estimated_start_date?->format('Y-m-d')) }}">
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
                   value="{{ old('registration_deadline', $tournament->registration_deadline?->format('Y-m-d\TH:i')) }}">
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
                    placeholder="Conta los detalles del torneo: premios, dinamica, etc.">{{ old('description', $tournament->description) }}</textarea>
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
                    placeholder="Reglas especificas del torneo (opcional)">{{ old('rules', $tournament->rules) }}</textarea>
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

          {{-- Imagen existente --}}
          @if($tournament->cover_image_path)
            <div class="tc-existing-image" x-show="!imagePreview">
              <p class="tc-existing-label">Imagen actual:</p>
              <img src="{{ \Illuminate\Support\Facades\Storage::url($tournament->cover_image_path) }}"
                   alt="{{ $tournament->name }}">
            </div>
          @endif

          <div class="tc-upload-zone" @click="$refs.coverInput.click()">
            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                   x-ref="coverInput"
                   @change="
                     const file = $event.target.files[0];
                     if (file) {
                       const reader = new FileReader();
                       reader.onload = (e) => { imagePreview = e.target.result; };
                       reader.readAsDataURL(file);
                     } else {
                       imagePreview = null;
                     }
                   ">
            <div class="tc-upload-icon">
              <i data-lucide="upload-cloud" style="width:32px;height:32px;stroke:currentColor;"></i>
            </div>
            <p class="tc-upload-text">{{ $tournament->cover_image_path ? 'Cambiar imagen' : 'Subir imagen' }}</p>
            <p class="tc-upload-hint">JPG, PNG o WebP. Maximo 5 MB.</p>
          </div>

          <div class="tc-image-preview" x-show="imagePreview" style="display:none;">
            <img :src="imagePreview" alt="Preview">
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
                       value="{{ old('primary_color', $tournament->primary_color ?? '#22c55e') }}"
                       style="width:48px;height:40px;border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:2px;cursor:pointer;background:#0a0a0a;">
                <input type="text" id="primary_color_text"
                       value="{{ old('primary_color', $tournament->primary_color ?? '#22c55e') }}"
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
                       value="{{ old('secondary_color', $tournament->secondary_color ?? '#111111') }}"
                       style="width:48px;height:40px;border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:2px;cursor:pointer;background:#0a0a0a;">
                <input type="text" id="secondary_color_text"
                       value="{{ old('secondary_color', $tournament->secondary_color ?? '#111111') }}"
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
            @if($tournament->logo_image_path)
              <div style="margin-bottom:8px;text-align:center;">
                <p class="tc-existing-label">Logo actual:</p>
                <img src="{{ \Illuminate\Support\Facades\Storage::url($tournament->logo_image_path) }}"
                     alt="Logo" style="max-width:80px;max-height:80px;border-radius:12px;border:1px solid rgba(255,255,255,.1);">
              </div>
            @endif
            <div class="tc-upload-zone" style="padding:16px 14px;">
              <input type="file" name="logo_image" accept="image/jpeg,image/png,image/webp"
                     onchange="var p=document.getElementById('tcLogoPreview'),i=document.getElementById('tcLogoImg');if(this.files[0]){var r=new FileReader();r.onload=function(e){i.src=e.target.result;p.style.display='block';};r.readAsDataURL(this.files[0]);}">
              <div class="tc-upload-icon">
                <i data-lucide="image-plus" style="width:24px;height:24px;stroke:currentColor;"></i>
              </div>
              <p class="tc-upload-text" style="font-size:13px;">{{ $tournament->logo_image_path ? 'Cambiar logo' : 'Subir logo' }}</p>
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
            <p style="margin:0 0 10px;font-size:13px;color:#a0a0a0;">Disponible con el plan Pro.</p>
            <a href="{{ route('organizador.planes') }}" style="font-size:13px;font-weight:700;color:#22c55e;text-decoration:none;">Ver planes &rarr;</a>
          </div>
        @endif
      </div>
    </div>

    {{-- ═══ Submit ═══ --}}
    <div class="tc-card">
      <div class="tc-card-footer" style="border-top:none;">
        <button type="submit" class="tc-submit">
          <i data-lucide="save" style="width:18px;height:18px;stroke:currentColor;"></i>
          Guardar Cambios
        </button>
      </div>
    </div>

  </form>

<script>
['primary_color', 'secondary_color'].forEach(function(id) {
  var cp = document.getElementById(id);
  var txt = document.getElementById(id + '_text');
  if (cp && txt) cp.addEventListener('input', function() { txt.value = cp.value; });
});
</script>

</div>

@endsection
