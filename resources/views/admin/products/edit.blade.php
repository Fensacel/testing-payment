@extends('admin.layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')
@section('page-description', 'Update product information')

@section('content')
<div class="max-w-3xl">
    
    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center text-gray-600 hover:text-black transition mb-6">
        <i class="fas fa-arrow-left mr-2"></i> Back to Products
    </a>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('name') border-red-500 @enderror">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                <textarea name="description" rows="4" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price (Rp) *</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('price') border-red-500 @enderror">
                    @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('stock') border-red-500 @enderror">
                    @error('stock')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Discount Percentage (%)</label>
                <input type="number" name="discount_percentage" value="{{ old('discount_percentage', $product->discount_percentage) }}" min="0" max="100"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('discount_percentage') border-red-500 @enderror">
                @error('discount_percentage')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                @if($product->image)
                <div class="mb-3">
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg">
                </div>
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-black focus:border-black @error('image') border-red-500 @enderror">
                @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Packages Section -->
            <div class="border-t pt-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Product Packages (Variants)</h3>
                    <button type="button" onclick="addPackage()" class="text-sm bg-black text-white px-3 py-1.5 rounded hover:bg-gray-800">
                        <i class="fas fa-plus mr-1"></i> Add Package
                    </button>
                </div>
                
                <div id="packages-container" class="space-y-4">
                    @forelse($product->packages as $index => $package)
                    <div class="package-item border border-gray-200 rounded-lg p-4 bg-gray-50 relative mt-4">
                        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                        <h4 class="text-sm font-bold mb-3 text-gray-500">Package {{ $index + 1 }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Package Name</label>
                                <input type="text" name="packages[{{ $index }}][name]" value="{{ $package->name }}" placeholder="e.g. Premium Package" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Price</label>
                                <input type="number" name="packages[{{ $index }}][price]" value="{{ $package->price }}" placeholder="Price" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Description / Features</label>
                            <textarea name="packages[{{ $index }}][description]" rows="2" placeholder="List the features..." class="w-full px-3 py-2 border border-gray-300 rounded text-sm">{{ $package->description }}</textarea>
                        </div>
                    </div>
                    @empty
                    <!-- No packages yet -->
                    @endforelse
                </div>
            </div>

            <script>
                let packageIndex = {{ $product->packages->count() }};
                function addPackage() {
                    const container = document.getElementById('packages-container');
                    const html = `
                    <div class="package-item border border-gray-200 rounded-lg p-4 bg-gray-50 relative mt-4">
                        <button type="button" onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                        <h4 class="text-sm font-bold mb-3 text-gray-500">Package ${packageIndex + 1}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Package Name</label>
                                <input type="text" name="packages[${packageIndex}][name]" placeholder="e.g. Premium Package" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Price</label>
                                <input type="number" name="packages[${packageIndex}][price]" placeholder="Price" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Description / Features</label>
                            <textarea name="packages[${packageIndex}][description]" rows="2" placeholder="List the features..." class="w-full px-3 py-2 border border-gray-300 rounded text-sm"></textarea>
                        </div>
                    </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                    packageIndex++;
                }
            </script>
            
            <div class="flex gap-3">
                <button type="submit" class="px-6 py-3 bg-black text-white rounded-lg hover:bg-gray-800 transition">
                    <i class="fas fa-save mr-2"></i> Update Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    
</div>
@endsection
