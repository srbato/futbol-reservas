@extends('layouts.app')

@section('title', 'Mi Perfil Deportivo')

@push('styles')
<style>
  .sp-header {
    background: linear-gradient(135deg, #111 0%, #2d2d2d 100%);
    border-radius: 20px;
    padding: 28px 28px;
    margin-bottom: 24px;
    color: #fff;
  }
  .sp-header h1 {
    font-size: 26px;
    font-weight: 800;
    margin: 0 0 6px 0;
    letter-spacing: -.02em;
  }
  .sp-header p {
    font-size: 14px;
    color: rgba(255,255,255,.65);
    margin: 0;
  }

  .sp-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .sp-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,.03);
    transition: box-shadow .2s, transform .2s;
  }
  .sp-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.08);
    transform: translateY(-2px);
  }

  .sp-sport-icon {
    font-size: 28px;
    margin-bottom: 10px;
  }

  .sp-sport-name {
    font-size: 18px;
    font-weight: 800;
    color: #111;
    margin: 0 0 4px 0;
  }

  .sp-category-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: capitalize;
    margin-bottom: 14px;
  }

  .sp-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    margin-bottom: 14px;
  }

  .sp-stat-box {
    text-align: center;
    background: #f8f8f8;
    border-radius: 10px;
    padding: 8px 4px;
  }

  .sp-stat-val {
    font-size: 18px;
    font-weight: 800;
    color: #111;
    display: block;
  }

  .sp-stat-lbl {
    font-size: 10px;
    color: #888;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .05em;
  }

  .sp-stars {
    font-size: 14px;
    color: #f59e0b;
    margin-bottom: 14px;
  }

  .sp-actions {
    display: flex;
    gap: 8px;
  }

  .btn-add-sport {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #111;
    color: #fff;
    border-radius: 12px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 700;
    text-decoration: none;
    transition: background .15s;
  }
  .btn-add-sport:hover { background: #222; color: #fff; }
</style>
@endpush

@section('content')

<div class="sp-header">
  <h1>Mi Perfil Deportivo</h1>
  <p>Tus estadísticas y categoría por deporte para unirte a partidos Falta Uno.</p>
</div>

@if(session('success'))
  <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:12px 16px; margin-bottom:16px; color:#15803d; font-weight:600; font-size:14px;">
    {{ session('success') }}
  </div>
@endif

@if($profiles->isEmpty())
  <div class="page-card" style="text-align:center; padding:48px 24px; margin-bottom:20px;">
    <div style="font-size:48px; margin-bottom:12px;">🎯</div>
    <p style="font-size:16px; font-weight:700; margin:0 0 6px;">Aún no tenés ningún perfil deportivo</p>
    <p style="color:#888; font-size:14px; margin:0 0 20px;">Creá tu perfil para poder unirte a partidos Falta Uno.</p>
    <a href="{{ route('sport-profile.create') }}" class="btn-add-sport">
      + Agregar deporte
    </a>
  </div>
@else
  <div class="sp-grid">
    @foreach($profiles as $profile)
    @php
      $sportLabel = match($profile->sport) {
        'football'   => '⚽ Fútbol',
        'padel'      => '🏓 Pádel',
        'tennis'     => '🎾 Tenis',
        'basketball' => '🏀 Básquet',
        'volleyball' => '🏐 Vóley',
        default      => ucfirst($profile->sport),
      };
      $catColors = match($profile->category) {
        'recreativo', 'cuarta'   => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
        'intermedio', 'tercera'  => ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
        'avanzado', 'segunda'    => ['bg' => '#fef3c7', 'color' => '#d97706'],
        'competitivo', 'primera' => ['bg' => '#fce7f3', 'color' => '#db2777'],
        default                  => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
      };
      $stars = round($profile->average_rating);
    @endphp
    <div class="sp-card">
      <div class="sp-sport-icon">{{ explode(' ', $sportLabel)[0] }}</div>
      <h3 class="sp-sport-name">{{ ltrim(strstr($sportLabel, ' ')) }}</h3>

      <span class="sp-category-badge" style="background:{{ $catColors['bg'] }}; color:{{ $catColors['color'] }};">
        {{ $profile->category }}
      </span>

      @if($profile->age_group)
        <span style="font-size:11px; background:#f4f4f4; color:#555; padding:2px 9px; border-radius:999px; font-weight:600; margin-left:4px;">
          {{ $profile->age_group }}
        </span>
      @endif

      <div class="sp-stats-row">
        <div class="sp-stat-box">
          <span class="sp-stat-val">{{ $profile->games_played }}</span>
          <span class="sp-stat-lbl">PJ</span>
        </div>
        <div class="sp-stat-box">
          <span class="sp-stat-val" style="color:#22c55e;">{{ $profile->wins }}</span>
          <span class="sp-stat-lbl">PG</span>
        </div>
        <div class="sp-stat-box">
          <span class="sp-stat-val" style="color:#f59e0b;">{{ $profile->draws }}</span>
          <span class="sp-stat-lbl">PE</span>
        </div>
        <div class="sp-stat-box">
          <span class="sp-stat-val" style="color:#ef4444;">{{ $profile->losses }}</span>
          <span class="sp-stat-lbl">PP</span>
        </div>
      </div>

      <div class="sp-stars">
        @for($i = 1; $i <= 5; $i++)
          {{ $i <= $stars ? '★' : '☆' }}
        @endfor
        <span style="font-size:12px; color:#888; margin-left:4px;">
          {{ number_format($profile->average_rating, 1) }}
        </span>
      </div>

      <div class="sp-actions">
        <a href="{{ route('sport-profile.edit', $profile->sport) }}"
           style="flex:1; text-align:center; padding:8px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:13px; font-weight:600; color:#333; text-decoration:none; transition:border-color .15s;"
           onmouseover="this.style.borderColor='#bbb'" onmouseout="this.style.borderColor='#e0e0e0'">
          Editar
        </a>
        <a href="{{ route('sport-profile.public', auth()->user()) }}"
           style="flex:1; text-align:center; padding:8px; border:1.5px solid #e0e0e0; border-radius:10px; font-size:13px; font-weight:600; color:#333; text-decoration:none; transition:border-color .15s;"
           onmouseover="this.style.borderColor='#bbb'" onmouseout="this.style.borderColor='#e0e0e0'">
          Ver público
        </a>
      </div>
    </div>
    @endforeach
  </div>

  <a href="{{ route('sport-profile.create') }}" class="btn-add-sport">
    + Agregar deporte
  </a>
@endif

@endsection
