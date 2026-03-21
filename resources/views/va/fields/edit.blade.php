@extends('layouts.admin')

@section('title', 'Editar cancha')
@section('page_title', 'Editar cancha')
@section('page_subtitle', $field->venue->name . ' · ' . $field->name)

@push('styles')
<style>
  .field-edit-grid {
    display: grid;
    gap: 20px;
    max-width: 780px;
  }

  .fe-section {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
  }

  .fe-section-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #888;
    margin: 0 0 16px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid #f0f0f0;
  }

  .fe-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
  .fe-3col { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }

  .form-hint {
    font-size: 12px;
    color: #999;
    margin: 5px 0 0 0;
    line-height: 1.5;
  }

  /* Image upload */
  .image-upload-wrap {
    display: flex;
    gap: 20px;
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .image-preview img,
  .image-preview-placeholder {
    width: 180px;
    height: 120px;
    border-radius: 12px;
    object-fit: cover;
    display: block;
  }

  .image-preview-placeholder {
    border: 1.5px dashed #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #bbb;
    font-size: 13px;
    flex-direction: column;
    gap: 6px;
  }

  .file-input-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    border: 1.5px solid #ddd;
    border-radius: 10px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: #444;
    background: #fff;
    transition: border-color .15s, background .15s;
  }

  .file-input-label:hover { border-color: #aaa; background: #fafafa; }
  .file-input-label input[type=file] { display: none; }

  /* Night price toggle */
  .night-toggle-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .toggle-switch {
    position: relative;
    width: 42px;
    height: 24px;
    flex-shrink: 0;
  }

  .toggle-switch input { display: none; }

  .toggle-track {
    position: absolute;
    inset: 0;
    background: #ddd;
    border-radius: 999px;
    cursor: pointer;
    transition: background .2s;
  }

  .toggle-track::after {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    background: #fff;
    border-radius: 999px;
    top: 3px;
    left: 3px;
    transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
  }

  .toggle-switch input:checked + .toggle-track { background: #111; }
  .toggle-switch input:checked + .toggle-track::after { transform: translateX(18px); }

  .night-panel {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #f0f0f0;
  }

  @media (max-width: 600px) {
    .fe-2col, .fe-3col { grid-template-columns: 1fr; }
    .image-upload-wrap { flex-direction: column; }
  }
</style>
@endpush

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_field_edit',
  'helpTitle' => 'Editar cancha',
  'helpText'  => 'Acá editás la información de la cancha: nombre, deporte, precio por turno, precio nocturno y duración del turno. Los cambios se aplican de inmediato para nuevas reservas. Las reservas ya existentes no se ven afectadas.',
])

@php $hasNight = !is_null($field->price->night_price_per_slot ?? null); @endphp

