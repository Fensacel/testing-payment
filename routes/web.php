<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{slug}', [HomeController::class, 'show'])->name('product.detail');
Route::get('cart', [CartController::class, 'index'])->name('cart.index');
Route::post('cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('cart/remove', [CartController::class, 'remove'])->name('cart.remove');