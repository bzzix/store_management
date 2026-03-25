<?php

namespace App\Livewire\Dashboard\Pricing;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\ProfitMarginTier;
use App\Models\SaleMethod;
use App\Services\ProfitMarginTierService;

class Tiers extends Component
{
    /**
     * الـ tab الحالي المعروض
     */
    public string $pricing_tab = 'pricing_tiers';

    /**
     * حالة النموذج - للشرائح
     */
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    /**
     * حالة النموذج - لطرق البيع
     */
    public bool $showCreateMethodModal = false;
    public bool $showEditMethodModal = false;
    public bool $showDeleteMethodModal = false;

    /**
     * بيانات النموذج - للشرائح
     */
    public ?int $editingTierId = null;
    public ?int $deletingTierId = null;
    public string $deletingTierName = '';

    /**
     * بيانات النموذج - لطرق البيع
     */
    public ?int $editingMethodId = null;
    public ?int $deletingMethodId = null;
    public string $deletingMethodName = '';

    #[Validate('required|string|min:2|max:255')]
    public string $name = '';
    
    #[Validate('required|numeric|min:0')]
    public string $minValue = '';
    
    #[Validate('nullable|numeric|min:0')]
    public string $maxValue = '';
    
    #[Validate('nullable|numeric|min:0')]
    public string $priority = '0';
    
    public bool $isActive = true;

    /**
     * متغيرات طريقة البيع
     */
    #[Validate('required|string|min:2|max:255')]
    public string $methodName = '';
    
    #[Validate('nullable|string|max:50')]
    public string $methodCode = '';
    
    #[Validate('nullable|numeric|min:0')]
    public string $methodPriority = '0';
    
    public bool $methodIsActive = true;

    /**
     * الخدمة
     */
    private ?ProfitMarginTierService $tierService = null;

    private function getTierService(): ProfitMarginTierService
    {
        if ($this->tierService === null) {
            $this->tierService = new ProfitMarginTierService();
        }
        return $this->tierService;
    }

    /**
     * الاستماع إلى الأحداث
     */
    #[\Livewire\Attributes\On('open-edit-modal')]
    public function openEditModal($tier)
    {
        $tier = is_array($tier) ? (object)$tier : $tier;
        $this->editingTierId = $tier->id;
        $this->name = $tier->name;
        $this->minValue = (string)$tier->min_value;
        $this->maxValue = $tier->max_value ? (string)$tier->max_value : '';
        $this->priority = (string)$tier->priority;
        $this->isActive = (bool)$tier->is_active;
        $this->showEditModal = true;
    }

    #[\Livewire\Attributes\On('open-edit-method')]
    public function handleOpenEditMethod($methodId)
    {
        $this->openEditMethodModal($methodId);
    }

    #[\Livewire\Attributes\On('toggle-method-active')]
    public function handleToggleMethodActive($methodId)
    {
        $this->toggleMethodActive($methodId);
    }

    #[\Livewire\Attributes\On('confirm-delete-method')]
    public function handleConfirmDeleteMethod($methodId)
    {
        $this->deletingMethodId = $methodId;
        $this->confirmDeleteMethod();
    }

    public function handleConfirmDelete($tierId)
    {
        $this->deletingTierId = $tierId;
        $this->confirmDelete();
    }

    /**
     * التحقق من الأسماء
     */
    public function validateName()
    {
        $this->validateOnly('name');
    }

    /**
     * التحقق من القيمة الدنيا
     */
    public function validateMinValue()
    {
        $this->validateOnly('minValue');
    }

    /**
     * التحقق من القيمة العليا
     */
    public function validateMaxValue()
    {
        $this->validateOnly('maxValue');
    }

    /**
     * التحقق من الأولوية
     */
    public function validatePriority()
    {
        $this->validateOnly('priority');
    }

    /**
     * تحديث الـ tab المعروض
     */
    public function updatingPricingTab($value)
    {
        $this->pricing_tab = $value;
    }

    /**
     * فتح نموذج الإضافة
     */
    public function showAddModal()
    {
        $this->resetForm();
        $this->editingTierId = null;
        $this->showCreateModal = true;
    }

