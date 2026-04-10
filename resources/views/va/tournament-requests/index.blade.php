@extends('layouts.admin')

@section('title', 'Solicitudes de Torneos')
@section('page_title', 'Solicitudes de Torneos')

@push('styles')
<style>
  .tc-chat-badge {
    display: inline-flex; align-items: center; gap: 5px; padding: 5px 12px;
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
    font-size: 12px; font-weight: 700; color: #16a34a; cursor: pointer;
    transition: all .15s;
  }
  .tc-chat-badge:hover { background: #dcfce7; border-color: #86efac; }
  .tc-chat-badge svg { width: 14px; height: 14px; }
  .tc-chat-count {
    background: #16a34a; color: #fff; font-size: 10px; font-weight: 800;
    min-width: 18px; height: 18px; border-radius: 9px;
    display: inline-flex; align-items: center; justify-content: center; padding: 0 5px;
  }
</style>
@endpush

@section('content')

<div class="mb-6">
  <p class="text-sm text-slate-500">Gestiona las solicitudes de organizadores que quieren usar tus canchas para torneos.</p>
</div>

@if($requests->isEmpty())
  <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center">
    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H6.911a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661z"/>
    </svg>
    <p class="text-slate-500 font-medium">No hay solicitudes de torneos todavia.</p>
    <p class="text-slate-400 text-sm mt-1">Cuando un organizador solicite usar tu cancha para un torneo, aparecera aca.</p>
  </div>
@else
  <div class="space-y-4">
    @foreach($requests as $req)
      @php
        $contactMethod = $req->field?->tournamentSetting?->contact_method ?? 'internal';
        $isInternal = $contactMethod === 'internal';
        $chatMessages = $isInternal ? $req->messages : collect();
      @endphp
      <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100">
          <div>
            <h3 class="text-base font-bold text-slate-900">{{ $req->tournament->name ?? 'Torneo eliminado' }}</h3>
            <div class="flex flex-wrap items-center gap-2 mt-1">
              <span class="text-xs text-slate-500">
                Organizador: <strong>{{ $req->tournament->organizer->name ?? 'N/A' }}</strong>
              </span>
              <span class="text-xs text-slate-400">&middot;</span>
              <span class="text-xs text-slate-500">
                Cancha: <strong>{{ $req->field->name ?? 'N/A' }}</strong>
              </span>
              <span class="text-xs text-slate-400">&middot;</span>
              <span class="text-xs text-slate-500">
                {{ $req->created_at->diffForHumans() }}
              </span>
            </div>
          </div>

          <div class="flex items-center gap-2">
            {{-- Chat button for internal requests --}}
            @if($isInternal && $req->isPending())
              <button type="button" class="tc-chat-badge" onclick="openVaChat({{ $req->id }})">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Chat
                @php
                  $adminLastRead = $req->admin_last_read_at;
                  $vaUnread = $chatMessages->where('user_id', '!=', auth()->id())
                    ->when($adminLastRead, fn($c) => $c->where('created_at', '>', $adminLastRead))
                    ->count();
                @endphp
                @if($vaUnread > 0)
                  <span class="tc-chat-count" id="va-unread-badge-{{ $req->id }}">{{ $vaUnread }}</span>
                @endif
              </button>
            @endif

            {{-- Status badge --}}
            @php
              $statusConfig = [
                'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Pendiente'],
                'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Aprobada'],
                'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'label' => 'Rechazada'],
                'counter_proposed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'label' => 'Contrapropuesta'],
              ];
              $sc = $statusConfig[$req->status] ?? $statusConfig['pending'];
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $sc['bg'] }} {{ $sc['text'] }}">
              {{ $sc['label'] }}
            </span>
          </div>
        </div>

        <div class="px-6 py-4">
          {{-- Proposed dates --}}
          @if($req->proposed_dates)
            <div class="mb-3">
              <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fechas propuestas</span>
              <p class="text-sm text-slate-700 mt-0.5">
                @foreach($req->proposed_dates as $date)
                  {{ $date }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
              </p>
            </div>
          @endif

          {{-- Message --}}
          @if($req->message)
            <div class="mb-3">
              <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mensaje del organizador</span>
              <p class="text-sm text-slate-700 mt-0.5">{{ $req->message }}</p>
            </div>
          @endif

          {{-- Contact method info --}}
          @if($req->isPending() && !$isInternal)
            @php $contactValue = $req->field?->tournamentSetting?->contact_value ?? ''; @endphp
            <div class="mb-3 flex items-start gap-2 px-3 py-2.5 rounded-xl bg-blue-50 border border-blue-200">
              @if($contactMethod === 'whatsapp')
                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.625.846 5.059 2.284 7.034L.789 23.492a.5.5 0 00.612.638l4.603-1.209A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-2.352 0-4.55-.764-6.332-2.058l-.182-.137-3.223.846.862-3.149-.15-.237A9.935 9.935 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
                <p class="text-xs text-blue-800">El organizador se contactara por <strong>WhatsApp</strong> al <strong>{{ $contactValue }}</strong></p>
              @elseif($contactMethod === 'phone')
                <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <p class="text-xs text-blue-800">El organizador se contactara por <strong>telefono</strong> al <strong>{{ $contactValue }}</strong></p>
              @elseif($contactMethod === 'email')
                <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <p class="text-xs text-blue-800">El organizador se contactara por <strong>email</strong> a <strong>{{ $contactValue }}</strong></p>
              @endif
            </div>
          @endif

          {{-- Response message (if already responded) --}}
          @if($req->response_message)
            <div class="mb-3">
              <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tu respuesta</span>
              <p class="text-sm text-slate-700 mt-0.5">{{ $req->response_message }}</p>
            </div>
          @endif

          {{-- Actions for pending requests --}}
          @if($req->isPending())
            <div class="mt-4 pt-4 border-t border-slate-100" x-data="{ showReject: false }">
              <div class="flex flex-wrap items-center gap-3">
                {{-- Approve --}}
                <form method="POST" action="{{ route('va.tournament_requests.approve', $req) }}" class="inline">
                  @csrf
                  <button type="submit"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Aprobar
                  </button>
                </form>

                {{-- Reject toggle --}}
                <button type="button" @click="showReject = !showReject"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 text-sm font-bold rounded-xl border border-red-200 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Rechazar
                </button>
              </div>

              {{-- Reject form --}}
              <div x-show="showReject" x-transition class="mt-3">
                <form method="POST" action="{{ route('va.tournament_requests.reject', $req) }}">
                  @csrf
                  <textarea name="response_message" rows="2"
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-red-200 focus:border-red-400 outline-none resize-none mb-2"
                            placeholder="Motivo del rechazo (opcional)"></textarea>
                  <button type="submit"
                          class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl transition-colors">
                    Confirmar rechazo
                  </button>
                </form>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- Chat modal for internal requests --}}
      @if($isInternal && $req->isPending())
      <div id="va-chat-overlay-{{ $req->id }}"
           style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;padding:16px;"
           onclick="if(event.target===this)closeVaChat({{ $req->id }})">
        <div style="background:#fff;border-radius:20px;width:100%;max-width:480px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.25);">
          <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <div>
              <h3 style="font-size:15px;font-weight:800;color:#111;margin:0;">Chat — {{ $req->tournament->name ?? 'Torneo' }}</h3>
              <div style="font-size:12px;color:#94a3b8;margin-top:2px;">{{ $req->tournament->organizer->name ?? '' }} &middot; {{ $req->field->name ?? '' }}</div>
            </div>
            <button type="button" onclick="closeVaChat({{ $req->id }})"
                    style="width:32px;height:32px;border-radius:10px;border:none;background:#f1f5f9;cursor:pointer;display:flex;align-items:center;justify-content:center;">
              <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
          <div id="va-chat-body-{{ $req->id }}" style="flex:1;overflow-y:auto;padding:16px 20px;display:flex;flex-direction:column;gap:8px;min-height:200px;">
            @if($chatMessages->isEmpty())
              <div data-chat-empty style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#94a3b8;text-align:center;padding:20px;">
                <svg style="width:40px;height:40px;margin-bottom:8px;opacity:.5;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span style="font-size:13px;font-weight:600;">Sin mensajes todavia</span>
                <span style="font-size:12px;">Escribi un mensaje para coordinar con el organizador.</span>
              </div>
            @else
              @foreach($chatMessages as $msg)
              <div style="display:flex;flex-direction:column;{{ $msg->user_id === auth()->id() ? 'align-items:flex-end' : 'align-items:flex-start' }}">
                <div style="max-width:80%;padding:10px 14px;font-size:13px;line-height:1.5;word-break:break-word;{{ $msg->user_id === auth()->id() ? 'background:#16a34a;color:#fff;border-radius:16px 16px 4px 16px;' : 'background:#f1f5f9;color:#1e293b;border-radius:16px 16px 16px 4px;' }}">
                  {{ $msg->message }}
                </div>
                <span style="font-size:10px;color:#94a3b8;margin-top:3px;padding:0 4px;">{{ $msg->user->name ?? 'Usuario' }} &middot; {{ $msg->created_at->diffForHumans() }}</span>
              </div>
              @endforeach
            @endif
          </div>
          <div style="padding:12px 16px;border-top:1px solid #f1f5f9;">
            <form method="POST" action="{{ route('va.tournament_requests.message', $req) }}" data-chat-form="va-chat-body-{{ $req->id }}" style="display:flex;gap:8px;width:100%;">
              @csrf
              <input type="text" name="message" placeholder="Escribi un mensaje..." required maxlength="1000" autocomplete="off"
                     style="flex:1;padding:10px 14px;border:1px solid #e2e8f0;border-radius:12px;font-size:13px;font-family:inherit;outline:none;">
              <button type="submit"
                      style="width:40px;height:40px;border-radius:12px;border:none;background:#16a34a;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
              </button>
            </form>
          </div>
        </div>
      </div>
      @endif
    @endforeach
  </div>

  <div class="mt-6">
    {{ $requests->links() }}
  </div>
