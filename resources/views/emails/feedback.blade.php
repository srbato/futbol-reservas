<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Nuevo feedback — TuCancha</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background: #f7f7f8;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color: #111;
    }
    .wrap {
      max-width: 560px;
      margin: 40px auto;
      background: #fff;
      border: 1px solid #ececec;
      border-radius: 16px;
      overflow: hidden;
    }
    .header {
      background: #111;
      padding: 28px 32px;
    }
    .header h1 {
      margin: 0;
      color: #fff;
      font-size: 20px;
      font-weight: 700;
      letter-spacing: -0.01em;
    }
    .header p {
      margin: 6px 0 0 0;
      color: rgba(255,255,255,.6);
      font-size: 13px;
    }
    .body {
      padding: 28px 32px;
    }
    .label {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .07em;
      color: #888;
      margin-bottom: 8px;
    }
    .message-box {
      background: #f7f7f8;
      border: 1px solid #ececec;
      border-radius: 10px;
      padding: 16px 18px;
      font-size: 15px;
      line-height: 1.7;
      color: #222;
      white-space: pre-wrap;
      word-break: break-word;
    }
    .sender {
      margin-top: 20px;
      font-size: 14px;
      color: #555;
    }
    .sender strong {
      color: #111;
    }
    .footer {
      padding: 16px 32px;
      border-top: 1px solid #ececec;
      font-size: 12px;
      color: #aaa;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>Nuevo feedback recibido</h1>
      <p>TuCancha — {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <div class="body">
      <div class="label">Mensaje</div>
      <div class="message-box">{{ $feedbackMessage }}</div>

      @if($senderEmail)
        <div class="sender">
          Enviado por: <strong>{{ $senderEmail }}</strong>
        </div>
      @else
        <div class="sender">Enviado de forma anónima.</div>
      @endif
    </div>
    <div class="footer">
      Este mensaje fue enviado desde el formulario de feedback de TuCancha.
    </div>
  </div>
</body>
</html>
