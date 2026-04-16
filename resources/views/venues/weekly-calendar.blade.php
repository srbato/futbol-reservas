@extends('layouts.app')

@section('title', 'Disponibilidad semanal - ' . $venue->name . ' | TuCancha')

@section('content')
@php
  $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
  $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');
  $weekEndDate = $weekStart->copy()->addDays(6);
  $dayNames = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom'];
  $dayNamesFull = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo'];
@endphp

<div style="max-width:1200px; margin:0 auto; padding:20px 16px;">

  {{-- Header --}}
  <div style="margin-bottom:24px;">
    <a href="{{ route('venues.show', $venue) }}"
       style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#a0a0a0; text-decoration:none; margin-bottom:12px;">
      <svg style="width:16px; height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
      Volver a {{ $venue->name }}
    </a>
    <h1 style="font-size:28px; font-weight:900; color:#e8e8e8; margin:0 0 4px 0; letter-spacing:-0.02em;">
      Disponibilidad semanal
    </h1>
    <p style="font-size:14px; color:#a0a0a0; margin:0;">
      {{ $venue->name }} — {{ $fields->count() }} {{ $fields->count() === 1 ? 'cancha' : 'canchas' }}
    </p>
  </div>

  {{-- Week navigation --}}
  <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
    <a href="{{ route('venues.weekly-calendar', ['venue' => $venue, 'week' => $prevWeek]) }}"
       style="display:inline-flex; align-items:center; gap:4px; padding:8px 16px; background:#111; border:1.5px solid rgba(255,255,255,.08); border-radius:10px; font-size:13px; font-weight:700; color:#a0a0a0; text-decoration:none; transition:border-color .2s;"
       onmouseover="this.style.borderColor='#22c55e'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
      <svg style="width:14px; height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
      Anterior
    </a>

    <div style="text-align:center;">
      <div style="font-size:16px; font-weight:800; color:#e8e8e8;">
        Semana del {{ $weekStart->format('d/m') }} al {{ $weekEndDate->format('d/m') }}
      </div>
      <div style="font-size:12px; color:#666;">{{ $weekStart->format('Y') }}</div>
    </div>

    <a href="{{ route('venues.weekly-calendar', ['venue' => $venue, 'week' => $nextWeek]) }}"
       style="display:inline-flex; align-items:center; gap:4px; padding:8px 16px; background:#111; border:1.5px solid rgba(255,255,255,.08); border-radius:10px; font-size:13px; font-weight:700; color:#a0a0a0; text-decoration:none; transition:border-color .2s;"
       onmouseover="this.style.borderColor='#22c55e'" onmouseout="this.style.borderColor='rgba(255,255,255,.08)'">
      Siguiente
      <svg style="width:14px; height:14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </a>
  </div>

  {{-- Today button --}}
  @if(!$weekStart->isCurrentWeek())
    <div style="text-align:center; margin-bottom:16px;">
      <a href="{{ route('venues.weekly-calendar', $venue) }}"
         style="display:inline-flex; align-items:center; gap:5px; padding:6px 14px; background:rgba(34,197,94,.1); border:1.5px solid rgba(34,197,94,.3); border-radius:999px; font-size:12px; font-weight:700; color:#6ee7a0; text-decoration:none;">
        Ir a esta semana
      </a>
    </div>
  @endif

  @if($fields->isEmpty())
    <div style="text-align:center; padding:60px 20px; background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.08); border-radius:20px;">
      <div style="font-weight:700; font-size:16px; color:#a0a0a0; margin-bottom:8px;">Este complejo no tiene canchas activas</div>
      <a href="{{ route('venues.show', $venue) }}" style="color:#22c55e; font-weight:700; text-decoration:none;">Volver al complejo</a>
    </div>
  @else

    {{-- Legend --}}
    <div style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:20px; padding:14px 18px; background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.08); border-radius:14px;">
      <div style="font-size:12px; font-weight:700; color:#a0a0a0; text-transform:uppercase; letter-spacing:.06em; display:flex; align-items:center; margin-right:8px;">Canchas:</div>
      @foreach($fields as $field)
        <div style="display:flex; align-items:center; gap:6px;">
          <div style="width:14px; height:14px; border-radius:4px; background:{{ $fieldColors[$field->id] }};"></div>
          <span style="font-size:13px; font-weight:600; color:#a0a0a0;">{{ $field->name }}</span>
        </div>
      @endforeach
      <div style="display:flex; align-items:center; gap:12px; margin-left:auto;">
        <div style="display:flex; align-items:center; gap:4px;">
          <div style="width:14px; height:14px; border-radius:4px; background:rgba(255,255,255,.08);"></div>
          <span style="font-size:11px; color:#666;">Ocupado</span>
        </div>
        <div style="display:flex; align-items:center; gap:4px;">
          <div style="width:14px; height:14px; border-radius:4px; background:#444; opacity:.5;"></div>
          <span style="font-size:11px; color:#666;">Pasado</span>
        </div>
      </div>
    </div>

    {{-- Desktop: weekly grid --}}
    <div id="wc-desktop" style="overflow-x:auto; border:1px solid rgba(255,255,255,.08); border-radius:16px; background:#111;">
      <table style="width:100%; border-collapse:collapse; min-width:700px;">
        <thead>
          <tr>
            <th style="padding:14px 12px; font-size:12px; font-weight:700; color:#666; text-transform:uppercase; letter-spacing:.06em; text-align:left; border-bottom:2px solid rgba(255,255,255,.08); background:rgba(255,255,255,.02); position:sticky; left:0; z-index:2; min-width:70px;">
              Hora
            </th>
            @foreach($weekDays as $i => $day)
              @php
                $isToday = $day->isToday();
              @endphp
              <th style="padding:14px 8px; font-size:12px; font-weight:700; text-align:center; border-bottom:2px solid rgba(255,255,255,.08); {{ $isToday ? 'background:rgba(34,197,94,.1); color:#6ee7a0;' : 'background:rgba(255,255,255,.02); color:#a0a0a0;' }} text-transform:uppercase; letter-spacing:.04em;">
                <div style="font-size:11px; color:{{ $isToday ? '#6ee7a0' : '#666' }};">{{ $dayNames[$i] }}</div>
                <div style="font-size:18px; font-weight:900; margin-top:2px; color:{{ $isToday ? '#6ee7a0' : '#a0a0a0' }};">{{ $day->format('d') }}</div>
                <div style="font-size:10px; color:{{ $isToday ? '#6ee7a0' : '#444' }};">{{ $day->format('M') }}</div>
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @forelse($slots as $slotIndex => $slotTime)
            <tr style="{{ $slotIndex % 2 === 0 ? 'background:#111;' : 'background:rgba(255,255,255,.02);' }}">
              <td style="padding:8px 12px; font-size:13px; font-weight:700; color:#a0a0a0; border-right:1px solid rgba(255,255,255,.04); position:sticky; left:0; z-index:1; {{ $slotIndex % 2 === 0 ? 'background:#111;' : 'background:rgba(255,255,255,.02);' }}">
                {{ $slotTime }}
              </td>
              @foreach($weekDays as $day)
                @php
                  $dateKey = $day->format('Y-m-d');
                  $cellFields = $calendarData[$slotTime][$dateKey] ?? [];
                @endphp
                <td style="padding:4px 4px; vertical-align:middle; border-right:1px solid rgba(255,255,255,.04);">
                  @if(!empty($cellFields))
                    <div style="display:flex; flex-wrap:wrap; gap:3px; justify-content:center;">
                      @foreach($cellFields as $fieldId => $status)
                        @php
                          $field = $fields->firstWhere('id', $fieldId);
                          $color = $fieldColors[$fieldId] ?? '#999';
                        @endphp
                        @if($status === 'available')
                          <a href="{{ route('fields.show', ['field' => $fieldId, 'date' => $dateKey]) }}"
                             title="{{ $field->name }} - Disponible - {{ $slotTime }}"
                             style="display:block; width:28px; height:28px; border-radius:6px; background:{{ $color }}; opacity:.85; transition:opacity .15s, transform .15s; cursor:pointer;"
                             onmouseover="this.style.opacity='1'; this.style.transform='scale(1.15)';"
                             onmouseout="this.style.opacity='.85'; this.style.transform='scale(1)';">
                          </a>
                        @elseif($status === 'occupied' || $status === 'blocked')
                          <div title="{{ $field->name }} - Ocupado - {{ $slotTime }}"
                               style="width:28px; height:28px; border-radius:6px; background:rgba(255,255,255,.08); position:relative; cursor:default;">
                            <div style="position:absolute; inset:3px; border-radius:4px; border:2px solid {{ $color }}; opacity:.4;"></div>
                          </div>
                        @elseif($status === 'past')
                          <div title="{{ $field->name }} - Pasado - {{ $slotTime }}"
                               style="width:28px; height:28px; border-radius:6px; background:rgba(255,255,255,.04); opacity:.4; cursor:default;">
                          </div>
                        @endif
                      @endforeach
                    </div>
                  @endif
                </td>
              @endforeach
            </tr>
          @empty
            <tr>
              <td colspan="{{ count($weekDays) + 1 }}" style="padding:40px; text-align:center; color:#666; font-size:14px;">
                No hay horarios disponibles esta semana
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Mobile: day selector + single day view --}}
    <div id="wc-mobile" style="display:none;">
      {{-- Day selector --}}
      <div style="display:flex; gap:6px; overflow-x:auto; margin-bottom:16px; padding-bottom:4px; -webkit-overflow-scrolling:touch;">
        @foreach($weekDays as $i => $day)
          @php $isToday = $day->isToday(); @endphp
          <button onclick="wcSelectDay({{ $i }})"
                  data-day-btn="{{ $i }}"
                  style="flex-shrink:0; padding:10px 16px; border:1.5px solid {{ $isToday ? '#22c55e' : 'rgba(255,255,255,.08)' }}; border-radius:12px; background:{{ $isToday ? 'rgba(34,197,94,.1)' : '#111' }}; cursor:pointer; text-align:center; font-family:inherit; transition:all .2s; {{ $i === 0 && !$isToday ? 'border-color:#22c55e; background:rgba(34,197,94,.1);' : '' }}">
            <div style="font-size:11px; font-weight:700; color:#666; text-transform:uppercase;">{{ $dayNames[$i] }}</div>
            <div style="font-size:20px; font-weight:900; color:#a0a0a0; margin-top:2px;">{{ $day->format('d') }}</div>
          </button>
        @endforeach
      </div>

      {{-- Day content panels --}}
      @foreach($weekDays as $dayIndex => $day)
        @php $dateKey = $day->format('Y-m-d'); @endphp
        <div data-day-panel="{{ $dayIndex }}" style="{{ $dayIndex !== 0 ? 'display:none;' : '' }}">
          <div style="font-size:16px; font-weight:800; color:#e8e8e8; margin-bottom:14px;">
            {{ $dayNamesFull[$dayIndex] }} {{ $day->format('d/m') }}
          </div>

          @php
            $dayHasSlots = false;
            foreach ($slots as $st) {
              if (!empty($calendarData[$st][$dateKey] ?? [])) { $dayHasSlots = true; break; }
            }
          @endphp

          @if(!$dayHasSlots)
            <div style="padding:30px; text-align:center; background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.08); border-radius:14px; color:#666; font-size:14px;">
              Sin horarios este dia
            </div>
          @else
            <div style="display:flex; flex-direction:column; gap:6px;">
              @foreach($slots as $slotTime)
                @php $cellFields = $calendarData[$slotTime][$dateKey] ?? []; @endphp
                @if(!empty($cellFields))
                  <div style="display:flex; align-items:center; gap:10px; padding:10px 14px; background:#111; border:1px solid rgba(255,255,255,.04); border-radius:12px;">
                    <div style="font-size:14px; font-weight:700; color:#a0a0a0; min-width:48px;">{{ $slotTime }}</div>
                    <div style="display:flex; flex-wrap:wrap; gap:6px; flex:1;">
                      @foreach($cellFields as $fieldId => $status)
                        @php
                          $field = $fields->firstWhere('id', $fieldId);
                          $color = $fieldColors[$fieldId] ?? '#999';
                        @endphp
                        @if($status === 'available')
                          <a href="{{ route('fields.show', ['field' => $fieldId, 'date' => $dateKey]) }}"
                             style="display:inline-flex; align-items:center; gap:5px; padding:5px 10px; border-radius:8px; background:{{ $color }}; color:#fff; font-size:12px; font-weight:700; text-decoration:none; opacity:.9; transition:opacity .15s;"
                             onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='.9'">
                            {{ $field->name }}
                          </a>
                        @elseif($status === 'occupied' || $status === 'blocked')
                          <div style="display:inline-flex; align-items:center; gap:5px; padding:5px 10px; border-radius:8px; background:rgba(255,255,255,.04); color:#666; font-size:12px; font-weight:600; border:1.5px solid rgba(255,255,255,.08);">
                            <div style="width:8px; height:8px; border-radius:3px; background:{{ $color }}; opacity:.4;"></div>
                            {{ $field->name }}
                          </div>
                        @elseif($status === 'past')
                          <div style="display:inline-flex; align-items:center; gap:5px; padding:5px 10px; border-radius:8px; background:rgba(255,255,255,.02); color:#444; font-size:12px; font-weight:600;">
                            <div style="width:8px; height:8px; border-radius:3px; background:{{ $color }}; opacity:.25;"></div>
                            {{ $field->name }}
                          </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                @endif
              @endforeach
            </div>
          @endif
        </div>
      @endforeach
    </div>

  @endif
