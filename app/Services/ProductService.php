<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductUnit;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * ProductService
 * 
 * خدمة إدارة المنتجات:
 * - إنشاء وتحديث وحذف المنتجات
 * - إدارة الأسعار
 * - إدارة الوحدات
 */
class ProductService
{
    /**
     * إنشاء منتج جديد
     * 
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // إنشاء المنتج
            $product = Product::create([
                'category_id' => $data['category_id'],
                'warehouse_id' => $data['warehouse_id'],
                'profit_margin_tier_id' => $data['profit_margin_tier_id'] ?? 1,
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'sku' => $data['sku'] ?? $this->generateSKU(),
                'barcode' => $data['barcode'] ?? null,
                'description' => $data['description'] ?? null,
                'base_unit' => $data['base_unit'] ?? 'قطعة',
                'weight' => $data['weight'] ?? null,
                'min_stock_level' => $data['min_stock_level'] ?? null,
                'max_stock_level' => $data['max_stock_level'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'is_featured' => $data['is_featured'] ?? false,
            ]);

            // إنشاء الوحدة الافتراضية (ProductObserver يقوم بهذا تلقائياً)
            // لكن إذا كان Observer معطل، يمكن إنشاؤها هنا

            return $product->fresh();
        });
    }

    /**
     * تحديث منتج
     * 
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update([
                'category_id' => $data['category_id'] ?? $product->category_id,
                'warehouse_id' => $data['warehouse_id'] ?? $product->warehouse_id,
                'profit_margin_tier_id' => $data['profit_margin_tier_id'] ?? $product->profit_margin_tier_id,
                'name' => $data['name'] ?? $product->name,
                'slug' => $data['slug'] ?? $product->slug,
                'sku' => $data['sku'] ?? $product->sku,
                'barcode' => $data['barcode'] ?? $product->barcode,
                'description' => $data['description'] ?? $product->description,
                'base_unit' => $data['base_unit'] ?? $product->base_unit,
                'weight' => $data['weight'] ?? $product->weight,
                'min_stock_level' => $data['min_stock_level'] ?? $product->min_stock_level,
                'max_stock_level' => $data['max_stock_level'] ?? $product->max_stock_level,
                'is_active' => $data['is_active'] ?? $product->is_active,
                'is_featured' => $data['is_featured'] ?? $product->is_featured,
            ]);

            return $product->fresh();
        });
    }

    /**
     * حذف منتج
     * 
     * @param Product $product
     * @return bool
     */
    public function delete(Product $product): bool
    {
        // ProductObserver سيتحقق من وجود فواتير
        return $product->delete();
    }

    /**
     * تحديث سعر منتج
     * 
     * @param Product $product
     * @param float $costPrice سعر التكلفة
     * @param float $basePrice السعر الأساسي
     * @return ProductPrice
     */
    public function updatePrice(Product $product, float $costPrice, float $basePrice): ProductPrice
    {
        return DB::transaction(function () use ($product, $costPrice, $basePrice) {
            // إنشاء سجل سعر جديد
            $price = ProductPrice::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'cost_price' => $costPrice,
                'base_price' => $basePrice,
                'is_current' => true,
            ]);

            // ProductPriceObserver سيقوم بتحديث current_cost_price و current_base_price تلقائياً

            return $price;
        });
    }

    /**
     * إضافة وحدة جديدة لمنتج
     * 
     * @param Product $product
     * @param array $data
     * @return ProductUnit
     */
    public function addUnit(Product $product, array $data): ProductUnit
    {
        return DB::transaction(function () use ($product, $data) {
            // إذا كانت الوحدة افتراضية، إلغاء الافتراضية من الوحدات الأخرى
            if ($data['is_default'] ?? false) {
                $product->units()->update(['is_default' => false]);
            }

            return $product->units()->create([
                'unit_name' => $data['unit_name'],
                'unit_name_en' => $data['unit_name_en'] ?? $data['unit_name'],
                'unit_value' => $data['unit_value'],
                'is_default' => $data['is_default'] ?? false,
            ]);
        });
    }

    /**
     * تحديث وحدة
     * 
     * @param ProductUnit $unit
     * @param array $data
     * @return ProductUnit
     */
    public function updateUnit(ProductUnit $unit, array $data): ProductUnit
    {
        return DB::transaction(function () use ($unit, $data) {
            // إذا كانت الوحدة افتراضية، إلغاء الافتراضية من الوحدات الأخرى
            if ($data['is_default'] ?? false) {
                $unit->product->units()
                    ->where('id', '!=', $unit->id)
                    ->update(['is_default' => false]);
            }

            $unit->update([
                'unit_name' => $data['unit_name'] ?? $unit->unit_name,
                'unit_name_en' => $data['unit_name_en'] ?? $unit->unit_name_en,
                'unit_value' => $data['unit_value'] ?? $unit->unit_value,
                'is_default' => $data['is_default'] ?? $unit->is_default,
            ]);

            return $unit->fresh();
        });
    }

    /**
     * حذف وحدة
     * 
     * @param ProductUnit $unit
     * @return bool
     */
    public function deleteUnit(ProductUnit $unit): bool
    {
        if ($unit->is_default) {
            throw new \Exception("Cannot delete default unit.");
        }

        return $unit->delete();
    }

    /**
     * تعيين وحدة كافتراضية
     * 
     * @param ProductUnit $unit
     * @return ProductUnit
     */
    public function setDefaultUnit(ProductUnit $unit): ProductUnit
    {
        return DB::transaction(function () use ($unit) {
            // إلغاء الافتراضية من جميع الوحدات
            $unit->product->units()->update(['is_default' => false]);

            // تعيين الوحدة الحالية كافتراضية
            $unit->update(['is_default' => true]);

            return $unit->fresh();
        });
    }

    /**
     * توليد SKU تلقائي
     * 
     * @return string
     */
    protected function generateSKU(): string
    {
        do {
            $sku = 'PRD-' . strtoupper(Str::random(8));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    /**
     * البحث عن منتجات
     * 
     * @param string $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search(string $query, array $filters = [])
    {
        $products = Product::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%")
                  ->orWhere('barcode', 'like', "%{$query}%");
            });

        if (isset($filters['category_id'])) {
            $products->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $products->where('is_active', $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $products->where('is_featured', $filters['is_featured']);
        }

        return $products->with(['category', 'warehouse', 'profitMarginTier'])
            ->get();
    }

    /**
     * الحصول على المنتجات ذات المخزون المنخفض
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockProducts(int $limit = 20)
    {
        return Product::whereNotNull('min_stock')
            ->whereHas('warehouseStock', function ($q) {
                $q->whereRaw('quantity < products.min_stock');
            })
            ->with(['warehouseStock'])
            ->limit($limit)
            ->get();
    }

    /**
     * إضافة صور للمنتج
     * 
     * @param Product $product
     * @param array $images
     * @return void
     */
    public function addImages(Product $product, array $images): void
    {
        foreach ($images as $image) {
            if ($image) {
                $path = $image->store('products', 'public');
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }
    }
}
