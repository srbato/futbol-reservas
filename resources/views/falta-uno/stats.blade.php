@extends('layouts.app')

@section('title', 'Mis Stats · ' . $game->field->name)

@push('styles')
<style>
  .stats-wrap { max-width: 480px; margin: 0 auto; }
  .stats-header {
    background: linear-gradient(135deg,#111 0%,#2d2d2d 100%);
    border-radius: 18px;
    padding: 22px 26px;
    color: #fff;
    margin-bottom: 20px;
  }
  .stats-header h1 { margin:0 0 4px; font-size:20px; font-weight:800; letter-spacing:-.02em; }
  .stats-header p  { margin:0; font-size:13px; color:rgba(255,255,255,.6); }

  .stats-card {
    background:#fff;
    border:1px solid #ececec;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 2px 8px rgba(0,0,0,.03);
  }
  .stats-body { padding:22px 24px; display:grid; gap:20px; }
  .stats-footer { padding:16px 24px; border-top:1px solid #f4f4f4; }

  .stat-field { display:grid; gap:6px; }
  .stat-label { font-size:13px; font-weight:700; color:#333; }
  .stat-input {
    padding:10px 14px;
    border:1.5px solid #e0e0e0;
    border-radius:10px;
    font-size:15px;
    font-weight:700;
    outline:none;
    transition:border-color .15s;
    width:100%;
    box-sizing:border-box;
    text-align:center;
  }
  .stat-input:focus { border-color:#111; }

  .result-group { display:flex; gap:10px; }
  .result-btn {
    flex:1;
    padding:12px;
    border:2px solid #e0e0e0;
    border-radius:12px;
    font-size:14px;
    font-weight:700;
    cursor:pointer;
    text-align:center;
    background:#fff;
    color:#666;
    transition:all .15s;
  }
  .result-btn input { display:none; }
  .result-btn.win:has(input:checked)  { border-color:#22c55e; background:#f0fdf4; color:#15803d; }
  .result-btn.draw:has(input:checked) { border-color:#f59e0b; background:#fffbeb; color:#b45309; }
  .result-btn.loss:has(input:checked) { border-color:#ef4444; background:#fef2f2; color:#dc2626; }

  .stats-submit {
    width:100%;
    padding:13px;
    background:#111;
    color:#fff;
    border:none;
    border-radius:12px;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
    transition:background .15s;
  }
  .stats-submit:hover { background:#222; }
</style>
@endpush

@section('content')

<div class="stats-wrap">

  <div style="margin-bottom:14px;">
    <a href="{{ route('falta-uno.index') }}"
       style="display:inline-flex; align-items:center; gap:5px; font-size:13px; color:#888; text-decoration:none; font-weight:600;"
       onmouseover="this.style.color='#111'" onmouseout="this.style.color='#888'">
      <i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;"></i> Volver a partidos
    </a>
  </div>

  <div class="stats-header">
    <h1>¿Cómo te fue?</h1>
    <p>{{ $game->field->name }} · {{ $game->field->venue->name }} · {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y H:i') }} hs</p>
  </div>

  @if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; padding:12px 16px; margin-bottom:16px; color:#15803d; font-weight:600; font-size:14px;">
      {{ session('success') }}
    </div>
  @endif

  @if(session('info'))
    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:12px 16px; margin-bottom:16px; color:#1e40af; font-size:14px; font-weight:600;">
      {{ session('info') }}
    </div>
  @endif

  @if($errors->any())
    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#dc2626;">
      @foreach($errors->all() as $err)
        <div>{{ $err }}</div>
      @endforeach
    </div>
  @endif

  @php
    $sport = $game->field->sport;
    $statsFields = match($sport) {
      'football'   => [['name'=>'goals',   'label'=>'Goles'],
                       ['name'=>'assists',  'label'=>'Asistencias']],
      'basketball' => [['name'=>'goals',   'label'=>'Puntos anotados'],
                       ['name'=>'assists',  'label'=>'Asistencias']],
      'volleyball' => [['name'=>'goals',   'label'=>'Puntos anotados'],
                       ['name'=>'assists',  'label'=>'Aces']],
      'padel',
      'tennis'     => [],   // solo resultado
      default      => [['name'=>'goals',   'label'=>'Puntos anotados']],
    };
    $hasDrawOption = !in_array($sport, ['padel', 'tennis']);
  @endphp

  <form method="POST" action="{{ route('falta-uno.stats.store', $game) }}">
    @csrf
    <div class="stats-card">
      <div class="stats-body">

        {{-- Campos numéricos según deporte --}}
        @foreach($statsFields as $field)
        <div class="stat-field">
          <label class="stat-label" for="{{ $field['name'] }}">{{ $field['label'] }}</label>
          <input type="number" id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="stat-input"
                 min="0" value="{{ old($field['name'], 0) }}" placeholder="0">
        </div>
        @endforeach

        @if(empty($statsFields))
        <p style="font-size:13px; color:#aaa; margin:0; text-align:center;">
          Para {{ $sport === 'padel' ? 'pádel' : 'tenis' }} solo registramos el resultado del partido.
        </p>
        @endif

        {{-- Resultado --}}
        <div class="stat-field">
          <label class="stat-label">Resultado</label>
          <div class="result-group">
            <label class="result-btn win">
              <input type="radio" name="result" value="win" {{ old('result') === 'win' ? 'checked' : '' }} required>
              Victoria
            </label>
            @if($hasDrawOption)
            <label class="result-btn draw">
              <input type="radio" name="result" value="draw" {{ old('result') === 'draw' ? 'checked' : '' }}>
              Empate
            </label>
            @endif
            <label class="result-btn loss">
              <input type="radio" name="result" value="loss" {{ old('result') === 'loss' ? 'checked' : '' }}>
              Derrota
            </label>
          </div>
        </div>

      </div>
      <div class="stats-footer">
        <button type="submit" class="stats-submit" style="display:inline-flex;align-items:center;gap:6px;">Guardar estadísticas <i data-lucide="arrow-right" style="width:16px;height:16px;stroke:currentColor;"></i></button>
      </div>
    </div>
  </form>

</div>

@endsection
