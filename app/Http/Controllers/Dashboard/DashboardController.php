<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ProfitMarginTier;
use App\Models\SaleMethod;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as Pdf;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use RalphJSmit\Laravel\SEO\Support\SEOData;

/**
 * DashboardController
 * 
 * كونتروللر لوحة التحكم الرئيسي
 * يتعامل مع:
 * - عرض لوحة التحكم الرئيسية
 * - إدارة الشرائح السعرية
 * - معاينة التسعير
 */
class DashboardController extends Controller
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
     * ========================================
     * 📊 لوحة التحكم الرئيسية
     * ========================================
     */

    /**
     * عرض لوحة التحكم الرئيسية
     * 
     * GET /dashboard
     * 
     * @return View
     */
    public function dashboard_index(): View
    {
        $title = __('Dashboard');
        $pageType = 'dashboard_index';
        $SEOData = new SEOData(
            title: $title,
            description: get_setting('appName', 'أولاد عبد الستار'),
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        $today = \Carbon\Carbon::today();

        $totalSales = \App\Models\SaleInvoice::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalPurchases = \App\Models\PurchaseInvoice::where('status', '!=', 'cancelled')->sum('total_amount');
        
        $netProfit = \App\Models\SaleInvoice::where('status', 'completed')
                        ->selectRaw('SUM(subtotal - total_cost) as profit')
                        ->value('profit') ?? 0;
                        
        $lowStockCount = \App\Models\Product::lowStock()->count();

        $latestSales = \App\Models\SaleInvoice::with(['customer', 'user', 'saleMethod'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                            
        $todayPurchasesCount = \App\Models\PurchaseInvoice::whereDate('created_at', $today)->count();

        return view('dashboard.index', compact(
            'title',
            'SEOData',
            'pageType',
            'totalSales',
            'totalPurchases',
            'netProfit',
            'lowStockCount',
            'latestSales',
            'todayPurchasesCount'
        ));
    }

    /**
     * ========================================
     * 📍 إدارة الشرائح السعرية
     * ========================================
     */

    /**
     * عرض جميع الشرائح السعرية
     * 
     * GET /dashboard/pricing/tiers
     * 
     * @return View
     */
    public function pricingTiersIndex(): View
    {

        $pageType = 'pricing_tiers_index';
        $title = __('Pricing Tiers');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة الشرائح السعرية والأرباح',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.pricing.tiers', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * صفحة إنشاء شريحة سعرية جديدة
     * 
     * GET /dashboard/pricing/tiers/create
     * 
     * @return View
     */
    public function pricingTiersCreate(): View
    {
        // جلب طرق البيع المتاحة لربطها بالشريحة
        $saleMethods = SaleMethod::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $pageType = 'pricing_tiers_create';
        $title = __('Create Pricing Tier');
        $SEOData = new SEOData(
            title: $title,
            description: 'إنشاء شريحة سعرية جديدة',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.pricing.tiers.create', compact('saleMethods', 'title', 'pageType', 'SEOData'));
    }

    /**
     * حفظ شريحة سعرية جديدة
     * 
     * POST /dashboard/pricing/tiers
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function pricingTiersStore(Request $request): RedirectResponse
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
    public function pricingTiersEdit(ProfitMarginTier $tier): View
    {
        // جلب طرق البيع المتاحة
        $saleMethods = SaleMethod::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        // جلب طرق البيع المرتبطة بالشريحة الحالية
        $connectedMethods = $tier->saleMethods()
            ->pluck('profit_value', 'sale_method_id')
            ->toArray();

        $pageType = 'pricing_tiers_edit';
        $title = __('Edit Pricing Tier');
        $SEOData = new SEOData(
            title: $title,
            description: 'تعديل الشريحة السعرية: ' . $tier->name,
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.pricing.tiers.edit', compact('tier', 'saleMethods', 'connectedMethods', 'title', 'pageType', 'SEOData'));
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
    public function pricingTiersUpdate(Request $request, ProfitMarginTier $tier): RedirectResponse
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
    public function pricingTiersDestroy(ProfitMarginTier $tier): RedirectResponse
    {
        $tier->delete();

        return redirect()
            ->route('pricing.tiers.index')
            ->with('success', 'تم حذف الشريحة السعرية بنجاح');
    }

    /**
     * ========================================
     * 📊 معاينة التسعير
     * ========================================
     */

    /**
     * عرض صفحة معاينة التسعير
     * 
     * GET /dashboard/pricing/preview
     * 
     * @return View
     */
    public function pricingPreview(): View
    {
        // جلب طرق البيع المتاحة
        $saleMethods = $this->pricingService->availableSaleMethods();

        $pageType = 'pricing_preview';
        $title = __('Pricing Preview');
        $SEOData = new SEOData(
            title: $title,
            description: 'معاينة التسعير والسعر النهائي',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.pricing.preview', compact('saleMethods', 'title', 'pageType', 'SEOData'));
    }

    /**
     * حساب السعر النهائي (Ajax Request)
     * 
     * POST /dashboard/pricing/calculate
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function pricingCalculate(Request $request): JsonResponse
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
    public function pricingReport(): View
    {
        // جلب جميع الشرائح السعرية مع طرق البيع
        $tiers = ProfitMarginTier::with('saleMethods')
            ->where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        $pageType = 'pricing_report';
        $title = __('Pricing Report');
        $SEOData = new SEOData(
            title: $title,
            description: 'تقرير التسعير والشرائح السعرية الكاملة',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.pricing.report', compact('tiers', 'title', 'pageType', 'SEOData'));
    }

    /**
     * ========================================
     * 💳 إدارة طرق البيع
     * ========================================
     */

    /**
     * عرض جميع طرق البيع
     * 
     * GET /dashboard/pricing/sale-methods
     * 
     * @return View
     */
    public function saleMethods(): View
    {
        $saleMethods = SaleMethod::orderBy('priority', 'desc')->paginate(15);

        $pageType = 'sale_methods_index';
        $title = __('Sale Methods');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة طرق البيع والدفع',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.pricing.sale-methods', compact('saleMethods', 'title', 'pageType', 'SEOData'));
    }

    /**
     * ========================================
     * 📦 إدارة المخازن
     * ========================================
     */

    /**
     * عرض صفحة إدارة المخازن
     *
     * GET /dashboard/warehouses
     *
     * @return View
     */
    public function warehousesIndex(): View
    {
        $pageType = 'warehouses_index';
        $title = __('Warehouses');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة المخازن والمستودعات',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.warehouses.index', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * ========================================
     * ️ إدارة المنتجات
     * ========================================
     */

    /**
     * عرض صفحة قائمة المنتجات
     *
     * GET /dashboard/products
     *
     * @return View
     */
    public function productsIndex(): View
    {
        $pageType = 'products_index';
        $title = __('Products');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة المنتجات',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.products.index', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * عرض صفحة إدارة التصنيفات
     *
     * GET /dashboard/products/categories
     *
     * @return View
     */
    public function productsCategoriesIndex(): View
    {
        $pageType = 'products_categories';
        $title = __('Categories');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة التصنيفات',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.products.categories', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * عرض صفحة قائمة الموردين
     *
     * GET /dashboard/suppliers
     *
     * @return View
     */
    public function suppliersIndex(): View
    {
        $pageType = 'suppliers_index';
        $title = __('Suppliers');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة الموردين',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.suppliers.index', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * عرض صفحة قائمة العملاء
     *
     * GET /dashboard/customers
     *
     * @return View
     */
    public function customersIndex(): View
    {
        $pageType = 'customers_index';
        $title = __('Customers');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة العملاء والمديونيات',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.customers.index', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * عرض كشف حساب العميل
     *
     * GET /dashboard/customers/{customer}/statement
     *
     * @param \App\Models\Customer $customer
     * @return View
     */
    public function customerStatement(\App\Models\Customer $customer): View
    {
        $pageType = 'customers_statement';
        $title = __('Customer Statement') . ' - ' . $customer->display_name;
        $SEOData = new SEOData(
            title: $title,
            description: 'كشف حساب عميل تفصيلي',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.customers.statement', compact('customer', 'title', 'pageType', 'SEOData'));
    }

    /**
     * طباعة كشف حساب عميل (80mm)
     */
    public function customerStatementPrint(\App\Models\Customer $customer, \App\Services\CustomerService $customerService, \Illuminate\Http\Request $request)
    {
        $fromDate = $request->get('fromDate', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('toDate', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));

        $transactions = $customerService->getStatementData($customer, $fromDate, $toDate);

        return view('dashboard.customers.statement-print', [
            'customer' => $customer,
            'transactions' => $transactions,
            'items' => $transactions['items'],
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
    }

    /**
     * ========================================
     * 🧾 إدارة المبيعات
     * ========================================
     */

    /**
     * عرض صفحة المبيعات
     * 
     * GET /dashboard/sales
     * 
     * @return View
     */
    public function salesIndex(): View
    {
        $pageType = 'sales_index';
        $title = __('Sales');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة المبيعات وفواتير العملاء',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.sales.index', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * عرض وطباعة فاتورة مبيعات
     */
    public function saleInvoicePrint(\App\Models\SaleInvoice $invoice)
    {
        $invoice->load(['customer', 'warehouse', 'user', 'items.product.units', 'saleMethod']);
        
        $data = ['invoice' => $invoice];
        
        // We will use a special view for thermal 80mm printing
        return view('dashboard.sales.print', $data);
    }

    /**
     * عرض صفحة المشتريات
     *
     * GET /dashboard/suppliers/purchases
     *
     * @return View
     */
    public function purchaseInvoicesIndex(): View
    {
        $pageType = 'purchase_invoices_index';
        $title = __('Purchases');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة المشتريات وفواتير الموردين',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.suppliers.purchases.index', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * عرض كشف حساب المورد
     *
     * GET /dashboard/suppliers/{supplier}/statement
     *
     * @param \App\Models\Supplier $supplier
     * @return View
     */
    public function supplierStatement(\App\Models\Supplier $supplier): View
    {
        $pageType = 'suppliers_statement';
        $title = __('Supplier Statement') . ' - ' . $supplier->name;
        $SEOData = new SEOData(
            title: $title,
            description: 'كشف حساب مورد تفصيلي',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.suppliers.statement', compact('supplier', 'title', 'pageType', 'SEOData'));
    }

    /**
     * طباعة كشف حساب مورد
     */
    public function supplierStatementPrint(\App\Models\Supplier $supplier, \App\Services\SupplierService $supplierService, \Illuminate\Http\Request $request)
    {
        $fromDate = $request->get('fromDate', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('toDate', \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d'));

        $transactions = $supplierService->getStatementData($supplier, $fromDate, $toDate);

        return view('dashboard.suppliers.statement-print', [
            'supplier' => $supplier,
            'transactions' => $transactions,
            'items' => $transactions['items'],
            'fromDate' => $fromDate,
            'toDate' => $toDate
        ]);
    }


    /**
     * تصدير فاتورة المشتريات كملف PDF
     *
     * GET /dashboard/suppliers/purchases/{invoice}/print
     *
     */
    public function purchaseInvoicePrint(PurchaseInvoice $invoice)
    {
        $invoice->load(['supplier', 'warehouse', 'user', 'items.product.units']);
        
        $data = ['invoice' => $invoice];
        
        return view('dashboard.suppliers.purchases.print', $data);
    }

    /**
     * ========================================
     * 📊 التقارير
     * ========================================
     */

    /**
     * عرض صفحة التقارير
     * 
     * GET /dashboard/reports
     * 
     * @return View
     */
    public function reports(): View
    {
        $pageType = 'reports_index';
        $title = __('Reports');
        $SEOData = new SEOData(
            title: $title,
            description: 'التقارير والإحصائيات',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.reports', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * ========================================
     * ⚙️ الإعدادات
     * ========================================
     */

    /**
     * عرض صفحة الإعدادات
     * 
     * GET /dashboard/settings
     * 
     * @return View
     */
    public function settings(): View
    {
        $pageType = 'settings_index';
        $title = __('Settings');
        $SEOData = new SEOData(
            title: $title,
            description: 'إعدادات التطبيق والنظام',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.settings', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * عرض مركز البيع والشراء الموحد (POS)
     * 
     * GET /dashboard/pos
     * 
     * @return View
     */
    public function posIndex(): View
    {
        $pageType = 'pos_center';
        $title = __('Universal POS Center');
        $SEOData = new SEOData(
            title: $title,
            description: 'مركز البيع والشراء الموحد لإدارة المعاملات السريعة',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.pos.index', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * طباعة فاتورة مبيعات (POS)
     */
    public function posPrintSale(\App\Models\SaleInvoice $invoice): View
    {
        $invoice->load(['items.product', 'customer', 'user']);
        return view('dashboard.sales.print', compact('invoice'));
    }

    /**
     * طباعة فاتورة مشتريات (POS)
     */
    public function posPrintPurchase(\App\Models\PurchaseInvoice $invoice): View
    {
        $invoice->load(['items.product', 'supplier', 'user']);
        return view('dashboard.pos.purchase-print', compact('invoice'));
    }

    /**
     * عرض صفحة سندات القبض والصرف
     */
    public function paymentsIndex(): View
    {
        $pageType = 'payments_index';
        $title = __('Payment & Receipt Vouchers');
        $SEOData = new SEOData(
            title: $title,
            description: 'إدارة سندات القبض والصرف للعملاء والموردين',
            author: get_setting('appName', 'أولاد عبد الستار'),
            site_name: get_setting('appName', 'أولاد عبد الستار'),
            image: get_setting('appLogo'),
        );

        return view('dashboard.payments.index', compact('title', 'pageType', 'SEOData'));
    }

    /**
     * طباعة سند قبض أو صرف (80mm)
     */
    public function paymentVoucherPrint(\App\Models\Payment $payment)
    {
        $payment->load(['payer', 'user']);
        return view('dashboard.payments.voucher-print', compact('payment'));
    }
}
