<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController; // Import Controller Toko
use App\Http\Controllers\CartController; // Import Controller Keranjang
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;

// --- 1. HALAMAN PUBLIK (Bisa diakses siapa saja) ---

// Halaman Utama Toko (Ganti view welcome bawaan jadi HomeController)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman Detail Produk
Route::get('/product/{slug}', [HomeController::class, 'show'])->name('product.detail');

// Halaman Keranjang & Kelola Keranjang
Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/add/{id}', 'addToCart')->name('add');
    Route::delete('/remove', 'remove')->name('remove');
    Route::post('/update-quantity', 'updateQuantity')->name('updateQuantity');
    Route::post('/update-package', 'updatePackage')->name('updatePackage');
    Route::post('/apply-promo', 'applyPromo')->name('applyPromo');
    Route::post('/remove-promo', 'removePromo')->name('removePromo');
});

// Route Penyelamat (Redirect jika user akses /checkout via GET)
Route::get('checkout', function() {
    return redirect()->route('cart.index')->with('error', 'Silakan pilih barang dulu.');
});

// --- OAuth Social Login ---
Route::controller(SocialAuthController::class)->prefix('oauth')->name('oauth.')->group(function () {
    Route::get('/{provider}', 'redirect')->name('redirect');
    Route::get('/{provider}/callback', 'callback')->name('callback');
    Route::get('/{provider}/debug', 'debug')->name('debug');
});


// --- 2. HALAMAN DASHBOARD USER (Bawaan Breeze) ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// --- 3. HALAMAN KHUSUS MEMBER (Wajib Login) ---
Route::middleware('auth')->group(function () {
    
    Route::controller(CartController::class)->group(function () {
        // Fitur Toko yang butuh Login (Checkout, Payment, History)
        Route::post('checkout', 'viewCheckout')->name('cart.checkout');
        Route::post('payment', 'processPayment')->name('cart.payment');
        
        // Custom payment method selection
        Route::get('payment-method/{order}', 'selectPaymentMethod')->name('payment.select');
        Route::post('payment-method/{order}', 'processPaymentMethod')->name('payment.process');
        Route::get('payment-cancel/{order}', 'cancelPayment')->name('payment.cancel');
        
        Route::get('history', 'history')->name('history');
        Route::get('history/{id}', 'historyDetail')->name('history.detail');
        
        // Payment simulation for testing (development only)
        Route::get('/simulate-payment/{order}', 'simulatePayment')->name('simulate.payment');
    });

    // Fitur Profile Bawaan Breeze
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });
});

// Import Route Auth (Login, Register, Logout)
require __DIR__.'/auth.php';

// --- 4. API endpoints (for Next.js frontend) ---
Route::prefix('api')->group(function () {
    // Auth
    Route::post('/auth/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
    Route::post('/auth/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
        Route::post('/auth/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    });

    // Products
    Route::get('/products', [\App\Http\Controllers\Api\ProductController::class, 'index']);
    Route::get('/products/{product}', [\App\Http\Controllers\Api\ProductController::class, 'show']);
});