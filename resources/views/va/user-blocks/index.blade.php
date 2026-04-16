@extends('layouts.admin')

@section('title', 'Bloqueo de usuarios')
@section('page_title', 'Bloqueo de usuarios')
@section('page_subtitle', 'Bloqueá usuarios para que no puedan reservar ni unirse a partidos en tu complejo')

@section('content')

@include('va.partials.help-modal', [
  'helpKey'   => 'va_user_blocks',
  'helpTitle' => 'Bloqueo de usuarios',
  'helpText'  => 'Desde esta seccion podes bloquear usuarios especificos para que no puedan reservar canchas ni unirse a partidos de Falta Uno en tu complejo. El usuario bloqueado no recibe una notificacion, simplemente no podra completar la reserva.',
])

{{-- Flash messages --}}
@if(session('success'))
<div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.75rem; color: #065f46; font-size: 0.875rem; font-weight: 500;">
  {{ session('success') }}
</div>
@endif

@if(session('error'))
<div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; color: #991b1b; font-size: 0.875rem; font-weight: 500;">
  {{ session('error') }}
</div>
@endif

{{-- Formulario de bloqueo --}}
<div style="background: #fff; border-radius: 1rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); margin-bottom: 1.5rem;">

  <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
      <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.75rem; background: #fef2f2; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <svg style="width: 1rem; height: 1rem; color: #ef4444;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
      </div>
      <div>
        <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0;">Bloquear usuario</h2>
        <p style="font-size: 0.875rem; color: #64748b; margin: 0.25rem 0 0 0;">Busca un usuario por nombre o email y selecciona en que complejo bloquearlo.</p>
      </div>
    </div>
  </div>

  <div style="padding: 1.25rem 1.5rem;">
    <form method="POST" action="{{ route('va.user-blocks.store') }}" id="blockForm">
      @csrf

      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; align-items: end;">

        {{-- Complejo --}}
        <div>
          <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem;">Complejo</label>
          <select name="venue_id" required
                  style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 0.75rem; border: 1px solid #cbd5e1; background: #fff; font-size: 0.875rem; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.05); outline: none;">
            @foreach($venues as $venue)
              <option value="{{ $venue->id }}">{{ $venue->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Buscar usuario --}}
        <div style="position: relative;">
          <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem;">Usuario</label>
          <input type="text" id="userSearch" placeholder="Buscar por nombre o email..."
                 autocomplete="off"
                 style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 0.75rem; border: 1px solid #cbd5e1; background: #fff; font-size: 0.875rem; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.05); outline: none; box-sizing: border-box;">
          <input type="hidden" name="user_id" id="selectedUserId" required>
          {{-- Dropdown de resultados --}}
          <div id="searchResults" style="display: none; position: absolute; top: 100%; left: 0; right: 0; z-index: 50; background: #fff; border: 1px solid #e2e8f0; border-radius: 0.75rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-top: 0.25rem; max-height: 200px; overflow-y: auto;">
          </div>
        </div>

        {{-- Motivo --}}
        <div>
          <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem;">Motivo <span style="color: #94a3b8; text-transform: none; font-weight: 400;">(opcional)</span></label>
          <input name="reason" placeholder="Ej: comportamiento inadecuado"
                 style="width: 100%; padding: 0.5rem 0.75rem; border-radius: 0.75rem; border: 1px solid #cbd5e1; background: #fff; font-size: 0.875rem; color: #0f172a; box-shadow: 0 1px 2px rgba(0,0,0,0.05); outline: none; box-sizing: border-box;">
        </div>

      </div>

      <div style="margin-top: 1rem;">
        <button type="submit" id="blockBtn" disabled
                style="padding: 0.5rem 1.25rem; background: linear-gradient(to right, #dc2626, #ef4444); color: #fff; font-size: 0.875rem; font-weight: 600; border-radius: 0.75rem; border: none; cursor: pointer; opacity: 0.5; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;">
          Bloquear usuario
        </button>
      </div>
    </form>
  </div>

</div>

