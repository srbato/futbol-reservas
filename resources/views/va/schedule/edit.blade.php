@extends('layouts.admin')

@section('title', 'Horarios de cancha')
@section('page_title', 'Editar horarios')
@section('page_subtitle', $field->venue->name . ' · ' . $field->name)

@section('content')
  <div class="admin-card" style="margin-bottom:16px;">
    <p style="margin-top:0;">
      <strong>Cancha:</strong> {{ $field->name }}<br>
      <strong>Complejo:</strong> {{ $field->venue->name }}
    </p>

    <form method="POST" action="{{ route('va.schedule.update', $field) }}">
      @csrf

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

      <div style="display:grid; gap:14px;">
        @foreach($days as $dayNumber => $dayLabel)
          @php
            $schedule = $byDay[$dayNumber] ?? null;
            $isClosed = !$schedule;
          @endphp

          <div style="padding:14px; border:1px solid #eee; border-radius:12px; background:#fafafa;">
            <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:10px;">
              <div style="font-weight:700;">{{ $dayLabel }}</div>

              <label style="display:flex; align-items:center; gap:8px; font-size:14px; color:#444;">
                <input
                  type="checkbox"
                  name="days[{{ $dayNumber }}][is_closed]"
                  value="1"
                  {{ $isClosed ? 'checked' : '' }}
                  onchange="toggleDayInputs({{ $dayNumber }}, this.checked)"
                >
                Cerrado
              </label>
            </div>

            <div id="day-inputs-{{ $dayNumber }}" style="display:flex; gap:14px; flex-wrap:wrap; align-items:end; {{ $isClosed ? 'opacity:.5;' : '' }}">
              <div>
                <label style="display:block; font-size:12px; color:#666;">Abre</label>
                <input type="time"
                       name="days[{{ $dayNumber }}][open_time]"
                       value="{{ $schedule->open_time ?? '' }}"
                       {{ $isClosed ? 'disabled' : '' }}
                       style="padding:10px; border:1px solid #ddd; border-radius:10px;">
              </div>

              <div>
                <label style="display:block; font-size:12px; color:#666;">Cierra</label>
                <input type="time"
                       name="days[{{ $dayNumber }}][close_time]"
                       value="{{ $schedule->close_time ?? '' }}"
                       {{ $isClosed ? 'disabled' : '' }}
                       style="padding:10px; border:1px solid #ddd; border-radius:10px;">
              </div>

              <div style="font-size:13px; color:#666;">
                Marcá “Cerrado” para indicar que este día no abre.
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div style="margin-top:16px; display:flex; gap:10px; align-items:center;">
        <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
          Guardar horarios
        </button>

        <a href="{{ route('va.fields.edit', $field) }}">Volver a la cancha</a>
      </div>
    </form>
  </div>

  <script>
    function toggleDayInputs(dayNumber, isClosed) {
      const wrap = document.getElementById('day-inputs-' + dayNumber);
      if (!wrap) return;

      const inputs = wrap.querySelectorAll('input[type="time"]');

      inputs.forEach(input => {
        input.disabled = isClosed;

        if (isClosed) {
          input.value = '';
        }
      });

      wrap.style.opacity = isClosed ? '.5' : '1';
    }
  </script>
@endsection