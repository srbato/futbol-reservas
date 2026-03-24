@extends('layouts.app')

@section('title', $venue->name)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<style>
  /* ── Scroll progress bar ───────────────────────── */
  .vs-progress {
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
  .vs-hero {
    position: relative;
    height: 520px;
    overflow: hidden;
    border-radius: 28px;
    margin-bottom: 32px;
  }

  .vs-hero-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .vs-hero-bg-placeholder {
    position: absolute;
    inset: 0;
    background-image: url('/Images/hero-cancha.webp');
    background-size: cover;
    background-position: center;
    opacity: .3;
  }

  .vs-hero-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.55);
  }

  .vs-hero-gradient {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,.1) 0%, rgba(0,0,0,.85) 100%);
  }

  .vs-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    border: 2px solid rgba(255,255,255,.06);
    border-radius: 28px;
    pointer-events: none;
    z-index: 2;
  }

  .vs-hero::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 160px;
    height: 160px;
    border: 1.5px solid rgba(255,255,255,.04);
    border-radius: 999px;
    pointer-events: none;
    z-index: 2;
  }

  .vs-hero-content {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    padding: 36px 40px;
    z-index: 5;
    gap: 16px;
  }

  .vs-back-link {
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
    border-radius: 999px;
    padding: 7px 16px;
    transition: all .2s;
    z-index: 6;
  }
  .vs-back-link:hover {
    background: rgba(255,255,255,.18);
    color: #fff;
    transform: translateX(-2px);
  }

  .vs-fav-btn {
    position: absolute;
    top: 28px;
    right: 40px;
    width: 44px;
    height: 44px;
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    z-index: 6;
  }
  .vs-fav-btn:hover {
    background: rgba(255,255,255,.2);
    transform: scale(1.1);
  }

  .vs-badge-verified {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 14px;
    background: rgba(34,197,94,.15);
    border: 1px solid rgba(34,197,94,.35);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    color: #4ade80;
    align-self: flex-start;
    animation: vs-badge-pulse 2.5s ease infinite;
  }

  @keyframes vs-badge-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34,197,94,0); }
    50% { box-shadow: 0 0 0 6px rgba(34,197,94,.18); }
  }

  .vs-badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 999px;
    background: #4ade80;
    animation: vs-pulse 1.8s ease-in-out infinite;
  }

  @keyframes vs-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: .6; transform: scale(1.35); }
  }

  .vs-hero-title {
    font-size: 52px;
    font-weight: 900;
    letter-spacing: -.035em;
    line-height: 1.05;
    color: #fff;
    margin: 0;
    text-shadow: 0 2px 20px rgba(0,0,0,.4);
  }

  .vs-hero-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .vs-hero-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,.9);
  }

  .vs-hero-chip.amber {
    background: rgba(245,158,11,.12);
    border-color: rgba(245,158,11,.28);
    color: #fcd34d;
  }

  /* Particles */
  .vs-particle {
    position: absolute;
    width: 4px;
    height: 4px;
    border-radius: 999px;
    background: rgba(74,222,128,.5);
    animation: vs-float linear infinite;
    z-index: 3;
    pointer-events: none;
  }

  @keyframes vs-float {
    0%   { transform: translateY(0) translateX(0) scale(1); opacity: .6; }
    33%  { transform: translateY(-28px) translateX(10px) scale(.8); opacity: .3; }
    66%  { transform: translateY(-14px) translateX(-8px) scale(1.1); opacity: .5; }
    100% { transform: translateY(-44px) translateX(4px) scale(.7); opacity: 0; }
  }

  @media (max-width: 768px) {
    .vs-particle { display: none; }
  }

  /* ── STATS + DESCRIPTION ───────────────────────── */
  .vs-info-block {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    padding: 28px 32px;
    margin-bottom: 28px;
  }

  .vs-stats-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 24px;
  }

  .vs-stat {
    flex: 1;
    min-width: 110px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 18px 20px;
    transition: border-color .2s, box-shadow .2s, transform .2s;
    cursor: default;
  }

  .vs-stat:hover {
    border-color: #22c55e;
    box-shadow: 0 4px 18px rgba(34,197,94,.12);
    transform: translateY(-3px);
  }

  .vs-stat-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: #9ca3af;
    margin-bottom: 6px;
  }

  .vs-stat-value {
    font-size: 36px;
    font-weight: 900;
    color: #22c55e;
    line-height: 1;
  }

  .vs-stat-sub {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 4px;
  }

  .vs-description {
    font-size: 15px;
    color: #4b5563;
    line-height: 1.65;
    margin: 0 0 20px 0;
  }

  .vs-amenities-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .vs-amenity-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 13px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: #15803d;
  }

  /* ── FIELDS SECTION ────────────────────────────── */
  .vs-fields-block {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    padding: 28px 32px;
    margin-bottom: 28px;
  }

  .vs-section-title {
    font-size: 22px;
    font-weight: 800;
    letter-spacing: -.02em;
    color: #111827;
    margin: 0 0 4px 0;
    border-left: 4px solid #22c55e;
    padding-left: 14px;
    line-height: 1.2;
  }

  .vs-section-subtitle {
    font-size: 13px;
    color: #9ca3af;
    margin: 0 0 20px 0;
    padding-left: 18px;
  }

  /* ── FIELD FILTERS ─────────────────────────────── */
  .vs-filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 20px;
  }

  .vs-filter-btn {
    padding: 7px 16px;
    border: 1.5px solid #e5e7eb;
    background: #ffffff;
    border-radius: 999px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    color: #6b7280;
    transition: all .18s;
  }

  .vs-filter-btn:hover {
    border-color: #22c55e;
    color: #111827;
  }

  .vs-filter-btn.active {
    border-color: #22c55e;
    background: #f0fdf4;
    color: #16a34a;
  }

  /* ── FIELD CARDS ───────────────────────────────── */
  .vs-fields-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 16px;
  }

  .vs-field-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
    cursor: pointer;
    position: relative;
  }

  .vs-field-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(105deg, transparent 40%, rgba(255,255,255,.5) 50%, transparent 60%);
    transform: translateX(-100%);
    transition: transform 0.5s ease;
    z-index: 1;
    pointer-events: none;
  }

  .vs-field-card:hover::before {
    transform: translateX(200%);
  }

  .vs-field-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 36px rgba(34,197,94,.15);
    border-color: #22c55e;
  }

  .vs-field-card-img-wrap {
    overflow: hidden;
    height: 160px;
  }

  .vs-field-card-img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    display: block;
    transition: transform .35s ease;
  }

  .vs-field-card:hover .vs-field-card-img {
    transform: scale(1.06);
  }

  .vs-field-card-img-placeholder {
    width: 100%;
    height: 160px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
  }

  .vs-field-card-body {
    padding: 18px 20px 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
  }

  .vs-field-card-name {
    font-size: 17px;
    font-weight: 800;
    color: #111827;
    margin: 0;
    letter-spacing: -.01em;
  }

  .vs-field-badges {
    display: flex;
    gap: 5px;
    flex-wrap: wrap;
  }

  .vs-field-badge {
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
  }

  .vs-field-badge.sport  { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
  .vs-field-badge.format { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
  .vs-field-badge.time   { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }

  .vs-zone-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    color: #15803d;
  }

  .vs-falta-uno-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: #22c55e;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    align-self: flex-start;
  }

  .vs-falta-uno-badge .vs-badge-dot {
    background: #fff;
    animation: vs-pulse 1.4s ease-in-out infinite;
  }

  .vs-field-price {
    font-size: 22px;
    font-weight: 900;
    color: #22c55e;
    line-height: 1;
  }

  .vs-field-price span {
    font-size: 12px;
    color: #9ca3af;
    font-weight: 400;
  }

  .vs-btn-green {
    display: block;
    text-align: center;
    text-decoration: none;
    background: #111827;
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    padding: 11px 16px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    transition: background .2s, box-shadow .2s, transform .15s;
    margin-top: auto;
  }

  .vs-btn-green:hover {
    background: #22c55e;
    box-shadow: 0 0 20px rgba(34,197,94,.35);
    transform: translateY(-1px);
    color: #fff;
  }

  .vs-btn-ghost-green {
    display: block;
    text-align: center;
    text-decoration: none;
    background: transparent;
    color: #16a34a;
    font-weight: 700;
    font-size: 13px;
    padding: 9px 16px;
    border-radius: 12px;
    border: 1px solid #22c55e;
    cursor: pointer;
    transition: background .2s, border-color .2s;
  }

  .vs-btn-ghost-green:hover {
    background: #f0fdf4;
    border-color: #16a34a;
  }

  /* ── REVIEWS ───────────────────────────────────── */
  .vs-reviews-block {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    padding: 28px 32px;
    margin-top: 8px;
  }

  .vs-reviews-avg {
    font-size: 48px;
    font-weight: 900;
    color: #22c55e;
    line-height: 1;
  }

  .vs-review-form-wrap {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 22px;
    margin-bottom: 24px;
  }

  .vs-review-textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background: #ffffff;
    color: #111827;
    font-size: 14px;
    resize: vertical;
    font-family: inherit;
    outline: none;
    transition: border-color .2s;
    box-sizing: border-box;
  }

  .vs-review-textarea:focus {
    border-color: #22c55e;
  }

  .vs-review-item {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px 18px;
    transition: box-shadow .2s;
  }

  .vs-review-item:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.05);
  }

  .vs-review-avatar {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    object-fit: cover;
    border: 2px solid #22c55e;
    flex-shrink: 0;
  }

  .vs-review-avatar-placeholder {
    width: 36px;
    height: 36px;
    border-radius: 999px;
    background: #f0fdf4;
    border: 2px solid #bbf7d0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
  }

  .vs-btn-primary-sm {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    background: #22c55e;
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    transition: box-shadow .2s, transform .15s, background .2s;
    font-family: inherit;
  }

  .vs-btn-primary-sm:hover {
    background: #16a34a;
    box-shadow: 0 0 16px rgba(34,197,94,.35);
    transform: translateY(-1px);
  }

  .vs-btn-ghost-sm {
    display: inline-flex;
    align-items: center;
    padding: 9px 18px;
    background: transparent;
    color: #6b7280;
    font-weight: 600;
    font-size: 13px;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: background .18s, border-color .18s;
    font-family: inherit;
  }

  .vs-btn-ghost-sm:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
  }

  /* ── MAP ───────────────────────────────────────── */
  .vs-map-block {
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    overflow: hidden;
    margin-top: 28px;
  }

  /* ── Label for review form ─────────────────────── */
  .vs-form-label {
    display: block;
    font-size: 12px;
    color: #6b7280;
    margin-bottom: 8px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
  }

  /* ── PRESENTATION BLOCK ─────────────────────────── */
  .vs-presentation-block {
    position: relative;
    border-radius: 24px;
    min-height: 220px;
    padding: 40px 48px;
    margin-bottom: 28px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 20px;
  }

  .vs-presentation-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    opacity: .25;
  }

  .vs-presentation-bg-placeholder {
    position: absolute;
    inset: 0;
    background-image: url('/Images/hero-cancha.webp');
    background-size: cover;
    background-position: center;
    opacity: .25;
  }

  .vs-presentation-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(5,46,22,0.90), rgba(10,20,14,0.85));
  }

  .vs-presentation-content {
    position: relative;
    z-index: 2;
  }

  .vs-presentation-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: rgba(255,255,255,.55);
    margin-bottom: 8px;
  }

  .vs-presentation-title {
    font-size: 40px;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.05;
    letter-spacing: -.03em;
    margin: 0 0 14px 0;
  }

  .vs-presentation-divider {
    width: 60px;
    height: 3px;
    background: #22c55e;
    border-radius: 99px;
    margin-bottom: 16px;
  }

  .vs-presentation-chips {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .vs-presentation-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 13px;
    background: rgba(255,255,255,.1);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: rgba(255,255,255,.9);
  }

  /* ── GALLERY BLOCK ──────────────────────────────── */
  .vs-gallery-block {
    margin-bottom: 28px;
  }

  .vs-gallery-title {
    font-size: 18px;
    font-weight: 800;
    color: #111827;
    margin: 0 0 14px 0;
    border-left: 4px solid #22c55e;
    padding-left: 14px;
  }

  .vs-gallery-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    grid-template-rows: 200px 160px;
    gap: 12px;
  }

  .vs-gallery-cell {
    border-radius: 16px;
    overflow: hidden;
    position: relative;
    cursor: pointer;
  }

  .vs-gallery-cell.tall {
    grid-row: 1 / 3;
  }

  .vs-gallery-cell img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .4s ease;
  }

  .vs-gallery-cell:hover img {
    transform: scale(1.05);
  }

  .vs-gallery-cell-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.45);
    opacity: 0;
    transition: opacity .3s;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .vs-gallery-cell:hover .vs-gallery-cell-overlay {
    opacity: 1;
  }

  .vs-gallery-cell-label {
    color: #fff;
    font-size: 14px;
    font-weight: 700;
    text-align: center;
    padding: 0 16px;
  }

  /* ── VIBE SECTION ───────────────────────────────── */
  .vs-vibe-section {
    position: relative;
    border-radius: 24px;
    height: 320px;
    margin-bottom: 28px;
    overflow: hidden;
    display: flex;
    align-items: center;
  }

  .vs-vibe-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    opacity: .18;
  }

  .vs-vibe-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to right, rgba(5,46,22,0.93) 0%, rgba(5,46,22,0.72) 55%, transparent 100%);
  }

  .vs-vibe-content {
    position: relative;
    z-index: 2;
    padding: 0 48px;
    max-width: 560px;
  }

  .vs-vibe-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #4ade80;
    margin-bottom: 10px;
  }

  .vs-vibe-title {
    font-size: 42px;
    font-weight: 900;
    color: #ffffff;
    line-height: 1.05;
    letter-spacing: -.03em;
    margin: 0 0 12px 0;
  }

  .vs-vibe-text {
    font-size: 16px;
    color: rgba(255,255,255,.7);
    line-height: 1.6;
    margin: 0 0 24px 0;
  }

  .vs-vibe-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 12px 24px;
    background: #22c55e;
    color: #fff;
    font-weight: 700;
    font-size: 15px;
    border-radius: 12px;
    text-decoration: none;
    transition: background .2s, box-shadow .2s, transform .15s;
  }

  .vs-vibe-btn:hover {
    background: #16a34a;
    box-shadow: 0 0 24px rgba(34,197,94,.45);
    transform: translateY(-2px);
    color: #fff;
  }

  /* ── MAP SECTION ────────────────────────────────── */
  .vs-map-section {
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    overflow: hidden;
    margin-top: 28px;
  }

  .vs-map-header {
    padding: 24px 28px;
    background: #ffffff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }

  .vs-map-header h3 {
    font-size: 20px;
    font-weight: 800;
    color: #111827;
    margin: 0 0 4px 0;
    letter-spacing: -.01em;
  }

  .vs-map-header p {
    font-size: 14px;
    color: #6b7280;
    margin: 0;
  }

  .vs-map-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border: 1.5px solid #22c55e;
    border-radius: 10px;
    color: #16a34a;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    transition: background .18s, border-color .18s;
    white-space: nowrap;
  }

  .vs-map-cta:hover {
    background: #f0fdf4;
    border-color: #16a34a;
    color: #15803d;
  }

  .vs-map-embed iframe {
    display: block;
    border: 0;
  }

  /* ── STAT COUNTER ───────────────────────────────── */
  .vs-stat-num {
    display: inline-block;
  }

  /* ── RESPONSIVE ────────────────────────────────── */
  @media (max-width: 768px) {
    .vs-hero { height: 360px; border-radius: 20px; }
    .vs-hero-content { padding: 24px 22px; }
    .vs-back-link { top: 18px; left: 20px; }
    .vs-fav-btn { top: 18px; right: 20px; }
    .vs-hero-title { font-size: 32px; }
    .vs-info-block { padding: 20px; }
    .vs-fields-block { padding: 20px; }
    .vs-reviews-block { padding: 20px; }
    .vs-stats-row { flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .vs-stat { min-width: 100px; flex-shrink: 0; }
    .vs-stat-value { font-size: 28px; }
    .vs-fields-grid { grid-template-columns: 1fr; }
    .vs-presentation-block { padding: 28px 24px; min-height: auto; }
    .vs-presentation-title { font-size: 28px; }
    .vs-gallery-grid { grid-template-columns: 1fr; grid-template-rows: 200px 160px 160px; }
    .vs-gallery-cell.tall { grid-row: auto; }
    .vs-vibe-section { height: auto; padding: 40px 0; }
    .vs-vibe-content { padding: 0 24px; }
    .vs-vibe-title { font-size: 28px; }
    .vs-map-header { padding: 18px 20px; }
  }
</style>
@endpush

@section('content')
@php
  use App\Http\Controllers\VenueAdmin\VenueController;
  $allAmenities   = VenueController::amenitiesList();
  $venueAmenities = $venue->amenities ?? [];
  $activeFields   = $venue->fields->where('is_active', true);
  $sports         = $activeFields->pluck('sport')->unique()->filter()->values();
  $roundedAverage = round($averageRating);
@endphp

{{-- Scroll progress bar --}}
<div class="vs-progress" id="vsProgress"></div>

{{-- HERO --}}
<section class="vs-hero">
  @if($venue->cover_image_path)
    <img class="vs-hero-bg"
         src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
         alt="{{ $venue->name }}">
  @else
    <div class="vs-hero-bg-placeholder"></div>
  @endif

  <div class="vs-hero-overlay"></div>
  <div class="vs-hero-gradient"></div>

  {{-- Particles (desktop only) --}}
  <div class="vs-particle" style="left:8%;  bottom:30%; animation-duration:6.2s; animation-delay:0s;    width:5px; height:5px;"></div>
  <div class="vs-particle" style="left:15%; bottom:20%; animation-duration:7.8s; animation-delay:.8s;   width:3px; height:3px;"></div>
  <div class="vs-particle" style="left:25%; bottom:40%; animation-duration:5.5s; animation-delay:1.6s;  width:4px; height:4px;"></div>
  <div class="vs-particle" style="left:38%; bottom:15%; animation-duration:8.1s; animation-delay:.3s;   width:6px; height:6px;"></div>
  <div class="vs-particle" style="left:52%; bottom:35%; animation-duration:6.7s; animation-delay:2.1s;  width:3px; height:3px;"></div>
  <div class="vs-particle" style="left:62%; bottom:25%; animation-duration:7.3s; animation-delay:1.2s;  width:5px; height:5px;"></div>
  <div class="vs-particle" style="left:72%; bottom:45%; animation-duration:5.9s; animation-delay:.5s;   width:4px; height:4px;"></div>
  <div class="vs-particle" style="left:80%; bottom:18%; animation-duration:8.4s; animation-delay:1.9s;  width:3px; height:3px;"></div>
  <div class="vs-particle" style="left:88%; bottom:38%; animation-duration:6.1s; animation-delay:2.6s;  width:5px; height:5px;"></div>
  <div class="vs-particle" style="left:20%; bottom:55%; animation-duration:7.5s; animation-delay:.7s;   width:3px; height:3px;"></div>
  <div class="vs-particle" style="left:45%; bottom:60%; animation-duration:6.9s; animation-delay:3.1s;  width:4px; height:4px;"></div>
  <div class="vs-particle" style="left:70%; bottom:58%; animation-duration:8.0s; animation-delay:1.4s;  width:3px; height:3px;"></div>

  {{-- Back link --}}
  <a href="{{ route('venues.index') }}" class="vs-back-link">← Volver a complejos</a>

  {{-- Fav button --}}
  @auth
    <a href="#" class="vs-fav-btn" title="Agregar a favoritos">♡</a>
  @endauth

  {{-- Hero content --}}
  <div class="vs-hero-content">
    <span class="vs-badge-verified" data-aos="fade-up" data-aos-delay="0">
      <span class="vs-badge-dot"></span>
      Complejo verificado
    </span>
    <h1 class="vs-hero-title" data-aos="fade-up" data-aos-delay="100">{{ $venue->name }}</h1>
    <div class="vs-hero-chips" data-aos="fade-up" data-aos-delay="200">
      @if($venue->zone)
        <span class="vs-hero-chip">📍 {{ $venue->zone }}</span>
      @endif
      @if($venue->address)
        <span class="vs-hero-chip">🏠 {{ $venue->address }}</span>
      @endif
      @if($venue->cancellation_hours)
        <span class="vs-hero-chip amber">⏱ Cancelaciones hasta {{ $venue->cancellation_hours }}h antes</span>
      @endif
    </div>
  </div>
</section>

{{-- PRESENTATION BLOCK --}}
<div class="vs-presentation-block" data-aos="fade-up">
  @if($venue->cover_image_path)
    <img class="vs-presentation-bg"
         src="{{ \Illuminate\Support\Facades\Storage::url($venue->cover_image_path) }}"
         alt="{{ $venue->name }}">
  @else
    <div class="vs-presentation-bg-placeholder"></div>
  @endif
  <div class="vs-presentation-overlay"></div>

  <div class="vs-presentation-content" data-aos="fade-right" data-aos-delay="100">
    <div class="vs-presentation-label">Bienvenido a</div>
    <h2 class="vs-presentation-title">{{ $venue->name }}</h2>
    <div class="vs-presentation-divider"></div>
  </div>

  <div class="vs-presentation-chips" data-aos="fade-left" data-aos-delay="200">
    @foreach($sports as $i => $sport)
      <span class="vs-presentation-chip" data-aos="fade-left" data-aos-delay="{{ 200 + $i * 60 }}">
        {{ match($sport) { 'football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley', default=>ucfirst($sport) } }}
      </span>
    @endforeach
    <span class="vs-presentation-chip" data-aos="fade-left" data-aos-delay="{{ 200 + $sports->count() * 60 }}">
      🏟️ {{ $activeFields->count() }} {{ $activeFields->count() === 1 ? 'cancha' : 'canchas' }}
    </span>
  </div>
</div>

{{-- GALLERY BLOCK --}}
<div class="vs-gallery-block" data-aos="fade-up">
  <h3 class="vs-gallery-title">El ambiente</h3>
  <div class="vs-gallery-grid">

    {{-- MAPA (celda izquierda tall) --}}
    <div class="vs-gallery-cell tall" style="overflow:hidden; cursor:default;">
      @if($venue->address)
        <iframe
          src="https://maps.google.com/maps?q={{ urlencode($venue->address) }}&z=16&output=embed"
          width="100%" height="100%" style="border:0;display:block;min-height:370px;" allowfullscreen loading="lazy">
        </iframe>
        {{-- Botones pegados abajo --}}
        <div style="position:absolute; bottom:0; left:0; right:0; padding:12px 14px; background:linear-gradient(to top, rgba(0,0,0,.75) 0%, transparent 100%); display:flex; gap:8px; flex-wrap:wrap; z-index:2;">
          <button onclick="openDirections()" style="display:inline-flex;align-items:center;gap:6px;background:#16a34a;color:#fff;border:none;border-radius:10px;padding:8px 14px;font-size:13px;font-weight:700;cursor:pointer;font-family:inherit;">
            🧭 Cómo llegar
          </button>
          <a href="https://maps.google.com/?q={{ urlencode($venue->address) }}" target="_blank" rel="noopener"
             style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);border-radius:10px;padding:8px 14px;font-size:13px;font-weight:700;text-decoration:none;backdrop-filter:blur(4px);">
            🗺 Ver en Maps
          </a>
        </div>
      @else
        <img src="/Images/amigos-post-futbol.webp" alt="Amigos después del partido" loading="lazy">
        <div class="vs-gallery-cell-overlay">
          <span class="vs-gallery-cell-label">La pasión del juego</span>
        </div>
      @endif
    </div>

    <div class="vs-gallery-cell">
      <img src="/Images/hero-cancha.webp" alt="Cancha de fútbol" loading="lazy">
      <div class="vs-gallery-cell-overlay">
        <span class="vs-gallery-cell-label">Canchas de primer nivel</span>
      </div>
    </div>
    <div class="vs-gallery-cell">
      <img src="/Images/dueno-complejo.webp" alt="El complejo" loading="lazy">
      <div class="vs-gallery-cell-overlay">
        <span class="vs-gallery-cell-label">Atención personalizada</span>
      </div>
    </div>
  </div>
