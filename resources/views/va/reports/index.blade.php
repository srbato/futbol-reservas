@extends('layouts.admin')

@section('title', 'Reportes')
@section('page_title', 'Reportes')
@section('page_subtitle', 'Ingresos y actividad de tus complejos')

@push('styles')
<style>
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
  }
  .kpi-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 20px 22px;
  }
  .kpi-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #999;
    margin-bottom: 10px;
  }
  .kpi-value {
    font-size: 32px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.02em;
  }
  .kpi-sub {
    font-size: 13px;
    color: #888;
    margin-top: 6px;
  }
  .occupancy-item {
    padding: 14px 16px;
    border: 1px solid #ececec;
    border-radius: 12px;
    margin-bottom: 10px;
  }
  .occupancy-item:last-child { margin-bottom: 0; }
  .occupancy-bar-track {
    width: 100%;
    height: 8px;
    background: #f1f1f1;
    border-radius: 999px;
    overflow: hidden;
    margin-top: 12px;
  }
  .occupancy-bar-fill {
    height: 8px;
    background: #111;
    border-radius: 999px;
  }
</style>
@endpush

@section('content')

  {{-- Filtros --}}
  <div class="filter-bar" style="justify-content:space-between; align-items:flex-end;">
    <form method="GET" action="{{ route('va.reports') }}" class="form-row">
      <div class="form-group">
        <label class="form-label">Cancha</label>
        <select name="field_id" class="form-control" style="width:auto;">
          <option value="">Todas</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}" {{ (string)$fieldId === (string)$field->id ? 'selected' : '' }}>
              {{ $field->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Desde</label>
        <input type="date" name="from" value="{{ $from->toDateString() }}"
               class="form-control" style="width:auto;">
      </div>

      <div class="form-group">
        <label class="form-label">Hasta</label>
        <input type="date" name="to" value="{{ $to->toDateString() }}"
               class="form-control" style="width:auto;">
      </div>

      <button type="submit" class="btn btn-primary">Filtrar</button>
      <a href="{{ route('va.reports') }}" class="btn btn-ghost">Limpiar</a>
    </form>

    <a href="{{ route('va.reports.export', request()->query()) }}" class="btn btn-ghost">
      ↓ Exportar CSV
    </a>
  </div>

  {{-- KPIs --}}
  <div class="kpi-grid">
    <div class="kpi-card">
      <div class="kpi-label">Hoy</div>
      <div class="kpi-value">${{ number_format($todayRevenue, 0, ',', '.') }}</div>
      <div class="kpi-sub">{{ $todayReservations }} reservas</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Esta semana</div>
      <div class="kpi-value">${{ number_format($weekRevenue, 0, ',', '.') }}</div>
      <div class="kpi-sub">{{ $weekReservations }} reservas</div>
    </div>
    <div class="kpi-card">
      <div class="kpi-label">Este mes</div>
      <div class="kpi-value">${{ number_format($monthRevenue, 0, ',', '.') }}</div>
      <div class="kpi-sub">{{ $monthReservations }} reservas</div>
    </div>
  </div>

  {{-- Gráfico reservas --}}
  <div class="admin-card" style="margin-bottom:20px;">
    <div class="section-title" style="margin-bottom:18px;">Reservas por día</div>
    <canvas id="reservationsChart" height="100"></canvas>
  </div>

  {{-- Gráfico ingresos --}}
  <div class="admin-card" style="margin-bottom:20px;">
    <div class="section-title" style="margin-bottom:18px;">Ingresos por día</div>
    <canvas id="revenueChart" height="100"></canvas>
  </div>

  {{-- Ocupación por cancha --}}
  <div class="admin-card">
    <div class="section-title" style="margin-bottom:18px;">Ocupación por cancha en el rango</div>

    @if(empty($fieldOccupancy))
      <div class="empty-state">No hay datos para el rango seleccionado.</div>
    @else
      @foreach($fieldOccupancy as $item)
        <div class="occupancy-item">
          <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <div>
              <strong>{{ $item['field']->name }}</strong>
              <div style="font-size:12px; color:#888; margin-top:2px;">{{ $item['field']->venue->name }}</div>
            </div>
            <div style="text-align:right;">
              <strong>{{ $item['reserved_slots'] }}/{{ $item['total_slots'] }} slots — {{ $item['occupancy_percent'] }}%</strong>
              <div style="font-size:13px; color:#888; margin-top:2px;">${{ number_format($item['revenue'], 0, ',', '.') }}</div>
            </div>
          </div>
          <div class="occupancy-bar-track">
            <div class="occupancy-bar-fill" style="width:{{ $item['occupancy_percent'] }}%;"></div>
          </div>
        </div>
      @endforeach
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
          backgroundColor: '#111',
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } }, x: { grid: { display: false } } }
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
          borderColor: '#111',
          backgroundColor: 'rgba(0,0,0,.05)',
          fill: true,
          pointBackgroundColor: '#111',
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: '#f0f0f0' } }, x: { grid: { display: false } } }
      }
    });
  </script>

@endsection
