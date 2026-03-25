<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductWarehouse extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_warehouse';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'reserved_quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:3',
        'reserved_quantity' => 'decimal:3',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['available_quantity'];

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the warehouse.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get available quantity (quantity - reserved).
     */
    public function getAvailableQuantityAttribute(): float
    {
        return max(0, $this->quantity - $this->reserved_quantity);
    }

    /**
     * Add stock to this warehouse.
     */
    public function addStock(float $quantity): void
    {
        $this->increment('quantity', $quantity);
    }

    /**
     * Remove stock from this warehouse.
     */
    public function removeStock(float $quantity): void
    {
        $this->decrement('quantity', $quantity);
    }

    /**
     * Reserve stock.
     */
    public function reserveStock(float $quantity): void
    {
        if ($quantity > $this->available_quantity) {
            throw new \Exception('Insufficient available stock to reserve');
        }

        $this->increment('reserved_quantity', $quantity);
    }

    /**
     * Release reserved stock.
     */
    public function releaseStock(float $quantity): void
    {
        $this->decrement('reserved_quantity', min($quantity, $this->reserved_quantity));
    }
}
