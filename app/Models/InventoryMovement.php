<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'user_id',
        'movement_type',
        'reference_type',
        'reference_id',
        'quantity',
        'quantity_before',
        'quantity_after',
        'unit_cost',
        'notes',
        'movement_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:3',
        'quantity_before' => 'decimal:3',
        'quantity_after' => 'decimal:3',
        'unit_cost' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['total_cost'];

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
     * Get the user who created the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model (polymorphic).
     */
    public function reference()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    /**
     * Scope a query to only include IN movements.
     */
    public function scopeIn($query)
    {
        return $query->where('movement_type', 'in');
    }

    /**
     * Scope a query to only include OUT movements.
     */
    public function scopeOut($query)
    {
        return $query->where('movement_type', 'out');
    }

    /**
     * Scope a query to only include TRANSFER movements.
     */
    public function scopeTransfer($query)
    {
        return $query->where('movement_type', 'transfer');
    }

    /**
     * Scope a query to only include ADJUSTMENT movements.
     */
    public function scopeAdjustment($query)
    {
        return $query->where('movement_type', 'adjustment');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('movement_date', [$from, $to]);
    }

    /**
     * Get total cost (quantity × unit_cost).
     */
    public function getTotalCostAttribute(): ?float
    {
        if (is_null($this->unit_cost)) {
            return null;
        }

        return abs($this->quantity) * $this->unit_cost;
    }

    /**
     * Get movement direction (+ or -).
     */
    public function getDirectionAttribute(): string
    {
        return $this->quantity >= 0 ? '+' : '-';
    }

    /**
     * Get formatted movement type.
     */
    public function getFormattedTypeAttribute(): string
    {
        return match($this->movement_type) {
            'in' => 'إضافة',
            'out' => 'خصم',
            'transfer' => 'نقل',
            'adjustment' => 'تعديل',
            'return' => 'مرتجع',
            default => $this->movement_type,
        };
    }
}
