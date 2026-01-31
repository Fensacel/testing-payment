<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(\App\Services\ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getApiList();
        return response()->json($products);
    }

    public function show(Product $product)
    {
        // Check if active just in case, though route model binding might find inactive
        if (!$product->is_active) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product);
    }
}
