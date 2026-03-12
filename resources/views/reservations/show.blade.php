@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.app')

@section('title', 'Reserva #' . $reservation->id)

@section('content')
  <div style="max-width:900px; margin:auto; display:grid; gap:18px;">
    <div class="page-card">
      <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-start;">
        <div>
          <h1 style="margin:0 0 10px 0; font-size:34px; letter-spacing:-0.02em;">
            Reserva #{{ $reservation->id }}
          </h1>

          <p class="muted" style="margin:0; line-height:1.6;">
            Revisá el estado de tu reserva, el horario y las acciones disponibles.
          </p>
        </div>

        <div>
          <span class="badge" style="{{ ReservationStatus::color($reservation->status) }}">
            {{ ReservationStatus::label($reservation->status) }}
          </span>
        </div>
      </div>
    </div>

    @if($reservation->status === 'PENDING_PAYMENT' && $reservation->expires_at && $reservation->expires_at->isFuture())
      <div class="page-card" style="background:#fff4db; border-color:#f5d48a;">
        <div style="color:#9a6700; font-weight:700; margin-bottom:6px;">Pago pendiente</div>
        <div style="color:#9a6700; line-height:1.6;">
          Esta reserva sigue pendiente de pago y vence el
          <strong>{{ $reservation->expires_at->format('d/m/Y H:i') }}</strong>.
        </div>
      </div>
    @elseif($reservation->status === 'PAID')
      <div class="page-card" style="background:#e8f7ee; border-color:#cfe9d7;">
        <div style="color:#157347; font-weight:700; margin-bottom:6px;">Reserva confirmada</div>
        <div style="color:#157347; line-height:1.6;">
          El pago fue aprobado correctamente. Ya tenés tu turno confirmado.
        </div>
      </div>
    @elseif($reservation->status === 'EXPIRED')
      <div class="page-card" style="background:#f8d7da; border-color:#f1b9c0;">
        <div style="color:#842029; font-weight:700; margin-bottom:6px;">Reserva vencida</div>
        <div style="color:#842029; line-height:1.6;">
          La reserva venció por falta de pago. Si querés ese turno, vas a tener que volver a reservar.
        </div>
      </div>
    @elseif($reservation->status === 'CANCELLED')
      <div class="page-card" style="background:#f8d7da; border-color:#f1b9c0;">
        <div style="color:#842029; font-weight:700; margin-bottom:6px;">Reserva cancelada</div>
        <div style="color:#842029; line-height:1.6;">
          Esta reserva fue cancelada y ya no está activa.
        </div>
      </div>
    @elseif($reservation->status === 'CHECKED_IN')
      <div class="page-card" style="background:#e8f7ee; border-color:#cfe9d7;">
        <div style="color:#157347; font-weight:700; margin-bottom:6px;">Check-in realizado</div>
        <div style="color:#157347; line-height:1.6;">
          El ingreso ya fue validado en el complejo.
        </div>
      </div>
    @endif

    <div style="display:grid; grid-template-columns:1.1fr .9fr; gap:18px;">
      <div class="page-card">
        <h2 class="section-title" style="font-size:24px; margin-bottom:14px;">Detalle de la reserva</h2>

        <div style="display:grid; gap:12px;">
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
          </div>

          <div style="display:grid; gap:8px; margin-top:8px;">
            <div style="display:flex; justify-content:space-between; gap:12px;">
              <span class="muted">Total</span>
              <strong>{{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</strong>
            </div>

            <div style="display:flex; justify-content:space-between; gap:12px;">
              <span class="muted">Estado</span>
              <strong>{{ ReservationStatus::label($reservation->status) }}</strong>
            </div>

            @if($reservation->payment_provider)
              <div style="display:flex; justify-content:space-between; gap:12px;">
                <span class="muted">Proveedor de pago</span>
                <strong>{{ ucfirst($reservation->payment_provider) }}</strong>
              </div>
            @endif

            @if($reservation->payment_status)
              <div style="display:flex; justify-content:space-between; gap:12px;">
                <span class="muted">Estado del pago</span>
                <strong>{{ $reservation->payment_status }}</strong>
              </div>
            @endif

            @if($reservation->expires_at)
              <div style="display:flex; justify-content:space-between; gap:12px;">
                <span class="muted">Vencimiento</span>
                <strong>{{ $reservation->expires_at->format('d/m/Y H:i') }}</strong>
              </div>
            @endif
          </div>
        </div>
      </div>

      <div style="display:grid; gap:18px;">
        <div class="page-card">
          <h2 class="section-title" style="font-size:24px; margin-bottom:14px;">Código de verificación</h2>

          @if(in_array($reservation->status, ['PAID', 'CHECKED_IN']) && $reservation->verification_code)
            <div style="font-size:34px; font-weight:800; letter-spacing:.08em; line-height:1.1;">
              {{ $reservation->verification_code }}
            </div>

            <p class="muted" style="margin:12px 0 0 0; line-height:1.6;">
              Mostrá este código al momento de hacer el check-in en el complejo.
            </p>
          @else
            <p class="muted" style="margin:0; line-height:1.6;">
              El código de verificación se habilita cuando la reserva está pagada.
            </p>
          @endif
        </div>

        <div class="page-card">
          <h2 class="section-title" style="font-size:24px; margin-bottom:14px;">Acciones</h2>

          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('venues.index') }}" class="btn">
              Volver a complejos
            </a>

            <a href="{{ route('my_reservations') }}" class="btn">
              Ver mis reservas
            </a>

            @if($reservation->status === 'PENDING_PAYMENT' && (!$reservation->expires_at || $reservation->expires_at->isFuture()))
              <a href="{{ route('reservations.checkout', $reservation) }}" class="btn btn-primary">
                Ir a pagar
              </a>
            @endif

            @if(in_array($reservation->status, ['EXPIRED', 'CANCELLED']))
              <a href="{{ route('fields.show', $reservation->field) }}" class="btn btn-primary">
                Volver a la cancha
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    @media (max-width: 900px) {
      div[style*="grid-template-columns:1.1fr .9fr"] {
        grid-template-columns: 1fr !important;
      }
    }
  </style>
@endsection