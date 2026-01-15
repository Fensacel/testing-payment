@extends('layouts.app')

@section('title', 'MyShop - Temukan Barang Impianmu')

@section('content')
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Temukan Barang Impianmu</h1>
            <p class="text-blue-100 text-lg mb-8">Kualitas terbaik dengan harga yang paling bersahabat.</p>
            <a href="#products" class="bg-white text-blue-600 px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition shadow-lg">
                Belanja Sekarang
            </a>
        </div>
    </div>

    <div id="products" class="container mx-auto px-4 py-12">
        <div class="flex justify-between items-end mb-8">
            <h2 class="text-3xl font-bold text-gray-800 border-l-4 border-blue-600 pl-4">Katalog Terbaru</h2>
        </div>

        @if($products->isEmpty())
            <div class="text-center py-20">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Belum ada produk yang tersedia saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                @foreach($products as $product)
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group border border-gray-100 flex flex-col">
                    
                    <a href="{{ route('product.detail', $product->slug) }}" class="block relative w-full aspect-square overflow-hidden bg-gray-200">
                        @if($product->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" 
                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500" 
                                 alt="{{ $product->name }}">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif

                        @if($product->stock <= 0)
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center backdrop-blur-sm">
                                <span class="bg-red-600 text-white px-4 py-1 rounded-full text-sm font-bold shadow">Stok Habis</span>
                            </div>
                        @endif
                    </a>
                    
                    <div class="p-5 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold px-2 py-1 rounded bg-blue-50 text-blue-600">Terbaru</span>
                            <span class="text-xs {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas fa-cube mr-1"></i> Stok: {{ $product->stock }}
                            </span>
                        </div>

                        <a href="{{ route('product.detail', $product->slug) }}">
                            <h3 class="font-bold text-lg text-gray-800 mb-2 leading-tight line-clamp-2 hover:text-blue-600 transition">
                                {{ $product->name }}
                            </h3>
                        </a>
                        
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2 flex-grow">{{ $product->description }}</p>
                        
                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-400">Harga</p>
                                <span class="text-blue-600 font-extrabold text-xl">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            </div>
                            
                            @if($product->stock > 0)
                                <a href="{{ route('product.detail', $product->slug) }}" class="bg-blue-600 hover:bg-blue-700 text-white w-10 h-10 rounded-full flex items-center justify-center transition-colors shadow-lg hover:shadow-blue-300/50">
                                    <i class="fas fa-cart-plus"></i>
                                </a>
                            @else
                                <button disabled class="bg-gray-300 text-gray-500 w-10 h-10 rounded-full flex items-center justify-center cursor-not-allowed">
                                    <i class="fas fa-ban"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection