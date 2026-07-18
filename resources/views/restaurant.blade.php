<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EUT Restaurant — Eat • Unwind • Tea</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
    <style>
/* ══════════════════════════════════════════════════════
   RESET & BASE
══════════════════════════════════════════════════════ */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; background: #04040a; }
body {
    font-family: 'Inter', sans-serif;
    background: #080810;
    color: #f9fafb;
    overflow-x: hidden;
    zoom: 0.75;
    max-width: 1400px;
    margin: 0 auto;
    box-shadow: 0 0 80px rgba(0,0,0,0.6);
}
a { text-decoration: none; }
img { display: block; }

/* ══════════════════════════════════════════════════════
   NOISE FILTER
══════════════════════════════════════════════════════ */
.noise-layer {
    position: fixed; inset: 0; pointer-events: none; z-index: 0;
    opacity: 0.025;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='300' height='300' filter='url(%23n)'/%3E%3C/svg%3E");
}

/* ══════════════════════════════════════════════════════
   BUTTONS
══════════════════════════════════════════════════════ */
.btn-primary {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    color: #fff; padding: 13px 28px; border-radius: 10px;
    font-weight: 700; font-size: 14px; border: none; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: all 0.22s; box-shadow: 0 4px 18px rgba(220,38,38,0.38);
    letter-spacing: 0.01em; white-space: nowrap;
}
.btn-primary:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
    box-shadow: 0 8px 28px rgba(220,38,38,0.55);
    transform: translateY(-2px);
}
.btn-secondary {
    background: transparent; color: #facc15; padding: 13px 28px;
    border-radius: 10px; font-weight: 700; font-size: 14px;
    border: 1.5px solid rgba(250,204,21,0.6); cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: all 0.22s; letter-spacing: 0.01em; white-space: nowrap;
}
.btn-secondary:hover {
    background: rgba(250,204,21,0.1);
    border-color: #facc15;
    transform: translateY(-2px);
}
.btn-outline-white {
    background: rgba(255,255,255,0.05); color: #e5e7eb;
    border: 1px solid rgba(255,255,255,0.14); padding: 9px 20px;
    border-radius: 9px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: all 0.18s; white-space: nowrap;
}
.btn-outline-white:hover {
    background: rgba(255,255,255,0.1);
    border-color: rgba(250,204,21,0.35);
    color: #facc15;
}

/* ══════════════════════════════════════════════════════
   NAV
══════════════════════════════════════════════════════ */
.nav {
    position: sticky; top: 0; z-index: 200;
    height: 60px;
    background: rgba(8,8,16,0.96);
    border-bottom: 1px solid rgba(220,38,38,0.3);
    box-shadow: 0 1px 0 rgba(220,38,38,0.15), 0 4px 24px rgba(0,0,0,0.6);
    backdrop-filter: blur(18px) saturate(1.4);
    padding: 0 48px;
    display: flex; align-items: center; justify-content: space-between;
    transition: background 0.3s;
}
.nav.scrolled {
    background: rgba(8,8,16,0.82);
    box-shadow: 0 0 0 1px rgba(220,38,38,0.2), 0 8px 40px rgba(0,0,0,0.7);
}
.nav-brand {
    display: flex; align-items: center; gap: 8px;
    color: #facc15; font-family: 'Playfair Display', serif;
    font-weight: 700; font-style: italic; font-size: 17px;
    letter-spacing: -0.01em; text-decoration: none; flex-shrink: 0;
}
.nav-links {
    display: flex; align-items: center; gap: 32px; list-style: none;
}
.nav-links a {
    font-size: 13px; font-weight: 500; color: #9ca3af;
    transition: color 0.18s; letter-spacing: 0.01em; text-decoration: none;
}
.nav-links a:hover { color: #f9fafb; }
.nav-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.nav-hamburger {
    display: none; background: none; border: none; cursor: pointer;
    color: #9ca3af; padding: 6px;
}
.mobile-menu {
    display: none; position: fixed; top: 60px; left: 0; right: 0;
    background: #0c0c18; border-bottom: 1px solid rgba(255,255,255,0.07);
    padding: 20px 24px; z-index: 199;
    flex-direction: column; gap: 16px;
}
.mobile-menu.open { display: flex; }
.mobile-menu a {
    font-size: 15px; font-weight: 600; color: #d1d5db;
    text-decoration: none; padding: 8px 0;
    border-bottom: 1px solid rgba(255,255,255,0.06);
    transition: color 0.18s;
}
.mobile-menu a:hover { color: #facc15; }

/* ══════════════════════════════════════════════════════
   HERO
══════════════════════════════════════════════════════ */
.hero {
    position: relative; overflow: hidden;
    min-height: 92vh;
    background: #080810;
    display: flex; align-items: center;
    padding: 80px 48px;
}
.hero-glow-red {
    position: absolute; top: -120px; left: -120px;
    width: 600px; height: 600px; border-radius: 50%;
    background: radial-gradient(circle, rgba(220,38,38,0.14) 0%, transparent 70%);
    pointer-events: none;
}
.hero-glow-amber {
    position: absolute; bottom: -100px; right: 80px;
    width: 500px; height: 500px; border-radius: 50%;
    background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, transparent 70%);
    pointer-events: none;
}
.hero-grid-pattern {
    position: absolute; inset: 0; pointer-events: none; opacity: 0.03;
    background-image:
        linear-gradient(rgba(255,255,255,0.5) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.5) 1px, transparent 1px);
    background-size: 60px 60px;
    transform: skewY(-4deg);
}
.hero-inner {
    max-width: 1160px; margin: 0 auto; width: 100%;
    display: flex; align-items: center; gap: 72px;
    position: relative; z-index: 1;
}
.hero-left { flex: 1; min-width: 0; }
.hero-badge {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3);
    color: #22c55e; font-size: 12px; font-weight: 700;
    padding: 6px 16px; border-radius: 999px; margin-bottom: 28px;
    letter-spacing: 0.06em; text-transform: uppercase;
    animation: badge-pulse 3s ease-in-out infinite;
}
.hero-badge-dot {
    width: 8px; height: 8px; background: #22c55e; border-radius: 50%;
    box-shadow: 0 0 8px rgba(34,197,94,0.8);
    animation: dot-blink 1.5s ease-in-out infinite;
}
@keyframes dot-blink { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.3;transform:scale(0.7)} }
@keyframes badge-pulse { 0%,100%{box-shadow:0 0 0 rgba(34,197,94,0)} 50%{box-shadow:0 0 16px rgba(34,197,94,0.2)} }
.hero-title {
    font-family: 'Playfair Display', serif;
    font-size: 62px; font-weight: 700; line-height: 1.1;
    color: #f9fafb; margin-bottom: 18px; letter-spacing: -0.02em;
}
.hero-title-gold {
    color: #facc15; font-style: italic;
    text-shadow: 0 0 40px rgba(250,204,21,0.25);
}
.hero-desc {
    font-size: 16px; color: #9ca3af; line-height: 1.8;
    margin-bottom: 36px; max-width: 460px;
    font-weight: 400;
}
.hero-btns { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 44px; }
.hero-stats { display: flex; gap: 16px; flex-wrap: wrap; }
.hero-stat-chip {
    display: inline-flex; align-items: center; gap: 8px;
    background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.09);
    border-radius: 999px; padding: 8px 18px;
    font-size: 13px; font-weight: 600; color: #d1d5db;
    backdrop-filter: blur(8px);
    transition: border-color 0.2s;
}
.hero-stat-chip svg { flex-shrink: 0; }
.hero-stat-chip:hover { border-color: rgba(250,204,21,0.25); color: #facc15; }
.hero-right {
    flex-shrink: 0; position: relative;
    display: flex; align-items: center; justify-content: center;
}
.hero-panda-wrap { position: relative; }
.hero-panda {
    width: 460px; height: auto; object-fit: contain;
    filter: drop-shadow(0 0 60px rgba(220,38,38,0.3)) drop-shadow(0 20px 40px rgba(220,38,38,0.2));
    animation: panda-float 5s ease-in-out infinite;
}
@keyframes panda-float {
    0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)}
}
.hero-sales-card {
    position: absolute; bottom: -10px; left: -90px;
    background: rgba(12,12,24,0.92); border: 1px solid rgba(255,255,255,0.12);
    border-radius: 18px; padding: 18px 22px; min-width: 220px;
    backdrop-filter: blur(20px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.05);
    animation: card-float 5s ease-in-out infinite 1s;
}
@keyframes card-float {
    0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)}
}
.hero-sales-label {
    display: flex; align-items: center; gap: 7px;
    font-size: 11px; font-weight: 600; color: #6b7280;
    text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;
}
.hero-sales-dot {
    width: 7px; height: 7px; background: #22c55e; border-radius: 50%;
    box-shadow: 0 0 6px rgba(34,197,94,0.7);
    animation: dot-blink 1.5s ease-in-out infinite;
}
.hero-sales-amount {
    font-size: 28px; font-weight: 900; color: #facc15;
    font-family: 'Playfair Display', serif; margin-bottom: 6px;
    letter-spacing: -0.02em;
}
.hero-sales-growth {
    font-size: 12px; font-weight: 700; color: #22c55e;
    display: flex; align-items: center; gap: 4px;
}

