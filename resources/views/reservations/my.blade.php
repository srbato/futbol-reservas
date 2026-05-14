@php
  use App\Support\ReservationStatus;
  use Carbon\Carbon;
@endphp

@extends('layouts.app')

@section('title', 'Mi actividad — TuCancha')

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@200;300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
@endpush

@php
  $sportLabel = fn($s) => match($s) {
    'football'   => 'Fútbol',
    'padel'      => 'Pádel',
    'tennis'     => 'Tenis',
    'basketball' => 'Básquet',
    'volleyball' => 'Vóley',
    default      => ucfirst($s ?? ''),
  };

  // Tag de status para reservas
  $statusBadge = function($status) {
    return match($status) {
      'PAID'           => ['ok',     'Pagado'],
      'PENDING_CASH'   => ['cash',   'Pago en complejo'],
      'PENDING_PAYMENT'=> ['warn',   'Pago pendiente'],
      'CANCELLED'      => ['danger', 'Cancelado'],
      'EXPIRED'        => ['muted',  'Expirado'],
      default          => ['muted',  ucfirst(strtolower($status))],
    };
  };

  $avatarGradient = function($name) {
    $palette = [
      ['#4ade80', '#22a55a'], ['#7abef5', '#2a6aaa'], ['#fda4af', '#be123c'],
      ['#a78bfa', '#5a3da8'], ['#f5c17a', '#a88844'], ['#94e8c4', '#33996c'],
      ['#fcb46e', '#c0712a'], ['#82e0e5', '#319196'], ['#f9a8d4', '#9d174d'],
    ];
    return $palette[abs(crc32((string) $name)) % count($palette)];
  };

  // Para el sport icon
  $sportIcon = function($sport) {
    return match($sport) {
      'padel', 'tennis' => '<rect x="4" y="6" width="16" height="12" rx="2"/><path d="M4 12h16"/>',
      default           => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a14 14 0 0 1 0 20M12 2a14 14 0 0 0 0 20"/>',
    };
  };

  // Particionar reservas
  $upcoming   = $reservations->filter(fn($r) => in_array($r->status, ['PAID','PENDING_CASH','PENDING_PAYMENT']) && $r->start_at->isFuture());
  $past       = $reservations->filter(fn($r) => $r->status === 'PAID' && $r->start_at->isPast())->take(10);
  $cancelled  = $reservations->filter(fn($r) => in_array($r->status, ['CANCELLED','EXPIRED']))->take(10);
  $pendingPay = $reservations->filter(fn($r) => $r->status === 'PENDING_PAYMENT' && $r->expires_at && $r->expires_at->isFuture());

  // Agrupar upcoming por bucket: mañana / esta semana / más adelante
  $tomorrow = now()->copy()->addDay()->endOfDay();
  $weekEnd  = now()->copy()->endOfWeek(Carbon::SUNDAY);
  $upTomorrow = $upcoming->filter(fn($r) => $r->start_at->lte($tomorrow));
  $upWeek     = $upcoming->filter(fn($r) => $r->start_at->gt($tomorrow) && $r->start_at->lte($weekEnd));
  $upLater    = $upcoming->filter(fn($r) => $r->start_at->gt($weekEnd));

  // FU partitioning
  $fuUpcoming  = $misPartidos->filter(fn($g) => $g->start_at->isFuture() && in_array($g->status, ['open','full']));
  $fuCancelled = $misPartidos->filter(fn($g) => in_array($g->status, ['cancelled','expired']))->take(5);
  $fuHistory   = $misPartidos->filter(fn($g) => $g->start_at->isPast() && !in_array($g->status, ['cancelled','expired']))->take(8);

  // Recurring subs partitioning
  $subsActive    = $recurringSubscriptions->where('status', 'ACTIVE');
  $subsPaused    = $recurringSubscriptions->whereIn('status', ['PAUSED']);
  $subsFinished  = $recurringSubscriptions->whereIn('status', ['CANCELLED','FINISHED']);

  // Categorías legibles
  $categoryLabel = fn($cat) => match(strtolower($cat ?? '')) {
    'recreativo'   => 'Recreativo',
    'intermedio'   => 'Intermedio',
    'avanzado'     => 'Avanzado',
    'competitivo'  => 'Competitivo',
    default        => ucfirst($cat ?? '—'),
  };
@endphp

