@extends('layouts.app')

@section('title', 'Mis notificaciones')

@section('content')
<div style="max-width:700px; margin:0 auto;">

  <div class="page-card" style="margin-bottom:22px; display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap;">
    <div>
      <h1 style="margin:0 0 6px; font-size:32px; letter-spacing:-0.02em;">Notificaciones</h1>
      <p class="muted" style="margin:0;">Todas tus notificaciones en un solo lugar.</p>
    </div>
    @if(auth()->user()->unreadNotifications()->exists())
      <form method="POST" action="{{ route('notifications.mark_all_read') }}" style="margin:0;">
        @csrf
        <button type="submit" class="btn">Marcar todas como leídas</button>
      </form>
    @endif
  </div>

  @if($notifications->isEmpty())
    <div class="page-card" style="text-align:center; padding:56px 24px;">
      <div style="width:72px; height:72px; margin:0 auto 18px; background:linear-gradient(135deg, rgba(34,197,94,.1), rgba(34,197,94,.15)); border-radius:50%; display:flex; align-items:center; justify-content:center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
      </div>
      <p style="font-size:18px; font-weight:800; margin:0 0 8px; letter-spacing:-0.01em;">Todo tranquilo por acá</p>
      <p style="color:#666; font-size:14px; margin:0 0 20px; max-width:320px; margin-left:auto; margin-right:auto; line-height:1.6;">
        Cuando reserves una cancha, te unas a un Falta Uno o recibas calificaciones, vas a ver las novedades acá.
      </p>
      <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
        <a href="{{ route('venues.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:#111; color:#fff; border-radius:12px; font-size:14px; font-weight:700; text-decoration:none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          Explorar complejos
        </a>
        <a href="{{ route('falta-uno.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:10px 20px; background:rgba(34,197,94,.1); color:#6ee7a0; border:1.5px solid rgba(34,197,94,.2); border-radius:12px; font-size:14px; font-weight:700; text-decoration:none;">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          Ver partidos Falta Uno
        </a>
      </div>
    </div>
  @else
    <div style="background:#111; border:1px solid rgba(255,255,255,.08); border-radius:18px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.15);">
      @foreach($notifications as $notif)
        <form method="POST" action="{{ route('notifications.read', $notif->id) }}" style="margin:0;">
          @csrf
          <button type="submit"
                  style="display:flex; align-items:flex-start; gap:14px; padding:16px 20px; width:100%; text-align:left; border:0; border-bottom:1px solid rgba(255,255,255,.06); background:{{ $notif->read_at ? '#111' : 'rgba(59,130,246,.08)' }}; font-family:inherit; cursor:pointer; transition:background .12s; color:#e8e8e8;"
                  onmouseover="this.style.background='{{ $notif->read_at ? 'rgba(255,255,255,.02)' : 'rgba(59,130,246,.12)' }}'"
                  onmouseout="this.style.background='{{ $notif->read_at ? '#111' : 'rgba(59,130,246,.08)' }}'">
            <span style="font-size:22px; line-height:1; flex-shrink:0; margin-top:2px;">{{ $notif->data['icon'] ?? '🔔' }}</span>
            <div style="flex:1; min-width:0;">
              <div style="font-size:14px; font-weight:{{ $notif->read_at ? '600' : '800' }}; color:#e8e8e8; margin-bottom:3px;">
                {{ $notif->data['title'] ?? '' }}
              </div>
              <div style="font-size:13px; color:#a0a0a0; line-height:1.5; margin-bottom:4px;">
                {{ $notif->data['body'] ?? '' }}
              </div>
              <div style="font-size:11px; color:#666;">
                {{ $notif->created_at->diffForHumans() }}
              </div>
            </div>
            @if(!$notif->read_at)
              <span style="width:9px; height:9px; border-radius:50%; background:#3b82f6; flex-shrink:0; margin-top:6px;"></span>
            @endif
          </button>
        </form>
      @endforeach
    </div>

    <div style="margin-top:20px; display:flex; justify-content:center;">
      {{ $notifications->links() }}
    </div>
  @endif

</div>
@endsection
