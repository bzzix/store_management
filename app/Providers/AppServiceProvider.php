<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
        \App\Models\Category::observe(\App\Observers\CategoryObserver::class);
        \App\Models\Supplier::observe(\App\Observers\SupplierObserver::class);
        \App\Models\SaleInvoice::observe(\App\Observers\SaleInvoiceObserver::class);
        \App\Models\SaleInvoiceItem::observe(\App\Observers\SaleInvoiceItemObserver::class);
        \App\Models\PurchaseInvoice::observe(\App\Observers\PurchaseInvoiceObserver::class);
        \App\Models\Customer::observe(\App\Observers\CustomerObserver::class);
    }
}
