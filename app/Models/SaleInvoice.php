<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleInvoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'warehouse_id',
        'user_id',
        'sale_method_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'tax_percentage',
        'discount_amount',
        'discount_percentage',
        'shipping_cost',
        'total_amount',
        'paid_amount',
        'total_cost',
        'status',
        'payment_status',
        'notes',
        'internal_notes',
        'previous_balance',
        'car_number',
        'driver_name',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['remaining_amount', 'total_profit'];

    /**
     * Get the customer.
     */

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the warehouse.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the user who created the invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sale method.
     */
    public function saleMethod(): BelongsTo
    {
        return $this->belongsTo(SaleMethod::class);
    }

    /**
     * Get all invoice items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(SaleInvoiceItem::class);
    }

    /**
     * Get all payments (polymorphic).
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'paymentable');
    }

    /**
     * Scope a query to only include completed invoices.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending invoices.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include draft invoices.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include unpaid invoices.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    /**
     * Get remaining amount to be paid.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    /**
     * Get total profit (subtotal - total_cost).
     */
    public function getTotalProfitAttribute(): float
    {
        return $this->subtotal - $this->total_cost;
    }

    /**
     * Check if invoice is fully paid.
     */
    public function isFullyPaid(): bool
    {
        return $this->remaining_amount <= 0.01;
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !$this->isFullyPaid();
    }

    /**
     * Calculate totals from items.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total');
        $this->total_cost = $this->items->sum(function ($item) {
            return $item->cost_price * $item->quantity;
        });
        $this->total_amount = $this->subtotal + $this->tax_amount + $this->shipping_cost - $this->discount_amount;
        $this->save();
    }

    /**
     * Update payment status based on paid amount.
     */
    public function updatePaymentStatus(): void
    {
        if ($this->paid_amount <= 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->isFullyPaid()) {
            $this->payment_status = 'paid';
        } else {
            $this->payment_status = 'partial';
        }

        $this->save();
    }

    /**
     * Get profit margin percentage.
     */
    public function getProfitMarginPercentageAttribute(): float
    {
        if ($this->total_cost == 0) {
            return 0;
        }

        return ($this->total_profit / $this->total_cost) * 100;
    }
}
