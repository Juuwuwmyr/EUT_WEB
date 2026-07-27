<?php

use Illuminate\Support\Facades\Broadcast;

// Customer's own orders channel — only the order owner can listen
Broadcast::channel('orders.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Kitchen channel — only chefs and admins
Broadcast::channel('kitchen', function ($user) {
    return $user->isChef() || $user->isAdmin();
});

// Admin orders channel — admins only
Broadcast::channel('admin.orders', function ($user) {
    return $user->isAdmin();
});

// Admin riders channel — admins only
Broadcast::channel('admin.riders', function ($user) {
    return $user->isAdmin();
});
