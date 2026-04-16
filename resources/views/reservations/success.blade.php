@extends('layouts.app')

@section('title','Estado del pago')

@section('content')
<div class="page-card" style="max-width:760px; margin:auto;">

    @if($reservation->status === 'PAID')
        <div style="text-align:center; margin-bottom:24px; position:relative;">
            {{-- Check animado --}}
            <div class="success-check-wrap">
                <svg class="success-check" viewBox="0 0 52 52">
                    <circle class="success-check-circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="success-check-tick" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>
            <h1 style="font-size:34px; margin-bottom:10px;">Reserva confirmada</h1>
            <p class="muted" style="margin:0;">
                Tu pago fue aprobado y la reserva quedó confirmada correctamente.
            </p>
            {{-- Confetti canvas --}}
            <canvas id="confettiCanvas" style="position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:9999;"></canvas>
        </div>
    @elseif($reservation->status === 'PENDING_PAYMENT')
        <div style="text-align:center; margin-bottom:24px;">
            <h1 style="font-size:34px; margin-bottom:10px;">⏳ Esperando confirmación del pago</h1>
            <p class="muted" style="margin:0;">
                Estamos esperando la confirmación de Mercado Pago. Esto puede tardar unos segundos.
            </p>
            <div style="margin-top:16px; display:flex; align-items:center; justify-content:center; gap:10px; color:#fbbf24; font-weight:600;">
                <span id="pollingDot" style="width:10px; height:10px; border-radius:999px; background:#fbbf24; display:inline-block; animation:pulse 1s infinite;"></span>
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

            @if($reservation->status === 'PAID' && auth()->check() && auth()->id() === $reservation->user_id)
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
        <div class="page-card" style="margin-bottom:18px; background:rgba(245,158,11,.08); border-color:rgba(245,158,11,.2);">
            <p style="margin:0; color:#fbbf24; line-height:1.6;">
                Si el pago fue aprobado, la reserva se actualizará automáticamente en esta misma página.
            </p>
        </div>
    @endif

    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('venues.index') }}" class="btn">Volver a complejos</a>
        <a href="{{ route('my_reservations') }}" class="btn btn-primary">Ver mis reservas</a>
    </div>
</div>

@if($reservation->status === 'PAID')
<style>
    .success-check-wrap {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
    }
    .success-check {
        width: 80px;
        height: 80px;
    }
    .success-check-circle {
        stroke: #22c55e;
        stroke-width: 2;
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-linecap: round;
        animation: sc-circle .6s ease-in-out forwards;
    }
    .success-check-tick {
        stroke: #22c55e;
        stroke-width: 3;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: sc-tick .3s .6s ease-in-out forwards;
    }
    @keyframes sc-circle {
        to { stroke-dashoffset: 0; }
    }
    @keyframes sc-tick {
        to { stroke-dashoffset: 0; }
    }
</style>
<script>
(function() {
    const canvas = document.getElementById('confettiCanvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const colors = ['#22c55e','#16a34a','#6eeaa0','#f5b301','#3b82f6','#ec4899','#8b5cf6'];
    const pieces = [];
    for (let i = 0; i < 120; i++) {
        pieces.push({
            x: canvas.width / 2,
            y: canvas.height / 2,
            vx: (Math.random() - 0.5) * 16,
            vy: (Math.random() - 1) * 14 - 4,
            w: Math.random() * 8 + 4,
            h: Math.random() * 6 + 2,
            color: colors[Math.floor(Math.random() * colors.length)],
            rotation: Math.random() * 360,
            rotSpeed: (Math.random() - 0.5) * 12,
            gravity: 0.25 + Math.random() * 0.15,
            opacity: 1,
        });
    }

    let frame = 0;
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        let alive = false;
        pieces.forEach(p => {
            if (p.opacity <= 0) return;
            alive = true;
            p.x += p.vx;
            p.vy += p.gravity;
            p.y += p.vy;
            p.vx *= 0.99;
            p.rotation += p.rotSpeed;
            if (frame > 40) p.opacity -= 0.015;

            ctx.save();
            ctx.translate(p.x, p.y);
            ctx.rotate(p.rotation * Math.PI / 180);
            ctx.globalAlpha = Math.max(0, p.opacity);
            ctx.fillStyle = p.color;
            ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
            ctx.restore();
        });
        frame++;
        if (alive) requestAnimationFrame(draw);
        else canvas.remove();
    }
    setTimeout(draw, 600);
})();
</script>
@endif

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
            document.getElementById('pollingDot').style.background = '#f87171';
            return;
        }

        attempts++;

        fetch(`/reservations/${reservationId}/status`)
            .then(r => {
                if (r.status === 401) {
                    // Sesión expirada: redirigir al login
                    window.location.href = '/login';
                    return null;
                }
                if (r.status === 403) {
                    document.getElementById('pollingText').innerText = 'No tenés permiso para ver esta reserva.';
                    document.getElementById('pollingDot').style.background = '#f87171';
                    return null;
                }
                return r.json();
            })
            .then(data => {
                if (!data) return;
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