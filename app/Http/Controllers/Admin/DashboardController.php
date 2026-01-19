<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_orders' => Order::count(),
            'total_revenue' => Order::where('status', 'success')->sum('total_price'),
            'total_products' => Product::count(),
            'total_users' => User::count(),
        ];
        
        $recent_orders = Order::with('items')
            ->latest()
            ->take(10)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }
}
