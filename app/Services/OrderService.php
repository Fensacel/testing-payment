<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PromoCode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\WhatsAppService;
use App\Mail\PaymentReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }
    /**
     * Create a new order from cart items.
     *
     * @param array $data
     * @param array $cartItems
     * @param array|null $promoCodeSession
     * @return Order
     * @throws \Exception
     */
    public function createOrder(array $data, array $cartItems, ?array $promoCodeSession = null): Order
    {
        return DB::transaction(function () use ($data, $cartItems, $promoCodeSession) {
            
            $subtotal = 0;
            
            // 1. Validate Stock & Calculate Subtotal
            foreach ($cartItems as $item) {
                // Ensure we are locking for update to prevent race conditions
                $product = Product::lockForUpdate()->find($item['product_id']);

                if (!$product) {
                    throw new \Exception("Produk '{$item['name']}' tidak ditemukan.");
                }

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok '{$item['name']}' tidak mencukupi (Tersisa: {$product->stock}).");
                }

                // Calculate price
                $price = $item['price'];
                $discountPercentage = $item['discount_percentage'] ?? 0;

                if ($discountPercentage > 0) {
                    $price = $price - ($price * $discountPercentage / 100);
                }
                $subtotal += $price * $item['quantity'];
            }

            // 2. Handle Promo Code
            $promoDiscount = 0;
            $promoCodeId = null;

            if ($promoCodeSession && isset($promoCodeSession['id'], $promoCodeSession['discount_amount'])) {
                $promoDiscount = $promoCodeSession['discount_amount'];
                $promoCodeId = $promoCodeSession['id'];
            }

            // 3. Calculate Final Total
            $subtotalAfterPromo = max(0, $subtotal - $promoDiscount);
            $serviceFee = $subtotalAfterPromo * 0.01; // 2% removed? Previous code said 1% in calculation (0.01), comment said 2%. Following code: 0.01
            // Correction: Previous code: $serviceFee = $subtotalAfterPromo * 0.01;
            // Let's stick to 1% as per code behavior
            $grandTotal = $subtotalAfterPromo + $serviceFee;

            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            // 4. Create Order Record
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'total_price' => $grandTotal,
                'promo_code_id' => $promoCodeId,
                'promo_discount' => $promoDiscount,
                'status' => 'pending',
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'email' => $data['email'],
                'note' => $data['note'] ?? null,
            ]);

            // 5. Create Order Items & Deduct Stock
            foreach ($cartItems as $item) {
                $product = Product::find($item['product_id']);
                $product->decrement('stock', $item['quantity']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $item['name'],
                    'package_name' => $item['package_name'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // 6. Update Promo Code Usage
            if ($promoCodeId) {
                PromoCode::where('id', $promoCodeId)->increment('used_count');
            }

            return $order;
        });
    }

    /**
     * Cancel an order and restore stock.
     *
     * @param Order $order
     * @return void
     * @throws \Exception
     */
    public function cancelOrder(Order $order): void
    {
        if ($order->status !== 'pending') {
            throw new \Exception('Order sudah diproses, tidak dapat dibatalkan.');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }
            $order->items()->delete();
            $order->delete();
        });
    }

    /**
     * Send payment reminder.
     *
     * @param Order $order
     * @return array
     */
    public function sendPaymentReminder(Order $order): array
    {
        try {
            $order->load(['user', 'items']);

            $whatsappSent = $this->whatsappService->sendPaymentReminder($order);

            $emailSent = false;
            if ($order->email) {
                Mail::to($order->email)->send(new PaymentReminderMail($order));
                $emailSent = true;
            }

            $order->update([
                'last_reminder_sent_at' => now(),
                'reminder_count' => $order->reminder_count + 1
            ]);

            return [
                'success' => true,
                'whatsapp' => $whatsappSent,
                'email' => $emailSent
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send reminder', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
