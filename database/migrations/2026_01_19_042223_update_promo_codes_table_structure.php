<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('discount_value', 'discount_percentage');
            $table->renameColumn('max_usage', 'max_uses');
            $table->renameColumn('valid_until', 'expires_at');
        });
        
        // Drop unused columns
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'valid_from']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->string('discount_type')->default('percentage');
            $table->datetime('valid_from')->nullable();
        });
        
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->renameColumn('discount_percentage', 'discount_value');
            $table->renameColumn('max_uses', 'max_usage');
            $table->renameColumn('expires_at', 'valid_until');
        });
    }
};
