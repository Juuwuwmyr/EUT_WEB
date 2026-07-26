@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
    <p>Welcome back, {{ auth()->user()->name }}. Here's what's happening at EUT today.</p>
</div>

{{-- ── STAT CARDS ── --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4 mb-8">
    @php
        $statCards = [
            [
                'label'  => 'Total Users',
                'value'  => $stats['total_users'],
                'sub'    => 'Registered accounts',
                'icon'   => 'users',
                'color'  => '#6366f1',   /* indigo */
                'bg'     => 'rgba(99,102,241,0.12)',
            ],
            [
                'label'  => 'Admins',
                'value'  => $stats['admin_users'],
                'sub'    => 'Admin accounts',
                'icon'   => 'shield-check',
                'color'  => '#dc2626',   /* red — always */
                'bg'     => 'rgba(220,38,38,0.10)',
            ],
            [
                'label'  => 'Chefs',
                'value'  => $stats['chef_users'],
                'sub'    => 'Kitchen staff',
                'icon'   => 'chef-hat',
                'color'  => '#d97706',   /* amber */
                'bg'     => 'rgba(217,119,6,0.12)',
            ],
            [
                'label'  => 'Riders',
                'value'  => $stats['rider_users'],
                'sub'    => 'Delivery crew',
                'icon'   => 'bike',
                'color'  => '#2563eb',   /* blue */
                'bg'     => 'rgba(37,99,235,0.12)',
            ],
            [
                'label'  => 'Menu Items',
                'value'  => $stats['total_items'],
                'sub'    => 'Across all categories',
                'icon'   => 'utensils',
                'color'  => '#f59e0b',   /* amber */
                'bg'     => 'rgba(245,158,11,0.12)',
            ],
            [
                'label'  => 'Categories',
                'value'  => $stats['total_categories'],
                'sub'    => 'Active categories',
                'icon'   => 'layout-grid',
                'color'  => '#10b981',   /* emerald */
                'bg'     => 'rgba(16,185,129,0.12)',
            ],
            [
                'label'  => 'Featured',
                'value'  => $stats['featured_items'],
                'sub'    => 'Highlighted items',
                'icon'   => 'star',
                'color'  => '#f59e0b',   /* amber */
                'bg'     => 'rgba(245,158,11,0.12)',
            ],
        ];
    @endphp

    @foreach($statCards as $s)
    <div class="stat-card" style="position:relative;overflow:hidden;">
        {{-- icon badge --}}
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;margin-bottom:.875rem;">
            <i data-lucide="{{ $s['icon'] }}" style="width:1.25rem;height:1.25rem;color:{{ $s['color'] }};stroke-width:2;"></i>
        </div>
        <p style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin:0 0 .3rem;font-weight:600;">{{ $s['label'] }}</p>
        <p style="font-size:2rem;font-weight:800;color:{{ $s['color'] }};margin:0 0 .2rem;line-height:1;">{{ $s['value'] }}</p>
        <p style="font-size:.7rem;color:var(--text-muted);margin:0;">{{ $s['sub'] }}</p>
        {{-- decorative corner glow --}}
        <div style="position:absolute;bottom:-1rem;right:-1rem;width:4rem;height:4rem;border-radius:50%;background:{{ $s['bg'] }};filter:blur(12px);pointer-events:none;"></div>
    </div>
    @endforeach
</div>

{{-- ── MIDDLE ROW ── --}}
<div class="grid lg:grid-cols-2 gap-6 mb-8">

    {{-- Category breakdown --}}
    <div class="section-card">
        <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="pie-chart" style="width:1rem;height:1rem;color:var(--accent);stroke-width:2;"></i>
                <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0;">Menu by Category</h2>
            </div>
            <a href="{{ route('admin.menu-items') }}" style="font-size:.7rem;color:var(--accent);text-decoration:none;font-weight:500;">View all →</a>
        </div>
        <div class="p-5" style="display:flex;flex-direction:column;gap:1rem;">
            @foreach($categories as $cat)
            <div style="display:flex;align-items:center;gap:.875rem;">
                <div style="width:2rem;height:2rem;border-radius:.5rem;background:{{ $cat['hex'] }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-lucide="{{ $cat['icon'] }}" style="width:.9rem;height:.9rem;color:{{ $cat['hex'] }};stroke-width:2.5;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.3rem;">
                        <span style="font-size:.8rem;font-weight:500;color:var(--text-strong);">{{ $cat['name'] }}</span>
                        <span style="font-size:.8rem;font-weight:700;color:{{ $cat['hex'] }};">{{ $cat['count'] }}</span>
                    </div>
                    <div style="height:5px;background:var(--border-card);border-radius:9999px;overflow:hidden;">
                        <div style="height:100%;border-radius:9999px;background:{{ $cat['hex'] }};width:{{ $stats['total_items'] > 0 ? min(100,($cat['count']/$stats['total_items'])*100) : 0 }}%;transition:width .6s ease;"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Users --}}
    <div class="section-card">
        <div class="px-5 py-4 card-header-border" style="display:flex;justify-content:space-between;align-items:center;">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="users-round" style="width:1rem;height:1rem;color:var(--accent);stroke-width:2;"></i>
                <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0;">Recent Users</h2>
            </div>
            <a href="{{ route('admin.users') }}" style="font-size:.7rem;color:var(--accent);text-decoration:none;font-weight:500;">View all →</a>
        </div>
        <table class="admin-table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr>
            </thead>
            <tbody>
                @forelse($recent_users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.5rem;">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" class="w-7 h-7 rounded-full object-cover" style="border:1px solid var(--border-card);" alt="">
                            @else
                                <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs shrink-0"
                                     style="background:var(--accent);color:#fff;">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                            @endif
                            <span style="font-weight:500;color:var(--text-strong);font-size:.875rem;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="color:var(--text-muted);font-size:.8rem;">{{ $user->email }}</td>
                    <td><span class="badge {{ $user->role==='admin' ? 'badge-admin' : 'badge-user' }}">{{ $user->role ?? 'user' }}</span></td>
                    <td style="color:var(--text-muted);font-size:.75rem;">{{ $user->created_at->format('M d, Y') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:1.5rem;">No users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── QUICK ACTIONS ── --}}
<div class="section-card">
    <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;gap:.5rem;">
        <i data-lucide="zap" style="width:1rem;height:1rem;color:var(--accent);stroke-width:2;"></i>
        <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0;">Quick Actions</h2>
    </div>
    <div class="p-5 grid grid-cols-2 md:grid-cols-4 gap-3">
        @php
            $actions = [
                ['href'=>route('admin.users'),      'icon'=>'users',        'label'=>'Manage Users',  'color'=>'#6366f1','bg'=>'rgba(99,102,241,.10)'],
                ['href'=>route('admin.menu-items'), 'icon'=>'utensils',     'label'=>'Menu Items',    'color'=>'#f59e0b','bg'=>'rgba(245,158,11,.10)'],
                ['href'=>route('admin.orders'),     'icon'=>'shopping-bag', 'label'=>'View Orders',   'color'=>'#10b981','bg'=>'rgba(16,185,129,.10)'],
                ['href'=>route('admin.settings'),   'icon'=>'settings-2',   'label'=>'Settings',      'color'=>'#94a3b8','bg'=>'rgba(148,163,184,.10)'],
            ];
        @endphp
        @foreach($actions as $a)
        <a href="{{ $a['href'] }}"
           style="display:flex;flex-direction:column;align-items:center;gap:.625rem;padding:1.25rem 1rem;border-radius:.875rem;border:1px solid var(--border-card);text-decoration:none;transition:all .2s;background:transparent;"
           onmouseenter="this.style.borderColor='{{ $a['color'] }}44';this.style.background='{{ $a['bg'] }}';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px {{ $a['color'] }}22';"
           onmouseleave="this.style.borderColor='var(--border-card)';this.style.background='transparent';this.style.transform='none';this.style.boxShadow='none';">
            <div style="width:2.75rem;height:2.75rem;border-radius:.75rem;background:{{ $a['bg'] }};display:flex;align-items:center;justify-content:center;transition:background .2s;">
                <i data-lucide="{{ $a['icon'] }}" style="width:1.25rem;height:1.25rem;color:{{ $a['color'] }};stroke-width:2;"></i>
            </div>
            <span style="font-size:.75rem;font-weight:600;color:var(--text-subtle);text-align:center;transition:color .2s;">{{ $a['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>
@endsection
