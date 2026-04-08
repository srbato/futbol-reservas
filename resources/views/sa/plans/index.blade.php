@extends('layouts.admin')

@section('title', 'Planes de membresía')
@section('page_title', 'Planes de membresía')

@section('content')

  <div class="space-y-5">
    @foreach($plans as $plan)
      <div class="bg-white border rounded-2xl shadow-sm overflow-hidden
                  {{ $plan->is_featured ? 'border-l-4 border-l-indigo-500 border-slate-200' : 'border-slate-200' }}">

        {{-- Plan header --}}
        <div class="flex items-center justify-between gap-4 flex-wrap px-6 py-4 border-b border-slate-100 bg-slate-50">
          <div class="flex items-center gap-3 flex-wrap">
            <h2 class="text-lg font-extrabold text-slate-900">{{ $plan->name }}</h2>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">{{ $plan->slug }}</span>
            @if($plan->is_featured)
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-600 text-white">
                Destacado
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
              Mensual:
              <span class="font-extrabold text-slate-900 text-base">ARS {{ number_format($plan->monthly_price, 0, ',', '.') }}</span>
            </span>
            <span class="text-slate-300">·</span>
            <span>
              {{ $plan->longTermLabel() }}:
              <span class="font-bold text-slate-900">ARS {{ number_format($plan->annualTotalPrice(), 0, ',', '.') }}</span>
              <span class="text-xs text-slate-400">({{ $plan->annual_discount_percentage }}% off · {{ $plan->longTermMonths() }} meses)</span>
            </span>
          </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('sa.plans.update', $plan) }}" class="px-6 py-5">
          @csrf

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-5">

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Nombre del plan</label>
              <input type="text" name="name" value="{{ old('name', $plan->name) }}" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Máx. canchas</label>
              <input type="number" name="max_fields" value="{{ old('max_fields', $plan->max_fields) }}"
                     min="1" placeholder="Ilimitadas"
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Precio mensual (ARS)</label>
              <input type="number" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price) }}"
                     min="1" step="0.01" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Días prueba gratuita</label>
              <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}"
                     min="0" max="365" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
              <p class="text-xs mt-1
                        {{ $plan->trial_days > 0 ? 'text-emerald-600 font-semibold' : 'text-slate-400' }}">
                {{ $plan->trial_days > 0 ? $plan->trial_days.' días gratis activos' : 'Sin período de prueba' }}
              </p>
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipo plan largo plazo</label>
              <select name="long_term_months" required
                      class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
                <option value="12" {{ (old('long_term_months', $plan->long_term_months) == 12) ? 'selected' : '' }}>Anual (12 meses)</option>
                <option value="6"  {{ (old('long_term_months', $plan->long_term_months) == 6)  ? 'selected' : '' }}>Semestral (6 meses)</option>
              </select>
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Descuento largo plazo (%)</label>
              <input type="number" name="annual_discount_percentage"
                     value="{{ old('annual_discount_percentage', $plan->annual_discount_percentage) }}"
                     min="0" max="100" step="0.01" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
              <p class="text-xs text-slate-400 mt-1">
                {{ $plan->longTermLabel() }}: ARS {{ number_format($plan->annualTotalPrice(), 0, ',', '.') }}
              </p>
            </div>

            <div class="flex flex-col gap-1">
              <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Orden de aparición</label>
              <input type="number" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}"
                     min="0" required
                     class="px-3 py-2 text-sm border border-slate-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 focus:shadow-indigo-100 transition-all duration-200 w-full">
            </div>

            <div class="flex flex-col gap-3 justify-end pb-1">
              <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Plan activo
              </label>
              <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-700">
                <input type="hidden" name="is_featured" value="0">
                <input type="checkbox" name="is_featured" value="1"
                       {{ old('is_featured', $plan->is_featured) ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                Destacado
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
    <h3 class="text-sm font-bold text-slate-900 mb-3">Como funciona la facturacion</h3>
    <div class="text-sm text-slate-600 leading-relaxed space-y-2">
      <p>
        <span class="font-semibold text-slate-800">Mensual:</span>
        el usuario paga el precio mensual del plan y obtiene 30 dias de acceso.
      </p>
      <p>
        <span class="font-semibold text-slate-800">Anual / Semestral:</span>
        el usuario paga <code class="text-xs bg-slate-200 px-1.5 py-0.5 rounded font-mono">precio_mensual x meses x (1 - descuento%)</code>
        de una sola vez y obtiene acceso por esa cantidad de meses (12 o 6).
      </p>
      <p>
        El <span class="font-semibold">slug</span> del plan (starter, pro, full) no se puede cambiar desde aca
        ya que identifica el plan en el codigo. Si necesitas agregar un plan nuevo, contacta al equipo tecnico.
      </p>
    </div>
  </div>

@endsection
