@extends('layouts.app')

@section('title','Pago pendiente')

@section('content')
<div class="page-card" style="max-width:760px; margin:auto;">

    <div style="text-align:center; margin-bottom:24px;">
        <h1 style="font-size:34px; margin-bottom:10px;">⏳ Pago pendiente</h1>

        <p class="muted" style="margin:0;">
            Tu pago todavía no fue confirmado. Cuando Mercado Pago lo apruebe, la reserva se actualizará automáticamente.
        </p>
    </div>

    <div class="page-card" style="margin-bottom:18px;">
        <div style="display:grid; gap:10px;">
            <p style="margin:0;"><strong>Complejo:</strong> {{ $reservation->field->venue->name }}</p>
            <p style="margin:0;"><strong>Cancha:</strong> {{ $reservation->field->name }}</p>
            <p style="margin:0;"><strong>Fecha:</strong> {{ $reservation->start_at->format('d/m/Y') }}</p>
            <p style="margin:0;"><strong>Horario:</strong> {{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</p>
            <p style="margin:0;"><strong>Estado actual:</strong> {{ $reservation->status }}</p>

            @if($reservation->expires_at)
                <p style="margin:0;">
                    <strong>Vence:</strong> {{ $reservation->expires_at->format('d/m/Y H:i') }}
                </p>
            @endif
        </div>
    </div>

    @if($reservation->expires_at && $reservation->expires_at->isFuture())
        <div class="page-card" style="margin-bottom:18px; background:rgba(245,158,11,.08); border-color:rgba(245,158,11,.2);">
            <p style="margin:0; color:#fbbf24; line-height:1.6;">
                La reserva sigue vigente. Si el pago no termina de impactar, podés volver a revisar el estado desde <strong>Mis reservas</strong>.
            </p>
        </div>
    @else
        <div class="page-card" style="margin-bottom:18px; background:rgba(229,57,53,.1); border-color:rgba(229,57,53,.2);">
            <p style="margin:0; color:#f87171; line-height:1.6;">
                La reserva ya venció o está por vencer. Si el pago no fue aprobado a tiempo, vas a tener que volver a reservar.
            </p>
        </div>
    @endif

    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('venues.index') }}" class="btn">Volver a complejos</a>
        <a href="{{ route('my_reservations') }}" class="btn btn-primary">Ver mis reservas</a>
    </div>
</div>

<script>
    // Polling: verificar si el pago fue aprobado y recargar automáticamente
    const reservationId = {{ $reservation->id }};
    let attempts = 0;
    const maxAttempts = 20;

    function checkStatus() {
        if (attempts >= maxAttempts) return;
        attempts++;

        fetch(`/reservations/${reservationId}/status`)
            .then(r => {
                if (r.status === 401) { window.location.href = '/login'; return null; }
                return r.json();
            })
            .then(data => {
                if (!data) return;
                if (data.status === 'PAID') {
                    window.location.href = '/reservation-success/' + reservationId;
                } else if (data.status === 'CANCELLED' || data.status === 'EXPIRED') {
                    window.location.href = '/reservation-failure/' + reservationId;
                } else {
                    setTimeout(checkStatus, 3000);
                }
            })
            .catch(() => setTimeout(checkStatus, 3000));
    }

    setTimeout(checkStatus, 3000);
</script>
@endsection