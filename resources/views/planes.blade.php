@extends('layouts.marketing')

@section('title', 'Planes para tu complejo — TuCancha')
@section('meta_description', 'Planes de suscripción para complejos deportivos. Gestión de reservas online, cobros automáticos con MercadoPago, agenda y reportes. Sin comisiones sobre tus ingresos.')

@push('jsonld')
<script type="application/ld+json">
@php
echo json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Inicio', 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Planes', 'item' => url('/planes')],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
@endpush

@push('styles')
<style>
  /* ── Hero ────────────────────────────────────────── */
  .pl-hero {
    padding: 40px 0 0 0;
  }

  .pl-hero-inner {
    border-radius: 28px;
    padding: 72px 48px;
    color: #fff;
    text-align: center;
    position: relative;
    overflow: hidden;
    background-image: url('/images/ambiente-cancha-noche.webp');
    background-size: cover;
    background-position: center;
  }

  .pl-hero-inner::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(0,0,0,.82) 0%, rgba(10,10,10,.68) 100%);
    border-radius: 28px;
    z-index: 0;
  }

  .pl-hero-inner > * {
    position: relative;
    z-index: 1;
  }

  .pl-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 999px;
    background: rgba(34,197,94,.15);
    border: 1px solid rgba(34,197,94,.35);
    color: #4ade80;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-bottom: 18px;
  }

  .pl-hero-inner h1 {
    margin: 0 0 14px 0;
    font-size: 54px;
    line-height: 1.04;
    letter-spacing: -0.03em;
  }

  .pl-hero-inner h1 .hero-highlight {
    color: #22c55e;
  }

  .pl-hero-inner p {
    margin: 0;
    color: rgba(255,255,255,.78);
    font-size: 18px;
    line-height: 1.6;
  }

  /* ── Billing toggle ──────────────────────────────── */
  .billing-toggle-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    padding: 40px 0 8px 0;
  }

  .billing-toggle {
    display: flex;
    background: #111;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 999px;
    padding: 4px;
    gap: 4px;
  }

  .billing-opt {
    padding: 8px 22px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 700;
    color: #666;
    cursor: pointer;
    border: none;
    background: none;
    font-family: inherit;
    transition: background .18s, color .18s;
  }

  .billing-opt.active {
    background: #22c55e;
    color: #050505;
  }

  .annual-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 999px;
    background: rgba(34,197,94,.12);
    color: #6eeaa0;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .03em;
  }

  /* ── Pricing grid ────────────────────────────────── */
  .pricing-section {
    padding: 32px 0 56px 0;
  }

  .pricing-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    align-items: start;
  }

  .plan-card {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 24px;
    padding: 32px 28px;
    position: relative;
    transition: transform .2s, box-shadow .2s;
  }

  .plan-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(0,0,0,.25);
  }

  /* Featured card */
  .plan-card.featured {
    background: #1a1a1a;
    border-color: rgba(34,197,94,.3);
    border-top: 4px solid #22c55e;
    color: #fff;
    transform: scale(1.03);
    box-shadow: 0 16px 48px rgba(0,0,0,.35);
  }

  .plan-card.featured:hover {
    transform: scale(1.03) translateY(-4px);
    box-shadow: 0 20px 56px rgba(0,0,0,.28);
  }

  .plan-popular-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    background: #4ade80;
    color: #052e16;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 18px;
  }

  /* ── Launch offer banner ─────────────────────────── */
  .plan-offer-banner {
    position: relative;
    margin: -32px -28px 20px -28px;
    padding: 16px 20px;
    background: linear-gradient(135deg, #052e16 0%, #064e3b 50%, #052e16 100%);
    border-bottom: 2px solid #22c55e;
    border-radius: 24px 24px 0 0;
    text-align: center;
    overflow: hidden;
  }
  .plan-card.featured .plan-offer-banner {
    margin-top: -32px;
    border-radius: 20px 20px 0 0;
  }
  .plan-offer-banner::before {
    content: '';
    position: absolute;
    inset: 0;
    background: repeating-linear-gradient(
      -45deg,
      transparent,
      transparent 8px,
      rgba(34,197,94,.06) 8px,
      rgba(34,197,94,.06) 16px
    );
  }
  .plan-offer-pulse {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 5px 14px;
    border-radius: 999px;
    background: rgba(34,197,94,.2);
    border: 1px solid rgba(34,197,94,.4);
    color: #4ade80;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: 8px;
    position: relative;
    animation: offer-pulse 2s ease-in-out infinite;
  }
  @keyframes offer-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,.3); }
    50% { box-shadow: 0 0 0 8px rgba(34,197,94,0); }
  }
  .plan-offer-title {
    position: relative;
    font-size: 18px;
    font-weight: 800;
    color: #fff;
    margin: 0;
    letter-spacing: -.01em;
  }
  .plan-offer-title span { color: #4ade80; }
  .plan-offer-sub {
    position: relative;
    font-size: 12px;
    color: rgba(255,255,255,.55);
    margin: 4px 0 0;
  }

  /* ── Price with offer styling ────────────────────── */
  .plan-price-offer {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }
  .plan-price-old {
    font-size: 22px;
    font-weight: 700;
    color: #555;
    text-decoration: line-through;
    text-decoration-color: #ef4444;
    text-decoration-thickness: 2px;
  }
  .plan-card.featured .plan-price-old { color: rgba(255,255,255,.35); }
  .plan-price-free {
    font-size: 42px;
    font-weight: 900;
    letter-spacing: -.03em;
    line-height: 1;
    background: linear-gradient(135deg, #22c55e, #4ade80);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  .plan-price-free-period {
    font-size: 13px;
    color: #4ade80;
    font-weight: 700;
    margin-top: 2px;
  }
  .plan-price-then {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: #666;
    margin-top: 6px;
    padding: 3px 10px;
    background: rgba(255,255,255,.04);
    border-radius: 6px;
  }
  .plan-card.featured .plan-price-then { color: rgba(255,255,255,.45); }

  .plan-name {
    font-size: 20px;
    font-weight: 800;
    margin: 0 0 6px 0;
    color: #e8e8e8;
  }

  .plan-limit {
    font-size: 14px;
    color: #666;
    margin: 0 0 24px 0;
  }

  .plan-card.featured .plan-limit { color: rgba(255,255,255,.5); }

  .plan-price-wrap {
    margin-bottom: 28px;
    min-height: 68px;
  }

  .plan-price {
    font-size: 46px;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1;
    color: #e8e8e8;
  }

  .plan-price-period {
    font-size: 14px;
    color: #666;
    margin-top: 6px;
  }

  .plan-card.featured .plan-price-period { color: rgba(255,255,255,.5); }

  .plan-original-price {
    font-size: 15px;
    color: #555;
    text-decoration: line-through;
    margin-bottom: 2px;
  }

  .plan-card.featured .plan-original-price { color: rgba(255,255,255,.35); }

  /* Feature list */
  .plan-features {
    list-style: none;
    margin: 0 0 28px 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 11px;
    border-top: 1px solid rgba(255,255,255,.08);
    padding-top: 24px;
  }

  .plan-card.featured .plan-features { border-top-color: rgba(255,255,255,.12); }

  .plan-feature {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 14px;
    line-height: 1.4;
    color: #a0a0a0;
  }

  .plan-card.featured .plan-feature { color: rgba(255,255,255,.82); }

  .plan-feature-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(34,197,94,.12);
    color: #22c55e;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1px;
  }

  .plan-card.featured .plan-feature-icon {
    background: rgba(74,222,128,.18);
    color: #4ade80;
  }

  /* Feature deshabilitada (tachada) */
  .plan-feature.disabled {
    opacity: 0.45;
    text-decoration: line-through;
    color: #555;
  }

  .plan-card.featured .plan-feature.disabled {
    color: rgba(255,255,255,.35);
  }

  .plan-feature.disabled .plan-feature-icon {
    background: rgba(255,255,255,.06);
    color: #444;
  }

  .plan-card.featured .plan-feature.disabled .plan-feature-icon {
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.25);
  }

  /* CTA buttons */
  .plan-btn {
    display: block;
    width: 100%;
    padding: 13px 20px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 700;
    text-align: center;
    border: 2px solid rgba(255,255,255,.2);
    background: transparent;
    color: #e8e8e8;
    cursor: pointer;
    transition: background .15s, color .15s, transform .15s, border-color .15s;
    font-family: inherit;
    text-decoration: none;
  }

  .plan-btn:hover {
    background: #22c55e;
    color: #050505;
    border-color: #22c55e;
    transform: translateY(-1px);
  }

  .plan-card.featured .plan-btn {
    background: #4ade80;
    color: #052e16;
    border-color: #4ade80;
  }

  .plan-card.featured .plan-btn:hover {
    background: #86efac;
    border-color: #86efac;
    color: #052e16;
  }

  /* ── FAQ ─────────────────────────────────────────── */
  .faq-section { padding: 0 0 56px 0; }

  .faq-list { display: flex; flex-direction: column; gap: 10px; }

  .faq-item {
    background: #111;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 20px;
    overflow: hidden;
    transition: box-shadow .2s, border-left-color .2s;
    border-left: 4px solid transparent;
  }

  .faq-item.open {
    box-shadow: 0 4px 20px rgba(0,0,0,.2);
    border-left-color: #22c55e;
  }

  .faq-trigger {
    width: 100%;
    background: none;
    border: none;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    cursor: pointer;
    text-align: left;
    font: inherit;
  }

  .faq-trigger-text { font-size: 16px; font-weight: 700; color: #e8e8e8; }

  .faq-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a0a0a0;
    flex-shrink: 0;
    transition: transform .22s, background .22s, color .22s;
  }

  .faq-item.open .faq-icon {
    transform: rotate(45deg);
    background: #22c55e;
    color: #fff;
  }

  .faq-body {
    display: none;
    padding: 0 24px 20px 28px;
    color: #a0a0a0;
    font-size: 15px;
    line-height: 1.7;
    border-top: 1px solid rgba(255,255,255,.06);
    padding-top: 16px;
  }

  .faq-item.open .faq-body { display: block; }

  /* ── Responsive ──────────────────────────────────── */
  @media (max-width: 900px) {
    .pricing-grid {
      grid-template-columns: 1fr;
      max-width: 480px;
      margin: 0 auto;
    }

    .plan-card.featured { transform: none; }
    .plan-card.featured:hover { transform: translateY(-4px); }
  }

  @media (max-width: 768px) {
    .pl-hero-inner { padding: 48px 24px; }
    .pl-hero-inner h1 { font-size: 34px; }
  }

  @media (max-width: 480px) {
    .pl-hero { padding: 16px 0 0 0; }
    .pl-hero-inner h1 { font-size: 28px; }
    .plan-price { font-size: 38px; }
  }
