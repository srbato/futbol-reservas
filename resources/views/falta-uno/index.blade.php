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
    background: var(--color-primary);
    z-index: 9999;
    transition: width .1s linear;
  }

  /* ── Hero ──────────────────────────────────────── */
  .fui-hero {
    position: relative;
    height: 360px;
    border-radius: 28px;
    overflow: visible;
    margin-bottom: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .fui-hero-bg,
  .fui-hero-overlay,
  .fui-hero-gradient {
    border-radius: 28px;
  }
  .fui-hero-bg {
    position: absolute;
    inset: 0;
    background-image: url('/images/jugadores-falta-uno.webp');
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
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 16px;
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
    border-radius: var(--radius-full);
    margin-bottom: 12px;
  }
  .fui-badge-dot {
    width: 7px;
    height: 7px;
    background: var(--color-primary);
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
    color: var(--color-text-inverse);
    letter-spacing: -.03em;
    line-height: 1.05;
  }
  .fui-hero-sub {
    margin: 0;
    font-size: 16px;
    color: rgba(255,255,255,.7);
  }
  .fui-hero-cta-wrap {
    position: relative;
    z-index: 3;
    text-align: center;
    margin-top: -56px;
    margin-bottom: 18px;
  }
  .fui-hero-cta {
    display: inline-block;
    background: var(--color-primary);
    color: #052e16;
    border-radius: 14px;
    padding: 14px 32px;
    font-size: 15px;
    font-weight: 800;
    text-decoration: none;
    white-space: nowrap;
    transition: background .15s, transform .15s, box-shadow .15s;
    box-shadow: 0 6px 20px rgba(34,197,94,.35);
  }
  .fui-hero-cta:hover { background: var(--color-primary-hover); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(34,197,94,.45); }

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
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 16px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
    text-align: center;
  }
  .fui-filter-divider {
    border: none;
    border-top: 1px solid var(--color-border);
    margin: 12px 0;
  }
  .fui-filter-row {
    display: flex;
    gap: 8px;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: center;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    padding-bottom: 4px;
  }
  .fui-filter-row::-webkit-scrollbar { display: none; }
  .fui-pill {
    padding: 7px 16px;
    border: 1.5px solid var(--color-border);
    background: var(--color-bg);
    border-radius: var(--radius-full);
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    transition: border-color .15s, background .15s, color .15s;
    color: #555;
    text-decoration: none;
    display: inline-block;
    flex-shrink: 0;
    white-space: nowrap;
  }
  .fui-pill:hover { border-color: var(--color-primary); color: var(--color-primary-hover); }
  .fui-pill.active { background: var(--color-primary); color: var(--color-text-inverse); border-color: var(--color-primary); }
  .fui-pill-sm { font-size: 12px; padding: 5px 13px; }
  .fui-filter-label {
    font-size: 12px;
    color: var(--color-text-muted);
    font-weight: 700;
    flex-shrink: 0;
  }
  .fui-filter-sep {
    color: var(--color-border);
    font-size: 16px;
    line-height: 1;
    flex-shrink: 0;
  }

  /* ── Card avatars ──────────────────────────────── */
  .fui-card-avatars {
    display: flex;
    align-items: center;
  }
  .fui-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #e8e8e8;
    border: 2px solid var(--color-bg-card);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
    color: var(--color-text-secondary);
    overflow: hidden;
    margin-left: -8px;
    flex-shrink: 0;
  }
  .fui-avatar:first-child { margin-left: 0; }
  .fui-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .fui-avatar-initiator {
    border-color: var(--color-primary);
    z-index: 1;
    background: rgba(34,197,94,.15);
    color: var(--color-primary-hover);
  }
  .fui-avatar-extra {
    background: var(--color-bg-hover);
    font-size: 10px;
    color: var(--color-text-muted);
  }

  /* ── Card logo ───────────────────────────────── */
  .fui-card-logo {
    position: absolute;
    bottom: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
    opacity: 1;
    pointer-events: none;
  }

  /* ── Games section ─────────────────────────────── */
  .fui-games-section {
    position: relative;
    background: #f9fafb;
    border-radius: 24px;
    padding: 28px 24px;
    overflow: hidden;
  }
  .fui-games-deco {
    position: absolute;
    top: -80px;
    right: -60px;
    width: 300px;
    height: 300px;
    background: linear-gradient(135deg, rgba(34,197,94,.06) 0%, rgba(34,197,94,.02) 100%);
    border-radius: 50%;
    pointer-events: none;
  }
  .fui-games-section::after {
    content: '';
    position: absolute;
    bottom: -40px;
    left: -30px;
    width: 200px;
    height: 200px;
    background: linear-gradient(135deg, rgba(34,197,94,.04) 0%, transparent 100%);
    border-radius: 50%;
    pointer-events: none;
  }

  /* ── Cards ─────────────────────────────────────── */
  .fui-games-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
  }
  @media (max-width: 1024px) { .fui-games-grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 600px) { .fui-games-grid { grid-template-columns: 1fr; } }

  .fui-card {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 16px;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    position: relative;
    box-shadow: var(--shadow-sm);
  }
  .fui-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0,0,0,.10);
    border-color: var(--color-primary);
  }
  .fui-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: 16px 16px 0 0;
    background: linear-gradient(90deg, var(--color-primary), var(--color-primary-light));
    opacity: 0;
    transition: opacity .18s ease;
  }
  .fui-card:hover::before {
    opacity: 1;
  }

  /* Círculo SVG */
  .fui-progress-wrap {
    flex-shrink: 0;
    text-align: center;
    width: 52px;
  }
  .fui-progress-circle {
    transform: rotate(-90deg);
    display: block;
  }
  @keyframes fui-stroke-draw {
    from { stroke-dashoffset: 138; }
  }
  .fui-arc-animated {
    animation: fui-stroke-draw .8s ease-out both;
  }
  .fui-progress-label {
    margin-top: 4px;
    font-size: 12px;
    font-weight: 700;
  }
  .fui-progress-sub {
    font-size: 11px;
    color: var(--color-text-muted);
    margin-top: 2px;
  }

  /* Zona de acciones */
  .fui-card-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
    margin-top: auto;
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
  .fui-btn-black   { background: var(--color-bg-dark); color: var(--color-text-inverse); }
  .fui-btn-black:hover { background: var(--color-primary); color: #052e16; }
  .fui-btn-ghost   { background: transparent; border: 1.5px solid var(--color-border); color: #333; }
  .fui-btn-cancel  { background: transparent; border: 1.5px solid #fecaca; color: #dc2626; }
  .fui-btn-outline { background: transparent; border: 1.5px solid #d1d5db; color: #555; }
  .fui-btn-stats   { background: var(--color-bg-hover); color: #333; }
  .fui-btn-rate    { background: #fef3c7; color: #92400e; }
  .fui-badge-joined {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--color-primary);
    font-weight: 700;
  }

  /* ── Estado vacío ──────────────────────────────── */
  .fui-empty {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
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
  .fui-empty p { color: var(--color-text-muted); font-size: 14px; margin: 0 0 24px; }
  .fui-empty-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }

  /* ── Mobile ────────────────────────────────────── */
  @media (max-width: 600px) {
    .fui-hero { height: 280px; }
    .fui-hero-h1 { font-size: 32px; }
    .fui-hero-content { padding: 20px; }
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
    <h1 class="fui-hero-h1" data-aos="fade-up" data-aos-delay="100">Encontrá tu partido</h1>
    <p class="fui-hero-sub" data-aos="fade-up" data-aos-delay="180">Partidos armándose. Anotate y jugá.</p>
  </div>
</div>
<div class="fui-hero-cta-wrap" data-aos="fade-up" data-aos-delay="260">
  <button onclick="document.getElementById('fuiFieldModal').style.display='flex'" class="fui-hero-cta" style="border:none;cursor:pointer;">+ Crear partido</button>
</div>

{{-- Banner sin perfil deportivo --}}
@auth
  @if(auth()->user()->faltaUnoSportProfiles()->doesntExist())
    <div class="fui-profile-banner" data-aos="fade-right">
      <div style="flex-shrink:0;"><i data-lucide="alert-triangle" style="width:20px;height:20px;stroke:#92400e;stroke-width:2;"></i></div>
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

{{-- Link a mis partidos --}}
@auth
  <div style="display:flex; justify-content:flex-end; margin-bottom:12px;" data-aos="fade-left">
    <a href="{{ route('my_reservations', ['tab' => 'faltauno']) }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:700; color:#16a34a; text-decoration:none; padding:6px 14px; border:1.5px solid #dcfce7; border-radius:999px; background:#f0fdf4; transition:all .15s;" onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
      <i data-lucide="user" style="width:14px;height:14px;stroke:currentColor;"></i> Mis partidos
    </a>
  </div>
@endauth

{{-- Filtros --}}
<div class="fui-filters-wrap">
  {{-- Fila 1: deporte --}}
  <div class="fui-filter-row">
    <a href="{{ route('falta-uno.index', array_filter(['gender' => $gender ?? null, 'category' => $category ?? null, 'zone' => $zone ?? null])) }}"
       class="fui-pill {{ !$sport ? 'active' : '' }}"
       data-aos="fade-up" data-aos-delay="0">Todos</a>
    @php $sportPills = ['football'=>'Fútbol','padel'=>'Pádel','tennis'=>'Tenis','basketball'=>'Básquet','volleyball'=>'Vóley']; $pi = 0; @endphp
    @foreach($sportPills as $val => $label)
      @php $pi++ @endphp
      <a href="{{ route('falta-uno.index', array_filter(['sport' => $val, 'gender' => $gender ?? null, 'category' => $category ?? null, 'zone' => $zone ?? null])) }}"
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
      <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$val ?: null,'category'=>$category ?? null,'zone'=>$zone ?? null])) }}"
         class="fui-pill fui-pill-sm {{ ($gender ?? '') === $val ? 'active' : '' }}"
         data-aos="fade-up" data-aos-delay="{{ $gi * 50 }}">{{ $label }}</a>
    @endforeach
    <span class="fui-filter-sep">|</span>
    <span class="fui-filter-label">Categoría:</span>
    <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$gender ?? null,'zone'=>$zone ?? null])) }}"
       class="fui-pill fui-pill-sm {{ ($category ?? '') === '' ? 'active' : '' }}"
       data-aos="fade-up" data-aos-delay="{{ ++$gi * 50 }}">Cualquier cat.</a>
    @foreach($visibleCategories as $val => $label)
      @php $gi++ @endphp
      <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$gender ?? null,'category'=>$val,'zone'=>$zone ?? null])) }}"
         class="fui-pill fui-pill-sm {{ ($category ?? '') === $val ? 'active' : '' }}"
         data-aos="fade-up" data-aos-delay="{{ min($gi * 50, 400) }}">{{ $label }}</a>
    @endforeach
  </div>

  {{-- Fila 3: zona (solo si hay zonas disponibles) --}}
  @if($zones->isNotEmpty())
  <hr class="fui-filter-divider">
  <div class="fui-filter-row">
    <span class="fui-filter-label">Zona:</span>
    <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$gender ?? null,'category'=>$category ?? null])) }}"
       class="fui-pill fui-pill-sm {{ !$zone ? 'active' : '' }}"
       data-aos="fade-up" data-aos-delay="0">Todas las zonas</a>
    @foreach($zones as $zi => $z)
      <a href="{{ route('falta-uno.index', array_filter(['sport'=>$sport,'gender'=>$gender ?? null,'category'=>$category ?? null,'zone'=>$z])) }}"
         class="fui-pill fui-pill-sm {{ ($zone ?? '') === $z ? 'active' : '' }}"
         data-aos="fade-up" data-aos-delay="{{ min(($zi + 1) * 50, 400) }}">{{ $z }}</a>
    @endforeach
  </div>
  @endif
</div>

{{-- Listado de partidos --}}
<div class="fui-games-section">
  <div class="fui-games-deco" aria-hidden="true"></div>
@if($games->isEmpty())
  <div class="fui-empty" data-aos="zoom-in">
    <img class="fui-empty-icon" src="/images/silueta-jugador-vacia.webp" alt="" width="80" height="80"
         style="width:80px; height:80px; object-fit:contain; border-radius:0;">
    <h3>No hay partidos disponibles</h3>
    <p>¿Por qué no iniciás uno vos? Elegí una cancha con Falta Uno habilitado.</p>
    <div class="fui-empty-actions">
      <button onclick="document.getElementById('fuiFieldModal').style.display='flex'" class="btn btn-primary" style="border:none;cursor:pointer;">Crear partido</button>
      <a href="{{ route('venues.index') }}" class="btn">Ver complejos</a>
    </div>
  </div>
@else
  <div class="fui-games-grid">
    @foreach($games as $idx => $game)
    @php
      $joined  = $game->activeParticipants->count();
      $needed  = $game->players_needed;
      $pct     = $needed > 0 ? min(100, round(($joined / $needed) * 100)) : 100;
      $dash    = 138;
      $filled  = round($dash * $pct / 100);
      $empty   = $dash - $filled;
      $isAuthJoined = auth()->check() && $game->activeParticipants->contains('user_id', auth()->id());
      $isInitiator  = auth()->check() && $game->initiator_user_id === auth()->id();
      $sportLabel   = match($game->field->sport ?? '') {
        'football'   => 'Fútbol',
        'padel'      => 'Pádel',
        'tennis'     => 'Tenis',
        'basketball' => 'Básquet',
        'volleyball' => 'Vóley',
        default      => ucfirst($game->field->sport ?? 'Cancha'),
      };
      $cardDelay = min($idx * 80, 400);
    @endphp

    <div class="fui-card" data-game-id="{{ $game->id }}"
         data-aos="fade-up" data-aos-delay="{{ $cardDelay }}">

      {{-- Top: Progress circle + venue thumbnail --}}
      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div class="fui-progress-wrap">
          <svg class="fui-progress-circle" width="52" height="52" viewBox="0 0 52 52">
            <circle cx="26" cy="26" r="22" fill="none" stroke="rgba(34,197,94,.12)" stroke-width="5"/>
            <circle cx="26" cy="26" r="22" fill="none"
                    stroke="{{ $game->status === 'full' ? '#22c55e' : '#111' }}" stroke-width="5"
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

        <div style="width:72px; height:72px; border-radius:12px; overflow:hidden; flex-shrink:0; background:var(--color-bg-page); border:1px solid var(--color-border); display:flex; align-items:center; justify-content:center;">
          @if($game->field->venue->cover_image_path)
            <img src="{{ Storage::url($game->field->venue->cover_image_path) }}" style="width:100%; height:100%; object-fit:cover;" alt="{{ $game->field->venue->name }}">
          @else
            <i data-lucide="map-pin" style="width:24px; height:24px; stroke:var(--color-text-muted);"></i>
          @endif
        </div>
      </div>

      {{-- Middle: Game info --}}
      <div>
        <div style="margin-bottom:8px;">
          <span style="font-weight:800; font-size:16px; color:var(--color-primary-hover);">{{ $sportLabel }}</span>
          <span style="font-size:13px; color:var(--color-text-muted);"> &middot; {{ $game->field->venue->name }}</span>
        </div>
        <div style="display:flex; flex-direction:column; gap:5px; font-size:13px; color:var(--color-text-secondary);">
          <span style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="calendar" style="width:13px;height:13px;stroke:currentColor;flex-shrink:0;"></i> {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y') }}</span>
          <span style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="clock" style="width:13px;height:13px;stroke:currentColor;flex-shrink:0;"></i> {{ \Carbon\Carbon::parse($game->start_at)->format('H:i') }} hs</span>
          <span style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="users" style="width:13px;height:13px;stroke:currentColor;flex-shrink:0;"></i> {{ $game->gender_filter === 'male' ? 'Masculino' : ($game->gender_filter === 'female' ? 'Femenino' : 'Mixto') }}</span>
          @if($game->category_min || $game->category_max)
            <span style="display:inline-flex;align-items:center;gap:6px;"><i data-lucide="trophy" style="width:13px;height:13px;stroke:currentColor;flex-shrink:0;"></i>
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
      </div>

      {{-- Participants avatars --}}
      <div class="fui-card-avatars">
        @php
          $initiator = $game->activeParticipants->firstWhere('user_id', $game->initiator_user_id);
          $others = $game->activeParticipants->where('user_id', '!=', $game->initiator_user_id)->take(3);
          $extraCount = max(0, $joined - 4);
        @endphp
        @if($initiator && $initiator->user)
          <div class="fui-avatar fui-avatar-initiator" title="{{ $initiator->user->name }} (organizador)">
            @if($initiator->user->avatar_path)
              <img src="{{ Storage::url($initiator->user->avatar_path) }}" alt="">
            @else
              <i data-lucide="user" style="width:14px;height:14px;stroke:currentColor;stroke-width:2;"></i>
            @endif
          </div>
        @endif
        @foreach($others as $p)
          @if($p->user)
            <div class="fui-avatar" title="{{ $p->user->name }}">
              @if($p->user->avatar_path)
                <img src="{{ Storage::url($p->user->avatar_path) }}" alt="">
              @else
                <i data-lucide="user" style="width:14px;height:14px;stroke:currentColor;stroke-width:2;"></i>
              @endif
            </div>
          @endif
        @endforeach
        @if($extraCount > 0)
          <div class="fui-avatar fui-avatar-extra">+{{ $extraCount }}</div>
        @endif
      </div>

      {{-- Mini logo --}}
      <img src="/images/logo-multicolor-responsive.svg" alt="" class="fui-card-logo">

      {{-- Bottom: Action buttons --}}
      <div class="fui-card-actions">
        @auth
          @if($isInitiator)
            <a href="{{ route('falta-uno.chat', $game) }}" class="fui-btn fui-btn-black" style="display:inline-flex;align-items:center;gap:5px;font-size:12px;"><i data-lucide="message-circle" style="width:12px;height:12px;stroke:currentColor;"></i> Chat</a>
            <form method="POST" action="{{ route('falta-uno.cancel', $game) }}"
                  onsubmit="return confirm('¿Cancelar el partido? {{ $game->canRefund() ? 'Recibirás un reembolso.' : 'No se devuelve el dinero.' }}')">
              @csrf
              <button type="submit" class="fui-btn fui-btn-cancel" style="font-size:12px;">Cancelar</button>
            </form>
          @elseif($isAuthJoined)
            <span class="fui-badge-joined" style="display:inline-flex;align-items:center;gap:5px;font-size:12px;"><i data-lucide="check-circle" style="width:12px;height:12px;stroke:currentColor;"></i> Anotado</span>
            <a href="{{ route('falta-uno.chat', $game) }}" class="fui-btn fui-btn-black" style="display:inline-flex;align-items:center;gap:5px;font-size:12px;"><i data-lucide="message-circle" style="width:12px;height:12px;stroke:currentColor;"></i> Chat</a>
            @if($game->isFinished())
              <a href="{{ route('falta-uno.stats', $game) }}" class="fui-btn fui-btn-stats" style="display:inline-flex;align-items:center;gap:5px;font-size:12px;"><i data-lucide="bar-chart-2" style="width:12px;height:12px;stroke:currentColor;"></i> Stats</a>
              <a href="{{ route('falta-uno.rate', $game) }}" class="fui-btn fui-btn-rate" style="display:inline-flex;align-items:center;gap:5px;font-size:12px;"><i data-lucide="star" style="width:12px;height:12px;stroke:currentColor;"></i> Calificar</a>
            @endif
          @elseif($game->status !== 'full')
            <form method="POST" action="{{ route('falta-uno.join', $game) }}">
              @csrf
              <button type="submit" class="fui-btn fui-btn-black" style="font-size:12px;">Unirme</button>
            </form>
          @endif
        @else
          <a href="{{ route('login') }}" class="fui-btn fui-btn-outline" style="font-size:12px;">Iniciá sesión</a>
        @endauth

      </div>

      {{-- Invite friends button (only for open games) --}}
      @if($game->status === 'open')
        @php
          $remaining = $game->players_needed - $joined;
          $sportName = match($game->field->sport) { 'football'=>'Fútbol','padel'=>'Pádel','tennis'=>'Tenis','basketball'=>'Básquet','volleyball'=>'Vóley',default=>$game->field->sport };
          $inviteMsg = "Sumate al partido de {$sportName} el " . $game->start_at->format('d/m') . " a las " . $game->start_at->format('H:i') . " en " . $game->field->venue->name . ". Faltan {$remaining} jugadores!\n" . url('/falta-uno');
        @endphp
        <a href="https://wa.me/?text={{ urlencode($inviteMsg) }}" target="_blank" rel="noopener"
           class="fui-btn" style="background:transparent; border:1px solid var(--color-border); color:var(--color-text-secondary); font-size:12px; display:inline-flex; align-items:center; gap:5px; width:fit-content;">
          <i data-lucide="share-2" style="width:12px; height:12px; stroke:currentColor;"></i> Invitar amigos
        </a>
      @endif

    </div>{{-- /.fui-card --}}
    @endforeach
  </div>
@endif
</div>{{-- /.fui-games-section --}}

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
    wsHost:            '{{ config('broadcasting.connections.reverb.client_host') }}',
    wsPort:            {{ config('broadcasting.connections.reverb.client_port') }},
    wssPort:           {{ config('broadcasting.connections.reverb.client_port') }},
    forceTLS:          true,
    enabledTransports: ['ws', 'wss'],
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
      const dash   = 138;
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

{{-- Modal selector de cancha para crear partido --}}
<div id="fuiFieldModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,.5); align-items:center; justify-content:center; padding:20px;" onclick="if(event.target===this)this.style.display='none'">
  <div style="background:#fff; border-radius:18px; max-width:520px; width:100%; max-height:80vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <div style="padding:20px 24px 0; display:flex; justify-content:space-between; align-items:center;">
      <h3 style="margin:0; font-size:20px; font-weight:800;">Elegir cancha</h3>
      <button onclick="document.getElementById('fuiFieldModal').style.display='none'" style="background:none; border:none; cursor:pointer; padding:4px;">
        <i data-lucide="x" style="width:20px;height:20px;stroke:#999;"></i>
      </button>
    </div>
    <p style="padding:0 24px; margin:8px 0 16px; font-size:14px; color:#666;">Selecciona una cancha con Falta Uno habilitado para crear tu partido.</p>

    <div id="fuiFieldSearch" style="padding:0 24px 12px;">
      <input type="text" id="fuiFieldSearchInput" placeholder="Buscar por cancha o complejo..." oninput="filterFuiFields()" style="width:100%; padding:10px 14px; border:1.5px solid #e5e7eb; border-radius:10px; font-size:14px; outline:none; box-sizing:border-box;" onfocus="this.style.borderColor='#16a34a'" onblur="this.style.borderColor='#e5e7eb'">
    </div>

    <div id="fuiFieldList" style="padding:0 24px 20px; display:grid; gap:8px;">
      @foreach($faltaUnoFields as $f)
        <a href="{{ route('falta-uno.create', $f) }}" class="fui-field-option" data-search="{{ strtolower($f->name . ' ' . $f->venue->name) }}" style="display:flex; align-items:center; gap:12px; padding:12px 14px; border:1.5px solid #f0f0f0; border-radius:12px; text-decoration:none; color:inherit; transition:all .15s;" onmouseover="this.style.borderColor='#16a34a';this.style.background='#f0fdf4'" onmouseout="this.style.borderColor='#f0f0f0';this.style.background='transparent'">
          <div style="width:40px; height:40px; border-radius:10px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            @php
              $sportIcon = match($f->sport) {
                'football' => 'circle-dot', 'padel' => 'table-tennis-paddle',
                'tennis' => 'circle', 'basketball' => 'dribbble',
                default => 'map-pin',
              };
            @endphp
            <i data-lucide="{{ $sportIcon }}" style="width:18px;height:18px;stroke:#16a34a;stroke-width:2;"></i>
          </div>
          <div style="flex:1; min-width:0;">
            <div style="font-weight:700; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $f->name }}</div>
            <div style="font-size:12px; color:#888; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $f->venue->name }} · {{ ucfirst($f->sport) }}</div>
          </div>
          <i data-lucide="chevron-right" style="width:16px;height:16px;stroke:#ccc;flex-shrink:0;"></i>
        </a>
      @endforeach

      @if($faltaUnoFields->isEmpty())
        <div style="text-align:center; padding:28px 16px;">
          <i data-lucide="map-pin-off" style="width:32px;height:32px;stroke:#ccc;stroke-width:1.5;margin-bottom:8px;"></i>
          <div style="font-weight:700; font-size:14px; color:#888;">No hay canchas con Falta Uno habilitado</div>
          <div style="font-size:13px; color:#aaa; margin-top:4px;">Cuando un complejo active esta función, sus canchas aparecerán acá</div>
        </div>
      @endif
    </div>
  </div>
</div>

@push('scripts')
<script>
  function filterFuiFields() {
    const q = document.getElementById('fuiFieldSearchInput').value.toLowerCase();
    document.querySelectorAll('.fui-field-option').forEach(function(el) {
      el.style.display = el.dataset.search.includes(q) ? 'flex' : 'none';
    });
  }
</script>
@endpush

@endsection
