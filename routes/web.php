<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'can:admin_view',
])->prefix('dashboard')->name('dashboard.')->controller(DashboardController::class)->group(function () {

    // 📊 لوحة التحكم الرئيسية
    Route::get('/', 'dashboard_index')->name('index');

    // 💰 راوابط إدارة التسعير والشرائح السعرية
    Route::prefix('pricing')->name('pricing.')->group(function () {
        Route::get('/tiers', 'pricingTiersIndex')->name('tiers.index')->middleware('can:pricing_tiers_view');
        Route::get('/sale-methods', 'saleMethods')->name('sale-methods.index')->middleware('can:sale_methods_view');
        Route::get('/preview', 'pricingPreview')->name('preview')->middleware('can:pricing_view');
        Route::get('/report', 'pricingReport')->name('report')->middleware('can:pricing_view');
    });

    // 📦 راوابط إدارة المخازن
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/', 'warehousesIndex')->name('index')->middleware('can:warehouses_view');
    });

    // 🛍️ راوابط إدارة المنتجات
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', 'productsIndex')->name('index')->middleware('can:products_view');
        Route::get('/categories', 'productsCategoriesIndex')->name('categories')->middleware('can:products_view');
    });

    // 🚚 راوابط إدارة الموردين
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/', 'suppliersIndex')->name('index')->middleware('can:suppliers_view');
        Route::get('/purchases', 'purchaseInvoicesIndex')->name('purchases.index')->middleware('can:suppliers_view');
        Route::get('/purchases/{invoice}/print', 'purchaseInvoicePrint')->name('purchases.print')->middleware('can:suppliers_view');
        Route::get('/{supplier}/statement', 'supplierStatement')->name('statement')->middleware('can:suppliers_view');
        Route::get('/{supplier}/statement/print', 'supplierStatementPrint')->name('statement.print')->middleware('can:suppliers_view');
    });

    // 👤 راوابط إدارة العملاء
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', 'customersIndex')->name('index')->middleware('can:customers_view');
        Route::get('/{customer}/statement', 'customerStatement')->name('statement')->middleware('can:customers_view');
        Route::get('/{customer}/statement/print', 'customerStatementPrint')->name('statement.print')->middleware('can:customers_view');
    });

    // 🧾 راوابط إدارة المبيعات
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/', 'salesIndex')->name('index')->middleware('can:sales_view');
        Route::get('/{invoice}/print', 'saleInvoicePrint')->name('print')->middleware('can:sales_view');
    });

    // 💰 سندات القبض والصرف
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('/', 'paymentsIndex')->name('index');
        Route::get('/{payment}/print', 'paymentVoucherPrint')->name('print');
    });

    // 📊 التقارير
    Route::get('/reports', 'reports')->name('reports')->middleware('can:admin_view');

    // ⚙️ الإعدادات
    Route::get('/settings', 'settings')->name('settings')->middleware('can:settings_view');

    // 🏪 مركز البيع والشراء الموحد (POS)
    Route::get('/pos', 'posIndex')->name('pos')->middleware('can:sales_view');
    Route::get('/pos/print/sale/{invoice}', 'posPrintSale')->name('pos.print.sale')->middleware('can:sales_view');
    Route::get('/pos/print/purchase/{invoice}', 'posPrintPurchase')->name('pos.print.purchase')->middleware('can:sales_view');

});