</div>

{{-- STATS + DESCRIPTION --}}
<div class="vs-info-block" data-aos="fade-up">
  <div class="vs-stats-row">
    <div class="vs-stat" data-aos="zoom-in" data-aos-delay="0">
      <div class="vs-stat-label">Canchas</div>
      <div class="vs-stat-value"><span class="vs-stat-num" data-target="{{ $activeFields->count() }}">{{ $activeFields->count() }}</span></div>
      <div class="vs-stat-sub">disponibles</div>
    </div>
    @if($venue->reviews->count() > 0)
    <div class="vs-stat" data-aos="zoom-in" data-aos-delay="100">
      <div class="vs-stat-label">Calificacion</div>
      <div class="vs-stat-value"><span class="vs-stat-num" data-target="{{ $averageRating }}" data-decimal="1">{{ $averageRating }}</span></div>
      <div class="vs-stat-sub">{{ $venue->reviews->count() }} reseñas</div>
    </div>
    @endif
    @if(!empty($venueAmenities))
    <div class="vs-stat" data-aos="zoom-in" data-aos-delay="200">
      <div class="vs-stat-label">Servicios</div>
      <div class="vs-stat-value"><span class="vs-stat-num" data-target="{{ count($venueAmenities) }}">{{ count($venueAmenities) }}</span></div>
      <div class="vs-stat-sub">instalaciones</div>
    </div>
    @endif
  </div>

  @if($venue->description)
    <p class="vs-description" data-aos="fade-up" data-aos-delay="80">{{ $venue->description }}</p>
  @endif

  @if(!empty($venueAmenities))
    <div class="vs-amenities-grid" data-aos="fade-up" data-aos-delay="120">
      @foreach($venueAmenities as $key)
        @if(isset($allAmenities[$key]))
          <span class="vs-amenity-pill">{{ $allAmenities[$key]['emoji'] }} {{ $allAmenities[$key]['label'] }}</span>
        @endif
      @endforeach
    </div>
  @endif
