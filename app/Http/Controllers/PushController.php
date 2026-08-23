<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use App\Services\WebPushService;
use Illuminate\Http\Request;

class PushController extends Controller
{
    /**
     * Store or update a push subscription
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint'  => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth'   => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint_hash' => hash('sha256', $request->endpoint)],
            [
                'user_id'    => auth()->id(),
                'endpoint'   => $request->endpoint,
                'p256dh_key' => $request->input('keys.p256dh'),
                'auth_token' => $request->input('keys.auth'),
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Remove a push subscription
     */
    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);

        PushSubscription::where('endpoint_hash', hash('sha256', $request->endpoint))->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Get VAPID public key for frontend
     */
    public function vapidKey()
    {
        return response()->json([
            'vapid_public_key' => config('services.vapid.public_key'),
        ]);
    }
}
