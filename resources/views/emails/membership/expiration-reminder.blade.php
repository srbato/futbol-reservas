<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Recordatorio de membresía</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">
      @if($daysRemaining === 0)
        ⚠️ Tu membresía vence hoy
      @else
        ⏳ Tu membresía vence en {{ $daysRemaining }} {{ $daysRemaining === 1 ? 'día' : 'días' }}
      @endif
    </h1>

    <p>
      Hola {{ $subscription->user->name }},
    </p>

    <p>
      @if($daysRemaining === 0)
        Tu membresía de socio en TuCancha vence hoy.
      @else
        Te recordamos que tu membresía de socio en TuCancha está por vencer.
      @endif
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Estado actual:</strong> {{ $subscription->status }}</p>
    <p><strong>Vence el:</strong> {{ $subscription->expires_at?->format('d/m/Y H:i') }}</p>
    <p><strong>Último monto pagado:</strong> {{ $subscription->currency }} {{ number_format((float) $subscription->monthly_price, 2, ',', '.') }}</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p>
      Para seguir administrando tus complejos, renová tu membresía desde TuCancha.
    </p>

    <p style="margin-bottom:0; color:#666;">
      Gracias por usar TuCancha.
    </p>
  </div>
</body>
</html>