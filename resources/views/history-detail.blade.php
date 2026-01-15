@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->order_number)

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4">
        <a href="{{ route('history') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 mb-6 font-medium transition">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat
        </a>

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800">Detail Pesanan</h1>
            
            @if($order->status == 'success')
                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold text-sm border border-green-200 shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Lunas
                </span>
            @elseif($order->status == 'pending')
                <span class="bg-orange-100 text-orange-700 px-4 py-2 rounded-full font-bold text-sm border border-orange-200 shadow-sm animate-pulse">
                    <i class="fas fa-clock mr-1"></i> Menunggu Pembayaran
                </span>
            @else
                <span class="bg-red-100 text-red-700 px-4 py-2 rounded-full font-bold text-sm border border-red-200 shadow-sm">
                    Gagal / Dibatalkan
                </span>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6">
                
                @if($order->status == 'pending')
                <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-xl shadow-sm flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-orange-500 mt-1"></i>
                    <div>
                        <p class="font-bold text-orange-800">Segera Selesaikan Pembayaran!</p>
                        <p class="text-sm text-orange-700 mt-1">
                            Batas pembayaran Anda adalah 
                            <span class="font-bold bg-white px-2 py-0.5 rounded border border-orange-200">
                                {{ $order->created_at->addDay()->format('d F Y, H:i') }} WIB
                            </span>
                        </p>
                        <p class="text-xs text-orange-600 mt-1">Pesanan akan otomatis dibatalkan jika melewati batas waktu.</p>
                    </div>
                </div>
                @endif

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-600"></i> Informasi Pemesan
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide">No. Order</p>
                            <p class="font-bold text-gray-800 font-mono text-base">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide">Waktu Pemesanan</p>
                            <p class="font-bold text-gray-800">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide">Nama</p>
                            <p class="font-bold text-gray-800">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs uppercase tracking-wide">Kontak</p>
                            <p class="font-bold text-gray-800">{{ $order->email }} <br> {{ $order->customer_phone }}</p>
                        </div>
                    </div>

                    @if($order->note)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <p class="text-gray-500 text-xs uppercase tracking-wide mb-2">Catatan Pesanan:</p>
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-gray-700 italic text-sm">
                                <i class="fas fa-quote-left text-gray-300 mr-2"></i>{{ $order->note }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 flex items-center gap-2">
                        <i class="fas fa-box text-blue-600"></i> Rincian Item
                    </h2>
                    
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                        <div class="flex gap-4 p-3 hover:bg-gray-50 rounded-lg transition border border-transparent hover:border-gray-100">
                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden border flex-shrink-0">
                                @if($item->product && $item->product->image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->image) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-400"><i class="fas fa-image"></i></div>
                                @endif
                            </div>
                            
                            <div class="flex-grow flex flex-col justify-center">
                                <h3 class="font-bold text-gray-800 text-base">{{ $item->product_name }}</h3>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                    <p class="font-bold text-blue-600">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-800 mb-6 border-b pb-2">Ringkasan Pembayaran</h2>
                    
                    <div class="space-y-3 text-sm text-gray-600 mb-6">
                        <div class="flex justify-between">
                            <span>Subtotal Produk</span>
                            <span class="font-medium">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>Biaya Layanan</span>
                            <span class="font-medium">Gratis</span>
                        </div>
                        <div class="border-t border-dashed my-2"></div>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800 text-lg">Total Tagihan</span>
                            <span class="font-bold text-xl text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($order->status == 'pending')
                        <div class="bg-orange-50 text-orange-800 text-xs font-bold text-center py-2 rounded-lg mb-4 border border-orange-100">
                            Bayar sebelum: {{ $order->created_at->addDay()->format('d M, H:i') }}
                        </div>

                        <button id="pay-button" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1">
                            Bayar Sekarang <i class="fas fa-wallet ml-2"></i>
                        </button>
                    @elseif($order->status == 'success')
                        <button disabled class="w-full bg-gray-100 text-green-600 border border-green-200 font-bold py-3 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fas fa-check-circle"></i> Pembayaran Berhasil
                        </button>
                    @else
                        <button disabled class="w-full bg-red-50 text-red-500 border border-red-100 font-bold py-3 rounded-xl cursor-not-allowed">
                            Pesanan Dibatalkan
                        </button>
                    @endif
                    
                    <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                        <a href="https://wa.me/6289603166370?text=Halo Admin, saya butuh bantuan untuk Order ID: {{ $order->order_number }}" target="_blank" class="inline-flex items-center text-gray-500 hover:text-green-600 font-medium text-sm transition">
                            <i class="fab fa-whatsapp text-lg mr-2 text-green-500"></i> Butuh Bantuan? Chat Admin
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if($order->status == 'pending')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        window.snap.pay('{{ $order->snap_token }}', {
            onSuccess: function(result){ 
                Swal.fire("Berhasil!", "Pembayaran diterima.", "success").then(() => location.reload());
            },
            onPending: function(result){ 
                Swal.fire("Menunggu!", "Silakan selesaikan pembayaran.", "info").then(() => location.reload());
            },
            onError: function(result){ 
                Swal.fire("Gagal!", "Pembayaran gagal.", "error");
            },
            onClose: function(){
                Swal.fire("Batal", "Anda menutup pop-up pembayaran.", "warning");
            }
        });
    };
</script>
@endif

@endsection