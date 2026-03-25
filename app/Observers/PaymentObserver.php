<?php

namespace App\Observers;

use App\Models\Payment;

use App\Traits\GeneratesSequences;

class PaymentObserver
{
    use GeneratesSequences;

    /**
     * Handle the Payment "creating" event.
     */
    public function creating(Payment $payment): void
    {
        $this->setSequentialNumber($payment, 'PAY', 'payment_number');

        // Set default status
        if (empty($payment->status)) {
            $payment->status = 'completed';
        }
    }

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        // Update invoice paid amount
        if ($payment->paymentable) {
            $invoice = $payment->paymentable;
            $invoice->increment('paid_amount', $payment->amount);
            $invoice->updatePaymentStatus();
        }

        // Update payer balance (Customer or Supplier)
        if ($payment->payer) {
            $payment->payer->decrement('current_balance', $payment->amount);
        }

        \Log::info('Payment created', [
            'payment_id' => $payment->id,
            'payment_number' => $payment->payment_number,
            'amount' => $payment->amount,
            'paymentable_type' => $payment->paymentable_type,
            'paymentable_id' => $payment->paymentable_id,
        ]);
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        // If amount changed, update invoice and payer balance
        if ($payment->wasChanged('amount')) {
            $difference = $payment->amount - $payment->getOriginal('amount');
            
            if ($payment->paymentable) {
                $invoice = $payment->paymentable;
                $invoice->increment('paid_amount', $difference);
                $invoice->updatePaymentStatus();
            }

            if ($payment->payer) {
                $payment->payer->decrement('current_balance', $difference);
            }
        }

        // If status changed to cancelled, reverse the payment
        if ($payment->wasChanged('status') && $payment->status === 'cancelled') {
            $this->reversePayment($payment);
        }
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        // Reverse payment from invoice
        if ($payment->paymentable) {
            $invoice = $payment->paymentable;
            $invoice->decrement('paid_amount', $payment->amount);
            $invoice->updatePaymentStatus();
        }

        // Reverse payment from payer balance
        if ($payment->payer) {
            $payment->payer->increment('current_balance', $payment->amount);
        }

        \Log::warning('Payment deleted', [
            'payment_id' => $payment->id,
            'payment_number' => $payment->payment_number,
            'amount' => $payment->amount,
        ]);
    }

    /**
     * Reverse payment.
     */
    protected function reversePayment(Payment $payment): void
    {
        if ($payment->paymentable) {
            $invoice = $payment->paymentable;
            $invoice->decrement('paid_amount', $payment->amount);
            $invoice->updatePaymentStatus();
        }

        if ($payment->payer) {
            $payment->payer->increment('current_balance', $payment->amount);
        }

        \Log::warning('Payment cancelled', [
            'payment_id' => $payment->id,
            'payment_number' => $payment->payment_number,
        ]);
    }
}
