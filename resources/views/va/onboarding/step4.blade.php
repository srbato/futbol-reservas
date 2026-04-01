@extends('va.onboarding.layout')

@section('onboarding_content')

<div style="background:#fff; border-radius:18px; border:1px solid #ececec; padding:28px 24px; box-shadow:0 2px 12px rgba(0,0,0,.03);">
    <div style="text-align:center; margin-bottom:28px;">
        <div style="width:64px; height:64px; border-radius:50%; background:#dcfce7; display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
            <svg width="32" height="32" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h2 style="margin:0 0 6px; font-size:22px; font-weight:800; color:#111;">¡Todo listo!</h2>
        <p style="margin:0; font-size:14px; color:#888;">Tu complejo está configurado y listo para recibir reservas.</p>
    </div>

    {{-- Resumen --}}
    <div style="display:flex; flex-direction:column; gap:14px; margin-bottom:28px;">
        @if($venue)
        <div style="padding:16px; border-radius:12px; background:#f0fdf4; border:1px solid #bbf7d0;">
            <div style="display:flex; align-items:flex-start; gap:12px;">
                <div style="width:32px; height:32px; border-radius:50%; background:#22c55e; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px;">
                    <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:700; color:#166534;">Complejo</div>
                    <div style="font-size:14px; color:#15803d; margin-top:2px;">{{ $venue->name }}</div>
                    @if($venue->address)
                        <div style="font-size:12px; color:#16a34a; margin-top:2px;">{{ $venue->address }}</div>
                    @endif
                    @if($venue->phone)
                        <div style="font-size:12px; color:#16a34a;">Tel: {{ $venue->phone }}</div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if($field)
        <div style="padding:16px; border-radius:12px; background:#f0fdf4; border:1px solid #bbf7d0;">
            <div style="display:flex; align-items:flex-start; gap:12px;">
                <div style="width:32px; height:32px; border-radius:50%; background:#22c55e; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px;">
                    <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <div style="font-size:14px; font-weight:700; color:#166534;">Cancha</div>
                    <div style="font-size:14px; color:#15803d; margin-top:2px;">{{ $field->name }}</div>
                    @php
                        $sportLabels = ['football' => 'Fútbol', 'padel' => 'Pádel', 'tennis' => 'Tenis', 'basketball' => 'Básquet', 'volleyball' => 'Vóley'];
                    @endphp
                    <div style="font-size:12px; color:#16a34a; margin-top:2px;">
                        {{ $sportLabels[$field->sport] ?? $field->sport }} · {{ $field->format }} vs {{ $field->format }}
                        @if($field->is_indoor) · Techada @endif
                    </div>
                    @if($field->price)
                        <div style="font-size:12px; color:#16a34a;">${{ number_format($field->price->price_per_slot, 0, ',', '.') }} {{ $field->price->currency }} por turno</div>
                    @endif
                </div>
            </div>
        </div>

        @if($field->schedules && $field->schedules->count() > 0)
        <div style="padding:16px; border-radius:12px; background:#f0fdf4; border:1px solid #bbf7d0;">
            <div style="display:flex; align-items:flex-start; gap:12px;">
                <div style="width:32px; height:32px; border-radius:50%; background:#22c55e; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px;">
                    <svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div style="flex:1;">
                    <div style="font-size:14px; font-weight:700; color:#166534;">Horarios</div>
                    <div style="font-size:12px; color:#16a34a; margin-top:2px;">Turnos de {{ $field->slot_minutes }} minutos</div>
                    @php
                        $dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                    @endphp
                    <div style="margin-top:6px;">
                        @foreach($field->schedules->sortBy('day_of_week') as $schedule)
                            <div style="font-size:12px; color:#16a34a;">
                                <span style="font-weight:600;">{{ $dayNames[$schedule->day_of_week] }}:</span>
                                {{ \Carbon\Carbon::parse($schedule->open_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->close_time)->format('H:i') }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>

    {{-- Tip --}}
    <div style="padding:16px 18px; border-radius:12px; background:#f8fafc; border:1px solid #e2e8f0; margin-bottom:24px;">
        <div style="font-size:14px; font-weight:700; color:#334155; margin-bottom:6px;">Completá tu perfil</div>
        <p style="margin:0; font-size:13px; color:#64748b; line-height:1.5;">
            Desde el panel podés editar tu complejo y tus canchas para agregar más detalles:
            amenities, fotos, política de cancelación, precios nocturnos, y más.
            Cuanto más completo esté tu perfil, más atractivo será para los jugadores.
        </p>
    </div>

    {{-- Botones --}}
    <form method="POST" action="{{ route('va.onboarding.complete') }}">
        @csrf
        <button type="submit"
                style="width:100%; padding:14px; border-radius:12px; background:#22c55e; color:#fff; font-size:15px; font-weight:700; border:none; cursor:pointer; font-family:inherit;">
            Ir al panel
        </button>
    </form>

    @if($venue)
    <div style="text-align:center; margin-top:12px;">
        <a href="{{ route('venues.show', $venue) }}" target="_blank"
           style="font-size:13px; color:#888; text-decoration:underline;">
            Ver mi complejo en la página pública →
        </a>
    </div>
    @endif
</div>

@endsection
