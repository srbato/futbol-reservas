@extends('va.onboarding.layout')

@section('onboarding_content')

<div style="background:#fff; border-radius:18px; border:1px solid #ececec; padding:28px 24px; box-shadow:0 2px 12px rgba(0,0,0,.03);">
    <div style="margin-bottom:24px;">
        <h2 style="margin:0 0 6px; font-size:20px; font-weight:800; color:#111;">Datos de tu complejo</h2>
        <p style="margin:0; font-size:14px; color:#888;">Empecemos con la información básica de tu espacio deportivo.</p>
    </div>

    <form method="POST" action="{{ route('va.onboarding.store_venue') }}" enctype="multipart/form-data">
        @csrf

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Nombre del complejo *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none;"
                   placeholder="Ej: Complejo El Gol">
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Dirección *</label>
            <input type="text" name="address" value="{{ old('address') }}" required
                   style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none;"
                   placeholder="Ej: Av. Rivadavia 1234, CABA">
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Zona / Barrio</label>
            <input type="text" name="zone" value="{{ old('zone') }}"
                   style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none;"
                   placeholder="Ej: Caballito">
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Descripción</label>
            <textarea name="description" rows="3"
                      style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none; resize:none;"
                      placeholder="Breve descripción de tu complejo...">{{ old('description') }}</textarea>
        </div>

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Teléfono de contacto</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                   style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none;"
                   placeholder="Ej: 11-1234-5678">
        </div>

        <div style="margin-bottom:24px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Imagen de portada</label>
            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                   style="width:100%; font-size:13px; color:#666;">
            <p style="margin:4px 0 0; font-size:12px; color:#aaa;">JPG, PNG o WebP. Máximo 2 MB.</p>
        </div>

        <button type="submit"
                style="display:inline-block; padding:12px 32px; border-radius:12px; background:#111; color:#fff; font-size:14px; font-weight:700; border:none; cursor:pointer; font-family:inherit;">
            Siguiente paso →
        </button>
    </form>
</div>

@endsection
