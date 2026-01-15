@extends('layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Riwayat Pesanan Saya</h1>

    @if($orders->isEmpty())
        <div class="bg-white p-8 rounded-xl shadow text-center">
            <p class="text-gray-500">Kamu belum pernah memesan jasa apapun.</p>
            <a href="{{ route('home') }}" class="text-blue-600 font-bold hover:underline mt-2 inline-block">Cari Jasa Sekarang</a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($orders as $order)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center flex-wrap gap-2">
                    <div>
                        <span class="text-sm text-gray-500">Order ID:</span>
                        <span class="font-mono font-bold text-gray-800">{{ $order->order_number }}</span>
                        <span class="text-xs text-gray-400 ml-2">{{ $order->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div>
                        @if($order->status == 'pending')
                            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-bold animate-pulse">Menunggu Pembayaran</span>
                        @elseif($order->status == 'success')
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-bold">Lunas / Proses</span>
                        @elseif($order->status == 'failed')
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-bold">Dibatalkan</span>
                        @endif
                    </div>
                </div>

                <div class="p-6 cursor-pointer" onclick="window.location='{{ route('history.detail', $order->id) }}'">
                    @foreach($order->items as $item)
                    <div class="flex items-center gap-4 mb-4 last:mb-0">
                        <div class="w-16 h-16 bg-gray-100 rounded border overflow-hidden flex-shrink-0">
                            @if($item->product && $item->product->image)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center h-full text-gray-400"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $item->product_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500">Total Tagihan</p>
                        <p class="font-bold text-lg text-blue-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="flex gap-3 items-center">
                        <a href="{{ route('history.detail', $order->id) }}" class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition border border-transparent hover:border-blue-100">
                            Lihat Detail
                        </a>

                        @if($order->status == 'pending')
                            <button onclick="payAgain('{{ $order->snap_token }}')" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                                Bayar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script>
    function payAgain(token) {
        window.snap.pay(token, {
            onSuccess: function(result){ location.reload(); },
            onPending: function(result){ location.reload(); },
            onError: function(result){ alert("Pembayaran Gagal"); }
        });
    }
</script>
@endsection