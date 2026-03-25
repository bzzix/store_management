<?php

namespace App\Services;

use App\Models\ProfitMarginTier;
use App\Models\SaleMethod;
use Illuminate\Validation\ValidationException;

class ProfitMarginTierService
{
    /**
     * إضافة شريحة سعرية جديدة
     *
     * @param array $data بيانات الشريحة السعرية
     * @return ProfitMarginTier
     * @throws ValidationException
     */
    public function createTier(array $data): ProfitMarginTier
    {
        $this->validateTierData($data);

        // التحقق من عدم تضارب القيم
        if ($data['max_value'] !== null && (float)$data['min_value'] > (float)$data['max_value']) {
            throw new \InvalidArgumentException(
                __('Min value cannot be greater than max value')
            );
        }

        // التحقق من عدم تضارب الشرائح الموجودة
        $this->validateTierRange(
            (float)$data['min_value'],
            $data['max_value'] ? (float)$data['max_value'] : null
        );

        return ProfitMarginTier::create([
            'name' => $data['name'],
            'min_value' => (float)$data['min_value'],
            'max_value' => $data['max_value'] ? (float)$data['max_value'] : null,
            'priority' => $data['priority'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * تحديث شريحة سعرية
     *
     * @param ProfitMarginTier $tier الشريحة المراد تحديثها
     * @param array $data البيانات الجديدة
     * @return ProfitMarginTier
     * @throws ValidationException
     */
    public function updateTier(ProfitMarginTier $tier, array $data): ProfitMarginTier
    {
        $this->validateTierData($data);

        // التحقق من عدم تضارب القيم
        if ($data['max_value'] !== null && (float)$data['min_value'] > (float)$data['max_value']) {
            throw new \InvalidArgumentException(
                __('Min value cannot be greater than max value')
            );
        }

        // التحقق من عدم تضارب الشرائح الموجودة (باستثناء الشريحة الحالية)
        $this->validateTierRange(
            (float)$data['min_value'],
            $data['max_value'] ? (float)$data['max_value'] : null,
            $tier->id
        );

        $tier->update([
            'name' => $data['name'] ?? $tier->name,
            'min_value' => isset($data['min_value']) ? (float)$data['min_value'] : $tier->min_value,
            'max_value' => isset($data['max_value']) ? ($data['max_value'] ? (float)$data['max_value'] : null) : $tier->max_value,
            'priority' => $data['priority'] ?? $tier->priority,
            'is_active' => isset($data['is_active']) ? (bool)$data['is_active'] : $tier->is_active,
        ]);

        return $tier->fresh();
    }

    /**
     * حذف شريحة سعرية
     *
     * @param ProfitMarginTier $tier الشريحة المراد حذفها
     * @return bool
     */
    public function deleteTier(ProfitMarginTier $tier): bool
    {
        // حذف العلاقات أولاً
        $tier->saleMethods()->detach();

        // ثم حذف الشريحة
        return $tier->delete();
    }

    /**
     * التحقق من صحة بيانات الشريحة السعرية
     *
     * @param array $data البيانات المراد التحقق منها
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateTierData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException(__('validation.required', ['attribute' => __('Name')]));
        }

        if (!isset($data['min_value']) || $data['min_value'] === '') {
            throw new \InvalidArgumentException(__('validation.required', ['attribute' => __('Min Value')]));
        }

        if ((float)$data['min_value'] < 0) {
            throw new \InvalidArgumentException(__('Min value must be a positive number'));
        }

        if (isset($data['max_value']) && $data['max_value'] !== null && (float)$data['max_value'] < 0) {
            throw new \InvalidArgumentException(__('Max value must be a positive number'));
        }
    }

    /**
     * التحقق من عدم تضارب نطاق الشريحة مع الشرائح الموجودة
     *
     * @param float $minValue القيمة الدنيا
     * @param float|null $maxValue القيمة العليا
     * @param int|null $excludeTierId معرف الشريحة المراد استثناؤها من البحث
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function validateTierRange(
        float $minValue,
        ?float $maxValue = null,
        ?int $excludeTierId = null
    ): void {
        // ابدأ الاستعلام
        $query = ProfitMarginTier::query();

        // أولاً: استثنِ الشريحة الحالية إن وجدت
        if ($excludeTierId !== null) {
            $query->where('id', '!=', $excludeTierId);
        }

        // ثانياً: ابحث عن شرائح متضاربة
        // نطاقان متضاربان إذا: min1 < max2 و min2 < max1
        $conflicting = $query
            ->where('min_value', '<', $maxValue === null ? 999999999 : $maxValue)
            ->where(function ($q) use ($minValue) {
                $q->whereNull('max_value')  // شريحة unlimited
                  ->orWhere('max_value', '>', $minValue);
            })
            ->first();

        if ($conflicting) {
            throw new \InvalidArgumentException(
                __('This price range conflicts with an existing tier: :name', [
                    'name' => $conflicting->name
                ])
            );
        }
    }

    /**
     * ربط طريقة بيع بشريحة سعرية
     *
     * @param ProfitMarginTier $tier الشريحة السعرية
     * @param int $saleMethodId معرف طريقة البيع
     * @param float $profitValue قيمة الربح
     * @return void
     * @throws \InvalidArgumentException
     */
    public function attachSaleMethod(
        ProfitMarginTier $tier,
        int $saleMethodId,
        float $profitValue
    ): void {
        // التحقق من وجود طريقة البيع
        if (!SaleMethod::find($saleMethodId)) {
            throw new \InvalidArgumentException(__('Sale method not found'));
        }

        // التحقق من عدم ربط طريقة البيع مسبقاً
        if ($tier->saleMethods()->where('sale_methods.id', $saleMethodId)->exists()) {
            throw new \InvalidArgumentException(__('This sale method is already attached'));
        }

        $tier->saleMethods()->attach($saleMethodId, [
            'profit_value' => (float)$profitValue,
        ]);
    }

    /**
     * فصل طريقة بيع عن شريحة سعرية
     *
     * @param ProfitMarginTier $tier الشريحة السعرية
     * @param int $saleMethodId معرف طريقة البيع
     * @return bool
     */
    public function detachSaleMethod(ProfitMarginTier $tier, int $saleMethodId): bool
    {
        return $tier->saleMethods()->detach($saleMethodId) > 0;
    }

    /**
     * تحديث قيمة الربح لطريقة بيع في شريحة سعرية
     *
     * @param ProfitMarginTier $tier الشريحة السعرية
     * @param int $saleMethodId معرف طريقة البيع
     * @param float $profitValue قيمة الربح الجديدة
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function updateSaleMethodProfit(
        ProfitMarginTier $tier,
        int $saleMethodId,
        float $profitValue
    ): bool {
        // التحقق من وجود طريقة البيع
        if (!SaleMethod::find($saleMethodId)) {
            throw new \InvalidArgumentException(__('Sale method not found'));
        }

        // التحقق من ربط طريقة البيع بالشريحة
        if (!$tier->saleMethods()->where('sale_methods.id', $saleMethodId)->exists()) {
            throw new \InvalidArgumentException(__('This sale method is not attached to this tier'));
        }

        return $tier->saleMethods()->updateExistingPivot($saleMethodId, [
            'profit_value' => (float)$profitValue,
        ]) > 0;
    }

    /**
     * الحصول على جميع الشرائح السعرية النشطة
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveTiers()
    {
        return ProfitMarginTier::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('min_value', 'asc')
            ->get();
    }

    /**
     * الحصول على جميع الشرائح السعرية
     *
     * @param array $filters عوامل التصفية (search, sort_by, sort_direction)
     * @param int $perPage عدد النتائج في الصفحة
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTiers(array $filters = [], int $perPage = 15)
    {
        $query = ProfitMarginTier::query();

        // البحث
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'like', "%{$search}%");
        }

        // الفرز
        $sortBy = $filters['sort_by'] ?? 'priority';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage);
    }

    /**
     * الحصول على الشريحة المناسبة للسعر المعطى
     *
     * @param float $price السعر المعطى
     * @return ProfitMarginTier|null
     */
    public function getTierForPrice(float $price): ?ProfitMarginTier
    {
        return ProfitMarginTier::where('is_active', true)
            ->where('min_value', '<=', $price)
            ->where(function ($q) use ($price) {
                $q->whereNull('max_value')
                  ->orWhere('max_value', '>=', $price);
            })
            ->orderByDesc('priority')
            ->first();
    }

    /**
     * الحصول على إحصائيات الشرائح
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $totalTiers = ProfitMarginTier::count();
        $activeTiers = ProfitMarginTier::where('is_active', true)->count();
        $inactiveTiers = ProfitMarginTier::where('is_active', false)->count();

        return [
            'total_tiers' => $totalTiers,
            'active_tiers' => $activeTiers,
            'inactive_tiers' => $inactiveTiers,
        ];
    }
}
