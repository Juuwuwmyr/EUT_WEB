<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EUT Restaurant - Menu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* ── TOP PROMO BANNER ── */
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

        /* ── NAVBAR ── */
        .topnav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(8,8,16,0.96);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
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

        /* ── HERO ── */
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
        .hero-text { position: relative; z-index: 1; max-width: 55%; }
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
        .hero-pills { display: flex; gap: 8px; flex-wrap: wrap; }
        .hero-pill {
            display: flex; align-items: center; gap: 5px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 99px; padding: 5px 12px;
            font-size: 11px; color: #9ca3af; font-weight: 500;
        }
        .hero-img {
            position: absolute;
            right: -30px; bottom: 0;
            height: 100%; max-height: 220px;
            width: auto;
            object-fit: contain; object-position: right bottom;
            z-index: 1;
            filter: drop-shadow(-8px 0 20px rgba(0,0,0,0.5));
        }

        /* ── CATEGORIES ── */
        .cats-wrap {
            position: sticky; top: 62px; z-index: 90;
            background: rgba(8,8,16,0.96);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.05);
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

        /* ── SECTION HEADER ── */
        .section-head {
            max-width: 1200px; margin: 0 auto;
            padding: 24px 16px 12px;
            display: flex; align-items: baseline; justify-content: space-between;
        }
        .section-title { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 700; color: #fff; }
        .section-count { font-size: 12px; color: #4b5563; }

        /* ── PRODUCT GRID ── */
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

        /* ── PRODUCT CARD ── */
        .p-card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 18px; overflow: hidden;
            transition: transform 0.22s ease, border-color 0.22s, box-shadow 0.22s;
            box-shadow: 0 4px 16px rgba(0,0,0,0.4);
            display: flex; flex-direction: column;
        }
        .p-card:hover {
            transform: translateY(-4px);
            border-color: rgba(250,204,21,0.3);
            box-shadow: 0 8px 28px rgba(0,0,0,0.5), 0 0 0 1px rgba(250,204,21,0.15);
        }
        .p-card-img-wrap { position: relative; overflow: hidden; }
        .p-card-img {
            width: 100%; aspect-ratio: 1 / 1; object-fit: cover;
            transition: transform 0.4s ease;
            display: block;
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

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center; padding: 60px 24px;
            grid-column: 1 / -1;
        }
        .empty-state-icon { font-size: 52px; margin-bottom: 14px; opacity: 0.5; }
        .empty-state-text { font-size: 15px; color: #6b7280; }

        /* ── LIGHT MODE ── */
        .light-mode body { background: #f0f0f8 !important; }
        .light-mode .topnav { background: rgba(255,255,255,0.96) !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .cats-wrap { background: rgba(255,255,255,0.96) !important; border-color: rgba(0,0,0,0.06) !important; }
        .light-mode .nav-logo-text { color: #d97706 !important; }
        .light-mode .search-input { background: #fff !important; border-color: rgba(0,0,0,0.15) !important; color: #111 !important; }
        .light-mode .nav-icon-btn { background: rgba(0,0,0,0.05) !important; border-color: rgba(0,0,0,0.08) !important; color: #374151 !important; }
        .light-mode .p-card { background: #fff !important; border-color: rgba(0,0,0,0.07) !important; box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important; }
        .light-mode .p-card-name { color: #111 !important; }
        .light-mode .p-card-desc { color: #9ca3af !important; }
        .light-mode .hero-card { background: linear-gradient(135deg, #fff5f5, #fffbeb, #fff) !important; border-color: rgba(220,38,38,0.15) !important; }
        .light-mode .hero-sub { color: #9ca3af !important; }
        .light-mode .cat-pill { background: rgba(0,0,0,0.04) !important; border-color: rgba(0,0,0,0.09) !important; color: #6b7280 !important; }
        .light-mode .cat-pill:hover { background: rgba(0,0,0,0.08) !important; color: #111 !important; }
        .light-mode .cats-wrap { background: rgba(255,255,255,0.97) !important; border-color: rgba(0,0,0,0.06) !important; }
        .light-mode .section-title { color: #111 !important; }

        /* ── BOTTOM NAV ── */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(8,8,16,0.97);
            border-top: 1px solid rgba(255,255,255,0.07);
            backdrop-filter: blur(20px);
            padding: 10px 0 14px; z-index: 100;
        }
        @media (min-width: 1024px) { .bottom-nav { display: none; } }
        .bottom-nav-inner { display: flex; }
        .bnav-item {
            flex: 1; display: flex; flex-direction: column; align-items: center;
            gap: 3px; color: #4b5563; text-decoration: none;
            font-size: 10px; font-weight: 500; transition: color 0.15s;
        }
        .bnav-item.active { color: #facc15; }

        /* Animations */
        @keyframes fade-up { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .p-card { animation: fade-up 0.3s ease both; }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="topnav">
    <div class="topnav-inner">
        <div class="search-wrap" style="max-width:100%;">
            <input type="text" id="searchInput" class="search-input" placeholder="Search burgers, fries, drinks…">
            <button class="search-btn">
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

<!-- ── HERO ── -->
<div class="hero">
    <div class="hero-card">
        <div class="hero-text">
            <div class="hero-badge"><span class="hero-badge-dot"></span> Open Now</div>
            <h1 class="hero-title">EUT Restaurant</h1>
            <p class="hero-sub">Eat • Unwind • Tea — Delivered Fast</p>
            <div class="hero-pills">
                <span class="hero-pill">🚀 30–45 min</span>
                <span class="hero-pill">⭐ 4.9 Rating</span>
                <span class="hero-pill">📍 Metro Manila</span>
            </div>
        </div>
        <img src="{{ asset('images/DeliveryPanda.png') }}" alt="Delivery Panda" class="hero-img">
    </div>
</div>

<!-- ── CATEGORIES ── -->
<div class="cats-wrap">
    <div style="max-width:1200px; margin:0 auto; padding:14px 0 14px 16px; display:flex; gap:10px; overflow-x:scroll; overflow-y:visible; -webkit-overflow-scrolling:touch; scrollbar-width:none; flex-wrap:nowrap; -ms-overflow-style:none;" id="catsRow">
        <button class="cat-pill active" data-category="all" style="flex-shrink:0;">
            <span style="font-size:17px; line-height:1;">🍽️</span> All
        </button>
        @php
            $iconMap = [
                'beef'    => '🍔',
                'flame'   => '🍟',
                'coffee'  => '🥤',
                'package' => '🍱',
                'tag'     => '🏷️',
            ];
        @endphp
        @foreach($categories as $cat)
        <button class="cat-pill" data-category="{{ $cat->slug }}" style="flex-shrink:0;">
            <span style="font-size:17px; line-height:1;">{{ $iconMap[$cat->icon] ?? '🍽️' }}</span> {{ $cat->name }}
        </button>
        @endforeach
        <span style="flex-shrink:0; width:20px; display:inline-block;"></span>
    </div>
</div>

<!-- ── PRODUCTS ── -->
<div class="section-head">
    <h2 class="section-title">Our Menu</h2>
    <span class="section-count" id="visibleCount"></span>
</div>

<div class="products-grid" id="productsGrid">
    @foreach($menuItems as $index => $item)
    <div class="p-card" data-category="{{ $item->category->slug ?? '' }}" data-name="{{ strtolower($item->name) }}" style="animation-delay: {{ $index * 0.04 }}s;">
        <a href="{{ route('shop.product', $item->id) }}" style="text-decoration:none; display:block;">
            <div class="p-card-img-wrap">
                <img src="{{ $item->image ? asset($item->image) : 'https://placehold.co/400x300/1a1a2e/facc15?text=' . urlencode($item->name) }}" alt="{{ $item->name }}" class="p-card-img" loading="lazy">
                <div class="p-card-img-overlay"></div>
                @if($item->featured)
                    <span class="badge-hot">🔥 Hot</span>
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
                    <span class="p-card-price">₱{{ number_format($item->price, 0) }}</span>
                    <span class="p-card-sold">{{ rand(200,4800) }}+ sold</span>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>

<!-- ── BOTTOM NAV ── -->
<div style="height:80px;" class="lg:hidden"></div>
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item active">
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
    updateCount();
});

/* ── Cart badge ── */
let cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
function updateCartBadge() {
    const total = cart.reduce((s, i) => s + i.quantity, 0);
    const badge = document.getElementById('cartBadge');
    badge.textContent = total;
    badge.style.display = total > 0 ? 'flex' : 'none';
}



/* ── Category filter ── */
document.querySelectorAll('.cat-pill').forEach(pill => {
    pill.addEventListener('click', () => {
        document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
        pill.classList.add('active');
        filterProducts();
    });
});

/* ── Search ── */
document.getElementById('searchInput').addEventListener('input', filterProducts);

function filterProducts() {
    const cat    = document.querySelector('.cat-pill.active')?.dataset.category || 'all';
    const query  = document.getElementById('searchInput').value.toLowerCase().trim();
    let visible  = 0;
    document.querySelectorAll('.p-card').forEach(card => {
        const matchCat  = cat === 'all' || card.dataset.category === cat;
        const matchName = !query || card.dataset.name.includes(query);
        const show = matchCat && matchName;
        card.style.display = show ? 'flex' : 'none';
        if (show) visible++;
    });
    updateCount(visible);
}

function updateCount(n) {
    const all = document.querySelectorAll('.p-card').length;
    const count = n !== undefined ? n : all;
    document.getElementById('visibleCount').textContent = count + ' items';
}
</script>
</body>
</html>
