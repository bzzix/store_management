@extends('dashboard.layouts.master')

@section('breadcrumb', __('Settings'))

@section('content')
    @livewire('dashboard.settings-form')
@endsection
