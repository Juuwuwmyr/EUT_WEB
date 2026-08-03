<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E.U.T Snack House</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
        body{background:#080810;color:#fff;min-height:100vh;}
        .topnav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(8,8,16,.98);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border-bottom:1px solid rgba(255,255,255,.06);will-change:transform;transform:translate3d(0,0,0);}
        .topnav-inner{max-width:760px;margin:0 auto;padding:13px 16px;display:flex;align-items:center;gap:10px;}
        .back-btn{width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:#9ca3af;text-decoration:none;transition:all .2s;flex-shrink:0;}
        .back-btn:hover{background:rgba(255,255,255,.12);color:#fff;}
        .topnav-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;flex:1;}
        .theme-btn{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;flex-shrink:0;}
        .page-body{max-width:760px;margin:0 auto;padding:78px 16px 120px;}
        .checkout-grid{display:grid;grid-template-columns:1fr;gap:14px;}
        @media(min-width:660px){.checkout-grid{grid-template-columns:1fr 320px;align-items:start;}}
        .card{background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:20px;overflow:hidden;margin-bottom:14px;box-shadow:0 4px 24px rgba(0,0,0,.4);}
        .card:last-child{margin-bottom:0;}
        .card-header{padding:15px 18px;border-bottom:1px solid rgba(255,255,255,.05);display:flex;align-items:center;gap:10px;}
        .card-icon{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .card-title{font-size:14px;font-weight:700;color:#fff;flex:1;}
        .card-body{padding:16px 18px;}

        /* ── ADDRESS SELECTOR ── */
        .addr-selected{display:flex;align-items:flex-start;gap:12px;padding:16px 18px;cursor:pointer;transition:background .15s;}
        .addr-selected:hover{background:rgba(255,255,255,.02);}
        .addr-pin{width:36px;height:36px;border-radius:50%;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;}
        .addr-info{flex:1;min-width:0;}
        .addr-name-row{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px;}
        .addr-recipient{font-size:13px;font-weight:700;color:#fff;}
        .addr-phone{font-size:12px;color:#6b7280;}
        .addr-label-badge{font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;background:rgba(250,204,21,.1);color:#facc15;border:1px solid rgba(250,204,21,.2);}
        .addr-label-badge.default{background:rgba(34,197,94,.1);color:#4ade80;border-color:rgba(34,197,94,.2);}
        .addr-text{font-size:12px;color:#9ca3af;line-height:1.5;}
        .addr-chevron{color:#4b5563;flex-shrink:0;margin-top:6px;}
        .addr-empty{padding:20px 18px;text-align:center;}
        .addr-empty-txt{font-size:13px;color:#4b5563;margin-bottom:12px;}
        .btn-add-addr{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;background:rgba(250,204,21,.1);border:1.5px dashed rgba(250,204,21,.3);color:#facc15;font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;}
        .btn-add-addr:hover{background:rgba(250,204,21,.18);}

        /* ── PAYMENT ── */
        .pay-option{display:flex;align-items:center;gap:14px;padding:13px 14px;border:1.5px solid rgba(255,255,255,.07);border-radius:14px;cursor:pointer;transition:all .2s;margin-bottom:8px;}
        .pay-option:last-child{margin-bottom:0;}
        .pay-option:hover{border-color:rgba(250,204,21,.3);}
        .pay-option.selected{border-color:#facc15;background:rgba(250,204,21,.06);}
        .pay-option input[type=radio]{accent-color:#facc15;width:16px;height:16px;flex-shrink:0;}
        .pay-emoji{font-size:22px;line-height:1;flex-shrink:0;}
        .pay-label{font-size:13px;font-weight:600;color:#fff;}
        .pay-sub{font-size:11px;color:#4b5563;margin-top:1px;}

        /* ── ORDER SUMMARY ── */
        .summary-sticky{position:sticky;top:78px;}
        .items-scroll{max-height:240px;overflow-y:auto;padding:14px 18px 0;}
        .items-scroll::-webkit-scrollbar{width:3px;}
        .items-scroll::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:99px;}
        .sum-item{display:flex;align-items:flex-start;gap:10px;padding-bottom:12px;margin-bottom:12px;border-bottom:1px solid rgba(255,255,255,.04);}
        .sum-item:last-child{border-bottom:none;margin-bottom:0;}
        .sum-img{width:46px;height:46px;border-radius:10px;object-fit:cover;flex-shrink:0;}
        .sum-name{font-size:12px;font-weight:600;color:#e5e7eb;line-height:1.35;}
        .sum-meta{font-size:11px;color:#4b5563;margin-top:2px;}
        .sum-price{font-size:13px;font-weight:700;color:#facc15;flex-shrink:0;margin-left:auto;padding-left:6px;}
        .tot-row{display:flex;justify-content:space-between;align-items:center;padding:9px 18px;}
        .tot-label{font-size:12px;color:#6b7280;}
        .tot-val{font-size:12px;color:#9ca3af;font-weight:500;}
        .tot-grand-label{font-size:14px;font-weight:700;color:#fff;}
        .tot-grand-val{font-size:20px;font-weight:800;color:#facc15;}
        .free-bar-wrap{margin:0 18px 12px;background:rgba(34,197,94,.06);border:1px solid rgba(34,197,94,.15);border-radius:10px;padding:9px 12px;display:flex;align-items:center;gap:10px;}
        .free-bar-track{height:4px;border-radius:99px;background:#1a1b2e;overflow:hidden;flex:1;}
        .free-bar-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#16a34a,#4ade80);transition:width .6s ease;}
        .place-btn{display:block;margin:4px 18px 18px;padding:15px;border-radius:14px;background:linear-gradient(135deg,#f59e0b,#facc15);border:none;color:#000;font-size:15px;font-weight:800;cursor:pointer;transition:all .2s;box-shadow:0 4px 18px rgba(250,204,21,.3);text-align:center;width:calc(100% - 36px);}
        .place-btn:hover{transform:translateY(-1px);box-shadow:0 6px 24px rgba(250,204,21,.45);}
        .place-btn:disabled{opacity:.6;cursor:not-allowed;transform:none;}
        .guest-notice{margin:4px 18px 18px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:12px;padding:12px 14px;font-size:12px;color:#f87171;text-align:center;}
        .guest-notice a{color:#facc15;font-weight:700;}
        .notes-input{width:100%;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.08);border-radius:12px;padding:10px 14px;font-size:13px;color:#fff;outline:none;transition:border-color .2s;resize:none;}
        .notes-input::placeholder{color:#374151;}
        .notes-input:focus{border-color:rgba(250,204,21,.4);}
        @keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}

        /* ── BOTTOM NAV ── */
        .bottom-nav{position:fixed;bottom:0;left:0;right:0;background:rgba(8,8,16,.97);border-top:1px solid rgba(255,255,255,.07);backdrop-filter:blur(20px);padding:10px 0 14px;z-index:100;will-change:transform;transform:translate3d(0,0,0);}
        @media(min-width:1024px){.bottom-nav{display:none;}}
        .bottom-nav-inner{display:flex;}
        .bnav-item{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:#4b5563;text-decoration:none;font-size:10px;font-weight:500;transition:color .15s;}
        .bnav-item.active{color:#facc15;}

        /* ── SHEET (address picker / add form) ── */
        .sheet-backdrop{position:fixed;inset:0;z-index:300;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);opacity:0;pointer-events:none;transition:opacity .3s;}
        .sheet-backdrop.open{opacity:1;pointer-events:all;}
        .sheet{position:fixed;bottom:0;left:50%;transform:translateX(-50%) translateY(110%);width:100%;max-width:560px;z-index:400;background:#0e0f1a;border-radius:24px 24px 0 0;border:1px solid rgba(255,255,255,.08);border-bottom:none;transition:transform .38s cubic-bezier(.32,.72,0,1);max-height:92vh;overflow-y:auto;}
        @media(max-width:560px){.sheet{left:0;transform:translateY(110%);}}
        .sheet.open{transform:translateX(-50%) translateY(0);}
        @media(max-width:560px){.sheet.open{transform:translateY(0);}}
        .sheet-handle{width:40px;height:4px;border-radius:99px;background:rgba(255,255,255,.15);margin:12px auto 0;}
        .sheet-head{display:flex;align-items:center;justify-content:space-between;padding:14px 18px 10px;}
        .sheet-head-title{font-size:16px;font-weight:800;color:#fff;}
        .sheet-close{width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#6b7280;}
        .sheet-close:hover{background:rgba(255,255,255,.12);color:#fff;}
        .sheet-divider{height:1px;background:rgba(255,255,255,.06);margin:0 18px;}

        /* Saved address cards in picker */
        .saved-addr-card{display:flex;align-items:flex-start;gap:12px;padding:14px 18px;cursor:pointer;transition:background .15s;border-bottom:1px solid rgba(255,255,255,.04);}
        .saved-addr-card:last-child{border-bottom:none;}
        .saved-addr-card:hover{background:rgba(255,255,255,.03);}
        .saved-addr-card.selected-addr{background:rgba(250,204,21,.05);border-left:3px solid #facc15;}
        .addr-radio{width:18px;height:18px;border-radius:50%;border:2px solid rgba(255,255,255,.15);flex-shrink:0;margin-top:2px;display:flex;align-items:center;justify-content:center;transition:all .2s;}
        .addr-radio.checked{border-color:#facc15;background:#facc15;}
        .addr-radio.checked::after{content:'';width:7px;height:7px;border-radius:50%;background:#000;}
        .addr-card-actions{display:flex;gap:8px;margin-top:6px;}
        .addr-action-btn{font-size:11px;font-weight:600;padding:3px 10px;border-radius:6px;border:none;cursor:pointer;transition:all .15s;}
        .addr-edit-btn{background:rgba(250,204,21,.1);color:#facc15;border:1px solid rgba(250,204,21,.2);}
        .addr-edit-btn:hover{background:rgba(250,204,21,.2);}
        .addr-delete-btn{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.15);}
        .addr-delete-btn:hover{background:rgba(239,68,68,.15);}
        .addr-set-default-btn{background:rgba(34,197,94,.08);color:#4ade80;border:1px solid rgba(34,197,94,.15);}
        .addr-set-default-btn:hover{background:rgba(34,197,94,.15);}

        /* Add/Edit address form */
        .form-group{margin-bottom:13px;}
        .form-label{display:block;font-size:11px;font-weight:700;color:#6b7280;margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em;}
        .form-input{width:100%;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.08);border-radius:11px;padding:11px 14px;font-size:13px;color:#fff;outline:none;transition:border-color .2s;}
        .form-input::placeholder{color:#374151;}
        .form-input:focus{border-color:rgba(250,204,21,.45);background:rgba(255,255,255,.07);}
        .form-row-2{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
        .form-btn-row{display:flex;gap:10px;padding:14px 18px 32px;}
        .btn-save{flex:1;padding:13px;border-radius:13px;background:linear-gradient(135deg,#f59e0b,#facc15);border:none;color:#000;font-size:14px;font-weight:800;cursor:pointer;}
        .btn-cancel{padding:13px 18px;border-radius:13px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);color:#9ca3af;font-size:14px;font-weight:600;cursor:pointer;}
        .label-chips{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:13px;}
        .label-chip{padding:7px 16px;border-radius:99px;border:1.5px solid rgba(255,255,255,.09);background:rgba(255,255,255,.04);color:#6b7280;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;}
        .label-chip.active{border-color:#facc15;color:#facc15;background:rgba(250,204,21,.1);}

        /* light mode */
        .light-mode body{background:#f0f0f8!important;}
        .light-mode .topnav{background:rgba(255,255,255,.96)!important;border-color:rgba(0,0,0,.07)!important;}
        .light-mode .topnav-title{color:#111!important;}
        .light-mode .back-btn,.light-mode .theme-btn{background:rgba(0,0,0,.05)!important;border-color:rgba(0,0,0,.08)!important;color:#555!important;}
        .light-mode .card{background:#fff!important;border-color:rgba(0,0,0,.07)!important;box-shadow:0 2px 12px rgba(0,0,0,.06)!important;}
        .light-mode .card-title,.light-mode .addr-recipient,.light-mode .pay-label,.light-mode .tot-grand-label,.light-mode .sum-name{color:#111!important;}
        .light-mode .form-input{background:#f9fafb!important;border-color:rgba(0,0,0,.12)!important;color:#111!important;}
        .light-mode .pay-option{border-color:rgba(0,0,0,.1)!important;}
        .light-mode .bottom-nav{background:rgba(255,255,255,.97)!important;border-color:rgba(0,0,0,.07)!important;}
        .light-mode .sheet{background:#fff!important;border-color:rgba(0,0,0,.07)!important;}
        .light-mode .sheet-head-title{color:#111!important;}
        .light-mode .saved-addr-card{border-color:rgba(0,0,0,.06)!important;}
    </style>
</head>
<body>

{{-- Naujan-Only Geo-Restriction (same logic as shop index) --}}
<div id="geoOverlay" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(4,4,10,0.97);backdrop-filter:blur(20px);align-items:center;justify-content:center;flex-direction:column;font-family:'Inter',sans-serif;">
    <style>
        @keyframes geoPulse2{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,0.4)}60%{box-shadow:0 0 0 20px rgba(239,68,68,0)}}
        @keyframes geoFloat2{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}
        @keyframes spin2{to{transform:rotate(360deg)}}
        #geoOverlay .geo-card{background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(239,68,68,0.3);border-radius:28px;padding:40px 36px;max-width:420px;width:calc(100% - 32px);text-align:center;box-shadow:0 40px 80px rgba(0,0,0,0.8);}
        #geoOverlay .geo-icon-ring{width:88px;height:88px;border-radius:50%;margin:0 auto 24px;background:rgba(239,68,68,0.1);border:2px solid rgba(239,68,68,0.25);display:flex;align-items:center;justify-content:center;font-size:40px;animation:geoPulse2 2.5s ease-in-out infinite,geoFloat2 4s ease-in-out infinite;}
        #geoOverlay .geo-title{font-size:22px;font-weight:800;color:#fff;margin-bottom:10px;line-height:1.25;}
        #geoOverlay .geo-subtitle{font-size:13px;color:#6b7280;line-height:1.7;margin-bottom:20px;}
        #geoOverlay .geo-badge{display:inline-flex;align-items:center;gap:7px;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#f87171;font-size:12px;font-weight:700;padding:7px 16px;border-radius:99px;margin-bottom:28px;letter-spacing:0.04em;text-transform:uppercase;}
        #geoOverlay .geo-dist-box{background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:14px;padding:16px 20px;margin-bottom:24px;display:none;}
        #geoOverlay .geo-dist-box.visible{display:block;}
        #geoOverlay .geo-dist-label{font-size:11px;color:#4b5563;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px;}
        #geoOverlay .geo-dist-val{font-size:28px;font-weight:900;color:#ef4444;letter-spacing:-0.02em;}
        #geoOverlay .geo-dist-unit{font-size:13px;color:#6b7280;font-weight:500;}
        #geoOverlay .geo-map-pin{display:flex;align-items:center;gap:8px;font-size:12px;color:#4b5563;justify-content:center;margin-bottom:24px;}
        #geoOverlay .geo-back-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;border:none;border-radius:14px;padding:14px 32px;font-size:14px;font-weight:700;cursor:pointer;transition:all 0.2s;width:100%;box-shadow:0 4px 18px rgba(220,38,38,0.4);text-decoration:none;}
        #geoOverlay .geo-checking{display:flex;flex-direction:column;align-items:center;gap:12px;color:#9ca3af;font-size:13px;}
        #geoOverlay .geo-spinner{width:32px;height:32px;border:3px solid rgba(255,255,255,0.08);border-top-color:#facc15;border-radius:50%;animation:spin2 0.8s linear infinite;}
    </style>
    <div class="geo-card">
        <div class="geo-checking" id="geoChecking"><div class="geo-spinner"></div><p>Verifying your location…</p></div>
        <div id="geoBlocked" style="display:none;">
            <div class="geo-icon-ring">📍</div>
            <div class="geo-badge">Outside Coverage Area</div>
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
(function(){
    var NAUJAN_LAT=13.3215,NAUJAN_LNG=121.3021,MAX_KM=30,CACHE_KEY='eut_geo_ok',CACHE_MS=30*60*1000;
    function hav(a,b,c,d){var R=6371,dL=(c-a)*Math.PI/180,dG=(d-b)*Math.PI/180,e=Math.sin(dL/2)**2+Math.cos(a*Math.PI/180)*Math.cos(c*Math.PI/180)*Math.sin(dG/2)**2;return R*2*Math.atan2(Math.sqrt(e),Math.sqrt(1-e));}
    function showOvr(){document.getElementById('geoOverlay').style.display='flex';document.body.style.overflow='hidden';}
    function block(km){showOvr();document.getElementById('geoChecking').style.display='none';document.getElementById('geoBlocked').style.display='block';if(km){document.getElementById('geoDistVal').textContent=Math.round(km);document.getElementById('geoDistBox').classList.add('visible');}}
    function allow(la,ln){sessionStorage.setItem(CACHE_KEY,JSON.stringify({lat:la,lng:ln,ts:Date.now()}));window.__customerLat=la;window.__customerLng=ln;}
    try{var c=JSON.parse(sessionStorage.getItem(CACHE_KEY)||'null');if(c&&(Date.now()-c.ts)<CACHE_MS){var km=hav(c.lat,c.lng,NAUJAN_LAT,NAUJAN_LNG);if(km<=MAX_KM){window.__customerLat=c.lat;window.__customerLng=c.lng;return;}}}catch(e){}
    if(!navigator.geolocation)return;
    showOvr();
    navigator.geolocation.getCurrentPosition(
        function(p){var km=hav(p.coords.latitude,p.coords.longitude,NAUJAN_LAT,NAUJAN_LNG);if(km<=MAX_KM){allow(p.coords.latitude,p.coords.longitude);document.getElementById('geoOverlay').style.display='none';document.body.style.overflow='';}else{block(km);}},
        function(){document.getElementById('geoOverlay').style.display='none';document.body.style.overflow='';},
        {timeout:8000,maximumAge:300000}
    );
})();
</script>

<!-- NAVBAR -->

<nav class="topnav">
    <div class="topnav-inner">
        <a href="{{ route('shop.cart') }}" class="back-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="topnav-title">Checkout</span>
        <button id="shopThemeToggle" class="theme-btn">
            <svg id="shopSunIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#facc15;display:none;"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            <svg id="shopMoonIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#9ca3af;"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
        </button>
    </div>
</nav>

<!-- PAGE -->
<div class="page-body">
  <form id="checkoutForm">
    <div class="checkout-grid">

      <!-- LEFT: Address + Payment + Notes -->
      <div>

        <!-- Delivery Address Card -->
        <div class="card" id="addressCard">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(239,68,68,.1);">
                    <svg width="15" height="15" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="card-title">Delivery Address</span>
                @auth
                <button type="button" onclick="openAddressPicker()" style="font-size:11px;font-weight:700;color:#facc15;background:none;border:none;cursor:pointer;padding:0;">
                    Change
                </button>
                @endauth
            </div>

            @auth
            <!-- Selected address display (filled by JS) -->
            <div id="addrSelectedWrap">
                <div class="addr-selected" onclick="openAddressPicker()">
                    <div class="addr-pin">
                        <svg width="14" height="14" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="addr-info" id="addrInfoDisplay">
                        <p style="font-size:13px;color:#4b5563;">Loading your addresses…</p>
                    </div>
                    <svg class="addr-chevron" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
            </div>
            <div style="padding:6px 18px 12px;">
                <span id="gpsCheckoutStatus" style="font-size:11px;color:#4b5563;display:flex;align-items:center;gap:4px;">
                    <span style="width:5px;height:5px;background:#f59e0b;border-radius:50%;animation:blink 1.2s infinite;display:inline-block;"></span>
                    Capturing your location for delivery pin…
                </span>
            </div>
            @endauth

            @guest
            <div class="addr-empty">
                <p class="addr-empty-txt">Log in to use saved addresses</p>
                <a href="{{ route('restaurant') }}" class="btn-add-addr">Log in</a>
            </div>
            @endguest
        </div>

        <!-- Order Type -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(250,204,21,.1);">
                    <svg width="15" height="15" fill="none" stroke="#facc15" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <span class="card-title">Order Type</span>
            </div>
            <div class="card-body" style="padding-bottom:6px; display: flex; gap: 8px;">
                <label class="pay-option selected" style="flex:1; flex-direction: column; padding: 10px; gap: 4px; text-align: center;" onclick="selectOrderType(this, 'delivery')">
                    <input type="radio" name="order_type" value="delivery" checked style="display:none;">
                    <span style="display:flex;justify-content:center;"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3M5 8c0 1.657 3.134 3 7 3s7-1.343 7-3"/><circle cx="17" cy="17" r="2"/><path stroke-linecap="round" d="M3 11h3l1.5 5h9l1.5-5h3"/></svg></span>
                    <span style="font-size:12px; font-weight:700;">Delivery</span>
                </label>
                <label class="pay-option" style="flex:1; flex-direction: column; padding: 10px; gap: 4px; text-align: center;" onclick="selectOrderType(this, 'pickup')">
                    <input type="radio" name="order_type" value="pickup" style="display:none;">
                    <span style="display:flex;justify-content:center;"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg></span>
                    <span style="font-size:12px; font-weight:700;">Pickup</span>
                </label>
                <label class="pay-option" style="flex:1; flex-direction: column; padding: 10px; gap: 4px; text-align: center;" onclick="selectOrderType(this, 'dine_in')">
                    <input type="radio" name="order_type" value="dine_in" style="display:none;">
                    <span style="display:flex;justify-content:center;"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.5 6M17 13l1.5 6M9 19h6"/></svg></span>
                    <span style="font-size:12px; font-weight:700;">Dine-in</span>
                </label>
            </div>
        </div>

        <!-- Table Number — hidden field, filled by QR scanner modal -->
        <input type="hidden" id="tableNumberInput" name="table_number">

        <!-- Dine-in Location Warning (only shown when not at restaurant) -->
        <div id="dineInLocationWarning" style="display:none;align-items:center;justify-content:space-between;gap:1rem;padding:.8rem 1.25rem;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:.75rem;margin-bottom:1rem;flex-wrap:wrap;flex-direction:column;text-align:center;">
            <div style="display:flex;align-items:center;gap:.6rem;width:100%;justify-content:center;">
                <svg width="1.1rem" height="1.1rem" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2a7 7 0 0 1 7 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 0 1 7-7Z"/>
                    <circle cx="12" cy="9" r="2.5" stroke-width="1.75"/>
                </svg>
                <p style="font-size:.8rem;font-weight:700;color:#ef4444;margin:0;">Dine-in orders require you to be at our restaurant location</p>
            </div>
            <p style="font-size:.75rem;color:#9ca3af;margin:0;width:100;">Please visit us in person to place a dine-in order.</p>
        </div>

        <!-- Payment Method -->
        <div class="card" id="paymentMethodCard">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(96,165,250,.1);">
                    <svg width="15" height="15" fill="none" stroke="#60a5fa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="card-title">Payment Method</span>
            </div>
            <div class="card-body" style="padding-bottom:6px;">
                <label class="pay-option selected" onclick="selectPay(this)">
                    <input type="radio" name="payment" value="cod" checked>
                    <span class="pay-emoji"><svg width="18" height="18" fill="none" stroke="#4ade80" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></span>
                    <div><p class="pay-label">Cash on Delivery</p><p class="pay-sub">Pay when order arrives</p></div>
                </label>
                <label class="pay-option" onclick="selectPay(this)">
                    <input type="radio" name="payment" value="gcash">
                    <span class="pay-emoji"><svg width="18" height="18" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><path stroke-linecap="round" d="M12 18h.01"/></svg></span>
                    <div><p class="pay-label">GCash</p><p class="pay-sub">Pay via GCash e-wallet</p></div>
                </label>
                <label class="pay-option" onclick="selectPay(this)">
                    <input type="radio" name="payment" value="card">
                    <span class="pay-emoji">💳</span>
                    <div><p class="pay-label">Credit / Debit Card</p><p class="pay-sub">Visa or Mastercard</p></div>
                </label>
            </div>
        </div>

        <!-- Notes -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(139,92,246,.1);">
                    <svg width="15" height="15" fill="none" stroke="#a78bfa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <span class="card-title">Order Notes <span style="font-size:11px;font-weight:400;color:#4b5563;">(optional)</span></span>
            </div>
            <div class="card-body">
                <textarea name="notes" id="orderNotes" class="notes-input" rows="2" placeholder="e.g. Leave at gate, no spicy, extra napkins…"></textarea>
            </div>
        </div>

      </div><!-- /left -->

      <!-- RIGHT: Order Summary -->
      <div class="summary-sticky">
        <div class="card" style="margin-bottom:0;">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(250,204,21,.1);">
                    <svg width="15" height="15" fill="none" stroke="#facc15" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <span class="card-title">Order Summary</span>
                <span id="itemCountBadge" style="margin-left:auto;font-size:11px;background:rgba(250,204,21,.1);border:1px solid rgba(250,204,21,.25);color:#facc15;padding:2px 10px;border-radius:99px;font-weight:700;">0 items</span>
            </div>

            <!-- Free delivery bar -->
            <div id="freeBar" class="free-bar-wrap" style="display:none;">
                <span style="font-size:14px;display:flex;align-items:center;"><svg width="14" height="14" fill="none" stroke="#facc15" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg></span>
                <div style="flex:1;">
                    <p style="font-size:11px;color:#4b5563;margin-bottom:4px;" id="freeBarText"></p>
                    <div class="free-bar-track"><div class="free-bar-fill" id="freeBarFill" style="width:0%"></div></div>
                </div>
            </div>

            <div class="items-scroll" id="checkoutItemsList"></div>

            <div style="margin-top:8px;border-top:1px solid rgba(255,255,255,.05);">
                <div class="tot-row"><span class="tot-label">Subtotal</span><span class="tot-val" id="coSubtotal">₱0</span></div>
                <div class="tot-row"><span class="tot-label">Delivery fee</span><span class="tot-val" id="coDelivery">₱30</span></div>
                <div id="deliveryDistRow" style="margin:-6px 0 4px;"><span style="font-size:10px;color:#4b5563;" id="deliveryDistLabel">Calculating distance…</span></div>
                <div class="tot-row" style="padding-top:12px;padding-bottom:12px;border-top:1px solid rgba(255,255,255,.06);">
                    <span class="tot-grand-label">Total</span>
                    <span class="tot-grand-val" id="coTotal">₱0</span>
                </div>
            </div>

            @guest
            <div class="guest-notice">⚠️ Please <a href="{{ route('restaurant') }}">log in</a> to place your order.</div>
            @endguest
            @auth
            @if(!$isOpen)
            <div style="margin:4px 18px 12px;padding:12px 14px;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:12px;text-align:center;display:flex;align-items:center;justify-content:center;gap:8px;">
                <svg width="15" height="15" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                <span style="font-size:12px;font-weight:700;color:#f87171;">Shop is <strong>CLOSED</strong> — orders not accepted right now.</span>
            </div>
            <button type="button" class="place-btn" disabled style="opacity:.5;cursor:not-allowed;background:linear-gradient(135deg,#374151,#4b5563);">Shop Closed</button>
            @else
            <button type="submit" class="place-btn" id="placeOrderBtn">Place Order</button>
            @endif
            @endauth
        </div>
      </div><!-- /right -->

    </div><!-- /grid -->
  </form>
</div>

<!-- BOTTOM NAV -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>Menu</a>
        <a href="{{ route('shop.tracking') }}" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Orders</a>
        <a href="{{ route('shop.cart') }}" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Cart</a>
        <a href="{{ route('shop.profile') }}" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Profile</a>
    </div>
</nav>

<!-- ══ ADDRESS PICKER SHEET ══ -->
<div class="sheet-backdrop" id="pickerBackdrop" onclick="closePicker()"></div>
<div class="sheet" id="pickerSheet">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
        <span class="sheet-head-title" id="pickerTitle">Select Address</span>
        <button class="sheet-close" onclick="closePicker()">✕</button>
    </div>
    <div class="sheet-divider"></div>
    <!-- list of saved addresses -->
    <div id="addrList"></div>
    <!-- add new button -->
    <div style="padding:14px 18px 24px;">
        <button type="button" class="btn-add-addr" style="width:100%;justify-content:center;" onclick="openAddForm()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Add New Address
        </button>
    </div>
</div>

<!-- ══ ADD / EDIT ADDRESS SHEET ══ -->
<div class="sheet-backdrop" id="formBackdrop" onclick="closeAddrForm()"></div>
<div class="sheet" id="formSheet">
    <div class="sheet-handle"></div>
    <div class="sheet-head">
        <span class="sheet-head-title" id="formTitle">Add Address</span>
        <button class="sheet-close" onclick="closeAddrForm()">✕</button>
    </div>
    <div class="sheet-divider"></div>
    <div style="padding:16px 18px 0;">
        <input type="hidden" id="editAddrId">
        <!-- Label chips -->
        <div style="margin-bottom:6px;">
            <p class="form-label">Label</p>
            <div class="label-chips">
                <button type="button" class="label-chip active" data-label="Home" onclick="selectLabel(this)">🏠 Home</button>
                <button type="button" class="label-chip" data-label="Work" onclick="selectLabel(this)">💼 Work</button>
                <button type="button" class="label-chip" data-label="Other" onclick="selectLabel(this)">📍 Other</button>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:13px;">
            <div>
                <p class="form-label">Recipient Name</p>
                <input type="text" id="fName" class="form-input" placeholder="Juan dela Cruz">
            </div>
            <div>
                <p class="form-label">Phone</p>
                <input type="tel" id="fPhone" class="form-input" placeholder="09XX XXX XXXX">
            </div>
        </div>
        <div class="form-group">
            <p class="form-label">Street / House No.</p>
            <input type="text" id="fAddress" class="form-input" placeholder="123 Rizal St., Brgy. ...">
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:13px;">
            <div>
                <p class="form-label">Barangay / City</p>
                <input type="text" id="fBarangay" class="form-input" placeholder="Barangay / City">
            </div>
            <div>
                <p class="form-label">Postal Code</p>
                <input type="text" id="fPostal" class="form-input" placeholder="4300">
            </div>
        </div>
        <label style="display:flex;align-items:center;gap:10px;padding:12px 0;cursor:pointer;">
            <input type="checkbox" id="fDefault" style="accent-color:#facc15;width:16px;height:16px;">
            <span style="font-size:13px;color:#9ca3af;font-weight:500;">Set as default address</span>
        </label>
        <p id="formError" style="display:none;color:#f87171;font-size:12px;margin-top:4px;"></p>
    </div>
    <div class="form-btn-row">
        <button type="button" class="btn-cancel" onclick="closeAddrForm()">Cancel</button>
        <button type="button" class="btn-save" id="saveAddrBtn" onclick="saveAddress()">Save Address</button>
    </div>
</div>

<script>
/* ── Theme ── */
function applyTheme(t){document.documentElement.classList.toggle('light-mode',t==='light');document.getElementById('shopSunIcon').style.display=t==='dark'?'block':'none';document.getElementById('shopMoonIcon').style.display=t==='light'?'block':'none';}
document.addEventListener('DOMContentLoaded',()=>{
    applyTheme(localStorage.getItem('eutTheme')||'dark');
    document.getElementById('shopThemeToggle').addEventListener('click',()=>{
        const t=(localStorage.getItem('eutTheme')||'dark')==='dark'?'light':'dark';
        localStorage.setItem('eutTheme',t);applyTheme(t);
    });
    renderSummary();
    @auth loadAddresses(); @endauth

    // Restore order type selected on shop menu page
    const savedType = localStorage.getItem('eutOrderType') || 'delivery';
    const savedLabel = document.querySelector(`.pay-option input[value="${savedType}"]`);
    if (savedLabel) {
        const label = savedLabel.closest('.pay-option');
        if (label) selectOrderType(label, savedType);
    }
});

/* ── Helpers ── */
const CSRF = '{{ csrf_token() }}';
const DELIVERY_FEE_URL = '{{ route("delivery-fee") }}';
// Delivery pricing constants (mirrors server-side)
const DELIVERY_BASE_FEE = 30;   // ₱30 base (covers first 2 km)
const DELIVERY_PER_KM   = 10;   // ₱10 per km beyond 2 km
const DELIVERY_FREE_KM  = 2;    // first 2 km included in base
const DELIVERY_MAX_KM   = 100;  // max range
let currentDeliveryFee  = DELIVERY_BASE_FEE; // updated when address coords are known

function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function modChips(mods){
    if(!mods||!mods.length)return'';
    const tc={flavor:{bg:'rgba(59,130,246,.15)',c:'#3b82f6',i:'🌶'},modifier:{bg:'rgba(139,92,246,.15)',c:'#8b5cf6',i:'⚙'},addon:{bg:'rgba(245,158,11,.15)',c:'#d97706',i:'➕'}};
    const chips=(mods||[]).filter(m=>m&&m.name&&!/^no\s/i.test(m.name)).map(m=>{
        const s=tc[m.type]||tc.modifier;
        const adj=parseFloat(m.price_adjustment||0);
        const ex=(m.price_type==='add'&&adj>0)?` <span style="color:#4ade80;font-size:.6rem;">+₱${adj.toLocaleString()}</span>`:'';
        return`<span style="display:inline-flex;align-items:center;gap:3px;padding:1px 8px;border-radius:99px;font-size:10px;font-weight:600;background:${s.bg};color:${s.c};border:1px solid ${s.c}30;white-space:nowrap;">${s.i} ${esc(m.name)}${ex}</span>`;
    });
    return chips.length?`<div style="display:flex;flex-wrap:wrap;gap:3px;margin-top:5px;">${chips.join('')}</div>`:'';
}

/* ── Fetch delivery fee from server when coordinates are known ── */
async function updateDeliveryFeeByCoords(lat, lng) {
    if (!lat || !lng || currentOrderType !== 'delivery') return;
    try {
        const res = await fetch(`${DELIVERY_FEE_URL}?lat=${lat}&lng=${lng}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();
        if (data.success) {
            currentDeliveryFee = data.fee;
            const distEl = document.getElementById('deliveryDistLabel');
            if (distEl) distEl.textContent = data.label;
            renderSummary();
        } else {
            const errEl = document.getElementById('addressError');
            if (errEl) { errEl.textContent = data.message; errEl.style.display = 'block'; }
        }
    } catch(e) {}
}

/* ── Order summary ── */
function renderSummary(){
    const cart=JSON.parse(localStorage.getItem('eutCart')||'[]');
    const el=document.getElementById('checkoutItemsList');
    el.innerHTML='';
    let sub=0;
    if(!cart.length){el.innerHTML='<p style="font-size:13px;color:#4b5563;text-align:center;padding:16px;">Cart is empty</p>';}
    cart.forEach(item=>{
        const row=document.createElement('div');
        row.className='sum-item';
        row.innerHTML=`<img src="${esc(item.image||'')}" class="sum-img" alt="${esc(item.name)}" onerror="this.src='{{ asset('images/hero-burger.webp') }}'">
            <div style="flex:1;min-width:0;"><p class="sum-name">${esc(item.name)}</p><p class="sum-meta">× ${item.quantity} · ₱${Number(item.price).toLocaleString()} each</p>${modChips(item.modifiers)}</div>
            <span class="sum-price">₱${(item.price*item.quantity).toLocaleString()}</span>`;
        el.appendChild(row);sub+=item.price*item.quantity;
    });
    const qty=cart.reduce((s,i)=>s+i.quantity,0);
    const fee = currentOrderType !== 'delivery' ? 0 : currentDeliveryFee;
    document.getElementById('itemCountBadge').textContent=qty+(qty===1?' item':' items');
    document.getElementById('coSubtotal').textContent='₱'+sub.toLocaleString();
    document.getElementById('coDelivery').innerHTML=fee===0
        ? '<span style="color:#4ade80;font-weight:700;">FREE</span>'
        : '₱'+fee.toLocaleString();
    document.getElementById('coTotal').textContent='₱'+(sub+fee).toLocaleString();
    const fb=document.getElementById('freeBar');
    if(fb) fb.style.display='none'; // no free-delivery threshold anymore
}

/* ── Payment highlight ── */
function selectPay(el){document.querySelectorAll('.pay-option').forEach(l=>l.classList.remove('selected'));el.classList.add('selected');}

/* ── Order Type highlight ── */
let currentOrderType = 'delivery';
function selectOrderType(el, type){
    localStorage.setItem('eutOrderType', type); // persist selection across pages
    const container = el.closest('.card-body');
    container.querySelectorAll('.pay-option').forEach(l=>l.classList.remove('selected'));
    el.classList.add('selected');
    el.querySelector('input').checked = true;
    currentOrderType = type;

    // Hide/Show address card based on type
    const addrCard = document.getElementById('addressCard');
    if(type === 'delivery') {
        addrCard.style.display = 'block';
        // Recalculate fee for currently selected address
        const a = addresses.find(a => a.id === selectedAddressId);
        if (a && a.lat && a.lng) updateDeliveryFeeByCoords(a.lat, a.lng);
    } else {
        addrCard.style.display = 'none';
    }

    // Show table number field only for dine-in
    const tableCard = document.getElementById('tableNumberCard');
    if (tableCard) tableCard.style.display = type === 'dine_in' ? 'block' : 'none';
    const tableInput = document.getElementById('tableNumberInput');
    if (tableInput) tableInput.required = (type === 'dine_in');

    // Hide/Show payment method card — only visible for delivery
    const paymentCard = document.getElementById('paymentMethodCard');
    if(paymentCard) {
        paymentCard.style.display = type === 'delivery' ? 'block' : 'none';
    }
}

/* ═══════════════════════════════════
   ADDRESS SYSTEM
═══════════════════════════════════ */
let addresses = [];
let selectedAddressId = null;
let editingAddressId  = null;
let activeLabel = 'Home';

async function loadAddresses(){
    try{
        const r=await fetch('/addresses',{headers:{'Accept':'application/json','X-CSRF-TOKEN':CSRF}});
        const d=await r.json();
        addresses=d.addresses||[];
        const def=addresses.find(a=>a.is_default)||addresses[0]||null;
        selectedAddressId=def?def.id:null;
        renderSelectedAddress();
        // Calculate delivery fee for default address
        if (def && def.lat && def.lng) {
            updateDeliveryFeeByCoords(def.lat, def.lng);
        }
    }catch(e){console.error('addr load failed',e);}
}

function renderSelectedAddress(){
    const el=document.getElementById('addrInfoDisplay');
    if(!el)return;
    const a=addresses.find(a=>a.id===selectedAddressId);
    if(!a){
        el.innerHTML=`<p style="font-size:13px;color:#4b5563;margin-bottom:8px;">No address saved yet.</p>
            <button type="button" class="btn-add-addr" onclick="openAddForm()">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Add Address
            </button>`;
        return;
    }
    el.innerHTML=`<div class="addr-name-row">
            <span class="addr-recipient">${esc(a.recipient_name)}</span>
            <span class="addr-phone">${esc(a.phone)}</span>
            <span class="addr-label-badge ${a.is_default?'default':''}">${esc(a.label)}${a.is_default?' · Default':''}</span>
        </div>
        <p class="addr-text">${esc(a.full_address)}</p>`;
}

/* ── Picker sheet ── */
function openAddressPicker(){
    renderAddrList();
    document.getElementById('pickerBackdrop').classList.add('open');
    document.getElementById('pickerSheet').classList.add('open');
    document.body.style.overflow='hidden';
}
function closePicker(){
    document.getElementById('pickerBackdrop').classList.remove('open');
    document.getElementById('pickerSheet').classList.remove('open');
    document.body.style.overflow='';
}

function renderAddrList(){
    const el=document.getElementById('addrList');
    if(!addresses.length){
        el.innerHTML='<p style="padding:16px 18px;font-size:13px;color:#4b5563;text-align:center;">No saved addresses yet.</p>';
        return;
    }
    el.innerHTML=addresses.map(a=>`
        <div class="saved-addr-card ${a.id===selectedAddressId?'selected-addr':''}" onclick="selectAddress(${a.id})">
            <div class="addr-radio ${a.id===selectedAddressId?'checked':''}"></div>
            <div style="flex:1;min-width:0;">
                <div class="addr-name-row">
                    <span class="addr-recipient">${esc(a.recipient_name)}</span>
                    <span class="addr-phone">${esc(a.phone)}</span>
                    <span class="addr-label-badge ${a.is_default?'default':''}">${esc(a.label)}${a.is_default?' · Default':''}</span>
                </div>
                <p class="addr-text">${esc(a.full_address)}</p>
                <div class="addr-card-actions">
                    <button class="addr-action-btn addr-edit-btn" onclick="event.stopPropagation();openEditForm(${a.id})">Edit</button>
                    ${!a.is_default?`<button class="addr-action-btn addr-set-default-btn" onclick="event.stopPropagation();setDefault(${a.id})">Set Default</button>`:''}
                    <button class="addr-action-btn addr-delete-btn" onclick="event.stopPropagation();deleteAddress(${a.id})">Delete</button>
                </div>
            </div>
        </div>`).join('');
}

function selectAddress(id){
    selectedAddressId=id;
    renderSelectedAddress();
    renderAddrList();
    // Update delivery fee based on selected address coordinates
    const a = addresses.find(a => a.id === id);
    if (a && a.lat && a.lng) {
        updateDeliveryFeeByCoords(a.lat, a.lng);
    }
    setTimeout(closePicker,200);
}

async function setDefault(id){
    await fetch(`/addresses/${id}/default`,{method:'PATCH',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
    addresses=addresses.map(a=>({...a,is_default:a.id===id}));
    renderAddrList();
    renderSelectedAddress();
}

async function deleteAddress(id){
    if(!confirm('Remove this address?'))return;
    await fetch(`/addresses/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
    addresses=addresses.filter(a=>a.id!==id);
    if(selectedAddressId===id){const def=addresses.find(a=>a.is_default)||addresses[0]||null;selectedAddressId=def?def.id:null;}
    renderAddrList();renderSelectedAddress();
}

/* ── Add/Edit form sheet ── */
function selectLabel(el){document.querySelectorAll('.label-chip').forEach(c=>c.classList.remove('active'));el.classList.add('active');activeLabel=el.dataset.label;}

function openAddForm(){
    editingAddressId=null;
    document.getElementById('formTitle').textContent='Add Address';
    document.getElementById('editAddrId').value='';
    document.getElementById('fName').value='{{ auth()->user()?->name ?? "" }}';
    document.getElementById('fPhone').value='';
    document.getElementById('fAddress').value='';
    document.getElementById('fBarangay').value='';
    document.getElementById('fPostal').value='';
    document.getElementById('fDefault').checked=addresses.length===0;
    document.getElementById('formError').style.display='none';
    selectLabel(document.querySelector('.label-chip[data-label="Home"]'));
    closePicker();
    document.getElementById('formBackdrop').classList.add('open');
    document.getElementById('formSheet').classList.add('open');
    document.body.style.overflow='hidden';
}

function openEditForm(id){
    const a=addresses.find(a=>a.id===id);if(!a)return;
    editingAddressId=id;
    document.getElementById('formTitle').textContent='Edit Address';
    document.getElementById('editAddrId').value=id;
    document.getElementById('fName').value=a.recipient_name;
    document.getElementById('fPhone').value=a.phone;
    document.getElementById('fAddress').value=a.address;
    document.getElementById('fBarangay').value=a.barangay||'';
    document.getElementById('fPostal').value=a.postal||'';
    document.getElementById('fDefault').checked=a.is_default;
    document.getElementById('formError').style.display='none';
    const chip=document.querySelector(`.label-chip[data-label="${a.label}"]`)||document.querySelector('.label-chip[data-label="Other"]');
    selectLabel(chip);
    closePicker();
    document.getElementById('formBackdrop').classList.add('open');
    document.getElementById('formSheet').classList.add('open');
    document.body.style.overflow='hidden';
}

function closeAddrForm(){
    document.getElementById('formBackdrop').classList.remove('open');
    document.getElementById('formSheet').classList.remove('open');
    document.body.style.overflow='';
}

/* ── Geocode an address string → [lat, lng] using Nominatim ── */
async function geocodeAddress(addrText) {
    if (!addrText) return [null, null];
    const queries = [
        addrText + ', Naujan, Oriental Mindoro, Philippines',
        addrText + ', Oriental Mindoro, Philippines',
        addrText + ', Philippines',
    ];
    for (const q of queries) {
        try {
            const r = await fetch(
                'https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=1&countrycodes=ph',
                { headers: { 'Accept-Language': 'en', 'User-Agent': 'EUT-Delivery-App/1.0' } }
            );
            const data = await r.json();
            if (data && data.length) {
                const lat = parseFloat(data[0].lat), lng = parseFloat(data[0].lon);
                // Sanity-check: must be within Philippine bounding box
                if (lat > 4 && lat < 22 && lng > 116 && lng < 127) return [lat, lng];
            }
        } catch(e) { /* try next query */ }
        await new Promise(res => setTimeout(res, 350));
    }
    return [null, null];
}

async function saveAddress(){
    const name=document.getElementById('fName').value.trim();
    const phone=document.getElementById('fPhone').value.trim();
    const addr=document.getElementById('fAddress').value.trim();
    const errEl=document.getElementById('formError');
    const btn=document.getElementById('saveAddrBtn');
    errEl.style.display='none';
    if(!name||!phone||!addr){errEl.textContent='Name, phone and address are required.';errEl.style.display='block';return;}
    btn.disabled=true;btn.textContent='Saving…';
    // Geocode the address so we store accurate coords on the address record
    const fullAddrText = [addr, document.getElementById('fBarangay').value.trim()].filter(Boolean).join(', ');
    const [addrLat, addrLng] = await geocodeAddress(fullAddrText);
    const payload={
        label:activeLabel,recipient_name:name,phone,address:addr,
        barangay:document.getElementById('fBarangay').value.trim(),
        postal:document.getElementById('fPostal').value.trim(),
        is_default:document.getElementById('fDefault').checked,
        lat: addrLat,
        lng: addrLng,
    };
    const url=editingAddressId?`/addresses/${editingAddressId}`:'/addresses';
    const method=editingAddressId?'PUT':'POST';
    try{
        const r=await fetch(url,{method,headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},body:JSON.stringify(payload)});
        const d=await r.json();
        if(d.success){
            if(editingAddressId){addresses=addresses.map(a=>a.id===editingAddressId?d.address:a);}
            else{addresses.push(d.address);}
            if(d.address.is_default){addresses=addresses.map(a=>({...a,is_default:a.id===d.address.id}));}
            if(!selectedAddressId||d.address.is_default)selectedAddressId=d.address.id;
            renderSelectedAddress();
            closeAddrForm();
        }else{errEl.textContent=d.message||'Failed to save.';errEl.style.display='block';}
    }catch(e){errEl.textContent='Network error.';errEl.style.display='block';}
    btn.disabled=false;btn.textContent='Save Address';
}

/* ── GPS capture — starts immediately and persists for accuracy ── */
let _gpsLat = null, _gpsLng = null;
let _gpsWatchCo = null;
let _gpsGranted = false;

function showLocationGate(show, reason) {
    const gate = document.getElementById('locationGate');
    const msg  = document.getElementById('locationGateMsg');
    if (!gate) return;
    gate.style.display = show ? 'flex' : 'none';
    document.body.style.overflow = show ? 'hidden' : '';
    if (reason && msg) msg.textContent = reason;
}

function retryCheckoutGps() {
    showLocationGate(false);
    startCheckoutGps();
}

function startCheckoutGps() {
    // Geolocation requires HTTPS — skip silently on HTTP instead of blocking the user
    if (location.protocol !== 'https:') {
        const el = document.getElementById('gpsCheckoutStatus');
        if (el) el.innerHTML = '<span style="color:#6b7280;">📍 GPS unavailable on HTTP — using saved address</span>';
        return;
    }
    if (!navigator.geolocation) {
        const el = document.getElementById('gpsCheckoutStatus');
        if (el) el.innerHTML = '<span style="color:#6b7280;">GPS not supported by browser</span>';
        return;
    }
    if (_gpsWatchCo !== null) navigator.geolocation.clearWatch(_gpsWatchCo);
    _gpsWatchCo = navigator.geolocation.watchPosition(
        p => {
            _gpsLat = p.coords.latitude;
            _gpsLng = p.coords.longitude;
            _gpsGranted = true;
            showLocationGate(false);
            const el = document.getElementById('gpsCheckoutStatus');
            if (el) {
                el.innerHTML = '<span style="width:6px;height:6px;background:#10b981;border-radius:50%;display:inline-block;animation:blink 1.5s infinite;"></span><span style="color:#10b981;font-weight:600;">Location locked — accurate delivery pin ✓</span>';
            }
        },
        err => {
            _gpsGranted = false;
            // Don't block checkout — GPS is optional, delivery address is used as fallback
            const el = document.getElementById('gpsCheckoutStatus');
            if (err.code === 1) {
                if (el) el.innerHTML = '<span style="color:#6b7280;">📍 Location not shared — using saved address</span>';
            } else {
                if (el) el.innerHTML = '<span style="color:#6b7280;">📍 GPS unavailable — using saved address</span>';
            }
        },
        { enableHighAccuracy: true, maximumAge: 0, timeout: 12000 }
    );
}

// Check permission first, then start — never block checkout on GPS failure
if (navigator.permissions && location.protocol === 'https:') {
    navigator.permissions.query({ name: 'geolocation' }).then(r => {
        if (r.state !== 'denied') {
            startCheckoutGps();
        } else {
            const el = document.getElementById('gpsCheckoutStatus');
            if (el) el.innerHTML = '<span style="color:#6b7280;">📍 Location blocked — using saved address</span>';
        }
        r.onchange = () => {
            if (r.state === 'granted') { showLocationGate(false); startCheckoutGps(); }
        };
    });
} else {
    startCheckoutGps();
}

/* ── Place Order ── */
document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    @guest alert('Please log in to place an order.'); return; @endguest

    const cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
    if (!cart.length) { alert('Your cart is empty.'); return; }

    const orderType = document.querySelector('input[name=order_type]:checked')?.value || 'delivery';
    const addr = addresses.find(a => a.id === selectedAddressId);
    
    if (orderType === 'delivery' && !addr) { 
        alert('Please add a delivery address first.'); 
        openAddForm(); 
        return; 
    }

    // For dine-in: intercept and open QR scanner modal
    if (orderType === 'dine_in') {
        openTableScanner();
        return; // will be resumed by confirmTableAndOrder()
    }
    const tableNumber = '';

    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true; btn.textContent = 'Placing order…';

    const payRaw  = document.querySelector('input[name=payment]:checked')?.value || 'cod';
    const payment = payRaw === 'cod' ? 'cash' : payRaw;
    const notes   = document.getElementById('orderNotes')?.value?.trim() || '';
    
    let deliveryAddress = 'Store Pickup / Dine-in';
    let deliveryBarangay = '';
    let lat = null;
    let lng = null;

    if (orderType === 'delivery') {
        deliveryAddress = `${addr.recipient_name}, ${addr.full_address}`;
        deliveryBarangay = addr.barangay || addr.city || '';
        
        /* If GPS not yet captured, do one last blocking attempt (5s max) */
        if (!_gpsLat && navigator.geolocation) {
            await new Promise(res => navigator.geolocation.getCurrentPosition(
                p => { _gpsLat = p.coords.latitude; _gpsLng = p.coords.longitude; res(); },
                () => res(),
                { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
            ));
        }
        lat = _gpsLat || addr.lat || null;
        lng = _gpsLng || addr.lng || null;
    }

    const items = cart.map(i => ({
        id: i.id, qty: i.quantity,
        modifiers: (i.modifiers || [])
            .filter(m => m && typeof m === 'object' && m.name)
            .map(m => ({
                type: m.type || 'modifier',
                name: m.name || '',
                price_type: m.price_type || 'none',
                price_adjustment: parseFloat(m.price_adjustment || 0),
            })),
    }));

    const payload = {
        items, 
        order_type: orderType,
        delivery_address: deliveryAddress,
        delivery_barangay: deliveryBarangay,
        payment_method: payment, 
        notes,
        delivery_lat: lat,
        delivery_lng: lng,
        table_number: orderType === 'dine_in' ? tableNumber : null,
        // ── Naujan geo-restriction: send customer location to backend ──
        customer_lat: window.__customerLat || (function() {
            try { const c = JSON.parse(sessionStorage.getItem('eut_geo_ok') || 'null'); return c?.lat || null; } catch(e) { return null; }
        })(),
        customer_lng: window.__customerLng || (function() {
            try { const c = JSON.parse(sessionStorage.getItem('eut_geo_ok') || 'null'); return c?.lng || null; } catch(e) { return null; }
        })(),
    };


    try {
        const r = await fetch('{{ route("orders.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const d = await r.json();
        if (d.success) {
            localStorage.setItem('eutCart', JSON.stringify([]));
            // Clear server cart too
            @auth
            fetch('/cart', { method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'} }).catch(()=>{});
            @endauth
            if (d.merged) {
                // Items were added to existing table order — go to tracking
                window.location.href = '{{ route("shop.tracking") }}';
            } else {
                window.location.href = '{{ route("shop.tracking") }}';
            }
        } else {
            alert(d.message || 'Order failed.');
            btn.disabled = false; btn.textContent = 'Place Order';
        }
    } catch (err) {
        console.error(err); alert('Network error.');
        btn.disabled = false; btn.textContent = 'Place Order';
    }
});

// ── Echo: disable checkout if admin closes shop in real time ──
if (window.Echo) {
    window.Echo.channel('shop.status')
        .listen('.shop.status', (data) => {
            const btn = document.getElementById('placeOrderBtn');
            if (!btn) return;
            if (!data.is_open) {
                btn.disabled = true;
                btn.textContent = '🔴 Shop Closed';
                btn.style.background = 'linear-gradient(135deg,#374151,#4b5563)';
                btn.style.opacity = '0.6';
                btn.style.cursor = 'not-allowed';
                btn.type = 'button'; // prevent form submit
            } else {
                btn.disabled = false;
                btn.textContent = 'Place Order';
                btn.style.background = '';
                btn.style.opacity = '';
                btn.style.cursor = '';
                btn.type = 'submit';
            }
        });
}
</script>
@include('partials.pwa-register')

<!-- ── LOCATION PERMISSION GATE ── -->
<div id="locationGate" style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(8,8,16,.97);backdrop-filter:blur(8px);align-items:center;justify-content:center;padding:24px;flex-direction:column;text-align:center;">
    <div style="max-width:320px;width:100%;">
        <div style="width:80px;height:80px;border-radius:50%;background:rgba(239,68,68,.12);border:2px solid rgba(239,68,68,.35);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;animation:pulse-ring 2s ease-in-out infinite;">
            <svg width="36" height="36" fill="none" stroke="#ef4444" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a7 7 0 0 1 7 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 0 1 7-7Z"/><circle cx="12" cy="9" r="2.5" stroke-width="1.75"/></svg>
        </div>
        <h2 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#fff;margin:0 0 10px;">Location Required</h2>
        <p id="locationGateMsg" style="font-size:13px;color:#9ca3af;line-height:1.7;margin:0 0 8px;">We need your location to accurately pin your delivery address on the map.</p>
        <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:12px;padding:12px 16px;margin:16px 0 24px;text-align:left;">
            <p style="font-size:12px;font-weight:700;color:#fbbf24;margin:0 0 8px;">📱 How to enable:</p>
            <p style="font-size:11px;color:#d97706;margin:0 0 4px;line-height:1.6;">• <strong>Chrome:</strong> Tap the lock icon in the address bar → Site settings → Location → Allow</p>
            <p style="font-size:11px;color:#d97706;margin:0 0 4px;line-height:1.6;">• <strong>Safari:</strong> Settings → Safari → Location → Allow</p>
            <p style="font-size:11px;color:#d97706;margin:0;line-height:1.6;">• <strong>Phone:</strong> Settings → Apps → Browser → Permissions → Location → Allow</p>
        </div>
        <button onclick="retryCheckoutGps()" style="width:100%;padding:15px;border-radius:14px;background:linear-gradient(135deg,#dc2626,#ef4444);border:none;color:#fff;font-size:15px;font-weight:800;cursor:pointer;box-shadow:0 4px 20px rgba(220,38,38,.4);display:flex;align-items:center;justify-content:center;gap:8px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2a7 7 0 0 1 7 7c0 5.25-7 13-7 13S5 14.25 5 9a7 7 0 0 1 7-7Z"/><circle cx="12" cy="9" r="2.5"/></svg>
            Try Again
        </button>
        <button onclick="showLocationGate(false)" style="width:100%;margin-top:10px;padding:13px;border-radius:14px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#9ca3af;font-size:14px;font-weight:600;cursor:pointer;">
            Continue Without GPS
        </button>
        <p style="font-size:11px;color:#4b5563;margin:14px 0 0;line-height:1.6;">Location is only used to pin your delivery address.<br>We never track you outside this order.</p>
    </div>
</div>

<style>
@keyframes pulse-ring {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,.3); }
    50%       { box-shadow: 0 0 0 12px rgba(239,68,68,0); }
}
</style>

{{-- ══════════════════════════════════════════════════════════════
     DINE-IN TABLE QR SCANNER MODAL
     Appears when "Place Order" is clicked with Dine-in selected.
     Primary: camera QR scan via BarcodeDetector API
     Fallback: dropdown of tables 1–20
══════════════════════════════════════════════════════════════ --}}
<div id="qrScannerBackdrop" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,.82);backdrop-filter:blur(10px);align-items:flex-end;justify-content:center;"></div>

<div id="qrScannerSheet" style="display:none;position:fixed;bottom:0;left:50%;transform:translateX(-50%) translateY(110%);width:100%;max-width:480px;z-index:9001;background:#0e0f1a;border-radius:28px 28px 0 0;border:1px solid rgba(255,255,255,.1);border-bottom:none;transition:transform .38s cubic-bezier(.32,.72,0,1);max-height:92vh;overflow-y:auto;">
    <style>
        /* ── QR Scanner Sheet ── */
        #qrScannerSheet .qr-handle { width:40px;height:4px;border-radius:99px;background:rgba(255,255,255,.15);margin:14px auto 0; }
        #qrScannerSheet .qr-head { display:flex;align-items:center;justify-content:space-between;padding:16px 20px 10px; }
        #qrScannerSheet .qr-head-title { font-size:17px;font-weight:800;color:#fff; }
        #qrScannerSheet .qr-close-btn { width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#6b7280;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:18px;line-height:1;transition:all .2s; }
        #qrScannerSheet .qr-close-btn:hover { background:rgba(255,255,255,.12);color:#fff; }
        #qrScannerSheet .qr-body { padding:0 20px 32px; }
        /* Camera viewport */
        #qrVideoWrap { position:relative;border-radius:20px;overflow:hidden;background:#000;aspect-ratio:1;max-height:280px;margin-bottom:16px; }
        #qrVideo { width:100%;height:100%;object-fit:cover;display:block; }
        #qrScanLine { position:absolute;left:10%;right:10%;height:2px;background:linear-gradient(90deg,transparent,#facc15,transparent);animation:scanMove 2s ease-in-out infinite;top:50%; }
        @keyframes scanMove { 0%,100%{top:20%}50%{top:80%} }
        #qrCorners { position:absolute;inset:10%;pointer-events:none; }
        #qrCorners::before,#qrCorners::after,#qrCorners > span::before,#qrCorners > span::after {
            content:'';position:absolute;width:22px;height:22px;border-color:#facc15;border-style:solid;
        }
        #qrCorners::before  { top:0;left:0;border-width:3px 0 0 3px;border-radius:4px 0 0 0; }
        #qrCorners::after   { top:0;right:0;border-width:3px 3px 0 0;border-radius:0 4px 0 0; }
        #qrCorners > span::before { bottom:0;left:0;border-width:0 0 3px 3px;border-radius:0 0 0 4px; }
        #qrCorners > span::after  { bottom:0;right:0;border-width:0 3px 3px 0;border-radius:0 0 4px 0; }
        /* Status chip */
        #qrStatus { text-align:center;font-size:13px;color:#6b7280;margin-bottom:16px;min-height:20px; }
        #qrStatus.scanning { color:#facc15; }
        #qrStatus.success  { color:#4ade80; }
        #qrStatus.error    { color:#f87171; }
        /* Detected table chip */
        #qrDetectedChip { display:none;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);border-radius:16px;padding:14px 18px;margin-bottom:16px;text-align:center; }
        #qrDetectedChip .chip-label { font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px; }
        #qrDetectedChip .chip-val { font-size:32px;font-weight:900;color:#4ade80; }
        /* Fallback dropdown */
        #qrFallbackWrap { display:none; }
        #qrFallbackToggle { display:flex;align-items:center;justify-content:center;gap:6px;width:100%;background:none;border:none;color:#6b7280;font-size:13px;font-weight:600;cursor:pointer;padding:8px 0;transition:color .2s;margin-bottom:12px; }
        #qrFallbackToggle:hover { color:#9ca3af; }
        #qrTableSelect { width:100%;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.1);border-radius:14px;padding:13px 16px;font-size:15px;font-weight:700;color:#fff;outline:none;cursor:pointer;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;font-family:'Inter',sans-serif; }
        #qrTableSelect:focus { border-color:rgba(250,204,21,.5); }
        #qrTableSelect option { background:#0e0f1a;color:#fff; }
        /* Confirm button */
        #qrConfirmBtn { width:100%;padding:15px;border-radius:16px;background:linear-gradient(135deg,#f59e0b,#facc15);border:none;color:#000;font-size:15px;font-weight:800;cursor:pointer;transition:all .2s;box-shadow:0 4px 18px rgba(250,204,21,.3);display:flex;align-items:center;justify-content:center;gap:8px;margin-top:12px; }
        #qrConfirmBtn:hover { transform:translateY(-1px);box-shadow:0 6px 24px rgba(250,204,21,.45); }
        #qrConfirmBtn:disabled { opacity:.5;cursor:not-allowed;transform:none; }
    </style>

    <div class="qr-handle"></div>
    <div class="qr-head">
        <span class="qr-head-title">🍽️ Scan Your Table QR</span>
        <button class="qr-close-btn" onclick="closeTableScanner()">×</button>
    </div>
    <div class="qr-body">

        {{-- Camera viewport --}}
        <div id="qrVideoWrap">
            <video id="qrVideo" autoplay playsinline muted></video>
            <div id="qrScanLine"></div>
            <div id="qrCorners"><span></span></div>
        </div>

        {{-- Status text --}}
        <p id="qrStatus" class="scanning">📷 Point camera at the table QR code…</p>

        {{-- Detected table display --}}
        <div id="qrDetectedChip">
            <div class="chip-label">Table detected</div>
            <div class="chip-val" id="qrDetectedVal">—</div>
        </div>

        {{-- Fallback toggle --}}
        <button id="qrFallbackToggle" onclick="toggleQrFallback()">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Can't scan? Pick table manually
        </button>
        <div id="qrFallbackWrap">
            <select id="qrTableSelect" onchange="onDropdownPick(this.value)">
                <option value="">— Select your table —</option>
                @for($t = 1; $t <= 20; $t++)
                <option value="Table {{ $t }}">Table {{ $t }}</option>
                @endfor
            </select>
        </div>

        {{-- Confirm button --}}
        <button id="qrConfirmBtn" disabled onclick="confirmTableAndOrder()">
            Confirm Table & Place Order
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </button>
    </div>
</div>

<script>
/* ── Table QR Scanner ─────────────────────────────────────────────────────── */
let _qrStream     = null;
let _qrInterval   = null;
let _qrCanvas     = null;
let _detectedTable= null;
let _qrOpen       = false;

function openTableScanner() {
    _qrOpen = true;
    _detectedTable = null;
    document.getElementById('qrDetectedChip').style.display = 'none';
    document.getElementById('qrConfirmBtn').disabled = true;
    document.getElementById('qrFallbackWrap').style.display = 'none';
    document.getElementById('qrTableSelect').value = '';
    setQrStatus('scanning', '📷 Point camera at the table QR code…');

    const backdrop = document.getElementById('qrScannerBackdrop');
    const sheet    = document.getElementById('qrScannerSheet');
    backdrop.style.display = 'flex';
    sheet.style.display = 'block';
    document.body.style.overflow = 'hidden';
    requestAnimationFrame(() => { sheet.style.transform = 'translateX(-50%) translateY(0)'; });

    startCamera();
}

function closeTableScanner() {
    _qrOpen = false;
    stopCamera();
    const sheet = document.getElementById('qrScannerSheet');
    sheet.style.transform = 'translateX(-50%) translateY(110%)';
    setTimeout(() => {
        sheet.style.display = 'none';
        document.getElementById('qrScannerBackdrop').style.display = 'none';
    }, 400);
    document.body.style.overflow = '';
}

function setQrStatus(cls, msg) {
    const el = document.getElementById('qrStatus');
    el.className = cls;
    el.textContent = msg;
}

function toggleQrFallback() {
    const wrap = document.getElementById('qrFallbackWrap');
    const visible = wrap.style.display !== 'none';
    wrap.style.display = visible ? 'none' : 'block';
}

function onDropdownPick(val) {
    if (!val) return;
    setDetectedTable(val, false); // from dropdown, no camera needed
}

function setDetectedTable(tableStr, fromQR = true) {
    _detectedTable = tableStr;
    document.getElementById('qrDetectedVal').textContent = tableStr;
    document.getElementById('qrDetectedChip').style.display = 'block';
    document.getElementById('qrConfirmBtn').disabled = false;
    if (fromQR) {
        setQrStatus('success', '✅ QR code scanned successfully!');
        stopCamera();
    }
}

/* ── Camera & BarcodeDetector ─────────────────────────────── */
async function startCamera() {
    try {
        _qrStream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 640 } }
        });
        const video = document.getElementById('qrVideo');
        video.srcObject = _qrStream;
        await video.play();
        startScanning(video);
    } catch(err) {
        setQrStatus('error', '⚠️ Camera not available — use the dropdown below.');
        document.getElementById('qrFallbackWrap').style.display = 'block';
    }
}

function stopCamera() {
    clearInterval(_qrInterval);
    _qrInterval = null;
    if (_qrStream) {
        _qrStream.getTracks().forEach(t => t.stop());
        _qrStream = null;
    }
    const video = document.getElementById('qrVideo');
    video.srcObject = null;
}

function startScanning(video) {
    // Try native BarcodeDetector first (Chrome Android, Edge)
    if (typeof BarcodeDetector !== 'undefined') {
        const detector = new BarcodeDetector({ formats: ['qr_code'] });
        _qrInterval = setInterval(async () => {
            if (!_qrOpen || _detectedTable) return;
            try {
                const codes = await detector.detect(video);
                if (codes.length) {
                    const raw = codes[0].rawValue;
                    const parsed = parseTableFromQr(raw);
                    if (parsed) { setDetectedTable(parsed); }
                    else { setQrStatus('error', '❌ QR unrecognized. Use dropdown below.'); document.getElementById('qrFallbackWrap').style.display='block'; }
                }
            } catch(e) {}
        }, 400);
    } else {
        // BarcodeDetector not supported — fall back to canvas + zxing-like approach
        // We'll just show the dropdown since we can't decode without a lib
        setQrStatus('error', '⚠️ QR scanning not supported on this browser. Use the dropdown.');
        document.getElementById('qrFallbackWrap').style.display = 'block';
    }
}

function parseTableFromQr(raw) {
    if (!raw) return null;
    raw = raw.trim();
    // Formats: "TABLE:5", "Table 5", "5", "T5", etc.
    const m = raw.match(/(?:table[:\s#-]*)?(\d{1,2})/i);
    if (m) return 'Table ' + m[1];
    return null;
}

/* ── Confirm & Resume Order ───────────────────────────────── */
async function confirmTableAndOrder() {
    if (!_detectedTable) return;

    // Inject into hidden field
    document.getElementById('tableNumberInput').value = _detectedTable;

    closeTableScanner();

    // Short delay for sheet close animation, then resume submission
    await new Promise(r => setTimeout(r, 420));

    // Re-run the order flow (now tableNumber will be filled)
    const btn = document.getElementById('placeOrderBtn');
    btn.disabled = true; btn.textContent = 'Placing order…';

    const cart = JSON.parse(localStorage.getItem('eutCart') || '[]');
    const orderType = 'dine_in';
    const tableNumber = _detectedTable;
    const payRaw  = document.querySelector('input[name=payment]:checked')?.value || 'cod';
    const payment = payRaw === 'cod' ? 'cash' : payRaw;
    const notes   = document.getElementById('orderNotes')?.value?.trim() || '';
    const CSRF    = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    const items = cart.map(i => ({
        id: i.id, qty: i.quantity,
        modifiers: (i.modifiers || [])
            .filter(m => m && typeof m === 'object' && m.name)
            .map(m => ({
                type: m.type || 'modifier',
                name: m.name || '',
                price_type: m.price_type || 'none',
                price_adjustment: parseFloat(m.price_adjustment || 0),
            })),
    }));

    const payload = {
        items,
        order_type: orderType,
        delivery_address: 'Dine-in · ' + tableNumber,
        delivery_barangay: '',
        payment_method: payment,
        notes,
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
        const r = await fetch('{{ route("orders.store") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        });
        const d = await r.json();
        if (d.success) {
            localStorage.setItem('eutCart', JSON.stringify([]));
            @auth
            fetch('/cart', { method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'} }).catch(()=>{});
            @endauth
            window.location.href = '{{ route("shop.tracking") }}';
        } else {
            alert(d.message || 'Order failed. Please try again.');
            btn.disabled = false; btn.textContent = 'Place Order';
        }
    } catch(err) {
        alert('Network error. Please try again.');
        btn.disabled = false; btn.textContent = 'Place Order';
    }
}

// Close sheet on backdrop click
document.getElementById('qrScannerBackdrop').addEventListener('click', closeTableScanner);
</script>

</body>
</html>

