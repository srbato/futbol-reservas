@extends('layouts.app')

@section('title', 'Mis referidos')

@section('content')

  <div style="margin-bottom:24px;">
    <h1 style="margin:0 0 8px 0; font-size:36px; font-weight:800; letter-spacing:-0.02em;">Mis referidos</h1>
    <p class="muted" style="margin:0; font-size:16px; line-height:1.6;">
      Compartí tu código, invitá amigos y ganá recompensas cada vez que activen su membresía.
    </p>
  </div>

  {{-- ── Referral code card ────────────────────────────────────── --}}
  <div class="page-card" style="margin-bottom:20px; padding:28px;">
    <div style="display:grid; grid-template-columns:1fr auto; gap:20px; align-items:center; flex-wrap:wrap;">
      <div>
        <div style="font-size:12px; color:#a0a0a0; text-transform:uppercase; letter-spacing:.06em; font-weight:700; margin-bottom:10px;">
          Tu código de referido
        </div>
        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
          <div id="referralCodeText"
               style="font-family:monospace; font-size:32px; font-weight:800; letter-spacing:.08em; color:#e8e8e8; background:#0a0a0a; border:2px dashed rgba(255,255,255,.15); border-radius:12px; padding:14px 24px; user-select:all;">
            {{ $referralCode->code }}
          </div>
          <button
            onclick="copyReferralCode()"
            id="copyCodeBtn"
            class="btn btn-primary"
            style="font-size:14px; padding:12px 20px;"
          >
            Copiar código
          </button>
        </div>
        <div class="muted" style="margin-top:12px; font-size:13px; line-height:1.6;">
          Compartí este código con tus amigos. Cuando activen una membresía usándolo, ¡te ganás una recompensa!
        </div>
      </div>

      @if($availableCount > 0)
        <div style="text-align:center; min-width:120px;">
          <div style="font-size:48px; font-weight:800; color:#6ee7a0; line-height:1;">
            {{ $availableCount }}
          </div>
          <div style="font-size:13px; color:#6ee7a0; font-weight:700; margin-top:4px;">
            {{ $availableCount === 1 ? 'recompensa disponible' : 'recompensas disponibles' }}
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- ── How it works ──────────────────────────────────────────── --}}
  <div class="page-card" style="margin-bottom:20px; padding:28px;">
    <h2 style="margin:0 0 20px 0; font-size:22px; font-weight:800; letter-spacing:-0.01em;">¿Cómo funciona?</h2>
    <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:16px;">

      <div style="text-align:center; padding:20px 16px; background:rgba(255,255,255,.04); border-radius:14px; border:1px solid rgba(255,255,255,.08);">
        <div style="font-size:36px; margin-bottom:12px;">📤</div>
        <div style="font-weight:700; font-size:15px; margin-bottom:6px;">1. Compartí tu código</div>
        <div class="muted" style="font-size:13px; line-height:1.6;">
          Enviá tu código único a amigos o compañeros que quieran administrar su complejo en TuCancha.
        </div>
      </div>

      <div style="text-align:center; padding:20px 16px; background:rgba(255,255,255,.04); border-radius:14px; border:1px solid rgba(255,255,255,.08);">
        <div style="font-size:36px; margin-bottom:12px;">🤝</div>
        <div style="font-weight:700; font-size:15px; margin-bottom:6px;">2. Tu amigo se suscribe</div>
        <div class="muted" style="font-size:13px; line-height:1.6;">
          Cuando activen su membresía usando tu código, el sistema lo registra automáticamente.
        </div>
      </div>

      <div style="text-align:center; padding:20px 16px; background:rgba(34,197,94,.1); border-radius:14px; border:2px solid rgba(34,197,94,.3);">
        <div style="font-size:36px; margin-bottom:12px;">🎁</div>
        <div style="font-weight:700; font-size:15px; margin-bottom:6px; color:#6ee7a0;">3. ¡Ganás una recompensa!</div>
        <div style="font-size:13px; line-height:1.6; color:#6ee7a0;">
          Recibís una <strong>reserva gratis</strong> o un <strong>mes gratis</strong> de membresía para canjear cuando quieras.
        </div>
      </div>

    </div>
  </div>

  {{-- ── Rewards table ─────────────────────────────────────────── --}}
  <div class="page-card" style="padding:28px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
      <div>
        <h2 style="margin:0 0 4px 0; font-size:22px; font-weight:800; letter-spacing:-0.01em;">Historial de recompensas</h2>
        <div class="muted" style="font-size:13px;">
          Cada vez que uno de tus referidos activa su membresía, aparece aquí.
        </div>
      </div>
      @if($availableCount > 0)
        <span class="badge" style="background:rgba(34,197,94,.1); color:#6ee7a0; border:1px solid rgba(34,197,94,.2); font-size:13px; padding:8px 14px;">
          {{ $availableCount }} disponible{{ $availableCount > 1 ? 's' : '' }}
        </span>
      @endif
    </div>

    @if($rewards->isEmpty())
      <div style="padding:32px; text-align:center; background:rgba(255,255,255,.04); border-radius:14px; border:1px dashed rgba(255,255,255,.1);">
        <div style="font-size:36px; margin-bottom:12px;">📭</div>
        <div style="font-weight:700; font-size:16px; margin-bottom:6px;">Todavía no tenés referidos</div>
        <div class="muted" style="font-size:14px; line-height:1.6;">
          Compartí tu código y empezá a ganar recompensas.
        </div>
      </div>
    @else
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:640px;">
          <thead>
            <tr style="background:rgba(255,255,255,.02);">
              <th style="text-align:left; padding:12px 14px; border-bottom:1px solid rgba(255,255,255,.08); font-size:13px; color:#a0a0a0; font-weight:700;">Referido</th>
              <th style="text-align:left; padding:12px 14px; border-bottom:1px solid rgba(255,255,255,.08); font-size:13px; color:#a0a0a0; font-weight:700;">Fecha</th>
              <th style="text-align:left; padding:12px 14px; border-bottom:1px solid rgba(255,255,255,.08); font-size:13px; color:#a0a0a0; font-weight:700;">Estado</th>
              <th style="text-align:left; padding:12px 14px; border-bottom:1px solid rgba(255,255,255,.08); font-size:13px; color:#a0a0a0; font-weight:700;">Tipo</th>
              <th style="text-align:left; padding:12px 14px; border-bottom:1px solid rgba(255,255,255,.08); font-size:13px; color:#a0a0a0; font-weight:700;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rewards as $reward)
              <tr>
                <td style="padding:14px; border-bottom:1px solid rgba(255,255,255,.06);">
                  <div style="font-weight:700; font-size:14px;">{{ $reward->referred->name }}</div>
                  <div class="muted" style="font-size:12px;">{{ $reward->referred->email }}</div>
                </td>

                <td style="padding:14px; border-bottom:1px solid rgba(255,255,255,.06);">
                  <div style="font-size:14px;">{{ $reward->created_at->format('d/m/Y') }}</div>
                  <div class="muted" style="font-size:12px;">{{ $reward->created_at->format('H:i') }}</div>
                </td>

                <td style="padding:14px; border-bottom:1px solid rgba(255,255,255,.06);">
                  @if($reward->status === 'available')
                    <span class="badge" style="background:rgba(34,197,94,.1); color:#6ee7a0; border:1px solid rgba(34,197,94,.2);">
                      Disponible
                    </span>
                  @else
                    <span class="badge" style="background:rgba(255,255,255,.04); color:#666; border:1px solid rgba(255,255,255,.08);">
                      Canjeada
                    </span>
                  @endif
                </td>

                <td style="padding:14px; border-bottom:1px solid rgba(255,255,255,.06);">
                  @if($reward->reward_type === 'free_reservation')
                    <span class="badge" style="background:rgba(59,130,246,.08); color:#93c5fd; border:1px solid rgba(59,130,246,.2);">
                      Reserva gratis
                    </span>
                  @elseif($reward->reward_type === 'free_month')
                    <span class="badge" style="background:rgba(139,92,246,.08); color:#c4b5fd; border:1px solid rgba(139,92,246,.2);">
                      Mes gratis
                    </span>
                  @else
                    <span class="muted" style="font-size:13px;">—</span>
                  @endif
                </td>

                <td style="padding:14px; border-bottom:1px solid rgba(255,255,255,.06);">
                  @if($reward->status === 'available')
                    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
                      <a href="{{ route('my_reservations') }}"
                         class="btn"
                         style="font-size:12px; padding:8px 12px;"
                         title="Ir a Mis reservas para elegir qué reserva pagar con esta recompensa">
                        Usar en reserva
                      </a>

                      @if(in_array(auth()->user()->role, ['venue_admin', 'super_admin']))
                        <form method="POST" action="{{ route('referral.redeem_month', $reward) }}" style="margin:0;"
                              onsubmit="return confirm('¿Querés canjear esta recompensa para extender tu membresía 30 días?')">
                          @csrf
                          <button type="submit" class="btn btn-primary" style="font-size:12px; padding:8px 12px;">
                            Canjear mes gratis
                          </button>
                        </form>
                      @endif
                    </div>
                  @else
                    <span class="muted" style="font-size:13px;">
                      Canjeada el {{ $reward->redeemed_at?->format('d/m/Y') ?? '—' }}
                    </span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  <style>
    @media (max-width: 700px) {
      div[style*="grid-template-columns:repeat(3, 1fr)"] {
        grid-template-columns: 1fr !important;
      }
      div[style*="grid-template-columns:1fr auto"] {
        grid-template-columns: 1fr !important;
      }
    }
  </style>

  <script>
    function copyReferralCode() {
      const code = '{{ $referralCode->code }}';
      const btn = document.getElementById('copyCodeBtn');

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(code).then(() => {
          btn.innerText = '¡Copiado!';
          btn.style.background = '#16a34a';
          btn.style.borderColor = '#157347';
          setTimeout(() => {
            btn.innerText = 'Copiar código';
            btn.style.background = '';
            btn.style.borderColor = '';
          }, 2000);
        }).catch(() => fallbackCopy(code, btn));
      } else {
        fallbackCopy(code, btn);
      }
    }

    function fallbackCopy(text, btn) {
      const ta = document.createElement('textarea');
      ta.value = text;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.focus();
      ta.select();
      try {
        document.execCommand('copy');
        btn.innerText = '¡Copiado!';
        setTimeout(() => { btn.innerText = 'Copiar código'; }, 2000);
      } catch (e) {}
      document.body.removeChild(ta);
    }
  </script>

@endsection
