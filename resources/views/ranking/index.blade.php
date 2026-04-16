@extends('layouts.app')

@section('title', 'Ranking de Jugadores')

@push('styles')
<style>
    .ranking-hero {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%);
        border-radius: 1.25rem;
        padding: 48px 32px;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
    }
    .ranking-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(52,211,153,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .ranking-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: 20%;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(16,185,129,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .ranking-hero h1 {
        color: #ffffff !important;
        font-size: 2rem;
        font-weight: 800;
        margin: 0 0 8px;
        position: relative;
        z-index: 1;
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    .ranking-hero p {
        color: #d1fae5 !important;
        font-size: 1.05rem;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 1px 2px rgba(0,0,0,0.2);
    }

    .sport-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .sport-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 999px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        border: 1.5px solid rgba(255,255,255,.1);
        background: #111;
        color: #a0a0a0;
        transition: all 0.2s;
    }
    .sport-pill:hover {
        border-color: #22c55e;
        color: #6ee7a0;
        background: rgba(34,197,94,.1);
    }
    .sport-pill.active {
        background: #059669;
        color: #fff;
        border-color: #059669;
    }

    .ranking-table-wrap {
        background: #111;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 1rem;
        overflow: hidden;
    }
    .ranking-table {
        width: 100%;
        border-collapse: collapse;
    }
    .ranking-table thead th {
        background: rgba(255,255,255,.02);
        padding: 14px 16px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        text-align: left;
        border-bottom: 1px solid rgba(255,255,255,.08);
        white-space: nowrap;
    }
    .ranking-table tbody tr {
        border-bottom: 1px solid rgba(255,255,255,.06);
        transition: background 0.15s;
    }
    .ranking-table tbody tr:last-child {
        border-bottom: none;
    }
    .ranking-table tbody tr:hover {
        background: rgba(255,255,255,.02);
    }
    .ranking-table tbody td {
        padding: 14px 16px;
        font-size: 0.9rem;
        color: #e8e8e8;
        vertical-align: middle;
    }

    .rank-pos {
        font-weight: 800;
        font-size: 1.1rem;
        width: 44px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .rank-1 {
        background: linear-gradient(135deg, rgba(245,158,11,.15), rgba(245,158,11,.25));
        color: #fbbf24;
    }
    .rank-2 {
        background: linear-gradient(135deg, rgba(255,255,255,.06), rgba(255,255,255,.1));
        color: #a0a0a0;
    }
    .rank-3 {
        background: linear-gradient(135deg, rgba(234,88,12,.1), rgba(234,88,12,.18));
        color: #fb923c;
    }
    .rank-default {
        background: rgba(255,255,255,.04);
        color: #666;
    }

    .player-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .player-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255,255,255,.1);
    }
    .player-avatar-placeholder {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 700;
        color: #666;
    }
    .player-name {
        font-weight: 700;
        color: #e8e8e8;
        text-decoration: none;
    }
    .player-name:hover {
        color: #059669;
    }
    .player-meta {
        font-size: 0.78rem;
        color: #666;
        margin-top: 2px;
    }

    .stat-cell {
        text-align: center;
        font-weight: 600;
        font-variant-numeric: tabular-nums;
    }
    .stat-win  { color: #16a34a; }
    .stat-draw { color: #ca8a04; }
    .stat-loss { color: #dc2626; }

    .win-rate-bar {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .win-rate-track {
        width: 60px;
        height: 6px;
        background: rgba(255,255,255,.06);
        border-radius: 3px;
        overflow: hidden;
    }
    .win-rate-fill {
        height: 100%;
        border-radius: 3px;
        background: #22c55e;
        transition: width 0.4s;
    }

    .rating-stars {
        display: flex;
        align-items: center;
        gap: 3px;
        color: #fbbf24;
        font-size: 0.85rem;
    }
    .rating-value {
        color: #a0a0a0;
        font-size: 0.8rem;
        margin-left: 4px;
    }

    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 600;
        border: 1px solid;
        white-space: nowrap;
    }

    .ranking-score {
        font-weight: 800;
        font-size: 1rem;
        color: #059669;
    }

    .empty-state {
        text-align: center;
        padding: 80px 24px;
    }
    .empty-state-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(34,197,94,.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .empty-state h3 {
        font-size: 1.15rem;
        font-weight: 700;
        color: #e8e8e8;
        margin: 0 0 8px;
    }
    .empty-state p {
        color: #666;
        font-size: 0.95rem;
        margin: 0;
    }

    @media (max-width: 768px) {
        .ranking-hero { padding: 32px 20px; }
        .ranking-hero h1 { font-size: 1.5rem; }
        .ranking-table thead { display: none; }
        .ranking-table tbody tr {
            display: flex;
            flex-wrap: wrap;
            padding: 16px;
            gap: 8px;
            align-items: center;
        }
        .ranking-table tbody td {
            padding: 0;
            border: none;
        }
        .td-rank { order: 1; flex: 0 0 auto; }
        .td-player { order: 2; flex: 1 1 0; }
        .td-score { order: 3; flex: 0 0 auto; }
        .td-stats { order: 4; flex: 1 1 100%; font-size: 0.8rem; display: flex; gap: 12px; padding-top: 8px !important; }
        .td-winrate { order: 5; flex: 0 0 auto; }
        .td-rating { display: none; }
        .td-badges { order: 6; flex: 1 1 100%; padding-top: 6px !important; }
    }
</style>
@endpush

@section('content')
<div style="max-width:960px; margin:0 auto; padding:0 16px 60px;">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg, #064e3b 0%, #065f46 50%, #047857 100%); border-radius:1.25rem; padding:48px 32px 24px; margin-bottom:24px; position:relative; overflow:hidden;">
        <h1 style="color:#fff; font-size:2rem; font-weight:800; margin:0 0 8px; text-shadow:0 2px 4px rgba(0,0,0,0.4);">Ranking de Jugadores</h1>
        <p style="color:#d1fae5; font-size:1.05rem; margin:0 0 24px; text-shadow:0 1px 3px rgba(0,0,0,0.3);">Los mejores jugadores de la comunidad, ordenados por rendimiento, asistencia y reputacion.</p>

        {{-- Sport filters dentro del hero --}}
        <div class="sport-pills" style="margin-bottom:0;">
        <a href="{{ route('ranking.index') }}" class="sport-pill {{ !$sportFilter ? 'active' : '' }}">
            <i data-lucide="trophy" style="width:16px;height:16px;"></i>
            Todos
        </a>
        @foreach($sportLabels as $key => $label)
            <a href="{{ route('ranking.index', ['sport' => $key]) }}" class="sport-pill {{ $sportFilter === $key ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
        </div>
    </div>

    @if($players->isEmpty())
        {{-- Empty state --}}
        <div class="ranking-table-wrap">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i data-lucide="users" style="width:32px;height:32px;color:#22c55e;"></i>
                </div>
                <h3>Aún no hay jugadores en el ranking</h3>
                <p>Se necesitan al menos 3 partidos jugados para aparecer.</p>
                <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-top:20px;">
                    <a href="{{ route('falta-uno.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:#22c55e; color:#050505; border-radius:12px; font-size:14px; font-weight:700; text-decoration:none;">
                        Sumate a un Falta Uno
                    </a>
                    <a href="{{ route('venues.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:#111; color:#a0a0a0; border:1.5px solid rgba(255,255,255,.1); border-radius:12px; font-size:14px; font-weight:600; text-decoration:none;">
                        Reservar cancha
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="ranking-table-wrap">
            <table class="ranking-table">
                <thead>
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Jugador</th>
                        <th style="text-align:center;">PJ</th>
                        <th style="text-align:center;">PG</th>
                        <th style="text-align:center;">PE</th>
                        <th style="text-align:center;">PP</th>
                        <th>Win Rate</th>
                        <th>Rating</th>
                        <th>Badges</th>
                        <th style="text-align:right;">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($players as $index => $player)
                        @php
                            $globalPos = ($page - 1) * 20 + $index + 1;
                            $rankClass = match($globalPos) {
                                1 => 'rank-1',
                                2 => 'rank-2',
                                3 => 'rank-3',
                                default => 'rank-default',
                            };
                            $categoryLabels = [
                                'recreativo'   => 'Recreativo',
                                'intermedio'   => 'Intermedio',
                                'avanzado'     => 'Avanzado',
                                'competitivo'  => 'Competitivo',
                                'primera'      => '1ra',
                                'segunda'      => '2da',
                                'tercera'      => '3ra',
                                'cuarta'       => '4ta',
                                'quinta'       => '5ta',
                                'sexta'        => '6ta',
                                'septima'      => '7ma',
                                'octava'       => '8va',
                            ];
                        @endphp
                        <tr>
                            {{-- Position --}}
                            <td class="td-rank">
                                <span class="rank-pos {{ $rankClass }}">
                                    @if($globalPos === 1)
                                        <i data-lucide="crown" style="width:20px;height:20px;"></i>
                                    @else
                                        {{ $globalPos }}
                                    @endif
                                </span>
                            </td>

                            {{-- Player info --}}
                            <td class="td-player">
                                <div class="player-info">
                                    @if($player->user->avatar_path)
                                        <img src="{{ asset('storage/' . $player->user->avatar_path) }}" alt="" class="player-avatar">
                                    @else
                                        <div class="player-avatar-placeholder">
                                            {{ strtoupper(substr($player->user->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('sport-profile.public', $player->user) }}" class="player-name">{{ $player->user->name }}</a>
                                        <div class="player-meta">
                                            {{ $sportLabels[$player->sport] ?? $player->sport }}
                                            @if($player->category)
                                                &middot; {{ $categoryLabels[$player->category] ?? ucfirst($player->category) }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Stats --}}
                            <td class="td-stats stat-cell">{{ $player->games_played }}</td>
                            <td class="td-stats stat-cell stat-win">{{ $player->wins }}</td>
                            <td class="td-stats stat-cell stat-draw">{{ $player->draws }}</td>
                            <td class="td-stats stat-cell stat-loss">{{ $player->losses }}</td>

                            {{-- Win Rate --}}
                            <td class="td-winrate">
                                <div class="win-rate-bar">
                                    <div class="win-rate-track">
                                        <div class="win-rate-fill" style="width:{{ $player->win_rate }}%;"></div>
                                    </div>
                                    <span style="font-weight:600; font-size:0.85rem; color:#e8e8e8;">{{ $player->win_rate }}%</span>
                                </div>
                            </td>

                            {{-- Rating --}}
                            <td class="td-rating">
                                @if($player->rating > 0)
                                    <div class="rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($player->rating))
                                                <i data-lucide="star" style="width:14px;height:14px;fill:#fbbf24;stroke:#fbbf24;"></i>
                                            @else
                                                <i data-lucide="star" style="width:14px;height:14px;stroke:#d1d5db;"></i>
                                            @endif
                                        @endfor
                                        <span class="rating-value">{{ number_format($player->rating, 1) }}</span>
                                    </div>
                                @else
                                    <span style="color:#d1d5db; font-size:0.85rem;">-</span>
                                @endif
                            </td>

                            {{-- Badges --}}
                            <td class="td-badges">
                                @if(!empty($player->badges))
                                    <div style="display:flex; flex-wrap:wrap; gap:4px;">
                                        @foreach(array_slice($player->badges, 0, 3) as $badge)
                                            <span class="badge-pill" style="color:{{ $badge['color'] }}; background:{{ $badge['bg'] }}; border-color:{{ $badge['border'] }};" title="{{ $badge['description'] }}">
                                                <i data-lucide="{{ $badge['icon'] }}" style="width:12px;height:12px;"></i>
                                                {{ $badge['name'] }}
                                            </span>
                                        @endforeach
                                        @if(count($player->badges) > 3)
                                            <span style="font-size:0.7rem; color:#9ca3af; padding:3px 6px;">+{{ count($player->badges) - 3 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="color:#d1d5db; font-size:0.8rem;">-</span>
                                @endif
                            </td>

                            {{-- Score --}}
                            <td class="td-score" style="text-align:right;">
                                <span class="ranking-score">{{ $player->score }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($players->hasPages())
            <div style="margin-top:24px; display:flex; justify-content:center;">
                {{ $players->links() }}
            </div>
        @endif
    @endif

</div>
@endsection
