@extends('layouts.admin')

@section('title', 'Conectar Mercado Pago')
@section('page_title', 'Conectar Mercado Pago')
@section('page_subtitle', 'Paso obligatorio para activar tu complejo')

@section('content')

  @if(session('error'))
    <div class="flash error" style="margin-bottom:20px;">{{ session('error') }}</div>
  @endif

  <div style="max-width:640px;">

    <div class="admin-card" style="margin-bottom:20px; padding:28px;">
      <div style="font-size:22px; font-weight:800; margin-bottom:10px;">
        {{ $venue->name }}
      </div>
      <div class="muted" style="font-size:15px; line-height:1.65; margin-bottom:24px;">
        Para que los usuarios puedan reservar y pagar en tu complejo, necesitás conectar tu cuenta de
        <strong>Mercado Pago</strong>. Los pagos de las reservas llegarán directo a tu cuenta.
      </div>

      <div style="display:grid; gap:12px; margin-bottom:28px;">
        <div style="display:flex; align-items:flex-start; gap:12px;">
          <div style="width:32px; height:32px; border-radius:999px; background:#111; color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0;">1</div>
          <div>
            <strong>Hacé click en "Conectar Mercado Pago"</strong>
            <div class="muted" style="font-size:13px; margin-top:2px;">Serás redirigido a Mercado Pago para autorizar la conexión.</div>
          </div>
        </div>
        <div style="display:flex; align-items:flex-start; gap:12px;">
          <div style="width:32px; height:32px; border-radius:999px; background:#111; color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0;">2</div>
          <div>
            <strong>Autorizá el acceso con tu cuenta MP</strong>
            <div class="muted" style="font-size:13px; margin-top:2px;">Usá la cuenta MP donde querés recibir los pagos de tus reservas.</div>
          </div>
        </div>
        <div style="display:flex; align-items:flex-start; gap:12px;">
          <div style="width:32px; height:32px; border-radius:999px; background:#111; color:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; flex-shrink:0;">3</div>
          <div>
            <strong>Listo — tu complejo queda activo</strong>
            <div class="muted" style="font-size:13px; margin-top:2px;">Podrás crear canchas, configurar horarios y empezar a recibir reservas.</div>
          </div>
        </div>
      </div>

      <a href="{{ route('va.mp_oauth.redirect', $venue) }}" class="btn btn-primary" style="font-size:16px; padding:13px 28px; border-radius:12px;">
        Conectar Mercado Pago →
      </a>
    </div>

    <div style="font-size:13px; color:#aaa; line-height:1.6; padding:0 4px;">
      Esta conexión es necesaria para operar. Sin ella no podrás acceder al panel ni gestionar reservas.
      Si tenés dudas, contactanos a <a href="mailto:tucancha10@gmail.com" style="color:#666;">tucancha10@gmail.com</a>.
    </div>

  </div>

@endsection
