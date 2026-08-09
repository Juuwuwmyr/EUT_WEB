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
        return redirect()->route('shop.home');
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
// Rider panel
// -------------------------------------------------------
Route::prefix('rider')->name('rider.')->middleware(['auth', 'rider'])->group(function () {
    Route::get('/dashboard',                        [\App\Http\Controllers\RiderController::class, 'dashboard'])->name('dashboard');
    Route::patch('/status',                         [\App\Http\Controllers\RiderController::class, 'updateStatus'])->name('status');
    Route::patch('/location',                       [\App\Http\Controllers\RiderController::class, 'updateLocation'])->name('location');
    Route::get('/orders',                           [\App\Http\Controllers\RiderController::class, 'orders'])->name('orders');
    Route::post('/orders/{order}/picked-up',        [\App\Http\Controllers\RiderController::class, 'pickedUp'])->name('orders.picked-up');
    Route::post('/orders/{order}/delivered',        [\App\Http\Controllers\RiderController::class, 'delivered'])->name('orders.delivered');
    Route::get('/earnings',                         [\App\Http\Controllers\RiderController::class, 'earnings'])->name('earnings');
    Route::get('/pickups/pending',                  [\App\Http\Controllers\RiderController::class, 'pendingPickups'])->name('pickups.pending');
    Route::post('/pickups/{order}/mark-printed',    [\App\Http\Controllers\RiderController::class, 'markPickupPrinted'])->name('pickups.mark-printed');
});

// -------------------------------------------------------
// Chef / Kitchen panel
// -------------------------------------------------------
Route::prefix('chef')->name('chef.')->middleware(['auth', 'chef'])->group(function () {
    Route::get('/dashboard',                            [\App\Http\Controllers\ChefController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders',                               [\App\Http\Controllers\ChefController::class, 'getOrders'])->name('orders');
    Route::post('/orders/{order}/accept',               [\App\Http\Controllers\ChefController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{order}/start',                [\App\Http\Controllers\ChefController::class, 'startCooking'])->name('orders.start');
    Route::post('/orders/{order}/ready',                [\App\Http\Controllers\ChefController::class, 'markReady'])->name('orders.ready');
    Route::post('/orders/{order}/assign-rider',         [\App\Http\Controllers\ChefController::class, 'assignRider'])->name('orders.assign-rider');
    Route::delete('/orders/{order}/items/{item}',       [\App\Http\Controllers\ChefController::class, 'cancelItem'])->name('orders.cancel-item');
    Route::get('/orders/{order}/receipt',               [\App\Http\Controllers\ChefController::class, 'receipt'])->name('orders.receipt');
    Route::get('/orders/{order}/receipt.html',          [\App\Http\Controllers\ChefController::class, 'receipt']);
    Route::get('/orders/{order}/table-receipt',         [\App\Http\Controllers\ChefController::class, 'tableReceipt'])->name('orders.table-receipt');
    Route::get('/orders/{order}/table-receipt.html',    [\App\Http\Controllers\ChefController::class, 'tableReceipt']);
    Route::get('/orders/{order}/kitchen-ticket',        [\App\Http\Controllers\ChefController::class, 'kitchenTicket'])->name('orders.kitchen-ticket');
    Route::get('/orders/{order}/kitchen-ticket.html',   [\App\Http\Controllers\ChefController::class, 'kitchenTicket']);
    Route::get('/orders/{order}/takeout-slip',          [\App\Http\Controllers\ChefController::class, 'takeoutSlip'])->name('orders.takeout-slip');
    Route::get('/orders/{order}/takeout-slip.html',     [\App\Http\Controllers\ChefController::class, 'takeoutSlip']);
});

// -------------------------------------------------------
// Orders (authenticated customers)
// -------------------------------------------------------
// Orders — store is public (guests can place dine-in orders)
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');

Route::middleware('auth')->group(function () {
    // -------------------------------------------------------
    // Cart sync (server-side cart for logged-in users)
    // -------------------------------------------------------
    Route::get   ('/cart/sync',            [\App\Http\Controllers\CartController::class, 'index'])->name('cart.sync');
    Route::post  ('/cart/sync',            [\App\Http\Controllers\CartController::class, 'bulkSync'])->name('cart.bulk-sync');
    Route::post  ('/cart/item',            [\App\Http\Controllers\CartController::class, 'upsertItem'])->name('cart.upsert');
    Route::patch ('/cart/item/{cartKey}',  [\App\Http\Controllers\CartController::class, 'updateQty'])->name('cart.update-qty');
    Route::delete('/cart/item/{cartKey}',  [\App\Http\Controllers\CartController::class, 'removeItem'])->name('cart.remove');
    Route::delete('/cart',                 [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');

    Route::get('/orders',                   [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');

    // Profile
    Route::post ('/profile',          [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post ('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    // Saved addresses
    Route::get   ('/addresses',                      [\App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
    Route::post  ('/addresses',                      [\App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::put   ('/addresses/{address}',            [\App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::patch ('/addresses/{address}/default',    [\App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.default');
    Route::delete('/addresses/{address}',            [\App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
});

// -------------------------------------------------------
// Print Server API — used by kitchen print agent
// -------------------------------------------------------
Route::prefix('api/print-server')->middleware(['auth.printserver'])->group(function () {
    Route::get('/pending-prints', [\App\Http\Controllers\PrintServerController::class, 'pendingPrints']);
    Route::post('/mark-printed/{id}', [\App\Http\Controllers\PrintServerController::class, 'markPrinted']);
});
Route::post('/auth/login',  [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/signup', [AuthController::class, 'signup'])->name('auth.signup');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

// -------------------------------------------------------
// Auth — Google OAuth
// -------------------------------------------------------
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// -------------------------------------------------------
// Admin panel — protected by auth + admin middleware
// -------------------------------------------------------
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // ── Users ──────────────────────────────────────────────
    Route::get   ('/users',                  [AdminController::class, 'users'])->name('users');
    Route::post  ('/users',                  [AdminController::class, 'storeUser'])->name('users.store');
    Route::put   ('/users/{user}',           [AdminController::class, 'updateUser'])->name('users.update');
    Route::patch ('/users/{user}/role',      [AdminController::class, 'updateUserRole'])->name('users.role');
    Route::patch ('/users/{user}/archive',   [AdminController::class, 'archiveUser'])->name('users.archive');
    Route::delete('/users/{user}',           [AdminController::class, 'deleteUser'])->name('users.delete');

    // ── Categories ─────────────────────────────────────────
    Route::get   ('/categories',             [AdminController::class, 'categories'])->name('categories');
    Route::post  ('/categories',             [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put   ('/categories/{category}',  [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::patch ('/categories/{category}/archive', [AdminController::class, 'archiveCategory'])->name('categories.archive');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.delete');

    // ── Menu Items ─────────────────────────────────────────
    Route::get   ('/menu-items',             [AdminController::class, 'menuItems'])->name('menu-items');
    Route::post  ('/menu-items',             [AdminController::class, 'storeMenuItem'])->name('menu-items.store');
    Route::put   ('/menu-items/{menuItem}',  [AdminController::class, 'updateMenuItem'])->name('menu-items.update');
    Route::patch ('/menu-items/{menuItem}/archive', [AdminController::class, 'archiveMenuItem'])->name('menu-items.archive');
    Route::delete('/menu-items/{menuItem}',  [AdminController::class, 'deleteMenuItem'])->name('menu-items.delete');

    // ── Modifier Groups (Flavors / Modifiers) ──────────────
    Route::post  ('/menu-items/{menuItem}/modifier-groups',                  [AdminController::class, 'storeModifierGroup'])->name('modifier-groups.store');
    Route::put   ('/menu-items/{menuItem}/modifier-groups/{group}',          [AdminController::class, 'updateModifierGroup'])->name('modifier-groups.update');
    Route::delete('/menu-items/{menuItem}/modifier-groups/{group}',          [AdminController::class, 'deleteModifierGroup'])->name('modifier-groups.delete');

    // ── Modifier Options ───────────────────────────────────
    Route::post  ('/modifier-groups/{group}/options',          [AdminController::class, 'storeModifierOption'])->name('modifier-options.store');
    Route::put   ('/modifier-groups/{group}/options/{option}', [AdminController::class, 'updateModifierOption'])->name('modifier-options.update');
    Route::delete('/modifier-groups/{group}/options/{option}', [AdminController::class, 'deleteModifierOption'])->name('modifier-options.delete');

    // ── Orders ─────────────────────────────────────────────
    Route::get('/orders',                           [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/poll',                      [AdminController::class, 'ordersPoll'])->name('orders.poll');
    Route::post('/orders/{order}/accept',           [AdminController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{order}/assign-rider',     [AdminController::class, 'assignRider'])->name('orders.assign-rider');
    Route::patch('/orders/{order}/status',          [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::patch('/orders/{order}/archive',         [AdminController::class, 'archiveOrder'])->name('orders.archive');
    Route::delete('/orders/{order}',                [AdminController::class, 'deleteOrder'])->name('orders.delete');
    Route::get('/orders/{order}/takeout-slip',       [\App\Http\Controllers\ChefController::class, 'takeoutSlip'])->name('orders.takeout-slip');
    Route::get('/riders/locations',                 [AdminController::class, 'riderLocations'])->name('riders.locations');

    // ── Riders ─────────────────────────────────────────────
    Route::get('/riders',                           [AdminController::class, 'riders'])->name('riders');
    Route::post('/riders',                          [AdminController::class, 'storeRider'])->name('riders.store');
    Route::patch('/riders/{rider}',                 [AdminController::class, 'updateRider'])->name('riders.update');
    Route::delete('/riders/{rider}',                [AdminController::class, 'removeRider'])->name('riders.destroy');

    // ── Settings ───────────────────────────────────────────
    Route::get ('/settings',          [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings',          [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.password');
    Route::patch('/settings/toggle-open', [AdminController::class, 'toggleOpen'])->name('settings.toggle-open');

    // ── Table QR Codes ─────────────────────────────────────
    Route::get('/table-qrcodes',      [TableQrController::class, 'index'])->name('table-qrcodes');
    Route::get('/table-qrcodes/print', [TableQrController::class, 'print'])->name('table-qrcodes.print');
    Route::get('/table-qrcodes/coupon', [TableQrController::class, 'coupon'])->name('table-qrcodes.coupon');
});
