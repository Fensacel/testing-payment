@extends('admin.layouts.app')

@section('title', 'Order Detail')
@section('page-title', 'Order Detail')
@section('page-description', 'View order #' . $order->order_number)

@section('content')
<div class="space-y-6">
    
    <!-- Back Button & Actions -->
    <div class="flex justify-between items-center">
        <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center text-gray-600 hover:text-black transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
        
        @if($order->status === 'pending')
        <form action="{{ route('admin.orders.sendReminder', $order) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition shadow-sm">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Reminder
            </button>
        </form>
        @endif
    </div>
    
    <!-- Order Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Pesanan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">No. Order</p>
                <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Tanggal Pemesanan</p>
                <p class="font-semibold text-gray-900">{{ $order->created_at->format('d F Y, H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status Pembayaran</p>
                <div class="mt-1">
                    @if($order->status === 'success')
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                    @elseif($order->status === 'pending')
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Pembayaran</span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Gagal</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- PAYMENT METHOD INFO (MOVED HERE) -->
        @if($order->payment_type && $order->payment_info)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-2">Metode Pembayaran</p>
                
                @if(isset($order->payment_info['bank']))
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-university text-blue-600"></i>
                        <span class="font-bold text-gray-900">Bank Transfer - {{ $order->payment_info['bank'] }}</span>
                    </div>
                    @if($order->status == 'pending')
                    <div class="bg-gray-50 px-4 py-3 rounded border border-gray-200 inline-block mt-2">
                        <div class="flex items-center gap-4">
                            <span class="font-mono font-bold text-xl text-blue-700 tracking-wider">{{ $order->payment_info['va_number'] }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $order->payment_info['va_number'] }}')" class="text-xs text-gray-500 hover:text-blue-600 bg-white border border-gray-300 px-2 py-1 rounded shadow-sm">
                                <i class="fas fa-copy mr-1"></i> Copy
                            </button>
                        </div>
                    </div>
                    @endif
                @elseif(isset($order->payment_info['store']))
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-store text-blue-600"></i>
                        <span class="font-bold text-gray-900">{{ $order->payment_info['store'] }}</span>
                    </div>
                    @if($order->status == 'pending')
                    <div class="bg-gray-50 px-4 py-3 rounded border border-gray-200 inline-block mt-2">
                        <span class="block text-xs text-gray-500 mb-1">Kode Pembayaran:</span>
                        <span class="font-mono font-bold text-xl text-blue-700 tracking-wider">{{ $order->payment_info['payment_code'] }}</span>
                    </div>
                    @endif
                @elseif(isset($order->payment_info['bill_key']))
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-file-invoice text-blue-600"></i>
                        <span class="font-bold text-gray-900">Mandiri Bill</span>
                    </div>
                    @if($order->status == 'pending')
                    <div class="bg-gray-50 px-4 py-3 rounded border border-gray-200 inline-block mt-2 min-w-[300px]">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs text-gray-500">Bill Key:</span>
                            <span class="font-mono font-bold text-gray-900">{{ $order->payment_info['bill_key'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">Biller Code:</span>
                            <span class="font-mono font-bold text-gray-900">{{ $order->payment_info['biller_code'] }}</span>
                        </div>
                    </div>
                    @endif
                @elseif($order->payment_type == 'qris' || $order->payment_type == 'gopay')
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-qrcode text-blue-600"></i>
                        <span class="font-bold text-gray-900">QRIS / GoPay</span>
                    </div>
                    @if($order->status == 'pending')
                        @if(isset($order->payment_info['qr_code_url']) && $order->payment_info['qr_code_url'])
                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-2">Scan QR Code:</p>
                                <img src="{{ $order->payment_info['qr_code_url'] }}" class="w-48 h-48 object-contain border rounded bg-white p-2">
                            </div>
                        @else
                            <div class="bg-yellow-50 px-3 py-2 rounded text-yellow-800 text-xs flex gap-2 items-center mt-2 inline-flex">
                                <i class="fas fa-info-circle"></i>
                                <span>QR Code belum di-generate user.</span>
                            </div>
                        @endif
                    @endif
                @endif
            </div>
        @endif
    </div>
    
    <!-- Customer Information Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Pelanggan</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Nama Lengkap</p>
                <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">No. Telepon</p>
                <p class="font-semibold text-gray-900">{{ $order->customer_phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Email</p>
                <p class="font-semibold text-gray-900">{{ $order->email }}</p>
            </div>
        </div>
        @if($order->note)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-500 mb-1">Catatan Pesanan</p>
            <p class="text-gray-900">{{ $order->note }}</p>
        </div>
        @endif
    </div>
    
    <!-- Order Items Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Daftar Produk</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Satuan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($item->product && $item->product->image)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($item->product->image) }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-16 h-16 object-cover rounded border border-gray-200">
                                @else
                                    <div class="w-16 h-16 bg-gray-100 rounded border border-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <p class="font-medium text-gray-900">{{ $item->product_name }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-900">{{ $item->quantity }} pcs</td>
                        <td class="px-6 py-4 text-right text-gray-900">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-medium text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Payment Summary Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Rincian Pembayaran</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <p class="text-gray-600">Subtotal Produk</p>
                <p class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</p>
            </div>
            
            @if($order->promo_code_id && $order->promoCode)
            <div class="flex justify-between items-center bg-green-50 -mx-2 px-2 py-2 rounded">
                <div>
                    <p class="text-gray-600">Kode Promo</p>
                    <p class="text-sm font-mono font-bold text-green-700">{{ $order->promoCode->code }}</p>
                </div>
                <p class="font-semibold text-green-600">- Rp {{ number_format($order->promo_discount, 0, ',', '.') }}</p>
            </div>
            @elseif($order->promo_discount > 0)
            <div class="flex justify-between items-center">
                <p class="text-gray-600">Diskon Promo</p>
                <p class="font-semibold text-green-600">- Rp {{ number_format($order->promo_discount, 0, ',', '.') }}</p>
            </div>
            @endif
            
            <div class="flex justify-between items-center">
                <p class="text-gray-600">Biaya Admin (1%)</p>
                <p class="font-semibold text-gray-900">Rp {{ number_format($adminFee, 0, ',', '.') }}</p>
            </div>
            
            <div class="pt-3 border-t-2 border-gray-300">
                <div class="flex justify-between items-center">
                    <p class="text-lg font-bold text-gray-900">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
