@extends('layouts.app')

@section('title', 'Convertir reserva en Falta Uno')

@push('styles')
<style>
  .fu-wrap {
    max-width: 600px;
    margin: 0 auto;
  }

  .fu-hero {
    position: relative;
    height: 180px;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #111 0%, #333 100%);
  }

  .fu-hero img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: .55;
  }

  .fu-hero-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 20px 22px;
    background: linear-gradient(to top, rgba(0,0,0,.7) 0%, transparent 60%);
  }

  .fu-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.15);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 999px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 8px;
    width: fit-content;
  }

  .fu-hero-title {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    margin: 0;
    letter-spacing: -.02em;
    text-shadow: 0 1px 4px rgba(0,0,0,.4);
  }

  .fu-hero-sub {
    font-size: 13px;
    color: rgba(255,255,255,.7);
    margin: 2px 0 0 0;
  }

  .fu-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.15);
    overflow: hidden;
  }

  .fu-card-header {
    padding: 20px 24px 0;
    border-bottom: 1px solid rgba(255,255,255,.06);
    padding-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .fu-step-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #111;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .fu-card-header-text h2 {
    font-size: 16px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.01em;
  }

  .fu-card-header-text p {
    font-size: 13px;
    color: #666;
    margin: 1px 0 0 0;
  }

  .fu-card-body {
    padding: 22px 24px;
    display: grid;
    gap: 18px;
  }

  .fu-input-group {
    display: grid;
    gap: 6px;
  }

  .fu-label {
    font-size: 13px;
    font-weight: 700;
    color: #e8e8e8;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .fu-label-icon {
    font-size: 15px;
  }

  .fu-hint {
    font-size: 12px;
    color: #666;
    margin: 0;
    line-height: 1.5;
  }

  .fu-counter {
    display: flex;
    align-items: center;
    gap: 0;
    width: fit-content;
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 12px;
    overflow: hidden;
    background: #0a0a0a;
  }

  .fu-counter button {
    width: 40px;
    height: 42px;
    border: none;
    background: rgba(255,255,255,.04);
    color: #e8e8e8;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
    flex-shrink: 0;
  }

  .fu-counter button:hover { background: rgba(255,255,255,.08); }
  .fu-counter button:active { background: rgba(255,255,255,.12); }

  .fu-counter input {
    width: 60px;
    height: 42px;
    border: none;
    border-left: 1.5px solid rgba(255,255,255,.1);
    border-right: 1.5px solid rgba(255,255,255,.1);
    text-align: center;
    font-size: 16px;
    font-weight: 800;
    color: #e8e8e8;
    outline: none;
    background: #0a0a0a;
    -moz-appearance: textfield;
  }

  .fu-counter input::-webkit-outer-spin-button,
  .fu-counter input::-webkit-inner-spin-button { -webkit-appearance: none; }

  .fu-preview {
    background: linear-gradient(135deg, rgba(255,255,255,.02) 0%, rgba(255,255,255,.04) 100%);
    border: 1.5px solid rgba(255,255,255,.08);
    border-radius: 16px;
    padding: 18px 20px;
    display: none;
    animation: fadeSlide .25s ease;
  }

  @keyframes fadeSlide {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .fu-preview.visible { display: block; }

  .fu-preview-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #666;
    margin: 0 0 12px 0;
  }

  .fu-preview-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 14px;
  }

  .fu-preview-stat {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 12px;
    padding: 12px 14px;
  }

  .fu-preview-stat-label {
    font-size: 11px;
    color: #666;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .fu-preview-stat-value {
    font-size: 17px;
    font-weight: 800;
    color: #e8e8e8;
    letter-spacing: -.01em;
  }

  .fu-preview-total {
    background: #111;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .fu-preview-total-label {
    font-size: 13px;
    color: rgba(255,255,255,.7);
    font-weight: 600;
  }

  .fu-preview-total-value {
    font-size: 20px;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
  }

  .fu-preview-footer {
    font-size: 12px;
    color: #666;
    margin: 10px 0 0 0;
    line-height: 1.5;
  }

  .fu-divider {
    height: 1px;
    background: rgba(255,255,255,.06);
    margin: 0 -24px;
  }

  .fu-card-footer {
    padding: 18px 24px;
    border-top: 1px solid rgba(255,255,255,.06);
  }

  .fu-submit {
    width: 100%;
    padding: 14px;
    font-size: 15px;
    font-weight: 700;
    background: #22c55e;
    color: #050505;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: background .15s, transform .1s;
    letter-spacing: -.01em;
  }

  .fu-submit:hover  { background: #16a34a; }
  .fu-submit:active { transform: scale(.98); }

  .fu-error {
    background: rgba(229,57,53,.1);
    border: 1px solid rgba(229,57,53,.2);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    color: #f87171;
  }

  .fu-reservation-info {
    background: rgba(34,197,94,.1);
    border: 1.5px solid rgba(34,197,94,.2);
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 20px;
  }

  .fu-reservation-info-title {
    font-size: 13px;
    font-weight: 800;
    color: #6ee7a0;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .fu-reservation-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }

  .fu-reservation-info-item {
    font-size: 13px;
    color: #6ee7a0;
    line-height: 1.5;
  }

  .fu-reservation-info-item strong {
    display: block;
    font-size: 11px;
    color: #22c55e;
    text-transform: uppercase;
    letter-spacing: .05em;
    font-weight: 700;
    margin-bottom: 2px;
  }

  @media (max-width: 600px) {
    .fu-card-body { padding: 18px 18px; }
    .fu-card-header { padding: 16px 18px 14px; }
    .fu-card-footer { padding: 14px 18px; }
    .fu-preview-grid { grid-template-columns: 1fr; }
    .fu-reservation-info-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')

<div class="fu-wrap">

  {{-- Back --}}
  <div style="margin-bottom:14px;">
    <a href="{{ route('reservations.show', $reservation) }}"
       style="display:inline-flex; align-items:center; gap:5px; font-size:13px; color:#666; text-decoration:none; font-weight:600; transition:color .15s;"
       onmouseover="this.style.color='#e8e8e8'" onmouseout="this.style.color='#666'">
      <i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;"></i> Volver a la reserva
    </a>
  </div>

  {{-- Hero --}}
  <div class="fu-hero">
    @if($field->cover_image_path)
      <img src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}" alt="{{ $field->name }}">
    @endif
    <div class="fu-hero-overlay">
      <div class="fu-hero-badge" style="display:inline-flex;align-items:center;gap:6px;">
        <i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;"></i> Convertir en Falta Uno
      </div>
      <h1 class="fu-hero-title">{{ $field->name }}</h1>
      <p class="fu-hero-sub">
        {{ $field->venue->name }} ·
        {{ match($field->sport ?? '') { 'football'=>'Futbol','padel'=>'Padel','tennis'=>'Tenis','basketball'=>'Basquet','volleyball'=>'Voley', default=>ucfirst($field->sport ?? '') } }}
      </p>
    </div>
  </div>

  {{-- Reservation info --}}
  <div class="fu-reservation-info">
    <div class="fu-reservation-info-title">
      <i data-lucide="check-circle" style="width:16px;height:16px;stroke:currentColor;"></i>
      Reserva #{{ $reservation->id }} - Pagada
    </div>
    <div class="fu-reservation-info-grid">
      <div class="fu-reservation-info-item">
        <strong>Fecha</strong>
        {{ $reservation->start_at->format('d/m/Y') }}
      </div>
      <div class="fu-reservation-info-item">
        <strong>Horario</strong>
        {{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}
      </div>
      <div class="fu-reservation-info-item">
        <strong>Cancha</strong>
        {{ $field->name }}
      </div>
      <div class="fu-reservation-info-item">
        <strong>Total pagado</strong>
        {{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}
      </div>
    </div>
  </div>

  {{-- Errores --}}
  @if($errors->any())
    <div class="fu-error" style="margin-bottom:14px;">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  @if(session('error'))
    <div class="fu-error" style="margin-bottom:14px;">{{ session('error') }}</div>
  @endif

  {{-- Card del formulario --}}
  <form method="POST" action="{{ route('reservations.convert-falta-uno.store', $reservation) }}">
    @csrf
    <div class="fu-card">

      {{-- Header --}}
      <div class="fu-card-header">
        <div class="fu-step-icon"><i data-lucide="users" style="width:28px;height:28px;stroke:#22c55e;stroke-width:2;"></i></div>
        <div class="fu-card-header-text">
          <h2>Configura tu partido</h2>
          <p>Tu reserva ya esta pagada, solo indica cuantos jugadores necesitas</p>
        </div>
      </div>

      <div class="fu-card-body">

        {{-- Total jugadores --}}
        <div class="fu-input-group">
          <label class="fu-label" for="total_players">
            <span class="fu-label-icon"><i data-lucide="users" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;"></i></span> Total de jugadores del partido
          </label>
          <div class="fu-counter">
            <button type="button" onclick="step('total_players', -1)">-</button>
            <input type="number" id="total_players" name="total_players"
                   min="2" max="100" required
                   value="{{ old('total_players', ($field->format ?? 5) * 2) }}"
                   oninput="recalculate()">
            <button type="button" onclick="step('total_players', 1)">+</button>
          </div>
          <p class="fu-hint">Ej: 10 para un partido de 5 vs 5, 14 para 7 vs 7.</p>
        </div>

        {{-- Jugadores del iniciador --}}
        <div class="fu-input-group">
          <label class="fu-label" for="initiator_players">
            <span class="fu-label-icon"><i data-lucide="user-plus" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;"></i></span> Jugadores que traes vos
          </label>
          <div class="fu-counter">
            <button type="button" onclick="step('initiator_players', -1)">-</button>
            <input type="number" id="initiator_players" name="initiator_players"
                   min="1" max="99" required
                   value="{{ old('initiator_players', 1) }}"
                   oninput="recalculate()">
            <button type="button" onclick="step('initiator_players', 1)">+</button>
          </div>
          <p class="fu-hint">Los demas se unen desde la plataforma y pagan en el complejo.</p>
        </div>

        <div class="fu-divider"></div>

        {{-- Filtro de genero --}}
        <div class="fu-input-group">
          <label class="fu-label">
            <span class="fu-label-icon"><i data-lucide="user" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;"></i></span> Genero de los jugadores
          </label>
          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            @foreach(['mixed'=>'Mixto', 'male'=>'Solo masculino', 'female'=>'Solo femenino'] as $val => $gLabel)
              <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; font-weight:600; color:#a0a0a0;">
                <input type="radio" name="gender_filter" value="{{ $val }}"
                       {{ old('gender_filter', 'mixed') === $val ? 'checked' : '' }}>
                {{ $gLabel }}
              </label>
            @endforeach
          </div>
          <p class="fu-hint">Solo jugadores con el genero seleccionado podran unirse.</p>
        </div>

        <div class="fu-divider"></div>

        {{-- Filtro de categoria (rango) --}}
        @php $sportCategories = \App\Models\FaltaUnoSportProfile::getCategoriesForSport($field->sport); @endphp
        <div class="fu-input-group">
          <label class="fu-label">
            <span class="fu-label-icon"><i data-lucide="award" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;"></i></span> Rango de categorias aceptadas
          </label>
          <div style="display:flex; gap:10px; align-items:center;">
            <div style="flex:1;">
              <div style="font-size:11px; color:#666; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.05em;">Desde</div>
              <select id="category_min" name="category_min" class="form-control" style="width:100%; font-size:14px;" onchange="syncCategoryMax()">
                <option value="">Cualquiera</option>
                @foreach($sportCategories as $cat)
                  <option value="{{ $cat }}" {{ old('category_min') === $cat ? 'selected' : '' }}>
                    {{ ucfirst($cat) }}
                  </option>
                @endforeach
              </select>
            </div>
            <div style="color:#444; padding-top:18px; display:flex; align-items:center;"><i data-lucide="arrow-right" style="width:18px;height:18px;stroke:#444;"></i></div>
            <div style="flex:1;">
              <div style="font-size:11px; color:#666; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.05em;">Hasta</div>
              <select id="category_max" name="category_max" class="form-control" style="width:100%; font-size:14px;" onchange="syncCategoryMin()">
                <option value="">Cualquiera</option>
                @foreach($sportCategories as $cat)
                  <option value="{{ $cat }}" {{ old('category_max') === $cat ? 'selected' : '' }}>
                    {{ ucfirst($cat) }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <p class="fu-hint">Deja ambos en "Cualquiera" para aceptar jugadores de todo nivel.</p>
        </div>

        <div class="fu-divider"></div>

        {{-- Filtro de grupo de edad (rango) --}}
        @php
          $ageGroups = [
            'sub10' => 'Sub 10', 'sub12' => 'Sub 12', 'sub14' => 'Sub 14',
            'sub16' => 'Sub 16', 'sub18' => 'Sub 18', '19a25' => '19 a 25',
            '26a34' => '26 a 34', 'open'  => 'Open',   'mas35' => '+35',
            'mas40' => '+40',     'mas45' => '+45',     'mas50' => '+50',
            'mas55' => '+55',     'mas60' => '+60',
          ];
        @endphp
        <div class="fu-input-group">
          <label class="fu-label">
            <span class="fu-label-icon"><i data-lucide="calendar-range" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;"></i></span> Rango de edad aceptado
          </label>
          <div style="display:flex; gap:10px; align-items:center;">
            <div style="flex:1;">
              <div style="font-size:11px; color:#666; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.05em;">Desde</div>
              <select id="age_group_min" name="age_group_min" class="form-control" style="width:100%; font-size:14px;" onchange="syncAgeMax()">
                <option value="">Cualquiera</option>
                @foreach($ageGroups as $val => $label)
                  <option value="{{ $val }}" {{ old('age_group_min') === $val ? 'selected' : '' }}>
                    {{ $label }}
                  </option>
                @endforeach
              </select>
            </div>
            <div style="color:#444; padding-top:18px; display:flex; align-items:center;"><i data-lucide="arrow-right" style="width:18px;height:18px;stroke:#444;"></i></div>
            <div style="flex:1;">
              <div style="font-size:11px; color:#666; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.05em;">Hasta</div>
              <select id="age_group_max" name="age_group_max" class="form-control" style="width:100%; font-size:14px;" onchange="syncAgeMin()">
                <option value="">Cualquiera</option>
                @foreach($ageGroups as $val => $label)
                  <option value="{{ $val }}" {{ old('age_group_max') === $val ? 'selected' : '' }}>
                    {{ $label }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <p class="fu-hint">Deja ambos en "Cualquiera" para aceptar jugadores de cualquier edad.</p>
        </div>

        <div class="fu-divider"></div>

        {{-- Preview --}}
        <div class="fu-preview" id="fuPreview">
          <p class="fu-preview-title">Resumen del partido</p>

          <div class="fu-preview-grid">
            <div class="fu-preview-stat">
              <div class="fu-preview-stat-label">Jugadores confirmados</div>
              <div class="fu-preview-stat-value" id="prevInitiator">--</div>
            </div>
            <div class="fu-preview-stat">
              <div class="fu-preview-stat-label">Faltan unirse</div>
              <div class="fu-preview-stat-value" id="prevNeeded" style="color:#f59e0b;">--</div>
            </div>
            <div class="fu-preview-stat">
              <div class="fu-preview-stat-label">Precio por persona</div>
              <div class="fu-preview-stat-value" id="prevPerPerson">--</div>
            </div>
            <div class="fu-preview-stat">
              <div class="fu-preview-stat-label">Total jugadores</div>
              <div class="fu-preview-stat-value" id="prevTotal2">--</div>
            </div>
          </div>

          <div class="fu-preview-total">
            <span class="fu-preview-total-label">Ya pagaste por esta reserva</span>
            <span class="fu-preview-total-value" id="prevTotal">--</span>
          </div>

          <p class="fu-preview-footer">
            Los <strong id="prevNeedTxt">?</strong> jugadores restantes abonan su parte directamente en el complejo el dia del partido.
          </p>
        </div>

      </div>

      {{-- Info panel --}}
      @php
        $setting = $field->faltaUnoSetting;
        $refundMin = $setting->refund_deadline_minutes ?? 60;
        $lateMin   = $setting->late_leave_deadline_minutes ?? 240;
        $fillMin   = $setting->fill_deadline_minutes ?? 120;

        $fmt = function($min) {
          if ($min >= 1440) {
            $d = floor($min / 1440);
            $h = floor(($min % 1440) / 60);
            return $d . ' dia' . ($d > 1 ? 's' : '') . ($h > 0 ? ' y ' . $h . 'h' : '');
          }
          if ($min >= 60) {
            $h = floor($min / 60);
            $m = $min % 60;
            return $h . 'h' . ($m > 0 ? ' ' . $m . 'min' : '');
          }
          return $min . ' minutos';
        };
      @endphp
      <div style="background:rgba(59,130,246,.08); border:1.5px solid rgba(59,130,246,.15); border-radius:14px; padding:18px 20px; margin:0 20px 20px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
          <i data-lucide="info" style="width:18px;height:18px;stroke:#93c5fd;stroke-width:2;flex-shrink:0;"></i>
          <span style="font-size:14px; font-weight:800; color:#e8e8e8;">Informacion importante</span>
        </div>
        <div style="display:grid; gap:10px; font-size:13px; color:#a0a0a0; line-height:1.5;">

          <div style="display:flex; gap:8px; align-items:flex-start;">
            <i data-lucide="check-circle" style="width:15px;height:15px;stroke:#22c55e;stroke-width:2;flex-shrink:0;margin-top:2px;"></i>
            <div>
              <strong style="color:#6ee7a0;">No necesitas pagar de nuevo</strong><br>
              Tu reserva ya esta pagada. Solo se publicara el partido para que otros jugadores se unan.
            </div>
          </div>

          <div style="display:flex; gap:8px; align-items:flex-start;">
            <i data-lucide="clock" style="width:15px;height:15px;stroke:#22c55e;stroke-width:2;flex-shrink:0;margin-top:2px;"></i>
            <div>
              <strong style="color:#6ee7a0;">Cancelacion con reembolso</strong><br>
              Podes cancelar el partido hasta <strong>{{ $fmt($refundMin) }} antes</strong> del inicio.
            </div>
          </div>

          <div style="display:flex; gap:8px; align-items:flex-start;">
            <i data-lucide="users" style="width:15px;height:15px;stroke:#3b82f6;stroke-width:2;flex-shrink:0;margin-top:2px;"></i>
            <div>
              <strong style="color:#93c5fd;">Si no se completa</strong><br>
              Si el partido no se llena {{ $fmt($fillMin) }} antes del inicio, se cancela automaticamente.
            </div>
          </div>

          <div style="display:flex; gap:8px; align-items:flex-start;">
            <i data-lucide="wallet" style="width:15px;height:15px;stroke:#6b7280;stroke-width:2;flex-shrink:0;margin-top:2px;"></i>
            <div>
              <strong style="color:#a0a0a0;">Pago de los demas jugadores</strong><br>
              Los jugadores que se unan abonan su parte directamente en el complejo el dia del partido.
            </div>
          </div>

        </div>
      </div>

      {{-- Footer con boton --}}
      <div class="fu-card-footer">
        <button type="submit" class="fu-submit" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;">
          Publicar partido de Falta Uno <i data-lucide="arrow-right" style="width:16px;height:16px;stroke:currentColor;"></i>
        </button>
        <p style="text-align:center; font-size:12px; color:#666; margin:10px 0 0 0;">
          Tu reserva se mantiene igual. Solo se crea el partido publico para que se unan jugadores.
        </p>
      </div>

    </div>
  </form>

</div>

<script>
  const CURRENCY    = '{{ $reservation->currency ?? 'ARS' }}';
  const TOTAL_PAID  = {{ (float) $reservation->total_amount }};

  function fmt(n) {
    const rounded = Math.round(n * 100) / 100;
    return CURRENCY + '\u00a0' + rounded.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
  }

  function step(id, delta) {
    const input = document.getElementById(id);
    const val   = parseInt(input.value) || 0;
    const min   = parseInt(input.min) || 1;
    const max   = parseInt(input.max) || 999;
    input.value = Math.min(max, Math.max(min, val + delta));
    recalculate();
  }

  function recalculate() {
    const total     = parseInt(document.getElementById('total_players').value) || 0;
    const initiator = parseInt(document.getElementById('initiator_players').value) || 0;
    const preview   = document.getElementById('fuPreview');

    if (total < 2 || initiator < 1 || initiator >= total) {
      preview.classList.remove('visible');
      return;
    }

    const needed    = total - initiator;
    const perPerson = TOTAL_PAID / total;

    document.getElementById('prevInitiator').textContent = initiator;
    document.getElementById('prevNeeded').textContent    = needed;
    document.getElementById('prevPerPerson').textContent = fmt(perPerson);
    document.getElementById('prevTotal2').textContent    = total;
    document.getElementById('prevTotal').textContent     = fmt(TOTAL_PAID);
    document.getElementById('prevNeedTxt').textContent   = needed;

    preview.classList.add('visible');
  }

  recalculate();

  // Sincroniza selects de categoria
  const CATEGORY_ORDER = @json($sportCategories);

  function catIndex(val) {
    return val === '' ? -1 : CATEGORY_ORDER.indexOf(val);
  }

  function syncCategoryMax() {
    const minSel = document.getElementById('category_min');
    const maxSel = document.getElementById('category_max');
    const minIdx = catIndex(minSel.value);
    if (minIdx === -1) return;
    const maxIdx = catIndex(maxSel.value);
    if (maxIdx !== -1 && maxIdx < minIdx) {
      maxSel.value = minSel.value;
    }
  }

  function syncCategoryMin() {
    const minSel = document.getElementById('category_min');
    const maxSel = document.getElementById('category_max');
    const maxIdx = catIndex(maxSel.value);
    if (maxIdx === -1) return;
    const minIdx = catIndex(minSel.value);
    if (minIdx !== -1 && minIdx > maxIdx) {
      minSel.value = maxSel.value;
    }
  }

  // Sincroniza selects de grupo de edad
  const AGE_ORDER = @json(array_keys($ageGroups));

  function ageIndex(val) {
    return val === '' ? -1 : AGE_ORDER.indexOf(val);
  }

  function syncAgeMax() {
    const minSel = document.getElementById('age_group_min');
    const maxSel = document.getElementById('age_group_max');
    const minIdx = ageIndex(minSel.value);
    if (minIdx === -1) return;
    const maxIdx = ageIndex(maxSel.value);
    if (maxIdx !== -1 && maxIdx < minIdx) {
      maxSel.value = minSel.value;
    }
  }

  function syncAgeMin() {
    const minSel = document.getElementById('age_group_min');
    const maxSel = document.getElementById('age_group_max');
    const maxIdx = ageIndex(maxSel.value);
    if (maxIdx === -1) return;
    const minIdx = ageIndex(minSel.value);
    if (minIdx !== -1 && minIdx > maxIdx) {
      minSel.value = maxSel.value;
    }
  }
</script>

@endsection
