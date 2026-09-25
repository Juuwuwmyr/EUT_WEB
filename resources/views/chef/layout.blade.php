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
                <a href="{{ route('admin.dashboard') }}" id="adminBackBtn" title="Back to Admin" style="margin-left:.25rem;background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);border-radius:.4rem;cursor:pointer;padding:.25rem .6rem;color:#f59e0b;display:flex;align-items:center;gap:.3rem;text-decoration:none;font-size:.7rem;font-weight:700;">
                    <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Admin
                </a>
                <script>
                    (function(){
                        var last = localStorage.getItem('lastAdminPage');
                        if (last && last.indexOf(window.location.origin) === 0) {
                            document.getElementById('adminBackBtn').href = last;
                        }
                    })();
                </script>
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

{{-- ══════════ MAINTENANCE MODE (Kitchen) ══════════ --}}
@php $maintenance = true; @endphp
@if($maintenance)
<style>
#maint-screen{position:fixed;inset:0;z-index:99999;background:#0a0a0a;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:1.25rem;padding:2rem;text-align:center;font-family:'Inter',sans-serif;}
#maint-screen .m-icon{width:80px;height:48px;border-radius:.75rem;background:#111827;border:1px solid #1f2937;display:flex;align-items:center;justify-content:center;margin-bottom:.25rem;padding:0 .75rem;}
#maint-screen .m-icon svg{display:block;}
#maint-screen .m-brand{font-size:.7rem;color:#374151;letter-spacing:.1em;text-transform:uppercase;font-weight:500;}
#maint-screen h1{margin:0;font-size:1.5rem;font-weight:700;color:#f9fafb;letter-spacing:-.02em;line-height:1.2;}
#maint-screen p{margin:0;font-size:.875rem;color:#6b7280;max-width:320px;line-height:1.7;}
#maint-screen .m-timer{font-family:monospace;font-size:2.75rem;font-weight:800;color:#f59e0b;line-height:1;letter-spacing:.02em;}
#maint-screen .m-note{font-size:.72rem;color:#1f2937;}
</style>
<div id="maint-screen">
    <div class="m-icon">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 304 182" width="54" height="32">
            <path d="M86.4 66.4c0 3.7.4 6.7 1.1 8.9.8 2.2 1.8 4.6 3.2 7.2.5.8.7 1.6.7 2.3 0 1-.6 2-1.9 3l-6.3 4.2c-.9.6-1.8.9-2.6.9-1 0-2-.5-3-1.4-1.4-1.5-2.6-3.1-3.6-4.7-1-1.7-2-3.6-3.1-5.9-7.8 9.2-17.6 13.8-29.4 13.8-8.4 0-15.1-2.4-20-7.2-4.9-4.8-7.4-11.2-7.4-19.2 0-8.5 3-15.4 9.1-20.6 6.1-5.2 14.2-7.8 24.5-7.8 3.4 0 6.9.3 10.6.8 3.7.5 7.5 1.3 11.5 2.2v-7.3c0-7.6-1.6-12.9-4.7-16-3.2-3.1-8.6-4.6-16.3-4.6-3.5 0-7.1.4-10.8 1.3-3.7.9-7.3 2-10.8 3.4-1.6.7-2.8 1.1-3.5 1.3-.7.2-1.2.3-1.6.3-1.4 0-2.1-1-2.1-3.1v-4.9c0-1.6.2-2.8.7-3.5.5-.7 1.4-1.4 2.8-2.1 3.5-1.8 7.7-3.3 12.6-4.5 4.9-1.3 10.1-1.9 15.6-1.9 11.9 0 20.6 2.7 26.2 8.1 5.5 5.4 8.3 13.6 8.3 24.6v32.4zm-40.6 15.2c3.3 0 6.7-.6 10.3-1.8 3.6-1.2 6.8-3.4 9.5-6.4 1.6-1.9 2.8-4 3.4-6.4.6-2.4 1-5.3 1-8.7v-4.2c-2.9-.7-6-1.3-9.2-1.7-3.2-.4-6.3-.6-9.4-.6-6.7 0-11.6 1.3-14.9 4-3.3 2.7-4.9 6.5-4.9 11.5 0 4.7 1.2 8.2 3.7 10.6 2.4 2.5 5.9 3.7 10.5 3.7zm80.3 10.8c-1.8 0-3-.3-3.8-1-.8-.6-1.5-2-2.1-3.9L96.7 10.2c-.6-2-.9-3.3-.9-4 0-1.6.8-2.5 2.4-2.5h9.8c1.9 0 3.2.3 3.9 1 .8.6 1.4 2 2 3.9l19.4 76.5 18-76.5c.5-2 1.1-3.3 1.9-3.9.8-.6 2.2-1 4-1h8c1.9 0 3.2.3 4.1 1 .8.6 1.5 2 1.9 3.9l18.2 77.5 20-77.5c.6-2 1.3-3.3 2-3.9.8-.6 2.1-1 3.9-1h9.3c1.6 0 2.5.8 2.5 2.5 0 .5-.1 1-.2 1.6-.1.6-.3 1.4-.7 2.5l-23.7 77.3c-.6 2-1.3 3.3-2.1 3.9-.8.6-2.1 1-3.8 1h-8.6c-1.9 0-3.2-.3-4.1-1-.8-.7-1.5-2-1.9-4L156 23l-17.9 68.4c-.5 2-1.1 3.3-1.9 4-.8.6-2.2 1-4 1h-8.1zm126.4 2.7c-5.2 0-10.4-.6-15.4-1.8-5-1.2-8.9-2.5-11.5-4-1.6-.9-2.7-1.9-3.1-2.8-.4-.9-.6-1.9-.6-2.8v-5.1c0-2.1.8-3.1 2.3-3.1.6 0 1.2.1 1.8.3.6.2 1.5.6 2.5 1 3.4 1.5 7.1 2.7 11 3.5 4 .8 7.9 1.2 11.9 1.2 6.3 0 11.2-1.1 14.6-3.3 3.4-2.2 5.2-5.4 5.2-9.5 0-2.8-.9-5.1-2.7-7-1.8-1.9-5.2-3.6-10.1-5.2l-14.5-4.5c-7.3-2.3-12.7-5.7-16-10.2-3.3-4.4-5-9.3-5-14.5 0-4.2.9-7.9 2.7-11.1 1.8-3.2 4.2-6 7.2-8.2 3-2.3 6.4-4 10.4-5.2 4-1.2 8.2-1.7 12.6-1.7 2.2 0 4.5.1 6.7.4 2.3.3 4.4.7 6.5 1.1 2 .5 3.9 1 5.7 1.6 1.8.6 3.2 1.2 4.2 1.8 1.4.8 2.4 1.6 3 2.5.6.8.9 1.9.9 3.3v4.7c0 2.1-.8 3.2-2.3 3.2-.8 0-2.1-.4-3.8-1.2-5.7-2.6-12.1-3.9-19.2-3.9-5.7 0-10.2 1-13.4 2.9-3.2 1.9-4.8 4.9-4.8 9 0 2.8 1 5.2 3 7.1 2 1.9 5.7 3.8 11 5.5l14.2 4.5c7.2 2.3 12.4 5.5 15.5 9.6 3.1 4.1 4.6 8.8 4.6 14 0 4.3-.9 8.2-2.6 11.6-1.8 3.4-4.2 6.4-7.3 8.8-3.1 2.5-6.8 4.3-11.1 5.6-4.5 1.4-9.2 2.1-14.3 2.1z" fill="#232F3E"/>
            <path d="M273.5 143.7c-32.9 24.3-80.7 37.2-121.8 37.2-57.6 0-109.5-21.3-148.7-56.7-3.1-2.8-.3-6.6 3.4-4.4 42.4 24.6 94.7 39.5 148.8 39.5 36.5 0 76.6-7.6 113.5-23.2 5.5-2.5 10.2 3.6 4.8 7.6z" fill="#FF9900"/>
            <path d="M287.2 128.1c-4.2-5.4-27.8-2.6-38.5-1.3-3.2.4-3.7-2.4-.8-4.5 18.8-13.2 49.7-9.4 53.3-5 3.6 4.5-1 35.4-18.6 50.2-2.7 2.3-5.3 1.1-4.1-1.9 3.9-9.9 12.9-32.2 8.7-37.5z" fill="#FF9900"/>
        </svg>
    </div>
    <span class="m-brand">AWS Server</span>
    <h1>We'll be right back.</h1>
    <p>Our AWS server is currently down for scheduled maintenance.<br>Estimated back online in:</p>
    <div class="m-timer" id="maint-timer-chef">5:00</div>
    <p class="m-note" id="maint-note-chef">This page refreshes automatically.</p>
</div>
<script>
(function(){
    var end=Date.now()+5*60*1000;
    function fmt(ms){var s=Math.max(0,Math.ceil(ms/1000));return Math.floor(s/60)+':'+(('0'+(s%60)).slice(-2));}
    var timer=document.getElementById('maint-timer-chef');
    var note=document.getElementById('maint-note-chef');
    var desc=timer&&timer.previousElementSibling;
    var done=false;
    setInterval(function(){
        if(done)return;
        var rem=end-Date.now();
        if(rem>0){if(timer)timer.textContent=fmt(rem);}
        else{done=true;if(timer){timer.style.fontSize='.875rem';timer.style.color='#ef4444';timer.style.fontFamily='inherit';timer.style.fontWeight='600';timer.textContent='Service Disrupted';}if(desc)desc.textContent='The AWS server is taking longer than expected to recover.';if(note){note.style.color='#6b7280';note.style.fontSize='.8rem';note.textContent='This may take up to 24 hours. We apologize for the inconvenience — please check back later.';}}
    },500);
})();
</script>
@endif
</body>
</html>
