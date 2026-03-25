@extends('layouts.app')

@section('title', 'Calificar jugadores')

@push('styles')
<style>
  .rate-wrap { max-width: 640px; margin: 0 auto; }
  .rate-header {
    background: linear-gradient(135deg,#111 0%,#2d2d2d 100%);
    border-radius: 18px;
    padding: 22px 26px;
    color: #fff;
    margin-bottom: 20px;
  }
  .rate-header h1 { margin:0 0 4px; font-size:20px; font-weight:800; letter-spacing:-.02em; }
  .rate-header p  { margin:0; font-size:13px; color:rgba(255,255,255,.6); }

  .rate-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 16px;
    padding: 18px 20px;
    margin-bottom: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,.03);
  }

  .rate-player {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
  }

  .rate-avatar {
    width:44px; height:44px; border-radius:50%;
    background:#111; display:flex; align-items:center; justify-content:center;
    font-size:16px; font-weight:800; color:#fff; overflow:hidden; flex-shrink:0;
  }
  .rate-avatar img { width:100%; height:100%; object-fit:cover; }
  .rate-name { font-size:16px; font-weight:800; color:#111; }

  .assess-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 8px;
    margin-bottom: 12px;
  }

  .assess-btn {
    padding: 10px 8px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
    text-align: center;
    transition: all .15s;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
  }
  .assess-btn .assess-icon { font-size: 22px; line-height: 1; }
  .assess-btn .assess-label { font-size: 12px; font-weight: 700; color: #555; }
  .assess-btn .assess-desc { font-size: 10px; color: #aaa; line-height: 1.3; }

  .assess-btn:hover { border-color: #9ca3af; }
  .assess-btn.selected-below  { border-color: #ef4444; background: #fef2f2; }
  .assess-btn.selected-below .assess-label  { color: #dc2626; }
  .assess-btn.selected-match  { border-color: #6b7280; background: #f3f4f6; }
  .assess-btn.selected-match .assess-label  { color: #374151; }
  .assess-btn.selected-above  { border-color: #22c55e; background: #f0fdf4; }
  .assess-btn.selected-above .assess-label  { color: #15803d; }

  @media (max-width: 480px) {
    .assess-row { grid-template-columns: 1fr; }
  }

  .rate-comment {
    width: 100%;
    padding: 8px 12px;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    font-size: 13px;
    resize: vertical;
    min-height: 56px;
    outline: none;
    transition: border-color .15s;
    box-sizing: border-box;
    font-family: inherit;
  }
  .rate-comment:focus { border-color:#111; }

  .rate-submit {
    width: 100%;
    padding: 14px;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 8px;
    transition: background .15s;
  }
  .rate-submit:hover { background: #222; }
</style>
@endpush

@section('content')

<div class="rate-wrap">

  <div style="margin-bottom:14px;">
    <a href="{{ route('falta-uno.index') }}"
       style="display:inline-flex; align-items:center; gap:5px; font-size:13px; color:#888; text-decoration:none; font-weight:600;"
       onmouseover="this.style.color='#111'" onmouseout="this.style.color='#888'">
      <i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;"></i> Volver a partidos
    </a>
  </div>

  <div class="rate-header">
    <h1>Calificá a tus compañeros</h1>
    <p>{{ $game->field->name }} · {{ $game->field->venue->name }} · {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y H:i') }} hs</p>
  </div>

  @if(session('info'))
    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:12px; padding:12px 16px; margin-bottom:16px; color:#1e40af; font-size:14px; font-weight:600;">
      {{ session('info') }}
    </div>
  @endif

  <form method="POST" action="{{ route('falta-uno.rate.store', $game) }}" id="rateForm">
    @csrf

    @foreach($otherUsers as $i => $player)
    <div class="rate-card">
      <div class="rate-player">
        <div class="rate-avatar">
          @if($player->avatar_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($player->avatar_path) }}" alt="{{ $player->name }}">
          @else
            {{ mb_strtoupper(mb_substr($player->name, 0, 1)) }}
          @endif
        </div>
        <div class="rate-name">{{ $player->name }}</div>
      </div>

      <input type="hidden" name="ratings[{{ $i }}][user_id]" value="{{ $player->id }}">

      {{-- Evaluación de nivel --}}
      @php
        $playerSport = $game->field->sport;
        $playerProfile = $player->sportProfileFor($playerSport);
      @endphp
      @if($playerProfile)
        <div style="font-size:12px; color:#888; margin-bottom:10px;">
          Categoría actual:
          <strong style="color:#111; text-transform:capitalize;">{{ $playerProfile->category }}</strong>
        </div>
      @endif

      <div class="assess-row" data-index="{{ $i }}">
        <button type="button" class="assess-btn" data-value="below">
          <span class="assess-icon"><i data-lucide="trending-down" style="width:20px;height:20px;stroke:currentColor;stroke-width:2;"></i></span>
          <span class="assess-label">Por debajo</span>
          <span class="assess-desc">Debería estar en una categoría menor</span>
        </button>
        <button type="button" class="assess-btn" data-value="match">
          <span class="assess-icon"><i data-lucide="check" style="width:20px;height:20px;stroke:currentColor;stroke-width:2;"></i></span>
          <span class="assess-label">A la altura</span>
          <span class="assess-desc">Está bien en su categoría actual</span>
        </button>
        <button type="button" class="assess-btn" data-value="above">
          <span class="assess-icon"><i data-lucide="trending-up" style="width:20px;height:20px;stroke:currentColor;stroke-width:2;"></i></span>
          <span class="assess-label">Por encima</span>
          <span class="assess-desc">Debería estar en una categoría mayor</span>
        </button>
        <input type="hidden" name="ratings[{{ $i }}][assessment]" class="assess-score" value="">
      </div>

      {{-- Comment --}}
      <textarea
        class="rate-comment"
        name="ratings[{{ $i }}][comment]"
        placeholder="Comentario opcional..."></textarea>
    </div>
    @endforeach

    <button type="submit" class="rate-submit" style="display:inline-flex;align-items:center;justify-content:center;gap:6px;">
      Enviar calificaciones <i data-lucide="arrow-right" style="width:16px;height:16px;stroke:currentColor;"></i>
    </button>
  </form>

</div>

<script>
  document.querySelectorAll('.assess-row').forEach(function(row) {
    const btns   = row.querySelectorAll('.assess-btn');
    const hidden = row.querySelector('.assess-score');

    btns.forEach(function(btn) {
      btn.addEventListener('click', function() {
        btns.forEach(function(b) {
          b.classList.remove('selected-below', 'selected-match', 'selected-above');
        });
        btn.classList.add('selected-' + btn.dataset.value);
        hidden.value = btn.dataset.value;
      });
    });
  });

  document.getElementById('rateForm').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('.assess-score');
    for (const input of inputs) {
      if (!input.value) {
        e.preventDefault();
        alert('Por favor evaluá a todos los jugadores antes de enviar.');
        return;
      }
    }
  });
</script>

@endsection
