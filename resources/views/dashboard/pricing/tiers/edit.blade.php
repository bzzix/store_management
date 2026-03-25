@php
    $pageType = 'pricing_tiers_edit';
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Edit Pricing Tier') }}: {{ $tier->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="page-wrapper">
        <div class="container-xl">
            @livewire('dashboard.pricing.pricing-tier-form', ['tier' => $tier])
        </div>
    </div>
</x-app-layout>
