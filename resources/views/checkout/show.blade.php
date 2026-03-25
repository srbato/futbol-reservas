@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.app')

@section('title', 'Pagar reserva')

@section('content')
  @if(session('info'))
    <div style="background:linear-gradient(135deg,#f0f9ff,#e0f2fe); border:1px solid #bae6fd; border-radius:14px; padding:14px 18px; margin-bottom:18px; display:flex; align-items:center; gap:12px;">
      <i data-lucide="zap" style="width:20px;height:20px;stroke:#0369a1;flex-shrink:0;"></i>
      <div style="font-size:14px; font-weight:700; color:#0369a1;">{{ session('info') }}</div>
    </div>
  @endif

  @if($availableReward ?? null)
    <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7); border:1px solid #86efac; border-radius:14px; padding:14px 18px; margin-bottom:18px; display:flex; align-items:center; gap:12px;">
      <i data-lucide="gift" style="width:20px;height:20px;stroke:#15803d;flex-shrink:0;"></i>
      <div>
        <div style="font-size:14px; font-weight:700; color:#15803d;">Tenés un beneficio de referido disponible</div>
        <div style="font-size:13px; color:#166534; margin-top:2px; line-height:1.5;">
          Tenés un beneficio pendiente por haber referido a un nuevo socio.
          Contactá al soporte de TuCancha para aplicarlo a tu reserva.
        </div>
      </div>
    </div>
  @endif
  <div style="display:grid; grid-template-columns:1.1fr .9fr; gap:20px; align-items:start;">
    <div class="page-card">
      <h1 style="margin:0 0 10px 0; font-size:34px; letter-spacing:-0.02em;">Pagar reserva</h1>
      <p class="muted" style="margin:0 0 18px 0;">
        Revisá los datos del turno antes de completar el pago.
      </p>

      <div style="display:grid; gap:14px;">
        <div>
          <div style="font-size:12px; color:#666; margin-bottom:4px;">Complejo</div>
          <div style="font-size:18px; font-weight:700;">{{ $reservation->field->venue->name }}</div>
        </div>

        <div>
          <div style="font-size:12px; color:#666; margin-bottom:4px;">Cancha</div>
          <div style="font-size:18px; font-weight:700;">{{ $reservation->field->name }}</div>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <span class="badge">{{ $reservation->start_at->format('d/m/Y') }}</span>
          <span class="badge">{{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</span>
          <span class="badge" style="{{ ReservationStatus::color($reservation->status) }}">
            {{ ReservationStatus::label($reservation->status) }}
          </span>
        </div>

        <div id="checkoutStatusBox">
          @if($reservation->status === 'PAID')
            <div style="padding:12px 14px; border-radius:12px; background:#e8f7ee; color:#157347; border:1px solid #cfe9d7;">
              Esta reserva ya fue pagada correctamente.
            </div>
          @elseif($reservation->expires_at && $reservation->expires_at->isFuture())
            <div style="padding:12px 14px; border-radius:12px; background:#fff3cd; color:#856404; border:1px solid #f3df8a;">
              <div>
                Esta reserva está pendiente de pago y vence el
                <strong>{{ $reservation->expires_at->format('d/m/Y H:i') }}</strong>.
              </div>
              <div id="checkoutCountdown" style="margin-top:6px; font-weight:700;"></div>
            </div>
          @elseif($reservation->expires_at && $reservation->expires_at->isPast())
            <div style="padding:12px 14px; border-radius:12px; background:#f8d7da; color:#842029; border:1px solid #f1b9c0;">
              Esta reserva ya expiró y no se puede pagar.
            </div>
          @else
            <div style="padding:12px 14px; border-radius:12px; background:#f3f3f3; color:#444; border:1px solid #e2e2e2;">
              Revisá el estado actual de la reserva antes de continuar.
            </div>
          @endif
        </div>

        <div id="checkoutActions" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:8px;">
          @if($reservation->status === 'PENDING_PAYMENT' && (!$reservation->expires_at || $reservation->expires_at->isFuture()))
            <form method="POST" action="{{ route('reservations.mercadopago', $reservation) }}" style="margin:0;" id="mercadoPagoCheckoutForm">
              @csrf
              <button type="submit" class="btn btn-primary" id="checkoutPayButton">Pagar con Mercado Pago</button>
            </form>
          @endif

          <a href="{{ route('my_reservations') }}" class="btn">Volver a mis reservas</a>

          @if($reservation->expires_at && $reservation->expires_at->isPast() && $reservation->status !== 'PAID')
            <a href="{{ route('fields.show', $reservation->field) }}" class="btn btn-primary">
              Volver a la cancha
            </a>
          @endif
        </div>
      </div>
    </div>

    <div style="display:grid; gap:16px;">
<div class="page-card">
      <div style="font-size:12px; color:#666; margin-bottom:6px;">Resumen del pago</div>
      <div style="font-size:38px; font-weight:800; line-height:1.1; margin-bottom:14px;">
        {{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}
      </div>

      <div style="display:grid; gap:10px;">
        <div style="display:flex; justify-content:space-between; gap:12px;">
          <span class="muted">Reserva</span>
          <strong>#{{ $reservation->id }}</strong>
        </div>

        <div style="display:flex; justify-content:space-between; gap:12px;">
          <span class="muted">Estado actual</span>
          <strong>{{ ReservationStatus::label($reservation->status) }}</strong>
        </div>

        <div style="display:flex; justify-content:space-between; gap:12px;">
          <span class="muted">Método</span>
          <strong>Mercado Pago</strong>
        </div>
      </div>

      <hr style="margin:18px 0; border:none; border-top:1px solid #eee;">

      @if($reservation->status === 'PAID')
        <p class="muted" style="margin:0; line-height:1.6;">
          La reserva ya está pagada. Podés revisar el código de verificación desde <strong style="color:#111;">Mis reservas</strong>.
        </p>
      @elseif($reservation->expires_at && $reservation->expires_at->isFuture())
        <p class="muted" style="margin:0; line-height:1.6;">
          Una vez aprobado el pago, la reserva pasará automáticamente a estado
          <strong style="color:#111;">Pagada</strong> y se habilitará el código de verificación.
        </p>
      @else
        <p class="muted" style="margin:0; line-height:1.6;">
          Si la reserva venció, ya no se podrá pagar y será necesario volver a reservar el turno.
        </p>
      @endif
    </div>
    </div>{{-- end stacked right column --}}
  </div>

  <style>
    @media (max-width: 900px) {
      div[style*="grid-template-columns:1.1fr .9fr"] {
        grid-template-columns: 1fr !important;
      }
    }
    @keyframes mp-spin { to { transform: rotate(360deg); } }
    .mp-spinner {
      display: inline-block;
      width: 14px;
      height: 14px;
      border: 2.5px solid rgba(255,255,255,.35);
      border-top-color: #fff;
      border-radius: 50%;
      animation: mp-spin .7s linear infinite;
      vertical-align: middle;
      margin-right: 7px;
      flex-shrink: 0;
    }
  </style>

  <script>
    // Loading state al pagar con MP
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('mercadoPagoCheckoutForm');
      if (!form) return;
      form.addEventListener('submit', function () {
        const btn = document.getElementById('checkoutPayButton');
        if (!btn) return;
        btn.disabled = true;
        btn.style.opacity = '.75';
        btn.style.cursor = 'default';
        btn.style.transform = 'none';
        btn.style.display = 'inline-flex';
        btn.style.alignItems = 'center';
        btn.innerHTML = '<span class="mp-spinner"></span>Redirigiendo a Mercado Pago&hellip;';
      });
    });

    function markCheckoutAsExpired() {
      const statusBox = document.getElementById('checkoutStatusBox');
      const payForm = document.getElementById('mercadoPagoCheckoutForm');
      const payButton = document.getElementById('checkoutPayButton');

      if (statusBox) {
        statusBox.innerHTML = `
          <div style="padding:12px 14px; border-radius:12px; background:#f8d7da; color:#842029; border:1px solid #f1b9c0;">
            Esta reserva expiró mientras estabas en la pantalla de pago. Ya no se puede pagar.
          </div>
        `;
      }

      if (payButton) {
        payButton.disabled = true;
        payButton.innerText = 'Reserva expirada';
        payButton.style.opacity = '.65';
        payButton.style.cursor = 'not-allowed';
      }

      if (payForm) {
        payForm.remove();
      }

      const actions = document.getElementById('checkoutActions');
      if (actions && !document.getElementById('backToFieldBtn')) {
        actions.insertAdjacentHTML('beforeend', `
          <a href="{{ route('fields.show', $reservation->field) }}" class="btn btn-primary" id="backToFieldBtn">
            Volver a la cancha
          </a>
        `);
      }
    }

    function startCountdown(elementId, expiresAt) {
      const el = document.getElementById(elementId);
      if (!el) return;

      function tick() {
        const now = new Date().getTime();
        const end = new Date(expiresAt).getTime();
        const diff = end - now;

        if (diff <= 0) {
          el.innerText = 'La reserva expiró';
          markCheckoutAsExpired();
          return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');

        el.innerText = `Te quedan ${minutes}:${seconds} para pagar`;
      }

      tick();
      const interval = setInterval(() => {
        const now = new Date().getTime();
        const end = new Date(expiresAt).getTime();

        if (end - now <= 0) {
          clearInterval(interval);
        }

        tick();
      }, 1000);
    }
  </script>

  @if($reservation->status === 'PENDING_PAYMENT' && $reservation->expires_at && $reservation->expires_at->isFuture())
    <script>
      startCountdown('checkoutCountdown', '{{ $reservation->expires_at->toIso8601String() }}');
    </script>
  @endif

@endsection