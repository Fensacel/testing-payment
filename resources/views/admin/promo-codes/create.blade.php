@extends('admin.layouts.app')

@section('title', 'Create Promo Code')
@section('page-title', 'Create Promo Code')
@section('page-description', 'Add a new promo code')

@section('content')
<div class="max-w-2xl">
    
    <a href="{{ route('admin.promo-codes.index') }}" class="inline-flex items-center text-gray-600 hover:text-black transition mb-6">
        <i class="fas fa-arrow-left mr-2"></i> Back to Promo Codes
    </a>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.promo-codes.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Promo Code *</label>
                <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g., WELCOME2024"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black font-mono uppercase @error('code') border-red-500 @enderror">
                @error('code')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-500 mt-1">Code will be automatically converted to uppercase</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage (%) *</label>
                <input type="number" name="discount_percentage" value="{{ old('discount_percentage') }}" required min="1" max="100"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('discount_percentage') border-red-500 @enderror">
                @error('discount_percentage')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Uses *</label>
                <input type="number" name="max_uses" value="{{ old('max_uses') }}" required min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('max_uses') border-red-500 @enderror">
                @error('max_uses')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expiration Date *</label>
                <input type="date" name="expires_at" value="{{ old('expires_at') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('expires_at') border-red-500 @enderror">
                @error('expires_at')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                    <i class="fas fa-save mr-2"></i> Create Promo Code
                </button>
                <a href="{{ route('admin.promo-codes.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    
</div>
@endsection
