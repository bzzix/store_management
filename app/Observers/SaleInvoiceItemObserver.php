<?php

namespace App\Observers;

use App\Models\SaleInvoiceItem;
use App\Models\ProductWarehouse;

class SaleInvoiceItemObserver
{
    /**
     * Handle the SaleInvoiceItem "created" event.
     */
    public function created(SaleInvoiceItem $item): void
    {
        $invoice = $item->saleInvoice;
        if (!$invoice || $item->is_custom || !$item->product) return;

        // Adjust stock only if it's not a draft and we are tracking inventory
        if ($invoice->status !== 'draft' && $item->product->track_inventory) {
            $stock = ProductWarehouse::where('product_id', $item->product_id)
                ->where('warehouse_id', $invoice->warehouse_id)
                ->first();

            if ($stock) {
                // Use quantity in base unit for accurate stock tracking
                $stock->decrement('quantity', $item->quantity_in_base_unit);
            }
        }
    }

    /**
     * Handle the SaleInvoiceItem "updated" event.
     */
    public function updated(SaleInvoiceItem $item): void
    {
        $invoice = $item->saleInvoice;
        if (!$invoice || $item->is_custom || !$item->product) return;

        if ($invoice->status !== 'draft' && $item->product->track_inventory) {
            $stock = ProductWarehouse::where('product_id', $item->product_id)
                ->where('warehouse_id', $invoice->warehouse_id)
                ->first();

            if ($stock && $item->isDirty('quantity')) {
                $oldQuantity = (float) $item->getOriginal('quantity');
                $newQuantity = (float) $item->quantity;
                
                // Get conversion factor
                $factor = $item->productUnit ? (float) $item->productUnit->base_unit_multiplier : 1;
                $diff = ($newQuantity - $oldQuantity) * $factor;

                if ($diff != 0) {
                    $stock->decrement('quantity', $diff);
                }
            }
        }
    }

    /**
     * Handle the SaleInvoiceItem "deleted" event.
     */
    public function deleted(SaleInvoiceItem $item): void
    {
        $invoice = $item->saleInvoice;
        if (!$invoice || $item->is_custom || !$item->product) return;

        // Restore stock when item is deleted (e.g. during invoice update or cancellation)
        if ($invoice->status !== 'draft' && $item->product->track_inventory) {
            $stock = ProductWarehouse::where('product_id', $item->product_id)
                ->where('warehouse_id', $invoice->warehouse_id)
                ->first();

            if ($stock) {
                // Restore quantity in base unit
                $stock->increment('quantity', $item->quantity_in_base_unit);
            }
        }
    }
}
