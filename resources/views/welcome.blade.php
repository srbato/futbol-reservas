@extends('layouts.marketing')

@section('title', 'TuCancha — Reservá, jugá, encontrá jugadores')
@section('meta_description', 'Reservá canchas de fútbol, tenis, pádel y más en Argentina. Encontrá el complejo más cercano, elegí el horario y confirmá tu turno online al instante.')

@push('styles')
/* ── Smooth scroll global ────────────────────────── */
html { scroll-behavior: smooth; }

/* ── Hero full-height ─────────────────────────────── */
.hero-full {
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  overflow: hidden;
  background: #0a0a0a;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background-image: url('/images/hero-cancha-800w.webp');
  background-size: cover;
  background-position: center 30%;
  background-attachment: fixed;
  opacity: .55;
}
@media (min-width: 801px) {
  .hero-bg { background-image: url('/images/hero-cancha-1200w.webp'); }
}
@media (min-width: 1201px) {
  .hero-bg { background-image: url('/images/hero-cancha.webp'); }
}

/* ── Parallax floating blobs ─────────────────────── */
.parallax-blob {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  will-change: transform;
  z-index: 1;
}

.parallax-blob-1 {
  width: 300px;
  height: 300px;
  background: rgba(34,197,94,.08);
  top: -60px;
  right: -80px;
}

.parallax-blob-2 {
  width: 200px;
  height: 200px;
  background: rgba(34,197,94,.12);
  bottom: 80px;
  left: -50px;
}

.parallax-blob-3 {
  width: 150px;
  height: 150px;
  background: rgba(34,197,94,.08);
  top: 40%;
  left: 45%;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(0,0,0,.82) 0%, rgba(0,0,0,.55) 60%, rgba(0,0,0,.45) 100%);
}

.hero-content {
  position: relative;
  z-index: 2;
  width: 100%;
  padding: 100px 0 72px 0;
}

.hero-grid {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 56px;
  align-items: center;
}

/* Badge pulsante */
.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 16px 6px 8px;
  border-radius: var(--radius-full);
  background: rgba(110, 234, 160, .12);
  border: 1px solid rgba(110, 234, 160, .25);
  font-size: 13px;
  font-weight: 700;
  color: var(--color-primary-light);
  margin-bottom: 28px;
  letter-spacing: .02em;
}

.hero-badge-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: var(--color-primary-light);
  animation: pulse-dot 1.8s ease-in-out infinite;
}

@keyframes pulse-dot {
  0%, 100% { transform: scale(1); opacity: 1; box-shadow: 0 0 0 0 rgba(110,234,160,.4); }
  50% { transform: scale(1.2); opacity: .9; box-shadow: 0 0 0 6px rgba(110,234,160,0); }
}

.hero-copy h1 {
  margin: 0 0 20px 0;
  font-size: 68px;
  line-height: 1.0;
  letter-spacing: -0.035em;
  color: var(--color-text-inverse);
  font-weight: 900;
}

.hero-copy h1 em {
  font-style: normal;
  color: var(--color-primary-light);
}

.hero-copy > p {
  margin: 0 0 36px 0;
  color: rgba(255,255,255,.78);
  font-size: 19px;
  line-height: 1.65;
  max-width: 560px;
}

.hero-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 52px;
}

/* Micro stats */
.hero-stats {
  display: flex;
  gap: 40px;
  flex-wrap: wrap;
}

.hero-stat-item {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.hero-stat-num {
  font-size: 28px;
  font-weight: 900;
  color: var(--color-text-inverse);
  letter-spacing: -0.03em;
  line-height: 1;
}

.hero-stat-label {
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,.5);
  text-transform: uppercase;
  letter-spacing: .07em;
}

/* Search card */
.search-card {
  background: rgba(255,255,255,.97);
  border-radius: 24px;
  padding: 28px 28px 24px 28px;
  box-shadow: 0 20px 60px rgba(0,0,0,.35), 0 4px 16px rgba(0,0,0,.15);
}

.search-card-title {
  font-size: 15px;
  font-weight: 800;
  color: var(--color-text);
  margin: 0 0 20px 0;
  letter-spacing: -0.01em;
}

.search-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 14px;
}

.search-field label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .07em;
  color: #888;
}

.search-field select,
.search-field input {
  padding: 12px 14px;
  border: 1.5px solid var(--color-border);
  border-radius: 12px;
  font: inherit;
  font-size: 14px;
  background: #fafafa;
  color: var(--color-text);
  outline: none;
  transition: border-color .15s, background .15s;
  width: 100%;
}

.search-field select:focus,
.search-field input:focus {
  border-color: var(--color-primary);
  background: var(--color-bg);
}

.search-btn-full {
  display: block;
  width: 100%;
  padding: 14px;
  border-radius: 14px;
  background: var(--color-bg-dark);
  color: var(--color-text-inverse);
  font-size: 15px;
  font-weight: 800;
  border: none;
  cursor: pointer;
  transition: background .15s, transform .15s;
  font-family: inherit;
  letter-spacing: -0.01em;
  margin-top: 6px;
}

