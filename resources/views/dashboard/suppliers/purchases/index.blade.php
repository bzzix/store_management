@extends('dashboard.layouts.master')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    {{ __('Suppliers Management') }}
                </div>
                <h2 class="page-title">
                    {{ __('Purchases') }}
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <livewire:dashboard.suppliers.purchases.purchases-list />
    </div>
</div>
@endsection
