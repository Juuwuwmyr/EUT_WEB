@extends('admin.layout')
@section('title', 'Orders')

{{-- Leaflet for rider live map in modal --}}
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>
/* ── Orders Card Grid ─────────────────────────────────────────────────────── */
.orders-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
    padding: 1.1rem 1.25rem 1.5rem;
}
.orders-grid-empty {
    grid-column: 1 / -1;
    text-align: center;
    color: var(--text-muted);
    padding: 3rem 1rem;
    font-size: .85rem;
}

/* Card shell */
.order-card {
    background: var(--bg-card, #1a1a2e);
    border: 1px solid var(--border-card, rgba(255,255,255,.08));
    border-radius: 1rem;
    padding: 1rem 1.1rem .9rem;
    display: flex;
    flex-direction: column;
    gap: .5rem;
    transition: box-shadow .15s, transform .15s;
}
.order-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.18);
    transform: translateY(-1px);
}

/* Header: type badge (left) + status badge (right) */
.order-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
}
.order-card-num {
    font-family: monospace;
    font-size: .82rem;
    font-weight: 800;
    color: #60a5fa;
    line-height: 1.3;
    word-break: break-all;
    display: inline-flex;
    align-items: center;
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

/* Meta: table/type + time */
.order-card-meta {
    display: flex;
    flex-direction: column;
    gap: .1rem;
    font-size: .72rem;
    color: var(--text-muted, #9ca3af);
}

/* Body */
.order-card-body {
    display: flex;
    flex-direction: column;
    gap: .3rem;
    flex: 1;
}

/* Items (solo cards) */
.order-card-items {
    display: flex;
    flex-direction: column;
    gap: .2rem;
    margin-bottom: .15rem;
}
.order-card-item {
    display: flex;
    align-items: center;
    gap: .4rem;
    font-size: .74rem;
}
.order-card-item-qty {
    font-size: .68rem;
    font-weight: 700;
    color: #d97706;
    background: rgba(245,158,11,.1);
    border-radius: 4px;
    padding: 1px 5px;
    flex-shrink: 0;
}
.order-card-item-name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
    color: var(--text-body, #e2e8f0);
}
.order-card-item-more {
    font-size: .66rem;
    color: var(--text-muted, #9ca3af);
}

/* Sub-rows (grouped table cards) */
.order-card-subitems {
    display: flex;
    flex-direction: column;
    gap: .25rem;
}
.order-card-subrow {
    display: flex;
    align-items: center;
    gap: .35rem;
    flex-wrap: wrap;
    padding: .25rem .4rem;
    border-radius: .4rem;
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.06);
}
.order-card-subrow-num {
    font-family: monospace;
    font-size: .67rem;
    font-weight: 700;
    color: #60a5fa;
    flex-shrink: 0;
}
.order-card-subrow-total {
    font-size: .67rem;
    color: var(--text-muted, #9ca3af);
    flex-shrink: 0;
}
.order-card-subrow-btns {
    display: flex;
    gap: .25rem;
    flex-wrap: wrap;
    margin-left: auto;
    align-items: center;
}

/* Total */
.order-card-total {
    font-size: 1.3rem;
    font-weight: 800;
    color: var(--text-strong, #f1f5f9);
    letter-spacing: -.01em;
    margin-top: .1rem;
}

/* Action buttons row */
.order-card-actions {
    display: flex;
    gap: .4rem;
    flex-wrap: wrap;
    align-items: center;
    margin-top: .35rem;
}
.order-card-actions > button,
.order-card-actions > span {
    flex-shrink: 0;
    justify-content: center;
    white-space: nowrap;
    text-align: center;
    line-height: 1.3;
}

/* Card-level buttons (Complete Table, Done Ordering) */
.oc-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .3rem;
    padding: .45rem .7rem;
    border-radius: .5rem;
    font-size: .74rem;
    font-weight: 600;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: opacity .15s, filter .15s;
    white-space: nowrap;
}
.oc-btn:disabled { opacity: .5; cursor: default; }
.oc-btn:not(:disabled):hover { filter: brightness(1.1); }

.oc-btn-complete {
    background: #16a34a;
    border-color: #16a34a;
    color: #fff;
    flex: 1;
}
.oc-btn-lock {
    background: transparent;
    border-color: rgba(251,146,60,.4);
    color: #fb923c;
    flex-shrink: 0;
}
.oc-btn-locked {
    background: rgba(251,146,60,.1);
    border-color: rgba(251,146,60,.25);
    color: #fb923c;
    font-size: .7rem;
    font-weight: 700;
    cursor: default;
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    padding: .35rem .6rem;
    border-radius: .45rem;
    border: 1px solid rgba(251,146,60,.25);
}
.oc-locked-sm { font-size: .75rem; }
.oc-locked-badge {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .3rem .6rem;
    border-radius: .4rem;
    background: rgba(251,146,60,.08);
    color: #fb923c;
    font-size: .7rem;
    font-weight: 700;
    margin-top: .35rem;
}

/* Light mode */
html.light .order-card {
    background: #fff;
    border-color: rgba(0,0,0,.09);
}
html.light .order-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.08);
}
html.light .order-card-num { color: #2563eb; }
html.light .order-card-total { color: #111827; }
html.light .order-card-subrow {
    background: rgba(0,0,0,.025);
    border-color: rgba(0,0,0,.07);
}

@media (max-width: 600px) {
    .orders-grid {
        grid-template-columns: 1fr;
        padding: .75rem;
    }
}
</style>

@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endpush

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="page-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="shopping-bag" style="width:1.2rem;height:1.2rem;color:#10b981;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;">Orders</h1>
            <p style="margin:0;">Track and manage customer orders in real time.</p>
        </div>
    </div>
    <button onclick="location.reload()" class="btn-ghost" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.75rem;">
        <i data-lucide="refresh-cw" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Refresh
    </button>
</div>

{{-- -- ERROR LOG PANEL -- --}}
<div id="errorLogPanel" style="display:none;margin-bottom:1.25rem;border:1px solid rgba(239,68,68,.35);border-radius:.75rem;background:rgba(239,68,68,.06);overflow:hidden;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.6rem 1rem;border-bottom:1px solid rgba(239,68,68,.2);background:rgba(239,68,68,.08);">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i data-lucide="alert-triangle" style="width:.9rem;height:.9rem;color:#ef4444;stroke-width:2;"></i>
            <span style="font-size:.78rem;font-weight:700;color:#ef4444;">Error Log</span>
            <span id="errorCount" style="font-size:.65rem;background:rgba(239,68,68,.2);color:#ef4444;border-radius:99px;padding:1px 7px;font-weight:700;">0</span>
        </div>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <button onclick="copyErrorLog()" style="font-size:.7rem;padding:.25rem .6rem;border-radius:.375rem;border:1px solid rgba(239,68,68,.3);background:transparent;color:#ef4444;cursor:pointer;display:inline-flex;align-items:center;gap:.3rem;">
                <i data-lucide="copy" style="width:.7rem;height:.7rem;stroke-width:2;"></i> Copy All
            </button>
            <button onclick="clearErrorLog()" style="font-size:.7rem;padding:.25rem .6rem;border-radius:.375rem;border:1px solid rgba(239,68,68,.3);background:transparent;color:#ef4444;cursor:pointer;display:inline-flex;align-items:center;gap:.3rem;">
                <i data-lucide="trash-2" style="width:.7rem;height:.7rem;stroke-width:2;"></i> Clear
            </button>
        </div>
    </div>
    <div id="errorLogBody" style="max-height:220px;overflow-y:auto;padding:.5rem .75rem;font-family:monospace;font-size:.72rem;line-height:1.6;"></div>
</div>

{{-- -- TABLE CARD -- --}}
<div class="section-card">
    <div class="filter-bar" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i data-lucide="filter" style="width:.875rem;height:.875rem;color:var(--text-muted);stroke-width:2;"></i>
            <select id="statusFilter" class="admin-input" style="max-width:180px;"
                    onchange="applyStatusFilter(this.value)">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="preparing">Preparing</option>
                <option value="rider_assigned">Rider Assigned</option>
                <option value="out_for_delivery">Out for Delivery</option>
                <option value="delivered">Served / Done / Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        {{-- Date range toggle --}}
        <div style="display:flex;align-items:center;gap:.35rem;">
            <button id="btnToday" onclick="applyDateFilter('today')" class="btn-primary" style="font-size:.72rem;padding:.3rem .7rem;">Today</button>
            <button id="btnAll"   onclick="applyDateFilter('all')"   class="btn-ghost"  style="font-size:.72rem;padding:.3rem .7rem;">All Orders</button>
            <button id="btnArchived" onclick="applyDateFilter('archived')" class="btn-ghost" style="font-size:.72rem;padding:.3rem .7rem;">
                <i data-lucide="archive" style="width:.7rem;height:.7rem;stroke-width:2;vertical-align:middle;"></i> Archived
            </button>
        </div>
        <span id="clearFilter" style="display:none;">
            <a href="#" class="btn-ghost" onclick="applyStatusFilter('');return false;"
               style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;">
                <i data-lucide="x" style="width:.75rem;height:.75rem;stroke-width:2.5;"></i> Clear
            </a>
        </span>
        <span id="orderCount" style="margin-left:auto;font-size:.72rem;color:var(--text-muted);"></span>
        <span style="font-size:.68rem;color:var(--text-muted);display:flex;align-items:center;gap:.3rem;">
            <span id="pollDot" style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;transition:background .3s;"></span>
            <span id="pollLabel">Live</span>
        </span>
    </div>

    <div id="ordersGrid" class="orders-grid"><div class="orders-grid-empty">Loading&#8230;</div></div>

    {{-- Archived pagination --}}
    <div id="archivedPagination"
         style="display:none;align-items:center;gap:.35rem;flex-wrap:wrap;padding:.875rem 1.25rem;border-top:1px solid var(--border-divider);">
    </div>
</div>

{{-- ══════════ MANAGE ORDER MODAL ══════════ --}}
<div id="manageModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'manageModal')">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="settings-2" style="width:1.1rem;height:1.1rem;color:#10b981;stroke-width:2;"></i>
                <h3 class="modal-title" id="mmTitle">Manage Order</h3>
            </div>
            <button onclick="closeModal('manageModal')" class="modal-close">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i>
            </button>
        </div>
        <div id="mmBody" class="modal-body" style="gap:.875rem;">
            <div style="text-align:center;padding:2rem;color:var(--text-muted);">Loading…</div>
        </div>
    </div>
</div>

{{-- ══════════ CANCEL ORDER CONFIRM MODAL ══════════ --}}
<div id="cancelConfirmModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'cancelConfirmModal')">
    <div class="modal-box" style="max-width:380px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="alert-triangle" style="width:1.1rem;height:1.1rem;color:#ef4444;stroke-width:2;"></i>
                <h3 class="modal-title">Cancel Order</h3>
            </div>
            <button onclick="closeModal('cancelConfirmModal')" class="modal-close">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i>
            </button>
        </div>
        <div class="modal-body" style="gap:.75rem;padding:1.25rem 1.4rem;">
            <div style="display:flex;align-items:flex-start;gap:.875rem;">
                <div style="flex-shrink:0;width:2.5rem;height:2.5rem;border-radius:.625rem;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.25);display:flex;align-items:center;justify-content:center;">
                    <i data-lucide="x-circle" style="width:1.1rem;height:1.1rem;color:#ef4444;stroke-width:2;"></i>
                </div>
                <div>
                    <p style="margin:0 0 .3rem;font-size:.875rem;font-weight:700;color:var(--text-strong);">Are you sure?</p>
                    <p style="margin:0;font-size:.8rem;color:var(--text-muted);line-height:1.5;">This will cancel <strong id="cancelOrderLabel" style="color:var(--text-body);">this order</strong>. This action cannot be undone.</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="closeModal('cancelConfirmModal')" class="btn-ghost" style="font-size:.8rem;">
                Keep Order
            </button>
            <button id="cancelConfirmBtn" class="btn-danger" style="font-size:.8rem;display:inline-flex;align-items:center;gap:.35rem;">
                <i data-lucide="x-circle" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Yes, Cancel Order
            </button>
        </div>
    </div>
</div>

{{-- ══════════ ORDER PREVIEW MODAL (View before Accept) ══════════ --}}
<div id="previewModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'previewModal')">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="eye" style="width:1.1rem;height:1.1rem;color:#f59e0b;stroke-width:2;"></i>
                <h3 class="modal-title" id="pvTitle">Order Preview</h3>
            </div>
            <button onclick="closeModal('previewModal')" class="modal-close">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i>
            </button>
        </div>
        <div id="pvBody" class="modal-body" style="gap:.75rem;"></div>
        <div style="padding:.75rem 1.25rem 1.25rem;display:flex;gap:.5rem;">
            <button onclick="closeModal('previewModal')" class="btn-ghost" style="flex:1;justify-content:center;font-size:.8rem;">
                Close
            </button>
            <button id="pvAcceptBtn" class="btn-accept" style="flex:2;justify-content:center;font-size:.875rem;display:inline-flex;align-items:center;gap:.35rem;">
                <i data-lucide="check" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i> Accept Order
            </button>
        </div>
    </div>
</div>
<script>
var CSRF_TOKEN   = '{{ csrf_token() }}';
var POLL_URL     = '{{ route("admin.orders.poll") }}';
var ORDERS_MAP   = {};
var RIDERS       = [];
var activeFilter = '{{ request("status","") }}';
var dateFilter   = 'today'; // 'today' | 'all' | 'archived'
var pollTimer    = null;
var POLL_INTERVAL = 5000; // 5 seconds
var printedPickupIds = new Set(); // track which orders already auto-printed on pickup

// ── Archived pagination state ─────────────────────────────────────────────
var archivedPage      = 1;
var archivedTotalPages = 1;
var archivedTotal     = 0;

// -- Error Log --------------------------------------------
var errorLogEntries = [];

function logError(context, message, detail) {
    var ts = new Date().toLocaleTimeString();
    var entry = { ts: ts, context: context, message: message, detail: detail || '' };
    errorLogEntries.push(entry);

    var panel = document.getElementById('errorLogPanel');
    var body  = document.getElementById('errorLogBody');
    var count = document.getElementById('errorCount');

    if (panel) panel.style.display = 'block';
    if (count) count.textContent = errorLogEntries.length;

    if (body) {
        var color = '#ef4444';
        var row = document.createElement('div');
        row.style.cssText = 'padding:3px 0;border-bottom:1px solid rgba(239,68,68,.1);display:flex;gap:.5rem;align-items:flex-start;';
        row.innerHTML =
            '<span style="color:#6b7280;flex-shrink:0;">' + ts + '</span>' +
            '<span style="color:#f87171;font-weight:700;flex-shrink:0;">[' + escHtml(context) + ']</span>' +
            '<span style="color:#fca5a5;word-break:break-all;">' + escHtml(message) +
                (detail ? '<br><span style="color:#9ca3af;font-size:.68rem;">' + escHtml(String(detail)) + '</span>' : '') +
            '</span>';
        body.appendChild(row);
        body.scrollTop = body.scrollHeight;
        if (window.lucide) lucide.createIcons();
    }
}

function copyErrorLog() {
    if (!errorLogEntries.length) return;
    var text = errorLogEntries.map(function(e) {
        return '[' + e.ts + '] [' + e.context + '] ' + e.message + (e.detail ? '\n  ' + e.detail : '');
    }).join('\n');
    navigator.clipboard.writeText(text).then(function() {
        var btn = document.querySelector('#errorLogPanel button');
        if (btn) { var orig = btn.innerHTML; btn.innerHTML = '? Copied!'; setTimeout(function(){ btn.innerHTML = orig; }, 1500); }
    });
}

function clearErrorLog() {
    errorLogEntries = [];
    var body  = document.getElementById('errorLogBody');
    var panel = document.getElementById('errorLogPanel');
    var count = document.getElementById('errorCount');
    if (body)  body.innerHTML = '';
    if (panel) panel.style.display = 'none';
    if (count) count.textContent = '0';
}

// -- Status config (client-side) -------------------------
var STATUS_COLOR_MAP = {
    pending:          { bg:'rgba(245,158,11,.12)',  color:'#d97706',  label:'Pending'        },
    accepted:         { bg:'rgba(59,130,246,.12)',  color:'#2563eb',  label:'Accepted'       },
    preparing:        { bg:'rgba(59,130,246,.12)',  color:'#2563eb',  label:'Preparing'      },
    rider_assigned:   { bg:'rgba(139,92,246,.12)',  color:'#7c3aed',  label:'Rider Assigned' },
    out_for_delivery: { bg:'rgba(139,92,246,.12)',  color:'#7c3aed',  label:'On the Way'     },
    delivered:        { bg:'rgba(16,185,129,.12)',  color:'#16a34a',  label:'Delivered'      },
    cancelled:        { bg:'rgba(239,68,68,.12)',   color:'#dc2626',  label:'Cancelled'      },
};

// Returns a status colour+label object, making 'delivered' label order-type aware
function statusChip(status, orderType) {
    var chip = Object.assign({}, STATUS_COLOR_MAP[status] || STATUS_COLOR_MAP['pending']);
    if (status === 'delivered') {
        chip.label = orderType === 'dine_in' ? 'Served' : (orderType === 'pickup' ? 'Done' : 'Delivered');
    }
    return chip;
}

// -- Status pipeline for modal
// Admin: Accept (? auto preparing) | Dispatch rider (after chef marks ready) | Cancel
// Chef: Mark Ready (on Kitchen Dashboard)
// Rider: Picked Up | Delivered
var STATUS_PIPELINE = {
    pending:          { label:'Pending',        color:'#f59e0b', next:'accepted',  nextLabel:'Accept Order',    btnClass:'btn-accept' },
    accepted:         { label:'Accepted',       color:'#3b82f6', next:null,        nextLabel:null,              btnClass:'', chefAction:true },
    preparing:        { label:'Preparing',      color:'#dc2626', next:null,        nextLabel:null,              btnClass:'', chefAction:true },
    rider_assigned:   { label:'Rider Assigned', color:'#8b5cf6', next:null,        nextLabel:null,              btnClass:'', riderAction:true },
    out_for_delivery: { label:'On the Way',     color:'#8b5cf6', next:null,        nextLabel:null,              btnClass:'' },
    delivered:        { label:'Served/Done',   color:'#10b981', next:null,        nextLabel:null,              btnClass:'' },
    cancelled:        { label:'Cancelled',      color:'#ef4444', next:null,        nextLabel:null,              btnClass:'' },
};

var STATUS_TIMELINE = [
    { key:'pending',          label:'Order Placed',     icon:'<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>' },
    { key:'accepted',         label:'Order Accepted',   icon:'<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'  },
    { key:'preparing',        label:'Being Prepared',   icon:'<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>' },
    { key:'out_for_delivery', label:'Out for Delivery', icon:'<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3"/></svg>' },
    { key:'delivered',        label:'Delivered',        icon:'<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>' },
];

// -- Auto-poll --------------------------------------------
function startPolling() {
    if (pollTimer) clearInterval(pollTimer); // prevent duplicate intervals
    fetchOrders();
    pollTimer = setInterval(fetchOrders, POLL_INTERVAL);
}

async function fetchOrders() {
    var dot   = document.getElementById('pollDot');
    var label = document.getElementById('pollLabel');
    if (dot) dot.style.background = '#f59e0b'; // yellow = fetching

    var controller = new AbortController();
    var timeoutId  = setTimeout(function() { controller.abort(); }, 15000); // 15s timeout

    try {
        var params = [];
        if (activeFilter) params.push('status=' + activeFilter);
        if (dateFilter === 'all')      params.push('all=1');
        if (dateFilter === 'archived') {
            params.push('archived=1');
            params.push('page=' + archivedPage);
        }
        var url = POLL_URL + (params.length ? '?' + params.join('&') : '');
        var res = await fetch(url, {
            signal: controller.signal,
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
        });
        clearTimeout(timeoutId);

        // Session expired or auth redirect — reload to login
        if (res.status === 419 || res.status === 401) {
            logError('Poll', 'Session expired', 'Reloading page…');
            setTimeout(function() { window.location.reload(); }, 1500);
            return;
        }

        if (res.redirected && !res.url.includes('/orders/poll')) {
            logError('Poll', 'Session expired (redirect)', 'Reloading page…');
            setTimeout(function() { window.location.reload(); }, 1500);
            return;
        }

        if (!res.ok) throw new Error('HTTP ' + res.status);
        var data = await res.json();

        ORDERS_MAP = {};
        data.orders.forEach(function(o) { ORDERS_MAP[o.id] = o; });
        RIDERS = data.riders || [];

        renderGrid(data.orders);

        // Handle archived pagination
        if (dateFilter === 'archived' && data.pagination) {
            archivedPage       = data.pagination.page;
            archivedTotalPages = data.pagination.totalPages;
            archivedTotal      = data.pagination.total;
            renderArchivedPagination();
        } else {
            var pag = document.getElementById('archivedPagination');
            if (pag) pag.style.display = 'none';
        }

        if (dot)   dot.style.background   = '#10b981'; // green = ok
        if (label) label.textContent = 'Live';
    } catch (e) {
        clearTimeout(timeoutId);
        var reason = e.name === 'AbortError'
            ? 'Request timed out (15s)'
            : (e.message || 'Network error');
        console.warn('Poll error:', e);
        logError('Poll', 'Failed to fetch orders', reason);
        if (dot)   dot.style.background   = '#ef4444'; // red = error
        if (label) label.textContent = 'Offline';
    }
}

function applyStatusFilter(val) {
    activeFilter = val;
    var sel = document.getElementById('statusFilter');
    if (sel) sel.value = val;
    var clr = document.getElementById('clearFilter');
    if (clr) clr.style.display = val ? 'inline' : 'none';
    fetchOrders();
}

function applyDateFilter(mode) {
    dateFilter = mode;
    archivedPage = 1; // reset to page 1 when switching filters
    var btnMap = { today:'btnToday', all:'btnAll', archived:'btnArchived' };
    Object.keys(btnMap).forEach(function(m) {
        var btn = document.getElementById(btnMap[m]);
        if (btn) btn.className = m === mode ? 'btn-primary' : 'btn-ghost';
    });
    fetchOrders();
}

// ── Archived pagination ───────────────────────────────────────────────────
function renderArchivedPagination() {
    var pag = document.getElementById('archivedPagination');
    if (!pag) return;

    if (archivedTotalPages <= 1) {
        pag.style.display = 'none';
        return;
    }

    pag.style.display = 'flex';

    var html = '';
    var from = ((archivedPage - 1) * 20) + 1;
    var to   = Math.min(archivedPage * 20, archivedTotal);
    html += '<span style="font-size:.75rem;color:var(--text-muted);margin-right:.5rem;">'
          + from + '–' + to + ' of ' + archivedTotal + '</span>';

    html += '<button onclick="goArchivedPage(' + (archivedPage - 1) + ')" '
          + (archivedPage <= 1 ? 'disabled' : '') + ' '
          + 'class="btn-ghost" style="padding:.3rem .6rem;font-size:.75rem;">'
          + '<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>'
          + '</button>';

    var startP = Math.max(1, archivedPage - 2);
    var endP   = Math.min(archivedTotalPages, archivedPage + 2);
    if (startP > 1) html += '<button onclick="goArchivedPage(1)" class="btn-ghost" style="padding:.3rem .55rem;font-size:.75rem;">1</button>';
    if (startP > 2) html += '<span style="color:var(--text-muted);padding:0 .2rem;">…</span>';
    for (var p = startP; p <= endP; p++) {
        html += '<button onclick="goArchivedPage(' + p + ')" class="' + (p === archivedPage ? 'btn-primary' : 'btn-ghost') + '" '
              + 'style="padding:.3rem .55rem;font-size:.75rem;min-width:2rem;">' + p + '</button>';
    }
    if (endP < archivedTotalPages - 1) html += '<span style="color:var(--text-muted);padding:0 .2rem;">…</span>';
    if (endP < archivedTotalPages)     html += '<button onclick="goArchivedPage(' + archivedTotalPages + ')" class="btn-ghost" style="padding:.3rem .55rem;font-size:.75rem;">' + archivedTotalPages + '</button>';

    html += '<button onclick="goArchivedPage(' + (archivedPage + 1) + ')" '
          + (archivedPage >= archivedTotalPages ? 'disabled' : '') + ' '
          + 'class="btn-ghost" style="padding:.3rem .6rem;font-size:.75rem;">'
          + '<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>'
          + '</button>';

    pag.innerHTML = html;
}

function goArchivedPage(page) {
    if (page < 1 || page > archivedTotalPages) return;
    archivedPage = page;
    fetchOrders();
    var tbody = document.getElementById('ordersTableBody');
    if (tbody) tbody.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// -- Render table rows ------------------------------------
// Flow: Admin ACCEPTS (auto?preparing) ? Chef MARKS READY ? Admin DISPATCHES rider ? Rider PICKS UP ? Rider DELIVERS
var INLINE_ACTIONS = {
    pending: { label:'Accept', icon:'check', btnClass:'btn-accept', type:'accept' },
    // preparing: handled dynamically — 'Dispatch'/'Serve'/'Picked Up' shown only when chef has marked ready (prepared_at set on order data)
};

function renderTable(orders) {
    var tbody = document.getElementById('ordersTableBody');
    if (!tbody) return;

    var countEl = document.getElementById('orderCount');

    if (!orders.length) {
        if (countEl) countEl.textContent = '0 order(s)';
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:3rem;">No orders found.</td></tr>';
        return;
    }

    // ── Group dine-in orders by SESSION — both active AND completed (served) ────
    //    Group key = table_session_id (unique per customer sitting).
    //    Falling back to table_number only when session ID is missing (legacy rows).
    //    This ensures a NEW customer at the same table NEVER gets mixed into a
    //    previous customer's row — even if the old orders are still visible today.
    //    Non-dine-in, no-table, cancelled, and archived orders are always solo rows.
    var ACTIVE_STATUSES = ['pending','accepted','preparing','rider_assigned','out_for_delivery'];
    var GROUPABLE_STATUSES = ACTIVE_STATUSES.concat(['delivered']);
    var tableGroups = {}; // session_key -> [orders]
    var soloOrders  = [];

    orders.forEach(function(o) {
        var canGroup = o.order_type === 'dine_in'
            && o.table_number
            && GROUPABLE_STATUSES.indexOf(o.status) !== -1
            && !o.is_archived
            && !activeFilter; // don't collapse when a specific status filter is active
        if (canGroup) {
            // Use session_id as the unique group key — different sittings at the same
            // table get their own row; fall back to table_number for orders without a session.
            var groupKey = o.table_session_id ? ('session_' + o.table_session_id) : ('table_' + o.table_number);
            if (!tableGroups[groupKey]) tableGroups[groupKey] = [];
            tableGroups[groupKey].push(o);
        } else {
            soloOrders.push(o);
        }
    });

    // Flatten: groups with only one order go into soloOrders
    Object.keys(tableGroups).forEach(function(k) {
        if (tableGroups[k].length === 1) {
            soloOrders.push(tableGroups[k][0]);
            delete tableGroups[k];
        }
    });


    var rowCount = soloOrders.length + Object.keys(tableGroups).length;
    if (countEl) countEl.textContent = rowCount + ' row(s) · ' + orders.length + ' order(s)';

    var html = '';

    // ── Render grouped table rows first (most urgent on top) ─────────────────
    Object.keys(tableGroups).forEach(function(sessionKey) {
        var group   = tableGroups[sessionKey].sort(function(a,b){ return a.id - b.id; }); // oldest first
        var rep     = group[0]; // representative order (oldest / first placed)
        var tableNum = rep.table_number || sessionKey; // real table number for display
        var initial = (rep.customer || 'G').charAt(0).toUpperCase();

        // Determine the most urgent status in the group for display
        var statusPriority = ['pending','accepted','preparing','rider_assigned','out_for_delivery','delivered','cancelled'];
        var topStatus = group.reduce(function(best, o) {
            return statusPriority.indexOf(o.status) < statusPriority.indexOf(best) ? o.status : best;
        }, group[0].status);

        // Override: if ALL orders are ready (prepared_at set), show "Ready to Serve" instead of "Preparing"
        var allGroupReady = group.every(function(o) {
            return o.status === 'preparing' && o.prepared_at && o.order_type !== 'delivery';
        });
        var sc = allGroupReady
            ? { bg:'rgba(16,185,129,.12)', color:'#10b981', label:'Ready to Serve' }
            : statusChip(topStatus, group[0].order_type);

        // All items across all orders in the group
        var allItems = [];
        group.forEach(function(o) { allItems = allItems.concat(o.items || []); });
        var itemsHtml = '';
        allItems.slice(0, 3).forEach(function(item) {
            itemsHtml +=
                '<div style="display:flex;align-items:center;gap:4px;margin-bottom:2px;">' +
                '<span style="font-size:.72rem;font-weight:700;color:var(--accent);background:rgba(250,204,21,.1);border-radius:4px;padding:1px 5px;flex-shrink:0;">x' + item.qty + '</span>' +
                '<span style="font-size:.75rem;color:var(--text-strong);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;" title="' + escHtml(item.name) + '">' + escHtml(item.name) + '</span>' +
                '</div>';
        });
        if (allItems.length > 3) itemsHtml += '<span style="font-size:.68rem;color:var(--text-muted);">+' + (allItems.length - 3) + ' more</span>';

        var grandTotal = group.reduce(function(s,o){ return s + parseFloat(o.total||0); }, 0);

        // Sub-order action pills — one per order in the group
        var subHtml = '<div style="display:flex;flex-direction:column;gap:.3rem;margin-top:.4rem;">';

        // Check if ALL orders in the group are "ready" (prepared_at set, status preparing)
        // If so, replace individual Complete buttons with one combined "Complete Table" button
        var allReady = group.every(function(o) {
            return o.status === 'preparing' && o.prepared_at && o.order_type !== 'delivery';
        });

        // Check if session is locked (any order in the group has ordering_locked = true)
        var isSessionLocked = group.some(function(o) { return o.ordering_locked; });

        if (allReady && group.length > 1) {
            // Show status summary pills (no individual Complete buttons)
            group.forEach(function(o) {
                var oSc = statusChip(o.status, o.order_type);
                subHtml +=
                    '<div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;padding:.3rem .5rem;border-radius:.5rem;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);">' +
                    '<span style="font-family:monospace;font-size:.7rem;font-weight:700;color:var(--accent);flex-shrink:0;">' + escHtml(o.order_number) + '</span>' +
                    '<span style="display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .5rem;border-radius:9999px;font-size:.62rem;font-weight:700;background:rgba(16,185,129,.12);color:#10b981;">✓ Ready</span>' +
                    '<span style="font-size:.68rem;color:var(--text-muted);flex-shrink:0;">₱' + Number(o.total).toLocaleString() + '</span>' +
                    '<button class="btn-ghost" style="font-size:.65rem;padding:.2rem .45rem;margin-left:auto;" onclick="openManageModal(' + o.id + ')" title="Details">' +
                    '<i data-lucide="settings-2" style="width:.65rem;height:.65rem;stroke-width:2;"></i></button>' +
                    '</div>';
            });
            // Single Complete Table button covering all orders
            subHtml +=
                '<div style="margin-top:.2rem;display:flex;gap:.35rem;">' +
                '<button type="button" class="btn-success" data-grand-total="' + grandTotal + '" style="flex:1;justify-content:center;font-size:.78rem;display:inline-flex;align-items:center;gap:.35rem;padding:.5rem;" ' +
                'onclick="quickAction(' + rep.id + ',\'complete-table\',\'\',this)">' +
                '<i data-lucide="circle-check" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>' +
                'Complete Table \u00B7 \u20B1' + Number(grandTotal).toLocaleString() +
                '</button>' +
                // Lock button (only if not already locked)
                (isSessionLocked
                    ? '<span style="display:inline-flex;align-items:center;gap:.3rem;padding:.4rem .65rem;border-radius:.45rem;background:rgba(251,146,60,.12);color:#fb923c;font-size:.72rem;font-weight:700;">\uD83D\uDD12 Locked</span>'
                    : '<button type="button" class="btn-ghost" title="Done Ordering — lock this session" style="flex-shrink:0;padding:.4rem .6rem;font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;border:1px solid rgba(251,146,60,.35);color:#fb923c;border-radius:.45rem;" onclick="quickAction(' + rep.id + ',\'lock-table\',\'\',this)"><i data-lucide="lock" style="width:.75rem;height:.75rem;stroke-width:2;"></i>Done Ordering</button>') +
                '</div>';
        } else {
            // Normal: individual action buttons per sub-order
            group.forEach(function(o) {
                var oSc  = statusChip(o.status, o.order_type);
                var oBtns = buildActionBtns(o);
                subHtml +=
                    '<div style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;padding:.3rem .5rem;border-radius:.5rem;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);">' +
                    '<span style="font-family:monospace;font-size:.7rem;font-weight:700;color:var(--accent);flex-shrink:0;">' + escHtml(o.order_number) + '</span>' +
                    '<span style="display:inline-flex;align-items:center;gap:.25rem;padding:.15rem .5rem;border-radius:9999px;font-size:.62rem;font-weight:700;background:' + oSc.bg + ';color:' + oSc.color + ';">' + oSc.label + '</span>' +
                    '<span style="font-size:.68rem;color:var(--text-muted);flex-shrink:0;">\u20B1' + Number(o.total).toLocaleString() + '</span>' +
                    '<div style="display:flex;gap:.3rem;flex-wrap:wrap;margin-left:auto;">' + oBtns + '</div>' +
                    '</div>';
            });
            // Lock button at bottom of normal group (only when there are active non-locked orders)
            var hasActiveOrders = group.some(function(o) {
                return ['pending','accepted','preparing'].indexOf(o.status) !== -1;
            });
            if (hasActiveOrders) {
                subHtml += isSessionLocked
                    ? '<div style="margin-top:.3rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .65rem;border-radius:.45rem;background:rgba(251,146,60,.1);color:#fb923c;font-size:.72rem;font-weight:700;">\uD83D\uDD12 Done Ordering — Locked</div>'
                    : '<div style="margin-top:.3rem;"><button type="button" class="btn-ghost" title="Mark as Done Ordering — prevents further pahabol merges" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .6rem;border:1px solid rgba(251,146,60,.35);color:#fb923c;border-radius:.45rem;" onclick="quickAction(' + rep.id + ',\'lock-table\',\'\',this)"><i data-lucide="lock" style="width:.75rem;height:.75rem;stroke-width:2;"></i>Done Ordering</button></div>';
            }
        }

        subHtml += '</div>';

        html +=
            '<tr id="order-row-group-' + sessionKey.replace(/[^a-z0-9]/gi,'_') + '">' +
            '<td>' +
                '<div style="display:flex;flex-direction:column;gap:.15rem;">' +
                group.map(function(o){ return '<span style="font-family:monospace;font-size:.72rem;color:var(--accent);">' + escHtml(o.order_number) + '</span>'; }).join('') +
                '</div>' +
            '</td>' +
            '<td>' +
                '<div style="display:flex;align-items:center;gap:.5rem;">' +
                '<div style="width:1.875rem;height:1.875rem;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#000;font-weight:700;font-size:.7rem;flex-shrink:0;">' + initial + '</div>' +
                '<div>' +
                    '<div style="display:flex;align-items:center;gap:.3rem;margin-bottom:.1rem;">' +
                    '<p style="font-weight:600;color:var(--text-strong);font-size:.8rem;margin:0;">' + escHtml(rep.customer) + '</p>' +
                    '<span style="font-size:10px;padding:1px 5px;border-radius:4px;background:rgba(250,204,21,.1);color:#facc15;font-weight:700;">\uD83E\uDE91 Table ' + escHtml(tableNum) + '</span>' +
                    (isSessionLocked ? '<span style="font-size:10px;padding:1px 5px;border-radius:4px;background:rgba(251,146,60,.1);color:#fb923c;font-weight:700;">\uD83D\uDD12 Locked</span>' : '') +
                    '</div>' +
                    '<p style="font-size:.68rem;color:var(--text-muted);margin:0;">' + group.length + (group.every(function(o){ return o.status === 'delivered'; }) ? ' order(s) · Served' : ' order(s) active') + '</p>' +
                '</div>' +
                '</div>' +
            '</td>' +
            '<td style="max-width:200px;">' + itemsHtml + '</td>' +
            '<td style="font-weight:700;color:var(--accent);">&#x20B1;' + Number(grandTotal).toLocaleString() + '</td>' +
            '<td><span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:9999px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;background:' + sc.bg + ';color:' + sc.color + ';">' + sc.label + '</span></td>' +
            '<td style="color:var(--text-muted);font-size:.72rem;white-space:nowrap;">' + escHtml(rep.date_short || rep.date) + '</td>' +
            '<td>' + subHtml +
                (group.every(function(o){ return o.status === 'delivered'; })
                    ? '<div style="margin-top:.4rem;"><button class="btn-ghost" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .55rem;color:var(--text-muted);" onclick="archiveOrder(' + JSON.stringify(group.map(function(o){ return o.id; })) + ',this)" title="Archive Table Session"><i data-lucide="archive" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Archive</button></div>'
                    : '') +
            '</td>' +
            '</tr>';
    });

    // ── Render individual (non-grouped) order rows ────────────────────────────
    soloOrders.forEach(function(o) {
        html += renderSingleOrderRow(o);
    });

    tbody.innerHTML = html;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// Build just the action buttons for one order (used in both grouped and solo rows)
function buildActionBtns(o) {
    var actionBtn = '';
    var act = INLINE_ACTIONS[o.status];

    if (o.status === 'preparing') {
        if (!o.prepared_at) {
            act = null;
            actionBtn = '<span style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .85rem;border-radius:99px;font-size:.78rem;font-weight:700;background:rgba(220,38,38,.1);color:#f87171;border:1px solid rgba(220,38,38,.25);">' +
                '<span style="width:7px;height:7px;border-radius:50%;background:#f87171;flex-shrink:0;animation:blink 1.2s infinite;display:inline-block;"></span>' +
                'Chef Cooking' +
                '</span>';
        } else if (o.order_type === 'delivery') {
            act = { label:'Dispatch Rider', icon:'bike', btnClass:'btn-warning', type:'dispatch' };
        } else {
            act = { label: o.order_type === 'pickup' ? 'Picked Up' : (o.order_type === 'dine_in' ? 'Serve' : 'Complete'), icon: o.order_type === 'pickup' ? 'package-check' : 'circle-check', btnClass:'btn-success', type: o.order_type === 'dine_in' ? 'serve-dine-in' : 'status', next:'delivered' };
        }
    }

    if (o.status === 'rider_assigned') {
        var riderName = o.rider ? escHtml(o.rider) : 'Rider';
        actionBtn = '<span style="display:inline-flex;align-items:center;gap:.4rem;padding:.45rem .85rem;border-radius:99px;font-size:.78rem;font-weight:700;background:rgba(139,92,246,.1);color:#a78bfa;border:1px solid rgba(139,92,246,.25);">' +
            '<span style="width:7px;height:7px;border-radius:50%;background:#a78bfa;flex-shrink:0;animation:blink 1.2s infinite;display:inline-block;"></span>' +
            'Awaiting Rider Pickup' +
            '</span>';
    }

    if (act) {
        actionBtn = '<button type="button" class="' + act.btnClass + '" style="font-size:.8rem;display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1rem;flex:1;justify-content:center;" ' +
            'onclick="quickAction(' + o.id + ',\'' + act.type + '\',\'' + (act.next || '') + '\',this)">' +
            '<i data-lucide="' + act.icon + '" style="width:.85rem;height:.85rem;stroke-width:2.5;flex-shrink:0;"></i>' +
            act.label +
            '</button>';

    }

    // Settings/detail button always
    actionBtn += '<button class="btn-ghost" style="font-size:.8rem;display:inline-flex;align-items:center;gap:.4rem;padding:.5rem .65rem;" onclick="openManageModal(' + o.id + ')" title="Details">' +
        '<i data-lucide="settings-2" style="width:.85rem;height:.85rem;stroke-width:2;"></i>' +
        '</button>';

    // Mark Ready button — shown for accepted or preparing-not-yet-ready
    if (o.status === 'accepted' || (o.status === 'preparing' && !o.prepared_at)) {
        actionBtn += '<button class="btn-ghost" style="font-size:.78rem;display:inline-flex;align-items:center;gap:.35rem;padding:.45rem .75rem;color:#d97706;border:1px solid rgba(217,119,6,.3);font-weight:700;white-space:nowrap;flex-shrink:0;" ' +
            'onclick="adminMarkReady(' + o.id + ',this)" title="Mark as Ready"' +
            ' onmouseover="this.style.background=\'rgba(217,119,6,.1)\'" onmouseout="this.style.background=\'transparent\'">' +
            '<i data-lucide="check-circle-2" style="width:.82rem;height:.82rem;stroke-width:2;flex-shrink:0;"></i> Ready' +
            '</button>';
    }

    return actionBtn;
}

function renderSingleOrderRow(o) {
        var sc = statusChip(o.status, o.order_type);
        var initial = (o.customer || 'G').charAt(0).toUpperCase();

        // Items preview
        var itemsHtml = '';
        var preview = o.items.slice(0, 2);
        preview.forEach(function(item) {
            itemsHtml +=
                '<div style="display:flex;align-items:center;gap:4px;margin-bottom:2px;">' +
                '<span style="font-size:.72rem;font-weight:700;color:var(--accent);background:rgba(250,204,21,.1);border-radius:4px;padding:1px 5px;flex-shrink:0;">x' + item.qty + '</span>' +
                '<span style="font-size:.75rem;color:var(--text-strong);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;" title="' + escHtml(item.name) + '">' + escHtml(item.name) + '</span>' +
                '</div>';
        });
        if (o.items.length > 2) {
            itemsHtml += '<span style="font-size:.68rem;color:var(--text-muted);">+' + (o.items.length - 2) + ' more</span>';
        }

        var actionBtn = buildActionBtns(o);

        // Archive / delete buttons
        if (o.order_type === 'delivery') {
            actionBtn += '<button class="btn-ghost" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .55rem;color:#60a5fa;" onclick="adminPrintTakeoutSlip(' + o.id + ')" title="Print Takeout Slip">' +
                '<i data-lucide="printer" style="width:.75rem;height:.75rem;stroke-width:2;"></i>' +
                '</button>';
        }
        // For dine-in solo rows — no print bill button needed
        if (['delivered','cancelled'].indexOf(o.status) !== -1) {
            actionBtn += '<button class="btn-ghost" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .55rem;color:' + (o.is_archived ? '#f59e0b' : 'var(--text-muted)') + ';" onclick="archiveOrder(' + o.id + ',this)" title="' + (o.is_archived ? 'Restore' : 'Archive') + '">' +
                '<i data-lucide="' + (o.is_archived ? 'archive-restore' : 'archive') + '" style="width:.75rem;height:.75rem;stroke-width:2;"></i>' +
                '</button>';
        }
        if (o.is_archived) {
            actionBtn += '<button class="btn-icon-delete" style="font-size:.72rem;padding:.35rem .55rem;" onclick="deleteOrder(' + o.id + ',\'' + escHtml(o.order_number) + '\',this)" title="Delete permanently">' +
                '<i data-lucide="trash-2" style="width:.75rem;height:.75rem;stroke-width:2;"></i>' +
                '</button>';
        }

        return '<tr id="order-row-' + o.id + '">' +
            '<td><span style="font-family:monospace;font-weight:700;color:var(--accent);font-size:.875rem;">' + escHtml(o.order_number) + '</span></td>' +
            '<td>' +
                '<div style="display:flex;align-items:center;gap:.5rem;">' +
                '<div style="width:1.875rem;height:1.875rem;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#000;font-weight:700;font-size:.7rem;flex-shrink:0;">' + initial + '</div>' +
                '<div>' +
                    '<div style="display:flex;align-items:center;gap:.3rem;margin-bottom:.1rem;">' +
                    '<p style="font-weight:600;color:var(--text-strong);font-size:.8rem;margin:0;">' + escHtml(o.customer) + '</p>' +
                    '<span style="font-size:10px;padding:1px 5px;border-radius:4px;background:rgba(255,255,255,.05);color:var(--text-muted);border:1px solid rgba(255,255,255,.1);display:inline-flex;align-items:center;gap:3px;">' + (o.order_type_icon || '') + ' ' + escHtml(o.order_type_label) + '</span>' +
                    '</div>' +
                    (o.order_type === 'dine_in' && o.table_number
                        ? '<p style="font-size:.7rem;color:#facc15;font-weight:700;margin:0 0 1px;">🪑 Table ' + escHtml(o.table_number) + '</p>'
                        : '<p style="font-size:.68rem;color:var(--text-muted);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;">' + escHtml(o.address || '') + '</p>') +
                '</div>' +
                '</div>' +
            '</td>' +
            '<td style="max-width:180px;">' + itemsHtml + '</td>' +
            '<td style="font-weight:700;color:var(--accent);">&#x20B1;' + Number(o.total).toLocaleString() + '</td>' +
            '<td><span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:9999px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;background:' + sc.bg + ';color:' + sc.color + ';">' + sc.label + '</span></td>' +
            '<td style="color:var(--text-muted);font-size:.72rem;white-space:nowrap;">' + escHtml(o.date_short || o.date) + '</td>' +
            '<td>' +
                '<div style="display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;">' +
                actionBtn +
                '</div>' +
            '</td>' +
            '</tr>';
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}


// ── Card Grid Renderer ────────────────────────────────────────────────────────
// Replaces renderTable() as the display layer ONLY.
// ALL logic (quickAction, buildActionBtns, modals, payment, auto-print, Echo,
// grouping algorithm) is 100% untouched — this just wraps output in card shells.
function renderGrid(orders) {
    var grid    = document.getElementById('ordersGrid');
    var countEl = document.getElementById('orderCount');
    if (!grid) return;

    if (!orders || !orders.length) {
        if (countEl) countEl.textContent = '0 order(s)';
        grid.innerHTML = '<div class="orders-grid-empty">No orders found.</div>';
        return;
    }

    // ── Exact same grouping logic as original renderTable() ──────────────────
    var GROUPABLE_STATUSES = ['pending','accepted','preparing','rider_assigned','out_for_delivery','delivered'];
    var tableGroups = {};
    var soloOrders  = [];

    orders.forEach(function(o) {
        var canGroup = o.order_type === 'dine_in'
            && o.table_number
            && o.table_session_id           // only group when session ID is known
            && GROUPABLE_STATUSES.indexOf(o.status) !== -1
            && !o.is_archived
            && !activeFilter;
        if (canGroup) {
            var gk = 'session_' + o.table_session_id;
            if (!tableGroups[gk]) tableGroups[gk] = [];
            tableGroups[gk].push(o);
        } else {
            soloOrders.push(o);
        }
    });

    Object.keys(tableGroups).forEach(function(k) {
        if (tableGroups[k].length === 1) {
            soloOrders.push(tableGroups[k][0]);
            delete tableGroups[k];
        }
    });

    var rowCount = soloOrders.length + Object.keys(tableGroups).length;
    if (countEl) countEl.textContent = rowCount + ' card(s) \u00B7 ' + orders.length + ' order(s)';

    // ── Sort: active cards first (newest active at top), served/done at bottom ──
    var ACTIVE_STATUSES = ['pending','accepted','preparing','rider_assigned','out_for_delivery'];

    // Priority score: lower = shown first
    function cardPriority(statusStr) {
        if (ACTIVE_STATUSES.indexOf(statusStr) !== -1) return 0; // active
        return 1; // delivered / cancelled
    }

    // For grouped cards: use the most urgent (highest priority) status in the group
    function groupTopStatus(group) {
        var STATUS_RANK = ['pending','accepted','preparing','rider_assigned','out_for_delivery','delivered','cancelled'];
        return group.reduce(function(best, o) {
            return STATUS_RANK.indexOf(o.status) < STATUS_RANK.indexOf(best) ? o.status : best;
        }, group[0].status);
    }

    // Build a unified list of cards to render, each with a sort key
    var allCards = [];

    Object.keys(tableGroups).forEach(function(sessionKey) {
        var group = tableGroups[sessionKey];
        var top   = groupTopStatus(group);
        // Use the newest order's id as the tie-breaker (most recent active = smallest id desc)
        var newestId = Math.max.apply(null, group.map(function(o){ return o.id; }));
        allCards.push({ type: 'group', sessionKey: sessionKey, priority: cardPriority(top), newestId: newestId });
    });

    soloOrders.forEach(function(o) {
        allCards.push({ type: 'solo', order: o, priority: cardPriority(o.status), newestId: o.id });
    });

    // Sort: active (priority 0) before done (priority 1).
    // Within the same priority tier:
    //   - For archived view: sort by created_at descending (newest date first)
    //   - For normal view: newest ID first
    allCards.sort(function(a, b) {
        if (a.priority !== b.priority) return a.priority - b.priority;
        if (dateFilter === 'archived') {
            // Sort by created_at descending for archived view
            var tA = a.order ? new Date(a.order.created_at || 0).getTime() : 0;
            var tB = b.order ? new Date(b.order.created_at || 0).getTime() : 0;
            if (tA !== tB) return tB - tA;
        }
        return b.newestId - a.newestId; // newest first within same tier
    });

    var html = '';

    // ── Render cards in sorted order ─────────────────────────────────────────
    allCards.forEach(function(card) {
      if (card.type === 'group') {
        var sessionKey = card.sessionKey;
        var group    = tableGroups[sessionKey].sort(function(a,b){ return a.id - b.id; });
        var rep      = group[0];
        var tableNum = rep.table_number || sessionKey;
        var grandTotal = group.reduce(function(s,o){ return s + parseFloat(o.total||0); }, 0);

        var statusPriority = ['pending','accepted','preparing','rider_assigned','out_for_delivery','delivered','cancelled'];
        var topStatus = group.reduce(function(best,o) {
            return statusPriority.indexOf(o.status) < statusPriority.indexOf(best) ? o.status : best;
        }, group[0].status);

        var allReady = group.every(function(o) {
            return o.status === 'preparing' && o.prepared_at && o.order_type !== 'delivery';
        });
        var sc = allReady
            ? { bg:'rgba(16,185,129,.15)', color:'#10b981', label:'Ready to Serve' }
            : statusChip(topStatus, 'dine_in');

        var isSessionLocked = group.some(function(o){ return o.ordering_locked; });

        var subHtml = '<div class="order-card-subitems">';

        if (allReady && group.length > 1) {
            // All ready: show each order as ready + single Complete Table button
            group.forEach(function(o) {
                subHtml +=
                    '<div class="order-card-subrow">' +
                    '<span class="order-card-badge" style="background:rgba(16,185,129,.12);color:#10b981;">\u2713 Ready</span>' +
                    '<span class="order-card-subrow-total">\u20B1' + Number(o.total).toLocaleString() + '</span>' +
                    '<button class="btn-ghost" style="font-size:.63rem;padding:.15rem .35rem;margin-left:auto;" onclick="openManageModal(' + o.id + ')" title="Details">' +
                    '<i data-lucide="settings-2" style="width:.65rem;height:.65rem;stroke-width:2;"></i></button>' +
                    '</div>';
            });
            subHtml += '</div>';
            // Complete Table + Done Ordering buttons
            subHtml +=
                '<div class="order-card-total" style="margin:.4rem 0 .2rem;">\u20B1' + Number(grandTotal).toLocaleString() + '</div>' +
                '<div class="order-card-actions">' +
                '<button type="button" class="oc-btn oc-btn-complete" data-grand-total="' + grandTotal + '" ' +
                'onclick="quickAction(' + rep.id + ',\'complete-table\',\'\',this)">' +
                '<i data-lucide="circle-check" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>' +
                ' Complete \u00B7 \u20B1' + Number(grandTotal).toLocaleString() + '</button>' +
                (isSessionLocked
                    ? '<span class="oc-btn oc-btn-locked">\uD83D\uDD12 Locked</span>'
                    : '<button type="button" class="oc-btn oc-btn-lock" onclick="quickAction(' + rep.id + ',\'lock-table\',\'\',this)" title="Done Ordering">' +
                      '<i data-lucide="lock" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Done</button>') +
                '</div>';
        } else {
            // Normal group: per-order buttons via existing buildActionBtns()
            group.forEach(function(o) {
                var oSc   = statusChip(o.status, o.order_type);
                var oBtns = buildActionBtns(o);
                subHtml +=
                    '<div class="order-card-subrow">' +
                    '<span class="order-card-badge" style="background:' + oSc.bg + ';color:' + oSc.color + ';">' + oSc.label + '</span>' +
                    '<span class="order-card-subrow-total">\u20B1' + Number(o.total).toLocaleString() + '</span>' +
                    '<div class="order-card-subrow-btns">' + oBtns + '</div>' +
                    '</div>';
            });
            subHtml += '</div>';

            // Total shown right after subrows, before any action buttons
            subHtml += '<div class="order-card-total" style="margin:.5rem 0 .15rem;">\u20B1' + Number(grandTotal).toLocaleString() + '</div>';

            // Lock button (same condition as original)
            var hasActive = group.some(function(o){ return ['pending','accepted','preparing'].indexOf(o.status) !== -1; });
            if (hasActive) {
                subHtml += isSessionLocked
                    ? '<div class="oc-locked-badge">\uD83D\uDD12 Done Ordering \u2014 Locked</div>'
                    : '<div style="margin-top:.4rem;"><button type="button" class="oc-btn oc-btn-lock" ' +
                      'onclick="quickAction(' + rep.id + ',\'lock-table\',\'\',this)" title="Done Ordering">' +
                      '<i data-lucide="lock" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Done Ordering</button></div>';
            }

            // Archive when all delivered
            if (group.every(function(o){ return o.status === 'delivered'; })) {
                var groupIds = group.map(function(o){ return o.id; });
                subHtml +=
                    '<div style="margin-top:.4rem;"><button class="btn-ghost" ' +
                    'style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .55rem;color:var(--text-muted);" ' +
                    'onclick="archiveOrder(' + JSON.stringify(groupIds) + ',this)" title="Archive Table Session">' +
                    '<i data-lucide="archive" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Archive</button></div>';
            }
        }

        html += buildOrderCard({
            cardId:    'group-' + sessionKey.replace(/[^a-z0-9]/gi,'_'),
            headerLeft: '\uD83E\uDE91 <span style="background:rgba(250,204,21,.18);border:1px solid rgba(250,204,21,.4);border-radius:.4rem;padding:.15rem .6rem;font-size:.95rem;font-weight:800;color:#facc15;letter-spacing:.01em;">Table ' + escHtml(tableNum) + '</span>' +
                (isSessionLocked ? ' <span class="oc-locked-sm">\uD83D\uDD12</span>' : ''),
            sc:        sc,
            metaLine1: group.length + ' order(s) \u00B7 ' + escHtml(rep.date_short || rep.date),
            metaLine2: '',
            total:     null,
            body:      subHtml,
        });
      } else {
        // Solo card
        html += buildSoloOrderCard(card.order);
      }
    });

    grid.innerHTML = html;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// Build a card for a single (non-grouped) order.
// Uses buildActionBtns(o) directly — zero reimplementation of any logic.
function buildSoloOrderCard(o) {
    var sc = statusChip(o.status, o.order_type);

    var isDineIn = o.order_type === 'dine_in' && o.table_number;
    var headerLeft, metaLine1;
    if (isDineIn) {
        headerLeft = '\uD83E\uDE91 <span style="background:rgba(250,204,21,.15);border:1px solid rgba(250,204,21,.35);border-radius:.4rem;padding:.15rem .6rem;font-size:.95rem;font-weight:800;color:#facc15;letter-spacing:.01em;">Table ' + escHtml(o.table_number) + '</span>';
        metaLine1 = '';
    } else {
        var typeIcon  = o.order_type_icon || '';
        var typeLabel = escHtml(o.order_type_label || o.order_type);
        var typeBg    = o.order_type === 'delivery' ? 'rgba(96,165,250,.15)'  : 'rgba(167,139,250,.15)';
        var typeBorder= o.order_type === 'delivery' ? 'rgba(96,165,250,.4)'   : 'rgba(167,139,250,.4)';
        var typeColor = o.order_type === 'delivery' ? '#60a5fa'                : '#a78bfa';
        headerLeft = '<span style="display:inline-flex;align-items:center;gap:.35rem;background:' + typeBg + ';border:1px solid ' + typeBorder + ';border-radius:.4rem;padding:.15rem .6rem;font-size:.95rem;font-weight:800;color:' + typeColor + ';letter-spacing:.01em;">' + typeIcon + ' ' + typeLabel + '</span>';
        metaLine1 = '';
    }

    // Items preview (up to 3)
    var itemsHtml = '<div class="order-card-items">';
    o.items.slice(0, 3).forEach(function(item) {
        itemsHtml +=
            '<div class="order-card-item">' +
            '<span class="order-card-item-qty">x' + item.qty + '</span>' +
            '<span class="order-card-item-name" title="' + escHtml(item.name) + '">' + escHtml(item.name) + '</span>' +
            '</div>';
    });
    if (o.items.length > 3) {
        itemsHtml += '<span class="order-card-item-more">+' + (o.items.length - 3) + ' more</span>';
    }
    itemsHtml += '</div>';

    // All action buttons come from the original buildActionBtns() — untouched
    var actionBtns = buildActionBtns(o);

    // Print button for delivery orders (available at preparing and beyond)
    if (o.order_type === 'delivery' && ['preparing','rider_assigned','out_for_delivery','delivered'].indexOf(o.status) !== -1) {
        actionBtns += '<button class="btn-ghost" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .55rem;color:#60a5fa;" onclick="adminPrintTakeoutSlip(' + o.id + ')" title="Print Takeout Slip">' +
            '<i data-lucide="printer" style="width:.75rem;height:.75rem;stroke-width:2;"></i>' +
            '</button>';
    }

    // Archive / Restore button
    if (['delivered','cancelled'].indexOf(o.status) !== -1) {
        actionBtns += '<button class="btn-ghost" style="font-size:.8rem;display:inline-flex;align-items:center;gap:.4rem;padding:.5rem .8rem;color:' + (o.is_archived ? '#f59e0b' : 'var(--text-muted)') + ';" onclick="archiveOrder(' + o.id + ',this)" title="' + (o.is_archived ? 'Restore' : 'Archive') + '">' +
            '<i data-lucide="' + (o.is_archived ? 'archive-restore' : 'archive') + '" style="width:.85rem;height:.85rem;stroke-width:2;"></i>' +
            (o.is_archived ? ' Restore' : ' Archive') +
            '</button>';
    }

    // Delete button (only when archived)
    if (o.is_archived) {
        actionBtns += '<button class="btn-icon-delete" style="font-size:.8rem;display:inline-flex;align-items:center;gap:.4rem;padding:.5rem .8rem;" onclick="deleteOrder(' + o.id + ',\'' + escHtml(o.order_number) + '\',this)" title="Delete permanently">' +
            '<i data-lucide="trash-2" style="width:.85rem;height:.85rem;stroke-width:2;"></i> Delete' +
            '</button>';
    }

    var actionsHtml = '<div class="order-card-actions">' + actionBtns + '</div>';

    return buildOrderCard({
        cardId:    'solo-' + o.id,
        headerLeft: headerLeft,
        sc:        sc,
        metaLine1: metaLine1,
        metaLine2: '\u23F0 ' + escHtml(o.date_short || o.date),
        total:     o.total,
        body:      itemsHtml,
        actions:   actionsHtml,
    });
}

// Shared card HTML shell — pure layout, zero logic.
function buildOrderCard(opts) {
    return '<div class="order-card" id="order-card-' + opts.cardId + '">' +
        '<div class="order-card-header">' +
            (opts.headerLeft ? '<span class="order-card-num">' + opts.headerLeft + '</span>' : '') +
            '<span class="order-card-badge" style="background:' + opts.sc.bg + ';color:' + opts.sc.color + ';">' + opts.sc.label + '</span>' +
        '</div>' +
        '<div class="order-card-meta">' +
            (opts.metaLine1 ? '<span>' + opts.metaLine1 + '</span>' : '') +
            (opts.metaLine2 ? '<span>' + opts.metaLine2 + '</span>' : '') +
        '</div>' +
        '<div class="order-card-body">' + (opts.body || '') + '</div>' +
        (opts.total != null ? '<div class="order-card-total">\u20B1' + Number(opts.total).toLocaleString() + '</div>' : '') +
        (opts.actions ? opts.actions : '') +
    '</div>';
}


// -- Print takeout slip (delivery orders only) --------------
function adminPrintTakeoutSlip(orderId) {
    var url = '/admin/orders/' + orderId + '/takeout-slip';
    var win = window.open(url, '_blank', 'width=320,height=620,menubar=no,toolbar=no,location=no,status=no');
    if (!win) {
        window.open(url, '_blank');
    }
}

// -- Print combined table bill (all today's dine-in orders at a table) ------
// Used for pahabol orders that ended up in different sessions but same table.
function printTableBill(tableNumber) {
    var url = '/chef/orders/table-bill/' + encodeURIComponent(tableNumber);
    var win = window.open(url, '_blank', 'width=380,height=700,menubar=no,toolbar=no,location=no,status=no');
    if (!win) {
        window.open(url, '_blank');
    }
}

// -- Rider card selection ----------------------------------
function selectRiderCard(el, riderId) {
    // Find the order id from the cards container
    var container = el.closest('[id^="riderCards_"]');
    if (!container) return;
    var orderId = container.id.replace('riderCards_', '');

    // Deselect all cards in this group
    container.querySelectorAll('.rider-card').forEach(function(card) {
        card.style.borderColor = 'var(--border-card)';
        card.style.background  = 'var(--bg-filter)';
        card.querySelector('.rider-radio').style.background   = 'transparent';
        card.querySelector('.rider-radio').style.borderColor  = 'var(--border-card)';
    });

    // Select this card
    el.style.borderColor = '#f59e0b';
    el.style.background  = 'rgba(245,158,11,.08)';
    el.querySelector('.rider-radio').style.background  = '#f59e0b';
    el.querySelector('.rider-radio').style.borderColor = '#f59e0b';

    // Store selected rider and enable button
    var input = document.getElementById('selectedRider_' + orderId);
    var btn   = document.getElementById('assignBtn_' + orderId);
    if (input) input.value = riderId;
    if (btn)   btn.disabled = false;
}

async function assignRider(orderId, btn) {
    var input    = document.getElementById('selectedRider_' + orderId);
    var riderId  = input ? input.value : '';
    if (!riderId) return;

    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '? Assigning�';

    try {
        var url = '{{ route("admin.orders.assign-rider", ":id") }}'.replace(':id', orderId);
        var res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ rider_id: riderId }),
        });
        var data = await res.json();
        if (data.success || res.ok) {
            closeModal('manageModal');
            await fetchOrders();
        } else {
            var msg = data.message || 'Failed to assign rider.';
            logError('AssignRider', '[Order #' + orderId + '] ' + msg);
            alert(msg);
            btn.disabled = false;
            btn.innerHTML = orig;
        }
    } catch(e) {
        logError('AssignRider', '[Order #' + orderId + '] ' + e.message);
        alert('An error occurred.');
        btn.disabled = false;
        btn.innerHTML = orig;
    }
}

// -- Inline quick action (replaces quickAccept for all statuses) --------------
async function quickAction(orderId, type, nextStatus, btn) {
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '?';

    try {
        let url, method = 'POST', body = null;

        if (type === 'accept') {
            url = '{{ route("admin.orders.accept", ":id") }}'.replace(':id', orderId);

        } else if (type === 'complete-table') {
            // Intercept — open payment modal first
            var o = ORDERS_MAP[orderId];
            var tableNum   = o ? (o.table_number || '\u2014') : '\u2014';
            // Try data attribute first, then button text, then fallback to single order total
            var grandTotal = parseFloat(btn.getAttribute('data-grand-total')) || 0;
            if (!grandTotal) {
                var btnText = btn.textContent || '';
                var match   = btnText.match(/\u20B1([\d,]+)/);
                if (match) grandTotal = parseFloat(match[1].replace(/,/g, ''));
            }
            if (!grandTotal) grandTotal = o ? parseFloat(o.total || 0) : 0;
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            btn._origHtml = originalHtml;
            openPaymentModal(orderId, grandTotal, tableNum, btn);
            return;

        } else if (type === 'serve-dine-in') {
            // Individual dine-in "Serve" — open payment modal
            var oDine = ORDERS_MAP[orderId];
            var tableNumDine   = oDine ? (oDine.table_number || '\u2014') : '\u2014';
            var grandTotalDine = oDine ? parseFloat(oDine.total || 0) : 0;
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            btn._origHtml = originalHtml;
            openPaymentModal(orderId, grandTotalDine, tableNumDine, btn, 'serve-dine-in');
            return;

        } else if (type === 'lock-table') {
            // Lock ordering for this table session — no more pahabol merges allowed
            var o = ORDERS_MAP[orderId];
            var tableNum = o ? ('Table ' + (o.table_number || '?')) : 'this table';
            if (!confirm('Mark ' + tableNum + ' as Done Ordering?\n\nAfter locking:\n\u2022 No new pahabol orders will be merged into this session.\n\u2022 A new customer on the same table will get a fresh session.')) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                return;
            }
            url = '{{ route("admin.orders.lock-table", ":id") }}'.replace(':id', orderId);
            method = 'POST';
            body = null;

        } else if (type === 'dispatch') {
            // Open the manage modal so the admin can pick a rider
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            openManageModal(orderId);
            return;

        } else {
            url    = '{{ route("admin.orders.status", ":id") }}'.replace(':id', orderId);
            method = 'PATCH';
            body   = JSON.stringify({ status: nextStatus });
        }

        const res  = await fetch(url, {
            method: method,
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: body,
        });
        const data = await res.json();

        if (data.success || res.ok) {
            if (data.receipt_url) printReceipt(data.receipt_url);
            await fetchOrders();
        } else {
            var msg = data.message || 'Action failed.';
            logError('QuickAction', '[Order #' + orderId + '] ' + msg, 'Status: ' + res.status);
            alert(msg);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            if (window.lucide) lucide.createIcons();
        }
    } catch(e) {
        console.error(e);
        logError('QuickAction', '[Order #' + orderId + '] Network or JS error', e.message);
        alert('An error occurred. Please try again.');
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if (window.lucide) lucide.createIcons();
    }
}

// Keep old quickAccept/handleModalAccept wired to quickAction for modal use
async function quickAccept(orderId, btn) {
    if (!confirm('Accept this order?')) return;
    await quickAction(orderId, 'accept', '', btn);
}

// -- Cancel order confirmation modal --------------------------------------
function openCancelConfirm(orderId, orderNumber) {
    var label = document.getElementById('cancelOrderLabel');
    if (label) label.textContent = orderNumber || 'this order';

    var btn = document.getElementById('cancelConfirmBtn');
    // Remove any previous listener by cloning the button
    var fresh = btn.cloneNode(true);
    btn.parentNode.replaceChild(fresh, btn);
    fresh.addEventListener('click', function() {
        var form = document.getElementById('cancelOrderForm-' + orderId);
        if (form) form.submit();
        closeModal('cancelConfirmModal');
    });

    openModal('cancelConfirmModal');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// -- Preview modal: show full order before accepting -----------------------
function openPreviewModal(id) {
    var o = ORDERS_MAP[id];
    if (!o) { fetchOrders().then(function(){ openPreviewModal(id); }); return; }

    document.getElementById('pvTitle').textContent = o.order_number +
        (o.table_number ? ' · 🪑 Table ' + o.table_number : '');

    // Build items list
    var itemsHtml = '';
    var subtotal  = 0;
    o.items.forEach(function(item) {
        subtotal += parseFloat(item.subtotal || 0);
        var modTags = '';
        if (item.modifiers && item.modifiers.length) {
            item.modifiers.forEach(function(m) {
                if (!m || !m.name || /^no\s/i.test(m.name)) return;
                var colors = { flavor:'#3b82f6', modifier:'#8b5cf6', addon:'#d97706' };
                var c = colors[m.type] || '#8b5cf6';
                modTags += '<span style="padding:.1rem .4rem;border-radius:99px;font-size:.58rem;background:' + c + '18;color:' + c + ';font-weight:600;margin-right:.2rem;">' + escHtml(m.name) + '</span>';
            });
        }
        itemsHtml +=
            '<div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.5rem 0;border-bottom:1px solid var(--border-divider);">' +
                '<div style="flex:1;min-width:0;">' +
                    '<span style="font-weight:700;color:var(--text-strong);font-size:.82rem;">×' + item.qty + ' ' + escHtml(item.name) + '</span>' +
                    (modTags ? '<div style="margin-top:.25rem;display:flex;flex-wrap:wrap;gap:.2rem;">' + modTags + '</div>' : '') +
                    '<div style="font-size:.68rem;color:var(--text-muted);margin-top:.15rem;">₱' + Number(item.price || (item.subtotal / item.qty)).toLocaleString() + ' each</div>' +
                '</div>' +
                '<span style="font-size:.82rem;font-weight:700;color:var(--accent);flex-shrink:0;margin-left:.75rem;">₱' + Number(item.subtotal).toLocaleString() + '</span>' +
            '</div>';
    });

    var html =
        // Table / order type badge
        '<div style="display:flex;align-items:center;gap:.5rem;padding:.5rem .75rem;border-radius:.5rem;background:rgba(250,204,21,.07);border:1px solid rgba(250,204,21,.18);">' +
            '<span style="font-size:1.1rem;">🪑</span>' +
            '<div>' +
                '<p style="margin:0;font-size:.75rem;font-weight:700;color:#facc15;">' +
                    (o.table_number ? 'Table ' + escHtml(o.table_number) + ' — Dine-in' : escHtml(o.order_type_label || o.order_type)) +
                '</p>' +
                '<p style="margin:0;font-size:.65rem;color:var(--text-muted);">Placed ' + escHtml(o.date_short || o.date) + ' · ' + escHtml(o.customer) + '</p>' +
            '</div>' +
        '</div>' +
        // Items
        '<div>' +
            '<p style="font-size:.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .35rem;font-weight:600;">Items Ordered</p>' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.25rem .875rem;">' +
                itemsHtml +
                '<div style="display:flex;justify-content:space-between;padding:.5rem 0;margin-top:.15rem;">' +
                    '<span style="font-weight:800;color:var(--text-strong);font-size:.875rem;">Total</span>' +
                    '<span style="font-weight:800;font-size:1.05rem;color:var(--accent);">₱' + Number(o.total).toLocaleString() + '</span>' +
                '</div>' +
            '</div>' +
        '</div>' +
        // Notes
        (o.notes
            ? '<div style="background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.2);border-radius:.5rem;padding:.6rem .875rem;">' +
                '<p style="font-size:.65rem;color:#d97706;text-transform:uppercase;letter-spacing:.06em;margin:0 0 .25rem;font-weight:700;">📝 Customer Note</p>' +
                '<p style="font-size:.78rem;color:var(--text-body);margin:0;">' + escHtml(o.notes) + '</p>' +
              '</div>'
            : '');

    document.getElementById('pvBody').innerHTML = html;

    // Wire up the Accept button
    var acceptBtn = document.getElementById('pvAcceptBtn');
    // Clone to remove any previous listener
    var fresh = acceptBtn.cloneNode(true);
    acceptBtn.parentNode.replaceChild(fresh, acceptBtn);
    fresh.addEventListener('click', function() {
        closeModal('previewModal');
        // Call quickAction directly — the fresh button is used just as a dummy ref
        quickAction(id, 'accept', '', fresh);
    });

    openModal('previewModal');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

async function archiveOrder(orderIds, btn) {
    // orderIds can be a single integer or an array of integers
    var ids = Array.isArray(orderIds) ? orderIds : [orderIds];
    var isBulk = ids.length > 1;
    var orig = btn.innerHTML;
    btn.disabled = true;
    try {
        var url, body;
        if (isBulk) {
            url  = '/admin/orders/bulk-archive';
            body = JSON.stringify({ ids: ids, archive: true });
        } else {
            url  = '/admin/orders/' + ids[0] + '/archive';
            body = null;
        }
        var res = await fetch(url, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: body,
        });
        var data = await res.json();
        if (data.success) { await fetchOrders(); }
        else { alert(data.message || 'Failed.'); btn.disabled = false; btn.innerHTML = orig; }
    } catch(e) { alert('Network error.'); btn.disabled = false; btn.innerHTML = orig; }
}

async function deleteOrder(orderId, orderNum, btn) {
    if (!confirm('Permanently delete order ' + orderNum + '? This cannot be undone.')) return;
    var orig = btn.innerHTML;
    btn.disabled = true;
    try {
        var url = '/admin/orders/' + orderId;
        var res = await fetch(url, { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json','Content-Type':'application/json'} });
        var data = await res.json();
        if (data.success) { await fetchOrders(); }
        else { alert(data.message || 'Failed.'); btn.disabled = false; btn.innerHTML = orig; }
    } catch(e) { alert('Network error.'); btn.disabled = false; btn.innerHTML = orig; }
}

async function handleModalAccept(orderId, btn) {
    await quickAction(orderId, 'accept', '', btn);
}

function openManageModal(id) {
    var o = ORDERS_MAP[id];
    if (!o) { fetchOrders().then(function(){ openManageModal(id); }); return; }

    // Adjust pipeline based on order state
    var sp = JSON.parse(JSON.stringify(STATUS_PIPELINE[o.status] || {}));

    // Delivery order, preparing: show Dispatch only when chef has marked ready
    if (o.order_type === 'delivery' && o.status === 'preparing') {
        if (o.prepared_at) {
            sp.next      = null; // dispatch via modal rider picker, not status patch
            sp.nextLabel = null;
            sp.btnClass  = '';
            sp.chefAction = false;
            sp.dispatchReady = true; // signal to show rider assignment section
        } else {
            sp.chefAction = true; // still cooking
        }
    }

    // Allow rider reassignment for rider_assigned and out_for_delivery
    if (o.order_type === 'delivery' && ['rider_assigned', 'out_for_delivery'].includes(o.status)) {
        sp.dispatchReady = true;
        sp.chefAction    = false;
    }

    // Non-delivery (pickup/dine-in), preparing ? only allow Complete AFTER kitchen marks ready
    if (o.order_type !== 'delivery' && o.status === 'preparing') {
        if (o.prepared_at) {
            sp.next      = 'delivered';
            sp.nextLabel = o.order_type === 'pickup' ? 'Mark as Picked Up' : 'Mark as Completed';
            sp.btnClass  = 'btn-success';
            sp.chefAction = false;
        } else {
            // Still cooking � keep chefAction true so the modal shows "awaiting kitchen" state
            sp.next      = null;
            sp.nextLabel = null;
            sp.btnClass  = '';
            sp.chefAction = true;
        }
    }

    document.getElementById('mmTitle').textContent = 'Manage Order ' + o.order_number;

    // -- Timeline
    var isDelivery = o.order_type === 'delivery';
    var statusOrder = isDelivery
        ? ['pending','accepted','preparing','out_for_delivery','delivered']
        : ['pending','accepted','preparing','delivered'];

    var curIdx = statusOrder.indexOf(o.status);
    if (isDelivery && o.status === 'rider_assigned') curIdx = 2;

    var timelineSteps = isDelivery ? STATUS_TIMELINE : STATUS_TIMELINE.filter(function(s){ return s.key !== 'out_for_delivery'; });

    var tlHtml = '<div style="display:flex;align-items:center;gap:0;margin-bottom:.25rem;overflow-x:auto;padding-bottom:.25rem;">';
    timelineSteps.forEach(function(step, i) {
        var done     = i < (isDelivery && o.status === 'rider_assigned' ? 3 : curIdx);
        var current  = (i === curIdx) || (isDelivery && o.status === 'rider_assigned' && i === 2);
        var dotColor  = done || current ? (step.key === 'delivered' ? '#10b981' : '#3b82f6') : 'var(--border-card)';
        var textColor = done || current ? 'var(--text-strong)' : 'var(--text-muted)';
        var fw        = current ? '700' : '500';
        tlHtml +=
            '<div style="display:flex;flex-direction:column;align-items:center;flex:1;min-width:60px;">' +
                '<div style="width:2rem;height:2rem;border-radius:50%;background:' + dotColor + ';display:flex;align-items:center;justify-content:center;font-size:.875rem;' +
                (current ? 'box-shadow:0 0 0 4px ' + dotColor + '33;' : '') + '">' +
                    step.icon +
                '</div>' +
                '<span style="font-size:.6rem;font-weight:' + fw + ';color:' + textColor + ';text-align:center;margin-top:.3rem;line-height:1.2;">' +
                    (step.key === 'delivered' && !isDelivery ? (o.order_type === 'pickup' ? 'Picked Up' : 'Completed') : step.label) +
                '</span>' +
            '</div>';
        if (i < timelineSteps.length - 1) {
            tlHtml += '<div style="height:2px;flex:1;background:' + (done ? '#3b82f6' : 'var(--border-card)') + ';margin-top:1rem;min-width:16px;"></div>';
        }
    });
    tlHtml += '</div>';

    if (o.status === 'cancelled') {
        tlHtml += '<div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:.5rem;padding:.5rem .875rem;font-size:.75rem;color:#ef4444;font-weight:600;margin-top:.25rem;display:flex;align-items:center;gap:.4rem;"><svg width="13" height="13" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> This order was cancelled.</div>';
    }

    // -- Items summary
    var itemsHtml = '';
    o.items.forEach(function(item) {
        var modTags = '';
        if (item.modifiers && item.modifiers.length) {
            item.modifiers.forEach(function(m) {
                if (!m || !m.name || /^no\s/i.test(m.name)) return;
                var colors = { flavor:'#3b82f6', modifier:'#8b5cf6', addon:'#d97706' };
                var c = colors[m.type] || '#8b5cf6';
                modTags += '<span style="padding:.1rem .45rem;border-radius:99px;font-size:.6rem;background:' + c + '18;color:' + c + ';font-weight:600;">' + escHtml(m.name) + '</span>';
            });
        }
        itemsHtml +=
            '<div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.45rem 0;border-bottom:1px solid var(--border-divider);">' +
                '<div>' +
                    '<span style="font-weight:600;color:var(--text-strong);font-size:.8rem;">x' + item.qty + ' ' + escHtml(item.name) + '</span>' +
                    (modTags ? '<div style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.25rem;">' + modTags + '</div>' : '') +
                '</div>' +
                '<div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;margin-left:.5rem;">' +
                    '<span style="font-size:.8rem;color:var(--text-body);font-weight:600;">&#x20B1;' + Number(item.subtotal).toLocaleString() + '</span>' +
                    ((['pending','accepted','preparing'].indexOf(o.status) !== -1 && !o.prepared_at && item.id)
                        ? '<div style="display:inline-flex;align-items:center;gap:.25rem;flex-shrink:0;">' +
                              '<button type="button" ' +
                                'data-order="' + o.id + '" data-item="' + item.id + '" data-dir="-1" ' +
                                'onclick="admInlineQty(this)" ' +
                                'style="width:1.6rem;height:1.6rem;border-radius:.35rem;border:1px solid rgba(239,68,68,.35);background:rgba(239,68,68,.1);color:#ef4444;cursor:pointer;font-size:.95rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;transition:background .15s;flex-shrink:0;" ' +
                                'onmouseover="this.style.background=\'rgba(239,68,68,.22)\'" onmouseout="this.style.background=\'rgba(239,68,68,.1)\'">&minus;</button>' +
                              '<span class="adm-item-qty-display" data-item="' + item.id + '" style="min-width:1.4rem;text-align:center;font-size:.85rem;font-weight:800;color:#facc15;">' + item.qty + '</span>' +
                              '<button type="button" ' +
                                'data-order="' + o.id + '" data-item="' + item.id + '" data-dir="1" ' +
                                'onclick="admInlineQty(this)" ' +
                                'style="width:1.6rem;height:1.6rem;border-radius:.35rem;border:1px solid rgba(74,222,128,.35);background:rgba(74,222,128,.1);color:#4ade80;cursor:pointer;font-size:.95rem;font-weight:700;display:inline-flex;align-items:center;justify-content:center;transition:background .15s;flex-shrink:0;" ' +
                                'onmouseover="this.style.background=\'rgba(74,222,128,.22)\'" onmouseout="this.style.background=\'rgba(74,222,128,.1)\'">+</button>' +
                          '</div>'
                        : '') +
                '</div>' +
            '</div>';
    });

    // -- Action buttons
    var actionsHtml = '';
    var acceptRoute  = '{{ route("admin.orders.accept", ":id") }}';
    var statusRoute  = '{{ route("admin.orders.status", ":id") }}';
    var assignRoute  = '{{ route("admin.orders.assign-rider", ":id") }}';

    if (sp.next && o.status !== 'delivered' && o.status !== 'cancelled') {
        var actionRoute = o.status === 'pending'
            ? acceptRoute.replace(':id', o.id)
            : statusRoute.replace(':id', o.id);

        if (o.status === 'pending') {
            actionsHtml +=
                '<button type="button" class="' + sp.btnClass + '" style="font-size:.875rem;width:100%;justify-content:center;gap:.4rem;display:inline-flex;align-items:center;" ' +
                'onclick="handleModalAccept(' + o.id + ', this)">' +
                    sp.nextLabel +
                '</button>';
        } else {
            actionsHtml +=
                '<form method="POST" action="' + actionRoute + '" style="display:inline;" id="nextStepForm_' + o.id + '">' +
                    '<input type="hidden" name="_token" value="' + CSRF_TOKEN + '">' +
                    '<input type="hidden" name="_method" value="PATCH"><input type="hidden" name="status" value="' + sp.next + '">' +
                    '<button type="submit" class="' + sp.btnClass + '" style="font-size:.875rem;width:100%;justify-content:center;gap:.4rem;display:inline-flex;align-items:center;">' +
                        sp.nextLabel +
                    '</button>' +
                '</form>';
        }
    }

    // rider_assigned ? inform admin that the rider must confirm pickup themselves
    if (o.status === 'rider_assigned' && sp.riderAction) {
        var riderName = o.rider ? escHtml(o.rider) : 'the assigned rider';
        actionsHtml +=
            '<div style="border:1px solid rgba(139,92,246,.25);border-radius:.75rem;background:rgba(139,92,246,.07);padding:.875rem 1rem;display:flex;align-items:flex-start;gap:.6rem;">' +
                '<div style="width:2rem;height:2rem;border-radius:.5rem;background:rgba(139,92,246,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                    '<svg width="14" height="14" fill="none" stroke="#a78bfa" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3"/></svg>' +
                '</div>' +
                '<div>' +
                    '<p style="font-size:.8rem;font-weight:700;color:#a78bfa;margin:0 0 .2rem;">Waiting for Rider to Pick Up</p>' +
                    '<p style="font-size:.72rem;color:var(--text-muted);margin:0;line-height:1.5;">' +
                        riderName + ' must tap <strong style="color:#c4b5fd;">Picked Up</strong> on their Rider Dashboard to move this order to <em>Out for Delivery</em>.' +
                    '</p>' +
                '</div>' +
            '</div>';
    }

    // pending / accepted / preparing ? handled by the Chef in the Kitchen board
    if (sp.chefAction) {
        var chefStepLabel = o.status === 'pending' ? 'accept this order' : (o.status === 'accepted' ? 'start cooking' : 'mark it ready & assign a rider');
        actionsHtml +=
            '<div style="border:1px solid rgba(217,119,6,.25);border-radius:.75rem;background:rgba(217,119,6,.07);padding:.875rem 1rem;display:flex;align-items:flex-start;gap:.6rem;">' +
                '<div style="width:2rem;height:2rem;border-radius:.5rem;background:rgba(217,119,6,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">' +
                    '<svg width="14" height="14" fill="none" stroke="#f59e0b" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>' +
                '</div>' +
                '<div>' +
                    '<p style="font-size:.8rem;font-weight:700;color:#f59e0b;margin:0 0 .2rem;">' + (o.status === 'accepted' ? 'Queued for Kitchen' : 'Chef is Cooking') + '</p>' +
                    '<p style="font-size:.72rem;color:var(--text-muted);margin:0;line-height:1.5;">' +
                        (o.status === 'accepted'
                            ? 'Waiting for the chef to <strong style="color:#fcd34d;">start cooking</strong> from the Kitchen Dashboard.'
                            : 'Waiting for the chef to <strong style="color:#fcd34d;">mark this order as ready</strong> from the Kitchen Dashboard.') +
                    '</p>' +
                '</div>' +
            '</div>';
    }

    if (sp.dispatchReady) {
        var riderCards   = '';
        var ridersToShow = RIDERS.length > 0 ? RIDERS : [];

        if (ridersToShow.length > 0) {
            ridersToShow.forEach(function(r) {
                var initials = r.name.split(' ').map(function(w){ return w[0]; }).join('').substring(0,2).toUpperCase();
                var busyTag  = r.busy ? '<span style="font-size:.6rem;background:rgba(239,68,68,.15);color:#ef4444;border-radius:4px;padding:1px 5px;margin-left:4px;">On Delivery</span>' : '';
                riderCards +=
                    '<div class="rider-card" data-rider-id="' + r.id + '" onclick="selectRiderCard(this,' + r.id + ')" ' +
                    'style="display:flex;align-items:center;gap:.6rem;padding:.5rem .75rem;border-radius:.5rem;border:2px solid var(--border-card);cursor:pointer;transition:all .15s;background:var(--bg-filter);">' +
                        '<div style="width:2rem;height:2rem;border-radius:50%;background:rgba(245,158,11,.15);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.65rem;color:#f59e0b;flex-shrink:0;">' + escHtml(initials) + '</div>' +
                        '<div style="flex:1;min-width:0;">' +
                            '<div style="font-size:.8rem;font-weight:600;color:var(--text-strong);display:flex;align-items:center;">' + escHtml(r.name) + busyTag + '</div>' +
                            (r.phone ? '<div style="font-size:.68rem;color:var(--text-muted);">' + escHtml(r.phone) + '</div>' : '') +
                        '</div>' +
                        '<div style="width:.875rem;height:.875rem;border-radius:50%;border:2px solid var(--border-card);flex-shrink:0;" class="rider-radio"></div>' +
                    '</div>';
            });

            actionsHtml +=
                '<div style="margin-top:.5rem;">' +
                    '<p style="font-size:.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .4rem;font-weight:600;">Assign Rider</p>' +
                    '<div id="riderCards_' + o.id + '" style="display:flex;flex-direction:column;gap:.35rem;margin-bottom:.5rem;">' +
                        riderCards +
                    '</div>' +
                    '<input type="hidden" id="selectedRider_' + o.id + '" value="">' +
                    '<button type="button" onclick="assignRider(' + o.id + ',this)" class="btn-primary" style="width:100%;justify-content:center;font-size:.8rem;display:inline-flex;align-items:center;gap:.3rem;" disabled id="assignBtn_' + o.id + '">' +
                        '<i data-lucide="bike" style="width:.8rem;height:.8rem;stroke-width:2;"></i> ' +
                        (['rider_assigned','out_for_delivery'].includes(o.status) ? 'Reassign Rider' : 'Assign Rider') +
                    '</button>' +
                '</div>';
        } else {
            actionsHtml +=
                '<div style="margin-top:.5rem;padding:.6rem .75rem;border-radius:.5rem;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);font-size:.78rem;color:#ef4444;">' +
                    '<div style="display:flex;align-items:center;gap:.4rem;"><svg width="13" height="13" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> No riders found. Add a rider first.</div>' +
                '</div>';
        }
    }

    if (['pending','accepted','preparing','rider_assigned','out_for_delivery'].includes(o.status)) {
        actionsHtml +=
            '<form id="cancelOrderForm-' + o.id + '" method="POST" action="' + statusRoute.replace(':id', o.id) + '" style="margin-top:.25rem;">' +
                '<input type="hidden" name="_token" value="' + CSRF_TOKEN + '">' +
                '<input type="hidden" name="_method" value="PATCH">' +
                '<input type="hidden" name="status" value="cancelled">' +
                '<button type="button" class="btn-danger" style="font-size:.8rem;display:inline-flex;align-items:center;gap:.3rem;width:100%;justify-content:center;" onclick="openCancelConfirm(' + o.id + ', \'' + escHtml(o.order_number) + '\')">' +
                    '<i data-lucide="x-circle" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Cancel Order' +
                '</button>' +
            '</form>';
    }

    // -- Assemble modal body
    var html =
        '<div>' +
            '<p style="font-size:.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .75rem;font-weight:600;">Order Progress</p>' +
            tlHtml +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:.625rem;">' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.75rem;">' +
                '<p style="font-size:.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .25rem;">Customer</p>' +
                '<p style="font-weight:600;color:var(--text-strong);font-size:.8rem;margin:0;">' + escHtml(o.customer) + '</p>' +
                '<p style="color:var(--text-muted);font-size:.7rem;margin:.1rem 0 0;">' + escHtml(o.email) + '</p>' +
                (o.phone ? '<p style="color:#60a5fa;font-size:.75rem;font-weight:600;margin:.15rem 0 0;display:flex;align-items:center;gap:.3rem;"><svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>' + escHtml(o.phone) + '</p>' : '') +
            '</div>' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.75rem;">' +
                '<p style="font-size:.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .25rem;">Payment &middot; Total</p>' +
                '<p style="font-weight:700;color:var(--accent);font-size:1rem;margin:0;">&#x20B1;' + Number(o.total).toLocaleString() + '</p>' +
                '<p style="color:var(--text-muted);font-size:.7rem;margin:.1rem 0 0;text-transform:capitalize;">' + escHtml(o.payment) + '</p>' +
            '</div>' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.75rem;grid-column:span 2;">' +
                '<p style="font-size:.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .25rem;">' + (o.order_type === 'dine_in' ? 'Table Number' : 'Delivery Address') + '</p>' +
                (o.order_type === 'dine_in' && o.table_number
                    ? '<p style="font-size:.95rem;font-weight:800;color:#facc15;margin:0;">?? Table ' + escHtml(o.table_number) + '</p>'
                    : '<p style="font-size:.8rem;color:var(--text-body);margin:0;">' + escHtml(o.address || '') + '</p>') +
            '</div>' +
        '</div>' +
        '<div>' +
            '<p style="font-size:.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .5rem;font-weight:600;">Items Ordered</p>' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.625rem 1rem;">' +
                itemsHtml +
                '<div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.8rem;">' +
                    '<span style="color:var(--text-muted);">Delivery Fee</span><span style="color:var(--text-body);">&#x20B1;' + Number(o.delivery_fee).toLocaleString() + '</span>' +
                '</div>' +
                '<div style="display:flex;justify-content:space-between;padding:.5rem 0;border-top:1px solid var(--border-divider);margin-top:.25rem;">' +
                    '<span style="font-weight:700;color:var(--text-strong);">Total</span>' +
                    '<span style="font-weight:800;font-size:1rem;color:var(--accent);">&#x20B1;' + Number(o.total).toLocaleString() + '</span>' +
                '</div>' +
            '</div>' +
        '</div>' +
        (o.notes ? '<div style="background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.2);border-radius:.625rem;padding:.75rem 1rem;"><p style="font-size:.68rem;color:#d97706;text-transform:uppercase;letter-spacing:.06em;margin:0 0 .3rem;font-weight:700;display:flex;align-items:center;gap:.3rem;"><svg width="12" height="12" fill="none" stroke="#d97706" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg> Customer Note</p><p style="font-size:.8rem;color:var(--text-body);margin:0;">' + escHtml(o.notes) + '</p></div>' : '') +
        (o.status === 'out_for_delivery'
            ? '<div style="background:rgba(139,92,246,.06);border:1px solid rgba(139,92,246,.2);border-radius:.625rem;padding:.75rem 1rem;">' +
                '<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.625rem;">' +
                    '<div style="width:2rem;height:2rem;border-radius:50%;background:rgba(139,92,246,.15);display:flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="#a78bfa" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3"/></svg></div>' +
                    '<div><p style="font-size:.75rem;font-weight:700;color:#a78bfa;margin:0 0 .1rem;">Rider En Route to Customer</p>' +
                    '<p style="font-size:.68rem;color:var(--text-muted);margin:0;">Live tracking � only the rider can mark as delivered.</p></div>' +
                '</div>' +
                '<div id="adminOrderMap-' + o.id + '" style="height:220px;width:100%;border-radius:.5rem;overflow:hidden;background:#0a0a14;"></div>' +
              '</div>'
            : '') +
        (actionsHtml ? '<div><p style="font-size:.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .5rem;font-weight:600;">Actions</p>' + actionsHtml + '</div>' : '');

    document.getElementById('mmBody').innerHTML = html;

    var mmTitle = document.getElementById('mmTitle');
    mmTitle.innerHTML = 'Manage Order ' + escHtml(o.order_number) +
        ' <span style="font-size:10px;padding:2px 8px;border-radius:6px;background:rgba(255,255,255,.08);color:var(--text-muted);border:1px solid rgba(255,255,255,.12);margin-left:.5rem;vertical-align:middle;">' +
        (o.order_type_icon || '') + ' ' + escHtml(o.order_type_label) + '</span>';

    openModal('manageModal');
    if (typeof lucide !== 'undefined') setTimeout(function(){ lucide.createIcons(); }, 0);

    if (o.status === 'out_for_delivery' && o.rider_lat && o.rider_lng) {
        setTimeout(function() { initAdminRiderMap(o); }, 250);
    }
}

// Admin live rider map
var adminMapInstance = null;
const ADMIN_RESTAURANT = [13.321512, 121.302098];

async function fetchAdminRoute(from, to) {
    var url = 'https://router.project-osrm.org/route/v1/driving/' + from[1] + ',' + from[0] + ';' + to[1] + ',' + to[0] + '?overview=full&geometries=geojson';
    try {
        var r = await fetch(url);
        var d = await r.json();
        if (d.code === 'Ok' && d.routes.length) {
            return d.routes[0].geometry.coordinates.map(function(c){ return [c[1], c[0]]; });
        }
    } catch(e) { console.warn('OSRM admin', e); }
    return null;
}

async function initAdminRiderMap(o) {
    var mapEl = document.getElementById('adminOrderMap-' + o.id);
    if (!mapEl) return;

    if (adminMapInstance) {
        try { adminMapInstance.remove(); } catch(e) {}
        adminMapInstance = null;
    }

    var riderPos = [parseFloat(o.rider_lat), parseFloat(o.rider_lng)];
    var custPos  = (o.delivery_lat && o.delivery_lng)
        ? [parseFloat(o.delivery_lat), parseFloat(o.delivery_lng)]
        : null;

    adminMapInstance = L.map(mapEl, { zoomControl: true, attributionControl: false });
    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20 }).addTo(adminMapInstance);
    L.tileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', { maxZoom: 20, opacity: 0.85 }).addTo(adminMapInstance);

    L.marker(ADMIN_RESTAURANT, { icon: L.divIcon({
        html: '<div style="background:#facc15;width:34px;height:34px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #d97706;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.5);"><span style="transform:rotate(45deg);display:flex;align-items:center;justify-content:center;"><svg width="16" height="16" fill="none" stroke="#000" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 002-2V2M7 2v20M21 15V2a5 5 0 00-5 5v6h3.5v7"/></svg></span></div>',
        className: '', iconSize: [34,34], iconAnchor: [17,34]
    })}).addTo(adminMapInstance).bindPopup('<b>E.U.T Snack House</b>');

    L.marker(riderPos, { icon: L.divIcon({
        html: '<div style="background:#8b5cf6;width:40px;height:40px;border-radius:50%;border:3px solid #fff;display:flex;align-items:center;justify-content:center;box-shadow:0 0 12px rgba(139,92,246,.7);"><svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19c-3.866 0-7-1.343-7-3V8m7 11c3.866 0 7-1.343 7-3V8M5 8c0-1.657 3.134-3 7-3s7 1.343 7 3"/></svg></div>',
        className: '', iconSize: [40,40], iconAnchor: [20,20]
    })}).addTo(adminMapInstance).bindPopup('<b>Rider: ' + escHtml(o.rider || 'Rider') + '</b>');

    if (custPos) {
        L.marker(custPos, { icon: L.divIcon({
            html: '<div style="background:#ef4444;width:34px;height:34px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #b91c1c;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.5);"><span style="transform:rotate(45deg);display:flex;align-items:center;justify-content:center;"><svg width="14" height="14" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22" stroke-linecap="round" stroke-linejoin="round"/></svg></span></div>',
            className: '', iconSize: [34,34], iconAnchor: [17,34]
        })}).addTo(adminMapInstance).bindPopup('<b>' + escHtml(o.customer) + '</b>');
    }

    var dest = custPos || [ADMIN_RESTAURANT[0]+.005, ADMIN_RESTAURANT[1]+.005];
    adminMapInstance.fitBounds([riderPos, dest], { padding: [40,40] });

    var route = await fetchAdminRoute(riderPos, dest);
    if (route && route.length) {
        var line = L.polyline(route, { color:'#8b5cf6', weight:5, opacity:1 }).addTo(adminMapInstance);
        adminMapInstance.fitBounds(line.getBounds(), { padding: [35,35] });
    } else {
        L.polyline([riderPos, dest], { color:'#8b5cf6', weight:3, opacity:.7, dashArray:'8 5' }).addTo(adminMapInstance);
    }
}

function kitchenAutoPrint(receiptUrl) {
    var old = document.getElementById('adminPickupPrintFrame');
    if (old) old.remove();
    var iframe = document.createElement('iframe');
    iframe.id = 'adminPickupPrintFrame';
    iframe.src = receiptUrl;
    iframe.style.cssText = 'position:fixed;left:-9999px;top:-9999px;width:1px;height:1px;border:none;opacity:0;';
    document.body.appendChild(iframe);
    iframe.onload = function() {
        try { iframe.contentWindow.focus(); iframe.contentWindow.print(); } catch(e) { console.warn('Auto-print pickup receipt failed:', e); }
        setTimeout(function() { iframe.remove(); }, 30000);
    };
}

function printReceipt(receiptUrl) {
    const w    = 220; // 200px content + padding buffer
    const h    = 800;
    const left = Math.round((screen.width  - w) / 2);
    const top  = Math.round((screen.height - h) / 2);

    const win = window.open(
        receiptUrl,
        'receipt_print',
        `width=${w},height=${h},left=${left},top=${top},toolbar=0,scrollbars=0,status=0,menubar=0,location=0`
    );
    if (!win) {
        window.open(receiptUrl, '_blank');
    }
}

// -- Boot -------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    // Set initial filter from URL if present
    var urlParams = new URLSearchParams(window.location.search);
    var urlStatus = urlParams.get('status') || '';
    if (urlStatus) {
        activeFilter = urlStatus;
        var sel = document.getElementById('statusFilter');
        if (sel) sel.value = urlStatus;
        var clr = document.getElementById('clearFilter');
        if (clr) clr.style.display = 'inline';
    }

    // Always start polling so orders refresh even without WebSockets
    startPolling();

    // Echo: real-time nudge � fetch fresh data immediately on any order event
    if (window.Echo) {
        window.Echo.private('admin.orders')
            .listen('.order.updated', function(order) {
                // If rider just picked up — print receipt immediately via Echo
                if (order.status === 'out_for_delivery' && !printedPickupIds.has(order.id)) {
                    printedPickupIds.add(order.id);
                    var receiptUrl = '/chef/orders/' + order.id + '/takeout-slip';
                    setTimeout(function() { kitchenAutoPrint(receiptUrl); }, 300);
                }
                fetchOrders(); // pull full fresh snapshot from server
            });
    }
});

