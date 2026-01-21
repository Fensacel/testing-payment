@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
<div class="bg-gray-100 min-h-screen py-6 md:py-8">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ route('cart.index') }}" class="inline-flex items-center text-gray-600 hover:text-black transition mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Keranjang
        </a>
        
        <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Checkout</h1>

        <form action="{{ route('cart.payment') }}" method="POST">
            @csrf
            <input type="hidden" name="selected_products" value="{{ implode(',', $selectedItemKeys) }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-black mr-2"></i> Data Pemesan
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2">Nama Lengkap</label>
                                <input type="text" name="customer_name" class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200" placeholder="Contoh: Budi Santoso" required>
                            </div>
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2">Nomor WhatsApp</label>
                                <input type="number" name="customer_phone" class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200" placeholder="08123xxxxx" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-600 text-sm font-bold mb-2">Alamat Email (Untuk Pengiriman File)</label>
                                <input type="email" name="email" class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200" placeholder="email@contoh.com" required>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-edit text-black mr-2"></i> Detail Pesanan / Catatan
                        </h2>
                        <textarea name="note" rows="3" class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200" placeholder="Tuliskan detail pesanan Anda, misal: Nama domain yang diinginkan, referensi desain, atau link file materi..."></textarea>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Item Jasa</h2>
                        @foreach($itemsToBuy as $item)
                        <div class="flex gap-4 mb-4 border-b last:border-0 pb-4 last:pb-0 border-gray-100">
                            <div class="w-16 h-16 bg-gray-100 rounded-md overflow-hidden border relative">
                                @if($item['image'])
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item['image']) }}" class="w-full h-full object-cover">
                                @endif
                                @if(isset($item['discount_percentage']) && $item['discount_percentage'] > 0)
                                    <div class="absolute top-0 right-0 bg-red-500 text-white text-[8px] font-bold px-1 rounded-bl">
                                        -{{ number_format($item['discount_percentage'], 0) }}%
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800">{{ $item['name'] }}</h3>
                                <p class="text-gray-500 text-sm">Qty: {{ $item['quantity'] }}</p>
                                @if(isset($item['discount_percentage']) && $item['discount_percentage'] > 0)
                                    @php
                                        $originalPrice = $item['price'];
                                        $discountedPrice = $originalPrice - ($originalPrice * $item['discount_percentage'] / 100);
                                    @endphp
                                    <p class="text-xs text-gray-400 line-through">Rp {{ number_format($originalPrice, 0, ',', '.') }}</p>
                                    <p class="font-bold text-black">Rp {{ number_format($discountedPrice, 0, ',', '.') }}</p>
                                @else
                                    <p class="font-bold text-black">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 sticky top-24">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h2>
                        
                        @php
                            $subtotalBeforeDiscount = 0;
                            $totalDiscount = 0;
                            foreach($itemsToBuy as $item) {
                                $originalPrice = $item['price'] * $item['quantity'];
                                $subtotalBeforeDiscount += $originalPrice;
                                
                                if (isset($item['discount_percentage']) && $item['discount_percentage'] > 0) {
                                    $discountAmount = $originalPrice * ($item['discount_percentage'] / 100);
                                    $totalDiscount += $discountAmount;
                                }
                            }
                            $subtotalAfterDiscount = $subtotalBeforeDiscount - $totalDiscount;
                            // Subtract promo discount if exists
                            $subtotalAfterPromo = $subtotalAfterDiscount - (isset($promoDiscount) ? $promoDiscount : 0);
                            $serviceFee = $subtotalAfterPromo * 0.01; // 2% service fee
                            $finalTotal = $subtotalAfterPromo + $serviceFee;
                        @endphp

                        <div class="space-y-3 text-sm text-gray-600 mb-4">
                            <div class="flex justify-between">
                                <span>Subtotal Jasa</span>
                                <span>Rp {{ number_format($subtotalBeforeDiscount, 0, ',', '.') }}</span>
                            </div>
                            @if($totalDiscount > 0)
                            <div class="flex justify-between text-green-600 font-medium">
                                <span>Diskon Produk</span>
                                <span>- Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            @if(isset($promoDiscount) && $promoDiscount > 0)
                            <div class="flex justify-between text-green-600 font-medium">
                                <span>Diskon Promo ({{ $promoCode['code'] }})</span>
                                <span>- Rp {{ number_format($promoDiscount, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span>Biaya Admin (1%)</span>
                                <span>Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="border-t pt-4 flex justify-between items-center mb-6">
                            <span class="font-bold text-lg text-gray-800">Total Tagihan</span>
                            <span class="font-bold text-xl text-black">Rp {{ number_format($finalTotal, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="w-full bg-black hover:bg-gray-900 text-white font-bold py-3 rounded-lg shadow transition transform active:scale-95">
                            Lanjut Pembayaran <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection