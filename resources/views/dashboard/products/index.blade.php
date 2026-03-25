@extends('dashboard.layouts.master')

@section('header')
    <div class="col">
      <!-- Page pre-title -->
      <div class="page-pretitle">
        {{ $title }}
      </div>
      <h2 class="page-title">
        {{ __('Products') }}
      </h2>
    </div>
    <!-- Page title actions -->
    <div class="col-auto ms-auto d-print-none">
      <div class="btn-list">
      </div>
    </div>
@endsection

@section('content')

        @livewire('dashboard.products.products')

@endsection
