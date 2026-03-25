<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProfitMarginTierMethod extends Pivot
{
    protected $table = 'profit_margin_tier_methods';

    protected $fillable = [
        'profit_margin_tier_id',
        'sale_method_id',
        'profit_value',
    ];

    public function profitMarginTier()
    {
        return $this->belongsTo(ProfitMarginTier::class);
    }

    public function saleMethod()
    {
        return $this->belongsTo(SaleMethod::class);
    }

    protected $casts = [
        'profit_value' => 'decimal:2',
    ];
}
