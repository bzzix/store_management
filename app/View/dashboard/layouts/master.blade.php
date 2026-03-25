<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="{{ App::getLocale() === 'ar' ? 'rtl' : 'ltr'}}">
<head>
    @include('tenants.admin.layouts.partials.head')
</head>
    <body>

        <div class="page">
            @include('tenants.admin.layouts.partials.main-sidebar')

            <div class="page-wrapper">

                <div class="page-header d-print-none">
                    <div class="container-xl">
                      <div class="row g-2 align-items-center">
                            @yield('header')
                      </div>
                    </div>
                  </div>

                <div class="page-body">
                    <div class="container-xl">
                        @yield('content')
                    </div>
                </div>

                @include('tenants.admin.layouts.partials.main-footer')
                
            </div>

        </div>

        @include('tenants.admin.layouts.partials.footer-scripts')
    </body>
</html>