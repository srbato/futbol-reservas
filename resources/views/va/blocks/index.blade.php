@extends('layouts.admin')

@section('title', 'Bloqueos de horarios')
@section('page_title', 'Bloqueos')
@section('page_subtitle', 'Bloqueá horarios manualmente por mantenimiento, torneos o eventos')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_blocks',
  'helpTitle' => 'Bloqueos de horario',
  'helpText'  => 'Usá esta sección para bloquear horarios específicos en tus canchas. Los horarios bloqueados no aparecen disponibles para reservar. Es útil para mantenimientos, torneos, eventos privados o cualquier uso interno que no quieras que se reserve online.',
])

{{-- ── Formulario de creación ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm mb-6">

  <div class="px-6 py-5 border-b border-slate-100">
    <div class="flex items-start gap-3">
      <div class="w-9 h-9 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0">
        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
      </div>
      <div>
        <h2 class="text-base font-bold text-slate-900">Nuevo bloqueo</h2>
        <p class="text-sm text-slate-500 mt-0.5">Los horarios bloqueados no aparecen disponibles para reservar online.</p>
      </div>
    </div>
  </div>

  <div class="px-6 py-5">
    <form method="POST" action="{{ route('va.blocks.store') }}">
      @csrf
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

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
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Fecha</label>
          <input type="date" name="date" required
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Hora inicio</label>
          <input type="time" name="start_time" required
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Hora fin</label>
          <input type="time" name="end_time" required
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Motivo <span class="text-slate-400 normal-case font-normal">(opcional)</span></label>
          <input name="reason" placeholder="Ej: mantenimiento"
                 class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
        </div>

      </div>

      <div class="mt-4">
        <button type="submit"
                class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
          Guardar bloqueo
        </button>
      </div>
    </form>
  </div>

</div>

{{-- ── Lista de bloqueos ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

  <div class="px-6 py-5 border-b border-slate-100">
    <h2 class="text-base font-bold text-slate-900">Bloqueos cargados</h2>
    <p class="text-sm text-slate-500 mt-0.5">Horarios actualmente bloqueados en tus canchas.</p>
  </div>

  @if($blocks->isEmpty())
    <div class="px-6 py-12 text-center">
      <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
      </div>
      <p class="text-sm text-slate-400">No hay bloqueos cargados.</p>
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-slate-50 border-b border-slate-100">
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Fecha</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Cancha</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Horario</th>
            <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3">Motivo</th>
            <th class="px-6 py-3"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($blocks as $block)
            <tr class="hover:bg-slate-50 transition-colors">
              <td class="px-6 py-3.5 font-semibold text-slate-900 whitespace-nowrap">
                {{ \Carbon\Carbon::parse($block->date)->format('d/m/Y') }}
              </td>
              <td class="px-6 py-3.5 text-slate-700">{{ $block->field->venue->name }} / {{ $block->field->name }}</td>
              <td class="px-6 py-3.5 text-slate-600 whitespace-nowrap">
                <span class="inline-flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ $block->start_time }} – {{ $block->end_time }}
                </span>
              </td>
              <td class="px-6 py-3.5 text-slate-600">
                @if($block->reason)
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                    {{ $block->reason }}
                  </span>
                @else
                  <span class="text-slate-300">—</span>
                @endif
              </td>
              <td class="px-6 py-3.5 text-right">
                <form method="POST" action="{{ route('va.blocks.destroy', $block) }}"
                      onsubmit="return confirm('¿Eliminar este bloqueo?')">
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
