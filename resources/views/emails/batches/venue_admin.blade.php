<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nuevas reservas recurrentes</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">📅 Nuevas reservas recurrentes</h1>

    <p>Se confirmaron reservas recurrentes en tu cancha <strong>{{ $batch->field->name }}</strong> ({{ $batch->field->venue->name }}).</p>

    <p><strong>Cliente:</strong> {{ $batch->user->name }} ({{ $batch->user->email }})</p>

    <table style="width:100%; border-collapse:collapse; margin-top:12px;">
      <thead>
        <tr style="background:#f3f3f3;">
          <th style="text-align:left; padding:8px;">Fecha</th>
          <th style="text-align:left; padding:8px;">Horario</th>
        </tr>
      </thead>
      <tbody>
        @foreach($batch->reservations->sortBy('start_at') as $r)
          <tr>
            <td style="padding:8px; border-bottom:1px solid #eee;">{{ $r->start_at->format('d/m/Y') }}</td>
            <td style="padding:8px; border-bottom:1px solid #eee;">{{ $r->start_at->format('H:i') }} - {{ $r->end_at->format('H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p><strong>Total cobrado:</strong> {{ $batch->currency }} {{ number_format($batch->total_amount, 0, ',', '.') }}</p>
    @if($batch->discount_percentage > 0)
      <p style="color:#9a6700; font-size:13px;">Se aplicó un descuento por reserva recurrente del {{ $batch->discount_percentage }}%.</p>
    @endif
  </div>
</body>
</html>
