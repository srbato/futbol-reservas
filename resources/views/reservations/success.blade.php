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
            <h1 style="font-size:34px; margin-bottom:10px;">⏳ Esperando confirmación del pago</h1>
            <p class="muted" style="margin:0;">
                Estamos esperando la confirmación de Mercado Pago. Esto puede tardar unos segundos.
            </p>
            <div style="margin-top:16px; display:flex; align-items:center; justify-content:center; gap:10px; color:#9a6700; font-weight:600;">
                <span id="pollingDot" style="width:10px; height:10px; border-radius:999px; background:#f0ad00; display:inline-block; animation:pulse 1s infinite;"></span>
                <span id="pollingText">Verificando estado...</span>
            </div>
        </div>
    @else
        <div style="text-align:center; margin-bottom:24px;">
            <h1 style="font-size:34px; margin-bottom:10px;">ℹ️ Estado actualizado</h1>
            <p class="muted" style="margin:0;">
                Estado actual: <strong>{{ $reservation->status }}</strong>
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
                Si el pago fue aprobado, la reserva se actualizará automáticamente en esta misma página.
            </p>
        </div>
    @endif

    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('venues.index') }}" class="btn">Volver a complejos</a>
        <a href="{{ route('my_reservations') }}" class="btn btn-primary">Ver mis reservas</a>
    </div>
</div>

@if($reservation->status === 'PENDING_PAYMENT')
<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }
</style>
<script>
    const reservationId = {{ $reservation->id }};
    let attempts = 0;
    const maxAttempts = 20; // intenta por ~1 minuto

    function checkStatus() {
        if (attempts >= maxAttempts) {
            document.getElementById('pollingText').innerText = 'No se pudo confirmar automáticamente. Revisá tus reservas.';
            document.getElementById('pollingDot').style.background = '#842029';
            return;
        }

        attempts++;

        fetch(`/reservations/${reservationId}/status`)
            .then(r => r.json())
            .then(data => {
                if (data.status === 'PAID') {
                    window.location.reload();
                } else {
                    setTimeout(checkStatus, 3000);
                }
            })
            .catch(() => {
                setTimeout(checkStatus, 3000);
            });
    }

    setTimeout(checkStatus, 3000);
</script>
@endif
@endsection