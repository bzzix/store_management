<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SaleMethod extends Model
{
    protected $table = 'sale_methods';

    protected $fillable = [
        'name',
        'code',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'priority'  => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * الشرائح المرتبطة بطريقة البيع
     */
    public function profitMarginTiers()
    {
        return $this->belongsToMany(
            ProfitMarginTier::class,
            'profit_margin_tier_methods'
        )
        ->using(ProfitMarginTierMethod::class)
        ->withPivot('profit_value')
        ->withTimestamps();
    }

    /**
     * Scope a query to only include active sale methods.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
