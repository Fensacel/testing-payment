<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $wahaUrl;
    protected $wahaSession;
    protected $wahaApiKey;

    public function __construct()
    {
        // WAHA (WhatsApp HTTP API)
        // Setup: https://waha.devlike.pro/
        $this->wahaUrl = env('WAHA_URL', 'http://localhost:3000');
        $this->wahaSession = env('WAHA_SESSION', 'default');
        $this->wahaApiKey = env('WAHA_API_KEY', ''); // Optional API key
    }

    /**
     * Send WhatsApp message using WAHA
     */
    public function sendMessage($phone, $message)
    {
        if (!$this->wahaUrl) {
            Log::error('WAHA API not configured. Please set WAHA_URL in .env');
            return false;
        }

        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ensure phone starts with country code
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Add @c.us suffix for WhatsApp ID
        $chatId = $phone . '@c.us';

        try {
            // Prepare headers
            $headers = ['Content-Type' => 'application/json'];
            if ($this->wahaApiKey) {
                $headers['X-Api-Key'] = $this->wahaApiKey;
            }

            $response = Http::withHeaders($headers)->post("{$this->wahaUrl}/api/sendText", [
                'session' => $this->wahaSession,
                'chatId' => $chatId,
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp sent successfully via WAHA', [
                    'to' => $phone,
                    'response' => $response->json()
                ]);
                return true;
            }

            Log::error('WhatsApp send failed via WAHA', [
                'to' => $phone,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp send exception via WAHA', [
                'to' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send WhatsApp message with image using WAHA
     */
    public function sendImage($phone, $imageUrl, $caption = '')
    {
        if (!$this->wahaUrl) {
            Log::error('WAHA API not configured');
            return false;
        }

        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $chatId = $phone . '@c.us';

        try {
            $response = Http::post("{$this->wahaUrl}/api/sendImage", [
                'session' => $this->wahaSession,
                'chatId' => $chatId,
                'file' => [
                    'url' => $imageUrl,
                ],
                'caption' => $caption,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp image sent successfully via WAHA', [
                    'to' => $phone,
                ]);
                return true;
            }

            Log::error('WhatsApp image send failed', [
                'to' => $phone,
                'status' => $response->status(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp image send exception', [
                'to' => $phone,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send payment reminder via WhatsApp
     */
    public function sendPaymentReminder($order)
    {
        $message = $this->buildPaymentReminderMessage($order);
        return $this->sendMessage($order->customer_phone, $message);
    }

    /**
     * Send payment success notification
     */
    public function sendPaymentSuccess($order)
    {
        $message = $this->buildPaymentSuccessMessage($order);
        return $this->sendMessage($order->customer_phone, $message);
    }

    /**
     * Build payment reminder message
     */
    protected function buildPaymentReminderMessage($order)
    {
        $appName = env('APP_NAME', 'Toko Online');
        $paymentUrl = route('history.detail', $order->id);
        
        $message = "🔔 *REMINDER PEMBAYARAN*\n\n";
        $message .= "Halo *{$order->customer_name}*,\n\n";
        $message .= "Pesanan Anda menunggu pembayaran:\n\n";
        $message .= "📦 Order: `{$order->order_number}`\n";
        $message .= "💰 Total: *Rp " . number_format($order->total_price, 0, ',', '.') . "*\n";
        $message .= "📅 Tanggal: " . $order->created_at->format('d M Y, H:i') . "\n\n";
        
        if ($order->payment_type && $order->payment_info) {
            $message .= "💳 *Info Pembayaran:*\n";
            
            if (isset($order->payment_info['bank'])) {
                $message .= "Bank: {$order->payment_info['bank']}\n";
                $message .= "VA: `{$order->payment_info['va_number']}`\n";
            } elseif (isset($order->payment_info['store'])) {
                $message .= "{$order->payment_info['store']}\n";
                $message .= "Kode: `{$order->payment_info['payment_code']}`\n";
            } elseif (isset($order->payment_info['bill_key'])) {
                $message .= "Mandiri Bill\n";
                $message .= "Bill Key: `{$order->payment_info['bill_key']}`\n";
                $message .= "Biller: `{$order->payment_info['biller_code']}`\n";
            }
            $message .= "\n";
        }
        
        $message .= "Silakan selesaikan pembayaran segera.\n";
        $message .= "Detail: {$paymentUrl}\n\n";
        $message .= "Terima kasih! 🙏\n";
        $message .= "_{$appName}_";
        
        return $message;
    }

    /**
     * Build payment success message
     */
    protected function buildPaymentSuccessMessage($order)
    {
        $appName = env('APP_NAME', 'Toko Online');
        $orderUrl = route('history.detail', $order->id);
        
        $message = "✅ *PEMBAYARAN BERHASIL*\n\n";
        $message .= "Halo *{$order->customer_name}*,\n\n";
        $message .= "Terima kasih! Pembayaran Anda telah kami terima.\n\n";
        $message .= "📦 Order: `{$order->order_number}`\n";
        $message .= "💰 Total: *Rp " . number_format($order->total_price, 0, ',', '.') . "*\n";
        $message .= "✅ Status: *LUNAS*\n\n";
        
        $message .= "📦 *Langkah Selanjutnya:*\n";
        $message .= "Produk digital Anda akan segera diproses.\n";
        $message .= "Silakan cek email untuk detail lebih lanjut.\n\n";
        
        $message .= "Detail pesanan: {$orderUrl}\n\n";
        $message .= "Terima kasih atas kepercayaan Anda! 🙏\n";
        $message .= "_{$appName}_";
        
        return $message;
    }

    /**
     * Check WAHA session status
     */
    public function checkStatus()
    {
        try {
            $response = Http::get("{$this->wahaUrl}/api/sessions/{$this->wahaSession}");
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'unknown',
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get session status',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get QR code for session (if not authenticated)
     */
    public function getQRCode()
    {
        try {
            $response = Http::get("{$this->wahaUrl}/api/sessions/{$this->wahaSession}/qr");
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'qr' => $response->body(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get QR code',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
