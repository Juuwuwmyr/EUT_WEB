<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* ── HERO ── */
        .hero {
            background: linear-gradient(135deg, #b91c1c 0%, #dc2626 40%, #c2410c 100%);
            padding: 56px 20px 70px;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 240px; height: 240px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute; bottom: -40px; left: -40px;
            width: 180px; height: 180px;
            background: radial-gradient(circle, rgba(0,0,0,0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .hero-inner { max-width: 560px; margin: 0 auto; position: relative; z-index: 1; }
        .hero-avatar-row { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
        .hero-avatar {
            width: 62px; height: 62px; border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
            object-fit: cover; flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        }
        .hero-avatar-placeholder {
            width: 62px; height: 62px; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            border: 3px solid rgba(255,255,255,0.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; font-weight: 800; color: #fff;
            flex-shrink: 0; box-shadow: 0 4px 16px rgba(0,0,0,0.3);
        }
        .hero-greeting { font-size: 11px; color: rgba(255,255,255,0.6); letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 3px; }
        .hero-name { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #fff; }
        .hero-email { font-size: 12px; color: rgba(255,255,255,0.55); margin-top: 2px; }

        /* Stats row */
        .hero-stats {
            display: flex; gap: 0;
            background: rgba(0,0,0,0.2); border-radius: 14px;
            overflow: hidden; border: 1px solid rgba(255,255,255,0.1);
        }
        .hero-stat {
            flex: 1; padding: 12px 8px; text-align: center;
            border-right: 1px solid rgba(255,255,255,0.08);
        }
        .hero-stat:last-child { border-right: none; }
        .hero-stat-value { font-size: 18px; font-weight: 800; color: #fff; }
        .hero-stat-label { font-size: 10px; color: rgba(255,255,255,0.5); margin-top: 2px; }

        /* Theme toggle in hero */
        .hero-theme-btn {
            position: absolute; top: 14px; right: 16px;
            width: 34px; height: 34px; border-radius: 50%;
            background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; z-index: 2;
        }
        .hero-theme-btn:hover { background: rgba(0,0,0,0.35); }

        /* ── BODY ── */
        .page-body {
            max-width: 560px; margin: 0 auto;
            padding: 20px 0 110px;
            margin-top: -22px; /* overlap hero */
            position: relative; z-index: 2;
        }

        /* ── SECTION GROUPS ── */
        .section-card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 20px; overflow: hidden;
            margin: 0 14px 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }
        .section-label {
            font-size: 11px; font-weight: 700; color: #4b5563;
            text-transform: uppercase; letter-spacing: 0.07em;
            padding: 18px 18px 6px;
        }

        /* ── MENU ROWS ── */
        .menu-row {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            text-decoration: none; cursor: pointer;
            transition: background 0.15s;
            position: relative;
        }
        .menu-row:last-child { border-bottom: none; }
        .menu-row:hover { background: rgba(255,255,255,0.03); }
        .menu-row:active { background: rgba(255,255,255,0.05); }

        .menu-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .menu-text { flex: 1; }
        .menu-title { font-size: 14px; font-weight: 500; color: #e5e7eb; }
        .menu-sub { font-size: 11px; color: #4b5563; margin-top: 2px; }
        .menu-badge {
            font-size: 10px; font-weight: 700; padding: 2px 8px;
            border-radius: 99px; margin-right: 6px; flex-shrink: 0;
        }
        .badge-red { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
        .badge-yellow { background: rgba(250,204,21,0.1); color: #facc15; border: 1px solid rgba(250,204,21,0.2); }
        .badge-green { background: rgba(34,197,94,0.1); color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
        .chevron { color: #374151; flex-shrink: 0; transition: transform 0.15s; }
        .menu-row:hover .chevron { transform: translateX(2px); color: #6b7280; }

        /* ── ORDER QUICK-ACCESS ── */
        .order-quick {
            display: flex; padding: 16px 18px; gap: 0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .oq-item {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; gap: 6px;
            text-decoration: none; padding: 6px 4px;
            border-radius: 12px; transition: background 0.15s;
        }
        .oq-item:hover { background: rgba(255,255,255,0.04); }
        .oq-icon-wrap {
            width: 42px; height: 42px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .oq-label { font-size: 11px; color: #6b7280; font-weight: 500; text-align: center; }
        .oq-dot {
            position: absolute; top: -3px; right: -3px;
            width: 16px; height: 16px; border-radius: 50%;
            background: #ef4444; border: 2px solid #0e0f1a;
            font-size: 9px; font-weight: 700; color: #fff;
            display: flex; align-items: center; justify-content: center;
        }

        /* ── WALLET CARD ── */
        .wallet-row {
            display: flex; align-items: center; gap: 14px;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .wallet-row:last-child { border-bottom: none; }
        .wallet-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .wallet-text { flex: 1; }
        .wallet-name { font-size: 13px; font-weight: 600; color: #e5e7eb; }
        .wallet-val { font-size: 13px; font-weight: 700; }

        /* ── LOGOUT ── */
        .logout-btn {
            display: flex; align-items: center; gap: 14px;
            padding: 15px 18px; width: 100%;
            background: none; border: none; cursor: pointer;
            text-align: left; transition: background 0.15s;
            border-radius: 0;
        }
        .logout-btn:hover { background: rgba(239,68,68,0.06); }

        /* ── LIGHT MODE ── */
        .light-mode body { background: #f0f0f8 !important; }
        .light-mode .section-card { background: #fff !important; border-color: rgba(0,0,0,0.07) !important; box-shadow: 0 2px 12px rgba(0,0,0,0.06) !important; }
        .light-mode .menu-title { color: #111 !important; }
        .light-mode .section-label { color: #9ca3af !important; }
        .light-mode .menu-row:hover { background: rgba(0,0,0,0.03) !important; }
        .light-mode .chevron { color: #9ca3af !important; }
        .light-mode .wallet-name { color: #111 !important; }
        .light-mode .oq-label { color: #9ca3af !important; }
        .light-mode .oq-item:hover { background: rgba(0,0,0,0.03) !important; }
        .light-mode .oq-dot { border-color: #fff !important; }

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

<!-- ══════════ HERO HEADER ══════════ -->
<div class="hero">
    <button id="shopThemeToggle" class="hero-theme-btn">
        <svg id="shopSunIcon" width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="color:#fff;display:none;">
            <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <svg id="shopMoonIcon" width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="color:#fff;">
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
        </svg>
    </button>
    <div class="hero-inner">
        <div class="hero-avatar-row">
            @auth
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="hero-avatar">
                @else
                    <div class="hero-avatar-placeholder">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                @endif
                <div>
                    <p class="hero-greeting">Welcome back</p>
                    <p class="hero-name">{{ auth()->user()->name }}</p>
                    <p class="hero-email">{{ auth()->user()->email }}</p>
                </div>
            @else
                <div class="hero-avatar-placeholder">?</div>
                <div>
                    <p class="hero-greeting">Hello there</p>
                    <p class="hero-name">Guest User</p>
                    <p class="hero-email">Login to access all features</p>
                </div>
            @endauth
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <p class="hero-stat-value" id="statOrders">3</p>
                <p class="hero-stat-label">Orders</p>
            </div>
            <div class="hero-stat">
                <p class="hero-stat-value">₱1,590</p>
                <p class="hero-stat-label">Spent</p>
            </div>
            <div class="hero-stat">
                <p class="hero-stat-value">4.9</p>
                <p class="hero-stat-label">Rating</p>
            </div>
            <div class="hero-stat">
                <p class="hero-stat-value" id="statReviews">2</p>
                <p class="hero-stat-label">Reviews</p>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ PAGE BODY ══════════ -->
<div class="page-body">

    <!-- ── ACCOUNT ── -->
    <p class="section-label" style="padding-left:32px;">Account</p>
    <div class="section-card">
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(250,204,21,0.1);">
                <svg width="18" height="18" fill="none" stroke="#facc15" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">My Profile</p><p class="menu-sub">Edit personal information</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(239,68,68,0.1);">
                <svg width="18" height="18" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">My Addresses</p><p class="menu-sub">Manage delivery addresses</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(96,165,250,0.1);">
                <svg width="18" height="18" fill="none" stroke="#60a5fa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Payment Methods</p><p class="menu-sub">Cards, GCash &amp; more</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- ── SETTINGS ── -->
    <p class="section-label" style="padding-left:32px;">Settings</p>
    <div class="section-card">
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(52,211,153,0.1);">
                <svg width="18" height="18" fill="none" stroke="#34d399" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Notifications</p><p class="menu-sub">Push, email &amp; SMS alerts</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(167,139,250,0.1);">
                <svg width="18" height="18" fill="none" stroke="#a78bfa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Security</p><p class="menu-sub">Password &amp; passkey</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(251,146,60,0.1);">
                <svg width="18" height="18" fill="none" stroke="#fb923c" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Preferences</p><p class="menu-sub">Language, theme &amp; display</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- ── SUPPORT ── -->
    <p class="section-label" style="padding-left:32px;">Support</p>
    <div class="section-card">
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(250,204,21,0.1);">
                <svg width="18" height="18" fill="none" stroke="#facc15" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Help Center</p><p class="menu-sub">FAQs &amp; support tickets</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(96,165,250,0.1);">
                <svg width="18" height="18" fill="none" stroke="#60a5fa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Contact Us</p><p class="menu-sub">Chat, call or email</p></div>
            <span class="menu-badge badge-green">Online</span>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- ── LEGAL ── -->
    <p class="section-label" style="padding-left:32px;">Legal</p>
    <div class="section-card">
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(255,255,255,0.05);">
                <svg width="18" height="18" fill="none" stroke="#6b7280" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Terms &amp; Conditions</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="#" class="menu-row">
            <div class="menu-icon" style="background:rgba(255,255,255,0.05);">
                <svg width="18" height="18" fill="none" stroke="#6b7280" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Privacy Notice</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- ── LOGOUT / LOGIN ── -->
    <div class="section-card" style="margin-bottom:20px;">
        @auth
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="logout-btn" style="width:100%;">
                <div class="menu-icon" style="background:rgba(239,68,68,0.1);">
                    <svg width="18" height="18" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                <span style="font-size:14px; font-weight:600; color:#f87171; flex:1; text-align:left;">Log Out</span>
            </button>
        </form>
        @else
        <a href="{{ route('home') }}" class="menu-row">
            <div class="menu-icon" style="background:rgba(250,204,21,0.1);">
                <svg width="18" height="18" fill="none" stroke="#facc15" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            </div>
            <span style="font-size:14px; font-weight:600; color:#facc15; flex:1;">Log In / Sign Up</span>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
        @endauth
    </div>

    <p style="text-align:center; font-size:11px; color:#1f2937; padding-bottom:8px;">EUT Restaurant v1.0.0 · Made with ❤️</p>

</div><!-- /page-body -->

<!-- ══════════ BOTTOM NAV ══════════ -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Home
        </a>
        <a href="{{ route('shop.tracking') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Orders
        </a>
        <a href="{{ route('shop.cart') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Cart
        </a>
        <a href="{{ route('shop.profile') }}" class="bnav-item active">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profile
        </a>
    </div>
</nav>

<script>
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
    // Pull real order count
    const history = JSON.parse(localStorage.getItem('eutOrderHistory') || '[]');
    const total = history.length + 3; // 3 mock orders
    document.getElementById('statOrders').textContent = total;
});
</script>
</body>
</html>
