<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Waiter — EUT Snack House</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/lucide@0.441.0/dist/umd/lucide.min.js" onload="lucide.createIcons()"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0a0a0a; color: #d4d4d4; margin: 0; }

        /* NAV */
        .w-nav { background: #111; border-bottom: 1px solid rgba(34,197,94,.3); padding: 0 1rem; height: 56px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 50; }
        .w-nav-title { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 700; color: #4ade80; display: flex; align-items: center; gap: .5rem; }
        .w-nav-right { display: flex; align-items: center; gap: .75rem; }
        .w-badge { font-size: .7rem; font-weight: 700; background: rgba(34,197,94,.12); color: #4ade80; border: 1px solid rgba(34,197,94,.3); border-radius: 99px; padding: .2rem .6rem; }

        /* CONTENT */
        .w-content { max-width: 1200px; margin: 0 auto; padding: 1.25rem 1rem 5rem; }

        /* STATS */
        .w-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: .75rem; margin-bottom: 1.25rem; }
        @media(min-width: 600px) { .w-stats { grid-template-columns: repeat(4, 1fr); } }
        .w-stat { background: #161616; border: 1px solid rgba(255,255,255,.07); border-radius: .75rem; padding: 1rem; text-align: center; }
        .w-stat-val { font-size: 1.6rem; font-weight: 800; line-height: 1; margin-bottom: .25rem; }
        .w-stat-lbl { font-size: .7rem; color: #737373; text-transform: uppercase; letter-spacing: .05em; }

        /* TABLE GRID */
        .w-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: .875rem; }
        .w-card { background: #161616; border: 1px solid rgba(255,255,255,.07); border-radius: .875rem; overflow: hidden; transition: border-color .2s; }
        .w-card.status-pending    { border-color: rgba(245,158,11,.4); }
        .w-card.status-accepted   { border-color: rgba(59,130,246,.4); }
        .w-card.status-preparing  { border-color: rgba(239,68,68,.4); }
        .w-card.status-delivered  { border-color: rgba(34,197,94,.3); opacity: .7; }

        .w-card-head { padding: .875rem 1rem .6rem; display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
        .w-table-badge { font-size: 1rem; font-weight: 800; color: #4ade80; background: rgba(74,222,128,.12); border: 1px solid rgba(74,222,128,.3); border-radius: .5rem; padding: .25rem .75rem; }
        .w-status-chip { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; padding: .2rem .55rem; border-radius: 99px; }
        .chip-pending   { background: rgba(245,158,11,.12); color: #f59e0b; }
        .chip-accepted  { background: rgba(59,130,246,.12); color: #60a5fa; }
        .chip-preparing { background: rgba(239,68,68,.12); color: #f87171; }
        .chip-ready     { background: rgba(34,197,94,.15); color: #4ade80; animation: pulse-green 1.5s ease-in-out infinite; }
        .chip-delivered { background: rgba(34,197,94,.08); color: #4ade80; }
        @keyframes pulse-green { 0%,100%{opacity:1} 50%{opacity:.6} }

        .w-card-meta { padding: 0 1rem .5rem; font-size: .75rem; color: #737373; display: flex; gap: .75rem; }
        .w-card-items { padding: 0 1rem .75rem; border-top: 1px solid rgba(255,255,255,.05); margin-top: .25rem; }
        .w-item { display: flex; justify-content: space-between; align-items: baseline; padding: .4rem 0; border-bottom: 1px solid rgba(255,255,255,.04); font-size: .8rem; }
        .w-item:last-child { border-bottom: none; }
        .w-item-name { color: #e5e7eb; font-weight: 600; }
        .w-item-qty  { color: #737373; font-size: .72rem; margin-left: .35rem; }
        .w-item-mods { font-size: .7rem; color: #737373; margin-top: .1rem; }
        .w-item-price { color: #facc15; font-weight: 700; font-size: .78rem; flex-shrink: 0; }

        .w-card-notes { margin: 0 1rem .75rem; background: rgba(245,158,11,.06); border: 1px solid rgba(245,158,11,.18); border-radius: .5rem; padding: .5rem .75rem; font-size: .75rem; color: #d97706; }

        .w-card-foot { padding: .6rem 1rem .875rem; display: flex; gap: .5rem; border-top: 1px solid rgba(255,255,255,.05); }
        .w-btn { flex: 1; padding: .6rem .5rem; border-radius: .625rem; font-size: .78rem; font-weight: 700; cursor: pointer; border: none; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: .35rem; }
        .w-btn-serve  { background: linear-gradient(135deg, #16a34a, #22c55e); color: #000; }
        .w-btn-serve:hover { opacity: .9; transform: translateY(-1px); }
        .w-btn-serve:disabled { opacity: .4; cursor: not-allowed; transform: none; }
        .w-btn-bill   { background: rgba(250,204,21,.1); border: 1px solid rgba(250,204,21,.3); color: #facc15; }
        .w-btn-bill:hover { background: rgba(250,204,21,.18); }
        .w-btn-print  { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); color: #9ca3af; flex: 0 0 auto; width: 2.5rem; }
        .w-btn-print:hover { background: rgba(255,255,255,.12); color: #fff; }

        .w-empty { text-align: center; padding: 4rem 1rem; color: #737373; }
        .w-empty-icon { font-size: 3rem; margin-bottom: .75rem; }

        /* SEARCH */
        .w-search-wrap { position: relative; margin-bottom: 1rem; }
        .w-search-input {
            width: 100%; padding: .65rem 2.75rem .65rem 2.75rem;
            background: #161616; border: 1px solid rgba(255,255,255,.1);
            border-radius: .75rem; color: #e5e7eb; font-size: .875rem;
            outline: none; transition: border-color .2s, box-shadow .2s;
        }
        .w-search-input::placeholder { color: #525252; }
        .w-search-input:focus { border-color: rgba(74,222,128,.45); box-shadow: 0 0 0 3px rgba(74,222,128,.1); }
        .w-search-icon { position: absolute; left: .85rem; top: 50%; transform: translateY(-50%); color: #525252; pointer-events: none; display: flex; }
        .w-search-clear { position: absolute; right: .75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #525252; cursor: pointer; display: none; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; border-radius: 50%; padding: 0; }
        .w-search-clear:hover { color: #e5e7eb; background: rgba(255,255,255,.08); }

        /* TOAST */
        .w-toast { position: fixed; bottom: 5rem; left: 50%; transform: translateX(-50%); background: #1a1b2e; border: 1px solid rgba(74,222,128,.3); color: #4ade80; padding: .625rem 1.25rem; border-radius: 99px; font-size: .8rem; font-weight: 600; z-index: 9999; pointer-events: none; opacity: 0; transition: opacity .3s; }
        .w-toast.show { opacity: 1; }

        /* BOTTOM NAV */
        .w-bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(10,10,10,.98); border-top: 1px solid rgba(255,255,255,.07); display: flex; z-index: 40; }
        .w-bnav-item { position: relative; flex: 1; display: flex; flex-direction: column; align-items: center; gap: .2rem; padding: .625rem 0 .75rem; color: #737373; font-size: .65rem; font-weight: 600; text-decoration: none; cursor: pointer; background: none; border: none; }
        .w-bnav-item.active { color: #4ade80; }
        .w-bnav-badge { background: #ef4444; color: #fff; border-radius: 99px; font-size: .6rem; font-weight: 800; padding: 1px 5px; min-width: 16px; text-align: center; position: absolute; top: 6px; right: calc(50% - 18px); }
    </style>
</head>
<body>

{{-- NAV --}}
<nav class="w-nav">
    <div class="w-nav-title">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        Waiter Panel
    </div>
    <div class="w-nav-right">
        <span class="w-badge" id="liveIndicator">● Live</span>
        <span style="font-size:.78rem;color:#9ca3af;">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('auth.logout') }}" style="margin:0;">
            @csrf
            <button type="submit" style="background:none;border:none;cursor:pointer;color:#737373;font-size:.75rem;padding:.25rem .5rem;">Logout</button>
        </form>
    </div>
</nav>

{{-- CONTENT --}}
<div class="w-content">

    {{-- Stats --}}
    <div class="w-stats">
        <div class="w-stat" style="border-color:rgba(245,158,11,.25);">
            <div class="w-stat-val" style="color:#f59e0b;" id="stat-pending">—</div>
            <div class="w-stat-lbl">Pending</div>
        </div>
        <div class="w-stat" style="border-color:rgba(239,68,68,.25);">
            <div class="w-stat-val" style="color:#f87171;" id="stat-preparing">—</div>
            <div class="w-stat-lbl">Preparing</div>
        </div>
        <div class="w-stat" style="border-color:rgba(34,197,94,.25);">
            <div class="w-stat-val" style="color:#4ade80;" id="stat-ready">—</div>
            <div class="w-stat-lbl">Ready to Serve</div>
        </div>
        <div class="w-stat" style="border-color:rgba(99,102,241,.25);">
            <div class="w-stat-val" style="color:#818cf8;" id="stat-tables">—</div>
            <div class="w-stat-lbl">Active Tables</div>
        </div>
    </div>

    {{-- Search --}}
    <div class="w-search-wrap">
        <span class="w-search-icon">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
        <input type="text" id="waiterSearch" class="w-search-input" placeholder="Search table, customer, item…" oninput="onSearchInput()">
        <button class="w-search-clear" id="waiterSearchClear" onclick="clearWaiterSearch()" title="Clear">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Order Cards --}}
    <div id="waiterGrid" class="w-grid">
        <div class="w-empty"><div class="w-empty-icon">🍽️</div><p>Loading tables…</p></div>
    </div>

</div>

{{-- Bottom Nav --}}
<div class="w-bottom-nav">
    <button class="w-bnav-item active" onclick="filterOrders('all', this)">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18M3 12h18M3 18h18"/></svg>
        All Tables
    </button>
    <button class="w-bnav-item" onclick="filterOrders('ready', this)">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Ready
        <span id="readyBadge" style="display:none;" class="w-bnav-badge">0</span>
    </button>
    <button class="w-bnav-item" onclick="filterOrders('pending', this)">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg>
        Pending
    </button>
    <a href="{{ route('waiter.order') }}" class="w-bnav-item">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
        Menu
    </a>
</div>

{{-- Toast --}}
<div id="waiterToast" class="w-toast"></div>

<script>
const CSRF   = document.querySelector('meta[name="csrf-token"]').content;
const ORDERS_URL = '{{ route("waiter.orders") }}';
const SERVE_URL  = id => `/waiter/orders/${id}/serve`;
const BILL_URL   = id => `/waiter/orders/${id}/request-bill`;

let allOrders   = [];
let currentFilter = 'all';
let searchQuery   = '';
let searchDebounce = null;
let pollTimer   = null;
const POLL_MS   = 6000;

// ── Fetch & render ──────────────────────────────────────────────────────────
async function fetchOrders() {
    try {
        const res  = await fetch(ORDERS_URL, { headers: { Accept: 'application/json' } });
        const data = await res.json();
        allOrders  = data.orders || [];
        renderStats();
        renderGrid();
        document.getElementById('liveIndicator').style.color = '#4ade80';
    } catch (e) {
        document.getElementById('liveIndicator').style.color = '#ef4444';
        document.getElementById('liveIndicator').textContent = '● Offline';
    }
}

function renderStats() {
    const pending   = allOrders.filter(o => o.status === 'pending').length;
    const preparing = allOrders.filter(o => ['accepted','preparing'].includes(o.status)).length;
    const ready     = allOrders.filter(o => o.status === 'preparing' && o.prepared_at).length;
    const tables    = new Set(allOrders.map(o => o.table_number).filter(Boolean)).size;

    document.getElementById('stat-pending').textContent   = pending;
    document.getElementById('stat-preparing').textContent = preparing;
    document.getElementById('stat-ready').textContent     = ready;
    document.getElementById('stat-tables').textContent    = tables;

    // Ready badge on bottom nav
    const badge = document.getElementById('readyBadge');
    badge.textContent     = ready;
    badge.style.display   = ready > 0 ? 'block' : 'none';
}

function filterOrders(f, btn) {
    currentFilter = f;
    document.querySelectorAll('.w-bnav-item').forEach(b => b.classList.remove('active'));
    btn?.classList.add('active');
    renderGrid();
}

function onSearchInput() {
    clearTimeout(searchDebounce);
    searchDebounce = setTimeout(() => {
        searchQuery = document.getElementById('waiterSearch').value.trim().toLowerCase();
        const clearBtn = document.getElementById('waiterSearchClear');
        clearBtn.style.display = searchQuery ? 'flex' : 'none';
        renderGrid();
    }, 200);
}

function clearWaiterSearch() {
    document.getElementById('waiterSearch').value = '';
    document.getElementById('waiterSearchClear').style.display = 'none';
    searchQuery = '';
    renderGrid();
}

function renderGrid() {
    const grid = document.getElementById('waiterGrid');
    let orders = allOrders;

    if (currentFilter === 'ready')   orders = orders.filter(o => o.status === 'preparing' && o.prepared_at);
    if (currentFilter === 'pending') orders = orders.filter(o => o.status === 'pending');

    // Search filter
    if (searchQuery) {
        orders = orders.filter(o => {
            const haystack = [
                o.table_number ?? '',
                o.customer ?? '',
                o.order_number ?? '',
                ...(o.items || []).map(i => i.name ?? ''),
            ].join(' ').toLowerCase();
            return haystack.includes(searchQuery);
        });
    }

    // Sort: ready first, then by table number
    orders = [...orders].sort((a, b) => {
        const ap = a.status === 'preparing' && a.prepared_at ? 0 : 1;
        const bp = b.status === 'preparing' && b.prepared_at ? 0 : 1;
        if (ap !== bp) return ap - bp;
        return (a.table_number ?? '').localeCompare(b.table_number ?? '', undefined, { numeric: true });
    });

    if (!orders.length) {
        const emptyMsg = searchQuery
            ? `No results for "${esc(searchQuery)}".`
            : currentFilter === 'ready' ? 'No orders ready to serve.'
            : currentFilter === 'pending' ? 'No pending orders.'
            : 'No active dine-in tables.';
        grid.innerHTML = `<div class="w-empty" style="grid-column:1/-1;">
            <div class="w-empty-icon">${searchQuery ? '🔍' : '🍽️'}</div>
            <p>${emptyMsg}</p>
        </div>`;
        return;
    }

    grid.innerHTML = orders.map(o => renderCard(o)).join('');
    if (window.lucide) lucide.createIcons();
}

function renderCard(o) {
    const isReady    = o.status === 'preparing' && o.prepared_at;
    const isServed   = o.status === 'delivered';
    const chipClass  = isReady ? 'chip-ready' : `chip-${o.status}`;
    const chipLabel  = isReady ? '✓ Ready to Serve' : o.status_label;
    const cardClass  = isServed ? 'status-delivered' : isReady ? 'status-preparing' : `status-${o.status}`;

    const itemsHtml = o.items.map(i => `
        <div class="w-item">
            <div>
                <span class="w-item-name">${esc(i.name)}</span>
                <span class="w-item-qty">${i.qty}×</span>
                ${i.modifiers?.length ? `<div class="w-item-mods">${i.modifiers.map(m=>esc(m.name||m)).join(', ')}</div>` : ''}
            </div>
            <span class="w-item-price">₱${parseFloat(i.subtotal).toLocaleString()}</span>
        </div>`).join('');

    const notesHtml = o.notes
        ? `<div class="w-card-notes">📝 ${esc(o.notes)}</div>` : '';

    const isPending = o.status === 'pending';

    const footHtml = isPending
        ? `<div class="w-card-foot" style="justify-content:center;color:#737373;font-size:.75rem;padding:.7rem 1rem .875rem;">
            ⏳ Waiting for kitchen to accept order
        </div>`
        : '';

    return `<div class="w-card ${cardClass}" id="wcard_${o.id}">
        <div class="w-card-head">
            <span class="w-table-badge">🪩 Table ${esc(o.table_number ?? '—')}</span>
            <span class="w-status-chip ${chipClass}">${chipLabel}</span>
        </div>
        <div class="w-card-meta">
            <span>#${esc(o.order_number)}</span>
            <span>${esc(o.customer)}</span>
            <span>🕐 ${esc(o.placed_at)}</span>
        </div>
        <div class="w-card-items">${itemsHtml}</div>
        ${notesHtml}
        <div style="padding:0 1rem .5rem;display:flex;justify-content:flex-end;">
            <span style="font-size:.9rem;font-weight:800;color:#facc15;">₱${parseFloat(o.total).toLocaleString()}</span>
        </div>
        ${footHtml}
    </div>`;
}

// ── Actions ─────────────────────────────────────────────────────────────────
async function serveOrder(id, btn) {
    btn.disabled  = true;
    btn.innerHTML = 'Serving…';
    try {
        const res  = await fetch(SERVE_URL(id), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, Accept: 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            showToast('✓ ' + data.message);
            await fetchOrders();
        } else {
            showToast('⚠ ' + (data.message || 'Failed'), true);
            btn.disabled = false;
            btn.innerHTML = 'Mark Served';
        }
    } catch(e) {
        showToast('Network error', true);
        btn.disabled = false;
        btn.innerHTML = 'Mark Served';
    }
}

async function requestBill(id, table, btn) {
    btn.disabled = true;
    try {
        const res  = await fetch(BILL_URL(id), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, Accept: 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();
        showToast(data.success ? `🧾 Bill requested for Table ${table}` : (data.message || 'Failed'), !data.success);
    } catch(e) {
        showToast('Network error', true);
    }
    btn.disabled = false;
}

function printReceipt(url) {
    window.open(url, '_blank', 'width=340,height=600,menubar=no,toolbar=no');
}

// ── Toast ────────────────────────────────────────────────────────────────────
function showToast(msg, isError = false) {
    const t = document.getElementById('waiterToast');
    t.textContent   = msg;
    t.style.borderColor = isError ? 'rgba(239,68,68,.4)' : 'rgba(74,222,128,.3)';
    t.style.color       = isError ? '#f87171' : '#4ade80';
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// ── Utils ────────────────────────────────────────────────────────────────────
function esc(s) {
    return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Boot ─────────────────────────────────────────────────────────────────────
fetchOrders();
pollTimer = setInterval(fetchOrders, POLL_MS);

// Echo real-time updates
if (window.Echo) {
    window.Echo.private('kitchen')
        .listen('.order.updated', () => fetchOrders());
}
</script>

@include('partials.pwa-register')
@include('partials.ajax-nav')

{{-- ══════════ MAINTENANCE MODE (Waiter) ══════════
     To enable:  set $maintenance = true  below
     To disable: set $maintenance = false
--}}
@php $maintenance = true; @endphp

@if($maintenance)
<style>
#maint-screen{
    position:fixed;inset:0;z-index:99999;
    background:#0a0a0a;
    display:flex;flex-direction:column;align-items:center;justify-content:center;
    gap:1.25rem;padding:2rem;text-align:center;
    font-family:'Inter',sans-serif;
}
#maint-screen .m-icon{
    width:80px;height:48px;border-radius:.75rem;
    background:#111827;border:1px solid #1f2937;
    display:flex;align-items:center;justify-content:center;
    margin-bottom:.25rem;padding:0 .75rem;
}
#maint-screen .m-icon svg{display:block;}
#maint-screen .m-brand{font-size:.7rem;color:#374151;letter-spacing:.1em;text-transform:uppercase;font-weight:500;}
#maint-screen h1{margin:0;font-size:1.5rem;font-weight:700;color:#f9fafb;letter-spacing:-.02em;line-height:1.2;}
#maint-screen p{margin:0;font-size:.875rem;color:#6b7280;max-width:320px;line-height:1.7;}
#maint-screen .m-timer{font-family:monospace;font-size:2.75rem;font-weight:800;color:#f59e0b;line-height:1;letter-spacing:.02em;}
#maint-screen .m-note{font-size:.72rem;color:#1f2937;}
</style>
<div id="maint-screen">
    <div class="m-icon">
        {{-- AWS Logo --}}
        <svg width="38" height="23" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.8 16.2L11.2 27.8H11L7.3 16.2H4.8L9.6 29.8H12.5L17.3 16.2H14.8Z" fill="#FF9900"/>
            <path d="M24.5 16C21.2 16 18.8 18.3 18.8 23C18.8 27.5 21.1 30 24.6 30C26.8 30 28.4 29.1 29.5 27.6L27.9 26.2C27.1 27.2 26.1 27.8 24.7 27.8C22.8 27.8 21.5 26.6 21.3 24.4H29.9C29.9 24.1 30 23.6 30 23.1C30 18.9 27.8 16 24.5 16ZM21.3 22.4C21.5 20.4 22.7 18.2 24.5 18.2C26.4 18.2 27.5 20.2 27.6 22.4H21.3Z" fill="#FF9900"/>
            <path d="M38.2 22.5C36.5 22 35 21.6 35 20.5C35 19.5 36 18.9 37.3 18.9C38.7 18.9 39.9 19.6 40.7 20.5L42.3 19C41.1 17.6 39.4 16.7 37.3 16.7C34.5 16.7 32.5 18.3 32.5 20.7C32.5 23.3 34.8 24 36.8 24.6C38.5 25.1 40 25.5 40 26.7C40 27.8 39 28.4 37.5 28.4C35.8 28.4 34.4 27.5 33.5 26.4L31.8 27.9C33.1 29.4 35 30.3 37.5 30.3C40.5 30.3 42.5 28.7 42.5 26.4C42.5 23.7 40.1 23 38.2 22.5Z" fill="#FF9900"/>
            <path d="M12 36C16.5 39.5 22.5 41.5 29 41.5C37.5 41.5 45.2 38.2 50.8 32.8C51.5 32.1 50.9 31.1 50 31.5C44.2 34 37.5 35.5 30.5 35.5C23 35.5 15.8 33.7 9.5 30.5C8.6 30 8 31.2 8.8 31.8L12 36Z" fill="#FF9900"/>
            <path d="M52.5 30.5C53.5 29.3 56.5 29.8 57 31C57.5 32.2 54.8 35.5 53.5 34.8C52.8 34.5 51.5 31.7 52.5 30.5Z" fill="#FF9900"/>
        </svg>
    </div>
    <span class="m-brand">AWS Server</span>
    <h1>We'll be right back.</h1>
    <p>Our AWS server is currently down for scheduled maintenance.<br>Estimated back online in:</p>
    <div class="m-timer" id="maint-timer-w">5:00</div>
    <p class="m-note" id="maint-note-w">This page refreshes automatically.</p>
</div>
<script>
(function(){
    var end = Date.now() + 5*60*1000;
    function fmt(ms){var s=Math.max(0,Math.ceil(ms/1000));return Math.floor(s/60)+':'+(('0'+(s%60)).slice(-2));}
    var timer = document.getElementById('maint-timer-w');
    var note  = document.getElementById('maint-note-w');
    var desc  = timer && timer.previousElementSibling;
    var done  = false;
    setInterval(function(){
        if(done) return;
        var rem = end - Date.now();
        if(rem > 0){
            if(timer) timer.textContent = fmt(rem);
        } else {
            done = true;
            if(timer){ timer.style.fontSize='.875rem'; timer.style.color='#ef4444'; timer.style.letterSpacing='.02em'; timer.style.fontFamily='inherit'; timer.style.fontWeight='600'; timer.textContent='Service Disrupted'; }
            if(desc)  desc.textContent = 'The AWS server is taking longer than expected to recover.';
            if(note)  { note.style.color='#6b7280'; note.style.fontSize='.8rem'; note.textContent='This may take up to 24 hours. We apologize for the inconvenience — please check back later.'; }
        }
    }, 500);
})();
</script>
@endif
</body>
</html>
