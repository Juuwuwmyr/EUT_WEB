@php
    $elapsed = (int) $order->created_at->diffInMinutes(now());
    $urgent = $elapsed >= 20 && $column !== 'ready';
    $elapsedBg = $elapsed >= 20 ? 'rgba(239,68,68,.15)' : ($elapsed >= 10 ? 'rgba(245,158,11,.15)' : 'rgba(255,255,255,.06)');
    $elapsedColor = $elapsed >= 20 ? '#ef4444' : ($elapsed >= 10 ? '#f59e0b' : 'var(--text-muted)');
    $elapsedLabel = $elapsed >= 20 ? "{$elapsed}m — URGENT" : "{$elapsed}m ago";

    $isChef = request()->routeIs('chef.*');

    $deliveryMeta = match (true) {
        $order->status === 'out_for_delivery' => [
            'label'  => $isChef ? '✓ Out for Delivery' : 'Picked Up — On the Way',
            'color'  => '#8b5cf6',
            'bg'     => 'rgba(139,92,246,.14)',
            'detail' => $order->picked_up_at ? 'Left kitchen at ' . $order->picked_up_at->format('g:i A') : null,
            'picked' => true,
        ],
        $order->status === 'rider_assigned' => [
            'label'  => $isChef ? '✓ Food Ready' : 'Hand to Rider: ' . ($order->rider?->user?->name ?? 'Assigned'),
            'color'  => $isChef ? '#10b981' : '#2563eb',
            'bg'     => $isChef ? 'rgba(16,185,129,.14)' : 'rgba(37,99,235,.14)',
            'detail' => $order->prepared_at ? 'Food ready at ' . $order->prepared_at->format('g:i A') : null,
            'picked' => false,
        ],
        default => [
            'label'  => '✓ Food Ready',
            'color'  => '#10b981',
            'bg'     => 'rgba(16,185,129,.14)',
            'detail' => $order->prepared_at ? 'Food ready at ' . $order->prepared_at->format('g:i A') : null,
            'picked' => false,
        ],
    };
@endphp

<div class="k-order-card{{ $urgent ? ' is-urgent' : '' }}{{ !empty($deliveryMeta['picked']) ? ' is-picked-up' : '' }}" data-order-id="{{ $order->id }}" style="cursor:pointer;"
    onclick="if(!event.target.closest('.k-actions'))openOrderModal({{ $order->id }})">
    @if($column === 'ready')
        <div class="k-delivery-banner" style="background:{{ $deliveryMeta['bg'] }};color:{{ $deliveryMeta['color'] }};">
            {{ $deliveryMeta['label'] }}
        </div>
    @endif

    <div class="k-card-top">
        <div>
            <div class="k-order-num">{{ $order->order_number }}</div>
            <div class="k-customer">{{ $order->user?->name ?? 'Guest' }} · {{ $order->created_at->format('g:i A') }}</div>
        </div>
        @if($column !== 'ready')
            <span class="k-elapsed" style="background:{{ $elapsedBg }};color:{{ $elapsedColor }};">{{ $elapsedLabel }}</span>
        @endif
    </div>

    <div class="k-items">
        @foreach($order->items as $item)
            <div class="k-item">
                <img class="k-item-img" src="{{ $item->image ? asset($item->image) : asset('images/hero-burger.webp') }}" alt="">
                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:baseline;gap:.4rem;">
                        <span class="k-item-qty">{{ $item->quantity }}×</span>
                        <span class="k-item-name">{{ $item->item_name }}</span>
                    </div>
                    @if(!empty($item->modifiers))
                        <div class="k-modifiers">
                            @foreach($item->modifiers as $mod)
                                @if(!empty($mod['name']))
                                    <span class="k-mod-tag">{{ $mod['name'] }}</span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($order->notes)
        <div class="k-notes">📝 {{ $order->notes }}</div>
    @endif

    <div class="k-actions">
        @if($column === 'new')
            @if(auth()->user()->isAdmin())
                <button type="button" class="k-btn k-btn-accept" onclick="event.stopPropagation();kitchenAction('accept', {{ $order->id }}, this)">✓ Accept</button>
            @else
                <div style="text-align:center;width:100%;font-size:.72rem;color:var(--text-muted);padding:.4rem;">Waiting for Admin to accept...</div>
            @endif
        @elseif($column === 'queued')
            <button type="button" class="k-btn k-btn-cook" onclick="event.stopPropagation();kitchenAction('start', {{ $order->id }}, this)">🍳 Start Cooking</button>
        @elseif($column === 'cooking')
            <button type="button" class="k-btn k-btn-ready" onclick="event.stopPropagation();kitchenAction('ready', {{ $order->id }}, this)">✅ Mark Ready</button>
        @else
            <div style="text-align:center;width:100%;font-size:.72rem;color:{{ $deliveryMeta['color'] }};font-weight:700;padding:.4rem;line-height:1.45;">
                {{ $deliveryMeta['detail'] ?? 'Ready for delivery' }}
            </div>
        @endif
    </div>
</div>
