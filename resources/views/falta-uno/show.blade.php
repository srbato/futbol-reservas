@extends('layouts.app')

@section('title', 'Partido · ' . ($game->field->name ?? 'Falta Uno'))

@php
  use Illuminate\Support\Facades\Storage;

  $sportLabel = match($game->field->sport ?? '') {
    'football'   => 'Fútbol',
    'football5'  => 'Fútbol 5',
    'football7'  => 'Fútbol 7',
    'padel'      => 'Pádel',
    'tennis'     => 'Tenis',
    'basketball' => 'Básquet',
    'volleyball' => 'Vóley',
    'hockey'     => 'Hockey',
    default      => ucfirst(str_replace('_',' ',$game->field->sport ?? 'Deporte')),
  };

  $statusLabel = match($game->status) {
    'open'      => 'Buscando jugadores',
    'full'      => 'Completo',
    'finished'  => 'Finalizado',
    'cancelled' => 'Cancelado',
    'expired'   => 'Expirado',
    default     => ucfirst($game->status ?? ''),
  };

  $extraJoined  = $game->activeParticipants->count();
  $initiatorN   = (int) ($game->initiator_players ?? 1);
  $needed       = (int) $game->total_players;
  $joined       = $extraJoined + $initiatorN;
  $remaining    = max(0, $needed - $joined);
  $fillPct      = $needed > 0 ? min(100, round(($joined / $needed) * 100)) : 0;
  $filled       = $needed > 0 ? round(($joined / $needed) * 100) : 0;
  $empty        = 100 - $filled;

  $genderLabel  = match($game->gender_filter) {
    'male' => 'Masculino', 'female' => 'Femenino', default => 'Mixto',
  };

  $ageRangeLabel = $game->ageRangeLabel();

  $catLabel = null;
  if ($game->category_min || $game->category_max) {
    if ($game->category_min === $game->category_max && $game->category_min) {
      $catLabel = ucfirst($game->category_min);
    } elseif ($game->category_min && $game->category_max) {
      $catLabel = ucfirst($game->category_min) . ' – ' . ucfirst($game->category_max);
    } else {
      $catLabel = $game->category_min ? 'Desde ' . ucfirst($game->category_min) : 'Hasta ' . ucfirst($game->category_max);
    }
  }

  $perPerson = ($game->reservation && $needed > 0)
    ? round(((float) $game->reservation->total_amount * $needed) / $needed / $needed * $needed / $needed, 0)
    : 0;
  // Mejor cálculo: total_amount es lo que pagó el iniciador por initiator_players porciones.
  $totalCancha = ($game->reservation && $initiatorN > 0)
    ? round((float) $game->reservation->total_amount * $needed / $initiatorN, 0)
    : 0;
  $perPerson   = $needed > 0 ? round($totalCancha / $needed, 0) : 0;

  $endAt = $game->start_at->copy()->addMinutes((int) ($game->field->slot_minutes ?? 60));

  // Avatar gradient deterministic from user id
  $avaColor = function($userId, $name) {
    $palette = [
      ['#4ade80','#22a55a'], ['#7abef5','#2a6aaa'], ['#fda4af','#be123c'],
      ['#a78bfa','#5a3da8'], ['#f5c17a','#a88844'], ['#94e8c4','#33996c'],
      ['#fbbf24','#a16207'], ['#f472b6','#9d174d'],
    ];
    $idx = abs(crc32((string)$userId)) % count($palette);
    return ['from' => $palette[$idx][0], 'to' => $palette[$idx][1], 'initial' => strtoupper(mb_substr($name ?? '?', 0, 1))];
  };

  // Surface label
  $surfaceLabel = $game->field->surface ? ucfirst($game->field->surface) : null;

  // Reglas / setting
  $setting    = $game->field->faltaUnoSetting;
  $refundMin  = $setting->refund_deadline_minutes ?? 60;
  $fillMin    = $setting->fill_deadline_minutes ?? 120;
  $lateMin    = $setting->late_leave_deadline_minutes ?? 240;
  $fmtMin = function($min) {
    if ($min >= 1440) { $d = floor($min/1440); $h = floor(($min%1440)/60); return $d.' día'.($d>1?'s':'').($h>0?' y '.$h.'h':''); }
    if ($min >= 60)   { $h = floor($min/60); $m = $min%60; return $h.'h'.($m>0?' '.$m.'min':''); }
    return $min.' minutos';
  };

  $canRate = $userId = auth()->id();
  $canRate = $canRate && $game->isFinished() && !$yaCalifico && $isParticipant && !$wasNoShow;

  $heroImage = $game->field->cover_image_path
    ? Storage::url($game->field->cover_image_path)
    : ($game->field->venue->cover_image_path ? Storage::url($game->field->venue->cover_image_path) : '/images/jugadores-falta-uno.webp');

  // Open graph
  $ogSlotsLeft = $remaining;
@endphp

@section('og_title', 'Partido de ' . $sportLabel . ' en ' . $game->field->venue->name . ' — Falta Uno')
@section('og_description', $game->start_at->format('d/m/Y') . ' a las ' . $game->start_at->format('H:i') . ' hs — ' . ($ogSlotsLeft > 0 ? $ogSlotsLeft . ' lugar' . ($ogSlotsLeft > 1 ? 'es' : '') . ' disponible' . ($ogSlotsLeft > 1 ? 's' : '') : 'Completo') . ' — ' . $game->field->venue->name)
@if($game->field->venue->cover_image_path)
  @section('og_image', Storage::url($game->field->venue->cover_image_path))
@endif

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@200;300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
@endpush

