@extends('admin.layout')
@section('title', 'Audit Logs')

@section('content')

@php
    $livePolling = ! request()->hasAny(['search', 'action', 'user_id', 'model', 'date_from', 'date_to'])
        && $logs->onFirstPage();
@endphp

{{-- ── PAGE HEADER ─────────────────────────────────────────── --}}
<div class="page-header" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(234,179,8,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="scroll-text" style="width:1.2rem;height:1.2rem;color:#eab308;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;">Audit Logs</h1>
            <p style="margin:0;">Complete trail of every action taken in the system.</p>
        </div>
    </div>
    <span style="font-size:.8rem;color:var(--text-muted);display:flex;align-items:center;gap:.6rem;">
        <span id="auditTotalCount">{{ number_format($logs->total()) }}</span> total entries
        <span id="liveBadge" style="display:none;align-items:center;gap:.3rem;padding:.15rem .5rem;border-radius:9999px;font-size:.68rem;font-weight:700;background:rgba(99,102,241,.15);color:#818cf8;border:1px solid rgba(99,102,241,.3);">
            <span style="width:6px;height:6px;border-radius:50%;background:#818cf8;display:inline-block;animation:pulse 1s infinite;"></span>
            <span id="liveBadgeText"></span>
        </span>
        <span style="display:inline-flex;align-items:center;gap:.25rem;font-size:.68rem;color:var(--text-muted);">
            @if($livePolling)
            <span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;animation:pulse 1.5s infinite;"></span>
            Live
            @else
            <span style="width:6px;height:6px;border-radius:50%;background:#6b7280;display:inline-block;"></span>
            Paused
            @endif
        </span>
    </span>
</div>

{{-- ── FILTERS ──────────────────────────────────────────────── --}}
<div class="section-card mb-6" style="margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('admin.audit-logs') }}" class="filter-bar" style="flex-wrap:wrap;gap:.625rem;">

        {{-- Search --}}
        <div style="position:relative;flex:1;min-width:200px;">
            <i data-lucide="search" style="position:absolute;left:.65rem;top:50%;transform:translateY(-50%);width:.9rem;height:.9rem;color:var(--text-muted);stroke-width:2;pointer-events:none;"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search user, record, IP…"
                class="admin-input" style="padding-left:2.1rem;width:100%;">
        </div>

        {{-- Action --}}
        <select name="action" class="admin-input" style="max-width:160px;">
            <option value="">All Actions</option>
            @foreach($actions as $act)
                <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $act)) }}
                </option>
            @endforeach
        </select>

        {{-- User --}}
        <select name="user_id" class="admin-input" style="max-width:160px;">
            <option value="">All Users</option>
            @foreach($users as $u)
                <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>
                    {{ $u->user_name }}
                </option>
            @endforeach
        </select>

        {{-- Model --}}
        <select name="model" class="admin-input" style="max-width:140px;">
            <option value="">All Models</option>
            <option value="Order"    {{ request('model') === 'Order'    ? 'selected' : '' }}>Orders</option>
            <option value="User"     {{ request('model') === 'User'     ? 'selected' : '' }}>Users</option>
            <option value="MenuItem" {{ request('model') === 'MenuItem' ? 'selected' : '' }}>Menu Items</option>
            <option value="Category" {{ request('model') === 'Category' ? 'selected' : '' }}>Categories</option>
            <option value="Rider"    {{ request('model') === 'Rider'    ? 'selected' : '' }}>Riders</option>
        </select>

        {{-- Date range --}}
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="max-width:145px;" title="From">
        <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="admin-input" style="max-width:145px;" title="To">

        <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.35rem;">
            <i data-lucide="filter" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Filter
        </button>

        @if(request()->hasAny(['search','action','user_id','model','date_from','date_to']))
        <a href="{{ route('admin.audit-logs') }}" class="btn-ghost" style="display:inline-flex;align-items:center;gap:.35rem;">
            <i data-lucide="x" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i> Clear
        </a>
        @endif
    </form>

    {{-- ── TABLE ───────────────────────────────────────────── --}}
    <div style="overflow-x:auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Record</th>
                    <th>IP Address</th>
                    <th style="text-align:center;">Changes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $ac = \App\Models\AuditLog::actionMeta($log->action);
                    $modelIcon = match(class_basename($log->auditable_type ?? '')) {
                        'Order'    => 'shopping-bag',
                        'User'     => 'user',
                        'MenuItem' => 'utensils',
                        'Category' => 'layout-grid',
                        'Rider'    => 'bike',
                        default    => 'box',
                    };
                    $avColor = match($log->user_role) {
                        'admin'  => '#dc2626',
                        'chef'   => '#f59e0b',
                        'rider'  => '#8b5cf6',
                        default  => '#3b82f6',
                    };
                    $initials = $log->user_name
                        ? strtoupper(implode('', array_map(fn($p) => $p[0], array_slice(explode(' ', trim($log->user_name)), 0, 2))))
                        : '?';
                @endphp
                <tr>
                    {{-- Time --}}
                    <td style="white-space:nowrap;">
                        <div style="font-size:.8rem;font-weight:600;color:var(--text-body);">{{ $log->created_at->format('g:i A') }}</div>
                        <div style="font-size:.68rem;color:var(--text-muted);">{{ $log->created_at->format('M d, Y') }}</div>
                        <div style="font-size:.65rem;color:var(--text-muted);margin-top:.1rem;">{{ $log->created_at->diffForHumans() }}</div>
                    </td>

                    {{-- User --}}
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            <div style="width:2rem;height:2rem;border-radius:50%;background:{{ $avColor }}22;border:1.5px solid {{ $avColor }}55;color:{{ $avColor }};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.62rem;flex-shrink:0;">
                                {{ $initials }}
                            </div>
                            <div>
                                @if($log->user_name)
                                    <div style="font-weight:600;font-size:.82rem;color:var(--text-strong);">{{ $log->user_name }}</div>
                                    @if($log->user_role)
                                    <span class="badge" style="background:{{ $avColor }}18;color:{{ $avColor }};font-size:.6rem;padding:.1rem .4rem;border-radius:.25rem;">{{ ucfirst($log->user_role) }}</span>
                                    @endif
                                @else
                                    <div style="font-size:.78rem;color:var(--text-muted);font-style:italic;">Guest</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Action --}}
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:9999px;font-size:.68rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;background:{{ $ac[1] }};color:{{ $ac[0] }};">
                            <i data-lucide="{{ $ac[2] }}" style="width:.65rem;height:.65rem;stroke-width:2.5;"></i>
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                        @if($log->auditable_type)
                        <div style="margin-top:.35rem;display:flex;align-items:center;gap:.25rem;font-size:.72rem;color:var(--text-subtle);">
                            <i data-lucide="{{ $modelIcon }}" style="width:.68rem;height:.68rem;stroke-width:2;color:var(--text-muted);"></i>
                            <span style="font-weight:600;color:var(--text-body);">{{ $log->auditable_label ?? class_basename($log->auditable_type) }}</span>
                            <span style="color:var(--text-muted);">· {{ class_basename($log->auditable_type) }}@if($log->auditable_id)&nbsp;#{{ $log->auditable_id }}@endif</span>
                        </div>
                        @endif
                    </td>

                    {{-- Record / description --}}
                    <td style="font-size:.78rem;color:var(--text-subtle);max-width:200px;">
                        {{ $log->description ?? '—' }}
                    </td>

                    {{-- IP --}}
                    <td style="font-size:.72rem;color:var(--text-muted);font-family:monospace;white-space:nowrap;">
                        {{ $log->ip_address ?? '—' }}
                    </td>

                    {{-- Diff toggle --}}
                    <td style="text-align:center;">
                        @if($log->old_values || $log->new_values)
                        <button onclick="openDiffModal({{ $log->id }})"
                            class="btn-ghost"
                            style="font-size:.72rem;padding:.25rem .6rem;display:inline-flex;align-items:center;gap:.3rem;">
                            <i data-lucide="diff" style="width:.72rem;height:.72rem;stroke-width:2;"></i> View
                        </button>
                        @else
                        <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                        @endif
                    </td>
                </tr>



                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:4rem;color:var(--text-muted);">
                        <i data-lucide="scroll-text" style="width:2.5rem;height:2.5rem;opacity:.25;display:block;margin:0 auto .75rem;"></i>
                        <div style="font-size:.9rem;font-weight:600;color:var(--text-subtle);margin-bottom:.3rem;">No entries found</div>
                        <div style="font-size:.8rem;">Try adjusting your filters or wait for activity to be recorded.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div style="padding:.75rem 1.25rem;border-top:1px solid var(--border-divider);display:flex;justify-content:space-between;align-items:center;font-size:.875rem;color:var(--text-muted);">
        <span>Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</span>
        <div style="display:flex;gap:.375rem;">
            @if($logs->onFirstPage())
                <span class="btn-ghost" style="opacity:.4;cursor:not-allowed;">← Prev</span>
            @else
                <a href="{{ $logs->previousPageUrl() }}" class="btn-ghost">← Prev</a>
            @endif
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}" class="btn-ghost">Next →</a>
            @else
                <span class="btn-ghost" style="opacity:.4;cursor:not-allowed;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// ── DIFF MODAL ────────────────────────────────────────────