function computeStatusCounts(orders) {
    var counts = { pending: 0, preparing: 0, out: 0, delivered: 0, cancelled: 0, today: 0, revenue_today: 0 };
    var todayStr = new Date().toDateString();
    orders.forEach(function(o) {
        if (o.status === 'pending') counts.pending++;
        else if (o.status === 'accepted' || o.status === 'preparing') counts.preparing++;
        else if (o.status === 'rider_assigned' || o.status === 'out_for_delivery') counts.out++;
        else if (o.status === 'delivered') {
            counts.delivered++;
            if (o.delivered_at && new Date(o.delivered_at).toDateString() === todayStr) counts.revenue_today += (o.total || 0);
        }
        else if (o.status === 'cancelled') counts.cancelled++;
        if (o.date && new Date(o.date).toDateString() === todayStr) counts.today++;
    });
    return counts;
}

// ── Inline qty stepper in Manage modal ───────────────────────────────────
// Called by the − / + buttons next to each item in openManageModal.
// Patches quantity instantly; updates modal display without closing it.
var _admQtyInFlight = {}; // orderId_itemId → true while request pending

async function admInlineQty(btn) {
    var orderId = parseInt(btn.dataset.order);
    var itemId  = parseInt(btn.dataset.item);
    var dir     = parseInt(btn.dataset.dir); // -1 or +1
    var key     = orderId + '_' + itemId;

    if (_admQtyInFlight[key]) return; // debounce

    // Read current displayed qty
    var display = document.querySelector('.adm-item-qty-display[data-item="' + itemId + '"]');
    if (!display) return;
    var currentQty = parseInt(display.textContent) || 1;
    var newQty     = currentQty + dir;
    if (newQty < 0) return;

    // Optimistic UI update
    display.textContent = newQty === 0 ? '0' : newQty;
    btn.disabled = true;

    _admQtyInFlight[key] = true;
    try {
        var res  = await fetch('/admin/orders/' + orderId + '/items/' + itemId, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ qty: newQty }),
        });
        var data = await res.json();

        if (!data.success) {
            // Roll back optimistic update
            display.textContent = currentQty;
            alert(data.message || 'Could not update quantity.');
        } else {
            // Update ORDERS_MAP with fresh items + total from response
            if (ORDERS_MAP[orderId]) {
                if (data.items)     ORDERS_MAP[orderId].items = data.items;
                if (data.new_total) ORDERS_MAP[orderId].total = data.new_total;
            }

            if (newQty === 0) {
                // Item removed — re-render the whole modal
                await fetchOrders();
                var o = ORDERS_MAP[orderId];
                if (o && o.items && o.items.length > 0) {
                    openManageModal(orderId);
                } else {
                    closeModal('manageModal');
                }
                return;
            }

            // Update the subtotal displayed next to the item
            var itemData = (data.items || []).find(function(i){ return i.id === itemId; });
            if (itemData) {
                var subtotalEl = display.closest('div[style*="flex-shrink:0"]')
                    ?.previousElementSibling;
                if (subtotalEl && subtotalEl.tagName === 'SPAN') {
                    subtotalEl.textContent = '₱' + Number(itemData.subtotal).toLocaleString();
                }
            }

            // Update the grand total in the modal footer
            if (data.new_total) {
                var totalEl = document.querySelector('#mmBody .modal-total-value');
                if (totalEl) totalEl.textContent = '₱' + Number(data.new_total).toLocaleString();
                // Also patch the bold Total row (it's the last bold flex row in the items section)
                var totalRows = document.querySelectorAll('#mmBody div[style*="justify-content:space-between"]');
                totalRows.forEach(function(row) {
                    var label = row.querySelector('span:first-child');
                    if (label && label.textContent.trim() === 'Total') {
                        var valEl = row.querySelector('span:last-child');
                        if (valEl) valEl.textContent = '₱' + Number(data.new_total).toLocaleString();
                    }
                });
            }

            // Refresh the grid card in background without closing the modal
            fetchOrders();
        }
    } catch(e) {
        display.textContent = currentQty;
        alert('Network error. Please try again.');
    } finally {
        btn.disabled = false;
        delete _admQtyInFlight[key];
    }
}

