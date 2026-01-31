<?php

namespace App\Services;

use App\Models\PromoCode;
use Carbon\Carbon;

class PromoCodeService
{
    /**
     * Validate and retrieve promo code by code string.
     *
     * @param string $code
     * @return PromoCode
     * @throws \Exception
     */
    public function validateCode(string $code): PromoCode
    {
        $promo = PromoCode::where('code', strtoupper(trim($code)))->first();

        if (!$promo) {
            throw new \Exception('Kode promo tidak valid');
        }

        if (!$promo->is_active) {
            throw new \Exception('Kode promo sudah tidak aktif');
        }

        if ($promo->used_count >= $promo->max_uses) {
            throw new \Exception('Kode promo sudah mencapai batas penggunaan');
        }

        if ($promo->expires_at && now()->gt($promo->expires_at)) {
            throw new \Exception('Kode promo sudah kadaluarsa');
        }

        return $promo;
    }

    /**
     * Calculate discount amount.
     *
     * @param PromoCode $promo
     * @param float $subtotal
     * @return float
     */
    public function calculateDiscount(PromoCode $promo, float $subtotal): float
    {
        // Add more complex logic here if needed (e.g. max discount amount)
        return $subtotal * ($promo->discount_percentage / 100);
    }
}
