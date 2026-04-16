@extends('layouts.app')

@section('title', 'Gestionar Torneo — ' . $tournament->name)

@push('styles')
<style>
  /* ── Layout ──────────────────────────────────────── */
  .tm-wrap {
    max-width: 900px;
    margin: 0 auto;
    padding: 32px 16px 80px;
  }

  /* ── Cards ───────────────────────────────────────── */
  .tm-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    padding: 28px;
    margin-bottom: 24px;
  }
  .tm-card-title {
    font-size: 18px;
    font-weight: 800;
    color: #e8e8e8;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .tm-card-title svg {
    width: 20px;
    height: 20px;
    color: var(--color-primary);
  }

  /* ── Header ──────────────────────────────────────── */
  .tm-header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 8px;
  }
  .tm-header-left {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .tm-header-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
  }
  .tm-name {
    font-size: 28px;
    font-weight: 900;
    color: #e8e8e8;
    letter-spacing: -.02em;
    margin: 0;
    line-height: 1.15;
  }
  .tm-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
  }

  /* ── Badges ──────────────────────────────────────── */
  .tm-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .02em;
    text-transform: uppercase;
    border: 1px solid transparent;
  }
  .tm-badge-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
  }
  /* Status badges */
  .tm-badge-draft         { background: rgba(255,255,255,.06); color: #a0a0a0; border-color: rgba(255,255,255,.1); }
  .tm-badge-draft .tm-badge-dot { background: #666; }
  .tm-badge-open_registration { background: rgba(34,197,94,.12); color: #22c55e; border-color: rgba(34,197,94,.3); }
  .tm-badge-open_registration .tm-badge-dot { background: #22c55e; }
  .tm-badge-registration_closed { background: rgba(245,158,11,.1); color: #fbbf24; border-color: rgba(245,158,11,.3); }
  .tm-badge-registration_closed .tm-badge-dot { background: #f59e0b; }
  .tm-badge-in_progress   { background: rgba(59,130,246,.12); color: #60a5fa; border-color: rgba(59,130,246,.3); }
  .tm-badge-in_progress .tm-badge-dot { background: #3b82f6; }
  .tm-badge-finished      { background: rgba(16,185,129,.12); color: #34d399; border-color: rgba(16,185,129,.3); }
  .tm-badge-finished .tm-badge-dot { background: #10b981; }
  .tm-badge-cancelled     { background: rgba(239,68,68,.1); color: #f87171; border-color: rgba(239,68,68,.3); }
  .tm-badge-cancelled .tm-badge-dot { background: #ef4444; }
  /* Sport badges */
  .tm-badge-football      { background: rgba(34,197,94,.12); color: #4ade80; border-color: rgba(34,197,94,.3); }
  .tm-badge-padel         { background: rgba(124,58,237,.12); color: #a78bfa; border-color: rgba(124,58,237,.3); }
  .tm-badge-tennis        { background: rgba(212,184,150,.12); color: #e8d5b7; border-color: rgba(212,184,150,.3); }
  .tm-badge-basketball    { background: rgba(249,115,22,.12); color: #fb923c; border-color: rgba(249,115,22,.3); }
  .tm-badge-volleyball    { background: rgba(59,130,246,.12); color: #60a5fa; border-color: rgba(59,130,246,.3); }

  /* ── Buttons ─────────────────────────────────────── */
  .tm-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 20px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    font-family: inherit;
    text-decoration: none;
    transition: background .15s, transform .1s, box-shadow .15s;
  }
  .tm-btn:hover { transform: translateY(-1px); }
  .tm-btn svg { width: 16px; height: 16px; }
  .tm-btn-primary {
    background: var(--color-primary);
    color: #052e16;
    box-shadow: 0 4px 14px rgba(34,197,94,.3);
  }
  .tm-btn-primary:hover { background: var(--color-primary-hover); box-shadow: 0 6px 20px rgba(34,197,94,.4); }
  .tm-btn-outline {
    background: transparent;
    color: #a0a0a0;
    border: 1.5px solid rgba(255,255,255,.12);
  }
  .tm-btn-outline:hover { border-color: rgba(255,255,255,.2); background: #1a1a1a; }
  .tm-btn-danger {
    background: rgba(239,68,68,.1);
    color: #f87171;
    border: 1px solid rgba(239,68,68,.25);
  }
  .tm-btn-danger:hover { background: rgba(239,68,68,.2); }
  .tm-btn-sm {
    padding: 6px 14px;
    font-size: 12px;
    border-radius: 10px;
  }
  .tm-btn-sm svg { width: 14px; height: 14px; }
  .tm-status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 20px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    background: rgba(255,255,255,.06);
    color: #a0a0a0;
  }
  .tm-status-indicator svg { width: 16px; height: 16px; }

  /* ── Info grid ───────────────────────────────────── */
  .tm-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
  }
  .tm-info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }
  .tm-info-label {
    font-size: 11px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    letter-spacing: .06em;
  }
  .tm-info-value {
    font-size: 15px;
    font-weight: 600;
    color: #e8e8e8;
  }

  /* ── Teams table ─────────────────────────────────── */
  .tm-teams-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
  }
  .tm-teams-table th {
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    letter-spacing: .06em;
    padding: 0 12px 12px;
    border-bottom: 1px solid rgba(255,255,255,.06);
  }
  .tm-teams-table td {
    padding: 14px 12px;
    font-size: 14px;
    color: #a0a0a0;
    border-bottom: 1px solid rgba(255,255,255,.04);
    vertical-align: middle;
  }
  .tm-teams-table tr:last-child td { border-bottom: none; }
  .tm-team-name {
    font-weight: 700;
    color: #e8e8e8;
  }
  .tm-team-status {
    display: inline-flex;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
  }
  .tm-team-status-confirmed { background: rgba(34,197,94,.12); color: #22c55e; }
  .tm-team-status-disqualified { background: rgba(239,68,68,.1); color: #f87171; }
  .tm-team-status-withdrawn { background: rgba(255,255,255,.06); color: #a0a0a0; }
  .tm-team-actions {
    display: flex;
    gap: 8px;
    align-items: center;
  }
  .tm-teams-empty {
    text-align: center;
    padding: 40px 20px;
    color: #666;
    font-size: 14px;
  }
  .tm-teams-empty svg {
    width: 40px;
    height: 40px;
    margin-bottom: 12px;
    color: #444;
  }

  /* ── Matches / Bracket ───────────────────────────── */
  .tm-round-group {
    margin-bottom: 28px;
  }
  .tm-round-group:last-child { margin-bottom: 0; }
  .tm-round-title {
    font-size: 14px;
    font-weight: 800;
    color: #a0a0a0;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin: 0 0 14px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .tm-round-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,255,255,.06);
  }
  .tm-match-card {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,.06);
    border-radius: 14px;
    padding: 16px 20px;
    margin-bottom: 10px;
    transition: border-color .15s;
  }
  .tm-match-card:hover { border-color: rgba(255,255,255,.12); }
  .tm-match-card:last-child { margin-bottom: 0; }
  .tm-match-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .tm-match-number {
    font-size: 11px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
    letter-spacing: .04em;
  }
  .tm-match-status {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 6px;
  }
  .tm-match-status-scheduled { background: rgba(255,255,255,.06); color: #a0a0a0; }
  .tm-match-status-finished { background: rgba(34,197,94,.12); color: #22c55e; }
  .tm-match-status-walkover { background: rgba(245,158,11,.1); color: #fbbf24; }
  .tm-match-status-cancelled { background: rgba(239,68,68,.1); color: #f87171; }
  .tm-match-teams {
    display: flex;
    align-items: center;
    gap: 14px;
    justify-content: center;
  }
  .tm-match-team {
    flex: 1;
    text-align: center;
    font-size: 15px;
    font-weight: 700;
    color: #e8e8e8;
  }
  .tm-match-team-tbd {
    color: #444;
    font-style: italic;
    font-weight: 500;
  }
  .tm-match-vs {
    font-size: 12px;
    font-weight: 800;
    color: #444;
    text-transform: uppercase;
    flex-shrink: 0;
  }
  .tm-match-score {
    font-size: 22px;
    font-weight: 900;
    color: #e8e8e8;
    text-align: center;
    margin-top: 8px;
    letter-spacing: .02em;
  }
  .tm-match-score-pen {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    display: block;
    margin-top: 2px;
  }
  .tm-match-winner {
    color: var(--color-primary);
  }
  .tm-match-wo {
    text-align: center;
    margin-top: 8px;
    font-size: 13px;
    font-weight: 800;
    color: #f59e0b;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  /* ── Result form ─────────────────────────────────── */
  .tm-result-form {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    margin-top: 12px;
    flex-wrap: wrap;
  }
  .tm-result-input {
    width: 60px;
    padding: 8px;
    text-align: center;
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 10px;
    font-size: 16px;
    font-weight: 700;
    font-family: inherit;
    color: #e8e8e8;
    background: #0a0a0a;
    transition: border-color .15s;
  }
  .tm-result-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(34,197,94,.12);
  }
  .tm-result-sep {
    font-size: 18px;
    font-weight: 800;
    color: #444;
  }
  .tm-result-pen-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
    width: 100%;
    justify-content: center;
    margin-top: 4px;
  }
  .tm-result-pen-label {
    font-size: 11px;
    font-weight: 700;
    color: #666;
    text-transform: uppercase;
  }
  .tm-result-pen-input {
    width: 52px;
    padding: 6px;
    text-align: center;
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 700;
    font-family: inherit;
    color: #e8e8e8;
    background: #0a0a0a;
    transition: border-color .15s;
  }
  .tm-result-pen-input:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(34,197,94,.12);
  }

  /* ── Share ───────────────────────────────────────── */
  .tm-share-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    align-items: center;
  }
  .tm-share-url {
    flex: 1;
    min-width: 200px;
    padding: 10px 14px;
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 10px;
    font-size: 13px;
    color: #a0a0a0;
    background: #0a0a0a;
    font-family: monospace;
    word-break: break-all;
  }
  .tm-btn-whatsapp {
    background: #25d366;
    color: #fff;
  }
  .tm-btn-whatsapp:hover { background: #1eb851; }
  .tm-btn-copy {
    background: rgba(255,255,255,.06);
    color: #a0a0a0;
    border: 1px solid rgba(255,255,255,.1);
  }
  .tm-btn-copy:hover { background: rgba(255,255,255,.1); }

  /* ── Flash ───────────────────────────────────────── */
  .tm-flash {
    padding: 14px 20px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .tm-flash svg { width: 18px; height: 18px; flex-shrink: 0; }
  .tm-flash-success { background: rgba(34,197,94,.1); color: #22c55e; border: 1px solid rgba(34,197,94,.25); }
  .tm-flash-error { background: rgba(239,68,68,.1); color: #f87171; border: 1px solid rgba(239,68,68,.25); }

  /* ── Responsive ──────────────────────────────────── */
  @media (max-width: 640px) {
    .tm-wrap { padding: 20px 12px 60px; }
    .tm-card { padding: 20px 16px; border-radius: 16px; }
    .tm-name { font-size: 22px; }
    .tm-header { flex-direction: column; }
    .tm-actions { width: 100%; }
    .tm-info-grid { grid-template-columns: 1fr 1fr; }
    .tm-teams-table { font-size: 13px; }
    .tm-teams-table th,
    .tm-teams-table td { padding: 10px 8px; }
    .tm-match-teams { flex-direction: column; gap: 6px; }
    .tm-match-team { text-align: center; }
    .tm-result-form { flex-direction: column; gap: 6px; }
    .tm-share-row { flex-direction: column; }
    .tm-share-url { width: 100%; }
  }
</style>
@endpush

@section('content')
<div class="tm-wrap" x-data="tournamentManage()">

  {{-- ── Flash messages ──────────────────────────── --}}
  @if(session('success'))
    <div class="tm-flash tm-flash-success">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="tm-flash tm-flash-error">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      {{ session('error') }}
    </div>
  @endif
  @if($errors->any())
    <div class="tm-flash tm-flash-error">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      {{ $errors->first() }}
    </div>
  @endif

  {{-- ══════════════════════════════════════════════ --}}
  {{-- 1. HEADER                                      --}}
  {{-- ══════════════════════════════════════════════ --}}
  <div class="tm-card">
    <div class="tm-header">
      <div class="tm-header-left">
        <div class="tm-header-badges">
          @php
            $sportLabels = ['football' => 'Futbol', 'padel' => 'Padel', 'tennis' => 'Tenis', 'basketball' => 'Basquet', 'volleyball' => 'Voley'];
            $statusLabels = [
              'draft' => 'Borrador',
              'open_registration' => 'Inscripcion abierta',
              'registration_closed' => 'Inscripcion cerrada',
              'in_progress' => 'En curso',
              'finished' => 'Finalizado',
              'cancelled' => 'Cancelado',
            ];
          @endphp
          <span class="tm-badge tm-badge-{{ $tournament->sport }}">
            {{ $sportLabels[$tournament->sport] ?? ucfirst($tournament->sport) }}
          </span>
          <span class="tm-badge tm-badge-{{ $tournament->status }}">
            <span class="tm-badge-dot"></span>
            {{ $statusLabels[$tournament->status] ?? ucfirst($tournament->status) }}
          </span>
        </div>
        <h1 class="tm-name">{{ $tournament->name }}</h1>
      </div>

      <div class="tm-actions">
        @if($tournament->status === 'draft')
          @php
              $pendingReqs = collect($venueRequestsData)->where('status', 'pending');
              $rejectedReqs = collect($venueRequestsData)->where('status', 'rejected');
              $approvedReqs = collect($venueRequestsData)->where('status', 'approved');
              $hasPending = $pendingReqs->isNotEmpty();
              $hasRejected = $rejectedReqs->isNotEmpty();
              $hasSchedulesPending = $approvedReqs->contains(fn($r) => !isset($r['schedule']) || !$r['schedule'] || $r['schedule']['status'] !== 'approved');
              $showVenueSection = $hasPending || $hasRejected || $hasSchedulesPending;
          @endphp
          @if($showVenueSection)

            {{-- Pending requests --}}
            @if($hasPending)
            <div style="display:flex;flex-direction:column;gap:8px;padding:14px 18px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.3);border-radius:12px;font-size:13px;color:#fbbf24;">
              <div style="display:flex;align-items:center;gap:8px;">
                <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <span style="font-weight:700;">{{ $pendingReqs->count() }} cancha(s) pendiente(s) de aprobacion</span>
              </div>
              @foreach($pendingReqs as $pReq)
                <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#1a1a1a;border-radius:8px;">
                  @if($pReq['contact_method'] === 'whatsapp' && $pReq['contact_value'])
                    <svg style="width:18px;height:18px;color:#25d366;flex-shrink:0;" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.612.638l4.603-1.209A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.352 0-4.55-.764-6.332-2.058l-.182-.137-3.223.846.862-3.149-.15-.237A9.935 9.935 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                    <div style="flex:1;min-width:0;">
                      <span style="font-weight:700;">{{ $pReq['field_name'] }}</span>
                      <span style="color:#666;"> — {{ $pReq['venue_name'] }}</span>
                      <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pReq['contact_value']) }}?text={{ urlencode('Hola! Soy organizador del torneo ' . $tournament->name . ' en TuCancha. Quería consultar sobre la solicitud para usar la cancha.') }}"
                         target="_blank" rel="noopener"
                         style="display:inline-flex;align-items:center;gap:4px;margin-left:6px;font-weight:700;color:#25d366;text-decoration:none;">
                        {{ $pReq['contact_value'] }}
                        <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                      </a>
                    </div>
                  @elseif($pReq['contact_method'] === 'phone' && $pReq['contact_value'])
                    <svg style="width:16px;height:16px;color:#fbbf24;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <div style="flex:1;min-width:0;">
                      <span style="font-weight:700;">{{ $pReq['field_name'] }}</span>
                      <span style="color:#666;"> — {{ $pReq['venue_name'] }}</span>
                      <a href="tel:{{ $pReq['contact_value'] }}" style="margin-left:6px;font-weight:700;color:#fbbf24;text-decoration:none;">{{ $pReq['contact_value'] }}</a>
                    </div>
                  @elseif($pReq['contact_method'] === 'email' && $pReq['contact_value'])
                    <svg style="width:16px;height:16px;color:#fbbf24;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <div style="flex:1;min-width:0;">
                      <span style="font-weight:700;">{{ $pReq['field_name'] }}</span>
                      <span style="color:#666;"> — {{ $pReq['venue_name'] }}</span>
                    </div>
                    <span style="position:relative;">
                      <button type="button"
                         onclick="navigator.clipboard.writeText('{{ $pReq['contact_value'] }}');var b=this;b.querySelector('.tc-copy-label').textContent='Copiado!';setTimeout(function(){b.querySelector('.tc-copy-label').textContent='{{ $pReq['contact_value'] }}'},2000);"
                         style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#d97706;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;flex-shrink:0;font-family:inherit;transition:background .15s;"
                         onmouseover="this.style.background='#b45309';this.parentElement.querySelector('.tc-tooltip').style.display='block'"
                         onmouseout="this.style.background='#d97706';this.parentElement.querySelector('.tc-tooltip').style.display='none'">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="tc-copy-label">{{ $pReq['contact_value'] }}</span>
                      </button>
                      <span class="tc-tooltip" style="display:none;position:absolute;bottom:calc(100% + 8px);right:0;background:#22c55e;color:#050505;font-size:11px;font-weight:600;padding:8px 12px;border-radius:8px;white-space:nowrap;pointer-events:none;z-index:10;box-shadow:0 4px 12px rgba(0,0,0,.15);">
                        Hace click para copiar el email y escribile al complejo
                        <span style="position:absolute;bottom:-4px;right:16px;width:8px;height:8px;background:#111;transform:rotate(45deg);"></span>
                      </span>
                    </span>
                  @else
                    {{-- Internal chat --}}
                    <svg style="width:16px;height:16px;color:#fbbf24;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <div style="flex:1;min-width:0;">
                      <span style="font-weight:700;">{{ $pReq['field_name'] }}</span>
                      <span style="color:#666;"> — {{ $pReq['venue_name'] }}</span>
                    </div>
                    <button type="button" onclick="openTcChat({{ $pReq['id'] }})"
                            style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.25);border-radius:10px;font-size:12px;font-weight:700;color:#22c55e;cursor:pointer;font-family:inherit;transition:all .15s;"
                            onmouseover="this.style.background='rgba(34,197,94,.15)';this.style.borderColor='rgba(34,197,94,.4)'"
                            onmouseout="this.style.background='rgba(34,197,94,.08)';this.style.borderColor='rgba(34,197,94,.25)'">
                      <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                      Abrir chat
                      @if(($pReq['unread_count'] ?? 0) > 0)
                        <span id="tc-unread-badge-{{ $pReq['id'] }}" style="background:#22c55e;color:#052e16;font-size:10px;font-weight:800;min-width:18px;height:18px;border-radius:9px;display:inline-flex;align-items:center;justify-content:center;padding:0 5px;">{{ $pReq['unread_count'] }}</span>
                      @endif
                    </button>
                  @endif
                </div>
              @endforeach
            </div>
            @endif

            {{-- Rejected requests --}}
            @if($hasRejected)
            <div style="display:flex;flex-direction:column;gap:8px;padding:14px 18px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:12px;font-size:13px;color:#f87171;">
              <div style="display:flex;align-items:center;gap:8px;">
                <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                <span style="font-weight:700;">{{ $rejectedReqs->count() }} cancha(s) rechazada(s)</span>
              </div>
              @foreach($rejectedReqs as $rReq)
                <div style="padding:6px 12px;background:#1a1a1a;border-radius:8px;">
                  <span style="font-weight:700;">{{ $rReq['field_name'] }}</span>
                  <span style="color:#666;"> — {{ $rReq['venue_name'] }}</span>
                  @if($rReq['response_message'])
                    <span style="color:#f87171;"> — {{ $rReq['response_message'] }}</span>
                  @endif
                </div>
              @endforeach
              <span style="font-size:12px;">Edita el torneo para quitar canchas rechazadas o elegir otras.</span>
            </div>
            @endif

            {{-- Approved requests — schedule phase --}}
            @if($approvedReqs->isNotEmpty())
            @foreach($approvedReqs as $aReq)
              @php $schedule = $aReq['schedule'] ?? null; @endphp

              @if($schedule && $schedule['status'] === 'approved')
                {{-- Schedule confirmed --}}
                <div style="display:flex;flex-direction:column;gap:8px;padding:14px 18px;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);border-radius:12px;font-size:13px;color:#22c55e;">
                  <div style="display:flex;align-items:center;gap:8px;">
                    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="font-weight:700;">{{ $aReq['field_name'] }} — {{ $aReq['venue_name'] }}</span>
                    <span style="font-size:11px;padding:2px 8px;background:rgba(34,197,94,.12);border-radius:6px;font-weight:700;">Horarios confirmados</span>
                  </div>
                  <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($schedule['slots'] as $slot)
                      <span style="padding:4px 10px;background:#1a1a1a;border:1px solid rgba(34,197,94,.25);border-radius:8px;font-size:12px;font-weight:600;">
                        {{ \Carbon\Carbon::parse($slot['date'])->format('d/m') }} {{ $slot['start_time'] }}–{{ $slot['end_time'] }}
                      </span>
                    @endforeach
                  </div>
                </div>

              @elseif($schedule && $schedule['status'] === 'pending')
                {{-- Schedule sent, waiting for approval --}}
                <div style="display:flex;flex-direction:column;gap:8px;padding:14px 18px;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.25);border-radius:12px;font-size:13px;color:#60a5fa;">
                  <div style="display:flex;align-items:center;gap:8px;">
                    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span style="font-weight:700;">{{ $aReq['field_name'] }} — {{ $aReq['venue_name'] }}</span>
                    <span style="font-size:11px;padding:2px 8px;background:rgba(59,130,246,.15);border-radius:6px;font-weight:700;">Esperando aprobacion de horarios</span>
                  </div>
                  <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach($schedule['slots'] as $slot)
                      <span style="padding:4px 10px;background:#1a1a1a;border:1px solid rgba(59,130,246,.25);border-radius:8px;font-size:12px;font-weight:600;">
                        {{ \Carbon\Carbon::parse($slot['date'])->format('d/m') }} {{ $slot['start_time'] }}–{{ $slot['end_time'] }}
                      </span>
                    @endforeach
                  </div>
                </div>

              @elseif($schedule && $schedule['status'] === 'rejected')
                {{-- Schedule rejected — can resend --}}
                <div style="display:flex;flex-direction:column;gap:8px;padding:14px 18px;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.3);border-radius:12px;font-size:13px;color:#fbbf24;" x-data="{ showForm: false }">
                  <div style="display:flex;align-items:center;gap:8px;">
                    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    <span style="font-weight:700;">{{ $aReq['field_name'] }} — {{ $aReq['venue_name'] }}</span>
                    <span style="font-size:11px;padding:2px 8px;background:rgba(245,158,11,.2);border-radius:6px;font-weight:700;">Horarios rechazados</span>
                  </div>
                  @if($schedule['response_message'])
                    <p style="font-size:12px;margin:0;">Motivo: {{ $schedule['response_message'] }}</p>
                  @endif
                  <button type="button" @click="showForm = !showForm"
                          style="align-self:flex-start;display:inline-flex;align-items:center;gap:5px;padding:6px 14px;background:#d97706;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;font-family:inherit;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Enviar nuevos horarios
                  </button>
                  <div x-show="showForm" x-transition style="margin-top:4px;">
                    @include('torneos._schedule-form', ['tournament' => $tournament, 'venueRequestId' => $aReq['id'], 'fieldName' => $aReq['field_name'], 'venueName' => $aReq['venue_name']])
                  </div>
                </div>

              @else
                {{-- Approved but no schedule sent yet — show form directly --}}
                <div style="display:flex;flex-direction:column;gap:8px;padding:14px 18px;background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);border-radius:12px;font-size:13px;color:#22c55e;">
                  <div style="display:flex;align-items:center;gap:8px;">
                    <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="font-weight:700;">{{ $aReq['field_name'] }} — {{ $aReq['venue_name'] }}</span>
                    <span style="font-size:11px;padding:2px 8px;background:rgba(34,197,94,.12);border-radius:6px;font-weight:700;">Cancha aprobada</span>
                  </div>
                  <p style="margin:0;font-size:12px;">Envia los horarios que necesitas para que el complejo los confirme.</p>
                  @include('torneos._schedule-form', ['tournament' => $tournament, 'venueRequestId' => $aReq['id'], 'fieldName' => $aReq['field_name'], 'venueName' => $aReq['venue_name']])
                </div>
              @endif
            @endforeach
            @endif

            {{-- Show Publish button if all venues approved even if schedules pending --}}
            @if($tournament->venue_approval_status === 'approved')
            <form method="POST" action="{{ route('torneos.publish', $tournament) }}">
              @csrf
              <button type="submit" class="tm-btn tm-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Publicar
              </button>
            </form>
            @endif
          @elseif($tournament->venue_approval_status === 'rejected' && empty($venueRequestsData))
            <div style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:rgba(239,68,68,.1);border:1px solid #f87171;border-radius:10px;font-size:13px;color:#f87171;">
              <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
              <span>El complejo rechazo la solicitud{{ $tournament->venue_rejection_reason ? ': ' . $tournament->venue_rejection_reason : '' }}. Edita el torneo para elegir otra cancha.</span>
            </div>
          @else
            <form method="POST" action="{{ route('torneos.publish', $tournament) }}">
              @csrf
              <button type="submit" class="tm-btn tm-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                Publicar
              </button>
            </form>
          @endif
          <a href="{{ route('torneos.edit', $tournament) }}" class="tm-btn tm-btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Editar
          </a>

        @elseif($tournament->status === 'open_registration')
          <form method="POST" action="{{ route('torneos.close_registration', $tournament) }}" x-on:submit.prevent="confirmAction($event, '¿Cerrar la inscripcion? No se podran anotar mas equipos.')">
            @csrf
            <button type="submit" class="tm-btn tm-btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Cerrar Inscripcion
            </button>
          </form>
          <a href="{{ route('torneos.edit', $tournament) }}" class="tm-btn tm-btn-outline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Editar
          </a>

        @elseif($tournament->status === 'registration_closed')
          <form method="POST" action="{{ route('torneos.generate_fixture', $tournament) }}" x-on:submit.prevent="confirmAction($event, '¿Generar el fixture? Se crearan los partidos automaticamente.')">
            @csrf
            <button type="submit" class="tm-btn tm-btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 3 21 3 21 8"/><line x1="4" y1="20" x2="21" y2="3"/><polyline points="21 16 21 21 16 21"/><line x1="15" y1="15" x2="21" y2="21"/><line x1="4" y1="4" x2="9" y2="9"/></svg>
              Generar Fixture
            </button>
          </form>

        @elseif($tournament->status === 'in_progress')
          <span class="tm-status-indicator" style="background: rgba(59,130,246,.1); color: #1e40af;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            En curso
          </span>

        @elseif($tournament->status === 'finished')
          <span class="tm-status-indicator" style="background: rgba(34,197,94,.1); color: #22c55e;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Torneo finalizado
          </span>

        @elseif($tournament->status === 'cancelled')
          <span class="tm-status-indicator" style="background: rgba(239,68,68,.08); color: #f87171;">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            Cancelado
          </span>
        @endif

        @if(!in_array($tournament->status, ['finished', 'cancelled']))
          <form method="POST" action="{{ route('torneos.cancel', $tournament) }}" x-on:submit.prevent="confirmAction($event, '¿Cancelar el torneo? Esta accion no se puede deshacer.')">
            @csrf
            <button type="submit" class="tm-btn tm-btn-danger tm-btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
              Cancelar torneo
            </button>
          </form>
        @endif
      </div>
    </div>
  </div>

  {{-- ══════════════════════════════════════════════ --}}
  {{-- 2. INFO PANEL                                  --}}
  {{-- ══════════════════════════════════════════════ --}}
  <div class="tm-card">
    <h2 class="tm-card-title">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      Informacion del torneo
    </h2>
    <div class="tm-info-grid">
      <div class="tm-info-item">
        <span class="tm-info-label">Equipos inscriptos</span>
        <span class="tm-info-value">{{ $tournament->teams->where('status', 'confirmed')->count() }} / {{ $tournament->max_teams }}</span>
      </div>
      <div class="tm-info-item">
        <span class="tm-info-label">Deporte</span>
        <span class="tm-info-value">{{ $sportLabels[$tournament->sport] ?? ucfirst($tournament->sport) }}</span>
      </div>
      <div class="tm-info-item">
        <span class="tm-info-label">Formato</span>
        <span class="tm-info-value">{{ ucfirst(str_replace('_', ' ', $tournament->format)) }}</span>
      </div>
      {{-- Venue info --}}
      @if(!empty($venueRequestsData))
        <div class="tm-info-item">
          <span class="tm-info-label">Canchas ({{ count($venueRequestsData) }})</span>
          <div style="display:flex;flex-direction:column;gap:4px;">
            @foreach($venueRequestsData as $vrd)
              <span class="tm-info-value" style="display:flex;align-items:center;gap:6px;">
                @if($vrd['status'] === 'approved')
                  <svg width="14" height="14" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif($vrd['status'] === 'pending')
                  <svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                @else
                  <svg width="14" height="14" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                @endif
                <span style="font-size:13px;">{{ $vrd['field_name'] }} — {{ $vrd['venue_name'] }}</span>
              </span>
            @endforeach
          </div>
        </div>
      @elseif($tournament->external_venue_name)
        <div class="tm-info-item">
          <span class="tm-info-label">Sede</span>
          <span class="tm-info-value">{{ $tournament->external_venue_name }}</span>
        </div>
      @else
        <div class="tm-info-item">
          <span class="tm-info-label">Cancha</span>
          <span class="tm-info-value">
            <a href="{{ route('torneos.search_venue', $tournament) }}" style="display:inline-flex;align-items:center;gap:5px;color:var(--color-primary);font-weight:700;text-decoration:none;font-size:14px;">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
              Buscar cancha en TuCancha
            </a>
          </span>
        </div>
      @endif
      @if($tournament->estimated_start_date)
        <div class="tm-info-item">
          <span class="tm-info-label">Fecha estimada inicio</span>
          <span class="tm-info-value">{{ $tournament->estimated_start_date->format('d/m/Y') }}</span>
        </div>
      @endif
      @if($tournament->registration_deadline)
        <div class="tm-info-item">
          <span class="tm-info-label">Cierre inscripcion</span>
          <span class="tm-info-value">{{ $tournament->registration_deadline->format('d/m/Y H:i') }}</span>
        </div>
      @endif
      @if($tournament->players_per_team)
        <div class="tm-info-item">
          <span class="tm-info-label">Jugadores por equipo</span>
          <span class="tm-info-value">{{ $tournament->players_per_team }}</span>
        </div>
      @endif
      @if($tournament->inscription_price > 0)
        <div class="tm-info-item">
          <span class="tm-info-label">Precio inscripcion</span>
          <span class="tm-info-value" style="color: var(--color-primary);">${{ number_format($tournament->inscription_price, 0, ',', '.') }}</span>
        </div>
      @endif
    </div>
  </div>

  {{-- ══════════════════════════════════════════════ --}}
  {{-- 3. TEAMS SECTION                               --}}
  {{-- ══════════════════════════════════════════════ --}}
  <div class="tm-card">
    <h2 class="tm-card-title">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Equipos ({{ $tournament->teams->count() }})
    </h2>

    @if($tournament->teams->isEmpty())
      <div class="tm-teams-empty">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        <p>No hay equipos inscriptos todavia</p>
      </div>
    @else
      <div style="overflow-x: auto;">
        <table class="tm-teams-table">
          <thead>
            <tr>
              <th>Equipo</th>
              <th>Capitan</th>
              <th>Jugadores</th>
              <th>Estado</th>
              @if($tournament->inscription_price > 0)
                <th>Pago</th>
              @endif
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($tournament->teams as $team)
              <tr>
                <td><span class="tm-team-name">{{ $team->name }}</span></td>
                <td>{{ $team->captain->name ?? '—' }}</td>
                <td>{{ $team->players->count() }}</td>
                <td>
                  @php
                    $teamStatusLabels = ['confirmed' => 'Confirmado', 'disqualified' => 'Descalificado', 'withdrawn' => 'Retirado'];
                  @endphp
                  <span class="tm-team-status tm-team-status-{{ $team->status }}">
                    {{ $teamStatusLabels[$team->status] ?? ucfirst($team->status) }}
                  </span>
                </td>
                @if($tournament->inscription_price > 0)
                  <td>
                    @if($team->payment_confirmed)
                      <span class="tm-team-status" style="background: rgba(34,197,94,.1); color: #22c55e; display: inline-flex; align-items: center; gap: 4px;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="20 6 9 17 4 12"/></svg>
                        Pagado
                      </span>
                      @if($team->payment_confirmed_at)
                        <div style="font-size: 11px; color: #9ca3af; margin-top: 2px;">
                          {{ $team->payment_confirmed_at->format('d/m/Y') }}
                          @if($team->payment_method)
                            &middot; {{ $team->payment_method === 'mercadopago' ? 'MercadoPago' : 'Manual' }}
                          @endif
                        </div>
                      @endif
                    @else
                      <div style="display: flex; align-items: center; gap: 6px;">
                        <span class="tm-team-status" style="background: rgba(245,158,11,.08); color: #fbbf24;">Pendiente</span>
                        <form method="POST" action="{{ route('torneos.teams.confirm_payment', [$tournament, $team]) }}" style="margin:0;" onsubmit="return confirm('Confirmar pago manual de {{ addslashes($team->name) }}?')">
                          @csrf
                          <button type="submit" class="tm-btn tm-btn-primary tm-btn-sm" style="padding: 4px 10px; font-size: 11px;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><polyline points="20 6 9 17 4 12"/></svg>
                            Confirmar pago
                          </button>
                        </form>
                      </div>
                    @endif
                  </td>
                @endif
                <td>
                  <div class="tm-team-actions">
                    <a href="{{ route('torneos.teams.show', [$tournament, $team]) }}" class="tm-btn tm-btn-outline tm-btn-sm">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                      Ver
                    </a>
                    @if($team->status === 'confirmed' && !in_array($tournament->status, ['finished', 'cancelled']))
                      <form method="POST" action="{{ route('torneos.teams.disqualify', [$tournament, $team]) }}" x-on:submit.prevent="confirmAction($event, '¿Descalificar a {{ addslashes($team->name) }}? Esta accion no se puede deshacer.')">
                        @csrf
                        <button type="submit" class="tm-btn tm-btn-danger tm-btn-sm">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                          Descalificar
                        </button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  {{-- ══════════════════════════════════════════════ --}}
  {{-- 4. BRACKET / MATCHES SECTION                   --}}
  {{-- ══════════════════════════════════════════════ --}}
  @if($tournament->matches->isNotEmpty())
    <div class="tm-card">
      <h2 class="tm-card-title">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        Fixture ({{ $tournament->matches->count() }} partidos)
      </h2>

      @foreach($tournament->matches->groupBy('round_name') as $roundName => $matches)
        <div class="tm-round-group">
          <h3 class="tm-round-title">{{ $roundName }}</h3>

          @foreach($matches->sortBy('match_number') as $match)
            <div class="tm-match-card">
              <div class="tm-match-header">
                <span class="tm-match-number">Partido #{{ $match->match_number }}</span>
                @php
                  $matchStatusLabels = ['scheduled' => 'Programado', 'finished' => 'Finalizado', 'walkover' => 'W.O.', 'cancelled' => 'Cancelado'];
                @endphp
                <span class="tm-match-status tm-match-status-{{ $match->status }}">
                  {{ $matchStatusLabels[$match->status] ?? ucfirst($match->status) }}
                </span>
              </div>

              <div class="tm-match-teams">
                <div class="tm-match-team {{ $match->status === 'finished' && $match->winner_team_id === $match->home_team_id ? 'tm-match-winner' : '' }}">
                  @if($match->homeTeam)
                    {{ $match->homeTeam->name }}
                  @else
                    <span class="tm-match-team-tbd">TBD</span>
                  @endif
                </div>
                <span class="tm-match-vs">VS</span>
                <div class="tm-match-team {{ $match->status === 'finished' && $match->winner_team_id === $match->away_team_id ? 'tm-match-winner' : '' }}">
                  @if($match->awayTeam)
                    {{ $match->awayTeam->name }}
                  @else
                    <span class="tm-match-team-tbd">TBD</span>
                  @endif
                </div>
              </div>

              {{-- Finished: show score --}}
              @if($match->status === 'finished')
                <div class="tm-match-score">
                  {{ $match->home_score }} - {{ $match->away_score }}
                  @if($match->hasPenalties())
                    <span class="tm-match-score-pen">({{ $match->home_penalties }} - {{ $match->away_penalties }} pen)</span>
                  @endif
                </div>

              {{-- Walkover --}}
              @elseif($match->status === 'walkover')
                <div class="tm-match-wo">W.O.</div>

              {{-- Scheduled: result form --}}
              @elseif($match->status === 'scheduled' && $tournament->status === 'in_progress')
                <form method="POST" action="{{ route('torneos.match_result', [$tournament, $match]) }}" class="tm-result-form">
                  @csrf
                  <input type="number" name="home_score" min="0" max="99" class="tm-result-input" placeholder="0" required>
                  <span class="tm-result-sep">-</span>
                  <input type="number" name="away_score" min="0" max="99" class="tm-result-input" placeholder="0" required>
                  <button type="submit" class="tm-btn tm-btn-primary tm-btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Guardar
                  </button>
                  <div class="tm-result-pen-wrap" x-data="{ showPen: false }">
                    <button type="button" class="tm-btn tm-btn-outline tm-btn-sm" x-on:click="showPen = !showPen" style="width: 100%; justify-content: center;">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                      Penales (opcional)
                    </button>
                    <div x-show="showPen" x-cloak style="display: flex; align-items: center; gap: 6px; width: 100%; justify-content: center; margin-top: 8px;">
                      <span class="tm-result-pen-label">Pen:</span>
                      <input type="number" name="home_penalties" min="0" max="99" class="tm-result-pen-input" placeholder="0">
                      <span class="tm-result-sep" style="font-size: 14px;">-</span>
                      <input type="number" name="away_penalties" min="0" max="99" class="tm-result-pen-input" placeholder="0">
                    </div>
                  </div>
                </form>
              @endif
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  @endif

  {{-- ══════════════════════════════════════════════ --}}
  {{-- 5. STATS SECTION (Pro only)                    --}}
  {{-- ══════════════════════════════════════════════ --}}
  @if($hasStats && !empty($statsData))
  <div class="tm-card">
    <h2 class="tm-card-title">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
      Estadisticas
      <span style="font-size:10px;padding:2px 8px;background:rgba(59,130,246,.15);color:#60a5fa;border-radius:6px;font-weight:700;margin-left:8px;">PRO</span>
    </h2>

    <div style="overflow-x:auto;">
      <table class="tm-teams-table">
        <thead>
          <tr>
            <th style="text-align:left;">Equipo</th>
            <th>PJ</th>
            <th>PG</th>
            <th>PP</th>
            <th>GF</th>
            <th>GC</th>
            <th>DG</th>
          </tr>
        </thead>
        <tbody>
          @foreach($statsData as $i => $s)
            <tr>
              <td>
                <span style="display:inline-flex;align-items:center;gap:6px;">
                  @if($i === 0 && $s['played'] > 0)
                    <span style="font-size:14px;">🥇</span>
                  @elseif($i === 1 && $s['played'] > 0)
                    <span style="font-size:14px;">🥈</span>
                  @elseif($i === 2 && $s['played'] > 0)
                    <span style="font-size:14px;">🥉</span>
                  @endif
                  <span class="tm-team-name">{{ $s['name'] }}</span>
                </span>
              </td>
              <td style="text-align:center;">{{ $s['played'] }}</td>
              <td style="text-align:center;color:#22c55e;font-weight:700;">{{ $s['won'] }}</td>
              <td style="text-align:center;color:#f87171;font-weight:700;">{{ $s['lost'] }}</td>
              <td style="text-align:center;">{{ $s['goals_for'] }}</td>
              <td style="text-align:center;">{{ $s['goals_against'] }}</td>
              <td style="text-align:center;font-weight:700;{{ $s['goal_diff'] > 0 ? 'color:#22c55e;' : ($s['goal_diff'] < 0 ? 'color:#f87171;' : '') }}">
                {{ $s['goal_diff'] > 0 ? '+' : '' }}{{ $s['goal_diff'] }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Quick summary --}}
    @php
      $finishedMatches = $tournament->matches->where('status', 'finished');
      $totalGoals = $finishedMatches->sum('home_score') + $finishedMatches->sum('away_score');
      $matchesWithPenalties = $finishedMatches->filter(fn($m) => $m->home_penalties !== null)->count();
    @endphp
    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:16px;padding-top:16px;border-top:1px solid rgba(255,255,255,.06);">
      <div style="flex:1;min-width:120px;padding:12px 16px;background:#1a1a1a;border-radius:12px;text-align:center;">
        <div style="font-size:24px;font-weight:900;color:#e8e8e8;">{{ $finishedMatches->count() }}</div>
        <div style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;">Partidos jugados</div>
      </div>
      <div style="flex:1;min-width:120px;padding:12px 16px;background:#1a1a1a;border-radius:12px;text-align:center;">
        <div style="font-size:24px;font-weight:900;color:#e8e8e8;">{{ $totalGoals }}</div>
        <div style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;">Goles totales</div>
      </div>
      <div style="flex:1;min-width:120px;padding:12px 16px;background:#1a1a1a;border-radius:12px;text-align:center;">
        <div style="font-size:24px;font-weight:900;color:#e8e8e8;">{{ $finishedMatches->count() > 0 ? round($totalGoals / $finishedMatches->count(), 1) : 0 }}</div>
        <div style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;">Goles/partido</div>
      </div>
      @if($matchesWithPenalties > 0)
      <div style="flex:1;min-width:120px;padding:12px 16px;background:#1a1a1a;border-radius:12px;text-align:center;">
        <div style="font-size:24px;font-weight:900;color:#e8e8e8;">{{ $matchesWithPenalties }}</div>
        <div style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;">Definidos x penales</div>
      </div>
      @endif
    </div>
  </div>
  @elseif(!$hasStats && $tournament->matches->where('status', 'finished')->count() > 0)
  <div class="tm-card" style="text-align:center;padding:24px;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px;margin:0 auto 8px;"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg>
    <p style="font-size:14px;font-weight:700;color:#64748b;margin:0 0 4px;">Estadisticas del torneo</p>
    <p style="font-size:13px;color:#94a3b8;margin:0 0 12px;">Con el plan Pro podes ver tabla de posiciones, goles y mas.</p>
    <a href="{{ route('organizador.planes') }}" class="tm-btn tm-btn-outline tm-btn-sm">Ver planes</a>
  </div>
  @endif

  {{-- ══════════════════════════════════════════════ --}}
  {{-- 6. SHARE SECTION                               --}}
  {{-- ══════════════════════════════════════════════ --}}
  <div class="tm-card">
    <h2 class="tm-card-title">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
      Compartir torneo
    </h2>
    <div class="tm-share-row">
      <div class="tm-share-url">{{ route('torneos.show', $tournament) }}</div>
      @php
        $shareText = urlencode("Sumate al torneo *{$tournament->name}* en TuCancha! Inscribi a tu equipo aca: " . route('torneos.show', $tournament));
      @endphp
      <a href="https://wa.me/?text={{ $shareText }}" target="_blank" rel="noopener" class="tm-btn tm-btn-whatsapp">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Compartir por WhatsApp
      </a>
      <button type="button" class="tm-btn tm-btn-copy" x-on:click="copyLink">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
        <span x-text="copied ? 'Copiado!' : 'Copiar enlace'"></span>
      </button>
    </div>
  </div>

</div>

@endsection

@push('scripts')
{{-- Chat modals for internal requests --}}
@php $internalReqs = collect($venueRequestsData)->where('contact_method', 'internal'); @endphp
@foreach($internalReqs as $iReq)
<div id="tc-chat-overlay-{{ $iReq['id'] }}"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;padding:16px;"
     onclick="if(event.target===this)closeTcChat({{ $iReq['id'] }})">
  <div style="background:#111;border-radius:20px;width:100%;max-width:480px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.5);">
    <div style="padding:18px 20px;border-bottom:1px solid rgba(255,255,255,.06);display:flex;align-items:center;justify-content:space-between;">
      <div>
        <h3 style="font-size:15px;font-weight:800;color:#e8e8e8;margin:0;">Chat — {{ $iReq['venue_name'] }}</h3>
        <div style="font-size:12px;color:#666;margin-top:2px;">{{ $iReq['field_name'] }}</div>
      </div>
      <button type="button" onclick="closeTcChat({{ $iReq['id'] }})"
              style="width:32px;height:32px;border-radius:10px;border:none;background:rgba(255,255,255,.06);cursor:pointer;display:flex;align-items:center;justify-content:center;color:#a0a0a0;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div id="tc-chat-body-{{ $iReq['id'] }}" style="flex:1;overflow-y:auto;padding:16px 20px;display:flex;flex-direction:column;gap:8px;min-height:200px;">
      @if(empty($iReq['messages']))
        <div data-chat-empty style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#94a3b8;text-align:center;padding:20px;">
          <svg style="width:40px;height:40px;margin-bottom:8px;opacity:.5;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
          <span style="font-size:13px;font-weight:600;">Sin mensajes todavia</span>
          <span style="font-size:12px;">Escribi un mensaje para coordinar con el complejo.</span>
        </div>
      @else
        @foreach($iReq['messages'] as $msg)
        <div style="display:flex;flex-direction:column;{{ $msg['is_mine'] ? 'align-items:flex-end' : 'align-items:flex-start' }}">
          <div style="max-width:80%;padding:10px 14px;font-size:13px;line-height:1.5;word-break:break-word;{{ $msg['is_mine'] ? 'background:#22c55e;color:#052e16;border-radius:16px 16px 4px 16px;' : 'background:#1a1a1a;color:#e8e8e8;border-radius:16px 16px 16px 4px;' }}">
            {{ $msg['message'] }}
          </div>
          <span style="font-size:10px;color:#94a3b8;margin-top:3px;padding:0 4px;">{{ $msg['user_name'] }} &middot; {{ $msg['created_at'] }}</span>
        </div>
        @endforeach
      @endif
    </div>
    <div style="padding:12px 16px;border-top:1px solid rgba(255,255,255,.06);">
      <form method="POST" action="{{ route('torneos.request_message', $iReq['id']) }}" data-chat-form="tc-chat-body-{{ $iReq['id'] }}" style="display:flex;gap:8px;width:100%;">
        @csrf
        <input type="text" name="message" placeholder="Escribi un mensaje..." required maxlength="1000" autocomplete="off"
               style="flex:1;padding:10px 14px;border:1px solid rgba(255,255,255,.1);border-radius:12px;font-size:13px;font-family:inherit;outline:none;background:#0a0a0a;color:#e8e8e8;">
        <button type="submit"
                style="width:40px;height:40px;border-radius:12px;border:none;background:#22c55e;color:#052e16;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
        </button>
      </form>
    </div>
  </div>
</div>
@endforeach

<script>
  function openTcChat(id) {
    var el = document.getElementById('tc-chat-overlay-' + id);
    if (el) {
      el.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      var body = document.getElementById('tc-chat-body-' + id);
      if (body) body.scrollTop = body.scrollHeight;
      // Mark as read
      var badge = document.getElementById('tc-unread-badge-' + id);
      if (badge) badge.style.display = 'none';
      fetch('/torneos/solicitudes/' + id + '/leido', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
      });
    }
  }
  function closeTcChat(id) {
    var el = document.getElementById('tc-chat-overlay-' + id);
    if (el) {
      el.style.display = 'none';
      document.body.style.overflow = '';
    }
  }
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('[id^="tc-chat-overlay-"]').forEach(function(o) {
        if (o.style.display === 'flex') {
          o.style.display = 'none';
          document.body.style.overflow = '';
        }
      });
    }
  });

  // AJAX chat submit
  document.querySelectorAll('[data-chat-form]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      var input = form.querySelector('input[name="message"]');
      var msg = input.value.trim();
      if (!msg) return;

      var btn = form.querySelector('button[type="submit"]');
      btn.disabled = true;
      input.disabled = true;

      fetch(form.action, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ message: msg })
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        var bodyId = form.getAttribute('data-chat-form');
        var body = document.getElementById(bodyId);
        if (body) {
          // Remove empty state if present
          var empty = body.querySelector('[data-chat-empty]');
          if (empty) empty.remove();

          var bubble = document.createElement('div');
          bubble.style.cssText = 'display:flex;flex-direction:column;align-items:flex-end;';
          bubble.innerHTML =
            '<div style="max-width:80%;padding:10px 14px;font-size:13px;line-height:1.5;word-break:break-word;background:#22c55e;color:#052e16;border-radius:16px 16px 4px 16px;">' +
              data.message.replace(/</g,'&lt;').replace(/>/g,'&gt;') +
            '</div>' +
            '<span style="font-size:10px;color:#94a3b8;margin-top:3px;padding:0 4px;">' +
              data.user_name + ' &middot; ahora</span>';
          body.appendChild(bubble);
          body.scrollTop = body.scrollHeight;
        }
        input.value = '';
      })
      .catch(function() { alert('Error al enviar el mensaje.'); })
      .finally(function() { btn.disabled = false; input.disabled = false; input.focus(); });
    });
  });

  // Real-time chat via Echo
  function tcAppendIncoming(bodyId, data) {
    var body = document.getElementById(bodyId);
    if (!body) return;
    var empty = body.querySelector('[data-chat-empty]');
    if (empty) empty.remove();
    var bubble = document.createElement('div');
    bubble.style.cssText = 'display:flex;flex-direction:column;align-items:flex-start;';
    bubble.innerHTML =
      '<div style="max-width:80%;padding:10px 14px;font-size:13px;line-height:1.5;word-break:break-word;background:#1a1a1a;color:#e8e8e8;border-radius:16px 16px 16px 4px;">' +
        data.message.replace(/</g,'&lt;').replace(/>/g,'&gt;') +
      '</div><span style="font-size:10px;color:#94a3b8;margin-top:3px;padding:0 4px;">' +
        data.user_name + ' &middot; ahora</span>';
    body.appendChild(bubble);
    body.scrollTop = body.scrollHeight;
  }
  if (window.Echo) {
    @foreach($internalReqs as $iReq)
    window.Echo.private('tournament-request.{{ $iReq['id'] }}')
      .listen('.chat.message', function(data) {
        if (data.user_id === {{ auth()->id() }}) return;
        tcAppendIncoming('tc-chat-body-{{ $iReq['id'] }}', data);
      });
    @endforeach
  }

  function tournamentManage() {
    return {
      copied: false,

      confirmAction(event, message) {
        if (confirm(message)) {
          event.target.closest('form').submit();
        }
      },

      copyLink() {
        const url = @json(route('torneos.show', $tournament));
        navigator.clipboard.writeText(url).then(() => {
          this.copied = true;
          setTimeout(() => { this.copied = false; }, 2000);
        });
      }
    };
  }
</script>
@endpush
