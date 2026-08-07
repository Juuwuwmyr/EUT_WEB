<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - E.U.T Snack House</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #080810; color: #fff; min-height: 100vh; }

        /* ── HERO ── */
        .hero { background: linear-gradient(135deg,#b91c1c 0%,#dc2626 40%,#c2410c 100%); padding:56px 20px 70px; position:relative; overflow:hidden; }
        .hero::before { content:''; position:absolute; top:-60px; right:-60px; width:240px; height:240px; background:radial-gradient(circle,rgba(255,255,255,.08) 0%,transparent 70%); pointer-events:none; }
        .hero-inner { max-width:560px; margin:0 auto; position:relative; z-index:1; }
        .hero-avatar-row { display:flex; align-items:center; gap:14px; margin-bottom:16px; }
        .hero-avatar { width:62px; height:62px; border-radius:50%; border:3px solid rgba(255,255,255,.3); object-fit:cover; flex-shrink:0; box-shadow:0 4px 16px rgba(0,0,0,.3); }
        .hero-avatar-placeholder { width:62px; height:62px; border-radius:50%; background:rgba(255,255,255,.15); border:3px solid rgba(255,255,255,.3); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; color:#fff; flex-shrink:0; box-shadow:0 4px 16px rgba(0,0,0,.3); }
        .hero-greeting { font-size:11px; color:rgba(255,255,255,.6); letter-spacing:.05em; text-transform:uppercase; margin-bottom:3px; }
        .hero-name { font-family:'Playfair Display',serif; font-size:22px; font-weight:700; color:#fff; }
        .hero-email { font-size:12px; color:rgba(255,255,255,.55); margin-top:2px; }
        .hero-stats { display:flex; gap:0; background:rgba(0,0,0,.2); border-radius:14px; overflow:hidden; border:1px solid rgba(255,255,255,.1); }
        .hero-stat { flex:1; padding:12px 8px; text-align:center; border-right:1px solid rgba(255,255,255,.08); }
        .hero-stat:last-child { border-right:none; }
        .hero-stat-value { font-size:18px; font-weight:800; color:#fff; }
        .hero-stat-label { font-size:10px; color:rgba(255,255,255,.5); margin-top:2px; }
        .hero-theme-btn { position:absolute; top:14px; right:16px; width:34px; height:34px; border-radius:50%; background:rgba(0,0,0,.2); border:1px solid rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; z-index:2; }
        .hero-edit-btn { position:absolute; top:14px; right:58px; width:34px; height:34px; border-radius:50%; background:rgba(0,0,0,.2); border:1px solid rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all .2s; z-index:2; color:#fff; }
        .hero-edit-btn:hover { background:rgba(0,0,0,.35); }
        .hero-theme-btn:hover { background:rgba(0,0,0,.35); }

        /* ── BODY ── */
        .page-body { max-width:560px; margin:0 auto; padding:20px 0 110px; margin-top:-22px; position:relative; z-index:2; }

        /* ── SECTION GROUPS ── */
        .section-card { background:linear-gradient(145deg,#12131f,#0e0f1a); border:1px solid rgba(255,255,255,.07); border-radius:20px; overflow:hidden; margin:0 14px 14px; box-shadow:0 4px 24px rgba(0,0,0,.4); }
        .section-label { font-size:11px; font-weight:700; color:#4b5563; text-transform:uppercase; letter-spacing:.07em; padding:18px 18px 6px; }

        /* ── MENU ROWS ── */
        .menu-row { display:flex; align-items:center; gap:14px; padding:14px 18px; border-bottom:1px solid rgba(255,255,255,.04); text-decoration:none; cursor:pointer; transition:background .15s; position:relative; background:none; border-left:none; border-top:none; border-right:none; width:100%; text-align:left; color:inherit; }
        .menu-row:last-child { border-bottom:none; }
        .menu-row:hover { background:rgba(255,255,255,.03); }
        .menu-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .menu-text { flex:1; }
        .menu-title { font-size:14px; font-weight:500; color:#e5e7eb; }
        .menu-sub { font-size:11px; color:#4b5563; margin-top:2px; }
        .menu-badge { font-size:10px; font-weight:700; padding:2px 8px; border-radius:99px; margin-right:6px; flex-shrink:0; }
        .badge-green { background:rgba(34,197,94,.1); color:#4ade80; border:1px solid rgba(34,197,94,.2); }
        .chevron { color:#374151; flex-shrink:0; transition:transform .15s; }
        .menu-row:hover .chevron { transform:translateX(2px); color:#6b7280; }
        .logout-btn { display:flex; align-items:center; gap:14px; padding:15px 18px; width:100%; background:none; border:none; cursor:pointer; transition:background .15s; }
        .logout-btn:hover { background:rgba(239,68,68,.06); }

        /* ── MODAL OVERLAY ── */
        .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.75); backdrop-filter:blur(6px); z-index:500; align-items:flex-end; justify-content:center; }
        .modal-backdrop.open { display:flex; }
        .modal-sheet { background:#0e0f1a; border:1px solid rgba(255,255,255,.08); border-radius:24px 24px 0 0; width:100%; max-width:560px; max-height:92vh; overflow-y:auto; padding-bottom:env(safe-area-inset-bottom,0px); }
        .modal-handle { width:40px; height:4px; border-radius:99px; background:rgba(255,255,255,.15); margin:12px auto 0; }
        .modal-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px 12px; border-bottom:1px solid rgba(255,255,255,.06); }
        .modal-title { font-family:'Playfair Display',serif; font-size:18px; font-weight:700; color:#fff; }
        .modal-close { width:30px; height:30px; border-radius:50%; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.08); color:#6b7280; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:16px; }
        .modal-close:hover { background:rgba(255,255,255,.12); color:#fff; }
        .modal-body { padding:20px; }
        .form-field { margin-bottom:16px; }
        .form-label { display:block; font-size:12px; font-weight:600; color:#9ca3af; margin-bottom:7px; letter-spacing:.05em; text-transform:uppercase; }
        .form-input { width:100%; background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.09); border-radius:10px; padding:12px 14px; font-size:14px; color:#f9fafb; outline:none; transition:border-color .18s; }
        .form-input:focus { border-color:rgba(250,204,21,.4); box-shadow:0 0 0 3px rgba(250,204,21,.07); }
        .form-input::placeholder { color:#4b5563; }
        .form-input:disabled { opacity:.5; cursor:not-allowed; }
        .btn-submit { width:100%; padding:14px; border-radius:12px; background:linear-gradient(135deg,#dc2626,#ef4444); color:#fff; font-size:14px; font-weight:700; border:none; cursor:pointer; transition:all .2s; margin-top:4px; }
        .btn-submit:hover { background:linear-gradient(135deg,#b91c1c,#dc2626); }
        .btn-submit:disabled { opacity:.6; cursor:not-allowed; }
        .form-alert { display:none; padding:10px 14px; border-radius:10px; font-size:13px; font-weight:600; margin-bottom:14px; }
        .form-alert.error { background:rgba(220,38,38,.12); color:#f87171; border:1px solid rgba(220,38,38,.25); }
        .form-alert.success { background:rgba(34,197,94,.12); color:#4ade80; border:1px solid rgba(34,197,94,.25); }

        /* ── ADDRESS CARD ── */
        .addr-card { background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.07); border-radius:14px; padding:14px; margin-bottom:10px; position:relative; transition:border-color .2s; }
        .addr-card.default { border-color:rgba(250,204,21,.3); }
        .addr-label-badge { display:inline-block; font-size:10px; font-weight:700; padding:2px 8px; border-radius:99px; background:rgba(250,204,21,.1); color:#facc15; border:1px solid rgba(250,204,21,.2); margin-bottom:6px; }
        .addr-name { font-size:13px; font-weight:700; color:#f3f4f6; margin-bottom:3px; }
        .addr-text { font-size:12px; color:#6b7280; line-height:1.5; }
        .addr-actions { display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; }
        .addr-btn { font-size:11px; font-weight:600; padding:5px 12px; border-radius:8px; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.04); color:#9ca3af; cursor:pointer; transition:all .2s; }
        .addr-btn:hover { border-color:rgba(250,204,21,.3); color:#facc15; }
        .addr-btn.danger:hover { border-color:rgba(239,68,68,.35); color:#f87171; }
        .addr-btn.primary { background:rgba(250,204,21,.1); border-color:rgba(250,204,21,.25); color:#facc15; }
        .add-addr-btn { width:100%; padding:12px; border-radius:12px; background:rgba(255,255,255,.03); border:1px dashed rgba(255,255,255,.12); color:#6b7280; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; display:flex; align-items:center; justify-content:center; gap:8px; margin-top:4px; }
        .add-addr-btn:hover { border-color:rgba(250,204,21,.3); color:#facc15; background:rgba(250,204,21,.04); }
        .default-star { position:absolute; top:12px; right:12px; color:#facc15; font-size:14px; }

        /* ── LIGHT MODE ── */
        .light-mode body { background:#f0f0f8!important; }
        .light-mode .section-card { background:#fff!important; border-color:rgba(0,0,0,.07)!important; }
        .light-mode .menu-title { color:#111!important; }
        .light-mode .section-label { color:#9ca3af!important; }
        .light-mode .modal-sheet { background:#fff!important; }
        .light-mode .form-input { background:rgba(0,0,0,.04)!important; border-color:rgba(0,0,0,.1)!important; color:#111!important; }
        .light-mode .addr-card { background:#f8f8ff!important; border-color:rgba(0,0,0,.08)!important; }

        /* ── BOTTOM NAV ── */
        .bottom-nav { position:fixed; bottom:0; left:0; right:0; background:rgba(8,8,16,.97); border-top:1px solid rgba(255,255,255,.07); padding:10px 0 14px; z-index:100; }
        @media(min-width:1024px){ .bottom-nav{display:none;} }
        .bottom-nav-inner { display:flex; }
        .bnav-item { flex:1; display:flex; flex-direction:column; align-items:center; gap:3px; color:#4b5563; text-decoration:none; font-size:10px; font-weight:500; transition:color .15s; }
        .bnav-item.active { color:#facc15; }
    </style>
</head>
<body>

<!-- ══════════ HERO ══════════ -->
<div class="hero">
    <button type="button" id="shopThemeToggle" class="hero-theme-btn">
        <svg id="shopSunIcon" width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="color:#fff;display:none;"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <svg id="shopMoonIcon" width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="color:#fff;"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
    </button>
    @auth
    <button type="button" class="hero-edit-btn" onclick="openModal('profileModal')" title="Edit profile">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
    </button>
    @endauth
    <div class="hero-inner">
        <div class="hero-avatar-row">
            @auth
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="hero-avatar" id="heroAvatar">
                @else
                    <div class="hero-avatar-placeholder" id="heroAvatarPlaceholder">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                @endif
                <div>
                    <p class="hero-greeting">Welcome back</p>
                    <p class="hero-name" id="heroName">{{ auth()->user()->name }}</p>
                    <p class="hero-email">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->phone)
                    <p class="hero-email" style="color:#4ade80;margin-top:2px;">📞 {{ auth()->user()->phone }}</p>
                    @else
                    <p class="hero-email" style="color:#ef4444;margin-top:2px;font-size:11px;">⚠ No phone number — <a href="#" onclick="event.preventDefault();openModal('profileModal')" style="color:#facc15;text-decoration:underline;">Add now</a></p>
                    @endif
                </div>
            @else
                <div class="hero-avatar-placeholder" style="display:flex;align-items:center;justify-content:center;">
                    <svg width="32" height="32" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="hero-greeting">Hello there</p>
                    <p class="hero-name">Guest User</p>
                    <p class="hero-email">Login to access all features</p>
                </div>
            @endauth
        </div>
        <div class="hero-stats">
            <div class="hero-stat"><p class="hero-stat-value" id="statOrders">—</p><p class="hero-stat-label">Orders</p></div>
            <div class="hero-stat"><p class="hero-stat-value" id="statSpent">—</p><p class="hero-stat-label">Spent</p></div>
            <div class="hero-stat"><p class="hero-stat-value" id="statDelivered">—</p><p class="hero-stat-label">Delivered</p></div>
            <div class="hero-stat"><p class="hero-stat-value" id="statPending">—</p><p class="hero-stat-label">Active</p></div>
        </div>
    </div>
</div>

<!-- ══════════ PAGE BODY ══════════ -->
<div class="page-body">

    @guest
    <!-- Guest gate -->
    <div style="text-align:center;padding:50px 24px 40px;">
        <p style="font-size:15px;font-weight:700;color:#fff;margin-bottom:8px;">Sign in to your account</p>
        <p style="font-size:13px;color:#6b7280;margin-bottom:28px;">Manage orders, addresses, and more.</p>
        <div style="display:flex;flex-direction:column;gap:10px;max-width:280px;margin:0 auto;">
            <a href="{{ route('restaurant') }}#login" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;border-radius:12px;background:linear-gradient(135deg,#dc2626,#ef4444);color:#fff;font-size:14px;font-weight:700;text-decoration:none;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                Log In
            </a>
            <a href="{{ route('restaurant') }}#register" style="display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;border-radius:12px;background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;font-size:14px;font-weight:700;text-decoration:none;">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Create Account
            </a>
        </div>
    </div>
    @endguest

    @auth
    <!-- ── ACCOUNT ── -->
    <p class="section-label" style="padding-left:32px;">Account</p>
    <div class="section-card">
        <button type="button" class="menu-row" onclick="openModal('profileModal')">
            <div class="menu-icon" style="background:rgba(250,204,21,.1);">
                <svg width="18" height="18" fill="none" stroke="#facc15" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">My Profile</p><p class="menu-sub">Edit name &amp; avatar</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
        <button type="button" class="menu-row" onclick="openModal('addressModal')">
            <div class="menu-icon" style="background:rgba(239,68,68,.1);">
                <svg width="18" height="18" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">My Addresses</p><p class="menu-sub">Manage delivery addresses</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <!-- ── SECURITY ── -->
    <p class="section-label" style="padding-left:32px;">Security</p>
    <div class="section-card">
        @if(auth()->user()->provider !== 'google')
        <button type="button" class="menu-row" onclick="openModal('passwordModal')">
            <div class="menu-icon" style="background:rgba(167,139,250,.1);">
                <svg width="18" height="18" fill="none" stroke="#a78bfa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Change Password</p><p class="menu-sub">Update your login password</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
        @else
        <div class="menu-row" style="cursor:default;">
            <div class="menu-icon" style="background:rgba(167,139,250,.1);">
                <svg width="18" height="18" fill="none" stroke="#a78bfa" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Signed in with Google</p><p class="menu-sub">Password managed by Google</p></div>
        </div>
        @endif
    </div>

    <!-- ── PREFERENCES ── -->
    <p class="section-label" style="padding-left:32px;">Preferences</p>
    <div class="section-card">
        <button type="button" class="menu-row" onclick="document.getElementById('shopThemeToggle').click()">
            <div class="menu-icon" style="background:rgba(251,146,60,.1);">
                <svg width="18" height="18" fill="none" stroke="#fb923c" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">Dark / Light Mode</p><p class="menu-sub">Toggle app appearance</p></div>
            <span id="themeLabel" style="font-size:11px;color:#facc15;font-weight:600;margin-right:6px;"></span>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
    </div>

    <!-- ── ORDERS SHORTCUT ── -->
    <p class="section-label" style="padding-left:32px;">My Orders</p>
    <div class="section-card">
        <a href="{{ route('shop.tracking') }}" class="menu-row" style="text-decoration:none;">
            <div class="menu-icon" style="background:rgba(99,102,241,.1);">
                <svg width="18" height="18" fill="none" stroke="#818cf8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div class="menu-text"><p class="menu-title">View All Orders</p><p class="menu-sub" id="ordersSubtext">Loading…</p></div>
            <svg class="chevron" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    <!-- ── LOGOUT ── -->
    <div class="section-card" style="margin-bottom:20px;">
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="logout-btn" style="width:100%;">
                <div class="menu-icon" style="background:rgba(239,68,68,.1);">
                    <svg width="18" height="18" fill="none" stroke="#f87171" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                <span style="font-size:14px;font-weight:600;color:#f87171;flex:1;text-align:left;">Log Out</span>
            </button>
        </form>
    </div>
    @endauth

    <p style="text-align:center;font-size:11px;color:#1f2937;padding-bottom:8px;">E.U.T Snack House v1.0.0 · Made with ❤️</p>
</div>

<!-- ══════════ BOTTOM NAV ══════════ -->
<nav class="bottom-nav">
    <div class="bottom-nav-inner">
        <a href="{{ route('shop.home') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>Menu
        </a>
        <a href="{{ route('shop.tracking') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Orders
        </a>
        <a href="{{ route('shop.cart') }}" class="bnav-item">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Cart
        </a>
        <a href="{{ route('shop.profile') }}" class="bnav-item active">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>Profile
        </a>
    </div>
</nav>

@auth
<!-- ══════════ EDIT PROFILE MODAL ══════════ -->
<div class="modal-backdrop" id="profileModal">
    <div class="modal-sheet">
        <div class="modal-handle"></div>
        <div class="modal-head">
            <span class="modal-title">Edit Profile</span>
            <button class="modal-close" onclick="closeModal('profileModal')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-alert" id="profileAlert"></div>
            <!-- Avatar preview -->
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding:14px;background:rgba(255,255,255,.03);border-radius:12px;border:1px solid rgba(255,255,255,.06);">
                <div id="avatarPreviewWrap">
                    @if(auth()->user()->avatar)
                        <img id="avatarPreview" src="{{ auth()->user()->avatar }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.2);">
                    @else
                        <div id="avatarPreviewInitial" style="width:60px;height:60px;border-radius:50%;background:rgba(220,38,38,.2);border:2px solid rgba(220,38,38,.3);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#f87171;">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                    @endif
                </div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:#e5e7eb;margin-bottom:4px;">Profile Photo</p>
                    <label for="avatarInput" style="font-size:12px;color:#facc15;cursor:pointer;font-weight:600;">
                        Change photo
                        <input type="file" id="avatarInput" accept="image/*" style="display:none;" onchange="previewAvatar(this)">
                    </label>
                </div>
            </div>
            <div class="form-field">
                <label class="form-label">Full Name</label>
                <input type="text" id="profileName" class="form-input" value="{{ auth()->user()->name }}" placeholder="Your name">
            </div>
            <div class="form-field">
                <label class="form-label">Mobile Number <span style="color:#ef4444;">*</span></label>
                <input type="tel" id="profilePhone" class="form-input" value="{{ auth()->user()->phone }}" placeholder="09XX XXX XXXX" maxlength="11" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            </div>
            <div class="form-field">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" value="{{ auth()->user()->email }}" disabled>
                <p style="font-size:11px;color:#4b5563;margin-top:5px;">Email cannot be changed.</p>
            </div>
            <button class="btn-submit" id="profileSaveBtn" onclick="saveProfile()">Save Changes</button>
        </div>
    </div>
</div>

<!-- ══════════ ADDRESSES MODAL ══════════ -->
<div class="modal-backdrop" id="addressModal">
    <div class="modal-sheet">
        <div class="modal-handle"></div>
        <div class="modal-head">
            <span class="modal-title">My Addresses</span>
            <button class="modal-close" onclick="closeModal('addressModal')">✕</button>
        </div>
        <div class="modal-body">
            <div id="addressList"><p style="text-align:center;color:#4b5563;padding:20px 0;">Loading…</p></div>
            <button class="add-addr-btn" onclick="openAddressForm()">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add New Address
            </button>
        </div>
    </div>
</div>

<!-- ══════════ ADDRESS FORM MODAL ══════════ -->
<div class="modal-backdrop" id="addrFormModal">
    <div class="modal-sheet">
        <div class="modal-handle"></div>
        <div class="modal-head">
            <span class="modal-title" id="addrFormTitle">Add Address</span>
            <button class="modal-close" onclick="closeModal('addrFormModal')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-alert" id="addrAlert"></div>
            <input type="hidden" id="addrId">
            <div class="form-field">
                <label class="form-label">Label (e.g. Home, Work)</label>
                <input type="text" id="addrLabel" class="form-input" placeholder="Home">
            </div>
            <div class="form-field">
                <label class="form-label">Recipient Name</label>
                <input type="text" id="addrName" class="form-input" placeholder="Full name" value="{{ auth()->user()->name }}">
            </div>
            <div class="form-field">
                <label class="form-label">Phone</label>
                <input type="text" id="addrPhone" class="form-input" placeholder="09XX XXX XXXX">
            </div>
            <div class="form-field">
                <label class="form-label">Address</label>
                <input type="text" id="addrAddress" class="form-input" placeholder="Street, building, unit…">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div class="form-field">
                    <label class="form-label">Barangay</label>
                    <input type="text" id="addrBarangay" class="form-input" placeholder="Barangay">
                </div>
                <div class="form-field">
                    <label class="form-label">City</label>
                    <input type="text" id="addrCity" class="form-input" placeholder="City">
                </div>
            </div>
            <div class="form-field" style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <input type="checkbox" id="addrDefault" style="width:16px;height:16px;accent-color:#facc15;">
                <label for="addrDefault" style="font-size:13px;color:#9ca3af;cursor:pointer;">Set as default address</label>
            </div>
            <button class="btn-submit" id="addrSaveBtn" onclick="saveAddress()">Save Address</button>
        </div>
    </div>
</div>

<!-- ══════════ CHANGE PASSWORD MODAL ══════════ -->
<div class="modal-backdrop" id="passwordModal">
    <div class="modal-sheet">
        <div class="modal-handle"></div>
        <div class="modal-head">
            <span class="modal-title">Change Password</span>
            <button class="modal-close" onclick="closeModal('passwordModal')">✕</button>
        </div>
        <div class="modal-body">
            <div class="form-alert" id="pwAlert"></div>
            <div class="form-field">
                <label class="form-label">Current Password</label>
                <input type="password" id="pwCurrent" class="form-input" placeholder="Enter current password">
            </div>
            <div class="form-field">
                <label class="form-label">New Password</label>
                <input type="password" id="pwNew" class="form-input" placeholder="Min 6 characters">
            </div>
            <div class="form-field">
                <label class="form-label">Confirm New Password</label>
                <input type="password" id="pwConfirm" class="form-input" placeholder="Repeat new password">
            </div>
            <button class="btn-submit" id="pwSaveBtn" onclick="savePassword()">Update Password</button>
        </div>
    </div>
</div>
@endauth

<script>
const CSRF    = document.querySelector('meta[name="csrf-token"]').content;
const IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};
const ORDERS_URL = '{{ auth()->check() ? route("orders.index") : "" }}';
const DEF_NAME   = {{ auth()->check() ? json_encode(auth()->user()->name) : '""' }};

/* ── Theme ── */
function applyTheme(t) {
    document.documentElement.classList.toggle('light-mode', t === 'light');
    document.getElementById('shopSunIcon').style.display  = t === 'dark'  ? 'block' : 'none';
    document.getElementById('shopMoonIcon').style.display = t === 'light' ? 'block' : 'none';
    const lbl = document.getElementById('themeLabel');
    if (lbl) lbl.textContent = t === 'dark' ? 'Dark' : 'Light';
}

document.addEventListener('DOMContentLoaded', () => {
    applyTheme(localStorage.getItem('eutTheme') || 'dark');
    document.getElementById('shopThemeToggle').addEventListener('click', () => {
        const t = (localStorage.getItem('eutTheme') || 'dark') === 'dark' ? 'light' : 'dark';
        localStorage.setItem('eutTheme', t);
        applyTheme(t);
    });
    if (IS_AUTH) loadStats();
    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
    });
});

/* ── Modal helpers ── */
function openModal(id) {
    const el = document.getElementById(id);
    if (!el) { console.error('Modal not found:', id); return; }
    el.classList.add('open');
    if (id === 'addressModal') loadAddresses();
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('open');
}

/* ── Helpers ── */
function showAlert(el, type, msg) {
    if (!el) return;
    el.className = 'form-alert' + (type ? ' ' + type : '');
    el.textContent = msg;
    el.style.display = msg ? 'block' : 'none';
}
function escHtml(s) {
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}


/* ── Load Order Stats ── */
async function loadStats() {
    try {
        const res  = await fetch(ORDERS_URL, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        if (!res.ok) return;
        const data = await res.json();
        const active    = data.active    || [];
        const past      = data.past      || [];
        const cancelled = data.cancelled || [];
        const total     = active.length + past.length + cancelled.length;
        const delivered = past.length;
        const spent     = past.reduce((s, o) => s + parseFloat(o.total || 0), 0);

        const g = id => document.getElementById(id);
        if (g('statOrders'))    g('statOrders').textContent    = total;
        if (g('statSpent'))     g('statSpent').textContent     = '₱' + (spent >= 1000 ? (spent/1000).toFixed(1)+'k' : Math.round(spent));
        if (g('statDelivered')) g('statDelivered').textContent = delivered;
        if (g('statPending'))   g('statPending').textContent   = active.length;
        const sub = g('ordersSubtext');
        if (sub) sub.textContent = active.length > 0
            ? `${active.length} active order${active.length > 1 ? 's' : ''}`
            : `${total} order${total !== 1 ? 's' : ''} total`;
    } catch (e) {
        ['statOrders','statSpent','statDelivered','statPending'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '0';
        });
    }
}
/* ── Avatar preview ── */
function previewAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const wrap = document.getElementById('avatarPreviewWrap');
        wrap.innerHTML = `<img id="avatarPreview" src="${e.target.result}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.2);">`;
    };
    reader.readAsDataURL(input.files[0]);
}

async function saveProfile() {
    const btn  = document.getElementById('profileSaveBtn');
    const alert = document.getElementById('profileAlert');
    const name  = document.getElementById('profileName').value.trim();
    const phone = document.getElementById('profilePhone').value.trim();
    showAlert(alert, '', '');

    if (!name)  { showAlert(alert, 'error', 'Name is required.'); return; }
    if (!phone) { showAlert(alert, 'error', 'Mobile number is required.'); return; }
    if (phone.length < 10) { showAlert(alert, 'error', 'Please enter a valid mobile number.'); return; }

    btn.disabled = true; btn.textContent = 'Saving…';
    const fd = new FormData();
    fd.append('name', name);
    fd.append('phone', phone);
    const avatarFile = document.getElementById('avatarInput').files[0];
    if (avatarFile) fd.append('avatar', avatarFile);

    try {
        const res = await fetch('/profile', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: fd
        });
        const data = await res.json();
        if (data.success) {
            showAlert(alert, 'success', '✅ Profile updated!');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showAlert(alert, 'error', data.message || 'Failed to save.');
        }
    } catch(e) {
        showAlert(alert, 'error', 'Network error. Try again.');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Changes';
    }
}

/* ── ADDRESSES ── */
let editingAddressId = null;

async function loadAddresses() {
    const list = document.getElementById('addressList');
    list.innerHTML = '<p style="text-align:center;color:#4b5563;padding:20px 0;">Loading…</p>';
    try {
        const res  = await fetch('/addresses', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
        const data = await res.json();
        renderAddresses(data.addresses || []);
    } catch(e) {
        list.innerHTML = '<p style="text-align:center;color:#f87171;padding:20px 0;">Failed to load addresses.</p>';
    }
}

function renderAddresses(addresses) {
    const list = document.getElementById('addressList');
    if (!addresses.length) {
        list.innerHTML = '<p style="text-align:center;color:#4b5563;padding:20px 0;font-size:13px;">No addresses saved yet.</p>';
        return;
    }
    list.innerHTML = addresses.map(a => `
        <div class="addr-card ${a.is_default ? 'default' : ''}">
            ${a.is_default ? '<span class="default-star">★</span>' : ''}
            <span class="addr-label-badge">${escHtml(a.label || 'Home')}</span>
            <p class="addr-name">${escHtml(a.recipient_name)} · ${escHtml(a.phone)}</p>
            <p class="addr-text">${escHtml(a.address)}${a.barangay ? ', ' + escHtml(a.barangay) : ''}${a.city ? ', ' + escHtml(a.city) : ''}</p>
            <div class="addr-actions">
                <button class="addr-btn" onclick="editAddress(${a.id})">Edit</button>
                ${!a.is_default ? `<button class="addr-btn primary" onclick="setDefaultAddr(${a.id})">Set Default</button>` : ''}
                <button class="addr-btn danger" onclick="deleteAddr(${a.id})">Delete</button>
            </div>
        </div>`).join('');
}

function openAddressForm(addr) {
    editingAddressId = addr ? addr.id : null;
    document.getElementById('addrFormTitle').textContent = addr ? 'Edit Address' : 'Add Address';
    document.getElementById('addrId').value       = addr ? addr.id : '';
    document.getElementById('addrLabel').value    = addr ? addr.label : '';
    document.getElementById('addrName').value     = addr ? addr.recipient_name : '{{ auth()->user()->name }}';
    document.getElementById('addrPhone').value    = addr ? addr.phone : '';
    document.getElementById('addrAddress').value  = addr ? addr.address : '';
    document.getElementById('addrBarangay').value = addr ? (addr.barangay||'') : '';
    document.getElementById('addrCity').value     = addr ? (addr.city||'') : '';
    document.getElementById('addrDefault').checked = addr ? !!addr.is_default : false;
    document.getElementById('addrAlert').style.display = 'none';
    openModal('addrFormModal');
}

async function editAddress(id) {
    const res  = await fetch('/addresses', { headers: { 'Accept':'application/json','X-CSRF-TOKEN':CSRF }});
    const data = await res.json();
    const addr = (data.addresses||[]).find(a => a.id === id);
    if (addr) openAddressForm(addr);
}

async function setDefaultAddr(id) {
    await fetch(`/addresses/${id}/default`, { method:'PATCH', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
    loadAddresses();
}

async function deleteAddr(id) {
    if (!confirm('Delete this address?')) return;
    await fetch(`/addresses/${id}`, { method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
    loadAddresses();
}

async function saveAddress() {
    const btn   = document.getElementById('addrSaveBtn');
    const alert = document.getElementById('addrAlert');
    showAlert(alert, '', '');

    const body = {
        label:          document.getElementById('addrLabel').value || 'Home',
        recipient_name: document.getElementById('addrName').value.trim(),
        phone:          document.getElementById('addrPhone').value.trim(),
        address:        document.getElementById('addrAddress').value.trim(),
        barangay:       document.getElementById('addrBarangay').value.trim(),
        city:           document.getElementById('addrCity').value.trim(),
        is_default:     document.getElementById('addrDefault').checked,
    };
    if (!body.recipient_name || !body.phone || !body.address) {
        showAlert(alert, 'error', 'Name, phone and address are required.'); return;
    }

    btn.disabled = true; btn.textContent = 'Saving…';
    const id     = editingAddressId;
    const url    = id ? `/addresses/${id}` : '/addresses';
    const method = id ? 'PUT' : 'POST';
    try {
        const res  = await fetch(url, { method, headers:{'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'}, body: JSON.stringify(body) });
        const data = await res.json();
        if (data.success) {
            closeModal('addrFormModal');
            loadAddresses();
        } else {
            showAlert(alert, 'error', data.message || 'Failed to save address.');
        }
    } catch(e) {
        showAlert(alert, 'error', 'Network error.');
    } finally {
        btn.disabled = false; btn.textContent = 'Save Address';
    }
}

/* ── CHANGE PASSWORD ── */
async function savePassword() {
    const btn   = document.getElementById('pwSaveBtn');
    const alert = document.getElementById('pwAlert');
    showAlert(alert,'','');

    const current  = document.getElementById('pwCurrent').value;
    const newPw    = document.getElementById('pwNew').value;
    const confirm  = document.getElementById('pwConfirm').value;

    if (!current || !newPw || !confirm) { showAlert(alert,'error','All fields are required.'); return; }
    if (newPw !== confirm)              { showAlert(alert,'error','New passwords do not match.'); return; }
    if (newPw.length < 6)               { showAlert(alert,'error','Password must be at least 6 characters.'); return; }

    btn.disabled = true; btn.textContent = 'Updating…';
    try {
        const res  = await fetch('/profile/password', {
            method: 'POST',
            headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json' },
            body: JSON.stringify({ current_password:current, password:newPw, password_confirmation:confirm })
        });
        const data = await res.json();
        if (data.success) {
            showAlert(alert,'success','Password updated successfully!');
            document.getElementById('pwCurrent').value = '';
            document.getElementById('pwNew').value = '';
            document.getElementById('pwConfirm').value = '';
            setTimeout(() => closeModal('passwordModal'), 1500);
        } else {
            showAlert(alert,'error', data.message || 'Failed to update password.');
        }
    } catch(e) {
        showAlert(alert,'error','Network error.');
    } finally {
        btn.disabled = false; btn.textContent = 'Update Password';
    }
}

// ── Mobile back button guard ──────────────────────────────
history.pushState({ page: 'profile' }, '', window.location.href);
window.addEventListener('popstate', function() {
    history.pushState({ page: 'profile' }, '', window.location.href);
});

@auth
// ── Echo: live order count on profile stats ───────────────
if (window.Echo) {
    window.Echo.private('orders.{{ auth()->id() }}')
        .listen('.order.updated', (order) => {
            // Refresh order stats without a full page reload
            fetch('/orders', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' } })
                .then(r => r.json())
                .then(data => {
                    const orders = Array.isArray(data) ? data : (data.orders || []);
                    const total     = orders.length;
                    const delivered = orders.filter(o => o.status === 'delivered').length;
                    const active    = orders.filter(o => !['delivered','cancelled'].includes(o.status)).length;
                    // Update stat chips if they exist (blade renders them server-side but we can update the numbers)
                    document.querySelectorAll('[data-stat="total"]').forEach(el => el.textContent = total);
                    document.querySelectorAll('[data-stat="delivered"]').forEach(el => el.textContent = delivered);
                    document.querySelectorAll('[data-stat="active"]').forEach(el => el.textContent = active);

                    // Show a subtle toast for status changes
                    if (['delivered','cancelled','out_for_delivery'].includes(order.status)) {
                        const msgs = {
                            delivered: '🎉 Your order was delivered!',
                            cancelled: '❌ Order was cancelled.',
                            out_for_delivery: '🛵 Your order is on the way!',
                        };
                        const t = document.createElement('div');
                        Object.assign(t.style, {
                            position:'fixed', bottom:'90px', left:'50%', transform:'translateX(-50%)',
                            background:'#0d1f17', border:'1px solid rgba(74,222,128,.3)', color:'#4ade80',
                            padding:'12px 22px', borderRadius:'99px', fontSize:'13px', fontWeight:'700',
                            zIndex:'9999', boxShadow:'0 4px 24px rgba(0,0,0,.5)',
                        });
                        t.textContent = msgs[order.status] || order.status_label;
                        document.body.appendChild(t);
                        setTimeout(() => t.remove(), 3000);
                    }
                })
                .catch(() => {});
        });
}
@endauth
</script>
</body>
</html>
