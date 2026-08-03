@extends('admin.layout')
@section('title', 'Settings')

@section('content')
<div class="page-header" style="display:flex;align-items:center;gap:.75rem;">
    <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(148,163,184,.12);display:flex;align-items:center;justify-content:center;">
        <i data-lucide="settings-2" style="width:1.2rem;height:1.2rem;color:#94a3b8;stroke-width:2;"></i>
    </div>
    <div>
        <h1 style="margin:0 0 .15rem;">Settings</h1>
        <p style="margin:0;">Configure restaurant details and your admin account.</p>
    </div>
</div>

<style>
#settingsGrid{display:grid;grid-template-columns:1fr;gap:1.5rem;}
@media(min-width:1024px){#settingsGrid{grid-template-columns:repeat(2,1fr);}}
</style>
<div id="settingsGrid">

    {{-- ── SHOP STATUS CARD ── --}}
    <div class="section-card" style="grid-column:1/-1;">
        <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <div style="width:10px;height:10px;border-radius:50%;background:{{ $isOpen ? '#22c55e' : '#ef4444' }};box-shadow:0 0 8px {{ $isOpen ? '#22c55e' : '#ef4444' }};"></div>
                <div>
                    <h2 style="font-size:.875rem;font-weight:700;color:var(--text-strong);margin:0 0 .1rem;">
                        Shop is currently <span style="color:{{ $isOpen ? '#22c55e' : '#ef4444' }};">{{ $isOpen ? 'OPEN' : 'CLOSED' }}</span>
                    </h2>
                    <p style="font-size:.72rem;color:var(--text-muted);margin:0;">Customers {{ $isOpen ? 'can' : 'cannot' }} place orders right now.</p>
                </div>
            </div>
            <button id="toggleOpenBtn" onclick="toggleShopOpen()"
                style="display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1.4rem;border-radius:.6rem;border:none;cursor:pointer;font-size:.8rem;font-weight:700;transition:all .2s;
                background:{{ $isOpen ? 'rgba(239,68,68,.12)' : 'rgba(34,197,94,.12)' }};
                color:{{ $isOpen ? '#ef4444' : '#22c55e' }};
                border:1px solid {{ $isOpen ? 'rgba(239,68,68,.3)' : 'rgba(34,197,94,.3)' }};">
                <i data-lucide="{{ $isOpen ? 'door-closed' : 'door-open' }}" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i>
                {{ $isOpen ? 'Close Shop Now' : 'Open Shop Now' }}
            </button>
        </div>
        <div style="padding:14px 20px;font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:8px;">
            <i data-lucide="info" style="width:.75rem;height:.75rem;stroke-width:2;flex-shrink:0;"></i>
            Toggling updates instantly — customers see "Closed" banner in real time without refreshing.
        </div>
    </div>

    {{-- Restaurant Information --}}
    <div class="section-card">
        <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;gap:.5rem;">
            <i data-lucide="store" style="width:1rem;height:1rem;color:var(--accent);stroke-width:2;"></i>
            <div>
                <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0 0 .1rem;">Restaurant Information</h2>
                <p style="font-size:.72rem;color:var(--text-muted);margin:0;">General details shown to customers</p>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-5" style="display:flex;flex-direction:column;gap:1rem;">
            @csrf
            <div>
                <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                    <i data-lucide="building-2" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Restaurant Name
                </label>
                <input type="text" name="restaurant_name" value="E.U.T Snack House" class="admin-input" placeholder="e.g. E.U.T Snack House">
            </div>
            <div>
                <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                    <i data-lucide="mail" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Contact Email
                </label>
                <input type="email" name="contact_email" value="info@eutrestaurant.com" class="admin-input" placeholder="info@restaurant.com">
            </div>
            <div>
                <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                    <i data-lucide="phone" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Contact Phone
                </label>
                <input type="text" name="contact_phone" value="+63 912 345 6789" class="admin-input" placeholder="+63 900 000 0000">
            </div>
            <div>
                <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                    <i data-lucide="map-pin" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Address
                </label>
                <input type="text" name="address" value="123 Food Street, Culinary District" class="admin-input" placeholder="Full address">
            </div>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;">
                <div>
                    <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                        <i data-lucide="bike" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Delivery Fee (₱)
                    </label>
                    <input type="number" name="delivery_fee" value="50" min="0" class="admin-input">
                </div>
                <div>
                    <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                        <i data-lucide="shopping-cart" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Minimum Order (₱)
                    </label>
                    <input type="number" name="min_order" value="200" min="0" class="admin-input">
                </div>
            </div>
            <button type="submit" class="btn-primary w-full" style="display:flex;align-items:center;justify-content:center;gap:.4rem;">
                <i data-lucide="save" style="width:.9rem;height:.9rem;stroke-width:2.5;"></i> Save Restaurant Settings
            </button>
        </form>
    </div>

    <div style="display:flex;flex-direction:column;gap:1.5rem;">

        {{-- Change Password --}}
        <div class="section-card">
            <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="key-round" style="width:1rem;height:1rem;color:var(--accent);stroke-width:2;"></i>
                <div>
                    <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0 0 .1rem;">Change Password</h2>
                    <p style="font-size:.72rem;color:var(--text-muted);margin:0;">Update your admin account password</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.settings.password') }}" class="p-5" style="display:flex;flex-direction:column;gap:1rem;">
                @csrf
                <div>
                    <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                        <i data-lucide="lock" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Current Password
                    </label>
                    <input type="password" name="current_password" class="admin-input" placeholder="••••••••">
                    @error('current_password')<p style="color:#dc2626;font-size:.75rem;margin:.25rem 0 0;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                        <i data-lucide="lock-keyhole" style="width:.75rem;height:.75rem;stroke-width:2;"></i> New Password
                    </label>
                    <input type="password" name="password" class="admin-input" placeholder="Min. 8 characters">
                    @error('password')<p style="color:#dc2626;font-size:.75rem;margin:.25rem 0 0;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="field-label" style="display:flex;align-items:center;gap:.3rem;">
                        <i data-lucide="shield-check" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Confirm New Password
                    </label>
                    <input type="password" name="password_confirmation" class="admin-input" placeholder="Repeat new password">
                </div>
                <button type="submit" class="btn-primary w-full" style="display:flex;align-items:center;justify-content:center;gap:.4rem;">
                    <i data-lucide="refresh-cw" style="width:.9rem;height:.9rem;stroke-width:2.5;"></i> Update Password
                </button>
            </form>
        </div>

        {{-- Admin Account --}}
        <div class="section-card">
            <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="circle-user-round" style="width:1rem;height:1rem;color:var(--accent);stroke-width:2;"></i>
                <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0;">Your Admin Account</h2>
            </div>
            <div class="p-5">
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="Avatar" class="w-14 h-14 rounded-full object-cover" style="border:2px solid var(--accent-border);">
                    @else
                        <div class="w-14 h-14 rounded-full flex items-center justify-center font-bold text-xl shrink-0" style="background:var(--accent);color:#fff;">
                            {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                        </div>
                    @endif
                    <div>
                        <p style="font-weight:600;color:var(--text-strong);margin:0 0 .2rem;">{{ auth()->user()->name }}</p>
                        <p style="font-size:.875rem;color:var(--text-muted);margin:0 0 .4rem;">{{ auth()->user()->email }}</p>
                        <span class="badge badge-admin" style="display:inline-flex;align-items:center;gap:.3rem;">
                            <i data-lucide="shield" style="width:.65rem;height:.65rem;stroke-width:2.5;"></i> Administrator
                        </span>
                    </div>
                </div>
                <div style="border-top:1px solid var(--border-divider);padding-top:1rem;display:flex;flex-direction:column;gap:.6rem;font-size:.875rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:var(--text-muted);display:flex;align-items:center;gap:.35rem;">
                            <i data-lucide="globe" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Provider
                        </span>
                        <span style="color:var(--text-body);">{{ ucfirst(auth()->user()->provider ?? 'email') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:var(--text-muted);display:flex;align-items:center;gap:.35rem;">
                            <i data-lucide="calendar" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Member Since
                        </span>
                        <span style="color:var(--text-body);">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:var(--text-muted);display:flex;align-items:center;gap:.35rem;">
                            <i data-lucide="hash" style="width:.8rem;height:.8rem;stroke-width:2;"></i> User ID
                        </span>
                        <span style="font-family:monospace;font-size:.75rem;color:var(--text-muted);">#{{ auth()->user()->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="section-card" style="border-color:rgba(220,38,38,.2);">
            <div class="px-5 py-4" style="border-bottom:1px solid rgba(220,38,38,.15);display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="triangle-alert" style="width:1rem;height:1rem;color:#dc2626;stroke-width:2;"></i>
                <div>
                    <h2 style="font-size:.875rem;font-weight:600;color:#dc2626;margin:0 0 .1rem;">Danger Zone</h2>
                    <p style="font-size:.72rem;color:var(--text-muted);margin:0;">Irreversible actions — proceed with caution</p>
                </div>
            </div>
            <div class="p-5" style="display:flex;flex-direction:column;gap:.875rem;">
                <div style="display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p style="font-size:.875rem;font-weight:500;color:var(--text-strong);margin:0 0 .2rem;display:flex;align-items:center;gap:.35rem;">
                            <i data-lucide="trash-2" style="width:.85rem;height:.85rem;stroke-width:2;color:#dc2626;"></i> Clear Cache
                        </p>
                        <p style="font-size:.75rem;color:var(--text-muted);margin:0;">Clear Laravel's compiled views and config cache</p>
                    </div>
                    <button onclick="alert('Connect to an Artisan command route to activate.')" class="btn-danger">Clear</button>
                </div>
                <div style="border-top:1px solid var(--border-divider);padding-top:.875rem;display:flex;align-items:center;justify-content:space-between;">
                    <div>
                        <p style="font-size:.875rem;font-weight:500;color:var(--text-strong);margin:0 0 .2rem;display:flex;align-items:center;gap:.35rem;">
                            <i data-lucide="external-link" style="width:.85rem;height:.85rem;stroke-width:2;"></i> Back to Site
                        </p>
                        <p style="font-size:.75rem;color:var(--text-muted);margin:0;">Return to the customer-facing restaurant page</p>
                    </div>
                    <a href="{{ route('home') }}" class="btn-ghost" style="font-size:.75rem;display:inline-flex;align-items:center;gap:.35rem;">
                        <i data-lucide="arrow-left" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i> Go to Site
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
async function toggleShopOpen() {
    const btn = document.getElementById('toggleOpenBtn');
    btn.disabled = true;
    btn.innerHTML = '<i data-lucide="loader-2" style="width:.85rem;height:.85rem;animation:spin 1s linear infinite;"></i> Updating…';

    try {
        const res = await fetch('{{ route("admin.settings.toggle-open") }}', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
        });
        const data = await res.json();
        if (data.success) {
            // Reload page to reflect new state cleanly
            window.location.reload();
        } else {
            alert('Failed to update shop status.');
            btn.disabled = false;
        }
    } catch(e) {
        alert('Network error.');
        btn.disabled = false;
    }
}
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