.search-btn-full:hover { background: #1a7a45; transform: translateY(-1px); }

/* ── Sports strip ────────────────────────────────── */
.sports-strip {
  background: var(--color-bg);
  border-top: 1px solid var(--color-border);
  border-bottom: 1px solid var(--color-border);
  padding: 28px 0;
}

.sports-strip-inner {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
}

.sport-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border-radius: var(--radius-full);
  border: 1.5px solid var(--color-border);
  font-size: 14px;
  font-weight: 700;
  color: #333;
  background: var(--color-bg);
  transition: border-color .15s, color .15s, background .15s, transform .15s;
  text-decoration: none;
}

.sport-pill:hover {
  border-color: var(--color-primary);
  color: #15803d;
  background: #f0fdf4;
  transform: translateY(-2px);
}

.sport-pill-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  line-height: 1;
}

/* ── Why TuCancha ────────────────────────────────── */
.why-section {
  padding: 80px 0;
  background: var(--color-bg-page);
}

.why-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

.why-card {
  background: var(--color-bg-card);
  border: 1px solid #d9d9d9;
  border-left: 4px solid var(--color-primary);
  border-radius: 20px;
  padding: 28px 26px;
  box-shadow: 0 2px 12px rgba(0,0,0,.05);
  transition: transform .2s, box-shadow .2s, border-left-color .2s;
  position: relative;
  overflow: hidden;
}

.why-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 36px rgba(34,197,94,.1), 0 4px 16px rgba(0,0,0,.06);
  border-left-color: var(--color-primary-hover);
}

.why-card-num {
  position: absolute;
  top: 18px;
  right: 22px;
  font-size: 52px;
  font-weight: 900;
  color: rgba(0,0,0,.04);
  line-height: 1;
  letter-spacing: -0.04em;
  user-select: none;
  pointer-events: none;
}

.why-icon {
  display: block;
  margin-bottom: 16px;
  line-height: 1;
  width: 28px;
  height: 28px;
}

.why-icon [data-lucide] {
  width: 28px;
  height: 28px;
  stroke: var(--color-primary);
  stroke-width: 2;
  fill: none;
}

.why-card h3 {
  margin: 0 0 8px 0;
  font-size: 17px;
  font-weight: 800;
  letter-spacing: -0.01em;
}

.why-card p {
  margin: 0;
  color: var(--color-text-secondary);
  font-size: 14px;
  line-height: 1.65;
}

/* ── Split section (50/50) ───────────────────────── */
.split-section {
  padding: 80px 0;
  background: var(--color-bg);
}

.split-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 64px;
  align-items: center;
}

.split-image-wrap {
  border-radius: 28px;
  overflow: hidden;
  position: relative;
  aspect-ratio: 4/3;
  box-shadow: 0 24px 64px rgba(0,0,0,.14);
}

.split-image-wrap img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.split-image-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(15,76,42,.15) 0%, transparent 60%);
}

.split-copy-label {
  display: inline-block;
  padding: 4px 12px;
  border-radius: var(--radius-full);
  background: #dcfce7;
  font-size: 12px;
  font-weight: 700;
  color: #166534;
  text-transform: uppercase;
  letter-spacing: .07em;
  margin-bottom: 18px;
}

.split-copy h2 {
  font-size: 40px;
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.05;
  margin: 0 0 18px 0;
  color: var(--color-text);
}

.split-copy h2 em {
  font-style: normal;
  color: var(--color-primary-hover);
}

.split-copy > p {
  color: #555;
  font-size: 16px;
  line-height: 1.7;
  margin: 0 0 28px 0;
}

.split-features {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.split-feature {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.split-feature-icon {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  background: #dcfce7;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
}

.split-feature-icon [data-lucide] {
  width: 16px;
  height: 16px;
  stroke: var(--color-primary-hover);
  stroke-width: 2.2;
  fill: none;
}

.split-feature-text {
  font-size: 14px;
  color: #444;
  line-height: 1.55;
  font-weight: 500;
}

/* ── Owner section ───────────────────────────────── */
.owner-section {
  position: relative;
  padding: 100px 0;
  overflow: hidden;
  background: #0a0a0a;
}

.owner-bg {
  position: absolute;
  inset: 0;
  background-image: url('/images/admin-viendo-tableta.webp');
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  opacity: .40;
}

.owner-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(10,61,31,.92) 0%, rgba(0,0,0,.80) 100%);
}

.owner-content {
  position: relative;
  z-index: 2;
}

.owner-grid {
  display: grid;
  grid-template-columns: 1fr auto;
  gap: 48px;
  align-items: center;
}

.owner-tag {
  display: inline-block;
  padding: 5px 14px;
  border-radius: var(--radius-full);
  background: rgba(110, 234, 160, .15);
  border: 1px solid rgba(110, 234, 160, .25);
  font-size: 12px;
  font-weight: 700;
  color: var(--color-primary-light);
  text-transform: uppercase;
  letter-spacing: .07em;
  margin-bottom: 18px;
}

.owner-content h2 {
  font-size: 44px;
  font-weight: 900;
  letter-spacing: -0.03em;
  line-height: 1.05;
  color: var(--color-text-inverse);
  margin: 0 0 16px 0;
  max-width: 620px;
}

