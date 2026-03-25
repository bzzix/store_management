<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>{{ $title ?? __('Home') }} {{ settings('appSeprator') ?? '|' }} {{ settings('appName') ?? tenant('name') }}</title>
@if (App::getLocale() == 'ar')
    <link href="{{ asset('tenants/assets/dist/css/tabler.rtl.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/tabler-flags.rtl.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/tabler-payments.rtl.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/tabler-vendors.rtl.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/demo.rtl.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/custom-rtl.css') }}" rel="stylesheet"/>
@else
    <link href="{{ asset('tenants/assets/dist/css/tabler.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/tabler-flags.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/tabler-payments.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/tabler-vendors.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('tenants/assets/dist/css/demo.min.css') }}" rel="stylesheet"/>
@endif
<link rel="stylesheet" type="text/css" href="{{ url('frontend/izitoast/css/iziToast.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ url('tenants/assets/dist/css//custom.css') }}">
<link href="{{ asset('frontend/css/custome.css') }}" rel="stylesheet"/>

@FilemanagerScript
@livewireStyles