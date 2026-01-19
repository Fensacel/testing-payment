<!-- Header -->
<header class="bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h1>
            <p class="text-sm text-gray-500 mt-1">@yield('page-description', 'Welcome to admin panel')</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
            
            <div class="relative group">
                <button class="w-10 h-10 bg-gray-900 rounded-full flex items-center justify-center text-white">
                    <i class="fas fa-user"></i>
                </button>
                
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-user-circle mr-2"></i> Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
