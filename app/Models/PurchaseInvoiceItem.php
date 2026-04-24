<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseInvoiceItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'purchase_invoice_id',
        'product_id',
        'product_unit_id',
        'quantity',
        'unit_price',
        'tax_amount',
        'discount_amount',
        'total',
        'is_custom',
        'custom_name',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'is_custom' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['subtotal', 'quantity_in_base_unit'];

    /**
     * Get the purchase invoice.
     */
    public function purchaseInvoice(): BelongsTo
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    /**
     * Get the product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
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
}
