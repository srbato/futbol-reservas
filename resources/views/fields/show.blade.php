@extends('layouts.app')

@section('title', $field->name . ' — ' . $field->venue->name . ' | TuCancha')
@section('meta_description', 'Reservá ' . $field->name . ' en ' . $field->venue->name . '. Cancha de ' . $field->sport . ' disponible online en TuCancha. Consultá horarios y confirmá tu turno.')
@section('og_title', $field->name . ' — ' . $field->venue->name)
@section('og_description', 'Reservá ' . $field->name . ' en ' . $field->venue->name . '. Cancha de ' . $field->sport . ' disponible online en TuCancha.')
@if($field->cover_image_path)
  @section('og_image', Storage::url($field->cover_image_path))
@elseif($field->venue->cover_image_path)
  @section('og_image', Storage::url($field->venue->cover_image_path))
@endif

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<style>
  /* ── Scroll progress bar ───────────────────────── */
  .fs-progress {
    position: fixed;
    top: 0;
    left: 0;
    width: 0%;
    height: 3px;
    background: linear-gradient(90deg, #4ade80, #22c55e);
    z-index: 9999;
    transition: width .1s linear;
  }

  /* ── HERO ──────────────────────────────────────── */
  .fs-hero {
    position: relative;
    height: 480px;
    overflow: hidden;
    border-radius: 28px;
    margin-bottom: 28px;
  }

  .fs-hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .fs-hero-bg-placeholder {
    position: absolute;
    inset: 0;
    background-image: url('/images/hero-cancha.webp');
    background-size: cover;
    background-position: center;
    opacity: .3;
  }

  .fs-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.55);
  }

  .fs-hero-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,.1) 0%, rgba(0,0,0,.85) 100%);
  }

  .fs-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    border: 2px solid rgba(255,255,255,.06);
    border-radius: 28px;
    pointer-events: none;
    z-index: 2;
  }

  .fs-hero::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 140px;
    height: 140px;
    border: 1.5px solid rgba(255,255,255,.04);
    border-radius: var(--radius-full);
    pointer-events: none;
    z-index: 2;
  }

  .fs-hero-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 36px 40px;
    z-index: 5;
    gap: 14px;
  }

  .fs-back-link {
    position: absolute;
    top: 28px;
    left: 40px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    color: rgba(255,255,255,.8);
    text-decoration: none;
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: var(--radius-full);
    padding: 7px 16px;
    transition: all .2s;
    z-index: 6;
  }
  .fs-back-link:hover {
    background: rgba(255,255,255,.18);
    color: #fff;
    transform: translateX(-2px);
  }

  .fs-hero-title {
    font-size: 48px;
    font-weight: 900;
    letter-spacing: -.035em;
    line-height: 1.05;
    color: #fff;
    margin: 0;
    text-shadow: 0 2px 20px rgba(0,0,0,.4);
  }

  .fs-hero-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .fs-hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,.9);
  }

  .fs-hero-chip.green {
    background: rgba(34,197,94,.12);
    border-color: rgba(34,197,94,.3);
    color: #4ade80;
  }

  .fs-sport-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(59,130,246,.15);
    border: 1px solid rgba(59,130,246,.3);
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 700;
    color: #93c5fd;
    align-self: flex-start;
  }

  /* Particles */
  .fs-particle {
    position: absolute;
    width: 4px;
    height: 4px;
    border-radius: var(--radius-full);
    background: rgba(74,222,128,.5);
    animation: fs-float linear infinite;
    z-index: 3;
    pointer-events: none;
  }

  @keyframes fs-float {
    0%   { transform: translateY(0) translateX(0) scale(1); opacity: .6; }
    33%  { transform: translateY(-28px) translateX(10px) scale(.8); opacity: .3; }
    66%  { transform: translateY(-14px) translateX(-8px) scale(1.1); opacity: .5; }
    100% { transform: translateY(-44px) translateX(4px) scale(.7); opacity: 0; }
  }

  @media (max-width: 768px) { .fs-particle { display: none; } }

  /* ── AVAILABILITY BLOCK ────────────────────────── */
  .fs-avail-block {
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 24px;
    padding: 24px 28px;
    margin-bottom: 24px;
  }

  .fs-avail-title {
    font-size: 20px;
    font-weight: 800;
    color: #e8e8e8;
    margin: 0 0 2px 0;
    border-left: 4px solid var(--color-primary);
    padding-left: 14px;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .fs-avail-dot {
    width: 8px;
    height: 8px;
    border-radius: var(--radius-full);
    background: var(--color-primary);
    animation: fs-dot-pulse 2s ease-in-out infinite;
    flex-shrink: 0;
  }

  @keyframes fs-dot-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: .4; transform: scale(0.7); }
  }

  .fs-avail-sub {
    font-size: 13px;
    color: var(--color-text-muted);
    margin: 4px 0 20px 18px;
  }

  /* Custom date picker */
  .fs-date-display {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 10px 18px;
    background: #111;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 14px;
    color: #e8e8e8;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background .18s, border-color .2s;
    position: relative;
    user-select: none;
  }
  .fs-date-display:hover {
    background: rgba(34,197,94,.08);
    border-color: var(--color-primary);
  }

  .fs-date-input-native {
    position: absolute;
    inset: 0;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
    z-index: 2;
  }

  /* Legend */
  .fs-legend-bar {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    padding-top: 16px;
    border-top: 1px solid var(--color-border);
    margin-top: 16px;
  }

  .fs-legend-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 11px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .fs-legend-dot {
    width: 7px;
    height: 7px;
    border-radius: var(--radius-full);
    flex-shrink: 0;
  }

  /* ── SLOT CARDS ────────────────────────────────── */
  .fs-slot-card {
    background: var(--color-bg-card);
    border-radius: 14px;
    border: 1px solid var(--color-border);
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
    transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
  }

  .fs-slot-card.fs-available:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(34,197,94,.15);
    border-color: var(--color-primary);
  }

  .fs-slot-card.fs-disabled {
    opacity: .5;
    cursor: not-allowed;
  }

  .fs-slot-time {
    font-size: 22px;
    font-weight: 900;
    color: #e8e8e8;
    letter-spacing: -.01em;
  }

  .fs-slot-duration {
    font-size: 12px;
    color: #666;
    margin-top: 2px;
  }

  .fs-slot-status-badge {
    position: absolute;
    top: 14px;
    right: 14px;
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    border-radius: var(--radius-full);
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
  }

  .fs-slot-price {
    font-size: 20px;
    font-weight: 800;
  }

  .fs-slot-price.green  { color: var(--color-primary); }
  .fs-slot-price.yellow { color: #fbbf24; }
  .fs-slot-price.purple { color: #7c3aed; }
  .fs-slot-price.muted  { color: var(--color-text-muted); }

  .fs-slot-price-strike {
    text-decoration: line-through;
    color: var(--color-text-muted);
    font-size: 13px;
    font-weight: 400;
  }

  .fs-btn-reserve {
    display: block;
    width: 100%;
    padding: 11px 16px;
    background: var(--color-primary);
    color: var(--color-text-inverse);
    font-weight: 700;
    font-size: 14px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background .2s, box-shadow .2s, transform .15s;
    font-family: inherit;
    text-align: center;
    box-sizing: border-box;
  }
  .fs-btn-reserve:hover {
    background: var(--color-primary-hover);
    box-shadow: 0 0 20px rgba(34,197,94,.35);
    transform: translateY(-1px);
  }

  .fs-btn-recurring {
    display: block;
    width: 100%;
    padding: 9px 16px;
    background: transparent;
    color: var(--color-primary-hover);
    font-weight: 700;
    font-size: 13px;
    border: 1px solid var(--color-primary);
    border-radius: 12px;
    cursor: pointer;
    transition: background .18s, border-color .2s;
    font-family: inherit;
    text-align: center;
    box-sizing: border-box;
  }
  .fs-btn-recurring:hover {
    background: rgba(34,197,94,.1);
    border-color: var(--color-primary-hover);
  }

  /* Skeleton loader */
  @keyframes fs-skeleton-pulse {
    0%, 100% { opacity: .6; }
    50% { opacity: .25; }
  }

  .fs-skeleton-card {
    background: rgba(255,255,255,.04);
    border-radius: 14px;
    border: 1px solid var(--color-border);
    padding: 18px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    animation: fs-skeleton-pulse 1.5s ease-in-out infinite;
  }

  .fs-skeleton-line {
    background: var(--color-border);
    border-radius: 8px;
  }

  /* ── MODALS ────────────────────────────────────── */
  .fs-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.7);
    z-index: 200;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .fs-modal {
    background: #111;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 24px;
    padding: 28px 30px;
    width: 100%;
    max-width: 420px;
    max-height: calc(100vh - 40px);
    overflow-y: auto;
    box-shadow: 0 24px 64px rgba(0,0,0,.45);
    position: relative;
  }

  .fs-modal-wide {
    max-width: 460px;
  }

  .fs-modal-title {
    font-size: 19px;
    font-weight: 800;
    color: #e8e8e8;
    margin: 0 0 6px 0;
    letter-spacing: -.015em;
  }

  .fs-modal-sub {
    font-size: 13px;
    color: #a0a0a0;
    margin: 0 0 20px 0;
    line-height: 1.5;
  }

  .fs-checkmark {
    width: 56px;
    height: 56px;
    background: rgba(34,197,94,.1);
    border: 2px solid rgba(34,197,94,.2);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    margin: 0 auto 16px auto;
    box-shadow: 0 0 28px rgba(34,197,94,.15);
    color: var(--color-primary);
  }

  .fs-modal-label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--color-text-muted);
    margin-bottom: 10px;
  }

  /* Frequency toggle pills */
  .fs-freq-pills {
    display: flex;
    gap: 8px;
  }

  .fs-freq-pill {
    flex: 1;
    padding: 9px 14px;
    text-align: center;
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 12px;
    background: #0a0a0a;
    color: #a0a0a0;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all .18s;
    font-family: inherit;
  }

  .fs-freq-pill.active,
  .fs-freq-pill:has(input:checked) {
    border-color: var(--color-primary);
    background: rgba(34,197,94,.1);
    color: #6ee7a0;
  }

  .fs-freq-pill input[type="radio"] {
    display: none;
  }

  .fs-modal-select {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid rgba(255,255,255,.1);
    border-radius: 12px;
    background: #0a0a0a;
    color: #e8e8e8;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
    appearance: none;
    cursor: pointer;
  }
  .fs-modal-select:focus {
    border-color: var(--color-primary);
  }

  .fs-discount-preview {
    padding: 12px 16px;
    background: rgba(34,197,94,.1);
    border: 1px solid rgba(34,197,94,.2);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    color: #6ee7a0;
    display: none;
  }

  .fs-discount-tiers {
    padding: 12px 16px;
    background: #0a0a0a;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 12px;
  }

  .modo-btn {
    display: block;
    padding: 12px 14px;
    border: 2px solid rgba(255,255,255,.1);
    border-radius: 12px;
    cursor: pointer;
    background: #0a0a0a;
    transition: border-color .15s, background .15s;
    font-size: 13px;
    color: #e8e8e8;
    line-height: 1.4;
  }

  .modo-btn:hover {
    border-color: #86efac;
  }

  .modo-btn--active {
    border-color: var(--color-primary-hover);
    background: rgba(34,197,94,.1);
  }

  .fs-modal-btn-row {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
    flex-wrap: wrap;
  }

  .fs-btn-modal-primary {
    padding: 10px 20px;
    background: var(--color-primary);
    color: var(--color-text-inverse);
    font-weight: 700;
    font-size: 14px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background .2s, box-shadow .2s, transform .15s;
    font-family: inherit;
  }
  .fs-btn-modal-primary:hover:not(:disabled) {
    background: var(--color-primary-hover);
    box-shadow: 0 0 16px rgba(34,197,94,.3);
    transform: translateY(-1px);
  }
  .fs-btn-modal-primary:disabled {
    opacity: .6;
    cursor: not-allowed;
  }

  .fs-btn-modal-ghost {
    padding: 10px 20px;
    background: transparent;
    color: #a0a0a0;
    font-weight: 600;
    font-size: 14px;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 12px;
    cursor: pointer;
    transition: background .18s, border-color .18s;
    font-family: inherit;
  }
  .fs-btn-modal-ghost:hover {
    background: rgba(255,255,255,.04);
    border-color: rgba(255,255,255,.2);
    color: #e8e8e8;
  }

  /* Results rows */
  .fs-result-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid var(--color-border-light);
  }

  .fs-result-pill {
    padding: 6px 14px;
    border-radius: var(--radius-full);
    font-weight: 700;
    font-size: 13px;
  }

  /* Feedback */
  .fs-feedback {
    padding: 13px 16px;
    border-radius: 14px;
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 16px;
  }
  .fs-feedback.error {
    background: rgba(229,57,53,.1);
    border: 1px solid rgba(229,57,53,.2);
    color: #f87171;
  }
  .fs-feedback.success {
    background: rgba(34,197,94,.1);
    border: 1px solid rgba(34,197,94,.2);
    color: #6ee7a0;
  }

  /* ── SLOT ENTER ANIMATION ───────────────────────── */
  @keyframes fs-slot-enter {
    from { opacity: 0; transform: translateY(16px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ── FIELD INFO BLOCK ───────────────────────────── */
  .fs-field-info {
    display: grid;
    grid-template-columns: 340px 1fr;
    gap: 28px;
    align-items: start;
    margin-bottom: 24px;
  }

  .fs-field-info-img {
    width: 100%;
    height: 260px;
    border-radius: 20px;
    object-fit: cover;
    display: block;
  }

  .fs-field-info-body {
    background: var(--color-bg-page);
    border: 1px solid var(--color-border);
    border-radius: 24px;
    padding: 28px;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }

  .fs-field-info-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #a0a0a0;
    border-left: 4px solid var(--color-primary);
    padding-left: 10px;
  }

  .fs-field-info-title {
    font-size: 32px;
    font-weight: 900;
    color: #e8e8e8;
    letter-spacing: -.025em;
    line-height: 1.05;
    margin: 0;
  }

  .fs-field-info-badges {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
  }

  .fs-field-info-badge {
    padding: 5px 12px;
    border-radius: var(--radius-full);
    font-size: 13px;
    font-weight: 700;
  }

  .fs-field-info-badge.sport  { background: rgba(59,130,246,.08); color: #93c5fd; border: 1px solid rgba(59,130,246,.2); }
  .fs-field-info-badge.format { background: rgba(34,197,94,.1); color: #6ee7a0; border: 1px solid rgba(34,197,94,.2); }
  .fs-field-info-badge.time   { background: rgba(245,158,11,.08); color: #fbbf24; border: 1px solid rgba(245,158,11,.2); }

  .fs-field-info-chars {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
  }

  .fs-field-info-char {
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 12px;
    padding: 12px 14px;
  }

  .fs-field-info-char-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--color-text-muted);
    margin-bottom: 4px;
  }

  .fs-field-info-char-value {
    font-size: 14px;
    font-weight: 700;
    color: #e8e8e8;
  }

  .fs-field-info-price {
    font-size: 28px;
    font-weight: 900;
    color: var(--color-primary);
    line-height: 1;
  }

  .fs-field-info-price span {
    font-size: 13px;
    color: var(--color-text-muted);
    font-weight: 400;
  }

  .fs-field-info-back {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 700;
    color: #a0a0a0;
    text-decoration: none;
    transition: color .2s;
  }

  .fs-field-info-back:hover {
    color: var(--color-primary);
  }

  /* ── OTHER FIELDS BLOCK ─────────────────────────── */
  .fs-other-fields {
    margin-top: 24px;
  }

  .fs-other-fields-title {
    font-size: 18px;
    font-weight: 800;
    color: #e8e8e8;
    margin: 0 0 14px 0;
    border-left: 4px solid var(--color-primary);
    padding-left: 14px;
  }

  .fs-other-field-card {
    display: flex;
    align-items: center;
    gap: 14px;
    height: 88px;
    background: var(--color-bg-card);
    border: 1px solid var(--color-border);
    border-radius: 16px;
    padding: 0 16px 0 0;
    text-decoration: none;
    overflow: hidden;
    transition: box-shadow .2s, border-color .2s;
    margin-bottom: 10px;
  }

  .fs-other-field-card:hover {
    box-shadow: 0 4px 16px rgba(34,197,94,.15);
    border-color: var(--color-primary);
  }

  .fs-other-field-img {
    width: 88px;
    height: 88px;
    object-fit: cover;
    border-radius: 0;
    flex-shrink: 0;
    display: block;
  }

  .fs-other-field-img-placeholder {
    width: 88px;
    height: 88px;
    background: linear-gradient(135deg, rgba(34,197,94,.08), rgba(34,197,94,.15));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
  }

  .fs-other-field-info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .fs-other-field-name {
    font-size: 15px;
    font-weight: 800;
    color: #e8e8e8;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .fs-other-field-badges {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
  }

  .fs-other-field-badge {
    padding: 2px 8px;
    border-radius: var(--radius-full);
    font-size: 11px;
    font-weight: 700;
  }

  .fs-other-field-badge.sport  { background: rgba(59,130,246,.08); color: #93c5fd; border: 1px solid rgba(59,130,246,.2); }
  .fs-other-field-badge.format { background: rgba(34,197,94,.1); color: #6ee7a0; border: 1px solid rgba(34,197,94,.2); }

  .fs-other-field-price {
    font-size: 15px;
    font-weight: 800;
    color: var(--color-primary);
  }

  .fs-other-field-arrow {
    font-size: 20px;
    color: var(--color-primary);
    flex-shrink: 0;
    margin-left: 4px;
  }

  /* ── AVAIL BLOCK AMBIENT BG ─────────────────────── */
  .fs-avail-block {
    position: relative;
    overflow: hidden;
  }

  .fs-avail-ambient {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    opacity: .04;
    pointer-events: none;
    z-index: 0;
  }

  .fs-avail-block > *:not(.fs-avail-ambient) {
    position: relative;
    z-index: 1;
  }

  /* ── RESPONSIVE ────────────────────────────────── */
  @media (max-width: 768px) {
    .fs-hero { height: 340px; border-radius: 20px; }
    .fs-hero-content { padding: 22px 20px; }
    .fs-back-link { top: 16px; left: 18px; }
    .fs-hero-title { font-size: 30px; }
    .fs-avail-block { padding: 18px; }
    .fs-modal { padding: 22px 20px; }
    .fs-field-info { grid-template-columns: 1fr; }
    .fs-field-info-img { height: 200px; }
    .fs-field-info-chars { grid-template-columns: repeat(2, 1fr); }
    .fs-field-info-title { font-size: 24px; }
  }
</style>
@endpush

@section('content')

{{-- Scroll progress bar --}}
<div class="fs-progress" id="fsProgress"></div>

{{-- HERO --}}
<section class="fs-hero">
  @if($field->cover_image_path)
    <img class="fs-hero-bg"
         src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
         alt="{{ $field->name }}">
  @else
    <div class="fs-hero-bg-placeholder"></div>
  @endif

  <div class="fs-hero-overlay"></div>
  <div class="fs-hero-gradient"></div>

  {{-- Particles --}}
  <div class="fs-particle" style="left:7%;  bottom:28%; animation-duration:6.4s; animation-delay:0s;   width:5px; height:5px;"></div>
  <div class="fs-particle" style="left:18%; bottom:18%; animation-duration:7.6s; animation-delay:.9s;  width:3px; height:3px;"></div>
  <div class="fs-particle" style="left:30%; bottom:42%; animation-duration:5.8s; animation-delay:1.7s; width:4px; height:4px;"></div>
  <div class="fs-particle" style="left:42%; bottom:14%; animation-duration:8.2s; animation-delay:.4s;  width:6px; height:6px;"></div>
  <div class="fs-particle" style="left:55%; bottom:34%; animation-duration:6.6s; animation-delay:2.2s; width:3px; height:3px;"></div>
  <div class="fs-particle" style="left:65%; bottom:22%; animation-duration:7.1s; animation-delay:1.1s; width:5px; height:5px;"></div>
  <div class="fs-particle" style="left:75%; bottom:48%; animation-duration:6.0s; animation-delay:.6s;  width:4px; height:4px;"></div>
  <div class="fs-particle" style="left:84%; bottom:16%; animation-duration:8.5s; animation-delay:2.0s; width:3px; height:3px;"></div>
  <div class="fs-particle" style="left:90%; bottom:36%; animation-duration:6.3s; animation-delay:2.8s; width:5px; height:5px;"></div>
  <div class="fs-particle" style="left:22%; bottom:56%; animation-duration:7.4s; animation-delay:.8s;  width:3px; height:3px;"></div>
  <div class="fs-particle" style="left:48%; bottom:62%; animation-duration:7.0s; animation-delay:3.2s; width:4px; height:4px;"></div>
  <div class="fs-particle" style="left:68%; bottom:60%; animation-duration:8.1s; animation-delay:1.5s; width:3px; height:3px;"></div>

  {{-- Back link --}}
  <a href="{{ route('venues.show', $field->venue) }}" class="fs-back-link" style="display:inline-flex;align-items:center;gap:4px;">
    <i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;"></i> {{ $field->venue->name }}
  </a>

  {{-- Hero content --}}
  <div class="fs-hero-content">
    <span class="fs-sport-badge">
      {{ match($field->sport ?? '') { 'football'=>'Fútbol','padel'=>'Pádel','tennis'=>'Tenis','basketball'=>'Básquet','volleyball'=>'Vóley', default=>ucfirst($field->sport ?? 'Cancha') } }}
    </span>
    <h1 class="fs-hero-title">{{ $field->name }}</h1>
    <div class="fs-hero-chips">
      <span class="fs-hero-chip">{{ $field->format ?? '?' }}v{{ $field->format ?? '?' }}</span>
      <span class="fs-hero-chip">{{ $field->slot_minutes }} min</span>
      <span class="fs-hero-chip green">
        {{ $field->price->currency ?? 'ARS' }} {{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }} / turno
      </span>
    </div>
  </div>
</section>

{{-- FIELD INFO BLOCK --}}
<div class="fs-field-info" data-aos="fade-up">
  <img class="fs-field-info-img"
       src="{{ $field->cover_image_path ? \Illuminate\Support\Facades\Storage::url($field->cover_image_path) : '/images/hero-cancha.webp' }}"
       alt="{{ $field->name }}"
       data-aos="fade-right"
       loading="lazy">

  <div class="fs-field-info-body">
    <div data-aos="fade-left" data-aos-delay="60">
      <div class="fs-field-info-label">Sobre esta cancha</div>
    </div>
    <h2 class="fs-field-info-title" data-aos="fade-left" data-aos-delay="120">{{ $field->name }}</h2>

    <div class="fs-field-info-badges" data-aos="fade-left" data-aos-delay="160">
      <span class="fs-field-info-badge sport">
        {{ match($field->sport ?? '') { 'football'=>'Fútbol','padel'=>'Pádel','tennis'=>'Tenis','basketball'=>'Básquet','volleyball'=>'Vóley', default=>ucfirst($field->sport ?? 'Cancha') } }}
      </span>
      <span class="fs-field-info-badge format">{{ $field->format ?? '?' }}v{{ $field->format ?? '?' }}</span>
      <span class="fs-field-info-badge time">{{ $field->slot_minutes }} min</span>
    </div>

    <div class="fs-field-info-chars" data-aos="fade-left" data-aos-delay="200">
      @if($field->surface ?? null)
        <div class="fs-field-info-char">
          <div class="fs-field-info-char-label">Superficie</div>
          <div class="fs-field-info-char-value">{{ ucfirst($field->surface) }}</div>
        </div>
      @endif
      @if(($field->length ?? null) && ($field->width ?? null))
        <div class="fs-field-info-char">
          <div class="fs-field-info-char-label">Dimensiones</div>
          <div class="fs-field-info-char-value">{{ $field->length }}×{{ $field->width }}m</div>
        </div>
      @endif
      <div class="fs-field-info-char" data-aos="fade-left" data-aos-delay="240">
        <div class="fs-field-info-char-label">Iluminación</div>
        <div class="fs-field-info-char-value">{{ ($field->has_lighting ?? false) ? 'Con luces' : 'Sin luces' }}</div>
      </div>
    </div>

    <div data-aos="fade-left" data-aos-delay="280">
      <div class="fs-field-info-price">
        {{ $field->price->currency ?? 'ARS' }} {{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }}
        <span>por turno</span>
      </div>
    </div>

    <a href="{{ route('venues.show', $field->venue) }}" class="fs-field-info-back" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="arrow-left" style="width:14px;height:14px;stroke:currentColor;"></i> Ver el complejo</a>
  </div>
</div>

{{-- AVAILABILITY BLOCK --}}
<div class="fs-avail-block" data-aos="fade-up" data-aos-duration="500">
  <img class="fs-avail-ambient" src="/images/complejo-esperando-jugadores.webp" alt="" aria-hidden="true" loading="lazy">
  <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap; align-items:flex-end; margin-bottom:4px;">
    <div>
      <h2 class="fs-avail-title">
        <span class="fs-avail-dot"></span>
        Elegí tu turno
      </h2>
      <div class="fs-avail-sub">Seleccioná una fecha para ver los horarios disponibles</div>
    </div>
    <div>
      <label class="fs-modal-label" style="margin-bottom:6px;">Fecha</label>
      <div class="fs-date-display" id="fsDateDisplay">
        <svg width="16" height="16" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        <span id="fsDateLabel">Hoy</span>
        <input type="date" id="datePicker" class="fs-date-input-native"
               value="{{ now()->toDateString() }}"
               min="{{ now()->toDateString() }}">
      </div>
    </div>
  </div>

  <div class="fs-legend-bar">
    <span class="fs-legend-chip" style="background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.2); color:#6ee7a0;">
      <span class="fs-legend-dot" style="background:#22c55e;"></span>Disponible
    </span>
    <span class="fs-legend-chip" style="background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.2); color:#fbbf24;">
      <span class="fs-legend-dot" style="background:#f59e0b;"></span>Con descuento
    </span>
    <span class="fs-legend-chip" style="background:rgba(139,92,246,.08); border:1px solid rgba(139,92,246,.2); color:#c4b5fd;">
      <span class="fs-legend-dot" style="background:#8b5cf6;"></span>Nocturno
    </span>
    <span class="fs-legend-chip" style="background:rgba(229,57,53,.1); border:1px solid rgba(229,57,53,.2); color:#f87171;">
      <span class="fs-legend-dot" style="background:#ef4444;"></span>Bloqueado
    </span>
    <span class="fs-legend-chip" style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); color:#666;">
      <span class="fs-legend-dot" style="background:#555;"></span>No disponible
    </span>
  </div>
</div>

<div id="reservationFeedback" style="display:none; margin-bottom:16px;"></div>

<div id="slots">
  {{-- Skeleton --}}
  <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:12px;">
    @for($s = 0; $s < 6; $s++)
      <div class="fs-skeleton-card" style="animation-delay:{{ $s * 0.1 }}s;">
        <div class="fs-skeleton-line" style="height:28px; width:55%;"></div>
        <div class="fs-skeleton-line" style="height:14px; width:30%;"></div>
        <div class="fs-skeleton-line" style="height:22px; width:40%;"></div>
        <div class="fs-skeleton-line" style="height:40px; width:100%; border-radius:12px;"></div>
      </div>
    @endfor
  </div>
</div>

{{-- OTHER FIELDS --}}
@php $otherFields = $field->venue->fields->where('is_active', true)->where('id', '!=', $field->id)->take(3); @endphp
@if($otherFields->isNotEmpty())
<div class="fs-other-fields" data-aos="fade-up">
  <h3 class="fs-other-fields-title">Otras canchas del complejo</h3>
  @foreach($otherFields as $i => $otherField)
    <a href="{{ route('fields.show', $otherField) }}" class="fs-other-field-card" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
      @if($otherField->cover_image_path)
        <img class="fs-other-field-img"
             src="{{ \Illuminate\Support\Facades\Storage::url($otherField->cover_image_path) }}"
             alt="{{ $otherField->name }}"
             loading="lazy">
      @else
        <div class="fs-other-field-img-placeholder">
          <i data-lucide="layers" style="width:20px;height:20px;stroke:#aaa;stroke-width:1.5;"></i>
        </div>
      @endif
      <div class="fs-other-field-info">
        <div class="fs-other-field-name">{{ $otherField->name }}</div>
        <div class="fs-other-field-badges">
          <span class="fs-other-field-badge sport">{{ match($otherField->sport ?? '') { 'football'=>'Fútbol','padel'=>'Pádel','tennis'=>'Tenis','basketball'=>'Básquet','volleyball'=>'Vóley', default=>ucfirst($otherField->sport ?? 'Cancha') } }}</span>
          <span class="fs-other-field-badge format">{{ $otherField->format ?? '?' }}v{{ $otherField->format ?? '?' }}</span>
        </div>
        <div class="fs-other-field-price">
          {{ $otherField->price->currency ?? 'ARS' }} {{ number_format($otherField->price->price_per_slot ?? 0, 0, ',', '.') }}
        </div>
      </div>
      <span class="fs-other-field-arrow"><i data-lucide="arrow-right" style="width:16px;height:16px;stroke:currentColor;"></i></span>
    </a>
  @endforeach
</div>
@endif

{{-- PAY MODAL --}}
<div id="payModal" style="display:none;" class="fs-modal-overlay">
  <div class="fs-modal">
    <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px;">
      <img src="/images/reserva-confirmada.webp" alt="Reserva confirmada" loading="lazy"
           style="width:100px; height:100px; object-fit:cover; border-radius:12px; flex-shrink:0;">
      <div>
        <div class="fs-checkmark" style="margin:0 0 8px 0;"><i data-lucide="check-circle" style="width:24px;height:24px;stroke:#22c55e;stroke-width:2;"></i></div>
        <h3 class="fs-modal-title" style="margin:0 0 4px 0;">¡Turno reservado!</h3>
        <p id="payModalText" class="fs-modal-sub" style="margin:0;"></p>
      </div>
    </div>
    <div class="fs-modal-btn-row" style="justify-content:center;">
      <button onclick="closePayModal()" class="fs-btn-modal-ghost">Seguir mirando</button>
      <a id="payLink" href="#" class="fs-btn-modal-primary" style="text-decoration:none; display:inline-flex; align-items:center; gap:5px;">Ir a pagar <i data-lucide="arrow-right" style="width:15px;height:15px;stroke:currentColor;"></i></a>
    </div>
  </div>
</div>

@php $venueRecurringMode = $field->venue->recurring_payment_mode ?? 'upfront'; @endphp

<script>
  // ── Feedback helpers ────────────────────────────
  function showReservationFeedback(message, type = 'error') {
    const el = document.getElementById('reservationFeedback');
    if (!el) return;
    el.style.display = 'block';
    el.innerHTML = `<div class="fs-feedback ${type}">${message}</div>`;
  }

  function clearReservationFeedback() {
    const el = document.getElementById('reservationFeedback');
    if (!el) return;
    el.style.display = 'none';
    el.innerHTML = '';
  }

  function formatMoney(value, currency) {
    return `${currency} ${Number(value || 0).toLocaleString('es-AR')}`;
  }

  // ── Slot status config ──────────────────────────
  function getStatusConfig(slot) {
    if (slot.status === 'BLOCKED')     return { label: 'Bloqueado',     bg: 'rgba(229,57,53,.1)',   color: '#f87171',  border: 'rgba(229,57,53,.2)',  borderLeft: '#ef4444',  priceClass: 'muted',  cardClass: 'fs-disabled' };
    if (slot.status === 'PAST')        return { label: 'Pasado',        bg: 'rgba(255,255,255,.04)', color: '#666',     border: 'rgba(255,255,255,.08)', borderLeft: '#444',     priceClass: 'muted',  cardClass: 'fs-disabled' };
    if (slot.status === 'UNAVAILABLE') return { label: 'No disponible', bg: 'rgba(255,255,255,.04)', color: '#666',     border: 'rgba(255,255,255,.08)', borderLeft: '#444',     priceClass: 'muted',  cardClass: 'fs-disabled' };
    if (slot.has_discount)             return { label: 'Con descuento', bg: 'rgba(245,158,11,.08)', color: '#fbbf24',  border: 'rgba(245,158,11,.2)',  borderLeft: '#f59e0b',  priceClass: 'yellow', cardClass: 'fs-available' };
    if (slot.is_night_price)           return { label: 'Nocturno',      bg: 'rgba(139,92,246,.08)', color: '#c4b5fd',  border: 'rgba(139,92,246,.2)',  borderLeft: '#8b5cf6',  priceClass: 'purple', cardClass: 'fs-available' };
    return                                    { label: 'Disponible',    bg: 'rgba(34,197,94,.1)',   color: '#6ee7a0',  border: 'rgba(34,197,94,.2)',   borderLeft: '#22c55e',  priceClass: 'green',  cardClass: 'fs-available' };
  }

  // ── Render helpers ──────────────────────────────
  function renderEmptySlots(message) {
    document.getElementById('slots').innerHTML = `
      <div style="background:#0a0a0a; border:1px solid rgba(255,255,255,.08); border-radius:20px; padding:40px; text-align:center;">
        <div style="margin-bottom:10px;"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#444" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <p style="margin:0; color:#666; font-size:15px;">${message}</p>
      </div>`;
  }

  function renderSlots(data) {
    const el = document.getElementById('slots');
    if (!data.slots || data.slots.length === 0) { renderEmptySlots('No hay horarios disponibles para esa fecha.'); return; }

    const cards = data.slots.map((slot, index) => {
      const disabled = slot.status !== 'AVAILABLE';
      const status   = getStatusConfig(slot);

      const priceHtml = slot.has_discount
        ? `<div style="display:flex; align-items:baseline; gap:7px;">
             <span class="fs-slot-price-strike">${formatMoney(slot.original_price, slot.currency)}</span>
             <span class="fs-slot-price yellow">${formatMoney(slot.price, slot.currency)}</span>
           </div>`
        : disabled
        ? `<span class="fs-slot-price muted">${formatMoney(slot.price, slot.currency)}</span>`
        : slot.is_night_price
        ? `<span class="fs-slot-price purple">${formatMoney(slot.price, slot.currency)}</span>`
        : `<span class="fs-slot-price green">${formatMoney(slot.price, slot.currency)}</span>`;

      const discountHtml = slot.has_discount
        ? `<div style="font-size:12px; color:#d97706; font-weight:700; display:flex; align-items:center; gap:4px;"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2c0 6-8 8-8 14a8 8 0 0016 0c0-6-8-8-8-14z"/></svg> ${slot.discount_label ?? 'Descuento aplicado'}</div>` : '';
      const reasonHtml   = slot.reason
        ? `<div style="font-size:12px; color:#991b1b; line-height:1.4;">Motivo: ${slot.reason}</div>` : '';

      const reserveButton = disabled
        ? `<button type="button" disabled class="fs-btn-reserve" style="opacity:.5; cursor:not-allowed; background:#333; color:#666;">
             ${slot.status === 'PAST' ? 'Turno pasado' : 'No disponible'}
           </button>`
        : `<div style="display:flex; flex-direction:column; gap:7px;">
             <button type="button" class="fs-btn-reserve" onclick="reserve('${slot.start_at}')">Reservar</button>
             @if(($recurringPaymentMode ?? 'upfront') !== 'manual')
             <button type="button" class="fs-btn-recurring" onclick="openRecurringModal('${slot.start_at}')">Reservar recurrente</button>
             @endif
           </div>`;

      return `
        <div class="fs-slot-card ${status.cardClass}" style="border-left:4px solid ${status.borderLeft}; animation: fs-slot-enter 0.35s ease both; animation-delay: ${index * 60}ms;">
          <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:8px;">
            <div>
              <div class="fs-slot-time">${slot.start_at} – ${slot.end_at}</div>
              <div class="fs-slot-duration">{{ $field->slot_minutes }} min</div>
            </div>
            <span class="fs-slot-status-badge" style="background:${status.bg}; color:${status.color}; border:1px solid ${status.border};">${status.label}</span>
          </div>
          ${priceHtml}
          ${discountHtml}
          ${reasonHtml}
          ${reserveButton}
        </div>`;
    }).join('');

    el.innerHTML = `
      <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
        <h3 style="margin:0; font-size:17px; font-weight:800; color:#e8e8e8;">Turnos del día</h3>
        <span style="font-size:13px; color:#666;">${data.slots.length} horario${data.slots.length === 1 ? '' : 's'}</span>
      </div>
      <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:12px;">
        ${cards}
      </div>`;
  }

  // ── Load slots ──────────────────────────────────
  function loadSlots() {
    const date = document.getElementById('datePicker').value;
    clearReservationFeedback();

    // Show skeleton
    let skeletonHtml = '<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:12px;">';
    for (let i = 0; i < 6; i++) {
      skeletonHtml += `<div class="fs-skeleton-card" style="animation-delay:${i * 0.1}s;">
        <div class="fs-skeleton-line" style="height:28px; width:55%;"></div>
        <div class="fs-skeleton-line" style="height:14px; width:30%;"></div>
        <div class="fs-skeleton-line" style="height:22px; width:40%;"></div>
        <div class="fs-skeleton-line" style="height:40px; width:100%; border-radius:12px;"></div>
      </div>`;
    }
    skeletonHtml += '</div>';
    document.getElementById('slots').innerHTML = skeletonHtml;

    fetch(`{{ route('fields.availability', $field, false) }}?date=${encodeURIComponent(date)}`, { headers: { 'Accept': 'application/json' } })
      .then(async (r) => {
        const text = await r.text();
        if (!r.ok) throw new Error(`HTTP ${r.status}\n\n${text}`);
        try { return JSON.parse(text); } catch (e) { throw new Error(`La respuesta no es JSON válido:\n\n${text}`); }
      })
      .then(data => renderSlots(data))
      .catch((err) => {
        document.getElementById('slots').innerHTML = `
          <div class="fs-feedback error">
            Error cargando disponibilidad.
            <pre style="white-space:pre-wrap; margin-top:10px; font-size:12px; font-weight:400;">${err.message}</pre>
          </div>`;
        console.error(err);
      });
  }

  // ── Date display update ─────────────────────────
  function updateDateLabel(dateStr) {
    if (!dateStr) return;
    const d = new Date(dateStr + 'T00:00:00');
    const days = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    const months = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    const label = `${days[d.getDay()]} ${d.getDate()} ${months[d.getMonth()]}`;
    const el = document.getElementById('fsDateLabel');
    if (el) el.textContent = label;
  }

  loadSlots();
  updateDateLabel(document.getElementById('datePicker').value);

  document.getElementById('datePicker').addEventListener('change', function () {
    updateDateLabel(this.value);
    loadSlots();
  });

  // ── Reserve ─────────────────────────────────────
  function reserve(time) {
    const date = document.getElementById('datePicker').value;
    clearReservationFeedback();
    fetch("/reservations", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
      body: JSON.stringify({ field_id: {{ $field->id }}, start_at: date + " " + time })
    })
    .then(async (r) => {
      if (r.status === 401) { window.location.href = "{{ route('login') }}"; return; }
      const text = await r.text();
      let parsed = null;
      try { parsed = JSON.parse(text); } catch (e) { parsed = null; }
      if (!r.ok) {
        const msg = parsed?.message || parsed?.error || (parsed?.errors ? Object.values(parsed.errors).flat().join(' ') : null);
        const error = new Error(msg || `HTTP ${r.status} ${r.statusText}`);
        error.status = r.status;
        throw error;
      }
      return parsed;
    })
    .then((data) => {
      if (!data) return;
      const res = data.reservation ?? data;
      document.getElementById('payLink').href = `/reservations/${res.id}/checkout`;
      document.getElementById('payModalText').innerText = `Horario: ${time}. Tenés 10 minutos para completar el pago.`;
      document.getElementById('payModal').style.display = 'flex';
    })
    .catch((err) => {
      let message = 'No se pudo crear la reserva. Intentá nuevamente.';
      if (err.status === 409) message = 'Ese horario acaba de ser reservado por otra persona. Ya actualizamos la disponibilidad.';
      else if (err.message) message = err.message;
      loadSlots();
      setTimeout(() => {
        showReservationFeedback(message, 'error');
        document.getElementById('reservationFeedback')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }, 300);
      console.error(err);
    });
  }

  function closePayModal() {
    document.getElementById('payModal').style.display = 'none';
    location.reload();
  }

  // ── Recurring modal ─────────────────────────────
  let recurringTime = null;

  function openRecurringModal(time) {
    const date = document.getElementById('datePicker').value;
    recurringTime = time;
    const label = document.getElementById('recurringDateLabel');
    if (label) label.textContent = date + ' a las ' + time;
    document.getElementById('recurringModal').style.display = 'flex';
    updateUpfrontPreview();
    checkRecurringAvailability();
  }

  function closeRecurringModal() {
    document.getElementById('recurringModal').style.display = 'none';
    recurringTime = null;
  }

  function closeResultsModal() {
    document.getElementById('recurringResultsModal').style.display = 'none';
    location.reload();
  }

  const recurringTiers = {!! $tiersJson ?? '[]' !!};

  function updateDiscountPreview() {
    const occurrences = parseInt(document.getElementById('recurOccurrences')?.value ?? '0', 10);
    const preview = document.getElementById('discountPreview');
    if (!preview) return;
    const applicable = recurringTiers.filter(t => t.min_occurrences <= occurrences).sort((a, b) => b.min_occurrences - a.min_occurrences)[0];
    if (applicable) {
      preview.style.display = 'block';
      preview.textContent = `Se aplicará un descuento del ${applicable.discount_percentage}% al total`;
    } else {
      preview.style.display = 'none';
    }
  }

  const fieldPricePerSlot = {{ (float) ($field->price?->price_per_slot ?? 0) }};
  const fieldCurrency     = '{{ $field->price?->currency ?? 'ARS' }}';

  function formatPrice(amount) {
    return new Intl.NumberFormat('es-AR', { style: 'currency', currency: fieldCurrency, maximumFractionDigits: 0 }).format(amount);
  }

  function updateUpfrontPreview() {
    const occurrences = parseInt(document.getElementById('recurOccurrences')?.value ?? '4', 10);
    const subtotal = fieldPricePerSlot * occurrences;
    const applicable = recurringTiers.filter(t => t.min_occurrences <= occurrences).sort((a, b) => b.min_occurrences - a.min_occurrences)[0];
    const discount = applicable ? applicable.discount_percentage : 0;
    const total = Math.round(subtotal * (1 - discount / 100));
    const el = document.getElementById('upfrontTotalAmount');
    if (el) {
      el.textContent = formatPrice(total);
      if (discount > 0) {
        el.innerHTML = '<s style="color:#9ca3af; font-weight:400;">' + formatPrice(subtotal) + '</s> ' + formatPrice(total) + ' <span style="font-size:12px; color:#16a34a;">(' + discount + '% off)</span>';
      }
    }
  }

  @if(($venueRecurringMode ?? 'upfront') === 'subscription')
  function updateSubscriptionPreview() {
    const frequency = document.querySelector('input[name="recurFrequency"]:checked')?.value ?? 'weekly';
    const slotsPerMonth = frequency === 'biweekly' ? 2 : 4;
    const monthly = fieldPricePerSlot * slotsPerMonth;
    const el = document.getElementById('subscriptionMonthlyAmount');
    if (el) el.textContent = formatPrice(monthly);
  }
  @else
  function updateSubscriptionPreview() {}
  @endif

  let recurAvailAbort = null;

  function checkRecurringAvailability() {
    if (!recurringTime) return;
    const date = document.getElementById('datePicker').value;
    const frequency = document.querySelector('input[name="recurFrequency"]:checked')?.value ?? 'weekly';
    const occurrences = parseInt(document.getElementById('recurOccurrences')?.value ?? '4', 10);

    const preview = document.getElementById('recurringAvailabilityPreview');
    const btn = document.getElementById('recurSubmitBtn');

    preview.style.display = 'block';
    preview.innerHTML = '<div style="text-align:center; padding:12px; color:#a0a0a0; font-size:13px;">Verificando disponibilidad...</div>';
    btn.disabled = true;

    if (recurAvailAbort) recurAvailAbort.abort();
    recurAvailAbort = new AbortController();

    const params = new URLSearchParams({
      start_at: date + ' ' + recurringTime,
      frequency: frequency,
      occurrences: occurrences,
    });

    fetch(`/fields/{{ $field->id }}/recurring-availability?${params}`, {
      signal: recurAvailAbort.signal,
    })
    .then(r => r.json())
    .then(data => {
      const slots = data.slots || [];
      const unavailable = slots.filter(s => s.status === 'unavailable');
      const available = slots.filter(s => s.status === 'available');

      let html = '<div style="font-size:10px; font-weight:700; color:#9ca3af; margin-bottom:8px; text-transform:uppercase; letter-spacing:.07em;">Turnos a reservar</div>';

      html += '<div style="display:flex; flex-direction:column; gap:4px;">';
      slots.forEach(function(s) {
        // Parsear directamente el string "YYYY-MM-DD HH:MM:SS" sin pasar por Date()
        // para evitar conversiones de timezone del browser
        const parts = s.start.split(/[- :]/);
        const dateStr = new Date(parts[0], parts[1]-1, parts[2]).toLocaleDateString('es-AR', { weekday: 'long', day: 'numeric', month: 'short' });
        const timeStr = (parts[3] || '00') + ':' + (parts[4] || '00');
        const isOk = s.status === 'available';
        const icon = isOk
          ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>'
          : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
        const color = isOk ? '#6ee7a0' : '#f87171';
        const bg = isOk ? 'rgba(34,197,94,.1)' : 'rgba(229,57,53,.1)';
        const border = isOk ? 'rgba(34,197,94,.2)' : 'rgba(229,57,53,.2)';
        const reason = !isOk && s.reason ? ' — ' + s.reason : '';

        html += `<div style="display:flex; align-items:center; gap:8px; padding:8px 10px; background:${bg}; border:1px solid ${border}; border-radius:8px; font-size:13px; color:${color};">
          ${icon}
          <span style="font-weight:600;">${dateStr}</span> ${timeStr}${reason}
        </div>`;
      });
      html += '</div>';

      if (unavailable.length > 0) {
        html += `<div style="margin-top:10px; padding:10px 12px; background:rgba(229,57,53,.1); border:1px solid rgba(229,57,53,.2); border-radius:8px; font-size:13px; color:#f87171; font-weight:600;">
          ${unavailable.length} de ${slots.length} turnos no disponibles. Cambia la frecuencia o la cantidad para continuar.
        </div>`;
        btn.disabled = true;
      } else {
        btn.disabled = false;
      }

      preview.innerHTML = html;
    })
    .catch(err => {
      if (err.name === 'AbortError') return;
      preview.innerHTML = '<div style="padding:10px; color:#991b1b; font-size:13px;">Error al verificar disponibilidad.</div>';
      btn.disabled = false;
    });
  }

  function submitRecurring() {
    if (!recurringTime) return;
    const date = document.getElementById('datePicker').value;
    const frequency = document.querySelector('input[name="recurFrequency"]:checked')?.value ?? 'weekly';
    const mode = document.querySelector('[name="recurring_mode"]:checked')?.value ?? 'upfront';

    if (mode === 'subscription') {
      const occurrences = parseInt(document.getElementById('recurOccurrences').value, 10);
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/reservas/suscripcion';

      const csrf = document.createElement('input');
      csrf.type = 'hidden'; csrf.name = '_token';
      csrf.value = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

      const fieldId = document.createElement('input');
      fieldId.type = 'hidden'; fieldId.name = 'field_id';
      fieldId.value = {{ $field->id }};

      const startAt = document.createElement('input');
      startAt.type = 'hidden'; startAt.name = 'start_at';
      startAt.value = date + ' ' + recurringTime;

      const freq = document.createElement('input');
      freq.type = 'hidden'; freq.name = 'frequency';
      freq.value = frequency;

      const occ = document.createElement('input');
      occ.type = 'hidden'; occ.name = 'occurrences';
      occ.value = occurrences;

      form.appendChild(csrf);
      form.appendChild(fieldId);
      form.appendChild(startAt);
      form.appendChild(freq);
      form.appendChild(occ);
      document.body.appendChild(form);
      form.submit();
      return;
    }

    // Modo upfront — comportamiento original sin cambios
    const occurrences = parseInt(document.getElementById('recurOccurrences').value, 10);
    const btn = document.getElementById('recurSubmitBtn');
    btn.disabled = true; btn.textContent = 'Creando reservas...';
    fetch('/reservations/recurring', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
      body: JSON.stringify({ field_id: {{ $field->id }}, start_at: date + ' ' + recurringTime, frequency, occurrences }),
    })
    .then(async (r) => {
      if (r.status === 401) { window.location.href = '{{ route('login') }}'; return null; }
      const text = await r.text();
      try { return JSON.parse(text); } catch (e) { throw new Error('Respuesta inesperada del servidor.'); }
    })
    .then((data) => {
      if (!data) return;
      closeRecurringModal();
      if (data.batch && data.batch.checkout_url) { window.location.href = data.batch.checkout_url; }
      else { showRecurringResults(data); }
    })
    .catch((err) => { showReservationFeedback(err.message || 'No se pudieron crear las reservas.', 'error'); closeRecurringModal(); })
    .finally(() => { btn.disabled = false; btn.textContent = 'Crear reservas'; });
  }

  function showRecurringResults(data) {
    const { results, summary } = data;
    const rows = results.map(r => r.status === 'created'
      ? `<div class="fs-result-row">
           <span style="color:#22c55e; display:inline-flex; align-items:center;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg></span>
           <div>
             <div style="font-weight:700; font-size:14px; color:#e8e8e8;">${r.date} — ${r.time}</div>
             <div style="font-size:13px; color:#6ee7a0;">Reservada correctamente</div>
           </div>
         </div>`
      : `<div class="fs-result-row">
           <span style="color:#dc2626; display:inline-flex; align-items:center;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span>
           <div>
             <div style="font-weight:700; font-size:14px; color:#e8e8e8;">${r.date} — ${r.time}</div>
             <div style="font-size:13px; color:#f87171;">${r.reason ?? 'No disponible'}</div>
           </div>
         </div>`
    ).join('');

    const summaryHtml = `
      <div style="display:flex; gap:10px; margin-bottom:16px; flex-wrap:wrap;">
        <span class="fs-result-pill" style="background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.2); color:#6ee7a0; display:inline-flex; align-items:center; gap:4px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6ee7a0" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg> ${summary.created} reservadas</span>
        ${summary.failed > 0 ? `<span class="fs-result-pill" style="background:rgba(229,57,53,.1); border:1px solid rgba(229,57,53,.2); color:#f87171; display:inline-flex; align-items:center; gap:4px;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> ${summary.failed} fallidas</span>` : ''}
      </div>`;

    const noteHtml = summary.created > 0
      ? `<div style="margin-top:12px; padding:12px 16px; border-radius:12px; background:rgba(245,158,11,.08); border:1px solid rgba(245,158,11,.2); color:#fbbf24; font-size:13px;">
           Tenés <strong>30 minutos</strong> para pagar cada reserva desde <strong>Mis Reservas</strong>.
         </div>` : '';

    document.getElementById('recurringResultsBody').innerHTML = summaryHtml + rows + noteHtml;
    document.getElementById('recurringResultsModal').style.display = 'flex';
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.js"></script>
<script>
  const echoClient = new Echo({
    broadcaster:  'reverb',
    key:          '{{ config('broadcasting.connections.reverb.key') }}',
    wsHost:       '{{ config('broadcasting.connections.reverb.client_host') }}',
    wsPort:       {{ config('broadcasting.connections.reverb.client_port') }},
    wssPort:      {{ config('broadcasting.connections.reverb.client_port') }},
    forceTLS:     true,
    enabledTransports: ['ws', 'wss'],
  });

  echoClient.channel('field.{{ $field->id }}')
    .listen('.availability.changed', (e) => {
      const currentDate = document.getElementById('datePicker').value;
      if (e.date === currentDate) {
        loadSlots();
      }
    });
</script>

{{-- Recurring modal --}}
<div id="recurringModal" style="display:none;" class="fs-modal-overlay">
  <div class="fs-modal fs-modal-wide">
    <h3 class="fs-modal-title" style="display:flex;align-items:center;gap:8px;"><i data-lucide="refresh-cw" style="width:18px;height:18px;stroke:currentColor;"></i> Reserva recurrente</h3>
    <p class="fs-modal-sub">Turno: <strong id="recurringDateLabel" style="color:#e8e8e8;"></strong></p>

    @if($venueRecurringMode === 'subscription')
    <div style="margin-bottom:18px;">
      <label class="fs-modal-label">Tipo de pago</label>
      <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <label class="modo-btn modo-btn--active" data-mode="upfront" style="flex:1; min-width:130px;">
          <input type="radio" name="recurring_mode" value="upfront" checked style="display:none;">
          <strong style="display:block; margin-bottom:3px;">Pago unico</strong>
          <span style="font-size:12px; color:#a0a0a0;">Pagas todo ahora</span>
        </label>
        <label class="modo-btn" data-mode="subscription" style="flex:1; min-width:130px;">
          <input type="radio" name="recurring_mode" value="subscription" style="display:none;">
          <strong style="display:block; margin-bottom:3px;">Suscripcion mensual</strong>
          <span style="font-size:12px; color:#a0a0a0;">MP te cobra automaticamente cada mes</span>
        </label>
      </div>
    </div>
    @endif

    <div style="margin-bottom:18px;">
      <label class="fs-modal-label">Frecuencia</label>
      <div class="fs-freq-pills">
        <label class="fs-freq-pill active" id="fsFreqWeekly">
          <input type="radio" name="recurFrequency" value="weekly" checked onchange="updateSubscriptionPreview(); checkRecurringAvailability();">
          Semanal
        </label>
        <label class="fs-freq-pill" id="fsFreqBiweekly">
          <input type="radio" name="recurFrequency" value="biweekly" onchange="updateSubscriptionPreview(); checkRecurringAvailability();">
          Quincenal
        </label>
      </div>
    </div>

    <div id="occurrencesSection" style="margin-bottom:18px;">
      <label for="recurOccurrences" class="fs-modal-label">Cantidad de turnos</label>
      <select id="recurOccurrences" onchange="updateDiscountPreview(); updateSubscriptionPreview(); updateUpfrontPreview(); checkRecurringAvailability();" class="fs-modal-select">
        <option value="2">2 turnos</option>
        <option value="3">3 turnos</option>
        <option value="4" selected>4 turnos (~1 mes)</option>
        <option value="6">6 turnos (~1.5 meses)</option>
        <option value="8">8 turnos (~2 meses)</option>
        <option value="12">12 turnos (~3 meses)</option>
      </select>
    </div>

    @php
      $tiersJson = $recurringDiscounts->map(fn($t) => [
        'min_occurrences' => $t->min_occurrences,
        'discount_percentage' => (float) $t->discount_percentage,
      ])->values()->toJson();
    @endphp

    <div id="discountPreview" class="fs-discount-preview" style="margin-bottom:18px;"></div>

    <div id="upfrontPreview" style="margin-bottom:18px; padding:12px 14px; background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.2); border-radius:10px; font-size:13px; color:#6ee7a0;">
      Total a pagar: <strong id="upfrontTotalAmount">—</strong>
    </div>

    <div id="subscriptionPreview" style="display:none; margin-bottom:18px; padding:12px 14px; background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.2); border-radius:10px; font-size:13px; color:#6ee7a0;">
      MercadoPago cobrara <strong id="subscriptionMonthlyAmount">—</strong> por mes automaticamente.
      <div style="font-size:12px; color:#16a34a; margin-top:4px;">Podes cancelar cuando quieras.</div>
    </div>

    {{-- Previsualización de disponibilidad --}}
    <div id="recurringAvailabilityPreview" style="display:none; margin-bottom:18px;"></div>

    @if($recurringDiscounts->isNotEmpty())
      <div class="fs-discount-tiers" style="margin-bottom:18px;">
        <div style="font-size:10px; font-weight:700; color:#9ca3af; margin-bottom:10px; text-transform:uppercase; letter-spacing:.07em;">Descuentos disponibles</div>
        @foreach($recurringDiscounts as $t)
          <div style="font-size:13px; color:#a0a0a0; margin-bottom:4px;">
            {{ $t->min_occurrences }}+ turnos
            <strong style="color:#16a34a; display:inline-flex; align-items:center; gap:3px;"><i data-lucide="arrow-right" style="width:12px;height:12px;stroke:#16a34a;"></i> {{ $t->discount_percentage }}% off</strong>
          </div>
        @endforeach
      </div>
    @endif

    <div class="fs-modal-btn-row">
      <button onclick="closeRecurringModal()" class="fs-btn-modal-ghost">Cancelar</button>
      <button id="recurSubmitBtn" onclick="submitRecurring()" class="fs-btn-modal-primary">Crear reservas</button>
    </div>
  </div>
</div>

{{-- Results modal --}}
<div id="recurringResultsModal" style="display:none;" class="fs-modal-overlay" style="overflow-y:auto; align-items:flex-start; padding-top:40px;">
  <div class="fs-modal fs-modal-wide">
    <h3 class="fs-modal-title">Resultado de reservas</h3>
    <div id="recurringResultsBody" style="margin-top:16px;"></div>
    <div class="fs-modal-btn-row">
      <button onclick="closeResultsModal()" class="fs-btn-modal-ghost">Cerrar</button>
      <a href="{{ route('my_reservations') }}" class="fs-btn-modal-primary" style="text-decoration:none; display:inline-block;">Ir a Mis Reservas</a>
    </div>
  </div>
</div>

@if(($venueRecurringMode ?? 'upfront') === 'subscription')
<script>
  // ── Subscription mode selector (after modal HTML exists) ──
  document.querySelectorAll('[name="recurring_mode"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
      const isSubscription = this.value === 'subscription';
      document.getElementById('discountPreview').style.display      = isSubscription ? 'none' : '';
      document.getElementById('upfrontPreview').style.display        = isSubscription ? 'none' : '';
      document.getElementById('subscriptionPreview').style.display  = isSubscription ? '' : 'none';
      document.querySelectorAll('.modo-btn').forEach(function(btn) { btn.classList.remove('modo-btn--active'); });
      this.closest('.modo-btn').classList.add('modo-btn--active');
      if (isSubscription) updateSubscriptionPreview();
      else updateUpfrontPreview();
      checkRecurringAvailability();
    });
  });

  document.querySelectorAll('.modo-btn').forEach(function(label) {
    label.addEventListener('click', function() {
      const radio = this.querySelector('input[type="radio"]');
      if (radio) {
        radio.checked = true;
        radio.dispatchEvent(new Event('change'));
      }
    });
  });

  updateSubscriptionPreview();
</script>
@endif

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 550, once: true, easing: 'ease-out-cubic' });

  // Scroll progress bar
  (function () {
    const bar = document.getElementById('fsProgress');
    if (!bar) return;
    window.addEventListener('scroll', function () {
      const scrollTop = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      bar.style.width = (docHeight > 0 ? (scrollTop / docHeight) * 100 : 0) + '%';
    }, { passive: true });
  })();

  // Frequency pill toggle
  document.querySelectorAll('.fs-freq-pill').forEach(pill => {
    pill.addEventListener('click', function () {
      document.querySelectorAll('.fs-freq-pill').forEach(p => p.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>
@endpush

@endsection
