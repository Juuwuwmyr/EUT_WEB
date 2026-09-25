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
@php $maintenance = false; @endphp
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
        <svg width="38" height="23" viewBox="0 0 80 48" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M14.8 16.2L11.2 27.8H11L7.3 16.2H4.8L9.6 29.8H12.5L17.3 16.2H14.8Z" fill="#FF9900"/>
            <path d="M24.5 16C21.2 16 18.8 18.3 18.8 23C18.8 27.5 21.1 30 24.6 30C26.8 30 28.4 29.1 29.5 27.6L27.9 26.2C27.1 27.2 26.1 27.8 24.7 27.8C22.8 27.8 21.5 26.6 21.3 24.4H29.9C29.9 24.1 30 23.6 30 23.1C30 18.9 27.8 16 24.5 16ZM21.3 22.4C21.5 20.4 22.7 18.2 24.5 18.2C26.4 18.2 27.5 20.2 27.6 22.4H21.3Z" fill="#FF9900"/>
            <path d="M38.2 22.5C36.5 22 35 21.6 35 20.5C35 19.5 36 18.9 37.3 18.9C38.7 18.9 39.9 19.6 40.7 20.5L42.3 19C41.1 17.6 39.4 16.7 37.3 16.7C34.5 16.7 32.5 18.3 32.5 20.7C32.5 23.3 34.8 24 36.8 24.6C38.5 25.1 40 25.5 40 26.7C40 27.8 39 28.4 37.5 28.4C35.8 28.4 34.4 27.5 33.5 26.4L31.8 27.9C33.1 29.4 35 30.3 37.5 30.3C40.5 30.3 42.5 28.7 42.5 26.4C42.5 23.7 40.1 23 38.2 22.5Z" fill="#FF9900"/>
            <path d="M12 36C16.5 39.5 22.5 41.5 29 41.5C37.5 41.5 45.2 38.2 50.8 32.8C51.5 32.1 50.9 31.1 50 31.5C44.2 34 37.5 35.5 30.5 35.5C23 35.5 15.8 33.7 9.5 30.5C8.6 30 8 31.2 8.8 31.8L12 36Z" fill="#FF9900"/>
            <path d="M52.5 30.5C53.5 29.3 56.5 29.8 57 31C57.5 32.2 54.8 35.5 53.5 34.8C52.8 34.5 51.5 31.7 52.5 30.5Z" fill="#FF9900"/>
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
