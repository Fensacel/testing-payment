<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'total_price',
        'status',
        'customer_name',
        'customer_phone',
        'email',
        'note',
        'snap_token',
        'promo_code_id',
        'promo_discount',
        'payment_type',
        'payment_info',
        'last_reminder_sent_at',
        'reminder_count',
    ];

    protected $casts = [
        'payment_info' => 'array',
        'last_reminder_sent_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user() {
    return $this->belongsTo(User::class);
    }
    
    public function promoCode() {
        return $this->belongsTo(PromoCode::class);
    }
}
