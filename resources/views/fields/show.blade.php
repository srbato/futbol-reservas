@extends('layouts.app')

@section('title', $field->name)

@section('content')
  <div class="page-card" style="padding:0; overflow:hidden; margin-bottom:22px;">
    @if($field->cover_image_path)
      <img
        src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
        alt="{{ $field->name }}"
        style="width:100%; height:360px; object-fit:cover; display:block;"
      >
    @endif

    <div style="padding:24px;">
      <p style="margin:0 0 12px 0;">
        <a href="{{ route('venues.show', $field->venue) }}" class="btn">← Volver al complejo</a>
      </p>

      <div style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap; align-items:flex-start;">
        <div style="max-width:760px;">
          <h1 style="margin:0 0 10px 0; font-size:40px; letter-spacing:-0.02em;">
            {{ $field->name }}
          </h1>

          <div class="muted" style="margin-bottom:14px;">
            Complejo: <strong style="color:#111;">{{ $field->venue->name }}</strong>
          </div>

          <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:14px;">
            <span class="badge">{{ ucfirst($field->sport ?? 'cancha') }}</span>
            <span class="badge">Formato: {{ $field->format ?? '?' }}</span>
            <span class="badge">Turno: {{ $field->slot_minutes }} min</span>
          </div>

          <p class="muted" style="margin:0; line-height:1.6;">
            Elegí una fecha, revisá la disponibilidad y reservá tu turno online.
          </p>
        </div>

        <div class="page-card" style="min-width:240px; padding:18px;">
          <div style="font-size:12px; color:#666; margin-bottom:6px;">Precio base por turno</div>
          <div style="font-size:34px; font-weight:800; line-height:1.1;">
            {{ $field->price->currency ?? 'ARS' }} {{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }}
          </div>
          <div class="muted" style="margin-top:8px; font-size:13px;">
            Los descuentos y bloqueos se reflejan según la fecha elegida.
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="page-card" style="margin-bottom:18px;">
    <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-end;">
      <div>
        <h2 class="section-title" style="font-size:24px; margin:0 0 10px 0;">Disponibilidad</h2>
        <div class="muted" style="font-size:14px;">
          Revisá horarios disponibles, descuentos activos y estados de cada turno.
        </div>
      </div>

      <div>
        <label style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Fecha</label>
        <input
          type="date"
          id="datePicker"
          value="{{ now()->toDateString() }}"
          min="{{ now()->toDateString() }}"
          style="padding:10px 12px; border:1px solid #ddd; border-radius:12px; background:#fff; min-width:190px;"
        >
      </div>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:16px;">
      <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background:#e8f7ee; color:#157347; font-weight:700; font-size:13px;">
        <span style="width:10px; height:10px; border-radius:999px; background:#157347; display:inline-block;"></span>
        Disponible
      </div>

      <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background:#fff4db; color:#9a6700; font-weight:700; font-size:13px;">
        <span style="width:10px; height:10px; border-radius:999px; background:#f0ad00; display:inline-block;"></span>
        Con descuento
      </div>

      <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background:#f3f3f3; color:#666; font-weight:700; font-size:13px;">
        <span style="width:10px; height:10px; border-radius:999px; background:#666; display:inline-block;"></span>
        No disponible
      </div>

      <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background:#f8d7da; color:#842029; font-weight:700; font-size:13px;">
        <span style="width:10px; height:10px; border-radius:999px; background:#842029; display:inline-block;"></span>
        Bloqueado
      </div>

      <div style="display:flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background:#e2e3e5; color:#41464b; font-weight:700; font-size:13px;">
        <span style="width:10px; height:10px; border-radius:999px; background:#41464b; display:inline-block;"></span>
        Pasado
      </div>
    </div>
  </div>

  <div id="reservationFeedback" style="display:none; margin-bottom:16px;"></div>

    <div id="slots">
      <div class="page-card">
        <p class="muted" style="margin:0;">Cargando disponibilidad...</p>
      </div>
    </div>


  <div id="payModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100;">
    <div style="background:#fff; padding:20px; max-width:440px; margin:10% auto; border-radius:18px; box-shadow:0 12px 40px rgba(0,0,0,.18);">
      <h3 style="margin-top:0;">Reserva creada</h3>
      <p id="payModalText" class="muted"></p>

      <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:16px; flex-wrap:wrap;">
        <button onclick="closePayModal()" class="btn">Seguir mirando</button>
        <a id="payLink" href="#" class="btn btn-primary">
          Ir a pagar
        </a>
      </div>
    </div>
  </div>

  <script>
    function showReservationFeedback(message, type = 'error') {
      const el = document.getElementById('reservationFeedback');
      if (!el) return;

      const styles = type === 'success'
        ? {
            bg: '#e8f7ee',
            color: '#157347',
            border: '#cfe9d7'
          }
        : {
            bg: '#f8d7da',
            color: '#842029',
            border: '#f1b9c0'
          };

      el.style.display = 'block';
      el.innerHTML = `
        <div class="page-card" style="background:${styles.bg}; color:${styles.color}; border:1px solid ${styles.border};">
          <p style="margin:0; font-weight:700;">${message}</p>
        </div>
      `;
    }

    function clearReservationFeedback() {
      const el = document.getElementById('reservationFeedback');
      if (!el) return;

      el.style.display = 'none';
      el.innerHTML = '';
    }

    function formatMoney(value, currency) {
      const number = Number(value || 0);

      return `${currency} ${number.toLocaleString('es-AR')}`;
    }

    function getStatusConfig(slot) {
      if (slot.status === 'BLOCKED') {
        return {
          label: 'Bloqueado',
          bg: '#f8d7da',
          color: '#842029',
          border: '1px solid #f1aeb5'
        };
      }

      if (slot.status === 'PAST') {
        return {
          label: 'Pasado',
          bg: '#e2e3e5',
          color: '#41464b',
          border: '1px solid #d3d6d8'
        };
      }

      if (slot.status === 'UNAVAILABLE') {
        return {
          label: 'No disponible',
          bg: '#f3f3f3',
          color: '#666',
          border: '1px solid #e2e2e2'
        };
      }

    if (slot.has_discount) {
        return {
          label: 'Disponible con descuento',
          bg: '#fff4db',
          color: '#9a6700',
          border: '1px solid #f5d48a'
        };
      }

      if (slot.is_night_price) {
        return {
          label: '🌙 Precio nocturno',
          bg: '#ede9fe',
          color: '#5b21b6',
          border: '1px solid #c4b5fd'
        };
      }

      return {
        label: 'Disponible',
        bg: '#e8f7ee',
        color: '#157347',
        border: '1px solid #b7e1c1'
      };
    }

    function renderEmptySlots(message) {
      document.getElementById('slots').innerHTML = `
        <div class="page-card">
          <p class="muted" style="margin:0;">${message}</p>
        </div>
      `;
    }

    function renderSlots(data) {
      const el = document.getElementById('slots');

      if (!data.slots || data.slots.length === 0) {
        renderEmptySlots('No hay horarios disponibles para esa fecha.');
        return;
      }

      const cards = data.slots.map(slot => {
        const disabled = slot.status !== 'AVAILABLE';
        const status = getStatusConfig(slot);

        const reserveButton = disabled
          ? `
            <button
              type="button"
              disabled
              class="btn"
              style="width:100%; opacity:.65; cursor:not-allowed;"
            >
              ${slot.status === 'PAST' ? 'Turno pasado' : 'No disponible'}
            </button>
          `
          : `
            <button
              type="button"
              class="btn btn-primary"
              style="width:100%;"
              onclick="reserve('${slot.start_at}')"
            >
              Reservar
            </button>
          `;

        const reasonHtml = slot.reason
          ? `
            <div style="margin-top:10px; font-size:12px; color:#842029; line-height:1.4;">
              Motivo: ${slot.reason}
            </div>
          `
          : '';

        const discountHtml = slot.has_discount
          ? `
            <div style="margin-top:10px; font-size:12px; color:#9a6700; font-weight:700; line-height:1.4;">
              🔥 ${slot.discount_label ?? 'Descuento aplicado'}
            </div>
          `
          : '';

          const priceHtml = slot.has_discount
            ? `
              <div style="display:flex; align-items:baseline; gap:8px; flex-wrap:wrap;">
                <span style="text-decoration:line-through; color:#999; font-size:14px;">
                  ${formatMoney(slot.original_price, slot.currency)}
                </span>
                <span style="font-size:22px; font-weight:800; color:#157347;">
                  ${formatMoney(slot.price, slot.currency)}
                </span>
              </div>
            `
            : slot.is_night_price
            ? `
              <div style="font-size:22px; font-weight:800; color:#5b21b6;">
                ${formatMoney(slot.price, slot.currency)}
              </div>
            `
            : `
              <div style="font-size:22px; font-weight:800; color:#111;">
                ${formatMoney(slot.price, slot.currency)}
              </div>
            `;

        return `
          <div class="page-card" style="padding:18px; height:100%; display:flex; flex-direction:column; justify-content:space-between;">
            <div>
              <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; margin-bottom:14px;">
                <div>
                  <div style="font-size:18px; font-weight:800; color:#111;">
                    ${slot.start_at} - ${slot.end_at}
                  </div>
                  <div class="muted" style="font-size:13px; margin-top:4px;">
                    Turno de {{ $field->slot_minutes }} minutos
                  </div>
                </div>

                <div style="padding:7px 10px; border-radius:999px; background:${status.bg}; color:${status.color}; border:${status.border}; font-weight:700; font-size:12px; text-align:center;">
                  ${status.label}
                </div>
              </div>

              ${priceHtml}
              ${discountHtml}
              ${reasonHtml}
            </div>

            <div style="margin-top:16px;">
              ${reserveButton}
            </div>
          </div>
        `;
      }).join('');

      el.innerHTML = `
        <div style="margin-bottom:12px; display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center;">
          <h3 style="margin:0; font-size:20px;">Turnos del día</h3>
          <div class="muted" style="font-size:14px;">
            ${data.slots.length} horario${data.slots.length === 1 ? '' : 's'} encontrado${data.slots.length === 1 ? '' : 's'}
          </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:14px;">
          ${cards}
        </div>
      `;
    }

    function loadSlots() {
      const date = document.getElementById('datePicker').value;
      const slotsEl = document.getElementById('slots');

      clearReservationFeedback();

      slotsEl.innerHTML = `
        <div class="page-card">
          <p class="muted" style="margin:0;">Cargando disponibilidad...</p>
        </div>
      `;

      fetch(`{{ route('fields.availability', $field, false) }}?date=${encodeURIComponent(date)}`)
        .then(async (r) => {
          const text = await r.text();

          if (!r.ok) {
            throw new Error(`HTTP ${r.status}\n\n${text}`);
          }

          try {
            return JSON.parse(text);
          } catch (e) {
            throw new Error(`La respuesta no es JSON válido:\n\n${text}`);
          }
        })
        .then(data => {
          renderSlots(data);
        })
        .catch((err) => {
          document.getElementById('slots').innerHTML = `
            <div class="page-card">
              <p style="margin:0; color:#842029;">Error cargando disponibilidad.</p>
              <pre style="white-space:pre-wrap; margin-top:10px; color:#842029;">${err.message}</pre>
            </div>
          `;
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
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Accept": "application/json"
        },
        body: JSON.stringify({
          field_id: {{ $field->id }},
          start_at: date + " " + time
        })
      })
      .then(async (r) => {
        if (r.status === 401) {
          window.location.href = "{{ route('login') }}";
          return;
        }

        const text = await r.text();
        let parsed = null;

        try {
          parsed = JSON.parse(text);
        } catch (e) {
          parsed = null;
        }

        if (!r.ok) {
          const backendMessage =
            parsed?.message ||
            parsed?.error ||
            (parsed?.errors ? Object.values(parsed.errors).flat().join(' ') : null);

          const error = new Error(backendMessage || `HTTP ${r.status} ${r.statusText}`);
          error.status = r.status;
          throw error;
        }

        return parsed;
      })
      .then((data) => {
        if (!data) return;

        const res = data.reservation ?? data;

        document.getElementById('payLink').href = `/reservations/${res.id}/checkout`;
        document.getElementById('payModalText').innerText =
          `Horario: ${time}. Tenés 10 minutos para completar el pago.`;

        document.getElementById('payModal').style.display = 'block';
      })
      .catch((err) => {
        let message = 'No se pudo crear la reserva. Intentá nuevamente.';

        if (err.status === 409) {
          message = 'Ese horario acaba de ser reservado por otra persona. Ya actualizamos la disponibilidad.';
        } else if (err.message) {
          message = err.message;
        }

        showReservationFeedback(message, 'error');
        loadSlots();
        console.error(err);
      });
    }

    function closePayModal() {
      document.getElementById('payModal').style.display = 'none';
      location.reload();
    }
  </script>
@endsection