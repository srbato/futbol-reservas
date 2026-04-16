@extends('layouts.app')

@section('title', 'Confirmar cambio — Reserva #' . $reservation->id)

@section('content')
<div style="max-width: 640px; margin: auto; display: grid; gap: 18px;">

  <div class="page-card">
    <h1 style="margin:0 0 8px; font-size:30px; letter-spacing:-0.02em;">Confirmar cambio</h1>
    <p class="muted" style="margin:0;">Revisá los detalles antes de confirmar el nuevo horario.</p>
  </div>

  {{-- Comparativa antes / después --}}
  <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
    <div class="page-card" style="background:#1a1a1a; border-color:rgba(255,255,255,.08);">
      <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#aaa; margin-bottom:12px;">Horario actual</div>
      <div style="font-size:16px; font-weight:700; margin-bottom:6px;">{{ $reservation->field->name }}</div>
      <div class="muted" style="font-size:13px; margin-bottom:10px;">{{ $reservation->field->venue->name }}</div>
      <span class="badge">{{ $reservation->start_at->format('d/m/Y') }}</span>
      <span class="badge" style="margin-top:6px; display:inline-block;">{{ $reservation->start_at->format('H:i') }} – {{ $reservation->end_at->format('H:i') }}</span>
      <div style="margin-top:10px; font-size:18px; font-weight:800;">
        {{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}
      </div>
    </div>

    <div class="page-card" style="border-color:#22c55e; background:rgba(34,197,94,.06);">
      <div style="font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:#16a34a; margin-bottom:12px;">Nuevo horario</div>
      <div style="font-size:16px; font-weight:700; margin-bottom:6px;">{{ $newField->name }}</div>
      <div class="muted" style="font-size:13px; margin-bottom:10px;">{{ $newField->venue->name }}</div>
      <span class="badge" style="background:#dcfce7; color:#15803d; border-color:#bbf7d0;">{{ $newStart->format('d/m/Y') }}</span>
      <span class="badge" style="margin-top:6px; display:inline-block; background:#dcfce7; color:#15803d; border-color:#bbf7d0;">
        {{ $newStart->format('H:i') }} – {{ $newStart->copy()->addMinutes($newField->slot_minutes ?: 60)->format('H:i') }}
      </span>
      <div style="margin-top:10px; font-size:18px; font-weight:800; color:#15803d;">
        {{ $reservation->currency }} {{ number_format($newPrice, 0, ',', '.') }}
      </div>
    </div>
  </div>

  {{-- Delta de precio --}}
  @if($diff == 0)
    <div class="page-card" style="background:#e8f7ee; border-color:#cfe9d7; color:#157347; font-weight:700;">
      Sin diferencia de precio. El cambio se confirma sin costo adicional.
    </div>
  @elseif($diff < 0)
    <div class="page-card" style="background:#e8f7ee; border-color:#cfe9d7;">
      <div style="color:#157347; font-weight:700; margin-bottom:4px;">
        Recibirás un reembolso de {{ $reservation->currency }} {{ number_format(abs($diff), 0, ',', '.') }}
      </div>
      <div style="font-size:13px; color:#555; line-height:1.6;">
        El nuevo horario es más económico. Se procesará un reembolso parcial a tu cuenta de MercadoPago.
      </div>
    </div>
  @else
    {{-- diff > 0: Fase 2 — pagar diferencia con MercadoPago --}}
    <div class="page-card" style="background:#eff6ff; border-color:#bfdbfe; color:#1e40af;">
      <div style="font-weight:700; margin-bottom:4px;">
        El nuevo horario tiene un costo adicional de {{ $reservation->currency }} {{ number_format($diff, 0, ',', '.') }}
      </div>
      <div style="font-size:13px; line-height:1.6;">
        Se te cobrará solo la diferencia de precio a través de MercadoPago. Tu reserva original sigue vigente hasta que el pago sea confirmado.
      </div>
    </div>
  @endif

  {{-- Acciones --}}
  <div class="page-card">
    <div style="display:flex; gap:12px; flex-wrap:wrap;">
      <form method="POST" action="{{ route('reservations.modify.confirm', $reservation) }}" style="margin:0;">
        @csrf
        @if($diff > 0)
          <button type="submit" class="btn btn-primary">
            Pagar diferencia de {{ $reservation->currency }} {{ number_format($diff, 0, ',', '.') }} con MercadoPago
          </button>
        @else
          <button type="submit" class="btn btn-primary">Confirmar cambio</button>
        @endif
      </form>
      <a href="{{ route('reservations.modify.show', $reservation) }}" class="btn">Elegir otro horario</a>
      <a href="{{ route('reservations.show', $reservation) }}" class="btn">Cancelar</a>
    </div>
  </div>

</div>

<style>
  @media (max-width: 600px) {
    div[style*="grid-template-columns:1fr 1fr"] { grid-template-columns: 1fr !important; }
  }
</style>
@endsection
