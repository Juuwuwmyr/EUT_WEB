<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - E.U.T Snack House</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet"></noscript>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"></noscript>
</head>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
body{background:#080810;color:#fff;min-height:100vh;}
.topnav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(8,8,16,1);border-bottom:1px solid rgba(255,255,255,.06);will-change:transform;transform:translate3d(0,0,0);}
.topnav-inner{max-width:540px;margin:0 auto;padding:14px 16px 0;}
.topnav-row{display:flex;align-items:center;gap:10px;}
.back-btn{width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:#9ca3af;text-decoration:none;transition:all .2s;flex-shrink:0;}
.back-btn:hover{background:rgba(255,255,255,.12);color:#fff;}
.topnav-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;flex:1;}
.theme-btn{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;flex-shrink:0;}
.tabs-bar{display:flex;margin-top:14px;border-bottom:1px solid rgba(255,255,255,.06);}
.tab{padding:10px 20px;font-size:13px;font-weight:600;color:#6b7280;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;transition:all .2s;position:relative;bottom:-1px;}
.tab.active{color:#facc15;border-bottom-color:#facc15;}
.tab-dot{display:inline-block;width:6px;height:6px;background:#ef4444;border-radius:50%;margin-left:5px;vertical-align:middle;animation:blink 1.5s ease-in-out infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.page-body{max-width:540px;margin:0 auto;padding:130px 16px 110px;}

/* ── SUMMARY STAT CARDS ── */
.summary-stat-card{background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:14px;padding:10px 6px 8px;text-align:center;transition:border-color .2s;overflow:hidden;min-width:0;}
.ssc-icon{display:flex;align-items:center;justify-content:center;height:20px;margin-bottom:4px;overflow:hidden;flex-shrink:0;}
.ssc-icon svg{width:16px;height:16px;flex-shrink:0;}
.ssc-val{font-size:15px;font-weight:900;color:#facc15;margin:0 0 2px;line-height:1;}
.ssc-lbl{font-size:10px;color:#4b5563;margin:0;text-transform:uppercase;letter-spacing:.04em;}

/* ── ORDER CARD (compact) ── */
.ocard{background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:18px;overflow:hidden;margin-bottom:12px;box-shadow:0 4px 20px rgba(0,0,0,.4);cursor:pointer;transition:all .2s;-webkit-tap-highlight-color:transparent;}
.ocard:hover{border-color:rgba(250,204,21,.25);transform:translateY(-1px);}
.ocard:active{transform:scale(.98);}
.ocard-top{display:flex;align-items:center;justify-content:space-between;padding:13px 16px 10px;}
.ocard-num{font-size:13px;font-weight:800;color:#fff;}
.ocard-date{font-size:11px;color:#4b5563;margin-top:2px;}
.ocard-images{display:flex;gap:-6px;padding:0 16px 10px;align-items:center;}
.ocard-img{width:40px;height:40px;border-radius:9px;object-fit:cover;border:2px solid #0e0f1a;margin-right:-8px;flex-shrink:0;}
.ocard-img:last-child{margin-right:0;}
.ocard-more{width:40px;height:40px;border-radius:9px;background:rgba(255,255,255,.06);border:2px solid #0e0f1a;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#6b7280;flex-shrink:0;}
.ocard-bottom{display:flex;align-items:center;justify-content:space-between;padding:10px 16px 13px;border-top:1px solid rgba(255,255,255,.04);}
.ocard-total{font-size:15px;font-weight:800;color:#facc15;}
.ocard-total-label{font-size:10px;color:#4b5563;margin-bottom:1px;}
.ocard-chevron{color:#4b5563;}

/* Status badge */
.badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:700;}
.badge-active{background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.25);}
.badge-done{background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.22);}
.badge-pending{background:rgba(245,158,11,.1);color:#fbbf24;border:1px solid rgba(245,158,11,.2);}
.badge-cancelled{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.18);}
.badge-pulse{width:6px;height:6px;background:#ef4444;border-radius:50%;animation:blink 1.2s infinite;}

/* ── PROGRESS BAR (inside sheet) ── */
.progress-track{height:6px;background:#1a1b2e;border-radius:99px;overflow:hidden;}
.progress-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#dc2626,#f59e0b,#facc15);transition:width 1.2s cubic-bezier(.4,0,.2,1);}
.progress-steps{display:flex;justify-content:space-between;margin-top:8px;}
.step-label{font-size:10px;color:#374151;}
.step-label.done{color:#facc15;}
.step-label.now{color:#ef4444;font-weight:700;}

/* ── TIMELINE ── */
.timeline{padding:16px 18px;display:flex;flex-direction:column;gap:0;}
.tl-step{display:flex;gap:12px;position:relative;}
.tl-step:not(:last-child)::after{content:'';position:absolute;left:11px;top:24px;width:2px;bottom:-2px;background:rgba(255,255,255,.06);}
.tl-step.tl-done::after{background:rgba(250,204,21,.3);}
.tl-dot{width:24px;height:24px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:12px;border:2px solid rgba(255,255,255,.08);background:#0e0f1a;margin-top:2px;}
.tl-done .tl-dot{background:rgba(250,204,21,.15);border-color:rgba(250,204,21,.4);color:#facc15;}
.tl-now .tl-dot{background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.5);color:#f87171;animation:pulse-dot .9s ease-in-out infinite;}
@keyframes pulse-dot{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,.4)}50%{box-shadow:0 0 0 6px rgba(239,68,68,0)}}
.tl-body{padding-bottom:18px;flex:1;}
.tl-title{font-size:13px;font-weight:700;color:#fff;margin-bottom:2px;}
.tl-done .tl-title{color:#facc15;}
.tl-future .tl-title{color:#374151;}
.tl-time{font-size:11px;color:#4b5563;}

/* ── SHEET ── */
.sheet-backdrop{position:fixed;inset:0;z-index:300;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);opacity:0;pointer-events:none;transition:opacity .3s;}
.sheet-backdrop.open{opacity:1;pointer-events:all;}
.sheet{position:fixed;bottom:0;left:50%;transform:translateX(-50%) translateY(110%);width:100%;max-width:540px;z-index:400;background:#0e0f1a;border-radius:24px 24px 0 0;border:1px solid rgba(255,255,255,.08);border-bottom:none;transition:transform .38s cubic-bezier(.32,.72,0,1);max-height:92vh;overflow-y:auto;}
@media(max-width:540px){.sheet{left:0;transform:translateY(110%);}}
.sheet.open{transform:translateX(-50%) translateY(0);}
@media(max-width:540px){.sheet.open{transform:translateY(0);}}
.sheet-handle{width:40px;height:4px;border-radius:99px;background:rgba(255,255,255,.15);margin:12px auto 0;}
.sheet-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px 10px;}
.sheet-head-title{font-size:16px;font-weight:800;color:#fff;}
.sheet-close{width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#6b7280;font-size:14px;}
.sheet-close:hover{background:rgba(255,255,255,.12);color:#fff;}
.sheet-divider{height:1px;background:rgba(255,255,255,.06);margin:0 18px;}
.sheet-section{padding:14px 18px 0;}
.sheet-section-title{font-size:11px;font-weight:700;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;}

/* ── ITEM ROW (inside sheet) ── */
.sitem{display:flex;align-items:flex-start;gap:10px;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid rgba(255,255,255,.04);}
.sitem:last-child{border-bottom:none;margin-bottom:0;padding-bottom:0;}
.sitem-img{width:46px;height:46px;border-radius:10px;object-fit:cover;flex-shrink:0;}
.sitem-name{font-size:13px;font-weight:600;color:#e5e7eb;}
.sitem-meta{font-size:11px;color:#4b5563;margin-top:2px;}
.sitem-price{font-size:13px;font-weight:700;color:#facc15;flex-shrink:0;margin-left:auto;padding-left:8px;}

/* totals */
.tot-row{display:flex;justify-content:space-between;align-items:center;padding:8px 18px;}
.tot-label{font-size:12px;color:#6b7280;}
.tot-val{font-size:12px;color:#9ca3af;}
.tot-grand{font-size:15px;font-weight:800;color:#facc15;}
.tot-grand-label{font-size:14px;font-weight:700;color:#fff;}

/* info rows */
.irow{display:flex;align-items:flex-start;gap:10px;padding:10px 18px;border-top:1px solid rgba(255,255,255,.04);}
.irow-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.irow-label{font-size:10px;color:#4b5563;margin-bottom:2px;text-transform:uppercase;letter-spacing:.05em;}
.irow-val{font-size:12px;color:#e5e7eb;font-weight:500;}
.irow-sub{font-size:11px;color:#6b7280;margin-top:1px;}

/* empty */
.empty-state{text-align:center;padding:64px 24px;}
.empty-icon{font-size:56px;margin-bottom:16px;opacity:.7;}
.empty-title{font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;}
.empty-sub{font-size:13px;color:#4b5563;margin-bottom:24px;line-height:1.6;}
.btn-primary{background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;padding:12px 28px;border-radius:99px;font-weight:700;font-size:14px;text-decoration:none;display:inline-block;}
.btn-reorder{background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;padding:8px 18px;border-radius:99px;font-size:12px;font-weight:700;text-decoration:none;display:inline-block;}

/* bottom nav */
.bottom-nav{position:fixed;bottom:0;left:0;right:0;background:rgba(8,8,16,1);border-top:1px solid rgba(255,255,255,.07);padding:10px 0 14px;z-index:100;will-change:transform;transform:translate3d(0,0,0);}
@media(min-width:1024px){.bottom-nav{display:none;}}
.bottom-nav-inner{display:flex;}
.bnav-item{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:#4b5563;text-decoration:none;font-size:10px;font-weight:500;transition:color .15s;}
.bnav-item.active{color:#facc15;}

/* light mode */
.light-mode body{background:#f0f0f8!important;}
.light-mode .topnav{background:rgba(255,255,255,.96)!important;border-color:rgba(0,0,0,.07)!important;}

@keyframes rider-pulse {
    0%   { transform: scale(1);   opacity: .6; }
    100% { transform: scale(2.2); opacity: 0;  }
}
.light-mode .topnav-title{color:#111!important;}
.light-mode .ocard{background:#fff!important;border-color:rgba(0,0,0,.07)!important;}
.light-mode .ocard-num,.light-mode .empty-title{color:#111!important;}
.light-mode .back-btn,.light-mode .theme-btn{background:rgba(0,0,0,.05)!important;border-color:rgba(0,0,0,.08)!important;color:#555!important;}
.light-mode .sheet{background:#fff!important;border-color:rgba(0,0,0,.07)!important;}
.light-mode .sheet-head-title,.light-mode .tl-title,.light-mode .sitem-name,.light-mode .tot-grand-label,.light-mode .irow-val{color:#111!important;}
.light-mode .bottom-nav{background:rgba(255,255,255,.97)!important;border-color:rgba(0,0,0,.07)!important;}
</style>
<body>
<!-- NAVBAR -->
<nav class="topnav">
    <div class="topnav-inner">
        <div class="topnav-row">
            <a href="{{ route('shop.home') }}" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <span class="topnav-title">My Orders</span>
            <button id="shopThemeToggle" class="theme-btn">
                <svg id="shopSunIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#facc15;display:none;"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg id="shopMoonIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#9ca3af;"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
            </button>
        </div>
        <div class="tabs-bar">
            <button class="tab active" id="tab-all"       onclick="switchTab('all')">All <span id="allDot" class="tab-dot" style="display:none;"></span></button>
            <button class="tab"        id="tab-past"      onclick="switchTab('past')">Past</button>
            <button class="tab"        id="tab-cancelled" onclick="switchTab('cancelled')">Cancelled</button>
        </div>
    </div>
</nav>

<!-- PAGE BODY -->
<div class="page-body">

    @guest
    <!-- ── GUEST GATE ── -->
    <div style="text-align:center; padding: 60px 24px 40px;">
        <div style="width:96px;height:96px;margin:0 auto 24px;background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,0.07);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 32px rgba(0,0,0,0.4);">
            <svg width="44" height="44" fill="none" stroke="#4b5563" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#fff;margin-bottom:8px;">Track your orders</h2>
        <p style="font-size:14px;color:#6b7280;line-height:1.7;margin-bottom:32px;max-width:280px;margin-left:auto;margin-right:auto;">
            Sign in to view your order history, live status, and delivery tracking.
        </p>
        <div style="display:flex;flex-direction:column;gap:10px;max-width:320px;margin:0 auto 24px;">
            <a href="{{ route('restaurant') }}#login"
               style="display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 24px;border-radius:14px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;font-size:15px;font-weight:700;text-decoration:none;box-shadow:0 4px 18px rgba(220,38,38,0.38);">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Log In
            </a>
            <a href="{{ route('restaurant') }}#register"
               style="display:flex;align-items:center;justify-content:center;gap:8px;padding:14px 24px;border-radius:14px;background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;font-size:15px;font-weight:700;text-decoration:none;box-shadow:0 4px 16px rgba(245,158,11,0.3);">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Create Account
            </a>
        </div>
        <a href="{{ route('shop.home') }}" style="font-size:13px;color:#4b5563;text-decoration:none;display:inline-flex;align-items:center;gap:5px;" onmouseover="this.style.color='#9ca3af'" onmouseout="this.style.color='#4b5563'">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Browse the menu instead
        </a>
    </div>
    @endguest

    @auth
    <!-- Summary Stats Banner (JS-rendered) -->
    <div id="orderSummaryBanner" style="display:none;margin-bottom:16px;">
        <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;min-width:0;overflow:hidden;">
            <div class="summary-stat-card" id="ssc-active">
                <span class="ssc-icon"><svg width="16" height="16" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg></span>
                <p class="ssc-val" id="ssc-val-active">0</p>
                <p class="ssc-lbl">Active</p>
            </div>
            <div class="summary-stat-card" id="ssc-total">
                <span class="ssc-icon"><svg width="16" height="16" fill="none" stroke="#6366f1" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></span>
                <p class="ssc-val" id="ssc-val-total">0</p>
                <p class="ssc-lbl">Total</p>
            </div>
            <div class="summary-stat-card" id="ssc-delivered">
                <span class="ssc-icon"><svg width="16" height="16" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                <p class="ssc-val" id="ssc-val-delivered">0</p>
                <p class="ssc-lbl">Delivered</p>
            </div>
            <div class="summary-stat-card" id="ssc-cancelled">
                <span class="ssc-icon"><svg width="16" height="16" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                <p class="ssc-val" id="ssc-val-cancelled">0</p>
                <p class="ssc-lbl">Cancelled</p>
            </div>
            <div class="summary-stat-card" id="ssc-spent">
                <span class="ssc-icon"><svg width="16" height="16" fill="none" stroke="#facc15" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></span>
                <p class="ssc-val" id="ssc-val-spent" style="font-size:12px;">₱0</p>
                <p class="ssc-lbl">Spent</p>
            </div>
        </div>
    </div>

    <div id="view-all">
        <div class="empty-state"><div class="empty-icon"><svg width="40" height="40" fill="none" stroke="#4b5563" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg></div><p class="empty-title">Loading…</p></div>
    </div>
    <div id="view-past"      style="display:none;"></div>
    <div id="view-cancelled" style="display:none;"></div>
</div>
@endauth

<!-- ORDER DETAIL SHEET -->
@auth
<div class="sheet-backdrop" id="detailBackdrop" onclick="closeDetail()"></div>
<div class="sheet" id="detailSheet">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
        <span class="sheet-head-title" id="detailOrderNum">#EUT-00000</span>
        <button class="sheet-close" onclick="closeDetail()">&#x2715;</button>
    </div>
    <div class="sheet-divider"></div>
    <div id="detailBody"></div>
</div>

<!-- CANCEL MODAL -->
<div id="cancelModalBackdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;backdrop-filter:blur(4px);" onclick="closeCancelModal()"></div>
<div id="cancelModal" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:1001;background:linear-gradient(145deg,#1a0a0a,#120808);border:1px solid rgba(239,68,68,.25);border-radius:24px 24px 0 0;padding:24px 20px 40px;max-width:540px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div><p style="font-size:17px;font-weight:800;color:#fff;">Cancel Order</p><p style="font-size:12px;color:#6b7280;margin-top:2px;">Tell us why you're cancelling</p></div>
        <button onclick="closeCancelModal()" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);color:#9ca3af;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;">&#x2715;</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px;" id="cancelReasons">
        @foreach(['Changed my mind','Ordered by mistake','Found a better option','Taking too long','Other reason'] as $reason)
        <label style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;border:1.5px solid rgba(255,255,255,.07);cursor:pointer;transition:all .2s;">
            <input type="radio" name="cancelReason" value="{{ $reason }}" style="accent-color:#ef4444;width:16px;height:16px;" onchange="document.querySelectorAll('#cancelReasons label').forEach(l=>l.style.borderColor='rgba(255,255,255,.07)');this.closest('label').style.borderColor='rgba(239,68,68,.5)';">
            <span style="font-size:13px;color:#d1d5db;font-weight:500;">{{ $reason }}</span>
        </label>
        @endforeach
    </div>
    <div id="cancelModalError" style="display:none;color:#f87171;font-size:12px;margin-bottom:10px;padding:8px 12px;background:rgba(239,68,68,.08);border-radius:8px;"></div>
    <button id="confirmCancelBtn" onclick="submitCancel()" style="width:100%;padding:14px;border-radius:14px;background:linear-gradient(135deg,#ef4444,#dc2626);border:none;color:#fff;font-size:15px;font-weight:800;cursor:pointer;box-shadow:0 4px 20px rgba(239,68,68,.3);">Yes, Cancel My Order</button>
    <button onclick="closeCancelModal()" style="width:100%;padding:12px;margin-top:8px;border-radius:14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);color:#9ca3af;font-size:14px;font-weight:600;cursor:pointer;">Never Mind</button>
</div>
@endauth

<!-- BOTTOM NAV -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>Menu</a>
        <a href="{{ route('shop.tracking') }}" class="bnav-item active"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Orders</a>
        <a href="{{ route('shop.cart') }}" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Cart</a>
        <a href="{{ route('shop.profile') }}" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Profile</a>
    </div>
</nav>

<script>
/* ── Theme ── */
function applyTheme(t){document.documentElement.classList.toggle('light-mode',t==='light');document.getElementById('shopSunIcon').style.display=t==='dark'?'block':'none';document.getElementById('shopMoonIcon').style.display=t==='light'?'block':'none';}

/* ── Tab switching ── */
let currentTab = 'all';
function switchTab(tab) {
    currentTab = tab;
    ['all','past','cancelled'].forEach(id => {
        document.getElementById('view-'+id).style.display = id===tab ? 'block' : 'none';
        document.getElementById('tab-'+id).classList.toggle('active', id===tab);
    });
}

/* ── Helpers ── */
function escHtml(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function modifierTagsHtml(modifiers) {
    if(!modifiers||!modifiers.length)return'';
    const tc={
        flavor:  {bg:'rgba(59,130,246,.12)', c:'#3b82f6', i:'<svg width="10" height="10" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>'},
        modifier:{bg:'rgba(139,92,246,.12)', c:'#8b5cf6', i:'<svg width="10" height="10" fill="none" stroke="#8b5cf6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>'},
        addon:   {bg:'rgba(245,158,11,.12)', c:'#d97706', i:'<svg width="10" height="10" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>'},
    };
    const tags=(modifiers||[]).filter(m=>m&&m.name&&!/^no\s/i.test(m.name)).map(m=>{
        const s=tc[m.type]||tc.modifier;const adj=parseFloat(m.price_adjustment||0);
        const ex=(m.price_type==='add'&&adj>0)?` <span style="color:#4ade80;font-size:.6rem;">+&#8369;${adj.toLocaleString()}</span>`:'';
        return`<span style="display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .55rem;border-radius:99px;font-size:.68rem;font-weight:600;background:${s.bg};color:${s.c};border:1px solid ${s.c}30;white-space:nowrap;">${s.i} ${escHtml(m.name)}${ex}</span>`;
    });
    return tags.length?`<div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">${tags.join('')}</div>`:'';
}

const STATUS_CFG = {
    pending:          {label:'Order Placed',   badge:'badge-pending',  icon:'<svg width="13" height="13" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>', progress:10},
    accepted:         {label:'Accepted',        badge:'badge-active',   icon:'<svg width="13" height="13" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>', progress:25},
    preparing:        {label:'Preparing',       badge:'badge-active',   icon:'<svg width="13" height="13" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>', progress:45},
    rider_assigned:   {label:'Rider Assigned',  badge:'badge-active',   icon:'<svg width="13" height="13" fill="none" stroke="#8b5cf6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>', progress:65},
    out_for_delivery: {label:'Out for Delivery',badge:'badge-active',   icon:'<svg width="13" height="13" fill="none" stroke="#8b5cf6" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3"/></svg>', progress:82},
    delivered:        {label:'Delivered',       badge:'badge-done',     icon:'<svg width="13" height="13" fill="none" stroke="#10b981" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>', progress:100},
    cancelled:        {label:'Cancelled',       badge:'badge-cancelled',icon:'<svg width="13" height="13" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>', progress:0},
};
const TIMELINE_STEPS = ['pending','accepted','preparing','rider_assigned','out_for_delivery','delivered'];

/* ────────────────────────────────
   COMPACT ORDER CARD
──────────────────────────────── */
function buildOrderCard(o) {
    const cfg = STATUS_CFG[o.status] || STATUS_CFG.pending;
    const imgs = o.items.slice(0,3).map(i=>
        `<img src="${escHtml(i.image)}" class="ocard-img" alt="${escHtml(i.name)}" loading="lazy" decoding="async" onerror="this.src='{{ asset('images/hero-burger.webp') }}'">`)
        .join('');
    const extra = o.items.length > 3 ? `<div class="ocard-more">+${o.items.length-3}</div>` : '';
    const isLive = !['delivered','cancelled'].includes(o.status);
    return `
    <div class="ocard" onclick="openDetail(${o.id})">
        <div class="ocard-top">
            <div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <p class="ocard-num">#${escHtml(o.order_number)}</p>
                    <span style="font-size:10px;padding:1px 5px;border-radius:4px;background:rgba(255,255,255,.05);color:var(--text-muted);border:1px solid rgba(255,255,255,.1);display:inline-flex;align-items:center;gap:3px;">
                        ${o.order_type_icon} ${o.order_type_label}
                    </span>
                </div>
                <p class="ocard-date">${escHtml(o.placed_at)}</p>
            </div>
            <span class="badge ${cfg.badge}">${isLive?'<span class="badge-pulse"></span> ':''} ${escHtml(cfg.label)}</span>
        </div>
        <div class="ocard-images" style="padding:0 16px 10px;">${imgs}${extra}</div>
        <div class="ocard-bottom">
            <div>
                <p class="ocard-total-label">${o.items.length} item${o.items.length!==1?'s':''}</p>
                <p class="ocard-total">₱${Number(o.total).toLocaleString()}</p>
            </div>
            <svg class="ocard-chevron" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </div>`;
}

function emptyState(icon, title, sub, showBtn) {
    return `<div class="empty-state">
        <div class="empty-icon">${icon}</div>
        <p class="empty-title">${title}</p>
        <p class="empty-sub">${sub}</p>
        ${showBtn?`<a href="{{ route('shop.home') }}" class="btn-primary">Browse Menu</a>`:''}
    </div>`;
}

/* ────────────────────────────────
   RENDER TABS
──────────────────────────────── */
let allOrders = [];

function renderAll() {
    const active    = allOrders.filter(o=>!['delivered','cancelled'].includes(o.status));
    const past      = allOrders.filter(o=>o.status==='delivered');
    const cancelled = allOrders.filter(o=>o.status==='cancelled');

    // ── Update summary stats banner ──
    const banner = document.getElementById('orderSummaryBanner');
    if (allOrders.length > 0 && banner) {
        banner.style.display = 'block';
        const totalSpent = past.reduce((s,o) => s + (parseFloat(o.total)||0), 0);
        document.getElementById('ssc-val-active').textContent    = active.length;
        document.getElementById('ssc-val-total').textContent     = allOrders.length;
        document.getElementById('ssc-val-delivered').textContent = past.length;
        document.getElementById('ssc-val-cancelled').textContent = cancelled.length;
        document.getElementById('ssc-val-spent').textContent     = '₱' + totalSpent.toLocaleString();
        // Highlight active card if there are active orders
        document.getElementById('ssc-active').style.borderColor = active.length > 0 ? 'rgba(250,204,21,.35)' : '';
    }

    // Tab dot on All tab — blink when there are active orders
    document.getElementById('allDot').style.display = active.length ? 'inline-block' : 'none';

    // All tab — shows active/in-progress orders only
    document.getElementById('view-all').innerHTML = active.length
        ? active.map(o=>buildOrderCard(o)).join('')
        : emptyState('<svg width="40" height="40" fill="none" stroke="#4b5563" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>','No active orders','Place an order and track it here.',true);

    // Past tab
    document.getElementById('view-past').innerHTML = past.length
        ? past.map(o=>buildOrderCard(o)).join('')
        : emptyState('<svg width="40" height="40" fill="none" stroke="#4b5563" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>','No completed orders','Your delivered orders will show here.',false);

    // Cancelled tab
    document.getElementById('view-cancelled').innerHTML = cancelled.length
        ? cancelled.map(o=>buildOrderCard(o)).join('')
        : emptyState('<svg width="40" height="40" fill="none" stroke="#4b5563" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>','No cancelled orders','Great — you haven&#39;t cancelled anything.',false);
}

/* ────────────────────────────────
   DETAIL SHEET
──────────────────────────────── */
let detailOrderId = null;
const activeMaps  = {};

function openDetail(orderId) {
    detailOrderId = orderId;
    const o = allOrders.find(x=>x.id===orderId);
    if(!o)return;
    document.getElementById('detailOrderNum').textContent = '#' + o.order_number;
    document.getElementById('detailBody').innerHTML = buildDetailBody(o);
    document.getElementById('detailBackdrop').classList.add('open');
    document.getElementById('detailSheet').classList.add('open');
    document.body.style.overflow = 'hidden';
    // Init map inside sheet for active orders
    if(!['delivered','cancelled'].includes(o.status)) {
        setTimeout(()=>{ if(typeof initOrderMap==='function') initOrderMap(o); }, 300);
    }
}

function closeDetail() {
    document.getElementById('detailBackdrop').classList.remove('open');
    document.getElementById('detailSheet').classList.remove('open');
    document.body.style.overflow = '';
    // Destroy the Leaflet map so it re-initialises fresh on next open
    if(detailOrderId && activeMaps[detailOrderId]) {
        try { activeMaps[detailOrderId].map.remove(); } catch(e) {}
        delete activeMaps[detailOrderId];
    }
}

function buildDetailBody(o) {
    const cfg = STATUS_CFG[o.status] || STATUS_CFG.pending;
    const isActive = !['delivered','cancelled'].includes(o.status);
    const cancellable = ['pending','accepted','preparing'].includes(o.status);
    const isDelivery = o.order_type === 'delivery';

    // ── Progress bar (active only) ──
    const isPlaced    = o.status==='pending';
    const isPreparing = ['accepted','preparing'].includes(o.status);
    const isOnWay     = ['rider_assigned','out_for_delivery'].includes(o.status);
    const isDelivered = o.status==='delivered';

    const progressHtml = isActive ? `
        <div style="padding:16px 18px 0;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <span style="font-size:11px;color:#6b7280;">Status</span>
                <span style="font-size:13px;font-weight:700;color:#facc15;">${escHtml(cfg.label)}</span>
            </div>
            <div class="progress-track"><div class="progress-fill" style="width:${cfg.progress}%"></div></div>
            <div class="progress-steps">
                <span class="step-label ${isPlaced?'now':!isPlaced?'done':''}">Placed</span>
                <span class="step-label ${isPreparing?'now':(isOnWay||isDelivered)?'done':''}">Preparing</span>
                ${isDelivery ? `<span class="step-label ${isOnWay?'now':isDelivered?'done':''}">On the way</span>` : ''}
                <span class="step-label ${isDelivered?'done':''}">${!isDelivery ? (o.order_type === 'pickup' ? 'Picked Up' : 'Completed') : 'Delivered'}</span>
            </div>
        </div>
        <div class="sheet-divider" style="margin-top:14px;"></div>` : '';

    // ── Timeline ──
    const timelineSteps = isDelivery 
        ? ['pending','accepted','preparing','rider_assigned','out_for_delivery','delivered']
        : ['pending','accepted','preparing','delivered'];

    const stepsArr = o.status === 'cancelled'
        ? [{key:'placed',label:'Order Placed',time:o.placed_at,done:true},{key:'cancelled',label:'Cancelled',time:o.cancelled_at||'',done:true,is_cancel:true}]
        : timelineSteps.map((s,i) => {
            const currentIdx = timelineSteps.indexOf(o.status);
            const isDone = i < currentIdx || o.status === 'delivered';
            const isNow  = s === o.status;
            const timeMap = {pending:o.placed_at,accepted:o.accepted_at,preparing:o.accepted_at,rider_assigned:o.assigned_at,out_for_delivery:o.picked_up_at,delivered:o.delivered_at};
            let label = STATUS_CFG[s]?.label||s;
            if(s === 'delivered' && !isDelivery) label = o.order_type === 'pickup' ? 'Picked Up' : 'Completed';
            return {key:s,label:label,time:timeMap[s]||'',done:isDone,isNow,future:!isDone&&!isNow};
        });

    const timelineHtml = `
        <div class="sheet-section"><p class="sheet-section-title">Order Timeline</p></div>
        <div class="timeline">
            ${stepsArr.map(step=>`
            <div class="tl-step ${step.done?'tl-done':step.isNow?'tl-now':'tl-future'}">
                <div class="tl-dot">${step.done?'<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>':step.isNow?'<svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="currentColor"/></svg>':'<svg width="8" height="8" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" fill="none" stroke="currentColor" stroke-width="1.5"/></svg>'}</div>
                <div class="tl-body">
                    <p class="tl-title">${escHtml(step.label)}</p>
                    ${step.time?`<p class="tl-time">${escHtml(step.time)}</p>`:''}
                </div>
            </div>`).join('')}
        </div>
        <div class="sheet-divider"></div>`;

    // ── Items ──
    const itemsHtml = `
        <div class="sheet-section" style="padding-bottom:14px;">
            <p class="sheet-section-title">Items (${o.items.length})</p>
            ${o.items.map(i=>`
            <div class="sitem">
                <img src="${escHtml(i.image)}" class="sitem-img" alt="${escHtml(i.name)}" loading="lazy" decoding="async" onerror="this.src='{{ asset('images/hero-burger.webp') }}'">
                <div style="flex:1;min-width:0;">
                    <p class="sitem-name">${escHtml(i.name)}</p>
                    <p class="sitem-meta">x${i.qty}</p>
                    ${modifierTagsHtml(i.modifiers)}
                </div>
                <span class="sitem-price">₱${Number(i.subtotal).toLocaleString()}</span>
            </div>`).join('')}
        </div>
        <div class="sheet-divider"></div>`;

    // ── Totals ──
    const totalsHtml = `
        <div style="padding-top:4px;">
            <div class="tot-row"><span class="tot-label">Subtotal</span><span class="tot-val">₱${Number(o.subtotal).toLocaleString()}</span></div>
            <div class="tot-row"><span class="tot-label">Delivery fee</span><span class="tot-val">₱${Number(o.delivery_fee).toLocaleString()}</span></div>
            <div class="tot-row" style="padding-top:10px;padding-bottom:10px;border-top:1px solid rgba(255,255,255,.05);">
                <span class="tot-grand-label">Total</span><span class="tot-grand">₱${Number(o.total).toLocaleString()}</span>
            </div>
        </div>
        <div class="sheet-divider"></div>`;

    // ── Delivery info ──
    const infoHtml = `
        <div class="irow">
            <div class="irow-icon" style="background:rgba(250,204,21,.1);"><svg width="14" height="14" fill="none" stroke="#facc15" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></div>
            <div><p class="irow-label">Order Type</p><p class="irow-val">${o.order_type_icon} ${o.order_type_label}</p></div>
        </div>
        ${o.order_type === 'delivery' ? `
        <div class="irow">
            <div class="irow-icon" style="background:rgba(239,68,68,.1);"><svg width="14" height="14" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div><p class="irow-label">Delivery Address</p><p class="irow-val">${escHtml(o.delivery_address)}</p></div>
        </div>
        ` : ''}
        <div class="irow">
            <div class="irow-icon" style="background:rgba(96,165,250,.1);"><svg width="14" height="14" fill="none" stroke="#60a5fa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
            <div><p class="irow-label">Payment</p><p class="irow-val" style="text-transform:capitalize;">${escHtml(o.payment_method)}</p></div>
        </div>
        ${o.rider?`<div class="irow">
            <div class="irow-icon" style="background:rgba(167,139,250,.1);"><svg width="14" height="14" fill="none" stroke="#a78bfa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            <div><p class="irow-label">Rider</p><p class="irow-val">${escHtml(o.rider.name)}</p><p class="irow-sub"><svg width="11" height="11" fill="#facc15" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg> ${o.rider.rating} · ${escHtml(o.rider.phone)}</p></div>
        </div>`:''}`;

    // ── Map (active delivery only) ──
    const mapHtml = (isActive && o.order_type === 'delivery') ? `
        <div class="sheet-divider" style="margin-top:4px;"></div>
        <div style="padding:12px 18px 8px;display:flex;align-items:center;justify-content:space-between;">
            <p style="font-size:13px;font-weight:700;color:#fff;">${isOnWay?'<svg width="14" height="14" fill="none" stroke="#a78bfa" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:4px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3"/></svg> Live Rider Tracking':'<svg width="14" height="14" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:4px"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg> Order Location'}</p>
            <p style="font-size:11px;color:#4b5563;" id="riderEtaText-${o.id}">${isOnWay?'Fetching route…':'Locating…'}</p>
        </div>
        <div id="trackingMap-${o.id}" style="height:220px;width:100%;background:#0a0a14;"></div>` : '';

    // ── Cancel button ──
    const cancelHtml = cancellable ? `
        <div style="padding:14px 18px 10px;">
            <button onclick="openCancelModal(${o.id})" style="width:100%;padding:12px;border-radius:12px;background:rgba(239,68,68,.1);border:1.5px solid rgba(239,68,68,.3);color:#f87171;font-size:14px;font-weight:700;cursor:pointer;">
                Cancel Order
            </button>
        </div>` : '';

    // ── Reorder (past/cancelled) ──
    const reorderHtml = (o.status==='delivered'||o.status==='cancelled') ? `
        <div style="padding:14px 18px 32px;display:flex;justify-content:center;">
            <a href="{{ route('shop.home') }}" class="btn-reorder"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:4px"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Order Again</a>
        </div>` : '<div style="height:32px;"></div>';

    // ── Cancel reason ──
    const cancelReasonHtml = o.cancel_reason ? `
        <div style="margin:0 18px 12px;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.15);border-radius:10px;padding:10px 14px;">
            <p style="font-size:12px;font-weight:600;color:#f87171;margin-bottom:3px;">Cancellation Reason</p>
            <p style="font-size:12px;color:#9ca3af;">${escHtml(o.cancel_reason)}</p>
        </div>` : '';

    return progressHtml + timelineHtml + itemsHtml + totalsHtml + infoHtml + cancelReasonHtml + mapHtml + cancelHtml + reorderHtml;
}

/* ────────────────────────────────
   LOAD ORDERS
──────────────────────────────── */
async function loadAllOrders() {
    try {
        const res = await fetch('/orders', {headers:{'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}});
        const raw = await res.text();
        if(!res.ok){
            document.getElementById('view-all').innerHTML = `<div class="empty-state"><div class="empty-icon"><svg width="40" height="40" fill="none" stroke="#f59e0b" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></div><p class="empty-title">Could not load orders</p><p class="empty-sub">HTTP ${res.status} — please try refreshing.</p></div>`;
            return;
        }
        let data;
        try{data=JSON.parse(raw);}catch(e){
            document.getElementById('view-all').innerHTML = `<div class="empty-state"><div class="empty-icon"><svg width="40" height="40" fill="none" stroke="#f59e0b" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></div><p class="empty-title">Session expired</p><p class="empty-sub">Please log out and log back in.</p></div>`;
            return;
        }
        allOrders = [...(data.active||[]), ...(data.past||[]), ...(data.cancelled||[])];
        // Keep timestamp fields
        allOrders.forEach(o => {
            o.accepted_at  = o.accepted_at  || null;
            o.assigned_at  = o.assigned_at  || null;
            o.picked_up_at = o.picked_up_at || null;
            o.delivered_at = o.delivered_at || null;
            o.cancelled_at = o.cancelled_at || null;
        });
        renderAll();

        if (detailOrderId) {
            const updated = allOrders.find(x => x.id === detailOrderId);
            if (updated) {
                const prevStatus = (activeMaps[detailOrderId] && activeMaps[detailOrderId]._lastStatus) || null;
                const statusChanged = prevStatus !== null && prevStatus !== updated.status;

                if (statusChanged) {
                    // Status changed — rebuild sheet + map so timeline/progress update
                    if (activeMaps[detailOrderId]) {
                        try { activeMaps[detailOrderId].map.remove(); } catch(e) {}
                        delete activeMaps[detailOrderId];
                    }
                    document.getElementById('detailBody').innerHTML = buildDetailBody(updated);
                    if (!['delivered','cancelled'].includes(updated.status)) {
                        setTimeout(() => { if(typeof initOrderMap==='function') initOrderMap(updated); }, 300);
                    }
                } else {
                    // Status unchanged — only move the rider marker, no rebuild
                    if (updated.rider && updated.rider.lat && updated.rider.lng) {
                        updateMapRiderPos(updated.id, updated.rider.lat, updated.rider.lng);
                    }
                    // Update timeline timestamps silently (no map impact)
                }

                // Track current status on the map state
                if (activeMaps[detailOrderId]) {
                    activeMaps[detailOrderId]._lastStatus = updated.status;
                }
            }
        }

        // Also update any maps not currently in the open sheet
        (data.active||[]).forEach(o => {
            if (o.id !== detailOrderId && o.rider && o.rider.lat && o.rider.lng && activeMaps[o.id]) {
                updateMapRiderPos(o.id, o.rider.lat, o.rider.lng);
            }
        });
    } catch(e) {
        console.error(e);
        document.getElementById('view-all').innerHTML = `<div class="empty-state"><div class="empty-icon"><svg width="40" height="40" fill="none" stroke="#f59e0b" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg></div><p class="empty-title">Network error</p><p class="empty-sub">Check your connection and try refreshing.</p></div>`;
    }
}

/* ────────────────────────────────
   CANCEL
──────────────────────────────── */
let currentCancelOrderId = null;
function openCancelModal(id){currentCancelOrderId=id;document.getElementById('cancelModalBackdrop').style.display='block';document.getElementById('cancelModal').style.display='block';document.getElementById('cancelModalError').style.display='none';document.querySelectorAll('input[name="cancelReason"]').forEach(r=>r.checked=false);document.querySelectorAll('#cancelReasons label').forEach(l=>l.style.borderColor='rgba(255,255,255,.07)');}
function closeCancelModal(){document.getElementById('cancelModalBackdrop').style.display='none';document.getElementById('cancelModal').style.display='none';currentCancelOrderId=null;}
async function submitCancel(){
    const sel=document.querySelector('input[name="cancelReason"]:checked');
    const errEl=document.getElementById('cancelModalError');
    const btn=document.getElementById('confirmCancelBtn');
    if(!sel){errEl.textContent='<svg width="40" height="40" fill="none" stroke="#f59e0b" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> Please select a reason.';errEl.style.display='block';return;}
    errEl.style.display='none';btn.textContent='Cancelling…';btn.disabled=true;
    try{
        const r=await fetch(`/orders/${currentCancelOrderId}/cancel`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({reason:sel.value})});
        const d=await r.json();
        if(d.success){closeCancelModal();closeDetail();await loadAllOrders();showToast('Order cancelled.');}
        else{errEl.textContent=d.message||'Failed.';errEl.style.display='block';}
    }catch(e){errEl.textContent='Network error.';errEl.style.display='block';}
    btn.textContent='Yes, Cancel My Order';btn.disabled=false;
}
function showToast(msg){const t=document.createElement('div');t.textContent=msg;Object.assign(t.style,{position:'fixed',bottom:'90px',left:'50%',transform:'translateX(-50%)',background:'#0d1f17',border:'1px solid rgba(74,222,128,.3)',color:'#4ade80',padding:'12px 22px',borderRadius:'99px',fontSize:'13px',fontWeight:'700',zIndex:'9999'});document.body.appendChild(t);setTimeout(()=>t.remove(),2500);}
async function updateMapRiderPos(orderId,lat,lng){
    // Delegated to the full implementation below — this stub kept for safety
    const s=activeMaps[orderId];
    if(!s)return;
}

/* ── Init ── */
let pollTimer = null;
function startPolling() {
    if (pollTimer) return;
    pollTimer = setInterval(loadAllOrders, 15000);
}
function stopPolling() {
    if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
}
document.addEventListener('DOMContentLoaded',()=>{
    applyTheme(localStorage.getItem('eutTheme')||'dark');
    document.getElementById('shopThemeToggle').addEventListener('click',()=>{
        const t=(localStorage.getItem('eutTheme')||'dark')==='dark'?'light':'dark';
        localStorage.setItem('eutTheme',t);applyTheme(t);
    });

    // Initial load
    loadAllOrders();

    // Echo: listen for real-time order updates on the customer's private channel
    if (window.Echo) {
        window.Echo.private('orders.{{ auth()->id() }}')
            .listen('.order.updated', (order) => {
                // Update or insert the order in allOrders
                const idx = allOrders.findIndex(o => o.id === order.id);
                if (idx !== -1) {
                    allOrders[idx] = order;
                } else {
                    allOrders.unshift(order);
                }

                renderAll();

                // If detail sheet is open for this order, refresh it
                if (detailOrderId === order.id) {
                    const detailBody = document.getElementById('detailBody');
                    if (detailBody) {
                        detailBody.innerHTML = buildDetailBody(order);
                        if (!['delivered','cancelled'].includes(order.status)) {
                            setTimeout(() => { if(typeof initOrderMap==='function') initOrderMap(order); }, 300);
                        }
                    }
                }
            });
    } else {
        // Fallback: poll every 15s if Echo isn't available — pause when tab is hidden
        startPolling();
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopPolling();
            } else {
                loadAllOrders();
                startPolling();
            }
        });
    }
});
</script>

<script>
/* ── Map (Leaflet + OSRM) — only initialised inside the detail sheet ── */
const RESTAURANT_POS = [13.3213129, 121.3027265]; // EUT Snack House — verified Google Maps coordinates

async function fetchOSRMRoute(from, to) {
    const url = 'https://router.project-osrm.org/route/v1/driving/'
        + from[1] + ',' + from[0] + ';' + to[1] + ',' + to[0]
        + '?overview=full&geometries=geojson';
    try {
        const r = await fetch(url);
        const d = await r.json();
        if (d.code === 'Ok' && d.routes.length)
            return d.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
    } catch(e) { console.warn('OSRM', e); }
    return null;
}

/* ── Geocode address text → [lat, lng] with multi-attempt fallback ── */
async function geocodeDeliveryAddr(rawAddr) {
    if (!rawAddr) return null;
    // Strip leading "Name, " prefix if first segment has no digits
    let addr = rawAddr;
    const parts = rawAddr.split(',');
    if (parts.length > 1 && !/\d/.test(parts[0])) addr = parts.slice(1).join(',').trim();

    const attempts = [
        addr + ', Naujan, Oriental Mindoro, Philippines',
        addr + ', Oriental Mindoro, Philippines',
        addr + ', Philippines',
        parts[parts.length - 1].trim() + ', Naujan, Oriental Mindoro, Philippines',
    ];
    for (const q of attempts) {
        try {
            const res  = await fetch(
                'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=1&countrycodes=ph',
                { headers: { 'Accept-Language': 'en', 'User-Agent': 'EUT-Delivery-App/1.0' } }
            );
            const data = await res.json();
            if (data && data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                if (lat > 4 && lat < 22 && lng > 116 && lng < 127) return [lat, lng];
            }
        } catch(e) { /* try next */ }
        await new Promise(r => setTimeout(r, 400));
    }
    return [13.3213129, 121.3027265]; // EUT Snack House fallback
}

async function initOrderMap(order) {
    const el = document.getElementById('trackingMap-' + order.id);
    if (!el || activeMaps[order.id]) return;   // never rebuild an existing map

    const isOnWay  = ['rider_assigned', 'out_for_delivery'].includes(order.status);
    const isOut    = order.status === 'out_for_delivery';
    const etaEl    = document.getElementById('riderEtaText-' + order.id);
    const hasRider = order.rider && order.rider.lat && order.rider.lng;
    const riderPos = hasRider
        ? [parseFloat(order.rider.lat),  parseFloat(order.rider.lng)]
        : [RESTAURANT_POS[0] + 0.002,    RESTAURANT_POS[1] + 0.002];

    // Use saved delivery coords if available, otherwise geocode the address text
    let customerPos = (order.delivery_lat && order.delivery_lng)
        ? [parseFloat(order.delivery_lat), parseFloat(order.delivery_lng)]
        : null;

    const map = L.map(el, { zoomControl: true, attributionControl: false });
    activeMaps[order.id] = { map, riderMarker: null, routeLine: null, roadPoints: [], simStep: 0, _lastStatus: order.status, destLatLng: null, _lastReroute: 0 };

    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20 }).addTo(map);
    L.tileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', { maxZoom: 20, opacity: 0.85 }).addTo(map);

    // Restaurant pin
    L.marker(RESTAURANT_POS, { icon: L.divIcon({
        html: `<div style="background:#facc15;width:38px;height:38px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #d97706;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);"><span style="transform:rotate(45deg);font-size:18px;line-height:1;">🍽️</span></div>`,
        className: '', iconSize: [38, 38], iconAnchor: [19, 38],
    }) }).addTo(map).bindPopup('<b>E.U.T Snack House</b>');

    // Geocode fallback if coords missing
    if (!customerPos && order.delivery_address) {
        if (etaEl) etaEl.textContent = 'Locating address…';
        customerPos = await geocodeDeliveryAddr(order.delivery_address);
        // Save back so next time is instant
        if (customerPos) {
            fetch('/orders/' + order.id + '/set-coords', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ lat: customerPos[0], lng: customerPos[1] })
            }).catch(() => {});
        }
    }

    // Customer / delivery destination pin
    if (customerPos) {
        L.marker(customerPos, { icon: L.divIcon({
            html: `<div style="background:#ef4444;width:38px;height:38px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #b91c1c;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);"><span style="transform:rotate(45deg);font-size:18px;line-height:1;">🏠</span></div>`,
            className: '', iconSize: [38, 38], iconAnchor: [19, 38],
        }) }).addTo(map).bindPopup('<b>Your Delivery Location</b>');
    }

    if (!isOnWay) {
        const bounds = customerPos
            ? [RESTAURANT_POS, customerPos]
            : [RESTAURANT_POS, [RESTAURANT_POS[0] + 0.01, RESTAURANT_POS[1] + 0.01]];
        map.fitBounds(bounds, { padding: [50, 50] });
        if (etaEl) etaEl.textContent = customerPos ? 'Waiting for pickup' : 'Preparing your order';
        return;
    }

    const dest = customerPos || [RESTAURANT_POS[0] + 0.005, RESTAURANT_POS[1] + 0.006];
    // Store destination on map state for re-routing
    activeMaps[order.id].destLatLng = dest;

    // Rider marker — animated pulse circle with motorbike icon
    const rM = L.marker(riderPos, { icon: L.divIcon({
        html: `<div style="background:#10b981;width:42px;height:42px;border-radius:50%;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 0 10px rgba(16,185,129,.8);">🏍️</div>`,
        className: '', iconSize: [42, 42], iconAnchor: [21, 21],
    }) }).addTo(map);
    if (order.rider) rM.bindPopup('<b>' + order.rider.name + '</b><br><svg width="11" height="11" fill="#facc15" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg> ' + order.rider.rating);
    activeMaps[order.id].riderMarker = rM;

    if (isOut) {
        map.fitBounds([riderPos, dest], { padding: [40, 40] });
        const route = await fetchOSRMRoute(riderPos, dest);
        if (route) {
            activeMaps[order.id].roadPoints = route;
            const ln = L.polyline(route, { color: '#facc15', weight: 5, opacity: 1 }).addTo(map);
            activeMaps[order.id].routeLine = ln;
            map.fitBounds(ln.getBounds(), { padding: [40, 40] });
            if (etaEl) etaEl.textContent = '~' + Math.max(1, Math.round(route.length / 30)) + ' min away';
        } else {
            activeMaps[order.id].routeLine = L.polyline([riderPos, dest], { color: '#facc15', weight: 3, opacity: 0.7, dashArray: '8 6' }).addTo(map);
            if (etaEl) etaEl.textContent = 'On the way';
        }
    } else {
        map.fitBounds([RESTAURANT_POS, dest, riderPos], { padding: [40, 40] });
        const route = await fetchOSRMRoute(RESTAURANT_POS, dest);
        if (route) {
            activeMaps[order.id].roadPoints = route;
            const ln = L.polyline(route, { color: '#facc15', weight: 5, opacity: 0.7, dashArray: '8 6' }).addTo(map);
            activeMaps[order.id].routeLine = ln;
            map.fitBounds(ln.getBounds(), { padding: [40, 40] });
        } else {
            activeMaps[order.id].routeLine = L.polyline([RESTAURANT_POS, dest], { color: '#facc15', weight: 3, opacity: 0.6, dashArray: '8 6' }).addTo(map);
        }
        if (etaEl) etaEl.textContent = 'Rider heading to pickup';
    }

    if (!hasRider) simulateMapRider(order.id, dest);
}

/* Called on every poll — moves the marker, trims the route, re-routes if rider is off-route */
const REROUTE_THRESHOLD_DEG = 0.0015; // ~150m — if rider is further than this from the route, re-route
const REROUTE_COOLDOWN_MS   = 20000;  // re-fetch OSRM at most every 20s

async function updateMapRiderPos(orderId, lat, lng) {
    const s = activeMaps[orderId];
    if (!s || !s.riderMarker) return;

    const newPos = [parseFloat(lat), parseFloat(lng)];
    s.riderMarker.setLatLng(newPos);

    if (!s.routeLine) return;

    const etaEl = document.getElementById('riderEtaText-' + orderId);

    if (s.roadPoints && s.roadPoints.length > 1) {
        // Find closest point on stored road route
        let closest = 0, minDist = Infinity;
        s.roadPoints.forEach((pt, i) => {
            const d = (pt[0] - newPos[0]) ** 2 + (pt[1] - newPos[1]) ** 2;
            if (d < minDist) { minDist = d; closest = i; }
        });

        const distFromRoute = Math.sqrt(minDist);
        const now = Date.now();
        const canReroute = (now - (s._lastReroute || 0)) > REROUTE_COOLDOWN_MS;

        if (distFromRoute > REROUTE_THRESHOLD_DEG && canReroute && s.destLatLng) {
            // Rider is off-route — fetch a fresh OSRM route from current position
            s._lastReroute = now;
            const fresh = await fetchOSRMRoute(newPos, s.destLatLng);
            if (fresh && fresh.length > 1) {
                s.roadPoints = fresh;
                s.routeLine.setLatLngs(fresh);
                if (etaEl) etaEl.textContent = '~' + Math.max(1, Math.round(fresh.length / 30)) + ' min away';
            } else {
                // OSRM failed — fall back to straight line
                s.routeLine.setLatLngs([newPos, s.destLatLng]);
                if (etaEl) etaEl.textContent = 'On the way';
            }
        } else {
            // On-route — trim the polyline to start from the rider's current position
            s.routeLine.setLatLngs(s.roadPoints.slice(closest));
            const remaining = s.roadPoints.length - closest;
            if (etaEl) {
                const mins = Math.max(0, Math.round(30 * remaining / s.roadPoints.length));
                etaEl.textContent = mins > 0 ? '~' + mins + ' min away' : 'Arriving now!';
            }
        }
    } else if (s.destLatLng) {
        // No road points yet — just draw straight line
        s.routeLine.setLatLngs([newPos, s.destLatLng]);
    }
}

function simulateMapRider(orderId, dest) {
    const s = activeMaps[orderId];
    if (!s) return;
    const etaEl = document.getElementById('riderEtaText-' + orderId);
    if (s.roadPoints && s.roadPoints.length) {
        const total = s.roadPoints.length;
        const iv = setInterval(() => {
            if (!activeMaps[orderId]) { clearInterval(iv); return; }
            s.simStep = Math.min(s.simStep + 1, total - 1);
            const pos = s.roadPoints[s.simStep];
            if (s.riderMarker) s.riderMarker.setLatLng(pos);
            if (s.routeLine)   s.routeLine.setLatLngs(s.roadPoints.slice(s.simStep));
            const m = Math.max(0, Math.round(30 * (1 - s.simStep / total)));
            if (etaEl) etaEl.textContent = m > 0 ? '~' + m + ' min away' : 'Arriving now!';
            if (s.simStep >= total - 1) clearInterval(iv);
        }, 2500);
    } else {
        let step = 0, tot = 60;
        let pos = s.riderMarker ? s.riderMarker.getLatLng() : { lat: RESTAURANT_POS[0], lng: RESTAURANT_POS[1] };
        const dLat = (dest[0] - pos.lat) / tot, dLng = (dest[1] - pos.lng) / tot;
        const iv = setInterval(() => {
            if (!activeMaps[orderId]) { clearInterval(iv); return; }
            if (step >= tot) { clearInterval(iv); return; }
            step++;
            pos = { lat: pos.lat + dLat, lng: pos.lng + dLng };
            if (s.riderMarker) s.riderMarker.setLatLng(pos);
            if (s.routeLine)   s.routeLine.setLatLngs([[pos.lat, pos.lng], dest]);
            const m = Math.max(0, Math.round(30 * (1 - step / tot)));
            if (etaEl) etaEl.textContent = m > 0 ? '~' + m + ' min away' : 'Arriving now!';
        }, 3000);
    }
}
</script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>
@include('partials.pwa-register')
</body>
</html>