</div>

@push('scripts')
<script>
  // Responsive: show desktop or mobile view
  function wcLayout() {
    var w = window.innerWidth;
    var desktop = document.getElementById('wc-desktop');
    var mobile = document.getElementById('wc-mobile');
    if (!desktop || !mobile) return;
    if (w < 768) {
      desktop.style.display = 'none';
      mobile.style.display = 'block';
    } else {
      desktop.style.display = 'block';
      mobile.style.display = 'none';
    }
  }
  wcLayout();
  window.addEventListener('resize', wcLayout);

  // Mobile: select day
  var wcCurrentDay = 0;
  // Auto-select today if it's in this week
  (function() {
    var btns = document.querySelectorAll('[data-day-btn]');
    @foreach($weekDays as $i => $day)
      @if($day->isToday())
        wcCurrentDay = {{ $i }};
      @endif
    @endforeach
    wcSelectDay(wcCurrentDay);
  })();

  function wcSelectDay(index) {
    wcCurrentDay = index;
    // Toggle buttons
    document.querySelectorAll('[data-day-btn]').forEach(function(btn) {
      var isActive = parseInt(btn.dataset.dayBtn) === index;
      btn.style.borderColor = isActive ? '#22c55e' : 'rgba(255,255,255,.08)';
      btn.style.background = isActive ? 'rgba(34,197,94,.1)' : '#111';
    });
    // Toggle panels
    document.querySelectorAll('[data-day-panel]').forEach(function(panel) {
      panel.style.display = parseInt(panel.dataset.dayPanel) === index ? '' : 'none';
    });
  }
</script>
@endpush

@endsection
