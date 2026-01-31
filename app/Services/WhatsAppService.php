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
     * Start WAHA session
     */
    public function startSession()
    {
        try {
            // Prepare headers
            $headers = ['Content-Type' => 'application/json'];
            if ($this->wahaApiKey) {
                $headers['X-Api-Key'] = $this->wahaApiKey;
            }

            // 1. Try to start existing session
            $response = Http::withHeaders($headers)->post("{$this->wahaUrl}/api/sessions/start", [
                'name' => $this->wahaSession,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            // 2. If start failed (maybe session doesn't exist), try to create it
            // WAHA returns 404 or specific error if session not found
            if ($response->status() === 404 || str_contains($response->body(), 'not found')) {
                $createResponse = Http::withHeaders($headers)->post("{$this->wahaUrl}/api/sessions", [
                    'name' => $this->wahaSession,
                    'config' => [
                        'proxy' => null,
                        'webhooks' => [],
                    ]
                ]);

                if ($createResponse->successful()) {
                    return ['success' => true, 'data' => $createResponse->json()];
                }
                return ['success' => false, 'error' => 'Failed to create session: ' . $createResponse->body()];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Stop WAHA session
     */
    public function stopSession()
    {
        try {
            $headers = ['Content-Type' => 'application/json'];
            if ($this->wahaApiKey) {
                $headers['X-Api-Key'] = $this->wahaApiKey;
            }

            $response = Http::withHeaders($headers)->post("{$this->wahaUrl}/api/sessions/stop", [
                'name' => $this->wahaSession,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Logout WAHA session
     */
    public function logoutSession()
    {
        try {
            $headers = ['Content-Type' => 'application/json'];
            if ($this->wahaApiKey) {
                $headers['X-Api-Key'] = $this->wahaApiKey;
            }

            $response = Http::withHeaders($headers)->post("{$this->wahaUrl}/api/sessions/logout", [
                'name' => $this->wahaSession,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'error' => $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check WAHA session status
     */
    public function checkStatus()
    {
        try {
            $headers = [];
            if ($this->wahaApiKey) {
                $headers['X-Api-Key'] = $this->wahaApiKey;
            }

            $response = Http::withHeaders($headers)->get("{$this->wahaUrl}/api/sessions/{$this->wahaSession}");
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['status'] ?? 'unknown',
                    'data' => $data,
                ];
            }

            // Treat 404 (Session not found) as STOPPED so we can show Start button
            if ($response->status() === 404) {
                return [
                    'success' => true, // Request successful, just no session
                    'status' => 'STOPPED',
                    'data' => [],
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get session status: ' . $response->status(),
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
            $headers = [];
            if ($this->wahaApiKey) {
                $headers['X-Api-Key'] = $this->wahaApiKey;
            }

            // Correct URL: /api/{session}/auth/qr
            $response = Http::withHeaders($headers)->get("{$this->wahaUrl}/api/{$this->wahaSession}/auth/qr");
            
            if ($response->successful()) {
                return [
                    'success' => true,
                    'qr' => $response->body(),
                    'content_type' => $response->header('Content-Type'),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get QR code: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
