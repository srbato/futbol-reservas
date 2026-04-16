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
  <div class="flex items-center gap-2">
    <button onclick="document.getElementById('modal-recurring').style.display='flex'"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-xl shadow-sm hover:bg-slate-50 transition-all duration-200">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      Recurrente manual
    </button>
    <button onclick="document.getElementById('modal-manual').style.display='flex'"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
      Nueva reserva manual
    </button>
  </div>
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
          <option value="PENDING_CASH"    {{ ($status ?? '') === 'PENDING_CASH'    ? 'selected' : '' }}>Pago en el complejo</option>
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
                <span class="font-medium text-slate-800">{{ $r->user->name ?? '—' }}</span>
                @if($r->user && $r->user->phone)
                  <br><a href="tel:{{ $r->user->phone }}" class="text-xs text-slate-400 hover:text-indigo-600 transition-colors">{{ $r->user->phone }}</a>
                @endif
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

            <td class="px-6 py-3.5 text-right whitespace-nowrap">
              @if($r->status === 'PENDING_CASH')
                <form method="POST" action="{{ route('va.reservations.confirm_cash', $r) }}"
                      style="display:inline;"
                      onsubmit="return confirm('¿Confirmar el pago en efectivo de esta reserva?')">
                  @csrf
                  <button type="submit"
                          style="padding:6px 12px; background:#16a34a; color:#fff; font-size:12px; font-weight:700; border-radius:8px; border:none; cursor:pointer;">
                    Confirmar pago
                  </button>
                </form>
              @endif
              @if(in_array($r->status, ['PENDING_PAYMENT', 'PENDING_CASH', 'PAID']))
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
     class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto"
     role="dialog" aria-modal="true">

  {{-- Backdrop --}}
  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

  {{-- Panel --}}
  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 max-h-[90vh] overflow-y-auto min-h-0 my-auto">

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
        <select name="field_id" id="modal-field-id" required
                class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
          <option value="">Seleccioná una cancha</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}">{{ $field->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Fecha</label>
        <input type="date" id="manual-date-picker"
               value="{{ now()->toDateString() }}"
               min="{{ now()->toDateString() }}"
               class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
      </div>

      {{-- Hidden inputs reales que se envían al controller --}}
      <input type="hidden" name="date" id="manual-hidden-date">
      <input type="hidden" name="time" id="manual-hidden-time">

      {{-- ── Panel de slots ── --}}
      <div id="slots-section" class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Horario disponible</label>

        {{-- Estado: esperando selección de cancha y fecha --}}
        <div id="slots-placeholder" class="px-4 py-3 rounded-xl border border-dashed border-slate-300 text-sm text-slate-400 text-center">
          Seleccioná una cancha y una fecha para ver los horarios.
        </div>

        {{-- Estado: cargando --}}
        <div id="slots-loading" style="display:none;" class="px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-500 text-center flex items-center justify-center gap-2">
          <svg class="w-4 h-4 animate-spin text-indigo-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
          Cargando horarios...
        </div>

        {{-- Estado: sin horario --}}
        <div id="slots-empty" style="display:none;" class="px-4 py-3 rounded-xl border border-dashed border-slate-300 text-sm text-slate-400 text-center">
          Sin horario disponible para este dia.
        </div>

        {{-- Estado: error --}}
        <div id="slots-error" style="display:none;" class="px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-sm text-red-600 text-center">
          Error al cargar los horarios. Intenta de nuevo.
        </div>

        {{-- Grid de slots --}}
        <div id="slots-grid" style="display:none;" class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto"></div>
      </div>

      {{-- ── Buscar cliente registrado ── --}}
      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
          Cliente registrado
          <span class="text-slate-400 normal-case font-normal ml-1">(opcional — buscá por nombre o email)</span>
        </label>

        {{-- Panel de cliente seleccionado --}}
        <div id="selected-client-panel" style="display:none;"
             class="flex items-center justify-between px-3 py-2 rounded-xl border border-indigo-300 bg-indigo-50 text-sm mb-2">
          <div>
            <span id="selected-client-name" class="font-semibold text-slate-900"></span>
            <span id="selected-client-email" class="text-slate-500 ml-1.5"></span>
          </div>
          <button type="button" id="deselect-client"
                  class="ml-2 text-slate-400 hover:text-red-500 transition-colors" title="Desvincular">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        {{-- Input de búsqueda --}}
        <div class="relative" id="search-client-wrapper">
          <input type="text" id="client-search-input"
                 autocomplete="off"
                 placeholder="Escribí al menos 3 caracteres..."
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
          {{-- Dropdown de resultados --}}
          <div id="client-search-dropdown"
               style="display:none;"
               class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden">
          </div>
        </div>

        <input type="hidden" name="client_user_id" id="client_user_id_input">
      </div>

      {{-- ── Monto cobrado ── --}}
      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
          Monto cobrado
          <span class="text-slate-400 normal-case font-normal ml-1">(opcional)</span>
        </label>
        <input type="number" name="amount_paid" min="0" step="0.01"
               placeholder="0.00"
               class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
      </div>

      {{-- ── Nota / Comentario ── --}}
      <div class="mb-6">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
          Nota / Comentario
          <span class="text-slate-400 normal-case font-normal ml-1">(opcional)</span>
        </label>
        <input type="text" name="notes" maxlength="255"
               placeholder="Ej: llamó por teléfono, pago en efectivo..."
               class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
      </div>

      <div class="flex items-center justify-end gap-3">
        <button type="button"
                onclick="document.getElementById('modal-manual').style.display='none'"
                class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all duration-200">
          Cancelar
        </button>
        <button type="submit" id="manual-submit-btn" disabled
                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
          Crear reserva
        </button>
      </div>

    </form>
  </div>