.owner-content h2 em {
  font-style: normal;
  color: var(--color-primary-light);
}

.owner-copy p {
  color: rgba(255,255,255,.85);
  font-size: 17px;
  line-height: 1.65;
  margin: 0 0 32px 0;
  max-width: 560px;
}

.owner-stats {
  display: flex;
  gap: 32px;
  margin-bottom: 36px;
  flex-wrap: wrap;
}

.owner-stat {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.owner-stat-num {
  font-size: 30px;
  font-weight: 900;
  color: var(--color-primary-light);
  letter-spacing: -0.03em;
  line-height: 1;
}

.owner-stat-label {
  font-size: 12px;
  font-weight: 600;
  color: rgba(255,255,255,.5);
  text-transform: uppercase;
  letter-spacing: .07em;
}

.owner-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.btn-owner-primary {
  padding: 14px 32px;
  background: var(--color-primary-light);
  color: #0a3d1f;
  border-radius: 14px;
  font-size: 15px;
  font-weight: 800;
  text-decoration: none;
  transition: background .15s, transform .15s;
  border: none;
  cursor: pointer;
  display: inline-block;
}

.btn-owner-primary:hover { background: #4dd882; transform: translateY(-2px); }

.btn-owner-ghost {
  padding: 14px 24px;
  background: rgba(255,255,255,.1);
  color: rgba(255,255,255,.9) !important;
  border-radius: 14px;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  border: 1px solid rgba(255,255,255,.2);
  transition: background .15s, transform .15s;
  display: inline-block;
}

.btn-owner-ghost:hover { background: rgba(255,255,255,.18); transform: translateY(-2px); }

.owner-visual-card {
  background: rgba(255,255,255,.08);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 24px;
  padding: 28px 26px;
  backdrop-filter: blur(8px);
  min-width: 240px;
  text-align: center;
}

.owner-visual-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
  width: 100%;
}

.owner-visual-icon [data-lucide] {
  width: 40px;
  height: 40px;
  stroke: var(--color-primary);
  stroke-width: 1.8;
  fill: none;
}

.owner-visual-card p {
  margin: 0;
  color: rgba(255,255,255,.8);
  font-size: 14px;
  line-height: 1.6;
  font-weight: 600;
}

/* ── FAQ ─────────────────────────────────────────── */
.faq-section {
  padding: 100px 0;
  background: var(--color-bg);
}

.faq-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 72px;
  align-items: start;
}

/* imagen lado izquierdo */
.faq-visual {
  position: sticky;
  top: 100px;
}

.faq-visual-inner {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 24px 64px rgba(0,0,0,.14);
}

.faq-visual-inner img {
  width: 100%;
  height: 520px;
  object-fit: cover;
  display: block;
  transition: transform .6s ease;
}

.faq-visual-inner:hover img {
  transform: scale(1.04);
}

.faq-visual-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(160deg, rgba(0,0,0,.08) 0%, rgba(0,0,0,.45) 100%);
}

.faq-visual-badge {
  position: absolute;
  bottom: 28px;
  left: 28px;
  background: var(--color-primary);
  color: var(--color-text-inverse);
  font-size: 13px;
  font-weight: 700;
  padding: 10px 18px;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 8px;
  box-shadow: 0 4px 20px rgba(34,197,94,.45);
  letter-spacing: .3px;
}

.faq-visual-badge svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

.faq-visual-accent {
  position: absolute;
  top: 24px;
  right: 24px;
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: rgba(34,197,94,.18);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(34,197,94,.35);
  display: flex;
  align-items: center;
  justify-content: center;
}

.faq-visual-accent [data-lucide] {
  width: 28px;
  height: 28px;
  stroke: var(--color-primary);
  stroke-width: 2;
  fill: none;
}

/* acordeon lado derecho */
.faq-col-header {
  margin-bottom: 36px;
}

.faq-col-header .section-label {
  display: inline-flex;
  margin-bottom: 12px;
}

.faq-col-header h2 {
  font-size: 36px;
  font-weight: 800;
  color: #0a0a0a;
  line-height: 1.2;
  margin: 0 0 14px 0;
}

.faq-col-header p {
  color: #6b7280;
  font-size: 16px;
  line-height: 1.65;
  margin: 0;
}

.faq-list { display: flex; flex-direction: column; gap: 8px; }

.faq-item {
  background: var(--color-bg-card);
  border: 1.5px solid var(--color-border);
  border-radius: 16px;
  overflow: hidden;
  transition: box-shadow .25s, border-color .25s, transform .25s;
}

.faq-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 28px rgba(0,0,0,.07);
}

.faq-item.open {
  box-shadow: 0 8px 32px rgba(34,197,94,.12);
  border-color: #86efac;
  transform: translateY(-2px);
}

.faq-trigger {
  width: 100%;
  background: none;
  border: none;
  padding: 20px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  cursor: pointer;
  text-align: left;
  font: inherit;
}

.faq-trigger-text {
  font-size: 15px;
  font-weight: 700;
  color: var(--color-text);
  line-height: 1.4;
}

