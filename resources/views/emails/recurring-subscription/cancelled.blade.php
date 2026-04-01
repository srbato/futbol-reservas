<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tu suscripción mensual fue cancelada</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0; color:#f59e0b;">Suscripción mensual cancelada</h1>

    <p>Hola {{ $subscription->user->name }},</p>

    <p>Tu suscripción para el turno del <strong>{{ $subscription->dayLabel() }}</strong> a las <strong>{{ $subscription->start_time }}</strong> en <strong>{{ $subscription->field->venue->name }}</strong> fue cancelada.</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    @if($subscription->cancel_reason === 'venue_cancelled')
      <p style="color:#555;">El complejo canceló la suscripción. Contactalos para más información.</p>
    @elseif($subscription->cancel_reason === 'user_requested')
      <p style="color:#555;">Cancelaste la suscripción correctamente.</p>
    @endif

    <p style="color:#555;">Los turnos futuros ya generados fueron cancelados.</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p>
      <a href="{{ route('my_reservations') }}"
         style="display:inline-block; background:#111; color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:700;">
        Ver mis reservas
      </a>
    </p>

    <p style="margin-bottom:0; color:#666;">Gracias por usar TuCancha.</p>
  </div>
</body>
</html>
