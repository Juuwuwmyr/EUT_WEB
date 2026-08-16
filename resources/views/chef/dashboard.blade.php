@extends('chef.layout')
@section('title', 'Kitchen Display')

@push('head')
<style>
/* ── Layout ──────────────────────────────────────────────── */
.kitchen-board {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    min-height: calc(100vh - 220px);
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
</style>
@endpush

@push('head')
<style>
/* ── Cards ───────────────────────────────────────────────── */
.k-order-card {
    background: var(--bg-card);
    border: 1px solid var(--border-card);
    border-radius: .875rem;
    overflow: hidden;
    transition: transform .15s, box-shadow .15s;
    animation: kSlideIn .25s ease;
    cursor: pointer;
}
.k-order-card:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,.25); }
.k-order-card.is-urgent { border-color: rgba(239,68,68,.45); box-shadow: 0 0 0 1px rgba(239,68,68,.15); }

@keyframes kSlideIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Card header */
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
.k-customer { font-size: .78rem; color: var(--text-muted); margin-top: .15rem; }

/* Items list inside card */
.k-items {
    padding: .65rem 1rem;
    display: flex;
    flex-direction: column;
    gap: .55rem;
}
.k-item { display: flex; align-items: flex-start; gap: .6rem; }
.k-item-img {
    width: 42px; height: 42px;
    border-radius: .5rem;
    object-fit: cover;
    flex-shrink: 0;
    border: 1px solid var(--border-divider);
}
.k-item-qty  { font-size: .85rem; font-weight: 800; color: #facc15; min-width: 1.5rem; }
.k-item-name { font-size: .88rem; font-weight: 700; color: var(--text-strong); line-height: 1.35; }
.k-modifiers { display: flex; flex-wrap: wrap; gap: .25rem; margin-top: .25rem; }
.k-mod-tag {
    font-size: .62rem; font-weight: 600;
    padding: .15rem .45rem; border-radius: .35rem;
    background: rgba(59,130,246,.12); color: #60a5fa;
    border: 1px solid rgba(59,130,246,.2);
}

/* Notes */
.k-notes {
    margin: 0 1rem .65rem;
    padding: .55rem .7rem;
    border-radius: .5rem;
    background: rgba(245,158,11,.08);
    border: 1px solid rgba(245,158,11,.2);
    font-size: .72rem; color: #fbbf24; line-height: 1.45;
}

/* Sub-order divider inside a grouped card */
.k-sub-divider {
    margin: 0 1rem .45rem;
    padding: .3rem .6rem;
    border-radius: .4rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.07);
    font-size: .7rem; font-weight: 700;
    color: var(--text-muted);
    display: flex; align-items: center; gap: .4rem;
}
</style>
@endpush

@push('head')
<style>
/* ── Action buttons ──────────────────────────────────────── */
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
    font-size: .78rem; font-weight: 700;
    cursor: pointer;
    transition: all .15s;
    display: inline-flex; align-items: center; justify-content: center; gap: .35rem;
}
.k-btn:disabled { opacity: .55; cursor: not-allowed; }

