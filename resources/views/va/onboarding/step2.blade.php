@extends('va.onboarding.layout')

@section('onboarding_content')

<div style="background:#fff; border-radius:18px; border:1px solid #ececec; padding:28px 24px; box-shadow:0 2px 12px rgba(0,0,0,.03);">
    <div style="margin-bottom:24px;">
        <h2 style="margin:0 0 6px; font-size:20px; font-weight:800; color:#111;">Tu primera cancha</h2>
        <p style="margin:0; font-size:14px; color:#888;">Configurá los datos básicos de una cancha. Después podés agregar más desde el panel.</p>
    </div>

    <form method="POST" action="{{ route('va.onboarding.store_field') }}">
        @csrf

        <div style="margin-bottom:18px;">
            <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Nombre de la cancha *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none;"
                   placeholder="Ej: Cancha 1">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px;">
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Deporte *</label>
                <select name="sport" required
                        style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none; background:#fff;">
                    <option value="">Seleccionar...</option>
                    <option value="football" {{ old('sport') === 'football' ? 'selected' : '' }}>Fútbol</option>
                    <option value="padel" {{ old('sport') === 'padel' ? 'selected' : '' }}>Pádel</option>
                    <option value="tennis" {{ old('sport') === 'tennis' ? 'selected' : '' }}>Tenis</option>
                    <option value="basketball" {{ old('sport') === 'basketball' ? 'selected' : '' }}>Básquet</option>
                    <option value="volleyball" {{ old('sport') === 'volleyball' ? 'selected' : '' }}>Vóley</option>
                </select>
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Formato *</label>
                <select name="format" required
                        style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none; background:#fff;">
                    <option value="">Seleccionar...</option>
                    @for($i = 1; $i <= 11; $i++)
                        <option value="{{ $i }}" {{ (int) old('format') === $i ? 'selected' : '' }}>{{ $i }} vs {{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px;">
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Superficie *</label>
                <select name="surface" required
                        style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none; background:#fff;">
                    <option value="">Seleccionar...</option>
                    <option value="sintetico" {{ old('surface') === 'sintetico' ? 'selected' : '' }}>Césped sintético</option>
                    <option value="natural" {{ old('surface') === 'natural' ? 'selected' : '' }}>Césped natural</option>
                    <option value="cemento" {{ old('surface') === 'cemento' ? 'selected' : '' }}>Cemento</option>
                    <option value="otro" {{ old('surface') === 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            <div style="display:flex; align-items:flex-end; padding-bottom:4px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_indoor" value="1" {{ old('is_indoor') ? 'checked' : '' }}
                           style="width:18px; height:18px; accent-color:#22c55e;">
                    <span style="font-size:14px; font-weight:600; color:#333;">Techada</span>
                </label>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:24px;">
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Precio por turno *</label>
                <input type="number" name="price_per_slot" value="{{ old('price_per_slot') }}" required step="0.01" min="0"
                       style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none;"
                       placeholder="Ej: 25000">
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Moneda *</label>
                <select name="currency" required
                        style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none; background:#fff;">
                    <option value="ARS" {{ old('currency', 'ARS') === 'ARS' ? 'selected' : '' }}>ARS</option>
                    <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>USD</option>
                </select>
            </div>
        </div>

        <button type="submit"
                style="display:inline-block; padding:12px 32px; border-radius:12px; background:#111; color:#fff; font-size:14px; font-weight:700; border:none; cursor:pointer; font-family:inherit;">
            Siguiente paso →
        </button>
    </form>
</div>

@endsection
