<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Left Side - Visual -->
        <div class="hidden lg:flex lg:w-7/12 bg-gradient-to-br from-purple-600 via-pink-500 to-orange-400 animate-gradient relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-20 right-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 left-20 w-96 h-96 bg-white rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan-300 rounded-full blur-3xl"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 flex items-center justify-center p-16 w-full">
                <div class="max-w-2xl text-center text-white">
                    <!-- Icon -->
                    <div class="mb-12 animate-float">
                        <div class="inline-flex items-center justify-center w-32 h-32 bg-white/20 backdrop-blur-xl rounded-3xl border border-white/30 shadow-2xl">
                            <i class="fas fa-rocket text-6xl text-white"></i>
                        </div>
                    </div>

                    <!-- Text -->
                    <h2 class="text-5xl font-bold mb-6 leading-tight">
                        Join Our Amazing<br/>Community
                    </h2>
                    <p class="text-2xl text-white/90 mb-12 leading-relaxed">
                        Get exclusive benefits, early access to sales, and personalized shopping experience.
                    </p>

                    <!-- Benefits -->
                    <div class="grid grid-cols-2 gap-6">
                        <div class="glass rounded-2xl p-6 border border-white/20 text-left">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-gift text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Welcome Bonus</h3>
                            <p class="text-white/80">20% off your first order</p>
                        </div>
                        <div class="glass rounded-2xl p-6 border border-white/20 text-left">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-shipping-fast text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Free Shipping</h3>
                            <p class="text-white/80">On orders over $50</p>
                        </div>
                        <div class="glass rounded-2xl p-6 border border-white/20 text-left">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-star text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Loyalty Points</h3>
                            <p class="text-white/80">Earn rewards with every purchase</p>
                        </div>
                        <div class="glass rounded-2xl p-6 border border-white/20 text-left">
                            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                                <i class="fas fa-bell text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Early Access</h3>
                            <p class="text-white/80">Exclusive sales notifications</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="w-full lg:w-5/12 flex items-center justify-center p-8 lg:p-16 bg-white relative">
            <div class="w-full max-w-md animate-fade-in-up">
                <!-- Logo -->
                <div class="mb-12">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-shopping-bag text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-500 bg-clip-text text-transparent">ShopHub</span>
                    </div>
                </div>

                <!-- Header -->
                <div class="mb-10">
                    <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                        Create your<br/>account
                    </h1>
                    <p class="text-gray-600 text-lg">
                        Join thousands of happy shoppers today
                    </p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-3">Full name</label>
                        <input 
                            id="name" 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            required 
                            autofocus
                            class="input-modern w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-purple-500 focus:bg-white focus:outline-none text-gray-900 placeholder-gray-400 @error('name') border-red-400 @enderror"
                            placeholder="Enter your full name">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">Email address</label>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required
                            class="input-modern w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-purple-500 focus:bg-white focus:outline-none text-gray-900 placeholder-gray-400 @error('email') border-red-400 @enderror"
                            placeholder="Enter your email">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-3">Password</label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required
                            class="input-modern w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-purple-500 focus:bg-white focus:outline-none text-gray-900 placeholder-gray-400 @error('password') border-red-400 @enderror"
                            placeholder="Create a password">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-3">Confirm password</label>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required
                            class="input-modern w-full px-5 py-4 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:border-purple-500 focus:bg-white focus:outline-none text-gray-900 placeholder-gray-400"
                            placeholder="Confirm your password">
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start pt-2">
                        <input 
                            id="terms" 
                            type="checkbox" 
                            required
                            class="w-5 h-5 mt-0.5 text-purple-600 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        <label for="terms" class="ml-3 text-sm text-gray-600">
                            I agree to the <a href="#" class="font-semibold text-purple-600 hover:text-purple-500">Terms of Service</a> and <a href="#" class="font-semibold text-purple-600 hover:text-purple-500">Privacy Policy</a>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit"
                        class="btn-modern w-full bg-gradient-to-r from-purple-600 to-pink-500 text-white font-semibold py-4 rounded-2xl shadow-lg mt-6">
                        Create account
                    </button>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500 font-medium">Or continue with</span>
                        </div>
                    </div>

                    <!-- Social Login -->
                    <div class="grid grid-cols-3 gap-4">
                        <button 
                            type="button"
                            class="flex items-center justify-center py-3 px-4 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all">
                            <i class="fab fa-google text-xl text-gray-700"></i>
                        </button>
                        <button 
                            type="button"
                            class="flex items-center justify-center py-3 px-4 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all">
                            <i class="fab fa-facebook text-xl text-gray-700"></i>
                        </button>
                        <button 
                            type="button"
                            class="flex items-center justify-center py-3 px-4 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all">
                            <i class="fab fa-apple text-xl text-gray-700"></i>
                        </button>
                    </div>

                    <!-- Login Link -->
                    <p class="text-center text-sm text-gray-600 pt-6">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-semibold text-purple-600 hover:text-purple-500">
                            Sign in
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
