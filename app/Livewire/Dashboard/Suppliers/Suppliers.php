<?php

namespace App\Livewire\Dashboard\Suppliers;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Supplier;

class Suppliers extends Component
{
    /**
     * Modal States
     */
    public bool $showCreateModal = false;
    public bool $showEditModal = false;

    /**
     * Form Data
     */
    public ?int $editingSupplierId = null;
    public ?int $deletingSupplierId = null;
    public string $deletingSupplierName = '';

    #[Validate('required|string|min:2|max:255')]
    public string $name = '';
    
    #[Validate('nullable|string|max:255')]
    public string $company_name = '';
    
    #[Validate('nullable|email|max:255')]
    public string $email = '';
    
    #[Validate('required|string|max:50')]
    public string $phone = '';
    
    #[Validate('nullable|string|max:50')]
    public string $phone_2 = '';
    
    #[Validate('nullable|string|max:500')]
    public string $address = '';
    
    #[Validate('nullable|string|max:100')]
    public string $city = '';
    
    #[Validate('nullable|string|max:100')]
    public string $country = '';
    
    #[Validate('nullable|string|max:50')]
    public string $tax_number = '';
    
    #[Validate('nullable|numeric|min:0')]
    public ?float $credit_limit = null;
    
    #[Validate('nullable|numeric')]
    public ?float $current_balance = null;
    
    #[Validate('nullable|string|max:1000')]
    public string $notes = '';
    
    #[Validate('boolean')]
    public bool $is_active = true;

    /**
     * Event Listeners
     */
    #[\Livewire\Attributes\On('open-edit-modal')]
    public function openEditModal($supplierId)
    {
        $fullSupplier = Supplier::find($supplierId);
        
        if (!$fullSupplier) {
            $this->dispatch('notify', type: 'error', message: __('Supplier not found'));
            return;
        }

        $this->editingSupplierId = $fullSupplier->id;
        $this->name = $fullSupplier->name;
        $this->company_name = $fullSupplier->company_name ?? '';
        $this->email = $fullSupplier->email ?? '';
        $this->phone = $fullSupplier->phone ?? '';
        $this->phone_2 = $fullSupplier->phone_2 ?? '';
        $this->address = $fullSupplier->address ?? '';
        $this->city = $fullSupplier->city ?? '';
        $this->country = $fullSupplier->country ?? '';
        $this->tax_number = $fullSupplier->tax_number ?? '';
        $this->credit_limit = $fullSupplier->credit_limit;
        $this->current_balance = $fullSupplier->current_balance;
        $this->notes = $fullSupplier->notes ?? '';
        $this->is_active = (bool)$fullSupplier->is_active;
        
        $this->showEditModal = true;
    }

    #[\Livewire\Attributes\On('open-delete-modal')]
    public function openDeleteModal($supplierId, $supplierName)
    {
        $this->deletingSupplierId = $supplierId;
        $this->deletingSupplierName = $supplierName;
    }

    #[\Livewire\Attributes\On('confirm-delete')]
    public function handleConfirmDelete($supplierId)
    {
        $this->deletingSupplierId = $supplierId;
        $this->confirmDelete();
    }

    #[\Livewire\Attributes\On('toggle-status')]
    public function toggleStatus($supplierId)
    {
        try {
            $supplier = Supplier::findOrFail($supplierId);
            $supplier->is_active = !$supplier->is_active;
            $supplier->save();
            
            $status = $supplier->is_active ? __('Activated') : __('Deactivated');
            $this->dispatch('notify', type: 'success', message: __('Supplier :status successfully', ['status' => $status]));
            $this->dispatch('refresh-suppliers');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error toggling status'));
        }
    }


    /**
     * Modal Management
     */
    public function showAddModal()
    {
        $this->resetForm();
        $this->editingSupplierId = null;
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->resetForm();
    }

    /**
     * CRUD Operations
     */
    public function saveSupplier()
    {
        try {
            $this->validate();

            $data = [
                'name' => trim($this->name),
                'company_name' => trim($this->company_name) ?: null,
                'email' => trim($this->email) ?: null,
                'phone' => trim($this->phone),
                'phone_2' => trim($this->phone_2) ?: null,
                'address' => trim($this->address) ?: null,
                'city' => trim($this->city) ?: null,
                'country' => trim($this->country) ?: 'Egypt',
                'tax_number' => trim($this->tax_number) ?: null,
                'notes' => trim($this->notes) ?: null,
                'credit_limit' => $this->credit_limit ?? 0,
                'is_active' => $this->is_active,
            ];

            if ($this->editingSupplierId) {
                // Update
                $supplier = Supplier::findOrFail($this->editingSupplierId);
                
                // Allow balance updates during edit only if specifically intended or through dedicated process.
                // Here we might just allow updating it directly as requested for a simple setup.
                $data['current_balance'] = $this->current_balance ?? 0;
                
                $supplier->update($data);

                $this->dispatch('notify', type: 'success', message: __('Supplier updated successfully'));
            } else {
                // Create
                $data['current_balance'] = $this->current_balance ?? 0;
                $supplier = Supplier::create($data);

                $this->dispatch('notify', type: 'success', message: __('Supplier created successfully'));
            }

            $this->closeModal();
            $this->dispatch('refresh-suppliers');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('An error occurred: ') . $e->getMessage());
        }
    }

    public function confirmDelete()
    {
        try {
            if (!$this->deletingSupplierId) {
                throw new \Exception(__('Supplier ID is missing'));
            }

            $supplier = Supplier::findOrFail($this->deletingSupplierId);
            $supplier->delete();
            
            $this->dispatch('notify', type: 'success', message: __('Supplier deleted successfully'));
            $this->resetDelete();
            $this->dispatch('refresh-suppliers');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: __('Error deleting supplier: ') . $e->getMessage());
        }
    }

    /**
     * Reset Methods
     */
    private function resetForm()
    {
        $this->name = '';
        $this->company_name = '';
        $this->email = '';
        $this->phone = '';
        $this->phone_2 = '';
        $this->address = '';
        $this->city = '';
        $this->country = '';
        $this->tax_number = '';
        $this->credit_limit = null;
        $this->current_balance = null;
        $this->notes = '';
        $this->is_active = true;
        
        $this->editingSupplierId = null;
    }

    private function resetDelete()
    {
        $this->deletingSupplierId = null;
        $this->deletingSupplierName = '';
    }

    public function render()
    {
        return view('livewire.dashboard.suppliers.suppliers');
    }
}
