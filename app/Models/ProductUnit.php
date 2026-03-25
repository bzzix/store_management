<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductUnit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'unit_name',
        'unit_name_en',
        'unit_value',
        'is_default',
        'barcode',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_value' => 'decimal:3',
        'is_default' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($unit) {
            // If this is set as default, unset other defaults
            if ($unit->is_default) {
                ProductUnit::where('product_id', $unit->product_id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });

        static::updating(function ($unit) {
            if ($unit->isDirty('is_default') && $unit->is_default) {
                ProductUnit::where('product_id', $unit->product_id)
                    ->where('id', '!=', $unit->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * Get the product that owns the unit.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get purchase invoice items using this unit.
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    /**
     * Get sale invoice items using this unit.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleInvoiceItem::class);
    }

    /**
     * Scope a query to only include default units.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Convert quantity from this unit to base unit.
     */
    public function toBaseUnit(float $quantity): float
    {
        return $quantity * $this->unit_value;
    }

    /**
     * Convert quantity from base unit to this unit.
     */
    public function fromBaseUnit(float $baseQuantity): float
    {
        return $baseQuantity / $this->unit_value;
    }

    /**
     * Get display name (with value if not default).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->is_default || $this->unit_value == 1) {
            return $this->unit_name;
        }

        return "{$this->unit_name} ({$this->unit_value} {$this->product->base_unit})";
    }
}
