@extends('layouts.admin')
@section('title', 'Suscripciones recurrentes')
@section('page_title', 'Suscripciones recurrentes')
@section('page_subtitle', 'Jugadores con abono mensual en tus canchas')

@section('content')

@php
  $failedCount = $subscriptions->where('status', 'FAILED')->count();
@endphp

{{-- Alerta de pagos fallidos --}}
@if($failedCount > 0)
  <div class="flex items-start gap-3 px-4 py-3 mb-6 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
    <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span>
      Hay <strong>{{ $failedCount }}</strong> suscripción(es) con pago fallido. El jugador fue notificado por MercadoPago.
    </span>
  </div>
@endif

@if($subscriptions->isEmpty())
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-8 py-12 text-center">
    <svg class="w-10 h-10 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
    <h3 class="text-base font-bold text-slate-700 mb-1">No hay suscripciones activas</h3>
    <p class="text-sm text-slate-500">
      Cuando un jugador active un abono mensual en una de tus canchas, aparecerá acá.
    </p>
  </div>
@else
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="border-b border-slate-100 bg-slate-50">
            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Jugador</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Cancha</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Turno</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Monto mensual</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Último cobro</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Próximo cobro</th>
            <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($subscriptions as $sub)
            @php
              $lastPeriod = $sub->periods->first();

              $badgeClass = match($sub->status) {
                'ACTIVE'          => 'bg-green-100 text-green-800',
                'PENDING_PAYMENT' => 'bg-amber-100 text-amber-800',
                'FAILED'          => 'bg-red-100 text-red-800',
                default           => 'bg-slate-100 text-slate-600',
              };
            @endphp
            <tr class="hover:bg-slate-50 transition-colors">

              {{-- Jugador --}}
              <td class="px-5 py-4">
                <div class="font-semibold text-slate-800">{{ $sub->user->name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $sub->user->email }}</div>
              </td>

              {{-- Cancha --}}
              <td class="px-5 py-4">
                <div class="font-medium text-slate-700">{{ $sub->field->name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $sub->field->venue->name }}</div>
              </td>

              {{-- Turno --}}
              <td class="px-5 py-4 text-slate-600">
                <div>{{ $sub->dayLabel() }} {{ \Carbon\Carbon::parse($sub->start_time)->format('H:i') }}</div>
                <div class="text-xs text-slate-400 mt-0.5">{{ $sub->frequencyLabel() }}</div>
              </td>

              {{-- Monto mensual --}}
              <td class="px-5 py-4 font-semibold text-slate-800">
                {{ $sub->currency }} {{ number_format($sub->monthly_amount, 0, ',', '.') }}
              </td>

              {{-- Estado --}}
              <td class="px-5 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                  {{ $sub->statusLabel() }}
                </span>
              </td>

              {{-- Último cobro --}}
              <td class="px-5 py-4 text-slate-600">
                @if($lastPeriod && $lastPeriod->paid_at)
                  {{ \Carbon\Carbon::parse($lastPeriod->paid_at)->format('d/m/Y') }}
                @elseif($sub->last_payment_at)
                  {{ $sub->last_payment_at->format('d/m/Y') }}
                @else
                  <span class="text-slate-400">—</span>
                @endif
              </td>

              {{-- Próximo cobro --}}
              <td class="px-5 py-4 text-slate-600">
                @if($sub->next_billing_date)
                  {{ $sub->next_billing_date->format('d/m/Y') }}
                @else
                  <span class="text-slate-400">—</span>
                @endif
              </td>

              {{-- Acciones --}}
              <td class="px-5 py-4">
                @if(in_array($sub->status, ['ACTIVE', 'PENDING_PAYMENT']))
                  <form method="POST"
                        action="{{ route('va.recurring_subscriptions.cancel', $sub) }}"
                        onsubmit="return confirm('¿Cancelar la suscripción de {{ addslashes($sub->user->name) }}?')"
                        class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-red-50 hover:border-red-300 hover:text-red-700 transition-all duration-150">
                      Cancelar
                    </button>
                  </form>
                @else
                  <span class="text-slate-400 text-xs">—</span>
                @endif
              </td>

            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 text-xs text-slate-400">
      {{ $subscriptions->count() }} suscripción(es) en total
    </div>
  </div>
@endif

@endsection
