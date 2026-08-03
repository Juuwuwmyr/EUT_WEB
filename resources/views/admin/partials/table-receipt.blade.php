<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=185, initial-scale=1.0, maximum-scale=1.0">
<title>Table {{ $tableNumber }} Receipt</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

html, body {
    width: 185px;
    max-width: 185px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8.5px;
    color: #000;
    background: #fff;
}

body {
    padding: 4px 8px 20px 4px;
}

.center  { text-align: center; }
.bold    { font-weight: bold; }

hr {
    border: none;
    border-top: 1px dashed #000;
    margin: 3px 0;
    width: 100%;
}
hr.solid {
    border-top: 1px solid #000;
}

.header { text-align: center; margin-bottom: 3px; }
.shop-name { font-size: 14px; font-weight: bold; letter-spacing: 1px; }
.tagline { font-size: 8px; letter-spacing: 1px; margin-top: 1px; }

.table-num { font-size: 12px; font-weight: bold; text-align: center; margin: 3px 0; }

.row {
    display: block;
    margin: 1px 0;
    font-size: 9px;
    width: 100%;
    word-break: break-word;
}
.row span:first-child { font-weight: normal; }
.row span:last-child  { margin-left: 4px; }

.item-row {
    display: block;
    width: 100%;
    margin: 2px 0;
}
.item-qty   { flex-shrink: 0; font-size: 10px; margin-right: 2px; }
.item-name  { flex: 1; font-weight: bold; font-size: 10px; word-break: break-word; padding-right: 4px; }

.spec-list { padding-left: 8px; }
.spec-row  { font-size: 8.5px; color: #000; font-weight: bold; line-height: 1.4; }

.order-label { font-size: 8px; font-weight: bold; background: #000; color: #fff; padding: 1px 3px; margin: 3px 0 2px; display: block; }

.notes-box { border: 1px dashed #000; padding: 2px 3px; font-size: 9px; margin: 3px 0; word-break: break-word; }
.footer    { text-align: center; margin-top: 5px; font-size: 8px; line-height: 1.6; }

@media print {
    @page {
        size: 58mm auto;
        margin: 0;
    }
    html, body {
        width: 185px !important;
        max-width: 185px !important;
        padding: 4px 8px 20px 4px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    script, noscript { display: none !important; visibility: hidden !important; }
}
</style>
</head>
<body>

<div class="header">
    <div class="shop-name">E.U.T</div>
    <div class="tagline">EAT &middot; UNWIND &middot; TEA</div>
    <div style="font-size:8px;margin-top:1px;">Snack House</div>
</div>

<hr class="solid">

<div class="table-num">TABLE {{ $tableNumber }}</div>

<div class="row"><span>Type: </span><span class="bold">Dine-in</span></div>
<div class="row"><span>Date: {{ now()->format('M d, Y g:i A') }}</span></div>
<div class="row"><span>Orders: </span><span class="bold">{{ $orders->count() }} order(s)</span></div>
<div class="row"><span>Payment: <strong>CASH (DINE-IN)</strong></span></div>

<hr>

<div style="display:flex;justify-content:space-between;font-size:9px;font-weight:bold;border-bottom:1px solid #000;padding-bottom:2px;margin-bottom:2px;">
    <span>QTY &nbsp;ITEM</span>
    <span>PRICE</span>
</div>

@php $grandTotal = 0; @endphp

@foreach($orders as $order)
@php $grandTotal += $order->total; @endphp

{{-- Only show order label separator when there are multiple orders --}}
@if($orders->count() > 1)
<span class="order-label">{{ $order->order_number }}</span>
@endif

@foreach($order->items as $item)
<div class="item-row">
    <div style="display:flex;justify-content:space-between;width:100%;">
        <span style="flex:1;word-break:break-word;"><span class="item-qty">{{ $item->quantity }}x</span> <span class="item-name">{{ $item->item_name }}</span></span>
        <span style="flex-shrink:0;margin-left:4px;font-size:9px;">P{{ number_format($item->subtotal, 2) }}</span>
    </div>
@php
    $specs = collect($item->modifiers ?? [])
        ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
        ->values();
@endphp
@if($specs->count())
<div class="spec-list">
    @foreach($specs as $spec)
    <div class="spec-row">- {{ $spec['name'] }}@if(($spec['price_type'] ?? '') === 'add' && ($spec['price_adjustment'] ?? 0) > 0) +P{{ number_format($spec['price_adjustment'], 2) }}@endif</div>
    @endforeach
</div>
@endif
</div>
@endforeach

@if($order->notes)
<div class="notes-box"><span class="bold">Note:</span> {{ $order->notes }}</div>
@endif

@endforeach

<hr>

<div style="display:flex;justify-content:space-between;margin:3px 0 1px;font-size:11px;font-weight:bold;width:100%;padding-top:3px;border-top:1px solid #000;">
    <span>TOTAL</span><span>P{{ number_format($grandTotal, 2) }}</span>
</div>

<hr class="solid" style="margin-top:5px;">
<div class="footer">
    <div>Thank you for dining with us!</div>
    <div style="margin-top:1px;">{{ now()->format('g:i A') }}</div>
    <div style="margin-top:2px;">*** EAT * UNWIND * TEA ***</div>
</div>

<script>
window.onload = function () {
    document.body.style.width = '185px';
    document.body.style.maxWidth = '185px';
    setTimeout(function() { window.print(); }, 300);
};
</script>
</body>
</html>
