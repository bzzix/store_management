<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SaleInvoice;
use App\Models\PurchaseInvoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RecalculateBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate total_invoices, total_paid and current_balance for all customers and suppliers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting recalculation for all customers and suppliers...');

        $this->recalculateCustomers();
        $this->recalculateSuppliers();

        $this->info('Recalculation completed successfully.');
    }

    protected function recalculateCustomers()
    {
        $customers = Customer::all();
        $this->info("Recalculating {$customers->count()} customers...");

        $bar = $this->output->createProgressBar($customers->count());

        foreach ($customers as $customer) {
            $totalSales = SaleInvoice::where('customer_id', $customer->id)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');

            // Handle receipts (standard) and disbursements (refunds)
            $receipts = Payment::where('payer_type', Customer::class)
                ->where('payer_id', $customer->id)
                ->where(function($q) {
                    $q->where('voucher_type', '!=', 'disbursement')
                      ->orWhereNull('voucher_type');
                })
                ->where('status', '!=', 'cancelled')
                ->sum('amount');
            
            $disbursements = Payment::where('payer_type', Customer::class)
                ->where('payer_id', $customer->id)
                ->where('voucher_type', 'disbursement')
                ->where('status', '!=', 'cancelled')
                ->sum('amount');

            $totalPaid = (float)$receipts - (float)$disbursements;

            $finalSpent = (float)$customer->opening_balance + (float)$totalSales;
            $finalPaid = $totalPaid;

            $customer->updateQuietly([
                'total_invoices' => (float)$totalSales,
                'total_paid' => $finalPaid,
                'current_balance' => ((float)$customer->opening_balance + (float)$totalSales) - $finalPaid,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    protected function recalculateSuppliers()
    {
        $suppliers = Supplier::all();
        $this->info("Recalculating {$suppliers->count()} suppliers...");

        $bar = $this->output->createProgressBar($suppliers->count());

        foreach ($suppliers as $supplier) {
            $totalPurchases = PurchaseInvoice::where('supplier_id', $supplier->id)
                ->where('status', '!=', 'cancelled')
                ->sum('total_amount');

            // Handle disbursements (standard) and receipts (refunds)
            $disbursements = Payment::where('payer_type', Supplier::class)
                ->where('payer_id', $supplier->id)
                ->where(function($q) {
                    $q->where('voucher_type', '!=', 'receipt')
                      ->orWhereNull('voucher_type');
                })
                ->where('status', '!=', 'cancelled')
                ->sum('amount');
            
            $receipts = Payment::where('payer_type', Supplier::class)
                ->where('payer_id', $supplier->id)
                ->where('voucher_type', 'receipt')
                ->where('status', '!=', 'cancelled')
                ->sum('amount');

            $totalPaid = (float)$disbursements - (float)$receipts;

            $finalSpent = (float)$supplier->opening_balance + (float)$totalPurchases;
            $finalPaid = $totalPaid;

            $supplier->updateQuietly([
                'total_invoices' => (float)$totalPurchases,
                'total_paid' => $finalPaid,
                'current_balance' => ((float)$supplier->opening_balance + (float)$totalPurchases) - $finalPaid,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }
}
