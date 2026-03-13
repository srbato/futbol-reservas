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

        {{-- Sección Mercado Pago --}}
        <div style="border:1px solid #e0e0e0; border-radius:12px; padding:16px; background:#fafafa;">
          <div style="font-weight:700; margin-bottom:4px;">Mercado Pago</div>
          <div style="font-size:13px; color:#666; margin-bottom:12px;">
            Pegá tu <strong>Access Token de producción</strong> para que los pagos de este complejo lleguen directamente a tu cuenta.
            Lo encontrás en
            <a href="https://www.mercadopago.com.ar/developers/panel/app" target="_blank">mercadopago.com.ar → Desarrolladores → Credenciales de producción</a>.
          </div>

          @if($venue->mp_access_token)
            <div style="margin-bottom:10px; padding:10px 14px; background:#e8f5e9; border-radius:8px; color:#2e7d32; font-size:13px; font-weight:600;">
              ✓ Cuenta de Mercado Pago conectada
            </div>
          @endif

          <div>
            <label style="font-size:13px;">Access Token</label><br>
            <input
              name="mp_access_token"
              type="password"
              value="{{ old('mp_access_token', $venue->mp_access_token ? '••••••••••••••••' : '') }}"
              placeholder="APP_USR-..."
              autocomplete="off"
              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px; font-family:monospace; font-size:13px;">
            <div style="font-size:12px; color:#999; margin-top:4px;">
              Dejá el campo vacío si no querés modificarlo.
            </div>
          </div>
        </div>

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