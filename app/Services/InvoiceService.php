<?php

namespace App\Services;

use App\Models\PurchaseInvoice;
use App\Models\SaleInvoice;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

/**
 * InvoiceService
 * 
 * خدمة إدارة الفواتير:
 * - إنشاء فواتير الشراء والبيع
 * - إتمام وإلغاء الفواتير
 * - حساب الأرباح
 */
class InvoiceService
{
    protected $inventoryService;
    protected $pricingService;

    public function __construct(InventoryService $inventoryService, PricingService $pricingService)
    {
        $this->inventoryService = $inventoryService;
        $this->pricingService = $pricingService;
    }

    public function createPurchaseInvoice(array $data, array $items): PurchaseInvoice
    {
        return DB::transaction(function () use ($data, $items) {
            // 1. Create the invoice
            $invoice = PurchaseInvoice::create([
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'user_id' => auth()->id(),
                'invoice_date' => $data['invoice_date'] ?? now(),
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => $data['subtotal'] ?? 0,
                'tax_percentage' => $data['tax_percentage'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'shipping_cost' => $data['shipping_cost'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0,
                'paid_amount' => 0, // Will be updated by recordPayment
                'previous_balance' => $data['previous_balance'] ?? 0,
                'status' => $data['status'] ?? 'completed',
                'notes' => $data['notes'] ?? null,
                'image' => $data['image'] ?? null,
            ]);

            // 2. Add items
            foreach ($items as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'product_unit_id' => $item['unit_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_amount' => (float)($item['tax_amount'] ?? 0),
                    'discount_amount' => (float)($item['discount_amount'] ?? 0),
                    'total' => (float)$item['total'],
                ]);

                // Update product cost if needed (handled by PurchaseInvoiceObserver if enabled, 
                // but we will do it explicitly or via Observer. Since existing logic was in Livewire, we keep it robust.)
            }

            // 3. Process Inventory if completed
            if ($invoice->status === 'completed') {
                $this->processInventory($invoice);
            }

            // 4. Record payment if any
            if (isset($data['paid_amount']) && (float)$data['paid_amount'] > 0) {
                $this->recordPurchasePayment(
                    $invoice, 
                    (float)$data['paid_amount'], 
                    $data['payment_method'] ?? 'cash',
                    $data['payment_notes'] ?? null
                );
            }

            return $invoice->fresh(['items.product', 'supplier', 'warehouse']);
        });
    }

    /**
     * تحديث فاتورة شراء
     */
    public function updatePurchaseInvoice(PurchaseInvoice $invoice, array $data, array $items): PurchaseInvoice
    {
        return DB::transaction(function () use ($invoice, $data, $items) {
            // In a real scenario, updating a completed invoice should reverse stock first.
            // For now, mirroring Sales logic: delete and re-add if items changed.
            
            // Logic to reverse stock if it was already completed
            if ($invoice->status === 'completed') {
                $this->reverseInventory($invoice);
            }

            $invoice->update([
                'supplier_id' => $data['supplier_id'],
                'warehouse_id' => $data['warehouse_id'],
                'invoice_date' => $data['invoice_date'] ?? $invoice->invoice_date,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
                'subtotal' => $data['subtotal'] ?? $invoice->subtotal,
                'tax_percentage' => $data['tax_percentage'] ?? $invoice->tax_percentage,
                'tax_amount' => $data['tax_amount'] ?? $invoice->tax_amount,
                'discount_amount' => $data['discount_amount'] ?? $invoice->discount_amount,
                'shipping_cost' => $data['shipping_cost'] ?? $invoice->shipping_cost,
                'total_amount' => $data['total_amount'] ?? $invoice->total_amount,
                'notes' => $data['notes'] ?? $invoice->notes,
                'image' => $data['image'] ?? $invoice->image,
            ]);

            // Refresh items
            $invoice->items()->delete();
            foreach ($items as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'product_unit_id' => $item['unit_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_amount' => (float)($item['tax_amount'] ?? 0),
                    'discount_amount' => (float)($item['discount_amount'] ?? 0),
                    'total' => (float)$item['total'],
                ]);
            }

            if ($invoice->status === 'completed') {
                $this->processInventory($invoice);
            }

            return $invoice->fresh(['items.product', 'supplier', 'warehouse']);
        });
    }

    /**
     * تسجيل عملية دفع لفاتورة شراء
     */
    public function recordPurchasePayment(PurchaseInvoice $invoice, float $amount, string $method = 'cash', ?string $notes = null)
    {
        // Map the frontend method to the database enum
        $dbMethod = match ($method) {
            'cash' => 'cash',
            'bank_transfer', 'bank' => 'bank_transfer',
            'check' => 'check',
            'credit_card', 'card' => 'credit_card',
            default => 'other',
        };

        return $invoice->payments()->create([
            'user_id' => auth()->id(),
            'payer_type' => \App\Models\Supplier::class,
            'payer_id' => $invoice->supplier_id,
            'amount' => $amount,
            'payment_method' => $dbMethod,
            'payment_date' => $invoice->invoice_date,
            'status' => 'completed',
            'notes' => $notes,
        ]);
    }

