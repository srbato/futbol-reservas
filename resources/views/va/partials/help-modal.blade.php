{{--
  Parámetros:
    $helpKey   – clave única de localStorage (ej. 'va_dashboard')
    $helpTitle – título del modal
    $helpText  – texto explicativo (puede tener HTML básico)
--}}
<div id="help-modal-{{ $helpKey }}" style="
  display:none;
  position:fixed; inset:0; z-index:9999;
  background:rgba(0,0,0,.45);
  backdrop-filter:blur(3px);
  align-items:center;
  justify-content:center;
  padding:20px;
">
  <div style="
    background:#fff;
    border-radius:20px;
    padding:32px 28px 24px;
    max-width:460px;
    width:100%;
    box-shadow:0 20px 60px rgba(0,0,0,.18);
    position:relative;
    animation: helpModalIn .22s ease;
  ">
    <div style="font-size:28px; margin-bottom:12px;">💡</div>
    <h3 style="margin:0 0 10px; font-size:20px; font-weight:800; letter-spacing:-.02em;">
      {{ $helpTitle }}
    </h3>
    <p style="margin:0 0 24px; font-size:14px; color:#555; line-height:1.65;">
      {!! $helpText !!}
    </p>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <button
        onclick="helpModalDismiss('{{ $helpKey }}', false)"
        style="flex:1; padding:11px 16px; border-radius:12px; border:1.5px solid #e0e0e0; background:#fff; font-size:13px; font-weight:700; color:#666; cursor:pointer; font-family:inherit; transition:background .15s;"
        onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='#fff'"
      >
        Entendido
      </button>
      <button
        onclick="helpModalDismiss('{{ $helpKey }}', true)"
        style="flex:1; padding:11px 16px; border-radius:12px; border:none; background:#111; font-size:13px; font-weight:700; color:#fff; cursor:pointer; font-family:inherit; transition:background .15s;"
        onmouseover="this.style.background='#333'" onmouseout="this.style.background='#111'"
      >
        No mostrar nunca más
      </button>
    </div>
  </div>
</div>

<style>
  @keyframes helpModalIn {
    from { opacity:0; transform:translateY(12px) scale(.97); }
    to   { opacity:1; transform:translateY(0)   scale(1);    }
  }
</style>

<script>
  (function() {
    var key = 'help_dismissed_{{ $helpKey }}';
    if (localStorage.getItem(key)) return;
    var el = document.getElementById('help-modal-{{ $helpKey }}');
    el.style.display = 'flex';
  })();

  function helpModalDismiss(pageKey, permanent) {
    if (permanent) {
      localStorage.setItem('help_dismissed_' + pageKey, '1');
    }
    var el = document.getElementById('help-modal-' + pageKey);
    el.style.opacity = '0';
    el.style.transition = 'opacity .15s';
    setTimeout(function() { el.style.display = 'none'; }, 150);
  }
</script>
