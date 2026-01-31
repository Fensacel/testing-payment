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
    Route::post('/orders/{order}/send-reminder', [OrderController::class, 'sendReminder'])->name('orders.sendReminder');
    
    // Products
    Route::resource('products', ProductController::class);
    Route::post('/products/{product}/toggle', [\App\Http\Controllers\Admin\ProductToggleController::class, 'toggle'])->name('products.toggle');
    
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    
    // Promo Codes
    Route::resource('promo-codes', PromoCodeController::class);
    Route::post('/promo-codes/{promo_code}/toggle', [\App\Http\Controllers\Admin\PromoCodeToggleController::class, 'toggle'])->name('promo-codes.toggle');

    // WhatsApp Management
    Route::get('/whatsapp', [\App\Http\Controllers\Admin\WhatsAppController::class, 'index'])->name('whatsapp');
    Route::post('/whatsapp/start', [\App\Http\Controllers\Admin\WhatsAppController::class, 'start'])->name('whatsapp.start');
    Route::post('/whatsapp/stop', [\App\Http\Controllers\Admin\WhatsAppController::class, 'stop'])->name('whatsapp.stop');
    Route::post('/whatsapp/logout', [\App\Http\Controllers\Admin\WhatsAppController::class, 'logout'])->name('whatsapp.logout');
    Route::get('/whatsapp/qr', [\App\Http\Controllers\Admin\WhatsAppController::class, 'qr'])->name('whatsapp.qr');
    Route::get('/whatsapp/status', [\App\Http\Controllers\Admin\WhatsAppController::class, 'status'])->name('whatsapp.status');
});
