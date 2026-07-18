@extends('admin.layout')
@section('title', 'Riders')

@section('content')

{{-- ── PAGE HEADER ── --}}
<div class="page-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(245,158,11,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="bike" style="width:1.2rem;height:1.2rem;color:#f59e0b;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;">Riders</h1>
            <p style="margin:0;">Manage delivery riders and monitor their activity.</p>
        </div>
    </div>
    <button onclick="openModal('addRiderModal')" class="btn-primary" style="display:inline-flex;align-items:center;gap:.4rem;">
        <i data-lucide="plus" style="width:.9rem;height:.9rem;stroke-width:2.5;"></i>
        Add Rider
    </button>
</div>

{{-- ── STAT CARDS ── --}}
@php
$riderStats = [
    ['label'=>'Total Riders',    'value'=>8,  'sub'=>'Registered riders',   'icon'=>'users',        'color'=>'#f59e0b','bg'=>'rgba(245,158,11,.10)'],
    ['label'=>'Online Now',      'value'=>5,  'sub'=>'Available for orders','icon'=>'circle-check', 'color'=>'#10b981','bg'=>'rgba(16,185,129,.10)'],
    ['label'=>'On Delivery',     'value'=>3,  'sub'=>'Currently delivering','icon'=>'bike',         'color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.10)'],
    ['label'=>'Offline',         'value'=>2,  'sub'=>'Not available',       'icon'=>'circle-x',     'color'=>'#6b7280','bg'=>'rgba(107,114,128,.10)'],
    ['label'=>'Deliveries Today','value'=>24, 'sub'=>'Completed orders',    'icon'=>'package-check','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.10)'],
];
@endphp
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 mb-6">
    @foreach($riderStats as $s)
    <div class="stat-card" style="position:relative;overflow:hidden;border-color:{{ $s['color'] }}22;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.875rem;">
            <div style="width:3rem;height:3rem;border-radius:.875rem;background:{{ $s['bg'] }};display:flex;align-items:center;justify-content:center;">
                <i data-lucide="{{ $s['icon'] }}" style="width:1.4rem;height:1.4rem;color:{{ $s['color'] }};stroke-width:2;"></i>
            </div>
            <span style="font-size:2.5rem;font-weight:900;color:{{ $s['color'] }};line-height:1;">{{ $s['value'] }}</span>
        </div>
        <h3 style="font-size:.9375rem;font-weight:700;color:var(--text-strong);margin:0 0 .25rem;">{{ $s['label'] }}</h3>
        <p style="font-size:.72rem;color:var(--text-muted);margin:0;">{{ $s['sub'] }}</p>
        <div style="position:absolute;bottom:-1.5rem;right:-1.5rem;width:5.5rem;height:5.5rem;border-radius:50%;background:{{ $s['bg'] }};filter:blur(18px);pointer-events:none;"></div>
    </div>
    @endforeach
</div>

{{-- ── FILTER BAR ── --}}
<div class="section-card">
    <div class="filter-bar" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="search" style="width:.875rem;height:.875rem;color:var(--text-muted);stroke-width:2;"></i>
                <input type="text" class="admin-input" placeholder="Search riders..." style="max-width:220px;" oninput="filterRiders(this.value)">
            </div>
            <select class="admin-input" style="max-width:160px;" onchange="filterByStatus(this.value)">
                <option value="">All Statuses</option>
                <option value="online">Online</option>
                <option value="on_delivery">On Delivery</option>
                <option value="offline">Offline</option>
            </select>
        </div>
        <span style="font-size:.72rem;color:var(--text-muted);display:inline-flex;align-items:center;gap:.35rem;">
            <i data-lucide="info" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            Sample data — connect riders table to go live
        </span>
    </div>

    {{-- ── RIDERS TABLE ── --}}
    <table class="admin-table" id="ridersTable">
        <thead>
            <tr>
                <th>Rider</th>
                <th>Phone</th>
                <th>Vehicle</th>
                <th>Status</th>
                <th>Current Order</th>
                <th>Today's Deliveries</th>
                <th>Rating</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="ridersTbody">

