@extends('layouts.admin')

@section('title', 'Mensajes')
@section('page_title', 'Mensajes del sistema')
@section('page_subtitle', 'Avisos globales o individuales para usuarios y admins')

@push('styles')
<style>
  .message-card {
    border: 1px solid #ececec;
    border-radius: 14px;
    padding: 18px 20px;
    margin-bottom: 10px;
    transition: border-color .15s;
  }
  .message-card:last-child { margin-bottom: 0; }
  .message-card.is-active { border-left: 4px solid #111; }
  .message-card.is-inactive { opacity: .6; }
  .message-title { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
  .message-body  { font-size: 14px; color: #555; white-space: pre-wrap; line-height: 1.55; margin-bottom: 12px; }
  .message-meta  { font-size: 12px; color: #aaa; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
</style>
@endpush

@section('content')

  {{-- Precio de membresía --}}
  <div class="admin-card" style="margin-bottom:20px;">
    <div class="section-title" style="margin-bottom:4px;">Precio de membresía mensual</div>
    <div class="section-subtitle" style="margin-bottom:18px;">
      Valor que se muestra en "Hacete socio" y en el checkout de membresía.
    </div>

    <form method="POST" action="{{ route('sa.settings.membership_price') }}" class="form-row">
      @csrf
      <div class="form-group">
        <label class="form-label">Precio mensual (ARS)</label>
        <input type="number" name="membership_price" min="1" step="0.01"
               value="{{ $membershipPrice }}" required class="form-control" style="width:200px;">
      </div>
      <button type="submit" class="btn btn-primary">Guardar precio</button>
    </form>

    <div class="muted" style="margin-top:10px; font-size:13px;">
      Valor actual: <strong>ARS {{ number_format($membershipPrice, 2, ',', '.') }}</strong>
    </div>
  </div>

  {{-- Crear mensaje --}}
  <div class="admin-card" style="margin-bottom:20px;">
    <div class="section-title" style="margin-bottom:16px;">Crear mensaje</div>

    <form method="POST" action="{{ route('sa.messages.store') }}" style="display:grid; gap:14px; max-width:680px;">
      @csrf

      <div class="form-group">
        <label class="form-label">Título</label>
        <input name="title" required class="form-control" style="width:100%;">
      </div>

      <div class="form-group">
        <label class="form-label">Mensaje</label>
        <textarea name="body" rows="4" required class="form-control" style="width:100%; resize:vertical;"></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Destinatario</label>
        <select name="target_user_id" class="form-control" style="width:100%;">
          <option value="">Todos los usuarios</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <button type="submit" class="btn btn-primary">Crear mensaje</button>
      </div>
    </form>
  </div>

  {{-- Lista de mensajes --}}
  <div class="admin-card">
    <div class="section-title" style="margin-bottom:16px;">Mensajes creados</div>

    @if($messages->isEmpty())
      <div class="empty-state">No hay mensajes cargados.</div>
    @else
      @foreach($messages as $message)
        <div class="message-card {{ $message->is_active ? 'is-active' : 'is-inactive' }}">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
            <div style="flex:1; min-width:0;">
              <div class="message-title">{{ $message->title }}</div>
              <div class="message-body">{{ $message->body }}</div>
              <div class="message-meta">
                <span>
                  Para: <strong>{{ $message->targetUser ? $message->targetUser->name : 'Todos los usuarios' }}</strong>
                </span>
                <span class="badge {{ $message->is_active ? 'badge-success' : 'badge-default' }}">
                  {{ $message->is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>
            </div>

            <div style="display:flex; gap:8px; flex-wrap:wrap; flex-shrink:0;">
              <form method="POST" action="{{ route('sa.messages.toggle', $message) }}">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm">
                  {{ $message->is_active ? 'Desactivar' : 'Activar' }}
                </button>
              </form>

              <form method="POST" action="{{ route('sa.messages.destroy', $message) }}"
                    onsubmit="return confirm('¿Eliminar este mensaje?')">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    @endif
  </div>

@endsection
