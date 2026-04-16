@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.app')

@section('title', 'Mi actividad')

@push('styles')
<style>
  /* ── Tabs ─────────────────────────────────────────── */
  .my-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 4px;
  }

  .my-tabs::-webkit-scrollbar { display: none; }

  .my-tab {
    padding: 10px 22px;
    border-radius: var(--radius-full);
    border: 1px solid rgba(255,255,255,.1);
    background: #111;
    font-size: 14px;
    font-weight: 700;
    color: #a0a0a0;
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s;
    font-family: inherit;
    white-space: nowrap;
    flex-shrink: 0;
  }

  .my-tab.active {
    background: #22c55e;
    color: #050505;
    border-color: #22c55e;
  }

  .my-tab-count {
    display: inline-block;
    margin-left: 6px;
    background: rgba(0,0,0,.12);
    border-radius: var(--radius-full);
    padding: 1px 7px;
    font-size: 12px;
    font-weight: 800;
  }

  .my-tab.active .my-tab-count {
    background: rgba(255,255,255,.2);
  }

  .tab-panel { display: none; }
  .tab-panel.active { display: block; }

  /* ── Batch card ───────────────────────────────────── */
  .batch-card {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 18px;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: var(--shadow-sm);
  }

  .batch-card-header {
    padding: 20px 22px 16px 22px;
    border-bottom: 1px solid var(--color-border-light);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    flex-wrap: wrap;
  }

  .batch-card-title {
    margin: 0 0 4px 0;
    font-size: 20px;
    font-weight: 800;
    letter-spacing: -0.01em;
  }

  .batch-card-meta {
    font-size: 13px;
    color: var(--color-text-secondary);
  }

  .batch-discount-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: var(--radius-full);
    background: rgba(34,197,94,.1);
    color: #6ee7a0;
    font-size: 12px;
    font-weight: 800;
    margin-top: 6px;
  }

  .batch-slots {
    padding: 0 22px 8px 22px;
  }

  .batch-slot {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--color-bg-hover);
    flex-wrap: wrap;
  }

  .batch-slot:last-child {
    border-bottom: none;
  }

  .batch-slot-date {
    font-size: 14px;
    font-weight: 700;
    min-width: 130px;
  }

  .batch-slot-time {
    font-size: 13px;
    color: var(--color-text-secondary);
    min-width: 100px;
  }

  .batch-slot-code {
    font-size: 15px;
    font-weight: 800;
    letter-spacing: 0.06em;
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 8px;
    padding: 3px 10px;
    color: var(--color-text);
  }

  .batch-card-footer {
    padding: 14px 22px;
    background: #0a0a0a;
    border-top: 1px solid rgba(255,255,255,.06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .batch-total {
    font-size: 14px;
    color: #a0a0a0;
  }

  .batch-total strong {
    font-size: 18px;
    font-weight: 800;
    color: var(--color-text);
  }

  /* ── Recurrentes sub-tabs ─────────────────────────── */
  .recur-subtab {
    padding: 7px 16px;
    border-radius: var(--radius-full);
    border: 1px solid rgba(255,255,255,.1);
    background: #111;
    font-size: 13px;
    font-weight: 700;
    color: #a0a0a0;
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s;
    font-family: inherit;
    white-space: nowrap;
  }

  .recur-subtab.active {
    background: var(--color-primary);
    color: var(--color-text-inverse);
    border-color: var(--color-primary);
  }

  .recur-subtab.active .my-tab-count {
    background: rgba(255,255,255,.25);
  }
</style>
@endpush

@section('content')
  <div class="page-card" style="margin-bottom:22px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 8px 0; font-size:34px; letter-spacing:-0.02em;">Mi actividad</h1>
        <p class="muted" style="margin:0;">
          Tus turnos, partidos Falta Uno y actividad reciente.
        </p>
      </div>

      <div>
        <a href="{{ route('venues.index') }}" class="btn btn-primary">Explorar complejos</a>
      </div>
    </div>
  </div>

  {{-- ── Tabs ──────────────────────────────────────────────────────── --}}
  <div class="my-tabs">
    <button class="my-tab active" onclick="switchTab('individual', this)">
      Mis turnos
      <span class="my-tab-count">{{ $reservations->count() }}</span>
    </button>
    <button class="my-tab" onclick="switchTab('recurrentes', this)">
      Recurrentes
      <span class="my-tab-count">{{ $batches->count() + $recurringSubscriptions->count() }}</span>
    </button>
    <button class="my-tab" onclick="switchTab('faltauno', this)" style="display:inline-flex;align-items:center;gap:6px;">
      <i data-lucide="zap" style="width:14px;height:14px;stroke:currentColor;"></i> Falta Uno
      <span class="my-tab-count">{{ $misPartidos->count() }}</span>
    </button>
  </div>

  {{-- Banner partidos sin calificar --}}
  @if($pendingRatingsCount > 0)
    <div style="background:rgba(245,158,11,.08); border:1.5px solid rgba(245,158,11,.2); border-radius:14px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; gap:12px; flex-wrap:wrap;" data-aos="fade-down">
      <i data-lucide="star" style="width:20px;height:20px;stroke:#fbbf24;stroke-width:2;flex-shrink:0;"></i>
      <div style="flex:1; min-width:180px;">
        <div style="font-size:14px; font-weight:700; color:#fbbf24;">
          Tenes {{ $pendingRatingsCount }} partido{{ $pendingRatingsCount > 1 ? 's' : '' }} sin calificar
        </div>
        <div style="font-size:12px; color:#fbbf24; margin-top:2px;">Califica a tus companeros para mejorar la experiencia de todos.</div>
      </div>
      <a href="{{ route('falta-uno.show', $pendingRatingGames->first()) }}" style="display:inline-flex; align-items:center; gap:5px; font-size:13px; font-weight:700; color:#fff; background:#d97706; padding:8px 16px; border-radius:10px; text-decoration:none; white-space:nowrap;">
        Calificar <i data-lucide="arrow-right" style="width:14px;height:14px;stroke:currentColor;"></i>
      </a>
    </div>
  @endif

  {{-- ── Panel: turnos individuales ────────────────────────────────── --}}
  <div id="panel-individual" class="tab-panel active">
    @if($reservations->isEmpty())
      <div class="page-card" style="text-align:center; padding:48px 24px;">
        <div style="width:72px; height:72px; margin:0 auto 18px; background:linear-gradient(135deg, rgba(34,197,94,.1), rgba(34,197,94,.15)); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
        </div>
        <h3 style="margin:0 0 8px; font-size:18px; font-weight:800; letter-spacing:-0.01em;">Todavía no tenés turnos</h3>
        <p class="muted" style="margin:0 0 20px; max-width:340px; margin-left:auto; margin-right:auto; line-height:1.6; font-size:14px;">
          Buscá un complejo cerca tuyo, elegí la cancha y el horario que te guste, y reservá en segundos.
        </p>
        <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
          <a href="{{ route('venues.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:#111; color:#fff; border-radius:12px; font-size:14px; font-weight:700; text-decoration:none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            Buscar cancha
          </a>
          <a href="{{ route('match_history') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:rgba(255,255,255,.04); color:#a0a0a0; border:1.5px solid rgba(255,255,255,.08); border-radius:12px; font-size:14px; font-weight:600; text-decoration:none;">
            Ver historial
          </a>
        </div>
      </div>
    @else
      <div class="grid" style="grid-template-columns:repeat(auto-fit, minmax(min(340px, 100%), 1fr));">
        @foreach($reservations as $r)
          <article class="venue-card" style="border-radius:18px;">
            <div class="venue-card-body" style="padding:20px;">
              <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
                <div>
                  <div style="font-size:12px; color:#a0a0a0; margin-bottom:6px;">
                    Reserva #{{ $r->id }}
                  </div>

                  <h3 style="margin:0 0 6px 0; font-size:22px;">
                    {{ $r->field->name }}
                  </h3>

                  <div class="muted">
                    {{ $r->field->venue->name }}
                  </div>
                </div>

                <span
                  id="status-badge-{{ $r->id }}"
                  style="padding:6px 12px; border-radius:999px; font-weight:700; font-size:13px; {{ ReservationStatus::color($r->status) }}"
                >
                  {{ ReservationStatus::label($r->status) }}
                </span>
              </div>

              <div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:12px;">
                <span class="badge">{{ $r->start_at->format('d/m/Y') }}</span>
                <span class="badge">{{ $r->start_at->format('H:i') }} - {{ $r->end_at->format('H:i') }}</span>
                <span class="badge">{{ $r->currency }} {{ number_format($r->total_amount, 0, ',', '.') }}</span>
              </div>

              @if($r->verification_code && $r->status === 'PAID')
                <div class="page-card" style="margin-top:14px; padding:14px; border-radius:14px;">
                  <div style="font-size:12px; color:#a0a0a0; margin-bottom:4px;">Código de verificación</div>
                  <div style="font-size:24px; font-weight:800; letter-spacing:0.06em;">
                    {{ $r->verification_code }}
                  </div>
                </div>
              @endif

              <div id="reservation-meta-{{ $r->id }}" style="margin-top:12px; font-size:13px;">
                @if($r->status === 'PENDING_PAYMENT' && (!$r->expires_at || $r->expires_at->isFuture()))
                  <div style="color:#fbbf24;">
                    <div>
                      Pendiente de pago
                      @if($r->expires_at)
                        — vence {{ $r->expires_at->format('d/m/Y H:i') }}
                      @endif
                    </div>

                    @if($r->expires_at)
                      <div id="countdown-{{ $r->id }}" style="margin-top:6px; font-weight:700;"></div>
                    @endif
                  </div>
                @elseif($r->status === 'PENDING_CASH')
                  <div style="color:#93c5fd; font-weight:700;">Pago en el complejo — recorda pagar al llegar.</div>
                @elseif($r->status === 'EXPIRED')
                  <div style="color:#f87171; font-weight:700;">Reserva vencida por falta de pago.</div>
                @elseif($r->status === 'CANCELLED')
                  <div style="color:#f87171; font-weight:700;">Reserva cancelada.</div>
                @elseif($r->status === 'PAID')
                  <div style="color:#6ee7a0; font-weight:700;">Reserva pagada correctamente.</div>
                @endif

                @if($r->payment_status)
                  <div class="muted" style="margin-top:6px;">
                    Estado de pago: <strong>{{ $r->payment_status }}</strong>
                  </div>
                @endif
              </div>

              <div id="actions-wrap-{{ $r->id }}" style="display:flex; gap:10px; flex-wrap:wrap; margin-top:16px;">
                <a href="{{ route('reservations.show', $r) }}" class="btn">Ver detalle</a>

                @if($r->status === 'PENDING_PAYMENT' && (!$r->expires_at || $r->expires_at->isFuture()))
                  <span id="pay-btn-wrap-{{ $r->id }}">
                    <a href="{{ route('reservations.checkout', $r) }}" class="btn btn-primary">Pagar</a>
                  </span>
                @endif

                @if(in_array($r->status, ['PENDING_PAYMENT','PENDING_CASH','PAID']))
                  @php
                    $cancellationHours = $r->field->venue->cancellation_hours ?? null;
                    $canCancel = in_array($r->status, ['PENDING_PAYMENT', 'PENDING_CASH'])
                        || $cancellationHours === null
                        || now()->isBefore($r->start_at->copy()->subHours($cancellationHours));
                  @endphp

                  @if($canCancel)
                    <form method="POST" action="{{ route('reservations.cancel', $r) }}" style="margin:0;">
                      @csrf
                      <button type="submit" class="btn">Cancelar</button>
                    </form>
                  @else
                    <span class="badge" style="background:rgba(245,158,11,.08); color:#fbbf24; border-color:rgba(245,158,11,.2); font-size:12px;">
                      Cancelación no disponible — venció el plazo de {{ $cancellationHours }}h
                    </span>
                  @endif
                @endif

                @if($r->status === 'PAID' && $r->batch_id === null && $r->start_at->isFuture())
                  @php
                    $modHours = $r->field->venue->modification_hours ?? null;
                    $canModify = $modHours !== null && now()->isBefore($r->start_at->copy()->subHours($modHours));
                  @endphp
                  @if($canModify)
                    <a href="{{ route('reservations.modify.show', $r) }}" class="btn btn-primary">Cambiar horario</a>
                  @endif
                @endif

                @if(in_array($r->status, ['EXPIRED', 'CANCELLED']))
                  <a href="{{ route('fields.show', $r->field) }}" class="btn btn-primary">Volver a la cancha</a>
                @endif
              </div>
            </div>
          </article>
        @endforeach
      </div>
    @endif

    <div style="text-align:center; margin-top:20px;">
      <a href="{{ route('match_history') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:#16a34a; text-decoration:none;" onmouseover="this.style.color='#15803d'" onmouseout="this.style.color='#16a34a'">
        <i data-lucide="history" style="width:15px;height:15px;stroke:currentColor;"></i> Ver historial completo de partidos
      </a>
    </div>
  </div>

  {{-- ── Panel: reservas recurrentes ────────────────────────────────── --}}
  <div id="panel-recurrentes" class="tab-panel">
    @if($recurringSubscriptions->isEmpty() && $batches->isEmpty())
      <div class="page-card" style="text-align:center; padding:36px 24px;">
        <div style="margin-bottom:10px;"><i data-lucide="refresh-cw" style="width:36px;height:36px;stroke:#22c55e;stroke-width:1.5;"></i></div>
        <h3 style="margin:0 0 8px;">No tenés reservas recurrentes</h3>
        <p class="muted" style="margin-bottom:14px;">
          Cuando reserves un turno recurrente (semanal o quincenal), aparece acá.
        </p>
        <a href="{{ route('venues.index') }}" class="btn btn-primary">Ver complejos</a>
      </div>
    @else
      {{-- Sub-tabs dentro de Recurrentes --}}
      <div class="recur-subtabs" style="display:flex; gap:6px; margin-bottom:20px;">
        <button class="recur-subtab active" onclick="switchRecurSubtab('suscripciones', this)">
          Suscripciones
          <span class="my-tab-count">{{ $recurringSubscriptions->count() }}</span>
        </button>
        <button class="recur-subtab" onclick="switchRecurSubtab('paquetes', this)">
          Pago único
          <span class="my-tab-count">{{ $batches->count() }}</span>
        </button>
      </div>

      {{-- ── Sub-panel: Suscripciones ── --}}
      <div id="recur-sub-suscripciones" class="recur-subpanel">
        @if($recurringSubscriptions->isEmpty())
          <div class="page-card" style="text-align:center; padding:40px 20px;">
            <div style="font-size:28px; margin-bottom:10px;">📅</div>
            <p style="font-weight:700; font-size:15px; margin:0 0 6px;">No tenés suscripciones activas</p>
            <p class="muted" style="margin:0 0 16px; font-size:13px; max-width:300px; margin-left:auto; margin-right:auto; line-height:1.5;">
              Con una suscripción reservás tu turno fijo semanal o quincenal de forma automática.
            </p>
            <a href="{{ route('venues.index') }}" style="display:inline-flex; align-items:center; gap:5px; font-size:13px; font-weight:700; color:#16a34a; text-decoration:none;">
              Explorar complejos con turnos recurrentes →
            </a>
          </div>
        @else
          <div style="display:grid; gap:16px;">
            @foreach($recurringSubscriptions as $sub)
              @php
                $badgeStyle = match($sub->status) {
                  'ACTIVE'          => 'background:rgba(34,197,94,.1); color:#6ee7a0;',
                  'PENDING_PAYMENT' => 'background:rgba(245,158,11,.08); color:#fbbf24;',
                  'FAILED'          => 'background:rgba(229,57,53,.1); color:#f87171;',
                  default           => 'background:rgba(255,255,255,.04); color:#666;',
                };
              @endphp
              <div class="batch-card">
                <div class="batch-card-header">
                  <div>
                    <h3 class="batch-card-title">{{ $sub->field->name }}</h3>
                    <div class="batch-card-meta">
                      {{ $sub->field->venue->name }}
                    </div>
                    <div class="batch-card-meta" style="margin-top:4px;">
                      Turno: {{ $sub->dayLabel() }} {{ \Carbon\Carbon::parse($sub->start_time)->format('H:i') }} &middot; {{ $sub->frequencyLabel() }}
                    </div>
                    <div class="batch-card-meta" style="margin-top:2px;">
                      Monto mensual: <strong style="color:#e8e8e8;">${{ number_format($sub->monthly_amount, 0, ',', '.') }} {{ $sub->currency }}</strong>
                    </div>
                  </div>
                  <span style="padding:6px 14px; border-radius:999px; font-weight:700; font-size:13px; {{ $badgeStyle }} flex-shrink:0;">
                    {{ $sub->statusLabel() }}
                  </span>
                </div>

                @php
                  $subReservations = $sub->reservations
                      ->where('status', '!=', 'CANCELLED')
                      ->where('start_at', '>=', now()->subDay());
                @endphp

                @if($subReservations->isNotEmpty())
                  <div class="batch-slots">
                    <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#666; padding:10px 0 6px;">
                      Turnos reservados
                    </div>
                    @foreach($subReservations->take(8) as $slot)
                      <div class="batch-slot">
                        <div class="batch-slot-date">
                          {{ $slot->start_at->locale('es')->isoFormat('ddd DD/MM') }}
                        </div>
                        <div class="batch-slot-time">
                          {{ $slot->start_at->format('H:i') }} – {{ $slot->end_at->format('H:i') }}
                        </div>
                        <span style="padding:3px 10px; border-radius:999px; font-size:12px; font-weight:700; {{ ReservationStatus::color($slot->status) }}">
                          {{ ReservationStatus::label($slot->status) }}
                        </span>
                        @if($slot->verification_code && $slot->status === 'PAID')
                          <span class="batch-slot-code">{{ $slot->verification_code }}</span>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @elseif($sub->status === 'ACTIVE')
                  @php $proximas = $sub->nextOccurrences(4); @endphp
                  <div class="batch-slots">
                    <div style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#666; padding:10px 0 6px;">
                      Próximos turnos (pendientes de generación)
                    </div>
                    @foreach($proximas as $fecha)
                      <div class="batch-slot">
                        <div class="batch-slot-date">
                          {{ $fecha->locale('es')->isoFormat('ddd DD/MM') }}
                        </div>
                        <div class="batch-slot-time">
                          {{ $fecha->format('H:i') }}
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif

                @if(in_array($sub->status, ['ACTIVE', 'PENDING_PAYMENT']))
                  <div class="batch-card-footer">
                    <div></div>
                    <form method="POST" action="{{ route('recurring.subscription.cancel', $sub) }}" style="margin:0;"
                          onsubmit="return confirm('¿Cancelar esta suscripción? Se dejarán de generar reservas automáticamente.')">
                      @csrf
                      <button type="submit" class="btn">Cancelar suscripción</button>
                    </form>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- ── Sub-panel: Paquetes pago único ── --}}
      <div id="recur-sub-paquetes" class="recur-subpanel" style="display:none;">
        @if($batches->isEmpty())
          <div class="page-card" style="text-align:center; padding:40px 20px;">
            <div style="font-size:28px; margin-bottom:10px;">📦</div>
            <p style="font-weight:700; font-size:15px; margin:0 0 6px;">No tenés paquetes de pago único</p>
            <p class="muted" style="margin:0 0 16px; font-size:13px; max-width:300px; margin-left:auto; margin-right:auto; line-height:1.5;">
              Algunos complejos ofrecen paquetes de varios turnos con descuento. Pagás todo junto y listo.
            </p>
            <a href="{{ route('venues.index') }}" style="display:inline-flex; align-items:center; gap:5px; font-size:13px; font-weight:700; color:#16a34a; text-decoration:none;">
              Buscar complejos →
            </a>
          </div>
        @else
          @foreach($batches as $batch)
            @php
              $batchReservations = $batch->reservations;
              $firstSlot  = $batchReservations->first();
              $lastSlot   = $batchReservations->last();
              $paid       = $batchReservations->where('status', 'PAID')->count();
              $total      = $batchReservations->count();
              $hasPending = $batchReservations->where('status', 'PENDING_PAYMENT')
                              ->filter(fn($r) => !$r->expires_at || $r->expires_at->isFuture())
                              ->isNotEmpty();

              if ($paid > 0) {
                  $effectiveStatus = 'PAID';
              } elseif ($hasPending) {
                  $effectiveStatus = 'PENDING_PAYMENT';
              } else {
                  $effectiveStatus = 'CANCELLED';
              }
            @endphp

            <div class="batch-card">
              <div class="batch-card-header">
                <div>
                  <h3 class="batch-card-title">{{ $batch->field->name }}</h3>
                  <div class="batch-card-meta">
                    {{ $batch->field->venue->name }}
                    @if($firstSlot && $lastSlot)
                      &nbsp;·&nbsp;
                      {{ $firstSlot->start_at->format('d/m/Y') }}
                      →
                      {{ $lastSlot->start_at->format('d/m/Y') }}
                    @endif
                  </div>

                  @if($batch->discount_percentage > 0)
                    <span class="batch-discount-badge">
                      {{ number_format($batch->discount_percentage, 0) }}% de descuento aplicado
                    </span>
                  @endif
                </div>

                <span style="padding:6px 12px; border-radius:999px; font-weight:700; font-size:13px; {{ ReservationStatus::color($effectiveStatus) }}">
                  {{ ReservationStatus::label($effectiveStatus) }}
                </span>
              </div>

              <div class="batch-slots">
                @foreach($batchReservations as $slot)
                  <div class="batch-slot">
                    <div class="batch-slot-date">
                      {{ $slot->start_at->locale('es')->isoFormat('ddd D MMM YYYY') }}
                    </div>
                    <div class="batch-slot-time">
                      {{ $slot->start_at->format('H:i') }} – {{ $slot->end_at->format('H:i') }}
                    </div>
                    <div style="flex:1; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                      <span style="padding:3px 10px; border-radius:999px; font-size:12px; font-weight:700; {{ ReservationStatus::color($slot->status) }}">
                        {{ ReservationStatus::label($slot->status) }}
                      </span>

                      @if($slot->verification_code && $slot->status === 'PAID')
                        <span class="batch-slot-code">{{ $slot->verification_code }}</span>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="batch-card-footer">
                <div class="batch-total">
                  @if($batch->discount_percentage > 0)
                    <span style="color:#a0a0a0; font-size:13px; text-decoration:line-through; margin-right:6px;">
                      {{ $batch->currency }} {{ number_format($batch->subtotal, 0, ',', '.') }}
                    </span>
                  @endif
                  <strong>{{ $batch->currency }} {{ number_format($batch->total_amount, 0, ',', '.') }}</strong>
                  <span style="color:#a0a0a0; font-size:13px;"> · {{ $paid }}/{{ $total }} turnos confirmados</span>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                  @if($effectiveStatus === 'PENDING_PAYMENT')
                    <a href="{{ route('batches.checkout', $batch) }}" class="btn btn-primary">Pagar paquete</a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>
    @endif
  </div>


  {{-- ── Panel: Falta Uno ────────────────────────────────── --}}
  <div id="panel-faltauno" class="tab-panel">
    {{-- Link a perfil público --}}
    @if(auth()->user()->faltaUnoSportProfiles()->exists())
      <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
        <a href="{{ route('sport-profile.public', auth()->user()) }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#16a34a; text-decoration:none; padding:6px 14px; border:1.5px solid rgba(34,197,94,.2); border-radius:999px; background:rgba(34,197,94,.1); transition:all .15s;" onmouseover="this.style.background='rgba(34,197,94,.15)'" onmouseout="this.style.background='rgba(34,197,94,.1)'">
          <i data-lucide="user" style="width:14px;height:14px;stroke:currentColor;"></i> Ver mi perfil de jugador
        </a>
      </div>
    @endif
    @if($misPartidos->isEmpty())
      <div class="page-card" style="text-align:center; padding:36px 24px;">
        <div style="margin-bottom:10px;"><i data-lucide="zap" style="width:36px;height:36px;stroke:#22c55e;stroke-width:1.5;"></i></div>
        <h3 style="margin:0 0 8px;">No participaste en ningún partido Falta Uno</h3>
        <p class="muted" style="margin-bottom:14px;">Unite a un partido o creá el tuyo.</p>
        <a href="{{ route('falta-uno.index') }}" class="btn btn-primary">Ver partidos Falta Uno</a>
      </div>
    @else
      @php
        $proximosPartidos = $misPartidos->filter(fn($g) => $g->start_at->isFuture() && in_array($g->status, ['open', 'full']))
            ->sortByDesc(fn($g) => $g->reservation?->status === 'PENDING_PAYMENT' ? 1 : 0);
        $canceladosRecientes = $misPartidos->filter(fn($g) => in_array($g->status, ['cancelled', 'expired']) && $g->cancelled_at && $g->cancelled_at->isAfter(now()->subDays(3)));
        $partidosPasados  = $misPartidos->filter(fn($g) => $g->start_at->isPast() && !in_array($g->status, ['cancelled', 'expired']));
        $userId = auth()->id();
      @endphp
      @if($proximosPartidos->isNotEmpty())
        <h3 style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#666; margin:0 0 10px;">Próximos</h3>
        <div style="display:grid; gap:10px; margin-bottom:20px;">
          @foreach($proximosPartidos as $pg)
          @php
            $esIniciador = $pg->initiator_user_id === $userId;
            $sportLabel  = match($pg->field->sport ?? '') {
              'football'   => '⚽ Fútbol', 'padel' => '🏓 Pádel',
              'tennis'     => '🎾 Tenis',  'basketball' => '🏀 Básquet',
              'volleyball' => '🏐 Vóley',  default => ucfirst($pg->field->sport ?? ''),
            };
          @endphp
          <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; background:#111; border:1px solid rgba(255,255,255,.08); border-radius:12px; flex-wrap:wrap; box-shadow:0 2px 6px rgba(0,0,0,.15);">
            <div style="font-size:22px;">{{ explode(' ', $sportLabel)[0] }}</div>
            <div style="flex:1; min-width:140px;">
              <div style="font-weight:700; font-size:14px;">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</div>
              <div style="font-size:12px; color:#666; margin-top:2px;">
                {{ $pg->start_at->format('d/m/Y H:i') }} hs
                @if($esIniciador) · <span style="color:#2563eb;">Organizador</span> @endif
              </div>
            </div>
            @if($pg->reservation && $pg->reservation->status === 'PENDING_PAYMENT')
              <span style="font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px; background:rgba(229,57,53,.1); color:#f87171;">
                Pendiente de pago
              </span>
              <a href="{{ route('reservations.checkout', $pg->reservation) }}"
                 style="font-size:12px; padding:5px 14px; background:#22c55e; color:#050505; border-radius:8px; text-decoration:none; font-weight:700; white-space:nowrap;">
                Pagar
              </a>
            @else
              <span style="font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px;
                           background:{{ $pg->status === 'full' ? 'rgba(34,197,94,.1)' : 'rgba(245,158,11,.08)' }};
                           color:{{ $pg->status === 'full' ? '#6ee7a0' : '#fbbf24' }};">
                {{ $pg->status === 'full' ? 'Completo' : 'Buscando jugadores' }}
              </span>
              <a href="{{ route('falta-uno.show', $pg) }}"
                 style="font-size:12px; padding:5px 14px; border:1.5px solid rgba(255,255,255,.08); border-radius:8px; color:#a0a0a0; text-decoration:none; font-weight:600; white-space:nowrap;">
                Ver partido
              </a>
            @endif
          </div>
          @endforeach
        </div>
      @endif
      @if($canceladosRecientes->isNotEmpty())
        <h3 style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#666; margin:0 0 10px;">Cancelados</h3>
        <div style="display:grid; gap:10px; margin-bottom:20px;">
          @foreach($canceladosRecientes as $pg)
          @php
            $sportLabel = match($pg->field->sport ?? '') {
              'football'   => '⚽ Fútbol', 'padel' => '🏓 Pádel',
              'tennis'     => '🎾 Tenis',  'basketball' => '🏀 Básquet',
              'volleyball' => '🏐 Vóley',  default => ucfirst($pg->field->sport ?? ''),
            };
          @endphp
          <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; background:rgba(255,255,255,.02); border:1px solid rgba(255,255,255,.06); border-radius:12px; flex-wrap:wrap; opacity:.7;">
            <div style="font-size:22px; opacity:.5;">{{ explode(' ', $sportLabel)[0] }}</div>
            <div style="flex:1; min-width:140px;">
              <div style="font-weight:700; font-size:14px; color:#666;">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</div>
              <div style="font-size:12px; color:#666; margin-top:2px;">
                {{ $pg->start_at->format('d/m/Y H:i') }} hs
              </div>
            </div>
            <span style="font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px; background:rgba(255,255,255,.04); color:#666;">
              {{ $pg->status === 'expired' ? 'Expirado' : 'Cancelado' }}
            </span>
          </div>
          @endforeach
        </div>
      @endif
      @if($partidosPasados->isNotEmpty())
        <h3 style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#666; margin:0 0 10px;">Historial</h3>
        <div style="display:grid; gap:10px;">
          @foreach($partidosPasados as $pg)
          @php
            $esIniciador = $pg->initiator_user_id === $userId;
            $sportLabel  = match($pg->field->sport ?? '') {
              'football'   => '⚽ Fútbol', 'padel' => '🏓 Pádel',
              'tennis'     => '🎾 Tenis',  'basketball' => '🏀 Básquet',
              'volleyball' => '🏐 Vóley',  default => ucfirst($pg->field->sport ?? ''),
            };
            $yaCalifico = App\Models\FaltaUnoRating::where('game_id', $pg->id)
              ->where('rater_user_id', $userId)->exists();
            $myParticipation = $pg->participants->first();
            $myResult = $myParticipation?->result;
            $myGoals = $myParticipation?->goals;
            $myAssists = $myParticipation?->assists;
            $resultStyle = match($myResult) {
              'win'  => ['bg' => 'rgba(34,197,94,.1)', 'color' => '#6ee7a0', 'border' => 'rgba(34,197,94,.2)', 'label' => 'Victoria', 'icon' => 'W'],
              'draw' => ['bg' => 'rgba(255,255,255,.04)', 'color' => '#a0a0a0', 'border' => 'rgba(255,255,255,.08)', 'label' => 'Empate', 'icon' => 'E'],
              'loss' => ['bg' => 'rgba(229,57,53,.1)', 'color' => '#f87171', 'border' => 'rgba(229,57,53,.2)', 'label' => 'Derrota', 'icon' => 'D'],
              default => null,
            };
          @endphp
          <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; background:#111; border:1px solid {{ $resultStyle ? $resultStyle['border'] : 'rgba(255,255,255,.08)' }}; border-left:4px solid {{ $resultStyle ? $resultStyle['color'] : 'rgba(255,255,255,.08)' }}; border-radius:12px; flex-wrap:wrap; box-shadow:0 2px 6px rgba(0,0,0,.15);">
            <div style="font-size:22px; opacity:.6;">{{ explode(' ', $sportLabel)[0] }}</div>
            <div style="flex:1; min-width:140px;">
              <div style="font-weight:700; font-size:14px; color:#e8e8e8;">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</div>
              <div style="font-size:12px; color:#666; margin-top:2px;">
                {{ $pg->start_at->format('d/m/Y H:i') }} hs
                @if($esIniciador) · <span style="color:#666;">Organizador</span> @endif
              </div>
              @if($myGoals !== null || $myAssists !== null)
                <div style="font-size:11px; color:#666; margin-top:3px;">
                  @if($myGoals !== null) {{ $myGoals }} gol{{ $myGoals !== 1 ? 'es' : '' }} @endif
                  @if($myGoals !== null && $myAssists !== null) · @endif
                  @if($myAssists !== null) {{ $myAssists }} asist{{ $myAssists !== 1 ? 'encias' : 'encia' }} @endif
                </div>
              @endif
            </div>
            @if($resultStyle)
              <span style="font-size:12px; font-weight:800; padding:4px 12px; border-radius:999px; background:{{ $resultStyle['bg'] }}; color:{{ $resultStyle['color'] }}; white-space:nowrap;">
                {{ $resultStyle['label'] }}
              </span>
            @else
              <span style="font-size:11px; font-weight:600; padding:4px 10px; border-radius:999px; background:rgba(255,255,255,.04); color:#666; white-space:nowrap;">
                Sin resultado
              </span>
            @endif
            <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
              @if(!$yaCalifico)
                <a href="{{ route('falta-uno.rate', $pg) }}"
                   style="font-size:12px; padding:5px 12px; background:rgba(245,158,11,.08); border-radius:8px; color:#fbbf24; text-decoration:none; font-weight:700; white-space:nowrap;">
                  ★ Calificar
                </a>
              @endif
              <a href="{{ route('falta-uno.show', $pg) }}"
                 style="font-size:12px; padding:5px 12px; background:rgba(255,255,255,.04); border-radius:8px; color:#a0a0a0; text-decoration:none; font-weight:600; white-space:nowrap;">
                Ver partido
              </a>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    @endif

    <div style="text-align:center; margin-top:20px;">
      <a href="{{ route('falta-uno.index') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:14px; font-weight:600; color:#16a34a; text-decoration:none;" onmouseover="this.style.color='#15803d'" onmouseout="this.style.color='#16a34a'">
        <i data-lucide="zap" style="width:15px;height:15px;stroke:currentColor;"></i> Ver todos los partidos Falta Uno
      </a>
    </div>
  </div>

  <script>
    function switchTab(name, btn) {
      document.querySelectorAll('.my-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('panel-' + name).classList.add('active');
    }

    // Activar tab desde query param ?tab=
    (function() {
      const params = new URLSearchParams(window.location.search);
      const tab = params.get('tab');
      if (tab) {
        const panel = document.getElementById('panel-' + tab);
        const btns = document.querySelectorAll('.my-tab');
        if (panel) {
          document.querySelectorAll('.my-tab').forEach(t => t.classList.remove('active'));
          document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
          panel.classList.add('active');
          btns.forEach(function(b) {
            if (b.getAttribute('onclick') && b.getAttribute('onclick').includes("'" + tab + "'")) {
              b.classList.add('active');
            }
          });
        }
      }
    })();

    function switchRecurSubtab(name, btn) {
      document.querySelectorAll('.recur-subtab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.recur-subpanel').forEach(p => p.style.display = 'none');
      btn.classList.add('active');
      document.getElementById('recur-sub-' + name).style.display = 'block';
    }

    function markReservationAsExpired(reservationId, fieldUrl) {
      const payWrap = document.getElementById(`pay-btn-wrap-${reservationId}`);
      const meta    = document.getElementById(`reservation-meta-${reservationId}`);
      const badge   = document.getElementById(`status-badge-${reservationId}`);
      const actions = document.getElementById(`actions-wrap-${reservationId}`);

      if (payWrap) payWrap.style.display = 'none';

      if (meta) {
        meta.innerHTML = `<div style="color:#f87171; font-weight:700;">Reserva vencida por falta de pago.</div>`;
      }

      if (badge) {
        badge.textContent = 'Expirada';
        badge.style.background = 'rgba(229,57,53,.1)';
        badge.style.color = '#f87171';
        badge.style.border = '1px solid rgba(229,57,53,.2)';
      }

      if (actions && !document.getElementById(`back-to-field-${reservationId}`)) {
        actions.insertAdjacentHTML('beforeend', `
          <a href="${fieldUrl}" class="btn btn-primary" id="back-to-field-${reservationId}">
            Volver a la cancha
          </a>
        `);
      }
    }

    function startCountdown(elementId, expiresAt, payWrapId, reservationId, fieldUrl) {
      const el      = document.getElementById(elementId);
      const payWrap = document.getElementById(payWrapId);
      if (!el) return;

      function tick() {
        const diff = new Date(expiresAt).getTime() - Date.now();

        if (diff <= 0) {
          el.innerText = 'La reserva expiró';
          if (payWrap) payWrap.style.display = 'none';
          markReservationAsExpired(reservationId, fieldUrl);
          return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
        const seconds = String(totalSeconds % 60).padStart(2, '0');
        el.innerText = `Te quedan ${minutes}:${seconds} para pagar`;
      }

      tick();
      const interval = setInterval(() => {
        if (new Date(expiresAt).getTime() - Date.now() <= 0) clearInterval(interval);
        tick();
      }, 1000);
    }
  </script>

  @foreach($reservations as $r)
    @if($r->status === 'PENDING_PAYMENT' && $r->expires_at && $r->expires_at->isFuture())
      <script>
        startCountdown(
          'countdown-{{ $r->id }}',
          '{{ $r->expires_at->toIso8601String() }}',
          'pay-btn-wrap-{{ $r->id }}',
          {{ $r->id }},
          '{{ route('fields.show', $r->field) }}'
        );
      </script>
    @endif
  @endforeach

@endsection
