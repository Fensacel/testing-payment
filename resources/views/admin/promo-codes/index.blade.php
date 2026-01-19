@extends('admin.layouts.app')

@section('title', 'Promo Codes')
@section('page-title', 'Promo Codes')
@section('page-description', 'Manage all promo codes')

@section('content')
<div class="space-y-6">
    
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif
    
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div></div>
        <a href="{{ route('admin.promo-codes.create') }}" class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition">
            <i class="fas fa-plus mr-2"></i> Add New Promo Code
        </a>
    </div>
    
    <!-- Promo Codes Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($promoCodes as $promo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono font-bold text-gray-900">{{ $promo->code }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                            {{ $promo->discount_percentage }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $promo->used_count }} / {{ $promo->max_uses }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($promo->expires_at)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $isExpired = \Carbon\Carbon::parse($promo->expires_at)->isPast();
                                $isMaxedOut = $promo->used_count >= $promo->max_uses;
                                $isManuallyInactive = !$promo->is_active;
                            @endphp
                            @if($isManuallyInactive)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Manually Disabled</span>
                            @elseif($isExpired)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                            @elseif($isMaxedOut)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Max Uses Reached</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <form action="{{ route('admin.promo-codes.toggle', $promo) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs font-semibold rounded {{ $promo->is_active ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-green-600 text-white hover:bg-green-700' }} transition">
                                        {{ $promo->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.promo-codes.edit', $promo) }}" class="text-black hover:text-gray-700">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.promo-codes.destroy', $promo) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-ticket-alt text-4xl mb-3"></i>
                            <p>No promo codes yet</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($promoCodes->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $promoCodes->links() }}
        </div>
        @endif
    </div>
    
</div>
@endsection
