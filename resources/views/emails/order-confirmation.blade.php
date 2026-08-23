<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.08);">

                {{-- Header --}}
                <tr>
                    <td style="background:#0a0a0a;padding:28px 32px;text-align:center;">
                        <div style="font-size:24px;font-weight:700;color:#facc15;letter-spacing:.5px;">E.U.T Snack House</div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:6px;letter-spacing:1px;text-transform:uppercase;">Order Confirmed 🎉</div>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0 0 12px;font-size:16px;color:#111827;">Hi {{ $name }},</p>
                        <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#4b5563;">
                            Your order has been received and is being processed. Here's a summary:
                        </p>

                        {{-- Order Info --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f9fafb;border-radius:10px;padding:16px;margin-bottom:20px;">
                            <tr>
                                <td style="padding:6px 0;">
                                    <span style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Order Number</span><br>
                                    <span style="font-size:18px;font-weight:700;color:#111827;">#{{ $order->order_number }}</span>
                                </td>
                                <td style="padding:6px 0;text-align:right;">
                                    <span style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Type</span><br>
                                    <span style="font-size:15px;font-weight:600;color:#111827;">{{ ucfirst(str_replace('_', ' ', $order->order_type)) }}</span>
                                </td>
                            </tr>
                            @if($order->order_type === 'delivery' && $order->delivery_address)
                            <tr>
                                <td colspan="2" style="padding:6px 0;border-top:1px solid #e5e7eb;">
                                    <span style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Delivery Address</span><br>
                                    <span style="font-size:14px;color:#374151;">{{ $order->delivery_address }}</span>
                                </td>
                            </tr>
                            @endif
                            @if($order->table_number)
                            <tr>
                                <td colspan="2" style="padding:6px 0;border-top:1px solid #e5e7eb;">
                                    <span style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Table</span><br>
                                    <span style="font-size:14px;color:#374151;">Table {{ $order->table_number }}</span>
                                </td>
                            </tr>
                            @endif
                            @if($order->notes)
                            <tr>
                                <td colspan="2" style="padding:6px 0;border-top:1px solid #e5e7eb;">
                                    <span style="font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">Notes</span><br>
                                    <span style="font-size:14px;color:#374151;">{{ $order->notes }}</span>
                                </td>
                            </tr>
                            @endif
                        </table>

                        {{-- Items --}}
                        <p style="font-size:13px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em;margin:0 0 10px;">Items Ordered</p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:16px;">
                            @foreach($order->items as $item)
                            <tr>
                                <td style="padding:8px 0;border-bottom:1px solid #f3f4f6;">
                                    <span style="font-size:14px;font-weight:600;color:#111827;">{{ $item->quantity }}× {{ $item->item_name }}</span>
                                    @if(!empty($item->modifiers))
                                        <br><span style="font-size:12px;color:#6b7280;">
                                            {{ collect($item->modifiers)->where('name', '!=', '')->pluck('name')->implode(', ') }}
                                        </span>
                                    @endif
                                </td>
                                <td style="padding:8px 0;border-bottom:1px solid #f3f4f6;text-align:right;font-size:14px;font-weight:600;color:#111827;">
                                    ₱{{ number_format($item->subtotal, 2) }}
                                </td>
                            </tr>
                            @endforeach
                        </table>

                        {{-- Totals --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:24px;">
                            <tr>
                                <td style="padding:4px 0;font-size:13px;color:#6b7280;">Subtotal</td>
                                <td style="padding:4px 0;font-size:13px;color:#6b7280;text-align:right;">₱{{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            @if($order->delivery_fee > 0)
                            <tr>
                                <td style="padding:4px 0;font-size:13px;color:#6b7280;">Delivery Fee</td>
                                <td style="padding:4px 0;font-size:13px;color:#6b7280;text-align:right;">₱{{ number_format($order->delivery_fee, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding:8px 0 0;font-size:16px;font-weight:700;color:#111827;border-top:2px solid #111827;">Total</td>
                                <td style="padding:8px 0 0;font-size:16px;font-weight:700;color:#facc15;text-align:right;border-top:2px solid #111827;">₱{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </table>

                        {{-- Payment --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fef3c7;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:24px;">
                            <tr>
                                <td style="font-size:13px;color:#92400e;">
                                    💳 <strong>Payment:</strong>
                                    {{ $order->payment_method === 'gcash' ? 'GCash' : ($order->payment_method === 'card' ? 'Card' : 'Cash on Delivery') }}
                                    @if($order->payment_status === 'paid')
                                        <span style="color:#16a34a;font-weight:700;"> ✓ Paid</span>
                                    @else
                                        <span style="color:#d97706;font-weight:700;"> (Pending)</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        {{-- Track button --}}
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center">
                                    <a href="{{ config('app.url') }}/shop/tracking"
                                       style="display:inline-block;background:linear-gradient(135deg,#f59e0b,#facc15);color:#000;font-weight:700;font-size:15px;padding:14px 40px;border-radius:10px;text-decoration:none;box-shadow:0 4px 16px rgba(250,204,21,.3);">
                                        Track Your Order →
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:20px 32px;background:#f9fafb;border-top:1px solid #e5e7eb;text-align:center;">
                        <p style="margin:0 0 4px;font-size:12px;color:#6b7280;">Questions? Contact us on Facebook or visit our store.</p>
                        <p style="margin:0;font-size:11px;color:#9ca3af;">&copy; {{ date('Y') }} E.U.T Snack House · Naujan, Oriental Mindoro</p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>
</body>
</html>
