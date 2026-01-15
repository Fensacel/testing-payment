<nav class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="{{ route('home') }}" class="font-bold text-2xl text-blue-600 tracking-tighter">
            <i class="fas fa-shopping-bag mr-2"></i>MyShop
        </a>
        <div class="space-x-4">
            <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-blue-600 font-medium relative">
                <i class="fas fa-shopping-cart text-xl"></i>
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                    {{ count((array) session('cart')) }}
                </span>
            </a>
        </div>
    </div>
</nav>