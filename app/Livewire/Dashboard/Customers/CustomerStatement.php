<?php

namespace App\Livewire\Dashboard\Customers;

use App\Models\Customer;
use App\Services\CustomerService;
use Livewire\Component;
use Carbon\Carbon;

class CustomerStatement extends Component
{
    public Customer $customer;
    public $fromDate;
    public $toDate;

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
        $this->fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->toDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render(CustomerService $customerService)
    {
        $transactions = $customerService->getStatementData($this->customer, $this->fromDate, $this->toDate);
        
        return view('livewire.dashboard.customers.customer-statement', [
            'transactions' => $transactions,
            'items' => $transactions['items']
        ]);
    }

    public function print()
    {
        return redirect()->route('dashboard.customers.statement.print', [
            'customer' => $this->customer->id,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate
        ]);
    }
}
