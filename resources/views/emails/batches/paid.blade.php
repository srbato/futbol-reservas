<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Reservas recurrentes confirmadas</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">✅ Reservas recurrentes confirmadas</h1>

    <p>Hola {{ $batch->user->name }},</p>

    <p>Tu pago fue aprobado. Tus turnos recurrentes quedaron reservados:</p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Complejo:</strong> {{ $batch->field->venue->name }}</p>
    <p><strong>Cancha:</strong> {{ $batch->field->name }}</p>

    <table style="width:100%; border-collapse:collapse; margin-top:12px;">
      <thead>
        <tr style="background:#f3f3f3;">
          <th style="text-align:left; padding:8px;">Fecha</th>
          <th style="text-align:left; padding:8px;">Horario</th>
          <th style="text-align:left; padding:8px;">Código</th>
        </tr>
      </thead>
      <tbody>
        @foreach($batch->reservations->sortBy('start_at') as $r)
          <tr>
            <td style="padding:8px; border-bottom:1px solid #eee;">{{ $r->start_at->format('d/m/Y') }}</td>
            <td style="padding:8px; border-bottom:1px solid #eee;">{{ $r->start_at->format('H:i') }} - {{ $r->end_at->format('H:i') }}</td>
            <td style="padding:8px; border-bottom:1px solid #eee; font-weight:700;">{{ $r->verification_code }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    @if($batch->discount_percentage > 0)
      <p><strong>Subtotal:</strong> {{ $batch->currency }} {{ number_format($batch->subtotal, 0, ',', '.') }}</p>
      <p><strong>Descuento ({{ $batch->discount_percentage }}%):</strong> -{{ $batch->currency }} {{ number_format($batch->discount_amount, 0, ',', '.') }}</p>
    @endif
    <p><strong>Total abonado:</strong> {{ $batch->currency }} {{ number_format($batch->total_amount, 0, ',', '.') }}</p>

    <p style="margin-bottom:0; color:#666;">Gracias por usar TuCancha.</p>
  </div>
</body>
</html>
