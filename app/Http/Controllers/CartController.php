<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\OrderItem; 
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk Transaksi Database

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

    // 2. Menambah Barang ke Keranjang
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Cek Stok Dulu
        if ($product->stock <= 0) {
            return redirect()->back()->with('error', 'Stok barang habis!');
        }

        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        if(isset($cart[$id])) {
            // Cek apakah penambahan melebihi stok
            if (($cart[$id]['quantity'] + $quantity) > $product->stock) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi!');
            }
            $cart[$id]['quantity'] += $quantity;
        } else {
            if ($quantity > $product->stock) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi!');
            }
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "discount_percentage" => $product->discount_percentage,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);

        if ($request->input('action') === 'buy_now') {
            return redirect()->route('cart.index');
        }

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

    // 3b. Update Quantity di Keranjang
    public function updateQuantity(Request $request)
    {
        $cart = session()->get('cart', []);
        $id = $request->id;
        $quantity = $request->quantity;

        if (isset($cart[$id])) {
            $product = Product::find($id);
            
            if ($product && $quantity <= $product->stock) {
                $cart[$id]['quantity'] = $quantity;
                session()->put('cart', $cart);
                return redirect()->back();
            } else {
                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    // 3c. Apply Promo Code
    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string'
        ]);

        $code = strtoupper(trim($request->promo_code));
        $promo = PromoCode::where('code', $code)->first();

        if (!$promo) {
            return redirect()->back()->with('error', 'Kode promo tidak valid');
        }

        if (!$promo->isValid()) {
            if (!$promo->is_active) {
                return redirect()->back()->with('error', 'Kode promo sudah tidak aktif');
            }
            if ($promo->used_count >= $promo->max_usage) {
                return redirect()->back()->with('error', 'Kode promo sudah mencapai batas penggunaan');
            }
            if ($promo->valid_until && now()->gt($promo->valid_until)) {
                return redirect()->back()->with('error', 'Kode promo sudah kadaluarsa');
            }
            return redirect()->back()->with('error', 'Kode promo tidak dapat digunakan');
        }

        // Calculate subtotal after product discounts
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $price = $item['price'];
            if (isset($item['discount_percentage']) && $item['discount_percentage'] > 0) {
                $price = $price - ($price * $item['discount_percentage'] / 100);
            }
            $subtotal += $price * $item['quantity'];
        }

        // Calculate promo discount
        $promoDiscount = $promo->calculateDiscount($subtotal);

        // Store promo in session
        session()->put('promo_code', [
            'id' => $promo->id,
            'code' => $promo->code,
            'discount_type' => $promo->discount_type,
            'discount_value' => $promo->discount_value,
            'discount_amount' => $promoDiscount,
        ]);

        return redirect()->back()->with('success', 'Kode promo berhasil diterapkan!');
    }

    // 3d. Remove Promo Code
    public function removePromo()
    {
        session()->forget('promo_code');
        return redirect()->back();
    }

    // 4. TAMPILKAN FORM CHECKOUT
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
                
                // Calculate price with discount
                $price = $item['price'];
                if (isset($item['discount_percentage']) && $item['discount_percentage'] > 0) {
                    $price = $price - ($price * $item['discount_percentage'] / 100);
                }
                $subtotal += $price * $item['quantity'];
            }
        }

        if (empty($itemsToBuy)) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih.');
        }

        // Get promo code from session
        $promoCode = session('promo_code');
        $promoDiscount = 0;
        
        if ($promoCode) {
            // Calculate promo discount based on subtotal
            if ($promoCode['discount_type'] === 'percentage') {
                $promoDiscount = $subtotal * ($promoCode['discount_value'] / 100);
            } else {
                $promoDiscount = min($promoCode['discount_value'], $subtotal);
            }
        }

        return view('checkout', compact('itemsToBuy', 'subtotal', 'selectedItemIds', 'promoCode', 'promoDiscount'));
    }

    // 5. PROSES PEMBAYARAN (POTONG STOK DISINI)
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
        
        // Mulai Transaksi Database (Agar aman)
        return DB::transaction(function () use ($request, $selectedItemIds, $cart) {
            
            $subtotal = 0;

            // Tahap 1: Validasi Stok Sebelum Membuat Order
            foreach ($selectedItemIds as $id) {
                if (isset($cart[$id])) {
                    $product = Product::lockForUpdate()->find($id); // Kunci baris database agar tidak bentrok
                    
                    if (!$product || $product->stock < $cart[$id]['quantity']) {
                        // Jika stok habis saat mau bayar, batalkan semua
                        return redirect()->route('cart.index')->with('error', 'Stok ' . $cart[$id]['name'] . ' tidak mencukupi. Transaksi dibatalkan.');
                    }
                    $subtotal += $cart[$id]['price'] * $cart[$id]['quantity'];
                }
            }

            $grandTotal = $subtotal; 
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            // Buat Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'total_price' => $grandTotal,
                'status' => 'pending',
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'email' => $request->email,
                'note' => $request->note,
            ]);

            // Tahap 2: Simpan Item & KURANGI STOK
            foreach ($selectedItemIds as $id) {
                if (isset($cart[$id])) {
                    // Kurangi Stok di Database Real
                    $product = Product::find($id);
                    $product->decrement('stock', $cart[$id]['quantity']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $id,
                        'product_name' => $cart[$id]['name'],
                        'quantity' => $cart[$id]['quantity'],
                        'price' => $cart[$id]['price'],
                    ]);
                    
                    // Hapus dari session keranjang
                    unset($cart[$id]); 
                }
            }
            session()->put('cart', $cart);

            // Midtrans Logic
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
        });
    }

    // 6. HALAMAN HISTORY (BALIKIN STOK DISINI)
    public function history()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $pendingOrders = Order::where('user_id', Auth::id())
                              ->where('status', 'pending')
                              ->with('items') // Load items untuk balikin stok
                              ->get();

        foreach ($pendingOrders as $order) {
            try {
                $status = Transaction::status($order->order_number);
                
                // Jika SUKSES
                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    $order->update(['status' => 'success']);
                } 
                // Jika GAGAL / EXPIRE / CANCEL -> Balikin Stok!
                else if (in_array($status->transaction_status, ['expire', 'cancel', 'deny', 'failure'])) {
                    
                    // Update status order jadi failed
                    $order->update(['status' => 'failed']);

                    // LOOP ITEMS DAN KEMBALIKAN STOK
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock', $item->quantity);
                        }
                    }
                }

            } catch (\Exception $e) {
                continue;
            }
        }

        $orders = Order::where('user_id', Auth::id())
                        ->with('items.product')
                        ->latest()
                        ->get();

        return view('history', compact('orders'));
    }

    // 7. DETAIL HISTORY
    public function historyDetail($id)
    {
        $order = Order::where('id', $id)
                      ->where('user_id', Auth::id())
                      ->with(['items.product'])
                      ->firstOrFail();

        return view('history-detail', compact('order'));
    }
}