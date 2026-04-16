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
    <div class="page-card" style="background:rgba(229,57,53,.1); border-color:rgba(229,57,53,.2); color:#f87171; margin-bottom:16px;">
      <p style="margin:0;">{{ session('error') }}</p>
    </div>
  @endif

  @if($batch->status === 'PAID')
    <div class="page-card" style="background:rgba(34,197,94,.1); border-color:rgba(34,197,94,.2); text-align:center; margin-bottom:20px;">
      <h2 style="margin:0 0 8px 0; color:#6ee7a0;">✅ Pago confirmado</h2>
      <p class="muted" style="margin:0;">Tus reservas ya están confirmadas. Podés ver los códigos en Mis Reservas.</p>
    </div>
  @elseif($batch->expires_at && $batch->expires_at->isPast())
    <div class="page-card" style="background:rgba(229,57,53,.1); border-color:rgba(229,57,53,.2); text-align:center; margin-bottom:20px;">
      <h2 style="margin:0 0 8px 0; color:#f87171;">⏰ Lote expirado</h2>
      <p class="muted" style="margin:0;">El tiempo para pagar expiró. Las reservas fueron liberadas.</p>
    </div>
  @else
    <div class="page-card" style="background:rgba(245,158,11,.08); border-color:rgba(245,158,11,.2); margin-bottom:16px;">
      <p style="margin:0; color:#fbbf24; font-weight:600;">
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
          <th style="text-align:left; padding:10px 8px; border-bottom:2px solid rgba(255,255,255,.08); font-size:13px; color:#a0a0a0;">Fecha</th>
          <th style="text-align:left; padding:10px 8px; border-bottom:2px solid rgba(255,255,255,.08); font-size:13px; color:#a0a0a0;">Horario</th>
          <th style="text-align:right; padding:10px 8px; border-bottom:2px solid rgba(255,255,255,.08); font-size:13px; color:#a0a0a0;">Precio</th>
        </tr>
      </thead>
      <tbody>
        @foreach($batch->reservations->sortBy('start_at') as $r)
          <tr>
            <td style="padding:10px 8px; border-bottom:1px solid rgba(255,255,255,.06); text-transform:capitalize;">
              {{ $r->start_at->locale('es')->isoFormat('dddd D/MM/YYYY') }}
            </td>
            <td style="padding:10px 8px; border-bottom:1px solid rgba(255,255,255,.06);">
              {{ $r->start_at->format('H:i') }} – {{ $r->end_at->format('H:i') }}
            </td>
            <td style="padding:10px 8px; border-bottom:1px solid rgba(255,255,255,.06); text-align:right;">
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
        <div style="display:flex; justify-content:space-between; color:#6ee7a0; font-weight:700;">
          <span>🔥 Descuento por reserva recurrente ({{ $batch->discount_percentage }}%)</span>
          <span>– {{ $batch->currency }} {{ number_format($batch->discount_amount, 0, ',', '.') }}</span>
        </div>
      @endif

      <div style="display:flex; justify-content:space-between; font-size:22px; font-weight:800; border-top:2px solid rgba(255,255,255,.08); padding-top:12px; margin-top:4px;">
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
