@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.app')

@section('title', 'Mis reservas')

@push('styles')
  /* ── Tabs ─────────────────────────────────────────── */
  .my-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
  }

  .my-tab {
    padding: 10px 22px;
    border-radius: 999px;
    border: 1px solid #e0e0e0;
    background: #fff;
    font-size: 14px;
    font-weight: 700;
    color: #555;
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s;
    font-family: inherit;
  }

  .my-tab.active {
    background: #111;
    color: #fff;
    border-color: #111;
  }

  .my-tab-count {
    display: inline-block;
    margin-left: 6px;
    background: rgba(0,0,0,.12);
    border-radius: 999px;
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
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    overflow: hidden;
    margin-bottom: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
  }

  .batch-card-header {
    padding: 20px 22px 16px 22px;
    border-bottom: 1px solid #f0f0f0;
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
    color: #666;
  }

  .batch-discount-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    background: #e8f7ee;
    color: #157347;
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
    border-bottom: 1px solid #f5f5f5;
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
    color: #666;
    min-width: 100px;
  }

  .batch-slot-code {
    font-size: 15px;
    font-weight: 800;
    letter-spacing: 0.06em;
    background: #f7f7f8;
    border: 1px solid #e8e8e8;
    border-radius: 8px;
    padding: 3px 10px;
    color: #111;
  }

  .batch-card-footer {
    padding: 14px 22px;
    background: #fafafa;
    border-top: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }

  .batch-total {
    font-size: 14px;
    color: #444;
  }

  .batch-total strong {
    font-size: 18px;
    font-weight: 800;
    color: #111;
  }
@endpush

