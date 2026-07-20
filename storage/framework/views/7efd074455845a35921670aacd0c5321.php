
<?php $__env->startSection('title', 'Riders'); ?>

<?php $__env->startSection('content'); ?>


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


<?php
// Calculate real stats from database
$totalRiders = $riders->count();
$onlineRiders = $riders->filter(fn($r) => $r->is_available && !$r->activeOrder())->count();
$onDeliveryRiders = $riders->filter(fn($r) => $r->activeOrder() && in_array($r->activeOrder()->status, ['rider_assigned', 'out_for_delivery']))->count();
$offlineRiders = $totalRiders - $onlineRiders - $onDeliveryRiders;
$deliveriesToday = \App\Models\Order::where('status', 'delivered')->whereDate('delivered_at', today())->count();

$riderStats = [
    ['label'=>'Total Riders',    'value'=>$totalRiders,      'sub'=>'Registered riders',   'icon'=>'users',        'color'=>'#f59e0b','bg'=>'rgba(245,158,11,.10)'],
    ['label'=>'Online Now',      'value'=>$onlineRiders,     'sub'=>'Available for orders','icon'=>'circle-check', 'color'=>'#10b981','bg'=>'rgba(16,185,129,.10)'],
    ['label'=>'On Delivery',     'value'=>$onDeliveryRiders, 'sub'=>'Currently delivering','icon'=>'bike',         'color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.10)'],
    ['label'=>'Offline',         'value'=>$offlineRiders,    'sub'=>'Not available',       'icon'=>'circle-x',     'color'=>'#6b7280','bg'=>'rgba(107,114,128,.10)'],
    ['label'=>'Deliveries Today','value'=>$deliveriesToday,  'sub'=>'Completed orders',    'icon'=>'package-check','color'=>'#3b82f6','bg'=>'rgba(59,130,246,.10)'],
];
?>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5 mb-6">
    <?php $__currentLoopData = $riderStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="stat-card" style="position:relative;overflow:hidden;border-color:<?php echo e($s['color']); ?>22;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.875rem;">
            <div style="width:3rem;height:3rem;border-radius:.875rem;background:<?php echo e($s['bg']); ?>;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="<?php echo e($s['icon']); ?>" style="width:1.4rem;height:1.4rem;color:<?php echo e($s['color']); ?>;stroke-width:2;"></i>
            </div>
            <span style="font-size:2.5rem;font-weight:900;color:<?php echo e($s['color']); ?>;line-height:1;"><?php echo e($s['value']); ?></span>
        </div>
        <h3 style="font-size:.9375rem;font-weight:700;color:var(--text-strong);margin:0 0 .25rem;"><?php echo e($s['label']); ?></h3>
        <p style="font-size:.72rem;color:var(--text-muted);margin:0;"><?php echo e($s['sub']); ?></p>
        <div style="position:absolute;bottom:-1.5rem;right:-1.5rem;width:5.5rem;height:5.5rem;border-radius:50%;background:<?php echo e($s['bg']); ?>;filter:blur(18px);pointer-events:none;"></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="section-card" style="margin-bottom:1.5rem;overflow:hidden;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:1rem 1.25rem;border-bottom:1px solid var(--border-section);">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i data-lucide="map-pin" style="width:1rem;height:1rem;color:#f59e0b;stroke-width:2;"></i>
            <h2 style="font-size:.875rem;font-weight:600;color:var(--text-strong);margin:0;">Live Rider Map</h2>
        </div>
        <span style="display:inline-flex;align-items:center;gap:.35rem;font-size:.7rem;font-weight:700;color:#10b981;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);padding:.25rem .6rem;border-radius:99px;">
            <span style="width:6px;height:6px;background:#10b981;border-radius:50%;animation:blink 1.2s infinite;"></span>
            <?php echo e($onlineRiders + $onDeliveryRiders); ?> riders active
        </span>
    </div>
    <div id="adminRidersMap" style="width:100%;height:320px;position:relative;z-index:0;"></div>
    <div style="padding:.75rem 1.25rem;display:flex;gap:1.25rem;flex-wrap:wrap;">
        <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:.4rem;">
            <span style="width:10px;height:10px;background:#8b5cf6;border-radius:50%;display:inline-block;"></span> On Delivery
        </span>
        <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:.4rem;">
            <span style="width:10px;height:10px;background:#10b981;border-radius:50%;display:inline-block;"></span> Online / Available
        </span>
        <span style="font-size:.72rem;color:var(--text-muted);display:flex;align-items:center;gap:.4rem;">
            <span style="width:10px;height:10px;background:#facc15;border-radius:50%;display:inline-block;"></span> EUT Restaurant
        </span>
    </div>
