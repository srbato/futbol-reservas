@extends('layouts.app')
@section('title', 'Esperando pago')

@section('content')
<div class="page-card" style="max-width:600px; margin:auto; text-align:center;">
  <h1 style="font-size:32px; margin-bottom:10px;">⏳ Procesando pago</h1>
  <p class="muted">Mercado Pago está procesando tu pago. Esto puede tardar unos instantes.</p>

  <div style="margin:24px 0; display:flex; align-items:center; justify-content:center; gap:10px; color:#9a6700; font-weight:600;">
    <span id="dot" style="width:12px; height:12px; border-radius:999px; background:#f0ad00; display:inline-block; animation:pulse 1s infinite;"></span>
    <span id="pollingText">Verificando...</span>
  </div>

  <a href="{{ route('my_reservations') }}" class="btn">Ver mis reservas</a>
</div>

<style>@keyframes pulse { 0%, 100% { opacity:1; } 50% { opacity:.3; } }</style>
<script>
  const batchId = {{ $batch->id }};
  let attempts = 0;

  function checkStatus() {
    if (attempts >= 20) {
      document.getElementById('pollingText').innerText = 'No se pudo confirmar. Revisá tus reservas en unos minutos.';
      document.getElementById('dot').style.background = '#842029';
      return;
    }
    attempts++;
    fetch('/batches/' + batchId + '/status')
      .then(r => r.json())
      .then(data => {
        if (data.status === 'PAID') {
          window.location.href = '/batch-success/{{ $batch->id }}';
        } else {
          setTimeout(checkStatus, 3000);
        }
      })
      .catch(() => setTimeout(checkStatus, 3000));
  }

  setTimeout(checkStatus, 3000);
</script>
@endsection
