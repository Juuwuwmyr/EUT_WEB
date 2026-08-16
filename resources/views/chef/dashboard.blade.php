@extends('chef.layout')
@section('title', 'Kitchen Display')

@push('head')
<style>
/* ── Grid — same pattern as admin orders page ─────────── */
.kitchen-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
    padding: 1.1rem 1.25rem 1.5rem;
}
.kitchen-grid-empty {
    grid-column: 1 / -1;
    text-align: center;
    color: var(--text-muted);
    padding: 3rem 1rem;
    font-size: .85rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .5rem;
    opacity: .7;
}
@media (max-width: 600px) {
    .kitchen-grid { grid-template-columns: 1fr; padding: .75rem; }
}

/* ── Card shell — same as .order-card ────────────────── */
.order-card {
    background: var(--bg-card, #1a1a2e);
    border: 1px solid var(--border-card, rgba(255,255,255,.08));
    border-radius: 1rem;
    padding: 1rem 1.1rem .9rem;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    transition: box-shadow .15s, transform .15s;
    cursor: pointer;
    position: relative;
}
.order-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.22);
    transform: translateY(-1px);
}
.order-card.is-urgent {
    border-color: rgba(239,68,68,.45);
    box-shadow: 0 0 0 1px rgba(239,68,68,.15);
}
.order-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .5rem;
}
.order-card-num {
    font-family: monospace;
    font-size: .82rem;
    font-weight: 800;
    color: #60a5fa;
    line-height: 1.3;
}
.order-card-badge {
    display: inline-flex;
    align-items: center;
    padding: .18rem .55rem;
    border-radius: 9999px;
    font-size: .62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    white-space: nowrap;
    flex-shrink: 0;
}
.order-card-meta {
    display: flex;
    flex-direction: column;
    gap: .1rem;
    font-size: .72rem;
    color: var(--text-muted, #9ca3af);
}
.order-card-body  { display: flex; flex-direction: column; gap: .3rem; flex: 1; }
.order-card-items { display: flex; flex-direction: column; gap: .2rem; margin-bottom: .15rem; }
.order-card-item  { display: flex; align-items: center; gap: .4rem; font-size: .74rem; }
.order-card-item-qty {
    font-size: .68rem; font-weight: 700; color: #d97706;
    background: rgba(245,158,11,.1); border-radius: 4px;
    padding: 1px 5px; flex-shrink: 0;
}
.order-card-item-name {
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    max-width: 200px; color: var(--text-body, #e2e8f0);
}
.order-card-item-more { font-size: .66rem; color: var(--text-muted); }
.order-card-total {
    font-size: 1.3rem; font-weight: 800;
    color: var(--text-strong, #f1f5f9);
    letter-spacing: -.01em; margin-top: .1rem;
}
.order-card-actions {
    display: flex; gap: .4rem; flex-wrap: wrap;
    align-items: center; margin-top: .35rem;
}

/* Sub-rows for grouped dine-in cards */
.order-card-subitems { display: flex; flex-direction: column; gap: .25rem; }
.order-card-subrow {
    display: flex; align-items: center; gap: .35rem; flex-wrap: wrap;
    padding: .25rem .4rem; border-radius: .4rem;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.06);
}
.order-card-subrow-num {
    font-family: monospace; font-size: .67rem; font-weight: 700;
    color: #60a5fa; flex-shrink: 0;
}
.order-card-subrow-total { font-size: .67rem; color: var(--text-muted); flex-shrink: 0; }

/* Elapsed badge */
.k-elapsed {
    font-size: .65rem; font-weight: 700;
    padding: .18rem .5rem; border-radius: 9999px; white-space: nowrap;
}

/* Kitchen buttons */
.oc-btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: .3rem; padding: .45rem .7rem; border-radius: .5rem;
    font-size: .74rem; font-weight: 600; cursor: pointer;
    border: 1.5px solid transparent;
    transition: opacity .15s, filter .15s; white-space: nowrap;
}
.oc-btn:disabled { opacity: .5; cursor: default; }
.oc-btn:not(:disabled):hover { filter: brightness(1.1); }
.oc-btn-ready   { background: #d97706; border-color: #d97706; color: #fff; flex: 1; }
.oc-btn-print   { background: transparent; border-color: rgba(255,255,255,.15); color: var(--text-muted); flex-shrink: 0; padding: .45rem .6rem; }
.oc-btn-remove  { background: rgba(239,68,68,.1); border-color: rgba(239,68,68,.25); color: #ef4444; flex-shrink: 0; padding: .45rem .6rem; }
.oc-btn-remove:hover:not(:disabled) { background: rgba(239,68,68,.2); border-color: rgba(239,68,68,.5); }

@keyframes blink { 0%,100%{opacity:1} 50%{opacity:.35} }

/* Toolbar */
.kitchen-toolbar {
    display: flex; flex-wrap: wrap;
    align-items: center; justify-content: space-between;
    gap: 1rem; margin-bottom: 1.25rem;
}
.kitchen-live   { display: inline-flex; align-items: center; gap: .45rem; font-size: .75rem; color: var(--text-muted); }
.live-dot       { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; animation: pulse 1.5s infinite; }
@keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.85)} }

body.kitchen-fullscreen .admin-nav       { display: none; }
body.kitchen-fullscreen .admin-content   { max-width: none; padding: 1rem; }
body.kitchen-fullscreen .kitchen-hide-fs { display: none; }

