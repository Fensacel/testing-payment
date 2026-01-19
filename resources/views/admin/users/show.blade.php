@extends('admin.layouts.app')

@section('title', 'User Detail')
@section('page-title', 'User Detail')
@section('page-description', $user->name)

@section('content')
<div class="space-y-6">
    
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-gray-600 hover:text-black transition">
        <i class="fas fa-arrow-left mr-2"></i> Back to Users
    </a>
    
    <!-- User Information -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-bold text-gray-900 mb-4">User Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Name</p>
                <p class="font-semibold text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Email</p>
                <p class="font-semibold text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Role</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-black text-white' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($user->role) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Joined Date</p>
                <p class="font-semibold text-gray-900">{{ $user->created_at->format('d F Y, H:i') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Order History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Order History ({{ $user->orders->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($user->orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($order->status === 'success')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                            @elseif($order->status === 'pending')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Gagal</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-gray-900">
                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-black hover:text-gray-700 font-medium">
                                View <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No orders yet
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
</div>
@endsection
