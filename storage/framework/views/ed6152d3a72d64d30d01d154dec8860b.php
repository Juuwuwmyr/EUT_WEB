<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EUT Restaurant</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
        body{background:#080810;color:#fff;min-height:100vh;}
        .topnav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(8,8,16,.94);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.06);}
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

        /* ── BOTTOM NAV ── */
        .bottom-nav{position:fixed;bottom:0;left:0;right:0;background:rgba(8,8,16,.97);border-top:1px solid rgba(255,255,255,.07);backdrop-filter:blur(20px);padding:10px 0 14px;z-index:100;}
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
<!-- NAVBAR -->
<nav class="topnav">
    <div class="topnav-inner">
        <a href="<?php echo e(route('shop.cart')); ?>" class="back-btn">
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
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(239,68,68,.1);">
                    <svg width="15" height="15" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="card-title">Delivery Address</span>
                <?php if(auth()->guard()->check()): ?>
                <button type="button" onclick="openAddressPicker()" style="font-size:11px;font-weight:700;color:#facc15;background:none;border:none;cursor:pointer;padding:0;">
                    Change
                </button>
                <?php endif; ?>
            </div>

            <?php if(auth()->guard()->check()): ?>
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
            <?php endif; ?>

            <?php if(auth()->guard()->guest()): ?>
            <div class="addr-empty">
                <p class="addr-empty-txt">Log in to use saved addresses</p>
                <a href="<?php echo e(route('restaurant')); ?>" class="btn-add-addr">Log in</a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Payment Method -->
        <div class="card">
            <div class="card-header">
                <div class="card-icon" style="background:rgba(96,165,250,.1);">
                    <svg width="15" height="15" fill="none" stroke="#60a5fa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <span class="card-title">Payment Method</span>
            </div>
            <div class="card-body" style="padding-bottom:6px;">
                <label class="pay-option selected" onclick="selectPay(this)">
                    <input type="radio" name="payment" value="cod" checked>
                    <span class="pay-emoji">💵</span>
                    <div><p class="pay-label">Cash on Delivery</p><p class="pay-sub">Pay when order arrives</p></div>
                </label>
                <label class="pay-option" onclick="selectPay(this)">
                    <input type="radio" name="payment" value="gcash">
                    <span class="pay-emoji">📱</span>
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
                <span style="font-size:14px;">🚀</span>
                <div style="flex:1;">
                    <p style="font-size:11px;color:#4b5563;margin-bottom:4px;" id="freeBarText"></p>
                    <div class="free-bar-track"><div class="free-bar-fill" id="freeBarFill" style="width:0%"></div></div>
                </div>
            </div>

            <div class="items-scroll" id="checkoutItemsList"></div>

            <div style="margin-top:8px;border-top:1px solid rgba(255,255,255,.05);">
                <div class="tot-row"><span class="tot-label">Subtotal</span><span class="tot-val" id="coSubtotal">₱0</span></div>
                <div class="tot-row"><span class="tot-label">Delivery fee</span><span class="tot-val" id="coDelivery">₱50</span></div>
                <div class="tot-row" style="padding-top:12px;padding-bottom:12px;border-top:1px solid rgba(255,255,255,.06);">
                    <span class="tot-grand-label">Total</span>
                    <span class="tot-grand-val" id="coTotal">₱0</span>
                </div>
            </div>

            <?php if(auth()->guard()->guest()): ?>
            <div class="guest-notice">⚠️ Please <a href="<?php echo e(route('restaurant')); ?>">log in</a> to place your order.</div>
            <?php endif; ?>
            <?php if(auth()->guard()->check()): ?>
            <button type="submit" class="place-btn" id="placeOrderBtn">🛒 Place Order</button>
            <?php endif; ?>
        </div>
      </div><!-- /right -->

    </div><!-- /grid -->
  </form>
</div>

<!-- BOTTOM NAV -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="<?php echo e(route('shop.home')); ?>" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>Menu</a>
        <a href="<?php echo e(route('shop.tracking')); ?>" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Orders</a>
        <a href="<?php echo e(route('shop.cart')); ?>" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Cart</a>
        <a href="<?php echo e(route('shop.profile')); ?>" class="bnav-item"><svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Profile</a>
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
    <?php if(auth()->guard()->check()): ?> loadAddresses(); <?php endif; ?>
});

