<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SaleInvoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerService
{
    /**
     * Create a new customer
     */
    public function create(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::create([
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'],
                'phone_2' => $data['phone_2'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'tax_number' => $data['tax_number'] ?? null,
                'customer_type' => $data['customer_type'] ?? 'individual',
                'company_name' => $data['customer_type'] === 'company' ? ($data['company_name'] ?? null) : null,
                'notes' => $data['notes'] ?? null,
                'opening_balance' => $data['opening_balance'] ?? 0,
                'total_invoices' => 0,
                'total_paid' => 0,
                'current_balance' => $data['opening_balance'] ?? 0,
                'credit_limit' => $data['credit_limit'] ?? 0,
                'is_active' => $data['is_active'] ?? true,
            ]);

            return $customer;
        });
    }

    /**
     * Update an existing customer
     */
    public function update(Customer $customer, array $data): Customer
    {
        return DB::transaction(function () use ($customer, $data) {
            // Adjust current balance if opening balance changed
            if (isset($data['opening_balance'])) {
                $customer->opening_balance = $data['opening_balance'];
                // Recalculate current_balance to ensure integrity
                $customer->current_balance = $customer->opening_balance + $customer->total_invoices - $customer->total_paid;
            }

            $customer->update([
                'name' => $data['name'] ?? $customer->name,
                'email' => $data['email'] ?? $customer->email,
                'phone' => $data['phone'] ?? $customer->phone,
                'phone_2' => $data['phone_2'] ?? $customer->phone_2,
                'address' => $data['address'] ?? $customer->address,
                'city' => $data['city'] ?? $customer->city,
                'tax_number' => $data['tax_number'] ?? $customer->tax_number,
                'customer_type' => $data['customer_type'] ?? $customer->customer_type,
                'company_name' => ($data['customer_type'] ?? $customer->customer_type) === 'company' 
                    ? ($data['company_name'] ?? $customer->company_name) 
                    : null,
                'notes' => $data['notes'] ?? $customer->notes,
                'opening_balance' => $data['opening_balance'] ?? $customer->opening_balance,
                'credit_limit' => $data['credit_limit'] ?? $customer->credit_limit,
                'is_active' => $data['is_active'] ?? $customer->is_active,
            ]);

            return $customer->fresh();
        });
    }

    /**
     * Delete a customer
     */
    public function delete(Customer $customer): bool
    {
        return DB::transaction(function () use ($customer) {
            // Check if customer has invoices before deleting (logic can be in Observer too)
            if ($customer->saleInvoices()->exists()) {
                throw new \Exception(__('Cannot delete customer with existing invoices.'));
            }
            return $customer->delete();
        });
    }

    /**
     * Toggle customer status
     */
    public function toggleStatus(Customer $customer): bool
    {
        return $customer->update([
            'is_active' => !$customer->is_active
        ]);
    }

    /**
     * Add invoice amount to customer totals
     */
    public function addInvoiceAmount(Customer $customer, float $amount): void
    {
        $customer->increment('total_invoices', $amount);
        $customer->increment('current_balance', $amount);
    }

    /**
     * Subtract invoice amount from customer totals
     */
    public function subtractInvoiceAmount(Customer $customer, float $amount): void
    {
        $customer->decrement('total_invoices', $amount);
        $customer->decrement('current_balance', $amount);
    }

    /**
     * Add payment amount to customer totals
     */
    public function addPayment(Customer $customer, float $amount): void
    {
        $customer->increment('total_paid', $amount);
        $customer->decrement('current_balance', $amount);
    }

    /**
     * Subtract payment amount from customer totals
     */
    public function subtractPayment(Customer $customer, float $amount): void
    {
        $customer->decrement('total_paid', $amount);
        $customer->increment('current_balance', $amount);
    }

    /**
     * Get statement data for a customer
     */
    public function getStatementData(Customer $customer, $fromDate, $toDate)
    {
        $fromDate = Carbon::parse($fromDate)->startOfDay();
        $toDate = Carbon::parse($toDate)->endOfDay();

        // 1. Calculate Previous Balance
        // Previous Balance = Opening Balance + (Invoices before fromDate) - (Payments before fromDate)
        $invoicesBefore = $customer->saleInvoices()
            ->where('invoice_date', '<', $fromDate)
            ->sum('total_amount');

        $paymentsBefore = $customer->payments()
            ->where('payment_date', '<', $fromDate)
            ->sum('amount');

        $previousBalance = $customer->opening_balance + $invoicesBefore - $paymentsBefore;

        // 1. Fetch Invoices with their linked payments for consolidation
        $invoicesRaw = $customer->saleInvoices()
            ->with('payments')
            ->whereBetween('invoice_date', [$fromDate, $toDate])
            ->get();

        $consolidatedPaymentIds = [];

        $invoiceItems = $invoicesRaw->map(function ($invoice) use (&$consolidatedPaymentIds) {
            // Find payments made on the SAME day as the invoice (initial payments)
            $initialPayments = $invoice->payments->filter(function($p) use ($invoice) {
                return $p->payment_date->format('Y-m-d') === $invoice->invoice_date->format('Y-m-d');
            });

            $initialDeduction = $initialPayments->sum('amount');
            
            // Mark these payments to be excluded from the separate payments list
            foreach($initialPayments as $p) {
                $consolidatedPaymentIds[] = $p->id;
            }

            return [
                'id' => $invoice->invoice_number,
                'number' => $invoice->invoice_number,
                'type' => 'invoice',
                'description' => __('Sale Invoice') . " #{$invoice->invoice_number}",
                'date' => $invoice->invoice_date->format('Y-m-d 00:00:01'), // Ensure invoice comes before payment on same day
                'value' => $invoice->total_amount,
                'addition' => $invoice->total_amount,
                'deduction' => $initialDeduction,
                'url' => route('dashboard.sales.print', $invoice),
                'created_at' => $invoice->created_at,
            ];
        });

        // 2. Fetch Independent Payments (not already consolidated in invoices)
        $paymentsRaw = $customer->payments()
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->whereNotIn('id', $consolidatedPaymentIds)
            ->get();

        $paymentItems = $paymentsRaw->map(function ($payment) {
            $isVoucher = $payment->paymentable_id === null;
            return [
                'id' => $payment->payment_number,
                'number' => $payment->payment_number,
                'type' => $isVoucher ? 'voucher' : 'payment',
                'voucher_type' => $payment->voucher_type,
                'description' => $isVoucher 
                    ? ($payment->voucher_type === 'receipt' ? __('Receipt Voucher') : __('Disbursement Voucher')) . " #{$payment->payment_number}"
                    : __('Payment for Invoice') . " #{$payment->payment_number}",
                'date' => $payment->payment_date->format('Y-m-d 23:59:59'),
                'value' => $payment->amount,
                'addition' => 0,
                'deduction' => $payment->amount,
                'url' => $isVoucher ? route('dashboard.payments.print', $payment) : '#',
                'created_at' => $payment->created_at,
            ];
        });

        // 3. Combine and Sort (Oldest first)
        $allTransactions = $invoiceItems->concat($paymentItems)
            ->sortBy([
                ['date', 'asc'],
                ['created_at', 'asc']
            ]);

        // 4. Calculate Running Balance
        $currentBalance = $previousBalance;
        $items = $allTransactions->map(function ($trans) use (&$currentBalance) {
            $before = $currentBalance;
            $currentBalance += ($trans['addition'] - $trans['deduction']);
            $trans['previous_balance'] = $before;
            $trans['balance'] = $currentBalance;
            return $trans;
        });

        return [
            'previous_balance' => $previousBalance,
            'items' => $items->values()->all(),
            'total_addition' => $items->sum('addition'),
            'total_deduction' => $items->sum('deduction'),
            'final_balance' => $currentBalance
        ];
    }
}
