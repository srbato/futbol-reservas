<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Te extrañamos en la cancha</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0; font-size:22px;">Hace rato que no jugás</h1>

    <p>Hola {{ $user->name }},</p>

    <p>
      Vimos que hace un tiempo no reservás una cancha. Capaz se complicó la semana,
      capaz no encontraste horario. Sea lo que sea, las canchas te están esperando.
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Algunas ideas para esta semana:</strong></p>
    <ul style="padding-left:20px; line-height:2;">
      <li>Buscá canchas disponibles cerca tuyo</li>
      <li>Sumate a un partido en Falta Uno (otros jugadores te buscan)</li>
      <li>Mirá tu perfil deportivo y tus stats</li>
    </ul>

    <div style="margin:24px 0;">
      <a href="{{ url('/venues') }}"
         style="display:inline-block; padding:12px 24px; background:#22c55e; color:#fff; border-radius:10px; text-decoration:none; font-weight:700;">
        Ver canchas disponibles
      </a>
    </div>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666; font-size:13px;">
      Si no querés recibir estos mails, simplemente respondé "no más mails" y te sacamos de la lista.
    </p>
  </div>
</body>
</html>
