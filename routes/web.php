<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController; // Import Controller Toko
use App\Http\Controllers\CartController; // Import Controller Keranjang
use Illuminate\Support\Facades\Route;

// --- 1. HALAMAN PUBLIK (Bisa diakses siapa saja) ---

// Halaman Utama Toko (Ganti view welcome bawaan jadi HomeController)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman Detail Produk
Route::get('/product/{slug}', [HomeController::class, 'show'])->name('product.detail');

// Halaman Keranjang & Kelola Keranjang
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::post('cart/apply-promo', [CartController::class, 'applyPromo'])->name('cart.applyPromo');
Route::post('cart/remove-promo', [CartController::class, 'removePromo'])->name('cart.removePromo');

// Route Penyelamat (Redirect jika user akses /checkout via GET)
Route::get('checkout', function() {
    return redirect()->route('cart.index')->with('error', 'Silakan pilih barang dulu.');
});


// --- 2. HALAMAN DASHBOARD USER (Bawaan Breeze) ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// --- 3. HALAMAN KHUSUS MEMBER (Wajib Login) ---
Route::middleware('auth')->group(function () {
    
    // Fitur Toko yang butuh Login (Checkout, Payment, History)
    Route::post('checkout', [CartController::class, 'viewCheckout'])->name('cart.checkout');
    Route::post('payment', [CartController::class, 'processPayment'])->name('cart.payment');
    Route::get('history', [CartController::class, 'history'])->name('history');
    Route::get('history/{id}', [CartController::class, 'historyDetail'])->name('history.detail');

    // Fitur Profile Bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Import Route Auth (Login, Register, Logout)
require __DIR__.'/auth.php';