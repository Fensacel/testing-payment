<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Get all active products for the homepage.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveProducts()
    {
        return Product::where('is_active', true)->get();
    }

    /**
     * Find an active product by slug.
     *
     * @param string $slug
     * @return Product
     */
    public function findActiveBySlug(string $slug): Product
    {
        return Product::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Get paginated products for API.
     *
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getApiList(int $perPage = 12)
    {
        return Product::query()
            ->select(['id', 'name', 'slug', 'price', 'stock', 'image'])
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);
    }
    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        $data['slug'] = Str::slug($data['name']);
        
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            $data['image'] = $data['image']->store('products', 'public');
        }

        $product = Product::create($data);

        if (isset($data['packages']) && is_array($data['packages'])) {
            $this->syncPackages($product, $data['packages']);
        }

        return $product;
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $data['image']->store('products', 'public');
        }

        $product->update($data);

        if (isset($data['packages'])) {
            $this->syncPackages($product, $data['packages']);
        }

        return $product;
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return void
     */
    public function deleteProduct(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();
    }

    /**
     * Sync product packages.
     *
     * @param Product $product
     * @param array $packages
     * @return void
     */
    protected function syncPackages(Product $product, array $packages): void
    {
        // Simple strategy: delete all and recreate
        // Ideally we should update existing ones by ID if provided to preserve history if needed,
        // but for this simple use case, delete-recreate is safer and easier.
        $product->packages()->delete();

        foreach ($packages as $pkg) {
            if (!empty($pkg['name'])) {
                $product->packages()->create([
                    'name' => $pkg['name'],
                    'price' => $pkg['price'] ?? 0,
                    'description' => $pkg['description'] ?? null
                ]);
            }
        }
    }
}
