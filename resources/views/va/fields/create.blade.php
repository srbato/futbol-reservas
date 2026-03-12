@extends('layouts.admin')

@section('title', 'Crear cancha')
@section('page_title', 'Crear cancha')
@section('page_subtitle', 'Complejo: ' . $venue->name)

@section('content')
  <div class="admin-card">
    <form method="POST" action="{{ route('va.fields.store', $venue) }}">
      @csrf

      <div style="display:grid; gap:14px; max-width:700px;">
        <div>
          <label>Nombre</label><br>
          <input name="name" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div>
          <label>Deporte</label><br>
          <input name="sport" value="football" required
                 style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label>Formato (5/7/11)</label><br>
            <input type="number" name="format" value="5" min="3" max="11" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>

          <div>
            <label>Turno (min)</label><br>
            <input type="number" name="slot_minutes" value="60" min="30" max="180" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
          <div>
            <label>Precio por turno</label><br>
            <input type="number" name="price_per_slot" value="12000" step="0.01" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>

          <div>
            <label>Moneda</label><br>
            <input name="currency" value="ARS" required
                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:10px;">
          </div>
        </div>

        <div style="display:flex; gap:10px; align-items:center;">
          <button type="submit" style="padding:10px 14px; border:0; background:#111; color:#fff; border-radius:10px; cursor:pointer;">
            Crear cancha
          </button>
          <a href="{{ route('va.dashboard') }}">Volver</a>
        </div>
      </div>
    </form>
  </div>
@endsection