@endif

@endsection

@push('scripts')
<script>
function openVaChat(id) {
  var el = document.getElementById('va-chat-overlay-' + id);
  if (el) {
    el.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    var body = document.getElementById('va-chat-body-' + id);
    if (body) body.scrollTop = body.scrollHeight;
    // Mark as read
    var badge = document.getElementById('va-unread-badge-' + id);
    if (badge) badge.style.display = 'none';
    fetch('/torneos/solicitudes/' + id + '/leido', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    });
  }
}
function closeVaChat(id) {
  var el = document.getElementById('va-chat-overlay-' + id);
  if (el) {
    el.style.display = 'none';
    document.body.style.overflow = '';
  }
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('[id^="va-chat-overlay-"]').forEach(function(o) {
      if (o.style.display === 'flex') {
        o.style.display = 'none';
        document.body.style.overflow = '';
      }
    });
  }
});

// AJAX chat submit
document.querySelectorAll('[data-chat-form]').forEach(function(form) {
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    var input = form.querySelector('input[name="message"]');
    var msg = input.value.trim();
    if (!msg) return;
    var btn = form.querySelector('button[type="submit"]');
    btn.disabled = true; input.disabled = true;
    fetch(form.action, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ message: msg })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
      var body = document.getElementById(form.getAttribute('data-chat-form'));
      if (body) {
        var empty = body.querySelector('[data-chat-empty]');
        if (empty) empty.remove();
        var bubble = document.createElement('div');
        bubble.style.cssText = 'display:flex;flex-direction:column;align-items:flex-end;';
        bubble.innerHTML =
          '<div style="max-width:80%;padding:10px 14px;font-size:13px;line-height:1.5;word-break:break-word;background:#16a34a;color:#fff;border-radius:16px 16px 4px 16px;">' +
            data.message.replace(/</g,'&lt;').replace(/>/g,'&gt;') +
          '</div><span style="font-size:10px;color:#94a3b8;margin-top:3px;padding:0 4px;">' +
            data.user_name + ' &middot; ahora</span>';
        body.appendChild(bubble);
        body.scrollTop = body.scrollHeight;
      }
      input.value = '';
    })
    .catch(function() { alert('Error al enviar el mensaje.'); })
    .finally(function() { btn.disabled = false; input.disabled = false; input.focus(); });
  });
});

