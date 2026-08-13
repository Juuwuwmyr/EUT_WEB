@extends('admin.layout')
@section('title', 'Audit Logs')

@push('head')
<style>
/* ── AUDIT HERO ──────────────────────────────────────────── */
.audit-hero {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #1c0a0a 0%, #200d0d 40%, #140a1e 100%);
    border: 1px solid rgba(220,38,38,.25);
    border-radius: 1.25rem;
    padding: 2rem 2rem 1.75rem;
    margin-bottom: 1.75rem;
    box-shadow: 0 0 0 1px rgba(220,38,38,.08), 0 8px 32px rgba(0,0,0,.5);
}
.audit-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(220,38,38,.28) 0%, transparent 65%);
    pointer-events: none;
}
.audit-hero::after {
    content: '';
    position: absolute;
    bottom: -50px; left: 20%;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(139,92,246,.18) 0%, transparent 65%);
    pointer-events: none;
}
html.light .audit-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #0f172a 100%);
    border-color: rgba(255,255,255,.15);
    box-shadow: 0 8px 32px rgba(0,0,0,.3);
}
.audit-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .55rem .9rem;
    border-radius: .75rem;
    background: rgba(255,255,255,.07);
    border: 1px solid rgba(255,255,255,.13);
    backdrop-filter: blur(6px);
}

/* ── FILTER PANEL ────────────────────────────────────────── */
.audit-filter-panel {
    background: var(--bg-card);
    border: 1px solid var(--border-card);
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.audit-filter-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .875rem 1.25rem;
    cursor: pointer;
    user-select: none;
    transition: background .2s;
}
.audit-filter-header:hover { background: rgba(255,255,255,.03); }
.audit-filter-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .875rem;
    padding: 1.25rem;
    border-top: 1px solid var(--border-card);
}
@media(max-width:780px){ .audit-filter-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:480px){ .audit-filter-grid { grid-template-columns: 1fr; } }
.filter-full { grid-column: 1 / -1; }

/* ── TIMELINE ────────────────────────────────────────────── */
.timeline-wrap {
    background: var(--bg-card);
    border: 1px solid var(--border-card);
    border-radius: 1rem;
    overflow: hidden;
}
.timeline-entry {
    display: grid;
    grid-template-columns: 130px 44px 1fr;
    position: relative;
}
@media(max-width:600px){ .timeline-entry { grid-template-columns: 72px 36px 1fr; } }

/* time column */
.tl-time {
    padding: 1.25rem .75rem 0 0;
    text-align: right;
}
/* spine column */
.tl-spine {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 1.5rem;
}
.tl-dot {
    width: 11px; height: 11px;
    border-radius: 50%;
    border: 2px solid currentColor;
    background: var(--bg-body);
    flex-shrink: 0;
    z-index: 1;
    transition: transform .15s, box-shadow .15s;
}
.timeline-entry:hover .tl-dot {
    transform: scale(1.5);
}
.tl-rail {
    width: 2px;
    flex: 1;
    min-height: 24px;
    margin-top: 5px;
    background: linear-gradient(to bottom, var(--border-card), transparent);
}
.timeline-entry:last-child .tl-rail { opacity: 0; }

/* card */
.tl-card {
    margin: .75rem 0 .75rem 1rem;
    background: var(--bg-section);
    border: 1px solid var(--border-section);
    border-radius: .875rem;
    padding: .9rem 1.1rem;
    transition: border-color .2s, box-shadow .2s, transform .15s;
}
.timeline-entry:hover .tl-card {
    border-color: rgba(255,255,255,.18);
    box-shadow: 0 4px 20px rgba(0,0,0,.35);
    transform: translateX(3px);
}

/* action pill */
.action-pill {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    padding: .2rem .65rem;
    border-radius: 9999px;
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .05em;
    text-transform: uppercase;
    border: 1px solid;
    white-space: nowrap;
}

/* record tag */
.record-tag {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: .4rem;
    padding: .15rem .5rem;
    font-size: .74rem;
    white-space: nowrap;
    max-width: 260px;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* user chip */
.user-chip {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.07);
    border-radius: 9999px;
    padding: .15rem .55rem .15rem .2rem;
    flex-shrink: 0;
}
.user-av {
    width: 20px; height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .58rem;
    font-weight: 800;
    flex-shrink: 0;
}

/* diff block */
.diff-panel {
    display: none;
    margin-top: .8rem;
    padding-top: .8rem;
    border-top: 1px solid rgba(255,255,255,.06);
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
}
.diff-panel.open { display: grid; }
@media(max-width:520px){ .diff-panel.open { grid-template-columns: 1fr; } }
.diff-label {
    font-size: .62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: .4rem;
    display: flex;
    align-items: center;
    gap: .3rem;
}
.diff-pre {
    border-radius: .6rem;
    padding: .65rem .8rem;
    overflow: auto;
    max-height: 180px;
    font-size: .69rem;
    font-family: 'JetBrains Mono','Fira Code','Courier New',monospace;
    line-height: 1.65;
    white-space: pre-wrap;
    word-break: break-all;
}

