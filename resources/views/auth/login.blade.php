<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left Side - Form -->
        <div class="w-full lg:w-5/12 flex items-center justify-center p-8 lg:p-16 bg-white relative">
            <div class="w-full max-w-md animate-fade-in-up">
                <!-- Logo -->
                <div class="mb-12">
                    <div class="flex items-center space-x-3">
                        <img src="{{ asset('logo.png') }}" alt="Fachri" class="h-12 w-auto rounded-xl shadow-lg">
                        <span class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-black bg-clip-text text-transparent">Fachri</span>
                    </div>
                </div>

                <!-- Header -->
                <div class="mb-10">
                    <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                        Selamat datang<br/>kembali
                    </h1>
                    <p class="text-gray-600 text-lg">
                        Masuk untuk melanjutkan pengalaman belanja Anda
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-6" :status="session('status')" />

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">Alamat email</label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus
                            class="input-modern w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-indigo-500 focus:bg-white focus:outline-none text-gray-900 placeholder-gray-400 @error('email') border-red-400 @enderror"
                            placeholder="Masukkan email Anda">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label for="password" class="block text-sm font-semibold text-gray-700">Kata sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required
                            class="input-modern w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-indigo-500 focus:bg-white focus:outline-none text-gray-900 placeholder-gray-400 @error('password') border-red-400 @enderror"
                            placeholder="Masukkan kata sandi">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember_me" 
                            type="checkbox" 
                            name="remember"
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <label for="remember_me" class="ml-3 text-sm font-medium text-gray-700">
                            Ingat saya
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="btn-modern w-full bg-gradient-to-r from-gray-900 to-black text-white font-semibold py-4 rounded-2xl shadow-lg">
                        Masuk
                    </button>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500 font-medium">Atau lanjutkan dengan</span>
                        </div>
                    </div>

                    <!-- Social Login -->
                    <div class="grid grid-cols-3 gap-4">
                        <a href="{{ route('oauth.redirect', 'google') }}" class="flex items-center justify-center py-3 px-4 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all">
                            <i class="fab fa-google text-xl text-gray-700"></i>
                        </a>
                        <a href="{{ route('oauth.redirect', 'facebook') }}" class="flex items-center justify-center py-3 px-4 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all">
                            <i class="fab fa-facebook text-xl text-gray-700"></i>
                        </a>
                        <a href="{{ route('oauth.redirect', 'github') }}" class="flex items-center justify-center py-3 px-4 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all">
                            <i class="fab fa-github text-xl text-gray-700"></i>
                        </a>
                    </div>

                    <!-- Register Link -->
                    <p class="text-center text-sm text-gray-600 pt-6">
                        Belum punya akun? 
                        <a href="{{ route('register') }}" data-smooth class="font-semibold text-indigo-600 hover:text-indigo-500">
                            Daftar gratis
                        </a>
                    </p>
                </form>
            </div>
        </div>

        <!-- Right Side - Visual -->
        <div class="hidden lg:flex lg:w-7/12 bg-gradient-to-br from-gray-900 via-gray-800 to-black animate-gradient relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-20 left-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-yellow-300 rounded-full blur-3xl"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex items-center justify-center p-16 w-full">
                <div class="max-w-2xl text-center text-white">
                    <!-- Icon -->
                    <div class="mb-12 animate-float">
                        <div class="inline-flex items-center justify-center w-48 h-48 bg-white/10 backdrop-blur-xl rounded-3xl border border-white/20 shadow-2xl p-6">
                            <img src="{{ asset('logo.png') }}" alt="Fachri" class="h-24 w-auto">
                        </div>
                    </div>

                    <!-- Text -->
                    <h2 class="text-5xl font-bold mb-6 leading-tight">
                        Pengalaman Belanja<br/>Lebih Menyenangkan
                    </h2>
                    <p class="text-2xl text-white/90 mb-12 leading-relaxed">
                        Temukan promo eksklusif, rekomendasi personal, dan checkout yang mulus.
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-8">
                        <div class="glass rounded-2xl p-6 border border-white/20">
                            <div class="text-4xl font-bold mb-2">50K+</div>
                            <div class="text-white/80">Pelanggan puas</div>
                        </div>
                        <div class="glass rounded-2xl p-6 border border-white/20">
                            <div class="text-4xl font-bold mb-2">10K+</div>
                            <div class="text-white/80">Produk</div>
                        </div>
                        <div class="glass rounded-2xl p-6 border border-white/20">
                            <div class="text-4xl font-bold mb-2">4.9★</div>
                            <div class="text-white/80">Rating</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