// Real-time chat via Echo
function vaAppendIncoming(bodyId, data) {
  var body = document.getElementById(bodyId);
  if (!body) return;
  var empty = body.querySelector('[data-chat-empty]');
  if (empty) empty.remove();
  var bubble = document.createElement('div');
  bubble.style.cssText = 'display:flex;flex-direction:column;align-items:flex-start;';
  bubble.innerHTML =
    '<div style="max-width:80%;padding:10px 14px;font-size:13px;line-height:1.5;word-break:break-word;background:#f1f5f9;color:#1e293b;border-radius:16px 16px 16px 4px;">' +
      data.message.replace(/</g,'&lt;').replace(/>/g,'&gt;') +
    '</div><span style="font-size:10px;color:#94a3b8;margin-top:3px;padding:0 4px;">' +
      data.user_name + ' &middot; ahora</span>';
  body.appendChild(bubble);
  body.scrollTop = body.scrollHeight;
}

if (window.Echo) {
  @foreach($requests as $req)
    @php $cm = $req->field?->tournamentSetting?->contact_method ?? 'internal'; @endphp
    @if($cm === 'internal' && $req->isPending())
    window.Echo.private('tournament-request.{{ $req->id }}')
      .listen('.chat.message', function(data) {
        if (data.user_id === {{ auth()->id() }}) return;
        vaAppendIncoming('va-chat-body-{{ $req->id }}', data);
      });
    @endif
  @endforeach
}
</script>
@endpush
