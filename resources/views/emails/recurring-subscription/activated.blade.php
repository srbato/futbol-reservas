<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Tu suscripción mensual fue activada</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0; color:#16a34a;">Suscripción mensual activada</h1>

    <p>Hola {{ $subscription->user->name }},</p>

    <p>Tu suscripción mensual en <strong>{{ $subscription->field->venue->name }}</strong> - <strong>{{ $subscription->field->name }}</strong> fue activada correctamente.</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Turno:</strong> {{ $subscription->dayLabel() }} a las {{ $subscription->start_time }} ({{ $subscription->frequencyLabel() }})</p>
    <p><strong>Monto mensual:</strong> ${{ number_format($subscription->monthly_amount, 0, ',', '.') }} {{ $subscription->currency }}</p>

    <p style="color:#555; font-size:14px;">MercadoPago cobrará <strong>${{ number_format($subscription->monthly_amount, 0, ',', '.') }} {{ $subscription->currency }}</strong> por mes automáticamente.</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Próximos turnos estimados:</strong></p>

    <table style="width:100%; border-collapse:collapse; margin-top:8px;">
      <thead>
        <tr style="background:#f3f3f3;">
          <th style="text-align:left; padding:8px;">Fecha</th>
          <th style="text-align:left; padding:8px;">Horario</th>
        </tr>
      </thead>
      <tbody>
        @foreach($subscription->nextOccurrences(4) as $date)
          <tr>
            <td style="padding:8px; border-bottom:1px solid #eee;">{{ $date->format('d/m/Y') }}</td>
            <td style="padding:8px; border-bottom:1px solid #eee;">{{ $date->format('H:i') }} hs</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="color:#555;">Podés cancelar tu suscripción en cualquier momento desde tu panel de reservas.</p>

    <p>
      <a href="{{ route('my_reservations') }}"
         style="display:inline-block; background:#16a34a; color:#fff; text-decoration:none; padding:10px 20px; border-radius:8px; font-weight:700;">
        Ver mis reservas
      </a>
    </p>

    <p style="margin-bottom:0; color:#666;">Gracias por usar TuCancha.</p>
  </div>
</body>
</html>
