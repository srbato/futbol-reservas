<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Invitación de staff</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; padding:24px;">
    <h1 style="margin-top:0;">👋 Te invitaron a trabajar en {{ $invitation->venue->name }}</h1>

    <p>Hola {{ $invitation->user->name }},</p>

    <p>
      <strong>{{ $invitation->invitedBy->name }}</strong> te invitó a unirte como empleado del complejo
      <strong>{{ $invitation->venue->name }}</strong>.
      Como empleado vas a poder gestionar reservas, agenda, canchas y bloqueos desde el panel de administración.
    </p>

    <p>La invitación vence en 48 horas.</p>

    <div style="margin:28px 0; display:flex; gap:12px;">
      <a href="{{ route('staff.invitations.accept', $invitation->token) }}"
         style="display:inline-block; background:#111; color:#fff; padding:12px 24px; border-radius:10px; text-decoration:none; font-weight:700; margin-right:10px;">
        Aceptar invitación
      </a>
      <a href="{{ route('staff.invitations.reject', $invitation->token) }}"
         style="display:inline-block; background:#f1f1f1; color:#333; padding:12px 24px; border-radius:10px; text-decoration:none; font-weight:700;">
        Rechazar
      </a>
    </div>

    <p style="font-size:13px; color:#888;">
      Si no esperabas esta invitación podés ignorar este email.
    </p>

    <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

    <p style="margin-bottom:0; color:#666; font-size:13px;">
      TuCancha
    </p>
  </div>
</body>
</html>
