<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;
    protected $phone;
    protected $groqApiKey;
    protected $useGroq;

    public function __construct()
    {
        // TextMeBot - Free WhatsApp API
        // Setup: https://textmebot.com
        $this->apiUrl = 'https://api.textmebot.com/send.php';
        $this->apiKey = env('WHATSAPP_API_KEY');
        $this->phone = env('WHATSAPP_PHONE');
        
        // Groq AI for message generation
        $this->groqApiKey = env('GROQ_API_KEY');
        $this->useGroq = env('USE_GROQ_FOR_MESSAGES', false);
    }

    /**
     * Send WhatsApp message using TextMeBot
     */
    public function sendMessage($phone, $message)
    {
        if (!$this->apiKey || !$this->phone) {
            Log::error('WhatsApp API not configured. Please set WHATSAPP_API_KEY and WHATSAPP_PHONE in .env');
            return false;
        }

        // Clean phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ensure phone starts with country code
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $response = Http::asForm()->post($this->apiUrl, [
                'recipient' => $phone,
                'apikey' => $this->apiKey,
                'text' => $message,
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp sent successfully via TextMeBot', [
                    'to' => $phone,
                    'response' => $response->body()
                ]);
                return true;
            }

            Log::error('WhatsApp send failed', [
                'to' => $phone,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp send exception', [
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
     * Build payment reminder message using Groq AI or template
     */
    protected function buildPaymentReminderMessage($order)
    {
        // Try to use Groq AI if enabled and configured
        if ($this->useGroq && $this->groqApiKey) {
            try {
                return $this->generateMessageWithGroq($order);
            } catch (\Exception $e) {
                Log::warning('Groq AI failed, falling back to template', [
                    'error' => $e->getMessage()
                ]);
                // Fall back to template if Groq fails
            }
        }

        // Default template-based message
        return $this->buildTemplateMessage($order);
    }

    /**
     * Generate personalized message using Groq AI
     */
    protected function generateMessageWithGroq($order)
    {
        $paymentUrl = route('history.detail', $order->id);
        
        // Prepare order data for AI
        $orderData = [
            'customer_name' => $order->customer_name,
            'order_number' => $order->order_number,
            'total_price' => 'Rp ' . number_format($order->total_price, 0, ',', '.'),
            'created_at' => $order->created_at->format('d M Y, H:i'),
            'customer_phone' => $order->customer_phone,
            'payment_url' => $paymentUrl,
        ];

        if ($order->payment_type && $order->payment_info) {
            if (isset($order->payment_info['bank'])) {
                $orderData['payment_method'] = 'Bank Transfer ' . $order->payment_info['bank'];
                $orderData['payment_detail'] = 'VA: ' . $order->payment_info['va_number'];
            } elseif (isset($order->payment_info['store'])) {
                $orderData['payment_method'] = $order->payment_info['store'];
                $orderData['payment_detail'] = 'Kode: ' . $order->payment_info['payment_code'];
            } elseif (isset($order->payment_info['bill_key'])) {
                $orderData['payment_method'] = 'Mandiri Bill';
                $orderData['payment_detail'] = 'Bill Key: ' . $order->payment_info['bill_key'];
            }
        }

        $prompt = "Generate a professional yet engaging WhatsApp payment reminder message in Indonesian for an e-commerce order. 

Order details:
" . json_encode($orderData, JSON_PRETTY_PRINT) . "

IMPORTANT: Follow this EXACT format structure:

🔔 Pengingat Pembayaran

Yth. Bapak/Ibu [Customer Name],

Kami ingin mengingatkan bahwa pesanan Anda masih menunggu pembayaran:

📦 No. Order: [Order Number]
💰 Total: [Total Amount]
📅 Tanggal: [Date]

[If payment method exists:]
💳 Metode Pembayaran:
[Payment Method]
[Payment Details]

Mohon segera selesaikan pembayaran Anda agar pesanan dapat kami proses.

Detail lengkap: [Payment URL]

Terima kasih atas kepercayaan Anda! 🙏
_" . env('APP_NAME', 'Toko Online') . "_

Requirements:
- Use EXACTLY the format above
- Replace [placeholders] with actual data
- Keep it clean and well-structured
- Use emojis as shown in template
- Keep lines short and readable
- Professional but friendly tone
- Maximum 15 lines total

Generate only the message text following the template above.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->groqApiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant that generates professional WhatsApp messages for e-commerce payment reminders in Indonesian.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 500,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $message = $data['choices'][0]['message']['content'] ?? null;
            
            if ($message) {
                Log::info('Groq AI generated message successfully');
                return trim($message);
            }
        }

        throw new \Exception('Groq API failed: ' . $response->body());
    }

    /**
     * Build template-based message (fallback)
     */
    protected function buildTemplateMessage($order)
    {
        $appName = env('APP_NAME', 'Toko Online');
        $paymentUrl = route('history.detail', $order->id);
        
        $message = "*REMINDER PEMBAYARAN*\n\n";
        $message .= "Halo *{$order->customer_name}*,\n\n";
        $message .= "Pesanan Anda menunggu pembayaran:\n\n";
        $message .= "Order: {$order->order_number}\n";
        $message .= "Total: Rp " . number_format($order->total_price, 0, ',', '.') . "\n";
        $message .= "Tanggal: " . $order->created_at->format('d M Y, H:i') . "\n";
        $message .= "Customer: {$order->customer_name}\n";
        $message .= "Phone: {$order->customer_phone}\n\n";
        
        if ($order->payment_type && $order->payment_info) {
            $message .= "*Info Pembayaran:*\n";
            
            if (isset($order->payment_info['bank'])) {
                $message .= "Bank: {$order->payment_info['bank']}\n";
                $message .= "VA: {$order->payment_info['va_number']}\n";
            } elseif (isset($order->payment_info['store'])) {
                $message .= "{$order->payment_info['store']}\n";
                $message .= "Kode: {$order->payment_info['payment_code']}\n";
            } elseif (isset($order->payment_info['bill_key'])) {
                $message .= "Mandiri Bill\n";
                $message .= "Bill Key: {$order->payment_info['bill_key']}\n";
                $message .= "Biller: {$order->payment_info['biller_code']}\n";
            }
            $message .= "\n";
        }
        
        $message .= "Silakan selesaikan pembayaran segera.\n";
        $message .= "Detail: {$paymentUrl}\n\n";
        $message .= "_{$appName}_";
        
        return $message;
    }
}
