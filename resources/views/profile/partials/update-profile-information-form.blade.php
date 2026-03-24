<section class="pf-card">
    <div class="pf-card-header">
        <div class="pf-card-icon-wrap">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
            <h2 class="pf-card-title">Información personal</h2>
            <p class="pf-card-subtitle">Actualizá tu nombre, email y foto de perfil</p>
        </div>
    </div>

    {{-- Toast éxito --}}
    @if (session('status') === 'profile-updated')
        <div class="pf-toast-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            Perfil actualizado correctamente
        </div>
    @endif

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data"
          style="display:grid; gap:22px;">
        @csrf
        @method('POST')

        {{-- Avatar --}}
        <div style="text-align:center;">
            <div class="pf-avatar-wrap">
                @if(auth()->user()->avatar_path)
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar_path) }}"
                        alt="Avatar"
                        class="pf-avatar-img"
                    >
                @else
                    <div class="pf-avatar-initial">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif

                <div class="pf-avatar-overlay">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <span>Cambiar foto</span>
                </div>

                <label class="pf-avatar-file-label" for="pf-avatar-input"></label>
            </div>

            <input
                type="file"
                name="avatar"
                id="pf-avatar-input"
                accept="image/*"
                class="pf-avatar-file-input"
                onchange="pfPreviewAvatar(this)"
            >
            <p style="font-size:12px; color:#9ca3af; margin:8px 0 0 0;">JPG, PNG o WebP. Max. 2MB.</p>
        </div>

        {{-- Nombre --}}
        <div>
            <label class="pf-label" for="name">Nombre</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="pf-input"
            >
            @error('name', 'updateProfileInformation')
                <div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="pf-label" for="email">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="pf-input"
            >
            @error('email', 'updateProfileInformation')
                <div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email no verificado --}}
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div style="background:#fef9c3; border:1px solid #fde68a; border-radius:14px; padding:14px 16px;">
                <p style="margin:0 0 10px 0; font-size:14px; color:#854d0e; font-weight:600;">
                    Tu dirección de correo no está verificada.
                </p>
                <button form="send-verification" type="submit" class="pf-btn-save" style="padding:9px 20px; font-size:13px;">
                    Reenviar verificación
                </button>
                @if (session('status') === 'verification-link-sent')
                    <p style="margin:10px 0 0 0; color:#15803d; font-size:13px; font-weight:600;">
                        Se envio un nuevo enlace a tu correo.
                    </p>
                @endif
            </div>
        @endif

        <div>
            <button type="submit" class="pf-btn-save">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar cambios
            </button>
        </div>
    </form>
</section>

<script>
function pfPreviewAvatar(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var wrap = input.closest('.pf-avatar-wrap') || document.querySelector('.pf-avatar-wrap');
            if (!wrap) return;
            var img = wrap.querySelector('.pf-avatar-img');
            var initial = wrap.querySelector('.pf-avatar-initial');
            if (img) {
                img.src = e.target.result;
            } else if (initial) {
                var newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.className = 'pf-avatar-img';
                newImg.alt = 'Avatar';
                initial.replaceWith(newImg);
            }
            // también actualizar el hero si existe
            var heroAvatar = document.querySelector('.pf-hero .pf-avatar-hero');
            if (heroAvatar && heroAvatar.tagName === 'IMG') {
                heroAvatar.src = e.target.result;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