.k-btn-accept { background: #16a34a; color: #fff; }
.k-btn-accept:hover:not(:disabled) { background: #15803d; }
.k-btn-ready  { background: #d97706; color: #fff; }
.k-btn-ready:hover:not(:disabled)  { background: #b45309; }
.k-btn-remove {
    flex: 0 0 auto; width: 2.4rem;
    background: rgba(239,68,68,.1); color: #ef4444;
    border: 1px solid rgba(239,68,68,.25);
}
.k-btn-remove:hover:not(:disabled) {
    background: rgba(239,68,68,.22);
    border-color: rgba(239,68,68,.5);
}

/* ── Remove-item modal ───────────────────────────────────── */
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
.k-remove-header {
    padding: 1rem 1.2rem .8rem;
    border-bottom: 1px solid var(--border-divider);
    display: flex; align-items: center; justify-content: space-between;
}
.k-remove-title  { font-size: .95rem; font-weight: 800; color: var(--text-strong); }
.k-remove-subtitle { font-size: .72rem; color: var(--text-muted); margin-top: .1rem; }
.k-remove-body {
    padding: .75rem 1.2rem; display: flex; flex-direction: column; gap: .45rem;
    max-height: 52vh; overflow-y: auto;
}
.k-remove-item-row {
    display: flex; align-items: center; gap: .7rem;
    padding: .55rem .7rem; border-radius: .65rem;
    border: 1px solid var(--border-divider);
    background: rgba(255,255,255,.03); cursor: pointer;
    transition: border-color .15s, background .15s;
}
.k-remove-item-row:hover   { border-color: rgba(239,68,68,.4); background: rgba(239,68,68,.06); }
.k-remove-item-row.selected { border-color: rgba(239,68,68,.6); background: rgba(239,68,68,.1); }
.k-remove-item-img { width: 38px; height: 38px; border-radius: .45rem; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border-divider); }
.k-remove-item-name { flex: 1; font-size: .85rem; font-weight: 700; color: var(--text-strong); }
.k-remove-item-qty  { font-size: .75rem; font-weight: 800; color: #facc15; flex-shrink: 0; }
.k-remove-check {
    width: 1.25rem; height: 1.25rem; border-radius: 50%;
    border: 2px solid rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: .65rem; color: transparent; transition: all .15s;
}
.k-remove-item-row.selected .k-remove-check { background: #ef4444; border-color: #ef4444; color: #fff; }
.k-remove-footer {
    padding: .75rem 1.2rem 1rem;
    border-top: 1px solid var(--border-divider);
    display: flex; gap: .5rem;
}

/* ── Empty state ─────────────────────────────────────────── */
.k-empty { text-align: center; padding: 2.5rem 1rem; color: var(--text-muted); font-size: .8rem; }
.k-empty-icon { margin-bottom: .5rem; opacity: .6; display:flex; justify-content:center; }

/* ── Toolbar ─────────────────────────────────────────────── */
.kitchen-toolbar {
    display: flex; flex-wrap: wrap;
    align-items: center; justify-content: space-between;
    gap: 1rem; margin-bottom: 1.25rem;
}
.kitchen-live {
    display: inline-flex; align-items: center;
    gap: .45rem; font-size: .75rem; color: var(--text-muted);
}
.live-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: #22c55e; animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.85); }
}

body.kitchen-fullscreen .admin-nav       { display: none; }
body.kitchen-fullscreen .admin-content   { max-width: none; padding: 1rem; }
body.kitchen-fullscreen .kitchen-hide-fs { display: none; }
</style>
@endpush

@push('head')
<style>
/* ── Order detail modal ──────────────────────────────────── */
.k-modal-backdrop {
    display: none; position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.75); backdrop-filter: blur(6px);
    align-items: center; justify-content: center; padding: 1rem;
}
.k-modal-backdrop.open { display: flex; }
.k-modal {
    background: var(--bg-card); border: 1px solid var(--border-card);
    border-radius: 1.25rem; width: 100%; max-width: 520px;
    max-height: 90vh; overflow-y: auto;
    box-shadow: 0 30px 80px rgba(0,0,0,.6); animation: kModalIn .2s ease;
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
    display: flex; align-items: center; justify-content: space-between; gap: .75rem;
    position: sticky; top: 0; background: var(--bg-card); z-index: 1;
}
.k-modal-title   { font-family: monospace; font-size: 1.1rem; font-weight: 800; color: var(--accent); }
.k-modal-close {
    width: 2rem; height: 2rem; border-radius: .5rem;
    border: 1px solid var(--border-divider); background: transparent;
    color: var(--text-muted); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; transition: all .15s; flex-shrink: 0;
}
.k-modal-close:hover { background: rgba(239,68,68,.12); color: #ef4444; border-color: rgba(239,68,68,.3); }

.k-modal-meta {
    padding: .75rem 1.25rem; border-bottom: 1px solid var(--border-divider);
    display: flex; gap: 1rem; flex-wrap: wrap; font-size: .75rem; color: var(--text-muted);
}
.k-modal-meta span { display: flex; align-items: center; gap: .3rem; }
.k-modal-meta strong { color: var(--text-strong); }
.k-modal-items { padding: .85rem 1.25rem; display: flex; flex-direction: column; gap: 1rem; }

.k-modal-item { background: rgba(255,255,255,.03); border: 1px solid var(--border-divider); border-radius: .875rem; overflow: hidden; }
.k-modal-item-header { display: flex; align-items: center; gap: .75rem; padding: .75rem .85rem; }
.k-modal-item-img { width: 52px; height: 52px; border-radius: .6rem; object-fit: cover; flex-shrink: 0; border: 1px solid var(--border-divider); }
.k-modal-item-qty  { font-size: 1rem; font-weight: 900; color: #facc15; flex-shrink: 0; }
.k-modal-item-name { font-size: .9rem; font-weight: 700; color: var(--text-strong); flex: 1; min-width: 0; }
.k-modal-item-price { font-size: .8rem; font-weight: 700; color: #4ade80; flex-shrink: 0; }

.k-modal-specs { border-top: 1px solid var(--border-divider); padding: .65rem .85rem .75rem; }
.k-modal-specs-label { font-size: .65rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--text-muted); margin-bottom: .45rem; }
.k-modal-spec-row { display: flex; align-items: center; justify-content: space-between; padding: .3rem 0; border-bottom: 1px solid rgba(255,255,255,.04); gap: .5rem; }
.k-modal-spec-row:last-child { border-bottom: none; }
.k-modal-spec-left { display: flex; align-items: center; gap: .4rem; }
.k-modal-spec-badge { font-size: .58rem; font-weight: 800; padding: .1rem .35rem; border-radius: .25rem; text-transform: uppercase; letter-spacing: .04em; flex-shrink: 0; }
.spec-flavor   { background: rgba(59,130,246,.15);  color: #3b82f6; }
.spec-modifier { background: rgba(139,92,246,.15); color: #8b5cf6; }
.spec-addon    { background: rgba(245,158,11,.15);  color: #d97706; }
.k-modal-spec-name  { font-size: .78rem; font-weight: 600; color: var(--text-strong); }
.k-modal-spec-price { font-size: .75rem; font-weight: 700; color: #4ade80; flex-shrink: 0; }
.k-modal-no-specs   { font-size: .75rem; color: var(--text-muted); font-style: italic; padding: .3rem 0; }

.k-modal-footer { padding: .85rem 1.25rem; border-top: 1px solid var(--border-divider); display: flex; flex-direction: column; gap: .4rem; }
.k-modal-total-row { display: flex; justify-content: space-between; font-size: .8rem; color: var(--text-muted); }
.k-modal-total-row.grand { font-size: .95rem; font-weight: 800; color: var(--text-strong); padding-top: .4rem; border-top: 1px solid var(--border-divider); margin-top: .2rem; }
.k-modal-total-row.grand span:last-child { color: #facc15; }

.k-modal-notes { margin: 0 1.25rem .85rem; padding: .65rem .85rem; border-radius: .65rem; background: rgba(245,158,11,.08); border: 1px solid rgba(245,158,11,.2); font-size: .78rem; color: #fbbf24; line-height: 1.5; }
.k-modal-action-bar { padding: .85rem 1.25rem 1rem; border-top: 1px solid var(--border-divider); display: flex; gap: .6rem; flex-wrap: wrap; }
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
            <p style="margin:0;font-size:.875rem;color:var(--text-muted);">Orders appear here when admin accepts — mark ready when done cooking.</p>
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

{{-- Auto-Print Banner --}}
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
        <p style="font-size:.75rem;color:var(--text-muted);margin:0;">For auto-print to work: allow popups for this site in your browser address bar when prompted.</p>
    </div>
    <button onclick="document.getElementById('popupNotice').style.display='none';localStorage.setItem('kitchenPopupDismissed','1')" style="font-size:.7rem;color:var(--text-muted);background:none;border:none;cursor:pointer;white-space:nowrap;">Dismiss</button>
</div>

<div class="kitchen-board" id="kitchenBoard">

    {{-- QUEUE: accepted orders (admin just accepted = start cooking immediately) --}}
    <div class="kitchen-col" data-col="queued">
        <div class="kitchen-col-header" style="background:rgba(59,130,246,.06);">
            <h2 class="kitchen-col-title">
                <i data-lucide="list-ordered" style="width:1rem;height:1rem;color:#3b82f6;stroke-width:2;"></i>
                Queue
                <span style="font-size:.68rem;font-weight:500;color:var(--text-muted);">— Start cooking now</span>
            </h2>
            <span class="kitchen-col-count" style="background:rgba(59,130,246,.15);color:#3b82f6;" id="count-queued">{{ $queuedOrders->count() }}</span>
        </div>
        <div class="kitchen-col-body" id="col-queued">
            @forelse($queuedOrders as $order)
                @include('admin.partials.kitchen-order-card', ['order' => $order, 'column' => 'queued'])
            @empty
                <div class="k-empty">
                    <div class="k-empty-icon"><svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                    Queue is empty
                </div>
            @endforelse
        </div>
    </div>

    {{-- COOKING: preparing orders (not yet marked ready) --}}
    <div class="kitchen-col" data-col="cooking">
        <div class="kitchen-col-header" style="background:rgba(220,38,38,.06);">
            <h2 class="kitchen-col-title">
                <i data-lucide="flame" style="width:1rem;height:1rem;color:#dc2626;stroke-width:2;"></i>
                Cooking
                <span style="font-size:.68rem;font-weight:500;color:var(--text-muted);">— Mark ready when done</span>
            </h2>
            <span class="kitchen-col-count" style="background:rgba(220,38,38,.15);color:#dc2626;" id="count-cooking">{{ $cookingOrders->count() }}</span>
        </div>
        <div class="kitchen-col-body" id="col-cooking">
            @forelse($cookingOrders as $order)
                @include('admin.partials.kitchen-order-card', ['order' => $order, 'column' => 'cooking'])
            @empty
                <div class="k-empty">
                    <div class="k-empty-icon"><svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg></div>
                    Nothing cooking
                </div>
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
        <div id="modalTableNote" style="display:none;padding:0 1.25rem .65rem;"></div>
        <div class="k-modal-footer" id="modalFooter"></div>
        <div class="k-modal-action-bar" id="modalActions"></div>
    </div>
</div>

{{-- ── Remove Item Modal ── --}}
<div class="k-remove-backdrop" id="removeItemModal" onclick="closeRemoveItemModal(event)">
    <div class="k-remove-modal">
        <div class="k-remove-header">
            <div>
                <div class="k-remove-title">Remove Item from Order</div>
                <div class="k-remove-subtitle" id="removeModalOrderLabel">—</div>
            </div>
            <button class="k-modal-close" onclick="closeRemoveItemModal()" style="flex-shrink:0;">✕</button>
        </div>
        <div class="k-remove-body" id="removeItemList"></div>

        <div id="removeQtyRow" style="display:none;padding:.6rem 1.2rem .4rem;border-top:1px solid var(--border-divider);">
            <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:.45rem;">How many to cancel?</div>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <button type="button" id="removeQtyDec" onclick="adjustRemoveQty(-1)"
                    style="width:2.1rem;height:2.1rem;border-radius:.5rem;border:1px solid var(--border-divider);background:rgba(255,255,255,.06);color:var(--text-strong);font-size:1.1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">−</button>
                <span id="removeQtyDisplay" style="font-size:1.3rem;font-weight:800;color:#facc15;min-width:2rem;text-align:center;">1</span>
                <button type="button" id="removeQtyInc" onclick="adjustRemoveQty(1)"
                    style="width:2.1rem;height:2.1rem;border-radius:.5rem;border:1px solid var(--border-divider);background:rgba(255,255,255,.06);color:var(--text-strong);font-size:1.1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">+</button>
                <span id="removeQtyLabel" style="font-size:.78rem;color:var(--text-muted);flex:1;">of <strong id="removeQtyMax" style="color:var(--text-strong);">1</strong> total</span>
            </div>
        </div>

        <div class="k-remove-footer">
            <button id="removeItemConfirmBtn" class="k-btn"
                style="flex:1;background:rgba(239,68,68,.15);color:#ef4444;border:1px solid rgba(239,68,68,.3);"
                onclick="confirmRemoveItem()" disabled>
                Remove Selected Item
            </button>
            <button class="k-btn" style="flex:0 0 auto;padding:.6rem 1rem;background:rgba(255,255,255,.06);color:var(--text-muted);"
                onclick="closeRemoveItemModal()">Keep All</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ── Constants ──────────────────────────────────────────────────────────────
const CSRF_TOKEN      = document.querySelector('meta[name="csrf-token"]').content;
const IS_ADMIN        = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
const KITCHEN_URL     = '{{ route('chef.orders') }}';
const ACCEPT_URL      = id => IS_ADMIN ? `/admin/orders/${id}/accept` : `/chef/orders/${id}/accept`;
const READY_URL       = id => `/chef/orders/${id}/ready`;
// Bulk ready for an entire table session (key = table_session_id OR table_number)
const TABLE_READY_URL = key => `/chef/orders/table-session/${encodeURIComponent(key)}/ready`;
const REMOVE_ITEM_URL = (orderId, itemId) => `/chef/orders/${orderId}/items/${itemId}`;

// ── State ──────────────────────────────────────────────────────────────────
let orderDataMap      = {};   // orderId → formatted order object
let fallbackTimer     = null;
let printedOrderIds   = new Set();
let printedItemIds    = {};   // orderId → Set of item IDs already printed
let autoPrintEnabled  = false;
let columnSignatures  = {};   // col → last rendered sig (smart diff)

// ── Utilities ──────────────────────────────────────────────────────────────
function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function elapsedBadge(mins) {
    let bg, color, label;
    if (mins >= 20)      { bg='rgba(239,68,68,.15)';  color='#ef4444'; label=mins+'m — URGENT'; }
    else if (mins >= 10) { bg='rgba(245,158,11,.15)'; color='#f59e0b'; label=mins+'m'; }
    else                 { bg='rgba(255,255,255,.06)'; color='var(--text-muted)'; label=mins+'m ago'; }
    return `<span class="k-elapsed" style="background:${bg};color:${color};">${label}</span>`;
}

function showToast(message, type='success', duration=3000) {
    let tc = document.getElementById('toastContainer');
    if (!tc) {
        tc = document.createElement('div');
        tc.id = 'toastContainer';
        tc.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:8px;';
        document.body.appendChild(tc);
    }
    const toast = document.createElement('div');
    const bg = type==='success' ? 'rgba(16,185,129,.9)' : type==='error' ? 'rgba(239,68,68,.9)' : 'rgba(59,130,246,.9)';
    toast.style.cssText = `background:${bg};color:#fff;padding:12px 16px;border-radius:8px;font-size:14px;font-weight:600;box-shadow:0 8px 32px rgba(0,0,0,.3);max-width:300px;word-wrap:break-word;animation:slideIn .3s ease;`;
    toast.textContent = message;
    tc.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut .3s ease forwards';
        setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 300);
    }, duration);
}

if (!document.getElementById('toastStyles')) {
    const s = document.createElement('style');
    s.id = 'toastStyles';
    s.textContent = '@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes slideOut{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}';
    document.head.appendChild(s);
}
</script>
@endpush

@push('scripts')
<script>
// ── Render helpers ─────────────────────────────────────────────────────────
function renderItems(items) {
    return items.map(item => {
        const modList = item.modifiers || [];
        const mods = modList
            .filter(m => m && (typeof m==='string' ? m : m.name) && !/^no\s/i.test(typeof m==='string' ? m : (m.name||'')))
            .map(m => {
                if (typeof m === 'string') return `<span class="k-mod-tag">${escapeHtml(m)}</span>`;
                const colors = { flavor:'#3b82f6', modifier:'#8b5cf6', addon:'#d97706' };
                const c = colors[m.type] || '#60a5fa';
                const adj = parseFloat(m.price_adjustment||0);
                const extra = (m.price_type==='add' && adj>0) ? ` +₱${adj}` : '';
                return `<span class="k-mod-tag" style="background:${c}18;color:${c};border-color:${c}30;">${escapeHtml(m.name)}${extra}</span>`;
            }).join('');
        return `<div class="k-item">
            <img class="k-item-img" src="${item.image||'{{ asset('images/menu/default-menu-item.webp') }}'}" alt="" onerror="this.onerror=null;this.src='{{ asset('images/menu/default-menu-item.webp') }}'">
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

// ── GROUP helpers ──────────────────────────────────────────────────────────
/**
 * Group an array of orders into "cards".
 * - Dine-in orders that share the same table_session_id (or same table_number
 *   as fallback) are merged into one card.
 * - All other orders (delivery, pickup, dine-in without a session) stay as
 *   individual cards.
 *
 * Returns an array of "card groups":
 *   { key, orders: [], isDineInGroup, tableNumber, sessionId, customer, elapsed_mins, column }
 */
function groupOrdersIntoCards(orders) {
    const groups = [];
    const dineInBuckets = {}; // sessionKey → group

    for (const o of orders) {
        if (o.order_type === 'dine_in' && o.table_number) {
            // Use table_session_id when available, else fall back to table number
            const sessionKey = o.table_session_id || ('tbl_' + o.table_number);
            if (!dineInBuckets[sessionKey]) {
                dineInBuckets[sessionKey] = {
                    key: sessionKey,
                    orders: [],
                    isDineInGroup: true,
                    tableNumber: o.table_number,
                    sessionId: o.table_session_id || o.table_number,
                    customer: o.customer,
                    elapsed_mins: o.elapsed_mins,
                    column: o.column || null,
                };
                groups.push(dineInBuckets[sessionKey]);
            }
            dineInBuckets[sessionKey].orders.push(o);
            // Use the oldest elapsed time for the group header
            if (o.elapsed_mins > dineInBuckets[sessionKey].elapsed_mins) {
                dineInBuckets[sessionKey].elapsed_mins = o.elapsed_mins;
            }
        } else {
            // Non-dine-in or no table: solo card
            groups.push({ key: 'order_' + o.id, orders: [o], isDineInGroup: false, tableNumber: null, sessionId: null, customer: o.customer, elapsed_mins: o.elapsed_mins, column: o.column || null });
        }
    }
    return groups;
}
</script>
@endpush

@push('scripts')
<script>
// ── Card renderer ──────────────────────────────────────────────────────────
function renderCardGroup(group, col) {
    const urgent = group.elapsed_mins >= 20 ? ' is-urgent' : '';
    const elapsed = elapsedBadge(group.elapsed_mins);

    if (group.isDineInGroup) {
        // ── GROUPED dine-in card ──────────────────────────────────────────
        const orderIds   = group.orders.map(o => o.id);
        const firstOrder = group.orders[0];
        const sessionKey = group.sessionId; // used for bulk ready

        // Build items section: each sub-order is separated by a divider
        let itemsSections = '';
        group.orders.forEach((o, idx) => {
            // Show sub-order divider only when there are multiple orders
            if (group.orders.length > 1) {
                itemsSections += `<div class="k-sub-divider">
                    <span style="font-family:monospace;">${escapeHtml(o.order_number)}</span>
                    <span style="opacity:.5;">·</span>
                    <span>${o.placed_at}</span>
                </div>`;
            }
            itemsSections += `<div class="k-items">${renderItems(o.items)}</div>`;
            if (o.notes) {
                itemsSections += `<div class="k-notes">📝 ${escapeHtml(o.notes)}</div>`;
            }
        });

        // Action buttons
        let actions = '';
        const tableReceiptUrl = `/chef/orders/table-bill/${encodeURIComponent(group.tableNumber)}`;
        const printBtn = `<button class="k-btn" style="flex:0 0 auto;width:2.5rem;background:rgba(255,255,255,.06);color:var(--text-muted);" onclick="event.stopPropagation();printReceipt('${tableReceiptUrl}')" title="Print Table Receipt"><i data-lucide="printer" style="width:14px;height:14px;"></i></button>`;

        if (col === 'queued') {
            // Queue: accepted — show Mark Ready (bulk)
            actions = printBtn + `<button class="k-btn k-btn-ready" onclick="event.stopPropagation();tableAction('ready','${escapeHtml(sessionKey)}',this)">✅ Mark Ready</button>`;
        } else if (col === 'cooking') {
            // Cooking: preparing — show Mark Ready (bulk)
            const removeBtn = `<button class="k-btn k-btn-remove" onclick="event.stopPropagation();openRemoveItemModal(${firstOrder.id})" title="Remove an item">✕</button>`;
            actions = removeBtn + printBtn + `<button class="k-btn k-btn-ready" onclick="event.stopPropagation();tableAction('ready','${escapeHtml(sessionKey)}',this)">✅ Mark Ready</button>`;
        }

        return `<div class="k-order-card${urgent}" data-group-key="${escapeHtml(group.key)}" data-order-ids="${orderIds.join(',')}" onclick="openGroupModal('${escapeHtml(group.key)}',${JSON.stringify(orderIds)})" style="cursor:pointer;">
            <div class="k-card-top">
                <div>
                    <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.15rem;">
                        <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .7rem;border-radius:.5rem;background:rgba(74,222,128,.18);border:1.5px solid rgba(74,222,128,.5);font-size:.92rem;font-weight:800;color:#86efac;letter-spacing:.02em;">🪑 Table ${escapeHtml(group.tableNumber)}</span>
                        ${group.orders.length > 1 ? `<span style="font-size:.68rem;padding:2px 6px;border-radius:4px;background:rgba(245,158,11,.12);color:#f59e0b;border:1px solid rgba(245,158,11,.25);font-weight:700;">${group.orders.length} orders</span>` : ''}
                    </div>
                    <div class="k-customer">${escapeHtml(group.customer)}</div>
                </div>
                ${elapsed}
            </div>
            ${itemsSections}
            <div class="k-actions">${actions}</div>
        </div>`;
    }

    // ── SOLO card (non-dine-in or single dine-in without table) ───────────
    const o = group.orders[0];
    const notes = o.notes ? `<div class="k-notes">📝 ${escapeHtml(o.notes)}</div>` : '';
    const tableNote = (o.order_type === 'dine_in' && o.table_number)
        ? `<div style="margin:0 1rem .55rem;"><span style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .7rem;border-radius:.5rem;background:rgba(74,222,128,.18);border:1.5px solid rgba(74,222,128,.5);font-size:.85rem;font-weight:800;color:#86efac;letter-spacing:.02em;">🪑 Table ${escapeHtml(o.table_number)}</span></div>`
        : '';

    const receiptUrl = `/chef/orders/${o.id}/receipt`;
    const printBtn = `<button class="k-btn" style="flex:0 0 auto;width:2.5rem;background:rgba(255,255,255,.06);color:var(--text-muted);" onclick="event.stopPropagation();printReceipt('${receiptUrl}')" title="Print Receipt"><i data-lucide="printer" style="width:14px;height:14px;"></i></button>`;
    const removeBtn = (o.order_type === 'dine_in') ? `<button class="k-btn k-btn-remove" onclick="event.stopPropagation();openRemoveItemModal(${o.id})" title="Remove an item">✕</button>` : '';

    let actions = '';
    if (col === 'new') {
        actions = IS_ADMIN
            ? `<button class="k-btn k-btn-accept" onclick="event.stopPropagation();kitchenAction('accept',${o.id},this)">✓ Accept Order</button>`
            : `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);" disabled>Waiting for acceptance…</button>`;
    } else if (col === 'queued') {
        // Accepted non-dine-in: just Mark Ready (single order)
        actions = printBtn + `<button class="k-btn k-btn-ready" onclick="event.stopPropagation();kitchenAction('ready',${o.id},this)">✅ Mark Ready</button>`;
    } else if (col === 'cooking') {
        actions = removeBtn + printBtn + `<button class="k-btn k-btn-ready" onclick="event.stopPropagation();kitchenAction('ready',${o.id},this)">✅ Mark Ready</button>`;
    } else {
        actions = printBtn;
    }

    return `<div class="k-order-card${urgent}" data-order-id="${o.id}" onclick="if(!event.target.closest('.k-actions'))openOrderModal(${o.id})" style="cursor:pointer;">
        <div class="k-card-top">
            <div>
                <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.15rem;">
                    <div class="k-order-num">${escapeHtml(o.order_number)}</div>
                    <span style="font-size:10px;padding:1px 5px;border-radius:4px;background:rgba(255,255,255,.05);color:var(--text-muted);border:1px solid rgba(255,255,255,.1);display:inline-flex;align-items:center;gap:3px;">
                        ${o.order_type_icon} ${o.order_type_label}
                    </span>
                </div>
                <div class="k-customer">${escapeHtml(o.customer)} · ${o.placed_at}</div>
            </div>
            ${elapsed}
        </div>
        <div class="k-items">${renderItems(o.items)}</div>
        ${tableNote}
        ${notes}
        <div class="k-actions">${actions}</div>
    </div>`;
}
</script>
@endpush

@push('scripts')
<script>
// ── Column renderer (with smart diff) ─────────────────────────────────────
function renderColumn(col, orders) {
    const el      = document.getElementById('col-' + col);
    const countEl = document.getElementById('count-' + col);
    if (!el) return;

    // Smart diff: skip re-render if nothing changed
    const sig = orders.map(o => o.id + ':' + (o.updated_at||o.status||'')).join('|');
    const countChanged = countEl.textContent != orders.length;
    if (!countChanged && columnSignatures[col] === sig) return;
    columnSignatures[col] = sig;

    // Store updated order data
    orders.forEach(o => { orderDataMap[o.id] = { ...o, column: col }; });

    // Guard: never regress an order to an earlier column
    const STATUS_RANK = { pending:0, accepted:1, preparing:2, rider_assigned:3, out_for_delivery:4, delivered:5, cancelled:5 };
    const safeOrders  = orders.filter(o => {
        const known = orderDataMap[o.id];
        if (!known) return true;
        return (STATUS_RANK[o.status]??0) >= (STATUS_RANK[known.status]??0);
    });

    countEl.textContent = safeOrders.length;

    if (!safeOrders.length) {
        const emptyMsg  = { new:'No new orders', queued:'Queue is empty', cooking:'Nothing cooking' };
        const emptyIcon = {
            new:     '<svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
            queued:  '<svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            cooking: '<svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>',
        };
        el.innerHTML = `<div class="k-empty"><div class="k-empty-icon">${emptyIcon[col]||''}</div>${emptyMsg[col]||''}</div>`;
        return;
    }

    const groups = groupOrdersIntoCards(safeOrders);
    el.innerHTML = groups.map(g => renderCardGroup(g, col)).join('');
}

// ── Kitchen actions ────────────────────────────────────────────────────────
async function kitchenAction(action, orderId, btn) {
    const urls = { accept: ACCEPT_URL(orderId), ready: READY_URL(orderId) };
    btn.disabled = true;
    const orig = btn.textContent;
    btn.textContent = '…';
    try {
        const res = await fetch(urls[action], {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Action failed.'); btn.disabled = false; btn.textContent = orig; return; }
        if (data.receipt_url) printReceipt(data.receipt_url);
        await refreshKitchen(true);
    } catch (e) {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = orig;
    }
}

// Bulk Mark Ready for an entire table session
async function tableAction(action, sessionKey, btn) {
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
        await refreshKitchen(true);
    } catch (e) {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = orig;
    }
}
</script>
@endpush

@push('scripts')
<script>
// ── Order detail modal (single order) ─────────────────────────────────────
function openOrderModal(orderId) {
    const order = orderDataMap[orderId];
    if (!order) return;

    document.getElementById('modalOrderNum').innerHTML =
        order.order_number +
        ` <span style="font-size:10px;padding:2px 8px;border-radius:6px;background:rgba(255,255,255,.08);color:var(--text-muted);border:1px solid rgba(255,255,255,.12);margin-left:.5rem;vertical-align:middle;font-family:sans-serif;font-weight:600;">${order.order_type_icon} ${order.order_type_label}</span>`;
    document.getElementById('modalCustomer').textContent = order.customer + ' · ' + order.placed_at;

    const statusColors = { pending:'#f59e0b', accepted:'#3b82f6', preparing:'#ef4444', rider_assigned:'#8b5cf6', out_for_delivery:'#8b5cf6', delivered:'#22c55e' };
    const statusLabels = { pending:'Pending', accepted:'Accepted', preparing:'Cooking', rider_assigned:'Rider Assigned', out_for_delivery:'Out for Delivery', delivered:'Delivered' };
    const sc = statusColors[order.status] || '#6b7280';
    document.getElementById('modalMeta').innerHTML =
        `<span><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:2px"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg> <strong>${order.placed_at}</strong></span>` +
        `<span style="color:${sc};font-weight:700;">● ${statusLabels[order.status]||order.status}</span>` +
        (order.elapsed_mins >= 0 ? `<span><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:2px"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg> <strong>${order.elapsed_mins}m ago</strong></span>` : '');

    let subtotal = 0;
    const itemsHtml = (order.items||[]).map(item => {
        const qty  = parseInt(item.qty||1);
        const price = parseFloat(item.price||0);
        const itemTotal = item.subtotal > 0 ? parseFloat(item.subtotal) : price * qty;
        subtotal += itemTotal;
        const specs = (item.modifiers||[]).filter(m => { const n = typeof m==='string'?m:(m.name||''); return n&&!/^no\s/i.test(n); });
        const specsInner = specs.length ? specs.map(m => {
            const name  = typeof m==='string'?m:m.name;
            const type  = typeof m==='object'?(m.type||'modifier'):'modifier';
            const adj   = typeof m==='object'?parseFloat(m.price_adjustment||0):0;
            const pType = typeof m==='object'?(m.price_type||'none'):'none';
            const label = {flavor:'Flavor',modifier:'Option',addon:'Add-on'}[type]||'Option';
            const priceHtml = (pType==='add'&&adj>0) ? `<span class="k-modal-spec-price">+₱${adj.toLocaleString()}</span>` : '';
            return `<div class="k-modal-spec-row"><div class="k-modal-spec-left"><span class="k-modal-spec-badge spec-${type}">${label}</span><span class="k-modal-spec-name">${escapeHtml(name)}</span></div>${priceHtml}</div>`;
        }).join('') : `<div class="k-modal-no-specs">Standard / No special customization</div>`;
        return `<div class="k-modal-item">
            <div class="k-modal-item-header">
                <img class="k-modal-item-img" src="${escapeHtml(item.image||'')}" alt="" onerror="this.src='/images/hero-burger.webp'">
                <span class="k-modal-item-qty">${qty}×</span>
                <span class="k-modal-item-name">${escapeHtml(item.name)}</span>
                <span class="k-modal-item-price">₱${itemTotal.toLocaleString()}</span>
            </div>
            <div class="k-modal-specs"><div class="k-modal-specs-label">📋 Specifications / Choices</div>${specsInner}</div>
        </div>`;
    }).join('');
    document.getElementById('modalItems').innerHTML = itemsHtml;
    document.getElementById('modalNotes').innerHTML = order.notes ? `<div class="k-modal-notes">📝 <strong>Note:</strong> ${escapeHtml(order.notes)}</div>` : '';

    const tableNoteEl = document.getElementById('modalTableNote');
    if (order.order_type==='dine_in' && order.table_number) {
        tableNoteEl.innerHTML = `<span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .6rem;border-radius:.375rem;background:rgba(74,222,128,.13);border:1px solid rgba(74,222,128,.35);font-size:.85rem;font-weight:800;color:#4ade80;">🪑 Table ${escapeHtml(order.table_number)}</span>`;
        tableNoteEl.style.display = 'block';
    } else { tableNoteEl.style.display = 'none'; }

    const delivery = parseFloat(order.delivery_fee ?? 50);
    const total    = parseFloat(order.total > 0 ? order.total : (order.subtotal + delivery));
    const displaySub = parseFloat(order.subtotal > 0 ? order.subtotal : subtotal);
    document.getElementById('modalFooter').innerHTML =
        `<div class="k-modal-total-row"><span>Subtotal</span><span>₱${displaySub.toLocaleString()}</span></div>` +
        `<div class="k-modal-total-row"><span>Delivery fee</span><span>${delivery===0?'<span style="color:#4ade80">FREE</span>':'₱'+delivery.toLocaleString()}</span></div>` +
        (order.payment_method ? `<div class="k-modal-total-row"><span>Payment</span><span style="text-transform:capitalize;">${escapeHtml(order.payment_method==='cash'?'💵 Cash on Delivery':order.payment_method==='gcash'?'📱 GCash':'💳 Card')}</span></div>` : '') +
        `<div class="k-modal-total-row grand"><span>Total</span><span>₱${total.toLocaleString()}</span></div>`;

    const col = order.column;
    const printBtn = `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);flex:0 0 auto;width:3rem;" onclick="printReceipt('/chef/orders/${order.id}/receipt')" title="Print Receipt"><i data-lucide="printer" style="width:16px;height:16px;"></i></button>`;
    const removeModalBtn = (order.order_type==='dine_in' && (col==='queued'||col==='cooking'))
        ? `<button class="k-btn k-btn-remove" style="flex:0 0 auto;padding:.6rem 1rem;width:auto;" onclick="closeOrderModal();openRemoveItemModal(${order.id})">✕ Remove Item</button>`
        : '';

    let actionBtn = '';
    if (col === 'queued') actionBtn = `<button class="k-btn k-btn-ready" style="flex:1;" onclick="modalAction('ready',${order.id})">✅ Mark Ready</button>`;
    if (col === 'cooking') actionBtn = `<button class="k-btn k-btn-ready" style="flex:1;" onclick="modalAction('ready',${order.id})">✅ Mark Ready</button>`;
    if (col === 'new') actionBtn = IS_ADMIN
        ? `<button class="k-btn k-btn-accept" style="flex:1;" onclick="modalAction('accept',${order.id})">✓ Accept Order</button>`
        : `<button class="k-btn" style="flex:1;background:rgba(255,255,255,.06);color:var(--text-muted);" disabled>Waiting for acceptance…</button>`;

    document.getElementById('modalActions').innerHTML =
        (col !== 'new' ? printBtn : '') + actionBtn + removeModalBtn +
        `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);flex:0 0 auto;padding:.6rem 1.2rem;" onclick="closeOrderModal()">Close</button>`;

    document.getElementById('orderModal').classList.add('open');
    document.body.style.overflow = 'hidden';
    if (window.lucide) lucide.createIcons();
}

// ── Group modal (dine-in table) ────────────────────────────────────────────
function openGroupModal(groupKey, orderIds) {
    // Build combined view for multiple orders at the same table
    const orders = orderIds.map(id => orderDataMap[id]).filter(Boolean);
    if (!orders.length) return;

    const first = orders[0];
    const tableNum = first.table_number;
    const sessionKey = first.table_session_id || ('tbl_' + tableNum);
    const totalAmt = orders.reduce((sum, o) => sum + parseFloat(o.total||o.subtotal||0), 0);
    const maxElapsed = Math.max(...orders.map(o => o.elapsed_mins||0));
    const col = first.column;

    document.getElementById('modalOrderNum').innerHTML =
        `🪑 Table ${escapeHtml(tableNum)} <span style="font-size:10px;padding:2px 8px;border-radius:6px;background:rgba(74,222,128,.1);color:#4ade80;border:1px solid rgba(74,222,128,.25);margin-left:.5rem;vertical-align:middle;font-family:sans-serif;font-weight:600;">${orders.length} order${orders.length>1?'s':''}</span>`;
    document.getElementById('modalCustomer').textContent = first.customer;

    document.getElementById('modalMeta').innerHTML =
        `<span style="color:#4ade80;font-weight:700;">🪑 Dine-in — Table ${escapeHtml(tableNum)}</span>` +
        `<span><strong>${maxElapsed}m ago</strong></span>` +
        `<span style="color:${maxElapsed>=20?'#ef4444':maxElapsed>=10?'#f59e0b':'var(--text-muted)'};font-weight:700;">${maxElapsed>=20?'⚠ URGENT':maxElapsed>=10?'Check soon':'On time'}</span>`;

    // Show each order as a section
    let itemsHtml = orders.map(o => {
        const header = orders.length > 1
            ? `<div style="padding:.5rem .85rem;border-bottom:1px solid var(--border-divider);font-family:monospace;font-size:.8rem;font-weight:700;color:var(--accent);">${escapeHtml(o.order_number)} <span style="font-weight:400;color:var(--text-muted);">· ${o.placed_at}</span></div>`
            : '';
        const itemRows = (o.items||[]).map(item => {
            const qty = parseInt(item.qty||1);
            const price = parseFloat(item.price||0);
            const itemTotal = item.subtotal>0 ? parseFloat(item.subtotal) : price*qty;
            const specs = (item.modifiers||[]).filter(m => { const n=typeof m==='string'?m:(m.name||''); return n&&!/^no\s/i.test(n); });
            const specsInner = specs.length ? specs.map(m => {
                const name = typeof m==='string'?m:m.name;
                const type = typeof m==='object'?(m.type||'modifier'):'modifier';
                const adj  = typeof m==='object'?parseFloat(m.price_adjustment||0):0;
                const pType= typeof m==='object'?(m.price_type||'none'):'none';
                const label= {flavor:'Flavor',modifier:'Option',addon:'Add-on'}[type]||'Option';
                const ph   = (pType==='add'&&adj>0)?`<span class="k-modal-spec-price">+₱${adj.toLocaleString()}</span>`:'';
                return `<div class="k-modal-spec-row"><div class="k-modal-spec-left"><span class="k-modal-spec-badge spec-${type}">${label}</span><span class="k-modal-spec-name">${escapeHtml(name)}</span></div>${ph}</div>`;
            }).join('') : `<div class="k-modal-no-specs">Standard</div>`;
            return `<div class="k-modal-item">
                <div class="k-modal-item-header">
                    <img class="k-modal-item-img" src="${escapeHtml(item.image||'')}" alt="" onerror="this.src='/images/hero-burger.webp'">
                    <span class="k-modal-item-qty">${qty}×</span>
                    <span class="k-modal-item-name">${escapeHtml(item.name)}</span>
                    <span class="k-modal-item-price">₱${itemTotal.toLocaleString()}</span>
                </div>
                <div class="k-modal-specs"><div class="k-modal-specs-label">📋 Choices</div>${specsInner}</div>
            </div>`;
        }).join('');
        const notesHtml = o.notes ? `<div class="k-modal-notes" style="margin:.5rem 1.25rem;">📝 ${escapeHtml(o.notes)}</div>` : '';
        return `<div style="border:1px solid var(--border-divider);border-radius:.75rem;overflow:hidden;margin-bottom:.65rem;">${header}<div style="padding:.6rem 1.25rem;display:flex;flex-direction:column;gap:.65rem;">${itemRows}</div>${notesHtml}</div>`;
    }).join('');

    document.getElementById('modalItems').innerHTML = itemsHtml;
    document.getElementById('modalNotes').innerHTML = '';
    const tne = document.getElementById('modalTableNote'); tne.style.display = 'none';

    const tableReceiptUrl = `/chef/orders/table-bill/${encodeURIComponent(tableNum)}`;
    document.getElementById('modalFooter').innerHTML =
        `<div class="k-modal-total-row grand"><span>Table Total</span><span>₱${totalAmt.toLocaleString()}</span></div>`;

    const printBtn = `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);flex:0 0 auto;width:3rem;" onclick="printReceipt('${tableReceiptUrl}')" title="Print Table Receipt"><i data-lucide="printer" style="width:16px;height:16px;"></i></button>`;
    const readyBtn = (col==='queued'||col==='cooking')
        ? `<button class="k-btn k-btn-ready" style="flex:1;" onclick="closeOrderModal();tableAction('ready','${escapeHtml(sessionKey)}',this)">✅ Mark Table Ready</button>`
        : '';

    document.getElementById('modalActions').innerHTML =
        printBtn + readyBtn +
        `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);flex:0 0 auto;padding:.6rem 1.2rem;" onclick="closeOrderModal()">Close</button>`;

    document.getElementById('orderModal').classList.add('open');
    document.body.style.overflow = 'hidden';
    if (window.lucide) lucide.createIcons();
}

function closeOrderModal(e) {
    if (e && e.target !== document.getElementById('orderModal')) return;
    document.getElementById('orderModal').classList.remove('open');
    document.body.style.overflow = '';
}

async function modalAction(action, orderId) {
    const btn = document.querySelector('#modalActions .k-btn-ready, #modalActions .k-btn-accept');
    if (btn) { btn.disabled = true; btn.textContent = '…'; }
    const urls = { accept: ACCEPT_URL(orderId), ready: READY_URL(orderId) };
    try {
        const res = await fetch(urls[action], { method:'POST', headers:{ 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json', 'Content-Type':'application/json' } });
        const data = await res.json();
        if (!data.success) { alert(data.message||'Action failed.'); if (btn) { btn.disabled=false; btn.textContent=action==='ready'?'✅ Mark Ready':'✓ Accept'; } return; }
        closeOrderModal();
        await refreshKitchen(true);
    } catch(e) { alert('Network error.'); if (btn) btn.disabled = false; }
}
</script>
@endpush

@push('scripts')
<script>
// ── Remove Item Modal ──────────────────────────────────────────────────────
let _removeOrderId = null, _removeItemId = null, _removeItemQty = 1, _removeItemMax = 1;

function openRemoveItemModal(orderId) {
    const order = orderDataMap[orderId];
    if (!order) return;
    _removeOrderId = orderId; _removeItemId = null; _removeItemQty = 1; _removeItemMax = 1;
    document.getElementById('removeModalOrderLabel').textContent =
        order.order_number + (order.table_number ? ' · Table ' + order.table_number : '');
    document.getElementById('removeItemList').innerHTML = (order.items||[]).map(item => `
        <div class="k-remove-item-row" data-item-id="${item.id}" data-item-qty="${item.qty}" onclick="selectRemoveItem(this,${item.id},${item.qty})">
            <img class="k-remove-item-img" src="${escapeHtml(item.image||'')}" alt="" onerror="this.src='{{ asset('images/menu/default-menu-item.webp') }}'">
            <span class="k-remove-item-name">${escapeHtml(item.name)}</span>
            <span class="k-remove-item-qty">${item.qty}×</span>
            <span class="k-remove-check">✓</span>
        </div>`).join('');
    document.getElementById('removeQtyRow').style.display = 'none';
    const cb = document.getElementById('removeItemConfirmBtn');
    cb.disabled = true; cb.textContent = 'Remove Selected Item';
    document.getElementById('removeItemModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function selectRemoveItem(el, itemId, itemQty) {
    document.querySelectorAll('#removeItemList .k-remove-item-row').forEach(r => r.classList.remove('selected'));
    el.classList.add('selected');
    _removeItemId = itemId; _removeItemMax = itemQty; _removeItemQty = itemQty;
    const qr = document.getElementById('removeQtyRow'); qr.style.display = 'block';
    document.getElementById('removeQtyDisplay').textContent = _removeItemQty;
    document.getElementById('removeQtyMax').textContent = _removeItemMax;
    _syncQtyButtons();
    _updateConfirmBtn(el.querySelector('.k-remove-item-name')?.textContent || 'item');
}

function adjustRemoveQty(delta) {
    _removeItemQty = Math.max(1, Math.min(_removeItemMax, _removeItemQty + delta));
    document.getElementById('removeQtyDisplay').textContent = _removeItemQty;
    _syncQtyButtons();
    const sel = document.querySelector('#removeItemList .k-remove-item-row.selected');
    _updateConfirmBtn(sel?.querySelector('.k-remove-item-name')?.textContent || 'item');
}

function _syncQtyButtons() {
    document.getElementById('removeQtyDec').disabled = _removeItemQty <= 1;
    document.getElementById('removeQtyInc').disabled = _removeItemQty >= _removeItemMax;
}

function _updateConfirmBtn(name) {
    const cb = document.getElementById('removeItemConfirmBtn');
    cb.disabled = false;
    cb.textContent = _removeItemQty >= _removeItemMax
        ? `Remove all "${name}"`
        : `Remove ${_removeItemQty}× of ${_removeItemMax}× "${name}"`;
}

function closeRemoveItemModal(e) {
    if (e && e.target !== document.getElementById('removeItemModal')) return;
    document.getElementById('removeItemModal').classList.remove('open');
    document.body.style.overflow = '';
    _removeOrderId = _removeItemId = null; _removeItemQty = _removeItemMax = 1;
}

async function confirmRemoveItem() {
    if (!_removeOrderId || !_removeItemId) return;
    const cb = document.getElementById('removeItemConfirmBtn');
    cb.disabled = true; cb.textContent = 'Removing...';
    try {
        const res = await fetch(REMOVE_ITEM_URL(_removeOrderId, _removeItemId), {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ qty: _removeItemQty }),
        });
        const data = await res.json();
        if (!data.success) {
            showToast(data.message||'Could not remove item.', 'error');
            cb.disabled = false; cb.textContent = 'Remove Selected Item';
            return;
        }
        showToast(data.items_left===0 ? 'All items removed — order cancelled.' : (data.message||('Item updated. '+data.items_left+' item(s) remaining.')), 'success');
        document.getElementById('removeItemModal').classList.remove('open');
        document.getElementById('removeQtyRow').style.display = 'none';
        document.body.style.overflow = '';
        _removeOrderId = _removeItemId = null; _removeItemQty = _removeItemMax = 1;
        columnSignatures = {};
        await refreshKitchen(true);
    } catch (err) {
        showToast('Network error. Please try again.', 'error');
        cb.disabled = false; cb.textContent = 'Remove Selected Item';
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeOrderModal(); closeRemoveItemModal(); }
});
</script>
@endpush

@push('scripts')
<script>
// ── Auto-print ─────────────────────────────────────────────────────────────
function autoPrintKitchenTicket(orderId, addonIds) {
    let url = `/chef/orders/${orderId}/kitchen-ticket`;
    if (addonIds && addonIds.length > 0) url += '?addon_ids=' + addonIds.join(',');

    const iframe = document.createElement('iframe');
    iframe.name = 'kitchen_autoprint';
    iframe.style.cssText = 'position:fixed;top:-99999px;left:-99999px;width:210px;height:600px;border:0;opacity:0;pointer-events:none;z-index:-9999';
    iframe.setAttribute('tabindex', '-1');
    iframe.setAttribute('aria-hidden', 'true');
    document.body.appendChild(iframe);

    function _remove() { if (iframe.parentNode) iframe.parentNode.removeChild(iframe); }
    function _onMsg(e) { if (e.data && e.data.type === 'kitchen_ticket_printed') { window.removeEventListener('message', _onMsg); setTimeout(_remove, 500); } }
    window.addEventListener('message', _onMsg);
    setTimeout(() => { window.removeEventListener('message', _onMsg); _remove(); }, 15000);

    iframe.onload = () => { showToast('🖨️ Kitchen ticket printing…', 'success', 1800); };
    iframe.onerror = () => {
        window.removeEventListener('message', _onMsg); _remove();
        const w = window.open(url, 'kitchen_print_'+orderId, 'width=300,height=500,left=0,top=0,toolbar=0,scrollbars=0,status=0,menubar=0,location=0');
        if (w) { showToast('🖨️ Kitchen ticket printing...', 'success', 2000); return; }
        showToast('⚠️ Popup blocked. Open the ticket manually.', 'error', 5000);
    };
    iframe.src = url;
}

function printReceipt(receiptUrl) {
    const w=220, h=800, left=Math.round((screen.width-w)/2), top=Math.round((screen.height-h)/2);
    const win = window.open(receiptUrl, 'receipt_print', `width=${w},height=${h},left=${left},top=${top},toolbar=0,scrollbars=0,status=0,menubar=0,location=0`);
    if (!win) window.open(receiptUrl, '_blank');
}

function enableAutoPrint() {
    autoPrintEnabled = true;
    const banner = document.getElementById('autoPrintBanner');
    if (banner) {
        banner.style.background = 'rgba(16,185,129,.08)';
        banner.style.borderColor = 'rgba(16,185,129,.25)';
        banner.innerHTML = `<div style="display:flex;align-items:center;gap:.6rem;"><i data-lucide="check-circle-2" style="width:1.1rem;height:1.1rem;color:#10b981;stroke-width:2;flex-shrink:0;"></i><p style="font-size:.8rem;font-weight:700;color:#10b981;margin:0;">✓ Auto-Print ENABLED — Kitchen tickets will print automatically when orders are accepted</p></div>`;
        if (window.lucide) lucide.createIcons();
        setTimeout(() => { banner.style.display = 'none'; }, 5000);
    }
    // Mark all currently visible orders as already printed so they don't re-print
    document.querySelectorAll('.k-order-card[data-order-id]').forEach(card => {
        const id = card.dataset.orderId;
        if (id) { printedOrderIds.add('accept_'+id); printedOrderIds.add('ready_'+id); printedOrderIds.add('pickup_'+id); }
    });
    Object.keys(orderDataMap).forEach(id => {
        printedOrderIds.add('accept_'+id); printedOrderIds.add('ready_'+id); printedOrderIds.add('pickup_'+id);
        const o = orderDataMap[id];
        if (o) printedOrderIds.add('accept_'+id+'_'+(o.updated_at||''));
    });
    localStorage.setItem('autoPrintEnabled', 'true');
}
</script>
@endpush

@push('scripts')
<script>
// ── Polling / WebSocket status ─────────────────────────────────────────────
function setPollingStatus(ok) {
    const statusEl = document.getElementById('wsStatusText');
    const dotEl    = document.querySelector('.live-dot');
    const wsEl     = document.getElementById('wsStatus');
    if (ok) {
        if (statusEl && !statusEl.textContent.includes('WS')) statusEl.textContent = 'Live';
        if (dotEl)  dotEl.style.background = '#22c55e';
        if (wsEl)   wsEl.title = 'Polling every 3s — connected';
    } else {
        if (statusEl) statusEl.textContent = 'Offline';
        if (dotEl)  dotEl.style.background = '#ef4444';
        if (wsEl)   wsEl.title = 'Cannot reach server';
    }
}

function updateWSStatus(connected) {
    const statusEl = document.getElementById('wsStatusText');
    const dotEl    = document.querySelector('.live-dot');
    const wsEl     = document.getElementById('wsStatus');
    if (connected) {
        if (statusEl) statusEl.textContent = 'Live (WS)';
        if (dotEl)    dotEl.style.background = '#22c55e';
        if (wsEl)     wsEl.title = 'WebSocket connected + polling active';
    }
}

function toggleKitchenFullscreen() {
    document.body.classList.toggle('kitchen-fullscreen');
}

// ── Main refresh ───────────────────────────────────────────────────────────
async function refreshKitchen(manual) {
    try {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), 8000);
        const res = await fetch(KITCHEN_URL, { headers: { 'Accept': 'application/json' }, signal: controller.signal });
        clearTimeout(timer);

        if (!res.ok) throw new Error('HTTP ' + res.status);
        const contentType = res.headers.get('content-type') || '';
        if (!contentType.includes('application/json')) {
            setPollingStatus(false);
            setTimeout(() => location.reload(), 2000);
            return;
        }

        const data = await res.json();
        setPollingStatus(true);

        renderColumn('new',     data.new     || []);
        renderColumn('queued',  data.queued  || []);
        renderColumn('cooking', data.cooking || []);

        // ── AUTO-PRINT: fires for newly-accepted (queued) orders ──────────
        // This block is UNTOUCHED from the original implementation.
        (data.queued || []).forEach(order => {
            const printKey      = 'accept_' + order.id;
            const currentItemIds = (order.items || []).map(i => i.id).filter(Boolean);
            if (!printedItemIds[order.id]) printedItemIds[order.id] = new Set();
            const alreadyPrinted = printedItemIds[order.id];
            const newItemIds     = currentItemIds.filter(id => !alreadyPrinted.has(id));

            if (!autoPrintEnabled) return;

            if (!printedOrderIds.has(printKey)) {
                // First time seeing this order → full print
                printedOrderIds.add(printKey);
                printedOrderIds.add('accept_' + order.id + '_' + (order.updated_at || ''));
                currentItemIds.forEach(id => printedItemIds[order.id].add(id));
                setTimeout(() => autoPrintKitchenTicket(order.id, null), 500);
            } else if (newItemIds.length > 0) {
                // Pahabol: order already printed but has NEW items added
                newItemIds.forEach(id => printedItemIds[order.id].add(id));
                setTimeout(() => autoPrintKitchenTicket(order.id, newItemIds), 500);
            }
        });

        // Auto-print orders marked ready (cooking column with prepared_at set)
        (data.cooking || []).forEach(order => {
            if (order.prepared_at && autoPrintEnabled && !printedOrderIds.has('ready_' + order.id)) {
                printedOrderIds.add('ready_' + order.id);
                setTimeout(() => autoPrintKitchenTicket(order.id, null), 500);
            }
        });

        if (window.lucide) lucide.createIcons();
    } catch (e) {
        setPollingStatus(false);
        if (manual) alert('Could not refresh kitchen board: ' + e.message);
    }
}
</script>
@endpush

@push('scripts')
<script>
// ── DOMContentLoaded init ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();

    // Auto-print: restore from localStorage
    if (localStorage.getItem('autoPrintEnabled') === 'true') {
        autoPrintEnabled = true;
    } else {
        const banner = document.getElementById('autoPrintBanner');
        if (banner) banner.style.display = 'flex';
    }

    // Enable auto-print on first interaction (browser security)
    const enableOnInteraction = () => {
        if (!autoPrintEnabled) enableAutoPrint();
        document.removeEventListener('click',      enableOnInteraction);
        document.removeEventListener('keydown',    enableOnInteraction);
        document.removeEventListener('touchstart', enableOnInteraction);
    };
    document.addEventListener('click',      enableOnInteraction);
    document.addEventListener('keydown',    enableOnInteraction);
    document.addEventListener('touchstart', enableOnInteraction);

    // Dismiss popup notice if already dismissed
    if (localStorage.getItem('kitchenPopupDismissed')) {
        const n = document.getElementById('popupNotice');
        if (n) n.style.display = 'none';
    }

    // ── Seed orderDataMap from server-rendered data ────────────────────────
    @php
        $allKitchenOrders = array_merge(
            $queuedOrders->all(),
            $cookingOrders->all(),
            $readyOrders->all()
        );
        $kitchenSeed = [];
        foreach ($allKitchenOrders as $o) {
            if ($o->status === 'pending')   { $col = 'new'; }
            elseif ($o->status === 'accepted') { $col = 'queued'; }
            else                            { $col = 'cooking'; }
            $kitchenSeed[] = [
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
                'subtotal'         => (float) ($o->subtotal ?? 0),
                'delivery_fee'     => (float) ($o->delivery_fee ?? 50),
                'total'            => (float) ($o->total ?? 0),
                'payment_method'   => $o->payment_method,
                'rider_name'       => $o->rider?->user?->name,
                'rider_id'         => $o->rider_id,
                'prepared_at'      => $o->prepared_at ? $o->prepared_at->toISOString() : null,
                'updated_at'       => $o->updated_at?->toISOString(),
                'column'           => $col,
                'items'            => $o->items->map(function($i) {
                    $mods = collect($i->modifiers ?? [])
                        ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
                        ->values()->all();
                    return [
                        'id'        => $i->id,
                        'name'      => $i->item_name,
                        'qty'       => $i->quantity,
                        'price'     => (float) $i->unit_price,
                        'subtotal'  => (float) $i->subtotal,
                        'image'     => $i->image ? asset($i->image) : asset('images/hero-burger.webp'),
                        'modifiers' => $mods,
                    ];
                })->all(),
            ];
        }
    @endphp
    const _kitchenSeed = @json($kitchenSeed);
    _kitchenSeed.forEach(o => {
        orderDataMap[o.id] = o;
        // Seed print-guard keys so page reload never re-prints existing orders
        if (['accepted','preparing','rider_assigned','out_for_delivery','delivered'].includes(o.status)) {
            printedOrderIds.add('accept_' + o.id);
            printedOrderIds.add('accept_' + o.id + '_' + (o.updated_at || ''));
            if (!printedItemIds[o.id]) printedItemIds[o.id] = new Set();
            o.items.forEach(i => printedItemIds[o.id].add(i.id));
        }
        if (['preparing','rider_assigned','out_for_delivery','delivered'].includes(o.status) && o.prepared_at) {
            printedOrderIds.add('ready_' + o.id);
        }
        if (['rider_assigned','out_for_delivery','delivered'].includes(o.status)) {
            printedOrderIds.add('pickup_' + o.id);
        }
    });

    // Wire click on blade-rendered cards (before first AJAX refresh)
    document.querySelectorAll('.k-order-card[data-order-id]').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('.k-actions')) return;
            openOrderModal(parseInt(this.dataset.orderId));
        });
    });

    // ── WebSocket (Echo) subscription ─────────────────────────────────────
    if (window.Echo) {
        try {
            window.Echo.private('kitchen')
                .listen('.order.updated', (order) => {
                    // AUTO-PRINT (unchanged from original)
                    if (autoPrintEnabled && order.status === 'accepted') {
                        const printKey       = 'accept_' + order.id;
                        const currentItemIds = (order.items || []).map(i => i.id).filter(Boolean);
                        if (!printedItemIds[order.id]) printedItemIds[order.id] = new Set();
                        const alreadyPrinted = printedItemIds[order.id];
                        const newItemIds     = currentItemIds.filter(id => !alreadyPrinted.has(id));
                        if (!printedOrderIds.has(printKey)) {
                            printedOrderIds.add(printKey);
                            printedOrderIds.add('accept_' + order.id + '_' + (order.updated_at || ''));
                            currentItemIds.forEach(id => printedItemIds[order.id].add(id));
                            setTimeout(() => autoPrintKitchenTicket(order.id, null), 400);
                        } else if (newItemIds.length > 0) {
                            newItemIds.forEach(id => printedItemIds[order.id].add(id));
                            setTimeout(() => autoPrintKitchenTicket(order.id, newItemIds), 400);
                        }
                    }
                    if (autoPrintEnabled && order.status === 'preparing' && order.prepared_at && !printedOrderIds.has('ready_' + order.id)) {
                        printedOrderIds.add('ready_' + order.id);
                        autoPrintKitchenTicket(order.id);
                    }
                    if (autoPrintEnabled && order.status === 'out_for_delivery' && !printedOrderIds.has('pickup_' + order.id)) {
                        printedOrderIds.add('pickup_' + order.id);
                        autoPrintKitchenTicket(order.id);
                    }
                    refreshKitchen(false);
                });

            if (window.Echo.connector?.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', () => updateWSStatus(true));
            }
        } catch (e) { console.warn('Echo subscription failed:', e); }
    }

    // ── Polling every 3s ──────────────────────────────────────────────────
    if (!fallbackTimer) {
        refreshKitchen(false);
        fallbackTimer = setInterval(() => refreshKitchen(false), 3000);
    }

    // Fallback: if status still shows Loading after 5s, force a refresh
    setTimeout(() => {
        const statusEl = document.getElementById('wsStatusText');
        if (statusEl && statusEl.textContent === 'Loading...') refreshKitchen(true);
    }, 5000);
});
</script>
@endpush
