<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=160, initial-scale=1.0, maximum-scale=1.0">
<title>Kitchen Ticket {{ $order->order_number }}</title>
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

body { padding: 4px 4px 20px 4px; }

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

.header { text-align: center; margin-bottom: 3px; }
.kitchen-label {
    font-size: 16px;
    font-weight: bold;
    letter-spacing: 2px;
    text-transform: uppercase;
    text-align: center;
    margin: 3px 0;
    border: 2px solid #000;
    padding: 2px 0;
}

.order-num {
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
.row .label { font-weight: normal; }
.row .val   { font-weight: bold; margin-left: 2px; }

/* Order type badge */
.type-badge {
    display: block;
    text-align: center;
    font-size: 12px;
    font-weight: bold;
    letter-spacing: 1px;
    padding: 2px 4px;
    margin: 3px 0;
    border: 1px solid #000;
}

/* Items — large and bold for kitchen readability */
.item-row {
    display: block;
    width: 100%;
    margin: 4px 0;
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
    font-size: 14px;
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
    @page {
        size: 58mm auto;
        margin: 0;
    }
    html, body {
        width: 160px !important;
        max-width: 160px !important;
        padding: 4px 4px 20px 4px !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    script, noscript { display: none !important; }
}
</style>
</head>
<body>

@if($addonIds)
<div class="kitchen-label" style="background:#000;color:#fff;letter-spacing:1px;">➕ ADD-ON ORDER ➕</div>
@else
<div class="kitchen-label">⬛ KITCHEN ⬛</div>
@endif

<div class="order-num">{{ $order->order_number }}</div>

<div class="type-badge">{{ strtoupper($order->order_type_label) }}</div>

<div class="row"><span class="label">Customer:</span> <span class="val">{{ $order->user?->name ?? 'Guest' }}</span></div>
<div class="row"><span class="label">Time:</span> <span class="val">{{ $order->created_at->format('g:i A') }}</span></div>
@if($order->order_type === 'dine_in' && $order->table_number)
<div class="row" style="font-size:13px;font-weight:bold;border:1px solid #000;padding:2px 3px;margin:3px 0;text-align:center;">🪑 TABLE {{ $order->table_number }}</div>
@endif
@if($order->order_type === 'delivery' && $order->delivery_address)
<div class="row"><span class="label">Address:</span> <span class="val">{{ $order->delivery_address }}</span></div>
@endif

<hr class="double">

{{-- Items — big and clear, NO prices --}}
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

<hr class="double">

@if($order->notes)
<div class="notes-box">
    <div class="notes-label">⚠ Special Instructions</div>
    {{ $order->notes }}
</div>
@endif

<div class="footer">
    <div>{{ now()->format('M d, Y · g:i A') }}</div>
    <div style="margin-top:2px;">*** KITCHEN COPY — NO PRICE ***</div>
</div>

<script>
// ── Auto-print silently on every load ────────────────────────────────────
// Works in three contexts:
//   1. Popup   (window.opener set)
//   2. Hidden iframe (window.name = 'kitchen_autoprint')
//   3. Direct  (?autoprint=1 in URL)
//
// For a completely dialog-free experience launch Chrome with:
//   --kiosk-printing
// That flag makes window.print() go directly to the default printer.
// Without it, the OS print dialog will appear once per invocation.

function _fixBodyWidth() {
    document.body.style.width    = '185px';
    document.body.style.maxWidth = '185px';
}
document.addEventListener('DOMContentLoaded', _fixBodyWidth);

window.onload = function () {
    _fixBodyWidth();

    // Always auto-print — popup, iframe, or direct URL
    setTimeout(function () {
        try {
            window.focus();
            window.print();
        } catch (e) {}
    }, 300);
};

// After printing: close popup OR signal parent iframe to clean up
window.addEventListener('afterprint', function () {
    if (window.opener) {
        // Popup context — close self
        setTimeout(function () { try { window.close(); } catch (e) {} }, 500);
    } else if (window.parent && window.parent !== window) {
        // iframe context — post message so dashboard.blade.php removes the iframe
        try { window.parent.postMessage({ type: 'kitchen_ticket_printed' }, '*'); } catch (e) {}
    }
});

// Safety net: close popup after 10 s even if afterprint never fires
if (window.opener) {
    setTimeout(function () { try { window.close(); } catch (e) {} }, 10000);
}
</script>
</body>
</html>
