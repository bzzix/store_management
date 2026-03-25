@extends('dashboard.layouts.master')

@section('breadcrumb', __('Suppliers'))

@section('content')
    @livewire('dashboard.suppliers.suppliers')
@endsection
