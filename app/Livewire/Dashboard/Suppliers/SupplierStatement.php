<?php

namespace App\Livewire\Dashboard\Suppliers;

use App\Models\Supplier;
use App\Services\SupplierService;
use Livewire\Component;
use Carbon\Carbon;

class SupplierStatement extends Component
{
    public Supplier $supplier;
    public $fromDate;
    public $toDate;

    public function mount(Supplier $supplier)
    {
        $this->supplier = $supplier;
        $this->fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->toDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render(SupplierService $supplierService)
    {
        $transactions = $supplierService->getStatementData($this->supplier, $this->fromDate, $this->toDate);
        
        return view('livewire.dashboard.suppliers.supplier-statement', [
            'transactions' => $transactions,
            'items' => $transactions['items']
        ]);
    }

    public function print()
    {
        return redirect()->route('dashboard.suppliers.statement.print', [
            'supplier' => $this->supplier->id,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate
        ]);
    }
}
