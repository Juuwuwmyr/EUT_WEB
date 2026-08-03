@extends('chef.layout')
@section('title', 'Kitchen Dashboard')

@push('head')
<style>
    .kitchen-board {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        min-height: calc(100vh - 220px);
    }
    @media (max-width: 640px) {
        .kitchen-board { grid-template-columns: 1fr; }
    }

    .kitchen-col {
        background: var(--bg-section);
        border: 1px solid var(--border-section);
        border-radius: 1rem;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-height: 420px;
    }

    .kitchen-col-header {
        padding: 1rem 1.1rem;
        border-bottom: 1px solid var(--border-divider);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
    }

    .kitchen-col-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .9rem;
        font-weight: 700;
        color: var(--text-strong);
        margin: 0;
    }

    .kitchen-col-count {
        min-width: 1.75rem;
        height: 1.75rem;
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .75rem;
        font-weight: 800;
    }

    .kitchen-col-body {
        flex: 1;
        padding: .85rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: .85rem;
    }

    .kitchen-col-body::-webkit-scrollbar { width: 4px; }
    .kitchen-col-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 99px; }

    .k-order-card {
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: .875rem;
        overflow: hidden;
        transition: transform .15s, box-shadow .15s;
        animation: kSlideIn .25s ease;
    }
    .k-order-card:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,0,0,.25); }
    .k-order-card.is-urgent { border-color: rgba(239,68,68,.45); box-shadow: 0 0 0 1px rgba(239,68,68,.15); }

    @keyframes kSlideIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .k-card-top {
        padding: .85rem 1rem .65rem;
        border-bottom: 1px solid var(--border-divider);
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .5rem;
    }

    .k-order-num {
        font-family: monospace;
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--accent);
        letter-spacing: .02em;
    }

    .k-elapsed {
        font-size: .68rem;
        font-weight: 700;
        padding: .2rem .55rem;
        border-radius: 9999px;
        white-space: nowrap;
    }

    .k-customer {
        font-size: .78rem;
        color: var(--text-muted);
        margin-top: .15rem;
    }

    .k-items {
        padding: .65rem 1rem;
        display: flex;
        flex-direction: column;
        gap: .55rem;
    }

    .k-item {
        display: flex;
        align-items: flex-start;
        gap: .6rem;
    }

    .k-item-img {
        width: 42px;
        height: 42px;
        border-radius: .5rem;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid var(--border-divider);
    }

    .k-item-qty {
        font-size: .85rem;
        font-weight: 800;
        color: #facc15;
        min-width: 1.5rem;
    }

    .k-item-name {
        font-size: .88rem;
        font-weight: 700;
        color: var(--text-strong);
        line-height: 1.35;
    }

    .k-modifiers {
        display: flex;
        flex-wrap: wrap;
        gap: .25rem;
        margin-top: .25rem;
    }

    .k-mod-tag {
        font-size: .62rem;
        font-weight: 600;
        padding: .15rem .45rem;
        border-radius: .35rem;
        background: rgba(59,130,246,.12);
        color: #60a5fa;
        border: 1px solid rgba(59,130,246,.2);
    }

    .k-notes {
        margin: 0 1rem .65rem;
        padding: .55rem .7rem;
        border-radius: .5rem;
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.2);
        font-size: .72rem;
        color: #fbbf24;
        line-height: 1.45;
    }

    .k-actions {
        padding: .65rem 1rem .85rem;
        display: flex;
        gap: .5rem;
    }

    .k-btn {
        flex: 1;
        border: none;
        border-radius: .55rem;
        padding: .6rem .75rem;
        font-size: .78rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
    }
    .k-btn:disabled { opacity: .55; cursor: not-allowed; }

    .k-btn-accept  { background: #16a34a; color: #fff; }
    .k-btn-accept:hover:not(:disabled)  { background: #15803d; }
    .k-btn-cook    { background: #2563eb; color: #fff; }
    .k-btn-cook:hover:not(:disabled)    { background: #1d4ed8; }
    .k-btn-ready   { background: #d97706; color: #fff; }
    .k-btn-ready:hover:not(:disabled)   { background: #b45309; }

    .k-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: var(--text-muted);
        font-size: .8rem;
    }

    .k-empty-icon { margin-bottom: .5rem; opacity: .6; display:flex; justify-content:center; }

    .kitchen-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .kitchen-live {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font-size: .75rem;
        color: var(--text-muted);
    }

    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #22c55e;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%      { opacity: .5; transform: scale(.85); }
    }

    body.kitchen-fullscreen .admin-nav { display: none; }
    body.kitchen-fullscreen .admin-content { max-width: none; padding: 1rem; }
    body.kitchen-fullscreen .kitchen-hide-fs { display: none; }

    /* ── Order Detail Modal ── */
    .k-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0,0,0,.75);
        backdrop-filter: blur(6px);
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .k-modal-backdrop.open { display: flex; }

    .k-modal {
        background: var(--bg-card);
        border: 1px solid var(--border-card);
        border-radius: 1.25rem;
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 30px 80px rgba(0,0,0,.6);
        animation: kModalIn .2s ease;
    }
    @keyframes kModalIn {
        from { opacity: 0; transform: translateY(16px) scale(.97); }
        to   { opacity: 1; transform: translateY(0) scale(1); }
    }
    .k-modal::-webkit-scrollbar { width: 4px; }
    .k-modal::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 99px; }

    .k-modal-header {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid var(--border-divider);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        position: sticky;
        top: 0;
        background: var(--bg-card);
        z-index: 1;
    }
    .k-modal-title {
        font-family: monospace;
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--accent);
    }
    .k-modal-close {
        width: 2rem;
        height: 2rem;
        border-radius: .5rem;
        border: 1px solid var(--border-divider);
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all .15s;
        flex-shrink: 0;
    }
    .k-modal-close:hover { background: rgba(239,68,68,.12); color: #ef4444; border-color: rgba(239,68,68,.3); }

    .k-modal-meta {
        padding: .75rem 1.25rem;
        border-bottom: 1px solid var(--border-divider);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        font-size: .75rem;
        color: var(--text-muted);
    }
    .k-modal-meta span { display: flex; align-items: center; gap: .3rem; }
    .k-modal-meta strong { color: var(--text-strong); }

    .k-modal-items { padding: .85rem 1.25rem; display: flex; flex-direction: column; gap: 1rem; }

    .k-modal-item {
        background: rgba(255,255,255,.03);
        border: 1px solid var(--border-divider);
        border-radius: .875rem;
        overflow: hidden;
    }
    .k-modal-item-header {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .75rem .85rem;
    }
    .k-modal-item-img {
        width: 52px;
        height: 52px;
        border-radius: .6rem;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid var(--border-divider);
    }
    .k-modal-item-qty {
        font-size: 1rem;
        font-weight: 900;
        color: #facc15;
        flex-shrink: 0;
    }
    .k-modal-item-name {
        font-size: .9rem;
        font-weight: 700;
        color: var(--text-strong);
        flex: 1;
        min-width: 0;
    }
    .k-modal-item-price {
        font-size: .8rem;
        font-weight: 700;
        color: #4ade80;
        flex-shrink: 0;
    }

    .k-modal-specs {
        border-top: 1px solid var(--border-divider);
        padding: .65rem .85rem .75rem;
    }
    .k-modal-specs-label {
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: .45rem;
    }
    .k-modal-spec-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .3rem 0;
        border-bottom: 1px solid rgba(255,255,255,.04);
        gap: .5rem;
    }
    .k-modal-spec-row:last-child { border-bottom: none; }
    .k-modal-spec-left { display: flex; align-items: center; gap: .4rem; }
    .k-modal-spec-badge {
        font-size: .58rem;
        font-weight: 800;
        padding: .1rem .35rem;
        border-radius: .25rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        flex-shrink: 0;
    }
    .spec-flavor   { background: rgba(59,130,246,.15);  color: #3b82f6; }
    .spec-modifier { background: rgba(139,92,246,.15); color: #8b5cf6; }
    .spec-addon    { background: rgba(245,158,11,.15);  color: #d97706; }
    .k-modal-spec-name {
        font-size: .78rem;
        font-weight: 600;
        color: var(--text-strong);
    }
    .k-modal-spec-price {
        font-size: .75rem;
        font-weight: 700;
        color: #4ade80;
        flex-shrink: 0;
    }

    .k-modal-no-specs {
        font-size: .75rem;
        color: var(--text-muted);
        font-style: italic;
        padding: .3rem 0;
    }

    .k-modal-footer {
        padding: .85rem 1.25rem;
        border-top: 1px solid var(--border-divider);
        display: flex;
        flex-direction: column;
        gap: .4rem;
    }
    .k-modal-total-row {
        display: flex;
        justify-content: space-between;
        font-size: .8rem;
        color: var(--text-muted);
    }
    .k-modal-total-row.grand {
        font-size: .95rem;
        font-weight: 800;
        color: var(--text-strong);
        padding-top: .4rem;
        border-top: 1px solid var(--border-divider);
        margin-top: .2rem;
    }
    .k-modal-total-row.grand span:last-child { color: #facc15; }

    .k-modal-notes {
        margin: 0 1.25rem .85rem;
        padding: .65rem .85rem;
        border-radius: .65rem;
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.2);
        font-size: .78rem;
        color: #fbbf24;
        line-height: 1.5;
    }

    .k-modal-action-bar {
        padding: .85rem 1.25rem 1rem;
        border-top: 1px solid var(--border-divider);
        display: flex;
        gap: .6rem;
        flex-wrap: wrap;
    }
</style>
@endpush

@section('content')


<div class="kitchen-toolbar">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.75rem;height:2.75rem;border-radius:.75rem;background:rgba(217,119,6,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="chef-hat" style="width:1.3rem;height:1.3rem;color:#d97706;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;font-family:'Playfair Display',serif;font-size:1.5rem;font-weight:700;color:var(--text-heading);">Kitchen Display</h1>
            <p style="margin:0;font-size:.875rem;color:var(--text-muted);">Track and manage orders from new through cooking.</p>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:.65rem;flex-wrap:wrap;">
        <span class="kitchen-live" id="wsStatus" title="Polling every 3s">
            <span class="live-dot" style="background:#f59e0b;"></span> 
            <span id="wsStatusText">Loading...</span>
        </span>
        <button type="button" class="btn-ghost" style="font-size:.75rem;" onclick="refreshKitchen(true)">
            <i data-lucide="refresh-cw" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Refresh
        </button>
        <button type="button" class="btn-ghost" style="font-size:.75rem;" onclick="toggleKitchenFullscreen()">
            <i data-lucide="maximize-2" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Fullscreen
        </button>
        <button type="button" id="wahbStatusBtn" class="btn-primary" style="font-size:.75rem;" onclick="openWahbSettings()">
            <i data-lucide="printer" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Bridge Settings
        </button>
        <button type="button" class="btn-ghost" style="font-size:.75rem;" onclick="toggleWahbConsole()">
            <i data-lucide="terminal" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            Console
            <span id="wahbConsoleBadge" style="display:none;width:7px;height:7px;border-radius:50%;background:#ef4444;margin-left:2px;flex-shrink:0;"></span>
        </button>
    </div>
</div>

{{-- WAHB Web UI Modal --}}
<div id="wahbModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.75);z-index:9999;padding:1.5rem;backdrop-filter:blur(4px);">
    <div style="max-width:1400px;margin:0 auto;height:100%;display:flex;flex-direction:column;background:var(--bg-primary);border-radius:1rem;box-shadow:0 20px 60px rgba(0,0,0,.4);overflow:hidden;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid var(--border-card);background:var(--bg-filter);">
            <div style="display:flex;align-items:center;gap:.6rem;">
                <i data-lucide="printer" style="width:1.1rem;height:1.1rem;color:#818cf8;stroke-width:2;"></i>
                <h2 style="margin:0;font-size:1rem;font-weight:700;color:var(--text-strong);">WebApp Hardware Bridge Settings</h2>
                <span style="font-size:.7rem;color:var(--text-muted);margin-left:.5rem;">Configure printer mappings for silent printing</span>
            </div>
            <button onclick="closeWahbSettings()" style="width:2rem;height:2rem;border-radius:.5rem;border:none;background:transparent;color:var(--text-muted);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .2s;" onmouseover="this.style.background='var(--bg-card)'" onmouseout="this.style.background='transparent'">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i>
            </button>
        </div>
        <iframe id="wahbIframe" src="" style="flex:1;border:none;width:100%;background:#fff;"></iframe>
        <div style="padding:.75rem 1.5rem;border-top:1px solid var(--border-card);background:var(--bg-filter);font-size:.7rem;color:var(--text-muted);display:flex;align-items:center;justify-content:space-between;">
            <span>Bridge running at <code style="background:rgba(255,255,255,.07);padding:2px 6px;border-radius:3px;color:var(--text-body);">http://127.0.0.1:12212</code></span>
            <span>Add a printer with key <code style="background:rgba(99,102,241,.15);padding:2px 6px;border-radius:3px;color:#818cf8;font-weight:700;">RECEIPT</code> in Printers section</span>
        </div>
    </div>
</div>

{{-- WAHB Console Log Panel (floating bottom-right) --}}
<div id="wahbConsolePanel" style="display:none;position:fixed;bottom:1.25rem;right:1.25rem;width:480px;max-width:calc(100vw - 2rem);z-index:9998;border-radius:.875rem;overflow:hidden;box-shadow:0 8px 40px rgba(0,0,0,.5);border:1px solid rgba(99,102,241,.3);">
    {{-- Header --}}
    <div style="background:#0f0f23;padding:.65rem 1rem;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(99,102,241,.2);">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i data-lucide="terminal" style="width:.85rem;height:.85rem;color:#818cf8;stroke-width:2;"></i>
            <span style="font-size:.75rem;font-weight:700;color:#c7d2fe;">Silent Print Console</span>
            <span id="wahbConsoleCount" style="font-size:.6rem;background:rgba(99,102,241,.25);color:#818cf8;border-radius:99px;padding:1px 7px;font-weight:700;">0</span>
        </div>
        <div style="display:flex;align-items:center;gap:.4rem;">
            <button onclick="wahbRunDiagnostics()" style="font-size:.67rem;padding:.25rem .6rem;border-radius:.35rem;border:1px solid rgba(99,102,241,.4);background:rgba(99,102,241,.15);color:#818cf8;cursor:pointer;display:inline-flex;align-items:center;gap:.25rem;">
                <i data-lucide="zap" style="width:.6rem;height:.6rem;stroke-width:2.5;"></i> Diagnose
            </button>
            <button onclick="wahbCopyConsole()" style="font-size:.67rem;padding:.25rem .6rem;border-radius:.35rem;border:1px solid rgba(255,255,255,.1);background:transparent;color:#94a3b8;cursor:pointer;display:inline-flex;align-items:center;gap:.25rem;">
                <i data-lucide="copy" style="width:.6rem;height:.6rem;stroke-width:2;"></i> Copy
            </button>
            <button onclick="wahbClearConsole()" style="font-size:.67rem;padding:.25rem .6rem;border-radius:.35rem;border:1px solid rgba(255,255,255,.1);background:transparent;color:#94a3b8;cursor:pointer;display:inline-flex;align-items:center;gap:.25rem;">
                <i data-lucide="trash-2" style="width:.6rem;height:.6rem;stroke-width:2;"></i> Clear
            </button>
            <button onclick="toggleWahbConsole()" style="background:transparent;border:none;color:#6b7280;cursor:pointer;padding:.2rem .3rem;border-radius:.3rem;">
                <i data-lucide="x" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>
            </button>
        </div>
    </div>
    {{-- Log body --}}
    <div id="wahbConsoleBody" style="background:#0a0a1a;max-height:320px;overflow-y:auto;font-family:'Courier New',monospace;font-size:.69rem;line-height:1.7;padding:.5rem .75rem;">
        <div style="color:#4b5563;font-style:italic;">Waiting for events… click Diagnose to run checks.</div>
    </div>
    {{-- Status bar --}}
    <div style="background:#0f0f23;padding:.35rem 1rem;border-top:1px solid rgba(99,102,241,.15);display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <span style="font-size:.63rem;color:#4b5563;">ws://127.0.0.1:12212/printer</span>
        <span id="wahbConsoleStatus" style="font-size:.63rem;color:#6b7280;font-weight:700;">● UNKNOWN</span>
    </div>
</div>

{{-- Auto-Print Status Banner --}}
<div id="autoPrintBanner" style="display:none;align-items:center;justify-content:space-between;gap:1rem;padding:.8rem 1.25rem;background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.25);border-radius:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:.6rem;">
        <i data-lucide="printer" style="width:1.1rem;height:1.1rem;color:#f59e0b;stroke-width:2;flex-shrink:0;"></i>
        <p style="font-size:.8rem;font-weight:700;color:#f59e0b;margin:0;">Click anywhere on this page to enable auto-printing (browser security requirement)</p>
    </div>
    <button onclick="enableAutoPrint()" style="font-size:.7rem;color:#f59e0b;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);border-radius:.5rem;padding:.4rem .8rem;cursor:pointer;white-space:nowrap;">Enable Auto-Print</button>
</div>

{{-- Popup permission notice — only shown once --}}
<div id="popupNotice" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.6rem 1.25rem;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.25);border-radius:.75rem;margin-bottom:1rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:.5rem;">
        <i data-lucide="info" style="width:.9rem;height:.9rem;color:#3b82f6;stroke-width:2;flex-shrink:0;"></i>
        <p style="font-size:.75rem;color:var(--text-muted);margin:0;">For auto-print to work: allow popups for this site in your browser address bar when prompted.</p>
    </div>
    <button onclick="document.getElementById('popupNotice').style.display='none';localStorage.setItem('kitchenPopupDismissed','1')" style="font-size:.7rem;color:var(--text-muted);background:none;border:none;cursor:pointer;white-space:nowrap;">Dismiss</button>
</div>

<div class="kitchen-board" id="kitchenBoard">
    {{-- Queue (accepted by admin) --}}
    <div class="kitchen-col" data-col="queued">
        <div class="kitchen-col-header" style="background:rgba(59,130,246,.06);">
            <h2 class="kitchen-col-title">
                <i data-lucide="list-ordered" style="width:1rem;height:1rem;color:#3b82f6;stroke-width:2;"></i>
                Queue
            </h2>
            <span class="kitchen-col-count" style="background:rgba(59,130,246,.15);color:#3b82f6;" id="count-queued">{{ $queuedOrders->count() }}</span>
        </div>
        <div class="kitchen-col-body" id="col-queued">
            @forelse($queuedOrders as $order)
                @include('admin.partials.kitchen-order-card', ['order' => $order, 'column' => 'queued'])
            @empty
                <div class="k-empty"><div class="k-empty-icon"><svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>Queue is empty</div>
            @endforelse
        </div>
    </div>

    {{-- Cooking --}}
    <div class="kitchen-col" data-col="cooking">
        <div class="kitchen-col-header" style="background:rgba(220,38,38,.06);">
            <h2 class="kitchen-col-title">
                <i data-lucide="flame" style="width:1rem;height:1rem;color:#dc2626;stroke-width:2;"></i>
                Cooking
            </h2>
            <span class="kitchen-col-count" style="background:rgba(220,38,38,.15);color:#dc2626;" id="count-cooking">{{ $cookingOrders->count() }}</span>
        </div>
        <div class="kitchen-col-body" id="col-cooking">
            @forelse($cookingOrders as $order)
                @include('admin.partials.kitchen-order-card', ['order' => $order, 'column' => 'cooking'])
            @empty
                <div class="k-empty"><div class="k-empty-icon"><svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg></div>Nothing cooking</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ── Order Detail Modal ── --}}
<div class="k-modal-backdrop" id="orderModal" onclick="closeOrderModal(event)">
    <div class="k-modal" id="orderModalBox">
        <div class="k-modal-header">
            <div>
                <div class="k-modal-title" id="modalOrderNum">—</div>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:.15rem;" id="modalCustomer">—</div>
            </div>
            <button class="k-modal-close" onclick="closeOrderModal()">✕</button>
        </div>
        <div class="k-modal-meta" id="modalMeta"></div>
        <div class="k-modal-items" id="modalItems"></div>
        <div id="modalNotes"></div>
        <div id="modalTableNote" style="display:none;"></div>
        <div class="k-modal-footer" id="modalFooter"></div>
        <div class="k-modal-action-bar" id="modalActions"></div>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/wahb-printer.js') }}"></script>
<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const IS_ADMIN   = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
const KITCHEN_URL = '{{ route('chef.orders') }}';
const ACCEPT_URL  = id => IS_ADMIN ? `/admin/orders/${id}/accept` : `/chef/orders/${id}/accept`;
const START_URL   = id => `/chef/orders/${id}/start`;
const READY_URL   = id => `/chef/orders/${id}/ready`;

console.log('[INIT] Constants:', {
    CSRF_TOKEN: !!CSRF_TOKEN,
    IS_ADMIN,
    KITCHEN_URL,
    currentUser: '{{ auth()->user()->name ?? "Unknown" }}'
});

let lastNewCount = 0; // not used but kept to avoid reference errors
let orderDataMap = {};
let fallbackTimer = null;
let printedOrderIds = new Set();
let autoPrintEnabled = false;

function testKitchenAPI() {
    console.log('[TEST] Testing kitchen API endpoint...');
    fetch(KITCHEN_URL, { 
        headers: { 'Accept': 'application/json' } 
    })
    .then(res => {
        console.log('[TEST] Response status:', res.status);
        console.log('[TEST] Response headers:', Array.from(res.headers.entries()));
        return res.text();
    })
    .then(text => {
        console.log('[TEST] Raw response:', text.substring(0, 200));
        try {
            const data = JSON.parse(text);
            console.log('[TEST] Parsed JSON:', data);
        } catch (e) {
            console.error('[TEST] JSON parse error:', e);
        }
    })
    .catch(err => console.error('[TEST] Fetch error:', err));
}

// Make it available globally for testing
window.testKitchenAPI = testKitchenAPI;

function enableAutoPrint() {
    autoPrintEnabled = true;
    const banner = document.getElementById('autoPrintBanner');
    if (banner) {
        banner.style.background = 'rgba(16,185,129,.08)';
        banner.style.borderColor = 'rgba(16,185,129,.25)';
        banner.innerHTML = `
            <div style="display:flex;align-items:center;gap:.6rem;">
                <i data-lucide="check-circle-2" style="width:1.1rem;height:1.1rem;color:#10b981;stroke-width:2;flex-shrink:0;"></i>
                <p style="font-size:.8rem;font-weight:700;color:#10b981;margin:0;">✓ Auto-Print ENABLED — Kitchen tickets will print automatically when orders are accepted</p>
            </div>`;
        if (window.lucide) lucide.createIcons();
        setTimeout(() => { banner.style.display = 'none'; }, 5000);
    }
    
    // Mark existing orders as already printed to avoid re-printing them
    document.querySelectorAll('.k-order-card[data-order-id]').forEach(card => {
        const id = card.dataset.orderId;
        if (id) {
            printedOrderIds.add('accept_' + id);
            printedOrderIds.add('ready_' + id);
            printedOrderIds.add('pickup_' + id);
        }
    });
    
    Object.keys(orderDataMap).forEach(id => {
        printedOrderIds.add('accept_' + id);
        printedOrderIds.add('ready_' + id);
        printedOrderIds.add('pickup_' + id);
    });
    
    localStorage.setItem('autoPrintEnabled', 'true');
}

function unlockAutoPrint() {
    enableAutoPrint(); // Use the same logic
}

function elapsedBadge(mins) {
    let bg, color, label;
    if (mins >= 20)      { bg = 'rgba(239,68,68,.15)';  color = '#ef4444'; label = mins + 'm — URGENT'; }
    else if (mins >= 10) { bg = 'rgba(245,158,11,.15)'; color = '#f59e0b'; label = mins + 'm'; }
    else                 { bg = 'rgba(255,255,255,.06)'; color = 'var(--text-muted)'; label = mins + 'm ago'; }
    return `<span class="k-elapsed" style="background:${bg};color:${color};">${label}</span>`;
}

function renderItems(items) {
    return items.map(item => {
        const modList = item.modifiers || [];
        // modifiers may be plain strings (plucked) or objects {name, type, ...}
        const mods = modList
            .filter(m => m && (typeof m === 'string' ? m : m.name) && !/^no\s/i.test(typeof m === 'string' ? m : (m.name || '')))
            .map(m => {
                if (typeof m === 'string') {
                    return `<span class="k-mod-tag">${escapeHtml(m)}</span>`;
                }
                const colors = { flavor: '#3b82f6', modifier: '#8b5cf6', addon: '#d97706' };
                const c = colors[m.type] || '#60a5fa';
                const adj = parseFloat(m.price_adjustment || 0);
                const extra = (m.price_type === 'add' && adj > 0) ? ` +₱${adj}` : '';
                return `<span class="k-mod-tag" style="background:${c}18;color:${c};border-color:${c}30;">${escapeHtml(m.name)}${extra}</span>`;
            }).join('');
        return `
            <div class="k-item">
                <img class="k-item-img" src="${item.image}" alt="">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:baseline;gap:.4rem;">
                        <span class="k-item-qty">${item.qty}×</span>
                        <span class="k-item-name">${escapeHtml(item.name)}</span>
                    </div>
                    ${mods ? `<div class="k-modifiers">${mods}</div>` : ''}
                </div>
            </div>`;
    }).join('');
}

function renderActions(order, column) {
    const receiptUrl = `/chef/orders/${order.id}/receipt`;
    const printBtn = `<button class="k-btn" style="flex:0 0 auto;width:2.5rem;background:rgba(255,255,255,.06);color:var(--text-muted);" onclick="event.stopPropagation();printReceipt('${receiptUrl}')" title="Print Receipt"><i data-lucide="printer" style="width:14px;height:14px;"></i></button>`;

    if (column === 'new') {
        // Pending orders — admin accepts
        return IS_ADMIN
            ? `<button class="k-btn k-btn-accept" onclick="event.stopPropagation();kitchenAction('accept', ${order.id}, this)">✓ Accept Order</button>`
            : `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);" disabled>Waiting for acceptance…</button>`;
    }
    if (column === 'queued') {
        // Accepted orders — chef starts cooking
        return printBtn + `<button class="k-btn k-btn-cook" onclick="event.stopPropagation();kitchenAction('start', ${order.id}, this)">🍳 Start Cooking</button>`;
    }
    if (column === 'cooking') {
        return printBtn + `<button class="k-btn k-btn-ready" onclick="event.stopPropagation();kitchenAction('ready', ${order.id}, this)">Mark Ready</button>`;
    }
    return printBtn;
}

function renderOrderCard(order, column) {
    const urgent = order.elapsed_mins >= 20 ? ' is-urgent' : '';
    const notes = order.notes
        ? `<div class="k-notes">📝 ${escapeHtml(order.notes)}</div>`
        : '';
    const tableNote = (order.order_type === 'dine_in' && order.table_number)
        ? `<div class="k-notes" style="background:rgba(250,204,21,.08);border-color:rgba(250,204,21,.25);color:#facc15;">🪑 Table ${escapeHtml(order.table_number)}</div>`
        : '';

    const elapsed = elapsedBadge(order.elapsed_mins);

    return `
        <div class="k-order-card${urgent}" data-order-id="${order.id}" onclick="openOrderModal(${order.id})" style="cursor:pointer;">
            <div class="k-card-top">
                <div>
                    <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:.15rem;">
                        <div class="k-order-num">${escapeHtml(order.order_number)}</div>
                        <span style="font-size:10px;padding:1px 5px;border-radius:4px;background:rgba(255,255,255,.05);color:var(--text-muted);border:1px solid rgba(255,255,255,.1);display:inline-flex;align-items:center;gap:3px;">
                            ${order.order_type_icon} ${order.order_type_label}
                        </span>
                    </div>
                    <div class="k-customer">${escapeHtml(order.customer)} · ${order.placed_at}</div>
                </div>
                ${elapsed}
            </div>
            <div class="k-items">${renderItems(order.items)}</div>
            ${tableNote}
            ${notes}
            <div class="k-actions">${renderActions(order, column)}</div>
        </div>`;
}

function renderColumn(col, orders) {
    const el = document.getElementById('col-' + col);
    const countEl = document.getElementById('count-' + col);
    if (!el) return;

    countEl.textContent = orders.length;

    if (!orders.length) {
        const emptyMsg = { queued: 'Queue is empty', cooking: 'Nothing cooking' };
        const emptyIcon = {
            queued:  '<svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>',
            cooking: '<svg width="28" height="28" fill="none" stroke="#6b7280" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 14v6m-3-3h6M6 10h2a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2zm10 0h2a2 2 0 002-2V6a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2zM6 20h2a2 2 0 002-2v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2a2 2 0 002 2z"/></svg>',
        };
        el.innerHTML = `<div class="k-empty"><div class="k-empty-icon">${emptyIcon[col] || ''}</div>${emptyMsg[col] || ''}</div>`;
        return;
    }

    el.innerHTML = orders.map(o => renderOrderCard(o, col)).join('');
    orders.forEach(o => { orderDataMap[o.id] = { ...o, column: col }; });
}

// ── ORDER DETAIL MODAL ──────────────────────────────────────────────────────
function openOrderModal(orderId) {
    const order = orderDataMap[orderId];
    if (!order) return;

    document.getElementById('modalOrderNum').innerHTML = 
        order.order_number + 
        ` <span style="font-size:10px;padding:2px 8px;border-radius:6px;background:rgba(255,255,255,.08);color:var(--text-muted);border:1px solid rgba(255,255,255,.12);margin-left:.5rem;vertical-align:middle;font-family:sans-serif;font-weight:600;">` + 
        order.order_type_icon + ' ' + order.order_type_label + '</span>';
    document.getElementById('modalCustomer').textContent = order.customer + ' · ' + order.placed_at;

    const statusColors = { pending:'#f59e0b', accepted:'#3b82f6', preparing:'#ef4444', rider_assigned:'#8b5cf6', out_for_delivery:'#8b5cf6', delivered:'#22c55e' };
    const statusLabels = { pending:'Pending', accepted:'Accepted', preparing:'Cooking', rider_assigned:'Rider Assigned', out_for_delivery:'Out for Delivery', delivered:'Delivered' };
    const sc = statusColors[order.status] || '#6b7280';
    document.getElementById('modalMeta').innerHTML =
        `<span><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:2px"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg> <strong>${order.placed_at}</strong></span>` +
        `<span style="color:${sc};font-weight:700;">● ${statusLabels[order.status] || order.status}</span>` +
        (order.elapsed_mins >= 0 ? `<span><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:2px"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 6v6l4 2"/></svg> <strong>${order.elapsed_mins}m ago</strong></span>` : '') +
        (order.rider_name ? `<span><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:middle;margin-right:2px"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> <strong>${escapeHtml(order.rider_name)}</strong></span>` : '');

    let subtotal = 0;
    const itemsHtml = (order.items || []).map(item => {
        const qty = parseInt(item.qty || 1);
        const price = parseFloat(item.price || 0);
        // prefer stored subtotal if available, otherwise compute
        const itemTotal = item.subtotal > 0 ? parseFloat(item.subtotal) : price * qty;
        subtotal += itemTotal;

        const specs = (item.modifiers || []).filter(m => {
            const name = typeof m === 'string' ? m : (m.name || '');
            return name && !/^no\s/i.test(name);
        });

        const specsInner = specs.length ? specs.map(m => {
            const name  = typeof m === 'string' ? m : m.name;
            const type  = typeof m === 'object' ? (m.type || 'modifier') : 'modifier';
            const adj   = typeof m === 'object' ? parseFloat(m.price_adjustment || 0) : 0;
            const pType = typeof m === 'object' ? (m.price_type || 'none') : 'none';
            const label = { flavor:'Flavor', modifier:'Option', addon:'Add-on' }[type] || 'Option';
            const priceHtml = (pType === 'add' && adj > 0) ? `<span class="k-modal-spec-price">+₱${adj.toLocaleString()}</span>` : '';
            return `<div class="k-modal-spec-row">
                <div class="k-modal-spec-left">
                    <span class="k-modal-spec-badge spec-${type}">${label}</span>
                    <span class="k-modal-spec-name">${escapeHtml(name)}</span>
                </div>${priceHtml}</div>`;
        }).join('') : `<div class="k-modal-no-specs">Standard / No special customization</div>`;

        return `<div class="k-modal-item">
            <div class="k-modal-item-header">
                <img class="k-modal-item-img" src="${escapeHtml(item.image||'')}" alt="" onerror="this.src='/images/hero-burger.webp'">
                <span class="k-modal-item-qty">${qty}×</span>
                <span class="k-modal-item-name">${escapeHtml(item.name)}</span>
                <span class="k-modal-item-price">₱${itemTotal.toLocaleString()}</span>
            </div>
            <div class="k-modal-specs">
                <div class="k-modal-specs-label">📋 Specifications / Choices</div>
                ${specsInner}
            </div>
        </div>`;
    }).join('');
    document.getElementById('modalItems').innerHTML = itemsHtml;

    document.getElementById('modalNotes').innerHTML = order.notes
        ? `<div class="k-modal-notes">📝 <strong>Note:</strong> ${escapeHtml(order.notes)}</div>` : '';

    // Show table number prominently for dine-in
    const tableNoteEl = document.getElementById('modalTableNote');
    if (tableNoteEl) {
        if (order.order_type === 'dine_in' && order.table_number) {
            tableNoteEl.innerHTML = `<div class="k-modal-notes" style="background:rgba(250,204,21,.08);border:1px solid rgba(250,204,21,.25);color:#facc15;">🪑 <strong>Table Number:</strong> ${escapeHtml(order.table_number)}</div>`;
            tableNoteEl.style.display = 'block';
        } else {
            tableNoteEl.style.display = 'none';
        }
    }

    const delivery = parseFloat(order.delivery_fee ?? 50);
    const total    = parseFloat(order.total > 0 ? order.total : (order.subtotal + delivery));
    const displaySub = parseFloat(order.subtotal > 0 ? order.subtotal : subtotal);
    document.getElementById('modalFooter').innerHTML =
        `<div class="k-modal-total-row"><span>Subtotal</span><span>₱${displaySub.toLocaleString()}</span></div>` +
        `<div class="k-modal-total-row"><span>Delivery fee</span><span>${delivery === 0 ? '<span style="color:#4ade80">FREE</span>' : '₱' + delivery.toLocaleString()}</span></div>` +
        (order.payment_method ? `<div class="k-modal-total-row"><span>Payment</span><span style="text-transform:capitalize;">${escapeHtml(order.payment_method === 'cash' ? '💵 Cash on Delivery' : order.payment_method === 'gcash' ? '📱 GCash' : '💳 Card')}</span></div>` : '') +
        `<div class="k-modal-total-row grand"><span>Total</span><span>₱${total.toLocaleString()}</span></div>`;

    const col = order.column;
    let btn = '';
    let printBtn = `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);flex:0 0 auto;width:3rem;" onclick="printReceipt('/chef/orders/${order.id}/receipt')" title="Print Receipt"><i data-lucide="printer" style="width:16px;height:16px;"></i></button>`;

    if (col === 'new') {
        // Pending — admin accepts
        btn = IS_ADMIN
            ? `<button class="k-btn k-btn-accept" style="flex:1;" onclick="modalAction('accept',${order.id})">✓ Accept Order</button>`
            : `<button class="k-btn" style="flex:1;background:rgba(255,255,255,.06);color:var(--text-muted);" disabled>Waiting for acceptance…</button>`;
    }
    if (col === 'queued') {
        // Accepted — chef starts cooking
        btn = `<button class="k-btn k-btn-cook" style="flex:1;" onclick="modalAction('start',${order.id})">🍳 Start Cooking</button>`;
    }
    if (col === 'cooking') btn = `<button class="k-btn k-btn-ready" style="flex:1;" onclick="modalAction('ready',${order.id})">Mark Ready</button>`;

    document.getElementById('modalActions').innerHTML =
        (col !== 'new' ? printBtn : '') +
        btn + `<button class="k-btn" style="background:rgba(255,255,255,.06);color:var(--text-muted);flex:0 0 auto;padding:.6rem 1.2rem;" onclick="closeOrderModal()">Close</button>`;

    document.getElementById('orderModal').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeOrderModal(e) {
    if (e && e.target !== document.getElementById('orderModal')) return;
    document.getElementById('orderModal').classList.remove('open');
    document.body.style.overflow = '';
}

async function modalAction(action, orderId) {
    const btn = document.querySelector('#modalActions button:first-child');
    if (btn) { btn.disabled = true; btn.textContent = '…'; }
    const urls = { accept: ACCEPT_URL(orderId), start: START_URL(orderId), ready: READY_URL(orderId) };
    try {
        const res = await fetch(urls[action], { method:'POST', headers:{ 'X-CSRF-TOKEN':CSRF_TOKEN, 'Accept':'application/json', 'Content-Type':'application/json' } });
        const data = await res.json();
        if (!data.success) { alert(data.message || 'Action failed.'); if (btn) btn.disabled = false; return; }

        if (data.receipt_url) {
            printReceipt(data.receipt_url);
        }

        closeOrderModal();
        await refreshKitchen(true);
    } catch(e) { alert('Network error.'); if (btn) btn.disabled = false; }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeOrderModal(); });

function showToast(message, type = 'success', duration = 3000) {
    const toastContainer = document.getElementById('toastContainer') || (() => {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:10000;display:flex;flex-direction:column;gap:8px;';
        document.body.appendChild(container);
        return container;
    })();

    const toast = document.createElement('div');
    const bgColor = type === 'success' ? 'rgba(16,185,129,.9)' : type === 'error' ? 'rgba(239,68,68,.9)' : 'rgba(59,130,246,.9)';
    const textColor = '#ffffff';
    
    toast.style.cssText = `
        background: ${bgColor};
        color: ${textColor};
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        backdrop-filter: blur(8px);
        max-width: 300px;
        word-wrap: break-word;
        animation: slideIn 0.3s ease;
    `;
    
    toast.textContent = message;
    toastContainer.appendChild(toast);

    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 300);
    }, duration);
}