/* diff toggle btn */
.diff-btn {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: .45rem;
    padding: .28rem .6rem;
    cursor: pointer;
    color: var(--text-muted);
    font-size: .69rem;
    font-weight: 600;
    transition: all .15s;
    white-space: nowrap;
}
.diff-btn:hover, .diff-btn.open {
    background: rgba(99,102,241,.12);
    border-color: rgba(99,102,241,.35);
    color: #818cf8;
}

/* empty */
.tl-empty {
    padding: 5rem 2rem;
    text-align: center;
    color: var(--text-muted);
}

/* active filter dot indicator */
.filter-active-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #dc2626;
    display: inline-block;
    flex-shrink: 0;
}
</style>
@endpush

@section('content')

{{-- ══════════════════ HERO HEADER ══════════════════ --}}
<div class="audit-hero">
    <div style="position:relative;z-index:1;display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1.25rem;">

        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:3.25rem;height:3.25rem;border-radius:1.1rem;background:linear-gradient(135deg,rgba(220,38,38,.35),rgba(139,92,246,.35));border:1px solid rgba(255,255,255,.14);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="rgba(255,255,255,.95)" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
                <h1 style="margin:0;font-family:'Playfair Display',serif;font-size:1.8rem;font-weight:700;color:#fff;letter-spacing:-.025em;line-height:1.1;">Audit Logs</h1>
                <p style="margin:.3rem 0 0;color:rgba(255,255,255,.4);font-size:.82rem;letter-spacing:.01em;">Complete trail of every action taken in the system</p>
            </div>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:.625rem;align-items:center;">
            <div class="audit-stat-pill">
                <svg width="13" height="13" fill="none" stroke="#a78bfa" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span style="font-size:.75rem;color:rgba(255,255,255,.5);">Total entries</span>
                <span style="font-size:.9rem;font-weight:700;color:#fff;">{{ number_format($logs->total()) }}</span>
            </div>
            <div class="audit-stat-pill">
                <svg width="13" height="13" fill="none" stroke="#34d399" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                <span style="font-size:.75rem;color:rgba(255,255,255,.5);">Page</span>
                <span style="font-size:.9rem;font-weight:700;color:#fff;">{{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>
            </div>
            @if(request()->hasAny(['search','action','user_id','model','date_from','date_to']))
            <div class="audit-stat-pill" style="border-color:rgba(220,38,38,.35);background:rgba(220,38,38,.1);">
                <svg width="13" height="13" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 8h10M11 12h2"/></svg>
                <span style="font-size:.75rem;color:#fca5a5;font-weight:600;">Filtered view</span>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ══════════════════ FILTER PANEL ══════════════════ --}}
<div class="audit-filter-panel">
    <div class="audit-filter-header" onclick="toggleFilters(this)">
        <div style="display:flex;align-items:center;gap:.65rem;font-size:.875rem;font-weight:600;color:var(--text-body);">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 8h10M11 12h2"/></svg>
            Search &amp; Filters
            @if(request()->hasAny(['search','action','user_id','model','date_from','date_to']))
                <span class="filter-active-dot"></span>
            @endif
        </div>
        <svg id="filterChevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
             style="transition:transform .2s;{{ request()->hasAny(['search','action','user_id','model','date_from','date_to']) ? 'transform:rotate(180deg)' : '' }}">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>

    <div id="filterBody" style="{{ request()->hasAny(['search','action','user_id','model','date_from','date_to']) ? '' : 'display:none' }}">
        <form method="GET" action="{{ route('admin.audit-logs') }}">
            <div class="audit-filter-grid">

                {{-- Search —— full width --}}
                <div class="filter-full" style="position:relative;">
                    <svg style="position:absolute;left:.8rem;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted);pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search description, user, record label or IP address…"
                        class="admin-input" style="padding-left:2.4rem;">
                </div>

                {{-- Action --}}
                <div>
                    <label class="field-label">Action</label>
                    <select name="action" class="admin-input">
                        <option value="">All Actions</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $act)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- User --}}
                <div>
                    <label class="field-label">User</label>
                    <select name="user_id" class="admin-input">
                        <option value="">All Users</option>
                        @foreach($users as $u)
                            <option value="{{ $u->user_id }}" {{ request('user_id') == $u->user_id ? 'selected' : '' }}>
                                {{ $u->user_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Model --}}
                <div>
                    <label class="field-label">Model</label>
                    <select name="model" class="admin-input">
                        <option value="">All Models</option>
                        <option value="Order"    {{ request('model') === 'Order'    ? 'selected' : '' }}>Orders</option>
                        <option value="User"     {{ request('model') === 'User'     ? 'selected' : '' }}>Users</option>
                        <option value="MenuItem" {{ request('model') === 'MenuItem' ? 'selected' : '' }}>Menu Items</option>
                        <option value="Category" {{ request('model') === 'Category' ? 'selected' : '' }}>Categories</option>
                        <option value="Rider"    {{ request('model') === 'Rider'    ? 'selected' : '' }}>Riders</option>
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="field-label">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="admin-input">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="field-label">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="admin-input">
                </div>

                {{-- Buttons --}}
                <div class="filter-full" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
                    <button type="submit" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
                        Apply
                    </button>
                    @if(request()->hasAny(['search','action','user_id','model','date_from','date_to']))
                    <a href="{{ route('admin.audit-logs') }}" class="btn-ghost" style="display:inline-flex;align-items:center;gap:.4rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════ TIMELINE ══════════════════ --}}
