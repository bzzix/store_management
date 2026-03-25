<?php

namespace App\Services;

use App\Models\Warehouse;

class WarehouseService
{
    /**
     * إنشاء مخزن جديد
     */
    public function create(array $data): Warehouse
    {
        $this->validateWarehouseData($data);

        if (isset($data['code']) && Warehouse::where('code', $data['code'])->exists()) {
            throw new \InvalidArgumentException(__('The code already exists'));
        }

        return Warehouse::create([
            'name' => $data['name'],
            'code' => $data['code'] ?? \Str::slug($data['name']),
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'is_main' => $data['is_main'] ?? false,
            'capacity' => isset($data['capacity']) ? (int) $data['capacity'] : null,
            'current_stock_value' => 0,
            'manager_id' => $data['manager_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    /**
     * تحديث مخزن
     */
    public function update(Warehouse $warehouse, array $data): Warehouse
    {
        $this->validateWarehouseData($data);

        if (isset($data['code']) && Warehouse::where('code', $data['code'])->where('id', '!=', $warehouse->id)->exists()) {
            throw new \InvalidArgumentException(__('The code already exists'));
        }

        $warehouse->update([
            'name' => $data['name'] ?? $warehouse->name,
            'code' => $data['code'] ?? $warehouse->code,
            'address' => $data['address'] ?? $warehouse->address,
            'phone' => $data['phone'] ?? $warehouse->phone,
            'email' => $data['email'] ?? $warehouse->email,
            'is_main' => isset($data['is_main']) ? (bool) $data['is_main'] : $warehouse->is_main,
            'capacity' => isset($data['capacity']) ? ($data['capacity'] ? (int) $data['capacity'] : null) : $warehouse->capacity,
            'manager_id' => $data['manager_id'] ?? $warehouse->manager_id,
            'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $warehouse->is_active,
        ]);

        return $warehouse->fresh();
    }

    /**
     * حذف مخزن
     */
    public function delete(Warehouse $warehouse): bool
    {
        return $warehouse->delete();
    }

    protected function validateWarehouseData(array $data): void
    {
        if (empty($data['name'])) {
            throw new \InvalidArgumentException(__('validation.required', ['attribute' => __('Name')]));
        }
    }
}
