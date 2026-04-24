<?php

namespace App\Livewire\Dashboard\Pos;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\SaleInvoice;
use App\Models\PurchaseInvoice;
use App\Services\PricingService;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PosCenter extends Component
{
    use WithPagination;

    // Transaction Mode: 'sale' or 'purchase'
    public $mode = 'sale';
    
    // Cart Items
    public $items = [];
    
    // Selection IDs
    public $customer_id = null;
    public $supplier_id = null;
    public $warehouse_id = null;
    
    // Financials
    public $discount = 0;
    public $tax_percent = 0;
    public $paid_amount = 0;
    public $shipping_cost = 0;
    public $payment_method = 'cash';
    public $sale_method = 'cash'; // for pricing service
    public $previous_balance = 0;
    
    // Search & UI
    public $search = '';
    public $activeTab = 'pos'; // 'pos' or 'history'
    public $viewMode = 'list'; // 'grid' or 'list'
    
    // Logistics
    public $car_number = '';
    public $driver_name = '';
    public $notes = '';
    public $due_date = null;

    // History Filters
    public $historyFrom = null;
    public $historyTo = null;

    protected $queryString = ['activeTab', 'mode'];

    public function mount()
    {
        $this->warehouse_id = Warehouse::where('is_main', true)->first()?->id ?? Warehouse::first()?->id;
        $this->historyFrom = now()->format('Y-m-d');
        $this->historyTo = now()->format('Y-m-d');
    }

    public function updatedMode()
    {
        $this->items = [];
        $this->resetFinancials();
        $this->customer_id = null;
        $this->supplier_id = null;
    }

    public function resetFinancials()
    {
        $this->discount = 0;
        $this->tax_percent = 0;
        $this->paid_amount = 0;
        $this->shipping_cost = 0;
        $this->previous_balance = 0;
        $this->car_number = '';
        $this->driver_name = '';
        $this->notes = '';
    }

    public function updatedCustomerId($value)
    {
        if ($value) {
            $customer = Customer::find($value);
            if ($customer) {
                $customer->recalculateBalance();
                $this->previous_balance = (float)$customer->current_balance;
            } else {
                $this->previous_balance = 0;
            }
        } else {
            $this->previous_balance = 0;
        }
    }

    public function updatedSupplierId($value)
    {
        if ($value) {
            $supplier = Supplier::find($value);
            if ($supplier) {
                $supplier->recalculateBalance();
                $this->previous_balance = (float)$supplier->current_balance;
            } else {
                $this->previous_balance = 0;
            }
        } else {
            $this->previous_balance = 0;
        }
    }

    public function updatedSaleMethod()
    {
        foreach ($this->items as $index => $item) {
            $product = Product::find($item['id']);
            $pricing = app(PricingService::class)->calculate($product->current_base_price, $this->sale_method);
            $this->items[$index]['price'] = $pricing['final_price'];
            $this->items[$index]['total'] = $item['quantity'] * $pricing['final_price'];
        }
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $index = $this->findItemIndex($productId);

        if ($index !== false) {
            $this->items[$index]['quantity']++;
        } else {
            // Determine price based on mode
            $price = $this->mode === 'sale' 
                ? (new PricingService())->calculate($product->current_base_price, $this->sale_method)['final_price']
                : $product->current_cost_price ?? 0;

            $this->items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'barcode' => $product->barcode,
                'unit' => $product->base_unit,
                'quantity' => 1,
                'price' => $price,
                'total' => $price,
            ];
        }

        $this->calculateTotals();
        $this->dispatch('item-added');
    }

    public function removeFromCart($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($index);
            return;
        }

        $this->items[$index]['quantity'] = (float)$quantity;
        $this->items[$index]['total'] = $this->items[$index]['quantity'] * $this->items[$index]['price'];
        $this->calculateTotals();
    }

    public function updatePrice($index, $price)
    {
        $this->items[$index]['price'] = (float)$price;
        $this->items[$index]['total'] = $this->items[$index]['quantity'] * $this->items[$index]['price'];
        $this->calculateTotals();
    }

    protected function findItemIndex($productId)
    {
        foreach ($this->items as $index => $item) {
            if ($item['id'] == $productId) return $index;
        }
        return false;
    }

    public function calculateTotals()
    {
        // Totals are calculated dynamically in the view or here if needed for state
    }

    public function getSubtotalProperty()
    {
        return ceil(collect($this->items)->sum('total'));
    }

    public function getTotalProperty()
    {
        $tax = ($this->subtotal * $this->tax_percent) / 100;
        return ceil($this->subtotal + $tax + ($this->shipping_cost ?: 0) - ($this->discount ?: 0));
    }

    public function getTotalDueProperty()
    {
        return ceil($this->total + $this->previous_balance);
    }

    public function processTransaction()
    {
        if (empty($this->items)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => __('Cart is empty')]);
            return;
        }

        if ($this->mode === 'sale' && !$this->customer_id) {
            $this->dispatch('notify', ['type' => 'error', 'message' => __('Please select a customer')]);
            return;
        }

        if ($this->mode === 'purchase' && !$this->supplier_id) {
            $this->dispatch('notify', ['type' => 'error', 'message' => __('Please select a supplier')]);
            return;
        }

        try {
            $invoice = \DB::transaction(function () {
                if ($this->mode === 'sale') {
                    return $this->saveSale();
                } else {
                    return $this->savePurchase();
                }
            });

            $invoiceId = $invoice->id;
            $type = $this->mode;

            $this->items = [];
            $this->customer_id = null;
            $this->supplier_id = null;
            $this->resetFinancials();
            $this->dispatch('notify', ['type' => 'success', 'message' => __('Transaction completed successfully')]);
            
            // Trigger auto-print
            $this->dispatch('print-invoice', invoiceId: $invoiceId, type: $type);

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function printInvoice($invoiceId, $type)
    {
        $this->dispatch('print-invoice', invoiceId: $invoiceId, type: $type);
    }

    protected function saveSale()
    {
        $service = app(\App\Services\SalesInvoiceService::class);
        
        $customer = Customer::find($this->customer_id);
        $freshPreviousBalance = $customer ? (float)$customer->current_balance : 0;
        
        $invoiceData = [
            'customer_id' => $this->customer_id,
            'warehouse_id' => $this->warehouse_id,
            'sale_method_id' => \App\Models\SaleMethod::where('code', $this->sale_method)->first()?->id,
            'invoice_date' => now(),
            'due_date' => $this->due_date,
            'subtotal' => $this->subtotal,
            'tax_percentage' => $this->tax_percent,
            'tax_amount' => ($this->subtotal * $this->tax_percent) / 100,
            'discount_amount' => $this->discount,
            'shipping_cost' => $this->shipping_cost,
            'total_amount' => $this->total,
            'paid_amount' => $this->paid_amount,
            'previous_balance' => $freshPreviousBalance,
            'car_number' => $this->car_number,
            'driver_name' => $this->driver_name,
            'notes' => $this->notes,
            'status' => 'completed',
            'payment_method' => $this->sale_method,
            'payment_notes' => __('POS Transaction'),
        ];

        $itemsData = [];
        foreach ($this->items as $item) {
            $product = Product::find($item['id']);
            $defaultUnit = $product->units()->where('is_default', true)->first();
            
            $itemsData[] = [
                'product_id' => $item['id'],
                'product_unit_id' => $defaultUnit?->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total' => $item['total'],
            ];
        }

        return $service->create($invoiceData, $itemsData);
    }

    protected function savePurchase()
    {
        $service = app(\App\Services\InvoiceService::class);
        
        $supplier = Supplier::find($this->supplier_id);
        $freshPreviousBalance = $supplier ? (float)$supplier->current_balance : 0;

        $invoiceData = [
            'supplier_id' => $this->supplier_id,
            'warehouse_id' => $this->warehouse_id,
            'invoice_date' => now(),
            'due_date' => $this->due_date,
            'subtotal' => $this->subtotal,
            'tax_percentage' => $this->tax_percent,
            'tax_amount' => ($this->subtotal * $this->tax_percent) / 100,
            'discount_amount' => $this->discount,
            'total_amount' => $this->total,
            'paid_amount' => $this->paid_amount,
            'previous_balance' => $freshPreviousBalance,
            'status' => 'completed',
            'notes' => $this->notes,
            'payment_method' => 'cash',
            'payment_notes' => __('POS Transaction'),
        ];

        $itemsData = [];
        foreach ($this->items as $item) {
            $product = Product::find($item['id']);
            $defaultUnit = $product->units()->where('is_default', true)->first();

            $itemsData[] = [
                'product_id' => $item['id'],
                'unit_id' => $defaultUnit?->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total' => $item['total'],
            ];
        }

        return $service->createPurchaseInvoice($invoiceData, $itemsData);
    }

    public function getHistoryTransactionsProperty()
    {
        if ($this->activeTab !== 'history') return collect();

        $sales = SaleInvoice::with(['customer', 'user'])
            ->whereBetween('invoice_date', [$this->historyFrom, $this->historyTo])
            ->get()
            ->map(function($invoice) {
                $invoice->history_type = 'sale';
                $invoice->history_account = $invoice->customer?->name;
                return $invoice;
            });

        $purchases = PurchaseInvoice::with(['supplier', 'user'])
            ->whereBetween('invoice_date', [$this->historyFrom, $this->historyTo])
            ->get()
            ->map(function($invoice) {
                $invoice->history_type = 'purchase';
                $invoice->history_account = $invoice->supplier?->name;
                return $invoice;
            });

        return $sales->concat($purchases)->sortByDesc('created_at');
    }

    public function render()
    {
        $products = Product::active()
            ->search($this->search)
            ->with(['warehouseStock' => function($query) {
                $query->where('warehouse_id', $this->warehouse_id);
            }])
            ->paginate(12);

        $customers = Customer::active()->get();
        $suppliers = Supplier::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('livewire.dashboard.pos.pos-center', [
            'products' => $products,
            'customers' => $customers,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'historyTransactions' => $this->historyTransactions,
        ]);
    }
}
