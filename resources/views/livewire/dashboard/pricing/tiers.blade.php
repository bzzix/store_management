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

            // تأكيد حذف الشريحة
            window.addEventListener('open-delete-modal', event => {
                const { tierId, tierName } = event.detail;
                
                window["iziToast"]['question']({
                    message: `{{ __('Do you really want to delete this pricing tier') }}: <strong>${tierName}</strong>?`,
                    rtl: true,
                    timeout: 20000,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question',
                    zindex: 999999999,
                    position: 'center',
                    buttons: [
                        ['<button><b>{{ __("Delete") }}</b></button>', function (instance, toast) {
                            @this.handleConfirmDelete(tierId)
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }, true],
                        ['<button>{{ __("Cancel") }}</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ]
                });
            });

            // تأكيد حذف طريقة البيع
            window.addEventListener('open-delete-method-modal', event => {
                const { methodId, methodName } = event.detail;
                
                window["iziToast"]['question']({
                    message: `{{ __('Do you really want to delete this sale method') }}: <strong>${methodName}</strong>?`,
                    rtl: true,
                    timeout: 20000,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question-method',
                    zindex: 999999999,
                    position: 'center',
                    buttons: [
                        ['<button><b>{{ __("Delete") }}</b></button>', function (instance, toast) {
                            @this.confirmDeleteMethod()
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }, true],
                        ['<button>{{ __("Cancel") }}</button>', function (instance, toast) {
                            @this.closeDeleteMethodModal()
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
                <input type="radio" class="btn-check" wire:model.live="pricing_tab" id="btn-radio-vertical-dropdown-1" autocomplete="off" value="pricing_preview">
                <label for="btn-radio-vertical-dropdown-1" type="button" class="btn d-block text-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                    {{ __('Pricing Preview') }}
                </label>
                <input type="radio" class="btn-check" wire:model.live="pricing_tab" id="btn-radio-vertical-dropdown-2" autocomplete="off" value="sale_methods">
                <label for="btn-radio-vertical-dropdown-2" type="button" class="btn d-block text-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" /><path d="M9 15a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /><path d="M4 12h3m4 0h3" /></svg>
                    {{ __('Sale Methods') }}
                </label>
                <input type="radio" class="btn-check" wire:model.live="pricing_tab" id="btn-radio-vertical-dropdown-3" autocomplete="off" value="pricing_tiers">
                <label for="btn-radio-vertical-dropdown-3" type="button" class="btn d-block text-start">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layers" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
                    {{ __('Pricing Tiers') }}
                </label>

            </div>
        </div>

        <div class="col-md-9">
            @if ($pricing_tab == 'pricing_preview')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('Pricing Tiers') }}</h3>
                        <button wire:click="showAddModal" class="btn btn-primary btn-sm" wire:loading.attr="disabled" wire:loading.class="opacity-50">
                            <span wire:loading.remove wire:target="showAddModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                {{ __('Add Tier') }}
                            </span>
                            <span wire:loading wire:target="showAddModal">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Loading...') }}
                            </span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <livewire:dashboard.pricing.profit-margin-data-table />
                    </div>
                </div>
            @elseif ($pricing_tab == 'sale_methods')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('Sale Methods') }}</h3>
                        <button wire:click="showAddMethodModal" class="btn btn-primary btn-sm" wire:loading.attr="disabled" wire:loading.class="opacity-50">
                            <span wire:loading.remove wire:target="showAddMethodModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                {{ __('Add Method') }}
                            </span>
                            <span wire:loading wire:target="showAddMethodModal">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Loading...') }}
                            </span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <livewire:dashboard.pricing.sale-methodes-data-table />
                    </div>
                </div>
            @elseif ($pricing_tab == 'pricing_tiers')
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ __('Pricing Tiers') }}</h3>
                        <button wire:click="showAddModal" class="btn btn-primary btn-sm" wire:loading.attr="disabled" wire:loading.class="opacity-50">
                            <span wire:loading.remove wire:target="showAddModal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                                {{ __('Add Tier') }}
                            </span>
                            <span wire:loading wire:target="showAddModal">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Loading...') }}
                            </span>
                        </button>
                    </div>
                    <div class="table-responsive">
                        <livewire:dashboard.pricing.tiers-data-table />
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- نموذج الإضافة/التعديل -->
    @if ($showCreateModal || $showEditModal)
        <div class="modal modal-blur fade show" style="display: block;" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if ($showEditModal)
                                {{ __('Edit Pricing Tier') }}
                            @else
                                {{ __('Add New Pricing Tier') }}
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- حقل الاسم -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Tier Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.blur="name" wire:blur="validateName" placeholder="{{ __('e.g., Standard, Premium, VIP') }}">
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- حقل القيمة الدنيا والعليا -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Min Value') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('minValue') is-invalid @enderror" wire:model.blur="minValue" wire:blur="validateMinValue" placeholder="0" step="0.01" min="0">
                                @error('minValue')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Max Value') }}</label>
                                <input type="number" class="form-control @error('maxValue') is-invalid @enderror" wire:model.blur="maxValue" wire:blur="validateMaxValue" placeholder="{{ __('Leave empty for unlimited') }}" step="0.01" min="0">
                                @error('maxValue')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- حقل الأولوية والحالة -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Priority') }}</label>
                                <input type="number" class="form-control @error('priority') is-invalid @enderror" wire:model.blur="priority" wire:blur="validatePriority" placeholder="0">
                                @error('priority')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="isActive" id="isActive">
                                    <label class="form-check-label" for="isActive">
                                        {{ __('Active') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveTier" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:target="saveTier">
                            <span wire:loading.remove wire:target="saveTier">
                                {{ __('Save') }}
                            </span>
                            <span wire:loading wire:target="saveTier">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Saving...') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @endif
    
    <!-- نموذج الإضافة/التعديل - طرق البيع -->
    @if ($showCreateMethodModal || $showEditMethodModal)
        <div class="modal modal-blur fade show" style="display: block;" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @if ($showEditMethodModal)
                                {{ __('Edit Sale Method') }}
                            @else
                                {{ __('Add New Sale Method') }}
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeMethodModal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- حقل الاسم -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Method Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('methodName') is-invalid @enderror" wire:model.blur="methodName" placeholder="{{ __('e.g., Cash, Credit Card, Check') }}">
                            @error('methodName')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- حقل الرمز -->
                        <div class="mb-3">
                            <label class="form-label">{{ __('Code') }}</label>
                            <input type="text" class="form-control" wire:model.blur="methodCode" placeholder="{{ __('e.g., CSH, CC, CHK') }}" maxlength="50">
                        </div>

                        <!-- حقل الأولوية والحالة -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Priority') }}</label>
                                <input type="number" class="form-control" wire:model.blur="methodPriority" placeholder="0" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">{{ __('Status') }}</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="methodIsActive" id="methodIsActive">
                                    <label class="form-check-label" for="methodIsActive">
                                        {{ __('Active') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeMethodModal" wire:loading.attr="disabled">
                            {{ __('Cancel') }}
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="saveSaleMethod" wire:loading.attr="disabled" wire:loading.class="opacity-50" wire:target="saveSaleMethod">
                            <span wire:loading.remove wire:target="saveSaleMethod">
                                {{ __('Save') }}
                            </span>
                            <span wire:loading wire:target="saveSaleMethod">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                {{ __('Saving...') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- حدث تأكيد الحذف - طرق البيع -->
    @if ($showDeleteMethodModal)
        @push('scripts')
            <script>
                window.dispatchEvent(new CustomEvent('open-delete-method-modal', {
                    detail: {
                        methodId: {{ $deletingMethodId ?? 0 }},
                        methodName: '{{ $deletingMethodName }}'
                    }
                }));
            </script>
        @endpush
    @endif
</div>