    /**
     * معالجة المخزون للفاتورة
     */
    protected function processInventory(PurchaseInvoice $invoice)
    {
        foreach ($invoice->items as $item) {
            $this->inventoryService->addStock(
                $item->product,
                $invoice->warehouse,
                $item->quantity,
                [
                    'reference_type' => 'purchase',
                    'reference_id' => $invoice->id,
                    'notes' => 'فاتورة مشتريات رقم: ' . $invoice->invoice_number
                ]
            );

            // Update product prices if price changed
            $product = $item->product;
            if ($product && (round((float)$product->current_cost_price, 2) !== round((float)$item->unit_price, 2))) {
                \App\Models\ProductPrice::create([
                    'product_id' => $product->id,
                    'purchase_invoice_id' => $invoice->id,
                    'user_id' => $invoice->user_id,
                    'cost_price' => $item->unit_price,
                    'base_price' => $item->unit_price,
                    'effective_from' => $invoice->invoice_date,
                    'is_current' => true,
                    'change_reason' => 'purchase',
                ]);

                $product->update([
                    'current_cost_price' => $item->unit_price,
                    'current_base_price' => $item->unit_price,
                ]);
            }
        }
    }

    /**
     * عكس المخزون للفاتورة
     */
    protected function reverseInventory(PurchaseInvoice $invoice)
    {
        foreach ($invoice->items as $item) {
            $this->inventoryService->removeStock(
                $item->product,
                $invoice->warehouse,
                $item->quantity,
                [
                    'reference_type' => 'purchase_reversal',
                    'reference_id' => $invoice->id,
                    'notes' => 'تعديل/عكس فاتورة مشتريات: ' . $invoice->invoice_number
                ]
            );
        }
    }

    /**
     * إنشاء فاتورة بيع
     * 
     * @param array $data
     * @return SaleInvoice
     */
    public function createSaleInvoice(array $data): SaleInvoice
    {
        return DB::transaction(function () use ($data) {
            // إنشاء الفاتورة
            $invoice = SaleInvoice::create([
                'customer_id' => $data['customer_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'sale_method_id' => $data['sale_method_id'],
                'user_id' => auth()->id(),
                'invoice_number' => $this->generateInvoiceNumber('sale'),
                'invoice_date' => $data['invoice_date'] ?? now(),
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => 0,
                'tax_rate' => $data['tax_rate'] ?? 0,
                'tax_amount' => 0,
                'discount' => $data['discount'] ?? 0,
                'total' => 0,
                'total_cost' => 0,
                'total_profit' => 0,
                'paid_amount' => 0,
                'payment_status' => 'pending',
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
            ]);

            // إضافة الأصناف
            $subtotal = 0;
            $totalCost = 0;

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                $salePrice = $item['sale_price'];
                $costPrice = $product->current_cost_price ?? 0;
                $itemTotal = $salePrice * $item['quantity'];
                $itemCost = $costPrice * $item['quantity'];
                $itemProfit = $itemTotal - $itemCost;

                $invoice->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'cost_price' => $costPrice,
                    'sale_price' => $salePrice,
                    'total' => $itemTotal,
                    'profit' => $itemProfit,
                ]);

                $subtotal += $itemTotal;
                $totalCost += $itemCost;
            }

            // حساب الإجمالي
            $taxAmount = ($subtotal * $invoice->tax_rate) / 100;
            $total = $subtotal + $taxAmount - $invoice->discount;
            $totalProfit = $total - $totalCost;

            $invoice->update([
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'total_cost' => $totalCost,
                'total_profit' => $totalProfit,
            ]);

            return $invoice->fresh(['items.product', 'customer', 'warehouse']);
        });
    }

    /**
     * إتمام فاتورة شراء
     * 
     * @param PurchaseInvoice $invoice
     * @return PurchaseInvoice
     */
    public function completePurchaseInvoice(PurchaseInvoice $invoice): PurchaseInvoice
    {
        if ($invoice->status === 'completed') {
            throw new \Exception("Invoice already completed.");
        }

        return DB::transaction(function () use ($invoice) {
            $invoice->update(['status' => 'completed']);

            // PurchaseInvoiceObserver سيقوم بتحديث المخزون والأسعار تلقائياً

            return $invoice->fresh();
        });
    }

    /**
     * إتمام فاتورة بيع
     * 
     * @param SaleInvoice $invoice
     * @return SaleInvoice
     */
    public function completeSaleInvoice(SaleInvoice $invoice): SaleInvoice
    {
        if ($invoice->status === 'completed') {
            throw new \Exception("Invoice already completed.");
        }

        return DB::transaction(function () use ($invoice) {
            $invoice->update(['status' => 'completed']);

            // SaleInvoiceObserver سيقوم بخصم المخزون تلقائياً

            return $invoice->fresh();
        });
    }

    /**
     * حذف فاتورة شراء
     */
    public function deletePurchaseInvoice(PurchaseInvoice $invoice): bool
    {
        return DB::transaction(function () use ($invoice) {
            // Reverting stock and balances will be handled by the Observer
            return $invoice->delete();
        });
    }

    /**
     * إلغاء فاتورة
     * 
     * @param PurchaseInvoice|SaleInvoice $invoice
     * @return PurchaseInvoice|SaleInvoice
     */
    public function cancelInvoice($invoice)
    {
        if ($invoice->status === 'cancelled') {
            throw new \Exception("Invoice already cancelled.");
        }

        if ($invoice->status === 'completed') {
            throw new \Exception("Cannot cancel completed invoice. Please create a return invoice instead.");
        }

        return DB::transaction(function () use ($invoice) {
            $invoice->update(['status' => 'cancelled']);
            return $invoice->fresh();
        });
    }

    /**
     * توليد رقم فاتورة تلقائي
     * 
     * @param string $type 'purchase' or 'sale'
     * @return string
     */
    protected function generateInvoiceNumber(string $type): string
    {
        $prefix = $type === 'purchase' ? 'PUR' : 'SAL';
        $date = now()->format('Ymd');
        
        if ($type === 'purchase') {
            $lastInvoice = PurchaseInvoice::whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
        } else {
            $lastInvoice = SaleInvoice::whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
        }

        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return $prefix . '-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
