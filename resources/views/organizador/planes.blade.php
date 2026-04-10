@extends('layouts.app')

@section('title', 'Planes para Organizadores — TuCancha')
@section('meta_description', 'Elegí el plan ideal para organizar torneos en TuCancha. Plan gratuito o Pro con funciones avanzadas.')

@push('styles')
<style>
.planes-hero {
    text-align: center;
    padding: var(--space-3xl) var(--space-md) var(--space-2xl);
    max-width: 700px;
    margin: 0 auto;
}

.planes-hero h1 {
    font-size: var(--text-2xl);
    font-weight: var(--font-bold);
    color: var(--color-text);
    margin: 0 0 var(--space-sm);
    line-height: var(--leading-tight);
}

.planes-hero p {
    font-size: var(--text-base);
    color: var(--color-text-secondary);
    margin: 0;
    line-height: var(--leading-normal);
}

.planes-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-lg);
    max-width: 900px;
    margin: 0 auto;
    padding: 0 var(--space-md) var(--space-3xl);
    align-items: start;
}

.plan-card {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 20px;
    padding: var(--space-xl);
    position: relative;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.plan-card:hover {
    transform: translateY(-2px);
}

.plan-card--pro {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 1px var(--color-primary), 0 8px 32px rgba(34, 197, 94, 0.12);
}

.plan-card--pro:hover {
    box-shadow: 0 0 0 1px var(--color-primary), 0 12px 40px rgba(34, 197, 94, 0.18);
}

.plan-badges {
    display: flex;
    gap: var(--space-sm);
    margin-bottom: var(--space-md);
    flex-wrap: wrap;
}

.plan-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: var(--text-xs);
    font-weight: var(--font-semibold);
    padding: 4px 12px;
    border-radius: var(--radius-full);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.plan-badge--current {
    background: #e8f7ee;
    color: #157347;
    border: 1px solid #cfe9d7;
}

.plan-badge--recommended {
    background: var(--color-primary);
    color: #fff;
}

.plan-name {
    font-size: var(--text-lg);
    font-weight: var(--font-bold);
    color: var(--color-text);
    margin: 0 0 var(--space-xs);
}

.plan-price {
    font-size: var(--text-2xl);
    font-weight: var(--font-black);
    color: var(--color-text);
    margin: 0 0 var(--space-lg);
    line-height: var(--leading-tight);
}

.plan-price span {
    font-size: var(--text-sm);
    font-weight: var(--font-normal);
    color: var(--color-text-secondary);
}

.plan-divider {
    height: 1px;
    background: var(--color-border);
    margin: 0 0 var(--space-lg);
}

.plan-features-title {
    font-size: var(--text-xs);
    font-weight: var(--font-semibold);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--color-text-muted);
    margin: 0 0 var(--space-md);
}

.plan-features {
    list-style: none;
    padding: 0;
    margin: 0 0 var(--space-lg);
}

.plan-features li {
    display: flex;
    align-items: flex-start;
    gap: var(--space-sm);
    font-size: var(--text-sm);
    color: var(--color-text);
    padding: 6px 0;
    line-height: var(--leading-normal);
}

.plan-features li.feature--disabled {
    color: var(--color-text-muted);
}

.plan-features li.feature--coming {
    color: var(--color-text-secondary);
}

.feature-icon {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    margin-top: 1px;
}

.feature-icon--check {
    color: var(--color-primary);
}

.feature-icon--x {
    color: #ccc;
}

.feature-icon--star {
    color: var(--color-primary);
}

.coming-badge {
    display: inline-flex;
    font-size: 10px;
    font-weight: var(--font-semibold);
    padding: 2px 6px;
    border-radius: var(--radius-full);
    background: #f0f0f0;
    color: var(--color-text-muted);
    margin-left: 4px;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.plan-cta {
    margin-top: auto;
}

.btn-plan {
    display: block;
    width: 100%;
    padding: 14px var(--space-lg);
    border: none;
    border-radius: var(--radius-md);
    font-size: var(--text-sm);
    font-weight: var(--font-semibold);
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: background 0.15s ease, transform 0.1s ease;
}

.btn-plan:active {
    transform: scale(0.98);
}

.btn-plan--outline {
    background: transparent;
    border: 1.5px solid var(--color-border);
    color: var(--color-text);
}

.btn-plan--outline:hover {
    background: var(--color-bg-hover);
    border-color: var(--color-text-muted);
}

.btn-plan--primary {
    background: var(--color-bg-dark);
    color: var(--color-text-inverse);
}

.btn-plan--primary:hover {
    background: #333;
}

.btn-plan--disabled {
    background: #f3f3f3;
    color: var(--color-text-muted);
    cursor: not-allowed;
    border: 1px solid var(--color-border);
}

.plan-subscribe-form {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
}

.plan-email-input {
    width: 100%;
    padding: 12px var(--space-md);
    border: 1.5px solid var(--color-border);
    border-radius: var(--radius-md);
    font-size: var(--text-sm);
    font-family: var(--font-family);
    outline: none;
    transition: border-color 0.15s ease;
}

.plan-email-input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.plan-email-input::placeholder {
    color: var(--color-text-muted);
}

.subscription-status {
    margin-top: var(--space-lg);
    padding: var(--space-md);
    border-radius: var(--radius-md);
    background: #f8f9fa;
    border: 1px solid var(--color-border);
}

.subscription-status-label {
    font-size: var(--text-xs);
    font-weight: var(--font-semibold);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-text-muted);
    margin: 0 0 var(--space-sm);
}

