<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Promo Codes
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->decimal('discount_percentage', 10, 2);
            $table->integer('max_uses');
            $table->integer('used_count')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2); // Changed to 15,2 for consistency
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Product Packages
        Schema::create('product_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., Paket Basic, Paket Premium
            $table->decimal('price', 15, 2);
            $table->text('description')->nullable(); 
            $table->timestamps();
        });

        // 5. Orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();
            $table->decimal('total_price', 15, 2);
            $table->string('status')->default('pending'); // pending, success, failed
            $table->string('payment_type')->nullable();
            $table->json('payment_info')->nullable();
            
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable(); // Kept for completeness based on migrations
            $table->string('email')->nullable(); // Main email used in controller
            $table->text('note')->nullable();
            
            $table->string('snap_token')->nullable();
            
            // Promo Code Relation
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();
            $table->decimal('promo_discount', 10, 2)->default(0);

            // Reminder Logic
            $table->timestamp('last_reminder_sent_at')->nullable();
            $table->integer('reminder_count')->default(0);

            $table->timestamps();
        });

        // 6. Order Items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name');
            $table->string('package_name')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('product_packages');
        Schema::dropIfExists('products');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('categories');
    }
};
