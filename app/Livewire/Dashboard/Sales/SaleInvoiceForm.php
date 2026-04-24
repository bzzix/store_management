<?php

namespace App\Livewire\Dashboard\Sales;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\SaleInvoice;
use App\Models\SaleMethod;
use App\Services\SalesInvoiceService;
use App\Services\PricingService;
use Illuminate\Support\Facades\DB;

class SaleInvoiceForm extends Component
{
    public $showModal = false;
    public $isEdit = false;
    public $isReadOnly = false;
    public $invoiceId;

    // Header Data
    public $customer_id;
    public $warehouse_id;
    public $sale_method_code = 'cash';
    public $invoice_date;
    public $due_date;
    public $notes;
    
    // Items
    public $items = []; // Each: product_id, product_name, unit_id, quantity, unit_price, total, available_units
    
    // Totals
    public $subtotal = 0;
    public $tax_percentage = 0;
    public $tax_amount = 0;
    public $discount_amount = 0;
    public $shipping_cost = 0;
    public $total_amount = 0;
    public $total_due = 0;
    public $paid_amount = 0;
    public $total_profit = 0;
    
    // UI Helpers
    public $searchProduct = '';
    public $searchResults = [];
    public $customer_balance = 0;
    public $should_print = true;
    public $car_number = '';
    public $driver_name = '';

    protected $listeners = [
        'create-invoice' => 'openCreateModal', 
        'edit-invoice' => 'openEditModal',
        'view-invoice' => 'viewInvoice'
    ];

    public function mount()
    {
        $this->invoice_date = now()->format('Y-m-d');
        $this->warehouse_id = Warehouse::first()?->id;
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->isReadOnly = false;
        $this->calculateTotals();
        $this->showModal = true;
    }

    public function openEditModal($invoiceId)
    {
        abort_if(!auth()->user()->can('sale_invoices_edit'), 403);

        $this->resetForm();
        $this->invoiceId = $invoiceId;
        $this->isEdit = true;
        $this->isReadOnly = false;
        
        $invoice = SaleInvoice::with(['items.product.units', 'customer', 'saleMethod'])->findOrFail($invoiceId);
        
        $this->customer_id = $invoice->customer_id;
        $this->warehouse_id = $invoice->warehouse_id;
        $this->sale_method_code = $invoice->saleMethod?->code ?? 'cash';
        $this->invoice_date = $invoice->invoice_date->format('Y-m-d');
        $this->due_date = $invoice->due_date ? $invoice->due_date->format('Y-m-d') : null;
        $this->notes = $invoice->notes;
        $this->car_number = $invoice->car_number;
        $this->driver_name = $invoice->driver_name;
        
        $this->subtotal = $invoice->subtotal;
        $this->tax_percentage = $invoice->tax_percentage;
        $this->tax_amount = $invoice->tax_amount;
        $this->discount_amount = $invoice->discount_amount;
        $this->shipping_cost = $invoice->shipping_cost;
        $this->total_amount = $invoice->total_amount;
        $this->paid_amount = $invoice->paid_amount;
        $this->customer_balance = $invoice->previous_balance;

        foreach ($invoice->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_unit_id' => $item->product_unit_id,
                'units' => $item->product->units->toArray(),
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'base_unit_price' => $item->product->current_base_price,
                'cost_price' => $item->cost_price,
                'profit' => $item->profit,
                'total' => $item->total,
            ];
        }