// ── Mark Ready (admin card button) ────────────────────────────────────────
async function adminMarkReady(orderId, btn) {
    var orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '…';
    try {
        var res  = await fetch('/admin/orders/' + orderId + '/ready', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        var data = await res.json();
        if (data.success) {
            await fetchOrders();
        } else {
            alert(data.message || 'Could not mark ready.');
            btn.disabled = false; btn.innerHTML = orig;
        }
    } catch(e) {
        alert('Network error. Please try again.');
        btn.disabled = false; btn.innerHTML = orig;
    }
}

// ── Remove Item modal (admin) ─────────────────────────────────────────────
var _adm_orderId = null, _adm_itemId = null, _adm_qty = 1, _adm_max = 1;

function openAdminRemoveItemModal(orderId, preItemId, preItemQty) {
    var o = ORDERS_MAP[orderId];
    if (!o) return;
    _adm_orderId = orderId; _adm_itemId = null; _adm_qty = 1; _adm_max = 1;

    document.getElementById('admRmOrderLabel').textContent =
        (o.order_number || '') + (o.table_number ? ' · Table ' + o.table_number : '');

    var listHtml = '';
    (o.items || []).forEach(function(item) {
        if (!item.id) return;
        var isPreSelected = preItemId && item.id === preItemId;
        var selStyle = isPreSelected
            ? 'border-color:rgba(239,68,68,.6);background:rgba(239,68,68,.1);'
            : '';
        var checkStyle = isPreSelected
            ? 'background:#ef4444;border-color:#ef4444;color:#fff;'
            : '';
        listHtml +=
            '<div class="adm-rm-row" onclick="admSelectItem(this,' + item.id + ',' + item.qty + ')" style="display:flex;align-items:center;gap:.7rem;padding:.55rem .7rem;border-radius:.65rem;border:1px solid var(--border-divider);background:rgba(255,255,255,.03);cursor:pointer;margin-bottom:.35rem;transition:border-color .15s,background .15s;' + selStyle + '">' +
            '<span style="flex:1;font-size:.85rem;font-weight:700;color:var(--text-strong);">' + escHtml(item.name) + '</span>' +
            '<span style="font-size:.75rem;font-weight:800;color:#facc15;">' + item.qty + '×</span>' +
            '<span class="adm-rm-check" style="width:1.25rem;height:1.25rem;border-radius:50%;border:2px solid rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:.65rem;transition:all .15s;flex-shrink:0;' + checkStyle + '">✓</span>' +
            '</div>';
        if (isPreSelected) {
            _adm_itemId = item.id;
            _adm_max    = item.qty;
            _adm_qty    = preItemQty !== undefined ? preItemQty : item.qty;
        }
    });
    document.getElementById('admRmItemList').innerHTML = listHtml || '<p style="color:var(--text-muted);font-size:.8rem;">No items found.</p>';

    var cb = document.getElementById('admRmConfirmBtn');
    if (_adm_itemId) {
        document.getElementById('admRmQtyRow').style.display = 'block';
        document.getElementById('admRmQtyDisplay').textContent = _adm_qty;
        document.getElementById('admRmQtyMax').textContent = _adm_max;
        document.getElementById('admRmQtyDec').disabled = _adm_qty <= 1;
        document.getElementById('admRmQtyInc').disabled = _adm_qty >= _adm_max;
        var selName = (o.items.find(function(i){ return i.id === _adm_itemId; }) || {}).name || 'item';
        _admUpdateConfirm(selName);
    } else {
        document.getElementById('admRmQtyRow').style.display = 'none';
        cb.disabled = true; cb.textContent = 'Remove Selected Item';
    }

    // Close manage modal if open, then show remove modal
    document.getElementById('manageModal').style.display = 'none';
    document.getElementById('adminRemoveModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function admSelectItem(el, itemId, qty) {
    document.querySelectorAll('#admRmItemList .adm-rm-row').forEach(function(r) {
        r.style.borderColor = 'var(--border-divider)';
        r.style.background  = 'rgba(255,255,255,.03)';
        r.querySelector('.adm-rm-check').style.background  = 'transparent';
        r.querySelector('.adm-rm-check').style.borderColor = 'rgba(255,255,255,.2)';
        r.querySelector('.adm-rm-check').style.color       = 'transparent';
    });
    el.style.borderColor = 'rgba(239,68,68,.6)';
    el.style.background  = 'rgba(239,68,68,.1)';
    el.querySelector('.adm-rm-check').style.background  = '#ef4444';
    el.querySelector('.adm-rm-check').style.borderColor = '#ef4444';
    el.querySelector('.adm-rm-check').style.color       = '#fff';

    _adm_itemId = itemId; _adm_max = qty; _adm_qty = qty;
    document.getElementById('admRmQtyRow').style.display = 'block';
    document.getElementById('admRmQtyDisplay').textContent = _adm_qty;
    document.getElementById('admRmQtyMax').textContent = _adm_max;
    document.getElementById('admRmQtyDec').disabled = _adm_qty <= 1;
    document.getElementById('admRmQtyInc').disabled = _adm_qty >= _adm_max;

    var name = el.querySelector('span:first-child').textContent || 'item';
    _admUpdateConfirm(name);
}

function admAdjustQty(d) {
    _adm_qty = Math.max(1, Math.min(_adm_max, _adm_qty + d));
    document.getElementById('admRmQtyDisplay').textContent = _adm_qty;
    document.getElementById('admRmQtyDec').disabled = _adm_qty <= 1;
    document.getElementById('admRmQtyInc').disabled = _adm_qty >= _adm_max;
    var sel = document.querySelector('#admRmItemList .adm-rm-row[style*="rgba(239"]');
    var name = sel ? sel.querySelector('span:first-child').textContent : 'item';
    _admUpdateConfirm(name);
}

function _admUpdateConfirm(name) {
    var cb = document.getElementById('admRmConfirmBtn');
    cb.disabled = false;
    cb.textContent = _adm_qty >= _adm_max
        ? 'Remove all "' + name + '"'
        : 'Remove ' + _adm_qty + '× of ' + _adm_max + '× "' + name + '"';
}

function closeAdminRemoveModal(e) {
    if (e && e.target !== document.getElementById('adminRemoveModal')) return;
    document.getElementById('adminRemoveModal').style.display = 'none';
    document.body.style.overflow = '';
    _adm_orderId = _adm_itemId = null; _adm_qty = _adm_max = 1;
}

async function admConfirmRemove() {
    if (!_adm_orderId || !_adm_itemId) return;
    var cb = document.getElementById('admRmConfirmBtn');
    cb.disabled = true; cb.textContent = 'Removing…';
    try {
        var res = await fetch('/admin/orders/' + _adm_orderId + '/items/' + _adm_itemId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ qty: _adm_qty }),
        });
        var data = await res.json();
        if (!data.success) {
            alert(data.message || 'Could not remove item.');
            cb.disabled = false; cb.textContent = 'Remove Selected Item';
            return;
        }
        var savedOrderId = _adm_orderId;
        document.getElementById('adminRemoveModal').style.display = 'none';
        document.body.style.overflow = '';
        _adm_orderId = _adm_itemId = null; _adm_qty = _adm_max = 1;
        await fetchOrders();
        // Re-open the manage modal so admin can see the updated order
        if (data.items_left > 0 && savedOrderId) {
            openManageModal(savedOrderId);
        }
    } catch(err) {
        alert('Network error. Please try again.');
        cb.disabled = false; cb.textContent = 'Remove Selected Item';
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('adminRemoveModal').style.display !== 'none') {
        closeAdminRemoveModal();
    }
});

