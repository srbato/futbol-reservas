<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>¡Ganaste una recompensa!</title>
</head>
<body style="margin:0; padding:0; background:#f7f7f8; font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color:#111;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f7f7f8; padding:40px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#fff; border-radius:18px; border:1px solid #ececec; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,.06);">

          <!-- Header -->
          <tr>
            <td style="background:#111; padding:28px 32px;">
              <div style="font-size:22px; font-weight:800; color:#fff; letter-spacing:-0.02em;">TuCancha</div>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:32px;">

              <!-- Icon + heading -->
              <div style="font-size:48px; text-align:center; margin-bottom:16px;">🎁</div>
              <h1 style="margin:0 0 10px 0; font-size:28px; font-weight:800; letter-spacing:-0.02em; text-align:center;">
                ¡Ganaste una recompensa!
              </h1>
              <p style="margin:0 0 28px 0; color:#666; font-size:15px; line-height:1.7; text-align:center;">
                Hola, <strong style="color:#111;">{{ $referrer->name }}</strong>.
                Alguien que invitaste acaba de activar su suscripción en TuCancha.
              </p>

              <!-- Reward box -->
              <div style="background:#f0fdf4; border:2px solid #4ade80; border-radius:14px; padding:20px 24px; margin-bottom:28px;">
                <div style="font-size:13px; font-weight:700; color:#166534; text-transform:uppercase; letter-spacing:.04em; margin-bottom:8px;">
                  Tu recompensa disponible
                </div>
                <div style="font-size:17px; color:#166534; line-height:1.6;">
                  Podés usar esta recompensa para:
                </div>
                <ul style="margin:10px 0 0 0; padding-left:20px; color:#166534; font-size:15px; line-height:1.8;">
                  <li><strong>Una reserva gratis</strong> — pagá cualquier cancha sin costo.</li>
                  <li>
                    <strong>Un mes gratis</strong> — extendé tu membresía 30 días
                    <span style="font-size:13px; color:#4ade80;">(si sos administrador de complejo)</span>.
                  </li>
                </ul>
              </div>

              <!-- Referred user info -->
              <div style="background:#f7f7f8; border:1px solid #ececec; border-radius:12px; padding:16px 20px; margin-bottom:28px;">
                <div style="font-size:12px; color:#999; text-transform:uppercase; letter-spacing:.04em; margin-bottom:6px;">Referido activado</div>
                <div style="font-size:15px; font-weight:600; color:#111;">{{ $reward->referred->name }}</div>
                <div style="font-size:13px; color:#666;">{{ $reward->referred->email }}</div>
              </div>

              <!-- CTA button -->
              <div style="text-align:center; margin-bottom:28px;">
                <a href="{{ route('referral.index') }}"
                   style="display:inline-block; padding:14px 28px; background:#111; color:#fff; text-decoration:none; border-radius:12px; font-weight:700; font-size:15px; letter-spacing:-.01em;">
                  Canjear mi recompensa
                </a>
              </div>

              <!-- Divider -->
              <div style="border-top:1px solid #eee; margin-bottom:20px;"></div>

              <!-- Footer note -->
              <p style="margin:0; font-size:13px; color:#999; line-height:1.6; text-align:center;">
                Este correo fue generado automáticamente por TuCancha.<br>
                Si no esperabas este mensaje, podés ignorarlo.
              </p>

            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="background:#fafafa; border-top:1px solid #ececec; padding:18px 32px; text-align:center;">
              <div style="font-size:13px; color:#999;">
                © {{ date('Y') }} TuCancha — Todos los derechos reservados.
              </div>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
