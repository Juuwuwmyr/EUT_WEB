<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - EUT Restaurant</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
</head>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
body{background:#080810;color:#fff;min-height:100vh;}
.topnav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(8,8,16,.92);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.06);}
.topnav-inner{max-width:540px;margin:0 auto;padding:14px 16px 0;}
.topnav-row{display:flex;align-items:center;gap:10px;}
.back-btn{width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:#9ca3af;cursor:pointer;text-decoration:none;transition:all .2s;flex-shrink:0;}
.back-btn:hover{background:rgba(255,255,255,.12);color:#fff;}
.topnav-title{font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#fff;flex:1;}
.theme-btn{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;flex-shrink:0;}
.tabs-bar{display:flex;margin-top:14px;border-bottom:1px solid rgba(255,255,255,.06);}
.tab{padding:10px 20px;font-size:13px;font-weight:600;color:#6b7280;background:none;border:none;border-bottom:2px solid transparent;cursor:pointer;transition:all .2s;position:relative;bottom:-1px;}
.tab.active{color:#facc15;border-bottom-color:#facc15;}
.tab-dot{display:inline-block;width:6px;height:6px;background:#ef4444;border-radius:50%;margin-left:5px;vertical-align:middle;animation:blink 1.5s ease-in-out infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.3}}
.page-body{max-width:540px;margin:0 auto;padding:130px 16px 110px;}
.card{background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:20px;overflow:hidden;margin-bottom:14px;box-shadow:0 4px 24px rgba(0,0,0,.4);transition:border-color .25s;}
.card:hover{border-color:rgba(250,204,21,.2);}
.card-header{padding:16px 18px;border-bottom:1px solid rgba(255,255,255,.05);display:flex;align-items:center;justify-content:space-between;}
.card-title{font-size:13px;font-weight:700;color:#fff;}
.card-sub{font-size:11px;color:#4b5563;margin-top:2px;}
.badge{display:inline-flex;align-items:center;gap:5px;padding:5px 11px;border-radius:99px;font-size:11px;font-weight:700;}
.badge-live{background:rgba(239,68,68,.12);color:#f87171;border:1px solid rgba(239,68,68,.25);}
.badge-done{background:rgba(34,197,94,.1);color:#4ade80;border:1px solid rgba(34,197,94,.22);}
.badge-cancelled{background:rgba(239,68,68,.08);color:#f87171;border:1px solid rgba(239,68,68,.18);}
.badge-pulse{width:7px;height:7px;background:#ef4444;border-radius:50%;animation:blink 1.2s infinite;}
.progress-eta-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;}
.progress-eta-label{font-size:11px;color:#6b7280;}
.progress-eta-time{font-size:13px;font-weight:700;color:#facc15;}
.progress-track{height:6px;background:#1a1b2e;border-radius:99px;overflow:hidden;position:relative;}
.progress-fill{height:100%;border-radius:99px;background:linear-gradient(90deg,#dc2626,#f59e0b,#facc15);transition:width 1.2s cubic-bezier(.4,0,.2,1);}
.progress-steps{display:flex;justify-content:space-between;margin-top:8px;}
.progress-step-label{font-size:10px;color:#374151;}
.progress-step-label.done{color:#facc15;}
.progress-step-label.active{color:#ef4444;}
.item-row{display:flex;align-items:center;gap:12px;padding:13px 18px;border-bottom:1px solid rgba(255,255,255,.04);}
.item-row:last-child{border-bottom:none;}
.item-img{width:50px;height:50px;border-radius:12px;object-fit:cover;flex-shrink:0;}
.item-name{font-size:13px;font-weight:600;color:#e5e7eb;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.item-qty{font-size:11px;color:#4b5563;margin-top:3px;}
.item-price{font-size:13px;font-weight:700;color:#facc15;flex-shrink:0;margin-left:auto;}
.totals{padding:14px 18px;border-top:1px solid rgba(255,255,255,.05);}
.total-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;}
.total-row:last-child{margin-bottom:0;}
.total-label{font-size:12px;color:#6b7280;}
.total-value{font-size:12px;color:#9ca3af;}
.total-grand-label{font-size:14px;font-weight:700;color:#fff;}
.total-grand-value{font-size:16px;font-weight:800;color:#facc15;}
.total-divider{border:none;border-top:1px solid rgba(255,255,255,.05);margin:10px 0;}
.info-row{display:flex;align-items:flex-start;gap:12px;padding:12px 18px;border-bottom:1px solid rgba(255,255,255,.04);}
.info-row:last-child{border-bottom:none;}
.info-icon{width:34px;height:34px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.info-text-label{font-size:10px;color:#4b5563;margin-bottom:2px;text-transform:uppercase;letter-spacing:.05em;}
.info-text-val{font-size:13px;color:#e5e7eb;font-weight:500;}
.info-text-sub{font-size:11px;color:#6b7280;margin-top:1px;}
.pcard{background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:20px;overflow:hidden;margin-bottom:14px;box-shadow:0 4px 24px rgba(0,0,0,.4);transition:border-color .2s,transform .15s;}
.pcard:hover{border-color:rgba(250,204,21,.18);transform:translateY(-1px);}
.pcard-cancelled{border-color:rgba(239,68,68,.12)!important;}
.pcard-header{padding:14px 18px 12px;border-bottom:1px solid rgba(255,255,255,.05);}
.pcard-id-row{display:flex;justify-content:space-between;align-items:center;}
.pcard-id{font-size:13px;font-weight:700;color:#fff;}
.pcard-date{font-size:11px;color:#4b5563;margin-top:3px;}
.pcard-items{padding:12px 18px;border-bottom:1px solid rgba(255,255,255,.04);}
.pcard-item-row{display:flex;align-items:center;gap:10px;margin-bottom:8px;}
.pcard-item-row:last-child{margin-bottom:0;}
.pcard-item-img{width:40px;height:40px;border-radius:10px;object-fit:cover;flex-shrink:0;}
.pcard-item-name{font-size:12px;color:#d1d5db;font-weight:500;flex:1;}
.pcard-item-price{font-size:12px;font-weight:700;color:#9ca3af;}
.pcard-footer{padding:12px 18px;display:flex;justify-content:space-between;align-items:center;}
.pcard-total{font-size:16px;font-weight:800;color:#facc15;}
.pcard-total-label{font-size:10px;color:#4b5563;}
.pcard-cancelled .pcard-total{color:#6b7280!important;text-decoration:line-through;}
.cancel-reason{margin:0 18px 14px;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.15);border-radius:10px;padding:10px 14px;display:flex;align-items:flex-start;gap:8px;}
.cancel-reason-icon{font-size:14px;flex-shrink:0;margin-top:1px;}
.cancel-reason-text{font-size:11px;color:#9ca3af;line-height:1.5;}
.cancel-reason-title{font-size:12px;font-weight:600;color:#f87171;margin-bottom:2px;}
.btn-primary{background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;padding:14px;border-radius:14px;font-weight:700;font-size:14px;text-align:center;text-decoration:none;display:block;box-shadow:0 4px 16px rgba(250,204,21,.25);transition:all .2s;}
.btn-ghost{background:rgba(255,255,255,.04);color:#9ca3af;padding:14px;border-radius:14px;font-weight:600;font-size:14px;text-align:center;text-decoration:none;display:block;border:1px solid rgba(255,255,255,.08);transition:all .2s;}
.btn-reorder{background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;padding:8px 18px;border-radius:99px;font-size:12px;font-weight:700;text-decoration:none;display:inline-block;transition:all .2s;box-shadow:0 2px 10px rgba(250,204,21,.2);}
.empty-state{text-align:center;padding:64px 24px;}
.empty-icon{font-size:60px;margin-bottom:18px;opacity:.7;}
.empty-title{font-size:18px;font-weight:700;color:#fff;margin-bottom:8px;}
.empty-sub{font-size:13px;color:#4b5563;margin-bottom:24px;line-height:1.6;}
.bottom-nav{position:fixed;bottom:0;left:0;right:0;background:rgba(8,8,16,.97);border-top:1px solid rgba(255,255,255,.07);backdrop-filter:blur(20px);padding:10px 0 14px;z-index:100;}
@media(min-width:1024px){.bottom-nav{display:none;}}
.bottom-nav-inner{display:flex;}
.bnav-item{flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:#4b5563;text-decoration:none;font-size:10px;font-weight:500;transition:color .15s;}
.bnav-item.active{color:#facc15;}
.light-mode body{background:#f0f0f8!important;color:#111!important;}
.light-mode .topnav{background:rgba(255,255,255,.95)!important;border-color:rgba(0,0,0,.07)!important;}
.light-mode .card,.light-mode .pcard{background:#fff!important;border-color:rgba(0,0,0,.07)!important;}
.light-mode .card-title,.light-mode .pcard-id,.light-mode .item-name,.light-mode .info-text-val,.light-mode .total-grand-label{color:#111!important;}
.light-mode .back-btn,.light-mode .theme-btn{background:rgba(0,0,0,.05)!important;border-color:rgba(0,0,0,.08)!important;color:#555!important;}
</style>
<body>

<!-- NAVBAR -->
<nav class="topnav">
    <div class="topnav-inner">
        <div class="topnav-row">
            <a href="<?php echo e(route('shop.home')); ?>" class="back-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <span class="topnav-title">My Orders</span>
            <button id="shopThemeToggle" class="theme-btn">
                <svg id="shopSunIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#facc15;display:none;">
                    <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <svg id="shopMoonIcon" width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="color:#9ca3af;">
                    <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                </svg>
            </button>
        </div>
        <div class="tabs-bar">
            <button class="tab active" id="tab-active" onclick="switchTab('active')">
                Active <span class="tab-dot" id="activeDot"></span>
            </button>
            <button class="tab" id="tab-past" onclick="switchTab('past')">Past Orders</button>
            <button class="tab" id="tab-cancelled" onclick="switchTab('cancelled')">Cancelled</button>
        </div>
    </div>
</nav>

<!-- PAGE BODY -->
<div class="page-body">
    <!-- ACTIVE VIEW -->
    <div id="view-active">
        <div class="empty-state">
            <div class="empty-icon">⏳</div>
            <p class="empty-title">Loading orders…</p>
        </div>
    </div>

    <!-- PAST ORDERS VIEW -->
    <div id="view-past" style="display:none;">
        <div id="pastOrdersList"></div>
        <div class="empty-state" id="pastEmpty" style="display:none;">
            <div class="empty-icon">📦</div>
            <p class="empty-title">No completed orders yet</p>
            <p class="empty-sub">Your delivered orders will appear here.</p>
            <a href="<?php echo e(route('shop.home')); ?>" class="btn-primary" style="display:inline-block;width:auto;padding:12px 28px;border-radius:99px;">Start Ordering</a>
        </div>
    </div>

    <!-- CANCELLED VIEW -->
    <div id="view-cancelled" style="display:none;">
        <div id="cancelledOrdersList"></div>
        <div class="empty-state" id="cancelledEmpty" style="display:none;">
            <div class="empty-icon">✅</div>
            <p class="empty-title">No cancelled orders</p>
            <p class="empty-sub">Great news — you haven't had to cancel any orders.</p>
            <a href="<?php echo e(route('shop.home')); ?>" class="btn-primary" style="display:inline-block;width:auto;padding:12px 28px;border-radius:99px;">Browse Menu</a>
        </div>
    </div>
</div>

<!-- CANCEL MODAL -->
<div id="cancelModalBackdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:1000;backdrop-filter:blur(4px);" onclick="closeCancelModal()"></div>
<div id="cancelModal" style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:1001;background:linear-gradient(145deg,#1a0a0a,#120808);border:1px solid rgba(239,68,68,.25);border-radius:24px 24px 0 0;padding:24px 20px 40px;max-width:540px;margin:0 auto;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <div>
            <p style="font-size:17px;font-weight:800;color:#fff;">Cancel Order</p>
            <p style="font-size:12px;color:#6b7280;margin-top:2px;">Please tell us why you're cancelling</p>
        </div>
        <button onclick="closeCancelModal()" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);color:#9ca3af;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;">✕</button>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px;" id="cancelReasons">
        <?php $__currentLoopData = ['Changed my mind','Ordered by mistake','Found a better option','Taking too long','Other reason']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <label style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;border:1.5px solid rgba(255,255,255,.07);cursor:pointer;transition:all .2s;">
            <input type="radio" name="cancelReason" value="<?php echo e($reason); ?>" style="accent-color:#ef4444;width:16px;height:16px;"
                   onchange="document.querySelectorAll('#cancelReasons label').forEach(l=>l.style.borderColor='rgba(255,255,255,.07)');this.closest('label').style.borderColor='rgba(239,68,68,.5)';">
            <span style="font-size:13px;color:#d1d5db;font-weight:500;"><?php echo e($reason); ?></span>
        </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div id="cancelModalError" style="display:none;color:#f87171;font-size:12px;margin-bottom:10px;padding:8px 12px;background:rgba(239,68,68,.08);border-radius:8px;"></div>
    <button id="confirmCancelBtn" onclick="submitCancel()" style="width:100%;padding:14px;border-radius:14px;background:linear-gradient(135deg,#ef4444,#dc2626);border:none;color:#fff;font-size:15px;font-weight:800;cursor:pointer;box-shadow:0 4px 20px rgba(239,68,68,.3);">Yes, Cancel My Order</button>
    <button onclick="closeCancelModal()" style="width:100%;padding:12px;margin-top:8px;border-radius:14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);color:#9ca3af;font-size:14px;font-weight:600;cursor:pointer;">Never Mind</button>
</div>

<!-- DEBUG POPUP -->
<div id="dbg-overlay" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.85);backdrop-filter:blur(6px);align-items:flex-end;justify-content:center;">
    <div style="width:100%;max-width:540px;background:#0f0f1a;border:1px solid rgba(239,68,68,.35);border-radius:20px 20px 0 0;padding:20px;max-height:80vh;display:flex;flex-direction:column;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <span style="font-size:13px;font-weight:800;color:#f87171;">🐛 Debug Info</span>
            <button onclick="closeDebugPopup()" style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);color:#9ca3af;padding:4px 12px;border-radius:8px;font-size:12px;cursor:pointer;">Close</button>
        </div>
        <p id="dbg-title" style="font-size:14px;font-weight:700;color:#fbbf24;margin-bottom:10px;"></p>
        <pre id="dbg-body" style="font-size:11px;color:#d1d5db;white-space:pre-wrap;word-break:break-all;overflow-y:auto;flex:1;background:#080810;padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,.06);line-height:1.6;"></pre>
    </div>
</div>

<!-- DEBUG TRIGGER BUTTON (fixed, bottom-right) -->
<button onclick="showDebugPopup('Manual Debug','No error captured yet — trigger an action first.')" style="position:fixed;bottom:80px;right:16px;z-index:500;width:38px;height:38px;border-radius:50%;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.4);color:#f87171;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;" title="Debug log">🐛</button>

<!-- BOTTOM NAV -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="<?php echo e(route('shop.home')); ?>" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Home
        </a>
        <a href="<?php echo e(route('shop.tracking')); ?>" class="bnav-item active">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Orders
        </a>
        <a href="<?php echo e(route('shop.cart')); ?>" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Cart
        </a>
        <a href="<?php echo e(route('shop.profile')); ?>" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
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

/* ── Tab switching ── */
function switchTab(tab) {
    ['active','past','cancelled'].forEach(id => {
        document.getElementById('view-' + id).style.display = id === tab ? 'block' : 'none';
        document.getElementById('tab-' + id).classList.toggle('active', id === tab);
    });
}

/* ── Cancel Modal ── */
let currentCancelOrderId = null;

function openCancelModal(orderId) {
    currentCancelOrderId = orderId;
    document.getElementById('cancelModalBackdrop').style.display = 'block';
    document.getElementById('cancelModal').style.display = 'block';
    document.getElementById('cancelModalError').style.display = 'none';
    document.querySelectorAll('input[name="cancelReason"]').forEach(r => r.checked = false);
    document.querySelectorAll('#cancelReasons label').forEach(l => l.style.borderColor = 'rgba(255,255,255,.07)');
}

function closeCancelModal() {
    document.getElementById('cancelModalBackdrop').style.display = 'none';
    document.getElementById('cancelModal').style.display = 'none';
    currentCancelOrderId = null;
}

async function submitCancel() {
    const selected = document.querySelector('input[name="cancelReason"]:checked');
    const errEl = document.getElementById('cancelModalError');
    const btn   = document.getElementById('confirmCancelBtn');
    if (!selected) { errEl.textContent = '⚠ Please select a reason.'; errEl.style.display = 'block'; return; }
    if (!currentCancelOrderId) { errEl.textContent = 'No order selected.'; errEl.style.display = 'block'; return; }
    errEl.style.display = 'none';
    btn.textContent = 'Cancelling…';
    btn.disabled = true;
    try {
        const res  = await fetch(`/orders/${currentCancelOrderId}/cancel`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
            body: JSON.stringify({ reason: selected.value })
        });
        const data = await res.json();
        if (data.success) {
            closeCancelModal();
            await loadAllOrders();
            switchTab('cancelled');
            showToast('✅ Order cancelled.');
        } else {
            errEl.textContent = data.message || 'Failed.';
            errEl.style.display = 'block';
            btn.textContent = 'Yes, Cancel My Order';
            btn.disabled = false;
        }
    } catch (e) {
        errEl.textContent = 'Network error. Please try again.';
        errEl.style.display = 'block';
        btn.textContent = 'Yes, Cancel My Order';
        btn.disabled = false;
    }
}

function showToast(msg) {
    const t = document.createElement('div');
    t.textContent = msg;
    Object.assign(t.style, {
        position:'fixed', bottom:'90px', left:'50%', transform:'translateX(-50%)',
        background:'#0d1f17', border:'1px solid rgba(74,222,128,.3)',
        color:'#4ade80', padding:'12px 22px', borderRadius:'99px',
        fontSize:'13px', fontWeight:'700', zIndex:'9999',
    });
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 2500);
}

/* ── Debug Popup ── */
function showDebugPopup(title, lines) {
    document.getElementById('dbg-overlay').style.display = 'flex';
    document.getElementById('dbg-title').textContent = title;
    document.getElementById('dbg-body').textContent = lines.join('\n');
}
function closeDebugPopup() {
    document.getElementById('dbg-overlay').style.display = 'none';
}

/* ── Load All Orders from API ── */
async function loadAllOrders() {
    try {
        const res = await fetch('/orders', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
        });

        const rawText = await res.text();

        if (!res.ok) {
            const preview = rawText.substring(0, 300);
            showDebugPopup(
                'HTTP ' + res.status + ' — ' + res.statusText,
                [
                    'URL: /orders',
                    'Status: ' + res.status + ' ' + res.statusText,
                    'Content-Type: ' + (res.headers.get('content-type') || 'unknown'),
                    '',
                    'Response preview:',
                    preview + (rawText.length > 300 ? '…' : '')
                ]
            );
            document.getElementById('view-active').innerHTML =
                '<div class="empty-state">' +
                    '<div class="empty-icon">⚠️</div>' +
                    '<p class="empty-title">Could not load orders</p>' +
                    '<p class="empty-sub">HTTP ' + res.status + ' — tap the 🐛 button for details.</p>' +
                '</div>';
            return;
        }

        let data;
        try {
            data = JSON.parse(rawText);
        } catch (parseErr) {
            const preview = rawText.substring(0, 300);
            showDebugPopup(
                'JSON Parse Error',
                [
                    'The server returned non-JSON (probably the login page HTML).',
                    'This usually means your session expired — try logging out and back in.',
                    '',
                    'Parse error: ' + parseErr.message,
                    '',
                    'Response preview:',
                    preview + (rawText.length > 300 ? '…' : '')
                ]
            );
            document.getElementById('view-active').innerHTML =
                '<div class="empty-state">' +
                    '<div class="empty-icon">⚠️</div>' +
                    '<p class="empty-title">Session expired</p>' +
                    '<p class="empty-sub">Please log out and log back in. Tap 🐛 for details.</p>' +
                '</div>';
            return;
        }

        renderActiveOrders(data.active || []);
        renderPastOrders(data.past || []);
        renderCancelledOrders(data.cancelled || []);
        const dot = document.getElementById('activeDot');
        if (dot) dot.style.display = (data.active || []).length ? 'inline-block' : 'none';

        // Update live rider positions on existing maps
        (data.active || []).forEach(o => {
            if (o.rider && o.rider.lat && o.rider.lng && activeMaps[o.id]) {
                updateMapRiderPos(o.id, o.rider.lat, o.rider.lng);
            }
        });
    } catch (e) {
        console.error('Failed to load orders:', e);
        showDebugPopup(
            'Network / Fetch Error',
            [
                e.name + ': ' + e.message,
                '',
                'Check that the Laravel dev server is running on 127.0.0.1:8000'
            ]
        );
        document.getElementById('view-active').innerHTML =
            '<div class="empty-state">' +
                '<div class="empty-icon">⚠️</div>' +
                '<p class="empty-title">Could not load orders</p>' +
                '<p class="empty-sub">Network error — tap 🐛 for details.</p>' +
            '</div>';
    }
}

/* ── Status config ── */
const STATUS_MAP = {
    pending:          { label: 'Order Placed',      progress: 10 },
    accepted:         { label: 'Accepted',           progress: 20 },
    preparing:        { label: 'Preparing',          progress: 40 },
    rider_assigned:   { label: 'Rider Assigned',     progress: 60 },
    out_for_delivery: { label: 'Out for Delivery',   progress: 80 },
    delivered:        { label: 'Delivered',          progress: 100 },
};

/* ── Modifier tags HTML ── */
function modifierTagsHtml(modifiers) {
    if (!modifiers || !modifiers.length) return '';
    const typeColors = {
        flavor:   { bg:'rgba(59,130,246,.12)',  color:'#3b82f6', icon:'🌶' },
        modifier: { bg:'rgba(139,92,246,.12)',  color:'#8b5cf6', icon:'⚙' },
        addon:    { bg:'rgba(245,158,11,.12)',  color:'#d97706', icon:'➕' },
    };
    const tags = modifiers
        .filter(m => !/^no\s/i.test(m.name))
        .map(m => {
            const tc  = typeColors[m.type] || typeColors.modifier;
            const adj = parseFloat(m.price_adjustment || 0);
            const extra = (m.price_type === 'add' && adj > 0)
                ? ` <span style="color:#4ade80;font-size:.6rem;">+₱${adj.toLocaleString()}</span>`
                : '';
            return `<span style="display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .55rem;border-radius:99px;font-size:.68rem;font-weight:600;background:${tc.bg};color:${tc.color};border:1px solid ${tc.color}30;">${tc.icon} ${m.name}${extra}</span>`;
        });
    if (!tags.length) return '';
    return `<div style="display:flex;flex-wrap:wrap;gap:4px;margin-top:6px;">${tags.join('')}</div>`;
}

/* ── Render Active Orders ── */
function renderActiveOrders(orders) {
    const view = document.getElementById('view-active');
    if (!orders.length) {
        // Don't re-render if the empty state + demo map is already showing
        if (document.getElementById('trackingMap-demo')) return;

        view.innerHTML =
            '<div class="empty-state">' +
                '<div class="empty-icon">📦</div>' +
                '<p class="empty-title">No active orders</p>' +
                '<p class="empty-sub">Place an order and track it here in real-time.</p>' +
                '<a href="<?php echo e(route('shop.home')); ?>" class="btn-primary" style="display:inline-block;width:auto;padding:12px 28px;border-radius:99px;">Browse Menu</a>' +
            '</div>' +
            // TODO: REMOVE this demo map block when real orders are in use
            '<div style="margin-top:8px;">' +
                '<div style="padding:14px 18px 10px;display:flex;justify-content:space-between;align-items:center;">' +
                    '<div>' +
                        '<p style="font-size:13px;font-weight:700;color:#fff;">🛵 Demo Live Tracking</p>' +
                        '<p style="font-size:11px;color:#4b5563;margin-top:2px;" id="riderEtaText-demo">Fetching route…</p>' +
                    '</div>' +
                    '<span style="font-size:10px;background:rgba(250,204,21,.1);color:#facc15;border:1px solid rgba(250,204,21,.25);padding:3px 10px;border-radius:99px;font-weight:700;">DEMO</span>' +
                '</div>' +
                '<div id="trackingMap-demo" style="height:260px;width:100%;background:#0a0a14;border-radius:0 0 20px 20px;"></div>' +
            '</div>';

        // Init demo map — wait for Leaflet if not ready yet
        if (typeof L !== 'undefined') {
            setTimeout(initDemoMap, 100);
        }
        // else initDemoMap() will be called by the post-Leaflet script block
        return;
    }
    // Only re-render if the set of order IDs changed (avoids destroying live maps on every poll)
    const currentIds = [...document.querySelectorAll('#view-active [data-order-id]')]
        .map(el => el.dataset.orderId).sort().join(',');
    const newIds = orders.map(o => String(o.id)).sort().join(',');
    if (currentIds !== newIds) {
        view.innerHTML = orders.map(o => buildActiveCard(o)).join('');
    }
    // Init maps for all active orders — skip ones already initialized
    orders.forEach(o => initOrderMap(o));
}

function buildActiveCard(o) {
    const st = STATUS_MAP[o.status] || STATUS_MAP.pending;
    const cancellable = ['pending','accepted','preparing'].includes(o.status);
    const isPlaced     = o.status === 'pending';
    const isPreparing  = ['accepted','preparing'].includes(o.status);
    const isOnWay      = ['rider_assigned','out_for_delivery'].includes(o.status);
    const isDelivered  = o.status === 'delivered';

    const itemsHtml = o.items.map(item => `
        <div class="item-row">
            <img src="${item.image}" class="item-img" alt="${item.name}">
            <div style="flex:1;min-width:0;">
                <p class="item-name">${item.name}</p>
                <p class="item-qty">Qty: ${item.qty}</p>
                ${modifierTagsHtml(item.modifiers)}
            </div>
            <p class="item-price">₱${Number(item.subtotal).toLocaleString()}</p>
        </div>`).join('');

    const riderHtml = o.rider ? `
        <div class="info-row" style="border:none;">
            <div class="info-icon" style="background:rgba(167,139,250,.1);">
                <svg width="16" height="16" fill="none" stroke="#a78bfa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <p class="info-text-label">Rider</p>
                <p class="info-text-val">${o.rider.name}</p>
                <p class="info-text-sub">⭐ ${o.rider.rating} · ${o.rider.phone}</p>
            </div>
        </div>` : '';

    const cancelHtml = cancellable ? `
        <div style="padding:14px 18px;">
            <button onclick="openCancelModal(${o.id})" style="width:100%;padding:12px;border-radius:12px;background:rgba(239,68,68,.1);border:1.5px solid rgba(239,68,68,.3);color:#f87171;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s;">
                Cancel Order
            </button>
        </div>` : '';

    const mapHtml = `
        <div class="card-header" style="margin-top:4px;">
            <div>
                <p class="card-title">${isOnWay ? '🛵 Live Rider Tracking' : '📍 Order Location'}</p>
                <p class="card-sub" id="riderEtaText-${o.id}">${isOnWay ? 'Fetching route…' : 'Locating your position…'}</p>
            </div>
        </div>
        <div id="trackingMap-${o.id}" style="height:220px;width:100%;background:#0a0a14;"></div>`;

    return `
    <div class="card" style="margin-bottom:20px;position:relative;overflow:hidden;" data-order-id="${o.id}">
        <div style="position:absolute;top:-60px;right:-40px;width:200px;height:200px;background:radial-gradient(circle,rgba(250,204,21,.06) 0%,transparent 70%);pointer-events:none;"></div>
        <!-- Status banner -->
        <div style="background:linear-gradient(135deg,#1a0a00,#1a1200,#0e0f1a);border-bottom:1px solid rgba(250,204,21,.1);padding:20px 18px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                <div>
                    <p style="font-size:10px;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px;">Order ID</p>
                    <p style="font-size:15px;font-weight:800;color:#fff;">#${o.order_number}</p>
                    <p style="font-size:11px;color:#4b5563;margin-top:2px;">${o.placed_at}</p>
                </div>
                <span class="badge badge-live"><span class="badge-pulse"></span> ${st.label}</span>
            </div>
            <div class="progress-eta-row">
                <span class="progress-eta-label">Status</span>
                <span class="progress-eta-time">${st.label}</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width:${st.progress}%"></div>
            </div>
            <div class="progress-steps">
                <span class="progress-step-label ${isPlaced ? 'active' : 'done'}">Placed</span>
                <span class="progress-step-label ${isPreparing ? 'active' : isPlaced ? '' : 'done'}">Preparing</span>
                <span class="progress-step-label ${isOnWay ? 'active' : (isPlaced||isPreparing) ? '' : 'done'}">On the way</span>
                <span class="progress-step-label ${isDelivered ? 'done' : ''}">Delivered</span>
            </div>
        </div>
        <!-- Items -->
        <div class="card-header">
            <div>
                <p class="card-title">Order Items</p>
                <p class="card-sub">${o.items.length} item(s)</p>
            </div>
        </div>
        ${itemsHtml}
        <div class="totals">
            <div class="total-row"><span class="total-label">Subtotal</span><span class="total-value">₱${Number(o.subtotal).toLocaleString()}</span></div>
            <div class="total-row"><span class="total-label">Delivery fee</span><span class="total-value">₱${Number(o.delivery_fee).toLocaleString()}</span></div>
            <hr class="total-divider">
            <div class="total-row"><span class="total-grand-label">Total</span><span class="total-grand-value">₱${Number(o.total).toLocaleString()}</span></div>
        </div>
        <!-- Delivery details -->
        <div class="card-header" style="margin-top:4px;">
            <p class="card-title">Delivery Details</p>
        </div>
        <div class="info-row">
            <div class="info-icon" style="background:rgba(239,68,68,.1);">
                <svg width="16" height="16" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="info-text-label">Address</p>
                <p class="info-text-val">${o.delivery_address}</p>
            </div>
        </div>
        <div class="info-row">
            <div class="info-icon" style="background:rgba(96,165,250,.1);">
                <svg width="16" height="16" fill="none" stroke="#60a5fa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div>
                <p class="info-text-label">Payment</p>
                <p class="info-text-val" style="text-transform:capitalize;">${o.payment_method}</p>
            </div>
        </div>
        ${riderHtml}
        ${mapHtml}
        ${cancelHtml}
    </div>`;
}

/* ── Render Past Orders ── */
function renderPastOrders(orders) {
    const list  = document.getElementById('pastOrdersList');
    const empty = document.getElementById('pastEmpty');
    if (!orders.length) { list.innerHTML = ''; empty.style.display = 'block'; return; }
    empty.style.display = 'none';
    list.innerHTML = orders.map(o => `
        <div class="pcard">
            <div class="pcard-header">
                <div class="pcard-id-row">
                    <span class="pcard-id">#${o.order_number}</span>
                    <span class="badge badge-done">✓ Delivered</span>
                </div>
                <p class="pcard-date">${o.placed_at}</p>
            </div>
            <div class="pcard-items">
                ${o.items.map(i => `
                <div class="pcard-item-row">
                    <img src="${i.image}" class="pcard-item-img" alt="${i.name}">
                    <span class="pcard-item-name">${i.name} × ${i.qty}</span>
                    <span class="pcard-item-price">₱${Number(i.subtotal).toLocaleString()}</span>
                </div>`).join('')}
            </div>
            <div class="pcard-footer">
                <div>
                    <p class="pcard-total-label">Total paid</p>
                    <p class="pcard-total">₱${Number(o.total).toLocaleString()}</p>
                </div>
                <a href="<?php echo e(route('shop.home')); ?>" class="btn-reorder">🔁 Reorder</a>
            </div>
        </div>`).join('');
}

/* ── Render Cancelled Orders ── */
function renderCancelledOrders(orders) {
    const list  = document.getElementById('cancelledOrdersList');
    const empty = document.getElementById('cancelledEmpty');
    if (!orders.length) { list.innerHTML = ''; empty.style.display = 'block'; return; }
    empty.style.display = 'none';
    list.innerHTML = orders.map(o => `
        <div class="pcard pcard-cancelled">
            <div class="pcard-header">
                <div class="pcard-id-row">
                    <span class="pcard-id" style="color:#9ca3af;">#${o.order_number}</span>
                    <span class="badge badge-cancelled">✕ Cancelled</span>
                </div>
                <p class="pcard-date">${o.placed_at}</p>
            </div>
            <div class="pcard-items" style="opacity:.5;">
                ${o.items.map(i => `
                <div class="pcard-item-row">
                    <img src="${i.image}" class="pcard-item-img" alt="${i.name}" style="filter:grayscale(1);">
                    <span class="pcard-item-name" style="text-decoration:line-through;color:#4b5563;">${i.name} × ${i.qty}</span>
                    <span class="pcard-item-price" style="color:#4b5563;text-decoration:line-through;">₱${Number(i.subtotal).toLocaleString()}</span>
                </div>`).join('')}
            </div>
            ${o.cancel_reason ? `
            <div class="cancel-reason">
                <span class="cancel-reason-icon">⚠️</span>
                <div>
                    <p class="cancel-reason-title">Cancellation Reason</p>
                    <p class="cancel-reason-text">${o.cancel_reason}</p>
                </div>
            </div>` : ''}
            <div class="pcard-footer">
                <div>
                    <p class="pcard-total-label">Order total</p>
                    <p class="pcard-total">₱${Number(o.total).toLocaleString()}</p>
                </div>
                <a href="<?php echo e(route('shop.home')); ?>" class="btn-reorder" style="background:linear-gradient(135deg,#374151,#4b5563);color:#e5e7eb;box-shadow:none;">Try Again</a>
            </div>
        </div>`).join('');
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
    applyTheme(localStorage.getItem('eutTheme') || 'dark');
    document.getElementById('shopThemeToggle').addEventListener('click', () => {
        const t = (localStorage.getItem('eutTheme') || 'dark') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('eutTheme', t);
        applyTheme(t);
    });
    loadAllOrders();
    setInterval(loadAllOrders, 5000);
});

/* ── Order Map (Leaflet + OSRM) ── */
const RESTAURANT_POS = [13.3213129, 121.3027265]; // EUT Restaurant coords
const activeMaps     = {};                         // keyed by order id

async function fetchOSRMRoute(from, to) {
    const url = 'https://router.project-osrm.org/route/v1/driving/'
        + from[1] + ',' + from[0] + ';' + to[1] + ',' + to[0]
        + '?overview=full&geometries=geojson';
    try {
        const res  = await fetch(url);
        const data = await res.json();
        if (data.code === 'Ok' && data.routes.length) {
            return data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
        }
    } catch (e) { console.warn('OSRM error:', e); }
    return null;
}

async function initOrderMap(order) {
    const el = document.getElementById('trackingMap-' + order.id);
    if (!el || activeMaps[order.id]) return;

    const isOnWay = ['rider_assigned','out_for_delivery'].includes(order.status);
    const etaEl   = document.getElementById('riderEtaText-' + order.id);

    // Use real rider coords if available, else simulate near the restaurant
    const hasRealCoords = order.rider && order.rider.lat && order.rider.lng;
    let riderPos = hasRealCoords
        ? [parseFloat(order.rider.lat), parseFloat(order.rider.lng)]
        : [RESTAURANT_POS[0] + 0.002, RESTAURANT_POS[1] + 0.002];

    const map = L.map(el, { zoomControl: true, attributionControl: false });
    activeMaps[order.id] = { map, riderMarker: null, routeLine: null, roadPoints: [], simStep: 0 };

    // Google Satellite
    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20 }).addTo(map);
    // Google Hybrid labels overlay
    L.tileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', { maxZoom: 20, opacity: 0.85 }).addTo(map);

    // Restaurant pin 🍔
    L.marker(RESTAURANT_POS, { icon: L.divIcon({
        html: '<div style="background:#facc15;width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #d97706;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.5);"><span style="transform:rotate(45deg);font-size:15px;">🍔</span></div>',
        className:'', iconSize:[36,36], iconAnchor:[18,36],
    })}).addTo(map).bindPopup('<b>EUT Restaurant</b><br>Metro Naujan');

    // Try to get customer's current GPS location
    let customerPos = null;
    if (navigator.geolocation) {
        try {
            customerPos = await new Promise((resolve) => {
                navigator.geolocation.getCurrentPosition(
                    pos => resolve([pos.coords.latitude, pos.coords.longitude]),
                    ()  => resolve(null),
                    { timeout: 5000, maximumAge: 30000 }
                );
            });
        } catch(e) { customerPos = null; }
    }

    if (customerPos) {
        L.marker(customerPos, { icon: L.divIcon({
            html: '<div style="background:#ef4444;width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #b91c1c;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.5);"><span style="transform:rotate(45deg);font-size:15px;">🏠</span></div>',
            className:'', iconSize:[36,36], iconAnchor:[18,36],
        })}).addTo(map).bindPopup('<b>Your Location</b>');
    }

    const boundsPoints = customerPos
        ? [RESTAURANT_POS, customerPos]
        : [RESTAURANT_POS];

    if (!isOnWay) {
        // Just restaurant + customer — no rider yet
        map.fitBounds(boundsPoints.length > 1 ? boundsPoints : [RESTAURANT_POS, [RESTAURANT_POS[0]+0.01, RESTAURANT_POS[1]+0.01]], { padding:[50,50] });
        if (etaEl) etaEl.textContent = customerPos ? 'Showing restaurant & your location' : 'Showing restaurant location';
        return;
    }

    // ── Rider on the way ──
    const destination = customerPos || [RESTAURANT_POS[0] + 0.005, RESTAURANT_POS[1] + 0.006];

    // Rider pin 🛵
    const riderMarker = L.marker(riderPos, { icon: L.divIcon({
        html: '<div style="background:#10b981;width:44px;height:44px;border-radius:50%;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:22px;box-shadow:0 0 14px rgba(16,185,129,.7);">🛵</div>',
        className:'', iconSize:[44,44], iconAnchor:[22,22],
    })}).addTo(map);

    if (order.rider) {
        riderMarker.bindPopup('<b>' + order.rider.name + '</b><br>⭐ ' + order.rider.rating + ' · ' + order.rider.phone);
    }

    activeMaps[order.id].riderMarker = riderMarker;
    map.fitBounds([RESTAURANT_POS, destination, riderPos], { padding:[40,40] });

    // Fetch OSRM route
    const route = await fetchOSRMRoute(riderPos, destination);
    if (route) {
        activeMaps[order.id].roadPoints = route;
        const line = L.polyline(route, { color:'#facc15', weight:5, opacity:1 }).addTo(map);
        activeMaps[order.id].routeLine = line;
        map.fitBounds(line.getBounds(), { padding:[40,40] });
    } else {
        const line = L.polyline([riderPos, destination], { color:'#facc15', weight:3, opacity:.7, dashArray:'8 6' }).addTo(map);
        activeMaps[order.id].routeLine = line;
    }

    if (!hasRealCoords) {
        simulateMapRider(order.id, destination);
    }
}

function simulateMapRider(orderId, destination) {
    const state = activeMaps[orderId];
    if (!state) return;
    const etaEl = document.getElementById('riderEtaText-' + orderId);

    if (state.roadPoints.length) {
        // Walk along road
        const total = state.roadPoints.length;
        const iv = setInterval(() => {
            if (!activeMaps[orderId]) { clearInterval(iv); return; }
            state.simStep = Math.min(state.simStep + 1, total - 1);
            const pos = state.roadPoints[state.simStep];
            state.riderMarker.setLatLng(pos);
            state.routeLine.setLatLngs(state.roadPoints.slice(state.simStep));
            const minsLeft = Math.max(0, Math.round(30 * (1 - state.simStep / total)));
            if (etaEl) etaEl.textContent = minsLeft > 0 ? '~' + minsLeft + ' min away' : 'Arriving now!';
            if (state.simStep >= total - 1) clearInterval(iv);
        }, 2500);
    } else {
        // Straight-line fallback
        let step = 0, totalSteps = 60;
        let pos = state.riderMarker.getLatLng();
        const dLat = (destination[0] - pos.lat) / totalSteps;
        const dLng = (destination[1] - pos.lng) / totalSteps;
        const iv = setInterval(() => {
            if (!activeMaps[orderId]) { clearInterval(iv); return; }
            step++;
            pos = { lat: pos.lat + dLat, lng: pos.lng + dLng };
            state.riderMarker.setLatLng(pos);
            state.routeLine.setLatLngs([[pos.lat, pos.lng], destination]);
            const minsLeft = Math.max(0, Math.round(30 * (1 - step / totalSteps)));
            if (etaEl) etaEl.textContent = minsLeft > 0 ? '~' + minsLeft + ' min away' : 'Arriving now!';
            if (step >= totalSteps) clearInterval(iv);
        }, 3000);
    }
}

// Update rider marker position on live poll (called from loadAllOrders)
function updateMapRiderPos(orderId, lat, lng) {
    const state = activeMaps[orderId];
    if (!state || !state.riderMarker) return;
    state.riderMarker.setLatLng([lat, lng]);
}

/* ============================================================
   TODO: REMOVE initDemoMap() when real orders are in use.
   This is a placeholder so the map is visible during dev/demo.
   ============================================================ */
async function initDemoMap() {
    const el = document.getElementById('trackingMap-demo');
    if (!el || activeMaps['demo']) return;

    const RESTAURANT = [13.3213129, 121.3027265];
    const CUSTOMER   = [13.3265,    121.3085];
    let   riderPos   = [13.3235,    121.3050];

    const map = L.map(el, { zoomControl: true, attributionControl: false });
    activeMaps['demo'] = { map, riderMarker: null, routeLine: null, roadPoints: [], simStep: 0 };

    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20 }).addTo(map);
    L.tileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', { maxZoom: 20, opacity: 0.85 }).addTo(map);

    // Restaurant 🍔
    L.marker(RESTAURANT, { icon: L.divIcon({
        html: '<div style="background:#facc15;width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #d97706;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.5);"><span style="transform:rotate(45deg);font-size:15px;">🍔</span></div>',
        className:'', iconSize:[36,36], iconAnchor:[18,36],
    })}).addTo(map).bindPopup('<b>EUT Restaurant</b><br>Metro Naujan');

    // Customer 🏠
    L.marker(CUSTOMER, { icon: L.divIcon({
        html: '<div style="background:#ef4444;width:36px;height:36px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #b91c1c;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.5);"><span style="transform:rotate(45deg);font-size:15px;">🏠</span></div>',
        className:'', iconSize:[36,36], iconAnchor:[18,36],
    })}).addTo(map).bindPopup('<b>Your Location</b>');

    // Rider 🛵
    const riderMarker = L.marker(riderPos, { icon: L.divIcon({
        html: '<div style="background:#10b981;width:44px;height:44px;border-radius:50%;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:22px;box-shadow:0 0 14px rgba(16,185,129,.7);">🛵</div>',
        className:'', iconSize:[44,44], iconAnchor:[22,22],
    })}).addTo(map).bindPopup('<b>Juan dela Cruz</b><br>⭐ 4.9 · Demo Rider');

    activeMaps['demo'].riderMarker = riderMarker;
    map.fitBounds([RESTAURANT, CUSTOMER, riderPos], { padding:[40,40] });

    const route = await fetchOSRMRoute(riderPos, CUSTOMER);
    if (route) {
        activeMaps['demo'].roadPoints = route;
        const line = L.polyline(route, { color:'#facc15', weight:5, opacity:1 }).addTo(map);
        activeMaps['demo'].routeLine = line;
        map.fitBounds(line.getBounds(), { padding:[40,40] });
    } else {
        activeMaps['demo'].routeLine = L.polyline([riderPos, CUSTOMER], { color:'#facc15', weight:3, opacity:.7, dashArray:'8 6' }).addTo(map);
    }

    simulateMapRider('demo', CUSTOMER);
}
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// Leaflet is now loaded — safe to init the demo map if it hasn't run yet
if (typeof initDemoMap === 'function' && !activeMaps['demo']) {
    initDemoMap();
}
</script>
</body>
</html>
<?php /**PATH C:\Users\patri\Desktop\EUT_WEB\resources\views/shop/tracking.blade.php ENDPATH**/ ?>