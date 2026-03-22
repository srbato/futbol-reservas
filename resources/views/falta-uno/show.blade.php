@extends('layouts.app')

@section('title', 'Partido · ' . ($game->field->name ?? 'Falta Uno'))

@section('content')
@php
  $joined  = $game->activeParticipants->count();
  $needed  = $game->players_needed;
  $pct     = $needed > 0 ? min(100, round(($joined / $needed) * 100)) : 100;
  $sportLabel = match($game->field->sport ?? '') {
    'football'   => '⚽ Fútbol',
    'padel'      => '🏓 Pádel',
    'tennis'     => '🎾 Tenis',
    'basketball' => '🏀 Básquet',
    'volleyball' => '🏐 Vóley',
    default      => ucfirst($game->field->sport ?? 'Cancha'),
  };
  $statusLabel = match($game->status) {
    'open'      => 'Abierto',
    'full'      => 'Completo',
    'cancelled' => 'Cancelado',
    'expired'   => 'Expirado',
    default     => ucfirst($game->status),
  };
  $statusColor = match($game->status) {
    'open'      => 'background:#fef9c3; color:#854d0e;',
    'full'      => 'background:#dcfce7; color:#15803d;',
    'cancelled' => 'background:#fee2e2; color:#991b1b;',
    default     => 'background:#f4f4f4; color:#555;',
  };
@endphp

