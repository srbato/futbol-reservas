@extends('layouts.admin')

@section('page_title', 'Configuración inicial')

@section('content')

{{-- Stepper --}}
@php
    $steps = [
        1 => 'Complejo',
        2 => 'Cancha',
        3 => 'Horarios',
        4 => 'Listo',
    ];
    $currentStep = $step ?? 1;
@endphp

<div style="max-width:720px; margin:0 auto 32px;">
    <div style="display:flex; align-items:center; justify-content:space-between;">
        @foreach($steps as $num => $label)
            <div style="display:flex; align-items:center; {{ $num < count($steps) ? 'flex:1;' : '' }}">
                {{-- Círculo del paso --}}
                <div style="display:flex; flex-direction:column; align-items:center;">
                    @if($num < $currentStep)
                        <div style="width:40px; height:40px; border-radius:50%; background:#22c55e; color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700;">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    @elseif($num === $currentStep)
                        <div style="width:40px; height:40px; border-radius:50%; background:#111; color:#fff; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700;">
                            {{ $num }}
                        </div>
                    @else
                        <div style="width:40px; height:40px; border-radius:50%; background:#e2e8f0; color:#94a3b8; display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:700;">
                            {{ $num }}
                        </div>
                    @endif
                    <span class="onb-step-label" style="margin-top:6px; font-size:12px; font-weight:600;
                        @if($num < $currentStep) color:#22c55e;
                        @elseif($num === $currentStep) color:#111;
                        @else color:#94a3b8;
                        @endif
                    ">{{ $label }}</span>
                </div>

                {{-- Línea conectora --}}
                @if($num < count($steps))
                    <div style="flex:1; margin:0 8px;">
                        <div style="height:2px; border-radius:2px; background:{{ $num < $currentStep ? '#22c55e' : '#e2e8f0' }};"></div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div style="max-width:720px; margin:0 auto;">
    @yield('onboarding_content')

    {{-- Link saltar configuración --}}
    @if($currentStep < 4)
        <div style="margin-top:24px; text-align:center;">
            <form method="POST" action="{{ route('va.onboarding.skip') }}">
                @csrf
                <button type="submit" style="background:none; border:none; cursor:pointer; font-size:13px; color:#94a3b8; font-family:inherit; text-decoration:underline;">
                    Saltar configuración
                </button>
            </form>
        </div>
    @endif
</div>

<style>
    @media (max-width: 480px) {
        .onb-step-label { display: none !important; }
    }
</style>

@endsection
