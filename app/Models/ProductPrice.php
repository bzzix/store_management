<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'purchase_invoice_id',
        'user_id',
        'cost_price',
        'base_price',
        'effective_from',
        'effective_to',
        'is_current',
        'change_reason',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cost_price' => 'decimal:2',
        'base_price' => 'decimal:2',
        'effective_from' => 'datetime',
        'effective_to' => 'datetime',
        'is_current' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($price) {
            if (is_null($price->effective_from)) {
                $price->effective_from = now();
            }

            // Set previous prices as not current
            if ($price->is_current) {
                ProductPrice::where('product_id', $price->product_id)
                    ->where('is_current', true)
                    ->update([
                        'is_current' => false,
                        'effective_to' => now(),
                    ]);
            }
        });
    }

    /**
     * Get the product that owns the price.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the purchase invoice that triggered this price.
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    /**
     * Get the user who created this price record.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include current prices.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope a query to only include prices from purchases.
     */
    public function scopeFromPurchase($query)
    {
        return $query->where('change_reason', 'purchase');
    }

    /**
     * Scope a query to only include manual price changes.
     */
    public function scopeManual($query)
    {
        return $query->where('change_reason', 'manual');
    }

    /**
     * Get price change percentage from previous price.
     */
    public function getChangePercentageAttribute(): ?float
    {
        $previous = ProductPrice::where('product_id', $this->product_id)
            ->where('effective_from', '<', $this->effective_from)
            ->orderByDesc('effective_from')
            ->first();

        if (!$previous || $previous->base_price == 0) {
            return null;
        }

        return (($this->base_price - $previous->base_price) / $previous->base_price) * 100;
    }
}

