<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Cart - E.U.T Snack House</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* -- NAVBAR -- */
        .topnav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(8,8,16,1);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            will-change: transform;
            transform: translate3d(0,0,0);
        }
        .topnav-inner {
            max-width: 560px; margin: 0 auto;
            padding: 14px 16px;
            display: flex; align-items: center; gap: 10px;
        }
        .back-btn {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            color: #9ca3af; cursor: pointer; text-decoration: none;
            transition: all 0.2s; flex-shrink: 0;
        }
        .back-btn:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .topnav-title { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #fff; flex: 1; }
        .cart-count-pill {
            background: rgba(250,204,21,0.12); border: 1px solid rgba(250,204,21,0.25);
            color: #facc15; font-size: 11px; font-weight: 700;
            padding: 3px 10px; border-radius: 99px;
        }
        .theme-btn {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; flex-shrink: 0;
        }
        .theme-btn:hover { background: rgba(255,255,255,0.12); }

        /* -- PAGE -- */
        .page-body { max-width: 560px; margin: 0 auto; padding: 82px 16px 120px; }

        /* -- CARDS -- */
        .card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px; overflow: hidden;
            margin-bottom: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.35);
        }
        .card-header {
            padding: 15px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 13px; font-weight: 700; color: #fff; letter-spacing: 0.01em; }
        .card-sub { font-size: 11px; color: #4b5563; margin-top: 2px; }

        /* -- CART ITEMS -- */
        .cart-item {
            display: flex; align-items: center; gap: 13px;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: background 0.15s;
            position: relative;
        }
        .cart-item:last-child { border-bottom: none; }
        .cart-item:hover { background: rgba(255,255,255,0.015); }

        /* Clickable link wrapping image + info � removed, kept for reference */
        .item-edit-hint { display: none; }

        .item-img {
            width: 72px; height: 72px; border-radius: 10px;
            object-fit: cover; flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.35);
        }
        .item-info { flex: 1; min-width: 0; }
        .item-name {
            font-size: 13px; font-weight: 600; color: #f3f4f6;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin-bottom: 3px;
        }
        .item-unit-price { font-size: 11px; color: #4b5563; }
        .item-right { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; }
        .item-total-price { font-size: 14px; font-weight: 800; color: #facc15; }

        /* Qty controls */
        .qty-wrap {
            display: flex; align-items: center; gap: 0;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 99px; overflow: hidden;
        }
        .qty-btn {
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            background: none; border: none; color: #9ca3af;
            cursor: pointer; font-size: 16px; font-weight: 700;
            transition: all 0.15s;
        }
        .qty-btn:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .qty-value {
            width: 28px; text-align: center;
            font-size: 13px; font-weight: 700; color: #fff;
            border: none; background: none;
            -moz-appearance: textfield;
        }
        .qty-value::-webkit-outer-spin-button,
        .qty-value::-webkit-inner-spin-button { -webkit-appearance: none; }

        /* Remove btn */
        .remove-btn {
            position: absolute; top: 10px; right: 14px;
            width: 24px; height: 24px; border-radius: 50%;
            background: rgba(239,68,68,0.08);
            border: 1px solid rgba(239,68,68,0.15);
            display: flex; align-items: center; justify-content: center;
            color: #6b7280; cursor: pointer; transition: all 0.2s;
        }
        .remove-btn:hover { background: rgba(239,68,68,0.18); color: #f87171; border-color: rgba(239,68,68,0.35); }

        /* Swipe hint label */
        .item-category-tag {
            display: inline-block; font-size: 10px; color: #4b5563;
            background: rgba(255,255,255,0.04); border-radius: 4px;
            padding: 1px 6px; margin-top: 4px;
        }

        /* -- PROMO CODE -- */
        .promo-wrap {
            display: flex; gap: 8px; padding: 14px 18px;
        }
        .promo-input {
            flex: 1; background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 10px; padding: 10px 14px;
            font-size: 13px; color: #fff; outline: none;
            transition: border-color 0.2s;
        }
        .promo-input::placeholder { color: #374151; }
        .promo-input:focus { border-color: rgba(250,204,21,0.4); }
        .promo-btn {
            background: rgba(250,204,21,0.1);
            border: 1px solid rgba(250,204,21,0.2);
            color: #facc15; padding: 10px 16px; border-radius: 10px;
            font-size: 12px; font-weight: 700; cursor: pointer;
            transition: all 0.2s; white-space: nowrap;
        }
        .promo-btn:hover { background: rgba(250,204,21,0.18); }

        /* -- ORDER SUMMARY -- */
        .summary-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { font-size: 13px; color: #6b7280; }
        .summary-value { font-size: 13px; color: #9ca3af; font-weight: 500; }
        .summary-label-bold { font-size: 15px; font-weight: 700; color: #fff; }
        .summary-value-bold { font-size: 18px; font-weight: 800; color: #facc15; }
        .free-badge {
            font-size: 10px; font-weight: 700; color: #4ade80;
            background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.2);
            padding: 2px 8px; border-radius: 99px;
        }

        /* -- FREE DELIVERY BANNER -- */
        .free-delivery-bar {
            margin: 0 18px 14px;
            background: rgba(34,197,94,0.06);
            border: 1px solid rgba(34,197,94,0.15);
            border-radius: 10px; padding: 10px 14px;
            display: flex; align-items: center; gap: 10px;
        }
        .free-delivery-fill {
            height: 4px; border-radius: 99px; background: #1a1b2e;
            overflow: hidden; flex: 1;
        }
        .free-delivery-fill-inner {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, #16a34a, #4ade80);
            transition: width 0.6s ease;
        }

        /* -- BUY NOW BAR (sticky) -- */
        .buy-now-bar {
            display: flex; align-items: center; justify-content: space-between;
            background: linear-gradient(135deg, #f59e0b, #facc15);
            color: #000; padding: 14px 18px;
            border-radius: 12px; margin: 0 16px;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(250,204,21,0.25);
            transition: transform 0.2s;
        }
        .buy-now-bar:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(250,204,21,0.5); }
        .buy-now-bar:active { transform: scale(0.98); }
        .buy-now-left { display: flex; align-items: center; gap: 12px; }
        .buy-now-icon { font-size: 22px; }
        .buy-now-label { font-size: 11px; font-weight: 600; opacity: 0.7; letter-spacing: 0.04em; text-transform: uppercase; }
        .buy-now-total { font-size: 20px; font-weight: 900; line-height: 1.1; }
        .buy-now-cta {
            font-size: 15px; font-weight: 800;
            background: rgba(0,0,0,0.12);
            padding: 8px 16px; border-radius: 10px;
            letter-spacing: 0.01em;
        }
        .continue-btn {
            display: block; width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            color: #9ca3af; padding: 13px;
            border-radius: 10px; font-size: 14px; font-weight: 600;
            text-align: center; text-decoration: none;
            margin-top: 10px; transition: all 0.2s;
        }
        .continue-btn:hover { background: rgba(255,255,255,0.08); color: #fff; }


        /* -- TRUST BADGES -- */
        .trust-row {
            display: flex; justify-content: center; gap: 20px;
            padding: 14px 18px;
            border-top: 1px solid rgba(255,255,255,0.04);
        }
        .trust-item {
            display: flex; flex-direction: column; align-items: center;
            gap: 4px; font-size: 10px; color: #4b5563; text-align: center;
        }
        .trust-icon { font-size: 18px; }

        /* -- EMPTY STATE -- */
        .empty-state {
            text-align: center; padding: 80px 24px 40px;
        }
        .empty-bag {
            width: 100px; height: 100px; margin: 0 auto 24px;
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }
        .empty-title { font-size: 20px; font-weight: 800; color: #fff; margin-bottom: 8px; }
        .empty-sub { font-size: 13px; color: #4b5563; margin-bottom: 28px; line-height: 1.6; }
        .empty-suggestions { display: flex; gap: 8px; flex-wrap: wrap; justify-content: center; margin-bottom: 28px; }
        .suggestion-chip {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            color: #9ca3af; padding: 7px 14px; border-radius: 99px;
            font-size: 12px; text-decoration: none; transition: all 0.2s;
        }
        .suggestion-chip:hover { border-color: rgba(250,204,21,0.3); color: #facc15; }

        /* -- UPSELL ROW -- */
        .upsell-scroll { display: flex; gap: 10px; overflow-x: auto; padding: 4px 18px 16px; scrollbar-width: none; }
        .upsell-scroll::-webkit-scrollbar { display: none; }
        .upsell-chip {
            flex-shrink: 0; background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px; padding: 8px 12px;
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; transition: all 0.2s; text-decoration: none;
        }
        .upsell-chip:hover { border-color: rgba(250,204,21,0.3); }
        .upsell-img { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; }
        .upsell-name { font-size: 11px; color: #d1d5db; font-weight: 500; white-space: nowrap; }
        .upsell-price { font-size: 11px; color: #facc15; font-weight: 700; }

        /* -- LIGHT MODE -- */
        .light-mode body { background: #f0f0f8 !important; }
        .light-mode .card { background: #fff !important; border-color: rgba(0,0,0,0.07) !important; box-shadow: 0 2px 16px rgba(0,0,0,0.06) !important; }
        .light-mode .topnav { background: rgba(255,255,255,0.96) !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .card-title, .light-mode .item-name, .light-mode .topnav-title { color: #111 !important; }
        .light-mode .qty-wrap { background: rgba(0,0,0,0.04) !important; border-color: rgba(0,0,0,0.1) !important; }
        .light-mode .qty-btn, .light-mode .qty-value { color: #374151 !important; }
        .light-mode .promo-input { background: rgba(0,0,0,0.04) !important; border-color: rgba(0,0,0,0.1) !important; color: #111 !important; }
        .light-mode .back-btn, .light-mode .theme-btn { background: rgba(0,0,0,0.05) !important; border-color: rgba(0,0,0,0.08) !important; color: #555 !important; }
        .light-mode .summary-row { border-color: rgba(0,0,0,0.05) !important; }
        .light-mode .summary-label { color: #6b7280 !important; }
        .light-mode .summary-value { color: #374151 !important; }
        .light-mode .summary-label-bold { color: #111 !important; }
        .light-mode .empty-bag { background: #fff !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .upsell-chip { background: #fff !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .upsell-name { color: #374151 !important; }

        @keyframes fadeInUp {
            from { opacity:0; transform: translateX(-50%) translateY(12px); }
            to   { opacity:1; transform: translateX(-50%) translateY(0); }
        }


        /* -- BOTTOM NAV -- */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(8,8,16,0.98);
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

        /* Animate in */
        @keyframes slide-up { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .cart-item { animation: slide-up 0.25s ease both; }
    </style>
</head>
<body>

<!-- ---------- NAVBAR ---------- -->
<nav class="topnav">
    <div class="topnav-inner">
        <a href="{{ route('shop.home') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <span class="topnav-title">My Cart</span>
        <span class="cart-count-pill" id="navCartCount">0 items</span>
        <button id="shopThemeToggle" class="theme-btn">
            <svg id="shopSunIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#facc15;display:none;">
                <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <svg id="shopMoonIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#9ca3af;">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
        </button>
    </div>
</nav>

<!-- ---------- PAGE BODY ---------- -->
<div class="page-body">

    @guest
    <!-- ── GUEST GATE — content swapped by JS based on dine-in vs regular guest ── -->
    <div id="guestGate" style="text-align:center; padding: 60px 24px 40px;">

        <!-- Default: login gate (for non-dine-in guests) -->
        <div id="guestLoginGate">
            <div style="width:96px;height:96px;margin:0 auto 24px;background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,0.07);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 32px rgba(0,0,0,0.4);">
                <svg width="44" height="44" fill="none" stroke="#4b5563" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:8px;">Your cart awaits</h2>
            <p style="font-size:14px;color:#6b7280;line-height:1.7;margin-bottom:32px;max-width:280px;margin-left:auto;margin-right:auto;">
                Sign in to view your cart, save items, and place orders.
            </p>
            <div style="display:flex;flex-direction:column;gap:10px;max-width:320px;margin:0 auto 24px;">
                <a href="{{ route('restaurant') }}#login"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 24px;border-radius:10px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;font-size:15px;font-weight:700;text-decoration:none;box-shadow:0 4px 14px rgba(220,38,38,0.3);transition:all 0.2s;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Log In
                </a>
                <a href="{{ route('restaurant') }}#register"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 24px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;font-size:15px;font-weight:700;text-decoration:none;box-shadow:0 4px 12px rgba(245,158,11,0.25);transition:all 0.2s;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    Create Account
                </a>
            </div>
            <a href="{{ route('shop.home') }}" style="font-size:13px;color:#4b5563;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Continue browsing menu
            </a>
        </div>

        <!-- Dine-in guest: empty cart — no login needed -->
        <div id="guestDineInEmptyGate" style="display:none;">
            <div style="width:96px;height:96px;margin:0 auto 24px;background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(250,204,21,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 32px rgba(0,0,0,0.4);">
                <svg width="44" height="44" fill="none" stroke="#facc15" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:8px;">Your cart is empty</h2>
            <p style="font-size:14px;color:#6b7280;line-height:1.7;margin-bottom:8px;max-width:280px;margin-left:auto;margin-right:auto;">
                You're dining at <strong id="guestTableLabel" style="color:#facc15;"></strong> — no login needed.
            </p>
            <p style="font-size:13px;color:#4b5563;margin-bottom:32px;">Browse the menu and add items to get started.</p>
            <a href="{{ route('shop.home') }}"
               style="display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:15px 32px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;font-size:15px;font-weight:800;text-decoration:none;box-shadow:0 4px 16px rgba(245,158,11,0.3);">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h7"/>
                </svg>
                Browse Menu
            </a>
        </div>
    </div>

    {{-- Guest cart — for dine-in guests who have items in localStorage --}}
    <div id="guestCartWrap" style="display:none;">
        <div id="guestCartList" style="padding:8px 0;"></div>
        <div style="padding:12px 18px 8px;border-top:1px solid rgba(255,255,255,.06);">
            <div style="display:flex;justify-content:space-between;font-size:13px;color:#9ca3af;margin-bottom:4px;">
                <span>Subtotal</span><span id="guestSubtotal">₱0</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:11px;color:#4b5563;margin-bottom:12px;">
                <span id="guestItemCount"></span>
                <button onclick="guestClearCart()" style="background:none;border:none;color:#4b5563;font-size:11px;cursor:pointer;padding:0;text-decoration:underline;">Clear all</button>
            </div>
            {{-- Notes — only visible when customer arrived via table QR scan --}}
            <div id="guestDineInNotesWrap" style="display:none;margin-bottom:12px;">
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#a78bfa;margin-bottom:6px;">
                    <svg width="13" height="13" fill="none" stroke="#a78bfa" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Order Notes <span style="font-size:11px;font-weight:400;color:#4b5563;">(optional)</span>
                </label>
                <textarea id="qrDineInNotes" rows="2"
                    placeholder="e.g. less ice, no spicy, extra napkins…"
                    style="width:100%;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.08);border-radius:10px;padding:10px 13px;color:#fff;font-size:13px;resize:none;outline:none;box-sizing:border-box;font-family:inherit;"
                    onfocus="this.style.borderColor='rgba(250,204,21,.4)'" onblur="this.style.borderColor='rgba(255,255,255,.08)'"></textarea>
            </div>
            <a id="guestCheckoutBtn" href="{{ route('shop.checkout') }}" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 24px;border-radius:10px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;font-size:15px;font-weight:700;text-decoration:none;margin-top:8px;">
                Proceed to Checkout →
            </a>
        </div>
        <a href="{{ route('shop.home') }}" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;font-size:13px;color:#4b5563;text-decoration:none;margin-top:4px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Add more items
        </a>
    </div>

    <script>
    // ── Guest cart (dine-in) — fully interactive ─────────────────────────────
    let guestCart = JSON.parse(localStorage.getItem('eutCart') || '[]');
    const _guestTable = sessionStorage.getItem('eutTableNumber') || localStorage.getItem('eutTableNumber');

    // Decide which empty state to show
    (function() {
        if (guestCart.length > 0) {
            document.getElementById('guestGate').style.display = 'none';
            document.getElementById('guestCartWrap').style.display = 'block';
            renderGuestCart();
        } else if (_guestTable) {
            // Dine-in guest with empty cart — show friendly empty state, no login prompt
            document.getElementById('guestLoginGate').style.display = 'none';
            document.getElementById('guestDineInEmptyGate').style.display = 'block';
            const lbl = document.getElementById('guestTableLabel');
            if (lbl) lbl.textContent = 'Table ' + _guestTable;
        }
        // else: regular guest, login gate already visible by default
    })();

    function saveGuestCart() {
        localStorage.setItem('eutCart', JSON.stringify(guestCart));
    }

    function renderGuestCart() {
        const list = document.getElementById('guestCartList');
        if (!list) return;

        if (guestCart.length === 0) {
            // Cart emptied — show appropriate empty state
            document.getElementById('guestCartWrap').style.display = 'none';
            document.getElementById('guestGate').style.display = 'block';
            if (_guestTable) {
                document.getElementById('guestLoginGate').style.display = 'none';
                document.getElementById('guestDineInEmptyGate').style.display = 'block';
                const lbl = document.getElementById('guestTableLabel');
                if (lbl) lbl.textContent = 'Table ' + _guestTable;
            }
            return;
        }

        list.innerHTML = '';
        let sub = 0;
        guestCart.forEach((item, idx) => {
            sub += item.price * item.quantity;
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid rgba(255,255,255,.05);';
            row.innerHTML = `
                <img src="${item.image||''}" style="width:44px;height:44px;border-radius:10px;object-fit:cover;flex-shrink:0;" onerror="this.src='/images/menu/default-menu-item.webp'">
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:600;color:#fff;margin:0 0 4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.name}</p>
                    <p style="font-size:12px;color:#6b7280;margin:0 0 6px;">₱${Number(item.price).toLocaleString()} each</p>
                    <div style="display:flex;align-items:center;gap:0;">
                        <button onclick="guestChangeQty(${idx}, -1)" style="width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#fff;font-size:16px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;">−</button>
                        <span style="width:32px;text-align:center;font-size:13px;font-weight:700;color:#fff;">${item.quantity}</span>
                        <button onclick="guestChangeQty(${idx}, 1)" style="width:28px;height:28px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#fff;font-size:16px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;">+</button>
                    </div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0;">
                    <span style="font-size:13px;font-weight:700;color:#facc15;">₱${(item.price*item.quantity).toLocaleString()}</span>
                    <button onclick="guestRemoveItem(${idx})" style="background:none;border:none;color:#4b5563;cursor:pointer;padding:2px;" title="Remove">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>`;
            list.appendChild(row);
        });

        const totalQty = guestCart.reduce((s,i) => s + i.quantity, 0);
        document.getElementById('guestSubtotal').textContent = '₱' + sub.toLocaleString();
        document.getElementById('guestItemCount').textContent = totalQty + (totalQty === 1 ? ' item' : ' items');
    }

    function guestChangeQty(idx, delta) {
        guestCart[idx].quantity += delta;
        if (guestCart[idx].quantity < 1) guestCart[idx].quantity = 1;
        saveGuestCart();
        renderGuestCart();
    }

    function guestRemoveItem(idx) {
        guestCart.splice(idx, 1);
        saveGuestCart();
        renderGuestCart();
    }

    function guestClearCart() {
        if (!confirm('Remove all items from cart?')) return;
        guestCart = [];
        saveGuestCart();
        renderGuestCart();
    }

    // Initial render
    (function() {
        if (guestCart.length > 0) {
            document.getElementById('guestGate').style.display = 'none';
            document.getElementById('guestCartWrap').style.display = 'block';
            renderGuestCart();
        }
    })();
    </script>
    @endguest

    @auth
    <!-- -- EMPTY STATE -- -->
    <div id="emptyCart" style="display:none;">
        <!-- Empty cart hero -->
        <div style="text-align:center; padding: 48px 24px 32px;">
            <!-- Animated cart icon -->
            <div style="position:relative; width:110px; height:110px; margin:0 auto 28px;">
                <div style="width:110px; height:110px; border-radius:50%;
                    background: linear-gradient(145deg,#1a0d00,#12131f);
                    border:1px solid rgba(245,158,11,0.2);
                    display:flex; align-items:center; justify-content:center;
                    box-shadow: 0 0 0 8px rgba(245,158,11,0.05), 0 0 0 16px rgba(245,158,11,0.025), 0 16px 40px rgba(0,0,0,0.5);">
                    <svg width="48" height="48" fill="none" stroke="url(#cartGrad)" stroke-width="1.5" viewBox="0 0 24 24">
                        <defs>
                            <linearGradient id="cartGrad" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#f59e0b"/>
                                <stop offset="100%" stop-color="#facc15"/>
                            </linearGradient>
                        </defs>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <!-- Floating dots -->
                <div style="position:absolute; top:4px; right:8px; width:14px; height:14px; border-radius:50%; background:rgba(250,204,21,0.15); border:1px solid rgba(250,204,21,0.3);"></div>
                <div style="position:absolute; bottom:8px; left:4px; width:9px; height:9px; border-radius:50%; background:rgba(220,38,38,0.2); border:1px solid rgba(220,38,38,0.3);"></div>
            </div>

            <h2 style="font-family:'Playfair Display',serif; font-size:24px; font-weight:700; color:#fff; margin-bottom:10px; letter-spacing:-0.01em;">
                Your cart is empty
            </h2>
            <p style="font-size:14px; color:#6b7280; line-height:1.7; margin-bottom:32px; max-width:260px; margin-left:auto; margin-right:auto;">
                Looks like you haven't added anything yet. Discover our menu and find something delicious!
            </p>

            <!-- CTA button -->
            <a href="{{ route('shop.home') }}"
               style="display:inline-flex; align-items:center; justify-content:center; gap:9px;
                      padding:15px 32px; border-radius:10px;
                      background:linear-gradient(135deg,#f59e0b,#facc15);
                      color:#000; font-size:15px; font-weight:800; text-decoration:none;
                      box-shadow:0 4px 16px rgba(245,158,11,0.3);
                      transition:all 0.2s; letter-spacing:0.01em; margin-bottom:32px;">
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h7"/>
                </svg>
                Browse Menu
            </a>
        </div>

        <!-- Quick category chips -->
        <div style="padding: 0 20px 20px;">
            <p style="font-size:11px; font-weight:700; color:#4b5563; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:12px;">Popular right now</p>
            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                <a href="{{ route('shop.home') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#9ca3af;font-size:12px;font-weight:600;text-decoration:none;transition:all 0.2s;">
                    🍔 Burgers
                </a>
                <a href="{{ route('shop.home') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#9ca3af;font-size:12px;font-weight:600;text-decoration:none;transition:all 0.2s;">
                    🍟 Fries
                </a>
                <a href="{{ route('shop.home') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#9ca3af;font-size:12px;font-weight:600;text-decoration:none;transition:all 0.2s;">
                    🧋 Drinks
                </a>
                <a href="{{ route('shop.home') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#9ca3af;font-size:12px;font-weight:600;text-decoration:none;transition:all 0.2s;">
                    🍱 Combos
                </a>
                <a href="{{ route('shop.home') }}" style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:99px;background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08);color:#9ca3af;font-size:12px;font-weight:600;text-decoration:none;transition:all 0.2s;">
                    🍰 Snacks
                </a>
            </div>
        </div>

        <!-- Trust strip -->
        <div style="margin:8px 20px 16px; padding:16px 20px; border-radius:12px; background:linear-gradient(145deg,#12131f,#0e0f1a); border:1px solid rgba(255,255,255,0.06);">
            <div style="display:flex; justify-content:space-around; gap:8px;">
                <div style="text-align:center;">
                    <div style="font-size:20px; margin-bottom:4px;">⚡</div>
                    <p style="font-size:10px; font-weight:700; color:#facc15; margin-bottom:2px;">Fast</p>
                    <p style="font-size:10px; color:#4b5563;">30–45 min</p>
                </div>
                <div style="width:1px; background:rgba(255,255,255,0.06);"></div>
                <div style="text-align:center;">
                    <div style="font-size:20px; margin-bottom:4px;">🛡️</div>
                    <p style="font-size:10px; font-weight:700; color:#facc15; margin-bottom:2px;">Secure</p>
                    <p style="font-size:10px; color:#4b5563;">Safe checkout</p>
                </div>
                <div style="width:1px; background:rgba(255,255,255,0.06);"></div>
                <div style="text-align:center;">
                    <div style="font-size:20px; margin-bottom:4px;">⭐</div>
                    <p style="font-size:10px; font-weight:700; color:#facc15; margin-bottom:2px;">4.9 Rating</p>
                    <p style="font-size:10px; color:#4b5563;">Top quality</p>
                </div>
            </div>
        </div>
    </div>

    <!-- -- CART CONTENT -- -->
    <div id="cartContent" style="display:none;">

        <!-- Free delivery progress -->
        <div id="freeDeliveryBar" class="free-delivery-bar" style="display:none;">
            <span style="display:flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12v10H4V12M2 7h20v5H2zM12 22V7m0 0a2 2 0 00-2-2 2 2 0 000 4h2m0-4a2 2 0 012-2 2 2 0 010 4h-2"/></svg></span>
            <div style="flex:1;">
                <p style="font-size:11px; color:#4b5563; margin-bottom:5px;" id="freeDeliveryText">Add &#8369;X more for free delivery</p>
                <div class="free-delivery-fill">
                    <div class="free-delivery-fill-inner" id="freeDeliveryFill" style="width:0%"></div>
                </div>
            </div>
        </div>

        <!-- Cart items card -->
        <div class="card">
            <div class="card-header">
                <div>
                    <p class="card-title">Order Items</p>
                    <p class="card-sub" id="itemSubCount">0 items selected</p>
                </div>
                <button id="clearAllBtn" style="font-size:11px; color:#4b5563; background:none; border:none; cursor:pointer; transition:color 0.2s;" onmouseover="this.style.color='#f87171'" onmouseout="this.style.color='#4b5563'">
                    Clear all
                </button>
            </div>
            <div id="cartItemsList"></div>

            <!-- Upsell -->
            <div style="padding:14px 0 0;">
                <p style="font-size:11px; color:#4b5563; font-weight:600; padding:0 18px 10px; letter-spacing:0.04em; text-transform:uppercase;">You might also like</p>
                <div class="upsell-scroll">
                    @foreach($upsellItems as $upsell)
                    <a href="{{ route('shop.product', $upsell->id) }}" class="upsell-chip">
                        <img src="{{ asset($upsell->image) }}" class="upsell-img" alt="{{ $upsell->name }}" onerror="this.src='{{ asset('images/hero-burger.webp') }}'">
                        <div><p class="upsell-name">{{ $upsell->name }}</p><p class="upsell-price">&#8369;{{ number_format($upsell->price, 0) }}</p></div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Promo code card -->
        <div class="card">
            <div class="card-header">
                <div>
                    <p class="card-title">Promo Code</p>
                    <p class="card-sub">Got a voucher? Apply it here</p>
                </div>
                <svg width="16" height="16" fill="none" stroke="#4b5563" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
            <div class="promo-wrap">
                <input type="text" class="promo-input" id="promoInput" placeholder="Enter code e.g. EUTFREE">
                <button class="promo-btn" onclick="applyPromo()">Apply</button>
            </div>
            <div id="promoMsg" style="padding:0 18px 12px; font-size:12px; display:none;"></div>
        </div>

        <!-- Order summary card -->
        <div class="card">
            <div class="card-header">
                <p class="card-title">Order Summary</p>
            </div>
            <div class="summary-row">
                <span class="summary-label">Subtotal (<span id="totalItems">0</span> items)</span>
                <span class="summary-value" id="subtotal">&#8369;0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Delivery fee</span>
                <span class="summary-value" id="deliveryFeeDisplay">&#8369;30</span>
            </div>
            <div class="summary-row" id="discountRow" style="display:none;">
                <span class="summary-label" style="color:#4ade80;">Promo discount</span>
                <span class="summary-value" id="discountDisplay" style="color:#4ade80;">-&#8369;0</span>
            </div>
            <div class="summary-row" style="padding-top:14px; padding-bottom:14px;">
                <span class="summary-label-bold">Total</span>
                <span class="summary-value-bold" id="grandTotal">&#8369;0</span>
            </div>

            <!-- Trust badges -->
            <div class="trust-row">
                <div class="trust-item"><span class="trust-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"/></svg></span>Secure<br>Payment</div>
                <div class="trust-item"><span class="trust-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3"/></svg></span>Fast<br>Delivery</div>
                <div class="trust-item"><span class="trust-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg></span>Easy<br>Returns</div>
                <div class="trust-item"><span class="trust-icon"><svg width="18" height="18" fill="#facc15" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.538-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg></span>Top<br>Quality</div>
            </div>
        </div>

        <!-- Sticky Buy Now Bar -->
        @if(!$isOpen)
        <div style="margin:0 0 10px;padding:12px 16px;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:10px;text-align:center;display:flex;align-items:center;justify-content:center;gap:8px;">
            <svg width="16" height="16" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <span style="font-size:13px;font-weight:700;color:#f87171;">We're currently <strong>CLOSED</strong> — orders not accepted right now.</span>
        </div>
        <div class="buy-now-bar" id="buyNowBar" style="opacity:.5;cursor:not-allowed;pointer-events:none;background:linear-gradient(135deg,#374151,#4b5563);">
        @else
        <a href="{{ route('shop.checkout') }}" class="buy-now-bar" id="buyNowBar" data-checkout-base="{{ route('shop.checkout') }}">
        @endif
            <div class="buy-now-left">
                <span class="buy-now-icon"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg></span>
                <div>
                    <p class="buy-now-label">{{ $isOpen ? 'Place Order' : 'Shop Closed' }}</p>
                    <p class="buy-now-total" id="buyBarTotal">&#8369;0</p>
                </div>
            </div>
            <span class="buy-now-cta" style="display:inline-flex;align-items:center;gap:5px;">
                {{ $isOpen ? 'Buy Now' : 'Closed' }}
                @if($isOpen)<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>@endif
            </span>
        @if(!$isOpen)</div>@else</a>@endif
        <a href="{{ route('shop.home') }}" class="continue-btn" style="display:inline-flex;align-items:center;gap:6px;">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg> Continue Shopping
        </a>

    </div><!-- /cartContent -->
    @endauth

</div><!-- /page-body -->

<!-- ---------- BOTTOM NAV ---------- -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Home
        </a>
        <a href="{{ route('shop.tracking') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Orders
        </a>
        <a href="{{ route('shop.cart') }}" class="bnav-item active">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Cart
        </a>
        <a href="{{ route('shop.profile') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
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
    renderCart();

    @auth
    // Load server cart and merge after local render (server is source of truth)
    loadServerCart();
    @endauth

    const clearBtn = document.getElementById('clearAllBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            if (confirm('Remove all items from cart?')) {
                cart = [];
                saveCart();
                renderCart();
                @auth serverClearCart(); @endauth
            }
        });
    }

    // -- BUY NOW: validate required flavors before proceeding to checkout --
    const buyNowBar = document.getElementById('buyNowBar');
    if (buyNowBar) {
        buyNowBar.addEventListener('click', e => {
            // Find any item that needs a flavor but doesn't have one confirmed
            const badItem = cart.find(item => item.requires_flavor && !item.flavor_ok);
            if (badItem) {
                e.preventDefault();
                // Highlight the offending cart item
                const el = document.querySelector(`[data-id="${badItem.id}"]`);
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.style.outline = '2px solid #ef4444';
                    el.style.borderRadius = '14px';
                    el.style.transition = 'outline 0.2s';
                    setTimeout(() => el.style.outline = '', 2500);
                }
                showCartToast(`"${badItem.name.split('(')[0].trim()}" needs a flavor selected! Tap the item to fix it.`, badItem.item_id);
                return;
            }
            // All good � let the link navigate to checkout normally
            if (typeof isQrScannedDineIn === 'function' && isQrScannedDineIn()) {
                e.preventDefault();
                handleQrDineInCheckout(e, buyNowBar);
            }
        });
    }

});

let cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
let promoDiscount = 0;
const FREE_DELIVERY_THRESHOLD = 500;

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

@auth
/* ── Server cart sync helpers ── */

async function loadServerCart() {
    try {
        const res = await fetch('/cart/sync', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        });
        if (!res.ok) return;
        const data = await res.json();
        if (data.items && data.items.length > 0) {
            const serverItems = data.items.map(i => ({
                id:           i.cart_key,
                item_id:      i.menu_item_id,
                menu_item_id: i.menu_item_id,
                name:         i.name,
                image:        i.image,
                price:        parseFloat(i.price),
                quantity:     i.quantity,
                category:     i.category,
                modifiers:    i.modifiers || [],
            }));
            // Merge: server items override local by cart_key; local-only items are kept
            const merged = [...serverItems];
            cart.forEach(localItem => {
                if (!merged.find(m => m.id === localItem.id)) {
                    merged.push(localItem);
                }
            });
            cart = merged;
            saveCart();
            renderCart();
        }
    } catch (e) {
        // Silent fail
    }
}

async function serverRemoveItem(cartKey) {
    try {
        await fetch('/cart/item/' + encodeURIComponent(cartKey), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        });
    } catch (e) { /* Silent fail */ }
}

async function serverUpdateQty(cartKey, quantity) {
    try {
        await fetch('/cart/item/' + encodeURIComponent(cartKey), {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
            body: JSON.stringify({ quantity }),
        });
    } catch (e) { /* Silent fail */ }
}

async function serverClearCart() {
    try {
        await fetch('/cart', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        });
    } catch (e) { /* Silent fail */ }
}
@endauth

const PROMOS = {
    'EUTFREE': { type: 'delivery', label: 'Free delivery applied!' },
    'EUT10':   { type: 'percent', value: 10, label: '10% discount applied!' },
    'SAVE50':  { type: 'fixed',   value: 50, label: '&#8369;50 off applied!' },
};

function saveCart() {
    localStorage.setItem('eutCart', JSON.stringify(cart));
}

function renderCart() {
    const empty   = document.getElementById('emptyCart');
    const content = document.getElementById('cartContent');

    // Guest view — these elements don't exist, nothing to render
    if (!empty || !content) return;

    if (!cart.length) {
        empty.style.display   = 'block';
        content.style.display = 'none';
        document.getElementById('navCartCount').textContent = '0 items';
        return;
    }

    empty.style.display   = 'none';
    content.style.display = 'block';

    const totalQty = cart.reduce((s,i) => s + i.quantity, 0);
    document.getElementById('navCartCount').textContent = totalQty + (totalQty === 1 ? ' item' : ' items');
    document.getElementById('itemSubCount').textContent = totalQty + (totalQty === 1 ? ' item' : ' items') + ' in your order';

    // Free delivery bar
    const subtotalRaw = cart.reduce((s,i) => s + i.price * i.quantity, 0);
    const barEl = document.getElementById('freeDeliveryBar');
    if (subtotalRaw < FREE_DELIVERY_THRESHOLD) {
        const needed = FREE_DELIVERY_THRESHOLD - subtotalRaw;
        const pct = Math.min((subtotalRaw / FREE_DELIVERY_THRESHOLD) * 100, 100);
        document.getElementById('freeDeliveryText').textContent = `Add ₱${needed.toLocaleString()} more for free delivery`;
        document.getElementById('freeDeliveryFill').style.width = pct + '%';
        barEl.style.display = 'flex';
    } else {
        barEl.style.display = 'none';
    }

    // Render items
    const list = document.getElementById('cartItemsList');
    list.innerHTML = '';
    cart.forEach((item, idx) => {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.style.animationDelay = (idx * 0.05) + 's';
        div.dataset.id    = item.id;
        div.dataset.price = item.price;

        // Build modifier chips
        const modChipsHtml = (item.modifiers || [])
            .filter(m => m && m.name && !/^no\s/i.test(m.name))
            .map(m => {
                const colors = { flavor: '#3b82f6', modifier: '#8b5cf6', addon: '#d97706' };
                const c = colors[m.type] || '#8b5cf6';
                const adj = parseFloat(m.price_adjustment || 0);
                const extra = (m.price_type === 'add' && adj > 0)
                    ? ` <span style="color:#4ade80;font-size:.6rem;">+&#8369;${adj.toLocaleString()}</span>` : '';
                return `<span style="display:inline-flex;align-items:center;gap:.2rem;padding:.15rem .5rem;border-radius:999px;font-size:.65rem;font-weight:600;background:${c}18;color:${c};border:1px solid ${c}30;">${m.name}${extra}</span>`;
            }).join('');

        div.innerHTML = `
            <img src="${item.image ? (item.image.startsWith('http') ? item.image : '/' + item.image.replace(/^\//, '')) : '/images/hero-burger.webp'}" 
                 alt="${item.name}" class="item-img" loading="lazy" decoding="async"
                 onerror="this.onerror=null;this.src='/images/hero-burger.webp'">
            <div class="item-info">
                <p class="item-name">${item.name}</p>
                <p class="item-unit-price">&#8369;${item.price.toLocaleString()} each</p>
                <span class="item-category-tag">${item.category || 'Food'}</span>
                ${modChipsHtml ? `<div style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.4rem;">${modChipsHtml}</div>` : ''}
            </div>
            <div class="item-right">
                <p class="item-total-price">&#8369;${(item.price * item.quantity).toLocaleString()}</p>
                <div class="qty-wrap">
                    <button class="qty-btn qty-dec">-</button>
                    <input type="number" class="qty-value" value="${item.quantity}" min="1">
                    <button class="qty-btn qty-inc">+</button>
                </div>
            </div>
            <button class="remove-btn" title="Remove">
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>`;

        list.appendChild(div);

        const id       = item.id;
        const price    = item.price;
        const qtyInput = div.querySelector('.qty-value');
        const totalEl  = div.querySelector('.item-total-price');

        function setQty(val) {
            const q = Math.max(1, val);
            qtyInput.value = q;
            const ci = cart.find(c => c.id === id);
            if (ci) ci.quantity = q;
            saveCart();
            totalEl.textContent = '₱' + (price * q).toLocaleString();
            updateTotals();
            // refresh nav count
            const tq = cart.reduce((s,i) => s + i.quantity, 0);
            document.getElementById('navCartCount').textContent = tq + (tq === 1 ? ' item' : ' items');
            @auth serverUpdateQty(id, q); @endauth
        }

        div.querySelector('.qty-dec').addEventListener('click', () => setQty(parseInt(qtyInput.value) - 1));
        div.querySelector('.qty-inc').addEventListener('click', () => setQty(parseInt(qtyInput.value) + 1));
        qtyInput.addEventListener('change', () => setQty(parseInt(qtyInput.value) || 1));

        div.querySelector('.remove-btn').addEventListener('click', () => {
            div.style.opacity = '0';
            div.style.transform = 'translateX(20px)';
            div.style.transition = 'all 0.2s ease';
            setTimeout(() => {
                cart = cart.filter(c => c.id !== id);
                saveCart();
                renderCart();
                @auth serverRemoveItem(id); @endauth
            }, 200);
        });
    });

    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((s,i) => s + i.price * i.quantity, 0);
    const totalQty = cart.reduce((s,i) => s + i.quantity, 0);

    // Check promo
    let delivery = 30;   // base delivery fee (₱30 for first 2 km)
    let discount = 0;
    const promoCode = document.getElementById('promoInput').value.trim().toUpperCase();
    const promo = PROMOS[promoCode];
    if (promo) {
        if (promo.type === 'delivery') delivery = 0;
        else if (promo.type === 'percent') discount = Math.round(subtotal * promo.value / 100);
        else if (promo.type === 'fixed')   discount = promo.value;
    }

    const grand = subtotal + delivery - discount;
    document.getElementById('subtotal').textContent      = '₱' + subtotal.toLocaleString();
    document.getElementById('totalItems').textContent    = totalQty;
    document.getElementById('deliveryFeeDisplay').innerHTML = delivery === 0
        ? '<span class="free-badge">FREE</span>'
        : '₱' + delivery;
    document.getElementById('grandTotal').textContent    = '₱' + grand.toLocaleString();
    document.getElementById('itemSubCount').textContent  = totalQty + (totalQty === 1 ? ' item' : ' items') + ' in your order';

    // Sync Buy Now bar total
    const buyBarTotal = document.getElementById('buyBarTotal');
    if (buyBarTotal) buyBarTotal.textContent = '₱' + grand.toLocaleString();

    if (discount > 0) {
        document.getElementById('discountRow').style.display   = 'flex';
        document.getElementById('discountDisplay').textContent = '-₱' + discount.toLocaleString();
    } else {
        document.getElementById('discountRow').style.display   = 'none';
    }
}

function applyPromo() {
    const code  = document.getElementById('promoInput').value.trim().toUpperCase();
    const msgEl = document.getElementById('promoMsg');
    const promo = PROMOS[code];
    msgEl.style.display = 'block';
    if (promo) {
        msgEl.innerHTML = `<span style="color:#4ade80;display:inline-flex;align-items:center;gap:4px;"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> ${promo.label}</span>`;
        updateTotals();
    } else {
        msgEl.innerHTML = `<span style="color:#f87171;display:inline-flex;align-items:center;gap:4px;"><svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> Invalid promo code</span>`;
    }
}

// Toast with optional "Fix it" link to product page
function showCartToast(msg, itemId) {
    const existing = document.getElementById('cartToast');
    if (existing) existing.remove();

    const t = document.createElement('div');
    t.id = 'cartToast';
    t.innerHTML = `
        <span>${msg}</span>
        ${itemId ? `<a href="/shop/product/${itemId}" style="color:#facc15;font-weight:700;white-space:nowrap;margin-left:10px;">Fix it ?</a>` : ''}
    `;
    Object.assign(t.style, {
        position: 'fixed', bottom: '100px', left: '50%',
        transform: 'translateX(-50%)',
        background: '#1e0a0a',
        border: '1px solid rgba(239,68,68,0.4)',
        color: '#f87171', padding: '12px 20px',
        borderRadius: '14px', fontSize: '13px', fontWeight: '600',
        zIndex: '9999', boxShadow: '0 4px 24px rgba(239,68,68,0.3)',
        display: 'flex', alignItems: 'center', gap: '6px',
        maxWidth: '90vw', textAlign: 'left',
        animation: 'fadeInUp 0.3s ease',
    });
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 4000);
}

// ── Mobile back button guard ──────────────────────────────
history.pushState({ page: 'cart' }, '', window.location.href);
window.addEventListener('popstate', function() {
    history.pushState({ page: 'cart' }, '', window.location.href);
});

// ── Echo: real-time shop close — disable buy now bar ────
if (window.Echo) {
    window.Echo.channel('shop.status')
        .listen('.shop.status', (data) => {
            // If dine-in just closed, clear the QR table session
            if (!data.is_open_dine_in || !data.is_open) {
                sessionStorage.removeItem('eutTableNumber');
                localStorage.removeItem('eutTableNumber');
                sessionStorage.removeItem('eutTableFromQr');
                localStorage.removeItem('eutTableFromQr');
                localStorage.setItem('eutOrderType', 'delivery');
            }

            const bar = document.getElementById('buyNowBar');
            if (!bar) return;
            if (!data.is_open) {
                bar.style.opacity = '0.5';
                bar.style.pointerEvents = 'none';
                bar.style.background = 'linear-gradient(135deg,#374151,#4b5563)';
                bar.style.cursor = 'not-allowed';
                const label = bar.querySelector('.buy-now-label');
                if (label) label.textContent = '🔴 Shop Closed';
            } else {
                bar.style.opacity = '';
                bar.style.pointerEvents = '';
                bar.style.background = '';
                bar.style.cursor = '';
                const label = bar.querySelector('.buy-now-label');
                if (label) label.textContent = 'Place Order';
            }
        });
}

@auth
// ── Echo: live order status toast while on cart page ─────
(function() {
    const STATUS_LABELS = {
        accepted:         '✅ Order Accepted! Kitchen is on it.',
        preparing:        '👨‍🍳 Your food is being prepared.',
        rider_assigned:   '🏍️ Rider assigned to your order.',
        out_for_delivery: '🚀 Your order is on the way!',
        delivered:        '🎉 Your order has been delivered!',
        cancelled:        '❌ Your order was cancelled.',
    };

    function showOrderToast(order) {
        const msg = STATUS_LABELS[order.status];
        if (!msg) return;

        const existing = document.getElementById('echoOrderToast');
        if (existing) existing.remove();

        const t = document.createElement('div');
        t.id = 'echoOrderToast';
        const isGood = !['cancelled'].includes(order.status);
        Object.assign(t.style, {
            position: 'fixed', bottom: '100px', left: '50%',
            transform: 'translateX(-50%) translateY(20px)',
            background: isGood ? '#0d1f17' : '#1e0a0a',
            border: `1px solid ${isGood ? 'rgba(74,222,128,.35)' : 'rgba(239,68,68,.35)'}`,
            color: isGood ? '#4ade80' : '#f87171',
            padding: '12px 20px', borderRadius: '14px',
            fontSize: '13px', fontWeight: '600',
            zIndex: '9999', boxShadow: '0 4px 24px rgba(0,0,0,.5)',
            display: 'flex', alignItems: 'center', gap: '8px',
            maxWidth: '90vw', textAlign: 'left', cursor: 'pointer',
            transition: 'all .35s cubic-bezier(.32,.72,0,1)', opacity: '0',
        });
        t.innerHTML = `<span>${msg}</span><a href="{{ route('shop.tracking') }}" style="color:#facc15;font-weight:700;white-space:nowrap;margin-left:6px;text-decoration:none;">Track →</a>`;
        document.body.appendChild(t);

        requestAnimationFrame(() => {
            t.style.opacity = '1';
            t.style.transform = 'translateX(-50%) translateY(0)';
        });
        t.addEventListener('click', () => window.location.href = '{{ route("shop.tracking") }}');
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateX(-50%) translateY(20px)';
            setTimeout(() => t.remove(), 400);
        }, 6000);
    }

    if (window.Echo) {
        window.Echo.private('orders.{{ auth()->id() }}')
            .listen('.order.updated', (order) => {
                showOrderToast(order);
            });
    }
})();
@endauth
</script>
@include('partials.pwa-register')
<script>
// ── QR-scanned dine-in: place order directly from cart (skip checkout page) ──
const SHOP_IS_OPEN_DINE_IN = @json($isOpenDineIn) && @json($isOpen);
const ORDERS_STORE_URL = '{{ route("orders.store") }}';
const TRACKING_URL = '{{ route("shop.tracking") }}';

function isQrScannedDineIn() {
    const fromQr = sessionStorage.getItem('eutTableFromQr') === '1'
                || localStorage.getItem('eutTableFromQr') === '1';
    if (!fromQr) return null;
    const tableNum = (sessionStorage.getItem('eutTableNumber') || localStorage.getItem('eutTableNumber') || '').trim();
    if (!tableNum || !/^\d{1,2}$/.test(tableNum)) return null;
    return tableNum;
}

function buildOrderItems(cartItems) {
    return cartItems.map(i => ({
        id: i.id,
        qty: i.quantity,
        modifiers: (i.modifiers || [])
            .filter(m => m && typeof m === 'object' && m.name)
            .map(m => ({
                type: m.type || 'modifier',
                name: m.name || '',
                price_type: m.price_type || 'none',
                price_adjustment: parseFloat(m.price_adjustment || 0),
            })),
    }));
}

async function placeQrDineInOrder(tableNumber, btnEl) {
    const cartItems = JSON.parse(localStorage.getItem('eutCart') || '[]');
    if (!cartItems.length) {
        alert('Your cart is empty.');
        return;
    }

    if (!SHOP_IS_OPEN_DINE_IN) {
        alert('Dine-in service is currently closed. Orders cannot be placed right now.');
        return;
    }

    const badItem = cartItems.find(item => item.requires_flavor && !item.flavor_ok);
    if (badItem) {
        alert(`"${(badItem.name || '').split('(')[0].trim()}" needs a flavor selected! Tap the item to fix it.`);
        return;
    }

    const origHtml = btnEl ? (btnEl.dataset.origHtml || btnEl.innerHTML) : null;
    if (btnEl) {
        btnEl.dataset.origHtml = origHtml;
        btnEl.style.pointerEvents = 'none';
        if (btnEl.tagName === 'BUTTON') btnEl.disabled = true;
        btnEl.innerHTML = 'Placing order…';
    }

    const payload = {
        items: buildOrderItems(cartItems),
        order_type: 'dine_in',
        delivery_address: 'Dine-in · ' + tableNumber,
        delivery_barangay: '',
        payment_method: 'cash',
        notes: (document.getElementById('qrDineInNotes')?.value?.trim() || ''),
        delivery_lat: null,
        delivery_lng: null,
        table_number: tableNumber,
        customer_lat: window.__customerLat || (function() {
            try { const c = JSON.parse(sessionStorage.getItem('eut_geo_ok') || 'null'); return c?.lat || null; } catch(e) { return null; }
        })(),
        customer_lng: window.__customerLng || (function() {
            try { const c = JSON.parse(sessionStorage.getItem('eut_geo_ok') || 'null'); return c?.lng || null; } catch(e) { return null; }
        })(),
    };

    try {
        const r = await fetch(ORDERS_STORE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const d = await r.json();
        if (d.success) {
            localStorage.setItem('eutCart', JSON.stringify([]));
            sessionStorage.removeItem('eutTableNumber');
            @auth
            fetch('/cart', { method:'DELETE', headers:{'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept':'application/json'} }).catch(()=>{});
            @endauth
            window.location.href = TRACKING_URL;
            return;
        }
        if (d.clear_cart) {
            localStorage.removeItem('eutCart');
            alert(d.message || 'Some items are no longer available. Your cart has been cleared.');
            window.location.href = '{{ route("shop.home") }}';
            return;
        }
        alert(d.message || 'Order failed. Please try again.');
    } catch (err) {
        alert('Network error. Please try again.');
    }

    if (btnEl) {
        btnEl.style.pointerEvents = '';
        if (btnEl.tagName === 'BUTTON') btnEl.disabled = false;
        btnEl.innerHTML = origHtml;
    }
}

function handleQrDineInCheckout(e, btnEl) {
    const tableNum = isQrScannedDineIn();
    if (!tableNum) return;
    e.preventDefault();
    placeQrDineInOrder(tableNum, btnEl);
}

function updateQrCheckoutLabels() {
    if (!isQrScannedDineIn()) return;
    const guestBtn = document.getElementById('guestCheckoutBtn');
    if (guestBtn) guestBtn.textContent = 'Place Order →';
    const buyCta = document.querySelector('#buyNowBar .buy-now-cta');
    if (buyCta) buyCta.textContent = 'Place Order';
    // Show notes field for auth users (qrDineInNotesWrap)
    const notesWrap = document.getElementById('qrDineInNotesWrap');
    if (notesWrap) notesWrap.style.display = 'block';
    // Show notes field for guest dine-in users
    const guestNotesWrap = document.getElementById('guestDineInNotesWrap');
    if (guestNotesWrap) guestNotesWrap.style.display = 'block';
}

document.addEventListener('DOMContentLoaded', () => {
    updateQrCheckoutLabels();
    const guestBtn = document.getElementById('guestCheckoutBtn');
    if (guestBtn) guestBtn.addEventListener('click', e => handleQrDineInCheckout(e, guestBtn));
});
</script>
</body>
</html>