// Add CSS animations
if (!document.getElementById('toastStyles')) {
    const style = document.createElement('style');
    style.id = 'toastStyles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function autoPrintKitchenTicket(orderId) {
    const url = `/chef/orders/${orderId}/kitchen-ticket`;

    // ── Silent iframe print ────────────────────────────────────────────────
    // WAHB bridge does NOT support HTML URLs (only PDF/images).
    // We use a hidden iframe so the browser's native print handles HTML tickets.
    wahbLog('info', 'Print', 'Silent iframe print for kitchen ticket', 'orderId=' + orderId + ' url=' + url);

    const iframe = document.createElement('iframe');
    iframe.style.cssText = [
        'position:fixed', 'top:-99999px', 'left:-99999px',
        'width:1px', 'height:1px', 'border:0',
        'opacity:0', 'pointer-events:none', 'z-index:-9999'
    ].join(';');
    iframe.setAttribute('tabindex', '-1');
    iframe.setAttribute('aria-hidden', 'true');
    document.body.appendChild(iframe);

    iframe.onload = function () {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
            showToast('🖨️ Kitchen ticket printing…', 'success', 1800);
        } catch (e) {
            wahbLog('error', 'Print', 'iframe print failed: ' + e.message, url);
        }
        setTimeout(function () {
            if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
        }, 2000);
    };

    iframe.onerror = function () {
        if (iframe.parentNode) iframe.parentNode.removeChild(iframe);
        // Fallback: popup
        wahbLog('warn', 'Print', 'iframe load failed — using popup fallback', url);
        const w = window.open(url, 'kitchen_print_' + orderId, 'width=300,height=500,left=0,top=0,toolbar=0,scrollbars=0,status=0,menubar=0,location=0');
        if (w) { showToast('🖨️ Kitchen ticket printing...', 'success', 2000); return; }
        showToast('⚠️ Popup blocked. Open the ticket manually.', 'error', 5000);
    };

    iframe.src = url;
}

