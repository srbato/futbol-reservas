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
    <div class="page-card" style="text-align:center; padding:48px 24px;">
      <div style="font-size:40px; margin-bottom:12px;">🔔</div>
      <p style="font-size:16px; font-weight:700; margin:0 0 6px;">Sin notificaciones</p>
      <p style="color:#888; font-size:14px; margin:0;">Cuando haya novedades, aparecerán acá.</p>
    </div>
  @else
    <div style="background:#fff; border:1px solid #ececec; border-radius:18px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.03);">
      @foreach($notifications as $notif)
        <form method="POST" action="{{ route('notifications.read', $notif->id) }}" style="margin:0;">
          @csrf
          <button type="submit"
                  style="display:flex; align-items:flex-start; gap:14px; padding:16px 20px; width:100%; text-align:left; border:0; border-bottom:1px solid #f5f5f5; background:{{ $notif->read_at ? '#fff' : '#f0f7ff' }}; font-family:inherit; cursor:pointer; transition:background .12s;"
                  onmouseover="this.style.background='{{ $notif->read_at ? '#fafafa' : '#e4f0fd' }}'"
                  onmouseout="this.style.background='{{ $notif->read_at ? '#fff' : '#f0f7ff' }}'">
            <span style="font-size:22px; line-height:1; flex-shrink:0; margin-top:2px;">{{ $notif->data['icon'] ?? '🔔' }}</span>
            <div style="flex:1; min-width:0;">
              <div style="font-size:14px; font-weight:{{ $notif->read_at ? '600' : '800' }}; color:#111; margin-bottom:3px;">
                {{ $notif->data['title'] ?? '' }}
              </div>
              <div style="font-size:13px; color:#666; line-height:1.5; margin-bottom:4px;">
                {{ $notif->data['body'] ?? '' }}
              </div>
              <div style="font-size:11px; color:#aaa;">
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
