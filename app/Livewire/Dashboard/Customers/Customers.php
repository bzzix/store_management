<?php

namespace App\Livewire\Dashboard\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\On;

class Customers extends Component
{
    public $deletingCustomerId = null;
    public $deletingCustomerName = '';

    #[\Livewire\Attributes\On('refresh-customers')]
    public function refresh()
    {
        // Table will refresh automatically if it listens to this event or via refresh-datatable
        $this->dispatch('refresh-datatable');
    }

    #[On('edit-customer-form')]
    public function openEditModal($customerId)
    {
        // This is now handled directly by CustomerForm listening to the same event
        // But if we want to do something here, we can.
    }

    #[On('delete-customer')]
    public function openDeleteModal($customerId, $customerName)
    {
        abort_if(!auth()->user()->can('customers_delete'), 403);
        $this->deletingCustomerId = $customerId;
        $this->deletingCustomerName = $customerName;
    }

    public function confirmDelete(\App\Services\CustomerService $service)
    {
        abort_if(!auth()->user()->can('customers_delete'), 403);
        
        try {
            $customer = Customer::findOrFail($this->deletingCustomerId);
            $service->delete($customer);
            
            $this->dispatch('notify', [
                'type' => 'success', 
                'title' => __('Success'), 
                'msg' => __('Customer deleted successfully')
            ]);
            $this->dispatch('refresh-datatable');
            $this->resetDelete();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => __('Error'),
                'msg' => __('Error deleting customer')
            ]);
        }
    }

    public function resetDelete()
    {
        $this->deletingCustomerId = null;
        $this->deletingCustomerName = '';
    }

    public function render()
    {
        return view('livewire.dashboard.customers.customers');
    }
}
