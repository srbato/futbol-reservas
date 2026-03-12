<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reserva confirmada</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">✅ Reserva confirmada</h1>

    <p>
      Hola {{ $reservation->user->name }},
    </p>

    <p>
      Tu pago fue aprobado y tu reserva quedó confirmada.
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Complejo:</strong> {{ $reservation->field->venue->name }}</p>
    <p><strong>Cancha:</strong> {{ $reservation->field->name }}</p>
    <p><strong>Fecha:</strong> {{ $reservation->start_at->format('d/m/Y') }}</p>
    <p><strong>Horario:</strong> {{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</p>
    <p><strong>Monto:</strong> {{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</p>

    @if($reservation->verification_code)
      <p style="margin-top:18px;">
        <strong>Código de verificación:</strong><br>
        <span style="display:inline-block; margin-top:6px; font-size:24px; font-weight:800; letter-spacing:.08em;">
          {{ $reservation->verification_code }}
        </span>
      </p>
    @endif

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666;">
      Gracias por usar TuCancha.
    </p>
  </div>
</body>
</html>