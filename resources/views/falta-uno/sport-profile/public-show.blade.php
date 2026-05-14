@extends('layouts.app')

@section('title', $user->name . ' — Perfil público · TuCancha')
@section('meta_description', 'Perfil deportivo de ' . $user->name . ' en TuCancha. Mirá sus stats, partidos y reseñas.')

@push('styles')
<style>
  /* ═══════════════════════════════════════════════════════════════
     PROFILE V2 — Editorial Dark Public Profile
     Palette: TuCancha #22c55e · Font: Sora bold
     Scoped with .pp2 prefix
     ═══════════════════════════════════════════════════════════════ */
  .pp2 {
    --bg: #050505;
    --bg-1: #0a0a0a;
    --bg-2: #111;
    --bg-3: #161616;
    --bd: rgba(255,255,255,.07);
    --bd-2: rgba(255,255,255,.14);
    --tx: #f2f2f2;
    --tx-2: #c8c8c8;
    --tx-3: #8a8a8a;
    --tx-4: #555;
    --accent: #22c55e;
    --accent-ink: #052010;
    --accent-hover: #4ade80;
    --gold: #d4b878;
    --silver: #c7ccd1;
    --bronze: #c68a5a;
    --danger: #f87171;
    --warn: #f5c17a;
    --blue: #7abef5;
    --violet: #a78bfa;
    --pink: #f596c8;
    --orange: #ff9a4b;
  }
  .pp2 { background: var(--bg); color: var(--tx); font-family: 'Sora', system-ui, sans-serif; }
  .pp2 * { box-sizing: border-box; }
  .pp2 a { color: inherit; text-decoration: none; }
  .pp2 button, .pp2 select, .pp2 input { font-family: inherit; }

  /* Break out of layout's .site-main */
  .pp2 {
    margin-inline: calc(50% - 50vw);
    width: 100vw;
    max-width: 100vw;
    overflow-x: clip;
    margin-top: -24px;
    margin-bottom: -40px;
  }
  @media (max-width: 639px) {
    .pp2 { margin-top: -16px; margin-bottom: -32px; }
  }

  .pp2-page { max-width: 1360px; margin: 0 auto; padding: 64px 40px 80px; }

  /* ── BREADCRUMB ── */
  .pp2-crumbs {
    display: flex; align-items: center; gap: 10px;
    font-size: 12px; color: var(--tx-3); font-weight: 600;
    margin-bottom: 20px;
  }
  .pp2-crumbs a { color: var(--tx-3); transition: color .15s; }
  .pp2-crumbs a:hover { color: var(--tx); }
  .pp2-crumbs .curr { color: var(--tx); }

  /* ── HEADER CARD ── */
  .pp2-hdr {
    position: relative;
    border: 1px solid var(--bd);
    border-radius: 24px;
    overflow: hidden;
    background: var(--bg-1);
    margin-bottom: 24px;
  }
  .pp2-hdr-cover {
    height: 180px;
    position: relative;
    background:
      radial-gradient(ellipse 500px 280px at 20% 80%, rgba(34,197,94,.35), transparent 60%),
      radial-gradient(ellipse 400px 240px at 85% 30%, rgba(122,190,245,.25), transparent 60%),
      linear-gradient(135deg, #0a1a0f 0%, #0d1218 100%);
    border-bottom: 1px solid var(--bd);
  }
  .pp2-hdr-cover::before {
    content: '';
    position: absolute; inset: 0;
    background-image:
      linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(ellipse 80% 60% at 50% 40%, black, transparent 80%);
    -webkit-mask-image: radial-gradient(ellipse 80% 60% at 50% 40%, black, transparent 80%);
  }
  .pp2-hdr-cover::after {
    content: '';
    position: absolute; left: 0; right: 0; bottom: 0;
    height: 80px;
    background: linear-gradient(to bottom, transparent, rgba(10,10,10,.9));
  }
  .pp2-hdr-share {
    position: absolute; top: 20px; right: 20px; z-index: 3;
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 14px 9px 12px;
    background: rgba(0,0,0,.4);
    border: 1px solid var(--bd-2);
    border-radius: 999px;
    color: var(--tx); font-size: 12px; font-weight: 600;
    backdrop-filter: blur(12px);
    cursor: pointer;
    transition: background .15s;
  }
  .pp2-hdr-share:hover { background: rgba(0,0,0,.6); }

  .pp2-hdr-body {
    position: relative;
    padding: 0 36px 32px;
    margin-top: -60px;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 28px;
    align-items: end;
  }
  .pp2-hdr-avatar {
    width: 128px; height: 128px; border-radius: 50%;
    background: linear-gradient(135deg, var(--accent), #16a34a);
    border: 5px solid var(--bg-1);
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 900; font-size: 44px; color: var(--accent-ink);
    flex: none;
    box-shadow: 0 12px 40px rgba(0,0,0,.5);
    position: relative;
    letter-spacing: -0.02em;
    overflow: hidden;
  }
  .pp2-hdr-avatar img { width: 100%; height: 100%; object-fit: cover; }

  .pp2-hdr-who { min-width: 0; padding-bottom: 6px; }
  .pp2-hdr-meta-top {
    display: flex; align-items: center; gap: 10px;
    font-size: 11px; font-weight: 800; letter-spacing: .14em;
    text-transform: uppercase; color: var(--tx-3);
    margin-bottom: 10px;
  }
  .pp2-hdr-name {
    font-size: clamp(36px, 4vw, 52px);
    font-weight: 800;
    letter-spacing: -0.04em; line-height: 1;
    color: var(--tx);
    margin: 0;
  }
  .pp2-hdr-name b { font-weight: 900; }

  .pp2-hdr-badges {
    display: flex; gap: 6px; flex-wrap: wrap;
    margin-top: 16px;
  }

  .pp2-hdr-cta {
    display: flex; align-items: stretch; gap: 8px;
    padding-bottom: 6px;
  }
  .pp2-cta-share {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 20px;
    background: var(--accent); color: var(--accent-ink);
    border: none; border-radius: 12px;
    font-size: 13px; font-weight: 800;
    cursor: pointer;
    transition: background .15s;
    text-decoration: none;
  }
  .pp2-cta-share:hover { background: var(--accent-hover); color: var(--accent-ink); }
  .pp2-cta-ico {
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--tx-2);
    cursor: pointer;
    transition: background .15s, color .15s;
  }
  .pp2-cta-ico:hover { background: rgba(255,255,255,.08); color: var(--tx); }

  /* STAT ROW */
  .pp2-hdr-stats {
    display: grid; grid-template-columns: repeat(4, 1fr);
    border-top: 1px solid var(--bd);
  }
  .pp2-hdr-stat {
    padding: 20px 28px;
    position: relative;
  }
  .pp2-hdr-stat + .pp2-hdr-stat { border-left: 1px solid var(--bd); }
  .pp2-hdr-stat-k {
    font-size: 10px; font-weight: 800; letter-spacing: .14em;
    text-transform: uppercase; color: var(--tx-3); margin-bottom: 8px;
  }
  .pp2-hdr-stat-v {
    font-size: 32px; font-weight: 800;
    letter-spacing: -0.035em; color: var(--tx);
    font-variant-numeric: tabular-nums; line-height: 1;
    display: flex; align-items: baseline; gap: 8px;
  }
  .pp2-hdr-stat-v small {
    font-size: 14px; color: var(--tx-3); font-weight: 600;
  }
  .pp2-hdr-stat-sub {
    font-size: 11px; color: var(--tx-3); margin-top: 8px; font-weight: 600;
  }
  .pp2-hdr-stat-rating {
    display: inline-flex; align-items: center; gap: 2px; margin-left: 6px;
    color: var(--accent);
  }

  /* ── BADGE (shared) ── */
  .pp2-badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 10px 3px 8px;
    border-radius: 999px;
    font-size: 11px; font-weight: 700;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    color: var(--tx-2);
    white-space: nowrap;
  }
  .pp2-badge svg { opacity: .9; }
  .pp2-badge.b-conf { background: rgba(34,197,94,.08); color: var(--accent); border-color: rgba(34,197,94,.2); }
  .pp2-badge.b-pun { background: rgba(122,190,245,.08); color: var(--blue); border-color: rgba(122,190,245,.2); }
  .pp2-badge.b-fire { background: rgba(255,154,75,.08); color: var(--orange); border-color: rgba(255,154,75,.2); }
  .pp2-badge.b-gold { background: rgba(212,184,120,.08); color: var(--gold); border-color: rgba(212,184,120,.2); }
  .pp2-badge.b-vet { background: rgba(167,139,250,.08); color: var(--violet); border-color: rgba(167,139,250,.2); }
  .pp2-badge.b-deb { background: rgba(245,150,200,.08); color: var(--pink); border-color: rgba(245,150,200,.2); }
  .pp2-badge.b-lvl {
    text-transform: capitalize; font-size: 10px; font-weight: 800;
    background: rgba(245,193,122,.1); color: var(--warn);
    border-color: rgba(245,193,122,.2); letter-spacing: .04em;
  }

  /* ── GRID ── */
  .pp2-grid {
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 24px;
  }
  .pp2-col { display: flex; flex-direction: column; gap: 24px; min-width: 0; }

  /* ── CARD ── */
  .pp2-card {
    background: var(--bg-1);
    border: 1px solid var(--bd);
    border-radius: 20px;
    padding: 24px 28px 26px;
  }
  .pp2-card-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; gap: 12px; flex-wrap: wrap;
  }
  .pp2-card-title {
    font-size: 15px; font-weight: 800; letter-spacing: -0.01em;
    color: var(--tx);
    display: inline-flex; align-items: center; gap: 10px;
  }
  .pp2-card-title svg { color: var(--tx-3); }
  .pp2-card-eyebrow {
    font-size: 10px; font-weight: 800; letter-spacing: .14em;
    text-transform: uppercase; color: var(--tx-3);
    margin-bottom: 6px;
  }
  .pp2-card-link {
    font-size: 12px; color: var(--tx-3); font-weight: 700;
    display: inline-flex; align-items: center; gap: 4px;
    transition: color .15s;
  }
  .pp2-card-link:hover { color: var(--accent); }

  /* ── CHART ── */
  .pp2-chart-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 20px; margin-bottom: 8px;
  }
  .pp2-chart-now { display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap; }
  .pp2-chart-num {
    font-size: 48px; font-weight: 800;
    letter-spacing: -0.04em; line-height: 1;
    color: var(--tx); font-variant-numeric: tabular-nums;
    display: flex; align-items: baseline; gap: 8px;
  }
  .pp2-chart-num small { font-size: 14px; font-weight: 700; color: var(--tx-3); }
  .pp2-chart-meta {
    font-size: 12px; color: var(--tx-3);
    margin-top: 6px; font-weight: 500;
  }
  .pp2-chart-meta b { color: var(--tx-2); font-weight: 700; }
  .pp2-chart-wrap {
    margin: 16px -28px 0;
    padding: 0;
    position: relative;
  }
  .pp2-chart-wrap svg { display: block; width: 100%; }
  .pp2-chart-empty {
    padding: 40px 0; text-align: center;
    color: var(--tx-4); font-size: 13px; font-weight: 500;
  }

  /* ── SPORTS CARD ── */
  .pp2-sports-stack { display: grid; gap: 10px; }
  .pp2-sport-row {
    display: grid;
    grid-template-columns: 44px 1fr auto;
    gap: 14px; align-items: center;
    padding: 14px;
    background: rgba(255,255,255,.02);
    border: 1px solid var(--bd);
    border-radius: 14px;
    transition: background .15s, border-color .15s;
  }
  .pp2-sport-row:hover { background: rgba(255,255,255,.04); border-color: var(--bd-2); }
  .pp2-sport-row.main { background: rgba(34,197,94,.04); border-color: rgba(34,197,94,.2); }
  .pp2-sport-ico {
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(255,255,255,.04); border: 1px solid var(--bd);
    display: flex; align-items: center; justify-content: center;
    color: var(--tx-2); flex: none;
  }
  .pp2-sport-row.main .pp2-sport-ico {
    background: rgba(34,197,94,.1); border-color: rgba(34,197,94,.2); color: var(--accent);
  }
  .pp2-sport-main { min-width: 0; display: flex; flex-direction: column; gap: 6px; }
  .pp2-sport-name {
    font-size: 14px; font-weight: 800; letter-spacing: -0.01em;
    color: var(--tx);
    display: flex; align-items: center; gap: 8px;
  }
  .pp2-sport-name .pill {
    font-size: 9px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase;
    padding: 2px 8px; border-radius: 999px;
    background: var(--accent); color: var(--accent-ink);
  }
  .pp2-sport-stats {
    display: flex; align-items: center; gap: 10px;
    font-size: 12px; color: var(--tx-3);
    flex-wrap: wrap;
    font-weight: 600;
  }
  .pp2-sport-stats b { color: var(--tx-2); font-weight: 800; font-variant-numeric: tabular-nums; }
  .pp2-sport-stats .sep { width: 2px; height: 2px; background: var(--tx-4); border-radius: 50%; }
  .pp2-sport-end { text-align: right; }
  .pp2-sport-rating {
    font-size: 18px; font-weight: 800; color: var(--tx);
    letter-spacing: -0.02em; font-variant-numeric: tabular-nums;
    display: inline-flex; align-items: center; gap: 4px;
  }
  .pp2-sport-rating svg { color: var(--accent); }
  .pp2-sport-cat {
    font-size: 11px; color: var(--tx-3); margin-top: 2px;
    text-transform: capitalize; font-weight: 600;
  }

  /* ── MEDALS ── */
  .pp2-medals-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
  }
  .pp2-medal {
    position: relative;
    padding: 16px 12px;
    background: rgba(255,255,255,.02);
    border: 1px solid var(--bd);
    border-radius: 14px;
    text-align: center;
    transition: border-color .15s, background .15s, transform .15s;
  }
  .pp2-medal:hover { border-color: var(--bd-2); transform: translateY(-2px); }
  .pp2-medal-ico {
    width: 44px; height: 44px; border-radius: 50%;
    margin: 0 auto 10px;
    display: flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.04);
    color: var(--tx-2);
  }
  .pp2-medal.t-green .pp2-medal-ico { background: rgba(34,197,94,.12); color: var(--accent); box-shadow: 0 0 20px rgba(34,197,94,.15); }
  .pp2-medal.t-blue  .pp2-medal-ico { background: rgba(122,190,245,.12); color: var(--blue); }
  .pp2-medal.t-gold  .pp2-medal-ico { background: rgba(212,184,120,.12); color: var(--gold); box-shadow: 0 0 20px rgba(212,184,120,.15); }
  .pp2-medal.t-orange .pp2-medal-ico { background: rgba(255,154,75,.12); color: var(--orange); }
  .pp2-medal.t-violet .pp2-medal-ico { background: rgba(167,139,250,.12); color: var(--violet); }
  .pp2-medal.t-pink  .pp2-medal-ico { background: rgba(245,150,200,.12); color: var(--pink); }
  .pp2-medal-name {
    font-size: 11px; font-weight: 700; color: var(--tx);
    letter-spacing: -0.005em; line-height: 1.2;
  }
  .pp2-medal-desc {
    font-size: 10px; color: var(--tx-3); margin-top: 4px;
    line-height: 1.3; font-weight: 500;
  }
  .pp2-medals-empty {
    padding: 28px 20px; text-align: center;
    color: var(--tx-4); font-size: 13px; font-weight: 500;
    border: 1px dashed var(--bd-2);
    border-radius: 14px;
    line-height: 1.55;
  }

  /* ── MATCHES ── */
  .pp2-match-list { display: grid; gap: 10px; }
  .pp2-match {
    display: grid;
    grid-template-columns: 44px 1.8fr auto auto;
    gap: 16px;
    align-items: center;
    padding: 14px 16px;
    background: rgba(255,255,255,.02);
    border: 1px solid var(--bd);
    border-radius: 14px;
    transition: background .15s, border-color .15s;
  }
  .pp2-match:hover { background: rgba(255,255,255,.04); border-color: var(--bd-2); }
  .pp2-match-result {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 900; letter-spacing: .08em;
    text-transform: uppercase;
  }
  .pp2-match-result.win {
    background: rgba(34,197,94,.12); color: var(--accent);
    border: 1px solid rgba(34,197,94,.22);
  }
  .pp2-match-result.loss {
    background: rgba(248,113,113,.1); color: var(--danger);
    border: 1px solid rgba(248,113,113,.2);
  }
  .pp2-match-result.draw {
    background: rgba(199,204,209,.06); color: var(--silver);
    border: 1px solid rgba(199,204,209,.16);
  }
  .pp2-match-info { min-width: 0; }
  .pp2-match-kind {
    font-size: 13px; font-weight: 700; color: var(--tx);
    letter-spacing: -0.005em;
  }
  .pp2-match-venue {
    font-size: 11px; color: var(--tx-3); margin-top: 3px;
    display: inline-flex; align-items: center; gap: 5px;
    font-weight: 500;
  }
  .pp2-match-venue svg { opacity: .6; }
  .pp2-match-score {
    font-family: 'Sora'; font-variant-numeric: tabular-nums;
    font-size: 18px; font-weight: 800; letter-spacing: -0.02em;
    color: var(--tx);
    white-space: nowrap;
  }
  .pp2-match-score .faded { color: var(--tx-3); font-weight: 600; font-size: 12px; }
  .pp2-match-when {
    text-align: right;
    font-size: 11px; color: var(--tx-3);
    white-space: nowrap; font-weight: 500;
  }
  .pp2-match-when b { display: block; color: var(--tx-2); font-weight: 700; margin-bottom: 2px; }
  .pp2-match-empty {
    padding: 40px 20px; text-align: center;
    color: var(--tx-4); font-size: 13px; font-weight: 500;
  }

  /* ── RATINGS / REVIEWS ── */
  .pp2-rating-summary {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 24px;
    padding-bottom: 20px;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--bd);
    align-items: center;
  }
  .pp2-rating-big {
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding-right: 24px;
    border-right: 1px solid var(--bd);
  }
  .pp2-rating-num {
    font-size: 48px; font-weight: 800;
    letter-spacing: -0.04em; color: var(--tx);
    line-height: 1; font-variant-numeric: tabular-nums;
  }
  .pp2-rating-stars {
    display: inline-flex; gap: 1px; color: var(--accent);
  }
  .pp2-rating-count { font-size: 11px; color: var(--tx-3); font-weight: 600; }
  .pp2-rating-bars { display: grid; gap: 8px; }
  .pp2-rating-bar {
    display: grid; grid-template-columns: 150px 1fr 32px;
    gap: 12px; align-items: center;
    font-size: 11px; color: var(--tx-3);
    font-weight: 700;
  }
  .pp2-rating-bar-fill {
    height: 6px; background: rgba(255,255,255,.05);
    border-radius: 999px; overflow: hidden;
  }
  .pp2-rating-bar-fill > span { display: block; height: 100%; border-radius: 999px; }
  .pp2-rating-bar.above .pp2-rating-bar-fill > span { background: linear-gradient(90deg, var(--accent), var(--accent-hover)); }
  .pp2-rating-bar.match .pp2-rating-bar-fill > span { background: linear-gradient(90deg, var(--silver), #e0e5ea); }
  .pp2-rating-bar.below .pp2-rating-bar-fill > span { background: linear-gradient(90deg, var(--danger), #ffa3a3); }
  .pp2-rating-bar-count {
    text-align: right; font-variant-numeric: tabular-nums;
    color: var(--tx-2); font-weight: 800;
  }

  .pp2-reviews { display: flex; flex-direction: column; gap: 18px; }
  .pp2-review { display: grid; grid-template-columns: 40px 1fr; gap: 14px; }
  .pp2-rv-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: 13px; color: #000;
    background: linear-gradient(135deg, var(--gold), #a88844);
    flex: none; overflow: hidden;
  }
  .pp2-rv-avatar img { width: 100%; height: 100%; object-fit: cover; }
  .pp2-rv-head {
    display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap;
    margin-bottom: 6px;
  }
  .pp2-rv-name { font-size: 13px; font-weight: 800; color: var(--tx); letter-spacing: -0.005em; }
  .pp2-rv-when { font-size: 11px; color: var(--tx-4); font-weight: 500; }
  .pp2-rv-assessment {
    font-size: 10px; font-weight: 800;
    padding: 2px 8px; border-radius: 999px;
    text-transform: uppercase; letter-spacing: .06em;
  }
  .pp2-rv-assessment.above { background: rgba(34,197,94,.1); color: var(--accent); }
  .pp2-rv-assessment.match { background: rgba(199,204,209,.08); color: var(--silver); }
  .pp2-rv-assessment.below { background: rgba(248,113,113,.1); color: var(--danger); }
  .pp2-rv-text {
    font-size: 13px; color: var(--tx-2);
    line-height: 1.55; margin: 0; font-weight: 400;
  }
  .pp2-reviews-empty {
    padding: 28px 20px; text-align: center;
    color: var(--tx-4); font-size: 13px; font-weight: 500;
    border: 1px dashed var(--bd-2);
    border-radius: 14px;
    line-height: 1.55;
  }

  /* ── FORM CARD ── */
  .pp2-form-row {
    display: grid; grid-template-columns: 90px 1fr 36px;
    gap: 14px; align-items: center;
    padding: 10px 0;
    font-size: 12px;
  }
  .pp2-form-row + .pp2-form-row { border-top: 1px solid var(--bd); }
  .pp2-form-k {
    color: var(--tx-3); font-weight: 800;
    font-size: 11px; letter-spacing: .06em; text-transform: uppercase;
  }
  .pp2-form-bar {
    height: 8px; background: rgba(255,255,255,.04);
    border-radius: 4px; overflow: hidden;
  }
  .pp2-form-bar > span { display: block; height: 100%; border-radius: 4px; }
  .pp2-form-bar.w > span { background: var(--accent); }
  .pp2-form-bar.d > span { background: var(--silver); }
  .pp2-form-bar.l > span { background: var(--danger); }
  .pp2-form-v {
    text-align: right; color: var(--tx); font-weight: 800;
    font-variant-numeric: tabular-nums;
  }
  .pp2-streak {
    margin-top: 14px; display: flex; gap: 5px; align-items: center;
    flex-wrap: wrap;
  }
  .pp2-streak-lbl {
    font-size: 10px; font-weight: 800; letter-spacing: .14em;
    text-transform: uppercase; color: var(--tx-3); margin-right: 4px;
  }
  .pp2-streak-chip {
    width: 24px; height: 24px; border-radius: 7px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 900; color: #000;
  }
  .pp2-streak-chip.w { background: var(--accent); }
  .pp2-streak-chip.l { background: var(--danger); color: #fff; }
  .pp2-streak-chip.d { background: var(--silver); }

  /* ── EMPTY profile ── */
  .pp2-empty-state {
    padding: 64px 32px; text-align: center;
    background: var(--bg-1);
    border: 1px dashed var(--bd-2);
    border-radius: 20px;
    margin-bottom: 24px;
  }
  .pp2-empty-ico {
    width: 56px; height: 56px; border-radius: 16px;
    background: rgba(255,255,255,.04);
    border: 1px solid var(--bd);
    display: inline-flex; align-items: center; justify-content: center;
    color: var(--tx-3); margin-bottom: 14px;
  }
  .pp2-empty-state h4 { font-size: 17px; font-weight: 800; color: var(--tx); margin: 0 0 6px; }
  .pp2-empty-state p { font-size: 13px; color: var(--tx-3); margin: 0; font-weight: 500; }

  /* ── Responsive ── */
  @media (max-width: 1100px) {
    .pp2-grid { grid-template-columns: 1fr; }
    .pp2-hdr-stats { grid-template-columns: repeat(2, 1fr); }
    .pp2-hdr-stat:nth-child(n+3) { border-top: 1px solid var(--bd); }
    .pp2-hdr-stat:nth-child(odd) { border-left: 0 !important; }
  }
  @media (max-width: 900px) {
    .pp2-hdr-body { padding: 0 20px 24px; grid-template-columns: 1fr; gap: 16px; }
    .pp2-hdr-avatar { width: 96px; height: 96px; font-size: 34px; margin-top: -48px; }
    .pp2-hdr-cta { padding-bottom: 0; justify-content: flex-start; }
    .pp2-medals-grid { grid-template-columns: repeat(2, 1fr); }
    .pp2-match { grid-template-columns: 40px 1fr auto; grid-template-areas: 'r info score' 'r meta meta'; gap: 8px 12px; }
    .pp2-match-result { grid-area: r; }
    .pp2-match-info { grid-area: info; }
    .pp2-match-score { grid-area: score; }
    .pp2-match-when { grid-area: meta; text-align: left; }
    .pp2-rating-summary { grid-template-columns: 1fr; }
    .pp2-rating-big { padding-right: 0; border-right: 0; padding-bottom: 16px; border-bottom: 1px solid var(--bd); }
    .pp2-rating-bar { grid-template-columns: 110px 1fr 28px; }
  }
  @media (max-width: 640px) {
    .pp2-page { padding: 48px 20px 60px; }
    .pp2-card { padding: 20px 20px 22px; }
    .pp2-hdr-stats { grid-template-columns: 1fr; }
    .pp2-hdr-stat + .pp2-hdr-stat { border-left: 0; border-top: 1px solid var(--bd); }
    .pp2-chart-wrap { margin-left: -20px; margin-right: -20px; }
  }
</style>
@endpush

@section('content')
@php
  $initials = function($name) {
    $parts = preg_split('/\s+/', trim($name ?? ''));
    $out = '';
    foreach ($parts as $p) { if ($p) $out .= strtoupper(mb_substr($p, 0, 1)); if (strlen($out) >= 2) break; }
    return $out ?: 'U';
  };

  $sportLabels = [
    'football'   => 'Fútbol',
    'padel'      => 'Pádel',
    'tennis'     => 'Tenis',
    'basketball' => 'Básquet',
    'volleyball' => 'Vóley',
  ];
  $sportIcons = [
    'football'   => '<circle cx="12" cy="12" r="10"/><path d="M12 2a15 15 0 0 1 0 20M2 12h20"/>',
    'padel'      => '<rect x="4" y="6" width="16" height="12" rx="2"/><path d="M4 12h16"/>',
    'tennis'     => '<circle cx="12" cy="12" r="10"/><path d="M4.5 9h15M4.5 15h15"/>',
    'basketball' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a14 14 0 0 1 0 20M12 2a14 14 0 0 0 0 20"/>',
    'volleyball' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M8 4v16M16 4v16"/>',
  ];

  // Main sport: highest games_played
  $mainSport = null; $mainStats = null; $mainProfile = null;
  foreach ($profiles as $profile) {
    $s = $realStats[$profile->sport] ?? null;
    if (!$s) continue;
    if (!$mainStats || $s['games_played'] > $mainStats['games_played']) {
      $mainSport = $profile->sport;
      $mainStats = $s;
      $mainProfile = $profile;
    }
  }

  // Aggregate totals
  $totalGames = 0; $totalWins = 0; $totalDraws = 0; $totalLosses = 0;
  foreach ($realStats as $s) {
    $totalGames  += $s['games_played'];
    $totalWins   += $s['wins'];
    $totalDraws  += $s['draws'];
    $totalLosses += $s['losses'];
  }
  $overallWinRate = $totalGames > 0 ? round(($totalWins / $totalGames) * 100) : 0;

  // Rating global = promedio de TODAS las reseñas recibidas en todos los deportes.
  // Fórmula: above=5, match=3, below=1 → avg.
  $allAboveCount = 0; $allMatchCount = 0; $allBelowCount = 0;
  foreach ($ratingsData as $sportData) {
    $allAboveCount += $sportData['above'];
    $allMatchCount += $sportData['match'];
    $allBelowCount += $sportData['below'];
  }
  $totalAssessments = $allAboveCount + $allMatchCount + $allBelowCount;
  $overallRating = $totalAssessments > 0
    ? round((($allAboveCount * 5) + ($allMatchCount * 3) + ($allBelowCount * 1)) / $totalAssessments, 2)
    : 0;

  // Total reviews
  $totalReviews = 0;
  foreach ($ratingsData as $sportData) {
    $totalReviews += $sportData['total'];
  }

  $categoryMap = [
    'recreativo'   => 'Recreativo',
    'intermedio'   => 'Intermedio',
    'avanzado'     => 'Avanzado',
    'competitivo'  => 'Competitivo',
    'primera'      => '1ra',
    'segunda'      => '2da',
    'tercera'      => '3ra',
    'cuarta'       => '4ta',
    'quinta'       => '5ta',
    'sexta'        => '6ta',
    'septima'      => '7ma',
    'octava'       => '8va',
  ];

  // Combined matches (Falta Uno + convencional)
  $combinedMatches = collect();
  foreach ($recentParticipations as $p) {
    if ($p->status === 'no_show' || !$p->result) continue;
    $combinedMatches->push((object) [
      'type'    => 'fu',
      'result'  => $p->result,
      'sport'   => $p->game->field->sport ?? null,
      'venue'   => $p->game->field->venue->name ?? 'Complejo',
      'field'   => $p->game->field->name ?? null,
      'zone'    => $p->game->field->venue->zone ?? null,
      'score'   => $p->game->match_score ?? null,
      'date'    => $p->game->start_at ?? $p->updated_at,
      'label'   => 'Falta Uno',
    ]);
  }
  foreach ($conventionalHistory as $h) {
    $outcome = $h->outcome;
    $result  = $outcome === 'W' ? 'win' : ($outcome === 'L' ? 'loss' : 'draw');
    $combinedMatches->push((object) [
      'type'    => 'conv',
      'result'  => $result,
      'sport'   => $h->field->sport ?? null,
      'venue'   => $h->venue->name ?? 'Complejo',
      'field'   => $h->field->name ?? null,
      'zone'    => $h->venue->zone ?? null,
      'score'   => $h->score,
      'date'    => $h->date,
      'label'   => 'Partido casual',
    ]);
  }
  $combinedMatches = $combinedMatches->sortByDesc('date')->values()->take(8);

  // Streak for main sport
  $streak = $combinedMatches->filter(fn($m) => !$mainSport || $m->sport === $mainSport)->take(8);

  // Chart data: últimos 15 partidos (todos los deportes combinados) con scoring win=3/draw=1/loss=0
  // Ordenados por fecha ascendente para que el chart vaya de izq→der en el tiempo.
  $allCombinedForChart = collect();
  foreach ($recentParticipations as $p) {
    if ($p->status === 'no_show' || !$p->result) continue;
    $allCombinedForChart->push((object) [
      'result' => $p->result,
      'date'   => $p->game->start_at ?? $p->updated_at,
    ]);
  }
  foreach ($conventionalHistory as $h) {
    $outcome = $h->outcome;
    $result  = $outcome === 'W' ? 'win' : ($outcome === 'L' ? 'loss' : 'draw');
    $allCombinedForChart->push((object) [
      'result' => $result,
      'date'   => $h->date,
    ]);
  }
  $chartPoints = $allCombinedForChart
    ->sortByDesc('date')
    ->take(15)
    ->sortBy('date')
    ->values()
    ->map(fn($m) => [
      'result' => match($m->result) { 'win' => 3, 'draw' => 1, 'loss' => 0, default => null },
      'date'   => $m->date ? \Carbon\Carbon::parse($m->date)->format('d/m') : '',
    ])
    ->all();

  // Aggregate ratings across ALL sports (reviews section shows everything combined)
  $aggregatedRatings = [
    'total'    => 0,
    'above'    => 0,
    'match'    => 0,
    'below'    => 0,
    'comments' => collect(),
  ];
  foreach ($ratingsData as $sportKey => $sportData) {
    $aggregatedRatings['total'] += $sportData['total'];
    $aggregatedRatings['above'] += $sportData['above'];
    $aggregatedRatings['match'] += $sportData['match'];
    $aggregatedRatings['below'] += $sportData['below'];
    foreach ($sportData['comments'] as $c) {
      // Attach sport to comment for display
      $c->_sport = $sportKey;
      $aggregatedRatings['comments']->push($c);
    }
  }
  // Sort comments by date desc and take latest 5
  $aggregatedRatings['comments'] = $aggregatedRatings['comments']->sortByDesc('created_at')->take(5)->values();

  $badgeColorClass = function($color) {
    return match($color ?? '') {
      '#16a34a', '#22c55e' => 'b-conf',
      '#0284c7'            => 'b-pun',
      '#ea580c'            => 'b-fire',
      '#ca8a04'            => 'b-gold',
      '#7c3aed'            => 'b-vet',
      '#db2777'            => 'b-deb',
      default              => '',
    };
  };

  $medalTierClass = function($color) {
    return match($color ?? '') {
      '#16a34a', '#22c55e' => 't-green',
      '#0284c7'            => 't-blue',
      '#ea580c'            => 't-orange',
      '#ca8a04'            => 't-gold',
      '#7c3aed'            => 't-violet',
      '#db2777'            => 't-pink',
      default              => 't-green',
    };
  };

  $badgeIcon = function($iconName) {
    return match($iconName ?? '') {
      'shield-check' => '<path d="M12 2 4 5v6c0 5 3.5 9.3 8 11 4.5-1.7 8-6 8-11V5l-8-3z" fill="currentColor"/>',
      'star'         => '<path d="M12 2l3 7 7 .5-5.5 4.7L18 22l-6-4-6 4 1.5-7.8L2 9.5 9 9z" fill="currentColor"/>',
      'award'        => '<circle cx="12" cy="8" r="6"/><path d="m8.5 13.5-2 8.5 5.5-3 5.5 3-2-8.5"/>',
      'flame'        => '<path d="M13 2s5 4 5 10a6 6 0 0 1-12 0c0-2 1-4 2-5 0 2 1 3 2 3 0-4 3-8 3-8z" fill="currentColor"/>',
      'clock'        => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
      'sparkles'     => '<path d="M12 2v6M12 16v6M2 12h6M16 12h6M5 5l4 4M15 15l4 4M5 19l4-4M15 9l4-4"/>',
      default        => '<circle cx="12" cy="12" r="10"/>',
    };
  };
@endphp

<div class="pp2">
  <main class="pp2-page">

    {{-- ─── BREADCRUMB ─── --}}
    <div class="pp2-crumbs">
      <a href="{{ route('home') }}">Inicio</a>
      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
      <a href="{{ route('ranking.index') }}">Jugadores</a>
      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
      <span class="curr">{{ $user->name }}</span>
    </div>

    {{-- ─── HEADER CARD ─── --}}
    <header class="pp2-hdr">
      <div class="pp2-hdr-cover">
        <div style="position:absolute; top:14px; right:14px; display:flex; gap:8px; align-items:center; z-index:5;">
          @auth
            @if(auth()->id() === $user->id)
              <a href="{{ route('profile.edit') }}" class="pp2-hdr-settings" title="Configuración" aria-label="Configuración"
                 style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:50%; background:rgba(0,0,0,.45); border:1px solid rgba(255,255,255,.16); color:#e8e8e8; backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); transition:background .15s, border-color .15s, transform .15s; text-decoration:none;"
                 onmouseover="this.style.background='rgba(0,0,0,.65)'; this.style.borderColor='rgba(255,255,255,.3)'; this.style.transform='rotate(45deg)';"
                 onmouseout="this.style.background='rgba(0,0,0,.45)'; this.style.borderColor='rgba(255,255,255,.16)'; this.style.transform='none';">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </a>
            @endif
          @endauth
          <button type="button" class="pp2-hdr-share" onclick="pp2Share()" style="position:static;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.59 13.51 15.42 17.49M15.41 6.51 8.59 10.49"/></svg>
            Compartir perfil
          </button>
        </div>
      </div>

      <div class="pp2-hdr-body">
        <div class="pp2-hdr-avatar">
          @if($user->avatar_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_path) }}" alt="{{ $user->name }}">
          @else
            {{ $initials($user->name) }}
          @endif
        </div>

        <div class="pp2-hdr-who">
          <div class="pp2-hdr-meta-top">
            <span>Miembro desde {{ $user->created_at->isoFormat('MMMM YYYY') }}</span>
          </div>
          <h1 class="pp2-hdr-name">{{ $user->name }}</h1>

          <div class="pp2-hdr-badges">
            @if($mainProfile && $mainProfile->category)
              <span class="pp2-badge b-lvl">{{ $categoryMap[$mainProfile->category] ?? ucfirst($mainProfile->category) }}</span>
            @endif
            @foreach(array_slice($allBadges, 0, 4) as $badge)
              @php $cls = $badgeColorClass($badge['color'] ?? ''); @endphp
              <span class="pp2-badge {{ $cls }}" title="{{ $badge['description'] ?? '' }}">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $badgeIcon($badge['icon'] ?? '') !!}</svg>
                {{ $badge['name'] ?? '' }}
              </span>
            @endforeach
            @if(count($allBadges) > 4)
              <span class="pp2-badge" style="color: var(--tx-3);">+{{ count($allBadges) - 4 }}</span>
            @endif
          </div>
        </div>

        <div class="pp2-hdr-cta">
          <button type="button" class="pp2-cta-share" onclick="pp2Share()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="M8.59 13.51 15.42 17.49M15.41 6.51 8.59 10.49"/></svg>
            Compartir
          </button>
          <button type="button" class="pp2-cta-ico" onclick="pp2CopyLink()" aria-label="Copiar enlace" title="Copiar enlace">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
          </button>
        </div>
      </div>

      {{-- STAT ROW --}}
      <div class="pp2-hdr-stats">
        <div class="pp2-hdr-stat">
          <div class="pp2-hdr-stat-k">Rating</div>
          <div class="pp2-hdr-stat-v" style="color: var(--accent);">
            {{ $overallRating > 0 ? number_format($overallRating, 1) : '—' }}@if($overallRating > 0)<small>/5</small>@endif
            <span class="pp2-hdr-stat-rating">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 7 7 .5-5.5 4.7L18 22l-6-4-6 4 1.5-7.8L2 9.5 9 9z"/></svg>
            </span>
          </div>
          <div class="pp2-hdr-stat-sub">{{ $totalReviews }} {{ $totalReviews === 1 ? 'reseña' : 'reseñas' }}</div>
        </div>
        <div class="pp2-hdr-stat">
          <div class="pp2-hdr-stat-k">Partidos</div>
          <div class="pp2-hdr-stat-v">{{ $totalGames }}</div>
          <div class="pp2-hdr-stat-sub">{{ $profiles->count() }} {{ $profiles->count() === 1 ? 'deporte' : 'deportes' }}</div>
        </div>
        <div class="pp2-hdr-stat">
          <div class="pp2-hdr-stat-k">Win rate</div>
          <div class="pp2-hdr-stat-v">{{ $overallWinRate }}<small>%</small></div>
          <div class="pp2-hdr-stat-sub">{{ $totalWins }}-{{ $totalDraws }}-{{ $totalLosses }}</div>
        </div>
        <div class="pp2-hdr-stat">
          <div class="pp2-hdr-stat-k">Medallas</div>
          <div class="pp2-hdr-stat-v">{{ count($allBadges) }}</div>
          <div class="pp2-hdr-stat-sub">{{ count($allBadges) > 0 ? 'Conseguidas' : 'Sin medallas aún' }}</div>
        </div>
      </div>
    </header>

    {{-- ─── EMPTY PROFILE STATE ─── --}}
    @if($profiles->isEmpty())
      <div class="pp2-empty-state">
        <div class="pp2-empty-ico">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
        </div>
        <h4>Este jugador todavía no tiene perfil deportivo</h4>
        <p>Cuando {{ $user->name }} se sume a su primer partido, vas a poder ver acá su actividad.</p>
      </div>
    @else

    {{-- ─── MAIN GRID ─── --}}
    <div class="pp2-grid">

      {{-- ─── LEFT COL ─── --}}
      <div class="pp2-col">

        {{-- ─── ACTIVITY CHART ─── --}}
        <section class="pp2-card">
          <div class="pp2-card-head">
            <div>
              <div class="pp2-card-eyebrow">Actividad reciente</div>
              <div class="pp2-chart-top">
                <div class="pp2-chart-now">
                  <span class="pp2-chart-num">{{ $totalGames }} <small>partidos</small></span>
                </div>
              </div>
              <div class="pp2-chart-meta">Últimos {{ count($chartPoints) }} resultados · <b>{{ $totalWins }}G</b> · <b>{{ $totalDraws }}E</b> · <b>{{ $totalLosses }}P</b></div>
            </div>
          </div>
          <div class="pp2-chart-wrap" id="pp2Chart"></div>
        </section>

        {{-- ─── LAST MATCHES ─── --}}
        <section class="pp2-card">
          <div class="pp2-card-head">
            <div class="pp2-card-title">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a14 14 0 0 1 0 20M12 2a14 14 0 0 0 0 20"/></svg>
              Últimos partidos
            </div>
            @if($totalGames > $combinedMatches->count())
              <span style="font-size:12px; color: var(--tx-3); font-weight:600;">{{ $combinedMatches->count() }} de {{ $totalGames }}</span>
            @endif
          </div>

          @if($combinedMatches->isEmpty())
            <div class="pp2-match-empty">Todavía no hay partidos con resultado registrado.</div>
          @else
            <div class="pp2-match-list">
              @foreach($combinedMatches as $m)
                @php
                  $resLabel = match($m->result) { 'win' => 'G', 'loss' => 'P', 'draw' => 'E', default => '—' };
                  $resCls   = match($m->result) { 'win' => 'win', 'loss' => 'loss', 'draw' => 'draw', default => 'draw' };
                  $sportLbl = $sportLabels[$m->sport] ?? ucfirst($m->sport ?? 'Deporte');
                  $whenCarbon = $m->date ? \Carbon\Carbon::parse($m->date) : null;
                @endphp
                <div class="pp2-match">
                  <div class="pp2-match-result {{ $resCls }}">{{ $resLabel }}</div>
                  <div class="pp2-match-info">
                    <div class="pp2-match-kind">{{ $sportLbl }} · {{ $m->label }}</div>
                    <div class="pp2-match-venue">
                      <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-7 8-13a8 8 0 0 0-16 0c0 6 8 13 8 13z"/></svg>
                      {{ $m->venue }}@if($m->field) · {{ $m->field }}@endif
                      @if($m->zone) · {{ $m->zone }}@endif
                    </div>
                  </div>
                  <div class="pp2-match-score">
                    @if($m->score)
                      {{ $m->score }}
                    @else
                      <span class="faded">Sin resultado</span>
                    @endif
                  </div>
                  <div class="pp2-match-when">
                    @if($whenCarbon)
                      <b>{{ $whenCarbon->isoFormat('D MMM') }}</b>
                      {{ $whenCarbon->format('H:i') }}
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </section>

        {{-- ─── REVIEWS ─── --}}
        <section class="pp2-card">
          <div class="pp2-card-head">
            <div class="pp2-card-title">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
              Reseñas de otros jugadores
            </div>
            @if($aggregatedRatings['total'] > 0)
              <span style="font-size:12px; color: var(--tx-3); font-weight:600;">{{ $aggregatedRatings['total'] }} {{ $aggregatedRatings['total'] === 1 ? 'reseña' : 'reseñas' }}</span>
            @endif
          </div>

          @if($aggregatedRatings['total'] === 0)
            <div class="pp2-reviews-empty">
              Todavía no hay reseñas. Cuando jugadores califiquen a {{ $user->name }}, van a aparecer acá.
            </div>
          @else
            @php
              $total = $aggregatedRatings['total'];
              $aboveP = $total > 0 ? round(($aggregatedRatings['above'] / $total) * 100) : 0;
              $matchP = $total > 0 ? round(($aggregatedRatings['match'] / $total) * 100) : 0;
              $belowP = $total > 0 ? round(($aggregatedRatings['below'] / $total) * 100) : 0;
              // Mismo rating que el header: promedio global de todas las reseñas.
              $displayRating = $overallRating;
              $roundedStars = (int) round($displayRating);
            @endphp
            <div class="pp2-rating-summary">
              <div class="pp2-rating-big">
                <div class="pp2-rating-num">{{ number_format($displayRating, 1) }}</div>
                <div class="pp2-rating-stars">
                  @for($i = 1; $i <= 5; $i++)
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="{{ $i <= $roundedStars ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.5"><path d="M12 2l3 7 7 .5-5.5 4.7L18 22l-6-4-6 4 1.5-7.8L2 9.5 9 9z"/></svg>
                  @endfor
                </div>
                <div class="pp2-rating-count">{{ $total }} {{ $total === 1 ? 'reseña' : 'reseñas' }}</div>
              </div>
              <div class="pp2-rating-bars">
                <div class="pp2-rating-bar above">
                  <span>Mejor de lo esperado</span>
                  <div class="pp2-rating-bar-fill"><span style="width:{{ $aboveP }}%"></span></div>
                  <span class="pp2-rating-bar-count">{{ $aggregatedRatings['above'] }}</span>
                </div>
                <div class="pp2-rating-bar match">
                  <span>Como lo esperado</span>
                  <div class="pp2-rating-bar-fill"><span style="width:{{ $matchP }}%"></span></div>
                  <span class="pp2-rating-bar-count">{{ $aggregatedRatings['match'] }}</span>
                </div>
                <div class="pp2-rating-bar below">
                  <span>Por debajo</span>
                  <div class="pp2-rating-bar-fill"><span style="width:{{ $belowP }}%"></span></div>
                  <span class="pp2-rating-bar-count">{{ $aggregatedRatings['below'] }}</span>
                </div>
              </div>
            </div>

            @if($aggregatedRatings['comments']->isNotEmpty())
              <div class="pp2-reviews">
                @foreach($aggregatedRatings['comments'] as $c)
                  @php
                    $rater = $c->rater;
                    $assess = $c->assessment;
                    $assessLabel = match($assess) { 'above' => 'Mejor', 'match' => 'Esperado', 'below' => 'Por debajo', default => '' };
                    $commentSport = $sportLabels[$c->_sport ?? ''] ?? null;
                  @endphp
                  <div class="pp2-review">
                    <div class="pp2-rv-avatar">
                      @if($rater && $rater->avatar_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($rater->avatar_path) }}" alt="{{ $rater->name }}">
                      @else
                        {{ $initials($rater->name ?? '?') }}
                      @endif
                    </div>
                    <div>
                      <div class="pp2-rv-head">
                        <span class="pp2-rv-name">{{ $rater->name ?? 'Anónimo' }}</span>
                        @if($assess)
                          <span class="pp2-rv-assessment {{ $assess }}">{{ $assessLabel }}</span>
                        @endif
                        @if($commentSport)
                          <span style="font-size:11px; color: var(--tx-4); font-weight:600;">· {{ $commentSport }}</span>
                        @endif
                        <span class="pp2-rv-when">{{ $c->created_at?->diffForHumans() }}</span>
                      </div>
                      @if($c->comment)
                        <p class="pp2-rv-text">{{ $c->comment }}</p>
                      @else
                        <p class="pp2-rv-text" style="color: var(--tx-4); font-style: italic;">Sin comentario.</p>
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          @endif
        </section>

      </div>

      {{-- ─── RIGHT COL ─── --}}
      <div class="pp2-col">

        {{-- ─── SPORTS ─── --}}
        <section class="pp2-card">
          <div class="pp2-card-head">
            <div class="pp2-card-title">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a14 14 0 0 1 0 20M12 2a14 14 0 0 0 0 20"/></svg>
              Deportes
            </div>
          </div>

          <div class="pp2-sports-stack">
            @foreach($profiles->sortByDesc(fn($p) => $realStats[$p->sport]['games_played'] ?? 0) as $profile)
              @php
                $s = $realStats[$profile->sport] ?? ['games_played' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0];
                $wr = $s['games_played'] > 0 ? round(($s['wins'] / $s['games_played']) * 100) : 0;
                $isMain = $profile->sport === $mainSport && $mainSport !== null;
              @endphp
              <div class="pp2-sport-row {{ $isMain ? 'main' : '' }}">
                <div class="pp2-sport-ico">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $sportIcons[$profile->sport] ?? '' !!}</svg>
                </div>
                <div class="pp2-sport-main">
                  <div class="pp2-sport-name">
                    {{ $sportLabels[$profile->sport] ?? ucfirst($profile->sport) }}
                    @if($isMain && $profiles->count() > 1)<span class="pill">Principal</span>@endif
                  </div>
                  <div class="pp2-sport-stats">
                    <span><b>{{ $s['games_played'] }}</b> PJ</span>
                    <span class="sep"></span>
                    <span><b>{{ $s['wins'] }}-{{ $s['draws'] }}-{{ $s['losses'] }}</b></span>
                    <span class="sep"></span>
                    <span><b>{{ $wr }}%</b> WR</span>
                  </div>
                </div>
                <div class="pp2-sport-end">
                  <div class="pp2-sport-rating">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 7 7 .5-5.5 4.7L18 22l-6-4-6 4 1.5-7.8L2 9.5 9 9z"/></svg>
                    {{ number_format((float) $profile->average_rating, 1) }}
                  </div>
                  @if($profile->category)
                    <div class="pp2-sport-cat">{{ $categoryMap[$profile->category] ?? $profile->category }}</div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </section>

        {{-- ─── FORM ─── --}}
        @if($mainSport && $mainStats && $mainStats['games_played'] > 0)
          @php
            $mg = $mainStats['games_played'];
            $mw = $mainStats['wins'];
            $md = $mainStats['draws'];
            $ml = $mainStats['losses'];
            $mwp = $mg > 0 ? ($mw / $mg) * 100 : 0;
            $mdp = $mg > 0 ? ($md / $mg) * 100 : 0;
            $mlp = $mg > 0 ? ($ml / $mg) * 100 : 0;
          @endphp
          <section class="pp2-card">
            <div class="pp2-card-head">
              <div>
                <div class="pp2-card-eyebrow">Rendimiento · {{ $sportLabels[$mainSport] ?? ucfirst($mainSport) }}</div>
                <div class="pp2-card-title" style="margin-top:4px">Forma</div>
              </div>
            </div>

            <div class="pp2-form-row">
              <span class="pp2-form-k">Ganados</span>
              <div class="pp2-form-bar w"><span style="width:{{ $mwp }}%"></span></div>
              <span class="pp2-form-v">{{ $mw }}</span>
            </div>
            <div class="pp2-form-row">
              <span class="pp2-form-k">Empates</span>
              <div class="pp2-form-bar d"><span style="width:{{ $mdp }}%"></span></div>
              <span class="pp2-form-v">{{ $md }}</span>
            </div>
            <div class="pp2-form-row">
              <span class="pp2-form-k">Perdidos</span>
              <div class="pp2-form-bar l"><span style="width:{{ $mlp }}%"></span></div>
              <span class="pp2-form-v">{{ $ml }}</span>
            </div>

            @if($streak->isNotEmpty())
              <div class="pp2-streak">
                <span class="pp2-streak-lbl">Últimos {{ $streak->count() }}</span>
                @foreach($streak->reverse() as $m)
                  @php $chipCls = match($m->result) { 'win' => 'w', 'loss' => 'l', 'draw' => 'd', default => 'd' }; @endphp
                  <span class="pp2-streak-chip {{ $chipCls }}">{{ match($m->result) { 'win' => 'G', 'loss' => 'P', 'draw' => 'E', default => '—' } }}</span>
                @endforeach
              </div>
            @endif
          </section>
        @endif

        {{-- ─── MEDALS ─── --}}
        <section class="pp2-card">
          <div class="pp2-card-head">
            <div class="pp2-card-title">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="6"/><path d="m8.5 13.5-2 8.5 5.5-3 5.5 3-2-8.5"/></svg>
              Medallas
              <span style="color: var(--tx-3); font-weight: 500; font-size: 13px; margin-left: 4px;">{{ count($allBadges) }} conseguida{{ count($allBadges) === 1 ? '' : 's' }}</span>
            </div>
          </div>

          @if(empty($allBadges))
            <div class="pp2-medals-empty">
              Todavía no tiene medallas. Se desbloquean jugando partidos, manteniendo buena asistencia y recibiendo buenas calificaciones.
            </div>
          @else
            <div class="pp2-medals-grid">
              @foreach($allBadges as $badge)
                @php $tier = $medalTierClass($badge['color'] ?? ''); @endphp
                <div class="pp2-medal {{ $tier }}" title="{{ $badge['description'] ?? '' }}">
                  <div class="pp2-medal-ico">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">{!! $badgeIcon($badge['icon'] ?? '') !!}</svg>
                  </div>
                  <div class="pp2-medal-name">{{ $badge['name'] ?? '' }}</div>
                </div>
              @endforeach
            </div>
          @endif
        </section>

      </div>
    </div>

    @endif

  </main>
