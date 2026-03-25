<?php

namespace App\Livewire\Dashboard\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\Product;
use App\Models\Category;
use App\Models\Warehouse;
use App\Models\ProfitMarginTier;
use App\Models\ProductUnit;
use App\Models\ProductImage;
use App\Models\ProductPrice;
use App\Services\ProductService;
use Illuminate\Support\Str;

class Products extends Component
{
    use WithFileUploads;

    /**
     * Modal States
     */
    public bool $showCreateModal = false;
    public bool $showEditModal = false;
    public bool $showUnitsModal = false;
    public bool $showImagesModal = false;
    public bool $showPricesModal = false;

    /**
     * Form Data
     */
    public ?int $editingProductId = null;
    public ?int $managingProductId = null;
    public ?int $deletingProductId = null;
    public string $deletingProductName = '';

    #[Validate('required|string|min:2|max:255')]
    public string $name = '';
    
    public string $slug = '';
    
    public string $sku = '';
    
    public string $barcode = '';
    
    #[Validate('required|exists:categories,id')]
    public ?int $category_id = null;
    
    #[Validate('required|exists:warehouses,id')]
    public ?int $warehouse_id = null;
    
    public ?int $profit_margin_tier_id = null;
    
    public string $description = '';
    
    #[Validate('required|string|max:50')]
    public string $base_unit = 'قطعة';
    
    #[Validate('nullable|numeric|min:0')]
    public ?float $weight = null;
    
    #[Validate('nullable|numeric|min:0')]
    public ?float $min_stock = 0;
    
    #[Validate('nullable|numeric|min:0')]
    public ?float $max_stock = null;
    
    public bool $is_active = true;
    
    public bool $is_featured = false;

    // Images
    public array $images = [];
    public array $existingImages = [];

    // Units
    public array $units = [];
    public ?int $editingUnitIndex = null;
    public string $unit_name = '';
    public float $unit_conversion_factor = 1;
    public bool $unit_is_default = false;

    // Prices
    #[Validate('nullable|numeric|min:0')]
    public ?float $cost_price = null;
    
    #[Validate('nullable|numeric|min:0')]
    public ?float $base_price = null;

    /**
     * Service
     */
    private ?ProductService $productService = null;

    private function getProductService(): ProductService
    {
        if ($this->productService === null) {
            $this->productService = new ProductService();
        }
        return $this->productService;
    }

    /**
     * Event Listeners
     */
    #[\Livewire\Attributes\On('open-edit-modal')]
    public function openEditModal($product)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        // Handle both ID and product object
        $productId = is_numeric($product) ? $product : ($product->id ?? null);
        
