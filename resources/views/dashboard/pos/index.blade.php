@extends('dashboard.layouts.master')

@section('content')
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 rtl:space-x-reverse text-sm font-bold">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center text-surface-400 hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4 me-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/></svg>
                        {{ __('Dashboard') }}
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-3 h-3 text-surface-300 mx-1 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="ms-1 text-surface-900 md:ms-2">{{ __('Universal POS Center') }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <livewire:dashboard.pos.pos-center />
    </div>
@endsection
