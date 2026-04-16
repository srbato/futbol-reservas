@extends('layouts.app')
@section('title', 'Suscripción recurrente')

@section('content')
<div style="max-width:540px; margin:40px auto; padding:0 16px;">

  {{-- Cabecera con info de la cancha --}}
  <div class="page-card" style="margin-bottom:20px;">
    <p class="muted" style="margin:0; font-size:14px;">
      {{ $subscription->field->name }} — {{ $subscription->field->venue->name }}
    </p>
  </div>

  {{-- Estado principal --}}
  @if($subscription->status === 'ACTIVE')

    <div class="page-card" style="text-align:center; margin-bottom:20px;">
      <div style="font-size:56px; margin-bottom:12px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:72px; height:72px; background:rgba(34,197,94,.1); border-radius:50%;">
          <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </span>
      </div>
      <h1 style="margin:0 0 10px 0; font-size:26px; font-weight:800; color:#6ee7a0;">¡Suscripción activada!</h1>
      <p style="margin:0 0 20px 0; color:#a0a0a0; line-height:1.6;">
        MercadoPago cobrará
        <strong>{{ $subscription->currency }} {{ number_format($subscription->monthly_amount, 0, ',', '.') }}</strong>
        por mes automáticamente y generará tus turnos de
        <strong>{{ $subscription->dayLabel() }}</strong>
        a las <strong>{{ \Carbon\Carbon::parse($subscription->start_time)->format('H:i') }}</strong>.
      </p>

      @if(count($nextOccurrences) > 0)
        <div style="background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.2); border-radius:10px; padding:16px; text-align:left; margin-bottom:20px;">
          <p style="margin:0 0 10px 0; font-size:13px; font-weight:700; color:#6ee7a0; text-transform:uppercase; letter-spacing:.04em;">
            Proximos turnos estimados
          </p>
          <ul style="margin:0; padding:0; list-style:none; display:flex; flex-direction:column; gap:8px;">
            @foreach($nextOccurrences as $occurrence)
              <li style="display:flex; justify-content:space-between; align-items:center; font-size:15px;">
                <span style="text-transform:capitalize;">
                  {{ $occurrence->locale('es')->isoFormat('dddd D [de] MMMM YYYY') }}
                </span>
                <span style="font-weight:700; color:#6ee7a0;">
                  {{ $occurrence->format('H:i') }}
                </span>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

      <div style="display:flex; flex-direction:column; gap:10px;">
        <a href="{{ route('my_reservations') }}" class="btn btn-primary" style="text-align:center;">
          Ver mis reservas
        </a>

        <form method="POST" action="{{ route('recurring.subscription.cancel', $subscription) }}"
              onsubmit="return confirm('¿Seguro que querés cancelar esta suscripción? Se cancelarán también tus turnos futuros.');">
          @csrf
          <button type="submit" class="btn" style="width:100%; background:rgba(229,57,53,.1); border:1px solid rgba(229,57,53,.2); color:#f87171; font-size:14px;">
            Cancelar suscripción
          </button>
        </form>
      </div>
    </div>

  @elseif($subscription->status === 'PENDING_PAYMENT')

    <div class="page-card" style="text-align:center; margin-bottom:20px;">
      <div style="margin-bottom:12px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:72px; height:72px; background:rgba(245,158,11,.08); border-radius:50%;">
          <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </span>
      </div>
      <h1 style="margin:0 0 10px 0; font-size:24px; font-weight:800; color:#fbbf24;">Pendiente de confirmacion</h1>
      <p style="margin:0 0 20px 0; color:#a0a0a0; line-height:1.6;">
        Estamos esperando la confirmacion de MercadoPago. Esto puede tardar unos minutos.
        Cuando se confirme, recibirás un email y tus turnos se crearán automáticamente.
      </p>
      <div style="background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.2); border-radius:8px; padding:12px; margin-bottom:20px;">
        <p style="margin:0; font-size:13px; color:#fbbf24;">
          Esta página se actualiza automáticamente. No hace falta que la cierres.
        </p>
      </div>
      <a href="{{ route('my_reservations') }}" class="btn btn-primary" style="display:inline-block;">
        Ver mis reservas
      </a>
    </div>

  @else

    {{-- FAILED o CANCELLED --}}
    <div class="page-card" style="text-align:center; margin-bottom:20px;">
      <div style="margin-bottom:12px;">
        <span style="display:inline-flex; align-items:center; justify-content:center; width:72px; height:72px; background:rgba(229,57,53,.1); border-radius:50%;">
          <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </span>
      </div>
      <h1 style="margin:0 0 10px 0; font-size:24px; font-weight:800; color:#f87171;">No se pudo activar la suscripción</h1>
      <p style="margin:0 0 20px 0; color:#a0a0a0; line-height:1.6;">
        Hubo un problema al procesar tu suscripción. Podés intentarlo nuevamente desde la página de la cancha.
      </p>
      <button onclick="history.back()" class="btn" style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.1); cursor:pointer;">
        Volver
      </button>
    </div>

  @endif

  {{-- Resumen de la suscripción --}}
  <div class="page-card">
    <h2 style="margin:0 0 14px 0; font-size:16px; font-weight:700;">Detalle de la suscripción</h2>
    <div style="display:flex; flex-direction:column; gap:10px; font-size:14px;">
      <div style="display:flex; justify-content:space-between;">
        <span class="muted">Dia</span>
        <span style="font-weight:600;">{{ $subscription->dayLabel() }}</span>
      </div>
      <div style="display:flex; justify-content:space-between;">
        <span class="muted">Horario</span>
        <span style="font-weight:600;">{{ \Carbon\Carbon::parse($subscription->start_time)->format('H:i') }}</span>
      </div>
      <div style="display:flex; justify-content:space-between;">
        <span class="muted">Frecuencia</span>
        <span style="font-weight:600;">{{ $subscription->frequencyLabel() }}</span>
      </div>
      <div style="display:flex; justify-content:space-between; border-top:1px solid rgba(255,255,255,.08); padding-top:10px; margin-top:2px;">
        <span class="muted">Monto mensual</span>
        <span style="font-weight:800; font-size:16px;">
          {{ $subscription->currency }} {{ number_format($subscription->monthly_amount, 0, ',', '.') }}
        </span>
      </div>
    </div>
  </div>

</div>

@if($subscription->status === 'PENDING_PAYMENT')
<script>
  setTimeout(function checkStatus() {
    fetch('/reservas/suscripcion/{{ $subscription->id }}/estado')
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (data.status !== 'PENDING_PAYMENT') {
          window.location.reload();
        } else {
          setTimeout(checkStatus, 5000);
        }
      })
      .catch(function() { setTimeout(checkStatus, 5000); });
  }, 5000);
</script>
@endif

@endsection
