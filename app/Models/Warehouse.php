<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'is_main',
        'capacity',
        'current_stock_value',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'current_stock_value' => 'decimal:2',
    ];

    /**
     * مدير المخزن
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Scope a query to only include active warehouses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
