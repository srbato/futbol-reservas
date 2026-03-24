@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.admin')

@section('title', 'Reservas')
@section('page_title', 'Reservas')
@section('page_subtitle', 'Filtrá y gestioná las reservas de tus canchas')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_reservations',
  'helpTitle' => 'Lista de reservas',
  'helpText'  => 'Acá podés ver todas las reservas de tus canchas. Usá los filtros para buscar por fecha, estado o cancha. También podés cancelar reservas individuales y crear reservas manuales para clientes que pagan en el momento.',
])

{{-- ── Header con acción principal ── --}}
<div class="flex items-center justify-between mb-5">
  <div>
    <p class="text-sm text-slate-500">
      Mostrando reservas del <span class="font-semibold text-slate-700">{{ $date->format('d/m/Y') }}</span>
    </p>
  </div>
  <button onclick="document.getElementById('modal-manual').style.display='flex'"
          class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
    </svg>
    Nueva reserva manual
  </button>
</div>

{{-- ── Filtros ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-4 mb-6">
  <form method="GET" action="{{ route('va.reservations.index') }}">
    <div class="flex flex-wrap items-end gap-4">

      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Fecha</label>
        <input type="date" name="date" value="{{ $date->toDateString() }}"
               class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Estado</label>
        <select name="status"
                class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
          <option value="">Todos</option>
          <option value="PENDING_PAYMENT" {{ ($status ?? '') === 'PENDING_PAYMENT' ? 'selected' : '' }}>Pendiente de pago</option>
          <option value="PAID"            {{ ($status ?? '') === 'PAID'            ? 'selected' : '' }}>Pagada</option>
          <option value="CANCELLED"       {{ ($status ?? '') === 'CANCELLED'       ? 'selected' : '' }}>Cancelada</option>
          <option value="EXPIRED"         {{ ($status ?? '') === 'EXPIRED'         ? 'selected' : '' }}>Expirada</option>
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cancha</label>
        <select name="field_id"
                class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
          <option value="">Todas</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}" {{ (string)($fieldId ?? '') === (string)$field->id ? 'selected' : '' }}>
              {{ $field->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="flex items-center gap-2">
        <button type="submit"
                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
          Filtrar
        </button>
        <a href="{{ route('va.reservations.index') }}"
           class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all duration-200">
          Limpiar
        </a>
      </div>

    </div>
  </form>
</div>

{{-- ── Tabla de reservas ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="bg-slate-50 border-b border-slate-100">
          <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Hora</th>
          <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Cancha</th>
          <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Usuario / Cliente</th>
          <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Estado</th>
          <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Código</th>
          <th class="px-6 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($reservations as $r)
          <tr class="hover:bg-slate-50 transition-colors">

            <td class="px-6 py-3.5 font-semibold text-slate-900 whitespace-nowrap">
              {{ $r->start_at->format('H:i') }} – {{ $r->end_at->format('H:i') }}
            </td>

            <td class="px-6 py-3.5 text-slate-700">{{ $r->field->name }}</td>

            <td class="px-6 py-3.5 text-slate-700">
              @if($r->payment_provider === 'manual')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 mr-1.5">
                  Manual
                </span>
                {{ $r->notes ?? '—' }}
              @else
                {{ $r->user->name ?? '—' }}
              @endif
            </td>

            <td class="px-6 py-3.5">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                    style="{{ ReservationStatus::color($r->status) }}">
                {{ ReservationStatus::label($r->status) }}
              </span>
            </td>

            <td class="px-6 py-3.5">
              <code class="text-xs text-slate-500 bg-slate-100 px-2 py-1 rounded-lg font-mono">
                {{ $r->verification_code ?? '—' }}
              </code>
            </td>

            <td class="px-6 py-3.5 text-right">
              @if(in_array($r->status, ['PENDING_PAYMENT', 'PAID']))
                <form method="POST" action="{{ route('va.reservations.cancel', $r) }}"
                      style="display:inline;"
                      onsubmit="return confirm('¿Cancelar esta reserva?')">
                  @csrf
                  <button type="submit"
                          class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-500 text-white text-xs font-semibold rounded-lg hover:shadow-md transition-all duration-200">
                    Cancelar
                  </button>
                </form>
              @endif
            </td>

          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">
              No hay reservas para esos filtros.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ── Modal: nueva reserva manual ── --}}
<div id="modal-manual"
     style="display:none;"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     role="dialog" aria-modal="true">

  {{-- Backdrop --}}
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

  {{-- Panel --}}
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">

    <div class="flex items-center justify-between mb-6">
      <h2 class="text-lg font-bold text-slate-900">Nueva reserva manual</h2>
      <button type="button"
              onclick="document.getElementById('modal-manual').style.display='none'"
              class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('va.reservations.manual_store') }}">
      @csrf

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cancha</label>
        <select name="field_id" required
                class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
          <option value="">Seleccioná una cancha</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}">{{ $field->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-4">
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Fecha</label>
          <input type="date" name="date" required
                 value="{{ now()->toDateString() }}"
                 min="{{ now()->toDateString() }}"
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Hora de inicio</label>
          <input type="time" name="time" required
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>
      </div>

      <div class="mb-6">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
          Cliente / Nota
          <span class="text-slate-400 normal-case font-normal ml-1">(opcional)</span>
        </label>
        <input type="text" name="notes" maxlength="255"
               placeholder="Ej: Juan García — llamó por teléfono"
               class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
      </div>

      <div class="flex items-center justify-end gap-3">
        <button type="button"
                onclick="document.getElementById('modal-manual').style.display='none'"
                class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all duration-200">
          Cancelar
        </button>
        <button type="submit"
                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
          Crear reserva
        </button>
      </div>

    </form>
  </div>
</div>

<script>
  // Close modal clicking outside (backdrop)
  document.getElementById('modal-manual').addEventListener('click', function(e) {
    if (e.target === this || e.target.classList.contains('backdrop-blur-sm')) {
      this.style.display = 'none';
    }
  });
</script>

@endsection
