@extends('layouts.admin')

@section('title', 'Editar cancha')
@section('page_title', 'Editar cancha')
@section('page_subtitle', $field->venue->name . ' · ' . $field->name)

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_field_edit',
  'helpTitle' => 'Editar cancha',
  'helpText'  => 'Acá editás la información de la cancha: nombre, deporte, precio por turno, precio nocturno y duración del turno. Los cambios se aplican de inmediato para nuevas reservas. Las reservas ya existentes no se ven afectadas.',
])

@php $hasNight = !is_null($field->price->night_price_per_slot ?? null); @endphp

<div class="max-w-2xl space-y-5">

  <form method="POST" action="{{ route('va.fields.update', $field) }}" enctype="multipart/form-data">
    @csrf

    {{-- Foto de la cancha --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Foto de la cancha
      </p>

      <label class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 block" id="uploadZone">
        <input type="file" name="cover_image" accept="image/*" class="hidden" onchange="previewFieldImage(this)">

        @if($field->cover_image_path)
          <img id="imgPreview"
               src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
               alt="Imagen actual"
               class="mx-auto h-36 rounded-xl object-cover shadow-sm mb-3">
        @else
          <img id="imgPreview" class="hidden mx-auto h-36 rounded-xl object-cover shadow-sm mb-3" alt="Preview">
          <div id="imgPlaceholder" class="flex flex-col items-center gap-2 text-slate-400">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm font-medium">Sin foto</span>
          </div>
        @endif

        <p id="fileName" class="text-xs text-slate-400 mt-2">Hacé clic para elegir · JPG, PNG o WEBP · máx. 4 MB</p>
      </label>
    </div>

    {{-- Datos básicos --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Datos básicos
      </p>

      <div class="space-y-4">

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="name">
            Nombre de la cancha
          </label>
          <input id="name" name="name" value="{{ old('name', $field->name) }}" required maxlength="100"
                 placeholder="Ej: Cancha 1 · Césped"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('name') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('name')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="sport">
              Deporte
            </label>
            <select id="sport" name="sport" onchange="updateFormat()"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 bg-white shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              @foreach(['football'=>'Fútbol','padel'=>'Pádel','tennis'=>'Tenis','basketball'=>'Básquet','volleyball'=>'Vóley'] as $val=>$lbl)
                <option value="{{ $val }}" {{ old('sport', $field->sport) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
            @error('sport')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label id="format-label" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
              Formato
            </label>
            <select id="format-select" onchange="syncFormat()"
                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 bg-white shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </select>
            <input type="hidden" name="format" id="format-hidden" value="{{ old('format', $field->format) }}">
            <p id="format-hint" class="text-xs text-slate-400 mt-1"></p>
            @error('format')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="slot_minutes">
            Duración del turno
          </label>
          <div class="flex items-center gap-3">
            @php $smCurrent = (int) old('slot_minutes', $field->slot_minutes ?: 60); @endphp
            <select id="slot_minutes" name="slot_minutes" required
                    class="w-44 rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('slot_minutes') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
              @foreach([30, 45, 60, 90, 120] as $opt)
                <option value="{{ $opt }}" {{ $smCurrent === $opt ? 'selected' : '' }}>
                  {{ $opt }} minutos
                </option>
              @endforeach
              @if(!in_array($smCurrent, [30, 45, 60, 90, 120]))
                <option value="{{ $smCurrent }}" selected>{{ $smCurrent }} minutos</option>
              @endif
            </select>
            <span class="text-xs text-slate-400">por turno</span>
          </div>
          <p class="text-xs text-slate-500 mt-1.5">
            Esto determina cuánto dura cada slot reservable. Los usuarios pueden reservar uno o varios turnos seguidos.
          </p>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="price_per_slot">
              Precio por turno
            </label>
            <input id="price_per_slot" type="number" name="price_per_slot" step="0.01" min="0" required
                   value="{{ old('price_per_slot', $field->price->price_per_slot ?? 0) }}" placeholder="Ej: 12000"
                   class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('price_per_slot') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
            @error('price_per_slot')
              <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="currency">
              Moneda
            </label>
            <input id="currency" name="currency" required
                   value="{{ old('currency', $field->price->currency ?? 'ARS') }}"
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
              <div class="text-xs text-slate-500 mt-0.5">Precio diferencial en horario nocturno</div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
              <input type="checkbox" id="toggle_night_edit" class="sr-only peer"
                     onchange="toggleNight('edit')" {{ $hasNight ? 'checked' : '' }}>
              <div class="w-10 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:bg-indigo-600 transition-colors duration-200 after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-[18px] after:w-[18px] after:transition-all after:shadow-sm peer-checked:after:translate-x-4"></div>
            </label>
          </div>

          <div id="night_panel_edit" class="{{ $hasNight ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                  Precio nocturno
                </label>
                <input type="number" name="night_price_per_slot" step="0.01" min="0" placeholder="Ej: 15000"
                       value="{{ old('night_price_per_slot', $field->price->night_price_per_slot ?? '') }}"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                  Desde
                </label>
                <input type="time" name="night_start_time"
                       value="{{ $field->price->night_start_time ? \Carbon\Carbon::parse($field->price->night_start_time)->format('H:i') : '' }}"
                       class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
                  Hasta
                </label>
                <input type="time" name="night_end_time"
                       value="{{ $field->price->night_end_time ? \Carbon\Carbon::parse($field->price->night_end_time)->format('H:i') : '' }}"
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
        Guardar cambios
      </button>
      <a href="{{ route('va.schedule.edit', $field) }}"
         class="border border-slate-300 text-slate-600 font-semibold rounded-xl px-6 py-2.5 hover:bg-slate-50 transition-all duration-200 text-sm">
        Editar horarios
      </a>
      <a href="{{ route('va.dashboard') }}"
         class="border border-slate-300 text-slate-600 font-semibold rounded-xl px-6 py-2.5 hover:bg-slate-50 transition-all duration-200 text-sm">
        Volver
      </a>
    </div>

  </form>

  {{-- Falta Uno — form separado --}}
  @php $fuSetting = $field->faltaUnoSetting; @endphp
  <form method="POST" action="{{ route('va.fields.falta_uno_settings', $field) }}">
    @csrf
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Falta Uno
      </p>

      <div class="flex items-center justify-between gap-4 mb-4">
        <div>
          <div class="text-sm font-bold text-slate-700">Habilitar Falta Uno en esta cancha</div>
          <div class="text-xs text-slate-500 mt-0.5">Los usuarios podrán iniciar partidos abiertos y otros se unen para completar el equipo.</div>
        </div>
        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
          <input type="checkbox" id="toggle_falta_uno" name="enabled" value="1" class="sr-only peer"
                 {{ $fuSetting?->enabled ? 'checked' : '' }}
                 onchange="toggleFaltaUnoPanel()">
          <div class="w-10 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:bg-indigo-600 transition-colors duration-200 after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-[18px] after:w-[18px] after:transition-all after:shadow-sm peer-checked:after:translate-x-4"></div>
        </label>
      </div>

      <div id="falta_uno_panel" class="{{ $fuSetting?->enabled ? '' : 'hidden' }} border-t border-slate-100 pt-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
              Límite para cancelar con reembolso
            </label>
            <div class="flex items-center gap-2">
              <input type="number" name="refund_deadline_minutes" min="0" max="10080"
                     value="{{ old('refund_deadline_minutes', $fuSetting?->refund_deadline_minutes ?? 60) }}"
                     class="w-24 rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              <span class="text-xs text-slate-500">minutos antes del partido</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">El iniciador puede cancelar y recibir reembolso hasta X minutos antes del partido.</p>
          </div>
          <div>
            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
              Límite para que se llene el partido
            </label>
            <div class="flex items-center gap-2">
              <input type="number" name="fill_deadline_minutes" min="0" max="10080"
                     value="{{ old('fill_deadline_minutes', $fuSetting?->fill_deadline_minutes ?? 120) }}"
                     class="w-24 rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
              <span class="text-xs text-slate-500">minutos antes del partido</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Si el partido no se llena X minutos antes, se cancela automáticamente y el slot vuelve a estar disponible.</p>
          </div>
        </div>
      </div>

      <div class="pt-2">
        <button type="submit"
                class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-semibold rounded-xl px-6 py-2.5 hover:shadow-md transition-all duration-200 text-sm">
          Guardar configuración Falta Uno
        </button>
      </div>
    </div>
  </form>

</div>

<script>
  function toggleFaltaUnoPanel() {
    const cb    = document.getElementById('toggle_falta_uno');
    const panel = document.getElementById('falta_uno_panel');
    panel.classList.toggle('hidden', !cb.checked);
  }

  const SPORT_CONFIG = {
    football:   { label: 'Jugadores por equipo', hint: 'Formato del partido', options: [{v:5,l:'5 (Fútbol 5)'},{v:7,l:'7 (Fútbol 7)'},{v:9,l:'9 (Fútbol 9)'},{v:11,l:'11 (Fútbol 11)'}] },
    padel:      { label: 'Formato', hint: 'Siempre 2 jugadores por equipo', options: [{v:2,l:'2 vs 2'}] },
    tennis:     { label: 'Modalidad', hint: '', options: [{v:1,l:'Singles (1 vs 1)'},{v:2,l:'Dobles (2 vs 2)'}] },
    basketball: { label: 'Jugadores por equipo', hint: '', options: [{v:3,l:'3 (3x3)'},{v:5,l:'5 (5x5)'}] },
    volleyball: { label: 'Formato', hint: 'Siempre 6 jugadores por equipo', options: [{v:6,l:'6 vs 6'}] },
  };

  function updateFormat() {
    const sport   = document.getElementById('sport').value;
    const config  = SPORT_CONFIG[sport] || SPORT_CONFIG.football;
    const sel     = document.getElementById('format-select');
    const current = parseInt(document.getElementById('format-hidden').value) || 0;

    document.getElementById('format-label').textContent = config.label;
    document.getElementById('format-hint').textContent  = config.hint;

    sel.innerHTML = '';
    config.options.forEach(function(opt) {
      const o = document.createElement('option');
      o.value = opt.v;
      o.textContent = opt.l;
      if (opt.v === current) o.selected = true;
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

  function previewFieldImage(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('fileName').textContent = file.name;
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview     = document.getElementById('imgPreview');
      const placeholder = document.getElementById('imgPlaceholder');
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      if (placeholder) placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(file);
  }

  updateFormat();
</script>
@endsection
