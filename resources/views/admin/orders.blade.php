@extends('admin.layout')
@section('title', 'Orders')

{{-- Leaflet for rider live map in modal --}}
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
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
                <option value="delivered">Delivered</option>
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

    <table class="admin-table">
        <thead>
            <tr>
                <th>Order #</th><th>Customer</th><th>Items</th>
                <th>Total</th><th>Status</th><th>Placed</th><th>Actions</th>
            </tr>
        </thead>
        <tbody id="ordersTableBody">
            <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:3rem;">Loading�</td></tr>
        </tbody>
    </table>
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

// -- Status pipeline for modal
// Admin: Accept (? auto preparing) | Dispatch rider (after chef marks ready) | Cancel
// Chef: Mark Ready (on Kitchen Dashboard)
// Rider: Picked Up | Delivered
var STATUS_PIPELINE = {
    pending:          { label:'Pending',        color:'#f59e0b', next:'accepted',  nextLabel:'Accept Order',    btnClass:'btn-success' },
    accepted:         { label:'Accepted',       color:'#3b82f6', next:null,        nextLabel:null,              btnClass:'', chefAction:true },
    preparing:        { label:'Preparing',      color:'#dc2626', next:null,        nextLabel:null,              btnClass:'', chefAction:true },
    rider_assigned:   { label:'Rider Assigned', color:'#8b5cf6', next:null,        nextLabel:null,              btnClass:'', riderAction:true },
    out_for_delivery: { label:'On the Way',     color:'#8b5cf6', next:null,        nextLabel:null,              btnClass:'' },
    delivered:        { label:'Delivered',      color:'#10b981', next:null,        nextLabel:null,              btnClass:'' },
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

    try {
        var params = [];
        if (activeFilter) params.push('status=' + activeFilter);
        if (dateFilter === 'all')      params.push('all=1');
        if (dateFilter === 'archived') params.push('archived=1');
        var url = POLL_URL + (params.length ? '?' + params.join('&') : '');
        var res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN } });
        if (!res.ok) throw new Error('HTTP ' + res.status);
        var data = await res.json();

        ORDERS_MAP = {};
        data.orders.forEach(function(o) { ORDERS_MAP[o.id] = o; });
        RIDERS = data.riders || [];

        // On the very first poll (page load), seed printedPickupIds with all existing
        // out_for_delivery/delivered orders so we never re-print on refresh.
        // On subsequent polls, any order newly appearing as out_for_delivery is a real
        // transition and should trigger a print.
        if (!window._adminFirstPollDone) {
            data.orders.forEach(function(o) {
                if (['out_for_delivery', 'delivered'].includes(o.status)) {
                    printedPickupIds.add(o.id);
                }
            });
            window._adminFirstPollDone = true;
        } else {
            // Subsequent poll � only print for orders that just transitioned
            data.orders.forEach(function(o) {
                if (o.status === 'out_for_delivery' && !printedPickupIds.has(o.id)) {
                    printedPickupIds.add(o.id);
                    var receiptUrl = '/chef/orders/' + o.id + '/pickup-slip';
                    setTimeout(function() { kitchenAutoPrint(receiptUrl); }, 400);
                }
            });
        }

        renderTable(data.orders);

        if (dot)   dot.style.background   = '#10b981'; // green = ok
        if (label) label.textContent = 'Live';
    } catch (e) {
        console.warn('Poll error:', e);
        logError('Poll', 'Failed to fetch orders', e.message);
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
    var btnMap = { today:'btnToday', all:'btnAll', archived:'btnArchived' };
    Object.keys(btnMap).forEach(function(m) {
        var btn = document.getElementById(btnMap[m]);
        if (btn) btn.className = m === mode ? 'btn-primary' : 'btn-ghost';
    });
    fetchOrders();
}

// -- Render table rows ------------------------------------
// Flow: Admin ACCEPTS (auto?preparing) ? Chef MARKS READY ? Admin DISPATCHES rider ? Rider PICKS UP ? Rider DELIVERS
var INLINE_ACTIONS = {
    pending: { label:'Accept', icon:'check', btnClass:'btn-success', type:'accept' },
    // preparing: handled dynamically � 'Dispatch' only when chef has marked ready (picked_up_at set on order data)
};

