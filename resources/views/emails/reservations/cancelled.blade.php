<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reserva cancelada</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">

    @if($recipient === 'admin')
      <h1 style="margin-top:0;">❌ Reserva cancelada en tu complejo</h1>
      <p>Una reserva fue cancelada por el usuario.</p>
    @else
      <h1 style="margin-top:0;">❌ Tu reserva fue cancelada</h1>
      <p>Hola {{ $reservation->user->name }}, tu reserva fue cancelada.</p>
    @endif

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Complejo:</strong> {{ $reservation->field->venue->name }}</p>
    <p><strong>Cancha:</strong> {{ $reservation->field->name }}</p>
    <p><strong>Fecha:</strong> {{ $reservation->start_at->format('d/m/Y') }}</p>
    <p><strong>Horario:</strong> {{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</p>
    <p><strong>Monto:</strong> {{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</p>

    @if($recipient === 'admin')
      <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">
      <p><strong>Usuario:</strong> {{ $reservation->user->name }}</p>
      <p><strong>Email:</strong> {{ $reservation->user->email }}</p>
    @endif

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666;">
      @if($recipient === 'admin')
        Podés ver todos los detalles en tu <a href="{{ url('/va') }}">panel de administración</a>.
      @else
        Si tenés alguna consulta, contactanos desde <a href="{{ url('/') }}">TuCancha</a>.
      @endif
    </p>
  </div>
</body>
</html>