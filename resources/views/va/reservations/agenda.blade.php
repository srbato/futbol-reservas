@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.admin')

@section('title', $view === 'week' ? 'Agenda semanal' : 'Agenda del día')
@section('page_title', 'Agenda')
@section('page_subtitle', $view === 'week' ? 'Vista semanal — reservas confirmadas por cancha' : 'Vista diaria — reservas confirmadas por horario y cancha')

@push('styles')
<style>
  /* ── Toggle Día / Semana ── */
  .view-toggle {
    display: inline-flex;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 18px;
  }
  .view-toggle a {
    padding: 7px 18px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    color: #555;
    background: #fff;
    transition: background .15s, color .15s;
  }
  .view-toggle a:first-child { border-right: 1px solid #ddd; }
  .view-toggle a.active {
    background: #111;
    color: #fff;
  }

  /* ── Navegación de fecha ── */
  .agenda-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 18px;
  }
  .agenda-nav-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 1px solid #ddd;
    border-radius: 10px;
    text-decoration: none;
    color: #111;
    font-size: 16px;
    transition: background .15s;
  }
  .agenda-nav-arrow:hover { background: #f3f3f3; }
  .agenda-date-label {
    font-size: 17px;
    font-weight: 700;
    padding: 0 6px;
  }
  .agenda-today-badge {
    display: inline-block;
    background: #111;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    vertical-align: middle;
    margin-left: 4px;
  }

  /* ── Filtros ── */
  .agenda-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: flex-end;
    margin-bottom: 18px;
  }
  .agenda-filter-group label {
    display: block;
    font-size: 12px;
    color: #666;
    margin-bottom: 4px;
  }
  .agenda-filter-group input,
  .agenda-filter-group select {
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 14px;
    background: #fff;
  }

  /* ── Resumen ── */
  .agenda-summary {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 18px;
  }
  .agenda-stat {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 12px;
    padding: 12px 18px;
    min-width: 130px;
  }
  .agenda-stat-value {
    font-size: 22px;
    font-weight: 700;
    line-height: 1;
  }
  .agenda-stat-label {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
  }

  /* ── Grilla compartida ── */
  .agenda-scroll { overflow-x: auto; }
  .agenda-table {
    border-collapse: collapse;
    width: 100%;
    font-size: 14px;
  }
  .agenda-table th {
    background: #111;
    color: #fff;
    padding: 10px 14px;
    text-align: left;
    white-space: nowrap;
    font-weight: 600;
  }
  .agenda-table th.th-hour {
    width: 72px;
    text-align: center;
  }
  .agenda-table td {
    padding: 6px 8px;
    vertical-align: top;
    border-bottom: 1px solid #f0f0f0;
  }
  .agenda-table tr:last-child td { border-bottom: none; }
  .agenda-table td.td-hour {
    font-size: 13px;
    font-weight: 700;
    color: #666;
    text-align: center;
    white-space: nowrap;
    background: #fafafa;
    border-right: 2px solid #e8e8e8;
  }
  .agenda-table tr:nth-child(even) td { background: #fafafa; }
  .agenda-table tr:nth-child(even) td.td-hour { background: #f4f4f4; }

  /* ── Celdas con reserva (día) ── */
  .agenda-table td.day-col { min-width: 200px; }
  .res-cell {
    border-radius: 10px;
    padding: 9px 11px;
    border-left: 4px solid transparent;
  }
  .res-cell.status-PAID            { background: #d1e7dd; border-left-color: #198754; color: #0f5132; }
  .res-cell.status-PENDING_PAYMENT { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
  .res-cell.status-CANCELLED       { background: #e9ecef; border-left-color: #adb5bd; color: #6c757d; }
  .res-cell.status-EXPIRED         { background: #f8d7da; border-left-color: #dc3545; color: #842029; }
  .res-user   { font-weight: 700; font-size: 14px; }
  .res-status { font-size: 12px; margin-top: 3px; opacity: .85; }
  .res-time   { font-size: 12px; margin-top: 5px; font-weight: 600; }
  .res-price  { font-size: 13px; font-weight: 700; margin-top: 4px; }
  .res-code   { font-size: 11px; margin-top: 3px; opacity: .7; font-family: monospace; }
  .res-actions { margin-top: 8px; display: flex; gap: 6px; flex-wrap: wrap; }
  .res-btn-cancel {
    font-size: 12px; padding: 8px 12px; border: 1px solid currentColor;
    border-radius: 6px; background: transparent; cursor: pointer;
    font-weight: 600; opacity: .75; min-height: 36px;
    display: inline-flex; align-items: center;
  }
  .res-btn-cancel:hover { opacity: 1; }
  .res-btn-view {
    font-size: 12px; padding: 8px 12px; border: 1px solid currentColor;
    border-radius: 6px; text-decoration: none; font-weight: 600;
    opacity: .75; min-height: 36px; display: inline-flex; align-items: center;
  }
  .res-btn-view:hover { opacity: 1; }
  .free-cell {
    border-radius: 10px; padding: 8px 10px; background: #f8f9fa;
    color: #adb5bd; font-size: 12px; text-align: center;
  }

  /* ── Vista semanal ── */
  .week-table { min-width: 1000px; }
  .week-table th.th-day {
    min-width: 130px;
    text-align: center;
    font-weight: 600;
  }
  .week-table th.th-day.today-col {
    background: #1a6b3a;
  }
  .week-table td.week-col { min-width: 130px; text-align: center; }

  /* Reserva compacta (semana) */
  .wres-cell {
    border-radius: 8px;
    padding: 7px 9px;
    border-left: 3px solid transparent;
    text-align: left;
  }
  .wres-cell.status-PAID            { background: #d1e7dd; border-left-color: #198754; color: #0f5132; }
  .wres-cell.status-PENDING_PAYMENT { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
  .wres-cell.status-CANCELLED       { background: #e9ecef; border-left-color: #adb5bd; color: #6c757d; }
  .wres-cell.status-EXPIRED         { background: #f8d7da; border-left-color: #dc3545; color: #842029; }
  .wres-name  { font-weight: 700; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 110px; }
  .wres-price { font-size: 11px; font-weight: 600; margin-top: 2px; }
  .wres-acts  { margin-top: 5px; display: flex; gap: 4px; }
  .wres-btn {
    font-size: 11px; padding: 4px 8px; border: 1px solid currentColor;
    border-radius: 5px; background: transparent; cursor: pointer;
    font-weight: 600; opacity: .75; text-decoration: none;
    display: inline-flex; align-items: center;
  }
  .wres-btn:hover { opacity: 1; }

  .week-free   { color: #ccc; font-size: 11px; }
  .week-closed { color: #e8e8e8; font-size: 11px; }

  /* ── Footer ── */
  .agenda-footer {
    margin-top: 16px;
    font-size: 14px;
    color: #666;
  }
  .agenda-footer a { color: #111; font-weight: 600; }
</style>
@endpush

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_agenda',
  'helpTitle' => 'Agenda',
  'helpText'  => 'La agenda muestra en formato de grilla los turnos organizados por horario y cancha. Podés alternar entre vista diaria (todas las canchas de un día) y vista semanal (una cancha, los 7 días de la semana). Solo aparecen las reservas confirmadas (pagadas).',
])

{{-- ── Toggle Día / Semana ── --}}
<div class="view-toggle">
  <a href="{{ route('va.reservations.agenda', array_filter(['date' => $date->toDateString(), 'field_id' => $fieldId, 'view' => 'day'])) }}"
     class="{{ $view === 'day' ? 'active' : '' }}">
    Día
  </a>
  <a href="{{ route('va.reservations.agenda', array_filter(['date' => $date->toDateString(), 'field_id' => $fieldId, 'view' => 'week'])) }}"
     class="{{ $view === 'week' ? 'active' : '' }}">
    Semana
  </a>
</div>

{{-- ════════════════════════════════════════════════════════ VISTA DIARIA ══ --}}
@if($view === 'day')

@php
  $prevDate = $date->copy()->subDay()->toDateString();
  $nextDate = $date->copy()->addDay()->toDateString();
  $isToday  = $date->isToday();

  $paid    = $reservations->where('status', 'PAID')->count();
  $pending = $reservations->where('status', 'PENDING_PAYMENT')->count();
  $total   = $reservations->where('status', 'PAID')->sum('total_amount');
@endphp

<div class="agenda-nav">
  <a class="agenda-nav-arrow"
     href="{{ route('va.reservations.agenda', array_filter(['date' => $prevDate, 'field_id' => $fieldId])) }}"
     title="Día anterior">&#8592;</a>
  <span class="agenda-date-label">
    {{ ucfirst($date->translatedFormat('l d \d\e F')) }}
    @if($isToday)<span class="agenda-today-badge">Hoy</span>@endif
  </span>
  <a class="agenda-nav-arrow"
     href="{{ route('va.reservations.agenda', array_filter(['date' => $nextDate, 'field_id' => $fieldId])) }}"
     title="Día siguiente">&#8594;</a>
</div>

<form method="GET" action="{{ route('va.reservations.agenda') }}" class="agenda-filters">
  <input type="hidden" name="view" value="day">
  <div class="agenda-filter-group">
    <label>Fecha</label>
    <input type="date" name="date" value="{{ $date->toDateString() }}">
  </div>
  <div class="agenda-filter-group">
    <label>Cancha</label>
    <select name="field_id">
      <option value="">Todas las canchas</option>
      @foreach($fields as $field)
        <option value="{{ $field->id }}" {{ (string)($fieldId ?? '') === (string)$field->id ? 'selected' : '' }}>
          {{ $field->name }} — {{ $field->venue->name }}
        </option>
      @endforeach
    </select>
  </div>
  <button type="submit" class="btn-primary">Ver agenda</button>
  <a href="{{ route('va.reservations.agenda') }}" class="btn-ghost">Hoy</a>
</form>

<div class="agenda-summary">
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $reservations->count() }}</div>
    <div class="agenda-stat-label">Total reservas</div>
  </div>
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $paid }}</div>
    <div class="agenda-stat-label">Pagadas</div>
  </div>
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $pending }}</div>
    <div class="agenda-stat-label">Pendientes</div>
  </div>
  <div class="agenda-stat">
    <div class="agenda-stat-value">${{ number_format($total, 0, ',', '.') }}</div>
    <div class="agenda-stat-label">Ingresos del día</div>
  </div>
</div>

@if($fields->isEmpty())
  <div class="admin-card" style="color:#666; text-align:center; padding:40px;">
    No tenés canchas configuradas aún.
  </div>
@else
  <div class="admin-card" style="padding: 0; overflow: hidden;">
    <div class="agenda-scroll">
      <table class="agenda-table">
        <thead>
          <tr>
            <th class="th-hour">Hora</th>
            @foreach($fields as $field)
              <th>
                {{ $field->name }}
                <div style="font-size: 11px; font-weight: 400; opacity: .7; margin-top: 2px;">
                  {{ $field->venue->name }}
                </div>
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @forelse($slots as $slot)
            <tr>
              <td class="td-hour">{{ $slot }}</td>
              @foreach($fields as $field)
                @php
                  $key         = $field->id . '|' . $slot;
                  $reservation = $reservationMap[$key] ?? null;
                @endphp
                <td class="day-col">
                  @if($reservation)
                    <div class="res-cell status-{{ $reservation->status }}">
                      <div class="res-user">{{ $reservation->user->name ?? 'Usuario eliminado' }}</div>
                      <div class="res-status">{{ ReservationStatus::label($reservation->status) }}</div>
                      <div class="res-time">
                        {{ $reservation->start_at->format('H:i') }} – {{ $reservation->end_at->format('H:i') }}
                      </div>
                      @if($reservation->total_amount)
                        <div class="res-price">${{ number_format($reservation->total_amount, 0, ',', '.') }}</div>
                      @endif
                      @if($reservation->verification_code)
                        <div class="res-code">{{ $reservation->verification_code }}</div>
                      @endif
                      <div class="res-actions">
                        <a class="res-btn-view"
                           href="{{ route('reservations.show', $reservation) }}"
                           target="_blank">Ver</a>
                        @if(in_array($reservation->status, ['PENDING_PAYMENT', 'PAID']))
                          <form method="POST"
                                action="{{ route('va.reservations.cancel', $reservation) }}"
                                style="display:inline;"
                                onsubmit="return confirm('¿Cancelar la reserva de {{ addslashes($reservation->user->name ?? 'este usuario') }}?')">
                            @csrf
                            <button type="submit" class="res-btn-cancel">Cancelar</button>
                          </form>
                        @endif
                      </div>
                    </div>
                  @else
                    <div class="free-cell">Libre</div>
                  @endif
                </td>
              @endforeach
            </tr>
          @empty
            <tr>
              <td colspan="{{ $fields->count() + 1 }}" style="text-align:center; padding:32px; color:#aaa;">
                No hay horarios configurados para este día.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endif

{{-- ════════════════════════════════════════════════════════ VISTA SEMANAL ══ --}}
@elseif($view === 'week')

@php
  $prevWeek  = $weekStart->copy()->subDays(7)->toDateString();
  $nextWeek  = $weekStart->copy()->addDays(7)->toDateString();
  $weekEnd4Display = $weekStart->copy()->addDays(6);
  $weekTotal = $reservations->sum('total_amount');
  $weekCount = $reservations->count();

  $diasEs = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
@endphp

<div class="agenda-nav">
  <a class="agenda-nav-arrow"
     href="{{ route('va.reservations.agenda', array_filter(['date' => $prevWeek, 'field_id' => $fieldId, 'view' => 'week'])) }}"
     title="Semana anterior">&#8592;</a>
  <span class="agenda-date-label">
    {{ ucfirst($weekStart->translatedFormat('d \d\e F')) }}
    &ndash;
    {{ ucfirst($weekEnd4Display->translatedFormat('d \d\e F Y')) }}
  </span>
  <a class="agenda-nav-arrow"
     href="{{ route('va.reservations.agenda', array_filter(['date' => $nextWeek, 'field_id' => $fieldId, 'view' => 'week'])) }}"
     title="Semana siguiente">&#8594;</a>
</div>

<form method="GET" action="{{ route('va.reservations.agenda') }}" class="agenda-filters">
  <input type="hidden" name="view" value="week">
  <input type="hidden" name="date" value="{{ $weekStart->toDateString() }}">
  <div class="agenda-filter-group">
    <label>Cancha</label>
    <select name="field_id" onchange="this.form.submit()">
      @foreach($fields as $field)
        <option value="{{ $field->id }}" {{ (string)($fieldId ?? '') === (string)$field->id ? 'selected' : '' }}>
          {{ $field->name }} — {{ $field->venue->name }}
        </option>
      @endforeach
    </select>
  </div>
  <a href="{{ route('va.reservations.agenda', array_filter(['view' => 'week', 'field_id' => $fieldId])) }}"
     class="btn-ghost">Esta semana</a>
</form>

<div class="agenda-summary">
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $weekCount }}</div>
    <div class="agenda-stat-label">Reservas esta semana</div>
  </div>
  <div class="agenda-stat">
    <div class="agenda-stat-value">${{ number_format($weekTotal, 0, ',', '.') }}</div>
    <div class="agenda-stat-label">Ingresos de la semana</div>
  </div>
  @if($selectedField)
    <div class="agenda-stat">
      <div class="agenda-stat-value" style="font-size:16px;">{{ $selectedField->name }}</div>
      <div class="agenda-stat-label">{{ $selectedField->venue->name }}</div>
    </div>
  @endif
</div>

@if($fields->isEmpty())
  <div class="admin-card" style="color:#666; text-align:center; padding:40px;">
    No tenés canchas configuradas aún.
  </div>
@elseif(!$selectedField)
  <div class="admin-card" style="color:#666; text-align:center; padding:40px;">
    Seleccioná una cancha para ver la agenda semanal.
  </div>
@else
  <div class="admin-card" style="padding: 0; overflow: hidden;">
    <div class="agenda-scroll">
      <table class="agenda-table week-table">
        <thead>
          <tr>
            <th class="th-hour">Hora</th>
            @foreach($weekDays as $day)
              @php $isTodayCol = $day->isToday(); @endphp
              <th class="th-day {{ $isTodayCol ? 'today-col' : '' }}">
                {{ $diasEs[$day->dayOfWeek] }}
                <div style="font-size: 11px; font-weight: 400; opacity: .75; margin-top: 2px;">
                  {{ $day->format('d/m') }}
                  @if($isTodayCol)<span style="font-size:10px; background:rgba(255,255,255,.2); border-radius:4px; padding:1px 5px;">Hoy</span>@endif
                </div>
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @forelse($slots as $slot)
            <tr>
              <td class="td-hour">{{ $slot }}</td>
              @foreach($weekDays as $day)
                @php
                  $dateKey     = $day->format('Y-m-d');
                  $mapKey      = $dateKey . '|' . $slot;
                  $reservation = $reservationMap[$mapKey] ?? null;
                  $isActive    = in_array($slot, $activeSlotsPerDay[$dateKey] ?? []);
                @endphp
                <td class="week-col">
                  @if($reservation)
                    <div class="wres-cell status-{{ $reservation->status }}">
                      <div class="wres-name" title="{{ $reservation->user->name ?? 'Usuario eliminado' }}">
                        {{ $reservation->user->name ?? 'Usuario eliminado' }}
                      </div>
                      @if($reservation->total_amount)
                        <div class="wres-price">${{ number_format($reservation->total_amount, 0, ',', '.') }}</div>
                      @endif
                      <div class="wres-acts">
                        <a class="wres-btn"
                           href="{{ route('reservations.show', $reservation) }}"
                           target="_blank">Ver</a>
                        @if(in_array($reservation->status, ['PENDING_PAYMENT', 'PAID']))
                          <form method="POST"
                                action="{{ route('va.reservations.cancel', $reservation) }}"
                                style="display:inline;"
                                onsubmit="return confirm('¿Cancelar la reserva de {{ addslashes($reservation->user->name ?? 'este usuario') }}?')">
                            @csrf
                            <button type="submit" class="wres-btn">Cancelar</button>
                          </form>
                        @endif
                      </div>
                    </div>
                  @elseif($isActive)
                    <span class="week-free">Libre</span>
                  @else
                    <span class="week-closed">—</span>
                  @endif
                </td>
              @endforeach
            </tr>
          @empty
            <tr>
              <td colspan="8" style="text-align:center; padding:32px; color:#aaa;">
                No hay horarios configurados para esta cancha.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endif

@endif
{{-- fin if view --}}

<div class="agenda-footer">
  <a href="{{ route('va.reservations.index', array_filter(['date' => $date->toDateString()])) }}">Ver como tabla</a>
  &nbsp;—&nbsp;
  <a href="{{ route('va.dashboard') }}">Volver al panel</a>
</div>

@endsection
