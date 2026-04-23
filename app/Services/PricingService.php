<?php

namespace App\Services;

use App\Models\ProfitMarginTier;
use App\Models\SaleMethod;

class PricingService
{
    /**
     * حساب السعر النهائي حسب طريقة البيع والشريحة السعرية
     * 
     * @param float $basePrice السعر الأساسي
     * @param string $saleMethodCode كود طريقة البيع (cash, installment, credit)
     * @return array النتيجة تحتوي على السعر النهائي والربح والتفاصيل
     */
    public function calculate(
        float $basePrice,
        string $saleMethodCode
    ): array {

        // التحقق من صحة طريقة البيع
        if (!$this->isValidSaleMethod($saleMethodCode)) {
            return $this->fallback(
                $basePrice,
                'invalid_sale_method'
            );
        }

        /**
         * تم تعطيل نظام الشرائح السعرية مؤقتاً بناءً على طلب العميل
         * والاعتماد على النسب الثابتة (fallback) بشكل مباشر.
         */
        return $this->fallback(
            $basePrice,
            'temporary_fixed_margin',
            null,
            $saleMethodCode
        );

        // جلب الشريحة المناسبة للسعر
        // $tier = ProfitMarginTier::where('is_active', true)
        // ... (كود معطل مؤقتاً)
    }

    /**
     * التحقق من أن طريقة البيع موجودة ومفعلة
     */
    protected function isValidSaleMethod(string $code): bool
    {
        return SaleMethod::where('code', $code)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Fallback ذكي وآمن عند عدم وجود بيانات مناسبة
     */
    protected function fallback(
        float $basePrice,
        string $reason,
        $tier = null,
        string $saleMethodCode = 'cash'
    ): array {
        $percentages = [
            'cash' => 0.025,        // 2.5%
            'installment' => 0.05,  // 5%
            'credit' => 0.20,       // 20% (updated from 30%)
        ];

        $percentage = $percentages[$saleMethodCode] ?? 0;
        $profit = ($basePrice + 2) * $percentage;

        // تطبيق حدود الربح للكاش فقط (أقل ربح 15 وأعلى ربح 30)
        if ($saleMethodCode === 'cash') {
            $profit = max(15.0, min(30.0, (float)$profit));
        }

        // تقريب الربح والسعر لأقرب رقم صحيح (بدون كسور)
        $finalPrice = ceil($basePrice + $profit);
        $actualProfit = ceil($finalPrice - $basePrice);

        return [
            'base_price'  => $basePrice,
            'final_price' => (float)$finalPrice,
            'profit'      => (float)$actualProfit,
            'tier'        => $tier,
            'sale_method' => $saleMethodCode,
            'fallback'    => $reason,
        ];
    }

    /**
     * الحصول على جميع طرق البيع المتاحة
     */
    public function availableSaleMethods(): array
    {
        return SaleMethod::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->pluck('name', 'code')
            ->toArray();
    }

    /**
     * حساب السعر مع معالجة الأخطاء
     */
    public function safeCalculate(float $basePrice, string $saleMethodCode): array
    {
        try {
            return $this->calculate($basePrice, $saleMethodCode);
        } catch (\Exception $e) {
            return $this->fallback($basePrice, 'error: ' . $e->getMessage());
        }
    }
}