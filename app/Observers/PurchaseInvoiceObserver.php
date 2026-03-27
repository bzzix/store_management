<?php

namespace App\Observers;

use App\Models\PurchaseInvoice;
use App\Models\ProductPrice;
use App\Models\InventoryMovement;

use App\Traits\GeneratesSequences;

class PurchaseInvoiceObserver
{
    use GeneratesSequences;

    /**
     * Handle the PurchaseInvoice "creating" event.
     */
    public function creating(PurchaseInvoice $invoice): void
    {
        $this->setSequentialNumber($invoice, 'PUR', 'invoice_number');

        // Set default status
        if (empty($invoice->status)) {
            $invoice->status = 'draft';
        }

        // Set default payment status
        if (empty($invoice->payment_status)) {
            $invoice->payment_status = 'unpaid';
        }
    }

    /**
     * Handle the PurchaseInvoice "created" event.
     */
    public function created(PurchaseInvoice $invoice): void
    {
        $this->updateNumberWithId($invoice, 'PUR', 'invoice_number');
        
        /*
        if ($invoice->status === 'completed') {
            $this->processCompletedInvoice($invoice);
        }
        */

        if ($invoice->total_amount > 0) {
            app(\App\Services\SupplierService::class)->addInvoiceAmount($invoice->supplier, (float)$invoice->total_amount);
        }

        \Log::info('Purchase invoice created', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'supplier_id' => $invoice->supplier_id,
            'total_amount' => $invoice->total_amount,
        ]);
    }

    /**
     * Handle the PurchaseInvoice "updated" event.
     */
    public function updated(PurchaseInvoice $invoice): void
    {
        // If status changed to completed, update inventory and prices
        /*
        if ($invoice->wasChanged('status') && $invoice->status === 'completed') {
            $this->processCompletedInvoice($invoice);
        }
        */

        // Update supplier balance if total changed
        // skip if the invoice was just created in this request to avoid double-counting
        if (!$invoice->wasRecentlyCreated && $invoice->wasChanged('total_amount')) {
            $oldTotal = (float)$invoice->getOriginal('total_amount');
            $newTotal = (float)$invoice->total_amount;
            $diff = $newTotal - $oldTotal;

            if ($diff > 0) {
                app(\App\Services\SupplierService::class)->addInvoiceAmount($invoice->supplier, $diff);
            } elseif ($diff < 0) {
                app(\App\Services\SupplierService::class)->subtractInvoiceAmount($invoice->supplier, abs($diff));
            }
        }
    }

    /**
     * Process completed invoice.
     */
    protected function processCompletedInvoice(PurchaseInvoice $invoice): void
    {
        foreach ($invoice->items as $item) {
            $product = $item->product;

            // Update product prices
            $this->updateProductPrice($invoice, $item);

            // Update inventory
            $this->updateInventory($invoice, $item);
        }

        \Log::info('Purchase invoice completed', [
            'invoice_id' => $invoice->id,
            'items_count' => $invoice->items->count(),
        ]);
    }

    /**
     * Update product price from purchase.
     */
    protected function updateProductPrice(PurchaseInvoice $invoice, $item): void
    {
        $product = $item->product;

        // التحقق مما إذا كان السعر الجديد مختلفاً عن السعر الحالي (أو إذا لم يكن هناك سعر حالي)
        if ($product->current_cost_price == null || $product->current_cost_price != $item->unit_price) {
            // Create price history record
            ProductPrice::create([
                'product_id' => $product->id,
                'purchase_invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'cost_price' => $item->unit_price,
                'base_price' => $item->unit_price,
                'effective_from' => $invoice->invoice_date,
                'is_current' => true,
                'change_reason' => 'purchase',
            ]);

            // Update product's current prices
            $product->update([
                'current_cost_price' => $item->unit_price,
                'current_base_price' => $item->unit_price,
            ]);
        }
    }

    /**
     * Update inventory from purchase.
     */
    protected function updateInventory(PurchaseInvoice $invoice, $item): void
    {
        $product = $item->product;
        $warehouse = $invoice->warehouse;

        // Get quantity
        $quantity = $item->quantity;
        $quantityBefore = $product->stock_quantity ?? 0;

        // Update product stock
        $product->increment('stock_quantity', $quantity);

        // Create inventory movement record
        InventoryMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'user_id' => $invoice->user_id,
            'quantity' => $quantity,
            'reference_type' => 'purchase',
            'reference_id' => $invoice->id,
            'notes' => "فاتورة مشتريات رقم: {$invoice->invoice_number}",
        ]);
    }

    /**
     * Handle the PurchaseInvoice "deleted" event.
     */
    public function deleted(PurchaseInvoice $invoice): void
    {
        \DB::transaction(function () use ($invoice) {
            // 1. Reverse stock (Deduct what was purchased)
            $inventoryService = app(\App\Services\InventoryService::class);
            foreach ($invoice->items as $item) {
                try {
                    $inventoryService->removeStock(
                        $item->product,
                        $invoice->warehouse,
                        $item->quantity,
                        [
                            'reference_type' => 'purchase_reversal',
                            'reference_id' => $invoice->id,
                            'notes' => 'عكس فاتورة مشتريات (حذف): ' . $invoice->invoice_number
                        ]
                    );
                } catch (\Exception $e) {
                    \Log::error("Error reversing stock for purchase deletion: " . $e->getMessage());
                    // We continue for others even if one fails, or we could fail the whole transaction.
                    // Given it's a delete, we should try our best.
                }
            }

            // 2. Delete/Cancel associated payments (triggers PaymentObserver to revert balance)
            foreach ($invoice->payments as $payment) {
                $payment->delete();
            }

            // 3. Revert supplier balance: Decrement what we owed them via Service
            app(\App\Services\SupplierService::class)->subtractInvoiceAmount($invoice->supplier, (float)$invoice->total_amount);
        });

        \Log::warning('Purchase invoice deleted', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
        ]);
    }
}
