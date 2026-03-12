<section>
    <p class="muted" style="margin:0 0 18px 0;">
        Una vez eliminada tu cuenta, se borrarán permanentemente tus datos personales asociados.
    </p>

    <button
        type="button"
        class="btn"
        onclick="document.getElementById('deleteAccountModal').style.display='block'"
        style="border-color:#f1b9c0; color:#842029;"
    >
        Eliminar cuenta
    </button>

    <div id="deleteAccountModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:100;">
        <div style="background:#fff; padding:20px; max-width:460px; margin:10% auto; border-radius:18px; box-shadow:0 12px 40px rgba(0,0,0,.18);">
            <h3 style="margin-top:0; color:#842029;">Eliminar cuenta</h3>

            <p class="muted" style="margin-bottom:16px;">
                Esta acción no se puede deshacer. Ingresá tu contraseña para confirmar.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}" style="display:grid; gap:14px;">
                @csrf
                @method('delete')

                <div>
                    <label for="delete_password" style="display:block; font-size:13px; color:#666; margin-bottom:6px;">Contraseña</label>
                    <input
                        id="delete_password"
                        name="password"
                        type="password"
                        placeholder="Tu contraseña"
                        style="width:100%; padding:11px 12px; border:1px solid #ddd; border-radius:14px; background:#fff;"
                    >
                    @error('password', 'userDeletion')
                        <div style="color:#842029; font-size:13px; margin-top:6px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                    <button
                        type="button"
                        class="btn"
                        onclick="document.getElementById('deleteAccountModal').style.display='none'"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn"
                        style="background:#842029; color:#fff; border-color:#842029;"
                    >
                        Sí, eliminar cuenta
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
