@extends('layouts.app')

@section('title', 'Mis Torneos')

@push('styles')
<style>
  .mt-wrap {
    max-width: 900px;
    margin: 0 auto;
    padding: 32px 16px 80px;
  }
  .mt-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 28px;
  }
  .mt-title {
    font-size: 28px;
    font-weight: 900;
    color: #111;
    letter-spacing: -.02em;
    margin: 0;
  }
  .mt-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: var(--color-text-secondary);
    text-decoration: none;
    transition: color .15s;
  }
  .mt-back:hover { color: var(--color-primary); }
  .mt-back svg { width: 16px; height: 16px; }

  .mt-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .mt-item {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 16px 20px;
    text-decoration: none;
    color: inherit;
    transition: border-color .15s, box-shadow .15s, transform .15s;
  }
  .mt-item:hover {
    border-color: var(--color-primary);
    box-shadow: 0 4px 16px rgba(0,0,0,.06);
    transform: translateY(-2px);
  }
  .mt-item-cover {
    width: 64px;
    height: 64px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
  }
  .mt-item-cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .mt-item-cover-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .mt-item-cover-placeholder svg {
    width: 28px;
    height: 28px;
    stroke: rgba(255,255,255,.4);
  }
  .mt-item-info {
    flex: 1;
    min-width: 0;
  }
  .mt-item-name {
    font-size: 16px;
    font-weight: 800;
    color: #111;
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .mt-item-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    font-size: 13px;
    color: var(--color-text-secondary);
  }
  .mt-item-meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }
  .mt-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .02em;
    flex-shrink: 0;
  }
  .mt-badge-draft        { background: #f3f4f6; color: #6b7280; }
  .mt-badge-pending      { background: #fef3c7; color: #92400e; }
  .mt-badge-open         { background: #dcfce7; color: #166534; }
  .mt-badge-closed       { background: #fee2e2; color: #991b1b; }
  .mt-badge-in_progress  { background: #dbeafe; color: #1e40af; }
  .mt-badge-finished     { background: #e5e7eb; color: #374151; }
  .mt-badge-cancelled    { background: #fee2e2; color: #991b1b; }

  .mt-item-arrow {
    flex-shrink: 0;
    color: var(--color-text-muted);
    transition: color .15s, transform .15s;
  }
  .mt-item:hover .mt-item-arrow {
    color: var(--color-primary);
    transform: translateX(3px);
  }
  .mt-item-arrow svg { width: 18px; height: 18px; }

  .mt-empty {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
  }
  .mt-empty h3 { font-size: 18px; font-weight: 800; margin: 0 0 8px; }
  .mt-empty p { font-size: 14px; color: var(--color-text-muted); margin: 0 0 20px; }
  .mt-empty-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--color-primary);
    color: #052e16;
    border-radius: 12px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s;
  }
  .mt-empty-btn:hover { background: var(--color-primary-hover); }

  @media (max-width: 640px) {
    .mt-wrap { padding: 20px 12px 60px; }
    .mt-title { font-size: 22px; }
    .mt-item { padding: 12px 14px; gap: 12px; }
    .mt-item-cover { width: 48px; height: 48px; border-radius: 10px; }
    .mt-item-name { font-size: 14px; }
    .mt-item-meta { font-size: 12px; }
  }
</style>
@endpush

@section('content')
<div class="mt-wrap">

  <div class="mt-header">
    <div>
      <a href="{{ route('torneos.index') }}" class="mt-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Volver a torneos
      </a>
      <h1 class="mt-title">Mis torneos</h1>
    </div>
    <a href="{{ route('torneos.create') }}" style="display:inline-flex;align-items:center;gap:6px;background:var(--color-primary);color:#052e16;border-radius:12px;padding:10px 20px;font-size:14px;font-weight:700;text-decoration:none;transition:background .15s;">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Crear torneo
    </a>
  </div>

  @if($tournaments->isEmpty())
    <div class="mt-empty">
      <h3>No tenes torneos todavia</h3>
      <p>Crea tu primer torneo y empeza a organizar competencias.</p>
      <a href="{{ route('torneos.create') }}" class="mt-empty-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Crear torneo
      </a>
    </div>
  @else
    <div class="mt-list">
      @foreach($tournaments as $tournament)
        @php
          $sportLabel = match($tournament->sport) {
            'football'   => 'Futbol',
            'padel'      => 'Padel',
            'tennis'     => 'Tenis',
            'basketball' => 'Basquet',
            'volleyball' => 'Voley',
            default      => ucfirst($tournament->sport ?? ''),
          };

          $statusLabel = match($tournament->status) {
            'draft'               => 'Borrador',
            'open_registration'   => 'Inscripcion abierta',
            'registration_closed' => 'Inscripcion cerrada',
            'in_progress'         => 'En curso',
            'finished'            => 'Finalizado',
            'cancelled'           => 'Cancelado',
            default               => ucfirst($tournament->status),
          };

          $statusClass = match($tournament->status) {
            'draft'               => 'mt-badge-draft',
            'open_registration'   => 'mt-badge-open',
            'registration_closed' => 'mt-badge-closed',
            'in_progress'         => 'mt-badge-in_progress',
            'finished'            => 'mt-badge-finished',
            'cancelled'           => 'mt-badge-cancelled',
            default               => 'mt-badge-draft',
          };

          // Show "pending" badge if draft + waiting for venue
          $showPending = $tournament->status === 'draft'
            && $tournament->venue_approval_status === 'pending';

          $coverGradient = match($tournament->sport) {
            'football'   => 'linear-gradient(135deg, #064e3b, #22c55e)',
            'padel'      => 'linear-gradient(135deg, #2e1065, #7c3aed)',
            'tennis'     => 'linear-gradient(135deg, #78350f, #d4b896)',
            'basketball' => 'linear-gradient(135deg, #7c2d12, #f97316)',
            'volleyball' => 'linear-gradient(135deg, #1e3a5f, #3b82f6)',
            default      => 'linear-gradient(135deg, #374151, #6b7280)',
          };

          $confirmedCount = $tournament->confirmedTeams->count();

          $manageUrl = route('torneos.show', $tournament);
        @endphp

        <div style="border:1px solid #ececec; border-radius:16px; overflow:hidden; transition:box-shadow .15s;">
          <a href="{{ $manageUrl }}" class="mt-item" style="border:none; border-radius:0;">
            <div class="mt-item-cover">
              @if($tournament->cover_image_path)
                <img src="{{ asset('storage/' . $tournament->cover_image_path) }}" alt="{{ $tournament->name }}" loading="lazy">
              @else
                <div class="mt-item-cover-placeholder" style="background:{{ $coverGradient }};">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                </div>
              @endif
            </div>

            <div class="mt-item-info">
              <h3 class="mt-item-name">{{ $tournament->name }}</h3>
              <div class="mt-item-meta">
                <span>{{ $sportLabel }}</span>
                <span>{{ $confirmedCount }}/{{ $tournament->max_teams }} equipos</span>
                @if($tournament->estimated_start_date)
                  <span>{{ $tournament->estimated_start_date->translatedFormat('d M Y') }}</span>
                @endif
              </div>
            </div>

            @if($showPending)
              <span class="mt-badge mt-badge-pending">Pendiente</span>
            @else
              <span class="mt-badge {{ $statusClass }}">{{ $statusLabel }}</span>
            @endif

            <div class="mt-item-arrow">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </div>
          </a>

          {{-- MercadoPago connection status --}}
          @if($tournament->inscription_price && $tournament->inscription_price > 0)
            <div style="padding:10px 20px; border-top:1px solid #f3f4f6; display:flex; align-items:center; justify-content:space-between; gap:10px; background:#fafafa;">
              @if(auth()->user()->mp_access_token)
                <span style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#15803d;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                  MercadoPago conectado
                </span>
                <form action="{{ route('organizador.mp_oauth.disconnect') }}" method="POST" style="margin:0;">
                  @csrf
                  <button type="submit" style="background:none; border:none; font-size:12px; font-weight:600; color:#991b1b; cursor:pointer; text-decoration:underline;">Desconectar</button>
                </form>
              @else
                <span style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:#92400e;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  Conecta MP para cobrar inscripciones
                </span>
                <a href="{{ route('organizador.mp_oauth.redirect') }}" style="display:inline-flex; align-items:center; gap:5px; background:#009ee3; color:#fff; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:700; text-decoration:none; transition:background .15s;">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                  Conectar MercadoPago
                </a>
              @endif
            </div>
          @endif
        </div>
      @endforeach
    </div>
  @endif

</div>
@endsection
