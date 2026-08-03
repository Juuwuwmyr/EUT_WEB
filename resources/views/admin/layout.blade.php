<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — EUT Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.pwa-head')
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons — pinned stable version, deferred -->
    <script defer src="https://cdn.jsdelivr.net/npm/lucide@0.441.0/dist/umd/lucide.min.js" onload="lucide.createIcons()"></script>
    @stack('head')
    <style>
        /* ── CSS VARIABLES ─────────────────────────────────── */
        :root {
            /* ── DARK MODE (default) ── */
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
            /* accent = red in dark */
            --accent:         #dc2626;
            --accent-hover:   #b91c1c;
            --accent-soft:    rgba(220,38,38,0.1);
            --accent-border:  rgba(220,38,38,0.35);
            --accent-avatar:  rgba(220,38,38,0.45);
            --accent-badge-bg:rgba(220,38,38,0.12);
            --accent-badge-tx:#dc2626;
        }
        html.light {
            /* ── LIGHT MODE ── */
            --bg-body:        #f0f4f8;
            --bg-nav:         #0a0a0a;
            --bg-card:        #ffffff;
            --bg-section:     #ffffff;
            --bg-table-head:  #f8fafc;
            --bg-filter:      #f1f5f9;
            --bg-input:       #ffffff;
            --bg-hover-row:   rgba(15,23,42,0.03);
            --border-nav:     rgba(0,0,0,0.0);
            --border-card:    rgba(0,0,0,0.08);
            --border-section: rgba(0,0,0,0.07);
            --border-input:   rgba(0,0,0,0.18);
            --border-ghost:   rgba(0,0,0,0.18);
            --border-divider: rgba(0,0,0,0.07);
            --text-body:      #334155;
            --text-muted:     #94a3b8;
            --text-subtle:    #cbd5e1;
            --text-heading:   #0f172a;
            --text-strong:    #0f172a;
            --text-th:        #475569;
            --text-input:     #0f172a;
            --text-label:     #64748b;
            --shadow-nav:     0 2px 20px rgba(0,0,0,0.35);
            --shadow-card:    0 1px 6px rgba(0,0,0,0.07), 0 4px 16px rgba(0,0,0,0.04);
            /* accent = charcoal/slate in light */
            --accent:         #0f172a;
            --accent-hover:   #1e293b;
            --accent-soft:    rgba(15,23,42,0.07);
            --accent-border:  rgba(15,23,42,0.25);
            --accent-avatar:  rgba(15,23,42,0.5);
            --accent-badge-bg:rgba(15,23,42,0.1);
            --accent-badge-tx:#0f172a;
        }
        /* ── BASE ──────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family:'Inter',sans-serif; background:var(--bg-body)!important; color:var(--text-body)!important; margin:0; transition:background .3s,color .3s; }

        /* ── NAV ────────────────────────────────────────────── */
        .admin-nav { background:var(--bg-nav); border-bottom:1px solid var(--border-nav); box-shadow:var(--shadow-nav); transition:background .3s,border-color .3s,box-shadow .3s; }

        /* ── NAV LINKS ──────────────────────────────────────── */
        .nav-link { display:flex; align-items:center; gap:.45rem; padding:.55rem 1rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:var(--text-subtle); transition:all .2s; white-space:nowrap; text-decoration:none; }
        .nav-link:hover { color:#fff; background:rgba(255,255,255,.1); }
        .nav-link.active { color:#fff; background:rgba(255,255,255,.15); border-bottom:2px solid #fff; border-radius:.5rem .5rem 0 0; }
        /* dark mode nav links */
        html.dark .nav-link { color:var(--text-subtle); }
        html.dark .nav-link:hover { color:#dc2626; background:rgba(220,38,38,.07); }
        html.dark .nav-link.active { color:#dc2626; background:rgba(220,38,38,.1); border-bottom-color:#dc2626; }

        /* ── CONTENT ────────────────────────────────────────── */
        .admin-content { min-height:calc(100vh - 64px); padding:2rem; }

        /* ── STAT CARDS ─────────────────────────────────────── */
        .stat-card { background:var(--bg-card); border:1px solid var(--border-card); border-radius:.75rem; padding:1.5rem; box-shadow:var(--shadow-card); transition:border-color .2s,background .3s,box-shadow .3s; }
        .stat-card:hover { border-color:var(--accent-border); box-shadow:0 4px 20px rgba(0,0,0,.1); }

        /* ── TABLES ─────────────────────────────────────────── */
        .admin-table { width:100%; border-collapse:collapse; }
        .admin-table th { background:var(--bg-table-head); color:var(--text-th); font-size:.75rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; padding:.75rem 1rem; text-align:left; border-bottom:1px solid var(--border-card); transition:background .3s,color .3s; }
        .admin-table td { padding:.85rem 1rem; border-bottom:1px solid var(--border-section); font-size:.875rem; vertical-align:middle; color:var(--text-body); transition:background .3s,color .3s; }
        .admin-table tr:last-child td { border-bottom:none; }
        .admin-table tr:hover td { background:var(--bg-hover-row); }

        /* ── BADGES ─────────────────────────────────────────── */
        .badge { display:inline-flex; align-items:center; padding:.2rem .65rem; border-radius:9999px; font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; }
        .badge-admin     { background:var(--accent-badge-bg); color:var(--accent-badge-tx); }
        .badge-user      { background:rgba(100,100,100,.12); color:var(--text-subtle); }
        .badge-pending   { background:rgba(234,179,8,.12);  color:#d97706; }
        .badge-preparing { background:rgba(59,130,246,.12); color:#2563eb; }
        .badge-out       { background:rgba(168,85,247,.12); color:#7c3aed; }
        .badge-delivered { background:rgba(34,197,94,.12);  color:#16a34a; }
        .badge-cancelled { background:rgba(239,68,68,.12);  color:#dc2626; }

        /* ── BUTTONS ─────────────────────────────────────────── */
        .btn-primary { background:#4f46e5; color:#fff; border:none; padding:.5rem 1.1rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; transition:background .2s,box-shadow .2s; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; }
        .btn-primary:hover { background:#4338ca; box-shadow:0 4px 12px rgba(79,70,229,.3); }
        .btn-success { background:#16a34a; color:#fff; border:none; padding:.5rem 1.1rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; transition:background .2s,box-shadow .2s; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; }
        .btn-success:hover { background:#15803d; box-shadow:0 4px 12px rgba(22,163,74,.3); }
        .btn-warning { background:#d97706; color:#fff; border:none; padding:.5rem 1.1rem; border-radius:.5rem; font-size:.875rem; font-weight:600; cursor:pointer; transition:background .2s,box-shadow .2s; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; }
        .btn-warning:hover { background:#b45309; box-shadow:0 4px 12px rgba(217,119,6,.3); }
        .btn-ghost   { background:transparent; color:var(--text-subtle); border:1px solid var(--border-ghost); padding:.45rem .9rem; border-radius:.5rem; font-size:.8rem; cursor:pointer; transition:all .2s; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; }
        .btn-ghost:hover { color:var(--text-body); border-color:var(--border-input); background:var(--accent-soft); }
        .btn-danger  { background:transparent; color:#dc2626; border:1px solid rgba(220,38,38,.35); padding:.45rem .9rem; border-radius:.5rem; font-size:.8rem; cursor:pointer; transition:all .2s; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; }
        .btn-danger:hover { background:rgba(220,38,38,.1); color:#b91c1c; border-color:rgba(220,38,38,.6); }
        /* icon-only tight variants */
        .btn-icon-edit    { background:rgba(79,70,229,.08);  color:#4f46e5; border:1px solid rgba(79,70,229,.2);  border-radius:.5rem; padding:.35rem .55rem; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; }
        .btn-icon-edit:hover    { background:rgba(79,70,229,.16); border-color:rgba(79,70,229,.4); }
        .btn-icon-archive { background:rgba(217,119,6,.08);  color:#d97706; border:1px solid rgba(217,119,6,.2);  border-radius:.5rem; padding:.35rem .55rem; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; }
        .btn-icon-archive:hover { background:rgba(217,119,6,.16); border-color:rgba(217,119,6,.4); }
        .btn-icon-restore { background:rgba(22,163,74,.08);  color:#16a34a; border:1px solid rgba(22,163,74,.2);  border-radius:.5rem; padding:.35rem .55rem; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; }
        .btn-icon-restore:hover { background:rgba(22,163,74,.16); border-color:rgba(22,163,74,.4); }
        .btn-icon-delete  { background:rgba(220,38,38,.08);  color:#dc2626; border:1px solid rgba(220,38,38,.2);  border-radius:.5rem; padding:.35rem .55rem; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; }
        .btn-icon-delete:hover  { background:rgba(220,38,38,.16); border-color:rgba(220,38,38,.4); }

        /* ── INPUTS ──────────────────────────────────────────── */
        .admin-input { background:var(--bg-input); border:1px solid var(--border-input); color:var(--text-input); border-radius:.5rem; padding:.55rem .9rem; font-size:.875rem; width:100%; transition:border-color .2s,background .3s,color .3s; }
        .admin-input:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-soft); }
        .admin-input::placeholder { color:var(--text-muted); }
        select.admin-input option { background:var(--bg-input); color:var(--text-input); }

        /* ── SECTION CARD ────────────────────────────────────── */
        .section-card { background:var(--bg-section); border:1px solid var(--border-section); border-radius:.875rem; overflow:hidden; box-shadow:var(--shadow-card); transition:background .3s,border-color .3s,box-shadow .3s; }

        /* ── FILTER BAR ──────────────────────────────────────── */
        .filter-bar { background:var(--bg-filter); border-bottom:1px solid var(--border-section); padding:1rem 1.25rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; transition:background .3s; }

        /* ── PAGE HEADER ─────────────────────────────────────── */
        .page-header { margin-bottom:1.75rem; }
        .page-header h1 { font-size:1.5rem; font-weight:700; color:var(--text-heading); font-family:'Playfair Display',serif; margin:0 0 .25rem; }
        .page-header p  { color:var(--text-muted); font-size:.875rem; margin:0; }

        /* ── SECTION HEADINGS (inside cards) ─────────────────── */
        .card-header-border { border-bottom:1px solid var(--border-divider); }

        /* ── LABELS ──────────────────────────────────────────── */
        .field-label { display:block; font-size:.75rem; color:var(--text-label); text-transform:uppercase; letter-spacing:.05em; margin-bottom:.375rem; }

        /* ── FLASH ALERTS ────────────────────────────────────── */
        .flash-success { background:rgba(34,197,94,.09); border:1px solid rgba(34,197,94,.28); color:#16a34a; border-radius:.625rem; padding:.75rem 1rem; margin-bottom:1.25rem; font-size:.875rem; }
        .flash-error   { background:rgba(239,68,68,.09); border:1px solid rgba(239,68,68,.28); color:#dc2626; border-radius:.625rem; padding:.75rem 1rem; margin-bottom:1.25rem; font-size:.875rem; }

        /* ── THEME TOGGLE ────────────────────────────────────── */
        .theme-toggle { position:relative; width:52px; height:28px; background:#374151; border:none; border-radius:9999px; cursor:pointer; transition:background .3s; flex-shrink:0; padding:0; }
        html.light .theme-toggle { background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); }
        .toggle-thumb { position:absolute; top:3px; left:3px; width:22px; height:22px; border-radius:50%; background:#facc15; transition:transform .3s,background .3s; display:flex; align-items:center; justify-content:center; font-size:13px; box-shadow:0 1px 4px rgba(0,0,0,.3); }
        html.light .toggle-thumb { transform:translateX(24px); background:#fff; }

        /* ── MISC ────────────────────────────────────────────── */
        .th-text-strong { color:var(--text-strong); }
        .th-text-body   { color:var(--text-body); }
        .th-text-muted  { color:var(--text-muted); }
        .th-text-subtle { color:var(--text-subtle); }

        /* ── MODALS ───────────────────────────────────────────── */
        .modal-backdrop { display:none; position:fixed; inset:0; background:rgba(0,0,0,.65); backdrop-filter:blur(4px); z-index:999; align-items:center; justify-content:center; padding:1rem; }
        .modal-backdrop.open { display:flex; animation:fadeIn .18s ease; }
        @keyframes fadeIn { from{opacity:0} to{opacity:1} }
        .modal-box { background:var(--bg-card); border:1px solid var(--border-card); border-radius:1rem; width:100%; max-width:540px; box-shadow:0 24px 64px rgba(0,0,0,.45); animation:slideUp .2s ease; overflow:hidden; }
        .modal-box.modal-lg { max-width:680px; }
        @keyframes slideUp { from{transform:translateY(24px);opacity:0} to{transform:translateY(0);opacity:1} }
        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:1.1rem 1.4rem; border-bottom:1px solid var(--border-divider); }
        .modal-title { font-size:.9375rem; font-weight:700; color:var(--text-strong); margin:0; }
        .modal-close { background:none; border:none; cursor:pointer; padding:.3rem; border-radius:.375rem; color:var(--text-muted); transition:all .2s; display:flex; align-items:center; }
        .modal-close:hover { background:var(--accent-soft); color:var(--text-body); }
        .modal-body { padding:1.4rem; display:flex; flex-direction:column; gap:1rem; max-height:65vh; overflow-y:auto; }
        .modal-footer { padding:1rem 1.4rem; border-top:1px solid var(--border-divider); display:flex; align-items:center; justify-content:flex-end; gap:.625rem; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        @media(max-width:540px){ .form-row { grid-template-columns:1fr; } }
        .form-group { display:flex; flex-direction:column; gap:.375rem; }
        .form-group label { font-size:.72rem; font-weight:600; color:var(--text-label); text-transform:uppercase; letter-spacing:.05em; }
        /* Light mode modal overrides */
        html.light .modal-backdrop { background:rgba(0,0,0,.45); }
        html.light .modal-box { box-shadow:0 24px 64px rgba(0,0,0,.18); }

        /* ── LIGHT MODE OVERRIDES ────────────────────────────── */
        html.light .admin-body,
        html.light .admin-body p,
        html.light .admin-body span,
        html.light .admin-body div,
        html.light .admin-body td,
        html.light .admin-body th,
        html.light .admin-body label,
        html.light .admin-body li {
            color: var(--text-body);
        }
        html.light .admin-body .nav-link         { color: rgba(255,255,255,.75) !important; }
        html.light .admin-body .nav-link:hover   { color: #fff !important; background:rgba(255,255,255,.12) !important; }
        html.light .admin-body .nav-link.active  { color: #fff !important; background:rgba(255,255,255,.18) !important; border-bottom-color:#fff !important; }
        html.light .admin-body .admin-table th   { color: var(--text-th) !important; background: var(--bg-table-head) !important; }
        html.light .admin-body .admin-table td   { color: var(--text-body) !important; }
        html.light .admin-body input,
        html.light .admin-body select,
        html.light .admin-body textarea          { background-color:var(--bg-input)!important; color:var(--text-input)!important; border-color:var(--border-input)!important; }
        html.light .admin-body input::placeholder,
        html.light .admin-body textarea::placeholder { color:var(--text-muted)!important; }
        html.light .admin-body input:focus,
        html.light .admin-body select:focus,
        html.light .admin-body textarea:focus    { border-color:var(--accent)!important; box-shadow:0 0 0 3px var(--accent-soft)!important; }
        html.light .admin-body .admin-nav        { background:var(--bg-nav)!important; border-bottom-color:var(--border-nav)!important; }
        html.light .admin-body .page-header h1   { color:var(--text-heading)!important; }
        html.light .admin-body .page-header p    { color:var(--text-muted)!important; }
        html.light .admin-body .section-card,
        html.light .admin-body .stat-card        { background:var(--bg-card)!important; border-color:var(--border-card)!important; }
        html.light .admin-body .filter-bar       { background:var(--bg-filter)!important; }
        html.light .admin-body .btn-ghost        { color:var(--text-subtle)!important; border-color:rgba(255,255,255,.25)!important; }
        html.light .admin-body .btn-ghost:hover  { color:#fff!important; background:rgba(255,255,255,.12)!important; }

        /* ── LIGHT MODE: eye-catching stat card numbers ──────── */
        html.light .admin-body .stat-card:hover  { border-color:rgba(30,41,59,.2)!important; box-shadow:0 8px 24px rgba(30,41,59,.1)!important; transform:translateY(-1px); }
        html.light .admin-body .section-card     { border-radius:1rem!important; }
        html.light .admin-body .admin-table      { border-radius:.5rem; overflow:hidden; }

        /* ── LIGHT SCROLLBAR ────────────────────────────────── */
        html.light ::-webkit-scrollbar-track  { background:#f0f4f8; }
        html.light ::-webkit-scrollbar-thumb  { background:#1e293b; }

        /* ── LIGHT MODE: button adjustments ─────────────────── */
        html.light .admin-body .btn-icon-edit,
        html.light .admin-body .btn-icon-archive,
        html.light .admin-body .btn-icon-restore,
        html.light .admin-body .btn-icon-delete { border-width:1px; }
    </style>

    <script>
        /* Apply saved theme before paint — prevents flash */
        (function(){
            var t = localStorage.getItem('eut-admin-theme');
            var h = document.documentElement;
            h.classList.remove('dark','light');
            h.classList.add(t === 'light' ? 'light' : 'dark');
        })();
        /* Modal helpers — defined early so views can call them */
        function openModal(id){ var el=document.getElementById(id); if(el){el.classList.add('open');document.body.style.overflow='hidden';} }
        function closeModal(id){ var el=document.getElementById(id); if(el){el.classList.remove('open');document.body.style.overflow='';} }
        function closeModalBackdrop(e,id){ if(e.target===document.getElementById(id)) closeModal(id); }
    </script>
</head>
<body class="admin-body">

{{-- ═══════════════════════ TOP NAVIGATION ═══════════════════════ --}}
<nav class="admin-nav" style="position:sticky;top:0;z-index:50;">
    <div style="max-width:1536px;margin:0 auto;padding:0 1.5rem;height:64px;display:flex;align-items:center;justify-content:space-between;gap:1rem;">

        {{-- Logo --}}
        <a href="{{ route('admin.dashboard') }}" style="display:flex;align-items:center;gap:.5rem;text-decoration:none;flex-shrink:0;">
            <span style="font-family:'Playfair Display',serif; color:#fff; font-weight:700; font-size:1.25rem; letter-spacing:.05em;">EUT</span>
            <span style="color:rgba(255,255,255,.9); border:1px solid rgba(255,255,255,.3); font-size:.65rem; font-weight:700; letter-spacing:.12em; text-transform:uppercase; padding:.2rem .55rem; border-radius:.3rem; background:rgba(255,255,255,.08);">Admin</span>
        </a>

        {{-- Nav links --}}
        <div style="display:flex;align-items:center;gap:.25rem;overflow-x:auto;flex:1;">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M23 21v-2a4 4 0 0 0-3-3.87"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Users
            </a>
            <a href="{{ route('admin.categories') }}" class="nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16"/></svg>
                Categories
            </a>
            <a href="{{ route('admin.menu-items') }}" class="nav-link {{ request()->routeIs('admin.menu-items*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6h18M3 12h18M3 18h18"/><circle cx="19" cy="6" r="2" fill="currentColor" stroke="none"/><circle cx="19" cy="12" r="2" fill="currentColor" stroke="none"/><circle cx="5" cy="18" r="2" fill="currentColor" stroke="none"/></svg>
                Menu Items
            </a>
            <a href="{{ route('admin.riders') }}" class="nav-link {{ request()->routeIs('admin.riders*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><circle cx="12" cy="5" r="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 20v-5l3-3 1.5 4L12 14l2.5 2L16 15l3 3v2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l-3 2h4l1 5 2-4 2 4 1-5h4l-3-2"/></svg>
                Riders
            </a>
            <a href="{{ route('admin.orders') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Orders
            </a>
            <a href="{{ route('admin.table-qrcodes') }}" class="nav-link {{ request()->routeIs('admin.table-qrcodes*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/><rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 17h7M17 14v7"/></svg>
                QR Codes
            </a>
            <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
                Settings
            </a>
        </div>

        {{-- Right: toggle + view site + user --}}
        <div style="display:flex;align-items:center;gap:.75rem;flex-shrink:0;">

            {{-- Dark / Light Toggle --}}
            <button class="theme-toggle" id="themeToggle" title="Toggle light / dark mode" aria-label="Toggle theme">
                <span class="toggle-thumb" id="toggleThumb">
                    <span id="iconMoon">🌙</span>
                    <span id="iconSun" style="display:none;">☀️</span>
                </span>
            </button>

            <a href="{{ route('home') }}" target="_blank" class="btn-ghost" style="font-size:.75rem;color:rgba(255,255,255,.75);border-color:rgba(255,255,255,.25);display:inline-flex;align-items:center;gap:.35rem;"
               onmouseenter="this.style.color='#fff';this.style.background='rgba(255,255,255,.1)'" onmouseleave="this.style.color='rgba(255,255,255,.75)';this.style.background='transparent'">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                View Site
            </a>

            <div style="display:flex;align-items:center;gap:.5rem;padding-left:.75rem;border-left:1px solid rgba(255,255,255,.15);">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" width="32" height="32" style="border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.4);flex-shrink:0;">
                @else
                    <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.875rem;background:rgba(255,255,255,.2);color:#fff;border:2px solid rgba(255,255,255,.3);flex-shrink:0;">
                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                    </div>
                @endif
                <div style="display:none;" class="md-show">
                    <p style="font-size:.75rem; font-weight:600; color:#fff; line-height:1; margin:0;">{{ auth()->user()->name }}</p>
                    <p style="font-size:.7rem; color:rgba(255,255,255,.6); margin:.2rem 0 0;">Administrator</p>
                </div>
                <form method="POST" action="{{ route('auth.logout') }}" style="margin-left:.25rem;">
                    @csrf
                    <button type="submit" title="Logout" style="background:none;border:none;cursor:pointer;padding:.25rem;color:rgba(255,255,255,.5);transition:color .2s;display:flex;align-items:center;"
                            onmouseenter="this.style.color='#fff'" onmouseleave="this.style.color='rgba(255,255,255,.5)'">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

{{-- ═══════════════════════ MAIN CONTENT ═══════════════════════ --}}
<main class="admin-content" style="max-width:1536px;margin:0 auto;">
    @if(session('success'))
        <div class="flash-success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flash-error">✕ {{ session('error') }}</div>
    @endif
    @yield('content')
</main>

{{-- ═══════════════════════ THEME SCRIPT ═══════════════════════ --}}
<script>
(function(){
    var html     = document.documentElement;
    var btn      = document.getElementById('themeToggle');
    var iconMoon = document.getElementById('iconMoon');
    var iconSun  = document.getElementById('iconSun');

    function syncIcon(){
        var light = html.classList.contains('light');
        iconMoon.style.display = light ? 'none'   : 'inline';
        iconSun.style.display  = light ? 'inline' : 'none';
    }
    syncIcon();

    btn.addEventListener('click', function(){
        var light = html.classList.contains('light');
        html.classList.remove('dark','light');
        var next = light ? 'dark' : 'light';
        html.classList.add(next);
        localStorage.setItem('eut-admin-theme', next);
        syncIcon();
    });
})();
</script>
<script>
window._lucideRefresh = function() { if (typeof lucide !== 'undefined') lucide.createIcons(); };
</script>
<script>
function openModal(id){ var el=document.getElementById(id); if(el){el.classList.add('open');document.body.style.overflow='hidden';} }
function closeModal(id){ var el=document.getElementById(id); if(el){el.classList.remove('open');document.body.style.overflow='';} }
function closeModalBackdrop(e,id){ if(e.target===document.getElementById(id)) closeModal(id); }
document.addEventListener('keydown',function(e){ if(e.key==='Escape'){ document.querySelectorAll('.modal-backdrop.open').forEach(m=>m.classList.remove('open')); document.body.style.overflow=''; }});
</script>

{{-- ══════════ JS ERROR HELPER PANEL ══════════ --}}
<div id="errPanel" style="display:none;position:fixed;bottom:1.25rem;right:1.25rem;z-index:9999;width:420px;max-width:calc(100vw - 2.5rem);background:#1e1e1e;border:1px solid #ef4444;border-radius:.875rem;box-shadow:0 8px 32px rgba(0,0,0,.6);font-family:monospace;overflow:hidden;">
    <div style="background:#ef444420;padding:.6rem 1rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #ef444430;">
        <span style="color:#f87171;font-size:.75rem;font-weight:700;display:flex;align-items:center;gap:.4rem;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            JS Error Detected
        </span>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <button onclick="copyErr()" style="background:#ef4444;color:#fff;border:none;border-radius:.35rem;padding:.2rem .6rem;font-size:.7rem;cursor:pointer;font-family:monospace;">Copy</button>
            <button onclick="document.getElementById('errPanel').style.display='none'" style="background:none;border:none;color:#a3a3a3;cursor:pointer;font-size:1rem;line-height:1;padding:.1rem .3rem;">✕</button>
        </div>
    </div>
    <div id="errBody" style="padding:.75rem 1rem;max-height:200px;overflow-y:auto;font-size:.72rem;color:#fca5a5;line-height:1.6;white-space:pre-wrap;word-break:break-all;"></div>
</div>
<script>
(function(){
    var errors = [];
    function showErr(msg, src, line, col) {
        errors.push((src||'')+(line?':'+line:'')+(col?':'+col:'')+'\n'+msg);
        var body = document.getElementById('errBody');
        var panel = document.getElementById('errPanel');
        if(body && panel){
            body.textContent = errors.join('\n\n---\n\n');
            panel.style.display = 'block';
        }
    }
    window.onerror = function(msg, src, line, col, err){
        showErr(err ? err.stack || msg : msg, src, line, col);
        return false;
    };
    window.addEventListener('unhandledrejection', function(e){
        showErr('Unhandled Promise: ' + (e.reason && e.reason.stack ? e.reason.stack : e.reason), '', '', '');
    });
    window.copyErr = function(){
        var t = document.getElementById('errBody').textContent;
        navigator.clipboard.writeText(t).then(function(){
            var btn = document.querySelector('#errPanel button');
            if(btn){ var old=btn.textContent; btn.textContent='Copied!'; setTimeout(function(){ btn.textContent=old; },1500); }
        });
    };
})();

// ── Global Rider Pickup Notification (Dialog Only) ──────────────────────────────
// Listen for order status updates and detect rider pickup
if (typeof window.Echo !== 'undefined' && window.Echo) {
    window.Echo.channel('orders')
        .listen('.order.status.updated', function(data) {
            console.log('[Admin] Order status updated:', data);
            
            // Check if this is a rider pickup event
            if (data.status === 'out_for_delivery' && data.picked_up_at && data.order_id && data.order_number) {
                console.log('[Admin] Detected rider pickup for order:', data.order_number);
                showAdminPickupNotification(data.order_id, data.order_number);
            }
        });
}

// Show pickup notification on admin dashboard
function showAdminPickupNotification(orderId, orderNumber) {
    const notifHtml = `
        <div style="position:fixed;bottom:24px;right:24px;z-index:9998;background:#fff;border-radius:12px;padding:20px;box-shadow:0 10px 40px rgba(0,0,0,.2);border-left:5px solid #8b5cf6;max-width:400px;" onclick="event.stopPropagation();" id="pickup-notif-${orderId}">
            <div style="display:flex;align-items:flex-start;gap:12px;">
                <div style="font-size:32px;flex-shrink:0;">📋</div>
                <div style="flex:1;">
                    <p style="margin:0 0 4px;color:#000;font-weight:700;font-size:15px;">Takeout Slip Ready</p>
                    <p style="margin:0 0 12px;color:#666;font-size:13px;">Order <strong>#${orderNumber}</strong></p>
                    <div style="display:flex;gap:8px;">
                        <button onclick="document.getElementById('pickup-notif-${orderId}').remove()" style="padding:6px 12px;background:#f0f0f0;border:none;border-radius:6px;cursor:pointer;color:#333;font-weight:600;font-size:12px;">Dismiss</button>
                        <button onclick="openAdminPickupSlip(${orderId}); document.getElementById('pickup-notif-${orderId}').remove();" style="padding:6px 12px;background:#8b5cf6;border:none;border-radius:6px;cursor:pointer;color:#fff;font-weight:600;font-size:12px;">Print</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove any existing notification for this order
    const existing = document.getElementById('pickup-notif-' + orderId);
    if (existing) existing.remove();
    
    const div = document.createElement('div');
    div.innerHTML = notifHtml;
    document.body.appendChild(div.firstElementChild);
    
    // Play notification sound
    if (typeof NotificationSound !== 'undefined') {
        NotificationSound.play();
    }
    
    // Auto-remove after 15 seconds if not dismissed (longer time)
    setTimeout(() => {
        const el = document.getElementById('pickup-notif-' + orderId);
        if (el) el.remove();
    }, 15000);
}

// Open takeout slip in new window for admin to print
function openAdminPickupSlip(orderId) {
    const slipUrl = '/chef/orders/' + orderId + '/takeout-slip.html';
    const win = window.open(slipUrl, 'takeout_slip_' + orderId, 'width=320,height=620,menubar=no,toolbar=no,location=no,status=no');
    if (win) {
        win.focus();
    } else {
        window.open(slipUrl, '_blank');
    }
}
</script>
<script src="/sounds/notification.js"></script>
@stack('scripts')
</body>
</html>
