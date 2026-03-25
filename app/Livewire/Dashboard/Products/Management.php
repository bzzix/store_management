<?php

namespace App\Livewire\Dashboard\Products;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;

class Management extends Component
{
    /**
     * التبويب المعروض
     */
    public string $products_tab = 'categories';

    /**
     * حالة نموذج المنتجات
     */
    public bool $showCreateProductModal = false;
    public bool $showEditProductModal = false;
    public bool $showDeleteProductModal = false;

    /**
     * بيانات نموذج المنتجات
     */
    public ?int $editingProductId = null;
    public ?int $deletingProductId = null;
    public string $deletingProductName = '';

    #[Validate('required|string|min:2|max:255')]
    public string $productName = '';
    
    #[Validate('nullable|string|max:100')]
    public string $productSku = '';
    
    #[Validate('required|exists:categories,id')]
    public ?int $productCategoryId = null;
    
    #[Validate('nullable|string')]
    public string $productDescription = '';
    
    public bool $productIsActive = true;

    /**
     * الخدمة
     */
    private ?ProductService $service = null;

    private function getService(): ProductService
    {
        if ($this->service === null) {
            $this->service = new ProductService();
        }
        return $this->service;
    }

    /**
     * الاستماع للأحداث
     */
    #[\Livewire\Attributes\On('open-edit-product')]
    public function handleOpenEditProduct($productId)
    {
        $this->openEditProductModal($productId);
    }

    #[\Livewire\Attributes\On('toggle-product-active')]
    public function handleToggleProductActive($productId)
    {
        $this->toggleProductActive($productId);
    }

    /**
     * فتح نموذج إضافة منتج
     */
    public function showAddProductModal()
    {
        $this->resetProductForm();
        $this->editingProductId = null;
        $this->showCreateProductModal = true;
    }

    /**
     * فتح نموذج تعديل المنتج
     */
    public function openEditProductModal($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $this->editingProductId = $product->id;
            $this->productName = $product->name;
            $this->productSku = $product->sku ?? '';
            $this->productCategoryId = $product->category_id;
            $this->productDescription = $product->description ?? '';
            $this->productIsActive = (bool)$product->is_active;
            $this->showEditProductModal = true;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Product not found'));
        }
    }

    /**
     * حفظ المنتج
     */
    public function saveProduct()
    {
        try {
            $this->validate();

            $data = [
                'name' => trim($this->productName),
                'sku' => $this->productSku ? trim($this->productSku) : null,
                'category_id' => (int)$this->productCategoryId,
                'description' => $this->productDescription ? trim($this->productDescription) : null,
                'is_active' => $this->productIsActive,
            ];

            if ($this->editingProductId) {
                // تعديل
                $product = Product::findOrFail($this->editingProductId);
                $this->getService()->updateProduct($product, $data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Product updated successfully'));
            } else {
                // إضافة
                $this->getService()->createProduct($data);
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Product created successfully'));
            }

            $this->closeProductModal();
            $this->dispatch('refresh-products');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('An error occurred: ') . $e->getMessage());
        }
    }

    /**
     * تأكيد حذف المنتج
     */
    public function confirmDeleteProduct()
    {
        try {
            if (!$this->deletingProductId) {
                throw new \Exception(__('Product ID is missing'));
            }

            $product = Product::findOrFail($this->deletingProductId);
            $this->getService()->deleteProduct($product);
            
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Product deleted successfully'));
            $this->closeDeleteModal();
            $this->dispatch('refresh-products');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error deleting product: ') . $e->getMessage());
        }
    }

    /**
     * تبديل حالة النشاط
     */
    public function toggleProductActive($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $product->update(['is_active' => !$product->is_active]);
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Product status updated'));
            $this->dispatch('refresh-products');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating product'));
        }
    }

    /**
     * فتح نموذج حذف
     */
    public function openDeleteModal($productId, $productName)
    {
        $this->deletingProductId = $productId;
        $this->deletingProductName = $productName;
        $this->showDeleteProductModal = true;
    }

    /**
     * إغلاق نموذج المنتج
     */
    public function closeProductModal()
    {
        $this->showCreateProductModal = false;
        $this->showEditProductModal = false;
        $this->resetProductForm();
    }

    /**
     * إغلاق نموذج الحذف
     */
    public function closeDeleteModal()
    {
        $this->showDeleteProductModal = false;
        $this->resetDeleteProduct();
    }

    /**
     * إعادة تعيين نموذج المنتج
     */
    private function resetProductForm()
    {
        $this->productName = '';
        $this->productSku = '';
        $this->productCategoryId = null;
        $this->productDescription = '';
        $this->productIsActive = true;
        $this->editingProductId = null;
        $this->resetErrorBag();
    }

    /**
     * إعادة تعيين بيانات الحذف
     */
    private function resetDeleteProduct()
    {
        $this->deletingProductId = null;
        $this->deletingProductName = '';
    }

    public function render()
    {
        return view('livewire.dashboard.products.management', [
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
