<?php

namespace App\Livewire\Dashboard\Warehouses;

use Livewire\Component;
use App\Models\Warehouse;
use App\Models\User;
use App\Services\WarehouseService;

class Warehouses extends Component
{
    public bool $showCreateModal = false;
    public bool $showEditModal = false;

    public ?int $editingWarehouseId = null;
    public ?int $deletingWarehouseId = null;
    public string $deletingWarehouseName = '';

    public string $name = '';
    public string $code = '';
    public ?string $address = '';
    public ?string $phone = '';
    public ?string $email = '';
    public bool $isMain = false;
    public ?string $capacity = '';
    public ?int $managerId = null;
    public bool $isActive = true;

    private ?WarehouseService $warehouseService = null;

    private function getWarehouseService(): WarehouseService
    {
        if ($this->warehouseService === null) {
            $this->warehouseService = new WarehouseService();
        }
        return $this->warehouseService;
    }

    #[\Livewire\Attributes\On('open-edit-warehouse-modal')]
    public function openEditModal($warehouse): void
    {
        $warehouse = is_array($warehouse) ? (object) $warehouse : $warehouse;
        $this->editingWarehouseId = $warehouse->id;
        $this->name = $warehouse->name;
        $this->code = $warehouse->code;
        $this->address = $warehouse->address ?? '';
        $this->phone = $warehouse->phone ?? '';
        $this->email = $warehouse->email ?? '';
        $this->isMain = (bool) $warehouse->is_main;
        $this->capacity = $warehouse->capacity ? (string) $warehouse->capacity : '';
        $this->managerId = $warehouse->manager_id;
        $this->isActive = (bool) $warehouse->is_active;
        $this->showEditModal = true;
    }

    public function showAddModal(): void
    {
        $this->resetForm();
        $this->editingWarehouseId = null;
        $this->showCreateModal = true;
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function saveWarehouse(): void
    {
        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'code' => 'required|string|min:2|max:50',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'capacity' => 'nullable|integer|min:0',
        ]);

        try {
            $data = [
                'name' => trim($this->name),
                'code' => trim($this->code),
                'address' => $this->address ?: null,
                'phone' => $this->phone ?: null,
                'email' => $this->email ?: null,
                'is_main' => $this->isMain,
                'capacity' => $this->capacity ? (int) $this->capacity : null,
                'manager_id' => $this->managerId ?: null,
                'is_active' => $this->isActive,
            ];

            if ($this->editingWarehouseId) {
                $warehouse = Warehouse::findOrFail($this->editingWarehouseId);
                $this->getWarehouseService()->update($warehouse, $data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Warehouse updated successfully'));
            } else {
                $this->getWarehouseService()->create($data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Warehouse created successfully'));
            }

            $this->closeModal();
            $this->dispatch('refresh-warehouses');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('An error occurred: ') . $e->getMessage());
        }
    }

    public function confirmDelete(): void
    {
        try {
            if (! $this->deletingWarehouseId) {
                throw new \Exception(__('Warehouse ID is missing'));
            }

            $warehouse = Warehouse::findOrFail($this->deletingWarehouseId);
            $this->getWarehouseService()->delete($warehouse);

            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Warehouse deleted successfully'));
            $this->deletingWarehouseId = null;
            $this->deletingWarehouseName = '';
            $this->dispatch('refresh-warehouses');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error deleting warehouse: ') . $e->getMessage());
        }
    }

    public function handleConfirmDelete($warehouseId): void
    {
        $this->deletingWarehouseId = $warehouseId;
        $this->confirmDelete();
    }

    private function resetForm(): void
    {
        $this->name = '';
        $this->code = '';
        $this->address = '';
        $this->phone = '';
        $this->email = '';
        $this->isMain = false;
        $this->capacity = '';
        $this->managerId = null;
        $this->isActive = true;
        $this->editingWarehouseId = null;
    }

    public function render()
    {
        return view('livewire.dashboard.warehouses.warehouses');
    }
}
