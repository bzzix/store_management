<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * إنشاء منتج جديد
     */
    public function createProduct(array $data): Product
    {
        try {
            // توليد slug من الاسم
            if (!isset($data['slug']) || empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            return Product::create($data);
        } catch (QueryException $e) {
            throw new \Exception(__('Error creating product: ') . $e->getMessage());
        }
    }

    /**
     * تحديث منتج
     */
    public function updateProduct(Product $product, array $data): Product
    {
        try {
            // توليد slug من الاسم إذا تغير الاسم
            if (isset($data['name']) && $data['name'] !== $product->name) {
                if (!isset($data['slug']) || empty($data['slug'])) {
                    $data['slug'] = Str::slug($data['name']);
                }
            }

            $product->update($data);
            return $product;
        } catch (QueryException $e) {
            throw new \Exception(__('Error updating product: ') . $e->getMessage());
        }
    }

    /**
     * حذف منتج
     */
    public function deleteProduct(Product $product): bool
    {
        try {
            return $product->delete();
        } catch (QueryException $e) {
            throw new \Exception(__('Error deleting product: ') . $e->getMessage());
        }
    }

    /**
     * الحصول على جميع المنتجات النشطة
     */
    public function getActiveProducts()
    {
        return Product::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * البحث عن المنتجات
     */
    public function searchProducts(string $query, ?int $categoryId = null)
    {
        $search = Product::query();

        if (!empty($query)) {
            $search->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('sku', 'like', "%$query%")
                  ->orWhere('barcode', 'like', "%$query%");
            });
        }

        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        return $search->orderBy('name', 'asc')->get();
    }

    /**
     * التحقق من وجود منتج بنفس الاسم
     */
    public function isNameExists(string $name, ?int $excludeId = null): bool
    {
        $query = Product::where('name', $name);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * التحقق من وجود منتج بنفس SKU
     */
    public function isSkuExists(string $sku, ?int $excludeId = null): bool
    {
        $query = Product::where('sku', $sku);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
