<div>
    @push('js')
        <script>
            // تأكيد حذف التصنيف
            window.addEventListener('delete-category', event => {
                const { categoryId, categoryName } = event.detail;
                
                window["iziToast"]['question']({
                    message: `{{ __('Do you really want to delete this category') }}: <strong>${categoryName}</strong>?`,
                    rtl: true,
                    timeout: 20000,
                    overlay: true,
                    displayMode: 'once',
                    id: 'question-category',
                    zindex: 999999999,
                    position: 'center',
                    buttons: [
                        ['<button class="bg-red-600 text-white px-4 py-1.5 rounded-lg font-bold text-xs ml-2">{{ __("Delete") }}</button>', function (instance, toast) {
                            Livewire.find('{{ $this->getId() }}').deletingCategoryId = categoryId;
                            Livewire.find('{{ $this->getId() }}').confirmDeleteCategory();
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }, true],
                        ['<button class="bg-surface-200 text-surface-700 px-4 py-1.5 rounded-lg font-bold text-xs">{{ __("Cancel") }}</button>', function (instance, toast) {
                            instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                        }],
                    ]
                });
            });
        </script>
    @endpush
    {{-- Categories Section --}}
    <div class="bg-white border border-surface-200/60 rounded-2xl shadow-soft overflow-hidden">
        <div class="p-6 border-b border-surface-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-surface-900">{{ __('Categories') }}</h3>
                <p class="text-sm text-surface-500 mt-0.5">{{ __('Manage and organize your products into categories.') }}</p>
            </div>
            
            <button wire:click="showAddModal" 
                class="flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50"
                wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="showAddModal" class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" />
                    </svg>
                    {{ __('Add Category') }}
                </span>
                <span wire:loading wire:target="showAddModal" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Loading...') }}
                </span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <div class="min-w-full inline-block align-middle">
                <livewire:dashboard.products.categories-data-table />
            </div>
        </div>
    </div>

    <!-- Create/Edit Category Modal -->
    @if ($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-data x-transition>
            <!-- Overlay -->
            <div class="absolute inset-0 bg-surface-900/40 backdrop-blur-sm" wire:click="closeModal"></div>
            
            <!-- Modal Content -->
            <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl relative z-10 flex flex-col overflow-hidden border border-white/20 glass-panel">
                <!-- Header -->
                <div class="px-8 py-6 border-b border-surface-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-xl font-bold text-surface-900">
                            @if($showEditModal) {{ __('Edit Category') }} @else {{ __('Add New Category') }} @endif
                        </h4>
                        <p class="text-xs text-surface-500 mt-1 font-medium">
                            @if($showEditModal) {{ __('Update existing category information.') }} @else {{ __('Define a new category for your products.') }} @endif
                        </p>
                    </div>
                    <button wire:click="closeModal" class="p-2 hover:bg-surface-100 rounded-full transition-colors">
                        <svg class="w-5 h-5 text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form Body -->
                <div class="px-8 py-8 overflow-y-auto custom-scrollbar space-y-6">
                    <!-- Basic Info -->
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Category Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" 
                                class="w-full bg-surface-50 border {{ $errors->has('name') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                wire:model.blur="name" placeholder="{{ __('e.g., Electronics, Fertilizers') }}">
                            @error('name')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-bold text-surface-700">{{ __('Description') }}</label>
                            <textarea 
                                class="w-full bg-surface-50 border {{ $errors->has('description') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all min-h-[100px]" 
                                wire:model.blur="description" rows="3" placeholder="{{ __('Category description...') }}"></textarea>
                            @error('description')
                                <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Sort Order') }}</label>
                                <input type="number" 
                                    class="w-full bg-surface-50 border {{ $errors->has('sortOrder') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all" 
                                    wire:model.blur="sortOrder" placeholder="0">
                                @error('sortOrder')
                                    <p class="text-xs font-bold text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-surface-700">{{ __('Status') }}</label>
                                <div class="flex items-center h-10 px-4 bg-surface-50 rounded-xl border border-surface-100">
                                    <button type="button" 
                                        @click="$wire.set('isActive', !@js($isActive))"
                                        :class="@js($isActive) ? 'bg-secondary-500' : 'bg-surface-300'"
                                        class="relative inline-flex h-5 w-10 items-center rounded-full transition-colors focus:outline-none">
                                        <span :class="@js($isActive) ? '-translate-x-5' : '-translate-x-1'"
                                            class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform"></span>
                                    </button>
                                    <span class="mr-3 text-xs font-bold @if($isActive) text-secondary-600 @else text-surface-400 @endif">
                                        {{ $isActive ? __('Active') : __('Inactive') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-8 py-6 bg-surface-50/50 border-t border-surface-100 flex items-center justify-end gap-3">
                    <button wire:click="closeModal" 
                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-surface-600 hover:bg-surface-100 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="saveCategory" 
                        class="px-8 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-primary-500/20 transition-all active:scale-95 flex items-center gap-2">
                        <span wire:loading.remove wire:target="saveCategory">{{ __('Save') }}</span>
                        <span wire:loading wire:target="saveCategory" class="flex items-center gap-2">
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
