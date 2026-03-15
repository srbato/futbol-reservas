@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page_title', 'Usuarios del sistema')
@section('page_subtitle', 'Gestión global de usuarios y roles')

@push('styles')
<style>
  .role-user        { background: #f1f1f1; color: #444; }
  .role-venue_admin { background: #cfe2ff; color: #084298; }
  .role-super_admin { background: #111;    color: #fff;    }
</style>
@endpush

@section('content')

  {{-- Filtros --}}
  <form method="GET" action="{{ route('sa.users.index') }}" class="filter-bar">
    <div class="form-group">
      <label class="form-label">Buscar</label>
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nombre o email"
             class="form-control" style="min-width:220px;">
    </div>

    <div class="form-group">
      <label class="form-label">Rol</label>
      <select name="role" class="form-control" style="width:auto;">
        <option value="">Todos</option>
        <option value="user"        {{ ($role ?? '') === 'user'        ? 'selected' : '' }}>Usuario</option>
        <option value="venue_admin" {{ ($role ?? '') === 'venue_admin' ? 'selected' : '' }}>Admin complejo</option>
        <option value="super_admin" {{ ($role ?? '') === 'super_admin' ? 'selected' : '' }}>Superadmin</option>
      </select>
    </div>

    <button type="submit" class="btn btn-primary">Filtrar</button>
    <a href="{{ route('sa.users.index') }}" class="btn btn-ghost">Limpiar</a>
  </form>

  {{-- Tabla --}}
  @if($users->isEmpty())
    <div class="admin-card">
      <div class="empty-state">No hay usuarios que coincidan con la búsqueda.</div>
    </div>
  @else
    <div class="admin-card" style="padding:0; overflow:hidden;">
      <div style="overflow-x:auto;">
        <table class="data-table" style="min-width:860px;">
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Email</th>
              <th>Estado</th>
              <th>Rol</th>
              <th>Cambiar rol</th>
              <th>Creado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    @if($user->avatar_path)
                      <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}"
                           alt="{{ $user->name }}"
                           style="width:34px; height:34px; border-radius:999px; object-fit:cover; border:1px solid #eee; flex-shrink:0;">
                    @else
                      <div style="width:34px; height:34px; border-radius:999px; background:#f3f3f3; border:1px solid #eee; display:flex; align-items:center; justify-content:center; font-size:14px; color:#bbb; flex-shrink:0;">
                        ●
                      </div>
                    @endif
                    <div>
                      <strong>{{ $user->name }}</strong>
                      <div style="font-size:12px; color:#aaa;">#{{ $user->id }}</div>
                    </div>
                  </div>
                </td>

                <td style="color:#555;">{{ $user->email }}</td>

                <td>
                  @if($user->is_active)
                    <span class="badge badge-success">Activo</span>
                  @else
                    <span class="badge badge-danger">Desactivado</span>
                  @endif
                </td>

                <td>
                  <span class="badge role-{{ $user->role }}">{{ $user->role }}</span>
                </td>

                <td>
                  <form method="POST" action="{{ route('sa.users.role', $user) }}"
                        style="display:flex; gap:8px; align-items:center;">
                    @csrf
                    <select name="role" class="form-control" style="width:auto; padding:7px 10px; font-size:13px;">
                      <option value="user"        {{ $user->role === 'user'        ? 'selected' : '' }}>user</option>
                      <option value="venue_admin" {{ $user->role === 'venue_admin' ? 'selected' : '' }}>venue_admin</option>
                      <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>super_admin</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                  </form>
                </td>

                <td style="color:#aaa; font-size:13px; white-space:nowrap;">
                  {{ $user->created_at?->format('d/m/Y') }}
                </td>

                <td>
                  @if(auth()->id() !== $user->id)
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                      @if($user->is_active)
                        <form method="POST" action="{{ route('sa.users.deactivate', $user) }}"
                              onsubmit="return confirm('¿Desactivar este usuario?')">
                          @csrf
                          <button type="submit" class="btn btn-warning btn-sm">Desactivar</button>
                        </form>
                      @else
                        <form method="POST" action="{{ route('sa.users.activate', $user) }}">
                          @csrf
                          <button type="submit" class="btn btn-success btn-sm">Activar</button>
                        </form>
                      @endif

                      <form method="POST" action="{{ route('sa.users.destroy', $user) }}"
                            onsubmit="return confirm('¿Eliminar definitivamente este usuario?')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                      </form>
                    </div>
                  @else
                    <span class="muted" style="font-size:13px;">Tu cuenta</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif

@endsection
