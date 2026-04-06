@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.app')

@section('title', 'Reserva #' . $reservation->id)

@section('content')
  <div style="max-width:900px; margin:auto; display:grid; gap:18px;">
    <div class="page-card">
      <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-start;">
        <div>
          <h1 style="margin:0 0 10px 0; font-size:34px; letter-spacing:-0.02em;">
            Reserva #{{ $reservation->id }}
          </h1>

          <p class="muted" style="margin:0; line-height:1.6;">
            Revisá el estado de tu reserva, el horario y las acciones disponibles.
          </p>
        </div>

        <div>
          <span class="badge" style="{{ ReservationStatus::color($reservation->status) }}">
            {{ ReservationStatus::label($reservation->status) }}
          </span>
        </div>
      </div>
    </div>

    @if($reservation->status === 'PENDING_PAYMENT' && $reservation->expires_at && $reservation->expires_at->isFuture())
      <div class="page-card" style="background:#fff4db; border-color:#f5d48a;">
        <div style="color:#9a6700; font-weight:700; margin-bottom:6px;">Pago pendiente</div>
        <div style="color:#9a6700; line-height:1.6;">
          Esta reserva sigue pendiente de pago y vence el
          <strong>{{ $reservation->expires_at->format('d/m/Y H:i') }}</strong>.
        </div>
      </div>
    @elseif($reservation->status === 'PAID')
      <div class="page-card" style="background:#e8f7ee; border-color:#cfe9d7;">
        <div style="color:#157347; font-weight:700; margin-bottom:6px;">Reserva confirmada</div>
        <div style="color:#157347; line-height:1.6;">
          El pago fue aprobado correctamente. Ya tenés tu turno confirmado.
        </div>
      </div>
    @elseif($reservation->status === 'EXPIRED')
      <div class="page-card" style="background:#f8d7da; border-color:#f1b9c0;">
        <div style="color:#842029; font-weight:700; margin-bottom:6px;">Reserva vencida</div>
        <div style="color:#842029; line-height:1.6;">
          La reserva venció por falta de pago. Si querés ese turno, vas a tener que volver a reservar.
        </div>
      </div>
    @elseif($reservation->status === 'CANCELLED')
      <div class="page-card" style="background:#f8d7da; border-color:#f1b9c0;">
        <div style="color:#842029; font-weight:700; margin-bottom:6px;">Reserva cancelada</div>
        <div style="color:#842029; line-height:1.6;">
          Esta reserva fue cancelada y ya no está activa.
        </div>
      </div>
    @endif

    <div style="display:grid; grid-template-columns:1.1fr .9fr; gap:18px;">
      <div class="page-card">
        <h2 class="section-title" style="font-size:24px; margin-bottom:14px;">Detalle de la reserva</h2>

        <div style="display:grid; gap:12px;">
          <div>
            <div style="font-size:12px; color:#666; margin-bottom:4px;">Complejo</div>
            <div style="font-size:18px; font-weight:700;">{{ $reservation->field->venue->name }}</div>
          </div>

          <div>
            <div style="font-size:12px; color:#666; margin-bottom:4px;">Cancha</div>
            <div style="font-size:18px; font-weight:700;">{{ $reservation->field->name }}</div>
          </div>

          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <span class="badge">{{ $reservation->start_at->format('d/m/Y') }}</span>
            <span class="badge">{{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}</span>
          </div>

          <div style="display:grid; gap:8px; margin-top:8px;">
            <div style="display:flex; justify-content:space-between; gap:12px;">
              <span class="muted">Total</span>
              <strong>{{ $reservation->currency }} {{ number_format($reservation->total_amount, 0, ',', '.') }}</strong>
            </div>

            <div style="display:flex; justify-content:space-between; gap:12px;">
              <span class="muted">Estado</span>
              <strong>{{ ReservationStatus::label($reservation->status) }}</strong>
            </div>

            @if($reservation->payment_provider)
              <div style="display:flex; justify-content:space-between; gap:12px;">
                <span class="muted">Proveedor de pago</span>
                <strong>{{ ucfirst($reservation->payment_provider) }}</strong>
              </div>
            @endif

            @if($reservation->payment_status)
              <div style="display:flex; justify-content:space-between; gap:12px;">
                <span class="muted">Estado del pago</span>
                <strong>{{ $reservation->payment_status }}</strong>
              </div>
            @endif

            @if($reservation->expires_at)
              <div style="display:flex; justify-content:space-between; gap:12px;">
                <span class="muted">Vencimiento</span>
                <strong>{{ $reservation->expires_at->format('d/m/Y H:i') }}</strong>
              </div>
            @endif
          </div>
        </div>
      </div>

      <div style="display:grid; gap:18px;">
        <div class="page-card">
          <h2 class="section-title" style="font-size:24px; margin-bottom:14px;">Código de verificación</h2>

          @if($reservation->status === 'PAID' && $reservation->verification_code)
            <div style="font-size:34px; font-weight:800; letter-spacing:.08em; line-height:1.1;">
              {{ $reservation->verification_code }}
            </div>

            <p class="muted" style="margin:12px 0 0 0; line-height:1.6;">
              Mostrá este código al ingresar al complejo.
            </p>
          @else
            <p class="muted" style="margin:0; line-height:1.6;">
              El código de verificación se habilita cuando la reserva está pagada.
            </p>
          @endif
        </div>

        <div class="page-card">
          <h2 class="section-title" style="font-size:24px; margin-bottom:14px;">Acciones</h2>

          <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('venues.index') }}" class="btn">
              Volver a complejos
            </a>

            <a href="{{ route('my_reservations') }}" class="btn">
              Ver mis reservas
            </a>

            @if($reservation->status === 'PENDING_PAYMENT' && (!$reservation->expires_at || $reservation->expires_at->isFuture()))
              <a href="{{ route('reservations.checkout', $reservation) }}" class="btn btn-primary">
                Ir a pagar
              </a>
            @endif

            @if($reservation->status === 'PAID' && $reservation->batch_id === null && $reservation->start_at->isFuture())
              @php
                $modHours = $reservation->field->venue->modification_hours ?? null;
                $canModify = $modHours !== null && now()->isBefore($reservation->start_at->copy()->subHours($modHours));
                $deadlineExpired = $modHours !== null && !$canModify;
              @endphp
              @if($canModify)
                <a href="{{ route('reservations.modify.show', $reservation) }}" class="btn btn-primary">
                  Cambiar horario
                </a>
              @elseif($deadlineExpired)
                <span class="muted" style="font-size:13px; align-self:center;">
                  El plazo para modificar esta reserva venció (requiere {{ $modHours }}h de anticipación)
                </span>
              @endif
            @endif

            @if(in_array($reservation->status, ['EXPIRED', 'CANCELLED']))
              <a href="{{ route('fields.show', $reservation->field) }}" class="btn btn-primary">
                Volver a la cancha
              </a>
            @endif

            @if($reservation->status === 'PAID')
              @php
                $waMsg = "Reservé en *" . $reservation->field->venue->name . "* 🏟️\n"
                       . "📍 " . ($reservation->field->venue->address ?? '') . "\n"
                       . "⚽ " . $reservation->field->name . "\n"
                       . "📅 " . $reservation->start_at->format('d/m/Y') . " de " . $reservation->start_at->format('H:i') . " a " . $reservation->end_at->format('H:i') . "\n\n"
                       . "Sumate! Reservá en " . url('/');
              @endphp
              <a href="https://wa.me/?text={{ urlencode($waMsg) }}" target="_blank" rel="noopener"
                 class="btn" style="background:#25D366; color:#fff; border-color:#25D366;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="#fff" style="vertical-align:middle; margin-right:6px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.611.611l4.458-1.495A11.943 11.943 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.34 0-4.508-.654-6.363-1.787l-.362-.222-3.75 1.257 1.257-3.75-.222-.362A9.935 9.935 0 012 12C2 6.486 6.486 2 12 2s10 4.486 10 10-4.486 10-10 10z"/></svg>
                Compartir por WhatsApp
              </a>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Players section: visible when PAID --}}
    @if($reservation->status === 'PAID')
      <div class="page-card">
        <h2 class="section-title" style="font-size:24px; margin-bottom:6px;">Jugadores del partido</h2>
        <p class="muted" style="margin:0 0 18px 0; font-size:14px; line-height:1.6;">
          @if($isOwner)
            Agregá a los usuarios que jugaron con vos. Ellos podrán ver este partido en su historial y cargar el resultado.
          @else
            Jugadores que participaron en esta reserva.
          @endif
        </p>

        {{-- Current player list --}}
        @php
          $players = $reservation->players;
        @endphp

        @if($players->isEmpty())
          <div style="text-align:center; padding:16px 0; margin-bottom:18px;">
            <i data-lucide="users" style="width:32px;height:32px;stroke:#ccc;stroke-width:1.5;margin-bottom:8px;"></i>
            <div style="font-size:14px; color:#888; font-weight:600;">Todavía no hay jugadores agregados</div>
            @if($isOwner)
              <div style="font-size:13px; color:#aaa; margin-top:4px;">Usá el buscador de abajo para agregar a quienes jugaron con vos</div>
            @endif
          </div>
        @else
          <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:18px;">
            @foreach($players as $rp)
              <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:10px 14px; background:#f9f9f9; border:1px solid #ececec; border-radius:12px;">
                <div style="display:flex; align-items:center; gap:10px;">
                  @if($rp->user->avatar_path)
                    <img
                      src="{{ \Illuminate\Support\Facades\Storage::url($rp->user->avatar_path) }}"
                      alt=""
                      style="width:34px; height:34px; border-radius:999px; object-fit:cover; border:1px solid #eee;"
                    >
                  @else
                    <div style="width:34px; height:34px; border-radius:999px; background:#e9e9e9; display:flex; align-items:center; justify-content:center; font-size:14px; color:#999;">
                      👤
                    </div>
                  @endif
                  <div>
                    <div style="font-weight:700; font-size:14px;">{{ $rp->user->name }}</div>
                    <div style="font-size:12px; color:#888;">{{ $rp->user->email }}</div>
                  </div>
                </div>

                @if($isOwner)
                  <form method="POST" action="{{ route('reservations.players.destroy', [$reservation, $rp->user]) }}" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn" style="font-size:12px; padding:6px 12px; color:#842029; border-color:#f1b9c0;">
                      Quitar
                    </button>
                  </form>
                @endif
              </div>
            @endforeach
          </div>
        @endif

        {{-- Add player form (owner only) --}}
        @if($isOwner)
          <form method="POST" action="{{ route('reservations.players.store', $reservation) }}" style="display:flex; gap:10px; align-items:flex-start; flex-wrap:wrap;">
            @csrf
            <div style="flex:1; min-width:200px;">
              <input
                type="text"
                name="search"
                placeholder="Nombre exacto o email del jugador"
                value="{{ old('search') }}"
                style="width:100%; padding:10px 14px; border:1px solid {{ $errors->has('search') ? '#f1b9c0' : '#ddd' }}; border-radius:10px; font-size:14px; font-family:inherit;"
              >
              @error('search')
                <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
              @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
              Agregar jugador
            </button>
          </form>
        @endif
      </div>
    @endif
  </div>

  <style>
    @media (max-width: 900px) {
      div[style*="grid-template-columns:1.1fr .9fr"] {
        grid-template-columns: 1fr !important;
      }
    }
  </style>
@endsection
