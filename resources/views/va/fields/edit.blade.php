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
            <input id="sport" name="sport" class="form-control" style="width:100%;"
                   value="{{ old('sport', $field->sport) }}" required
                   placeholder="Ej: fútbol, pádel...">
          </div>
          <div>
            <label class="form-label" for="format">Formato</label>
            <input id="format" type="number" name="format" class="form-control" style="width:100%;"
                   value="{{ old('format', $field->format) }}" min="3" max="11" required
                   placeholder="5, 7, 11...">
            <p class="form-hint">Jugadores por equipo</p>
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

<script>
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
</script>
@endsection
