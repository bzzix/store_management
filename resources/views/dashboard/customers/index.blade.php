@extends('dashboard.layouts.master')

@section('breadcrumb', __('Customers'))

@section('content')
    @livewire('dashboard.customers.customers')
@endsection
