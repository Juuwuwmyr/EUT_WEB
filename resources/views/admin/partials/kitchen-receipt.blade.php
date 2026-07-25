<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $order->order_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    font-family: 'Courier New', Courier, monospace;
    font-size: 12px;
    color: #000;
    background: #fff;
    width: 80mm;
    margin: 0 auto;
    padding: 4mm 4mm 10mm;
}

.center  { text-align: center; }
.right   { text-align: right; }
.bold    { font-weight: bold; }
.small   { font-size: 10px; }
.lg      { font-size: 15px; }
.xl      { font-size: 18px; font-weight: bold; }

.divider {
    border: none;
    border-top: 1px dashed #000;
    margin: 4px 0;
}
.divider-solid {
    border: none;
    border-top: 1px solid #000;
    margin: 4px 0;
}

/* Header */
.header { text-align: center; margin-bottom: 4px; }
.shop-name { font-size: 20px; font-weight: bold; letter-spacing: 1px; }
.tagline { font-size: 10px; letter-spacing: 2px; margin-top: 1px; }

/* Order meta */
.meta-row { display: flex; justify-content: space-between; margin: 1px 0; }
.order-num { font-size: 15px; font-weight: bold; text-align: center; margin: 4px 0; }

/* Items */
.item-block { margin: 3px 0; }
.item-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 4px;
}
.item-name { flex: 1; font-weight: bold; font-size: 12px; }
.item-price { font-size: 12px; white-space: nowrap; }
.item-qty { font-size: 11px; margin-right: 2px; }

.spec-list { padding-left: 10px; margin-top: 1px; }
.spec-row {
    font-size: 10px;
    color: #333;
    line-height: 1.4;
}
.spec-type {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.6;
}

/* Totals */
.totals { margin-top: 4px; }
.total-row { display: flex; justify-content: space-between; margin: 1px 0; }
.total-row.grand {
    font-size: 15px;
    font-weight: bold;
    margin-top: 4px;
    padding-top: 4px;
    border-top: 1px solid #000;
}

/* Payment */
.payment-badge {
    display: inline-block;
    border: 1px solid #000;
    padding: 1px 6px;
    font-size: 10px;
    font-weight: bold;
    letter-spacing: 1px;
    text-transform: uppercase;
}

/* Notes */
.notes-box {
    border: 1px dashed #000;
    padding: 3px 5px;
    font-size: 11px;
    margin: 4px 0;
}

/* Footer */
.footer { text-align: center; margin-top: 8px; font-size: 10px; line-height: 1.6; }

@media print {
    @page {
        size: 80mm auto;
        margin: 0;
    }
    body {
        width: 80mm;
        padding: 3mm 3mm 8mm;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    /* Hide everything except the receipt when printing from a full page */
    body > * { display: block; }
}
</style>
</head>
<body>

{{-- ── HEADER ── --}}
<div class="header">
    <div class="shop-name">E.U.T</div>
    <div class="tagline">EAT • UNWIND • TEA</div>
    <div class="small" style="margin-top:2px;">Snack House</div>
</div>

<hr class="divider-solid" style="margin:5px 0;">

{{-- ── ORDER INFO ── --}}
<div class="order-num">{{ $order->order_number }}</div>

<div class="meta-row small">
    <span>Date:</span>
    <span>{{ $order->created_at->format('M d, Y g:i A') }}</span>
</div>
<div class="meta-row small">
    <span>Customer:</span>
    <span>{{ $order->user?->name ?? 'Guest' }}</span>
</div>
<div class="meta-row small">
    <span>Payment:</span>
    <span class="bold" style="text-transform:uppercase;">
        {{ $order->payment_method === 'cash' ? 'Cash on Delivery' : strtoupper($order->payment_method) }}
    </span>
</div>

<hr class="divider" style="margin:5px 0;">

{{-- ── ORDER ITEMS ── --}}
@foreach($order->items as $item)
    <div class="item-block">
        <div class="item-row">
            <span class="item-qty">{{ $item->quantity }}x</span>
            <span class="item-name">{{ $item->item_name }}</span>
            <span class="item-price">₱{{ number_format($item->subtotal, 2) }}</span>
        </div>

        @php
            $specs = collect($item->modifiers ?? [])
                ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
                ->values();
        @endphp

        @if($specs->count())
            <div class="spec-list">
                @foreach($specs as $spec)
                    <div class="spec-row">
                        <span class="spec-type">{{ ucfirst($spec['type'] ?? 'option') }}:</span>
                        {{ $spec['name'] }}@if(($spec['price_type'] ?? '') === 'add' && ($spec['price_adjustment'] ?? 0) > 0)
                            <span style="font-size:9px;"> +₱{{ number_format($spec['price_adjustment'], 2) }}</span>@endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endforeach

<hr class="divider" style="margin:5px 0;">

{{-- ── TOTALS ── --}}
<div class="totals">
    <div class="total-row small">
        <span>Subtotal</span>
        <span>₱{{ number_format($order->subtotal, 2) }}</span>
    </div>
    <div class="total-row small">
        <span>Delivery fee</span>
        <span>{{ $order->delivery_fee == 0 ? 'FREE' : '₱' . number_format($order->delivery_fee, 2) }}</span>
    </div>
    <div class="total-row grand">
        <span>TOTAL</span>
        <span>₱{{ number_format($order->total, 2) }}</span>
    </div>
</div>

{{-- ── DELIVERY ADDRESS ── --}}
@if($order->delivery_address)
<hr class="divider" style="margin:5px 0;">
<div class="small">
    <div class="bold" style="margin-bottom:1px;">📍 Deliver to:</div>
    <div style="line-height:1.4;">{{ $order->delivery_address }}</div>
</div>
@endif

{{-- ── NOTES ── --}}
@if($order->notes)
<div class="notes-box">
    <span class="bold">📝 Note:</span> {{ $order->notes }}
</div>
@endif

{{-- ── FOOTER ── --}}
<hr class="divider-solid" style="margin:6px 0 4px;">
<div class="footer">
    <div>Thank you for your order!</div>
    <div class="small" style="margin-top:2px;">Accepted at {{ now()->format('g:i A') }}</div>
    <div class="small" style="margin-top:4px; letter-spacing:1px;">*** EAT • UNWIND • TEA ***</div>
</div>

<script>
    // Auto-print and close when loaded in the print iframe
    window.onload = function () {
        window.print();
    };
</script>
</body>
</html>
