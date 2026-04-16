@extends('layouts.app')

@section('title', 'Inscribir Equipo — ' . $tournament->name)

@push('styles')
<style>
  /* ── Layout ─────────────────────────────────────── */
  .te-create-wrap {
    max-width: 680px;
    margin: 0 auto;
    padding: 32px 16px 64px;
  }

  /* ── Back link ──────────────────────────────────── */
  .te-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    font-weight: 600;
    color: #a0a0a0;
    text-decoration: none;
    margin-bottom: 20px;
    transition: color .15s;
  }
  .te-back:hover { color: #22c55e; }
  .te-back svg { width: 16px; height: 16px; }

  /* ── Card ────────────────────────────────────────── */
  .te-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.2);
    padding: 32px;
  }

  /* ── Header ─────────────────────────────────────── */
  .te-header {
    margin-bottom: 28px;
  }
  .te-header-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(34,197,94,.12);
    color: #22c55e;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    padding: 5px 12px;
    border-radius: 999px;
    margin-bottom: 12px;
  }
  .te-header-badge svg { width: 14px; height: 14px; }
  .te-header h1 {
    font-size: 26px;
    font-weight: 800;
    color: #e8e8e8;
    margin: 0 0 6px;
    letter-spacing: -.02em;
  }
  .te-header p {
    font-size: 14px;
    color: #a0a0a0;
    margin: 0;
  }
  .te-header-tournament {
    font-weight: 600;
    color: #e8e8e8;
  }

  /* ── Form ────────────────────────────────────────── */
  .te-form-group {
    margin-bottom: 20px;
  }
  .te-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #e8e8e8;
    margin-bottom: 6px;
  }
  .te-label-hint {
    font-weight: 400;
    color: #666;
    font-size: 13px;
  }
  .te-input {
    width: 100%;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 15px;
    font-family: inherit;
    color: #e8e8e8;
    background: #111;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
  }
  .te-input:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.1);
  }
  .te-input::placeholder { color: #666; }
  .te-input.is-invalid { border-color: #ef4444; }
  .te-input-readonly {
    background: #1a1a1a;
    color: #666;
    cursor: not-allowed;
  }
  .te-error {
    font-size: 13px;
    color: #ef4444;
    margin-top: 4px;
  }

  /* ── File upload ────────────────────────────────── */
  .te-file-area {
    display: flex;
    align-items: center;
    gap: 16px;
  }
  .te-file-preview {
    width: 72px;
    height: 72px;
    border-radius: 14px;
    border: 2px dashed rgba(255,255,255,.12);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
    background: #0a0a0a;
    transition: border-color .15s;
  }
  .te-file-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .te-file-preview svg { width: 28px; height: 28px; color: rgba(255,255,255,.2); }
  .te-file-input-wrap { flex: 1; }
  .te-file-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #e8e8e8;
    background: #111;
    cursor: pointer;
    transition: border-color .15s, background .15s;
  }
  .te-file-btn:hover { border-color: #22c55e; background: rgba(34,197,94,.04); }
  .te-file-btn svg { width: 16px; height: 16px; }
  .te-file-hint {
    font-size: 12px;
    color: #666;
    margin-top: 6px;
  }

  /* ── Divider ────────────────────────────────────── */
  .te-divider {
    border: none;
    border-top: 1px solid rgba(255,255,255,.06);
    margin: 28px 0;
  }

  /* ── Players section ────────────────────────────── */
  .te-players-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }
  .te-players-title {
    font-size: 16px;
    font-weight: 700;
    color: #e8e8e8;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .te-players-title svg { width: 18px; height: 18px; color: #22c55e; }
  .te-players-count {
    font-size: 13px;
    font-weight: 600;
    color: #666;
  }

  .te-player-row {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    margin-bottom: 10px;
    animation: te-slide-in .25s ease;
  }
  @keyframes te-slide-in {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .te-player-row.te-removing {
    animation: te-slide-out .2s ease forwards;
  }
  @keyframes te-slide-out {
    from { opacity: 1; transform: translateY(0); max-height: 60px; }
    to   { opacity: 0; transform: translateY(-8px); max-height: 0; margin-bottom: 0; }
  }
  .te-player-name { flex: 1; }
  .te-player-role { width: 160px; flex-shrink: 0; }
  .te-player-remove {
    width: 40px;
    height: 44px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(239,68,68,.25);
    border-radius: 12px;
    background: #111;
    color: #ef4444;
    cursor: pointer;
    transition: background .15s, border-color .15s;
  }
  .te-player-remove:hover { background: rgba(239,68,68,.1); border-color: #ef4444; }
  .te-player-remove svg { width: 16px; height: 16px; }
  .te-player-remove-placeholder {
    width: 40px;
    flex-shrink: 0;
  }

  .te-player-captain-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 700;
    color: #d97706;
    background: rgba(217,119,6,.1);
    padding: 2px 8px;
    border-radius: 999px;
    margin-top: 4px;
  }
  .te-player-captain-badge svg { width: 12px; height: 12px; }

  .te-select {
    width: 100%;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 15px;
    font-family: inherit;
    color: #e8e8e8;
    background: #111;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 38px;
  }
  .te-select:focus {
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34,197,94,.1);
  }

  /* ── Add player btn ─────────────────────────────── */
  .te-add-player {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    border: 1.5px dashed rgba(255,255,255,.12);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #a0a0a0;
    background: transparent;
    cursor: pointer;
    transition: border-color .15s, color .15s, background .15s;
    font-family: inherit;
    margin-top: 6px;
  }
  .te-add-player:hover {
    border-color: #22c55e;
    color: #22c55e;
    background: rgba(34,197,94,.04);
  }
  .te-add-player:disabled {
    opacity: .45;
    cursor: not-allowed;
  }
  .te-add-player svg { width: 16px; height: 16px; }

  /* ── Submit ─────────────────────────────────────── */
  .te-submit-wrap {
    margin-top: 28px;
    display: flex;
    justify-content: flex-end;
  }
  .te-submit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    background: #22c55e;
    color: #052e16;
    border: none;
    border-radius: 14px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    transition: background .15s, transform .15s, box-shadow .15s;
    box-shadow: 0 4px 14px rgba(34,197,94,.3);
  }
  .te-submit:hover {
    background: #16a34a;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(34,197,94,.4);
  }
  .te-submit:active { transform: translateY(0); }
  .te-submit svg { width: 18px; height: 18px; }

  /* ── Alerts ─────────────────────────────────────── */
  .te-alert {
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
  }
  .te-alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }
  .te-alert-error {
    background: rgba(239,68,68,.08);
    color: #f87171;
    border: 1px solid rgba(239,68,68,.25);
  }
  .te-alert-info {
    background: rgba(59,130,246,.08);
    color: #60a5fa;
    border: 1px solid rgba(59,130,246,.25);
  }

  /* ── Responsive ─────────────────────────────────── */
  @media (max-width: 640px) {
    .te-card { padding: 24px 20px; }
    .te-header h1 { font-size: 22px; }
    .te-player-row { flex-wrap: wrap; gap: 8px; }
    .te-player-name { flex: 1 1 100%; }
    .te-player-role { width: 100%; flex: 1 1 calc(100% - 50px); }
    .te-file-area { flex-direction: column; align-items: flex-start; }
  }
</style>
@endpush

@section('content')
<div class="te-create-wrap" x-data="{
  logoPreview: null,
  players: [],
  idCounter: 0,
  previewLogo(event) {
    const file = event.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) {
      alert('El archivo es demasiado grande. Maximo 2 MB.');
      event.target.value = '';
      return;
    }
    const reader = new FileReader();
    reader.onload = (e) => { this.logoPreview = e.target.result; };
    reader.readAsDataURL(file);
  },
  addPlayer() {
    this.players.push({ id: ++this.idCounter, name: '', dni: '', jersey_number: '', removing: false });
    this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
  },
  removePlayer(index) {
    this.players[index].removing = true;
    setTimeout(() => { this.players.splice(index, 1); }, 200);
  },
}">

  {{-- Back --}}
  <a href="{{ route('torneos.show', $tournament) }}" class="te-back">
    <i data-lucide="arrow-left"></i>
    Volver al torneo
  </a>

  {{-- Validation errors --}}
  @if($errors->any())
    <div class="te-alert te-alert-error">
      <i data-lucide="alert-circle"></i>
      <div>
        <strong>Hay errores en el formulario:</strong>
        <ul style="margin: 6px 0 0; padding-left: 18px;">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <div class="te-card">
    {{-- Header --}}
    <div class="te-header">
      <div class="te-header-badge">
        <i data-lucide="users"></i>
        Inscripcion de equipo
      </div>
      <h1>Inscribir Equipo</h1>
      <p>Torneo: <span class="te-header-tournament">{{ $tournament->name }}</span></p>
      @if($tournament->players_per_team)
        <div class="te-alert te-alert-info" style="margin-top: 12px; margin-bottom: 0;">
          <i data-lucide="info"></i>
          <span>Minimo <strong>{{ $tournament->players_per_team }} jugadores</strong> por equipo.</span>
        </div>
      @endif
    </div>

    <form action="{{ route('torneos.teams.store', $tournament) }}" method="POST" enctype="multipart/form-data">
      @csrf

      {{-- Team name --}}
      <div class="te-form-group">
        <label for="team_name" class="te-label">Nombre del equipo</label>
        <input
          type="text"
          id="team_name"
          name="team_name"
          class="te-input @error('team_name') is-invalid @enderror"
          value="{{ old('team_name') }}"
          placeholder="Ej: Los Cracks FC"
          required
          maxlength="100"
        >
        @error('team_name')
          <div class="te-error">{{ $message }}</div>
        @enderror
      </div>

      {{-- Logo --}}
      <div class="te-form-group">
        <label class="te-label">
          Logo del equipo
          <span class="te-label-hint">(opcional)</span>
        </label>
        <div class="te-file-area">
          <div class="te-file-preview">
            <template x-if="logoPreview">
              <img :src="logoPreview" alt="Preview del logo">
            </template>
            <template x-if="!logoPreview">
              <i data-lucide="image"></i>
            </template>
          </div>
          <div class="te-file-input-wrap">
            <label class="te-file-btn">
              <i data-lucide="upload"></i>
              Subir logo
              <input
                type="file"
                name="logo"
                accept="image/jpeg,image/png,image/webp"
                style="display: none;"
                @change="previewLogo($event)"
              >
            </label>
            <div class="te-file-hint">JPG, PNG o WebP. Max 2 MB.</div>
          </div>
        </div>
        @error('logo')
          <div class="te-error">{{ $message }}</div>
        @enderror
      </div>

      <hr class="te-divider">

      {{-- Players --}}
      <div class="te-players-header">
        <div class="te-players-title">
          <i data-lucide="users"></i>
          Jugadores
        </div>
        <div class="te-players-count">
          <span x-text="players.length + 1"></span>
          jugador<span x-show="players.length !== 0">es</span>
        </div>
      </div>

      {{-- Captain row (editable) --}}
      <div class="te-player-row">
        <div class="te-player-name" style="flex:1;">
          <input
            type="text"
            name="players[0][name]"
            class="te-input"
            value="{{ old('players.0.name', auth()->user()->name) }}"
            placeholder="Nombre del capitan"
            required
          >
          <input type="hidden" name="players[0][role]" value="capitan">
          <div class="te-player-captain-badge">
            <i data-lucide="crown"></i>
            Capitan
          </div>
        </div>
        <input
          type="text"
          name="players[0][dni]"
          class="te-input"
          value="{{ old('players.0.dni') }}"
          placeholder="DNI"
          required
          style="width:120px;flex-shrink:0;"
        >
        <input
          type="number"
          name="players[0][jersey_number]"
          class="te-input"
          value="{{ old('players.0.jersey_number') }}"
          placeholder="N°"
          required
          min="0"
          max="99"
          style="width:70px;flex-shrink:0;text-align:center;"
        >
        <div class="te-player-remove-placeholder"></div>
      </div>

      {{-- Dynamic player rows --}}
      <template x-for="(player, index) in players" :key="player.id">
        <div class="te-player-row" :class="{ 'te-removing': player.removing }">
          <div class="te-player-name" style="flex:1;">
            <input
              type="text"
              :name="'players[' + (index + 1) + '][name]'"
              x-model="player.name"
              class="te-input"
              placeholder="Nombre del jugador"
              required
            >
            <input type="hidden" :name="'players[' + (index + 1) + '][role]'" value="jugador">
          </div>
          <input
            type="text"
            :name="'players[' + (index + 1) + '][dni]'"
            x-model="player.dni"
            class="te-input"
            placeholder="DNI"
            required
            style="width:120px;flex-shrink:0;"
          >
          <input
            type="number"
            :name="'players[' + (index + 1) + '][jersey_number]'"
            x-model="player.jersey_number"
            class="te-input"
            placeholder="N°"
            required
            min="0"
            max="99"
            style="width:70px;flex-shrink:0;text-align:center;"
          >
          <button
            type="button"
            class="te-player-remove"
            @click="removePlayer(index)"
            title="Eliminar jugador"
          >
            <i data-lucide="x"></i>
          </button>
        </div>
      </template>

      {{-- Add player button --}}
      <button
        type="button"
        class="te-add-player"
        @click="addPlayer()"
      >
        <i data-lucide="plus"></i>
        Agregar jugador
      </button>

      {{-- Submit --}}
      <div class="te-submit-wrap">
        <button type="submit" class="te-submit">
          <i data-lucide="check-circle"></i>
          Inscribir equipo
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
