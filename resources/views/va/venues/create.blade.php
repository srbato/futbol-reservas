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

        <input type="hidden" id="lat" name="lat" value="{{ old('lat') }}">
        <input type="hidden" id="lng" name="lng" value="{{ old('lng') }}">

        <div>
          <button type="button" onclick="geocodeAddress()"
                  class="inline-flex items-center gap-2 border border-slate-300 text-slate-600 font-semibold rounded-xl px-5 py-2.5 hover:bg-slate-50 transition-all duration-200 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Buscar ubicación en el mapa
          </button>
          <p id="geocodeMsg" class="text-xs mt-2 text-slate-500"></p>
          <div id="mapPreview" class="hidden mt-3 rounded-xl overflow-hidden h-52 border border-slate-200"></div>
        </div>

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

  function geocodeAddress() {
    const address = document.getElementById('address').value.trim();
    const msg     = document.getElementById('geocodeMsg');
    if (!address) {
      msg.textContent  = 'Ingresá una dirección primero.';
      msg.className    = 'text-xs mt-2 text-red-500';
      return;
    }
    msg.className   = 'text-xs mt-2 text-slate-500';
    msg.textContent = 'Buscando...';
    fetch(`{{ route('va.geocode') }}?address=${encodeURIComponent(address)}`)
      .then(r => r.json())
      .then(data => {
        if (data.status === 'OK') {
          const loc = data.results[0].geometry.location;
          document.getElementById('lat').value = loc.lat;
          document.getElementById('lng').value = loc.lng;
          msg.className   = 'text-xs mt-2 text-green-600 font-medium';
          msg.textContent = 'Ubicación encontrada: ' + data.results[0].formatted_address;
          const div = document.getElementById('mapPreview');
          div.classList.remove('hidden');
          div.innerHTML = `<iframe width="100%" height="208" frameborder="0" style="border:0;"
            src="https://www.google.com/maps?q=${loc.lat},${loc.lng}&z=16&output=embed" allowfullscreen></iframe>`;
        } else {
          msg.className   = 'text-xs mt-2 text-red-500';
          msg.textContent = 'No se encontró la dirección. Intentá con más detalle (ciudad, país).';
        }
      })
      .catch(() => {
        msg.className   = 'text-xs mt-2 text-red-500';
        msg.textContent = 'Error al buscar la dirección.';
      });
  }
</script>
@endsection
