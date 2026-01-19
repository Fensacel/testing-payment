@extends('layouts.app')

@section('title', $product->name . ' - MyShop')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <!-- Back Button -->
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-black transition mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
        
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                
                <div class="p-8 bg-gray-50 flex items-center justify-center relative group">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 opacity-50 z-0"></div>
                    
                    <div class="relative z-10 w-full max-w-md aspect-square rounded-2xl overflow-hidden shadow-2xl border-4 border-white transform group-hover:scale-105 transition duration-500">
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

                <div class="p-8 lg:p-12 flex flex-col justify-center">
                    
                    <div class="mb-4 flex items-center gap-2">
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wide">
                            Digital Product
                        </span>
                        @if($product->discount_percentage > 0)
                            <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full">
                                DISKON -{{ number_format($product->discount_percentage, 0) }}%
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="flex items-baseline gap-4 mb-6 border-b border-gray-100 pb-6">
                        <div class="flex flex-col">
                            @if($product->discount_percentage > 0)
                                @php
                                    $discountedPrice = $product->price - ($product->price * $product->discount_percentage / 100);
                                @endphp
                                <span class="text-lg text-gray-400 line-through">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                                <span class="text-4xl font-extrabold text-black">
                                    Rp {{ number_format($discountedPrice, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-4xl font-extrabold text-black">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                        @if($product->stock > 0)
                            <span class="text-green-600 font-medium flex items-center bg-green-50 px-3 py-1 rounded-lg text-sm">
                                <i class="fas fa-check-circle mr-2"></i> Stok Tersedia: {{ $product->stock }}
                            </span>
                        @else
                            <span class="text-red-500 font-medium flex items-center bg-red-50 px-3 py-1 rounded-lg text-sm">
                                <i class="fas fa-times-circle mr-2"></i> Stok Habis
                            </span>
                        @endif
                    </div>

                    <div class="mb-8">
                        <h3 class="text-sm font-bold text-gray-900 uppercase mb-2">Deskripsi Produk</h3>
                        <p class="text-gray-600 leading-relaxed text-sm lg:text-base">
                            {{ $product->description }}
                        </p>
                    </div>

                    @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-8">
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

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" name="action" value="add_cart" 
                                    class="flex-1 py-4 px-6 rounded-xl border-2 border-black text-black font-bold hover:bg-blue-50 transition flex items-center justify-center group">
                                <i class="fas fa-shopping-cart mr-2 group-hover:scale-110 transition-transform"></i> 
                                + Keranjang
                            </button>

                            <button type="submit" name="action" value="buy_now" 
                                    class="flex-1 py-4 px-6 rounded-xl bg-black text-white font-bold hover:bg-gray-900 shadow-lg shadow-gray-500/30 transition flex items-center justify-center transform hover:-translate-y-1">
                                Beli Sekarang
                            </button>
                        </div>
                    </form>
                    @else
                        <button disabled class="w-full py-4 bg-gray-200 text-gray-400 rounded-xl font-bold cursor-not-allowed flex items-center justify-center">
                            Stok Tidak Tersedia
                        </button>
                    @endif

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