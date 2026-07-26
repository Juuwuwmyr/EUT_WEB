@extends('chef.layout')
@section('title', 'Kitchen Dashboard')

@push('head')
<style>
    .kitchen-board {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        min-height: calc(100vh - 220px);
    }
    @media (max-width: 1200px) {
        .kitchen-board { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .kitchen-board { grid-template-columns: 1fr; }
    }

    .kitchen-col {
        background: var(--bg-section);
        border: 1px solid var(--border-section);
        border-radius: 1rem;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 420px;
    }

    .kitchen-col-header {
        padding: 1rem 1.1rem;
        border-bottom: 1px solid var(--border-divider);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }

    .kitchen-col-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .9rem;
        font-weight: 700;
        color: var(--text-strong);
        margin: 0;
    }

    .kitchen-col-count {
        min-width: 1.75rem;
        height: 1.75rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
        font-weight: 800;
    }

    .kitchen-col-body {
        flex: 1;
        padding: .85rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .kitchen-col-body::-webkit-scrollbar { width: 4px; }
    .kitchen-col-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 99px; }

    .k-order-card {
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: .875rem;
        overflow: hidden;
        transition: transform .15s, box-shadow .15s;
        animation: kSlideIn .25s ease;
    }
    .k-order-card:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,.25); }
    .k-order-card.is-urgent { border-color: rgba(239,68,68,.45); box-shadow: 0 0 0 1px rgba(239,68,68,.15); }
    .k-order-card.is-picked-up { opacity: .75; border-color: rgba(139,92,246,.35); }

    .k-delivery-banner {
        margin: 0;
        padding: .55rem 1rem;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .03em;
        text-transform: uppercase;
        border-bottom: 1px solid var(--border-divider);
    }

    @keyframes kSlideIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .k-card-top {
        padding: .85rem 1rem .65rem;
        border-bottom: 1px solid var(--border-divider);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .5rem;
    }

    .k-order-num {
        font-family: monospace;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: .02em;
    }

    .k-elapsed {
        font-size: .68rem;
        font-weight: 700;
        padding: .2rem .55rem;
        border-radius: 9999px;
        white-space: nowrap;
    }

    .k-customer {
        font-size: .78rem;
        color: var(--text-muted);
        margin-top: .15rem;
    }

    .k-items {
        padding: .65rem 1rem;
        display: flex;
        flex-direction: column;
        gap: .55rem;
    }

    .k-item {
        display: flex;
        align-items: flex-start;
        gap: .6rem;
    }

    .k-item-img {
        width: 42px;
        height: 42px;
        border-radius: .5rem;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid var(--border-divider);
    }

    .k-item-qty {
        font-size: .85rem;
        font-weight: 800;
        color: #facc15;
        min-width: 1.5rem;
    }

    .k-item-name {
        font-size: .88rem;
        font-weight: 700;
        color: var(--text-strong);
        line-height: 1.35;
    }

    .k-modifiers {
        display: flex;
        flex-wrap: wrap;
        gap: .25rem;
        margin-top: .25rem;
    }

    .k-mod-tag {
        font-size: .62rem;
        font-weight: 600;
        padding: .15rem .45rem;
        border-radius: .35rem;
        background: rgba(59,130,246,.12);
        color: #60a5fa;
        border: 1px solid rgba(59,130,246,.2);
    }

    .k-notes {
        margin: 0 1rem .65rem;
        padding: .55rem .7rem;
        border-radius: .5rem;
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.2);
        font-size: .72rem;
        color: #fbbf24;
        line-height: 1.45;
    }

    .k-actions {
        padding: .65rem 1rem .85rem;
        display: flex;
        gap: .5rem;
    }

    .k-btn {
        flex: 1;
        border: none;
        border-radius: .55rem;
        padding: .6rem .75rem;
        font-size: .78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
    }
    .k-btn:disabled { opacity: .55; cursor: not-allowed; }

    .k-btn-accept  { background: #16a34a; color: #fff; }
    .k-btn-accept:hover:not(:disabled)  { background: #15803d; }
    .k-btn-cook    { background: #2563eb; color: #fff; }
    .k-btn-cook:hover:not(:disabled)    { background: #1d4ed8; }
    .k-btn-ready   { background: #d97706; color: #fff; }
    .k-btn-ready:hover:not(:disabled)   { background: #b45309; }

    .k-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: var(--text-muted);
        font-size: .8rem;
    }

    .k-empty-icon { font-size: 2rem; margin-bottom: .5rem; opacity: .5; }

    .kitchen-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .kitchen-live {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .75rem;
        color: var(--text-muted);
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%      { opacity: .5; transform: scale(.85); }
    }

    body.kitchen-fullscreen .admin-nav { display: none; }
    body.kitchen-fullscreen .admin-content { max-width: none; padding: 1rem; }
    body.kitchen-fullscreen .kitchen-hide-fs { display: none; }

    /* ── Order Detail Modal ── */
    .k-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0,0,0,.75);
        backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .k-modal-backdrop.open { display: flex; }

    .k-modal {
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: 1.25rem;
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 30px 80px rgba(0,0,0,.6);
        animation: kModalIn .2s ease;
    }
    @keyframes kModalIn {
        from { opacity: 0; transform: translateY(16px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .k-modal::-webkit-scrollbar { width: 4px; }
    .k-modal::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 99px; }

    .k-modal-header {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid var(--border-divider);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        position: sticky;
        top: 0;
        background: var(--bg-card);
        z-index: 1;
    }
    .k-modal-title {
        font-family: monospace;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--accent);
    }
    .k-modal-close {
        width: 2rem;
        height: 2rem;
        border-radius: .5rem;
        border: 1px solid var(--border-divider);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all .15s;
        flex-shrink: 0;
    }
    .k-modal-close:hover { background: rgba(239,68,68,.12); color: #ef4444; border-color: rgba(239,68,68,.3); }

    .k-modal-meta {
        padding: .75rem 1.25rem;
        border-bottom: 1px solid var(--border-divider);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        font-size: .75rem;
        color: var(--text-muted);
    }
    .k-modal-meta span { display: flex; align-items: center; gap: .3rem; }
    .k-modal-meta strong { color: var(--text-strong); }

    .k-modal-items { padding: .85rem 1.25rem; display: flex; flex-direction: column; gap: 1rem; }

    .k-modal-item {
        background: rgba(255,255,255,.03);
        border: 1px solid var(--border-divider);
        border-radius: .875rem;
        overflow: hidden;
    }
    .k-modal-item-header {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .75rem .85rem;
    }
    .k-modal-item-img {
        width: 52px;
        height: 52px;
        border-radius: .6rem;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid var(--border-divider);
    }
    .k-modal-item-qty {
        font-size: 1rem;
        font-weight: 900;
        color: #facc15;
        flex-shrink: 0;
    }
    .k-modal-item-name {
        font-size: .9rem;
        font-weight: 700;
        color: var(--text-strong);
        flex: 1;
        min-width: 0;
    }
    .k-modal-item-price {
        font-size: .8rem;
        font-weight: 700;
        color: #4ade80;
        flex-shrink: 0;
    }

    .k-modal-specs {
        border-top: 1px solid var(--border-divider);
        padding: .65rem .85rem .75rem;
    }
    .k-modal-specs-label {
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: .45rem;
    }
    .k-modal-spec-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .3rem 0;
        border-bottom: 1px solid rgba(255,255,255,.04);
        gap: .5rem;
    }
    .k-modal-spec-row:last-child { border-bottom: none; }
    .k-modal-spec-left { display: flex; align-items: center; gap: .4rem; }
    .k-modal-spec-badge {
        font-size: .58rem;
        font-weight: 800;
        padding: .1rem .35rem;
        border-radius: .25rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        flex-shrink: 0;
    }
    .spec-flavor   { background: rgba(59,130,246,.15);  color: #3b82f6; }
    .spec-modifier { background: rgba(139,92,246,.15); color: #8b5cf6; }
    .spec-addon    { background: rgba(245,158,11,.15);  color: #d97706; }
    .k-modal-spec-name {
        font-size: .78rem;
        font-weight: 600;
        color: var(--text-strong);
    }
    .k-modal-spec-price {
        font-size: .75rem;
        font-weight: 700;
        color: #4ade80;
        flex-shrink: 0;
    }

    .k-modal-no-specs {
        font-size: .75rem;
        color: var(--text-muted);
        font-style: italic;
        padding: .3rem 0;
    }

    .k-modal-footer {
        padding: .85rem 1.25rem;
        border-top: 1px solid var(--border-divider);
        display: flex;
        flex-direction: column;
        gap: .4rem;
    }
    .k-modal-total-row {
        display: flex;
        justify-content: space-between;
        font-size: .8rem;
        color: var(--text-muted);
    }
    .k-modal-total-row.grand {
        font-size: .95rem;
        font-weight: 800;
        color: var(--text-strong);
        padding-top: .4rem;
        border-top: 1px solid var(--border-divider);
        margin-top: .2rem;
    }
    .k-modal-total-row.grand span:last-child { color: #facc15; }

    .k-modal-notes {
        margin: 0 1.25rem .85rem;
        padding: .65rem .85rem;
        border-radius: .65rem;
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.2);
        font-size: .78rem;
        color: #fbbf24;
        line-height: 1.5;
    }

    .k-modal-action-bar {
        padding: .85rem 1.25rem 1rem;
        border-top: 1px solid var(--border-divider);
        display: flex;
        gap: .6rem;
        flex-wrap: wrap;
    }

    /* ── Rider assign section inside modal ── */
    .k-rider-assign {
        padding: .75rem 1.25rem .85rem;
        border-top: 1px solid var(--border-divider);
        background: rgba(59,130,246,.04);
    }
    .k-rider-assign-label {
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: .5rem;
    }
    .k-rider-row {
        display: flex;
        gap: .5rem;
        align-items: center;
    }
    .k-rider-select {
        flex: 1;
        background: var(--bg-card);
        border: 1px solid var(--border-divider);
        border-radius: .55rem;
        padding: .55rem .75rem;
        font-size: .8rem;
        color: var(--text-strong);
        cursor: pointer;
        outline: none;
        transition: border-color .15s;
    }
    .k-rider-select:focus { border-color: rgba(59,130,246,.5); }
    .k-btn-assign {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: .55rem;
        padding: .55rem 1rem;
        font-size: .78rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
        transition: background .15s;
        flex-shrink: 0;
    }
    .k-btn-assign:hover:not(:disabled) { background: #1d4ed8; }
    .k-btn-assign:disabled { opacity: .55; cursor: not-allowed; }
    .k-rider-assigned-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .75rem;
        font-weight: 700;
        color: #60a5fa;
        background: rgba(59,130,246,.1);
        border: 1px solid rgba(59,130,246,.25);
        border-radius: .5rem;
        padding: .3rem .7rem;
    }
</style>
@endpush

@section('content')

<div class="kitchen-toolbar">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.75rem;height:2.75rem;border-radius:.75rem;background:rgba(217,119,6,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="chef-hat" style="width:1.3rem;height:1.3rem;color:#d97706;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:var(--text-heading);">Kitchen Display</h1>
            <p style="margin:0;font-size:.875rem;color:var(--text-muted);">Track orders from cooking through ready for delivery and rider pickup.</p>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:.65rem;flex-wrap:wrap;">
        <span class="kitchen-live"><span class="live-dot"></span> Auto-refresh <span id="refreshCountdown">15</span>s</span>
        <button type="button" class="btn-ghost" style="font-size:.75rem;" onclick="refreshKitchen(true)">
            <i data-lucide="refresh-cw" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Refresh
        </button>
        <button type="button" class="btn-ghost" style="font-size:.75rem;" onclick="toggleKitchenFullscreen()">
            <i data-lucide="maximize-2" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Fullscreen
        </button>
    </div>
</div>

<div class="kitchen-board" id="kitchenBoard">
    {{-- New Orders --}}
    <div class="kitchen-col" data-col="new">
        <div class="kitchen-col-header" style="background:rgba(245,158,11,.06);">
            <h2 class="kitchen-col-title">
                <i data-lucide="bell" style="width:1rem;height:1rem;color:#f59e0b;stroke-width:2;"></i>
                New Orders
            </h2>
            <span class="kitchen-col-count" style="background:rgba(245,158,11,.15);color:#f59e0b;" id="count-new">{{ $newOrders->count() }}</span>
        </div>
        <div class="kitchen-col-body" id="col-new">
            @forelse($newOrders as $order)
                @include('admin.partials.kitchen-order-card', ['order' => $order, 'column' => 'new'])
            @empty
                <div class="k-empty"><div class="k-empty-icon">🔔</div>No new orders</div>
            @endforelse
        </div>
    </div>

    {{-- Queue --}}
    <div class="kitchen-col" data-col="queued">
        <div class="kitchen-col-header" style="background:rgba(59,130,246,.06);">
            <h2 class="kitchen-col-title">
                <i data-lucide="list-ordered" style="width:1rem;height:1rem;color:#3b82f6;stroke-width:2;"></i>
                Queue
            </h2>
            <span class="kitchen-col-count" style="background:rgba(59,130,246,.15);color:#3b82f6;" id="count-queued">{{ $queuedOrders->count() }}</span>
        </div>
        <div class="kitchen-col-body" id="col-queued">
            @forelse($queuedOrders as $order)
                @include('admin.partials.kitchen-order-card', ['order' => $order, 'column' => 'queued'])
            @empty
                <div class="k-empty"><div class="k-empty-icon">📋</div>Queue is empty</div>
            @endforelse
        </div>
    </div>

    {{-- Cooking --}}
    <div class="kitchen-col" data-col="cooking">
        <div class="kitchen-col-header" style="background:rgba(220,38,38,.06);">
            <h2 class="kitchen-col-title">
                <i data-lucide="flame" style="width:1rem;height:1rem;color:#dc2626;stroke-width:2;"></i>
                Cooking
            </h2>
            <span class="kitchen-col-count" style="background:rgba(220,38,38,.15);color:#dc2626;" id="count-cooking">{{ $cookingOrders->count() }}</span>
        </div>
        <div class="kitchen-col-body" id="col-cooking">
            @forelse($cookingOrders as $order)
                @include('admin.partials.kitchen-order-card', ['order' => $order, 'column' => 'cooking'])
            @empty
                <div class="k-empty"><div class="k-empty-icon">🍳</div>Nothing cooking</div>
            @endforelse
        </div>
    </div>

    {{-- Ready for Delivery --}}
    <div class="kitchen-col" data-col="ready">
        <div class="kitchen-col-header" style="background:rgba(16,185,129,.06);">
            <h2 class="kitchen-col-title">
                <i data-lucide="package-check" style="width:1rem;height:1rem;color:#10b981;stroke-width:2;"></i>
                Ready for Delivery
            </h2>
            <span class="kitchen-col-count" style="background:rgba(16,185,129,.15);color:#10b981;" id="count-ready">{{ $readyOrders->count() }}</span>
        </div>
        <div class="kitchen-col-body" id="col-ready">
            @forelse($readyOrders as $order)
                @include('admin.partials.kitchen-order-card', ['order' => $order, 'column' => 'ready'])
            @empty
                <div class="k-empty"><div class="k-empty-icon">📦</div>No orders ready for delivery</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ── Order Detail Modal ── --}}
<div class="k-modal-backdrop" id="orderModal" onclick="closeOrderModal(event)">
    <div class="k-modal" id="orderModalBox">
        <div class="k-modal-header">
            <div>
                <div class="k-modal-title" id="modalOrderNum">—</div>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:.15rem;" id="modalCustomer">—</div>
            </div>
            <button class="k-modal-close" onclick="closeOrderModal()">✕</button>
        </div>
        <div class="k-modal-meta" id="modalMeta"></div>
        <div class="k-modal-items" id="modalItems"></div>
        <div id="modalNotes"></div>
        <div class="k-modal-footer" id="modalFooter"></div>
        <div id="riderAssignSection"></div>
        <div class="k-modal-action-bar" id="modalActions"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const IS_ADMIN   = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
const KITCHEN_URL = '{{ route('chef.orders') }}';
const ACCEPT_URL  = id => `/chef/orders/${id}/accept`;
const START_URL   = id => `/chef/orders/${id}/start`;
const READY_URL   = id => `/chef/orders/${id}/ready`;

let refreshTimer = 15;
let countdownTimer = null;
let lastNewCount = {{ $newOrders->count() }};
let orderDataMap = {};

const AVAILABLE_RIDERS = @json($availableRiders ?? []);

function elapsedBadge(mins) {
    let bg, color, label;
    if (mins >= 20)      { bg = 'rgba(239,68,68,.15)';  color = '#ef4444'; label = mins + 'm — URGENT'; }
    else if (mins >= 10) { bg = 'rgba(245,158,11,.15)'; color = '#f59e0b'; label = mins + 'm'; }
    else                 { bg = 'rgba(255,255,255,.06)'; color = 'var(--text-muted)'; label = mins + 'm ago'; }
    return `<span class="k-elapsed" style="background:${bg};color:${color};">${label}</span>`;
}

function renderItems(items) {
    return items.map(item => {
        const modList = item.modifiers || [];
        // modifiers may be plain strings (plucked) or objects {name, type, ...}
        const mods = modList
            .filter(m => m && (typeof m === 'string' ? m : m.name) && !/^no\s/i.test(typeof m === 'string' ? m : (m.name || '')))
            .map(m => {
                if (typeof m === 'string') {
                    return `<span class="k-mod-tag">${escapeHtml(m)}</span>`;
                }
                const colors = { flavor: '#3b82f6', modifier: '#8b5cf6', addon: '#d97706' };
                const c = colors[m.type] || '#60a5fa';
                const adj = parseFloat(m.price_adjustment || 0);
                const extra = (m.price_type === 'add' && adj > 0) ? ` +₱${adj}` : '';
                return `<span class="k-mod-tag" style="background:${c}18;color:${c};border-color:${c}30;">${escapeHtml(m.name)}${extra}</span>`;
            }).join('');
        return `
            <div class="k-item">
                <img class="k-item-img" src="${item.image}" alt="">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:baseline;gap:.4rem;">
                        <span class="k-item-qty">${item.qty}×</span>
                        <span class="k-item-name">${escapeHtml(item.name)}</span>
                    </div>
                    ${mods ? `<div class="k-modifiers">${mods}</div>` : ''}
                </div>
            </div>`;
    }).join('');
}

function renderActions(order, column) {
    let printBtn = '';
    if (column !== 'new') {
        const receiptUrl = `/chef/orders/${order.id}/receipt`;
        printBtn = `<button class="k-btn" style="flex:0 0 auto;width:2.5rem;background:rgba(255,255,255,.06);color:var(--text-muted);" onclick="event.stopPropagation();printReceipt('${receiptUrl}')" title="Print Receipt"><i data-lucide="printer" style="width:14px;height:14px;"></i></button>`;
    }

    if (column === 'new') {
        if (!IS_ADMIN) return `<div style="text-align:center;width:100%;font-size:.72rem;color:var(--text-muted);padding:.4rem;">Waiting for Admin to accept...</div>`;
        return `<button class="k-btn k-btn-accept" onclick="event.stopPropagation();kitchenAction('accept', ${order.id}, this)">✓ Accept</button>`;
    }
    if (column === 'queued') {
        return printBtn + `<button class="k-btn k-btn-cook" onclick="event.stopPropagation();kitchenAction('start', ${order.id}, this)">🍳 Start Cooking</button>`;
    }
    if (column === 'cooking') {
        return printBtn + `<button class="k-btn k-btn-ready" onclick="event.stopPropagation();kitchenAction('ready', ${order.id}, this)">✅ Mark Ready</button>`;
    }
    const detail = order.delivery_detail || 'Ready for delivery';
    const color = order.delivery_color || '#10b981';
    return printBtn + `<div style="text-align:center;width:100%;font-size:.72rem;color:${color};font-weight:700;padding:.4rem;line-height:1.45;">${escapeHtml(detail)}</div>`;
}

function renderOrderCard(order, column) {
    const urgent = order.elapsed_mins >= 20 && column !== 'ready' ? ' is-urgent' : '';
    const pickedUp = column === 'ready' && order.delivery_status === 'picked_up' ? ' is-picked-up' : '';
    const notes = order.notes
        ? `<div class="k-notes">📝 ${escapeHtml(order.notes)}</div>`
        : '';

    const deliveryBanner = column === 'ready'
        ? `<div class="k-delivery-banner" style="background:${order.delivery_bg || 'rgba(16,185,129,.14)'};color:${order.delivery_color || '#10b981'};">${escapeHtml(order.delivery_label || 'Ready for Delivery')}</div>`
        : '';

    const elapsed = column !== 'ready'
        ? elapsedBadge(order.elapsed_mins)
        : '';

    return `
        <div class="k-order-card${urgent}${pickedUp}" data-order-id="${order.id}" onclick="openOrderModal(${order.id})" style="cursor:pointer;">
            ${deliveryBanner}
            <div class="k-card-top">
                <div>
                    <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.15rem;">
                        <div class="k-order-num">${escapeHtml(order.order_number)}</div>
                        <span style="font-size:10px;padding:1px 5px;border-radius:4px;background:rgba(255,255,255,.05);color:var(--text-muted);border:1px solid rgba(255,255,255,.1);display:inline-flex;align-items:center;gap:3px;">
                            ${order.order_type_icon} ${order.order_type_label}
                        </span>
                    </div>
                    <div class="k-customer">${escapeHtml(order.customer)} · ${order.placed_at}</div>
                </div>
                ${elapsed}
            </div>
            <div class="k-items">${renderItems(order.items)}</div>
            ${notes}
            <div class="k-actions">${renderActions(order, column)}</div>
        </div>`;
}

function renderColumn(col, orders) {
    const el = document.getElementById('col-' + col);
    const countEl = document.getElementById('count-' + col);
    if (!el) return;

    countEl.textContent = orders.length;

    if (!orders.length) {
        const emptyMsg = { new: 'No new orders', queued: 'Queue is empty', cooking: 'Nothing cooking', ready: 'No orders ready for delivery' };
        const emptyIcon = { new: '🔔', queued: '📋', cooking: '🍳', ready: '📦' };
        el.innerHTML = `<div class="k-empty"><div class="k-empty-icon">${emptyIcon[col]}</div>${emptyMsg[col]}</div>`;
        return;
    }

    el.innerHTML = orders.map(o => renderOrderCard(o, col)).join('');
    orders.forEach(o => { orderDataMap[o.id] = { ...o, column: col }; });
}

// ── ORDER DETAIL MODAL ──────────────────────────────────────────────────────
function openOrderModal(orderId) {
    const order = orderDataMap[orderId];
    if (!order) return;

    document.getElementById('modalOrderNum').innerHTML = 
        order.order_number + 
        ` <span style="font-size:10px;padding:2px 8px;border-radius:6px;background:rgba(255,255,255,.08);color:var(--text-muted);border:1px solid rgba(255,255,255,.12);margin-left:.5rem;vertical-align:middle;font-family:sans-serif;font-weight:600;">` + 
        order.order_type_icon + ' ' + order.order_type_label + '</span>';
    document.getElementById('modalCustomer').textContent = order.customer + ' · ' + order.placed_at;

    const statusColors = { pending:'#f59e0b', accepted:'#3b82f6', preparing:'#ef4444', rider_assigned:'#8b5cf6', out_for_delivery:'#8b5cf6', delivered:'#22c55e' };
    const statusLabels = { pending:'Pending', accepted:'Accepted', preparing:'Cooking', rider_assigned:'Rider Assigned', out_for_delivery:'Out for Delivery', delivered:'Delivered' };
    const sc = statusColors[order.status] || '#6b7280';
    document.getElementById('modalMeta').innerHTML =
        `<span>🕐 <strong>${order.placed_at}</strong></span>` +
        `<span style="color:${sc};font-weight:700;">● ${statusLabels[order.status] || order.status}</span>` +
        (order.elapsed_mins >= 0 ? `<span>⏱ <strong>${order.elapsed_mins}m ago</strong></span>` : '') +
        (order.rider_name ? `<span>🛵 <strong>${escapeHtml(order.rider_name)}</strong></span>` : '');

    let subtotal = 0;
    const itemsHtml = (order.items || []).map(item => {
        const qty = parseInt(item.qty || 1);
        const price = parseFloat(item.price || 0);
        // prefer stored subtotal if available, otherwise compute
        const itemTotal = item.subtotal > 0 ? parseFloat(item.subtotal) : price * qty;
        subtotal += itemTotal;

        const specs = (item.modifiers || []).filter(m => {
            const name = typeof m === 'string' ? m : (m.name || '');
            return name && !/^no\s/i.test(name);
        });

        const specsInner = specs.length ? specs.map(m => {
            const name  = typeof m === 'string' ? m : m.name;
            const type  = typeof m === 'object' ? (m.type || 'modifier') : 'modifier';
            const adj   = typeof m === 'object' ? parseFloat(m.price_adjustment || 0) : 0;
            const pType = typeof m === 'object' ? (m.price_type || 'none') : 'none';
            const label = { flavor:'Flavor', modifier:'Option', addon:'Add-on' }[type] || 'Option';
            const priceHtml = (pType === 'add' && adj > 0) ? `<span class="k-modal-spec-price">+₱${adj.toLocaleString()}</span>` : '';
            return `<div class="k-modal-spec-row">
                <div class="k-modal-spec-left">
                    <span class="k-modal-spec-badge spec-${type}">${label}</span>
                    <span class="k-modal-spec-name">${escapeHtml(name)}</span>
                </div>${priceHtml}</div>`;
        }).join('') : `<div class="k-modal-no-specs">Standard / No special customization</div>`;

        return `<div class="k-modal-item">
            <div class="k-modal-item-header">
                <img class="k-modal-item-img" src="${escapeHtml(item.image||'')}" alt="" onerror="this.src='/images/hero-burger.jpg'">
                <span class="k-modal-item-qty">${qty}×</span>
                <span class="k-modal-item-name">${escapeHtml(item.name)}</span>
                <span class="k-modal-item-price">₱${itemTotal.toLocaleString()}</span>
            </div>
            <div class="k-modal-specs">
                <div class="k-modal-specs-label">📋 Specifications / Choices</div>
                ${specsInner}
            </div>
        </div>`;
    }).join('');
    document.getElementById('modalItems').innerHTML = itemsHtml;

    document.getElementById('modalNotes').innerHTML = order.notes
        ? `<div class="k-modal-notes">📝 <strong>Note:</strong> ${escapeHtml(order.notes)}</div>` : '';

    const delivery = parseFloat(order.delivery_fee ?? 50);
    const total    = parseFloat(order.total > 0 ? order.total : (order.subtotal + delivery));
    const displaySub = parseFloat(order.subtotal > 0 ? order.subtotal : subtotal);
    document.getElementById('modalFooter').innerHTML =
        `<div class="k-modal-total-row"><span>Subtotal</span><span>₱${displaySub.toLocaleString()}</span></div>` +
        `<div class="k-modal-total-row"><span>Delivery fee</span><span>${delivery === 0 ? '<span style="color:#4ade80">FREE</span>' : '₱' + delivery.toLocaleString()}</span></div>` +
        (order.payment_method ? `<div class="k-modal-total-row"><span>Payment</span><span style="text-transform:capitalize;">${escapeHtml(order.payment_method === 'cash' ? '💵 Cash on Delivery' : order.payment_method === 'gcash' ? '📱 GCash' : '💳 Card')}</span></div>` : '') +
        `<div class="k-modal-total-row grand"><span>Total</span><span>₱${total.toLocaleString()}</span></div>`;

    const col = order.column;
    let btn = '';
    let printBtn = `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);flex:0 0 auto;width:3rem;" onclick="printReceipt('/chef/orders/${order.id}/receipt')" title="Print Receipt"><i data-lucide="printer" style="width:16px;height:16px;"></i></button>`;

    if (col === 'new' && IS_ADMIN) btn = `<button class="k-btn k-btn-accept" style="flex:1;" onclick="modalAction('accept',${order.id})">✓ Accept Order</button>`;
    if (col === 'queued')  btn = `<button class="k-btn k-btn-cook" style="flex:1;" onclick="modalAction('start',${order.id})">🍳 Start Cooking</button>`;
    if (col === 'cooking') btn = `<button class="k-btn k-btn-ready" style="flex:1;" onclick="modalAction('ready',${order.id})">✅ Mark Ready</button>`;

    document.getElementById('modalActions').innerHTML =
        (col !== 'new' ? printBtn : '') +
        btn + `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);flex:0 0 auto;padding:.6rem 1.2rem;" onclick="closeOrderModal()">Close</button>`;

    // ── Rider assign section (visible on cooking + ready columns, ADMIN ONLY) ──────────
    const canAssign = (col === 'cooking' || col === 'ready') && IS_ADMIN;
    const riderSection = document.getElementById('riderAssignSection');
    if (canAssign) {
        if (order.rider_id && order.rider_name) {
            riderSection.innerHTML = `
                <div class="k-rider-assign">
                    <div class="k-rider-assign-label">🛵 Rider Assignment</div>
                    <div class="k-rider-assigned-badge">✓ ${escapeHtml(order.rider_name)} assigned</div>
                    <div style="margin-top:.5rem;">
                        <button class="k-btn-assign" style="font-size:.72rem;padding:.4rem .7rem;background:rgba(255,255,255,.08);color:var(--text-muted);" onclick="showRiderReassign(${order.id})">Change Rider</button>
                    </div>
                </div>`;
        } else {
            riderSection.innerHTML = buildRiderSelectHtml(order.id);
        }
    } else {
        riderSection.innerHTML = '';
    }

    document.getElementById('orderModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function buildRiderSelectHtml(orderId) {
    if (!AVAILABLE_RIDERS.length) {
        return `<div class="k-rider-assign">
            <div class="k-rider-assign-label">🛵 Rider Assignment</div>
            <div style="font-size:.75rem;color:var(--text-muted);font-style:italic;">No riders available right now</div>
        </div>`;
    }
    const options = AVAILABLE_RIDERS.map(r =>
        `<option value="${r.id}">${escapeHtml(r.name)}${r.phone ? ' · ' + escapeHtml(r.phone) : ''}</option>`
    ).join('');
    return `
        <div class="k-rider-assign">
            <div class="k-rider-assign-label">🛵 Assign Rider</div>
            <div class="k-rider-row">
                <select class="k-rider-select" id="riderSelectFor_${orderId}">
                    <option value="">— Choose a rider —</option>
                    ${options}
                </select>
                <button class="k-btn-assign" id="riderAssignBtn_${orderId}"
                    onclick="assignRiderAction(${orderId})">Assign</button>
            </div>
        </div>`;
}

function showRiderReassign(orderId) {
    document.getElementById('riderAssignSection').innerHTML = buildRiderSelectHtml(orderId);
}

async function assignRiderAction(orderId) {
    const select = document.getElementById('riderSelectFor_' + orderId);
    const btn    = document.getElementById('riderAssignBtn_' + orderId);
    const riderId = select ? select.value : '';

    if (!riderId) { select.style.borderColor = '#ef4444'; setTimeout(() => select.style.borderColor = '', 1500); return; }

    btn.disabled = true;
    btn.textContent = '…';

    try {
        const res = await fetch(`/chef/orders/${orderId}/assign-rider`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ rider_id: riderId }),
        });
        const data = await res.json();
        if (!data.success) {
            alert(data.message || 'Failed to assign rider.');
            btn.disabled = false;
            btn.textContent = 'Assign';
            return;
        }
        // Update local orderDataMap
        const riderObj = AVAILABLE_RIDERS.find(r => r.id == riderId);
        if (orderDataMap[orderId]) {
            orderDataMap[orderId].rider_id   = parseInt(riderId);
            orderDataMap[orderId].rider_name = riderObj ? riderObj.name : 'Rider';
            orderDataMap[orderId].column     = 'ready';
        }
        closeOrderModal();
        await refreshKitchen(true);
    } catch (e) {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Assign';
    }
}

function closeOrderModal(e) {
    if (e && e.target !== document.getElementById('orderModal')) return;
    document.getElementById('orderModal').classList.remove('open');
    document.body.style.overflow = '';
}

async function modalAction(action, orderId) {
    const btn = document.querySelector('#modalActions button:first-child');
    if (btn) { btn.disabled = true; btn.textContent = '…'; }
    const urls = { accept: ACCEPT_URL(orderId), start: START_URL(orderId), ready: READY_URL(orderId) };
    try {
        const res = await fetch(urls[action], { method:'POST', headers:{ 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json', 'Content-Type':'application/json' } });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Action failed.'); if (btn) btn.disabled = false; return; }

        if (data.receipt_url) {
            printReceipt(data.receipt_url);
        }

        closeOrderModal();
        await refreshKitchen(true);
    } catch(e) { alert('Network error.'); if (btn) btn.disabled = false; }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeOrderModal(); });

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// ── RECEIPT PRINTING ────────────────────────────────────────────────────────
function printReceipt(receiptUrl) {
    const existing = document.getElementById('receiptFrame');
    if (existing) existing.remove();

    const iframe = document.createElement('iframe');
    iframe.id = 'receiptFrame';
    iframe.src = receiptUrl;
    iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:0;height:0;border:none;';
    document.body.appendChild(iframe);

    iframe.onerror = () => window.open(receiptUrl, '_blank');
}

async function kitchenAction(action, orderId, btn) {
    const urls = {
        accept: ACCEPT_URL(orderId),
        start:  START_URL(orderId),
        ready:  READY_URL(orderId),
    };

    btn.disabled = true;
    btn.textContent = '…';

    try {
        const res = await fetch(urls[action], {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });
        const data = await res.json();
        if (!data.success) {
            alert(data.message || 'Action failed.');
            btn.disabled = false;
            return;
        }

        if (data.receipt_url) {
            printReceipt(data.receipt_url);
        }

        await refreshKitchen(true);
    } catch (e) {
        alert('Network error. Please try again.');
        btn.disabled = false;
    }
}

async function refreshKitchen(manual) {
    try {
        const res = await fetch(KITCHEN_URL, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        const newCount = data.new.length;
        if (newCount > lastNewCount) {
            playNewOrderSound();
        }
        lastNewCount = newCount;

        renderColumn('new', data.new);
        renderColumn('queued', data.queued);
        renderColumn('cooking', data.cooking);
        renderColumn('ready', data.ready);

        if (window.lucide) lucide.createIcons();

        refreshTimer = 15;
    } catch (e) {
        if (manual) alert('Could not refresh kitchen board.');
    }
}

function playNewOrderSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        [880, 1100].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = freq;
            gain.gain.value = 0.08;
            osc.start(ctx.currentTime + i * 0.15);
            osc.stop(ctx.currentTime + i * 0.15 + 0.12);
        });
    } catch (e) {}
}

function toggleKitchenFullscreen() {
    document.body.classList.toggle('kitchen-fullscreen');
}

function startCountdown() {
    clearInterval(countdownTimer);
    countdownTimer = setInterval(() => {
        refreshTimer--;
        const el = document.getElementById('refreshCountdown');
        if (el) el.textContent = refreshTimer;
        if (refreshTimer <= 0) {
            refreshKitchen(false);
        }
    }, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();

    // Seed orderDataMap from initial server-rendered data
    @php
        $allKitchenOrders = array_merge(
            $newOrders->all(),
            $queuedOrders->all(),
            $cookingOrders->all(),
            $readyOrders->all()
        );
        $kitchenSeed = [];
        foreach ($allKitchenOrders as $o) {
            if (in_array($o->status, ['pending'])) {
                $col = 'new';
            } elseif (in_array($o->status, ['accepted'])) {
                $col = 'queued';
            } elseif ($o->status === 'preparing' && !$o->prepared_at) {
                $col = 'cooking';
            } else {
                $col = 'ready';
            }
            $kitchenSeed[] = [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'customer'     => $o->user?->name ?? 'Guest',
                'status'       => $o->status,
                'order_type'   => $o->order_type,
                'order_type_label' => $o->order_type_label,
                'order_type_icon' => $o->order_type_icon,
                'placed_at'    => $o->created_at->format('g:i A'),
                'elapsed_mins' => (int) $o->created_at->diffInMinutes(now()),
                'notes'        => $o->notes,
                'subtotal'     => (float) ($o->subtotal ?? 0),
                'delivery_fee' => (float) ($o->delivery_fee ?? 50),
                'total'        => (float) ($o->total ?? 0),
                'payment_method' => $o->payment_method,
                'rider_name'   => $o->rider?->user?->name,
                'rider_id'     => $o->rider_id,
                'column'       => $col,
                'items'        => $o->items->map(function($i) {
                    $mods = collect($i->modifiers ?? [])
                        ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
                        ->values()
                        ->all();
                    return [
                        'name'      => $i->item_name,
                        'qty'       => $i->quantity,
                        'price'     => (float) $i->unit_price,
                        'subtotal'  => (float) $i->subtotal,
                        'image'     => $i->image ? asset($i->image) : asset('images/hero-burger.jpg'),
                        'modifiers' => $mods,
                    ];
                })->all(),
            ];
        }
    @endphp
    const _kitchenSeed = @json($kitchenSeed);
    _kitchenSeed.forEach(o => { orderDataMap[o.id] = o; });

    // Wire click on blade-rendered cards (before first AJAX refresh)
    document.querySelectorAll('.k-order-card[data-order-id]').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function(e) {
            if (e.target.closest('.k-actions')) return;
            openOrderModal(parseInt(this.dataset.orderId));
        });
    });

    startCountdown();
});
</script>
@endpush
