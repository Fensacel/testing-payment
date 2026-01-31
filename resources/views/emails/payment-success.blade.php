<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #000 0%, #333 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }
        .success-badge {
            background: #10b981;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            display: inline-block;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .order-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e0e0e0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            color: #666;
            font-size: 14px;
        }
        .value {
            font-weight: bold;
            color: #000;
        }
        .items-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 0;
        }
        .items-table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .total-row {
            background: #000;
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .total-row .amount {
            font-size: 24px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #e0e0e0;
            margin-top: 30px;
        }
        .button {
            display: inline-block;
            background: #000;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 28px;">{{ env('APP_NAME') }}</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Konfirmasi Pembayaran</p>
    </div>

    <div class="content">
        <div style="text-align: center;">
            <div class="success-badge">✓ PEMBAYARAN BERHASIL</div>
        </div>

        <p>Halo <strong>{{ $order->customer_name }}</strong>,</p>
        
        <p>Terima kasih! Pembayaran Anda telah berhasil kami terima. Berikut adalah detail pesanan Anda:</p>

        <div class="order-info">
            <div class="info-row">
                <span class="label">Nomor Order</span>
                <span class="value">{{ $order->order_number }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal</span>
                <span class="value">{{ $order->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Status</span>
                <span class="value" style="color: #10b981;">LUNAS</span>
            </div>
            <div class="info-row">
                <span class="label">Metode Pembayaran</span>
                <span class="value">
                    @php
                        $paymentType = $order->payment_type ?? 'Midtrans';
                        $displayName = strtoupper($paymentType);
                        
                        // Map payment types to readable names
                        $paymentNames = [
                            'bank_transfer' => 'Bank Transfer',
                            'echannel' => 'Mandiri Bill Payment',
                            'bca' => 'BCA Virtual Account',
                            'bni' => 'BNI Virtual Account',
                            'bri' => 'BRI Virtual Account',
                            'permata' => 'Permata Virtual Account',
                            'gopay' => 'GoPay',
                            'qris' => 'QRIS',
                            'cstore' => 'Convenience Store',
                            'indomaret' => 'Indomaret',
                            'alfamart' => 'Alfamart',
                        ];
                        
                        $displayName = $paymentNames[$paymentType] ?? strtoupper($paymentType);
                    @endphp
                    {{ $displayName }}
                </span>
            </div>
        </div>

        <h3 style="margin-top: 30px;">Detail Produk</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Harga</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Price Breakdown -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #e0e0e0;">
            <h3 style="margin: 0 0 15px 0; font-size: 14px; color: #666; text-transform: uppercase;">Rincian Pembayaran</h3>
            
            @php
                $subtotal = 0;
                foreach($order->items as $item) {
                    $subtotal += $item->price * $item->quantity;
                }
            @endphp
            
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="color: #666;">Subtotal Produk</span>
                <span style="font-weight: 500;">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            
            @if($order->promo_discount > 0)
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="color: #10b981;">Diskon Promo 
                    @if($order->promoCode)
                        <span style="font-size: 12px; background: #10b981; color: white; padding: 2px 8px; border-radius: 4px; margin-left: 5px;">{{ $order->promoCode->code }}</span>
                    @endif
                </span>
                <span style="color: #10b981; font-weight: 500;">- Rp {{ number_format($order->promo_discount, 0, ',', '.') }}</span>
            </div>
            @endif
            
            @php
                // Calculate payment fee if exists (Midtrans might add fees)
                $expectedTotal = $subtotal - $order->promo_discount;
                $paymentFee = $order->total_price - $expectedTotal;
            @endphp
            
            @if($paymentFee > 0)
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f0f0f0;">
                <span style="color: #666;">Biaya Admin/Payment</span>
                <span style="font-weight: 500;">Rp {{ number_format($paymentFee, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <div class="total-row">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span>Total Pembayaran</span>
                <span class="amount">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('history.detail', $order->id) }}" class="button">Lihat Detail Pesanan</a>
        </div>

        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <strong>📦 Langkah Selanjutnya:</strong>
            <p style="margin: 10px 0 0 0;">Produk digital Anda akan segera diproses. Silakan cek email Anda untuk link download atau instruksi lebih lanjut.</p>
        </div>
    </div>

    <div class="footer">
        <p>Email ini dikirim otomatis, mohon tidak membalas email ini.</p>
        <p>Jika ada pertanyaan, silakan hubungi customer service kami.</p>
        <p style="margin-top: 20px;">© {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.</p>
    </div>
</body>
</html>
