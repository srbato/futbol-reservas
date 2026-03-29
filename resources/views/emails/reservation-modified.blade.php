<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reserva modificada</title>
  <style>
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; margin: 0; padding: 24px; color: #111; }
    .container { max-width: 560px; margin: auto; background: #fff; border-radius: 16px; overflow: hidden; border: 1px solid #e8e8e8; }
    .header { background: #111; color: #fff; padding: 28px 32px; }
    .header h1 { margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.01em; }
    .header p { margin: 6px 0 0; font-size: 14px; opacity: .7; }
    .body { padding: 28px 32px; }
    .row { display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
    .row:last-child { border-bottom: none; }
    .row .label { color: #666; }
    .row .value { font-weight: 700; text-align: right; }
    .section-title { font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em; color: #aaa; margin: 20px 0 10px; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
    .badge-green { background: #d1e7dd; color: #0f5132; }
    .badge-yellow { background: #fff3cd; color: #856404; }
    .footer { padding: 20px 32px; background: #fafafa; border-top: 1px solid #f0f0f0; font-size: 12px; color: #888; }
    .btn { display: inline-block; background: #111; color: #fff; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 14px; margin-top: 20px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>Reserva modificada</h1>
      <p>Reserva #{{ $reservation->id }} — TuCancha</p>
    </div>

    <div class="body">
      <p style="margin:0 0 20px; font-size:15px; line-height:1.6;">
        Hola <strong>{{ $reservation->user->name }}</strong>, tu reserva fue modificada exitosamente.
      </p>

      <div class="section-title">Nuevo horario</div>
      <div class="row"><span class="label">Complejo</span><span class="value">{{ $reservation->field->venue->name }}</span></div>
      <div class="row"><span class="label">Cancha</span><span class="value">{{ $reservation->field->name }}</span></div>
      <div class="row"><span class="label">Fecha</span><span class="value">{{ $reservation->start_at->format('d/m/Y') }}</span></div>
      <div class="row"><span class="label">Horario</span><span class="value">{{ $reservation->start_at->format('H:i') }} – {{ $reservation->end_at->format('H:i') }}</span></div>
      <div class="row"><span class="label">Total</span><span class="value">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</span></div>

      @if($refundAmount > 0)
        <div class="section-title">Reembolso</div>
        @if($refundResult === true)
          <p style="font-size:14px; line-height:1.6; color:#0f5132; background:#d1e7dd; padding:12px 16px; border-radius:10px; margin:0;">
            Se procesó un reembolso de <strong>{{ $reservation->currency }} {{ number_format($refundAmount, 0, ',', '.') }}</strong>
            a tu cuenta de MercadoPago por la diferencia de precio.
          </p>
        @else
          <p style="font-size:14px; line-height:1.6; color:#856404; background:#fff3cd; padding:12px 16px; border-radius:10px; margin:0;">
            Hay un reembolso de <strong>{{ $reservation->currency }} {{ number_format($refundAmount, 0, ',', '.') }}</strong> pendiente.
            No pudimos procesarlo automáticamente — por favor contactá a soporte.
          </p>
        @endif
      @endif

      <a href="{{ route('reservations.show', $reservation) }}" class="btn">Ver mi reserva</a>
    </div>

    <div class="footer">
      Este email fue enviado automáticamente por TuCancha. Si no realizaste este cambio, contactanos de inmediato.
    </div>
  </div>
</body>
</html>
