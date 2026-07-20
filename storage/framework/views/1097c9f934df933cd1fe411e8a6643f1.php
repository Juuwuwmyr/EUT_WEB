
<?php $__env->startSection('title', 'Orders'); ?>


<?php $__env->startPush('head'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="page-header" style="display:flex;flex-wrap:wrap;align-items:flex-start;justify-content:space-between;gap:1rem;">
    <div style="display:flex;align-items:center;gap:.75rem;">
        <div style="width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(16,185,129,.12);display:flex;align-items:center;justify-content:center;">
            <i data-lucide="shopping-bag" style="width:1.2rem;height:1.2rem;color:#10b981;stroke-width:2;"></i>
        </div>
        <div>
            <h1 style="margin:0 0 .15rem;">Orders</h1>
            <p style="margin:0;">Track and manage customer orders in real time.</p>
        </div>
    </div>
    <button onclick="location.reload()" class="btn-ghost" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.75rem;">
        <i data-lucide="refresh-cw" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Refresh
    </button>
</div>


<?php
$statusConfig = [
    'pending'          => ['label'=>'Pending',         'sub'=>'Awaiting confirmation',  'icon'=>'clock',         'color'=>'#f59e0b','bg'=>'rgba(245,158,11,.10)'],
    'preparing'        => ['label'=>'Preparing',       'sub'=>'Being cooked',           'icon'=>'chef-hat',      'color'=>'#3b82f6','bg'=>'rgba(59,130,246,.10)'],
    'out'              => ['label'=>'On the Way',      'sub'=>'Out for delivery',       'icon'=>'bike',          'color'=>'#8b5cf6','bg'=>'rgba(139,92,246,.10)'],
    'delivered'        => ['label'=>'Delivered',       'sub'=>'Completed orders',       'icon'=>'circle-check',  'color'=>'#10b981','bg'=>'rgba(16,185,129,.10)'],
    'cancelled'        => ['label'=>'Cancelled',       'sub'=>'Cancelled orders',       'icon'=>'circle-x',      'color'=>'#ef4444','bg'=>'rgba(239,68,68,.10)'],
];
?>

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem;">
    <?php $__currentLoopData = $statusCounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php $sc = $statusConfig[$status]; ?>
    <a href="<?php echo e(route('admin.orders',['status'=>$status])); ?>"
       class="stat-card"
       style="text-decoration:none;position:relative;overflow:hidden;cursor:pointer;
              border-color:<?php echo e($sc['color']); ?>22;
              <?php echo e(request('status')===$status ? 'border-color:'.$sc['color'].'66;box-shadow:0 0 0 3px '.$sc['color'].'18;' : ''); ?>">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:.75rem;">
            <div style="width:2.75rem;height:2.75rem;border-radius:.75rem;background:<?php echo e($sc['bg']); ?>;display:flex;align-items:center;justify-content:center;">
                <i data-lucide="<?php echo e($sc['icon']); ?>" style="width:1.3rem;height:1.3rem;color:<?php echo e($sc['color']); ?>;stroke-width:2;"></i>
            </div>
            <span style="font-size:2.25rem;font-weight:900;color:<?php echo e($sc['color']); ?>;line-height:1;"><?php echo e($count); ?></span>
        </div>
        <h3 style="font-size:.875rem;font-weight:700;color:var(--text-strong);margin:0 0 .15rem;"><?php echo e($sc['label']); ?></h3>
        <p style="font-size:.7rem;color:var(--text-muted);margin:0;"><?php echo e($sc['sub']); ?></p>
        <div style="position:absolute;bottom:-1.5rem;right:-1.5rem;width:5rem;height:5rem;border-radius:50%;background:<?php echo e($sc['bg']); ?>;filter:blur(18px);pointer-events:none;"></div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>


<div class="section-card">
    <div class="filter-bar" style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:.5rem;">
            <i data-lucide="filter" style="width:.875rem;height:.875rem;color:var(--text-muted);stroke-width:2;"></i>
            <select onchange="location='<?php echo e(route('admin.orders')); ?>?status='+this.value" class="admin-input" style="max-width:180px;">
                <option value="" <?php echo e(!request('status') ? 'selected':''); ?>>All Statuses</option>
                <option value="pending"          <?php echo e(request('status')==='pending'          ? 'selected':''); ?>>Pending</option>
                <option value="accepted"         <?php echo e(request('status')==='accepted'         ? 'selected':''); ?>>Accepted</option>
                <option value="preparing"        <?php echo e(request('status')==='preparing'        ? 'selected':''); ?>>Preparing</option>
                <option value="rider_assigned"   <?php echo e(request('status')==='rider_assigned'   ? 'selected':''); ?>>Rider Assigned</option>
                <option value="out_for_delivery" <?php echo e(request('status')==='out_for_delivery' ? 'selected':''); ?>>Out for Delivery</option>
                <option value="delivered"        <?php echo e(request('status')==='delivered'        ? 'selected':''); ?>>Delivered</option>
                <option value="cancelled"        <?php echo e(request('status')==='cancelled'        ? 'selected':''); ?>>Cancelled</option>
            </select>
        </div>
        <?php if(request('status')): ?>
            <a href="<?php echo e(route('admin.orders')); ?>" class="btn-ghost" style="display:inline-flex;align-items:center;gap:.3rem;font-size:.75rem;">
                <i data-lucide="x" style="width:.75rem;height:.75rem;stroke-width:2.5;"></i> Show All
            </a>
        <?php endif; ?>
        <span style="margin-left:auto;font-size:.72rem;color:var(--text-muted);"><?php echo e($orders->count()); ?> order(s)</span>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>Order #</th><th>Customer</th><th>Items</th>
                <th>Total</th><th>Status</th><th>Placed</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $s = $order->status;
                $statusColorMap = [
                    'pending'          => ['bg'=>'rgba(245,158,11,.12)', 'color'=>'#d97706',  'label'=>'Pending'],
                    'accepted'         => ['bg'=>'rgba(59,130,246,.12)', 'color'=>'#2563eb',  'label'=>'Accepted'],
                    'preparing'        => ['bg'=>'rgba(59,130,246,.12)', 'color'=>'#2563eb',  'label'=>'Preparing'],
                    'rider_assigned'   => ['bg'=>'rgba(139,92,246,.12)','color'=>'#7c3aed',  'label'=>'Rider Assigned'],
                    'out_for_delivery' => ['bg'=>'rgba(139,92,246,.12)','color'=>'#7c3aed',  'label'=>'On the Way'],
                    'delivered'        => ['bg'=>'rgba(16,185,129,.12)', 'color'=>'#16a34a',  'label'=>'Delivered'],
                    'cancelled'        => ['bg'=>'rgba(239,68,68,.12)',  'color'=>'#dc2626',  'label'=>'Cancelled'],
                ];
                $sc = $statusColorMap[$s] ?? $statusColorMap['pending'];
                $customerName = $order->user?->name ?? 'Guest';
            ?>
            <tr>
                <td>
                    <span style="font-family:monospace;font-weight:700;color:var(--accent);font-size:.875rem;"><?php echo e($order->order_number); ?></span>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <div style="width:1.875rem;height:1.875rem;border-radius:50%;background:var(--accent);display:flex;align-items:center;justify-content:center;color:#000;font-weight:700;font-size:.7rem;flex-shrink:0;">
                            <?php echo e(strtoupper(substr($customerName,0,1))); ?>

                        </div>
                        <div>
                            <p style="font-weight:600;color:var(--text-strong);font-size:.8rem;margin:0;"><?php echo e($customerName); ?></p>
                            <p style="font-size:.68rem;color:var(--text-muted);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px;"><?php echo e($order->delivery_address); ?></p>
                        </div>
                    </div>
                </td>
                <td style="max-width:180px;">
                    <?php $__currentLoopData = $order->items->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="display:flex;align-items:center;gap:4px;margin-bottom:2px;">
                            <span style="font-size:.72rem;font-weight:700;color:var(--accent);background:rgba(250,204,21,.1);border-radius:4px;padding:1px 5px;flex-shrink:0;">x<?php echo e($item->quantity); ?></span>
                            <span style="font-size:.75rem;color:var(--text-strong);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;" title="<?php echo e($item->item_name); ?>"><?php echo e($item->item_name); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($order->items->count() > 2): ?>
                        <span style="font-size:.68rem;color:var(--text-muted);">+<?php echo e($order->items->count() - 2); ?> more</span>
                    <?php endif; ?>
                </td>
                <td style="font-weight:700;color:var(--accent);">&#x20B1;<?php echo e(number_format($order->total)); ?></td>
                <td>
                    <span style="display:inline-flex;align-items:center;gap:.3rem;padding:.2rem .65rem;border-radius:9999px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;background:<?php echo e($sc['bg']); ?>;color:<?php echo e($sc['color']); ?>;">
                        <?php echo e($sc['label']); ?>

                    </span>
                </td>
                <td style="color:var(--text-muted);font-size:.72rem;white-space:nowrap;"><?php echo e($order->created_at->format('M d g:i A')); ?></td>
                <td>
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                        <button class="btn-ghost" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .65rem;"
                                onclick="openManageModal(<?php echo e($order->id); ?>)">
                            <i data-lucide="settings-2" style="width:.75rem;height:.75rem;stroke-width:2;"></i> Manage
                        </button>

                        
                        <?php if($s === 'pending'): ?>
                            <form method="POST" action="<?php echo e(route('admin.orders.accept', $order)); ?>" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-success" style="font-size:.72rem;display:inline-flex;align-items:center;gap:.3rem;padding:.35rem .65rem;">
                                    <i data-lucide="check" style="width:.75rem;height:.75rem;stroke-width:2.5;"></i> Accept
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:3rem;">No orders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<div id="manageModal" class="modal-backdrop" onclick="closeModalBackdrop(event,'manageModal')">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:.5rem;">
                <i data-lucide="settings-2" style="width:1.1rem;height:1.1rem;color:#10b981;stroke-width:2;"></i>
                <h3 class="modal-title" id="mmTitle">Manage Order</h3>
            </div>
            <button onclick="closeModal('manageModal')" class="modal-close">
                <i data-lucide="x" style="width:1rem;height:1rem;stroke-width:2.5;"></i>
            </button>
        </div>
        <div id="mmBody" class="modal-body" style="gap:.875rem;">
            <div style="text-align:center;padding:2rem;color:var(--text-muted);">Loadingâ€¦</div>
        </div>
    </div>
</div>


<?php
$ordersMap = [];
foreach($orders as $o) {
    $ordersMap[$o->id] = [
        'id'           => $o->id,
        'order_number' => $o->order_number,
        'status'       => $o->status,
        'status_label' => $o->status_label,
        'customer'     => $o->user?->name ?? 'Guest',
        'email'        => $o->user?->email ?? '',
        'address'      => $o->delivery_address,
        'delivery_lat' => $o->delivery_lat,
        'delivery_lng' => $o->delivery_lng,
        'payment'      => $o->payment_method,
        'subtotal'     => $o->subtotal,
        'delivery_fee' => $o->delivery_fee,
        'total'        => $o->total,
        'notes'        => $o->notes,
        'date'         => $o->created_at->format('M d, Y g:i A'),
        'accepted_at'  => $o->accepted_at?->format('g:i A'),
        'picked_up_at' => $o->picked_up_at?->format('g:i A'),
        'delivered_at' => $o->delivered_at?->format('g:i A'),
        'rider'        => ($o->rider && $o->rider->user) ? $o->rider->user->name : null,
        'rider_lat'    => $o->rider?->current_lat,
        'rider_lng'    => $o->rider?->current_lng,
        'items'        => $o->items->map(fn($i) => [
            'name'      => $i->item_name,
            'qty'       => $i->quantity,
            'price'     => $i->unit_price,
            'subtotal'  => $i->subtotal,
            'modifiers' => $i->modifiers ?? [],
        ])->toArray(),
    ];
}
$ridersMap = $availableRiders->map(fn($r) => ['id'=>$r->id,'name'=>$r->user->name,'phone'=>$r->phone])->values();
?>

<script>
var ORDERS_MAP   = <?php echo json_encode($ordersMap, 15, 512) ?>;
var RIDERS       = <?php echo json_encode($ridersMap, 15, 512) ?>;
var CSRF_TOKEN   = '<?php echo e(csrf_token()); ?>';

// -- Status pipeline
var STATUS_PIPELINE = {
    pending:          { label:'Pending',         color:'#f59e0b', next:'accepted',         nextLabel:'&#x2705; Accept Order',              btnClass:'btn-success'  },
    accepted:         { label:'Accepted',        color:'#3b82f6', next:'preparing',        nextLabel:'&#x1F373; Start Preparing',          btnClass:'btn-primary'  },
    preparing:        { label:'Preparing',       color:'#3b82f6', next:'out_for_delivery', nextLabel:'&#x1F6F5; Mark Out for Delivery',    btnClass:'btn-warning'  },
    rider_assigned:   { label:'Rider Assigned',  color:'#8b5cf6', next:'out_for_delivery', nextLabel:'&#x1F6F5; Mark Out for Delivery',    btnClass:'btn-warning'  },
    out_for_delivery: { label:'On the Way',      color:'#8b5cf6', next:null,               nextLabel:null,                                 btnClass:''             },
    delivered:        { label:'Delivered',       color:'#10b981', next:null,               nextLabel:null,                                 btnClass:''             },
    cancelled:        { label:'Cancelled',       color:'#ef4444', next:null,               nextLabel:null,                                 btnClass:''             },
};

var STATUS_TIMELINE = [
    { key:'pending',          label:'Order Placed',     icon:'&#x1F4CB;' },
    { key:'accepted',         label:'Order Accepted',   icon:'&#x2705;'  },
    { key:'preparing',        label:'Being Prepared',   icon:'&#x1F373;' },
    { key:'out_for_delivery', label:'Out for Delivery', icon:'&#x1F6F5;' },
    { key:'delivered',        label:'Delivered',        icon:'&#x1F4E6;' },
];

function openManageModal(id) {
    var o = ORDERS_MAP[id];
    if (!o) return;
    var sp = STATUS_PIPELINE[o.status] || {};

    document.getElementById('mmTitle').textContent = 'Manage Order ' + o.order_number;

    // -- Timeline
    var statusOrder = ['pending','accepted','preparing','out_for_delivery','delivered'];
    var curIdx = statusOrder.indexOf(o.status);
    if (o.status === 'rider_assigned') curIdx = 2;

    var tlHtml = '<div style="display:flex;align-items:center;gap:0;margin-bottom:.25rem;overflow-x:auto;padding-bottom:.25rem;">';
    STATUS_TIMELINE.forEach(function(step, i) {
        var done     = i < (o.status === 'rider_assigned' ? 3 : curIdx);
        var current  = (i === curIdx) || (o.status === 'rider_assigned' && i === 2);
        var cancelled = o.status === 'cancelled';
        var dotColor  = done || current ? (cancelled ? '#ef4444' : step.key === 'delivered' ? '#10b981' : '#3b82f6') : 'var(--border-card)';
        var textColor = done || current ? 'var(--text-strong)' : 'var(--text-muted)';
        var fw        = current ? '700' : '500';
        tlHtml +=
            '<div style="display:flex;flex-direction:column;align-items:center;flex:1;min-width:60px;">' +
                '<div style="width:2rem;height:2rem;border-radius:50%;background:' + dotColor + ';display:flex;align-items:center;justify-content:center;font-size:.875rem;' +
                (current ? 'box-shadow:0 0 0 4px ' + dotColor + '33;' : '') + '">' +
                    step.icon +
                '</div>' +
                '<span style="font-size:.6rem;font-weight:' + fw + ';color:' + textColor + ';text-align:center;margin-top:.3rem;line-height:1.2;">' + step.label + '</span>' +
            '</div>';
        if (i < STATUS_TIMELINE.length - 1) {
            tlHtml += '<div style="height:2px;flex:1;background:' + (done ? '#3b82f6' : 'var(--border-card)') + ';margin-top:1rem;min-width:16px;"></div>';
        }
    });
    tlHtml += '</div>';

    if (o.status === 'cancelled') {
        tlHtml += '<div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:.5rem;padding:.5rem .875rem;font-size:.75rem;color:#ef4444;font-weight:600;margin-top:.25rem;">&#x274C; This order was cancelled.</div>';
    }

    // -- Items summary
    var itemsHtml = '';
    o.items.forEach(function(item) {
        var modTags = '';
        if (item.modifiers && item.modifiers.length) {
            item.modifiers.forEach(function(m) {
                if (!m || !m.name || /^no\s/i.test(m.name)) return;
                var colors = { flavor:'#3b82f6', modifier:'#8b5cf6', addon:'#d97706' };
                var c = colors[m.type] || '#8b5cf6';
                modTags += '<span style="padding:.1rem .45rem;border-radius:99px;font-size:.6rem;background:' + c + '18;color:' + c + ';font-weight:600;">' + m.name + '</span>';
            });
        }
        itemsHtml +=
            '<div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.45rem 0;border-bottom:1px solid var(--border-divider);">' +
                '<div>' +
                    '<span style="font-weight:600;color:var(--text-strong);font-size:.8rem;">x' + item.qty + ' ' + item.name + '</span>' +
                    (modTags ? '<div style="display:flex;flex-wrap:wrap;gap:.25rem;margin-top:.25rem;">' + modTags + '</div>' : '') +
                '</div>' +
                '<span style="font-size:.8rem;color:var(--text-body);font-weight:600;flex-shrink:0;margin-left:.5rem;">&#x20B1;' + Number(item.subtotal).toLocaleString() + '</span>' +
            '</div>';
    });

    // -- Action buttons
    var actionsHtml = '';
    if (sp.next && o.status !== 'delivered' && o.status !== 'cancelled') {
        var actionRoute = o.status === 'pending'
            ? '<?php echo e(route("admin.orders.accept", ":id")); ?>'.replace(':id', o.id)
            : '<?php echo e(route("admin.orders.status", ":id")); ?>'.replace(':id', o.id);
        actionsHtml +=
            '<form method="POST" action="' + actionRoute + '" style="display:inline;" id="nextStepForm_' + o.id + '">' +
                '<input type="hidden" name="_token" value="' + CSRF_TOKEN + '">' +
                (o.status !== 'pending' ? '<input type="hidden" name="_method" value="PATCH"><input type="hidden" name="status" value="' + sp.next + '">' : '') +
                '<button type="submit" class="' + sp.btnClass + '" style="font-size:.875rem;width:100%;justify-content:center;gap:.4rem;display:inline-flex;align-items:center;">' +
                    sp.nextLabel +
                '</button>' +
            '</form>';
    }

    if (o.status === 'preparing' && RIDERS.length > 0) {
        var riderOptions = '<option value="">-- Select a rider --</option>';
        RIDERS.forEach(function(r) { riderOptions += '<option value="' + r.id + '">' + r.name + (r.phone ? ' · ' + r.phone : '') + '</option>'; });
        actionsHtml +=
            '<form method="POST" action="<?php echo e(route("admin.orders.assign-rider", ":id")); ?>'.replace(':id', o.id) + '" style="display:flex;gap:.5rem;margin-top:.5rem;" id="assignForm_' + o.id + '">' +
                '<input type="hidden" name="_token" value="' + CSRF_TOKEN + '">' +
                '<select name="rider_id" class="admin-input" style="flex:1;">' + riderOptions + '</select>' +
                '<button type="submit" class="btn-primary" style="white-space:nowrap;font-size:.8rem;display:inline-flex;align-items:center;gap:.3rem;">' +
                    '<i data-lucide="bike" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Assign Rider' +
                '</button>' +
            '</form>';
    }

    if (['pending','accepted','preparing'].includes(o.status)) {
        actionsHtml +=
            '<form method="POST" action="<?php echo e(route("admin.orders.status", ":id")); ?>'.replace(':id', o.id) + '" style="margin-top:.25rem;" onsubmit="return confirm(\'Cancel this order?\')">' +
                '<input type="hidden" name="_token" value="' + CSRF_TOKEN + '">' +
                '<input type="hidden" name="_method" value="PATCH">' +
                '<input type="hidden" name="status" value="cancelled">' +
                '<button type="submit" class="btn-danger" style="font-size:.8rem;display:inline-flex;align-items:center;gap:.3rem;width:100%;justify-content:center;">' +
                    '<i data-lucide="x-circle" style="width:.8rem;height:.8rem;stroke-width:2;"></i> Cancel Order' +
                '</button>' +
            '</form>';
    }

    // -- Assemble modal body
    var html =
        '<div>' +
            '<p style="font-size:.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .75rem;font-weight:600;">Order Progress</p>' +
            tlHtml +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:.625rem;">' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.75rem;">' +
                '<p style="font-size:.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .25rem;">Customer</p>' +
                '<p style="font-weight:600;color:var(--text-strong);font-size:.8rem;margin:0;">' + o.customer + '</p>' +
                '<p style="color:var(--text-muted);font-size:.7rem;margin:.1rem 0 0;">' + o.email + '</p>' +
            '</div>' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.75rem;">' +
                '<p style="font-size:.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .25rem;">Payment &middot; Total</p>' +
                '<p style="font-weight:700;color:var(--accent);font-size:1rem;margin:0;">&#x20B1;' + Number(o.total).toLocaleString() + '</p>' +
                '<p style="color:var(--text-muted);font-size:.7rem;margin:.1rem 0 0;text-transform:capitalize;">' + o.payment + '</p>' +
            '</div>' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.75rem;grid-column:span 2;">' +
                '<p style="font-size:.65rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .25rem;">Delivery Address</p>' +
                '<p style="font-size:.8rem;color:var(--text-body);margin:0;">' + o.address + '</p>' +
            '</div>' +
        '</div>' +
        '<div>' +
            '<p style="font-size:.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .5rem;font-weight:600;">Items Ordered</p>' +
            '<div style="background:var(--bg-filter);border-radius:.625rem;padding:.625rem 1rem;">' +
                itemsHtml +
                '<div style="display:flex;justify-content:space-between;padding:.4rem 0;font-size:.8rem;">' +
                    '<span style="color:var(--text-muted);">Delivery Fee</span><span style="color:var(--text-body);">&#x20B1;' + Number(o.delivery_fee).toLocaleString() + '</span>' +
                '</div>' +
                '<div style="display:flex;justify-content:space-between;padding:.5rem 0;border-top:1px solid var(--border-divider);margin-top:.25rem;">' +
                    '<span style="font-weight:700;color:var(--text-strong);">Total</span>' +
                    '<span style="font-weight:800;font-size:1rem;color:var(--accent);">&#x20B1;' + Number(o.total).toLocaleString() + '</span>' +
                '</div>' +
            '</div>' +
        '</div>' +
        (o.notes ? '<div style="background:rgba(245,158,11,.06);border:1px solid rgba(245,158,11,.2);border-radius:.625rem;padding:.75rem 1rem;"><p style="font-size:.68rem;color:#d97706;text-transform:uppercase;letter-spacing:.06em;margin:0 0 .3rem;font-weight:700;">&#x1F4DD; Customer Note</p><p style="font-size:.8rem;color:var(--text-body);margin:0;">' + o.notes + '</p></div>' : '') +
        (o.status === 'out_for_delivery'
            ? '<div style="background:rgba(139,92,246,.06);border:1px solid rgba(139,92,246,.2);border-radius:.625rem;padding:.75rem 1rem;">' +
                '<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.625rem;">' +
                    '<span style="font-size:1.1rem;">&#x1F6F5;</span>' +
                    '<div>' +
                        '<p style="font-size:.75rem;font-weight:700;color:#a78bfa;margin:0 0 .1rem;">Rider En Route to Customer</p>' +
                        '<p style="font-size:.68rem;color:var(--text-muted);margin:0;">Live tracking — only the rider can mark as delivered.</p>' +
                    '</div>' +
                '</div>' +
                '<div id="adminOrderMap-' + o.id + '" style="height:220px;width:100%;border-radius:.5rem;overflow:hidden;background:#0a0a14;"></div>' +
              '</div>'
            : '') +
        (actionsHtml ? '<div><p style="font-size:.68rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin:0 0 .5rem;font-weight:600;">Actions</p>' + actionsHtml + '</div>' : '');

    document.getElementById('mmBody').innerHTML = html;
    openModal('manageModal');
    if (typeof lucide !== 'undefined') setTimeout(function(){ lucide.createIcons(); }, 0);

    // Init live rider map for out_for_delivery orders
    if (o.status === 'out_for_delivery' && o.rider_lat && o.rider_lng) {
        setTimeout(function() { initAdminRiderMap(o); }, 250);
    }
}

// Admin live rider map
var adminMapInstance = null;
const ADMIN_RESTAURANT = [13.3213129, 121.3027265];

async function fetchAdminRoute(from, to) {
    var url = 'https://router.project-osrm.org/route/v1/driving/' + from[1] + ',' + from[0] + ';' + to[1] + ',' + to[0] + '?overview=full&geometries=geojson';
    try {
        var r = await fetch(url);
        var d = await r.json();
        if (d.code === 'Ok' && d.routes.length) {
            return d.routes[0].geometry.coordinates.map(function(c){ return [c[1], c[0]]; });
        }
    } catch(e) { console.warn('OSRM admin', e); }
    return null;
}

async function initAdminRiderMap(o) {
    var mapEl = document.getElementById('adminOrderMap-' + o.id);
    if (!mapEl) return;

    // Destroy previous map if exists
    if (adminMapInstance) {
        try { adminMapInstance.remove(); } catch(e) {}
        adminMapInstance = null;
    }

    var riderPos = [parseFloat(o.rider_lat), parseFloat(o.rider_lng)];
    var custPos  = (o.delivery_lat && o.delivery_lng)
        ? [parseFloat(o.delivery_lat), parseFloat(o.delivery_lng)]
        : null;

    adminMapInstance = L.map(mapEl, { zoomControl: true, attributionControl: false });
    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20 }).addTo(adminMapInstance);
    L.tileLayer('https://mt1.google.com/vt/lyrs=h&x={x}&y={y}&z={z}', { maxZoom: 20, opacity: 0.85 }).addTo(adminMapInstance);

    // Restaurant marker
    L.marker(ADMIN_RESTAURANT, { icon: L.divIcon({
        html: '<div style="background:#facc15;width:34px;height:34px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #d97706;display:flex;align-items:center;justify-content:center;"><span style="transform:rotate(45deg);font-size:14px;">&#x1F354;</span></div>',
        className: '', iconSize: [34,34], iconAnchor: [17,34]
    })}).addTo(adminMapInstance).bindPopup('<b>EUT Restaurant</b>');

    // Rider marker
    L.marker(riderPos, { icon: L.divIcon({
        html: '<div style="background:#8b5cf6;width:40px;height:40px;border-radius:50%;border:3px solid #fff;display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 0 12px rgba(139,92,246,.7);">&#x1F6F5;</div>',
        className: '', iconSize: [40,40], iconAnchor: [20,20]
    })}).addTo(adminMapInstance).bindPopup('<b>Rider: ' + (o.rider || 'Rider') + '</b>');

    // Customer marker
    if (custPos) {
        L.marker(custPos, { icon: L.divIcon({
            html: '<div style="background:#ef4444;width:34px;height:34px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #b91c1c;display:flex;align-items:center;justify-content:center;"><span style="transform:rotate(45deg);font-size:14px;">&#x1F3E0;</span></div>',
            className: '', iconSize: [34,34], iconAnchor: [17,34]
        })}).addTo(adminMapInstance).bindPopup('<b>' + o.customer + '</b>');
    }

    var dest = custPos || [ADMIN_RESTAURANT[0]+.005, ADMIN_RESTAURANT[1]+.005];
    adminMapInstance.fitBounds([riderPos, dest], { padding: [40,40] });

    // Draw route: rider -> customer
    var route = await fetchAdminRoute(riderPos, dest);
    if (route && route.length) {
        var line = L.polyline(route, { color:'#8b5cf6', weight:5, opacity:1 }).addTo(adminMapInstance);
        adminMapInstance.fitBounds(line.getBounds(), { padding: [35,35] });
    } else {
        L.polyline([riderPos, dest], { color:'#8b5cf6', weight:3, opacity:.7, dashArray:'8 5' }).addTo(adminMapInstance);
    }
}

</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\patri\Desktop\EUT_WEB\resources\views/admin/orders.blade.php ENDPATH**/ ?>