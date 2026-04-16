<!DOCTYPE html>
<html lang="es-AR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Error del servidor — TuCancha</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Sora', system-ui, sans-serif;
      background: #050505;
      color: #e8e8e8;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      -webkit-font-smoothing: antialiased;
    }
    .error-card {
      text-align: center;
      max-width: 480px;
      background: #111;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 20px;
      padding: 60px 32px;
    }
    .error-code {
      font-size: 80px;
      font-weight: 800;
      line-height: 1;
      letter-spacing: -0.04em;
      margin-bottom: 12px;
      background: linear-gradient(135deg, #22c55e 0%, #6eeaa0 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .error-title {
      font-size: 20px;
      font-weight: 600;
      color: #e8e8e8;
      margin-bottom: 8px;
    }
    .error-desc {
      font-size: 15px;
      color: #666;
      line-height: 1.6;
      margin-bottom: 32px;
    }
    .error-actions {
      display: flex;
      gap: 12px;
      justify-content: center;
      flex-wrap: wrap;
    }
    .error-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 10px 22px;
      border-radius: 12px;
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      font-family: 'Sora', sans-serif;
      transition: opacity .2s;
    }
    .error-btn:hover { opacity: .85; }
    .error-btn-primary {
      background: #22c55e;
      color: #050505;
    }
    .error-btn-ghost {
      background: rgba(255,255,255,.06);
      color: #a0a0a0;
      border: 1px solid rgba(255,255,255,.1);
    }
  </style>
</head>
<body>
  <div class="error-card">
    <div class="error-code">500</div>
    <div class="error-title">Algo salio mal</div>
    <p class="error-desc">
      Hubo un error en nuestro servidor. Estamos trabajando para solucionarlo. Intenta de nuevo en unos minutos.
    </p>
    <div class="error-actions">
      <a href="/" class="error-btn error-btn-primary">Volver al inicio</a>
      <a href="javascript:location.reload()" class="error-btn error-btn-ghost">Reintentar</a>
    </div>
  </div>
</body>
</html>
