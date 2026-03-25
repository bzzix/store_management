@section('title', $title)

@extends('tenants.admin.layouts.master')

@section('header')
    <div class="col">
      <!-- Page pre-title -->
      <div class="page-pretitle">
        {{ __('Users mangement') }}
      </div>
      <h2 class="page-title">
        {{ $title }}
      </h2>
    </div>
    <!-- Page title actions -->
    <div class="col-auto ms-auto d-print-none">
      <div class="btn-list">
        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-nweuser">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
          {{ __('Add new') }}
        </a>
        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-nweuser">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
        </a>
      </div>
    </div>
@endsection

@section('content')

@livewire('tenants.admin.users.get-users')

@endsection