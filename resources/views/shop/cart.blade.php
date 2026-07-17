<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* ── NAVBAR ── */
        .topnav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(8,8,16,0.94);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
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

        /* ── PAGE ── */
        .page-body { max-width: 560px; margin: 0 auto; padding: 82px 16px 120px; }

        /* ── CARDS ── */
        .card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px; overflow: hidden;
            margin-bottom: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }
        .card-header {
            padding: 15px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 13px; font-weight: 700; color: #fff; letter-spacing: 0.01em; }
        .card-sub { font-size: 11px; color: #4b5563; margin-top: 2px; }

        /* ── CART ITEMS ── */
        .cart-item {
            display: flex; align-items: center; gap: 13px;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            transition: background 0.15s;
            position: relative;
        }
        .cart-item:last-child { border-bottom: none; }
        .cart-item:hover { background: rgba(255,255,255,0.015); }

        .item-img {
            width: 72px; height: 72px; border-radius: 14px;
            object-fit: cover; flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
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

        /* ── PROMO CODE ── */
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

        /* ── ORDER SUMMARY ── */
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

        /* ── FREE DELIVERY BANNER ── */
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

        /* ── CHECKOUT BUTTON ── */
        .checkout-btn {
            display: block; width: 100%;
            background: linear-gradient(135deg, #f59e0b, #facc15);
            color: #000; padding: 16px;
            border-radius: 14px; border: none;
            font-size: 15px; font-weight: 800;
            text-align: center; text-decoration: none;
            cursor: pointer; transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(250,204,21,0.3);
            letter-spacing: 0.01em;
        }
        .checkout-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(250,204,21,0.4); }
        .continue-btn {
            display: block; width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            color: #9ca3af; padding: 13px;
            border-radius: 14px; font-size: 14px; font-weight: 600;
            text-align: center; text-decoration: none;
            margin-top: 10px; transition: all 0.2s;
        }
        .continue-btn:hover { background: rgba(255,255,255,0.08); color: #fff; }

        /* ── TRUST BADGES ── */
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

        /* ── EMPTY STATE ── */
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

        /* ── UPSELL ROW ── */
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

        /* ── LIGHT MODE ── */
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

        /* Animate in */
        @keyframes slide-up { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        .cart-item { animation: slide-up 0.25s ease both; }
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

<!-- ══════════ PAGE BODY ══════════ -->
<div class="page-body">

    <!-- ── EMPTY STATE ── -->
    <div id="emptyCart" style="display:none;">
        <div class="empty-state">
            <div class="empty-bag">🛒</div>
            <p class="empty-title">Your cart is empty</p>
            <p class="empty-sub">Looks like you haven't added anything yet.<br>Browse our menu and find something delicious!</p>
            <div class="empty-suggestions">
                <a href="{{ route('shop.home') }}" class="suggestion-chip">🍔 Burgers</a>
                <a href="{{ route('shop.home') }}" class="suggestion-chip">🍟 Fries</a>
                <a href="{{ route('shop.home') }}" class="suggestion-chip">🥤 Beverages</a>
                <a href="{{ route('shop.home') }}" class="suggestion-chip">🍱 Combos</a>
            </div>
            <a href="{{ route('shop.home') }}" class="checkout-btn" style="max-width:280px; margin:0 auto;">
                Browse Menu
            </a>
        </div>
    </div>

    <!-- ── CART CONTENT ── -->
    <div id="cartContent" style="display:none;">

        <!-- Free delivery progress -->
        <div id="freeDeliveryBar" class="free-delivery-bar" style="display:none;">
            <span style="font-size:16px;">🚀</span>
            <div style="flex:1;">
                <p style="font-size:11px; color:#4b5563; margin-bottom:5px;" id="freeDeliveryText">Add ₱X more for free delivery</p>
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
                    <a href="{{ route('shop.home') }}" class="upsell-chip">
                        <img src="{{ asset('images/french-fries.jpg') }}" class="upsell-img" alt="Fries">
                        <div><p class="upsell-name">Crispy Fries</p><p class="upsell-price">₱120</p></div>
                    </a>
                    <a href="{{ route('shop.home') }}" class="upsell-chip">
                        <img src="{{ asset('images/combo-meal.jpg') }}" class="upsell-img" alt="Combo">
                        <div><p class="upsell-name">Combo Meal</p><p class="upsell-price">₱390</p></div>
                    </a>
                    <a href="{{ route('shop.home') }}" class="upsell-chip">
                        <img src="{{ asset('images/gourmet-burger.jpg') }}" class="upsell-img" alt="Burger">
                        <div><p class="upsell-name">Gourmet Burger</p><p class="upsell-price">₱420</p></div>
                    </a>
                    <a href="{{ route('shop.home') }}" class="upsell-chip">
                        <img src="{{ asset('images/hero-burger.jpg') }}" class="upsell-img" alt="Classic">
                        <div><p class="upsell-name">Classic Burger</p><p class="upsell-price">₱350</p></div>
                    </a>
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
                <span class="summary-value" id="subtotal">₱0</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Delivery fee</span>
                <span class="summary-value" id="deliveryFeeDisplay">₱50</span>
            </div>
            <div class="summary-row" id="discountRow" style="display:none;">
                <span class="summary-label" style="color:#4ade80;">Promo discount</span>
                <span class="summary-value" id="discountDisplay" style="color:#4ade80;">-₱0</span>
            </div>
            <div class="summary-row" style="padding-top:14px; padding-bottom:14px;">
                <span class="summary-label-bold">Total</span>
                <span class="summary-value-bold" id="grandTotal">₱0</span>
            </div>

            <!-- Trust badges -->
            <div class="trust-row">
                <div class="trust-item"><span class="trust-icon">🔒</span>Secure<br>Payment</div>
                <div class="trust-item"><span class="trust-icon">🚀</span>Fast<br>Delivery</div>
                <div class="trust-item"><span class="trust-icon">♻️</span>Easy<br>Returns</div>
                <div class="trust-item"><span class="trust-icon">⭐</span>Top<br>Quality</div>
            </div>
        </div>

        <!-- Action buttons -->
        <a href="{{ route('shop.checkout') }}" class="checkout-btn">
            Proceed to Checkout →
        </a>
        <a href="{{ route('shop.home') }}" class="continue-btn">
            ← Continue Shopping
        </a>

    </div><!-- /cartContent -->

</div><!-- /page-body -->

<!-- ══════════ BOTTOM NAV ══════════ -->
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
    renderCart();
    document.getElementById('clearAllBtn').addEventListener('click', () => {
        if (confirm('Remove all items from cart?')) {
            cart = [];
            saveCart();
            renderCart();
        }
    });
});

let cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
let promoDiscount = 0;
const FREE_DELIVERY_THRESHOLD = 500;

const PROMOS = {
    'EUTFREE': { type: 'delivery', label: 'Free delivery applied!' },
    'EUT10':   { type: 'percent', value: 10, label: '10% discount applied!' },
    'SAVE50':  { type: 'fixed',   value: 50, label: '₱50 off applied!' },
};

function saveCart() {
    localStorage.setItem('eutCart', JSON.stringify(cart));
}

function renderCart() {
    const empty   = document.getElementById('emptyCart');
    const content = document.getElementById('cartContent');

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

        div.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="item-img">
            <div class="item-info">
                <p class="item-name">${item.name}</p>
                <p class="item-unit-price">₱${item.price.toLocaleString()} each</p>
                <span class="item-category-tag">${item.category || 'Food'}</span>
            </div>
            <div class="item-right">
                <p class="item-total-price" id="itotal-${item.id}">₱${(item.price * item.quantity).toLocaleString()}</p>
                <div class="qty-wrap">
                    <button class="qty-btn qty-dec">−</button>
                    <input type="number" class="qty-value" value="${item.quantity}" min="1" id="qty-${item.id}">
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
        const totalEl  = div.querySelector(`#itotal-${id}`);

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
            }, 200);
        });
    });

    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((s,i) => s + i.price * i.quantity, 0);
    const totalQty = cart.reduce((s,i) => s + i.quantity, 0);

    // Check promo
    let delivery = 50;
    let discount = 0;
    const promoCode = document.getElementById('promoInput').value.trim().toUpperCase();
    const promo = PROMOS[promoCode];
    if (promo) {
        if (promo.type === 'delivery') delivery = 0;
        else if (promo.type === 'percent') discount = Math.round(subtotal * promo.value / 100);
        else if (promo.type === 'fixed')   discount = promo.value;
    }
    // Free delivery threshold
    if (subtotal >= FREE_DELIVERY_THRESHOLD) delivery = 0;

    const grand = subtotal + delivery - discount;
    document.getElementById('subtotal').textContent      = '₱' + subtotal.toLocaleString();
    document.getElementById('totalItems').textContent    = totalQty;
    document.getElementById('deliveryFeeDisplay').textContent = delivery === 0
        ? '<span class="free-badge">FREE</span>'
        : '₱' + delivery;
    document.getElementById('deliveryFeeDisplay').innerHTML = delivery === 0
        ? '<span class="free-badge">FREE</span>'
        : '₱' + delivery;
    document.getElementById('grandTotal').textContent    = '₱' + grand.toLocaleString();
    document.getElementById('itemSubCount').textContent  = totalQty + (totalQty === 1 ? ' item' : ' items') + ' in your order';

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
        msgEl.innerHTML = `<span style="color:#4ade80;">✓ ${promo.label}</span>`;
        updateTotals();
    } else {
        msgEl.innerHTML = `<span style="color:#f87171;">✕ Invalid promo code</span>`;
    }
}
</script>
</body>
</html>
