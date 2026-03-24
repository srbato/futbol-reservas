<section class="pf-card" data-aos="fade-up" data-aos-delay="150">
    <div class="pf-card-header">
        <div class="pf-card-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div>
            <h2 class="pf-card-title">Seguridad</h2>
            <p class="pf-card-subtitle">Usá una contraseña segura para proteger tu cuenta</p>
        </div>
    </div>

    @if (session('status') === 'password-updated')
        <div class="pf-toast-success" style="margin-bottom:20px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Contraseña actualizada correctamente
        </div>
    @endif

    <form method="post" action="{{ route('password.update') }}" style="display:grid; gap:22px;">
        @csrf
        @method('put')

        {{-- Contraseña actual --}}
        <div>
            <label class="pf-label" for="update_password_current_password">Contraseña actual</label>
            <div class="pf-pass-wrap">
                <input
                    id="update_password_current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    class="pf-input"
                >
                <button type="button" class="pf-pass-toggle" onclick="pfTogglePass('update_password_current_password', this)" aria-label="Mostrar contraseña">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pf-eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pf-eye-off-icon" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            @error('current_password', 'updatePassword')
                <div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Nueva contraseña + barra fortaleza --}}
        <div>
            <label class="pf-label" for="update_password_password">Nueva contraseña</label>
            <div class="pf-pass-wrap">
                <input
                    id="update_password_password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    class="pf-input"
                    oninput="pfEvalStrength(this.value)"
                >
                <button type="button" class="pf-pass-toggle" onclick="pfTogglePass('update_password_password', this)" aria-label="Mostrar contraseña">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pf-eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pf-eye-off-icon" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            {{-- Barra de fortaleza --}}
            <div class="pf-strength-bar" id="pfStrengthBar">
                <div class="pf-strength-seg" id="pfSeg1"></div>
                <div class="pf-strength-seg" id="pfSeg2"></div>
                <div class="pf-strength-seg" id="pfSeg3"></div>
                <div class="pf-strength-seg" id="pfSeg4"></div>
            </div>
            <div class="pf-strength-label" id="pfStrengthLabel"></div>
            @error('password', 'updatePassword')
                <div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <div>
            <label class="pf-label" for="update_password_password_confirmation">Confirmar nueva contraseña</label>
            <div class="pf-pass-wrap">
                <input
                    id="update_password_password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="pf-input"
                >
                <button type="button" class="pf-pass-toggle" onclick="pfTogglePass('update_password_password_confirmation', this)" aria-label="Mostrar contraseña">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pf-eye-icon"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pf-eye-off-icon" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            @error('password_confirmation', 'updatePassword')
                <div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <button type="submit" class="pf-btn-save">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Actualizar contraseña
            </button>
        </div>
    </form>
</section>

<script>
function pfTogglePass(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    var eyeIcon    = btn.querySelector('.pf-eye-icon');
    var eyeOffIcon = btn.querySelector('.pf-eye-off-icon');
    if (eyeIcon)    eyeIcon.style.display    = isPassword ? 'none'  : '';
    if (eyeOffIcon) eyeOffIcon.style.display = isPassword ? ''      : 'none';
}

function pfEvalStrength(val) {
    var segs  = [
        document.getElementById('pfSeg1'),
        document.getElementById('pfSeg2'),
        document.getElementById('pfSeg3'),
        document.getElementById('pfSeg4'),
    ];
    var label = document.getElementById('pfStrengthLabel');
    if (!segs[0] || !label) return;

    var level = 0;
    var text  = '';
    var color = '#e5e7eb';

    if (val.length === 0) {
        level = 0;
    } else if (val.length <= 5) {
        level = 1; text = 'Debil'; color = '#ef4444';
    } else if (val.length <= 8) {
        level = 2; text = 'Regular'; color = '#f97316';
    } else if (val.length <= 11) {
        level = 3; text = 'Buena'; color = '#eab308';
    } else {
        var hasUpper  = /[A-Z]/.test(val);
        var hasNum    = /[0-9]/.test(val);
        var hasSym    = /[^A-Za-z0-9]/.test(val);
        if (hasUpper && hasNum && hasSym) {
            level = 4; text = 'Fuerte'; color = '#22c55e';
        } else {
            level = 3; text = 'Buena'; color = '#eab308';
        }
    }

    var segColors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
    segs.forEach(function (seg, i) {
        seg.style.background = i < level ? segColors[level - 1] : '#e5e7eb';
    });

    label.textContent = text;
    label.style.color = level > 0 ? color : '#9ca3af';
}
</script>
