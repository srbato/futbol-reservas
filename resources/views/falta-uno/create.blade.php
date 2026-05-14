@extends('layouts.app')

@section('title', 'Crear partido Falta Uno · ' . $field->name)

@push('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@200;300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
@endpush

@push('styles')
<style>
  :root {
    --cf-bg: #050505;
    --cf-bg-1: #0a0a0a;
    --cf-bg-2: #111;
    --cf-bg-3: #161616;
    --cf-bd: rgba(255,255,255,.07);
    --cf-bd-2: rgba(255,255,255,.14);
    --cf-tx: #f2f2f2;
    --cf-tx-2: #c8c8c8;
    --cf-tx-3: #8a8a8a;
    --cf-tx-4: #555;
    --cf-accent: #4ade80;
    --cf-accent-ink: #052010;
    --cf-accent-hover: #6ee7a0;
    --cf-accent-soft: rgba(74,222,128,.08);
    --cf-warn: #f5c17a;
    --cf-danger: #f87171;
    --cf-blue: #7abef5;
    --cf-purple: #a78bfa;
    --cf-mono: 'JetBrains Mono', ui-monospace, monospace;
  }

  .cf-page { font-family: 'Sora', system-ui, sans-serif; max-width: 1220px; width: 100%; margin: 0 auto; padding: 8px 0 60px; color: var(--cf-tx); overflow-x: clip; }
  .cf-page * { box-sizing: border-box; min-width: 0; }

  /* breadcrumb */
  .cf-crumbs { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--cf-tx-3); margin-bottom: 22px; flex-wrap: wrap; }
  .cf-crumbs a { color: var(--cf-tx-3); text-decoration: none; transition: color .15s; }
  .cf-crumbs a:hover { color: var(--cf-tx); }
  .cf-crumbs svg { opacity: .5; }
  .cf-crumbs .now { color: var(--cf-tx-2); }

  /* HEADER : court summary */
  .cf-court {
    background: linear-gradient(160deg, rgba(74,222,128,.06), rgba(74,222,128,.01));
    border: 1px solid rgba(74,222,128,.18);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
  }
  .cf-court::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 100% at 90% 50%, rgba(74,222,128,.08), transparent 70%);
    pointer-events: none;
  }
  .cf-court-eye { font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--cf-accent); margin-bottom: 8px; display: inline-flex; align-items: center; gap: 8px; position: relative; }
  .cf-court-eye .pill { background: rgba(74,222,128,.12); padding: 3px 10px; border-radius: 999px; }
  .cf-court h1 { font-size: clamp(24px, 4vw, 36px); font-weight: 300; letter-spacing: -0.035em; margin: 0 0 8px; line-height: 1.05; position: relative; color: var(--cf-tx); font-family: 'Sora', sans-serif; word-break: break-word; }
  .cf-court h1 b { font-weight: 600; }
  .cf-court .meta { font-size: 13px; color: var(--cf-tx-2); position: relative; display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
  .cf-court .meta b { color: var(--cf-tx); font-weight: 500; }
  .cf-court .meta .sep { width: 2px; height: 2px; background: var(--cf-tx-4); border-radius: 50%; }
  .cf-court .meta svg { vertical-align: -2px; margin-right: 4px; }

  /* STEPS HEADER */
  .cf-steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
    margin-bottom: 28px;
    background: var(--cf-bg-1);
    border: 1px solid var(--cf-bd);
    border-radius: 14px;
    overflow: hidden;
  }
  .cf-step {
    padding: 16px 20px;
    border-right: 1px solid var(--cf-bd);
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 12px;
    align-items: center;
  }
  .cf-step:last-child { border-right: 0; }
  .cf-step-num {
    width: 28px; height: 28px;
    border-radius: 8px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--cf-bd);
    color: var(--cf-tx-3);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--cf-mono); font-size: 11px; font-weight: 500;
  }
  .cf-step.active .cf-step-num { background: var(--cf-accent); color: var(--cf-accent-ink); border-color: var(--cf-accent); font-weight: 700; }
  .cf-step-info .k { font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--cf-tx-3); margin-bottom: 2px; }
  .cf-step.active .cf-step-info .k { color: var(--cf-accent); }
  .cf-step-info .v { font-size: 13px; color: var(--cf-tx-2); font-weight: 500; }
  .cf-step.active .cf-step-info .v { color: var(--cf-tx); }

  /* LAYOUT */
  .cf-grid { display: grid; grid-template-columns: minmax(0, 1fr) 360px; gap: 28px; align-items: start; }
  .cf-grid > * { min-width: 0; }
  @media (max-width: 1100px) { .cf-grid { grid-template-columns: 1fr; } }

  /* SECTION */
  .cf-section {
    background: var(--cf-bg-1);
    border: 1px solid var(--cf-bd);
    border-radius: 16px;
    margin-bottom: 16px;
    overflow: hidden;
  }
  .cf-section-head {
    padding: 18px 24px;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 14px;
    align-items: center;
    border-bottom: 1px solid var(--cf-bd);
  }
  .cf-section-num {
    width: 28px; height: 28px;
    border-radius: 8px;
    background: var(--cf-accent-soft);
    border: 1px solid rgba(74,222,128,.2);
    color: var(--cf-accent);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--cf-mono); font-size: 11px; font-weight: 600;
  }
  .cf-section-head h3 { font-size: 14px; font-weight: 500; color: var(--cf-tx); margin: 0; letter-spacing: -0.005em; font-family: 'Sora', sans-serif; }
  .cf-section-head .sub { font-size: 12px; color: var(--cf-tx-3); margin-top: 2px; }
  .cf-section-status { font-size: 10px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--cf-accent); display: inline-flex; align-items: center; gap: 6px; }
  .cf-section-status.opt { color: var(--cf-tx-3); }
  .cf-section-status .ok { width: 14px; height: 14px; border-radius: 50%; background: var(--cf-accent); color: var(--cf-accent-ink); display: inline-flex; align-items: center; justify-content: center; }
  .cf-section-status.todo .ok { background: rgba(255,255,255,.06); color: var(--cf-tx-4); }
  .cf-section-status.todo { color: var(--cf-tx-3); }

  .cf-section-body { padding: 22px 24px; display: flex; flex-direction: column; gap: 22px; }

  /* FIELD */
  .cf-field-label { font-size: 11px; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: var(--cf-tx-3); margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
  .cf-field-help { font-size: 11px; color: var(--cf-tx-3); margin-top: 8px; line-height: 1.5; }

  .cf-input, .cf-select {
    width: 100%;
    padding: 13px 16px;
    background: rgba(255,255,255,.025);
    border: 1px solid var(--cf-bd);
    border-radius: 11px;
    font-size: 14px; color: var(--cf-tx);
    transition: border-color .15s, background .15s;
    outline: none;
    font-family: inherit;
  }
  .cf-input:focus, .cf-select:focus, .cf-input:hover, .cf-select:hover { border-color: var(--cf-bd-2); background: rgba(255,255,255,.035); }
  .cf-select { appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238a8a8a' stroke-width='2'><path d='m6 9 6 6 6-6'/></svg>"); background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px; cursor: pointer; }
  .cf-select option { background: var(--cf-bg-1); color: var(--cf-tx); }

  /* SLOT picker */
  .cf-slot-wrap { position: relative; }
  .cf-slot-tabs {
    display: flex; gap: 6px; overflow-x: auto; padding: 0 36px 4px;
    scrollbar-width: none; -ms-overflow-style: none;
    scroll-behavior: smooth;
    -webkit-mask-image: linear-gradient(to right, transparent 0, #000 24px, #000 calc(100% - 24px), transparent 100%);
            mask-image: linear-gradient(to right, transparent 0, #000 24px, #000 calc(100% - 24px), transparent 100%);
  }
  .cf-slot-tabs::-webkit-scrollbar { display: none; height: 0; width: 0; }
  .cf-slot-arrow {
    position: absolute; top: 50%; transform: translateY(-50%);
    width: 30px; height: 30px; border-radius: 50%;
    background: rgba(20,20,20,.85); backdrop-filter: blur(8px);
    border: 1px solid var(--cf-bd-2); color: var(--cf-tx-2);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; z-index: 2;
    transition: background .15s, color .15s, opacity .15s;
  }
  .cf-slot-arrow:hover { background: rgba(40,40,40,.95); color: var(--cf-tx); }
  .cf-slot-arrow.left { left: -2px; }
  .cf-slot-arrow.right { right: -2px; }
  .cf-slot-arrow.disabled { opacity: 0; pointer-events: none; }
  .cf-slot-day {
    flex: none;
    padding: 10px 14px;
    background: rgba(255,255,255,.025);
    border: 1px solid var(--cf-bd);
    border-radius: 11px;
    text-align: center;
    min-width: 72px;
    cursor: pointer;
    transition: background .15s, border-color .15s;
    font-family: inherit;
    color: inherit;
  }
  .cf-slot-day:hover { background: rgba(255,255,255,.045); }
  .cf-slot-day.active { background: var(--cf-accent); border-color: var(--cf-accent); }
  .cf-slot-day .lbl { font-size: 9px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--cf-tx-3); display: block; }
  .cf-slot-day.active .lbl { color: var(--cf-accent-ink); }
  .cf-slot-day .num { font-size: 18px; font-weight: 500; color: var(--cf-tx); margin-top: 2px; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; display: block; }
  .cf-slot-day.active .num { color: var(--cf-accent-ink); font-weight: 700; }
  .cf-slot-day .mo { font-size: 9px; color: var(--cf-tx-3); letter-spacing: .14em; text-transform: uppercase; margin-top: 2px; font-weight: 600; display: block; }
  .cf-slot-day.active .mo { color: var(--cf-accent-ink); opacity: .7; }

  .cf-slot-times {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 6px;
    margin-top: 4px;
  }
  .cf-slot-time {
    padding: 11px 8px;
    background: rgba(255,255,255,.025);
    border: 1px solid var(--cf-bd);
    border-radius: 9px;
    font-family: var(--cf-mono);
    font-size: 12px;
    font-weight: 500;
    color: var(--cf-tx-2);
    text-align: center;
    cursor: pointer;
    transition: background .15s, border-color .15s, color .15s;
    font-variant-numeric: tabular-nums;
  }
  .cf-slot-time:hover { background: rgba(255,255,255,.05); color: var(--cf-tx); }
  .cf-slot-time.active { background: rgba(74,222,128,.1); border-color: var(--cf-accent); color: var(--cf-accent); }
  .cf-slot-time.taken { opacity: .35; cursor: not-allowed; text-decoration: line-through; text-decoration-color: var(--cf-tx-4); }
  .cf-slot-time .price { display: block; font-family: 'Sora', sans-serif; font-size: 10px; font-weight: 400; color: var(--cf-tx-3); margin-top: 2px; }
  .cf-slot-time.active .price { color: var(--cf-accent); }
  .cf-slot-empty { font-size: 12px; color: var(--cf-tx-3); padding: 18px; text-align: center; background: rgba(255,255,255,.02); border: 1px dashed var(--cf-bd); border-radius: 10px; }

  /* STEPPER */
  .cf-stepper {
    display: grid;
    grid-template-columns: 44px 1fr 44px;
    align-items: center;
    background: rgba(255,255,255,.025);
    border: 1px solid var(--cf-bd);
    border-radius: 11px;
    overflow: hidden;
    max-width: 200px;
  }
  .cf-stepper button {
    height: 46px;
    color: var(--cf-tx-2);
    transition: background .15s, color .15s;
    font-size: 16px;
    background: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
  }
  .cf-stepper button:hover { background: rgba(255,255,255,.06); color: var(--cf-tx); }
  .cf-stepper input {
    width: 100%; height: 46px;
    background: transparent; border: 0; outline: 0;
    text-align: center; color: var(--cf-tx);
    font-size: 18px; font-weight: 500;
    border-left: 1px solid var(--cf-bd);
    border-right: 1px solid var(--cf-bd);
    font-variant-numeric: tabular-nums;
    font-family: inherit;
    -moz-appearance: textfield;
  }
  .cf-stepper input::-webkit-outer-spin-button,
  .cf-stepper input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

  /* TWO COL */
  .cf-two { display: grid; grid-template-columns: minmax(0,1fr) minmax(0,1fr); gap: 14px; }
  @media (max-width: 540px) { .cf-two { grid-template-columns: 1fr; } }

  /* SEGMENT (radio cards) */
  .cf-segments { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; }
  @media (max-width: 540px) { .cf-segments { grid-template-columns: 1fr; } }
  .cf-seg {
    padding: 16px;
    background: rgba(255,255,255,.025);
    border: 1px solid var(--cf-bd);
    border-radius: 12px;
    cursor: pointer;
    transition: background .15s, border-color .15s;
    display: flex; flex-direction: column; gap: 4px;
    position: relative;
    text-align: left;
    color: inherit;
    font-family: inherit;
  }
  .cf-seg:hover { background: rgba(255,255,255,.04); }
  .cf-seg.active { background: rgba(74,222,128,.06); border-color: rgba(74,222,128,.3); }
  .cf-seg-ico {
    width: 32px; height: 32px; border-radius: 9px;
    background: rgba(255,255,255,.04); color: var(--cf-tx-3);
    display: inline-flex; align-items: center; justify-content: center;
    margin-bottom: 6px;
  }
  .cf-seg.active .cf-seg-ico { background: rgba(74,222,128,.1); color: var(--cf-accent); }
  .cf-seg .t { font-size: 13px; font-weight: 500; color: var(--cf-tx); letter-spacing: -0.005em; }
  .cf-seg .d { font-size: 11px; color: var(--cf-tx-3); line-height: 1.4; }
  .cf-seg.active::after {
    content: '';
    position: absolute;
    top: 12px; right: 12px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: var(--cf-accent);
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23052010' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'><polyline points='20 6 9 17 4 12'/></svg>");
    background-position: center; background-repeat: no-repeat;
  }

  /* PRICE BOX */
  .cf-price-row { display: grid; grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr); gap: 14px; }
  @media (max-width: 540px) { .cf-price-row { grid-template-columns: 1fr; } }
  .cf-price-input-wrap { position: relative; }
  .cf-price-input-wrap .currency {
    position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
    font-size: 13px; color: var(--cf-tx-3); font-weight: 500;
    pointer-events: none;
  }
  .cf-price-input-wrap .cf-input { padding-left: 44px; font-variant-numeric: tabular-nums; }
  .cf-pp-display {
    display: flex; flex-direction: column; align-items: flex-start;
    gap: 4px;
    padding: 11px 14px;
    background: rgba(74,222,128,.06);
    border: 1px solid rgba(74,222,128,.2);
    border-radius: 11px;
    min-width: 0; max-width: 100%;
    overflow: hidden;
  }
  .cf-pp-display .k { font-size: 9px; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--cf-accent); opacity: .85; }
  .cf-pp-display .v { font-family: var(--cf-mono); font-size: 18px; font-weight: 600; color: var(--cf-accent); font-variant-numeric: tabular-nums; white-space: nowrap; max-width: 100%; overflow: hidden; text-overflow: ellipsis; letter-spacing: -0.02em; }

  /* TOGGLES (opciones extra) */
  .cf-toggles { display: flex; flex-direction: column; gap: 8px; }
  .cf-toggle {
    display: grid;
    grid-template-columns: 28px minmax(0, 1fr) auto;
    gap: 12px;
    align-items: center;
    padding: 12px 14px;
    background: rgba(255,255,255,.02);
    border: 1px solid var(--cf-bd);
    border-radius: 10px;
    cursor: pointer;
    transition: background .15s;
  }
  .cf-toggle:hover { background: rgba(255,255,255,.035); }
  .cf-toggle.on { background: rgba(74,222,128,.05); border-color: rgba(74,222,128,.18); }
  .cf-toggle-ico {
    width: 28px; height: 28px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.05); color: var(--cf-tx-3);
  }
  .cf-toggle.on .cf-toggle-ico { background: rgba(74,222,128,.1); color: var(--cf-accent); }
  .cf-toggle-info .t { font-size: 13px; font-weight: 500; color: var(--cf-tx); }
  .cf-toggle-info .d { font-size: 11px; color: var(--cf-tx-3); margin-top: 2px; line-height: 1.4; }
  .cf-switch {
    width: 36px; height: 20px;
    border-radius: 999px;
    background: rgba(255,255,255,.08);
    position: relative; flex: none;
    transition: background .2s;
  }
  .cf-switch::after {
    content: '';
    position: absolute; top: 2px; left: 2px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: var(--cf-tx-3);
    transition: transform .2s, background .2s;
  }
  .cf-toggle.on .cf-switch { background: rgba(74,222,128,.3); }
  .cf-toggle.on .cf-switch::after { transform: translateX(16px); background: var(--cf-accent); }
  .cf-toggle input[type="checkbox"] { display: none; }

  /* TEXTAREA */
  textarea.cf-input { resize: vertical; min-height: 86px; font-family: inherit; line-height: 1.5; }

  /* RULES */
  .cf-rules { display: flex; flex-direction: column; gap: 14px; }
  .cf-rule { display: grid; grid-template-columns: 22px 1fr; gap: 14px; align-items: start; }
  .cf-rule-ico {
    width: 22px; height: 22px;
    border-radius: 7px;
    background: var(--cf-accent-soft);
    color: var(--cf-accent);
    display: flex; align-items: center; justify-content: center;
    flex: none; margin-top: 2px;
  }
  .cf-rule .t { font-size: 13px; font-weight: 500; color: var(--cf-tx); margin-bottom: 3px; letter-spacing: -0.005em; }
  .cf-rule .d { font-size: 12px; color: var(--cf-tx-3); line-height: 1.5; }

  /* SIDEBAR */
  .cf-side { display: flex; flex-direction: column; gap: 16px; position: sticky; top: 90px; align-self: start; }
  @media (max-width: 1100px) { .cf-side { position: static; } }

  /* PREVIEW CARD */
  .cf-preview-card {
    background: var(--cf-bg-1);
    border: 1px solid var(--cf-bd);
    border-radius: 16px;
    overflow: hidden;
  }
  .cf-preview-head {
    padding: 14px 18px;
    border-bottom: 1px solid var(--cf-bd);
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(255,255,255,.02);
  }
  .cf-preview-head .k { font-size: 10px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--cf-tx-3); }
  .cf-preview-head .live { font-size: 10px; color: var(--cf-accent); display: inline-flex; align-items: center; gap: 6px; font-weight: 500; }
  .cf-preview-head .live::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--cf-accent); animation: cfPulse 2s infinite; }
  @keyframes cfPulse { 0%,100% { opacity: 1; } 50% { opacity: .4; } }

  .cf-preview-body { padding: 18px; }
  .cf-preview-mock {
    background: var(--cf-bg-2);
    border: 1px solid var(--cf-bd);
    border-radius: 12px;
    padding: 16px;
  }
  .cf-pm-row1 { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
  .cf-pm-ico { width: 24px; height: 24px; border-radius: 7px; background: var(--cf-accent-soft); color: var(--cf-accent); display: inline-flex; align-items: center; justify-content: center; flex: none; }
  .cf-pm-title { font-size: 14px; font-weight: 500; color: var(--cf-tx); }
  .cf-pm-tag { font-size: 9px; padding: 2px 7px; border-radius: 999px; background: rgba(255,255,255,.04); border: 1px solid var(--cf-bd); color: var(--cf-tx-2); font-weight: 500; }
  .cf-pm-tag.cat { background: rgba(167,139,250,.06); color: var(--cf-purple); border-color: rgba(167,139,250,.16); }
  .cf-pm-tag.lvl { background: rgba(245,193,122,.08); color: var(--cf-warn); border-color: rgba(245,193,122,.18); }

  .cf-pm-meta { font-size: 11px; color: var(--cf-tx-3); display: flex; align-items: center; gap: 8px; margin-bottom: 12px; flex-wrap: wrap; }
  .cf-pm-meta .sep { width: 2px; height: 2px; background: var(--cf-tx-4); border-radius: 50%; }
  .cf-pm-meta b { color: var(--cf-tx-2); font-weight: 500; }

  .cf-pm-roster { display: flex; align-items: center; margin-bottom: 12px; flex-wrap: wrap; }
  .cf-pm-ava { width: 22px; height: 22px; border-radius: 50%; border: 2px solid var(--cf-bg-2); display: inline-flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: #000; margin-left: -6px; flex: none; }
  .cf-pm-ava:first-child { margin-left: 0; }
  .cf-pm-ava.empty { background: transparent; border: 2px dashed var(--cf-bd-2); color: var(--cf-tx-3); font-weight: 400; }

  .cf-pm-bar { height: 3px; background: rgba(255,255,255,.04); border-radius: 2px; overflow: hidden; margin-bottom: 12px; }
  .cf-pm-bar > span { display: block; height: 100%; background: var(--cf-accent); border-radius: 2px; transition: width .25s; }

  .cf-pm-foot { display: flex; align-items: center; justify-content: space-between; }
  .cf-pm-price b { font-size: 14px; color: var(--cf-tx); font-weight: 500; font-variant-numeric: tabular-nums; }
  .cf-pm-price span { font-size: 10px; color: var(--cf-tx-3); margin-left: 4px; }
  .cf-pm-cta { font-size: 11px; font-weight: 600; color: var(--cf-accent-ink); background: var(--cf-accent); padding: 6px 12px; border-radius: 8px; }

  /* COST BOX */
  .cf-cost {
    background: var(--cf-bg-1);
    border: 1px solid var(--cf-bd);
    border-radius: 16px;
    padding: 22px;
  }
  .cf-cost h4 { font-size: 13px; font-weight: 600; color: var(--cf-tx); margin: 0 0 14px; font-family: 'Sora', sans-serif; }
  .cf-cost-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--cf-bd); font-size: 13px; }
  .cf-cost-row:last-of-type { border-bottom: 0; }
  .cf-cost-row .k { color: var(--cf-tx-3); }
  .cf-cost-row .v { color: var(--cf-tx); font-variant-numeric: tabular-nums; font-weight: 500; }
  .cf-cost-total { display: flex; align-items: baseline; justify-content: space-between; margin-top: 14px; padding-top: 16px; border-top: 1px solid var(--cf-bd-2); }
  .cf-cost-total .k { font-size: 11px; font-weight: 600; letter-spacing: .14em; text-transform: uppercase; color: var(--cf-tx-3); }
  .cf-cost-total .v { font-size: 26px; color: var(--cf-tx); letter-spacing: -0.02em; font-weight: 300; font-variant-numeric: tabular-nums; }
  .cf-cost-total .v small { font-size: 11px; color: var(--cf-tx-3); margin-left: 4px; font-weight: 400; }

  .cf-submit {
    margin-top: 24px;
    width: 100%;
    padding: 16px;
    background: var(--cf-accent);
    color: var(--cf-accent-ink);
    border-radius: 14px;
    font-size: 14px; font-weight: 600;
    display: inline-flex; align-items: center; justify-content: center; gap: 10px;
    transition: background .15s;
    border: none;
    cursor: pointer;
    font-family: inherit;
  }
  .cf-submit:hover { background: var(--cf-accent-hover); }
  .cf-submit:disabled { opacity: .45; cursor: not-allowed; }
  .cf-submit-foot { font-size: 11px; color: var(--cf-tx-3); text-align: center; margin-top: 12px; line-height: 1.5; }
  .cf-submit-foot a { color: var(--cf-tx-2); border-bottom: 1px dashed var(--cf-bd-2); text-decoration: none; }

  /* ERROR */
  .cf-error {
    background: rgba(248,113,113,.08);
    border: 1px solid rgba(248,113,113,.25);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 13px;
    color: #fca5a5;
    margin-bottom: 14px;
  }

  @media (max-width: 760px) {
    .cf-page { padding: 4px 0 40px; }
    .cf-court { padding: 22px; }
    .cf-court h1 { font-size: 28px; }
    .cf-steps { grid-template-columns: 1fr; }
    .cf-step { border-right: 0; border-bottom: 1px solid var(--cf-bd); }
    .cf-step:last-child { border-bottom: 0; }
    .cf-side { position: static; }
  }
