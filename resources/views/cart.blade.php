@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Keranjang Belanja</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('cart') && count(session('cart')) > 0)
        <div class="flex flex-col lg:flex-row gap-8">
            <div class="flex-grow bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="py-4 px-4 w-10 text-center">
                                <input type="checkbox" id="select-all" checked onclick="toggleAll(this)" 
                                       class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                            </th>
                            <th class="text-left py-4 px-2 text-gray-600 font-semibold uppercase text-sm">Produk</th>
                            <th class="text-center py-4 px-2 text-gray-600 font-semibold uppercase text-sm">Harga</th>
                            <th class="text-center py-4 px-2 text-gray-600 font-semibold uppercase text-sm">Qty</th>
                            <th class="text-center py-4 px-2 text-gray-600 font-semibold uppercase text-sm hidden sm:table-cell">Total</th>
                            <th class="text-center py-4 px-2 text-gray-600 font-semibold uppercase text-sm">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach(session('cart') as $id => $details)
                            @php $subtotal = $details['price'] * $details['quantity']; @endphp
                            
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" checked 
                                           class="item-checkbox w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer"
                                           data-id="{{ $id }}" 
                                           data-subtotal="{{ $subtotal }}"
                                           onchange="recalculateTotal()">
                                </td>
                                
                                <td class="py-4 px-2">
                                    <div class="flex items-center">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($details['image']) }}" class="w-12 h-12 md:w-16 md:h-16 object-cover rounded border mr-3 hidden sm:block">
                                        <div>
                                            <span class="font-bold text-gray-800 block">{{ $details['name'] }}</span>
                                            <span class="text-xs text-gray-500 sm:hidden">x{{ $details['quantity'] }} @ Rp {{ number_format($details['price'],0,',','.') }}</span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="py-4 px-2 text-center text-sm">
                                    Rp {{ number_format($details['price'], 0, ',', '.') }}
                                </td>
                                
                                <td class="py-4 px-2 text-center">
                                    <span class="bg-gray-100 px-3 py-1 rounded font-bold text-sm">{{ $details['quantity'] }}</span>
                                </td>
                                
                                <td class="py-4 px-2 text-center font-bold text-blue-600 hidden sm:table-cell">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                                
                                <td class="py-4 px-2 text-center">
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="text-red-400 hover:text-red-600 transition" onclick="return confirm('Hapus barang ini?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow p-6 sticky top-24">
                    <h2 class="text-lg font-bold mb-4 border-b pb-2">Ringkasan Pesanan</h2>
                    
                    <div class="flex justify-between mb-2 text-gray-600">
                        <span>Item Dipilih</span>
                        <span id="selected-count">0 barang</span>
                    </div>
                    
                    <div class="flex justify-between mb-6 text-xl font-bold text-gray-800">
                        <span>Total Bayar</span>
                        <span id="grand-total" class="text-blue-600">Rp 0</span>
                    </div>
                    
                    <form action="{{ route('cart.checkout') }}" method="POST" id="checkout-form">
                        @csrf
                        <input type="hidden" name="selected_products" id="selected-products-input">
                        
                        <button type="button" onclick="submitCheckout()" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center font-bold py-3 rounded-xl transition shadow-lg mb-3 flex items-center justify-center gap-2">
                            <i class="fas fa-credit-card text-xl"></i> Bayar Sekarang
                        </button>
                    </form>
                    
                    <p class="text-xs text-gray-400 text-center mt-2">
                        *Hanya barang yang dicentang yang akan diproses pembayaran.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-xl shadow">
            <i class="fas fa-shopping-cart text-6xl text-gray-200 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-600 mb-2">Keranjang belanja kosong</h2>
            <p class="text-gray-500 mb-6">Yuk isi dengan barang-barang impianmu!</p>
            <a href="{{ route('home') }}" class="bg-blue-600 text-white px-6 py-3 rounded-full font-bold hover:bg-blue-700 transition">
                Mulai Belanja
            </a>
        </div>
    @endif
</div>

<script>
    // 1. Fungsi Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // 2. Fungsi Hitung Ulang Total
    function recalculateTotal() {
        let total = 0;
        let count = 0;
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const selectAllBox = document.getElementById('select-all');
        let allChecked = true;

        checkboxes.forEach(box => {
            if (box.checked) {
                total += parseInt(box.getAttribute('data-subtotal'));
                count++;
            } else {
                allChecked = false;
            }
        });

        if(selectAllBox) selectAllBox.checked = allChecked;

        document.getElementById('grand-total').innerText = formatRupiah(total);
        document.getElementById('selected-count').innerText = count + " barang";
    }

    // 3. Fungsi Select All
    function toggleAll(source) {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(box => {
            box.checked = source.checked;
        });
        recalculateTotal();
    }

    // 4. FUNGSI BARU: Submit ke Laravel (Bukan WA lagi)
    function submitCheckout() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        
        if (checkboxes.length === 0) {
            alert("Pilih minimal satu barang untuk checkout!");
            return;
        }

        // Kumpulkan ID barang yang dicentang
        let selectedIds = [];
        checkboxes.forEach(box => {
            selectedIds.push(box.getAttribute('data-id'));
        });

        // Masukkan ke input hidden dan submit form
        document.getElementById('selected-products-input').value = selectedIds.join(',');
        document.getElementById('checkout-form').submit();
    }

    // Jalankan saat load
    document.addEventListener("DOMContentLoaded", function() {
        recalculateTotal();
    });
</script>
@endsection