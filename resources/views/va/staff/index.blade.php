@extends('layouts.admin')

@section('title', 'Gestión de empleados')
@section('page_title', 'Empleados')
@section('page_subtitle', 'Invitá y gestioná los empleados de tus complejos')

@section('content')

  @if($venues->isEmpty())
    <div class="admin-card" style="text-align:center; padding:40px;">
      <div style="font-size:40px; margin-bottom:12px;">🏟️</div>
      <div style="font-weight:800; font-size:16px; margin-bottom:8px;">No tenés complejos todavía</div>
      <div style="color:#888; font-size:14px;">Creá un complejo primero para poder agregar empleados.</div>
    </div>
  @endif

  @foreach($venues as $venue)
    <div class="admin-card" style="margin-bottom:24px;">

      <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:20px;">
        <div>
          <div style="font-size:18px; font-weight:800;">{{ $venue->name }}</div>
          <div style="font-size:13px; color:#888; margin-top:2px;">
            {{ $venue->staff->count() }} empleado{{ $venue->staff->count() !== 1 ? 's' : '' }} activo{{ $venue->staff->count() !== 1 ? 's' : '' }}
          </div>
        </div>
      </div>

      {{-- Empleados activos --}}
      @if($venue->staff->isNotEmpty())
        <div style="margin-bottom:20px;">
          <div class="section-title" style="margin-bottom:12px;">Empleados activos</div>
          <div style="display:flex; flex-direction:column; gap:8px;">
            @foreach($venue->staff as $staffUser)
              <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 16px; border:1px solid #f0f0f0; border-radius:12px; background:#fafafa;">
                <div>
                  <div style="font-weight:700; font-size:14px;">{{ $staffUser->name }}</div>
                  <div style="font-size:12px; color:#888; margin-top:2px;">{{ $staffUser->email }}</div>
                </div>
                <form method="POST" action="{{ route('va.staff.remove') }}" style="margin:0;"
                      onsubmit="return confirm('¿Quitar a {{ $staffUser->name }} del complejo?')">
                  @csrf
                  <input type="hidden" name="venue_id" value="{{ $venue->id }}">
                  <input type="hidden" name="user_id" value="{{ $staffUser->id }}">
                  <button type="submit" class="btn" style="font-size:12px; padding:6px 12px; color:#842029; border-color:#f1b9c0;">
                    Quitar
                  </button>
                </form>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      {{-- Invitaciones pendientes --}}
      @if($venue->staffInvitations->isNotEmpty())
        <div style="margin-bottom:20px;">
          <div class="section-title" style="margin-bottom:12px;">Invitaciones pendientes</div>
          <div style="display:flex; flex-direction:column; gap:8px;">
            @foreach($venue->staffInvitations as $inv)
              <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 16px; border:1px solid #f5d48a; border-radius:12px; background:#fff9ec;">
                <div>
                  <div style="font-weight:700; font-size:14px;">{{ $inv->user->name }}</div>
                  <div style="font-size:12px; color:#888; margin-top:2px;">{{ $inv->user->email }}</div>
                  <div style="font-size:11px; color:#9a6700; margin-top:4px;">
                    Vence {{ $inv->expires_at->format('d/m/Y H:i') }}
                  </div>
                </div>
                <form method="POST" action="{{ route('va.staff.cancel_invitation') }}" style="margin:0;"
                      onsubmit="return confirm('¿Cancelar la invitación?')">
                  @csrf
                  <input type="hidden" name="invitation_id" value="{{ $inv->id }}">
                  <button type="submit" class="btn" style="font-size:12px; padding:6px 12px;">
                    Cancelar
                  </button>
                </form>
              </div>
            @endforeach
          </div>
        </div>
      @endif

      {{-- Formulario invitar --}}
      <div>
        <div class="section-title" style="margin-bottom:12px;">Invitar empleado</div>
        <form method="POST" action="{{ route('va.staff.invite') }}" style="display:flex; gap:10px; align-items:flex-start; flex-wrap:wrap;">
          @csrf
          <input type="hidden" name="venue_id" value="{{ $venue->id }}">
          <div style="flex:1; min-width:220px;">
            <input
              type="email"
              name="email"
              placeholder="Email del empleado"
              value="{{ old('email') }}"
              style="width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:10px; font-size:14px; font-family:inherit;"
            >
            @error('email')
              <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
            Enviar invitación
          </button>
        </form>
        <p style="font-size:12px; color:#aaa; margin:8px 0 0 0;">
          El usuario debe estar registrado en TuCancha. Recibirá un email para aceptar o rechazar.
        </p>
      </div>

    </div>
  @endforeach

@endsection
