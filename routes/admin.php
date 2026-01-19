<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Middleware\AdminMiddleware;

Route::middleware(['web', 'auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    
    // Products
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/toggle', [\App\Http\Controllers\Admin\ProductToggleController::class, 'toggle'])->name('products.toggle');
    
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    
    // Promo Codes
    Route::resource('promo-codes', PromoCodeController::class);
    Route::post('/promo-codes/{promo_code}/toggle', [\App\Http\Controllers\Admin\PromoCodeToggleController::class, 'toggle'])->name('promo-codes.toggle');
});
