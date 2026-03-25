@php
    $pageType = 'pricing_tiers_create';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Create Pricing Tier') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="page-wrapper">
        <div class="container-xl">
            @livewire('dashboard.pricing.pricing-tier-form')
        </div>
    </div>
</x-app-layout>
