@extends('layouts.admin')

@section('title', 'Pagos a venues')
@section('page_title', 'Pagos a venues')
@section('page_subtitle', 'Deuda de la plataforma hacia los dueños de canchas por reservas gratis canjeadas')

@push('styles')
<style>
  .payout-venue-card {
    border: 1px solid #ececec;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 16px;
  }
  .payout-venue-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding: 16px 20px;
    background: #fafafa;
    border-bottom: 1px solid #ececec;
  }
  .payout-venue-name {
    font-size: 16px;
    font-weight: 700;
  }
  .payout-venue-total {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.02em;
  }
</style>
@endpush

@section('content')

  {{-- Resumen --}}
  <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:24px;">
    <div class="admin-card">
      <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#999; margin-bottom:8px;">
        Deuda pendiente
      </div>
      <div style="font-size:32px; font-weight:800; color:#842029;">
        ARS {{ number_format($totalPending, 0, ',', '.') }}
      </div>
    </div>
    <div class="admin-card">
      <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#999; margin-bottom:8px;">
        Total pagado históricamente
      </div>
      <div style="font-size:32px; font-weight:800; color:#157347;">
        ARS {{ number_format($totalPaid, 0, ',', '.') }}
      </div>
    </div>
  </div>

  {{-- Pendientes por venue --}}
  <div style="margin-bottom:32px;">
    <div class="section-title" style="margin-bottom:16px;">Pendientes de pago</div>

    @if($pendingByVenue->isEmpty())
      <div class="admin-card">
        <div class="empty-state">No hay pagos pendientes. ¡Estás al día con todos los venues!</div>
      </div>
    @else
      @foreach($pendingByVenue as $venueId => $payouts)
        @php
          $venue        = $payouts->first()->venue;
          $venuePending = $payouts->sum('amount');
          $currency     = $payouts->first()->currency;
        @endphp

        <div class="payout-venue-card">
          <div class="payout-venue-header">
            <div>
              <div class="payout-venue-name">{{ $venue->name }}</div>
              <div style="font-size:13px; color:#888; margin-top:3px;">
                {{ $payouts->count() }} reserva(s) gratis canjeada(s)
                @if($venue->mp_user_id)
                  · <span style="color:#157347; font-weight:600;">MP conectado ✓</span>
                @else
                  · <span style="color:#856404;">Sin MP conectado</span>
                @endif
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
              <div class="payout-venue-total" style="color:#842029;">
                {{ $currency }} {{ number_format($venuePending, 0, ',', '.') }}
              </div>
              {{-- Pago directo via MP --}}
              @if($venue->mp_access_token)
                <form method="POST" action="{{ route('sa.payouts.pay_via_mp', $venue) }}"
                      onsubmit="return confirm('¿Pagar ARS {{ number_format($venuePending, 0, ',', '.') }} a {{ addslashes($venue->name) }} vía Mercado Pago?')">
                  @csrf
                  <button type="submit" class="btn btn-primary btn-sm">
                    Pagar con MP →
                  </button>
                </form>
              @endif
              {{-- Marcar como pagado manualmente --}}
              <form method="POST" action="{{ route('sa.payouts.mark_venue_paid', $venue) }}"
                    onsubmit="return confirm('¿Marcar todos los pagos pendientes de {{ addslashes($venue->name) }} como pagados?')">
                @csrf
                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                  <input type="text" name="notes" placeholder="Nota (opcional)"
                         class="form-control" style="width:160px; font-size:13px;">
                  <button type="submit" class="btn btn-ghost btn-sm">
                    Marcar pagado
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div style="overflow-x:auto;">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Reserva</th>
                  <th>Cancha</th>
                  <th>Usuario que canjeó</th>
                  <th>Fecha</th>
                  <th>Monto</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                @foreach($payouts as $payout)
                  <tr>
                    <td>
                      <a href="{{ route('reservations.show', $payout->reservation) }}"
                         target="_blank" style="color:#111; font-weight:600;">
                        #{{ $payout->reservation_id }}
                      </a>
                    </td>
                    <td>{{ $payout->reservation->field->name ?? '—' }}</td>
                    <td>{{ $payout->referralReward->referred->name ?? '—' }}</td>
                    <td style="color:#888; white-space:nowrap;">
                      {{ $payout->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td style="font-weight:700;">
                      {{ $payout->currency }} {{ number_format($payout->amount, 0, ',', '.') }}
                    </td>
                    <td>
                      <form method="POST" action="{{ route('sa.payouts.mark_paid', $payout) }}"
                            onsubmit="return confirm('¿Marcar este pago como realizado?')">
                        @csrf
                        <button type="submit" class="btn btn-ghost btn-sm">Marcar pagado</button>
                      </form>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endforeach
    @endif
  </div>

  {{-- Historial reciente de pagos realizados --}}
  <div>
    <div class="section-title" style="margin-bottom:16px;">Últimos 30 pagos realizados</div>

    @if($paidRecent->isEmpty())
      <div class="admin-card">
        <div class="empty-state">Aún no se registraron pagos realizados.</div>
      </div>
    @else
      <div class="admin-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Venue</th>
                <th>Reserva</th>
                <th>Cancha</th>
                <th>Monto</th>
                <th>Pagado el</th>
                <th>Nota</th>
              </tr>
            </thead>
            <tbody>
              @foreach($paidRecent as $payout)
                <tr>
                  <td style="font-weight:600;">{{ $payout->venue->name ?? '—' }}</td>
                  <td>
                    <a href="{{ route('reservations.show', $payout->reservation) }}"
                       target="_blank" style="color:#111;">#{{ $payout->reservation_id }}</a>
                  </td>
                  <td>{{ $payout->reservation->field->name ?? '—' }}</td>
                  <td style="font-weight:700; color:#157347;">
                    {{ $payout->currency }} {{ number_format($payout->amount, 0, ',', '.') }}
                  </td>
                  <td style="color:#888; white-space:nowrap;">
                    {{ $payout->paid_at?->format('d/m/Y H:i') ?? '—' }}
                  </td>
                  <td style="color:#666; font-size:13px;">{{ $payout->notes ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif
  </div>

@endsection
