@extends('layouts.admin')

@section('title', 'Configuracion de Torneos')
@section('page_title', 'Configuracion de Torneos')

@push('styles')
<style>
  .ts-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    margin-bottom: 20px;
  }
  .ts-card-header {
    padding: 16px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .ts-card-header h3 { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
  .ts-card-header p { font-size: 12px; color: #94a3b8; margin: 4px 0 0; }
  .ts-badge-on { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #d1fae5; color: #047857; }
  .ts-badge-off { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; background: #f1f5f9; color: #64748b; }
  .ts-body { padding: 24px; }

  /* Switch toggle */
  .ts-switch {
    width: 48px;
    height: 26px;
    border-radius: 13px;
    background: #cbd5e1;
    cursor: pointer;
    flex-shrink: 0;
    position: relative;
    transition: background .2s;
    display: inline-block;
  }
  .ts-switch.on { background: #10b981; }
  .ts-switch-knob {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 20px;
    height: 20px;
    background: #fff;
    border-radius: 50%;
    transition: transform .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.2);
    pointer-events: none;
  }
  .ts-switch.on .ts-switch-knob { transform: translateX(22px); }

  .ts-toggle-row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
  }
  .ts-toggle-label { font-size: 14px; font-weight: 600; color: #1e293b; }
  .ts-toggle-hint { font-size: 12px; color: #94a3b8; margin: 2px 0 0; }

  /* Mode selector */
  .ts-mode-title { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 10px; }
  .ts-mode-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 24px; }
  .ts-mode-option {
    border: 2px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    border-radius: 14px;
    padding: 16px;
    transition: all .15s;
  }
  .ts-mode-option.selected {
    border-color: #10b981;
    background: #f0fdf4;
  }
  .ts-mode-option:hover:not(.selected) {
    border-color: #94a3b8;
  }
  .ts-mode-radio {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .ts-mode-option.selected .ts-mode-radio {
    border-color: #10b981;
  }
  .ts-mode-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #10b981;
    display: none;
  }
  .ts-mode-option.selected .ts-mode-dot { display: block; }
  .ts-mode-head { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
  .ts-mode-name { font-size: 14px; font-weight: 700; color: #1e293b; }
  .ts-mode-desc { font-size: 12px; color: #64748b; margin: 0; line-height: 1.4; }

  /* Section groups */
  .ts-section {
    background: #f8fafc;
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 16px;
  }
  .ts-section-title {
    font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 14px;
    display: flex; align-items: center; gap: 6px;
  }
  .ts-section-title svg { width: 16px; height: 16px; }

  /* Day pills */
  .ts-day-pills { display: flex; flex-wrap: wrap; gap: 6px; }
  .ts-day {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    background: #fff;
    color: #64748b;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: all .15s;
    user-select: none;
  }
  .ts-day.selected { background: #d1fae5; color: #047857; border-color: #6ee7b7; }
  .ts-day:hover:not(.selected) { border-color: #94a3b8; }

  /* Inputs */
  .ts-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    outline: none;
    transition: border-color .15s;
  }
  .ts-input:focus { border-color: #10b981; }
  .ts-label { display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 4px; }
  .ts-hint { font-size: 11px; color: #94a3b8; margin: 4px 0 0; }
  .ts-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

  /* Conditional content */
  .ts-conditional { border-top: 1px solid #f1f5f9; padding-top: 20px; display: none; }
  .ts-conditional.visible { display: block; }

  /* Save */
  .ts-save-row { margin-top: 20px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
  .ts-save-btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 20px; background: #059669; color: #fff;
    font-size: 13px; font-weight: 700; border-radius: 12px;
    border: none; cursor: pointer; transition: background .2s;
  }
  .ts-save-btn:hover { background: #047857; }
  .ts-save-btn svg { width: 16px; height: 16px; }

  @media (max-width: 640px) {
    .ts-mode-grid { grid-template-columns: 1fr; }
    .ts-grid-2 { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')

<div style="margin-bottom:24px;">
  <p style="font-size:14px;color:#64748b;">Habilita tus canchas para recibir solicitudes de torneos organizados en TuCancha.</p>
</div>

@if($venue->fields->isEmpty())
  <div class="ts-card" style="padding:40px;text-align:center;">
    <svg style="width:48px;height:48px;margin:0 auto 12px;color:#cbd5e1;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
    </svg>
    <p style="font-size:15px;font-weight:600;color:#64748b;">No tenes canchas creadas todavia.</p>
    <p style="font-size:13px;color:#94a3b8;margin-top:4px;">Crea una cancha primero para poder configurar torneos.</p>
  </div>
@else
  @foreach($venue->fields as $fieldIndex => $field)
    @php
      $setting = $field->tournamentSetting;
      $isEnabled = $setting && $setting->tournament_enabled;
      $isAuto = $setting && $setting->auto_approve;
      $selectedDays = $setting->available_days ?? [];
      if (!is_array($selectedDays)) $selectedDays = [];
      $uid = 'field_' . $field->id;
    @endphp

    <div class="ts-card" id="{{ $uid }}">
      <div class="ts-card-header">
        <div>
          <h3>{{ $field->name }}</h3>
          <p>{{ ucfirst($field->sport) }}</p>
        </div>
        <span id="{{ $uid }}_badge" class="{{ $isEnabled ? 'ts-badge-on' : 'ts-badge-off' }}">
          {{ $isEnabled ? 'Habilitado' : 'Deshabilitado' }}
        </span>
      </div>

      <form method="POST" action="{{ route('va.tournament_settings.update', $field) }}" class="ts-body">
        @csrf

        {{-- 1. TOGGLE: Aceptar torneos --}}
        <div class="ts-toggle-row">
          <input type="hidden" name="tournament_enabled" value="{{ $isEnabled ? '1' : '0' }}" id="{{ $uid }}_enabled_val">
          <div class="ts-switch {{ $isEnabled ? 'on' : '' }}" id="{{ $uid }}_switch" onclick="toggleSwitch('{{ $uid }}')">
            <div class="ts-switch-knob"></div>
          </div>
          <div>
            <div class="ts-toggle-label">Aceptar torneos en esta cancha</div>
            <div class="ts-toggle-hint">Los organizadores podran solicitar esta cancha para sus torneos.</div>
          </div>
        </div>

        {{-- 2. CONTENIDO CONDICIONAL --}}
        <div class="ts-conditional {{ $isEnabled ? 'visible' : '' }}" id="{{ $uid }}_content">

          {{-- MODO --}}
          <div class="ts-mode-title">Cuando recibas una solicitud de torneo:</div>
          <input type="hidden" name="auto_approve" value="{{ $isAuto ? '1' : '0' }}" id="{{ $uid }}_auto_val">
          <div class="ts-mode-grid">
            <div class="ts-mode-option {{ !$isAuto ? 'selected' : '' }}" id="{{ $uid }}_mode_manual"
                 onclick="setMode('{{ $uid }}', 'manual')">
              <div class="ts-mode-head">
                <div class="ts-mode-radio"><div class="ts-mode-dot"></div></div>
                <span class="ts-mode-name">Yo apruebo</span>
              </div>
              <p class="ts-mode-desc">Reviso cada solicitud y decido si acepto o rechazo. Me llega una notificacion por cada pedido.</p>
            </div>
            <div class="ts-mode-option {{ $isAuto ? 'selected' : '' }}" id="{{ $uid }}_mode_auto"
                 onclick="setMode('{{ $uid }}', 'auto')">
              <div class="ts-mode-head">
                <div class="ts-mode-radio"><div class="ts-mode-dot"></div></div>
                <span class="ts-mode-name">Automatico</span>
              </div>
              <p class="ts-mode-desc">Todas las solicitudes se aprueban sin tu intervencion. Ideal si queres maximizar la ocupacion.</p>
            </div>
          </div>

          {{-- DISPONIBILIDAD --}}
          <div class="ts-section">
            <div class="ts-section-title">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Disponibilidad
            </div>

            <div style="margin-bottom:14px;">
              <div class="ts-label" style="margin-bottom:8px;">Dias disponibles</div>
              <div class="ts-day-pills">
                @php $dayNames = ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab']; @endphp
                @foreach($dayNames as $i => $dayName)
                  @php $isDaySelected = in_array($i, $selectedDays); @endphp
                  <div class="ts-day {{ $isDaySelected ? 'selected' : '' }}"
                       onclick="toggleDay(this, '{{ $uid }}_day_{{ $i }}')"
                       id="{{ $uid }}_daybtn_{{ $i }}">
                    {{ $dayName }}
                    <input type="checkbox" name="available_days[]" value="{{ $i }}"
                           style="display:none;" {{ $isDaySelected ? 'checked' : '' }}
                           id="{{ $uid }}_day_{{ $i }}">
                  </div>
                @endforeach
              </div>
              <div class="ts-hint">Ninguno seleccionado = todos los dias.</div>
            </div>

            <div class="ts-grid-2">
              <div>
                <div class="ts-label">Desde</div>
                <input type="time" name="available_start_time" class="ts-input"
                       value="{{ $setting->available_start_time ?? '' }}">
              </div>
              <div>
                <div class="ts-label">Hasta</div>
                <input type="time" name="available_end_time" class="ts-input"
                       value="{{ $setting->available_end_time ?? '' }}">
              </div>
            </div>
          </div>

          {{-- PRECIO Y CONTACTO --}}
          <div class="ts-section">
            <div class="ts-section-title">
              <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
              Precio y contacto
            </div>

            <div class="ts-grid-2" style="margin-bottom:12px;">
              <div>
                <div class="ts-label">Precio por partido</div>
                <div style="position:relative;">
                  <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:13px;color:#94a3b8;font-weight:700;">$</span>
                  <input type="number" name="tournament_price_per_match" step="0.01" min="0" class="ts-input"
                         style="padding-left:28px;"
                         value="{{ $setting->tournament_price_per_match ?? '' }}"
                         placeholder="Ej: 25000">
                </div>
                <div class="ts-hint">Vacio = a convenir.</div>
              </div>
              <div>
                <div class="ts-label">Metodo de contacto</div>
                <select name="contact_method" class="ts-input" style="background:#fff;">
                  <option value="whatsapp" {{ ($setting->contact_method ?? '') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                  <option value="phone" {{ ($setting->contact_method ?? '') === 'phone' ? 'selected' : '' }}>Telefono</option>
                  <option value="email" {{ ($setting->contact_method ?? '') === 'email' ? 'selected' : '' }}>Email</option>
                  <option value="internal" {{ ($setting->contact_method ?? '') === 'internal' ? 'selected' : '' }}>Chat interno</option>
                </select>
              </div>
            </div>

            <div>
              <div class="ts-label">Dato de contacto</div>
              <input type="text" name="contact_value" class="ts-input"
                     value="{{ $setting->contact_value ?? '' }}"
                     placeholder="Ej: +54 11 1234-5678">
            </div>
          </div>

          {{-- NOTAS --}}
          <div>
            <div class="ts-label">Notas para organizadores</div>
            <textarea name="notes" rows="2" class="ts-input" style="resize:none;"
                      placeholder="Ej: Tenemos vestuarios, estacionamiento, cantina...">{{ $setting->notes ?? '' }}</textarea>
          </div>

        </div>

        {{-- GUARDAR --}}
        <div class="ts-save-row">
          <button type="submit" class="ts-save-btn">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Guardar
          </button>
        </div>
      </form>
    </div>
  @endforeach
@endif

<script>
function toggleSwitch(uid) {
  const sw = document.getElementById(uid + '_switch');
  const valInput = document.getElementById(uid + '_enabled_val');
  const badge = document.getElementById(uid + '_badge');
  const content = document.getElementById(uid + '_content');

  const isOn = sw.classList.contains('on');
  sw.classList.toggle('on', !isOn);
  valInput.value = isOn ? '0' : '1';
  badge.className = isOn ? 'ts-badge-off' : 'ts-badge-on';
  badge.textContent = isOn ? 'Deshabilitado' : 'Habilitado';
  content.classList.toggle('visible', !isOn);
}

function setMode(uid, mode) {
  const manual = document.getElementById(uid + '_mode_manual');
  const auto = document.getElementById(uid + '_mode_auto');
  const valInput = document.getElementById(uid + '_auto_val');

  manual.classList.remove('selected');
  auto.classList.remove('selected');

  if (mode === 'auto') {
    auto.classList.add('selected');
    valInput.value = '1';
  } else {
    manual.classList.add('selected');
    valInput.value = '0';
  }
}

function toggleDay(el, inputId) {
  const input = document.getElementById(inputId);
  input.checked = !input.checked;
  el.classList.toggle('selected', input.checked);
}
</script>

@endsection
