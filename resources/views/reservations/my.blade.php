@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.app')

@section('title', 'Mis reservas')

@section('content')
  <div class="page-card" style="margin-bottom:22px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 8px 0; font-size:34px; letter-spacing:-0.02em;">Mis reservas</h1>
        <p class="muted" style="margin:0;">
          Consultá el estado de tus turnos, pagá reservas pendientes o cancelalas si todavía corresponde.
        </p>
      </div>

      <div>
        <a href="{{ route('venues.index') }}" class="btn btn-primary">Explorar complejos</a>
      </div>
    </div>
  </div>

  @if($reservations->isEmpty())
    <div class="page-card">
      <h3 style="margin-top:0;">Todavía no tenés reservas</h3>
      <p class="muted" style="margin-bottom:14px;">
        Cuando reserves una cancha, la vas a ver acá con su estado y acceso al pago.
      </p>

      <a href="{{ route('venues.index') }}" class="btn btn-primary">Ver complejos</a>
    </div>
  @else
    <div class="grid" style="grid-template-columns:repeat(auto-fit, minmax(min(340px, 100%), 1fr));">
      @foreach($reservations as $r)
        <article class="venue-card" style="border-radius:18px;">
          <div class="venue-card-body" style="padding:20px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
              <div>
                <div style="font-size:12px; color:#666; margin-bottom:6px;">
                  Reserva #{{ $r->id }}
                </div>

                <h3 style="margin:0 0 6px 0; font-size:22px;">
                  {{ $r->field->name }}
                </h3>

                <div class="muted">
                  {{ $r->field->venue->name }}
                </div>
              </div>

              <span
                id="status-badge-{{ $r->id }}"
                style="padding:6px 12px; border-radius:999px; font-weight:700; font-size:13px; {{ ReservationStatus::color($r->status) }}"
              >
                {{ ReservationStatus::label($r->status) }}
              </span>
            </div>

            <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:12px;">
              <span class="badge">
                {{ $r->start_at->format('d/m/Y') }}
              </span>
              <span class="badge">
                {{ $r->start_at->format('H:i') }} - {{ $r->end_at->format('H:i') }}
              </span>
              <span class="badge">
                {{ $r->currency }} {{ number_format($r->total_amount, 0, ',', '.') }}
              </span>
            </div>

            @if($r->verification_code && in_array($r->status, ['PAID', 'CHECKED_IN']))
              <div class="page-card" style="margin-top:14px; padding:14px; border-radius:14px;">
                <div style="font-size:12px; color:#666; margin-bottom:4px;">Código de verificación</div>
                <div style="font-size:24px; font-weight:800; letter-spacing:0.06em;">
                  {{ $r->verification_code }}
                </div>
              </div>
            @endif

            <div id="reservation-meta-{{ $r->id }}" style="margin-top:12px; font-size:13px;">
              @if($r->status === 'PENDING_PAYMENT' && (!$r->expires_at || $r->expires_at->isFuture()))
                <div style="color:#856404;">
                  <div>
                    Pendiente de pago
                    @if($r->expires_at)
                      — vence {{ $r->expires_at->format('d/m/Y H:i') }}
                    @endif
                  </div>

                  @if($r->expires_at)
                    <div id="countdown-{{ $r->id }}" style="margin-top:6px; font-weight:700;"></div>
                  @endif
                </div>
              @elseif($r->status === 'EXPIRED')
                <div style="color:#842029; font-weight:700;">
                  Reserva vencida por falta de pago.
                </div>
              @elseif($r->status === 'CANCELLED')
                <div style="color:#842029; font-weight:700;">
                  Reserva cancelada.
                </div>
              @elseif($r->status === 'PAID')
                <div style="color:#157347; font-weight:700;">
                  Reserva pagada correctamente.
                </div>
              @elseif($r->status === 'CHECKED_IN')
                <div style="color:#157347; font-weight:700;">
                  Check-in realizado.
                </div>
              @endif

              @if($r->payment_status)
                <div class="muted" style="margin-top:6px;">
                  Estado de pago: <strong>{{ $r->payment_status }}</strong>
                </div>
              @endif
            </div>

            <div id="actions-wrap-{{ $r->id }}" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:16px;">
              <a href="{{ route('reservations.show', $r) }}" class="btn">
                Ver detalle
              </a>

              @if($r->status === 'PENDING_PAYMENT' && (!$r->expires_at || $r->expires_at->isFuture()))
                <span id="pay-btn-wrap-{{ $r->id }}">
                  <a href="{{ route('reservations.checkout', $r) }}" class="btn btn-primary">
                    Pagar
                  </a>
                </span>
              @endif

              @if(in_array($r->status, ['PENDING_PAYMENT','PAID']))
                <form method="POST" action="{{ route('reservations.cancel', $r) }}" style="margin:0;">
                  @csrf
                  <button type="submit" class="btn">Cancelar</button>
                </form>
              @endif

              @if(in_array($r->status, ['EXPIRED', 'CANCELLED']))
                <a href="{{ route('fields.show', $r->field) }}" class="btn btn-primary">
                  Volver a la cancha
                </a>
              @endif
            </div>
          </div>
        </article>
      @endforeach
    </div>
  @endif

  <script>
    function markReservationAsExpired(reservationId, fieldUrl) {
      const payWrap = document.getElementById(`pay-btn-wrap-${reservationId}`);
      const meta = document.getElementById(`reservation-meta-${reservationId}`);
      const badge = document.getElementById(`status-badge-${reservationId}`);
      const actions = document.getElementById(`actions-wrap-${reservationId}`);

      if (payWrap) {
        payWrap.style.display = 'none';
      }

      if (meta) {
        meta.innerHTML = `
          <div style="color:#842029; font-weight:700;">
            Reserva vencida por falta de pago.
          </div>
        `;
      }

      if (badge) {
        badge.textContent = 'Expirada';
        badge.style.background = '#f8d7da';
        badge.style.color = '#842029';
        badge.style.border = '1px solid #f1b9c0';
      }

      if (actions && !document.getElementById(`back-to-field-${reservationId}`)) {
        actions.insertAdjacentHTML('beforeend', `
          <a href="${fieldUrl}" class="btn btn-primary" id="back-to-field-${reservationId}">
            Volver a la cancha
          </a>
        `);
      }
    }

    function startCountdown(elementId, expiresAt, payWrapId, reservationId, fieldUrl) {
      const el = document.getElementById(elementId);
      const payWrap = document.getElementById(payWrapId);

      if (!el) return;

      function tick() {
        const now = new Date().getTime();
        const end = new Date(expiresAt).getTime();
        const diff = end - now;

        if (diff <= 0) {
          el.innerText = 'La reserva expiró';

          if (payWrap) {
            payWrap.style.display = 'none';
          }

          markReservationAsExpired(reservationId, fieldUrl);
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

  @foreach($reservations as $r)
    @if($r->status === 'PENDING_PAYMENT' && $r->expires_at && $r->expires_at->isFuture())
      <script>
        startCountdown(
          'countdown-{{ $r->id }}',
          '{{ $r->expires_at->toIso8601String() }}',
          'pay-btn-wrap-{{ $r->id }}',
          {{ $r->id }},
          '{{ route('fields.show', $r->field) }}'
        );
      </script>
    @endif
  @endforeach

@endsection