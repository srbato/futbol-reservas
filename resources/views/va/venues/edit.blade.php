@extends('layouts.admin')

@section('title', 'Editar complejo')
@section('page_title', 'Editar complejo')
@section('page_subtitle', 'Actualizá los datos, servicios e imagen de tu complejo')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_venue_edit',
  'helpTitle' => 'Editar complejo',
  'helpText'  => 'Acá editás los datos generales de tu complejo: nombre, descripción, zona, dirección, coordenadas para el mapa, imagen de portada y política de cancelación. Esta información es la que ven los usuarios al buscar un complejo.',
])

<div class="max-w-2xl space-y-5">

  <form method="POST" action="{{ route('va.venues.update', $venue) }}" enctype="multipart/form-data">
    @csrf

    {{-- Información básica --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Información básica
      </p>

      <div class="space-y-4">

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="name">
            Nombre del complejo
          </label>
          <input id="name" name="name" value="{{ old('name', $venue->name) }}" required maxlength="120"
                 placeholder="Ej: Complejo Don Juan"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('name') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('name')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="description">
            Descripción corta
          </label>
          <input id="description" name="description" value="{{ old('description', $venue->description) }}" maxlength="255"
                 placeholder="Una línea que aparece en la página del complejo"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('description') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('description')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="phone">
            Teléfono de contacto <span class="text-slate-400 normal-case font-normal">(opcional)</span>
          </label>
          <input type="tel" id="phone" name="phone" value="{{ old('phone', $venue->phone) }}" maxlength="30"
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
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="address">
            Dirección
          </label>
          <input id="address" name="address" value="{{ old('address', $venue->address) }}" maxlength="200"
                 placeholder="Ej: Av. Corrientes 1234, CABA"
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('address') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('address')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5" for="zone">
            Zona
          </label>
          <input id="zone" name="zone" value="{{ old('zone', $venue->zone) }}" maxlength="120"
                 placeholder="Ej: Palermo, GBA Norte..."
                 class="w-full rounded-xl border px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $errors->has('zone') ? 'border-red-400 focus:ring-red-400' : 'border-slate-300' }}">
          @error('zone')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <p class="text-xs text-slate-400">La ubicación en el mapa se detecta automáticamente al guardar.</p>

      </div>
    </div>

    {{-- Política de cancelación --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Política de cancelación y modificación
      </p>

      <div class="flex items-center gap-3 flex-wrap mb-4">
        <input type="number" id="cancellation_hours" name="cancellation_hours"
               value="{{ old('cancellation_hours', $venue->cancellation_hours) }}"
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
               value="{{ old('modification_hours', $venue->modification_hours) }}"
               min="1" max="720" placeholder="—"
               class="w-28 rounded-xl border border-slate-300 px-4 py-2.5 text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <label for="modification_hours" class="text-sm text-slate-500">
          horas mínimas de anticipación para modificar horario
        </label>
      </div>
      <p class="text-xs text-slate-400 mt-2">
        Dejá vacío para no permitir modificaciones. Cuando se configure, los usuarios podrán cambiar el horario de su reserva pagada con la anticipación indicada.
      </p>
      @if(old('modification_hours', $venue->modification_hours) === null)
        <div class="mt-3 flex items-start gap-2 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
          <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <p class="text-xs text-amber-700">
            La modificación de reservas está desactivada para este complejo. Los usuarios no podrán cambiar el horario de sus reservas. Para habilitarla, configurá las horas mínimas de anticipación en el campo de arriba.
          </p>
        </div>
      @endif
    </div>

    {{-- Reservas recurrentes --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <p class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-4 pb-3 border-b border-slate-100">
        Reservas recurrentes
      </p>

      <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Modo de pago</label>
      <select name="recurring_payment_mode"
              class="w-full max-w-xs px-3 py-2.5 rounded-xl border border-slate-300 bg-white text-sm text-slate-900 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="upfront" {{ old('recurring_payment_mode', $venue->recurring_payment_mode ?? 'upfront') === 'upfront' ? 'selected' : '' }}>
          Pago único online (MercadoPago)
        </option>
        <option value="manual" {{ old('recurring_payment_mode', $venue->recurring_payment_mode ?? 'upfront') === 'manual' ? 'selected' : '' }}>
          Solo manual / efectivo
        </option>
        <option value="subscription" {{ old('recurring_payment_mode', $venue->recurring_payment_mode ?? 'upfront') === 'subscription' ? 'selected' : '' }}>
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
               {{ old('accepts_cash_payment', $venue->accepts_cash_payment) ? 'checked' : '' }}
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
          @php $checked = in_array($key, old('amenities', $venue->amenities ?? [])); @endphp
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
        <input type="file" name="cover_image" accept="image/*" class="hidden" onchange="previewImage(this)">

        @if($venue->cover_image_path)
          <img id="imgPreview"
               src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
               alt="Imagen actual"
               class="mx-auto h-36 rounded-xl object-cover shadow-sm mb-3">
          <div id="imgPlaceholder" class="hidden flex flex-col items-center gap-2 text-slate-400">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm font-medium">Sin imagen</span>
          </div>
        @else
          <img id="imgPreview" class="hidden mx-auto h-36 rounded-xl object-cover shadow-sm mb-3" alt="Preview">
          <div id="imgPlaceholder" class="flex flex-col items-center gap-2 text-slate-400">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="text-sm font-medium">Sin imagen</span>
          </div>
        @endif

        <p id="fileName" class="text-xs text-slate-400 mt-2">Hacé clic para elegir · JPG, PNG o WEBP · máx. 4 MB</p>
      </label>
    </div>

    {{-- Galería (hasta 5 imágenes) --}}
    @php $currentGallery = is_array($venue->gallery_paths) ? $venue->gallery_paths : []; @endphp
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
      <div class="flex items-start justify-between mb-4 pb-3 border-b border-slate-100 gap-3">
        <div>
          <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">
            Galería del complejo
          </p>
          <p class="text-xs text-slate-400 mt-1">Hasta 5 fotos que se muestran en la página pública. La imagen de portada (arriba) es la principal.</p>
        </div>
        <span class="text-xs font-semibold text-slate-500 bg-slate-100 rounded-full px-3 py-1 whitespace-nowrap" id="galleryCounter">
          {{ count($currentGallery) }} / 5
        </span>
      </div>

      {{-- Fotos actuales --}}
      @if(!empty($currentGallery))
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4">
          @foreach($currentGallery as $path)
            <label class="relative group cursor-pointer aspect-square block rounded-xl overflow-hidden border-2 border-transparent has-[:checked]:border-indigo-500 transition-all">
              <input type="checkbox" name="gallery_keep[]" value="{{ $path }}" checked class="absolute inset-0 opacity-0 peer">
              <img src="{{ \Illuminate\Support\Facades\Storage::url($path) }}"
                   alt="Foto del complejo"
                   class="w-full h-full object-cover peer-checked:opacity-100 opacity-40 transition-opacity">
              {{-- Check overlay --}}
              <span class="absolute top-2 right-2 w-6 h-6 bg-white rounded-full shadow flex items-center justify-center text-indigo-500 text-xs peer-checked:bg-indigo-500 peer-checked:text-white transition-all font-bold">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </span>
              {{-- Hint overlay when unchecked --}}
              <span class="absolute inset-0 flex items-center justify-center bg-red-500/60 text-white text-xs font-semibold opacity-0 peer-not-checked:opacity-100 transition-opacity pointer-events-none">
                Se eliminará
              </span>
            </label>
          @endforeach
        </div>
        <p class="text-xs text-slate-400 mb-4">Destildá una foto para eliminarla al guardar.</p>
      @endif

      {{-- Subir nuevas --}}
      <label class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200 block">
        <input type="file" name="gallery[]" accept="image/*" multiple class="hidden" id="galleryInput" onchange="previewGallery(this)">
        <div id="galleryPreview" class="hidden grid grid-cols-5 gap-2 mb-3"></div>
        <div class="flex flex-col items-center gap-2 text-slate-400">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/>
          </svg>
          <span class="text-sm font-medium text-slate-600">Agregar fotos</span>
          <span class="text-xs text-slate-400">JPG, PNG o WEBP · máx. 4 MB cada una · hasta 5 en total</span>
        </div>
      </label>

      @error('gallery') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
      @error('gallery.*') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
    </div>

    {{-- Acciones --}}
    <div class="flex items-center gap-3 flex-wrap">
      <button type="submit"
              class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-semibold rounded-xl px-6 py-2.5 hover:shadow-md transition-all duration-200 text-sm">
        Guardar cambios
      </button>
      <a href="{{ route('va.dashboard') }}"
         class="border border-slate-300 text-slate-600 font-semibold rounded-xl px-6 py-2.5 hover:bg-slate-50 transition-all duration-200 text-sm">
        Volver
      </a>
    </div>

  </form>

  {{-- Mercado Pago — card separada del form principal --}}
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
    <div class="flex items-center gap-3 mb-1">
      <span class="text-xl">💳</span>
      <p class="text-base font-bold text-slate-800">Mercado Pago</p>
    </div>
    <p class="text-sm text-slate-500 mb-4">
      Conectá tu cuenta de Mercado Pago para que los cobros lleguen directamente a tu billetera.
    </p>

    @if($venue->mp_access_token)
      <div class="flex items-center gap-4 flex-wrap">
        <span class="inline-flex items-center gap-2 px-3 py-2 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-semibold">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Cuenta conectada
        </span>
        <form method="POST" action="{{ route('va.mp_oauth.disconnect', $venue) }}"
              onsubmit="return confirm('¿Desconectar la cuenta de Mercado Pago de este complejo?')">
          @csrf
          <button type="submit"
                  class="border border-slate-300 text-slate-600 font-semibold rounded-xl px-5 py-2 hover:bg-slate-50 transition-all duration-200 text-sm">
            Desconectar
          </button>
        </form>
      </div>
    @else
      <div class="flex items-center gap-4 flex-wrap">
        <span class="inline-flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 text-sm">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          Sin cuenta conectada — los pagos irán a la cuenta general de TuCancha
        </span>
        <a href="{{ route('va.mp_oauth.redirect', $venue) }}"
           class="inline-flex items-center gap-2 bg-[#009ee3] hover:bg-[#0088cc] text-white font-semibold rounded-xl px-5 py-2.5 transition-all duration-200 text-sm">
          Conectar Mercado Pago
        </a>
      </div>
    @endif
  </div>

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

  // Image preview
  function previewImage(input) {
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

  // Gallery preview + counter
  function previewGallery(input) {
    const preview = document.getElementById('galleryPreview');
    const counter = document.getElementById('galleryCounter');
    const files = Array.from(input.files || []);

    // Count how many existing kept + new
    const kept = document.querySelectorAll('input[name="gallery_keep[]"]:checked').length;
    const total = kept + files.length;

    // Enforce 5 max
    if (total > 5) {
      alert('Solo podés subir hasta 5 fotos en total. Tenés ' + kept + ' guardadas y estás intentando agregar ' + files.length + '.');
      input.value = '';
      preview.classList.add('hidden');
      preview.innerHTML = '';
      return;
    }

    if (counter) counter.textContent = total + ' / 5';

    if (!files.length) {
      preview.classList.add('hidden');
      preview.innerHTML = '';
      return;
    }

    preview.innerHTML = '';
    files.forEach(file => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const div = document.createElement('div');
        div.className = 'aspect-square rounded-lg overflow-hidden border border-slate-200';
        div.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
        preview.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
    preview.classList.remove('hidden');
  }

  // Recalcula el counter cuando el usuario destilda/tilda fotos actuales
  document.querySelectorAll('input[name="gallery_keep[]"]').forEach(cb => {
    cb.addEventListener('change', () => {
      const kept = document.querySelectorAll('input[name="gallery_keep[]"]:checked').length;
      const newFiles = (document.getElementById('galleryInput')?.files?.length) || 0;
      const counter = document.getElementById('galleryCounter');
      if (counter) counter.textContent = (kept + newFiles) + ' / 5';
    });
  });

</script>
@endsection
