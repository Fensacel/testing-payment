<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    protected $fillable = [
        'code',
        'discount_percentage',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Check if promo code is valid
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->used_count >= $this->max_uses) {
            return false;
        }

        $now = now();
        
        if ($this->expires_at && $now->gt(\Carbon\Carbon::parse($this->expires_at))) {
            return false;
        }

        return true;
    }

    // Calculate discount amount
    public function calculateDiscount(float $subtotal): float
    {
        return $subtotal * ($this->discount_percentage / 100);
    }
}
