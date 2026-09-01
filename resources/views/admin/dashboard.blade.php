@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
    <p>Welcome back, {{ auth()->user()->name }}. Here's what's happening at EUT today.</p>
</div>

<div class="dashboard-gate {{ $dashboardVerified ? '' : 'dashboard-gate--locked' }}">
<div class="dashboard-gate__content">

{{-- ── STAT CARDS ── --}}
<style>
#statCardsGrid{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:2rem;}
@media(min-width:768px){#statCardsGrid{grid-template-columns:repeat(3,1fr);}}
@media(min-width:1024px){#statCardsGrid{grid-template-columns:repeat(5,1fr);}}
</style>
<div id="statCardsGrid">
    @php
        $statCards = [
            [
                'label' => 'Total Orders',
                'value' => number_format($stats['total_orders']),
                'sub'   => 'All time',
                'icon'  => 'shopping-bag',
                'color' => '#10b981',
                'bg'    => 'rgba(16,185,129,0.12)',
            ],
            [
                'label' => 'Items Sold Today',
                'value' => number_format($stats['items_sold_today']),
                'sub'   => $stats['today_orders'] . ' orders today',
                'icon'  => 'shopping-bag',
                'color' => '#6366f1',
                'bg'    => 'rgba(99,102,241,0.12)',
            ],
            [
                'label' => 'Pending',
                'value' => $stats['pending_orders'],
                'sub'   => 'Awaiting action',
                'icon'  => 'hourglass',
                'color' => '#f59e0b',
                'bg'    => 'rgba(245,158,11,0.12)',
            ],
            [
                'label' => 'Today\'s Revenue',
                'value' => '₱' . number_format($stats['today_revenue']),
                'sub'   => 'From delivered orders',
                'icon'  => 'trending-up',
                'color' => '#22c55e',
                'bg'    => 'rgba(34,197,94,0.12)',
            ],
            [
                'label' => 'Total Revenue',
                'value' => '₱' . number_format($stats['total_revenue']),
                'sub'   => 'All delivered orders',
                'icon'  => 'banknote',
                'color' => '#facc15',
                'bg'    => 'rgba(250,204,21,0.12)',
            ],
            [
                'label' => 'Customers',
                'value' => $stats['total_customers'],
                'sub'   => 'Registered users',
                'icon'  => 'users',
                'color' => '#a78bfa',
                'bg'    => 'rgba(167,139,250,0.12)',
            ],
            [
                'label' => 'Active Riders',
                'value' => $stats['active_riders'] . ' / ' . $stats['rider_users'],
                'sub'   => 'Online right now',
                'icon'  => 'bike',
                'color' => '#2563eb',
                'bg'    => 'rgba(37,99,235,0.12)',
            ],
            [
                'label' => 'Menu Items',
                'value' => $stats['total_items'],
                'sub'   => $stats['total_categories'] . ' categories',
                'icon'  => 'utensils',
                'color' => '#f97316',
                'bg'    => 'rgba(249,115,22,0.12)',
            ],
            [
                'label' => 'Featured Items',
                'value' => $stats['featured_items'],
                'sub'   => 'Highlighted on menu',
                'icon'  => 'star',
                'color' => '#eab308',
                'bg'    => 'rgba(234,179,8,0.12)',
            ],
            [
                'label' => 'Staff',
                'value' => $stats['admin_users'] + $stats['chef_users'] + $stats['rider_users'] + ($stats['waiter_users'] ?? 0),
                'sub'   => $stats['chef_users'] . ' chefs · ' . $stats['rider_users'] . ' riders · ' . ($stats['waiter_users'] ?? 0) . ' waiters',
                'icon'  => 'shield-check',
                'color' => '#dc2626',
                'bg'    => 'rgba(220,38,38,0.10)',
            ],
        ];
    @endphp

    @foreach($statCards as $s)
    <div class="stat-card" style="position:relative;overflow:hidden;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;margin-bottom:.875rem;">
            <i data-lucide="{{ $s['icon'] }}" style="width:1.25rem;height:1.25rem;color:{{ $s['color'] }};stroke-width:2;"></i>
        </div>
        <p style="font-size:.7rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.07em;margin:0 0 .3rem;font-weight:600;">{{ $s['label'] }}</p>
        <p style="font-size:1.75rem;font-weight:800;color:{{ $s['color'] }};margin:0 0 .2rem;line-height:1;">{{ $s['value'] }}</p>
        <p style="font-size:.7rem;color:var(--text-muted);margin:0;">{{ $s['sub'] }}</p>
        <div style="position:absolute;bottom:-1rem;right:-1rem;width:4rem;height:4rem;border-radius:50%;background:{{ $s['bg'] }};filter:blur(12px);pointer-events:none;"></div>
    </div>
    @endforeach
</div>

{{-- ── MIDDLE ROW ── --}}
<div style="display:grid;grid-template-columns:1fr;gap:1.5rem;margin-bottom:2rem;" id="midRow">
<style>@media(min-width:1024px){#midRow{grid-template-columns:repeat(2,1fr);}}</style>

    {{-- Top Selling Items --}}
    <div class="section-card">
        <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                <i data-lucide="trending-up" style="width:1rem;height:1rem;color:var(--accent);stroke-width:2;"></i>
                <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0;">Today's Sold Items</h2>
                <span style="font-size:.7rem;color:#4ade80;font-weight:700;background:rgba(74,222,128,.1);border:1px solid rgba(74,222,128,.25);border-radius:99px;padding:.1rem .5rem;">Today · {{ now()->format('M d, Y') }}</span>
            </div>
            <a href="{{ route('admin.orders') }}" style="font-size:.7rem;color:var(--accent);text-decoration:none;font-weight:500;flex-shrink:0;">View orders →</a>
        </div>
        <div class="p-5" style="display:flex;flex-direction:column;gap:.75rem;">
            @php $maxSold = collect($topItems)->max('total_sold') ?: 1; @endphp
            @forelse($topItems as $i => $item)
            <div style="display:flex;align-items:center;gap:.75rem;">
                <span style="font-size:.7rem;font-weight:800;color:var(--text-muted);min-width:1.25rem;text-align:right;">{{ $i+1 }}</span>
                <img src="{{ $item['image'] }}" alt=""
                     style="width:2.25rem;height:2.25rem;border-radius:.5rem;object-fit:cover;flex-shrink:0;border:1px solid var(--border-card);"
                     onerror="this.src='/images/hero-burger.webp'">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.25rem;">
                        <span style="font-size:.8rem;font-weight:600;color:var(--text-strong);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:55%;">{{ $item['name'] }}</span>
                        <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;">
                            <span style="font-size:.72rem;color:var(--text-muted);">₱{{ number_format($item['total_revenue']) }}</span>
                            <span style="font-size:.78rem;font-weight:800;color:#facc15;">{{ number_format($item['total_sold']) }}×</span>
                        </div>
                    </div>
                    <div style="height:4px;background:var(--border-card);border-radius:9999px;overflow:hidden;">
                        <div style="height:100%;border-radius:9999px;background:{{ $item['category_color'] }};width:{{ min(100, ($item['total_sold']/$maxSold)*100) }}%;transition:width .6s ease;"></div>
                    </div>
                    <span style="font-size:.65rem;color:var(--text-muted);margin-top:.2rem;display:block;">{{ $item['category'] }}</span>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:2rem 1rem;">
                <div style="font-size:2rem;margin-bottom:.5rem;">📊</div>
                <p style="color:var(--text-muted);font-size:.85rem;margin:0;font-weight:600;">No sales yet today</p>
                <p style="color:var(--text-muted);font-size:.75rem;margin:.25rem 0 0;opacity:.7;">Sold items will appear here as orders come in</p>
            </div>
            @endforelse
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
    <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;" id="quickActGrid">
    <style>@media(min-width:768px){#quickActGrid{grid-template-columns:repeat(4,1fr);}}</style>
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

{{-- ── MONTHLY RESET ── --}}
<div class="section-card" style="margin-top:1.5rem;">
    <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;gap:.5rem;">
        <i data-lucide="archive" style="width:1rem;height:1rem;color:#ef4444;stroke-width:2;"></i>
        <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0;">Danger Zone</h2>
    </div>
    <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem;" id="dangerActGrid">
    <style>@media(min-width:768px){#dangerActGrid{grid-template-columns:repeat(4,1fr);}}</style>
        <button onclick="openResetModal()"
            style="display:flex;flex-direction:column;align-items:center;gap:.625rem;padding:1.25rem 1rem;border-radius:.875rem;border:1px solid rgba(239,68,68,.2);background:transparent;cursor:pointer;transition:all .2s;"
            onmouseenter="this.style.borderColor='rgba(239,68,68,.5)';this.style.background='rgba(239,68,68,.08)';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(239,68,68,.15)';"
            onmouseleave="this.style.borderColor='rgba(239,68,68,.2)';this.style.background='transparent';this.style.transform='none';this.style.boxShadow='none';">
            <div style="width:2.75rem;height:2.75rem;border-radius:.75rem;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center;">
                <i data-lucide="refresh-ccw" style="width:1.25rem;height:1.25rem;color:#ef4444;stroke-width:2;"></i>
            </div>
            <span style="font-size:.75rem;font-weight:600;color:#f87171;text-align:center;">Reset Month</span>
        </button>
    </div>
</div>

{{-- ── RESET MONTH MODAL ── --}}
<div id="resetModal"
     style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;">
    <div style="width:100%;max-width:420px;background:#13141f;border:1px solid rgba(239,68,68,.3);border-radius:1.1rem;padding:1.75rem 1.5rem;box-shadow:0 24px 60px rgba(0,0,0,.6);">

        {{-- Header --}}
        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.1rem;">
            <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i data-lucide="archive" style="width:1.1rem;height:1.1rem;color:#ef4444;stroke-width:2;"></i>
            </div>
            <div>
                <h3 style="margin:0;font-size:1rem;font-weight:700;color:#fff;">Reset Month</h3>
                <p style="margin:0;font-size:.73rem;color:#9ca3af;">Archive orders to JSON, then delete from DB</p>
            </div>
            <button onclick="closeResetModal()" style="margin-left:auto;background:none;border:none;color:#6b7280;cursor:pointer;padding:.25rem;line-height:1;">
                <i data-lucide="x" style="width:1.1rem;height:1.1rem;stroke-width:2.5;"></i>
            </button>
        </div>

        {{-- Warning box --}}
        <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:.75rem;padding:.85rem 1rem;margin-bottom:1.25rem;display:flex;gap:.65rem;align-items:flex-start;">
            <i data-lucide="triangle-alert" style="width:1rem;height:1rem;color:#ef4444;flex-shrink:0;margin-top:.1rem;stroke-width:2;"></i>
            <p style="margin:0;font-size:.77rem;color:#fca5a5;line-height:1.5;">
                This action is <strong>irreversible</strong>. Orders will be saved to
                <code style="background:rgba(255,255,255,.08);border-radius:.3rem;padding:.05rem .3rem;font-size:.72rem;">storage/app/archives/orders-YYYY-MM.json</code>
                before deletion.
            </p>
        </div>

        {{-- Month / Year selectors --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem;">
            <div>
                <label style="display:block;font-size:.7rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Month</label>
                <select id="resetMonth"
                    style="width:100%;background:#0d0e1a;border:1px solid rgba(255,255,255,.1);border-radius:.65rem;padding:.65rem .85rem;font-size:.875rem;color:#e5e7eb;outline:none;appearance:none;cursor:pointer;">
                    @php
                        $months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                        $defaultMonth = now()->subMonthNoOverflow()->month;
                    @endphp
                    @foreach($months as $i => $mName)
                    <option value="{{ $i + 1 }}" {{ ($i + 1) === $defaultMonth ? 'selected' : '' }}>{{ $mName }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:.7rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Year</label>
                <select id="resetYear"
                    style="width:100%;background:#0d0e1a;border:1px solid rgba(255,255,255,.1);border-radius:.65rem;padding:.65rem .85rem;font-size:.875rem;color:#e5e7eb;outline:none;appearance:none;cursor:pointer;">
                    @php $defaultYear = now()->subMonthNoOverflow()->year; @endphp
                    @for($y = now()->year; $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $y === $defaultYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- Confirm label --}}
        <div style="margin-bottom:1.25rem;">
            <label style="display:block;font-size:.7rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">
                Type <strong style="color:#f87171;">RESET</strong> to confirm
            </label>
            <input type="text" id="resetConfirmInput" placeholder="RESET"
                style="width:100%;background:#0d0e1a;border:1px solid rgba(255,255,255,.1);border-radius:.65rem;padding:.7rem .9rem;font-size:.9rem;color:#e5e7eb;outline:none;box-sizing:border-box;"
                oninput="document.getElementById('resetConfirmBtn').disabled = this.value !== 'RESET';"
                onfocus="this.style.borderColor='rgba(239,68,68,.5)';"
                onblur="this.style.borderColor='rgba(255,255,255,.1)';">
        </div>

        {{-- Result message --}}
        <div id="resetResult" style="display:none;margin-bottom:1rem;border-radius:.65rem;padding:.7rem .9rem;font-size:.8rem;font-weight:600;"></div>

        {{-- Buttons --}}
        <div style="display:flex;gap:.75rem;">
            <button onclick="closeResetModal()"
                style="flex:1;padding:.75rem;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;color:#9ca3af;font-size:.85rem;font-weight:600;cursor:pointer;transition:all .2s;"
                onmouseenter="this.style.background='rgba(255,255,255,.1)';"
                onmouseleave="this.style.background='rgba(255,255,255,.05)';">
                Cancel
            </button>
            <button id="resetConfirmBtn" onclick="submitReset()" disabled
                style="flex:1;padding:.75rem;background:linear-gradient(135deg,#dc2626,#ef4444);border:none;border-radius:.75rem;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;transition:all .2s;opacity:.4;"
                onmouseenter="if(!this.disabled){this.style.opacity='1';this.style.boxShadow='0 4px 16px rgba(239,68,68,.45)';}"
                onmouseleave="this.style.opacity=this.disabled?'.4':'1';this.style.boxShadow='none';">
                <span id="resetBtnLabel">Archive &amp; Delete</span>
            </button>
        </div>

    </div>
</div>

</div>{{-- /.dashboard-gate__content --}}

@if(! $dashboardVerified)
<div class="dashboard-gate__overlay">
    <div class="dashboard-verify-card">
        <div class="dashboard-verify-icon">
            <svg width="22" height="22" fill="none" stroke="#f59e0b" stroke-width="2.2" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0110 0v4"/>
            </svg>
        </div>
        <h2>Verify to View Dashboard</h2>
        <p>Sales, revenue, and user data are hidden until you confirm your password. Your password is required every time you open the dashboard.</p>

        @if($errors->has('password'))
        <div class="dashboard-verify-error">{{ $errors->first('password') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.verify.submit') }}">
            @csrf
            <input type="hidden" name="scope" value="dashboard">
            <label for="dashboardPassword">Password</label>
            <input type="password" id="dashboardPassword" name="password" placeholder="Enter your password" required autofocus>
            <button type="submit">Verify &amp; Unlock</button>
        </form>
    </div>
</div>
@endif

</div>{{-- /.dashboard-gate --}}

@push('head')
<style>
.dashboard-gate { position: relative; min-height: 320px; }
.dashboard-gate--locked .dashboard-gate__content {
    filter: blur(10px);
    opacity: .55;
    user-select: none;
    pointer-events: none;
}
.dashboard-gate__overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 2rem 1rem 3rem;
    z-index: 20;
    background: linear-gradient(180deg, rgba(13,14,26,.15) 0%, rgba(13,14,26,.45) 100%);
}
.dashboard-verify-card {
    width: 100%;
    max-width: 380px;
    background: #13141f;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 1rem;
    padding: 1.75rem 1.5rem;
    box-shadow: 0 20px 50px rgba(0,0,0,.45);
    text-align: center;
}
.dashboard-verify-icon {
    width: 3rem;
    height: 3rem;
    margin: 0 auto 1rem;
    border-radius: 50%;
    background: rgba(245,158,11,.12);
    display: flex;
    align-items: center;
    justify-content: center;
}
.dashboard-verify-card h2 {
    margin: 0 0 .4rem;
    font-size: 1.05rem;
    font-weight: 700;
    color: #fff;
}
.dashboard-verify-card p {
    margin: 0 0 1.25rem;
    font-size: .8rem;
    color: #9ca3af;
    line-height: 1.5;
}
.dashboard-verify-error {
    background: rgba(239,68,68,.1);
    border: 1px solid rgba(239,68,68,.25);
    border-radius: .6rem;
    padding: .6rem .85rem;
    font-size: .78rem;
    color: #f87171;
    margin-bottom: 1rem;
    text-align: left;
}
.dashboard-verify-card label {
    display: block;
    text-align: left;
    font-size: .72rem;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: .06em;
    margin-bottom: .4rem;
}
.dashboard-verify-card input[type="password"] {
    width: 100%;
    background: #0d0e1a;
    border: 1px solid rgba(255,255,255,.1);
    border-radius: .65rem;
    padding: .75rem .9rem;
    font-size: .9rem;
    color: #e5e7eb;
    outline: none;
    margin-bottom: 1rem;
}
.dashboard-verify-card input:focus { border-color: #f59e0b; }
.dashboard-verify-card button {
    width: 100%;
    padding: .8rem;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border: none;
    border-radius: .75rem;
    color: #000;
    font-size: .9rem;
    font-weight: 700;
    cursor: pointer;
}
.dashboard-verify-card button:hover { opacity: .92; }

/* ── Reset modal confirm button state ── */
#resetConfirmBtn:not([disabled]) { opacity: 1; cursor: pointer; }
#resetConfirmBtn[disabled]       { opacity: .4; cursor: not-allowed; }
</style>
@endpush

@push('scripts')
<script>
// ── Monthly Reset modal ──────────────────────────────────────────────────────
function openResetModal() {
    const modal = document.getElementById('resetModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // clear state
    document.getElementById('resetConfirmInput').value = '';
    document.getElementById('resetConfirmBtn').disabled = true;
    const res = document.getElementById('resetResult');
    res.style.display = 'none';
    res.textContent = '';
    document.getElementById('resetBtnLabel').textContent = 'Archive & Delete';
    // re-init lucide icons inside modal
    if (window.lucide) lucide.createIcons();
}

function closeResetModal() {
    const modal = document.getElementById('resetModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// Close on backdrop click
document.getElementById('resetModal').addEventListener('click', function(e) {
    if (e.target === this) closeResetModal();
});

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeResetModal();
});

async function submitReset() {
    const btn   = document.getElementById('resetConfirmBtn');
    const label = document.getElementById('resetBtnLabel');
    const res   = document.getElementById('resetResult');
    const month = document.getElementById('resetMonth').value;
    const year  = document.getElementById('resetYear').value;

    btn.disabled = true;
    label.innerHTML = '<span style="display:inline-flex;align-items:center;gap:.4rem;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin .8s linear infinite;"><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" opacity=".25"/><path d="M21 12a9 9 0 00-9-9" stroke-linecap="round"/></svg> Processing…</span>';
    res.style.display = 'none';

    try {
        const resp = await fetch('{{ route('admin.orders.reset-month') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                             ?? '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ month: parseInt(month), year: parseInt(year) }),
        });

        const data = await resp.json();

        res.style.display = 'block';
        if (data.success) {
            res.style.background    = 'rgba(34,197,94,.1)';
            res.style.border        = '1px solid rgba(34,197,94,.25)';
            res.style.color         = '#4ade80';
            res.innerHTML = `✓ ${data.message}<br><span style="font-size:.72rem;opacity:.75;font-weight:400;">Saved → ${data.archive_file}</span>`;
            label.textContent = 'Done';
            // Auto-close after 3 s and reload stats
            setTimeout(() => { closeResetModal(); location.reload(); }, 3000);
        } else {
            res.style.background = 'rgba(239,68,68,.1)';
            res.style.border     = '1px solid rgba(239,68,68,.25)';
            res.style.color      = '#f87171';
            res.textContent = data.message ?? 'Something went wrong.';
            label.textContent = 'Archive & Delete';
            btn.disabled = false;
        }
    } catch (err) {
        res.style.display    = 'block';
        res.style.background = 'rgba(239,68,68,.1)';
        res.style.border     = '1px solid rgba(239,68,68,.25)';
        res.style.color      = '#f87171';
        res.textContent      = 'Network error — please try again.';
        label.textContent    = 'Archive & Delete';
        btn.disabled = false;
    }
}
</script>
@endpush
@endsection
