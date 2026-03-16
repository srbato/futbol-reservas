@extends('layouts.admin')

@section('title', 'Editar complejo')
@section('page_title', 'Editar complejo')
@section('page_subtitle', 'Actualizá los datos, servicios e imagen de tu complejo')

@push('styles')
<style>
  .edit-grid {
    display: grid;
    gap: 20px;
    max-width: 780px;
  }

  .edit-section {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
  }

  .edit-section-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #888;
    margin: 0 0 16px 0;
    padding-bottom: 12px;
    border-bottom: 1px solid #f0f0f0;
  }

  .field-group {
    display: grid;
    gap: 14px;
  }

  .field-group-2 {
    grid-template-columns: 1fr 1fr;
  }

  .form-hint {
    font-size: 12px;
    color: #999;
    margin: 5px 0 0 0;
    line-height: 1.5;
  }

  /* Amenities */
  .amenity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 8px;
  }

  .amenity-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    border: 1.5px solid #e5e5e5;
    border-radius: 10px;
    cursor: pointer;
    user-select: none;
    transition: border-color .15s, background .15s;
    font-size: 13px;
    color: #333;
  }

  .amenity-item:hover {
    border-color: #bbb;
    background: #fafafa;
  }

  .amenity-item.is-checked {
    border-color: #111;
    background: #f5f5f5;
    font-weight: 600;
    color: #111;
  }

  .amenity-item input[type=checkbox] {
    display: none;
  }

  .amenity-check {
    width: 18px;
    height: 18px;
    border: 1.5px solid #ccc;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background .15s, border-color .15s;
    font-size: 11px;
    color: transparent;
  }

  .amenity-item.is-checked .amenity-check {
    background: #111;
    border-color: #111;
    color: #fff;
  }

  /* Image upload */
  .image-upload-wrap {
    display: flex;
    gap: 20px;
    align-items: flex-start;
    flex-wrap: wrap;
  }

  .image-preview-wrap {
    position: relative;
    flex-shrink: 0;
  }

  .image-preview-wrap img {
    width: 180px;
    height: 120px;
    object-fit: cover;
    border-radius: 12px;
    border: 1px solid #eee;
    display: block;
  }

  .image-placeholder {
    width: 180px;
    height: 120px;
    border-radius: 12px;
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

  .file-input-label:hover {
    border-color: #aaa;
    background: #fafafa;
  }

  .file-input-label input[type=file] {
    display: none;
  }

  .file-name-display {
    font-size: 12px;
    color: #777;
    margin-top: 6px;
  }

  /* MP card */
  .mp-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 22px 24px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
    max-width: 780px;
    margin-top: 20px;
  }

  .mp-card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 6px;
  }

  .mp-logo {
    font-size: 20px;
  }

  .mp-card-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
  }

  .mp-card-desc {
    font-size: 13px;
    color: #666;
    margin: 0 0 18px 0;
    line-height: 1.5;
  }

  .mp-status-connected {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: #e8f7ee;
    border: 1px solid #c3e6cb;
    border-radius: 10px;
    color: #157347;
    font-size: 13px;
    font-weight: 600;
  }

  .mp-status-missing {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: #fff8e1;
    border: 1px solid #ffe082;
    border-radius: 10px;
    color: #b45309;
    font-size: 13px;
  }

  .btn-mp {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: #009ee3;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: opacity .15s;
  }

  .btn-mp:hover { opacity: .85; }

  @media (max-width: 600px) {
    .field-group-2 { grid-template-columns: 1fr; }
    .image-upload-wrap { flex-direction: column; }
  }
</style>
@endpush

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_venue_edit',
  'helpTitle' => 'Editar complejo',
  'helpText'  => 'Acá editás los datos generales de tu complejo: nombre, descripción, zona, dirección, coordenadas para el mapa, imagen de portada y política de cancelación. Esta información es la que ven los usuarios al buscar un complejo.',
])