/* Remove-item modal */
.k-remove-backdrop {
    display: none; position: fixed; inset: 0; z-index: 10000;
    background: rgba(0,0,0,.78); backdrop-filter: blur(6px);
    align-items: center; justify-content: center; padding: 1rem;
}
.k-remove-backdrop.open { display: flex; }
.k-remove-modal {
    background: var(--bg-card); border: 1px solid var(--border-card);
    border-radius: 1.1rem; width: 100%; max-width: 420px;
    box-shadow: 0 24px 60px rgba(0,0,0,.7);
    animation: kModalIn .2s ease; overflow: hidden;
}
@keyframes kModalIn { from{opacity:0;transform:translateY(14px) scale(.97)} to{opacity:1;transform:none} }
.k-remove-header {
    padding: 1rem 1.2rem .8rem; border-bottom: 1px solid var(--border-divider);
    display: flex; align-items: center; justify-content: space-between;
}
.k-remove-title    { font-size: .95rem; font-weight: 800; color: var(--text-strong); }
.k-remove-subtitle { font-size: .72rem; color: var(--text-muted); margin-top: .1rem; }
.k-remove-body     { padding: .75rem 1.2rem; display: flex; flex-direction: column; gap: .45rem; max-height: 52vh; overflow-y: auto; }
.k-remove-item-row {
    display: flex; align-items: center; gap: .7rem; padding: .55rem .7rem;
    border-radius: .65rem; border: 1px solid var(--border-divider);
    background: rgba(255,255,255,.03); cursor: pointer;
    transition: border-color .15s, background .15s;
}
.k-remove-item-row:hover    { border-color: rgba(239,68,68,.4); background: rgba(239,68,68,.06); }
.k-remove-item-row.selected { border-color: rgba(239,68,68,.6); background: rgba(239,68,68,.1); }
.k-remove-item-img  { width:38px; height:38px; border-radius:.45rem; object-fit:cover; flex-shrink:0; border:1px solid var(--border-divider); }
.k-remove-item-name { flex:1; font-size:.85rem; font-weight:700; color:var(--text-strong); }
.k-remove-item-qty  { font-size:.75rem; font-weight:800; color:#facc15; flex-shrink:0; }
.k-remove-check {
    width:1.25rem; height:1.25rem; border-radius:50%; border:2px solid rgba(255,255,255,.2);
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0; font-size:.65rem; color:transparent; transition:all .15s;
}
.k-remove-item-row.selected .k-remove-check { background:#ef4444; border-color:#ef4444; color:#fff; }
.k-remove-footer { padding:.75rem 1.2rem 1rem; border-top:1px solid var(--border-divider); display:flex; gap:.5rem; }
.k-modal-close {
    width:2rem; height:2rem; border-radius:.5rem; border:1px solid var(--border-divider);
    background:transparent; color:var(--text-muted); cursor:pointer;
    display:flex; align-items:center; justify-content:center; font-size:1.1rem; transition:all .15s;
}
.k-modal-close:hover { background:rgba(239,68,68,.12); color:#ef4444; border-color:rgba(239,68,68,.3); }

html.light .order-card { background:#fff; border-color:rgba(0,0,0,.09); }
html.light .order-card:hover { box-shadow:0 6px 24px rgba(0,0,0,.08); }
html.light .order-card-num   { color:#2563eb; }
html.light .order-card-total { color:#111827; }
html.light .order-card-subrow { background:rgba(0,0,0,.025); border-color:rgba(0,0,0,.07); }
</style>
@endpush

@section('content')

{{-- ── Toolbar ─────────────────────────────────────────── --}}
<div class="kitchen-toolbar">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.75rem;height:2.75rem;border-radius:.75rem;background:rgba(217,119,6,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="chef-hat" style="width:1.3rem;height:1.3rem;color:#d97706;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:var(--text-heading);">Kitchen Display</h1>
            <p style="margin:0;font-size:.875rem;color:var(--text-muted);">Orders appear here when admin accepts. Mark ready when done.</p>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:.65rem;flex-wrap:wrap;">
        <span class="kitchen-live" id="wsStatus" title="Polling every 3s">
            <span class="live-dot" style="background:#f59e0b;"></span>
            <span id="wsStatusText">Loading...</span>
        </span>
        <button type="button" class="btn-ghost" style="font-size:.75rem;" onclick="refreshKitchen(true)">
            <i data-lucide="refresh-cw" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Refresh
        </button>
        <button type="button" class="btn-ghost" style="font-size:.75rem;" onclick="toggleKitchenFullscreen()">
            <i data-lucide="maximize-2" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Fullscreen
        </button>
    </div>
</div>

{{-- Auto-print banner --}}
<div id="autoPrintBanner" style="display:none;align-items:center;justify-content:space-between;gap:1rem;padding:.8rem 1.25rem;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);border-radius:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:.6rem;">
        <i data-lucide="printer" style="width:1.1rem;height:1.1rem;color:#f59e0b;stroke-width:2;flex-shrink:0;"></i>
        <p style="font-size:.8rem;font-weight:700;color:#f59e0b;margin:0;">Click anywhere to enable auto-printing (browser security requirement)</p>
    </div>
    <button onclick="enableAutoPrint()" style="font-size:.7rem;color:#f59e0b;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);border-radius:.5rem;padding:.4rem .8rem;cursor:pointer;white-space:nowrap;">Enable Auto-Print</button>
</div>

{{-- Popup notice --}}
<div id="popupNotice" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.6rem 1.25rem;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.25);border-radius:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <i data-lucide="info" style="width:.9rem;height:.9rem;color:#3b82f6;stroke-width:2;flex-shrink:0;"></i>
        <p style="font-size:.75rem;color:var(--text-muted);margin:0;">For auto-print: allow popups for this site in your browser address bar when prompted.</p>
    </div>
    <button onclick="document.getElementById('popupNotice').style.display='none';localStorage.setItem('kitchenPopupDismissed','1')" style="font-size:.7rem;color:var(--text-muted);background:none;border:none;cursor:pointer;white-space:nowrap;">Dismiss</button>
</div>

{{-- ── Main cooking board ──────────────────────────────── --}}
<div class="section-card">
    <div class="filter-bar" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;padding:.85rem 1.25rem;border-bottom:1px solid var(--border-divider);">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i data-lucide="flame" style="width:.9rem;height:.9rem;color:#dc2626;stroke-width:2;"></i>
            <span style="font-size:.85rem;font-weight:700;color:var(--text-strong);">Cooking Board</span>
        </div>
        <span id="kitchenOrderCount" style="font-size:.72rem;color:var(--text-muted);margin-left:.25rem;"></span>
        <span style="margin-left:auto;font-size:.68rem;color:var(--text-muted);display:flex;align-items:center;gap:.3rem;">
            <span id="pollDotKitchen" style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;transition:background .3s;"></span>
            <span id="pollLabelKitchen">Live</span>
        </span>
    </div>
    <div class="kitchen-grid" id="kitchenGrid">
        @forelse($cookingOrders as $order)
            {{-- initial server-side render; JS takes over after first poll --}}
            @php
                $elapsed = (int) $order->created_at->diffInMinutes(now());
                $urgent  = $elapsed >= 20;
                $elapsedBg    = $elapsed >= 20 ? 'rgba(239,68,68,.15)' : ($elapsed >= 10 ? 'rgba(245,158,11,.15)' : 'rgba(255,255,255,.06)');
                $elapsedColor = $elapsed >= 20 ? '#ef4444' : ($elapsed >= 10 ? '#f59e0b' : 'var(--text-muted)');
                $elapsedLabel = $elapsed >= 20 ? "{$elapsed}m — URGENT" : "{$elapsed}m ago";
                $statusBg     = $order->status === 'accepted' ? 'rgba(59,130,246,.12)' : 'rgba(220,38,38,.12)';
                $statusColor  = $order->status === 'accepted' ? '#3b82f6' : '#f87171';
                $statusLabel  = $order->status === 'accepted' ? 'In Queue' : 'Cooking';
            @endphp
            <div class="order-card{{ $urgent ? ' is-urgent' : '' }}" data-order-id="{{ $order->id }}">
                <div class="order-card-header">
                    <div>
                        <div class="order-card-num">{{ $order->order_number }}</div>
                        <div class="order-card-meta" style="margin-top:.2rem;">
                            <span>{{ $order->user?->name ?? 'Guest' }} · {{ $order->created_at->format('g:i A') }}</span>
                            @if($order->order_type === 'dine_in' && $order->table_number)
                                <span style="color:#4ade80;font-weight:700;">🪑 Table {{ $order->table_number }}</span>
                            @endif
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.3rem;">
                        <span class="order-card-badge" style="background:{{ $statusBg }};color:{{ $statusColor }};">{{ $statusLabel }}</span>
                        <span class="k-elapsed" style="background:{{ $elapsedBg }};color:{{ $elapsedColor }};">{{ $elapsedLabel }}</span>
                    </div>
                </div>
                <div class="order-card-body">
                    <div class="order-card-items">
                        @foreach($order->items->take(3) as $item)
                            <div class="order-card-item">
                                <span class="order-card-item-qty">x{{ $item->quantity }}</span>
                                <span class="order-card-item-name">{{ $item->item_name }}</span>
                            </div>
                        @endforeach
                        @if($order->items->count() > 3)
                            <span class="order-card-item-more">+{{ $order->items->count() - 3 }} more</span>
                        @endif
                    </div>
                    @if($order->notes)
                        <div style="font-size:.72rem;color:#fbbf24;padding:.4rem .5rem;border-radius:.4rem;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.15);">📝 {{ $order->notes }}</div>
                    @endif
                    {{-- no price on kitchen display --}}
                </div>
                <div class="order-card-actions">
                    @if($order->order_type === 'dine_in' && $order->table_number)
                    <button class="oc-btn oc-btn-print" onclick="event.stopPropagation();printReceipt('/chef/orders/{{ $order->id }}/session-ticket')" title="Print kitchen ticket">
                    @else
                    <button class="oc-btn oc-btn-print" onclick="event.stopPropagation();printKitchenTicket({{ $order->id }})" title="Print ticket">
                    @endif
                        <i data-lucide="printer" style="width:13px;height:13px;stroke-width:2;"></i>
                    </button>
                    @if($order->order_type === 'dine_in')
                        <button class="oc-btn oc-btn-remove" onclick="event.stopPropagation();openRemoveItemModal({{ $order->id }},this)" title="Remove item">✕</button>
                    @endif
                    <button class="oc-btn oc-btn-ready" onclick="event.stopPropagation();markReady({{ $order->id }},this)">✅ Mark Ready</button>
                </div>
            </div>
        @empty
            <div class="kitchen-grid-empty">
                <svg width="32" height="32" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
                No active orders right now
            </div>
        @endforelse
    </div>
</div>

{{-- ── Remove Item Modal ───────────────────────────────── --}}
<div class="k-remove-backdrop" id="removeItemModal" onclick="closeRemoveItemModal(event)">
    <div class="k-remove-modal">
        <div class="k-remove-header">
            <div>
                <div class="k-remove-title">Remove Item from Order</div>
                <div class="k-remove-subtitle" id="removeModalOrderLabel">—</div>
            </div>
            <button class="k-modal-close" onclick="closeRemoveItemModal()">✕</button>
        </div>
        <div class="k-remove-body" id="removeItemList"></div>
        <div id="removeQtyRow" style="display:none;padding:.6rem 1.2rem .4rem;border-top:1px solid var(--border-divider);">
            <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:.45rem;">How many to cancel?</div>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <button type="button" id="removeQtyDec" onclick="adjustRemoveQty(-1)" style="width:2.1rem;height:2.1rem;border-radius:.5rem;border:1px solid var(--border-divider);background:rgba(255,255,255,.06);color:var(--text-strong);font-size:1.1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">−</button>
                <span id="removeQtyDisplay" style="font-size:1.3rem;font-weight:800;color:#facc15;min-width:2rem;text-align:center;">1</span>
                <button type="button" id="removeQtyInc" onclick="adjustRemoveQty(1)" style="width:2.1rem;height:2.1rem;border-radius:.5rem;border:1px solid var(--border-divider);background:rgba(255,255,255,.06);color:var(--text-strong);font-size:1.1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">+</button>
                <span style="font-size:.78rem;color:var(--text-muted);flex:1;">of <strong id="removeQtyMax" style="color:var(--text-strong);">1</strong> total</span>
            </div>
        </div>
        <div class="k-remove-footer">
            <button id="removeItemConfirmBtn" class="oc-btn oc-btn-ready" style="background:rgba(239,68,68,.15);color:#ef4444;border-color:rgba(239,68,68,.3);" onclick="confirmRemoveItem()" disabled>Remove Selected Item</button>
            <button class="oc-btn oc-btn-print" onclick="closeRemoveItemModal()">Keep All</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Constants ──────────────────────────────────────────────────────────────
const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]').content;
const KITCHEN_URL     = '{{ route('chef.orders') }}';
const READY_URL       = id  => `/chef/orders/${id}/ready`;
const TABLE_READY_URL = key => `/chef/orders/table-session/${encodeURIComponent(key)}/ready`;
const REMOVE_URL      = (oid, iid) => `/chef/orders/${oid}/items/${iid}`;

// ── State ──────────────────────────────────────────────────────────────────
let orderDataMap     = {};   // id → order object
let pollTimer        = null;
let gridSignature    = '';   // smart-diff: skip re-render when nothing changed
let printedOrderIds  = new Set();
let printedItemIds   = {};   // orderId → Set<itemId>
let autoPrintEnabled = false;

// ── Utilities ──────────────────────────────────────────────────────────────
function escH(s) {
    if (!s) return '';
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function elapsedBadge(mins) {
    if (mins >= 20) return `<span class="k-elapsed" style="background:rgba(239,68,68,.15);color:#ef4444;">${mins}m — URGENT</span>`;
    if (mins >= 10) return `<span class="k-elapsed" style="background:rgba(245,158,11,.15);color:#f59e0b;">${mins}m</span>`;
    return `<span class="k-elapsed" style="background:rgba(255,255,255,.06);color:var(--text-muted);">${mins}m ago</span>`;
}

function statusBadge(status) {
    const map = {
        accepted:  { bg:'rgba(59,130,246,.12)',  color:'#3b82f6', label:'In Queue'  },
        preparing: { bg:'rgba(220,38,38,.12)',   color:'#f87171', label:'Cooking'   },
    };
    const s = map[status] || { bg:'rgba(255,255,255,.06)', color:'var(--text-muted)', label: status };
    return `<span class="order-card-badge" style="background:${s.bg};color:${s.color};">${s.label}</span>`;
}

function showToast(msg, type='success', dur=3000) {
    let tc = document.getElementById('toastContainer');
    if (!tc) { tc = document.createElement('div'); tc.id='toastContainer'; tc.style.cssText='position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:8px;'; document.body.appendChild(tc); }
    const t = document.createElement('div');
    const bg = type==='success'?'rgba(16,185,129,.9)':type==='error'?'rgba(239,68,68,.9)':'rgba(59,130,246,.9)';
    t.style.cssText = `background:${bg};color:#fff;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:600;box-shadow:0 8px 32px rgba(0,0,0,.3);max-width:300px;word-wrap:break-word;`;
    t.textContent = msg; tc.appendChild(t);
    setTimeout(() => { t.style.opacity='0'; t.style.transition='opacity .3s'; setTimeout(()=>t.remove(),300); }, dur);
}
</script>
@endpush

@push('scripts')
<script>
// ── Grouping: same logic as admin orders renderGrid() ──────────────────────
function groupOrders(orders) {
    const groups = [];
    const buckets = {}; // sessionKey → group

    for (const o of orders) {
        const isDineIn = o.order_type === 'dine_in' && o.table_number;
        if (isDineIn) {
            const key = o.table_session_id ? ('s_' + o.table_session_id) : ('t_' + o.table_number);
            if (!buckets[key]) {
                buckets[key] = { key, grouped: true, tableNumber: o.table_number, sessionId: o.table_session_id || o.table_number, orders: [] };
                groups.push(buckets[key]);
            }
            buckets[key].orders.push(o);
        } else {
            groups.push({ key: 'o_' + o.id, grouped: false, tableNumber: null, sessionId: null, orders: [o] });
        }
    }

    // Collapse single-order dine-in groups to solo cards
    return groups.map(g => {
        if (g.grouped && g.orders.length === 1) return { ...g, grouped: false };
        return g;
    });
}

// ── Items renderer (compact, 3-item preview) ───────────────────────────────
function renderItemsPreview(items, max = 3) {
    let html = '<div class="order-card-items">';
    items.slice(0, max).forEach(item => {
        html += `<div class="order-card-item">
            <span class="order-card-item-qty">x${item.qty}</span>
            <span class="order-card-item-name" title="${escH(item.name)}">${escH(item.name)}</span>
        </div>`;
    });
    if (items.length > max) html += `<span class="order-card-item-more">+${items.length - max} more</span>`;
    html += '</div>';
    return html;
}

// ── Single-order card ──────────────────────────────────────────────────────
function renderSoloCard(o) {
    const urgent   = o.elapsed_mins >= 20 ? ' is-urgent' : '';
    const elapsed  = elapsedBadge(o.elapsed_mins);
    const badge    = statusBadge(o.status);
    const tableRow = (o.order_type === 'dine_in' && o.table_number)
        ? `<span style="color:#4ade80;font-weight:700;font-size:.72rem;">🪑 Table ${escH(o.table_number)}</span>` : '';
    const notesRow = o.notes
        ? `<div style="font-size:.72rem;color:#fbbf24;padding:.4rem .5rem;border-radius:.4rem;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.15);">📝 ${escH(o.notes)}</div>` : '';

    const printBtn  = (o.order_type === 'dine_in' && o.table_number)
        ? `<button class="oc-btn oc-btn-print" onclick="event.stopPropagation();printReceipt('/chef/orders/${o.id}/session-ticket')" title="Print kitchen ticket"><i data-lucide="printer" style="width:13px;height:13px;stroke-width:2;"></i></button>`
        : `<button class="oc-btn oc-btn-print" onclick="event.stopPropagation();printKitchenTicket(${o.id})" title="Print ticket"><i data-lucide="printer" style="width:13px;height:13px;stroke-width:2;"></i></button>`;
    const removeBtn = (o.order_type === 'dine_in')
        ? `<button class="oc-btn oc-btn-remove" onclick="event.stopPropagation();openRemoveItemModal(${o.id},this)" title="Remove item">✕</button>` : '';
    const readyBtn  = `<button class="oc-btn oc-btn-ready" onclick="event.stopPropagation();markReady(${o.id},this)">✅ Mark Ready</button>`;

    return `<div class="order-card${urgent}" data-order-id="${o.id}" onclick="void(0)">
        <div class="order-card-header">
            <div>
                <div class="order-card-num">${escH(o.order_number)}</div>
                <div class="order-card-meta" style="margin-top:.2rem;">
                    <span>${escH(o.customer)} · ${o.placed_at}</span>
                    ${tableRow}
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.3rem;">
                ${badge}${elapsed}
            </div>
        </div>
        <div class="order-card-body">
            ${renderItemsPreview(o.items)}
            ${notesRow}
        </div>
        <div class="order-card-actions">${printBtn}${removeBtn}${readyBtn}</div>
    </div>`;
}

// ── Grouped dine-in card (multiple orders, same table session) ─────────────
function renderGroupCard(group) {
    const orders     = group.orders.sort((a, b) => a.id - b.id);
    const rep        = orders[0];
    const tableNum   = group.tableNumber;
    const sessionKey = group.sessionId;
    const grandTotal = orders.reduce((s, o) => s + parseFloat(o.total || o.subtotal || 0), 0);
    const maxElapsed = Math.max(...orders.map(o => o.elapsed_mins || 0));
    const urgent     = maxElapsed >= 20 ? ' is-urgent' : '';
    const elapsed    = elapsedBadge(maxElapsed);

    // ── Per-order sections: header badge + items ────────────────────────────
    // Each pahabol wave gets its own labeled block so the kitchen can see
    // exactly what belongs to which order.
    let subRows = '<div class="order-card-subitems">';
    orders.forEach((o, idx) => {
        const sc = o.status === 'accepted'
            ? { bg:'rgba(59,130,246,.12)', color:'#3b82f6', label:'In Queue' }
            : { bg:'rgba(220,38,38,.12)',  color:'#f87171', label:'Cooking'  };

        // Label first order as "Original", subsequent ones as "Pahabol #N"
        const waveLabel = idx === 0 ? 'Original' : `Pahabol #${idx}`;
        const waveColor = idx === 0 ? '#9ca3af' : '#facc15';
        const waveBg    = idx === 0 ? 'rgba(255,255,255,.04)' : 'rgba(250,204,21,.07)';
        const waveBorder= idx === 0 ? 'rgba(255,255,255,.08)' : 'rgba(250,204,21,.25)';

        // Items for this sub-order
        const itemsHtml = (o.items || []).map(item => `
            <div class="order-card-item">
                <span class="order-card-item-qty">x${item.qty}</span>
                <span class="order-card-item-name" title="${escH(item.name)}">${escH(item.name)}</span>
            </div>`).join('');

        const notesHtml = o.notes
            ? `<div style="font-size:.68rem;color:#fbbf24;margin-top:.3rem;padding:.3rem .45rem;border-radius:.35rem;background:rgba(245,158,11,.07);border:1px solid rgba(245,158,11,.15);">📝 ${escH(o.notes)}</div>`
            : '';

        // Individual Mark Ready button — only shown if this order isn't already ready
        const indivReadyBtn = (o.status !== 'preparing' || !o.prepared_at)
            ? `<button class="oc-btn oc-btn-ready" style="font-size:.68rem;padding:.28rem .6rem;flex-shrink:0;" onclick="event.stopPropagation();markReady(${o.id},this)">✅ Ready</button>`
            : `<span style="font-size:.65rem;font-weight:700;color:#10b981;padding:.28rem .5rem;flex-shrink:0;">✓ Ready</span>`;

        subRows += `
        <div style="border:1px solid ${waveBorder};border-radius:.55rem;overflow:hidden;background:${waveBg};">
            <div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;padding:.35rem .55rem;border-bottom:1px solid ${waveBorder};background:${waveBg};">
                <span style="font-size:.6rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:${waveColor};flex-shrink:0;">${waveLabel}</span>
                <span class="order-card-subrow-num">${escH(o.order_number)}</span>
                <span class="order-card-badge" style="background:${sc.bg};color:${sc.color};font-size:.56rem;padding:.1rem .4rem;">${sc.label}</span>
                <span style="margin-left:auto;">${indivReadyBtn}</span>
            </div>
            <div class="order-card-items" style="padding:.35rem .55rem .4rem;">
                ${itemsHtml}
                ${notesHtml}
            </div>
        </div>`;
    });
    subRows += '</div>';

    const tableReceiptUrl = `/chef/orders/${rep.id}/session-ticket`;
    const orderIds  = orders.map(o => o.id);
    const printBtn  = `<button class="oc-btn oc-btn-print" onclick="event.stopPropagation();printReceipt('${tableReceiptUrl}')" title="Print kitchen ticket (all orders)"><i data-lucide="printer" style="width:13px;height:13px;stroke-width:2;"></i></button>`;
    const removeBtn = `<button class="oc-btn oc-btn-remove" onclick="event.stopPropagation();openRemoveItemModal([${orderIds.join(',')}])" title="Remove item">✕</button>`;

    // Bulk button: "Mark All Ready" — only shown if any order isn't ready yet
    const anyNotReady = orders.some(o => o.status !== 'preparing' || !o.prepared_at);
    const readyBtn  = anyNotReady
        ? `<button class="oc-btn oc-btn-ready" onclick="event.stopPropagation();markTableReady('${escH(sessionKey)}',this)">✅ Mark All Ready</button>`
        : `<span class="oc-btn" style="background:rgba(16,185,129,.12);color:#10b981;border:1px solid rgba(16,185,129,.25);flex:1;pointer-events:none;">✓ All Ready</span>`;

    return `<div class="order-card${urgent}" data-group-key="${escH(group.key)}" onclick="void(0)">
        <div class="order-card-header">
            <div>
                <div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;margin-bottom:.2rem;">
                    <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.28rem .65rem;border-radius:.5rem;background:rgba(74,222,128,.18);border:1.5px solid rgba(74,222,128,.5);font-size:.85rem;font-weight:800;color:#86efac;letter-spacing:.02em;">🪑 Table ${escH(tableNum)}</span>
                    <span style="font-size:.65rem;padding:2px 7px;border-radius:4px;background:rgba(245,158,11,.12);color:#f59e0b;border:1px solid rgba(245,158,11,.25);font-weight:700;">${orders.length} orders</span>
                </div>
                <div class="order-card-meta"><span>${escH(rep.customer)}</span></div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.3rem;">${elapsed}</div>
        </div>
        <div class="order-card-body">
            <div style="margin-top:.1rem;">${subRows}</div>
        </div>
        <div class="order-card-actions">${printBtn}${removeBtn}${readyBtn}</div>
    </div>`;
}
</script>
@endpush

@push('scripts')
<script>
// ── Grid renderer ──────────────────────────────────────────────────────────
function renderGrid(orders) {
    const grid    = document.getElementById('kitchenGrid');
    const countEl = document.getElementById('kitchenOrderCount');
    if (!grid) return;

    // Smart diff: skip re-render if nothing changed
    const sig = orders.map(o => o.id + ':' + (o.updated_at || o.status)).join('|');
    if (sig === gridSignature) return;
    gridSignature = sig;

    // Update order data map
    orders.forEach(o => { orderDataMap[o.id] = o; });

    if (!countEl) {} else countEl.textContent = orders.length + ' order(s)';

    if (!orders.length) {
        grid.innerHTML = `<div class="kitchen-grid-empty">
            <svg width="32" height="32" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>
            No active orders right now
        </div>`;
        if (window.lucide) lucide.createIcons();
        return;
    }

    const groups = groupOrders(orders);
    grid.innerHTML = groups.map(g => g.grouped ? renderGroupCard(g) : renderSoloCard(g.orders[0])).join('');
    if (window.lucide) lucide.createIcons();
}

// ── Kitchen actions ────────────────────────────────────────────────────────
async function markReady(orderId, btn) {
    btn.disabled = true;
    const orig = btn.textContent;
    btn.textContent = '…';
    try {
        const res = await fetch(READY_URL(orderId), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Action failed.'); btn.disabled = false; btn.textContent = orig; return; }
        showToast('✅ Order marked ready', 'success');
        gridSignature = ''; // force re-render
        await refreshKitchen(false);
    } catch (e) {
        alert('Network error. Please try again.');
        btn.disabled = false; btn.textContent = orig;
    }
}

async function markTableReady(sessionKey, btn) {
    btn.disabled = true;
    const orig = btn.textContent;
    btn.textContent = '…';
    try {
        const res = await fetch(TABLE_READY_URL(sessionKey), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Action failed.'); btn.disabled = false; btn.textContent = orig; return; }
        showToast(data.message || '✅ Table marked ready', 'success');
        gridSignature = '';
        await refreshKitchen(false);
    } catch (e) {
        alert('Network error. Please try again.');
        btn.disabled = false; btn.textContent = orig;
    }
}

// ── Print helpers ──────────────────────────────────────────────────────────
function autoPrintKitchenTicket(orderId, addonIds) {
    let url = `/chef/orders/${orderId}/kitchen-ticket`;
    if (addonIds && addonIds.length) url += '?addon_ids=' + addonIds.join(',');
    const iframe = document.createElement('iframe');
    iframe.name = 'kitchen_autoprint';
    iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:210px;height:600px;border:0;opacity:0;pointer-events:none;z-index:-1';
    iframe.setAttribute('tabindex', '-1'); iframe.setAttribute('aria-hidden', 'true');
    document.body.appendChild(iframe);
    function rm() { if (iframe.parentNode) iframe.parentNode.removeChild(iframe); }
    function onMsg(e) { if (e.data?.type === 'kitchen_ticket_printed') { window.removeEventListener('message', onMsg); setTimeout(rm, 500); } }
    window.addEventListener('message', onMsg);
    setTimeout(() => { window.removeEventListener('message', onMsg); rm(); }, 15000);
    iframe.onload = () => showToast('🖨️ Printing ticket…', 'success', 1800);
    iframe.onerror = () => { window.removeEventListener('message', onMsg); rm(); window.open(url, '_blank'); };
    iframe.src = url;
}

function printKitchenTicket(orderId) { autoPrintKitchenTicket(orderId, null); }

function printReceipt(url) {
    const w=220, h=800, l=Math.round((screen.width-w)/2), t=Math.round((screen.height-h)/2);
    const win = window.open(url, 'receipt_print', `width=${w},height=${h},left=${l},top=${t},toolbar=0,scrollbars=0,status=0,menubar=0,location=0`);
    if (!win) window.open(url, '_blank');
}

function enableAutoPrint() {
    autoPrintEnabled = true;
    const banner = document.getElementById('autoPrintBanner');
    if (banner) {
        banner.style.background  = 'rgba(16,185,129,.08)';
        banner.style.borderColor = 'rgba(16,185,129,.25)';
        banner.innerHTML = `<div style="display:flex;align-items:center;gap:.6rem;"><i data-lucide="check-circle-2" style="width:1.1rem;height:1.1rem;color:#10b981;stroke-width:2;flex-shrink:0;"></i><p style="font-size:.8rem;font-weight:700;color:#10b981;margin:0;">✓ Auto-Print ENABLED — tickets print automatically when orders are accepted</p></div>`;
        if (window.lucide) lucide.createIcons();
        setTimeout(() => { banner.style.display = 'none'; }, 5000);
    }
    // Mark already-visible orders as printed so they don't re-print on reload
    Object.keys(orderDataMap).forEach(id => {
        printedOrderIds.add('accept_' + id);
        printedOrderIds.add('accept_' + id + '_' + (orderDataMap[id]?.updated_at || ''));
        if (!printedItemIds[id]) printedItemIds[id] = new Set();
        (orderDataMap[id]?.items || []).forEach(i => printedItemIds[id].add(i.id));
    });
    localStorage.setItem('autoPrintEnabled', 'true');
}
</script>
@endpush

@push('scripts')
<script>
// ── Remove Item Modal ──────────────────────────────────────────────────────
let _rmOrderId = null, _rmItemId = null, _rmQty = 1, _rmMax = 1;

// openRemoveItemModal accepts either a single orderId or an array of orderIds (for grouped table cards)
function openRemoveItemModal(orderIdOrIds) {
    const orderIds = Array.isArray(orderIdOrIds) ? orderIdOrIds : [orderIdOrIds];
    const orders   = orderIds.map(id => orderDataMap[id]).filter(Boolean);
    if (!orders.length) return;

    _rmOrderId = orders[0].id; // default; overridden per item selection
    _rmItemId  = null; _rmQty = 1; _rmMax = 1;

    const first = orders[0];
    const label = orders.length > 1
        ? `Table ${first.table_number} · ${orders.length} orders`
        : (first.order_number || '') + (first.table_number ? ' · Table ' + first.table_number : '');
    document.getElementById('removeModalOrderLabel').textContent = label;

    // Build item rows across ALL orders in the group, tagging each with its orderId
    document.getElementById('removeItemList').innerHTML = orders.flatMap(order =>
        (order.items || []).map(item => `
            <div class="k-remove-item-row" data-order-id="${order.id}" onclick="selectRemoveItem(this,${order.id},${item.id},${item.qty})">
                <img class="k-remove-item-img" src="${escH(item.image || '')}" alt="" onerror="this.src='{{ asset('images/menu/default-menu-item.webp') }}'">
                <div style="flex:1;min-width:0;">
                    <span class="k-remove-item-name">${escH(item.name)}</span>
                    ${orders.length > 1 ? `<div style="font-size:.62rem;color:var(--text-muted);margin-top:.1rem;">${escH(order.order_number)}</div>` : ''}
                </div>
                <span class="k-remove-item-qty">${item.qty}×</span>
                <span class="k-remove-check">✓</span>
            </div>`)
    ).join('');

    document.getElementById('removeQtyRow').style.display = 'none';
    const cb = document.getElementById('removeItemConfirmBtn');
    cb.disabled = true; cb.textContent = 'Remove Selected Item';
    document.getElementById('removeItemModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function selectRemoveItem(el, orderId, itemId, qty) {
    document.querySelectorAll('#removeItemList .k-remove-item-row').forEach(r => r.classList.remove('selected'));
    el.classList.add('selected');
    _rmOrderId = orderId; _rmItemId = itemId; _rmMax = qty; _rmQty = qty;
    document.getElementById('removeQtyRow').style.display = 'block';
    document.getElementById('removeQtyDisplay').textContent = _rmQty;
    document.getElementById('removeQtyMax').textContent = _rmMax;
    _syncRmBtns();
    _updateConfirmBtn(el.querySelector('.k-remove-item-name')?.textContent || 'item');
}

function adjustRemoveQty(d) {
    _rmQty = Math.max(1, Math.min(_rmMax, _rmQty + d));
    document.getElementById('removeQtyDisplay').textContent = _rmQty;
    _syncRmBtns();
    const sel = document.querySelector('#removeItemList .k-remove-item-row.selected');
    _updateConfirmBtn(sel?.querySelector('.k-remove-item-name')?.textContent || 'item');
}

function _syncRmBtns() {
    document.getElementById('removeQtyDec').disabled = _rmQty <= 1;
    document.getElementById('removeQtyInc').disabled = _rmQty >= _rmMax;
}

function _updateConfirmBtn(name) {
    const cb = document.getElementById('removeItemConfirmBtn');
    cb.disabled = false;
    cb.textContent = _rmQty >= _rmMax ? `Remove all "${name}"` : `Remove ${_rmQty}× of ${_rmMax}× "${name}"`;
}

function closeRemoveItemModal(e) {
    if (e && e.target !== document.getElementById('removeItemModal')) return;
    document.getElementById('removeItemModal').classList.remove('open');
    document.body.style.overflow = '';
    _rmOrderId = _rmItemId = null; _rmQty = _rmMax = 1;
}

async function confirmRemoveItem() {
    if (!_rmOrderId || !_rmItemId) return;
    const cb = document.getElementById('removeItemConfirmBtn');
    cb.disabled = true; cb.textContent = 'Removing...';
    try {
        const res = await fetch(REMOVE_URL(_rmOrderId, _rmItemId), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ qty: _rmQty }),
        });
        const data = await res.json();
        if (!data.success) { showToast(data.message || 'Could not remove item.', 'error'); cb.disabled = false; cb.textContent = 'Remove Selected Item'; return; }
        showToast(data.items_left === 0 ? 'All items removed — order cancelled.' : (data.message || `Item updated. ${data.items_left} item(s) remaining.`), 'success');
        document.getElementById('removeItemModal').classList.remove('open');
        document.getElementById('removeQtyRow').style.display = 'none';
        document.body.style.overflow = '';
        _rmOrderId = _rmItemId = null; _rmQty = _rmMax = 1;
        gridSignature = '';
        await refreshKitchen(false);
    } catch (err) {
        showToast('Network error. Please try again.', 'error');
        cb.disabled = false; cb.textContent = 'Remove Selected Item';
    }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeRemoveItemModal(); });
</script>
@endpush

@push('scripts')
<script>
// ── Polling / status ───────────────────────────────────────────────────────
function setPollStatus(ok) {
    const dot   = document.getElementById('pollDotKitchen');
    const label = document.getElementById('pollLabelKitchen');
    const wsDot = document.querySelector('.live-dot');
    const wsTxt = document.getElementById('wsStatusText');
    if (ok) {
        if (dot)   dot.style.background = '#10b981';
        if (label) label.textContent    = 'Live';
        if (wsDot && !wsTxt?.textContent.includes('WS')) wsDot.style.background = '#22c55e';
        if (wsTxt && !wsTxt.textContent.includes('WS'))  wsTxt.textContent = 'Live';
    } else {
        if (dot)   dot.style.background = '#ef4444';
        if (label) label.textContent    = 'Offline';
        if (wsDot) wsDot.style.background = '#ef4444';
        if (wsTxt) wsTxt.textContent    = 'Offline';
    }
}

function toggleKitchenFullscreen() { document.body.classList.toggle('kitchen-fullscreen'); }

// ── Main refresh ───────────────────────────────────────────────────────────
async function refreshKitchen(manual) {
    try {
        const ctrl = new AbortController();
        const t = setTimeout(() => ctrl.abort(), 8000);
        const res = await fetch(KITCHEN_URL, { headers: { 'Accept': 'application/json' }, signal: ctrl.signal });
        clearTimeout(t);
        if (!res.ok) throw new Error('HTTP ' + res.status);
        const ct = res.headers.get('content-type') || '';
        if (!ct.includes('application/json')) { setPollStatus(false); setTimeout(() => location.reload(), 2000); return; }

        const data = await res.json();
        setPollStatus(true);

        // Render the single cooking board (accepted + preparing merged)
        renderGrid(data.cooking || []);

        // ── AUTO-PRINT (unchanged — watches data.queued for newly-accepted) ──
        // data.queued = only accepted-status orders (alias from ChefController)
        (data.queued || []).forEach(order => {
            const printKey       = 'accept_' + order.id;
            const currentItemIds = (order.items || []).map(i => i.id).filter(Boolean);
            if (!printedItemIds[order.id]) printedItemIds[order.id] = new Set();
            const newItemIds = currentItemIds.filter(id => !printedItemIds[order.id].has(id));
            if (!autoPrintEnabled) return;
            if (!printedOrderIds.has(printKey)) {
                printedOrderIds.add(printKey);
                printedOrderIds.add('accept_' + order.id + '_' + (order.updated_at || ''));
                currentItemIds.forEach(id => printedItemIds[order.id].add(id));
                setTimeout(() => autoPrintKitchenTicket(order.id, null), 500);
            } else if (newItemIds.length > 0) {
                newItemIds.forEach(id => printedItemIds[order.id].add(id));
                setTimeout(() => autoPrintKitchenTicket(order.id, newItemIds), 500);
            }
        });

        // Auto-print ready orders (cooking col, prepared_at set)
        (data.cooking || []).forEach(order => {
            if (order.prepared_at && autoPrintEnabled && !printedOrderIds.has('ready_' + order.id)) {
                printedOrderIds.add('ready_' + order.id);
                setTimeout(() => autoPrintKitchenTicket(order.id, null), 500);
            }
        });

    } catch (e) {
        setPollStatus(false);
        if (manual) alert('Could not refresh kitchen: ' + e.message);
    }
}

// ── DOMContentLoaded ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();

    // Restore auto-print preference
    if (localStorage.getItem('autoPrintEnabled') === 'true') {
        autoPrintEnabled = true;
    } else {
        const b = document.getElementById('autoPrintBanner'); if (b) b.style.display = 'flex';
    }

    // Enable on first interaction
    const enableOnce = () => {
        if (!autoPrintEnabled) enableAutoPrint();
        document.removeEventListener('click',      enableOnce);
        document.removeEventListener('keydown',    enableOnce);
        document.removeEventListener('touchstart', enableOnce);
    };
    document.addEventListener('click',      enableOnce);
    document.addEventListener('keydown',    enableOnce);
    document.addEventListener('touchstart', enableOnce);

    if (localStorage.getItem('kitchenPopupDismissed')) {
        const n = document.getElementById('popupNotice'); if (n) n.style.display = 'none';
    }

    // ── Seed orderDataMap from server-rendered data ────────────────────────
    @php
        $kitchenSeed = $cookingOrders->map(function($o) {
            $mods = fn($i) => collect($i->modifiers ?? [])
                ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
                ->values()->all();
            return [
                'id'               => $o->id,
                'order_number'     => $o->order_number,
                'customer'         => $o->user?->name ?? 'Guest',
                'status'           => $o->status,
                'order_type'       => $o->order_type,
                'order_type_label' => $o->order_type_label,
                'order_type_icon'  => $o->order_type_icon,
                'placed_at'        => $o->created_at->format('g:i A'),
                'elapsed_mins'     => (int) $o->created_at->diffInMinutes(now()),
                'notes'            => $o->notes,
                'table_number'     => $o->table_number,
                'table_session_id' => $o->table_session_id,
                'subtotal'         => (float)($o->subtotal ?? 0),
                'total'            => (float)($o->total ?? 0),
                'payment_method'   => $o->payment_method,
                'prepared_at'      => $o->prepared_at?->toISOString(),
                'updated_at'       => $o->updated_at?->toISOString(),
                'items'            => $o->items->map(fn($i) => [
                    'id'        => $i->id,
                    'name'      => $i->item_name,
                    'qty'       => $i->quantity,
                    'price'     => (float)$i->unit_price,
                    'subtotal'  => (float)$i->subtotal,
                    'image'     => $i->image ? asset($i->image) : asset('images/hero-burger.webp'),
                    'modifiers' => $mods($i),
                ])->all(),
            ];
        })->all();
    @endphp
    const _seed = @json($kitchenSeed);
    _seed.forEach(o => {
        orderDataMap[o.id] = o;
        // Seed print guards so existing orders don't re-print on reload
        if (['accepted','preparing'].includes(o.status)) {
            printedOrderIds.add('accept_' + o.id);
            printedOrderIds.add('accept_' + o.id + '_' + (o.updated_at || ''));
            if (!printedItemIds[o.id]) printedItemIds[o.id] = new Set();
            o.items.forEach(i => printedItemIds[o.id].add(i.id));
        }
        if (o.status === 'preparing' && o.prepared_at) printedOrderIds.add('ready_' + o.id);
    });

    // ── WebSocket (Echo) ───────────────────────────────────────────────────
    if (window.Echo) {
        try {
            window.Echo.private('kitchen').listen('.order.updated', (order) => {
                // Auto-print on accept (unchanged)
                if (autoPrintEnabled && order.status === 'accepted') {
                    const pk  = 'accept_' + order.id;
                    const ids = (order.items || []).map(i => i.id).filter(Boolean);
                    if (!printedItemIds[order.id]) printedItemIds[order.id] = new Set();
                    const newIds = ids.filter(id => !printedItemIds[order.id].has(id));
                    if (!printedOrderIds.has(pk)) {
                        printedOrderIds.add(pk);
                        ids.forEach(id => printedItemIds[order.id].add(id));
                        setTimeout(() => autoPrintKitchenTicket(order.id, null), 400);
                    } else if (newIds.length) {
                        newIds.forEach(id => printedItemIds[order.id].add(id));
                        setTimeout(() => autoPrintKitchenTicket(order.id, newIds), 400);
                    }
                }
                if (autoPrintEnabled && order.status === 'preparing' && order.prepared_at && !printedOrderIds.has('ready_' + order.id)) {
                    printedOrderIds.add('ready_' + order.id);
                    autoPrintKitchenTicket(order.id, null);
                }
                gridSignature = ''; // force re-render on next poll
                refreshKitchen(false);
            });
            if (window.Echo.connector?.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    const t = document.getElementById('wsStatusText');
                    const d = document.querySelector('.live-dot');
                    if (t) t.textContent = 'Live (WS)';
                    if (d) d.style.background = '#22c55e';
                });
            }
        } catch (e) { console.warn('Echo subscription failed:', e); }
    }

    // ── Poll every 3s ──────────────────────────────────────────────────────
    if (!pollTimer) {
        refreshKitchen(false);
        pollTimer = setInterval(() => refreshKitchen(false), 3000);
    }

    setTimeout(() => {
        const t = document.getElementById('wsStatusText');
        if (t && t.textContent === 'Loading...') refreshKitchen(true);
    }, 5000);
});
</script>
@endpush
