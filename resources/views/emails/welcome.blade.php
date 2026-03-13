<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Bienvenido a TuCancha</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">⚽ ¡Bienvenido a TuCancha!</h1>

    <p>Hola {{ $user->name }},</p>

    <p>
      Tu cuenta fue creada con éxito. Ya podés explorar complejos deportivos,
      ver disponibilidad en tiempo real y reservar tu cancha en segundos.
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>¿Qué podés hacer en TuCancha?</strong></p>
    <ul style="padding-left:20px; line-height:2;">
      <li>Explorar complejos deportivos cerca tuyo</li>
      <li>Ver disponibilidad de canchas en tiempo real</li>
      <li>Reservar y pagar online en minutos</li>
      <li>Presentarte con tu código de verificación</li>
    </ul>

    <div style="margin:24px 0;">
      <a href="{{ url('/venues') }}"
         style="display:inline-block; padding:12px 24px; background:#111; color:#fff; border-radius:10px; text-decoration:none; font-weight:700;">
        Explorar canchas
      </a>
    </div>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666;">
      Gracias por unirte a TuCancha.
    </p>
  </div>
</body>
</html>