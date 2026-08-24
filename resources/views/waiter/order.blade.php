<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Waiter Order — EUT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;font-family:'Inter',sans-serif;}
        body{background:#080810;color:#fff;min-height:100vh;}

        /* NAV */
        .topnav{position:fixed;top:0;left:0;right:0;z-index:100;background:rgba(8,8,16,.96);border-bottom:1px solid rgba(255,255,255,.06);}
        .topnav-inner{max-width:560px;margin:0 auto;padding:10px 16px;display:flex;align-items:center;gap:10px;}
        .nav-btn{width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;color:#9ca3af;text-decoration:none;cursor:pointer;}
        .nav-title{font-size:15px;font-weight:600;color:#fff;flex:1;}

        /* TABLE SELECTOR */
        .table-selector{max-width:560px;margin:0 auto;padding:76px 16px 12px;}
        .table-label{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#9ca3af;margin-bottom:.5rem;}
        .table-select-wrap{position:relative;}
        .table-select{width:100%;padding:.875rem 1rem;background:rgba(74,222,128,.08);border:1.5px solid rgba(74,222,128,.35);border-radius:14px;color:#4ade80;font-size:1rem;font-weight:700;outline:none;appearance:none;cursor:pointer;}
        .table-select option{background:#111;color:#fff;}

        /* CATEGORY PILLS */
        .cats-bar{position:sticky;top:56px;z-index:40;background:#080810;border-bottom:1px solid rgba(255,255,255,.06);}
        .cats-inner{max-width:560px;margin:0 auto;padding:10px 16px;display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;}
        .cats-inner::-webkit-scrollbar{display:none;}
        .cat-pill{flex-shrink:0;padding:7px 16px;border-radius:99px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid rgba(255,255,255,.09);background:rgba(255,255,255,.05);color:#6b7280;transition:all .2s;white-space:nowrap;}
        .cat-pill.active{background:linear-gradient(135deg,#16a34a,#22c55e);border-color:transparent;color:#fff;box-shadow:0 3px 14px rgba(34,197,94,.4);}

        /* PRODUCT GRID */
        .products-grid{max-width:560px;margin:0 auto;padding:0 16px 180px;display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-top:12px;}
        @media(min-width:480px){.products-grid{grid-template-columns:repeat(3,1fr);}}

        /* PRODUCT CARD */
        .p-card{background:linear-gradient(145deg,#12131f,#0e0f1a);border:1px solid rgba(255,255,255,.07);border-radius:18px;overflow:hidden;cursor:pointer;transition:transform .2s,border-color .2s;}
        .p-card:hover{transform:translateY(-2px);border-color:rgba(34,197,94,.3);}
        .p-card-img{width:100%;aspect-ratio:1;object-fit:cover;}
        .p-card-body{padding:10px 12px 14px;}
        .p-card-name{font-size:.8rem;font-weight:700;color:#fff;margin-bottom:4px;}
        .p-card-price{font-size:.875rem;font-weight:800;color:#facc15;}

        /* CART BAR */
        .cart-bar{position:fixed;bottom:0;left:0;right:0;z-index:200;background:rgba(8,8,16,.98);border-top:1px solid rgba(255,255,255,.07);padding:12px 16px 24px;}
        .cart-bar-inner{max-width:560px;margin:0 auto;display:flex;gap:10px;align-items:center;}
        .cart-info{flex:1;}
        .cart-count{font-size:.7rem;color:#9ca3af;}
        .cart-total{font-size:1.1rem;font-weight:800;color:#facc15;}
        .btn-checkout{flex-shrink:0;padding:13px 24px;border-radius:14px;background:linear-gradient(135deg,#16a34a,#22c55e);border:none;color:#000;font-size:.875rem;font-weight:800;cursor:pointer;box-shadow:0 4px 18px rgba(34,197,94,.3);transition:all .2s;}
        .btn-checkout:disabled{opacity:.4;cursor:not-allowed;}

        /* BOTTOM SHEET */
        .sheet-backdrop{position:fixed;inset:0;z-index:300;background:rgba(0,0,0,.7);backdrop-filter:blur(4px);opacity:0;pointer-events:none;transition:opacity .3s;}
        .sheet-backdrop.open{opacity:1;pointer-events:all;}
        .sheet{position:fixed;bottom:0;left:0;right:0;z-index:400;background:#0e0f1a;border-radius:24px 24px 0 0;border:1px solid rgba(255,255,255,.08);border-bottom:none;max-height:92vh;overflow-y:auto;transform:translateY(100%);transition:transform .4s cubic-bezier(.32,.72,0,1);}
        .sheet.open{transform:translateY(0);}
        .sheet-handle{width:40px;height:4px;border-radius:99px;background:rgba(255,255,255,.15);margin:12px auto 0;}
        .sheet-header{display:flex;align-items:center;gap:14px;padding:14px 18px 12px;}
        .sheet-thumb{width:64px;height:64px;border-radius:14px;object-fit:cover;flex-shrink:0;}
        .sheet-name{font-family:'Playfair Display',serif;font-size:15px;font-weight:700;color:#fff;}
        .sheet-price{font-size:18px;font-weight:800;color:#facc15;}
        .sheet-close{margin-left:auto;width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);display:flex;align-items:center;justify-content:center;cursor:pointer;color:#6b7280;}
        .sheet-divider{height:1px;background:rgba(255,255,255,.06);margin:0 18px;}
        .sheet-section{padding:14px 18px 8px;}
        .sheet-section-title{font-size:13px;font-weight:700;color:#fff;margin-bottom:10px;display:flex;align-items:center;justify-content:space-between;}
        .flavor-grid{display:flex;flex-wrap:wrap;gap:8px;}
        .swatch{cursor:pointer;border-radius:10px;overflow:hidden;border:2px solid transparent;transition:all .2s;flex-shrink:0;}
        .swatch.sel{border-color:#facc15;box-shadow:0 0 0 1px #facc15;}
        .swatch-inner{width:68px;height:68px;display:flex;align-items:flex-end;justify-content:center;padding-bottom:5px;}
        .swatch-name{font-size:9px;font-weight:700;color:#fff;text-shadow:0 1px 4px rgba(0,0,0,.8);text-align:center;}
        .pill-grid{display:flex;gap:8px;flex-wrap:wrap;}
        .pill{padding:9px 16px;border-radius:10px;background:rgba(255,255,255,.05);border:1.5px solid rgba(255,255,255,.08);cursor:pointer;transition:all .2s;text-align:center;min-width:60px;}
        .pill.sel{background:rgba(250,204,21,.1);border-color:#facc15;}
        .pill-label{font-size:13px;font-weight:700;color:#e5e7eb;display:block;}
        .pill.sel .pill-label{color:#facc15;}
        .pill-desc{font-size:10px;color:#4b5563;display:block;}
        .addon-card{display:flex;align-items:center;justify-content:space-between;padding:11px 14px;border-radius:12px;background:rgba(255,255,255,.04);border:1.5px solid rgba(255,255,255,.07);cursor:pointer;transition:all .2s;margin-bottom:8px;}
        .addon-card.sel{background:rgba(245,158,11,.1);border-color:#f59e0b;}
        .addon-check{width:20px;height:20px;border-radius:5px;background:rgba(255,255,255,.06);border:1.5px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-right:10px;transition:all .2s;}
        .addon-card.sel .addon-check{background:#f59e0b;border-color:#f59e0b;}
        .qty-section{padding:8px 18px 14px;display:flex;align-items:center;justify-content:space-between;}
        .qty-controls{display:flex;align-items:center;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:99px;}
        .qty-btn{width:38px;height:38px;border-radius:99px;background:none;border:none;cursor:pointer;font-size:18px;font-weight:700;color:#9ca3af;display:flex;align-items:center;justify-content:center;}
        .qty-val{width:36px;text-align:center;font-size:15px;font-weight:700;color:#fff;background:none;border:none;outline:none;}
        .sheet-total-row{display:flex;align-items:center;justify-content:space-between;padding:8px 18px 12px;}
        .sheet-actions{display:flex;gap:10px;padding:0 18px 32px;}
        .btn-add-sheet{flex:1;padding:14px;border-radius:14px;background:linear-gradient(135deg,#16a34a,#22c55e);border:none;color:#000;font-size:14px;font-weight:800;cursor:pointer;}

        /* CART REVIEW SHEET */
        .cart-item-row{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.05);}
        .cart-item-img{width:48px;height:48px;border-radius:8px;object-fit:cover;flex-shrink:0;}
        .ci-unit-price{font-size:.75rem;color:#9ca3af;}
        .ci-qty-controls{display:flex;align-items:center;gap:0;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:99px;flex-shrink:0;}
        .ci-qty-btn{width:26px;height:26px;border-radius:99px;background:none;border:none;cursor:pointer;font-size:15px;font-weight:700;color:#9ca3af;display:flex;align-items:center;justify-content:center;line-height:1;padding:0;}
        .ci-qty-btn:hover{color:#fff;}
        .ci-qty-val{width:22px;text-align:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;}
        .ci-remove{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:6px;padding:4px 8px;cursor:pointer;font-size:11px;font-weight:700;flex-shrink:0;}
        .btn-place{width:100%;padding:15px;border-radius:14px;background:linear-gradient(135deg,#16a34a,#22c55e);border:none;color:#000;font-size:15px;font-weight:800;cursor:pointer;box-shadow:0 4px 18px rgba(34,197,94,.3);}
        .btn-place:disabled{opacity:.4;cursor:not-allowed;}

        /* NO TABLE */
        .no-table{text-align:center;padding:3rem 1rem;color:#6b7280;}

        /* DINE-IN CLOSED BANNER */
        .dinein-closed-banner{display:none;max-width:560px;margin:66px auto 0;padding:10px 16px;background:rgba(239,68,68,.12);border-bottom:1px solid rgba(239,68,68,.3);color:#fca5a5;font-size:.78rem;font-weight:600;align-items:center;gap:.5rem;}
        body.dinein-closed .dinein-closed-banner{display:flex;}
        body.dinein-closed .table-selector{padding-top:12px;}
    </style>
</head>
<body class="{{ $isOpenDineIn ? '' : 'dinein-closed' }}">

{{-- NAV --}}
<nav class="topnav">
    <div class="topnav-inner">
        <a href="{{ route('waiter.dashboard') }}" class="nav-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="nav-title">🧾 Take Order</span>
        <button onclick="openCartSheet()" class="nav-btn" style="position:relative;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span id="cartBadge" style="display:none;position:absolute;top:-3px;right:-3px;min-width:16px;height:16px;border-radius:99px;background:#22c55e;border:2px solid #080810;font-size:9px;font-weight:800;color:#000;display:flex;align-items:center;justify-content:center;padding:0 3px;"></span>
        </button>
    </div>
</nav>

{{-- DINE-IN CLOSED BANNER --}}
<div id="dineInClosedBanner" class="dinein-closed-banner">
    🔴 Dine-In service is currently CLOSED — orders cannot be placed right now.
</div>

{{-- TABLE SELECTOR --}}
<div class="table-selector">
    <p class="table-label">Select Table</p>
    <div class="table-select-wrap">
        <select id="tableSelect" class="table-select" onchange="onTableChange(this.value)">
            <option value="">— Choose a table —</option>
            @foreach($tables as $t)
            <option value="{{ $t }}">Table {{ $t }}</option>
            @endforeach
        </select>
    </div>
    <p id="tableHint" style="font-size:.72rem;color:#6b7280;margin-top:.5rem;display:none;">
        Ordering for <strong id="tableHintNum" style="color:#4ade80;"></strong>
    </p>
</div>

{{-- CATEGORY PILLS --}}
<div class="cats-bar">
    <div class="cats-inner" id="catPills">
        <button class="cat-pill active" onclick="filterCat('all', this)">All</button>
        @foreach($categories as $cat)
        <button class="cat-pill" onclick="filterCat('{{ $cat->slug }}', this)">{{ $cat->name }}</button>
        @endforeach
    </div>
</div>

{{-- PRODUCTS --}}
<div class="products-grid" id="productsGrid">
    @foreach($menuItems as $item)
    <div class="p-card" data-cat="{{ $item->category->slug ?? '' }}" data-item-id="{{ $item->id }}" onclick="openItemSheet({{ $item->id }})">
        <img src="{{ $item->image ? asset($item->image) : asset('images/menu/default-menu-item.webp') }}"
             alt="{{ $item->name }}" class="p-card-img" loading="lazy"
             onerror="this.src='{{ asset('images/menu/default-menu-item.webp') }}'">
        <div class="p-card-body">
            <p class="p-card-name">{{ $item->name }}</p>
            <p class="p-card-price">₱{{ number_format($item->price, 0) }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- CART BAR --}}
<div class="cart-bar" id="cartBar" style="display:none;">
    <div class="cart-bar-inner">
        <div class="cart-info">
            <div class="cart-count" id="cartCountLabel">0 items</div>
            <div class="cart-total" id="cartTotalLabel">₱0</div>
        </div>
        <button class="btn-checkout" id="checkoutBtn" onclick="openCartSheet()">
            Review Order →
        </button>
    </div>
</div>

{{-- ITEM BOTTOM SHEET --}}
<div class="sheet-backdrop" id="itemBackdrop" onclick="closeItemSheet()"></div>
<div class="sheet" id="itemSheet">
    <div class="sheet-handle"></div>
    <div class="sheet-header">
        <img id="sThumb" src="" alt="" class="sheet-thumb" onerror="this.src='{{ asset('images/menu/default-menu-item.webp') }}'">
        <div style="flex:1;min-width:0;">
            <p id="sName" class="sheet-name"></p>
            <span id="sPrice" class="sheet-price"></span>
        </div>
        <button class="sheet-close" onclick="closeItemSheet()">✕</button>
    </div>
    <div class="sheet-divider"></div>
    <div id="sModifiers"></div>
    <div id="sAddons" style="display:none;">
        <div class="sheet-divider"></div>
        <div class="sheet-section">
            <p class="sheet-section-title">Add-ons <span style="font-size:11px;color:#4b5563;font-weight:400;">Optional</span></p>
            <div id="sAddonsList"></div>
        </div>
    </div>
    <div class="sheet-divider"></div>
    <div class="qty-section">
        <span style="font-size:13px;font-weight:700;color:#fff;">Quantity</span>
        <div class="qty-controls">
            <button class="qty-btn" onclick="changeQty(-1)">−</button>
            <span id="sQtyVal" class="qty-val">1</span>
            <button class="qty-btn" onclick="changeQty(1)">+</button>
        </div>
    </div>
    <div class="sheet-divider"></div>
    <div class="sheet-total-row">
        <span style="font-size:12px;color:#4b5563;">Total</span>
        <span id="sTotal" style="font-size:20px;font-weight:800;color:#facc15;"></span>
    </div>
    <div class="sheet-actions">
        <button class="btn-add-sheet" onclick="addToCart()">+ Add to Order</button>
    </div>
</div>

{{-- CART REVIEW SHEET --}}
<div class="sheet-backdrop" id="cartBackdrop" onclick="closeCartSheet()"></div>
<div class="sheet" id="cartSheet">
    <div class="sheet-handle"></div>
    <div style="padding:14px 18px 8px;display:flex;align-items:center;justify-content:space-between;">
        <p style="font-size:15px;font-weight:700;color:#fff;">Order Summary</p>
        <button class="sheet-close" onclick="closeCartSheet()">✕</button>
    </div>
    <div class="sheet-divider"></div>

    {{-- Table number display --}}
    <div style="padding:10px 18px 0;">
        <div id="cartTableDisplay" style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.3);border-radius:8px;padding:.3rem .75rem;font-size:.8rem;font-weight:700;color:#4ade80;">
            🪑 <span id="cartTableNum">No table selected</span>
        </div>
    </div>

    <div id="cartItemsList" style="padding:0 18px;"></div>
    <div class="sheet-divider" style="margin-top:8px;"></div>
    <div style="padding:10px 18px;display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:.85rem;color:#9ca3af;">Total</span>
        <span id="cartSheetTotal" style="font-size:1.2rem;font-weight:800;color:#facc15;"></span>
    </div>

    {{-- Notes --}}
    <div style="padding:0 18px 12px;">
        <p style="font-size:.72rem;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;">Special Request <span style="font-weight:400;">(optional)</span></p>
        <textarea id="orderNotes" rows="2"
            style="width:100%;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;color:#d4d4d4;font-size:.85rem;padding:.6rem .8rem;outline:none;resize:none;"
            placeholder="e.g. No spicy, extra rice, allergies…"></textarea>
    </div>

    <div style="padding:0 18px 32px;">
        <div id="cartError" style="display:none;color:#f87171;font-size:.78rem;margin-bottom:.75rem;padding:.5rem .75rem;background:rgba(239,68,68,.08);border-radius:8px;"></div>
        <button id="placeOrderBtn" class="btn-place" onclick="placeOrder()" disabled>
            Place Order
        </button>
    </div>
</div>

{{-- Toast --}}
<div id="toast" style="position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#1a1b2e;border:1px solid rgba(34,197,94,.3);color:#4ade80;padding:10px 20px;border-radius:99px;font-size:13px;font-weight:600;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,.5);display:none;"></div>

<script>
const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
const IQ_ITEMS     = @json($menuItemsData->keyBy('id'));
const SWATCH_COLORS = [
    'linear-gradient(135deg,#b45309,#92400e)',
    'linear-gradient(135deg,#dc2626,#b91c1c)',
    'linear-gradient(135deg,#292524,#57534e)',
    'linear-gradient(135deg,#854d0e,#ca8a04)',
    'linear-gradient(135deg,#f59e0b,#ef4444)',
    'linear-gradient(135deg,#3f3f46,#1c1917)',
    'linear-gradient(135deg,#1d4ed8,#1e3a8a)',
    'linear-gradient(135deg,#15803d,#14532d)',
];

let selectedTable = '';
let cart          = [];
let curItem       = null;
let curQty        = 1;
let curSelOpts    = {};
let curSelAddons  = {};
let DINE_IN_OPEN  = @json($isOpenDineIn);

// ── Dine-in service status ──────────────────────────────────────────────────
function refreshPlaceOrderBtn() {
    document.getElementById('placeOrderBtn').disabled = !DINE_IN_OPEN || cart.length === 0 || !selectedTable;
}

function setDineInOpen(isOpen) {
    DINE_IN_OPEN = isOpen;
    document.body.classList.toggle('dinein-closed', !isOpen);
    refreshPlaceOrderBtn();
}

// ── Table ───────────────────────────────────────────────────────────────────
function onTableChange(val) {
    selectedTable = val;
    const hint = document.getElementById('tableHint');
    const num  = document.getElementById('tableHintNum');
    if (val) {
        hint.style.display = 'block';
        num.textContent = 'Table ' + val;
        document.getElementById('cartTableNum').textContent = 'Table ' + val;
    } else {
        hint.style.display = 'none';
        document.getElementById('cartTableNum').textContent = 'No table selected';
    }
    refreshPlaceOrderBtn();
}

// ── Category filter ─────────────────────────────────────────────────────────
function filterCat(slug, btn) {
    document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.p-card').forEach(card => {
        card.style.display = (slug === 'all' || card.dataset.cat === slug) ? '' : 'none';
    });
}

// ── Item Sheet ───────────────────────────────────────────────────────────────
function openItemSheet(itemId) {
    curItem       = IQ_ITEMS[itemId];
    curQty        = 1;
    curSelOpts    = {};
    curSelAddons  = {};
    if (!curItem) return;

    document.getElementById('sThumb').src  = curItem.image ? '/' + curItem.image.replace(/^\//, '') : '{{ asset("images/menu/default-menu-item.webp") }}';
    document.getElementById('sName').textContent = curItem.name;
    document.getElementById('sQtyVal').textContent = '1';

    buildModifiers();
    buildAddons();
    updateTotal();

    document.getElementById('itemBackdrop').classList.add('open');
    document.getElementById('itemSheet').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeItemSheet() {
    document.getElementById('itemBackdrop').classList.remove('open');
    document.getElementById('itemSheet').classList.remove('open');
    document.body.style.overflow = '';
}

function buildModifiers() {
    const cont   = document.getElementById('sModifiers');
    const groups = curItem.modifier_groups || [];
    if (!groups.length) { cont.innerHTML = ''; return; }

    cont.innerHTML = groups.map((g, gi) => {
        const isFlavor = g.type === 'flavor';
        const label    = g.required ? 'Required' : 'Select one';
        const divider  = gi < groups.length - 1 ? '<div style="height:1px;background:rgba(255,255,255,.06);margin:0 18px;"></div>' : '';
        const optsHtml = isFlavor ? flavorHtml(g) : pillHtml(g);
        return `<div class="sheet-section" id="sg_${g.id}">
            <p class="sheet-section-title">${esc(g.name)}<span id="sgl_${g.id}" style="font-size:11px;color:#4b5563;font-weight:400;">${label}</span></p>
            ${optsHtml}
        </div>${divider}`;
    }).join('');

    // Pre-select defaults
    groups.forEach(g => {
        const def = (g.active_options || []).find(o => o.is_default);
        if (def) {
            curSelOpts[g.id] = def;
            document.getElementById(`so_${g.id}_${def.id}`)?.classList.add('sel');
            const lbl = document.getElementById(`sgl_${g.id}`);
            if (lbl) lbl.textContent = def.name;
        }
    });
}

function flavorHtml(g) {
    return `<div class="flavor-grid">` + (g.active_options||[]).map((o,i)=>`
        <div class="swatch" id="so_${g.id}_${o.id}" onclick="selectOpt(${g.id},${o.id})">
            <div class="swatch-inner" style="background:${SWATCH_COLORS[i%SWATCH_COLORS.length]};">
                <span class="swatch-name">${esc(o.name)}</span>
            </div>
        </div>`).join('') + `</div>`;
}

function pillHtml(g) {
    return `<div class="pill-grid">` + (g.active_options||[]).map(o=>{
        const adj=parseFloat(o.price_adjustment||0);
        const pl=o.price_type==='add'&&adj>0?`+₱${adj.toLocaleString()}`:o.price_type==='replace'?`₱${adj.toLocaleString()}`:'Free';
        return `<div class="pill" id="so_${g.id}_${o.id}" onclick="selectOpt(${g.id},${o.id})">
            <span class="pill-label">${esc(o.name)}</span>
            <span class="pill-desc">${pl}</span>
        </div>`;
    }).join('') + `</div>`;
}

function selectOpt(gid, oid) {
    const g   = (curItem.modifier_groups||[]).find(x=>x.id==gid);
    if (!g) return;
    const opt = (g.active_options||[]).find(x=>x.id==oid);
    if (!opt) return;
    // Single select
    (g.active_options||[]).forEach(o=>document.getElementById(`so_${gid}_${o.id}`)?.classList.remove('sel'));
    const same = curSelOpts[gid]?.id == oid;
    if (same) { curSelOpts[gid]=null; const l=document.getElementById(`sgl_${gid}`); if(l)l.textContent=g.required?'Required':'Select one'; }
    else      { document.getElementById(`so_${gid}_${oid}`)?.classList.add('sel'); curSelOpts[gid]=opt; const l=document.getElementById(`sgl_${gid}`); if(l)l.textContent=opt.name; }
    updateTotal();
}

function buildAddons() {
    const wrap   = document.getElementById('sAddons');
    const list   = document.getElementById('sAddonsList');
    const groups = curItem.addon_groups || [];
    curSelAddons = {};
    if (!groups.length) { wrap.style.display='none'; return; }
    wrap.style.display = 'block';
    const isRadio = groups.some(g=>g.max_selections===1);
    if (isRadio) {
        list.style.cssText='display:flex;flex-wrap:wrap;gap:8px;';
        list.innerHTML = groups.map(g=>(g.active_options||[]).map(o=>{
            const adj=parseFloat(o.price_adjustment||0);
            const paid=o.price_type==='add'&&adj>0;
            return `<div class="pill" id="sao_${g.id}_${o.id}" data-gid="${g.id}" onclick="toggleAddonOpt(${g.id},${o.id},'${esc(o.name)}','${o.price_type}',${adj})">
                <span class="pill-label">${esc(o.name)}</span>
                ${paid?`<span class="pill-desc">+₱${adj.toLocaleString()}</span>`:''}
            </div>`;
        }).join('')).join('');
    } else {
        list.style.cssText='display:flex;flex-direction:column;gap:0;';
        list.innerHTML = groups.map(g=>{
            const o=g.active_options?.[0];
            const adj=parseFloat(o?.price_adjustment||0);
            const paid=o?.price_type==='add'&&adj>0;
            return `<div class="addon-card" id="sag_${g.id}" onclick="toggleAddon(${g.id},'${esc(g.name)}','${o?.price_type||'none'}',${adj})">
                <div style="display:flex;align-items:center;flex:1;">
                    <div class="addon-check" id="sac_${g.id}"><svg width="10" height="10" fill="none" stroke="#000" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>
                    <span style="font-size:13px;font-weight:600;color:#e5e7eb;">${esc(g.name)}</span>
                </div>
                <span style="font-size:12px;font-weight:700;padding:3px 10px;border-radius:99px;color:${paid?'#4ade80':'#6b7280'};background:${paid?'rgba(74,222,128,.1)':'rgba(255,255,255,.04)'};">${paid?'+₱'+adj.toLocaleString():'Free'}</span>
            </div>`;
        }).join('');
    }
}

function toggleAddonOpt(gid,oid,name,pt,adj){
    document.querySelectorAll(`[data-gid="${gid}"]`).forEach(p=>p.classList.remove('sel'));
    if(curSelAddons[gid]?.optId===oid){delete curSelAddons[gid];updateTotal();return;}
    curSelAddons[gid]={optId:oid,name,priceType:pt,adj};
    document.getElementById(`sao_${gid}_${oid}`)?.classList.add('sel');
    updateTotal();
}
function toggleAddon(gid,name,pt,adj){
    const card=document.getElementById(`sag_${gid}`);
    if(curSelAddons[gid]){delete curSelAddons[gid];card?.classList.remove('sel');}
    else{curSelAddons[gid]={optId:gid,name,priceType:pt,adj};card?.classList.add('sel');}
    updateTotal();
}

function changeQty(d){curQty=Math.max(1,curQty+d);document.getElementById('sQtyVal').textContent=curQty;updateTotal();}

function updateTotal(){
    if(!curItem)return;
    let p=parseFloat(curItem.price);
    Object.values(curSelOpts).forEach(o=>{if(!o)return;if(o.price_type==='add')p+=parseFloat(o.price_adjustment||0);else if(o.price_type==='replace')p=parseFloat(o.price_adjustment||0);});
    Object.values(curSelAddons).forEach(a=>{if(a.priceType==='add')p+=parseFloat(a.adj||0);});
    const unit=Math.round(p);
    document.getElementById('sPrice').textContent='₱'+unit.toLocaleString();
    document.getElementById('sTotal').textContent='₱'+(unit*curQty).toLocaleString();
}

function addToCart(){
    if(!curItem)return;
    // Validate required
    for(const g of (curItem.modifier_groups||[])){
        if(!g.required)continue;
        if(!curSelOpts[g.id]){
            const el=document.getElementById(`sg_${g.id}`);
            const lbl=document.getElementById(`sgl_${g.id}`);
            el?.scrollIntoView({behavior:'smooth',block:'center'});
            if(el){el.style.outline='2px solid #ef4444';el.style.borderRadius='12px';setTimeout(()=>el.style.outline='',2000);}
            if(lbl){const prev=lbl.textContent;lbl.style.color='#ef4444';lbl.textContent='⚠ Required!';setTimeout(()=>{lbl.style.color='';lbl.textContent=prev;},2000);}
            return;
        }
    }
    let p=parseFloat(curItem.price);
    Object.values(curSelOpts).forEach(o=>{if(!o)return;if(o.price_type==='add')p+=parseFloat(o.price_adjustment||0);else if(o.price_type==='replace')p=parseFloat(o.price_adjustment||0);});
    Object.values(curSelAddons).forEach(a=>{if(a.priceType==='add')p+=parseFloat(a.adj||0);});
    const unit=Math.round(p);
    const modifiers=[];
    Object.values(curSelOpts).filter(Boolean).forEach(o=>{if(/^no\s/i.test(o.name))return;const g=(curItem.modifier_groups||[]).find(x=>(x.active_options||[]).find(y=>y.id==o.id));if(g)modifiers.push({type:g.type,name:o.name,price_type:o.price_type,price_adjustment:parseFloat(o.price_adjustment||0)});});
    Object.entries(curSelAddons).forEach(([gid,a])=>{const ag=(curItem.addon_groups||[]).find(x=>x.id==gid);modifiers.push({type:'addon',name:ag&&ag.name!==a.name?ag.name+': '+a.name:a.name,price_type:a.priceType,price_adjustment:parseFloat(a.adj||0)});});
    const optIds=[];Object.values(curSelOpts).filter(Boolean).forEach(o=>o.id&&optIds.push(o.id));
    const addonKey=Object.values(curSelAddons).map(a=>a.optId||a.name).sort().join('a');
    const key=curItem.id+(optIds.length?'_'+optIds.sort().join('-'):'')+(addonKey?'_ad'+addonKey:'');
    const labels=[];Object.values(curSelOpts).filter(Boolean).forEach(o=>o.name&&labels.push(o.name));Object.values(curSelAddons).forEach(a=>labels.push(a.name));
    const name=curItem.name+(labels.length?' ('+labels.join(', ')+')':'');
    const existing=cart.find(i=>i.id===key);
    if(existing)existing.quantity+=curQty;
    else cart.push({id:key,item_id:curItem.id,name,price:unit,image:curItem.image?'/'+curItem.image.replace(/^\//,''):'',quantity:curQty,modifiers});
    updateCartBar();
    closeItemSheet();
    showToast('✓ Added to order');
}

// ── Cart bar ─────────────────────────────────────────────────────────────────
function updateCartBar(){
    const count=cart.reduce((s,i)=>s+i.quantity,0);
    const total=cart.reduce((s,i)=>s+i.price*i.quantity,0);
    const bar=document.getElementById('cartBar');
    document.getElementById('cartCountLabel').textContent=count+' item'+(count!==1?'s':'');
    document.getElementById('cartTotalLabel').textContent='₱'+total.toLocaleString();
    bar.style.display=count>0?'block':'none';
    document.getElementById('cartBadge').textContent=count;
    document.getElementById('cartBadge').style.display=count>0?'flex':'none';
    refreshPlaceOrderBtn();
}

// ── Cart sheet ────────────────────────────────────────────────────────────────
function openCartSheet(){
    renderCartItems();
    document.getElementById('cartBackdrop').classList.add('open');
    document.getElementById('cartSheet').classList.add('open');
    document.body.style.overflow='hidden';
}
function closeCartSheet(){
    document.getElementById('cartBackdrop').classList.remove('open');
    document.getElementById('cartSheet').classList.remove('open');
    document.body.style.overflow='';
}
function renderCartItems(){
    const list=document.getElementById('cartItemsList');
    const total=cart.reduce((s,i)=>s+i.price*i.quantity,0);
    list.innerHTML=cart.map((item,idx)=>`
        <div class="cart-item-row">
            <img src="${esc(item.image||'')}" alt="" class="cart-item-img" onerror="this.src='{{ asset('images/menu/default-menu-item.webp') }}'">
            <div style="flex:1;min-width:0;">
                <p style="font-size:.82rem;font-weight:600;color:#e5e7eb;">${esc(item.name)}</p>
                <p class="ci-unit-price">₱${item.price.toLocaleString()} each · ₱${(item.price*item.quantity).toLocaleString()}</p>
            </div>
            <div class="ci-qty-controls">
                <button class="ci-qty-btn" onclick="changeCartQty(${idx},-1)" aria-label="Decrease quantity">−</button>
                <span class="ci-qty-val">${item.quantity}</span>
                <button class="ci-qty-btn" onclick="changeCartQty(${idx},1)" aria-label="Increase quantity">+</button>
            </div>
            <button class="ci-remove" onclick="removeFromCart(${idx})">✕</button>
        </div>`).join('');
    document.getElementById('cartSheetTotal').textContent='₱'+total.toLocaleString();
}
function removeFromCart(idx){
    cart.splice(idx,1);
    renderCartItems();
    updateCartBar();
    if(cart.length===0)closeCartSheet();
}

function changeCartQty(idx, delta){
    const item = cart[idx];
    if(!item) return;
    item.quantity = Math.min(99, item.quantity + delta);
    if(item.quantity <= 0){
        cart.splice(idx, 1);
    }
    renderCartItems();
    updateCartBar();
    if(cart.length===0)closeCartSheet();
}

// ── Place order ───────────────────────────────────────────────────────────────
async function placeOrder(){
    if(!DINE_IN_OPEN){showError('Dine-In service is currently closed. Orders cannot be placed right now.');return;}
    if(!selectedTable){showError('Please select a table first.');return;}
    if(!cart.length){showError('Cart is empty.');return;}
    const btn=document.getElementById('placeOrderBtn');
    btn.disabled=true;btn.textContent='Placing order…';
    const errEl=document.getElementById('cartError');errEl.style.display='none';
    try{
        const res=await fetch('/orders',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body:JSON.stringify({
                order_type:'dine_in',
                table_number:selectedTable,
                items:cart.map(i=>({id:i.item_id,qty:i.quantity,modifiers:i.modifiers})),
                notes:document.getElementById('orderNotes').value.trim()||null,
                payment_method:'cash',
            }),
        });
        const data=await res.json();
        if(data.success||data.order_id){
            cart=[];
            updateCartBar();
            closeCartSheet();
            showToast('🎉 Order placed for Table '+selectedTable+'!');
            document.getElementById('orderNotes').value='';
            btn.textContent='Place Order';
        } else {
            showError(data.message||'Order failed. Please try again.');
            btn.disabled=false;btn.textContent='Place Order';
        }
    }catch(e){
        showError('Network error. Please try again.');
        btn.disabled=false;btn.textContent='Place Order';
    }
}
function showError(msg){const el=document.getElementById('cartError');el.textContent=msg;el.style.display='block';}

// ── Utils ─────────────────────────────────────────────────────────────────────
function showToast(msg){const t=document.getElementById('toast');t.textContent=msg;t.style.display='block';setTimeout(()=>t.style.display='none',2500);}
function esc(s){return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

// Re-check table select on load (in case browser cached value)
document.addEventListener('DOMContentLoaded',()=>{
    const sel=document.getElementById('tableSelect');
    if(sel.value)onTableChange(sel.value);
});

// Real-time dine-in open/closed status (public channel, no auth needed)
if (window.Echo) {
    window.Echo.channel('shop.status')
        .listen('.shop.status', (data) => {
            setDineInOpen(!!data.is_open_dine_in && !!data.is_open);
        });
}
</script>
@include('partials.ajax-nav')
</body>
</html>
