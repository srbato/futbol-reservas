@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page_title', 'Usuarios del sistema')

@section('content')

  {{-- Filtros --}}
  <form method="GET" action="{{ route('sa.users.index') }}"
        class="bg-white border border-slate-200 rounded-2xl px-5 py-4 mb-6 flex flex-wrap gap-4 items-end shadow-sm">

    <div class="flex flex-col gap-1">
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Buscar</label>
      <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Nombre o email"
             class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 min-w-[220px]">
    </div>

    <div class="flex flex-col gap-1">
      <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rol</label>
      <select name="role"
              class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-all duration-200">
        <option value="">Todos</option>
        <option value="user"        {{ ($role ?? '') === 'user'        ? 'selected' : '' }}>Usuario</option>
        <option value="venue_admin" {{ ($role ?? '') === 'venue_admin' ? 'selected' : '' }}>Admin complejo</option>
        <option value="super_admin" {{ ($role ?? '') === 'super_admin' ? 'selected' : '' }}>Superadmin</option>
      </select>
    </div>

    <div class="flex gap-2">
      <button type="submit"
              class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-200 hover:shadow-indigo-200 hover:shadow-md">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        Filtrar
      </button>
      <a href="{{ route('sa.users.index') }}"
         class="inline-flex items-center px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-semibold rounded-xl shadow-sm hover:bg-slate-50 hover:border-slate-300 transition-all duration-200">
        Limpiar
      </a>
    </div>

  </form>

  {{-- Tabla --}}
  @if($users->isEmpty())
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm px-8 py-16 text-center">
      <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
      <p class="text-slate-400 text-sm">No hay usuarios que coincidan con la búsqueda.</p>
    </div>
  @else
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm" style="min-width:900px;">
          <thead>
            <tr class="border-b border-slate-200 bg-slate-50">
              <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Usuario</th>
              <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
              <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
              <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Rol</th>
              <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Cambiar rol</th>
              <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Creado</th>
              <th class="text-left px-5 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            @foreach($users as $user)
              <tr class="hover:bg-slate-50 transition-colors">

                {{-- Usuario --}}
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    @if($user->avatar_path)
                      <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}"
                           alt="{{ $user->name }}"
                           class="w-9 h-9 rounded-full object-cover border border-slate-200 flex-shrink-0">
                    @else
                      <div class="w-9 h-9 rounded-full bg-indigo-100 border border-slate-200 flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-bold text-indigo-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                      </div>
                    @endif
                    <div>
                      <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                      <p class="text-xs text-slate-400">#{{ $user->id }}</p>
                    </div>
                  </div>
                </td>

                {{-- Email --}}
                <td class="px-5 py-4 text-slate-600">{{ $user->email }}</td>

                {{-- Estado --}}
                <td class="px-5 py-4">
                  @if($user->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                      Activo
                    </span>
                  @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                      Desactivado
                    </span>
                  @endif
                </td>

                {{-- Rol badge --}}
                <td class="px-5 py-4">
                  @php
                    $roleClasses = [
                      'user'        => 'bg-slate-100 text-slate-600',
                      'venue_admin' => 'bg-blue-100 text-blue-700',
                      'super_admin' => 'bg-indigo-600 text-white',
                    ][$user->role] ?? 'bg-slate-100 text-slate-600';
                  @endphp
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $roleClasses }}">
                    {{ $user->role }}
                  </span>
                </td>

                {{-- Cambiar rol --}}
                <td class="px-5 py-4">
                  <form method="POST" action="{{ route('sa.users.role', $user) }}"
                        class="flex items-center gap-2">
                    @csrf
                    <select name="role"
                            class="px-2.5 py-1.5 text-xs border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-all duration-200">
                      <option value="user"        {{ $user->role === 'user'        ? 'selected' : '' }}>user</option>
                      <option value="venue_admin" {{ $user->role === 'venue_admin' ? 'selected' : '' }}>venue_admin</option>
                      <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>super_admin</option>
                    </select>
                    <button type="submit"
                            class="px-3 py-1.5 bg-gradient-to-r from-slate-800 to-slate-700 hover:from-slate-700 hover:to-slate-600 text-white text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                      Guardar
                    </button>
                  </form>
                </td>

                {{-- Creado --}}
                <td class="px-5 py-4 text-xs text-slate-400 whitespace-nowrap">
                  {{ $user->created_at?->format('d/m/Y') }}
                </td>

                {{-- Acciones --}}
                <td class="px-5 py-4">
                  @if(auth()->id() !== $user->id)
                    <div class="flex items-center gap-2 flex-wrap">

                      {{-- Toggle activo/inactivo --}}
                      @if($user->is_active)
                        <form method="POST" action="{{ route('sa.users.deactivate', $user) }}"
                              onsubmit="return confirm('¿Desactivar este usuario?')">
                          @csrf
                          <button type="submit"
                                  class="px-3 py-1.5 bg-gradient-to-r from-amber-500 to-amber-400 hover:from-amber-400 hover:to-amber-300 text-white text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                            Desactivar
                          </button>
                        </form>
                      @else
                        <form method="POST" action="{{ route('sa.users.activate', $user) }}">
                          @csrf
                          <button type="submit"
                                  class="px-3 py-1.5 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                            Activar
                          </button>
                        </form>
                      @endif

                      {{-- Impersonar --}}
                      @if($user->role !== 'super_admin')
                        <form method="POST" action="{{ route('sa.users.impersonate', $user) }}"
                              onsubmit="return confirm('¿Iniciar sesión como {{ $user->name }}?')">
                          @csrf
                          <button type="submit"
                                  class="px-3 py-1.5 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-500 hover:to-violet-400 text-white text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                            Ingresar como
                          </button>
                        </form>
                      @endif

                      {{-- Eliminar --}}
                      <form method="POST" action="{{ route('sa.users.destroy', $user) }}"
                            onsubmit="return confirm('¿Eliminar definitivamente este usuario?')">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                          Eliminar
                        </button>
                      </form>

                    </div>
                  @else
                    <span class="text-xs text-slate-400 italic">Tu cuenta</span>
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
