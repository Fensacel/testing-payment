<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPackage;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Get synced cart from session.
     *
     * @return array
     */
    public function getCart(): array
    {
        $cart = session()->get('cart', []);
        $syncedCart = [];

        foreach ($cart as $key => $details) {
            $productId = $details['product_id'] ?? (strpos($key, '_') !== false ? explode('_', $key)[0] : $key);
            $product = Product::with('packages')->find($productId);

            if ($product) {
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
                    'quantity' => min($details['quantity'], $product->stock),
                    'price' => $price,
                    'discount_percentage' => $product->discount_percentage,
                    'image' => $product->image,
                    'stock' => $product->stock,
                    'available_packages' => $product->packages
                ];
            }
        }

        session()->put('cart', $syncedCart);
        return $syncedCart;
    }

    /**
     * Add item to cart.
     *
     * @param int $productId
     * @param int $quantity
     * @param int|null $packageId
     * @return void
     * @throws \Exception
     */
    public function addToCart(int $productId, int $quantity, ?int $packageId = null): void
    {
        $product = Product::findOrFail($productId);

        if ($product->stock <= 0) {
            throw new \Exception('Stok barang habis!');
        }

        $cart = session()->get('cart', []);
        
        // Determine key and price based on package
        $cartKey = $productId;
        $packageName = null;
        $price = $product->price;

        if ($packageId) {
            $package = ProductPackage::where('product_id', $productId)->find($packageId);
            if ($package) {
                $cartKey = $productId . '_' . $packageId;
                $packageName = $package->name;
                if ($package->price > 0) {
                    $price = $package->price;
                }
            }
        }

        if (isset($cart[$cartKey])) {
            if (($cart[$cartKey]['quantity'] + $quantity) > $product->stock) {
                throw new \Exception('Stok tidak mencukupi!');
            }
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            if ($quantity > $product->stock) {
                 throw new \Exception('Stok tidak mencukupi!');
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
    }

    /**
     * Remove item from cart.
     *
     * @param string $key
     */
    public function removeFromCart(string $key): void
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$key])) {
            unset($cart[$key]);
            session()->put('cart', $cart);
        }
    }

    /**
     * Update item quantity.
     *
     * @param string $key
     * @param int $quantity
     * @return void
     */
    public function updateQuantity(string $key, int $quantity): void
    {
        $cart = session()->get('cart', []);
        
        if (isset($cart[$key])) {
            $productId = $cart[$key]['product_id'];
            $product = Product::find($productId);

            if ($product && $quantity <= $product->stock && $quantity > 0) {
                $cart[$key]['quantity'] = $quantity;
                session()->put('cart', $cart);
            }
        }
    }

    /**
     * Get total price of cart.
     *
     * @return float
     */
    public function calculateTotal(): float
    {
        $cart = $this->getCart(); // Ensure synced
        $total = 0;

        foreach ($cart as $item) {
            $price = $item['price'];
            if (isset($item['discount_percentage']) && $item['discount_percentage'] > 0) {
                $price = $price - ($price * $item['discount_percentage'] / 100);
            }
            $total += $price * $item['quantity'];
        }

        return $total;
    }

    /**
     * Get items ready for checkout based on selected keys.
     *
     * @param array $selectedKeys
     * @return array
     */
    public function getCheckoutItems(array $selectedKeys): array
    {
        $cart = session()->get('cart', []);
        $itemsToBuy = [];
        
        foreach ($selectedKeys as $key) {
            $key = trim($key);
            if (isset($cart[$key])) {
                $productId = $cart[$key]['product_id'];
                $product = Product::find($productId);
                
                if (!$product) continue; // Skip if deleted

                $price = $cart[$key]['price'];
                $discountPercentage = $cart[$key]['discount_percentage'] ?? $product->discount_percentage;

                $itemsToBuy[] = [
                    'id' => $key,
                    'product_id' => $productId,
                    'name' => $cart[$key]['name'],
                    'quantity' => min($cart[$key]['quantity'], $product->stock),
                    'price' => $price,
                    'discount_percentage' => $discountPercentage,
                    'package_name' => $cart[$key]['package_name'] ?? null,
                    'image' => $product->image
                ];
            }
        }

        return $itemsToBuy;
    }
}
