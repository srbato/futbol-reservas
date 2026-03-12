@extends('layouts.admin')

@section('title', 'Editar complejo')
@section('page_title', 'Editar complejo')
@section('page_subtitle', 'Actualizá los datos principales del complejo')

@section('content')
  <div class="admin-card">
    <form method="POST" action="{{ route('va.venues.update', $venue) }}" enctype="multipart/form-data">
      @csrf

      <div style="display:grid; gap:14px; max-width:700px;">
        <div>
          <label>Nombre</label><br>
          <input name="name" value="{{ old('name', $venue->name) }}" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Descripción corta</label><br>
          <input name="description" value="{{ old('description', $venue->description) }}" maxlength="255"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Dirección</label><br>
          <input name="address" value="{{ old('address', $venue->address) }}"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Zona</label><br>
          <input name="zone" value="{{ old('zone', $venue->zone) }}"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label>Latitud</label><br>
            <input name="lat" value="{{ old('lat', $venue->lat) }}"
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>

          <div>
            <label>Longitud</label><br>
            <input name="lng" value="{{ old('lng', $venue->lng) }}"
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>
        </div>

        <div>
          <label>Imagen del complejo</label><br>
          <input type="file" name="cover_image" accept="image/*">
        </div>

        @if($venue->cover_image_path)
          <div>
            <p style="margin:0 0 8px 0;">Actual:</p>
            <img src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
                 style="max-width:420px; border-radius:12px; border:1px solid #eee;">
          </div>
        @endif

        <div style="display:flex; gap:10px; align-items:center;">
          <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
            Guardar
          </button>
          <a href="{{ route('va.dashboard') }}">Volver</a>
        </div>
      </div>
    </form>
  </div>
@endsection