function printReceipt(receiptUrl) {
    // ── WAHB silent print ──────────────────────────────────────────────────
    if (window._wahbPrinter && window._wahbPrinter.isConnected()) {
        const sent = window._wahbPrinter.printReceiptUrl(receiptUrl);
        wahbLog(sent ? 'success' : 'error', 'Print', sent ? 'Receipt sent to bridge ✓' : 'Send FAILED', receiptUrl);
        if (sent) return;
    } else {
        wahbLog('warn', 'Print', 'WAHB not connected — using popup fallback', receiptUrl);
    }

    // ── Fallback: popup ────────────────────────────────────────────────────
    const w = 220, h = 800;
    const left = Math.round((screen.width - w) / 2);
    const top  = Math.round((screen.height - h) / 2);
    const win = window.open(receiptUrl, 'receipt_print', `width=${w},height=${h},left=${left},top=${top},toolbar=0,scrollbars=0,status=0,menubar=0,location=0`);
    if (!win) window.open(receiptUrl, '_blank');
}

function kitchenAutoPrint(receiptUrl) {
    // ── WAHB silent print ──────────────────────────────────────────────────
    if (window._wahbPrinter && window._wahbPrinter.isConnected()) {
        const sent = window._wahbPrinter.printReceiptUrl(receiptUrl);
        wahbLog(sent ? 'success' : 'error', 'Print', sent ? 'Auto-receipt sent to bridge ✓' : 'Send FAILED', receiptUrl);
        if (sent) return;
    } else {
        wahbLog('warn', 'Print', 'WAHB not connected — using popup fallback', receiptUrl);
    }

    // ── Fallback: popup only ───────────────────────────────────────────────
    const win = window.open(receiptUrl, 'kitchen_receipt_print', 'width=300,height=600,left=0,top=0,toolbar=0,scrollbars=0,status=0,menubar=0,location=0');
    if (!win) window.open(receiptUrl, '_blank');
}

