<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E.U.T Snack House - Menu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet"></noscript>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; overscroll-behavior-y: none; }
        body { background: #080810; color: #fff; min-height: 100vh; overscroll-behavior-y: none; }

        /* -- TOP PROMO BANNER -- */
        .promo-banner {
            background: linear-gradient(90deg, #dc2626, #b45309, #facc15);
            text-align: center; padding: 9px 16px;
            font-size: 12px; font-weight: 600; color: #fff;
            letter-spacing: 0.02em; position: relative; overflow: hidden;
        }
        .promo-banner::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
            animation: shimmer 3s infinite;
        }
        @keyframes shimmer { 0%{transform:translateX(-100%)} 100%{transform:translateX(100%)} }

        /* -- NAVBAR -- */
        .topnav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(8,8,16,1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            will-change: transform;
            transform: translate3d(0,0,0);
        }
        .topnav-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 12px 16px;
            display: flex; align-items: center; gap: 12px;
        }
        .nav-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; }
        .nav-logo-icon { font-size: 26px; }
        .nav-logo-text { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #facc15; }

        .search-wrap { flex: 1; max-width: 480px; position: relative; }
        .search-input {
            width: 100%; padding: 10px 46px 10px 16px;
            background: rgba(255,255,255,0.92);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 12px; color: #111; font-size: 13px;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-input::placeholder { color: #9ca3af; }
        .search-input:focus { border-color: rgba(250,204,21,0.7); background: #fff; box-shadow: 0 0 0 3px rgba(250,204,21,0.15); }
        .search-btn {
            position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
            width: 30px; height: 30px; border-radius: 8px;
            background: linear-gradient(135deg, #f59e0b, #facc15);
            border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .search-btn:hover { box-shadow: 0 2px 8px rgba(250,204,21,0.4); }

        .nav-actions { display: flex; align-items: center; gap: 6px; margin-left: auto; }
        .nav-icon-btn {
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #9ca3af; text-decoration: none;
            transition: all 0.2s; position: relative;
        }
        .nav-icon-btn:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .cart-badge-dot {
            position: absolute; top: -4px; right: -4px;
            min-width: 18px; height: 18px; border-radius: 99px;
            background: #ef4444; border: 2px solid #080810;
            font-size: 10px; font-weight: 700; color: #fff;
            display: flex; align-items: center; justify-content: center;
            padding: 0 4px;
        }

        /* -- HERO -- */
        .hero {
            max-width: 1200px; margin: 0 auto;
            padding: 24px 16px 0;
        }
        .hero-card {
            background: linear-gradient(135deg, #1a0506 0%, #1a0d00 50%, #0e0f1a 100%);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px; overflow: hidden;
            position: relative; min-height: 220px;
            padding: 28px 28px 28px;
            transform: translate3d(0,0,0);
        }
        .hero-card::before {
            content: '';
            position: absolute; top: -40px; left: -40px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(220,38,38,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-card::after {
            content: '';
            position: absolute; bottom: -20px; right: 200px;
            width: 160px; height: 160px;
            background: radial-gradient(circle, rgba(250,204,21,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-text { position: relative; z-index: 2; max-width: 55%; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);
            color: #f87171; font-size: 11px; font-weight: 700;
            padding: 4px 10px; border-radius: 99px; margin-bottom: 10px;
        }
        .hero-badge-dot { width: 6px; height: 6px; background: #ef4444; border-radius: 50%; animation: blink 1.2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }
        .hero-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: #fff; margin-bottom: 6px; line-height: 1.2; }
        .hero-sub { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
        .hero-pills { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
        .hero-pill {
            display: flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 99px; padding: 5px 12px;
            font-size: 11px; color: #9ca3af; font-weight: 500;
        }

        /* -- ORDER TYPE SWITCHER -- */
        .order-type-switcher {
            display: flex; gap: 6px; flex-wrap: nowrap;
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px; padding: 4px;
            width: fit-content; max-width: 100%;
        }
        .ot-btn {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 14px; border-radius: 9px; border: none;
            background: transparent; color: #6b7280;
            font-size: 12px; font-weight: 600; cursor: pointer;
            transition: all 0.2s; white-space: nowrap;
        }
        .ot-btn:hover { color: #d1d5db; background: rgba(255,255,255,0.06); }
        .ot-btn.active {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff;
            box-shadow: 0 2px 10px rgba(220,38,38,0.4);
        }
        .ot-btn.active svg { stroke: #fff; }
        .ot-btn.locked { opacity: 0.3; cursor: not-allowed !important; pointer-events: none; }
        .ot-btn.locked:hover { background: transparent; color: #6b7280; }
        .light-mode .order-type-switcher { background: rgba(0,0,0,0.06) !important; border-color: rgba(0,0,0,0.1) !important; }
        .light-mode .ot-btn { color: #6b7280 !important; }
        .light-mode .ot-btn.active { background: linear-gradient(135deg,#dc2626,#ef4444) !important; color: #fff !important; }

        @media (max-width: 480px) {
            .hero-text { max-width: 100%; }
            .hero-img { opacity: 0.25; right: -10px; }
            .order-type-switcher { width: 100%; justify-content: stretch; }
            .ot-btn { flex: 1; justify-content: center; }
        }
        .hero-img {
            position: absolute;
            right: -30px; bottom: 0;
            height: 100%; max-height: 220px;
            width: auto;
            object-fit: contain; object-position: right bottom;
            z-index: 1; /* hero-text is z-index:2, so buttons stay clickable */
            filter: drop-shadow(-8px 0 20px rgba(0,0,0,0.5));
            pointer-events: none; /* image never intercepts clicks */
        }

        /* -- CATEGORIES -- */
        .cats-wrap {
            position: sticky; top: 62px; z-index: 90;
            background: rgba(8,8,16,1);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            will-change: transform;
            transform: translate3d(0,0,0);
        }
        .cats-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 14px 16px;
            display: flex; gap: 8px;
            overflow-x: scroll;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            flex-wrap: nowrap;
        }
        .cats-inner::-webkit-scrollbar { display: none; }
        .cat-pill {
            flex-shrink: 0;
            padding: 9px 20px; border-radius: 99px;
            font-size: 13px; font-weight: 600; cursor: pointer;
            border: 1px solid rgba(255,255,255,0.09);
            background: rgba(255,255,255,0.05);
            color: #6b7280; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 7px;
            white-space: nowrap; user-select: none;
            -webkit-tap-highlight-color: transparent;
        }
        .cat-pill:hover { background: rgba(255,255,255,0.1); color: #d1d5db; }
        .cat-pill.active {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            border-color: transparent; color: #fff;
            box-shadow: 0 3px 14px rgba(220,38,38,0.45);
        }
        .cat-emoji { font-size: 15px; line-height: 1; }

        /* -- SECTION HEADER -- */
        .section-head {
            max-width: 1200px; margin: 0 auto;
            padding: 24px 16px 12px;
            display: flex; align-items: baseline; justify-content: space-between;
        }
        .section-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #fff; }
        .section-count { font-size: 12px; color: #4b5563; }

        /* -- PRODUCT GRID -- */
        .products-grid {
            max-width: 1200px; margin: 0 auto;
            padding: 0 16px 24px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        @media (min-width: 640px) { .products-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (min-width: 900px) { .products-grid { grid-template-columns: repeat(4, 1fr); } }
        @media (min-width: 1100px) { .products-grid { grid-template-columns: repeat(5, 1fr); } }

        /* -- PRODUCT CARD -- */
        .p-card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 18px;
            transition: transform 0.22s ease, border-color 0.22s, box-shadow 0.22s;
            box-shadow: 0 4px 16px rgba(0,0,0,0.4);
            display: flex; flex-direction: column;
            content-visibility: auto;
            contain-intrinsic-size: 0 340px;
            contain: content;
            will-change: transform, opacity;
            transform: translate3d(0,0,0);
            backface-visibility: hidden;
            perspective: 1000px;
        }
        .p-card:hover {
            transform: translateY(-4px);
            border-color: rgba(250,204,21,0.3);
            box-shadow: 0 8px 28px rgba(0,0,0,0.5), 0 0 0 1px rgba(250,204,21,0.15);
        }
        .p-card-img-wrap { 
            position: relative; overflow: hidden; 
            border-radius: 18px 18px 0 0;
            transform: translate3d(0,0,0);
            backface-visibility: hidden;
        }
        .p-card-img {
            width: 100%; aspect-ratio: 1 / 1; object-fit: cover;
            transition: transform 0.4s ease;
            display: block;
            transform: translate3d(0,0,0);
            backface-visibility: hidden;
            will-change: transform;
        }
        .p-card:hover .p-card-img { transform: scale(1.06); }
        .p-card-img-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(8,8,16,0.7) 0%, transparent 50%);
        }
        .badge-hot {
            position: absolute; top: 10px; left: 10px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff; font-size: 10px; font-weight: 800;
            padding: 3px 9px; border-radius: 99px;
            letter-spacing: 0.04em;
            box-shadow: 0 2px 8px rgba(220,38,38,0.5);
        }
        .badge-rating {
            position: absolute; top: 10px; right: 10px;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);
            border: 1px solid rgba(250,204,21,0.25);
            color: #facc15; font-size: 10px; font-weight: 700;
            padding: 3px 8px; border-radius: 99px;
            display: flex; align-items: center; gap: 3px;
        }
        .p-card-body { padding: 12px 12px 0; flex: 1; }
        .p-card-name {
            font-size: 13px; font-weight: 600; color: #f3f4f6;
            line-height: 1.35; margin-bottom: 4px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .p-card-desc {
            font-size: 11px; color: #4b5563; line-height: 1.4; margin-bottom: 8px;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }
        .p-card-price-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 2px; }
        .p-card-price { font-size: 16px; font-weight: 800; color: #facc15; }
        .p-card-sold { font-size: 10px; color: #374151; }
        .p-card-footer { padding: 10px 12px 12px; }
        .add-btn {
            width: 100%; padding: 9px;
            background: linear-gradient(135deg, #f59e0b, #facc15);
            border: none; border-radius: 10px;
            font-size: 12px; font-weight: 700; color: #000;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(250,204,21,0.2);
        }

        /* -- EMPTY STATE -- */
        .empty-state {
            text-align: center; padding: 60px 24px;
            grid-column: 1 / -1;
        }
        .empty-state-icon { font-size: 52px; margin-bottom: 14px; opacity: 0.5; }
        .empty-state-text { font-size: 15px; color: #6b7280; }

        /* -- LIGHT MODE -- */
        .light-mode body { background: #f0f0f8 !important; }
        .light-mode .topnav { background: rgba(255,255,255,0.98) !important; border-color: rgba(0,0,0,0.09) !important; box-shadow: 0 1px 0 rgba(0,0,0,0.06) !important; }
        .light-mode .cats-wrap { background: rgba(255,255,255,0.98) !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .nav-logo-text { color: #d97706 !important; }

        /* Search bar */
        .light-mode .search-input { background: #f3f4f6 !important; border-color: rgba(0,0,0,0.12) !important; color: #111 !important; }
        .light-mode .search-input::placeholder { color: #9ca3af !important; }
        .light-mode .search-input:focus { background: #fff !important; border-color: rgba(220,38,38,0.4) !important; }
        .light-mode .search-btn { background: linear-gradient(135deg, #f59e0b, #facc15) !important; }

        /* Nav icon buttons */
        .light-mode .nav-icon-btn { background: #f3f4f6 !important; border-color: rgba(0,0,0,0.1) !important; color: #374151 !important; }
        .light-mode .nav-icon-btn:hover { background: #e5e7eb !important; color: #111 !important; }
        .light-mode .nav-icon-btn svg { color: #374151 !important; stroke: #374151 !important; }

        /* Hero card */
        .light-mode .hero-card { background: linear-gradient(135deg, #fff5f5 0%, #fffbeb 50%, #f9fafb 100%) !important; border-color: rgba(220,38,38,0.18) !important; }
        .light-mode .hero-title { color: #111 !important; }
        .light-mode .hero-sub { color: #6b7280 !important; }
        .light-mode .hero-badge { background: rgba(239,68,68,0.1) !important; border-color: rgba(239,68,68,0.25) !important; color: #dc2626 !important; }
        .light-mode .hero-pill { background: rgba(0,0,0,0.04) !important; border-color: rgba(0,0,0,0.1) !important; color: #374151 !important; }
        .light-mode .hero-pill svg { stroke: #374151 !important; }

        /* Category pills */
        .light-mode .cat-pill { background: #f3f4f6 !important; border-color: rgba(0,0,0,0.1) !important; color: #6b7280 !important; }
        .light-mode .cat-pill:hover { background: #e5e7eb !important; color: #111 !important; }
        .light-mode .cat-pill.active { background: linear-gradient(135deg,#dc2626,#ef4444) !important; border-color: transparent !important; color: #fff !important; }

        /* Product cards */
        .light-mode .p-card { background: #fff !important; border-color: rgba(0,0,0,0.08) !important; box-shadow: 0 2px 12px rgba(0,0,0,0.07) !important; }
        .light-mode .p-card:hover { border-color: rgba(220,38,38,0.25) !important; box-shadow: 0 6px 24px rgba(0,0,0,0.1) !important; }
        .light-mode .p-card-name { color: #111 !important; }
        .light-mode .p-card-desc { color: #9ca3af !important; }
        .light-mode .p-card-sold { color: #9ca3af !important; }

        /* Section header */
        .light-mode .section-title { color: #111 !important; }
        .light-mode .section-count { color: #9ca3af !important; }

        /* Bottom nav */
        .light-mode .bottom-nav { background: rgba(255,255,255,0.98) !important; border-color: rgba(0,0,0,0.08) !important; }
        .light-mode .bnav-item { color: #9ca3af !important; }
        .light-mode .bnav-item.active { color: #dc2626 !important; }

        /* Empty state */
        .light-mode .empty-state-text { color: #6b7280 !important; }

        /* -- BOTTOM NAV -- */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(8,8,16,1);
            border-top: 1px solid rgba(255,255,255,0.07);
            padding: 10px 0 14px; z-index: 100;
            will-change: transform;
            transform: translate3d(0,0,0);
        }
        @media (min-width: 1024px) { .bottom-nav { display: none; } }
        .bottom-nav-inner { display: flex; }
        .bnav-item {
            flex: 1; display: flex; flex-direction: column; align-items: center;
            gap: 3px; color: #4b5563; text-decoration: none;
            font-size: 10px; font-weight: 500; transition: color 0.15s;
        }
        .bnav-item.active { color: #facc15; }

        /* -- HIDDEN (for JS filtering) -- */
        .p-card-hidden { display: none !important; }

        /* Animations */
        @keyframes fade-up { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .p-card { animation: fade-up 0.3s ease both; }
        .p-card.no-anim { animation: none !important; opacity: 1 !important; transform: none !important; }
        
        @media (prefers-reduced-motion: reduce) {
            .p-card { animation: none !important; transition: none !important; }
            *, *::before, *::after { animation-duration: 0.001ms !important; animation-iteration-count: 1 !important; transition-duration: 0.001ms !important; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body>

{{-- ═══════════════════════════════════════════════════════════
     NAUJAN-ONLY GEO-RESTRICTION OVERLAY
     Checks browser geolocation. If user is outside Naujan
     municipality (> 30 km from town center), blocks the page.
════════════════════════════════════════════════════════════ --}}
<div id="geoOverlay" style="
    display:none; position:fixed; inset:0; z-index:99999;
    background:rgba(4,4,10,0.97);
    backdrop-filter:blur(20px);
    align-items:center; justify-content:center;
    flex-direction:column;
    font-family:'Inter',sans-serif;
    animation: geoFadeIn 0.4s ease;
">
    <style>
        @keyframes geoFadeIn { from{opacity:0;transform:scale(0.97)} to{opacity:1;transform:scale(1)} }
        @keyframes geoPulse  { 0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,0.4)} 60%{box-shadow:0 0 0 20px rgba(239,68,68,0)} }
        @keyframes geoFloat  { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        #geoOverlay .geo-card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(239,68,68,0.3);
            border-radius: 28px;
            padding: 40px 36px;
            max-width: 420px; width: calc(100% - 32px);
            text-align: center;
            box-shadow: 0 40px 80px rgba(0,0,0,0.8), 0 0 0 1px rgba(239,68,68,0.1), 0 0 60px rgba(239,68,68,0.06);
        }
        #geoOverlay .geo-icon-ring {
            width: 88px; height: 88px; border-radius: 50%; margin: 0 auto 24px;
            background: rgba(239,68,68,0.1); border: 2px solid rgba(239,68,68,0.25);
            display: flex; align-items: center; justify-content: center;
            font-size: 40px;
            animation: geoPulse 2.5s ease-in-out infinite, geoFloat 4s ease-in-out infinite;
        }
        #geoOverlay .geo-title {
            font-size: 22px; font-weight: 800; color: #fff;
            margin-bottom: 10px; line-height: 1.25;
        }
        #geoOverlay .geo-subtitle {
            font-size: 13px; color: #6b7280; line-height: 1.7;
            margin-bottom: 20px;
        }
        #geoOverlay .geo-badge {
            display: inline-flex; align-items: center; gap: 7px;
            background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.25);
            color: #f87171; font-size: 12px; font-weight: 700;
            padding: 7px 16px; border-radius: 99px; margin-bottom: 28px;
            letter-spacing: 0.04em; text-transform: uppercase;
        }
        #geoOverlay .geo-dist-box {
            background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);
            border-radius: 14px; padding: 16px 20px; margin-bottom: 24px;
            display: none;
        }
        #geoOverlay .geo-dist-box.visible { display: block; }
        #geoOverlay .geo-dist-label { font-size: 11px; color: #4b5563; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px; }
        #geoOverlay .geo-dist-val   { font-size: 28px; font-weight: 900; color: #ef4444; letter-spacing: -0.02em; }
        #geoOverlay .geo-dist-unit  { font-size: 13px; color: #6b7280; font-weight: 500; }
        #geoOverlay .geo-map-pin {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: #4b5563;
            justify-content: center; margin-bottom: 24px;
        }
        #geoOverlay .geo-back-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff; border: none; border-radius: 14px;
            padding: 14px 32px; font-size: 14px; font-weight: 700; cursor: pointer;
            transition: all 0.2s; width: 100%;
            box-shadow: 0 4px 18px rgba(220,38,38,0.4);
            text-decoration: none;
        }
        #geoOverlay .geo-back-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(220,38,38,0.55); }
        #geoOverlay .geo-checking {
            display: flex; flex-direction: column; align-items: center; gap: 12px;
            color: #9ca3af; font-size: 13px;
        }
        #geoOverlay .geo-spinner {
            width: 32px; height: 32px; border: 3px solid rgba(255,255,255,0.08);
            border-top-color: #facc15; border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <div class="geo-card" id="geoCard">
        {{-- Checking state --}}
        <div class="geo-checking" id="geoChecking">
            <div class="geo-spinner"></div>
            <p>Verifying your location…</p>
        </div>
        {{-- Blocked state --}}
        <div id="geoBlocked" style="display:none;">
            <div class="geo-icon-ring">📍</div>
            <div class="geo-badge">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Outside Coverage Area
            </div>
            <h2 class="geo-title">Exclusive to<br>Naujan, Oriental Mindoro</h2>
            <p class="geo-subtitle">EUT Snack House currently serves customers within <strong style="color:#fff;">Naujan municipality</strong> only. We're working on expanding our delivery zone soon!</p>
            <div class="geo-dist-box" id="geoDistBox">
                <div class="geo-dist-label">Your distance from Naujan</div>
                <div><span class="geo-dist-val" id="geoDistVal">—</span> <span class="geo-dist-unit">km away</span></div>
            </div>
            <div class="geo-map-pin">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                EUT Snack House · Naujan, Oriental Mindoro
            </div>
            <a href="{{ route('restaurant') }}" class="geo-back-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Back to Homepage
            </a>
        </div>
    </div>
</div>

<script>
// ── Naujan Geo-Restriction ──────────────────────────────────────────────────
(function() {
    const NAUJAN_LAT  = 13.3215;
    const NAUJAN_LNG  = 121.3021;
    const MAX_KM      = 30;           // municipality radius
    const CACHE_KEY   = 'eut_geo_ok';
    const CACHE_MS    = 30 * 60 * 1000; // re-check after 30 min

    // Haversine distance in km
    function haversine(lat1, lng1, lat2, lng2) {
        const R = 6371, dLat = (lat2-lat1)*Math.PI/180, dLng = (lng2-lng1)*Math.PI/180;
        const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLng/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    function showOverlay() {
        const overlay = document.getElementById('geoOverlay');
        overlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function hideChecking() {
        document.getElementById('geoChecking').style.display = 'none';
        document.getElementById('geoBlocked').style.display  = 'block';
    }

    function blockUser(distKm) {
        showOverlay();
        hideChecking();
        if (distKm) {
            const box = document.getElementById('geoDistBox');
            document.getElementById('geoDistVal').textContent = Math.round(distKm);
            box.classList.add('visible');
        }
    }

    function geoAllowed(lat, lng) {
        // Store approval with timestamp so we don't keep asking
        sessionStorage.setItem(CACHE_KEY, JSON.stringify({ lat, lng, ts: Date.now() }));
        // Expose coords globally so the checkout can attach them to the order payload
        window.__customerLat = lat;
        window.__customerLng = lng;
    }

    // Check session cache first (avoid re-asking every page load)
    try {
        const cached = JSON.parse(sessionStorage.getItem(CACHE_KEY) || 'null');
        if (cached && (Date.now() - cached.ts) < CACHE_MS) {
            const km = haversine(cached.lat, cached.lng, NAUJAN_LAT, NAUJAN_LNG);
            if (km <= MAX_KM) {
                window.__customerLat = cached.lat;
                window.__customerLng = cached.lng;
                return; // ✅ already verified, skip check
            }
        }
    } catch(e) {}

    // No cache or expired — ask for location
    if (!navigator.geolocation) return; // no GPS? skip gracefully

    showOverlay(); // show "checking" state

    navigator.geolocation.getCurrentPosition(
        function(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const km  = haversine(lat, lng, NAUJAN_LAT, NAUJAN_LNG);
            if (km <= MAX_KM) {
                geoAllowed(lat, lng);
                document.getElementById('geoOverlay').style.display = 'none';
                document.body.style.overflow = '';
            } else {
                blockUser(km);
            }
        },
        function(err) {
            // Permission denied or unavailable — don't block, let backend guard it
            document.getElementById('geoOverlay').style.display = 'none';
            document.body.style.overflow = '';
        },
        { timeout: 8000, maximumAge: 300000 }
    );
})();
</script>


<!-- CLOSED BANNER -->
@if(false) {{-- Shop is always open (controlled by Echo) --}}
<div id="shopClosedBanner" style="background:linear-gradient(135deg,#7f1d1d,#991b1b);border-bottom:1px solid rgba(239,68,68,.4);padding:12px 16px;text-align:center;position:relative;z-index:200;">
    <p style="font-size:13px;font-weight:700;color:#fca5a5;margin:0;display:flex;align-items:center;justify-content:center;gap:8px;">
        <svg width="16" height="16" fill="none" stroke="#fca5a5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        We're currently <strong style="color:#fff;">CLOSED</strong> — orders are not being accepted right now.
    </p>
</div>
@else
<div id="shopClosedBanner" style="display:none;background:linear-gradient(135deg,#7f1d1d,#991b1b);border-bottom:1px solid rgba(239,68,68,.4);padding:12px 16px;text-align:center;position:relative;z-index:200;">
    <p style="font-size:13px;font-weight:700;color:#fca5a5;margin:0;display:flex;align-items:center;justify-content:center;gap:8px;">
        <svg width="16" height="16" fill="none" stroke="#fca5a5" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
        We're currently <strong style="color:#fff;">CLOSED</strong> — orders are not being accepted right now.
    </p>
</div>
@endif

<!-- -- NAVBAR -- -->
<nav class="topnav">
    <div class="topnav-inner">
        <a href="{{ route('shop.home') }}" class="nav-brand" style="font-family:'Playfair Display',serif;font-weight:800;font-size:22px;text-decoration:none;flex-shrink:0;white-space:nowrap;letter-spacing:.05em;line-height:1;">
            <span style="color:#f97316;">E</span><span style="color:#38bdf8;">U</span><span style="color:#ef4444;">T</span>
        </a>
        {{-- Back button — only visible when searching --}}
        <button id="searchBackBtn" onclick="clearSearch()" style="display:none;flex-shrink:0;width:36px;height:36px;border-radius:50%;background:none;border:none;color:#9ca3af;cursor:pointer;align-items:center;justify-content:center;transition:color .15s;" title="Back">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <div class="search-wrap" style="max-width:100%;position:relative;">
            <input type="text" id="searchInput" class="search-input" placeholder="Search burgers, fries, drinks...">
            <button class="search-btn" id="searchClearBtn" onclick="clearSearch()" style="display:none;background:none;border:none;color:#9ca3af;padding:0;position:absolute;right:8px;top:50%;transform:translateY(-50%);cursor:pointer;width:28px;height:28px;display:none;align-items:center;justify-content:center;border-radius:50%;" title="Clear search">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <button class="search-btn" id="searchMagnifier">
                <svg width="14" height="14" fill="none" stroke="#000" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>
        </div>
        <div class="nav-actions">
            <button id="shopThemeToggle" class="nav-icon-btn">
                <svg id="shopSunIcon" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="color:#facc15;display:none;">
                    <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg id="shopMoonIcon" width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="color:#9ca3af;">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>
            <a href="{{ route('shop.cart') }}" class="nav-icon-btn" style="color:#9ca3af;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span class="cart-badge-dot" id="cartBadge" style="display:none;">0</span>
            </a>
            <a href="{{ route('shop.profile') }}" class="nav-icon-btn" style="color:#facc15;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </a>
        </div>
    </div>
</nav>

<!-- -- HERO -- -->
<div class="hero" id="heroSection">
    <div class="hero-card">
        <div class="hero-text">
            <div class="hero-badge"><span class="hero-badge-dot" id="shopStatusDot" style="background:{{ $isOpen ? '#22c55e' : '#ef4444' }};"></span> <span id="shopStatusText">{{ $isOpen ? 'Open Now' : 'Closed' }}</span></div>
            <h1 class="hero-title">E.U.T Snack House</h1>
            <p class="hero-sub">Eat &middot; Unwind &middot; Tea &middot; Delivered Fast</p>
            <div class="hero-pills">
                <span class="hero-pill"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg> 30&ndash;45 min</span>
                <span class="hero-pill"><svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> 4.9 Rating</span>
                <span class="hero-pill"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Metro Naujan</span>
            </div>

            <!-- ── ORDER TYPE SWITCHER ── -->
            <div class="order-type-switcher" id="orderTypeSwitcher">
                <button class="ot-btn active" data-type="delivery" onclick="setOrderType('delivery')">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3"/></svg>
                    Delivery
                </button>
                <button class="ot-btn" data-type="pickup" onclick="setOrderType('pickup')">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Pickup
                </button>
                <button class="ot-btn" data-type="dine_in" onclick="setOrderType('dine_in')">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18M3 7h18M3 11h18M3 15h12M3 19h8"/></svg>
                    Dine In
                </button>
            </div>
        </div>
        <img src="{{ asset('images/DeliveryPanda.webp') }}" alt="Delivery Panda" class="hero-img">
    </div>
</div>

<!-- -- CATEGORIES -- -->
<div class="cats-wrap">
    <div style="max-width:1200px; margin:0 auto; padding:14px 0 14px 16px; display:flex; gap:10px; overflow-x:scroll; overflow-y:visible; -webkit-overflow-scrolling:touch; scrollbar-width:none; flex-wrap:nowrap; -ms-overflow-style:none; will-change:scroll-position; transform:translate3d(0,0,0);" id="catsRow">
        <button class="cat-pill active" data-category="all" style="flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg> All
        </button>
        @php
            $iconMap = [
                'beef'    => 'beef',
                'flame'   => 'flame',
                'coffee'  => 'coffee',
                'package' => 'package',
                'tag'     => 'tag',
            ];
        @endphp
        @foreach($categories as $cat)
        <button class="cat-pill" data-category="{{ $cat->slug }}" style="flex-shrink:0;">
            {{ $cat->name }}
        </button>
        @endforeach
        <span style="flex-shrink:0; width:20px; display:inline-block;"></span>
    </div>
</div>

<!-- -- PRODUCTS -- -->
<div class="section-head" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <h2 class="section-title">Our Menu</h2>
        <span class="section-count" id="visibleCount"></span>
    </div>
    {{-- Sort by price --}}
    <select id="sortSelect" onchange="applySortAndFilter()"
        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:99px;
               color:#d1d5db;font-size:11px;font-weight:600;padding:6px 14px;cursor:pointer;outline:none;
               -webkit-appearance:none;appearance:none;
               background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%239ca3af' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E\");
               background-repeat:no-repeat;background-position:right 10px center;padding-right:28px;">
        <option value="default">Default</option>
        <option value="price_asc">Price: Low → High</option>
        <option value="price_desc">Price: High → Low</option>
    </select>
</div>

<div class="products-grid" id="productsGrid">
    @foreach($menuItems as $index => $item)
    <div class="p-card" data-category="{{ $item->category->slug ?? '' }}" data-name="{{ strtolower($item->name) }}" data-price="{{ $item->price }}">
        <a href="{{ route('shop.product', $item->id) }}" style="text-decoration:none; display:block;">
            <div class="p-card-img-wrap">
                <img src="{{ $item->image ? asset($item->image) : asset('images/menu/default-menu-item.webp') }}" alt="{{ $item->name }}" class="p-card-img" loading="lazy" decoding="async" fetchpriority="low" onerror="this.onerror=null;this.src='{{ asset('images/menu/default-menu-item.webp') }}'">
                <div class="p-card-img-overlay"></div>
                @if($item->featured)
                    <span class="badge-hot" style="display:inline-flex;align-items:center;gap:3px;"><svg width="10" height="10" fill="#fff" viewBox="0 0 24 24"><path d="M17.66 11.2C17.43 10.9 17.15 10.64 16.89 10.38C16.22 9.78 15.46 9.35 14.82 8.72C13.33 7.26 13 4.85 13.95 3C13 3.23 12.17 3.75 11.46 4.32C8.87 6.4 7.85 10.07 9.07 13.22C9.11 13.32 9.15 13.42 9.15 13.55C9.15 13.77 9 13.97 8.8 14.05C8.57 14.15 8.33 14.09 8.14 13.93C8.08 13.88 8.04 13.83 8 13.76C6.87 12.33 6.69 10.28 7.45 8.64C5.78 10 4.87 12.3 5 14.47C5.06 14.97 5.12 15.47 5.29 15.97C5.43 16.57 5.7 17.17 6 17.7C7.08 19.43 8.95 20.67 10.96 20.92C13.1 21.19 15.39 20.8 17.03 19.32C18.86 17.66 19.5 15 18.56 12.72L18.43 12.46C18.22 12 17.66 11.2 17.66 11.2Z"/></svg> Hot</span>
                @endif
                <span class="badge-rating">
                    <svg width="9" height="9" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    4.9
                </span>
            </div>
            <div class="p-card-body">
                <p class="p-card-name">{{ $item->name }}</p>
                <p class="p-card-desc">{{ $item->description }}</p>
                <div class="p-card-price-row">
                    <span class="p-card-price">&#8369;{{ number_format($item->price, 0) }}</span>
                    <span class="p-card-sold">{{ rand(200,4800) }}+ sold</span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

<!-- -- INFINITE SCROLL LOADER -- -->
<div id="infiniteScrollLoader" style="display:none; text-align:center; padding: 20px 0 40px; grid-column: 1 / -1; width: 100%;">
    <div style="display:inline-flex; align-items:center; gap:8px; background:rgba(255,255,255,0.05); padding:8px 16px; border-radius:99px; border:1px solid rgba(255,255,255,0.08);">
        <svg style="animation: spin 1s linear infinite; color:#facc15;" width="16" height="16" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-opacity="0.25"></circle>
            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span style="font-size:12px; color:#9ca3af; font-weight:600;">Loading more tasty items...</span>
    </div>
</div>
<style>
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<!-- -- BOTTOM NAV -- -->
<div style="height:80px;" class="lg:hidden"></div>
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item active">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 6h18M3 10h18M10 14h4M10 18h4M5 3l1 3h12l1-3"/><rect x="4" y="13" width="16" height="8" rx="1" stroke-width="1.75"/></svg>
            Menu
        </a>
        <a href="{{ route('shop.tracking') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Orders
        </a>
        <a href="{{ route('shop.cart') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6" stroke-width="1.75" stroke-linecap="round"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 10a4 4 0 01-8 0"/></svg>
            Cart
        </a>
        <a href="{{ route('shop.profile') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" stroke-width="1.75"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            Profile
        </a>
    </div>
</nav>

<script>
/* -- Theme -- */
function applyTheme(t) {
    document.documentElement.classList.toggle('light-mode', t === 'light');
    document.getElementById('shopSunIcon').style.display  = t === 'dark'  ? 'block' : 'none';
    document.getElementById('shopMoonIcon').style.display = t === 'light' ? 'block' : 'none';
}
document.addEventListener('DOMContentLoaded', () => {
    applyTheme(localStorage.getItem('eutTheme') || 'dark');
    document.getElementById('shopThemeToggle').addEventListener('click', () => {
        const t = (localStorage.getItem('eutTheme') || 'dark') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('eutTheme', t);
        applyTheme(t);
    });
    updateCartBadge();
    updateCount();

    // ── Table QR: if customer arrived via /shop?table=N QR code ─────────────
    // IMPORTANT: Only lock to dine-in if table number is in the current URL
    // or in sessionStorage (active tab session). Never lock from localStorage
    // alone — that would trap logged-in delivery/pickup users from previous sessions.
    const _urlTableParam = new URLSearchParams(window.location.search).get('table');
    const _sessionTable  = sessionStorage.getItem('eutTableNumber');
    const _urlTable      = _urlTableParam || _sessionTable
                        || ({{ auth()->check() ? 'false' : 'true' }} ? localStorage.getItem('eutTableNumber') : null);

    @auth
    // Logged-in user: clear stale dine-in table context unless actively scanning
    if (!_urlTableParam && !_sessionTable) {
        localStorage.removeItem('eutTableNumber');
        sessionStorage.removeItem('eutTableNumber');
        if (localStorage.getItem('eutOrderType') === 'dine_in') {
            localStorage.setItem('eutOrderType', 'delivery');
        }
    }
    @endauth

    if (_urlTable && /^\d{1,2}$/.test(_urlTable.trim())) {
        sessionStorage.setItem('eutTableNumber', _urlTable.trim());
        localStorage.setItem('eutTableNumber', _urlTable.trim());
        localStorage.setItem('eutOrderType', 'dine_in');
        // Only lock the UI if table came from URL or active session — not stale localStorage
        window._tableQrLocked = !!(_urlTableParam || _sessionTable);
    }

    // Restore saved order type
    const savedType = localStorage.getItem('eutOrderType') || 'delivery';
    setOrderType(savedType, false); // false = don't save again on init

    // If table QR locked, gray out Delivery & Pickup buttons
    if (window._tableQrLocked) {
        document.querySelectorAll('.ot-btn[data-type="delivery"], .ot-btn[data-type="pickup"]').forEach(btn => {
            btn.classList.add('locked');
            btn.setAttribute('disabled', 'disabled');
            btn.title = 'Locked to Dine-in — scanned via table QR';
        });
        // Add lock icon to Dine-in button
        const dineBtn = document.querySelector('.ot-btn[data-type="dine_in"]');
        if (dineBtn && !dineBtn.querySelector('.lock-icon')) {
            const lock = document.createElement('span');
            lock.className = 'lock-icon';
            lock.textContent = '🔒';
            lock.style.cssText = 'font-size:9px;margin-left:2px;';
            dineBtn.appendChild(lock);
        }
    }
});

/* -- Cart badge -- */
let cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
function updateCartBadge() {
    const total = cart.reduce((s, i) => s + i.quantity, 0);
    const badge = document.getElementById('cartBadge');
    badge.textContent = total;
    badge.style.display = total > 0 ? 'flex' : 'none';
}

/* -- Order type switcher -- */
function setOrderType(type, save = true) {
    // If locked to dine_in via table QR, refuse any switch away from it
    if (window._tableQrLocked && type !== 'dine_in') return;
    if (save) localStorage.setItem('eutOrderType', type);
    document.querySelectorAll('.ot-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.type === type);
    });
    // Update a visible label in the cart/checkout CTA if present
    const label = document.getElementById('orderTypeLabel');
    const labels = { delivery: '🛵 Delivery', pickup: '📦 Pickup', dine_in: '🪑 Dine In' };
    if (label) label.textContent = labels[type] || labels.delivery;
}



/* -- Category filter -- */
document.querySelectorAll('.cat-pill').forEach(pill => {
    pill.addEventListener('click', () => {
        document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        visibleItemsCount = 0;
        allFilteredCards = [];
        applySortAndFilter();
    });
});

/* -- Search -- */
let searchDebounceTimer;
document.getElementById('searchInput').addEventListener('input', () => {
    clearTimeout(searchDebounceTimer);
    const val = document.getElementById('searchInput').value;
    const clearBtn  = document.getElementById('searchClearBtn');
    const magnifier = document.getElementById('searchMagnifier');
    const hero      = document.getElementById('heroSection');

    if (val.length > 0) {
        // Hide hero, show clear button + back button, hide brand
        if (hero)      { hero.style.display = 'none'; }
        if (clearBtn)  { clearBtn.style.display = 'flex'; }
        if (magnifier) { magnifier.style.display = 'none'; }
        document.getElementById('searchBackBtn').style.display = 'flex';
        document.querySelector('.nav-brand').style.display = 'none';
        // Scroll to top so category pills stick right under navbar
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        // Restore hero, reset buttons, show brand
        if (hero)      { hero.style.display = ''; }
        if (clearBtn)  { clearBtn.style.display = 'none'; }
        if (magnifier) { magnifier.style.display = 'flex'; }
        document.getElementById('searchBackBtn').style.display = 'none';
        document.querySelector('.nav-brand').style.display = '';
    }

    searchDebounceTimer = setTimeout(() => {
        visibleItemsCount = 0;
        allFilteredCards = [];
        applySortAndFilter();
    }, 300);
});

function clearSearch() {
    const input     = document.getElementById('searchInput');
    const clearBtn  = document.getElementById('searchClearBtn');
    const magnifier = document.getElementById('searchMagnifier');
    const hero      = document.getElementById('heroSection');

    input.value = '';
    input.focus();

    // Restore hero
    if (hero)      { hero.style.display = ''; }
    if (clearBtn)  { clearBtn.style.display = 'none'; }
    if (magnifier) { magnifier.style.display = 'flex'; }
    document.getElementById('searchBackBtn').style.display = 'none';
    document.querySelector('.nav-brand').style.display = '';

    visibleItemsCount = 0;
    allFilteredCards = [];
    applySortAndFilter();
}

/* -- Infinite Scroll & Cache State -- */
const ITEMS_PER_PAGE = 8;
let visibleItemsCount = 0;
let allFilteredCards = [];
let isLoaderVisible = false;

// DOM Cache to prevent querying the DOM constantly
const domCache = {
    loader: document.getElementById('infiniteScrollLoader'),
    grid: document.getElementById('productsGrid'),
    searchInput: document.getElementById('searchInput'),
    visibleCount: document.getElementById('visibleCount'),
    cards: Array.from(document.querySelectorAll('.p-card'))
};

function filterProducts() {
    const activeCat = document.querySelector('.cat-pill.active');
    const cat = activeCat ? activeCat.dataset.category : 'all';
    const query = domCache.searchInput.value.toLowerCase().trim();

    // Collect matching cards
    allFilteredCards = [];
    domCache.cards.forEach(card => {
        const matchCat = cat === 'all' || card.dataset.category === cat;
        const matchName = !query || card.dataset.name.includes(query);
        if (matchCat && matchName) {
            allFilteredCards.push(card);
        }
        // Hide everything first; we'll reveal the right slice below
        card.classList.add('p-card-hidden');
        card.classList.remove('no-anim');
    });

    // Apply sort
    const sort = document.getElementById('sortSelect')?.value || 'default';
    if (sort === 'price_asc') {
        allFilteredCards.sort((a, b) => parseFloat(a.dataset.price) - parseFloat(b.dataset.price));
    } else if (sort === 'price_desc') {
        allFilteredCards.sort((a, b) => parseFloat(b.dataset.price) - parseFloat(a.dataset.price));
    }

    // Reorder DOM to match sorted order
    const grid = domCache.grid;
    allFilteredCards.forEach(card => grid.appendChild(card));

    // Determine how many to show in this batch
    visibleItemsCount = Math.min(ITEMS_PER_PAGE, allFilteredCards.length);

    // Show first batch with staggered animation
    allFilteredCards.slice(0, visibleItemsCount).forEach((card, i) => {
        card.classList.remove('p-card-hidden');
        // Force animation replay
        card.style.animation = 'none';
        card.style.opacity   = '';
        card.style.transform = '';
        // Trigger reflow so the browser notices the animation reset
        void card.offsetWidth;
        card.style.animation = '';
        card.style.animationDelay = (i * 0.04) + 's';
    });

    updateCount(allFilteredCards.length);
    checkScrollLoader();
}

function loadMoreItems() {
    if (visibleItemsCount >= allFilteredCards.length || isLoaderVisible) return;

    isLoaderVisible = true;
    if (domCache.loader) {
        domCache.loader.style.display = 'block';
        domCache.loader.style.opacity = '1';
    }

    requestAnimationFrame(() => {
        setTimeout(() => {
            const nextLimit = Math.min(visibleItemsCount + ITEMS_PER_PAGE, allFilteredCards.length);

            for (let i = visibleItemsCount; i < nextLimit; i++) {
                const card = allFilteredCards[i];
                card.classList.add('no-anim');
                card.classList.remove('p-card-hidden');
            }

            visibleItemsCount = nextLimit;

            if (domCache.loader) domCache.loader.style.display = 'none';
            isLoaderVisible = false;
        }, 150);
    });
}

function checkScrollLoader() {
    if (!domCache.loader) return;
    if (visibleItemsCount >= allFilteredCards.length) {
        domCache.loader.style.display = 'none';
    } else {
        domCache.loader.style.display = 'none'; // hidden until scroll triggers it
    }
}

// Simple scroll-based infinite load — no IntersectionObserver race conditions
function onScroll() {
    if (isLoaderVisible) return;
    if (visibleItemsCount >= allFilteredCards.length) return;

    const scrolled   = window.scrollY || document.documentElement.scrollTop;
    const viewHeight = window.innerHeight;
    const docHeight  = document.documentElement.scrollHeight;

    // Load more when user is within 400px of the bottom
    if (scrolled + viewHeight >= docHeight - 400) {
        loadMoreItems();
    }
}

window.addEventListener('scroll', onScroll, { passive: true });

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    applySortAndFilter();
});

function applySortAndFilter() {
    visibleItemsCount = 0;
    allFilteredCards = [];
    filterProducts();
}

function updateCount(n) {
    const all = document.querySelectorAll('.p-card').length;
    const count = n !== undefined ? n : all;
    document.getElementById('visibleCount').textContent = count + ' items';
}

// ── Mobile back button guard ──────────────────────────────
history.pushState({ page: 'shop' }, '', window.location.href);
window.addEventListener('popstate', function(e) {
    history.pushState({ page: 'shop' }, '', window.location.href);
});

// ── Echo: shop open/close status (public channel, no auth needed) ──
if (window.Echo) {
    window.Echo.channel('shop.status')
        .listen('.shop.status', (data) => {
            const banner = document.getElementById('shopClosedBanner');
            const dot    = document.getElementById('shopStatusDot');
            const text   = document.getElementById('shopStatusText');
            if (data.is_open) {
                if (banner) banner.style.display = 'none';
                if (dot)    { dot.style.background = '#22c55e'; }
                if (text)   text.textContent = 'Open Now';
            } else {
                if (banner) banner.style.display = 'block';
                if (dot)    { dot.style.background = '#ef4444'; }
                if (text)   text.textContent = 'Closed';
            }
        });
}

@auth
// ── Echo: live order status banner ────────────────────────
(function() {
    const STATUS_LABELS = {
        pending:          '⏳ Order Placed',
        accepted:         '✅ Order Accepted!',
        preparing:        '👨‍🍳 Kitchen is Cooking',
        rider_assigned:   '🏍️ Rider Assigned',
        out_for_delivery: '🚀 Out for Delivery!',
        delivered:        '🎉 Order Delivered!',
        cancelled:        '❌ Order Cancelled',
    };
    const ACTIVE = ['pending','accepted','preparing','rider_assigned','out_for_delivery'];

    function showOrderBanner(order) {
        let banner = document.getElementById('echoOrderBanner');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'echoOrderBanner';
            Object.assign(banner.style, {
                position:'fixed', bottom:'90px', left:'50%',
                transform:'translateX(-50%) translateY(20px)',
                zIndex:'9998', maxWidth:'360px', width:'calc(100% - 32px)',
                background:'linear-gradient(135deg,#0d1f17,#091510)',
                border:'1px solid rgba(74,222,128,.35)',
                borderRadius:'16px', padding:'12px 16px',
                display:'flex', alignItems:'center', gap:'12px',
                boxShadow:'0 8px 32px rgba(0,0,0,.6)',
                cursor:'pointer', transition:'all .35s cubic-bezier(.32,.72,0,1)',
                opacity:'0',
            });
            banner.addEventListener('click', () => {
                window.location.href = '{{ route("shop.tracking") }}';
            });
            document.body.appendChild(banner);
        }

        const label = STATUS_LABELS[order.status] || order.status_label || order.status;
        const isActive = ACTIVE.includes(order.status);
        const color = order.status === 'delivered' ? '#4ade80'
                    : order.status === 'cancelled' ? '#f87171' : '#4ade80';

        banner.innerHTML = `
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.25);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:18px;">
                ${order.status === 'delivered' ? '🎉' : order.status === 'cancelled' ? '❌' : order.status === 'out_for_delivery' ? '🛵' : '🍔'}
            </div>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:700;color:#fff;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${label}</p>
                <p style="font-size:11px;color:#6b7280;margin:0;">#${order.order_number} · Tap to track</p>
            </div>
            <svg width="14" height="14" fill="none" stroke="#6b7280" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        `;

        // Animate in
        requestAnimationFrame(() => {
            banner.style.opacity = '1';
            banner.style.transform = 'translateX(-50%) translateY(0)';
        });

        // Auto-hide after 6 seconds (keep visible for delivered/cancelled)
        if (order.status !== 'delivered' && order.status !== 'cancelled') {
            clearTimeout(banner._hideTimer);
            banner._hideTimer = setTimeout(() => {
                banner.style.opacity = '0';
                banner.style.transform = 'translateX(-50%) translateY(20px)';
            }, 6000);
        }
    }

    if (window.Echo) {
        window.Echo.private('orders.{{ auth()->id() }}')
            .listen('.order.updated', (order) => {
                showOrderBanner(order);
                // Update cart badge if needed
                updateCartBadge();
            });
    }
})();
@endauth
</script>

{{-- PWA Install Banner --}}
<div id="pwaInstallBanner" style="display:none;position:fixed;bottom:80px;left:50%;transform:translateX(-50%);z-index:9999;width:calc(100% - 32px);max-width:480px;background:linear-gradient(135deg,#12131f,#1a1b2e);border:1px solid rgba(250,204,21,.3);border-radius:16px;padding:14px 16px;align-items:center;gap:12px;box-shadow:0 8px 32px rgba(0,0,0,.6);">
    <img src="/images/icons/icon-72x72.png" style="width:44px;height:44px;border-radius:10px;flex-shrink:0;" alt="EUT">
    <div style="flex:1;min-width:0;">
        <p style="font-size:13px;font-weight:700;color:#fff;margin:0 0 2px;">Install EUT App</p>
        <p style="font-size:11px;color:#9ca3af;margin:0;">Add to home screen for faster access</p>
    </div>
    <button onclick="installPWA()" style="background:#facc15;color:#000;border:none;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:700;cursor:pointer;flex-shrink:0;">Install</button>
    <button onclick="dismissPWABanner()" style="background:none;border:none;color:#6b7280;cursor:pointer;font-size:18px;padding:4px;flex-shrink:0;">×</button>
</div>

@include('partials.pwa-register')
</body>
</html>>
