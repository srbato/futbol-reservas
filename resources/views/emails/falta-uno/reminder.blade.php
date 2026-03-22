<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Último momento — Falta Uno</title>
</head>
<body style="font-family:Arial, Helvetica, sans-serif; background:#f7f7f8; color:#111; padding:24px; margin:0;">
  <div style="max-width:600px; margin:0 auto; background:#fff; border:1px solid #ececec; border-radius:16px; overflow:hidden;">

    {{-- Header urgente --}}
    <div style="background:linear-gradient(135deg,#dc2626 0%,#b91c1c 100%); padding:28px 28px 24px; text-align:center;">
      <div style="font-size:36px; margin-bottom:8px;">⏰</div>
      <h1 style="margin:0; color:#fff; font-size:22px; font-weight:800; letter-spacing:-.02em;">¡Último momento!</h1>
      <p style="margin:6px 0 0; color:rgba(255,255,255,.8); font-size:14px;">
        Un partido de
        <strong style="color:#fff;">
          {{ match($game->field->sport ?? '') {
            'football'   => 'Fútbol',
            'padel'      => 'Pádel',
            'tennis'     => 'Tenis',
            'basketball' => 'Básquet',
            'volleyball' => 'Vóley',
            default      => ucfirst($game->field->sport ?? 'deporte'),
          } }}
        </strong>
        está por quedarse sin jugadores
      </p>
    </div>

    <div style="padding:28px;">

      {{-- Saludo --}}
      <p style="font-size:15px; margin:0 0 20px;">
        Hola <strong>{{ $recipient->name }}</strong>, hay un partido que encaja con tu perfil y todavía tiene lugar.
      </p>

      {{-- Faltan X jugadores --}}
      <div style="background:linear-gradient(135deg,#fef2f2,#fee2e2); border:1px solid #fecaca; border-radius:12px; padding:16px 20px; margin-bottom:24px; text-align:center;">
        <div style="font-size:40px; font-weight:800; color:#dc2626; letter-spacing:-.03em;">
          {{ $game->remainingSlots() }}
        </div>
        <div style="color:#991b1b; font-size:14px; font-weight:700;">
          {{ $game->remainingSlots() === 1 ? 'lugar disponible' : 'lugares disponibles' }}
        </div>
      </div>

      {{-- Cuándo --}}
      @php
        $deadline = $game->field->faltaUnoSetting
            ? $game->start_at->copy()->subMinutes($game->field->faltaUnoSetting->fill_deadline_minutes)
            : null;
        $minutesLeft = $deadline ? (int) now()->diffInMinutes($deadline, false) : null;
      @endphp

      @if($minutesLeft !== null && $minutesLeft > 0)
      <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:12px 16px; margin-bottom:20px; text-align:center; font-size:13px; color:#92400e; font-weight:700;">
        ⚡ Quedan aprox. {{ $minutesLeft }} minutos para que cierre el cupo
      </div>
      @endif

      {{-- Detalles del partido --}}
      <h2 style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#aaa; margin:0 0 14px;">
        Detalles del partido
      </h2>

      <table style="width:100%; border-collapse:collapse;">
        <tr>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; color:#666; font-size:13px; width:40%;">Complejo</td>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; font-weight:700; font-size:13px;">{{ $game->field->venue->name }}</td>
        </tr>
        <tr>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; color:#666; font-size:13px;">Cancha</td>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; font-weight:700; font-size:13px;">{{ $game->field->name }}</td>
        </tr>
        @if($game->field->venue->address)
        <tr>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; color:#666; font-size:13px;">Dirección</td>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; font-weight:700; font-size:13px;">{{ $game->field->venue->address }}</td>
        </tr>
        @endif
        <tr>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; color:#666; font-size:13px;">Fecha</td>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; font-weight:700; font-size:13px;">
            {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y') }}
          </td>
        </tr>
        <tr>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; color:#666; font-size:13px;">Hora</td>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; font-weight:700; font-size:13px;">
            {{ \Carbon\Carbon::parse($game->start_at)->format('H:i') }} hs
          </td>
        </tr>
        <tr>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; color:#666; font-size:13px;">Jugadores totales</td>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; font-weight:700; font-size:13px;">{{ $game->total_players }}</td>
        </tr>
        @if($game->category_min || $game->category_max)
        <tr>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; color:#666; font-size:13px;">Categoría</td>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; font-weight:700; font-size:13px; text-transform:capitalize;">
            @if($game->category_min && $game->category_max && $game->category_min === $game->category_max)
              {{ ucfirst($game->category_min) }}
            @elseif($game->category_min && $game->category_max)
              {{ ucfirst($game->category_min) }} – {{ ucfirst($game->category_max) }}
            @elseif($game->category_min)
              Desde {{ ucfirst($game->category_min) }}
            @else
              Hasta {{ ucfirst($game->category_max) }}
            @endif
          </td>
        </tr>
        @endif
        @if($game->gender_filter !== 'mixed')
        <tr>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; color:#666; font-size:13px;">Género</td>
          <td style="padding:8px 0; border-bottom:1px solid #f4f4f4; font-weight:700; font-size:13px;">
            {{ $game->gender_filter === 'male' ? 'Masculino' : 'Femenino' }}
          </td>
        </tr>
        @endif
      </table>

      {{-- CTA --}}
      <div style="text-align:center; margin-top:28px;">
        <a href="{{ route('falta-uno.index') }}"
           style="display:inline-block; background:#dc2626; color:#fff; text-decoration:none; font-weight:700; font-size:15px; padding:14px 32px; border-radius:12px; letter-spacing:-.01em;">
          ¡Me apunto! &rarr;
        </a>
      </div>

      <hr style="border:none; border-top:1px solid #eee; margin:28px 0 18px;">

      <p style="margin:0; font-size:12px; color:#bbb; text-align:center; line-height:1.6;">
        Recibís este mail porque tenés un perfil deportivo de
        {{ match($game->field->sport ?? '') {
          'football'   => 'Fútbol',
          'padel'      => 'Pádel',
          'tennis'     => 'Tenis',
          'basketball' => 'Básquet',
          'volleyball' => 'Vóley',
          default      => ucfirst($game->field->sport ?? 'deporte'),
        } }}
        en TuCancha y tu categoría está dentro del rango requerido por el partido.
      </p>
    </div>
  </div>
</body>
</html>
