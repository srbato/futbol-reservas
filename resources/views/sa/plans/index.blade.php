@extends('layouts.admin')

@section('title', 'Planes de membresía')
@section('page_title', 'Planes de membresía')
@section('page_subtitle', 'Configurá los precios, descuentos y características de cada plan')

@section('content')

  @if(session('success'))
    <div class="admin-card" style="background:#e8f7ee; border-color:#cfe9d7; color:#157347; margin-bottom:16px;">
      <p style="margin:0; font-weight:700;">{{ session('success') }}</p>
    </div>
  @endif

  @if($errors->any())
    <div class="admin-card" style="background:#f8d7da; border-color:#f1b9c0; color:#842029; margin-bottom:16px;">
      <ul style="margin:0; padding-left:18px;">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div style="display:grid; gap:20px;">
    @foreach($plans as $plan)
      <div class="admin-card" style="{{ $plan->is_featured ? 'border-left:4px solid #111;' : '' }}">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:20px;">
          <div style="display:flex; align-items:center; gap:12px;">
            <h2 style="margin:0; font-size:22px;">{{ $plan->name }}</h2>
            <span style="font-size:12px; color:#888; font-weight:600; text-transform:uppercase; letter-spacing:.05em;">{{ $plan->slug }}</span>
            @if($plan->is_featured)
              <span style="padding:3px 10px; border-radius:999px; background:#111; color:#fff; font-size:11px; font-weight:700;">⭐ Destacado</span>
            @endif
            @if(!$plan->is_active)
              <span style="padding:3px 10px; border-radius:999px; background:#f8d7da; color:#842029; font-size:11px; font-weight:700;">Inactivo</span>
            @endif
          </div>

          <div style="font-size:13px; color:#666;">
            Precio actual mensual:
            <strong style="color:#111; font-size:16px;">
              ARS {{ number_format($plan->monthly_price, 0, ',', '.') }}
            </strong>
            &nbsp;·&nbsp;
            Anual:
            <strong style="color:#111;">
              ARS {{ number_format($plan->annualTotalPrice(), 0, ',', '.') }}
            </strong>
            ({{ $plan->annual_discount_percentage }}% off)
          </div>
        </div>

        <form method="POST" action="{{ route('sa.plans.update', $plan) }}">
          @csrf
          <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; margin-bottom:18px;">

            <div>
              <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Nombre del plan</label>
              <input
                type="text"
                name="name"
                value="{{ old('name', $plan->name) }}"
                required
                style="padding:10px 12px; border:1px solid #ddd; border-radius:10px; width:100%; font-size:14px;"
              >
            </div>

            <div>
              <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Máx. canchas (vacío = ilimitadas)</label>
              <input
                type="number"
                name="max_fields"
                value="{{ old('max_fields', $plan->max_fields) }}"
                min="1"
                placeholder="Ilimitadas"
                style="padding:10px 12px; border:1px solid #ddd; border-radius:10px; width:100%; font-size:14px;"
              >
            </div>

            <div>
              <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Precio mensual (ARS)</label>
              <input
                type="number"
                name="monthly_price"
                value="{{ old('monthly_price', $plan->monthly_price) }}"
                min="1"
                step="0.01"
                required
                style="padding:10px 12px; border:1px solid #ddd; border-radius:10px; width:100%; font-size:14px;"
              >
            </div>

            <div>
              <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Descuento anual (%)</label>
              <input
                type="number"
                name="annual_discount_percentage"
                value="{{ old('annual_discount_percentage', $plan->annual_discount_percentage) }}"
                min="0"
                max="100"
                step="0.01"
                required
                style="padding:10px 12px; border:1px solid #ddd; border-radius:10px; width:100%; font-size:14px;"
              >
              <div style="font-size:11px; color:#888; margin-top:4px;">
                Precio anual resultante: ARS {{ number_format($plan->annualTotalPrice(), 0, ',', '.') }}
              </div>
            </div>

            <div>
              <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Orden de aparición</label>
              <input
                type="number"
                name="sort_order"
                value="{{ old('sort_order', $plan->sort_order) }}"
                min="0"
                required
                style="padding:10px 12px; border:1px solid #ddd; border-radius:10px; width:100%; font-size:14px;"
              >
            </div>

            <div style="display:flex; flex-direction:column; gap:10px; justify-content:flex-end;">
              <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px;">
                <input type="hidden" name="is_active" value="0">
                <input
                  type="checkbox"
                  name="is_active"
                  value="1"
                  {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                  style="width:16px; height:16px;"
                >
                Plan activo (visible en /planes)
              </label>

              <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px;">
                <input type="hidden" name="is_featured" value="0">
                <input
                  type="checkbox"
                  name="is_featured"
                  value="1"
                  {{ old('is_featured', $plan->is_featured) ? 'checked' : '' }}
                  style="width:16px; height:16px;"
                >
                Destacado ("Más popular")
              </label>
            </div>

          </div>

          <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
            <button type="submit" style="padding:10px 20px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer; font-size:14px; font-weight:700;">
              Guardar cambios
            </button>

            <div style="font-size:13px; color:#888;">
              Slug: <code>{{ $plan->slug }}</code> · ID: {{ $plan->id }}
            </div>
          </div>
        </form>
      </div>
    @endforeach
  </div>

  <div class="admin-card" style="margin-top:20px; background:#f7f7f8; border-color:#e8e8e8;">
    <h3 style="margin-top:0; font-size:16px;">ℹ️ Cómo funciona la facturación</h3>
    <div style="color:#555; font-size:14px; line-height:1.7;">
      <p style="margin:0 0 8px 0;">
        <strong>Mensual:</strong> el usuario paga el precio mensual del plan y obtiene 30 días de acceso.
      </p>
      <p style="margin:0 0 8px 0;">
        <strong>Anual:</strong> el usuario paga <code>precio_mensual × 12 × (1 − descuento%)</code> de una sola vez y obtiene 365 días de acceso.
      </p>
      <p style="margin:0;">
        El <strong>slug</strong> del plan (starter, pro, full) no se puede cambiar desde acá ya que identifica el plan en el código.
        Si necesitás agregar un plan nuevo, contactá al equipo técnico.
      </p>
    </div>
  </div>

@endsection
