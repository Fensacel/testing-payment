<footer class="bg-gray-900 text-white pt-16 pb-8 mt-auto border-t border-gray-800">
    <div class="container mx-auto px-4">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
            
            <div>
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-6">
                    <img src="{{ asset('logo.png') }}" alt="Fachri" class="h-8 w-auto">
                </a>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    Platform digital terpercaya untuk mewujudkan ide kreatifmu. Kami menyediakan layanan jasa dan produk digital berkualitas tinggi.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white transition transform hover:scale-110">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white transition transform hover:scale-110">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-gray-700 hover:text-white transition transform hover:scale-110">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-6">Tautan Cepat</h3>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> Beranda</a></li>
                    <li><a href="{{ route('home') }}#products" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> Katalog Produk</a></li>
                    @auth
                        <li><a href="{{ route('history') }}" class="hover:text-white transition flex items-center gap-2"><i class="fas fa-chevron-right text-xs"></i> Riwayat Pesanan</a></li>
                    @endauth
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-6">Layanan Kami</h3>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Pembuatan Website</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Desain Grafis</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Produk Digital</li>
                    <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Konsultasi IT</li>
                </ul>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-6">Hubungi Kami</h3>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-gray-400 mt-1"></i>
                        <span>Slawi, Tegal, Jawa Tengah<br>Indonesia 52411</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-envelope text-gray-400"></i>
                        <span>support@fachri.id</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fas fa-phone text-gray-400"></i>
                        <span>+62 812-3456-7890</span>
                    </li>
                </ul>
            </div>

        </div>

        <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-500 text-sm">
                &copy; {{ date('Y') }} <span class="text-white font-bold">Fachri</span>. All rights reserved.
            </p>
            <div class="flex gap-6 text-sm text-gray-500">
                <a href="#" class="hover:text-white transition">Privacy Policy</a>
                <a href="#" class="hover:text-white transition">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>