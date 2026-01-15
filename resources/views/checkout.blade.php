@extends('layouts.app')

@section('title', 'Detail Pemesanan')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Detail Pemesanan Jasa</h1>

        <form action="{{ route('cart.payment') }}" method="POST">
            @csrf
            <input type="hidden" name="selected_products" value="{{ implode(',', $selectedItemIds) }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-6">
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle text-blue-600 mr-2"></i> Data Pemesan
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-600 text-sm font-bold mb-2">Nama Lengkap</label>
                                <input type="text" name="customer_name" class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200" placeholder="Contoh: Fachri Mufidan" required>
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
                            <i class="fas fa-edit text-blue-600 mr-2"></i> Detail Pesanan / Catatan
                        </h2>
                        <textarea name="note" rows="3" class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200" placeholder="Tuliskan detail pesanan Anda, misal: Nama domain yang diinginkan, referensi desain, atau link file materi..."></textarea>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Item Jasa</h2>
                        @foreach($itemsToBuy as $item)
                        <div class="flex gap-4 mb-4 border-b last:border-0 pb-4 last:pb-0 border-gray-100">
                            <div class="w-16 h-16 bg-gray-100 rounded-md overflow-hidden border">
                                @if($item['image'])
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item['image']) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $item['name'] }}</h3>
                                <p class="text-gray-500 text-sm">Qty: {{ $item['quantity'] }}</p>
                                <p class="font-bold text-blue-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>

                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 sticky top-24">
                        <h2 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Pembayaran</h2>
                        
                        <div class="space-y-3 text-sm text-gray-600 mb-4">
                            <div class="flex justify-between">
                                <span>Subtotal Jasa</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-green-600">
                                <span>Biaya Layanan</span>
                                <span>Gratis</span>
                            </div>
                        </div>

                        <div class="border-t pt-4 flex justify-between items-center mb-6">
                            <span class="font-bold text-lg text-gray-800">Total Tagihan</span>
                            <span class="font-bold text-xl text-blue-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow transition transform active:scale-95">
                            Lanjut Pembayaran <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection