<section>
    <p class="muted" style="margin:0 0 18px 0;">
        Usá una contraseña segura para proteger tu cuenta.
    </p>

    <form method="post" action="{{ route('password.update') }}" style="display:grid; gap:14px;">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" style="display:block; font-size:13px; color:#666; margin-bottom:6px;">Contraseña actual</label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                style="width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:14px; background:#fff;"
            >
            @error('current_password', 'updatePassword')
                <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="update_password_password" style="display:block; font-size:13px; color:#666; margin-bottom:6px;">Nueva contraseña</label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                autocomplete="new-password"
                style="width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:14px; background:#fff;"
            >
            @error('password', 'updatePassword')
                <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="update_password_password_confirmation" style="display:block; font-size:13px; color:#666; margin-bottom:6px;">Confirmar nueva contraseña</label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                style="width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:14px; background:#fff;"
            >
            @error('password_confirmation', 'updatePassword')
                <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; padding-top:6px;">
            <button type="submit" class="btn btn-primary">Actualizar contraseña</button>

            @if (session('status') === 'password-updated')
                <p style="margin:0; color:#157347; font-size:14px;">Guardado.</p>
            @endif
        </div>
    </form>
</section>
