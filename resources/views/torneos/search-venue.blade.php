@extends('layouts.app')

@section('title', 'Buscar Cancha — ' . $tournament->name)

@push('styles')
<style>
  .sv-wrap {
    max-width: 800px;
    margin: 0 auto;
    padding: 32px 16px 80px;
  }

  .sv-back {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: #a0a0a0;
    text-decoration: none;
    font-weight: 600;
    transition: color .15s;
    margin-bottom: 14px;
  }
  .sv-back:hover { color: #e8e8e8; }

  .sv-title {
    font-size: 26px;
    font-weight: 800;
    color: #e8e8e8;
    letter-spacing: -.02em;
    margin: 0 0 6px;
  }

  .sv-sub {
    font-size: 14px;
    color: #a0a0a0;
    margin: 0 0 28px;
  }

  .sv-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.12);
    overflow: hidden;
    margin-bottom: 20px;
    transition: border-color .2s;
  }
  .sv-card:hover { border-color: rgba(255,255,255,.15); }

  .sv-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255,255,255,.06);
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
  }

  .sv-venue-name {
    font-size: 16px;
    font-weight: 800;
    color: #e8e8e8;
    margin: 0 0 2px;
  }

  .sv-field-name {
    font-size: 14px;
    font-weight: 600;
    color: #a0a0a0;
    margin: 0;
  }

  .sv-price {
    font-size: 18px;
    font-weight: 900;
    color: #059669;
    white-space: nowrap;
  }
  .sv-price-label {
    font-size: 11px;
    color: #a0a0a0;
    font-weight: 600;
  }

  .sv-details {
    padding: 16px 24px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 12px;
  }

  .sv-detail-label {
    font-size: 11px;
    font-weight: 700;
    color: #a0a0a0;
    text-transform: uppercase;
    letter-spacing: .06em;
  }
  .sv-detail-value {
    font-size: 14px;
    font-weight: 600;
    color: #e8e8e8;
    margin-top: 2px;
  }

  .sv-notes {
    padding: 0 24px 16px;
    font-size: 13px;
    color: #a0a0a0;
    line-height: 1.5;
  }

  .sv-form {
    padding: 16px 24px 20px;
    border-top: 1px solid rgba(255,255,255,.06);
    background: #0a0a0a;
  }

  .sv-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 12px;
  }

  .sv-input {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    color: #e8e8e8;
    background: #0a0a0a;
    transition: border-color .15s;
  }
  .sv-input:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.12);
  }

  .sv-input-label {
    font-size: 12px;
    font-weight: 700;
    color: #a0a0a0;
    margin-bottom: 4px;
    display: block;
  }

  .sv-submit {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
    background: var(--color-primary, #22c55e);
    color: #052e16;
    border: none;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .15s, transform .1s;
  }
  .sv-submit:hover { background: var(--color-primary-hover, #16a34a); transform: translateY(-1px); }
  .sv-submit svg { width: 16px; height: 16px; }

  .sv-empty {
    text-align: center;
    padding: 60px 24px;
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
  }
  .sv-empty svg {
    width: 48px;
    height: 48px;
    color: #444;
    margin: 0 auto 12px;
  }
  .sv-empty p {
    font-size: 15px;
    font-weight: 600;
    color: #a0a0a0;
    margin: 0 0 4px;
  }
  .sv-empty span {
    font-size: 13px;
    color: #555;
  }

  @media (max-width: 600px) {
    .sv-title { font-size: 22px; }
    .sv-form-row { grid-template-columns: 1fr; }
    .sv-details { grid-template-columns: 1fr 1fr; }
  }
</style>
@endpush

@section('content')
<div class="sv-wrap">

  <a href="{{ route('torneos.manage', $tournament) }}" class="sv-back">
    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
    Volver a gestionar
  </a>

  <h1 class="sv-title">Buscar Cancha</h1>
  <p class="sv-sub">Canchas disponibles en TuCancha para <strong>{{ $tournament->name }}</strong> ({{ ucfirst($tournament->sport) }})</p>

  @if($fields->isEmpty())
    <div class="sv-empty">
      <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
      </svg>
      <p>No hay canchas disponibles para {{ ucfirst($tournament->sport) }}</p>
      <span>Podes usar la opcion manual: ingresa el nombre y direccion de la cancha al editar el torneo.</span>
    </div>
  @else
    @foreach($fields as $field)
      @php $setting = $field->tournamentSetting; @endphp
      <div class="sv-card">
        <div class="sv-card-header">
          <div>
            <h3 class="sv-venue-name">{{ $field->venue->name }}</h3>
            <p class="sv-field-name">{{ $field->name }} &middot; {{ ucfirst($field->sport) }}</p>
          </div>
          @if($setting && $setting->tournament_price_per_match)
            <div style="text-align: right;">
              <div class="sv-price">${{ number_format($setting->tournament_price_per_match, 0, ',', '.') }}</div>
              <div class="sv-price-label">por partido</div>
            </div>
          @endif
        </div>

        <div class="sv-details">
          @if($setting && $setting->available_days)
            <div>
              <div class="sv-detail-label">Dias disponibles</div>
              <div class="sv-detail-value">{{ $setting->availableDaysDisplay() }}</div>
            </div>
          @endif
          @if($setting && $setting->available_start_time && $setting->available_end_time)
            <div>
              <div class="sv-detail-label">Horario</div>
              <div class="sv-detail-value">{{ $setting->available_start_time }} — {{ $setting->available_end_time }}</div>
            </div>
          @endif
          @if($setting && $setting->contact_method)
            <div>
              <div class="sv-detail-label">Contacto</div>
              <div class="sv-detail-value">
                {{ ucfirst($setting->contact_method) }}{{ $setting->contact_value ? ': ' . $setting->contact_value : '' }}
              </div>
            </div>
          @endif
          @if($field->venue->address)
            <div>
              <div class="sv-detail-label">Direccion</div>
              <div class="sv-detail-value">{{ $field->venue->address }}</div>
            </div>
          @endif
        </div>

        @if($setting && $setting->notes)
          <div class="sv-notes">{{ $setting->notes }}</div>
        @endif

        <div class="sv-form">
          <form method="POST" action="{{ route('torneos.request_venue', $tournament) }}">
            @csrf
            <input type="hidden" name="field_id" value="{{ $field->id }}">

            <div class="sv-form-row">
              <div>
                <label class="sv-input-label">Fechas propuestas</label>
                <input type="text" name="proposed_dates" class="sv-input" placeholder="Ej: Sabados de mayo a las 18hs">
              </div>
              <div>
                <label class="sv-input-label">Mensaje (opcional)</label>
                <input type="text" name="message" class="sv-input" placeholder="Ej: Torneo de 8 equipos, 4 partidos por fecha">
              </div>
            </div>

            <button type="submit" class="sv-submit">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
              Solicitar esta cancha
            </button>
          </form>
        </div>
      </div>
    @endforeach
  @endif

</div>
@endsection
