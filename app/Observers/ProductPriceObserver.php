<?php

namespace App\Observers;

use App\Models\ProductPrice;

class ProductPriceObserver
{
    /**
     * Handle the ProductPrice "creating" event.
     */
    public function creating(ProductPrice $price): void
    {
        // Set effective_from to now if not provided
        if (is_null($price->effective_from)) {
            $price->effective_from = now();
        }

        // If this is marked as current, unset previous current prices
        if ($price->is_current) {
            ProductPrice::where('product_id', $price->product_id)
                ->where('is_current', true)
                ->update([
                    'is_current' => false,
                    'effective_to' => now(),
                ]);
        }
    }

    /**
     * Handle the ProductPrice "created" event.
     */
    public function created(ProductPrice $price): void
    {
        // If this is the current price, update product's cached prices
        if ($price->is_current) {
            $price->product->update([
                'current_cost_price' => $price->cost_price,
                'current_base_price' => $price->base_price,
            ]);
        }

        \Log::info('Product price created', [
            'product_id' => $price->product_id,
            'cost_price' => $price->cost_price,
            'base_price' => $price->base_price,
            'is_current' => $price->is_current,
        ]);
    }
}
