<div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                // عرض الإشعارات
                Livewire.on('notify', (data) => {
                    const { type = 'info', message = 'Operation completed' } = data[0] || data;
                    
                    const options = {
                        title: type === 'success' ? '{{ __("Success") }}' : (type === 'error' ? '{{ __("Error") }}' : '{{ __("Info") }}'),
                        message: message,
                        position: 'topRight',
                        timeout: 5000,
                        rtl: true,
                    };

                    if (type === 'success') {
                        iziToast.success(options);
                    } else if (type === 'error') {
                        iziToast.error(options);
                    } else {
                        iziToast.info(options);
                    }
                });

                // تأكيد الحذف بـ izitoast
                window.addEventListener('open-delete-modal', event => {
                    const { supplierId, supplierName } = event.detail;
                    
                    iziToast.question({
                        timeout: 20000,
                        layout: 2,
                        title: '{{ __("Delete") }}',
                        message: `{{ __('Do you really want to delete this supplier') }}: <strong>${supplierName}</strong>?`,
                        rtl: true,
                        overlay: true,
                        displayMode: 'once',
                        id: 'delete-question',
                        zindex: 999999999,
                        position: 'center',
                        buttons: [
                            ['<button><b>{{ __("Delete") }}</b></button>', function (instance, toast) {
                                Livewire.find('{{ $this->getId() }}').handleConfirmDelete(supplierId);
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            }, true],
                            ['<button>{{ __("Cancel") }}</button>', function (instance, toast) {
                                instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                            }],
                        ]
                    });
                });
            });
        </script>
    @endpush

    <div class="bg-white border border-surface-200/60 rounded-2xl shadow-soft overflow-hidden">
        <div class="p-6 border-b border-surface-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-surface-900">{{ __('Suppliers Management') }}</h3>
                <p class="text-sm text-surface-500 mt-0.5">{{ __('Manage and monitor your suppliers and their balances.') }}</p>
            </div>
            
            @can('suppliers_add')
                <button wire:click="showAddModal" 
                    wire:loading.attr="disabled"
                    class="flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove wire:target="showAddModal" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" />
                        </svg>
                        {{ __('Add New Supplier') }}
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

        <div class="overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <livewire:dashboard.suppliers.suppliers-data-table />
            </div>
        </div>
    </div>

    {{-- Create/Edit Supplier Modal --}}
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
                                {{ __('Edit Supplier') }}
                            @else
                                {{ __('Add New Supplier') }}
                            @endif
                        </h5>
                        <p class="text-sm text-surface-500 mt-1">{{ __('Please fill in the supplier details below.') }}</p>
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
                        {{-- اسم المورد --}}
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-sm font-bold text-surface-700">{{ __('Supplier Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('name') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="name" placeholder="{{ __('Enter supplier name') }}">
                            @error('name')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- اسم الشركة --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Company Name') }}</label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('company_name') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="company_name" placeholder="{{ __('Company Name') }}">
                            @error('company_name')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Email') }}</label>
                            <input type="email" 
                                class="w-full bg-surface-50 border {{ $errors->has('email') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="email" placeholder="{{ __('Supplier Email') }}">
                            @error('email')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- الهاتف الأساسي --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Primary Phone') }} <span class="text-red-500">*</span></label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('phone') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="phone" placeholder="{{ __('e.g., 01xxxxxxxxx') }}">
                            @error('phone')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- الهاتف الثانوي --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Secondary Phone') }}</label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('phone_2') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="phone_2" placeholder="{{ __('e.g., 01xxxxxxxxx') }}">
                            @error('phone_2')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- العنوان --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Address') }}</label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('address') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="address" placeholder="{{ __('Full Address') }}">
                            @error('address')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- المدينة --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('City') }}</label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('city') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="city" placeholder="{{ __('City') }}">
                            @error('city')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- الرقم الضريبي --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Tax Number') }}</label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('tax_number') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="tax_number" placeholder="{{ __('Tax Registration No.') }}">
                            @error('tax_number')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- حد الائتمان --}}
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Credit Limit') }}</label>
                            <div class="relative">
                                <input type="number" 
                                    class="w-full bg-surface-50 border {{ $errors->has('credit_limit') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl pl-10 pr-4 py-2.5 text-sm font-display font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                    wire:model="credit_limit" placeholder="0.00" step="0.01">
                                <span class="absolute left-3 top-2.5 text-surface-400 text-xs">ج.م</span>
                            </div>
                        </div>

                        {{-- الرصيد الافتتاحي --}}
                        <div class="space-y-1.5 flex-1">
                            <label class="text-sm font-bold text-surface-700">{{ __('Initial Balance') }}</label>
                            <div class="relative">
                                <input type="number" 
                                    class="w-full bg-surface-50 border {{ $errors->has('opening_balance') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl pl-10 pr-4 py-2.5 text-sm font-display font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all disabled:opacity-50 disabled:bg-surface-100" 
                                    wire:model="opening_balance" placeholder="0.00" step="0.01"
                                    @if($editingSupplierId && !auth()->user()->hasRole('Super Admin')) disabled @endif>
                                <span class="absolute left-3 top-2.5 text-surface-400 text-xs">ج.م</span>
                            </div>
                        </div>

                        {{-- الرصيد الحالي --}}
                        <div class="space-y-1.5 flex-1">
                            <label class="text-sm font-bold text-surface-700">{{ __('Current Balance') }} <span class="text-surface-400 font-normal">({{ __('Positive = You Owe Them') }})</span></label>
                            <div class="relative">
                                <input type="number" 
                                    class="w-full bg-surface-50 border {{ $errors->has('current_balance') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl pl-10 pr-4 py-2.5 text-sm font-display font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all disabled:opacity-50 disabled:bg-surface-100" 
                                    wire:model="current_balance" placeholder="0.00" step="0.01"
                                    @if($editingSupplierId && !auth()->user()->hasRole('Super Admin')) disabled @endif>
                                <span class="absolute left-3 top-2.5 text-surface-400 text-xs">ج.م</span>
                            </div>
                        </div>

                        {{-- الحالة - Toggle --}}
                        <div class="flex items-center justify-between p-4 bg-surface-50 rounded-2xl border border-surface-100 md:col-span-2">
                             <div>
                                 <p class="text-sm font-bold text-surface-900">{{ __('Status') }}</p>
                                 <p class="text-xs text-surface-500">{{ __('Enable or disable this supplier.') }}</p>
                             </div>
                             <button type="button" 
                                @click="$wire.set('is_active', !@js($is_active))"
                                :class="@js($is_active) ? 'bg-primary-600' : 'bg-surface-300'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-offset-2 focus:ring-2 focus:ring-primary-500">
                                <span :class="@js($is_active) ? '-translate-x-6' : '-translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm"></span>
                             </button>
                        </div>

                        {{-- ملاحظات --}}
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Notes') }}</label>
                            <textarea 
                                class="w-full bg-surface-50 border {{ $errors->has('notes') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all min-h-[80px]" 
                                wire:model="notes" rows="2" placeholder="{{ __('Any additional notes...') }}"></textarea>
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
                        wire:click="saveSupplier" wire:loading.attr="disabled" wire:target="saveSupplier">
                        <span wire:loading.remove wire:target="saveSupplier">{{ __('Save') }}</span>
                        <span wire:loading wire:target="saveSupplier" class="flex items-center gap-2">
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

</div>