@section('content')
  <div class="page-card" style="margin-bottom:22px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
      <div>
        <h1 style="margin:0 0 8px 0; font-size:34px; letter-spacing:-0.02em;">Mis reservas</h1>
        <p class="muted" style="margin:0;">
          Consultá el estado de tus turnos, pagá reservas pendientes o cancelalas si todavía corresponde.
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
      <span class="my-tab-count">{{ $batches->count() }}</span>
    </button>
    <button class="my-tab" onclick="switchTab('historial', this)">
      Historial
    </button>
    <button class="my-tab" onclick="switchTab('faltauno', this)">
      ⚡ Falta Uno
      <span class="my-tab-count">{{ $misPartidos->count() }}</span>
    </button>
  </div>

  {{-- ── Panel: turnos individuales ────────────────────────────────── --}}
  <div id="panel-individual" class="tab-panel active">
    @if($reservations->isEmpty())
      <div class="page-card">
        <h3 style="margin-top:0;">No tenés turnos individuales</h3>
        <p class="muted" style="margin-bottom:14px;">
          Cuando reserves una cancha, la vas a ver acá con su estado y acceso al pago.
        </p>
        <a href="{{ route('venues.index') }}" class="btn btn-primary">Ver complejos</a>
      </div>
    @else
      <div class="grid" style="grid-template-columns:repeat(auto-fit, minmax(min(340px, 100%), 1fr));">
        @foreach($reservations as $r)
          <article class="venue-card" style="border-radius:18px;">
            <div class="venue-card-body" style="padding:20px;">
              <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start; flex-wrap:wrap;">
                <div>
                  <div style="font-size:12px; color:#666; margin-bottom:6px;">
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
                  <div style="font-size:12px; color:#666; margin-bottom:4px;">Código de verificación</div>
                  <div style="font-size:24px; font-weight:800; letter-spacing:0.06em;">
                    {{ $r->verification_code }}
                  </div>
                </div>
              @endif

              <div id="reservation-meta-{{ $r->id }}" style="margin-top:12px; font-size:13px;">
                @if($r->status === 'PENDING_PAYMENT' && (!$r->expires_at || $r->expires_at->isFuture()))
                  <div style="color:#856404;">
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
                @elseif($r->status === 'EXPIRED')
                  <div style="color:#842029; font-weight:700;">Reserva vencida por falta de pago.</div>
                @elseif($r->status === 'CANCELLED')
                  <div style="color:#842029; font-weight:700;">Reserva cancelada.</div>
                @elseif($r->status === 'PAID')
                  <div style="color:#157347; font-weight:700;">Reserva pagada correctamente.</div>
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

                @if(in_array($r->status, ['PENDING_PAYMENT','PAID']))
                  @php
                    $cancellationHours = $r->field->venue->cancellation_hours ?? null;
                    $canCancel = $r->status === 'PENDING_PAYMENT'
                        || $cancellationHours === null
                        || now()->isBefore($r->start_at->copy()->subHours($cancellationHours));
                  @endphp

                  @if($canCancel)
                    <form method="POST" action="{{ route('reservations.cancel', $r) }}" style="margin:0;">
                      @csrf
                      <button type="submit" class="btn">Cancelar</button>
                    </form>
                  @else
                    <span class="badge" style="background:#fff3cd; color:#856404; border-color:#ffeaa7; font-size:12px;">
                      Cancelación no disponible — venció el plazo de {{ $cancellationHours }}h
                    </span>
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
  </div>

  {{-- ── Panel: reservas recurrentes ────────────────────────────────── --}}
  <div id="panel-recurrentes" class="tab-panel">
    @if($batches->isEmpty())
      <div class="page-card">
        <h3 style="margin-top:0;">No tenés reservas recurrentes</h3>
        <p class="muted" style="margin-bottom:14px;">
          Cuando reserves un paquete de turnos recurrentes (semanal o quincenal), aparecen acá agrupados.
        </p>
        <a href="{{ route('venues.index') }}" class="btn btn-primary">Ver complejos</a>
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

          // Derive effective status from actual reservations
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

          {{-- Slots list --}}
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
                <span style="color:#666; font-size:13px; text-decoration:line-through; margin-right:6px;">
                  {{ $batch->currency }} {{ number_format($batch->subtotal, 0, ',', '.') }}
                </span>
              @endif
              <strong>{{ $batch->currency }} {{ number_format($batch->total_amount, 0, ',', '.') }}</strong>
              <span style="color:#666; font-size:13px;"> · {{ $paid }}/{{ $total }} turnos confirmados</span>
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

  {{-- ── Panel: historial ────────────────────────────────── --}}
  <div id="panel-historial" class="tab-panel">
    <div class="page-card" style="padding:20px;">
      <p class="muted" style="margin:0 0 12px;">Tu historial completo de partidos y reservas pasadas.</p>
      <a href="{{ route('match_history') }}" class="btn btn-primary">Ver historial de partidos →</a>
    </div>
  </div>

  {{-- ── Panel: Falta Uno ────────────────────────────────── --}}
  <div id="panel-faltauno" class="tab-panel">
    @if($misPartidos->isEmpty())
      <div class="page-card" style="text-align:center; padding:36px 24px;">
        <div style="font-size:36px; margin-bottom:10px;">⚡</div>
        <h3 style="margin:0 0 8px;">No participaste en ningún partido Falta Uno</h3>
        <p class="muted" style="margin-bottom:14px;">Unite a un partido o creá el tuyo.</p>
        <a href="{{ route('falta-uno.index') }}" class="btn btn-primary">Ver partidos Falta Uno</a>
      </div>
    @else
      @php
        $proximosPartidos = $misPartidos->filter(fn($g) => $g->start_at->isFuture());
        $partidosPasados  = $misPartidos->filter(fn($g) => $g->start_at->isPast());
        $userId = auth()->id();
      @endphp
      @if($proximosPartidos->isNotEmpty())
        <h3 style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#aaa; margin:0 0 10px;">Próximos</h3>
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
          <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; background:#fff; border:1px solid #ececec; border-radius:12px; flex-wrap:wrap; box-shadow:0 2px 6px rgba(0,0,0,.03);">
            <div style="font-size:22px;">{{ explode(' ', $sportLabel)[0] }}</div>
            <div style="flex:1; min-width:140px;">
              <div style="font-weight:700; font-size:14px;">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</div>
              <div style="font-size:12px; color:#888; margin-top:2px;">
                {{ $pg->start_at->format('d/m/Y H:i') }} hs
                @if($esIniciador) · <span style="color:#2563eb;">Organizador</span> @endif
              </div>
            </div>
            <span style="font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px;
                         background:{{ $pg->status === 'full' ? '#dcfce7' : '#fef9c3' }};
                         color:{{ $pg->status === 'full' ? '#15803d' : '#854d0e' }};">
              {{ $pg->status === 'full' ? 'Completo' : 'Buscando jugadores' }}
            </span>
            <a href="{{ route('falta-uno.show', $pg) }}"
               style="font-size:12px; padding:5px 14px; border:1.5px solid #e0e0e0; border-radius:8px; color:#333; text-decoration:none; font-weight:600; white-space:nowrap;">
              Ver partido
            </a>
          </div>
          @endforeach
        </div>
      @endif
      @if($partidosPasados->isNotEmpty())
        <h3 style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#aaa; margin:0 0 10px;">Historial</h3>
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
          @endphp
          <div style="display:flex; align-items:center; gap:14px; padding:12px 16px; background:#fff; border:1px solid #ececec; border-radius:12px; flex-wrap:wrap; box-shadow:0 2px 6px rgba(0,0,0,.03);">
            <div style="font-size:22px; opacity:.6;">{{ explode(' ', $sportLabel)[0] }}</div>
            <div style="flex:1; min-width:140px;">
              <div style="font-weight:700; font-size:14px; color:#444;">{{ $pg->field->name }} · {{ $pg->field->venue->name }}</div>
              <div style="font-size:12px; color:#aaa; margin-top:2px;">
                {{ $pg->start_at->format('d/m/Y H:i') }} hs
                @if($esIniciador) · <span style="color:#888;">Organizador</span> @endif
              </div>
            </div>
            <div style="display:flex; gap:6px; flex-wrap:wrap; align-items:center;">
              @if(!$yaCalifico)
                <a href="{{ route('falta-uno.rate', $pg) }}"
                   style="font-size:12px; padding:5px 12px; background:#fef3c7; border-radius:8px; color:#92400e; text-decoration:none; font-weight:700; white-space:nowrap;">
                  ★ Calificar
                </a>
              @endif
              <a href="{{ route('falta-uno.show', $pg) }}"
                 style="font-size:12px; padding:5px 12px; background:#f4f4f4; border-radius:8px; color:#555; text-decoration:none; font-weight:600; white-space:nowrap;">
                Ver partido
              </a>
            </div>
          </div>
          @endforeach
        </div>
      @endif
    @endif
  </div>

  <script>
    function switchTab(name, btn) {
      document.querySelectorAll('.my-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('panel-' + name).classList.add('active');
    }

    function markReservationAsExpired(reservationId, fieldUrl) {
      const payWrap = document.getElementById(`pay-btn-wrap-${reservationId}`);
      const meta    = document.getElementById(`reservation-meta-${reservationId}`);
      const badge   = document.getElementById(`status-badge-${reservationId}`);
      const actions = document.getElementById(`actions-wrap-${reservationId}`);

      if (payWrap) payWrap.style.display = 'none';

      if (meta) {
        meta.innerHTML = `<div style="color:#842029; font-weight:700;">Reserva vencida por falta de pago.</div>`;
      }

      if (badge) {
        badge.textContent = 'Expirada';
        badge.style.background = '#f8d7da';
        badge.style.color = '#842029';
        badge.style.border = '1px solid #f1b9c0';
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