/* ══════════════════════════════════════════════════════
   STATS BAR
══════════════════════════════════════════════════════ */
.stats-bar {
    background: #0c0c18;
    border-top: 1px solid rgba(255,255,255,0.05);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding: 52px 48px;
}
.stats-inner {
    max-width: 1160px; margin: 0 auto;
    display: grid; grid-template-columns: repeat(4,1fr);
}
.stat-item {
    text-align: center; padding: 10px 24px;
    position: relative;
}
.stat-item:not(:last-child)::after {
    content: ''; position: absolute; right: 0; top: 10%;
    height: 80%; width: 1px; background: rgba(255,255,255,0.08);
}
.stat-num {
    font-family: 'Playfair Display', serif;
    font-size: 44px; font-weight: 700; color: #facc15;
    line-height: 1; margin-bottom: 8px; letter-spacing: -0.02em;
}
.stat-label {
    font-size: 13px; color: #4b5563; font-weight: 500; letter-spacing: 0.02em;
}

/* ══════════════════════════════════════════════════════
   SECTION HEADERS
══════════════════════════════════════════════════════ */
.eyebrow {
    font-size: 11px; font-weight: 800; color: #ef4444;
    letter-spacing: 0.18em; text-transform: uppercase; margin-bottom: 14px;
    display: block;
}
.section-title {
    font-family: 'Playfair Display', serif;
    font-size: 42px; font-weight: 700; color: #f9fafb;
    line-height: 1.15; margin-bottom: 16px; letter-spacing: -0.02em;
}
.section-title .gold { color: #facc15; font-style: italic; }
.section-subtitle {
    font-size: 15px; color: #6b7280; max-width: 480px; margin: 0 auto;
    line-height: 1.75; font-weight: 400;
}

/* ══════════════════════════════════════════════════════
   MENU SECTION
══════════════════════════════════════════════════════ */
.menu-section {
    background: #080810;
    padding: 90px 48px;
}
.menu-cats {
    display: flex; gap: 9px; justify-content: center;
    flex-wrap: wrap; margin-bottom: 48px;
}
.menu-cat {
    padding: 10px 22px; border-radius: 999px; font-size: 13px; font-weight: 600;
    border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03);
    color: #9ca3af; cursor: pointer; transition: all 0.2s; white-space: nowrap;
    letter-spacing: 0.01em;
}
.menu-cat:hover {
    border-color: rgba(250,204,21,0.3); color: #facc15;
    background: rgba(250,204,21,0.04);
}
.menu-cat.active {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    border-color: transparent; color: #fff;
    box-shadow: 0 4px 16px rgba(220,38,38,0.42);
}
.menu-grid {
    display: grid; grid-template-columns: repeat(4,1fr); gap: 20px;
    max-width: 1160px; margin: 0 auto;
}
.menu-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px; overflow: hidden;
    transition: transform 0.25s, border-color 0.25s, box-shadow 0.25s;
    box-shadow: 0 4px 20px rgba(0,0,0,0.4);
    cursor: pointer;
}
.menu-card:hover {
    transform: translateY(-6px);
    border-color: rgba(250,204,21,0.35);
    box-shadow: 0 14px 40px rgba(0,0,0,0.55), 0 0 0 1px rgba(250,204,21,0.15), 0 0 30px rgba(250,204,21,0.08);
}
.menu-card-img-wrap { position: relative; overflow: hidden; }
.menu-card-img {
    width: 100%; aspect-ratio: 4/3; object-fit: cover;
    transition: transform 0.38s;
}
.menu-card:hover .menu-card-img { transform: scale(1.07); }
.menu-badge {
    position: absolute; top: 11px; left: 11px;
    font-size: 10px; font-weight: 800; padding: 4px 11px;
    border-radius: 999px; letter-spacing: 0.03em;
    display: inline-flex; align-items: center; gap: 4px;
    line-height: 1;
}
.badge-green  { background: #22c55e; color: #fff; }
.badge-pink   { background: #ec4899; color: #fff; }
.badge-red    { background: linear-gradient(135deg,#dc2626,#ef4444); color: #fff; }
.badge-yellow { background: linear-gradient(135deg,#f59e0b,#facc15); color: #000; }
.badge-teal   { background: #14b8a6; color: #fff; }
.badge-purple { background: #8b5cf6; color: #fff; }
.badge-amber  { background: #f59e0b; color: #000; }
.menu-card-body { padding: 14px 16px 18px; }
.menu-card-cat {
    font-size: 10px; font-weight: 800; color: #ef4444;
    letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 6px;
}
.menu-card-name {
    font-size: 14px; font-weight: 700; color: #f3f4f6; margin-bottom: 12px;
    line-height: 1.35;
}
.menu-card-footer { display: flex; align-items: center; justify-content: space-between; }
.menu-card-price { font-size: 18px; font-weight: 900; color: #facc15; letter-spacing: -0.01em; }
.menu-card-stars { font-size: 12px; color: #9ca3af; font-weight: 600; display: flex; align-items: center; gap: 4px; }

/* ══════════════════════════════════════════════════════
   GALLERY
══════════════════════════════════════════════════════ */
.gallery-section {
    background: #0c0c18;
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 90px 48px;
}
.gallery-grid {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 20px;
    max-width: 1160px; margin: 0 auto;
}
.gallery-card {
    position: relative; border-radius: 20px; overflow: hidden;
    cursor: pointer; border: 1px solid rgba(255,255,255,0.07);
    transition: border-color 0.3s, box-shadow 0.3s;
}
.gallery-card:hover {
    border-color: rgba(220,38,38,0.4);
    box-shadow: 0 0 30px rgba(220,38,38,0.12);
}
.gallery-img {
    width: 100%; aspect-ratio: 4/3; object-fit: cover;
    display: block; transition: transform 0.45s;
}
.gallery-card:hover .gallery-img { transform: scale(1.06); }
.gallery-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.1) 55%, transparent 100%);
    display: flex; flex-direction: column; justify-content: flex-end;
    padding: 24px;
}
.gallery-title-text {
    font-size: 16px; font-weight: 700; color: #fff;
    margin-bottom: 5px;
    transform: translateY(8px); opacity: 0.9;
    transition: transform 0.3s, opacity 0.3s;
}
.gallery-sub-text {
    font-size: 12px; color: rgba(255,255,255,0.6);
    transform: translateY(12px); opacity: 0;
    transition: transform 0.3s 0.05s, opacity 0.3s 0.05s;
}
.gallery-card:hover .gallery-title-text { transform: translateY(0); opacity: 1; }
.gallery-card:hover .gallery-sub-text { transform: translateY(0); opacity: 1; }

/* ══════════════════════════════════════════════════════
   FEATURES
══════════════════════════════════════════════════════ */
.features-section {
    background: #080810;
    padding: 90px 48px;
}
.features-grid {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 20px;
    max-width: 1160px; margin: 0 auto;
}
.feat-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px; padding: 28px;
    transition: border-color 0.25s, box-shadow 0.25s, transform 0.25s;
    opacity: 0; transform: translateY(24px);
    animation-fill-mode: forwards;
}
.feat-card.visible {
    animation: card-rise 0.55s ease forwards;
}
@keyframes card-rise {
    to { opacity: 1; transform: translateY(0); }
}
.feat-card:hover {
    border-color: rgba(250,204,21,0.2);
    box-shadow: 0 10px 32px rgba(0,0,0,0.35);
    transform: translateY(-4px);
}
.feat-icon-box {
    width: 52px; height: 52px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 20px; flex-shrink: 0;
}
.feat-icon-box svg { display: block; }
.feat-title { font-size: 16px; font-weight: 700; color: #f3f4f6; margin-bottom: 10px; }
.feat-desc { font-size: 13px; color: #6b7280; line-height: 1.7; margin-bottom: 16px; }
.feat-list { list-style: none; font-size: 13px; color: #9ca3af; line-height: 2; }
.feat-list li::before { content: '✓ '; color: #22c55e; font-weight: 700; }

/* ══════════════════════════════════════════════════════
   HOW IT WORKS
══════════════════════════════════════════════════════ */
.how-section {
    background: #0c0c18;
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 90px 48px;
}
.steps-row {
    display: grid; grid-template-columns: repeat(4,1fr); gap: 0;
    max-width: 1000px; margin: 0 auto; position: relative;
}
.steps-row::before {
    content: ''; position: absolute; top: 40px; left: 12.5%;
    width: 75%; height: 1px;
    border-top: 2px dashed rgba(220,38,38,0.25);
    z-index: 0;
}
.step-item { text-align: center; padding: 0 12px; position: relative; z-index: 1; }
.step-circle {
    width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 16px;
    display: flex; align-items: center; justify-content: center;
    color: #f9fafb;
    background: rgba(8,8,16,1);
    border: 2px solid transparent;
    background-clip: padding-box;
    position: relative;
}
.step-circle::before {
    content: ''; position: absolute; inset: -2px; border-radius: 50%;
    background: linear-gradient(135deg, #dc2626, #f59e0b);
    z-index: -1;
}
.step-num-label {
    font-size: 10px; font-weight: 800; color: #4b5563;
    letter-spacing: 0.12em; text-transform: uppercase; margin-bottom: 10px;
}
.step-title { font-size: 14px; font-weight: 700; color: #f3f4f6; margin-bottom: 8px; }
.step-desc { font-size: 13px; color: #6b7280; line-height: 1.65; }

/* ══════════════════════════════════════════════════════
   TESTIMONIALS
══════════════════════════════════════════════════════ */
.testi-section { background: #080810; padding: 90px 48px; }
.testi-grid {
    display: grid; grid-template-columns: repeat(3,1fr); gap: 20px;
    max-width: 1160px; margin: 0 auto;
}
.testi-card {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 20px; padding: 28px;
    transition: border-color 0.25s;
    position: relative; overflow: hidden;
}
.testi-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
    background: linear-gradient(90deg, transparent 0%, rgba(220,38,38,0.6) 50%, transparent 100%);
}
.testi-card:hover { border-color: rgba(250,204,21,0.2); }
.testi-stars { color: #facc15; font-size: 15px; letter-spacing: 3px; margin-bottom: 16px; }
.testi-quote {
    font-size: 14px; color: #d1d5db; line-height: 1.8;
    font-style: italic; margin-bottom: 24px;
}
.testi-author { display: flex; align-items: center; gap: 12px; }
.testi-avatar {
    width: 44px; height: 44px; border-radius: 50%;
    background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.testi-name { font-size: 14px; font-weight: 700; color: #f9fafb; margin-bottom: 2px; }
.testi-role { font-size: 12px; color: #6b7280; }

/* ══════════════════════════════════════════════════════
   FAQ
══════════════════════════════════════════════════════ */
.faq-section {
    background: #0c0c18;
    border-top: 1px solid rgba(255,255,255,0.05);
    padding: 90px 48px;
}
.faq-wrap { max-width: 720px; margin: 0 auto; }
.faq-item {
    border-bottom: 1px solid rgba(255,255,255,0.07);
    padding: 22px 0; cursor: pointer; user-select: none;
    border-left: 2px solid transparent;
    padding-left: 0; transition: border-color 0.2s, padding-left 0.2s;
}
.faq-item.open {
    border-left-color: #dc2626; padding-left: 16px;
}
.faq-header {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;
}
.faq-question {
    font-size: 15px; font-weight: 600; color: #f3f4f6;
    transition: color 0.2s; line-height: 1.5;
}
.faq-item.open .faq-question { color: #facc15; }
.faq-chevron {
    flex-shrink: 0; width: 22px; height: 22px; border-radius: 50%;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
    display: flex; align-items: center; justify-content: center;
    margin-top: 1px; transition: transform 0.28s, background 0.2s;
    color: #6b7280; font-size: 14px; font-weight: 700;
}
.faq-item.open .faq-chevron { transform: rotate(90deg); background: rgba(250,204,21,0.1); color: #facc15; }
.faq-answer {
    font-size: 14px; color: #9ca3af; line-height: 1.8;
    max-height: 0; overflow: hidden;
    transition: max-height 0.32s ease, margin-top 0.2s;
}
.faq-item.open .faq-answer { max-height: 240px; margin-top: 14px; }

/* ══════════════════════════════════════════════════════
   CTA BANNER
══════════════════════════════════════════════════════ */
.cta-section {
    background: radial-gradient(ellipse at 30% 50%, rgba(127,29,29,0.7) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 60%, rgba(180,83,9,0.5) 0%, transparent 55%),
                #0d0206;
    padding: 90px 48px;
    position: relative; overflow: hidden;
}
.cta-deco-panda {
    position: absolute; right: -60px; bottom: 0;
    width: 380px; height: auto; opacity: 0.1;
    pointer-events: none;
    filter: grayscale(1);
}
.cta-inner {
    max-width: 1160px; margin: 0 auto;
    display: flex; align-items: center; gap: 80px;
    position: relative; z-index: 1;
}
.cta-left { flex: 1; }
.cta-title {
    font-family: 'Playfair Display', serif;
    font-size: 40px; font-weight: 700; color: #fff;
    margin-bottom: 14px; letter-spacing: -0.02em; line-height: 1.2;
}
.cta-sub { font-size: 15px; color: rgba(255,255,255,0.65); margin-bottom: 36px; line-height: 1.7; }
.cta-points {
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px 40px; margin-bottom: 40px;
}
.cta-point {
    display: flex; align-items: center; gap: 10px;
    font-size: 14px; font-weight: 500; color: rgba(255,255,255,0.9);
}
.cta-check {
    flex-shrink: 0; width: 20px; height: 20px;
}
.btn-cta {
    background: #fff; color: #dc2626; padding: 14px 40px;
    border-radius: 10px; font-weight: 800; font-size: 15px;
    border: none; cursor: pointer; transition: all 0.22s;
    display: inline-flex; align-items: center; gap: 8px;
    letter-spacing: 0.01em; text-decoration: none;
    box-shadow: 0 8px 28px rgba(0,0,0,0.4);
}
.btn-cta:hover { background: #fff0f0; transform: translateY(-2px); box-shadow: 0 12px 36px rgba(0,0,0,0.5); }

/* ══════════════════════════════════════════════════════
   FOOTER
══════════════════════════════════════════════════════ */
.footer {
    background: #05050c;
    border-top: 1px solid rgba(220,38,38,0.2);
    box-shadow: 0 -1px 0 rgba(220,38,38,0.08), 0 -4px 30px rgba(220,38,38,0.05);
    padding: 32px 48px;
}
.footer-top {
    max-width: 1160px; margin: 0 auto;
    display: flex; align-items: center; justify-content: space-between;
    gap: 24px; flex-wrap: wrap;
}
.footer-brand {
    display: flex; align-items: center; gap: 9px;
    color: #facc15; font-family: 'Playfair Display', serif;
    font-weight: 700; font-style: italic; font-size: 18px;
    text-decoration: none;
}
.footer-nav { display: flex; gap: 28px; flex-wrap: wrap; }
.footer-nav a { font-size: 13px; color: #6b7280; text-decoration: none; transition: color 0.18s; }
.footer-nav a:hover { color: #9ca3af; }
.footer-socials { display: flex; gap: 14px; }
.social-icon {
    width: 36px; height: 36px; border-radius: 9px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08);
    display: flex; align-items: center; justify-content: center;
    color: #6b7280; transition: all 0.2s; cursor: pointer;
    text-decoration: none;
}
.social-icon:hover { background: rgba(220,38,38,0.12); border-color: rgba(220,38,38,0.3); color: #ef4444; }
.footer-bottom {
    max-width: 1160px; margin: 20px auto 0;
    padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.05);
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px;
}
.footer-copy { font-size: 12px; color: #4b5563; }
.footer-badge { font-size: 12px; color: #4b5563; display: flex; align-items: center; gap: 6px; }

/* ══════════════════════════════════════════════════════
   AUTH MODAL
══════════════════════════════════════════════════════ */
.auth-modal-backdrop {
    display: none; position: fixed; inset: 0; z-index: 9999;
    align-items: center; justify-content: center;
    background: rgba(0,0,0,0.85); backdrop-filter: blur(8px);
}
.auth-modal-backdrop.open { display: flex; }
.auth-modal {
    position: relative; width: 100%; max-width: 440px;
    margin: 16px; border-radius: 22px; overflow: hidden;
    background: #0a0a15; border: 1px solid rgba(255,255,255,0.1);
    box-shadow: 0 30px 80px rgba(0,0,0,0.7), 0 0 0 1px rgba(255,255,255,0.04);
}
.auth-tabs { display: flex; border-bottom: 1px solid rgba(255,255,255,0.08); }
.auth-tab {
    flex: 1; padding: 17px; font-size: 13px; font-weight: 700;
    letter-spacing: 0.08em; text-transform: uppercase;
    background: none; cursor: pointer; border: none; border-bottom: 2px solid transparent;
    color: #4b5563; transition: all 0.2s;
}
.auth-tab.active { color: #facc15; border-bottom-color: #facc15; }
.auth-body { padding: 30px; }
.auth-alert {
    display: none; margin-bottom: 18px; padding: 12px 16px;
    border-radius: 10px; font-size: 13px; font-weight: 600;
}
.auth-alert.error { background: rgba(220,38,38,0.12); color: #f87171; border: 1px solid rgba(220,38,38,0.25); }
.auth-alert.success { background: rgba(34,197,94,0.12); color: #4ade80; border: 1px solid rgba(34,197,94,0.25); }
.auth-heading {
    font-family: 'Playfair Display', serif;
    font-size: 24px; font-weight: 700; color: #facc15;
    margin-bottom: 4px;
}
.auth-sub { font-size: 13px; color: #6b7280; margin-bottom: 22px; }
.auth-google {
    width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px; padding: 13px; color: #e5e7eb;
    font-size: 14px; font-weight: 600; text-decoration: none;
    transition: all 0.18s; margin-bottom: 20px;
}
.auth-google:hover { background: rgba(255,255,255,0.09); border-color: rgba(255,255,255,0.18); }
.auth-divider {
    display: flex; align-items: center; gap: 12px;
    color: #4b5563; font-size: 12px; margin-bottom: 20px;
}
.auth-divider::before, .auth-divider::after {
    content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.07);
}
.auth-field { margin-bottom: 16px; }
.auth-label { display: block; font-size: 12px; font-weight: 600; color: #9ca3af; margin-bottom: 7px; letter-spacing: 0.05em; text-transform: uppercase; }
.auth-input {
    width: 100%; background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.09); border-radius: 10px;
    padding: 12px 14px; font-size: 14px; color: #f9fafb;
    font-family: 'Inter', sans-serif; transition: border-color 0.18s, box-shadow 0.18s;
    outline: none;
}
.auth-input:focus { border-color: rgba(250,204,21,0.4); box-shadow: 0 0 0 3px rgba(250,204,21,0.07); }
.auth-input::placeholder { color: #4b5563; }
.btn-login {
    width: 100%; padding: 14px; border-radius: 11px;
    background: linear-gradient(135deg,#dc2626,#ef4444); color: #fff;
    font-size: 14px; font-weight: 700; border: none; cursor: pointer;
    transition: all 0.2s; box-shadow: 0 4px 16px rgba(220,38,38,0.35);
    margin-top: 4px;
}
.btn-login:hover { background: linear-gradient(135deg,#b91c1c,#dc2626); box-shadow: 0 8px 24px rgba(220,38,38,0.5); }
.btn-signup {
    width: 100%; padding: 14px; border-radius: 11px;
    background: linear-gradient(135deg,#f59e0b,#facc15); color: #000;
    font-size: 14px; font-weight: 800; border: none; cursor: pointer;
    transition: all 0.2s; box-shadow: 0 4px 16px rgba(245,158,11,0.3);
    margin-top: 4px;
}
.btn-signup:hover { background: linear-gradient(135deg,#d97706,#f59e0b); box-shadow: 0 8px 24px rgba(245,158,11,0.45); }
.auth-panel { display: none; }
.auth-panel.active { display: block; }
.auth-close {
    position: absolute; top: 15px; right: 15px;
    background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px; color: #6b7280; cursor: pointer;
    width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
    transition: all 0.18s; z-index: 1;
}
.auth-close:hover { background: rgba(255,255,255,0.1); color: #fff; }

/* ══════════════════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════════════════ */
@media (max-width: 1100px) {
    .menu-grid { grid-template-columns: repeat(3,1fr); }
}
@media (max-width: 900px) {
    .nav { padding: 0 24px; }
    .nav-links { display: none; }
    .nav-hamburger { display: flex; }
    .hero { padding: 60px 24px; min-height: auto; }
    .hero-inner { flex-direction: column; gap: 40px; text-align: center; }
    .hero-right { order: -1; width: 100%; display: flex; justify-content: center; }
    .hero-panda-wrap { width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px; padding: 0 16px; }
    .hero-panda { width: 260px; }
    .hero-sales-card { position: relative; left: 0; bottom: 0; margin-top: 0; min-width: 130px; flex-shrink: 0; width: 140px; padding: 12px 14px; border-radius: 14px; }
    .hero-sales-amount { font-size: 20px; }
    .hero-sales-label { font-size: 9px; margin-bottom: 6px; }
    .hero-sales-growth { font-size: 10px; }
    .hero-btns, .hero-stats { justify-content: center; }
    .hero-btns { flex-direction: column; align-items: stretch; }
    .hero-btns .btn-primary, .hero-btns .btn-secondary { width: 100%; justify-content: center; }
    .hero-stat-chip { font-size: 11px; padding: 6px 10px; }
    .hero-stats { gap: 6px; justify-content: center; flex-wrap: wrap; }
    .stats-bar { padding: 40px 24px; }
    .stats-inner { grid-template-columns: repeat(2,1fr); gap: 0; }
    .stat-item:nth-child(2)::after, .stat-item:nth-child(4)::after { display: none; }
    .stat-item:nth-child(1), .stat-item:nth-child(2) { border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 28px; }
    .stat-item:nth-child(3), .stat-item:nth-child(4) { padding-top: 28px; }
    .menu-section, .gallery-section, .features-section, .how-section, .testi-section, .faq-section, .cta-section, .loc-section { padding: 64px 24px; }
    .menu-grid { grid-template-columns: repeat(2,1fr); }
    .gallery-grid { grid-template-columns: 1fr; }
    .features-grid { grid-template-columns: repeat(2,1fr); }
    .steps-row { grid-template-columns: repeat(2,1fr); gap: 32px; }
    .steps-row::before { display: none; }
    .testi-grid { grid-template-columns: 1fr; }
    .cta-inner { flex-direction: column; gap: 40px; }
    .cta-points { grid-template-columns: 1fr; }
    .footer-top { justify-content: flex-start; flex-direction: column; gap: 20px; }
    .footer-bottom { flex-direction: column; text-align: center; }
    /* Location section */
    #loc-map-row { flex-direction: column !important; }
    #loc-info-panel { flex: 1 1 100% !important; width: 100% !important; }
    /* Space gallery */
    #spaceGallery { grid-template-columns: repeat(2,1fr) !important; }
    #spaceGallery .loc-thumb:first-child { grid-column: span 2 !important; grid-row: span 1 !important; }
}
@media (max-width: 600px) {
    .hero { padding: 48px 16px; }
    .hero-title { font-size: 30px; }
    .hero-panda { width: 240px; }
    .hero-sales-card { width: 130px; min-width: 120px; padding: 10px 12px; border-radius: 12px; }
    .hero-sales-amount { font-size: 18px; }
    .hero-sales-label { font-size: 8px; }
    .hero-sales-growth { font-size: 9px; }
    .hero-desc { font-size: 14px; }
    .hero-btns { flex-direction: column; align-items: center; }
    .hero-btns .btn-primary, .hero-btns .btn-secondary { width: 100%; justify-content: center; }
    .hero-stat-chip { font-size: 12px; padding: 6px 12px; }
    .hero-stats { gap: 8px; }
    .section-title { font-size: 26px; }
    .section-subtitle { font-size: 13px; }
    .eyebrow { font-size: 10px; }
    .menu-cats { gap: 6px; }
    .menu-cat { font-size: 12px; padding: 8px 14px; }
    .menu-grid { grid-template-columns: repeat(2,1fr); gap: 12px; }
    .menu-card-name { font-size: 13px; }
    .menu-card-price { font-size: 15px; }
    .features-grid { grid-template-columns: 1fr; }
    .feat-card { padding: 20px; }
    .steps-row { grid-template-columns: 1fr; gap: 28px; }
    .step-circle { width: 64px; height: 64px; }
    .stats-bar { padding: 32px 16px; }
    .stat-num { font-size: 32px; }
    .stat-label { font-size: 11px; }
    .testi-card { padding: 20px; }
    .faq-question { font-size: 14px; }
    .cta-title { font-size: 26px; }
    .cta-sub { font-size: 13px; }
    .footer { padding: 24px 16px; }
    .footer-nav { gap: 16px; flex-wrap: wrap; }
    .footer-nav a { font-size: 12px; }
    /* Location section mobile */
    #loc-map-row { flex-direction: column !important; gap: 20px !important; }
    #loc-map-frame { min-height: 240px !important; }
    #loc-info-panel { flex: 1 1 100% !important; width: 100% !important; }
    /* Auth modal */
    .auth-modal { border-radius: 16px; }
    .auth-body { padding: 20px; }
    /* Sections padding */
    .menu-section, .gallery-section, .features-section, .how-section,
    .testi-section, .faq-section, .cta-section, .loc-section { padding: 48px 16px; }
}
    </style>
</head>
<body>
<div class="noise-layer"></div>

<!-- ══════════════════════════════════════════════════
     NAV
══════════════════════════════════════════════════ -->
<nav class="nav" id="mainNav">
    <a href="{{ route('restaurant') }}" class="nav-brand">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
        EUT Restaurant
    </a>
    <ul class="nav-links">
        <li><a href="#hero">Home</a></li>
        <li><a href="#menu">Menu</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#location">Location</a></li>
        <li><a href="#why">Why EUT</a></li>
    </ul>
    <div class="nav-actions">
        @auth
            <a href="{{ route('shop.home') }}" class="btn-primary" style="padding:8px 20px;font-size:13px;">Dashboard →</a>
        @else
            <button onclick="openModal('login')" class="btn-outline-white">Login</button>
            <button onclick="openModal('login')" class="btn-primary" style="padding:8px 20px;font-size:13px;">Order Now →</button>
        @endauth
        <button class="nav-hamburger" onclick="toggleMobileMenu()" aria-label="Menu">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
</nav>
<div class="mobile-menu" id="mobileMenu">
    <a href="#hero">Home</a>
    <a href="#menu">Menu</a>
    <a href="#about">About</a>
    <a href="#why">Why EUT</a>
    @auth
        <a href="{{ route('shop.home') }}">Dashboard →</a>
    @else
        <a href="#" onclick="openModal('login'); closeMobileMenu();">Login</a>
        <a href="#" onclick="openModal('signup'); closeMobileMenu();">Sign Up</a>
    @endauth
</div>

<!-- ══════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════ -->
<section class="hero" id="hero">
    <div class="hero-glow-red"></div>
    <div class="hero-glow-amber"></div>
    <div class="hero-grid-pattern"></div>
    <div class="hero-inner">
        <div class="hero-left">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span> Open Now · Accepting Orders
            </div>
            <h1 class="hero-title">
                Good food, delivered<br>
                <span class="hero-title-gold">straight to you</span>
            </h1>
            <p class="hero-desc">
                Browse our handcrafted menu, place your order in seconds, and get
                fresh, delicious food delivered fast — right to your door.
            </p>
            <div class="hero-btns">
                @auth
                    <a href="{{ route('shop.home') }}" class="btn-primary">Order Now →</a>
                @else
                    <button onclick="openModal('login')" class="btn-primary">Start Ordering →</button>
                @endauth
                <a href="#menu" class="btn-secondary">Browse Menu</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat-chip">
                    <svg width="14" height="14" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    4.9 Rating
                </div>
                <div class="hero-stat-chip">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    30–45 min
                </div>
                <div class="hero-stat-chip">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Metro Naujan, Oriental Mindoro
                </div>
            </div>
        </div>
        <div class="hero-right">
            <div class="hero-panda-wrap">
                <img src="{{ asset('images/DeliveryPanda1.png') }}" alt="EUT Delivery Mascot" class="hero-panda">
                <div class="hero-sales-card">
                    <div class="hero-sales-label">
                        <span class="hero-sales-dot"></span> Orders Today
                    </div>
                    <div class="hero-sales-amount">1,248</div>
                    <div class="hero-sales-growth">↑ +18% from yesterday</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════
     STATS BAR
══════════════════════════════════════════════════ -->
<div class="stats-bar" id="statsBar">
    <div class="stats-inner">
        <div class="stat-item">
            <div class="stat-num" data-target="50" data-suffix="+">0+</div>
            <div class="stat-label">Menu Items to Choose From</div>
        </div>
        <div class="stat-item">
            <div class="stat-num" data-target="2000000" data-suffix="+" data-format="abbr">0+</div>
            <div class="stat-label">Happy Orders Delivered</div>
        </div>
        <div class="stat-item">
            <div class="stat-num" data-target="98" data-suffix="%">0%</div>
            <div class="stat-label">Customer Satisfaction Rate</div>
        </div>
        <div class="stat-item">
            <div class="stat-num" data-target="30" data-suffix=" min">0 min</div>
            <div class="stat-label">Average Delivery Time</div>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════════════════
     MENU
══════════════════════════════════════════════════ -->
<section class="menu-section" id="menu">
    <div style="max-width:1160px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:48px;">
            <span class="eyebrow">Our Menu</span>
            <h2 class="section-title">What We <span class="gold">Serve</span></h2>
            <p class="section-subtitle">Handcrafted by EUT and delivered fresh — every dish is made with intention and care.</p>
        </div>
        <div class="menu-cats" id="menuCats">
            <button class="menu-cat active" data-cat="all">All</button>
            <button class="menu-cat" data-cat="main">Main Course</button>
            <button class="menu-cat" data-cat="desserts">Desserts</button>
            <button class="menu-cat" data-cat="healthy">Healthy</button>
            <button class="menu-cat" data-cat="fastfood">Fast Food</button>
            <button class="menu-cat" data-cat="drinks">Drinks</button>
        </div>
        <div class="menu-grid" id="menuGrid">

            <!-- Greek Salad -->
            <div class="menu-card" data-cat="healthy">
                <div class="menu-card-img-wrap">
                    <img src="{{ asset('images/combo-meal.jpg') }}" alt="Greek Salad" class="menu-card-img">
                    <span class="menu-badge badge-green"><svg width="9" height="9" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Best Seller</span>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-cat">Healthy</div>
                    <div class="menu-card-name">Greek Salad</div>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">₱149</span>
                        <span class="menu-card-stars"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.8</span>
                    </div>
                </div>
            </div>

            <!-- Freshly Baked Cake -->
            <div class="menu-card" data-cat="desserts">
                <div class="menu-card-img-wrap">
                    <img src="{{ asset('images/delicious-burger-fries.jpg') }}" alt="Freshly Baked Cake" class="menu-card-img">
                    <span class="menu-badge badge-pink"><svg width="9" height="9" fill="#fff" viewBox="0 0 24 24"><path d="M12 2a5 5 0 015 5v1h1a3 3 0 013 3v8a3 3 0 01-3 3H6a3 3 0 01-3-3v-8a3 3 0 013-3h1V7a5 5 0 015-5zm0 2a3 3 0 00-3 3v1h6V7a3 3 0 00-3-3z"/></svg> Fan Fave</span>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-cat">Desserts</div>
                    <div class="menu-card-name">Freshly Baked Cake</div>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">₱199</span>
                        <span class="menu-card-stars"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.9</span>
                    </div>
                </div>
            </div>

            <!-- Crispy Burger Combo -->
            <div class="menu-card" data-cat="fastfood">
                <div class="menu-card-img-wrap">
                    <img src="{{ asset('images/gourmet-burger.jpg') }}" alt="Crispy Burger Combo" class="menu-card-img">
                    <span class="menu-badge badge-red"><svg width="9" height="9" fill="#fff" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg> Hot</span>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-cat">Fast Food</div>
                    <div class="menu-card-name">Crispy Burger Combo</div>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">₱179</span>
                        <span class="menu-card-stars"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.7</span>
                    </div>
                </div>
            </div>

            <!-- Pasta Carbonara -->
            <div class="menu-card" data-cat="main">
                <div class="menu-card-img-wrap">
                    <img src="{{ asset('images/hero-burger.jpg') }}" alt="Pasta Carbonara" class="menu-card-img">
                    <span class="menu-badge badge-yellow"><svg width="9" height="9" fill="#000" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg> New</span>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-cat">Main Course</div>
                    <div class="menu-card-name">Pasta Carbonara</div>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">₱220</span>
                        <span class="menu-card-stars"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.8</span>
                    </div>
                </div>
            </div>

            <!-- Grilled Chicken Rice -->
            <div class="menu-card" data-cat="main">
                <div class="menu-card-img-wrap">
                    <img src="{{ asset('images/combo-meal.jpg') }}" alt="Grilled Chicken Rice" class="menu-card-img">
                    <span class="menu-badge badge-green"><svg width="9" height="9" fill="none" stroke="#fff" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Best Seller</span>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-cat">Main Course</div>
                    <div class="menu-card-name">Grilled Chicken Rice</div>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">₱195</span>
                        <span class="menu-card-stars"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.6</span>
                    </div>
                </div>
            </div>

            <!-- Mango Shake -->
            <div class="menu-card" data-cat="drinks">
                <div class="menu-card-img-wrap">
                    <img src="{{ asset('images/french-fries.jpg') }}" alt="Mango Shake" class="menu-card-img">
                    <span class="menu-badge badge-teal"><svg width="9" height="9" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg> Refreshing</span>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-cat">Drinks</div>
                    <div class="menu-card-name">Mango Shake</div>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">₱89</span>
                        <span class="menu-card-stars"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.8</span>
                    </div>
                </div>
            </div>

            <!-- Beef Sinigang -->
            <div class="menu-card" data-cat="main">
                <div class="menu-card-img-wrap">
                    <img src="{{ asset('images/restaurant-interior.jpg') }}" alt="Beef Sinigang" class="menu-card-img">
                    <span class="menu-badge badge-purple"><svg width="9" height="9" fill="#fff" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg> Signature</span>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-cat">Main Course</div>
                    <div class="menu-card-name">Beef Sinigang</div>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">₱235</span>
                        <span class="menu-card-stars"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.9</span>
                    </div>
                </div>
            </div>

            <!-- Chocolate Lava Cake -->
            <div class="menu-card" data-cat="desserts">
                <div class="menu-card-img-wrap">
                    <img src="{{ asset('images/hero-bg.jpg') }}" alt="Chocolate Lava Cake" class="menu-card-img">
                    <span class="menu-badge badge-amber"><svg width="9" height="9" fill="#000" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg> Top Rated</span>
                </div>
                <div class="menu-card-body">
                    <div class="menu-card-cat">Desserts</div>
                    <div class="menu-card-name">Chocolate Lava Cake</div>
                    <div class="menu-card-footer">
                        <span class="menu-card-price">₱169</span>
                        <span class="menu-card-stars"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 5.0</span>
                    </div>
                </div>
            </div>

        </div>
        <div style="text-align:center;margin-top:44px;">
            <a href="{{ route('shop.home') }}" class="btn-primary" style="font-size:15px;padding:14px 36px;">View Full Menu →</a>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════
     GALLERY
══════════════════════════════════════════════════ -->
<section class="gallery-section" id="about">
    <div style="max-width:1160px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:48px;">
            <span class="eyebrow">Our Space</span>
            <h2 class="section-title">Inside <span class="gold">EUT Snack House</span></h2>
            <p class="section-subtitle">A glimpse of where the magic happens — our place in Naujan, Oriental Mindoro.</p>
        </div>

        <!-- Browsable Photo Grid -->
        <div id="spaceGallery" style="
            display:grid;
            grid-template-columns: repeat(4,1fr);
            grid-template-rows: auto auto;
            gap:12px;
        ">
            <!-- Large hero tile -->
            <div class="loc-thumb" onclick="openLightbox(0)" style="grid-column:span 2; grid-row:span 2;">
                <img src="{{ asset('images/restaurant-interior.jpg') }}" alt="EUT Interior" style="width:100%;height:100%;object-fit:cover;">
                <div class="loc-thumb-overlay">
                    <div style="text-align:center;">
                        <svg width="28" height="28" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <div style="font-size:13px;font-weight:700;color:#fff;margin-top:6px;">Our Cozy Dining Area</div>
                    </div>
                </div>
            </div>
            <!-- Small tiles -->
            <div class="loc-thumb" onclick="openLightbox(1)">
                <img src="{{ asset('images/gourmet-burger.jpg') }}" alt="Gourmet Burger" style="width:100%;height:100%;object-fit:cover;">
                <div class="loc-thumb-overlay">
                    <div style="font-size:12px;font-weight:700;color:#fff;text-align:center;">Signature Burger</div>
                </div>
            </div>
            <div class="loc-thumb" onclick="openLightbox(2)">
                <img src="{{ asset('images/combo-meal.jpg') }}" alt="Combo Meal" style="width:100%;height:100%;object-fit:cover;">
                <div class="loc-thumb-overlay">
                    <div style="font-size:12px;font-weight:700;color:#fff;text-align:center;">Combo Meal</div>
                </div>
            </div>
            <div class="loc-thumb" onclick="openLightbox(3)">
                <img src="{{ asset('images/delicious-burger-fries.jpg') }}" alt="Burger & Fries" style="width:100%;height:100%;object-fit:cover;">
                <div class="loc-thumb-overlay">
                    <div style="font-size:12px;font-weight:700;color:#fff;text-align:center;">Burger &amp; Fries</div>
                </div>
            </div>
            <!-- Last tile with "View All" overlay -->
            <div class="loc-thumb" onclick="openLightbox(4)" style="position:relative;">
                <img src="{{ asset('images/french-fries.jpg') }}" alt="More Photos" style="width:100%;height:100%;object-fit:cover;">
                <div class="loc-thumb-overlay" style="background:rgba(0,0,0,0.6);">
                    <div style="text-align:center;">
                        <div style="font-size:22px;font-weight:900;color:#facc15;">+{{ 3 }}</div>
                        <div style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.8);margin-top:2px;">View All</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════
     LIGHTBOX
══════════════════════════════════════════════════ -->
<div id="lightbox" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,0.92);backdrop-filter:blur(10px);align-items:center;justify-content:center;flex-direction:column;">
    <button onclick="closeLightbox()" style="position:absolute;top:20px;right:20px;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:10px;color:#fff;width:42px;height:42px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:22px;z-index:2;" aria-label="Close">&times;</button>
    <div id="lbCounter" style="position:absolute;top:22px;left:50%;transform:translateX(-50%);font-size:13px;font-weight:600;color:rgba(255,255,255,0.5);"></div>
    <button onclick="shiftLightbox(-1)" style="position:absolute;left:20px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:10px;color:#fff;width:48px;height:48px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:26px;">&#8249;</button>
    <img id="lbImg" src="" alt="" style="max-width:88vw;max-height:80vh;border-radius:16px;object-fit:contain;box-shadow:0 30px 80px rgba(0,0,0,0.7);transition:opacity 0.2s;">
    <div id="lbCaption" style="margin-top:18px;font-size:14px;font-weight:500;color:rgba(255,255,255,0.7);text-align:center;"></div>
    <button onclick="shiftLightbox(1)" style="position:absolute;right:20px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:10px;color:#fff;width:48px;height:48px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:26px;">&#8250;</button>
    <div id="lbStrip" style="position:absolute;bottom:24px;left:50%;transform:translateX(-50%);display:flex;gap:8px;padding:8px;background:rgba(0,0,0,0.5);border-radius:12px;max-width:90vw;overflow-x:auto;"></div>
</div>

<style>
.loc-thumb {
    position:relative; border-radius:12px; overflow:hidden;
    aspect-ratio:4/3; cursor:pointer;
    border:1px solid rgba(255,255,255,0.07);
    transition:transform 0.22s, border-color 0.22s;
}
.loc-thumb:hover { transform:scale(1.03); border-color:rgba(250,204,21,0.4); }
.loc-thumb-overlay {
    position:absolute; inset:0;
    background:rgba(0,0,0,0); display:flex;
    align-items:center; justify-content:center;
    transition:background 0.22s;
}
.loc-thumb:hover .loc-thumb-overlay { background:rgba(0,0,0,0.38); }
</style>

<script>
const lbImages = [
    { src: '{{ asset("images/restaurant-interior.jpg") }}', caption: 'Our Place — EUT Snack House' },
    { src: '{{ asset("images/gourmet-burger.jpg") }}',      caption: 'Signature Burger' },
    { src: '{{ asset("images/combo-meal.jpg") }}',          caption: 'Combo Meal' },
    { src: '{{ asset("images/delicious-burger-fries.jpg") }}', caption: 'Burger & Fries' },
    { src: '{{ asset("images/french-fries.jpg") }}',        caption: 'Crispy French Fries' },
    { src: '{{ asset("images/hero-burger.jpg") }}',         caption: 'Fresh Burger' },
    { src: '{{ asset("images/hero-bg.jpg") }}',             caption: 'EUT Snack House Vibes' },
];
let lbCurrent = 0;

function openLightbox(idx) {
    lbCurrent = idx;
    document.getElementById('lightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    renderLightbox();
    buildStrip();
}
function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = '';
}
function shiftLightbox(dir) {
    lbCurrent = (lbCurrent + dir + lbImages.length) % lbImages.length;
    renderLightbox();
}
function renderLightbox() {
    const img = document.getElementById('lbImg');
    img.style.opacity = '0';
    setTimeout(() => { img.src = lbImages[lbCurrent].src; img.alt = lbImages[lbCurrent].caption; img.style.opacity = '1'; }, 100);
    document.getElementById('lbCaption').textContent = lbImages[lbCurrent].caption;
    document.getElementById('lbCounter').textContent = (lbCurrent + 1) + ' / ' + lbImages.length;
    document.querySelectorAll('.lb-strip-thumb').forEach((el, i) => {
        el.style.opacity = i === lbCurrent ? '1' : '0.4';
        el.style.borderColor = i === lbCurrent ? '#facc15' : 'transparent';
    });
}
function buildStrip() {
    const strip = document.getElementById('lbStrip');
    strip.innerHTML = '';
    lbImages.forEach((item, i) => {
        const t = document.createElement('img');
        t.src = item.src; t.alt = item.caption; t.className = 'lb-strip-thumb';
        t.style.cssText = 'width:52px;height:40px;object-fit:cover;border-radius:6px;cursor:pointer;border:2px solid transparent;transition:all 0.18s;flex-shrink:0;';
        t.onclick = () => { lbCurrent = i; renderLightbox(); };
        strip.appendChild(t);
    });
}
document.addEventListener('keydown', e => {
    if (document.getElementById('lightbox').style.display === 'flex') {
        if (e.key === 'ArrowRight') shiftLightbox(1);
        if (e.key === 'ArrowLeft')  shiftLightbox(-1);
        if (e.key === 'Escape')     closeLightbox();
    }
});
document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) closeLightbox();
});
</script>

<!-- ══════════════════════════════════════════════════
     LOCATION / MAP
══════════════════════════════════════════════════ -->
<section class="loc-section" style="background:#080810; padding:90px 48px; border-top:1px solid rgba(255,255,255,0.05);" id="location">
    <div style="max-width:1160px; margin:0 auto;">
        <div style="text-align:center; margin-bottom:48px;">
            <span class="eyebrow">Find Us</span>
            <h2 class="section-title">Where to <span class="gold">Find Us</span></h2>
            <p class="section-subtitle">Visit us at Apostol St, Naujan, Oriental Mindoro — or order online and we'll come to you.</p>
        </div>
        <div id="loc-map-row" style="display:flex; gap:40px; align-items:stretch; flex-wrap:wrap;">
            <!-- Map Embed -->
            <div id="loc-map-frame" style="flex:1; min-width:280px; border-radius:20px; overflow:hidden; border:1px solid rgba(255,255,255,0.08); min-height:360px;">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3891.234!2d121.3027265!3d13.3213129!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bcc1aee4d46ba9%3A0xa7041e92dff79eb!2sEUT%20SNACK%20HOUSE!5e0!3m2!1sen!2sph!4v1700000000000!5m2!1sen!2sph"
                    width="100%"
                    height="100%"
                    style="border:0; min-height:360px; display:block; filter:invert(90%) hue-rotate(180deg);"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="EUT Snack House Location">
                </iframe>
            </div>
            <!-- Info Panel -->
            <div id="loc-info-panel" style="flex:0 0 300px; display:flex; flex-direction:column; gap:20px;">
                <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:24px;">
                    <div style="display:flex; align-items:flex-start; gap:14px;">
                        <div style="width:40px; height:40px; border-radius:12px; background:rgba(220,38,38,0.12); border:1px solid rgba(220,38,38,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <div style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Address</div>
                            <div style="font-size:14px; font-weight:600; color:#f3f4f6; line-height:1.6;">Apostol St, Naujan<br>5204 Oriental Mindoro<br><span style="color:#9ca3af; font-weight:400;">Located in: LJM Apartments</span></div>
                        </div>
                    </div>
                </div>
                <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:24px;">
                    <div style="display:flex; align-items:flex-start; gap:14px;">
                        <div style="width:40px; height:40px; border-radius:12px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Hours</div>
                            <div style="font-size:14px; font-weight:600; color:#f3f4f6; line-height:1.6;">Open Daily<br><span style="color:#22c55e;">Until 11:00 PM</span></div>
                        </div>
                    </div>
                </div>
                <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:16px; padding:24px;">
                    <div style="display:flex; align-items:flex-start; gap:14px;">
                        <div style="width:40px; height:40px; border-radius:12px; background:rgba(250,204,21,0.1); border:1px solid rgba(250,204,21,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <svg width="18" height="18" fill="none" stroke="#facc15" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <div style="font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:6px;">Contact</div>
                            <div style="font-size:14px; font-weight:600; color:#f3f4f6; line-height:1.6;"><a href="tel:09052883320" style="color:#facc15; text-decoration:none;">0905 288 3320</a></div>
                        </div>
                    </div>
                </div>
                <a href="https://maps.app.goo.gl/azFF3zxcrzwSnctY7" target="_blank" rel="noopener noreferrer" class="btn-primary" style="justify-content:center; text-decoration:none;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Open in Google Maps
                </a>
            </div>
        </div>
    </div>
</section>
<!-- ══════════════════════════════════════════════════
     FEATURES
══════════════════════════════════════════════════ -->
<section class="features-section" id="vendors">
    <div style="max-width:1160px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:56px;">
            <span class="eyebrow">Why EUT</span>
            <h2 class="section-title">Everything You Love<br>in <span class="gold">One Place</span></h2>
            <p class="section-subtitle">From browsing the menu to tracking your delivery, EUT makes ordering fast, easy, and enjoyable every time.</p>
        </div>
        <div class="features-grid" id="featGrid">
            <div class="feat-card">
                <div class="feat-icon-box" style="background:rgba(250,204,21,0.1);">
                    <svg width="24" height="24" fill="none" stroke="#facc15" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <div class="feat-title">Live Order Tracking</div>
                <p class="feat-desc">Know exactly where your food is at every step — from the kitchen to your front door in real time.</p>
                <ul class="feat-list">
                    <li>Real-time delivery updates</li>
                    <li>Estimated arrival time</li>
                    <li>Order status notifications</li>
                </ul>
            </div>
            <div class="feat-card">
                <div class="feat-icon-box" style="background:rgba(220,38,38,0.1);">
                    <svg width="24" height="24" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <div class="feat-title">Easy Ordering</div>
                <p class="feat-desc">Browse the full menu, customize your meal, and place your order in just a few taps — no hassle.</p>
                <ul class="feat-list">
                    <li>Simple, intuitive menu</li>
                    <li>Meal customization options</li>
                    <li>Quick reorder feature</li>
                </ul>
            </div>
            <div class="feat-card">
                <div class="feat-icon-box" style="background:rgba(34,197,94,0.1);">
                    <svg width="24" height="24" fill="none" stroke="#22c55e" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <div class="feat-title">Fast Delivery</div>
                <p class="feat-desc">Get your food delivered fresh and hot, with an average arrival time of under 30 minutes.</p>
                <ul class="feat-list">
                    <li>30-minute average delivery</li>
                    <li>Contactless delivery option</li>
                    <li>Scheduled delivery slots</li>
                </ul>
            </div>
            <div class="feat-card">
                <div class="feat-icon-box" style="background:rgba(168,85,247,0.1);">
                    <svg width="24" height="24" fill="none" stroke="#a855f7" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="feat-title">Exclusive Deals</div>
                <p class="feat-desc">Enjoy member-only promos, discounts, and loyalty rewards just for ordering through EUT.</p>
                <ul class="feat-list">
                    <li>Member discounts</li>
                    <li>Loyalty reward points</li>
                    <li>Seasonal promotions</li>
                </ul>
            </div>
            <div class="feat-card">
                <div class="feat-icon-box" style="background:rgba(236,72,153,0.1);">
                    <svg width="24" height="24" fill="none" stroke="#ec4899" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                </div>
                <div class="feat-title">Fresh, Quality Food</div>
                <p class="feat-desc">Every dish is handcrafted by EUT's kitchen with fresh ingredients — cooked to order, never reheated.</p>
                <ul class="feat-list">
                    <li>Made-to-order meals</li>
                    <li>Quality-checked ingredients</li>
                    <li>Consistent taste every time</li>
                </ul>
            </div>
            <div class="feat-card">
                <div class="feat-icon-box" style="background:rgba(249,115,22,0.1);">
                    <svg width="24" height="24" fill="none" stroke="#f97316" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div class="feat-title">Secure & Easy Payment</div>
                <p class="feat-desc">Pay with confidence using multiple secure payment options — quick checkout, no hidden fees.</p>
                <ul class="feat-list">
                    <li>Multiple payment methods</li>
                    <li>Secure transactions</li>
                    <li>No hidden charges</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════
     HOW IT WORKS
══════════════════════════════════════════════════ -->
<section class="how-section">
    <div style="max-width:1160px;margin:0 auto;text-align:center;">
        <span class="eyebrow">Simple Process</span>
        <h2 class="section-title" style="margin-bottom:14px;">How to <span class="gold">Order</span></h2>
        <p class="section-subtitle" style="margin-bottom:64px;">
            Ordering from EUT is quick and easy. Just follow these simple steps and enjoy your meal.
        </p>
        <div class="steps-row">
            <div class="step-item">
                <div class="step-circle">
                    <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div class="step-num-label">Step 01</div>
                <div class="step-title">Create an Account</div>
                <p class="step-desc">Sign up for free in seconds and set up your delivery address.</p>
            </div>
            <div class="step-item">
                <div class="step-circle">
                    <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div class="step-num-label">Step 02</div>
                <div class="step-title">Browse the Menu</div>
                <p class="step-desc">Explore our handcrafted dishes, pick your favorites, and customize your meal.</p>
            </div>
            <div class="step-item">
                <div class="step-circle">
                    <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div class="step-num-label">Step 03</div>
                <div class="step-title">Place Your Order</div>
                <p class="step-desc">Add items to your cart, choose a payment method, and confirm your order.</p>
            </div>
            <div class="step-item">
                <div class="step-circle">
                    <svg width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
                <div class="step-num-label">Step 04</div>
                <div class="step-title">Enjoy Your Meal</div>
                <p class="step-desc">Track your delivery in real time and enjoy fresh, hot food at your door.</p>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════
     TESTIMONIALS
══════════════════════════════════════════════════ -->
<section class="testi-section">
    <div style="max-width:1160px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:48px;">
            <span class="eyebrow">Happy Customers</span>
            <h2 class="section-title">What Our <span class="gold">Customers Say</span></h2>
            <p class="section-subtitle">Real reviews from real customers who love ordering from EUT.</p>
        </div>
        <div class="testi-grid">
            <div class="testi-card">
                <div class="testi-stars">★★★★★</div>
                <p class="testi-quote">"The food arrived hot and fresh, exactly as described. Ordering was super easy and the delivery was faster than expected — I'm hooked!"</p>
                <div class="testi-author">
                    <div class="testi-avatar">
                        <svg width="22" height="22" fill="none" stroke="rgba(220,38,38,0.8)" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="testi-name">Camille Santos</div>
                        <div class="testi-role">Regular Customer</div>
                    </div>
                </div>
            </div>
            <div class="testi-card">
                <div class="testi-stars">★★★★★</div>
                <p class="testi-quote">"I love how easy it is to customize my order. The burgers are incredible and I can track exactly when they'll arrive. Best food app I've used!"</p>
                <div class="testi-author">
                    <div class="testi-avatar">
                        <svg width="22" height="22" fill="none" stroke="rgba(220,38,38,0.8)" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="testi-name">Marco Reyes</div>
                        <div class="testi-role">Loyal Customer</div>
                    </div>
                </div>
            </div>
            <div class="testi-card">
                <div class="testi-stars">★★★★★</div>
                <p class="testi-quote">"Ordered for our whole office and everyone was impressed. Delivery was on time, portions were generous, and the taste was amazing. Will order again!"</p>
                <div class="testi-author">
                    <div class="testi-avatar">
                        <svg width="22" height="22" fill="none" stroke="rgba(220,38,38,0.8)" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="testi-name">Andrea Lim</div>
                        <div class="testi-role">Happy Customer</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════
     FAQ
══════════════════════════════════════════════════ -->
<section class="faq-section">
    <div style="max-width:1160px;margin:0 auto;">
        <div style="text-align:center;margin-bottom:52px;">
            <span class="eyebrow">Got Questions?</span>
            <h2 class="section-title">Frequently Asked <span class="gold">Questions</span></h2>
            <p class="section-subtitle">Everything you need to know before placing your first order.</p>
        </div>
        <div class="faq-wrap" id="faqList">
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-header">
                    <div class="faq-question">How do I place an order?</div>
                    <div class="faq-chevron">›</div>
                </div>
                <div class="faq-answer">Simply create a free account, browse our menu, add your favorite items to the cart, and check out. It only takes a few minutes!</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-header">
                    <div class="faq-question">How long does delivery take?</div>
                    <div class="faq-chevron">›</div>
                </div>
                <div class="faq-answer">Our average delivery time is under 30 minutes within Naujan, Oriental Mindoro. You can track your order in real time from the moment it leaves our kitchen.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-header">
                    <div class="faq-question">What areas do you deliver to?</div>
                    <div class="faq-chevron">›</div>
                </div>
                <div class="faq-answer">We currently deliver within Naujan and nearby areas in Oriental Mindoro. Enter your address at checkout and we'll confirm if you're within our delivery zone.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-header">
                    <div class="faq-question">Can I customize my order?</div>
                    <div class="faq-chevron">›</div>
                </div>
                <div class="faq-answer">Yes! Many of our menu items let you choose add-ons, remove ingredients, or adjust portion sizes. Just select your preferences when adding items to your cart.</div>
            </div>
            <div class="faq-item" onclick="toggleFaq(this)">
                <div class="faq-header">
                    <div class="faq-question">What payment methods do you accept?</div>
                    <div class="faq-chevron">›</div>
                </div>
                <div class="faq-answer">We accept cash on delivery, GCash, credit/debit cards, and other major e-wallets. All online payments are secured and encrypted.</div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════
     CTA BANNER
══════════════════════════════════════════════════ -->
<section class="cta-section">
    <img src="{{ asset('images/DeliveryPanda.png') }}" alt="" class="cta-deco-panda">
    <div class="cta-inner">
        <div class="cta-left">
            <h2 class="cta-title">Why Choose EUT?</h2>
            <p class="cta-sub">Order now and get fast, reliable delivery straight to your door — anytime, anywhere in Naujan, Oriental Mindoro.</p>
            <div class="cta-points">
                <div class="cta-point">
                    <svg class="cta-check" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Fast and reliable delivery
                </div>
                <div class="cta-point">
                    <svg class="cta-check" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Real-time order tracking
                </div>
                <div class="cta-point">
                    <svg class="cta-check" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Wide variety of stores
                </div>
                <div class="cta-point">
                    <svg class="cta-check" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    No hidden fees
                </div>
            </div>
            @auth
                <a href="{{ route('shop.home') }}" class="btn-cta">Join Now →</a>
            @else
                <button onclick="openModal('signup')" class="btn-cta">Join Now →</button>
            @endauth
        </div>
    </div>
</section>

<!-- ══════════════════════════════════════════════════
     FOOTER
══════════════════════════════════════════════════ -->
<footer class="footer">
    <div class="footer-top">
        <a href="{{ route('restaurant') }}" class="footer-brand">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
            EUT Restaurant
        </a>
        <nav class="footer-nav">
            <a href="#hero">Home</a>
            <a href="#menu">Menu</a>
            <a href="#about">About</a>
            <a href="#vendors">Why EUT</a>
            <a href="{{ route('shop.home') }}">Order Now</a>
        </nav>
        <div class="footer-socials">
            <a href="#" class="social-icon" aria-label="Facebook">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="Instagram">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/></svg>
            </a>
            <a href="#" class="social-icon" aria-label="Twitter">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg>
            </a>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-copy">© 2026 EUT-Delivery (Eat • Unwind • Tea). All rights reserved.</div>
        <div class="footer-badge">
            <span style="color:#dc2626;">●</span> EUT Snack House — Naujan, Oriental Mindoro
        </div>
    </div>
</footer>

<!-- ══════════════════════════════════════════════════
     AUTH MODAL
══════════════════════════════════════════════════ -->
<div class="auth-modal-backdrop" id="authModal">
    <div class="auth-modal">
        <button class="auth-close" onclick="closeModal()" aria-label="Close">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="auth-tabs">
            <button class="auth-tab active" id="loginTab" onclick="switchTab('login')">Login</button>
            <button class="auth-tab" id="signupTab" onclick="switchTab('signup')">Sign Up</button>
        </div>
        <div class="auth-body">
            <div class="auth-alert" id="authAlert"></div>

            <!-- LOGIN PANEL -->
            <div class="auth-panel active" id="loginPanel">
                <h2 class="auth-heading">Welcome Back</h2>
                <p class="auth-sub">Sign in to your EUT account</p>
                <a href="{{ route('auth.google') }}" class="auth-google">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Continue with Google
                </a>
                <div class="auth-divider">or continue with email</div>
                <div class="auth-field">
                    <label class="auth-label">Email Address</label>
                    <input type="email" class="auth-input" id="loginEmail" placeholder="you@example.com" autocomplete="email">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Password</label>
                    <input type="password" class="auth-input" id="loginPassword" placeholder="••••••••" autocomplete="current-password">
                </div>
                <button class="btn-login" onclick="doLogin()">Sign In →</button>
            </div>

            <!-- SIGNUP PANEL -->
            <div class="auth-panel" id="signupPanel">
                <h2 class="auth-heading" style="color:#f59e0b;">Create Account</h2>
                <p class="auth-sub">Join EUT and start ordering today</p>
                <a href="{{ route('auth.google') }}" class="auth-google">
                    <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                    Sign up with Google
                </a>
                <div class="auth-divider">or sign up with email</div>
                <div class="auth-field">
                    <label class="auth-label">Full Name</label>
                    <input type="text" class="auth-input" id="signupName" placeholder="Juan dela Cruz" autocomplete="name">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Email Address</label>
                    <input type="email" class="auth-input" id="signupEmail" placeholder="you@example.com" autocomplete="email">
                </div>
                <div class="auth-field">
                    <label class="auth-label">Password</label>
                    <input type="password" class="auth-input" id="signupPassword" placeholder="Min. 8 characters" autocomplete="new-password">
                </div>
                <button class="btn-signup" onclick="doSignup()">Create Account →</button>
            </div>
        </div>
    </div>
</div>

<script>
/* ══════════════════════════════════════════════════════
   NAV SCROLL EFFECT
══════════════════════════════════════════════════════ */
window.addEventListener('scroll', function() {
    const nav = document.getElementById('mainNav');
    if (window.scrollY > 40) {
        nav.classList.add('scrolled');
    } else {
        nav.classList.remove('scrolled');
    }
}, { passive: true });

/* ══════════════════════════════════════════════════════
   MOBILE MENU
══════════════════════════════════════════════════════ */
function toggleMobileMenu() {
    document.getElementById('mobileMenu').classList.toggle('open');
}
function closeMobileMenu() {
    document.getElementById('mobileMenu').classList.remove('open');
}

/* ══════════════════════════════════════════════════════
   MENU CATEGORY FILTER
══════════════════════════════════════════════════════ */
document.getElementById('menuCats').addEventListener('click', function(e) {
    const btn = e.target.closest('.menu-cat');
    if (!btn) return;
    document.querySelectorAll('.menu-cat').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const cat = btn.dataset.cat;
    document.querySelectorAll('.menu-card').forEach(card => {
        if (cat === 'all' || card.dataset.cat === cat) {
            card.style.display = '';
            card.style.animation = 'none';
            card.offsetHeight; // reflow
            card.style.animation = '';
        } else {
            card.style.display = 'none';
        }
    });
});

/* ══════════════════════════════════════════════════════
   STATS COUNT-UP
══════════════════════════════════════════════════════ */
function formatNum(n, format) {
    if (format === 'abbr') {
        if (n >= 1000000) return (n / 1000000).toFixed(0) + 'M';
        if (n >= 1000) return (n / 1000).toFixed(0) + 'K';
    }
    return n.toLocaleString();
}

function animateCounter(el) {
    const target = parseInt(el.dataset.target, 10);
    const suffix = el.dataset.suffix || '';
    const format = el.dataset.format || '';
    const duration = 1800;
    const steps = 60;
    const increment = target / steps;
    let current = 0;
    let step = 0;
    const timer = setInterval(() => {
        step++;
        current = Math.min(Math.round(increment * step), target);
        el.textContent = formatNum(current, format) + suffix;
        if (step >= steps) clearInterval(timer);
    }, duration / steps);
}

const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.querySelectorAll('.stat-num').forEach(animateCounter);
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.4 });

const statsBar = document.getElementById('statsBar');
if (statsBar) statsObserver.observe(statsBar);

/* ══════════════════════════════════════════════════════
   FEATURES STAGGER ANIMATION
══════════════════════════════════════════════════════ */
const featObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const cards = entry.target.querySelectorAll('.feat-card');
            cards.forEach((card, i) => {
                setTimeout(() => card.classList.add('visible'), i * 90);
            });
            featObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1 });

const featGrid = document.getElementById('featGrid');
if (featGrid) featObserver.observe(featGrid);

/* ══════════════════════════════════════════════════════
   FAQ ACCORDION
══════════════════════════════════════════════════════ */
function toggleFaq(item) {
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}

/* ══════════════════════════════════════════════════════
   AUTH MODAL
══════════════════════════════════════════════════════ */
function openModal(tab) {
    const modal = document.getElementById('authModal');
    modal.classList.add('open');
    switchTab(tab || 'login');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('authModal').classList.remove('open');
    document.body.style.overflow = '';
    clearAlert();
}

document.getElementById('authModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});

function switchTab(tab) {
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginPanel = document.getElementById('loginPanel');
    const signupPanel = document.getElementById('signupPanel');
    clearAlert();
    if (tab === 'login') {
        loginTab.classList.add('active');
        signupTab.classList.remove('active');
        loginPanel.classList.add('active');
        signupPanel.classList.remove('active');
    } else {
        signupTab.classList.add('active');
        loginTab.classList.remove('active');
        signupPanel.classList.add('active');
        loginPanel.classList.remove('active');
    }
}

function showAlert(msg, type) {
    const el = document.getElementById('authAlert');
    el.textContent = msg;
    el.className = 'auth-alert ' + (type || 'error');
    el.style.display = 'block';
}

function clearAlert() {
    const el = document.getElementById('authAlert');
    el.style.display = 'none';
    el.textContent = '';
}

/* ══════════════════════════════════════════════════════
   LOGIN FETCH
══════════════════════════════════════════════════════ */
async function doLogin() {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    if (!email || !password) { showAlert('Please enter your email and password.'); return; }
    clearAlert();
    try {
        const res = await fetch('{{ route("auth.login") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email, password })
        });
        const data = await res.json();
        if (res.ok && data.redirect) {
            window.location.href = data.redirect;
        } else {
            showAlert(data.message || 'Invalid credentials. Please try again.');
        }
    } catch (err) {
        showAlert('Something went wrong. Please try again.');
    }
}

/* ══════════════════════════════════════════════════════
   SIGNUP FETCH
══════════════════════════════════════════════════════ */
async function doSignup() {
    const name = document.getElementById('signupName').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    const password = document.getElementById('signupPassword').value;
    if (!name || !email || !password) { showAlert('Please fill in all fields.'); return; }
    if (password.length < 8) { showAlert('Password must be at least 8 characters.'); return; }
    clearAlert();
    try {
        const res = await fetch('{{ route("auth.signup") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, email, password, password_confirmation: password })
        });
        const data = await res.json();
        if (res.ok && data.redirect) {
            window.location.href = data.redirect;
        } else {
            showAlert(data.message || 'Could not create account. Please try again.');
        }
    } catch (err) {
        showAlert('Something went wrong. Please try again.');
    }
}

/* ══════════════════════════════════════════════════════
   ENTER KEY SUPPORT
══════════════════════════════════════════════════════ */
document.getElementById('loginPassword').addEventListener('keydown', e => { if (e.key === 'Enter') doLogin(); });
document.getElementById('signupPassword').addEventListener('keydown', e => { if (e.key === 'Enter') doSignup(); });
</script>
</body>
</html>
