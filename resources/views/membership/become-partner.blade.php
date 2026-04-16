@extends('layouts.app')

@section('title', 'Hacete socio')


@section('content')
  @php
    $latestStatus = $latestSubscription->status ?? null;
    $isExpired = !$activeSubscription
      && $latestSubscription
      && in_array($latestSubscription->status, ['ACTIVE', 'TRIAL', 'EXPIRED'])
      && $latestSubscription->expires_at
      && $latestSubscription->expires_at->isPast();

    $isTrialActive = $activeSubscription && $activeSubscription->status === 'TRIAL';
    $isTrialExpired = !$activeSubscription
      && $latestSubscription
      && in_array($latestSubscription->status, ['TRIAL', 'EXPIRED'])
      && $latestSubscription->expires_at
      && $latestSubscription->expires_at->isPast();

  @endphp

  @if(auth()->user()->isVenueStaff())
    <div class="page-card" style="margin-bottom:24px; padding:28px; background:rgba(245,158,11,.08); border-color:rgba(251,191,36,.25);">
      <div style="color:#fbbf24; font-weight:800; font-size:16px; margin-bottom:6px;">No disponible</div>
      <div style="color:#fbbf24; line-height:1.6;">
        Actualmente sos empleado de un complejo en TuCancha. Para contratar una membresía de dueño, primero pedile al dueño que te quite del staff.
      </div>
    </div>
  @else

  <div class="page-card" style="margin-bottom:24px; padding:28px;">
    <div style="display:flex; justify-content:space-between; gap:20px; flex-wrap:wrap; align-items:flex-start;">
      <div style="max-width:760px;">
        <h1 style="margin:0 0 12px 0; font-size:40px; letter-spacing:-0.02em;">
          Hacete socio de TuCancha
        </h1>

        <p class="muted" style="margin:0; line-height:1.7; font-size:16px;">
          Activá tu acceso como administrador de complejos y empezá a crear canchas, definir horarios,
          bloquear turnos, ver reservas, administrar descuentos y gestionar tu operación desde el panel admin.
        </p>
      </div>

      <div class="page-card" style="min-width:260px; padding:20px;">
        <div style="font-size:12px; color:#a0a0a0; margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; font-weight:700;">
          Plan {{ $plan->name }}
        </div>
        <div style="font-size:12px; color:#666; margin-bottom:8px;">
          Facturación {{ $billingCycle === 'annual' ? strtolower($plan->longTermLabel()) : 'mensual' }}
        </div>
        <div style="font-size:34px; font-weight:800; line-height:1.1;">
          ARS {{ number_format($price, 0, ',', '.') }}
        </div>
        <div class="muted" style="margin-top:8px; font-size:13px;">
          @if($billingCycle === 'annual')
            Pago único por {{ $plan->longTermMonths() }} meses de acceso.
          @else
            Acceso como socio por 30 días.
          @endif
        </div>
        <div style="margin-top:12px;">
          <a href="{{ route('planes') }}" style="font-size:13px; color:#a0a0a0; text-decoration:underline;">
            Cambiar plan
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="grid" style="grid-template-columns:1.1fr .9fr; gap:18px; margin-bottom:18px;">
    <div class="page-card">
      <h2 class="section-title" style="font-size:26px; margin-bottom:12px;">¿Qué incluye?</h2>

      <div style="display:grid; gap:12px;">
        <div class="page-card" style="padding:16px;">
          <strong>Panel admin</strong>
          <div class="muted" style="margin-top:6px;">Gestioná complejos, canchas, horarios y bloqueos.</div>
        </div>

        <div class="page-card" style="padding:16px;">
          <strong>Reservas y agenda</strong>
          <div class="muted" style="margin-top:6px;">Controlá reservas, check-ins y movimiento diario.</div>
        </div>

        <div class="page-card" style="padding:16px;">
          <strong>Descuentos y promociones</strong>
          <div class="muted" style="margin-top:6px;">Configurá precios promocionales por día y horario.</div>
        </div>
      </div>
    </div>

    <div class="page-card">
      <h2 class="section-title" style="font-size:26px; margin-bottom:12px;">Estado de tu membresía</h2>

      @if($activeSubscription)
        @if($isTrialActive)
          <div style="padding:16px; border-radius:16px; background:rgba(139,92,246,.08); color:#a78bfa; border:1px solid rgba(167,139,250,.25); margin-bottom:14px;">
            <strong><i data-lucide="gift" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;margin-right:4px;"></i> Período de prueba activo</strong>
            <div style="margin-top:6px;">
              Estás usando tu período de prueba gratuito. Tenés acceso hasta el
              <strong>{{ $activeSubscription->expires_at?->format('d/m/Y H:i') }}</strong>.
              Al vencer, deberás contratar un plan para seguir administrando complejos.
            </div>
          </div>

          <div class="muted" style="margin-bottom:14px; font-size:13px; line-height:1.6;">
            Plan <strong>{{ $activeSubscription->plan_slug ?? '—' }}</strong> · Prueba gratuita
          </div>

          <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <a href="{{ route('va.dashboard') }}" class="btn btn-primary">
              Ir al panel admin
            </a>

            <form method="POST" action="{{ route('membership.cancel_subscription') }}"
                  onsubmit="return confirm('¿Cancelar el período de prueba? Perderás el acceso al panel admin inmediatamente.')">
              @csrf
              <button type="submit" class="btn" style="color:#f87171; border-color:rgba(248,113,113,.3);">
                Cancelar prueba
              </button>
            </form>
          </div>
        @else
          <div style="padding:16px; border-radius:16px; background:rgba(34,197,94,.1); color:#6eeaa0; border:1px solid rgba(74,222,128,.25); margin-bottom:14px;">
            <strong>Membresía activa</strong>
            <div style="margin-top:6px;">
              Tenés acceso como socio hasta el
              <strong>{{ $activeSubscription->expires_at?->format('d/m/Y H:i') }}</strong>.
              @if($activeSubscription->mp_subscription_status !== 'cancelled' && $activeSubscription->mp_preapproval_id)
                El cobro se renueva automáticamente cada {{ $activeSubscription->billing_cycle === 'annual' ? ($activeSubscription->long_term_months === 6 ? '6 meses' : 'año') : 'mes' }}.
              @endif
            </div>
          </div>

          <div class="muted" style="margin-bottom:14px; font-size:13px; line-height:1.6;">
            Plan <strong>{{ $activeSubscription->plan_slug ?? '—' }}</strong> ·
            {{ $activeSubscription->billing_cycle === 'annual' ? ($activeSubscription->long_term_months === 6 ? 'Facturación semestral' : 'Facturación anual') : 'Facturación mensual' }} ·
            <strong>{{ $activeSubscription->currency }} {{ number_format((float) $activeSubscription->monthly_price, 0, ',', '.') }}</strong>
          </div>

          @if($activeSubscription->mp_subscription_status === 'cancelled')
            <div style="padding:12px 16px; border-radius:12px; background:rgba(245,158,11,.08); color:#fbbf24; border:1px solid rgba(251,191,36,.25); margin-bottom:14px; font-size:14px;">
              <i data-lucide="alert-triangle" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;margin-right:4px;"></i> Tu suscripción está cancelada. Seguirás teniendo acceso hasta el <strong>{{ $activeSubscription->expires_at?->format('d/m/Y') }}</strong>, sin cobros adicionales.
            </div>
          @endif

          <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
            <a href="{{ route('va.dashboard') }}" class="btn btn-primary">
              Ir al panel admin
            </a>

            @if($activeSubscription->mp_subscription_status !== 'cancelled')
              <form method="POST" action="{{ route('membership.cancel_subscription') }}"
                    onsubmit="return confirm('¿Cancelar la suscripción? Seguirás teniendo acceso hasta el fin del período actual, sin cobros futuros.')">
                @csrf
                <button type="submit" class="btn" style="color:#f87171; border-color:rgba(248,113,113,.3);">
                  Cancelar suscripción
                </button>
              </form>
            @endif
          </div>
        @endif
      @else
        @if($latestSubscription && $latestStatus === 'PENDING_PAYMENT')
          <div style="padding:16px; border-radius:16px; background:rgba(245,158,11,.08); color:#fbbf24; border:1px solid rgba(251,191,36,.25); margin-bottom:14px;">
            <strong>Pago pendiente</strong>
            <div style="margin-top:6px;">
              Hay una solicitud de membresía pendiente de pago. Si ya completaste el pago, esperá a que
              MercadoPago lo confirme. Si el intento quedó incompleto, podés cancelarlo y volver a intentar.
            </div>
          </div>

          <form method="POST" action="{{ route('membership.cancel_pending') }}" style="margin-bottom:14px;"
                onsubmit="return confirm('¿Cancelar el intento de pago pendiente? Podrás iniciar uno nuevo a continuación.')">
            @csrf
            <button type="submit" class="btn" style="font-size:14px; padding:9px 16px; border-color:rgba(255,255,255,.1); color:#f87171;">
              Cancelar intento y volver a intentar
            </button>
          </form>
        @elseif($isTrialExpired)
          <div style="padding:16px; border-radius:16px; background:rgba(229,57,53,.1); color:#f87171; border:1px solid rgba(248,113,113,.25); margin-bottom:14px;">
            <strong>Período de prueba vencido</strong>
            <div style="margin-top:6px;">
              Tu período de prueba gratuito venció el
              <strong>{{ $latestSubscription->expires_at?->format('d/m/Y H:i') }}</strong>.
              Contratá un plan para volver a administrar complejos.
            </div>
          </div>
        @elseif($isExpired)
          <div style="padding:16px; border-radius:16px; background:rgba(229,57,53,.1); color:#f87171; border:1px solid rgba(248,113,113,.25); margin-bottom:14px;">
            <strong>Membresía vencida</strong>
            <div style="margin-top:6px;">
              Tu último acceso como socio venció el
              <strong>{{ $latestSubscription->expires_at?->format('d/m/Y H:i') }}</strong>.
              Renová tu plan para volver a administrar complejos.
            </div>
          </div>
        @elseif($latestSubscription && $latestStatus === 'CANCELLED')
          <div style="padding:16px; border-radius:16px; background:rgba(229,57,53,.1); color:#f87171; border:1px solid rgba(248,113,113,.25); margin-bottom:14px;">
            <strong>Pago cancelado</strong>
            <div style="margin-top:6px;">
              Tu último intento de pago no se completó. Podés volver a intentarlo cuando quieras.
            </div>
          </div>
        @else
          <div style="padding:16px; border-radius:16px; background:rgba(255,255,255,.04); color:#a0a0a0; border:1px solid rgba(255,255,255,.08); margin-bottom:14px;">
            <strong>Sin membresía activa</strong>
            <div style="margin-top:6px;">
              Todavía no tenés acceso como socio. Activá tu plan para ingresar al panel admin.
            </div>
          </div>
        @endif

        @if($latestStatus !== 'PENDING_PAYMENT')

          @if($trialAvailable ?? false)
            {{-- Trial available: show a simple "start free trial" button, no payment required --}}
            <div style="background:rgba(139,92,246,.08); border:1px solid rgba(167,139,250,.25); border-radius:12px; padding:14px 16px; margin-bottom:16px; font-size:14px; color:#a78bfa; line-height:1.6;">
              <strong><i data-lucide="gift" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;margin-right:4px;"></i> {{ $plan->trial_days }} días gratis disponibles</strong><br>
              Activá el período de prueba <strong>sin tarjeta de crédito</strong>. Tenés {{ $plan->trial_days }} días de acceso completo al panel admin de forma gratuita.
              Al vencer, te avisamos por email para que contrates un plan si querés continuar.
            </div>

            <form method="POST" action="{{ route('membership.start_trial') }}">
              @csrf
              <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
              <button type="submit" class="btn btn-primary">
                Empezar {{ $plan->trial_days }} días gratis
              </button>
            </form>

            <div class="muted" style="margin-top:12px; font-size:13px; line-height:1.6;">
              Sin cargo. Sin tarjeta. Al vencer el período de prueba, podés contratar el plan
              <strong>{{ $plan->name }}</strong> por <strong>ARS {{ number_format($price, 0, ',', '.') }}</strong>
              ({{ $billingCycle === 'annual' ? strtolower($plan->longTermLabel()) : 'mensual' }}).
            </div>
          @else
            {{-- No trial: show normal MP checkout form --}}
            <form method="POST" action="{{ route('membership.checkout') }}" id="membershipCheckoutForm">
              @csrf
              <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
              <input type="hidden" name="billing_cycle" value="{{ $billingCycle }}">

              <div style="margin-bottom:16px;">
                <label style="display:block; font-size:13px; color:#a0a0a0; margin-bottom:6px;">
                  Email de tu cuenta MercadoPago <span style="color:#f87171;">*</span>
                </label>
                <input type="email" name="mp_email" required
                  placeholder="tucuenta@email.com"
                  value="{{ old('mp_email') }}"
                  style="padding:10px 14px; border:1px solid rgba(255,255,255,.1); border-radius:10px; font-size:14px; width:100%; max-width:320px; background:#0a0a0a; color:#e8e8e8;">
                <div style="font-size:12px; color:#666; margin-top:5px; line-height:1.5;">
                  Necesitamos el email de tu cuenta MercadoPago para procesar el cobro automático.
                  Puede ser diferente al email de tu cuenta en TuCancha.
                </div>
              </div>

              {{-- referral code hidden --}}

              <button type="submit" class="btn btn-primary" id="membershipPayButton">
                {{ $isExpired || $isTrialExpired ? 'Contratar plan ' . $plan->name : 'Activar plan ' . $plan->name }}
              </button>
            </form>

            <div class="muted" style="margin-top:12px; font-size:13px; line-height:1.6;">
              Precio: <strong>ARS {{ number_format($price, 0, ',', '.') }}</strong>
              ({{ $billingCycle === 'annual' ? strtolower($plan->longTermLabel()) : 'mensual' }}) · cobro automático vía MercadoPago.
            </div>
          @endif

          <div class="muted" style="margin-top:8px; font-size:13px; line-height:1.6;">
            Tu acceso como administrador depende de que la membresía esté activa.
          </div>
        @endif {{-- end @if($latestStatus !== 'PENDING_PAYMENT') --}}
      @endif
    </div>
  </div>

  <div class="page-card">
    <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:14px;">
      <div>
        <h2 class="section-title" style="font-size:26px; margin:0 0 6px 0;">Historial de membresías</h2>
        <div class="muted" style="font-size:14px;">
          Revisá tus pagos, períodos activos y estados anteriores.
        </div>
      </div>
    </div>

    @if(($subscriptionHistory ?? collect())->isEmpty())
      <div style="padding:16px; border-radius:16px; background:rgba(255,255,255,.04); color:#a0a0a0; border:1px solid rgba(255,255,255,.08);">
        Todavía no tenés movimientos de membresía registrados.
      </div>
    @else
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:980px;">
          <thead>
            <tr style="background:rgba(255,255,255,.04);">
              <th style="text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08);">ID</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08);">Estado</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08);">Monto</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08);">Proveedor</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08);">Pago externo</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08);">Inicio</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08);">Vencimiento</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid rgba(255,255,255,.08);">Creada</th>
            </tr>
          </thead>
          <tbody>
            @foreach($subscriptionHistory as $subscription)
              <tr>
                <td style="padding:12px; border-bottom:1px solid rgba(255,255,255,.06);">
                  {{ $subscription->id }}
                </td>

                <td style="padding:12px; border-bottom:1px solid rgba(255,255,255,.06);">
                  <span style="display:inline-flex; align-items:center; padding:7px 10px; border-radius:999px; font-size:12px; font-weight:700; {{ $subscription->statusStyles() }}">
                    {{ $subscription->statusLabel() }}
                  </span>
                </td>

                <td style="padding:12px; border-bottom:1px solid rgba(255,255,255,.06); font-weight:600;">
                  {{ $subscription->currency }} {{ number_format((float) $subscription->monthly_price, 2, ',', '.') }}
                </td>

                <td style="padding:12px; border-bottom:1px solid rgba(255,255,255,.06);">
                  {{ $subscription->payment_provider ?? '-' }}
                </td>

                <td style="padding:12px; border-bottom:1px solid rgba(255,255,255,.06);">
                  {{ $subscription->payment_external_id ?? '-' }}
                </td>

                <td style="padding:12px; border-bottom:1px solid rgba(255,255,255,.06);">
                  {{ $subscription->starts_at?->format('d/m/Y H:i') ?? '-' }}
                </td>

                <td style="padding:12px; border-bottom:1px solid rgba(255,255,255,.06);">
                  {{ $subscription->expires_at?->format('d/m/Y H:i') ?? '-' }}
                </td>

                <td style="padding:12px; border-bottom:1px solid rgba(255,255,255,.06);">
                  {{ $subscription->created_at?->format('d/m/Y H:i') ?? '-' }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>

  @endif {{-- isVenueStaff --}}

@endsection

@push('scripts')
<style>
  @keyframes mp-spin { to { transform: rotate(360deg); } }
  .mp-spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2.5px solid rgba(255,255,255,.35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: mp-spin .7s linear infinite;
    vertical-align: middle;
    margin-right: 7px;
    flex-shrink: 0;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('membershipCheckoutForm');
    if (!form) return;
    form.addEventListener('submit', function () {
      const btn = document.getElementById('membershipPayButton');
      if (!btn) return;
      btn.disabled = true;
      btn.style.opacity = '.75';
      btn.style.cursor = 'default';
      btn.style.transform = 'none';
      btn.style.display = 'inline-flex';
      btn.style.alignItems = 'center';
      btn.innerHTML = '<span class="mp-spinner"></span>Redirigiendo a Mercado Pago&hellip;';
    });
  });
</script>
@endpush