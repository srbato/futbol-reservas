@extends('layouts.app')
@section('title', 'Reservas confirmadas')

@section('content')
<div class="page-card" style="max-width:760px; margin:auto;">

  @if($batch->status === 'PAID')
    <div style="text-align:center; margin-bottom:24px;">
      <h1 style="font-size:34px; margin-bottom:10px;">✅ ¡Reservas confirmadas!</h1>
      <p class="muted" style="margin:0;">Tu pago fue aprobado. Todos tus turnos están reservados.</p>
    </div>
  @else
    <div style="text-align:center; margin-bottom:24px;">
      <h1 style="font-size:34px; margin-bottom:10px;">⏳ Esperando confirmación</h1>
      <p class="muted" style="margin:0;">Estamos esperando la confirmación de Mercado Pago.</p>
      <div style="margin-top:16px; display:flex; align-items:center; justify-content:center; gap:10px; color:#9a6700; font-weight:600;">
        <span style="width:10px; height:10px; border-radius:999px; background:#f0ad00; display:inline-block; animation:pulse 1s infinite;"></span>
        <span id="pollingText">Verificando estado...</span>
      </div>
    </div>
  @endif

  <div class="page-card" style="margin-bottom:18px;">
    <p style="margin:0 0 8px 0;"><strong>Complejo:</strong> {{ $batch->field->venue->name }}</p>
    <p style="margin:0 0 8px 0;"><strong>Cancha:</strong> {{ $batch->field->name }}</p>
    <p style="margin:0 0 8px 0;"><strong>Turnos:</strong> {{ $batch->reservations->count() }}</p>
    @if($batch->discount_percentage > 0)
      <p style="margin:0 0 8px 0;"><strong>Descuento aplicado:</strong> {{ $batch->discount_percentage }}%</p>
    @endif
    <p style="margin:0;"><strong>Total:</strong> {{ $batch->currency }} {{ number_format($batch->total_amount, 0, ',', '.') }}</p>
  </div>

  @if($batch->status === 'PAID')
    <div class="page-card" style="margin-bottom:18px;">
      <h3 style="margin:0 0 12px 0;">Tus turnos</h3>
      @foreach($batch->reservations->sortBy('start_at') as $r)
        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #f3f3f3; flex-wrap:wrap; gap:8px;">
          <span style="text-transform:capitalize;">{{ $r->start_at->locale('es')->isoFormat('dddd D/MM/YYYY') }} — {{ $r->start_at->format('H:i') }}</span>
          <span style="font-weight:800; letter-spacing:.05em;">{{ $r->verification_code }}</span>
        </div>
      @endforeach
    </div>
  @endif

  <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
    <a href="{{ route('venues.index') }}" class="btn">Volver a complejos</a>
    <a href="{{ route('my_reservations') }}" class="btn btn-primary">Ver mis reservas</a>
  </div>
</div>

@if($batch->status !== 'PAID')
<style>@keyframes pulse { 0%, 100% { opacity:1; } 50% { opacity:.3; } }</style>
<script>
  const batchId = {{ $batch->id }};
  let attempts = 0;

  function checkStatus() {
    if (attempts >= 20) {
      document.getElementById('pollingText').innerText = 'No se pudo confirmar. Revisá tus reservas.';
      return;
    }
    attempts++;
    fetch('/batches/' + batchId + '/status')
      .then(r => r.json())
      .then(data => {
        if (data.status === 'PAID') {
          window.location.reload();
        } else {
          setTimeout(checkStatus, 3000);
        }
      })
      .catch(() => setTimeout(checkStatus, 3000));
  }

  setTimeout(checkStatus, 3000);
</script>
@endif
@endsection
