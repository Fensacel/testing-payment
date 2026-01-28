<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Metode Pembayaran - {{ env('APP_NAME') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .payment-card {
            transition: all 0.3s ease;
        }
        .payment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .payment-card.selected {
            border-color: #000;
            background: #fafafa;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen py-8">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Header -->
            <div class="mb-8">
                <a href="{{ route('payment.cancel', $order) }}" class="text-gray-600 hover:text-black inline-flex items-center mb-4 text-sm font-bold uppercase tracking-wider">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Pilih Metode Pembayaran</h1>
                <p class="text-gray-500 mt-2 text-sm">Order: <span class="font-mono">{{ $order->order_number }}</span></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Payment Methods -->
                <div class="lg:col-span-2">
                    <form action="{{ route('payment.process', $order) }}" method="POST" id="paymentForm">
                        @csrf
                        
                        <!-- E-Wallet -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center uppercase tracking-wider text-xs">
                                <i class="fas fa-wallet text-gray-900 mr-3"></i>
                                E-Wallet
                            </h2>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- GoPay -->
                                <label class="payment-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300">
                                    <input type="radio" name="payment_method" value="gopay" class="hidden payment-radio">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" alt="GoPay" class="h-6 mb-2 object-contain">
                                            <p class="text-xs text-gray-500 font-medium">GoPay</p>
                                        </div>
                                        <i class="fas fa-check-circle text-2xl text-gray-300 check-icon"></i>
                                    </div>
                                </label>

                                <!-- QRIS -->
                                <label class="payment-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300">
                                    <input type="radio" name="payment_method" value="qris" class="hidden payment-radio">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/QRIS_logo.svg" alt="QRIS" class="h-6 mb-2 object-contain">
                                            <p class="text-xs text-gray-500 font-medium">Scan QR</p>
                                        </div>
                                        <i class="fas fa-check-circle text-2xl text-gray-300 check-icon"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Virtual Account -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center uppercase tracking-wider text-xs">
                                <i class="fas fa-university text-gray-900 mr-3"></i>
                                Virtual Account
                            </h2>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- BCA -->
                                <label class="payment-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300">
                                    <input type="radio" name="payment_method" value="bca" class="hidden payment-radio">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-6 mb-2 object-contain">
                                            <p class="text-xs text-gray-500 font-medium">Bank BCA</p>
                                        </div>
                                        <i class="fas fa-check-circle text-2xl text-gray-300 check-icon"></i>
                                    </div>
                                </label>

                                <!-- BNI -->
                                <label class="payment-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300">
                                    <input type="radio" name="payment_method" value="bni" class="hidden payment-radio">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <img src="https://seeklogo.com/images/B/bni-logo-0F993F0FBB-seeklogo.com.png" alt="BNI" class="h-6 mb-2 object-contain">
                                            <p class="text-xs text-gray-500 font-medium">Bank BNI</p>
                                        </div>
                                        <i class="fas fa-check-circle text-2xl text-gray-300 check-icon"></i>
                                    </div>
                                </label>

                                <!-- BRI -->
                                <label class="payment-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300">
                                    <input type="radio" name="payment_method" value="bri" class="hidden payment-radio">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2e/BRI_2020.svg" alt="BRI" class="h-6 mb-2 object-contain">
                                            <p class="text-xs text-gray-500 font-medium">Bank BRI</p>
                                        </div>
                                        <i class="fas fa-check-circle text-2xl text-gray-300 check-icon"></i>
                                    </div>
                                </label>

                                <!-- Mandiri -->
                                <label class="payment-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300">
                                    <input type="radio" name="payment_method" value="mandiri" class="hidden payment-radio">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/a/ad/Bank_Mandiri_logo_2016.svg" alt="Mandiri" class="h-6 mb-2 object-contain">
                                            <p class="text-xs text-gray-500 font-medium">Bank Mandiri</p>
                                        </div>
                                        <i class="fas fa-check-circle text-2xl text-gray-300 check-icon"></i>
                                    </div>
                                </label>


                            </div>
                        </div>

                        <!-- Convenience Store -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
                            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center uppercase tracking-wider text-xs">
                                <i class="fas fa-store text-gray-900 mr-3"></i>
                                Convenience Store
                            </h2>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Indomaret -->
                                <label class="payment-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300">
                                    <input type="radio" name="payment_method" value="indomaret" class="hidden payment-radio">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <img src="https://upload.wikimedia.org/wikipedia/commons/9/9d/Logo_Indomaret.png" alt="Indomaret" class="h-6 mb-2 object-contain">
                                            <p class="text-xs text-gray-500 font-medium">Bayar di Indomaret</p>
                                        </div>
                                        <i class="fas fa-check-circle text-2xl text-gray-300 check-icon"></i>
                                    </div>
                                </label>

                                <!-- Alfamart -->
                                <label class="payment-card cursor-pointer border-2 border-gray-200 rounded-xl p-4 hover:border-gray-300">
                                    <input type="radio" name="payment_method" value="alfamart" class="hidden payment-radio">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <img src="https://seeklogo.com/images/A/alfamart-logo-CDCFB5B3A2-seeklogo.com.png" alt="Alfamart" class="h-6 mb-2 object-contain">
                                            <p class="text-xs text-gray-500 font-medium">Bayar di Alfamart</p>
                                        </div>
                                        <i class="fas fa-check-circle text-2xl text-gray-300 check-icon"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-8">
                        <h2 class="text-xs font-bold text-gray-900 mb-4 uppercase tracking-wider">Ringkasan Pesanan</h2>
                        
                        @foreach($order->items as $item)
                        <div class="flex justify-between mb-3 pb-3 border-b border-gray-100">
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 font-medium">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->quantity }}x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            </div>
                            <p class="text-sm text-gray-900 font-bold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                        </div>
                        @endforeach

                        <div class="pt-4 mt-4 border-t border-gray-200">
                            @if($order->promo_discount > 0)
                            <div class="flex justify-between mb-2 text-sm">
                                <p class="text-gray-600">Subtotal</p>
                                <p class="text-gray-900">Rp {{ number_format($order->total_price + $order->promo_discount, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex justify-between mb-3 text-sm">
                                <p class="text-green-600">Diskon Promo</p>
                                <p class="text-green-600">- Rp {{ number_format($order->promo_discount, 0, ',', '.') }}</p>
                            </div>
                            @endif
                            <div class="flex justify-between text-lg font-bold">
                                <p>Total</p>
                                <p class="text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        
                        <button type="submit" form="paymentForm" id="submitBtn" disabled class="w-full bg-gray-300 text-white py-4 rounded-xl font-bold text-sm uppercase tracking-widest cursor-not-allowed mt-6">
                            Pilih Metode Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const paymentRadios = document.querySelectorAll('.payment-radio');
        const submitBtn = document.getElementById('submitBtn');
        const paymentCards = document.querySelectorAll('.payment-card');

        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Remove selected class from all cards
                paymentCards.forEach(card => {
                    card.classList.remove('selected');
                    card.querySelector('.check-icon').classList.remove('text-black');
                    card.querySelector('.check-icon').classList.add('text-gray-300');
                });

                // Add selected class to selected card
                const selectedCard = this.closest('.payment-card');
                selectedCard.classList.add('selected');
                selectedCard.querySelector('.check-icon').classList.remove('text-gray-300');
                selectedCard.querySelector('.check-icon').classList.add('text-black');

                // Enable submit button
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                submitBtn.classList.add('bg-black', 'hover:bg-gray-800', 'cursor-pointer', 'shadow-xl');
            });
        });
    </script>
</body>
</html>
