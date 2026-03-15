@extends('layouts.admin')

@section('title', 'Bloqueos de horarios')
@section('page_title', 'Bloqueos')
@section('page_subtitle', 'Bloqueá horarios manualmente por mantenimiento, torneos o eventos')

@section('content')

  {{-- Formulario de creación --}}
  <div class="admin-card" style="margin-bottom:20px;">
    <div class="section-title" style="margin-bottom:16px;">Nuevo bloqueo</div>

    <form method="POST" action="{{ route('va.blocks.store') }}" class="form-row">
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
        <label class="form-label">Fecha</label>
        <input type="date" name="date" required class="form-control" style="width:auto;">
      </div>

      <div class="form-group">
        <label class="form-label">Hora inicio</label>
        <input type="time" name="start_time" required class="form-control" style="width:auto;">
      </div>

      <div class="form-group">
        <label class="form-label">Hora fin</label>
        <input type="time" name="end_time" required class="form-control" style="width:auto;">
      </div>

      <div class="form-group">
        <label class="form-label">Motivo</label>
        <input name="reason" placeholder="Ej: mantenimiento" class="form-control" style="width:200px;">
      </div>

      <button type="submit" class="btn btn-primary">Guardar bloqueo</button>
    </form>
  </div>

  {{-- Lista de bloqueos --}}
  <div class="admin-card" style="padding:0; overflow:hidden;">
    <div style="padding:18px 18px 14px; border-bottom:1px solid #ececec;">
      <div class="section-title">Bloqueos cargados</div>
    </div>

    @if($blocks->isEmpty())
      <div class="empty-state">No hay bloqueos cargados.</div>
    @else
      <div style="overflow-x:auto;">
        <table class="data-table">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Cancha</th>
              <th>Horario</th>
              <th>Motivo</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($blocks as $block)
              <tr>
                <td style="font-weight:600; white-space:nowrap;">
                  {{ \Carbon\Carbon::parse($block->date)->format('d/m/Y') }}
                </td>
                <td>{{ $block->field->venue->name }} / {{ $block->field->name }}</td>
                <td style="white-space:nowrap;">
                  {{ $block->start_time }} – {{ $block->end_time }}
                </td>
                <td>{{ $block->reason ?? '—' }}</td>
                <td>
                  <form method="POST" action="{{ route('va.blocks.destroy', $block) }}"
                        onsubmit="return confirm('¿Eliminar este bloqueo?')">
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
