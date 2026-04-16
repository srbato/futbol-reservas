@extends('layouts.admin')

@section('title', 'Crear complejo')
@section('page_title', 'Crear complejo')
@section('page_subtitle', 'Cargá un nuevo complejo para administrarlo desde el panel')

@section('content')
<div class="max-w-2xl space-y-5">

  <form method="POST" action="{{ route('va.venues.store') }}" enctype="multipart/form-data">
    @csrf

    {{-- Información básica --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Información básica
      </p>

      <div class="space-y-4">

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
            Nombre del complejo
          </label>
          <input name="name" value="{{ old('name') }}" required maxlength="120"
                 placeholder="Ej: Complejo Don Juan"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('name') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('name')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
            Descripción corta
          </label>
          <input name="description" value="{{ old('description') }}" maxlength="255"
                 placeholder="Una línea que aparece en la página del complejo"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('description') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('description')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
            Teléfono de contacto <span class="text-slate-400 normal-case font-normal">(opcional)</span>
          </label>
          <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="30"
                 placeholder="Ej: +54 11 1234-5678"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('phone') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('phone')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

      </div>
    </div>

    {{-- Ubicación --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Ubicación
      </p>

      <div class="space-y-4">

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
            Dirección
          </label>
          <input id="address" name="address" value="{{ old('address') }}" maxlength="200"
                 placeholder="Ej: Av. Corrientes 1234, CABA"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('address') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('address')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">
            Zona
          </label>
          <input name="zone" value="{{ old('zone') }}" maxlength="120"
                 placeholder="Ej: Palermo, GBA Norte..."
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('zone') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('zone')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <p class="text-xs text-slate-400">La ubicación en el mapa se detecta automáticamente al guardar.</p>

      </div>
    </div>

    {{-- Política de cancelación y modificación --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Política de cancelación y modificación
      </p>

      <div class="flex items-center gap-3 flex-wrap mb-4">
        <input type="number" id="cancellation_hours" name="cancellation_hours"
               value="{{ old('cancellation_hours') }}"
               min="1" max="720" placeholder="—"
               class="w-28 rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <label for="cancellation_hours" class="text-sm text-slate-500">
          horas mínimas de anticipación para cancelar
        </label>
      </div>
      <p class="text-xs text-slate-400 mb-4">
        Dejá vacío para permitir cancelaciones en cualquier momento. Cuando se supere el límite, el botón de cancelar desaparece para el usuario.
      </p>

      <div class="flex items-center gap-3 flex-wrap">
        <input type="number" id="modification_hours" name="modification_hours"
               value="{{ old('modification_hours') }}"
               min="1" max="720" placeholder="—"
               class="w-28 rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <label for="modification_hours" class="text-sm text-slate-500">
          horas mínimas de anticipación para modificar horario
        </label>
      </div>
      <p class="text-xs text-slate-400 mt-2">
        Dejá vacío para no permitir modificaciones. Cuando se configure, los usuarios podrán cambiar el horario de su reserva pagada con la anticipación indicada.
      </p>
    </div>

    {{-- Reservas recurrentes --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Reservas recurrentes
      </p>

      <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Modo de pago</label>
      <select name="recurring_payment_mode"
              class="w-full max-w-xs px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="upfront" {{ old('recurring_payment_mode', 'upfront') === 'upfront' ? 'selected' : '' }}>
          Pago único online (MercadoPago)
        </option>
        <option value="manual" {{ old('recurring_payment_mode') === 'manual' ? 'selected' : '' }}>
          Solo manual / efectivo
        </option>
        <option value="subscription" {{ old('recurring_payment_mode') === 'subscription' ? 'selected' : '' }}>
          Suscripción mensual (requiere MercadoPago conectado)
        </option>
      </select>
      @error('recurring_payment_mode')
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
      @enderror
      <p class="text-xs text-slate-400 mt-2">
        <strong>Pago único online:</strong> el jugador paga todos los turnos de una vez por MercadoPago.<br>
        <strong>Solo manual:</strong> el botón "Reservar recurrente" no aparece en la página pública. El admin crea las reservas recurrentes desde el panel.<br>
        <strong>Suscripción mensual:</strong> MercadoPago cobra automáticamente al jugador cada mes y genera los turnos. Requiere tener MercadoPago conectado.
      </p>
    </div>

    {{-- Pago en efectivo --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Pago en efectivo
      </p>

      <label class="flex items-start gap-3 cursor-pointer select-none">
        <input type="checkbox" name="accepts_cash_payment" value="1"
               {{ old('accepts_cash_payment') ? 'checked' : '' }}
               class="mt-0.5 rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
        <div>
          <span class="text-sm font-semibold text-slate-800">Acepta pago en efectivo en el complejo</span>
          <p class="text-xs text-slate-400 mt-1">
            Al activar esta opcion, los usuarios podran elegir "Pagar en el complejo" al reservar.
            La reserva quedara pendiente de pago presencial y no expirara automaticamente.
            Cuando el jugador llegue y pague, podras confirmar el pago desde tu panel.
          </p>
        </div>
      </label>
    </div>

    {{-- Servicios e instalaciones --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-1 pb-3 border-b border-slate-100">
        Servicios e instalaciones
      </p>
      <p class="text-xs text-slate-400 mb-4 mt-3">
        Seleccioná todo lo que ofrece el complejo. Aparece como chips en la página pública.
      </p>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-2" id="amenityGrid">
        @foreach($amenitiesList as $key => $amenity)
          @php $checked = in_array($key, old('amenities', [])); @endphp
          <label id="amenity-label-{{ $key }}"
                 class="flex items-center gap-2 px-3 py-2.5 rounded-xl border cursor-pointer select-none transition-all duration-150 text-sm
                        {{ $checked ? 'border-indigo-500 bg-indigo-50 text-indigo-700 font-semibold' : 'border-slate-200 text-slate-600 hover:border-slate-400 hover:bg-slate-50' }}">
            <input type="checkbox" name="amenities[]" value="{{ $key }}" {{ $checked ? 'checked' : '' }} class="hidden">
            <span class="flex-shrink-0 w-4 h-4 rounded border flex items-center justify-center text-xs transition-all duration-150
                         {{ $checked ? 'bg-indigo-500 border-indigo-500 text-white' : 'border-slate-300 opacity-0' }}">
              ✓
            </span>
            <span>{{ $amenity['emoji'] }} {{ $amenity['label'] }}</span>
          </label>
        @endforeach
      </div>
    </div>

    {{-- Imagen --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Imagen del complejo
      </p>

      <label class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 block" id="uploadZone">
        <input type="file" name="cover_image" accept="image/*" class="hidden" onchange="previewVenueImage(this)">
        <img id="imgPreview" class="hidden mx-auto h-36 rounded-xl object-cover shadow-sm mb-3" alt="Preview">
        <div id="imgPlaceholder" class="flex flex-col items-center gap-2 text-slate-400">
          <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span class="text-sm font-medium">Hacé clic para elegir una imagen</span>
        </div>
        <p id="fileName" class="text-xs text-slate-400 mt-2">JPG, PNG o WEBP · máx. 4 MB</p>
      </label>
      @error('cover_image')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
      @enderror
    </div>

    {{-- Acciones --}}
    <div class="flex items-center gap-3 flex-wrap">
      <button type="submit"
              class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-semibold rounded-xl px-6 py-2.5 hover:shadow-md transition-all duration-200 text-sm">
        Crear complejo
      </button>
      <a href="{{ route('va.dashboard') }}"
         class="border border-slate-300 text-slate-600 font-semibold rounded-xl px-6 py-2.5 hover:bg-slate-50 transition-all duration-200 text-sm">
        Volver
      </a>
    </div>

  </form>
</div>

<script>
  // Amenities toggle
  document.getElementById('amenityGrid').addEventListener('change', function(e) {
    if (e.target.type !== 'checkbox') return;
    const label    = e.target.closest('label');
    const checkBox = label.querySelector('span.flex-shrink-0');
    if (e.target.checked) {
      label.classList.remove('border-slate-200', 'text-slate-600', 'hover:border-slate-400', 'hover:bg-slate-50');
      label.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700', 'font-semibold');
      checkBox.classList.remove('border-slate-300', 'opacity-0');
      checkBox.classList.add('bg-indigo-500', 'border-indigo-500', 'text-white');
    } else {
      label.classList.add('border-slate-200', 'text-slate-600', 'hover:border-slate-400', 'hover:bg-slate-50');
      label.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700', 'font-semibold');
      checkBox.classList.add('border-slate-300', 'opacity-0');
      checkBox.classList.remove('bg-indigo-500', 'border-indigo-500', 'text-white');
    }
  });

  function previewVenueImage(input) {
    const file = input.files[0];
    if (!file) return;
    document.getElementById('fileName').textContent = file.name;
    const reader = new FileReader();
    reader.onload = function(e) {
      const preview     = document.getElementById('imgPreview');
      const placeholder = document.getElementById('imgPlaceholder');
      preview.src = e.target.result;
      preview.classList.remove('hidden');
      placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(file);
  }

</script>
@endsection
