<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TableQrController;

// -------------------------------------------------------
// Shop pages
// -------------------------------------------------------
Route::get('/shop', [ShopController::class, 'index'])->name('shop.home');
Route::get('/shop/product/{id}', [ShopController::class, 'product'])->name('shop.product');
Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
// Alias: legacy QR codes point to /checkout?table=N — send to shop menu first (no-cache so browsers never cache this)
Route::get('/checkout', fn() => redirect('/shop?' . request()->getQueryString(), 302)
    ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
    ->header('Pragma', 'no-cache'));
Route::get('/shop/tracking', [ShopController::class, 'tracking'])->name('shop.tracking');
Route::get('/shop/profile', [ShopController::class, 'profile'])->name('shop.profile');

// Public delivery fee calculator (used by checkout before login)
Route::get('/delivery-fee', [\App\Http\Controllers\OrderController::class, 'calcFee'])->name('delivery-fee');

// -------------------------------------------------------
// Public pages
// -------------------------------------------------------
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isRider()) return redirect()->route('rider.dashboard');
        if ($user->isChef())  return redirect()->route('chef.dashboard');
        if ($user->provider === 'email' && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
        return redirect()->route('shop.home');
    }
    if (\App\Services\PendingSignup::has()) {
        return redirect()->route('verification.notice');
    }
    return view('landing');
})->name('home');

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/restaurant', function () {
    $featuredItems = \App\Models\MenuItem::where('is_archived', false)
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->inRandomOrder()
        ->limit(8)
        ->get();
    return view('restaurant', compact('featuredItems'));
})->name('restaurant');

// Health check for AWS ALB/ELB
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'app' => config('app.name')], 200);
})->name('health');

Route::get('/example', function () {
    return view('example');
});

Route::get('/menu-pdf', function () {
    return view('menu-pdf');
});

