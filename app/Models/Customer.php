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