async function kitchenAction(action, orderId, btn) {
    const urls = {
        accept: ACCEPT_URL(orderId),
        start:  START_URL(orderId),
        ready:  READY_URL(orderId),
    };

    btn.disabled = true;
    btn.textContent = '…';

    try {
        const res = await fetch(urls[action], {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });
        const data = await res.json();
        if (!data.success) {
            alert(data.message || 'Action failed.');
            btn.disabled = false;
            return;
        }

        if (data.receipt_url) {
            printReceipt(data.receipt_url);
        }

        await refreshKitchen(true);
    } catch (e) {
        alert('Network error. Please try again.');
        btn.disabled = false;
    }
}

async function refreshKitchen(manual) {
    console.log('[KITCHEN] Starting refresh...', { manual, url: KITCHEN_URL });
    
    try {
        console.log('[KITCHEN] Fetching from:', KITCHEN_URL);
        
        const controller = new AbortController();
        const timer = setTimeout(() => {
            console.log('[KITCHEN] Fetch timeout after 8s');
            controller.abort();
        }, 8000);

        const res = await fetch(KITCHEN_URL, {
            headers: { 'Accept': 'application/json' },
            signal: controller.signal,
        });
        clearTimeout(timer);
        
        console.log('[KITCHEN] Response status:', res.status, res.statusText);
        console.log('[KITCHEN] Response headers:', Array.from(res.headers.entries()));

        if (!res.ok) throw new Error('HTTP ' + res.status);

        const contentType = res.headers.get('content-type') || '';
        console.log('[KITCHEN] Content-Type:', contentType);
        
        if (!contentType.includes('application/json')) {
            // Got HTML (e.g. redirect to login) — session expired
            console.warn('[KITCHEN] Non-JSON response - session expired?');
            setPollingStatus(false);
            setTimeout(() => location.reload(), 2000);
            return;
        }

        const data = await res.json();
        console.log('[KITCHEN] Parsed data keys:', Object.keys(data));
        console.log('[KITCHEN] Data counts:', {
            queued: data.queued?.length || 0,
            cooking: data.cooking?.length || 0
        });

        // Mark live BEFORE rendering so it always updates even if render throws
        console.log('[KITCHEN] About to set status to live...');
        setPollingStatus(true);
        console.log('[KITCHEN] Status set to live, now rendering...');

        renderColumn('queued',  data.queued  || []);
        renderColumn('cooking', data.cooking || []);

        // Auto-print newly accepted orders (queued column) — ALL order types
        (data.queued || []).forEach(order => {
            if (autoPrintEnabled && !printedOrderIds.has('accept_' + order.id)) {
                printedOrderIds.add('accept_' + order.id);
                console.log('🖨️ Auto-printing kitchen ticket for order', order.order_number);
                setTimeout(() => autoPrintKitchenTicket(order.id), 500);
            }
        });

        if (window.lucide) lucide.createIcons();
        console.log('[KITCHEN] Refresh completed successfully');

    } catch (e) {
        console.error('[KITCHEN] Refresh failed:', e);
        setPollingStatus(false);
        if (manual) alert('Could not refresh kitchen board: ' + e.message);
    }
}

