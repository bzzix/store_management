<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaymentVoucherService
{
    /**
     * Create a standalone payment/receipt voucher
     */
    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create([
                'payment_number' => $data['payment_number'] ?? null,
                'payer_type' => $data['target_type'] === 'customer' ? Customer::class : Supplier::class,
                'payer_id' => $data['target_id'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'] ?? 'cash',
                'payment_date' => $data['payment_date'] ?? now(),
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'voucher_type' => $data['voucher_type'], // receipt or disbursement
                'user_id' => Auth::id(),
                'status' => 'completed',
            ]);

            return $payment;
        });
    }

    /**
     * Delete a voucher (will trigger PaymentObserver::deleted to revert balances)
     */
    public function delete(Payment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            return $payment->delete();
        });
    }
}
