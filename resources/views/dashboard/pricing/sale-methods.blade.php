@php
    $pageType = 'sale_methods_index';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Sale Methods') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="page-wrapper">
        <div class="container-xl">
            @livewire('dashboard.pricing.sale-methods-table')
        </div>
    </div>
</x-app-layout>