</style>
@endpush

@section('content')

@php
  $sportLabel = match($field->sport ?? '') {
    'football'=>'Fútbol', 'football5'=>'Fútbol 5', 'football7'=>'Fútbol 7',
    'padel'=>'Pádel', 'tennis'=>'Tenis', 'basketball'=>'Básquet', 'volleyball'=>'Vóley',
    'hockey'=>'Hockey',
    default => ucfirst(str_replace('_', ' ', $field->sport ?? 'Deporte')),
  };
  $sportCategories = \App\Models\FaltaUnoSportProfile::getCategoriesForSport($field->sport);
  $setting    = $field->faltaUnoSetting;
  $refundMin  = $setting->refund_deadline_minutes ?? 60;
  $fillMin    = $setting->fill_deadline_minutes ?? 120;
  $lateMin    = $setting->late_leave_deadline_minutes ?? 240;
  $fmtMin = function($min) {
    if ($min >= 1440) { $d = floor($min/1440); $h = floor(($min%1440)/60); return $d.' día'.($d>1?'s':'').($h>0?' y '.$h.'h':''); }
    if ($min >= 60)   { $h = floor($min/60); $m = $min%60; return $h.'h'.($m>0?' '.$m.'min':''); }
    return $min.' minutos';
  };
  $defaultPrice = (float) ($field->price->price_per_slot ?? 0);
  $defaultTotal = $field->format ? (int)$field->format * 2 : 10;
  $oldStart = old('start_at');
  // Build 14 days starting today
  $days = [];
  for ($i=0; $i<14; $i++) {
    $d = now()->copy()->startOfDay()->addDays($i);
    $days[] = $d;
  }