</div>

@push('scripts')
<script>
  (function() {
    // ─── Share / copy link ──
    window.pp2Share = function() {
      var url = window.location.href;
      var title = @json($user->name . ' — Perfil en TuCancha');
      if (navigator.share) {
        navigator.share({ title: title, url: url }).catch(function(){});
      } else {
        pp2CopyLink(true);
      }
    };
    window.pp2CopyLink = function(silent) {
      var url = window.location.href;
      if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(function() {
          if (!silent) alert('Enlace copiado');
        });
      } else {
        var ta = document.createElement('textarea');
        ta.value = url; document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); } catch(e) {}
        document.body.removeChild(ta);
        if (!silent) alert('Enlace copiado');
      }
    };

    // ─── Activity chart (combined matches: Falta Uno + convencional) ──
    var CHART_DATA = @json($chartPoints);
    function renderChart() {
      var host = document.getElementById('pp2Chart');
      if (!host) return;
      var data = Array.isArray(CHART_DATA) ? CHART_DATA.filter(function(d) { return d && d.result !== null; }) : [];

      if (data.length === 0) {
        host.innerHTML = '<div class="pp2-chart-empty">Sin datos de partidos todavía.</div>';
        return;
      }

      var W = host.clientWidth || 640;
      var H = 160;
      var pad = { top: 24, right: 32, bottom: 32, left: 32 };
      var iw = W - pad.left - pad.right;
      var ih = H - pad.top - pad.bottom;

      var pts = data.map(function(d, i) {
        var x = pad.left + (data.length === 1 ? iw / 2 : (i / (data.length - 1)) * iw);
        // result: 3 (win) / 1 (draw) / 0 (loss). Mapeamos a Y invertido.
        var y = pad.top + ih - (d.result / 3) * ih;
        return { x: x, y: y, v: d.result, date: d.date };
      });

      // Líneas horizontales de referencia (win/draw/loss)
      var lines = [
        { y: pad.top, label: 'Gana', color: 'rgba(34,197,94,.1)' },
        { y: pad.top + ih * (2/3), label: 'Empate', color: 'rgba(199,204,209,.08)' },
        { y: pad.top + ih, label: 'Pierde', color: 'rgba(248,113,113,.08)' },
      ];
      var grid = lines.map(function(l) {
        return '<line x1="' + pad.left + '" x2="' + (W - pad.right) + '" y1="' + l.y + '" y2="' + l.y + '" stroke="rgba(255,255,255,.06)" stroke-width="1" stroke-dasharray="2,3"/>';
      }).join('');

      // Línea principal
      var linePath = '';
      pts.forEach(function(p, i) {
        linePath += (i === 0 ? 'M' : 'L') + p.x + ',' + p.y;
      });

      // Área bajo la línea
      var areaPath = linePath + ' L' + pts[pts.length - 1].x + ',' + (pad.top + ih) + ' L' + pts[0].x + ',' + (pad.top + ih) + ' Z';

      // Dots por resultado
      var dots = pts.map(function(p) {
        var color = p.v === 3 ? '#22c55e' : (p.v === 1 ? '#c7ccd1' : '#f87171');
        return '<circle cx="' + p.x + '" cy="' + p.y + '" r="5" fill="' + color + '" stroke="#0a0a0a" stroke-width="2"/>';
      }).join('');

      // Labels en eje X: primero, medio, último
      var labels = '';
      if (data.length >= 1) {
        var showIdx = data.length === 1 ? [0]
          : data.length === 2 ? [0, 1]
          : [0, Math.floor(data.length / 2), data.length - 1];
        showIdx.forEach(function(i) {
          var p = pts[i];
          labels += '<text x="' + p.x + '" y="' + (H - 10) + '" text-anchor="middle" font-size="10" font-family="Sora" fill="#8a8a8a" font-weight="600">' + data[i].date + '</text>';
        });
      }

      // Labels eje Y (izquierda)
      var yLabels =
        '<text x="' + (pad.left - 6) + '" y="' + (pad.top + 4) + '" text-anchor="end" font-size="9" font-family="Sora" fill="#22c55e" font-weight="700" letter-spacing=".06em">G</text>' +
        '<text x="' + (pad.left - 6) + '" y="' + (pad.top + ih * (2/3) + 3) + '" text-anchor="end" font-size="9" font-family="Sora" fill="#c7ccd1" font-weight="700" letter-spacing=".06em">E</text>' +
        '<text x="' + (pad.left - 6) + '" y="' + (pad.top + ih + 3) + '" text-anchor="end" font-size="9" font-family="Sora" fill="#f87171" font-weight="700" letter-spacing=".06em">P</text>';

      host.innerHTML =
        '<svg width="100%" height="' + H + '" viewBox="0 0 ' + W + ' ' + H + '" preserveAspectRatio="none">' +
          '<defs>' +
            '<linearGradient id="pp2ChartFill" x1="0" x2="0" y1="0" y2="1">' +
              '<stop offset="0%" stop-color="#22c55e" stop-opacity=".3"/>' +
              '<stop offset="100%" stop-color="#22c55e" stop-opacity="0"/>' +
            '</linearGradient>' +
          '</defs>' +
          grid +
          yLabels +
          '<path d="' + areaPath + '" fill="url(#pp2ChartFill)"/>' +
          '<path d="' + linePath + '" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' +
          dots +
          labels +
        '</svg>';
    }
    renderChart();
    window.addEventListener('resize', renderChart);
  })();
</script>
@endpush
@endsection
