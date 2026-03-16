@extends('layouts.admin')

@section('title', 'Crear cancha')
@section('page_title', 'Crear cancha')
@section('page_subtitle', 'Complejo: ' . $venue->name)

@section('content')
  <div class="admin-card">
    <form method="POST" action="{{ route('va.fields.store', $venue) }}">
      @csrf

      <div style="display:grid; gap:14px; max-width:700px;">

        <div>
          <label>Nombre</label><br>
          <input name="name" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label>Deporte</label><br>
            <select name="sport" id="sport" onchange="updateFormat()"
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px; background:#fff;">
              <option value="football">⚽ Fútbol</option>
              <option value="padel">🏓 Pádel</option>
              <option value="tennis">🎾 Tenis</option>
              <option value="basketball">🏀 Básquet</option>
              <option value="volleyball">🏐 Vóley</option>
            </select>
          </div>

          <div id="format-wrap">
            <label id="format-label">Formato</label><br>
            <select id="format-select" onchange="syncFormat()"
                    style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px; background:#fff;">
            </select>
            <input type="hidden" name="format" id="format-hidden">
            <p id="format-hint" style="margin:5px 0 0; font-size:12px; color:#999;"></p>
          </div>
        </div>

        <div>
          <label>Turno (minutos)</label><br>
          <input type="number" name="slot_minutes" value="60" min="30" max="180" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label>Precio por turno</label><br>
            <input type="number" name="price_per_slot" value="12000" step="0.01" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>
          <div>
            <label>Moneda</label><br>
            <input name="currency" value="ARS" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>
        </div>

        {{-- Precio nocturno --}}
        <div style="border:1px solid #e0e0e0; border-radius:12px; padding:16px; background:#fafafa;">
          <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              <div style="font-weight:700;">🌙 Precio nocturno</div>
              <div style="font-size:13px; color:#666; margin-top:2px;">Aplicá un precio distinto en horario nocturno</div>
            </div>
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
              <input type="checkbox" id="toggle_night_create" onchange="toggleNight('create')"
                     style="width:18px; height:18px; cursor:pointer;">
              <span style="font-size:13px; font-weight:600;">Activar</span>
            </label>
          </div>
          <div id="night_panel_create" style="display:none; margin-top:14px;">
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
              <div>
                <label style="font-size:13px;">Precio nocturno</label><br>
                <input type="number" name="night_price_per_slot" step="0.01" min="0"
                       placeholder="Ej: 15000"
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
              </div>
              <div>
                <label style="font-size:13px;">Desde</label><br>
                <input type="time" name="night_start_time"
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
              </div>
              <div>
                <label style="font-size:13px;">Hasta</label><br>
                <input type="time" name="night_end_time"
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
              </div>
            </div>
          </div>
        </div>

        <div style="display:flex; gap:10px; align-items:center;">
          <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
            Crear cancha
          </button>
          <a href="{{ route('va.dashboard') }}">Volver</a>
        </div>
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
      const panel = document.getElementById('night_panel_' + suffix);
      panel.style.display = checkbox.checked ? 'block' : 'none';
    }

    // Init
    updateFormat(null);
  </script>
@endsection
