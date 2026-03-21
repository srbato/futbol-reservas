<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tu partido venció sin completarse</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0; color:#f59e0b;">⏱ Tu partido no se completó a tiempo</h1>

    <p>Hola {{ $game->initiator->name }},</p>

    <p>
      Tu partido no alcanzó la cantidad de jugadores necesarios antes del límite de tiempo establecido
      por el complejo. El slot quedó liberado y está disponible para reservas normales.
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Complejo:</strong> {{ $game->field->venue->name }}</p>
    <p><strong>Cancha:</strong> {{ $game->field->name }}</p>
    <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y \a \l\a\s H:i') }} hs</p>
    <p><strong>Jugadores anotados:</strong> {{ $game->activeParticipants()->count() }} / {{ $game->players_needed }}</p>

    <p style="margin-top:16px; color:#dc2626; font-weight:700;">
      De acuerdo a la política del complejo, no corresponde reembolso por el vencimiento del plazo.
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666;">Gracias por usar TuCancha.</p>
  </div>
</body>
</html>
