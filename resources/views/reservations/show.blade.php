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
          <p class="muted" style="font-size:14px; margin-bottom:18px;">Todavía no hay jugadores agregados.</p>
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
