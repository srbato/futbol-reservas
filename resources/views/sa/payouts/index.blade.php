@extends('layouts.admin')

@section('title', 'Pagos a venues')
@section('page_title', 'Pagos a venues')

@section('content')

  {{-- Resumen --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm px-6 py-5">
      <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Deuda pendiente</p>
      <p class="text-4xl font-extrabold text-red-600 tracking-tight">
        ARS {{ number_format($totalPending, 0, ',', '.') }}
      </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm px-6 py-5">
      <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total pagado historicamente</p>
      <p class="text-4xl font-extrabold text-emerald-600 tracking-tight">
        ARS {{ number_format($totalPaid, 0, ',', '.') }}
      </p>
    </div>

  </div>

  {{-- Pendientes por venue --}}
  <div class="mb-10">

    <h2 class="text-base font-bold text-slate-900 mb-4">Pendientes de pago</h2>

    @if($pendingByVenue->isEmpty())
      <div class="bg-white border border-slate-200 rounded-2xl shadow-sm px-8 py-14 text-center">
        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-slate-400">No hay pagos pendientes. Estas al dia con todos los venues.</p>
      </div>
    @else
      <div class="space-y-4">
        @foreach($pendingByVenue as $venueId => $payouts)
          @php
            $venue        = $payouts->first()->venue;
            $venuePending = $payouts->sum('amount');
            $currency     = $payouts->first()->currency;
          @endphp

          <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

            {{-- Venue header --}}
            <div class="flex items-center justify-between gap-4 flex-wrap px-6 py-4 bg-slate-50 border-b border-slate-200">
              <div>
                <p class="text-base font-bold text-slate-900">{{ $venue->name }}</p>
                <p class="text-sm text-slate-500 mt-0.5">
                  {{ $payouts->count() }} reserva(s) gratis canjeada(s)
                  @if($venue->mp_user_id)
                    &nbsp;·&nbsp;
                    <span class="text-emerald-600 font-semibold">MP conectado</span>
                  @else
                    &nbsp;·&nbsp;
                    <span class="text-amber-600">Sin MP conectado</span>
                  @endif
                </p>
              </div>

              <div class="flex items-center gap-3 flex-wrap">
                <span class="text-2xl font-extrabold text-red-600 tracking-tight">
                  {{ $currency }} {{ number_format($venuePending, 0, ',', '.') }}
                </span>

                {{-- Pagar via MP --}}
                @if($venue->mp_access_token)
                  <form method="POST" action="{{ route('sa.payouts.pay_via_mp', $venue) }}"
                        onsubmit="return confirm('¿Pagar ARS {{ number_format($venuePending, 0, ',', '.') }} a {{ addslashes($venue->name) }} via Mercado Pago?')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-indigo-200 hover:shadow-md transition-all duration-200">
                      Pagar con MP
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                      </svg>
                    </button>
                  </form>
                @endif

                {{-- Marcar pagado manualmente --}}
                <form method="POST" action="{{ route('sa.payouts.mark_venue_paid', $venue) }}"
                      onsubmit="return confirm('¿Marcar todos los pagos pendientes de {{ addslashes($venue->name) }} como pagados?')">
                  @csrf
                  <div class="flex items-center gap-2 flex-wrap">
                    <input type="text" name="notes" placeholder="Nota (opcional)"
                           class="px-3 py-2 text-xs border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-40">
                    <button type="submit"
                            class="px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                      Marcar pagado
                    </button>
                  </div>
                </form>
              </div>
            </div>

            {{-- Tabla detalle --}}
            <div class="overflow-x-auto">
              <table class="w-full text-sm">
                <thead>
                  <tr class="border-b border-slate-100 bg-white">
                    <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Reserva</th>
                    <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Cancha</th>
                    <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Fecha</th>
                    <th class="text-left px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Monto</th>
                    <th class="px-5 py-3"></th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  @foreach($payouts as $payout)
                    <tr class="hover:bg-slate-50 transition-colors">
                      <td class="px-5 py-3">
                        <a href="{{ route('reservations.show', $payout->reservation) }}"
                           target="_blank"
                           class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                          #{{ $payout->reservation_id }}
                        </a>
                      </td>
                      <td class="px-5 py-3 text-slate-600">{{ $payout->reservation->field->name ?? '—' }}</td>
                      <td class="px-5 py-3 text-slate-400 text-xs whitespace-nowrap">
                        {{ $payout->created_at->format('d/m/Y H:i') }}
                      </td>
                      <td class="px-5 py-3 font-bold text-slate-900">
                        {{ $payout->currency }} {{ number_format($payout->amount, 0, ',', '.') }}
                      </td>
                      <td class="px-5 py-3">
                        <form method="POST" action="{{ route('sa.payouts.mark_paid', $payout) }}"
                              onsubmit="return confirm('¿Marcar este pago como realizado?')">
                          @csrf
                          <button type="submit"
                                  class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                            Marcar pagado
                          </button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>

          </div>
        @endforeach
      </div>
    @endif

  </div>

  {{-- Historial reciente --}}
  <div>

    <h2 class="text-base font-bold text-slate-900 mb-4">Ultimos 30 pagos realizados</h2>

    @if($paidRecent->isEmpty())
      <div class="bg-white border border-slate-200 rounded-2xl shadow-sm px-8 py-12 text-center">
        <p class="text-sm text-slate-400">Aun no se registraron pagos realizados.</p>
      </div>
    @else
      <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="border-b border-slate-200 bg-slate-50">
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Venue</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Reserva</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Cancha</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Monto</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Pagado el</th>
                <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Nota</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @foreach($paidRecent as $payout)
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-5 py-3 font-semibold text-slate-900">{{ $payout->venue->name ?? '—' }}</td>
                  <td class="px-5 py-3">
                    <a href="{{ route('reservations.show', $payout->reservation) }}"
                       target="_blank"
                       class="text-indigo-600 hover:text-indigo-800 transition-colors font-medium">
                      #{{ $payout->reservation_id }}
                    </a>
                  </td>
                  <td class="px-5 py-3 text-slate-600">{{ $payout->reservation->field->name ?? '—' }}</td>
                  <td class="px-5 py-3 font-bold text-emerald-600">
                    {{ $payout->currency }} {{ number_format($payout->amount, 0, ',', '.') }}
                  </td>
                  <td class="px-5 py-3 text-xs text-slate-400 whitespace-nowrap">
                    {{ $payout->paid_at?->format('d/m/Y H:i') ?? '—' }}
                  </td>
                  <td class="px-5 py-3 text-sm text-slate-500">{{ $payout->notes ?? '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif

  </div>

@endsection
