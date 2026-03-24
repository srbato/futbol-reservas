@extends('layouts.app')

@section('title', 'Falta Uno — Partidos buscando jugadores')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
@endpush

@push('styles')
<style>
  /* ── Scroll progress ───────────────────────────── */
  .fui-scroll-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: #22c55e;
    z-index: 9999;
    transition: width .1s linear;
  }

  /* ── Hero ──────────────────────────────────────── */
  .fui-hero {
    position: relative;
    height: 360px;
    border-radius: 28px;
    overflow: hidden;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-end;
  }
  .fui-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/Images/jugadores-falta-uno.webp');
    background-size: cover;
    background-position: center;
  }
  .fui-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.55);
  }
  .fui-hero-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.75) 0%, transparent 55%);
  }
  .fui-hero-content {
    position: relative;
    z-index: 2;
    padding: 32px 36px;
    width: 100%;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
  }

  /* ── Badge pulsante ────────────────────────────── */
  .fui-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    background: rgba(34,197,94,.18);
    border: 1px solid rgba(34,197,94,.4);
    color: #4ade80;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 999px;
    margin-bottom: 12px;
  }
  .fui-badge-dot {
    width: 7px;
    height: 7px;
    background: #22c55e;
    border-radius: 50%;
    animation: fui-dot-pulse 1.6s ease-in-out infinite;
  }
  @keyframes fui-dot-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.65); }
  }

  /* ── Hero títulos ──────────────────────────────── */
  .fui-hero-h1 {
    margin: 0 0 8px;
    font-size: 48px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.03em;
    line-height: 1.05;
  }
  .fui-hero-sub {
    margin: 0;
    font-size: 16px;
    color: rgba(255,255,255,.7);
  }
  .fui-hero-cta {
    display: inline-block;
    background: #22c55e;
    color: #052e16;
    border-radius: 12px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 800;
    text-decoration: none;
    white-space: nowrap;
    flex-shrink: 0;
    transition: background .15s, transform .15s;
    align-self: center;
    margin-bottom: 4px;
  }
  .fui-hero-cta:hover { background: #16a34a; transform: translateY(-1px); }

  /* ── Banner perfil ─────────────────────────────── */
  .fui-profile-banner {
    background: #fffbeb;
    border-left: 4px solid #f59e0b;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  /* ── Filtros ───────────────────────────────────── */
  .fui-filters-wrap {
    background: #f8f8f8;
    border-radius: 16px;
    padding: 16px 20px;
    margin-bottom: 24px;
  }
  .fui-filter-divider {
    border: none;
    border-top: 1px solid #e5e5e5;
    margin: 12px 0;
  }
  .fui-filter-row {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
  }
  .fui-pill {
    padding: 7px 16px;
    border: 1.5px solid #e0e0e0;
    background: #fff;
    border-radius: 999px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: border-color .15s, background .15s, color .15s;
    color: #555;
    text-decoration: none;
    display: inline-block;
  }
  .fui-pill:hover { border-color: #111; color: #111; }
  .fui-pill.active { background: #111; color: #fff; border-color: #111; }
  .fui-pill-sm { font-size: 12px; padding: 5px 13px; }
  .fui-filter-label {
    font-size: 12px;
    color: #888;
    font-weight: 700;
    flex-shrink: 0;
  }
  .fui-filter-sep {
    color: #ddd;
    font-size: 16px;
    line-height: 1;
    flex-shrink: 0;
  }

  /* ── Cards ─────────────────────────────────────── */
  .fui-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    display: flex;
    flex-direction: column;
  }
  .fui-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
  }
  .fui-card-stripe {
    width: 5px;
    flex-shrink: 0;
    align-self: stretch;
  }
  .fui-card-stripe.open   { background: #22c55e; }
  .fui-card-stripe.full   { background: #111; }
  .fui-card-stripe.cancel { background: #dc2626; }

  .fui-card-body {
    display: flex;
    gap: 20px;
    padding: 20px 20px 0 20px;
    flex-wrap: wrap;
  }
  .fui-card-inner {
    display: flex;
    gap: 20px;
    flex: 1;
    min-width: 0;
  }

  /* Círculo SVG */
  .fui-progress-wrap {
    flex-shrink: 0;
    text-align: center;
    width: 100px;
  }
  .fui-progress-circle {
    transform: rotate(-90deg);
    display: block;
  }
  @keyframes fui-stroke-draw {
    from { stroke-dashoffset: 283; }
  }
  .fui-arc-animated {
    animation: fui-stroke-draw .8s ease-out both;
  }
  .fui-progress-label {
    margin-top: -8px;
    font-size: 13px;
    font-weight: 700;
  }
  .fui-progress-sub {
    font-size: 11px;
    color: #aaa;
    margin-top: 2px;
  }

  /* Info derecha */
  .fui-card-info { flex: 1; min-width: 0; }
  .fui-venue-link {
    font-size: 12px;
    color: #888;
    text-decoration: none;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .fui-venue-link:hover { color: #111; }
  .fui-field-name {
    font-size: 18px;
    font-weight: 800;
    color: #111;
    text-decoration: none;
    display: block;
    margin: 4px 0 10px;
    line-height: 1.2;
  }
  .fui-field-name:hover { color: #333; }

  /* Chips */
  .fui-chips {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 10px;
  }
  .fui-chip {
    font-size: 11px;
    padding: 3px 10px;
    border-radius: 999px;
    font-weight: 700;
  }
  .fui-chip-sport  { background: rgba(29,78,216,.1);  border: 1px solid rgba(29,78,216,.2);  color: #1d4ed8; }
  .fui-chip-date   { background: rgba(85,85,85,.07);  border: 1px solid rgba(85,85,85,.12);  color: #555; }
  .fui-chip-gender { background: rgba(219,39,119,.08); border: 1px solid rgba(219,39,119,.15); color: #db2777; }
  .fui-chip-cat    { background: rgba(21,128,61,.08);  border: 1px solid rgba(21,128,61,.15);  color: #15803d; }

  /* Avatares apilados */
  .fui-avatars {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
  }
  .fui-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #fff;
    object-fit: cover;
    margin-left: -8px;
    background: #f1f1f1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #555;
    flex-shrink: 0;
  }
  .fui-avatar:first-child { margin-left: 0; }
  .fui-avatar-more {
    background: #f1f1f1;
    color: #888;
    font-size: 11px;
    font-weight: 700;
    margin-left: -8px;
  }
  .fui-counter {
    font-size: 13px;
    color: #666;
    margin-bottom: 2px;
  }

  /* Zona de acciones */
  .fui-card-actions {
    border-top: 1px solid #f0f0f0;
    padding: 14px 20px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    margin-top: 14px;
  }
  .fui-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    cursor: pointer;
    border: none;
    font-family: inherit;
    transition: transform .15s, box-shadow .15s, background .15s;
  }
  .fui-btn:hover { transform: translateY(-1px); box-shadow: 0 3px 10px rgba(0,0,0,.1); }
  .fui-btn-black   { background: #111; color: #fff; }
  .fui-btn-black:hover { background: #22c55e; color: #052e16; }
  .fui-btn-ghost   { background: transparent; border: 1.5px solid #e0e0e0; color: #333; }
  .fui-btn-cancel  { background: transparent; border: 1.5px solid #fecaca; color: #dc2626; }
  .fui-btn-outline { background: transparent; border: 1.5px solid #d1d5db; color: #555; }
  .fui-btn-stats   { background: #f4f4f4; color: #333; }
  .fui-btn-rate    { background: #fef3c7; color: #92400e; }
  .fui-badge-joined {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #22c55e;
    font-weight: 700;
  }

  /* ── Estado vacío ──────────────────────────────── */
  .fui-empty {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    padding: 64px 24px;
    text-align: center;
  }
  .fui-empty-icon {
    display: block;
    margin: 0 auto 20px;
    opacity: .55;
  }
  .fui-empty h3 {
    margin: 0 0 8px;
    font-size: 20px;
    font-weight: 800;
  }
  .fui-empty p { color: #888; font-size: 14px; margin: 0 0 24px; }
  .fui-empty-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }

  /* ── Mobile ────────────────────────────────────── */
  @media (max-width: 600px) {
    .fui-hero { height: 280px; }
    .fui-hero-h1 { font-size: 32px; }
    .fui-hero-content { padding: 20px; }
    .fui-card-inner { flex-direction: column; gap: 12px; }
    .fui-progress-wrap { width: 100%; display: flex; align-items: center; gap: 12px; }
  }
</style>
@endpush

@section('content')

{{-- Scroll progress bar --}}
<div class="fui-scroll-progress" id="fuiScrollProgress"></div>

{{-- Hero --}}
<div class="fui-hero">
  <div class="fui-hero-bg"></div>
  <div class="fui-hero-overlay"></div>
  <div class="fui-hero-gradient"></div>
  <div class="fui-hero-content">
    <div>
      <div data-aos="fade-up" data-aos-delay="0">
        <span class="fui-badge">
          <span class="fui-badge-dot"></span>
          Falta Uno
        </span>
      </div>
      <h1 class="fui-hero-h1" data-aos="fade-up" data-aos-delay="100">Encontrá tu partido</h1>
      <p class="fui-hero-sub" data-aos="fade-up" data-aos-delay="180">Partidos armándose. Anotate y jugá.</p>
    </div>
    <div data-aos="fade-up" data-aos-delay="260">
      <a href="{{ route('venues.index', ['falta_uno' => '1']) }}" class="fui-hero-cta">+ Crear partido</a>
    </div>
  </div>
</div>

{{-- Banner sin perfil deportivo --}}
@auth
  @if(auth()->user()->faltaUnoSportProfiles()->doesntExist())
    <div class="fui-profile-banner" data-aos="fade-right">
      <div style="font-size:20px; flex-shrink:0;">⚠️</div>
      <div style="flex:1;">
        <div style="font-size:14px; font-weight:700; color:#92400e;">Para unirte a partidos necesitás completar tu perfil deportivo</div>
        <div style="font-size:12px; color:#b45309; margin-top:2px;">Tu categoría y género determinan a qué partidos podés unirte.</div>
      </div>
      <a href="/profile#sport-profile"
         style="display:inline-block; background:#111; color:#fff; border-radius:10px; padding:8px 18px; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap;">
        Completar perfil
      </a>
    </div>
  @endif
@endauth

{{-- Filtros --}}
<div class="fui-filters-wrap">
  {{-- Fila 1: deporte --}}
  <div class="fui-filter-row">
    <a href="{{ route('falta-uno.index', array_filter(['gender' => $gender ?? null])) }}"
       class="fui-pill {{ !$sport ? 'active' : '' }}"
       data-aos="fade-up" data-aos-delay="0">Todos</a>
    @php $sportPills = ['football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley']; $pi = 0; @endphp
    @foreach($sportPills as $val => $label)
      @php $pi++ @endphp
      <a href="{{ route('falta-uno.index', array_filter(['sport' => $val, 'gender' => $gender ?? null])) }}"
         class="fui-pill {{ $sport === $val ? 'active' : '' }}"
         data-aos="fade-up" data-aos-delay="{{ $pi * 50 }}">{{ $label }}</a>
    @endforeach
  </div>

  <hr class="fui-filter-divider">

  {{-- Fila 2: género y categoría --}}
  @php
    $genericCategories = ['recreativo' => 'Recreativo', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado', 'competitivo' => 'Competitivo'];
    $padelCategories   = ['primera' => 'Primera', 'segunda' => 'Segunda', 'tercera' => 'Tercera', 'cuarta' => 'Cuarta', 'quinta' => 'Quinta', 'sexta' => 'Sexta', 'septima' => 'Séptima', 'octava' => 'Octava'];
    $visibleCategories = match($sport) {
      'padel'                                         => $padelCategories,
      'football','tennis','basketball','volleyball'   => $genericCategories,
      default                                         => array_merge($genericCategories, $padelCategories),
    };
    $gi = 0;
  @endphp
  <div class="fui-filter-row">
    <span class="fui-filter-label">Género:</span>
    @foreach([''=>'Cualquier género','male'=>'Masculino','female'=>'Femenino'] as $val => $label)
      @php $gi++ @endphp
      <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$val ?: null,'category'=>$category ?? null])) }}"
         class="fui-pill fui-pill-sm {{ ($gender ?? '') === $val ? 'active' : '' }}"
         data-aos="fade-up" data-aos-delay="{{ $gi * 50 }}">{{ $label }}</a>
    @endforeach
    <span class="fui-filter-sep">|</span>
    <span class="fui-filter-label">Categoría:</span>
    <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$gender ?? null])) }}"
       class="fui-pill fui-pill-sm {{ ($category ?? '') === '' ? 'active' : '' }}"
       data-aos="fade-up" data-aos-delay="{{ ++$gi * 50 }}">Cualquier cat.</a>
    @foreach($visibleCategories as $val => $label)
      @php $gi++ @endphp
      <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$gender ?? null,'category'=>$val])) }}"
         class="fui-pill fui-pill-sm {{ ($category ?? '') === $val ? 'active' : '' }}"
         data-aos="fade-up" data-aos-delay="{{ min($gi * 50, 400) }}">{{ $label }}</a>
    @endforeach
  </div>
</div>

{{-- Listado de partidos --}}
@if($games->isEmpty())
  <div class="fui-empty" data-aos="zoom-in">
    <img class="fui-empty-icon" src="/Images/silueta-jugador-vacia.webp" alt="" width="80" height="80"
         style="width:80px; height:80px; object-fit:contain; border-radius:0;">
    <h3>No hay partidos disponibles</h3>
    <p>¿Por qué no iniciás uno vos? Elegí una cancha con Falta Uno habilitado.</p>
    <div class="fui-empty-actions">
      <a href="{{ route('venues.index', ['falta_uno' => '1']) }}" class="btn btn-primary">Crear partido</a>
      <a href="{{ route('venues.index') }}" class="btn">Ver complejos</a>
    </div>
  </div>
@else
  <div style="display:grid; gap:16px;">
    @foreach($games as $idx => $game)
    @php
      $joined  = $game->activeParticipants->count();
      $needed  = $game->players_needed;
      $pct     = $needed > 0 ? min(100, round(($joined / $needed) * 100)) : 100;
      $dash    = 283;
      $filled  = round($dash * $pct / 100);
      $empty   = $dash - $filled;
      $isAuthJoined = auth()->check() && $game->activeParticipants->contains('user_id', auth()->id());
      $isInitiator  = auth()->check() && $game->initiator_user_id === auth()->id();
      $sportLabel   = match($game->field->sport ?? '') {
        'football'   => '⚽ Fútbol',
        'padel'      => '🏓 Pádel',
        'tennis'     => '🎾 Tenis',
        'basketball' => '🏀 Básquet',
        'volleyball' => '🏐 Vóley',
        default      => ucfirst($game->field->sport ?? 'Cancha'),
      };
      $stripeClass = match($game->status) {
        'full'      => 'full',
        'cancelled' => 'cancel',
        default     => 'open',
      };
      $cardDelay = min($idx * 80, 400);
    @endphp

    <div class="fui-card" data-game-id="{{ $game->id }}"
         data-aos="fade-up" data-aos-delay="{{ $cardDelay }}">

      {{-- Franja de color izquierda --}}
      <div style="display:flex;">
        <div class="fui-card-stripe {{ $stripeClass }}"></div>
        <div style="flex:1; display:flex; flex-direction:column;">

          {{-- Cuerpo --}}
          <div class="fui-card-body">
            <div class="fui-card-inner">

              {{-- Círculo de progreso --}}
              <div class="fui-progress-wrap">
                <svg class="fui-progress-circle" width="90" height="90" viewBox="0 0 100 100">
                  <circle cx="50" cy="50" r="45" fill="none" stroke="#f0f0f0" stroke-width="8"/>
                  <circle cx="50" cy="50" r="45" fill="none"
                          stroke="{{ $game->status === 'full' ? '#22c55e' : '#111' }}" stroke-width="8"
                          stroke-dasharray="{{ $filled }} {{ $empty }}"
                          stroke-linecap="round"
                          stroke-dashoffset="0"
                          class="game-arc fui-arc-animated">
                  </circle>
                </svg>
                <div class="fui-progress-label game-status-text">
                  @if($game->status === 'full')
                    <span style="color:#22c55e;">¡Completo!</span>
                  @else
                    <span style="color:#111;">Faltan {{ $needed - $joined }}</span>
                  @endif
                </div>
                <div class="fui-progress-sub game-counter-text">{{ $joined }}/{{ $needed }} anotados</div>
              </div>

              {{-- Info --}}
              <div class="fui-card-info">
                <a href="{{ route('venues.show', $game->field->venue) }}" class="fui-venue-link">
                  <span>📍</span> {{ $game->field->venue->name }}
                </a>
                <a href="{{ route('falta-uno.show', $game) }}" class="fui-field-name">
                  {{ $game->field->name }}
                </a>

                <div class="fui-chips">
                  <span class="fui-chip fui-chip-sport">{{ $sportLabel }}</span>
                  <span class="fui-chip fui-chip-date">📅 {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y') }}</span>
                  <span class="fui-chip fui-chip-date">🕐 {{ \Carbon\Carbon::parse($game->start_at)->format('H:i') }} hs</span>
                  @if($game->gender_filter !== 'mixed')
                    <span class="fui-chip fui-chip-gender">
                      {{ $game->gender_filter === 'male' ? 'Masculino' : 'Femenino' }}
                    </span>
                  @endif
                  @if($game->category_min || $game->category_max)
                    <span class="fui-chip fui-chip-cat">
                      @if($game->category_min && $game->category_max && $game->category_min === $game->category_max)
                        {{ ucfirst($game->category_min) }}
                      @elseif($game->category_min && $game->category_max)
                        {{ ucfirst($game->category_min) }} – {{ ucfirst($game->category_max) }}
                      @elseif($game->category_min)
                        Desde {{ ucfirst($game->category_min) }}
                      @else
                        Hasta {{ ucfirst($game->category_max) }}
                      @endif
                    </span>
                  @endif
                </div>

                {{-- Avatares apilados --}}
                @if($game->activeParticipants->isNotEmpty())
                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:6px;">
                  <div class="fui-avatars">
                    @foreach($game->activeParticipants->take(5) as $p)
                      @if($p->user->avatar_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($p->user->avatar_path) }}"
                             class="fui-avatar" title="{{ $p->user->name }}" alt="{{ $p->user->name }}">
                      @else
                        <div class="fui-avatar" title="{{ $p->user->name }}">
                          {{ mb_strtoupper(mb_substr($p->user->name, 0, 1)) }}
                        </div>
                      @endif
                    @endforeach
                    @if($game->activeParticipants->count() > 5)
                      <div class="fui-avatar fui-avatar-more">+{{ $game->activeParticipants->count() - 5 }}</div>
                    @endif
                  </div>
                  <span class="fui-counter">{{ $game->activeParticipants->count() }} confirmados de {{ $game->players_needed }}</span>
                </div>
                @endif
              </div>

            </div>{{-- /.fui-card-inner --}}
          </div>{{-- /.fui-card-body --}}

          {{-- Zona de acciones --}}
          <div class="fui-card-actions">
            @auth
              @if($isInitiator)
                <a href="{{ route('falta-uno.chat', $game) }}" class="fui-btn fui-btn-black">💬 Chat</a>
                <form method="POST" action="{{ route('falta-uno.cancel', $game) }}"
                      onsubmit="return confirm('¿Cancelar el partido? {{ $game->canRefund() ? 'Recibirás un reembolso.' : 'No se devuelve el dinero.' }}')">
                  @csrf
                  <button type="submit" class="fui-btn fui-btn-cancel">Cancelar partido</button>
                </form>
              @elseif($isAuthJoined)
                <span class="fui-badge-joined">✓ Ya estás anotado</span>
                <a href="{{ route('falta-uno.chat', $game) }}" class="fui-btn fui-btn-black">💬 Chat</a>
                @if($game->isFinished())
                  <a href="{{ route('falta-uno.stats', $game) }}" class="fui-btn fui-btn-stats">📊 Mis stats</a>
                  <a href="{{ route('falta-uno.rate', $game) }}" class="fui-btn fui-btn-rate">★ Calificar</a>
                @endif
              @elseif($game->status !== 'full')
                <form method="POST" action="{{ route('falta-uno.join', $game) }}">
                  @csrf
                  <button type="submit" class="fui-btn fui-btn-black">Unirme al partido</button>
                </form>
              @endif
            @else
              <a href="{{ route('login') }}" class="fui-btn fui-btn-outline">Iniciá sesión para unirte</a>
            @endauth
          </div>

        </div>{{-- /.flex:1 --}}
      </div>{{-- /.flex row (stripe + content) --}}

    </div>{{-- /.fui-card --}}
    @endforeach
  </div>
@endif

@auth
<div id="fuToast" style="display:none; position:fixed; bottom:24px; left:50%; transform:translateX(-50%); z-index:9999; min-width:300px; max-width:420px; padding:14px 20px; border-radius:14px; font-size:14px; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,.15); text-align:center; transition:opacity .3s;"></div>

<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.js"></script>
<script>
  function showFuToast(msg, type) {
    const el = document.getElementById('fuToast');
    el.textContent = msg;
    el.style.background = type === 'up' ? '#111' : '#fff';
    el.style.color       = type === 'up' ? '#fff' : '#111';
    el.style.border      = type === 'up' ? 'none' : '1px solid #e0e0e0';
    el.style.display     = 'block';
    el.style.opacity     = '1';
    setTimeout(() => {
      el.style.opacity = '0';
      setTimeout(() => { el.style.display = 'none'; }, 400);
    }, 5000);
  }

  const echoFu = new Echo({
    broadcaster:       'reverb',
    key:               '{{ config('broadcasting.connections.reverb.key') }}',
    wsHost:            '{{ env('REVERB_CLIENT_HOST', config('broadcasting.connections.reverb.options.host')) }}',
    wsPort:            {{ env('REVERB_CLIENT_PORT', 443) }},
    wssPort:           {{ env('REVERB_CLIENT_PORT', 443) }},
    forceTLS:          true,
    enabledTransports: ['ws'],
    authEndpoint:      '/broadcasting/auth',
  });

  echoFu.private('user.{{ auth()->id() }}')
    .listen('.category.changed', (e) => {
      if (e.direction === 'up') {
        confetti({ particleCount: 150, spread: 70, origin: { y: 0.6 } });
        showFuToast('¡Subiste a ' + e.new_category + '! ¡Seguí así!', 'up');
      } else {
        showFuToast('Seguí intentándolo, bajaste a ' + e.new_category + '. ¡El próximo partido será mejor!', 'down');
      }
    });

  echoFu.channel('falta-uno-games')
    .listen('.participant.joined', (e) => {
      const card = document.querySelector(`.fui-card[data-game-id="${e.game_id}"]`);
      if (!card) return;
      const dash   = 283;
      const pct    = e.needed > 0 ? Math.min(100, Math.round((e.joined / e.needed) * 100)) : 100;
      const filled = Math.round(dash * pct / 100);
      const empty  = dash - filled;
      const isFull = e.status === 'full';
      const arc         = card.querySelector('.game-arc');
      const statusText  = card.querySelector('.game-status-text');
      const counterText = card.querySelector('.game-counter-text');
      if (arc) {
        arc.setAttribute('stroke-dasharray', `${filled} ${empty}`);
        arc.setAttribute('stroke', isFull ? '#22c55e' : '#111');
      }
      if (statusText) {
        statusText.innerHTML = isFull
          ? '<span style="color:#22c55e;">¡Completo!</span>'
          : `<span style="color:#111;">Faltan ${e.needed - e.joined}</span>`;
      }
      if (counterText) {
        counterText.textContent = `${e.joined}/${e.needed} anotados`;
      }
    });
</script>
@endauth

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 500, easing: 'ease-out' });

  // Scroll progress bar
  window.addEventListener('scroll', () => {
    const el  = document.getElementById('fuiScrollProgress');
    if (!el) return;
    const max = document.body.scrollHeight - window.innerHeight;
    el.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
  });
</script>
@endpush

@endsection
