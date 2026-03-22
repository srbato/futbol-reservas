@extends('layouts.app')

@section('title', 'Iniciar partido · ' . $field->name)

@push('styles')
<style>
  .fu-wrap {
    max-width: 600px;
    margin: 0 auto;
  }

  /* Hero de la cancha */
  .fu-hero {
    position: relative;
    height: 180px;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #111 0%, #333 100%);
  }

  .fu-hero img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: .55;
  }

  .fu-hero-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 20px 22px;
    background: linear-gradient(to top, rgba(0,0,0,.7) 0%, transparent 60%);
  }

  .fu-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.15);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 999px;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 8px;
    width: fit-content;
  }

  .fu-hero-title {
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    margin: 0;
    letter-spacing: -.02em;
    text-shadow: 0 1px 4px rgba(0,0,0,.4);
  }

  .fu-hero-sub {
    font-size: 13px;
    color: rgba(255,255,255,.7);
    margin: 2px 0 0 0;
  }

  /* Card principal */
  .fu-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
    overflow: hidden;
  }

  .fu-card-header {
    padding: 20px 24px 0;
    border-bottom: 1px solid #f4f4f4;
    padding-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .fu-step-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background: #111;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .fu-card-header-text h2 {
    font-size: 16px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.01em;
  }

  .fu-card-header-text p {
    font-size: 13px;
    color: #999;
    margin: 1px 0 0 0;
  }

  .fu-card-body {
    padding: 22px 24px;
    display: grid;
    gap: 18px;
  }

  /* Inputs con contador visual */
  .fu-input-group {
    display: grid;
    gap: 6px;
  }

  .fu-label {
    font-size: 13px;
    font-weight: 700;
    color: #333;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .fu-label-icon {
    font-size: 15px;
  }

  .fu-hint {
    font-size: 12px;
    color: #aaa;
    margin: 0;
    line-height: 1.5;
  }

  /* Selector de jugadores con +/- */
  .fu-counter {
    display: flex;
    align-items: center;
    gap: 0;
    width: fit-content;
    border: 1.5px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
  }

  .fu-counter button {
    width: 40px;
    height: 42px;
    border: none;
    background: #f8f8f8;
    color: #333;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
    flex-shrink: 0;
  }

  .fu-counter button:hover { background: #efefef; }
  .fu-counter button:active { background: #e8e8e8; }

  .fu-counter input {
    width: 60px;
    height: 42px;
    border: none;
    border-left: 1.5px solid #e0e0e0;
    border-right: 1.5px solid #e0e0e0;
    text-align: center;
    font-size: 16px;
    font-weight: 800;
    color: #111;
    outline: none;
    background: #fff;
    -moz-appearance: textfield;
  }

  .fu-counter input::-webkit-outer-spin-button,
  .fu-counter input::-webkit-inner-spin-button { -webkit-appearance: none; }

  /* Vista previa */
  .fu-preview {
    background: linear-gradient(135deg, #f9f9f9 0%, #f4f4f4 100%);
    border: 1.5px solid #e8e8e8;
    border-radius: 16px;
    padding: 18px 20px;
    display: none;
    animation: fadeSlide .25s ease;
  }

  @keyframes fadeSlide {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .fu-preview.visible { display: block; }

  .fu-preview-title {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: #aaa;
    margin: 0 0 12px 0;
  }

  .fu-preview-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 14px;
  }

  .fu-preview-stat {
    background: #fff;
    border: 1px solid #eaeaea;
    border-radius: 12px;
    padding: 12px 14px;
  }

  .fu-preview-stat-label {
    font-size: 11px;
    color: #aaa;
    font-weight: 600;
    margin-bottom: 4px;
  }

  .fu-preview-stat-value {
    font-size: 17px;
    font-weight: 800;
    color: #111;
    letter-spacing: -.01em;
  }

  .fu-preview-total {
    background: #111;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .fu-preview-total-label {
    font-size: 13px;
    color: rgba(255,255,255,.7);
    font-weight: 600;
  }

  .fu-preview-total-value {
    font-size: 20px;
    font-weight: 800;
    color: #fff;
    letter-spacing: -.02em;
  }

  .fu-preview-footer {
    font-size: 12px;
    color: #aaa;
    margin: 10px 0 0 0;
    line-height: 1.5;
  }

  /* Separador de secciones */
  .fu-divider {
    height: 1px;
    background: #f4f4f4;
    margin: 0 -24px;
  }

  /* Footer del card */
  .fu-card-footer {
    padding: 18px 24px;
    border-top: 1px solid #f4f4f4;
  }

  .fu-submit {
    width: 100%;
    padding: 14px;
    font-size: 15px;
    font-weight: 700;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 14px;
    cursor: pointer;
    transition: background .15s, transform .1s;
    letter-spacing: -.01em;
  }

  .fu-submit:hover  { background: #222; }
  .fu-submit:active { transform: scale(.98); }

  /* Error */
  .fu-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    color: #dc2626;
  }

  @media (max-width: 600px) {
    .fu-card-body { padding: 18px 18px; }
    .fu-card-header { padding: 16px 18px 14px; }
    .fu-card-footer { padding: 14px 18px; }
    .fu-preview-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')

<div class="fu-wrap">

  {{-- Back --}}
  <div style="margin-bottom:14px;">
    <a href="{{ route('fields.show', $field) }}"
       style="display:inline-flex; align-items:center; gap:5px; font-size:13px; color:#888; text-decoration:none; font-weight:600; transition:color .15s;"
       onmouseover="this.style.color='#111'" onmouseout="this.style.color='#888'">
      ← Volver a {{ $field->name }}
    </a>
  </div>

  {{-- Hero --}}
  <div class="fu-hero">
    @if($field->cover_image_path)
      <img src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}" alt="{{ $field->name }}">
    @endif
    <div class="fu-hero-overlay">
      <div class="fu-hero-badge">
        ⚡ Falta Uno
      </div>
      <h1 class="fu-hero-title">{{ $field->name }}</h1>
      <p class="fu-hero-sub">
        {{ $field->venue->name }} ·
        {{ match($field->sport ?? '') { 'football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley', default=>ucfirst($field->sport ?? '') } }}
      </p>
    </div>
  </div>

  {{-- Errores --}}
  @if($errors->any())
    <div class="fu-error" style="margin-bottom:14px;">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  @if(session('error'))
    <div class="fu-error" style="margin-bottom:14px;">{{ session('error') }}</div>
  @endif

  {{-- Card del formulario --}}
  <form method="POST" action="{{ route('falta-uno.store', $field) }}">
    @csrf
    <div class="fu-card">

      {{-- Header --}}
      <div class="fu-card-header">
        <div class="fu-step-icon">📅</div>
        <div class="fu-card-header-text">
          <h2>Configurá tu partido</h2>
          <p>Indicá cuándo jugás y cuántos traés</p>
        </div>
      </div>

      <div class="fu-card-body">

        {{-- Fecha y hora --}}
        <div class="fu-input-group">
          <label class="fu-label" for="start_at">
            <span class="fu-label-icon">🗓</span> Fecha y hora del partido
          </label>
          <input type="datetime-local" id="start_at" name="start_at" class="form-control"
                 style="width:100%; font-size:14px;"
                 required
                 min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                 value="{{ old('start_at') }}">
          <p class="fu-hint">Debe coincidir con un horario disponible de la cancha.</p>
        </div>

        <div class="fu-divider"></div>

        {{-- Total jugadores --}}
        <div class="fu-input-group">
          <label class="fu-label" for="total_players">
            <span class="fu-label-icon">👥</span> Total de jugadores del partido
          </label>
          <div class="fu-counter">
            <button type="button" onclick="step('total_players', -1)">−</button>
            <input type="number" id="total_players" name="total_players"
                   min="2" max="100" required
                   value="{{ old('total_players', $field->format * 2 ?? 10) }}"
                   oninput="recalculate()">
            <button type="button" onclick="step('total_players', 1)">+</button>
          </div>
          <p class="fu-hint">Ej: 10 para un partido de 5 vs 5, 14 para 7 vs 7.</p>
        </div>

        {{-- Jugadores del iniciador --}}
        <div class="fu-input-group">
          <label class="fu-label" for="initiator_players">
            <span class="fu-label-icon">🙋</span> Jugadores que traés vos
          </label>
          <div class="fu-counter">
            <button type="button" onclick="step('initiator_players', -1)">−</button>
            <input type="number" id="initiator_players" name="initiator_players"
                   min="1" max="99" required
                   value="{{ old('initiator_players', 1) }}"
                   oninput="recalculate()">
            <button type="button" onclick="step('initiator_players', 1)">+</button>
          </div>
          <p class="fu-hint">Los demás se unen desde la plataforma y pagan en el complejo.</p>
        </div>

        <div class="fu-divider"></div>

        {{-- Filtro de género --}}
        <div class="fu-input-group">
          <label class="fu-label">
            <span class="fu-label-icon">👤</span> Género de los jugadores
          </label>
          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            @foreach(['mixed'=>'Mixto', 'male'=>'Solo masculino', 'female'=>'Solo femenino'] as $val => $gLabel)
              <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:13px; font-weight:600; color:#555;">
                <input type="radio" name="gender_filter" value="{{ $val }}"
                       {{ old('gender_filter', 'mixed') === $val ? 'checked' : '' }}>
                {{ $gLabel }}
              </label>
            @endforeach
          </div>
          <p class="fu-hint">Solo jugadores con el género seleccionado podrán unirse.</p>
        </div>

        <div class="fu-divider"></div>

        {{-- Filtro de categoría (rango) --}}
        @php $sportCategories = \App\Models\FaltaUnoSportProfile::getCategoriesForSport($field->sport); @endphp
        <div class="fu-input-group">
          <label class="fu-label">
            <span class="fu-label-icon">🏅</span> Rango de categorías aceptadas
          </label>
          <div style="display:flex; gap:10px; align-items:center;">
            <div style="flex:1;">
              <div style="font-size:11px; color:#aaa; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.05em;">Desde</div>
              <select id="category_min" name="category_min" class="form-control" style="width:100%; font-size:14px;" onchange="syncCategoryMax()">
                <option value="">Cualquiera</option>
                @foreach($sportCategories as $cat)
                  <option value="{{ $cat }}" {{ old('category_min') === $cat ? 'selected' : '' }}>
                    {{ ucfirst($cat) }}
                  </option>
                @endforeach
              </select>
            </div>
            <div style="font-size:20px; color:#ccc; padding-top:18px;">→</div>
            <div style="flex:1;">
              <div style="font-size:11px; color:#aaa; font-weight:600; margin-bottom:4px; text-transform:uppercase; letter-spacing:.05em;">Hasta</div>
              <select id="category_max" name="category_max" class="form-control" style="width:100%; font-size:14px;" onchange="syncCategoryMin()">
                <option value="">Cualquiera</option>
                @foreach($sportCategories as $cat)
                  <option value="{{ $cat }}" {{ old('category_max') === $cat ? 'selected' : '' }}>
                    {{ ucfirst($cat) }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <p class="fu-hint">Dejá ambos en "Cualquiera" para aceptar jugadores de todo nivel.</p>
        </div>

        <div class="fu-divider"></div>

        {{-- Preview --}}
        <div class="fu-preview" id="fuPreview">
          <p class="fu-preview-title">Resumen del partido</p>

          <div class="fu-preview-grid">
            <div class="fu-preview-stat">
              <div class="fu-preview-stat-label">Jugadores confirmados</div>
              <div class="fu-preview-stat-value" id="prevInitiator">—</div>
            </div>
            <div class="fu-preview-stat">
              <div class="fu-preview-stat-label">Faltan unirse</div>
              <div class="fu-preview-stat-value" id="prevNeeded" style="color:#f59e0b;">—</div>
            </div>
            <div class="fu-preview-stat">
              <div class="fu-preview-stat-label">Precio por persona</div>
              <div class="fu-preview-stat-value" id="prevPerPerson">—</div>
            </div>
            <div class="fu-preview-stat">
              <div class="fu-preview-stat-label">Total jugadores</div>
              <div class="fu-preview-stat-value" id="prevTotal2">—</div>
            </div>
          </div>

          <div class="fu-preview-total">
            <span class="fu-preview-total-label">Lo que pagás vos ahora</span>
            <span class="fu-preview-total-value" id="prevTotal">—</span>
          </div>

          <p class="fu-preview-footer">
            Los <strong id="prevNeedTxt">?</strong> jugadores restantes abonan su parte directamente en el complejo el día del partido.
          </p>
        </div>

      </div>

      {{-- Footer con botón --}}
      <div class="fu-card-footer">
        <button type="submit" class="fu-submit">
          Publicar partido y pagar →
        </button>
        <p style="text-align:center; font-size:12px; color:#bbb; margin:10px 0 0 0;">
          Serás redirigido a Mercado Pago para completar el pago.
        </p>
      </div>

    </div>
  </form>

</div>

<script>
  const FIELD_ID       = {{ $field->id }};
  const CURRENCY       = '{{ $field->price->currency ?? 'ARS' }}';
  const FALLBACK_PRICE = {{ (float) ($field->price->price_per_slot ?? 0) }};

  let currentSlotPrice = FALLBACK_PRICE;
  let fetchTimer       = null;

  function fmt(n) {
    const rounded = Math.round(n * 100) / 100;
    return CURRENCY + '\u00a0' + rounded.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
  }

  function step(id, delta) {
    const input = document.getElementById(id);
    const val   = parseInt(input.value) || 0;
    const min   = parseInt(input.min) || 1;
    const max   = parseInt(input.max) || 999;
    input.value = Math.min(max, Math.max(min, val + delta));
    recalculate();
  }

  let slotAvailable = true;

  // Consulta el precio real del slot y verifica disponibilidad
  function fetchSlotPrice(dateTimeStr) {
    clearTimeout(fetchTimer);

    // Limpiar error de disponibilidad previo
    const errEl = document.getElementById('slotAvailError');
    if (errEl) errEl.remove();

    fetchTimer = setTimeout(async () => {
      try {
        const dt   = new Date(dateTimeStr);
        const date = dt.toISOString().slice(0, 10);
        const time = dt.toTimeString().slice(0, 5);

        const res  = await fetch(`/fields/${FIELD_ID}/availability?date=${date}`);
        const data = await res.json();

        const slot = (data.slots || []).find(s => s.start_at === time);

        if (slot && slot.available === false) {
          slotAvailable = false;
          currentSlotPrice = slot.price ?? FALLBACK_PRICE;

          // Mostrar error inline
          let errDiv = document.getElementById('slotAvailError');
          if (!errDiv) {
            errDiv = document.createElement('div');
            errDiv.id = 'slotAvailError';
            errDiv.className = 'fu-error';
            errDiv.style.marginTop = '8px';
            document.getElementById('start_at').closest('.fu-input-group').appendChild(errDiv);
          }
          errDiv.textContent = 'Este horario no está disponible. Por favor elegí otro.';

          // Deshabilitar submit
          document.querySelector('.fu-submit').disabled = true;
          document.querySelector('.fu-submit').style.opacity = '0.5';
          document.querySelector('.fu-submit').style.cursor = 'not-allowed';
        } else {
          slotAvailable = true;
          currentSlotPrice = (slot && slot.price) ? slot.price : FALLBACK_PRICE;

          // Habilitar submit
          document.querySelector('.fu-submit').disabled = false;
          document.querySelector('.fu-submit').style.opacity = '1';
          document.querySelector('.fu-submit').style.cursor = 'pointer';
        }
      } catch {
        slotAvailable = true;
        currentSlotPrice = FALLBACK_PRICE;
      }
      recalculate();
    }, 400);
  }

  function recalculate() {
    const total     = parseInt(document.getElementById('total_players').value) || 0;
    const initiator = parseInt(document.getElementById('initiator_players').value) || 0;
    const preview   = document.getElementById('fuPreview');

    if (total < 2 || initiator < 1 || initiator >= total) {
      preview.classList.remove('visible');
      return;
    }

    const needed    = total - initiator;
    const perPerson = currentSlotPrice / total;
    const myTotal   = perPerson * initiator;

    document.getElementById('prevInitiator').textContent = initiator;
    document.getElementById('prevNeeded').textContent    = needed;
    document.getElementById('prevPerPerson').textContent = fmt(perPerson);
    document.getElementById('prevTotal2').textContent    = total;
    document.getElementById('prevTotal').textContent     = fmt(myTotal);
    document.getElementById('prevNeedTxt').textContent   = needed;

    preview.classList.add('visible');
  }

  document.getElementById('start_at').addEventListener('change', function () {
    if (this.value) fetchSlotPrice(this.value);
  });

  recalculate();

  // Sincroniza selects de categoría: min no puede superar max y viceversa
  const CATEGORY_ORDER = @json($sportCategories);

  function catIndex(val) {
    return val === '' ? -1 : CATEGORY_ORDER.indexOf(val);
  }

  function syncCategoryMax() {
    const minSel = document.getElementById('category_min');
    const maxSel = document.getElementById('category_max');
    const minIdx = catIndex(minSel.value);
    if (minIdx === -1) return;
    const maxIdx = catIndex(maxSel.value);
    if (maxIdx !== -1 && maxIdx < minIdx) {
      maxSel.value = minSel.value;
    }
  }

  function syncCategoryMin() {
    const minSel = document.getElementById('category_min');
    const maxSel = document.getElementById('category_max');
    const maxIdx = catIndex(maxSel.value);
    if (maxIdx === -1) return;
    const minIdx = catIndex(minSel.value);
    if (minIdx !== -1 && minIdx > maxIdx) {
      minSel.value = maxSel.value;
    }
  }
</script>

@endsection
