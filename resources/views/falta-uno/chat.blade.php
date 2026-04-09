@extends('layouts.app')

@section('title', 'Chat · ' . $game->field->name)

@push('styles')
<style>
  /* ── Chat Layout ─────────────────────────────────────── */
  .chat-shell {
    max-width: 640px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--header-height, 64px) - 32px);
    min-height: 480px;
    padding: 0 var(--space-md);
  }

  /* ── Back Link ───────────────────────────────────────── */
  .chat-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: var(--text-sm);
    color: var(--color-text-muted);
    text-decoration: none;
    font-weight: var(--font-semibold);
    padding: var(--space-sm) 0;
    transition: color var(--transition-fast);
  }
  .chat-back:hover { color: var(--color-text); }
  .chat-back svg {
    width: 16px;
    height: 16px;
    transition: transform var(--transition-fast);
  }
  .chat-back:hover svg {
    transform: translateX(-3px);
  }

  /* ── Header Card (double-bezel) ──────────────────────── */
  .chat-header-outer {
    background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
    border-radius: var(--radius-lg);
    padding: 3px;
    margin-bottom: var(--space-md);
    box-shadow: 0 4px 20px rgba(0,0,0,.15), 0 0 0 1px rgba(255,255,255,.04);
  }
  .chat-header-inner {
    background: linear-gradient(135deg, #111 0%, #1e1e1e 100%);
    border-radius: calc(var(--radius-lg) - 2px);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
    color: var(--color-text-inverse);
  }
  .chat-header-icon {
    width: 42px;
    height: 42px;
    border-radius: var(--radius-md);
    background: rgba(34, 197, 94, .12);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .chat-header-icon svg {
    width: 22px;
    height: 22px;
    stroke: var(--color-primary);
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
  }
  .chat-header-info {
    flex: 1;
    min-width: 0;
  }
  .chat-header-info h2 {
    margin: 0;
    font-size: var(--text-base);
    font-weight: var(--font-bold);
    letter-spacing: -.01em;
    line-height: var(--leading-tight);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .chat-header-info p {
    margin: 3px 0 0;
    font-size: var(--text-xs);
    color: rgba(255,255,255,.5);
    line-height: var(--leading-snug);
  }
  .chat-badge-live {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: var(--font-bold);
    color: var(--color-primary-light);
    background: rgba(34,197,94,.1);
    border: 1px solid rgba(34,197,94,.2);
    border-radius: var(--radius-full);
    padding: 3px 10px 3px 8px;
    flex-shrink: 0;
    letter-spacing: .02em;
    text-transform: uppercase;
  }
  .chat-badge-live::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--color-primary);
    animation: chat-pulse 2s ease-in-out infinite;
  }
  @keyframes chat-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: .4; transform: scale(.75); }
  }

  /* ── Notice Banner ───────────────────────────────────── */
  .chat-notice {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(245, 179, 1, .08);
    border: 1px solid rgba(245, 179, 1, .15);
    border-radius: var(--radius-md);
    padding: 8px 14px;
    font-size: 11px;
    color: var(--color-text-secondary);
    font-weight: var(--font-medium);
    line-height: var(--leading-snug);
    margin-bottom: 2px;
    flex-shrink: 0;
  }
  .chat-notice svg {
    width: 14px;
    height: 14px;
    stroke: var(--color-warning);
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
    flex-shrink: 0;
  }

  /* ── Messages Area ───────────────────────────────────── */
  .chat-messages-wrap {
    position: relative;
    flex: 1;
    min-height: 0;
    margin: var(--space-sm) 0;
  }
  .chat-messages {
    height: 100%;
    overflow-y: auto;
    padding: var(--space-md) var(--space-sm);
    display: flex;
    flex-direction: column;
    gap: 6px;
    scroll-behavior: smooth;
    background-color: var(--color-bg-page);
    background-image:
      radial-gradient(circle at 20% 50%, rgba(34,197,94,.02) 0%, transparent 50%),
      radial-gradient(circle at 80% 20%, rgba(34,197,94,.015) 0%, transparent 50%);
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-border-light);
  }
  /* Scroll fade at top */
  .chat-messages-wrap::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 32px;
    background: linear-gradient(to bottom, var(--color-bg-page), transparent);
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    z-index: 2;
    pointer-events: none;
    opacity: 0;
    transition: opacity var(--transition-normal);
  }
  .chat-messages-wrap.has-scroll::before {
    opacity: 1;
  }

  /* ── Date Separator ──────────────────────────────────── */
  .chat-date-sep {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 12px 0 8px;
    user-select: none;
  }
  .chat-date-sep::before,
  .chat-date-sep::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--color-border-light);
  }
  .chat-date-sep span {
    font-size: 10px;
    font-weight: var(--font-semibold);
    color: var(--color-text-muted);
    text-transform: uppercase;
    letter-spacing: .06em;
    white-space: nowrap;
  }

  /* ── Message Row ─────────────────────────────────────── */
  .chat-msg {
    display: flex;
    gap: 8px;
    align-items: flex-end;
    max-width: 85%;
    animation: chat-msg-in .3s var(--ease-out-expo) both;
  }
  .chat-msg.own {
    flex-direction: row-reverse;
    align-self: flex-end;
  }
  .chat-msg:not(.own) {
    align-self: flex-start;
  }

  @keyframes chat-msg-in {
    from {
      opacity: 0;
      transform: translateY(8px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* ── Avatar ──────────────────────────────────────────── */
  .chat-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    flex-shrink: 0;
    background: var(--color-bg-dark);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: var(--font-bold);
    color: var(--color-text-inverse);
    overflow: hidden;
    box-shadow: 0 0 0 2px var(--color-bg-page);
  }
  .chat-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .chat-avatar.is-creator {
    box-shadow: 0 0 0 2px var(--color-primary);
  }

  /* ── Bubble ──────────────────────────────────────────── */
  .chat-bubble {
    max-width: 100%;
    padding: 8px 12px 6px;
    position: relative;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }

  /* Others' bubbles */
  .chat-msg:not(.own) .chat-bubble {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 4px 16px 16px 16px;
    color: var(--color-text);
  }

  /* Own bubbles */
  .chat-msg.own .chat-bubble {
    background: #0f5c32;
    border: 1px solid rgba(34, 197, 94, .2);
    border-radius: 16px 4px 16px 16px;
    color: var(--color-text-inverse);
  }

  .chat-bubble-name {
    font-size: 11px;
    font-weight: var(--font-bold);
    color: var(--color-primary-hover);
    margin-bottom: 2px;
    line-height: 1;
  }

  .chat-bubble-body {
    font-size: var(--text-sm);
    line-height: var(--leading-normal);
    padding-right: 42px; /* space for timestamp */
  }

  .chat-bubble-time {
    font-size: 10px;
    color: rgba(0,0,0,.35);
    float: right;
    margin-top: -14px;
    position: relative;
    line-height: 1;
    user-select: none;
  }
  .chat-msg.own .chat-bubble-time {
    color: rgba(255,255,255,.5);
  }

  /* ── Input Area ──────────────────────────────────────── */
  .chat-input-area {
    flex-shrink: 0;
    padding: var(--space-sm) 0 var(--space-md);
  }
  .chat-input-row {
    display: flex;
    gap: 8px;
    align-items: center;
  }
  .chat-input {
    flex: 1;
    padding: 12px 18px;
    border: 2px solid var(--color-border);
    border-radius: var(--radius-full);
    font-size: var(--text-sm);
    font-family: var(--font-family);
    background: var(--color-bg-card);
    outline: none;
    transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
    color: var(--color-text);
  }
  .chat-input::placeholder {
    color: var(--color-text-muted);
  }
  .chat-input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(34, 197, 94, .12);
  }

  .chat-send {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: var(--color-primary);
    color: var(--color-text-inverse);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: background var(--transition-fast), transform var(--transition-fast);
    will-change: transform;
  }
  .chat-send svg {
    width: 18px;
    height: 18px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
    transition: transform var(--transition-fast);
  }
  .chat-send:hover {
    background: var(--color-primary-hover);
  }
  .chat-send:hover svg {
    transform: rotate(-45deg) scale(1.05);
  }
  .chat-send:active {
    transform: scale(.92);
  }
  .chat-send:disabled {
    background: var(--color-border);
    cursor: not-allowed;
    transform: none;
  }
  .chat-send:disabled svg {
    transform: none;
    opacity: .5;
  }

  /* ── Empty State ─────────────────────────────────────── */
  .chat-empty {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: var(--color-text-muted);
    text-align: center;
    padding: var(--space-2xl) var(--space-md);
  }
  .chat-empty svg {
    width: 36px;
    height: 36px;
    stroke: var(--color-border);
    fill: none;
    stroke-width: 1.5;
    stroke-linecap: round;
    stroke-linejoin: round;
    margin-bottom: 4px;
  }
  .chat-empty p {
    font-size: var(--text-sm);
    margin: 0;
    line-height: var(--leading-snug);
  }

  /* ── Reduced Motion ──────────────────────────────────── */
  @media (prefers-reduced-motion: reduce) {
    .chat-msg { animation: none; }
    .chat-badge-live::before { animation: none; }
    .chat-messages { scroll-behavior: auto; }
    .chat-back svg,
    .chat-send svg,
    .chat-send,
    .chat-input { transition: none; }
  }

  /* ── Responsive: Tablet ──────────────────────────────── */
  @media (max-width: 768px) {
    .chat-shell {
      padding: 0 var(--space-sm);
      height: calc(100vh - var(--header-height, 64px) - 16px);
      height: calc(100dvh - var(--header-height, 64px) - 16px);
    }
    .chat-header-inner { padding: 14px 16px; gap: 12px; }
    .chat-header-icon { width: 38px; height: 38px; }
    .chat-header-icon svg { width: 20px; height: 20px; }
    .chat-header-info h2 { font-size: var(--text-sm); }
  }

  /* ── Responsive: Mobile ──────────────────────────────── */
  @media (max-width: 480px) {
    .chat-shell {
      padding: 0 var(--space-xs);
      height: calc(100vh - var(--header-height, 56px) - 8px);
      height: calc(100dvh - var(--header-height, 56px) - 8px);
    }
    .chat-back { font-size: var(--text-xs); padding: 6px 0; }
    .chat-header-outer { margin-bottom: var(--space-sm); }
    .chat-header-inner { padding: 12px 14px; gap: 10px; }
    .chat-header-icon { width: 34px; height: 34px; }
    .chat-header-icon svg { width: 18px; height: 18px; }
    .chat-header-info h2 { font-size: 13px; }
    .chat-header-info p { font-size: 11px; }
    .chat-badge-live { font-size: 10px; padding: 2px 8px 2px 6px; }
    .chat-notice { font-size: 10px; padding: 6px 10px; }
    .chat-messages { padding: 12px 8px; gap: 4px; }
    .chat-msg { max-width: 90%; }
    .chat-input { padding: 10px 14px; font-size: 13px; }
    .chat-send { width: 40px; height: 40px; }
    .chat-send svg { width: 16px; height: 16px; }
    .chat-input-area { padding: 6px 0 var(--space-sm); }
  }
</style>
@endpush

@section('content')

<div class="chat-shell" id="chatWrap">

  {{-- Back --}}
  <a href="{{ route('falta-uno.index') }}" class="chat-back">
    <svg viewBox="0 0 24 24"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
    Volver a partidos
  </a>

  {{-- Header (double-bezel) --}}
  <div class="chat-header-outer">
    <div class="chat-header-inner">
      <div class="chat-header-icon">
        <svg viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
      </div>
      <div class="chat-header-info">
        <h2>{{ $game->field->name }}</h2>
        <p>{{ $game->field->venue->name }} &middot; {{ \Carbon\Carbon::parse($game->start_at)->format('d/m/Y H:i') }} hs</p>
      </div>
      @if(\Carbon\Carbon::parse($game->start_at)->isFuture())
        <div class="chat-badge-live">Activo</div>
      @endif
    </div>
  </div>

  {{-- Notice --}}
  <div class="chat-notice">
    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    <span>El chat se elimina 3 horas después de que termine el partido.</span>
  </div>

  {{-- Messages --}}
  <div class="chat-messages-wrap" id="chatMessagesWrap">
    <div class="chat-messages" id="chatMessages">
      @if($messages->isEmpty())
        <div class="chat-empty">
          <svg viewBox="0 0 24 24"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M8 12h.01"/><path d="M12 12h.01"/><path d="M16 12h.01"/></svg>
          <p>Todavía no hay mensajes.<br>Sé el primero en escribir.</p>
        </div>
      @else
        @php $lastDate = null; @endphp
        @foreach($messages as $msg)
          @php
            $isOwn = $msg->user_id === auth()->id();
            $msgDate = $msg->created_at->format('Y-m-d');
          @endphp

          @if($lastDate !== $msgDate)
            <div class="chat-date-sep">
              <span>{{ $msg->created_at->translatedFormat('d M Y') }}</span>
            </div>
            @php $lastDate = $msgDate; @endphp
          @endif

          <div class="chat-msg {{ $isOwn ? 'own' : '' }}">
            <div class="chat-avatar{{ isset($game->creator_id) && $msg->user_id === $game->creator_id ? ' is-creator' : '' }}">
              @if($msg->user->avatar_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($msg->user->avatar_path) }}" alt="" loading="lazy">
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
      @endif

      {{-- Dynamic messages appended via JS --}}
      <div id="dynamicMessages"></div>
    </div>
  </div>

  {{-- Input --}}
  <div class="chat-input-area">
    <div class="chat-input-row">
      <input type="text"
             id="chatInput"
             class="chat-input"
             placeholder="Escribí tu mensaje..."
             maxlength="1000"
             autocomplete="off">
      <button id="chatSendBtn" class="chat-send" aria-label="Enviar mensaje">
        <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </div>
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

  /* ── Scroll helpers ──────────────────────────────────── */
  function scrollToBottom() {
    const el = document.getElementById('chatMessages');
    if (el) el.scrollTop = el.scrollHeight;
  }

  function updateScrollIndicator() {
    const wrap = document.getElementById('chatMessagesWrap');
    const el   = document.getElementById('chatMessages');
    if (!wrap || !el) return;
    wrap.classList.toggle('has-scroll', el.scrollTop > 16);
  }

  /* ── Build message HTML ──────────────────────────────── */
  function buildMessageHTML(msg) {
    const isOwn    = msg.user.id === AUTH_USER_ID;
    const avatar   = msg.user.avatar
      ? `<img src="${msg.user.avatar}" alt="" loading="lazy">`
      : `<span>${msg.user.name.charAt(0).toUpperCase()}</span>`;
    const nameRow  = isOwn ? '' : `<div class="chat-bubble-name">${escapeHtml(msg.user.name)}</div>`;
    const time     = new Date(msg.created_at).toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit' });

    return `
      <div class="chat-msg ${isOwn ? 'own' : ''}" style="animation-delay:.05s">
        <div class="chat-avatar">${avatar}</div>
        <div class="chat-bubble">
          ${nameRow}
          <div class="chat-bubble-body">${escapeHtml(msg.body)}</div>
          <div class="chat-bubble-time">${time}</div>
        </div>
      </div>`;
  }

  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  /* ── Append and remove empty state ───────────────────── */
  function appendMessage(msg) {
    // Remove empty state if present
    const empty = document.querySelector('.chat-empty');
    if (empty) empty.remove();

    const container = document.getElementById('dynamicMessages');
    container.insertAdjacentHTML('beforeend', buildMessageHTML(msg));
    scrollToBottom();
  }

  /* ── Send ─────────────────────────────────────────────── */
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

  /* ── Event listeners ─────────────────────────────────── */
  document.getElementById('chatSendBtn').addEventListener('click', sendMessage);
  document.getElementById('chatInput').addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  // Scroll indicator
  const chatEl = document.getElementById('chatMessages');
  if (chatEl) {
    chatEl.addEventListener('scroll', updateScrollIndicator, { passive: true });
  }

  /* ── Echo (real-time) ────────────────────────────────── */
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
        if (e.user && e.user.id === AUTH_USER_ID) return;
        appendMessage(e);
      });
  } catch (err) {
    console.warn('Echo no disponible:', err);
  }

  scrollToBottom();
</script>

@endsection