<form method="POST" action="{{ route('va.fields.update', $field) }}" enctype="multipart/form-data">
  @csrf

  <div class="field-edit-grid">

    {{-- ── Imagen ── --}}
    <div class="fe-section">
      <p class="fe-section-title">Foto de la cancha</p>

      <div class="image-upload-wrap">
        <div class="image-preview">
          @if($field->cover_image_path)
            <img id="imgPreview"
                 src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
                 alt="Imagen actual">
          @else
            <div class="image-preview-placeholder" id="imgPlaceholder">
              <span style="font-size:26px;">📸</span>
              <span>Sin foto</span>
            </div>
            <img id="imgPreview" style="display:none; width:180px; height:120px; object-fit:cover; border-radius:12px; border:1px solid #eee;">
          @endif
        </div>

        <div>
          <label class="file-input-label">
            <span>📁</span>
            <span>Elegir imagen</span>
            <input type="file" name="cover_image" accept="image/*" onchange="previewFieldImage(this)">
          </label>
          <p class="form-hint" id="fileName">JPG, PNG o WEBP · máx. 4 MB</p>
        </div>
      </div>
    </div>

    {{-- ── Datos básicos ── --}}
    <div class="fe-section">
      <p class="fe-section-title">Datos básicos</p>
      <div style="display:grid; gap:14px;">

        <div>
          <label class="form-label" for="name">Nombre de la cancha</label>
          <input id="name" name="name" class="form-control" style="width:100%;"
                 value="{{ old('name', $field->name) }}" required maxlength="100"
                 placeholder="Ej: Cancha 1 · Césped">
        </div>

        <div class="fe-2col">
          <div>
            <label class="form-label" for="sport">Deporte</label>
            <select id="sport" name="sport" class="form-control" style="width:100%;" onchange="updateFormat()">
              @foreach(['football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley'] as $val=>$label)
                <option value="{{ $val }}" {{ old('sport', $field->sport) === $val ? 'selected' : '' }}>{{ $label }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="form-label" id="format-label">Formato</label>
            <select id="format-select" class="form-control" style="width:100%;" onchange="syncFormat()">
            </select>
            <input type="hidden" name="format" id="format-hidden" value="{{ old('format', $field->format) }}">
            <p class="form-hint" id="format-hint"></p>
          </div>
        </div>

        <div>
          <label class="form-label" for="slot_minutes">Duración del turno</label>
          <div style="display:flex; align-items:center; gap:10px;">
            <input id="slot_minutes" type="number" name="slot_minutes" class="form-control"
                   style="width:110px;"
                   value="{{ old('slot_minutes', $field->slot_minutes) }}" min="30" max="180" required>
            <span style="font-size:14px; color:#555;">minutos por turno</span>
          </div>
        </div>

      </div>
    </div>

    {{-- ── Precios ── --}}
    <div class="fe-section">
      <p class="fe-section-title">Precios</p>
      <div style="display:grid; gap:16px;">

        <div class="fe-2col">
          <div>
            <label class="form-label" for="price_per_slot">Precio por turno</label>
            <input id="price_per_slot" type="number" name="price_per_slot" class="form-control"
                   style="width:100%;" step="0.01" min="0"
                   value="{{ old('price_per_slot', $field->price->price_per_slot ?? 0) }}" required
                   placeholder="Ej: 12000">
          </div>
          <div>
            <label class="form-label" for="currency">Moneda</label>
            <input id="currency" name="currency" class="form-control" style="width:100%;"
                   value="{{ old('currency', $field->price->currency ?? 'ARS') }}" required>
          </div>
        </div>

        {{-- Precio nocturno --}}
        <div style="border:1px solid #ececec; border-radius:14px; padding:16px 18px; background:#fafafa;">
          <div class="night-toggle-row">
            <div>
              <div style="font-weight:700; font-size:14px; margin-bottom:2px;">🌙 Precio nocturno</div>
              <div style="font-size:13px; color:#666;">Precio diferencial en horario nocturno</div>
            </div>
            <label class="toggle-switch" title="Activar precio nocturno">
              <input type="checkbox" id="toggle_night_edit"
                     onchange="toggleNight('edit')"
                     {{ $hasNight ? 'checked' : '' }}>
              <span class="toggle-track"></span>
            </label>
          </div>

          <div id="night_panel_edit" class="night-panel" style="display:{{ $hasNight ? 'block' : 'none' }};">
            <div class="fe-3col">
              <div>
                <label class="form-label">Precio nocturno</label>
                <input type="number" name="night_price_per_slot" class="form-control"
                       style="width:100%;" step="0.01" min="0"
                       value="{{ old('night_price_per_slot', $field->price->night_price_per_slot ?? '') }}"
                       placeholder="Ej: 15000">
              </div>
              <div>
                <label class="form-label">Desde</label>
                <input type="time" name="night_start_time" class="form-control" style="width:100%;"
                       value="{{ $field->price->night_start_time ? \Carbon\Carbon::parse($field->price->night_start_time)->format('H:i') : '' }}">
              </div>
              <div>
                <label class="form-label">Hasta</label>
                <input type="time" name="night_end_time" class="form-control" style="width:100%;"
                       value="{{ $field->price->night_end_time ? \Carbon\Carbon::parse($field->price->night_end_time)->format('H:i') : '' }}">
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- ── Acciones ── --}}
    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:14px;">
        Guardar cambios
      </button>
      <a href="{{ route('va.schedule.edit', $field) }}" class="btn btn-ghost">
        🕐 Editar horarios
      </a>
      <a href="{{ route('va.dashboard') }}" class="btn btn-ghost">
        Volver
      </a>
    </div>

  </div>
</form>

