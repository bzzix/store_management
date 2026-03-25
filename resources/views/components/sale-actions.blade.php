<div class="flex items-center gap-1.5" role="group">
    {{-- Print Button --}}
    <a href="{{ route('dashboard.sales.print', $row->id) }}" target="_blank"
        class="p-1.5 rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-100 transition-colors" 
        title="{{ __('Print') }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
        </svg>
    </a>

    {{-- View Button --}}
    <button onclick="Livewire.dispatch('view-invoice', { invoiceId: {{ $row->id }} })" 
        class="p-1.5 rounded-lg bg-secondary-50 text-secondary-600 hover:bg-secondary-100 transition-colors" 
        title="{{ __('View') }}" type="button">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
        </svg>
    </button>

    {{-- Edit Button (Super Admin Only) --}}
    @can('sale_invoices_edit')
        <button onclick="Livewire.dispatch('edit-invoice', { invoiceId: {{ $row->id }} })" 
            class="p-1.5 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors" 
            title="{{ __('Edit') }}" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 20h4l10.5 -10.5a1.5 1.5 0 0 0 -4 -4l-10.5 10.5v4" />
                <path d="M13.5 6.5l4 4" />
            </svg>
        </button>
    @endcan

    @if($row->status !== 'cancelled')
        {{-- Cancel Button (Restricted) --}}
        @can('sales_cancel')
            <button wire:click="$parent.confirmCancellation({{ $row->id }})" 
                wire:loading.attr="disabled"
                class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed group" 
                title="{{ __('Cancel') }}" type="button">
                <svg wire:loading.remove wire:target="$parent.confirmCancellation({{ $row->id }})" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
                <svg wire:loading wire:target="$parent.confirmCancellation({{ $row->id }})" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </button>
        @endcan
    @endif

    {{-- Delete Button (Super Admin Only) --}}
    @can('sale_invoices_delete')
        <button wire:click="$parent.confirmDelete({{ $row->id }})" 
            wire:loading.attr="disabled"
            class="p-1.5 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed group" 
            title="{{ __('Delete') }}" type="button">
            <svg wire:loading.remove wire:target="$parent.confirmDelete({{ $row->id }})" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 7l16 0" />
                <path d="M10 11l0 6" />
                <path d="M14 11l0 6" />
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
            </svg>
            <svg wire:loading wire:target="$parent.confirmDelete({{ $row->id }})" class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </button>
    @endcan
</div>
