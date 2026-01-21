<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\PromoCode;
use App\Models\OrderItem; 
use App\Models\ProductPackage; // Tambahkan import
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
        
        // Sync cart with latest product data from database
        $syncedCart = [];
        $total = 0;
        
        foreach($cart as $key => $details) {
            // Determine Product ID (handle composite keys)
            $productId = $details['product_id'] ?? (strpos($key, '_') !== false ? explode('_', $key)[0] : $key);
            $productId = $details['product_id'] ?? (strpos($key, '_') !== false ? explode('_', $key)[0] : $key);
            $product = Product::with('packages')->find($productId);
            
            // If product still exists, use latest data
            if ($product) {
                // Handle Package Data Synching
                $packageId = $details['package_id'] ?? null;
                $packageName = null;
                $price = $product->price;

                if ($packageId) {
                    $package = ProductPackage::find($packageId);
                    if ($package) {
                        $packageName = $package->name;
                        if ($package->price > 0) {
                            $price = $package->price;
                        }
                    }
                }

                $syncedCart[$key] = [
                    'product_id' => $product->id,
                    'package_id' => $packageId,
                    'package_name' => $packageName,
                    'name' => $product->name . ($packageName ? " - $packageName" : ""),
                    'quantity' => min($details['quantity'], $product->stock), // Ensure quantity doesn't exceed stock
                    'price' => $price,
                    'discount_percentage' => $product->discount_percentage,
                    'image' => $product->image,
                    'quantity' => min($details['quantity'], $product->stock), // Ensure quantity doesn't exceed stock
                    'price' => $price,
                    'discount_percentage' => $product->discount_percentage,
                    'image' => $product->image,
                    'stock' => $product->stock,
                    'available_packages' => $product->packages
                ];
                
                // Calculate price with discount
                $finalPrice = $price;
                if ($product->discount_percentage > 0) {
                    $finalPrice = $finalPrice - ($finalPrice * $product->discount_percentage / 100);
                }
                $total += $finalPrice * $syncedCart[$key]['quantity'];
            }
            // If product deleted, remove from cart
        }
        
        // Update session with synced data
        session()->put('cart', $syncedCart);
        
        // Get recommended products (random 4 products that are active and in stock)
        $recommendedProducts = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('cart', compact('syncedCart', 'total', 'recommendedProducts'));
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
        
        // Handle Package
        $packageId = $request->input('package_id');
        $cartKey = $id;
        $packageName = null;
        $price = $product->price;

        if ($packageId) {
            $package = ProductPackage::where('product_id', $id)->find($packageId);
            if ($package) {
                $cartKey = $id . '_' . $packageId;
                $packageName = $package->name;
                if ($package->price > 0) {
                    $price = $package->price;
                }
            }
        }

        if(isset($cart[$cartKey])) {
            // Cek apakah penambahan melebihi stok
            if (($cart[$cartKey]['quantity'] + $quantity) > $product->stock) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi!');
            }
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            if ($quantity > $product->stock) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi!');
            }
            $cart[$cartKey] = [
                "product_id" => $product->id,
                "package_id" => $packageId,
                "package_name" => $packageName,
                "name" => $product->name . ($packageName ? " - $packageName" : ""),
                "quantity" => $quantity,
                "price" => $price,
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
        $key = $request->id;
        $quantity = $request->quantity;

        if (isset($cart[$key])) {
            $productId = $cart[$key]['product_id'] ?? (strpos($key, '_') !== false ? explode('_', $key)[0] : $key);
            $product = Product::find($productId);
            
            if ($product && $quantity <= $product->stock) {
                $cart[$key]['quantity'] = $quantity;
                session()->put('cart', $cart);
                return redirect()->back();
            } else {
                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    // 3c. Update Package di Keranjang
    public function updatePackage(Request $request)
    {
        $cart = session()->get('cart', []);
        $oldKey = $request->id; // cart key lama
        $newPackageId = $request->package_id;
        
        if (!isset($cart[$oldKey])) {
            return redirect()->back();
        }
        
        $item = $cart[$oldKey];
        $productId = $item['product_id'] ?? (strpos($oldKey, '_') !== false ? explode('_', $oldKey)[0] : $oldKey);
        $quantity = $item['quantity'];

        // Hapus item lama
        unset($cart[$oldKey]);
        
        // Buat item baru
        $product = Product::find($productId);
        $package = ProductPackage::find($newPackageId);
        
        if (!$product || !$package) {
             return redirect()->back()->with('error', 'Paket tidak valid');
        }

        // Generate new key
        $newKey = $productId . '_' . $newPackageId;
        
        // Harga baru
        $price = $package->price > 0 ? $package->price : $product->price;

        // Cek jika newKey sudah ada di cart (merge quantity)
        if (isset($cart[$newKey])) {
             $cart[$newKey]['quantity'] += $quantity;
        } else {
             $cart[$newKey] = [
                "product_id" => $productId,
                "package_id" => $newPackageId,
                "package_name" => $package->name,
                "name" => $product->name . " - " . $package->name,
                "quantity" => $quantity,
                "price" => $price,
                "discount_percentage" => $product->discount_percentage,
                "image" => $product->image
             ];
        }
        
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Paket berhasil diubah');
    }

    // 3d. Apply Promo Code
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
            if ($promo->used_count >= $promo->max_uses) {
                return redirect()->back()->with('error', 'Kode promo sudah mencapai batas penggunaan');
            }
            if ($promo->expires_at && now()->gt(\Carbon\Carbon::parse($promo->expires_at))) {
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
            'discount_percentage' => $promo->discount_percentage,
            'discount_amount' => $promoDiscount,
        ]);

        return redirect()->back()->with('success', 'Kode promo berhasil diterapkan!');
    }

    // 3e. Remove Promo Code
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

        $selectedItemKeys = explode(',', $request->input('selected_products'));
        $selectedItemKeys = array_map('trim', $selectedItemKeys);

        $cart = session()->get('cart', []);

        \Log::info('Checkout Debug:', [
            'input_keys' => $selectedItemKeys,
            'session_keys' => array_keys($cart)
        ]);
        
        $itemsToBuy = [];
        $subtotal = 0;

        foreach ($selectedItemKeys as $key) {
            if (isset($cart[$key])) {
                $productId = $cart[$key]['product_id'] ?? (strpos($key, '_') !== false ? explode('_', $key)[0] : $key);
                $product = Product::find($productId);
                
                if (!$product) {
                    continue; // Skip if product deleted
                }
                
                // Use data from cart (price might be package price)
                $price = $cart[$key]['price'];
                $discountPercentage = $cart[$key]['discount_percentage'] ?? $product->discount_percentage;

                $item = [
                    'id' => $key, // Use cart key as ID
                    'name' => $cart[$key]['name'], // Already includes package name
                    'quantity' => min($cart[$key]['quantity'], $product->stock),
                    'price' => $price,
                    'discount_percentage' => $discountPercentage,
                    'image' => $product->image
                ];
                
                $itemsToBuy[] = $item;
                
                // Calculate price with discount
                $finalPrice = $price;
                if ($discountPercentage > 0) {
                    $finalPrice = $finalPrice - ($finalPrice * $discountPercentage / 100);
                }
                $subtotal += $finalPrice * $item['quantity'];
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
            $promoDiscount = $subtotal * ($promoCode['discount_percentage'] / 100);
        }

        return view('checkout', compact('itemsToBuy', 'subtotal', 'selectedItemKeys', 'promoCode', 'promoDiscount'));
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

        $selectedItemKeys = explode(',', $request->input('selected_products'));
        $cart = session()->get('cart', []);
        
        // Mulai Transaksi Database (Agar aman)
        return DB::transaction(function () use ($request, $selectedItemKeys, $cart) {
            
            $subtotal = 0;

            // Tahap 1: Validasi Stok Sebelum Membuat Order
            foreach ($selectedItemKeys as $key) {
                if (isset($cart[$key])) {
                    $productId = $cart[$key]['product_id'] ?? (strpos($key, '_') !== false ? explode('_', $key)[0] : $key);
                    $product = Product::lockForUpdate()->find($productId); // Kunci baris database agar tidak bentrok
                    
                    if (!$product || $product->stock < $cart[$key]['quantity']) {
                        // Jika stok habis saat mau bayar, batalkan semua
                        return redirect()->route('cart.index')->with('error', 'Stok ' . $cart[$key]['name'] . ' tidak mencukupi. Transaksi dibatalkan.');
                    }
                    
                    // Calculate price
                    $price = $cart[$key]['price'];
                    $discountPercentage = $cart[$key]['discount_percentage'] ?? 0;
                    
                    if ($discountPercentage > 0) {
                        $price = $price - ($price * $discountPercentage / 100);
                    }
                    $subtotal += $price * $cart[$key]['quantity'];
                }
            }

            // Get promo code from session
            $promoCode = session('promo_code');
            $promoDiscount = 0;
            $promoCodeId = null;
            
            \Log::info('Promo Code in Session:', ['promo_code' => $promoCode]);
            
            if ($promoCode && isset($promoCode['id']) && isset($promoCode['discount_amount'])) {
                // Use pre-calculated discount amount from session
                $promoDiscount = $promoCode['discount_amount'];
                $promoCodeId = $promoCode['id'];
                \Log::info('Promo Applied:', ['id' => $promoCodeId, 'discount' => $promoDiscount]);
            }
            
            // Calculate service fee (2%) and grand total
            $subtotalAfterPromo = $subtotal - $promoDiscount;
            $serviceFee = $subtotalAfterPromo * 0.01;
            $grandTotal = $subtotalAfterPromo + $serviceFee;
            
            \Log::info('Before Order Creation:', [
                'subtotal' => $subtotal,
                'promoCodeId' => $promoCodeId,
                'promoDiscount' => $promoDiscount,
                'grandTotal' => $grandTotal
            ]);
            
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            // Buat Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => $orderNumber,
                'total_price' => $grandTotal,
                'promo_code_id' => $promoCodeId,
                'promo_discount' => $promoDiscount,
                'status' => 'pending',
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'email' => $request->email,
                'note' => $request->note,
            ]);

            // Tahap 2: Simpan Item & KURANGI STOK
            foreach ($selectedItemKeys as $key) {
                if (isset($cart[$key])) {
                    $productId = $cart[$key]['product_id'] ?? (strpos($key, '_') !== false ? explode('_', $key)[0] : $key);
                    
                    // Kurangi Stok di Database Real
                    $product = Product::find($productId);
                    $product->decrement('stock', $cart[$key]['quantity']);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $productId,
                        'product_name' => $cart[$key]['name'],
                        'package_name' => $cart[$key]['package_name'] ?? null,
                        'quantity' => $cart[$key]['quantity'],
                        'price' => $cart[$key]['price'],
                    ]);
                    
                    // Hapus dari session keranjang
                    unset($cart[$key]); 
                }
            }
            session()->put('cart', $cart);
            
            // Increment promo code usage count if promo was used
            if ($promoCodeId) {
                PromoCode::where('id', $promoCodeId)->increment('used_count');
                // Clear promo from session after successful order
                session()->forget('promo_code');
            }

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
                
                // --- UPDATE PAYMENT INFO (New Feature) ---
                $paymentType = $status->payment_type ?? null;
                $paymentInfo = [];

                if ($paymentType == 'bank_transfer' && isset($status->va_numbers[0])) {
                    $paymentInfo = [
                        'bank' => strtoupper($status->va_numbers[0]->bank),
                        'va_number' => $status->va_numbers[0]->va_number
                    ];
                } elseif ($paymentType == 'cstore' && isset($status->payment_code)) {
                    $paymentInfo = [
                        'store' => strtoupper($status->store),
                        'payment_code' => $status->payment_code
                    ];
                } elseif ($paymentType == 'echannel') {
                    $paymentInfo = [
                        'bill_key' => $status->bill_key ?? null,
                        'biller_code' => $status->biller_code ?? null
                    ];
                } elseif ($paymentType == 'qris' || $paymentType == 'gopay') {
                     // Try to get QR Code URL if available (common in Core API, simpler in Snap)
                     $qrUrl = $status->qr_code_url ?? null;
                     
                     // Sometimes it is in actions array for Gopay/QRIS
                     if (!$qrUrl && isset($status->actions)) {
                         foreach ($status->actions as $action) {
                             if ($action->name == 'generate-qr-code') {
                                 $qrUrl = $action->url;
                                 break;
                             }
                         }
                     }

                     $paymentInfo = [
                         'type' => 'QRIS',
                         'qr_code_url' => $qrUrl
                     ];
                }

                // Update Order Info
                $order->update([
                    'payment_type' => $paymentType,
                    'payment_info' => !empty($paymentInfo) ? $paymentInfo : null
                ]);

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
                      ->with(['items.product', 'promoCode'])
                      ->firstOrFail();

        return view('history-detail', compact('order'));
    }
}