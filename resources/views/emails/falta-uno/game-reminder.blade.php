<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tu partido es en 2 horas</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0; color:#22c55e;">⚽ Tu partido es en 2 horas</h1>

    <p>Hola {{ $recipient->name }},</p>

    <p>Te recordamos que en aproximadamente <strong>2 horas</strong> tenés partido.</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Complejo:</strong> {{ $game->field->venue->name }}</p>
    <p><strong>Cancha:</strong> {{ $game->field->name }}</p>
    <p><strong>Fecha y hora:</strong> {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y \a \l\a\s H:i') }} hs</p>
    @if($game->field->venue->address)
    <p><strong>Dirección:</strong> {{ $game->field->venue->address }}</p>
    @endif

    <p style="margin-top:16px; color:#666;">Acordate de llegar a tiempo. ¡Buen partido!</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666;">Gracias por usar TuCancha.</p>
  </div>
</body>
</html>
