<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* ── NAVBAR ── */
        .topnav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(8,8,16,0.92);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .topnav-inner {
            max-width: 540px; margin: 0 auto;
            padding: 14px 16px 0;
        }
        .topnav-row { display: flex; align-items: center; gap: 10px; margin-bottom: 0; }
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
        .theme-btn {
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; flex-shrink: 0;
        }
        .theme-btn:hover { background: rgba(255,255,255,0.12); }

        /* ── TABS ── */
        .tabs-bar {
            display: flex; gap: 0; margin-top: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .tab {
            padding: 10px 20px; font-size: 13px; font-weight: 600;
            color: #6b7280; background: none; border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer; transition: all 0.2s;
            position: relative; bottom: -1px;
        }
        .tab.active { color: #facc15; border-bottom-color: #facc15; }
        .tab-dot {
            display: inline-block; width: 6px; height: 6px;
            background: #ef4444; border-radius: 50;
            margin-left: 5px; vertical-align: middle;
            animation: blink 1.5s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* ── PAGE BODY ── */
        .page-body { max-width: 540px; margin: 0 auto; padding: 130px 16px 110px; }

        /* ── CARDS ── */
        .card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px; overflow: hidden;
            margin-bottom: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
            transition: border-color 0.25s, transform 0.2s;
        }
        .card:hover { border-color: rgba(250,204,21,0.2); }
        .card-header {
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 13px; font-weight: 700; color: #fff; letter-spacing: 0.01em; }
        .card-sub { font-size: 11px; color: #4b5563; margin-top: 2px; }

        /* ── BADGES ── */
        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 11px; border-radius: 99px;
            font-size: 11px; font-weight: 700; letter-spacing: 0.02em;
        }
        .badge-live { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.25); }
        .badge-done { background: rgba(34,197,94,0.10); color: #4ade80; border: 1px solid rgba(34,197,94,0.22); }
        .badge-cancelled { background: rgba(239,68,68,0.08); color: #f87171; border: 1px solid rgba(239,68,68,0.18); }
        .badge-pulse { width: 7px; height: 7px; background: #ef4444; border-radius: 50%; animation: blink 1.2s infinite; }

        /* ── PROGRESS ── */
        .progress-wrap { padding: 16px 18px 12px; }
        .progress-eta-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
        .progress-eta-label { font-size: 11px; color: #6b7280; }
        .progress-eta-time { font-size: 13px; font-weight: 700; color: #facc15; }
        .progress-track { height: 6px; background: #1a1b2e; border-radius: 99px; overflow: hidden; position: relative; }
        .progress-fill {
            height: 100%; border-radius: 99px;
            background: linear-gradient(90deg, #dc2626, #f59e0b, #facc15);
            position: relative; transition: width 1.2s cubic-bezier(.4,0,.2,1);
        }
        .progress-fill::after {
            content: ''; position: absolute; right: -1px; top: 50%;
            transform: translateY(-50%);
            width: 12px; height: 12px; border-radius: 50%;
            background: #facc15;
            box-shadow: 0 0 10px #facc15, 0 0 20px rgba(250,204,21,0.4);
        }
        .progress-steps {
            display: flex; justify-content: space-between;
            margin-top: 8px;
        }
        .progress-step-label { font-size: 10px; color: #374151; }
        .progress-step-label.done { color: #facc15; }
        .progress-step-label.active { color: #ef4444; }

        /* ── TIMELINE ── */
        .timeline { padding: 4px 18px 18px; }
        .t-step { display: flex; gap: 14px; position: relative; }
        .t-spine { display: flex; flex-direction: column; align-items: center; width: 18px; flex-shrink: 0; }
        .t-dot {
            width: 16px; height: 16px; border-radius: 50%; flex-shrink: 0;
            margin-top: 3px; position: relative; z-index: 1;
        }
        .t-dot-done { background: #facc15; box-shadow: 0 0 0 3px rgba(250,204,21,0.15), 0 0 14px rgba(250,204,21,0.4); }
        .t-dot-active {
            background: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.15), 0 0 14px rgba(239,68,68,0.5);
            animation: pulse-red 1.5s ease-in-out infinite;
        }
        .t-dot-pending { background: #1f2937; border: 2px solid #2d3748; }
        @keyframes pulse-red {
            0%,100%{ box-shadow: 0 0 0 3px rgba(239,68,68,0.15), 0 0 10px rgba(239,68,68,0.4); }
            50%{ box-shadow: 0 0 0 6px rgba(239,68,68,0.08), 0 0 20px rgba(239,68,68,0.6); }
        }
        .t-line { width: 2px; flex: 1; min-height: 32px; margin: 4px 0; border-radius: 2px; }
        .t-line-done { background: linear-gradient(180deg, #facc15, rgba(250,204,21,0.2)); }
        .t-line-active { background: linear-gradient(180deg, #ef4444, rgba(239,68,68,0.1)); }
        .t-line-pending { background: #1a1b2e; }
        .t-content { padding-bottom: 24px; flex: 1; }
        .t-content:last-child { padding-bottom: 0; }
        .t-label-done { font-size: 13px; font-weight: 600; color: #fff; }
        .t-label-active { font-size: 13px; font-weight: 700; color: #ef4444; }
        .t-label-pending { font-size: 13px; font-weight: 500; color: #374151; }
        .t-desc { font-size: 11px; color: #4b5563; margin-top: 3px; line-height: 1.5; }
        .t-desc-active { font-size: 11px; color: #9ca3af; margin-top: 3px; }
        .t-check {
            display: inline-flex; align-items: center; justify-content: center;
            width: 14px; height: 14px; background: #facc15;
            border-radius: 50%; margin-right: 4px; flex-shrink: 0;
        }

        /* ── ORDER ITEMS ── */
        .item-row {
            display: flex; align-items: center; gap: 12px;
            padding: 13px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .item-row:last-child { border-bottom: none; }
        .item-img { width: 50px; height: 50px; border-radius: 12px; object-fit: cover; flex-shrink: 0; }
        .item-name { font-size: 13px; font-weight: 600; color: #e5e7eb; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-qty { font-size: 11px; color: #4b5563; margin-top: 3px; }
        .item-price { font-size: 13px; font-weight: 700; color: #facc15; flex-shrink: 0; margin-left: auto; }

        /* ── TOTALS ── */
        .totals { padding: 14px 18px; border-top: 1px solid rgba(255,255,255,0.05); }
        .total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .total-row:last-child { margin-bottom: 0; }
        .total-label { font-size: 12px; color: #6b7280; }
        .total-value { font-size: 12px; color: #9ca3af; }
        .total-grand-label { font-size: 14px; font-weight: 700; color: #fff; }
        .total-grand-value { font-size: 16px; font-weight: 800; color: #facc15; }
        .total-divider { border: none; border-top: 1px solid rgba(255,255,255,0.05); margin: 10px 0; }

        /* ── DELIVERY INFO ── */
        .info-row { display: flex; align-items: flex-start; gap: 12px; padding: 12px 18px; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .info-row:last-child { border-bottom: none; }
        .info-icon {
            width: 34px; height: 34px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .info-text-label { font-size: 10px; color: #4b5563; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.05em; }
        .info-text-val { font-size: 13px; color: #e5e7eb; font-weight: 500; }
        .info-text-sub { font-size: 11px; color: #6b7280; margin-top: 1px; }

        /* ── PAST ORDER CARDS ── */
        .pcard {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px; overflow: hidden;
            margin-bottom: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
            transition: border-color 0.2s, transform 0.15s;
        }
        .pcard:hover { border-color: rgba(250,204,21,0.18); transform: translateY(-1px); }
        .pcard-cancelled { border-color: rgba(239,68,68,0.12) !important; }
        .pcard-cancelled:hover { border-color: rgba(239,68,68,0.3) !important; }

        .pcard-header { padding: 14px 18px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .pcard-id-row { display: flex; justify-content: space-between; align-items: center; }
        .pcard-id { font-size: 13px; font-weight: 700; color: #fff; }
        .pcard-date { font-size: 11px; color: #4b5563; margin-top: 3px; }

        .pcard-items { padding: 12px 18px; border-bottom: 1px solid rgba(255,255,255,0.04); }
        .pcard-item-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .pcard-item-row:last-child { margin-bottom: 0; }
        .pcard-item-img { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; flex-shrink: 0; }
        .pcard-item-name { font-size: 12px; color: #d1d5db; font-weight: 500; flex: 1; }
        .pcard-item-price { font-size: 12px; font-weight: 700; color: #9ca3af; }

        .pcard-footer { padding: 12px 18px; display: flex; justify-content: space-between; align-items: center; }
        .pcard-total { font-size: 16px; font-weight: 800; color: #facc15; }
        .pcard-total-label { font-size: 10px; color: #4b5563; }

        /* Cancelled strikethrough style */
        .pcard-cancelled .pcard-total { color: #6b7280 !important; text-decoration: line-through; }
        .pcard-cancelled .pcard-item-name { color: #4b5563 !important; }

        /* Cancelled reason box */
        .cancel-reason {
            margin: 0 18px 14px;
            background: rgba(239,68,68,0.06);
            border: 1px solid rgba(239,68,68,0.15);
            border-radius: 10px;
            padding: 10px 14px;
            display: flex; align-items: flex-start; gap: 8px;
        }
        .cancel-reason-icon { font-size: 14px; flex-shrink: 0; margin-top: 1px; }
        .cancel-reason-text { font-size: 11px; color: #9ca3af; line-height: 1.5; }
        .cancel-reason-title { font-size: 12px; font-weight: 600; color: #f87171; margin-bottom: 2px; }

        /* ── ACTION BUTTONS ── */
        .btn-primary {
            flex: 1; background: linear-gradient(135deg, #f59e0b, #facc15);
            color: #000; padding: 14px; border-radius: 14px;
            font-weight: 700; font-size: 14px; text-align: center;
            text-decoration: none; display: block;
            box-shadow: 0 4px 16px rgba(250,204,21,0.25);
            transition: all 0.2s;
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(250,204,21,0.35); }
        .btn-ghost {
            flex: 1; background: rgba(255,255,255,0.04);
            color: #9ca3af; padding: 14px; border-radius: 14px;
            font-weight: 600; font-size: 14px; text-align: center;
            text-decoration: none; display: block;
            border: 1px solid rgba(255,255,255,0.08);
            transition: all 0.2s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .btn-reorder {
            background: linear-gradient(135deg, #f59e0b, #facc15);
            color: #000; padding: 8px 18px; border-radius: 99px;
            font-size: 12px; font-weight: 700; text-decoration: none;
            display: inline-block; transition: all 0.2s;
            box-shadow: 0 2px 10px rgba(250,204,21,0.2);
        }
        .btn-reorder:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(250,204,21,0.35); }

        /* ── EMPTY STATE ── */
        .empty-state { text-align: center; padding: 64px 24px; }
        .empty-icon { font-size: 60px; margin-bottom: 18px; opacity: 0.7; }
        .empty-title { font-size: 18px; font-weight: 700; color: #fff; margin-bottom: 8px; }
        .empty-sub { font-size: 13px; color: #4b5563; margin-bottom: 24px; line-height: 1.6; }

        /* ── DECORATIVE GLOW ── */
        .active-glow {
            position: relative; overflow: hidden;
        }
        .active-glow::before {
            content: '';
            position: absolute; top: -60px; right: -40px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(250,204,21,0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── LIGHT MODE ── */
        .light-mode body { background: #f0f0f8 !important; color: #111 !important; }
        .light-mode .topnav { background: rgba(255,255,255,0.95) !important; border-color: rgba(0,0,0,0.07) !important; }
        .light-mode .card, .light-mode .pcard { background: #fff !important; border-color: rgba(0,0,0,0.07) !important; box-shadow: 0 2px 16px rgba(0,0,0,0.06) !important; }
        .light-mode .pcard-cancelled { border-color: rgba(239,68,68,0.2) !important; }
        .light-mode .card-title, .light-mode .pcard-id, .light-mode .t-label-done, .light-mode .t-label-active { color: #111 !important; }
        .light-mode .t-label-pending { color: #9ca3af !important; }
        .light-mode .t-line-pending, .light-mode .progress-track { background: #e5e7eb !important; }
        .light-mode .t-dot-pending { background: #e5e7eb !important; border-color: #d1d5db !important; }
        .light-mode .item-name, .light-mode .info-text-val, .light-mode .total-grand-label { color: #111 !important; }
        .light-mode .back-btn, .light-mode .theme-btn { background: rgba(0,0,0,0.05) !important; border-color: rgba(0,0,0,0.08) !important; color: #555 !important; }
        .light-mode .active-glow::before { background: radial-gradient(circle, rgba(250,204,21,0.08) 0%, transparent 70%) !important; }
        .light-mode .cancel-reason { background: rgba(239,68,68,0.04) !important; }

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
    </style>
</head>
<body>

<!-- ══════════════════════════════
     NAVBAR
══════════════════════════════ -->
<nav class="topnav">
    <div class="topnav-inner">
        <div class="topnav-row">
            <a href="{{ route('shop.home') }}" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span class="topnav-title">My Orders</span>
            <button id="shopThemeToggle" class="theme-btn">
                <svg id="shopSunIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#facc15; display:none;">
                    <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg id="shopMoonIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#9ca3af;">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>
        </div>
        <div class="tabs-bar">
            <button class="tab active" id="tab-active" onclick="switchTab('active')">
                Active <span class="tab-dot"></span>
            </button>
            <button class="tab" id="tab-past" onclick="switchTab('past')">Past Orders</button>
            <button class="tab" id="tab-cancelled" onclick="switchTab('cancelled')">Cancelled</button>
        </div>
    </div>
</nav>

<!-- ══════════════════════════════
     PAGE BODY
══════════════════════════════ -->
<div class="page-body">

    <!-- ─── ACTIVE VIEW ─── -->
    <div id="view-active">

        <!-- Hero status banner -->
        <div style="background: linear-gradient(135deg, #1a0a00, #1a1200, #0e0f1a); border: 1px solid rgba(250,204,21,0.15); border-radius: 20px; padding: 20px 18px; margin-bottom: 14px; position: relative; overflow: hidden;">
            <div style="position:absolute; top:-30px; right:-30px; width:140px; height:140px; background: radial-gradient(circle, rgba(250,204,21,0.08) 0%, transparent 70%); pointer-events:none;"></div>
            <div style="position:absolute; bottom:-20px; left:-20px; width:100px; height:100px; background: radial-gradient(circle, rgba(239,68,68,0.06) 0%, transparent 70%); pointer-events:none;"></div>
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; position:relative; z-index:1;">
                <div>
                    <p style="font-size:10px; color:#6b7280; text-transform:uppercase; letter-spacing:0.08em; margin-bottom:4px;">Order ID</p>
                    <p style="font-size:15px; font-weight:800; color:#fff; letter-spacing:0.01em;">#EUT-{{ str_pad(rand(1,99999),5,'0',STR_PAD_LEFT) }}</p>
                </div>
                <span class="badge badge-live"><span class="badge-pulse"></span> Preparing</span>
            </div>
            <div style="position:relative; z-index:1;">
                <div class="progress-eta-row">
                    <span class="progress-eta-label">Estimated delivery</span>
                    <span class="progress-eta-time">{{ date('g:i A', strtotime('+35 minutes')) }}</span>
                </div>
                <div class="progress-track">
                    <div class="progress-fill" id="progressBar" style="width:40%"></div>
                </div>
                <div class="progress-steps">
                    <span class="progress-step-label done">Placed</span>
                    <span class="progress-step-label active">Preparing</span>
                    <span class="progress-step-label">On the way</span>
                    <span class="progress-step-label">Delivered</span>
                </div>
            </div>
        </div>

        <!-- Timeline card -->
        <div class="card active-glow">
            <div class="card-header">
                <div>
                    <p class="card-title">Delivery Timeline</p>
                    <p class="card-sub">Real-time order status</p>
                </div>
                <svg width="18" height="18" fill="none" stroke="#facc15" viewBox="0 0 24 24" style="opacity:0.6;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div class="timeline">

                <!-- Step 1 done -->
                <div class="t-step">
                    <div class="t-spine">
                        <div class="t-dot t-dot-done">
                            <svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="#000" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="t-line t-line-done"></div>
                    </div>
                    <div class="t-content">
                        <p class="t-label-done">Order Placed</p>
                        <p class="t-desc">{{ date('g:i A') }} · Received &amp; confirmed</p>
                    </div>
                </div>

                <!-- Step 2 active -->
                <div class="t-step">
                    <div class="t-spine">
                        <div class="t-dot t-dot-active"></div>
                        <div class="t-line t-line-active"></div>
                    </div>
                    <div class="t-content">
                        <p class="t-label-active">🍳 Preparing Your Food</p>
                        <p class="t-desc-active">{{ date('g:i A', strtotime('+5 minutes')) }} · Our chefs are crafting your order right now</p>
                    </div>
                </div>

                <!-- Step 3 pending -->
                <div class="t-step">
                    <div class="t-spine">
                        <div class="t-dot t-dot-pending"></div>
                        <div class="t-line t-line-pending"></div>
                    </div>
                    <div class="t-content">
                        <p class="t-label-pending">Out for Delivery</p>
                        <p class="t-desc">Est. {{ date('g:i A', strtotime('+25 minutes')) }}</p>
                    </div>
                </div>

                <!-- Step 4 pending -->
                <div class="t-step">
                    <div class="t-spine">
                        <div class="t-dot t-dot-pending"></div>
                    </div>
                    <div class="t-content" style="padding-bottom:0;">
                        <p class="t-label-pending">Delivered 🎉</p>
                        <p class="t-desc">Est. {{ date('g:i A', strtotime('+40 minutes')) }}</p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Order items card -->
        <div class="card">
            <div class="card-header">
                <div>
                    <p class="card-title">Order Items</p>
                    <p class="card-sub" id="itemCountBadge">Loading…</p>
                </div>
                <svg width="16" height="16" fill="none" stroke="#6b7280" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <div id="orderItemsList"></div>
            <div class="totals">
                <div class="total-row">
                    <span class="total-label">Subtotal</span>
                    <span class="total-value" id="orderSubtotal">₱0</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Delivery fee</span>
                    <span class="total-value">₱50</span>
                </div>
                <hr class="total-divider">
                <div class="total-row">
                    <span class="total-grand-label">Total</span>
                    <span class="total-grand-value" id="orderTotal">₱0</span>
                </div>
            </div>
        </div>

        <!-- Delivery details card -->
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header">
                <p class="card-title">Delivery Details</p>
            </div>
            <div class="info-row">
                <div class="info-icon" style="background:rgba(239,68,68,0.1);">
                    <svg width="16" height="16" fill="none" stroke="#f87171" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="info-text-label">Delivery Address</p>
                    <p class="info-text-val">123 Food Street, Culinary District</p>
                    <p class="info-text-sub">Metro Manila, 1234</p>
                </div>
            </div>
            <div class="info-row">
                <div class="info-icon" style="background:rgba(250,204,21,0.1);">
                    <svg width="16" height="16" fill="none" stroke="#facc15" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="info-text-label">Estimated Arrival</p>
                    <p class="info-text-val">{{ date('g:i A', strtotime('+30 minutes')) }} – {{ date('g:i A', strtotime('+45 minutes')) }}</p>
                </div>
            </div>
            <div class="info-row">
                <div class="info-icon" style="background:rgba(96,165,250,0.1);">
                    <svg width="16" height="16" fill="none" stroke="#60a5fa" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="info-text-label">Payment Method</p>
                    <p class="info-text-val">Cash on Delivery</p>
                </div>
            </div>
            <div class="info-row" style="border:none;">
                <div class="info-icon" style="background:rgba(167,139,250,0.1);">
                    <svg width="16" height="16" fill="none" stroke="#a78bfa" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="info-text-label">Rider</p>
                    <p class="info-text-val">Juan dela Cruz</p>
                    <p class="info-text-sub">⭐ 4.9 · On the way to restaurant</p>
                </div>
            </div>
        </div>

        <!-- Action buttons -->
        <div style="display:flex; gap:10px;">
            <a href="{{ route('shop.home') }}" class="btn-primary">🔁 Order Again</a>
            <a href="{{ route('shop.home') }}" class="btn-ghost">← Back to Menu</a>
        </div>

    </div><!-- /view-active -->

    <!-- ─── PAST ORDERS VIEW ─── -->
    <div id="view-past" style="display:none;">
        <div id="pastOrdersList"></div>
        <div class="empty-state" id="pastEmpty" style="display:none;">
            <div class="empty-icon">📦</div>
            <p class="empty-title">No completed orders yet</p>
            <p class="empty-sub">Your delivered orders will appear here once you've received them.</p>
            <a href="{{ route('shop.home') }}" class="btn-primary" style="display:inline-block; width:auto; padding:12px 28px; border-radius:99px;">Start Ordering</a>
        </div>
    </div>

    <!-- ─── CANCELLED VIEW ─── -->
    <div id="view-cancelled" style="display:none;">
        <div id="cancelledOrdersList"></div>
        <div class="empty-state" id="cancelledEmpty" style="display:none;">
            <div class="empty-icon">✅</div>
            <p class="empty-title">No cancelled orders</p>
            <p class="empty-sub">Great news — you haven't had to cancel any orders.</p>
            <a href="{{ route('shop.home') }}" class="btn-primary" style="display:inline-block; width:auto; padding:12px 28px; border-radius:99px;">Browse Menu</a>
        </div>
    </div>

</div><!-- /page-body -->

<!-- ══════════════════════════════
     BOTTOM NAV
══════════════════════════════ -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Home
        </a>
        <a href="{{ route('shop.tracking') }}" class="bnav-item active">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Orders
        </a>
        <a href="{{ route('shop.cart') }}" class="bnav-item">
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

<!-- ══════════════════════════════
     SCRIPTS
══════════════════════════════ -->
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
    loadOrderItems();
    buildPastOrders();
    buildCancelledOrders();
    // animate progress bar
    setTimeout(() => { document.getElementById('progressBar').style.width = '40%'; }, 300);
});

/* ── Tabs ── */
function switchTab(tab) {
    ['active','past','cancelled'].forEach(id => {
        document.getElementById('view-' + id).style.display = (id === tab) ? 'block' : 'none';
        const btn = document.getElementById('tab-' + id);
        btn.classList.toggle('active', id === tab);
    });
}

/* ── Cart → Active order items ── */
function loadOrderItems() {
    // Prefer last completed order, fall back to current cart
    const lastOrder = JSON.parse(localStorage.getItem('eutLastOrder') || 'null');
    const cart = lastOrder ? lastOrder.items.map(i => ({
        name: i.name, price: i.price, quantity: i.qty, image: i.img
    })) : JSON.parse(localStorage.getItem('eutCart') || '[]');

    const list = document.getElementById('orderItemsList');
    let sub = 0;
    if (!cart.length) {
        list.innerHTML = '<div style="padding:20px 18px; font-size:13px; color:#4b5563; text-align:center;">No items in active order</div>';
        document.getElementById('itemCountBadge').textContent = '0 items';
        document.getElementById('orderSubtotal').textContent = '₱0';
        document.getElementById('orderTotal').textContent = '₱50';
        return;
    }
    const total = cart.reduce((s,i) => s + i.quantity, 0);
    document.getElementById('itemCountBadge').textContent = total + (total === 1 ? ' item' : ' items');
    list.innerHTML = cart.map(item => {
        sub += item.price * item.quantity;
        return `<div class="item-row">
            <img src="${item.image}" alt="${item.name}" class="item-img">
            <div style="flex:1; min-width:0;">
                <p class="item-name">${item.name}</p>
                <p class="item-qty">Qty: ${item.quantity}</p>
            </div>
            <p class="item-price">₱${(item.price * item.quantity).toLocaleString()}</p>
        </div>`;
    }).join('');
    document.getElementById('orderSubtotal').textContent = '₱' + sub.toLocaleString();
    document.getElementById('orderTotal').textContent = '₱' + (sub + 50).toLocaleString();
}

/* ── Past Orders (delivered) ── */
const mockPastOrders = [
    {
        id: 'EUT-00421', date: 'July 15, 2026 · 7:32 PM', total: 820,
        items: [
            { name: 'EUT Classic Burger', qty: 1, price: 350, img: '/images/hero-burger.jpg' },
            { name: 'Crispy French Fries', qty: 2, price: 120, img: '/images/french-fries.jpg' },
            { name: 'Iced Tea', qty: 1, price: 80, img: '/images/combo-meal.jpg' },
        ]
    },
    {
        id: 'EUT-00388', date: 'July 10, 2026 · 12:15 PM', total: 420,
        items: [
            { name: 'Gourmet Cheeseburger', qty: 1, price: 420, img: '/images/gourmet-burger.jpg' },
        ]
    },
    {
        id: 'EUT-00344', date: 'June 28, 2026 · 8:05 PM', total: 950,
        items: [
            { name: 'EUT Classic Burger', qty: 2, price: 350, img: '/images/hero-burger.jpg' },
            { name: 'Crispy French Fries', qty: 1, price: 120, img: '/images/french-fries.jpg' },
        ]
    },
];

function buildPastOrders() {
    // Merge real orders from localStorage with mock history
    const realOrders = JSON.parse(localStorage.getItem('eutOrderHistory') || '[]');
    const allOrders = [...realOrders, ...mockPastOrders];
    const list = document.getElementById('pastOrdersList');
    if (!allOrders.length) { document.getElementById('pastEmpty').style.display = 'block'; return; }
    list.innerHTML = allOrders.map(o => `
        <div class="pcard">
            <div class="pcard-header">
                <div class="pcard-id-row">
                    <span class="pcard-id">#${o.id}</span>
                    <span class="badge badge-done">✓ Delivered</span>
                </div>
                <p class="pcard-date">${o.date}</p>
            </div>
            <div class="pcard-items">
                ${o.items.map(i => `
                <div class="pcard-item-row">
                    <img src="${i.img}" class="pcard-item-img" alt="${i.name}">
                    <span class="pcard-item-name">${i.name} × ${i.qty}</span>
                    <span class="pcard-item-price">₱${(i.price * i.qty).toLocaleString()}</span>
                </div>`).join('')}
            </div>
            <div class="pcard-footer">
                <div>
                    <p class="pcard-total-label">Total paid</p>
                    <p class="pcard-total">₱${o.total.toLocaleString()}</p>
                </div>
                <a href="{{ route('shop.home') }}" class="btn-reorder">🔁 Reorder</a>
            </div>
        </div>`).join('');
}

/* ── Cancelled Orders ── */
const cancelledOrders = [
    {
        id: 'EUT-00301', date: 'July 3, 2026 · 6:20 PM', total: 470,
        reason: 'Restaurant was temporarily closed',
        refund: 'Full refund processed · July 3, 2026',
        items: [
            { name: 'Combo Meal Deluxe', qty: 1, price: 390, img: '/images/combo-meal.jpg' },
            { name: 'Iced Tea', qty: 1, price: 80, img: '/images/combo-meal.jpg' },
        ]
    },
    {
        id: 'EUT-00278', date: 'June 20, 2026 · 1:45 PM', total: 350,
        reason: 'Cancelled by customer before preparation',
        refund: 'Full refund processed · June 20, 2026',
        items: [
            { name: 'EUT Classic Burger', qty: 1, price: 350, img: '/images/hero-burger.jpg' },
        ]
    },
];

function buildCancelledOrders() {
    const list = document.getElementById('cancelledOrdersList');
    if (!cancelledOrders.length) { document.getElementById('cancelledEmpty').style.display = 'block'; return; }
    list.innerHTML = cancelledOrders.map(o => `
        <div class="pcard pcard-cancelled">
            <div class="pcard-header">
                <div class="pcard-id-row">
                    <span class="pcard-id" style="color:#9ca3af;">#${o.id}</span>
                    <span class="badge badge-cancelled">✕ Cancelled</span>
                </div>
                <p class="pcard-date">${o.date}</p>
            </div>
            <div class="pcard-items" style="opacity:0.5;">
                ${o.items.map(i => `
                <div class="pcard-item-row">
                    <img src="${i.img}" class="pcard-item-img" alt="${i.name}" style="filter:grayscale(1);">
                    <span class="pcard-item-name" style="text-decoration:line-through; color:#4b5563;">${i.name} × ${i.qty}</span>
                    <span class="pcard-item-price" style="color:#4b5563; text-decoration:line-through;">₱${(i.price * i.qty).toLocaleString()}</span>
                </div>`).join('')}
            </div>
            <div class="cancel-reason">
                <span class="cancel-reason-icon">⚠️</span>
                <div>
                    <p class="cancel-reason-title">Cancellation Reason</p>
                    <p class="cancel-reason-text">${o.reason}</p>
                    <p class="cancel-reason-text" style="color:#4ade80; margin-top:4px;">✓ ${o.refund}</p>
                </div>
            </div>
            <div class="pcard-footer">
                <div>
                    <p class="pcard-total-label">Order total</p>
                    <p class="pcard-total">₱${o.total.toLocaleString()}</p>
                </div>
                <a href="{{ route('shop.home') }}" class="btn-reorder" style="background:linear-gradient(135deg,#374151,#4b5563); color:#e5e7eb; box-shadow:none;">Try Again</a>
            </div>
        </div>`).join('');
}
</script>
</body>
</html>
