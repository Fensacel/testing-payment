<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'total_price',
        'status',
        'customer_name',
        'customer_phone',
        'email',
        'note',
        'snap_token',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
