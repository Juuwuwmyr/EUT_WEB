<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;

// -------------------------------------------------------
// Shop pages
// -------------------------------------------------------
Route::get('/shop', [ShopController::class, 'index'])->name('shop.home');
Route::get('/shop/product/{id}', [ShopController::class, 'product'])->name('shop.product');
Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
Route::get('/shop/tracking', [ShopController::class, 'tracking'])->name('shop.tracking');
Route::get('/shop/profile', [ShopController::class, 'profile'])->name('shop.profile');

// -------------------------------------------------------
// Public pages
// -------------------------------------------------------
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isRider()) return redirect()->route('rider.dashboard');
        return redirect()->route('shop.home');
    }
    return view('landing');
})->name('home');

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/restaurant', function () {
    return view('restaurant');
})->name('restaurant');

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
});

// -------------------------------------------------------
// Orders (authenticated customers)
// -------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::post('/orders',                  [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders',                   [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',           [\App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel',   [\App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::patch('/orders/{order}/set-coords', [\App\Http\Controllers\OrderController::class, 'setCoords'])->name('orders.set-coords');

    // Saved addresses
    Route::get   ('/addresses',                      [\App\Http\Controllers\AddressController::class, 'index'])->name('addresses.index');
    Route::post  ('/addresses',                      [\App\Http\Controllers\AddressController::class, 'store'])->name('addresses.store');
    Route::put   ('/addresses/{address}',            [\App\Http\Controllers\AddressController::class, 'update'])->name('addresses.update');
    Route::patch ('/addresses/{address}/default',    [\App\Http\Controllers\AddressController::class, 'setDefault'])->name('addresses.default');
    Route::delete('/addresses/{address}',            [\App\Http\Controllers\AddressController::class, 'destroy'])->name('addresses.destroy');
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
    Route::post('/orders/{order}/accept',           [AdminController::class, 'acceptOrder'])->name('orders.accept');
    Route::post('/orders/{order}/assign-rider',     [AdminController::class, 'assignRider'])->name('orders.assign-rider');
    Route::patch('/orders/{order}/status',          [AdminController::class, 'updateOrderStatus'])->name('orders.status');
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
});