</div>


<div class="section-card">
    <div class="filter-bar" style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="search" style="width:.875rem;height:.875rem;color:var(--text-muted);stroke-width:2;"></i>
                <input type="search" class="admin-input" placeholder="Search riders..." style="max-width:220px;" oninput="filterRiders(this.value)" autocomplete="new-password" readonly onfocus="this.removeAttribute('readonly')">
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

<?php
$statusMap = [
    'online'      => ['label'=>'Online',      'color'=>'#10b981','bg'=>'rgba(16,185,129,.12)','icon'=>'circle-check'],
    'on_delivery' => ['label'=>'On Delivery', 'color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.12)','icon'=>'bike'],
    'offline'     => ['label'=>'Offline',     'color'=>'#6b7280','bg'=>'rgba(107,114,128,.12)','icon'=>'circle-x'],
];
?>
<?php $__currentLoopData = $riders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    // Determine status based on is_available and active order
    $activeOrder = $rider->activeOrder();
    if ($activeOrder && in_array($activeOrder->status, ['rider_assigned', 'out_for_delivery'])) {
        $currentStatus = 'on_delivery';
    } else if ($rider->is_available) {
        $currentStatus = 'online';
    } else {
        $currentStatus = 'offline';
    }
    $st = $statusMap[$currentStatus];
    // Get initials from name
    $nameParts = explode(' ', $rider->user->name);
    $initials = '';
    foreach ($nameParts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    $initials = substr($initials, 0, 2);
    // Get today's deliveries count
    $todayDeliveries = \App\Models\Order::where('rider_id', $rider->id)
        ->where('status', 'delivered')
        ->whereDate('delivered_at', today())
        ->count();
?>
<tr data-name="<?php echo e(strtolower($rider->user->name)); ?>" data-status="<?php echo e($currentStatus); ?>">
    <td>
        <div style="display:flex;align-items:center;gap:.6rem;">
            <div style="width:2.25rem;height:2.25rem;border-radius:50%;background:rgba(245,158,11,.18);display:flex;align-items:center;justify-content:center;color:#f59e0b;font-weight:800;font-size:.75rem;flex-shrink:0;border:2px solid rgba(245,158,11,.25);">
                <?php echo e($initials); ?>

            </div>
            <div>
                <p style="font-weight:600;color:var(--text-strong);font-size:.875rem;margin:0 0 .15rem;"><?php echo e($rider->user->name); ?></p>
                <p style="font-size:.7rem;color:var(--text-muted);margin:0;">Joined <?php echo e($rider->created_at->format('M d, Y')); ?></p>
            </div>
        </div>
    </td>
    <td style="font-size:.8rem;color:var(--text-body);"><?php echo e($rider->phone); ?></td>
    <td>
        <span style="display:inline-flex;align-items:center;gap:.3rem;font-size:.78rem;color:var(--text-subtle);">
            <i data-lucide="<?php echo e($rider->vehicle_type==='motorcycle' ? 'bike' : 'bicycle'); ?>" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            <?php echo e(ucfirst($rider->vehicle_type)); ?>

        </span>
    </td>
    <td>
        <span style="display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .7rem;border-radius:9999px;font-size:.7rem;font-weight:700;background:<?php echo e($st['bg']); ?>;color:<?php echo e($st['color']); ?>;">
            <i data-lucide="<?php echo e($st['icon']); ?>" style="width:.65rem;height:.65rem;stroke-width:2.5;"></i>
            <?php echo e($st['label']); ?>

        </span>
    </td>
    <td style="font-size:.8rem;">
        <?php if($activeOrder): ?>
            <a href="<?php echo e(route('admin.orders')); ?>" style="color:var(--accent);font-weight:600;font-family:monospace;">#<?php echo e($activeOrder->order_number); ?></a>
        <?php else: ?>
            <span style="color:var(--text-muted);">—</span>
        <?php endif; ?>
    </td>
    <td>
        <span style="font-size:.875rem;font-weight:700;color:var(--text-strong);"><?php echo e($todayDeliveries); ?></span>
        <span style="font-size:.72rem;color:var(--text-muted);margin-left:.25rem;">orders</span>
    </td>
    <td>
        <span style="font-size:.875rem;font-weight:700;color:#facc15;">⭐ <?php echo e(number_format($rider->rating,1)); ?></span>
    </td>
    <td>
        <div style="display:flex;gap:.4rem;align-items:center;">
            <button class="btn-icon-edit" onclick="openRiderDetail(<?php echo e($rider->id); ?>)" title="View Details">
                <i data-lucide="eye" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            </button>
            <button class="btn-icon-archive" onclick="openModal('editRiderModal')" title="Edit">
                <i data-lucide="pencil" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
            </button>
            <form method="POST" action="<?php echo e(route('admin.riders.destroy', $rider)); ?>" onsubmit="return confirm('Remove <?php echo e($rider->user->name); ?> from riders? This action cannot be undone.');" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn-icon-delete" title="Remove">
                    <i data-lucide="user-x" style="width:.8rem;height:.8rem;stroke-width:2;"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>


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
            
            <div>
                <p style="font-size:.78rem;font-weight:700;color:var(--text-subtle);text-transform:uppercase;letter-spacing:.05em;margin:0 0 .625rem;">Recent Deliveries</p>
                <?php
                $recentDeliveries = [
                    ['order'=>'#EUT-00512','customer'=>'Andrea M.','total'=>'₱520','time'=>'5 min ago','status'=>'on_delivery'],
                    ['order'=>'#EUT-00498','customer'=>'Bong S.',   'total'=>'₱350','time'=>'1 hr ago', 'status'=>'delivered'],
                    ['order'=>'#EUT-00481','customer'=>'Celia R.',  'total'=>'₱680','time'=>'3 hrs ago','status'=>'delivered'],
                    ['order'=>'#EUT-00465','customer'=>'Danny T.',  'total'=>'₱290','time'=> 'Yesterday','status'=>'delivered'],
                ];
                ?>
                <?php $__currentLoopData = $recentDeliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:.625rem .75rem;border-radius:.5rem;margin-bottom:.375rem;background:var(--bg-card);border:1px solid var(--border-card);">
                    <div style="display:flex;align-items:center;gap:.6rem;">
                        <i data-lucide="<?php echo e($d['status']==='delivered' ? 'circle-check' : 'bike'); ?>" style="width:.875rem;height:.875rem;color:<?php echo e($d['status']==='delivered' ? '#10b981' : '#8b5cf6'); ?>;stroke-width:2;"></i>
                        <div>
                            <p style="font-size:.8rem;font-weight:600;color:var(--text-strong);margin:0;font-family:monospace;"><?php echo e($d['order']); ?></p>
                            <p style="font-size:.7rem;color:var(--text-muted);margin:0;"><?php echo e($d['customer']); ?></p>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <p style="font-size:.8rem;font-weight:700;color:#facc15;margin:0;"><?php echo e($d['total']); ?></p>
                        <p style="font-size:.7rem;color:var(--text-muted);margin:0;"><?php echo e($d['time']); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php
$ridersJson = $riders->map(function($rider) {
    $activeOrder = $rider->activeOrder();
    if ($activeOrder && in_array($activeOrder->status, ['rider_assigned', 'out_for_delivery'])) {
        $currentStatus = 'on_delivery';
    } else if ($rider->is_available) {
        $currentStatus = 'online';
    } else {
        $currentStatus = 'offline';
    }
    // Get initials from name
    $nameParts = explode(' ', $rider->user->name);
    $initials = '';
    foreach ($nameParts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    $initials = substr($initials, 0, 2);
    // Get today's deliveries count
    $todayDeliveries = \App\Models\Order::where('rider_id', $rider->id)
        ->where('status', 'delivered')
        ->whereDate('delivered_at', today())
        ->count();
    return [
        'id' => $rider->id,
        'name' => $rider->user->name,
        'initials' => $initials,
        'phone' => $rider->phone,
        'vehicle' => ucfirst($rider->vehicle_type),
        'status' => $currentStatus,
        'order' => $activeOrder ? '#' . $activeOrder->order_number : null,
        'deliveries' => $todayDeliveries,
        'rating' => $rider->rating,
        'joined' => $rider->created_at->format('M d, Y'),
    ];
})->values();
$statusMapJson = json_encode($statusMap);
?>
const RIDERS = <?php echo $ridersJson; ?>;
const STATUS_MAP = <?php echo $statusMapJson; ?>;

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

<script>
// ── LEAFLET + OSRM ADMIN RIDERS MAP — with real-time polling ──
</script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const RESTAURANT_ADMIN = [13.3213129, 121.3027265];
<?php
$adminRidersJson = $riders->map(function($rider) {
    $activeOrder = $rider->activeOrder();
    if ($activeOrder && in_array($activeOrder->status, ['rider_assigned', 'out_for_delivery'])) {
        $currentStatus = 'on_delivery';
    } else if ($rider->is_available) {
        $currentStatus = 'online';
    } else {
        $currentStatus = 'offline';
    }
    $pos = [
        $rider->current_lat ?? 13.3213129,
        $rider->current_lng ?? 121.3027265
    ];
    $dest = null;
    if ($currentStatus === 'on_delivery' && $activeOrder && $activeOrder->delivery_lat && $activeOrder->delivery_lng) {
        $dest = [$activeOrder->delivery_lat, $activeOrder->delivery_lng];
    }
    $color = $currentStatus === 'on_delivery' ? '#8b5cf6' : ($currentStatus === 'online' ? '#10b981' : '#6b7280');
    return [
        'id'     => $rider->id,
        'name'   => $rider->user->name,
        'pos'    => $pos,
        'dest'   => $dest,
        'status' => $currentStatus,
        'order'  => $activeOrder ? '#' . $activeOrder->order_number : null,
        'color'  => $color,
    ];
})->values();
?>
const ADMIN_RIDERS_INIT = <?php echo $adminRidersJson; ?>;

// Map state — built once, updated by polling
let adminMapInst   = null;
const adminMarkers = {};   // riderId → { marker, routeLine }

async function fetchOSRMAdmin(from, to) {
    const url = `https://router.project-osrm.org/route/v1/driving/${from[1]},${from[0]};${to[1]},${to[0]}?overview=full&geometries=geojson`;
    try {
        const res  = await fetch(url);
        const data = await res.json();
        if (data.code === 'Ok' && data.routes.length)
            return data.routes[0].geometry.coordinates.map(c => [c[1], c[0]]);
    } catch(e) {}
    return null;
}

async function initAdminMap() {
    const el = document.getElementById('adminRidersMap');
    if (!el || adminMapInst) return;

    adminMapInst = L.map('adminRidersMap', { zoomControl: true });

    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { attribution: '&copy; Google Maps', maxZoom: 20 }).addTo(adminMapInst);
    L.tileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', { attribution: '', maxZoom: 20, opacity: 0.85 }).addTo(adminMapInst);

    // Restaurant marker (permanent)
    L.marker(RESTAURANT_ADMIN, { icon: L.divIcon({
        html: `<div style="background:#facc15;width:42px;height:42px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #d97706;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3);"><span style="transform:rotate(45deg);font-size:18px;line-height:1;">&#x1F354;</span></div>`,
        className: '', iconSize: [42, 42], iconAnchor: [21, 42],
    }) }).addTo(adminMapInst).bindPopup('<b>EUT Restaurant</b><br>Metro Naujan, Oriental Mindoro');

    const allPoints = [RESTAURANT_ADMIN];

    for (const r of ADMIN_RIDERS_INIT) {
        allPoints.push(r.pos);
        await addOrUpdateAdminRider(r);
    }

    adminMapInst.fitBounds(allPoints.length > 1 ? allPoints : [RESTAURANT_ADMIN, [RESTAURANT_ADMIN[0]+0.01, RESTAURANT_ADMIN[1]+0.01]], { padding: [40, 40] });
}

async function addOrUpdateAdminRider(r) {
    if (!adminMapInst) return;

    if (adminMarkers[r.id]) {
        // Rider already on map — just move the marker
        adminMarkers[r.id].marker.setLatLng(r.pos);
        adminMarkers[r.id].marker.setPopupContent(`<b>${r.name}</b><br>${r.order ? '&#x1F7E3; ' + r.order : '&#x1F7E2; Available'}`);

        // Update route line for on-delivery riders
        if (r.status === 'on_delivery' && r.dest) {
            const route = await fetchOSRMAdmin(r.pos, r.dest);
            if (route) {
                if (adminMarkers[r.id].routeLine) {
                    adminMarkers[r.id].routeLine.setLatLngs(route);
                } else {
                    adminMarkers[r.id].routeLine = L.polyline(route, { color: r.color, weight: 5, opacity: 1 }).addTo(adminMapInst);
                    // Customer pin (added once per rider, not re-added on updates)
                    if (!adminMarkers[r.id].destMarker) {
                        adminMarkers[r.id].destMarker = L.circleMarker(r.dest, {
                            radius: 8, color: '#ef4444', fillColor: '#ef4444', fillOpacity: 0.85, weight: 2,
                        }).addTo(adminMapInst).bindPopup(`<b>Customer</b><br>${r.order || ''}`);
                    }
                }
            } else if (adminMarkers[r.id].routeLine) {
                adminMarkers[r.id].routeLine.setLatLngs([r.pos, r.dest]);
            }
        } else {
            // No longer on delivery — remove route line
            if (adminMarkers[r.id].routeLine) {
                adminMarkers[r.id].routeLine.remove();
                adminMarkers[r.id].routeLine = null;
            }
            if (adminMarkers[r.id].destMarker) {
                adminMarkers[r.id].destMarker.remove();
                adminMarkers[r.id].destMarker = null;
            }
        }
    } else {
        // New rider — add to map
        const marker = L.marker(r.pos, { icon: L.divIcon({
            html: `<div style="background:${r.color};width:42px;height:42px;border-radius:50%;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 0 10px ${r.color}88;">&#x1F6F5;</div>`,
            className: '', iconSize: [42, 42], iconAnchor: [21, 21],
        }) }).addTo(adminMapInst);
        marker.bindPopup(`<b>${r.name}</b><br>${r.order ? '&#x1F7E3; ' + r.order : '&#x1F7E2; Available'}`);
        adminMarkers[r.id] = { marker, routeLine: null, destMarker: null };

        if (r.status === 'on_delivery' && r.dest) {
            const route = await fetchOSRMAdmin(r.pos, r.dest);
            if (route) {
                adminMarkers[r.id].routeLine = L.polyline(route, { color: r.color, weight: 5, opacity: 1 }).addTo(adminMapInst);
                adminMarkers[r.id].destMarker = L.circleMarker(r.dest, {
                    radius: 8, color: '#ef4444', fillColor: '#ef4444', fillOpacity: 0.85, weight: 2,
                }).addTo(adminMapInst).bindPopup(`<b>Customer</b><br>${r.order || ''}`);
            } else {
                adminMarkers[r.id].routeLine = L.polyline([r.pos, r.dest], { color: r.color, weight: 3, opacity: 0.6, dashArray: '6 4' }).addTo(adminMapInst);
            }
        }
    }
}

async function pollAdminMap() {
    try {
        const res  = await fetch('<?php echo e(route("admin.riders.locations")); ?>', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
        });
        if (!res.ok) return;
        const riders = await res.json();
        for (const r of riders) {
            // Build a compatible object for addOrUpdateAdminRider
            const rd = {
                id:     r.id,
                name:   r.name,
                pos:    [parseFloat(r.lat), parseFloat(r.lng)],
                dest:   r.dest_lat && r.dest_lng ? [parseFloat(r.dest_lat), parseFloat(r.dest_lng)] : null,
                status: r.status === 'On Delivery' ? 'on_delivery' : 'online',
                order:  r.order ? '#' + r.order : null,
                color:  r.status === 'On Delivery' ? '#8b5cf6' : '#10b981',
            };
            await addOrUpdateAdminRider(rd);
        }
    } catch(e) { /* silent */ }
}

document.addEventListener('DOMContentLoaded', async () => {
    await initAdminMap();
    // Poll rider positions every 8 seconds without rebuilding the map
    setInterval(pollAdminMap, 8000);
});
</script>

<style>
@keyframes blink { 0%,100%{opacity:1} 50%{opacity:0.3} }
#adminRidersMap { z-index: 0 !important; }
#adminRidersMap .leaflet-pane,
#adminRidersMap .leaflet-top,
#adminRidersMap .leaflet-bottom { z-index: 0 !important; }
#adminRidersMap .leaflet-control { z-index: 1 !important; }
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\patri\Desktop\EUT_WEB\resources\views/admin/riders.blade.php ENDPATH**/ ?>