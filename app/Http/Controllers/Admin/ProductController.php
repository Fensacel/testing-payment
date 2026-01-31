<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }
    
    public function create()
    {
        return view('admin.products.create');
    }
    
    public function store(StoreProductRequest $request)
    {
        $this->productService->createProduct($request->validated());
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }
    
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }
    
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->productService->updateProduct($product, $request->validated());
        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }
    
    public function destroy(Product $product)
    {
        $this->productService->deleteProduct($product);
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
