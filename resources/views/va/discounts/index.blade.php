@extends('layouts.admin')

@section('title', 'Descuentos')
@section('page_title', 'Descuentos')
@section('page_subtitle', 'Configurá precios promocionales por día o por horario')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_discounts',
  'helpTitle' => 'Descuentos',
  'helpText'  => 'Acá configurás precios especiales para tus canchas. Podés crear descuentos por día de la semana (ej. lunes más barato), por fecha puntual, por franja horaria o combinar varios criterios. Los descuentos se aplican automáticamente cuando los usuarios reservan en esos horarios.',
])

  {{-- Descuentos por reserva recurrente --}}
  <div class="admin-card" style="margin-bottom:20px;">
    <div class="section-header">
      <div>
        <div class="section-title">Descuentos por reserva recurrente</div>
        <div class="section-subtitle">
          El sistema aplica el tier más alto que corresponda según la cantidad de turnos reservados a la vez.
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('va.recurring_discounts.store') }}" class="form-row" style="margin-bottom:20px;">
      @csrf

      <div class="form-group">
        <label class="form-label">Cancha</label>
        <select name="field_id" required class="form-control" style="width:auto;">
          <option value="">Seleccionar</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}">{{ $field->venue->name }} — {{ $field->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Desde N turnos</label>
        <select name="min_occurrences" required class="form-control" style="width:auto;">
          @foreach([2,3,4,5,6,8,10,12] as $n)
            <option value="{{ $n }}">{{ $n }} turnos</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Descuento (%)</label>
        <input type="number" step="0.01" min="1" max="100" name="discount_percentage" required
               placeholder="Ej: 10" class="form-control" style="width:110px;">
      </div>

      <button type="submit" class="btn btn-primary">Guardar tier</button>
    </form>

    @if($recurringDiscounts->isNotEmpty())
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th>Cancha</th>
              <th>Desde</th>
              <th>Descuento</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($recurringDiscounts as $rd)
              <tr>
                <td>{{ $rd->field->venue->name }} / {{ $rd->field->name }}</td>
                <td>{{ $rd->min_occurrences }}+ turnos</td>
                <td><span class="badge badge-success">{{ $rd->discount_percentage }}%</span></td>
                <td>
                  <form method="POST" action="{{ route('va.recurring_discounts.destroy', $rd) }}"
                        onsubmit="return confirm('¿Eliminar este tier?')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <p class="muted" style="margin:0;">No hay tiers configurados todavía.</p>
    @endif
  </div>

  {{-- Crear descuento puntual --}}
  <div class="admin-card" style="margin-bottom:20px;">
    <div class="section-title" style="margin-bottom:16px;">Crear descuento</div>

    <form method="POST" action="{{ route('va.discounts.store') }}" class="form-row">
      @csrf

      <div class="form-group">
        <label class="form-label">Cancha</label>
        <select name="field_id" required class="form-control" style="width:auto;">
          <option value="">Seleccionar</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}">{{ $field->venue->name }} — {{ $field->name }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Día de semana</label>
        <select name="day_of_week" class="form-control" style="width:auto;">
          <option value="">Sin día fijo</option>
          <option value="1">Lunes</option>
          <option value="2">Martes</option>
          <option value="3">Miércoles</option>
          <option value="4">Jueves</option>
          <option value="5">Viernes</option>
          <option value="6">Sábado</option>
          <option value="0">Domingo</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Fecha puntual</label>
        <input type="date" name="date" class="form-control" style="width:auto;">
      </div>

      <div class="form-group">
        <label class="form-label">Hora inicio</label>
        <input type="time" name="start_time" class="form-control" style="width:auto;">
      </div>

      <div class="form-group">
        <label class="form-label">Hora fin</label>
        <input type="time" name="end_time" class="form-control" style="width:auto;">
      </div>

      <div class="form-group">
        <label class="form-label">Precio promo</label>
        <input type="number" step="0.01" name="discount_price" required
               class="form-control" style="width:110px;">
      </div>

      <div class="form-group">
        <label class="form-label">Etiqueta</label>
        <input name="label" placeholder="Ej: Promo tarde" class="form-control" style="width:160px;">
      </div>

      <button type="submit" class="btn btn-primary">Guardar descuento</button>
    </form>

    <p class="muted" style="margin:14px 0 0; font-size:13px;">
      Sin horas: aplica todo el día. Con fecha puntual: solo ese día.
    </p>
  </div>

  {{-- Lista de descuentos --}}
  <div class="admin-card" style="padding:0; overflow:hidden;">
    <div style="padding:18px 18px 14px; border-bottom:1px solid #ececec;">
      <div class="section-title">Descuentos cargados</div>
    </div>

    @if($discounts->isEmpty())
      <div class="empty-state">No hay descuentos cargados.</div>
    @else
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th>Cancha</th>
              <th>Día / Fecha</th>
              <th>Horario</th>
              <th>Precio</th>
              <th>Etiqueta</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($discounts as $discount)
              @php
                $days = [0=>'Domingo',1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado'];
              @endphp
              <tr>
                <td>{{ $discount->field->venue->name }} / {{ $discount->field->name }}</td>
                <td>
                  @if($discount->date)
                    {{ $discount->date->format('d/m/Y') }}
                  @elseif(!is_null($discount->day_of_week))
                    {{ $days[$discount->day_of_week] }}
                  @else
                    Todos los días
                  @endif
                </td>
                <td style="white-space:nowrap;">
                  {{ $discount->start_time ?? 'Todo el día' }}
                  @if($discount->end_time) – {{ $discount->end_time }} @endif
                </td>
                <td style="font-weight:700;">${{ number_format($discount->discount_price, 0, ',', '.') }}</td>
                <td>
                  @if($discount->label)
                    <span class="badge badge-info">{{ $discount->label }}</span>
                  @else
                    <span class="muted">—</span>
                  @endif
                </td>
                <td>
                  <form method="POST" action="{{ route('va.discounts.destroy', $discount) }}"
                        onsubmit="return confirm('¿Eliminar este descuento?')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

@endsection
