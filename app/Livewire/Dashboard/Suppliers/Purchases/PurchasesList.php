<?php

namespace App\Livewire\Dashboard\Suppliers\Purchases;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;

class PurchasesList extends Component
{
    use WithFileUploads;

    /**
     * التبويب المعروض
     */
    public string $purchases_tab = 'invoices_list';

    /**
     * حالة نماذج الفواتير
     */
    public bool $showInvoiceModal = false;
    public bool $showDeleteInvoiceModal = false;
    public bool $showPrintModal = false;
    public $viewInvoice = null;
    public bool $isEditing = false;
    public ?int $invoiceId = null;

    /**
     * بيانات الفاتورة الأساسية
     */
    public $supplier_id;
    public $warehouse_id;
    public $invoice_date;
    public $due_date;
    public $status = 'completed';
    public $payment_status = 'unpaid';
    public $notes;
    public $image;
    public $previous_balance = 0;
    
    /**
     * عناصر الفاتورة
     */
    public array $items = []; // [{product_id, product_name, unit_id, unit_name, quantity, unit_price, tax, discount, total}]
    public $subtotal = 0;
    public $tax_amount = 0;
    public $discount_amount = 0;
    public $total_amount = 0;
    public $paid_amount = 0;

    /**
     * القوائم المنسدلة
     */
    public $suppliers = [];
    public $warehouses = [];
    public $products = [];
    
    /**
     * Search State
     */
    public string $productSearch = '';
    public $searchResults = [];

    /**
     * بيانات نماذج الحذف
     */
    public ?int $deletingInvoiceId = null;
    public string $deletingInvoiceNumber = '';

    public function mount()
    {
        $this->suppliers = Supplier::where('is_active', true)->get();
        $this->warehouses = Warehouse::where('is_active', true)->get();
        $this->products = Product::with('units')->where('is_active', true)->get();
        $this->invoice_date = date('Y-m-d');
        $this->due_date = date('Y-m-d');
    }

    public function openCreateModal()
    {
        $this->resetInvoiceForm();
        $this->isEditing = false;
        $this->showInvoiceModal = true;
    }

    public function updatedSupplierId($value)
    {
        if ($value) {
            $supplier = Supplier::find($value);
            if ($supplier) {
                $lastInvoice = PurchaseInvoice::where('supplier_id', $supplier->id)
                                              ->latest('id')
                                              ->first();
                if ($lastInvoice) {
                    $this->previous_balance = $lastInvoice->total_amount - $lastInvoice->paid_amount;
                } else {
                    $this->previous_balance = $supplier->current_balance;
                }
            } else {
                $this->previous_balance = 0;
            }
        } else {
            $this->previous_balance = 0;
        }
        $this->calculateTotals();
    }

    #[\Livewire\Attributes\On('open-edit-modal')]
    public function handleOpenEditModal($invoiceId)
    {
        $this->resetInvoiceForm();
        $this->isEditing = true;
        $this->invoiceId = $invoiceId;

        $invoice = PurchaseInvoice::with('items.product')->findOrFail($invoiceId);
        
        $this->supplier_id = $invoice->supplier_id;
        $this->warehouse_id = $invoice->warehouse_id;
        $this->invoice_date = $invoice->invoice_date->format('Y-m-d');
        $this->due_date = $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '';
        $this->status = $invoice->status;
        $this->payment_status = $invoice->payment_status;
        $this->notes = $invoice->notes;
        $this->discount_amount = $invoice->discount_amount;
        $this->tax_amount = $invoice->tax_amount;
        $this->paid_amount = $invoice->paid_amount;
        $this->previous_balance = $invoice->previous_balance;
        
        // Note: we don't bind existing image to the file input to avoid re-uploading, 
        // we keep it visual in blade if needed.
        $this->image = null;

        foreach ($invoice->items as $item) {
            $this->items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name ?? '',
                'unit_id' => $item->product_unit_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_amount' => $item->tax_amount,
                'discount_amount' => $item->discount_amount,
                'total' => $item->total,
            ];
        }