</script>

{{-- ── Admin Remove Item Modal ───────────────────────────────────────── --}}
<div id="adminRemoveModal" onclick="closeAdminRemoveModal(event)"
    style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.78);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:1rem;">
    <div style="background:var(--bg-card);border:1px solid var(--border-card);border-radius:1.1rem;width:100%;max-width:420px;box-shadow:0 24px 60px rgba(0,0,0,.7);overflow:hidden;">
        <div style="padding:1rem 1.2rem .8rem;border-bottom:1px solid var(--border-divider);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:.95rem;font-weight:800;color:var(--text-strong);">Remove Item from Order</div>
                <div id="admRmOrderLabel" style="font-size:.72rem;color:var(--text-muted);margin-top:.1rem;">—</div>
            </div>
            <button onclick="closeAdminRemoveModal()" class="modal-close">✕</button>
        </div>
        <div id="admRmItemList" style="padding:.75rem 1.2rem;max-height:50vh;overflow-y:auto;"></div>
        <div id="admRmQtyRow" style="display:none;padding:.6rem 1.2rem .4rem;border-top:1px solid var(--border-divider);">
            <div style="font-size:.72rem;color:var(--text-muted);margin-bottom:.45rem;">How many to cancel?</div>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <button type="button" id="admRmQtyDec" onclick="admAdjustQty(-1)"
                    style="width:2.1rem;height:2.1rem;border-radius:.5rem;border:1px solid var(--border-divider);background:rgba(255,255,255,.06);color:var(--text-strong);font-size:1.1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">−</button>
                <span id="admRmQtyDisplay" style="font-size:1.3rem;font-weight:800;color:#facc15;min-width:2rem;text-align:center;">1</span>
                <button type="button" id="admRmQtyInc" onclick="admAdjustQty(1)"
                    style="width:2.1rem;height:2.1rem;border-radius:.5rem;border:1px solid var(--border-divider);background:rgba(255,255,255,.06);color:var(--text-strong);font-size:1.1rem;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">+</button>
                <span style="font-size:.78rem;color:var(--text-muted);flex:1;">of <strong id="admRmQtyMax" style="color:var(--text-strong);">1</strong> total</span>
            </div>
        </div>
        <div style="padding:.75rem 1.2rem 1rem;border-top:1px solid var(--border-divider);display:flex;gap:.5rem;">
            <button id="admRmConfirmBtn" onclick="admConfirmRemove()" disabled
                style="flex:1;padding:.55rem .75rem;border-radius:.5rem;background:rgba(239,68,68,.15);color:#ef4444;border:1px solid rgba(239,68,68,.3);font-size:.8rem;font-weight:700;cursor:pointer;">
                Remove Selected Item
            </button>
            <button onclick="closeAdminRemoveModal()"
                style="flex:0 0 auto;padding:.55rem 1rem;border-radius:.5rem;background:rgba(255,255,255,.06);color:var(--text-muted);border:1px solid var(--border-divider);font-size:.8rem;cursor:pointer;">
                Keep All
            </button>
        </div>
    </div>
