<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsAppService;
use App\Mail\PaymentReminderMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

    /**
     * Send manual payment reminder
     */
    public function sendReminder(Order $order)
    {
        try {
            // Load relationships
            $order->load(['user', 'items']);

            // Send WhatsApp notification
            $whatsappService = new WhatsAppService();
            $whatsappSent = $whatsappService->sendPaymentReminder($order);

            // Send Email notification
            $emailSent = false;
            if ($order->email) {
                Mail::to($order->email)->send(new PaymentReminderMail($order));
                $emailSent = true;
            }

            // Update reminder tracking
            $order->update([
                'last_reminder_sent_at' => now(),
                'reminder_count' => $order->reminder_count + 1
            ]);

            $message = 'Reminder berhasil dikirim! ';
            if ($whatsappSent) $message .= 'WhatsApp ✓ ';
            if ($emailSent) $message .= 'Email ✓';

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Failed to send manual reminder', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Gagal mengirim reminder: ' . $e->getMessage());
        }
    }
}
