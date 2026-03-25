<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\InventoryMovement;
use App\Models\ProductWarehouse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * InventoryService
 * 
 * خدمة إدارة المخزون:
 * - إضافة وخصم المخزون
 * - نقل المخزون بين المخازن
 * - تعديل المخزون (Adjustment)
 * - تتبع حركات المخزون
 */
class InventoryService
{
    /**
     * إضافة مخزون لمنتج في مخزن
     * 
     * @param Product $product
     * @param Warehouse $warehouse
     * @param float $quantity الكمية (بالوحدة الأساسية)
     * @param array $metadata بيانات إضافية (invoice_id, notes, etc.)
     * @return InventoryMovement
     */
    public function addStock(Product $product, Warehouse $warehouse, float $quantity, array $metadata = []): InventoryMovement
    {
        if ($quantity <= 0) {
            throw new \Exception("Quantity must be greater than zero.");
        }

        return DB::transaction(function () use ($product, $warehouse, $quantity, $metadata) {
            // تحديث أو إنشاء سجل المخزون
            $productWarehouse = ProductWarehouse::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                ]
            );

            $oldQuantity = $productWarehouse->quantity;
            $productWarehouse->quantity += $quantity;
            $productWarehouse->save();

            // تسجيل الحركة
            return InventoryMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'movement_type' => 'in',
                'quantity' => $quantity,
                'quantity_before' => $oldQuantity,
                'quantity_after' => $productWarehouse->quantity,
                'reference_type' => $metadata['reference_type'] ?? null,
                'reference_id' => $metadata['reference_id'] ?? null,
                'notes' => $metadata['notes'] ?? null,
                'user_id' => auth()->id() ?? 1,
                'movement_date' => now(),
            ]);
        });
    }

    /**
     * خصم مخزون من منتج في مخزن
     * 
     * @param Product $product
     * @param Warehouse $warehouse
     * @param float $quantity الكمية (بالوحدة الأساسية)
     * @param array $metadata
     * @return InventoryMovement
     */
    public function removeStock(Product $product, Warehouse $warehouse, float $quantity, array $metadata = []): InventoryMovement
    {
        if ($quantity <= 0) {
            throw new \Exception("Quantity must be greater than zero.");
        }

        return DB::transaction(function () use ($product, $warehouse, $quantity, $metadata) {
            $productWarehouse = ProductWarehouse::where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->first();

            if (!$productWarehouse) {
                throw new \Exception("Product not found in warehouse.");
            }

            $availableQuantity = $productWarehouse->quantity - $productWarehouse->reserved_quantity;

            if ($availableQuantity < $quantity) {
                throw new \Exception("Insufficient stock. Available: {$availableQuantity}, Requested: {$quantity}");
            }

            $oldQuantity = $productWarehouse->quantity;
            $productWarehouse->quantity -= $quantity;
            $productWarehouse->save();

            // تسجيل الحركة
            return InventoryMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'movement_type' => 'out',
                'quantity' => -$quantity,
                'quantity_before' => $oldQuantity,
                'quantity_after' => $productWarehouse->quantity,
                'reference_type' => $metadata['reference_type'] ?? null,
                'reference_id' => $metadata['reference_id'] ?? null,
                'notes' => $metadata['notes'] ?? null,
                'user_id' => auth()->id() ?? 1,
                'movement_date' => now(),
            ]);
        });
    }

    /**
     * نقل مخزون بين مخزنين
     * 
     * @param Product $product
     * @param Warehouse $fromWarehouse
     * @param Warehouse $toWarehouse
     * @param float $quantity
     * @param string|null $notes
     * @return array ['from_movement' => InventoryMovement, 'to_movement' => InventoryMovement]
     */
    public function transferStock(
        Product $product,
        Warehouse $fromWarehouse,
        Warehouse $toWarehouse,
        float $quantity,
        ?string $notes = null
    ): array {
        if ($quantity <= 0) {
            throw new \Exception("Quantity must be greater than zero.");
        }

        if ($fromWarehouse->id === $toWarehouse->id) {
            throw new \Exception("Cannot transfer to the same warehouse.");
        }

        return DB::transaction(function () use ($product, $fromWarehouse, $toWarehouse, $quantity, $notes) {
            // خصم من المخزن الأول
            $fromMovement = $this->removeStock($product, $fromWarehouse, $quantity, [
                'reference_type' => 'transfer',
                'notes' => $notes ?? "Transfer to {$toWarehouse->name}",
            ]);

            // إضافة للمخزن الثاني
            $toMovement = $this->addStock($product, $toWarehouse, $quantity, [
                'reference_type' => 'transfer',
                'notes' => $notes ?? "Transfer from {$fromWarehouse->name}",
            ]);

            return [
                'from_movement' => $fromMovement,
                'to_movement' => $toMovement,
            ];
        });
    }

    /**
     * تعديل المخزون (Adjustment)
     * يستخدم لتصحيح الأخطاء أو الجرد
     * 
     * @param Product $product
     * @param Warehouse $warehouse
     * @param float $newQuantity الكمية الجديدة الصحيحة
     * @param string $reason سبب التعديل
     * @return InventoryMovement
     */
    public function adjustStock(
        Product $product,
        Warehouse $warehouse,
        float $newQuantity,
        string $reason
    ): InventoryMovement {
        if ($newQuantity < 0) {
            throw new \Exception("New quantity cannot be negative.");
        }

        return DB::transaction(function () use ($product, $warehouse, $newQuantity, $reason) {
            $productWarehouse = ProductWarehouse::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                ]
            );

            $oldQuantity = $productWarehouse->quantity;
            $difference = $newQuantity - $oldQuantity;

            $productWarehouse->quantity = $newQuantity;
            $productWarehouse->save();

            // تسجيل الحركة
            return InventoryMovement::create([
                'product_id' => $product->id,
                'warehouse_id' => $warehouse->id,
                'movement_type' => 'adjustment',
                'quantity' => $difference,
                'quantity_before' => $oldQuantity,
                'quantity_after' => $newQuantity,
                'reference_type' => 'adjustment',
                'reference_id' => null,
                'notes' => "Adjustment: {$reason}. Old: {$oldQuantity}, New: {$newQuantity}",
                'user_id' => auth()->id() ?? 1,
                'movement_date' => now(),
            ]);
        });
    }

    /**
     * الحصول على حركات المخزون لمنتج
     * 
     * @param Product $product
     * @param Warehouse|null $warehouse
     * @param int $limit
     * @return Collection
     */
    public function getMovements(Product $product, ?Warehouse $warehouse = null, int $limit = 50): Collection
    {
        $query = InventoryMovement::where('product_id', $product->id)
            ->with(['warehouse', 'user']);

        if ($warehouse) {
            $query->where('warehouse_id', $warehouse->id);
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * الحصول على المخزون الحالي لمنتج في جميع المخازن
     * 
     * @param Product $product
     * @return Collection
     */
    public function getStockByWarehouses(Product $product): Collection
    {
        return ProductWarehouse::where('product_id', $product->id)
            ->with('warehouse')
            ->get()
            ->map(function ($stock) {
                return [
                    'warehouse_id' => $stock->warehouse_id,
                    'warehouse_name' => $stock->warehouse->name,
                    'quantity' => $stock->quantity,
                    'reserved_quantity' => $stock->reserved_quantity,
                    'available_quantity' => $stock->quantity - $stock->reserved_quantity,
                ];
            });
    }

    /**
     * الحصول على إجمالي المخزون لمنتج
     * 
     * @param Product $product
     * @return array
     */
    public function getTotalStock(Product $product): array
    {
        $total = ProductWarehouse::where('product_id', $product->id)
            ->sum('quantity');

        $reserved = ProductWarehouse::where('product_id', $product->id)
            ->sum('reserved_quantity');

        return [
            'total_quantity' => $total,
            'reserved_quantity' => $reserved,
            'available_quantity' => $total - $reserved,
        ];
    }

    /**
     * حجز مخزون (للطلبات قيد المعالجة)
     * 
     * @param Product $product
     * @param Warehouse $warehouse
     * @param float $quantity
     * @return bool
     */
    public function reserveStock(Product $product, Warehouse $warehouse, float $quantity): bool
    {
        if ($quantity <= 0) {
            throw new \Exception("Quantity must be greater than zero.");
        }

        return DB::transaction(function () use ($product, $warehouse, $quantity) {
            $productWarehouse = ProductWarehouse::where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->lockForUpdate()
                ->first();

            if (!$productWarehouse) {
                throw new \Exception("Product not found in warehouse.");
            }

            $availableQuantity = $productWarehouse->quantity - $productWarehouse->reserved_quantity;

            if ($availableQuantity < $quantity) {
                throw new \Exception("Insufficient stock to reserve.");
            }

            $productWarehouse->reserved_quantity += $quantity;
            $productWarehouse->save();

            return true;
        });
    }

    /**
     * إلغاء حجز مخزون
     * 
     * @param Product $product
     * @param Warehouse $warehouse
     * @param float $quantity
     * @return bool
     */
    public function unreserveStock(Product $product, Warehouse $warehouse, float $quantity): bool
    {
        if ($quantity <= 0) {
            throw new \Exception("Quantity must be greater than zero.");
        }

        return DB::transaction(function () use ($product, $warehouse, $quantity) {
            $productWarehouse = ProductWarehouse::where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->lockForUpdate()
                ->first();

            if (!$productWarehouse) {
                throw new \Exception("Product not found in warehouse.");
            }

            if ($productWarehouse->reserved_quantity < $quantity) {
                throw new \Exception("Cannot unreserve more than reserved quantity.");
            }

            $productWarehouse->reserved_quantity -= $quantity;
            $productWarehouse->save();

            return true;
        });
    }

    /**
     * الحصول على المنتجات ذات المخزون المنخفض
     * 
     * @param Warehouse|null $warehouse
     * @param int $limit
     * @return Collection
     */
    public function getLowStockProducts(?Warehouse $warehouse = null, int $limit = 20): Collection
    {
        $query = Product::whereNotNull('min_stock')
            ->whereHas('warehouseStock', function ($q) use ($warehouse) {
                if ($warehouse) {
                    $q->where('warehouse_id', $warehouse->id);
                }
            })
            ->with(['warehouseStock' => function ($q) use ($warehouse) {
                if ($warehouse) {
                    $q->where('warehouse_id', $warehouse->id);
                }
            }]);

        return $query->get()
            ->filter(function ($product) {
                $totalStock = $product->warehouseStock->sum('quantity');
                return $totalStock < $product->min_stock;
            })
            ->take($limit);
    }
}
