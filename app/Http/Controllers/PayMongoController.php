<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoController extends Controller
{
    private function secretKey(): string
    {
        return config('services.paymongo.secret_key', '');
    }

    private function authHeader(): string
    {
        return 'Basic ' . base64_encode($this->secretKey() . ':');
    }

    // ── Create a PayMongo payment link ───────────────────────────────────────
    public function createPaymentLink(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::with('items')->find($request->order_id);

        // Only allow the order owner
        if ($order->user_id !== auth()->id()) abort(403);

        if (!$this->secretKey()) {
            return response()->json([
                'success' => false,
                'message' => 'Online payment is not configured yet.',
            ], 422);
        }

        try {
            $res = Http::withHeaders([
                'Authorization' => $this->authHeader(),
                'Content-Type'  => 'application/json',
            ])->post('https://api.paymongo.com/v1/links', [
                'data' => [
                    'attributes' => [
                        'amount'      => (int) ($order->total * 100), // centavos
                        'description' => "EUT Order #{$order->order_number}",
                        'remarks'     => "Order #{$order->order_number} · " . ucfirst(str_replace('_', ' ', $order->order_type)),
                    ],
                ],
            ]);

            if (!$res->successful()) {
                Log::error('[PayMongo] createLink failed: ' . $res->body());
                return response()->json(['success' => false, 'message' => 'Payment creation failed. Please try again.'], 422);
            }

            $link = $res->json('data');

            // Store reference on order
            $order->updateQuietly([
                'payment_reference' => $link['id'],
            ]);

            return response()->json([
                'success'      => true,
                'payment_url'  => $link['attributes']['checkout_url'],
                'reference_id' => $link['id'],
            ]);
        } catch (\Throwable $e) {
            Log::error('[PayMongo] Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Payment error. Please try again.'], 500);
        }
    }

    // ── Webhook: PayMongo calls this on payment success ─────────────────────
    public function webhook(Request $request)
    {
        $payload   = $request->getContent();
        $signature = $request->header('Paymongo-Signature');
        $secret    = config('services.paymongo.webhook_secret', '');

        // Verify webhook signature
        if ($secret && $signature) {
            $parts     = [];
            foreach (explode(',', $signature) as $part) {
                [$k, $v] = explode('=', $part, 2);
                $parts[$k] = $v;
            }
            $timestamp = $parts['t'] ?? '';
            $hmac      = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
            if (!hash_equals($hmac, $parts['te'] ?? '')) {
                Log::warning('[PayMongo] Invalid webhook signature');
                return response('Unauthorized', 401);
            }
        }

        $event     = json_decode($payload, true);
        $eventType = $event['data']['attributes']['type'] ?? '';

        Log::info('[PayMongo] Webhook received: ' . $eventType);

        if (in_array($eventType, ['payment.paid', 'link.payment.paid'])) {
            $resource = $event['data']['attributes']['data'] ?? null;
            if (!$resource) return response('ok', 200);

            // Try to find order by reference_id on the payment link
            $referenceId = $resource['attributes']['payment_intent_id']
                        ?? $resource['attributes']['source']['id']
                        ?? null;

            // Try payment description to extract order number
            $description = $resource['attributes']['description'] ?? '';
            preg_match('/Order #(\w+)/', $description, $matches);
            $orderNumber = $matches[1] ?? null;

            $order = null;
            if ($orderNumber) {
                $order = Order::where('order_number', $orderNumber)->first();
            }

            if ($order) {
                $order->updateQuietly([
                    'payment_status' => 'paid',
                ]);

                try {
                    broadcast(new OrderStatusUpdated($order));
                } catch (\Throwable $be) {
                    Log::warning('[PayMongo] Broadcast failed: ' . $be->getMessage());
                }

                Log::info('[PayMongo] Order paid: ' . $order->order_number);
            }
        }

        return response('ok', 200);
    }
}
