<div>
    @push('js')
        <script>
            // تأكيد حذف المنتج
            window.addEventListener('delete-product', event => {
                const { productId, productName } = event.detail;
                
                window["iziToast"]['question']({
                    message: `{{ __('Do you really want to delete this product') }}: <strong>${productName}</strong>?`,
                    rtl: true,
                    timeout: 20000,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question-product',
                    zindex: 999999999,
                    position: 'center',
                    buttons: [
                        ['<button class="bg-red-600 text-white px-4 py-1.5 rounded-lg font-bold text-xs ml-2">{{ __("Delete") }}</button>', function (instance, toast) {
                            Livewire.find('{{ $this->getId() }}').deletingProductId = productId;
                            Livewire.find('{{ $this->getId() }}').confirmDelete();
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }, true],
                        ['<button class="bg-surface-200 text-surface-700 px-4 py-1.5 rounded-lg font-bold text-xs">{{ __("Cancel") }}</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ]
                });
            });

            window.addEventListener('edit-product', event => {
                Livewire.find('{{ $this->getId() }}').openEditModal(event.detail.productId);
            });

            window.addEventListener('toggle-product-status', event => {
                Livewire.find('{{ $this->getId() }}').toggleProductActive(event.detail.productId);
            });
        </script>
    @endpush

    {{-- Products Section --}}
    <div class="bg-white border border-surface-200/60 rounded-2xl shadow-soft overflow-hidden">
        <div class="p-6 border-b border-surface-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-surface-900">{{ __('Products') }}</h3>
                <p class="text-sm text-surface-500 mt-0.5">{{ __('Manage and monitor your store inventory.') }}</p>
            </div>
            
            @can('products_create')
                <button wire:click="showAddModal" 
                    class="flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="showAddModal" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" />
                        </svg>
                        {{ __('Add Product') }}
                    </span>
                    <span wire:loading wire:target="showAddModal" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Loading...') }}
                    </span>
                </button>
            @endcan
        </div>

        {{-- Custom DataTable Container --}}
        <div class="overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <livewire:dashboard.products.products-data-table />
            </div>
        </div>
    </div>

    {{-- Create/Edit Product Modal --}}
    @if ($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-surface-900/40 backdrop-blur-sm" wire:click="closeModal"></div>
            
            <!-- Modal Content -->
            <div class="bg-white w-full max-w-4xl max-h-[90vh] rounded-3xl shadow-2xl relative z-10 flex flex-col overflow-hidden border border-white/20 glass-panel">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-surface-100 flex items-center justify-between">
                    <div>
                        <h5 class="text-xl font-bold text-surface-900">
                            @if ($showEditModal)
                                {{ __('Edit Product') }}
                            @else
                                {{ __('Add New Product') }}
                            @endif
                        </h5>
                        <p class="text-sm text-surface-500 mt-1">{{ __('Please fill in the product details below.') }}</p>
                    </div>
                    <button type="button" class="p-2 rounded-xl border border-surface-100 text-surface-400 hover:text-surface-900 hover:bg-surface-50 transition-all" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-8 py-8 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- اسم المنتج --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Product Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('name') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model.blur="name" wire:blur="validateName" placeholder="{{ __('Enter product name') }}">
                            @error('name')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- الرابط المختصر --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Slug') }}</label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('slug') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="slug" placeholder="{{ __('Auto-generated from name') }}">
                            @error('slug')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- SKU & Barcode --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('SKU') }}</label>
                                <input type="text" 
                                    class="w-full bg-surface-50 border {{ $errors->has('sku') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                    wire:model="sku" placeholder="SKU-000">
                                @error('sku')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Barcode') }}</label>
                                <input type="text" 
                                    class="w-full bg-surface-50 border {{ $errors->has('barcode') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                    wire:model="barcode" placeholder="Barcode">
                                @error('barcode')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- التصنيف --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Category') }} <span class="text-red-500">*</span></label>
                            <select 
                                class="w-full bg-surface-50 border {{ $errors->has('category_id') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all appearance-none" 
                                wire:model.blur="category_id" wire:blur="validateCategory">
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- المخزن --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Warehouse') }} <span class="text-red-500">*</span></label>
                            <select 
                                class="w-full bg-surface-50 border {{ $errors->has('warehouse_id') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all appearance-none" 
                                wire:model.blur="warehouse_id" wire:blur="validateWarehouse">
                                <option value="">{{ __('Select Warehouse') }}</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- شريحة الربح --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Profit Margin Tier') }}</label>
                            <select 
                                class="w-full bg-surface-50 border {{ $errors->has('profit_margin_tier_id') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all appearance-none" 
                                wire:model="profit_margin_tier_id">
                                <option value="">{{ __('Select Tier') }}</option>
                                @foreach($profitMarginTiers as $tier)
                                    <option value="{{ $tier->id }}">{{ $tier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- الأسعار --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Cost Price') }}</label>
                                <div class="relative">
                                    <input type="number" 
                                        class="w-full bg-surface-50 border {{ $errors->has('cost_price') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl pl-10 pr-4 py-2.5 text-sm font-display font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                        wire:model="cost_price" placeholder="0.00" step="0.01">
                                    <span class="absolute left-3 top-2.5 text-surface-400 text-xs">ر.س</span>
                                </div>
                                @error('cost_price')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Base Price') }}</label>
                                <div class="relative">
                                    <input type="number" 
                                        class="w-full bg-surface-50 border {{ $errors->has('base_price') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl pl-10 pr-4 py-2.5 text-sm font-display font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                        wire:model="base_price" placeholder="0.00" step="0.01">
                                    <span class="absolute left-3 top-2.5 text-surface-400 text-xs">ر.س</span>
                                </div>
                                @error('base_price')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- الوحدة الأساسية والوزن --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Base Unit') }} <span class="text-red-500">*</span></label>
                                <input type="text" 
                                    class="w-full bg-surface-50 border {{ $errors->has('base_unit') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                    wire:model="base_unit" placeholder="{{ __('e.g., Piece, Kg') }}">
                                @error('base_unit')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Weight') }}</label>
                                <div class="relative">
                                    <input type="number" 
                                        class="w-full bg-surface-50 border {{ $errors->has('weight') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl pr-4 pl-10 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                        wire:model="weight" placeholder="0.00" step="0.01">
                                    <span class="absolute left-3 top-2.5 text-surface-400 text-xs">KG</span>
                                </div>
                                @error('weight')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- حدود المخزون --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Min Stock Level') }}</label>
                                <input type="number" 
                                    class="w-full bg-surface-50 border {{ $errors->has('min_stock') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                    wire:model="min_stock" placeholder="0" step="0.001">
                                @error('min_stock')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Max Stock Level') }}</label>
                                <input type="number" 
                                    class="w-full bg-surface-50 border {{ $errors->has('max_stock') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                    wire:model="max_stock" placeholder="{{ __('Optional') }}" step="0.001">
                                @error('max_stock')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- الوصف --}}
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Description') }}</label>
                            <textarea 
                                class="w-full bg-surface-50 border {{ $errors->has('description') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all min-h-[100px]" 
                                wire:model="description" rows="3" placeholder="{{ __('Product description') }}"></textarea>
                            @error('description')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- الحالة - Toggle --}}
                        <div class="flex items-center justify-between p-4 bg-surface-50 rounded-2xl border border-surface-100">
                             <div>
                                 <p class="text-sm font-bold text-surface-900">{{ __('Status') }}</p>
                                 <p class="text-xs text-surface-500">{{ __('Enable or disable this product in the store.') }}</p>
                             </div>
                             <button type="button" 
                                @click="$wire.set('is_active', !@js($is_active))"
                                :class="@js($is_active) ? 'bg-secondary-500' : 'bg-surface-300'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-offset-2 focus:ring-2 focus:ring-primary-500">
                                <span :class="@js($is_active) ? '-translate-x-6' : '-translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                             </button>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-surface-50 rounded-2xl border border-surface-100">
                             <div>
                                 <p class="text-sm font-bold text-surface-900">{{ __('Featured') }}</p>
                                 <p class="text-xs text-surface-500">{{ __('Mark as a featured product.') }}</p>
                             </div>
                             <button type="button" 
                                @click="$wire.set('is_featured', !@js($is_featured))"
                                :class="@js($is_featured) ? 'bg-primary-500' : 'bg-surface-300'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-offset-2 focus:ring-2 focus:ring-primary-500">
                                <span :class="@js($is_featured) ? '-translate-x-6' : '-translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                             </button>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-6 bg-surface-50 border-t border-surface-100 flex items-center justify-end gap-3">
                    <button type="button" 
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-surface-600 hover:bg-surface-100 transition-all disabled:opacity-50" 
                        wire:click="closeModal" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" 
                        class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50 min-w-[120px]" 
                        wire:click="saveProduct" wire:loading.attr="disabled" wire:target="saveProduct">
                        <span wire:loading.remove wire:target="saveProduct">{{ __('Save') }}</span>
                        <span wire:loading wire:target="saveProduct" class="flex items-center gap-2">
                             <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                             </svg>
                             {{ __('Saving...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Units Modal --}}
    @if ($showUnitsModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
            <div class="absolute inset-0 bg-surface-900/40 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="bg-white w-full max-w-2xl max-h-[90vh] rounded-3xl shadow-2xl relative z-10 flex flex-col overflow-hidden border border-white/20 glass-panel">
                <div class="px-8 py-6 border-b border-surface-100 flex items-center justify-between">
                    <div>
                        <h5 class="text-xl font-bold text-surface-900">{{ __('Manage Units') }}</h5>
                        <p class="text-sm text-surface-500 mt-1">{{ __('Add or edit product units and conversion factors.') }}</p>
                    </div>
                    <button type="button" class="p-2 rounded-xl border border-surface-100 text-surface-400 hover:text-surface-900 hover:bg-surface-50 transition-all" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-8 py-8 overflow-y-auto flex-1 custom-scrollbar">
                    {{-- Add/Edit Unit Form --}}
                    <div class="bg-surface-50 rounded-2xl border border-surface-100 p-6 mb-8">
                        <h4 class="text-sm font-bold text-surface-900 mb-4">{{ $editingUnitIndex !== null ? __('Edit Unit') : __('Add Unit') }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-5 space-y-1.5">
                                <label class="text-xs font-bold text-surface-700">{{ __('Unit Name') }}</label>
                                <input type="text" class="w-full bg-white border border-surface-200 rounded-xl px-4 py-2 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" wire:model="unit_name" placeholder="{{ __('e.g., Box, Pack') }}">
                            </div>
                            <div class="sm:col-span-4 space-y-1.5">
                                <label class="text-xs font-bold text-surface-700">{{ __('Factor') }}</label>
                                <input type="number" class="w-full bg-white border border-surface-200 rounded-xl px-4 py-2 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" wire:model="unit_conversion_factor" placeholder="1" step="0.01">
                            </div>
                            <div class="sm:col-span-3 flex items-end">
                                <button type="button" class="w-full bg-primary-600 hover:bg-primary-700 text-white h-10 rounded-xl text-xs font-bold transition-all active:scale-95 shadow-sm shadow-primary-500/10" wire:click="addUnit">
                                    {{ $editingUnitIndex !== null ? __('Update') : __('Add') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Units List --}}
                    @if(!empty($units))
                        <div class="overflow-hidden border border-surface-100 rounded-2xl">
                            <table class="w-full text-right">
                                <thead class="bg-surface-50 border-b border-surface-100">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-bold text-surface-500">{{ __('Unit') }}</th>
                                        <th class="px-4 py-3 text-xs font-bold text-surface-500 text-center">{{ __('Factor') }}</th>
                                        <th class="px-4 py-3 text-xs font-bold text-surface-500 text-center">{{ __('Default') }}</th>
                                        <th class="px-4 py-3 text-xs font-bold text-surface-500 text-left">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-50">
                                    @foreach($units as $index => $unit)
                                        <tr class="hover:bg-surface-50/50 transition-all">
                                            <td class="px-4 py-4 text-sm font-bold text-surface-900">{{ $unit['name'] }}</td>
                                            <td class="px-4 py-4 text-sm font-medium text-surface-600 text-center">{{ $unit['conversion_factor'] }}</td>
                                            <td class="px-4 py-4 text-center">
                                                @if($unit['is_default'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded-lg bg-secondary-100 text-secondary-700 text-[10px] font-bold uppercase tracking-wider">{{ __('Default') }}</span>
                                                @else
                                                    <button type="button" class="text-surface-400 hover:text-primary-600 text-[10px] font-bold transition-all" wire:click="setDefaultUnit({{ $index }})">
                                                        {{ __('Set Default') }}
                                                    </button>
                                                @endif
                                            </td>
                                            <td class="px-4 py-4 text-left">
                                                <div class="flex items-center justify-end gap-2">
                                                    <button type="button" class="p-1.5 text-surface-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all" wire:click="editUnit({{ $index }})">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                                    </button>
                                                    <button type="button" class="p-1.5 text-surface-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" wire:click="deleteUnit({{ $index }})">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="py-12 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-surface-50 rounded-2xl flex items-center justify-center mb-4 border border-surface-100">
                                <svg class="w-8 h-8 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <p class="text-surface-400 text-sm">{{ __('No units added yet') }}</p>
                        </div>
                    @endif
                </div>

                <div class="px-8 py-6 bg-surface-50 border-t border-surface-100 flex items-center justify-end gap-3">
                    <button type="button" class="px-6 py-2.5 rounded-xl text-sm font-bold text-surface-600 hover:bg-surface-100 transition-all" wire:click="closeModal">
                        {{ __('Close') }}
                    </button>
                    <button type="button" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50" wire:click="saveUnits" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveUnits">{{ __('Save All Units') }}</span>
                        <span wire:loading wire:target="saveUnits" class="flex items-center gap-2">
                             <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                             {{ __('Saving...') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Images Modal --}}
    @if ($showImagesModal)
         <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
            <div class="absolute inset-0 bg-surface-900/40 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="bg-white w-full max-w-3xl max-h-[90vh] rounded-3xl shadow-2xl relative z-10 flex flex-col overflow-hidden border border-white/20 glass-panel">
                <div class="px-8 py-6 border-b border-surface-100 flex items-center justify-between">
                    <div>
                        <h5 class="text-xl font-bold text-surface-900">{{ __('Product Gallery') }}</h5>
                        <p class="text-sm text-surface-500 mt-1">{{ __('Manage your product photos.') }}</p>
                    </div>
                    <button type="button" class="p-2 rounded-xl border border-surface-100 text-surface-400 hover:text-surface-900 hover:bg-surface-50 transition-all" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-8 py-8 overflow-y-auto flex-1 custom-scrollbar">
                    {{-- Upload Area --}}
                    <div class="mb-8 p-8 border-2 border-dashed border-surface-200 rounded-3xl bg-surface-50/50 flex flex-col items-center justify-center text-center group hover:border-primary-400 transition-all">
                        <div class="w-16 h-16 bg-white rounded-2xl shadow-soft flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-surface-900 font-bold mb-1">{{ __('Click to upload images') }}</p>
                        <p class="text-surface-500 text-xs mb-4">{{ __('Max size: 2MB per image') }}</p>
                        <input type="file" class="hidden" id="image-upload" wire:model="images" multiple accept="image/*">
                        <label for="image-upload" class="bg-white border border-surface-200 text-surface-700 px-6 py-2 rounded-xl text-sm font-bold shadow-soft hover:bg-surface-50 transition-all cursor-pointer">
                            {{ __('Choose Files') }}
                        </label>
                        <div wire:loading wire:target="images" class="mt-4">
                            <div class="flex items-center gap-2 text-primary-600 font-bold text-xs uppercase tracking-widest">
                                <svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                {{ __('Uploading...') }}
                            </div>
                        </div>
                    </div>

                    {{-- Existing Images Grid --}}
                    @if(!empty($existingImages))
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($existingImages as $index => $image)
                                <div class="relative group aspect-square rounded-2xl overflow-hidden border border-surface-100">
                                    <img src="{{ $image['url'] }}" alt="Product" class="w-full h-full object-cover grayscale-[20%] group-hover:grayscale-0 transition-all group-hover:scale-110">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-4">
                                        <button type="button" class="w-10 h-10 bg-red-600 text-white rounded-xl shadow-lg flex items-center justify-center hover:scale-110 active:scale-95 transition-all" wire:click="deleteExistingImage({{ $index }})">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-12 flex flex-col items-center justify-center text-center">
                            <div class="w-16 h-16 bg-surface-50 rounded-2xl flex items-center justify-center mb-4 border border-surface-100">
                                <svg class="w-8 h-8 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-surface-400 text-sm">{{ __('No product images yet.') }}</p>
                        </div>
                    @endif
                </div>

                <div class="px-8 py-6 bg-surface-50 border-t border-surface-100 flex items-center justify-end gap-3">
                    <button type="button" class="px-6 py-2.5 rounded-xl text-sm font-bold text-surface-600 hover:bg-surface-100 transition-all" wire:click="closeModal">
                        {{ __('Close') }}
                    </button>
                    <button type="button" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50" wire:click="saveImages" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveImages">{{ __('Save Changes') }}</span>
                        <span wire:loading wire:target="saveImages" class="flex items-center gap-2">
                             <svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                             {{ __('Saving...') }}
                        </span>
                    </button>
                </div>
            </div>
         </div>
    @endif

    {{-- Prices Modal --}}
    @if ($showPricesModal)
         <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
            <div class="absolute inset-0 bg-surface-900/40 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="bg-white w-full max-w-xl max-h-[90vh] rounded-3xl shadow-2xl relative z-10 flex flex-col overflow-hidden border border-white/20 glass-panel">
                <div class="px-8 py-6 border-b border-surface-100 flex items-center justify-between">
                    <div>
                        <h5 class="text-xl font-bold text-surface-900">{{ __('Price Management') }}</h5>
                        <p class="text-sm text-surface-500 mt-1">{{ __('Update cost and base selling prices.') }}</p>
                    </div>
                    <button type="button" class="p-2 rounded-xl border border-surface-100 text-surface-400 hover:text-surface-900 hover:bg-surface-50 transition-all" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="px-8 py-8 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Cost Price') }}</label>
                            <div class="relative">
                                <input type="number" class="w-full bg-surface-50 border border-surface-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-display font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" wire:model="cost_price" placeholder="0.00" step="0.01">
                                <span class="absolute left-3 top-2.5 text-surface-400 text-xs">ر.س</span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Base Price') }}</label>
                            <div class="relative">
                                <input type="number" class="w-full bg-surface-50 border border-surface-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-display font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" wire:model="base_price" placeholder="0.00" step="0.01">
                                <span class="absolute left-3 top-2.5 text-surface-400 text-xs">ر.س</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 flex gap-4">
                        <div class="w-10 h-10 bg-white rounded-xl shadow-soft flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-900 mb-1">{{ __('Price History') }}</p>
                            <p class="text-xs text-blue-700/80 leading-relaxed">{{ __('Any changes will be automatically logged in the price history for tracking and auditing purposes.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-surface-50 border-t border-surface-100 flex items-center justify-end gap-3">
                    <button type="button" class="px-6 py-2.5 rounded-xl text-sm font-bold text-surface-600 hover:bg-surface-100 transition-all" wire:click="closeModal">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50" wire:click="savePrices" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="savePrices">{{ __('Update prices') }}</span>
                        <span wire:loading wire:target="savePrices" class="flex items-center gap-2">
                             <svg class="animate-spin h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                             {{ __('Updating...') }}
                        </span>
                    </button>
                </div>
            </div>
         </div>
    @endif

</div>

