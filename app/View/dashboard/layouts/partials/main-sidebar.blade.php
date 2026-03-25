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
            @if (settings('appLogo'))
                <img src="{{ settings('appLogo') }}" width="110" height="32" alt="{{ settings('appName') ?? tenant('name') }}" class="navbar-brand-image" style="width: 100px; height:auto">
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
                    <a href="{{ route('profile.show') }}" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-question" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" /><path d="M19 22v.01" /><path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" /></svg>
                        {{ __('Profile') }}
                    </a>                
                    <a href="{{ route('profile.settings') }}" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
                        {{ __('Settings') }}
                    </a>
                    @can('admin_view')
                    <a href="{{ route('te_dashboard') }}" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v4h-6z" /></svg>
                        {{ __('Dashboard') }}
                    </a>
                    @endcan
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="inline" id="logout">
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
                    <a href="{{ route('login') }}" class="dropdown-item">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="dropdown-item">{{ __('Register') }}</a>
                </div>
            @endauth
        </div>
    </div>
    <div class="collapse navbar-collapse" id="sidebar-menu">
        <ul class="navbar-nav pt-lg-3">
        <li class="nav-item {{ $pageType == 'admin_show' ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('te_dashboard') }}" >
            <span class="nav-link-icon d-md-none d-lg-inline-block"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg>
            </span>
            <span class="nav-link-title">
                {{ __('Home') }}
            </span>
            </a>
        </li>

        @can('pages_view')
            {{-- Start posts --}}
            @php
                $pages_route = ['admin_pages', 'admin_pages_new', 'admin_pages_update'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $pages_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $pages_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $pages_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4h6v8h-6z" /><path d="M4 16h6v4h-6z" /><path d="M14 12h6v8h-6z" /><path d="M14 4h6v4h-6z" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Pages') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $pages_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'admin_pages' ? 'active' : '' }}" href="{{ route('te_admin_pages') }}">
                        {{ __('All pages') }}
                    </a>
                        @can('pages_add')
                            {{-- <a class="dropdown-item {{ $pageType === 'admin_pages_new' ? 'active' : '' }}" href="{{ route('te_admin_pages_new') }}">
                                {{ __('New page') }}
                            </a> --}}
                        @endcan

                    </div>
                </div>
                </div>
            </li>
            {{-- End posts --}}
        @endcan
        @can('blog_view')
            {{-- Start posts --}}
            @php
                $posts_route = ['admin_posts', 'admin_posts_new', 'admin_categories', 'admin_posts_update', 'admin_categories_update'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $posts_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $posts_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $posts_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-rss" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" /><path d="M12 17a3 3 0 0 0 -3 -3" /><path d="M15 17a6 6 0 0 0 -6 -6" /><path d="M9 17h.01" /></svg>              </span>
                <span class="nav-link-title">
                    {{ __('Posts') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $posts_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'admin_posts' ? 'active' : '' }}" href="{{ route('te_admin_posts') }}">
                        {{ __('All posts') }}
                    </a>
                        @can('blog_add')
                            <a class="dropdown-item {{ $pageType === 'admin_posts_new' ? 'active' : '' }}" href="{{ route('te_admin_posts_new') }}">
                                {{ __('New post') }}
                            </a>
                        @endcan
                        @can('cats_view')
                            <a class="dropdown-item {{ $pageType === 'admin_categories' ? 'active' : '' }}" href="{{ route('te_admin_categories') }}">
                                {{ __('Categories') }}
                            </a>
                        @endcan
                    </div>
                </div>
                </div>
            </li>
            {{-- End posts --}}
        @endcan


        @can('users_view')
            {{-- Start users --}}
            @php
                $users_route = ['te_dashboard_users', 'te_dashboard_users_manage'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $users_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $users_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $users_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-cog" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" /><path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M19.001 15.5v1.5" /><path d="M19.001 21v1.5" /><path d="M22.032 17.25l-1.299 .75" /><path d="M17.27 20l-1.3 .75" /><path d="M15.97 17.25l1.3 .75" /><path d="M20.733 20l1.3 .75" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Users') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $users_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_users' ? 'active' : '' }}" href="{{ route('te_dashboard_users') }}">
                        {{ __('All users') }}
                    </a>
                    @livewire('tenants.admin.users.users-extra-menu-insert')
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_users_manage' ? 'active' : '' }}" href="{{ route('te_dashboard_users_manage') }}">
                        {{ __('Users mangement') }}
                    </a>
                    </div>
                </div>
                </div>
            </li>
            {{-- End users --}}
        @endcan

        @can('roles_view')
            {{-- Start roles --}}
            @php
                $roles_route = ['te_dashboard_roles'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $roles_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $roles_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $roles_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circles" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M6.5 17m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M17.5 17m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Roles') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $roles_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_roles' ? 'active' : '' }}" href="{{ route('te_dashboard_roles') }}">
                        {{ __('All roles') }}
                    </a>
                    </div>
                </div>
                </div>
            </li>
            {{-- End roles --}}
        @endcan

        @can('academic_system_manage_view')
            {{-- Start academic system --}}
            @php
                $acdmc_route = ['te_dashboard_academic_manage', 'te_dashboard_academic_content', 'te_dashboard_academic_specializations', 'te_dashboard_academic_shedule'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $acdmc_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $acdmc_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $acdmc_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-databricks" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 17l9 5l9 -5v-3l-9 5l-9 -5v-3l9 5l9 -5v-3l-9 5l-9 -5l9 -5l5.418 3.01" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Academic system') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $acdmc_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                        {{-- <a class="dropdown-item {{ $pageType === 'te_dashboard_academic_specializations' ? 'active' : '' }}" href="{{ route('te_dashboard_academic_specializations') }}">
                            {{ __('Specializations management') }}
                        </a> --}}
                        @can('academic_system_manage_view')
                            <a class="dropdown-item {{ $pageType === 'te_dashboard_academic_manage' ? 'active' : '' }}" href="{{ route('te_dashboard_academic_manage') }}">
                                {{ __('Academic system management') }}
                            </a>
                        @endcan
                        
                        @can('academic_content_manage_view')
                            <a class="dropdown-item {{ $pageType === 'te_dashboard_academic_content' ? 'active' : '' }}" href="{{ route('te_dashboard_academic_content') }}">
                                {{ __('Academic content management') }}
                            </a>
                        @endcan

                        @can('academic_schedule_manage_view')
                            <a class="dropdown-item {{ $pageType === 'te_dashboard_academic_shedule' ? 'active' : '' }}" href="{{ route('te_dashboard_academic_shedule') }}">
                                {{ __('الجدول الدراسي') }}
                            </a>
                        @endcan

                    </div>
                </div>
                </div>
            </li>
            {{-- Start academic system --}}
        @endcan

        @can('students_affairs_manage_view')
            {{-- Start Students Affairs --}}
            @php
                $students_route = ['te_dashboard_academic', 'te_dashboard_prospective_students', 'te_dashboard_student_registration'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $students_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $students_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $students_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users-group" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" /><path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M17 10h2a2 2 0 0 1 2 2v1" /><path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M3 13v-1a2 2 0 0 1 2 -2h2" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Students Affairs') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $students_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_prospective_students' ? 'active' : '' }}" href="{{ route('te_dashboard_prospective_students') }}">
                        {{ __('Prospective students') }}
                    </a>
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_student_registration' ? 'active' : '' }}" href="{{ route('te_dashboard_student_registration') }}">
                        {{ __('Students under registration') }}
                    </a>
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_academic' ? 'active' : '' }}" href="{{ route('te_dashboard_academic') }}">
                        {{ __('Students') }}
                    </a>
                    </div>
                </div>
                </div>
            </li>
            {{-- Start Students Affairs --}}
        @endcan

        @can('personnel_affairs_manage_view')
            {{-- Start Personnel Affairs --}}
            @php
                $pesonnel_route = ['te_dashboard_users_trainers'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $pesonnel_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $pesonnel_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $pesonnel_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-shield" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 21v-2a4 4 0 0 1 4 -4h2" /><path d="M22 16c0 4 -2.5 6 -3.5 6s-3.5 -2 -3.5 -6c1 0 2.5 -.5 3.5 -1.5c1 1 2.5 1.5 3.5 1.5z" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Personnel Affairs') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $pesonnel_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_users_trainers' ? 'active' : '' }}" href="{{ route('te_dashboard_users_trainers') }}">
                        {{ __('المدربين') }}
                    </a>

                    </div>
                </div>
                </div>
            </li>
            {{-- Start academic system --}}
        @endcan

        @can('financial_system_manage_view')
            {{-- Start Financial system --}}
            @php
                $financial_route = ['te_dashboard_financial'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $financial_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $financial_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $financial_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-coins"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" /><path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" /><path d="M3 6c0 1.072 1.144 2.062 3 2.598s4.144 .536 6 0c1.856 -.536 3 -1.526 3 -2.598c0 -1.072 -1.144 -2.062 -3 -2.598s-4.144 -.536 -6 0c-1.856 .536 -3 1.526 -3 2.598z" /><path d="M3 6v10c0 .888 .772 1.45 2 2" /><path d="M3 11c0 .888 .772 1.45 2 2" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Financial system management') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $financial_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_financial' ? 'active' : '' }}" href="{{ route('te_dashboard_financial') }}">
                        {{ __('Financial system') }}
                    </a>

                    </div>
                </div>
                </div>
            </li>
            {{-- Start academic system --}}
        @endcan

        @can('personnel_affairs_manage_view')
            {{-- Start Reports and statistics --}}
            @php
                $reports_route = ['te_dashboard_reports_statistics_manage', 'te_dashboard_reports_statistics_subjects', 'te_dashboard_reports_statistics_students'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $reports_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $reports_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $reports_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chart-infographic"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M7 3v4h4" /><path d="M9 17l0 4" /><path d="M17 14l0 7" /><path d="M13 13l0 8" /><path d="M21 12l0 9" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Reports and statistics') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $reports_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_reports_statistics_subjects' ? 'active' : '' }}" href="{{ route('te_dashboard_reports_statistics_subjects') }}">
                        {{ __('Subject file and reports') }}
                    </a>
                    <a class="dropdown-item {{ $pageType === 'te_dashboard_reports_statistics_students' ? 'active' : '' }}" href="{{ route('te_dashboard_reports_statistics_students') }}">
                        {{ __('Student file and reports') }}
                    </a>

                    </div>
                </div>
                </div>
            </li>
            {{-- Start Reports and statistics --}}
        @endcan

        @can('notification_manage_view')
            {{-- Start Personnel Affairs --}}
            @php
                $notification_route = ['te_admin_notification_templates', 'te_admin_notification_manage', 'te_admin_notification_templates_update'];
            @endphp

            <li class="nav-item dropdown {{ in_array($pageType, $notification_route) ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle {{ in_array($pageType, $notification_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $notification_route) ? 'true' : 'false' }}">
                <span class="nav-link-icon d-md-none d-lg-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bell-cog" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17h-8a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6a2 2 0 1 1 4 0a7 7 0 0 1 4 6v.5" /><path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M19.001 15.5v1.5" /><path d="M19.001 21v1.5" /><path d="M22.032 17.25l-1.299 .75" /><path d="M17.27 20l-1.3 .75" /><path d="M15.97 17.25l1.3 .75" /><path d="M20.733 20l1.3 .75" /><path d="M9 17v1a3 3 0 0 0 3 3" /></svg>
                </span>
                <span class="nav-link-title">
                    {{ __('Messages and notifications') }}
                </span>
                </a>
                <div class="dropdown-menu {{ in_array($pageType, $notification_route) ? 'show' : '' }}">
                <div class="dropdown-menu-columns">
                    <div class="dropdown-menu-column">
                    <a class="dropdown-item {{ $pageType === 'te_admin_notification_templates' ? 'active' : '' }}" href="{{ route('te_admin_notification_templates') }}">
                        {{ __('Notification Template') }}
                    </a>
                    <a class="dropdown-item {{ $pageType === 'te_admin_notification_manage' ? 'active' : '' }}" href="{{ route('te_admin_notification_manage') }}">
                        {{ __('Manage notifications') }}
                    </a>

                    </div>
                </div>
                </div>
            </li>
            {{-- Start academic system --}}
        @endcan

        @can('settings_view')
        {{-- Start settings --}}
        @php
            $settings_route = ['te_admin_settings', 'te_admin_menus', 'te_admin_social', 'te_admin_maintenance', 'te_admin_appterms', 'te_admin_apppolicy'];
        @endphp
        @php
            $plugin_route = [ 'te_admin_plugins'];
        @endphp

        <li class="nav-item dropdown {{ in_array($pageType, $plugin_route) ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle {{ in_array($pageType, $plugin_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $settings_route) ? 'true' : 'false' }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-plus"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 10a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M6 4v4" /><path d="M6 12v8" /><path d="M13.958 15.592a2 2 0 1 0 -1.958 2.408" /><path d="M12 4v10" /><path d="M12 18v2" /><path d="M16 7a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M18 4v1" /><path d="M18 9v3" /><path d="M16 19h6" /><path d="M19 16v6" /></svg>            </span>
            <span class="nav-link-title">
                {{ __('Plugins') }}
            </span>
            </a>
            <div class="dropdown-menu {{ in_array($pageType, $plugin_route) ? 'show' : '' }}">
            <div class="dropdown-menu-columns">
                <div class="dropdown-menu-column">
                <a class="dropdown-item {{ $pageType === 'te_admin_plugins' ? 'active' : '' }}" href="{{ route('te_admin_plugins') }}">
                    {{ __('Plugins Manage') }}
                </a>
                </div>
            </div>
            </div>
        </li>

        <li class="nav-item dropdown {{ in_array($pageType, $settings_route) ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle {{ in_array($pageType, $settings_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $settings_route) ? 'true' : 'false' }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" /></svg>
            </span>
            <span class="nav-link-title">
                {{ __('Settings') }}
            </span>
            </a>
            <div class="dropdown-menu {{ in_array($pageType, $settings_route) ? 'show' : '' }}">
            <div class="dropdown-menu-columns">
                <div class="dropdown-menu-column">
                <a class="dropdown-item {{ $pageType === 'te_admin_settings' ? 'active' : '' }}" href="{{ route('te_admin_settings') }}">
                    {{ __('General settings') }}
                </a>
                <a class="dropdown-item {{ $pageType === 'te_admin_menus' ? 'active' : '' }}" href="{{ route('te_admin_menus') }}">
                    {{ __('Menus settings') }}
                </a>
                <a class="dropdown-item {{ $pageType === 'te_admin_social' ? 'active' : '' }}" href="{{ route('te_admin_social') }}">
                    {{ __('Social media') }}
                </a>
                <a class="dropdown-item {{ $pageType === 'te_admin_maintenance' ? 'active' : '' }}" href="{{ route('te_admin_maintenance') }}">
                    {{ __('Maintenance mode') }}
                </a>
                <a class="dropdown-item {{ $pageType === 'te_admin_appterms' ? 'active' : '' }}" href="{{ route('te_admin_appterms') }}">
                    {{ __('Terms of Service') }}
                </a>
                <a class="dropdown-item {{ $pageType === 'te_admin_apppolicy' ? 'active' : '' }}" href="{{ route('te_admin_apppolicy') }}">
                    {{ __('Privacy Policy') }}
                </a>
                </div>
            </div>
            </div>
        </li>
        {{-- End settings --}}
        @endcan

        @can('settings_view')
        {{-- Start support --}}
        @php
            $support_route = ['te_support_contact_us'];
        @endphp

        <li class="nav-item dropdown {{ in_array($pageType, $support_route) ? 'active' : '' }}">
            <a class="nav-link dropdown-toggle {{ in_array($pageType, $support_route) ? 'show' : '' }}" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ in_array($pageType, $support_route) ? 'true' : 'false' }}">
            <span class="nav-link-icon d-md-none d-lg-inline-block">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-help-octagon"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12.802 2.165l5.575 2.389c.48 .206 .863 .589 1.07 1.07l2.388 5.574c.22 .512 .22 1.092 0 1.604l-2.389 5.575c-.206 .48 -.589 .863 -1.07 1.07l-5.574 2.388c-.512 .22 -1.092 .22 -1.604 0l-5.575 -2.389a2.036 2.036 0 0 1 -1.07 -1.07l-2.388 -5.574a2.036 2.036 0 0 1 0 -1.604l2.389 -5.575c.206 -.48 .589 -.863 1.07 -1.07l5.574 -2.388a2.036 2.036 0 0 1 1.604 0z" /><path d="M12 16v.01" /><path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" /></svg>
            </span>
            <span class="nav-link-title">
                {{ __('Support') }}
            </span>
            </a>
            <div class="dropdown-menu {{ in_array($pageType, $support_route) ? 'show' : '' }}">
            <div class="dropdown-menu-columns">
                <div class="dropdown-menu-column">
                <a class="dropdown-item {{ $pageType === 'te_support_contact_us' ? 'active' : '' }}" href="{{ route('te_support_contact_us') }}">
                    {{ __('Contact us form') }}
                </a>
                {{-- <a class="dropdown-item {{ $pageType === 'te_admin_menus' ? 'active' : '' }}" href="{{ route('te_admin_menus') }}">
                    {{ __('Menus support') }}
                </a>
                <a class="dropdown-item {{ $pageType === 'te_admin_social' ? 'active' : '' }}" href="{{ route('te_admin_social') }}">
                    {{ __('Social media') }}
                </a> --}}
                </div>
            </div>
            </div>
        </li>
        {{-- End support --}}
        @endcan
            <li class="nav-item">
                <a class="nav-link dropdown-toggle" href="javascript:void(0)" style="color: red">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg style="color: red" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-logout"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M9 12h12l-3 -3" /><path d="M18 15l3 -3" /></svg>
                    </span>
                    <span class="nav-link-title">
                        <form method="POST" action="{{ route('logout') }}" class="inline" id="logout">
                            @csrf
                            <button  class="btn dropdown-item" style="gap: 3px;justify-content: flex-start;">
                                <b>{{ __('Log Out') }}</b>
                            </button>
                        </form>
                    </span>
                </a>
                
            </li>
            <li class="nav-item">
                <a class="nav-link dropdown-toggle" href="{{route('clear.cache')}}" style="color: red">
                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-clock-play"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 7v5l2 2" /><path d="M17 22l5 -3l-5 -3z" /><path d="M13.017 20.943a9 9 0 1 1 7.831 -7.292" /></svg>
                    </span>
                    تحديث الكاش
                </a>
                
            </li>
        </ul>
    </div>
    </div>
</aside>