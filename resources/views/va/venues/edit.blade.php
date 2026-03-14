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

  {{-- Sección Mercado Pago (fuera del form principal, acciones propias) --}}
  <div class="admin-card" style="margin-top:18px; max-width:700px;">
    <div style="font-weight:700; font-size:16px; margin-bottom:4px;">Mercado Pago</div>
    <div style="font-size:13px; color:#666; margin-bottom:16px;">
      Conectá tu cuenta de Mercado Pago para que los cobros de este complejo lleguen directamente a tu billetera.
    </div>

    @if($venue->mp_access_token)
      <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
        <div style="padding:10px 16px; background:#e8f5e9; border:1px solid #c8e6c9; border-radius:10px; color:#2e7d32; font-size:14px; font-weight:600;">
          ✓ Cuenta conectada
        </div>

        <form method="POST" action="{{ route('va.mp_oauth.disconnect', $venue) }}"
              onsubmit="return confirm('¿Desconectar la cuenta de Mercado Pago de este complejo?')">
          @csrf
          <button type="submit"
                  style="padding:10px 16px; border:1px solid #ddd; background:#fff; border-radius:10px; cursor:pointer; font-size:14px; color:#666;">
            Desconectar
          </button>
        </form>
      </div>
    @else
      <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
        <div style="padding:10px 16px; background:#fff3cd; border:1px solid #ffeaa7; border-radius:10px; color:#856404; font-size:14px;">
          Sin cuenta conectada — los pagos irán a la cuenta general de TuCancha.
        </div>

        <a href="{{ route('va.mp_oauth.redirect', $venue) }}"
           style="padding:10px 16px; background:#009ee3; color:#fff; border-radius:10px; text-decoration:none; font-size:14px; font-weight:600;">
          Conectar Mercado Pago
        </a>
      </div>
    @endif
  </div>
@endsection