function playNewOrderSound() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        [880, 1100].forEach((freq, i) => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.frequency.value = freq;
            gain.gain.value = 0.08;
            osc.start(ctx.currentTime + i * 0.15);
            osc.stop(ctx.currentTime + i * 0.15 + 0.12);
        });
    } catch (e) {}
}

function updateWSStatus(connected) {
    const statusEl = document.getElementById('wsStatusText');
    const dotEl    = document.querySelector('.live-dot');
    const wsEl     = document.getElementById('wsStatus');

    if (connected) {
        if (statusEl) statusEl.textContent = 'Live (WS)';
        if (dotEl)    dotEl.style.background = '#22c55e';
        if (wsEl)     wsEl.title = 'WebSocket connected + polling active';
    }
    // If disconnected, don't touch the indicator — polling heartbeat owns it
    // (avoids "Reconnecting..." flash when Reverb is simply not running)
}

// Called after every successful poll
function setPollingStatus(ok) {
    console.log('[STATUS] Setting polling status:', ok);
    const statusEl = document.getElementById('wsStatusText');
    const dotEl    = document.querySelector('.live-dot');
    const wsEl     = document.getElementById('wsStatus');
    
    console.log('[STATUS] Elements found:', {
        statusEl: !!statusEl,
        dotEl: !!dotEl, 
        wsEl: !!wsEl,
        currentText: statusEl?.textContent
    });
    
    if (ok) {
        if (statusEl && !statusEl.textContent.includes('WS')) {
            statusEl.textContent = 'Live';
            console.log('[STATUS] Updated to Live');
        }
        if (dotEl) {
            dotEl.style.background = '#22c55e';
            console.log('[STATUS] Dot color updated to green');
        }
        if (wsEl) wsEl.title = 'Polling every 3s — connected';
    } else {
        if (statusEl) {
            statusEl.textContent = 'Offline';
            console.log('[STATUS] Updated to Offline');
        }
        if (dotEl) {
            dotEl.style.background = '#ef4444';
            console.log('[STATUS] Dot color updated to red');
        }
        if (wsEl) wsEl.title = 'Cannot reach server';
    }
}

