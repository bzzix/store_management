@php
    $pageType = 'pricing_preview';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Pricing Preview') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="page-wrapper">
        <div class="container-xl">
            @livewire('dashboard.pricing.pricing-preview')
        </div>
    </div>
</x-app-layout>
