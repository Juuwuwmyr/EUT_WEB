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

    {{-- ── SHOP SERVICE STATUS CARD (DELIVERY / PICKUP / DINE-IN) ── --}}
    <div class="section-card" style="grid-column:1/-1;">
        <div class="px-5 py-4 card-header-border" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
            <div>
                <h2 style="font-size:1rem;font-weight:700;color:var(--text-strong);margin:0 0 .2rem;display:flex;align-items:center;gap:.5rem;">
                    <i data-lucide="store" style="width:1.1rem;height:1.1rem;color:var(--accent);"></i>
                    Store Operating Status per Service
                </h2>
                <p style="font-size:.78rem;color:var(--text-muted);margin:0;">Control whether Delivery, Pickup, and Dine In services are independently OPEN or CLOSED.</p>
            </div>
            <button id="toggleAllBtn" onclick="toggleService('all')"
                style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;border-radius:.6rem;cursor:pointer;font-size:.78rem;font-weight:700;transition:all .2s;
                background:{{ $isOpen ? 'rgba(239,68,68,.12)' : 'rgba(34,197,94,.12)' }};
                color:{{ $isOpen ? '#ef4444' : '#22c55e' }};
                border:1px solid {{ $isOpen ? 'rgba(239,68,68,.3)' : 'rgba(34,197,94,.3)' }};">
                <i data-lucide="{{ $isOpen ? 'power-off' : 'power' }}" style="width:.8rem;height:.8rem;stroke-width:2.5;"></i>
                {{ $isOpen ? 'Close All Services' : 'Open All Services' }}
            </button>
        </div>

        <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
            {{-- Delivery Service --}}
            <div style="background:var(--bg-card-subtle,#1e2030);border:1px solid var(--border-divider,rgba(255,255,255,.08));border-radius:.8rem;padding:1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:38px;height:38px;border-radius:.6rem;background:rgba(56,189,248,.12);color:#38bdf8;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        🛵
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:var(--text-strong);">Delivery</div>
                        <div style="font-size:.72rem;display:flex;align-items:center;gap:.3rem;color:{{ $isOpenDelivery ? '#22c55e' : '#ef4444' }};font-weight:600;">
                            <span style="width:7px;height:7px;border-radius:50%;background:{{ $isOpenDelivery ? '#22c55e' : '#ef4444' }};display:inline-block;"></span>
                            {{ $isOpenDelivery ? 'OPEN' : 'CLOSED' }}
                        </div>
                    </div>
                </div>
                <button id="toggleDeliveryBtn" onclick="toggleService('delivery')"
                    style="padding:.45rem 1rem;border-radius:.5rem;font-size:.75rem;font-weight:700;cursor:pointer;transition:all .15s;
                    background:{{ $isOpenDelivery ? 'rgba(239,68,68,.15)' : 'rgba(34,197,94,.15)' }};
                    color:{{ $isOpenDelivery ? '#ef4444' : '#22c55e' }};
                    border:1px solid {{ $isOpenDelivery ? 'rgba(239,68,68,.3)' : 'rgba(34,197,94,.3)' }};">
                    {{ $isOpenDelivery ? 'Close Delivery' : 'Open Delivery' }}
                </button>
            </div>

            {{-- Pickup Service --}}
            <div style="background:var(--bg-card-subtle,#1e2030);border:1px solid var(--border-divider,rgba(255,255,255,.08));border-radius:.8rem;padding:1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:38px;height:38px;border-radius:.6rem;background:rgba(250,204,21,.12);color:#facc15;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        📦
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:var(--text-strong);">Pickup</div>
                        <div style="font-size:.72rem;display:flex;align-items:center;gap:.3rem;color:{{ $isOpenPickup ? '#22c55e' : '#ef4444' }};font-weight:600;">
                            <span style="width:7px;height:7px;border-radius:50%;background:{{ $isOpenPickup ? '#22c55e' : '#ef4444' }};display:inline-block;"></span>
                            {{ $isOpenPickup ? 'OPEN' : 'CLOSED' }}
                        </div>
                    </div>
                </div>
                <button id="togglePickupBtn" onclick="toggleService('pickup')"
                    style="padding:.45rem 1rem;border-radius:.5rem;font-size:.75rem;font-weight:700;cursor:pointer;transition:all .15s;
                    background:{{ $isOpenPickup ? 'rgba(239,68,68,.15)' : 'rgba(34,197,94,.15)' }};
                    color:{{ $isOpenPickup ? '#ef4444' : '#22c55e' }};
                    border:1px solid {{ $isOpenPickup ? 'rgba(239,68,68,.3)' : 'rgba(34,197,94,.3)' }};">
                    {{ $isOpenPickup ? 'Close Pickup' : 'Open Pickup' }}
                </button>
            </div>

            {{-- Dine-In Service --}}
            <div style="background:var(--bg-card-subtle,#1e2030);border:1px solid var(--border-divider,rgba(255,255,255,.08));border-radius:.8rem;padding:1rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:38px;height:38px;border-radius:.6rem;background:rgba(168,85,247,.12);color:#c084fc;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        🪑
                    </div>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:var(--text-strong);">Dine In</div>
                        <div style="font-size:.72rem;display:flex;align-items:center;gap:.3rem;color:{{ $isOpenDineIn ? '#22c55e' : '#ef4444' }};font-weight:600;">
                            <span style="width:7px;height:7px;border-radius:50%;background:{{ $isOpenDineIn ? '#22c55e' : '#ef4444' }};display:inline-block;"></span>
                            {{ $isOpenDineIn ? 'OPEN' : 'CLOSED' }}
                        </div>
                    </div>
                </div>
                <button id="toggleDineInBtn" onclick="toggleService('dine_in')"
                    style="padding:.45rem 1rem;border-radius:.5rem;font-size:.75rem;font-weight:700;cursor:pointer;transition:all .15s;
                    background:{{ $isOpenDineIn ? 'rgba(239,68,68,.15)' : 'rgba(34,197,94,.15)' }};
                    color:{{ $isOpenDineIn ? '#ef4444' : '#22c55e' }};
                    border:1px solid {{ $isOpenDineIn ? 'rgba(239,68,68,.3)' : 'rgba(34,197,94,.3)' }};">
                    {{ $isOpenDineIn ? 'Close Dine In' : 'Open Dine In' }}
                </button>
            </div>
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
                            <i data-lucide="refresh-ccw" style="width:.85rem;height:.85rem;stroke-width:2;color:#dc2626;"></i> Reset Month
                        </p>
                        <p style="font-size:.75rem;color:var(--text-muted);margin:0;">Archive delivered &amp; cancelled orders to JSON, then delete from DB</p>
                    </div>
                    <button onclick="openResetModal()" class="btn-danger">Reset</button>
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

