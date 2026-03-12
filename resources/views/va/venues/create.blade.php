@extends('layouts.admin')

@section('title', 'Crear complejo')
@section('page_title', 'Crear complejo')
@section('page_subtitle', 'Cargá un nuevo complejo para administrarlo desde el panel')

@section('content')
  <div class="admin-card">
    <form method="POST" action="{{ route('va.venues.store') }}" enctype="multipart/form-data">
      @csrf

      <div style="display:grid; gap:14px; max-width:700px;">
        <div>
          <label>Nombre</label><br>
          <input name="name" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Descripción corta</label><br>
          <input name="description" maxlength="255"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Dirección</label><br>
          <input name="address"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Zona</label><br>
          <input name="zone"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label>Latitud</label><br>
            <input name="lat"
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>

          <div>
            <label>Longitud</label><br>
            <input name="lng"
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>
        </div>

        <div>
          <label>Imagen del complejo</label><br>
          <input type="file" name="cover_image" accept="image/*">
        </div>

        <div style="display:flex; gap:10px; align-items:center;">
          <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
            Crear complejo
          </button>
          <a href="{{ route('va.dashboard') }}">Volver</a>
        </div>
      </div>
    </form>
  </div>
@endsection