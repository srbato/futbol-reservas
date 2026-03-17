@php use App\Support\ReservationStatus; @endphp

@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Resumen de tus complejos y reservas')

@push('styles')
<style>
  /* ── Tabs ──────────────────────────────────────── */
  .dash-tabs {
    display: flex;
    gap: 4px;
    background: #f3f3f3;
    border-radius: 14px;
    padding: 4px;
    margin-bottom: 24px;
    width: fit-content;
    flex-wrap: wrap;
  }

  .dash-tab {
    padding: 9px 20px;
    border-radius: 10px;
    border: none;
    background: none;
    font-size: 13px;
    font-weight: 700;
    color: #888;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, color .15s;
    white-space: nowrap;
  }

  .dash-tab:hover { background: rgba(0,0,0,.06); color: #333; }
  .dash-tab.active { background: #111; color: #fff; }

  .tab-pane { display: none; }
  .tab-pane.active { display: block; }

  /* ── KPI cards ─────────────────────────────────── */
  .kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
  }

  .kpi-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    padding: 20px 22px;
    box-shadow: 0 2px 10px rgba(0,0,0,.03);
    position: relative;
    overflow: hidden;
  }

  .kpi-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: #111;
    border-radius: 18px 18px 0 0;
  }

  .kpi-card.green::before  { background: #4ade80; }
  .kpi-card.blue::before   { background: #60a5fa; }
  .kpi-card.purple::before { background: #a78bfa; }
  .kpi-card.orange::before { background: #fb923c; }

  .kpi-icon {
    font-size: 22px;
    margin-bottom: 10px;
    display: block;
  }

  .kpi-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #999;
    margin-bottom: 6px;
  }

  .kpi-value {
    font-size: 32px;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1;
    color: #111;
  }

  .kpi-sub {
    font-size: 12px;
    color: #aaa;
    margin-top: 5px;
    font-weight: 600;
  }

  /* ── Content cards ─────────────────────────────── */
  .dash-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    padding: 22px 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,.03);
  }

  .dash-card-title {
    margin: 0 0 16px 0;
    font-size: 15px;
    font-weight: 800;
    color: #111;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .dash-card-title span { font-size: 18px; }

  /* ── Two-col ────────────────────────────────────── */
  .dash-two-col {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 16px;
    align-items: start;
  }

  /* ── Reservation row ────────────────────────────── */
  .res-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border: 1px solid #f0f0f0;
    border-radius: 14px;
    background: #fafafa;
    margin-bottom: 10px;
    transition: border-color .15s;
  }

  .res-row:last-child { margin-bottom: 0; }
  .res-row:hover { border-color: #ddd; background: #fff; }

  .res-row-main { flex: 1; min-width: 0; }

  .res-field-name {
    font-weight: 700;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .res-meta {
    font-size: 12px;
    color: #888;
    margin-top: 3px;
  }

  .res-time {
    font-size: 14px;
    font-weight: 800;
    color: #111;
    white-space: nowrap;
  }

  /* ── Status badge ───────────────────────────────── */
  .status-pill {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
  }

  /* ── Venue block ────────────────────────────────── */
  .venue-block {
    border: 1px solid #ececec;
    border-radius: 18px;
    overflow: hidden;
    margin-bottom: 16px;
    background: #fff;
  }

  .venue-block:last-child { margin-bottom: 0; }

  .venue-block-header {
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
  }

  .venue-block-name {
    font-size: 16px;
    font-weight: 800;
    color: #111;
  }

  .venue-block-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
  }

  .vba-link {
    font-size: 13px;
    font-weight: 700;
    color: #444;
    text-decoration: none;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    background: #fff;
    transition: background .15s, border-color .15s;
  }

  .vba-link:hover { background: #f3f3f3; border-color: #bbb; }

  .vba-mp-connected {
    font-size: 12px;
    font-weight: 700;
    color: #157347;
    background: #e8f7ee;
    border: 1px solid #cfe9d7;
    padding: 6px 12px;
    border-radius: 8px;
  }

  .vba-mp-btn {
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    background: #009ee3;
    border: none;
    padding: 6px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    display: inline-block;
  }

  .vba-disconnect-btn {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    background: #fff;
    border: 1px solid #ddd;
    padding: 6px 12px;
    border-radius: 8px;
    cursor: pointer;
    font-family: inherit;
  }

  /* ── Fields list ────────────────────────────────── */
  .fields-list {
    padding: 16px 22px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .field-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid #f0f0f0;
    background: #fafafa;
  }

  .field-row:hover { border-color: #ddd; background: #fff; }

  .field-name {
    font-weight: 700;
    font-size: 14px;
    flex: 1;
    min-width: 100px;
  }

  .field-price {
    font-size: 12px;
    color: #888;
    font-weight: 600;
  }

  .field-inactive-badge {
    font-size: 11px;
    font-weight: 700;
    color: #842029;
    background: #f8d7da;
    border: 1px solid #f1b9c0;
    padding: 2px 8px;
    border-radius: 999px;
  }

  .field-link {
    font-size: 12px;
    font-weight: 700;
    color: #555;
    text-decoration: none;
    padding: 4px 10px;
    border-radius: 7px;
    border: 1px solid #e0e0e0;
    background: #fff;
    transition: background .15s;
  }

  .field-link:hover { background: #f3f3f3; }

  .toggle-active-btn {
    font-size: 12px;
    font-weight: 700;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px 0;
    font-family: inherit;
  }

  .toggle-active-btn.deactivate { color: #c62828; }
  .toggle-active-btn.activate   { color: #157347; }

  /* ── Table ──────────────────────────────────────── */
  .dash-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }

  .dash-table th {
    text-align: left;
    padding: 10px 14px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #999;
    border-bottom: 1px solid #f0f0f0;
    white-space: nowrap;
  }

  .dash-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #f7f7f8;
    vertical-align: middle;
  }

  .dash-table tr:last-child td { border-bottom: none; }
  .dash-table tr:hover td { background: #fafafa; }

  /* ── Top field highlight ────────────────────────── */
  .top-field-card {
    background: linear-gradient(135deg, #111 0%, #1f1f1f 100%);
    border-radius: 16px;
    padding: 22px 24px;
    color: #fff;
    margin-bottom: 16px;
  }

  .top-field-card .label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #4ade80;
    margin-bottom: 10px;
  }

  .top-field-card .name {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 6px;
    letter-spacing: -0.02em;
  }

  .top-field-card .sub {
    font-size: 13px;
    color: rgba(255,255,255,.6);
  }

  /* ── Membership alert ───────────────────────────── */
  .membership-alert {
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
  }

  .membership-alert.warning {
    background: #fff4db;
    border: 1px solid #f5d48a;
    color: #9a6700;
  }

  .membership-alert.danger {
    background: #f8d7da;
    border: 1px solid #f1b9c0;
    color: #842029;
  }

  /* ── Responsive ─────────────────────────────────── */
  @media (max-width: 900px) {
    .dash-two-col { grid-template-columns: 1fr; }
    .kpi-grid { grid-template-columns: repeat(2, 1fr); }
  }

  @media (max-width: 480px) {
    .kpi-grid { grid-template-columns: 1fr 1fr; }
    .kpi-value { font-size: 26px; }
  }
</style>
@endpush

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_dashboard',
  'helpTitle' => 'Panel principal',
  'helpText'  => 'Acá encontrás un resumen de toda tu actividad: reservas del día, ingresos de la semana, próxima reserva y estadísticas generales. Es el punto de partida para tener un pantallazo rápido de cómo va tu complejo.',
])

  {{-- ── Membership alert ──────────────────────────────────────────────── --}}
  @if(auth()->user()->role === 'venue_admin' && $membershipAlert)
    <div class="membership-alert {{ $membershipAlert['tone'] === 'danger' ? 'danger' : 'warning' }}">
      <div>
        <div style="font-weight:800; margin-bottom:4px;">
          {{ $membershipAlert['tone'] === 'danger' ? '⚠️ Tu membresía vence muy pronto' : '🔔 Tu membresía está por vencer' }}
        </div>
        <div style="font-size:14px; line-height:1.5;">
          Vence el <strong>{{ $membershipAlert['expires_at']->format('d/m/Y H:i') }}</strong>
          @if($membershipAlert['days_remaining'] > 1)
            — faltan {{ $membershipAlert['days_remaining'] }} días.
          @elseif($membershipAlert['days_remaining'] === 1)
            — falta 1 día.
          @else
            — vence hoy.
          @endif
        </div>
      </div>
      <a href="{{ route('membership.become') }}" class="btn btn-primary" style="white-space:nowrap; font-size:13px;">
        Renovar membresía
      </a>
    </div>
  @endif

  {{-- ── Tabs ──────────────────────────────────────────────────────────── --}}
  <div class="dash-tabs">
    <button class="dash-tab" data-tab="resumen" onclick="showTab('resumen')">📊 Resumen</button>
    <button class="dash-tab" data-tab="complejos" onclick="showTab('complejos')">🏟️ Complejos</button>
    <button class="dash-tab" data-tab="reservas" onclick="showTab('reservas')">📅 Reservas de hoy</button>
    @if(auth()->user()->role === 'super_admin')
      <button class="dash-tab" data-tab="global" onclick="showTab('global')">🌐 Vista global</button>
    @endif
  </div>

  {{-- ── TAB: RESUMEN ──────────────────────────────────────────────────── --}}
  <div id="tab-resumen" class="tab-pane">

    {{-- KPI row --}}
    <div class="kpi-grid">
      <div class="kpi-card">
        <span class="kpi-icon">🏟️</span>
        <div class="kpi-label">Complejos</div>
        <div class="kpi-value">{{ $venues->count() }}</div>
      </div>

      <div class="kpi-card green">
        <span class="kpi-icon">📅</span>
        <div class="kpi-label">Reservas hoy</div>
        <div class="kpi-value">{{ $stats['reservations_today_count'] }}</div>
      </div>

      @unless(auth()->user()->isVenueStaff())
      <div class="kpi-card blue">
        <span class="kpi-icon">💰</span>
        <div class="kpi-label">Ingresos hoy</div>
        <div class="kpi-value" style="font-size:24px;">$ {{ number_format($revenueToday, 0, ',', '.') }}</div>
      </div>
      @endunless

      <div class="kpi-card purple">
        <span class="kpi-icon">📈</span>
        <div class="kpi-label">Reservas esta semana</div>
        <div class="kpi-value">{{ $reservationsWeek }}</div>
        @php $diffWeekRes = $reservationsWeek - $reservationsWeekPrev; @endphp
        <div class="kpi-sub" style="color: {{ $diffWeekRes >= 0 ? '#157347' : '#842029' }}">
          {{ $diffWeekRes >= 0 ? '+' : '' }}{{ $diffWeekRes }} vs semana anterior
        </div>
      </div>

      @unless(auth()->user()->isVenueStaff())
      <div class="kpi-card orange">
        <span class="kpi-icon">💵</span>
        <div class="kpi-label">Ingresos esta semana</div>
        <div class="kpi-value" style="font-size:22px;">$ {{ number_format($revenueWeek, 0, ',', '.') }}</div>
        @php $diffWeekRev = $revenueWeek - $revenueWeekPrev; @endphp
        <div class="kpi-sub" style="color: {{ $diffWeekRev >= 0 ? '#157347' : '#842029' }}">
          {{ $diffWeekRev >= 0 ? '+' : '' }}$ {{ number_format(abs($diffWeekRev), 0, ',', '.') }} vs semana anterior
        </div>
      </div>
      @endunless

      <div class="kpi-card blue">
        <span class="kpi-icon">🗓️</span>
        <div class="kpi-label">Reservas este mes</div>
        <div class="kpi-value">{{ $reservationsMonthCurrent }}</div>
        @php $diffMonthRes = $reservationsMonthCurrent - $reservationsMonthPrev; @endphp
        <div class="kpi-sub" style="color: {{ $diffMonthRes >= 0 ? '#157347' : '#842029' }}">
          {{ $diffMonthRes >= 0 ? '+' : '' }}{{ $diffMonthRes }} vs mes anterior
        </div>
      </div>

      @unless(auth()->user()->isVenueStaff())
      <div class="kpi-card green">
        <span class="kpi-icon">💰</span>
        <div class="kpi-label">Ingresos este mes</div>
        <div class="kpi-value" style="font-size:22px;">$ {{ number_format($revenueMonth, 0, ',', '.') }}</div>
        @php $diffMonthRev = $revenueMonth - $revenueMonthPrev; @endphp
        <div class="kpi-sub" style="color: {{ $diffMonthRev >= 0 ? '#157347' : '#842029' }}">
          {{ $diffMonthRev >= 0 ? '+' : '' }}$ {{ number_format(abs($diffMonthRev), 0, ',', '.') }} vs mes anterior
        </div>
      </div>
      @endunless
    </div>

    {{-- Two columns: reservas hoy + cancha top + mis complejos --}}
    <div class="dash-two-col">

      {{-- Próximas reservas --}}
      <div class="dash-card">
        <h3 class="dash-card-title"><span>📋</span> Próximas reservas de hoy</h3>

        @if($upcomingToday->isEmpty())
          <div style="padding:24px 0; text-align:center; color:#aaa; font-size:14px;">
            No hay reservas cargadas para hoy.
          </div>
        @else
          @foreach($upcomingToday as $reservation)
            <div class="res-row">
              <div class="res-row-main">
                <div class="res-field-name">
                  {{ $reservation->field->venue->name }} — {{ $reservation->field->name }}
                </div>
                <div class="res-meta">{{ $reservation->user->name ?? '—' }}</div>
              </div>
              <div style="text-align:right; flex-shrink:0;">
                <div class="res-time">
                  {{ $reservation->start_at->format('H:i') }}–{{ $reservation->end_at->format('H:i') }}
                </div>
                <div style="margin-top:5px;">
                  <span class="status-pill" style="{{ ReservationStatus::color($reservation->status) }}">
                    {{ ReservationStatus::label($reservation->status) }}
                  </span>
                </div>
              </div>
            </div>
          @endforeach
        @endif
      </div>

      {{-- Right column --}}
      <div style="display:flex; flex-direction:column; gap:16px;">

        {{-- Top cancha --}}
        @if($topField)
          <div class="top-field-card">
            <div class="label">🔥 Cancha más reservada esta semana</div>
            <div class="name">{{ $topField->field->name }}</div>
            <div class="sub">
              {{ $topField->field->venue->name }} ·
              {{ $topField->reservations_count }} reserva{{ $topField->reservations_count > 1 ? 's' : '' }}
            </div>
          </div>
        @endif

        {{-- Mis complejos --}}
        <div class="dash-card">
          <h3 class="dash-card-title"><span>🏟️</span> Mis complejos</h3>

          @if($venues->isEmpty())
            <div style="color:#aaa; font-size:14px;">No hay complejos cargados.</div>
          @else
            <div style="display:flex; flex-direction:column; gap:10px;">
              @foreach($venues as $venue)
                <div style="padding:12px 14px; border:1px solid #f0f0f0; border-radius:12px; background:#fafafa;">
                  <div style="font-weight:800; font-size:14px;">{{ $venue->name }}</div>
                  <div style="font-size:12px; color:#888; margin-top:3px;">
                    {{ $venue->fields->count() }} cancha{{ $venue->fields->count() !== 1 ? 's' : '' }}
                    @if($venue->zone) · {{ $venue->zone }} @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>

      </div>
    </div>
  </div>

  {{-- ── TAB: COMPLEJOS ────────────────────────────────────────────────── --}}
  <div id="tab-complejos" class="tab-pane">

    @if((auth()->user()->role === 'super_admin' || !$venues->count()) && !auth()->user()->isVenueStaff())
      <div style="margin-bottom:16px;">
        <a href="{{ route('va.venues.create') }}" class="btn btn-primary">+ Crear complejo</a>
      </div>
    @endif

    @forelse($venues as $v)
      <div class="venue-block">
        <div class="venue-block-header">
          <div>
            <div class="venue-block-name">{{ $v->name }}</div>
            @if($v->description)
              <div style="font-size:13px; color:#888; margin-top:2px;">{{ $v->description }}</div>
            @endif
          </div>

          <div class="venue-block-actions">
            <a href="{{ route('va.venues.edit', $v) }}" class="vba-link">Editar complejo</a>

            @if($v->mp_access_token)
              <span class="vba-mp-connected">✓ MP conectado</span>
              <form method="POST" action="{{ route('va.mp_oauth.disconnect', $v) }}" style="margin:0;"
                    onsubmit="return confirm('¿Desconectar Mercado Pago de este complejo?')">
                @csrf
                <button type="submit" class="vba-disconnect-btn">Desconectar</button>
              </form>
            @else
              <a href="{{ route('va.mp_oauth.redirect', $v) }}" class="vba-mp-btn">Conectar Mercado Pago</a>
            @endif
          </div>
        </div>

        <div class="fields-list">
          @if($v->fields->isEmpty())
            <div style="color:#aaa; font-size:14px; padding:8px 0;">No hay canchas cargadas todavía.</div>
          @else
            @foreach($v->fields as $f)
              <div class="field-row">
                <div style="display:flex; align-items:center; gap:8px; flex: 1; min-width:0;">
                  @if(!$f->is_active)
                    <span class="field-inactive-badge">Inactiva</span>
                  @endif
                  <span class="field-name">{{ $f->name }}</span>
                  <span class="field-price">
                    {{ number_format($f->price->price_per_slot ?? 0, 0, ',', '.') }} {{ $f->price->currency ?? 'ARS' }}
                  </span>
                </div>

                <div style="display:flex; gap:6px; align-items:center; flex-shrink:0; flex-wrap:wrap;">
                  <a href="{{ route('va.fields.edit', $f) }}" class="field-link">Editar</a>
                  <a href="{{ route('va.schedule.edit', $f) }}" class="field-link">Horarios</a>
                  <form method="POST" action="{{ route('va.fields.toggle_active', $f) }}" style="margin:0;">
                    @csrf
                    @if($f->is_active)
                      <button type="submit" class="toggle-active-btn deactivate">Desactivar</button>
                    @else
                      <button type="submit" class="toggle-active-btn activate">Activar</button>
                    @endif
                  </form>
                </div>
              </div>
            @endforeach
          @endif

          @unless(auth()->user()->isVenueStaff())
          <div style="padding-top:4px;">
            <a href="{{ route('va.fields.create', $v) }}" class="btn btn-primary" style="font-size:13px; padding:8px 16px;">
              + Agregar cancha
            </a>
          </div>
          @endunless
        </div>
      </div>
    @empty
      <div class="dash-card" style="text-align:center; padding:40px 24px;">
        <div style="font-size:40px; margin-bottom:12px;">🏟️</div>
        <div style="font-weight:800; font-size:16px; margin-bottom:8px;">Todavía no tenés complejos</div>
        <div style="color:#888; font-size:14px; margin-bottom:20px;">Creá tu primer complejo para empezar a recibir reservas.</div>
        @if(!auth()->user()->isVenueStaff())
          <a href="{{ route('va.venues.create') }}" class="btn btn-primary">+ Crear complejo</a>
        @endif
      </div>
    @endforelse
  </div>

  {{-- ── TAB: RESERVAS ─────────────────────────────────────────────────── --}}
  <div id="tab-reservas" class="tab-pane">
    <div class="dash-card">
      <h3 class="dash-card-title"><span>📅</span> Reservas de hoy</h3>

      @if($upcomingToday->isEmpty())
        <div style="padding:32px 0; text-align:center; color:#aaa; font-size:14px;">
          No hay reservas cargadas para hoy.
        </div>
      @else
        <div style="overflow-x:auto;">
          <table class="dash-table">
            <thead>
              <tr>
                <th>Hora</th>
                <th>Complejo</th>
                <th>Cancha</th>
                <th>Usuario</th>
                <th>Estado</th>
                <th>Monto</th>
              </tr>
            </thead>
            <tbody>
              @foreach($upcomingToday as $r)
                <tr>
                  <td style="font-weight:800; white-space:nowrap;">
                    {{ $r->start_at->format('H:i') }}–{{ $r->end_at->format('H:i') }}
                  </td>
                  <td>{{ $r->field->venue->name }}</td>
                  <td style="font-weight:600;">{{ $r->field->name }}</td>
                  <td>{{ $r->user->name ?? '—' }}</td>
                  <td>
                    <span class="status-pill" style="{{ ReservationStatus::color($r->status) }}">
                      {{ ReservationStatus::label($r->status) }}
                    </span>
                  </td>
                  <td style="font-weight:700;">
                    {{ $r->currency }} {{ number_format($r->total_amount, 0, ',', '.') }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  {{-- ── TAB: GLOBAL (super_admin) ─────────────────────────────────────── --}}
  @if(auth()->user()->role === 'super_admin')
    <div id="tab-global" class="tab-pane">

      <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
        <div class="kpi-card">
          <span class="kpi-icon">👤</span>
          <div class="kpi-label">Usuarios totales</div>
          <div class="kpi-value">{{ $superStats['users_count'] }}</div>
        </div>
        <div class="kpi-card green">
          <span class="kpi-icon">🏟️</span>
          <div class="kpi-label">Admins de complejo</div>
          <div class="kpi-value">{{ $superStats['venue_admins_count'] }}</div>
        </div>
        <div class="kpi-card blue">
          <span class="kpi-icon">⚙️</span>
          <div class="kpi-label">Superadmins</div>
          <div class="kpi-value">{{ $superStats['super_admins_count'] }}</div>
        </div>
        <div class="kpi-card purple">
          <span class="kpi-icon">🏟️</span>
          <div class="kpi-label">Complejos</div>
          <div class="kpi-value">{{ $superStats['venues_count'] }}</div>
        </div>
        <div class="kpi-card orange">
          <span class="kpi-icon">⚽</span>
          <div class="kpi-label">Canchas</div>
          <div class="kpi-value">{{ $superStats['fields_count'] }}</div>
        </div>
        <div class="kpi-card green">
          <span class="kpi-icon">📅</span>
          <div class="kpi-label">Reservas hoy</div>
          <div class="kpi-value">{{ $superStats['reservations_today_count'] }}</div>
        </div>
        <div class="kpi-card blue">
          <span class="kpi-icon">💰</span>
          <div class="kpi-label">Ingresos hoy</div>
          <div class="kpi-value" style="font-size:20px;">$ {{ number_format($superStats['revenue_today'], 0, ',', '.') }}</div>
        </div>
        <div class="kpi-card purple">
          <span class="kpi-icon">📈</span>
          <div class="kpi-label">Reservas semana</div>
          <div class="kpi-value">{{ $superStats['reservations_week_count'] }}</div>
        </div>
        <div class="kpi-card orange">
          <span class="kpi-icon">💵</span>
          <div class="kpi-label">Ingresos semana</div>
          <div class="kpi-value" style="font-size:18px;">$ {{ number_format($superStats['revenue_week'], 0, ',', '.') }}</div>
        </div>
      </div>

      <div class="dash-two-col">
        <div class="dash-card">
          <h3 class="dash-card-title"><span>🔥</span> Complejo más reservado</h3>
          @if($topVenue)
            <div class="top-field-card" style="margin-bottom:0;">
              <div class="label">Esta semana</div>
              <div class="name">{{ $topVenue->venue_name }}</div>
              <div class="sub">{{ $topVenue->reservations_count }} reserva{{ $topVenue->reservations_count > 1 ? 's' : '' }}</div>
            </div>
          @else
            <div style="color:#aaa; font-size:14px;">Todavía no hay suficientes datos.</div>
          @endif
        </div>

        <div class="dash-card">
          <h3 class="dash-card-title"><span>🆕</span> Usuarios recientes</h3>
          @if($recentUsers->isEmpty())
            <div style="color:#aaa; font-size:14px;">No hay usuarios recientes.</div>
          @else
            <div style="display:flex; flex-direction:column; gap:10px;">
              @foreach($recentUsers as $u)
                <div style="padding:10px 12px; border:1px solid #f0f0f0; border-radius:12px; background:#fafafa;">
                  <div style="font-weight:700; font-size:14px;">{{ $u->name }}</div>
                  <div style="font-size:12px; color:#888; margin-top:2px;">{{ $u->email }}</div>
                  <div style="margin-top:6px;">
                    <span style="display:inline-block; padding:2px 8px; background:#111; color:#fff; border-radius:999px; font-size:11px; font-weight:700;">
                      {{ $u->role }}
                    </span>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  @endif

  <script>
    function showTab(tabName) {
      document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.dash-tab').forEach(el => el.classList.remove('active'));
      const pane = document.getElementById('tab-' + tabName);
      if (pane) pane.classList.add('active');
      const btn = [...document.querySelectorAll('.dash-tab')].find(b => b.dataset.tab === tabName);
      if (btn) btn.classList.add('active');
      localStorage.setItem('va_dashboard_active_tab', tabName);
    }

    document.addEventListener('DOMContentLoaded', () => {
      const saved = localStorage.getItem('va_dashboard_active_tab') || 'resumen';
      showTab(saved);
    });
  </script>

@endsection
