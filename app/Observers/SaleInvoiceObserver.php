<?php

namespace App\Observers;

use App\Models\SaleInvoice;
use App\Models\ActivityLog;
use App\Models\ProductWarehouse;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

use App\Traits\GeneratesSequences;

class SaleInvoiceObserver
{
    use GeneratesSequences;

    public function creating(SaleInvoice $invoice): void
    {
        $this->setSequentialNumber($invoice, 'SAL', 'invoice_number');
    }

    public function created(SaleInvoice $invoice): void
    {
        // Update with actual ID for guaranteed uniqueness
        $this->updateNumberWithId($invoice, 'SAL', 'invoice_number');

        DB::transaction(function () use ($invoice) {
            // 1. Adjust customer balance: Increment by full invoice amount (debt creation) via Service
            app(\App\Services\CustomerService::class)->addInvoiceAmount($invoice->customer, (float)$invoice->total_amount);

            // 2. Log activity
            $this->logActivity($invoice, 'created', "تم إصدار فاتورة مبيعات جديدة رقم: {$invoice->invoice_number}");
        });
    }

    /**
     * Handle the SaleInvoice "updated" event.
     */
    public function updated(SaleInvoice $invoice): void
    {
        // 1. Handle cancellation
        if ($invoice->isDirty('status') && $invoice->status === 'cancelled' && $invoice->getOriginal('status') !== 'cancelled') {
            DB::transaction(function () use ($invoice) {
                // 1. Cancel associated payments (triggers PaymentObserver to revert balance)
                foreach ($invoice->payments as $payment) {
                    $payment->update(['status' => 'cancelled']);
                }

                // 2. Revert stock for all items by deleting them (triggers SaleInvoiceItemObserver)
                foreach ($invoice->items as $item) {
                    $item->delete();
                }

                // 3. Revert customer balance debt via Service
                app(\App\Services\CustomerService::class)->subtractInvoiceAmount($invoice->customer, (float)$invoice->total_amount);

                $this->logActivity($invoice, 'cancelled', "تم إلغاء فاتورة المبيعات رقم: {$invoice->invoice_number}");
            });
            return;
        }

        // 2. Handle customer_id or total_amount change (e.g. during edit)
        if ($invoice->isDirty(['customer_id', 'total_amount'])) {
            $oldCustomerId = (int)$invoice->getOriginal('customer_id');
            $newCustomerId = (int)$invoice->customer_id;
            $oldTotal = (float)$invoice->getOriginal('total_amount');
            $newTotal = (float)$invoice->total_amount;

            if ($oldCustomerId === $newCustomerId) {
                $diff = $newTotal - $oldTotal;
                if ($diff > 0) {
                    app(\App\Services\CustomerService::class)->addInvoiceAmount($invoice->customer, $diff);
                } elseif ($diff < 0) {
                    app(\App\Services\CustomerService::class)->subtractInvoiceAmount($invoice->customer, abs($diff));
                }
            } else {
                // Customer changed: Revert old total from old customer, apply new total to new customer
                $oldCustomer = Customer::find($oldCustomerId);
                if ($oldCustomer) {
                    app(\App\Services\CustomerService::class)->subtractInvoiceAmount($oldCustomer, $oldTotal);
                }
                app(\App\Services\CustomerService::class)->addInvoiceAmount($invoice->customer, $newTotal);
                
                // Note: If there were payments, they are usually linked to the invoice.
                // PaymentObserver handles balance per payment. If paymentable_id doesn't change, 
                // but payer_id DOES, we should handle that in PaymentObserver or here.
                // SalesInvoiceService currently handles moving payments.
            }
        }
    }

    /**
     * Handle the SaleInvoice "deleted" event.
     */
    public function deleted(SaleInvoice $invoice): void
    {
        // Load items with relationships BEFORE transaction/deletion to avoid lazy-loading issues
        $invoice->loadMissing(['items.product', 'items.productUnit', 'payments']);

        // If not already cancelled, revert changes
        if ($invoice->status !== 'cancelled') {
            DB::transaction(function () use ($invoice) {
                // 1. Delete associated payments FIRST (triggers PaymentObserver to revert balance for paid portion)
                foreach ($invoice->payments as $payment) {
                    $payment->delete();
                }

                // 2. Revert stock: Delete all items to trigger SaleInvoiceItemObserver logic
                foreach ($invoice->items as $item) {
                    $item->delete();
                }

                // 3. Revert customer balance debt via Service
                app(\App\Services\CustomerService::class)->subtractInvoiceAmount($invoice->customer, (float)$invoice->total_amount);
            });
        }

        $this->logActivity($invoice, 'deleted', "تم حذف فاتورة المبيعات رقم: {$invoice->invoice_number}");
    }

    /**
     * Helper to log activity
     */
    protected function logActivity($model, $type, $description, $properties = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => $type,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}