function toggleKitchenFullscreen() {
    document.body.classList.toggle('kitchen-fullscreen');
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('[INIT] DOM loaded, initializing kitchen dashboard...');

    // ── Init WAHB silent printer ───────────────────────────────────────────
    if (typeof WAHBPrinter !== 'undefined') {
        const savedKey = localStorage.getItem('wahb_printer_key') || 'RECEIPT';
        const savedUrl = localStorage.getItem('wahb_bridge_url')  || 'ws://127.0.0.1:12212/printer';
        window._wahbPrinter = new WAHBPrinter({ url: savedUrl, printerKey: savedKey });
        _patchWahbLogging();
        wahbLog('info', 'Init', 'WAHBPrinter initializing…', 'key=' + savedKey + ' url=' + savedUrl);
    }

    // Verify critical elements exist
    const statusEl = document.getElementById('wsStatusText');
    const dotEl = document.querySelector('.live-dot');
    const wsEl = document.getElementById('wsStatus');

    console.log('[INIT] Critical elements check:', {
        statusEl: !!statusEl,
        dotEl: !!dotEl,
        wsEl: !!wsEl
    });
    
    if (!statusEl) {
        console.error('[INIT] wsStatusText element not found!');
    } else {
        console.log('[INIT] Initial status text:', statusEl.textContent);
    }
    
    if (window.lucide) lucide.createIcons();

    // Check if auto-print was previously enabled
    if (localStorage.getItem('autoPrintEnabled') === 'true') {
        autoPrintEnabled = true;
    } else {
        // Show auto-print banner if not enabled
        const banner = document.getElementById('autoPrintBanner');
        if (banner) banner.style.display = 'flex';
    }

    // Enable auto-print on any user interaction
    const enableOnInteraction = () => {
        if (!autoPrintEnabled) {
            enableAutoPrint();
        }
        // Remove listeners after first interaction
        document.removeEventListener('click', enableOnInteraction);
        document.removeEventListener('keydown', enableOnInteraction);
        document.removeEventListener('touchstart', enableOnInteraction);
    };

    document.addEventListener('click', enableOnInteraction);
    document.addEventListener('keydown', enableOnInteraction);
    document.addEventListener('touchstart', enableOnInteraction);

    // Hide popup notice if already dismissed
    if (localStorage.getItem('kitchenPopupDismissed')) {
        const n = document.getElementById('popupNotice');
        if (n) n.style.display = 'none';
    }

    console.log('[INIT] Seeding orderDataMap with initial data...');
    // Seed orderDataMap from initial server-rendered data
    @php
        $allKitchenOrders = array_merge(
            $queuedOrders->all(),
            $cookingOrders->all(),
            $readyOrders->all()
        );
        $kitchenSeed = [];
        foreach ($allKitchenOrders as $o) {
            if ($o->status === 'pending') {
                $col = 'new';
            } elseif ($o->status === 'accepted') {
                $col = 'queued';
            } else {
                $col = 'cooking';
            }
            $kitchenSeed[] = [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'customer'     => $o->user?->name ?? 'Guest',
                'status'       => $o->status,
                'order_type'   => $o->order_type,
                'order_type_label' => $o->order_type_label,
                'order_type_icon' => $o->order_type_icon,
                'placed_at'    => $o->created_at->format('g:i A'),
                'elapsed_mins' => (int) $o->created_at->diffInMinutes(now()),
                'notes'        => $o->notes,
                'table_number' => $o->table_number,
                'subtotal'     => (float) ($o->subtotal ?? 0),
                'delivery_fee' => (float) ($o->delivery_fee ?? 50),
                'total'        => (float) ($o->total ?? 0),
                'payment_method' => $o->payment_method,
                'rider_name'   => $o->rider?->user?->name,
                'rider_id'     => $o->rider_id,
                'column'       => $col,
                'items'        => $o->items->map(function($i) {
                    $mods = collect($i->modifiers ?? [])
                        ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
                        ->values()
                        ->all();
                    return [
                        'name'      => $i->item_name,
                        'qty'       => $i->quantity,
                        'price'     => (float) $i->unit_price,
                        'subtotal'  => (float) $i->subtotal,
                        'image'     => $i->image ? asset($i->image) : asset('images/hero-burger.webp'),
                        'modifiers' => $mods,
                    ];
                })->all(),
            ];
        }
    @endphp
    const _kitchenSeed = @json($kitchenSeed);
    console.log('[INIT] Kitchen seed data:', _kitchenSeed.length, 'orders');
    _kitchenSeed.forEach(o => {
        orderDataMap[o.id] = o;
        // Seed all namespaced keys so page reload never re-prints existing orders
        if (['accepted','preparing','rider_assigned','out_for_delivery','delivered'].includes(o.status)) {
            printedOrderIds.add('accept_' + o.id);
        }
        if (['rider_assigned','out_for_delivery','delivered'].includes(o.status)) {
            printedOrderIds.add('ready_' + o.id);
            printedOrderIds.add('pickup_' + o.id);
        }
    });

    // Wire click on blade-rendered cards (before first AJAX refresh)
    document.querySelectorAll('.k-order-card[data-order-id]').forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function(e) {
            if (e.target.closest('.k-actions')) return;
            openOrderModal(parseInt(this.dataset.orderId));
        });
    });

    console.log('[INIT] Setting up Echo subscriptions...');
    // Echo: subscribe to kitchen channel for real-time order updates
    if (window.Echo) {
        try {
            window.Echo.private('kitchen')
                .listen('.order.updated', (order) => {
                    console.log('[ECHO] Order updated:', order.id, order.status);
                    // ── AUTO-PRINT RULES ──────────────────────────────────────
                    if (autoPrintEnabled && order.status === 'accepted' && !printedOrderIds.has('accept_' + order.id)) {
                        printedOrderIds.add('accept_' + order.id);
                        console.log('🖨️ Echo: Auto-printing kitchen ticket for order', order.order_number);
                        setTimeout(() => autoPrintKitchenTicket(order.id), 400);
                    }
                    if (autoPrintEnabled && order.status === 'preparing' && order.prepared_at && !printedOrderIds.has('ready_' + order.id)) {
                        printedOrderIds.add('ready_' + order.id);
                        autoPrintKitchenTicket(order.id);
                    }
                    if (autoPrintEnabled && order.status === 'out_for_delivery' && !printedOrderIds.has('pickup_' + order.id)) {
                        printedOrderIds.add('pickup_' + order.id);
                        autoPrintKitchenTicket(order.id);
                    }
                    // Full kitchen refresh to re-categorise the order
                    refreshKitchen(false);
                });

            // Listen to actual WebSocket connection events — don't assume connected
            if (window.Echo.connector?.pusher) {
                window.Echo.connector.pusher.connection.bind('connected', () => {
                    console.log('[ECHO] WebSocket connected');
                    updateWSStatus(true);
                });
                // Disconnected/unavailable: polling will handle status — no "Reconnecting..." shown
            }
        } catch (e) {
            console.warn('Echo subscription failed:', e);
        }
    } else {
        console.log('[INIT] Echo not available, relying on polling only');
    }
    // No else needed — polling heartbeat sets status green once it succeeds

    console.log('[INIT] Starting polling system...');
    // Always start polling immediately on load (3s interval, regardless of WS)
    if (!fallbackTimer) {
        console.log('[INIT] Initial refresh call...');
        refreshKitchen(false);
        console.log('[INIT] Setting up 3-second interval...');
        fallbackTimer = setInterval(() => refreshKitchen(false), 3000);
        console.log('[INIT] Polling system started');
    }
    
    // Fallback: Force status check after a short delay to ensure it's not stuck
    setTimeout(() => {
        const statusEl = document.getElementById('wsStatusText');
        if (statusEl && statusEl.textContent === 'Loading...') {
            console.warn('[FALLBACK] Status still shows Loading, forcing manual refresh...');
            refreshKitchen(true);
        }
    }, 5000);
});

