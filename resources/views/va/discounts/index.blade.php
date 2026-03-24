@extends('layouts.admin')

@section('title', 'Descuentos')
@section('page_title', 'Descuentos')
@section('page_subtitle', 'Configurá precios promocionales por día o por horario')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_discounts',
  'helpTitle' => 'Descuentos',
  'helpText'  => 'Acá configurás precios especiales para tus canchas. Podés crear descuentos por día de la semana (ej. lunes más barato), por fecha puntual, por franja horaria o combinar varios criterios. Los descuentos se aplican automáticamente cuando los usuarios reservan en esos horarios.',
])

{{-- ── SECCIÓN: Descuentos por reserva recurrente ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6">

  {{-- Header --}}
  <div class="px-6 py-5 border-b border-slate-100">
    <div class="flex items-start gap-3">
      <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
      </div>
      <div>
        <h2 class="text-base font-bold text-slate-900">Descuentos por reserva recurrente</h2>
        <p class="text-sm text-slate-500 mt-0.5">El sistema aplica el tier más alto que corresponda según la cantidad de turnos reservados a la vez.</p>
      </div>
    </div>
  </div>

  {{-- Formulario --}}
  <div class="px-6 py-5 border-b border-slate-100">
    <form method="POST" action="{{ route('va.recurring_discounts.store') }}">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cancha</label>
          <select name="field_id" required
                  class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
            <option value="">Seleccionar</option>
            @foreach($fields as $field)
              <option value="{{ $field->id }}">{{ $field->venue->name }} — {{ $field->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Desde N turnos</label>
          <select name="min_occurrences" required
                  class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
            @foreach([2,3,4,5,6,8,10,12] as $n)
              <option value="{{ $n }}">{{ $n }} turnos</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Descuento (%)</label>
          <input type="number" step="0.01" min="1" max="100" name="discount_percentage" required
                 placeholder="Ej: 10"
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

        <div>
          <button type="submit"
                  class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
            Guardar tier
          </button>
        </div>

      </div>
    </form>
  </div>

  {{-- Tabla de tiers --}}
  @if($recurringDiscounts->isNotEmpty())
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-100">
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Cancha</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Desde</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Descuento</th>
            <th class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($recurringDiscounts as $rd)
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-6 py-3.5 text-slate-700 font-medium">{{ $rd->field->venue->name }} / {{ $rd->field->name }}</td>
              <td class="px-6 py-3.5 text-slate-600">{{ $rd->min_occurrences }}+ turnos</td>
              <td class="px-6 py-3.5">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                  {{ $rd->discount_percentage }}%
                </span>
              </td>
              <td class="px-6 py-3.5 text-right">
                <form method="POST" action="{{ route('va.recurring_discounts.destroy', $rd) }}"
                      onsubmit="return confirm('¿Eliminar este tier?')">
                  @csrf
                  <button type="submit"
                          class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-500 text-white text-xs font-semibold rounded-lg hover:shadow-md transition-all duration-200">
                    Eliminar
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="px-6 py-8 text-center text-sm text-slate-400">
      No hay tiers configurados todavía.
    </div>
  @endif

</div>

{{-- ── SECCIÓN: Descuento puntual ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6">

  {{-- Header --}}
  <div class="px-6 py-5 border-b border-slate-100">
    <div class="flex items-start gap-3">
      <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/>
        </svg>
      </div>
      <div>
        <h2 class="text-base font-bold text-slate-900">Crear descuento puntual</h2>
        <p class="text-sm text-slate-500 mt-0.5">Por día de semana, fecha exacta o franja horaria. Sin horas: aplica todo el día.</p>
      </div>
    </div>
  </div>

  {{-- Formulario --}}
  <div class="px-6 py-5">
    <form method="POST" action="{{ route('va.discounts.store') }}">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cancha</label>
          <select name="field_id" required
                  class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
            <option value="">Seleccionar</option>
            @foreach($fields as $field)
              <option value="{{ $field->id }}">{{ $field->venue->name }} — {{ $field->name }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Día de semana</label>
          <select name="day_of_week"
                  class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
            <option value="">Sin día fijo</option>
            <option value="1">Lunes</option>
            <option value="2">Martes</option>
            <option value="3">Miércoles</option>
            <option value="4">Jueves</option>
            <option value="5">Viernes</option>
            <option value="6">Sábado</option>
            <option value="0">Domingo</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Fecha puntual</label>
          <input type="date" name="date"
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Precio promo ($)</label>
          <input type="number" step="0.01" name="discount_price" required
                 placeholder="Ej: 3500"
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Hora inicio</label>
          <input type="time" name="start_time"
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Hora fin</label>
          <input type="time" name="end_time"
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Etiqueta</label>
          <input name="label" placeholder="Ej: Promo tarde"
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

        <div>
          <button type="submit"
                  class="w-full px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
            Guardar descuento
          </button>
        </div>

      </div>
    </form>
  </div>

</div>

{{-- ── SECCIÓN: Lista de descuentos puntuales ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

  <div class="px-6 py-5 border-b border-slate-100">
    <h2 class="text-base font-bold text-slate-900">Descuentos cargados</h2>
    <p class="text-sm text-slate-500 mt-0.5">Todos los descuentos puntuales configurados en tus canchas.</p>
  </div>

  @if($discounts->isEmpty())
    <div class="px-6 py-12 text-center text-sm text-slate-400">
      No hay descuentos cargados.
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-100">
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Cancha</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Día / Fecha</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Horario</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Precio</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Etiqueta</th>
            <th class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($discounts as $discount)
            @php
              $days = [0=>'Domingo',1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado'];
            @endphp
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-6 py-3.5 font-medium text-slate-700">{{ $discount->field->venue->name }} / {{ $discount->field->name }}</td>
              <td class="px-6 py-3.5 text-slate-600">
                @if($discount->date)
                  {{ $discount->date->format('d/m/Y') }}
                @elseif(!is_null($discount->day_of_week))
                  {{ $days[$discount->day_of_week] }}
                @else
                  <span class="text-slate-400">Todos los días</span>
                @endif
              </td>
              <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                {{ $discount->start_time ?? 'Todo el día' }}
                @if($discount->end_time) – {{ $discount->end_time }} @endif
              </td>
              <td class="px-6 py-3.5 font-bold text-slate-900">${{ number_format($discount->discount_price, 0, ',', '.') }}</td>
              <td class="px-6 py-3.5">
                @if($discount->label)
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                    {{ $discount->label }}
                  </span>
                @else
                  <span class="text-slate-300">—</span>
                @endif
              </td>
              <td class="px-6 py-3.5 text-right">
                <form method="POST" action="{{ route('va.discounts.destroy', $discount) }}"
                      onsubmit="return confirm('¿Eliminar este descuento?')">
                  @csrf
                  <button type="submit"
                          class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-500 text-white text-xs font-semibold rounded-lg hover:shadow-md transition-all duration-200">
                    Eliminar
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

</div>

@endsection
