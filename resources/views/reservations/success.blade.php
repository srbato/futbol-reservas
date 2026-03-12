@extends('layouts.app')

@section('title','Estado del pago')

@section('content')
<div class="page-card" style="max-width:760px; margin:auto;">

    @if($reservation->status === 'PAID')
        <div style="text-align:center; margin-bottom:24px;">
            <h1 style="font-size:34px; margin-bottom:10px;">✅ Reserva confirmada</h1>
            <p class="muted" style="margin:0;">
                Tu pago fue aprobado y la reserva quedó confirmada correctamente.
            </p>
        </div>
    @elseif($reservation->status === 'PENDING_PAYMENT')
        <div style="text-align:center; margin-bottom:24px;">
            <h1 style="font-size:34px; margin-bottom:10px;">⏳ Pago recibido, esperando confirmación</h1>
            <p class="muted" style="margin:0;">
                Volviste desde Mercado Pago, pero la reserva todavía figura pendiente. Esto puede pasar si el webhook aún no impactó.
            </p>
        </div>
    @else
        <div style="text-align:center; margin-bottom:24px;">
            <h1 style="font-size:34px; margin-bottom:10px;">ℹ️ Estado actualizado</h1>
            <p class="muted" style="margin:0;">
                El pago volvió desde Mercado Pago, pero la reserva actualmente tiene el estado <strong>{{ $reservation->status }}</strong>.
            </p>
        </div>
    @endif

    <div class="page-card" style="margin-bottom:18px;">
        <div style="display:grid; gap:10px;">
            <p style="margin:0;"><strong>Complejo:</strong> {{ $reservation->field->venue->name }}</p>
            <p style="margin:0;"><strong>Cancha:</strong> {{ $reservation->field->name }}</p>
            <p style="margin:0;"><strong>Fecha:</strong> {{ $reservation->start_at->format('d/m/Y') }}</p>
            <p style="margin:0;"><strong>Horario:</strong> {{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</p>
            <p style="margin:0;"><strong>Estado actual:</strong> {{ $reservation->status }}</p>

            @if($reservation->status === 'PAID')
                <p style="margin:0;">
                    <strong>Código de verificación:</strong>
                    <span style="font-size:22px; font-weight:800;">
                        {{ $reservation->verification_code }}
                    </span>
                </p>
            @endif
        </div>
    </div>

    @if($reservation->status === 'PENDING_PAYMENT')
        <div class="page-card" style="margin-bottom:18px; background:#fff4db; border-color:#f5d48a;">
            <p style="margin:0; color:#9a6700; line-height:1.6;">
                Si el pago fue aprobado, la reserva debería actualizarse automáticamente en breve.
                Podés revisar el estado actual en <strong>Mis reservas</strong>.
            </p>
        </div>
    @endif

    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('venues.index') }}" class="btn">
            Volver a complejos
        </a>

        <a href="{{ route('my_reservations') }}" class="btn btn-primary">
            Ver mis reservas
        </a>
    </div>
</div>
@endsection