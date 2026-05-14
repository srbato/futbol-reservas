@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.admin')

@section('title', $view === 'week' ? 'Agenda semanal' : 'Agenda del día')
@section('page_title', 'Agenda')
@section('page_subtitle', $view === 'week' ? 'Vista semanal — reservas confirmadas por cancha' : 'Vista diaria — reservas confirmadas por horario y cancha')

@push('styles')
<style>
  /* ── Toggle Día / Semana ── */
  .view-toggle {
    display: inline-flex;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 18px;
  }
  .view-toggle a {
    padding: 7px 18px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    color: #555;
    background: #fff;
    transition: background .15s, color .15s;
  }
  .view-toggle a:first-child { border-right: 1px solid #ddd; }
  .view-toggle a.active {
    background: #111;
    color: #fff;
  }

  /* ── Navegación de fecha ── */
  .agenda-nav {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 18px;
  }
  .agenda-nav-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 1px solid #ddd;
    border-radius: 10px;
    text-decoration: none;
    color: #111;
    font-size: 16px;
    transition: background .15s;
  }
  .agenda-nav-arrow:hover { background: #f3f3f3; }
  .agenda-date-label {
    font-size: 17px;
    font-weight: 700;
    padding: 0 6px;
  }
  .agenda-today-badge {
    display: inline-block;
    background: #111;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 20px;
    vertical-align: middle;
    margin-left: 4px;
  }

  /* ── Filtros ── */
  .agenda-filters {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: flex-end;
    margin-bottom: 18px;
  }
  .agenda-filter-group label {
    display: block;
    font-size: 12px;
    color: #666;
    margin-bottom: 4px;
  }
  .agenda-filter-group input,
  .agenda-filter-group select {
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 14px;
    background: #fff;
  }

  /* ── Resumen ── */
  .agenda-summary {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 18px;
  }
  .agenda-stat {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 12px;
    padding: 12px 18px;
    min-width: 130px;
  }
  .agenda-stat-value {
    font-size: 22px;
    font-weight: 700;
    line-height: 1;
  }
  .agenda-stat-label {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
  }

  /* ── Grilla compartida ── */
  .agenda-scroll { overflow-x: auto; }
  .agenda-table {
    border-collapse: collapse;
    width: 100%;
    font-size: 14px;
  }
  .agenda-table th {
    background: #111;
    color: #fff;
    padding: 10px 14px;
    text-align: left;
    white-space: nowrap;
    font-weight: 600;
  }
  .agenda-table th.th-hour {
    width: 72px;
    text-align: center;
  }
  .agenda-table td {
    padding: 6px 8px;
    vertical-align: top;
    border-bottom: 1px solid #f0f0f0;
  }
  .agenda-table tr:last-child td { border-bottom: none; }
  .agenda-table td.td-hour {
    font-size: 13px;
    font-weight: 700;
    color: #666;
    text-align: center;
    white-space: nowrap;
    background: #fafafa;
    border-right: 2px solid #e8e8e8;
  }
  .agenda-table tr:nth-child(even) td { background: #fafafa; }
  .agenda-table tr:nth-child(even) td.td-hour { background: #f4f4f4; }

  /* ── Celdas con reserva (día) ── */
  .agenda-table td.day-col { min-width: 200px; }
  .res-cell {
    border-radius: 10px;
    padding: 9px 11px;
    border-left: 4px solid transparent;
  }
  .res-cell.status-PAID            { background: #d1e7dd; border-left-color: #198754; color: #0f5132; }
  .res-cell.status-PENDING_PAYMENT { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
  .res-cell.status-PENDING_CASH    { background: #e0e7ff; border-left-color: #6366f1; color: #3730a3; }
  .res-cell.status-CANCELLED       { background: #e9ecef; border-left-color: #adb5bd; color: #6c757d; }
  .res-cell.status-EXPIRED         { background: #f8d7da; border-left-color: #dc3545; color: #842029; }
  .res-user   { font-weight: 700; font-size: 14px; }
  .res-status { font-size: 12px; margin-top: 3px; opacity: .85; }
  .res-time   { font-size: 12px; margin-top: 5px; font-weight: 600; }
  .res-price  { font-size: 13px; font-weight: 700; margin-top: 4px; }
  .res-code   { font-size: 11px; margin-top: 3px; opacity: .7; font-family: monospace; }
  .res-actions { margin-top: 8px; display: flex; gap: 6px; flex-wrap: wrap; }
  .res-btn-cancel {
    font-size: 12px; padding: 8px 12px; border: 1px solid currentColor;
    border-radius: 6px; background: transparent; cursor: pointer;
    font-weight: 600; opacity: .75; min-height: 36px;
    display: inline-flex; align-items: center;
  }
  .res-btn-cancel:hover { opacity: 1; }
  .res-btn-view {
    font-size: 12px; padding: 8px 12px; border: 1px solid currentColor;
    border-radius: 6px; text-decoration: none; font-weight: 600;
    opacity: .75; min-height: 36px; display: inline-flex; align-items: center;
  }
  .res-btn-view:hover { opacity: 1; }
  .free-cell {
    border-radius: 10px; padding: 8px 10px; background: #f8f9fa;
    color: #adb5bd; font-size: 12px; text-align: center;
  }

  /* ── Vista semanal ── */
  .week-table { min-width: 1000px; }
  .week-table th.th-day {
    min-width: 130px;
    text-align: center;
    font-weight: 600;
  }
  .week-table th.th-day.today-col {
    background: #1a6b3a;
  }
  .week-table td.week-col { min-width: 130px; text-align: center; }

  /* Reserva compacta (semana) */
  .wres-cell {
    border-radius: 8px;
    padding: 7px 9px;
    border-left: 3px solid transparent;
    text-align: left;
  }
  .wres-cell.status-PAID            { background: #d1e7dd; border-left-color: #198754; color: #0f5132; }
  .wres-cell.status-PENDING_PAYMENT { background: #fff3cd; border-left-color: #ffc107; color: #856404; }
  .wres-cell.status-PENDING_CASH    { background: #e0e7ff; border-left-color: #6366f1; color: #3730a3; }
  .wres-cell.status-CANCELLED       { background: #e9ecef; border-left-color: #adb5bd; color: #6c757d; }
  .wres-cell.status-EXPIRED         { background: #f8d7da; border-left-color: #dc3545; color: #842029; }
  .wres-name  { font-weight: 700; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 110px; }
  .wres-price { font-size: 11px; font-weight: 600; margin-top: 2px; }
  .wres-acts  { margin-top: 5px; display: flex; gap: 4px; }
  .wres-btn {
    font-size: 11px; padding: 4px 8px; border: 1px solid currentColor;
    border-radius: 5px; background: transparent; cursor: pointer;
    font-weight: 600; opacity: .75; text-decoration: none;
    display: inline-flex; align-items: center;
  }
  .wres-btn:hover { opacity: 1; }

  .week-free   { color: #ccc; font-size: 11px; }
  .week-closed { color: #e8e8e8; font-size: 11px; }

  /* ── Footer ── */
  .agenda-footer {
    margin-top: 16px;
    font-size: 14px;
    color: #666;
  }
  .agenda-footer a { color: #111; font-weight: 600; }

  /* ════════════════════════════════════════════════════════════════════════
     Grid ATC admin (vista diaria nueva — filas=canchas, columnas=horas)
     ════════════════════════════════════════════════════════════════════════ */
  .adm-grid-wrap{
    --adm-cell-w:120px; --adm-row-h:84px; --adm-fname-w:220px;
    --adm-bd:#e6e6e6; --adm-bd2:#d4d4d4;
    --adm-text:#111; --adm-muted:#737373; --adm-faint:#a3a3a3;
    --adm-bg-head:#111; --adm-bg-card:#fff; --adm-bg-zebra:#fafafa;
  }
  .adm-grid-card{
    background:var(--adm-bg-card);border:1px solid var(--adm-bd);border-radius:14px;
    overflow:hidden;position:relative;
    box-shadow:0 8px 24px -10px rgba(0,0,0,.08);
  }
  .adm-grid-card::after{
    content:'';position:absolute;top:0;right:0;bottom:10px;width:36px;pointer-events:none;
    background:linear-gradient(270deg, #fff, transparent);opacity:0;transition:opacity .25s;z-index:5
  }
  .adm-grid-card.has-overflow::after{opacity:1}
  .adm-grid-scroll{overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:thin;scrollbar-color:#bbb transparent}
  .adm-grid-scroll::-webkit-scrollbar{height:10px}
  .adm-grid-scroll::-webkit-scrollbar-thumb{background:#ccc;border-radius:99px}
  .adm-grid{display:grid;width:100%;user-select:none;-webkit-user-select:none}

  /* Header row */
  .adm-h-corner{
    position:sticky;left:0;z-index:30;background:var(--adm-bg-head);color:#fff;
    height:52px;display:flex;align-items:center;padding:0 16px;
    font-size:11px;text-transform:uppercase;letter-spacing:.1em;font-weight:700;
    border-bottom:1px solid var(--adm-bd2);
  }
  .adm-h-hour{
    height:52px;background:var(--adm-bg-head);color:#fff;
    border-right:1px solid rgba(255,255,255,.12);
    border-bottom:1px solid var(--adm-bd2);
    display:flex;align-items:center;justify-content:center;
    font-size:13px;font-weight:700;font-variant-numeric:tabular-nums;letter-spacing:-.01em
  }
  .adm-h-hour:last-child{border-right:none}

  /* Field name (sticky col) */
  .adm-fname{
    position:sticky;left:0;z-index:10;background:#fff;
    height:var(--adm-row-h);
    border-right:1px solid var(--adm-bd2);border-bottom:1px solid var(--adm-bd);
    display:flex;flex-direction:column;justify-content:center;padding:0 16px;gap:4px
  }
  .adm-fname-name{font-size:14px;font-weight:700;color:var(--adm-text);
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
  .adm-fname-meta{font-size:11px;color:var(--adm-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600}

  /* Cells */
  .adm-cell{
    height:var(--adm-row-h);
    border-right:1px solid var(--adm-bd);border-bottom:1px solid var(--adm-bd);
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    transition:background .12s, box-shadow .15s;font-size:12px;position:relative;min-width:0;
    cursor:pointer
  }
  .adm-cell:last-child{border-right:none}
  .adm-cell-free{
    background:repeating-linear-gradient(45deg, #fafafa 0 8px, #fff 8px 16px);
    color:var(--adm-faint);
  }
  .adm-cell-free::before{
    content:'+';font-size:22px;font-weight:300;color:#cbd5e1;opacity:0;transition:opacity .15s;
  }
  .adm-cell-free:hover{background:#ecfdf5;}
  .adm-cell-free:hover::before{opacity:1;color:#10b981;}
  .adm-cell-free.adm-drag-selected{background:#10b981 !important;color:#fff;box-shadow:inset 0 0 0 2px rgba(255,255,255,.5)}
  .adm-cell-free.adm-drag-selected::before{opacity:1;color:#fff}
  .adm-cell-past{background:#fafafa;color:#d4d4d4;cursor:not-allowed}
  .adm-cell-closed{background:#f5f5f5;cursor:not-allowed;background-image:linear-gradient(0deg, transparent 49%, #e5e5e5 49% 51%, transparent 51%)}

  /* Reserved cell (spans across slots) */
  .adm-cell-res{
    padding:8px 10px;align-items:flex-start;justify-content:flex-start;text-align:left;
    border-radius:0;cursor:pointer;
    transition:transform .1s, box-shadow .15s;
  }
  .adm-cell-res:hover{box-shadow:inset 0 0 0 2px rgba(0,0,0,.18);z-index:5}
  .adm-cell-res.adm-status-PAID{background:#d1fae5;color:#065f46;border-left:3px solid #10b981}
  .adm-cell-res.adm-status-PENDING_PAYMENT{background:#fef3c7;color:#92400e;border-left:3px solid #f59e0b}
  .adm-cell-res.adm-status-PENDING_CASH{background:#dbeafe;color:#1e40af;border-left:3px solid #3b82f6}
  .adm-res-name{font-weight:700;font-size:13px;line-height:1.2;
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%}
  .adm-res-time{font-size:11px;font-weight:600;margin-top:3px;opacity:.85;font-variant-numeric:tabular-nums}
  .adm-res-price{font-size:12px;font-weight:700;margin-top:3px}
  .adm-res-badge{
    position:absolute;top:6px;right:8px;font-size:9px;text-transform:uppercase;
    letter-spacing:.06em;font-weight:700;padding:2px 6px;border-radius:99px;
    background:rgba(0,0,0,.08)
  }

  /* Block cell */
  .adm-cell-blk{
    padding:8px 10px;align-items:flex-start;justify-content:flex-start;text-align:left;
    background:repeating-linear-gradient(45deg, #f3f4f6 0 6px, #e5e7eb 6px 12px);
    color:#374151;border-left:3px solid #9ca3af;
  }
  .adm-cell-blk:hover{box-shadow:inset 0 0 0 2px rgba(0,0,0,.18)}
  .adm-blk-label{font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:.05em}
  .adm-blk-reason{font-size:11px;margin-top:3px;opacity:.8;
    overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%}

  /* Legend */
  .adm-legend{display:flex;gap:14px;flex-wrap:wrap;margin:12px 0 16px;font-size:12px;color:var(--adm-muted)}
  .adm-legend span{display:inline-flex;align-items:center;gap:6px}
  .adm-legend i{display:inline-block;width:14px;height:14px;border-radius:3px;border:1px solid var(--adm-bd)}
  .adm-l-free{background:repeating-linear-gradient(45deg, #fafafa 0 4px, #fff 4px 8px)}
  .adm-l-paid{background:#d1fae5;border-color:#10b981}
  .adm-l-cash{background:#dbeafe;border-color:#3b82f6}
  .adm-l-pend{background:#fef3c7;border-color:#f59e0b}
  .adm-l-blk {background:repeating-linear-gradient(45deg, #f3f4f6 0 4px, #e5e7eb 4px 8px)}
  .adm-l-past{background:#fafafa;border:1px dashed #d4d4d4}

  /* ── Modales ─────────────────────────────────────────────────────────── */
  .adm-modal{
    position:fixed;inset:0;z-index:1000;background:rgba(15,23,42,.55);
    display:flex;align-items:center;justify-content:center;padding:20px;
    backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);
    animation:adm-fade-in .15s ease-out
  }
  .adm-modal[hidden]{display:none !important}
  @keyframes adm-fade-in{from{opacity:0}to{opacity:1}}
  .adm-modal-card{
    background:#fff;border-radius:16px;padding:24px;width:100%;max-width:480px;
    box-shadow:0 20px 60px rgba(0,0,0,.25);
    animation:adm-slide-up .2s ease-out;max-height:90vh;overflow-y:auto
  }
  @keyframes adm-slide-up{from{transform:translateY(20px);opacity:0}to{transform:none;opacity:1}}
  .adm-modal h3{margin:0 0 6px;font-size:20px;font-weight:700;letter-spacing:-.01em}
  .adm-modal .adm-mod-sub{font-size:13px;color:#6b7280;margin-bottom:18px}
  .adm-modal label{display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;letter-spacing:.02em}
  .adm-modal input[type=text],
  .adm-modal input[type=number],
  .adm-modal input[type=time],
  .adm-modal textarea,
  .adm-modal select{
    width:100%;padding:10px 12px;border:1px solid #e5e7eb;border-radius:10px;
    font-size:14px;background:#fff;font-family:inherit;
    transition:border-color .15s
  }
  .adm-modal input:focus,.adm-modal textarea:focus,.adm-modal select:focus{outline:none;border-color:#10b981}
  .adm-modal textarea{resize:vertical;min-height:60px}
  .adm-mod-field{margin-bottom:14px}
  .adm-mod-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px}
  .adm-mod-actions{display:flex;gap:8px;justify-content:flex-end;margin-top:18px;padding-top:16px;border-top:1px solid #f3f4f6}
  .adm-mod-actions button{
    padding:10px 16px;border-radius:10px;font-weight:600;cursor:pointer;font-size:14px;
    border:1px solid #e5e7eb;background:#fff;color:#374151;font-family:inherit
  }
  .adm-mod-actions button.adm-primary{background:#111;color:#fff;border-color:#111}
  .adm-mod-actions button.adm-primary:hover{background:#000}
  .adm-mod-actions button.adm-danger{background:#fef2f2;color:#b91c1c;border-color:#fecaca}
  .adm-mod-actions button.adm-danger:hover{background:#fee2e2}
  .adm-mod-actions button.adm-success{background:#ecfdf5;color:#047857;border-color:#a7f3d0}
  .adm-mod-actions button.adm-success:hover{background:#d1fae5}
  .adm-mod-info{
    background:#f9fafb;border:1px solid #f3f4f6;border-radius:10px;padding:12px 14px;margin-bottom:14px;
    font-size:13px;color:#374151;line-height:1.5
  }
  .adm-mod-info b{color:#111;font-weight:700}
  .adm-mod-info .row{display:flex;justify-content:space-between;gap:8px;padding:3px 0}

  .adm-search-results{
    margin-top:6px;border:1px solid #e5e7eb;border-radius:10px;
    max-height:160px;overflow-y:auto;background:#fff
  }
  .adm-search-results:empty{display:none}
  .adm-search-result{
    padding:9px 12px;cursor:pointer;border-bottom:1px solid #f3f4f6;font-size:13px
  }
  .adm-search-result:last-child{border-bottom:none}
  .adm-search-result:hover{background:#f9fafb}
  .adm-search-result b{display:block;color:#111;font-weight:600}
  .adm-search-result span{color:#6b7280;font-size:12px}

  @media (max-width:768px){
    .adm-grid-wrap{--adm-cell-w:96px;--adm-row-h:78px;--adm-fname-w:152px}
    .adm-fname{padding:0 12px}
    .adm-fname-name{font-size:13px}
    .adm-fname-meta{font-size:10px}
    .adm-h-corner,.adm-h-hour{height:46px}
    .adm-modal-card{padding:18px}
    .adm-mod-row{grid-template-columns:1fr}
  }
</style>
@endpush

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_agenda',
  'helpTitle' => 'Agenda',
  'helpText'  => 'La agenda muestra en formato de grilla los turnos organizados por horario y cancha. Podés alternar entre vista diaria (todas las canchas de un día) y vista semanal (una cancha, los 7 días de la semana). Solo aparecen las reservas confirmadas (pagadas).',
])

{{-- ── Toggle Día / Semana ── --}}
<div class="view-toggle">
  <a href="{{ route('va.reservations.agenda', array_filter(['date' => $date->toDateString(), 'field_id' => $fieldId, 'view' => 'day'])) }}"
     class="{{ $view === 'day' ? 'active' : '' }}">
    Día
  </a>
  <a href="{{ route('va.reservations.agenda', array_filter(['date' => $date->toDateString(), 'field_id' => $fieldId, 'view' => 'week'])) }}"
     class="{{ $view === 'week' ? 'active' : '' }}">
    Semana
  </a>
</div>

{{-- ════════════════════════════════════════════════════════ VISTA DIARIA ══ --}}
@if($view === 'day')

@php
  $prevDate = $date->copy()->subDay()->toDateString();
  $nextDate = $date->copy()->addDay()->toDateString();
  $isToday  = $date->isToday();

  $paid        = $reservations->where('status', 'PAID')->count();
  $pending     = $reservations->where('status', 'PENDING_PAYMENT')->count();
  $pendingCash = $reservations->where('status', 'PENDING_CASH')->count();
  $total       = $reservations->where('status', 'PAID')->sum('total_amount');
@endphp

<div class="agenda-nav">
  <a class="agenda-nav-arrow"
     href="{{ route('va.reservations.agenda', array_filter(['date' => $prevDate, 'field_id' => $fieldId])) }}"
     title="Día anterior">&#8592;</a>
  <span class="agenda-date-label">
    {{ ucfirst($date->translatedFormat('l d \d\e F')) }}
    @if($isToday)<span class="agenda-today-badge">Hoy</span>@endif
  </span>
  <a class="agenda-nav-arrow"
     href="{{ route('va.reservations.agenda', array_filter(['date' => $nextDate, 'field_id' => $fieldId])) }}"
     title="Día siguiente">&#8594;</a>
</div>

<form method="GET" action="{{ route('va.reservations.agenda') }}" class="agenda-filters">
  <input type="hidden" name="view" value="day">
  <div class="agenda-filter-group">
    <label>Fecha</label>
    <input type="date" name="date" value="{{ $date->toDateString() }}">
  </div>
  <div class="agenda-filter-group">
    <label>Cancha</label>
    <select name="field_id">
      <option value="">Todas las canchas</option>
      @foreach($fields as $field)
        <option value="{{ $field->id }}" {{ (string)($fieldId ?? '') === (string)$field->id ? 'selected' : '' }}>
          {{ $field->name }} — {{ $field->venue->name }}
        </option>
      @endforeach
    </select>
  </div>
  <button type="submit" class="btn-primary">Ver agenda</button>
  <a href="{{ route('va.reservations.agenda') }}" class="btn-ghost">Hoy</a>
</form>

<div class="agenda-summary">
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $reservations->count() }}</div>
    <div class="agenda-stat-label">Total reservas</div>
  </div>
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $paid }}</div>
    <div class="agenda-stat-label">Pagadas</div>
  </div>
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $pending }}</div>
    <div class="agenda-stat-label">Pendientes</div>
  </div>
  @if($pendingCash > 0)
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $pendingCash }}</div>
    <div class="agenda-stat-label">Pago en complejo</div>
  </div>
  @endif
  <div class="agenda-stat">
    <div class="agenda-stat-value">${{ number_format($total, 0, ',', '.') }}</div>
    <div class="agenda-stat-label">Ingresos del día</div>
  </div>
</div>

@if($fields->isEmpty())
  <div class="admin-card" style="color:#666; text-align:center; padding:40px;">
    No tenés canchas configuradas aún.
  </div>
@elseif(empty($slots))
  <div class="admin-card" style="color:#666; text-align:center; padding:40px;">
    No hay horarios configurados para este día.
  </div>
@else
  {{-- Leyenda --}}
  <div class="adm-legend">
    <span><i class="adm-l-free"></i>Libre · clic para reservar</span>
    <span><i class="adm-l-paid"></i>Pagada</span>
    <span><i class="adm-l-cash"></i>Efectivo</span>
    <span><i class="adm-l-pend"></i>Pendiente pago</span>
    <span><i class="adm-l-blk"></i>Bloqueado</span>
    <span><i class="adm-l-past"></i>Pasado</span>
    <span style="margin-left:auto; font-style:italic;">Tip: arrastrá sobre celdas libres para crear un bloqueo</span>
  </div>

  <div class="adm-grid-wrap">
    <div class="adm-grid-card" id="admGridCard">
      <div class="adm-grid-scroll" id="admGridScroll">
        @php
          $totalCols = count($slots);
          $tplCols = "var(--adm-fname-w) repeat({$totalCols}, minmax(var(--adm-cell-w), 1fr))";
        @endphp
        <div class="adm-grid" style="grid-template-columns: {{ $tplCols }};">
          {{-- Header --}}
          <div class="adm-h-corner">Cancha</div>
          @foreach($slots as $slot)
            <div class="adm-h-hour">{{ $slot }}</div>
          @endforeach

          {{-- Filas: una por cancha --}}
          @foreach($fields as $field)
            <div class="adm-fname">
              <div class="adm-fname-name">{{ $field->name }}</div>
              <div class="adm-fname-meta">
                {{ $field->venue->name }}
                @if($field->slot_minutes && $field->slot_minutes != 60)
                  · {{ $field->slot_minutes }} min
                @endif
              </div>
            </div>

            @php $skipCols = 0; @endphp
            @foreach($slots as $slot)
              @if($skipCols > 0)
                @php $skipCols--; @endphp
                @continue
              @endif

              @php
                $key  = $field->id . '|' . $slot;
                $cell = $cellMap[$key] ?? null;
                $oc   = $fieldOpenClose[$field->id] ?? null;
                $slotDt = \Carbon\Carbon::parse($date->toDateString() . ' ' . $slot);
                $isPast = $slotDt->lt(now());
                $inSched = $oc && $slotDt >= $oc['open'] && $slotDt < $oc['close'];
              @endphp

              @if(!$inSched && !$cell)
                <div class="adm-cell adm-cell-closed" title="Fuera de horario"></div>
              @elseif($cell && $cell['type'] === 'reservation')
                @php
                  $r = $cell['data'];
                  $sp = max(1, (int) $cell['span']);
                  $skipCols = $sp - 1;
                  $resJsonAttrs = json_encode([
                    'id'      => $r->id,
                    'name'    => $r->user->name ?? 'Sin usuario',
                    'email'   => $r->user->email ?? '',
                    'time'    => $r->start_at->format('H:i') . ' – ' . $r->end_at->format('H:i'),
                    'date'    => $r->start_at->isoFormat('dddd D [de] MMMM'),
                    'field'   => $field->name,
                    'venue'   => $field->venue->name,
                    'status'  => $r->status,
                    'statusLabel' => ReservationStatus::label($r->status),
                    'total'   => $r->total_amount,
                    'currency'=> $r->currency,
                    'code'    => $r->verification_code,
                    'notes'   => $r->notes,
                    'detailUrl' => route('reservations.show', $r),
                    'cancelUrl' => route('va.reservations.cancel', $r),
                    'cashUrl'   => route('va.reservations.confirm_cash', $r),
                  ], JSON_UNESCAPED_UNICODE);
                @endphp
                <div class="adm-cell adm-cell-res adm-status-{{ $r->status }}"
                     style="grid-column: span {{ $sp }};"
                     data-res='{{ $resJsonAttrs }}'
                     onclick="admOpenReservation(this)"
                     title="{{ $r->user->name ?? 'Sin usuario' }} · {{ $r->start_at->format('H:i') }}–{{ $r->end_at->format('H:i') }}">
                  <div class="adm-res-name">{{ $r->user->name ?? 'Sin usuario' }}</div>
                  <div class="adm-res-time">{{ $r->start_at->format('H:i') }} – {{ $r->end_at->format('H:i') }}</div>
                  @if($r->total_amount)
                    <div class="adm-res-price">${{ number_format($r->total_amount, 0, ',', '.') }}</div>
                  @endif
                  @if($r->status === 'PENDING_CASH')
                    <span class="adm-res-badge">EFECTIVO</span>
                  @elseif($r->status === 'PENDING_PAYMENT')
                    <span class="adm-res-badge">PENDIENTE</span>
                  @endif
                </div>
              @elseif($cell && $cell['type'] === 'block')
                @php
                  $b  = $cell['data'];
                  $sp = max(1, (int) $cell['span']);
                  $skipCols = $sp - 1;
                  $blkJson = json_encode([
                    'id'        => $b->id,
                    'time'      => substr($b->start_time, 0, 5) . ' – ' . substr($b->end_time, 0, 5),
                    'reason'    => $b->reason,
                    'field'     => $field->name,
                    'destroyUrl'=> route('va.blocks.destroy', $b),
                  ], JSON_UNESCAPED_UNICODE);
                @endphp
                <div class="adm-cell adm-cell-blk"
                     style="grid-column: span {{ $sp }};"
                     data-blk='{{ $blkJson }}'
                     onclick="admOpenBlock(this)"
                     title="Bloqueo {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}">
                  <div class="adm-blk-label">Bloqueado</div>
                  @if($b->reason)
                    <div class="adm-blk-reason">{{ $b->reason }}</div>
                  @endif
                </div>
              @elseif($isPast)
                <div class="adm-cell adm-cell-past">—</div>
              @else
                <div class="adm-cell adm-cell-free"
                     data-field="{{ $field->id }}"
                     data-field-name="{{ $field->name }}"
                     data-venue-name="{{ $field->venue->name }}"
                     data-date="{{ $date->toDateString() }}"
                     data-slot="{{ $slot }}"
                     data-slot-minutes="{{ $field->slot_minutes ?: 60 }}"></div>
              @endif
            @endforeach
          @endforeach
        </div>
      </div>
    </div>
  </div>

  {{-- ─── Modales ─────────────────────────────────────────────────────── --}}

  {{-- Crear reserva manual --}}
  <div id="admModManual" class="adm-modal" hidden>
    <div class="adm-modal-card">
      <h3>Nueva reserva manual</h3>
      <p class="adm-mod-sub" id="admModManualSub">—</p>
      <form method="POST" action="{{ route('va.reservations.manual_store') }}">
        @csrf
        <input type="hidden" name="field_id" id="admMm_fieldId">
        <input type="hidden" name="date" id="admMm_date">
        <input type="hidden" name="time" id="admMm_time">

        <div class="adm-mod-field">
          <label>Cliente (opcional, dejá vacío si es reserva del complejo)</label>
          <input type="text" id="admMm_userSearch" autocomplete="off"
                 placeholder="Buscar por email o nombre…">
          <input type="hidden" name="client_user_id" id="admMm_clientUserId">
          <div id="admMm_userResults" class="adm-search-results"></div>
        </div>

        <div class="adm-mod-field">
          <label>Monto cobrado (opcional)</label>
          <input type="number" name="amount_paid" min="0" step="0.01" placeholder="0">
        </div>

        <div class="adm-mod-field">
          <label>Notas internas (opcional)</label>
          <textarea name="notes" maxlength="255" rows="2" placeholder="Ej: cliente recurrente, pagó seña, etc."></textarea>
        </div>

        <div class="adm-mod-actions">
          <button type="button" onclick="admCloseModal('admModManual')">Cancelar</button>
          <button type="submit" class="adm-primary">Crear reserva</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Detalle de reserva --}}
  <div id="admModRes" class="adm-modal" hidden>
    <div class="adm-modal-card">
      <h3 id="admMr_name">—</h3>
      <p class="adm-mod-sub" id="admMr_status">—</p>

      <div class="adm-mod-info">
        <div class="row"><span>Fecha</span><b id="admMr_date">—</b></div>
        <div class="row"><span>Horario</span><b id="admMr_time">—</b></div>
        <div class="row"><span>Cancha</span><b id="admMr_field">—</b></div>
        <div class="row" id="admMr_emailRow"><span>Email</span><b id="admMr_email">—</b></div>
        <div class="row" id="admMr_priceRow"><span>Total</span><b id="admMr_price">—</b></div>
        <div class="row" id="admMr_codeRow"><span>Código</span><b id="admMr_code">—</b></div>
        <div class="row" id="admMr_notesRow" style="display:block; padding-top:8px;"><span>Notas</span><div id="admMr_notes" style="margin-top:4px;color:#374151;font-size:13px;"></div></div>
      </div>

      <div class="adm-mod-actions" style="flex-wrap:wrap;">
        <button type="button" onclick="admCloseModal('admModRes')">Cerrar</button>
        <a id="admMr_detailLink" href="#" target="_blank"
           style="padding:10px 16px; border-radius:10px; font-weight:600; font-size:14px;
                  border:1px solid #e5e7eb; background:#fff; color:#374151; text-decoration:none;">Ver detalle completo</a>
        <form id="admMr_cashForm" method="POST" style="display:none;">
          @csrf
          <button type="submit" class="adm-success">Confirmar pago en efectivo</button>
        </form>
        <form id="admMr_cancelForm" method="POST"
              onsubmit="return confirm('¿Cancelar esta reserva? Si fue pagada online, se intentará reembolsar automáticamente.')">
          @csrf
          <button type="submit" class="adm-danger">Cancelar reserva</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Detalle de bloqueo --}}
  <div id="admModBlk" class="adm-modal" hidden>
    <div class="adm-modal-card">
      <h3>Horario bloqueado</h3>
      <p class="adm-mod-sub" id="admMb_field">—</p>

      <div class="adm-mod-info">
        <div class="row"><span>Horario</span><b id="admMb_time">—</b></div>
        <div class="row" id="admMb_reasonRow" style="display:block; padding-top:8px;">
          <span>Motivo</span>
          <div id="admMb_reason" style="margin-top:4px;color:#374151;font-size:13px;"></div>
        </div>
      </div>

      <div class="adm-mod-actions">
        <button type="button" onclick="admCloseModal('admModBlk')">Cerrar</button>
        <form id="admMb_destroyForm" method="POST"
              onsubmit="return confirm('¿Quitar este bloqueo?')">
          @csrf
          <button type="submit" class="adm-danger">Quitar bloqueo</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Crear bloqueo (drag-to-block) --}}
  <div id="admModBlkNew" class="adm-modal" hidden>
    <div class="adm-modal-card">
      <h3>Bloquear horario</h3>
      <p class="adm-mod-sub" id="admMbn_sub">—</p>

      <form method="POST" action="{{ route('va.blocks.store') }}">
        @csrf
        <input type="hidden" name="field_id" id="admMbn_fieldId">
        <input type="hidden" name="date" id="admMbn_date">

        <div class="adm-mod-row">
          <div>
            <label>Desde</label>
            <input type="time" name="start_time" id="admMbn_start" required>
          </div>
          <div>
            <label>Hasta</label>
            <input type="time" name="end_time" id="admMbn_end" required>
          </div>
        </div>

        <div class="adm-mod-field">
          <label>Motivo (opcional)</label>
          <input type="text" name="reason" maxlength="255" placeholder="Ej: mantenimiento, lluvia, evento privado">
        </div>

        <div class="adm-mod-actions">
          <button type="button" onclick="admCloseModal('admModBlkNew')">Cancelar</button>
          <button type="submit" class="adm-primary">Crear bloqueo</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  (function(){
    const SEARCH_USERS_URL = "{{ route('va.users.search') }}";
    let dragSelecting = false;
    let dragField = null;
    let dragDate = null;
    let dragSlots = [];

    // ── Modal helpers ──────────────────────────────────────────────────────
    window.admCloseModal = function(id){
      const m = document.getElementById(id);
      if(m) m.hidden = true;
    };
    document.addEventListener('keydown', e => {
      if(e.key === 'Escape'){
        document.querySelectorAll('.adm-modal').forEach(m => m.hidden = true);
      }
    });
    document.querySelectorAll('.adm-modal').forEach(m => {
      m.addEventListener('click', e => { if(e.target === m) m.hidden = true; });
    });

    // ── Click en celda libre → abrir modal "Crear reserva manual" ─────────
    document.querySelectorAll('.adm-cell-free').forEach(cell => {
      cell.addEventListener('click', () => {
        if(dragField !== null) return; // si venimos de un drag, no abrir el modal de manual
        const fid = cell.dataset.field;
        const fname = cell.dataset.fieldName;
        const vname = cell.dataset.venueName;
        const slot = cell.dataset.slot;
        const cellDate = cell.dataset.date; // YYYY-MM-DD (día específico, importante en vista semanal)
        document.getElementById('admMm_fieldId').value = fid;
        document.getElementById('admMm_date').value = cellDate;
        document.getElementById('admMm_time').value = slot;
        // Sub: incluye fecha si es vista semanal (cells de días distintos)
        const dateLabel = cell.dataset.dateLabel || '';
        const sub = dateLabel ? `${fname} · ${vname} · ${dateLabel} ${slot}` : `${fname} · ${vname} · ${slot}`;
        document.getElementById('admModManualSub').textContent = sub;
        // reset búsqueda
        document.getElementById('admMm_userSearch').value = '';
        document.getElementById('admMm_clientUserId').value = '';
        document.getElementById('admMm_userResults').innerHTML = '';
        document.getElementById('admModManual').hidden = false;
      });
    });

    // ── Drag para crear bloqueos ──────────────────────────────────────────
    document.querySelectorAll('.adm-cell-free').forEach(cell => {
      cell.addEventListener('mousedown', e => {
        e.preventDefault();
        dragSelecting = true;
        dragField = cell.dataset.field;
        dragDate  = cell.dataset.date;
        dragSlots = [cell];
        cell.classList.add('adm-drag-selected');
      });
      cell.addEventListener('mouseenter', () => {
        if(!dragSelecting) return;
        if(cell.dataset.field !== dragField) return;
        if(cell.dataset.date  !== dragDate)  return; // restringir al mismo día (importante en week view)
        if(!dragSlots.includes(cell)){
          dragSlots.push(cell);
          cell.classList.add('adm-drag-selected');
        }
      });
    });
    document.addEventListener('mouseup', () => {
      if(!dragSelecting) return;
      dragSelecting = false;
      // Si el usuario sólo cliqueó (1 celda) → manual reservation modal, no bloqueo
      if(dragSlots.length <= 1){
        dragSlots.forEach(c => c.classList.remove('adm-drag-selected'));
        dragField = null; dragDate = null; dragSlots = [];
        return;
      }
      // ≥2 celdas → modal de bloqueo
      const slots = dragSlots.map(c => c.dataset.slot).sort();
      const slotMin = parseInt(dragSlots[0].dataset.slotMinutes) || 60;
      const startTime = slots[0];
      // end = ultima hora + slotMinutes
      const lastHour = slots[slots.length - 1];
      const [h, m] = lastHour.split(':').map(Number);
      const totalMin = h * 60 + m + slotMin;
      const endH = Math.floor(totalMin / 60);
      const endM = totalMin % 60;
      const endTime = String(endH).padStart(2,'0') + ':' + String(endM).padStart(2,'0');

      const fname = dragSlots[0].dataset.fieldName;
      const vname = dragSlots[0].dataset.venueName;
      const dragDate = dragSlots[0].dataset.date;
      const dateLabel = dragSlots[0].dataset.dateLabel || '';
      document.getElementById('admMbn_fieldId').value = dragField;
      document.getElementById('admMbn_date').value = dragDate;
      document.getElementById('admMbn_start').value = startTime;
      document.getElementById('admMbn_end').value = endTime;
      const sub = dateLabel ? `${fname} · ${vname} · ${dateLabel} ${startTime}–${endTime}` : `${fname} · ${vname} · ${startTime}–${endTime}`;
      document.getElementById('admMbn_sub').textContent = sub;
      document.getElementById('admModBlkNew').hidden = false;

      // limpiar selección visual al cerrar (el modal abre encima)
      setTimeout(() => {
        dragSlots.forEach(c => c.classList.remove('adm-drag-selected'));
        dragField = null; dragDate = null; dragSlots = [];
      }, 200);
    });

    // ── Click en celda reservada → modal detalle ──────────────────────────
    window.admOpenReservation = function(el){
      const r = JSON.parse(el.dataset.res);
      document.getElementById('admMr_name').textContent = r.name;
      document.getElementById('admMr_status').textContent = r.statusLabel;
      document.getElementById('admMr_date').textContent = r.date.charAt(0).toUpperCase() + r.date.slice(1);
      document.getElementById('admMr_time').textContent = r.time;
      document.getElementById('admMr_field').textContent = r.field + ' · ' + r.venue;

      const emailRow = document.getElementById('admMr_emailRow');
      if(r.email){ document.getElementById('admMr_email').textContent = r.email; emailRow.style.display = ''; }
      else emailRow.style.display = 'none';

      const priceRow = document.getElementById('admMr_priceRow');
      if(r.total){ document.getElementById('admMr_price').textContent = '$' + Number(r.total).toLocaleString('es-AR'); priceRow.style.display = ''; }
      else priceRow.style.display = 'none';

      const codeRow = document.getElementById('admMr_codeRow');
      if(r.code){ document.getElementById('admMr_code').textContent = r.code; codeRow.style.display = ''; }
      else codeRow.style.display = 'none';

      const notesRow = document.getElementById('admMr_notesRow');
      if(r.notes){ document.getElementById('admMr_notes').textContent = r.notes; notesRow.style.display = 'block'; }
      else notesRow.style.display = 'none';

      document.getElementById('admMr_detailLink').href = r.detailUrl;
      document.getElementById('admMr_cancelForm').action = r.cancelUrl;

      const cashForm = document.getElementById('admMr_cashForm');
      if(r.status === 'PENDING_CASH'){
        cashForm.action = r.cashUrl;
        cashForm.style.display = '';
      } else {
        cashForm.style.display = 'none';
      }

      document.getElementById('admModRes').hidden = false;
    };

    // ── Click en celda bloqueada → modal con opción de quitar ─────────────
    window.admOpenBlock = function(el){
      const b = JSON.parse(el.dataset.blk);
      document.getElementById('admMb_field').textContent = b.field;
      document.getElementById('admMb_time').textContent = b.time;
      const reasonRow = document.getElementById('admMb_reasonRow');
      if(b.reason){ document.getElementById('admMb_reason').textContent = b.reason; reasonRow.style.display = 'block'; }
      else reasonRow.style.display = 'none';
      document.getElementById('admMb_destroyForm').action = b.destroyUrl;
      document.getElementById('admModBlk').hidden = false;
    };

    // ── Búsqueda de usuarios (autocomplete) ───────────────────────────────
    let searchTimer = null;
    document.getElementById('admMm_userSearch').addEventListener('input', e => {
      const q = e.target.value.trim();
      document.getElementById('admMm_clientUserId').value = ''; // reset
      const box = document.getElementById('admMm_userResults');
      clearTimeout(searchTimer);
      if(q.length < 3){ box.innerHTML = ''; return; }
      searchTimer = setTimeout(async () => {
        try{
          const r = await fetch(SEARCH_USERS_URL + '?q=' + encodeURIComponent(q), {
            headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}
          });
          const users = await r.json();
          if(!users.length){ box.innerHTML = '<div class="adm-search-result" style="color:#9ca3af; cursor:default;">Sin resultados</div>'; return; }
          box.innerHTML = users.map(u => `
            <div class="adm-search-result" data-id="${u.id}" data-name="${u.name.replace(/"/g,'&quot;')}">
              <b>${u.name}</b><span>${u.email}</span>
            </div>
          `).join('');
          box.querySelectorAll('.adm-search-result[data-id]').forEach(div => {
            div.addEventListener('click', () => {
              document.getElementById('admMm_clientUserId').value = div.dataset.id;
              document.getElementById('admMm_userSearch').value = div.dataset.name;
              box.innerHTML = '';
            });
          });
        }catch(err){
          box.innerHTML = '<div class="adm-search-result" style="color:#ef4444; cursor:default;">Error al buscar</div>';
        }
      }, 250);
    });

    // ── Detect grid horizontal overflow → fade indicator ──────────────────
    const card = document.getElementById('admGridCard');
    const scr  = document.getElementById('admGridScroll');
    if(card && scr){
      const updateOv = () => {
        const ov = scr.scrollWidth - scr.clientWidth - scr.scrollLeft > 4;
        card.classList.toggle('has-overflow', ov);
      };
      updateOv();
      scr.addEventListener('scroll', updateOv);
      window.addEventListener('resize', updateOv);
    }

    // Auto-scroll a la hora actual si HOY
    @if($date->isToday())
      requestAnimationFrame(() => {
        const now = new Date();
        const nowH = String(now.getHours()).padStart(2,'0') + ':00';
        const headers = document.querySelectorAll('.adm-h-hour');
        const target = Array.from(headers).find(h => h.textContent.trim() >= nowH);
        if(target && scr){
          const fnameW = parseInt(getComputedStyle(document.querySelector('.adm-grid')).gridTemplateColumns.split(' ')[0]) || 220;
          scr.scrollLeft = Math.max(0, target.offsetLeft - fnameW - 16);
        }
      });
    @endif
  })();
  </script>
@endif

{{-- ════════════════════════════════════════════════════════ VISTA SEMANAL ══ --}}
@elseif($view === 'week')

@php
  $prevWeek  = $weekStart->copy()->subDays(7)->toDateString();
  $nextWeek  = $weekStart->copy()->addDays(7)->toDateString();
  $weekEnd4Display = $weekStart->copy()->addDays(6);
  $weekTotal = $reservations->sum('total_amount');
  $weekCount = $reservations->count();

  $diasEs = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
@endphp

<div class="agenda-nav">
  <a class="agenda-nav-arrow"
     href="{{ route('va.reservations.agenda', array_filter(['date' => $prevWeek, 'field_id' => $fieldId, 'view' => 'week'])) }}"
     title="Semana anterior">&#8592;</a>
  <span class="agenda-date-label">
    {{ ucfirst($weekStart->translatedFormat('d \d\e F')) }}
    &ndash;
    {{ ucfirst($weekEnd4Display->translatedFormat('d \d\e F Y')) }}
  </span>
  <a class="agenda-nav-arrow"
     href="{{ route('va.reservations.agenda', array_filter(['date' => $nextWeek, 'field_id' => $fieldId, 'view' => 'week'])) }}"
     title="Semana siguiente">&#8594;</a>
</div>

<form method="GET" action="{{ route('va.reservations.agenda') }}" class="agenda-filters">
  <input type="hidden" name="view" value="week">
  <input type="hidden" name="date" value="{{ $weekStart->toDateString() }}">
  <div class="agenda-filter-group">
    <label>Cancha</label>
    <select name="field_id" onchange="this.form.submit()">
      @foreach($fields as $field)
        <option value="{{ $field->id }}" {{ (string)($fieldId ?? '') === (string)$field->id ? 'selected' : '' }}>
          {{ $field->name }} — {{ $field->venue->name }}
        </option>
      @endforeach
    </select>
  </div>
  <a href="{{ route('va.reservations.agenda', array_filter(['view' => 'week', 'field_id' => $fieldId])) }}"
     class="btn-ghost">Esta semana</a>
</form>

<div class="agenda-summary">
  <div class="agenda-stat">
    <div class="agenda-stat-value">{{ $weekCount }}</div>
    <div class="agenda-stat-label">Reservas esta semana</div>
  </div>
  <div class="agenda-stat">
    <div class="agenda-stat-value">${{ number_format($weekTotal, 0, ',', '.') }}</div>
    <div class="agenda-stat-label">Ingresos de la semana</div>
  </div>
  @if($selectedField)
    <div class="agenda-stat">
      <div class="agenda-stat-value" style="font-size:16px;">{{ $selectedField->name }}</div>
      <div class="agenda-stat-label">{{ $selectedField->venue->name }}</div>
    </div>
  @endif
</div>

@if($fields->isEmpty())
  <div class="admin-card" style="color:#666; text-align:center; padding:40px;">
    No tenés canchas configuradas aún.
  </div>
@elseif(!$selectedField)
  <div class="admin-card" style="color:#666; text-align:center; padding:40px;">
    Seleccioná una cancha para ver la agenda semanal.
  </div>
@elseif(empty($slots))
  <div class="admin-card" style="color:#666; text-align:center; padding:40px;">
    No hay horarios configurados para esta cancha.
  </div>
@else
  {{-- Misma leyenda que el día --}}
  <div class="adm-legend">
    <span><i class="adm-l-free"></i>Libre · clic para reservar</span>
    <span><i class="adm-l-paid"></i>Pagada</span>
    <span><i class="adm-l-cash"></i>Efectivo</span>
    <span><i class="adm-l-pend"></i>Pendiente pago</span>
    <span><i class="adm-l-blk"></i>Bloqueado</span>
    <span><i class="adm-l-past"></i>Pasado</span>
    <span style="margin-left:auto; font-style:italic;">Tip: arrastrá sobre celdas libres del mismo día para crear un bloqueo</span>
  </div>

  <div class="adm-grid-wrap">
    <div class="adm-grid-card" id="admGridCard">
      <div class="adm-grid-scroll" id="admGridScroll">
        @php
          $totalCols = count($slots);
          $tplCols = "var(--adm-fname-w) repeat({$totalCols}, minmax(var(--adm-cell-w), 1fr))";
        @endphp
        <div class="adm-grid" style="grid-template-columns: {{ $tplCols }};">
          {{-- Header --}}
          <div class="adm-h-corner">{{ $selectedField->name }}</div>
          @foreach($slots as $slot)
            <div class="adm-h-hour">{{ $slot }}</div>
          @endforeach

          {{-- Filas: una por día de la semana --}}
          @foreach($weekDays as $day)
            @php
              $dateKey   = $day->format('Y-m-d');
              $isTodayRow = $day->isToday();
              $dow       = $day->dayOfWeek;
              $schedule  = $selectedField->schedules->firstWhere('day_of_week', $dow);
              $exception = $selectedField->exceptions?->first(fn ($e) => $e->date->toDateString() === $dateKey);
              $openTime  = $exception?->open_time  ?? optional($schedule)->open_time;
              $closeTime = $exception?->close_time ?? optional($schedule)->close_time;
              $dayOpen  = $openTime  ? \Carbon\Carbon::parse($dateKey . ' ' . $openTime)  : null;
              $dayClose = $closeTime ? \Carbon\Carbon::parse($dateKey . ' ' . $closeTime) : null;
              $dayLabel = ucfirst($day->locale('es')->isoFormat('ddd D MMM'));
            @endphp
            <div class="adm-fname" @if($isTodayRow) style="background:#f0fdf4;" @endif>
              <div class="adm-fname-name">
                {{ $diasEs[$day->dayOfWeek] }} {{ $day->format('d/m') }}
                @if($isTodayRow)
                  <span style="display:inline-block; font-size:9px; background:#10b981; color:#fff; padding:2px 6px; border-radius:99px; vertical-align:middle; margin-left:4px; font-weight:700;">HOY</span>
                @endif
              </div>
              <div class="adm-fname-meta">
                @if($exception?->is_closed)
                  Cerrada
                @elseif($dayOpen && $dayClose)
                  {{ $openTime }}–{{ $closeTime }}
                @else
                  Sin horario
                @endif
              </div>
            </div>

            @php $skipCols = 0; @endphp
            @foreach($slots as $slot)
              @if($skipCols > 0)
                @php $skipCols--; @endphp
                @continue
              @endif

              @php
                $key   = $dateKey . '|' . $slot;
                $cell  = $weekCellMap[$key] ?? null;
                $slotDt = \Carbon\Carbon::parse($dateKey . ' ' . $slot);
                $isPast = $slotDt->lt(now());
                $inSched = ($exception?->is_closed)
                  ? false
                  : ($dayOpen && $dayClose && $slotDt >= $dayOpen && $slotDt < $dayClose);
              @endphp

              @if(!$inSched && !$cell)
                <div class="adm-cell adm-cell-closed" title="Fuera de horario"></div>
              @elseif($cell && $cell['type'] === 'reservation')
                @php
                  $r = $cell['data'];
                  $sp = max(1, (int) $cell['span']);
                  $skipCols = $sp - 1;
                  $resJsonAttrs = json_encode([
                    'id'      => $r->id,
                    'name'    => $r->user->name ?? 'Sin usuario',
                    'email'   => $r->user->email ?? '',
                    'time'    => $r->start_at->format('H:i') . ' – ' . $r->end_at->format('H:i'),
                    'date'    => $r->start_at->isoFormat('dddd D [de] MMMM'),
                    'field'   => $selectedField->name,
                    'venue'   => $selectedField->venue->name,
                    'status'  => $r->status,
                    'statusLabel' => ReservationStatus::label($r->status),
                    'total'   => $r->total_amount,
                    'currency'=> $r->currency,
                    'code'    => $r->verification_code,
                    'notes'   => $r->notes,
                    'detailUrl' => route('reservations.show', $r),
                    'cancelUrl' => route('va.reservations.cancel', $r),
                    'cashUrl'   => route('va.reservations.confirm_cash', $r),
                  ], JSON_UNESCAPED_UNICODE);
                @endphp
                <div class="adm-cell adm-cell-res adm-status-{{ $r->status }}"
                     style="grid-column: span {{ $sp }};"
                     data-res='{{ $resJsonAttrs }}'
                     onclick="admOpenReservation(this)"
                     title="{{ $r->user->name ?? 'Sin usuario' }} · {{ $r->start_at->format('H:i') }}–{{ $r->end_at->format('H:i') }}">
                  <div class="adm-res-name">{{ $r->user->name ?? 'Sin usuario' }}</div>
                  <div class="adm-res-time">{{ $r->start_at->format('H:i') }} – {{ $r->end_at->format('H:i') }}</div>
                  @if($r->total_amount)
                    <div class="adm-res-price">${{ number_format($r->total_amount, 0, ',', '.') }}</div>
                  @endif
                  @if($r->status === 'PENDING_CASH')
                    <span class="adm-res-badge">EFECTIVO</span>
                  @elseif($r->status === 'PENDING_PAYMENT')
                    <span class="adm-res-badge">PENDIENTE</span>
                  @endif
                </div>
              @elseif($cell && $cell['type'] === 'block')
                @php
                  $b  = $cell['data'];
                  $sp = max(1, (int) $cell['span']);
                  $skipCols = $sp - 1;
                  $blkJson = json_encode([
                    'id'        => $b->id,
                    'time'      => substr($b->start_time, 0, 5) . ' – ' . substr($b->end_time, 0, 5),
                    'reason'    => $b->reason,
                    'field'     => $selectedField->name,
                    'destroyUrl'=> route('va.blocks.destroy', $b),
                  ], JSON_UNESCAPED_UNICODE);
                @endphp
                <div class="adm-cell adm-cell-blk"
                     style="grid-column: span {{ $sp }};"
                     data-blk='{{ $blkJson }}'
                     onclick="admOpenBlock(this)"
                     title="Bloqueo {{ substr($b->start_time, 0, 5) }}–{{ substr($b->end_time, 0, 5) }}">
                  <div class="adm-blk-label">Bloqueado</div>
                  @if($b->reason)
                    <div class="adm-blk-reason">{{ $b->reason }}</div>
                  @endif
                </div>
              @elseif($isPast)
                <div class="adm-cell adm-cell-past">—</div>
              @else
                <div class="adm-cell adm-cell-free"
                     data-field="{{ $selectedField->id }}"
                     data-field-name="{{ $selectedField->name }}"
                     data-venue-name="{{ $selectedField->venue->name }}"
                     data-date="{{ $dateKey }}"
                     data-date-label="{{ $diasEs[$day->dayOfWeek] }} {{ $day->format('d/m') }}"
                     data-slot="{{ $slot }}"
                     data-slot-minutes="{{ $selectedField->slot_minutes ?: 60 }}"></div>
              @endif
            @endforeach
          @endforeach
        </div>
      </div>
    </div>
  </div>
@endif

@endif
{{-- fin if view --}}

<div class="agenda-footer">
  <a href="{{ route('va.reservations.index', array_filter(['date' => $date->toDateString()])) }}">Ver como tabla</a>
  &nbsp;—&nbsp;
  <a href="{{ route('va.dashboard') }}">Volver al panel</a>
</div>

{{-- ─── Real-time: refresca el agenda cuando cambia disponibilidad de alguna cancha ─── --}}
@if(isset($fields) && $fields->count() > 0)
<script>
(function(){
  if(typeof window.Echo === 'undefined') return;
  const fieldIds = @json($fields->pluck('id')->all());
  let reloadTimer = null;
  let toastShown = false;

  function showRealtimeToast(){
    if(toastShown) return;
    toastShown = true;
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#111;color:#fff;padding:12px 18px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.3);z-index:9999;font-size:13px;font-weight:600;display:flex;align-items:center;gap:10px;';
    t.innerHTML = '<span style="display:inline-block;width:8px;height:8px;background:#10b981;border-radius:50%;animation:pulse 1.5s infinite;"></span> Actualización detectada — refrescando…';
    document.body.appendChild(t);
  }

  fieldIds.forEach(fid => {
    try{
      window.Echo.channel('field.' + fid).listen('.availability.changed', () => {
        clearTimeout(reloadTimer);
        showRealtimeToast();
        // Debounce 800ms — si llegan varios eventos seguidos (ej: cancelación masiva), recargar una sola vez
        reloadTimer = setTimeout(() => { window.location.reload(); }, 800);
      });
    }catch(e){ /* sin Echo, ignorar */ }
  });
})();
</script>
<style>@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}</style>
@endif

@endsection
