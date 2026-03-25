<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'warehouse_id',
        'profit_margin_tier_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'short_description',
        'current_cost_price',
        'current_base_price',
        'base_unit',
        'weight',
        'stock_quantity',
        'min_stock_level',
        'max_stock_level',
        'main_image',
        'is_active',
        'is_featured',
        'track_inventory',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'current_cost_price' => 'decimal:2',
        'current_base_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock_quantity' => 'decimal:3',
        'min_stock_level' => 'decimal:3',
        'max_stock_level' => 'decimal:3',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'track_inventory' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['available_stock', 'is_low_stock'];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'PRD-' . strtoupper(Str::random(8));
            }
        });

        static::created(function ($product) {
            // Create default unit
            $product->units()->create([
                'unit_name' => ucfirst($product->base_unit),
                'unit_name_en' => $product->base_unit,
                'unit_value' => 1.000,
                'is_default' => true,
            ]);
        });
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the default warehouse.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the profit margin tier.
     */
    public function profitMarginTier(): BelongsTo
    {
        return $this->belongsTo(ProfitMarginTier::class);
    }

    /**
     * Get all price history records.
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class)->orderByDesc('effective_from');
    }

    /**
     * Get all product units.
     */
    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class);
    }

    /**
     * Get all product images.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get warehouse stock records.
     */
    public function warehouseStock(): HasMany
    {
        return $this->hasMany(ProductWarehouse::class);
    }

    /**
     * Get all warehouses that have this product.
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse')
            ->withPivot('quantity', 'reserved_quantity')
            ->withTimestamps();
    }

    /**
     * Get purchase invoice items.
     */
    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    /**
     * Get sale invoice items.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleInvoiceItem::class);
    }

    /**
     * Get inventory movements.
     */
    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include products with low stock.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('track_inventory', true);
    }

    /**
     * Scope a query to only include products with stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Scope a query to search products.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%");
        });
    }

    /**
     * Get the current price record.
     */
    public function getCurrentPriceAttribute()
    {
        return $this->prices()->where('is_current', true)->first();
    }

    /**
     * Get available stock (total - reserved).
     */
    public function getAvailableStockAttribute(): float
    {
        $reserved = $this->warehouseStock()->sum('reserved_quantity');
        return max(0, $this->stock_quantity - $reserved);
    }

    /**
     * Check if product is low on stock.
     */
    public function getIsLowStockAttribute(): bool
    {
        if (!$this->track_inventory) {
            return false;
        }

        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Get the default unit.
     */
    public function getDefaultUnitAttribute()
    {
        return $this->units()->where('is_default', true)->first();
    }

    /**
     * Check if product has price.
     */
    public function hasPrice(): bool
    {
        return !is_null($this->current_base_price);
    }

    /**
     * Get total value of stock (quantity × cost price).
     */
    public function getStockValueAttribute(): float
    {
        if (!$this->current_cost_price) {
            return 0;
        }

        return $this->stock_quantity * $this->current_cost_price;
    }
}