{{-- Lista de bloqueos --}}
<div style="background: #fff; border-radius: 1rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 2px rgba(0,0,0,0.05); overflow: hidden;">

  <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;">
    <h2 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0;">Usuarios bloqueados</h2>
    <p style="font-size: 0.875rem; color: #64748b; margin: 0.25rem 0 0 0;">Usuarios que no pueden reservar ni unirse a partidos en tus complejos.</p>
  </div>

  @if($blocks->isEmpty())
    <div style="padding: 3rem 1.5rem; text-align: center;">
      <div style="width: 3rem; height: 3rem; border-radius: 999px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
        <svg style="width: 1.25rem; height: 1.25rem; color: #94a3b8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
      </div>
      <p style="font-size: 0.875rem; color: #94a3b8; margin: 0;">No hay usuarios bloqueados.</p>
    </div>
  @else
    <div style="overflow-x: auto;">
      <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
        <thead>
          <tr style="background: #f8fafc; border-bottom: 1px solid #f1f5f9;">
            <th style="text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 1.5rem;">Usuario</th>
            <th style="text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 1.5rem;">Complejo</th>
            <th style="text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 1.5rem;">Motivo</th>
            <th style="text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 1.5rem;">Fecha</th>
            <th style="text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 1.5rem;">Bloqueado por</th>
            <th style="padding: 0.75rem 1.5rem;"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($blocks as $block)
            <tr style="border-bottom: 1px solid #f1f5f9;">
              <td style="padding: 0.875rem 1.5rem; white-space: nowrap;">
                <div style="font-weight: 600; color: #0f172a;">{{ $block->user->name }}</div>
                <div style="font-size: 0.75rem; color: #94a3b8;">{{ $block->user->email }}</div>
              </td>
              <td style="padding: 0.875rem 1.5rem; color: #334155;">{{ $block->venue->name }}</td>
              <td style="padding: 0.875rem 1.5rem; color: #475569;">
                @if($block->reason)
                  <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; background: #f1f5f9; color: #475569;">
                    {{ $block->reason }}
                  </span>
                @else
                  <span style="color: #cbd5e1;">—</span>
                @endif
              </td>
              <td style="padding: 0.875rem 1.5rem; color: #64748b; white-space: nowrap;">
                {{ $block->created_at->format('d/m/Y H:i') }}
              </td>
              <td style="padding: 0.875rem 1.5rem; color: #64748b;">
                {{ $block->blockedByUser->name ?? '—' }}
              </td>
              <td style="padding: 0.875rem 1.5rem; text-align: right;">
                <form method="POST" action="{{ route('va.user-blocks.destroy', $block) }}"
                      onsubmit="return confirm('Desbloquear a {{ $block->user->name }}?')">
                  @csrf
                  <button type="submit"
                          style="padding: 0.375rem 0.75rem; background: linear-gradient(to right, #16a34a, #22c55e); color: #fff; font-size: 0.75rem; font-weight: 600; border-radius: 0.5rem; border: none; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;">
                    Desbloquear
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

</div>

@endsection

@push('scripts')
<script>
(function() {
  const searchInput = document.getElementById('userSearch');
  const resultsDiv = document.getElementById('searchResults');
  const userIdInput = document.getElementById('selectedUserId');
  const blockBtn = document.getElementById('blockBtn');
  let debounceTimer = null;

  searchInput.addEventListener('input', function() {
    const q = this.value.trim();
    clearTimeout(debounceTimer);

    if (q.length < 2) {
      resultsDiv.style.display = 'none';
      return;
    }

    debounceTimer = setTimeout(() => {
      fetch('{{ route("va.user-blocks.search") }}?q=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(users => {
          if (users.length === 0) {
            resultsDiv.innerHTML = '<div style="padding: 0.75rem 1rem; font-size: 0.875rem; color: #94a3b8;">No se encontraron usuarios.</div>';
          } else {
            resultsDiv.innerHTML = users.map(u =>
              '<div class="user-result" data-id="' + u.id + '" data-name="' + u.name + '" data-email="' + u.email + '" ' +
              'style="padding: 0.625rem 1rem; cursor: pointer; transition: background 0.15s; border-bottom: 1px solid #f1f5f9;"' +
              'onmouseover="this.style.background=\'#f8fafc\'" onmouseout="this.style.background=\'transparent\'">' +
              '<div style="font-weight: 600; font-size: 0.875rem; color: #0f172a;">' + u.name + '</div>' +
              '<div style="font-size: 0.75rem; color: #94a3b8;">' + u.email + '</div>' +
              '</div>'
            ).join('');
          }
          resultsDiv.style.display = 'block';
        })
        .catch(() => {
          resultsDiv.style.display = 'none';
        });
    }, 300);
  });

  resultsDiv.addEventListener('click', function(e) {
    const item = e.target.closest('.user-result');
    if (!item) return;

    userIdInput.value = item.dataset.id;
    searchInput.value = item.dataset.name + ' (' + item.dataset.email + ')';
    resultsDiv.style.display = 'none';
    blockBtn.disabled = false;
    blockBtn.style.opacity = '1';
  });

  // Hide dropdown on click outside
  document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
      resultsDiv.style.display = 'none';
    }
  });

  // Reset if user clears the input
  searchInput.addEventListener('change', function() {
    if (!this.value.trim()) {
      userIdInput.value = '';
      blockBtn.disabled = true;
      blockBtn.style.opacity = '0.5';
    }
  });
})();
</script>
@endpush
