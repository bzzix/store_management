<div class="btn-group btn-group-sm" role="group">
    {{-- زر التعديل --}}
    <button type="button" class="btn btn-icon btn-ghost-primary" 
            wire:click="editProduct({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
            wire:target="editProduct({{ $row->id }})"
            title="{{ __('Edit') }}">
        <span wire:loading.remove wire:target="editProduct({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" /><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" /><path d="M16 5l3 3" /></svg>
        </span>
        <span wire:loading wire:target="editProduct({{ $row->id }})">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </span>
    </button>

    {{-- زر إدارة الوحدات --}}
    <button type="button" class="btn btn-icon btn-ghost-info" 
            wire:click="manageUnits({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
            title="{{ __('Manage Units') }}">
        <span wire:loading.remove wire:target="manageUnits({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2" /><path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z" /><path d="M9 12l.01 0" /><path d="M13 12l2 0" /><path d="M9 16l.01 0" /><path d="M13 16l2 0" /></svg>
        </span>
        <span wire:loading wire:target="manageUnits({{ $row->id }})">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </span>
    </button>

    {{-- زر إدارة الصور --}}
    <button type="button" class="btn btn-icon btn-ghost-purple" 
            wire:click="manageImages({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
            title="{{ __('Manage Images') }}">
        <span wire:loading.remove wire:target="manageImages({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
        </span>
        <span wire:loading wire:target="manageImages({{ $row->id }})">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </span>
    </button>

    {{-- زر إدارة الأسعار --}}
    <button type="button" class="btn btn-icon btn-ghost-success" 
            wire:click="managePrices({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
            title="{{ __('Manage Prices') }}">
        <span wire:loading.remove wire:target="managePrices({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" /><path d="M12 3v3m0 12v3" /></svg>
        </span>
        <span wire:loading wire:target="managePrices({{ $row->id }})">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </span>
    </button>

    {{-- زر تبديل المميز --}}
    <button type="button" class="btn btn-icon btn-ghost-warning" 
            wire:click="toggleFeatured({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
            title="{{ __('Toggle Featured') }}">
        <span wire:loading.remove wire:target="toggleFeatured({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
        </span>
        <span wire:loading wire:target="toggleFeatured({{ $row->id }})">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </span>
    </button>

    {{-- زر تبديل الحالة --}}
    <button type="button" class="btn btn-icon btn-ghost-secondary" 
            wire:click="toggleActive({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
            title="{{ __('Toggle Status') }}">
        <span wire:loading.remove wire:target="toggleActive({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c4.97 0 9 3.582 9 8c0 1.06 -.474 2.078 -1.318 3.02c.5 1.965 -.736 4.918 -4 6.368c-.811 .464 -1.88 1.023 -3.436 1.338c-.864 -.304 -1.782 -.596 -2.634 -.996c-4.694 -1.888 -7.612 -5.223 -7.612 -9.73c0 -4.418 4.03 -8 9 -8z" /></svg>
        </span>
        <span wire:loading wire:target="toggleActive({{ $row->id }})">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </span>
    </button>

    {{-- زر الحذف --}}
    <button type="button" class="btn btn-icon btn-ghost-danger" 
            wire:click="confirmDelete({{ $row->id }})"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50"
            title="{{ __('Delete') }}">
        <span wire:loading.remove wire:target="confirmDelete({{ $row->id }})">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v1" /></svg>
        </span>
        <span wire:loading wire:target="confirmDelete({{ $row->id }})">
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        </span>
    </button>
</div>
