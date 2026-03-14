@extends('layouts.app')
@section('title', 'Pago rechazado')

@section('content')
<div class="page-card" style="max-width:600px; margin:auto; text-align:center;">
  <h1 style="font-size:32px; margin-bottom:10px;">❌ Pago no aprobado</h1>
  <p class="muted">El pago fue rechazado por Mercado Pago. Tus reservas siguen pendientes por 30 minutos.</p>

  <div style="margin-top:24px; display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
    <a href="{{ route('batches.checkout', $batch) }}" class="btn btn-primary">Intentar de nuevo</a>
    <a href="{{ route('my_reservations') }}" class="btn">Ver mis reservas</a>
  </div>
</div>
@endsection
