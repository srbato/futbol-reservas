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
    color: #6b7280;
    text-decoration: none;
    margin-bottom: 20px;
    transition: color .15s;
  }
  .tc-back:hover { color: #22c55e; }
  .tc-back svg { width: 16px; height: 16px; }

  .tc-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
    padding: 28px;
    margin-bottom: 20px;
  }

  .tc-section-title {
    font-size: 15px;
    font-weight: 700;
    color: #9ca3af;
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
    border-top: 1px solid #f3f4f6;
  }
  .tc-info-label {
    font-size: 14px;
    color: #6b7280;
  }
  .tc-info-value {
    font-size: 14px;
    font-weight: 600;
    color: #111;
  }

  .tc-price-wrap {
    text-align: center;
    padding: 24px 0;
  }
  .tc-price-label {
    font-size: 13px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 8px;
  }
  .tc-price {
    font-size: 42px;
    font-weight: 900;
    color: #111;
    letter-spacing: -.02em;
    line-height: 1;
  }
  .tc-price-currency {
    font-size: 24px;
    font-weight: 700;
    color: #6b7280;
    vertical-align: super;
  }

  .tc-pay-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 16px 24px;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 16px;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background .15s, transform .1s, box-shadow .15s;
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
  }
  .tc-pay-btn:hover {
    background: #222;
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
    color: #9ca3af;
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
    background: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
  }
  .tc-alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
  }
  .tc-alert-info {
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
  }

  .tc-mp-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 12px;
    font-size: 12px;
    color: #9ca3af;
  }
  .tc-mp-badge svg { width: 14px; height: 14px; }

  .tc-no-mp {
    text-align: center;
    padding: 20px;
    color: #9ca3af;
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
