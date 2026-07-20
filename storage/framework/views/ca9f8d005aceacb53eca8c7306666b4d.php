<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Dashboard €” EUT</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* ”€”€ TOPNAV ”€”€ */
        .topnav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 9998;
            background: rgba(8,8,16,0.97); backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .topnav-inner { max-width: 540px; margin: 0 auto; padding: 14px 16px; }
        .topnav-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .topnav-brand { display: flex; align-items: center; gap: 8px; }
        .brand-logo { font-family: 'Playfair Display', serif; font-size: 18px; font-weight: 700; color: #fff; }
        .brand-tag { font-size: 9px; font-weight: 700; letter-spacing: .1em; color: #f59e0b; background: rgba(245,158,11,.12); border: 1px solid rgba(245,158,11,.25); border-radius: 4px; padding: 2px 6px; text-transform: uppercase; }

        /* ”€”€ ONLINE TOGGLE ”€”€ */
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

        /* ”€”€ PAGE ”€”€ */
        .page-body { max-width: 540px; margin: 0 auto; padding: 82px 16px 110px; }

        /* ”€”€ RIDER PROFILE CARD ”€”€ */
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

        /* ”€”€ SECTION LABEL ”€”€ */
        .section-label { font-size: 11px; font-weight: 700; color: #4b5563; text-transform: uppercase; letter-spacing: .07em; margin: 0 0 10px; }

        /* ”€”€ CARD ”€”€ */
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

        /* ”€”€ ORDER CARD ”€”€ */
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

        /* ”€”€ BUTTONS ”€”€ */
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

        /* ”€”€ BADGE ”€”€ */
        .badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; }
        .badge-assigned  { background: rgba(245,158,11,.12); color: #f59e0b; border: 1px solid rgba(245,158,11,.25); }
        .badge-pickup    { background: rgba(139,92,246,.12); color: #a78bfa; border: 1px solid rgba(139,92,246,.25); }
        .badge-delivering{ background: rgba(16,185,129,.12); color: #34d399; border: 1px solid rgba(16,185,129,.25); }
        .badge-done      { background: rgba(34,197,94,.10);  color: #4ade80; border: 1px solid rgba(34,197,94,.22); }

        /* ”€”€ PULSE DOT ”€”€ */
        .pulse-dot { width: 7px; height: 7px; background: currentColor; border-radius: 50%; animation: blink 1.2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }

        /* ”€”€ TABS ”€”€ */
        .tabs-bar { display: flex; gap: 0; border-bottom: 1px solid rgba(255,255,255,0.06); margin-bottom: 16px; }
        .tab { padding: 10px 20px; font-size: 13px; font-weight: 600; color: #6b7280; background: none; border: none; border-bottom: 2px solid transparent; cursor: pointer; transition: all .2s; position: relative; bottom: -1px; }
        .tab.active { color: #f59e0b; border-bottom-color: #f59e0b; }

        /* ”€”€ EMPTY STATE ”€”€ */
        .empty-state { text-align: center; padding: 48px 24px; }
        .empty-icon { font-size: 50px; margin-bottom: 14px; opacity: .7; }
        .empty-title { font-size: 16px; font-weight: 700; color: #fff; margin-bottom: 6px; }
        .empty-sub { font-size: 13px; color: #4b5563; line-height: 1.6; }

        /* ”€”€ BOTTOM NAV ”€”€ */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; right: 0;
            background: rgba(8,8,16,0.97); border-top: 1px solid rgba(255,255,255,0.07);
            backdrop-filter: blur(20px); padding: 10px 0 14px; z-index: 9999;
        }
        .bottom-nav-inner { display: flex; max-width: 540px; margin: 0 auto; }
        .bnav-item { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 3px; color: #4b5563; text-decoration: none; font-size: 10px; font-weight: 500; transition: color .15s; }
        .bnav-item.active { color: #f59e0b; }

        /* ”€”€ EARNING CARD ”€”€ */
        .earn-card {
            background: linear-gradient(135deg, #0e1a10, #0a1015);
            border: 1px solid rgba(16,185,129,.2); border-radius: 18px; padding: 16px 18px;
            position: relative; overflow: hidden;
        }
        .earn-card::before { content:''; position:absolute; top:-30px; right:-30px; width:120px; height:120px; background: radial-gradient(circle,rgba(16,185,129,.08) 0%,transparent 70%); pointer-events:none; }

        /* ”€”€ DELIVERY CONFIRM SHEET ”€”€ */
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

        /* ”€”€ STEP VIEWS inside sheet ”€”€ */
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

        /* ”€”€ SUCCESS OVERLAY ”€”€ */
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

<!-- -->
<nav class="topnav">
    <div class="topnav-inner">
        <div class="topnav-row">
            <div class="topnav-brand">
                <span class="brand-logo">EUT</span>
                <span class="brand-tag">Rider</span>
            </div>
            <div class="online-toggle-wrap">
                <span class="online-label" id="onlineLabel" style="color:<?php echo e($rider->is_available ? '#10b981' : '#4b5563'); ?>;">
                    <?php echo e($rider->is_available ? 'Online' : 'Offline'); ?>

                </span>
                <button class="toggle-pill <?php echo e($rider->is_available ? 'is-online' : ''); ?>" id="onlineToggle" onclick="toggleOnline()">
                    <span class="toggle-pill-thumb"></span>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- -->
<div class="page-body">

    <!-- -->
    <div class="profile-card">
        <div class="profile-row">
            <div class="profile-avatar"><?php echo e($rider->initials); ?></div>
            <div>
                <p class="profile-name"><?php echo e($rider->user->name); ?></p>
                <p class="profile-sub">&#x1F3CD;&#xFE0F; <?php echo e(ucfirst($rider->vehicle_type)); ?> &middot; ID: RIDER-<?php echo e(str_pad($rider->id, 3, '0', STR_PAD_LEFT)); ?></p>
            </div>
        </div>
        <div class="profile-stats">
            <div class="pstat"><p class="pstat-val"><?php echo e($todayDeliveries); ?></p><p class="pstat-label">Today</p></div>
            <div class="pstat"><p class="pstat-val" style="color:#10b981;"><?php echo e($rider->total_deliveries); ?></p><p class="pstat-label">All Time</p></div>
            <div class="pstat"><p class="pstat-val">&#11088; <?php echo e(number_format($rider->rating, 1)); ?></p><p class="pstat-label">Rating</p></div>
            <div class="pstat"><p class="pstat-val" style="color:#10b981;">&#8369;<?php echo e(number_format($todayEarnings)); ?></p><p class="pstat-label">Today's Pay</p></div>
        </div>
    </div>

    <!-- -->
    <div class="tabs-bar">
        <button class="tab active" id="tab-active" onclick="switchTab('active')">Active Orders</button>
        <button class="tab" id="tab-history" onclick="switchTab('history')">History</button>
        <button class="tab" id="tab-earnings" onclick="switchTab('earnings')">Earnings</button>
        <button class="tab" id="tab-profile" onclick="switchTab('profile')">Profile</button>
    </div>

    <!-- -->
    <div id="view-active">
        <!-- Live Map -->
        <p class="section-label">&#x1F7E3; Currently Delivering</p>
        <div style="background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(139,92,246,.3);border-radius:18px;overflow:hidden;margin-bottom:12px;">
            <div style="padding:12px 16px 8px;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="font-size:12px;font-weight:700;color:#a78bfa;">📍 Live Map</span>
                    <span style="font-size:10px;color:#4b5563;">
                        <?php
                            $activeOrder = $active->firstWhere('status', 'out_for_delivery') ?? $active->first();
                        ?>
                        <?php if($activeOrder): ?>
                            #<?php echo e($activeOrder->order_number); ?>

                        <?php else: ?>
                            Your Location
                        <?php endif; ?>
                    </span>
                </div>
                <span id="gpsStatusLabel" style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:#10b981;">
                    <span style="width:6px;height:6px;background:#10b981;border-radius:50%;animation:blink 1.2s infinite;"></span>GPS Live
                </span>
            </div>
            <div id="riderMap" style="width:100%;height:220px;"></div>
            <div style="padding:8px 16px 12px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:11px;color:#6b7280;">
                    <?php if($activeOrder && $activeOrder->status === 'out_for_delivery'): ?>
                        🟡 Restaurant → 🏠 Customer
                    <?php elseif($activeOrder && $activeOrder->status === 'rider_assigned'): ?>
                        🟡 Restaurant
                    <?php else: ?>
                        📍 Your Location
                    <?php endif; ?>
                </span>
                <span id="riderDistText" style="font-size:11px;font-weight:700;color:#a78bfa;"></span>
            </div>
        </div>

        <?php $__currentLoopData = $active; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($loop->first && $order->status === 'rider_assigned'): ?>
                <p class="section-label" style="margin-top:0;">🟡 Assigned — Head to Restaurant</p>
            <?php elseif($order->status === 'rider_assigned'): ?>
                <p class="section-label" style="margin-top:20px;">🟡 Assigned — Head to Restaurant</p>
            <?php elseif($loop->first && $order->status === 'out_for_delivery'): ?>
                <!-- no label, map already has it -->
            <?php endif; ?>

            <div class="order-card <?php echo e($order->status === 'out_for_delivery' ? 'active-order' : ''); ?>">
                <div class="oc-header">
                    <div class="oc-id-row">
                        <span class="oc-id">#<?php echo e($order->order_number); ?></span>
                        <span class="badge <?php echo e($order->status === 'out_for_delivery' ? 'badge-delivering' : 'badge-assigned'); ?>">
                            <span class="<?php echo e($order->status === 'out_for_delivery' ? 'pulse-dot' : ''); ?>"></span>
                            <?php echo e($order->status === 'out_for_delivery' ? 'On the Way' : 'Assigned'); ?>

                        </span>
                    </div>
                    <div class="oc-customer">
                        <div class="oc-avatar" style="background:rgba(<?php echo e($order->status === 'out_for_delivery' ? '139,92,246' : '245,158,11'); ?>,.15);color:#<?php echo e($order->status === 'out_for_delivery' ? 'a78bfa' : 'f59e0b'); ?>;">
                            <?php echo e(strtoupper(substr($order->user->name, 0, 1))); ?>

                        </div>
                        <div>
                            <p class="oc-cname"><?php echo e($order->user->name); ?></p>
                            <p class="oc-csub"><?php echo e($order->user->phone ?? 'No phone'); ?></p>
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
                            <p class="oc-addr-val">
                                <?php echo e($order->status === 'out_for_delivery' ? '✅ Picked up at ' . $order->picked_up_at->format('g:i A') : 'EUT Restaurant — Metro Naujan'); ?>

                            </p>
                        </div>
                    </div>
                    <div class="oc-address-row">
                        <div class="oc-addr-icon" style="background:rgba(239,68,68,.1);">
                            <svg width="13" height="13" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="oc-addr-label">Deliver To</p>
                            <p class="oc-addr-val"><?php echo e($order->delivery_address); ?></p>
                        </div>
                    </div>
                </div>
                <div class="oc-items">
                    <?php echo e($order->items->map(fn($i) => $i->item_name . ' × ' . $i->quantity)->implode(' · ')); ?>

                </div>
                <div class="oc-footer">
                    <div>
                        <p class="oc-total-label">Order Total</p>
                        <p class="oc-total">&#8369;<?php echo e(number_format($order->total, 2)); ?></p>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <?php if($order->user->phone): ?>
                            <button class="btn-call" onclick="window.location='tel:<?php echo e($order->user->phone); ?>'" title="Call Customer">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </button>
                        <?php endif; ?>
                        <?php if($order->status === 'rider_assigned'): ?>
                            <form method="POST" action="<?php echo e(route('rider.orders.picked-up', $order)); ?>" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-pickup">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    Picked Up
                                </button>
                            </form>
                        <?php elseif($order->status === 'out_for_delivery'): ?>
                            <button class="btn-delivered" onclick="openDeliverySheet(this)" data-order-id="<?php echo e($order->id); ?>">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Mark as Delivered
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($active->isEmpty()): ?>
            <p class="section-label" style="text-align:center;color:#6b7280;">No active orders right now</p>
        <?php endif; ?>
    </div><!-- /view-active -->

    <!-- -->
    <div id="view-history" style="display:none;">
        <p class="section-label">Completed Today</p>
        <?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:16px;padding:14px 16px;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <span style="font-size:13px;font-weight:800;color:#fff;font-family:monospace;">#<?php echo e($order->order_number); ?></span>
                    <span style="font-size:11px;color:#4b5563;"><?php echo e($order->delivered_at->format('g:i A')); ?></span>
                </div>
                <p style="font-size:12px;font-weight:600;color:#d1d5db;margin:0 0 2px;"><?php echo e($order->user->name); ?></p>
                <p style="font-size:11px;color:#4b5563;margin:0;"><?php echo e($order->items->map(fn($i) => $i->item_name . ' × ' . $i->quantity)->implode(' · ')); ?></p>
            </div>
            <div style="text-align:right;flex-shrink:0;">
                <p style="font-size:16px;font-weight:900;color:#facc15;margin:0 0 3px;">&#8369;<?php echo e(number_format($order->total, 2)); ?></p>
                <p style="font-size:12px;color:#facc15;">⭐</p>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php if($history->isEmpty()): ?>
            <p class="section-label" style="text-align:center;color:#6b7280;">No completed orders today</p>
        <?php endif; ?>
    </div>

    <!-- -->
    <div id="view-earnings" style="display:none;">
        <div class="earn-card" style="margin-bottom:14px;">
            <p style="font-size:11px;color:#4b5563;text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Today's Earnings</p>
            <p style="font-size:2.5rem;font-weight:900;color:#10b981;line-height:1;margin-bottom:4px;">&#8369;840</p>
            <p style="font-size:12px;color:#6b7280;">7 deliveries &middot; Avg. &#8369;120/order</p>
            <div style="display:flex;gap:12px;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,.05);">
                <div style="flex:1;text-align:center;">
                    <p style="font-size:18px;font-weight:800;color:#10b981;">&#8369;4,200</p>
                    <p style="font-size:10px;color:#4b5563;">This Week</p>
                </div>
                <div style="flex:1;text-align:center;">
                    <p style="font-size:18px;font-weight:800;color:#facc15;">&#8369;16,800</p>
                    <p style="font-size:10px;color:#4b5563;">This Month</p>
                </div>
                <div style="flex:1;text-align:center;">
                    <p style="font-size:18px;font-weight:800;color:#a78bfa;">142</p>
                    <p style="font-size:10px;color:#4b5563;">Total Rides</p>
                </div>
            </div>
        </div>
        <p class="section-label">Daily Breakdown</p>
        <?php
        $earningDays = [
            ['day'=>'Fri Jul 18','deliveries'=>7,'earn'=>840],
            ['day'=>'Thu Jul 17','deliveries'=>9,'earn'=>1080],
            ['day'=>'Wed Jul 16','deliveries'=>6,'earn'=>720],
            ['day'=>'Tue Jul 15','deliveries'=>8,'earn'=>960],
            ['day'=>'Mon Jul 14','deliveries'=>5,'earn'=>600],
        ];
        ?>
        <?php $__currentLoopData = $earningDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:14px;margin-bottom:8px;">
            <div>
                <p style="font-size:13px;font-weight:600;color:#e5e7eb;margin:0 0 2px;"><?php echo e($e['day']); ?></p>
                <p style="font-size:11px;color:#4b5563;margin:0;"><?php echo e($e['deliveries']); ?> deliveries</p>
            </div>
            <p style="font-size:18px;font-weight:900;color:#10b981;">&#8369;<?php echo e(number_format($e['earn'])); ?></p>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

</div><!-- /page-body -->

<!-- PROFILE TAB (inside its own page-body so it scrolls correctly) -->
<div id="view-profile" style="display:none;max-width:540px;margin:0 auto;padding:82px 16px 110px;">

    <!-- Profile Card -->
    <div style="background:linear-gradient(135deg,#1a0e00,#100f1a);border:1px solid rgba(245,158,11,.2);border-radius:20px;padding:20px 18px;margin-bottom:14px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-40px;right:-40px;width:160px;height:160px;background:radial-gradient(circle,rgba(245,158,11,.08) 0%,transparent 70%);pointer-events:none;"></div>
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:64px;height:64px;border-radius:50%;background:rgba(245,158,11,.2);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.4rem;color:#f59e0b;border:3px solid rgba(245,158,11,.3);flex-shrink:0;"><?php echo e($rider->initials); ?></div>
            <div>
                <p style="font-size:19px;font-weight:800;color:#fff;margin:0 0 4px;"><?php echo e($rider->user->name); ?></p>
                <p style="font-size:12px;color:#6b7280;margin:0;"><?php echo e($rider->user->email); ?></p>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:12px 14px;">
                <p style="font-size:10px;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px;">Vehicle</p>
                <p style="font-size:14px;font-weight:700;color:#fff;margin:0;">&#x1F3CD; <?php echo e(ucfirst($rider->vehicle_type)); ?></p>
            </div>
            <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:12px 14px;">
                <p style="font-size:10px;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px;">Plate / ID</p>
                <p style="font-size:14px;font-weight:700;color:#fff;margin:0;"><?php echo e($rider->plate_number ?? 'RIDER-' . str_pad($rider->id, 3, '0', STR_PAD_LEFT)); ?></p>
            </div>
            <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:12px 14px;">
                <p style="font-size:10px;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px;">Phone</p>
                <p style="font-size:14px;font-weight:700;color:#fff;margin:0;"><?php echo e($rider->phone ?? '—'); ?></p>
            </div>
            <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:12px 14px;">
                <p style="font-size:10px;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px;">Rating</p>
                <p style="font-size:14px;font-weight:700;color:#facc15;margin:0;">&#11088; <?php echo e(number_format($rider->rating, 1)); ?></p>
            </div>
        </div>
    </div>

    <!-- Career Stats -->
    <div style="background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:18px;padding:16px 18px;margin-bottom:14px;">
        <p style="font-size:11px;color:#4b5563;text-transform:uppercase;letter-spacing:.07em;font-weight:700;margin:0 0 12px;">Career Stats</p>
        <div style="display:flex;border-radius:12px;overflow:hidden;border:1px solid rgba(255,255,255,.07);">
            <div style="flex:1;text-align:center;padding:14px 8px;border-right:1px solid rgba(255,255,255,.07);">
                <p style="font-size:22px;font-weight:900;color:#facc15;margin:0 0 3px;"><?php echo e($rider->total_deliveries); ?></p>
                <p style="font-size:10px;color:#4b5563;margin:0;">Deliveries</p>
            </div>
            <div style="flex:1;text-align:center;padding:14px 8px;border-right:1px solid rgba(255,255,255,.07);">
                <p style="font-size:22px;font-weight:900;color:#10b981;margin:0 0 3px;">&#8369;<?php echo e(number_format($rider->total_deliveries * 120)); ?></p>
                <p style="font-size:10px;color:#4b5563;margin:0;">Total Earned</p>
            </div>
            <div style="flex:1;text-align:center;padding:14px 8px;">
                <p style="font-size:22px;font-weight:900;color:#f59e0b;margin:0 0 3px;">&#11088; <?php echo e(number_format($rider->rating,1)); ?></p>
                <p style="font-size:10px;color:#4b5563;margin:0;">Rating</p>
            </div>
        </div>
    </div>

    <!-- Availability -->
    <div style="background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:18px;padding:16px 18px;margin-bottom:14px;">
        <p style="font-size:11px;color:#4b5563;text-transform:uppercase;letter-spacing:.07em;font-weight:700;margin:0 0 12px;">Availability</p>
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
            <div>
                <p style="font-size:14px;font-weight:700;color:#fff;margin:0 0 3px;" id="profileStatusLabel">
                    <?php echo e($rider->is_available ? 'Online — Ready for orders' : 'Offline — Not accepting orders'); ?>

                </p>
                <p style="font-size:11px;color:#6b7280;margin:0;">Toggle to start or stop receiving deliveries</p>
            </div>
            <button class="toggle-pill <?php echo e($rider->is_available ? 'is-online' : ''); ?>" id="profileOnlineToggle" onclick="toggleOnlineSync()" style="flex-shrink:0;">
                <span class="toggle-pill-thumb"></span>
            </button>
        </div>
    </div>

    <!-- Account Actions -->
    <div style="background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:18px;overflow:hidden;margin-bottom:24px;">
        <a href="<?php echo e(route('shop.home')); ?>" style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.05);text-decoration:none;" onmouseenter="this.style.background='rgba(255,255,255,.04)'" onmouseleave="this.style.background='transparent'">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(99,102,241,.12);display:flex;align-items:center;justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="#818cf8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <span style="font-size:14px;font-weight:600;color:#e5e7eb;">Browse Menu</span>
            <svg width="16" height="16" fill="none" stroke="#4b5563" viewBox="0 0 24 24" style="margin-left:auto;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <form method="POST" action="<?php echo e(route('auth.logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" style="width:100%;display:flex;align-items:center;gap:12px;padding:14px 18px;background:transparent;border:none;cursor:pointer;text-align:left;" onmouseenter="this.style.background='rgba(239,68,68,.06)'" onmouseleave="this.style.background='transparent'">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;">
                    <svg width="16" height="16" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                <span style="font-size:14px;font-weight:600;color:#f87171;">Log Out</span>
            </button>
        </form>
    </div>

</div><!-- /view-profile -->

<!-- -->
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
        <a href="#" class="bnav-item" onclick="switchTab('profile');return false;">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Profile
        </a>
    </div>
</nav>

<!-- -->
<div id="deliverySheet" class="sheet-backdrop" onclick="closeSheetBackdrop(event)">
    <div class="sheet">
        <div class="sheet-handle"></div>
        <div class="sheet-header">
            <p class="sheet-title">Confirm Delivery</p>
            <p class="sheet-sub" id="sheetOrderId">#EUT-00512 &middot; Andrea Macaraeg</p>
        </div>

        <!-- Step 1: Customer present? -->
        <div class="sheet-step active" id="sheet-step-1">
            <p style="font-size:12px;color:#6b7280;margin-bottom:14px;text-transform:uppercase;letter-spacing:.06em;font-weight:600;">Is the customer present?</p>
            <button class="handover-btn present" onclick="sheetStep(2,'present')">
                <div class="handover-icon" style="background:rgba(16,185,129,.12);">&#x1F91D;</div>
                <div>
                    <p class="handover-label">Yes &#x2014; Direct Handover</p>
                    <p class="handover-desc">Customer is here. Hand over the order and confirm.</p>
                </div>
            </button>
            <button class="handover-btn no-contact" onclick="sheetStep(2,'photo')">
                <div class="handover-icon" style="background:rgba(245,158,11,.12);">&#x1F4F7;</div>
                <div>
                    <p class="handover-label">No &#x2014; Take Proof Photo</p>
                    <p class="handover-desc">No one answered. Take a photo of the delivered order as proof.</p>
                </div>
            </button>
            <button class="btn-sheet-back" onclick="closeSheet()">Cancel</button>
        </div>

        <!-- Step 2a: Direct handover -->
        <div class="sheet-step" id="sheet-step-2-present">
            <div style="text-align:center;padding:8px 0 20px;">
                <div style="font-size:52px;margin-bottom:12px;">¤</div>
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
            <button class="btn-sheet-back" onclick="sheetStep(1)">† Back</button>
        </div>

        <!-- Step 2b: Photo proof -->
        <div class="sheet-step" id="sheet-step-2-photo">
            <p style="font-size:11px;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-bottom:10px;">Take or upload a photo</p>
            <div class="photo-upload-area" id="photoUploadArea" onclick="document.getElementById('photoInput').click()">
                <input type="file" id="photoInput" accept="image/*" capture="environment" onchange="handlePhoto(this)">
                <div id="uploadPlaceholder">
                    <div class="upload-icon">&#x1F4F7;</div>
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
            <button class="btn-sheet-back" onclick="sheetStep(1)">† Back</button>
        </div>
    </div>
</div>

<!-- -->
<div id="successOverlay" class="success-overlay">
    <div class="success-ring">
        <svg width="44" height="44" fill="none" stroke="#10b981" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <img id="successPhotoThumb" class="success-photo-thumb" alt="Proof">
    <p class="success-title">Delivered! &#x1F389;</p>
    <p class="success-order" id="successOrderId">#EUT-00512</p>
    <p class="success-sub" id="successSubText">Order handed directly to customer.</p>
    <p style="font-size:12px;color:#374151;margin-bottom:28px;">
        <span style="color:#6b7280;" id="successTime"></span>
    </p>
    <button onclick="dismissSuccess()" style="background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;padding:14px 40px;border-radius:16px;font-size:15px;font-weight:800;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(250,204,21,.3);">
        Done œ“
    </button>
</div>

<script>
/* ”€”€ Online/Offline Toggle ”€”€ */
let isOnline = false;
function toggleOnline() {
    isOnline = !isOnline;
    const pill  = document.getElementById('onlineToggle');
    const label = document.getElementById('onlineLabel');
    pill.classList.toggle('is-online', isOnline);
    label.textContent = isOnline ? 'Online' : 'Offline';
    label.style.color  = isOnline ? '#10b981' : '#4b5563';
}

/* ”€”€ Tabs ”€”€ */
function switchTab(tab) {
    const allTabs = ['active','history','earnings','profile'];
    allTabs.forEach(id => {
        const view = document.getElementById('view-' + id);
        const btn  = document.getElementById('tab-' + id);
        if (view) {
            view.style.display = id === tab ? 'block' : 'none';
        }
        if (btn) {
            btn.classList.toggle('active', id === tab);
        }
    });
    document.querySelectorAll('.bnav-item').forEach((el, i) => {
        const bnavTabs = ['active','history','earnings','profile'];
        el.classList.toggle('active', bnavTabs[i] === tab);
    });
}

/* ”€”€ Mark Picked Up ”€”€ */
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

/* ”€”€ Delivery Sheet ”€”€ */
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

/* ”€”€ Photo handling ”€”€ */
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

/* ”€”€ Confirm Delivered ”€”€ */
function confirmDelivered(type) {
    closeSheet();

    if (_activeCard) {
        const badge = _activeCard.querySelector('.badge');
        badge.className = 'badge badge-done';
        badge.innerHTML = 'œ“ Delivered';
        _activeCard.style.opacity = '.55';
        _activeCard.style.pointerEvents = 'none';
        const actionDiv = _activeCard.querySelector('.oc-footer div:last-child');
        actionDiv.innerHTML = '<span style="font-size:13px;color:#4ade80;font-weight:700;">&#x1F389; Completed!</span>';
    }

    const orderId = _activeCard ? _activeCard.querySelector('.oc-id').textContent : '';
    const now = new Date().toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true});
    const date = new Date().toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});

    document.getElementById('successOrderId').textContent = orderId;
    document.getElementById('successTime').textContent = now + ' · ' + date;
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

<!-- LEAFLET + OSRM RIDER MAP -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- GPS Permission Banner (shown when permission is denied/missing) -->
<div id="gpsBanner" style="display:none;position:fixed;top:62px;left:0;right:0;z-index:9997;background:linear-gradient(135deg,#7c2d12,#92400e);border-bottom:1px solid rgba(245,158,11,.4);padding:10px 16px;text-align:center;">
    <p style="font-size:12px;font-weight:700;color:#fef3c7;margin:0 0 6px;">📍 Location access is needed for delivery tracking</p>
    <p style="font-size:11px;color:#fcd34d;margin:0 0 8px;">Open your browser settings → Site Settings → Location → Allow for this site, then tap below.</p>
    <button onclick="retryGps()" style="background:linear-gradient(135deg,#f59e0b,#facc15);border:none;color:#000;padding:7px 18px;border-radius:99px;font-size:12px;font-weight:800;cursor:pointer;">🔄 Retry Location</button>
</div>

<script>
<?php
$restaurantPos = [13.3213129, 121.3027265];
$riderPos      = [$rider->current_lat ?? 13.3235, $rider->current_lng ?? 121.3050];
$activeOrder   = $active->firstWhere('status', 'out_for_delivery') ?? $active->first();
$customerPos   = null;
$customerName  = null;
$deliveryAddr  = null;
if ($activeOrder) {
    $customerName = $activeOrder->user->name;
    $deliveryAddr = $activeOrder->delivery_address;
    if ($activeOrder->delivery_lat && $activeOrder->delivery_lng) {
        $customerPos = [$activeOrder->delivery_lat, $activeOrder->delivery_lng];
    }
}
$mapOrderStatus = $activeOrder ? $activeOrder->status : '';
?>
const RESTAURANT_R    = [<?= $restaurantPos[0] ?>, <?= $restaurantPos[1] ?>];
const CUSTOMER_R_INIT = <?= $customerPos ? json_encode($customerPos) : 'null' ?>;
const CUSTOMER_NAME   = <?= $customerName ? json_encode($customerName) : '"Customer"' ?>;
const DELIVERY_ADDR   = <?= $deliveryAddr ? json_encode($deliveryAddr) : 'null' ?>;
const ORDER_STATUS_R  = '<?= $mapOrderStatus ?? '' ?>';

let CUSTOMER_R   = CUSTOMER_R_INIT;   // may be filled later via geocode
let myPos        = [<?= $riderPos[0] ?>, <?= $riderPos[1] ?>];
let myMarker     = null;
let riderRouteL  = null;
let riderMapL    = null;
let roadPoints   = [];
let customerMarker = null;

/* ── Geocode delivery address via Nominatim (robust multi-attempt fallback) ── */
async function geocodeAddress(rawAddr) {
    if (!rawAddr) return null;

    // Strip leading "Name, " prefix — delivery_address is stored as "Name, street address"
    // Pattern: if the first comma-separated part has no digits, it's a name
    let addr = rawAddr;
    const parts = rawAddr.split(',');
    if (parts.length > 1 && !/\d/.test(parts[0])) {
        addr = parts.slice(1).join(',').trim();
    }

    // Try progressively broader queries
    const attempts = [
        addr + ', Naujan, Oriental Mindoro, Philippines',
        addr + ', Oriental Mindoro, Philippines',
        addr + ', Philippines',
        // Last resort: just the barangay/area words
        addr.split(',').pop().trim() + ', Naujan, Oriental Mindoro, Philippines',
    ];

    for (const q of attempts) {
        try {
            const res  = await fetch(
                `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(q)}&format=json&limit=1&countrycodes=ph`,
                { headers: { 'Accept-Language': 'en', 'User-Agent': 'EUT-Delivery-App/1.0' } }
            );
            const data = await res.json();
            if (data && data.length) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                // Sanity check — must be somewhere in Philippines (roughly)
                if (lat > 4 && lat < 22 && lng > 116 && lng < 127) {
                    console.log('Geocoded via Nominatim:', q, '->', lat, lng);
                    return [lat, lng];
                }
            }
        } catch(e) { /* try next */ }
        // Nominatim rate limit — small delay between attempts
        await new Promise(r => setTimeout(r, 400));
    }

    // Absolute fallback: use Naujan, Oriental Mindoro area center
    console.warn('Geocode failed for:', rawAddr, '— using area fallback');
    return [13.3100, 121.2950]; // Naujan area center
}

async function fetchOSRMRoute(from, to) {
    const url = `https://router.project-osrm.org/route/v1/driving/${from[1]},${from[0]};${to[1]},${to[0]}?overview=full&geometries=geojson`;
    try {
        const res  = await fetch(url);
        const data = await res.json();
        if (data.code === 'Ok' && data.routes.length)
            return data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
    } catch(e) { console.warn('OSRM error:', e); }
    return null;
}

/* ── Draw / refresh route on map ── */
async function drawRoute() {
    if (!riderMapL || !CUSTOMER_R) return;

    const dest = CUSTOMER_R;

    // Add customer marker if not yet on map
    if (!customerMarker) {
        customerMarker = L.marker(dest, { icon: L.divIcon({
            html: `<div style="background:#ef4444;width:38px;height:38px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #b91c1c;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);"><span style="transform:rotate(45deg);font-size:16px;line-height:1;">&#x1F3E0;</span></div>`,
            className: '', iconSize: [38, 38], iconAnchor: [19, 38],
        }) }).addTo(riderMapL).bindPopup(`<b>${CUSTOMER_NAME}</b>`);
    }

    if (ORDER_STATUS_R === 'out_for_delivery') {
        const route = await fetchOSRMRoute(myPos, dest);
        if (route) {
            roadPoints = route;
            if (riderRouteL) { riderRouteL.setLatLngs(route); }
            else { riderRouteL = L.polyline(route, { color: '#8b5cf6', weight: 5, opacity: 1 }).addTo(riderMapL); }
            riderMapL.fitBounds(riderRouteL.getBounds(), { padding: [40, 40] });
        } else {
            if (!riderRouteL) {
                riderRouteL = L.polyline([myPos, dest], { color: '#8b5cf6', weight: 3, opacity: 0.7, dashArray: '8 5' }).addTo(riderMapL);
            } else {
                riderRouteL.setLatLngs([myPos, dest]);
            }
            riderMapL.fitBounds([myPos, dest], { padding: [44, 44] });
        }
    } else {
        // rider_assigned: show full path rider → restaurant → customer
        const seg1 = await fetchOSRMRoute(myPos, RESTAURANT_R);
        const seg2 = await fetchOSRMRoute(RESTAURANT_R, dest);
        if (seg1 && seg2) {
            roadPoints = [...seg1, ...seg2];
            if (riderRouteL) { riderRouteL.setLatLngs(roadPoints); }
            else { riderRouteL = L.polyline(roadPoints, { color: '#8b5cf6', weight: 5, opacity: 1 }).addTo(riderMapL); }
            riderMapL.fitBounds(riderRouteL.getBounds(), { padding: [40, 40] });
        } else {
            if (!riderRouteL) {
                riderRouteL = L.polyline([myPos, RESTAURANT_R, dest], { color: '#8b5cf6', weight: 3, opacity: 0.7, dashArray: '8 5' }).addTo(riderMapL);
            }
            riderMapL.fitBounds([RESTAURANT_R, dest, myPos], { padding: [44, 44] });
        }
    }
}

async function initRiderMap() {
    const el = document.getElementById('riderMap');
    if (!el || riderMapL) return;

    riderMapL = L.map('riderMap', { zoomControl: true });
    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { attribution: '&copy; Google Maps', maxZoom: 20 }).addTo(riderMapL);
    L.tileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', { attribution: '', maxZoom: 20, opacity: 0.85 }).addTo(riderMapL);

    // Restaurant pin
    L.marker(RESTAURANT_R, { icon: L.divIcon({
        html: `<div style="background:#facc15;width:38px;height:38px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #d97706;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);"><span style="transform:rotate(45deg);font-size:16px;line-height:1;">&#x1F354;</span></div>`,
        className: '', iconSize: [38, 38], iconAnchor: [19, 38],
    }) }).addTo(riderMapL).bindPopup('<b>EUT Restaurant</b>');

    // Rider marker
    myMarker = L.marker(myPos, { icon: L.divIcon({
        html: `<div style="background:#8b5cf6;width:46px;height:46px;border-radius:50%;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:22px;box-shadow:0 0 14px rgba(139,92,246,0.7);">&#x1F6F5;</div>`,
        className: '', iconSize: [46, 46], iconAnchor: [23, 23],
    }) }).addTo(riderMapL).bindPopup('<b>You (Rider)</b>');

    // If no stored coords, try geocoding the address
    if (!CUSTOMER_R && DELIVERY_ADDR) {
        const gps = await geocodeAddress(DELIVERY_ADDR);
        if (gps) {
            CUSTOMER_R = gps;
            // Save the geocoded coords back to the order so future loads are instant
            <?php if($activeOrder): ?>
            fetch('/orders/<?php echo e($activeOrder->id); ?>/set-coords', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
                body: JSON.stringify({ lat: gps[0], lng: gps[1] })
            }).catch(() => {});
            <?php endif; ?>
        }
    }

    if (CUSTOMER_R) {
        await drawRoute();
    } else {
        riderMapL.fitBounds([RESTAURANT_R, myPos], { padding: [44, 44] });
    }
}

function updateRiderDist() {
    const el = document.getElementById('riderDistText');
    if (!el) return;
    if (CUSTOMER_R) {
        const d    = Math.sqrt(Math.pow(CUSTOMER_R[0] - myPos[0], 2) + Math.pow(CUSTOMER_R[1] - myPos[1], 2));
        const dist = Math.round(d * 111000);
        el.textContent = dist > 50 ? `~${Math.max(1, Math.round(dist / 400))} min away` : 'Almost there! 🎉';
    } else {
        el.textContent = '';
    }
}

document.addEventListener('DOMContentLoaded', initRiderMap);

/* ═══════════════════════════════════════════
   GPS — single watcher for ping + map update
═══════════════════════════════════════════ */
function pingLocation(lat, lng) {
    fetch('<?php echo e(route("rider.location")); ?>', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
        body: JSON.stringify({ lat, lng }),
    }).catch(() => {});
}

function setGpsLabel(ok, msg) {
    const el = document.getElementById('gpsStatusLabel');
    if (el) { el.textContent = ok ? '● GPS Live' : '⚠ ' + msg; el.style.color = ok ? '#22c55e' : '#f59e0b'; }
}

function showGpsBanner(show) {
    const b = document.getElementById('gpsBanner');
    if (b) b.style.display = show ? 'block' : 'none';
}

function retryGps() {
    showGpsBanner(false);
    startGpsWatch();
}

let _gpsWatchId    = null;
let _lastPing      = 0;
let _lastRoute     = 0;

function startGpsWatch() {
    if (!navigator.geolocation) {
        setGpsLabel(false, 'GPS not supported');
        showGpsBanner(true);
        return;
    }

    // Clear any existing watcher
    if (_gpsWatchId !== null) navigator.geolocation.clearWatch(_gpsWatchId);

    _gpsWatchId = navigator.geolocation.watchPosition(
        async pos => {
            showGpsBanner(false);
            setGpsLabel(true, '');

            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const now = Date.now();

            // Move rider marker
            myPos = [lat, lng];
            if (myMarker) myMarker.setLatLng(myPos);
            if (riderMapL) riderMapL.panTo(myPos);
            updateRiderDist();

            // Refresh OSRM route — throttled to once per 20 seconds
            if (CUSTOMER_R && now - _lastRoute > 20000) {
                _lastRoute = now;
                const fresh = await fetchOSRMRoute(myPos, CUSTOMER_R);
                if (fresh) {
                    roadPoints = fresh;
                    if (riderRouteL) riderRouteL.setLatLngs(fresh);
                }
            }

            // Ping server — throttled to once per 10 seconds
            if (now - _lastPing >= 10000) {
                _lastPing = now;
                pingLocation(lat, lng);
            }
        },
        err => {
            const msgs = { 1: 'Location denied — tap to enable', 2: 'Position unavailable', 3: 'GPS timeout' };
            setGpsLabel(false, msgs[err.code] || 'GPS error');
            if (err.code === 1) showGpsBanner(true); // permission denied — show banner
        },
        { enableHighAccuracy: true, maximumAge: 0, timeout: 15000 }
    );
}

// Check permission state immediately and start watching
if (navigator.permissions) {
    navigator.permissions.query({ name: 'geolocation' }).then(result => {
        if (result.state === 'denied') {
            setGpsLabel(false, 'Location denied — tap to enable');
            showGpsBanner(true);
        } else {
            startGpsWatch(); // 'granted' or 'prompt' — start watching (will trigger browser prompt if needed)
        }
        result.onchange = () => {
            if (result.state === 'granted') { showGpsBanner(false); startGpsWatch(); }
            if (result.state === 'denied')  { showGpsBanner(true); }
        };
    });
} else {
    startGpsWatch(); // fallback for browsers without Permissions API
}
</script>
</body>
</html>














<?php /**PATH C:\Users\patri\Desktop\EUT_WEB\resources\views/rider/dashboard.blade.php ENDPATH**/ ?>