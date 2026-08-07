<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chef') — EUT Kitchen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons — pinned stable version, deferred -->
    <script defer src="https://cdn.jsdelivr.net/npm/lucide@0.441.0/dist/umd/lucide.min.js" onload="lucide.createIcons()"></script>
    @stack('head')
    <style>
        /* ── CSS VARIABLES ─────────────────────────────────── */
        :root {
            --bg-body:        #0a0a0a;
            --bg-nav:         #111111;
            --bg-card:        #161616;
            --bg-section:     #111111;
            --bg-table-head:  #1a1a1a;
            --bg-filter:      #161616;
            --bg-input:       #111111;
            --bg-hover-row:   rgba(255,255,255,0.03);
            --border-nav:     rgba(220,38,38,0.35);
            --border-card:    rgba(255,255,255,0.08);
            --border-section: rgba(255,255,255,0.07);
            --border-input:   rgba(255,255,255,0.14);
            --border-ghost:   rgba(255,255,255,0.14);
            --border-divider: rgba(255,255,255,0.07);
            --text-body:      #d4d4d4;
            --text-muted:     #737373;
            --text-subtle:    #a3a3a3;
            --text-heading:   #facc15;
            --text-strong:    #ffffff;
            --text-th:        #a3a3a3;
            --text-input:     #d4d4d4;
            --text-label:     #a3a3a3;
            --shadow-nav:     0 2px 12px rgba(0,0,0,0.6);
            --shadow-card:    none;
            --accent:         #dc2626;
            --accent-hover:   #b91c1c;
            --accent-soft:    rgba(220,38,38,0.1);
            --accent-border:  rgba(220,38,38,0.35);
            --accent-avatar:  rgba(220,38,38,0.45);
            --accent-badge-bg:rgba(220,38,38,0.12);
            --accent-badge-tx:#dc2626;
        }

        *, *::before, *::after { box-sizing: border-box; }
        body { font-family:'Inter',sans-serif; background:var(--bg-body)!important; color:var(--text-body)!important; margin:0; }

        .admin-nav { background:var(--bg-nav); border-bottom:1px solid var(--border-nav); box-shadow:var(--shadow-nav); }
        .nav-link { display:flex; align-items:center; gap:.45rem; padding:.55rem 1rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:var(--text-subtle); transition:all .2s; text-decoration:none; }
        .nav-link:hover { color:#dc2626; background:rgba(220,38,38,.07); }
        .nav-link.active { color:#dc2626; background:rgba(220,38,38,.1); border-bottom:2px solid #dc2626; border-radius:.5rem .5rem 0 0; }

        .admin-content { min-height:calc(100vh - 64px); padding:2rem; }
        
        /* Modal basics */
        .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); backdrop-filter:blur(4px); z-index:999; align-items:center; justify-content:center; padding:1rem; }
        .modal-backdrop.open { display:flex; }
        .modal-box { background:var(--bg-card); border:1px solid var(--border-card); border-radius:1rem; width:100%; max-width:540px; box-shadow:0 24px 64px rgba(0,0,0,.45); overflow:hidden; }
        .modal-box.modal-lg { max-width:680px; }
        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:1.1rem 1.4rem; border-bottom:1px solid var(--border-divider); }
        .modal-title { font-size:.9375rem; font-weight:700; color:var(--text-strong); margin:0; }
        .modal-close { background:none; border:none; cursor:pointer; padding:.3rem; border-radius:.375rem; color:var(--text-muted); display:flex; align-items:center; }
        .modal-body { padding:1.4rem; display:flex; flex-direction:column; gap:1rem; max-height:65vh; overflow-y:auto; }

        .flash-success { background:rgba(34,197,94,.09); border:1px solid rgba(34,197,94,.28); color:#16a34a; border-radius:.625rem; padding:.75rem 1rem; margin-bottom:1.25rem; font-size:.875rem; }
        .flash-error   { background:rgba(239,68,68,.09); border:1px solid rgba(239,68,68,.28); color:#dc2626; border-radius:.625rem; padding:.75rem 1rem; margin-bottom:1.25rem; font-size:.875rem; }
    </style>
</head>
<body class="admin-body">

<nav class="admin-nav" style="position:sticky;top:0;z-index:50;">
    <div style="max-width:1536px;margin:0 auto;padding:0 1.5rem;height:64px;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
        <div style="display:flex;align-items:center;gap:.5rem;text-decoration:none;">
            <span style="font-family:'Playfair Display',serif; color:#fff; font-weight:700; font-size:1.25rem; letter-spacing:.05em;">EUT</span>
            <span style="color:rgba(255,255,255,.9); border:1px solid rgba(255,255,255,.3); font-size:.65rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; padding:.2rem .55rem; border-radius:.3rem; background:rgba(255,255,255,.08);">Kitchen</span>
        </div>

        <div style="display:flex;align-items:center;gap:.25rem;overflow-x:auto;flex:1;">
            <a href="{{ route('chef.dashboard') }}" class="nav-link {{ request()->routeIs('chef.dashboard') ? 'active' : '' }}">
                <i data-lucide="chef-hat" style="width:16px;height:16px;"></i>
                Kitchen Board
            </a>
        </div>

        <div style="display:flex;align-items:center;gap:.75rem;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:.5rem;padding-left:.75rem;border-left:1px solid rgba(255,255,255,.15);">
                <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.875rem;background:rgba(255,255,255,.2);color:#fff;border:2px solid rgba(255,255,255,.3);flex-shrink:0;">
                    {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                </div>
                <div>
                    <p style="font-size:.75rem; font-weight:600; color:#fff; line-height:1; margin:0;">{{ auth()->user()->name }}</p>
                    <p style="font-size:.7rem; color:rgba(255,255,255,.6); margin:.2rem 0 0;">{{ auth()->user()->isAdmin() ? 'Admin' : 'Chef' }}</p>
                </div>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" title="Back to Admin" style="margin-left:.25rem;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);border-radius:.4rem;cursor:pointer;padding:.25rem .6rem;color:#f59e0b;display:flex;align-items:center;gap:.3rem;text-decoration:none;font-size:.7rem;font-weight:700;">
                    <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Admin
                </a>
                @else
                <form method="POST" action="{{ route('auth.logout') }}" style="margin-left:.25rem;">
                    @csrf
                    <button type="submit" title="Logout" style="background:none;border:none;cursor:pointer;padding:.25rem;color:rgba(255,255,255,.5);display:flex;align-items:center;">
                        <i data-lucide="log-out" style="width:16px;height:16px;"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</nav>

<main class="admin-content" style="max-width:1536px;margin:0 auto;">
    @if(session('success'))
        <div class="flash-success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error">✕ {{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<script>
    function openModal(id){ var el=document.getElementById(id); if(el){el.classList.add('open');document.body.style.overflow='hidden';} }
    function closeModal(id){ var el=document.getElementById(id); if(el){el.classList.remove('open');document.body.style.overflow='';} }
    function closeModalBackdrop(e,id){ if(e.target===document.getElementById(id)) closeModal(id); }
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    window.addEventListener('load', function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@stack('scripts')
</body>
</html>
