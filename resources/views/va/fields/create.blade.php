@extends('layouts.admin')

@section('title', 'Crear cancha')
@section('page_title', 'Crear cancha')
@section('page_subtitle', 'Complejo: ' . $venue->name)

@section('content')
<div class="max-w-2xl space-y-5">

  <form method="POST" action="{{ route('va.fields.store', $venue) }}" enctype="multipart/form-data">
    @csrf

    {{-- Datos básicos --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Datos básicos
      </p>

      <div class="space-y-4">

        {{-- Nombre --}}
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
            Nombre
          </label>
          <input name="name" value="{{ old('name') }}" required maxlength="100"
                 placeholder="Ej: Cancha 1 · Césped"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('name') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('name')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Deporte + Formato --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
              Deporte
            </label>
            <select name="sport" id="sport" onchange="updateFormat()"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 bg-white shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              <option value="football" {{ old('sport') === 'football' ? 'selected' : '' }}>Fútbol</option>
              <option value="padel"    {{ old('sport') === 'padel'    ? 'selected' : '' }}>Pádel</option>
              <option value="tennis"   {{ old('sport') === 'tennis'   ? 'selected' : '' }}>Tenis</option>
              <option value="basketball" {{ old('sport') === 'basketball' ? 'selected' : '' }}>Básquet</option>
              <option value="volleyball" {{ old('sport') === 'volleyball' ? 'selected' : '' }}>Vóley</option>
            </select>
            @error('sport')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div id="format-wrap">
            <label id="format-label" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
              Formato
            </label>
            <select id="format-select" onchange="syncFormat()"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 bg-white shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </select>
            <input type="hidden" name="format" id="format-hidden" value="{{ old('format') }}">
            <p id="format-hint" class="text-xs text-slate-400 mt-1"></p>
            @error('format')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Duración del turno --}}
        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
            Duración del turno
          </label>
          <div class="flex items-center gap-3">
            <input type="number" name="slot_minutes" value="{{ old('slot_minutes', 60) }}" min="30" max="180" required
                   class="w-28 rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('slot_minutes') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
            <span class="text-sm text-slate-500">minutos por turno</span>
          </div>
          @error('slot_minutes')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

      </div>
    </div>

    {{-- Precios --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Precios
      </p>

      <div class="space-y-4">

        {{-- Precio + Moneda --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
              Precio por turno
            </label>
            <input type="number" name="price_per_slot" value="{{ old('price_per_slot', 12000) }}" step="0.01" min="0" required
                   placeholder="Ej: 12000"
                   class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('price_per_slot') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
            @error('price_per_slot')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
              Moneda
            </label>
            <input name="currency" value="{{ old('currency', 'ARS') }}" required
                   class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('currency') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
            @error('currency')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Precio nocturno --}}
        <div class="border border-slate-200 rounded-xl p-4 bg-slate-50">
          <div class="flex items-center justify-between gap-4">
            <div>
              <div class="text-sm font-bold text-slate-700">Precio nocturno</div>
              <div class="text-xs text-slate-500 mt-0.5">Aplicá un precio distinto en horario nocturno</div>
            </div>
            {{-- Toggle --}}
            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
              <input type="checkbox" id="toggle_night_create" class="sr-only peer" onchange="toggleNight('create')">
              <div class="w-10 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:bg-indigo-600 transition-colors duration-200 after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-[18px] after:w-[18px] after:transition-all after:shadow-sm peer-checked:after:translate-x-4"></div>
            </label>
          </div>

          <div id="night_panel_create" class="hidden mt-4 pt-4 border-t border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                  Precio nocturno
                </label>
                <input type="number" name="night_price_per_slot" step="0.01" min="0"
                       value="{{ old('night_price_per_slot') }}" placeholder="Ej: 15000"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                  Desde
                </label>
                <input type="time" name="night_start_time" value="{{ old('night_start_time') }}"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                  Hasta
                </label>
                <input type="time" name="night_end_time" value="{{ old('night_end_time') }}"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- Acciones --}}
    <div class="flex items-center gap-3 flex-wrap">
      <button type="submit"
              class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-semibold rounded-xl px-6 py-2.5 hover:shadow-md transition-all duration-200 text-sm">
        Crear cancha
      </button>
      <a href="{{ route('va.dashboard') }}"
         class="border border-slate-300 text-slate-600 font-semibold rounded-xl px-6 py-2.5 hover:bg-slate-50 transition-all duration-200 text-sm">
        Volver
      </a>
    </div>

  </form>
</div>

<script>
  const SPORT_CONFIG = {
    football:   { label: 'Jugadores por equipo', hint: 'Formato del partido', options: [{v:5,l:'5 (Fútbol 5)'},{v:7,l:'7 (Fútbol 7)'},{v:9,l:'9 (Fútbol 9)'},{v:11,l:'11 (Fútbol 11)'}] },
    padel:      { label: 'Formato', hint: 'Siempre 2 jugadores por equipo', options: [{v:2,l:'2 vs 2'}] },
    tennis:     { label: 'Modalidad', hint: '', options: [{v:1,l:'Singles (1 vs 1)'},{v:2,l:'Dobles (2 vs 2)'}] },
    basketball: { label: 'Jugadores por equipo', hint: '', options: [{v:3,l:'3 (3x3)'},{v:5,l:'5 (5x5)'}] },
    volleyball: { label: 'Formato', hint: 'Siempre 6 jugadores por equipo', options: [{v:6,l:'6 vs 6'}] },
  };

  function updateFormat(currentVal) {
    const sport  = document.getElementById('sport').value;
    const config = SPORT_CONFIG[sport] || SPORT_CONFIG.football;
    const sel    = document.getElementById('format-select');

    document.getElementById('format-label').textContent = config.label;
    document.getElementById('format-hint').textContent  = config.hint;

    sel.innerHTML = '';
    config.options.forEach(function(opt) {
      const o = document.createElement('option');
      o.value = opt.v;
      o.textContent = opt.l;
      if (currentVal && parseInt(currentVal) === opt.v) o.selected = true;
      sel.appendChild(o);
    });

    syncFormat();
  }

  function syncFormat() {
    document.getElementById('format-hidden').value = document.getElementById('format-select').value;
  }

  function toggleNight(suffix) {
    const checkbox = document.getElementById('toggle_night_' + suffix);
    const panel    = document.getElementById('night_panel_' + suffix);
    panel.classList.toggle('hidden', !checkbox.checked);
  }

  updateFormat('{{ old("format") }}');
</script>
@endsection
