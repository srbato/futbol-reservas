<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Complejos inactivos</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0; font-size:20px;">Complejos sin reservas hace 7+ dias</h1>

    <p>Hay {{ $venues->count() }} complejo(s) activo(s) que no reciben reservas hace una semana o mas. Puede ser que tengan la agenda mal configurada o necesiten ayuda.</p>

    <table style="width:100%; border-collapse:collapse; font-size:14px; margin:20px 0;">
      <thead>
        <tr style="border-bottom:2px solid #eee; text-align:left;">
          <th style="padding:8px;">Complejo</th>
          <th style="padding:8px;">Dueño</th>
          <th style="padding:8px;">Email</th>
        </tr>
      </thead>
      <tbody>
        @foreach($venues as $venue)
        <tr style="border-bottom:1px solid #f4f4f4;">
          <td style="padding:8px; font-weight:700;">{{ $venue->name }}</td>
          <td style="padding:8px;">{{ $venue->owner->name ?? '—' }}</td>
          <td style="padding:8px;">{{ $venue->owner->email ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <p style="color:#666; font-size:13px;">Contactalos para ofrecerles ayuda con el setup o la configuracion de la agenda.</p>
  </div>
</body>
</html>
