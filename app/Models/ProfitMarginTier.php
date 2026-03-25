<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleMethod;

class ProfitMarginTier extends Model
{
    protected $table = 'profit_margin_tiers';

    protected $fillable = [
        'name',
        'min_value',
        'max_value',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'priority'  => 'integer',
        'is_active' => 'boolean',
    ];

    public function saleMethods()
    {
        return $this->belongsToMany(
            SaleMethod::class,
            'profit_margin_tier_methods'
        )
        ->using(ProfitMarginTierMethod::class)
        ->withPivot('profit_value')
        ->withTimestamps();
    }

    /**
     * جلب الشريحة المناسبة لسعر معين وحساب السعر النهائي بدلالة طريقة البيع
     */
    public static function resolveForPrice( float $price, string $saleMethodCode): ?array {

        $tier = self::where('is_active', true)
            ->where('min_value', '<=', $price)
            ->where(function ($q) use ($price) {
                $q->whereNull('max_value')
                ->orWhere('max_value', '>=', $price);
            })
            ->orderByDesc('priority')
            ->first();

        if (!$tier) {
            return null;
        }

        $method = $tier->saleMethods()
            ->where('code', $saleMethodCode)
            ->where('is_active', true)
            ->first();

        if (!$method) {
            return null;
        }

        return [
            'tier'        => $tier,
            'profit'      => $method->pivot->profit_value,
            'final_price' => $price + $method->pivot->profit_value,
        ];
    }

    // مثال استخدام:
    // $result = ProfitMarginTier::resolveForPrice(1000, 'installment');
    // $finalPrice = $result['final_price'] ?? 1000;

    // use App\Services\PricingService;

    // $pricing = app(PricingService::class)
    //     ->calculate(1000, 'installment');

    // $finalPrice = $pricing['final_price'];

    // if ($pricing['fallback']) {
    //     // ممكن تعمل log أو تنبيه
    // }

}