</style>
@endpush

@section('content')

  {{-- ── Hero ─────────────────────────────────────────────────────── --}}
  <section class="pl-hero">
    <div class="container">
      <div class="pl-hero-inner" data-aos="fade-up">
        <div class="pl-hero-badge">
          <i data-lucide="shield-check" style="width:13px;height:13px;stroke:currentColor;stroke-width:2.5;"></i>
          Sin comisiones
        </div>
        <h1>Planes para tu <span class="hero-highlight">complejo</span></h1>
        <p>Todo lo que necesitás para gestionar tus canchas online.</p>
        @if($trialDays > 0)
          <div style="margin-top:18px; display:inline-flex; align-items:center; gap:8px; padding:8px 20px; border-radius:999px; background:rgba(34,197,94,.15); border:1px solid rgba(34,197,94,.3);">
            <i data-lucide="zap" style="width:14px;height:14px;stroke:#4ade80;stroke-width:2.5;"></i>
            <span style="color:#4ade80; font-size:14px; font-weight:700;">Oferta de lanzamiento: {{ $trialDays }} días gratis en todos los planes</span>
          </div>
        @endif
      </div>
    </div>
  </section>

  {{-- ── Billing toggle ────────────────────────────────────────────── --}}
  <div class="billing-toggle-wrap">
    <div class="billing-toggle">
      <button class="billing-opt active" onclick="setBilling('monthly', this)">Mensual</button>
      <button class="billing-opt" onclick="setBilling('annual', this)">{{ $plans->isNotEmpty() ? $plans->first()->longTermLabel() : 'Anual' }}</button>
    </div>
    <span class="annual-badge" id="annualBadge" style="opacity:0; transition:opacity .2s;">
      <i data-lucide="trending-down" style="width:12px;height:12px;stroke:currentColor;stroke-width:2.5;"></i>
      Ahorrás {{ $plans->max('annual_discount_percentage') }}%
    </span>
  </div>

  {{-- ── Pricing cards ─────────────────────────────────────────────── --}}
  <section class="pricing-section">
    <div class="container">
      <div class="pricing-grid">

        @foreach($plans as $i => $plan)
        @php
          $aosDir = $i === 0 ? 'fade-right' : ($i === 1 ? 'fade-up' : 'fade-left');
          $aosDelay = $i * 100;
        @endphp
        <div class="plan-card {{ $plan->is_featured ? 'featured' : '' }}"
             data-aos="{{ $aosDir }}" data-aos-delay="{{ $aosDelay }}">

          {{-- ── Offer banner (only if plan has trial) ── --}}
          @if($plan->hasTrial())
            <div class="plan-offer-banner trial-element">
              <div class="plan-offer-pulse">
                <i data-lucide="zap" style="width:11px;height:11px;stroke:currentColor;stroke-width:2.5;"></i>
                Oferta de lanzamiento
              </div>
              <p class="plan-offer-title"><span>{{ $plan->trial_days }} días GRATIS</span></p>
              <p class="plan-offer-sub">Probá sin compromiso · sin tarjeta</p>
            </div>
          @endif

          @if($plan->is_featured)
            <div class="plan-popular-badge" @if($plan->hasTrial()) style="margin-top:0;" @endif>
              <i data-lucide="star" style="width:12px;height:12px;stroke:#052e16;stroke-width:2;fill:#052e16;"></i>
              Más popular
            </div>
          @endif

          <p class="plan-name">{{ $plan->name }}</p>
          <p class="plan-limit">{{ $plan->maxFieldsLabel() }}</p>

          <div class="plan-price-wrap">
            @if($plan->hasTrial())
              {{-- Offer pricing: FREE then $X/mes --}}
              <div class="trial-element">
                <div class="plan-price-offer">
                  <span class="plan-price-old">${{ number_format($plan->monthly_price, 0, ',', '.') }}/mes</span>
                </div>
                <div class="plan-price-free">GRATIS</div>
                <div class="plan-price-free-period">por {{ $plan->trial_days }} días</div>
                <div class="plan-price-then">
                  <i data-lucide="arrow-right" style="width:10px;height:10px;stroke:currentColor;"></i>
                  Luego ${{ number_format($plan->monthly_price, 0, ',', '.') }} ARS/mes
                </div>
              </div>
              {{-- Normal pricing (shown when annual billing is selected) --}}
              <div class="no-trial-element" style="display:none;">
                <div class="plan-original-price" id="{{ $plan->slug }}-original" style="display:none;">
                  ${{ number_format($plan->monthly_price, 0, ',', '.') }}
                </div>
                <div class="plan-price">
                  $<span id="{{ $plan->slug }}-price">{{ number_format($plan->monthly_price, 0, ',', '.') }}</span>
                </div>
                <div class="plan-price-period">ARS / mes</div>
              </div>
            @else
              <div class="plan-original-price" id="{{ $plan->slug }}-original" style="display:none;">
                ${{ number_format($plan->monthly_price, 0, ',', '.') }}
              </div>
              <div class="plan-price">
                $<span id="{{ $plan->slug }}-price">{{ number_format($plan->monthly_price, 0, ',', '.') }}</span>
              </div>
              <div class="plan-price-period">ARS / mes</div>
            @endif
          </div>

          @php
            $allFeatures = [
              ['label' => $plan->maxFieldsLabel(), 'plans' => ['starter', 'pro', 'full']],
              ['label' => 'Reservas online 24/7', 'plans' => ['starter', 'pro', 'full']],
              ['label' => 'Cobro por Mercado Pago', 'plans' => ['starter', 'pro', 'full']],
              ['label' => 'Panel de administración', 'plans' => ['starter', 'pro', 'full']],
              ['label' => 'Mails automáticos', 'plans' => ['starter', 'pro', 'full']],
              ['label' => 'Reportes de actividad', 'plans' => ['starter', 'pro', 'full']],
              ['label' => 'Soporte prioritario', 'plans' => ['pro', 'full']],
              ['label' => 'Badge "Destacado" en el listado', 'plans' => ['pro', 'full']],
              ['label' => 'Posicionamiento prioritario', 'plans' => ['pro', 'full']],
              ['label' => 'Card diferenciada en búsqueda', 'plans' => ['pro', 'full']],
              ['label' => 'Badge "Premium" exclusivo', 'plans' => ['full']],
              ['label' => 'Máxima visibilidad y prioridad', 'plans' => ['full']],
            ];
          @endphp
          <ul class="plan-features">
            @foreach($allFeatures as $feature)
              @php $enabled = in_array($plan->slug, $feature['plans']); @endphp
              <li class="plan-feature {{ !$enabled ? 'disabled' : '' }}">
                <span class="plan-feature-icon">
                  @if($enabled)
                    <i data-lucide="check" style="width:11px;height:11px;stroke:currentColor;stroke-width:3;"></i>
                  @else
                    <i data-lucide="x" style="width:11px;height:11px;stroke:currentColor;stroke-width:3;"></i>
                  @endif
                </span>
                {{ $feature['label'] }}
              </li>
            @endforeach
          </ul>

          @if($plan->hasTrial())
            <div class="trial-text trial-element" style="font-size:12px; color:#6eeaa0; font-weight:600; text-align:center; margin-bottom:10px;">
              ⚡ {{ $plan->trial_days }} días gratis · sin compromiso
            </div>
          @endif
          <a id="{{ $plan->slug }}-btn"
             href="{{ route('membership.become') }}?plan={{ $plan->slug }}&billing=monthly"
             class="plan-btn"
             @if($plan->hasTrial()) style="background:#22c55e; color:#050505; border-color:#22c55e; font-size:16px;" @endif
          >{{ $plan->hasTrial() ? 'Empezar gratis →' : 'Empezar' }}</a>
        </div>
        @endforeach

      </div>
    </div>
  </section>

  {{-- ── FAQ de planes ─────────────────────────────────────────────── --}}
  <section class="faq-section">
    <div class="container" style="max-width: 760px;">
      <div class="section-head">
        <span class="section-label">Preguntas frecuentes</span>
        <h2 class="section-title">Dudas sobre los planes</h2>
      </div>

      <div class="faq-list">

        <div class="faq-item" data-aos="fade-up" data-aos-delay="0">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Hay período de prueba?</span>
            <span class="faq-icon"><i data-lucide="plus" style="width:14px;height:14px;stroke:currentColor;stroke-width:2.5;"></i></span>
          </button>
          <div class="faq-body">
            Sí. Tenés {{ $trialDays }} días de prueba completamente gratis para explorar el panel, cargar tus canchas y ver cómo funciona
            el sistema. No necesitás tarjeta y no se te cobra nada durante ese período.
          </div>
        </div>

        <div class="faq-item" data-aos="fade-up" data-aos-delay="60">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Puedo cancelar cuando quiera?</span>
            <span class="faq-icon"><i data-lucide="plus" style="width:14px;height:14px;stroke:currentColor;stroke-width:2.5;"></i></span>
          </button>
          <div class="faq-body">
            Sí, podés cancelar tu suscripción en cualquier momento desde el panel.
            No hay contratos ni permanencia mínima. Si cancelás antes de que venza el período pagado,
            seguís teniendo acceso hasta el último día.
          </div>
        </div>

        <div class="faq-item" data-aos="fade-up" data-aos-delay="120">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Hay costos extra o comisiones?</span>
            <span class="faq-icon"><i data-lucide="plus" style="width:14px;height:14px;stroke:currentColor;stroke-width:2.5;"></i></span>
          </button>
          <div class="faq-body">
            No cobramos comisión sobre tus reservas. El precio del plan es todo lo que pagás a TuCancha.
            Mercado Pago aplica sus propias comisiones estándar sobre cada transacción de pago,
            pero eso es independiente de TuCancha.
          </div>
        </div>

        <div class="faq-item" data-aos="fade-up" data-aos-delay="180">
          <button class="faq-trigger" onclick="toggleFaq(this)">
            <span class="faq-trigger-text">¿Cómo se cobra el plan?</span>
            <span class="faq-icon"><i data-lucide="plus" style="width:14px;height:14px;stroke:currentColor;stroke-width:2.5;"></i></span>
          </button>
          <div class="faq-body">
            El pago se realiza una vez al mes @if($plans->isNotEmpty())(o cada {{ $plans->first()->longTermMonths() }} meses si elegís el plan {{ strtolower($plans->first()->longTermLabel()) }})@endif a través de Mercado Pago.
            Podés pagar con tarjeta de crédito, débito o transferencia. Al aprobarse el pago,
            tu acceso se activa o renueva automáticamente.
          </div>
        </div>

      </div>
    </div>
  </section>

