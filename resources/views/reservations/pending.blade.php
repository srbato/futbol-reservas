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
        <div class="page-card" style="margin-bottom:18px; background:#fff4db; border-color:#f5d48a;">
            <p style="margin:0; color:#9a6700; line-height:1.6;">
                La reserva sigue vigente. Si el pago no termina de impactar, podés volver a revisar el estado desde <strong>Mis reservas</strong>.
            </p>
        </div>
    @else
        <div class="page-card" style="margin-bottom:18px; background:#f8d7da; border-color:#f1b9c0;">
            <p style="margin:0; color:#842029; line-height:1.6;">
                La reserva ya venció o está por vencer. Si el pago no fue aprobado a tiempo, vas a tener que volver a reservar.
            </p>
        </div>
    @endif

    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('venues.index') }}" class="btn">Volver a complejos</a>
        <a href="{{ route('my_reservations') }}" class="btn btn-primary">Ver mis reservas</a>
    </div>
</div>
@endsection