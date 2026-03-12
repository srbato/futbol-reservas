@extends('layouts.admin')

@section('title','Reportes')
@section('page_title','Reportes')
@section('page_subtitle','Ingresos y reservas')

@section('content')
  <div class="admin-card" style="margin-bottom:20px;">
    <form method="GET" action="{{ route('va.reports') }}"
          style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
      
      <div>
        <label style="display:block; font-size:12px; color:#666;">Cancha</label>
        <select name="field_id" style="padding:10px; border:1px solid #ddd; border-radius:10px;">
          <option value="">Todas</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}" {{ (string)$fieldId === (string)$field->id ? 'selected' : '' }}>
              {{ $field->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Desde</label>
        <input type="date" name="from" value="{{ $from->toDateString() }}"
               style="padding:10px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Hasta</label>
        <input type="date" name="to" value="{{ $to->toDateString() }}"
               style="padding:10px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <button type="submit"
              style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
        Filtrar
      </button>

      <a href="{{ route('va.reports') }}"
         style="padding:10px 14px; border:1px solid #ddd; border-radius:10px; text-decoration:none; color:#111;">
        Limpiar
      </a>

      <a href="{{ route('va.reports.export', request()->query()) }}"
        style="padding:10px 14px;margin-left:490px;border:1px solid #ddd;border-radius:10px;text-decoration:none;color:#111;">
        Exportar CSV
      </a>
    </form>
  </div>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px; margin-bottom:20px;">
    <div class="admin-card">
      <h3>Hoy</h3>
      <p><strong>${{ number_format($todayRevenue,0,',','.') }}</strong></p>
      <p>{{ $todayReservations }} reservas</p>
    </div>

    <div class="admin-card">
      <h3>Esta semana</h3>
      <p><strong>${{ number_format($weekRevenue,0,',','.') }}</strong></p>
      <p>{{ $weekReservations }} reservas</p>
    </div>

    <div class="admin-card">
      <h3>Este mes</h3>
      <p><strong>${{ number_format($monthRevenue,0,',','.') }}</strong></p>
      <p>{{ $monthReservations }} reservas</p>
    </div>
  </div>

  <div class="admin-card" style="margin-bottom:20px;">
    <h3 style="margin-top:0;">Reservas por día</h3>
    <canvas id="reservationsChart" height="100"></canvas>
  </div>

  <div class="admin-card">
    <h3 style="margin-top:0;">Ingresos por día</h3>
    <canvas id="revenueChart" height="100"></canvas>
  </div>

  <div class="admin-card" style="margin-top:20px;">
  <h3 style="margin-top:0;">Ocupación por cancha en el rango</h3>

  @if(empty($fieldOccupancy))
    <p>No hay datos para mostrar.</p>
  @else
    <div style="display:flex; flex-direction:column; gap:12px; margin-top:12px;">
      @foreach($fieldOccupancy as $item)
        <div style="padding:12px; border:1px solid #eee; border-radius:12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; gap:12px; flex-wrap:wrap;">
            <div>
              <strong>{{ $item['field']->name }}</strong>
              <div style="font-size:12px; color:#666;">
                {{ $item['field']->venue->name }}
              </div>
            </div>

            <div style="font-weight:700;">
              {{ $item['reserved_slots'] }}/{{ $item['total_slots'] }} slots
              — {{ $item['occupancy_percent'] }}%
            </div>
          </div>

          <div style="width:100%; height:14px; background:#f1f1f1; border-radius:999px; overflow:hidden;">
            <div style="height:14px; width: {{ $item['occupancy_percent'] }}%; background:#111;"></div>
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
        labels: labels,
        datasets: [{
          label: 'Reservas',
          data: reservationsPerDay
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true }
        }
      }
    });

    new Chart(document.getElementById('revenueChart'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Ingresos',
          data: revenuePerDay,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: true }
        }
      }
    });
  </script>
@endsection