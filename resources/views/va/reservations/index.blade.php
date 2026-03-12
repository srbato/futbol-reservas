@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.admin')

@section('title', 'Reservas')

@section('page_title', 'Reservas')
@section('page_subtitle', 'Filtrá y gestioná las reservas de tus canchas')

@section('content')
  <h1>Reservas del día</h1>

  <form method="GET" action="{{ route('va.reservations.index') }}"
        style="margin: 12px 0 18px 0; padding:12px; border:1px solid #eee; border-radius:12px; display:flex; gap:10px; flex-wrap:wrap; align-items:end;">

    <div>
      <label style="display:block; font-size:12px; color:#666;">Fecha</label>
      <input type="date" name="date" value="{{ $date->toDateString() }}"
             style="padding:8px; border:1px solid #ddd; border-radius:10px;">
    </div>

    <div>
      <label style="display:block; font-size:12px; color:#666;">Estado</label>
      <select name="status" style="padding:8px; border:1px solid #ddd; border-radius:10px;">
        <option value="">Todos</option>
        <option value="PENDING_PAYMENT" {{ ($status ?? '') === 'PENDING_PAYMENT' ? 'selected' : '' }}>Pendiente de pago</option>
        <option value="PAID" {{ ($status ?? '') === 'PAID' ? 'selected' : '' }}>Pagada</option>
        <option value="CHECKED_IN" {{ ($status ?? '') === 'CHECKED_IN' ? 'selected' : '' }}>Validada</option>
        <option value="CANCELLED" {{ ($status ?? '') === 'CANCELLED' ? 'selected' : '' }}>Cancelada</option>
        <option value="EXPIRED" {{ ($status ?? '') === 'EXPIRED' ? 'selected' : '' }}>Expirada</option>
      </select>
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
      Filtrar
    </button>

    <a href="{{ route('va.reservations.index') }}"
       style="padding:9px 14px; text-decoration:none; border:1px solid #ddd; border-radius:10px; color:#111;">
      Limpiar
    </a>
  </form>

  <table border="1" cellpadding="6" style="margin-top:12px; width:100%; border-collapse:collapse;">
    <tr>
      <th>Hora</th>
      <th>Complejo</th>
      <th>Cancha</th>
      <th>Usuario</th>
      <th>Estado</th>
      <th>Código</th>
      <th>Acciones</th>
    </tr>

    @forelse($reservations as $r)
      <tr>
        <td>{{ $r->start_at->format('H:i') }} - {{ $r->end_at->format('H:i') }}</td>
        <td>{{ $r->field->venue->name }}</td>
        <td>{{ $r->field->name }}</td>
        <td>{{ $r->user->name ?? '—' }}</td>

        <td>
          <span style="padding:4px 10px; border-radius:999px; font-weight:600; {{ ReservationStatus::color($r->status) }}">
            {{ ReservationStatus::label($r->status) }}
          </span>
        </td>

        <td>{{ $r->verification_code ?? '—' }}</td>

        <td>
          @if(in_array($r->status, ['PENDING_PAYMENT', 'PAID']))
            <form method="POST" action="{{ route('va.reservations.cancel', $r) }}" style="display:inline;">
              @csrf
              <button type="submit">Cancelar</button>
            </form>
          @endif
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="7">No hay reservas para esos filtros.</td>
      </tr>
    @endforelse
  </table>

  <p style="margin-top:12px;">
    <a href="{{ route('va.dashboard') }}">Volver al panel</a>
  </p>
@endsection