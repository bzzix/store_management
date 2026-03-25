@extends('dashboard.layouts.master')

@section('header')
    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2 text-xs font-bold text-surface-400 uppercase tracking-wider">
            <span>{{ __('Dashboard') }}</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-primary-600">{{ __('Warehouses') }}</span>
        </div>
        <h2 class="text-2xl font-bold text-surface-900 tracking-tight">
            {{ __('Warehouses Management') }}
        </h2>
    </div>
    <div class="flex items-center gap-3">
        {{-- Actions can be added here if needed --}}
    </div>
@endsection

@section('content')
    @livewire('dashboard.warehouses.warehouses')
@endsection
