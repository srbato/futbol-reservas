@extends('layouts.app')

@section('title', 'Pago en proceso — Reserva #' . $reservation->id)

@section('content')
<div style="max-width: 560px; margin: auto; display: grid; gap: 18px;">

  <div class="page-card" style="text-align:center; padding: 40px 32px;">
    <div style="width:64px; height:64px; border-radius:50%; background:rgba(253,224,71,.1); border:2px solid rgba(253,224,71,.3); display:flex; align-items:center; justify-content:center; margin:0 auto 20px; font-size:28px;">
      &#9203;
    </div>
    <h1 style="margin:0 0 10px; font-size:26px; letter-spacing:-0.02em;">Tu pago está siendo procesado</h1>
    <p style="margin:0; color:#a0a0a0; line-height:1.6; font-size:15px;">
      MercadoPago está procesando tu pago. Cuando se confirme, tu reserva se modificará automáticamente.
      La reserva original sigue vigente hasta entonces.
    </p>
  </div>

  {{-- Datos de la reserva original --}}
  <div class="page-card">
    <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#aaa; margin-bottom:14px;">Reserva original vigente</div>

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
      <div style="border-top:1px solid rgba(255,255,255,.08); padding-top:10px; display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#666; font-size:14px;">Importe abonado</span>
        <span style="font-weight:800; font-size:18px;">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</span>
      </div>
    </div>
  </div>

  <div class="page-card" style="background:rgba(34,197,94,.06); border-color:rgba(34,197,94,.15);">
    <div style="font-size:13px; color:#15803d; line-height:1.6;">
      <strong>Nota:</strong> Si el pago se confirma exitosamente, recibirás un email con los detalles del nuevo horario.
      Si el pago no se acredita en los próximos minutos, tu reserva original permanecera sin cambios.
    </div>
  </div>

  <div style="display:flex; justify-content:center;">
    <a href="{{ route('reservations.show', $reservation) }}" class="btn btn-primary">Ver mi reserva</a>
  </div>

</div>
@endsection
