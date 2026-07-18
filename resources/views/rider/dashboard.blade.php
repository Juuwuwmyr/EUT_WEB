<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Dashboard — EUT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* ── TOPNAV ── */
        .topnav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(8,8,16,0.97); backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .topnav-inner { max-width: 540px; margin: 0 auto; padding: 14px 16px; }
        .topnav-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .topnav-brand { display: flex; align-items: center; gap: 8px; }
        .brand-logo { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #fff; }
        .brand-tag { font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #f59e0b; background: rgba(245,158,11,.12); border: 1px solid rgba(245,158,11,.25); border-radius: 4px; padding: 2px 6px; text-transform: uppercase; }

        /* ── ONLINE TOGGLE ── */
        .online-toggle-wrap { display: flex; align-items: center; gap: 8px; }
        .online-label { font-size: 12px; font-weight: 600; transition: color .2s; }
        .toggle-pill {
            width: 44px; height: 24px; border-radius: 99px; border: none; cursor: pointer;
            position: relative; transition: background .3s; background: #1f2937;
        }
        .toggle-pill.is-online { background: #10b981; }
        .toggle-pill-thumb {
            position: absolute; top: 3px; left: 3px; width: 18px; height: 18px;
            border-radius: 50%; background: #fff; transition: transform .3s;
            box-shadow: 0 1px 4px rgba(0,0,0,.3);
        }
        .toggle-pill.is-online .toggle-pill-thumb { transform: translateX(20px); }

        /* ── PAGE ── */
        .page-body { max-width: 540px; margin: 0 auto; padding: 82px 16px 110px; }

        /* ── RIDER PROFILE CARD ── */
        .profile-card {
            background: linear-gradient(135deg, #1a0e00, #100f1a);
            border: 1px solid rgba(245,158,11,0.2); border-radius: 20px;
            padding: 18px; margin-bottom: 14px; position: relative; overflow: hidden;
        }
        .profile-card::before {
            content: ''; position: absolute; top: -40px; right: -40px;
            width: 160px; height: 160px;
            background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        .profile-row { display: flex; align-items: center; gap: 14px; }
        .profile-avatar {
            width: 52px; height: 52px; border-radius: 50%;
            background: rgba(245,158,11,.2); display: flex; align-items: center;
            justify-content: center; font-weight: 900; font-size: 1.1rem; color: #f59e0b;
            border: 2px solid rgba(245,158,11,.3); flex-shrink: 0;
        }
        .profile-name { font-size: 17px; font-weight: 800; color: #fff; margin-bottom: 3px; }
        .profile-sub { font-size: 12px; color: #6b7280; }
        .profile-stats { display: flex; gap: 16px; margin-top: 14px; padding-top: 14px; border-top: 1px solid rgba(255,255,255,0.06); }
        .pstat { text-align: center; flex: 1; }
        .pstat-val { font-size: 20px; font-weight: 900; color: #facc15; }
        .pstat-label { font-size: 10px; color: #4b5563; margin-top: 2px; }

        /* ── SECTION LABEL ── */
        .section-label { font-size: 11px; font-weight: 700; color: #4b5563; text-transform: uppercase; letter-spacing: .07em; margin: 0 0 10px; }

        /* ── CARD ── */
        .card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.07); border-radius: 20px;
            overflow: hidden; margin-bottom: 14px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.4);
        }
        .card-header {
            padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 13px; font-weight: 700; color: #fff; }
        .card-sub   { font-size: 11px; color: #4b5563; margin-top: 2px; }

        /* ── ORDER CARD ── */
        .order-card {
            background: linear-gradient(145deg, #12131f, #0e0f1a);
            border: 1px solid rgba(255,255,255,0.08); border-radius: 18px;
            margin-bottom: 12px; overflow: hidden;
            transition: border-color .2s, transform .15s;
        }
        .order-card:hover { border-color: rgba(245,158,11,.3); transform: translateY(-1px); }
        .order-card.active-order { border-color: rgba(139,92,246,.35); }
        .order-card.active-order:hover { border-color: rgba(139,92,246,.55); }

        .oc-header { padding: 14px 16px 10px; border-bottom: 1px solid rgba(255,255,255,.05); }
        .oc-id-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
        .oc-id { font-size: 14px; font-weight: 800; color: #fff; font-family: monospace; }
        .oc-time { font-size: 11px; color: #4b5563; }

        .oc-customer { display: flex; align-items: center; gap: 10px; }
        .oc-avatar { width: 32px; height: 32px; border-radius: 50%; background: rgba(220,38,38,.15); display: flex; align-items: center; justify-content: center; color: #f87171; font-weight: 800; font-size: .7rem; flex-shrink: 0; }
        .oc-cname { font-size: 13px; font-weight: 600; color: #e5e7eb; }
        .oc-csub  { font-size: 11px; color: #4b5563; margin-top: 2px; }

        .oc-body { padding: 10px 16px; }
        .oc-address-row { display: flex; align-items: flex-start; gap: 8px; margin-bottom: 8px; }
        .oc-addr-icon { width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .oc-addr-label { font-size: 10px; color: #4b5563; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 1px; }
        .oc-addr-val   { font-size: 12px; color: #d1d5db; font-weight: 500; }

        .oc-items { font-size: 11px; color: #6b7280; padding: 8px 16px; border-top: 1px solid rgba(255,255,255,.04); }

        .oc-footer { padding: 10px 16px 14px; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
        .oc-total { font-size: 16px; font-weight: 900; color: #facc15; }
        .oc-total-label { font-size: 10px; color: #4b5563; }

        /* ── BUTTONS ── */
        .btn-accept {
            background: linear-gradient(135deg, #10b981, #059669); color: #fff;
            padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 13px;
            border: none; cursor: pointer; display: flex; align-items: center; gap: 6px;
            box-shadow: 0 4px 14px rgba(16,185,129,.25); transition: all .2s;
        }
        .btn-accept:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16,185,129,.35); }
        .btn-pickup {
            flex: 1; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff;
            padding: 13px; border-radius: 14px; font-weight: 700; font-size: 14px;
            border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px;
            box-shadow: 0 4px 16px rgba(139,92,246,.3); transition: all .2s;
        }
        .btn-pickup:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(139,92,246,.4); }
        .btn-delivered {
            flex: 1; background: linear-gradient(135deg, #10b981, #059669); color: #fff;
            padding: 13px; border-radius: 14px; font-weight: 700; font-size: 14px;
            border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px;
            box-shadow: 0 4px 16px rgba(16,185,129,.3); transition: all .2s;
        }
        .btn-delivered:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16,185,129,.4); }
        .btn-call {
            width: 44px; height: 44px; border-radius: 12px; border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.05); color: #9ca3af; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: all .2s;
        }
        .btn-call:hover { background: rgba(34,197,94,.1); color: #4ade80; border-color: rgba(34,197,94,.3); }
        .btn-decline {
            padding: 10px 16px; border-radius: 12px; border: 1px solid rgba(239,68,68,.25);
            background: transparent; color: #f87171; font-size: 12px; font-weight: 600;
            cursor: pointer; transition: all .2s;
        }
        .btn-decline:hover { background: rgba(239,68,68,.08); }

        /* ── BADGE ── */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
        .badge-assigned  { background: rgba(245,158,11,.12); color: #f59e0b; border: 1px solid rgba(245,158,11,.25); }
        .badge-pickup    { background: rgba(139,92,246,.12); color: #a78bfa; border: 1px solid rgba(139,92,246,.25); }
        .badge-delivering{ background: rgba(16,185,129,.12); color: #34d399; border: 1px solid rgba(16,185,129,.25); }
        .badge-done      { background: rgba(34,197,94,.10);  color: #4ade80; border: 1px solid rgba(34,197,94,.22); }

        /* ── PULSE DOT ── */
        .pulse-dot { width: 7px; height: 7px; background: currentColor; border-radius: 50%; animation: blink 1.2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* ── TABS ── */
        .tabs-bar { display: flex; gap: 0; border-bottom: 1px solid rgba(255,255,255,0.06); margin-bottom: 16px; }
        .tab { padding: 10px 20px; font-size: 13px; font-weight: 600; color: #6b7280; background: none; border: none; border-bottom: 2px solid transparent; cursor: pointer; transition: all .2s; position: relative; bottom: -1px; }
        .tab.active { color: #f59e0b; border-bottom-color: #f59e0b; }

        /* ── EMPTY STATE ── */
        .empty-state { text-align: center; padding: 48px 24px; }
        .empty-icon { font-size: 50px; margin-bottom: 14px; opacity: .7; }
        .empty-title { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .empty-sub { font-size: 13px; color: #4b5563; line-height: 1.6; }

        /* ── BOTTOM NAV ── */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(8,8,16,0.97); border-top: 1px solid rgba(255,255,255,0.07);
            backdrop-filter: blur(20px); padding: 10px 0 14px; z-index: 100;
        }
        .bottom-nav-inner { display: flex; max-width: 540px; margin: 0 auto; }
        .bnav-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; color: #4b5563; text-decoration: none; font-size: 10px; font-weight: 500; transition: color .15s; }
        .bnav-item.active { color: #f59e0b; }

        /* ── EARNING CARD ── */
        .earn-card {
            background: linear-gradient(135deg, #0e1a10, #0a1015);
            border: 1px solid rgba(16,185,129,.2); border-radius: 18px; padding: 16px 18px;
            position: relative; overflow: hidden;
        }
        .earn-card::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background: radial-gradient(circle,rgba(16,185,129,.08) 0%,transparent 70%); pointer-events:none; }

        /* ── DELIVERY CONFIRM SHEET ── */
        .sheet-backdrop {
            display: none; position: fixed; inset: 0; z-index: 200;
            background: rgba(0,0,0,.7); backdrop-filter: blur(6px);
            align-items: flex-end; justify-content: center;
        }
        .sheet-backdrop.open { display: flex; animation: fadeIn .18s ease; }
        @keyframes fadeIn { from{opacity:0} to{opacity:1} }

        .sheet {
            width: 100%; max-width: 540px;
            background: linear-gradient(180deg, #14141f, #0e0f1a);
            border: 1px solid rgba(255,255,255,.09);
            border-bottom: none;
            border-radius: 24px 24px 0 0;
            padding: 0 0 32px;
            animation: slideUp .25s cubic-bezier(.4,0,.2,1);
        }
        @keyframes slideUp { from{transform:translateY(100%)} to{transform:translateY(0)} }

        .sheet-handle {
            width: 40px; height: 4px; background: rgba(255,255,255,.12);
            border-radius: 99px; margin: 12px auto 0;
        }
        .sheet-header {
            padding: 18px 20px 14px;
            border-bottom: 1px solid rgba(255,255,255,.06);
        }
        .sheet-title { font-size: 16px; font-weight: 800; color: #fff; margin: 0 0 3px; }
        .sheet-sub   { font-size: 12px; color: #6b7280; margin: 0; }

        /* ── STEP VIEWS inside sheet ── */
        .sheet-step { padding: 20px; display: none; }
        .sheet-step.active { display: block; }

        /* Handover choice buttons */
        .handover-btn {
            width: 100%; padding: 16px; border-radius: 16px; border: 1px solid;
            background: transparent; cursor: pointer; transition: all .2s;
            display: flex; align-items: center; gap: 14px; margin-bottom: 10px;
            text-align: left;
        }
        .handover-btn:last-child { margin-bottom: 0; }
        .handover-btn.present {
            border-color: rgba(16,185,129,.3);
        }
        .handover-btn.present:hover { background: rgba(16,185,129,.08); border-color: rgba(16,185,129,.6); }
        .handover-btn.no-contact {
            border-color: rgba(245,158,11,.3);
        }
        .handover-btn.no-contact:hover { background: rgba(245,158,11,.08); border-color: rgba(245,158,11,.6); }

        .handover-icon {
            width: 46px; height: 46px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            font-size: 22px;
        }
        .handover-label { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 3px; }
        .handover-desc  { font-size: 12px; color: #6b7280; line-height: 1.4; }

        /* Photo upload area */
        .photo-upload-area {
            border: 2px dashed rgba(245,158,11,.3); border-radius: 18px;
            padding: 32px 20px; text-align: center; cursor: pointer;
            transition: all .2s; position: relative; overflow: hidden;
            background: rgba(245,158,11,.03);
        }
        .photo-upload-area:hover { border-color: rgba(245,158,11,.6); background: rgba(245,158,11,.06); }
        .photo-upload-area.has-photo { border-style: solid; border-color: rgba(16,185,129,.5); background: rgba(16,185,129,.05); padding: 0; }
        .photo-upload-area input[type=file] { display: none; }
        .upload-icon { font-size: 40px; margin-bottom: 10px; }
        .upload-label { font-size: 14px; font-weight: 700; color: #fff; margin-bottom: 4px; }
        .upload-sub   { font-size: 12px; color: #4b5563; }
        #photoPreview { width: 100%; height: 200px; object-fit: cover; border-radius: 16px; display: none; }

        /* Note textarea */
        .delivery-note {
            width: 100%; background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.1);
            border-radius: 12px; padding: 12px 14px; color: #e5e7eb; font-size: 13px;
            resize: none; outline: none; transition: border-color .2s; font-family: 'Inter', sans-serif;
        }
        .delivery-note:focus { border-color: rgba(245,158,11,.5); }
        .delivery-note::placeholder { color: #374151; }

        /* Confirm buttons in sheet */
        .btn-sheet-confirm {
            width: 100%; padding: 16px; border-radius: 16px; border: none; cursor: pointer;
            font-size: 15px; font-weight: 800; display: flex; align-items: center;
            justify-content: center; gap: 8px; transition: all .2s;
        }
        .btn-sheet-confirm.green {
            background: linear-gradient(135deg, #10b981, #059669); color: #fff;
            box-shadow: 0 4px 20px rgba(16,185,129,.35);
        }
        .btn-sheet-confirm.green:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(16,185,129,.45); }
        .btn-sheet-confirm.amber {
            background: linear-gradient(135deg, #f59e0b, #d97706); color: #000;
            box-shadow: 0 4px 20px rgba(245,158,11,.3);
        }
        .btn-sheet-confirm.amber:hover { transform: translateY(-1px); }
        .btn-sheet-back {
            width: 100%; padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,.1);
            background: transparent; color: #6b7280; font-size: 13px; font-weight: 600;
            cursor: pointer; margin-top: 8px; transition: all .2s;
        }
        .btn-sheet-back:hover { color: #9ca3af; border-color: rgba(255,255,255,.2); }

        /* ── SUCCESS OVERLAY ── */
        .success-overlay {
            display: none; position: fixed; inset: 0; z-index: 300;
            background: rgba(8,8,16,.97); align-items: center; justify-content: center;
            flex-direction: column; text-align: center; padding: 32px;
        }
        .success-overlay.show { display: flex; animation: fadeIn .3s ease; }
        .success-ring {
            width: 100px; height: 100px; border-radius: 50%;
            background: rgba(16,185,129,.12); border: 3px solid rgba(16,185,129,.4);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 24px;
            animation: popIn .4s cubic-bezier(.34,1.56,.64,1) .1s both;
        }
        @keyframes popIn { from{transform:scale(0);opacity:0} to{transform:scale(1);opacity:1} }
        .success-title { font-size: 26px; font-weight: 900; color: #fff; margin-bottom: 8px; }
        .success-sub   { font-size: 14px; color: #4b5563; margin-bottom: 6px; line-height: 1.6; }
        .success-order { font-size: 18px; font-weight: 800; color: #facc15; font-family: monospace; margin-bottom: 28px; }
        .success-photo-thumb { width: 120px; height: 80px; border-radius: 12px; object-fit: cover; margin-bottom: 20px; border: 2px solid rgba(16,185,129,.3); display: none; }
    </style>
</head>
<body>

<!-- ── TOPNAV ── -->
<nav class="topnav">
    <div class="topnav-inner">
        <div class="topnav-row">
            <div class="topnav-brand">
                <span class="brand-logo">EUT</span>
                <span class="brand-tag">Rider</span>
            </div>
            <div class="online-toggle-wrap">
                <span class="online-label" id="onlineLabel" style="color:#4b5563;">Offline</span>
                <button class="toggle-pill" id="onlineToggle" onclick="toggleOnline()">
                    <span class="toggle-pill-thumb"></span>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- ── PAGE BODY ── -->
<div class="page-body">

    <!-- ── RIDER PROFILE ── -->
    <div class="profile-card">
        <div class="profile-row">
            <div class="profile-avatar">JD</div>
            <div>
                <p class="profile-name">Juan dela Cruz</p>
                <p class="profile-sub">🏍️ Motorcycle · ID: RIDER-001</p>
            </div>
        </div>
        <div class="profile-stats">
            <div class="pstat"><p class="pstat-val">7</p><p class="pstat-label">Today</p></div>
            <div class="pstat"><p class="pstat-val" style="color:#10b981;">142</p><p class="pstat-label">All Time</p></div>
            <div class="pstat"><p class="pstat-val">⭐ 4.9</p><p class="pstat-label">Rating</p></div>
            <div class="pstat"><p class="pstat-val" style="color:#10b981;">₱840</p><p class="pstat-label">Today's Pay</p></div>
        </div>
    </div>

    <!-- ── TABS ── -->
    <div class="tabs-bar">
        <button class="tab active" id="tab-active" onclick="switchTab('active')">Active Orders</button>
        <button class="tab" id="tab-history" onclick="switchTab('history')">History</button>
        <button class="tab" id="tab-earnings" onclick="switchTab('earnings')">Earnings</button>
    </div>

    <!-- ══════════ ACTIVE ORDERS TAB ══════════ -->
    <div id="view-active">

        <!-- CURRENT DELIVERY (on the way) -->
        <p class="section-label">🟣 Currently Delivering</p>
        <div class="order-card active-order">
            <div class="oc-header">
                <div class="oc-id-row">
                    <span class="oc-id">#EUT-00512</span>
                    <span class="badge badge-delivering"><span class="pulse-dot"></span> On the Way</span>
                </div>
                <div class="oc-customer">
                    <div class="oc-avatar">A</div>
                    <div>
                        <p class="oc-cname">Andrea Macaraeg</p>
                        <p class="oc-csub">09181234567</p>
                    </div>
                </div>
            </div>
            <div class="oc-body">
                <div class="oc-address-row">
                    <div class="oc-addr-icon" style="background:rgba(245,158,11,.1);">
                        <svg width="13" height="13" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="oc-addr-label">Pick Up (Restaurant)</p>
                        <p class="oc-addr-val">✅ Picked up at 5:48 PM</p>
                    </div>
                </div>
                <div class="oc-address-row">
                    <div class="oc-addr-icon" style="background:rgba(239,68,68,.1);">
                        <svg width="13" height="13" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="oc-addr-label">Deliver To</p>
                        <p class="oc-addr-val">123 Sampaguita St., Brgy. Santo Niño, Metro Naujan</p>
                    </div>
                </div>
            </div>
            <div class="oc-items">EUT Classic Burger × 1 · Crispy Fries × 2 · Iced Tea × 1</div>
            <div class="oc-footer">
                <div>
                    <p class="oc-total-label">Order Total</p>
                    <p class="oc-total">₱520</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button class="btn-call" onclick="window.location='tel:09181234567'" title="Call Customer">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </button>
                    <button class="btn-delivered" onclick="openDeliverySheet(this)">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Mark as Delivered
                    </button>
                </div>
            </div>
        </div>

        <!-- ASSIGNED (waiting to pick up) -->
        <p class="section-label" style="margin-top:20px;">🟡 Assigned — Head to Restaurant</p>
        <div class="order-card">
            <div class="oc-header">
                <div class="oc-id-row">
                    <span class="oc-id">#EUT-00518</span>
                    <span class="badge badge-assigned">Assigned</span>
                </div>
                <div class="oc-customer">
                    <div class="oc-avatar" style="background:rgba(245,158,11,.15);color:#f59e0b;">B</div>
                    <div>
                        <p class="oc-cname">Bong Soriano</p>
                        <p class="oc-csub">09271234567</p>
                    </div>
                </div>
            </div>
            <div class="oc-body">
                <div class="oc-address-row">
                    <div class="oc-addr-icon" style="background:rgba(245,158,11,.1);">
                        <svg width="13" height="13" fill="none" stroke="#f59e0b" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="oc-addr-label">Pick Up</p>
                        <p class="oc-addr-val">EUT Restaurant — Metro Naujan</p>
                    </div>
                </div>
                <div class="oc-address-row">
                    <div class="oc-addr-icon" style="background:rgba(239,68,68,.1);">
                        <svg width="13" height="13" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <p class="oc-addr-label">Deliver To</p>
                        <p class="oc-addr-val">456 Rosal Ave., Brgy. Bucayao, Metro Naujan</p>
                    </div>
                </div>
            </div>
            <div class="oc-items">Gourmet Cheeseburger × 1 · Onion Rings × 1</div>
            <div class="oc-footer">
                <div>
                    <p class="oc-total-label">Order Total</p>
                    <p class="oc-total">₱490</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button class="btn-call" onclick="window.location='tel:09271234567'" title="Call Customer">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </button>
                    <button class="btn-pickup" onclick="markPickedUp(this)">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        Picked Up
                    </button>
                </div>
            </div>
        </div>

    </div><!-- /view-active -->

    <!-- ══════════ HISTORY TAB ══════════ -->
    <div id="view-history" style="display:none;">
        <p class="section-label">Completed Today</p>
        @php
        $historyOrders = [
            ['id'=>'EUT-00498','customer'=>'Celia Reyes',  'items'=>'EUT Classic Burger × 1',           'total'=>'₱400','time'=>'5:10 PM','rating'=>5],
            ['id'=>'EUT-00481','customer'=>'Danny Torres', 'items'=>'Crispy Fries × 2 · Iced Tea × 2',  'total'=>'₱340','time'=>'3:45 PM','rating'=>5],
            ['id'=>'EUT-00465','customer'=>'Eva Lim',      'items'=>'Gourmet Burger × 1 · Rings × 1',   'total'=>'₱490','time'=>'1:20 PM','rating'=>4],
            ['id'=>'EUT-00452','customer'=>'Felix Cruz',   'items'=>'Combo Meal × 1',                    'total'=>'₱380','time'=>'12:05 PM','rating'=>5],
        ];
        @endphp
        @foreach($historyOrders as $h)
        <div style="background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:16px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <span style="font-size:13px;font-weight:800;color:#fff;font-family:monospace;">#{{ $h['id'] }}</span>
                    <span style="font-size:11px;color:#4b5563;">{{ $h['time'] }}</span>
                </div>
                <p style="font-size:12px;font-weight:600;color:#d1d5db;margin:0 0 2px;">{{ $h['customer'] }}</p>
                <p style="font-size:11px;color:#4b5563;margin:0;">{{ $h['items'] }}</p>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <p style="font-size:16px;font-weight:900;color:#facc15;margin:0 0 3px;">{{ $h['total'] }}</p>
                <p style="font-size:12px;color:#facc15;">{{ str_repeat('⭐', $h['rating']) }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ══════════ EARNINGS TAB ══════════ -->
    <div id="view-earnings" style="display:none;">
        <div class="earn-card" style="margin-bottom:14px;">
            <p style="font-size:11px;color:#4b5563;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Today's Earnings</p>
            <p style="font-size:2.5rem;font-weight:900;color:#10b981;line-height:1;margin-bottom:4px;">₱840</p>
            <p style="font-size:12px;color:#6b7280;">7 deliveries · Avg. ₱120/order</p>
            <div style="display:flex;gap:12px;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,.05);">
                <div style="flex:1;text-align:center;">
                    <p style="font-size:18px;font-weight:800;color:#10b981;">₱4,200</p>
                    <p style="font-size:10px;color:#4b5563;">This Week</p>
                </div>
                <div style="flex:1;text-align:center;">
                    <p style="font-size:18px;font-weight:800;color:#facc15;">₱16,800</p>
                    <p style="font-size:10px;color:#4b5563;">This Month</p>
                </div>
                <div style="flex:1;text-align:center;">
                    <p style="font-size:18px;font-weight:800;color:#a78bfa;">142</p>
                    <p style="font-size:10px;color:#4b5563;">Total Rides</p>
                </div>
            </div>
        </div>
        <p class="section-label">Daily Breakdown</p>
        @php
        $earningDays = [
            ['day'=>'Fri Jul 18','deliveries'=>7,'earn'=>'₱840'],
            ['day'=>'Thu Jul 17','deliveries'=>9,'earn'=>'₱1,080'],
            ['day'=>'Wed Jul 16','deliveries'=>6,'earn'=>'₱720'],
            ['day'=>'Tue Jul 15','deliveries'=>8,'earn'=>'₱960'],
            ['day'=>'Mon Jul 14','deliveries'=>5,'earn'=>'₱600'],
        ];
        @endphp
        @foreach($earningDays as $e)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:14px;margin-bottom:8px;">
            <div>
                <p style="font-size:13px;font-weight:600;color:#e5e7eb;margin:0 0 2px;">{{ $e['day'] }}</p>
                <p style="font-size:11px;color:#4b5563;margin:0;">{{ $e['deliveries'] }} deliveries</p>
            </div>
            <p style="font-size:18px;font-weight:900;color:#10b981;">{{ $e['earn'] }}</p>
        </div>
        @endforeach
    </div>

</div><!-- /page-body -->

<!-- ── BOTTOM NAV ── -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="#" class="bnav-item active" onclick="switchTab('active');return false;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Orders
        </a>
        <a href="#" class="bnav-item" onclick="switchTab('history');return false;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            History
        </a>
        <a href="#" class="bnav-item" onclick="switchTab('earnings');return false;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Earnings
        </a>
        <a href="{{ route('admin.dashboard') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Admin
        </a>
    </div>
</nav>

<!-- ══════════════════════════════════════
     DELIVERY CONFIRMATION BOTTOM SHEET
══════════════════════════════════════ -->
<div id="deliverySheet" class="sheet-backdrop" onclick="closeSheetBackdrop(event)">
    <div class="sheet">
        <div class="sheet-handle"></div>
        <div class="sheet-header">
            <p class="sheet-title">Confirm Delivery</p>
            <p class="sheet-sub" id="sheetOrderId">#EUT-00512 · Andrea Macaraeg</p>
        </div>

        <!-- Step 1: Customer present? -->
        <div class="sheet-step active" id="sheet-step-1">
            <p style="font-size:12px;color:#6b7280;margin-bottom:14px;text-transform:uppercase;letter-spacing:.06em;font-weight:600;">Is the customer present?</p>
            <button class="handover-btn present" onclick="sheetStep(2,'present')">
                <div class="handover-icon" style="background:rgba(16,185,129,.12);">🤝</div>
                <div>
                    <p class="handover-label">Yes — Direct Handover</p>
                    <p class="handover-desc">Customer is here. Hand over the order and confirm.</p>
                </div>
            </button>
            <button class="handover-btn no-contact" onclick="sheetStep(2,'photo')">
                <div class="handover-icon" style="background:rgba(245,158,11,.12);">📷</div>
                <div>
                    <p class="handover-label">No — Take Proof Photo</p>
                    <p class="handover-desc">No one answered. Take a photo of the delivered order as proof.</p>
                </div>
            </button>
            <button class="btn-sheet-back" onclick="closeSheet()">Cancel</button>
        </div>

        <!-- Step 2a: Direct handover -->
        <div class="sheet-step" id="sheet-step-2-present">
            <div style="text-align:center;padding:8px 0 20px;">
                <div style="font-size:52px;margin-bottom:12px;">🤝</div>
                <p style="font-size:15px;font-weight:800;color:#fff;margin-bottom:6px;">Hand over the order</p>
                <p style="font-size:13px;color:#6b7280;line-height:1.6;">Make sure the customer has all items before confirming.</p>
            </div>
            <div style="margin-bottom:14px;">
                <p style="font-size:11px;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-bottom:8px;">Add a note (optional)</p>
                <textarea class="delivery-note" rows="2" placeholder="e.g. Left at gate, handed to guard..."></textarea>
            </div>
            <button class="btn-sheet-confirm green" onclick="confirmDelivered('present')">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Confirm Delivered
            </button>
            <button class="btn-sheet-back" onclick="sheetStep(1)">← Back</button>
        </div>

        <!-- Step 2b: Photo proof -->
        <div class="sheet-step" id="sheet-step-2-photo">
            <p style="font-size:11px;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-bottom:10px;">Take or upload a photo</p>
            <div class="photo-upload-area" id="photoUploadArea" onclick="document.getElementById('photoInput').click()">
                <input type="file" id="photoInput" accept="image/*" capture="environment" onchange="handlePhoto(this)">
                <div id="uploadPlaceholder">
                    <div class="upload-icon">📸</div>
                    <p class="upload-label">Tap to take photo</p>
                    <p class="upload-sub">Photo of the order at the delivery location</p>
                </div>
                <img id="photoPreview" alt="Proof photo">
            </div>
            <div style="margin-top:12px;margin-bottom:14px;">
                <p style="font-size:11px;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-bottom:8px;">Add a note (optional)</p>
                <textarea class="delivery-note" rows="2" placeholder="e.g. Left at gate, no one answered..."></textarea>
            </div>
            <button class="btn-sheet-confirm amber" id="photoConfirmBtn" onclick="confirmDelivered('photo')" disabled style="opacity:.4;cursor:not-allowed;">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                Submit Proof &amp; Confirm
            </button>
            <button class="btn-sheet-back" onclick="sheetStep(1)">← Back</button>
        </div>
    </div>
</div>

<!-- ══════════════════════════════════════
     SUCCESS OVERLAY
══════════════════════════════════════ -->
<div id="successOverlay" class="success-overlay">
    <div class="success-ring">
        <svg width="44" height="44" fill="none" stroke="#10b981" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <img id="successPhotoThumb" class="success-photo-thumb" alt="Proof">
    <p class="success-title">Delivered! 🎉</p>
    <p class="success-order" id="successOrderId">#EUT-00512</p>
    <p class="success-sub" id="successSubText">Order handed directly to customer.</p>
    <p style="font-size:12px;color:#374151;margin-bottom:28px;">
        <span style="color:#6b7280;" id="successTime"></span>
    </p>
    <button onclick="dismissSuccess()" style="background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;padding:14px 40px;border-radius:16px;font-size:15px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(250,204,21,.3);">
        Done ✓
    </button>
</div>

<script>
/* ── Online/Offline Toggle ── */
let isOnline = false;
function toggleOnline() {
    isOnline = !isOnline;
    const pill  = document.getElementById('onlineToggle');
    const label = document.getElementById('onlineLabel');
    pill.classList.toggle('is-online', isOnline);
    label.textContent = isOnline ? 'Online' : 'Offline';
    label.style.color  = isOnline ? '#10b981' : '#4b5563';
}

/* ── Tabs ── */
function switchTab(tab) {
    ['active','history','earnings'].forEach(id => {
        const view = document.getElementById('view-' + id);
        const btn  = document.getElementById('tab-' + id);
        if (view) view.style.display = id === tab ? 'block' : 'none';
        if (btn)  btn.classList.toggle('active', id === tab);
    });
    document.querySelectorAll('.bnav-item').forEach((el, i) => {
        const tabs = ['active','history','earnings'];
        el.classList.toggle('active', tabs[i] === tab);
    });
}

/* ── Mark Picked Up ── */
function markPickedUp(btn) {
    const card  = btn.closest('.order-card');
    const badge = card.querySelector('.badge');
    badge.className = 'badge badge-pickup';
    badge.innerHTML = '<span class="pulse-dot"></span> Heading to Customer';
    btn.outerHTML = `<button class="btn-delivered" onclick="openDeliverySheet(this)">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Mark as Delivered
    </button>`;
}

/* ── Delivery Sheet ── */
let _activeCard = null;

function openDeliverySheet(btn) {
    _activeCard = btn ? btn.closest('.order-card') : document.querySelector('.order-card.active-order');
    const orderId  = _activeCard ? _activeCard.querySelector('.oc-id').textContent  : '';
    const custName = _activeCard ? _activeCard.querySelector('.oc-cname').textContent : '';
    document.getElementById('sheetOrderId').textContent = orderId + ' · ' + custName;
    sheetStep(1);
    // reset photo state
    document.getElementById('photoInput').value = '';
    document.getElementById('photoPreview').style.display = 'none';
    document.getElementById('uploadPlaceholder').style.display = 'block';
    document.getElementById('photoUploadArea').classList.remove('has-photo');
    const confirmBtn = document.getElementById('photoConfirmBtn');
    confirmBtn.disabled = true;
    confirmBtn.style.opacity = '.4';
    confirmBtn.style.cursor  = 'not-allowed';
    document.getElementById('deliverySheet').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeSheet() {
    document.getElementById('deliverySheet').classList.remove('open');
    document.body.style.overflow = '';
}

function closeSheetBackdrop(e) {
    if (e.target === document.getElementById('deliverySheet')) closeSheet();
}

function sheetStep(step, type) {
    document.querySelectorAll('.sheet-step').forEach(s => s.classList.remove('active'));
    if (step === 1)                          document.getElementById('sheet-step-1').classList.add('active');
    else if (step === 2 && type === 'present') document.getElementById('sheet-step-2-present').classList.add('active');
    else if (step === 2 && type === 'photo')   document.getElementById('sheet-step-2-photo').classList.add('active');
}

/* ── Photo handling ── */
function handlePhoto(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const preview = document.getElementById('photoPreview');
        preview.src = e.target.result;
        preview.style.display = 'block';
        document.getElementById('uploadPlaceholder').style.display = 'none';
        document.getElementById('photoUploadArea').classList.add('has-photo');
        const btn = document.getElementById('photoConfirmBtn');
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor  = 'pointer';
    };
    reader.readAsDataURL(input.files[0]);
}

/* ── Confirm Delivered ── */
function confirmDelivered(type) {
    closeSheet();

    if (_activeCard) {
        const badge = _activeCard.querySelector('.badge');
        badge.className = 'badge badge-done';
        badge.innerHTML = '✓ Delivered';
        _activeCard.style.opacity = '.55';
        _activeCard.style.pointerEvents = 'none';
        const actionDiv = _activeCard.querySelector('.oc-footer div:last-child');
        actionDiv.innerHTML = '<span style="font-size:13px;color:#4ade80;font-weight:700;">🎉 Completed!</span>';
    }

    const orderId = _activeCard ? _activeCard.querySelector('.oc-id').textContent : '';
    const now = new Date().toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true});
    const date = new Date().toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});

    document.getElementById('successOrderId').textContent = orderId;
    document.getElementById('successTime').textContent    = now + ' · ' + date;
    document.getElementById('successSubText').textContent = type === 'photo'
        ? 'Proof photo submitted. Order marked as delivered.'
        : 'Order handed directly to customer.';

    const thumb   = document.getElementById('successPhotoThumb');
    const preview = document.getElementById('photoPreview');
    if (type === 'photo' && preview.src && preview.src !== window.location.href) {
        thumb.src = preview.src;
        thumb.style.display = 'block';
    } else {
        thumb.style.display = 'none';
    }

    document.getElementById('successOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function dismissSuccess() {
    document.getElementById('successOverlay').classList.remove('show');
    document.body.style.overflow = '';
}
</script>
</body>
</html>