.faq-item.open .faq-trigger-text {
  color: var(--color-primary-hover);
}

.faq-icon {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  background: var(--color-bg-page);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: transform .3s cubic-bezier(.34,1.56,.64,1), background .25s, color .25s;
}

.faq-icon svg {
  width: 14px;
  height: 14px;
  stroke: #6b7280;
  transition: stroke .25s;
}

.faq-item.open .faq-icon {
  transform: rotate(45deg);
  background: var(--color-primary);
}

.faq-item.open .faq-icon svg {
  stroke: var(--color-text-inverse);
}

.faq-body {
  max-height: 0;
  overflow: hidden;
  transition: max-height .4s cubic-bezier(.4,0,.2,1), padding .3s ease;
  padding: 0 22px;
  color: #4b5563;
  font-size: 15px;
  line-height: 1.75;
  border-top: 0px solid #f0fdf4;
}

.faq-item.open .faq-body {
  max-height: 300px;
  padding: 14px 22px 22px 22px;
  border-top-width: 1px;
}

.faq-number {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  border-radius: 6px;
  background: #f0fdf4;
  color: var(--color-primary-hover);
  font-size: 11px;
  font-weight: 800;
  flex-shrink: 0;
  border: 1px solid #bbf7d0;
  margin-right: 4px;
}

/* ── Responsive ──────────────────────────────────── */
@media (max-width: 1100px) {
  .hero-grid { grid-template-columns: 1fr 380px; gap: 40px; }
  .hero-copy h1 { font-size: 52px; }
  .why-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 900px) {
  .hero-grid { grid-template-columns: 1fr; }
  .search-card { max-width: 480px; }
  .hero-copy h1 { font-size: 46px; }
  .split-grid { grid-template-columns: 1fr; gap: 40px; }
  .owner-grid { grid-template-columns: 1fr; }
  .owner-visual-card { display: none; }
  .owner-content h2 { font-size: 34px; }
}

@media (max-width: 640px) {
  .hero-content { padding: 80px 0 56px 0; }
  .hero-copy h1 { font-size: 36px; }
  .hero-copy > p { font-size: 16px; }
  .hero-stats { gap: 24px; }
  .why-grid { grid-template-columns: 1fr; }
  .split-copy h2 { font-size: 30px; }
  .owner-content h2 { font-size: 28px; }
  .owner-section { padding: 72px 0; }
  .faq-section { padding: 56px 0; }
  .faq-layout { grid-template-columns: 1fr; gap: 40px; }
  .faq-visual { display: none; }
  .faq-col-header h2 { font-size: 28px; }
  .why-section { padding: 56px 0; }
  .split-section { padding: 56px 0; }
}

/* ── Desactivar parallax en mobile (iOS bug) ─────── */
@media (max-width: 768px) {
  .hero-bg  { background-attachment: scroll; }
  .owner-bg { background-attachment: scroll; }
  .parallax-blob { display: none; }
}

/* ── Falta Uno section ───────────────────────────── */
.faltauno-section {
  position: relative;
  padding: 100px 0;
  overflow: hidden;
  background: #0a0a0a;
}

.faltauno-bg {
  position: absolute;
  inset: 0;
  background-image: url('/images/jugadores-falta-uno.webp');
  background-size: cover;
  background-position: center 40%;
  background-attachment: fixed;
  opacity: .38;
}

.faltauno-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(160deg, rgba(5,32,16,.88) 0%, rgba(0,0,0,.82) 55%, rgba(5,32,16,.75) 100%);
}

.faltauno-content {
  position: relative;
  z-index: 2;
}

.faltauno-head {
  text-align: center;
  margin-bottom: 52px;
}

.faltauno-tag {
  display: inline-block;
  padding: 5px 14px;
  border-radius: var(--radius-full);
  background: rgba(110, 234, 160, .15);
  border: 1px solid rgba(110, 234, 160, .25);
  font-size: 12px;
  font-weight: 700;
  color: var(--color-primary-light);
  text-transform: uppercase;
  letter-spacing: .07em;
  margin-bottom: 18px;
}

.faltauno-head h2 {
  font-size: 46px;
  font-weight: 900;
  letter-spacing: -0.035em;
  line-height: 1.06;
  color: var(--color-text-inverse);
  margin: 0 0 16px 0;
}

.faltauno-head h2 em {
  font-style: normal;
  color: var(--color-primary-light);
}

.faltauno-head p {
  color: rgba(255,255,255,.72);
  font-size: 17px;
  line-height: 1.65;
  max-width: 560px;
  margin: 0 auto;
}

.faltauno-cards {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 24px;
  max-width: 840px;
  margin: 0 auto 48px auto;
}

.faltauno-card {
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 24px;
  padding: 36px 32px;
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  transition: background .2s, border-color .2s, transform .2s;
}

.faltauno-card:hover {
  background: rgba(110,234,160,.08);
  border-color: rgba(110,234,160,.28);
  transform: translateY(-4px);
}

.faltauno-card-icon {
  display: block;
  margin-bottom: 20px;
  line-height: 1;
  width: 32px;
  height: 32px;
}

