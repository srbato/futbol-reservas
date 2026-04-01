@extends('va.onboarding.layout')

@section('onboarding_content')

<div style="background:#fff; border-radius:18px; border:1px solid #ececec; padding:28px 24px; box-shadow:0 2px 12px rgba(0,0,0,.03);">
    <div style="margin-bottom:24px;">
        <h2 style="margin:0 0 6px; font-size:20px; font-weight:800; color:#111;">Horarios de atención</h2>
        <p style="margin:0; font-size:14px; color:#888;">Definí el horario general y la duración de los turnos. Podés personalizar cada día si necesitás.</p>
    </div>

    <form method="POST" action="{{ route('va.onboarding.store_schedule') }}" id="scheduleForm">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; margin-bottom:22px;">
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Apertura *</label>
                <input type="time" name="open_time" value="{{ old('open_time', '08:00') }}" required
                       style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none;">
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Cierre *</label>
                <input type="time" name="close_time" value="{{ old('close_time', '23:00') }}" required
                       style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none;">
            </div>
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#333; margin-bottom:5px;">Duración turno *</label>
                <select name="slot_minutes" required
                        style="width:100%; padding:10px 14px; border-radius:12px; border:1px solid #ddd; font-size:14px; font-family:inherit; outline:none; background:#fff;">
                    <option value="30" {{ (int) old('slot_minutes') === 30 ? 'selected' : '' }}>30 min</option>
                    <option value="60" {{ (int) old('slot_minutes', 60) === 60 ? 'selected' : '' }}>1 hora</option>
                    <option value="90" {{ (int) old('slot_minutes') === 90 ? 'selected' : '' }}>1h 30m</option>
                    <option value="120" {{ (int) old('slot_minutes') === 120 ? 'selected' : '' }}>2 horas</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom:16px;">
            <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                <input type="checkbox" id="customizeDays" onchange="toggleDayCustomization()"
                       style="width:18px; height:18px; accent-color:#22c55e;">
                <span style="font-size:14px; font-weight:600; color:#333;">Personalizar horario por día</span>
            </label>
        </div>

        @php
            $dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        @endphp

        <div id="daysContainer" style="display:none; margin-bottom:22px;">
            @foreach($dayNames as $dow => $dayName)
                <div style="display:flex; align-items:center; gap:10px; padding:10px 12px; background:#f8f8f8; border-radius:12px; border:1px solid #f0f0f0; margin-bottom:6px; flex-wrap:wrap;">
                    <span style="font-size:14px; font-weight:600; color:#333; min-width:90px;">{{ $dayName }}</span>
                    <div id="dayInputs{{ $dow }}" style="display:flex; align-items:center; gap:6px; flex:1; min-width:200px;">
                        <input type="time" name="days[{{ $dow }}][open_time]" id="dayOpen{{ $dow }}"
                               style="flex:1; padding:8px 10px; border-radius:8px; border:1px solid #ddd; font-size:13px; font-family:inherit; outline:none;">
                        <span style="color:#aaa; font-size:13px;">a</span>
                        <input type="time" name="days[{{ $dow }}][close_time]" id="dayClose{{ $dow }}"
                               style="flex:1; padding:8px 10px; border-radius:8px; border:1px solid #ddd; font-size:13px; font-family:inherit; outline:none;">
                    </div>
                    <label style="display:flex; align-items:center; gap:4px; cursor:pointer; flex-shrink:0;">
                        <input type="checkbox" name="days[{{ $dow }}][is_closed]"
                               onchange="toggleDayClosed({{ $dow }})"
                               style="width:16px; height:16px; accent-color:#ef4444;">
                        <span style="font-size:12px; color:#888;">Cerrado</span>
                    </label>
                </div>
            @endforeach
        </div>

        <button type="submit"
                style="display:inline-block; padding:12px 32px; border-radius:12px; background:#111; color:#fff; font-size:14px; font-weight:700; border:none; cursor:pointer; font-family:inherit;">
            Siguiente paso →
        </button>
    </form>
</div>

<script>
    function toggleDayCustomization() {
        const container = document.getElementById('daysContainer');
        const isChecked = document.getElementById('customizeDays').checked;
        container.style.display = isChecked ? 'block' : 'none';

        if (isChecked) {
            const openTime = document.querySelector('[name="open_time"]').value;
            const closeTime = document.querySelector('[name="close_time"]').value;
            for (let i = 0; i < 7; i++) {
                const dayOpen = document.getElementById('dayOpen' + i);
                const dayClose = document.getElementById('dayClose' + i);
                if (dayOpen && !dayOpen.value) dayOpen.value = openTime;
                if (dayClose && !dayClose.value) dayClose.value = closeTime;
            }
        }
    }

    function toggleDayClosed(dow) {
        const cb = document.querySelector(`input[name="days[${dow}][is_closed]"]`);
        const inputs = document.getElementById('dayInputs' + dow);
        if (inputs) {
            inputs.style.opacity = cb.checked ? '0.3' : '1';
            inputs.style.pointerEvents = cb.checked ? 'none' : 'auto';
        }
    }
</script>

@endsection
