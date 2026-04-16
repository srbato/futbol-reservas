@extends('layouts.app')

@section('title', 'Pagar inscripcion — ' . $tournament->name)

@push('styles')
<style>
  .tc-wrap {
    max-width: 560px;
    margin: 0 auto;
    padding: 32px 16px 64px;
  }

  .tc-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #a0a0a0;
    text-decoration: none;
    margin-bottom: 20px;
    transition: color .15s;
  }
  .tc-back:hover { color: #22c55e; }
  .tc-back svg { width: 16px; height: 16px; }

  .tc-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.2);
    padding: 28px;
    margin-bottom: 20px;
  }

  .tc-section-title {
    font-size: 15px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin: 0 0 14px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .tc-section-title svg { width: 16px; height: 16px; }

  .tc-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
  }
  .tc-info-row + .tc-info-row {
    border-top: 1px solid rgba(255,255,255,.06);
  }
  .tc-info-label {
    font-size: 14px;
    color: #a0a0a0;
  }
  .tc-info-value {
    font-size: 14px;
    font-weight: 600;
    color: #e8e8e8;
  }

  .tc-price-wrap {
    text-align: center;
    padding: 24px 0;
  }
  .tc-price-label {
    font-size: 13px;
    font-weight: 600;
    color: #666;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 8px;
  }
  .tc-price {
    font-size: 42px;
    font-weight: 900;
    color: #e8e8e8;
    letter-spacing: -.02em;
    line-height: 1;
  }
  .tc-price-currency {
    font-size: 24px;
    font-weight: 700;
    color: #a0a0a0;
    vertical-align: super;
  }

  .tc-pay-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 16px 24px;
    background: #22c55e;
    color: #052e16;
    border: none;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .15s, transform .1s, box-shadow .15s;
    box-shadow: 0 4px 14px rgba(34,197,94,.3);
  }
  .tc-pay-btn:hover {
    background: #16a34a;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0,0,0,.2);
  }
  .tc-pay-btn:active { transform: translateY(0); }
  .tc-pay-btn svg { width: 20px; height: 20px; }

  .tc-pay-btn:disabled {
    opacity: .5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
  }

  .tc-info-text {
    text-align: center;
    font-size: 13px;
    color: #666;
    margin-top: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }
  .tc-info-text svg { width: 14px; height: 14px; flex-shrink: 0; }

  .tc-alert {
    padding: 14px 20px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .tc-alert svg { width: 18px; height: 18px; flex-shrink: 0; }
  .tc-alert-success {
    background: rgba(34,197,94,.08);
    color: #22c55e;
    border: 1px solid rgba(34,197,94,.25);
  }
  .tc-alert-error {
    background: rgba(239,68,68,.08);
    color: #f87171;
    border: 1px solid rgba(239,68,68,.25);
  }
  .tc-alert-info {
    background: rgba(59,130,246,.08);
    color: #60a5fa;
    border: 1px solid rgba(59,130,246,.25);
  }

  .tc-mp-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
    font-size: 12px;
    color: #666;
  }
  .tc-mp-badge svg { width: 14px; height: 14px; }

  .tc-no-mp {
    text-align: center;
    padding: 20px;
    color: #666;
    font-size: 14px;
  }

  @media (max-width: 640px) {
    .tc-card { padding: 24px 20px; }
    .tc-price { font-size: 36px; }
  }
</style>
@endpush

@section('content')
<div class="tc-wrap">

  <a href="{{ route('torneos.show', $tournament) }}" class="tc-back">
    <i data-lucide="arrow-left"></i>
    Volver al torneo
  </a>

  @if(session('error'))
    <div class="tc-alert tc-alert-error">
      <i data-lucide="alert-triangle"></i>
      {{ session('error') }}
    </div>
  @endif
  @if(session('success'))
    <div class="tc-alert tc-alert-success">
      <i data-lucide="check-circle"></i>
      {{ session('success') }}
    </div>
  @endif
  @if(session('info'))
    <div class="tc-alert tc-alert-info">
      <i data-lucide="info"></i>
      {{ session('info') }}
    </div>
  @endif

  {{-- Tournament info --}}
  <div class="tc-card">
    <div class="tc-section-title">
      <i data-lucide="trophy"></i>
      Torneo
    </div>
    <div class="tc-info-row">
      <span class="tc-info-label">Nombre</span>
      <span class="tc-info-value">{{ $tournament->name }}</span>
    </div>
    <div class="tc-info-row">
      <span class="tc-info-label">Deporte</span>
      <span class="tc-info-value">
        @php
          $sportLabels = ['football' => 'Futbol', 'padel' => 'Padel', 'tennis' => 'Tenis', 'basketball' => 'Basquet', 'volleyball' => 'Voley'];
        @endphp
        {{ $sportLabels[$tournament->sport] ?? ucfirst($tournament->sport) }}
      </span>
    </div>
    <div class="tc-info-row">
      <span class="tc-info-label">Organizador</span>
      <span class="tc-info-value">{{ $tournament->organizer->name }}</span>
    </div>
  </div>

  {{-- Price + Pay --}}
  <div class="tc-card">
    <div class="tc-price-wrap">
      <div class="tc-price-label">Precio de inscripcion</div>
      <div class="tc-price">
        <span class="tc-price-currency">$</span>{{ number_format($tournament->inscription_price, 0, ',', '.') }}
      </div>
    </div>

    @if($organizerHasMp)
      <form action="{{ route('torneos.teams.pay', $tournament) }}" method="POST">
        @csrf
        <button type="submit" class="tc-pay-btn">
          <i data-lucide="credit-card"></i>
          Pagar con MercadoPago
        </button>
      </form>

      <div class="tc-info-text">
        <i data-lucide="lock"></i>
        Seras redirigido a MercadoPago para completar el pago.
      </div>

      <div class="tc-mp-badge">
        <i data-lucide="shield-check"></i>
        Pago seguro con MercadoPago
      </div>
    @else
      <div class="tc-no-mp">
        <i data-lucide="alert-circle"></i>
        <p>El organizador aun no conecto su cuenta de MercadoPago.</p>
        <p>Contactalo para coordinar el pago de la inscripcion.</p>
      </div>
    @endif
  </div>

</div>
@endsection
