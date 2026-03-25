<?php

namespace App\Livewire\Dashboard\Customers;

use App\Models\Customer;
use Livewire\Component;
use Livewire\Attributes\On;

class CustomerForm extends Component
{
    public $showModal = false;
    public $isEdit = false;
    public $customerId;
    
    // Form fields
    public $name;
    public $email;
    public $phone;
    public $phone_2;
    public $address;
    public $city;
    public $tax_number;
    public $credit_limit = 0;
    public $customer_type = 'individual';
    public $company_name;
    public $notes;
    public $is_active = true;
    public $opening_balance = 0;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $this->customerId,
            'phone' => 'required|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'credit_limit' => 'required|numeric|min:0',
            'customer_type' => 'required|in:individual,company',
            'company_name' => 'required_if:customer_type,company|nullable|string|max:255',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'opening_balance' => 'nullable|numeric',
        ];
    }

    #[On('reset-customer-form')]
    public function resetForm()
    {
        $this->reset(['name', 'email', 'phone', 'phone_2', 'address', 'city', 'tax_number', 'customer_type', 'company_name', 'notes', 'opening_balance', 'credit_limit', 'is_active', 'customerId', 'isEdit']);
        $this->is_active = true;
        $this->customer_type = 'individual';
        $this->credit_limit = 0;
        $this->opening_balance = 0;
        $this->showModal = true;
    }

    public function showAddModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    #[On('edit-customer-form')]
    public function edit($customerId)
    {
        $this->resetForm();
        $this->isEdit = true;
        $this->customerId = $customerId;
        
        $customer = Customer::findOrFail($customerId);
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->phone_2 = $customer->phone_2;
        $this->address = $customer->address;
        $this->city = $customer->city;
        $this->tax_number = $customer->tax_number;
        $this->customer_type = $customer->customer_type;
        $this->company_name = $customer->company_name;
        $this->notes = $customer->notes;
        $this->opening_balance = $customer->opening_balance;
        $this->credit_limit = $customer->credit_limit;
        $this->is_active = $customer->is_active;

        $this->showModal = true;
    }

    public function save(\App\Services\CustomerService $service)
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_2' => $this->phone_2,
            'address' => $this->address,
            'city' => $this->city,
            'tax_number' => $this->tax_number,
            'customer_type' => $this->customer_type,
            'company_name' => $this->company_name,
            'notes' => $this->notes,
            'opening_balance' => $this->opening_balance,
            'credit_limit' => $this->credit_limit,
            'is_active' => $this->is_active,
        ];

        try {
            if ($this->isEdit) {
                abort_if(!auth()->user()->can('customers_update'), 403);
                $customer = Customer::findOrFail($this->customerId);
                $service->update($customer, $data);
                $msg = __('Customer updated successfully');
            } else {
                abort_if(!auth()->user()->can('customers_add'), 403);
                $service->create($data);
                $msg = __('Customer created successfully');
            }

            $this->showModal = false;
            $this->dispatch('refresh-customers');
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => __('Success'),
                'msg' => $msg
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => __('Error'),
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard.customers.customer-form');
    }
}
