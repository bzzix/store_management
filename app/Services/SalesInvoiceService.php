<?php

namespace App\Services;

use App\Models\SaleInvoice;
use App\Models\SaleInvoiceItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesInvoiceService
{
    /**
     * Create a new sales invoice
     */
    public function create(array $data, array $items): SaleInvoice
    {
        return DB::transaction(function () use ($data, $items) {
            // 1. Create the invoice
            $invoice = SaleInvoice::create([
                'customer_id' => $data['customer_id'],
                'warehouse_id' => $data['warehouse_id'],
                'user_id' => Auth::id(),
                'sale_method_id' => $data['sale_method_id'] ?? null,
                'invoice_date' => $data['invoice_date'] ?? now(),
                'due_date' => $data['due_date'] ?? null,
                'subtotal' => $data['subtotal'] ?? 0,
                'tax_percentage' => $data['tax_percentage'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'shipping_cost' => $data['shipping_cost'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0,
                'paid_amount' => 0, // Initial balance, will be updated by payments
                'status' => $data['status'] ?? 'completed',
                'previous_balance' => $data['previous_balance'] ?? 0,
                'car_number' => $data['car_number'] ?? null,
                'driver_name' => $data['driver_name'] ?? null,
                'notes' => $data['notes'] ?? null,
                'internal_notes' => $data['internal_notes'] ?? null,
            ]);

            // 2. Add items
            foreach ($items as $itemData) {
                $isCustom = empty($itemData['product_id']);
                
                if (!$isCustom) {
                    $product = Product::findOrFail($itemData['product_id']);
                    $costPrice = (float)($itemData['cost_price'] ?? $product->current_cost_price);
                } else {
                    // Custom Item Profit = 2.5% of total
                    $costPrice = (float)$itemData['unit_price'] * 0.975;
                }
                
                $invoice->items()->create([
                    'product_id' => $itemData['product_id'] ?? null,
                    'product_unit_id' => $itemData['product_unit_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'cost_price' => $costPrice,
                    'unit_price' => $itemData['unit_price'],
                    'tax_amount' => $itemData['tax_amount'] ?? 0,
                    'discount_amount' => $itemData['discount_amount'] ?? 0,
                    'total' => $itemData['total'],
                    'is_custom' => $isCustom,
                    'custom_name' => $isCustom ? ($itemData['product_name'] ?? $itemData['name'] ?? null) : null,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            // 3. Record payment if any
            $paidAmount = (float)($data['paid_amount'] ?? 0);
            if ($paidAmount > 0) {
                // Map the frontend method to the database enum
                $dbMethod = match ($data['payment_method'] ?? 'cash') {
                    'cash' => 'cash',
                    'bank_transfer', 'bank' => 'bank_transfer',
                    'check' => 'check',
                    'credit_card', 'card' => 'credit_card',
                    default => 'other',
                };

                $invoice->payments()->create([
                    'user_id' => Auth::id(),
                    'payer_type' => Customer::class,
                    'payer_id' => $invoice->customer_id,
                    'amount' => $paidAmount,
                    'payment_method' => $dbMethod,
                    'payment_date' => $invoice->invoice_date,
                    'status' => 'completed',
                    'notes' => $data['payment_notes'] ?? __('Initial payment'),
                ]);
            }

            // 4. Final totals calculation
            $invoice->calculateTotals();
            $invoice->updatePaymentStatus();

            return $invoice;
        });
    }

    /**
     * Update an existing sales invoice
     */
    public function update(SaleInvoice $invoice, array $data, array $items): SaleInvoice
    {
        return DB::transaction(function () use ($invoice, $data, $items) {
            $oldCustomerId = $invoice->customer_id;
            $oldRemaining = (float)$invoice->total_amount - (float)$invoice->paid_amount;
            $oldItems = $invoice->items; // Keep reference if needed

            // 1. Clear old items FIRST (This will trigger stock replenishment via Observer using OLD warehouse_id)
            $invoice->items()->delete();

            // 2. Update invoice core data (including potential NEW warehouse_id)
            $invoice->update([
                'customer_id' => $data['customer_id'],
                'warehouse_id' => $data['warehouse_id'],
                'sale_method_id' => $data['sale_method_id'] ?? $invoice->sale_method_id,
                'invoice_date' => $data['invoice_date'] ?? $invoice->invoice_date,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
                'subtotal' => $data['subtotal'] ?? $invoice->subtotal,
                'tax_percentage' => $data['tax_percentage'] ?? $invoice->tax_percentage,
                'tax_amount' => $data['tax_amount'] ?? $invoice->tax_amount,
                'discount_amount' => $data['discount_amount'] ?? $invoice->discount_amount,
                'shipping_cost' => $data['shipping_cost'] ?? $invoice->shipping_cost,
                'total_amount' => $data['total_amount'] ?? $invoice->total_amount,
                'car_number' => $data['car_number'] ?? $invoice->car_number,
                'driver_name' => $data['driver_name'] ?? $invoice->driver_name,
                'notes' => $data['notes'] ?? $invoice->notes,
            ]);

            // 3. Add new items (This will trigger stock deduction via Observer using NEW warehouse_id)
            foreach ($items as $itemData) {
                $isCustom = empty($itemData['product_id']);
                
                if (!$isCustom) {
                    $product = Product::findOrFail($itemData['product_id']);
                    $costPrice = (float)($itemData['cost_price'] ?? $product->current_cost_price);
                } else {
                    // Custom Item Profit = 2.5% of total
                    $costPrice = (float)$itemData['unit_price'] * 0.975;
                }

                $invoice->items()->create([
                    'product_id' => $itemData['product_id'] ?? null,
                    'product_unit_id' => $itemData['product_unit_id'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'cost_price' => $costPrice,
                    'unit_price' => $itemData['unit_price'],
                    'tax_amount' => $itemData['tax_amount'] ?? 0,
                    'discount_amount' => $itemData['discount_amount'] ?? 0,
                    'total' => $itemData['total'],
                    'is_custom' => $isCustom,
                    'custom_name' => $isCustom ? ($itemData['product_name'] ?? $itemData['name'] ?? null) : null,
                ]);
            }

            // 4. Handle Balance Adjustment: Use total_amount difference only
            // Payments are handled separately via Payment system
            $newCustomerId = (int)$invoice->customer_id;
            $newTotal = (float)$invoice->total_amount;
            $oldTotal = (float)$invoice->getOriginal('total_amount');

            // Customer changed: Handle moving existing payments to the new customer
            if ($oldCustomerId != $newCustomerId) {
                foreach ($invoice->payments as $payment) {
                    $payment->update(['payer_id' => $newCustomerId]);
                }
            }
            
            $invoice->calculateTotals();
            $invoice->updatePaymentStatus();

            return $invoice->fresh();
        });
    }

    /**
     * Cancel an invoice
     */
    public function cancel(SaleInvoice $invoice): bool
    {
        return DB::transaction(function () use ($invoice) {
            if ($invoice->status === 'cancelled') {
                return true;
            }

            $invoice->update(['status' => 'cancelled']);
            
            // Reverting stock and balances will be handled by the Observer
            return true;
        });
    }

    /**
     * Record a payment for an invoice
     */
    public function recordPayment(SaleInvoice $invoice, float $amount, string $method, ?string $notes = null)
    {
        return DB::transaction(function () use ($invoice, $amount, $method, $notes) {
            // Map SaleMethod code to Payment method enum
            $paymentMethod = match($method) {
                'cash' => 'cash',
                'bank_transfer' => 'bank_transfer',
                'credit_card' => 'credit_card',
                default => 'cash', // Default to cash for others like installment/credit for simple recording
            };

            $invoice->payments()->create([
                'user_id' => Auth::id(),
                'payer_type' => Customer::class,
                'payer_id' => $invoice->customer_id,
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'payment_date' => now(),
                'notes' => $notes,
            ]);

            $invoice->updatePaymentStatus();

            return $invoice->fresh();
        });
    }

    /**
     * Delete an invoice
     */
    public function delete(SaleInvoice $invoice): bool
    {
        return DB::transaction(function () use ($invoice) {
            // Reverting stock and balances will be handled by the Observer
            return $invoice->delete();
        });
    }
}
