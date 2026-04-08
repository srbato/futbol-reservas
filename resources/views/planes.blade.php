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
    background: #fff;
    border: 1px solid #e0e0e0;
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
    background: #111;
    color: #fff;
  }

  .annual-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 999px;
    background: #dcfce7;
    color: #166534;
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
    background: #fff;
    border: 1px solid #e8e8e8;
    border-radius: 24px;
    padding: 32px 28px;
    position: relative;
    transition: transform .2s, box-shadow .2s;
  }

  .plan-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 36px rgba(0,0,0,.09);
  }

  /* Featured card */
  .plan-card.featured {
    background: #111;
    border-color: #111;
    border-top: 4px solid #22c55e;
    color: #fff;
    transform: scale(1.03);
    box-shadow: 0 16px 48px rgba(0,0,0,.22);
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

  .plan-name {
    font-size: 20px;
    font-weight: 800;
    margin: 0 0 6px 0;
  }

  .plan-limit {
    font-size: 14px;
    color: #888;
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
  }

  .plan-price-period {
    font-size: 14px;
    color: #888;
    margin-top: 6px;
  }

  .plan-card.featured .plan-price-period { color: rgba(255,255,255,.5); }

  .plan-original-price {
    font-size: 15px;
    color: #aaa;
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
    border-top: 1px solid #efefef;
    padding-top: 24px;
  }

  .plan-card.featured .plan-features { border-top-color: rgba(255,255,255,.12); }

  .plan-feature {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 14px;
    line-height: 1.4;
    color: #444;
  }

  .plan-card.featured .plan-feature { color: rgba(255,255,255,.82); }

  .plan-feature-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #dcfce7;
    color: #157347;
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
    color: #aaa;
  }

  .plan-card.featured .plan-feature.disabled {
    color: rgba(255,255,255,.35);
  }

  .plan-feature.disabled .plan-feature-icon {
    background: #f0f0f0;
    color: #bbb;
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
    border: 2px solid #111;
    background: #fff;
    color: #111;
    cursor: pointer;
    transition: background .15s, color .15s, transform .15s;
    font-family: inherit;
    text-decoration: none;
  }

  .plan-btn:hover {
    background: #111;
    color: #fff;
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
    background: #fff;
    border: 1px solid #e4e4e4;
    border-radius: 20px;
    overflow: hidden;
    transition: box-shadow .2s, border-left-color .2s;
    border-left: 4px solid transparent;
  }

  .faq-item.open {
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
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

  .faq-trigger-text { font-size: 16px; font-weight: 700; color: #111; }

  .faq-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #f3f3f3;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #555;
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
    color: #555;
    font-size: 15px;
    line-height: 1.7;
    border-top: 1px solid #f0f0f0;
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
          @if($plan->hasTrial())
            <div class="plan-popular-badge trial-badge" style="background:#157347;">
              <i data-lucide="gift" style="width:12px;height:12px;stroke:#fff;stroke-width:2;"></i>
              {{ $plan->trial_days }} días gratis
            </div>
          @elseif($plan->is_featured)
            <div class="plan-popular-badge">
              <i data-lucide="star" style="width:12px;height:12px;stroke:#052e16;stroke-width:2;fill:#052e16;"></i>
              Más popular
            </div>
          @endif
          <p class="plan-name">{{ $plan->name }}</p>
          <p class="plan-limit">{{ $plan->maxFieldsLabel() }}</p>

          <div class="plan-price-wrap">
            <div class="plan-original-price" id="{{ $plan->slug }}-original" style="display:none;">
              ${{ number_format($plan->monthly_price, 0, ',', '.') }}
            </div>
            <div class="plan-price">
              $<span id="{{ $plan->slug }}-price">{{ number_format($plan->monthly_price, 0, ',', '.') }}</span>
            </div>
            <div class="plan-price-period">ARS / mes</div>
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
            <div class="trial-text" style="font-size:12px; color:#157347; font-weight:600; text-align:center; margin-bottom:10px;">
              {{ $plan->trial_days }} días gratis · luego se cobra automáticamente
            </div>
          @endif
          <a id="{{ $plan->slug }}-btn"
             href="{{ route('membership.become') }}?plan={{ $plan->slug }}&billing=monthly"
             class="plan-btn">{{ $plan->hasTrial() ? 'Empezar gratis' : 'Empezar' }}</a>
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
            Sí. Tenés 7 días de prueba gratuita para explorar el panel, cargar tus canchas y ver cómo funciona
            el sistema. No se te cobra nada hasta que decidís activar tu suscripción.
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
          ctaBtn.textContent = isAnnual ? 'Empezar' : 'Empezar gratis';
        }
      }
    });

    // Ocultar/mostrar badges y textos de trial según billing
    document.querySelectorAll('.trial-badge').forEach(el => el.style.display = isAnnual ? 'none' : '');
    document.querySelectorAll('.trial-text').forEach(el => el.style.display = isAnnual ? 'none' : '');
  }

  function toggleFaq(trigger) {
    const item = trigger.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
  }
</script>
@endpush
