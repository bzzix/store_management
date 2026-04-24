<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\PurchaseInvoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupplierService
{
    /**
     * Add invoice amount to supplier totals
     */
    public function addInvoiceAmount(Supplier $supplier, float $amount): void
    {
        $supplier->recalculateBalance();
    }

    /**
     * Subtract invoice amount from supplier totals
     */
    public function subtractInvoiceAmount(Supplier $supplier, float $amount): void
    {
        $supplier->recalculateBalance();
    }

    /**
     * Add payment amount to supplier totals
     */
    public function addPayment(Supplier $supplier, float $amount): void
    {
        $supplier->recalculateBalance();
    }

    /**
     * Subtract payment amount from supplier totals
     */
    public function subtractPayment(Supplier $supplier, float $amount): void
    {
        $supplier->recalculateBalance();
    }

    /**
     * Get statement data for a supplier
     */
    public function getStatementData(Supplier $supplier, $fromDate, $toDate)
    {
        $fromDate = Carbon::parse($fromDate)->startOfDay();
        $toDate = Carbon::parse($toDate)->endOfDay();

        // 1. Calculate Previous Balance
        // For Suppliers: Previous Balance = Opening Balance + (Purchases before fromDate) - (Payments before fromDate)
        // Opening balance should be stored such that positive means we owe them (debt).
        $invoicesBefore = $supplier->purchaseInvoices()
            ->where('invoice_date', '<', $fromDate)
            ->sum('total_amount');

        $paymentsBefore = $supplier->payments()
            ->where('payment_date', '<', $fromDate)
            ->sum('amount');

        $previousBalance = (float)$supplier->opening_balance + $invoicesBefore - $paymentsBefore;

        // 1. Fetch Invoices with their linked payments for consolidation
        $invoicesRaw = $supplier->purchaseInvoices()
            ->with(['payments'])
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
                'type' => 'purchase',
                'description' => __('Purchase Invoice'),
                'date' => $invoice->invoice_date->format('Y-m-d 00:00:01'),
                'value' => $invoice->total_amount,
                'addition' => $invoice->total_amount, // Adds to what we owe
                'deduction' => $initialDeduction, // Payments deduct from what we owe
                'url' => route('dashboard.suppliers.purchases.print', $invoice),
                'created_at' => $invoice->created_at,
            ];
        });

        // 2. Fetch Independent Payments (not already consolidated in invoices)
        $paymentsRaw = $supplier->payments()
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
                    ? ($payment->voucher_type === 'receipt' ? __('Receipt Voucher') : __('Disbursement Voucher')) 
                    : __('Payment'),
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
