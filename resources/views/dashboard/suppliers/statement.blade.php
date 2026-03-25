@extends('dashboard.layouts.master')

@section('content')
<div class="container-fluid py-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 font-cairo">
        <div>
            <h1 class="text-3xl font-bold text-surface-900">{{ __('Supplier Statement') }}</h1>
            <p class="text-surface-500 mt-1">{{ $supplier->name }}</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard.suppliers.index') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-surface-200 text-surface-700 hover:bg-surface-50 transition-all font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l14 0" />
                    <path d="M5 12l6 6" />
                    <path d="M5 12l6 -6" />
                </svg>
                {{ __('Back') }}
            </a>
            <button onclick="window.print()" 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary-600 text-white hover:bg-primary-700 shadow-lg shadow-primary-200 transition-all font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                    <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                </svg>
                {{ __('Print') }}
            </button>
        </div>
    </div>

    @livewire('dashboard.suppliers.supplier-statement', ['supplier' => $supplier])
</div>
@endsection