.subscription-status-badge {
    display: inline-flex;
    padding: 4px 10px;
    border-radius: var(--radius-full);
    font-size: var(--text-xs);
    font-weight: var(--font-semibold);
}

.subscription-meta {
    font-size: var(--text-sm);
    color: var(--color-text-secondary);
    margin: var(--space-sm) 0 0;
}

.cancel-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: var(--text-sm);
    color: var(--color-error);
    text-decoration: none;
    margin-top: var(--space-md);
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    font-family: var(--font-family);
}

.cancel-link:hover {
    text-decoration: underline;
}

/* Flash messages */
.planes-flash {
    max-width: 900px;
    margin: 0 auto var(--space-lg);
    padding: 0 var(--space-md);
}

.planes-flash-msg {
    padding: var(--space-md);
    border-radius: var(--radius-md);
    font-size: var(--text-sm);
    line-height: var(--leading-normal);
}

.planes-flash-msg--success {
    background: #e8f7ee;
    color: #157347;
    border: 1px solid #cfe9d7;
}

.planes-flash-msg--error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.planes-flash-msg--info {
    background: #eff6ff;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}

/* Responsive */
@media (max-width: 700px) {
    .planes-grid {
        grid-template-columns: 1fr;
        max-width: 440px;
    }

    .planes-hero h1 {
        font-size: var(--text-xl);
    }

    .plan-card--pro {
        order: -1;
    }
}
</style>
@endpush

@section('content')
<div class="planes-hero">
    <h1>Planes para Organizadores</h1>
    <p>Organizá torneos de manera simple. Elegí el plan que mejor se adapte a tus necesidades.</p>
</div>

{{-- Flash messages --}}
@if(session('success') || session('error') || session('info'))
<div class="planes-flash">
    @if(session('success'))
        <div class="planes-flash-msg planes-flash-msg--success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="planes-flash-msg planes-flash-msg--error">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="planes-flash-msg planes-flash-msg--info">{{ session('info') }}</div>
    @endif
</div>
@endif

@php
    $freePlan = $plans->firstWhere('slug', 'free');
    $proPlan = $plans->firstWhere('slug', 'pro');

    $formatLabels = [
        'single_elimination' => 'Eliminacion directa',
        'round_robin' => 'Liga (todos contra todos)',
        'groups_elimination' => 'Grupos + Eliminacion',
    ];
@endphp

<div class="planes-grid">
    {{-- FREE Plan --}}
    @if($freePlan)
    <div class="plan-card">
        <div class="plan-badges">
            @if($tier === 'free')
                <span class="plan-badge plan-badge--current">Plan actual</span>
            @endif
        </div>

        <h2 class="plan-name">{{ $freePlan->name }}</h2>
        <div class="plan-price">${{ number_format($freePlan->monthly_price, 0, ',', '.') }}</div>

        <div class="plan-divider"></div>

        <div class="plan-features-title">Incluido</div>
        <ul class="plan-features">
            <li>
                <svg class="feature-icon feature-icon--check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                {{ $freePlan->isUnlimited() ? 'Torneos ilimitados' : $freePlan->max_tournaments . ' torneo(s) activo(s) a la vez' }}
            </li>
            <li>
                <svg class="feature-icon feature-icon--check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Hasta {{ $freePlan->max_teams_per_tournament }} equipos por torneo
            </li>
            @foreach($freePlan->available_formats ?? [] as $format)
            <li>
                <svg class="feature-icon feature-icon--check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                {{ $formatLabels[$format] ?? $format }}
            </li>
            @endforeach
            <li>
                <svg class="feature-icon feature-icon--check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Pagina publica del torneo
            </li>
            <li>
                <svg class="feature-icon feature-icon--check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Compartir por WhatsApp
            </li>
            <li>
                <svg class="feature-icon feature-icon--check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Bracket automatico
            </li>
        </ul>

        <div class="plan-features-title">No incluido</div>
        <ul class="plan-features">
            @if(!$freePlan->has_stats)
            <li class="feature--disabled">
                <svg class="feature-icon feature-icon--x" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                Sin estadisticas
            </li>
            @endif
            @if(!$freePlan->has_notifications)
            <li class="feature--disabled">
                <svg class="feature-icon feature-icon--x" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                Sin notificaciones automaticas
            </li>
            @endif
            @if(!$freePlan->has_custom_branding)
            <li class="feature--disabled">
                <svg class="feature-icon feature-icon--x" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                Branding TuCancha obligatorio
            </li>
            @endif
            @if(!$freePlan->has_mp_payments)
            <li class="feature--disabled">
                <svg class="feature-icon feature-icon--x" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                Sin cobro integrado de inscripcion
            </li>
            @endif
        </ul>

        <div class="plan-cta">
            <a href="{{ route('torneos.create') }}" class="btn-plan btn-plan--outline">
                Crear torneo gratis
            </a>
        </div>
    </div>
    @endif

    {{-- PRO Plan --}}
    @if($proPlan)
    <div class="plan-card plan-card--pro">
        <div class="plan-badges">
            <span class="plan-badge plan-badge--recommended">Recomendado</span>
            @if($tier === 'pro')
                <span class="plan-badge plan-badge--current">Tu plan</span>
            @endif
        </div>

        <h2 class="plan-name">{{ $proPlan->name }}</h2>
        <div class="plan-price">
            ${{ number_format((float) $proPlan->monthly_price, 0, ',', '.') }}
            <span>/mes</span>
        </div>

        <div class="plan-divider"></div>

        <div class="plan-features-title">Todo lo del plan {{ $freePlan->name ?? 'Gratis' }}, mas</div>
        <ul class="plan-features">
            <li>
                <svg class="feature-icon feature-icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                {{ $proPlan->isUnlimited() ? 'Torneos ilimitados' : 'Hasta ' . $proPlan->max_tournaments . ' torneos activos' }}
            </li>
            <li>
                <svg class="feature-icon feature-icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Hasta {{ $proPlan->max_teams_per_tournament }} equipos por torneo
            </li>
            @php
                $proExtraFormats = array_diff($proPlan->available_formats ?? [], $freePlan->available_formats ?? []);
            @endphp
            @if(count($proExtraFormats) > 0)
            <li class="feature--coming">
                <svg class="feature-icon feature-icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                {{ implode(' + ', array_map(fn($f) => $formatLabels[$f] ?? $f, $proExtraFormats)) }} <span class="coming-badge">Pronto</span>
            </li>
            @endif
            @if($proPlan->has_stats)
            <li class="feature--coming">
                <svg class="feature-icon feature-icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Estadisticas: goleadores, tarjetas, MVP <span class="coming-badge">Pronto</span>
            </li>
            @endif
            @if($proPlan->has_notifications)
            <li class="feature--coming">
                <svg class="feature-icon feature-icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Notificaciones automaticas a equipos <span class="coming-badge">Pronto</span>
            </li>
            @endif
            @if($proPlan->has_mp_payments)
            <li class="feature--coming">
                <svg class="feature-icon feature-icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Cobro de inscripcion por MercadoPago <span class="coming-badge">Pronto</span>
            </li>
            @endif
            @if($proPlan->has_custom_branding)
            <li class="feature--coming">
                <svg class="feature-icon feature-icon--star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                Logo propio, sin branding TuCancha <span class="coming-badge">Pronto</span>
            </li>
            @endif
        </ul>

        <div class="plan-cta">
            @if($tier === 'pro')
                <button class="btn-plan btn-plan--disabled" disabled>Tu plan actual</button>

                @if($subscription)
                <div class="subscription-status">
                    <div class="subscription-status-label">Estado de la suscripcion</div>
                    <span class="subscription-status-badge" style="{{ $subscription->statusStyles() }}">
                        {{ $subscription->statusLabel() }}
                    </span>

                    @if($subscription->expires_at)
                    <div class="subscription-meta">
                        Vence el {{ $subscription->expires_at->format('d/m/Y') }}
                    </div>
                    @endif

                    <form action="{{ route('organizador.cancel') }}" method="POST"
                          onsubmit="return confirm('¿Seguro que queres cancelar tu suscripcion Pro?')">
                        @csrf
                        <button type="submit" class="cancel-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                            Cancelar suscripcion
                        </button>
                    </form>
                </div>
                @endif
            @else
                <form action="{{ route('organizador.subscribe') }}" method="POST" class="plan-subscribe-form">
                    @csrf
                    <input
                        type="email"
                        name="mp_email"
                        class="plan-email-input"
                        placeholder="Tu email de MercadoPago"
                        value="{{ old('mp_email', auth()->user()->email) }}"
                        required
                    >
                    @error('mp_email')
                        <span style="color: var(--color-error); font-size: var(--text-xs);">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="btn-plan btn-plan--primary">
                        Suscribirme a {{ $proPlan->name }}
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
