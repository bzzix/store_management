<div>
    @push('scripts')
        <script>
            // عرض الإشعارات
            window.addEventListener('notify', event => {
                window["iziToast"][event.detail.type]({
                    title: `${event.detail.title}`,
                    message: `${event.detail.msg}`,
                    position: 'topLeft',
                    rtl: true,
                });
            });

            // تأكيد حذف المنتج
            window.addEventListener('open-delete-product-modal', event => {
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
                        ['<button><b>{{ __("Delete") }}</b></button>', function (instance, toast) {
                            @this.confirmDeleteProduct()
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }, true],
                        ['<button>{{ __("Cancel") }}</button>', function (instance, toast) {
                            @this.closeDeleteModal()
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ]
                });
            });
        </script>
    @endpush

    <div class="row">
        <div class="col-md-3">
            <div class="btn-group-vertical w-100" role="group">
                <input type="radio" class="btn-check" wire:model.live="products_tab" id="btn-products-1" autocomplete="off" value="categories">
                <label for="btn-products-1" type="button" class="btn d-block text-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tags" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7v4a1 1 0 0 0 1 1h12a1 1 0 0 0 1 -1v-4a1 1 0 0 0 -1 -1h-12a1 1 0 0 0 -1 1z" /><path d="M16 3a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-4a2 2 0 0 1 2 -2h12z" /></svg>
                    {{ __('Categories') }}
                </label>
                <input type="radio" class="btn-check" wire:model.live="products_tab" id="btn-products-2" autocomplete="off" value="products">
                <label for="btn-products-2" type="button" class="btn d-block text-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
                    {{ __('Products') }}
                </label>
            </div>
        </div>

        <div class="col-md-9">
            @if ($products_tab == 'categories')
                <livewire:dashboard.products.categories />
            @elseif ($products_tab == 'products')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('Products') }}</h3>
                        <button wire:click="showAddProductModal" class="btn btn-primary btn-sm" wire:loading.attr="disabled" wire:loading.class="opacity-50">
                            <span wire:loading.remove wire:target="showAddProductModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                {{ __('Add Product') }}
                            </span>
                            <span wire:loading wire:target="showAddProductModal">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Loading...') }}
                            </span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <livewire:dashboard.products.products-data-table />
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- نموذج الإضافة/التعديل - المنتجات -->
    @if ($showCreateProductModal || $showEditProductModal)
        <div class="modal modal-blur fade show" style="display: block;" role="dialog">
            <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if ($showEditProductModal)
                                {{ __('Edit Product') }}
                            @else
                                {{ __('Add New Product') }}
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeProductModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- الاسم -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Product Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('productName') is-invalid @enderror" wire:model.blur="productName" placeholder="{{ __('Product name') }}">
                                @error('productName')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- SKU -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('SKU') }}</label>
                                <input type="text" class="form-control" wire:model.blur="productSku" placeholder="{{ __('e.g., PRD-001') }}">
                            </div>

                            <!-- التصنيف -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
                                <select class="form-control @error('productCategoryId') is-invalid @enderror" wire:model.blur="productCategoryId">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('productCategoryId')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- حالة النشاط -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="productIsActive" id="productIsActive">
                                    <label class="form-check-label" for="productIsActive">
                                        {{ __('Active') }}
                                    </label>
                                </div>
                            </div>

                            <!-- الوصف -->
                            <div class="col-12 mb-3">
                                <label class="form-label">{{ __('Description') }}</label>
                                <textarea class="form-control" wire:model.blur="productDescription" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeProductModal" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveProduct" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:target="saveProduct">
                            <span wire:loading.remove wire:target="saveProduct">
                                {{ __('Save') }}
                            </span>
                            <span wire:loading wire:target="saveProduct">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Saving...') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- حدث تأكيد الحذف - المنتجات -->
    @if ($showDeleteProductModal)
        @push('scripts')
            <script>
                window.dispatchEvent(new CustomEvent('open-delete-product-modal', {
                    detail: {
                        productId: {{ $deletingProductId ?? 0 }},
                        productName: '{{ $deletingProductName }}'
                    }
                }));
            </script>
        @endpush
    @endif
</div>
