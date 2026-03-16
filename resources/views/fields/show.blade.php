@extends('layouts.app')

@section('title', $field->name)

@push('styles')
<style>
  .field-header-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
  }

  .field-hero {
    position: relative;
    height: 300px;
    overflow: hidden;
  }

  .field-thumb {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .field-thumb-placeholder {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #2a2a2a, #444);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 56px;
  }

  .field-hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,.0) 0%, rgba(0,0,0,.75) 100%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 18px 22px;
    gap: 6px;
  }

  .field-header-back {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: rgba(255,255,255,.75);
    text-decoration: none;
    font-weight: 600;
    transition: color .15s;
    align-self: flex-start;
  }
  .field-header-back:hover { color: #fff; }

  .field-header-title {
    font-size: 26px;
    font-weight: 800;
    margin: 0;
    letter-spacing: -.02em;
    line-height: 1.2;
    color: #fff;
    text-shadow: 0 1px 4px rgba(0,0,0,.3);
  }

  .field-header-body {
    padding: 16px 22px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .field-header-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }

  .fh-badge {
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
  }

  .fh-badge.sport  { background: #dbeafe; color: #1d4ed8; }
  .fh-badge.format { background: #dcfce7; color: #166534; }
  .fh-badge.time   { background: #fef3c7; color: #92400e; }

  .field-price-box {
    display: inline-flex;
    flex-direction: column;
    padding: 12px 16px;
    background: #111;
    border-radius: 14px;
    color: #fff;
    align-self: flex-start;
    margin-top: 4px;
  }

  .field-price-box-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(255,255,255,.5);
    margin-bottom: 2px;
  }

  .field-price-box-value {
    font-size: 22px;
    font-weight: 800;
    line-height: 1;
  }

  .field-price-box-sub {
    font-size: 11px;
    color: rgba(255,255,255,.4);
    margin-top: 2px;
  }

  /* Availability */
  .availability-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    padding: 20px 22px;
    margin-bottom: 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
  }

  .date-picker-input {
    padding: 9px 14px;
    border: 1.5px solid #e0e0e0;
    border-radius: 12px;
    background: #fafafa;
    font-size: 14px;
    font-weight: 600;
    color: #111;
    min-width: 170px;
    cursor: pointer;
    transition: border-color .15s;
  }
  .date-picker-input:focus { outline: none; border-color: #111; background: #fff; }

  .legend-bar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding-top: 14px;
    border-top: 1px solid #f0f0f0;
    margin-top: 14px;
  }

  .legend-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
  }

  .legend-dot {
    width: 7px;
    height: 7px;
    border-radius: 999px;
    flex-shrink: 0;
  }

  /* Slot cards */
  .slot-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 14px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    border-left: 1px solid #ececec;
    box-shadow: 0 1px 6px rgba(0,0,0,.04);
    transition: box-shadow .15s;
  }
  .slot-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); }
  .slot-card.past        { opacity: .65; }
  .slot-card.unavailable { opacity: .7; }

  .slot-time {
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -.01em;
    color: #111;
  }

  .slot-duration {
    font-size: 12px;
    color: #aaa;
    margin-top: 1px;
  }

  .slot-status-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
  }

  .slot-price {
    font-size: 18px;
    font-weight: 800;
    color: #111;
  }

  .slot-price.green  { color: #16a34a; }
  .slot-price.purple { color: #7c3aed; }

  @media (max-width: 600px) {
    .field-hero { height: 160px; }
    .field-header-title { font-size: 20px; }
  }
</style>
@endpush

@section('content')

{{-- Header card --}}
<div class="field-header-card">
  <div class="field-hero">
    @if($field->cover_image_path)
      <img class="field-thumb"
           src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
           alt="{{ $field->name }}">
    @else
      <div class="field-thumb-placeholder">⚽</div>
    @endif

    <div class="field-hero-overlay">
      <a href="{{ route('venues.show', $field->venue) }}" class="field-header-back">
        ← {{ $field->venue->name }}
      </a>
      <h1 class="field-header-title">{{ $field->name }}</h1>
    </div>
  </div>

  <div class="field-header-body">
    <div class="field-header-badges">
      <span class="fh-badge sport">{{ match($field->sport ?? '') { 'football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley', default=>ucfirst($field->sport ?? 'Cancha') } }}</span>
      <span class="fh-badge format">{{ $field->format ?? '?' }}v{{ $field->format ?? '?' }}</span>
      <span class="fh-badge time">{{ $field->slot_minutes }} min</span>
    </div>

    <div class="field-price-box">
      <span class="field-price-box-label">Precio base</span>
      <span class="field-price-box-value">{{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }}</span>
      <span class="field-price-box-sub">{{ $field->price->currency ?? 'ARS' }} por turno</span>
    </div>
  </div>
</div>

{{-- Availability --}}
<div class="availability-card">
  <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-end; margin-bottom:4px;">
    <div>
      <h2 style="font-size:18px; font-weight:800; margin:0 0 3px 0;">Disponibilidad</h2>
      <div style="font-size:13px; color:#aaa;">Elegí una fecha para ver los turnos</div>
    </div>
    <div>
      <label style="display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#aaa; margin-bottom:5px;">Fecha</label>
      <input type="date" id="datePicker" class="date-picker-input"
             value="{{ now()->toDateString() }}"
             min="{{ now()->toDateString() }}">
    </div>
  </div>

  <div class="legend-bar">
    <span class="legend-chip" style="background:#dcfce7; color:#166534;"><span class="legend-dot" style="background:#22c55e;"></span>Disponible</span>
    <span class="legend-chip" style="background:#fef3c7; color:#92400e;"><span class="legend-dot" style="background:#f59e0b;"></span>Con descuento</span>
    <span class="legend-chip" style="background:#ede9fe; color:#5b21b6;"><span class="legend-dot" style="background:#8b5cf6;"></span>Nocturno</span>
    <span class="legend-chip" style="background:#fee2e2; color:#991b1b;"><span class="legend-dot" style="background:#ef4444;"></span>Bloqueado</span>
    <span class="legend-chip" style="background:#f3f4f6; color:#4b5563;"><span class="legend-dot" style="background:#9ca3af;"></span>No disponible</span>
  </div>
</div>

<div id="reservationFeedback" style="display:none; margin-bottom:16px;"></div>

<div id="slots">
  <div class="page-card">
    <p class="muted" style="margin:0;">Cargando disponibilidad...</p>
  </div>
</div>

{{-- Pay modal --}}
<div id="payModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:100;">
  <div style="background:#fff; padding:28px; max-width:400px; margin:12% auto; border-radius:20px; box-shadow:0 16px 48px rgba(0,0,0,.2);">
    <div style="font-size:32px; text-align:center; margin-bottom:10px;">✅</div>
    <h3 style="margin:0 0 6px 0; text-align:center;">¡Turno reservado!</h3>
    <p id="payModalText" class="muted" style="text-align:center; font-size:14px;"></p>
    <div style="display:flex; gap:10px; justify-content:center; margin-top:18px; flex-wrap:wrap;">
      <button onclick="closePayModal()" class="btn">Seguir mirando</button>
      <a id="payLink" href="#" class="btn btn-primary">Ir a pagar →</a>
    </div>
  </div>
</div>

<script>
  function showReservationFeedback(message, type = 'error') {
    const el = document.getElementById('reservationFeedback');
    if (!el) return;
    const s = type === 'success' ? {bg:'#e8f7ee',color:'#157347',border:'#cfe9d7'} : {bg:'#f8d7da',color:'#842029',border:'#f1b9c0'};
    el.style.display = 'block';
    el.innerHTML = `<div class="page-card" style="background:${s.bg}; color:${s.color}; border:1px solid ${s.border};"><p style="margin:0; font-weight:700;">${message}</p></div>`;
  }

  function clearReservationFeedback() {
    const el = document.getElementById('reservationFeedback');
    if (!el) return;
    el.style.display = 'none';
    el.innerHTML = '';
  }

  function formatMoney(value, currency) {
    return `${currency} ${Number(value || 0).toLocaleString('es-AR')}`;
  }

  function getStatusConfig(slot) {
    if (slot.status === 'BLOCKED')     return { label: 'Bloqueado',     bg: '#fee2e2', color: '#991b1b', border: '1px solid #fca5a5', cardClass: 'blocked' };
    if (slot.status === 'PAST')        return { label: 'Pasado',        bg: '#f3f4f6', color: '#4b5563', border: '1px solid #d1d5db', cardClass: 'past' };
    if (slot.status === 'UNAVAILABLE') return { label: 'No disponible', bg: '#f3f4f6', color: '#4b5563', border: '1px solid #d1d5db', cardClass: 'unavailable' };
    if (slot.has_discount)             return { label: 'Con descuento', bg: '#fef3c7', color: '#92400e', border: '1px solid #fde68a', cardClass: 'discount' };
    if (slot.is_night_price)           return { label: '🌙 Nocturno',   bg: '#ede9fe', color: '#5b21b6', border: '1px solid #c4b5fd', cardClass: 'night' };
    return                                    { label: 'Disponible',    bg: '#dcfce7', color: '#166534', border: '1px solid #86efac', cardClass: 'available' };
  }

  function renderEmptySlots(message) {
    document.getElementById('slots').innerHTML = `<div class="page-card"><p class="muted" style="margin:0; text-align:center; padding:20px 0;">${message}</p></div>`;
  }

  function renderSlots(data) {
    const el = document.getElementById('slots');
    if (!data.slots || data.slots.length === 0) { renderEmptySlots('No hay horarios disponibles para esa fecha.'); return; }

    const cards = data.slots.map(slot => {
      const disabled = slot.status !== 'AVAILABLE';
      const status   = getStatusConfig(slot);

      const priceHtml = slot.has_discount
        ? `<div style="display:flex; align-items:baseline; gap:6px;"><span style="text-decoration:line-through; color:#bbb; font-size:13px;">${formatMoney(slot.original_price, slot.currency)}</span><span class="slot-price green">${formatMoney(slot.price, slot.currency)}</span></div>`
        : slot.is_night_price
        ? `<span class="slot-price purple">${formatMoney(slot.price, slot.currency)}</span>`
        : `<span class="slot-price">${formatMoney(slot.price, slot.currency)}</span>`;

      const discountHtml = slot.has_discount ? `<div style="font-size:12px; color:#92400e; font-weight:700;">🔥 ${slot.discount_label ?? 'Descuento aplicado'}</div>` : '';
      const reasonHtml   = slot.reason       ? `<div style="font-size:12px; color:#991b1b; line-height:1.4;">Motivo: ${slot.reason}</div>` : '';

      const reserveButton = disabled
        ? `<button type="button" disabled class="btn" style="width:100%; opacity:.5; cursor:not-allowed;">${slot.status === 'PAST' ? 'Turno pasado' : 'No disponible'}</button>`
        : `<div style="display:flex; flex-direction:column; gap:7px;">
             <button type="button" class="btn btn-primary" style="width:100%;" onclick="reserve('${slot.start_at}')">Reservar</button>
             <button type="button" class="btn" style="width:100%; font-size:13px;" onclick="openRecurringModal('${slot.start_at}')">🔁 Reservar recurrente</button>
           </div>`;

      return `
        <div class="slot-card ${status.cardClass}">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
            <div>
              <div class="slot-time">${slot.start_at} – ${slot.end_at}</div>
              <div class="slot-duration">{{ $field->slot_minutes }} min</div>
            </div>
            <span class="slot-status-badge" style="background:${status.bg}; color:${status.color}; border:${status.border};">${status.label}</span>
          </div>
          ${priceHtml}
          ${discountHtml}
          ${reasonHtml}
          ${reserveButton}
        </div>`;
    }).join('');

    el.innerHTML = `
      <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
        <h3 style="margin:0; font-size:17px; font-weight:800;">Turnos del día</h3>
        <span class="muted" style="font-size:13px;">${data.slots.length} horario${data.slots.length === 1 ? '' : 's'}</span>
      </div>
      <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:12px;">
        ${cards}
      </div>`;
  }

  function loadSlots() {
    const date = document.getElementById('datePicker').value;
    clearReservationFeedback();
    document.getElementById('slots').innerHTML = `<div class="page-card"><p class="muted" style="margin:0;">Cargando disponibilidad...</p></div>`;

    fetch(`{{ route('fields.availability', $field, false) }}?date=${encodeURIComponent(date)}`)
      .then(async (r) => {
        const text = await r.text();
        if (!r.ok) throw new Error(`HTTP ${r.status}\n\n${text}`);
        try { return JSON.parse(text); } catch (e) { throw new Error(`La respuesta no es JSON válido:\n\n${text}`); }
      })
      .then(data => renderSlots(data))
      .catch((err) => {
        document.getElementById('slots').innerHTML = `<div class="page-card"><p style="margin:0; color:#842029;">Error cargando disponibilidad.</p><pre style="white-space:pre-wrap; margin-top:10px; color:#842029;">${err.message}</pre></div>`;
        console.error(err);
      });
  }

  loadSlots();
  document.getElementById('datePicker').addEventListener('change', loadSlots);

  function reserve(time) {
    const date = document.getElementById('datePicker').value;
    clearReservationFeedback();
    fetch("/reservations", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
      body: JSON.stringify({ field_id: {{ $field->id }}, start_at: date + " " + time })
    })
    .then(async (r) => {
      if (r.status === 401) { window.location.href = "{{ route('login') }}"; return; }
      const text = await r.text();
      let parsed = null;
      try { parsed = JSON.parse(text); } catch (e) { parsed = null; }
      if (!r.ok) {
        const msg = parsed?.message || parsed?.error || (parsed?.errors ? Object.values(parsed.errors).flat().join(' ') : null);
        const error = new Error(msg || `HTTP ${r.status} ${r.statusText}`);
        error.status = r.status;
        throw error;
      }
      return parsed;
    })
    .then((data) => {
      if (!data) return;
      const res = data.reservation ?? data;
      document.getElementById('payLink').href = `/reservations/${res.id}/checkout`;
      document.getElementById('payModalText').innerText = `Horario: ${time}. Tenés 10 minutos para completar el pago.`;
      document.getElementById('payModal').style.display = 'block';
    })
    .catch((err) => {
      let message = 'No se pudo crear la reserva. Intentá nuevamente.';
      if (err.status === 409) message = 'Ese horario acaba de ser reservado por otra persona. Ya actualizamos la disponibilidad.';
      else if (err.message) message = err.message;
      showReservationFeedback(message, 'error');
      loadSlots();
      console.error(err);
    });
  }

  function closePayModal() {
    document.getElementById('payModal').style.display = 'none';
    location.reload();
  }

  let recurringTime = null;

  function openRecurringModal(time) {
    const date = document.getElementById('datePicker').value;
    recurringTime = time;
    const label = document.getElementById('recurringDateLabel');
    if (label) label.textContent = date + ' a las ' + time;
    document.getElementById('recurringModal').style.display = 'block';
  }

  function closeRecurringModal() {
    document.getElementById('recurringModal').style.display = 'none';
    recurringTime = null;
  }

  function closeResultsModal() {
    document.getElementById('recurringResultsModal').style.display = 'none';
    location.reload();
  }

  const recurringTiers = {!! $tiersJson ?? '[]' !!};

  function updateDiscountPreview() {
    const occurrences = parseInt(document.getElementById('recurOccurrences')?.value ?? '0', 10);
    const preview = document.getElementById('discountPreview');
    if (!preview) return;
    const applicable = recurringTiers.filter(t => t.min_occurrences <= occurrences).sort((a, b) => b.min_occurrences - a.min_occurrences)[0];
    if (applicable) { preview.style.display = 'block'; preview.textContent = `🔥 Se aplicará un descuento del ${applicable.discount_percentage}% al total`; }
    else { preview.style.display = 'none'; }
  }

  function submitRecurring() {
    if (!recurringTime) return;
    const date = document.getElementById('datePicker').value;
    const frequency = document.querySelector('input[name="recurFrequency"]:checked')?.value ?? 'weekly';
    const occurrences = parseInt(document.getElementById('recurOccurrences').value, 10);
    const btn = document.getElementById('recurSubmitBtn');
    btn.disabled = true; btn.textContent = 'Creando reservas...';
    fetch('/reservations/recurring', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
      body: JSON.stringify({ field_id: {{ $field->id }}, start_at: date + ' ' + recurringTime, frequency, occurrences }),
    })
    .then(async (r) => {
      if (r.status === 401) { window.location.href = '{{ route('login') }}'; return null; }
      const text = await r.text();
      try { return JSON.parse(text); } catch (e) { throw new Error('Respuesta inesperada del servidor.'); }
    })
    .then((data) => {
      if (!data) return;
      closeRecurringModal();
      if (data.batch && data.batch.checkout_url) { window.location.href = data.batch.checkout_url; }
      else { showRecurringResults(data); }
    })
    .catch((err) => { showReservationFeedback(err.message || 'No se pudieron crear las reservas.', 'error'); closeRecurringModal(); })
    .finally(() => { btn.disabled = false; btn.textContent = 'Crear reservas'; });
  }

  function showRecurringResults(data) {
    const { results, summary } = data;
    const rows = results.map(r => r.status === 'created'
      ? `<div style="display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #eee;"><span style="font-size:18px;">✅</span><div><div style="font-weight:700;">${r.date} — ${r.time}</div><div style="font-size:13px; color:#157347;">Reservada correctamente</div></div></div>`
      : `<div style="display:flex; align-items:center; gap:10px; padding:10px 0; border-bottom:1px solid #eee;"><span style="font-size:18px;">❌</span><div><div style="font-weight:700;">${r.date} — ${r.time}</div><div style="font-size:13px; color:#842029;">${r.reason ?? 'No disponible'}</div></div></div>`
    ).join('');
    const summaryHtml = `<div style="display:flex; gap:10px; margin-bottom:14px; flex-wrap:wrap;"><div style="padding:7px 14px; border-radius:999px; background:#dcfce7; color:#166534; font-weight:700; font-size:13px;">✓ ${summary.created} reservadas</div>${summary.failed > 0 ? `<div style="padding:7px 14px; border-radius:999px; background:#fee2e2; color:#991b1b; font-weight:700; font-size:13px;">✗ ${summary.failed} fallidas</div>` : ''}</div>`;
    const noteHtml = summary.created > 0 ? `<div style="margin-top:12px; padding:12px 14px; border-radius:12px; background:#fef3c7; border:1px solid #fde68a; color:#92400e; font-size:13px;">Tenés <strong>30 minutos</strong> para pagar cada reserva desde <strong>Mis Reservas</strong>.</div>` : '';
    document.getElementById('recurringResultsBody').innerHTML = summaryHtml + rows + noteHtml;
    document.getElementById('recurringResultsModal').style.display = 'block';
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.js"></script>
<script>
  const echoClient = new Echo({
    broadcaster:  'reverb',
    key:          '{{ config('broadcasting.connections.reverb.key') }}',
    wsHost:       '{{ env('REVERB_CLIENT_HOST', config('broadcasting.connections.reverb.options.host')) }}',
    wsPort:       {{ env('REVERB_CLIENT_PORT', 443) }},
    wssPort:      {{ env('REVERB_CLIENT_PORT', 443) }},
    forceTLS:     true,
    enabledTransports: ['ws'],
  });

  echoClient.channel('field.{{ $field->id }}')
    .listen('.availability.changed', (e) => {
      const currentDate = document.getElementById('datePicker').value;
      if (e.date === currentDate) {
        loadSlots();
      }
    });
</script>

{{-- Recurring modal --}}
<div id="recurringModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:100;">
  <div style="background:#fff; padding:26px; max-width:440px; margin:8% auto; border-radius:20px; box-shadow:0 16px 48px rgba(0,0,0,.2);">
    <h3 style="margin:0 0 4px 0;">🔁 Reserva recurrente</h3>
    <p class="muted" style="margin:0 0 20px 0; font-size:14px;">Turno: <strong id="recurringDateLabel"></strong></p>

    <div style="margin-bottom:16px;">
      <label style="display:block; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#888; margin-bottom:10px;">Frecuencia</label>
      <div style="display:flex; gap:10px;">
        <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:14px;"><input type="radio" name="recurFrequency" value="weekly" checked> Semanal</label>
        <label style="display:flex; align-items:center; gap:6px; cursor:pointer; font-size:14px;"><input type="radio" name="recurFrequency" value="biweekly"> Quincenal</label>
      </div>
    </div>

    <div style="margin-bottom:18px;">
      <label for="recurOccurrences" style="display:block; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#888; margin-bottom:8px;">Cantidad de turnos</label>
      <select id="recurOccurrences" onchange="updateDiscountPreview()" style="padding:9px 14px; border:1.5px solid #e0e0e0; border-radius:12px; background:#fff; width:100%; font-size:14px; font-weight:600;">
        <option value="2">2 turnos</option>
        <option value="3">3 turnos</option>
        <option value="4" selected>4 turnos (~1 mes)</option>
        <option value="6">6 turnos (~1.5 meses)</option>
        <option value="8">8 turnos (~2 meses)</option>
        <option value="12">12 turnos (~3 meses)</option>
      </select>
    </div>

    @php
      $tiersJson = $recurringDiscounts->map(fn($t) => [
        'min_occurrences' => $t->min_occurrences,
        'discount_percentage' => (float) $t->discount_percentage,
      ])->values()->toJson();
    @endphp

    <div id="discountPreview" style="display:none; margin-bottom:18px; padding:12px 14px; border-radius:12px; background:#dcfce7; border:1px solid #86efac; color:#166534; font-weight:700; font-size:14px;"></div>

    @if($recurringDiscounts->isNotEmpty())
      <div style="margin-bottom:18px; padding:12px 14px; background:#f9f9f9; border-radius:12px;">
        <div style="font-size:11px; font-weight:700; color:#aaa; margin-bottom:8px; text-transform:uppercase; letter-spacing:.06em;">Descuentos disponibles</div>
        @foreach($recurringDiscounts as $t)
          <div style="font-size:13px; color:#444; margin-bottom:3px;">{{ $t->min_occurrences }}+ turnos → <strong style="color:#166534;">{{ $t->discount_percentage }}% off</strong></div>
        @endforeach
      </div>
    @endif

    <div style="display:flex; gap:10px; justify-content:flex-end;">
      <button onclick="closeRecurringModal()" class="btn">Cancelar</button>
      <button id="recurSubmitBtn" onclick="submitRecurring()" class="btn btn-primary">Crear reservas</button>
    </div>
  </div>
</div>

{{-- Results modal --}}
<div id="recurringResultsModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:100; overflow-y:auto;">
  <div style="background:#fff; padding:26px; max-width:480px; margin:6% auto; border-radius:20px; box-shadow:0 16px 48px rgba(0,0,0,.2);">
    <h3 style="margin:0 0 16px 0;">Resultado de reservas</h3>
    <div id="recurringResultsBody"></div>
    <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:18px; flex-wrap:wrap;">
      <a href="{{ route('my_reservations') }}" class="btn btn-primary">Ir a Mis Reservas</a>
      <button onclick="closeResultsModal()" class="btn">Cerrar</button>
    </div>
  </div>
</div>

@endsection
