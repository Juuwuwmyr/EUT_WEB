<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - EUT Restaurant</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
body{background:#080810;color:#fff;min-height:100vh;}
.topnav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(8,8,16,.94);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.06);}
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
.bottom-nav{position:fixed;bottom:0;left:0;right:0;background:rgba(8,8,16,.97);border-top:1px solid rgba(255,255,255,.07);backdrop-filter:blur(20px);padding:10px 0 14px;z-index:100;}
@media(min-width:1024px){.bottom-nav{display:none;}}
.bottom-nav-inner{display:flex;}
.bnav-item{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:#4b5563;text-decoration:none;font-size:10px;font-weight:500;transition:color .15s;}
.bnav-item.active{color:#facc15;}

/* light mode */
.light-mode body{background:#f0f0f8!important;}
.light-mode .topnav{background:rgba(255,255,255,.96)!important;border-color:rgba(0,0,0,.07)!important;}
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
    <div id="view-all">
        <div class="empty-state"><div class="empty-icon">⏳</div><p class="empty-title">Loading…</p></div>
    </div>
    <div id="view-past"      style="display:none;"></div>
    <div id="view-cancelled" style="display:none;"></div>
</div>

<!-- ORDER DETAIL SHEET -->
<div class="sheet-backdrop" id="detailBackdrop" onclick="closeDetail()"></div>
<div class="sheet" id="detailSheet">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
        <span class="sheet-head-title" id="detailOrderNum">#EUT-00000</span>
        <button class="sheet-close" onclick="closeDetail()">✕</button>
    </div>
    <div class="sheet-divider"></div>
    <div id="detailBody"></div>
</div>

<!-- CANCEL MODAL -->
<div id="cancelModalBackdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;backdrop-filter:blur(4px);" onclick="closeCancelModal()"></div>
<div id="cancelModal" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:1001;background:linear-gradient(145deg,#1a0a0a,#120808);border:1px solid rgba(239,68,68,.25);border-radius:24px 24px 0 0;padding:24px 20px 40px;max-width:540px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div><p style="font-size:17px;font-weight:800;color:#fff;">Cancel Order</p><p style="font-size:12px;color:#6b7280;margin-top:2px;">Tell us why you're cancelling</p></div>
        <button onclick="closeCancelModal()" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);color:#9ca3af;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;">✕</button>
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
    const tc={flavor:{bg:'rgba(59,130,246,.12)',c:'#3b82f6',i:'🌶'},modifier:{bg:'rgba(139,92,246,.12)',c:'#8b5cf6',i:'⚙'},addon:{bg:'rgba(245,158,11,.12)',c:'#d97706',i:'➕'}};
    const tags=(modifiers||[]).filter(m=>m&&m.name&&!/^no\s/i.test(m.name)).map(m=>{
        const s=tc[m.type]||tc.modifier;const adj=parseFloat(m.price_adjustment||0);
        const ex=(m.price_type==='add'&&adj>0)?` <span style="color:#4ade80;font-size:.6rem;">+₱${adj.toLocaleString()}</span>`:'';
        return`<span style="display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .55rem;border-radius:99px;font-size:.68rem;font-weight:600;background:${s.bg};color:${s.c};border:1px solid ${s.c}30;white-space:nowrap;">${s.i} ${escHtml(m.name)}${ex}</span>`;
    });
    return tags.length?`<div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">${tags.join('')}</div>`:'';
}

const STATUS_CFG = {
    pending:          {label:'Order Placed',   badge:'badge-pending', icon:'🕐', progress:10},
    accepted:         {label:'Accepted',        badge:'badge-active',  icon:'✅', progress:25},
    preparing:        {label:'Preparing',       badge:'badge-active',  icon:'👨‍🍳', progress:45},
    rider_assigned:   {label:'Rider Assigned',  badge:'badge-active',  icon:'🛵', progress:65},
    out_for_delivery: {label:'Out for Delivery',badge:'badge-active',  icon:'🚀', progress:82},
    delivered:        {label:'Delivered',       badge:'badge-done',    icon:'✔',  progress:100},
    cancelled:        {label:'Cancelled',       badge:'badge-cancelled',icon:'✕', progress:0},
};
const TIMELINE_STEPS = ['pending','accepted','preparing','rider_assigned','out_for_delivery','delivered'];

/* ────────────────────────────────
   COMPACT ORDER CARD
──────────────────────────────── */
function buildOrderCard(o) {
    const cfg = STATUS_CFG[o.status] || STATUS_CFG.pending;
    const imgs = o.items.slice(0,3).map(i=>
        `<img src="${escHtml(i.image)}" class="ocard-img" alt="${escHtml(i.name)}" onerror="this.src='{{ asset('images/hero-burger.jpg') }}'">`)
        .join('');
    const extra = o.items.length > 3 ? `<div class="ocard-more">+${o.items.length-3}</div>` : '';
    const isLive = !['delivered','cancelled'].includes(o.status);
    return `
    <div class="ocard" onclick="openDetail(${o.id})">
        <div class="ocard-top">
            <div>
                <p class="ocard-num">#${escHtml(o.order_number)}</p>
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

    // Tab dot on All tab — blink when there are active orders
    document.getElementById('allDot').style.display = active.length ? 'inline-block' : 'none';

    // All tab — shows active/in-progress orders only
    document.getElementById('view-all').innerHTML = active.length
        ? active.map(o=>buildOrderCard(o)).join('')
        : emptyState('⏳','No active orders','Place an order and track it here.',true);

    // Past tab
    document.getElementById('view-past').innerHTML = past.length
        ? past.map(o=>buildOrderCard(o)).join('')
        : emptyState('📦','No completed orders','Your delivered orders will show here.',false);

    // Cancelled tab
    document.getElementById('view-cancelled').innerHTML = cancelled.length
        ? cancelled.map(o=>buildOrderCard(o)).join('')
        : emptyState('✅','No cancelled orders','Great — you haven&#39;t cancelled anything.',false);
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
        setTimeout(()=>initOrderMap(o), 200);
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
                <span class="step-label ${isOnWay?'now':isDelivered?'done':''}">On the way</span>
                <span class="step-label ${isDelivered?'done':''}">Delivered</span>
            </div>
        </div>
        <div class="sheet-divider" style="margin-top:14px;"></div>` : '';

    // ── Timeline ──
    const stepsArr = o.status === 'cancelled'
        ? [{key:'placed',label:'Order Placed',time:o.placed_at,done:true},{key:'cancelled',label:'Cancelled',time:o.cancelled_at||'',done:true,is_cancel:true}]
        : TIMELINE_STEPS.map((s,i) => {
            const currentIdx = TIMELINE_STEPS.indexOf(o.status);
            const isDone = i < currentIdx || o.status === 'delivered';
            const isNow  = s === o.status;
            const timeMap = {pending:o.placed_at,accepted:o.accepted_at,preparing:o.accepted_at,rider_assigned:o.assigned_at,out_for_delivery:o.picked_up_at,delivered:o.delivered_at};
            return {key:s,label:STATUS_CFG[s]?.label||s,time:timeMap[s]||'',done:isDone,isNow,future:!isDone&&!isNow};
        });

    const timelineHtml = `
        <div class="sheet-section"><p class="sheet-section-title">Order Timeline</p></div>
        <div class="timeline">
            ${stepsArr.map(step=>`
            <div class="tl-step ${step.done?'tl-done':step.isNow?'tl-now':'tl-future'}">
                <div class="tl-dot">${step.done?'✓':step.isNow?'●':'○'}</div>
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
                <img src="${escHtml(i.image)}" class="sitem-img" alt="${escHtml(i.name)}" onerror="this.src='{{ asset('images/hero-burger.jpg') }}'">
                <div style="flex:1;min-width:0;">
                    <p class="sitem-name">${escHtml(i.name)}</p>
                    <p class="sitem-meta">× ${i.qty}</p>
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
            <div class="irow-icon" style="background:rgba(239,68,68,.1);"><svg width="14" height="14" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            <div><p class="irow-label">Address</p><p class="irow-val">${escHtml(o.delivery_address)}</p></div>
        </div>
        <div class="irow">
            <div class="irow-icon" style="background:rgba(96,165,250,.1);"><svg width="14" height="14" fill="none" stroke="#60a5fa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
            <div><p class="irow-label">Payment</p><p class="irow-val" style="text-transform:capitalize;">${escHtml(o.payment_method)}</p></div>
        </div>
        ${o.rider?`<div class="irow">
            <div class="irow-icon" style="background:rgba(167,139,250,.1);"><svg width="14" height="14" fill="none" stroke="#a78bfa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
            <div><p class="irow-label">Rider</p><p class="irow-val">${escHtml(o.rider.name)}</p><p class="irow-sub">⭐ ${o.rider.rating} · ${escHtml(o.rider.phone)}</p></div>
        </div>`:''}`;

    // ── Map (active only) ──
    const mapHtml = isActive ? `
        <div class="sheet-divider" style="margin-top:4px;"></div>
        <div style="padding:12px 18px 8px;display:flex;align-items:center;justify-content:space-between;">
            <p style="font-size:13px;font-weight:700;color:#fff;">${isOnWay?'🛵 Live Rider Tracking':'📍 Order Location'}</p>
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
            <a href="{{ route('shop.home') }}" class="btn-reorder">🔁 Order Again</a>
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
            document.getElementById('view-all').innerHTML = `<div class="empty-state"><div class="empty-icon">⚠️</div><p class="empty-title">Could not load orders</p><p class="empty-sub">HTTP ${res.status} — please try refreshing.</p></div>`;
            return;
        }
        let data;
        try{data=JSON.parse(raw);}catch(e){
            document.getElementById('view-all').innerHTML = `<div class="empty-state"><div class="empty-icon">⚠️</div><p class="empty-title">Session expired</p><p class="empty-sub">Please log out and log back in.</p></div>`;
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
                        setTimeout(() => initOrderMap(updated), 200);
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
        document.getElementById('view-all').innerHTML = `<div class="empty-state"><div class="empty-icon">⚠️</div><p class="empty-title">Network error</p><p class="empty-sub">Check your connection and try refreshing.</p></div>`;
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
    if(!sel){errEl.textContent='⚠ Please select a reason.';errEl.style.display='block';return;}
    errEl.style.display='none';btn.textContent='Cancelling…';btn.disabled=true;
    try{
        const r=await fetch(`/orders/${currentCancelOrderId}/cancel`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({reason:sel.value})});
        const d=await r.json();
        if(d.success){closeCancelModal();closeDetail();await loadAllOrders();showToast('✅ Order cancelled.');}
        else{errEl.textContent=d.message||'Failed.';errEl.style.display='block';}
    }catch(e){errEl.textContent='Network error.';errEl.style.display='block';}
    btn.textContent='Yes, Cancel My Order';btn.disabled=false;
}
function showToast(msg){const t=document.createElement('div');t.textContent=msg;Object.assign(t.style,{position:'fixed',bottom:'90px',left:'50%',transform:'translateX(-50%)',background:'#0d1f17',border:'1px solid rgba(74,222,128,.3)',color:'#4ade80',padding:'12px 22px',borderRadius:'99px',fontSize:'13px',fontWeight:'700',zIndex:'9999'});document.body.appendChild(t);setTimeout(()=>t.remove(),2500);}
async function updateMapRiderPos(orderId,lat,lng){
    const s=activeMaps[orderId];
    if(!s)return;
    const newPos=[lat,lng];
    if(s.riderMarker) s.riderMarker.setLatLng(newPos);
    // Refresh route polyline from new rider pos to customer
    if(s.routeLine && s.map) {
        // Find customer position by looking at the end of the current route
        const lls = s.routeLine.getLatLngs();
        if(lls && lls.length > 0) {
            const dest = lls[lls.length-1];
            fetchOSRMRoute(newPos, [dest.lat, dest.lng]).then(function(route){
                if(route && s.routeLine) s.routeLine.setLatLngs(route);
                else if(s.routeLine) s.routeLine.setLatLngs([newPos, [dest.lat, dest.lng]]);
            });
        }
    }
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded',()=>{
    applyTheme(localStorage.getItem('eutTheme')||'dark');
    document.getElementById('shopThemeToggle').addEventListener('click',()=>{
        const t=(localStorage.getItem('eutTheme')||'dark')==='dark'?'light':'dark';
        localStorage.setItem('eutTheme',t);applyTheme(t);
    });
    loadAllOrders();
    setInterval(loadAllOrders,6000);
});
</script>

