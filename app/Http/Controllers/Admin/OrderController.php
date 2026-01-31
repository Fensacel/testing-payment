<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

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
        $order->load(['items.product', 'promoCode']);
        
        // Logic kept here as it is view-specific calculation, or could be moved to model accessor
        $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
        $subtotalAfterPromo = $subtotal - $order->promo_discount;
        $adminFee = $subtotalAfterPromo * 0.01;
        
        return view('admin.orders.show', compact('order', 'subtotal', 'adminFee'));
    }

    public function sendReminder(Order $order)
    {
        try {
            $result = $this->orderService->sendPaymentReminder($order);

            $message = 'Reminder berhasil dikirim! ';
            if ($result['whatsapp']) $message .= 'WhatsApp ✓ ';
            if ($result['email']) $message .= 'Email ✓';

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengirim reminder: ' . $e->getMessage());
        }
    }
}
