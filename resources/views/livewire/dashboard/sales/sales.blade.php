<div class="py-6 px-4 sm:px-6 lg:px-8">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-black text-surface-900 tracking-tight flex items-center gap-3">
                <span class="p-2.5 rounded-2xl bg-primary-600 text-white shadow-xl shadow-primary-500/20">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </span>
                {{ __('Sales Invoices') }}
            </h1>
            <p class="mt-1 text-surface-500 font-medium mr-14">{{ __('Manage, track, and issue new sales invoices for customers.') }}</p>
        </div>

        @can('sales_add')
            <button wire:click="openCreateModal" wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-primary-600 text-white font-bold hover:bg-primary-700 active:scale-95 transition-all shadow-lg shadow-primary-500/25">
                <svg wire:loading.remove wire:target="openCreateModal" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <svg wire:loading wire:target="openCreateModal" class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                {{ __('New Invoice') }}
            </button>
        @endcan
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Total Sales Today --}}
        <div class="bg-white p-6 rounded-3xl border border-surface-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-surface-50 opacity-50 group-hover:scale-110 transition-transform">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M11 15h2v2h-2v-2m0-8h2v6h-2V7m1-5C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="text-surface-500 font-bold text-sm mb-1 uppercase tracking-wider">{{ __('Daily Sales Total') }}</div>
                <div class="text-3xl font-black text-surface-900">{{ number_format(\App\Models\SaleInvoice::whereDate('invoice_date', today())->completed()->sum('total_amount'), 2) }} <span class="text-sm font-medium text-surface-400">EGP</span></div>
                <div class="mt-2 text-xs font-bold text-green-500 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                    <span>{{ \App\Models\SaleInvoice::whereDate('invoice_date', today())->count() }} {{ __('Invoices') }}</span>
                </div>
            </div>
        </div>
        {{-- Unpaid --}}
        <div class="bg-white p-6 rounded-3xl border border-surface-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-orange-50 opacity-50 group-hover:scale-110 transition-transform">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="text-surface-500 font-bold text-sm mb-1 uppercase tracking-wider">{{ __('Total Unpaid') }}</div>
                <div class="text-3xl font-black text-orange-600">{{ number_format(\App\Models\SaleInvoice::sum('total_amount') - \App\Models\SaleInvoice::sum('paid_amount'), 2) }} <span class="text-sm font-medium text-surface-400">EGP</span></div>
                <div class="mt-2 text-xs font-bold text-orange-500 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ __('Waiting collection') }}</span>
                </div>
            </div>
        </div>
        {{-- Profit Today --}}
        <div class="bg-white p-6 rounded-3xl border border-surface-100 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 text-primary-50 opacity-50 group-hover:scale-110 transition-transform">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 17.08l1.5 1.41z"/></svg>
            </div>
            <div class="relative z-10">
                <div class="text-surface-500 font-bold text-sm mb-1 uppercase tracking-wider">{{ __('Daily Profit') }}</div>
                <div class="text-3xl font-black text-primary-600">{{ number_format(\App\Models\SaleInvoice::whereDate('invoice_date', today())->completed()->get()->sum('total_profit'), 2) }} <span class="text-sm font-medium text-surface-400">EGP</span></div>
                <div class="mt-2 text-xs font-bold text-primary-500 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <span>{{ __('Gross margin') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- DataTable Section --}}
    <div class="bg-white rounded-3xl border border-surface-100 shadow-sm overflow-hidden">
        <livewire:dashboard.sales.sales-data-table />
    </div>

    {{-- Modals --}}
    <livewire:dashboard.sales.sale-invoice-form />

    {{-- Cancel Confirmation --}}
    <div x-data="{ show: @entangle('confirmingCancellation') }" x-show="show" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" class="fixed inset-0 bg-surface-900/40 backdrop-blur-sm" @click="show = false"></div>
            <div x-show="show" class="inline-block w-full max-w-lg p-8 overflow-hidden text-right align-bottom transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle">
                <div class="w-16 h-16 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-surface-900 text-center mb-2">{{ __('Cancel Invoice') }}</h3>
                <p class="text-surface-500 text-center mb-8 px-4">{{ __('Are you sure you want to cancel this invoice? This action will revert stock and customer balance adjustments.') }}</p>
                
                <div class="flex gap-4">
                    <button @click="show = false" class="flex-1 px-6 py-3 rounded-2xl border border-surface-200 text-surface-600 font-bold hover:bg-surface-50 transition-all">
                        {{ __('No, Keep it') }}
                    </button>
                    <button wire:click="cancelInvoice" class="flex-1 px-6 py-3 rounded-2xl bg-red-600 text-white font-bold hover:bg-red-700 shadow-lg shadow-red-500/25 transition-all">
                        {{ __('Yes, Cancel it') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation --}}
    <div x-data="{ show: @entangle('confirmingDeletion') }" x-show="show" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="show" class="fixed inset-0 bg-surface-900/40 backdrop-blur-sm" @click="show = false"></div>
            <div x-show="show" class="inline-block w-full max-w-lg p-8 overflow-hidden text-right align-bottom transition-all transform bg-white shadow-2xl rounded-3xl sm:my-8 sm:align-middle">
                <div class="w-16 h-16 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-surface-900 text-center mb-2">{{ __('Delete Invoice') }}</h3>
                <p class="text-surface-500 text-center mb-8 px-4">{{ __('Are you sure you want to permanently delete this invoice? This action cannot be undone.') }}</p>
                
                <div class="flex gap-4">
                    <button @click="show = false" class="flex-1 px-6 py-3 rounded-2xl border border-surface-200 text-surface-600 font-bold hover:bg-surface-50 transition-all">
                        {{ __('No, Keep it') }}
                    </button>
                    <button wire:click="deleteInvoice" class="flex-1 px-6 py-3 rounded-2xl bg-red-700 text-white font-bold hover:bg-red-800 shadow-lg shadow-red-500/25 transition-all">
                        {{ __('Yes, Delete it') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