<script>
/* ── Map (Leaflet + OSRM) — only initialised inside the detail sheet ── */
const RESTAURANT_POS = [13.3213129, 121.3027265];

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
    return [13.3100, 121.2950]; // Naujan area fallback
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
    activeMaps[order.id] = { map, riderMarker: null, routeLine: null, roadPoints: [], simStep: 0, _lastStatus: order.status };

    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20 }).addTo(map);
    L.tileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', { maxZoom: 20, opacity: 0.85 }).addTo(map);

    // Restaurant pin
    L.marker(RESTAURANT_POS, { icon: L.divIcon({
        html: '<div style="background:#facc15;width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #d97706;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.5);"><span style="transform:rotate(45deg);font-size:15px;">🍔</span></div>',
        className: '', iconSize: [36, 36], iconAnchor: [18, 36],
    }) }).addTo(map).bindPopup('<b>EUT Restaurant</b>');

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
            html: '<div style="background:#ef4444;width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #b91c1c;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.5);"><span style="transform:rotate(45deg);font-size:15px;">🏠</span></div>',
            className: '', iconSize: [36, 36], iconAnchor: [18, 36],
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

    // Rider marker
    const rM = L.marker(riderPos, { icon: L.divIcon({
        html: '<div style="background:#10b981;width:44px;height:44px;border-radius:50%;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:22px;box-shadow:0 0 14px rgba(16,185,129,.7);">🛵</div>',
        className: '', iconSize: [44, 44], iconAnchor: [22, 22],
    }) }).addTo(map);
    if (order.rider) rM.bindPopup('<b>' + order.rider.name + '</b><br>⭐ ' + order.rider.rating);
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
        } else {
            activeMaps[order.id].routeLine = L.polyline([RESTAURANT_POS, dest], { color: '#facc15', weight: 3, opacity: 0.6, dashArray: '8 6' }).addTo(map);
        }
        if (etaEl) etaEl.textContent = 'Rider heading to pickup';
    }

    if (!hasRider) simulateMapRider(order.id, dest);
}

/* Called on every poll — only moves the marker and trims the route, never rebuilds the map */
function updateMapRiderPos(orderId, lat, lng) {
    const s = activeMaps[orderId];
    if (!s || !s.riderMarker) return;
    const newPos = [parseFloat(lat), parseFloat(lng)];
    s.riderMarker.setLatLng(newPos);

    if (s.routeLine && s.roadPoints && s.roadPoints.length) {
        // Trim to closest point on stored route from new rider position
        let closest = 0, minDist = Infinity;
        s.roadPoints.forEach((pt, i) => {
            const d = (pt[0] - newPos[0]) * (pt[0] - newPos[0]) + (pt[1] - newPos[1]) * (pt[1] - newPos[1]);
            if (d < minDist) { minDist = d; closest = i; }
        });
        s.routeLine.setLatLngs(s.roadPoints.slice(closest));
        const remaining = s.roadPoints.length - closest;
        const etaEl = document.getElementById('riderEtaText-' + orderId);
        if (etaEl) {
            const mins = Math.max(0, Math.round(30 * remaining / s.roadPoints.length));
            etaEl.textContent = mins > 0 ? '~' + mins + ' min away' : 'Arriving now!';
        }
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
