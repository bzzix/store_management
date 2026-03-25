<div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                // عرض الإشعارات
                Livewire.on('notify', (data) => {
                    const { type = 'info', title = '', msg = '' } = data[0] || data;
                    
                    const options = {
                        title: title || (type === 'success' ? '{{ __("Success") }}' : '{{ __("Info") }}'),
                        message: msg,
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

                // تأكيد حذف المخزن
                window.addEventListener('open-delete-warehouse-modal', event => {
                    const { warehouseId, warehouseName } = event.detail;
                    
                    iziToast.question({
                        timeout: 20000,
                        layout: 2,
                        title: '{{ __("Delete") }}',
                        message: `{{ __('Do you really want to delete this warehouse') }}: <strong>${warehouseName}</strong>?`,
                        rtl: true,
                        overlay: true,
                        displayMode: 'once',
                        id: 'delete-question',
                        zindex: 999999999,
                        position: 'center',
                        buttons: [
                            ['<button><b>{{ __("Delete") }}</b></button>', function (instance, toast) {
                                Livewire.find('{{ $this->getId() }}').handleConfirmDelete(warehouseId);
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
                <h3 class="text-xl font-bold text-surface-900">{{ __('Warehouses Management') }}</h3>
                <p class="text-sm text-surface-500 mt-0.5">{{ __('Manage and monitor your storage locations and inventory.') }}</p>
            </div>
            
            @can('warehouses_add')
                <button wire:click="showAddModal" 
                    wire:loading.attr="disabled"
                    class="flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove wire:target="showAddModal" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" />
                        </svg>
                        {{ __('Add New Warehouse') }}
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
                <livewire:dashboard.warehouses.warehouses-data-table />
            </div>
        </div>
    </div>

    {{-- Create/Edit Warehouse Modal --}}
    @if ($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-surface-900/40 backdrop-blur-sm" wire:click="closeModal"></div>
            
            <!-- Modal Content -->
            <div class="bg-white w-full max-w-3xl max-h-[90vh] rounded-3xl shadow-2xl relative z-10 flex flex-col overflow-hidden border border-white/20 glass-panel animate-in zoom-in duration-300">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-surface-100 flex items-center justify-between">
                    <div>
                        <h5 class="text-xl font-bold text-surface-900">
                            @if ($showEditModal)
                                {{ __('Edit Warehouse') }}
                            @else
                                {{ __('Add New Warehouse') }}
                            @endif
                        </h5>
                        <p class="text-sm text-surface-500 mt-1">{{ __('Please fill in the warehouse details below.') }}</p>
                    </div>
                    <button type="button" class="p-2 rounded-xl border border-surface-100 text-surface-400 hover:text-surface-900 hover:bg-surface-50 transition-all" wire:click="closeModal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-8 py-8 overflow-y-auto flex-1 custom-scrollbar">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-right" dir="rtl">
                        {{-- اسم المخزن --}}
                        <div class="space-y-1.5 text-right">
                            <label class="text-sm font-bold text-surface-700">{{ __('Warehouse Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('name') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="name" placeholder="{{ __('Warehouse name') }}">
                            @error('name')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- الكود --}}
                        <div class="space-y-1.5 text-right">
                            <label class="text-sm font-bold text-surface-700">{{ __('Code') }} <span class="text-red-500">*</span></label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('code') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all {{ $showEditModal ? 'bg-surface-100 opacity-60 cursor-not-allowed' : '' }}" 
                                wire:model="code" placeholder="{{ __('e.g., WH-001') }}" {{ $showEditModal ? 'readonly' : '' }}>
                            @error('code')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- العنوان --}}
                        <div class="md:col-span-2 space-y-1.5 text-right">
                            <label class="text-sm font-bold text-surface-700">{{ __('Address') }}</label>
                            <textarea 
                                class="w-full bg-surface-50 border {{ $errors->has('address') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all min-h-[80px]" 
                                wire:model="address" rows="2" placeholder="{{ __('Warehouse address') }}"></textarea>
                            @error('address')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- الهاتف --}}
                        <div class="space-y-1.5 text-right">
                            <label class="text-sm font-bold text-surface-700">{{ __('Phone') }}</label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('phone') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="phone" placeholder="{{ __('Phone number') }}">
                            @error('phone')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- البريد الإلكتروني --}}
                        <div class="space-y-1.5 text-right">
                            <label class="text-sm font-bold text-surface-700">{{ __('Email') }}</label>
                            <input type="email" 
                                class="w-full bg-surface-50 border {{ $errors->has('email') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="email" placeholder="{{ __('Email') }}">
                            @error('email')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- السعة --}}
                        <div class="space-y-1.5 text-right">
                            <label class="text-sm font-bold text-surface-700">{{ __('Capacity') }}</label>
                            <input type="number" 
                                class="w-full bg-surface-50 border {{ $errors->has('capacity') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="capacity" placeholder="0" min="0">
                            @error('capacity')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- المدير --}}
                        <div class="space-y-1.5 text-right">
                            <label class="text-sm font-bold text-surface-700">{{ __('Manager') }}</label>
                            <select 
                                class="w-full bg-surface-50 border {{ $errors->has('managerId') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model="managerId">
                                <option value="">{{ __('Select manager') }}</option>
                                @foreach (\App\Models\User::orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- الخيارات (Toggle) --}}
                        <div class="grid grid-cols-2 gap-4 md:col-span-2 text-right">
                             <div class="flex items-center justify-between p-4 bg-surface-50 rounded-2xl border border-surface-100">
                                 <div>
                                     <p class="text-sm font-bold text-surface-900">{{ __('Status') }}</p>
                                     <p class="text-xs text-surface-500">{{ __('Active or inactive.') }}</p>
                                 </div>
                                 <button type="button" 
                                    wire:click="$toggle('isActive')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-offset-2 focus:ring-2 focus:ring-primary-500 {{ $isActive ? 'bg-primary-600' : 'bg-surface-300' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm {{ $isActive ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                 </button>
                             </div>

                             <div class="flex items-center justify-between p-4 bg-surface-50 rounded-2xl border border-surface-100">
                                 <div>
                                     <p class="text-sm font-bold text-surface-900">{{ __('Main') }}</p>
                                     <p class="text-xs text-surface-500">{{ __('Primary location.') }}</p>
                                 </div>
                                 <button type="button" 
                                    wire:click="$toggle('isMain')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none ring-offset-2 focus:ring-2 focus:ring-primary-500 {{ $isMain ? 'bg-primary-600' : 'bg-surface-300' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform shadow-sm {{ $isMain ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                 </button>
                             </div>
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
                        wire:click="saveWarehouse" wire:loading.attr="disabled" wire:target="saveWarehouse">
                        <span wire:loading.remove wire:target="saveWarehouse">{{ __('Save') }}</span>
                        <span wire:loading wire:target="saveWarehouse" class="flex items-center gap-2">
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
