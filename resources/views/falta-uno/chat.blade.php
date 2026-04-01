@extends('layouts.app')

@section('title', 'Chat · ' . $game->field->name)

@push('styles')
<style>
  .chat-wrap { max-width: 640px; margin: 0 auto; }
  .chat-header {
    background: linear-gradient(135deg,#111 0%,#2d2d2d 100%);
    border-radius: 18px;
    padding: 18px 22px;
    color: #fff;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .chat-header-info h2 { margin:0; font-size:16px; font-weight:800; letter-spacing:-.01em; }
  .chat-header-info p  { margin:0; font-size:12px; color:rgba(255,255,255,.6); }

  .chat-notice {
    background:#fffbeb;
    border:1px solid #fde68a;
    border-radius:10px;
    padding:10px 14px;
    font-size:12px;
    color:#92400e;
    margin-bottom:14px;
    font-weight:600;
  }

  .chat-messages {
    background:#fff;
    border:1px solid #ececec;
    border-radius:18px;
    padding:16px;
    min-height:360px;
    max-height:420px;
    overflow-y:auto;
    margin-bottom:12px;
    display:flex;
    flex-direction:column;
    gap:10px;
    scroll-behavior:smooth;
  }

  .chat-msg { display:flex; gap:10px; align-items:flex-start; }
  .chat-msg.own { flex-direction:row-reverse; }

  .chat-avatar {
    width:34px; height:34px; border-radius:50%; flex-shrink:0;
    background:#111; display:flex; align-items:center; justify-content:center;
    font-size:13px; font-weight:800; color:#fff; overflow:hidden;
  }
  .chat-avatar img { width:100%; height:100%; object-fit:cover; }

  .chat-bubble {
    max-width: 75%;
    background: #f4f4f4;
    border-radius: 14px 14px 14px 4px;
    padding: 8px 12px;
  }
  .chat-msg.own .chat-bubble {
    background: #111;
    color: #fff;
    border-radius: 14px 14px 4px 14px;
  }

  .chat-bubble-name { font-size:11px; font-weight:700; color:#888; margin-bottom:3px; }
  .chat-msg.own .chat-bubble-name { color:rgba(255,255,255,.6); }
  .chat-bubble-body { font-size:14px; line-height:1.4; }
  .chat-bubble-time { font-size:10px; color:#aaa; margin-top:4px; text-align:right; }
  .chat-msg.own .chat-bubble-time { color:rgba(255,255,255,.5); }

  .chat-input-row {
    display:flex; gap:8px;
  }
  .chat-input {
    flex:1;
    padding:10px 14px;
    border:1.5px solid #e0e0e0;
    border-radius:12px;
    font-size:14px;
    outline:none;
    transition:border-color .15s;
  }
  .chat-input:focus { border-color:#111; }
  .chat-send {
    padding:10px 18px;
    background:#111;
    color:#fff;
    border:none;
    border-radius:12px;
    font-size:14px;
    font-weight:700;
    cursor:pointer;
    transition:background .15s;
  }
  .chat-send:hover { background:#222; }
  .chat-send:disabled { background:#ccc; cursor:not-allowed; }

  @media (max-width: 400px) {
    .chat-input-row { gap: 6px; }
    .chat-input { padding: 10px 10px; font-size: 13px; }
    .chat-send { padding: 10px 12px; font-size: 13px; }
    .chat-messages { min-height: 280px; max-height: 340px; padding: 12px; }
    .chat-header { padding: 14px 16px; }
  }
</style>
@endpush

@section('content')

<div class="chat-wrap" id="chatWrap">

  {{-- Header --}}
  <div style="margin-bottom:14px;">
    <a href="{{ route('falta-uno.index') }}"
       style="display:inline-flex; align-items:center; gap:5px; font-size:13px; color:#888; text-decoration:none; font-weight:600;"
       onmouseover="this.style.color='#111'" onmouseout="this.style.color='#888'">
      <i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;vertical-align:middle;margin-right:4px;"></i> Volver a partidos
    </a>
  </div>

  <div class="chat-header">
    <div><i data-lucide="message-circle" style="width:24px;height:24px;stroke:#111;stroke-width:2;"></i></div>
    <div class="chat-header-info">
      <h2>Chat · {{ $game->field->name }}</h2>
      <p>{{ $game->field->venue->name }} · {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y H:i') }} hs</p>
    </div>
  </div>

  <div class="chat-notice">
    Este chat se eliminará automáticamente 3 horas después de que finalice el partido.
  </div>

  {{-- Messages --}}
  <div class="chat-messages" id="chatMessages">
    @foreach($messages as $msg)
    @php $isOwn = $msg->user_id === auth()->id(); @endphp
    <div class="chat-msg {{ $isOwn ? 'own' : '' }}">
      <div class="chat-avatar">
        @if($msg->user->avatar_path)
          <img src="{{ \Illuminate\Support\Facades\Storage::url($msg->user->avatar_path) }}" alt="">
        @else
          {{ mb_strtoupper(mb_substr($msg->user->name, 0, 1)) }}
        @endif
      </div>
      <div class="chat-bubble">
        @if(!$isOwn)<div class="chat-bubble-name">{{ $msg->user->name }}</div>@endif
        <div class="chat-bubble-body">{{ $msg->body }}</div>
        <div class="chat-bubble-time">{{ $msg->created_at->format('H:i') }}</div>
      </div>
    </div>
    @endforeach

    {{-- Dynamic messages appended via JS --}}
    <div id="dynamicMessages"></div>
  </div>

  {{-- Input --}}
  <div class="chat-input-row">
    <input type="text"
           id="chatInput"
           class="chat-input"
           placeholder="Escribí tu mensaje..."
           maxlength="1000">
    <button id="chatSendBtn" class="chat-send">
      Enviar
    </button>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.js"></script>
<script>
  const AUTH_USER_ID   = {{ auth()->id() }};
  const STORE_URL      = '{{ route('falta-uno.chat.store', $game) }}';
  const CSRF_TOKEN     = '{{ csrf_token() }}';
  const GAME_ID        = {{ $game->id }};

  let sending = false;

  function scrollToBottom() {
    const el = document.getElementById('chatMessages');
    if (el) el.scrollTop = el.scrollHeight;
  }

  function buildMessageHTML(msg) {
    const isOwn    = msg.user.id === AUTH_USER_ID;
    const avatar   = msg.user.avatar
      ? `<img src="${msg.user.avatar}" alt="">`
      : `<span>${msg.user.name.charAt(0).toUpperCase()}</span>`;
    const nameRow  = isOwn ? '' : `<div class="chat-bubble-name">${msg.user.name}</div>`;
    const time     = new Date(msg.created_at).toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });

    return `
      <div class="chat-msg ${isOwn ? 'own' : ''}">
        <div class="chat-avatar">${avatar}</div>
        <div class="chat-bubble">
          ${nameRow}
          <div class="chat-bubble-body">${msg.body.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</div>
          <div class="chat-bubble-time">${time}</div>
        </div>
      </div>`;
  }

  function appendMessage(msg) {
    const container = document.getElementById('dynamicMessages');
    container.insertAdjacentHTML('beforeend', buildMessageHTML(msg));
    scrollToBottom();
  }

  async function sendMessage() {
    const input = document.getElementById('chatInput');
    const btn   = document.getElementById('chatSendBtn');
    const body  = input.value.trim();

    if (!body || sending) return;

    sending     = true;
    btn.disabled = true;

    try {
      const res = await fetch(STORE_URL, {
        method:  'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF_TOKEN,
          'Accept':       'application/json',
        },
        body: JSON.stringify({ body }),
      });

      if (res.ok) {
        const msg = await res.json();
        input.value = '';
        appendMessage(msg);
      } else {
        const data = await res.json().catch(() => ({}));
        alert(data.message || 'Error al enviar el mensaje. Intentá de nuevo.');
      }
    } catch (e) {
      console.error(e);
      alert('Error de conexión. Verificá tu internet e intentá de nuevo.');
    } finally {
      sending      = false;
      btn.disabled = false;
      input.focus();
    }
  }

  document.getElementById('chatSendBtn').addEventListener('click', sendMessage);
  document.getElementById('chatInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  // Echo para mensajes de otros usuarios
  try {
    const echoChat = new Echo({
      broadcaster:       'reverb',
      key:               '{{ config('broadcasting.connections.reverb.key') }}',
      wsHost:            '{{ config('broadcasting.connections.reverb.client_host') }}',
      wsPort:            {{ config('broadcasting.connections.reverb.client_port') }},
      wssPort:           {{ config('broadcasting.connections.reverb.client_port') }},
      forceTLS:          true,
      enabledTransports: ['ws', 'wss'],
      authEndpoint:      '/broadcasting/auth',
    });

    echoChat.private('falta-uno.' + GAME_ID)
      .listen('.chat.message', (e) => {
        // Ignorar mensajes propios: ya se agregaron via optimistic update al enviar
        if (e.user && e.user.id === AUTH_USER_ID) return;
        appendMessage(e);
      });
  } catch (err) {
    console.warn('Echo no disponible:', err);
  }

  scrollToBottom();
</script>

@endsection
