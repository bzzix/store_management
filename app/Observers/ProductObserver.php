<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        session()->flash('success', __('Product created successfully: ') . $product->name);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Only flash if not a simple stock update (optional logic)
        if ($product->isDirty('name') || $product->isDirty('is_active')) {
            session()->flash('success', __('Product updated successfully: ') . $product->name);
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        session()->flash('info', __('Product deleted successfully: ') . $product->name);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        session()->flash('success', __('Product restored successfully: ') . $product->name);
    }
}