</div>

{{-- ── Payment Collection Modal (Dine-in Complete Table) ── --}}
<div id="paymentModal" class="modal-backdrop" style="display:none;z-index:9999;">
    <div class="modal-box" style="max-width:380px;width:calc(100% - 32px);">
        <div class="modal-header">
            <div>
                <h3 class="modal-title" style="font-size:1rem;">💵 Collect Payment</h3>
                <p style="font-size:.72rem;color:var(--text-muted);margin:.1rem 0 0;" id="pmTableLabel">Table —</p>
            </div>
            <button class="modal-close" onclick="closePaymentModal()">✕</button>
        </div>
        <div style="padding:1.25rem 1.4rem;">

            {{-- Total --}}
            <div style="background:rgba(250,204,21,.07);border:1px solid rgba(250,204,21,.2);border-radius:.75rem;padding:1rem 1.25rem;margin-bottom:1.25rem;text-align:center;">
                <p style="font-size:.72rem;color:var(--text-muted);margin:0 0 .25rem;text-transform:uppercase;letter-spacing:.06em;">Total Amount Due</p>
                <p style="font-size:2rem;font-weight:800;color:#facc15;margin:0;" id="pmTotal">₱0</p>
            </div>

            {{-- Cash received input --}}
            <div style="margin-bottom:1rem;">
                <label style="font-size:.72rem;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;display:block;margin-bottom:.4rem;">Cash</label>
                <div style="position:relative;">
                    <span style="position:absolute;left:.875rem;top:50%;transform:translateY(-50%);font-size:.95rem;color:var(--text-muted);font-weight:700;">₱</span>
                    <input type="number" id="pmCashInput" min="0" step="1"
                        placeholder="0"
                        oninput="updateChange()"
                        style="width:100%;padding:.75rem .875rem .75rem 2rem;background:var(--bg-input,rgba(255,255,255,.06));border:1.5px solid var(--border-input,rgba(255,255,255,.12));border-radius:.625rem;font-size:1.1rem;font-weight:700;color:var(--text-strong);outline:none;">
                </div>
                {{-- Quick cash buttons --}}
                <div id="pmQuickBtns" style="display:flex;gap:.4rem;flex-wrap:wrap;margin-top:.6rem;"></div>
            </div>

            {{-- Change --}}
            <div style="background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.2);border-radius:.625rem;padding:.75rem 1rem;display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
                <span style="font-size:.8rem;font-weight:600;color:#10b981;">Change</span>
                <span style="font-size:1.25rem;font-weight:800;color:#10b981;" id="pmChange">₱0</span>
            </div>

            {{-- Actions --}}
            <button type="button" id="pmConfirmBtn" onclick="confirmPayment()"
                style="width:100%;padding:.85rem;border-radius:.75rem;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-size:.9rem;font-weight:700;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.5rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Complete &amp; Print Receipt
            </button>
            <button type="button" onclick="closePaymentModal()" style="width:100%;margin-top:.5rem;padding:.65rem;border-radius:.75rem;background:transparent;border:1px solid var(--border-input);color:var(--text-muted);font-size:.8rem;cursor:pointer;">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
