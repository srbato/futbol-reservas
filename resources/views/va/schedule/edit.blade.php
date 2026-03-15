@extends('layouts.admin')

@section('title', 'Horarios de cancha')
@section('page_title', 'Editar horarios')
@section('page_subtitle', $field->venue->name . ' · ' . $field->name)

@push('styles')
<style>
  .schedule-wrap {
    max-width: 640px;
  }

  .schedule-info-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
    margin-bottom: 18px;
  }

  .schedule-grid {
    display: grid;
    gap: 8px;
  }

  .day-row {
    border: 1.5px solid #e8e8e8;
    border-radius: 14px;
    background: #fff;
    overflow: hidden;
    transition: border-color .2s, box-shadow .2s;
  }

  .day-row.is-open {
    border-color: #111;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
  }

  .day-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 18px;
    cursor: pointer;
    user-select: none;
  }

  .day-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #ddd;
    flex-shrink: 0;
    transition: background .2s;
  }

  .day-row.is-open .day-dot { background: #111; }

  .day-name {
    font-weight: 700;
    font-size: 15px;
    flex: 1;
  }

  .day-status-pill {
    font-size: 12px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 999px;
    background: #f3f3f3;
    color: #999;
    transition: background .2s, color .2s;
  }

  .day-row.is-open .day-status-pill {
    background: #e8f7ee;
    color: #157347;
  }

  /* Toggle switch */
  .toggle-switch {
    position: relative;
    width: 42px;
    height: 24px;
    flex-shrink: 0;
  }

  .toggle-switch input { display: none; }

  .toggle-track {
    position: absolute;
    inset: 0;
    background: #ddd;
    border-radius: 999px;
    transition: background .2s;
  }

  .toggle-track::after {
    content: '';
    position: absolute;
    width: 18px;
    height: 18px;
    background: #fff;
    border-radius: 999px;
    top: 3px;
    left: 3px;
    transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
  }

  .toggle-switch input:checked + .toggle-track { background: #111; }
  .toggle-switch input:checked + .toggle-track::after { transform: translateX(18px); }

  /* Times panel */
  .day-times {
    display: none;
    padding: 4px 18px 16px 38px;
    gap: 14px;
    align-items: flex-end;
    flex-wrap: wrap;
  }

  .day-row.is-open .day-times { display: flex; }

  .time-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .time-group label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #888;
  }

  .time-input {
    padding: 9px 12px;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    color: #111;
    background: #fafafa;
    transition: border-color .15s, background .15s;
    cursor: pointer;
  }

  .time-input:focus {
    outline: none;
    border-color: #111;
    background: #fff;
  }

  .time-sep {
    font-size: 18px;
    color: #ccc;
    padding-bottom: 10px;
  }

  @media (max-width: 480px) {
    .day-header { padding: 12px 14px; }
    .day-times   { padding: 4px 14px 14px 28px; }
  }
</style>
@endpush

@section('content')
@php
  $days = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    0 => 'Domingo',
  ];
  $byDay = $field->schedules->keyBy('day_of_week');
@endphp

<div class="schedule-wrap">
  <div class="schedule-info-bar">
    <span style="font-size:24px;">🕐</span>
    <div>
      <div style="font-weight:700; font-size:14px;">{{ $field->name }}</div>
      <div style="font-size:13px; color:#888;">{{ $field->venue->name }} · Activá los días en que la cancha abre</div>
    </div>
  </div>

  <form method="POST" action="{{ route('va.schedule.update', $field) }}">
    @csrf

    <div class="schedule-grid" style="margin-bottom:20px;">
      @foreach($days as $dayNumber => $dayLabel)
        @php
          $schedule = $byDay[$dayNumber] ?? null;
          $isClosed = !$schedule;
        @endphp

        <div class="day-row {{ !$isClosed ? 'is-open' : '' }}" id="day-row-{{ $dayNumber }}">

          <div class="day-header" onclick="toggleDay({{ $dayNumber }})">
            <span class="day-dot"></span>
            <span class="day-name">{{ $dayLabel }}</span>

            <span class="day-status-pill" id="day-status-{{ $dayNumber }}">
              {{ !$isClosed ? ($schedule->open_time . ' – ' . $schedule->close_time) : 'Cerrado' }}
            </span>

            {{-- Real is_closed checkbox — only submitted (checked) when day IS closed --}}
            <input type="checkbox"
                   id="is-closed-{{ $dayNumber }}"
                   name="days[{{ $dayNumber }}][is_closed]"
                   value="1"
                   style="display:none;"
                   {{ $isClosed ? 'checked' : '' }}>

            {{-- Visual toggle — checked = OPEN --}}
            <label class="toggle-switch" onclick="event.stopPropagation();">
              <input type="checkbox"
                     id="toggle-vis-{{ $dayNumber }}"
                     {{ !$isClosed ? 'checked' : '' }}
                     onchange="toggleDay({{ $dayNumber }})">
              <span class="toggle-track"></span>
            </label>
          </div>

          <div class="day-times">
            <div class="time-group">
              <label for="open-{{ $dayNumber }}">Abre</label>
              <input type="time"
                     id="open-{{ $dayNumber }}"
                     name="days[{{ $dayNumber }}][open_time]"
                     class="time-input"
                     value="{{ $schedule->open_time ?? '08:00' }}"
                     onchange="updateStatusPill({{ $dayNumber }})">
            </div>

            <span class="time-sep">→</span>

            <div class="time-group">
              <label for="close-{{ $dayNumber }}">Cierra</label>
              <input type="time"
                     id="close-{{ $dayNumber }}"
                     name="days[{{ $dayNumber }}][close_time]"
                     class="time-input"
                     value="{{ $schedule->close_time ?? '22:00' }}"
                     onchange="updateStatusPill({{ $dayNumber }})">
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
      <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-size:14px;">
        Guardar horarios
      </button>
      <a href="{{ route('va.fields.edit', $field) }}" class="btn btn-ghost">
        Volver a la cancha
      </a>
    </div>
  </form>
</div>

<script>
  function toggleDay(dayNumber) {
    const row      = document.getElementById('day-row-' + dayNumber);
    const visCb    = document.getElementById('toggle-vis-' + dayNumber);
    const closedCb = document.getElementById('is-closed-' + dayNumber);

    // Sync: visual toggle checked = open; is_closed checkbox checked = closed
    const isNowOpen = visCb.checked;

    row.classList.toggle('is-open', isNowOpen);
    closedCb.checked = !isNowOpen;

    updateStatusPill(dayNumber);
  }

  function updateStatusPill(dayNumber) {
    const row    = document.getElementById('day-row-' + dayNumber);
    const pill   = document.getElementById('day-status-' + dayNumber);
    const isOpen = row.classList.contains('is-open');

    if (!isOpen) {
      pill.textContent = 'Cerrado';
      return;
    }

    const openVal  = document.getElementById('open-' + dayNumber)?.value  || '';
    const closeVal = document.getElementById('close-' + dayNumber)?.value || '';
    pill.textContent = (openVal && closeVal) ? (openVal + ' – ' + closeVal) : 'Abierto';
  }
</script>
@endsection
