<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\WhatsAppService;
use App\Mail\PaymentReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send payment reminders to users with pending orders';

    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        parent::__construct();
        $this->whatsappService = $whatsappService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting payment reminder process...');

        // Get pending orders that need reminders
        $orders = Order::where('status', 'pending')
            ->where('created_at', '>=', Carbon::now()->subHours(24)) // Only orders within 24 hours
            ->where(function($query) {
                $query->whereNull('last_reminder_sent_at')
                    ->orWhere('last_reminder_sent_at', '<=', Carbon::now()->subHours(2));
            })
            ->where('reminder_count', '<', 3) // Max 3 reminders
            ->with(['user', 'items'])
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders need reminders at this time.');
            return 0;
        }

        $this->info("Found {$orders->count()} orders to send reminders.");

        $successCount = 0;
        $failCount = 0;

        foreach ($orders as $order) {
            $this->line("Processing order: {$order->order_number}");

            $whatsappSent = false;
            $emailSent = false;

            // Send WhatsApp reminder
            if ($order->customer_phone) {
                try {
                    $whatsappSent = $this->whatsappService->sendPaymentReminder($order);
                    if ($whatsappSent) {
                        $this->info("  ✓ WhatsApp sent to {$order->customer_phone}");
                    } else {
                        $this->warn("  ✗ WhatsApp failed for {$order->customer_phone}");
                    }
                } catch (\Exception $e) {
                    $this->error("  ✗ WhatsApp error: " . $e->getMessage());
                }
            }

            // Send Email reminder (to checkout email)
            if ($order->email) {
                try {
                    Mail::to($order->email)->send(new PaymentReminderMail($order));
                    $emailSent = true;
                    $this->info("  ✓ Email sent to {$order->email}");
                } catch (\Exception $e) {
                    $this->error("  ✗ Email error: " . $e->getMessage());
                }
            }

            // Update reminder tracking
            if ($whatsappSent || $emailSent) {
                $order->update([
                    'last_reminder_sent_at' => Carbon::now(),
                    'reminder_count' => $order->reminder_count + 1
                ]);
                $successCount++;
                $this->info("  Updated reminder count: {$order->reminder_count}");
            } else {
                $failCount++;
            }

            $this->line('');
        }

        $this->info("Reminder process completed!");
        $this->info("Success: {$successCount} | Failed: {$failCount}");

        return 0;
    }
}
