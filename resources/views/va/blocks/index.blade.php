@extends('layouts.admin')

@section('title', 'Bloqueos de horarios')

@section('page_title', 'Bloqueos')
@section('page_subtitle', 'Bloqueá horarios manualmente por mantenimiento, torneos o eventos')

@section('content')
  <h1>Bloqueos de horarios</h1>

  <div style="padding:16px; border:1px solid #eee; border-radius:16px; margin-bottom:20px; background:#fff;">
    <h2 style="margin-top:0;">Crear bloqueo</h2>

    <form method="POST" action="{{ route('va.blocks.store') }}"
          style="display:flex; gap:12px; flex-wrap:wrap; align-items:end;">
      @csrf

      <div>
        <label style="display:block; font-size:12px; color:#666;">Cancha</label>
        <select name="field_id" required style="padding:8px; border:1px solid #ddd; border-radius:10px;">
          <option value="">Seleccionar</option>
          @foreach($fields as $field)
            <option value="{{ $field->id }}">
              {{ $field->venue->name }} — {{ $field->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Fecha</label>
        <input type="date" name="date" required
               style="padding:8px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Hora inicio</label>
        <input type="time" name="start_time" required
               style="padding:8px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Hora fin</label>
        <input type="time" name="end_time" required
               style="padding:8px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666;">Motivo</label>
        <input name="reason" placeholder="Ej: mantenimiento"
               style="padding:8px; border:1px solid #ddd; border-radius:10px; width:220px;">
      </div>

      <button type="submit"
              style="padding:9px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
        Guardar bloqueo
      </button>
    </form>
  </div>

  <div style="padding:16px; border:1px solid #eee; border-radius:16px; background:#fff;">
    <h2 style="margin-top:0;">Bloqueos cargados</h2>

    @if($blocks->isEmpty())
      <p>No hay bloqueos cargados.</p>
    @else
      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Fecha</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Cancha</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Horario</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Motivo</th>
          <th style="text-align:left; padding:10px; border-bottom:1px solid #eee;">Acción</th>
        </tr>

        @foreach($blocks as $block)
          <tr>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">{{ \Carbon\Carbon::parse($block->date)->format('d/m/Y') }}</td>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              {{ $block->field->venue->name }} / {{ $block->field->name }}
            </td>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              {{ $block->start_time }} - {{ $block->end_time }}
            </td>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              {{ $block->reason ?? '—' }}
            </td>
            <td style="padding:10px; border-bottom:1px solid #f3f3f3;">
              <form method="POST" action="{{ route('va.blocks.destroy', $block) }}">
                @csrf
                <button type="submit">Eliminar</button>
              </form>
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>

  <p style="margin-top:16px;">
    <a href="{{ route('va.dashboard') }}">Volver al panel</a>
  </p>
@endsection