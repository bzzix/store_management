<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    @include('dashboard.layouts.partials.head')
    @livewireStyles
</head>

<body class="text-surface-800 antialiased h-screen flex overflow-hidden"
    x-data="{ sidebarOpen: true, mobileMenuOpen: false }">

    <!-- Mobile Overlay -->
    <div x-show="mobileMenuOpen" @click="mobileMenuOpen = false"
        class="fixed inset-0 bg-surface-900/50 z-40 lg:hidden backdrop-blur-sm" x-transition.opacity></div>

    @include('dashboard.layouts.partials.sidebar')

    <!-- Main Content -->
    <main class="flex-1 h-full overflow-y-auto w-full flex flex-col relative transition-all duration-300">
        
        @include('dashboard.layouts.partials.header')

        <!-- Page Content -->
        <div class="p-4 lg:p-8 max-w-[1600px] w-full mx-auto space-y-6 lg:space-y-8 pb-20">
            @yield('content')
        </div>

        @include('dashboard.layouts.partials.footer')

    </main>

    @livewireScripts
    @include('dashboard.layouts.partials.scripts')
</body>

</html>