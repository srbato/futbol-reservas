<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Recordatorio de reserva</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">⏰ Tu reserva es en 2 horas</h1>

    <p>Hola {{ $reservation->user->name }},</p>

    <p>Te recordamos que tenés una reserva próximamente. ¡No llegues tarde!</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Complejo:</strong> {{ $reservation->field->venue->name }}</p>
    <p><strong>Cancha:</strong> {{ $reservation->field->name }}</p>
    <p><strong>Fecha:</strong> {{ $reservation->start_at->format('d/m/Y') }}</p>
    <p><strong>Horario:</strong> {{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</p>
    <p><strong>Dirección:</strong> {{ $reservation->field->venue->address }}</p>

    @if($reservation->verification_code)
      <p style="margin-top:18px;">
        <strong>Tu código de verificación:</strong><br>
        <span style="display:inline-block; margin-top:6px; font-size:24px; font-weight:800; letter-spacing:.08em;">
          {{ $reservation->verification_code }}
        </span>
      </p>
      <p style="font-size:13px; color:#666;">Presentá este código al llegar al complejo.</p>
    @endif

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666;">
      Gracias por usar TuCancha.
    </p>
  </div>
</body>
</html>