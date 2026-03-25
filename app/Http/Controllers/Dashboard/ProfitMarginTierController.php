<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProfitMarginTier;
use App\Models\SaleMethod;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * ProfitMarginTierController
 * 
 * كونتروللر إدارة الشرائح السعرية (Profit Margin Tiers)
 * يتعامل مع عرض وإنشاء وتعديل وحذف الشرائح السعرية
 */
class ProfitMarginTierController extends Controller
{
    /**
     * عرض جميع الشرائح السعرية
     * 
     * GET /dashboard/pricing/tiers
     * 
     * @return View
     */
    public function index(): View
    {
        // جلب جميع الشرائح السعرية مع علاقاتها
        $tiers = ProfitMarginTier::with('saleMethods')
            ->orderBy('priority', 'desc')
            ->paginate(15);

        return view('dashboard.pricing.tiers.index', compact('tiers'));
    }

    /**
     * صفحة إنشاء شريحة سعرية جديدة
     * 
     * GET /dashboard/pricing/tiers/create
     * 
     * @return View
     */
    public function create(): View
    {
        // جلب طرق البيع المتاحة لربطها بالشريحة
        $saleMethods = SaleMethod::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        return view('dashboard.pricing.tiers.create', compact('saleMethods'));
    }

    /**
     * حفظ شريحة سعرية جديدة
     * 
     * POST /dashboard/pricing/tiers
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // التحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'nullable|numeric|gte:min_value',
            'priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'sale_methods' => 'array|min:1',
            'sale_methods.*.id' => 'required|exists:sale_methods,id',
            'sale_methods.*.profit_value' => 'required|numeric|min:0',
        ]);

        // إنشاء الشريحة السعرية
        $tier = ProfitMarginTier::create([
            'name' => $validated['name'],
            'min_value' => $validated['min_value'],
            'max_value' => $validated['max_value'],
            'priority' => $validated['priority'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // ربط طرق البيع بالشريحة
        if (!empty($validated['sale_methods'])) {
            $syncData = [];
            foreach ($validated['sale_methods'] as $method) {
                $syncData[$method['id']] = [
                    'profit_value' => $method['profit_value'],
                ];
            }
            $tier->saleMethods()->sync($syncData);
        }

        return redirect()
            ->route('pricing.tiers.index')
            ->with('success', 'تم إنشاء الشريحة السعرية بنجاح');
    }

    /**
     * صفحة تعديل شريحة سعرية
     * 
     * GET /dashboard/pricing/tiers/{tier}/edit
     * 
     * @param ProfitMarginTier $tier
     * @return View
     */
    public function edit(ProfitMarginTier $tier): View
    {
        // جلب طرق البيع المتاحة
        $saleMethods = SaleMethod::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        // جلب طرق البيع المرتبطة بالشريحة الحالية
        $connectedMethods = $tier->saleMethods()
            ->pluck('profit_value', 'sale_method_id')
            ->toArray();

        return view('dashboard.pricing.tiers.edit', compact('tier', 'saleMethods', 'connectedMethods'));
    }

    /**
     * تحديث بيانات شريحة سعرية
     * 
     * PUT /dashboard/pricing/tiers/{tier}
     * 
     * @param Request $request
     * @param ProfitMarginTier $tier
     * @return RedirectResponse
     */
    public function update(Request $request, ProfitMarginTier $tier): RedirectResponse
    {
        // التحقق من صحة البيانات المدخلة
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'min_value' => 'required|numeric|min:0',
            'max_value' => 'nullable|numeric|gte:min_value',
            'priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'sale_methods' => 'array|min:1',
            'sale_methods.*.id' => 'required|exists:sale_methods,id',
            'sale_methods.*.profit_value' => 'required|numeric|min:0',
        ]);

        // تحديث بيانات الشريحة
        $tier->update([
            'name' => $validated['name'],
            'min_value' => $validated['min_value'],
            'max_value' => $validated['max_value'],
            'priority' => $validated['priority'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // تحديث علاقات طرق البيع
        if (!empty($validated['sale_methods'])) {
            $syncData = [];
            foreach ($validated['sale_methods'] as $method) {
                $syncData[$method['id']] = [
                    'profit_value' => $method['profit_value'],
                ];
            }
            $tier->saleMethods()->sync($syncData);
        }

        return redirect()
            ->route('pricing.tiers.index')
            ->with('success', 'تم تحديث الشريحة السعرية بنجاح');
    }

    /**
     * حذف شريحة سعرية
     * 
     * DELETE /dashboard/pricing/tiers/{tier}
     * 
     * @param ProfitMarginTier $tier
     * @return RedirectResponse
     */
    public function destroy(ProfitMarginTier $tier): RedirectResponse
    {
        $tier->delete();

        return redirect()
            ->route('pricing.tiers.index')
            ->with('success', 'تم حذف الشريحة السعرية بنجاح');
    }
}
