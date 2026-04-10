@extends('layouts.admin')

@section('title', 'Planes de organizadores')
@section('page_title', 'Planes de organizadores')

@section('content')

  <div class="space-y-5">
    @foreach($plans as $plan)
      <div class="bg-white border rounded-2xl shadow-sm overflow-hidden
                  {{ $plan->slug === 'pro' ? 'border-l-4 border-l-indigo-500 border-slate-200' : 'border-slate-200' }}">

        {{-- Plan header --}}
        <div class="flex items-center justify-between gap-4 flex-wrap px-6 py-4 border-b border-slate-100 bg-slate-50">
          <div class="flex items-center gap-3 flex-wrap">
            <h2 class="text-lg font-extrabold text-slate-900">{{ $plan->name }}</h2>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $plan->slug }}</span>
            @if($plan->slug === 'pro')
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-600 text-white">
                Pro
              </span>
            @endif
            @if(!$plan->is_active)
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                Inactivo
              </span>
            @endif
          </div>

          <div class="text-sm text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1">
            <span>
              Precio:
              <span class="font-extrabold text-slate-900 text-base">
                @if($plan->monthly_price > 0)
                  ARS {{ number_format($plan->monthly_price, 0, ',', '.') }}/mes
                @else
                  Gratis
                @endif
              </span>
            </span>
            <span class="text-slate-300">·</span>
            <span>
              Max torneos:
              <span class="font-bold text-slate-900">{{ $plan->isUnlimited() ? 'Ilimitados' : $plan->max_tournaments }}</span>
            </span>
            <span class="text-slate-300">·</span>
            <span>
              Max equipos:
              <span class="font-bold text-slate-900">{{ $plan->max_teams_per_tournament }}</span>
            </span>
          </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('sa.organizer_plans.update', $plan) }}" class="px-6 py-5">
          @csrf

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-5">

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nombre del plan</label>
              <input type="text" name="name" value="{{ old('name', $plan->name) }}" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Precio mensual (ARS)</label>
              <input type="number" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price) }}"
                     min="0" step="0.01" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Max torneos activos</label>
              <input type="number" name="max_tournaments" value="{{ old('max_tournaments', $plan->max_tournaments) }}"
                     min="-1" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
              <p class="text-xs mt-1 {{ $plan->isUnlimited() ? 'text-emerald-600 font-semibold' : 'text-slate-400' }}">
                {{ $plan->isUnlimited() ? 'Ilimitados (-1)' : $plan->max_tournaments . ' torneo(s)' }}
              </p>
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Max equipos por torneo</label>
              <input type="number" name="max_teams_per_tournament" value="{{ old('max_teams_per_tournament', $plan->max_teams_per_tournament) }}"
                     min="1" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Orden de aparicion</label>
              <input type="number" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}"
                     min="0" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
            </div>

            {{-- Formatos disponibles --}}
            <div class="flex flex-col gap-1 sm:col-span-2">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Formatos disponibles</label>
              <div class="flex flex-wrap gap-4 mt-1">
                @foreach(['single_elimination' => 'Eliminacion directa', 'round_robin' => 'Liga (todos contra todos)', 'groups_elimination' => 'Grupos + Eliminacion'] as $format => $label)
                  <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                    <input type="checkbox" name="available_formats[]" value="{{ $format }}"
                           {{ in_array($format, old('available_formats', $plan->available_formats ?? [])) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    {{ $label }}
                  </label>
                @endforeach
              </div>
            </div>
          </div>

          {{-- Feature toggles --}}
          <div class="border-t border-slate-100 pt-4 mb-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Funcionalidades</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
              <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                <input type="hidden" name="has_stats" value="0">
                <input type="checkbox" name="has_stats" value="1"
                       {{ old('has_stats', $plan->has_stats) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Estadisticas
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                <input type="hidden" name="has_notifications" value="0">
                <input type="checkbox" name="has_notifications" value="1"
                       {{ old('has_notifications', $plan->has_notifications) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Notificaciones
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                <input type="hidden" name="has_custom_branding" value="0">
                <input type="checkbox" name="has_custom_branding" value="1"
                       {{ old('has_custom_branding', $plan->has_custom_branding) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Branding propio
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                <input type="hidden" name="has_mp_payments" value="0">
                <input type="checkbox" name="has_mp_payments" value="1"
                       {{ old('has_mp_payments', $plan->has_mp_payments) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Cobro MercadoPago
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Plan activo
              </label>
            </div>
          </div>

          <div class="flex items-center gap-5 flex-wrap pt-1 border-t border-slate-100">
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white text-sm font-bold rounded-xl shadow-sm hover:shadow-indigo-200 hover:shadow-md transition-all duration-200">
              Guardar cambios
            </button>
            <p class="text-xs text-slate-400">
              Slug: <code class="font-mono text-slate-600">{{ $plan->slug }}</code>
              <span class="mx-1">·</span>
              ID: {{ $plan->id }}
            </p>
          </div>

        </form>
      </div>
    @endforeach
  </div>

  {{-- Info box --}}
  <div class="mt-6 bg-slate-50 border border-slate-200 rounded-2xl p-6">
    <h3 class="text-sm font-bold text-slate-900 mb-3">Como funcionan los planes de organizador</h3>
    <div class="text-sm text-slate-600 leading-relaxed space-y-2">
      <p>
        <span class="font-semibold text-slate-800">Plan Gratis:</span>
        permite crear torneos con limitaciones (1 activo, 8 equipos, solo eliminacion directa).
      </p>
      <p>
        <span class="font-semibold text-slate-800">Plan Pro:</span>
        suscripcion mensual via MercadoPago. Desbloquea torneos ilimitados, mas formatos, estadisticas y funcionalidades avanzadas.
      </p>
      <p>
        El <span class="font-semibold">slug</span> del plan (free, pro) no se puede cambiar desde aca
        ya que identifica el plan en el codigo. Si necesitas agregar un plan nuevo, contacta al equipo tecnico.
      </p>
      <p>
        <span class="font-semibold text-slate-800">Max torneos = -1</span> significa ilimitados.
      </p>
    </div>
  </div>

@endsection
