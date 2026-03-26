<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($customer) {
            $opening = (float)($customer->opening_balance ?? 0);
            $customer->total_invoices = $opening; // Starts with opening balance (signed)
            $customer->total_paid = 0;           // Actual payments sum starts at 0
            $customer->current_balance = $opening;
        });

        static::updating(function ($customer) {
            if ($customer->isDirty('opening_balance')) {
                // When opening balance changes, recalculate everything
                $customer->recalculateBalance();
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'phone_2',
        'address',
        'city',
        'country',
        'tax_number',
        'credit_limit',
        'current_balance',
        'customer_type',
        'company_name',
        'notes',
        'internal_notes',
        'opening_balance',
        'total_invoices',
        'total_paid',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'credit_limit' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'total_invoices' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['available_credit', 'display_name'];

    /**
     * Get all sale invoices.
     */
    public function saleInvoices(): HasMany
    {
        return $this->hasMany(SaleInvoice::class);
    }

    /**
     * Get all payments (polymorphic).
     */
    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payer');
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include individual customers.
     */
    public function scopeIndividual($query)
    {
        return $query->where('customer_type', 'individual');
    }

    /**
     * Scope a query to only include company customers.
     */
    public function scopeCompany($query)
    {
        return $query->where('customer_type', 'company');
    }

    /**
     * Scope a query to search customers.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Get available credit.
     */
    public function getAvailableCreditAttribute(): float
    {
        return max(0, $this->credit_limit - $this->current_balance);
    }

    /**
     * Get display name (company name if exists, otherwise name).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->customer_type === 'company' && $this->company_name) {
            return $this->company_name;
        }

        return $this->name;
    }

    /**
     * Check if customer has available credit.
     */
    public function hasAvailableCredit(float $amount = 0): bool
    {
        return $this->available_credit >= $amount;
    }

    /**
     * Update balance.
     */
    public function updateBalance(float $amount): void
    {
        $this->increment('current_balance', $amount);
    }

    /**
     * Recalculate financial balances from scratch
     */
    public function recalculateBalance(): void
    {
        $totalInvoices = (float) $this->saleInvoices()->where('status', 'completed')->sum('total_amount');
        $totalPaid = (float) $this->payments()->where('status', 'completed')->sum('amount');
        
        $this->updateQuietly([
            'total_invoices' => (float)$this->opening_balance + $totalInvoices,
            'total_paid' => $totalPaid,
            'current_balance' => ((float)$this->opening_balance + $totalInvoices) - $totalPaid
        ]);
    }

    /**
     * Get total sales amount.
     */
    public function getTotalSalesAttribute(): float
    {
        return $this->saleInvoices()
            ->where('status', 'completed')
            ->sum('total_amount');
    }

    /**
     * Get total paid amount.
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()
            ->where('status', 'completed')
            ->sum('amount');
    }
}
