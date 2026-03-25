<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_number',
        'paymentable_type',
        'paymentable_id',
        'payer_type',
        'payer_id',
        'user_id',
        'amount',
        'payment_method',
        'payment_date',
        'reference_number',
        'notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    /**
     * Get the paymentable model (invoice).
     */
    public function paymentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the payer model (customer or supplier).
     */
    public function payer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who recorded the payment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include cancelled payments.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to filter by payment method.
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('payment_date', [$from, $to]);
    }

    /**
     * Get formatted payment method.
     */
    public function getFormattedMethodAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'credit_card' => 'بطاقة ائتمان',
            'other' => 'أخرى',
            default => $this->payment_method,
        };
    }

    /**
     * Check if payment is for purchase invoice.
     */
    public function isPurchasePayment(): bool
    {
        return $this->paymentable_type === PurchaseInvoice::class;
    }

    /**
     * Check if payment is for sale invoice.
     */
    public function isSalePayment(): bool
    {
        return $this->paymentable_type === SaleInvoice::class;
    }
}
