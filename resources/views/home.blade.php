@extends('layouts.app')

@section('title', 'Fachri Store - Premium Digital Assets')

@section('content')
    <!-- Premium Light Hero -->
    <div class="relative bg-white overflow-hidden">
        <!-- Subtle Mesh Gradient Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-gray-50 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-pulse"></div>
            <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-60"></div>
             <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mixed-blend-overlay"></div>
        </div>

        <div class="container mx-auto px-4 pt-32 pb-40 relative z-10">
            <div class="max-w-4xl mx-auto text-center">
                
                <!-- Text Content -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-50 border border-gray-200 mb-8 mx-auto">
                     <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                     <span class="text-[10px] font-bold tracking-[0.2em] text-gray-500 uppercase">New Collection 2026</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-bold text-gray-900 tracking-tight mb-8 leading-[1.1]">
                    Elevate Your <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-gray-900 via-gray-700 to-gray-500">Digital Presence.</span>
                </h1>

                <p class="text-lg text-gray-500 mb-10 max-w-2xl mx-auto leading-relaxed font-light">
                    Discover a curated collection of premium digital assets designed to accelerate your creative workflow.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#collection" class="px-8 py-4 bg-black text-white text-sm font-bold uppercase tracking-widest rounded-lg shadow-xl hover:bg-gray-800 hover:shadow-2xl transition transform active:scale-95">
                        Shop Now
                    </a>
                    <a href="#" class="px-8 py-4 bg-white text-black text-sm font-bold uppercase tracking-widest rounded-lg border border-gray-200 hover:bg-gray-50 transition">
                        View Showcase
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust / Features Minimalist -->
    <div class="bg-gray-50 py-24 border-y border-gray-100">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <div class="group">
                    <div class="w-12 h-12 border border-gray-200 rounded-lg flex items-center justify-center mb-6 group-hover:bg-black group-hover:border-black transition duration-300">
                        <i class="fas fa-box text-gray-900 group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Instant Delivery.</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Receive your assets immediately after purchase via email. No waiting time.</p>
                </div>
                <div class="group">
                    <div class="w-12 h-12 border border-gray-200 rounded-lg flex items-center justify-center mb-6 group-hover:bg-black group-hover:border-black transition duration-300">
                        <i class="fas fa-lock text-gray-900 group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Secure Payments.</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">We use top-tier encryption and trusted payment gateways for your safety.</p>
                </div>
                <div class="group">
                    <div class="w-12 h-12 border border-gray-200 rounded-lg flex items-center justify-center mb-6 group-hover:bg-black group-hover:border-black transition duration-300">
                        <i class="fas fa-infinity text-gray-900 group-hover:text-white transition"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Lifetime Updates.</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Get free updates for life on selected premium products. Stay current.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Grid -->
    <div id="collection" class="bg-white py-24 min-h-screen">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 border-b border-gray-100 pb-6">
                <div>
                     <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Curated Selection</p>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900">Latest Products</h2>
                </div>
                <a href="#" class="hidden md:flex items-center gap-2 text-sm font-bold text-gray-900 hover:text-gray-600 transition uppercase tracking-wider">
                    View All <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            @if($products->isEmpty())
                <div class="text-center py-32 bg-gray-50 rounded-lg border border-gray-200">
                    <p class="text-gray-400 text-sm font-mono">No products found.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($products as $product)
                    <div class="group bg-white border border-gray-200 rounded-2xl p-4 shadow-sm hover:shadow-xl transition-all duration-500">
                        <a href="{{ route('product.detail', $product->slug) }}" class="block relative aspect-square bg-gray-50 overflow-hidden mb-6 rounded-xl border border-gray-100">
                            @if($product->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" 
                                     class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-in-out" 
                                     alt="{{ $product->name }}">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-300">
                                    <span class="text-xs font-bold tracking-widest uppercase">No Preview</span>
                                </div>
                            @endif

                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition duration-500"></div>
                            
                            @if($product->discount_percentage > 0)
                                <div class="absolute top-3 right-3 bg-black text-white text-[9px] font-bold px-2.5 py-1 uppercase tracking-widest rounded-full">
                                    Sale
                                </div>
                            @endif
                        </a>

                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">{{ $product->category->name ?? 'Asset' }}</p>
                                <h3 class="text-base font-bold text-gray-900 leading-snug group-hover:text-gray-600 transition">
                                    <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a>
                                </h3>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-100">
                                @if($product->discount_percentage > 0)
                                    @php
                                        $discountedPrice = $product->price - ($product->price * $product->discount_percentage / 100);
                                    @endphp
                                    <div class="flex flex-col">
                                        <span class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        <span class="text-sm font-bold text-gray-900">Rp {{ number_format($discountedPrice, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <span class="text-sm font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Newsletter / Footer CTA -->
    <div class="bg-white py-10 pb-10">
        <div class="container mx-auto px-4">
            <div class="relative bg-zinc-950 rounded-[2.5rem] overflow-hidden py-20 px-8 md:px-20 text-center">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_-20%,#3f3f46,transparent)] opacity-50"></div>
                <div class="relative z-10 max-w-3xl mx-auto">
                    <h2 class="text-3xl md:text-5xl font-bold text-white tracking-tight mb-6">Create Something Amazing.</h2>
                    <p class="text-zinc-400 mb-10 text-base md:text-lg font-light leading-relaxed">
                        Join our community of creators and start building better projects today. Access premium assets and exclusive resources.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        @auth
                            <a href="{{ route('history') }}" class="w-full sm:w-auto px-10 py-4 bg-white text-black font-bold uppercase tracking-widest text-xs rounded-full hover:bg-zinc-200 hover:scale-105 transition-all duration-300 shadow-xl shadow-white/5">
                                My Account
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-10 py-4 bg-white text-black font-bold uppercase tracking-widest text-xs rounded-full hover:bg-zinc-200 hover:scale-105 transition-all duration-300 shadow-xl shadow-white/5">
                                Get Started
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-10 py-4 bg-transparent text-white border border-zinc-800 font-bold uppercase tracking-widest text-xs rounded-full hover:bg-zinc-900 transition-all duration-300">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection