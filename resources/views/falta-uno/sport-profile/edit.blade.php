@extends('layouts.app')

@section('title', 'Editar Perfil Deportivo')

@push('styles')
<style>
  .sp-form-wrap { max-width: 560px; margin: 0 auto; }
  .sp-form-card {
    background: #fff;
    border: 1px solid #ececec;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.04);
    overflow: hidden;
  }
  .sp-form-header {
    background: linear-gradient(135deg, #111 0%, #2d2d2d 100%);
    padding: 24px 28px;
    color: #fff;
  }
  .sp-form-header h1 { margin: 0; font-size: 20px; font-weight: 800; letter-spacing: -.02em; }
  .sp-form-header p { margin: 4px 0 0; font-size: 13px; color: rgba(255,255,255,.65); }
  .sp-form-body { padding: 24px 28px; display: grid; gap: 20px; }
  .sp-form-footer { padding: 18px 28px; border-top: 1px solid #f4f4f4; }

  .sp-field { display: grid; gap: 6px; }
  .sp-label { font-size: 13px; font-weight: 700; color: #333; }
  .sp-select {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    color: #111;
    background: #fff;
    outline: none;
    transition: border-color .15s;
    box-sizing: border-box;
  }
  .sp-select:focus { border-color: #111; }
  .sp-hint { font-size: 12px; color: #aaa; margin: 0; }

  .gender-group { display: flex; gap: 10px; }
  .gender-btn {
    flex: 1;
    padding: 10px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    text-align: center;
    background: #fff;
    color: #666;
    transition: all .15s;
  }
  .gender-btn:has(input:checked) {
    border-color: #111;
    background: #111;
    color: #fff;
  }
  .gender-btn input { display: none; }

  .sp-submit {
    width: 100%;
    padding: 13px;
    font-size: 15px;
    font-weight: 700;
    background: #111;
    color: #fff;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background .15s;
  }
  .sp-submit:hover { background: #222; }

  .sp-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    color: #dc2626;
    margin-bottom: 14px;
  }

  .sp-sport-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f4f4f4;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 15px;
    font-weight: 700;
    color: #333;
  }
</style>
@endpush

@section('content')

<div class="sp-form-wrap">
  <div style="margin-bottom:14px;">
    <a href="{{ url('/profile#sport-profile') }}"
       style="display:inline-flex; align-items:center; gap:5px; font-size:13px; color:#888; text-decoration:none; font-weight:600;"
       onmouseover="this.style.color='#111'" onmouseout="this.style.color='#888'">
      ← Volver a mi perfil
    </a>
  </div>

  @if($errors->any())
    <div class="sp-error">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <div class="sp-form-card">
    <div class="sp-form-header">
      <h1>Editar Perfil Deportivo</h1>
      <p>Actualizá tu información para este deporte.</p>
    </div>

    <form method="POST" action="{{ route('sport-profile.update', $profile->sport) }}">
      @csrf
      @method('PUT')
      <div class="sp-form-body">

        {{-- Deporte (no editable) --}}
        <div class="sp-field">
          <label class="sp-label">Deporte</label>
          <div class="sp-sport-badge">
            {{ match($profile->sport) {
              'football'   => '⚽ Fútbol',
              'padel'      => '🏓 Pádel',
              'tennis'     => '🎾 Tenis',
              'basketball' => '🏀 Básquet',
              'volleyball' => '🏐 Vóley',
              default      => ucfirst($profile->sport),
            } }}
          </div>
        </div>

        {{-- Categoría --}}
        <div class="sp-field">
          <label class="sp-label" for="category">Categoría</label>
          @if($profile->games_played >= 3)
            <div class="sp-sport-badge" style="background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;">
              {{ ucfirst($profile->category) }}
            </div>
            <p class="sp-hint" style="color:#b45309;">
              Jugaste {{ $profile->games_played }} partido(s). Tu categoría se actualiza automáticamente y no se puede cambiar manualmente.
            </p>
            <input type="hidden" name="category" value="{{ $profile->category }}">
          @else
            <select id="category" name="category" class="sp-select" required>
              @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ old('category', $profile->category) === $cat ? 'selected' : '' }}>
                  {{ ucfirst($cat) }}
                </option>
              @endforeach
            </select>
            <p class="sp-hint">Elegí honestamente para que los partidos sean justos. Después de 3 partidos, la categoría se actualiza automáticamente.</p>
          @endif
        </div>

        {{-- Género --}}
        <div class="sp-field">
          <label class="sp-label">Género</label>
          <div class="gender-group">
            <label class="gender-btn">
              <input type="radio" name="gender" value="male" {{ old('gender', $profile->gender) === 'male' ? 'checked' : '' }} required>
              Masculino
            </label>
            <label class="gender-btn">
              <input type="radio" name="gender" value="female" {{ old('gender', $profile->gender) === 'female' ? 'checked' : '' }}>
              Femenino
            </label>
          </div>
        </div>

        {{-- Grupo de edad (solo pádel) --}}
        @if($profile->sport === 'padel')
        <div class="sp-field">
          <label class="sp-label" for="age_group">Grupo de edad</label>
          <select id="age_group" name="age_group" class="sp-select" required>
            <option value="">Seleccioná grupo...</option>
            @php
              $ageGroups = [
                'sub10' => 'Sub 10',
                'sub12' => 'Sub 12',
                'sub14' => 'Sub 14',
                'sub16' => 'Sub 16',
                'sub18' => 'Sub 18',
                '19a25' => '19 a 25',
                '26a34' => '26 a 34',
                'open'  => 'Open',
                'mas35' => '+35',
                'mas40' => '+40',
                'mas45' => '+45',
                'mas50' => '+50',
                'mas55' => '+55',
                'mas60' => '+60',
              ];
            @endphp
            @foreach($ageGroups as $ag => $label)
              <option value="{{ $ag }}" {{ old('age_group', $profile->age_group) === $ag ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </div>
        @endif

      </div>
      <div class="sp-form-footer">
        <button type="submit" class="sp-submit">Guardar cambios →</button>
      </div>
    </form>
  </div>
</div>

@endsection