        $this->calculateTotals();
        $this->showInvoiceModal = true;
    }

    #[\Livewire\Attributes\On('open-print-modal')]
    public function handleOpenPrintModal($invoiceId)
    {
        $this->viewInvoice = PurchaseInvoice::with(['supplier', 'warehouse', 'user', 'items.product.units'])->findOrFail($invoiceId);
        $this->showPrintModal = true;
    }

    public function closePrintModal()
    {
        $this->showPrintModal = false;
        $this->viewInvoice = null;
    }

    public function addProductToInvoice($productId)
    {
        $product = Product::with('units')->find($productId);
        if (!$product) return;

        // مراجعة ما إذا كان المنتج موجود مسبقاً في الفاتورة
        $existingIndex = collect($this->items)->search(fn($item) => $item['product_id'] == $productId);
        
        if ($existingIndex !== false) {
            $this->items[$existingIndex]['quantity'] += 1;
            $this->items[$existingIndex]['total'] = ($this->items[$existingIndex]['quantity'] * $this->items[$existingIndex]['unit_price']) - $this->items[$existingIndex]['discount_amount'] + $this->items[$existingIndex]['tax_amount'];
        } else {
            $this->items[] = [
                'id' => null,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_id' => $product->default_unit->id ?? null,
                'quantity' => 1,
                'unit_price' => $product->current_cost_price ?? 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total' => $product->current_cost_price ?? 0,
            ];
        }

        $this->calculateTotals();
        $this->dispatch('itemAdded'); // Optional: for UI scroll or focus
    }

    public function updatedProductSearch($value)
    {
        if (strlen($value) > 1) {
            $this->searchResults = Product::where('is_active', true)
                ->with(['units', 'warehouseStock'])
                ->where(function($query) use ($value) {
                    $query->where('name', 'like', '%' . $value . '%')
                          ->orWhere('sku', 'like', '%' . $value . '%')
                          ->orWhere('barcode', 'like', '%' . $value . '%');
                })
                ->take(10)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function addProductFromSearch($productId)
    {
        $this->addProductToInvoice($productId);
        $this->productSearch = '';
        $this->searchResults = [];
    }

    public function updateItemRow()
    {
        // يسمى كلما حدث تغيير في جدول المنتجات لإعادة حساب الإجمالي
        foreach ($this->items as $key => $item) {
            $qty = (float)($item['quantity'] ?? 0);
            $price = (float)($item['unit_price'] ?? 0);
            $discount = (float)($item['discount_amount'] ?? 0);
            $tax = (float)($item['tax_amount'] ?? 0);
            
            $this->items[$key]['total'] = ($qty * $price) - $discount + $tax;
        }
        $this->calculateTotals();
    }

    public function removeProductFromInvoice($index)
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // reindex
            $this->calculateTotals();
        }
    }

    public function calculateTotals()
    {
        $this->subtotal = ceil(collect($this->items)->sum(function ($item) {
            return (float)($item['quantity'] ?? 0) * (float)($item['unit_price'] ?? 0);
        }));

        // Sum row level taxes and discounts
        $rowsTax = ceil(collect($this->items)->sum(fn($item) => (float)($item['tax_amount'] ?? 0)));
        $rowsDiscount = ceil(collect($this->items)->sum(fn($item) => (float)($item['discount_amount'] ?? 0)));

        // Let global inputs override row ones, or aggregate them if desired. Here we just take the global ones or assume inputs map directly.
        // If the user inputs global tax/discount, we apply it. 
        $globalTax = ceil((float)$this->tax_amount);
        $globalDiscount = ceil((float)$this->discount_amount);

        // Total amount now includes the previous supplier balance
        $this->total_amount = ceil($this->subtotal + $globalTax - $globalDiscount);
    }

    public function saveInvoice()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'invoice_date' => 'required|date',
            'due_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:product_units,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if (empty($this->items) && (empty($this->paid_amount) || $this->paid_amount <= 0)) {
            $this->addError('items', 'يجب إضافة منتجات أو دفع مبلغ مالي على الأقل.');
            return;
        }

        try {
            $invoiceService = app(\App\Services\InvoiceService::class);
            
            $invoiceData = [
                'supplier_id' => $this->supplier_id,
                'warehouse_id' => $this->warehouse_id,
                'invoice_date' => $this->invoice_date,
                'due_date' => $this->due_date,
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'discount_amount' => $this->discount_amount,
                'total_amount' => $this->total_amount,
                'paid_amount' => $this->paid_amount,
                'previous_balance' => $this->previous_balance,
                'status' => $this->status,
                'notes' => $this->notes,
                'image' => $this->image ? $this->image->store('purchase_invoices', 'public') : null,
            ];

            if ($this->isEditing && $this->invoiceId) {
                $invoice = \App\Models\PurchaseInvoice::findOrFail($this->invoiceId);
                $invoiceService->updatePurchaseInvoice($invoice, $invoiceData, $this->items);
                $msg = __('Invoice updated successfully');
            } else {
                $invoiceService->createPurchaseInvoice($invoiceData, $this->items);
                $msg = __('Invoice saved successfully');
            }

            $this->dispatch('notify', type: 'success', title: __('Success'), message: $msg);
            $this->resetInvoiceForm();
            $this->showInvoiceModal = false;
            $this->dispatch('refreshDatatable');

        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), message: __('Error saving invoice: ') . $e->getMessage());
        }
    }

    public function resetInvoiceForm()
    {
        $this->invoiceId = null;
        $this->supplier_id = null;
        $this->warehouse_id = null;
        $this->invoice_date = date('Y-m-d');
        $this->due_date = date('Y-m-d');
        $this->status = 'completed';
        $this->payment_status = 'unpaid';
        $this->notes = null;
        $this->image = null;
        $this->previous_balance = 0;
        $this->items = [];
        $this->subtotal = 0;
        $this->tax_amount = 0;
        $this->discount_amount = 0;
        $this->total_amount = 0;
        $this->paid_amount = 0;
        $this->productSearch = '';
        $this->searchResults = [];
        $this->resetErrorBag();
        $this->resetValidation();
    }

    #[\Livewire\Attributes\On('open-delete-modal')]
    public function openDeleteModal($invoiceId, $invoiceNumber)
    {
        $this->deletingInvoiceId = $invoiceId;
        $this->deletingInvoiceNumber = $invoiceNumber;
        $this->showDeleteInvoiceModal = true;
    }

    public function confirmDeleteInvoice()
    {
        try {
            if (!$this->deletingInvoiceId) throw new \Exception(__('Invoice ID is missing'));

            $invoice = PurchaseInvoice::findOrFail($this->deletingInvoiceId);
            // Allow Super Admin to delete any invoice, others only draft
            if ($invoice->status !== 'draft' && !auth()->user()->can('purchase_invoices_delete_any')) {
                 throw new \Exception(__('Only draft invoices can be deleted or you lack permission to delete non-draft invoices'));
            }

            $invoiceService = app(\App\Services\InvoiceService::class);
            $invoiceService->deletePurchaseInvoice($invoice);
            $this->dispatch('notify', type: 'success', title: __('Success'), message: __('Invoice deleted successfully'));
            $this->closeDeleteModal();
            $this->dispatch('refreshDatatable');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), message: __('Error deleting invoice: ') . $e->getMessage());
        }
    }

    public function closeDeleteModal()
    {
        $this->showDeleteInvoiceModal = false;
        $this->deletingInvoiceId = null;
        $this->deletingInvoiceNumber = '';
    }

    public function setTab($tab)
    {
        $this->purchases_tab = $tab;
    }

    public function render()
    {
        return view('livewire.dashboard.suppliers.purchases.purchases-list');
    }
}


