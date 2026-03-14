@extends('layouts.admin')

@section('title', 'Descuentos')
@section('page_title', 'Descuentos')
@section('page_subtitle', 'Configurá precios promocionales por día o por horario')

@section('content')

  {{-- Recurring discounts section --}}
  <div class="admin-card" style="margin-bottom:20px;">
    <h2 style="margin-top:0;">Descuentos por reserva recurrente</h2>
    <p class="muted" style="margin-top:0;">
      Configurá un porcentaje de descuento automático cuando un usuario reserva múltiples turnos a la vez.
      El sistema aplica el tier más alto que corresponda según la cantidad de turnos.
    </p>

    <form method="POST" action="{{ route('va.recurring_discounts.store') }}"
          style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:20px;">
      @csrf

      <div>
        <label style="display:block; font-size:12px; color:#666;">Cancha</label>
        <select name="field_id" required style="padding:10px; border:1px solid #ddd; border-radius:10px;">
          <option value="">Seleccionar</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}">{{ $field->venue->name }} — {{ $field->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Desde N turnos</label>
        <select name="min_occurrences" required style="padding:10px; border:1px solid #ddd; border-radius:10px;">
          @foreach([2,3,4,5,6,8,10,12] as $n)
            <option value="{{ $n }}">{{ $n }} turnos</option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Descuento (%)</label>
        <input type="number" step="0.01" min="1" max="100" name="discount_percentage" required
               placeholder="Ej: 10"
               style="padding:10px; border:1px solid #ddd; border-radius:10px; width:100px;">
      </div>

      <button type="submit"
              style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
        Guardar tier
      </button>
    </form>

    @if($recurringDiscounts->isNotEmpty())
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Cancha</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Desde</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Descuento</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Acción</th>
        </tr>
        @foreach($recurringDiscounts as $rd)
          <tr>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              {{ $rd->field->venue->name }} / {{ $rd->field->name }}
            </td>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              {{ $rd->min_occurrences }}+ turnos
            </td>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3; font-weight:700; color:#157347;">
              {{ $rd->discount_percentage }}%
            </td>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              <form method="POST" action="{{ route('va.recurring_discounts.destroy', $rd) }}">
                @csrf
                <button type="submit">Eliminar</button>
              </form>
            </td>
          </tr>
        @endforeach
      </table>
    @else
      <p class="muted">No hay tiers configurados todavía.</p>
    @endif
  </div>

  <div class="admin-card" style="margin-bottom:20px;">
    <h2 style="margin-top:0;">Crear descuento</h2>

    <form method="POST" action="{{ route('va.discounts.store') }}"
          style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
      @csrf

      <div>
        <label style="display:block; font-size:12px; color:#666;">Cancha</label>
        <select name="field_id" required style="padding:10px; border:1px solid #ddd; border-radius:10px;">
          <option value="">Seleccionar</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}">
              {{ $field->venue->name }} — {{ $field->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Día de semana</label>
        <select name="day_of_week" style="padding:10px; border:1px solid #ddd; border-radius:10px;">
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

      <div>
        <label style="display:block; font-size:12px; color:#666;">Fecha puntual</label>
        <input type="date" name="date" style="padding:10px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Hora inicio</label>
        <input type="time" name="start_time" style="padding:10px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Hora fin</label>
        <input type="time" name="end_time" style="padding:10px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Precio promo</label>
        <input type="number" step="0.01" name="discount_price" required
               style="padding:10px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Etiqueta</label>
        <input name="label" placeholder="Ej: Promo tarde"
               style="padding:10px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <button type="submit"
              style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
        Guardar descuento
      </button>
    </form>

    <p class="muted" style="margin-top:12px;">
      Si dejás horas vacías, el descuento aplica a todo el día. Si ponés fecha, aplica solo a esa fecha.
    </p>
  </div>

  <div class="admin-card">
    <h2 style="margin-top:0;">Descuentos cargados</h2>

    @if($discounts->isEmpty())
      <p>No hay descuentos cargados.</p>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Cancha</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Día/Fecha</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Horario</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Precio</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Etiqueta</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Acción</th>
        </tr>

        @foreach($discounts as $discount)
          <tr>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              {{ $discount->field->venue->name }} / {{ $discount->field->name }}
            </td>

            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              @if($discount->date)
                {{ $discount->date->format('d/m/Y') }}
              @elseif(!is_null($discount->day_of_week))
                @php
                  $days = [0=>'Domingo',1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado'];
                @endphp
                {{ $days[$discount->day_of_week] }}
              @else
                Todos los días
              @endif
            </td>

            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              {{ $discount->start_time ?? 'Todo el día' }}
              @if($discount->end_time)
                - {{ $discount->end_time }}
              @endif
            </td>

            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              ${{ number_format($discount->discount_price, 0, ',', '.') }}
            </td>

            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              {{ $discount->label ?? '—' }}
            </td>

            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              <form method="POST" action="{{ route('va.discounts.destroy', $discount) }}">
                @csrf
                <button type="submit">Eliminar</button>
              </form>
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>
@endsection