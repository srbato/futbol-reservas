@extends('layouts.app')

@section('title', 'Perfil de ' . $user->name)

@push('head')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
@endpush

@push('styles')
<style>
  /* ── Scroll progress ───────────────────────────── */
  .fpp-scroll-progress {
    position: fixed;
    top: 0; left: 0;
    width: 0%;
    height: 3px;
    background: #22c55e;
    z-index: 9999;
    transition: width .1s linear;
  }

  /* ── Hero ──────────────────────────────────────── */
  .fpp-hero {
    position: relative;
    height: 280px;
    border-radius: 28px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 28px;
  }
  .fpp-hero-bg {
    position: absolute; inset: 0;
    background-image: url('/images/jugadores-dandose-la-mano-post-partido.webp');
    background-size: cover;
    background-position: center;
  }
  .fpp-hero-overlay {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.62);
  }
  .fpp-hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 24px;
  }

  /* Avatar hero */
  .fpp-hero-avatar {
    width: 88px; height: 88px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,.3);
    box-shadow: 0 0 0 4px rgba(34,197,94,.25);
    margin: 0 auto 14px;
    display: block;
  }
  .fpp-hero-avatar-initial {
    width: 88px; height: 88px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    border: 3px solid rgba(255,255,255,.3);
    box-shadow: 0 0 0 4px rgba(34,197,94,.25);
    margin: 0 auto 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 800;
    color: #fff;
  }
  .fpp-hero-name {
    margin: 0 0 6px;
    font-size: 28px;
    font-weight: 900;
    color: #fff;
    letter-spacing: -.02em;
  }
  .fpp-hero-sub {
    margin: 0;
    font-size: 14px;
    color: rgba(255,255,255,.65);
    font-weight: 600;
  }

  /* ── Sección títulos ───────────────────────────── */
  .fpp-section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }
  .fpp-section-label {
    font-size: 12px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #aaa;
    flex-shrink: 0;
  }
  .fpp-section-line {
    flex: 1;
    height: 1px;
    background: #ebebeb;
  }

  /* ── Cards de deporte ──────────────────────────── */
  .fpp-sports-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
    margin-bottom: 32px;
  }
  .fpp-sport-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 18px;
    padding: 20px;
    transition: transform .2s, box-shadow .2s;
  }
  .fpp-sport-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,.07);
  }
  .fpp-sport-emoji { font-size: 32px; margin-bottom: 8px; display: block; }
  .fpp-sport-name  { font-size: 18px; font-weight: 800; color: #111; margin-bottom: 8px; }
  .fpp-cat-badge   {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: capitalize;
    margin-bottom: 14px;
  }

  /* Mini tabla stats */
  .fpp-stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 5px;
    margin-bottom: 12px;
  }
  .fpp-stat-cell {
    text-align: center;
    border-radius: 8px;
    padding: 7px 4px;
  }
  .fpp-stat-num   { font-size: 20px; font-weight: 900; line-height: 1; }
  .fpp-stat-label { font-size: 10px; color: #888; font-weight: 700; text-transform: uppercase; margin-top: 3px; }

  /* Estrellas */
  .fpp-stars { font-size: 14px; color: #f59e0b; }
  .fpp-rating-num { font-size: 12px; color: #888; margin-left: 4px; }

  /* ── Últimos partidos ──────────────────────────── */
  .fpp-matches-wrap {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 32px;
  }
  .fpp-match-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f4f4f4;
    transition: background .12s;
  }
  .fpp-match-row:last-child { border-bottom: none; }
  .fpp-match-row:hover { background: #fafafa; }
  .fpp-result-pill {
    padding: 3px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    min-width: 70px;
    text-align: center;
    flex-shrink: 0;
  }
  .fpp-result-win  { background: #f0fdf4; color: #15803d; }
  .fpp-result-draw { background: #fffbeb; color: #b45309; }
  .fpp-result-loss { background: #fef2f2; color: #dc2626; }
  .fpp-result-none { background: #f4f4f4; color: #888; }
  .fpp-match-name  { font-size: 15px; font-weight: 700; color: #111; }
  .fpp-match-meta  { font-size: 12px; color: #888; margin-top: 2px; }
  .fpp-match-goals { font-size: 12px; color: #555; flex-shrink: 0; }

  /* ── ApexCharts card ───────────────────────────── */
  .fpp-chart-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
  }

  /* ── Reservas convencionales ───────────────────── */
  .fpp-conv-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 28px;
  }
  .fpp-conv-stats {
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    margin-bottom: 18px;
  }
  .fpp-conv-tile {
    text-align: center;
    border-radius: 12px;
    padding: 14px 20px;
    flex: 1;
    min-width: 70px;
  }
  .fpp-conv-num   { font-size: 24px; font-weight: 800; line-height: 1; }
  .fpp-conv-label { font-size: 10px; color: #888; font-weight: 700; text-transform: uppercase; margin-top: 4px; }

  .fpp-conv-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-top: 1px solid #f4f4f4;
  }

  /* ── Mobile ────────────────────────────────────── */
  @media (max-width: 600px) {
    .fpp-hero { height: 220px; }
    .fpp-hero-name { font-size: 22px; }
    .fpp-sports-grid { grid-template-columns: 1fr; }
    .fpp-conv-stats { gap: 8px; }
  }
</style>
@endpush

@section('content')

{{-- Scroll progress bar --}}
<div class="fpp-scroll-progress" id="fppScrollProgress"></div>

<div style="max-width: 720px; margin: 0 auto;">

  {{-- Hero --}}
  <div class="fpp-hero">
    <div class="fpp-hero-bg"></div>
    <div class="fpp-hero-overlay"></div>
    <div class="fpp-hero-content" data-aos="fade-up">
      @if($user->avatar_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}"
             class="fpp-hero-avatar" alt="{{ $user->name }}">
      @else
        <div class="fpp-hero-avatar-initial">
          {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
        </div>
      @endif
      <h1 class="fpp-hero-name">{{ $user->name }}</h1>
      <p class="fpp-hero-sub">Perfil deportivo</p>
    </div>
  </div>

  {{-- Perfiles por deporte --}}
  @if($profiles->isEmpty())
    <div class="page-card" style="text-align:center; padding:48px 24px; margin-bottom:28px;" data-aos="zoom-in">
      <div style="margin-bottom:12px;"><i data-lucide="target" style="width:40px;height:40px;stroke:#ccc;stroke-width:1.5;"></i></div>
      <p style="font-size:16px; font-weight:700; margin:0 0 6px;">Sin perfiles deportivos</p>
      <p style="color:#888; font-size:13px; margin:0;">Este jugador aún no completó su perfil deportivo.</p>
    </div>
  @else
    <div class="fpp-section-header">
      <span class="fpp-section-label">Deportes</span>
      <div class="fpp-section-line"></div>
    </div>
    <div class="fpp-sports-grid">
      @foreach($profiles as $pi => $profile)
      @php
        $sportParts = match($profile->sport) {
          'football'   => ['activity', 'Fútbol'],
          'padel'      => ['activity', 'Pádel'],
          'tennis'     => ['activity', 'Tenis'],
          'basketball' => ['activity', 'Básquet'],
          'volleyball' => ['activity', 'Vóley'],
          default      => ['activity', ucfirst($profile->sport)],
        };
        $catColors = match($profile->category) {
          'recreativo', 'octava',  'septima' => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
          'intermedio', 'sexta',   'quinta'  => ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
          'avanzado',  'cuarta',  'tercera'  => ['bg' => '#fef3c7', 'color' => '#d97706'],
          'competitivo','segunda', 'primera' => ['bg' => '#fce7f3', 'color' => '#db2777'],
          default                            => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
        };
        $stars = round($profile->average_rating);
      @endphp
      <div class="fpp-sport-card"
           data-aos="fade-up" data-aos-delay="{{ min($pi * 80, 400) }}">
        <span class="fpp-sport-emoji"><i data-lucide="{{ $sportParts[0] }}" style="width:24px;height:24px;stroke:#22c55e;stroke-width:2;"></i></span>
        <div class="fpp-sport-name">{{ $sportParts[1] }}</div>
        <span class="fpp-cat-badge"
              style="background:{{ $catColors['bg'] }}; color:{{ $catColors['color'] }};">
          {{ $profile->category }}
        </span>
        <div class="fpp-stats-grid">
          <div class="fpp-stat-cell" style="background:#f8f8f8;">
            <div class="fpp-stat-num">{{ $profile->games_played }}</div>
            <div class="fpp-stat-label">PJ</div>
          </div>
          <div class="fpp-stat-cell" style="background:#f0fdf4;">
            <div class="fpp-stat-num" style="color:#22c55e;">{{ $profile->wins }}</div>
            <div class="fpp-stat-label">PG</div>
          </div>
          <div class="fpp-stat-cell" style="background:#fffbeb;">
            <div class="fpp-stat-num" style="color:#f59e0b;">{{ $profile->draws }}</div>
            <div class="fpp-stat-label">PE</div>
          </div>
          <div class="fpp-stat-cell" style="background:#fef2f2;">
            <div class="fpp-stat-num" style="color:#ef4444;">{{ $profile->losses }}</div>
            <div class="fpp-stat-label">PP</div>
          </div>
        </div>
        <div class="fpp-stars">
          @for($i=1;$i<=5;$i++){{ $i<=$stars ? '★' : '☆' }}@endfor
          <span class="fpp-rating-num">{{ number_format($profile->average_rating,1) }}</span>
        </div>
        @if($profile->attendance_rate < 100)
          @php
            $attColor = $profile->attendance_rate >= 90 ? '#15803d' : ($profile->attendance_rate >= 70 ? '#b45309' : '#dc2626');
            $attBg    = $profile->attendance_rate >= 90 ? '#f0fdf4' : ($profile->attendance_rate >= 70 ? '#fffbeb' : '#fef2f2');
          @endphp
          <div style="margin-top:8px; display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:700; padding:3px 10px; border-radius:999px; background:{{ $attBg }}; color:{{ $attColor }};">
            Asistencia: {{ number_format($profile->attendance_rate, 0) }}%
          </div>
        @endif
      </div>
      @endforeach
    </div>
  @endif

  {{-- Últimos partidos --}}
  @if($recentParticipations->isNotEmpty())
    <div class="fpp-section-header">
      <span class="fpp-section-label">Últimos partidos</span>
      <div class="fpp-section-line"></div>
    </div>
    <div class="fpp-matches-wrap">
      @foreach($recentParticipations as $mi => $p)
      @php
        $resultCfg = match($p->result) {
          'win'  => ['Victoria', 'fpp-result-win'],
          'draw' => ['Empate',   'fpp-result-draw'],
          'loss' => ['Derrota',  'fpp-result-loss'],
          default=> ['-',        'fpp-result-none'],
        };
      @endphp
      <div class="fpp-match-row"
           data-aos="fade-right" data-aos-delay="{{ min($mi * 60, 360) }}">
        <span class="fpp-result-pill {{ $resultCfg[1] }}">{{ $resultCfg[0] }}</span>
        <div style="flex:1; min-width:0;">
          <div class="fpp-match-name">{{ $p->game->field->name ?? '—' }}</div>
          <div class="fpp-match-meta">
            {{ $p->game->field->venue->name ?? '' }} · {{ \Carbon\Carbon::parse($p->game->start_at)->format('d/m/Y') }}
          </div>
        </div>
        @if($p->goals !== null)
          <div class="fpp-match-goals">{{ $p->goals }} gol{{ $p->goals !== 1 ? 'es' : '' }}</div>
        @endif
      </div>
      @endforeach
    </div>

    {{-- Gráfico de rendimiento --}}
    <div class="fpp-section-header">
      <span class="fpp-section-label">Rendimiento reciente</span>
      <div class="fpp-section-line"></div>
    </div>
    <div class="fpp-chart-card" data-aos="fade-up">
      <div id="chart"></div>
    </div>
  @endif

  {{-- Historial de reservas convencionales --}}
  @if($conventionalStats['total'] > 0)
    <div class="fpp-section-header">
      <span class="fpp-section-label">Reservas convencionales</span>
      <div class="fpp-section-line"></div>
    </div>
    <div class="fpp-conv-card" data-aos="fade-up">
      <div class="fpp-conv-stats">
        <div class="fpp-conv-tile" style="background:#f8f8f8;">
          <div class="fpp-conv-num" style="color:#111;">{{ $conventionalStats['total'] }}</div>
          <div class="fpp-conv-label">Jugados</div>
        </div>
        <div class="fpp-conv-tile" style="background:#f0fdf4;">
          <div class="fpp-conv-num" style="color:#22c55e;">{{ $conventionalStats['wins'] }}</div>
          <div class="fpp-conv-label">Victorias</div>
        </div>
        <div class="fpp-conv-tile" style="background:#fffbeb;">
          <div class="fpp-conv-num" style="color:#f59e0b;">{{ $conventionalStats['draws'] }}</div>
          <div class="fpp-conv-label">Empates</div>
        </div>
        <div class="fpp-conv-tile" style="background:#fef2f2;">
          <div class="fpp-conv-num" style="color:#ef4444;">{{ $conventionalStats['losses'] }}</div>
          <div class="fpp-conv-label">Derrotas</div>
        </div>
      </div>
      @foreach($conventionalHistory as $ci => $r)
      @php
        $rCfg = match($r->outcome) {
          'W'    => ['Victoria', 'fpp-result-win'],
          'D'    => ['Empate',   'fpp-result-draw'],
          'L'    => ['Derrota',  'fpp-result-loss'],
          default=> ['-',        'fpp-result-none'],
        };
      @endphp
      <div class="fpp-conv-row" data-aos="fade-up" data-aos-delay="{{ min($ci * 50, 300) }}">
        <span class="fpp-result-pill {{ $rCfg[1] }}">{{ $rCfg[0] }}</span>
        <div style="flex:1; min-width:0;">
          <div style="font-size:13px; font-weight:700; color:#111;">{{ $r->field->name ?? '—' }}@if($r->score) <span style="font-weight:400; color:#888; font-size:12px;">{{ $r->score }}</span>@endif</div>
          <div style="font-size:11px; color:#888;">{{ $r->venue->name ?? '' }} · {{ $r->date?->format('d/m/Y') }}</div>
        </div>
      </div>
      @endforeach
    </div>
  @endif

</div>

@if($recentParticipations->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
  const results = {!! json_encode($chartData) !!};

  const options = {
    chart:  { type: 'line', height: 180, toolbar: { show: false }, sparkline: { enabled: false } },
    series: [{ name: 'Puntos', data: results.map(r => r.result) }],
    xaxis:  { categories: results.map(r => r.date), labels: { style: { fontSize: '11px' } } },
    yaxis:  {
      min: 0, max: 3,
      tickAmount: 3,
      labels: { formatter: v => v === 3 ? 'Victoria' : v === 1 ? 'Empate' : v === 0 ? 'Derrota' : '', style: { fontSize: '10px' } }
    },
    stroke: { curve: 'smooth', width: 3 },
    markers: { size: 5, colors: ['#22c55e'] },
    colors: ['#111827'],
    grid:   { borderColor: '#f3f4f6' },
    theme:  { mode: 'light' },
    tooltip: { y: { formatter: v => v === 3 ? 'Victoria' : v === 1 ? 'Empate' : 'Derrota' } },
  };

  new ApexCharts(document.getElementById('chart'), options).render();
</script>
@endif

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ once: true, duration: 500, easing: 'ease-out' });

  window.addEventListener('scroll', () => {
    const el  = document.getElementById('fppScrollProgress');
    if (!el) return;
    const max = document.body.scrollHeight - window.innerHeight;
    el.style.width = (max > 0 ? (window.scrollY / max) * 100 : 0) + '%';
  });
</script>
@endpush

@endsection
