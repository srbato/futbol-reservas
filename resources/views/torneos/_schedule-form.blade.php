<form method="POST" action="{{ route('torneos.schedule_request', [$tournament, $venueRequestId]) }}"
      x-data="{
        slots: [{ date: '', start_time: '', end_time: '' }],
        addSlot() { this.slots.push({ date: '', start_time: '', end_time: '' }); },
        removeSlot(i) { if (this.slots.length > 1) this.slots.splice(i, 1); }
      }">
  @csrf

  <div style="background:#111;border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:14px;">
    <div style="font-size:13px;font-weight:700;color:#e8e8e8;margin-bottom:10px;">
      Horarios para {{ $fieldName }} — {{ $venueName }}
    </div>

    <template x-for="(slot, index) in slots" :key="index">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;flex-wrap:wrap;">
        <div style="display:flex;flex-direction:column;gap:2px;">
          <label style="font-size:10px;font-weight:700;color:#666;text-transform:uppercase;">Fecha</label>
          <input type="date" :name="'slots['+index+'][date]'" x-model="slot.date" required
                 style="padding:6px 10px;border:1px solid rgba(255,255,255,.1);border-radius:8px;background:#0a0a0a;color:#e8e8e8;font-size:13px;font-family:inherit;outline:none;min-width:140px;">
        </div>
        <div style="display:flex;flex-direction:column;gap:2px;">
          <label style="font-size:10px;font-weight:700;color:#666;text-transform:uppercase;">Desde</label>
          <input type="time" :name="'slots['+index+'][start_time]'" x-model="slot.start_time"
                 placeholder="Todo el dia"
                 style="padding:6px 10px;border:1px solid rgba(255,255,255,.1);border-radius:8px;background:#0a0a0a;color:#e8e8e8;font-size:13px;font-family:inherit;outline:none;">
        </div>
        <div style="display:flex;flex-direction:column;gap:2px;">
          <label style="font-size:10px;font-weight:700;color:#666;text-transform:uppercase;">Hasta</label>
          <input type="time" :name="'slots['+index+'][end_time]'" x-model="slot.end_time"
                 placeholder="Todo el dia"
                 style="padding:6px 10px;border:1px solid rgba(255,255,255,.1);border-radius:8px;background:#0a0a0a;color:#e8e8e8;font-size:13px;font-family:inherit;outline:none;">
        </div>
        <button type="button" @click="removeSlot(index)" x-show="slots.length > 1"
                style="margin-top:14px;width:28px;height:28px;border-radius:8px;border:1px solid rgba(239,68,68,.25);background:rgba(239,68,68,.08);color:#ef4444;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;"
                title="Quitar">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>
    </template>

    <p style="margin:0;font-size:11px;color:#666;font-weight:500;">Si no pones horario, se toma como todo el dia.</p>
    <div style="display:flex;align-items:center;gap:10px;margin-top:8px;">
      <button type="button" @click="addSlot()"
              style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);border-radius:8px;font-size:12px;font-weight:600;color:#a0a0a0;cursor:pointer;font-family:inherit;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
        Agregar horario
      </button>
      <button type="submit"
              style="display:inline-flex;align-items:center;gap:5px;padding:6px 16px;background:#22c55e;color:#052e16;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
        Enviar solicitud de horarios
      </button>
    </div>
  </div>
</form>
