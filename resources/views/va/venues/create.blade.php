@extends('layouts.admin')

@section('title', 'Crear complejo')
@section('page_title', 'Crear complejo')
@section('page_subtitle', 'Cargá un nuevo complejo para administrarlo desde el panel')

@section('content')
  <div class="admin-card">
    <form method="POST" action="{{ route('va.venues.store') }}" enctype="multipart/form-data">
      @csrf

      <div style="display:grid; gap:14px; max-width:700px;">
        <div>
          <label>Nombre</label><br>
          <input name="name" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Descripción corta</label><br>
          <input name="description" maxlength="255"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Dirección</label><br>
          <input id="address" name="address"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Zona</label><br>
          <input name="zone"
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <input type="hidden" id="lat" name="lat">
        <input type="hidden" id="lng" name="lng">

        <div>
          <button type="button" onclick="geocodeAddress()"
                  style="padding:9px 18px; background:#111; color:#fff; border:none; border-radius:8px; font-size:14px; cursor:pointer;">
            📍 Buscar ubicación en el mapa
          </button>
          <p id="geocodeMsg" style="font-size:13px; margin-top:6px; color:#666;"></p>
          <div id="mapPreview" style="display:none; margin-top:10px; border-radius:10px; overflow:hidden; height:220px;"></div>
        </div>

        <div>
          <label>Imagen del complejo</label><br>
          <input type="file" name="cover_image" accept="image/*">
        </div>

        <div style="display:flex; gap:10px; align-items:center;">
          <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
            Crear complejo
          </button>
          <a href="{{ route('va.dashboard') }}">Volver</a>
        </div>
      </div>
    </form>
  </div>

<script>
  function geocodeAddress() {
    const address = document.getElementById('address').value.trim();
    if (!address) {
      document.getElementById('geocodeMsg').textContent = 'Ingresá una dirección primero.';
      return;
    }
    const msg = document.getElementById('geocodeMsg');
    msg.style.color = '#666';
    msg.textContent = 'Buscando...';
    fetch(`{{ route('va.geocode') }}?address=${encodeURIComponent(address)}`)
      .then(r => r.json())
      .then(data => {
        if (data.status === 'OK') {
          const loc = data.results[0].geometry.location;
          document.getElementById('lat').value = loc.lat;
          document.getElementById('lng').value = loc.lng;
          msg.style.color = '#16a34a';
          msg.textContent = '✓ Ubicación encontrada: ' + data.results[0].formatted_address;
          const div = document.getElementById('mapPreview');
          div.style.display = 'block';
          div.innerHTML = `<iframe width="100%" height="220" frameborder="0" style="border:0;"
            src="https://www.google.com/maps?q=${loc.lat},${loc.lng}&z=16&output=embed" allowfullscreen></iframe>`;
        } else {
          msg.style.color = '#dc2626';
          msg.textContent = 'No se encontró la dirección. Intentá con más detalle (ciudad, país).';
        }
      })
      .catch(() => {
        msg.style.color = '#dc2626';
        msg.textContent = 'Error al buscar la dirección.';
      });
  }
</script>
@endsection