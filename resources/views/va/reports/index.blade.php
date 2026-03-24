@extends('layouts.admin')

@section('title', 'Reportes')
@section('page_title', 'Reportes')
@section('page_subtitle', 'Ingresos y actividad de tus complejos')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_reports',
  'helpTitle' => 'Reportes',
  'helpText'  => 'Acá podés analizar el rendimiento de tu complejo: ingresos por período, reservas por cancha, deporte más reservado y más. Filtrá por rango de fechas para ver la evolución a lo largo del tiempo.',
])

{{-- ── Filtros ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm px-6 py-4 mb-6">
  <form method="GET" action="{{ route('va.reports') }}">
    <div class="flex flex-wrap items-end gap-4">

      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Cancha</label>
        <select name="field_id"
                class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
          <option value="">Todas</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}" {{ (string)$fieldId === (string)$field->id ? 'selected' : '' }}>
              {{ $field->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Desde</label>
        <input type="date" name="from" value="{{ $from->toDateString() }}"
               class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
      </div>

      <div>
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Hasta</label>
        <input type="date" name="to" value="{{ $to->toDateString() }}"
               class="px-3 py-2 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:shadow-indigo-100">
      </div>

      <div class="flex items-center gap-2">
        <button type="submit"
                class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
          Filtrar
        </button>
        <a href="{{ route('va.reports') }}"
           class="px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all duration-200">
          Limpiar
        </a>
      </div>

      <div class="ml-auto">
        <a href="{{ route('va.reports.export', request()->query()) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-600 text-sm font-medium rounded-xl hover:bg-slate-50 transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Exportar CSV
        </a>
      </div>

    </div>
  </form>
</div>

{{-- ── KPIs ── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Hoy</p>
    <p class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">${{ number_format($todayRevenue, 0, ',', '.') }}</p>
    <p class="text-sm text-slate-500 mt-2">{{ $todayReservations }} reservas</p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Esta semana</p>
    <p class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">${{ number_format($weekRevenue, 0, ',', '.') }}</p>
    <p class="text-sm text-slate-500 mt-2">{{ $weekReservations }} reservas</p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Este mes</p>
    <p class="text-3xl font-extrabold text-slate-900 leading-none tracking-tight">${{ number_format($monthRevenue, 0, ',', '.') }}</p>
    <p class="text-sm text-slate-500 mt-2">{{ $monthReservations }} reservas</p>
  </div>

  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Tasa de cancelación</p>
    <p class="text-3xl font-extrabold leading-none tracking-tight
              {{ $cancellationRate > 20 ? 'text-red-600' : ($cancellationRate > 10 ? 'text-amber-600' : 'text-emerald-600') }}">
      {{ $cancellationRate }}%
    </p>
    <p class="text-sm text-slate-500 mt-2">{{ $totalCancelled }} de {{ $totalCreated }} creadas</p>
  </div>

</div>

{{-- ── Gráfico reservas por día ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
  <h2 class="text-base font-bold text-slate-900 mb-5">Reservas por día</h2>
  <canvas id="reservationsChart" height="100"></canvas>
</div>

{{-- ── Gráfico ingresos por día ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
  <h2 class="text-base font-bold text-slate-900 mb-5">Ingresos por día</h2>
  <canvas id="revenueChart" height="100"></canvas>
</div>

{{-- ── Gráficos mensuales (solo si el rango supera 31 días) ── --}}
@if($showMonthlyChart)
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
  <h2 class="text-base font-bold text-slate-900 mb-5">Reservas por mes</h2>
  <canvas id="monthlyReservationsChart" height="80"></canvas>
</div>
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
  <h2 class="text-base font-bold text-slate-900 mb-5">Ingresos por mes</h2>
  <canvas id="monthlyRevenueChart" height="80"></canvas>
</div>
@endif

{{-- ── Hora pico ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
  <h2 class="text-base font-bold text-slate-900 mb-1">Horarios más reservados</h2>
  <p class="text-sm text-slate-500 mb-5">Reservas PAID por hora de inicio en el rango seleccionado</p>
  @if(array_sum($peakHourData) === 0)
    <div class="py-8 text-center text-sm text-slate-400">No hay datos para el rango seleccionado.</div>
  @else
    <canvas id="peakHoursChart" height="80"></canvas>
  @endif
</div>

{{-- ── Ocupación por cancha ── --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
  <h2 class="text-base font-bold text-slate-900 mb-5">Ocupación por cancha en el rango</h2>

  @if(empty($fieldOccupancy))
    <div class="py-8 text-center text-sm text-slate-400">No hay datos para el rango seleccionado.</div>
  @else
    <div class="space-y-3">
      @foreach($fieldOccupancy as $item)
        <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50">
          <div class="flex justify-between items-start gap-4 flex-wrap mb-3">
            <div>
              <p class="font-semibold text-slate-900 text-sm">{{ $item['field']->name }}</p>
              <p class="text-xs text-slate-500 mt-0.5">{{ $item['field']->venue->name }}</p>
            </div>
            <div class="text-right">
              <p class="font-bold text-slate-900 text-sm">{{ $item['reserved_slots'] }}/{{ $item['total_slots'] }} slots &mdash; {{ $item['occupancy_percent'] }}%</p>
              <p class="text-xs text-slate-500 mt-0.5">${{ number_format($item['revenue'], 0, ',', '.') }}</p>
            </div>
          </div>
          <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
            <div class="h-2 bg-indigo-600 rounded-full transition-all duration-500"
                 style="width:{{ $item['occupancy_percent'] }}%;"></div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const labels = @json($labels);
  const reservationsPerDay = @json($reservationsPerDay);
  const revenuePerDay = @json($revenuePerDay);

  new Chart(document.getElementById('reservationsChart'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Reservas',
        data: reservationsPerDay,
        backgroundColor: '#4f46e5',
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
  });

  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Ingresos',
        data: revenuePerDay,
        tension: 0.35,
        borderColor: '#4f46e5',
        backgroundColor: 'rgba(79,70,229,.08)',
        fill: true,
        pointBackgroundColor: '#4f46e5',
        pointRadius: 4
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
  });

  @if($showMonthlyChart)
  new Chart(document.getElementById('monthlyReservationsChart'), {
    type: 'bar',
    data: {
      labels: @json($monthlyLabels),
      datasets: [{
        label: 'Reservas',
        data: @json($reservationsPerMonth),
        backgroundColor: '#4f46e5',
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
  });

  new Chart(document.getElementById('monthlyRevenueChart'), {
    type: 'line',
    data: {
      labels: @json($monthlyLabels),
      datasets: [{
        label: 'Ingresos',
        data: @json($revenuePerMonth),
        tension: 0.35,
        borderColor: '#4f46e5',
        backgroundColor: 'rgba(79,70,229,.08)',
        fill: true,
        pointBackgroundColor: '#4f46e5',
        pointRadius: 5
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
  });
  @endif

  @if(array_sum($peakHourData) > 0)
  new Chart(document.getElementById('peakHoursChart'), {
    type: 'bar',
    data: {
      labels: @json($peakHourLabels),
      datasets: [{
        label: 'Reservas',
        data: @json($peakHourData),
        backgroundColor: @json(array_map(fn($v) => $v === max($peakHourData) ? '#4ade80' : '#4f46e5', $peakHourData)),
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
    }
  });
  @endif
</script>

@endsection
