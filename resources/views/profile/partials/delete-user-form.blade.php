<section class="pf-card pf-card-danger" data-aos="fade-up" data-aos-delay="220">
    <div class="pf-card-header" style="border-bottom-color:#fecaca;">
        <div class="pf-card-icon-wrap" style="background:#fee2e2;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div>
            <h2 class="pf-card-title" style="color:#dc2626;">Zona peligrosa</h2>
            <p class="pf-card-subtitle">Esta accion es permanente e irreversible.</p>
        </div>
    </div>

    <p style="font-size:14px; color:#6b7280; margin:0 0 20px 0; line-height:1.6;">
        Una vez que elimines tu cuenta, todos tus datos personales, reservas y perfiles deportivos asociados se borrarán permanentemente. No podrás recuperarlos.
    </p>

    <button
        type="button"
        class="pf-btn-danger"
        onclick="document.getElementById('pfDeleteModal').classList.add('pf-modal-open')"
    >
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        Eliminar cuenta
    </button>
</section>

{{-- Modal eliminación --}}
<div id="pfDeleteModal" class="pf-modal-overlay" onclick="pfCloseModalIfOutside(event)">
    <div class="pf-modal-card">
        <div class="pf-modal-header">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <h3 class="pf-modal-title">Estas seguro?</h3>
            <p class="pf-modal-sub">Esta accion no puede deshacerse. Tu cuenta y todos tus datos se eliminaran de forma permanente.</p>
        </div>

        <div class="pf-modal-body">
            <form method="post" action="{{ route('profile.destroy') }}" style="display:grid; gap:20px;">
                @csrf
                @method('delete')

                <div>
                    <label class="pf-label" for="delete_password">Confirma tu contraseña para continuar</label>
                    <input
                        id="delete_password"
                        name="password"
                        type="password"
                        placeholder="Tu contraseña actual"
                        class="pf-input"
                    >
                    @error('password', 'userDeletion')
                        <div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                    <button
                        type="button"
                        class="pf-btn-cancel"
                        onclick="document.getElementById('pfDeleteModal').classList.remove('pf-modal-open')"
                    >
                        Cancelar
                    </button>

                    <button type="submit" class="pf-btn-destroy">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                        Eliminar definitivamente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function pfCloseModalIfOutside(event) {
    var overlay = document.getElementById('pfDeleteModal');
    if (event.target === overlay) {
        overlay.classList.remove('pf-modal-open');
    }
}
</script>
