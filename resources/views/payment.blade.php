@extends('layouts.app')

@section('title', 'Selesaikan Pembayaran')

@section('content')
<div class="container mx-auto px-4 py-12 flex justify-center">
    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-lg w-full text-center">
        <div class="mb-6 animate-bounce">
            <i class="fas fa-check-circle text-6xl text-green-500"></i>
        </div>
        <h2 class="text-2xl font-bold mb-2">Pesanan Dibuat!</h2>
        <p class="text-gray-500 mb-6">ID Order: <span class="font-mono font-bold text-gray-800">{{ $order->order_number }}</span></p>

        <div class="text-left bg-blue-50 p-4 rounded-xl mb-6 text-sm text-blue-800">
            <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->email }}</p>
            <p class="mt-2 text-xs opacity-75">*Silakan selesaikan pembayaran agar pesanan segera diproses.</p>
        </div>

        <div class="bg-gray-50 p-4 rounded-xl mb-8">
            <p class="text-sm text-gray-500">Total Tagihan</p>
            <p class="text-3xl font-extrabold text-indigo-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
        </div>

        <button id="pay-button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:scale-105">
            Bayar Sekarang
        </button>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
<script type="text/javascript">
    // Trigger popup otomatis atau via tombol
    document.getElementById('pay-button').onclick = function(){
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                alert("Pembayaran Berhasil!");
                window.location.href = "{{ route('home') }}"; 
            },
            onPending: function(result){
                alert("Menunggu pembayaran...");
                window.location.href = "{{ route('home') }}";
            },
            onError: function(result){
                alert("Pembayaran Gagal!");
            }
        });
    };
    
    // Opsional: Langsung munculkan popup saat halaman dimuat
    // document.getElementById('pay-button').click();
</script>
@endsection