<div style="max-width:760px; margin:0 auto; display:grid; gap:18px;">

  {{-- Back --}}
  <div>
    <a href="{{ route('falta-uno.index') }}"
       style="display:inline-flex; align-items:center; gap:5px; font-size:13px; color:#888; text-decoration:none; font-weight:600;">
      ← Volver a Falta Uno
    </a>
  </div>

  {{-- Header del partido --}}
  <div style="background:linear-gradient(135deg,#111 0%,#222 100%); border-radius:20px; padding:28px; color:#fff;">
    <div style="display:flex; align-items:flex-start; gap:16px; flex-wrap:wrap;">
      <div style="flex:1;">
        <div style="font-size:12px; color:rgba(255,255,255,.5); font-weight:700; text-transform:uppercase; letter-spacing:.07em; margin-bottom:6px;">
          ⚡ Falta Uno
        </div>
        <h1 style="margin:0 0 4px; font-size:26px; font-weight:800; letter-spacing:-.02em;">
          {{ $game->field->name }}
        </h1>
        <div style="font-size:14px; color:rgba(255,255,255,.6); margin-bottom:16px;">
          <a href="{{ route('venues.show', $game->field->venue) }}"
             style="color:rgba(255,255,255,.7); text-decoration:none;">
            📍 {{ $game->field->venue->name }}
          </a>
          · {{ $sportLabel }}
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
          <span style="background:rgba(255,255,255,.12); color:#fff; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700;">
            📅 {{ $game->start_at->format('d/m/Y') }}
          </span>
          <span style="background:rgba(255,255,255,.12); color:#fff; padding:4px 12px; border-radius:999px; font-size:12px; font-weight:700;">
            🕐 {{ $game->start_at->format('H:i') }} hs
          </span>
          <span style="{{ $statusColor }} padding:4px 12px; border-radius:999px; font-size:12px; font-weight:800;">
            {{ $statusLabel }}
          </span>
        </div>
      </div>

      {{-- Círculo de progreso --}}
      <div style="text-align:center; flex-shrink:0;">
        @php
          $dash   = 283;
          $filled = round($dash * $pct / 100);
          $empty  = $dash - $filled;
        @endphp
        <svg width="90" height="90" viewBox="0 0 100 100" style="transform:rotate(-90deg);">
          <circle cx="50" cy="50" r="45" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="8"/>
          <circle cx="50" cy="50" r="45" fill="none"
                  stroke="{{ $game->status === 'full' ? '#22c55e' : '#fff' }}" stroke-width="8"
                  stroke-dasharray="{{ $filled }} {{ $empty }}"
                  stroke-linecap="round"/>
        </svg>
        <div style="margin-top:-8px; font-size:13px; font-weight:800; color:#fff;">
          {{ $joined }}/{{ $joined + $game->initiator_players + ($needed - $joined) }} jugadores
        </div>
        @if($game->status !== 'full')
          <div style="font-size:11px; color:rgba(255,255,255,.6);">Faltan {{ max(0, $needed - $joined) }}</div>
        @else
          <div style="font-size:11px; color:#22c55e; font-weight:700;">¡Completo!</div>
        @endif
      </div>
    </div>
  </div>

  {{-- Acciones principales (solo participantes) --}}
  @auth
  @if($isParticipant)
  <div class="page-card" style="padding:20px;">
    <h2 style="margin:0 0 14px; font-size:16px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#aaa;">Acciones</h2>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">

      {{-- Chat (siempre visible para participantes) --}}
      <a href="{{ route('falta-uno.chat', $game) }}"
         style="display:inline-flex; align-items:center; gap:7px; background:#111; color:#fff; border-radius:12px; padding:10px 18px; font-size:14px; font-weight:700; text-decoration:none;">
        💬 Chat del partido
      </a>

      {{-- Stats (partido terminado) --}}
      @if($game->isFinished())
        <a href="{{ route('falta-uno.stats', $game) }}"
           style="display:inline-flex; align-items:center; gap:7px; background:#f4f4f4; color:#333; border-radius:12px; padding:10px 18px; font-size:14px; font-weight:700; text-decoration:none;">
          📊 Mis estadísticas
        </a>

        {{-- Calificación --}}
        @if(!$yaCalifico)
          <a href="{{ route('falta-uno.rate', $game) }}"
             style="display:inline-flex; align-items:center; gap:7px; background:#fef3c7; color:#92400e; border-radius:12px; padding:10px 18px; font-size:14px; font-weight:700; text-decoration:none;">
            ★ Calificar compañeros
          </a>
        @else
          <span style="display:inline-flex; align-items:center; gap:6px; background:#f0fdf4; color:#15803d; border-radius:12px; padding:10px 18px; font-size:14px; font-weight:700;">
            ✓ Ya calificaste
          </span>
        @endif
      @endif

      {{-- Cancelar (solo iniciador, partido activo) --}}
      @if($isInitiator && in_array($game->status, ['open', 'full']))
        <form method="POST" action="{{ route('falta-uno.cancel', $game) }}"
              onsubmit="return confirm('¿Cancelar el partido?{{ $game->canRefund() ? ' Recibirás un reembolso.' : ' No se devuelve el dinero.' }}')">
          @csrf
          <button type="submit"
                  style="display:inline-flex; align-items:center; gap:7px; background:#fee2e2; color:#991b1b; border:none; border-radius:12px; padding:10px 18px; font-size:14px; font-weight:700; cursor:pointer;">
            Cancelar partido
          </button>
        </form>
      @endif

    </div>
  </div>
  @endif
  @endauth

  {{-- Unirse (partido abierto, no participante) --}}
  @auth
  @if(!$isParticipant && $game->status === 'open' && !$game->isFinished())
    @if(auth()->user()->faltaUnoSportProfiles()->doesntExist())
      <div class="page-card" style="padding:20px; background:#fffbeb; border-color:#fde68a;">
        <div style="font-size:14px; font-weight:700; color:#92400e; margin-bottom:6px;">Necesitás completar tu perfil deportivo</div>
        <a href="/profile#sport-profile" class="btn btn-primary" style="font-size:13px;">Completar perfil</a>
      </div>
    @else
      <div class="page-card" style="padding:20px;">
        <form method="POST" action="{{ route('falta-uno.join', $game) }}">
          @csrf
          <button type="submit" class="btn btn-primary" style="font-size:15px; padding:12px 28px;">
            Unirme a este partido
          </button>
        </form>
      </div>
    @endif
  @endif
  @endauth
  @guest
    <div class="page-card" style="padding:20px; text-align:center;">
      <p style="margin:0 0 12px; color:#666;">Iniciá sesión para unirte al partido</p>
      <a href="{{ route('login') }}" class="btn btn-primary">Iniciar sesión</a>
    </div>
  @endguest

  {{-- Info del partido --}}
  <div class="page-card" style="padding:20px;">
    <h2 style="margin:0 0 16px; font-size:16px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#aaa;">Detalles del partido</h2>
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(160px, 1fr)); gap:12px;">
      <div style="background:#f8f8f8; border-radius:12px; padding:14px;">
        <div style="font-size:11px; color:#888; font-weight:700; text-transform:uppercase; margin-bottom:4px;">Deporte</div>
        <div style="font-size:15px; font-weight:800;">{{ $sportLabel }}</div>
      </div>
      <div style="background:#f8f8f8; border-radius:12px; padding:14px;">
        <div style="font-size:11px; color:#888; font-weight:700; text-transform:uppercase; margin-bottom:4px;">Total jugadores</div>
        <div style="font-size:15px; font-weight:800;">{{ $game->total_players }}</div>
      </div>
      @if($game->gender_filter && $game->gender_filter !== 'mixed')
      <div style="background:#fce7f3; border-radius:12px; padding:14px;">
        <div style="font-size:11px; color:#db2777; font-weight:700; text-transform:uppercase; margin-bottom:4px;">Género</div>
        <div style="font-size:15px; font-weight:800; color:#db2777;">
          {{ $game->gender_filter === 'male' ? 'Masculino' : 'Femenino' }}
        </div>
      </div>
      @endif
      @if($game->category_min || $game->category_max)
      <div style="background:#f0fdf4; border-radius:12px; padding:14px;">
        <div style="font-size:11px; color:#15803d; font-weight:700; text-transform:uppercase; margin-bottom:4px;">Categoría</div>
        <div style="font-size:15px; font-weight:800; color:#15803d; text-transform:capitalize;">
          @if($game->category_min && $game->category_max && $game->category_min === $game->category_max)
            {{ ucfirst($game->category_min) }}
          @elseif($game->category_min && $game->category_max)
            {{ ucfirst($game->category_min) }} – {{ ucfirst($game->category_max) }}
          @elseif($game->category_min)
            Desde {{ ucfirst($game->category_min) }}
          @else
            Hasta {{ ucfirst($game->category_max) }}
          @endif
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- Lista de participantes --}}
  <div class="page-card" style="padding:20px;">
    <h2 style="margin:0 0 16px; font-size:16px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#aaa;">
      Jugadores ({{ $joined + $game->initiator_players }} / {{ $game->total_players }})
    </h2>

    {{-- Iniciador --}}
    <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f4f4f4;">
      @if($game->initiator->avatar_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($game->initiator->avatar_path) }}"
             style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
      @else
        <div style="width:36px; height:36px; border-radius:50%; background:#f1f1f1; display:flex; align-items:center; justify-content:center; font-size:14px; color:#999; font-weight:800;">
          {{ mb_strtoupper(mb_substr($game->initiator->name, 0, 1)) }}
        </div>
      @endif
      <div style="flex:1;">
        <a href="{{ route('sport-profile.public', $game->initiator) }}"
           style="font-size:14px; font-weight:700; color:#111; text-decoration:none;">
          {{ $game->initiator->name }}
        </a>
        <span style="margin-left:8px; font-size:11px; background:#dbeafe; color:#1d4ed8; padding:2px 8px; border-radius:999px; font-weight:700;">Organizador</span>
      </div>
      <span style="font-size:12px; color:#888; font-weight:600;">{{ $game->initiator_players }} lugar{{ $game->initiator_players > 1 ? 'es' : '' }}</span>
    </div>

    {{-- Participantes --}}
    @foreach($game->activeParticipants as $p)
    <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f4f4f4;">
      @if($p->user->avatar_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($p->user->avatar_path) }}"
             style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
      @else
        <div style="width:36px; height:36px; border-radius:50%; background:#f1f1f1; display:flex; align-items:center; justify-content:center; font-size:14px; color:#999; font-weight:800;">
          {{ mb_strtoupper(mb_substr($p->user->name, 0, 1)) }}
        </div>
      @endif
      <div style="flex:1;">
        <a href="{{ route('sport-profile.public', $p->user) }}"
           style="font-size:14px; font-weight:700; color:#111; text-decoration:none;">
          {{ $p->user->name }}
        </a>
      </div>
      @if($game->isFinished() && $p->result)
        <span style="font-size:12px; padding:2px 10px; border-radius:999px; font-weight:700;
          {{ match($p->result) { 'win'=>'background:#f0fdf4; color:#15803d;', 'draw'=>'background:#fffbeb; color:#b45309;', 'loss'=>'background:#fef2f2; color:#dc2626;', default=>'background:#f4f4f4; color:#888;' } }}">
          {{ match($p->result) { 'win'=>'Victoria', 'draw'=>'Empate', 'loss'=>'Derrota', default=>'-' } }}
        </span>
      @endif
    </div>
    @endforeach

    {{-- Slots vacíos --}}
    @php $slotsVacios = max(0, $needed - $joined); @endphp
    @for($i = 0; $i < $slotsVacios; $i++)
    <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-bottom:1px solid #f4f4f4; opacity:.4;">
      <div style="width:36px; height:36px; border-radius:50%; background:#f0f0f0; border:2px dashed #ccc;"></div>
      <div style="font-size:14px; color:#aaa; font-style:italic;">Lugar disponible</div>
    </div>
    @endfor

  </div>

  {{-- Resultados (si el partido terminó) --}}
  @if($game->isFinished() && $game->activeParticipants->whereNotNull('result')->isNotEmpty())
  <div class="page-card" style="padding:20px; background:#f0fdf4; border-color:#bbf7d0;">
    <h2 style="margin:0 0 14px; font-size:16px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#15803d;">Resultados del partido</h2>
    @php
      $wins   = $game->activeParticipants->where('result', 'win')->count();
      $draws  = $game->activeParticipants->where('result', 'draw')->count();
      $losses = $game->activeParticipants->where('result', 'loss')->count();
    @endphp
    <div style="display:flex; gap:16px; flex-wrap:wrap;">
      <div style="background:#fff; border-radius:12px; padding:12px 20px; text-align:center;">
        <div style="font-size:24px; font-weight:800; color:#22c55e;">{{ $wins }}</div>
        <div style="font-size:11px; color:#888; font-weight:700; text-transform:uppercase;">Victorias</div>
      </div>
      <div style="background:#fff; border-radius:12px; padding:12px 20px; text-align:center;">
        <div style="font-size:24px; font-weight:800; color:#f59e0b;">{{ $draws }}</div>
        <div style="font-size:11px; color:#888; font-weight:700; text-transform:uppercase;">Empates</div>
      </div>
      <div style="background:#fff; border-radius:12px; padding:12px 20px; text-align:center;">
        <div style="font-size:24px; font-weight:800; color:#ef4444;">{{ $losses }}</div>
        <div style="font-size:11px; color:#888; font-weight:700; text-transform:uppercase;">Derrotas</div>
      </div>
    </div>
  </div>
  @endif

</div>
@endsection
