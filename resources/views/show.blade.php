<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>{{ $product->name }} - MyShop</title>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <a href="{{ route('home') }}" class="font-bold text-2xl text-blue-600 tracking-tighter hover:opacity-80 transition">
                <i class="fas fa-shopping-bag mr-2"></i>MyShop
            </a>
            <div class="space-x-4">
                <a href="{{ route('home') }}" class="text-gray-600 hover:text-blue-600 font-medium">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        
        <div class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-blue-600">Beranda</a> 
            <span class="mx-2">/</span> 
            <span class="text-gray-800 font-medium">{{ $product->name }}</span>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">
                
                <div class="bg-gray-100 flex items-center justify-center p-8 relative">
                    @if($product->image)
                        <div class="w-full aspect-square bg-white rounded-xl overflow-hidden shadow-sm">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($product->image) }}" 
                                 class="w-full h-full object-cover" 
                                 alt="{{ $product->name }}">
                        </div>
                    @else
                        <div class="w-full aspect-square bg-gray-200 rounded-xl flex items-center justify-center text-gray-400">
                            <i class="fas fa-image text-6xl"></i>
                        </div>
                    @endif

                    @if($product->stock <= 0)
                        <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                            <span class="bg-red-600 text-white px-6 py-2 rounded-full text-lg font-bold shadow-lg transform -rotate-12 border-2 border-white">
                                STOK HABIS
                            </span>
                        </div>
                    @endif
                </div>

                <div class="p-8 md:p-12 flex flex-col justify-center">
                    
                    <div class="mb-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                            Official Product
                        </span>
                        @if($product->stock > 0)
                            <span class="ml-2 bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                <i class="fas fa-check-circle mr-1"></i> Tersedia ({{ $product->stock }})
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                        {{ $product->name }}
                    </h1>

                    <div class="text-4xl font-bold text-blue-600 mb-6">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>

                    <div class="border-t border-gray-100 my-6"></div>

                    <div class="prose max-w-none text-gray-600 mb-8 leading-relaxed">
                        <h3 class="font-bold text-gray-900 mb-2 text-lg">Deskripsi Produk</h3>
                        <p>
                            {!! nl2br(e($product->description)) !!}
                        </p>
                    </div>

                    <div class="mt-auto">
                        @if($product->stock > 0)
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
    @csrf
    
    <div class="mb-6">
        <label class="font-bold text-gray-700 block mb-2">Atur Jumlah:</label>
        <div class="flex items-center gap-4">
            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden w-fit">
                <button type="button" onclick="updateQty(-1)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                    <i class="fas fa-minus text-xs"></i>
                </button>
                
                <input type="number" id="quantity_input" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
                       class="w-16 text-center border-x border-gray-300 py-2 focus:outline-none bg-white" readonly>
                
                <button type="button" onclick="updateQty(1)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 transition">
                    <i class="fas fa-plus text-xs"></i>
                </button>
            </div>
            <span class="text-sm text-gray-500">Stok tersisa: <span class="font-bold text-gray-700">{{ $product->stock }}</span></span>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row gap-3">
        <button type="submit" name="action" value="add_cart" class="flex-1 bg-white border-2 border-blue-600 text-blue-600 py-3 px-6 rounded-xl font-bold text-lg hover:bg-blue-50 transition flex items-center justify-center">
            <i class="fas fa-cart-plus mr-2"></i> + Keranjang
        </button>

        <button type="submit" name="action" value="buy_now" class="flex-1 bg-blue-600 border-2 border-blue-600 text-white py-3 px-6 rounded-xl font-bold text-lg hover:bg-blue-700 transition shadow-lg hover:shadow-blue-500/30 flex items-center justify-center">
            Beli Langsung
        </button>
    </div>
</form>
                        @else
                            <button disabled class="w-full bg-gray-300 text-gray-500 py-4 px-6 rounded-xl font-bold text-lg cursor-not-allowed flex items-center justify-center">
                                <i class="fas fa-ban mr-2"></i> Stok Habis
                            </button>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    <footer class="bg-gray-800 text-gray-300 py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p class="font-semibold text-white">MyShop</p>
            <p class="text-sm text-gray-400 mt-2">&copy; {{ date('Y') }} Official Store.</p>
        </div>
    </footer>

</body>
</html>
<script>
    function updateQty(change) {
        let input = document.getElementById('quantity_input');
        let currentQty = parseInt(input.value);
        let maxStock = parseInt("{{ $product->stock }}"); // Ambil stok dari database

        let newQty = currentQty + change;

        // Validasi: Tidak boleh kurang dari 1 dan tidak boleh lebih dari stok
        if (newQty >= 1 && newQty <= maxStock) {
            input.value = newQty;
        }
    }
</script>