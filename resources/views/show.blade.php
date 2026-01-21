@extends('layouts.app')

@section('title', $product->name . ' - MyShop')

@section('content')
<div class="bg-gray-50 min-h-screen pb-28 md:pb-8 md:py-8">
    <div class="container mx-auto px-0 md:px-4">
        <!-- Back Button (Desktop Only) -->
        <a href="{{ route('home') }}" class="hidden md:inline-flex items-center text-gray-600 hover:text-black transition mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
        
        <div class="bg-white md:rounded-2xl md:shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                
                <!-- Image Section -->
                <div class="relative bg-gray-100/50 group">
                    <!-- Mobile Back Button overlay -->
                    <a href="{{ route('home') }}" class="absolute top-4 left-4 z-20 w-10 h-10 bg-white/80 backdrop-blur rounded-full flex items-center justify-center text-black shadow-sm md:hidden">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <div class="w-full aspect-square relative overflow-hidden">
                        @if($product->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" 
                                 class="w-full h-full object-cover" 
                                 alt="{{ $product->name }}">
                        @else
                            <div class="flex items-center justify-center h-full bg-gray-200 text-gray-400">
                                <i class="fas fa-image text-6xl"></i>
                            </div>
                        @endif

                        @if($product->stock <= 0)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center backdrop-blur-sm">
                                <span class="bg-red-600 text-white px-6 py-2 rounded-full font-bold shadow-lg transform rotate-[-10deg] text-xl border-2 border-white">
                                    STOK HABIS
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Content Section -->
                <div class="p-5 md:p-8 lg:p-12 flex flex-col justify-center relative bg-white -mt-6 md:mt-0 rounded-t-3xl md:rounded-none z-10 shadow-[0_-10px_20px_-5px_rgba(0,0,0,0.1)] md:shadow-none">
                    
                    <div class="flex justify-center md:justify-start mb-2">
                        <div class="w-12 h-1.5 bg-gray-200 rounded-full md:hidden mb-4"></div>
                    </div>

                    <div class="mb-4 flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-700 text-[10px] md:text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wide">
                            Digital Product
                        </span>
                        @if($product->discount_percentage > 0)
                            <span class="bg-red-500 text-white text-[10px] md:text-xs font-bold px-2.5 py-1 rounded-full">
                                HEMAT {{ number_format($product->discount_percentage, 0) }}%
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="flex items-baseline gap-4 mb-6 border-b border-gray-100 pb-6">
                        <div class="flex flex-col">
                            @php
                                $hasDiscount = $product->discount_percentage > 0;
                                $finalPrice = $hasDiscount 
                                    ? $product->price - ($product->price * $product->discount_percentage / 100) 
                                    : $product->price;
                            @endphp

                            <span id="original-price-display" class="text-base text-gray-400 line-through {{ $hasDiscount ? '' : 'hidden' }}">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            <span id="final-price-display" class="text-3xl md:text-4xl font-extrabold text-black">
                                Rp {{ number_format($finalPrice, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-sm font-bold text-gray-900 uppercase mb-2">Deskripsi Produk</h3>
                        <div class="prose prose-sm text-gray-600 leading-relaxed">
                            {{ $product->description }}
                        </div>
                    </div>

                    <form action="{{ route('cart.add', $product->id) }}" method="POST" id="add-to-cart-form">
                        @csrf
                        
                        @if($product->packages->count() > 0)
                        <div class="mb-6">
                            <label class="text-sm font-bold text-gray-900 uppercase mb-3 block">Pilih Paket</label>
                            <div class="space-y-3">
                                @foreach($product->packages as $package)
                                <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer hover:bg-gray-50 transition border-gray-200 has-[:checked]:border-black has-[:checked]:bg-gray-50">
                                    <input type="radio" name="package_id" value="{{ $package->id }}" class="hidden peer" required 
                                           data-price="{{ $package->price > 0 ? $package->price : $product->price }}"
                                           onchange="updatePrice(this)">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-gray-900">{{ $package->name }}</span>
                                            @if($package->price > 0)
                                            <span class="font-bold text-black">Rp {{ number_format($package->price, 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        @if($package->description)
                                        <p class="text-xs md:text-sm text-gray-500">{{ $package->description }}</p>
                                        @endif
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 ml-4 peer-checked:border-black peer-checked:bg-black flex items-center justify-center">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="mb-8 md:mb-0">
                            <label class="text-sm font-bold text-gray-900 uppercase mb-3 block">Atur Jumlah</label>
                            <div class="inline-flex items-center bg-gray-50 rounded-xl border border-gray-200 p-1">
                                <button type="button" onclick="updateQty(-1)" class="w-10 h-10 rounded-lg bg-white text-gray-600 hover:bg-gray-100 hover:text-black shadow-sm transition flex items-center justify-center font-bold text-lg">
                                    -
                                </button>
                                <input type="number" id="quantity_input" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
                                       class="w-16 bg-transparent text-center font-bold text-gray-800 focus:outline-none border-none p-0" readonly>
                                <button type="button" onclick="updateQty(1)" class="w-10 h-10 rounded-lg bg-white text-gray-600 hover:bg-gray-100 hover:text-black shadow-sm transition flex items-center justify-center font-bold text-lg">
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Desktop Buttons (Hidden on Mobile) -->
                        <div class="hidden md:flex flex-row gap-4 mt-8">
                            <button type="submit" name="action" value="add_cart" 
                                    class="flex-1 py-4 px-6 rounded-xl border-2 border-black text-black font-bold hover:bg-blue-50 transition flex items-center justify-center group"
                                    {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                <i class="fas fa-shopping-cart mr-2 group-hover:scale-110 transition-transform"></i> 
                                + Keranjang
                            </button>

                            <button type="submit" name="action" value="buy_now" 
                                    class="flex-1 py-4 px-6 rounded-xl bg-black text-white font-bold hover:bg-gray-900 shadow-lg shadow-gray-500/30 transition flex items-center justify-center transform hover:-translate-y-1"
                                    {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                Beli Sekarang
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 pt-6 border-t border-gray-100 grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-3 text-sm text-gray-500">
                            <i class="fas fa-shield-alt text-green-500 text-xl"></i>
                            <span>Garansi Layanan</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-500">
                            <i class="fas fa-bolt text-yellow-500 text-xl"></i>
                            <span>Proses Cepat</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    
    <!-- Mobile Sticky Footer Action Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 md:hidden z-50 flex gap-3 shadow-[0_-4px_20px_rgba(0,0,0,0.1)]">
        @if($product->stock > 0)
            <button onclick="document.querySelector('button[value=\'add_cart\']').click()" 
                    class="flex-1 py-3 px-4 rounded-xl border border-black text-black font-bold flex items-center justify-center active:bg-gray-50">
                <i class="fas fa-shopping-cart mr-2"></i>
            </button>
            <button onclick="document.querySelector('button[value=\'buy_now\']').click()" 
                    class="flex-[2] py-3 px-4 rounded-xl bg-black text-white font-bold hover:bg-gray-900 shadow-lg flex items-center justify-center active:scale-95 transition">
                Beli Sekarang
            </button>
        @else
            <button disabled class="w-full py-3 bg-gray-200 text-gray-400 rounded-xl font-bold cursor-not-allowed">
                Stok Habis
            </button>
        @endif
    </div>
</div>

<script>
    function updateQty(change) {
        let input = document.getElementById('quantity_input');
        let currentQty = parseInt(input.value);
        let maxStock = parseInt("{{ $product->stock }}");

        let newQty = currentQty + change;

        if (newQty >= 1 && newQty <= maxStock) {
            input.value = newQty;
        }
    }

    function updatePrice(radio) {
        const basePrice = parseFloat(radio.getAttribute('data-price'));
        const discountPercentage = parseFloat("{{ $product->discount_percentage }}");
        
        let finalPrice = basePrice;
        
        // Update original price display
        const originalPriceEl = document.getElementById('original-price-display');
        const finalPriceEl = document.getElementById('final-price-display');
        
        if (discountPercentage > 0) {
            const discountAmount = basePrice * (discountPercentage / 100);
            finalPrice = basePrice - discountAmount;
            
            originalPriceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(basePrice);
            originalPriceEl.classList.remove('hidden');
        } else {
            originalPriceEl.classList.add('hidden');
        }
        
        finalPriceEl.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(finalPrice);
    }
</script>

<style>
    /* Hilangkan panah spinner di input number */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
</style>
@endsection