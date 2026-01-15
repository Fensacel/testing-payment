<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman depan toko.
     */
    public function index()
    {
        // Logika pengambilan data harus ada DI DALAM fungsi ini
        $products = Product::where('is_active', true)->get();

        return view('home', compact('products'));
    }
    public function show($slug)
    {
        // Cari produk berdasarkan slug, jika tidak ketemu tampilkan 404
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('show', compact('product'));
    }
}