// ── WAHB Web UI Modal ───────────────────────────────────────────────────────
function openWahbSettings() {
    const modal  = document.getElementById('wahbModal');
    const iframe = document.getElementById('wahbIframe');
    if (!modal || !iframe) return;
    iframe.src = 'http://127.0.0.1:12212';
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeWahbSettings() {
    const modal  = document.getElementById('wahbModal');
    const iframe = document.getElementById('wahbIframe');
    if (!modal || !iframe) return;
    modal.style.display = 'none';
    document.body.style.overflow = '';
    iframe.src = '';
}

// ── WAHB Console Log ──────────────────────────────────────────────────────
const _wahbLogs = [];

const _wahbColors = {
    error:   { text: '#f87171', label: 'ERR ' },
    warn:    { text: '#fbbf24', label: 'WARN' },
    success: { text: '#34d399', label: ' OK ' },
    info:    { text: '#94a3b8', label: 'INFO' },
};

function wahbLog(level, context, message, detail) {
    const ts = new Date().toLocaleTimeString('en-US', { hour12: false });
    const entry = { ts, level, context, message, detail: detail || '' };
    _wahbLogs.push(entry);

    // Red badge on Console button for errors
    const badge = document.getElementById('wahbConsoleBadge');
    if (badge && (level === 'error' || level === 'warn')) badge.style.display = 'inline-block';

    // Update count
    const cnt = document.getElementById('wahbConsoleCount');
    if (cnt) cnt.textContent = _wahbLogs.length;

    // Append row
    const body = document.getElementById('wahbConsoleBody');
    if (!body) return;
    if (_wahbLogs.length === 1) body.innerHTML = '';

    const c = _wahbColors[level] || _wahbColors.info;
    const row = document.createElement('div');
    row.style.cssText = 'padding:1px 0;display:flex;gap:.5rem;align-items:flex-start;border-bottom:1px solid rgba(255,255,255,.03);';
    row.innerHTML =
        `<span style="color:#374151;flex-shrink:0;">${ts}</span>` +
        `<span style="color:${c.text};font-weight:700;flex-shrink:0;letter-spacing:.03em;">[${c.label}]</span>` +
        `<span style="color:#6d7fcc;flex-shrink:0;min-width:72px;">${escapeHtml(context)}</span>` +
        `<span style="color:${c.text};">${escapeHtml(message)}` +
        (detail ? `<br><span style="color:#4b5563;font-size:.63rem;">${escapeHtml(String(detail))}</span>` : '') +
        `</span>`;
    body.appendChild(row);
    body.scrollTop = body.scrollHeight;

    // Update status bar
    const statusEl = document.getElementById('wahbConsoleStatus');
    if (statusEl) {
        if (level === 'error')        { statusEl.textContent = '● ERROR';       statusEl.style.color = '#f87171'; }
        else if (level === 'success') { statusEl.textContent = '● CONNECTED';   statusEl.style.color = '#34d399'; }
        else if (level === 'warn')    { statusEl.textContent = '● WARNING';      statusEl.style.color = '#fbbf24'; }
    }
}

function toggleWahbConsole() {
    const panel = document.getElementById('wahbConsolePanel');
    if (!panel) return;
    panel.style.display = panel.style.display === 'none' ? 'flex' : 'none';
    panel.style.flexDirection = 'column';
    // Clear red badge
    const badge = document.getElementById('wahbConsoleBadge');
    if (badge) badge.style.display = 'none';
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function wahbCopyConsole() {
    if (!_wahbLogs.length) return;
    const text = _wahbLogs.map(e =>
        `[${e.ts}] [${e.level.toUpperCase().padEnd(4)}] [${e.context}] ${e.message}` +
        (e.detail ? ` | ${e.detail}` : '')
    ).join('\n');
    navigator.clipboard.writeText(text).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = text; ta.style.cssText = 'position:fixed;left:-9999px;';
        document.body.appendChild(ta); ta.select();
        document.execCommand('copy'); ta.remove();
    }).then(() => {
        const btn = document.querySelector('#wahbConsolePanel button:nth-child(2)');
        if (btn) { const o = btn.innerHTML; btn.textContent = '✓ Copied!'; setTimeout(() => { btn.innerHTML = o; if(lucide) lucide.createIcons(); }, 1500); }
    });
}