        if (!$productId) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Product ID is missing'));
            return;
        }
        
        // Load full product with relationships
        $fullProduct = Product::with(['units', 'images', 'prices'])->find($productId);
        
        if (!$fullProduct) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Product not found'));
            return;
        }

        $this->editingProductId = $fullProduct->id;
        $this->name = $fullProduct->name;
        $this->slug = $fullProduct->slug ?? '';
        $this->sku = $fullProduct->sku ?? '';
        $this->barcode = $fullProduct->barcode ?? '';
        $this->category_id = $fullProduct->category_id;
        $this->warehouse_id = $fullProduct->warehouse_id;
        $this->profit_margin_tier_id = $fullProduct->profit_margin_tier_id;
        $this->description = $fullProduct->description ?? '';
        $this->base_unit = $fullProduct->base_unit ?? 'قطعة';
        $this->weight = $fullProduct->weight;
        $this->min_stock = $fullProduct->min_stock_level;
        $this->max_stock = $fullProduct->max_stock_level;
        $this->is_active = (bool)$fullProduct->is_active;
        $this->is_featured = (bool)$fullProduct->is_featured;
        
        // Load existing images
        $this->existingImages = $fullProduct->images->map(function($img) {
            return [
                'id' => $img->id,
                'path' => $img->image_path,
                'url' => asset('storage/' . $img->image_path)
            ];
        })->toArray();

        // Load latest price
        $latestPrice = $fullProduct->prices()->latest()->first();
        if ($latestPrice) {
            $this->cost_price = $latestPrice->cost_price;
            $this->base_price = $latestPrice->base_price;
        } else {
            $this->cost_price = $fullProduct->current_cost_price;
            $this->base_price = $fullProduct->current_base_price;
        }

        $this->showEditModal = true;
    }

    /**
     * فتح نموذج حذف المنتج
     */
    public function openDeleteModal($productId, $productName)
    {
        abort_if(!auth()->user()->can('products_delete'), 403);
        $this->deletingProductId = $productId;
        $this->deletingProductName = $productName;
    }

    #[\Livewire\Attributes\On('confirm-delete')]
    public function handleConfirmDelete($productId)
    {
        $this->deletingProductId = $productId;
        $this->confirmDelete();
    }

    /**
     * تبديل حالة نشاط المنتج
     */
    public function toggleProductActive($productId)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        try {
            $product = Product::findOrFail($productId);
            $product->update(['is_active' => !$product->is_active]);
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Product status updated successfully'));
            $this->dispatch('refresh-products');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating product status'));
        }
    }

    #[\Livewire\Attributes\On('open-units-modal')]
    public function openUnitsModal($productId)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        $product = Product::with('units')->find($productId);
        
        if (!$product) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Product not found'));
            return;
        }

        $this->managingProductId = $productId;
        $this->units = $product->units->map(function($unit) {
            return [
                'id' => $unit->id,
                'name' => $unit->unit_name,
                'conversion_factor' => $unit->conversion_factor,
                'is_default' => $unit->is_default,
            ];
        })->toArray();

        $this->showUnitsModal = true;
    }

    #[\Livewire\Attributes\On('open-images-modal')]
    public function openImagesModal($productId)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        $product = Product::with('images')->find($productId);
        
        if (!$product) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Product not found'));
            return;
        }

        $this->managingProductId = $productId;
        $this->existingImages = $product->images->map(function($img) {
            return [
                'id' => $img->id,
                'path' => $img->image_path,
                'url' => asset('storage/' . $img->image_path)
            ];
        })->toArray();

        $this->showImagesModal = true;
    }

    #[\Livewire\Attributes\On('open-prices-modal')]
    public function openPricesModal($productId)
    {
        abort_if(!auth()->user()->can('products_edit'), 403);
        $product = Product::with('prices')->find($productId);
        
        if (!$product) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Product not found'));
            return;
        }

        $this->managingProductId = $productId;
        
        // Load latest price
        $latestPrice = $product->prices()->latest()->first();
        if ($latestPrice) {
            $this->cost_price = $latestPrice->cost_price;
            $this->base_price = $latestPrice->base_price;
        } else {
            $this->cost_price = null;
            $this->base_price = null;
        }

        $this->showPricesModal = true;
    }

    /**
     * Validation Methods
     */
    public function validateName()
    {
        $this->validateOnly('name');
    }

    public function validateCategory()
    {
        $this->validateOnly('category_id');
    }

    public function validateWarehouse()
    {
        $this->validateOnly('warehouse_id');
    }


    /**
     * Auto-generate slug from name
     */
    public function updatedName($value)
    {
        if (empty($this->slug) || $this->slug === Str::slug($this->name)) {
            $this->slug = Str::slug($value);
        }
    }

    /**
     * Modal Management
     */
    public function showAddModal()
    {
        abort_if(!auth()->user()->can('products_create'), 403);
        $this->resetForm();
        $this->editingProductId = null;
        $this->showCreateModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showUnitsModal = false;
        $this->showImagesModal = false;
        $this->showPricesModal = false;
        $this->resetForm();
    }

    /**
     * CRUD Operations
     */
    public function saveProduct()
    {
        try {
            if ($this->editingProductId) {
                abort_if(!auth()->user()->can('products_edit'), 403);
            } else {
                abort_if(!auth()->user()->can('products_create'), 403);
            }
            $this->validate();

            $data = [
                'category_id' => $this->category_id,
                'warehouse_id' => $this->warehouse_id,
                'profit_margin_tier_id' => $this->profit_margin_tier_id,
                'name' => trim($this->name),
                'slug' => $this->slug ?: Str::slug($this->name),
                'sku' => $this->sku ?: null,
                'barcode' => $this->barcode ?: null,
                'description' => $this->description ?: null,
                'base_unit' => $this->base_unit,
                'weight' => $this->weight,
                'min_stock_level' => $this->min_stock ?? 0,
                'max_stock_level' => $this->max_stock,
                'is_active' => $this->is_active,
                'is_featured' => $this->is_featured,
            ];

            if ($this->editingProductId) {
                // Update
                $product = Product::findOrFail($this->editingProductId);
                $this->getProductService()->update($product, $data);
                
                // Handle images if uploaded
                if (!empty($this->images)) {
                    $this->getProductService()->addImages($product, $this->images);
                }

                // Check if price changed
                $costPrice = (float)($this->cost_price ?: 0);
                $basePrice = (float)($this->base_price ?: 0);
                
                $currentCost = round((float)$product->current_cost_price, 2);
                $currentBase = round((float)$product->current_base_price, 2);
                $newCost = round($costPrice, 2);
                $newBase = round($basePrice, 2);

                if ($currentCost !== $newCost || $currentBase !== $newBase) {
                    $this->getProductService()->updatePrice($product, $costPrice, $basePrice);
                }

                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Product updated successfully'));
            } else {
                // Create
                $product = $this->getProductService()->create($data);
                
                // Handle images
                if (!empty($this->images)) {
                    $this->getProductService()->addImages($product, $this->images);
                }

                // Add initial price if provided
                if ($this->cost_price || $this->base_price) {
                    $this->getProductService()->updatePrice($product, $this->cost_price ?? 0,
                        $this->base_price ?? 0);
                }

                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Product created successfully'));
            }

            $this->closeModal();
            $this->dispatch('refresh-products');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors will be displayed automatically
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('An error occurred: ') . $e->getMessage());
        }
    }

    public function confirmDelete()
    {
        abort_if(!auth()->user()->can('products_delete'), 403);
        try {
            if (!$this->deletingProductId) {
                throw new \Exception(__('Product ID is missing'));
            }

            $product = Product::findOrFail($this->deletingProductId);
            $this->getProductService()->delete($product);
            
            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Product deleted successfully'));
            $this->resetDelete();
            $this->dispatch('refresh-products');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error deleting product: ') . $e->getMessage());
        }
    }

    /**
     * Units Management
     */
    public function addUnit()
    {
        $this->validate([
            'unit_name' => 'required|string|max:50',
            'unit_conversion_factor' => 'required|numeric|min:0.01',
        ]);

        if ($this->editingUnitIndex !== null) {
            // Update existing unit
            $this->units[$this->editingUnitIndex] = [
                'id' => $this->units[$this->editingUnitIndex]['id'] ?? null,
                'name' => $this->unit_name,
                'conversion_factor' => $this->unit_conversion_factor,
                'is_default' => $this->unit_is_default,
            ];
        } else {
            // Add new unit
            $this->units[] = [
                'id' => null,
                'name' => $this->unit_name,
                'conversion_factor' => $this->unit_conversion_factor,
                'is_default' => $this->unit_is_default,
            ];
        }

        // Reset unit form
        $this->unit_name = '';
        $this->unit_conversion_factor = 1;
        $this->unit_is_default = false;
        $this->editingUnitIndex = null;
    }

    public function editUnit($index)
    {
        $this->editingUnitIndex = $index;
        $this->unit_name = $this->units[$index]['name'];
        $this->unit_conversion_factor = $this->units[$index]['conversion_factor'];
        $this->unit_is_default = $this->units[$index]['is_default'];
    }

    public function deleteUnit($index)
    {
        unset($this->units[$index]);
        $this->units = array_values($this->units); // Re-index array
    }

    public function setDefaultUnit($index)
    {
        foreach ($this->units as $key => $unit) {
            $this->units[$key]['is_default'] = ($key === $index);
        }
    }

    public function saveUnits()
    {
        try {
            if (!$this->managingProductId) {
                throw new \Exception(__('Product ID is missing'));
            }

            $product = Product::findOrFail($this->managingProductId);
            
            // Delete all existing units
            $product->units()->delete();
            
            // Add new units
            foreach ($this->units as $unit) {
                ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_name' => $unit['name'],
                    'conversion_factor' => $unit['conversion_factor'],
                    'is_default' => $unit['is_default'] ?? false,
                ]);
            }

            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Units updated successfully'));
            $this->closeModal();
            $this->dispatch('refresh-products');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating units: ') . $e->getMessage());
        }
    }

    /**
     * Images Management
     */
    public function deleteExistingImage($index)
    {
        try {
            $image = $this->existingImages[$index];
            $productImage = ProductImage::find($image['id']);
            
            if ($productImage) {
                // Delete file from storage
                \Storage::disk('public')->delete($productImage->image_path);
                $productImage->delete();
                
                unset($this->existingImages[$index]);
                $this->existingImages = array_values($this->existingImages);
                
                $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Image deleted successfully'));
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error deleting image'));
        }
    }

    public function saveImages()
    {
        try {
            if (!$this->managingProductId) {
                throw new \Exception(__('Product ID is missing'));
            }

            $product = Product::findOrFail($this->managingProductId);
            
            if (!empty($this->images)) {
                $this->getProductService()->addImages($product, $this->images);
            }

            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Images uploaded successfully'));
            $this->closeModal();
            $this->dispatch('refresh-products');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error uploading images: ') . $e->getMessage());
        }
    }

    /**
     * Prices Management
     */
    public function savePrices()
    {
        $this->validate([
            'cost_price' => 'nullable|numeric|min:0',
            'base_price' => 'nullable|numeric|min:0',
        ]);

        try {
            if (!$this->managingProductId) {
                throw new \Exception(__('Product ID is missing'));
            }

            $product = Product::findOrFail($this->managingProductId);
            
            $this->getProductService()->updatePrice($product, $this->cost_price ?? 0,
                $this->base_price ?? 0);

            $this->dispatch('notify', type: 'success', title: __('Success'), msg: __('Prices updated successfully'));
            $this->closeModal();
            $this->dispatch('refresh-products');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', title: __('Error'), msg: __('Error updating prices: ') . $e->getMessage());
        }
    }

    /**
     * Reset Methods
     */
    private function resetForm()
    {
        $this->name = '';
        $this->slug = '';
        $this->sku = '';
        $this->barcode = '';
        $this->category_id = null;
        $this->warehouse_id = null;
        $this->profit_margin_tier_id = null;
        $this->description = '';
        $this->base_unit = 'قطعة';
        $this->weight = null;
        $this->min_stock = 0;
        $this->max_stock = null;
        $this->is_active = true;
        $this->is_featured = false;
        $this->images = [];
        $this->existingImages = [];
        $this->cost_price = null;
        $this->base_price = null;
        $this->editingProductId = null;
        $this->managingProductId = null;
        $this->units = [];
        $this->unit_name = '';
        $this->unit_conversion_factor = 1;
        $this->unit_is_default = false;
        $this->editingUnitIndex = null;
    }

    private function resetDelete()
    {
        $this->deletingProductId = null;
        $this->deletingProductName = '';
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $profitMarginTiers = ProfitMarginTier::where('is_active', true)->orderBy('priority', 'desc')->get();

        return view('livewire.dashboard.products.products', compact('categories', 'warehouses', 'profitMarginTiers'));
    }
}