var DIFF_DATA = {
@foreach($logs as $log)
@if($log->old_values || $log->new_values)
    {{ $log->id }}: {
        action:    "{{ addslashes(str_replace('_', ' ', $log->action)) }}",
        label:     "{{ addslashes($log->auditable_label ?? class_basename($log->auditable_type ?? '')) }}",
        model:     "{{ addslashes(class_basename($log->auditable_type ?? '')) }}",
        id:        {{ $log->auditable_id ?? 'null' }},
        old:       {!! $log->old_values ? json_encode($log->old_values, JSON_UNESCAPED_UNICODE) : 'null' !!},
        new:       {!! $log->new_values ? json_encode($log->new_values, JSON_UNESCAPED_UNICODE) : 'null' !!},
    },
@endif
@endforeach
};

function openDiffModal(id) {
    var d = DIFF_DATA[id];
    if (!d) return;

    document.getElementById('diffModalTitle').textContent =
        (d.label || d.model || 'Record') +
        (d.id ? ' #' + d.id : '') +
        ' — ' + d.action;

    var body = document.getElementById('diffModalBody');
    var html = '<div style="display:grid;grid-template-columns:' + (d.old && d.new ? '1fr 1fr' : '1fr') + ';gap:1rem;">';

    if (d.old) {
        html += '<div>' +
            '<div style="font-size:.65rem;font-weight:700;color:#f87171;margin-bottom:.5rem;display:flex;align-items:center;gap:.35rem;text-transform:uppercase;letter-spacing:.07em;">' +
                '<svg width="11" height="11" fill="none" stroke="#f87171" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>' +
                'Before' +
            '</div>' +
            '<pre style="margin:0;font-size:.72rem;font-family:\'Courier New\',monospace;background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.22);border-radius:.625rem;padding:.75rem .9rem;overflow:auto;max-height:320px;color:rgba(252,165,165,.9);white-space:pre-wrap;word-break:break-all;line-height:1.65;">' +
                escapeHtml(JSON.stringify(d.old, null, 2)) +
            '</pre>' +
        '</div>';
    }

    if (d.new) {
        html += '<div>' +
            '<div style="font-size:.65rem;font-weight:700;color:#34d399;margin-bottom:.5rem;display:flex;align-items:center;gap:.35rem;text-transform:uppercase;letter-spacing:.07em;">' +
                '<svg width="11" height="11" fill="none" stroke="#34d399" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8M8 12h8"/></svg>' +
                'After' +
            '</div>' +
            '<pre style="margin:0;font-size:.72rem;font-family:\'Courier New\',monospace;background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.22);border-radius:.625rem;padding:.75rem .9rem;overflow:auto;max-height:320px;color:rgba(110,231,183,.9);white-space:pre-wrap;word-break:break-all;line-height:1.65;">' +
                escapeHtml(JSON.stringify(d.new, null, 2)) +
            '</pre>' +
        '</div>';
    }

    html += '</div>';
    body.innerHTML = html;
    openModal('diffModal');
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

// ── REAL-TIME POLLING ─────────────────────────────────────
var pollEnabled = {{ $livePolling ? 'true' : 'false' }};
var latestId    = {{ $logs->first()?->id ?? 0 }};
var pollTimer   = null;
var newCount    = 0;

var ACTION_META = @json(\App\Models\AuditLog::actionMetaForJs());

var ROLE_COLORS = {
    'admin': '#dc2626',
    'chef':  '#f59e0b',
    'rider': '#8b5cf6',
    'user':  '#3b82f6',
};

function getActionColor(action) {
    if (ACTION_META[action]) return ACTION_META[action];
    var key = action.replace(/_/g, ' ').toLowerCase();
    for (var act in ACTION_META) {
        if (act.replace(/_/g, ' ').toLowerCase() === key) return ACTION_META[act];
    }
    return ['#a3a3a3','rgba(163,163,163,.08)'];
}

function getRoleColor(role) {
    return ROLE_COLORS[role] || '#6b7280';
}

function getInitials(name) {
    if (!name) return '?';
    return name.split(' ').slice(0,2).map(function(p){ return p[0]; }).join('').toUpperCase();
}

function buildRow(entry) {
    var ac       = getActionColor(entry.action);
    var avColor  = getRoleColor(entry.user_role);
    var initials = getInitials(entry.user_name);
    var label    = entry.action.replace(/_/g,' ').toUpperCase();
    var hasDiff  = entry.old_values || entry.new_values;

    // Store diff data for modal
    if (hasDiff) {
        DIFF_DATA[entry.id] = {
            action: entry.action.replace(/_/g,' '),
            label:  entry.auditable_label || entry.auditable_type || 'Record',
            model:  entry.auditable_type || '',
            id:     entry.auditable_id,
            old:    entry.old_values,
            new:    entry.new_values,
        };
    }

    var recordHtml = '';
    if (entry.auditable_type) {
        recordHtml = '<div style="margin-top:.35rem;font-size:.72rem;color:var(--text-muted);">'
            + escapeHtml(entry.auditable_label || entry.auditable_type)
            + ' · ' + escapeHtml(entry.auditable_type)
            + (entry.auditable_id ? ' #' + entry.auditable_id : '')
            + '</div>';
    }

    var diffBtn = hasDiff
        ? '<button onclick="openDiffModal(' + entry.id + ')" class="btn-ghost" style="font-size:.72rem;padding:.25rem .6rem;display:inline-flex;align-items:center;gap:.3rem;">'
            + '<svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg> View'
          + '</button>'
        : '<span style="color:var(--text-muted);font-size:.75rem;">—</span>';

    return '<tr style="animation:fadeInRow .4s ease;">'
        + '<td style="white-space:nowrap;">'
            + '<div style="font-size:.8rem;font-weight:600;color:var(--text-body);">' + escapeHtml(entry.time) + '</div>'
            + '<div style="font-size:.68rem;color:var(--text-muted);">' + escapeHtml(entry.date) + '</div>'
            + '<div style="font-size:.65rem;color:var(--text-muted);margin-top:.1rem;">' + escapeHtml(entry.ago) + '</div>'
        + '</td>'
        + '<td>'
            + '<div style="display:flex;align-items:center;gap:.5rem;">'
                + '<div style="width:2rem;height:2rem;border-radius:50%;background:' + avColor + '22;border:1.5px solid ' + avColor + '55;color:' + avColor + ';display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.62rem;flex-shrink:0;">' + escapeHtml(initials) + '</div>'
                + '<div>'
                    + (entry.user_name
                        ? '<div style="font-weight:600;font-size:.82rem;color:var(--text-strong);">' + escapeHtml(entry.user_name) + '</div>'
                          + (entry.user_role ? '<span style="background:' + avColor + '18;color:' + avColor + ';font-size:.6rem;padding:.1rem .4rem;border-radius:.25rem;">' + entry.user_role + '</span>' : '')
                        : '<div style="font-size:.78rem;color:var(--text-muted);font-style:italic;">Guest</div>')
                + '</div>'
            + '</div>'
        + '</td>'
        + '<td>'
            + '<span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:9999px;font-size:.68rem;font-weight:700;letter-spacing:.04em;text-transform:uppercase;background:' + ac[1] + ';color:' + ac[0] + ';">' + escapeHtml(label) + '</span>'
            + recordHtml
        + '</td>'
        + '<td style="font-size:.78rem;color:var(--text-subtle);max-width:200px;">' + escapeHtml(entry.description || '—') + '</td>'
        + '<td style="font-size:.72rem;color:var(--text-muted);font-family:monospace;white-space:nowrap;">' + escapeHtml(entry.ip_address || '—') + '</td>'
        + '<td style="text-align:center;">' + diffBtn + '</td>'
        + '</tr>';
}

function pollAuditLogs() {
    if (!pollEnabled) return;

    fetch('{{ route("admin.audit-logs.poll") }}?after=' + latestId, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r){ return r.json(); })
    .then(function(data) {
        if (!data.entries || data.entries.length === 0) return;

        var tbody = document.querySelector('.admin-table tbody');
        if (!tbody) return;

        // Remove "no entries" placeholder if present
        var empty = tbody.querySelector('[colspan="6"]');
        if (empty) empty.closest('tr').remove();

        // Prepend new rows (newest first)
        data.entries.slice().reverse().forEach(function(entry) {
            tbody.insertAdjacentHTML('afterbegin', buildRow(entry));
        });

        latestId = data.latest_id;
        newCount += data.entries.length;

        // Update counter in header
        var counter = document.getElementById('auditTotalCount');
        if (counter) {
            var current = parseInt(counter.textContent.replace(/,/g,'')) || 0;
            counter.textContent = (current + data.entries.length).toLocaleString();
        }

        // Show live badge
        var badge = document.getElementById('liveBadge');
        var badgeText = document.getElementById('liveBadgeText');
        if (badge && badgeText) {
            badgeText.textContent = '+' + newCount + ' new';
            badge.style.display = 'inline-flex';
        }

        // Re-run lucide icons on new rows
        if (typeof lucide !== 'undefined') lucide.createIcons();
    })
    .catch(function(){});
}