</div>

{{-- FIELDS --}}
<div id="canchas" class="vs-fields-block" data-aos="fade-up">
  <h2 class="vs-section-title">Canchas del complejo</h2>
  <p class="vs-section-subtitle">Elegí la cancha y revisá la disponibilidad en tiempo real</p>

  @if($activeFields->isEmpty())
    <div style="text-align:center; padding:40px 0;">
      <div style="font-size:36px; margin-bottom:10px;">🏟️</div>
      <p style="margin:0; color:#9ca3af; font-size:15px;">Este complejo todavía no tiene canchas cargadas.</p>
    </div>
  @else
    @if($sports->count() > 1)
      <div class="vs-filters">
        <button onclick="vsFilterSport(null)" class="vs-filter-btn active" data-sport="">Todos</button>
        @foreach($sports as $sport)
          <button onclick="vsFilterSport('{{ $sport }}')" class="vs-filter-btn" data-sport="{{ $sport }}">
            {{ match($sport) { 'football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley', default=>ucfirst($sport) } }}
          </button>
        @endforeach
      </div>
    @endif

    <div class="vs-fields-grid">
      @foreach($activeFields as $i => $field)
        <article class="vs-field-card"
                 data-sport="{{ $field->sport }}"
                 data-aos="fade-up"
                 data-aos-delay="{{ $i * 80 }}">
          <div class="vs-field-card-img-wrap">
            @if($field->cover_image_path)
              <img class="vs-field-card-img"
                   src="{{ \Illuminate\Support\Facades\Storage::url($field->cover_image_path) }}"
                   alt="{{ $field->name }}">
            @else
              <div class="vs-field-card-img-placeholder">
                {{ match($field->sport ?? '') { 'padel'=>'🏓','tennis'=>'🎾','basketball'=>'🏀','volleyball'=>'🏐', default=>'⚽' } }}
              </div>
            @endif
          </div>

          <div class="vs-field-card-body">
            <h3 class="vs-field-card-name">{{ $field->name }}</h3>

            <div class="vs-field-badges">
              <span class="vs-field-badge sport">{{ match($field->sport ?? '') { 'football'=>'⚽ Fútbol','padel'=>'🏓 Pádel','tennis'=>'🎾 Tenis','basketball'=>'🏀 Básquet','volleyball'=>'🏐 Vóley', default=>ucfirst($field->sport ?? 'Cancha') } }}</span>
              <span class="vs-field-badge format">{{ $field->format ?? '?' }}v{{ $field->format ?? '?' }}</span>
              <span class="vs-field-badge time">{{ $field->slot_minutes }} min</span>
            </div>

            @if($field->faltaUnoSetting?->enabled)
              <div class="vs-falta-uno-badge">
                <span class="vs-badge-dot"></span>
                Falta Uno activo
              </div>
            @endif

            <div class="vs-field-price">
              {{ $field->price->currency ?? 'ARS' }} {{ number_format($field->price->price_per_slot ?? 0, 0, ',', '.') }}
              <span>/ turno</span>
            </div>

            <a href="{{ route('fields.show', $field) }}" class="vs-btn-green">
              Ver disponibilidad →
            </a>

            @if($field->faltaUnoSetting?->enabled)
              <a href="{{ route('falta-uno.create', $field) }}" class="vs-btn-ghost-green">
                ⚡ Iniciar partido
              </a>
            @endif
          </div>
        </article>
      @endforeach
    </div>
  @endif