@php
$riders = [
    ['id'=>1,'name'=>'Juan dela Cruz',    'initials'=>'JD','phone'=>'09171234567','vehicle'=>'Motorcycle','status'=>'on_delivery','order'=>'#EUT-00512','deliveries'=>7,'rating'=>4.9,'joined'=>'Jan 12, 2026'],
    ['id'=>2,'name'=>'Maria Santos',      'initials'=>'MS','phone'=>'09281234567','vehicle'=>'Bicycle',   'status'=>'online',      'order'=>null,         'deliveries'=>4,'rating'=>4.7,'joined'=>'Feb 03, 2026'],
    ['id'=>3,'name'=>'Pedro Reyes',       'initials'=>'PR','phone'=>'09351234567','vehicle'=>'Motorcycle','status'=>'on_delivery','order'=>'#EUT-00509','deliveries'=>6,'rating'=>4.8,'joined'=>'Mar 15, 2026'],
    ['id'=>4,'name'=>'Ana Gomez',         'initials'=>'AG','phone'=>'09461234567','vehicle'=>'Bicycle',   'status'=>'online',      'order'=>null,         'deliveries'=>3,'rating'=>4.6,'joined'=>'Apr 01, 2026'],
    ['id'=>5,'name'=>'Carlo Villanueva',  'initials'=>'CV','phone'=>'09571234567','vehicle'=>'Motorcycle','status'=>'online',      'order'=>null,         'deliveries'=>5,'rating'=>4.9,'joined'=>'Apr 20, 2026'],
    ['id'=>6,'name'=>'Rosa Dela Torre',   'initials'=>'RD','phone'=>'09681234567','vehicle'=>'Motorcycle','status'=>'on_delivery','order'=>'#EUT-00514','deliveries'=>4,'rating'=>4.5,'joined'=>'May 08, 2026'],
    ['id'=>7,'name'=>'Dennis Ocampo',     'initials'=>'DO','phone'=>'09791234567','vehicle'=>'Bicycle',   'status'=>'offline',     'order'=>null,         'deliveries'=>0,'rating'=>4.3,'joined'=>'Jun 10, 2026'],
    ['id'=>8,'name'=>'Liza Mangubat',     'initials'=>'LM','phone'=>'09891234567','vehicle'=>'Motorcycle','status'=>'offline',     'order'=>null,         'deliveries'=>0,'rating'=>4.7,'joined'=>'Jul 01, 2026'],
];
$statusMap = [
    'online'      => ['label'=>'Online',      'color'=>'#10b981','bg'=>'rgba(16,185,129,.12)','icon'=>'circle-check'],
    'on_delivery' => ['label'=>'On Delivery', 'color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.12)','icon'=>'bike'],
    'offline'     => ['label'=>'Offline',     'color'=>'#6b7280','bg'=>'rgba(107,114,128,.12)','icon'=>'circle-x'],
];
@endphp
@foreach($riders as $r)
@php $st = $statusMap[$r['status']]; @endphp
<tr data-name="{{ strtolower($r['name']) }}" data-status="{{ $r['status'] }}">
    <td>
        <div style="display:flex;align-items:center;gap:.6rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:50%;background:rgba(245,158,11,.18);display:flex;align-items:center;justify-content:center;color:#f59e0b;font-weight:800;font-size:.75rem;flex-shrink:0;border:2px solid rgba(245,158,11,.25);">
                {{ $r['initials'] }}
            </div>
            <div>
                <p style="font-weight:600;color:var(--text-strong);font-size:.875rem;margin:0 0 .15rem;">{{ $r['name'] }}</p>
                <p style="font-size:.7rem;color:var(--text-muted);margin:0;">Joined {{ $r['joined'] }}</p>
            </div>
        </div>
    </td>
    <td style="font-size:.8rem;color:var(--text-body);">{{ $r['phone'] }}</td>
    <td>
        <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;color:var(--text-subtle);">
            <i data-lucide="{{ $r['vehicle']==='Motorcycle' ? 'bike' : 'bicycle' }}" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            {{ $r['vehicle'] }}
        </span>
    </td>
    <td>
        <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .7rem;border-radius:9999px;font-size:.7rem;font-weight:700;background:{{ $st['bg'] }};color:{{ $st['color'] }};">
            <i data-lucide="{{ $st['icon'] }}" style="width:.65rem;height:.65rem;stroke-width:2.5;"></i>
            {{ $st['label'] }}
        </span>
    </td>
    <td style="font-size:.8rem;">
        @if($r['order'])
            <a href="{{ route('admin.orders') }}" style="color:var(--accent);font-weight:600;font-family:monospace;">{{ $r['order'] }}</a>
        @else
            <span style="color:var(--text-muted);">—</span>
        @endif
    </td>
    <td>
        <span style="font-size:.875rem;font-weight:700;color:var(--text-strong);">{{ $r['deliveries'] }}</span>
        <span style="font-size:.72rem;color:var(--text-muted);margin-left:.25rem;">orders</span>
    </td>
    <td>
        <span style="font-size:.875rem;font-weight:700;color:#facc15;">⭐ {{ number_format($r['rating'],1) }}</span>
    </td>
    <td>
        <div style="display:flex;gap:.4rem;align-items:center;">
            <button class="btn-icon-edit" onclick="openRiderDetail({{ $r['id'] }})" title="View Details">
                <i data-lucide="eye" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            </button>
            <button class="btn-icon-archive" onclick="openModal('editRiderModal')" title="Edit">
                <i data-lucide="pencil" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            </button>
            <button class="btn-icon-delete" onclick="confirmRemoveRider('{{ $r['name'] }}')" title="Remove">
                <i data-lucide="user-x" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            </button>
        </div>
    </td>
