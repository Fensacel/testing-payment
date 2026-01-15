<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // 1. Menampilkan Halaman Keranjang
    public function index()
    {
        $cart = session()->get('cart', []);
        
        // Hitung Total Bayar
        $total = 0;
        foreach($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        return view('cart', compact('cart', 'total'));
    }

    // 2. Menambah Barang ke Keranjang
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        // Jika barang sudah ada, update jumlahnya
        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            // Jika belum ada, buat baru
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);

        // --- LOGIKA BARU DISINI ---
        // Jika tombol yang ditekan adalah "Beli Langsung" (buy_now), arahkan ke Keranjang
        if ($request->input('action') === 'buy_now') {
            return redirect()->route('cart.index')->with('success', 'Produk ditambahkan, silakan proses pembayaran.');
        }

        // Jika tombol "Keranjang" biasa, tetap di halaman produk
        return redirect()->back()->with('success', 'Produk berhasil masuk keranjang!');
    }

    // 3. Hapus Barang dari Keranjang
    public function remove(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
        }
    }
}