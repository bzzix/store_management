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
        {{-- <button class="btn btn-outline-success d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-nweusercsv">
          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-csv"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M7 16.5a1.5 1.5 0 0 0 -3 0v3a1.5 1.5 0 0 0 3 0" /><path d="M10 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75" /><path d="M16 15l2 6l2 -6" /></svg>
        إضافة من ملف أكسيل
        </button>
        <button class="btn btn-outline-success d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-nweusercsv">
          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-csv"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" /><path d="M7 16.5a1.5 1.5 0 0 0 -3 0v3a1.5 1.5 0 0 0 3 0" /><path d="M10 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75" /><path d="M16 15l2 6l2 -6" /></svg>
        </button> --}}
    </div>
    </div>
@endsection

@section('content')

@livewire('tenants.admin.users.student_registration')

@endsection