function renderTable(orders) {
    var tbody = document.getElementById('ordersTableBody');
    if (!tbody) return;

    var countEl = document.getElementById('orderCount');
    if (countEl) countEl.textContent = orders.length + ' order(s)';

    if (!orders.length) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:3rem;">No orders found.</td></tr>';
        return;
    }

    var html = '';
    orders.forEach(function(o) {
        var sc = STATUS_COLOR_MAP[o.status] || STATUS_COLOR_MAP['pending'];
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

        // Inline action button
        var actionBtn = '';
        var act = INLINE_ACTIONS[o.status];

        // preparing: behaviour depends on order type and whether kitchen marked ready
        if (o.status === 'preparing') {
            if (!o.prepared_at) {
                // Kitchen still cooking � show pulsing badge, no action button
                act = null;
                actionBtn = '<span style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .7rem;border-radius:99px;font-size:.68rem;font-weight:700;background:rgba(220,38,38,.1);color:#f87171;border:1px solid rgba(220,38,38,.25);white-space:nowrap;" title="Chef is cooking this order">' +
                    '<span style="width:6px;height:6px;border-radius:50%;background:#f87171;flex-shrink:0;animation:blink 1.2s infinite;display:inline-block;"></span>' +
                    'Chef Cooking' +
                    '</span>';
            } else if (o.order_type === 'delivery') {
                // Delivery + kitchen ready ? admin dispatches a rider
                act = { label:'Dispatch Rider', icon:'bike', btnClass:'btn-warning', type:'dispatch' };
            } else {
                // Dine-in / pickup + kitchen ready ? admin completes the order
                act = { label: o.order_type === 'pickup' ? 'Picked Up' : 'Complete', icon: o.order_type === 'pickup' ? 'package-check' : 'circle-check', btnClass:'btn-success', type:'status', next:'delivered' };
            }
        }

        // Dine-in/pickup accepted: kitchen hasn't started yet � admin waits, no Complete button
        // (chef must Start Cooking ? Mark Ready before admin can complete)

        if (act) {
            actionBtn = '<button type="button" class="' + act.btnClass + '" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .75rem;white-space:nowrap;" ' +
                'onclick="quickAction(' + o.id + ',\'' + act.type + '\',\'' + (act.next || '') + '\',this)">' +
                '<i data-lucide="' + act.icon + '" style="width:.75rem;height:.75rem;stroke-width:2.5;flex-shrink:0;"></i>' +
                act.label +
                '</button>';
        }

        // rider_assigned ? rider-owned
        if (o.status === 'rider_assigned') {
            var riderName = o.rider ? escHtml(o.rider) : 'Rider';
            actionBtn = '<span style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .7rem;border-radius:99px;font-size:.68rem;font-weight:700;background:rgba(139,92,246,.1);color:#a78bfa;border:1px solid rgba(139,92,246,.25);white-space:nowrap;" title="' + riderName + ' must click Picked Up on their dashboard">' +
                '<span style="width:6px;height:6px;border-radius:50%;background:#a78bfa;flex-shrink:0;animation:blink 1.2s infinite;display:inline-block;"></span>' +
                'Awaiting Rider Pickup' +
                '</span>';
        }

        html +=
            '<tr id="order-row-' + o.id + '">' +
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
                        ? '<p style="font-size:.7rem;color:#facc15;font-weight:700;margin:0 0 1px;">?? Table ' + escHtml(o.table_number) + '</p>'
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
                '<button class="btn-ghost" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .55rem;" onclick="openManageModal(' + o.id + ')" title="Details">' +
                    '<i data-lucide="settings-2" style="width:.75rem;height:.75rem;stroke-width:2;"></i>' +
                '</button>' +
                (o.order_type === 'delivery'
                    ? '<button class="btn-ghost" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .55rem;color:#60a5fa;" onclick="adminPrintPickupSlip(' + o.id + ')" title="Print Pickup Slip">' +
                        '<i data-lucide="printer" style="width:.75rem;height:.75rem;stroke-width:2;"></i>' +
                      '</button>'
                    : '') +
                ((['delivered','cancelled'].includes(o.status))
                    ? '<button class="btn-ghost" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .55rem;color:' + (o.is_archived ? '#f59e0b' : 'var(--text-muted)') + ';" onclick="archiveOrder(' + o.id + ',this)" title="' + (o.is_archived ? 'Restore' : 'Archive') + '">' +
                        '<i data-lucide="' + (o.is_archived ? 'archive-restore' : 'archive') + '" style="width:.75rem;height:.75rem;stroke-width:2;"></i>' +
                      '</button>'
                    : '') +
                (o.is_archived
                    ? '<button class="btn-icon-delete" style="font-size:.72rem;padding:.35rem .55rem;" onclick="deleteOrder(' + o.id + ',\'' + escHtml(o.order_number) + '\',this)" title="Delete permanently">' +
                        '<i data-lucide="trash-2" style="width:.75rem;height:.75rem;stroke-width:2;"></i>' +
                      '</button>'
                    : '') +
                '</div>' +
            '</td>' +
            '</tr>';

    });

    tbody.innerHTML = html;
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// -- Print pickup slip (delivery orders only) --------------
function adminPrintPickupSlip(orderId) {
    var old = document.getElementById('_adminPickupPrintFrame');
    if (old) old.remove();

    var iframe = document.createElement('iframe');
    iframe.id = '_adminPickupPrintFrame';
    iframe.style.cssText = 'position:fixed;top:-9999px;left:-9999px;width:210mm;height:297mm;border:none;opacity:0;pointer-events:none;';
    document.body.appendChild(iframe);
    iframe.src = '/chef/orders/' + orderId + '/pickup-slip';

    iframe.onload = function() {
        setTimeout(function() {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch(e) { console.warn('Print failed', e); }
            setTimeout(function() { try { iframe.remove(); } catch(e) {} }, 60000);
        }, 500);
    };
    iframe.onerror = function() { iframe.remove(); };
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

        } else if (type === 'dispatch') {
            // Open the manage modal so the admin can pick a rider
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            openManageModal(orderId);
            return;

        } else if (type === 'auto-assign') {
            if (!RIDERS.length) {
                logError('AutoAssign', '[Order #' + orderId + '] No riders in the system.');
                alert('No riders available. Please add a rider first.');
                btn.disabled = false; btn.innerHTML = originalHtml;
                return;
            }
            var rider = RIDERS[0]; // first free rider, or least-busy if all occupied
            url    = '{{ route("admin.orders.assign-rider", ":id") }}'.replace(':id', orderId);
            method = 'POST';
            body   = JSON.stringify({ rider_id: rider.id });

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

async function archiveOrder(orderId, btn) {
    var orig = btn.innerHTML;
    btn.disabled = true;
    try {
        var url = '/admin/orders/' + orderId + '/archive';
        var res = await fetch(url, { method:'PATCH', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Accept':'application/json','Content-Type':'application/json'} });
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
                '<span style="font-size:.8rem;color:var(--text-body);font-weight:600;flex-shrink:0;margin-left:.5rem;">&#x20B1;' + Number(item.subtotal).toLocaleString() + '</span>' +
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
                    '<p style="font-size:.8rem;font-weight:700;color:#f59e0b;margin:0 0 .2rem;">Chef is Cooking</p>' +
                    '<p style="font-size:.72rem;color:var(--text-muted);margin:0;line-height:1.5;">' +
                        'Waiting for the chef to <strong style="color:#fcd34d;">mark this order as ready</strong> from the Kitchen Dashboard.' +
                    '</p>' +
                '</div>' +
            '</div>';
    }

    if (o.status === 'preparing' && sp.dispatchReady) {
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
                        '<i data-lucide="bike" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Assign Rider' +
                    '</button>' +
                '</div>';
        } else {
            actionsHtml +=
                '<div style="margin-top:.5rem;padding:.6rem .75rem;border-radius:.5rem;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);font-size:.78rem;color:#ef4444;">' +
                    '<div style="display:flex;align-items:center;gap:.4rem;"><svg width="13" height="13" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg> No riders found. Add a rider first.</div>' +
                '</div>';
        }
    }

    if (['pending','accepted','preparing'].includes(o.status)) {
        actionsHtml +=
            '<form method="POST" action="' + statusRoute.replace(':id', o.id) + '" style="margin-top:.25rem;" onsubmit="return confirm(\'Cancel this order?\')">' +
                '<input type="hidden" name="_token" value="' + CSRF_TOKEN + '">' +
                '<input type="hidden" name="_method" value="PATCH">' +
                '<input type="hidden" name="status" value="cancelled">' +
                '<button type="submit" class="btn-danger" style="font-size:.8rem;display:inline-flex;align-items:center;gap:.3rem;width:100%;justify-content:center;">' +
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
                // If rider just picked up � print receipt immediately via Echo
                if (order.status === 'out_for_delivery' && !printedPickupIds.has(order.id)) {
                    printedPickupIds.add(order.id);
                    var receiptUrl = '/chef/orders/' + order.id + '/pickup-slip';
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

</script>

@endsection
