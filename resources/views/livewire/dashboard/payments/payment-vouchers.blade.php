<div>
    <div class="py-6">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold text-surface-900 tracking-tight font-display mb-1">
                        {{ __('Payment & Receipt Vouchers') }}
                    </h1>
                    <p class="text-surface-500 font-medium">{{ __('Manage standalone financial transactions for customers and suppliers.') }}</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <button wire:click="openCreateModal" 
                        class="group inline-flex items-center gap-2.5 px-6 py-3 bg-primary-600 text-white rounded-2xl font-bold hover:bg-primary-700 active:scale-95 transition-all shadow-lg shadow-primary-500/25">
                        <div class="p-1 bg-white/20 rounded-lg group-hover:rotate-90 transition-transform duration-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <span>{{ __('Create New Voucher') }}</span>
                    </button>
                </div>
            </div>

            {{-- Statistics / Summary Cards (Optional) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white/60 backdrop-blur-xl border border-white p-6 rounded-3xl shadow-soft">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-success-50 text-success-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <h3 class="text-surface-500 text-sm font-bold uppercase tracking-wider mb-1">{{ __('Total Receipts') }}</h3>
                    <p class="text-2xl font-black text-surface-900 font-display">
                        {{ number_format(\App\Models\Payment::where('voucher_type', 'receipt')->where('status', 'completed')->sum('amount'), 2) }} <span class="text-sm font-bold text-surface-400">ج.م</span>
                    </p>
                </div>

                <div class="bg-white/60 backdrop-blur-xl border border-white p-6 rounded-3xl shadow-soft">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-primary-50 text-primary-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <h3 class="text-surface-500 text-sm font-bold uppercase tracking-wider mb-1">{{ __('Total Disbursements') }}</h3>
                    <p class="text-2xl font-black text-surface-900 font-display">
                        {{ number_format(\App\Models\Payment::where('voucher_type', 'disbursement')->where('status', 'completed')->sum('amount'), 2) }} <span class="text-sm font-bold text-surface-400">ج.م</span>
                    </p>
                </div>

                <div class="bg-white/60 backdrop-blur-xl border border-white p-6 rounded-3xl shadow-soft">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-warning-50 text-warning-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <h3 class="text-surface-500 text-sm font-bold uppercase tracking-wider mb-1">{{ __('Recent Activities') }}</h3>
                    <p class="text-2xl font-black text-surface-900 font-display">
                        {{ \App\Models\Payment::whereNotNull('voucher_type')->where('created_at', '>=', now()->startOfDay())->count() }} <span class="text-sm font-bold text-surface-400">{{ __('Today') }}</span>
                    </p>
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="bg-white/80 backdrop-blur-xl border border-white rounded-[2.5rem] shadow-soft overflow-hidden">
                <div class="p-6 sm:p-8">
                    <livewire:dashboard.payments.payment-vouchers-data-table />
                </div>
            </div>
        </div>

        {{-- Create/Edit Modal - Premium Style --}}
        <x-dialog-modal wire:model="showModal" maxWidth="3xl">
            <x-slot name="title">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-primary-50 text-primary-600 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="font-display font-black text-2xl text-surface-900">
                        {{ $isEditing ? __('Edit Voucher') : __('Create New Voucher') }}
                    </span>
                </div>
            </x-slot>

            <x-slot name="content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-4">
                    {{-- Left Column: Basics --}}
                    <div class="space-y-6">
                        <div>
                            <x-label for="target_type" value="{{ __('Target Type') }}" class="mb-2 text-surface-700 font-bold" />
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" wire:click="$set('target_type', 'customer')" 
                                    class="flex items-center justify-center gap-2 p-3 rounded-2xl border-2 transition-all {{ $target_type === 'customer' ? 'border-primary-500 bg-primary-50 text-primary-700 font-bold' : 'border-surface-100 bg-surface-50 text-surface-500 grayscale opacity-60' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    {{ __('Customer') }}
                                </button>
                                <button type="button" wire:click="$set('target_type', 'supplier')" 
                                    class="flex items-center justify-center gap-2 p-3 rounded-2xl border-2 transition-all {{ $target_type === 'supplier' ? 'border-primary-500 bg-primary-50 text-primary-700 font-bold' : 'border-surface-100 bg-surface-50 text-surface-500 grayscale opacity-60' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    {{ __('Supplier') }}
                                </button>
                            </div>
                        </div>

                        <div>
                            <x-label for="target_id" value="{{ $target_type === 'customer' ? __('Customer') : __('Supplier') }}" class="mb-2 text-surface-700 font-bold" />
                            <div class="relative group">
                                <select wire:model.live="target_id" id="target_id" 
                                    class="w-full h-14 pl-4 pr-10 text-lg border-2 border-surface-100 rounded-2xl bg-surface-50 focus:bg-white focus:border-primary-500 transition-all appearance-none cursor-pointer">
                                    <option value="">{{ __('Select Target') }}...</option>
                                    @foreach($targets as $target)
                                        <option value="{{ $target->id }}">{{ $target->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-surface-400 group-hover:text-primary-500 transition-colors pointer-events-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                            <x-input-error for="target_id" class="mt-2" />
                            
                            {{-- Balance Display --}}
                            @if($target_id)
                                <div class="mt-4 p-4 rounded-2xl flex items-center justify-between border-2 {{ $currentTargetBalance > 0 ? 'bg-danger-50 border-danger-100 text-danger-700 ' : 'bg-success-50 border-success-100 text-success-700' }}">
                                    <div class="flex items-center gap-2">
                                        <div class="p-2 bg-white/50 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                        </div>
                                        <span class="text-sm font-bold uppercase tracking-wider">{{ __('Current Balance') }}</span>
                                    </div>
                                    <span class="text-xl font-black font-display font-medium">{{ number_format($currentTargetBalance, 2) }} ج.م</span>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-1">
                                <x-label for="voucher_type" value="{{ __('Voucher Type') }}" class="mb-2 text-surface-700 font-bold" />
                                <select wire:model="voucher_type" id="voucher_type" 
                                    class="w-full h-12 border-2 border-surface-100 rounded-2xl bg-surface-50 focus:bg-white focus:border-primary-500 transition-all">
                                    <option value="receipt">{{ __('Receipt (قبض)') }}</option>
                                    <option value="disbursement">{{ __('Disbursement (صرف)') }}</option>
                                </select>
                            </div>
                            <div class="col-span-1">
                                <x-label for="payment_date" value="{{ __('Date') }}" class="mb-2 text-surface-700 font-bold" />
                                <x-input type="date" wire:model="payment_date" id="payment_date" class="w-full h-12 rounded-2xl" />
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Financials --}}
                    <div class="space-y-6">
                        <div class="p-6 bg-surface-50 rounded-3xl border-2 border-surface-100/50">
                            <div class="mb-6">
                                <x-label for="amount" value="{{ __('Amount') }}" class="mb-2 text-surface-700 font-black text-lg" />
                                <div class="relative">
                                    <x-input type="number" step="0.01" wire:model="amount" id="amount" 
                                        class="w-full h-16 pl-4 pr-16 text-3xl font-black text-primary-700 border-2 border-surface-100 rounded-2xl focus:ring-4 focus:ring-primary-500/10 transition-all" />
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 text-surface-400 font-bold text-lg">ج.م</div>
                                </div>
                                <x-input-error for="amount" class="mt-2" />
                            </div>

                            <div>
                                <x-label for="payment_method" value="{{ __('Payment Method') }}" class="mb-2 text-surface-700 font-bold" />
                                <select wire:model="payment_method" id="payment_method" 
                                    class="w-full h-12 border-2 border-surface-100 rounded-2xl bg-surface-50 focus:bg-white focus:border-primary-500 transition-all">
                                    <option value="cash">{{ __('Cash') }}</option>
                                    <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                                    <option value="check">{{ __('Check') }}</option>
                                    <option value="credit_card">{{ __('Credit Card') }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <x-label for="reference_number" value="{{ __('Reference Number') }}" class="mb-2 text-surface-700 font-bold" />
                            <x-input type="text" wire:model="reference_number" id="reference_number" 
                                placeholder="{{ __('e.g. Check # or Bank Ref') }}"
                                class="w-full h-12 rounded-2xl" />
                        </div>

                        <div>
                            <x-label for="notes" value="{{ __('Notes') }}" class="mb-2 text-surface-700 font-bold" />
                            <textarea wire:model="notes" id="notes" 
                                class="form-textarea w-full h-24 border-2 border-surface-100 rounded-2xl bg-surface-50 focus:bg-white focus:border-primary-500 transition-all resize-none" 
                                placeholder="{{ __('Add any additional internal notes here...') }}"></textarea>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="flex items-center justify-between w-full">
                    <p class="text-xs text-surface-400 max-w-xs leading-relaxed">
                        {{ __('By saving this voucher, the system will automatically update the target balance and record the transaction in their statement.') }}
                    </p>
                    <div class="flex items-center gap-3">
                        <button wire:click="$set('showModal', false)" 
                            class="px-6 py-3 text-surface-600 font-bold hover:bg-surface-100 rounded-2xl transition-all">
                            {{ __('Cancel') }}
                        </button>
                        <button wire:click="save" wire:loading.attr="disabled"
                            class="px-10 py-3 bg-primary-600 text-white rounded-2xl font-black hover:bg-primary-700 active:scale-95 transition-all shadow-lg shadow-primary-500/20 flex items-center gap-3">
                            <span wire:loading.remove>{{ __('Save Voucher') }}</span>
                            <span wire:loading class="p-0.5"><svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
                        </button>
                    </div>
                </div>
            </x-slot>
        </x-dialog-modal>

        {{-- Delete Modal - Refined --}}
        <x-confirmation-modal wire:model="confirmDeleteModal">
            <x-slot name="title">
                <span class="font-display font-black text-danger-600">{{ __('Confirm Termination') }}</span>
            </x-slot>

            <x-slot name="content">
                <div class="flex items-start gap-4 p-4 bg-danger-50 rounded-2xl border border-danger-100">
                    <div class="p-2 bg-white text-danger-600 rounded-lg shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-danger-900 mb-1">{{ __('This action is irreversible!') }}</h3>
                        <p class="text-danger-700 text-sm">
                            {{ __('Are you sure you want to delete this voucher? Deleting it will reverse the financial impact on the') }} 
                            <span class="font-black underline">{{ $target_type === 'customer' ? __('Customer') : __('Supplier') }}</span> 
                            {{ __('immediately.') }}
                        </p>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('confirmDeleteModal', false)" class="rounded-2xl border-0 bg-surface-100 hover:bg-surface-200">
                    {{ __('Keep Voucher') }}
                </x-secondary-button>

                <x-danger-button class="ms-3 rounded-2xl shadow-lg shadow-danger-500/20" wire:click="deleteVoucher" wire:loading.attr="disabled">
                    {{ __('Delete Permanently') }}
                </x-danger-button>
            </x-slot>
        </x-confirmation-modal>
    </div>

    <style>
        .shadow-soft {
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
        }
        .font-display {
            font-family: 'Outfit', 'Cairo', sans-serif;
        }
    </style>
</div>
