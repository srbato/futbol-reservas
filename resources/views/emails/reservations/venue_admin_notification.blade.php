<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nueva reserva</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">📅 Nueva reserva confirmada</h1>

    <p>
      Hola, se realizó una nueva reserva en tu complejo.
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Complejo:</strong> {{ $reservation->field->venue->name }}</p>
    <p><strong>Cancha:</strong> {{ $reservation->field->name }}</p>
    <p><strong>Fecha:</strong> {{ $reservation->start_at->format('d/m/Y') }}</p>
    <p><strong>Horario:</strong> {{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</p>
    <p><strong>Monto:</strong> {{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Datos del usuario:</strong></p>
    <p>
      {{ $reservation->user->name }}<br>
      {{ $reservation->user->email }}
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666;">
      Podés ver todos los detalles en tu
      <a href="{{ url('/va') }}" style="color:#111;">panel de administración</a>.
    </p>
  </div>
</body>
</html>