<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductToggleController extends Controller
{
    public function toggle(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);
        
        $status = $product->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()->with('success', "Product {$status} successfully!");
    }
}
