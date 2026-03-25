<div>
    @push('js')
        <script>
            window.addEventListener('edit-customer-form', event => {
                // CustomerForm component listens to this directly
            });
        </script>
    @endpush

    <div class="bg-white border border-surface-200/60 rounded-2xl shadow-soft overflow-hidden">
        <div class="p-6 border-b border-surface-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-surface-900">{{ __('Customers') }}</h3>
                <p class="text-sm text-surface-500 mt-0.5">{{ __('Manage and monitor your customers and their balances.') }}</p>
            </div>
            
            @can('customers_add')
                <button wire:click="$dispatchTo('dashboard.customers.customer-form', 'reset-customer-form')" 
                    wire:loading.attr="disabled"
                    class="flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove wire:target="$dispatchTo('dashboard.customers.customer-form', 'reset-customer-form')" class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" />
                        </svg>
                        {{ __('Add New Customer') }}
                    </span>
                    <span wire:loading wire:target="$dispatchTo('dashboard.customers.customer-form', 'reset-customer-form')" class="flex items-center gap-2">
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
                @livewire('dashboard.customers.customers-data-table')
            </div>
        </div>
    </div>

    {{-- Customer Modal Form --}}
    @livewire('dashboard.customers.customer-form')
</div>
