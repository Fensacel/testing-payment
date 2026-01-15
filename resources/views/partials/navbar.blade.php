<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
            <div class="bg-blue-600 text-white w-10 h-10 flex items-center justify-center rounded-xl shadow-lg shadow-blue-500/30 transform group-hover:rotate-12 transition duration-300">
                <i class="fas fa-shopping-bag text-lg"></i>
            </div>
            <span class="font-extrabold text-2xl text-gray-800 tracking-tight group-hover:text-blue-600 transition">
                MyShop
            </span>
        </a>

        <div class="flex items-center space-x-4 md:space-x-8">
            
            
            <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-blue-600 transition group">
                <i class="fas fa-shopping-cart text-xl group-hover:scale-110 transition-transform"></i>
                @if(count((array) session('cart')) > 0)
                    <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-bold rounded-full h-5 w-5 flex items-center justify-center shadow-sm border-2 border-white transform translate-x-1 -translate-y-1">
                        {{ count((array) session('cart')) }}
                    </span>
                @endif
            </a>

            <div class="h-6 w-px bg-gray-200"></div>

            @auth
                <div class="relative group h-full flex items-center cursor-pointer">
                    
                    <button class="flex items-center gap-3 focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-700 leading-none group-hover:text-blue-600 transition">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 mt-1">Member</p>
                        </div>
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 group-hover:bg-blue-50 group-hover:text-blue-600 transition ring-2 ring-transparent group-hover:ring-blue-100">
                            <i class="fas fa-user"></i>
                        </div>
                    </button>

                    <div class="absolute top-full right-0 w-56 pt-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 ease-in-out">
                        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden transform group-hover:translate-y-0 translate-y-2 transition-transform duration-300">
                            
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 sm:hidden">
                                <p class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('history') }}" class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-3">
                                <i class="fas fa-history text-gray-400"></i> Riwayat Pesanan
                            </a>

                            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-gray-600 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-3">
                                <i class="fas fa-cog text-gray-400"></i> Pengaturan Akun
                            </a>
                            
                            <div class="border-t border-gray-100"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 text-sm text-red-500 hover:bg-red-50 transition flex items-center gap-3">
                                    <i class="fas fa-sign-out-alt"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:text-blue-600 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="px-6 py-2.5 text-sm font-bold bg-blue-600 text-white rounded-full shadow-lg shadow-blue-500/30 hover:bg-blue-700 hover:scale-105 transition transform">Daftar</a>
                </div>
            @endauth

        </div>
    </div>
</nav>