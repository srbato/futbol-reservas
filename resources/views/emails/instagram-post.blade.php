<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Post para Instagram listo</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px;">
  <div style="max-width:640px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; overflow:hidden;">

    <div style="background:#111; padding:24px 28px; color:#fff;">
      <h1 style="margin:0; font-size:20px; font-weight:800;">Post para Instagram</h1>
      <p style="margin:6px 0 0; font-size:13px; color:rgba(255,255,255,0.55);">Tema: {{ ucfirst($topic) }} — Listo para publicar</p>
    </div>

    <div style="padding:24px 28px;">

      <p style="font-size:13px; color:#666; margin:0 0 14px; font-weight:700;">Imagen generada:</p>
      <img src="{{ $imageUrl }}" alt="Post Instagram" style="width:100%; border-radius:12px; margin-bottom:20px; aspect-ratio:1/1; object-fit:cover;">
      <p style="font-size:12px; color:#999; margin:0 0 20px;">La imagen tambien esta adjunta al mail para descargarla directo.</p>

      <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

      <p style="font-size:13px; color:#666; margin:0 0 8px; font-weight:700;">Caption (copia y pega en Instagram):</p>
      <div style="background:#f5f5f5; border-radius:10px; padding:16px; font-size:14px; line-height:1.7; white-space:pre-wrap; font-family:inherit; border:1px solid #e5e5e5;">{{ $caption }}</div>

      <hr style="border:none; border-top:1px solid #eee; margin:20px 0;">

      <p style="color:#999; font-size:12px; margin:0; text-align:center;">
        Descarga la imagen adjunta, abri Instagram, subi la foto y pega el caption. 30 segundos.
      </p>
    </div>
  </div>
</body>
</html>
