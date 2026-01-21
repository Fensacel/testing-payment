<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items')
            ->when($request->search, function ($query, $search) {
                $query->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        // Load relationships
        $order->load(['items.product', 'promoCode']);
        
        // Calculate subtotal
        $subtotal = $order->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
        
        // Calculate admin fee (2% of subtotal after promo discount)
        $subtotalAfterPromo = $subtotal - $order->promo_discount;
        $adminFee = $subtotalAfterPromo * 0.01;
        
        return view('admin.orders.show', compact('order', 'subtotal', 'adminFee'));
    }
}
