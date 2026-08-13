@extends('admin.layout')
@section('title', 'Audit Logs')

@section('content')
<div class="page-header" style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(234,179,8,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="scroll-text" style="width:1.2rem;height:1.2rem;color:#eab308;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;">Audit Logs</h1>
            <p style="margin:0;">Track who did what, when, and on which record.</p>
        </div>
    </div>
    <span style="font-size:.8rem;color:var(--text-muted);">{{ number_format($logs->total()) }} total entries</span>
</div>

{{-- ── FILTER BAR ──────────────────────────────────────── --}}
<div class="section-card mb-6">
    <form method="GET" action="{{ route('admin.audit-logs') }}" class="filter-bar" style="flex-wrap:wrap;">
        {{-- Search --}}
        <div style="position:relative;min-width:200px;flex:1;">
            <i data-lucide="search" style="position:absolute;left:.65rem;top:50%;transform:translateY(-50%);width:.9rem;height:.9rem;color:var(--text-muted);stroke-width:2;pointer-events:none;"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description, user, record…" class="admin-input" style="padding-left:2.1rem;width:100%;">
        </div>

        {{-- Action filter --}}
        <select name="action" class="admin-input" style="min-width:150px;">
            <option value="">All Actions</option>
            @foreach($actions as $act)
                <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                    {{ ucwords(str_replace('_', ' ', $act)) }}
                </option>
            @endforeach
        </select>

        {{-- User filter --}}
        <select name="user_id" class="admin-input" style="min-width:150px;">
            <option value="">All Users</option>
            @foreach($users as $u)
                <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>
                    {{ $u->user_name }}
                </option>
            @endforeach
        </select>

        {{-- Model filter --}}
        <select name="model" class="admin-input" style="min-width:130px;">
            <option value="">All Models</option>
            <option value="Order"    {{ request('model') === 'Order'    ? 'selected' : '' }}>Orders</option>
            <option value="User"     {{ request('model') === 'User'     ? 'selected' : '' }}>Users</option>
            <option value="MenuItem" {{ request('model') === 'MenuItem' ? 'selected' : '' }}>Menu Items</option>
            <option value="Category" {{ request('model') === 'Category' ? 'selected' : '' }}>Categories</option>
            <option value="Rider"    {{ request('model') === 'Rider'    ? 'selected' : '' }}>Riders</option>
        </select>

        {{-- Date range --}}
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input" style="min-width:130px;" title="From date">
        <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="admin-input" style="min-width:130px;" title="To date">

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
                    <th>Description</th>
                    <th>IP Address</th>
                    <th style="text-align:center;">Changes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    {{-- Time --}}
                    <td style="white-space:nowrap;font-size:.75rem;color:var(--text-muted);" title="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                        {{ $log->created_at->format('M d, g:i A') }}
                    </td>

                    {{-- User --}}
                    <td>
                        @if($log->user_name)
                            <div style="font-weight:500;font-size:.82rem;">{{ $log->user_name }}</div>
                            @if($log->user_role)
                                <span style="font-size:.68rem;padding:.1rem .4rem;border-radius:.25rem;background:rgba(99,102,241,.12);color:#818cf8;">
                                    {{ ucfirst($log->user_role) }}
                                </span>
                            @endif
                        @else
                            <span style="color:var(--text-muted);font-size:.78rem;">Guest / System</span>
                        @endif
                    </td>

                    {{-- Action badge --}}
                    <td>
                        @php
                            $badgeColor = match($log->action) {
                                'created'          => ['#10b981','rgba(16,185,129,.12)'],
                                'updated'          => ['#3b82f6','rgba(59,130,246,.12)'],
                                'deleted'          => ['#ef4444','rgba(239,68,68,.12)'],
                                'archived'         => ['#f59e0b','rgba(245,158,11,.12)'],
                                'restored'         => ['#10b981','rgba(16,185,129,.12)'],
                                'status_changed'   => ['#8b5cf6','rgba(139,92,246,.12)'],
                                'role_changed'     => ['#f59e0b','rgba(245,158,11,.12)'],
                                'settings_changed' => ['#ec4899','rgba(236,72,153,.12)'],
                                'login'            => ['#6366f1','rgba(99,102,241,.12)'],
                                'logout'           => ['#6b7280','rgba(107,114,128,.12)'],
                                default            => ['#a3a3a3','rgba(163,163,163,.12)'],
                            };
                        @endphp
                        <span style="display:inline-block;padding:.15rem .5rem;border-radius:.35rem;font-size:.7rem;font-weight:600;letter-spacing:.03em;background:{{ $badgeColor[1] }};color:{{ $badgeColor[0] }};">
                            {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                        </span>
                    </td>

                    {{-- Affected record --}}
                    <td style="font-size:.78rem;">
                        @if($log->auditable_type)
                            <div style="font-weight:500;">{{ $log->auditable_label ?? '—' }}</div>
                            <div style="font-size:.68rem;color:var(--text-muted);">
                                {{ class_basename($log->auditable_type) }}
                                @if($log->auditable_id) #{{ $log->auditable_id }} @endif
                            </div>
                        @else
                            <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>

                    {{-- Description --}}
                    <td style="font-size:.8rem;color:var(--text-subtle);max-width:220px;">
                        {{ $log->description ?? '—' }}
                    </td>

                    {{-- IP --}}
                    <td style="font-size:.75rem;color:var(--text-muted);font-family:monospace;white-space:nowrap;">
                        {{ $log->ip_address ?? '—' }}
                    </td>

                    {{-- Changes toggle --}}
                    <td style="text-align:center;">
                        @if($log->old_values || $log->new_values)
                            <button onclick="toggleDiff({{ $log->id }})"
                                class="btn-ghost"
                                style="font-size:.72rem;padding:.2rem .55rem;display:inline-flex;align-items:center;gap:.25rem;">
                                <i data-lucide="diff" style="width:.75rem;height:.75rem;stroke-width:2;"></i>
                                View
                            </button>
                        @else
                            <span style="color:var(--text-muted);font-size:.75rem;">—</span>
                        @endif
                    </td>
                </tr>

                {{-- Expandable diff row --}}
                @if($log->old_values || $log->new_values)
                <tr id="diff-{{ $log->id }}" style="display:none;">
                    <td colspan="7" style="padding:.75rem 1rem 1rem;background:rgba(0,0,0,.15);">
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;max-width:700px;">
                            @if($log->old_values)
                            <div>
                                <div style="font-size:.7rem;font-weight:600;color:#ef4444;margin-bottom:.35rem;text-transform:uppercase;letter-spacing:.05em;">Before</div>
                                <pre style="margin:0;font-size:.72rem;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);border-radius:.5rem;padding:.6rem .75rem;overflow:auto;max-height:200px;color:var(--text-subtle);white-space:pre-wrap;word-break:break-all;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                            @endif
                            @if($log->new_values)
                            <div>
                                <div style="font-size:.7rem;font-weight:600;color:#10b981;margin-bottom:.35rem;text-transform:uppercase;letter-spacing:.05em;">After</div>
                                <pre style="margin:0;font-size:.72rem;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:.5rem;padding:.6rem .75rem;overflow:auto;max-height:200px;color:var(--text-subtle);white-space:pre-wrap;word-break:break-all;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted);">
                        <i data-lucide="scroll-text" style="width:2.5rem;height:2.5rem;opacity:.3;display:block;margin:0 auto .75rem;"></i>
                        No audit log entries found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div style="margin-top:1rem;display:flex;justify-content:flex-end;">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
function toggleDiff(id) {
    const row = document.getElementById('diff-' + id);
    if (!row) return;
    const visible = row.style.display !== 'none';
    row.style.display = visible ? 'none' : 'table-row';
}
</script>
@endpush
@endsection
