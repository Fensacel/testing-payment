@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Back Button -->
    <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-black transition mb-4">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
    </a>
    
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
                                       class="w-5 h-5 text-black rounded border-gray-300 focus:ring-gray-500 cursor-pointer">
                            </th>
                            <th class="text-left py-4 px-2 text-gray-600 font-semibold uppercase text-sm">Produk</th>
                            <th class="text-center py-4 px-2 text-gray-600 font-semibold uppercase text-sm">Harga</th>
                            <th class="text-center py-4 px-2 text-gray-600 font-semibold uppercase text-sm">Qty</th>
                            <th class="text-center py-4 px-2 text-gray-600 font-semibold uppercase text-sm hidden sm:table-cell">Total</th>
                            <th class="text-center py-4 px-2 text-gray-600 font-semibold uppercase text-sm">Hapus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($syncedCart as $id => $details)
                            @php 
                                $price = $details['price'];
                                $discount = $details['discount_percentage'] ?? 0;
                                if ($discount > 0) {
                                    $price = $price - ($price * $discount / 100);
                                }
                                $subtotal = $price * $details['quantity'];
                            @endphp
                            
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-4 text-center">
                                    @php
                                        $originalTotal = $details['price'] * $details['quantity'];
                                        $productDiscount = $originalTotal - $subtotal;
                                    @endphp
                                    <input type="checkbox" checked 
                                           class="item-checkbox w-5 h-5 text-black rounded border-gray-300 focus:ring-gray-500 cursor-pointer"
                                           data-id="{{ $id }}" 
                                           data-original="{{ $originalTotal }}"
                                           data-discount="{{ $productDiscount }}"
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
                                    @if(isset($details['discount_percentage']) && $details['discount_percentage'] > 0)
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="text-xs text-red-500 font-bold">-{{ number_format($details['discount_percentage'], 0) }}%</span>
                                            <span class="text-xs text-gray-400 line-through">Rp {{ number_format($details['price'], 0, ',', '.') }}</span>
                                            <span class="font-bold text-black">Rp {{ number_format($price, 0, ',', '.') }}</span>
                                        </div>
                                    @else
                                        Rp {{ number_format($details['price'], 0, ',', '.') }}
                                    @endif
                                </td>
                                
                                <td class="py-4 px-2 text-center">
                                    <div class="inline-flex items-center bg-gray-100 rounded-lg border border-gray-200">
                                        <button onclick="updateQuantity('{{ $id }}', -1)" class="w-8 h-8 hover:bg-gray-200 transition flex items-center justify-center font-bold text-gray-600">
                                            -
                                        </button>
                                        <span class="px-3 py-1 font-bold text-sm min-w-[40px] text-center" id="qty-{{ $id }}">{{ $details['quantity'] }}</span>
                                        <button onclick="updateQuantity('{{ $id }}', 1)" class="w-8 h-8 hover:bg-gray-200 transition flex items-center justify-center font-bold text-gray-600">
                                            +
                                        </button>
                                    </div>
                                </td>
                                
                                <td class="py-4 px-2 text-center font-bold text-black hidden sm:table-cell">
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

                    @php
                        $totalBeforeDiscount = 0;
                        $totalDiscount = 0;
                        foreach($syncedCart as $id => $details) {
                            $originalPrice = $details['price'] * $details['quantity'];
                            $totalBeforeDiscount += $originalPrice;
                            
                            if (isset($details['discount_percentage']) && $details['discount_percentage'] > 0) {
                                $discountAmount = $originalPrice * ($details['discount_percentage'] / 100);
                                $totalDiscount += $discountAmount;
                            }
                        }
                        $grandTotal = $totalBeforeDiscount - $totalDiscount;
                    @endphp

                    <div class="flex justify-between mb-2 text-gray-600 text-sm">
                        <span>Subtotal</span>
                        <span id="subtotal-display">Rp {{ number_format($totalBeforeDiscount, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between mb-2 text-green-600 font-medium text-sm" id="product-discount-row" style="display: {{ $totalDiscount > 0 ? 'flex' : 'none' }}">
                        <span>Diskon Produk</span>
                        <span id="product-discount-display">- Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
                    </div>
                    
                    @if(session('promo_code'))
                    <div class="flex justify-between mb-2 text-green-600 font-medium text-sm" id="promo-discount-row">
                        <span>Diskon Promo</span>
                        <span id="promo-discount-display">- Rp {{ number_format(session('promo_code')['discount_amount'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between mb-6 text-xl font-bold text-gray-800 border-t pt-3 mt-3">
                        <span>Total Bayar</span>
                        <span id="grand-total" class="text-black">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <!-- Promo Code Section -->
                    <div class="border-t pt-4 mb-4">
                        @if(session('promo_code'))
                            @php $promo = session('promo_code'); @endphp
                            <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-tag text-green-600"></i>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $promo['code'] }}</p>
                                        <p class="text-sm text-gray-600">Diskon Rp {{ number_format($promo['discount_amount'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <form action="{{ route('cart.removePromo') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('cart.applyPromo') }}" method="POST" class="mb-3">
                                @csrf
                                <label class="block text-sm font-medium text-gray-700 mb-2">Punya Kode Promo?</label>
                                <div class="flex gap-2">
                                    <input type="text" name="promo_code" 
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-800 uppercase" 
                                           placeholder="Masukkan kode promo"
                                           maxlength="50">
                                    <button type="submit" class="bg-black hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition">
                                        Pakai
                                    </button>
                                </div>
                            </form>
                        @endif
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
            <a href="{{ route('home') }}" class="bg-black text-white px-6 py-3 rounded-full font-bold hover:bg-gray-900 transition">
                Mulai Belanja
            </a>
        </div>
    @endif
    
    <!-- Recommended Products Section -->
    @if(isset($recommendedProducts) && $recommendedProducts->count() > 0)
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Rekomendasi Produk Lainnya</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($recommendedProducts as $product)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition group">
                <a href="{{ route('product.detail', $product->slug) }}" class="block">
                    <div class="aspect-square bg-gray-100 overflow-hidden relative">
                        @if($product->image)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300" 
                                 alt="{{ $product->name }}">
                        @else
                            <div class="flex items-center justify-center h-full text-gray-400">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif
                        @if($product->discount_percentage > 0)
                            <div class="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                                -{{ number_format($product->discount_percentage, 0) }}%
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 group-hover:text-black transition">
                            {{ $product->name }}
                        </h3>
                        <div class="flex items-baseline gap-2">
                            @if($product->discount_percentage > 0)
                                @php
                                    $discountedPrice = $product->price - ($product->price * $product->discount_percentage / 100);
                                @endphp
                                <span class="text-sm text-gray-400 line-through">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                                <span class="text-lg font-bold text-black">
                                    Rp {{ number_format($discountedPrice, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-lg font-bold text-black">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                        <div class="mt-3">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-box mr-1"></i> Stok: {{ $product->stock }}
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    // 1. Fungsi Format Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    // 2. Fungsi Hitung Ulang Total - COMPREHENSIVE VERSION
    function recalculateTotal() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const selectAllBox = document.getElementById('select-all');
        let allChecked = true;
        let count = 0;
        
        // Calculate totals for checked items only
        let subtotalBeforeDiscount = 0;
        let totalProductDiscount = 0;
        
        checkboxes.forEach(box => {
            if (box.checked) {
                const original = parseFloat(box.getAttribute('data-original'));
                const discount = parseFloat(box.getAttribute('data-discount'));
                
                subtotalBeforeDiscount += original;
                totalProductDiscount += discount;
                count++;
            } else {
                allChecked = false;
            }
        });
        
        // Update select all checkbox
        if(selectAllBox) selectAllBox.checked = allChecked;
        
        // Calculate subtotal after product discount
        const subtotalAfterProductDiscount = subtotalBeforeDiscount - totalProductDiscount;
        
        // Get promo discount from session (already calculated)
        let promoDiscount = 0;
        const promoDiscountRow = document.getElementById('promo-discount-row');
        if (promoDiscountRow) {
            // Promo discount is already calculated and stored in session
            // We just need to get it from the display
            const promoAmount = {{ session('promo_code')['discount_amount'] ?? 0 }};
            promoDiscount = promoAmount;
        }
        
        // Calculate grand total
        const grandTotal = subtotalAfterProductDiscount - promoDiscount;
        
        // Update display
        document.getElementById('selected-count').innerText = count + " barang";
        document.getElementById('subtotal-display').innerText = formatRupiah(subtotalBeforeDiscount);
        
        const productDiscountRow = document.getElementById('product-discount-row');
        if (totalProductDiscount > 0) {
            productDiscountRow.style.display = 'flex';
            document.getElementById('product-discount-display').innerText = '- ' + formatRupiah(totalProductDiscount);
        } else {
            productDiscountRow.style.display = 'none';
        }
        
        if (promoDiscountRow && promoDiscount > 0) {
            promoDiscountRow.style.display = 'flex';
            document.getElementById('promo-discount-display').innerText = '- ' + formatRupiah(promoDiscount);
        } else if (promoDiscountRow) {
            promoDiscountRow.style.display = 'none';
        }
        
        document.getElementById('grand-total').innerText = formatRupiah(grandTotal);
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

    // 5. UPDATE QUANTITY
    function updateQuantity(productId, change) {
        const qtyElement = document.getElementById('qty-' + productId);
        let currentQty = parseInt(qtyElement.textContent);
        let newQty = currentQty + change;

        if (newQty < 1) {
            if (confirm('Hapus produk ini dari keranjang?')) {
                window.location.href = '/cart/remove?id=' + productId;
            }
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/cart/update-quantity';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = productId;
        form.appendChild(idInput);
        
        const qtyInput = document.createElement('input');
        qtyInput.type = 'hidden';
        qtyInput.name = 'quantity';
        qtyInput.value = newQty;
        form.appendChild(qtyInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    // Jalankan saat load
    document.addEventListener("DOMContentLoaded", function() {
        recalculateTotal();
    });
</script>
@endsection