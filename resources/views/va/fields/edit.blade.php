@extends('layouts.admin')

@section('title', 'Editar cancha')
@section('page_title', 'Editar cancha')
@section('page_subtitle', $field->venue->name . ' · ' . $field->name)

@section('content')
  <div class="admin-card">
    <form method="POST" action="{{ route('va.fields.update', $field) }}" enctype="multipart/form-data">
      @csrf

      <div style="display:grid; gap:14px; max-width:700px;">
        <div>
          <label>Foto principal</label><br>
          <input type="file" name="cover_image" accept="image/*">
        </div>

        @if($field->cover_image_path)
          <div>
            <p style="margin:0 0 8px 0;">Actual:</p>
            <img src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
                 style="max-width:320px; border-radius:10px;">
          </div>
        @endif

        <div>
          <label>Nombre</label><br>
          <input name="name" value="{{ $field->name }}" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Deporte</label><br>
          <input name="sport" value="{{ $field->sport }}" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label>Formato (5/7/11)</label><br>
            <input type="number" name="format" value="{{ $field->format }}" min="3" max="11" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>

          <div>
            <label>Turno (min)</label><br>
            <input type="number" name="slot_minutes" value="{{ $field->slot_minutes }}" min="30" max="180" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label>Precio por turno</label><br>
            <input type="number" name="price_per_slot" step="0.01"
                   value="{{ $field->price->price_per_slot ?? 0 }}" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>

          <div>
            <label>Moneda</label><br>
            <input name="currency" value="{{ $field->price->currency ?? 'ARS' }}" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>
        </div>

        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
          <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
            Guardar cambios
          </button>

          <a href="{{ route('va.schedule.edit', $field) }}">Editar horarios</a>
          <a href="{{ route('va.dashboard') }}">Volver</a>
        </div>
      </div>
    </form>
  </div>
@endsection