.faltauno-card-icon [data-lucide] {
  width: 32px;
  height: 32px;
  stroke: var(--color-primary-light);
  stroke-width: 1.8;
  fill: none;
}

.faltauno-card h3 {
  font-size: 22px;
  font-weight: 900;
  color: var(--color-text-inverse);
  letter-spacing: -0.02em;
  margin: 0 0 12px 0;
}

.faltauno-card p {
  font-size: 14px;
  color: rgba(255,255,255,.68);
  line-height: 1.65;
  margin: 0 0 24px 0;
}

.faltauno-card-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.faltauno-card-list-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 13px;
  font-weight: 600;
  color: rgba(255,255,255,.8);
}

.faltauno-card-list-item::before {
  content: '';
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--color-primary-light);
  flex-shrink: 0;
}

.faltauno-cta {
  text-align: center;
}

.btn-faltauno {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 16px 40px;
  background: var(--color-primary-light);
  color: #0a3d1f;
  border-radius: 16px;
  font-size: 16px;
  font-weight: 800;
  text-decoration: none;
  letter-spacing: -0.01em;
  transition: background .15s, transform .15s, box-shadow .15s;
  box-shadow: 0 8px 32px rgba(110,234,160,.25);
}

.btn-faltauno:hover {
  background: #4dd882;
  transform: translateY(-2px);
  box-shadow: 0 12px 40px rgba(110,234,160,.35);
}

.faltauno-cta-sub {
  margin-top: 12px;
  font-size: 13px;
  color: rgba(255,255,255,.45);
  font-weight: 500;
}

@media (max-width: 900px) {
  .faltauno-cards { grid-template-columns: 1fr; max-width: 480px; }
  .faltauno-head h2 { font-size: 34px; }
  .faltauno-bg { background-attachment: scroll; }
}

@media (max-width: 640px) {
  .faltauno-section { padding: 72px 0; }
  .faltauno-head h2 { font-size: 28px; }
  .faltauno-head p { font-size: 15px; }
  .faltauno-card { padding: 28px 24px; }
}
@endpush

@section('content')

{{-- ── Hero full-height ──────────────────────────────────────────── --}}
<section class="hero-full">
  <div class="hero-bg"></div>
  <div class="hero-overlay"></div>
  {{-- Blobs decorativos con parallax JS --}}
  <div class="parallax-blob parallax-blob-1 parallax-slow"></div>
  <div class="parallax-blob parallax-blob-2 parallax-fast"></div>
  <div class="parallax-blob parallax-blob-3 parallax-slow"></div>
  <div class="hero-content">
    <div class="container">
      <div class="hero-grid">

        {{-- Copy izquierda --}}
        <div class="hero-copy" data-aos="fade-right" data-aos-duration="900">
          <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Plataforma de reservas deportivas
          </div>

          <h1>Reservá, jugá,<br><em>encontrá jugadores.</em></h1>

          <p>
            Canchas al instante, jugadores que se suman solos, y cero comisiones.
            Tu vida deportiva en un solo lugar.
          </p>

          <div class="hero-actions">
            <a href="{{ route('venues.index') }}" class="btn btn-primary">Ver complejos</a>
            <a href="{{ route('planes') }}" class="btn btn-ghost">Sumá tu complejo</a>
          </div>

          <div class="hero-stats">
            <div class="hero-stat-item">
              <span class="hero-stat-num">24/7</span>
              <span class="hero-stat-label">Disponible</span>
            </div>
            <div class="hero-stat-item">
              <span class="hero-stat-num">+10</span>
              <span class="hero-stat-label">Deportes</span>
            </div>
            <div class="hero-stat-item">
              <span class="hero-stat-num">100%</span>
              <span class="hero-stat-label">Online</span>
            </div>
          </div>
        </div>

        {{-- Search card derecha --}}
        <div data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
          <div class="search-card">
            <p class="search-card-title">Busca una cancha ahora</p>

            <form method="GET" action="{{ route('venues.index') }}">
              <div class="search-field">
                <label for="qs-deporte">Deporte</label>
                <select id="qs-deporte" name="deporte">
                  <option value="">Cualquier deporte</option>
                  <option value="futbol">Futbol</option>
                  <option value="padel">Padel</option>
                  <option value="tenis">Tenis</option>
                  <option value="basquet">Basquet</option>
                </select>
              </div>

              <div class="search-field">
                <label for="qs-fecha">Fecha</label>
                <input
                  type="date"
                  id="qs-fecha"
                  name="fecha"
                  value="{{ now()->toDateString() }}"
                  min="{{ now()->toDateString() }}"
                >
              </div>

              <button type="submit" class="search-btn-full">Buscar canchas &rarr;</button>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

