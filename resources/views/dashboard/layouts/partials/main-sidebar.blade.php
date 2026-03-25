@php
    $theme = 'darks'
@endphp
<aside class="navbar navbar-vertical navbar-expand-lg {{ $theme == 'dark' ? '' : 'navbar-transparent' }}" {!! $theme == 'dark' ? 'data-bs-theme="dark"' : '' !!}>
    <div class="container-fluid">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <h1 class="navbar-brand navbar-brand-autodark">
        <a href="/">
            @if (get_setting('appLogo'))
                <img src="{{ get_setting('appLogo') }}" width="110" height="32" alt="{{ get_setting('appName') ?? tenant('name') }}" class="navbar-brand-image" style="width: 100px; height:auto">
            @else
                {{ tenant('name') }}
            @endif
        </a>
    </h1>
    <div class="navbar-nav flex-row d-lg-none">

        <div class="nav-item dropdown">
            @auth
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="background-image: url('{{ Auth::user()->profile_photo_url }}')"></span>
                    <div class="d-none d-xl-block ps-2">
                        <div>{{ __(Auth::user()->name) }}</div>
                        <div class="mt-1 small text-muted">{{ Auth::user()->email }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="#" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-question" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" /><path d="M19 22v.01" /><path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" /></svg>
                        {{ __('Profile') }}
                    </a>                
                    <a href="#" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-get_setting" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                        {{ __('get_setting') }}
                    </a>
                    @can('admin_view')
                    <a href="#" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v4h-6z" /></svg>
                        {{ __('Dashboard') }}
                    </a>
                    @endcan
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="#" class="inline" id="logout">
                        @csrf
                        <button  class="btn dropdown-item" style="gap: 3px;justify-content: flex-start;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-volume-1">
                                <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                                <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                            </svg>
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            @else
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <span class="avatar avatar-sm" style="">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path><path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path></svg>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a href="#" class="dropdown-item">{{ __('Login') }}</a>
                    <a href="#" class="dropdown-item">{{ __('Register') }}</a>
                </div>
            @endauth
        </div>
    </div>
    <div class="collapse navbar-collapse" id="sidebar-menu">
        <ul class="navbar-nav pt-lg-3">
        <li class="nav-item {{ $pageType == 'admin_show' ? 'active' : '' }}">
            <a class="nav-link" href="#" >
            <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
            </span>
            <span class="nav-link-title">
                {{ __('Home') }}
            </span>
            </a>
        </li>

        @can('warehouses_view')
            <li class="nav-item {{ $pageType === 'warehouses_index' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard.warehouses.index') }}">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-warehouse" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 21v-13l9 -4l9 4v13" /><path d="M13 13h4v8h-10v-6h2" /><path d="M13 21v-9a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v3" /></svg>
                    </span>
                    <span class="nav-link-title">
                        {{ __('Warehouses') }}
                    </span>
                </a>
            </li>
        @endcan

        @can('products_view')
            <li class="nav-item dropdown {{ in_array($pageType, ['products_index', 'products_categories']) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle" href="#navbar-products" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
                    </span>
                    <span class="nav-link-title">
                        {{ __('Products Management') }}
                    </span>
                </a>
                <div class="dropdown-menu" id="navbar-products">
                    <a class="dropdown-item {{ $pageType === 'products_index' ? 'active' : '' }}" href="{{ route('dashboard.products.index') }}">
                        {{ __('Products') }}
                    </a>
                    <a class="dropdown-item {{ $pageType === 'products_categories' ? 'active' : '' }}" href="{{ route('dashboard.products.categories') }}">
                        {{ __('Categories') }}
                    </a>
                </div>
            </li>
        @endcan

        @can('suppliers_view')
            {{-- Start Suppliers --}}
            @php
                $suppliers_route = ['suppliers_index', 'purchase_invoices_create', 'purchase_invoices_index'];
            @endphp
            <li class="nav-item dropdown {{ isset($pageType) && in_array($pageType, $suppliers_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ isset($pageType) && in_array($pageType, $suppliers_route) ? 'show' : '' }}" href="#navbar-suppliers" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ isset($pageType) && in_array($pageType, $suppliers_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck-delivery" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5" /><path d="M3 9l4 0" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('إدارة الموردين') }}
                </span>
                </a>
                <div class="dropdown-menu {{ isset($pageType) && in_array($pageType, $suppliers_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ isset($pageType) && $pageType === 'suppliers_index' ? 'active' : '' }}" href="{{ route('dashboard.suppliers.index') }}">
                        {{ __('الموردين') }}
                    </a>
                    <a class="dropdown-item {{ isset($pageType) && $pageType === 'purchase_invoices_index' ? 'active' : '' }}" href="{{ route('dashboard.suppliers.purchases.index') }}">
                        {{ __('المشتريات') }}
                    </a>
                    </div>
                </div>
                </div>
            </li>
        {{-- End Suppliers --}}
        @endcan

        @can('pricing_tiers_view')
            {{-- Start pricing --}}
            @php
                $pricing_route = ['pricing_tiers_index', 'pricing_tiers_create', 'pricing_tiers_edit', 'pricing_preview', 'pricing_report', 'sale_methods_index'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $pricing_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $pricing_route) ? 'show' : '' }}" href="#navbar-pricing" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $pricing_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4c-1 0 -1.784 .576 -2.246 1.466" /><path d="M12 21h-4a3 3 0 0 1 -3 -3v-1h2a3 3 0 0 0 3 -3h4a3 3 0 0 0 -3 -3h-2v-1a3 3 0 0 1 3 -3h4a3 3 0 0 1 2.7 2" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('سياسة التسعير') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $pricing_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'pricing_tiers_index' ? 'active' : '' }}" href="{{ route('dashboard.pricing.tiers.index') }}">
                        {{ __('الشرائح السعرية') }}
                    </a>
                    <a class="dropdown-item {{ $pageType === 'sale_methods_index' ? 'active' : '' }}" href="{{ route('dashboard.pricing.sale-methods.index') }}">
                        {{ __('طرق البيع') }}
                    </a>
                    <a class="dropdown-item {{ $pageType === 'pricing_preview' ? 'active' : '' }}" href="{{ route('dashboard.pricing.preview') }}">
                        {{ __('معاينة التسعير') }}
                    </a>
                    <a class="dropdown-item {{ $pageType === 'pricing_report' ? 'active' : '' }}" href="{{ route('dashboard.pricing.report') }}">
                        {{ __('تقرير التسعير') }}
                    </a>
                    </div>
                </div>
                </div>
            </li>
        {{-- End pricing --}}
        @endcan

            <li class="nav-item">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" style="color: red">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg style="color: red" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-logout"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                    </span>
                    <span class="nav-link-title">
                        <form method="POST" action="#" class="inline" id="logout">
                            @csrf
                            <button  class="btn dropdown-item" style="gap: 3px;justify-content: flex-start;">
                                <b>{{ __('Log Out') }}</b>
                            </button>
                        </form>
                    </span>
                </a>
                
            </li>

        </ul>
    </div>
    </div>
</aside>