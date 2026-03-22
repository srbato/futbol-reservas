@extends('layouts.app')

@section('title', 'Perfil de ' . $user->name)

@section('content')

<div style="max-width: 720px; margin: 0 auto;">

  {{-- Perfil del jugador --}}
  <div style="background: linear-gradient(135deg,#111 0%,#2d2d2d 100%); border-radius: 20px; padding: 28px; margin-bottom: 24px; color: #fff; display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
    <div>
      @if($user->avatar_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}"
             style="width:72px; height:72px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,.2);"
             alt="{{ $user->name }}">
      @else
        <div style="width:72px; height:72px; border-radius:50%; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; color:#fff; border:3px solid rgba(255,255,255,.2);">
          {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
        </div>
      @endif
    </div>
    <div style="flex:1;">
      <h1 style="margin:0 0 4px; font-size:22px; font-weight:800; letter-spacing:-.02em;">{{ $user->name }}</h1>
      <p style="margin:0; font-size:13px; color:rgba(255,255,255,.6);">Perfil deportivo público</p>
    </div>
  </div>

  {{-- Perfiles por deporte --}}
  @if($profiles->isEmpty())
    <div class="page-card" style="text-align:center; padding:36px 24px; margin-bottom:24px;">
      <div style="font-size:36px; margin-bottom:10px;">🎯</div>
      <p style="font-size:15px; font-weight:700; margin:0 0 4px;">Sin perfiles deportivos</p>
      <p style="color:#888; font-size:13px; margin:0;">Este jugador aún no completó su perfil deportivo.</p>
    </div>
  @else
    <h2 style="font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#aaa; margin:0 0 14px;">Deportes</h2>
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:12px; margin-bottom:28px;">
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
          'recreativo', 'octava',  'septima' => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
          'intermedio', 'sexta',   'quinta'  => ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
          'avanzado',  'cuarta',  'tercera'  => ['bg' => '#fef3c7', 'color' => '#d97706'],
          'competitivo','segunda', 'primera' => ['bg' => '#fce7f3', 'color' => '#db2777'],
          default                            => ['bg' => '#f3f4f6', 'color' => '#6b7280'],
        };
        $stars = round($profile->average_rating);
      @endphp
      <div style="background:#fff; border:1px solid #ececec; border-radius:16px; padding:16px; box-shadow:0 2px 6px rgba(0,0,0,.03);">
        <div style="font-size:22px; margin-bottom:8px;">{{ explode(' ', $sportLabel)[0] }}</div>
        <div style="font-size:15px; font-weight:800; color:#111; margin-bottom:6px;">{{ ltrim(strstr($sportLabel, ' ')) }}</div>
        <span style="display:inline-block; background:{{ $catColors['bg'] }}; color:{{ $catColors['color'] }}; padding:2px 10px; border-radius:999px; font-size:11px; font-weight:700; text-transform:capitalize; margin-bottom:10px;">
          {{ $profile->category }}
        </span>
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:4px; margin-bottom:8px;">
          <div style="text-align:center; background:#f8f8f8; border-radius:8px; padding:6px 2px;">
            <div style="font-size:16px; font-weight:800;">{{ $profile->games_played }}</div>
            <div style="font-size:9px; color:#888; font-weight:700; text-transform:uppercase;">PJ</div>
          </div>
          <div style="text-align:center; background:#f0fdf4; border-radius:8px; padding:6px 2px;">
            <div style="font-size:16px; font-weight:800; color:#22c55e;">{{ $profile->wins }}</div>
            <div style="font-size:9px; color:#888; font-weight:700; text-transform:uppercase;">PG</div>
          </div>
          <div style="text-align:center; background:#fffbeb; border-radius:8px; padding:6px 2px;">
            <div style="font-size:16px; font-weight:800; color:#f59e0b;">{{ $profile->draws }}</div>
            <div style="font-size:9px; color:#888; font-weight:700; text-transform:uppercase;">PE</div>
          </div>
          <div style="text-align:center; background:#fef2f2; border-radius:8px; padding:6px 2px;">
            <div style="font-size:16px; font-weight:800; color:#ef4444;">{{ $profile->losses }}</div>
            <div style="font-size:9px; color:#888; font-weight:700; text-transform:uppercase;">PP</div>
          </div>
        </div>
        <div style="font-size:13px; color:#f59e0b;">
          @for($i=1;$i<=5;$i++){{ $i<=$stars ? '★' : '☆' }}@endfor
          <span style="font-size:11px; color:#888; margin-left:3px;">{{ number_format($profile->average_rating,1) }}</span>
        </div>
      </div>
      @endforeach
    </div>
  @endif

  {{-- Últimos partidos --}}
  @if($recentParticipations->isNotEmpty())
    <h2 style="font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#aaa; margin:0 0 14px;">Últimos partidos</h2>
    <div style="background:#fff; border:1px solid #ececec; border-radius:16px; overflow:hidden; margin-bottom:28px;">
      @foreach($recentParticipations as $p)
      @php
        $resultConfig = match($p->result) {
          'win'  => ['label'=>'Victoria', 'bg'=>'#f0fdf4', 'color'=>'#15803d'],
          'draw' => ['label'=>'Empate',   'bg'=>'#fffbeb', 'color'=>'#b45309'],
          'loss' => ['label'=>'Derrota',  'bg'=>'#fef2f2', 'color'=>'#dc2626'],
          default=> ['label'=>'-',        'bg'=>'#f4f4f4', 'color'=>'#888'],
        };
      @endphp
      <div style="display:flex; align-items:center; gap:12px; padding:12px 16px; border-bottom:1px solid #f4f4f4;">
        <span style="background:{{ $resultConfig['bg'] }}; color:{{ $resultConfig['color'] }}; padding:3px 12px; border-radius:999px; font-size:11px; font-weight:700; min-width:70px; text-align:center;">
          {{ $resultConfig['label'] }}
        </span>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:#111;">
            {{ $p->game->field->name ?? '—' }}
          </div>
          <div style="font-size:11px; color:#888;">
            {{ $p->game->field->venue->name ?? '' }} · {{ \Carbon\Carbon::parse($p->game->start_at)->format('d/m/Y') }}
          </div>
        </div>
        @if($p->goals !== null)
          <div style="font-size:12px; color:#555;">{{ $p->goals }} goles</div>
        @endif
      </div>
      @endforeach
    </div>

    {{-- Gráfico de rendimiento --}}
    <h2 style="font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#aaa; margin:0 0 14px;">Rendimiento reciente</h2>
    <div style="background:#fff; border:1px solid #ececec; border-radius:16px; padding:20px; margin-bottom:24px;">
      <div id="chart"></div>
    </div>
  @endif

  {{-- Historial de reservas convencionales --}}
  @if($conventionalStats['total'] > 0)
    <h2 style="font-size:14px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#aaa; margin:0 0 14px;">Reservas convencionales</h2>
    <div style="background:#fff; border:1px solid #ececec; border-radius:16px; padding:20px; margin-bottom:28px;">
      <div style="display:flex; gap:16px; flex-wrap:wrap; margin-bottom:16px;">
        <div style="text-align:center; background:#f8f8f8; border-radius:12px; padding:14px 20px; flex:1; min-width:70px;">
          <div style="font-size:24px; font-weight:800; color:#111;">{{ $conventionalStats['total'] }}</div>
          <div style="font-size:10px; color:#888; font-weight:700; text-transform:uppercase; margin-top:2px;">Jugados</div>
        </div>
        <div style="text-align:center; background:#f0fdf4; border-radius:12px; padding:14px 20px; flex:1; min-width:70px;">
          <div style="font-size:24px; font-weight:800; color:#22c55e;">{{ $conventionalStats['wins'] }}</div>
          <div style="font-size:10px; color:#888; font-weight:700; text-transform:uppercase; margin-top:2px;">Victorias</div>
        </div>
        <div style="text-align:center; background:#fffbeb; border-radius:12px; padding:14px 20px; flex:1; min-width:70px;">
          <div style="font-size:24px; font-weight:800; color:#f59e0b;">{{ $conventionalStats['draws'] }}</div>
          <div style="font-size:10px; color:#888; font-weight:700; text-transform:uppercase; margin-top:2px;">Empates</div>
        </div>
        <div style="text-align:center; background:#fef2f2; border-radius:12px; padding:14px 20px; flex:1; min-width:70px;">
          <div style="font-size:24px; font-weight:800; color:#ef4444;">{{ $conventionalStats['losses'] }}</div>
          <div style="font-size:10px; color:#888; font-weight:700; text-transform:uppercase; margin-top:2px;">Derrotas</div>
        </div>
      </div>
      @foreach($conventionalHistory as $r)
      @php
        $resultConfig = match($r->result) {
          'win'  => ['label'=>'Victoria', 'bg'=>'#f0fdf4', 'color'=>'#15803d'],
          'draw' => ['label'=>'Empate',   'bg'=>'#fffbeb', 'color'=>'#b45309'],
          'loss' => ['label'=>'Derrota',  'bg'=>'#fef2f2', 'color'=>'#dc2626'],
          default=> ['label'=>'-',        'bg'=>'#f4f4f4', 'color'=>'#888'],
        };
      @endphp
      <div style="display:flex; align-items:center; gap:12px; padding:10px 0; border-top:1px solid #f4f4f4;">
        <span style="background:{{ $resultConfig['bg'] }}; color:{{ $resultConfig['color'] }}; padding:3px 12px; border-radius:999px; font-size:11px; font-weight:700; min-width:70px; text-align:center;">
          {{ $resultConfig['label'] }}
        </span>
        <div style="flex:1;">
          <div style="font-size:13px; font-weight:700; color:#111;">{{ $r->field->name ?? '—' }}</div>
          <div style="font-size:11px; color:#888;">{{ $r->field->venue->name ?? '' }} · {{ $r->start_at->format('d/m/Y') }}</div>
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
    markers: { size: 5 },
    colors: ['#111'],
    grid:   { borderColor: '#f4f4f4' },
    tooltip: { y: { formatter: v => v === 3 ? 'Victoria' : v === 1 ? 'Empate' : 'Derrota' } },
  };

  new ApexCharts(document.getElementById('chart'), options).render();
</script>
@endif

@endsection