@endsection

@php
  $planDataForJs = $plans->map(fn($p) => [
    'slug'     => $p->slug,
    'monthly'  => number_format($p->monthly_price, 0, ',', '.'),
    'annual'   => number_format($p->annualMonthlyEquivalent(), 0, ',', '.'),
    'hasTrial' => $p->hasTrial(),
  ])->values()->toArray();
@endphp

@push('scripts')
<script>
  const planData = @json($planDataForJs);

  let currentBilling = 'monthly';

  function setBilling(mode, btn) {
    currentBilling = mode;
    document.querySelectorAll('.billing-opt').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const badge = document.getElementById('annualBadge');
    const isAnnual = mode === 'annual';
    badge.style.opacity = isAnnual ? '1' : '0';

    planData.forEach(plan => {
      const price = isAnnual ? plan.annual : plan.monthly;
      document.getElementById(plan.slug + '-price').textContent = price;

      const origEl = document.getElementById(plan.slug + '-original');
      if (isAnnual) {
        origEl.textContent = '$' + plan.monthly;
        origEl.style.display = 'block';
      } else {
        origEl.style.display = 'none';
      }

      // Update CTA link billing cycle and trial visibility
      const ctaBtn = document.getElementById(plan.slug + '-btn');
      if (ctaBtn) {
        const url = new URL(ctaBtn.href);
        url.searchParams.set('billing', mode);
        ctaBtn.href = url.toString();

        if (plan.hasTrial) {
          ctaBtn.textContent = isAnnual ? 'Empezar' : 'Empezar gratis →';
          if (isAnnual) {
            ctaBtn.style.background = '';
            ctaBtn.style.color = '';
            ctaBtn.style.borderColor = '';
            ctaBtn.style.fontSize = '';
          } else {
            ctaBtn.style.background = '#22c55e';
            ctaBtn.style.color = '#050505';
            ctaBtn.style.borderColor = '#22c55e';
            ctaBtn.style.fontSize = '16px';
          }
        }
      }
    });

    // Ocultar/mostrar todos los elementos de trial según billing
    document.querySelectorAll('.trial-element').forEach(el => el.style.display = isAnnual ? 'none' : '');
    document.querySelectorAll('.no-trial-element').forEach(el => el.style.display = isAnnual ? '' : 'none');
  }

  function toggleFaq(trigger) {
    const item = trigger.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  }
</script>
@endpush
