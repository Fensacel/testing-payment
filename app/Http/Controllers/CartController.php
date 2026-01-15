<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem; // Pastikan Model OrderItem ada
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // Penting untuk Auth::id()

class CartController extends Controller
{
    // 1. Menampilkan Halaman Keranjang
    public function index()
    {
        $cart = session()->get('cart', []);
        
        $total = 0;
        foreach($cart as $id => $details) {
            $total += $details['price'] * $details['quantity'];
        }

        return view('cart', compact('cart', 'total'));
    }

    // 2. Menambah Barang ke Keranjang (FIXED)
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);

        // LOGIKA REDIRECT:
        // Jika user klik "Beli Langsung", lempar ke Keranjang
        if ($request->input('action') === 'buy_now') {
            return redirect()->route('cart.index');
        }

        // Jika user klik "+ Keranjang", tetap di halaman Detail Produk
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

    // 4. TAMPILKAN FORM CHECKOUT (Input Data Jasa)
    public function viewCheckout(Request $request)
    {
        if (!$request->has('selected_products')) {
            return redirect()->route('cart.index')->with('error', 'Pilih barang dulu.');
        }

        $selectedItemIds = explode(',', $request->input('selected_products'));
        $cart = session()->get('cart', []);
        
        $itemsToBuy = [];
        $subtotal = 0;

        foreach ($selectedItemIds as $id) {
            if (isset($cart[$id])) {
                $item = $cart[$id];
                $item['id'] = $id; 
                $itemsToBuy[] = $item;
                $subtotal += $item['price'] * $item['quantity'];
            }
        }

        if (empty($itemsToBuy)) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih.');
        }

        return view('checkout', compact('itemsToBuy', 'subtotal', 'selectedItemIds'));
    }

    // 5. PROSES PEMBAYARAN (Simpan ke DB + Midtrans)
    public function processPayment(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'email' => 'required|email',
            'selected_products' => 'required'
        ]);

        $selectedItemIds = explode(',', $request->input('selected_products'));
        $cart = session()->get('cart', []);
        $subtotal = 0;
        
        // Hitung ulang total untuk keamanan
        foreach ($selectedItemIds as $id) {
            if (isset($cart[$id])) {
                $subtotal += $cart[$id]['price'] * $cart[$id]['quantity'];
            }
        }

        $grandTotal = $subtotal; 
        $orderNumber = 'ORD-' . strtoupper(Str::random(10));

        // SIMPAN ORDER (Dengan User ID agar bisa Tracking)
        $order = Order::create([
            'user_id' => Auth::id(), // <--- PENTING: Menyimpan ID User yang Login
            'order_number' => $orderNumber,
            'total_price' => $grandTotal,
            'status' => 'pending',
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'email' => $request->email,
            'note' => $request->note,
        ]);

        // SIMPAN ITEM
        foreach ($selectedItemIds as $id) {
            if (isset($cart[$id])) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'product_name' => $cart[$id]['name'],
                    'quantity' => $cart[$id]['quantity'],
                    'price' => $cart[$id]['price'],
                ]);
                unset($cart[$id]); 
            }
        }
        session()->put('cart', $cart);

        // MIDTRANS CONFIG
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $midtransParams = [
            'transaction_details' => [
                'order_id' => $orderNumber,
                'gross_amount' => (int) $grandTotal,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email' => $request->email,
                'phone' => $request->customer_phone,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($midtransParams);
            $order->update(['snap_token' => $snapToken]);

            return view('payment', compact('order', 'snapToken'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // 6. HALAMAN HISTORY / TRACKING PESANAN
    public function history()
    {
        // Pastikan user login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Ambil order milik user ini saja
        $orders = Order::where('user_id', Auth::id())
                        ->with('items')
                        ->latest()
                        ->get();

        return view('history', compact('orders'));
    }
}