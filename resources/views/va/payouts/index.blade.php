@extends('layouts.admin')

@section('title', 'Mis cobros')
@section('page_title', 'Mis cobros')
@section('page_subtitle', 'Lo que la plataforma te debe por reservas gratis canjeadas en tu complejo')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_payouts',
  'helpTitle' => 'Retiros y cobros',
  'helpText'  => 'Acá se registra lo que la plataforma te debe por reservas gratuitas canjeadas en tu complejo con membresías de usuarios. Podés ver el detalle de cada reserva canjeada y el total pendiente de pago.',
])

  {{-- Resumen --}}
  <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:24px;">
    <div class="admin-card">
      <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#999; margin-bottom:8px;">
        Pendiente de cobro
      </div>
      <div style="font-size:32px; font-weight:800; color:{{ $totalPending > 0 ? '#856404' : '#157347' }};">
        ARS {{ number_format($totalPending, 0, ',', '.') }}
      </div>
      @if($totalPending > 0)
        <div style="font-size:13px; color:#888; margin-top:6px;">La plataforma te debe este monto</div>
      @else
        <div style="font-size:13px; color:#157347; margin-top:6px;">Estás al día</div>
      @endif
    </div>
    <div class="admin-card">
      <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#999; margin-bottom:8px;">
        Total recibido históricamente
      </div>
      <div style="font-size:32px; font-weight:800; color:#157347;">
        ARS {{ number_format($totalPaid, 0, ',', '.') }}
      </div>
    </div>
  </div>

{{-- Pendientes --}}
  <div style="margin-bottom:32px;">
    <div class="section-title" style="margin-bottom:16px;">Pendientes de pago ({{ $pending->count() }})</div>

    @if($pending->isEmpty())
      <div class="admin-card">
        <div class="empty-state">No tenés cobros pendientes. La plataforma está al día con vos.</div>
      </div>
    @else
      <div class="admin-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Reserva</th>
                <th>Cancha</th>
                <th>Fecha del canje</th>
                <th>Monto</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pending as $payout)
                <tr>
                  <td>
                    <a href="{{ route('reservations.show', $payout->reservation) }}"
                       target="_blank" style="color:#111; font-weight:600;">
                      #{{ $payout->reservation_id }}
                    </a>
                  </td>
                  <td>{{ $payout->reservation->field->name ?? '—' }}</td>
                  <td style="color:#888; white-space:nowrap;">
                    {{ $payout->created_at->format('d/m/Y H:i') }}
                  </td>
                  <td style="font-weight:700; color:#856404;">
                    {{ $payout->currency }} {{ number_format($payout->amount, 0, ',', '.') }}
                  </td>
                  <td>
                    <span class="badge badge-warning">Pendiente</span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif
  </div>

  {{-- Pagados --}}
  <div>
    <div class="section-title" style="margin-bottom:16px;">Historial de pagos recibidos ({{ $paid->count() }})</div>

    @if($paid->isEmpty())
      <div class="admin-card">
        <div class="empty-state">Aún no recibiste pagos de la plataforma.</div>
      </div>
    @else
      <div class="admin-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x:auto;">
          <table class="data-table">
            <thead>
              <tr>
                <th>Reserva</th>
                <th>Cancha</th>
                <th>Monto</th>
                <th>Pagado el</th>
                <th>Nota</th>
              </tr>
            </thead>
            <tbody>
              @foreach($paid as $payout)
                <tr>
                  <td>
                    <a href="{{ route('reservations.show', $payout->reservation) }}"
                       target="_blank" style="color:#111; font-weight:600;">
                      #{{ $payout->reservation_id }}
                    </a>
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