{{-- ── Franja de deportes ───────────────────────────────────────── --}}
<section class="sports-strip" data-aos="fade-up">
  <div class="container">
    <div class="sports-strip-inner">
      <a href="{{ route('venues.index', ['deporte' => 'futbol']) }}" class="sport-pill">
        <span class="sport-pill-icon"><svg width="8" height="8"><circle cx="4" cy="4" r="4" fill="#22c55e"/></svg></span> Futbol
      </a>
      <a href="{{ route('venues.index', ['deporte' => 'futbol5']) }}" class="sport-pill">
        <span class="sport-pill-icon"><svg width="8" height="8"><circle cx="4" cy="4" r="4" fill="#22c55e"/></svg></span> Futbol 5
      </a>
      <a href="{{ route('venues.index', ['deporte' => 'padel']) }}" class="sport-pill">
        <span class="sport-pill-icon"><svg width="8" height="8"><circle cx="4" cy="4" r="4" fill="#22c55e"/></svg></span> Padel
      </a>
      <a href="{{ route('venues.index', ['deporte' => 'tenis']) }}" class="sport-pill">
        <span class="sport-pill-icon"><svg width="8" height="8"><circle cx="4" cy="4" r="4" fill="#22c55e"/></svg></span> Tenis
      </a>
      <a href="{{ route('venues.index', ['deporte' => 'basquet']) }}" class="sport-pill">
        <span class="sport-pill-icon"><svg width="8" height="8"><circle cx="4" cy="4" r="4" fill="#22c55e"/></svg></span> Basquet
      </a>
      <a href="{{ route('venues.index') }}" class="sport-pill">
        <span class="sport-pill-icon">+</span> Ver todos
      </a>
    </div>
  </div>
</section>

{{-- ── Por que TuCancha ─────────────────────────────────────────── --}}
<section id="por-que" class="why-section">
  <div class="container">
    <div class="section-head" data-aos="fade-up">
      <span class="section-label">Beneficios</span>
      <h2 class="section-title">Por que TuCancha?</h2>
      <p class="section-subtitle">
        Cada detalle esta disenado para que reservar una cancha sea tan simple como mandar un mensaje.
      </p>
    </div>

    <div class="why-grid">
      <div class="why-card" data-aos="fade-up" data-aos-delay="0">
        <span class="why-card-num">01</span>
        <span class="why-icon"><i data-lucide="zap"></i></span>
        <h3>Reservas al instante</h3>
        <p>Elegí complejo, horario y confirmá tu turno en segundos. Sin esperas ni llamadas.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="100">
        <span class="why-card-num">02</span>
        <span class="why-icon"><i data-lucide="credit-card"></i></span>
        <h3>Pago 100% seguro</h3>
        <p>Paga online con tarjeta, transferencia o efectivo via Mercado Pago. Tu reserva se confirma automaticamente.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="200">
        <span class="why-card-num">03</span>
        <span class="why-icon"><i data-lucide="smartphone"></i></span>
        <h3>Desde cualquier dispositivo</h3>
        <p>Reserva desde el celular, la tablet o la computadora. La plataforma se adapta a tu pantalla.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="0">
        <span class="why-card-num">04</span>
        <span class="why-icon"><i data-lucide="bell"></i></span>
        <h3>Recordatorios por mail</h3>
        <p>Confirmacion inmediata y recordatorios para que no se te escape ningun turno.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="100">
        <span class="why-card-num">05</span>
        <span class="why-icon"><i data-lucide="bar-chart-2"></i></span>
        <h3>Historial de partidos</h3>
        <p>Lleva el registro de todos tus partidos: estadisticas, resultados, deporte favorito y plata gastada en un solo lugar.</p>
      </div>

      <div class="why-card" data-aos="fade-up" data-aos-delay="200">
        <span class="why-card-num">06</span>
        <span class="why-icon"><i data-lucide="users"></i></span>
        <h3>Etiqueta a tus companeros</h3>
        <p>Agregá a los jugadores del partido. Ellos pueden ver el encuentro en su historial y cargar su resultado.</p>
      </div>
    </div>
  </div>
</section>

{{-- ── Split 50/50 imagen + texto ──────────────────────────────── --}}
<section class="split-section">
  <div class="container">
    <div class="split-grid">
      <div class="split-image-wrap" data-aos="fade-right">
        <img src="/images/amigos-post-futbol.webp" alt="Amigos después del partido" loading="lazy">
        <div class="split-image-overlay"></div>
      </div>

      <div class="split-copy" data-aos="fade-left" data-aos-delay="150">
        <span class="split-copy-label">Para jugadores</span>
        <h2>Jugar es facil.<br><em>Encontrar la cancha</em> tambien.</h2>
        <p>
          TuCancha reune los mejores complejos de tu ciudad en un solo lugar.
          Filtra por deporte, fecha y horario, y confirma tu reserva en segundos.
        </p>

        <div class="split-features">
          <div class="split-feature">
            <div class="split-feature-icon"><i data-lucide="calendar"></i></div>
            <span class="split-feature-text">Calendario en tiempo real — ves exactamente que horarios estan libres.</span>
          </div>
          <div class="split-feature">
            <div class="split-feature-icon"><i data-lucide="check-circle"></i></div>
            <span class="split-feature-text">Confirmacion instantanea por mail en cuanto el pago se procesa.</span>
          </div>
          <div class="split-feature">
            <div class="split-feature-icon"><i data-lucide="activity"></i></div>
            <span class="split-feature-text">Multiples deportes: futbol, padel, tenis, basquet y mas.</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── Falta Uno ─────────────────────────────────────────────────── --}}
