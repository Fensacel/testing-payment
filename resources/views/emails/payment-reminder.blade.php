<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Pembayaran</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .order-info p {
            margin: 8px 0;
            color: #333;
        }
        .order-info strong {
            color: #667eea;
        }
        .payment-info {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
        }
        .payment-info h3 {
            margin-top: 0;
            color: #856404;
        }
        .payment-code {
            background-color: #fff;
            border: 2px dashed #667eea;
            padding: 10px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
            letter-spacing: 2px;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
        .btn:hover {
            opacity: 0.9;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #667eea;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔔 Pengingat Pembayaran</h1>
        </div>
        
        <div class="content">
            <p>Yth. <strong>{{ $order->user->name ?? $order->customer_name }}</strong>,</p>
            
            <p>Kami ingin mengingatkan bahwa pesanan Anda masih menunggu pembayaran. Mohon segera selesaikan pembayaran agar pesanan dapat kami proses.</p>
            
            <div class="order-info">
                <p><strong>📦 No. Order:</strong> {{ $order->order_number }}</p>
                <p><strong>📅 Tanggal:</strong> {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                <p><strong>💰 Total Pembayaran:</strong> Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>
            
            @if($order->items && $order->items->count() > 0)
            <h3>📋 Detail Pesanan:</h3>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @php
                        $subtotal = 0;
                        foreach($order->items as $item) {
                            $subtotal += $item->price * $item->quantity;
                        }
                    @endphp
                    <tr>
                        <td colspan="2" style="text-align: right; padding-top: 15px; border-top: 2px solid #dee2e6;">Subtotal</td>
                        <td style="padding-top: 15px; border-top: 2px solid #dee2e6;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @if($order->promo_discount > 0)
                    <tr style="color: #10b981;">
                        <td colspan="2" style="text-align: right;">
                            Diskon Promo
                            @if($order->promoCode)
                                <span style="font-size: 11px; background: #10b981; color: white; padding: 2px 6px; border-radius: 3px; margin-left: 5px;">{{ $order->promoCode->code }}</span>
                            @endif
                        </td>
                        <td>- Rp {{ number_format($order->promo_discount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @php
                        $expectedTotal = $subtotal - $order->promo_discount;
                        $paymentFee = $order->total_price - $expectedTotal;
                    @endphp
                    @if($paymentFee > 0)
                    <tr>
                        <td colspan="2" style="text-align: right;">Biaya Admin/Payment</td>
                        <td>Rp {{ number_format($paymentFee, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="2">Total</td>
                        <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            @endif
            
            @if($order->payment_type && $order->payment_info)
            <div class="payment-info">
                <h3>💳 Informasi Pembayaran</h3>
                
                @if(isset($order->payment_info['bank']))
                    <p><strong>🏦 Bank:</strong> {{ strtoupper($order->payment_info['bank']) }}</p>
                    <p><strong>💳 Nomor Virtual Account:</strong></p>
                    <div class="payment-code">{{ $order->payment_info['va_number'] }}</div>
                @elseif(isset($order->payment_info['store']))
                    <p><strong>🏪 Toko:</strong> {{ strtoupper($order->payment_info['store']) }}</p>
                    <p><strong>🔢 Kode Pembayaran:</strong></p>
                    <div class="payment-code">{{ $order->payment_info['payment_code'] }}</div>
                @elseif(isset($order->payment_info['bill_key']))
                    <p><strong>🏦 Mandiri Bill Payment</strong></p>
                    <p><strong>🔑 Bill Key:</strong> {{ $order->payment_info['bill_key'] }}</p>
                    <p><strong>🔢 Biller Code:</strong> {{ $order->payment_info['biller_code'] }}</p>
                @endif
            </div>
            @endif
            
            <center>
                <a href="{{ route('history.detail', $order->id) }}" class="btn">
                    📄 Lihat Detail Pembayaran
                </a>
            </center>
            
            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                ℹ️ Jika Anda sudah melakukan pembayaran, harap abaikan email ini. Pembayaran Anda akan segera diverifikasi.
            </p>
            
            <p style="text-align: center; margin-top: 20px; font-weight: bold; color: #667eea;">
                Terima kasih atas kepercayaan Anda! 🙏
            </p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ env('APP_NAME', 'Toko Online') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