</tr>
@endforeach
        </tbody>
    </table>
</div>

{{-- ── ADD RIDER MODAL ── --}}
<div id="addRiderModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'addRiderModal')">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="user-plus" style="width:1rem;height:1rem;color:var(--accent);stroke-width:2;"></i>
                Add New Rider
            </h3>
            <button class="modal-close" onclick="closeModal('addRiderModal')">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2;"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="admin-input" placeholder="e.g. Juan dela Cruz">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" class="admin-input" placeholder="e.g. 09171234567">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" class="admin-input" placeholder="rider@email.com">
                </div>
                <div class="form-group">
                    <label>Vehicle Type</label>
                    <select class="admin-input">
                        <option value="motorcycle">Motorcycle</option>
                        <option value="bicycle">Bicycle</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Plate / ID Number</label>
                    <input type="text" class="admin-input" placeholder="e.g. ABC-1234 or RIDER-001">
                </div>
                <div class="form-group">
                    <label>Initial Status</label>
                    <select class="admin-input">
                        <option value="offline">Offline</option>
                        <option value="online">Online</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Temporary Password</label>
                <input type="password" class="admin-input" placeholder="Will be changed on first login">
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-ghost" onclick="closeModal('addRiderModal')">Cancel</button>
            <button class="btn-success" onclick="alert('Connect to DB to activate.')">
                <i data-lucide="user-plus" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i>
                Add Rider
            </button>
        </div>
    </div>
</div>