@push('styles')
<style>
  :root {
    --fs-bg: #050505;
    --fs-bg-1: #0a0a0a;
    --fs-bg-2: #111;
    --fs-bg-3: #161616;
    --fs-bd: rgba(255,255,255,.07);
    --fs-bd-2: rgba(255,255,255,.14);
    --fs-tx: #f2f2f2;
    --fs-tx-2: #c8c8c8;
    --fs-tx-3: #8a8a8a;
    --fs-tx-4: #555;
    --fs-accent: #4ade80;
    --fs-accent-ink: #052010;
    --fs-accent-hover: #6ee7a0;
    --fs-accent-soft: rgba(74,222,128,.08);
    --fs-warn: #f5c17a;
    --fs-danger: #f87171;
    --fs-blue: #7abef5;
    --fs-purple: #a78bfa;
    --fs-mono: 'JetBrains Mono', ui-monospace, monospace;
  }
  .fs-page { font-family: 'Sora', system-ui, sans-serif; max-width: 1240px; width: 100%; margin: 0 auto; padding: 8px 0 60px; color: var(--fs-tx); overflow-x: clip; }
  .fs-page * { box-sizing: border-box; min-width: 0; }

  /* CRUMB */
  .fs-crumb { display: flex; align-items: center; gap: 8px; padding: 0 0 22px; font-size: 12px; color: var(--fs-tx-3); flex-wrap: wrap; }
  .fs-crumb a { color: var(--fs-tx-3); text-decoration: none; transition: color .15s; }
  .fs-crumb a:hover { color: var(--fs-tx-2); }
  .fs-crumb svg { opacity: .5; }
  .fs-crumb b { color: var(--fs-tx-2); font-weight: 500; }

  /* HERO */
  .fs-hero {
    position: relative;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid var(--fs-bd);
    background: var(--fs-bg-1);
    aspect-ratio: 21 / 8;
    min-height: 360px;
    margin-bottom: 28px;
  }
  .fs-hero-bg { position: absolute; inset: 0; background-size: cover; background-position: center; filter: saturate(.9) brightness(.62); transform: scale(1.04); animation: fsHeroPan 22s ease-in-out infinite alternate; }
  @keyframes fsHeroPan { from { transform: scale(1.04) translateX(-8px); } to { transform: scale(1.08) translateX(8px); } }
  .fs-hero-grad { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(5,5,5,.15) 0%, rgba(5,5,5,.55) 60%, rgba(5,5,5,.92) 100%), linear-gradient(90deg, rgba(5,5,5,.85) 0%, rgba(5,5,5,.2) 50%, rgba(5,5,5,.5) 100%); }
  .fs-hero-inner { position: relative; height: 100%; padding: 36px 44px; display: grid; grid-template-columns: minmax(0,1fr) auto; align-items: end; gap: 36px; }
  .fs-hero-left { display: flex; flex-direction: column; gap: 16px; max-width: 720px; }
  .fs-hero-status {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 6px 12px;
    background: rgba(74,222,128,.1); border: 1px solid rgba(74,222,128,.25);
    border-radius: 999px; font-size: 11px; font-weight: 600; letter-spacing: .06em;
    color: var(--fs-accent); width: fit-content;
  }
  .fs-hero-status.full { background: rgba(74,222,128,.18); }
  .fs-hero-status.cancelled, .fs-hero-status.expired { background: rgba(248,113,113,.1); border-color: rgba(248,113,113,.25); color: var(--fs-danger); }
  .fs-hero-status.finished { background: rgba(167,139,250,.1); border-color: rgba(167,139,250,.25); color: var(--fs-purple); }
  .fs-hero-status .pulse { width: 7px; height: 7px; border-radius: 50%; background: currentColor; animation: fsPulse 1.6s infinite; }
  @keyframes fsPulse { 0% { box-shadow: 0 0 0 0 rgba(74,222,128,.7); } 70% { box-shadow: 0 0 0 8px rgba(74,222,128,0); } 100% { box-shadow: 0 0 0 0 rgba(74,222,128,0); } }
  .fs-hero-eyebrow { font-size: 10px; font-weight: 600; letter-spacing: .2em; text-transform: uppercase; color: var(--fs-tx-3); }
  .fs-hero-title { margin: 0; font-size: clamp(28px, 5vw, 60px); font-weight: 200; letter-spacing: -0.04em; line-height: 1.05; color: var(--fs-tx); font-family: 'Sora', sans-serif; word-break: break-word; }
  .fs-hero-title b { font-weight: 600; }
  .fs-hero-sub { font-size: 14px; color: var(--fs-tx-2); margin: 4px 0 0; }
  .fs-hero-sub b { color: var(--fs-tx); font-weight: 500; }
  .fs-hero-tags { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px; }
  .fs-hero-tag { padding: 6px 12px; background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1); border-radius: 999px; font-size: 11px; color: var(--fs-tx-2); display: inline-flex; align-items: center; gap: 7px; backdrop-filter: blur(8px); }
  .fs-hero-tag .ico { color: var(--fs-accent); display: inline-flex; }
  .fs-hero-tag b { color: var(--fs-tx); font-weight: 500; }

  /* HERO SLOT METER */
  .fs-hero-slot {
    background: rgba(5,5,5,.7); border: 1px solid var(--fs-bd-2);
    border-radius: 18px; padding: 22px 24px; backdrop-filter: blur(14px);
    min-width: 280px; display: flex; flex-direction: column; gap: 14px;
  }
  .fs-slot-head { display: flex; align-items: baseline; justify-content: space-between; gap: 12px; }
  .fs-slot-head .k { font-size: 10px; letter-spacing: .14em; text-transform: uppercase; color: var(--fs-tx-3); font-weight: 600; }
  .fs-slot-head .urgent { font-size: 10px; font-weight: 600; color: var(--fs-warn); padding: 3px 8px; background: rgba(245,193,122,.1); border-radius: 6px; letter-spacing: .04em; }
  .fs-slot-num { display: flex; align-items: baseline; gap: 6px; flex-wrap: wrap; }
  .fs-slot-num .v { font-size: 56px; font-weight: 200; letter-spacing: -0.04em; line-height: 1; color: var(--fs-tx); font-variant-numeric: tabular-nums; }
  .fs-slot-num .s { font-size: 22px; color: var(--fs-tx-3); font-weight: 300; }
  .fs-slot-num .label { font-size: 11px; color: var(--fs-tx-3); letter-spacing: .14em; text-transform: uppercase; margin-left: auto; }
  .fs-slot-bar { height: 4px; background: rgba(255,255,255,.06); border-radius: 2px; overflow: hidden; }
  .fs-slot-bar > span { display: block; height: 100%; background: linear-gradient(90deg, var(--fs-accent), var(--fs-accent-hover)); border-radius: 2px; transition: width .8s ease; }
  .fs-slot-people { display: flex; gap: 4px; flex-wrap: wrap; }
  .fs-slot-pip { flex: 1; min-width: 18px; height: 28px; border-radius: 6px; background: rgba(255,255,255,.04); border: 1px solid var(--fs-bd); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: var(--fs-tx-3); }
  .fs-slot-pip.on { background: linear-gradient(135deg, var(--fs-accent), #22a55a); border-color: transparent; color: var(--fs-accent-ink); }

  /* INFO STRIP */
  .fs-info { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 0; background: var(--fs-bg-1); border: 1px solid var(--fs-bd); border-radius: 16px; overflow: hidden; margin-bottom: 28px; }
  .fs-info-cell { padding: 18px 22px; border-right: 1px solid var(--fs-bd); }
  .fs-info-cell:last-child { border-right: 0; }
  .fs-info-cell .k { font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--fs-tx-3); margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
  .fs-info-cell .v { font-size: 18px; font-weight: 500; letter-spacing: -0.01em; color: var(--fs-tx); }
  .fs-info-cell .v b { font-weight: 600; color: var(--fs-accent); }
  .fs-info-cell .meta { font-size: 11px; color: var(--fs-tx-3); margin-top: 4px; }
  .fs-info-cell .meta b { color: var(--fs-tx-2); font-weight: 500; }
  @media (max-width: 760px) {
    .fs-info { grid-template-columns: 1fr 1fr; }
    .fs-info-cell:nth-child(odd) { border-right: 1px solid var(--fs-bd); }
    .fs-info-cell:nth-child(2) { border-right: 0; }
    .fs-info-cell:nth-child(1), .fs-info-cell:nth-child(2) { border-bottom: 1px solid var(--fs-bd); }
  }

  /* GRID */
  .fs-grid { display: grid; grid-template-columns: minmax(0,1fr) 360px; gap: 32px; align-items: flex-start; }
  .fs-grid > * { min-width: 0; }
  @media (max-width: 1100px) { .fs-grid { grid-template-columns: 1fr; } }

  /* SECTION */
  .fs-sec { margin-bottom: 32px; }
  .fs-sec-head { display: flex; align-items: center; gap: 14px; padding: 0 0 14px; margin-bottom: 18px; border-bottom: 1px solid var(--fs-bd); }
  .fs-sec-head h2 { margin: 0; font-size: 11px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--fs-tx-2); display: inline-flex; align-items: center; gap: 8px; font-family: 'Sora', sans-serif; }
  .fs-sec-head h2 .dot { width: 7px; height: 7px; border-radius: 50%; background: var(--fs-accent); }
  .fs-sec-head .ct { font-family: var(--fs-mono); font-size: 10px; color: var(--fs-tx-3); padding: 2px 7px; border: 1px solid var(--fs-bd); border-radius: 6px; }
  .fs-sec-head .ln { flex: 1; height: 1px; }
  .fs-sec-head .more { font-size: 12px; color: var(--fs-tx-3); transition: color .15s; text-decoration: none; }
  .fs-sec-head .more:hover { color: var(--fs-accent); }

  /* ORG card */
  .fs-org { display: grid; grid-template-columns: auto minmax(0,1fr) auto; gap: 16px; align-items: center; padding: 18px 22px; background: var(--fs-bg-1); border: 1px solid var(--fs-bd); border-radius: 16px; }
  .fs-org-ava { width: 52px; height: 52px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: #000; font-weight: 700; font-size: 18px; position: relative; flex: none; }
  .fs-org-info h3 { margin: 0 0 3px; font-size: 14px; font-weight: 500; color: var(--fs-tx); font-family: 'Sora', sans-serif; }
  .fs-org-info h3 .role { font-size: 10px; padding: 2px 7px; border-radius: 5px; background: var(--fs-accent-soft); color: var(--fs-accent); font-weight: 600; letter-spacing: .04em; margin-left: 6px; }
  .fs-org-meta { font-size: 12px; color: var(--fs-tx-3); display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
  .fs-org-meta b { color: var(--fs-tx-2); font-weight: 500; }
  .fs-org-meta .sep { width: 2px; height: 2px; border-radius: 50%; background: var(--fs-tx-4); }
  .fs-org-actions { display: flex; gap: 8px; flex-shrink: 0; }
  @media (max-width: 600px) { .fs-org { grid-template-columns: auto 1fr; } .fs-org-actions { grid-column: 1 / -1; } }

  /* MESSAGE / PRIVATE banners */
  .fs-banner { padding: 14px 18px; border-radius: 14px; margin-bottom: 18px; display: flex; gap: 12px; align-items: flex-start; font-size: 13px; line-height: 1.55; }
  .fs-banner.green { background: linear-gradient(160deg,rgba(74,222,128,.06),rgba(74,222,128,.01)); border: 1px solid rgba(74,222,128,.2); color: var(--fs-tx-2); }
  .fs-banner.green .h { color: var(--fs-accent); font-weight: 600; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 4px; }
  .fs-banner.purple { background: rgba(167,139,250,.06); border: 1px solid rgba(167,139,250,.18); color: var(--fs-tx-2); }
  .fs-banner.purple .h { color: var(--fs-purple); font-weight: 600; }
  .fs-banner.warn { background: rgba(245,193,122,.06); border: 1px solid rgba(245,193,122,.2); color: var(--fs-tx-2); }
  .fs-banner.warn .h { color: var(--fs-warn); font-weight: 600; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 4px; }
  .fs-banner.danger { background: rgba(248,113,113,.08); border: 1px solid rgba(248,113,113,.25); color: #fca5a5; }
  .fs-banner.danger .h { color: var(--fs-danger); font-weight: 600; font-size: 11px; letter-spacing: .12em; text-transform: uppercase; margin-bottom: 4px; }
  .fs-banner svg { flex-shrink: 0; margin-top: 2px; }

  /* ROSTER */
  .fs-roster { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 8px; }
  @media (max-width: 720px) { .fs-roster { grid-template-columns: 1fr; } }
  .fs-roster-row {
    background: var(--fs-bg-1); border: 1px solid var(--fs-bd); border-radius: 12px;
    padding: 12px 14px;
    display: grid; grid-template-columns: 28px minmax(0,1fr) auto; gap: 12px;
    align-items: center;
    transition: border-color .15s, background .15s;
    position: relative;
    text-decoration: none; color: inherit;
  }
  .fs-roster-row.empty { background: transparent; border: 1px dashed rgba(255,255,255,.08); }
  .fs-roster-row.empty:hover { border-color: rgba(74,222,128,.3); background: rgba(74,222,128,.02); }
  .fs-roster-row.you { border-color: rgba(74,222,128,.25); background: rgba(74,222,128,.04); }
  .fs-roster-num { font-family: var(--fs-mono); font-size: 10px; color: var(--fs-tx-4); font-weight: 500; text-align: center; }
  .fs-roster-info { display: grid; grid-template-columns: 32px minmax(0,1fr); gap: 10px; align-items: center; min-width: 0; }
  .fs-r-ava { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #000; flex: none; }
  .fs-r-name { display: flex; flex-direction: column; min-width: 0; }
  .fs-r-name .n { font-size: 13px; font-weight: 500; color: var(--fs-tx); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .fs-r-name .meta { font-size: 11px; color: var(--fs-tx-3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .fs-r-name .meta b { color: var(--fs-tx-2); font-weight: 500; }
  .fs-r-tag { font-size: 10px; font-weight: 600; padding: 3px 7px; border-radius: 5px; letter-spacing: .04em; flex-shrink: 0; }
  .fs-r-tag.host { background: var(--fs-accent-soft); color: var(--fs-accent); }
  .fs-r-tag.elo { background: rgba(255,255,255,.05); color: var(--fs-tx-2); border: 1px solid var(--fs-bd); font-family: var(--fs-mono); }
  .fs-r-tag.kick { background: rgba(248,113,113,.08); color: var(--fs-danger); border: 1px solid rgba(248,113,113,.25); cursor: pointer; }
  .fs-r-tag.kick:hover { background: rgba(248,113,113,.18); }
  .fs-roster-empty-cta {
    grid-column: 1 / -1;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    color: var(--fs-tx-3); font-size: 12px; padding: 4px 0;
  }
  .fs-roster-empty-cta .plus {
    width: 22px; height: 22px; border-radius: 6px;
    background: rgba(74,222,128,.06); border: 1px dashed rgba(74,222,128,.2);
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--fs-accent);
  }
  .fs-roster-empty-cta .num { font-family: var(--fs-mono); color: var(--fs-tx-4); }

  /* VENUE CARD */
  .fs-venue { display: grid; grid-template-columns: minmax(0,1fr) 220px; gap: 0; background: var(--fs-bg-1); border: 1px solid var(--fs-bd); border-radius: 16px; overflow: hidden; }
  @media (max-width: 760px) { .fs-venue { grid-template-columns: 1fr; } }
  .fs-venue-info { padding: 22px; display: flex; flex-direction: column; gap: 14px; min-width: 0; }
  .fs-venue-info h3 { margin: 0; font-size: 18px; font-weight: 500; letter-spacing: -0.01em; color: var(--fs-tx); font-family: 'Sora', sans-serif; }
  .fs-venue-meta { font-size: 12px; color: var(--fs-tx-3); display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .fs-venue-meta .star { color: var(--fs-warn); }
  .fs-venue-meta b { color: var(--fs-tx-2); font-weight: 500; }
  .fs-venue-meta .sep { width: 2px; height: 2px; border-radius: 50%; background: var(--fs-tx-4); }
  .fs-venue-tags { display: flex; flex-wrap: wrap; gap: 6px; }
  .fs-venue-tag { font-size: 11px; color: var(--fs-tx-2); padding: 4px 9px; background: rgba(255,255,255,.04); border: 1px solid var(--fs-bd); border-radius: 6px; }
  .fs-venue-cta { display: flex; gap: 8px; margin-top: auto; flex-wrap: wrap; }
  .fs-venue-map { background: linear-gradient(135deg, #0a0e0c, #0a1610); position: relative; border-left: 1px solid var(--fs-bd); overflow: hidden; min-height: 180px; }
  .fs-venue-map svg { position: absolute; inset: 0; width: 100%; height: 100%; }

  /* RULES */
  .fs-rules { background: var(--fs-bg-1); border: 1px solid var(--fs-bd); border-radius: 16px; padding: 22px; display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 0; }
  @media (max-width: 760px) { .fs-rules { grid-template-columns: 1fr; gap: 18px; } }
  .fs-rule { padding: 0 22px; border-right: 1px solid var(--fs-bd); }
  @media (max-width: 760px) { .fs-rule { padding: 0 0 18px; border-right: 0; border-bottom: 1px solid var(--fs-bd); } .fs-rule:last-child { border-bottom: 0; padding-bottom: 0; } }
  .fs-rule:first-child { padding-left: 0; }
  .fs-rule:last-child { padding-right: 0; border-right: 0; }
  .fs-rule .ico { width: 32px; height: 32px; border-radius: 9px; background: var(--fs-accent-soft); color: var(--fs-accent); display: flex; align-items: center; justify-content: center; margin-bottom: 12px; }
  .fs-rule h4 { margin: 0 0 4px; font-size: 13px; font-weight: 500; color: var(--fs-tx); font-family: 'Sora', sans-serif; }
  .fs-rule p { margin: 0; font-size: 12px; color: var(--fs-tx-3); line-height: 1.5; }

  /* CHAT */
  .fs-chat { background: var(--fs-bg-1); border: 1px solid var(--fs-bd); border-radius: 16px; overflow: hidden; }
  .fs-chat-msgs { padding: 18px 22px; display: flex; flex-direction: column; gap: 16px; }
  .fs-chat-empty { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 18px; color: var(--fs-tx-3); font-size: 13px; text-align: center; }
  .fs-chat-empty svg { color: var(--fs-tx-4); }
  .fs-msg { display: grid; grid-template-columns: 32px minmax(0,1fr); gap: 12px; }
  .fs-msg-ava { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: #000; flex: none; }
  .fs-msg-body { min-width: 0; }
  .fs-msg-head { display: flex; align-items: baseline; gap: 8px; margin-bottom: 4px; flex-wrap: wrap; }
  .fs-msg-head .n { font-size: 13px; font-weight: 500; color: var(--fs-tx); }
  .fs-msg-head .t { font-size: 11px; color: var(--fs-tx-4); font-family: var(--fs-mono); }
  .fs-msg-head .role { font-size: 9px; padding: 1px 6px; border-radius: 4px; background: var(--fs-accent-soft); color: var(--fs-accent); font-weight: 600; letter-spacing: .06em; text-transform: uppercase; }
  .fs-msg-text { font-size: 13px; color: var(--fs-tx-2); line-height: 1.5; word-wrap: break-word; }
  .fs-chat-input { display: grid; grid-template-columns: 32px minmax(0,1fr) auto; gap: 12px; align-items: center; padding: 14px 18px; border-top: 1px solid var(--fs-bd); background: rgba(255,255,255,.015); text-decoration: none; transition: background .15s; }
  .fs-chat-input:hover { background: rgba(255,255,255,.03); }
  .fs-chat-input .ph { color: var(--fs-tx-4); font-size: 13px; }
  .fs-chat-input .send { font-size: 12px; font-weight: 500; color: var(--fs-accent); display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: var(--fs-accent-soft); border-radius: 8px; }
  .fs-chat-input-locked { display: flex; align-items: center; justify-content: center; gap: 8px; color: var(--fs-tx-4); font-size: 12px; cursor: default; }
  .fs-chat-input-locked:hover { background: rgba(255,255,255,.015); }
  .fs-chat-input-locked svg { color: var(--fs-tx-4); flex: none; }

  /* SIDEBAR (CTA) */
  .fs-side { display: flex; flex-direction: column; gap: 18px; position: sticky; top: 90px; align-self: start; }
  @media (max-width: 1100px) { .fs-side { position: static; } }
  .fs-cta-card {
    background: linear-gradient(180deg, var(--fs-bg-1), var(--fs-bg-2));
    border: 1px solid var(--fs-bd-2);
    border-radius: 18px; padding: 22px;
    box-shadow: 0 30px 80px -20px rgba(0,0,0,.6);
    position: relative; overflow: hidden;
  }
  .fs-cta-card::before {
    content: '';
    position: absolute; top: -100px; right: -100px;
    width: 240px; height: 240px;
    background: radial-gradient(circle, rgba(74,222,128,.18), transparent 60%);
    pointer-events: none;
  }
  .fs-cta-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; position: relative; gap: 8px; flex-wrap: wrap; }
  .fs-cta-head .k { font-size: 10px; letter-spacing: .14em; text-transform: uppercase; color: var(--fs-tx-3); font-weight: 600; }
  .fs-cta-head .price { font-size: 20px; font-weight: 300; color: var(--fs-tx); letter-spacing: -0.02em; }
  .fs-cta-head .price b { font-weight: 600; }
  .fs-cta-head .price small { font-size: 11px; color: var(--fs-tx-3); font-weight: 400; }

  .fs-cta-when {
    background: rgba(255,255,255,.025); border: 1px solid var(--fs-bd);
    border-radius: 12px; padding: 14px 16px;
    display: grid; grid-template-columns: auto minmax(0,1fr); gap: 14px;
    align-items: center; margin-bottom: 14px; position: relative;
  }
  .fs-cta-when .date { width: 56px; flex: none; text-align: center; padding-right: 14px; border-right: 1px solid var(--fs-bd); }
  .fs-cta-when .date .day { font-size: 9px; color: var(--fs-accent); letter-spacing: .14em; text-transform: uppercase; font-weight: 600; }
  .fs-cta-when .date .d { font-size: 26px; font-weight: 300; color: var(--fs-tx); line-height: 1; letter-spacing: -0.04em; font-variant-numeric: tabular-nums; }
  .fs-cta-when .date .m { font-size: 9px; letter-spacing: .14em; text-transform: uppercase; color: var(--fs-tx-3); font-weight: 600; margin-top: 3px; }
  .fs-cta-when .when-info .h { font-size: 14px; color: var(--fs-tx); font-weight: 500; margin-bottom: 2px; }
  .fs-cta-when .when-info .s { font-size: 12px; color: var(--fs-tx-3); }
  .fs-cta-when .when-info b { color: var(--fs-tx-2); font-weight: 500; }

  .fs-cta-row { display: flex; align-items: center; justify-content: space-between; padding: 8px 0; font-size: 12px; gap: 10px; }
  .fs-cta-row .l { color: var(--fs-tx-3); display: inline-flex; align-items: center; gap: 8px; }
  .fs-cta-row .l svg { opacity: .55; }
  .fs-cta-row .v { color: var(--fs-tx-2); font-weight: 500; text-align: right; }
  .fs-cta-row + .fs-cta-row { border-top: 1px dashed var(--fs-bd); }

  .fs-cta-progress { margin: 14px 0 6px; }
  .fs-cta-progress .row { display: flex; justify-content: space-between; font-size: 11px; color: var(--fs-tx-3); margin-bottom: 6px; }
  .fs-cta-progress .row b { color: var(--fs-tx); font-weight: 600; }
  .fs-cta-progress .bar { height: 6px; background: rgba(255,255,255,.06); border-radius: 3px; overflow: hidden; }
  .fs-cta-progress .bar > span { display: block; height: 100%; background: linear-gradient(90deg, var(--fs-accent), var(--fs-accent-hover)); border-radius: 3px; }

  .fs-cta-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    padding: 14px 20px; background: var(--fs-accent); color: var(--fs-accent-ink);
    border-radius: 12px; font-size: 14px; font-weight: 600; width: 100%;
    margin-top: 16px; transition: background .15s, transform .15s;
    border: none; cursor: pointer; font-family: inherit; text-decoration: none;
  }
  .fs-cta-btn:hover { background: var(--fs-accent-hover); transform: translateY(-1px); color: var(--fs-accent-ink); }
  .fs-cta-btn.ghost { background: rgba(255,255,255,.05); color: var(--fs-tx-2); border: 1px solid var(--fs-bd); }
  .fs-cta-btn.ghost:hover { background: rgba(255,255,255,.1); color: var(--fs-tx); }
  .fs-cta-btn.danger { background: rgba(248,113,113,.1); color: var(--fs-danger); border: 1px solid rgba(248,113,113,.25); }
  .fs-cta-btn.danger:hover { background: rgba(248,113,113,.2); color: var(--fs-danger); }
  .fs-cta-btn:disabled { opacity: .5; cursor: not-allowed; }

  .fs-cta-secondary { display: flex; gap: 8px; margin-top: 10px; }
  .fs-cta-secondary a, .fs-cta-secondary button { flex: 1; padding: 11px; background: rgba(255,255,255,.04); border: 1px solid var(--fs-bd); color: var(--fs-tx-2); border-radius: 10px; font-size: 12px; font-weight: 500; display: inline-flex; align-items: center; justify-content: center; gap: 6px; transition: background .15s, color .15s; text-decoration: none; cursor: pointer; font-family: inherit; }
  .fs-cta-secondary a:hover, .fs-cta-secondary button:hover { background: rgba(255,255,255,.08); color: var(--fs-tx); }

  .fs-cta-foot { font-size: 11px; color: var(--fs-tx-3); text-align: center; margin-top: 12px; line-height: 1.5; }
  .fs-cta-foot b { color: var(--fs-tx-2); font-weight: 500; }

  /* ORGANIZER PANEL */
  .fs-org-panel {
    background: rgba(245,193,122,.04); border: 1px solid rgba(245,193,122,.18);
    border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 10px;
  }
  .fs-org-panel .k { font-size: 10px; letter-spacing: .14em; text-transform: uppercase; color: var(--fs-warn); font-weight: 600; display: flex; align-items: center; gap: 6px; }
  .fs-org-panel p { margin: 0; font-size: 11px; color: var(--fs-tx-3); line-height: 1.45; }
  .fs-op-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 6px; margin-top: 4px; }
  .fs-op-grid button, .fs-op-grid a { padding: 9px; background: rgba(255,255,255,.03); border: 1px solid var(--fs-bd); color: var(--fs-tx-2); border-radius: 8px; font-size: 11px; font-weight: 500; display: inline-flex; align-items: center; justify-content: center; gap: 6px; transition: background .15s, color .15s; cursor: pointer; font-family: inherit; text-decoration: none; }
  .fs-op-grid button:hover, .fs-op-grid a:hover { background: rgba(255,255,255,.06); color: var(--fs-tx); }
  .fs-op-grid .danger { color: var(--fs-danger); }
  .fs-op-grid form { width: 100%; }
  .fs-op-grid form button { width: 100%; }

  /* btn helpers */
  .fs-btn { padding: 8px 14px; font-size: 12px; font-weight: 500; border-radius: 9px; display: inline-flex; align-items: center; gap: 6px; transition: background .15s, color .15s; text-decoration: none; cursor: pointer; border: none; font-family: inherit; }
  .fs-btn-ghost { background: rgba(255,255,255,.04); border: 1px solid var(--fs-bd); color: var(--fs-tx-2); }
  .fs-btn-ghost:hover { background: rgba(255,255,255,.08); color: var(--fs-tx); }
  .fs-btn-prim { background: var(--fs-accent); color: var(--fs-accent-ink); font-weight: 600; }
  .fs-btn-prim:hover { background: var(--fs-accent-hover); color: var(--fs-accent-ink); }

  @media (max-width: 760px) {
    .fs-page { padding: 4px 0 40px; }
    .fs-hero-inner { grid-template-columns: 1fr; padding: 24px; gap: 20px; }
    .fs-hero-slot { min-width: 0; width: 100%; }
  }
</style>
@endpush

@section('content')

<div class="fs-page">

  {{-- breadcrumb --}}
  <nav class="fs-crumb">
    <a href="{{ route('falta-uno.index') }}">Falta Uno</a>
    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
    <a href="{{ route('venues.show', $game->field->venue) }}">{{ $game->field->venue->name }}</a>
    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
    <b>{{ $game->field->name }} · {{ $sportLabel }} · {{ $game->start_at->locale('es')->isoFormat('ddd D MMM') }}</b>
  </nav>

  {{-- HERO --}}
  <section class="fs-hero">
    <div class="fs-hero-bg" style="background-image:url('{{ $heroImage }}')"></div>
    <div class="fs-hero-grad"></div>
    <div class="fs-hero-inner">
      <div class="fs-hero-left">
        <span class="fs-hero-status {{ $game->status }}">
          <span class="pulse"></span>FALTA UNO · {{ strtoupper($statusLabel) }}
        </span>
        <div>
          <span class="fs-hero-eyebrow">{{ ucfirst($game->start_at->locale('es')->isoFormat('ddd D [de] MMMM')) }} · {{ $game->start_at->format('H:i') }}</span>
          <h1 class="fs-hero-title">{{ $game->field->name }} · <b>{{ $sportLabel }}</b></h1>
          <p class="fs-hero-sub">Organizado por <b>{{ $game->initiator->name ?? 'Anónimo' }}</b> en <b>{{ $game->field->venue->name }}</b>@if($game->field->venue->zone) · {{ $game->field->venue->zone }}@endif</p>
        </div>
        <div class="fs-hero-tags">
          <span class="fs-hero-tag"><span class="ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg></span>{{ $genderLabel }}</span>
          @if($catLabel)
            <span class="fs-hero-tag"><span class="ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M2 12h20"/></svg></span>{{ $catLabel }}</span>
          @endif
          @if($ageRangeLabel)
            <span class="fs-hero-tag"><span class="ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>{{ $ageRangeLabel }}</span>
          @endif
          <span class="fs-hero-tag"><span class="ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></span>{{ $game->field->slot_minutes ?? 60 }} min</span>
          @if($surfaceLabel)
            <span class="fs-hero-tag"><span class="ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 22h18M5 22V8h14v14"/></svg></span>{{ $surfaceLabel }}</span>
          @endif
        </div>
      </div>

      {{-- Slot meter --}}
      <div class="fs-hero-slot">
        <div class="fs-slot-head">
          <span class="k">Cupo del partido</span>
          @if($remaining > 0 && $game->status === 'open')
            <span class="urgent">Faltan {{ $remaining }}</span>
          @elseif($game->status === 'full')
            <span class="urgent" style="color:var(--fs-accent); background:rgba(74,222,128,.1);">Completo</span>
          @endif
        </div>
        <div class="fs-slot-num">
          <span class="v">{{ $joined }}</span>
          <span class="s">/ {{ $needed }}</span>
          <span class="label">JUGADORES</span>
        </div>
        <div class="fs-slot-bar"><span style="width:{{ $fillPct }}%"></span></div>
        <div class="fs-slot-people">
          @php $pipsCount = min($needed, 12); @endphp
          @for($i = 0; $i < $pipsCount; $i++)
            @php
              $isOn = $i < $joined;
              $initial = '';
              if ($isOn) {
                if ($i === 0) {
                  $initial = strtoupper(mb_substr($game->initiator->name ?? '?', 0, 1));
                } elseif ($i < $initiatorN) {
                  $initial = '·';
                } else {
                  $p = $game->activeParticipants->values()->get($i - $initiatorN);
                  $initial = $p ? strtoupper(mb_substr($p->user->name ?? '?', 0, 1)) : '·';
                }
              } else {
                $initial = '+';
              }
            @endphp
            <span class="fs-slot-pip {{ $isOn ? 'on' : '' }}">{{ $initial }}</span>
          @endfor
        </div>
      </div>
    </div>
  </section>

  {{-- INFO STRIP --}}
  <section class="fs-info">
    <div class="fs-info-cell">
      <div class="k"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg> Cuándo</div>
      <div class="v">{{ ucfirst($game->start_at->locale('es')->isoFormat('ddd D MMM')) }} · {{ $game->start_at->format('H:i') }}</div>
      <div class="meta">Termina a las {{ $endAt->format('H:i') }}@if($game->start_at->isFuture()) · <b>{{ $game->start_at->locale('es')->diffForHumans(['parts' => 1]) }}</b>@endif</div>
    </div>
    <div class="fs-info-cell">
      <div class="k"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-7 8-13a8 8 0 0 0-16 0c0 6 8 13 8 13z"/></svg> Dónde</div>
      <div class="v">{{ $game->field->venue->name }}</div>
      <div class="meta">@if($game->field->venue->zone){{ $game->field->venue->zone }}@else{{ $game->field->venue->address ?? '' }}@endif</div>
    </div>
    <div class="fs-info-cell">
      <div class="k"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7h-9M14 17H5"/><circle cx="17" cy="17" r="3"/><circle cx="5" cy="7" r="3"/></svg> Nivel</div>
      <div class="v">{{ $catLabel ?? 'Abierto' }}</div>
      <div class="meta">{{ $catLabel ? 'Filtro de categoría aplicado' : 'Sin restricción de nivel' }}</div>
    </div>
    <div class="fs-info-cell">
      <div class="k"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="3"/><circle cx="15" cy="7" r="3"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg> Categoría</div>
      <div class="v">{{ $genderLabel }}@if($ageRangeLabel) · {{ $ageRangeLabel }}@endif</div>
      <div class="meta">{{ $game->is_private ? 'Partido privado' : 'Partido público' }}</div>
    </div>
  </section>

  {{-- Banners --}}
  @auth
    @if($wasNoShow)
      <div class="fs-banner danger">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <div>
          <div class="h">Fuiste marcado como ausente</div>
          El organizador registró que no te presentaste. Se aplicó una penalización a tu cuenta.
        </div>
      </div>
    @endif
    @if($wasKicked)
      <div class="fs-banner danger">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><path d="M22 11l-3-3m0 6l3-3"/></svg>
        <div>
          <div class="h">Fuiste retirado del partido</div>
          El organizador te retiró de este partido.
        </div>
      </div>
    @endif
    @if(!empty($venueBlock) && !$isInitiator && !$isJoined)
      <div class="fs-banner danger">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <div>
          <div class="h">No podés sumarte a este complejo</div>
          El complejo te bloqueó @if($venueBlock->reason)por: <em>"{{ $venueBlock->reason }}"</em>.@else manualmente.@endif Contactá al complejo si pensás que es un error.
        </div>
      </div>
    @endif
    @if($isJoined && $wouldBeLateLeave && in_array($game->status, ['open','full']))
      <div class="fs-banner warn">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01"/></svg>
        <div>
          <div class="h">Bajarse ahora cuenta como tardía</div>
          Si te bajás del partido en este momento se aplicará una penalización a tu cuenta. El plazo era hasta {{ $fmtMin($lateMin) }} antes del inicio.
        </div>
      </div>
    @endif
  @endauth

  @if(!empty($game->message))
    <div class="fs-banner green">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      <div>
        <div class="h">Mensaje del organizador</div>
        <span style="white-space:pre-wrap;">{{ $game->message }}</span>
      </div>
    </div>
  @endif

  @if(!empty($game->is_private))
    <div class="fs-banner purple">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      <div>
        <strong style="color:var(--fs-purple);">Partido privado.</strong> No aparece en el feed público — solo entra quien tenga el link.
      </div>
    </div>
  @endif

  @if($canRate)
    <div class="fs-banner green" style="border-color:rgba(74,222,128,.4);">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; flex:1;">
        <div>
          <div class="h">Calificá a los jugadores</div>
          El partido terminó. Ayudá a la comunidad calificando a los demás participantes.
        </div>
        <a href="{{ route('falta-uno.rate', $game) }}" class="fs-btn fs-btn-prim" style="padding:9px 16px;">Calificar ahora →</a>
      </div>
    </div>
  @endif

  {{-- GRID --}}
  <div class="fs-grid">
    <div>

      {{-- ORGANIZADOR --}}
      <section class="fs-sec">
        <div class="fs-sec-head">
          <h2><span class="dot"></span>Organizador</h2>
          <div class="ln"></div>
        </div>
        @php $oA = $avaColor($game->initiator->id ?? 0, $game->initiator->name ?? '?'); @endphp
        <div class="fs-org">
          <div class="fs-org-ava" style="background:linear-gradient(135deg,{{ $oA['from'] }},{{ $oA['to'] }})">{{ $oA['initial'] }}</div>
          <div class="fs-org-info">
            <h3>{{ $game->initiator->name ?? 'Anónimo' }} <span class="role">CAPITÁN</span></h3>
            <div class="fs-org-meta">
              @php $orgProfile = $game->initiator->faltaUnoSportProfiles->where('sport', $game->field->sport)->first(); @endphp
              @if($orgProfile)
                @if($orgProfile->average_rating > 0)
                  <span>★ <b>{{ number_format((float)$orgProfile->average_rating, 1) }}</b> rating</span>
                  <span class="sep"></span>
                @endif
                @if($orgProfile->games_played > 0)
                  <span><b>{{ $orgProfile->games_played }}</b> partidos jugados</span>
                  <span class="sep"></span>
                @endif
                @if($orgProfile->category)
                  <span style="text-transform:capitalize;"><b>{{ $orgProfile->category }}</b></span>
                @endif
              @else
                <span>Nuevo organizador</span>
              @endif
            </div>
          </div>
          <div class="fs-org-actions">
            <a href="{{ route('sport-profile.public', $game->initiator) }}" class="fs-btn fs-btn-ghost">Ver perfil</a>
          </div>
        </div>
      </section>

      {{-- ROSTER --}}
      <section class="fs-sec">
        <div class="fs-sec-head">
          <h2><span class="dot"></span>Jugadores anotados</h2>
          <span class="ct">{{ $joined }} / {{ $needed }}</span>
          <div class="ln"></div>
        </div>

        <div class="fs-roster">
          @php $slot = 1; @endphp

          {{-- Iniciador --}}
          @php $oA = $avaColor($game->initiator->id ?? 0, $game->initiator->name ?? '?'); @endphp
          <a href="{{ route('sport-profile.public', $game->initiator) }}" class="fs-roster-row {{ auth()->id() === $game->initiator_user_id ? 'you' : '' }}">
            <div class="fs-roster-num">{{ str_pad($slot++, 2, '0', STR_PAD_LEFT) }}</div>
            <div class="fs-roster-info">
              <div class="fs-r-ava" style="background:linear-gradient(135deg,{{ $oA['from'] }},{{ $oA['to'] }})">{{ $oA['initial'] }}</div>
              <div class="fs-r-name">
                <span class="n">{{ $game->initiator->name ?? 'Anónimo' }}</span>
                <span class="meta">@if($orgProfile && $orgProfile->average_rating > 0)<b>{{ number_format((float)$orgProfile->average_rating, 1) }}</b>★@else Organizador @endif</span>
              </div>
            </div>
            <span class="fs-r-tag host">CAPITÁN</span>
          </a>

          {{-- Lugares ocupados por el iniciador (anónimos) --}}
          @for($i = 1; $i < $initiatorN; $i++)
            <div class="fs-roster-row" style="opacity:.7;">
              <div class="fs-roster-num">{{ str_pad($slot++, 2, '0', STR_PAD_LEFT) }}</div>
              <div class="fs-roster-info">
                <div class="fs-r-ava" style="background:rgba(255,255,255,.1); color:var(--fs-tx-3);">·</div>
                <div class="fs-r-name">
                  <span class="n" style="color:var(--fs-tx-3);">Invitado del organizador</span>
                  <span class="meta">Confirmado por {{ $game->initiator->name ?? 'el capitán' }}</span>
                </div>
              </div>
              <span class="fs-r-tag elo">·</span>
            </div>
          @endfor

          {{-- Participantes activos --}}
          @foreach($game->activeParticipants as $p)
            @php
              $pA = $avaColor($p->user_id, $p->user->name ?? '?');
              $pProfile = $p->user->faltaUnoSportProfiles->where('sport', $game->field->sport)->first();
            @endphp
            <a href="{{ route('sport-profile.public', $p->user) }}" class="fs-roster-row {{ auth()->id() === $p->user_id ? 'you' : '' }}">
              <div class="fs-roster-num">{{ str_pad($slot++, 2, '0', STR_PAD_LEFT) }}</div>
              <div class="fs-roster-info">
                <div class="fs-r-ava" style="background:linear-gradient(135deg,{{ $pA['from'] }},{{ $pA['to'] }})">{{ $pA['initial'] }}</div>
                <div class="fs-r-name">
                  <span class="n">{{ $p->user->name ?? 'Anónimo' }}</span>
                  <span class="meta">@if($pProfile && $pProfile->average_rating > 0)<b>{{ number_format((float)$pProfile->average_rating, 1) }}</b>★@if($pProfile->category) · <span style="text-transform:capitalize">{{ $pProfile->category }}</span>@endif @else Nuevo @endif</span>
                </div>
              </div>
              @if($isInitiator && $p->user_id !== auth()->id() && in_array($game->status, ['open','full']) && !$game->isFinished())
                <form method="POST" action="{{ route('falta-uno.kick', [$game, $p->user]) }}" onsubmit="return confirm('¿Retirar a {{ $p->user->name }} del partido?'); event.stopPropagation();" onclick="event.stopPropagation();" style="display:inline;">
                  @csrf
                  <button type="submit" class="fs-r-tag kick" title="Retirar jugador" style="border:none;">×</button>
                </form>
              @else
                <span class="fs-r-tag elo">{{ $pProfile->category ?? '·' }}</span>
              @endif
            </a>
          @endforeach

          {{-- Lugares vacíos --}}
          @php $emptyCount = max(0, $needed - $joined); @endphp
          @for($i = 0; $i < $emptyCount; $i++)
            <div class="fs-roster-row empty">
              <span class="fs-roster-empty-cta">
                <span class="plus"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg></span>
                <span class="num">{{ str_pad($slot++, 2, '0', STR_PAD_LEFT) }}</span>
                <span>Lugar libre · sumate</span>
              </span>
            </div>
          @endfor
        </div>
      </section>

      {{-- NO-SHOWS (si organizador y hay marcados) --}}
      @if($isInitiator && $noShowParticipants->count() > 0)
        <section class="fs-sec">
          <div class="fs-sec-head">
            <h2><span class="dot" style="background:var(--fs-danger);"></span>Marcados como ausentes</h2>
            <span class="ct">{{ $noShowParticipants->count() }}</span>
            <div class="ln"></div>
          </div>
          <div class="fs-roster">
            @foreach($noShowParticipants as $ns)
              @php $nsA = $avaColor($ns->user_id, $ns->user->name ?? '?'); @endphp
              <div class="fs-roster-row" style="opacity:.5; border-color:rgba(248,113,113,.2);">
                <div class="fs-roster-num">—</div>
                <div class="fs-roster-info">
                  <div class="fs-r-ava" style="background:linear-gradient(135deg,{{ $nsA['from'] }},{{ $nsA['to'] }}); filter:grayscale(.5);">{{ $nsA['initial'] }}</div>
                  <div class="fs-r-name">
                    <span class="n" style="text-decoration:line-through;">{{ $ns->user->name }}</span>
                    <span class="meta" style="color:var(--fs-danger);">No se presentó</span>
                  </div>
                </div>
                <span class="fs-r-tag" style="background:rgba(248,113,113,.08); color:var(--fs-danger);">AUSENTE</span>
              </div>
            @endforeach
          </div>
        </section>
      @endif

      {{-- Marcar no-shows (organizador, post-partido) --}}
      @if($isInitiator && $game->isFinished() && $game->activeParticipants->count() > 0 && $noShowParticipants->count() === 0)
        <section class="fs-sec">
          <div class="fs-sec-head">
            <h2><span class="dot" style="background:var(--fs-warn);"></span>Marcar ausentes</h2>
            <div class="ln"></div>
          </div>
          <form method="POST" action="{{ route('falta-uno.no-shows', $game) }}" onsubmit="return confirm('¿Marcar a los seleccionados como ausentes? Se les aplicará una penalización.')" style="background:var(--fs-bg-1); border:1px solid var(--fs-bd); border-radius:14px; padding:18px;">
            @csrf
            <p style="margin:0 0 14px; font-size:13px; color:var(--fs-tx-3);">Marcá a los jugadores que no se presentaron. Se les aplicará una penalización en su cuenta.</p>
            <div style="display:flex; flex-direction:column; gap:8px;">
              @foreach($game->activeParticipants as $p)
                <label style="display:flex; align-items:center; gap:10px; padding:10px; background:rgba(255,255,255,.025); border:1px solid var(--fs-bd); border-radius:10px; cursor:pointer; font-size:13px; color:var(--fs-tx-2);">
                  <input type="checkbox" name="no_show_user_ids[]" value="{{ $p->user_id }}">
                  {{ $p->user->name }}
                </label>
              @endforeach
            </div>
            <button type="submit" class="fs-btn fs-btn-prim" style="margin-top:14px;">Marcar como ausentes</button>
          </form>
        </section>
      @endif

      {{-- VENUE --}}
      <section class="fs-sec">
        <div class="fs-sec-head">
          <h2><span class="dot"></span>Lugar del partido</h2>
          <div class="ln"></div>
          <a class="more" href="{{ route('venues.show', $game->field->venue) }}">Ver complejo →</a>
        </div>
        <div class="fs-venue">
          <div class="fs-venue-info">
            <h3>{{ $game->field->venue->name }}</h3>
            <div class="fs-venue-meta">
              @if(($game->field->venue->avg_rating ?? 0) > 0)
                <span class="star">★</span><span><b>{{ number_format($game->field->venue->avg_rating, 1) }}</b></span>
                <span class="sep"></span>
              @endif
              @if($game->field->venue->address)
                <span>{{ $game->field->venue->address }}@if($game->field->venue->zone) · <b>{{ $game->field->venue->zone }}</b>@endif</span>
              @elseif($game->field->venue->zone)
                <span><b>{{ $game->field->venue->zone }}</b></span>
              @endif
            </div>
            <div class="fs-venue-tags">
              <span class="fs-venue-tag">{{ $sportLabel }}</span>
              @if($surfaceLabel)<span class="fs-venue-tag">{{ $surfaceLabel }}</span>@endif
              @if($game->field->is_indoor)<span class="fs-venue-tag">Cubierta</span>@endif
              @if($game->field->has_lighting)<span class="fs-venue-tag">Iluminada</span>@endif
            </div>
            <div class="fs-venue-cta">
              @if($game->field->venue->lat && $game->field->venue->lng)
                <a class="fs-btn fs-btn-prim" href="https://www.google.com/maps/dir/?api=1&destination={{ $game->field->venue->lat }},{{ $game->field->venue->lng }}" target="_blank" rel="noopener">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 18-9-9 18-2-7z"/></svg>
                  Cómo llegar
                </a>
              @endif
              @if($game->field->venue->phone)
                <a class="fs-btn fs-btn-ghost" href="tel:{{ $game->field->venue->phone }}">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                  Llamar
                </a>
              @endif
            </div>
          </div>
          <div class="fs-venue-map">
            <svg viewBox="0 0 220 220" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <pattern id="fsGrid" width="20" height="20" patternUnits="userSpaceOnUse">
                  <path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
                </pattern>
                <radialGradient id="fsPing" cx="50%" cy="50%">
                  <stop offset="0%" stop-color="#4ade80" stop-opacity="0.45"/>
                  <stop offset="100%" stop-color="#4ade80" stop-opacity="0"/>
                </radialGradient>
              </defs>
              <rect width="220" height="220" fill="url(#fsGrid)"/>
              <path d="M 0 80 L 220 80" stroke="rgba(255,255,255,0.06)" stroke-width="2"/>
              <path d="M 0 140 L 220 140" stroke="rgba(255,255,255,0.06)" stroke-width="2"/>
              <path d="M 80 0 L 80 220" stroke="rgba(255,255,255,0.06)" stroke-width="2"/>
              <path d="M 150 0 L 150 220" stroke="rgba(255,255,255,0.06)" stroke-width="2"/>
              <rect x="92" y="92" width="48" height="48" rx="4" fill="rgba(74,222,128,0.05)" stroke="rgba(74,222,128,0.25)"/>
              <circle cx="116" cy="116" r="44" fill="url(#fsPing)">
                <animate attributeName="r" from="20" to="44" dur="2.4s" repeatCount="indefinite"/>
                <animate attributeName="opacity" from="1" to="0" dur="2.4s" repeatCount="indefinite"/>
              </circle>
              <circle cx="116" cy="116" r="6" fill="#4ade80" stroke="#052010" stroke-width="2"/>
            </svg>
          </div>
        </div>
      </section>

      {{-- RULES --}}
      <section class="fs-sec">
        <div class="fs-sec-head">
          <h2><span class="dot"></span>Reglas del partido</h2>
          <div class="ln"></div>
        </div>
        <div class="fs-rules">
          <div class="fs-rule">
            <div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>
            <h4>Cancelación con reembolso</h4>
            <p>Hasta <b style="color:var(--fs-tx-2)">{{ $fmtMin($refundMin) }} antes</b> del inicio te devolvemos el 100%.</p>
          </div>
          <div class="fs-rule">
            <div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg></div>
            <h4>Garantía de jugadores</h4>
            <p>Si el partido no se completa <b style="color:var(--fs-tx-2)">{{ $fmtMin($fillMin) }}</b> antes, se cancela automáticamente.</p>
          </div>
          <div class="fs-rule">
            <div class="ico"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3 8-8"/></svg></div>
            <h4>Bajas y fair play</h4>
            <p>Quien se baje con menos de <b style="color:var(--fs-tx-2)">{{ $fmtMin($lateMin) }}</b> de anticipación recibe penalización.</p>
          </div>
        </div>
      </section>

      {{-- CHAT PREVIEW --}}
      @if(in_array($game->status, ['open','full','finished']))
        <section class="fs-sec" style="margin-bottom:0;">
          <div class="fs-sec-head">
            <h2><span class="dot"></span>Comentarios del partido</h2>
            @if($totalMessages > 0)<span class="ct">{{ $totalMessages }}</span>@endif
            <div class="ln"></div>
            @if($isParticipant && !$wasNoShow)
              <a class="more" href="{{ route('falta-uno.chat', $game) }}">Abrir chat →</a>
            @endif
          </div>
          <div class="fs-chat">
            <div class="fs-chat-msgs">
              @if($recentMessages->isEmpty())
                <div class="fs-chat-empty">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                  <span>Todavía no hay comentarios. {{ $isParticipant ? 'Sé el primero en escribir.' : 'Sumate al partido para comentar.' }}</span>
                </div>
              @else
                @foreach($recentMessages as $msg)
                  @php
                    $mA = $avaColor($msg->user_id, $msg->user->name ?? '?');
                    $isOrg = $msg->user_id === $game->initiator_user_id;
                  @endphp
                  <div class="fs-msg">
                    <div class="fs-msg-ava" style="background:linear-gradient(135deg,{{ $mA['from'] }},{{ $mA['to'] }})">{{ $mA['initial'] }}</div>
                    <div class="fs-msg-body">
                      <div class="fs-msg-head">
                        <span class="n">{{ $msg->user->name ?? 'Anónimo' }}</span>
                        @if($isOrg)<span class="role">organizador</span>@endif
                        <span class="t">{{ $msg->created_at->locale('es')->diffForHumans(['short' => true]) }}</span>
                      </div>
                      <div class="fs-msg-text">{{ $msg->body }}</div>
                    </div>
                  </div>
                @endforeach
                @if($totalMessages > $recentMessages->count() && $isParticipant && !$wasNoShow)
                  <a href="{{ route('falta-uno.chat', $game) }}" style="text-align:center; padding:8px; font-size:12px; color:var(--fs-tx-3); text-decoration:none; border-top:1px dashed var(--fs-bd); margin-top:4px;" onmouseover="this.style.color='var(--fs-accent)'" onmouseout="this.style.color='var(--fs-tx-3)'">
                    Ver los {{ $totalMessages }} comentarios →
                  </a>
                @endif
              @endif
            </div>
            @auth
              @if($isParticipant && !$wasNoShow && in_array($game->status, ['open','full']))
                @php $myA = $avaColor(auth()->id(), auth()->user()->name); @endphp
                <a href="{{ route('falta-uno.chat', $game) }}" class="fs-chat-input">
                  <span class="fs-msg-ava" style="background:linear-gradient(135deg,{{ $myA['from'] }},{{ $myA['to'] }}); width:32px; height:32px;">{{ $myA['initial'] }}</span>
                  <span class="ph">Escribir un comentario…</span>
                  <span class="send">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4 20-7z"/></svg>
                    Enviar
                  </span>
                </a>
              @elseif(!$isParticipant && $game->status === 'open')
                <div class="fs-chat-input fs-chat-input-locked">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  <span>Sumate al partido para escribir un comentario.</span>
                </div>
              @endif
            @endauth
          </div>
        </section>
      @endif

    </div>

    {{-- SIDEBAR --}}
    <aside class="fs-side">
      <div class="fs-cta-card">
        <div class="fs-cta-head">
          <span class="k">@if($isJoined)Estás anotado @elseif($isInitiator)Tu partido @else Sumarte al partido @endif</span>
          @if($perPerson > 0)
            <span class="price"><b>${{ number_format($perPerson, 0, ',', '.') }}</b><small> /pers</small></span>
          @endif
        </div>

        <div class="fs-cta-when">
          <div class="date">
            <div class="day">{{ ucfirst(substr($game->start_at->locale('es')->isoFormat('ddd'), 0, 3)) }}</div>
            <div class="d">{{ $game->start_at->format('d') }}</div>
            <div class="m">{{ strtoupper(substr($game->start_at->locale('es')->isoFormat('MMM'), 0, 3)) }}</div>
          </div>
          <div class="when-info">
            <div class="h">{{ $game->start_at->format('H:i') }} — {{ $endAt->format('H:i') }}</div>
            <div class="s">@if($game->start_at->isFuture())en <b>{{ $game->start_at->locale('es')->diffForHumans(['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</b>@else <b>Pasado</b>@endif</div>
          </div>
        </div>

        <div class="fs-cta-progress">
          <div class="row"><span>Cupo</span><span><b>{{ $joined }}</b> de {{ $needed }}</span></div>
          <div class="bar"><span style="width:{{ $fillPct }}%"></span></div>
        </div>

        <div class="fs-cta-row">
          <span class="l"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 22h18M5 22V8h14v14"/></svg>Cancha</span>
          <span class="v">{{ $game->field->name }}@if($surfaceLabel) · {{ $surfaceLabel }}@endif</span>
        </div>
        <div class="fs-cta-row">
          <span class="l"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="7" r="3"/><circle cx="15" cy="7" r="3"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>Modalidad</span>
          <span class="v">{{ $genderLabel }}</span>
        </div>
        @if($totalCancha > 0)
          <div class="fs-cta-row">
            <span class="l"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v12M8 10h8M8 14h8"/></svg>Total cancha</span>
            <span class="v">${{ number_format($totalCancha, 0, ',', '.') }} ÷ {{ $needed }}</span>
          </div>
        @endif

        {{-- BOTÓN PRINCIPAL --}}
        @auth
          @if($wasKicked || $wasNoShow)
            <button class="fs-cta-btn" disabled style="margin-top:16px;">No podés sumarte</button>
          @elseif(!empty($venueBlock) && !$isInitiator && !$isJoined)
            <button class="fs-cta-btn" disabled style="margin-top:16px;">Bloqueado por el complejo</button>
          @elseif($isInitiator && in_array($game->status, ['open','full']) && !$game->isFinished())
            <form method="POST" action="{{ route('falta-uno.cancel', $game) }}" onsubmit="return confirm('¿Cancelar el partido? Esta acción no se puede deshacer.');">
              @csrf
              <button type="submit" class="fs-cta-btn danger">Cancelar partido</button>
            </form>
          @elseif($isJoined && in_array($game->status, ['open','full']) && !$game->isFinished())
            <form method="POST" action="{{ route('falta-uno.leave', $game) }}" onsubmit="return confirm('¿Salir del partido?');">
              @csrf
              <button type="submit" class="fs-cta-btn ghost">Salirme del partido</button>
            </form>
          @elseif(!$isParticipant && $game->status === 'open' && !$game->isFinished() && (!isset($joinCheck) || $joinCheck['allowed']))
            <form method="POST" action="{{ route('falta-uno.join', $game) }}">
              @csrf
              <button type="submit" class="fs-cta-btn">Sumarme — ${{ number_format($perPerson, 0, ',', '.') }}</button>
            </form>
          @elseif($game->isFinished())
            @if($canRate)
              <a href="{{ route('falta-uno.rate', $game) }}" class="fs-cta-btn">Calificar partido</a>
            @else
              <button class="fs-cta-btn ghost" disabled>Partido finalizado</button>
            @endif
          @elseif($game->status === 'cancelled')
            <button class="fs-cta-btn ghost" disabled>Partido cancelado</button>
          @elseif($game->status === 'full')
            <button class="fs-cta-btn ghost" disabled>Cupo completo</button>
          @endif

          @if(isset($joinCheck) && !$joinCheck['allowed'] && !$isParticipant)
            <div style="margin-top:10px; padding:10px 12px; background:rgba(248,113,113,.08); border:1px solid rgba(248,113,113,.2); border-radius:10px; font-size:11px; color:#fca5a5;">
              {{ $joinCheck['reason'] }}
            </div>
          @endif
        @endauth
        @guest
          <a href="{{ route('login') }}" class="fs-cta-btn">Iniciá sesión para sumarte</a>
        @endguest

        {{-- Acciones secundarias --}}
        <div class="fs-cta-secondary">
          @php
            $waMsg = "Partido de *{$sportLabel}* en *{$game->field->venue->name}*\n📅 " . $game->start_at->format('d/m/Y') . " a las " . $game->start_at->format('H:i') . " hs\n👥 " . ($remaining > 0 ? "Faltan {$remaining} jugador" . ($remaining > 1 ? 'es' : '') : '¡Completo!') . "\n\nSumate! " . route('falta-uno.show', $game);
          @endphp
          <a href="https://wa.me/?text={{ urlencode($waMsg) }}" target="_blank" rel="noopener">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
            Compartir
          </a>
          @if($game->start_at->isFuture())
            <a href="{{ route('calendar.falta-uno', $game) }}">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
              Calendario
            </a>
          @endif
        </div>

        @if(!$isParticipant && $game->status === 'open' && !$game->isFinished())
          <div class="fs-cta-foot">
            Devolución <b>100%</b> automática si el partido no se completa.
          </div>
        @endif
      </div>

      {{-- ORGANIZER PANEL --}}
      @if($isInitiator && in_array($game->status, ['open','full']) && !$game->isFinished())
        <div class="fs-org-panel">
          <span class="k">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 2 7l10 5 10-5-10-5z"/></svg>
            PANEL DE ORGANIZADOR
          </span>
          <p>Solo vos podés ver esto. Como capitán, manejás el partido.</p>
          <div class="fs-op-grid">
            <a href="{{ route('falta-uno.chat', $game) }}">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              Chat
            </a>
            <a href="https://wa.me/?text={{ urlencode($waMsg) }}" target="_blank" rel="noopener">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M20 8v6M23 11h-6"/><circle cx="8.5" cy="7" r="4"/></svg>
              Invitar
            </a>
          </div>
        </div>
      @endif

      {{-- Stats button (post game) --}}
      @if($game->isFinished() && $isParticipant)
        <a href="{{ route('falta-uno.stats', $game) }}" class="fs-cta-btn ghost" style="margin:0;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18M9 17V9M15 17V5M21 17v-7"/></svg>
          Mis estadísticas
        </a>
      @endif

    </aside>
  </div>
</div>

@if(session('success'))
  <script>setTimeout(() => alert(@json(session('success'))), 100);</script>
@endif

@auth
<script>
(function () {
  const GAME_ID = {{ $game->id }};
  const AUTH_USER_ID = {{ auth()->id() }};
  const ORG_USER_ID  = {{ (int) $game->initiator_user_id }};
  const PALETTE = [
    ['#4ade80','#22a55a'],['#7abef5','#2a6aaa'],['#fda4af','#be123c'],
    ['#a78bfa','#5a3da8'],['#f5c17a','#a88844'],['#94e8c4','#33996c'],
    ['#fbbf24','#a16207'],['#f472b6','#9d174d'],
  ];
  function colorFor(uid) {
    let h = 0; const s = String(uid);
    for (let i = 0; i < s.length; i++) h = ((h << 5) - h + s.charCodeAt(i)) | 0;
    return PALETTE[Math.abs(h) % PALETTE.length];
  }
  function ago() { return 'recién'; }
  function escapeHtml(s) { return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c])); }

  function renderMsg(e) {
    const [c1, c2] = colorFor(e.user.id);
    const initial = (e.user.name || '?').charAt(0).toUpperCase();
    const isOrg = e.user.id === ORG_USER_ID;
    const div = document.createElement('div');
    div.className = 'fs-msg';
    div.innerHTML = `
      <div class="fs-msg-ava" style="background:linear-gradient(135deg,${c1},${c2})">${escapeHtml(initial)}</div>
      <div class="fs-msg-body">
        <div class="fs-msg-head">
          <span class="n">${escapeHtml(e.user.name || 'Anónimo')}</span>
          ${isOrg ? '<span class="role">organizador</span>' : ''}
          <span class="t">${ago()}</span>
        </div>
        <div class="fs-msg-text">${escapeHtml(e.body || '')}</div>
      </div>`;
    return div;
  }

  function appendMessage(e) {
    const wrap = document.querySelector('.fs-chat-msgs');
    if (!wrap) return;
    // Si está el empty state, limpiarlo
    const empty = wrap.querySelector('.fs-chat-empty');
    if (empty) empty.remove();
    // Quitar el link "Ver los X comentarios" si existe (lo re-agregamos al final si corresponde)
    const moreLink = wrap.querySelector('a[href*="/chat"]');
    if (moreLink) moreLink.remove();
    // Append message
    wrap.appendChild(renderMsg(e));
    // Mantener solo los últimos 3 mensajes
    const msgs = wrap.querySelectorAll('.fs-msg');
    while (msgs.length > 3 && msgs[0]) {
      msgs[0].remove();
      // refresh nodelist
      break;
    }
    // Actualizar contador de la sección
    const ct = document.querySelector('.fs-sec-head .ct');
    if (ct) {
      const n = parseInt(ct.textContent, 10) || 0;
      ct.textContent = n + 1;
    } else {
      // crear el contador si no existía
      const head = document.querySelector('.fs-sec-head h2');
      if (head) {
        const span = document.createElement('span');
        span.className = 'ct';
        span.textContent = '1';
        head.parentNode.insertBefore(span, head.nextSibling);
      }
    }
  }

  if (typeof Echo === 'undefined') return;
  try {
    const echoPreview = new Echo({
      broadcaster:       'reverb',
      key:               '{{ config('broadcasting.connections.reverb.key') }}',
      wsHost:            '{{ config('broadcasting.connections.reverb.client_host') }}',
      wsPort:            {{ config('broadcasting.connections.reverb.client_port') }},
      wssPort:           {{ config('broadcasting.connections.reverb.client_port') }},
      forceTLS:          true,
      enabledTransports: ['ws', 'wss'],
      authEndpoint:      '/broadcasting/auth',
    });
    echoPreview.private('falta-uno.' + GAME_ID)
      .listen('.chat.message', (e) => {
        appendMessage(e);
      });
  } catch (err) {
    console.warn('Echo (preview) no disponible:', err);
  }
})();
</script>
@endauth

@endsection
