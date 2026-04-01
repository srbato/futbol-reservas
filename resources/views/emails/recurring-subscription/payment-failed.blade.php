<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tu pago mensual no pudo procesarse</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0; color:#dc2626;">Pago mensual fallido</h1>

    <p>Hola {{ $subscription->user->name }},</p>

    <p>No pudimos procesar el cobro mensual de <strong>${{ number_format($subscription->monthly_amount, 0, ',', '.') }} {{ $subscription->currency }}</strong> de tu suscripción en <strong>{{ $subscription->field->venue->name }}</strong>.</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Complejo:</strong> {{ $subscription->field->venue->name }}</p>
    <p><strong>Cancha:</strong> {{ $subscription->field->name }}</p>
    <p><strong>Turno:</strong> {{ $subscription->dayLabel() }} a las {{ $subscription->start_time }} ({{ $subscription->frequencyLabel() }})</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="color:#555;">MercadoPago intentará nuevamente en los próximos días. Si el problema persiste, la suscripción se cancelará automáticamente.</p>

    <p style="color:#555;">Podés actualizar tu método de pago directamente en MercadoPago.</p>

    <p>
      <a href="{{ route('my_reservations') }}"
         style="display:inline-block; background:#2563eb; color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:700;">
        Ver mis suscripciones
      </a>
    </p>

    <p style="margin-bottom:0; color:#666;">Gracias por usar TuCancha.</p>
  </div>
</body>
</html>