{{-- ── RESET MONTH MODAL ── --}}
<div id="resetModal"
     style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:1rem;">
    <div style="width:100%;max-width:420px;background:#13141f;border:1px solid rgba(239,68,68,.3);border-radius:1.1rem;padding:1.75rem 1.5rem;box-shadow:0 24px 60px rgba(0,0,0,.6);">

        <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.1rem;">
            <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(239,68,68,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i data-lucide="refresh-ccw" style="width:1.1rem;height:1.1rem;color:#ef4444;stroke-width:2;"></i>
            </div>
            <div>
                <h3 style="margin:0;font-size:1rem;font-weight:700;color:#fff;">Reset Month</h3>
                <p style="margin:0;font-size:.73rem;color:#9ca3af;">Archive orders to JSON, then delete from DB</p>
            </div>
            <button onclick="closeResetModal()" style="margin-left:auto;background:none;border:none;color:#6b7280;cursor:pointer;padding:.25rem;line-height:1;">
                <i data-lucide="x" style="width:1.1rem;height:1.1rem;stroke-width:2.5;"></i>
            </button>
        </div>

        <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:.75rem;padding:.85rem 1rem;margin-bottom:1.25rem;display:flex;gap:.65rem;align-items:flex-start;">
            <i data-lucide="triangle-alert" style="width:1rem;height:1rem;color:#ef4444;flex-shrink:0;margin-top:.1rem;stroke-width:2;"></i>
            <p style="margin:0;font-size:.77rem;color:#fca5a5;line-height:1.5;">
                <strong>Irreversible.</strong> Orders are saved to
                <code style="background:rgba(255,255,255,.08);border-radius:.3rem;padding:.05rem .3rem;font-size:.72rem;">storage/app/archives/orders-YYYY-MM.json</code>
                before deletion.
            </p>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:1.25rem;">
            <div>
                <label style="display:block;font-size:.7rem;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:.4rem;">Month</label>
                <select id="resetMonth" style="width:100%;background:#0d0e1a;border:1px solid rgba(255,255,255,.1);border-radius:.65rem;padding:.65rem .85rem;font-size:.875rem;color:#e5e7eb;outline:none;cursor:pointer;">
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
                <select id="resetYear" style="width:100%;background:#0d0e1a;border:1px solid rgba(255,255,255,.1);border-radius:.65rem;padding:.65rem .85rem;font-size:.875rem;color:#e5e7eb;outline:none;cursor:pointer;">
                    @php $defaultYear = now()->subMonthNoOverflow()->year; @endphp
                    @for($y = now()->year; $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ $y === $defaultYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

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

        <div id="resetResult" style="display:none;margin-bottom:1rem;border-radius:.65rem;padding:.7rem .9rem;font-size:.8rem;font-weight:600;"></div>

        <div style="display:flex;gap:.75rem;">
            <button onclick="closeResetModal()"
                style="flex:1;padding:.75rem;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:.75rem;color:#9ca3af;font-size:.85rem;font-weight:600;cursor:pointer;transition:all .2s;"
                onmouseenter="this.style.background='rgba(255,255,255,.1)';"
                onmouseleave="this.style.background='rgba(255,255,255,.05)';">Cancel</button>
            <button id="resetConfirmBtn" onclick="submitReset()" disabled
                style="flex:1;padding:.75rem;background:linear-gradient(135deg,#dc2626,#ef4444);border:none;border-radius:.75rem;color:#fff;font-size:.85rem;font-weight:700;cursor:pointer;transition:opacity .2s;opacity:.4;"
                onmouseenter="if(!this.disabled)this.style.opacity='1';"
                onmouseleave="this.style.opacity=this.disabled?'.4':'1';">
                <span id="resetBtnLabel">Archive &amp; Delete</span>
            </button>
        </div>

    </div>
</div>

@push('scripts')
<script>
// ── Monthly Reset modal ───────────────────────────────────────────────────────
function openResetModal() {
    const modal = document.getElementById('resetModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    document.getElementById('resetConfirmInput').value = '';
    document.getElementById('resetConfirmBtn').disabled = true;
    const res = document.getElementById('resetResult');
    res.style.display = 'none';
    res.textContent = '';
    document.getElementById('resetBtnLabel').textContent = 'Archive & Delete';
    if (window.lucide) lucide.createIcons();
}

function closeResetModal() {
    document.getElementById('resetModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('resetModal').addEventListener('click', function(e) {
        if (e.target === this) closeResetModal();
    });
});

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
    label.innerHTML = '<span style="display:inline-flex;align-items:center;gap:.4rem;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin 0.8s linear infinite;"><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" opacity=".25"/><path d="M21 12a9 9 0 00-9-9" stroke-linecap="round"/></svg> Processing…</span>';
    res.style.display = 'none';

    try {
        const resp = await fetch('{{ route('admin.orders.reset-month') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ month: parseInt(month), year: parseInt(year) }),
        });

        const data = await resp.json();
        res.style.display = 'block';

        if (data.success) {
            res.style.background = 'rgba(34,197,94,.1)';
            res.style.border     = '1px solid rgba(34,197,94,.25)';
            res.style.color      = '#4ade80';
            res.innerHTML = `✓ ${data.message}<br><span style="font-size:.72rem;opacity:.75;font-weight:400;">Saved → ${data.archive_file}</span>`;
            label.textContent = 'Done';
            setTimeout(() => { closeResetModal(); }, 3000);
        } else {
            res.style.background = 'rgba(239,68,68,.1)';
            res.style.border     = '1px solid rgba(239,68,68,.25)';
            res.style.color      = '#f87171';
            res.textContent      = data.message ?? 'Something went wrong.';
            label.textContent    = 'Archive & Delete';
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

async function toggleService(type) {
    let btnId = 'toggleAllBtn';
    if (type === 'delivery') btnId = 'toggleDeliveryBtn';
    if (type === 'pickup')   btnId = 'togglePickupBtn';
    if (type === 'dine_in')  btnId = 'toggleDineInBtn';

    const btn = document.getElementById(btnId);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i data-lucide="loader-2" style="width:.85rem;height:.85rem;animation:spin 1s linear infinite;"></i> Updating…';
    }

    try {
        const res = await fetch('{{ route("admin.settings.toggle-open") }}', {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ type })
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert('Failed to update service status.');
            if (btn) btn.disabled = false;
        }
    } catch(e) {
        alert('Network error.');
        if (btn) btn.disabled = false;
    }
}
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush
