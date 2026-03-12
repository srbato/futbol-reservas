@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.admin')

@section('title', 'Agenda del día')

@section('page_title', 'Agenda del día')
@section('page_subtitle', 'Visualizá las reservas por horario y cancha')

@section('content')
  <h1>Agenda del día</h1>

  <form method="GET" action="{{ route('va.reservations.agenda') }}"
        style="margin: 12px 0 18px 0; padding:12px; border:1px solid #eee; border-radius:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:end;">

    <div>
      <label style="display:block; font-size:12px; color:#666;">Fecha</label>
      <input type="date" name="date" value="{{ $date->toDateString() }}"
             style="padding:8px; border:1px solid #ddd; border-radius:10px;">
    </div>

    <div>
      <label style="display:block; font-size:12px; color:#666;">Cancha</label>
      <select name="field_id" style="padding:8px; border:1px solid #ddd; border-radius:10px;">
        <option value="">Todas</option>
        @foreach($fields as $field)
          <option value="{{ $field->id }}" {{ (string)($fieldId ?? '') === (string)$field->id ? 'selected' : '' }}>
            {{ $field->name }}
          </option>
        @endforeach
      </select>
    </div>

    <button type="submit"
            style="padding:9px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
      Ver agenda
    </button>

    <a href="{{ route('va.reservations.agenda') }}"
       style="padding:9px 14px; text-decoration:none; border:1px solid #ddd; border-radius:10px; color:#111;">
      Limpiar
    </a>
  </form>

  @php
    $hours = [];
    for ($h = 8; $h <= 23; $h++) {
        $hours[] = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
    }
  @endphp

  <div style="overflow:auto;">
    <table border="1" cellpadding="8" style="border-collapse:collapse; min-width:900px; width:100%;">
      <tr>
        <th style="background:#f8f8f8;">Hora</th>
        @foreach($fields as $field)
          <th style="background:#f8f8f8;">
            {{ $field->name }}
            <div style="font-size:12px; font-weight:normal; color:#666;">
              {{ $field->venue->name }}
            </div>
          </th>
        @endforeach
      </tr>

      @foreach($hours as $hour)
        <tr>
          <td style="font-weight:600; white-space:nowrap;">{{ $hour }}</td>

          @foreach($fields as $field)
            @php
              $key = $field->id . '|' . $hour;
              $reservation = $reservationMap[$key] ?? null;
            @endphp

            <td style="vertical-align:top; min-width:220px;">
              @if($reservation)
                <div style="padding:8px; border-radius:10px; {{ ReservationStatus::color($reservation->status) }}">
                  <div style="font-weight:700;">
                    {{ $reservation->user->name ?? 'Usuario' }}
                  </div>

                  <div style="font-size:13px; margin-top:4px;">
                    {{ ReservationStatus::label($reservation->status) }}
                  </div>

                  <div style="font-size:12px; margin-top:4px;">
                    Código: {{ $reservation->verification_code ?? '—' }}
                  </div>

                  <div style="margin-top:8px;">
                    @if(in_array($reservation->status, ['PENDING_PAYMENT', 'PAID']))
                      <form method="POST" action="{{ route('va.reservations.cancel', $reservation) }}" style="display:inline;">
                        @csrf
                        <button type="submit">Cancelar</button>
                      </form>
                    @endif
                  </div>
                </div>
              @else
                <div style="padding:8px; border-radius:10px; background:#f8f9fa; color:#666;">
                  Libre
                </div>
              @endif
            </td>
          @endforeach
        </tr>
      @endforeach
    </table>
  </div>

  <p style="margin-top:12px;">
    <a href="{{ route('va.reservations.index') }}">Ver tabla</a>
    — <a href="{{ route('va.dashboard') }}">Volver al panel</a>
  </p>
@endsection