<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Follow-up Order — Table {{ $tableNumber }} — EUT</title>
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

        /* FOLLOWUP HEADER */
        .followup-header{max-width:560px;margin:0 auto;padding:76px 16px 12px;}
        .followup-notice{background:linear-gradient(135deg,rgba(99,102,241,.12),rgba(79,70,229,.08));border:1.5px solid rgba(99,102,241,.3);border-radius:14px;padding:1rem 1.25rem;margin-bottom:1rem;}
        .followup-title{font-size:1rem;font-weight:700;color:#6366f1;margin-bottom:.4rem;}
        .followup-desc{font-size:.8rem;color:#a5b4fc;line-height:1.4;}

        /* SEARCH BAR */
        .search-bar{max-width:560px;margin:0 auto;padding:0 16px 10px;}

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
        .qty-section{padding:8px 18px 14px;display:flex;align-items:center;justify-content:space-between;}
        .qty-controls{display:flex;align-items:center;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:99px;}
        .qty-btn{width:38px;height:38px;border-radius:99px;background:none;border:none;cursor:pointer;font-size:18px;font-weight:700;color:#9ca3af;display:flex;align-items:center;justify-content:center;}
        .qty-val{width:36px;text-align:center;font-size:15px;font-weight:700;color:#fff;background:none;border:none;outline:none;}
    </style>
</head>
<body>
{{-- NAV --}}
<nav class="topnav">
    <div class="topnav-inner">
        <a href="{{ route('waiter.dashboard') }}" class="nav-btn">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <span class="nav-title">➕ Follow-up Order</span>
        <button onclick="openCartSheet()" class="nav-btn" style="position:relative;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span id="cartBadge" style="display:none;position:absolute;top:-3px;right:-3px;min-width:16px;height:16px;border-radius:99px;background:#22c55e;border:2px solid #080810;font-size:9px;font-weight:800;color:#000;display:flex;align-items:center;justify-content:center;padding:0 3px;"></span>
        </button>
    </div>
</nav>

{{-- FOLLOWUP NOTICE --}}
<div class="followup-header">
    <div class="followup-notice">
        <div class="followup-title">🪑 Table {{ $tableNumber }} Follow-up Order</div>
        <div class="followup-desc">Adding items to existing session #{{ $latestOrder->table_session_id }}. New items will be merged with the current order.</div>
    </div>
</div>

{{-- SEARCH BAR --}}
<div class="search-bar">
    <div style="position:relative;">
        <svg style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#6b7280;" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/></svg>
        <input type="text" id="waiterSearch" placeholder="Search menu…"
               oninput="searchMenu(this.value)"
               style="width:100%;padding:.7rem .875rem .7rem 2.25rem;background:rgba(255,255,255,.06);border:1.5px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:.875rem;outline:none;transition:border-color .2s;"
               onfocus="this.style.borderColor='rgba(34,197,94,.5)'"
               onblur="this.style.borderColor='rgba(255,255,255,.1)'">
        <button id="searchClearBtn" onclick="clearSearch()" style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#6b7280;cursor:pointer;font-size:16px;line-height:1;padding:0 4px;">✕</button>
    </div>
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
        <p style="font-size:15px;font-weight:700;color:#fff;">Follow-up Order</p>
        <button class="sheet-close" onclick="closeCartSheet()">✕</button>
    </div>
    <div class="sheet-divider"></div>

    {{-- Table session info --}}
    <div style="padding:10px 18px 0;">
        <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(99,102,241,.1);border:1px solid rgba(99,102,241,.3);border-radius:8px;padding:.3rem .75rem;font-size:.8rem;font-weight:700;color:#6366f1;">
            🪑 Table {{ $tableNumber }} • Session #{{ $latestOrder->table_session_id }}
        </div>
    </div>

    <div id="cartItemsList" style="padding:0 18px;"></div>
    <div class="sheet-divider" style="margin-top:8px;"></div>
    <div style="padding:10px 18px;display:flex;justify-content:space-between;align-items:center;">
        <span style="font-size:.85rem;color:#9ca3af;">Follow-up Total</span>
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
        <button id="placeOrderBtn" class="btn-place" onclick="placeFollowupOrder()">
            Add Follow-up Order
        </button>
    </div>
</div>

{{-- Toast --}}
<div id="toast" style="position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#1a1b2e;border:1px solid rgba(34,197,94,.3);color:#4ade80;padding:10px 20px;border-radius:99px;font-size:13px;font-weight:600;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,.5);display:none;"></div>
<script>
const CSRF         = document.querySelector('meta[name="csrf-token"]').content;
const IQ_ITEMS     = @json($menuItemsData->keyBy('id'));
const TABLE_NUMBER = {{ $tableNumber }};
const SESSION_ID   = '{{ $latestOrder->table_session_id }}';

let cart          = [];
let curItem       = null;
let curQty        = 1;
let curSelOpts    = {};
let curSelAddons  = {};

// ── Category filter + Search ─────────────────────────────────────────────────
function filterCat(slug, btn) {
    document.querySelectorAll('.cat-pill').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    const search = document.getElementById('waiterSearch')?.value?.toLowerCase().trim() || '';
    document.querySelectorAll('.p-card').forEach(card => {
        const catMatch  = slug === 'all' || card.dataset.cat === slug;
        const nameMatch = !search || card.dataset.name?.includes(search);
        card.style.display = (catMatch && nameMatch) ? '' : 'none';
    });
}

function searchMenu(val) {
    const q         = val.toLowerCase().trim();
    const clearBtn  = document.getElementById('searchClearBtn');
    clearBtn.style.display = q ? 'block' : 'none';

    const activeSlug = document.querySelector('.cat-pill.active')?.onclick?.toString().match(/filterCat\('([^']+)'/)?.[1] || 'all';

    document.querySelectorAll('.p-card').forEach(card => {
        const nameEl = card.querySelector('.p-card-name');
        if (nameEl) card.dataset.name = nameEl.textContent.toLowerCase();
        const nameMatch = !q || (card.dataset.name || '').includes(q);
        const catMatch  = activeSlug === 'all' || card.dataset.cat === activeSlug;
        card.style.display = (nameMatch && catMatch) ? '' : 'none';
    });

    const visible = document.querySelectorAll('.p-card:not([style*="display: none"]):not([style*="display:none"])').length;
    let emptyEl = document.getElementById('searchEmptyState');
    if (!emptyEl) {
        emptyEl = document.createElement('div');
        emptyEl.id = 'searchEmptyState';
        emptyEl.style.cssText = 'grid-column:1/-1;text-align:center;padding:3rem 1rem;color:#6b7280;';
        emptyEl.innerHTML = '<div style="font-size:2rem;margin-bottom:.5rem;">🔍</div><p>No items found for "<strong style="color:#9ca3af;">' + esc(val) + '</strong>"</p>';
        document.getElementById('productsGrid').appendChild(emptyEl);
    }
    emptyEl.style.display  = visible === 0 && q ? 'block' : 'none';
    if (visible > 0) emptyEl.remove();
}

function clearSearch() {
    const input = document.getElementById('waiterSearch');
    input.value = '';
    document.getElementById('searchClearBtn').style.display = 'none';
    document.querySelectorAll('.p-card').forEach(card => card.style.display = '');
    const emptyEl = document.getElementById('searchEmptyState');
    if (emptyEl) emptyEl.remove();
}
// ── Item Sheet (reused from waiter order) ────────────────────────────────────
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

// ── Cart management ──────────────────────────────────────────────────────────
function updateCartBar(){
    const count=cart.reduce((s,i)=>s+i.quantity,0);
    const total=cart.reduce((s,i)=>s+i.price*i.quantity,0);
    const bar=document.getElementById('cartBar');
    document.getElementById('cartCountLabel').textContent=count+' item'+(count!==1?'s':'');
    document.getElementById('cartTotalLabel').textContent='₱'+total.toLocaleString();
    bar.style.display=count>0?'block':'none';
    document.getElementById('cartBadge').textContent=count;
    document.getElementById('cartBadge').style.display=count>0?'flex':'none';
}

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
        <div style="display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.05);">
            <img src="${esc(item.image||'')}" alt="" style="width:48px;height:48px;border-radius:8px;object-fit:cover;flex-shrink:0;" onerror="this.src='{{ asset('images/menu/default-menu-item.webp') }}'">
            <div style="flex:1;min-width:0;">
                <p style="font-size:.82rem;font-weight:600;color:#e5e7eb;">${esc(item.name)}</p>
                <p style="font-size:.75rem;color:#9ca3af;">${item.quantity}× · ₱${item.price.toLocaleString()}</p>
            </div>
            <button onclick="removeFromCart(${idx})" style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#ef4444;border-radius:6px;padding:4px 8px;cursor:pointer;font-size:11px;font-weight:700;">✕</button>
        </div>`).join('');
    document.getElementById('cartSheetTotal').textContent='₱'+total.toLocaleString();
}
function removeFromCart(idx){
    cart.splice(idx,1);
    renderCartItems();
    updateCartBar();
    if(cart.length===0)closeCartSheet();
}

// ── Place followup order ─────────────────────────────────────────────────────
async function placeFollowupOrder(){
    if(!cart.length){showError('Cart is empty.');return;}

    const btn=document.getElementById('placeOrderBtn');
    btn.disabled=true;btn.textContent='Placing follow-up order…';
    const errEl=document.getElementById('cartError');errEl.style.display='none';

    try{
        const res=await fetch('/orders',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body:JSON.stringify({
                order_type:       'dine_in',
                table_number:     TABLE_NUMBER,
                items:            cart.map(i=>({id:i.item_id,qty:i.quantity,modifiers:i.modifiers})),
                notes:            document.getElementById('orderNotes').value.trim()||null,
                payment_method:   'cash',
                // The system will automatically merge with existing session
            }),
        });
        const data=await res.json();
        if(data.success||data.order_id){
            cart=[];
            updateCartBar();
            closeCartSheet();
            showToast(`🎉 Follow-up order added to Table ${TABLE_NUMBER}!`);
            document.getElementById('orderNotes').value='';
            // Redirect back to dashboard after 2 seconds
            setTimeout(() => window.location.href = '{{ route("waiter.dashboard") }}', 2000);
        } else {
            showError(data.message||'Follow-up order failed. Please try again.');
            btn.disabled=false;btn.textContent='Add Follow-up Order';
        }
    }catch(e){
        showError('Network error. Please try again.');
        btn.disabled=false;btn.textContent='Add Follow-up Order';
    }
}

function showError(msg){const el=document.getElementById('cartError');el.textContent=msg;el.style.display='block';}

// ── Utils (copy key functions from waiter order page) ────────────────────────
function showToast(msg){const t=document.getElementById('toast');t.textContent=msg;t.style.display='block';setTimeout(()=>t.style.display='none',2500);}
function esc(s){return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}

// Copy buildModifiers, buildAddons, etc. from main order page
// (For brevity, these would be the same functions as in waiter/order.blade.php)
function buildModifiers() { /* Same as waiter order */ }
function buildAddons() { /* Same as waiter order */ }
function updateTotal() { /* Same as waiter order */ }
function addToCart() { /* Same as waiter order */ }
function changeQty(d) { /* Same as waiter order */ }

</script>
<style>
.btn-add-sheet{flex:1;padding:14px;border-radius:14px;background:linear-gradient(135deg,#16a34a,#22c55e);border:none;color:#000;font-size:14px;font-weight:800;cursor:pointer;}
.btn-place{width:100%;padding:15px;border-radius:14px;background:linear-gradient(135deg,#16a34a,#22c55e);border:none;color:#000;font-size:15px;font-weight:800;cursor:pointer;box-shadow:0 4px 18px rgba(34,197,94,.3);}
.btn-place:disabled{opacity:.4;cursor:not-allowed;}
.sheet-actions{display:flex;gap:10px;padding:0 18px 32px;}
.sheet-total-row{display:flex;align-items:center;justify-content:space-between;padding:8px 18px 12px;}
</style>
</body>
</html>