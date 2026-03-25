@section('title', $title)

@extends('tenants.admin.layouts.master')

@section('header')
    <div class="col">
      <!-- Page pre-title -->
      <div class="page-pretitle">
        {{ __('Users') }}
      </div>
      <h2 class="page-title">
        {{ $title }}
      </h2>
    </div>
    <!-- Page title actions -->
    <div class="col-auto ms-auto d-print-none">
      <div class="btn-list">

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
            <p><b>تمكنك هذه الصفحة من إدارة ملفات المستخدمين حيث تمكنك من:</b></p>
            <ul>
                <li>تفعيل/إلغاء تفعيل إنشاء حسابات جديدة.</li>
                <li>تحديد صلاحية المستخدمين الجدد.</li>
                <li>اتخاذ إجراء مع المستخدمين الجدد ك (طلب ملف أو تعبئة استبيان).</li>
            </ul>
        </div>
        
    </div>
    </div>
    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
</div>


    @livewire('tenants.admin.users.users-settings')


@endsection