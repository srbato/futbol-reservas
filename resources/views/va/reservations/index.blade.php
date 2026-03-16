@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.admin')

@section('title', 'Reservas')
@section('page_title', 'Reservas')
@section('page_subtitle', 'Filtrá y gestioná las reservas de tus canchas')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_reservations',
  'helpTitle' => 'Lista de reservas',
  'helpText'  => 'Acá podés ver todas las reservas de tus canchas. Usá los filtros para buscar por fecha, estado o cancha. También podés cancelar reservas individuales y crear reservas manuales para clientes que pagan en el momento.',
])

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>
  @endif

  {{-- Header con botón --}}
  <div style="display:flex; justify-content:flex-end; margin-bottom:16px;">
    <button class="btn btn-primary" onclick="document.getElementById('modal-manual').style.display='flex'">
      + Nueva reserva manual
    </button>
  </div>

  {{-- Filtros --}}
  <form method="GET" action="{{ route('va.reservations.index') }}" class="filter-bar">
    <div class="form-group">
      <label class="form-label">Fecha</label>
      <input type="date" name="date" value="{{ $date->toDateString() }}"
             class="form-control" style="width:auto;">
    </div>

    <div class="form-group">
      <label class="form-label">Estado</label>
      <select name="status" class="form-control" style="width:auto;">
        <option value="">Todos</option>
        <option value="PENDING_PAYMENT" {{ ($status ?? '') === 'PENDING_PAYMENT' ? 'selected' : '' }}>Pendiente de pago</option>
        <option value="PAID"            {{ ($status ?? '') === 'PAID'            ? 'selected' : '' }}>Pagada</option>
        <option value="CANCELLED"       {{ ($status ?? '') === 'CANCELLED'       ? 'selected' : '' }}>Cancelada</option>
        <option value="EXPIRED"         {{ ($status ?? '') === 'EXPIRED'         ? 'selected' : '' }}>Expirada</option>
      </select>
    </div>

    <div class="form-group">
      <label class="form-label">Cancha</label>
      <select name="field_id" class="form-control" style="width:auto;">
        <option value="">Todas</option>
        @foreach($fields as $field)
          <option value="{{ $field->id }}" {{ (string)($fieldId ?? '') === (string)$field->id ? 'selected' : '' }}>
            {{ $field->name }}
          </option>
        @endforeach
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Filtrar</button>
    <a href="{{ route('va.reservations.index') }}" class="btn btn-ghost">Limpiar</a>
  </form>

  {{-- Tabla --}}
  <div class="admin-card" style="padding:0; overflow:hidden;">
    <div style="overflow-x:auto;">
      <table class="data-table">
        <thead>
          <tr>
            <th>Hora</th>
            <th>Cancha</th>
            <th>Usuario / Cliente</th>
            <th>Estado</th>
            <th>Código</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($reservations as $r)
            <tr>
              <td style="font-weight:600; white-space:nowrap;">
                {{ $r->start_at->format('H:i') }} – {{ $r->end_at->format('H:i') }}
              </td>
              <td>{{ $r->field->name }}</td>
              <td>
                @if($r->payment_provider === 'manual')
                  <span style="font-size:11px; background:#f0f0f0; border-radius:999px; padding:2px 8px; font-weight:700; color:#555; margin-right:4px;">Manual</span>
                  {{ $r->notes ?? '—' }}
                @else
                  {{ $r->user->name ?? '—' }}
                @endif
              </td>
              <td>
                <span class="badge" style="{{ ReservationStatus::color($r->status) }}">
                  {{ ReservationStatus::label($r->status) }}
                </span>
              </td>
              <td>
                <code style="font-size:13px; color:#777; background:#f5f5f5; padding:2px 7px; border-radius:6px;">
                  {{ $r->verification_code ?? '—' }}
                </code>
              </td>
              <td>
                @if(in_array($r->status, ['PENDING_PAYMENT', 'PAID']))
                  <form method="POST" action="{{ route('va.reservations.cancel', $r) }}"
                        style="display:inline;"
                        onsubmit="return confirm('¿Cancelar esta reserva?')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="empty-state">No hay reservas para esos filtros.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Modal: nueva reserva manual --}}
  <div id="modal-manual" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; padding:16px;">
    <div style="background:#fff; border-radius:20px; padding:32px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.2);">
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h2 style="margin:0; font-size:20px; font-weight:800;">Nueva reserva manual</h2>
        <button onclick="document.getElementById('modal-manual').style.display='none'"
                style="background:none; border:none; font-size:22px; cursor:pointer; color:#888; line-height:1;">×</button>
      </div>

      <form method="POST" action="{{ route('va.reservations.manual_store') }}">
        @csrf

        <div class="form-group" style="margin-bottom:16px;">
          <label class="form-label">Cancha</label>
          <select name="field_id" class="form-control" required>
            <option value="">Seleccioná una cancha</option>
            @foreach($fields as $field)
              <option value="{{ $field->id }}">{{ $field->name }}</option>
            @endforeach
          </select>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
          <div class="form-group">
            <label class="form-label">Fecha</label>
            <input type="date" name="date" class="form-control"
                   value="{{ now()->toDateString() }}"
                   min="{{ now()->toDateString() }}" required>
          </div>
          <div class="form-group">
            <label class="form-label">Hora de inicio</label>
            <input type="time" name="time" class="form-control" required>
          </div>
        </div>

        <div class="form-group" style="margin-bottom:24px;">
          <label class="form-label">Cliente / Nota <span style="color:#aaa; font-weight:400;">(opcional)</span></label>
          <input type="text" name="notes" class="form-control"
                 placeholder="Ej: Juan García — llamó por teléfono" maxlength="255">
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end;">
          <button type="button" class="btn btn-ghost"
                  onclick="document.getElementById('modal-manual').style.display='none'">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary">Crear reserva</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Close modal clicking outside
    document.getElementById('modal-manual').addEventListener('click', function(e) {
      if (e.target === this) this.style.display = 'none';
    });
  </script>

@endsection
