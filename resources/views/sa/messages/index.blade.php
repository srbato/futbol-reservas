@extends('layouts.admin')

@section('title', 'Mensajes')
@section('page_title', 'Mensajes del sistema')

@section('content')

  {{-- Precio de membresía --}}
  <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-6">

    <div class="mb-4">
      <h2 class="text-base font-bold text-slate-900">Precio de membresía mensual</h2>
      <p class="text-sm text-slate-500 mt-0.5">Valor que se muestra en "Hacete socio" y en el checkout de membresía.</p>
    </div>

    <form method="POST" action="{{ route('sa.settings.membership_price') }}"
          class="flex flex-wrap items-end gap-4">
      @csrf
      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Precio mensual (ARS)</label>
        <input type="number" name="membership_price" min="1" step="0.01"
               value="{{ $membershipPrice }}" required
               class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-48">
      </div>
      <button type="submit"
              class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-indigo-200 hover:shadow-md transition-all duration-200">
        Guardar precio
      </button>
    </form>

    <p class="mt-3 text-sm text-slate-500">
      Valor actual: <span class="font-bold text-slate-900">ARS {{ number_format($membershipPrice, 2, ',', '.') }}</span>
    </p>
  </div>

  {{-- Crear mensaje --}}
  <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-6">

    <h2 class="text-base font-bold text-slate-900 mb-4">Crear mensaje</h2>

    <form method="POST" action="{{ route('sa.messages.store') }}"
          class="grid gap-4 max-w-2xl">
      @csrf

      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Título</label>
        <input name="title" required
               class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200">
      </div>

      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Mensaje</label>
        <textarea name="body" rows="4" required
                  class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 resize-y"></textarea>
      </div>

      <div class="flex flex-col gap-1">
        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Destinatario</label>
        <select name="target_user_id"
                class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-all duration-200">
          <option value="">Todos los usuarios</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-sm font-semibold rounded-xl shadow-sm hover:shadow-indigo-200 hover:shadow-md transition-all duration-200">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
          </svg>
          Crear mensaje
        </button>
      </div>
    </form>
  </div>

  {{-- Lista de mensajes manuales --}}
  <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 mb-6">

    <h2 class="text-base font-bold text-slate-900 mb-4">Mensajes creados</h2>

    @if($messages->isEmpty())
      <div class="py-10 text-center">
        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <p class="text-sm text-slate-400">No hay mensajes cargados.</p>
      </div>
    @else
      <div class="space-y-3">
        @foreach($messages as $message)
          <div class="border rounded-xl p-4 transition-colors
                      {{ $message->is_active
                           ? 'border-l-4 border-l-indigo-500 border-slate-200 bg-white'
                           : 'border-slate-200 bg-slate-50 opacity-60' }}">
            <div class="flex items-start justify-between gap-4 flex-wrap">
              <div class="flex-1 min-w-0">
                <p class="font-bold text-slate-900 text-sm mb-1">{{ $message->title }}</p>
                <p class="text-sm text-slate-600 whitespace-pre-wrap leading-relaxed mb-3">{{ $message->body }}</p>
                <div class="flex items-center gap-3 flex-wrap text-xs text-slate-400">
                  <span>
                    Para: <span class="font-semibold text-slate-600">{{ $message->targetUser ? $message->targetUser->name : 'Todos los usuarios' }}</span>
                  </span>
                  @if($message->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Activo</span>
                  @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-600">Inactivo</span>
                  @endif
                </div>
              </div>

              <div class="flex gap-2 flex-shrink-0 flex-wrap">
                <form method="POST" action="{{ route('sa.messages.toggle', $message) }}">
                  @csrf
                  <button type="submit"
                          class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                    {{ $message->is_active ? 'Desactivar' : 'Activar' }}
                  </button>
                </form>

                <form method="POST" action="{{ route('sa.messages.destroy', $message) }}"
                      onsubmit="return confirm('¿Eliminar este mensaje?')">
                  @csrf
                  <button type="submit"
                          class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-500 hover:from-red-500 hover:to-red-400 text-white text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
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

  {{-- Notificaciones automáticas del sistema --}}
  @if($systemMessages->isNotEmpty())
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">

      <div class="mb-4">
        <h2 class="text-base font-bold text-slate-900">Notificaciones automáticas</h2>
        <p class="text-sm text-slate-500 mt-0.5">Generadas por el sistema (recordatorios de membresía, etc.). No fueron creadas por vos.</p>
      </div>

      <div class="space-y-3">
        @foreach($systemMessages as $message)
          <div class="border border-slate-200 rounded-xl p-4 bg-slate-50
                      {{ $message->is_active ? '' : 'opacity-60' }}">
            <div class="flex items-start justify-between gap-4 flex-wrap">
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 text-sm mb-1">{{ $message->title }}</p>
                <p class="text-xs text-slate-500 whitespace-pre-wrap leading-relaxed mb-2">{{ $message->body }}</p>
                <div class="flex items-center gap-3 flex-wrap text-xs text-slate-400">
                  <span>Para: <span class="font-semibold text-slate-600">{{ $message->targetUser ? $message->targetUser->name : 'Todos' }}</span></span>
                  @if($message->is_active)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Activo</span>
                  @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-200 text-slate-600">Inactivo</span>
                  @endif
                </div>
              </div>
              <div class="flex-shrink-0">
                <form method="POST" action="{{ route('sa.messages.destroy', $message) }}"
                      onsubmit="return confirm('¿Eliminar esta notificación?')">
                  @csrf
                  <button type="submit"
                          class="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 text-slate-700 text-xs font-semibold rounded-xl shadow-sm transition-all duration-200">
                    Eliminar
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif

@endsection
