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
              @php
                $staffId          = $staffUser->pivot->id;
                $staffPermissions = is_array($staffUser->pivot->permissions)
                    ? $staffUser->pivot->permissions
                    : (json_decode($staffUser->pivot->permissions ?? '[]', true) ?? []);
              @endphp
              <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:12px 16px; border:1px solid #f0f0f0; border-radius:12px; background:#fafafa; flex-wrap:wrap;">
                <div>
                  <div style="font-weight:700; font-size:14px;">{{ $staffUser->name }}</div>
                  <div style="font-size:12px; color:#888; margin-top:2px;">{{ $staffUser->email }}</div>
                  @if(empty($staffPermissions))
                    <div style="font-size:11px; color:#d97706; margin-top:4px; font-weight:600;">Sin permisos configurados — acceso bloqueado</div>
                  @else
                    <div style="font-size:11px; color:#059669; margin-top:4px;">{{ count($staffPermissions) }} permiso{{ count($staffPermissions) !== 1 ? 's' : '' }} activo{{ count($staffPermissions) !== 1 ? 's' : '' }}</div>
                  @endif
                </div>
                <div style="display:flex; gap:8px; flex-shrink:0;">
                  <button
                    type="button"
                    onclick="openPermissionsModal({{ $staffId }}, '{{ addslashes($staffUser->name) }}', {{ json_encode($staffPermissions) }})"
                    class="btn btn-primary"
                    style="font-size:12px; padding:6px 12px;"
                  >
                    Permisos
                  </button>
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
        <form method="POST" action="{{ route('va.staff.invite') }}">
          @csrf
          <input type="hidden" name="venue_id" value="{{ $venue->id }}">

          <div style="display:flex; gap:10px; align-items:flex-start; flex-wrap:wrap; margin-bottom:16px;">
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
          </div>

          {{-- Permisos para la invitación --}}
          <div style="border:1px solid #e5e7eb; border-radius:12px; padding:16px; margin-bottom:16px; background:#f9fafb;">
            <div style="font-weight:700; font-size:13px; margin-bottom:12px; color:#374151;">Permisos para el nuevo empleado</div>
            <div style="display:flex; gap:8px; margin-bottom:12px;">
              <button type="button" onclick="selectAllInvite_{{ $venue->id }}(true)" class="btn" style="font-size:11px; padding:4px 10px;">Seleccionar todos</button>
              <button type="button" onclick="selectAllInvite_{{ $venue->id }}(false)" class="btn" style="font-size:11px; padding:4px 10px;">Ninguno</button>
            </div>
            @include('va.staff._permissions_checkboxes', [
              'inputName'    => 'permissions',
              'currentPerms' => [],
              'idPrefix'     => 'invite_' . $venue->id . '_',
              'jsSelectAll'  => 'selectAllInvite_' . $venue->id,
            ])
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

  {{-- Modal de permisos --}}
  <div id="permissionsModal" style="display:none; position:fixed; inset:0; z-index:100; background:rgba(0,0,0,.5); align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:16px; padding:28px; max-width:520px; width:92%; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px rgba(0,0,0,.2);">
      <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <div style="font-size:16px; font-weight:800;" id="modalStaffName">Permisos</div>
        <button type="button" onclick="closePermissionsModal()" style="background:none; border:none; cursor:pointer; padding:4px; color:#888;">
          <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form id="permissionsForm" method="POST" action="">
        @csrf

        <div style="display:flex; gap:8px; margin-bottom:16px;">
          <button type="button" onclick="selectAllModal(true)" class="btn" style="font-size:11px; padding:4px 10px;">Seleccionar todos</button>
          <button type="button" onclick="selectAllModal(false)" class="btn" style="font-size:11px; padding:4px 10px;">Ninguno</button>
        </div>

        @include('va.staff._permissions_checkboxes', [
          'inputName'    => 'permissions',
          'currentPerms' => [],
          'idPrefix'     => 'modal_',
          'jsSelectAll'  => 'selectAllModal',
        ])

        <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
          <button type="button" onclick="closePermissionsModal()" class="btn">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar permisos</button>
        </div>
      </form>
    </div>
  </div>

@endsection

@push('styles')
<script>
  const STAFF_PERMISSIONS_ROUTE = '{{ url('/va/staff') }}';

  const ALL_PERMISSIONS = [
    'view_dashboard',
    'view_reservations',
    'create_manual_reservations',
    'cancel_reservations',
    'view_agenda',
    'manage_fields',
    'manage_schedules',
    'manage_blocks',
    'manage_discounts',
    'view_checkin',
  ];

  function openPermissionsModal(staffId, staffName, currentPermissions) {
    document.getElementById('modalStaffName').textContent = 'Permisos — ' + staffName;
    document.getElementById('permissionsForm').action = STAFF_PERMISSIONS_ROUTE + '/' + staffId + '/permissions';

    // Marcar/desmarcar checkboxes según permisos actuales
    ALL_PERMISSIONS.forEach(function(perm) {
      const cb = document.getElementById('modal_' + perm);
      if (cb) {
        cb.checked = currentPermissions.includes(perm);
      }
    });

    const modal = document.getElementById('permissionsModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }

  function closePermissionsModal() {
    document.getElementById('permissionsModal').style.display = 'none';
    document.body.style.overflow = '';
  }

  function selectAllModal(checked) {
    ALL_PERMISSIONS.forEach(function(perm) {
      const cb = document.getElementById('modal_' + perm);
      if (cb) cb.checked = checked;
    });
  }

  // Cerrar modal al hacer click fuera
  document.getElementById('permissionsModal').addEventListener('click', function(e) {
    if (e.target === this) closePermissionsModal();
  });

  @foreach($venues as $venue)
  function selectAllInvite_{{ $venue->id }}(checked) {
    ALL_PERMISSIONS.forEach(function(perm) {
      const cb = document.getElementById('invite_{{ $venue->id }}_' + perm);
      if (cb) cb.checked = checked;
    });
  }
  @endforeach
</script>
@endpush
