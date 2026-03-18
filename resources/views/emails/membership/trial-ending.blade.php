<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tu período de prueba vence hoy</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">🎁 Tu período de prueba vence hoy</h1>

    <p>
      Hola {{ $subscription->user->name }},
    </p>

    <p>
      Tu período de prueba gratuito en TuCancha vence hoy. A partir de ahora <strong>perderás el acceso al panel de administración</strong> si no contratás un plan.
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Plan probado:</strong> {{ $subscription->plan_slug }}</p>
    <p><strong>Período de prueba venció:</strong> {{ $subscription->expires_at?->format('d/m/Y H:i') }}</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p>
      Para seguir administrando tus complejos, contratá un plan desde TuCancha. El proceso es rápido y podés elegir pago mensual o por período largo con descuento.
    </p>

    <p style="margin-bottom:0; color:#666;">
      Gracias por haber probado TuCancha. ¡Esperamos verte como socio!
    </p>
  </div>
</body>
</html>