/* ── Helpers ── */
const CSRF = '<?php echo e(csrf_token()); ?>';
const FREE_MIN = 500;
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
        row.innerHTML=`<img src="${esc(item.image||'')}" class="sum-img" alt="${esc(item.name)}" onerror="this.src='<?php echo e(asset('images/hero-burger.jpg')); ?>'">
            <div style="flex:1;min-width:0;"><p class="sum-name">${esc(item.name)}</p><p class="sum-meta">× ${item.quantity} · ₱${Number(item.price).toLocaleString()} each</p>${modChips(item.modifiers)}</div>
            <span class="sum-price">₱${(item.price*item.quantity).toLocaleString()}</span>`;
        el.appendChild(row);sub+=item.price*item.quantity;
    });
    const qty=cart.reduce((s,i)=>s+i.quantity,0);
    const fee=sub>=FREE_MIN?0:50;
    document.getElementById('itemCountBadge').textContent=qty+(qty===1?' item':' items');
    document.getElementById('coSubtotal').textContent='₱'+sub.toLocaleString();
    document.getElementById('coDelivery').innerHTML=fee===0?'<span style="color:#4ade80;font-weight:700;">FREE</span>':'₱50';
    document.getElementById('coTotal').textContent='₱'+(sub+fee).toLocaleString();
    const fb=document.getElementById('freeBar');
    if(sub>0&&sub<FREE_MIN){
        document.getElementById('freeBarText').textContent=`Add ₱${(FREE_MIN-sub).toLocaleString()} more for free delivery`;
        document.getElementById('freeBarFill').style.width=Math.round(sub/FREE_MIN*100)+'%';
        fb.style.display='flex';
    }else{fb.style.display='none';}
}

/* ── Payment highlight ── */
function selectPay(el){document.querySelectorAll('.pay-option').forEach(l=>l.classList.remove('selected'));el.classList.add('selected');}

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
    document.getElementById('fName').value='<?php echo e(auth()->user()?->name ?? ""); ?>';
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

async function saveAddress(){
    const name=document.getElementById('fName').value.trim();
    const phone=document.getElementById('fPhone').value.trim();
    const addr=document.getElementById('fAddress').value.trim();
    const errEl=document.getElementById('formError');
    const btn=document.getElementById('saveAddrBtn');
    errEl.style.display='none';
    if(!name||!phone||!addr){errEl.textContent='Name, phone and address are required.';errEl.style.display='block';return;}
    btn.disabled=true;btn.textContent='Saving…';
    const payload={
        label:activeLabel,recipient_name:name,phone,address:addr,
        barangay:document.getElementById('fBarangay').value.trim(),
        postal:document.getElementById('fPostal').value.trim(),
        is_default:document.getElementById('fDefault').checked,
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

/* ── Place Order ── */
document.getElementById('checkoutForm').addEventListener('submit',async function(e){
    e.preventDefault();
    <?php if(auth()->guard()->guest()): ?> alert('Please log in to place an order.');return; <?php endif; ?>

    const cart=JSON.parse(localStorage.getItem('eutCart')||'[]');
    if(!cart.length){alert('Your cart is empty.');return;}

    const addr=addresses.find(a=>a.id===selectedAddressId);
    if(!addr){alert('Please add a delivery address first.');openAddForm();return;}

    const btn=document.getElementById('placeOrderBtn');
    btn.disabled=true;btn.textContent='⏳ Placing order…';

    const payRaw=document.querySelector('input[name=payment]:checked')?.value||'cod';
    const payment=payRaw==='cod'?'cash':payRaw;
    const notes=document.getElementById('orderNotes')?.value?.trim()||'';
    const deliveryAddress=`${addr.recipient_name}, ${addr.full_address}`;

    const items=cart.map(i=>({
        id:i.id,qty:i.quantity,
        modifiers:(i.modifiers||[]).map(m=>({type:m.type||'modifier',name:m.name||'',price_type:m.price_type||'none',price_adjustment:parseFloat(m.price_adjustment||0)})),
    }));

    try{
        const r=await fetch('<?php echo e(route("orders.store")); ?>',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body:JSON.stringify({items,delivery_address:deliveryAddress,delivery_barangay:addr.barangay||addr.city||'',payment_method:payment,notes}),
        });
        const d=await r.json();
        if(d.success){localStorage.setItem('eutCart',JSON.stringify([]));window.location.href='<?php echo e(route("shop.tracking")); ?>';}
        else{alert(d.message||'Order failed.');btn.disabled=false;btn.textContent='🛒 Place Order';}
    }catch(err){console.error(err);alert('Network error.');btn.disabled=false;btn.textContent='🛒 Place Order';}
});
</script>
</body>
</html>
<?php /**PATH C:\Users\patri\Desktop\EUT_WEB\resources\views/shop/checkout.blade.php ENDPATH**/ ?>