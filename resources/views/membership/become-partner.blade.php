@extends('layouts.app')

@section('title', 'Hacete socio')

@section('content')
  @php
    $latestStatus = $latestSubscription->status ?? null;
    $isExpired = !$activeSubscription
      && $latestSubscription
      && $latestSubscription->status === 'ACTIVE'
      && $latestSubscription->expires_at
      && $latestSubscription->expires_at->isPast();

    function membershipStatusLabel($status) {
        return match ($status) {
            'ACTIVE' => 'Activa',
            'PENDING_PAYMENT' => 'Pendiente de pago',
            'CANCELLED' => 'Cancelada',
            default => $status ?? '-',
        };
    }

    function membershipStatusStyles($status) {
        return match ($status) {
            'ACTIVE' => 'background:#e8f7ee; color:#157347; border:1px solid #cfe9d7;',
            'PENDING_PAYMENT' => 'background:#fff4db; color:#9a6700; border:1px solid #f5d48a;',
            'CANCELLED' => 'background:#f8d7da; color:#842029; border:1px solid #f1b9c0;',
            default => 'background:#f3f3f3; color:#444; border:1px solid #e2e2e2;',
        };
    }
  @endphp

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
        <div style="font-size:12px; color:#666; margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; font-weight:700;">
          Plan {{ $plan->name }}
        </div>
        <div style="font-size:12px; color:#888; margin-bottom:8px;">
          Facturación {{ $billingCycle === 'annual' ? 'anual' : 'mensual' }}
        </div>
        <div style="font-size:34px; font-weight:800; line-height:1.1;">
          ARS {{ number_format($price, 0, ',', '.') }}
        </div>
        <div class="muted" style="margin-top:8px; font-size:13px;">
          @if($billingCycle === 'annual')
            Pago único por 365 días de acceso.
          @else
            Acceso como socio por 30 días.
          @endif
        </div>
        <div style="margin-top:12px;">
          <a href="{{ route('planes') }}" style="font-size:13px; color:#666; text-decoration:underline;">
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
        <div style="padding:16px; border-radius:16px; background:#e8f7ee; color:#157347; border:1px solid #cfe9d7; margin-bottom:14px;">
          <strong>Membresía activa</strong>
          <div style="margin-top:6px;">
            Tenés acceso como socio hasta el
            <strong>{{ $activeSubscription->expires_at?->format('d/m/Y H:i') }}</strong>.
          </div>
        </div>

        <div class="muted" style="margin-bottom:14px; font-size:13px; line-height:1.6;">
          Último pago registrado:
          <strong>{{ $activeSubscription->currency }} {{ number_format((float) $activeSubscription->monthly_price, 2, ',', '.') }}</strong>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <a href="{{ route('va.dashboard') }}" class="btn btn-primary">
            Ir al panel admin
          </a>

          <form method="POST" action="{{ route('membership.checkout') }}">
            @csrf
            <input type="hidden" name="plan_slug" value="{{ $activeSubscription->plan_slug ?? $plan->slug }}">
            <input type="hidden" name="billing_cycle" value="{{ $activeSubscription->billing_cycle ?? $billingCycle }}">
            <button type="submit" class="btn">
              Renovar ahora
            </button>
          </form>
        </div>
      @else
        @if($latestSubscription && $latestStatus === 'PENDING_PAYMENT')
          <div style="padding:16px; border-radius:16px; background:#fff4db; color:#9a6700; border:1px solid #f5d48a; margin-bottom:14px;">
            <strong>Pago pendiente</strong>
            <div style="margin-top:6px;">
              Ya generaste una solicitud de membresía. En cuanto MercadoPago confirme el pago,
              tu acceso como socio quedará activo.
            </div>
          </div>
        @elseif($isExpired)
          <div style="padding:16px; border-radius:16px; background:#f8d7da; color:#842029; border:1px solid #f1b9c0; margin-bottom:14px;">
            <strong>Membresía vencida</strong>
            <div style="margin-top:6px;">
              Tu último acceso como socio venció el
              <strong>{{ $latestSubscription->expires_at?->format('d/m/Y H:i') }}</strong>.
              Renová tu plan para volver a administrar complejos.
            </div>
          </div>
        @elseif($latestSubscription && $latestStatus === 'CANCELLED')
          <div style="padding:16px; border-radius:16px; background:#f8d7da; color:#842029; border:1px solid #f1b9c0; margin-bottom:14px;">
            <strong>Pago cancelado</strong>
            <div style="margin-top:6px;">
              Tu último intento de pago no se completó. Podés volver a intentarlo cuando quieras.
            </div>
          </div>
        @else
          <div style="padding:16px; border-radius:16px; background:#f3f3f3; color:#444; border:1px solid #e2e2e2; margin-bottom:14px;">
            <strong>Sin membresía activa</strong>
            <div style="margin-top:6px;">
              Todavía no tenés acceso como socio. Activá tu plan para ingresar al panel admin.
            </div>
          </div>
        @endif

        <form method="POST" action="{{ route('membership.checkout') }}">
          @csrf
          <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
          <input type="hidden" name="billing_cycle" value="{{ $billingCycle }}">

          <div style="margin-bottom:16px;">
            <label style="display:block; font-size:13px; color:#666; margin-bottom:6px;">
              Código de referido (opcional)
            </label>
            <input type="text" name="referral_code" placeholder="Ej: JUAN-X7K2"
              value="{{ old('referral_code') }}"
              style="padding:10px 14px; border:1px solid #ddd; border-radius:10px; font-size:14px; width:100%; max-width:260px; font-family:monospace; text-transform:uppercase;">
          </div>

          <button type="submit" class="btn btn-primary">
            {{ $isExpired ? 'Renovar membresía' : 'Activar plan ' . $plan->name }}
          </button>
        </form>

        <div class="muted" style="margin-top:12px; font-size:13px; line-height:1.6;">
          Precio:
          <strong>ARS {{ number_format($price, 0, ',', '.') }}</strong>
          ({{ $billingCycle === 'annual' ? 'anual' : 'mensual' }}).
        </div>

        <div class="muted" style="margin-top:8px; font-size:13px; line-height:1.6;">
          Tu acceso como <strong>venue_admin</strong> depende de que la membresía esté activa.
        </div>
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
      <div style="padding:16px; border-radius:16px; background:#f3f3f3; color:#444; border:1px solid #e2e2e2;">
        Todavía no tenés movimientos de membresía registrados.
      </div>
    @else
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:980px;">
          <thead>
            <tr style="background:#fafafa;">
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">ID</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Estado</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Monto</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Proveedor</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Pago externo</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Inicio</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Vencimiento</th>
              <th style="text-align:left; padding:12px; border-bottom:1px solid #eee;">Creada</th>
            </tr>
          </thead>
          <tbody>
            @foreach($subscriptionHistory as $subscription)
              <tr>
                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $subscription->id }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  <span style="display:inline-flex; align-items:center; padding:7px 10px; border-radius:999px; font-size:12px; font-weight:700; {{ membershipStatusStyles($subscription->status) }}">
                    {{ membershipStatusLabel($subscription->status) }}
                  </span>
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1; font-weight:600;">
                  {{ $subscription->currency }} {{ number_format((float) $subscription->monthly_price, 2, ',', '.') }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $subscription->payment_provider ?? '-' }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $subscription->payment_external_id ?? '-' }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $subscription->starts_at?->format('d/m/Y H:i') ?? '-' }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $subscription->expires_at?->format('d/m/Y H:i') ?? '-' }}
                </td>

                <td style="padding:12px; border-bottom:1px solid #f1f1f1;">
                  {{ $subscription->created_at?->format('d/m/Y H:i') ?? '-' }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endsection