<section class="faltauno-section">
  <div class="faltauno-bg"></div>
  <div class="faltauno-overlay"></div>
  <div class="faltauno-content">
    <div class="container">

      {{-- Encabezado centrado --}}
      <div class="faltauno-head" data-aos="fade-up">
        <span class="faltauno-tag">Falta Uno</span>
        <h2>Completa el equipo.<br><em>Hoy jugás.</em></h2>
        <p>
          ¿Te falta un jugador o querés sumarte a una partida? Falta Uno conecta
          jugadores y equipos en tiempo real para que ningun partido se cancele.
        </p>
      </div>

      {{-- 2 cards glass --}}
      <div class="faltauno-cards">

        {{-- Card izquierda: tengo cancha, me falta jugador --}}
        <div class="faltauno-card" data-aos="fade-right" data-aos-delay="100">
          <span class="faltauno-card-icon"><i data-lucide="target"></i></span>
          <h3>Me falta un jugador</h3>
          <p>
            Tenes la cancha reservada pero el equipo esta incompleto. Publicá tu partida
            y encontrá el jugador que falta en minutos.
          </p>
          <div class="faltauno-card-list">
            <span class="faltauno-card-list-item">Publicá deporte, horario y posicion</span>
            <span class="faltauno-card-list-item">Recibis solicitudes de jugadores cercanos</span>
            <span class="faltauno-card-list-item">Confirmás al candidato desde la app</span>
          </div>
        </div>

        {{-- Card derecha: quiero sumarme a un partido --}}
        <div class="faltauno-card" data-aos="fade-left" data-aos-delay="200">
          <span class="faltauno-card-icon"><i data-lucide="zap"></i></span>
          <h3>Quiero jugar</h3>
          <p>
            Tenes ganas de jugar pero no tenes equipo o cancha. Explorá las partidas
            abiertas cerca tuyo y sumate al instante.
          </p>
          <div class="faltauno-card-list">
            <span class="faltauno-card-list-item">Filtrá por deporte, zona y horario</span>
            <span class="faltauno-card-list-item">Solicitá unirte con un toque</span>
            <span class="faltauno-card-list-item">El organizador te confirma y listo</span>
          </div>
        </div>

      </div>

      {{-- CTA centrado --}}
      <div class="faltauno-cta" data-aos="fade-up" data-aos-delay="300">
        <a href="{{ route('falta-uno.index') }}" class="btn-faltauno">
          <span>Ver partidas disponibles</span>
          <i data-lucide="arrow-right" style="width:18px;height:18px;stroke:currentColor;"></i>
        </a>
        <p class="faltauno-cta-sub">Sin costo. Solo necesitas una cuenta en TuCancha.</p>
      </div>

    </div>
  </div>
</section>

{{-- ── Duenos / Owner CTA ───────────────────────────────────────── --}}
<section id="para-duenos" class="owner-section">
  <div class="owner-bg"></div>
  <div class="owner-overlay"></div>
  <div class="owner-content">
    <div class="container">
      <div class="owner-grid">
        <div class="owner-copy" data-aos="fade-right">
          <span class="owner-tag">Para dueños de complejos</span>
          <h2>Suma tu complejo<br>a <em>TuCancha</em></h2>
          <p>
            Recibí reservas online 24/7, cobra automaticamente y gestioná todo desde un panel simple.
            Sin complicaciones tecnicas.
          </p>

          <div class="owner-stats">
            <div class="owner-stat">
              <span class="owner-stat-num">24/7</span>
              <span class="owner-stat-label">Reservas online</span>
            </div>
            <div class="owner-stat">
              <span class="owner-stat-num">0%</span>
              <span class="owner-stat-label">Comision en plan base</span>
            </div>
            <div class="owner-stat">
              <span class="owner-stat-num">5min</span>
              <span class="owner-stat-label">Para empezar</span>
            </div>
          </div>

          <div class="owner-actions">
            <a href="{{ route('planes') }}" class="btn-owner-primary">Quiero sumarme &rarr;</a>
            <a href="{{ url('/como-funciona') }}" class="btn-owner-ghost">Ver como funciona</a>
          </div>
        </div>

        <div data-aos="zoom-in" data-aos-delay="200">
          <div class="owner-visual-card">
            <span class="owner-visual-icon"><i data-lucide="layout-dashboard"></i></span>
            <p>Panel de gestion<br>simple e intuitivo</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── FAQ ──────────────────────────────────────────────────────── --}}
