@php
  use App\Support\ReservationStatus;
  use App\Http\Controllers\MatchHistoryController;
@endphp

@extends('layouts.app')

@section('title', 'Historial de partidos')

@push('styles')
<style>
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(18px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .stat-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr));
    gap: 14px;
    margin-bottom: 22px;
  }

  .stat-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 18px;
    padding: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.15);
    animation: fadeInUp .45s ease both;
  }

  .stat-card:nth-child(1) { animation-delay: .05s; }
  .stat-card:nth-child(2) { animation-delay: .12s; }
  .stat-card:nth-child(3) { animation-delay: .19s; }
  .stat-card:nth-child(4) { animation-delay: .26s; }

  .stat-card-label {
    font-size: 11px;
    color: #666;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 8px;
  }

  .stat-card-value {
    font-size: 36px;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.05;
  }

  .stat-card-sub {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
  }

  .charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 22px;
    animation: fadeInUp .45s ease .3s both;
  }

  @media (max-width: 700px) {
    .charts-grid { grid-template-columns: 1fr; }
  }

  .sport-bar-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
  }

  .sport-bar-label {
    min-width: 80px;
    font-size: 13px;
    font-weight: 600;
  }

  .sport-bar-track {
    flex: 1;
    height: 8px;
    background: rgba(255,255,255,.06);
    border-radius: 999px;
    overflow: hidden;
  }

  .sport-bar-fill {
    height: 100%;
    background: #22c55e;
    border-radius: 999px;
    transform-origin: left;
    animation: barGrow .6s ease .4s both;
  }

  @keyframes barGrow {
    from { transform: scaleX(0); }
    to   { transform: scaleX(1); }
  }

  .sport-bar-count {
    font-size: 13px;
    font-weight: 700;
    min-width: 26px;
    text-align: right;
    color: #a0a0a0;
  }

  .history-table-wrap {
    animation: fadeInUp .45s ease .45s both;
  }

  .history-table {
    width: 100%;
    border-collapse: collapse;
  }

  .history-table th {
    text-align: left;
    font-size: 11px;
    color: #666;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 8px 12px;
    border-bottom: 1px solid rgba(255,255,255,.08);
  }

  .history-table td {
    padding: 14px 12px;
    border-bottom: 1px solid rgba(255,255,255,.06);
    font-size: 14px;
    vertical-align: middle;
  }

  .history-table tr:last-child td { border-bottom: 0; }
  .history-table tr:hover td { background: rgba(255,255,255,.02); }

  .outcome-select {
    padding: 6px 10px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    background: #0a0a0a;
    cursor: pointer;
  }

  .score-input {
    padding: 6px 10px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 8px;
    font-size: 13px;
    width: 75px;
    font-family: inherit;
  }

  .result-save-btn {
    padding: 6px 12px;
    border: 1px solid rgba(255,255,255,.1);
    background: #0a0a0a;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    white-space: nowrap;
  }

  .result-save-btn:hover { background: rgba(255,255,255,.06); }

  .outcome-badge-W { padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; background:rgba(34,197,94,.1); color:#6ee7a0; }
  .outcome-badge-D { padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; background:rgba(245,158,11,.08); color:#fbbf24; }
  .outcome-badge-L { padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; background:rgba(229,57,53,.1); color:#f87171; }

  .pie-sport-filter {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 16px;
  }

  .pie-sport-btn {
    padding: 5px 13px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,.1);
    background: #0a0a0a;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: all .15s ease;
  }

  .pie-sport-btn:hover { border-color: #22c55e; }
  .pie-sport-btn.active { background: #22c55e; color: #050505; border-color: #22c55e; }

  .pie-no-data {
    text-align: center;
    color: #444;
    font-size: 14px;
    padding: 40px 0;
  }

  /* help banner */
  .help-banner {
    background: rgba(59,130,246,.08);
    border: 1px solid rgba(59,130,246,.15);
    border-radius: 16px;
    padding: 16px 18px;
    margin-bottom: 22px;
    display: flex;
    gap: 14px;
    align-items: flex-start;
    animation: fadeInUp .45s ease .02s both;
  }

  .help-banner-icon {
    font-size: 22px;
    line-height: 1;
    flex-shrink: 0;
    margin-top: 2px;
  }

  .help-banner-text {
    flex: 1;
    font-size: 14px;
    color: #93c5fd;
    line-height: 1.6;
  }


  @media (max-width: 700px) {
    .history-table thead { display: none; }
    .history-table tr {
      display: block;
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 14px;
      margin-bottom: 12px;
      padding: 4px 0;
    }
    .history-table td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 8px;
      border-bottom: 1px solid rgba(255,255,255,.06);
      padding: 10px 14px;
    }
    .history-table td:last-child { border-bottom: 0; }
    .history-table td::before {
      content: attr(data-label);
      font-size: 11px;
      color: #666;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      flex-shrink: 0;
    }
  }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-card" style="margin-bottom:22px; animation:fadeInUp .4s ease both;">
  <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap;">
    <div>
      <h1 style="margin:0 0 8px 0; font-size:34px; letter-spacing:-0.02em;">Historial de partidos</h1>
      <p class="muted" style="margin:0;">Tus partidos, estadísticas personales y actividad deportiva.</p>
    </div>
    <a href="{{ route('my_reservations') }}" class="btn">Mis reservas</a>
  </div>
</div>

{{-- Help banner --}}
<div class="help-banner" id="helpBanner">
  <div class="help-banner-icon">💡</div>
  <div class="help-banner-text">
    <strong>¿Cómo funciona?</strong> Acá aparecen todos los partidos que jugaste (reservas pagas o validadas en el complejo),
    incluyendo los partidos donde otro usuario te agregó como jugador.
    Desde el <strong>detalle de cada reserva</strong> podés agregar a los compañeros que jugaron con vos para que ellos también vean el partido acá.
    Una vez que el partido pasó, podés cargar el resultado (ganamos / empatamos / perdimos) y el gráfico de torta se va actualizando.
  </div>
</div>

{{-- Summary cards --}}
<div class="stat-cards">
  <div class="stat-card">
    <div class="stat-card-label">Partidos jugados</div>
    <div class="stat-card-value">{{ $totalGames }}</div>
    <div class="stat-card-sub">pagados o validados en cancha</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-label">Deporte favorito</div>
    <div class="stat-card-value" style="font-size:22px; padding-top:4px;">
      {{ $favoriteSport ? MatchHistoryController::sportLabel($favoriteSport) : '—' }}
    </div>
    <div class="stat-card-sub">el que más jugaste</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-label">Complejo favorito</div>
    <div class="stat-card-value" style="font-size:18px; padding-top:6px; line-height:1.25;">
      {{ $favoriteVenue ?? '—' }}
    </div>
    <div class="stat-card-sub">donde más reservaste</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-label">Total gastado</div>
    <div class="stat-card-value">${{ number_format($totalSpent, 0, ',', '.') }}</div>
    <div class="stat-card-sub">en reservas propias · {{ $currency }}</div>
  </div>
</div>

{{-- Charts --}}
@if($totalGames > 0)
<div class="charts-grid">

  {{-- Pie chart: W/D/L --}}
  <div class="page-card">
    <h2 style="margin:0 0 4px 0; font-size:18px; font-weight:800;">Resultados</h2>
    <p class="muted" style="margin:0 0 14px 0; font-size:13px;">Seleccioná un deporte o mirá todos juntos.</p>

    <div class="pie-sport-filter">
      <button class="pie-sport-btn active" onclick="filterPie('_all', this)">Todos</button>
      @foreach($sportOptions as $key => $label)
        <button class="pie-sport-btn" onclick="filterPie('{{ $key }}', this)">{{ $label }}</button>
      @endforeach
    </div>

    <div style="position:relative; width:200px; height:200px; margin:0 auto 16px auto;">
      <canvas id="pieChart" width="200" height="200"></canvas>
    </div>

    <div id="pieLegend" style="display:flex; justify-content:center; gap:16px; flex-wrap:wrap; font-size:13px; font-weight:600;"></div>
    <div id="pieNoData" class="pie-no-data" style="display:none;">Sin resultados cargados aún.<br><span style="font-size:12px;">Cargalos desde la lista de abajo.</span></div>
  </div>

  {{-- Sports bars --}}
  <div class="page-card">
    <h2 style="margin:0 0 4px 0; font-size:18px; font-weight:800;">Por deporte</h2>
    <p class="muted" style="margin:0 0 18px 0; font-size:13px;">Partidos jugados por disciplina.</p>

    @php $maxSport = $statsBySport->max() ?: 1; @endphp
    @foreach($statsBySport as $sport => $count)
      <div class="sport-bar-wrap">
        <div class="sport-bar-label">{{ MatchHistoryController::sportLabel($sport) }}</div>
        <div class="sport-bar-track">
          <div class="sport-bar-fill" style="width:{{ round($count / $maxSport * 100) }}%"></div>
        </div>
        <div class="sport-bar-count">{{ $count }}</div>
      </div>
    @endforeach
  </div>

</div>
@endif

{{-- Match list --}}
<div class="history-table-wrap">
  <div class="page-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:12px;">
      <div>
        <h2 style="margin:0 0 2px 0; font-size:22px; font-weight:800;">Lista de partidos</h2>
        <p class="muted" style="margin:0; font-size:13px;">{{ $reservations->total() }} {{ $reservations->total() === 1 ? 'partido' : 'partidos' }} en total</p>
      </div>
    </div>

    @if($reservations->isEmpty())
      <div style="text-align:center; padding:56px 24px;">
        <div style="width:72px; height:72px; margin:0 auto 18px; background:linear-gradient(135deg, #f0fdf4, #dcfce7); border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <p style="font-weight:800; margin:0 0 8px; font-size:18px; letter-spacing:-0.01em;">Todavía no tenés partidos jugados</p>
        <p class="muted" style="margin:0 0 6px; font-size:14px; max-width:400px; margin-left:auto; margin-right:auto; line-height:1.6;">
          Acá van a aparecer todos tus partidos pagados o validados en cancha con fecha pasada. Vas a poder cargar resultados y ver tus estadísticas.
        </p>
        <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-top:20px;">
          <a href="{{ route('venues.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:#22c55e; color:#050505; border-radius:12px; font-size:14px; font-weight:700; text-decoration:none;">
            Reservar cancha
          </a>
          <a href="{{ route('my_reservations') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:#111; color:#a0a0a0; border:1.5px solid rgba(255,255,255,.1); border-radius:12px; font-size:14px; font-weight:600; text-decoration:none;">
            Mis reservas
          </a>
        </div>
      </div>
    @else
      <div style="overflow-x:auto;">
        <table class="history-table">
          <thead>
            <tr>
              <th>Fecha</th>
              <th>Cancha / Complejo</th>
              <th>Deporte</th>
              <th>Duración</th>
              <th>Monto</th>
              <th>Estado</th>
              <th>Resultado</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reservations as $r)
              @php
                $minutes  = $r->start_at->diffInMinutes($r->end_at);
                $duration = $minutes >= 60
                  ? floor($minutes / 60) . 'h' . ($minutes % 60 ? ' ' . ($minutes % 60) . 'min' : '')
                  : $minutes . ' min';
                $isOwner  = $r->user_id === auth()->id();
                $isPlayer = $r->players->contains('user_id', auth()->id());
                $isPast   = $r->start_at->isPast();
                $canEdit  = $isPast && ($isOwner || $isPlayer);
              @endphp
              <tr>
                <td data-label="Fecha">
                  <div style="font-weight:700;">{{ $r->start_at->format('d/m/Y') }}</div>
                  <div class="muted" style="font-size:12px;">{{ $r->start_at->format('H:i') }} – {{ $r->end_at->format('H:i') }}</div>
                  @if(!$isPast)
                    <span style="display:inline-block;margin-top:4px;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:700;background:#e0f0ff;color:#0a58ca;">Próximo</span>
                  @endif
                </td>

                <td data-label="Cancha">
                  <div style="font-weight:600;">{{ $r->field->name }}</div>
                  <div class="muted" style="font-size:12px;">{{ $r->field->venue->name }}</div>
                  @if(!$isOwner)
                    <div style="font-size:11px; color:#999; margin-top:2px;">Reservado por {{ $r->user->name ?? '—' }}</div>
                  @endif
                </td>

                <td data-label="Deporte">{{ MatchHistoryController::sportLabel($r->field->sport) }}</td>

                <td data-label="Duración">{{ $duration }}</td>

                <td data-label="Monto">
                  @if($isOwner)
                    {{ $r->currency }} {{ number_format($r->total_amount, 0, ',', '.') }}
                  @else
                    <span class="muted" style="font-size:12px;">—</span>
                  @endif
                </td>

                <td data-label="Estado">
                  <span style="padding:5px 10px; border-radius:999px; font-weight:700; font-size:12px; {{ ReservationStatus::color($r->status) }}">
                    {{ ReservationStatus::label($r->status) }}
                  </span>
                </td>

                <td data-label="Resultado">
                  @if(!$isPast)
                    <span class="muted" style="font-size:12px;">Disponible tras el partido</span>
                  @elseif($canEdit)
                    @php $myResult = $r->results->first(); @endphp
                    <form method="POST" action="{{ route('reservations.update_result', $r) }}" style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                      @csrf
                      <select name="match_outcome" class="outcome-select">
                        <option value="">— resultado —</option>
                        <option value="W" @selected($myResult?->match_outcome === 'W')>Ganamos</option>
                        <option value="D" @selected($myResult?->match_outcome === 'D')>Empatamos</option>
                        <option value="L" @selected($myResult?->match_outcome === 'L')>Perdimos</option>
                      </select>
                      <input
                        type="text"
                        name="match_result"
                        class="score-input"
                        value="{{ $myResult?->match_result }}"
                        placeholder="ej: 3-2"
                        maxlength="100"
                      >
                      <button type="submit" class="result-save-btn">Guardar</button>
                    </form>
                  @else
                    @php $myResult = $r->results->first(); @endphp
                    <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                      @if($myResult?->match_outcome)
                        <span class="outcome-badge-{{ $myResult->match_outcome }}">
                          {{ ['W' => 'Ganamos', 'D' => 'Empatamos', 'L' => 'Perdimos'][$myResult->match_outcome] }}
                        </span>
                      @endif
                      @if($myResult?->match_result)
                        <span style="font-size:13px; color:#555;">{{ $myResult->match_result }}</span>
                      @endif
                      @if(!$myResult?->match_outcome && !$myResult?->match_result)
                        <span class="muted" style="font-size:12px;">—</span>
                      @endif
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if($reservations->hasPages())
        <div style="margin-top:18px; display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
          @if($reservations->onFirstPage())
            <span class="btn" style="opacity:.4; cursor:default;">← Anterior</span>
          @else
            <a href="{{ $reservations->previousPageUrl() }}" class="btn">← Anterior</a>
          @endif

          <span class="muted" style="font-size:13px;">
            Página {{ $reservations->currentPage() }} de {{ $reservations->lastPage() }}
          </span>

          @if($reservations->hasMorePages())
            <a href="{{ $reservations->nextPageUrl() }}" class="btn">Siguiente →</a>
          @else
            <span class="btn" style="opacity:.4; cursor:default;">Siguiente →</span>
          @endif
        </div>
      @endif
    @endif
  </div>
</div>

@if($totalGames > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  const outcomesData = @json($outcomesBySport);

  const COLORS = {
    W: { bg: '#d1e7dd', border: '#0f5132', label: 'Ganamos' },
    D: { bg: '#fff3cd', border: '#856404', label: 'Empatamos' },
    L: { bg: '#f8d7da', border: '#842029', label: 'Perdimos' },
  };

  let pieChart = null;

  function buildPie(sportKey) {
    const raw    = outcomesData[sportKey] ?? { W: 0, D: 0, L: 0 };
    const total  = raw.W + raw.D + raw.L;
    const legend = document.getElementById('pieLegend');
    const noData = document.getElementById('pieNoData');
    const canvas = document.getElementById('pieChart');

    if (total === 0) {
      canvas.style.display = 'none';
      legend.style.display = 'none';
      noData.style.display = 'block';
      if (pieChart) { pieChart.destroy(); pieChart = null; }
      return;
    }

    canvas.style.display = 'block';
    legend.style.display = 'flex';
    noData.style.display = 'none';

    const keys    = ['W', 'D', 'L'].filter(k => raw[k] > 0);
    const data    = keys.map(k => raw[k]);
    const bgs     = keys.map(k => COLORS[k].bg);
    const borders = keys.map(k => COLORS[k].border);

    if (pieChart) {
      pieChart.data.labels                      = keys.map(k => COLORS[k].label);
      pieChart.data.datasets[0].data            = data;
      pieChart.data.datasets[0].backgroundColor = bgs;
      pieChart.data.datasets[0].borderColor     = borders;
      pieChart.update();
    } else {
      pieChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
          labels: keys.map(k => COLORS[k].label),
          datasets: [{
            data,
            backgroundColor: bgs,
            borderColor: borders,
            borderWidth: 2,
            hoverOffset: 10,
          }]
        },
        options: {
          responsive: false,
          animation: {
            duration: 900,
            easing: 'easeOutQuart',
          },
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: ctx => ` ${ctx.label}: ${ctx.parsed} (${Math.round(ctx.parsed / total * 100)}%)`
              }
            }
          },
          cutout: '58%',
        }
      });
    }

    legend.innerHTML = keys.map(k => `
      <span style="display:flex;align-items:center;gap:5px;">
        <span style="width:11px;height:11px;border-radius:3px;background:${COLORS[k].bg};border:1.5px solid ${COLORS[k].border};display:inline-block;flex-shrink:0;"></span>
        ${COLORS[k].label}: <strong>${raw[k]}</strong>
      </span>
    `).join('');
  }

  function filterPie(sportKey, btn) {
    document.querySelectorAll('.pie-sport-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    buildPie(sportKey);
  }

  buildPie('_all');
</script>
@endif


@endsection
