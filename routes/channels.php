<?php

use Illuminate\Support\Facades\Broadcast;

// Public shop status channel — all visitors
Broadcast::channel('shop.status', function () {
    return true;
});

// Customer's own orders channel — only the order owner can listen
Broadcast::channel('orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Kitchen channel — chefs, admins, and waiters (waiter dashboard listens here
// for real-time "ready to serve" / status updates)
Broadcast::channel('kitchen', function ($user) {
    return $user->isChef() || $user->isAdmin() || $user->isWaiter();
});

// Admin orders channel — admins only
Broadcast::channel('admin.orders', function ($user) {
    return $user->isAdmin();
});

// Admin riders channel — admins only
Broadcast::channel('admin.riders', function ($user) {
    return $user->isAdmin();
});