    /**
     * إغلاق النموذج
     */
    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->resetForm();
    }

    /**
     * إغلاق نموذج الحذف
     */
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->resetDelete();
    }

    /**
     * حفظ الشريحة (إضافة أو تعديل)
     */
    public function saveTier()
    {
        try {
            // التحقق من الصحة (Validation)
            $this->validate();

            $data = [
                'name' => trim($this->name),
                'min_value' => (float)$this->minValue,
                'max_value' => $this->maxValue ? (float)$this->maxValue : null,
                'priority' => (int)$this->priority,
                'is_active' => $this->isActive,
            ];

            if ($this->editingTierId) {
                // تعديل
                $tier = ProfitMarginTier::findOrFail($this->editingTierId);
                $this->getTierService()->updateTier($tier, $data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Tier updated successfully'));
            } else {
                // إضافة
                $this->getTierService()->createTier($data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Tier created successfully'));
            }

            $this->closeModal();
            // تحديث الجدول
            $this->dispatch('refresh-tiers');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('An error occurred: ') . $e->getMessage());
        }
    }

    /**
     * تأكيد الحذف
     */
    public function confirmDelete()
    {
        //dd('fffffff');
        try {
            if (!$this->deletingTierId) {
                throw new \Exception(__('Tier ID is missing'));
            }

            $tier = ProfitMarginTier::findOrFail($this->deletingTierId);
            $this->getTierService()->deleteTier($tier);
            
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Tier deleted successfully'));
            $this->closeDeleteModal();
            // تحديث الجدول
            $this->dispatch('refresh-tiers');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error deleting tier: ') . $e->getMessage());
        }
    }

    /**
     * إعادة تعيين النموذج
     */
    private function resetForm()
    {
        $this->name = '';
        $this->minValue = '';
        $this->maxValue = '';
        $this->priority = '0';
        $this->isActive = true;
        $this->editingTierId = null;
    }

    /**
     * إعادة تعيين بيانات الحذف
     */
    private function resetDelete()
    {
        $this->deletingTierId = null;
        $this->deletingTierName = '';
    }

    /**
     * ========================================
     * 📦 طرق البيع (Sale Methods)
     * ========================================
     */

    /**
     * فتح نموذج إضافة طريقة بيع
     */
    public function showAddMethodModal()
    {
        $this->resetMethodForm();
        $this->editingMethodId = null;
        $this->showCreateMethodModal = true;
    }

    /**
     * فتح نموذج تعديل طريقة بيع
     */
    public function openEditMethodModal($methodId)
    {
        try {
            $method = SaleMethod::findOrFail($methodId);
            $this->editingMethodId = $method->id;
            $this->methodName = $method->name;
            $this->methodCode = $method->code ?? '';
            $this->methodPriority = (string)$method->priority;
            $this->methodIsActive = (bool)$method->is_active;
            $this->showEditMethodModal = true;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Sale method not found'));
        }
    }

    /**
     * حفظ طريقة البيع (إضافة أو تعديل)
     */
    public function saveSaleMethod()
    {
        try {
            // التحقق من الصحة
            $this->validate([
                'methodName' => 'required|string|min:2|max:255',
                'methodCode' => 'nullable|string|max:50',
                'methodPriority' => 'nullable|numeric|min:0',
            ]);

            $data = [
                'name' => trim($this->methodName),
                'code' => $this->methodCode ? trim($this->methodCode) : null,
                'priority' => (int)$this->methodPriority,
                'is_active' => $this->methodIsActive,
            ];

            if ($this->editingMethodId) {
                // تعديل
                $method = SaleMethod::findOrFail($this->editingMethodId);
                $method->update($data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Sale method updated successfully'));
            } else {
                // إضافة
                SaleMethod::create($data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Sale method created successfully'));
            }

            $this->closeMethodModal();
            // تحديث الجدول
            $this->dispatch('refresh-sale-methods');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('An error occurred: ') . $e->getMessage());
        }
    }

    /**
     * تأكيد حذف طريقة البيع
     */
    public function confirmDeleteMethod()
    {
        try {
            if (!$this->deletingMethodId) {
                throw new \Exception(__('Sale method ID is missing'));
            }

            $method = SaleMethod::findOrFail($this->deletingMethodId);
            $method->delete();
            
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Sale method deleted successfully'));
            $this->closeDeleteMethodModal();
            // تحديث الجدول
            $this->dispatch('refresh-sale-methods');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error deleting sale method: ') . $e->getMessage());
        }
    }

    /**
     * تبديل حالة طريقة البيع
     */
    public function toggleMethodActive($methodId)
    {
        try {
            $method = SaleMethod::findOrFail($methodId);
            $method->update(['is_active' => !$method->is_active]);
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Sale method status updated'));
            $this->dispatch('refresh-sale-methods');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating sale method'));
        }
    }

    /**
     * إغلاق نموذج طريقة البيع
     */
    public function closeMethodModal()
    {
        $this->showCreateMethodModal = false;
        $this->showEditMethodModal = false;
        $this->resetMethodForm();
    }

    /**
     * إغلاق نموذج حذف طريقة البيع
     */
    public function closeDeleteMethodModal()
    {
        $this->showDeleteMethodModal = false;
        $this->resetDeleteMethod();
    }

    /**
     * فتح نموذج تأكيد حذف طريقة البيع
     */
    public function openDeleteMethodModal($methodId, $methodName)
    {
        $this->deletingMethodId = $methodId;
        $this->deletingMethodName = $methodName;
        $this->showDeleteMethodModal = true;
    }

    /**
     * إعادة تعيين بيانات النموذج - لطرق البيع
     */
    private function resetMethodForm()
    {
        $this->methodName = '';
        $this->methodCode = '';
        $this->methodPriority = '0';
        $this->methodIsActive = true;
        $this->editingMethodId = null;
        $this->resetErrorBag();
    }

    /**
     * إعادة تعيين بيانات الحذف - لطرق البيع
     */
    private function resetDeleteMethod()
    {
        $this->deletingMethodId = null;
        $this->deletingMethodName = '';
    }
}
