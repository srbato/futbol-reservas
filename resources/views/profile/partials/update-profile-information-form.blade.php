<section>
    <p class="muted" style="margin:0 0 18px 0;">
        Editá tu nombre, tu correo electrónico y tu foto de perfil.
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" style="display:grid; gap:14px;"
        enctype="multipart/form-data">
        @csrf
        @method('POST')

        {{-- Avatar --}}
        <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
            @if(auth()->user()->avatar_path)
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar_path) }}"
                    alt="Avatar"
                    style="width:72px; height:72px; border-radius:999px; object-fit:cover; border:2px solid #eee;"
                >
            @else
                <div style="width:72px; height:72px; border-radius:999px; background:#f1f1f1; display:flex; align-items:center; justify-content:center; font-size:28px; color:#999; border:2px solid #eee;">
                    👤
                </div>
            @endif

            <div>
                <label style="display:block; font-size:13px; color:#666; margin-bottom:6px;">Foto de perfil</label>
                <input type="file" name="avatar" accept="image/*">
                <div style="font-size:12px; color:#999; margin-top:4px;">JPG, PNG o WebP. Máx. 2MB.</div>
            </div>
        </div>

        <div>
            <label for="name" style="display:block; font-size:13px; color:#666; margin-bottom:6px;">Nombre</label>
            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                style="width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:14px; background:#fff;"
            >
            @error('name', 'updateProfileInformation')
                <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="email" style="display:block; font-size:13px; color:#666; margin-bottom:6px;">Email</label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                style="width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:14px; background:#fff;"
            >
            @error('email', 'updateProfileInformation')
                <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="page-card" style="padding:14px; margin-top:4px;">
                <p class="muted" style="margin:0 0 10px 0;">
                    Tu dirección de correo no está verificada.
                </p>

                <button form="send-verification" class="btn" type="submit">
                    Reenviar email de verificación
                </button>

                @if (session('status') === 'verification-link-sent')
                    <p style="margin:10px 0 0 0; color:#157347; font-size:14px;">
                        Se envió un nuevo enlace de verificación a tu correo.
                    </p>
                @endif
            </div>
        @endif

        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; padding-top:6px;">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>

            @if (session('status') === 'profile-updated')
                <p style="margin:0; color:#157347; font-size:14px;">Guardado.</p>
            @endif
        </div>
    </form>
</section>