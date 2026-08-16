<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=160, initial-scale=1.0, maximum-scale=1.0">
<title>Kitchen — Table {{ $tableNumber }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

html, body {
    width: 160px;
    max-width: 160px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8px;
    color: #000;
    background: #fff;
}

body { padding: 4px 4px 20px 4px; margin-left: 1.5mm; }

.center { text-align: center; }
.bold   { font-weight: bold; }

hr {
    border: none;
    border-top: 1px dashed #000;
    margin: 3px 0;
    width: 100%;
}
hr.solid { border-top: 1px solid #000; }
hr.double { border-top: 3px double #000; }

.kitchen-label {
    font-size: 14px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    text-align: center;
    margin: 3px 0;
    border: 2px solid #000;
    padding: 2px 0;
}

.table-num {
    font-size: 18px;
    font-weight: bold;
    text-align: center;
    margin: 4px 0;
    letter-spacing: 1px;
}

.row {
    display: block;
    margin: 1px 0;
    font-size: 9px;
    width: 100%;
    word-break: break-word;
}

/* Order label — black band separating each sub-order */
.order-label {
    font-size: 8px;
    font-weight: bold;
    background: #000;
    color: #fff;
    padding: 1px 3px;
    margin: 4px 0 2px;
    display: block;
    letter-spacing: .5px;
}

/* Items — big and readable for kitchen */
.item-row {
    display: block;
    width: 100%;
    margin: 3px 0;
    border-bottom: 1px dotted #999;
    padding-bottom: 3px;
}
.item-row:last-child { border-bottom: none; }

.item-line {
    display: flex;
    width: 100%;
    gap: 3px;
    align-items: baseline;
}
.item-qty  {
    font-size: 13px;
    font-weight: bold;
    flex-shrink: 0;
    min-width: 22px;
}
.item-name {
    font-size: 11px;
    font-weight: bold;
    flex: 1;
    word-break: break-word;
    line-height: 1.3;
}

.spec-list { padding-left: 10px; margin-top: 2px; }
.spec-row  {
    font-size: 9px;
    font-weight: bold;
    line-height: 1.5;
    color: #000;
}
.spec-row::before { content: "– "; }

.notes-box {
    border: 2px solid #000;
    padding: 3px 4px;
    font-size: 10px;
    font-weight: bold;
    margin: 3px 0;
    word-break: break-word;
}
.notes-label { font-size: 8px; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 1px; }

.footer {
    text-align: center;
    margin-top: 5px;
    font-size: 8px;
    line-height: 1.6;
}

@media print {
    @page { size: 58mm auto; margin: 0; }
    html, body {
        width: 160px !important;
        max-width: 160px !important;
        padding: 4px 4px 20px 4px !important;
        margin-left: 1.5mm !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    script, noscript { display: none !important; }
}
</style>
</head>
<body>

<div class="kitchen-label">⬛ KITCHEN ⬛</div>

<div class="table-num">🪑 TABLE {{ $tableNumber }}</div>

<div class="row"><span>Dine-in</span></div>
<div class="row"><span>{{ now()->format('M d, Y · g:i A') }}</span></div>
<div class="row"><span>{{ $orders->count() }} order(s)</span></div>

<hr class="double">

@foreach($orders as $order)

{{-- Show sub-order divider only when there are multiple orders --}}
@if($orders->count() > 1)
<span class="order-label">
    {{ $loop->first ? 'ORIGINAL' : 'PAHABOL #' . $loop->index }}
    &nbsp;·&nbsp; {{ $order->order_number }}
</span>
@endif

@foreach($order->items as $item)
<div class="item-row">
    <div class="item-line">
        <span class="item-qty">{{ $item->quantity }}×</span>
        <span class="item-name">{{ $item->item_name }}</span>
    </div>
    @php
        $specs = collect($item->modifiers ?? [])
            ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
            ->values();
    @endphp
    @if($specs->count())
    <div class="spec-list">
        @foreach($specs as $spec)
        <div class="spec-row">{{ $spec['name'] }}</div>
        @endforeach
    </div>
    @endif
</div>
@endforeach

@if($order->notes)
<div class="notes-box">
    <div class="notes-label">⚠ Note</div>
    {{ $order->notes }}
</div>
@endif

@if(!$loop->last)<hr>@endif

@endforeach

<hr class="double">

<div class="footer">
    <div>*** KITCHEN COPY — NO PRICE ***</div>
    <div style="margin-top:2px;">{{ now()->format('g:i A') }}</div>
</div>

<script>
window.onload = function () {
    document.body.style.width = '185px';
    document.body.style.maxWidth = '185px';
    if (window.opener || window.name === 'receipt_print') {
        setTimeout(function() { window.print(); }, 300);
    }
};
</script>
</body>
</html>
