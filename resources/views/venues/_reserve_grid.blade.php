{{--
  Partial reusable: grid de reservas tipo ATC.
  Usado tanto en venues.show (embedded) como en venues.grid (standalone).
  Variables esperadas:
    - $venue (App\Models\Venue)
    - $modifyReservation (App\Models\Reservation|null) — si está, activa modo "modificar reserva"
--}}
@php $modifyReservation = $modifyReservation ?? null; @endphp
<style>
  .vg-wrap{
    --vg-bg:#0a0a0a; --vg-card:#0e0e10; --vg-card-2:#141417;
    --vg-border:rgba(255,255,255,.08); --vg-border-strong:rgba(255,255,255,.18);
    --vg-text:#fff; --vg-muted:#9ca3af; --vg-faint:#6b7280;
    --vg-green:#10b981; --vg-green-dark:#059669; --vg-green-glow:rgba(16,185,129,.35);
    --vg-red:#ef4444; --vg-amber:#f59e0b;
    --vg-cell-w:120px; --vg-row-h:84px; --vg-fname-w:230px;
    color:var(--vg-text);
    font-family:ui-sans-serif,system-ui,-apple-system,'Inter',sans-serif;
  }

  /* Date picker */
  .vg-dates-wrap{position:relative;margin-bottom:14px}
  .vg-dates-wrap::after{
    content:'';position:absolute;top:0;right:0;bottom:12px;width:40px;pointer-events:none;
    background:linear-gradient(270deg, var(--vg-bg, #0a0a0a), transparent);
    opacity:0;transition:opacity .25s
  }
  .vg-dates-wrap.has-overflow::after{opacity:1}
  .vg-dates{
    display:flex;gap:6px;overflow-x:auto;padding:6px 0 10px;
    scrollbar-width:thin;scrollbar-color:#333 transparent;
    border-bottom:1px solid var(--vg-border);scroll-behavior:smooth
  }
  .vg-dates::-webkit-scrollbar{height:4px}
  .vg-dates::-webkit-scrollbar-thumb{background:#333;border-radius:99px}
  .vg-date{
    flex:0 0 auto;min-width:58px;padding:9px 10px;border-radius:12px;
    background:var(--vg-card);border:1px solid var(--vg-border);
    color:var(--vg-muted);cursor:pointer;text-align:center;
    transition:all .15s;font-family:inherit
  }
  .vg-date:hover{border-color:var(--vg-border-strong);color:#fff}
  .vg-date.active{
    background:var(--vg-green);border-color:var(--vg-green);color:#fff;
    box-shadow:0 4px 14px var(--vg-green-glow)
  }
  .vg-date-dow{font-size:10px;text-transform:uppercase;letter-spacing:.08em;opacity:.8;font-weight:600}
  .vg-date-day{font-size:18px;font-weight:700;line-height:1.1;margin-top:2px}
  .vg-date-mo{font-size:10px;opacity:.7;margin-top:1px}

  /* Legend */
  .vg-legend{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:14px;font-size:12px;color:var(--vg-muted)}
  .vg-legend span{display:inline-flex;align-items:center;gap:6px}
  .vg-legend i{display:inline-block;width:12px;height:12px;border-radius:3px}
  .vg-l-avail{background:rgba(16,185,129,.18);border:1px solid rgba(16,185,129,.4)}
  .vg-l-occ  {background:rgba(239,68,68,.18);border:1px solid rgba(239,68,68,.4)}
  .vg-l-blk  {background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18)}
  .vg-l-past {background:transparent;border:1px dashed rgba(255,255,255,.12)}
  .vg-l-sel  {background:var(--vg-green);box-shadow:0 0 0 2px rgba(16,185,129,.5)}

  /* Grid card */
  .vg-grid-card{
    background:linear-gradient(180deg, var(--vg-card) 0%, #0a0a0c 100%);
    border:1px solid var(--vg-border);border-radius:20px;
    overflow:hidden;position:relative;
    box-shadow:0 30px 80px -30px rgba(0,0,0,.6), inset 0 0 0 1px rgba(255,255,255,.02);
  }
  .vg-grid-card::before{
    content:'';position:absolute;inset:0;border-radius:inherit;pointer-events:none;
    background:radial-gradient(120% 80% at 50% -10%, rgba(16,185,129,.06), transparent 60%);
  }
  .vg-grid-scroll{overflow-x:auto;scrollbar-width:thin;scrollbar-color:#333 transparent;-webkit-overflow-scrolling:touch;position:relative;z-index:1}
  .vg-grid-scroll::-webkit-scrollbar{height:10px}
  .vg-grid-scroll::-webkit-scrollbar-thumb{background:#2a2a2e;border-radius:99px}
  .vg-grid-scroll::-webkit-scrollbar-thumb:hover{background:#3a3a3f}
  /* Fade derecho indicando "hay más" */
  .vg-grid-card::after{
    content:'';position:absolute;top:0;right:0;bottom:10px;width:48px;pointer-events:none;
    background:linear-gradient(270deg, rgba(14,14,16,1), transparent);
    opacity:0;transition:opacity .25s;z-index:2
  }
  .vg-grid-card.has-overflow::after{opacity:1}

  .vg-grid{display:grid;width:100%;user-select:none;-webkit-user-select:none}
  .vg-grid-head{display:contents}
  .vg-h-corner{
    position:sticky;left:0;z-index:20;background:var(--vg-card-2);
    border-right:1px solid var(--vg-border);border-bottom:1px solid var(--vg-border-strong);
    height:56px;display:flex;align-items:center;padding:0 18px;
    font-size:11px;color:var(--vg-faint);text-transform:uppercase;letter-spacing:.1em;font-weight:700
  }
  .vg-h-hour{
    height:56px;background:var(--vg-card-2);
    border-right:1px solid var(--vg-border);border-bottom:1px solid var(--vg-border-strong);
    display:flex;align-items:center;justify-content:center;
    font-size:14px;color:#e5e7eb;font-weight:700;font-variant-numeric:tabular-nums;letter-spacing:-.01em
  }
  .vg-h-hour:last-child{border-right:none}
  .vg-row{display:contents}
  .vg-fname{
    position:sticky;left:0;z-index:10;background:var(--vg-card);
    border-right:1px solid var(--vg-border);border-bottom:1px solid var(--vg-border);
    height:var(--vg-row-h);
    display:flex;flex-direction:column;justify-content:center;padding:0 18px;gap:4px
  }
  .vg-fname-name{font-size:15px;font-weight:700;color:#fff;line-height:1.2;letter-spacing:-.01em;
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .vg-fname-meta{font-size:11px;color:var(--vg-muted);text-transform:uppercase;letter-spacing:.06em;font-weight:600}

  .vg-cell{
    height:var(--vg-row-h);
    border-right:1px solid var(--vg-border);border-bottom:1px solid var(--vg-border);
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    cursor:pointer;transition:background .12s, transform .12s, box-shadow .15s;
    font-size:12px;position:relative;min-width:0;
    padding:6px 4px;text-align:center;gap:2px;overflow:hidden
  }
  .vg-cell:last-child{border-right:none}
  .vg-cell.is-avail{background:rgba(16,185,129,.08);color:#a7f3d0}
  .vg-cell.is-avail:hover{background:rgba(16,185,129,.18);box-shadow:inset 0 0 0 1px rgba(16,185,129,.4)}
  .vg-cell.is-avail .vg-cell-price{font-weight:700;font-size:15px;color:#fff;letter-spacing:-.01em}
  .vg-cell.is-avail .vg-cell-tag{font-size:10px;color:#6ee7b7;text-transform:uppercase;letter-spacing:.06em;font-weight:600}
  .vg-cell.is-occ{background:rgba(239,68,68,.10);color:#fca5a5;cursor:not-allowed;border-left:2px solid rgba(239,68,68,.45)}
  .vg-cell.is-blk{background:repeating-linear-gradient(45deg,rgba(255,255,255,.04) 0 6px,transparent 6px 12px);color:var(--vg-muted);cursor:not-allowed;border-left:2px solid rgba(255,255,255,.18)}
  .vg-cell.is-past{background:transparent;color:var(--vg-faint);cursor:not-allowed;opacity:.45}
  .vg-cell-status-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;line-height:1.1}
  .vg-cell-status-time{font-size:11px;font-weight:600;font-variant-numeric:tabular-nums;margin-top:3px;opacity:.85}
  .vg-cell-status-reason{font-size:10px;margin-top:3px;opacity:.7;
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:90%}
  .vg-cell.is-past .vg-cell-status-label{font-size:14px;font-weight:300;letter-spacing:0}
  .vg-cell.is-empty{background:rgba(255,255,255,.02);cursor:not-allowed}
  .vg-cell.is-selected{
    background:var(--vg-green) !important;color:#fff !important;
    box-shadow:inset 0 0 0 2px rgba(255,255,255,.3);z-index:5
  }
  .vg-cell.is-selected .vg-cell-price{color:#fff}
  .vg-cell.is-selected .vg-cell-tag{color:rgba(255,255,255,.85)}
  .vg-cell-price{font-weight:600;line-height:1.05}
  .vg-cell-tag{
    font-size:9.5px;line-height:1.1;opacity:.9;
    max-width:100%;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;
    overflow:hidden;text-overflow:ellipsis;
    word-break:break-word;hyphens:auto
  }
  .vg-cell-orig{font-size:10px;text-decoration:line-through;color:var(--vg-muted);font-weight:400;line-height:1}

  /* Bottom action bar (fixed, global) */
  .vg-bar{
    position:fixed;left:50%;bottom:24px;transform:translate(-50%, 200%);
    background:#0f1117;border:1px solid var(--vg-border-strong);border-radius:18px;
    padding:14px 18px;display:flex;align-items:center;gap:18px;
    box-shadow:0 20px 60px rgba(0,0,0,.6),0 0 0 1px rgba(16,185,129,.2);
    transition:transform .35s cubic-bezier(.2,.9,.3,1.2);
    z-index:50;max-width:calc(100vw - 32px);min-width:320px
  }
  .vg-bar.is-visible{transform:translate(-50%,0)}
  .vg-bar-info{display:flex;flex-direction:column;gap:2px}
  .vg-bar-line1{font-size:13px;color:var(--vg-muted)}
  .vg-bar-line2{font-size:18px;font-weight:700;color:#fff;letter-spacing:-.01em}
  .vg-bar-total{display:flex;flex-direction:column;align-items:flex-end;margin-left:auto}
  .vg-bar-total small{font-size:10px;color:var(--vg-muted);text-transform:uppercase;letter-spacing:.08em}
  .vg-bar-total b{font-size:20px;color:#10b981;font-weight:800;font-variant-numeric:tabular-nums}
  .vg-bar-btn{
    background:var(--vg-green);color:#fff;border:none;border-radius:12px;
    padding:12px 20px;font-weight:700;font-size:14px;cursor:pointer;
    transition:all .15s;font-family:inherit;letter-spacing:-.005em
  }
  .vg-bar-btn:hover{background:var(--vg-green-dark);transform:translateY(-1px);box-shadow:0 8px 20px var(--vg-green-glow)}
  .vg-bar-btn:disabled{opacity:.6;cursor:not-allowed;transform:none}
  .vg-bar-clear{
    background:transparent;border:1px solid var(--vg-border);color:var(--vg-muted);
    border-radius:10px;padding:10px 12px;cursor:pointer;font-size:12px;
    font-family:inherit;transition:all .15s
  }
  .vg-bar-clear:hover{color:#fff;border-color:var(--vg-border-strong)}
  .vg-bar-recurring{
    background:transparent;border:1px solid var(--vg-border);color:var(--vg-muted);
    border-radius:10px;padding:10px 12px;cursor:pointer;font-size:12px;
    font-family:inherit;transition:all .15s
  }
  .vg-bar-recurring:hover{color:#fff;border-color:var(--vg-border-strong)}
  .vg-bar-recurring.is-active{
    background:rgba(16,185,129,.15);border-color:rgba(16,185,129,.5);color:#6ee7b7
  }

  /* Modal recurring */
  .vg-rec-modal{
    position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.6);
    display:flex;align-items:center;justify-content:center;padding:20px;
    backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);
    animation:vg-rec-fade .15s ease-out
  }
  .vg-rec-modal[hidden]{display:none !important}
  @keyframes vg-rec-fade{from{opacity:0}to{opacity:1}}
  .vg-rec-modal-card{
    background:#0f1117;border:1px solid var(--vg-border-strong);border-radius:18px;
    padding:24px;width:100%;max-width:440px;color:#fff;
    box-shadow:0 30px 80px rgba(0,0,0,.5);
    animation:vg-rec-up .2s ease-out
  }
  @keyframes vg-rec-up{from{transform:translateY(20px);opacity:0}to{transform:none;opacity:1}}
  .vg-rec-modal-card h3{margin:0 0 6px;font-size:20px;font-weight:700;letter-spacing:-.01em}
  .vg-rec-sub{font-size:13px;color:var(--vg-muted);margin:0 0 18px}
  .vg-rec-field{margin-bottom:14px}
  .vg-rec-field label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.08em;
    color:var(--vg-muted);margin-bottom:8px;font-weight:700}
  .vg-rec-pills{display:flex;gap:8px;flex-wrap:wrap}
  .vg-rec-modal-card .vg-rec-pills button{
    flex:1 1 auto;
    background:rgba(255,255,255,.04) !important;
    border:1px solid rgba(255,255,255,.12) !important;
    color:#9ca3af !important;
    padding:10px 14px;border-radius:10px;cursor:pointer;font-size:13px;
    font-family:inherit;transition:all .15s;font-weight:600;
    text-align:center;line-height:1
  }
  .vg-rec-modal-card .vg-rec-pills button:hover{
    color:#fff !important;
    border-color:rgba(255,255,255,.25) !important;
    background:rgba(255,255,255,.07) !important
  }
  .vg-rec-modal-card .vg-rec-pills button.active{
    background:#10b981 !important;
    border-color:#10b981 !important;
    color:#fff !important;
    box-shadow:0 4px 14px rgba(16,185,129,.35), inset 0 0 0 1px rgba(255,255,255,.15) !important
  }
  .vg-rec-modal-card .vg-rec-pills button.active:hover{
    background:#059669 !important;border-color:#059669 !important
  }
  .vg-rec-info{
    background:rgba(255,255,255,.04);border:1px solid var(--vg-border);border-radius:10px;
    padding:10px 12px;font-size:13px;color:#e5e7eb;margin:14px 0 18px;line-height:1.5
  }
  .vg-rec-info b{color:#10b981}
  .vg-rec-actions{display:flex;gap:8px;justify-content:flex-end;padding-top:12px;border-top:1px solid var(--vg-border)}
  .vg-rec-btn-ghost{
    background:transparent;border:1px solid var(--vg-border);color:var(--vg-muted);
    padding:10px 16px;border-radius:10px;cursor:pointer;font-size:13px;font-family:inherit
  }
  .vg-rec-btn-ghost:hover{color:#fff}
  .vg-rec-btn-primary{
    background:var(--vg-green);border:none;color:#fff;padding:10px 18px;border-radius:10px;
    cursor:pointer;font-size:13px;font-weight:700;font-family:inherit
  }
  .vg-rec-btn-primary:hover{background:var(--vg-green-dark)}

  .vg-loading{padding:80px 20px;text-align:center;color:var(--vg-muted)}
  .vg-loading .vg-spin{
    display:inline-block;width:32px;height:32px;border:3px solid rgba(255,255,255,.08);
    border-top-color:var(--vg-green);border-radius:50%;animation:vg-spin .9s linear infinite;margin-bottom:12px
  }
  @keyframes vg-spin{to{transform:rotate(360deg)}}

  .vg-empty{padding:60px 20px;text-align:center;color:var(--vg-muted);font-size:14px}

  @media (max-width:768px){
    .vg-wrap{--vg-cell-w:90px;--vg-row-h:74px;--vg-fname-w:148px}
    .vg-fname{padding:0 12px}
    .vg-fname-name{font-size:13px}
    .vg-fname-meta{font-size:10px}
    .vg-h-corner,.vg-h-hour{height:48px}
    .vg-h-hour{font-size:13px}
    .vg-cell.is-avail .vg-cell-price{font-size:13px}
    .vg-cell.is-occ::after,.vg-cell.is-blk::after{font-size:9px}
  }
  @media (max-width:480px){
    .vg-wrap{--vg-cell-w:78px;--vg-row-h:68px;--vg-fname-w:120px}
    .vg-grid-card{border-radius:14px}
    .vg-bar{
      left:8px;right:8px;bottom:8px;transform:translateY(200%);
      max-width:none;min-width:0;border-radius:14px;padding:10px 12px;gap:8px;
      flex-wrap:wrap
    }
    .vg-bar.is-visible{transform:translateY(0)}
    .vg-bar-info{flex:1 1 60%;min-width:0}
    .vg-bar-line2{font-size:15px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .vg-bar-total{margin-left:0}
    .vg-bar-total b{font-size:18px}
    .vg-bar-clear{flex:0 0 auto;padding:9px 10px}
    .vg-bar-btn{flex:1 1 100%;order:99;text-align:center;padding:11px 14px;font-size:14px}
  }
</style>

<div class="vg-wrap" id="vgRoot"
     data-venue-id="{{ $venue->id }}"
     data-availability-url="{{ route('venues.grid_availability', $venue) }}"
     data-store-url="{{ route('reservations.contiguous') }}"
     data-recurring-url="{{ route('reservations.recurring') }}"
     data-subscription-url="{{ route('recurring.subscription.store') }}"
     data-recurring-mode="{{ $venue->recurring_payment_mode ?? 'upfront' }}"
     data-login-url="{{ route('login') }}"
     data-is-auth="{{ auth()->check() ? '1' : '0' }}"
     @if($modifyReservation)
       data-modify-id="{{ $modifyReservation->id }}"
       data-modify-preview-url="{{ route('reservations.modify.preview', $modifyReservation) }}"
       data-modify-old-time="{{ $modifyReservation->start_at->format('d/m/Y H:i') }}–{{ $modifyReservation->end_at->format('H:i') }}"
       data-modify-old-field="{{ $modifyReservation->field->name ?? '' }}"
     @endif>

@if($modifyReservation)
  {{-- Banner de modify mode --}}
  <div style="background:linear-gradient(135deg, rgba(245,158,11,.15), rgba(245,158,11,.05));
              border:1px solid rgba(245,158,11,.4); border-radius:14px; padding:14px 18px;
              margin-bottom:18px; display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
    <div style="flex-shrink:0; width:36px; height:36px; border-radius:10px; background:rgba(245,158,11,.2);
                display:flex; align-items:center; justify-content:center;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/>
      </svg>
    </div>
    <div style="flex:1; min-width:200px;">
      <div style="font-size:12px; text-transform:uppercase; letter-spacing:.08em; color:#f59e0b; font-weight:700; margin-bottom:2px;">
        Modificando tu reserva
      </div>
      <div style="font-size:14px; color:#e5e7eb; line-height:1.4;">
        Reserva actual: <b>{{ $modifyReservation->field->name }}</b> · {{ $modifyReservation->start_at->isoFormat('dddd D [de] MMM') }} · {{ $modifyReservation->start_at->format('H:i') }}–{{ $modifyReservation->end_at->format('H:i') }}
      </div>
      <div style="font-size:12px; color:#9ca3af; margin-top:4px;">
        Elegí un nuevo horario libre. Si cuesta lo mismo o menos, el cambio es inmediato; si cuesta más, vas a pagar la diferencia por Mercado Pago.
      </div>
    </div>
    <a href="{{ route('my_reservations') }}"
       style="background:transparent; border:1px solid rgba(255,255,255,.18); color:#e5e7eb;
              padding:9px 14px; border-radius:10px; text-decoration:none; font-size:13px; font-weight:600;">
      Cancelar modificación
    </a>
  </div>
@endif

  {{-- Date picker --}}
  <div class="vg-dates-wrap" id="vgDatesWrap">
    <div class="vg-dates" id="vgDates"></div>
  </div>

  {{-- Legend + summary --}}
  <div class="vg-legend">
    <span><i class="vg-l-avail"></i>Disponible</span>
    <span><i class="vg-l-occ"></i>Reservado</span>
    <span><i class="vg-l-blk"></i>Bloqueado</span>
    <span><i class="vg-l-past"></i>Pasado</span>
    <span><i class="vg-l-sel"></i>Tu selección</span>
    <span id="vgSummary" style="margin-left:auto; color:#10b981; font-weight:600;"></span>
  </div>

  {{-- Grid --}}
  <div class="vg-grid-card">
    <div id="vgGridArea">
      <div class="vg-loading"><div class="vg-spin"></div><div>Cargando disponibilidad…</div></div>
    </div>
  </div>
</div>

{{-- Bottom action bar (fixed) --}}
<div class="vg-bar" id="vgBar" role="region" aria-live="polite">
  <div class="vg-bar-info">
    <div class="vg-bar-line1" id="vgBarLine1">Cancha · 1 turno</div>
    <div class="vg-bar-line2" id="vgBarLine2">--:-- — --:--</div>
  </div>
  <div class="vg-bar-total">
    <small>Total</small>
    <b id="vgBarTotal">$0</b>
  </div>
  <button type="button" class="vg-bar-recurring" id="vgBarRecurring" title="Repetir esta reserva en próximas semanas">
    <span style="display:inline-flex;align-items:center;gap:6px;">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 15-6.7L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-15 6.7L3 16"/><path d="M3 21v-5h5"/></svg>
      <span id="vgRecLabel">Repetir</span>
    </span>
  </button>
  <button type="button" class="vg-bar-clear" id="vgBarClear">Cancelar</button>
  <button type="button" class="vg-bar-btn" id="vgBarConfirm">Continuar</button>
</div>

{{-- Modal: configurar repetición --}}
<div id="vgRecModal" class="vg-rec-modal" hidden>
  <div class="vg-rec-modal-card">
    <h3>Repetir esta reserva</h3>
    <p class="vg-rec-sub">Reservá el mismo horario varias semanas seguidas. Cada turno se cuenta por separado.</p>

    <div class="vg-rec-field">
      <label>Frecuencia</label>
      <div class="vg-rec-pills" id="vgRecFreq">
        <button type="button" data-val="weekly" class="active">Cada semana</button>
        <button type="button" data-val="biweekly">Cada 2 semanas</button>
      </div>
    </div>

    <div class="vg-rec-field">
      <label>Cantidad de turnos</label>
      <div class="vg-rec-pills" id="vgRecOcc">
        @foreach([2,4,6,8,12] as $n)
          <button type="button" data-val="{{ $n }}" class="{{ $n === 4 ? 'active' : '' }}">{{ $n }}</button>
        @endforeach
      </div>
    </div>

    <div class="vg-rec-info" id="vgRecPreview">—</div>

    <div class="vg-rec-actions">
      <button type="button" class="vg-rec-btn-ghost" onclick="vgRecClose(false)">Sin repetir</button>
      <button type="button" class="vg-rec-btn-primary" id="vgRecConfirm">Aplicar</button>
    </div>
  </div>
</div>

<script>
(function(){
  const root = document.getElementById('vgRoot');
  if(!root) return;

  const AVAIL_URL = root.dataset.availabilityUrl;
  const STORE_URL = root.dataset.storeUrl;
  const LOGIN_URL = root.dataset.loginUrl;
  const IS_AUTH   = root.dataset.isAuth === '1';
  const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content || '';
  // Modo "modificar reserva existente" — si está activo, el confirm POSTea al preview de modify
  const MODIFY_ID         = root.dataset.modifyId || null;
  const MODIFY_PREVIEW_URL= root.dataset.modifyPreviewUrl || null;
  const IS_MODIFY_MODE    = !!MODIFY_ID;

  let currentDate = new Date(); currentDate.setHours(0,0,0,0);
  let currentData = null;
  let selection   = { fieldId:null, startIdx:null, endIdx:null };
  let dragging    = false;
  let dragAnchor  = null;
  let lastFieldId = null; // recordar cancha vista al cambiar de día (para hacer scroll a esa fila)
  let echoChannels = []; // suscripciones activas a Reverb (para limpiar en cada loadGrid)
  let realtimeReloadTimer = null;
  // Configuración de recurrencia (null si el usuario NO eligió repetir)
  let recurringConfig = null; // { frequency:'weekly'|'biweekly', occurrences: int }
  const RECURRING_URL    = root.dataset.recurringUrl;
  const SUBSCRIPTION_URL = root.dataset.subscriptionUrl;
  const RECURRING_MODE   = root.dataset.recurringMode || 'upfront'; // 'upfront' o 'subscription'

  const fmtMoney = (n) => '$' + Math.round(n).toLocaleString('es-AR');
  const pad2 = (n) => n<10 ? '0'+n : ''+n;
  const isoDate = (d) => d.getFullYear()+'-'+pad2(d.getMonth()+1)+'-'+pad2(d.getDate());

  function renderDates(){
    const cont = document.getElementById('vgDates');
    cont.innerHTML = '';
    const dows = ['DOM','LUN','MAR','MIÉ','JUE','VIE','SÁB'];
    const mos  = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    for(let i=0;i<15;i++){
      const d = new Date(); d.setHours(0,0,0,0); d.setDate(d.getDate()+i);
      const btn = document.createElement('button');
      btn.type='button';
      btn.className = 'vg-date' + (isoDate(d)===isoDate(currentDate) ? ' active' : '');
      btn.innerHTML = `
        <div class="vg-date-dow">${i===0?'HOY':dows[d.getDay()]}</div>
        <div class="vg-date-day">${d.getDate()}</div>
        <div class="vg-date-mo">${mos[d.getMonth()]}</div>
      `;
      btn.addEventListener('click', ()=>{
        // Antes de cambiar día, guardar la cancha en foco (la última seleccionada o la primera con disponibilidad)
        if(selection.fieldId !== null) lastFieldId = selection.fieldId;
        currentDate = d; clearSelection(); renderDates(); loadGrid();
      });
      cont.appendChild(btn);
    }
    // Detectar overflow para mostrar fade derecho
    requestAnimationFrame(() => {
      const wrap = document.getElementById('vgDatesWrap');
      const updateOv = () => {
        const ov = cont.scrollWidth - cont.clientWidth - cont.scrollLeft > 4;
        wrap.classList.toggle('has-overflow', ov);
      };
      updateOv();
      cont.addEventListener('scroll', updateOv);
      window.addEventListener('resize', updateOv);
    });
  }

  async function loadGrid(silent = false){
    const area = document.getElementById('vgGridArea');
    if(!silent){
      area.innerHTML = '<div class="vg-loading"><div class="vg-spin"></div><div>Cargando disponibilidad…</div></div>';
    }
    try{
      const resp = await fetch(AVAIL_URL + '?date=' + isoDate(currentDate), {
        headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
      });
      if(!resp.ok) throw new Error('http '+resp.status);
      currentData = await resp.json();
      renderGrid();
      subscribeRealtime();
    }catch(e){
      if(!silent) area.innerHTML = '<div class="vg-empty">No pudimos cargar la disponibilidad. Probá de nuevo.</div>';
    }
  }

  // ── Real-time: escucha "availability.changed" en cada cancha del complejo ──
  function subscribeRealtime(){
    if(typeof window.Echo === 'undefined' || !currentData) return;
    // Limpiar suscripciones previas
    echoChannels.forEach(name => { try{ window.Echo.leave(name); }catch(e){} });
    echoChannels = [];
    currentData.fields.forEach(f => {
      const chName = `field.${f.id}`;
      try{
        window.Echo.channel(chName).listen('.availability.changed', () => {
          // Debounce: si llegan varios eventos seguidos, recargar una sola vez
          clearTimeout(realtimeReloadTimer);
          realtimeReloadTimer = setTimeout(() => {
            // Mantener selección si todavía es válida; el render siguiente la limpiará si los slots cambiaron
            const prevFieldId = selection.fieldId;
            loadGrid(true).then(() => {
              if(prevFieldId !== null) lastFieldId = prevFieldId;
            });
          }, 600);
        });
        echoChannels.push(chName);
      }catch(e){ /* sin Echo configurado, ignorar */ }
    });
  }

  function renderGrid(){
    const area = document.getElementById('vgGridArea');
    if(!currentData || !currentData.fields.length){
      area.innerHTML = '<div class="vg-empty">Este complejo no tiene canchas activas.</div>';
      return;
    }
    const hourSet = new Set();
    currentData.fields.forEach(f => f.slots.forEach(s => hourSet.add(s.start_at)));
    const hours = Array.from(hourSet).sort();

    if(!hours.length){
      area.innerHTML = '<div class="vg-empty">Este complejo no tiene horarios cargados para este día.</div>';
      return;
    }

    const totalCols = hours.length;
    // minmax: cells stretch to fill available width when few hours;
    // mantiene el ancho mínimo (var --vg-cell-w) y dispara scroll cuando hay muchas
    const gridStyle = `grid-template-columns: var(--vg-fname-w) repeat(${totalCols}, minmax(var(--vg-cell-w), 1fr));`;

    let html = '<div class="vg-grid-scroll"><div class="vg-grid" style="'+gridStyle+'">';
    html += '<div class="vg-h-corner">Cancha</div>';
    hours.forEach(h => { html += `<div class="vg-h-hour">${h}</div>`; });

    currentData.fields.forEach((f, fi) => {
      const sportLabel = (f.sport ? f.sport.charAt(0).toUpperCase()+f.sport.slice(1) : '') + (f.format ? ' · F'+f.format : '');
      html += `<div class="vg-fname">
        <div class="vg-fname-name">${escapeHtml(f.name)}</div>
        <div class="vg-fname-meta">${escapeHtml(sportLabel)}</div>
      </div>`;
      const slotByHour = {};
      f.slots.forEach((s, si) => { slotByHour[s.start_at] = { ...s, idx:si }; });

      // Para fusionar slots consecutivos de la misma reserva/bloqueo,
      // pre-calculo span por hora para ESTA fila.
      const spanByHour = {}; // hour => { span: N, isStart: true } o { skip: true }
      let i = 0;
      while (i < hours.length) {
        const h = hours[i];
        const s = slotByHour[h];
        if (s && s.entity_key) {
          // Contar consecutivos con mismo entity_key
          let span = 1;
          for (let j = i + 1; j < hours.length; j++) {
            const sj = slotByHour[hours[j]];
            if (sj && sj.entity_key === s.entity_key) { span++; }
            else break;
          }
          spanByHour[h] = { span, isStart: true };
          for (let k = 1; k < span; k++) spanByHour[hours[i + k]] = { skip: true };
          i += span;
        } else {
          i++;
        }
      }

      hours.forEach(h => {
        const meta = spanByHour[h];
        if (meta && meta.skip) return; // celda cubierta por un span anterior

        const s = slotByHour[h];
        if(!s){ html += '<div class="vg-cell is-empty"></div>'; return; }
        let cls = 'vg-cell'; let inner = '';
        let title = '';
        if(s.status === 'AVAILABLE'){
          cls += ' is-avail';
          const orig = (s.has_discount && s.original_price !== s.price)
            ? `<div class="vg-cell-orig">${fmtMoney(s.original_price)}</div>` : '';
          const tag  = s.has_discount ? `<div class="vg-cell-tag">${escapeHtml(s.discount_label||'OFERTA')}</div>`
                       : (s.is_night_price ? `<div class="vg-cell-tag">Nocturno</div>` : '');
          inner = `${orig}<div class="vg-cell-price">${fmtMoney(s.price)}</div>${tag}`;
          title = `${s.start_at} – ${s.end_at} · ${fmtMoney(s.price)}`;
        } else if(s.status === 'UNAVAILABLE'){
          cls += ' is-occ';
          const endLabel = s.occupied_until || s.end_at;
          title = `Reservado · ${s.start_at}–${endLabel}`;
          inner = `<div class="vg-cell-status-label">Reservado</div>` +
                  `<div class="vg-cell-status-time">${s.start_at}–${endLabel}</div>`;
        } else if(s.status === 'BLOCKED'){
          cls += ' is-blk';
          title = 'Bloqueado' + (s.reason ? ' · ' + s.reason : '');
          inner = `<div class="vg-cell-status-label">Bloqueado</div>` +
                  (s.reason ? `<div class="vg-cell-status-reason">${escapeHtml(s.reason)}</div>` : '');
        } else if(s.status === 'PAST'){
          cls += ' is-past';
          title = 'Horario pasado';
          inner = `<div class="vg-cell-status-label">—</div>`;
        }
        const spanAttr = (meta && meta.isStart && meta.span > 1) ? ` style="grid-column: span ${meta.span};"` : '';
        html += `<div class="${cls}"${spanAttr} data-field="${f.id}" data-fi="${fi}" data-si="${s.idx}" data-status="${s.status}" data-price="${s.price}" data-start="${s.start_at}" data-end="${s.end_at}" title="${escapeHtml(title)}">${inner}</div>`;
      });
    });
    html += '</div></div>';
    area.innerHTML = html;

    bindCellEvents(area);

    // Summary debajo de la leyenda
    let availCount = 0, minPrice = Infinity;
    currentData.fields.forEach(f => f.slots.forEach(s => {
      if(s.status === 'AVAILABLE'){ availCount++; if(s.price < minPrice) minPrice = s.price; }
    }));
    const summaryEl = document.getElementById('vgSummary');
    if(summaryEl){
      if(availCount > 0){
        summaryEl.textContent = `${availCount} turno${availCount>1?'s':''} disponible${availCount>1?'s':''} · desde ${fmtMoney(minPrice)}`;
        summaryEl.style.color = '#10b981';
      } else {
        summaryEl.textContent = 'Sin turnos disponibles este día';
        summaryEl.style.color = '#9ca3af';
      }
    }

    // Autoscroll a la primera hora con disponibilidad (no pasada)
    requestAnimationFrame(() => {
      const scroller = area.querySelector('.vg-grid-scroll');
      if(!scroller) return;

      // Si el usuario tenía una cancha en foco al cambiar de día y sigue presente,
      // priorizar autoscroll a la primera celda disponible de ESA cancha.
      let firstAvail = null;
      if(lastFieldId !== null){
        const targetField = currentData.fields.find(f => f.id === lastFieldId);
        if(targetField){
          const fi = currentData.fields.indexOf(targetField);
          firstAvail = area.querySelector(`.vg-cell.is-avail[data-fi="${fi}"]`);
        }
      }
      if(!firstAvail) firstAvail = area.querySelector('.vg-cell.is-avail');

      if(firstAvail){
        const fnameW = parseInt(getComputedStyle(area.querySelector('.vg-grid')).gridTemplateColumns.split(' ')[0]) || 200;
        const cellLeft = firstAvail.offsetLeft - fnameW - 8;
        if(cellLeft > 0) scroller.scrollLeft = cellLeft;
      }
      // Detectar overflow horizontal para mostrar el fade derecho
      const card = document.querySelector('.vg-grid-card');
      const updateOverflow = () => {
        if(scroller.scrollWidth - scroller.clientWidth - scroller.scrollLeft > 4){
          card.classList.add('has-overflow');
        }else{
          card.classList.remove('has-overflow');
        }
      };
      updateOverflow();
      scroller.addEventListener('scroll', updateOverflow);
      window.addEventListener('resize', updateOverflow);
    });
  }

  function escapeHtml(str){
    return String(str ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  }

  function bindCellEvents(area){
    const cells = area.querySelectorAll('.vg-cell');
    cells.forEach(cell => {
      cell.addEventListener('mousedown', onDown);
      cell.addEventListener('mouseenter', onEnter);
      cell.addEventListener('touchstart', onTouchStart, {passive:false});
    });
    document.addEventListener('mouseup', onUp);
    document.addEventListener('touchend', onUp);
    document.addEventListener('touchmove', onTouchMove, {passive:false});
  }

  function onDown(e){
    const c = e.currentTarget;
    if(c.dataset.status !== 'AVAILABLE') return;
    e.preventDefault();
    dragging = true;
    dragAnchor = { fieldId: +c.dataset.field, fi: +c.dataset.fi, si: +c.dataset.si };
    selection = { fieldId: dragAnchor.fieldId, startIdx: dragAnchor.si, endIdx: dragAnchor.si };
    paintSelection();
    updateBar();
  }
  function onEnter(e){
    if(!dragging) return;
    if(IS_MODIFY_MODE) return; // en modify mode: sólo 1 slot seleccionable
    const c = e.currentTarget;
    if(+c.dataset.fi !== dragAnchor.fi) return;
    if(c.dataset.status !== 'AVAILABLE') return;
    const si = +c.dataset.si;
    const a = dragAnchor.si;
    let lo = Math.min(a, si), hi = Math.max(a, si);
    if(!isContiguousAvailable(dragAnchor.fi, lo, hi)) return;
    selection.startIdx = lo; selection.endIdx = hi;
    paintSelection();
    updateBar();
  }
  function onUp(){ if(dragging){ dragging = false; } }

  function onTouchStart(e){
    if(e.touches.length !== 1) return;
    onDown({currentTarget:e.currentTarget, preventDefault:()=>e.preventDefault()});
  }
  function onTouchMove(e){
    if(!dragging || e.touches.length !== 1) return;
    e.preventDefault();
    const t = e.touches[0];
    const el = document.elementFromPoint(t.clientX, t.clientY);
    if(el && el.classList && el.classList.contains('vg-cell')){
      onEnter({currentTarget: el});
    }
  }

  function isContiguousAvailable(fi, lo, hi){
    const row = document.querySelectorAll(`.vg-cell[data-fi="${fi}"]`);
    for(let i=lo;i<=hi;i++){
      const c = Array.from(row).find(x => +x.dataset.si === i);
      if(!c || c.dataset.status !== 'AVAILABLE') return false;
    }
    return true;
  }

  function paintSelection(){
    document.querySelectorAll('.vg-cell.is-selected').forEach(c => c.classList.remove('is-selected'));
    if(selection.fieldId === null) return;
    const cells = document.querySelectorAll(`.vg-cell[data-fi="${dragAnchor.fi}"]`);
    cells.forEach(c => {
      const si = +c.dataset.si;
      if(si >= selection.startIdx && si <= selection.endIdx){ c.classList.add('is-selected'); }
    });
  }

  function getSelectedSlots(){
    if(selection.fieldId === null) return [];
    const cells = document.querySelectorAll(`.vg-cell.is-selected`);
    return Array.from(cells).map(c => ({
      start: c.dataset.start, end: c.dataset.end, price: +c.dataset.price
    })).sort((a,b) => a.start.localeCompare(b.start));
  }

  function updateBar(){
    const slots = getSelectedSlots();
    const bar = document.getElementById('vgBar');
    if(!slots.length){ bar.classList.remove('is-visible'); return; }
    const field = currentData.fields.find(f => f.id === selection.fieldId);
    const total = slots.reduce((acc,s) => acc + s.price, 0);
    const start = slots[0].start, end = slots[slots.length-1].end;
    document.getElementById('vgBarLine1').textContent = `${field.name} · ${slots.length} turno${slots.length>1?'s':''}`;
    document.getElementById('vgBarLine2').textContent = `${start} — ${end}`;
    document.getElementById('vgBarTotal').textContent = fmtMoney(total);
    // Si el usuario amplía a >1 slot, la opción de repetir semanal pierde sentido (1 sólo turno por semana). Reset.
    if(slots.length > 1 && recurringConfig){
      recurringConfig = null;
      updateRecurringButton();
    }
    // Mostrar/ocultar botón "Repetir" — sólo aplica con 1 slot y NO en modify mode
    if(recBtn) recBtn.style.display = (slots.length === 1 && !IS_MODIFY_MODE) ? '' : 'none';
    // En modify mode el botón de confirmar dice algo distinto
    if(IS_MODIFY_MODE){
      document.getElementById('vgBarConfirm').textContent = 'Continuar a confirmación';
    }
    bar.classList.add('is-visible');
  }

  function clearSelection(){
    selection = { fieldId:null, startIdx:null, endIdx:null };
    recurringConfig = null;
    updateRecurringButton();
    paintSelection();
    document.getElementById('vgBar').classList.remove('is-visible');
  }

  document.getElementById('vgBarClear').addEventListener('click', clearSelection);

  // ── Modal recurrencia ────────────────────────────────────────────────────
  const recModal = document.getElementById('vgRecModal');
  const recBtn   = document.getElementById('vgBarRecurring');
  const recLabel = document.getElementById('vgRecLabel');

  function updateRecurringButton(){
    if(!recBtn) return;
    if(recurringConfig){
      recBtn.classList.add('is-active');
      const cadence = recurringConfig.frequency === 'biweekly' ? 'quincenal' : 'semanal';
      recLabel.textContent = `${recurringConfig.occurrences} turnos · ${cadence}`;
    } else {
      recBtn.classList.remove('is-active');
      recLabel.textContent = 'Repetir';
    }
  }

  function openRecModal(){
    if(selection.fieldId === null) return;
    // Pre-cargar pills con el config actual o defaults
    const freq = recurringConfig?.frequency || 'weekly';
    const occ  = recurringConfig?.occurrences || 4;
    document.querySelectorAll('#vgRecFreq button').forEach(b => {
      b.classList.toggle('active', b.dataset.val === freq);
    });
    document.querySelectorAll('#vgRecOcc button').forEach(b => {
      b.classList.toggle('active', +b.dataset.val === occ);
    });
    updateRecPreview();
    recModal.hidden = false;
  }
  window.vgRecClose = function(applied){
    if(!applied){
      recurringConfig = null;
      updateRecurringButton();
    }
    recModal.hidden = true;
  };

  recBtn.addEventListener('click', openRecModal);
  recModal.addEventListener('click', e => { if(e.target === recModal) recModal.hidden = true; });
  document.addEventListener('keydown', e => { if(e.key === 'Escape') recModal.hidden = true; });

  document.querySelectorAll('#vgRecFreq button').forEach(b => {
    b.addEventListener('click', () => {
      document.querySelectorAll('#vgRecFreq button').forEach(x => x.classList.remove('active'));
      b.classList.add('active');
      updateRecPreview();
    });
  });
  document.querySelectorAll('#vgRecOcc button').forEach(b => {
    b.addEventListener('click', () => {
      document.querySelectorAll('#vgRecOcc button').forEach(x => x.classList.remove('active'));
      b.classList.add('active');
      updateRecPreview();
    });
  });

  function getRecChoice(){
    const f = document.querySelector('#vgRecFreq button.active')?.dataset.val || 'weekly';
    const o = +(document.querySelector('#vgRecOcc button.active')?.dataset.val || 4);
    return { frequency: f, occurrences: o };
  }
  function updateRecPreview(){
    const c = getRecChoice();
    const slots = getSelectedSlots();
    if(!slots.length){ document.getElementById('vgRecPreview').textContent = '—'; return; }
    const weeksSpan = c.frequency === 'biweekly' ? c.occurrences * 2 - 1 : c.occurrences;
    const each = c.frequency === 'biweekly' ? 'una vez cada 2 semanas' : 'una vez por semana';
    const period = c.frequency === 'biweekly'
      ? `repartidos en ${weeksSpan} semanas`
      : `durante ${weeksSpan} semanas`;
    const paymentNote = RECURRING_MODE === 'subscription'
      ? `<div style="margin-top:8px; padding-top:8px; border-top:1px solid rgba(255,255,255,.08); font-size:12px; color:#9ca3af;">
           <b style="color:#10b981;">Pago como abono mensual:</b> Mercado Pago va a cobrar este turno mes a mes automáticamente. Podés cancelar cuando quieras.
         </div>`
      : `<div style="margin-top:8px; padding-top:8px; border-top:1px solid rgba(255,255,255,.08); font-size:12px; color:#9ca3af;">
           <b style="color:#10b981;">Pago único:</b> vas a pagar todas las reservas juntas en un solo cobro al confirmar.
         </div>`;
    document.getElementById('vgRecPreview').innerHTML =
      `Vas a reservar este horario <b>${c.occurrences} ${c.occurrences === 1 ? 'vez' : 'veces'}</b> en total — ` +
      `${each}, ${period}. ` +
      `Si alguna fecha cae ocupada o bloqueada, esa repetición se omite.` +
      paymentNote;
  }
  document.getElementById('vgRecConfirm').addEventListener('click', () => {
    recurringConfig = getRecChoice();
    updateRecurringButton();
    recModal.hidden = true;
  });

  document.getElementById('vgBarConfirm').addEventListener('click', async () => {
    const slots = getSelectedSlots();
    if(!slots.length) return;
    if(!IS_AUTH){ window.location.href = LOGIN_URL; return; }

    const btn = document.getElementById('vgBarConfirm');
    btn.disabled = true; btn.textContent = 'Procesando…';

    const dateStr = isoDate(currentDate);
    const startAt = dateStr + ' ' + slots[0].start + ':00';

    // ── Modify mode: enviar al preview de modificación con form HTML (full page) ──
    if(IS_MODIFY_MODE){
      if(slots.length !== 1){
        alert('Para modificar la reserva tenés que elegir un solo turno.');
        btn.disabled = false; btn.textContent = 'Continuar a confirmación';
        return;
      }
      // Submit form clásico — modify preview devuelve una vista (no JSON)
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = MODIFY_PREVIEW_URL;
      form.innerHTML = `
        <input type="hidden" name="_token" value="${CSRF}">
        <input type="hidden" name="field_id" value="${selection.fieldId}">
        <input type="hidden" name="start_at" value="${startAt}">
      `;
      document.body.appendChild(form);
      form.submit();
      return;
    }

    try{
      let resp, data;

      if(recurringConfig && slots.length === 1){
        // ── Modo SUSCRIPCIÓN (mensual via MP) — el complejo cobra cuotas mes a mes ──
        if(RECURRING_MODE === 'subscription'){
          // El endpoint de suscripción espera un form classic POST y redirige a MP
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = SUBSCRIPTION_URL;
          form.innerHTML = `
            <input type="hidden" name="_token" value="${CSRF}">
            <input type="hidden" name="field_id" value="${selection.fieldId}">
            <input type="hidden" name="start_at" value="${startAt}">
            <input type="hidden" name="frequency" value="${recurringConfig.frequency}">
            <input type="hidden" name="occurrences" value="${recurringConfig.occurrences}">
          `;
          document.body.appendChild(form);
          form.submit();
          return;
        }

        // ── Modo UPFRONT (1 slot × N semanas, todo cobrado al toque en un batch) ──
        resp = await fetch(RECURRING_URL, {
          method:'POST',
          headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},
          body: JSON.stringify({
            field_id:    selection.fieldId,
            start_at:    startAt,
            frequency:   recurringConfig.frequency,
            occurrences: recurringConfig.occurrences,
          })
        });
        data = await resp.json();
        if(!resp.ok || !data.batch){
          const failed = data.summary?.failed ?? 0;
          alert((data.message || 'No se pudo crear la reserva recurrente.') + (failed ? ` (${failed} fechas no disponibles)` : ''));
          btn.disabled = false; btn.textContent = 'Continuar';
          loadGrid();
          return;
        }
        window.location.href = data.batch.checkout_url;
        return;
      }

      if(recurringConfig && slots.length > 1){
        alert('La repetición semanal sólo aplica a 1 turno. Quitá la repetición o seleccioná un solo slot.');
        btn.disabled = false; btn.textContent = 'Continuar';
        return;
      }

      // ── Reserva normal (1 o varios slots consecutivos del mismo día) ──
      resp = await fetch(STORE_URL, {
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF,'X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify({
          field_id: selection.fieldId,
          start_at: startAt,
          slots:    slots.length
        })
      });
      data = await resp.json();
      if(!resp.ok){
        alert(data.error || 'No se pudo crear la reserva.');
        btn.disabled = false; btn.textContent = 'Continuar';
        loadGrid();
        return;
      }
      window.location.href = data.checkout_url;
    }catch(e){
      alert('Error de red. Intentá de nuevo.');
      btn.disabled = false; btn.textContent = 'Continuar';
    }
  });

  renderDates();
  loadGrid();
})();
</script>
