<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\SaleMethod;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

/**
 * PricingPreviewController
 * 
 * كونتروللر معاينة التسعير
 * يتعامل مع عرض معاينة التسعير والسعر النهائي لمنتج معين
 */
class PricingPreviewController extends Controller
{
    /**
     * @var PricingService خدمة حساب الأسعار
     */
    protected PricingService $pricingService;

    /**
     * Constructor
     * 
     * @param PricingService $pricingService
     */
    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * عرض صفحة معاينة التسعير
     * 
     * GET /dashboard/pricing/preview
     * 
     * @return View
     */
    public function index(): View
    {
        // جلب طرق البيع المتاحة
        $saleMethods = $this->pricingService->availableSaleMethods();

        return view('dashboard.pricing.preview', compact('saleMethods'));
    }

    /**
     * حساب السعر النهائي (Ajax Request)
     * 
     * POST /dashboard/pricing/calculate
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function calculate(Request $request): JsonResponse
    {
        // التحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'base_price' => 'required|numeric|min:0',
            'sale_method_code' => 'required|string|in:cash,installment,credit',
        ]);

        // حساب السعر النهائي بشكل آمن
        $result = $this->pricingService->safeCalculate(
            (float) $validated['base_price'],
            $validated['sale_method_code']
        );

        // التحقق من وجود خطأ
        if ($result['fallback']) {
            return response()->json([
                'success' => false,
                'error' => $result['fallback'],
                'message' => 'خطأ في حساب السعر'
            ], 400);
        }

        // إرجاع النتيجة بنجاح
        return response()->json([
            'success' => true,
            'data' => [
                'base_price' => $result['base_price'],
                'profit' => $result['profit'],
                'final_price' => $result['final_price'],
                'tier_name' => $result['tier']?->name ?? 'بدون شريحة',
                'sale_method' => $result['sale_method'],
            ]
        ]);
    }

    /**
     * عرض تقرير التسعير الكامل
     * 
     * GET /dashboard/pricing/report
     * 
     * @return View
     */
    public function report(): View
    {
        // جلب جميع الشرائح السعرية مع طرق البيع
        $tiers = \App\Models\ProfitMarginTier::with('saleMethods')
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        return view('dashboard.pricing.report', compact('tiers'));
    }
}
