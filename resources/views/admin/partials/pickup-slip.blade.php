<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=185, initial-scale=1.0, maximum-scale=1.0">
<title>Pickup Slip {{ $order->order_number }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
html, body {
    width: 185px;
    max-width: 185px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9px;
    color: #000;
    background: #fff;
}
body { padding: 4px 8px 20px 4px; }
hr { border: none; border-top: 1px dashed #000; margin: 3px 0; width: 100%; }
hr.solid  { border-top: 1px solid #000; }
hr.double { border-top: 3px double #000; }

.store-name { font-size: 13px; font-weight: bold; text-align: center; letter-spacing: 1px; margin-bottom: 1px; }
.store-sub  { font-size: 8px; text-align: center; margin-bottom: 3px; }

.pickup-label {
    font-size: 15px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    text-align: center;
    margin: 4px 0;
    border: 3px solid #000;
    padding: 3px 0;
    background: #000;
    color: #fff;
}
.order-num {
    font-size: 18px; font-weight: bold; text-align: center;
    margin: 4px 0; letter-spacing: 1px;
}
.row { display: block; margin: 1px 0; font-size: 9px; width: 100%; word-break: break-word; }
.row .label { font-weight: normal; }
.row .val   { font-weight: bold; margin-left: 2px; }

.rider-box {
    border: 2px solid #000; padding: 3px 4px; margin: 4px 0;
    font-size: 10px; font-weight: bold;
}
.rider-label {
    font-size: 8px; text-transform: uppercase;
    letter-spacing: .06em; font-weight: normal; margin-bottom: 1px;
}

.item-row { display: block; width: 100%; margin: 4px 0; border-bottom: 1px dotted #999; padding-bottom: 3px; }
.item-row:last-child { border-bottom: none; }
.item-line { display: flex; width: 100%; gap: 3px; align-items: baseline; }
.item-qty  { font-size: 12px; font-weight: bold; flex-shrink: 0; min-width: 20px; }
.item-name { font-size: 10px; font-weight: bold; flex: 1; word-break: break-word; line-height: 1.3; }
.item-price{ font-size: 10px; font-weight: bold; flex-shrink: 0; text-align: right; min-width: 42px; }

.spec-list { padding-left: 10px; margin-top: 2px; }
.spec-row  { font-size: 8px; font-weight: bold; line-height: 1.5; }
.spec-row::before { content: "– "; }

.total-row { display: flex; justify-content: space-between; font-size: 9px; margin: 2px 0; }
.total-row.grand {
    font-size: 13px; font-weight: bold;
    border-top: 2px solid #000; padding-top: 3px; margin-top: 3px;
}
.notes-box { border: 2px solid #000; padding: 3px 4px; font-size: 10px; font-weight: bold; margin: 3px 0; word-break: break-word; }
.notes-label { font-size: 8px; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 1px; }
.footer { text-align: center; margin-top: 5px; font-size: 8px; line-height: 1.6; }

@media print {
    @page { size: 58mm auto; margin: 0; }
    html, body { width: 185px !important; max-width: 185px !important; padding: 4px 8px 20px 4px !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    script, noscript { display: none !important; }
}
</style>
</head>
<body>

<div class="store-name">EUT SNACK HOUSE</div>
<div class="store-sub">Naujan, Oriental Mindoro</div>
<hr class="solid">

<div class="pickup-label">🛵 PICKUP SLIP 🛵</div>
<div class="order-num">{{ $order->order_number }}</div>
<hr>

<div class="row"><span class="label">Date:</span> <span class="val">{{ $order->created_at->format('M d, Y') }}</span></div>
<div class="row"><span class="label">Time:</span> <span class="val">{{ $order->created_at->format('g:i A') }}</span></div>
<div class="row"><span class="label">Customer:</span> <span class="val">{{ $order->user?->name ?? 'Guest' }}</span></div>
@if($order->delivery_address)
<div class="row"><span class="label">Address:</span> <span class="val">{{ $order->delivery_address }}</span></div>
@endif
<hr>

{{-- Rider Box --}}
@php $riderUser = $order->rider?->user; @endphp
@if($riderUser)
<div class="rider-box">
    <div class="rider-label">🏍 Rider / Assigned To</div>
    {{ $riderUser->name }}
    @if($order->rider?->phone)
    <div style="font-size:8px;font-weight:normal;">📞 {{ $order->rider->phone }}</div>
    @endif
</div>
@endif

<hr>
<div style="font-size:8px;text-align:center;font-weight:bold;letter-spacing:.05em;margin-bottom:2px;">ORDER ITEMS</div>
<hr>

{{-- Items with prices --}}
@php $subtotalCalc = 0; @endphp
@foreach($order->items as $item)
@php
    $qty       = $item->quantity;
    $price     = (float) $item->unit_price;
    $lineTotal = $item->subtotal > 0 ? (float)$item->subtotal : $price * $qty;
    $subtotalCalc += $lineTotal;
    $specs = collect($item->modifiers ?? [])
        ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
        ->values();
@endphp
<div class="item-row">
    <div class="item-line">
        <span class="item-qty">{{ $qty }}×</span>
        <span class="item-name">{{ $item->item_name }}</span>
        <span class="item-price">₱{{ number_format($lineTotal, 2) }}</span>
    </div>
    @if($specs->count())
    <div class="spec-list">
        @foreach($specs as $spec)
        <div class="spec-row">{{ $spec['name'] }}</div>
        @endforeach
    </div>
    @endif
</div>
@endforeach

<hr class="double">

<div class="total-row">
    <span>Subtotal</span>
    <span>₱{{ number_format($order->subtotal ?? $subtotalCalc, 2) }}</span>
</div>
@if(($order->delivery_fee ?? 0) > 0)
<div class="total-row">
    <span>Delivery Fee</span>
    <span>₱{{ number_format($order->delivery_fee, 2) }}</span>
</div>
@endif
<div class="total-row grand">
    <span>TOTAL</span>
    <span>₱{{ number_format($order->total, 2) }}</span>
</div>
<hr>
<div class="total-row">
    <span>Payment</span>
    <span style="text-transform:uppercase;font-weight:bold;">{{ $order->payment_method ?? 'Cash' }}</span>
</div>

@if($order->notes)
<hr>
<div class="notes-box">
    <div class="notes-label">⚠ Special Instructions</div>
    {{ $order->notes }}
</div>
@endif

<div class="footer">
    <div>Picked up: {{ now()->format('M d, Y · g:i A') }}</div>
    <div style="margin-top:2px;font-weight:bold;">*** PICKUP / DELIVERY COPY ***</div>
    <div style="margin-top:1px;">Thank you! 🙏</div>
</div>

<script>
function _fixW() { document.body.style.width = '185px'; document.body.style.maxWidth = '185px'; }
document.addEventListener('DOMContentLoaded', _fixW);
window.onload = function () {
    _fixW();
    setTimeout(function () { try { window.focus(); window.print(); } catch(e) {} }, 300);
};
window.addEventListener('afterprint', function () {
    if (window.opener) {
        setTimeout(function () { try { window.close(); } catch(e) {} }, 500);
    } else if (window.parent && window.parent !== window) {
        try { window.parent.postMessage({ type: 'pickup_slip_printed' }, '*'); } catch(e) {}
    }
});
if (window.opener) { setTimeout(function () { try { window.close(); } catch(e) {} }, 10000); }
</script>
</body>
</html>

