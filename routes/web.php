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
Route::get('/rider/dashboard', function () {
    return view('rider.dashboard');
})->name('rider.dashboard');

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
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');

    // ── Riders ─────────────────────────────────────────────
    Route::get('/riders', [AdminController::class, 'riders'])->name('riders');

    // ── Settings ───────────────────────────────────────────
    Route::get ('/settings',          [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings',          [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/password', [AdminController::class, 'updatePassword'])->name('settings.password');
});