function startPolling() {
    if (!pollEnabled) return;
    if (pollTimer) clearInterval(pollTimer);
    pollAuditLogs();
    pollTimer = setInterval(pollAuditLogs, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    startPolling();
});

document.addEventListener('visibilitychange', function() {
    if (!pollEnabled) return;

    if (document.hidden) {
        clearInterval(pollTimer);
        pollTimer = null;
    } else {
        newCount = 0;
        var badge = document.getElementById('liveBadge');
        if (badge) badge.style.display = 'none';
        startPolling();
    }
});
</script>
<style>
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%      { opacity: .45; transform: scale(.92); }
}
@keyframes fadeInRow {
    from { opacity: 0; transform: translateY(-6px); background: rgba(99,102,241,.08); }
    to   { opacity: 1; transform: translateY(0);    background: transparent; }
}
</style>
@endpush
{{-- ── DIFF MODAL ───────────────────────────────────────────── --}}
<div id="diffModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'diffModal')">
    <div class="modal-box modal-lg" style="max-width:860px;width:95vw;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="diff" style="width:1.1rem;height:1.1rem;color:#eab308;stroke-width:2;"></i>
                <h3 class="modal-title" id="diffModalTitle">Changes</h3>
            </div>
            <button onclick="closeModal('diffModal')" class="modal-close">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i>
            </button>
        </div>
        <div id="diffModalBody" class="modal-body" style="max-height:72vh;overflow-y:auto;">
        </div>
    </div>
</div>

@endsection
