@extends('admin.layout')
@section('title', 'Audit Logs')

@section('content')

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
    <span style="font-size:.8rem;color:var(--text-muted);">{{ number_format($logs->total()) }} total entries</span>
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
                    $ac = match($log->action) {
                        'created'          => ['#10b981','rgba(16,185,129,.12)','plus-circle'],
                        'updated'          => ['#3b82f6','rgba(59,130,246,.12)','pencil'],
                        'deleted'          => ['#ef4444','rgba(239,68,68,.12)','trash-2'],
                        'archived'         => ['#f59e0b','rgba(245,158,11,.12)','archive'],
                        'restored'         => ['#10b981','rgba(16,185,129,.12)','rotate-ccw'],
                        'status_changed'   => ['#8b5cf6','rgba(139,92,246,.12)','refresh-cw'],
                        'role_changed'     => ['#f59e0b','rgba(245,158,11,.12)','shield'],
                        'settings_changed' => ['#ec4899','rgba(236,72,153,.12)','settings'],
                        'login'            => ['#6366f1','rgba(99,102,241,.12)','log-in'],
                        'logout'           => ['#6b7280','rgba(107,114,128,.1)', 'log-out'],
                        default            => ['#a3a3a3','rgba(163,163,163,.08)','activity'],
                    };
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
                        <button onclick="toggleDiff({{ $log->id }}, this)"
                            id="diffbtn-{{ $log->id }}"
                            class="btn-ghost"
                            style="font-size:.72rem;padding:.25rem .6rem;display:inline-flex;align-items:center;gap:.3rem;">
                            <i data-lucide="diff" style="width:.72rem;height:.72rem;stroke-width:2;"></i> View
                        </button>
                        @else
                        <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                        @endif
                    </td>
                </tr>

                {{-- Expandable diff row --}}
                @if($log->old_values || $log->new_values)
                <tr id="diff-{{ $log->id }}" style="display:none;">
                    <td colspan="6" style="padding:.5rem 1rem 1.1rem;background:rgba(0,0,0,.18);">
                        <div style="display:grid;grid-template-columns:{{ $log->old_values && $log->new_values ? '1fr 1fr' : '1fr' }};gap:.875rem;max-width:800px;">
                            @if($log->old_values)
                            <div>
                                <div style="font-size:.65rem;font-weight:700;color:#f87171;margin-bottom:.35rem;display:flex;align-items:center;gap:.3rem;text-transform:uppercase;letter-spacing:.06em;">
                                    <i data-lucide="minus-circle" style="width:.7rem;height:.7rem;stroke-width:2.5;"></i> Before
                                </div>
                                <pre style="margin:0;font-size:.7rem;font-family:'Courier New',monospace;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);border-radius:.5rem;padding:.625rem .8rem;overflow:auto;max-height:200px;color:rgba(252,165,165,.9);white-space:pre-wrap;word-break:break-all;line-height:1.6;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                            @endif
                            @if($log->new_values)
                            <div>
                                <div style="font-size:.65rem;font-weight:700;color:#34d399;margin-bottom:.35rem;display:flex;align-items:center;gap:.3rem;text-transform:uppercase;letter-spacing:.06em;">
                                    <i data-lucide="plus-circle" style="width:.7rem;height:.7rem;stroke-width:2.5;"></i> After
                                </div>
                                <pre style="margin:0;font-size:.7rem;font-family:'Courier New',monospace;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:.5rem;padding:.625rem .8rem;overflow:auto;max-height:200px;color:rgba(110,231,183,.9);white-space:pre-wrap;word-break:break-all;line-height:1.6;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endif

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
                <a href="{{ $logs->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="btn-ghost">← Prev</a>
            @endif
            @if($logs->hasMorePages())
                <a href="{{ $logs->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="btn-ghost">Next →</a>
            @else
                <span class="btn-ghost" style="opacity:.4;cursor:not-allowed;">Next →</span>
            @endif
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
function toggleDiff(id, btn) {
    const row = document.getElementById('diff-' + id);
    if (!row) return;
    const open = row.style.display === 'none';
    row.style.display = open ? 'table-row' : 'none';
    if (btn) {
        btn.style.background    = open ? 'rgba(99,102,241,.12)' : '';
        btn.style.borderColor   = open ? 'rgba(99,102,241,.4)'  : '';
        btn.style.color         = open ? '#818cf8'              : '';
    }
}
</script>
@endpush
@endsection
