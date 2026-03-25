<div class="flex items-center gap-1.5" role="group">
    @can('warehouses_update')
    {{-- Edit Button --}}
    <button type="button" 
            wire:click="editWarehouse({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:target="editWarehouse({{ $row->id }})"
            :class="{ 'opacity-50 pointer-events-none': $wire.get('loading_id') == {{ $row->id }} }"
            class="p-1.5 rounded-lg bg-primary-50 text-primary-600 hover:bg-primary-100 transition-colors"
            title="{{ __('Edit') }}">
        <span wire:loading.remove wire:target="editWarehouse({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
        </span>
        <span wire:loading wire:target="editWarehouse({{ $row->id }})">
            <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </span>
    </button>

    {{-- Toggle Status Button --}}
    <button type="button" 
            wire:click="toggleActive({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:target="toggleActive({{ $row->id }})"
            class="p-1.5 rounded-lg {{ $row->is_active ? 'bg-secondary-50 text-secondary-600 hover:bg-secondary-100' : 'bg-orange-50 text-orange-600 hover:bg-orange-100' }} transition-colors"
            title="{{ __('Toggle Status') }}">
        <span wire:loading.remove wire:target="toggleActive({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c-4.473 0 -8 3.582 -8 8c0 4.418 3.527 8 8 8c4.473 0 8 -3.582 8 -8c0 -4.418 -3.527 -8 -8 -8" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
        </span>
        <span wire:loading wire:target="toggleActive({{ $row->id }})">
            <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </span>
    </button>

    {{-- Delete Button --}}
    <button type="button" 
            wire:click="confirmDelete({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:target="confirmDelete({{ $row->id }})"
            class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
            title="{{ __('Delete') }}">
        <span wire:loading.remove wire:target="confirmDelete({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 2 0 0 1 1 1v1" /></svg>
        </span>
        <span wire:loading wire:target="confirmDelete({{ $row->id }})">
            <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </span>
    </button>
    @endcan
</div>


