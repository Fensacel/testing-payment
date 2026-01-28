<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Charge GoPay payment
     */
    public function chargeGoPay($order)
    {
        $params = [
            'payment_type' => 'gopay',
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->email,
                'phone' => $order->customer_phone,
            ],
        ];

        try {
            $response = CoreApi::charge($params);
            
            return [
                'success' => true,
                'payment_type' => 'gopay',
                'qr_code' => $response->actions[0]->url ?? null,
                'deeplink' => $response->actions[1]->url ?? null,
                'transaction_id' => $response->transaction_id,
                'status' => $response->transaction_status,
            ];
        } catch (\Exception $e) {
            Log::error('GoPay charge failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Charge Virtual Account payment
     */
    public function chargeVirtualAccount($order, $bank)
    {
        $params = [
            'payment_type' => 'bank_transfer',
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->email,
                'phone' => $order->customer_phone,
            ],
            'bank_transfer' => [
                'bank' => strtolower($bank),
            ],
        ];

        try {
            $response = CoreApi::charge($params);
            
            $vaNumber = null;
            if (isset($response->va_numbers[0])) {
                $vaNumber = $response->va_numbers[0]->va_number;
            } elseif (isset($response->permata_va_number)) {
                $vaNumber = $response->permata_va_number;
            } elseif (isset($response->bill_key)) {
                // Mandiri Bill Payment
                return [
                    'success' => true,
                    'payment_type' => 'bank_transfer',
                    'bank' => $bank,
                    'bill_key' => $response->bill_key,
                    'biller_code' => $response->biller_code,
                    'transaction_id' => $response->transaction_id,
                    'status' => $response->transaction_status,
                ];
            }

            return [
                'success' => true,
                'payment_type' => 'bank_transfer',
                'bank' => $bank,
                'va_number' => $vaNumber,
                'transaction_id' => $response->transaction_id,
                'status' => $response->transaction_status,
            ];
        } catch (\Exception $e) {
            Log::error('VA charge failed', ['bank' => $bank, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Charge Convenience Store payment
     */
    public function chargeConvenienceStore($order, $store)
    {
        $params = [
            'payment_type' => 'cstore',
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->email,
                'phone' => $order->customer_phone,
            ],
            'cstore' => [
                'store' => strtolower($store),
                'message' => 'Payment for order ' . $order->order_number,
            ],
        ];

        try {
            $response = CoreApi::charge($params);
            
            return [
                'success' => true,
                'payment_type' => 'cstore',
                'store' => $store,
                'payment_code' => $response->payment_code,
                'transaction_id' => $response->transaction_id,
                'status' => $response->transaction_status,
            ];
        } catch (\Exception $e) {
            Log::error('Convenience store charge failed', ['store' => $store, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Charge QRIS payment
     */
    public function chargeQRIS($order)
    {
        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int) $order->total_price,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->email,
                'phone' => $order->customer_phone,
            ],
        ];

        try {
            $response = CoreApi::charge($params);
            
            return [
                'success' => true,
                'payment_type' => 'qris',
                'qr_code_url' => $response->actions[0]->url ?? null,
                'transaction_id' => $response->transaction_id,
                'status' => $response->transaction_status,
            ];
        } catch (\Exception $e) {
            Log::error('QRIS charge failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($orderId)
    {
        try {
            $status = \Midtrans\Transaction::status($orderId);
            return $status;
        } catch (\Exception $e) {
            Log::error('Get payment status failed', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return null;
        }
    }
}