</div>

{{-- VIBE SECTION --}}
<section class="vs-vibe-section" data-aos="fade-up">
  <img class="vs-vibe-bg" src="/Images/ambiente-cancha-noche.webp" alt="Ambiente nocturno" loading="lazy">
  <div class="vs-vibe-overlay"></div>
  <div class="vs-vibe-content">
    <div class="vs-vibe-label" data-aos="fade-right" data-aos-delay="0">La experiencia</div>
    <h2 class="vs-vibe-title" data-aos="fade-right" data-aos-delay="80">Más que una cancha</h2>
    <p class="vs-vibe-text" data-aos="fade-up" data-aos-delay="200">Un lugar donde cada turno se convierte en un partido memorable.</p>
    <a href="#canchas" class="vs-vibe-btn" data-aos="fade-up" data-aos-delay="300">Reservar ahora →</a>
  </div>
</section>

{{-- REVIEWS --}}
<div class="vs-reviews-block" data-aos="fade-up">
  <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
    <div data-aos="fade-right">
      <h2 style="margin:0 0 8px 0; font-size:22px; font-weight:800; color:#111827;">Reseñas</h2>
      <div style="display:flex; align-items:center; gap:10px;">
        <span class="vs-reviews-avg">{{ $averageRating }}</span>
        <div>
          <div style="color:#f59e0b; font-size:20px; line-height:1;">
            @for($i = 1; $i <= 5; $i++){{ $i <= $roundedAverage ? '★' : '☆' }}@endfor
          </div>
          <div style="font-size:13px; color:#9ca3af; margin-top:2px;">{{ $venue->reviews->count() }} reseñas</div>
        </div>
      </div>
    </div>
    @auth
      <button type="button" class="vs-btn-primary-sm" onclick="vsToggleReviewForm()">Escribir reseña</button>
    @endauth
  </div>

  @auth
    <div id="vsReviewFormWrap" style="display:none; margin-bottom:24px;">
      <div class="vs-review-form-wrap">
        <form method="POST" action="{{ route('venues.reviews.store', $venue) }}" style="display:grid; gap:14px; max-width:560px;">
          @csrf
          <div>
            <label class="vs-form-label">Puntuacion</label>
            <input type="hidden" name="rating" id="vsRatingInput" value="{{ old('rating', '') }}" required>
            <div class="review-stars-picker" id="vsReviewStars">
              @for($i = 1; $i <= 5; $i++)
                <button type="button" data-value="{{ $i }}" aria-label="Puntuar con {{ $i }} estrella{{ $i > 1 ? 's' : '' }}">★</button>
              @endfor
            </div>
            <div id="vsRatingText" class="review-rating-text" style="color:#9ca3af; font-size:13px; margin-top:6px;">Selecciona una puntuacion</div>
            @error('rating')<div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="vs-form-label">Comentario</label>
            <textarea name="comment" rows="3" class="vs-review-textarea">{{ old('comment', '') }}</textarea>
            @error('comment')<div style="color:#dc2626; font-size:13px; margin-top:6px;">{{ $message }}</div>@enderror
          </div>
          <div style="display:flex; gap:10px;">
            <button type="submit" class="vs-btn-primary-sm">Publicar reseña</button>
            <button type="button" class="vs-btn-ghost-sm" onclick="vsToggleReviewForm(false)">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  @endauth

  <div style="display:grid; gap:12px;">
    @forelse($venue->reviews->sortByDesc('created_at') as $review)
      <div class="vs-review-item" data-aos="fade-up" data-aos-delay="{{ $loop->index * 60 }}">
        <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:10px;">
          <div style="display:flex; align-items:center; gap:10px;">
            @if($review->user->avatar_path)
              <img src="{{ \Illuminate\Support\Facades\Storage::url($review->user->avatar_path) }}"
                   alt="{{ $review->user->name }}"
                   class="vs-review-avatar">
            @else
              <div class="vs-review-avatar-placeholder">👤</div>
            @endif
            <div>
              <div style="font-weight:700; font-size:14px; color:#111827;">{{ $review->user->name }}</div>
              <div style="font-size:12px; color:#9ca3af;">{{ $review->created_at->format('d/m/Y') }}</div>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:5px;">
            <span style="color:#f59e0b; font-size:14px;">
              @for($i = 1; $i <= 5; $i++){{ $i <= $review->rating ? '★' : '☆' }}@endfor
            </span>
            <span style="font-size:13px; font-weight:700; color:#6b7280;">{{ $review->rating }}/5</span>
          </div>
        </div>
        @if($review->comment)
          <p style="margin:0; font-size:14px; color:#4b5563; line-height:1.6;">{{ $review->comment }}</p>
        @endif
      </div>
    @empty
      <div style="text-align:center; padding:32px 0; color:#9ca3af;" data-aos="fade-up">
        <div style="font-size:36px; margin-bottom:8px;">💬</div>
        <p style="margin:0; font-size:14px;">Todavia no hay reseñas.</p>
      </div>
    @endforelse
  </div>
