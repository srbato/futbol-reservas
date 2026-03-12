@extends('layouts.app')

@section('title','Pago no completado')

@section('content')
<div class="page-card" style="max-width:760px; margin:auto;">

    <div style="text-align:center; margin-bottom:24px;">
        <h1 style="font-size:34px; margin-bottom:10px;">❌ Pago no completado</h1>

        <p class="muted" style="margin:0;">
            El pago no se pudo procesar o fue cancelado. Según el estado de la reserva, podés intentar nuevamente o volver a reservar.
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
                    <strong>Vencimiento:</strong> {{ $reservation->expires_at->format('d/m/Y H:i') }}
                </p>
            @endif
        </div>
    </div>

    @if($reservation->status === 'PENDING_PAYMENT' && (!$reservation->expires_at || $reservation->expires_at->isFuture()))
        <div class="page-card" style="margin-bottom:18px; background:#fff4db; border-color:#f5d48a;">
            <p style="margin:0; color:#9a6700; line-height:1.6;">
                La reserva sigue vigente y todavía podés intentar pagar nuevamente.
            </p>
        </div>
    @else
        <div class="page-card" style="margin-bottom:18px; background:#f8d7da; border-color:#f1b9c0;">
            <p style="margin:0; color:#842029; line-height:1.6;">
                Esta reserva ya no está disponible para pago. Si querés ese horario, vas a tener que volver a reservarlo.
            </p>
        </div>
    @endif

    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('my_reservations') }}" class="btn">Ver mis reservas</a>

        @if($reservation->status === 'PENDING_PAYMENT' && (!$reservation->expires_at || $reservation->expires_at->isFuture()))
            <a href="{{ route('reservations.checkout', $reservation) }}" class="btn btn-primary">
                Intentar pagar otra vez
            </a>
        @else
            <a href="{{ route('fields.show', $reservation->field) }}" class="btn btn-primary">
                Volver a la cancha
            </a>
        @endif
    </div>
</div>
@endsection