@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page_title', 'Usuarios del sistema')
@section('page_subtitle', 'Vista global para superadmin')

@section('content')
  <div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
      <div>
        <h2 style="margin:0;">Todos los usuarios</h2>
        <p class="muted" style="margin:6px 0 0 0;">
          Incluye usuarios normales, admins de complejos y superadmins.
        </p>
      </div>
    </div>

    <form method="GET" action="{{ route('sa.users.index') }}"
          style="display:flex; gap:12px; flex-wrap:wrap; align-items:end; margin-bottom:18px;">
      <div>
        <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Buscar</label>
        <input
          type="text"
          name="q"
          value="{{ $q ?? '' }}"
          placeholder="Nombre o email"
          style="padding:10px 12px; border:1px solid #ddd; border-radius:10px; min-width:240px;"
        >
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Rol</label>
        <select name="role" style="padding:10px 12px; border:1px solid #ddd; border-radius:10px; min-width:180px;">
          <option value="">Todos</option>
          <option value="user" {{ ($role ?? '') === 'user' ? 'selected' : '' }}>user</option>
          <option value="venue_admin" {{ ($role ?? '') === 'venue_admin' ? 'selected' : '' }}>venue_admin</option>
          <option value="super_admin" {{ ($role ?? '') === 'super_admin' ? 'selected' : '' }}>super_admin</option>
        </select>
      </div>

      <button type="submit"
              style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
        Filtrar
      </button>

      <a href="{{ route('sa.users.index') }}"
         style="padding:10px 14px; border:1px solid #ddd; background:#fff; color:#111; border-radius:10px; text-decoration:none;">
        Limpiar
      </a>
    </form>

    @if($users->isEmpty())
      <p>No hay usuarios que coincidan con la búsqueda.</p>
    @else
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:1100px;">
          <thead>
            <tr style="background:#fafafa;">
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">ID</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Usuario</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Email</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Estado</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Rol actual</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Cambiar rol</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Creado</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Acciones</th>
            </tr>
          </thead>

          <tbody>
            @foreach($users as $user)
              <tr>
                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $user->id }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        @if($user->avatar_path)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}"
                                alt="{{ $user->name }}"
                                style="width:32px; height:32px; border-radius:999px; object-fit:cover; border:1px solid #eee; flex-shrink:0;"
                            >
                        @else
                            <div style="width:32px; height:32px; border-radius:999px; background:#f1f1f1; display:flex; align-items:center; justify-content:center; font-size:13px; color:#999; border:1px solid #eee; flex-shrink:0;">
                                👤
                            </div>
                        @endif
                        <strong>{{ $user->name }}</strong>
                    </div>
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $user->email }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  @if($user->is_active)
                    <span class="badge">Activo</span>
                  @else
                    <span class="badge" style="background:#f8d7da; color:#842029;">Desactivado</span>
                  @endif
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  <span class="badge">{{ $user->role }}</span>
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  <form method="POST" action="{{ route('sa.users.role', $user) }}" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                    @csrf

                    <select name="role" style="padding:8px 10px; border:1px solid #ddd; border-radius:8px;">
                      <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>user</option>
                      <option value="venue_admin" {{ $user->role === 'venue_admin' ? 'selected' : '' }}>venue_admin</option>
                      <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>super_admin</option>
                    </select>

                    <button type="submit" style="padding:8px 12px; border:0; background:#111; color:#fff; border-radius:8px; cursor:pointer;">
                      Guardar
                    </button>
                  </form>
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $user->created_at?->format('d/m/Y H:i') }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    @if(auth()->id() !== $user->id)
                      @if($user->is_active)
                        <form method="POST" action="{{ route('sa.users.deactivate', $user) }}" onsubmit="return confirm('¿Seguro que querés desactivar este usuario?');">
                          @csrf
                          <button type="submit" style="padding:8px 12px; border:0; background:#856404; color:#fff; border-radius:8px; cursor:pointer;">
                            Desactivar
                          </button>
                        </form>
                      @else
                        <form method="POST" action="{{ route('sa.users.activate', $user) }}">
                          @csrf
                          <button type="submit" style="padding:8px 12px; border:0; background:#157347; color:#fff; border-radius:8px; cursor:pointer;">
                            Activar
                          </button>
                        </form>
                      @endif

                      <form method="POST" action="{{ route('sa.users.destroy', $user) }}" onsubmit="return confirm('¿Seguro que querés eliminar este usuario?');">
                        @csrf
                        <button type="submit" style="padding:8px 12px; border:0; background:#842029; color:#fff; border-radius:8px; cursor:pointer;">
                          Eliminar
                        </button>
                      </form>
                    @else
                      <span class="muted">Tu usuario</span>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endsection