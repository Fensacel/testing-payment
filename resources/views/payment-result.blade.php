<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instruksi Pembayaran - {{ env('APP_NAME') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Success Header -->
            <div class="bg-white rounded-xl shadow-sm p-8 mb-6 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check text-4xl text-green-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Berhasil Dibuat!</h1>
                <p class="text-gray-600">Order: <span class="font-bold">{{ $order->order_number }}</span></p>
                <p class="text-2xl font-bold text-purple-600 mt-4">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            </div>

            <!-- Payment Instructions -->
            <div class="bg-white rounded-xl shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Instruksi Pembayaran</h2>

                @if($paymentInfo['payment_type'] == 'gopay')
                    <!-- GoPay Instructions -->
                    <div class="text-center mb-6">
                        <p class="text-lg font-semibold text-gray-900 mb-4">Scan QR Code dengan GoPay</p>
                        @if(isset($paymentInfo['qr_code']))
                        <img src="{{ $paymentInfo['qr_code'] }}" alt="QR Code" class="mx-auto w-64 h-64 border-4 border-purple-600 rounded-lg">
                        @endif
                        <p class="text-sm text-gray-600 mt-4">Atau buka aplikasi GoPay dan gunakan deeplink</p>
                    </div>

                @elseif($paymentInfo['payment_type'] == 'qris')
                    <!-- QRIS Instructions -->
                    <div class="text-center mb-6">
                        <p class="text-lg font-semibold text-gray-900 mb-4">Scan QR Code dengan aplikasi pembayaran Anda</p>
                        @if(isset($paymentInfo['qr_code_url']))
                        <img src="{{ $paymentInfo['qr_code_url'] }}" alt="QRIS" class="mx-auto w-64 h-64 border-4 border-purple-600 rounded-lg">
                        @endif
                    </div>

                @elseif($paymentInfo['payment_type'] == 'bank_transfer')
                    <!-- Virtual Account Instructions -->
                    <div class="mb-6">
                        <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-6 mb-6">
                            <p class="text-sm text-gray-600 mb-2">Bank</p>
                            <p class="text-2xl font-bold text-gray-900 mb-4">{{ strtoupper($paymentInfo['bank']) }}</p>
                            
                            @if(isset($paymentInfo['va_number']))
                            <p class="text-sm text-gray-600 mb-2">Nomor Virtual Account</p>
                            <div class="flex items-center justify-between bg-white rounded-lg p-4">
                                <p class="text-2xl font-mono font-bold text-purple-600">{{ $paymentInfo['va_number'] }}</p>
                                <button onclick="copyVA()" class="text-purple-600 hover:text-purple-700">
                                    <i class="fas fa-copy text-xl"></i>
                                </button>
                            </div>
                            @elseif(isset($paymentInfo['bill_key']))
                            <p class="text-sm text-gray-600 mb-2">Mandiri Bill Payment</p>
                            <div class="bg-white rounded-lg p-4 mb-2">
                                <p class="text-sm text-gray-600">Biller Code</p>
                                <p class="text-xl font-mono font-bold text-purple-600">{{ $paymentInfo['biller_code'] }}</p>
                            </div>
                            <div class="bg-white rounded-lg p-4">
                                <p class="text-sm text-gray-600">Bill Key</p>
                                <p class="text-xl font-mono font-bold text-purple-600">{{ $paymentInfo['bill_key'] }}</p>
                            </div>
                            @endif
                        </div>

                        <div class="space-y-3">
                            <h3 class="font-bold text-gray-900">Cara Pembayaran:</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Buka aplikasi mobile banking atau ATM</li>
                                <li>Pilih menu Transfer / Bayar</li>
                                <li>Pilih Virtual Account atau Bank Transfer</li>
                                <li>Masukkan nomor Virtual Account di atas</li>
                                <li>Masukkan nominal: Rp {{ number_format($order->total_price, 0, ',', '.') }}</li>
                                <li>Konfirmasi pembayaran</li>
                            </ol>
                        </div>
                    </div>

                @elseif($paymentInfo['payment_type'] == 'cstore')
                    <!-- Convenience Store Instructions -->
                    <div class="mb-6">
                        <div class="bg-purple-50 border-2 border-purple-200 rounded-lg p-6 mb-6">
                            <p class="text-sm text-gray-600 mb-2">Toko</p>
                            <p class="text-2xl font-bold text-gray-900 mb-4">{{ strtoupper($paymentInfo['store']) }}</p>
                            
                            <p class="text-sm text-gray-600 mb-2">Kode Pembayaran</p>
                            <div class="flex items-center justify-between bg-white rounded-lg p-4">
                                <p class="text-2xl font-mono font-bold text-purple-600">{{ $paymentInfo['payment_code'] }}</p>
                                <button onclick="copyCode()" class="text-purple-600 hover:text-purple-700">
                                    <i class="fas fa-copy text-xl"></i>
                                </button>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <h3 class="font-bold text-gray-900">Cara Pembayaran:</h3>
                            <ol class="list-decimal list-inside space-y-2 text-gray-700">
                                <li>Kunjungi {{ strtoupper($paymentInfo['store']) }} terdekat</li>
                                <li>Berikan kode pembayaran di atas ke kasir</li>
                                <li>Bayar sejumlah Rp {{ number_format($order->total_price, 0, ',', '.') }}</li>
                                <li>Simpan struk pembayaran sebagai bukti</li>
                            </ol>
                        </div>
                    </div>
                @endif

                <!-- Important Notes -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-1"></i>
                        <div>
                            <p class="font-bold text-gray-900">Penting!</p>
                            <ul class="text-sm text-gray-700 mt-2 space-y-1">
                                <li>• Pembayaran akan dikonfirmasi otomatis</li>
                                <li>• Selesaikan pembayaran dalam 24 jam</li>
                                <li>• Simpan bukti pembayaran Anda</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <a href="{{ route('history.detail', $order) }}" class="flex-1 bg-gradient-to-r from-purple-600 to-purple-700 text-white py-3 rounded-lg font-bold text-center hover:from-purple-700 hover:to-purple-800">
                        Lihat Detail Pesanan
                    </a>
                    <a href="{{ route('home') }}" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-bold text-center hover:bg-gray-300">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyVA() {
            const vaNumber = '{{ $paymentInfo['va_number'] ?? '' }}';
            navigator.clipboard.writeText(vaNumber);
            alert('Nomor VA berhasil disalin!');
        }

        function copyCode() {
            const code = '{{ $paymentInfo['payment_code'] ?? '' }}';
            navigator.clipboard.writeText(code);
            alert('Kode pembayaran berhasil disalin!');
        }
    </script>
</body>
</html>