// -------------------------------------------------------
// Waiter panel
// -------------------------------------------------------
Route::prefix('waiter')->name('waiter.')->middleware(['auth', 'waiter'])->group(function () {
    Route::get('/dashboard',                     [\App\Http\Controllers\WaiterController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders',                        [\App\Http\Controllers\WaiterController::class, 'getOrders'])->name('orders');
    Route::post('/orders/{order}/serve',         [\App\Http\Controllers\WaiterController::class, 'serveOrder'])->name('orders.serve');
    Route::post('/orders/{order}/request-bill',  [\App\Http\Controllers\WaiterController::class, 'requestBill'])->name('orders.request-bill');
    // Waiter ordering page — selects table from dropdown
    Route::get('/order',                         [\App\Http\Controllers\WaiterController::class, 'orderPage'])->name('order');
    // Table receipt — reuses the chef's read-only receipt view (waiters need this to print bills)
    Route::get('/orders/{order}/table-receipt.html', [\App\Http\Controllers\ChefController::class, 'tableReceipt'])->name('orders.table-receipt');
});

// -------------------------------------------------------
// Rider panel
// -------------------------------------------------------
Route::prefix('rider')->name('rider.')->middleware(['auth', 'rider'])->group(function () {
    Route::get('/dashboard',                        [\App\Http\Controllers\RiderController::class, 'dashboard'])->middleware('permission:view_deliveries')->name('dashboard');
    Route::patch('/status',                         [\App\Http\Controllers\RiderController::class, 'updateStatus'])->middleware('permission:update_delivery_status')->name('status');
    Route::patch('/location',                       [\App\Http\Controllers\RiderController::class, 'updateLocation'])->middleware('permission:update_location')->name('location');
    Route::get('/orders',                           [\App\Http\Controllers\RiderController::class, 'orders'])->middleware('permission:view_deliveries')->name('orders');
    Route::post('/orders/{order}/picked-up',        [\App\Http\Controllers\RiderController::class, 'pickedUp'])->middleware('permission:update_delivery_status')->name('orders.picked-up');
    Route::post('/orders/{order}/delivered',        [\App\Http\Controllers\RiderController::class, 'delivered'])->middleware('permission:mark_delivered')->name('orders.delivered');
    Route::get('/earnings',                         [\App\Http\Controllers\RiderController::class, 'earnings'])->middleware('permission:view_deliveries')->name('earnings');
    Route::get('/pickups/pending',                  [\App\Http\Controllers\RiderController::class, 'pendingPickups'])->middleware('permission:view_deliveries')->name('pickups.pending');
    Route::post('/pickups/{order}/mark-printed',    [\App\Http\Controllers\RiderController::class, 'markPickupPrinted'])->middleware('permission:update_delivery_status')->name('pickups.mark-printed');
});

// -------------------------------------------------------
// Chef / Kitchen panel
// -------------------------------------------------------
Route::prefix('chef')->name('chef.')->middleware(['auth', 'chef'])->group(function () {
    // ChefMiddleware already guarantees role=chef|admin — no permission gate needed on the entry points
    Route::get('/dashboard',                            [\App\Http\Controllers\ChefController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders',                               [\App\Http\Controllers\ChefController::class, 'getOrders'])->name('orders');
    Route::post('/orders/table-session/{key}/ready',    [\App\Http\Controllers\ChefController::class, 'markTableReady'])->middleware('permission:mark_order_ready')->name('orders.table-ready');
    Route::post('/orders/{order}/accept',               [\App\Http\Controllers\ChefController::class, 'acceptOrder'])->middleware('permission:accept_orders')->name('orders.accept');
    Route::post('/orders/{order}/start',                [\App\Http\Controllers\ChefController::class, 'startCooking'])->middleware('permission:start_cooking')->name('orders.start');
    Route::post('/orders/{order}/ready',                [\App\Http\Controllers\ChefController::class, 'markReady'])->middleware('permission:mark_order_ready')->name('orders.ready');
    Route::post('/orders/{order}/assign-rider',         [\App\Http\Controllers\ChefController::class, 'assignRider'])->middleware('permission:assign_riders')->name('orders.assign-rider');
    Route::delete('/orders/{order}/items/{item}',       [\App\Http\Controllers\ChefController::class, 'cancelItem'])->middleware('permission:cancel_order_items')->name('orders.cancel-item');
    Route::get('/orders/{order}/receipt',               [\App\Http\Controllers\ChefController::class, 'receipt'])->middleware('permission:print_receipts')->name('orders.receipt');
    Route::get('/orders/{order}/receipt.html',          [\App\Http\Controllers\ChefController::class, 'receipt'])->middleware('permission:print_receipts');
    Route::get('/orders/{order}/table-receipt',         [\App\Http\Controllers\ChefController::class, 'tableReceipt'])->middleware('permission:print_receipts')->name('orders.table-receipt');
    Route::get('/orders/{order}/table-receipt.html',    [\App\Http\Controllers\ChefController::class, 'tableReceipt'])->middleware('permission:print_receipts');
    Route::get('/orders/table-bill/{table}',            [\App\Http\Controllers\ChefController::class, 'tableReceiptByNumber'])->middleware('permission:print_receipts')->name('orders.table-bill');
    Route::get('/orders/{order}/kitchen-ticket',        [\App\Http\Controllers\ChefController::class, 'kitchenTicket'])->middleware('permission:print_receipts')->name('orders.kitchen-ticket');
    Route::get('/orders/{order}/kitchen-ticket.html',   [\App\Http\Controllers\ChefController::class, 'kitchenTicket'])->middleware('permission:print_receipts');
    Route::get('/orders/{order}/session-ticket',        [\App\Http\Controllers\ChefController::class, 'sessionKitchenTicket'])->middleware('permission:print_receipts')->name('orders.session-ticket');
    Route::get('/orders/{order}/takeout-slip',          [\App\Http\Controllers\ChefController::class, 'takeoutSlip'])->middleware('permission:print_receipts')->name('orders.takeout-slip');
    Route::get('/orders/{order}/takeout-slip.html',     [\App\Http\Controllers\ChefController::class, 'takeoutSlip'])->middleware('permission:print_receipts');
});

// -------------------------------------------------------
// Orders (authenticated customers)
// -------------------------------------------------------
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/table/{table}', [\App\Http\Controllers\OrderController::class, 'tableStatus'])->name('orders.table-status');

Route::middleware('auth')->group(function () {
    Route::get   ('/cart/sync',            [\App\Http\Controllers\CartController::class, 'index'])->name('cart.sync');
    Route::post  ('/cart/sync',            [\App\Http\Controllers\CartController::class, 'bulkSync'])->name('cart.bulk-sync');
    Route::post  ('/cart/item',            [\App\Http\Controllers\CartController::class, 'upsertItem'])->name('cart.upsert');
    Route::patch ('/cart/item/{cartKey}',  [\App\Http\Controllers\CartController::class, 'updateQty'])->name('cart.update-qty');
    Route::delete('/cart/item/{cartKey}',  [\App\Http\Controllers\CartController::class, 'removeItem'])->name('cart.remove');
    Route::delete('/cart',                 [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

    Route::get('/orders',                   [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{order}/cancel',   [\App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');

    Route::post ('/profile',          [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post ('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    Route::get   ('/addresses',                      [\App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
    Route::post  ('/addresses',                      [\App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::put   ('/addresses/{address}',            [\App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::patch ('/addresses/{address}/default',    [\App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.default');
    Route::delete('/addresses/{address}',            [\App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
});

// -------------------------------------------------------
// Print Server API
// -------------------------------------------------------
Route::prefix('api/print-server')->middleware(['auth.printserver'])->group(function () {
    Route::get('/pending-prints', [\App\Http\Controllers\PrintServerController::class, 'pendingPrints']);
    Route::post('/mark-printed/{id}', [\App\Http\Controllers\PrintServerController::class, 'markPrinted']);
});

Route::post('/auth/login',  [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/signup', [AuthController::class, 'signup'])->middleware('throttle:5,1')->name('auth.signup');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

// ── Push Notifications ──────────────────────────────────────────────────────
Route::get('/push/vapid-key',   [\App\Http\Controllers\PushController::class, 'vapidKey'])->name('push.vapid-key');
Route::post('/push/subscribe',  [\App\Http\Controllers\PushController::class, 'subscribe'])->middleware('auth')->name('push.subscribe');
Route::post('/push/unsubscribe',[\App\Http\Controllers\PushController::class, 'unsubscribe'])->middleware('auth')->name('push.unsubscribe');

// ── Forgot Password (OTP flow) ──────────────────────────────
Route::prefix('auth/forgot-password')->name('password.')->middleware('throttle:10,1')->group(function () {
    Route::post('/send-code',   [\App\Http\Controllers\ForgotPasswordController::class, 'sendCode'])->name('send-code');
    Route::post('/verify-code', [\App\Http\Controllers\ForgotPasswordController::class, 'verifyCode'])->name('verify-code');
    Route::post('/reset',       [\App\Http\Controllers\ForgotPasswordController::class, 'resetPassword'])->name('reset');
    Route::post('/cooldown',    [\App\Http\Controllers\ForgotPasswordController::class, 'cooldown'])->name('cooldown');
});

Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Email verification routes
Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
Route::post('/email/verify', [AuthController::class, 'verifyEmailCode'])->middleware(['throttle:10,1'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->middleware(['throttle:6,1'])->name('verification.send');
Route::post('/email/verify/cancel', [AuthController::class, 'cancelPendingSignup'])->name('verification.cancel');

// -------------------------------------------------------
// Admin panel — protected by auth + admin middleware
// -------------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'admin.sync-verify'])->group(function () {

    Route::get('/', [AdminController::class, 'dashboard'])->middleware('permission:view_dashboard')->name('dashboard');

    // ── Users ──────────────────────────────────────────────
    Route::get   ('/users',                  [AdminController::class, 'users'])->middleware('permission:view_users')->name('users');
    Route::post  ('/users',                  [AdminController::class, 'storeUser'])->middleware('permission:create_users')->name('users.store');
    Route::put   ('/users/{user}',           [AdminController::class, 'updateUser'])->middleware('permission:edit_users')->name('users.update');
    Route::patch ('/users/{user}/role',      [AdminController::class, 'updateUserRole'])->middleware('permission:change_user_roles')->name('users.role');
    Route::patch ('/users/{user}/archive',   [AdminController::class, 'archiveUser'])->middleware('permission:delete_users')->name('users.archive');
    Route::delete('/users/{user}',           [AdminController::class, 'deleteUser'])->middleware('permission:delete_users')->name('users.delete');

    // ── Credential re-verification (verify page itself is exempt) ─────
    Route::get ('/verify', [AdminController::class, 'showVerify'])->name('verify');
    Route::post('/verify', [AdminController::class, 'submitVerify'])->name('verify.submit');

    // ── Categories — separate password verification ────────────────────
    Route::middleware('admin.verify:categories')->group(function () {
        Route::get   ('/categories',                      [AdminController::class, 'categories'])->middleware('permission:view_categories')->name('categories');
        Route::post  ('/categories',                      [AdminController::class, 'storeCategory'])->middleware('permission:create_categories')->name('categories.store');
        Route::put   ('/categories/{category}',           [AdminController::class, 'updateCategory'])->middleware('permission:edit_categories')->name('categories.update');
        Route::patch ('/categories/{category}/archive',   [AdminController::class, 'archiveCategory'])->middleware('permission:delete_categories')->name('categories.archive');
        Route::delete('/categories/{category}',           [AdminController::class, 'deleteCategory'])->middleware('permission:delete_categories')->name('categories.delete');
    });

    // ── Menu Items — separate password verification ────────────────────
    Route::middleware('admin.verify:menu')->group(function () {
        Route::get   ('/menu-items',                              [AdminController::class, 'menuItems'])->middleware('permission:view_menu_items')->name('menu-items');
        Route::post  ('/menu-items',                              [AdminController::class, 'storeMenuItem'])->middleware('permission:create_menu_items')->name('menu-items.store');
        Route::put   ('/menu-items/{menuItem}',                   [AdminController::class, 'updateMenuItem'])->middleware('permission:edit_menu_items')->name('menu-items.update');
        Route::patch ('/menu-items/{menuItem}/archive',          [AdminController::class, 'archiveMenuItem'])->middleware('permission:delete_menu_items')->name('menu-items.archive');
        Route::delete('/menu-items/{menuItem}',                   [AdminController::class, 'deleteMenuItem'])->middleware('permission:delete_menu_items')->name('menu-items.delete');

        Route::post  ('/menu-items/{menuItem}/modifier-groups',         [AdminController::class, 'storeModifierGroup'])->middleware('permission:manage_modifiers')->name('modifier-groups.store');
        Route::put   ('/menu-items/{menuItem}/modifier-groups/{group}', [AdminController::class, 'updateModifierGroup'])->middleware('permission:manage_modifiers')->name('modifier-groups.update');
        Route::delete('/menu-items/{menuItem}/modifier-groups/{group}', [AdminController::class, 'deleteModifierGroup'])->middleware('permission:manage_modifiers')->name('modifier-groups.delete');

        Route::post  ('/modifier-groups/{group}/options',          [AdminController::class, 'storeModifierOption'])->middleware('permission:manage_modifiers')->name('modifier-options.store');
        Route::put   ('/modifier-groups/{group}/options/{option}', [AdminController::class, 'updateModifierOption'])->middleware('permission:manage_modifiers')->name('modifier-options.update');
        Route::delete('/modifier-groups/{group}/options/{option}', [AdminController::class, 'deleteModifierOption'])->middleware('permission:manage_modifiers')->name('modifier-options.delete');
    });

    // ── Orders ─────────────────────────────────────────────
    Route::get('/orders',                           [AdminController::class, 'orders'])->middleware('permission:view_orders')->name('orders');
    Route::get('/orders/poll',                      [AdminController::class, 'ordersPoll'])->middleware('permission:view_orders')->name('orders.poll');
    Route::post('/orders/{order}/accept',           [AdminController::class, 'acceptOrder'])->middleware('permission:accept_orders')->name('orders.accept');
    Route::post('/orders/{order}/assign-rider',     [AdminController::class, 'assignRider'])->middleware('permission:assign_riders')->name('orders.assign-rider');
    Route::post('/orders/{order}/ready',            [\App\Http\Controllers\ChefController::class, 'markReady'])->middleware('permission:mark_order_ready')->name('orders.ready');
    Route::patch('/orders/{order}/items/{item}',    [AdminController::class, 'updateItemQty'])->middleware('permission:cancel_order_items')->name('orders.update-item');
    Route::delete('/orders/{order}/items/{item}',   [AdminController::class, 'cancelItem'])->middleware('permission:cancel_order_items')->name('orders.cancel-item');
    Route::patch('/orders/{order}/status',          [AdminController::class, 'updateOrderStatus'])->middleware('permission:update_order_status')->name('orders.status');
    Route::post('/orders/{order}/complete-table',   [AdminController::class, 'completeTable'])->middleware('permission:complete_table_orders')->name('orders.complete-table');
    Route::post('/orders/{order}/lock-table',       [AdminController::class, 'lockTable'])->middleware('permission:complete_table_orders')->name('orders.lock-table');
    Route::patch('/orders/bulk-archive',            [AdminController::class, 'bulkArchiveOrders'])->middleware('permission:cancel_orders')->name('orders.bulk-archive');
    Route::patch('/orders/{order}/archive',         [AdminController::class, 'archiveOrder'])->middleware('permission:cancel_orders')->name('orders.archive');    Route::delete('/orders/{order}',                [AdminController::class, 'deleteOrder'])->middleware('permission:delete_orders')->name('orders.delete');
    Route::get('/orders/{order}/takeout-slip',      [\App\Http\Controllers\ChefController::class, 'takeoutSlip'])->middleware('permission:print_receipts')->name('orders.takeout-slip');
    Route::get('/riders/locations',                 [AdminController::class, 'riderLocations'])->middleware('permission:view_rider_locations')->name('riders.locations');

    // ── Riders ─────────────────────────────────────────────
    Route::get('/riders',                           [AdminController::class, 'riders'])->middleware('permission:view_riders')->name('riders');
    Route::post('/riders',                          [AdminController::class, 'storeRider'])->middleware('permission:create_riders')->name('riders.store');
    Route::patch('/riders/{rider}',                 [AdminController::class, 'updateRider'])->middleware('permission:edit_riders')->name('riders.update');
    Route::delete('/riders/{rider}',                [AdminController::class, 'removeRider'])->middleware('permission:delete_riders')->name('riders.destroy');

    // ── Settings ───────────────────────────────────────────
    Route::get ('/settings',              [AdminController::class, 'settings'])->middleware('permission:view_settings')->name('settings');
    Route::post('/settings',              [AdminController::class, 'updateSettings'])->middleware('permission:edit_settings')->name('settings.update');
    Route::post('/settings/password',     [AdminController::class, 'updatePassword'])->name('settings.password');
    Route::patch('/settings/toggle-open', [AdminController::class, 'toggleOpen'])->middleware('permission:toggle_shop_status')->name('settings.toggle-open');

    // ── Table QR Codes ─────────────────────────────────────
    Route::get('/table-qrcodes',       [TableQrController::class, 'index'])->middleware('permission:view_qr_codes')->name('table-qrcodes');
    Route::get('/table-qrcodes/print', [TableQrController::class, 'print'])->middleware('permission:generate_qr_codes')->name('table-qrcodes.print');
    Route::get('/table-qrcodes/coupon',[TableQrController::class, 'coupon'])->middleware('permission:generate_qr_codes')->name('table-qrcodes.coupon');

    // ── Audit Logs ─────────────────────────────────────────
    Route::get('/audit-logs',      [AdminController::class, 'auditLogs'])->middleware('permission:view_audit_logs')->name('audit-logs');
    Route::get('/audit-logs/poll', [AdminController::class, 'auditLogsPoll'])->middleware('permission:view_audit_logs')->name('audit-logs.poll');

    // ── Permissions & RBAC ─────────────────────────────────
    Route::prefix('permissions')->name('permissions.')->middleware('permission:view_permissions')->group(function () {
        Route::get('/',                                    [\App\Http\Controllers\PermissionController::class, 'index'])->name('index');
        Route::post('/',                                   [\App\Http\Controllers\PermissionController::class, 'store'])->middleware('permission:manage_permissions')->name('store');
        Route::put('/{permission}',                        [\App\Http\Controllers\PermissionController::class, 'update'])->middleware('permission:manage_permissions')->name('update');
        Route::delete('/{permission}',                     [\App\Http\Controllers\PermissionController::class, 'destroy'])->middleware('permission:manage_permissions')->name('destroy');
        Route::patch('/{permission}/toggle-active',        [\App\Http\Controllers\PermissionController::class, 'toggleActive'])->middleware('permission:manage_permissions')->name('toggle-active');
        Route::post('/role-permissions',                   [\App\Http\Controllers\PermissionController::class, 'updateRolePermissions'])->middleware('permission:manage_role_permissions')->name('role-update');
        Route::post('/generate-slug',                      [\App\Http\Controllers\PermissionController::class, 'generateSlug'])->middleware('permission:manage_permissions')->name('generate-slug');
        
        // User-specific permissions
        Route::get('/users/{user}',                        [\App\Http\Controllers\PermissionController::class, 'userPermissions'])->middleware('permission:manage_user_permissions')->name('user.show');
        Route::post('/users/{user}',                       [\App\Http\Controllers\PermissionController::class, 'updateUserPermissions'])->middleware('permission:manage_user_permissions')->name('user.update');
        Route::post('/users/{user}/grant',                 [\App\Http\Controllers\PermissionController::class, 'grantUserPermission'])->middleware('permission:manage_user_permissions')->name('user.grant');
        Route::post('/users/{user}/revoke',                [\App\Http\Controllers\PermissionController::class, 'revokeUserPermission'])->middleware('permission:manage_user_permissions')->name('user.revoke');
        Route::post('/users/{user}/reset',                 [\App\Http\Controllers\PermissionController::class, 'resetUserPermission'])->middleware('permission:manage_user_permissions')->name('user.reset');
    });
});