let _pmOrderId   = null;
let _pmTotal     = 0;
let _pmOrigBtn   = null;
let _pmType      = 'complete-table'; // 'complete-table' or 'serve-dine-in'

function openPaymentModal(orderId, grandTotal, tableNum, origBtn, type) {
    _pmOrderId  = orderId;
    _pmTotal    = parseFloat(grandTotal);
    _pmOrigBtn  = origBtn;
    _pmType     = type || 'complete-table';

    document.getElementById('pmTableLabel').textContent  = 'Table ' + tableNum;
    document.getElementById('pmTotal').textContent       = '₱' + _pmTotal.toLocaleString();
    document.getElementById('pmCashInput').value         = '';
    document.getElementById('pmChange').textContent      = '₱0';

    // Quick cash buttons: round up to nearest 50/100/200/500
    const btns = [50, 100, 200, 500, 1000].filter(v => v >= _pmTotal);
    // Also include exact and next round numbers
    const rounded = [
        Math.ceil(_pmTotal / 50)  * 50,
        Math.ceil(_pmTotal / 100) * 100,
        Math.ceil(_pmTotal / 500) * 500,
    ];
    const quickAmounts = [...new Set([...rounded, ...btns])].sort((a,b)=>a-b).slice(0, 5);
    const qWrap = document.getElementById('pmQuickBtns');
    qWrap.innerHTML = quickAmounts.map(v =>
        `<button type="button" onclick="setCash(${v})"
            style="flex:1;min-width:60px;padding:.35rem .5rem;border-radius:.5rem;background:rgba(255,255,255,.06);
                   border:1px solid rgba(255,255,255,.1);color:var(--text-body);font-size:.75rem;font-weight:600;cursor:pointer;">
            ₱${v.toLocaleString()}
        </button>`
    ).join('');

    document.getElementById('paymentModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('pmCashInput').focus(), 150);
}

function setCash(amount) {
    document.getElementById('pmCashInput').value = amount;
    updateChange();
}

function updateChange() {
    const cash   = parseFloat(document.getElementById('pmCashInput').value) || 0;
    const change = cash - _pmTotal;
    const el     = document.getElementById('pmChange');
    if (cash <= 0) {
        el.textContent = '₱0';
        el.style.color = '#10b981';
    } else if (change < 0) {
        el.textContent = '−₱' + Math.abs(change).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
        el.style.color = '#ef4444';
    } else {
        el.textContent = '₱' + change.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
        el.style.color = '#10b981';
    }
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    document.body.style.overflow = '';
    if (_pmOrigBtn) { _pmOrigBtn.disabled = false; _pmOrigBtn.innerHTML = _pmOrigBtn._origHtml || _pmOrigBtn.innerHTML; }
    _pmOrderId = null; _pmOrigBtn = null;
    // Always reset the confirm button so it's ready for next use
    var confirmBtn = document.getElementById('pmConfirmBtn');
    if (confirmBtn) {
        confirmBtn.disabled = false;
        confirmBtn.innerHTML = '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Complete &amp; Print Receipt';
    }
    // Also reset cash input
    var cashInput = document.getElementById('pmCashInput');
    if (cashInput) cashInput.value = '';
}

async function confirmPayment() {
    const cash = parseFloat(document.getElementById('pmCashInput').value) || 0;
    if (cash > 0 && cash < _pmTotal) {
        alert('Cash received is less than the total amount.');
        return;
    }

    const btn = document.getElementById('pmConfirmBtn');
    btn.disabled = true;
    btn.innerHTML = '⏳ Processing…';

    try {
        let url, method = 'POST', body = { cash_received: cash };

        if (_pmType === 'serve-dine-in') {
            // Individual dine-in order — mark single order as delivered
            url = '{{ route("admin.orders.status", ":id") }}'.replace(':id', _pmOrderId);
            method = 'PATCH';
            body = { status: 'delivered', cash_received: cash };
        } else {
            // Complete entire table session
            url = '{{ route("admin.orders.complete-table", ":id") }}'.replace(':id', _pmOrderId);
        }

        const res = await fetch(url, {
            method: method,
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.success) {
            closePaymentModal();
            if (data.receipt_url) printReceipt(data.receipt_url);
            await fetchOrders();
        } else {
            alert(data.message || 'Failed to complete.');
            btn.disabled = false;
            btn.innerHTML = '✓ Complete & Print Receipt';
        }
    } catch(e) {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.innerHTML = '✓ Complete & Print Receipt';
    }
}

// Close on backdrop click
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentModal();
});
</script>

@endsection