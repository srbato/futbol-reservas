@extends('layouts.admin')

@section('title', 'Mensajes')
@section('page_title', 'Mensajes del sistema')
@section('page_subtitle', 'Avisos globales o individuales para usuarios y admins')

@section('content')
  <div class="admin-card" style="margin-bottom:20px;">
    <h2 style="margin-top:0;">Precio de membresía mensual</h2>

    <p class="muted" style="margin-top:0; margin-bottom:14px;">
      Este valor se usa en la pantalla “Hacete socio” y en el checkout de la membresía para administradores de complejos.
    </p>

    <form method="POST" action="{{ route('sa.settings.membership_price') }}" style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; max-width:760px;">
      @csrf

      <div>
        <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Precio mensual</label>
        <input
          type="number"
          name="membership_price"
          min="1"
          step="0.01"
          value="{{ $membershipPrice }}"
          required
          style="padding:10px 12px; border:1px solid #ddd; border-radius:10px; min-width:220px;"
        >
      </div>

      <div>
        <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
          Guardar precio
        </button>
      </div>
    </form>

    <div class="muted" style="margin-top:10px; font-size:13px;">
      Valor actual:
      <strong>ARS {{ number_format($membershipPrice, 2, ',', '.') }}</strong>
    </div>
  </div>

  <div class="admin-card" style="margin-bottom:20px;">
    <h2 style="margin-top:0;">Crear mensaje</h2>

    <form method="POST" action="{{ route('sa.messages.store') }}" style="display:grid; gap:12px; max-width:760px;">
      @csrf

      <div>
        <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Título</label>
        <input name="title" required style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px;">
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Mensaje</label>
        <textarea name="body" rows="5" required style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px;"></textarea>
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Destinatario</label>
        <select name="target_user_id" style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:10px;">
          <option value="">Todos los usuarios</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
          Crear mensaje
        </button>
      </div>
    </form>
  </div>

  <div class="admin-card">
    <h2 style="margin-top:0;">Mensajes creados</h2>

    @if($messages->isEmpty())
      <p>No hay mensajes cargados.</p>
    @else
      <div style="display:grid; gap:14px;">
        @foreach($messages as $message)
          <div style="padding:14px; border:1px solid #eee; border-radius:12px;">
            <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:start;">
              <div>
                <div style="font-size:20px; font-weight:700;">{{ $message->title }}</div>
                <div class="muted" style="margin-top:6px; white-space:pre-wrap;">{{ $message->body }}</div>

                <div style="margin-top:10px; font-size:13px; color:#666;">
                  Destino:
                  <strong>
                    {{ $message->targetUser ? $message->targetUser->name . ' (' . $message->targetUser->email . ')' : 'Todos' }}
                  </strong>
                  — Estado:
                  <strong>{{ $message->is_active ? 'Activo' : 'Inactivo' }}</strong>
                </div>
              </div>

              <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <form method="POST" action="{{ route('sa.messages.toggle', $message) }}">
                  @csrf
                  <button type="submit" style="padding:8px 12px; border:0; background:#111; color:#fff; border-radius:8px; cursor:pointer;">
                    {{ $message->is_active ? 'Desactivar' : 'Activar' }}
                  </button>
                </form>

                <form method="POST" action="{{ route('sa.messages.destroy', $message) }}" onsubmit="return confirm('¿Eliminar este mensaje?');">
                  @csrf
                  <button type="submit" style="padding:8px 12px; border:0; background:#842029; color:#fff; border-radius:8px; cursor:pointer;">
                    Eliminar
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
@endsection