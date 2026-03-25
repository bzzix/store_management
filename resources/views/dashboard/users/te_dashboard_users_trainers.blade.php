@section('title', $title)

@extends('tenants.admin.layouts.master')

@section('header')
    <div class="col">
      <!-- Page pre-title -->
      <div class="page-pretitle">
        {{ __('Personnel Affairs') }}
      </div>
      <h2 class="page-title">
        {{ $title }}
      </h2>
    </div>
    <!-- Page title actions -->
    <div class="col-auto ms-auto d-print-none">
      <div class="btn-list mb-3">
        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-nweuser">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
          {{ __('Add new') }}
        </a>
        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-nweuser">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
        </a>
      </div>

      <div class="btn-list">
        <a href="#" class="btn btn-yellow d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modal-nweuserfromlist">
          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-playlist-add"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 8h-14" /><path d="M5 12h9" /><path d="M11 16h-6" /><path d="M15 16h6" /><path d="M18 13v6" /></svg>
          من القائمـــة
        </a>
        <a href="#" class="btn btn-yellow d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modal-nweuserfromlist">
          <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-playlist-add"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M19 8h-14" /><path d="M5 12h9" /><path d="M11 16h-6" /><path d="M15 16h6" /><path d="M18 13v6" /></svg>
        </a>
      </div>
    </div>
@endsection

@section('content')

<div class="alert alert-warning alert-dismissible" role="alert">
    <div class="d-flex">
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M12 9h.01"></path><path d="M11 12h1v4h1"></path></svg>
    </div>
    <div>
        <h4 class="alert-title">{{ __('Did you know?') }}</h4>

        <div class="text-secondary">
            <p><b>تمكنك هذه الصفحة من إدارة الموظفين في منصتك:</b></p>
            <ul>
                <li>إضافة، تعديل أو حذف مدربين.</li>
                <li>ربط المدربين مع المواد الدراسية.</li>
            </ul>
        </div>
        
    </div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>


    @livewire('tenants.admin.users.trainers-management')


@endsection