{{-- ── Falta Uno ── --}}
@php $fuSetting = $field->faltaUnoSetting; @endphp
<form method="POST" action="{{ route('va.fields.falta_uno_settings', $field) }}" style="max-width:780px; margin-top:20px;">
  @csrf
  <div class="fe-section">
    <p class="fe-section-title">⚽ Falta Uno</p>

    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
      <div>
        <div style="font-weight:700; font-size:14px; margin-bottom:2px;">Habilitar Falta Uno en esta cancha</div>
        <div style="font-size:13px; color:#666;">Los usuarios podrán iniciar partidos abiertos y otros se unen para completar el equipo.</div>
      </div>
      <label class="toggle-switch" title="Habilitar Falta Uno">
        <input type="checkbox" id="toggle_falta_uno" name="enabled" value="1"
               {{ $fuSetting?->enabled ? 'checked' : '' }}
               onchange="toggleFaltaUnoPanel()">
        <span class="toggle-track"></span>
      </label>
    </div>

    <div id="falta_uno_panel" style="display:{{ $fuSetting?->enabled ? 'block' : 'none' }}; border-top:1px solid #f0f0f0; padding-top:16px;">
      <div class="fe-2col" style="margin-bottom:14px;">
        <div>
          <label class="form-label">Límite para cancelar con reembolso</label>
          <div style="display:flex; align-items:center; gap:8px;">
            <input type="number" name="refund_deadline_minutes" class="form-control"
                   style="width:100px;" min="0" max="10080"
                   value="{{ old('refund_deadline_minutes', $fuSetting?->refund_deadline_minutes ?? 60) }}">
            <span style="font-size:13px; color:#555;">minutos antes del partido</span>
          </div>
          <p class="form-hint">El iniciador puede cancelar y recibir reembolso hasta X minutos antes del partido.</p>
        </div>
        <div>
          <label class="form-label">Límite para que se llene el partido</label>
          <div style="display:flex; align-items:center; gap:8px;">
            <input type="number" name="fill_deadline_minutes" class="form-control"
                   style="width:100px;" min="0" max="10080"
                   value="{{ old('fill_deadline_minutes', $fuSetting?->fill_deadline_minutes ?? 120) }}">
            <span style="font-size:13px; color:#555;">minutos antes del partido</span>
          </div>
          <p class="form-hint">Si el partido no se llena X minutos antes, se cancela automáticamente y el slot vuelve a estar disponible. Sin reembolso.</p>
        </div>
      </div>
    </div>

    <div style="margin-top:16px;">
      <button type="submit" class="btn btn-primary" style="padding:9px 20px; font-size:13px;">
        Guardar configuración Falta Uno
      </button>
    </div>
  </div>
</form>

<script>
  function toggleFaltaUnoPanel() {
    const cb    = document.getElementById('toggle_falta_uno');
    const panel = document.getElementById('falta_uno_panel');
    panel.style.display = cb.checked ? 'block' : 'none';
  }
</script>

<script>
  const SPORT_CONFIG = {
    football:   { label: 'Jugadores por equipo', hint: 'Formato del partido', options: [{v:5,l:'5 (Fútbol 5)'},{v:7,l:'7 (Fútbol 7)'},{v:9,l:'9 (Fútbol 9)'},{v:11,l:'11 (Fútbol 11)'}] },
    padel:      { label: 'Formato', hint: 'Siempre 2 jugadores por equipo', options: [{v:2,l:'2 vs 2'}] },
    tennis:     { label: 'Modalidad', hint: '', options: [{v:1,l:'Singles (1 vs 1)'},{v:2,l:'Dobles (2 vs 2)'}] },
    basketball: { label: 'Jugadores por equipo', hint: '', options: [{v:3,l:'3 (3x3)'},{v:5,l:'5 (5x5)'}] },
    volleyball: { label: 'Formato', hint: 'Siempre 6 jugadores por equipo', options: [{v:6,l:'6 vs 6'}] },
  };

  function updateFormat() {
    const sport    = document.getElementById('sport').value;
    const config   = SPORT_CONFIG[sport] || SPORT_CONFIG.football;
    const sel      = document.getElementById('format-select');
    const current  = parseInt(document.getElementById('format-hidden').value) || 0;

    document.getElementById('format-label').textContent = config.label;
    document.getElementById('format-hint').textContent  = config.hint;

    sel.innerHTML = '';
    config.options.forEach(function(opt) {
      const o = document.createElement('option');
      o.value = opt.v;
      o.textContent = opt.l;
      if (opt.v === current) o.selected = true;
      sel.appendChild(o);
    });

    syncFormat();
  }

  function syncFormat() {
    document.getElementById('format-hidden').value = document.getElementById('format-select').value;
  }

  function toggleNight(suffix) {
    const checkbox = document.getElementById('toggle_night_' + suffix);
    const panel    = document.getElementById('night_panel_' + suffix);
    panel.style.display = checkbox.checked ? 'block' : 'none';
  }

  function previewFieldImage(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('fileName').textContent = file.name;
    const reader = new FileReader();
    reader.onload = function (e) {
      const preview     = document.getElementById('imgPreview');
      const placeholder = document.getElementById('imgPlaceholder');
      preview.src = e.target.result;
      preview.style.display = 'block';
      if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
  }

  // Init: populate format options for the current sport
  updateFormat();
</script>
@endsection