<form method="POST" action="{{ route('va.venues.update', $venue) }}" enctype="multipart/form-data">
  @csrf

  <div class="edit-grid">

    {{-- ── Información básica ── --}}
    <div class="edit-section">
      <p class="edit-section-title">Información básica</p>
      <div class="field-group">

        <div>
          <label class="form-label" for="name">Nombre del complejo</label>
          <input id="name" name="name" class="form-control" style="width:100%;"
                 value="{{ old('name', $venue->name) }}" required maxlength="120"
                 placeholder="Ej: Complejo Don Juan">
        </div>

        <div>
          <label class="form-label" for="description">Descripción corta</label>
          <input id="description" name="description" class="form-control" style="width:100%;"
                 value="{{ old('description', $venue->description) }}" maxlength="255"
                 placeholder="Una línea que aparece en la página del complejo">
        </div>

      </div>
    </div>

    {{-- ── Ubicación ── --}}
    <div class="edit-section">
      <p class="edit-section-title">Ubicación</p>
      <div class="field-group">

        <div>
          <label class="form-label" for="address">Dirección</label>
          <input id="address" name="address" class="form-control" style="width:100%;"
                 value="{{ old('address', $venue->address) }}" maxlength="200"
                 placeholder="Ej: Av. Corrientes 1234, CABA">
        </div>

        <div>
          <label class="form-label" for="zone">Zona</label>
          <input id="zone" name="zone" class="form-control" style="width:100%;"
                 value="{{ old('zone', $venue->zone) }}" maxlength="120"
                 placeholder="Ej: Palermo, GBA Norte...">
        </div>

        <div class="field-group field-group-2">
          <div>
            <label class="form-label" for="lat">Latitud</label>
            <input id="lat" name="lat" class="form-control" style="width:100%;"
                   value="{{ old('lat', $venue->lat) }}" placeholder="-34.6037">
          </div>
          <div>
            <label class="form-label" for="lng">Longitud</label>
            <input id="lng" name="lng" class="form-control" style="width:100%;"
                   value="{{ old('lng', $venue->lng) }}" placeholder="-58.3816">
          </div>
        </div>

      </div>
    </div>

    {{-- ── Política de cancelación ── --}}
    <div class="edit-section">
      <p class="edit-section-title">Política de cancelación</p>

      <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
        <input type="number" id="cancellation_hours" name="cancellation_hours"
               class="form-control" style="width:120px;"
               value="{{ old('cancellation_hours', $venue->cancellation_hours) }}"
               min="1" max="720" placeholder="—">
        <label for="cancellation_hours" style="font-size:14px; color:#444;">
          horas mínimas de anticipación para cancelar
        </label>
      </div>
      <p class="form-hint">
        Dejá vacío para permitir cancelaciones en cualquier momento. Cuando se supere el límite, el botón de cancelar desaparece para el usuario.
      </p>
    </div>

    {{-- ── Servicios e instalaciones ── --}}
    <div class="edit-section">
      <p class="edit-section-title">Servicios e instalaciones</p>
      <p class="form-hint" style="margin: -8px 0 14px 0;">
        Seleccioná todo lo que ofrece el complejo. Aparece como chips en la página pública.
      </p>

      <div class="amenity-grid" id="amenityGrid">
        @foreach($amenitiesList as $key => $amenity)
          @php $checked = in_array($key, old('amenities', $venue->amenities ?? [])); @endphp
          <label class="amenity-item {{ $checked ? 'is-checked' : '' }}" id="amenity-label-{{ $key }}">
            <input type="checkbox" name="amenities[]" value="{{ $key }}" {{ $checked ? 'checked' : '' }}>
            <span class="amenity-check">✓</span>
            <span>{{ $amenity['emoji'] }} {{ $amenity['label'] }}</span>
          </label>
        @endforeach
      </div>
    </div>

    {{-- ── Imagen ── --}}
    <div class="edit-section">
      <p class="edit-section-title">Imagen del complejo</p>

      <div class="image-upload-wrap">
        <div class="image-preview-wrap">
          @if($venue->cover_image_path)
            <img id="imgPreview"
                 src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
                 alt="Imagen actual">
          @else
            <div class="image-placeholder" id="imgPlaceholder">
              <span style="font-size:28px;">🖼️</span>
              <span>Sin imagen</span>
            </div>
            <img id="imgPreview" style="display:none; width:180px; height:120px; object-fit:cover; border-radius:12px; border:1px solid #eee;">
          @endif
        </div>

        <div>
          <label class="file-input-label">
            <span>📁</span>
            <span>Elegir imagen</span>
            <input type="file" name="cover_image" accept="image/*" onchange="previewImage(this)">
          </label>
          <p class="file-name-display" id="fileName">JPG, PNG o WEBP · máx. 4 MB</p>
        </div>
      </div>
    </div>

    {{-- ── Acciones ── --}}
    <div style="display:flex; gap:10px; align-items:center;">
      <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:14px;">
        Guardar cambios
      </button>
      <a href="{{ route('va.dashboard') }}" class="btn btn-ghost">
        Volver
      </a>
    </div>

  </div>
</form>

{{-- ── Mercado Pago ── --}}
<div class="mp-card">
  <div class="mp-card-header">
    <span class="mp-logo">💳</span>
    <p class="mp-card-title">Mercado Pago</p>
  </div>
  <p class="mp-card-desc">
    Conectá tu cuenta de Mercado Pago para que los cobros lleguen directamente a tu billetera.
  </p>

  @if($venue->mp_access_token)
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
      <span class="mp-status-connected">✓ Cuenta conectada</span>
      <form method="POST" action="{{ route('va.mp_oauth.disconnect', $venue) }}"
            onsubmit="return confirm('¿Desconectar la cuenta de Mercado Pago de este complejo?')">
        @csrf
        <button type="submit" class="btn btn-ghost btn-sm">Desconectar</button>
      </form>
    </div>
  @else
    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
      <span class="mp-status-missing">⚠️ Sin cuenta conectada — los pagos irán a la cuenta general de TuCancha</span>
      <a href="{{ route('va.mp_oauth.redirect', $venue) }}" class="btn-mp">
        Conectar Mercado Pago
      </a>
    </div>
  @endif
</div>

<script>
  // Amenities toggle
  document.getElementById('amenityGrid').addEventListener('change', function (e) {
    if (e.target.type !== 'checkbox') return;
    const label = e.target.closest('.amenity-item');
    label.classList.toggle('is-checked', e.target.checked);
  });

  // Image preview
  function previewImage(input) {
    const file = input.files[0];
    if (!file) return;

    document.getElementById('fileName').textContent = file.name;

    const reader = new FileReader();
    reader.onload = function (e) {
      const preview   = document.getElementById('imgPreview');
      const placeholder = document.getElementById('imgPlaceholder');
      preview.src = e.target.result;
      preview.style.display = 'block';
      if (placeholder) placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
  }
</script>
@endsection
