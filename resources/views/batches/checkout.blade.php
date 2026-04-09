@extends('layouts.app')
@section('title', 'Pagar reservas recurrentes')

@section('content')
<div style="max-width:760px; margin:auto;">

  <div class="page-card" style="margin-bottom:20px;">
    <h1 style="margin:0 0 6px 0; font-size:32px; letter-spacing:-0.02em;">Reservas recurrentes</h1>
    <p class="muted" style="margin:0;">
      {{ $batch->field->name }} — {{ $batch->field->venue->name }}
    </p>
  </div>

  @if(session('error'))
    <div class="page-card" style="background:#f8d7da; border-color:#f1b9c0; color:#842029; margin-bottom:16px;">
      <p style="margin:0;">{{ session('error') }}</p>
    </div>
  @endif

  @if($batch->status === 'PAID')
    <div class="page-card" style="background:#e8f7ee; border-color:#cfe9d7; text-align:center; margin-bottom:20px;">
      <h2 style="margin:0 0 8px 0; color:#157347;">✅ Pago confirmado</h2>
      <p class="muted" style="margin:0;">Tus reservas ya están confirmadas. Podés ver los códigos en Mis Reservas.</p>
    </div>
  @elseif($batch->expires_at && $batch->expires_at->isPast())
    <div class="page-card" style="background:#f8d7da; border-color:#f1b9c0; text-align:center; margin-bottom:20px;">
      <h2 style="margin:0 0 8px 0; color:#842029;">⏰ Lote expirado</h2>
      <p class="muted" style="margin:0;">El tiempo para pagar expiró. Las reservas fueron liberadas.</p>
    </div>
  @else
    <div class="page-card" style="background:#fff4db; border-color:#f5d48a; margin-bottom:16px;">
      <p style="margin:0; color:#9a6700; font-weight:600;">
        ⏳ Tenés <span id="countdown" style="font-weight:800;"></span> para completar el pago.
      </p>
    </div>
  @endif

  {{-- Listado de turnos --}}
  <div class="page-card" style="margin-bottom:20px;">
    <h2 style="margin:0 0 16px 0; font-size:20px;">Turnos incluidos</h2>

    <div style="overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left; padding:10px 8px; border-bottom:2px solid #eee; font-size:13px; color:#666;">Fecha</th>
          <th style="text-align:left; padding:10px 8px; border-bottom:2px solid #eee; font-size:13px; color:#666;">Horario</th>
          <th style="text-align:right; padding:10px 8px; border-bottom:2px solid #eee; font-size:13px; color:#666;">Precio</th>
        </tr>
      </thead>
      <tbody>
        @foreach($batch->reservations->sortBy('start_at') as $r)
          <tr>
            <td style="padding:10px 8px; border-bottom:1px solid #f3f3f3; text-transform:capitalize;">
              {{ $r->start_at->locale('es')->isoFormat('dddd D/MM/YYYY') }}
            </td>
            <td style="padding:10px 8px; border-bottom:1px solid #f3f3f3;">
              {{ $r->start_at->format('H:i') }} – {{ $r->end_at->format('H:i') }}
            </td>
            <td style="padding:10px 8px; border-bottom:1px solid #f3f3f3; text-align:right;">
              {{ $batch->currency }} {{ number_format($r->total_amount, 0, ',', '.') }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    </div>
  </div>

  {{-- Resumen de precio --}}
  <div class="page-card" style="margin-bottom:20px;">
    <h2 style="margin:0 0 16px 0; font-size:20px;">Resumen</h2>

    <div style="display:flex; flex-direction:column; gap:10px;">
      <div style="display:flex; justify-content:space-between;">
        <span class="muted">Subtotal ({{ $batch->reservations->count() }} turnos)</span>
        <span>{{ $batch->currency }} {{ number_format($batch->subtotal, 0, ',', '.') }}</span>
      </div>

      @if($batch->discount_percentage > 0)
        <div style="display:flex; justify-content:space-between; color:#157347; font-weight:700;">
          <span>🔥 Descuento por reserva recurrente ({{ $batch->discount_percentage }}%)</span>
          <span>– {{ $batch->currency }} {{ number_format($batch->discount_amount, 0, ',', '.') }}</span>
        </div>
      @endif

      <div style="display:flex; justify-content:space-between; font-size:22px; font-weight:800; border-top:2px solid #eee; padding-top:12px; margin-top:4px;">
        <span>Total</span>
        <span>{{ $batch->currency }} {{ number_format($batch->total_amount, 0, ',', '.') }}</span>
      </div>
    </div>
  </div>

  {{-- Acción de pago --}}
  @if($batch->status === 'PENDING_PAYMENT' && (!$batch->expires_at || $batch->expires_at->isFuture()))
    <div style="display:flex; justify-content:center;">
      <form method="POST" action="{{ route('batches.mercadopago', $batch) }}">
        @csrf
        <button type="submit" class="btn btn-primary" style="font-size:16px; padding:14px 32px;">
          Pagar con Mercado Pago
        </button>
      </form>
    </div>
  @elseif($batch->status === 'PAID')
    <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap;">
      <a href="{{ route('my_reservations') }}" class="btn btn-primary">Ver mis reservas</a>
    </div>
  @endif

</div>

@if($batch->status === 'PENDING_PAYMENT' && $batch->expires_at && $batch->expires_at->isFuture())
<script>
  const expiresAt = new Date('{{ $batch->expires_at->toIso8601String() }}');

  function updateCountdown() {
    const diff = Math.max(0, Math.floor((expiresAt - new Date()) / 1000));
    const m = Math.floor(diff / 60);
    const s = diff % 60;
    const el = document.getElementById('countdown');
    if (el) el.textContent = m + ':' + String(s).padStart(2, '0');
    if (diff === 0) location.reload();
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
</script>
@endif
@endsection
