{{--
  Partial: checkboxes de permisos agrupados.

  Variables esperadas:
    $inputName    — nombre del campo (ej: "permissions")
    $currentPerms — array de permisos ya activos
    $idPrefix     — prefijo para los IDs de los inputs (para evitar duplicados en el DOM)
    $jsSelectAll  — nombre de la función JS de seleccionar todos (no se usa aquí, solo referencia)
--}}

@php
  $groups = [
    'Reservas' => [
      'view_reservations'         => 'Ver reservas',
      'create_manual_reservations'=> 'Crear reservas manuales',
      'cancel_reservations'       => 'Cancelar reservas',
      'view_agenda'               => 'Ver agenda',
    ],
    'Canchas' => [
      'manage_fields'    => 'Gestionar canchas (crear/editar/activar)',
      'manage_schedules' => 'Gestionar horarios',
      'manage_blocks'    => 'Gestionar bloqueos',
      'manage_discounts' => 'Gestionar descuentos',
    ],
    'General' => [
      'view_checkin' => 'Ver check-in',
    ],
  ];
@endphp

@foreach($groups as $groupLabel => $permissions)
  <div style="margin-bottom:14px;">
    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; margin-bottom:8px;">
      {{ $groupLabel }}
    </div>
    <div style="display:flex; flex-direction:column; gap:6px;">
      @foreach($permissions as $key => $label)
        <label style="display:flex; align-items:center; gap:10px; cursor:pointer; padding:6px 10px; border-radius:8px; background:#fff; border:1px solid #e5e7eb; transition: border-color .15s;"
               onmouseover="this.style.borderColor='#6366f1'" onmouseout="this.style.borderColor='#e5e7eb'">
          <input
            type="checkbox"
            id="{{ $idPrefix }}{{ $key }}"
            name="{{ $inputName }}[]"
            value="{{ $key }}"
            {{ in_array($key, $currentPerms ?? []) ? 'checked' : '' }}
            style="width:16px; height:16px; accent-color:#6366f1; flex-shrink:0;"
          >
          <span style="font-size:13px; color:#374151;">{{ $label }}</span>
        </label>
      @endforeach
    </div>
  </div>
@endforeach
