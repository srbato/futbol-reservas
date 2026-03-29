@extends('layouts.app')

@section('title', 'Pago no procesado — Reserva #' . $reservation->id)

@section('content')
<div style="max-width: 560px; margin: auto; display: grid; gap: 18px;">

  <div class="page-card" style="text-align:center; padding: 40px 32px;">
    <div style="width:64px; height:64px; border-radius:50%; background:#fee2e2; border:2px solid #fca5a5; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:28px;">
      &#10060;
    </div>
    <h1 style="margin:0 0 10px; font-size:26px; letter-spacing:-0.02em;">El pago no se procesó</h1>
    <p style="margin:0; color:#555; line-height:1.6; font-size:15px;">
      Hubo un problema con el pago. Tu reserva original sigue vigente sin cambios.
      Podés volver a intentar el cambio de horario cuando quieras.
    </p>
  </div>

  {{-- Datos de la reserva original --}}
  <div class="page-card">
    <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#aaa; margin-bottom:14px;">Tu reserva (sin cambios)</div>

    <div style="display:grid; gap:10px;">
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#666; font-size:14px;">Cancha</span>
        <span style="font-weight:700; font-size:14px;">{{ $reservation->field->name }}</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#666; font-size:14px;">Complejo</span>
        <span style="font-weight:700; font-size:14px;">{{ $reservation->field->venue->name }}</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#666; font-size:14px;">Fecha</span>
        <span style="font-weight:700; font-size:14px;">{{ $reservation->start_at->format('d/m/Y') }}</span>
      </div>
      <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#666; font-size:14px;">Horario</span>
        <span style="font-weight:700; font-size:14px;">{{ $reservation->start_at->format('H:i') }} – {{ $reservation->end_at->format('H:i') }}</span>
      </div>
      <div style="border-top:1px solid #eee; padding-top:10px; display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#666; font-size:14px;">Importe abonado</span>
        <span style="font-weight:800; font-size:18px;">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</span>
      </div>
    </div>
  </div>

  <div class="page-card" style="background:#fef9c3; border-color:#fde047;">
    <div style="font-size:13px; color:#854d0e; line-height:1.6;">
      <strong>Recordá:</strong> Para cambiar el horario, el nuevo slot debe seguir disponible y dentro del plazo permitido por el complejo.
    </div>
  </div>

  <div style="display:flex; gap:12px; flex-wrap:wrap; justify-content:center;">
    <a href="{{ route('reservations.modify.show', $reservation) }}" class="btn btn-primary">Volver a intentar</a>
    <a href="{{ route('reservations.show', $reservation) }}" class="btn">Ver mi reserva</a>
  </div>

</div>
@endsection
