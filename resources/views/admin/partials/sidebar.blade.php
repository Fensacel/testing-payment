<!-- Sidebar -->
<aside class="w-64 bg-gray-900 text-white flex-shrink-0">
    <div class="p-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" alt="Fachri" class="h-10 w-auto">
            <span class="text-xl font-bold">Admin Panel</span>
        </a>
    </div>
    
    <nav class="mt-6">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 border-l-4 border-white font-semibold' : 'hover:bg-gray-800' }} transition">
            <i class="fas fa-home w-5"></i>
            <span>Dashboard</span>
        </a>
        
        <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-6 py-3 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-800 border-l-4 border-white font-semibold' : 'hover:bg-gray-800' }} transition">
            <i class="fas fa-shopping-cart w-5"></i>
            <span>Orders</span>
        </a>
        
        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-6 py-3 {{ request()->routeIs('admin.products.*') ? 'bg-gray-800 border-l-4 border-white font-semibold' : 'hover:bg-gray-800' }} transition">
            <i class="fas fa-box w-5"></i>
            <span>Products</span>
        </a>
        
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-6 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 border-l-4 border-white font-semibold' : 'hover:bg-gray-800' }} transition">
            <i class="fas fa-users w-5"></i>
            <span>Users</span>
        </a>
        
        <a href="{{ route('admin.promo-codes.index') }}" class="flex items-center gap-3 px-6 py-3 {{ request()->routeIs('admin.promo-codes.*') ? 'bg-gray-800 border-l-4 border-white font-semibold' : 'hover:bg-gray-800' }} transition">
            <i class="fas fa-ticket-alt w-5"></i>
            <span>Promo Codes</span>
        </a>
    </nav>
    
    <div class="absolute bottom-0 w-64 p-6 border-t border-gray-800">
        <a href="{{ route('home') }}" class="flex items-center gap-3 text-gray-400 hover:text-white transition">
            <i class="fas fa-arrow-left w-5"></i>
            <span>Back to Website</span>
        </a>
    </div>
</aside>
