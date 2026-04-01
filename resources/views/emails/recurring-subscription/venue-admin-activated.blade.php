<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nueva suscripción mensual en tu complejo</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0; color:#16a34a;">Nueva suscripción mensual</h1>

    <p>Un jugador activó una suscripción mensual en <strong>{{ $subscription->field->venue->name }}</strong>.</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Jugador:</strong> {{ $subscription->user->name }} ({{ $subscription->user->email }})</p>
    <p><strong>Cancha:</strong> {{ $subscription->field->name }}</p>
    <p><strong>Turno:</strong> {{ $subscription->dayLabel() }} a las {{ $subscription->start_time }}</p>
    <p><strong>Frecuencia:</strong> {{ $subscription->frequencyLabel() }}</p>
    <p><strong>Monto mensual:</strong> ${{ number_format($subscription->monthly_amount, 0, ',', '.') }} {{ $subscription->currency }}</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p>
      <a href="{{ route('va.recurring_subscriptions.index') }}"
         style="display:inline-block; background:#16a34a; color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:700;">
        Ver suscripciones
      </a>
    </p>

    <p style="margin-bottom:0; color:#666;">TuCancha</p>
  </div>
</body>
</html>
