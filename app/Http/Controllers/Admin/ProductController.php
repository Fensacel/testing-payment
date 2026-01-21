<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }
    
    public function create()
    {
        return view('admin.products.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|max:20480',
            'packages' => 'nullable|array',
            'packages.*.name' => 'required_with:packages|string',
            'packages.*.price' => 'nullable|numeric|min:0',
            'packages.*.description' => 'nullable|string',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        $product = Product::create($validated);

        if ($request->has('packages')) {
            foreach ($request->packages as $pkg) {
                if (!empty($pkg['name'])) { // Only create if name is present
                    $product->packages()->create([
                        'name' => $pkg['name'],
                        'price' => $pkg['price'] ?? 0,
                        'description' => $pkg['description'] ?? null
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }
    
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }
    
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|max:20480',
            'packages' => 'nullable|array',
            'packages.*.name' => 'required_with:packages|string',
            'packages.*.price' => 'nullable|numeric|min:0',
            'packages.*.description' => 'nullable|string',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        $product->update($validated);

        // Sync Packages
        // Delete all existing packages and re-create them from the request
        // This handles additions, removals, and updates in a simple way
        $product->packages()->delete();

        if ($request->has('packages')) {
            foreach ($request->packages as $pkg) {
                if (!empty($pkg['name'])) {
                    $product->packages()->create([
                        'name' => $pkg['name'],
                        'price' => $pkg['price'] ?? 0,
                        'description' => $pkg['description'] ?? null
                    ]);
                }
            }
        }
        
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }
    
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