@endphp

<div class="cf-page">

  {{-- breadcrumbs --}}
  <nav class="cf-crumbs">
    <a href="{{ route('falta-uno.index') }}">Falta Uno</a>
    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
    <a href="{{ route('venues.show', $field->venue) }}">{{ $field->venue->name }}</a>
    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
    <a href="{{ route('fields.show', $field) }}">{{ $field->name }} — {{ $sportLabel }}</a>
    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
    <span class="now">Crear partido</span>
  </nav>

  {{-- court summary header --}}
  <header class="cf-court">
    <div class="cf-court-eye">
      <span class="pill">FALTA UNO · NUEVO PARTIDO</span>
    </div>
    <h1>{{ $field->name }} · <b>{{ $sportLabel }}</b></h1>
    <div class="meta">
      <span>
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-7 8-13a8 8 0 0 0-16 0c0 6 8 13 8 13z"/></svg>
        {{ $field->venue->name }}@if($field->venue->zone) · <b>{{ $field->venue->zone }}</b>@endif
      </span>
      @if($field->surface)
        <span class="sep"></span>
        <span>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 22h18M5 22V8h14v14M9 6V2h6v4"/></svg>
          <b>{{ ucfirst($field->surface) }}</b>
        </span>
      @endif
      @if($defaultPrice > 0)
        <span class="sep"></span>
        <span>
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
          {{ $field->slot_minutes ?? 60 }} min · <b>${{ number_format($defaultPrice, 0, ',', '.') }}</b> base
        </span>
      @endif
    </div>
  </header>

  {{-- progress steps --}}
  <div class="cf-steps">
    <div class="cf-step active">
      <span class="cf-step-num">01</span>
      <div class="cf-step-info"><div class="k">PASO 1</div><div class="v">Configurá tu partido</div></div>
    </div>
    <div class="cf-step">
      <span class="cf-step-num">02</span>
      <div class="cf-step-info"><div class="k">PASO 2</div><div class="v">Reservá y pagá</div></div>
    </div>
    <div class="cf-step">
      <span class="cf-step-num">03</span>
      <div class="cf-step-info"><div class="k">PASO 3</div><div class="v">Esperá tus jugadores</div></div>
    </div>
  </div>

  {{-- Errores --}}
  @if($errors->any())
    <div class="cf-error">
      @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
  @endif
  @if(session('error'))
    <div class="cf-error">{{ session('error') }}</div>
  @endif

  <form method="POST" action="{{ route('falta-uno.store', $field) }}" id="cfForm">
    @csrf
    <input type="hidden" name="start_at" id="cfStartAt" value="{{ $oldStart }}">

    <div class="cf-grid">
      <div class="cf-form">

        {{-- 01 DAY & TIME --}}
        <section class="cf-section">
          <div class="cf-section-head">
            <span class="cf-section-num">01</span>
            <div>
              <h3>Día y hora del partido</h3>
              <div class="sub">Elegí cuándo querés jugar — solo te mostramos slots disponibles</div>
            </div>
            <span class="cf-section-status todo" id="cfStatus1"><span class="ok"></span>Pendiente</span>
          </div>
          <div class="cf-section-body">
            <div>
              <div class="cf-field-label">Día</div>
              <div class="cf-slot-wrap">
                <button type="button" class="cf-slot-arrow left" id="cfDaysPrev" aria-label="Anterior">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <div class="cf-slot-tabs" id="cfDays">
                  @foreach($days as $i => $d)
                    @php
                      $lbl = $i === 0 ? 'Hoy' : ($i === 1 ? 'Mañ' : ucfirst(substr($d->locale('es')->isoFormat('ddd'), 0, 3)));
                      $mo  = strtoupper(substr($d->locale('es')->isoFormat('MMM'), 0, 3));
                    @endphp
                    <button type="button" class="cf-slot-day" data-date="{{ $d->format('Y-m-d') }}">
                      <span class="lbl">{{ $lbl }}</span>
                      <span class="num">{{ $d->format('d') }}</span>
                      <span class="mo">{{ $mo }}</span>
                    </button>
                  @endforeach
                </div>
                <button type="button" class="cf-slot-arrow right" id="cfDaysNext" aria-label="Siguiente">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </button>
              </div>
            </div>

            <div>
              <div class="cf-field-label">Horario disponible</div>
              <div id="cfTimes" class="cf-slot-empty">Elegí un día para ver los horarios disponibles.</div>
              <div class="cf-field-help">El precio es por hora completa de cancha — se reparte entre los jugadores anotados.</div>
            </div>
          </div>
        </section>

        {{-- 02 PLAYERS --}}
        <section class="cf-section">
          <div class="cf-section-head">
            <span class="cf-section-num">02</span>
            <div>
              <h3>Jugadores</h3>
              <div class="sub">Cuántos van en total y cuántos ya tenés confirmados</div>
            </div>
            <span class="cf-section-status" id="cfStatus2"><span class="ok"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg></span>Listo</span>
          </div>
          <div class="cf-section-body">
            <div class="cf-two">
              <div>
                <div class="cf-field-label">Total de jugadores</div>
                <div class="cf-stepper">
                  <button type="button" data-step="total_players" data-d="-1">−</button>
                  <input type="number" id="total_players" name="total_players" min="2" max="100" required value="{{ old('total_players', $defaultTotal) }}">
                  <button type="button" data-step="total_players" data-d="1">+</button>
                </div>
                <div class="cf-field-help">Ej: 10 para 5 vs 5, 14 para 7 vs 7.</div>
              </div>
              <div>
                <div class="cf-field-label">Ya tenés anotados</div>
                <div class="cf-stepper">
                  <button type="button" data-step="initiator_players" data-d="-1">−</button>
                  <input type="number" id="initiator_players" name="initiator_players" min="1" max="99" required value="{{ old('initiator_players', 1) }}">
                  <button type="button" data-step="initiator_players" data-d="1">+</button>
                </div>
                <div class="cf-field-help">TuCancha completa los <b style="color:var(--cf-accent)" id="cfNeededHint">— lugares</b> que faltan.</div>
              </div>
            </div>
          </div>
        </section>

        {{-- 03 GENDER --}}
        <section class="cf-section">
          <div class="cf-section-head">
            <span class="cf-section-num">03</span>
            <div>
              <h3>Categoría del partido</h3>
              <div class="sub">A quiénes pueden anotarse en los lugares vacíos</div>
            </div>
            <span class="cf-section-status"><span class="ok"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg></span>Listo</span>
          </div>
          <div class="cf-section-body">
            <input type="hidden" name="gender_filter" id="cfGender" value="{{ old('gender_filter', 'mixed') }}">
            <div class="cf-segments" id="cfGenderSegs">
              @php
                $genders = [
                  'mixed' => ['Mixto', 'Cualquiera puede anotarse', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="7" r="3"/><circle cx="15" cy="7" r="3"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2M13 21v-2a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v2"/></svg>'],
                  'male'  => ['Masculino', 'Solo se anotan jugadores', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="7" r="4"/><path d="M5 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/></svg>'],
                  'female'=> ['Femenino', 'Solo se anotan jugadoras', '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="7" r="4"/><path d="M12 11v10M9 18h6"/></svg>'],
                ];
                $curG = old('gender_filter', 'mixed');
              @endphp
              @foreach($genders as $val => [$lbl, $desc, $svg])
                <button type="button" class="cf-seg {{ $curG === $val ? 'active' : '' }}" data-gender="{{ $val }}">
                  <div class="cf-seg-ico">{!! $svg !!}</div>
                  <div class="t">{{ $lbl }}</div>
                  <div class="d">{{ $desc }}</div>
                </button>
              @endforeach
            </div>
          </div>
        </section>

        {{-- 04 AGE --}}
        <section class="cf-section">
          <div class="cf-section-head">
            <span class="cf-section-num">04</span>
            <div>
              <h3>Rango de edad</h3>
              <div class="sub">Para que los jugadores anotados sean parecidos</div>
            </div>
            <span class="cf-section-status opt">Opcional</span>
          </div>
          <div class="cf-section-body">
            <div class="cf-two">
              <div>
                <div class="cf-field-label">Edad mínima</div>
                <input type="number" class="cf-input" id="age_min" name="age_min" min="5" max="99" placeholder="Cualquiera" value="{{ old('age_min') }}">
              </div>
              <div>
                <div class="cf-field-label">Edad máxima</div>
                <input type="number" class="cf-input" id="age_max" name="age_max" min="5" max="99" placeholder="Cualquiera" value="{{ old('age_max') }}">
              </div>
            </div>
            <div class="cf-field-help">Dejá ambos vacíos para aceptar jugadores de todas las edades.</div>
          </div>
        </section>

        {{-- 05 LEVEL --}}
        @if(!empty($sportCategories))
        <section class="cf-section">
          <div class="cf-section-head">
            <span class="cf-section-num">05</span>
            <div>
              <h3>Nivel del partido</h3>
              <div class="sub">Que se anoten jugadores parecidos en habilidad</div>
            </div>
            <span class="cf-section-status opt">Opcional</span>
          </div>
          <div class="cf-section-body">
            <div class="cf-two">
              <div>
                <div class="cf-field-label">Desde</div>
                <select class="cf-select" id="category_min" name="category_min" onchange="cfSyncCat('min')">
                  <option value="">Cualquiera</option>
                  @foreach($sportCategories as $cat)
                    <option value="{{ $cat }}" {{ old('category_min') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <div class="cf-field-label">Hasta</div>
                <select class="cf-select" id="category_max" name="category_max" onchange="cfSyncCat('max')">
                  <option value="">Cualquiera</option>
                  @foreach($sportCategories as $cat)
                    <option value="{{ $cat }}" {{ old('category_max') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div style="display:grid;grid-template-columns:repeat({{ count($sportCategories) }},1fr);gap:8px;margin-top:8px" id="cfCatViz">
              @foreach($sportCategories as $cat)
                <div data-cat="{{ $cat }}" style="padding:10px 12px;background:rgba(255,255,255,.025);border:1px solid var(--cf-bd);border-radius:9px;text-align:center;font-size:11px;color:var(--cf-tx-3)">{{ ucfirst($cat) }}</div>
              @endforeach
            </div>
          </div>
        </section>
        @endif

        {{-- 06 PRICE --}}
        <section class="cf-section">
          <div class="cf-section-head">
            <span class="cf-section-num">06</span>
            <div>
              <h3>Precio</h3>
              <div class="sub">Cuánto cuesta la cancha y cuánto paga cada jugador</div>
            </div>
            <span class="cf-section-status"><span class="ok"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg></span>Auto</span>
          </div>
          <div class="cf-section-body">
            <div class="cf-price-row">
              <div>
                <div class="cf-field-label">Costo total de la cancha</div>
                <div class="cf-price-input-wrap">
                  <span class="currency">AR$</span>
                  <input class="cf-input" id="cfTotalPrice" type="text" value="{{ number_format($defaultPrice, 0, ',', '.') }}" readonly>
                </div>
                <div class="cf-field-help">Configurado por el complejo · cambia según el horario.</div>
              </div>
              <div>
                <div class="cf-field-label">Lo que paga cada jugador</div>
                <div class="cf-pp-display">
                  <span class="k">POR PERSONA</span>
                  <span class="v" id="cfPerPerson">AR$ —</span>
                </div>
                <div class="cf-field-help">Se calcula automáticamente: total ÷ jugadores.</div>
              </div>
            </div>
          </div>
        </section>

        {{-- 07 OPCIONES EXTRA --}}
        <section class="cf-section">
          <div class="cf-section-head">
            <span class="cf-section-num">07</span>
            <div>
              <h3>Opciones extra</h3>
              <div class="sub">Configuración fina para tu partido</div>
            </div>
            <span class="cf-section-status opt">Opcional</span>
          </div>
          <div class="cf-section-body">
            @php $isPriv = old('is_private', false); @endphp
            <div class="cf-toggles">
              <label class="cf-toggle {{ $isPriv ? 'on' : '' }}" id="cfTogglePrivate">
                <span class="cf-toggle-ico">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </span>
                <div class="cf-toggle-info">
                  <div class="t">Partido privado</div>
                  <div class="d">No aparece en el feed público · solo se entra con el link directo</div>
                </div>
                <span class="cf-switch"></span>
                <input type="checkbox" name="is_private" value="1" {{ $isPriv ? 'checked' : '' }}>
              </label>
            </div>

            <div>
              <div class="cf-field-label" style="margin-top:6px">Mensaje para los jugadores <span style="color:var(--cf-tx-4); font-weight:400; text-transform:none; letter-spacing:0">(opcional)</span></div>
              <textarea class="cf-input" name="message" maxlength="500" placeholder="Hola! Buscamos jugadores parecidos. Pelota se trae, agua hay en el complejo. Cualquier cosa por el chat 🙌">{{ old('message') }}</textarea>
              <div class="cf-field-help">Máx. 500 caracteres. Se muestra arriba en la página del partido.</div>
            </div>
          </div>
        </section>

        {{-- RULES --}}
        <section class="cf-section">
          <div class="cf-section-head">
            <span class="cf-section-num"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h0"/></svg></span>
            <div>
              <h3>Antes de crear tu partido</h3>
              <div class="sub">Cómo funciona el flujo · sin sorpresas</div>
            </div>
          </div>
          <div class="cf-section-body">
            <div class="cf-rules">
              <div class="cf-rule">
                <span class="cf-rule-ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>
                <div>
                  <div class="t">Cancelación con reembolso</div>
                  <div class="d">Podés cancelar hasta <b style="color:var(--cf-tx-2)">{{ $fmtMin($refundMin) }} antes</b> y te devolvemos el 100% de lo que pagaste.</div>
                </div>
              </div>
              <div class="cf-rule">
                <span class="cf-rule-ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>
                <div>
                  <div class="t">Garantía de jugadores</div>
                  <div class="d">Si no se completa el partido <b style="color:var(--cf-tx-2)">{{ $fmtMin($fillMin) }}</b> antes del inicio, se cancela automáticamente.</div>
                </div>
              </div>
              <div class="cf-rule">
                <span class="cf-rule-ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>
                <div>
                  <div class="t">El que paga, juega</div>
                  <div class="d">Vos pagás tu parte ahora. Los demás abonan su lugar al anotarse, directo en el complejo.</div>
                </div>
              </div>
              <div class="cf-rule">
                <span class="cf-rule-ico"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>
                <div>
                  <div class="t">Bajas tardías</div>
                  <div class="d">Quien se baje con menos de <b style="color:var(--cf-tx-2)">{{ $fmtMin($lateMin) }}</b> de anticipación recibe una penalización.</div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      {{-- SIDEBAR --}}
      <aside class="cf-side">

        <div class="cf-preview-card">
          <div class="cf-preview-head">
            <span class="k">VISTA PREVIA · ASÍ LO VEN</span>
            <span class="live">Live</span>
          </div>
          <div class="cf-preview-body">
            <div class="cf-preview-mock">
              <div class="cf-pm-row1">
                <span class="cf-pm-ico"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a14 14 0 0 1 0 20M12 2a14 14 0 0 0 0 20"/></svg></span>
                <span class="cf-pm-title">{{ $sportLabel }}</span>
                <span class="cf-pm-tag cat" id="cfPvGender">Mixto</span>
                <span class="cf-pm-tag lvl" id="cfPvLevel" style="display:none"></span>
              </div>
              <div class="cf-pm-meta">
                <span id="cfPvWhen"><b>Elegí día</b></span>
                <span class="sep"></span>
                <span>{{ $field->venue->name }}</span>
              </div>
              <div class="cf-pm-roster" id="cfPvRoster"></div>
              <div class="cf-pm-bar"><span id="cfPvBar" style="width:0%"></span></div>
              <div class="cf-pm-foot">
                <div class="cf-pm-price"><b id="cfPvPrice">$ —</b><span>por persona</span></div>
                <span class="cf-pm-cta">Sumarme</span>
              </div>
            </div>
          </div>
        </div>

        <div class="cf-cost">
          <h4>Resumen</h4>
          <div class="cf-cost-row"><span class="k">Cancha · {{ $field->slot_minutes ?? 60 }} min</span><span class="v" id="cfSumTotal">$ {{ number_format($defaultPrice, 0, ',', '.') }}</span></div>
          <div class="cf-cost-row"><span class="k">Total jugadores</span><span class="v" id="cfSumTotalPlayers">—</span></div>
          <div class="cf-cost-row"><span class="k">Anotados ahora</span><span class="v" id="cfSumInit">—</span></div>
          <div class="cf-cost-row"><span class="k">Faltan</span><span class="v" style="color:var(--cf-accent)" id="cfSumNeeded">—</span></div>
          <div class="cf-cost-row"><span class="k">Por persona</span><span class="v" id="cfSumPerPerson">—</span></div>

          <div class="cf-cost-total">
            <div class="k">PAGÁS AHORA</div>
            <div class="v" id="cfSumPay">$ —<small id="cfSumPaySub"></small></div>
          </div>

          <button type="submit" class="cf-submit" id="cfSubmit" disabled>
            Reservar cancha y pagar
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </button>
          <div class="cf-submit-foot">
            Te redirigimos a Mercado Pago. Los jugadores que se anoten pagan su parte en el complejo.
          </div>
        </div>

      </aside>
    </div>
  </form>
</div>

<script>
(function(){
  const FIELD_ID = {{ $field->id }};
  const FALLBACK_PRICE = {{ (float) $defaultPrice }};
  const CURRENCY = '{{ $field->price->currency ?? 'ARS' }}';
  const CATEGORY_ORDER = @json($sportCategories);
  const SPORT_LABEL = @json($sportLabel);

  let currentSlotPrice = FALLBACK_PRICE;
  let selectedDate = null;
  let selectedTime = null;
  let availabilityCache = {};

  function fmtMoney(n) {
    const r = Math.round(n);
    return '$ ' + r.toLocaleString('es-AR');
  }
  function fmtMoneyARS(n) {
    const r = Math.round(n * 100) / 100;
    return 'AR$ ' + r.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
  }

  // Day scroller — arrows + wheel
  const daysEl = document.getElementById('cfDays');
  const prevBtn = document.getElementById('cfDaysPrev');
  const nextBtn = document.getElementById('cfDaysNext');
  function updateArrows() {
    if (!daysEl) return;
    const max = daysEl.scrollWidth - daysEl.clientWidth - 1;
    prevBtn.classList.toggle('disabled', daysEl.scrollLeft <= 0);
    nextBtn.classList.toggle('disabled', daysEl.scrollLeft >= max);
  }
  if (daysEl) {
    prevBtn.addEventListener('click', () => daysEl.scrollBy({ left: -180, behavior: 'smooth' }));
    nextBtn.addEventListener('click', () => daysEl.scrollBy({ left: 180, behavior: 'smooth' }));
    daysEl.addEventListener('scroll', updateArrows);
    daysEl.addEventListener('wheel', (e) => {
      if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
        e.preventDefault();
        daysEl.scrollLeft += e.deltaY;
      }
    }, { passive: false });
    setTimeout(updateArrows, 50);
    window.addEventListener('resize', updateArrows);
  }

  // Day picker
  document.querySelectorAll('#cfDays .cf-slot-day').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('#cfDays .cf-slot-day').forEach(x => x.classList.remove('active'));
      btn.classList.add('active');
      selectedDate = btn.dataset.date;
      selectedTime = null;
      loadTimes(selectedDate);
    });
  });

  async function loadTimes(date) {
    const wrap = document.getElementById('cfTimes');
    wrap.className = 'cf-slot-empty';
    wrap.textContent = 'Cargando horarios…';
    try {
      let data;
      if (availabilityCache[date]) {
        data = availabilityCache[date];
      } else {
        const r = await fetch(`/fields/${FIELD_ID}/availability?date=${date}`);
        data = await r.json();
        availabilityCache[date] = data;
      }
      renderTimes(data.slots || []);
    } catch (e) {
      wrap.className = 'cf-slot-empty';
      wrap.textContent = 'Error cargando horarios. Probá otro día.';
    }
  }

  function renderTimes(slots) {
    const wrap = document.getElementById('cfTimes');
    // Filtrar solo los disponibles
    const available = (slots || []).filter(s => !s.status || String(s.status).toUpperCase() === 'AVAILABLE');
    if (!available.length) {
      wrap.className = 'cf-slot-empty';
      wrap.textContent = slots && slots.length
        ? 'Todos los horarios de este día están ocupados. Probá otro día.'
        : 'No hay horarios disponibles para este día.';
      return;
    }
    wrap.className = 'cf-slot-times';
    wrap.innerHTML = '';
    available.forEach(s => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'cf-slot-time';
      btn.dataset.time = s.start_at;
      btn.dataset.price = s.price ?? FALLBACK_PRICE;
      const priceTxt = s.price ? `<span class="price">$ ${Number(s.price).toLocaleString('es-AR')}</span>` : '';
      btn.innerHTML = (s.start_at || '') + priceTxt;
      btn.addEventListener('click', () => {
        wrap.querySelectorAll('.cf-slot-time').forEach(x => x.classList.remove('active'));
        btn.classList.add('active');
        selectedTime = s.start_at;
        currentSlotPrice = parseFloat(btn.dataset.price) || FALLBACK_PRICE;
        updateStartAt();
        recalculate();
      });
      wrap.appendChild(btn);
    });
  }

  function updateStartAt() {
    if (selectedDate && selectedTime) {
      document.getElementById('cfStartAt').value = selectedDate + 'T' + selectedTime;
      const status = document.getElementById('cfStatus1');
      status.classList.remove('todo');
      status.innerHTML = '<span class="ok"><svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg></span>Listo';
    } else {
      document.getElementById('cfStartAt').value = '';
    }
  }

  // Stepper
  document.querySelectorAll('[data-step]').forEach(b => {
    b.addEventListener('click', () => {
      const id = b.dataset.step;
      const d = parseInt(b.dataset.d, 10);
      const inp = document.getElementById(id);
      const v = parseInt(inp.value) || 0;
      const min = parseInt(inp.min) || 1;
      const max = parseInt(inp.max) || 999;
      inp.value = Math.min(max, Math.max(min, v + d));
      recalculate();
    });
  });
  document.getElementById('total_players').addEventListener('input', recalculate);
  document.getElementById('initiator_players').addEventListener('input', recalculate);

  // Toggles (opciones extra)
  document.querySelectorAll('.cf-toggle').forEach(t => {
    t.addEventListener('click', (e) => {
      // permitimos que el click en el input nativo lo maneje el browser
      if (e.target.tagName === 'INPUT') return;
      e.preventDefault();
      const cb = t.querySelector('input[type="checkbox"]');
      cb.checked = !cb.checked;
      t.classList.toggle('on', cb.checked);
    });
  });

  // Gender segments
  document.querySelectorAll('#cfGenderSegs .cf-seg').forEach(s => {
    s.addEventListener('click', () => {
      document.querySelectorAll('#cfGenderSegs .cf-seg').forEach(x => x.classList.remove('active'));
      s.classList.add('active');
      document.getElementById('cfGender').value = s.dataset.gender;
      recalculate();
    });
  });

  // Category sync + viz
  window.cfSyncCat = function(side) {
    const minSel = document.getElementById('category_min');
    const maxSel = document.getElementById('category_max');
    if (!minSel || !maxSel) return;
    const minIdx = minSel.value === '' ? -1 : CATEGORY_ORDER.indexOf(minSel.value);
    const maxIdx = maxSel.value === '' ? -1 : CATEGORY_ORDER.indexOf(maxSel.value);
    if (side === 'min' && minIdx !== -1 && maxIdx !== -1 && maxIdx < minIdx) maxSel.value = minSel.value;
    if (side === 'max' && maxIdx !== -1 && minIdx !== -1 && minIdx > maxIdx) minSel.value = maxSel.value;
    // viz
    const a = minSel.value === '' ? 0 : CATEGORY_ORDER.indexOf(minSel.value);
    const b = maxSel.value === '' ? CATEGORY_ORDER.length - 1 : CATEGORY_ORDER.indexOf(maxSel.value);
    document.querySelectorAll('#cfCatViz [data-cat]').forEach(el => {
      const idx = CATEGORY_ORDER.indexOf(el.dataset.cat);
      const inRange = idx >= a && idx <= b;
      el.style.background = inRange ? 'rgba(74,222,128,.06)' : 'rgba(255,255,255,.025)';
      el.style.borderColor = inRange ? 'rgba(74,222,128,.25)' : 'var(--cf-bd)';
      el.style.color = inRange ? 'var(--cf-accent)' : 'var(--cf-tx-3)';
      el.style.fontWeight = inRange ? '500' : '400';
    });
    recalculate();
  };
  if (document.getElementById('category_min')) cfSyncCat('init');

  // Age sync — inputs numéricos: min no puede superar max
  (function () {
    const minEl = document.getElementById('age_min');
    const maxEl = document.getElementById('age_max');
    if (!minEl || !maxEl) return;
    function sync(changed) {
      const min = parseInt(minEl.value, 10);
      const max = parseInt(maxEl.value, 10);
      if (!isNaN(min) && !isNaN(max) && min > max) {
        if (changed === 'min') maxEl.value = min;
        else minEl.value = max;
      }
      recalculate();
    }
    minEl.addEventListener('change', () => sync('min'));
    maxEl.addEventListener('change', () => sync('max'));
  })();

  // Live recalc + preview
  function recalculate() {
    const total = parseInt(document.getElementById('total_players').value) || 0;
    const init  = parseInt(document.getElementById('initiator_players').value) || 0;
    const valid = total >= 2 && init >= 1 && init < total;
    const needed = Math.max(0, total - init);
    const perPerson = total > 0 ? currentSlotPrice / total : 0;
    const myPay = perPerson * init;

    // hint under initiator
    document.getElementById('cfNeededHint').textContent = needed + (needed === 1 ? ' lugar' : ' lugares');

    // price section
    document.getElementById('cfTotalPrice').value = currentSlotPrice > 0 ? currentSlotPrice.toLocaleString('es-AR') : '—';
    document.getElementById('cfPerPerson').textContent = currentSlotPrice > 0 && total > 0 ? fmtMoneyARS(perPerson) : 'AR$ —';

    // sidebar resumen
    document.getElementById('cfSumTotal').textContent = currentSlotPrice > 0 ? fmtMoney(currentSlotPrice) : '$ —';
    document.getElementById('cfSumTotalPlayers').textContent = total || '—';
    document.getElementById('cfSumInit').textContent = init || '—';
    document.getElementById('cfSumNeeded').textContent = needed > 0 ? (needed + (needed === 1 ? ' lugar' : ' lugares')) : '—';
    document.getElementById('cfSumPerPerson').textContent = currentSlotPrice > 0 && total > 0 ? fmtMoney(perPerson) : '—';
    document.getElementById('cfSumPay').innerHTML = (currentSlotPrice > 0 && valid)
      ? fmtMoney(myPay) + '<small> tu parte × ' + init + '</small>'
      : '$ —<small></small>';

    // preview
    document.getElementById('cfPvPrice').textContent = (currentSlotPrice > 0 && total > 0) ? fmtMoney(perPerson) : '$ —';
    const gMap = { mixed: 'Mixto', male: 'Masculino', female: 'Femenino' };
    document.getElementById('cfPvGender').textContent = gMap[document.getElementById('cfGender').value] || 'Mixto';

    // level tag
    const minCat = document.getElementById('category_min');
    const maxCat = document.getElementById('category_max');
    const lvlTag = document.getElementById('cfPvLevel');
    if (minCat && maxCat && (minCat.value || maxCat.value)) {
      const txt = (minCat.value ? (minCat.value.charAt(0).toUpperCase() + minCat.value.slice(1)) : 'Cualq.') + ' – ' + (maxCat.value ? (maxCat.value.charAt(0).toUpperCase() + maxCat.value.slice(1)) : 'Cualq.');
      lvlTag.textContent = txt;
      lvlTag.style.display = '';
    } else if (lvlTag) {
      lvlTag.style.display = 'none';
    }

    // when label
    if (selectedDate && selectedTime) {
      const d = new Date(selectedDate + 'T' + selectedTime);
      const dayLbl = d.toLocaleDateString('es-AR', { weekday: 'short', day: '2-digit' });
      document.getElementById('cfPvWhen').innerHTML = '<b>' + dayLbl.replace('.', '') + '</b> · ' + selectedTime;
    } else {
      document.getElementById('cfPvWhen').innerHTML = '<b>Elegí día y hora</b>';
    }

    // roster avatars
    const roster = document.getElementById('cfPvRoster');
    roster.innerHTML = '';
    const max = Math.min(total, 10);
    for (let i = 0; i < max; i++) {
      const span = document.createElement('span');
      if (i < init) {
        const colors = [['#4ade80','#22a55a'],['#7abef5','#2a6aaa'],['#fda4af','#be123c'],['#a78bfa','#5a3da8'],['#f5c17a','#a88844'],['#94e8c4','#33996c']];
        const [c1, c2] = colors[i % colors.length];
        span.className = 'cf-pm-ava';
        span.style.background = `linear-gradient(135deg, ${c1}, ${c2})`;
        span.textContent = i === 0 ? '{{ strtoupper(substr(auth()->user()->name ?? 'Y', 0, 1)) }}' : '·';
      } else {
        span.className = 'cf-pm-ava empty';
        span.textContent = '+';
      }
      roster.appendChild(span);
    }
    const pct = total > 0 ? Math.round((init / total) * 100) : 0;
    document.getElementById('cfPvBar').style.width = pct + '%';

    // submit gating
    const btn = document.getElementById('cfSubmit');
    btn.disabled = !(valid && selectedDate && selectedTime && currentSlotPrice > 0);
  }

  recalculate();
})();
</script>

@endsection