</div>

{{-- ── Modal: nueva reserva recurrente manual ── --}}
<div id="modal-recurring"
     style="display:none;"
     class="fixed inset-0 z-50 flex items-start justify-center p-4 overflow-y-auto"
     role="dialog" aria-modal="true">

  <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

  <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 max-h-[90vh] overflow-y-auto min-h-0 my-auto">

    <div class="flex items-center justify-between mb-6">
      <h2 class="text-lg font-bold text-slate-900">Nueva reserva recurrente manual</h2>
      <button type="button"
              onclick="document.getElementById('modal-recurring').style.display='none'"
              class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <form method="POST" action="{{ route('va.reservations.manual_recurring_store') }}">
      @csrf

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cancha</label>
        <select name="field_id" id="recur-field-id" required
                class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">Seleccioná una cancha</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}">{{ $field->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Fecha del primer turno</label>
        <input type="date" id="recur-date-picker"
               value="{{ now()->toDateString() }}"
               min="{{ now()->toDateString() }}"
               class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
      </div>

      <input type="hidden" name="date" id="recur-hidden-date">
      <input type="hidden" name="time" id="recur-hidden-time">

      {{-- Slots --}}
      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Horario del primer turno</label>
        <div id="recur-slots-placeholder" class="px-4 py-3 rounded-xl border border-dashed border-slate-300 text-sm text-slate-400 text-center">
          Seleccioná una cancha y una fecha para ver los horarios.
        </div>
        <div id="recur-slots-loading" style="display:none;" class="px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-500 text-center flex items-center justify-center gap-2">
          <svg class="w-4 h-4 animate-spin text-indigo-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
          </svg>
          Cargando horarios...
        </div>
        <div id="recur-slots-empty" style="display:none;" class="px-4 py-3 rounded-xl border border-dashed border-slate-300 text-sm text-slate-400 text-center">
          Sin horario disponible para este día.
        </div>
        <div id="recur-slots-error" style="display:none;" class="px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-sm text-red-600 text-center">
          Error al cargar los horarios.
        </div>
        <div id="recur-slots-grid" style="display:none;" class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto"></div>
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Frecuencia</label>
        <select name="frequency"
                class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="weekly">Semanal</option>
          <option value="biweekly">Quincenal</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cantidad de turnos</label>
        <select name="occurrences"
                class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="2">2 turnos</option>
          <option value="4">4 turnos (~1 mes)</option>
          <option value="8" selected>8 turnos (~2 meses)</option>
          <option value="12">12 turnos (~3 meses)</option>
        </select>
      </div>

      {{-- Cliente --}}
      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
          Cliente registrado
          <span class="text-slate-400 normal-case font-normal ml-1">(opcional)</span>
        </label>
        <div id="recur-selected-client-panel" style="display:none;"
             class="flex items-center justify-between px-3 py-2 rounded-xl border border-indigo-300 bg-indigo-50 text-sm mb-2">
          <div>
            <span id="recur-selected-client-name" class="font-semibold text-slate-900"></span>
            <span id="recur-selected-client-email" class="text-slate-500 ml-1.5"></span>
          </div>
          <button type="button" id="recur-deselect-client"
                  class="ml-2 text-slate-400 hover:text-red-500 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="relative" id="recur-search-wrapper">
          <input type="text" id="recur-client-search"
                 autocomplete="off"
                 placeholder="Escribí al menos 3 caracteres..."
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <div id="recur-client-dropdown" style="display:none;"
               class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden"></div>
        </div>
        <input type="hidden" name="client_user_id" id="recur-client-user-id">
      </div>

      <div class="mb-4">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
          Monto total cobrado
          <span class="text-slate-400 normal-case font-normal ml-1">(opcional — se divide en partes iguales)</span>
        </label>
        <input type="number" name="amount_paid" min="0" step="0.01"
               placeholder="0.00"
               class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
      </div>

      <div class="mb-6">
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
          Nota / Comentario
          <span class="text-slate-400 normal-case font-normal ml-1">(opcional)</span>
        </label>
        <input type="text" name="notes" maxlength="255"
               placeholder="Ej: abono mensual en efectivo..."
               class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
      </div>

      <div class="flex items-center justify-end gap-3">
        <button type="button"
                onclick="document.getElementById('modal-recurring').style.display='none'"
                class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all duration-200">
          Cancelar
        </button>
        <button type="submit" id="recur-submit-btn" disabled
                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none">
          Crear reservas
        </button>
      </div>

    </form>
  </div>
</div>

<script>
  // ── Close modal clicking outside (backdrop) ──
  document.getElementById('modal-manual').addEventListener('click', function(e) {
    if (e.target === this || e.target.classList.contains('backdrop-blur-sm')) {
      this.style.display = 'none';
    }
  });

  // ── Client search autocomplete ──
  (function () {
    const searchInput    = document.getElementById('client-search-input');
    const dropdown       = document.getElementById('client-search-dropdown');
    const hiddenInput    = document.getElementById('client_user_id_input');
    const selectedPanel  = document.getElementById('selected-client-panel');
    const selectedName   = document.getElementById('selected-client-name');
    const selectedEmail  = document.getElementById('selected-client-email');
    const deselectBtn    = document.getElementById('deselect-client');
    const searchWrapper  = document.getElementById('search-client-wrapper');

    let debounceTimer = null;

    function showDropdown(results) {
      dropdown.innerHTML = '';
      if (!results.length) {
        dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">Sin resultados</div>';
        dropdown.style.display = 'block';
        return;
      }
      results.forEach(function (u) {
        const item = document.createElement('button');
        item.type = 'button';
        item.className = 'w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 transition-colors border-b border-slate-100 last:border-0';
        item.innerHTML =
          '<span class="font-semibold text-slate-900">' + escapeHtml(u.name) + '</span>' +
          '<span class="text-slate-500 ml-2">' + escapeHtml(u.email) + '</span>';
        item.addEventListener('click', function () {
          selectClient(u);
        });
        dropdown.appendChild(item);
      });
      dropdown.style.display = 'block';
    }

    function hideDropdown() {
      dropdown.style.display = 'none';
      dropdown.innerHTML = '';
    }

    function selectClient(u) {
      hiddenInput.value       = u.id;
      selectedName.textContent  = u.name;
      selectedEmail.textContent = u.email;
      selectedPanel.style.display = 'flex';
      searchWrapper.style.display = 'none';
      hideDropdown();
      searchInput.value = '';
    }

    function deselectClient() {
      hiddenInput.value = '';
      selectedPanel.style.display = 'none';
      searchWrapper.style.display = 'block';
      searchInput.value = '';
    }

    function escapeHtml(str) {
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    }

    searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      const q = this.value.trim();
      if (q.length < 3) {
        hideDropdown();
        return;
      }
      debounceTimer = setTimeout(function () {
        dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400 flex items-center gap-2"><svg class="w-3.5 h-3.5 animate-spin text-indigo-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>Buscando...</div>';
        dropdown.style.display = 'block';
        fetch('{{ route("va.users.search") }}?q=' + encodeURIComponent(q), {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(function (r) { return r.json(); })
          .then(function (data) { showDropdown(data); })
          .catch(function () { hideDropdown(); });
      }, 300);
    });

    deselectBtn.addEventListener('click', deselectClient);

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
      if (!searchWrapper.contains(e.target)) {
        hideDropdown();
      }
    });

    // Reset state when modal opens
    document.querySelector('[onclick="document.getElementById(\'modal-manual\').style.display=\'flex\'"]')
      .addEventListener('click', function () {
        deselectClient();
        document.querySelector('[name="amount_paid"]').value = '';
        document.querySelector('[name="notes"]').value = '';
        resetSlots();
      });
  })();

  // ── Slot picker ──
  (function () {
    const fieldSelect    = document.getElementById('modal-field-id');
    const datePicker     = document.getElementById('manual-date-picker');
    const hiddenDate     = document.getElementById('manual-hidden-date');
    const hiddenTime     = document.getElementById('manual-hidden-time');
    const submitBtn      = document.getElementById('manual-submit-btn');

    const elPlaceholder  = document.getElementById('slots-placeholder');
    const elLoading      = document.getElementById('slots-loading');
    const elEmpty        = document.getElementById('slots-empty');
    const elError        = document.getElementById('slots-error');
    const elGrid         = document.getElementById('slots-grid');

    let currentFieldId   = null;
    let currentDate      = null;
    let fetchController  = null;

    // Exponer resetSlots al scope externo para que el reset del modal lo llame
    window.resetSlots = function () {
      hiddenDate.value = '';
      hiddenTime.value = '';
      submitBtn.disabled = true;
      showState('placeholder');
      currentFieldId = null;
      currentDate    = null;
      // Resetear también el date picker a hoy
      datePicker.value = '{{ now()->toDateString() }}';
    };

    function showState(state) {
      elPlaceholder.style.display = state === 'placeholder' ? '' : 'none';
      elLoading.style.display     = state === 'loading'     ? '' : 'none';
      elEmpty.style.display       = state === 'empty'       ? '' : 'none';
      elError.style.display       = state === 'error'       ? '' : 'none';
      elGrid.style.display        = state === 'grid'        ? '' : 'none';
    }

    function tryFetch() {
      const fieldId = fieldSelect.value;
      const date    = datePicker.value;

      if (!fieldId || !date) {
        showState('placeholder');
        clearSelection();
        return;
      }

      // Si ya teníamos una selección y algo cambió, la limpiamos
      clearSelection();

      currentFieldId = fieldId;
      currentDate    = date;

      // Cancelar fetch previo si hubiera
      if (fetchController) {
        fetchController.abort();
      }
      fetchController = new AbortController();

      showState('loading');

      const url = '/fields/' + fieldId + '/availability?date=' + encodeURIComponent(date);

      fetch(url, { signal: fetchController.signal })
        .then(function (r) {
          if (!r.ok) throw new Error('HTTP ' + r.status);
          return r.json();
        })
        .then(function (data) {
          fetchController = null;
          if (!data.slots || data.slots.length === 0) {
            showState('empty');
            return;
          }
          renderSlots(data.slots, date);
        })
        .catch(function (err) {
          if (err.name === 'AbortError') return;
          fetchController = null;
          showState('error');
        });
    }

    function clearSelection() {
      hiddenDate.value = '';
      hiddenTime.value = '';
      submitBtn.disabled = true;
      // Quitar resaltado de cualquier slot seleccionado
      elGrid.querySelectorAll('.slot-btn').forEach(function (btn) {
        btn.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-500', 'bg-indigo-50');
        btn.classList.add('border-slate-200');
        const check = btn.querySelector('.slot-check');
        if (check) check.style.display = 'none';
      });
    }

    function formatPrice(price, currency) {
      if (!price && price !== 0) return '';
      return currency + ' ' + Number(price).toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
    }

    function renderSlots(slots, date) {
      elGrid.innerHTML = '';

      slots.forEach(function (slot) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.classList.add('slot-btn', 'relative', 'flex', 'flex-col', 'items-center', 'justify-center',
          'rounded-xl', 'border', 'px-2', 'py-2.5', 'text-center', 'transition-all', 'duration-150', 'text-xs');

        const timeLabel = document.createElement('span');
        timeLabel.className = 'font-semibold text-sm leading-tight';
        timeLabel.textContent = slot.start_at + ' – ' + slot.end_at;

        const priceLabel = document.createElement('span');
        priceLabel.className = 'mt-0.5 leading-tight';

        const statusLabel = document.createElement('span');
        statusLabel.className = 'mt-0.5 font-medium leading-tight';

        // Checkmark (oculto por defecto)
        const check = document.createElement('span');
        check.className = 'slot-check absolute top-1.5 right-1.5 text-indigo-600';
        check.style.display = 'none';
        check.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';

        if (slot.status === 'AVAILABLE') {
          btn.classList.add('border-green-300', 'bg-green-50', 'text-green-800', 'hover:border-green-500', 'hover:bg-green-100', 'cursor-pointer');
          timeLabel.classList.add('text-green-900');
          priceLabel.classList.add('text-green-700');
          if (slot.has_discount) {
            priceLabel.innerHTML =
              '<span class="line-through text-slate-400 mr-1">' + formatPrice(slot.original_price, slot.currency) + '</span>' +
              '<span class="text-green-700 font-semibold">' + formatPrice(slot.price, slot.currency) + '</span>';
          } else {
            priceLabel.textContent = formatPrice(slot.price, slot.currency);
          }

          btn.addEventListener('click', function () {
            // Deselect others
            elGrid.querySelectorAll('.slot-btn').forEach(function (b) {
              b.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-500', 'bg-indigo-50');
              if (b.classList.contains('border-green-300') || b.classList.contains('border-indigo-500')) {
                b.classList.remove('border-indigo-500');
                b.classList.add('border-green-300', 'bg-green-50');
              }
              const c = b.querySelector('.slot-check');
              if (c) c.style.display = 'none';
            });

            // Select this one
            btn.classList.remove('border-green-300', 'bg-green-50');
            btn.classList.add('ring-2', 'ring-indigo-500', 'border-indigo-500', 'bg-indigo-50');
            check.style.display = '';

            hiddenDate.value = date;
            hiddenTime.value = slot.start_at;
            submitBtn.disabled = false;
          });

        } else if (slot.status === 'UNAVAILABLE') {
          btn.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-400', 'cursor-not-allowed');
          btn.disabled = true;
          timeLabel.classList.add('text-slate-400');
          statusLabel.textContent = 'Ocupado';
          statusLabel.classList.add('text-slate-400');

        } else if (slot.status === 'BLOCKED') {
          btn.classList.add('border-orange-200', 'bg-orange-50', 'text-orange-700', 'cursor-not-allowed');
          btn.disabled = true;
          timeLabel.classList.add('text-orange-700');
          statusLabel.textContent = slot.reason || 'Bloqueado';
          statusLabel.classList.add('text-orange-600', 'truncate', 'max-w-full');

        } else if (slot.status === 'PAST') {
          btn.classList.add('border-slate-100', 'bg-slate-50', 'text-slate-300', 'cursor-not-allowed');
          btn.disabled = true;
          timeLabel.classList.add('text-slate-300');
        }

        btn.appendChild(timeLabel);
        if (slot.status === 'AVAILABLE') {
          btn.appendChild(priceLabel);
        } else if (slot.status === 'UNAVAILABLE' || slot.status === 'BLOCKED') {
          btn.appendChild(statusLabel);
        }
        btn.appendChild(check);

        elGrid.appendChild(btn);
      });

      showState('grid');
    }

    // Escuchar cambios en cancha y fecha
    fieldSelect.addEventListener('change', tryFetch);
    datePicker.addEventListener('change', tryFetch);

    // Inicializar: si ya hay cancha seleccionada (por old() en caso de error de validación), disparar
    if (fieldSelect.value && datePicker.value) {
      tryFetch();
    }
  })();

  // ── Modal recurrente manual ──
  document.getElementById('modal-recurring').addEventListener('click', function(e) {
    if (e.target === this || e.target.classList.contains('backdrop-blur-sm')) {
      this.style.display = 'none';
    }
  });

  (function () {
    const fieldSelect   = document.getElementById('recur-field-id');
    const datePicker    = document.getElementById('recur-date-picker');
    const hiddenDate    = document.getElementById('recur-hidden-date');
    const hiddenTime    = document.getElementById('recur-hidden-time');
    const submitBtn     = document.getElementById('recur-submit-btn');

    const elPlaceholder = document.getElementById('recur-slots-placeholder');
    const elLoading     = document.getElementById('recur-slots-loading');
    const elEmpty       = document.getElementById('recur-slots-empty');
    const elError       = document.getElementById('recur-slots-error');
    const elGrid        = document.getElementById('recur-slots-grid');

    let fetchController = null;

    function showState(state) {
      elPlaceholder.style.display = state === 'placeholder' ? '' : 'none';
      elLoading.style.display     = state === 'loading'     ? '' : 'none';
      elEmpty.style.display       = state === 'empty'       ? '' : 'none';
      elError.style.display       = state === 'error'       ? '' : 'none';
      elGrid.style.display        = state === 'grid'        ? '' : 'none';
    }

    function clearSelection() {
      hiddenDate.value = '';
      hiddenTime.value = '';
      submitBtn.disabled = true;
      elGrid.querySelectorAll('.recur-slot-btn').forEach(function(b) {
        b.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-500', 'bg-indigo-50');
        b.classList.add('border-green-300', 'bg-green-50');
        const c = b.querySelector('.slot-check');
        if (c) c.style.display = 'none';
      });
    }

    function tryFetch() {
      const fieldId = fieldSelect.value;
      const date    = datePicker.value;

      if (!fieldId || !date) { showState('placeholder'); clearSelection(); return; }

      clearSelection();

      if (fetchController) fetchController.abort();
      fetchController = new AbortController();

      showState('loading');

      fetch('/fields/' + fieldId + '/availability?date=' + encodeURIComponent(date), { signal: fetchController.signal })
        .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
        .then(function(data) {
          fetchController = null;
          if (!data.slots || data.slots.length === 0) { showState('empty'); return; }
          renderSlots(data.slots, date);
        })
        .catch(function(err) {
          if (err.name === 'AbortError') return;
          fetchController = null;
          showState('error');
        });
    }

    function renderSlots(slots, date) {
      elGrid.innerHTML = '';
      slots.forEach(function(slot) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.classList.add('recur-slot-btn', 'relative', 'flex', 'flex-col', 'items-center', 'justify-center',
          'rounded-xl', 'border', 'px-2', 'py-2.5', 'text-center', 'transition-all', 'duration-150', 'text-xs');

        const timeLabel = document.createElement('span');
        timeLabel.className = 'font-semibold text-sm leading-tight';
        timeLabel.textContent = slot.start_at + ' – ' + slot.end_at;

        const check = document.createElement('span');
        check.className = 'slot-check absolute top-1.5 right-1.5 text-indigo-600';
        check.style.display = 'none';
        check.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';

        if (slot.status === 'AVAILABLE') {
          btn.classList.add('border-green-300', 'bg-green-50', 'text-green-800', 'hover:border-green-500', 'hover:bg-green-100', 'cursor-pointer');
          timeLabel.classList.add('text-green-900');
          btn.addEventListener('click', function() {
            elGrid.querySelectorAll('.recur-slot-btn').forEach(function(b) {
              b.classList.remove('ring-2', 'ring-indigo-500', 'border-indigo-500', 'bg-indigo-50');
              b.classList.add('border-green-300', 'bg-green-50');
              const c = b.querySelector('.slot-check');
              if (c) c.style.display = 'none';
            });
            btn.classList.remove('border-green-300', 'bg-green-50');
            btn.classList.add('ring-2', 'ring-indigo-500', 'border-indigo-500', 'bg-indigo-50');
            check.style.display = '';
            hiddenDate.value = date;
            hiddenTime.value = slot.start_at;
            submitBtn.disabled = false;
          });
        } else {
          btn.classList.add('border-slate-200', 'bg-slate-50', 'text-slate-400', 'cursor-not-allowed');
          btn.disabled = true;
          const statusLabel = document.createElement('span');
          statusLabel.className = 'mt-0.5 font-medium leading-tight text-slate-400';
          statusLabel.textContent = slot.status === 'PAST' ? 'Pasado' : 'Ocupado';
          btn.appendChild(timeLabel);
          btn.appendChild(statusLabel);
          btn.appendChild(check);
          elGrid.appendChild(btn);
          return;
        }

        btn.appendChild(timeLabel);
        btn.appendChild(check);
        elGrid.appendChild(btn);
      });
      showState('grid');
    }

    fieldSelect.addEventListener('change', tryFetch);
    datePicker.addEventListener('change', tryFetch);

    // Client search for recurring modal
    (function() {
      const searchInput   = document.getElementById('recur-client-search');
      const dropdown      = document.getElementById('recur-client-dropdown');
      const hiddenInput   = document.getElementById('recur-client-user-id');
      const selectedPanel = document.getElementById('recur-selected-client-panel');
      const selectedName  = document.getElementById('recur-selected-client-name');
      const selectedEmail = document.getElementById('recur-selected-client-email');
      const deselectBtn   = document.getElementById('recur-deselect-client');
      const wrapper       = document.getElementById('recur-search-wrapper');

      let timer = null;

      function escapeHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      }

      function selectClient(u) {
        hiddenInput.value = u.id;
        selectedName.textContent = u.name;
        selectedEmail.textContent = u.email;
        selectedPanel.style.display = 'flex';
        wrapper.style.display = 'none';
        dropdown.style.display = 'none';
        searchInput.value = '';
      }

      deselectBtn.addEventListener('click', function() {
        hiddenInput.value = '';
        selectedPanel.style.display = 'none';
        wrapper.style.display = 'block';
        searchInput.value = '';
      });

      searchInput.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        if (q.length < 3) { dropdown.style.display = 'none'; return; }
        timer = setTimeout(function() {
          fetch('{{ route("va.users.search") }}?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
              dropdown.innerHTML = '';
              if (!data.length) {
                dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">Sin resultados</div>';
              } else {
                data.forEach(function(u) {
                  const item = document.createElement('button');
                  item.type = 'button';
                  item.className = 'w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 transition-colors border-b border-slate-100 last:border-0';
                  item.innerHTML = '<span class="font-semibold text-slate-900">' + escapeHtml(u.name) + '</span><span class="text-slate-500 ml-2">' + escapeHtml(u.email) + '</span>';
                  item.addEventListener('click', function() { selectClient(u); });
                  dropdown.appendChild(item);
                });
              }
              dropdown.style.display = 'block';
            })
            .catch(function() { dropdown.style.display = 'none'; });
        }, 300);
      });

      document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) dropdown.style.display = 'none';
      });
    })();
  })();
</script>

@endsection
