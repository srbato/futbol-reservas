@extends('layouts.app')

@section('title', 'Cambiar horario — Reserva #' . $reservation->id)

@section('content')
<div style="max-width: 780px; margin: auto; display: grid; gap: 18px;">

  {{-- Encabezado --}}
  <div class="page-card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 8px; font-size:30px; letter-spacing:-0.02em;">Cambiar horario</h1>
        <p class="muted" style="margin:0;">
          Reserva #{{ $reservation->id }} &mdash; {{ $reservation->field->name }}, {{ $reservation->field->venue->name }}
        </p>
      </div>
      <a href="{{ route('reservations.show', $reservation) }}" class="btn">Cancelar</a>
    </div>
  </div>

  {{-- Info reserva actual --}}
  <div class="page-card" style="background:rgba(255,255,255,.04); border-color:rgba(255,255,255,.08);">
    <div style="font-size:12px; color:#a0a0a0; font-weight:700; text-transform:uppercase; letter-spacing:.05em; margin-bottom:10px;">
      Horario actual
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <span class="badge">{{ $reservation->start_at->format('d/m/Y') }}</span>
      <span class="badge">{{ $reservation->start_at->format('H:i') }} – {{ $reservation->end_at->format('H:i') }}</span>
      <span class="badge">{{ $reservation->field->name }}</span>
      <span class="badge">{{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</span>
    </div>
  </div>

  @if(session('error'))
    <div class="page-card" style="background:rgba(229,57,53,.1); border-color:rgba(229,57,53,.2); color:#f87171;">
      {{ session('error') }}
    </div>
  @endif

  {{-- Filtro: cancha y fecha --}}
  <div class="page-card">
    <form method="GET" action="{{ route('reservations.modify.show', $reservation) }}"
          style="display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end;">
      <div>
        <label style="display:block; font-size:12px; color:#a0a0a0; margin-bottom:5px;">Cancha</label>
        <select name="field_id"
                style="padding:9px 12px; border:1px solid rgba(255,255,255,.1); border-radius:10px; font-size:14px; background:#0a0a0a; color:#e8e8e8;">
          @foreach($fields as $f)
            <option value="{{ $f->id }}" {{ $f->id === $selectedField->id ? 'selected' : '' }}>
              {{ $f->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label style="display:block; font-size:12px; color:#a0a0a0; margin-bottom:5px;">Fecha</label>
        <input type="date"
               name="date"
               value="{{ $selectedDate->toDateString() }}"
               min="{{ now()->toDateString() }}"
               style="padding:9px 12px; border:1px solid rgba(255,255,255,.1); border-radius:10px; font-size:14px; background:#0a0a0a; color:#e8e8e8;">
      </div>
      <button type="submit" class="btn btn-primary">Ver disponibilidad</button>
    </form>
  </div>

  {{-- Slots disponibles --}}
  <div class="page-card">
    <h2 style="margin:0 0 16px; font-size:18px; font-weight:800;">
      {{ ucfirst($selectedDate->locale('es')->isoFormat('dddd D [de] MMMM')) }}
      — {{ $selectedField->name }}
    </h2>

    @if(empty($slots))
      <p class="muted">No hay horarios disponibles para este día.</p>
    @else
      <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(130px, 1fr)); gap:10px;">
        @foreach($slots as $slot)
          @if($slot['is_current'])
            <div style="padding:12px; border-radius:12px; background:rgba(255,255,255,.04); border:2px dashed rgba(255,255,255,.15); text-align:center; opacity:.6;">
              <div style="font-size:18px; font-weight:800; color:#666;">{{ $slot['time'] }}</div>
              <div style="font-size:11px; color:#666; margin-top:3px;">Actual</div>
            </div>
          @elseif($slot['available'])
            <form method="POST" action="{{ route('reservations.modify.preview', $reservation) }}" style="margin:0;">
              @csrf
              <input type="hidden" name="field_id" value="{{ $selectedField->id }}">
              <input type="hidden" name="start_at" value="{{ $slot['start_at'] }}">
              <button type="submit"
                      style="width:100%; padding:12px; border-radius:12px; background:#0a0a0a; border:2px solid #22c55e; cursor:pointer; text-align:center; font-family:inherit; transition:background .15s; color:#e8e8e8;">
                <div style="font-size:18px; font-weight:800; color:#e8e8e8;">{{ $slot['time'] }}</div>
                <div style="font-size:11px; color:#22c55e; font-weight:700; margin-top:3px;">Disponible</div>
                @if($slot['price'] !== null)
                  <div style="font-size:11px; color:#a0a0a0; margin-top:2px;">${{ number_format($slot['price'], 0, ',', '.') }}</div>
                @endif
              </button>
            </form>
          @else
            <div style="padding:12px; border-radius:12px; background:rgba(255,255,255,.02); border:2px solid rgba(255,255,255,.04); text-align:center; opacity:.5; cursor:not-allowed;">
              <div style="font-size:18px; font-weight:800; color:#666;">{{ $slot['time'] }}</div>
              <div style="font-size:11px; color:#555; margin-top:3px;">Ocupado</div>
            </div>
          @endif
        @endforeach
      </div>
    @endif
  </div>

</div>
@endsection
