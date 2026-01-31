<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function index()
    {
        $status = $this->whatsappService->checkStatus();
        return view('admin.whatsapp.index', compact('status'));
    }

    public function start()
    {
        $result = $this->whatsappService->startSession();
        
        if ($result['success']) {
            return redirect()->route('admin.whatsapp')
                ->with('success', 'WhatsApp session started successfully. Please wait for the QR code.');
        }

        return redirect()->route('admin.whatsapp')
            ->with('error', 'Failed to start session: ' . ($result['error'] ?? 'Unknown error'));
    }

    public function stop()
    {
        $result = $this->whatsappService->stopSession();
        
        if ($result['success']) {
            return redirect()->route('admin.whatsapp')
                ->with('success', 'WhatsApp session stopped successfully.');
        }

        return redirect()->route('admin.whatsapp')
            ->with('error', 'Failed to stop session: ' . ($result['error'] ?? 'Unknown error'));
    }

    public function logout()
    {
        $result = $this->whatsappService->logoutSession();
        
        if ($result['success']) {
            return redirect()->route('admin.whatsapp')
                ->with('success', 'WhatsApp session logged out successfully.');
        }

        return redirect()->route('admin.whatsapp')
            ->with('error', 'Failed to logout session: ' . ($result['error'] ?? 'Unknown error'));
    }

    public function qr()
    {
        $result = $this->whatsappService->getQRCode();

        if ($result['success']) {
            // Check if it's base64 (WAHA usually returns JSON with data URI or raw image)
            // If raw image, return directly
            // If JSON with data URI, parse it
            
            // For now, based on previous curl output, WAHA returns raw image bytes on /qr endpoint
            // BUT checkStatus was returning JSON
            // getQRCode in service returns response body directly.
            
            return response($result['qr'])
                ->header('Content-Type', $result['content_type'] ?? 'image/png');
        }

        return redirect()->route('admin.whatsapp')
            ->with('error', 'Failed to get QR Code: ' . ($result['error'] ?? 'Unknown error'));
    }
    
    // API endpoint for dashboard auto-refresh
    public function status()
    {
        $status = $this->whatsappService->checkStatus();
        return response()->json($status);
    }
}
