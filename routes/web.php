<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

// Halaman Utama & Detail Produk
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{slug}', [HomeController::class, 'show'])->name('product.detail');

// Manajemen Keranjang (Cart)
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// --- BAGIAN CHECKOUT (PERBAIKAN) ---

// 1. Route GET (Penyelamat Error)
// Kalau user ketik /checkout di browser atau refresh halaman, lempar balik ke cart
Route::get('checkout', function() {
    return redirect()->route('cart.index')->with('error', 'Silakan pilih barang dari keranjang terlebih dahulu.');
});

// 2. Route POST (Logika Utama)
// Hanya aktif saat user klik tombol "Bayar Sekarang" dari halaman Cart
Route::post('checkout', [CartController::class, 'viewCheckout'])->name('cart.checkout');

// 3. Proses Simpan ke Database & Payment Midtrans
Route::post('payment', [CartController::class, 'processPayment'])->name('cart.payment');