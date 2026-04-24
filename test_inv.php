<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$customer = App\Models\Customer::first();
$prev = $customer->current_balance;
$prevTot = $customer->total_invoices;
$prevPaid = $customer->total_paid;

$invoiceData = [
    'customer_id' => $customer->id,
    'warehouse_id' => 1,
    'sale_method_id' => 1,
    'invoice_date' => now(),
    'subtotal' => 100,
    'total_amount' => 100,
    'paid_amount' => 20,
    'payment_method' => 'cash'
];
$items = [
    [
        'product_id' => 1,
        'product_unit_id' => 1,
        'quantity' => 1,
        'unit_price' => 100,
        'cost_price' => 80,
        'tax_amount' => 0,
        'discount_amount' => 0,
        'total' => 100
    ]
];

// Mock auth
Auth::loginUsingId(1);

app(App\Services\SalesInvoiceService::class)->create($invoiceData, $items);
$customer->refresh();

echo "Before: bal=$prev tot=$prevTot paid=$prevPaid\n";
echo "After: bal={$customer->current_balance} tot={$customer->total_invoices} paid={$customer->total_paid}\n";