        $this->calculateTotals();
        $this->showModal = true;
    }

    public function viewInvoice($invoiceId)
    {
        $this->openEditModal($invoiceId);
        $this->isReadOnly = true;
    }

    public function resetForm()
    {
        $this->reset(['customer_id', 'customer_balance', 'notes', 'items', 'subtotal', 'tax_percentage', 'tax_amount', 'discount_amount', 'shipping_cost', 'total_amount', 'total_due', 'paid_amount', 'searchProduct', 'searchResults', 'car_number', 'driver_name']);
        $this->invoice_date = now()->format('Y-m-d');
        $this->warehouse_id = Warehouse::first()?->id;
        $this->sale_method_code = 'cash';
        $this->items = [];
        $this->should_print = true;
    }

    public function updatedCustomerId($value)
    {
        if ($value) {
            $customer = Customer::find($value);
            if ($customer) {
                $customer->recalculateBalance();
                $this->customer_balance = (float)$customer->current_balance;
            } else {
                $this->customer_balance = 0;
            }
        } else {
            $this->customer_balance = 0;
        }
        $this->calculateTotals();
    }

    public function updatedSaleMethodCode()
    {
        foreach ($this->items as $index => $item) {
            $pricing = app(PricingService::class)->safeCalculate($item['base_unit_price'], $this->sale_method_code);
            $this->items[$index]['unit_price'] = $pricing['final_price'];
            $this->items[$index]['profit'] = $pricing['profit'] * $this->items[$index]['quantity'];
            $this->calculateItemTotal($index);
        }
    }

    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::active()
            ->search($this->searchProduct)
            ->with(['units', 'warehouseStock'])
            ->limit(10)
            ->get();
    }

    public function addProduct($productId)
    {
        $product = Product::with(['units', 'warehouseStock'])->find($productId);
        if (!$product) return;

        // Check if already in items
        foreach ($this->items as $index => $item) {
            if ($item['product_id'] == $productId) {
                $this->items[$index]['quantity']++;
                $this->calculateItemTotal($index);
                $this->searchProduct = '';
                $this->searchResults = [];
                return;
            }
        }

        $defaultUnit = $product->units->where('is_default', true)->first();
        
        // Calculate initial price using PricingService
        $pricing = app(PricingService::class)->safeCalculate($product->current_base_price, $this->sale_method_code);
        $unitPrice = $pricing['final_price'];

        array_unshift($this->items, [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_unit_id' => $defaultUnit?->id,
            'units' => $product->units->toArray(),
            'quantity' => 1,
            'unit_price' => $unitPrice,
            'base_unit_price' => $product->current_base_price,
            'cost_price' => $product->current_cost_price,
            'profit' => ($unitPrice - $product->current_cost_price),
            'total' => $unitPrice,
            'is_custom' => false,
        ]);

        $this->calculateTotals();
        $this->searchProduct = '';
        $this->searchResults = [];
    }

    public function addCustomItem()
    {
        array_unshift($this->items, [
            'product_id' => null,
            'product_name' => '',
            'product_unit_id' => null,
            'units' => [],
            'quantity' => 1,
            'unit_price' => 0,
            'base_unit_price' => 0,
            'cost_price' => 0,
            'profit' => 0,
            'total' => 0,
            'is_custom' => true,
        ]);
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if ($field === 'total') {
                $quantity = (float)($this->items[$index]['quantity'] ?: 1);
                $this->items[$index]['unit_price'] = (float)$this->items[$index]['total'] / $quantity;
            } elseif ($field === 'unit_price' || $field === 'quantity') {
                $this->items[$index]['total'] = (float)$this->items[$index]['unit_price'] * (float)$this->items[$index]['quantity'];
            }

            // Recalculate profit
            if (!empty($this->items[$index]['is_custom'])) {
                $this->items[$index]['profit'] = (float)$this->items[$index]['total'] * 0.025;
            } else {
                $this->items[$index]['profit'] = ($this->items[$index]['unit_price'] - $this->items[$index]['cost_price']) * $this->items[$index]['quantity'];
            }

            $this->calculateTotals();
        }
    }

    public function calculateItemTotal($index)
    {
        $item = $this->items[$index];
        $this->items[$index]['total'] = $item['quantity'] * $item['unit_price'];
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = ceil(collect($this->items)->sum('total'));
        $this->total_profit = ceil(collect($this->items)->sum('profit'));
        $this->tax_amount = ceil(($this->subtotal * $this->tax_percentage) / 100);
        $this->total_amount = ceil($this->subtotal + $this->tax_amount + ($this->shipping_cost ?: 0) - ($this->discount_amount ?: 0));
        
        $this->total_due = ceil($this->total_amount + $this->customer_balance);

        // تم إزالة التعبئة التلقائية لتسهيل عملية الدفع الجزئي أو الصفري (الآجل بالكامل)
        /*
        if (!$this->isEdit && empty($this->paid_amount)) {
            $this->paid_amount = $this->total_amount; 
        }
        */
    }

    public function updatedPaidAmount() { $this->calculateTotals(); }
    public function updatedTaxPercentage() { $this->calculateTotals(); }
    public function updatedDiscountAmount() { $this->calculateTotals(); }
    public function updatedShippingCost() { $this->calculateTotals(); }

    public function save(SalesInvoiceService $service)
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_unit_id' => 'nullable|exists:product_units,id',
            'items.*.quantity' => 'required_with:items|numeric|min:0.001',
            'items.*.unit_price' => 'required_with:items|numeric|min:0',
        ], [
            'customer_id.required' => __('Please select a customer'),
            'items.required' => __('Please add at least one product'),
            'items.*.quantity.min' => __('Quantity must be greater than zero'),
        ]);

        // التحقق من توافر المخزون قبل الحفظ
        foreach ($this->items as $item) {
            $product = \App\Models\Product::find($item['product_id']);
            if ($product && $product->track_inventory) {
                $stock = \App\Models\ProductWarehouse::where('product_id', $item['product_id'])
                    ->where('warehouse_id', $this->warehouse_id)
                    ->first();
                
                $unit = \App\Models\ProductUnit::find($item['product_unit_id']);
                $quantityInBaseUnit = $unit ? $unit->toBaseUnit($item['quantity']) : $item['quantity'];

                // في حالة التعديل، يجب استثناء الكمية القديمة من الفحص
                $currentOrdered = 0;
                if ($this->isEdit && isset($item['id'])) {
                    $oldItem = \App\Models\SaleInvoiceItem::find($item['id']);
                    $currentOrdered = $oldItem ? $oldItem->quantity_in_base_unit : 0;
                }

                $available = ($stock ? $stock->quantity : 0) + $currentOrdered;

                if ($quantityInBaseUnit > $available) {
                    $this->addError('items', __('Insufficient stock for product: ') . $item['product_name'] . " (" . __('Available: ') . $available . ")");
                    return;
                }
            }
        }

        try {
            $invoiceData = [
                'customer_id' => $this->customer_id,
                'warehouse_id' => $this->warehouse_id,
                'sale_method_id' => SaleMethod::where('code', $this->sale_method_code)->first()?->id,
                'invoice_date' => $this->invoice_date,
                'due_date' => $this->due_date,
                'subtotal' => $this->subtotal,
                'tax_percentage' => $this->tax_percentage,
                'tax_amount' => $this->tax_amount,
                'discount_amount' => $this->discount_amount,
                'shipping_cost' => $this->shipping_cost,
                'total_amount' => $this->total_amount,
                'paid_amount' => $this->paid_amount,
                'previous_balance' => $this->customer_balance,
                'car_number' => $this->car_number,
                'driver_name' => $this->driver_name,
                'notes' => $this->notes,
                'status' => 'completed',
                'payment_method' => $this->sale_method_code,
                'payment_notes' => __('Initial payment'),
            ];

            if ($this->isEdit && $this->invoiceId) {
                $invoice = SaleInvoice::findOrFail($this->invoiceId);
                $invoice = $service->update($invoice, $invoiceData, $this->items);
                $msg = __('Invoice updated successfully');
            } else {
                $invoice = $service->create($invoiceData, $this->items);
                $msg = __('Invoice created successfully');
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => __('Success'),
                'msg' => $msg
            ]);

            $this->showModal = false;
            $this->dispatch('refresh-sales');
            
            // Handle printing
            if ($this->should_print) {
                // Direct print event for JS to handle (hidden iframe or auto-window)
                $this->dispatch('print-invoice-direct', ['invoiceId' => $invoice->id]);
            } else {
                // Just open the print modal for manual action
                $this->dispatch('open-print-modal', ['invoiceId' => $invoice->id]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => __('Error'),
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.sales.sale-invoice-form', [
            'customers' => Customer::active()->orderBy('name')->get(),
            'warehouses' => Warehouse::all(),
            'saleMethods' => app(PricingService::class)->availableSaleMethods(),
        ]);
    }
}