<section id="faq" class="faq-section">
  <div class="container">
    <div class="faq-layout">

      {{-- columna izquierda: imagen --}}
      <div class="faq-visual" data-aos="fade-right" data-aos-duration="800">
        <div class="faq-visual-inner">
          <img src="/images/pagoconfirmado-sonrisa.webp" alt="Reserva confirmada" loading="lazy">
          <div class="faq-visual-overlay"></div>
          <div class="faq-visual-accent">
            <i data-lucide="help-circle"></i>
          </div>
          <div class="faq-visual-badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            Reserva en menos de 2 minutos
          </div>
        </div>
      </div>

      {{-- columna derecha: header + acordeón --}}
      <div>
        <div class="faq-col-header" data-aos="fade-left" data-aos-duration="700">
          <span class="section-label">Dudas frecuentes</span>
          <h2>Preguntas<br>frecuentes</h2>
          <p>Todo lo que necesitas saber antes de hacer tu primera reserva.</p>
        </div>

        <div class="faq-list" data-aos="fade-left" data-aos-delay="80" data-aos-duration="700">

          <div class="faq-item">
            <button class="faq-trigger" onclick="toggleFaq(this)">
              <span style="display:flex;align-items:center;gap:10px;">
                <span class="faq-number">1</span>
                <span class="faq-trigger-text">Como reservo una cancha?</span>
              </span>
              <span class="faq-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              </span>
            </button>
            <div class="faq-body">
              Crea una cuenta, explora los complejos, elegí la cancha y el horario.
              Confirmas la reserva, pagas online y recibes la confirmacion por mail en segundos.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-trigger" onclick="toggleFaq(this)">
              <span style="display:flex;align-items:center;gap:10px;">
                <span class="faq-number">2</span>
                <span class="faq-trigger-text">Como se que mi reserva esta confirmada?</span>
              </span>
              <span class="faq-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              </span>
            </button>
            <div class="faq-body">
              Una vez aprobado el pago tu reserva pasa a estado <strong>Confirmada</strong> automaticamente.
              Recibes un mail con todos los detalles: complejo, cancha, dia y horario reservado.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-trigger" onclick="toggleFaq(this)">
              <span style="display:flex;align-items:center;gap:10px;">
                <span class="faq-number">3</span>
                <span class="faq-trigger-text">Puedo cancelar una reserva?</span>
              </span>
              <span class="faq-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              </span>
            </button>
            <div class="faq-body">
              Si. Podes cancelar desde "Mis reservas" dentro del periodo de cancelacion que establece cada complejo.
              Si el pago fue procesado, se inicia el reintegro automaticamente a traves de Mercado Pago.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-trigger" onclick="toggleFaq(this)">
              <span style="display:flex;align-items:center;gap:10px;">
                <span class="faq-number">4</span>
                <span class="faq-trigger-text">Que metodos de pago aceptan?</span>
              </span>
              <span class="faq-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              </span>
            </button>
            <div class="faq-body">
              Aceptamos todos los metodos de Mercado Pago: tarjetas de credito y debito, transferencia bancaria
              y efectivo en puntos de pago. El proceso es 100% seguro.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-trigger" onclick="toggleFaq(this)">
              <span style="display:flex;align-items:center;gap:10px;">
                <span class="faq-number">5</span>
                <span class="faq-trigger-text">Como me registro?</span>
              </span>
              <span class="faq-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              </span>
            </button>
            <div class="faq-body">
              Haz clic en "Crear cuenta", completa tu nombre, mail y contrasena.
              En menos de un minuto tenes tu cuenta lista. No se requiere tarjeta para registrarse.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-trigger" onclick="toggleFaq(this)">
              <span style="display:flex;align-items:center;gap:10px;">
                <span class="faq-number">6</span>
                <span class="faq-trigger-text">Que es el historial de partidos?</span>
              </span>
              <span class="faq-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              </span>
            </button>
            <div class="faq-body">
              Es una seccion de tu perfil donde podes ver todos los partidos que jugaste: cuantos fueron,
              que deporte jugaste mas, tu complejo favorito y el total gastado. Tambien podes cargar el resultado
              de cada partido y ver estadisticas con graficos.
            </div>
          </div>

          <div class="faq-item">
            <button class="faq-trigger" onclick="toggleFaq(this)">
              <span style="display:flex;align-items:center;gap:10px;">
                <span class="faq-number">7</span>
                <span class="faq-trigger-text">Puedo agregar a mis companeros de partido?</span>
              </span>
              <span class="faq-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
              </span>
            </button>
            <div class="faq-body">
              Si. Cuando reservas y pagas una cancha, desde el detalle de la reserva podes buscar a otros usuarios
              de TuCancha por nombre o mail y agregarlos como jugadores del partido. Ellos podran ver ese encuentro
              en su propio historial y cargar su resultado de forma independiente al tuyo.
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
  function toggleFaq(trigger) {
    const item = trigger.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    // cerrar todos
    document.querySelectorAll('.faq-item.open').forEach(function(el) {
      el.classList.remove('open');
    });
    // abrir el clickeado si estaba cerrado
    if (!isOpen) item.classList.add('open');
  }

  /* ── Parallax JS — solo desktop ─────────────────── */
  (function () {
    if (window.innerWidth <= 768) return;

    var slowEls = document.querySelectorAll('.parallax-slow');
    var fastEls = document.querySelectorAll('.parallax-fast');

    function onScroll() {
      var scrollY = window.scrollY;
      slowEls.forEach(function (el) {
        el.style.transform = 'translateY(' + (scrollY * 0.3) + 'px)';
      });
      fastEls.forEach(function (el) {
        el.style.transform = 'translateY(' + (scrollY * 0.6) + 'px)';
      });
    }

    window.addEventListener('scroll', onScroll, { passive: true });
  })();
</script>
@endpush
