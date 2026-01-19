<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeToggleController extends Controller
{
    public function toggle(PromoCode $promoCode)
    {
        $promoCode->update([
            'is_active' => !$promoCode->is_active
        ]);
        
        $status = $promoCode->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()->with('success', "Promo code {$status} successfully!");
    }
}