<div class="timeline-wrap">
    @forelse($logs as $log)
    @php
        $am = match($log->action) {
            'created'          => ['c'=>'#10b981','bg'=>'rgba(16,185,129,.12)','br'=>'rgba(16,185,129,.3)','ic'=>'plus-circle'],
            'updated'          => ['c'=>'#3b82f6','bg'=>'rgba(59,130,246,.12)','br'=>'rgba(59,130,246,.3)','ic'=>'pencil'],
            'deleted'          => ['c'=>'#ef4444','bg'=>'rgba(239,68,68,.12)','br'=>'rgba(239,68,68,.3)','ic'=>'trash-2'],
            'archived'         => ['c'=>'#f59e0b','bg'=>'rgba(245,158,11,.12)','br'=>'rgba(245,158,11,.3)','ic'=>'archive'],
            'restored'         => ['c'=>'#10b981','bg'=>'rgba(16,185,129,.12)','br'=>'rgba(16,185,129,.3)','ic'=>'rotate-ccw'],
            'status_changed'   => ['c'=>'#8b5cf6','bg'=>'rgba(139,92,246,.12)','br'=>'rgba(139,92,246,.3)','ic'=>'refresh-cw'],
            'role_changed'     => ['c'=>'#f59e0b','bg'=>'rgba(245,158,11,.12)','br'=>'rgba(245,158,11,.3)','ic'=>'shield'],
            'settings_changed' => ['c'=>'#ec4899','bg'=>'rgba(236,72,153,.12)','br'=>'rgba(236,72,153,.3)','ic'=>'settings'],
            'login'            => ['c'=>'#6366f1','bg'=>'rgba(99,102,241,.12)','br'=>'rgba(99,102,241,.3)','ic'=>'log-in'],
            'logout'           => ['c'=>'#6b7280','bg'=>'rgba(107,114,128,.1)', 'br'=>'rgba(107,114,128,.25)','ic'=>'log-out'],
            default            => ['c'=>'#a3a3a3','bg'=>'rgba(163,163,163,.08)','br'=>'rgba(163,163,163,.2)','ic'=>'activity'],
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
            'user'   => '#3b82f6',
            default  => '#6b7280',
        };
        $initials = $log->user_name
            ? strtoupper(implode('', array_map(fn($p) => $p[0], array_slice(explode(' ', trim($log->user_name)), 0, 2))))
            : '?';
    @endphp

    <div class="timeline-entry">

        {{-- Time --}}
        <div class="tl-time">
            <div style="font-size:.73rem;font-weight:700;color:var(--text-subtle);">{{ $log->created_at->format('g:i A') }}</div>
            <div style="font-size:.62rem;color:var(--text-muted);margin-top:.1rem;">{{ $log->created_at->format('M d, Y') }}</div>
        </div>

        {{-- Spine --}}
        <div class="tl-spine">
            <div class="tl-dot" style="color:{{ $am['c'] }};box-shadow:0 0 7px {{ $am['c'] }}55;"></div>
            <div class="tl-rail"></div>
        </div>

        {{-- Card --}}
        <div class="tl-card" style="border-left:3px solid {{ $am['c'] }}44;">

            {{-- Top row: pill + record + right side --}}
            <div style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:.6rem 1rem;">

                {{-- Left cluster --}}
                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:.45rem;flex:1;min-width:0;">
                    {{-- Action --}}
                    <span class="action-pill" style="color:{{ $am['c'] }};background:{{ $am['bg'] }};border-color:{{ $am['br'] }};">
                        <i data-lucide="{{ $am['ic'] }}" style="width:.68rem;height:.68rem;stroke-width:2.5;"></i>
                        {{ str_replace('_', ' ', $log->action) }}
                    </span>

                    {{-- Record --}}
                    @if($log->auditable_type)
                    <span class="record-tag">
                        <i data-lucide="{{ $modelIcon }}" style="width:.68rem;height:.68rem;stroke-width:2;color:var(--text-muted);flex-shrink:0;"></i>
                        <span style="font-weight:600;color:var(--text-body);">{{ $log->auditable_label ?? class_basename($log->auditable_type) }}</span>
                        <span style="color:var(--text-muted);font-size:.67rem;">· {{ class_basename($log->auditable_type) }}@if($log->auditable_id) #{{ $log->auditable_id }}@endif</span>
                    </span>
                    @endif

                    {{-- Description (if non-trivial) --}}
                    @if($log->description)
                    <span style="font-size:.77rem;color:var(--text-muted);">{{ $log->description }}</span>
                    @endif
                </div>

                {{-- Right cluster: user + diff --}}
                <div style="display:flex;align-items:center;gap:.6rem;flex-shrink:0;">
                    {{-- User chip --}}
                    <div class="user-chip">
                        <div class="user-av" style="background:{{ $avColor }}20;border:1.5px solid {{ $avColor }}55;color:{{ $avColor }};">
                            {{ $initials }}
                        </div>
                        @if($log->user_name)
                            <span style="font-size:.72rem;font-weight:600;color:var(--text-body);">{{ $log->user_name }}</span>
                            @if($log->user_role)
                            <span style="font-size:.62rem;color:var(--text-muted);padding:.05rem .3rem;border-radius:.2rem;background:rgba(255,255,255,.05);">{{ ucfirst($log->user_role) }}</span>
                            @endif
                        @else
                            <span style="font-size:.72rem;color:var(--text-muted);font-style:italic;">Guest</span>
                        @endif
                    </div>

                    {{-- Diff button --}}
                    @if($log->old_values || $log->new_values)
                    <button class="diff-btn" id="diffbtn-{{ $log->id }}" onclick="toggleDiff({{ $log->id }})">
                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        diff
                    </button>
                    @endif
                </div>
            </div>

            {{-- Meta row: IP + time ago --}}
            <div style="margin-top:.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                @if($log->ip_address)
                <span style="font-size:.67rem;color:var(--text-muted);font-family:'Courier New',monospace;display:flex;align-items:center;gap:.3rem;">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M2 12h20M12 2a15.3 15.3 0 010 20M12 2a15.3 15.3 0 000 20"/></svg>
                    {{ $log->ip_address }}
                </span>
                @endif
                <span style="font-size:.67rem;color:var(--text-muted);display:flex;align-items:center;gap:.3rem;">
                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                    {{ $log->created_at->diffForHumans() }}
                </span>
            </div>

            {{-- Diff panel --}}
            @if($log->old_values || $log->new_values)
            <div class="diff-panel" id="diff-{{ $log->id }}">
                @if($log->old_values)
                <div>
                    <div class="diff-label" style="color:#f87171;">
                        <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                        Before
                    </div>
                    <div class="diff-pre" style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);color:rgba(252,165,165,.85);">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                </div>
                @endif
                @if($log->new_values)
                <div>
                    <div class="diff-label" style="color:#34d399;">
                        <svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        After
                    </div>
                    <div class="diff-pre" style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);color:rgba(110,231,183,.85);">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
                </div>
                @endif
            </div>
            @endif

        </div>{{-- /.tl-card --}}
    </div>{{-- /.timeline-entry --}}
    @empty
    <div class="tl-empty">
        <svg width="60" height="60" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" style="opacity:.18;margin:0 auto 1rem;display:block;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <p style="font-size:1rem;font-weight:600;color:var(--text-subtle);margin:0 0 .4rem;">No entries found</p>
        <p style="font-size:.82rem;margin:0;">Try adjusting your filters, or wait for activity to be recorded.</p>
    </div>
    @endforelse
</div>

{{-- ══════════════════ PAGINATION ══════════════════ --}}
@if($logs->hasPages())
<div style="margin-top:1.5rem;display:flex;justify-content:center;">
    {{ $logs->appends(request()->query())->links() }}
</div>
@endif

@push('scripts')
<script>
function toggleDiff(id) {
    const panel = document.getElementById('diff-' + id);
    const btn   = document.getElementById('diffbtn-' + id);
    if (!panel) return;
    const open = panel.classList.toggle('open');
    if (btn) btn.classList.toggle('open', open);
}

function toggleFilters(headerEl) {
    const body    = document.getElementById('filterBody');
    const chevron = document.getElementById('filterChevron');
    const isOpen  = body.style.display !== 'none';
    body.style.display     = isOpen ? 'none' : '';
    chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
@endpush
@endsection
