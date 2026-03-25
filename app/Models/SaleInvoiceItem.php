<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleInvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sale_invoice_id',
        'product_id',
        'product_unit_id',
        'quantity',
        'cost_price',
        'unit_price',
        'tax_amount',
        'discount_amount',
        'total',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:3',
        'cost_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['subtotal', 'profit', 'quantity_in_base_unit'];

    /**
     * Get the sale invoice.
     */
    public function saleInvoice(): BelongsTo
    {
        return $this->belongsTo(SaleInvoice::class)->withTrashed();
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Get the product unit.
     */
    public function productUnit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class);
    }

    /**
     * Get subtotal (quantity × unit_price).
     */
    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get profit ((unit_price - cost_price) × quantity).
     */
    public function getProfitAttribute(): float
    {
        return ($this->unit_price - $this->cost_price) * $this->quantity;
    }

    /**
     * Get quantity converted to base unit.
     */
    public function getQuantityInBaseUnitAttribute(): float
    {
        if (!$this->productUnit) {
            return $this->quantity;
        }

        return $this->productUnit->toBaseUnit($this->quantity);
    }

    /**
     * Calculate and update total.
     */
    public function calculateTotal(): void
    {
        $this->total = $this->subtotal + $this->tax_amount - $this->discount_amount;
        $this->save();
    }

    /**
     * Get profit margin percentage.
     */
    public function getProfitMarginPercentageAttribute(): float
    {
        if ($this->cost_price == 0) {
            return 0;
        }

        return (($this->unit_price - $this->cost_price) / $this->cost_price) * 100;
    }
}
