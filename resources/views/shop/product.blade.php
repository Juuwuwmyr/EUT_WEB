<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $item['name'] }} - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* ── NAVBAR ── */
        .topnav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(8,8,16,0.94); backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .topnav-inner {
            max-width: 560px; margin: 0 auto;
            padding: 13px 16px; display: flex; align-items: center; gap: 10px;
        }
        .back-btn {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            color: #9ca3af; text-decoration: none; transition: all 0.2s; flex-shrink: 0;
        }
        .back-btn:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .nav-title { font-size: 15px; font-weight: 600; color: #fff; flex: 1;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .nav-cart-btn {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            color: #9ca3af; text-decoration: none; transition: all 0.2s;
            position: relative; flex-shrink: 0;
        }
        .cart-dot {
            position: absolute; top: -3px; right: -3px;
            min-width: 16px; height: 16px; border-radius: 99px;
            background: #ef4444; border: 2px solid #080810;
            font-size: 9px; font-weight: 800; color: #fff;
            display: flex; align-items: center; justify-content: center; padding: 0 3px;
        }

        /* ── PRODUCT IMAGE HERO ── */
        .product-hero {
            position: relative; overflow: hidden;
            background: #0e0f1a;
        }
        .product-hero-img {
            width: 100%; aspect-ratio: 1 / 1; object-fit: cover;
            display: block; max-height: 380px;
        }
        .product-hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, transparent 50%, rgba(8,8,16,0.9) 100%);
        }
        .hero-badge-hot {
            position: absolute; top: 16px; left: 16px;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            color: #fff; font-size: 11px; font-weight: 800;
            padding: 5px 12px; border-radius: 99px;
            box-shadow: 0 3px 12px rgba(220,38,38,0.5);
        }
        .hero-badge-rating {
            position: absolute; top: 16px; right: 16px;
            background: rgba(0,0,0,0.65); backdrop-filter: blur(8px);
            border: 1px solid rgba(250,204,21,0.3);
            color: #facc15; font-size: 11px; font-weight: 700;
            padding: 5px 10px; border-radius: 99px;
            display: flex; align-items: center; gap: 4px;
        }

        /* ── PAGE BODY ── */
        .page-body { max-width: 560px; margin: 0 auto; padding: 62px 0 180px; }

        /* ── PRODUCT INFO ── */
        .info-section { padding: 20px 18px 0; }
        .info-category {
            font-size: 11px; color: #ef4444; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;
        }
        .info-name {
            font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700;
            color: #fff; line-height: 1.25; margin-bottom: 8px;
        }
        .info-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }
        .info-rating {
            display: flex; align-items: center; gap: 4px;
            font-size: 12px; color: #facc15; font-weight: 600;
        }
        .info-reviews { font-size: 12px; color: #4b5563; }
        .info-sold {
            font-size: 12px; color: #4b5563;
            padding-left: 10px; border-left: 1px solid rgba(255,255,255,0.07);
        }
        .info-desc { font-size: 13px; color: #6b7280; line-height: 1.65; margin-bottom: 16px; }
        .info-price-row { display: flex; align-items: baseline; gap: 10px; margin-bottom: 6px; }
        .info-price { font-size: 28px; font-weight: 800; color: #facc15; }
        .info-price-note { font-size: 11px; color: #4b5563; }
        .info-stock {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 11px; color: #4ade80; font-weight: 600;
            background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.18);
            padding: 3px 10px; border-radius: 99px; margin-bottom: 20px;
        }

        /* ── DIVIDER ── */
        .divider { height: 1px; background: rgba(255,255,255,0.05); margin: 0 18px; }

        /* ── DESCRIPTION CARD ── */
        .desc-card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px; margin: 16px 14px;
            padding: 16px 18px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .desc-card-title { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .desc-card-text { font-size: 13px; color: #6b7280; line-height: 1.65; }

        /* ── BOTTOM SHEET TRIGGER ── */
        .buy-bar {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 200;
            background: rgba(8,8,16,0.97); backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255,255,255,0.07);
            padding: 14px 16px 24px;
            max-width: 560px; margin: 0 auto; left: 50%; transform: translateX(-50%);
        }
        /* for screens smaller than 560px, full width */
        @media (max-width: 560px) { .buy-bar { left: 0; transform: none; max-width: 100%; } }
        .buy-bar-inner { display: flex; gap: 10px; }
        .btn-add-cart {
            flex: 1; padding: 14px; border-radius: 14px;
            background: rgba(250,204,21,0.1); border: 1.5px solid rgba(250,204,21,0.35);
            color: #facc15; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: all 0.2s; text-align: center;
        }
        .btn-add-cart:hover { background: rgba(250,204,21,0.18); }
        .btn-buy-now {
            flex: 1.6; padding: 14px; border-radius: 14px;
            background: linear-gradient(135deg, #f59e0b, #facc15);
            border: none; color: #000; font-size: 14px; font-weight: 800;
            cursor: pointer; transition: all 0.2s; text-align: center;
            box-shadow: 0 4px 18px rgba(250,204,21,0.3);
        }
        .btn-buy-now:hover { box-shadow: 0 6px 24px rgba(250,204,21,0.45); transform: translateY(-1px); }

        /* ════════════════════════════
           BOTTOM SHEET MODAL
        ════════════════════════════ */
        .sheet-backdrop {
            position: fixed; inset: 0; z-index: 300;
            background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);
            opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        .sheet-backdrop.open { opacity: 1; pointer-events: all; }

        .sheet {
            position: fixed; bottom: 0; left: 50%; transform: translateX(-50%) translateY(100%);
            width: 100%; max-width: 560px; z-index: 400;
            background: #0e0f1a;
            border-radius: 24px 24px 0 0;
            border: 1px solid rgba(255,255,255,0.08);
            border-bottom: none;
            transition: transform 0.4s cubic-bezier(0.32, 0.72, 0, 1);
            max-height: 92vh; overflow-y: auto;
        }
        @media (max-width: 560px) { .sheet { left: 0; transform: translateY(100%); } }
        .sheet.open { transform: translateX(-50%) translateY(0); }
        @media (max-width: 560px) { .sheet.open { transform: translateY(0); } }

        /* Sheet handle */
        .sheet-handle {
            width: 40px; height: 4px; border-radius: 99px;
            background: rgba(255,255,255,0.15); margin: 12px auto 0;
        }

        /* Sheet header */
        .sheet-header { display: flex; align-items: center; gap: 14px; padding: 14px 18px 12px; }
        .sheet-thumb {
            width: 68px; height: 68px; border-radius: 14px; object-fit: cover;
            flex-shrink: 0; box-shadow: 0 4px 14px rgba(0,0,0,0.5);
        }
        .sheet-item-name {
            font-family: 'Playfair Display', serif;
            font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 3px;
        }
        .sheet-item-price-row { display: flex; align-items: center; gap: 8px; }
        .sheet-price { font-size: 20px; font-weight: 800; color: #facc15; }
        .sheet-price-note { font-size: 11px; color: #4b5563; }
        .sheet-close {
            margin-left: auto; width: 32px; height: 32px; border-radius: 50%;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #6b7280; flex-shrink: 0; transition: all 0.2s;
        }
        .sheet-close:hover { background: rgba(255,255,255,0.12); color: #fff; }

        .sheet-divider { height: 1px; background: rgba(255,255,255,0.06); margin: 0 18px; }

        /* Section labels */
        .sheet-section { padding: 16px 18px 8px; }
        .sheet-section-title {
            font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 12px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .sheet-section-title span { font-size: 11px; color: #4b5563; font-weight: 400; }

        /* ── FLAVOR SWATCHES ── */
        .flavor-grid { display: flex; flex-wrap: wrap; gap: 10px; }
        .flavor-swatch {
            position: relative; cursor: pointer;
            border-radius: 12px; overflow: hidden;
            border: 2px solid transparent;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .flavor-swatch.selected { border-color: #facc15; box-shadow: 0 0 0 1px #facc15, 0 4px 14px rgba(250,204,21,0.3); }
        .flavor-swatch-inner {
            width: 72px; height: 72px; border-radius: 10px;
            display: flex; align-items: flex-end; justify-content: center;
            padding-bottom: 6px; position: relative; overflow: hidden;
        }
        .flavor-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .flavor-color-overlay { position: absolute; inset: 0; }
        .flavor-name {
            position: relative; z-index: 1;
            font-size: 10px; font-weight: 700; color: #fff;
            text-shadow: 0 1px 4px rgba(0,0,0,0.8);
            text-align: center; line-height: 1.2;
        }
        .flavor-hot-dot {
            position: absolute; top: 4px; right: 4px; z-index: 2;
            font-size: 11px;
        }
        .flavor-check {
            position: absolute; bottom: 4px; right: 4px; z-index: 2;
            width: 16px; height: 16px; border-radius: 50%;
            background: #facc15; display: none;
            align-items: center; justify-content: center;
        }
        .flavor-swatch.selected .flavor-check { display: flex; }

        /* ── SIZE PILLS ── */
        .size-grid { display: flex; gap: 10px; flex-wrap: wrap; }
        .size-pill {
            padding: 10px 20px; border-radius: 12px;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.08);
            cursor: pointer; transition: all 0.2s;
            text-align: center; min-width: 64px;
        }
        .size-pill-label { font-size: 14px; font-weight: 700; color: #e5e7eb; display: block; }
        .size-pill-desc { font-size: 10px; color: #4b5563; display: block; margin-top: 1px; }
        .size-pill:hover { background: rgba(255,255,255,0.09); border-color: rgba(255,255,255,0.15); }
        .size-pill.selected {
            background: rgba(250,204,21,0.1); border-color: #facc15;
            box-shadow: 0 2px 12px rgba(250,204,21,0.2);
        }
        .size-pill.selected .size-pill-label { color: #facc15; }
        .size-pill.selected .size-pill-desc { color: #92400e; }

        /* ── QUANTITY ── */
        .qty-section { padding: 8px 18px 16px; display: flex; align-items: center; justify-content: space-between; }
        .qty-label { font-size: 13px; font-weight: 700; color: #fff; }
        .qty-controls {
            display: flex; align-items: center; gap: 0;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1); border-radius: 99px;
        }
        .qty-btn {
            width: 38px; height: 38px; border-radius: 99px;
            background: none; border: none; cursor: pointer;
            font-size: 18px; font-weight: 700; color: #9ca3af;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .qty-btn:hover { color: #fff; background: rgba(255,255,255,0.08); }
        .qty-val {
            width: 36px; text-align: center; font-size: 15px; font-weight: 700;
            color: #fff; background: none; border: none; outline: none;
            -moz-appearance: textfield;
        }
        .qty-val::-webkit-inner-spin-button, .qty-val::-webkit-outer-spin-button { -webkit-appearance: none; }

        /* ── SHEET PRICE SUMMARY ── */
        .sheet-total-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 18px 14px;
        }
        .sheet-total-label { font-size: 12px; color: #4b5563; }
        .sheet-total-val { font-size: 20px; font-weight: 800; color: #facc15; }

        /* ── SHEET ACTION BUTTONS ── */
        .sheet-actions { display: flex; gap: 10px; padding: 0 18px 32px; }
        .sheet-btn-add {
            flex: 1; padding: 15px; border-radius: 14px;
            background: rgba(250,204,21,0.1); border: 1.5px solid rgba(250,204,21,0.35);
            color: #facc15; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.2s;
        }
        .sheet-btn-add:hover { background: rgba(250,204,21,0.18); }
        .sheet-btn-buy {
            flex: 1.6; padding: 15px; border-radius: 14px;
            background: linear-gradient(135deg, #f59e0b, #facc15);
            border: none; color: #000; font-size: 14px; font-weight: 800;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 4px 18px rgba(250,204,21,0.3);
        }
        .sheet-btn-buy:hover { box-shadow: 0 6px 24px rgba(250,204,21,0.45); transform: translateY(-1px); }

        /* ── LIGHT MODE ── */
        .light-mode body { background: #f0f0f8 !important; }
        .light-mode .topnav { background: rgba(255,255,255,0.96) !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .back-btn, .light-mode .nav-cart-btn { background: rgba(0,0,0,0.05) !important; border-color: rgba(0,0,0,0.08) !important; color: #555 !important; }
        .light-mode .nav-title { color: #111 !important; }
        .light-mode .info-name { color: #111 !important; }
        .light-mode .desc-card { background: #fff !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .desc-card-text { color: #9ca3af !important; }
        .light-mode .desc-card-title { color: #111 !important; }
        .light-mode .buy-bar { background: rgba(255,255,255,0.97) !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .sheet { background: #fff !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .sheet-item-name, .light-mode .qty-label { color: #111 !important; }
        .light-mode .size-pill { background: rgba(0,0,0,0.04) !important; border-color: rgba(0,0,0,0.1) !important; }
        .light-mode .size-pill-label { color: #374151 !important; }
        .light-mode .qty-controls { background: rgba(0,0,0,0.04) !important; border-color: rgba(0,0,0,0.1) !important; }
        .light-mode .qty-val { color: #111 !important; }
        .light-mode .sheet-divider { background: rgba(0,0,0,0.06) !important; }

        /* ── BOTTOM NAV ── */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(8,8,16,0.97); border-top: 1px solid rgba(255,255,255,0.07);
            backdrop-filter: blur(20px); padding: 10px 0 14px; z-index: 100;
        }
        @media (min-width: 1024px) { .bottom-nav { display: none; } }
        .bottom-nav-inner { display: flex; }
        .bnav-item {
            flex: 1; display: flex; flex-direction: column; align-items: center;
            gap: 3px; color: #4b5563; text-decoration: none;
            font-size: 10px; font-weight: 500; transition: color 0.15s;
        }
        .bnav-item.active { color: #facc15; }
    </style>
</head>
<body>

<!-- ══════════ NAVBAR ══════════ -->
<nav class="topnav">
    <div class="topnav-inner">
        <a href="{{ route('shop.home') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="nav-title">{{ $item['name'] }}</span>
        <button id="shopThemeToggle" class="nav-cart-btn" style="color:#9ca3af;">
            <svg id="shopSunIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#facc15;display:none;">
                <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg id="shopMoonIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#9ca3af;">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
        </button>
        <a href="{{ route('shop.cart') }}" class="nav-cart-btn">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span class="cart-dot" id="cartBadge" style="display:none;">0</span>
        </a>
    </div>
</nav>

<!-- ══════════ PAGE BODY ══════════ -->
<div class="page-body">

    <!-- Hero image -->
    <div class="product-hero">
        <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="product-hero-img">
        <div class="product-hero-overlay"></div>
        @if(!empty($item['featured']))
            <span class="hero-badge-hot">🔥 Hot Item</span>
        @endif
        <span class="hero-badge-rating">
            <svg width="11" height="11" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
            4.9 · {{ rand(120,4800) }} reviews
        </span>
    </div>

    <!-- Info section -->
    <div class="info-section">
        <p class="info-category">{{ ucfirst($item['category']['name'] ?? 'Food') }}</p>
        <h1 class="info-name">{{ $item['name'] }}</h1>
        <div class="info-meta">
            <span class="info-rating">
                <svg width="12" height="12" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                4.9
            </span>
            <span class="info-reviews">{{ rand(120,4800) }} reviews</span>
            <span class="info-sold">{{ rand(500,9999) }}+ sold</span>
        </div>
        <p class="info-desc">{{ $item['description'] }}</p>
        <div class="info-price-row">
            <span class="info-price">₱{{ number_format($item['price'], 0) }}</span>
            <span class="info-price-note">base price</span>
        </div>
        <div class="info-stock">
            <svg width="10" height="10" fill="#4ade80" viewBox="0 0 20 20"><circle cx="10" cy="10" r="5"/></svg>
            In Stock · Ready to prepare
        </div>
    </div>

    <div class="divider"></div>

    <!-- Description card -->
    <div class="desc-card">
        <p class="desc-card-title">About this item</p>
        <p class="desc-card-text">{{ $item['description'] }}</p>
        @if(!empty($item['ingredients']))
        <div style="margin-top:12px;">
            <p style="font-size:11px; font-weight:700; color:#6b7280; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.06em;">Ingredients</p>
            <div style="display:flex; flex-wrap:wrap; gap:6px;">
                @foreach($item['ingredients'] as $ing)
                <span style="background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); color:#9ca3af; padding:4px 10px; border-radius:99px; font-size:11px;">{{ $ing }}</span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Nutrition info card -->
    <div class="desc-card" style="margin-top:0;">
        <p class="desc-card-title">Nutritional Info</p>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:4px;">
            <div style="background:rgba(255,255,255,0.04); border-radius:10px; padding:10px; text-align:center;">
                <p style="font-size:16px; font-weight:800; color:#facc15;">~650</p>
                <p style="font-size:10px; color:#4b5563; margin-top:2px;">Calories</p>
            </div>
            <div style="background:rgba(255,255,255,0.04); border-radius:10px; padding:10px; text-align:center;">
                <p style="font-size:16px; font-weight:800; color:#60a5fa;">~28g</p>
                <p style="font-size:10px; color:#4b5563; margin-top:2px;">Protein</p>
            </div>
            <div style="background:rgba(255,255,255,0.04); border-radius:10px; padding:10px; text-align:center;">
                <p style="font-size:16px; font-weight:800; color:#f87171;">~42g</p>
                <p style="font-size:10px; color:#4b5563; margin-top:2px;">Carbs</p>
            </div>
            <div style="background:rgba(255,255,255,0.04); border-radius:10px; padding:10px; text-align:center;">
                <p style="font-size:16px; font-weight:800; color:#a78bfa;">~22g</p>
                <p style="font-size:10px; color:#4b5563; margin-top:2px;">Fats</p>
            </div>
        </div>
    </div>

</div><!-- /page-body -->

<!-- ══════════ FIXED BUY BAR ══════════ -->
<div class="buy-bar">
    <div class="buy-bar-inner">
        <button class="btn-add-cart" onclick="openSheet('cart')">+ Add to Cart</button>
        <button class="btn-buy-now" onclick="openSheet('buy')">Buy Now →</button>
    </div>
</div>

<!-- ══════════ BOTTOM SHEET MODAL ══════════ -->
<div class="sheet-backdrop" id="sheetBackdrop" onclick="closeSheet()"></div>
<div class="sheet" id="buySheet">
    <div class="sheet-handle"></div>

    <!-- Sheet header -->
    <div class="sheet-header">
        <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" class="sheet-thumb">
        <div style="flex:1; min-width:0;">
            <p class="sheet-item-name">{{ $item['name'] }}</p>
            <div class="sheet-item-price-row">
                <span class="sheet-price" id="sheetPrice">₱{{ number_format($item['price'], 0) }}</span>
                <span class="sheet-price-note">· <span id="sheetQtyLabel">1</span> serving</span>
            </div>
            <div style="display:flex; align-items:center; gap:4px; margin-top:3px;">
                <div style="width:8px;height:8px;background:#4ade80;border-radius:50%;"></div>
                <span style="font-size:11px;color:#4ade80;font-weight:600;">In Stock</span>
            </div>
        </div>
        <button class="sheet-close" onclick="closeSheet()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="sheet-divider"></div>

    <!-- ── MODIFIER GROUPS (DB-driven, fully dynamic) ── -->
    <div id="modifierGroupsContainer">
        <!-- JS populated -->
    </div>

    <div class="sheet-divider"></div>

    <!-- ── QUANTITY ── -->
    <div class="qty-section">
        <span class="qty-label">Quantity</span>
        <div class="qty-controls">
            <button class="qty-btn" id="qtyDec">−</button>
            <input type="number" class="qty-val" id="sheetQty" value="1" min="1" max="99">
            <button class="qty-btn" id="qtyInc">+</button>
        </div>
    </div>

    <!-- Total -->
    <div class="sheet-divider"></div>
    <div class="sheet-total-row">
        <span class="sheet-total-label">Total</span>
        <span class="sheet-total-val" id="sheetTotal">₱{{ number_format($item['price'], 0) }}</span>
    </div>

    <!-- Action buttons -->
    <div class="sheet-actions">
        <button class="sheet-btn-add" id="sheetAddBtn">+ Add to Cart</button>
        <button class="sheet-btn-buy" id="sheetBuyBtn">Buy Now →</button>
    </div>
</div>

<!-- ══════════ BOTTOM NAV ══════════ -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
            Menu
        </a>
        <a href="{{ route('shop.tracking') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Orders
        </a>
        <a href="{{ route('shop.cart') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Cart
        </a>
        <a href="{{ route('shop.profile') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profile
        </a>
    </div>
</nav>

<script>
/* ── Theme ── */
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
    buildModifierGroups();
    bindQty();
    bindActions();
});

/* ── Cart badge ── */
let cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
function updateCartBadge() {
    const total = cart.reduce((s,i) => s + i.quantity, 0);
    const el = document.getElementById('cartBadge');
    el.textContent = total;
    el.style.display = total > 0 ? 'flex' : 'none';
}

const BASE_PRICE = {{ $item['price'] }};
const ITEM_ID    = {{ $item['id'] }};
const ITEM_NAME  = @json($item['name']);
const ITEM_IMAGE = @json($item['image']);
const ITEM_CAT   = @json($item['category']['slug'] ?? 'food');

// ── Modifier groups from DB ──────────────────────────────
const MODIFIER_GROUPS = @json($item['modifier_groups'] ?? []);

let sheetMode      = 'cart';
let selectedOptions = {}; // group_id → option object
let currentQty      = 1;

/* ── COLORS for flavor swatches (cycle if no color in DB) ── */
const SWATCH_COLORS = [
    'linear-gradient(135deg,#b45309,#92400e)',
    'linear-gradient(135deg,#dc2626,#b91c1c)',
    'linear-gradient(135deg,#292524,#57534e)',
    'linear-gradient(135deg,#854d0e,#ca8a04)',
    'linear-gradient(135deg,#f59e0b,#ef4444)',
    'linear-gradient(135deg,#3f3f46,#1c1917)',
    'linear-gradient(135deg,#1d4ed8,#1e3a8a)',
    'linear-gradient(135deg,#15803d,#14532d)',
];

function buildModifierGroups() {
    const container = document.getElementById('modifierGroupsContainer');
    // Filter out addon groups — they're not customer-selectable options
    const visibleGroups = MODIFIER_GROUPS.filter(g => g.type !== 'addon');

    if (!visibleGroups.length) {
        container.innerHTML = '';
        return;
    }

    container.innerHTML = visibleGroups.map((group, gi) => {
        const isFlavor = group.type === 'flavor';
        const divider  = gi < visibleGroups.length - 1
            ? '<div style="height:1px;background:rgba(255,255,255,0.06);margin:0 18px;"></div>' : '';

        const optionsHtml = isFlavor
            ? buildFlavorOptions(group)
            : buildPillOptions(group);

        return `
        <div class="sheet-section" id="group_${group.id}">
            <p class="sheet-section-title">
                ${group.name}
                <span id="label_${group.id}">${group.required ? 'Required' : 'Select one'}</span>
            </p>
            ${optionsHtml}
        </div>
        ${divider}`;
    }).join('');

    // Pre-select only explicitly marked defaults — no fallback to first
    MODIFIER_GROUPS.forEach(group => {
        if (group.type === 'addon') return; // skip addons
        const def = group.active_options.find(o => o.is_default);
        if (def) {
            selectedOptions[group.id] = def;
            const label = document.getElementById('label_' + group.id);
            if (label) label.textContent = def.name;
        } else {
            // No default — leave unselected, show 'Select one'
            selectedOptions[group.id] = null;
        }
    });

    updateTotal();
}

function buildFlavorOptions(group) {
    return `<div class="flavor-grid">` +
        group.active_options.map((opt) => {
            const i = group.active_options.indexOf(opt);
            const color = SWATCH_COLORS[i % SWATCH_COLORS.length];
            const isSelected = !!opt.is_default;
            return `<div class="flavor-swatch${isSelected ? ' selected' : ''}" id="opt_${group.id}_${opt.id}"
                        onclick="selectOption(${group.id}, ${opt.id}, true)">
                <div class="flavor-swatch-inner" style="background:${color};">
                    <span class="flavor-name">${opt.name}</span>
                    <span class="flavor-check">
                        <svg width="9" height="9" fill="none" stroke="#000" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                </div>
            </div>`;
        }).join('') +
    `</div>`;
}

function buildPillOptions(group) {
    return `<div class="size-grid">` +
        group.active_options.map((opt) => {
            const isSelected = !!opt.is_default;
            const adj = parseFloat(opt.price_adjustment || 0);
            const priceLabel = opt.price_type === 'add' && adj > 0
                ? '+₱' + adj.toLocaleString()
                : opt.price_type === 'replace'
                    ? '₱' + adj.toLocaleString()
                    : 'Free';
            return `<div class="size-pill${isSelected ? ' selected' : ''}" id="opt_${group.id}_${opt.id}"
                        onclick="selectOption(${group.id}, ${opt.id}, false)">
                <span class="size-pill-label">${opt.name}</span>
                <span class="size-pill-desc">${priceLabel}</span>
            </div>`;
        }).join('') +
    `</div>`;
}

function selectOption(groupId, optId, isFlavor) {
    const group = MODIFIER_GROUPS.find(g => g.id === parseInt(groupId));
    if (!group) return;
    const current = selectedOptions[group.id];
    const isSame  = current && parseInt(current.id) === parseInt(optId);

    // Always deselect all options in this group first
    group.active_options.forEach(o => {
        const el = document.getElementById('opt_' + group.id + '_' + o.id);
        if (el) el.classList.remove('selected');
    });

    if (isSame) {
        // Tap same option again → unselect
        selectedOptions[group.id] = null;
        const label = document.getElementById('label_' + group.id);
        if (label) label.textContent = group.required ? 'Required' : 'Select one';
    } else {
        // Select the new option
        const opt = group.active_options.find(o => parseInt(o.id) === parseInt(optId));
        if (!opt) return;
        const chosen = document.getElementById('opt_' + group.id + '_' + opt.id);
        if (chosen) chosen.classList.add('selected');
        selectedOptions[group.id] = opt;
        const label = document.getElementById('label_' + group.id);
        if (label) label.textContent = opt.name;
    }

    updateTotal();
}

/* ── QTY ── */
function bindQty() {
    document.getElementById('qtyDec').addEventListener('click', () => {
        const v = parseInt(document.getElementById('sheetQty').value);
        if (v > 1) { document.getElementById('sheetQty').value = v - 1; currentQty = v - 1; updateTotal(); }
    });
    document.getElementById('qtyInc').addEventListener('click', () => {
        const v = parseInt(document.getElementById('sheetQty').value);
        document.getElementById('sheetQty').value = v + 1; currentQty = v + 1; updateTotal();
    });
    document.getElementById('sheetQty').addEventListener('input', () => {
        currentQty = Math.max(1, parseInt(document.getElementById('sheetQty').value) || 1);
        document.getElementById('sheetQty').value = currentQty;
        updateTotal();
    });
}

function updateTotal() {
    let price = parseFloat(BASE_PRICE);

    Object.entries(selectedOptions).forEach(([groupId, opt]) => {
        if (!opt) return; // unselected group
        // Skip addon groups — they don't affect price
        const group = MODIFIER_GROUPS.find(g => g.id === parseInt(groupId));
        if (!group || group.type === 'addon') return;

        const adj = parseFloat(opt.price_adjustment || 0);
        if (opt.price_type === 'add') {
            price += adj;
        } else if (opt.price_type === 'replace') {
            price = adj;
        }
    });

    const unit  = Math.round(price);
    const total = unit * currentQty;
    document.getElementById('sheetPrice').textContent    = '₱' + unit.toLocaleString();
    document.getElementById('sheetTotal').textContent    = '₱' + total.toLocaleString();
    document.getElementById('sheetQtyLabel').textContent = currentQty;
}

/* ── SHEET OPEN / CLOSE ── */
function openSheet(mode) {
    sheetMode = mode;
    document.getElementById('sheetAddBtn').textContent = mode === 'buy' ? 'Add to Cart' : '+ Add to Cart';
    document.getElementById('sheetBuyBtn').textContent = mode === 'buy' ? 'Buy Now →'   : 'Buy Now →';
    document.getElementById('sheetBackdrop').classList.add('open');
    document.getElementById('buySheet').classList.add('open');
    document.body.style.overflow = 'hidden';
    updateTotal();
}
function closeSheet() {
    document.getElementById('sheetBackdrop').classList.remove('open');
    document.getElementById('buySheet').classList.remove('open');
    document.body.style.overflow = '';
}

/* ── ADD / BUY ── */
function bindActions() {
    document.getElementById('sheetAddBtn').addEventListener('click', () => doAdd(false));
    document.getElementById('sheetBuyBtn').addEventListener('click', () => doAdd(true));
}

function doAdd(goToCart) {
    let price = BASE_PRICE;
    Object.values(selectedOptions).forEach(opt => {
        if (!opt) return;
        if (opt.price_type === 'add')     price += parseFloat(opt.price_adjustment || 0);
        else if (opt.price_type === 'replace') price = parseFloat(opt.price_adjustment);
    });
    const unit = Math.round(price);

    // Build a label from selected options for display
    const optLabels = Object.values(selectedOptions).filter(Boolean).map(o => o.name);
    const suffix    = optLabels.length ? ' (' + optLabels.join(', ') + ')' : '';
    const name      = ITEM_NAME + suffix;

    // Unique cart key per item+option combo
    const optKey = Object.values(selectedOptions).filter(Boolean).map(o => o.id).sort().join('-');
    const key    = ITEM_ID + (optKey ? '_' + optKey : '');

    const existing = cart.find(i => i.id === key);
    if (existing) existing.quantity += currentQty;
    else cart.push({ id: key, name, price: unit, image: ITEM_IMAGE, category: ITEM_CAT, quantity: currentQty });

    localStorage.setItem('eutCart', JSON.stringify(cart));
    updateCartBadge();
    closeSheet();
    if (goToCart) window.location.href = '{{ route("shop.cart") }}';
    else showToast('Added to cart! 🛒');
}

function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    Object.assign(t.style, {
        position:'fixed', bottom:'90px', left:'50%', transform:'translateX(-50%)',
        background:'#1a1b2e', border:'1px solid rgba(250,204,21,0.3)',
        color:'#facc15', padding:'10px 20px', borderRadius:'99px',
        fontSize:'13px', fontWeight:'600', zIndex:'9999',
        boxShadow:'0 4px 20px rgba(0,0,0,0.5)',
        animation:'fadeInUp 0.3s ease',
    });
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}
</script>
<style>
@keyframes fadeInUp { from{opacity:0;transform:translateX(-50%) translateY(10px)} to{opacity:1;transform:translateX(-50%) translateY(0)} }
</style>
</body>
</html>
