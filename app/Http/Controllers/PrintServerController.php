<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrintServerController extends Controller
{
    /**
     * GET /api/print-server/pending-prints
     * Returns unprinted kitchen print jobs with full order HTML
     */
    public function pendingPrints()
    {
        $jobs = DB::table('kitchen_print_jobs')
            ->where('printed', false)
            ->orderBy('created_at')
            ->get();

        $result = [];

        foreach ($jobs as $job) {
            $order = Order::with(['items', 'user'])->find($job->order_id);
            if (!$order) {
                // Mark orphaned job as printed
                DB::table('kitchen_print_jobs')->where('id', $job->id)->update(['printed' => true, 'printed_at' => now()]);
                continue;
            }

            $result[] = [
                'job_id'       => $job->id,
                'type'         => $job->type,
                'order_id'     => $order->id,
                'order_number' => $order->order_number,
                'order_type'   => $order->order_type,
                'order_type_label' => $order->order_type_label,
                'table_number' => $order->table_number,
                'customer'     => $order->user?->name ?? 'Guest',
                'placed_at'    => $order->created_at->format('M d, Y g:i A'),
                'accepted_at'  => $order->accepted_at?->format('g:i A'),
                'delivery_address' => $order->delivery_address,
                'notes'        => $order->notes,
                'payment_method' => $order->payment_method,
                'subtotal'     => (float) $order->subtotal,
                'delivery_fee' => (float) $order->delivery_fee,
                'total'        => (float) $order->total,
                'items'        => $order->items->map(fn($i) => [
                    'name'      => $i->item_name,
                    'qty'       => $i->quantity,
                    'price'     => (float) $i->unit_price,
                    'subtotal'  => (float) $i->subtotal,
                    'modifiers' => collect($i->modifiers ?? [])
                        ->filter(fn($m) => !empty($m['name']) && !preg_match('/^no\s/i', $m['name']))
                        ->values()
                        ->map(fn($m) => [
                            'name'  => $m['name'],
                            'price' => ($m['price_type'] ?? '') === 'add' && ($m['price_adjustment'] ?? 0) > 0
                                ? '+₱' . number_format($m['price_adjustment'], 2)
                                : null,
                        ])
                        ->toArray(),
                ])->toArray(),
            ];
        }

        return response()->json(['jobs' => $result]);
    }

    /**
     * POST /api/print-server/mark-printed/{id}
     * Marks a print job as done
     */
    public function markPrinted($id)
    {
        $updated = DB::table('kitchen_print_jobs')
            ->where('id', $id)
            ->where('printed', false)
            ->update(['printed' => true, 'printed_at' => now()]);

        return response()->json(['success' => (bool) $updated]);
    }
}
