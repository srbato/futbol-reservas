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

        {{-- Precio nocturno --}}
        @php $hasNight = !is_null($field->price->night_price_per_slot ?? null); @endphp

        <div style="border:1px solid #e0e0e0; border-radius:12px; padding:16px; background:#fafafa;">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-weight:700;">Precio nocturno</div>
              <div style="font-size:13px; color:#666; margin-top:2px;">Aplicá un precio distinto en horario nocturno</div>
            </div>
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
              <input type="checkbox" id="toggle_night_edit" onchange="toggleNight('edit')"
                     {{ $hasNight ? 'checked' : '' }}
                     style="width:18px; height:18px; cursor:pointer;">
              <span style="font-size:13px; font-weight:600;">Activar</span>
            </label>
          </div>

          <div id="night_panel_edit" style="display:{{ $hasNight ? 'block' : 'none' }}; margin-top:14px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
              <div>
                <label style="font-size:13px;">Precio nocturno</label><br>
                <input type="number" name="night_price_per_slot" step="0.01" min="0"
                       value="{{ $field->price->night_price_per_slot ?? '' }}"
                       placeholder="Ej: 15000"
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
              </div>
              <div>
                <label style="font-size:13px;">Desde</label><br>
                <input type="time" name="night_start_time"
                       value="{{ $field->price->night_start_time ? \Carbon\Carbon::parse($field->price->night_start_time)->format('H:i') : '' }}"
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
              </div>
              <div>
                <label style="font-size:13px;">Hasta</label><br>
                <input type="time" name="night_end_time"
                       value="{{ $field->price->night_end_time ? \Carbon\Carbon::parse($field->price->night_end_time)->format('H:i') : '' }}"
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
              </div>
            </div>
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

  <script>
    function toggleNight(suffix) {
      const checkbox = document.getElementById('toggle_night_' + suffix);
      const panel = document.getElementById('night_panel_' + suffix);
      panel.style.display = checkbox.checked ? 'block' : 'none';
    }
  </script>
@endsection