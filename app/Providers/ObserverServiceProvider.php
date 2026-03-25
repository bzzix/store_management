<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\PurchaseInvoice;
use App\Models\SaleInvoice;
use App\Models\Payment;
use App\Observers\ProductObserver;
use App\Observers\ProductPriceObserver;
use App\Observers\PurchaseInvoiceObserver;
use App\Observers\SaleInvoiceObserver;
use App\Observers\PaymentObserver;
use Illuminate\Support\ServiceProvider;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register all observers
        Product::observe(ProductObserver::class);
        ProductPrice::observe(ProductPriceObserver::class);
        PurchaseInvoice::observe(PurchaseInvoiceObserver::class);
        SaleInvoice::observe(SaleInvoiceObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