@push('styles')
<style>
  /* ── Mi Actividad — design v2 ─────────────────────────────────────────── */
  .my-scope {
    --my-bg:#050505; --my-bg-1:#0a0a0a; --my-bg-2:#111; --my-bg-3:#161616;
    --my-bd:rgba(255,255,255,.07); --my-bd-2:rgba(255,255,255,.14);
    --my-tx:#f2f2f2; --my-tx-2:#c8c8c8; --my-tx-3:#8a8a8a; --my-tx-4:#555;
    --my-accent:#4ade80; --my-accent-ink:#052010; --my-accent-hover:#6ee7a0;
    --my-accent-soft:rgba(74,222,128,.08);
    --my-warn:#f5c17a; --my-danger:#f87171; --my-blue:#7abef5; --my-purple:#a78bfa;
    --my-mono:'JetBrains Mono', ui-monospace, monospace;
    background: var(--my-bg); color: var(--my-tx);
    font-family: 'Sora', system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
  }
  .my-scope a { color: inherit; text-decoration: none; }
  .my-scope button { font-family: inherit; cursor: pointer; }
  .my-scope ::selection { background: var(--my-accent); color: var(--my-accent-ink); }

  .my-page {
    max-width: 1320px;
    margin: 0 auto;
    padding: 36px 36px 80px;
  }

  /* HEAD */
  .my-head {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 24px; margin-bottom: 36px;
  }
  .my-eyebrow {
    font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase;
    color: var(--my-tx-3); margin-bottom: 8px;
  }
  .my-title { font-size: clamp(36px, 5vw, 56px); font-weight: 200; letter-spacing: -0.04em; line-height: .95; margin: 0; }
  .my-title b { font-weight: 600; background: linear-gradient(135deg, #4ade80, #6ee7a0); -webkit-background-clip: text; background-clip: text; color: transparent; }
  .my-sub { color: var(--my-tx-3); font-size: 15px; margin: 14px 0 0; max-width: 540px; }
  .my-cta-new {
    background: var(--my-accent); color: var(--my-accent-ink);
    padding: 12px 22px; border-radius: 12px; border: 0;
    font-size: 14px; font-weight: 600;
    display: inline-flex; align-items: center; gap: 8px;
    text-decoration: none; transition: background .15s;
  }
  .my-cta-new:hover { background: var(--my-accent-hover); }

  /* STATS */
  .my-stats {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
    margin-bottom: 36px;
  }
  .my-stat {
    background: var(--my-bg-1); border: 1px solid var(--my-bd);
    border-radius: 14px; padding: 18px 20px;
  }
  .my-stat-k { font-size: 10px; letter-spacing: .12em; text-transform: uppercase; color: var(--my-tx-3); margin-bottom: 12px; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; }
  .my-stat-ico { width: 6px; height: 6px; border-radius: 50%; background: var(--my-accent); }
  .my-stat-ico.purple { background: var(--my-purple); }
  .my-stat-ico.blue   { background: var(--my-blue); }
  .my-stat-ico.warn   { background: var(--my-warn); }
  .my-stat-v { font-size: 36px; font-weight: 200; letter-spacing: -0.03em; color: var(--my-tx); line-height: 1; font-variant-numeric: tabular-nums; }
  .my-stat-v sub { font-size: 13px; color: var(--my-tx-3); font-weight: 400; margin-left: 4px; vertical-align: 2px; letter-spacing: 0; }
  .my-stat-meta { font-size: 11px; color: var(--my-tx-3); margin-top: 8px; }
  .my-stat-meta b { color: var(--my-tx-2); font-weight: 500; }
  .my-stat-meta .up { color: var(--my-accent); }

  /* TABS */
  .my-tabs {
    display: flex; gap: 4px;
    background: var(--my-bg-1); border: 1px solid var(--my-bd);
    border-radius: 14px; padding: 6px;
    margin-bottom: 22px;
    overflow-x: auto;
    scrollbar-width: none;
  }
  .my-tabs::-webkit-scrollbar { display: none; }
  .my-tab {
    flex: 1; min-width: max-content;
    padding: 11px 16px; border: 0; background: transparent;
    font-size: 13px; font-weight: 500; color: var(--my-tx-3);
    border-radius: 9px; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    transition: background .15s, color .15s;
  }
  .my-tab:hover { color: var(--my-tx-2); }
  .my-tab.active { background: rgba(255,255,255,.06); color: var(--my-tx); }
  .my-tab .count { font-size: 11px; padding: 1px 7px; border-radius: 999px; background: rgba(255,255,255,.06); color: var(--my-tx-3); font-variant-numeric: tabular-nums; }
  .my-tab.active .count { background: var(--my-accent-soft); color: var(--my-accent); }

  /* BODY */
  .my-body {
    display: grid; grid-template-columns: 1fr 320px;
    gap: 28px;
  }

  /* PANEL */
  .my-panel { display: none; }
  .my-panel.on { display: block; }

  /* SUB FILTERS */
  .my-subfilters { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; margin-bottom: 18px; }
  .my-sub-chip {
    padding: 6px 12px; background: rgba(255,255,255,.04);
    border: 1px solid var(--my-bd); border-radius: 9px;
    font-size: 12px; font-weight: 500; color: var(--my-tx-2);
    transition: background .15s, color .15s, border-color .15s;
    cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
  }
  .my-sub-chip:hover { background: rgba(255,255,255,.08); color: var(--my-tx); }
  .my-sub-chip.active { background: rgba(74,222,128,.06); border-color: rgba(74,222,128,.2); color: var(--my-accent); }
  .my-sub-divider { width: 1px; height: 22px; background: var(--my-bd); margin: 0 6px; }

  /* TIMELINE GROUP */
  .my-timeline { margin-bottom: 30px; }
  .my-timeline-head {
    display: flex; align-items: center; gap: 10px; margin-bottom: 14px;
    font-size: 13px;
  }
  .my-timeline-head h2 {
    font-size: 13px; font-weight: 500; color: var(--my-tx-2);
    margin: 0; display: inline-flex; align-items: center; gap: 8px;
    letter-spacing: -0.005em;
  }
  .my-timeline-head h2 .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--my-accent); }
  .my-timeline-head h2.history .dot { background: var(--my-tx-3); }
  .my-timeline-head h2.cancel  .dot { background: var(--my-danger); }
  .my-timeline-head .ct { font-size: 11px; color: var(--my-tx-3); }
  .my-timeline-head .ln { flex: 1; height: 1px; background: var(--my-bd); }

  /* TURN CARD (full) */
  .my-turn {
    background: var(--my-bg-1); border: 1px solid var(--my-bd);
    border-radius: 14px; overflow: hidden;
    margin-bottom: 12px;
    transition: border-color .15s;
  }
  .my-turn:hover { border-color: var(--my-bd-2); }
  .my-turn-head {
    display: grid; grid-template-columns: 64px 1fr auto;
    gap: 18px; align-items: center;
    padding: 18px 20px;
  }
  .my-turn-date {
    text-align: center; padding: 6px 0;
    background: rgba(255,255,255,.03); border-radius: 10px;
  }
  .my-turn-date .day { font-size: 9px; letter-spacing: .12em; text-transform: uppercase; color: var(--my-tx-3); display: block; font-weight: 600; }
  .my-turn-date .d { font-size: 22px; font-weight: 300; color: var(--my-tx); line-height: 1; display: block; margin: 2px 0; font-variant-numeric: tabular-nums; }
  .my-turn-date .m { font-size: 9px; letter-spacing: .12em; text-transform: uppercase; color: var(--my-tx-3); display: block; font-weight: 600; }
  .my-turn-info { min-width: 0; }
  .my-turn-title {
    font-size: 16px; font-weight: 500; color: var(--my-tx);
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    margin-bottom: 6px; letter-spacing: -0.01em;
  }
  .my-turn-meta {
    font-size: 12px; color: var(--my-tx-3);
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
  }
  .my-turn-meta span { display: inline-flex; align-items: center; gap: 5px; }
  .my-turn-meta b { color: var(--my-tx-2); font-weight: 500; }
  .my-turn-meta svg { opacity: .65; }
  .my-turn-cta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

  .my-tag {
    padding: 2px 10px; border-radius: 999px;
    font-size: 10px; font-weight: 500; letter-spacing: .04em;
    background: rgba(255,255,255,.04); border: 1px solid var(--my-bd);
    color: var(--my-tx-2); display: inline-flex; align-items: center; gap: 4px;
  }
  .my-tag.ok     { color: var(--my-accent); background: rgba(74,222,128,.06); border-color: rgba(74,222,128,.2); }
  .my-tag.warn   { color: var(--my-warn); background: rgba(245,193,122,.06); border-color: rgba(245,193,122,.2); }
  .my-tag.cash   { color: var(--my-blue); background: rgba(122,190,245,.06); border-color: rgba(122,190,245,.2); }
  .my-tag.danger { color: var(--my-danger); background: rgba(248,113,113,.06); border-color: rgba(248,113,113,.2); }
  .my-tag.muted  { color: var(--my-tx-3); }

  /* Turn body (expanded info — code + tags) */
  .my-turn-body {
    border-top: 1px solid var(--my-bd);
    padding: 16px 20px;
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 30px;
    background: rgba(0,0,0,.2);
  }
  .my-turn-payment-head { font-size: 9px; letter-spacing: .14em; text-transform: uppercase; color: var(--my-tx-3); font-weight: 600; margin-bottom: 8px; }
  .my-turn-code-row { display: flex; align-items: center; gap: 10px; }
  .my-turn-code {
    font-family: var(--my-mono); font-size: 18px; letter-spacing: .12em;
    color: var(--my-tx); padding: 8px 14px;
    background: rgba(74,222,128,.04); border: 1px dashed rgba(74,222,128,.2);
    border-radius: 8px; display: inline-flex; align-items: center; gap: 12px;
  }
  .my-turn-code-copy {
    background: transparent; border: 0; color: var(--my-tx-3);
    padding: 4px; border-radius: 5px; transition: color .15s, background .15s;
  }
  .my-turn-code-copy:hover { color: var(--my-accent); background: rgba(74,222,128,.06); }
  .my-turn-payment-status {
    font-size: 11px; color: var(--my-tx-3); margin-top: 8px;
    display: inline-flex; align-items: center; gap: 6px;
  }
  .my-turn-payment-status .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--my-accent); animation: my-pulse 2s infinite; }
  .my-turn-payment-status .dot.pending { background: var(--my-warn); }
  .my-turn-tags { display: flex; gap: 6px; flex-wrap: wrap; }

  @keyframes my-pulse {
    0%,100% { box-shadow: 0 0 0 0 rgba(74,222,128,.4); }
    70%     { box-shadow: 0 0 0 5px rgba(74,222,128,0); }
  }

  /* CARD compact (esta semana / historial) */
  .my-card {
    background: var(--my-bg-1); border: 1px solid var(--my-bd);
    border-radius: 12px; padding: 14px 18px;
    display: grid; grid-template-columns: 36px 1fr auto;
    gap: 14px; align-items: center;
    margin-bottom: 8px;
    transition: border-color .15s;
  }
  .my-card:hover { border-color: var(--my-bd-2); }
  .my-card.cancelled { opacity: .7; }
  .my-card-ico {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--my-accent-soft); color: var(--my-accent);
    display: inline-flex; align-items: center; justify-content: center; flex: none;
  }
  .my-card-ico.cancel  { background: rgba(248,113,113,.08); color: var(--my-danger); }
  .my-card-ico.history { background: rgba(255,255,255,.04); color: var(--my-tx-3); }
  .my-card-ico.padel   { background: rgba(167,139,250,.08); color: var(--my-purple); }
  .my-card-ico.tenis   { background: rgba(122,190,245,.08); color: var(--my-blue); }
  .my-card-main { min-width: 0; }
  .my-card-title-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .my-card-title { font-size: 14px; font-weight: 500; color: var(--my-tx); }
  .my-card-meta {
    font-size: 11px; color: var(--my-tx-3);
    display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
    margin-top: 2px;
  }
  .my-card-meta b { color: var(--my-tx-2); font-weight: 500; }
  .my-card-meta .sep { width: 2px; height: 2px; background: var(--my-tx-4); border-radius: 50%; flex: none; }
  .my-card-aside { display: flex; align-items: center; gap: 8px; }

  /* BUTTONS */
  .my-btn {
    padding: 8px 14px; border-radius: 9px; border: 0; cursor: pointer;
    font-size: 12px; font-weight: 500; font-family: inherit;
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    text-decoration: none; transition: background .15s, color .15s, border-color .15s;
  }
  .my-btn-prim { background: var(--my-accent); color: var(--my-accent-ink); font-weight: 600; }
  .my-btn-prim:hover { background: var(--my-accent-hover); }
  .my-btn-warn { background: var(--my-warn); color: #2a1500; font-weight: 600; }
  .my-btn-warn:hover { background: #e8a85a; }
  .my-btn-ghost {
    background: rgba(255,255,255,.04); border: 1px solid var(--my-bd);
    color: var(--my-tx-2);
  }
  .my-btn-ghost:hover { background: rgba(255,255,255,.08); color: var(--my-tx); border-color: var(--my-bd-2); }
  .my-btn-danger-ghost {
    background: transparent; border: 1px solid rgba(248,113,113,.24);
    color: var(--my-danger);
  }
  .my-btn-danger-ghost:hover { background: rgba(248,113,113,.08); }

  /* RECURRING */
  .my-recurring {
    background: var(--my-bg-1); border: 1px solid var(--my-bd);
    border-radius: 14px; overflow: hidden;
    margin-bottom: 14px;
    transition: border-color .15s;
  }
  .my-recurring-head {
    display: grid; grid-template-columns: 48px 1fr auto auto;
    gap: 18px; align-items: center;
    padding: 20px 22px;
  }
  .my-recurring-ico {
    width: 48px; height: 48px; border-radius: 12px;
    background: rgba(167,139,250,.08); color: var(--my-purple);
    display: inline-flex; align-items: center; justify-content: center;
  }
  .my-recurring-info h3 { font-size: 15px; font-weight: 500; color: var(--my-tx); margin: 0 0 6px; letter-spacing: -0.01em; }
  .my-recurring-info .meta {
    display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
    font-size: 12px; color: var(--my-tx-3);
  }
  .my-recurring-info .meta span { display: inline-flex; align-items: center; gap: 5px; }
  .my-recurring-info .meta b { color: var(--my-tx-2); font-weight: 500; }
  .my-recurring-amount { text-align: right; }
  .my-recurring-amount .k { font-size: 9px; letter-spacing: .14em; text-transform: uppercase; color: var(--my-tx-3); margin-bottom: 4px; font-weight: 600; }
  .my-recurring-amount .v { font-size: 18px; color: var(--my-tx); font-weight: 500; font-variant-numeric: tabular-nums; }
  .my-recurring-body {
    border-top: 1px solid var(--my-bd);
    padding: 16px 22px;
    background: rgba(0,0,0,.2);
  }
  .my-recurring-body-head {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 9px; letter-spacing: .14em; text-transform: uppercase; color: var(--my-tx-3); font-weight: 600; margin-bottom: 12px;
  }
  .my-recurring-body-head .next-pay { color: var(--my-warn); }
  .my-recurring-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 8px; margin-bottom: 14px; }
  .my-recurring-slot {
    background: rgba(255,255,255,.03); border: 1px solid var(--my-bd);
    border-radius: 9px; padding: 10px 12px;
  }
  .my-recurring-slot.next { border-color: rgba(74,222,128,.3); background: rgba(74,222,128,.03); }
  .my-recurring-slot .date { font-size: 12px; color: var(--my-tx); font-weight: 500; }
  .my-recurring-slot .time { font-size: 11px; color: var(--my-tx-3); margin-top: 1px; }
  .my-recurring-actions { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }

  /* FU CARD */
  .my-fu {
    background: var(--my-bg-1); border: 1px solid var(--my-bd);
    border-radius: 12px; padding: 14px 18px;
    display: grid; grid-template-columns: 36px 1fr auto auto;
    gap: 14px; align-items: center;
    margin-bottom: 8px;
    transition: border-color .15s;
  }
  .my-fu:hover { border-color: var(--my-bd-2); }
  .my-fu.cancelled { opacity: .7; }
  .my-fu-ico {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--my-accent-soft); color: var(--my-accent);
    display: inline-flex; align-items: center; justify-content: center; flex: none;
  }
  .my-fu-info { min-width: 0; }
  .my-fu-title { font-size: 14px; font-weight: 500; color: var(--my-tx); margin-bottom: 2px; letter-spacing: -0.01em; }
  .my-fu-meta { font-size: 11px; color: var(--my-tx-3); display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .my-fu-meta b { color: var(--my-tx-2); font-weight: 500; }
  .my-fu-meta .sep { width: 2px; height: 2px; background: var(--my-tx-4); border-radius: 50%; flex: none; }
  .my-fu-roster { display: flex; align-items: center; }
  .my-fu-ava {
    width: 24px; height: 24px; border-radius: 50%;
    border: 2px solid var(--my-bg-1);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 9px; font-weight: 700; color: #000;
    margin-left: -6px; flex: none;
  }
  .my-fu-ava:first-child { margin-left: 0; }
  .my-fu-ava.empty {
    background: rgba(255,255,255,.05); color: var(--my-tx-3);
    font-size: 10px; font-weight: 500;
  }
  .my-fu-cta { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .my-card-status {
    padding: 4px 10px; border-radius: 6px;
    font-size: 11px; font-weight: 600;
  }
  .my-card-status.victory { background: rgba(74,222,128,.1); color: var(--my-accent); }
  .my-card-status.defeat  { background: rgba(248,113,113,.1); color: var(--my-danger); }
  .my-card-status.draw    { background: rgba(245,193,122,.1); color: var(--my-warn); }

  /* EMPTY STATE */
  .my-empty {
    background: var(--my-bg-1); border: 1px dashed var(--my-bd-2);
    border-radius: 14px; padding: 50px 30px; text-align: center;
  }
  .my-empty-ico {
    width: 50px; height: 50px; border-radius: 12px;
    background: var(--my-accent-soft); color: var(--my-accent);
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 16px;
  }
  .my-empty h3 { font-size: 18px; font-weight: 300; letter-spacing: -0.02em; margin: 0 0 6px; color: var(--my-tx); }
  .my-empty p { font-size: 13px; color: var(--my-tx-3); margin: 0 0 18px; max-width: 340px; margin-inline: auto; }

  /* SIDEBAR */
  .my-side {
    position: sticky; top: 100px; height: fit-content;
    display: flex; flex-direction: column; gap: 16px;
  }
  .my-side-card { background: var(--my-bg-1); border: 1px solid var(--my-bd); border-radius: 14px; padding: 18px 20px; }
  .my-side-card h3 { font-size: 13px; font-weight: 600; margin: 0 0 14px; display: flex; align-items: center; justify-content: space-between; }
  .my-side-card h3 .pill { font-size: 10px; color: var(--my-tx-3); padding: 2px 8px; background: rgba(255,255,255,.04); border-radius: 999px; font-weight: 500; }

  /* WEEK MINI CALENDAR */
  .my-week { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; }
  .my-week-d {
    text-align: center; padding: 8px 4px;
    background: rgba(255,255,255,.02); border-radius: 8px;
    border: 1px solid transparent;
    position: relative;
  }
  .my-week-d span:first-child { font-size: 9px; color: var(--my-tx-3); display: block; letter-spacing: .08em; text-transform: uppercase; font-weight: 600; }
  .my-week-d .num { font-size: 14px; color: var(--my-tx); margin-top: 2px; display: block; font-weight: 500; font-variant-numeric: tabular-nums; }
  .my-week-d.today { background: rgba(74,222,128,.08); border-color: rgba(74,222,128,.25); }
  .my-week-d.today .num { color: var(--my-accent); }
  .my-week-d.has::after {
    content: ''; position: absolute; bottom: 5px; left: 50%; transform: translateX(-50%);
    width: 4px; height: 4px; border-radius: 50%; background: var(--my-accent);
  }
  .my-week-d.has-2::after { box-shadow: 6px 0 0 var(--my-accent), -6px 0 0 var(--my-accent); }

  /* LEVEL BARS */
  .my-levels { display: flex; flex-direction: column; gap: 12px; }
  .my-level-row { display: flex; justify-content: space-between; align-items: center; font-size: 11px; margin-bottom: 5px; }
  .my-level-row .l { color: var(--my-tx-3); display: inline-flex; align-items: center; gap: 6px; }
  .my-level-row .l .sw { width: 8px; height: 8px; border-radius: 2px; }
  .my-level-row .v { color: var(--my-tx); font-weight: 500; font-variant-numeric: tabular-nums; }
  .my-level-bar { height: 4px; background: rgba(255,255,255,.04); border-radius: 2px; overflow: hidden; }
  .my-level-bar > span { display: block; height: 100%; border-radius: 2px; transition: width .4s; }

  /* SIDE LINKS */
  .my-side-link {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 0; font-size: 13px; color: var(--my-tx-2);
    border-bottom: 1px solid var(--my-bd);
    transition: color .15s;
  }
  .my-side-link:last-child { border-bottom: 0; padding-bottom: 0; }
  .my-side-link:first-child { padding-top: 0; }
  .my-side-link:hover { color: var(--my-accent); }

  /* FOOTER LINK */
  .my-footer-link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 10px 16px; font-size: 13px; font-weight: 500; color: var(--my-tx-3);
    border: 1px solid var(--my-bd); border-radius: 10px;
    margin-top: 14px;
    transition: color .15s, border-color .15s, background .15s;
  }
  .my-footer-link:hover { color: var(--my-accent); border-color: rgba(74,222,128,.25); background: rgba(74,222,128,.04); }

  /* RESPONSIVE */
  @media (max-width: 1100px) {
    .my-stats { grid-template-columns: repeat(2, 1fr); }
    .my-body { grid-template-columns: 1fr; }
    .my-side { position: static; flex-direction: row; flex-wrap: wrap; }
    .my-side-card { flex: 1; min-width: 280px; }
  }
  @media (max-width: 760px) {
    .my-page { padding: 24px 18px 60px; }
    .my-head { flex-direction: column; align-items: flex-start; }
    .my-stats { grid-template-columns: 1fr; }
    .my-turn-head { grid-template-columns: 56px 1fr; gap: 14px; }
    .my-turn-cta { grid-column: 1 / -1; }
    .my-turn-body { grid-template-columns: 1fr; gap: 18px; }
    .my-card { grid-template-columns: 32px 1fr; gap: 10px; }
    .my-card-aside { grid-column: 1 / -1; flex-wrap: wrap; }
    .my-recurring-head { grid-template-columns: 1fr; gap: 12px; }
    .my-recurring-amount { text-align: left; }
    .my-fu { grid-template-columns: 32px 1fr; }
    .my-fu-roster, .my-fu-cta { grid-column: 1 / -1; }
  }
</style>
@endpush

@section('content')
<div class="my-scope">
<div class="my-page">

  {{-- ═══ HEAD ═══ --}}
  <div class="my-head">
    <div>
      <div class="my-eyebrow">MI CUENTA · ACTIVIDAD</div>
      <h1 class="my-title">Mi <b>actividad</b></h1>
      <p class="my-sub">Tus turnos reservados, partidos recurrentes y la actividad en Falta&nbsp;Uno, todo en un solo lugar.</p>
    </div>
    <a class="my-cta-new" href="{{ route('venues.index') }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
      Reservar nuevo
    </a>
  </div>

  {{-- ═══ STATS ═══ --}}
  <div class="my-stats">
    <div class="my-stat">
      <div class="my-stat-k"><span class="my-stat-ico"></span>PRÓXIMOS TURNOS</div>
      <div class="my-stat-v">{{ $upcomingCount }}</div>
      <div class="my-stat-meta">
        @if($nextReservation)
          Próximo: <b>{{ $nextReservation->start_at->locale('es')->isoFormat('ddd D [a las] HH:mm') }}</b>
        @else
          Sin próximos turnos
        @endif
      </div>
    </div>
    <div class="my-stat">
      <div class="my-stat-k"><span class="my-stat-ico purple"></span>RECURRENTES ACTIVOS</div>
      <div class="my-stat-v">{{ $activeSubsCount }}</div>
      <div class="my-stat-meta">
        @if($nextActiveSub)
          {{ $sportLabel($nextActiveSub->field->sport ?? '') }} · <b>{{ $nextActiveSub->start_time ? substr($nextActiveSub->start_time, 0, 5) : '' }}</b>
        @else
          Sin abonos activos
        @endif
      </div>
    </div>
    <div class="my-stat">
      <div class="my-stat-k"><span class="my-stat-ico blue"></span>FALTA UNO · ESTE MES</div>
      <div class="my-stat-v">{{ $fuMonthCount }}<sub>{{ $fuMonthCount === 1 ? 'partido' : 'partidos' }}</sub></div>
      <div class="my-stat-meta">
        @if($fuMonthWins + $fuMonthLosses + $fuMonthDraws > 0)
          <b class="up">{{ $fuMonthWins }} {{ $fuMonthWins === 1 ? 'victoria' : 'victorias' }}</b>
          @if($fuMonthLosses > 0) · {{ $fuMonthLosses }} {{ $fuMonthLosses === 1 ? 'derrota' : 'derrotas' }} @endif
        @else
          Sin resultados cargados
        @endif
      </div>
    </div>
    <div class="my-stat">
      <div class="my-stat-k"><span class="my-stat-ico warn"></span>TOTAL JUGADO</div>
      <div class="my-stat-v">{{ $totalHoursPlayed }}<sub>hs</sub></div>
      <div class="my-stat-meta">
        Desde {{ $memberSince->locale('es')->isoFormat('MMMM') }}
        @if($userRanking) · <b>{{ $categoryLabel($userRanking) }}</b> @endif
      </div>
    </div>
  </div>

  {{-- ═══ TABS ═══ --}}
  <div class="my-tabs" role="tablist">
    <button class="my-tab active" data-panel="turnos" type="button">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
      Mis turnos <span class="count">{{ $reservations->count() }}</span>
    </button>
    <button class="my-tab" data-panel="rec" type="button">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12a9 9 0 1 1-3.5-7"/><path d="M21 5v6h-6"/></svg>
      Recurrentes <span class="count">{{ $recurringSubscriptions->count() + $batches->count() }}</span>
    </button>
    <button class="my-tab" data-panel="fu" type="button">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
      Falta Uno <span class="count">{{ $misPartidos->count() }}</span>
    </button>
  </div>

  {{-- ═══ BODY ═══ --}}
  <div class="my-body">

    <div class="my-main">

      {{-- ═══ PANEL: MIS TURNOS ═══ --}}
      <div class="my-panel on" id="my-panel-turnos">

        <div class="my-subfilters">
          <button type="button" class="my-sub-chip active" data-filter="upcoming">Próximos <span style="opacity:.6">{{ $upcoming->count() }}</span></button>
          <button type="button" class="my-sub-chip" data-filter="past">Pasados <span style="opacity:.6">{{ $past->count() }}</span></button>
          <button type="button" class="my-sub-chip" data-filter="cancelled">Cancelados <span style="opacity:.6">{{ $cancelled->count() }}</span></button>
          @if($pendingPay->count())
            <span class="my-sub-divider"></span>
            <button type="button" class="my-sub-chip" data-filter="pending">Pago pendiente <span style="opacity:.6">{{ $pendingPay->count() }}</span></button>
          @endif
        </div>

        {{-- Sub-panel: Próximos --}}
        <div data-subpanel="upcoming">
          @if($upcoming->isEmpty())
            <div class="my-empty">
              <div class="my-empty-ico">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
              </div>
              <h3>Sin turnos próximos</h3>
              <p>Cuando reserves una cancha vas a verla acá. Empezá explorando complejos cerca tuyo.</p>
              <a class="my-btn my-btn-prim" href="{{ route('venues.index') }}">Explorar complejos</a>
            </div>
          @else
            @foreach([
              ['title' => 'Mañana', 'icon' => '', 'items' => $upTomorrow],
              ['title' => 'Esta semana', 'icon' => '', 'items' => $upWeek],
              ['title' => 'Más adelante', 'icon' => '', 'items' => $upLater],
            ] as $bucket)
              @if($bucket['items']->count())
                <div class="my-timeline">
                  <div class="my-timeline-head">
                    <h2><span class="dot"></span>{{ $bucket['title'] }}</h2>
                    <span class="ct">{{ $bucket['items']->count() }} {{ $bucket['items']->count() === 1 ? 'turno' : 'turnos' }}</span>
                    <div class="ln"></div>
                  </div>

                  @foreach($bucket['items'] as $r)
                    @php
                      [$badgeClass, $badgeText] = $statusBadge($r->status);
                      $venue = $r->field->venue;
                      $cancelHrs = $venue->cancellation_hours;
                      $deadline = $cancelHrs !== null ? $r->start_at->copy()->subHours($cancelHrs) : null;
                      $canCancel = $r->status === 'PAID' && $deadline && now()->lt($deadline);
                      $mapsUrl = $venue->lat && $venue->lng
                        ? "https://www.google.com/maps/search/?api=1&query={$venue->lat},{$venue->lng}"
                        : "https://www.google.com/maps/search/?api=1&query=" . urlencode(($venue->address ?? '') . ', ' . ($venue->zone ?? ''));
                    @endphp
                    <article class="my-turn">
                      <div class="my-turn-head">
                        <div class="my-turn-date">
                          <span class="day">{{ ucfirst($r->start_at->locale('es')->isoFormat('ddd')) }}</span>
                          <span class="d">{{ $r->start_at->format('d') }}</span>
                          <span class="m">{{ $r->start_at->locale('es')->isoFormat('MMM') }}</span>
                        </div>
                        <div class="my-turn-info">
                          <div class="my-turn-title">
                            {{ $r->field->name }} · {{ $sportLabel($r->field->sport) }}
                            <span class="my-tag {{ $badgeClass }}">{{ $badgeText }}</span>
                          </div>
                          <div class="my-turn-meta">
                            <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> {{ $r->start_at->format('H:i') }} — {{ $r->end_at->format('H:i') }}</span>
                            <span><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-7 8-13a8 8 0 0 0-16 0c0 6 8 13 8 13z"/></svg> {{ $venue->name }} @if($venue->zone)· <b>{{ $venue->zone }}</b>@endif</span>
                          </div>
                        </div>
                        <div class="my-turn-cta">
                          <a class="my-btn my-btn-ghost" href="{{ $mapsUrl }}" target="_blank" rel="noopener">Cómo llegar</a>
                          @if($r->status === 'PENDING_PAYMENT')
                            <a class="my-btn my-btn-warn" href="{{ route('reservations.checkout', $r) }}">Pagar ahora</a>
                          @elseif($canCancel)
                            <form method="POST" action="{{ route('reservations.cancel', $r) }}" onsubmit="return confirm('¿Cancelar esta reserva?')" style="display:inline-flex;">
                              @csrf
                              <button type="submit" class="my-btn my-btn-danger-ghost">Cancelar</button>
                            </form>
                          @endif
                          <a class="my-btn my-btn-prim" href="{{ route('reservations.show', $r) }}">Ver detalle</a>
                        </div>
                      </div>
                      <div class="my-turn-body">
                        <div>
                          <div class="my-turn-payment-head">CÓDIGO DE TURNO</div>
                          <div class="my-turn-code-row">
                            <div class="my-turn-code">
                              {{ $r->verification_code ?? '—' }}
                              @if($r->verification_code)
                                <button type="button" class="my-turn-code-copy" title="Copiar" onclick="navigator.clipboard.writeText('{{ $r->verification_code }}'); this.querySelector('svg').style.color='var(--my-accent)'; setTimeout(()=>this.querySelector('svg').style.color='',1200)">
                                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </button>
                              @endif
                            </div>
                          </div>
                          <div class="my-turn-payment-status">
                            @if($r->status === 'PAID')
                              <span class="dot"></span>Mostrá este código en recepción · pago confirmado
                            @elseif($r->status === 'PENDING_CASH')
                              <span class="dot pending"></span>Vas a pagar en efectivo en el complejo
                            @elseif($r->status === 'PENDING_PAYMENT')
                              <span class="dot pending"></span>
                              @if($r->expires_at)
                                Te quedan <b style="color:var(--my-warn)">{{ $r->expires_at->diffForHumans(now(), ['parts' => 1]) }}</b> para pagar antes de que se libere
                              @else
                                Pago pendiente
                              @endif
                            @endif
                          </div>
                        </div>
                        <div>
                          <div class="my-turn-payment-head">DETALLES</div>
                          <div class="my-turn-tags">
                            @if($r->total_amount)
                              <span class="my-tag">${{ number_format($r->total_amount, 0, ',', '.') }}</span>
                            @endif
                            @if($r->field->format)
                              <span class="my-tag">{{ $r->field->format }}v{{ $r->field->format }}</span>
                            @endif
                            @if($r->field->is_indoor)
                              <span class="my-tag">Cubierta</span>
                            @endif
                            <span class="my-tag">{{ $sportLabel($r->field->sport) }}</span>
                          </div>
                        </div>
                      </div>
                    </article>
                  @endforeach
                </div>
              @endif
            @endforeach
          @endif
        </div>

        {{-- Sub-panel: Pasados --}}
        <div data-subpanel="past" style="display:none;">
          @if($past->isEmpty())
            <div class="my-empty">
              <div class="my-empty-ico">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              </div>
              <h3>Aún no jugaste ningún turno</h3>
              <p>Una vez que pase tu primer reserva, va a aparecer en el historial.</p>
            </div>
          @else
            <div class="my-timeline">
              <div class="my-timeline-head">
                <h2 class="history"><span class="dot"></span>Historial</h2>
                <span class="ct">últimas {{ $past->count() }}</span>
                <div class="ln"></div>
              </div>
              @foreach($past as $r)
                <article class="my-card">
                  <div class="my-card-ico history">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">{!! $sportIcon($r->field->sport) !!}</svg>
                  </div>
                  <div class="my-card-main">
                    <div class="my-card-title-row">
                      <span class="my-card-title">{{ $r->field->name }} · {{ $sportLabel($r->field->sport) }}</span>
                      <span class="my-tag muted">Jugado</span>
                    </div>
                    <div class="my-card-meta">
                      <span>{{ ucfirst($r->start_at->locale('es')->isoFormat('ddd')) }} <b>{{ $r->start_at->format('d') }}</b> {{ $r->start_at->locale('es')->isoFormat('MMM') }} · <b>{{ $r->start_at->format('H:i') }}</b></span>
                      <span class="sep"></span>
                      <span>{{ $r->field->venue->name }} @if($r->field->venue->zone)· {{ $r->field->venue->zone }}@endif</span>
                    </div>
                  </div>
                  <div class="my-card-aside">
                    <a class="my-btn my-btn-ghost" href="{{ route('venues.show', $r->field->venue) }}">Reservar igual</a>
                    <a class="my-btn my-btn-ghost" href="{{ route('reservations.show', $r) }}">Ver detalle</a>
                  </div>
                </article>
              @endforeach
            </div>
          @endif
        </div>

        {{-- Sub-panel: Cancelados --}}
        <div data-subpanel="cancelled" style="display:none;">
          @if($cancelled->isEmpty())
            <div class="my-empty">
              <div class="my-empty-ico" style="background:rgba(248,113,113,.08); color:var(--my-danger);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 14.14 14.14"/></svg>
              </div>
              <h3>Sin reservas canceladas</h3>
              <p>Las reservas que canceles o que expiren van a aparecer acá.</p>
            </div>
          @else
            <div class="my-timeline">
              <div class="my-timeline-head">
                <h2 class="cancel"><span class="dot"></span>Canceladas / expiradas</h2>
                <span class="ct">{{ $cancelled->count() }}</span>
                <div class="ln"></div>
              </div>
              @foreach($cancelled as $r)
                @php [$bClass, $bText] = $statusBadge($r->status); @endphp
                <article class="my-card cancelled">
                  <div class="my-card-ico cancel">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 14.14 14.14"/></svg>
                  </div>
                  <div class="my-card-main">
                    <div class="my-card-title-row">
                      <span class="my-card-title">{{ $r->field->name }} · {{ $sportLabel($r->field->sport) }}</span>
                      <span class="my-tag {{ $bClass }}">{{ $bText }}</span>
                    </div>
                    <div class="my-card-meta">
                      <span>{{ ucfirst($r->start_at->locale('es')->isoFormat('ddd')) }} <b>{{ $r->start_at->format('d') }}</b> {{ $r->start_at->locale('es')->isoFormat('MMM') }} · <b>{{ $r->start_at->format('H:i') }}</b></span>
                      <span class="sep"></span>
                      <span>{{ $r->field->venue->name }}</span>
                    </div>
                  </div>
                  <div class="my-card-aside">
                    <a class="my-btn my-btn-ghost" href="{{ route('reservations.show', $r) }}">Ver detalle</a>
                  </div>
                </article>
              @endforeach
            </div>
          @endif
        </div>

        {{-- Sub-panel: Pago pendiente --}}
        @if($pendingPay->count())
          <div data-subpanel="pending" style="display:none;">
            <div class="my-timeline">
              <div class="my-timeline-head">
                <h2><span class="dot" style="background:var(--my-warn);"></span>Pago pendiente</h2>
                <span class="ct">{{ $pendingPay->count() }}</span>
                <div class="ln"></div>
              </div>
              @foreach($pendingPay as $r)
                <article class="my-card">
                  <div class="my-card-ico" style="background:rgba(245,193,122,.08); color:var(--my-warn);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                  </div>
                  <div class="my-card-main">
                    <div class="my-card-title-row">
                      <span class="my-card-title">{{ $r->field->name }} · {{ $sportLabel($r->field->sport) }}</span>
                      <span class="my-tag warn">Pago pendiente</span>
                    </div>
                    <div class="my-card-meta">
                      <span>{{ ucfirst($r->start_at->locale('es')->isoFormat('ddd')) }} <b>{{ $r->start_at->format('d') }}</b> {{ $r->start_at->locale('es')->isoFormat('MMM') }} · <b>{{ $r->start_at->format('H:i') }}</b></span>
                      @if($r->expires_at)
                        <span class="sep"></span>
                        <span>Vence {{ $r->expires_at->diffForHumans() }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="my-card-aside">
                    <a class="my-btn my-btn-warn" href="{{ route('reservations.checkout', $r) }}">Pagar</a>
                  </div>
                </article>
              @endforeach
            </div>
          </div>
        @endif

      </div>

      {{-- ═══ PANEL: RECURRENTES ═══ --}}
      <div class="my-panel" id="my-panel-rec">
        <div class="my-subfilters">
          <button type="button" class="my-sub-chip active" data-rec-filter="active">Activos <span style="opacity:.6">{{ $subsActive->count() }}</span></button>
          <button type="button" class="my-sub-chip" data-rec-filter="paused">Pausados <span style="opacity:.6">{{ $subsPaused->count() }}</span></button>
          <button type="button" class="my-sub-chip" data-rec-filter="finished">Finalizados <span style="opacity:.6">{{ $subsFinished->count() }}</span></button>
        </div>

        @php
          $allSubs = collect()->merge($subsActive)->merge($subsPaused)->merge($subsFinished);
        @endphp

        @if($allSubs->isEmpty() && $batches->isEmpty())
          <div class="my-empty">
            <div class="my-empty-ico" style="background:rgba(167,139,250,.08); color:var(--my-purple);">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 12a9 9 0 1 1-3.5-7"/><path d="M21 5v6h-6"/></svg>
            </div>
            <h3>Sin abonos recurrentes</h3>
            <p>Reservá un horario fijo cada semana, mismo día y horario, con un solo abono mensual.</p>
            <a class="my-btn my-btn-prim" href="{{ route('venues.index') }}">Crear recurrente</a>
          </div>
        @else
          @foreach($allSubs as $sub)
            @php
              $statusClass = match($sub->status) {
                'ACTIVE'   => 'active',
                'PAUSED'   => 'paused',
                'CANCELLED','FINISHED' => 'finished',
                default    => 'active',
              };
              $upcomingSlots = $sub->reservations
                ->where('start_at', '>', now())
                ->whereIn('status', ['PAID','PENDING_PAYMENT','PENDING_CASH'])
                ->sortBy('start_at')
                ->take(4)
                ->values();
              // Si no hay reservas futuras persistidas, proyectamos 4 fechas a partir de day_of_week + start_time
              if ($upcomingSlots->isEmpty() && $sub->status === 'ACTIVE') {
                $projected = collect();
                $cursor = now()->copy()->startOfDay();
                $targetDow = (int) $sub->day_of_week; // 0..6
                while ($projected->count() < 4) {
                  if ($cursor->dayOfWeek === $targetDow) {
                    [$h, $m] = array_pad(explode(':', $sub->start_time), 2, 0);
                    $start = $cursor->copy()->setTime((int)$h, (int)$m);
                    if ($start->isFuture()) {
                      $end = $start->copy()->addMinutes((int) $sub->slot_minutes);
                      $projected->push((object)['start_at' => $start, 'end_at' => $end, 'projected' => true]);
                    }
                  }
                  $cursor->addDay();
                }
                $upcomingSlots = $projected;
              }
              // Formatear duración
              $durMin = (int) $sub->slot_minutes;
              $durLabel = $durMin >= 60
                ? (intdiv($durMin, 60) . 'h' . ($durMin % 60 ? ' ' . ($durMin % 60) . 'm' : ''))
                : ($durMin . ' min');
              // End time string desde start_time + duración
              [$_h, $_m] = array_pad(explode(':', $sub->start_time), 2, 0);
              $startStr = sprintf('%02d:%02d', (int)$_h, (int)$_m);
              $endStr   = \Carbon\Carbon::createFromTime((int)$_h, (int)$_m)->addMinutes($durMin)->format('H:i');
            @endphp
            <article class="my-recurring" data-rec-status="{{ $statusClass }}" @if($statusClass !== 'active') style="display:none;" @endif>
              <div class="my-recurring-head">
                <div class="my-recurring-ico">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12a9 9 0 1 1-3.5-7"/><path d="M21 5v6h-6"/></svg>
                </div>
                <div class="my-recurring-info">
                  <h3>
                    {{ $sub->field->name ?? '—' }} · {{ $sportLabel($sub->field->sport ?? '') }}
                    · <span style="color:{{ $sub->status === 'ACTIVE' ? 'var(--my-accent)' : 'var(--my-tx-3)' }}; font-weight:600;">
                      {{ $sub->statusLabel() }}
                    </span>
                  </h3>
                  <div class="meta">
                    @php
                      $dowLabels = [0=>'domingos',1=>'lunes',2=>'martes',3=>'miércoles',4=>'jueves',5=>'viernes',6=>'sábados'];
                    @endphp
                    <span>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                      Todos los <b>{{ $dowLabels[$sub->day_of_week] ?? '—' }}</b>
                    </span>
                    <span>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                      {{ $startStr }} — {{ $endStr }} · <b>{{ $durLabel }}</b>
                    </span>
                    @if($sub->field->venue)
                      <span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-7 8-13a8 8 0 0 0-16 0c0 6 8 13 8 13z"/></svg>
                        {{ $sub->field->venue->name }} @if($sub->field->venue->zone)· <b>{{ $sub->field->venue->zone }}</b>@endif
                      </span>
                    @endif
                  </div>
                </div>
                <div class="my-recurring-amount">
                  <div class="k">MENSUAL</div>
                  <div class="v">${{ number_format($sub->monthly_amount, 0, ',', '.') }}</div>
                </div>
                <div class="my-turn-cta">
                  <a class="my-btn my-btn-ghost" href="{{ route('recurring.subscription.result', $sub) }}">Ver detalle</a>
                </div>
              </div>

              @if($upcomingSlots->count() > 0)
                <div class="my-recurring-body">
                  <div class="my-recurring-body-head">
                    <span>PRÓXIMOS TURNOS · {{ $upcomingSlots->count() }} INCLUIDOS</span>
                    @if($sub->next_billing_date)
                      <span class="next-pay">PRÓX. PAGO: {{ strtoupper($sub->next_billing_date->locale('es')->isoFormat('DD MMM')) }}</span>
                    @endif
                  </div>
                  <div class="my-recurring-grid">
                    @foreach($upcomingSlots as $idx => $slot)
                      <div class="my-recurring-slot {{ $idx === 0 ? 'next' : '' }}">
                        <div class="date">{{ ucfirst($slot->start_at->locale('es')->isoFormat('ddd D MMM')) }}</div>
                        <div class="time">{{ $slot->start_at->format('H:i') }} — {{ $slot->end_at->format('H:i') }}</div>
                      </div>
                    @endforeach
                  </div>
                  @if($sub->status === 'ACTIVE')
                    <div class="my-recurring-actions">
                      <form method="POST" action="{{ route('recurring.subscription.cancel', $sub) }}" onsubmit="return confirm('¿Cancelar esta suscripción mensual?')" style="display:inline-flex; margin-left:auto;">
                        @csrf
                        <button type="submit" class="my-btn my-btn-danger-ghost">Cancelar suscripción</button>
                      </form>
                    </div>
                  @endif
                </div>
              @endif
            </article>
          @endforeach

          {{-- Promo: crear nueva recurrente --}}
          <div style="background:var(--my-bg-1); border:1px solid var(--my-bd); border-radius:14px; padding:20px 22px; display:grid; grid-template-columns:auto 1fr auto; gap:18px; align-items:center; margin-top:14px;">
            <div style="width:44px; height:44px; border-radius:11px; background:rgba(74,222,128,.06); border:1px dashed rgba(74,222,128,.25); display:flex; align-items:center; justify-content:center; color:var(--my-accent);">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            </div>
            <div>
              <div style="font-size:14px; font-weight:500; color:var(--my-tx); margin-bottom:3px;">Reservá un horario fijo</div>
              <div style="font-size:12px; color:var(--my-tx-3);">Asegurá tu cancha cada semana, mismo día y horario. Pagás un solo abono mensual.</div>
            </div>
            <a class="my-btn my-btn-prim" href="{{ route('venues.index') }}">Crear recurrente</a>
          </div>
        @endif
      </div>

      {{-- ═══ PANEL: FALTA UNO ═══ --}}
      <div class="my-panel" id="my-panel-fu">
        <div class="my-subfilters">
          <button type="button" class="my-sub-chip active" data-fu-filter="upcoming">Próximos <span style="opacity:.6">{{ $fuUpcoming->count() }}</span></button>
          <button type="button" class="my-sub-chip" data-fu-filter="cancelled">Cancelados <span style="opacity:.6">{{ $fuCancelled->count() }}</span></button>
          <button type="button" class="my-sub-chip" data-fu-filter="history">Historial <span style="opacity:.6">{{ $fuHistory->count() }}</span></button>
          <span style="margin-left:auto;">
            <a href="{{ route('sport-profile.public', auth()->user()) }}" class="my-sub-chip" style="color:var(--my-accent); background:rgba(74,222,128,.06); border-color:rgba(74,222,128,.2); text-decoration:none;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg>
              Ver mi perfil de jugador
            </a>
          </span>
        </div>

        {{-- Próximos --}}
        <div data-fu-subpanel="upcoming">
          @if($fuUpcoming->isEmpty())
            <div class="my-empty">
              <div class="my-empty-ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 8v8M8 12h8"/></svg></div>
              <h3>No estás anotado en ningún partido</h3>
              <p>Sumate a partidos abiertos o creá uno nuevo en Falta Uno.</p>
              <a class="my-btn my-btn-prim" href="{{ route('falta-uno.index') }}">Ir a Falta Uno</a>
            </div>
          @else
            <div class="my-timeline">
              <div class="my-timeline-head">
                <h2><span class="dot"></span>Próximos partidos</h2>
                <span class="ct">{{ $fuUpcoming->count() }}</span>
                <div class="ln"></div>
              </div>
              @foreach($fuUpcoming as $g)
                @php
                  $remaining = $g->players_needed - $g->activeParticipants->count();
                  $isFull = $g->status === 'full' || $remaining <= 0;
                  $shown = $g->activeParticipants->take(5);
                  $extraCount = max(0, $g->activeParticipants->count() - 5);
                @endphp
                <article class="my-fu">
                  <div class="my-fu-ico">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">{!! $sportIcon($g->field->sport) !!}</svg>
                  </div>
                  <div class="my-fu-info">
                    <div class="my-fu-title">{{ $g->field->name }} · {{ $sportLabel($g->field->sport) }} — {{ $g->field->venue->name }}</div>
                    <div class="my-fu-meta">
                      <span>{{ ucfirst($g->start_at->locale('es')->isoFormat('ddd')) }} <b>{{ $g->start_at->format('d') }}</b> {{ $g->start_at->locale('es')->isoFormat('MMM') }} · <b>{{ $g->start_at->format('H:i') }}</b></span>
                      @if($g->field->venue->zone)<span class="sep"></span><span>{{ $g->field->venue->zone }}</span>@endif
                    </div>
                  </div>
                  <div class="my-fu-roster">
                    @foreach($shown as $p)
                      @php
                        $pName = $p->user->name ?? '?';
                        [$pFrom, $pTo] = $avatarGradient($pName);
                      @endphp
                      <span class="my-fu-ava" title="{{ $pName }}" style="background:linear-gradient(135deg, {{ $pFrom }}, {{ $pTo }});">
                        {{ strtoupper(substr($pName, 0, 1)) }}
                      </span>
                    @endforeach
                    @if($extraCount > 0)<span class="my-fu-ava empty">+{{ $extraCount }}</span>@endif
                  </div>
                  <div class="my-fu-cta">
                    @if($isFull)
                      <span class="my-tag ok">Confirmado</span>
                    @else
                      <span class="my-tag warn">Buscando jugadores</span>
                    @endif
                    <a class="my-btn my-btn-ghost" href="{{ route('falta-uno.show', $g) }}">Ver partido</a>
                  </div>
                </article>
              @endforeach
            </div>
          @endif
        </div>

        {{-- Cancelados --}}
        <div data-fu-subpanel="cancelled" style="display:none;">
          @if($fuCancelled->isEmpty())
            <div class="my-empty">
              <div class="my-empty-ico" style="background:rgba(248,113,113,.08); color:var(--my-danger);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 14.14 14.14"/></svg>
              </div>
              <h3>Sin partidos cancelados</h3>
              <p>Los partidos que se cancelan o expiren van a aparecer acá.</p>
            </div>
          @else
            <div class="my-timeline">
              <div class="my-timeline-head">
                <h2 class="cancel"><span class="dot"></span>Cancelados</h2>
                <span class="ct">últimos 30 días</span>
                <div class="ln"></div>
              </div>
              @foreach($fuCancelled as $g)
                <article class="my-fu cancelled">
                  <div class="my-fu-ico" style="background:rgba(248,113,113,.06); color:var(--my-danger);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $sportIcon($g->field->sport) !!}</svg>
                  </div>
                  <div class="my-fu-info">
                    <div class="my-fu-title">{{ $g->field->name }} · {{ $sportLabel($g->field->sport) }} — {{ $g->field->venue->name }}</div>
                    <div class="my-fu-meta">
                      <span>{{ $g->start_at->format('d/m/Y') }} · <b>{{ $g->start_at->format('H:i') }}</b></span>
                      <span class="sep"></span>
                      <span>{{ $g->total_players }} jugadores · {{ $g->activeParticipants->count() }} anotados</span>
                    </div>
                  </div>
                  <div class="my-fu-roster"></div>
                  <div class="my-fu-cta">
                    <span class="my-tag danger">{{ $g->status === 'expired' ? 'No se completó' : 'Cancelado' }}</span>
                    <a class="my-btn my-btn-ghost" href="{{ route('falta-uno.show', $g) }}">Ver partido</a>
                  </div>
                </article>
              @endforeach
            </div>
          @endif
        </div>

        {{-- Historial --}}
        <div data-fu-subpanel="history" style="display:none;">
          @if($fuHistory->isEmpty())
            <div class="my-empty">
              <div class="my-empty-ico" style="background:rgba(255,255,255,.04); color:var(--my-tx-3);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
              </div>
              <h3>Sin partidos jugados</h3>
              <p>Cuando termines tu primer partido en Falta Uno, va a aparecer en el historial.</p>
            </div>
          @else
            <div class="my-timeline">
              <div class="my-timeline-head">
                <h2 class="history"><span class="dot"></span>Historial</h2>
                <span class="ct">{{ $fuHistory->count() }} {{ $fuHistory->count() === 1 ? 'partido' : 'partidos' }}</span>
                <div class="ln"></div>
              </div>
              @foreach($fuHistory as $g)
                @php
                  $myP = $g->participants->firstWhere('user_id', auth()->id());
                  $result = $myP?->result;
                  $shown = $g->activeParticipants->take(4);
                @endphp
                <article class="my-fu {{ $result === 'win' ? 'victory' : ($result === 'loss' ? 'defeat' : '') }}">
                  <div class="my-fu-ico {{ $g->field->sport === 'padel' ? 'padel' : '' }}" style="{{ $g->field->sport === 'padel' ? 'background:rgba(167,139,250,.08); color:var(--my-purple);' : '' }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">{!! $sportIcon($g->field->sport) !!}</svg>
                  </div>
                  <div class="my-fu-info">
                    <div class="my-fu-title">{{ $g->field->name }} · {{ $sportLabel($g->field->sport) }} — {{ $g->field->venue->name }}</div>
                    <div class="my-fu-meta">
                      <span>{{ $g->start_at->format('d/m/Y') }} · <b>{{ $g->start_at->format('H:i') }}</b></span>
                      @if($myP?->goals !== null)
                        <span class="sep"></span>
                        <span>{{ $myP->goals }} {{ (int) $myP->goals === 1 ? 'gol' : 'goles' }}</span>
                      @endif
                    </div>
                  </div>
                  <div class="my-fu-roster">
                    @foreach($shown as $p)
                      @php
                        $pName = $p->user->name ?? '?';
                        [$pFrom, $pTo] = $avatarGradient($pName);
                      @endphp
                      <span class="my-fu-ava" title="{{ $pName }}" style="background:linear-gradient(135deg, {{ $pFrom }}, {{ $pTo }});">{{ strtoupper(substr($pName, 0, 1)) }}</span>
                    @endforeach
                  </div>
                  <div class="my-fu-cta">
                    @if($result === 'win')
                      <span class="my-card-status victory">Victoria</span>
                    @elseif($result === 'loss')
                      <span class="my-card-status defeat">Derrota</span>
                    @elseif($result === 'draw')
                      <span class="my-card-status draw">Empate</span>
                    @else
                      <span class="my-tag muted">Sin resultado</span>
                    @endif
                    <a class="my-btn my-btn-ghost" href="{{ route('falta-uno.show', $g) }}">Ver partido</a>
                    @if(!$result && $myP)
                      <a class="my-btn my-btn-prim" href="{{ route('falta-uno.stats', $g) }}">Cargar resultado</a>
                    @endif
                  </div>
                </article>
              @endforeach
            </div>
          @endif
        </div>

        <a class="my-footer-link" href="{{ route('falta-uno.index') }}">
          Ver todos los partidos Falta Uno
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      </div>
    </div>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="my-side">

      <div class="my-side-card">
        <h3>Esta semana <span class="pill">{{ $weekDays[0]['date']->locale('es')->isoFormat('D MMM') }} — {{ $weekDays[6]['date']->locale('es')->isoFormat('D MMM') }}</span></h3>
        <div class="my-week">
          @foreach($weekDays as $wd)
            <div class="my-week-d {{ $wd['isToday'] ? 'today' : '' }} {{ $wd['count'] >= 1 ? 'has' : '' }} {{ $wd['count'] >= 2 ? 'has-2' : '' }}" title="{{ $wd['count'] }} {{ $wd['count'] === 1 ? 'turno' : 'turnos' }}">
              <span>{{ ucfirst($wd['date']->locale('es')->isoFormat('ddd')) }}</span>
              <span class="num">{{ $wd['date']->format('d') }}</span>
            </div>
          @endforeach
        </div>
      </div>

      @if($levelMetrics)
        <div class="my-side-card">
          <h3>Tu nivel <span class="pill">{{ $sportLabel($levelMetrics['sport']) }}</span></h3>
          <div class="my-levels">
            <div>
              <div class="my-level-row">
                <span class="l"><span class="sw" style="background:var(--my-accent);"></span>Constancia</span>
                <span class="v">{{ $levelMetrics['constancia'] }}%</span>
              </div>
              <div class="my-level-bar"><span style="width:{{ min(100, $levelMetrics['constancia']) }}%; background:var(--my-accent);"></span></div>
            </div>
            <div>
              <div class="my-level-row">
                <span class="l"><span class="sw" style="background:var(--my-blue);"></span>Puntualidad</span>
                <span class="v">{{ $levelMetrics['puntualidad'] }}%</span>
              </div>
              <div class="my-level-bar"><span style="width:{{ min(100, $levelMetrics['puntualidad']) }}%; background:var(--my-blue);"></span></div>
            </div>
            <div>
              <div class="my-level-row">
                <span class="l"><span class="sw" style="background:var(--my-purple);"></span>Fair play</span>
                <span class="v">{{ $levelMetrics['fair_play'] }}%</span>
              </div>
              <div class="my-level-bar"><span style="width:{{ min(100, $levelMetrics['fair_play']) }}%; background:var(--my-purple);"></span></div>
            </div>
          </div>
        </div>
      @endif

      <div class="my-side-card">
        <h3>Atajos</h3>
        <a class="my-side-link" href="{{ route('venues.index') }}">
          <span>Buscar cancha</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a class="my-side-link" href="{{ route('falta-uno.index') }}">
          <span>Crear partido Falta Uno</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a class="my-side-link" href="{{ route('ranking.index') }}">
          <span>Mi ranking</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a class="my-side-link" href="{{ route('venues.favorites') }}">
          <span>Favoritos</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a class="my-side-link" href="{{ route('profile.edit') }}">
          <span>Configuración</span>
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      </div>

    </aside>
  </div>

</div>{{-- /.my-page --}}
</div>{{-- /.my-scope --}}

@push('scripts')
<script>
  // ── Tab switching ────────────────────────────────────────────────────
  document.querySelectorAll('.my-tab').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = btn.dataset.panel;
      document.querySelectorAll('.my-tab').forEach(b => b.classList.toggle('active', b === btn));
      document.querySelectorAll('.my-panel').forEach(p => p.classList.toggle('on', p.id === 'my-panel-' + target));
    });
  });

  // ── Subfilters: turnos ───────────────────────────────────────────────
  document.querySelectorAll('[data-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
      const f = btn.dataset.filter;
      document.querySelectorAll('[data-filter]').forEach(b => b.classList.toggle('active', b === btn));
      document.querySelectorAll('[data-subpanel]').forEach(p => p.style.display = p.dataset.subpanel === f ? '' : 'none');
    });
  });

  // ── Subfilters: recurrentes ──────────────────────────────────────────
  document.querySelectorAll('[data-rec-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
      const f = btn.dataset.recFilter;
      document.querySelectorAll('[data-rec-filter]').forEach(b => b.classList.toggle('active', b === btn));
      document.querySelectorAll('[data-rec-status]').forEach(el => {
        el.style.display = el.dataset.recStatus === f ? '' : 'none';
      });
    });
  });

  // ── Subfilters: falta uno ────────────────────────────────────────────
  document.querySelectorAll('[data-fu-filter]').forEach(btn => {
    btn.addEventListener('click', () => {
      const f = btn.dataset.fuFilter;
      document.querySelectorAll('[data-fu-filter]').forEach(b => b.classList.toggle('active', b === btn));
      document.querySelectorAll('[data-fu-subpanel]').forEach(p => p.style.display = p.dataset.fuSubpanel === f ? '' : 'none');
    });
  });
</script>
@endpush

@endsection