function wahbClearConsole() {
    _wahbLogs.length = 0;
    const body = document.getElementById('wahbConsoleBody');
    const cnt  = document.getElementById('wahbConsoleCount');
    const st   = document.getElementById('wahbConsoleStatus');
    if (body) body.innerHTML = '<div style="color:#4b5563;font-style:italic;">Cleared — waiting for events…</div>';
    if (cnt)  cnt.textContent = '0';
    if (st)   { st.textContent = '● UNKNOWN'; st.style.color = '#6b7280'; }
}

function wahbRunDiagnostics() {
    // Open console if hidden
    const panel = document.getElementById('wahbConsolePanel');
    if (panel) { panel.style.display = 'flex'; panel.style.flexDirection = 'column'; }
    if (typeof lucide !== 'undefined') lucide.createIcons();

    wahbLog('info', 'Diag', '── Running diagnostics ──');

    // 1. Script loaded?
    if (typeof WAHBPrinter === 'undefined') {
        wahbLog('error', 'Diag', 'wahb-printer.js NOT loaded', 'Check if public/js/wahb-printer.js exists and script tag is present');
        return;
    }
    wahbLog('success', 'Diag', 'wahb-printer.js loaded ✓');

    // 2. Instance created?
    if (!window._wahbPrinter) {
        wahbLog('error', 'Diag', '_wahbPrinter instance is NULL', 'DOMContentLoaded may not have fired or WAHBPrinter init failed');
        return;
    }
    wahbLog('success', 'Diag', '_wahbPrinter instance exists ✓');

    // 3. Connection status
    const connected = window._wahbPrinter.isConnected();
    if (!connected) {
        wahbLog('error', 'Diag', 'WebSocket NOT connected', 'WAHB app may not be running on this PC — open http://127.0.0.1:12212');
        wahbLog('warn',  'Diag', 'Fallback active', 'Prints will use popup window with browser dialog');
    } else {
        wahbLog('success', 'Diag', 'WebSocket CONNECTED ✓', 'ws://127.0.0.1:12212/printer');
    }

    // 4. Printer key
    const key = window._wahbPrinter.getPrinterKey ? window._wahbPrinter.getPrinterKey() : '(unknown)';
    wahbLog('info', 'Diag', 'Printer key: ' + key, 'Must match key in WAHB Web UI → Printers');

    // 5. Origin (important for WAHB URL building)
    wahbLog('info', 'Diag', 'Site origin: ' + window.location.origin);

    // 6. Test send
    if (connected) {
        wahbLog('info', 'Diag', 'Sending test print job…');
        const testUrl = window.location.origin + '/chef/orders/1/receipt';
        const sent = window._wahbPrinter.printReceiptUrl(testUrl);
        if (sent) {
            wahbLog('success', 'Diag', 'Test job SENT ✓', 'URL: ' + testUrl);
            wahbLog('info',    'Diag', 'Check WAHB log at http://127.0.0.1:12212 to see if it was received');
        } else {
            wahbLog('error', 'Diag', 'Test job FAILED to send', 'WebSocket send() returned false');
        }
    }

    wahbLog('info', 'Diag', '── Done ──');
}

// ── WAHB Console ─────────────────────────────────────────────────────────
// Patch WAHBPrinter after init to pipe all status changes to console
function _patchWahbLogging() {
    if (!window._wahbPrinter) return;
    const orig = window._wahbPrinter.onStatusChange.bind(window._wahbPrinter);
    window._wahbPrinter.onStatusChange(status => {
        const map = {
            connected:    ['success', 'Connect', 'WebSocket CONNECTED ✓',    'ws://127.0.0.1:12212/printer'],
            connecting:   ['info',    'Connect', 'Connecting to bridge…',     'ws://127.0.0.1:12212/printer'],
            disconnected: ['error',   'Connect', 'WebSocket DISCONNECTED',    'Bridge may not be running'],
        };
        const args = map[status] || ['info', 'Connect', status];
        wahbLog(...args);

        // Also run the original status callback (button update)
        const btn = document.getElementById('wahbStatusBtn');
        if (btn) {
            const cfg = {
                connected:    { text:'🟢 Bridge Connected',   color:'#10b981' },
                connecting:   { text:'🟡 Bridge Connecting…', color:'#f59e0b' },
                disconnected: { text:'🔴 Bridge Settings',    color:null      },
            };
            const c = cfg[status] || cfg.disconnected;
            const span = btn.querySelector('span') || btn;
            btn.childNodes.forEach(n => { if (n.nodeType === 3) n.textContent = ' ' + c.text.replace(/^\S+\s/, ''); });
            if (c.color) btn.style.outlineColor = c.color;
        }
    });
}

// ── WAHB Console ─────────────────────────────────────────────────────────
// Close modal on Escape key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const modal = document.getElementById('wahbModal');
        if (modal && modal.style.display === 'block') closeWahbSettings();
    }
});
</script>
@endpush
