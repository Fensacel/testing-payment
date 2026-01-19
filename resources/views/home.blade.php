@extends('layouts.app')

@section('title', 'MyShop - Solusi Digital Terbaik')

@section('content')
    
    <div class="relative bg-white overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute top-[-10%] right-[-5%] w-96 h-96 bg-gray-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob"></div>
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-gray-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-2000"></div>
            <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-gray-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-blob animation-delay-4000"></div>
        </div>

        <div class="container mx-auto px-4 pt-20 pb-24 relative z-10 text-center">
            <span class="inline-block py-1 px-3 rounded-full bg-gray-100 text-black text-xs font-bold tracking-wider mb-6 border border-gray-200">
                🚀 TOKO PRODUK & JASA DIGITAL TERPERCAYA
            </span>
            
            <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 tracking-tight mb-6">
                Wujudkan Ide Digitalmu <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-black">Lebih Cepat & Mudah</span>
            </h1>
            
            <p class="text-lg text-gray-500 mb-10 max-w-2xl mx-auto leading-relaxed">
                Temukan berbagai layanan pembuatan website, desain grafis, hingga produk digital berkualitas tinggi dengan harga yang bersahabat dan pengerjaan kilat.
            </p>

            <div class="flex justify-center gap-4">
                <a href="#products" class="px-8 py-4 bg-black text-white font-bold rounded-xl shadow-lg hover:bg-gray-800 hover:scale-105 transition transform">
                    Mulai Belanja <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <a href="#" class="px-8 py-4 bg-white text-gray-700 font-bold rounded-xl shadow-md border border-gray-200 hover:border-gray-400 hover:text-black transition">
                    Hubungi Admin
                </a>
            </div>
        </div>
    </div>

    <div class="bg-gray-50 py-12 border-y border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-black text-xl">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Proses Kilat</h3>
                        <p class="text-xs text-gray-500">Pengerjaan cepat & tepat waktu</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-700 text-xl">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Pembayaran Aman</h3>
                        <p class="text-xs text-gray-500">Transaksi terjamin via Midtrans</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-700 text-xl">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Support 24/7</h3>
                        <p class="text-xs text-gray-500">Bantuan kapanpun Anda butuh</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="products" class="container mx-auto px-4 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900">Katalog Pilihan</h2>
            <p class="text-gray-500 mt-2">Pilih layanan atau produk yang sesuai kebutuhanmu</p>
        </div>

        @if($products->isEmpty())
            <div class="text-center py-20 bg-gray-50 rounded-3xl border border-dashed border-gray-300">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 font-medium">Belum ada produk yang tersedia saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($products as $product)
                <a href="{{ route('product.detail', $product->slug) }}" class="group bg-white rounded-2xl shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col">
                    
                    <div class="relative block w-full aspect-square overflow-hidden bg-gray-100">
                        @if($product->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" 
                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700" 
                                 alt="{{ $product->name }}">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif

                        <div class="absolute top-3 right-3 flex flex-col gap-2">
                            @if($product->discount_percentage > 0)
                                <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                    -{{ number_format($product->discount_percentage, 0) }}%
                                </span>
                            @endif
                            
                            @if($product->stock > 0)
                                <span class="bg-white/90 backdrop-blur-sm text-black text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                    Digital Item
                                </span>
                            @else
                                <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                    Sold Out
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-grow">
                        <div class="mb-3">
                            <h3 class="font-bold text-lg text-gray-800 leading-snug group-hover:text-black transition line-clamp-2">
                                {{ $product->name }}
                            </h3>
                        </div>
                        
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $product->description }}</p>
                        
                        <div class="mt-auto pt-4 border-t border-gray-50">
                            <div class="flex flex-col gap-1">
                                @if($product->discount_percentage > 0)
                                    @php
                                        $discountedPrice = $product->price - ($product->price * $product->discount_percentage / 100);
                                    @endphp
                                    <span class="text-xs text-gray-400 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-black font-extrabold text-2xl">
                                        Rp {{ number_format($discountedPrice, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 font-medium">Harga Mulai</span>
                                    <span class="text-black font-extrabold text-2xl">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="container mx-auto px-4 pb-20">
        <div class="bg-gradient-to-r from-gray-900 to-black rounded-3xl p-8 md:p-12 text-center text-white shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-white opacity-10 rounded-full"></div>

            <h2 class="text-3xl md:text-4xl font-bold mb-4 relative z-10">Siap Mengembangkan Bisnismu?</h2>
            <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto relative z-10">Bergabunglah dengan ribuan pelanggan yang telah mempercayakan kebutuhan digitalnya kepada kami.</p>
            
            @auth
                <a href="{{ route('history') }}" class="inline-block bg-white text-black font-bold px-8 py-3 rounded-full shadow-lg hover:bg-gray-100 transition relative z-10">
                    Cek Pesanan Saya
                </a>
            @else
                <a href="{{ route('register') }}" class="inline-block bg-white text-black font-bold px-8 py-3 rounded-full shadow-lg hover:bg-gray-100 transition relative z-10">
                    Daftar Akun Gratis
                </a>
            @endauth
        </div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>

@endsection