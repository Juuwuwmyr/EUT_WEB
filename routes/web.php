<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShopController;

// -------------------------------------------------------
// Shop pages
// -------------------------------------------------------
Route::get('/shop', [ShopController::class, 'index'])->name('shop.home');
Route::get('/shop/product/{id}', [ShopController::class, 'product'])->name('shop.product');
Route::get('/shop/cart', [ShopController::class, 'cart'])->name('shop.cart');
Route::get('/shop/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
Route::get('/shop/tracking', [ShopController::class, 'tracking'])->name('shop.tracking');

// -------------------------------------------------------
// Public pages
// -------------------------------------------------------
Route::get('/', function () {
    return view('restaurant');
})->name('home');

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/example', function () {
    return view('example');
});

Route::get('/menu-pdf', function () {
    return view('menu-pdf');
});

// -------------------------------------------------------
// Auth — Email login & signup (JSON responses for modal)
// -------------------------------------------------------
Route::post('/auth/login',  [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/signup', [AuthController::class, 'signup'])->name('auth.signup');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout')->middleware('auth');

// -------------------------------------------------------
// Auth — Google OAuth
// -------------------------------------------------------
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
