@extends('layouts.app')

@section('title', 'Crear Perfil Deportivo')

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
  .sp-select, .sp-input {
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
  .sp-select:focus, .sp-input:focus { border-color: #111; }
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
</style>
@endpush

@section('content')

<div class="sp-form-wrap">
  <div style="margin-bottom:14px;">
    <a href="{{ route('sport-profile.index') }}"
       style="display:inline-flex; align-items:center; gap:5px; font-size:13px; color:#888; text-decoration:none; font-weight:600;"
       onmouseover="this.style.color='#111'" onmouseout="this.style.color='#888'">
      ← Volver
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
      <h1>Crear Perfil Deportivo</h1>
      <p>Completá tu perfil para poder unirte a partidos Falta Uno.</p>
    </div>

    <form method="POST" action="{{ route('sport-profile.store') }}">
      @csrf
      <div class="sp-form-body">

        {{-- Deporte --}}
        <div class="sp-field">
          <label class="sp-label" for="sport">Deporte</label>
          <select id="sport" name="sport" class="sp-select" required onchange="updateCategories(this.value)">
            <option value="">Seleccioná un deporte...</option>
            <option value="football"   {{ old('sport', $sport) === 'football'   ? 'selected' : '' }}>⚽ Fútbol</option>
            <option value="padel"      {{ old('sport', $sport) === 'padel'      ? 'selected' : '' }}>🏓 Pádel</option>
            <option value="tennis"     {{ old('sport', $sport) === 'tennis'     ? 'selected' : '' }}>🎾 Tenis</option>
            <option value="basketball" {{ old('sport', $sport) === 'basketball' ? 'selected' : '' }}>🏀 Básquet</option>
            <option value="volleyball" {{ old('sport', $sport) === 'volleyball' ? 'selected' : '' }}>🏐 Vóley</option>
          </select>
        </div>

        {{-- Categoría --}}
        <div class="sp-field">
          <label class="sp-label" for="category">Categoría</label>
          <select id="category" name="category" class="sp-select" required>
            <option value="">Primero seleccioná un deporte...</option>
          </select>
          <p class="sp-hint">Elegí honestamente para que los partidos sean justos.</p>
        </div>

        {{-- Género --}}
        <div class="sp-field">
          <label class="sp-label">Género</label>
          <div class="gender-group">
            <label class="gender-btn">
              <input type="radio" name="gender" value="male" {{ old('gender') === 'male' ? 'checked' : '' }} required>
              Masculino
            </label>
            <label class="gender-btn">
              <input type="radio" name="gender" value="female" {{ old('gender') === 'female' ? 'checked' : '' }}>
              Femenino
            </label>
          </div>
        </div>

        {{-- Grupo de edad (solo pádel) --}}
        <div class="sp-field" id="age-group-field" style="display:none;">
          <label class="sp-label" for="age_group">Grupo de edad</label>
          <select id="age_group" name="age_group" class="sp-select">
            <option value="">Seleccioná grupo...</option>
            <option value="sub10"  {{ old('age_group') === 'sub10'  ? 'selected' : '' }}>Sub 10</option>
            <option value="sub12"  {{ old('age_group') === 'sub12'  ? 'selected' : '' }}>Sub 12</option>
            <option value="sub14"  {{ old('age_group') === 'sub14'  ? 'selected' : '' }}>Sub 14</option>
            <option value="sub16"  {{ old('age_group') === 'sub16'  ? 'selected' : '' }}>Sub 16</option>
            <option value="sub18"  {{ old('age_group') === 'sub18'  ? 'selected' : '' }}>Sub 18</option>
            <option value="19a25"  {{ old('age_group') === '19a25'  ? 'selected' : '' }}>19 a 25</option>
            <option value="26a34"  {{ old('age_group') === '26a34'  ? 'selected' : '' }}>26 a 34</option>
            <option value="open"   {{ old('age_group') === 'open'   ? 'selected' : '' }}>Open</option>
            <option value="mas35"  {{ old('age_group') === 'mas35'  ? 'selected' : '' }}>+35</option>
            <option value="mas40"  {{ old('age_group') === 'mas40'  ? 'selected' : '' }}>+40</option>
            <option value="mas45"  {{ old('age_group') === 'mas45'  ? 'selected' : '' }}>+45</option>
            <option value="mas50"  {{ old('age_group') === 'mas50'  ? 'selected' : '' }}>+50</option>
            <option value="mas55"  {{ old('age_group') === 'mas55'  ? 'selected' : '' }}>+55</option>
            <option value="mas60"  {{ old('age_group') === 'mas60'  ? 'selected' : '' }}>+60</option>
          </select>
        </div>

      </div>
      <div class="sp-form-footer">
        <button type="submit" class="sp-submit">Crear perfil →</button>
      </div>
    </form>
  </div>
</div>

<script>
  const CATEGORIES = {
    padel:      ['primera', 'segunda', 'tercera', 'cuarta', 'quinta', 'sexta', 'septima', 'octava'],
    football:   ['recreativo', 'intermedio', 'avanzado', 'competitivo'],
    tennis:     ['recreativo', 'intermedio', 'avanzado', 'competitivo'],
    basketball: ['recreativo', 'intermedio', 'avanzado', 'competitivo'],
    volleyball: ['recreativo', 'intermedio', 'avanzado', 'competitivo'],
  };

  function updateCategories(sport) {
    const catSelect  = document.getElementById('category');
    const ageField   = document.getElementById('age-group-field');
    const ageSelect  = document.getElementById('age_group');
    const cats       = CATEGORIES[sport] || [];

    catSelect.innerHTML = '<option value="">Seleccioná categoría...</option>';
    cats.forEach(c => {
      const opt = document.createElement('option');
      opt.value = c;
      opt.textContent = c.charAt(0).toUpperCase() + c.slice(1);
      catSelect.appendChild(opt);
    });

    if (sport === 'padel') {
      ageField.style.display = 'block';
      ageSelect.required = true;
    } else {
      ageField.style.display = 'none';
      ageSelect.required = false;
      ageSelect.value = '';
    }
  }

  // Init if sport was pre-selected (old value or query param)
  const initialSport = document.getElementById('sport').value;
  if (initialSport) {
    updateCategories(initialSport);
    // Restore old category selection after repopulating
    const oldCat = '{{ old('category') }}';
    if (oldCat) {
      document.getElementById('category').value = oldCat;
    }
  }
</script>

@endsection