</div>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init({ duration: 600, once: true, easing: 'ease-out-cubic' });

  // Cómo llegar — abre Google Maps en modo indicaciones con geolocalización
  function openDirections() {
    const dest = encodeURIComponent('{{ addslashes($venue->address ?? '') }}');
    if (!dest) return;
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(pos) {
          const origin = pos.coords.latitude + ',' + pos.coords.longitude;
          window.open('https://www.google.com/maps/dir/?api=1&origin=' + origin + '&destination=' + dest + '&travelmode=driving', '_blank');
        },
        function() {
          window.open('https://www.google.com/maps/dir/?api=1&destination=' + dest + '&travelmode=driving', '_blank');
        }
      );
    } else {
      window.open('https://www.google.com/maps/dir/?api=1&destination=' + dest + '&travelmode=driving', '_blank');
    }
  }

  // Scroll progress bar
  (function () {
    const bar = document.getElementById('vsProgress');
    if (!bar) return;
    window.addEventListener('scroll', function () {
      const scrollTop = window.scrollY;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      bar.style.width = (docHeight > 0 ? (scrollTop / docHeight) * 100 : 0) + '%';
    }, { passive: true });
  })();

  // Stat counter animation
  (function () {
    const counters = document.querySelectorAll('.vs-stat-num');
    if (!counters.length) return;
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const target = parseFloat(el.dataset.target || '0');
        const isDecimal = !!el.dataset.decimal;
        const duration = 900;
        const start = performance.now();
        function step(now) {
          const elapsed = now - start;
          const progress = Math.min(elapsed / duration, 1);
          const ease = 1 - Math.pow(1 - progress, 3);
          const value = target * ease;
          el.textContent = isDecimal ? value.toFixed(1) : Math.floor(value);
          if (progress < 1) requestAnimationFrame(step);
          else el.textContent = isDecimal ? target.toFixed(1) : target;
        }
        requestAnimationFrame(step);
        observer.unobserve(el);
      });
    }, { threshold: 0.5 });
    counters.forEach(c => observer.observe(c));
  })();

  // Sport filter
  function vsFilterSport(sport) {
    document.querySelectorAll('.vs-field-card[data-sport]').forEach(c => {
      c.style.display = (!sport || c.dataset.sport === sport) ? '' : 'none';
    });
    document.querySelectorAll('.vs-filter-btn').forEach(b => {
      b.classList.toggle('active', b.dataset.sport === (sport ?? ''));
    });
  }

  // Review form toggle
  function vsToggleReviewForm(forceState = null) {
    const wrap = document.getElementById('vsReviewFormWrap');
    if (!wrap) return;
    if (forceState === false) { wrap.style.display = 'none'; return; }
    wrap.style.display = wrap.style.display === 'none' || wrap.style.display === '' ? 'block' : 'none';
  }

  // Star picker
  (function () {
    const input = document.getElementById('vsRatingInput');
    const starsWrap = document.getElementById('vsReviewStars');
    const ratingText = document.getElementById('vsRatingText');
    if (!input || !starsWrap || !ratingText) return;
    const buttons = Array.from(starsWrap.querySelectorAll('button'));
    function getLabel(v) { return v ? v + ' estrella' + (v > 1 ? 's' : '') : 'Selecciona una puntuacion'; }
    function paint(v) { buttons.forEach((b, i) => b.classList.toggle('active', i < v)); ratingText.innerText = getLabel(v); }
    buttons.forEach(b => b.addEventListener('click', () => { input.value = Number(b.dataset.value); paint(Number(b.dataset.value)); }));
    paint(Number(input.value || 0));
    @if($errors->has('rating') || $errors->has('comment'))
      document.getElementById('vsReviewFormWrap').style.display = 'block';
    @endif
  })();
</script>
@endpush

@endsection
