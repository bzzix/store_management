<div class="flex items-center gap-1.5" role="group">
    @can('purchase_invoices_view')
        <button x-data="{ clicked: false }" @click="clicked = true; $dispatch('open-print-modal', { invoiceId: {{ $row->id }} }); setTimeout(() => clicked = false, 1500)" 
            :class="{ 'opacity-50 pointer-events-none': clicked }" :disabled="clicked" 
            class="p-1.5 rounded-lg bg-secondary-50 text-secondary-600 hover:bg-secondary-100 transition-colors" title="{{ __('View') }}" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
        </button>
        <a href="{{ route('dashboard.suppliers.purchases.print', $row->id) }}" 
            class="p-1.5 rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-100 transition-colors" title="{{ __('Print') }}" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
        </a>
    @endcan

    @php
        $canEditAny = auth()->user()->hasRole(['super-admin', 'super_admin']) ?? auth()->id() === 1;
    @endphp

    @if($row->status === 'draft' || $canEditAny)
        @can('purchase_invoices_update')
            <button x-data="{ clicked: false }" @click="clicked = true; $dispatch('open-edit-modal', { invoiceId: {{ $row->id }} }); setTimeout(() => clicked = false, 1500)" 
                :class="{ 'opacity-50 pointer-events-none': clicked }" :disabled="clicked" 
                class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" title="{{ __('Edit') }}" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2 2 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
            </button>
        @endcan

        @can('purchase_invoices_delete')
            <button x-data="{ clicked: false }" @click="clicked = true; $dispatch('open-delete-modal', { invoiceId: {{ $row->id }}, invoiceNumber: '{{ addslashes($row->invoice_number) }}' }); setTimeout(() => clicked = false, 1500)" 
                :class="{ 'opacity-50 pointer-events-none': clicked }" :disabled="clicked" 
                class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors" title="{{ __('Delete') }}" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 2 0 0 1 1 1v1" /></svg>
            </button>
        @endcan
    @endif
</div>

