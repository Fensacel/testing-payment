<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $productService;

    public function __construct(\App\Services\ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Menampilkan halaman depan toko.
     */
    public function index()
    {
        $products = $this->productService->getActiveProducts();
        return view('home', compact('products'));
    }

    public function show($slug)
    {
        $product = $this->productService->findActiveBySlug($slug);
        return view('show', compact('product'));
    }
}