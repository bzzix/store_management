<?php

namespace App\Livewire\Dashboard\Products;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Category;
use App\Services\CategoryService;

class Categories extends Component
{
    /**
     * حالة النموذج
     */
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;

    /**
     * بيانات النموذج
     */
    public ?int $editingCategoryId = null;
    public ?int $deletingCategoryId = null;
    public string $deletingCategoryName = '';

    #[Validate('required|string|min:2|max:255')]
    public string $name = '';
    
    #[Validate('nullable|string')]
    public string $description = '';
    
    #[Validate('nullable|numeric|min:0')]
    public string $sortOrder = '0';
    
    public bool $isActive = true;

    /**
     * الخدمة
     */
    private ?CategoryService $service = null;

    private function getService(): CategoryService
    {
        if ($this->service === null) {
            $this->service = new CategoryService();
        }
        return $this->service;
    }

    /**
     * الاستماع إلى الأحداث
     */
    #[\Livewire\Attributes\On('open-edit-category')]
    public function openEditModal($categoryId)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        try {
            $category = Category::findOrFail($categoryId);
            $this->editingCategoryId = $category->id;
            $this->name = $category->name;
            $this->description = $category->description ?? '';
            $this->sortOrder = (string)$category->sort_order;
            $this->isActive = (bool)$category->is_active;
            $this->showEditModal = true;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Category not found'));
        }
    }

    #[\Livewire\Attributes\On('toggle-category-active')]
    public function handleToggleCategoryActive($categoryId)
    {
        $this->toggleCategoryActive($categoryId);
    }

    /**
     * فتح نموذج الإضافة
     */
    public function showAddModal()
    {
        abort_if(!auth()->user()->can('products_create'), 403);
        $this->resetForm();
        $this->editingCategoryId = null;
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
     * حفظ التصنيف (إضافة أو تعديل)
     */
    public function saveCategory()
    {
        try {
            if ($this->editingCategoryId) {
                abort_if(!auth()->user()->can('products_edit'), 403);
            } else {
                abort_if(!auth()->user()->can('products_create'), 403);
            }
            // التحقق من الصحة (Validation)
            $this->validate();

            $data = [
                'name' => trim($this->name),
                'description' => $this->description ? trim($this->description) : null,
                'sort_order' => (int)$this->sortOrder,
                'is_active' => $this->isActive,
            ];

            if ($this->editingCategoryId) {
                // تعديل
                $category = Category::findOrFail($this->editingCategoryId);
                $this->getService()->updateCategory($category, $data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Category updated successfully'));
            } else {
                // إضافة
                $this->getService()->createCategory($data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Category created successfully'));
            }

            $this->closeModal();
            // تحديث الجدول
            $this->dispatch('refresh-categories');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('An error occurred: ') . $e->getMessage());
        }
    }

    /**
     * تأكيد الحذف
     */
    public function confirmDeleteCategory()
    {
        abort_if(!auth()->user()->can('products_delete'), 403);
        try {
            if (!$this->deletingCategoryId) {
                throw new \Exception(__('Category ID is missing'));
            }

            $category = Category::findOrFail($this->deletingCategoryId);
            $this->getService()->deleteCategory($category);
            
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Category deleted successfully'));
            $this->closeDeleteModal();
            // تحديث الجدول
            $this->dispatch('refresh-categories');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error deleting category: ') . $e->getMessage());
        }
    }

    /**
     * تبديل حالة التصنيف
     */
    public function toggleCategoryActive($categoryId)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        try {
            $category = Category::findOrFail($categoryId);
            $category->update(['is_active' => !$category->is_active]);
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Category status updated'));
            $this->dispatch('refresh-categories');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating category'));
        }
    }

    /**
     * فتح نموذج تأكيد الحذف
     */
    public function openDeleteModal($categoryId, $categoryName)
    {
        abort_if(!auth()->user()->can('products_delete'), 403);
        $this->deletingCategoryId = $categoryId;
        $this->deletingCategoryName = $categoryName;
        $this->showDeleteModal = true;
    }

    /**
     * إعادة تعيين النموذج
     */
    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->sortOrder = '0';
        $this->isActive = true;
        $this->editingCategoryId = null;
        $this->resetErrorBag();
    }

    /**
     * إعادة تعيين بيانات الحذف
     */
    private function resetDelete()
    {
        $this->deletingCategoryId = null;
        $this->deletingCategoryName = '';
    }

    public function render()
    {
        return view('livewire.dashboard.products.categories');
    }
}