{{-- ── RIDER DETAIL MODAL ── --}}
<div id="riderDetailModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'riderDetailModal')">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <h3 class="modal-title" style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="bike" style="width:1rem;height:1rem;color:#f59e0b;stroke-width:2;"></i>
                <span id="detailRiderName">Rider Details</span>
            </h3>
            <button class="modal-close" onclick="closeModal('riderDetailModal')">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2;"></i>
            </button>
        </div>
        <div class="modal-body">
            {{-- Profile row --}}
            <div style="display:flex;align-items:center;gap:1rem;padding:.75rem 1rem;background:var(--accent-soft);border-radius:.75rem;border:1px solid var(--accent-border);">
                <div style="width:3.5rem;height:3.5rem;border-radius:50%;background:rgba(245,158,11,.2);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.1rem;color:#f59e0b;border:2px solid rgba(245,158,11,.3);">
                    <span id="detailInitials">JD</span>
                </div>
                <div style="flex:1;">
                    <p style="font-weight:700;color:var(--text-strong);font-size:1rem;margin:0 0 .2rem;" id="detailNameFull">Juan dela Cruz</p>
                    <p style="font-size:.78rem;color:var(--text-muted);margin:0;" id="detailPhone">09171234567 · Motorcycle</p>
                </div>
                <span id="detailStatusBadge" style="display:inline-flex;align-items:center;gap:.35rem;padding:.3rem .8rem;border-radius:9999px;font-size:.75rem;font-weight:700;"></span>
            </div>
            {{-- Stats row --}}
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;">
                <div style="background:var(--bg-card);border:1px solid var(--border-card);border-radius:.75rem;padding:1rem;text-align:center;">
                    <p style="font-size:1.75rem;font-weight:900;color:#facc15;margin:0 0 .2rem;" id="detailDeliveries">7</p>
                    <p style="font-size:.72rem;color:var(--text-muted);margin:0;">Today's Deliveries</p>
                </div>
                <div style="background:var(--bg-card);border:1px solid var(--border-card);border-radius:.75rem;padding:1rem;text-align:center;">
                    <p style="font-size:1.75rem;font-weight:900;color:#facc15;margin:0 0 .2rem;" id="detailRating">4.9</p>
                    <p style="font-size:.72rem;color:var(--text-muted);margin:0;">Avg. Rating</p>
                </div>
                <div style="background:var(--bg-card);border:1px solid var(--border-card);border-radius:.75rem;padding:1rem;text-align:center;">
                    <p style="font-size:1.75rem;font-weight:900;color:#10b981;margin:0 0 .2rem;">98%</p>
                    <p style="font-size:.72rem;color:var(--text-muted);margin:0;">On-Time Rate</p>
                </div>
            </div>
            {{-- Recent deliveries --}}
            <div>
                <p style="font-size:.78rem;font-weight:700;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.05em;margin:0 0 .625rem;">Recent Deliveries</p>
                @php
                $recentDeliveries = [
                    ['order'=>'#EUT-00512','customer'=>'Andrea M.','total'=>'₱520','time'=>'5 min ago','status'=>'on_delivery'],
                    ['order'=>'#EUT-00498','customer'=>'Bong S.',   'total'=>'₱350','time'=>'1 hr ago', 'status'=>'delivered'],
                    ['order'=>'#EUT-00481','customer'=>'Celia R.',  'total'=>'₱680','time'=>'3 hrs ago','status'=>'delivered'],
                    ['order'=>'#EUT-00465','customer'=>'Danny T.',  'total'=>'₱290','time'=> 'Yesterday','status'=>'delivered'],
                ];
                @endphp
                @foreach($recentDeliveries as $d)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.625rem .75rem;border-radius:.5rem;margin-bottom:.375rem;background:var(--bg-card);border:1px solid var(--border-card);">
                    <div style="display:flex;align-items:center;gap:.6rem;">
                        <i data-lucide="{{ $d['status']==='delivered' ? 'circle-check' : 'bike' }}" style="width:.875rem;height:.875rem;color:{{ $d['status']==='delivered' ? '#10b981' : '#8b5cf6' }};stroke-width:2;"></i>
                        <div>
                            <p style="font-size:.8rem;font-weight:600;color:var(--text-strong);margin:0;font-family:monospace;">{{ $d['order'] }}</p>
                            <p style="font-size:.7rem;color:var(--text-muted);margin:0;">{{ $d['customer'] }}</p>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <p style="font-size:.8rem;font-weight:700;color:#facc15;margin:0;">{{ $d['total'] }}</p>
                        <p style="font-size:.7rem;color:var(--text-muted);margin:0;">{{ $d['time'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="modal-footer" style="justify-content:space-between;">
            <button class="btn-danger" onclick="closeModal('riderDetailModal')">
                <i data-lucide="user-x" style="width:.85rem;height:.85rem;stroke-width:2;"></i>
                Suspend Rider
            </button>
            <div style="display:flex;gap:.5rem;">
                <button class="btn-ghost" onclick="closeModal('riderDetailModal')">Close</button>
                <button class="btn-primary" onclick="alert('Assign to order — connect to DB.')">
                    <i data-lucide="send" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i>
                    Assign to Order
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── EDIT RIDER MODAL ── --}}
<div id="editRiderModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'editRiderModal')">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="pencil" style="width:1rem;height:1rem;color:#d97706;stroke-width:2;"></i>
                Edit Rider
            </h3>
            <button class="modal-close" onclick="closeModal('editRiderModal')">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2;"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" class="admin-input" value="Juan dela Cruz">
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" class="admin-input" value="09171234567">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Vehicle Type</label>
                    <select class="admin-input">
                        <option selected>Motorcycle</option>
                        <option>Bicycle</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select class="admin-input">
                        <option value="online">Online</option>
                        <option value="on_delivery" selected>On Delivery</option>
                        <option value="offline">Offline</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-ghost" onclick="closeModal('editRiderModal')">Cancel</button>
            <button class="btn-warning" onclick="alert('Connect to DB to activate.')">
                <i data-lucide="save" style="width:.85rem;height:.85rem;stroke-width:2.5;"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<script>
@php
$ridersJson = json_encode(array_map(fn($r) => [
    'id'=>$r['id'],'name'=>$r['name'],'initials'=>$r['initials'],
    'phone'=>$r['phone'],'vehicle'=>$r['vehicle'],'status'=>$r['status'],
    'order'=>$r['order'],'deliveries'=>$r['deliveries'],'rating'=>$r['rating'],
], $riders));
$statusMapJson = json_encode($statusMap);
@endphp
const RIDERS = {!! $ridersJson !!};
const STATUS_MAP = {!! $statusMapJson !!};

function openRiderDetail(id) {
    const r = RIDERS.find(x => x.id === id);
    if (!r) return;
    const st = STATUS_MAP[r.status];
    document.getElementById('detailRiderName').textContent = r.name;
    document.getElementById('detailInitials').textContent  = r.initials;
    document.getElementById('detailNameFull').textContent  = r.name;
    document.getElementById('detailPhone').textContent     = r.phone + ' · ' + r.vehicle;
    document.getElementById('detailDeliveries').textContent = r.deliveries;
    document.getElementById('detailRating').textContent    = r.rating.toFixed(1);
    const badge = document.getElementById('detailStatusBadge');
    badge.textContent = st.label;
    badge.style.background = st.bg;
    badge.style.color = st.color;
    openModal('riderDetailModal');
}

function filterRiders(q) {
    document.querySelectorAll('#ridersTbody tr').forEach(row => {
        row.style.display = row.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
    });
}

function filterByStatus(val) {
    document.querySelectorAll('#ridersTbody tr').forEach(row => {
        row.style.display = (!val || row.dataset.status === val) ? '' : 'none';
    });
}

function confirmRemoveRider(name) {
    if (confirm('Remove ' + name + ' from riders? This action cannot be undone.')) {
        alert('Connect to DB to activate removal.');
    }
}
</script>

@endsection
