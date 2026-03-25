<!-- Sidebar -->
<aside id="sidebar"
    :class="{'sidebar-collapsed': !sidebarOpen, 'translate-x-0': mobileMenuOpen, 'translate-x-full lg:translate-x-0': !mobileMenuOpen}"
    class="w-72 h-full glass-panel shadow-glass flex flex-col justify-between py-6 px-4 z-50 fixed lg:relative right-0 border-l border-white transition-transform duration-300">

    <div class="flex-1 overflow-y-auto overflow-x-hidden no-scrollbar">
        <!-- Logo area -->
        <div class="flex items-center gap-3 px-2 mb-10 h-10">
            @if(get_setting('appLogo'))
                <img src="{{ get_setting('appLogo') }}" alt="Logo" class="h-10 w-10 flex-shrink-0 object-contain rounded-lg">
            @else
                <div class="h-10 w-10 flex-shrink-0 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-display font-bold text-xl shadow-lg shadow-primary-500/30">
                    {{ mb_substr(get_setting('appName', 'ع'), 0, 1) }}
                </div>
            @endif
            <div class="logo-text min-w-[150px]">
                <h1 class="font-bold text-xl tracking-tight text-surface-900 leading-none">{{ get_setting('appName', 'عبد الستار للزراعة') }}</h1>
                <p class="text-[10px] text-surface-500 font-bold mt-1 tracking-wider uppercase">نظام الإدارة المتكامل</p>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="space-y-1.5 px-2">
            <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mb-3 mt-4 nav-text px-2">
                الرئيسية</p>

            <a href="{{ route('dashboard.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.index') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100' }} transition-colors group">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="nav-text">لوحة القيادة</span>
            </a>

            @can('products_view')
            <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mb-3 mt-6 nav-text px-2">
                المخزون والمنتجات</p>

            <a href="{{ route('dashboard.products.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.products.index') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }} transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
                <span class="nav-text">إدارة الأصناف</span>
            </a>

            <a href="{{ route('dashboard.products.categories') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.products.categories') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }} transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <span class="nav-text">تصنيفات المنتجات</span>
            </a>
            @endcan

            @can('inventory_view')
            <a href="{{ route('dashboard.warehouses.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.warehouses.index') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }} transition-colors font-medium cursor-pointer group justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    <span class="nav-text">جرد المخازن</span>
                </div>
            </a>
            @endcan

            <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mb-3 mt-6 nav-text px-2">
                المبيعات والمشتريات</p>

            @can('sales_view')
            <a href="{{ route('dashboard.sales.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.sales.*') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }} transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                    </path>
                </svg>
                <span class="nav-text">فواتير المبيعات</span>
            </a>
            @endcan

            @can('sales_create')
            <a href="#" onclick="Livewire.dispatch('create-invoice')"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-secondary-50 text-secondary-700 font-bold transition-colors group border border-secondary-100 cursor-pointer">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span class="nav-text">نظام الكاشير (POS)</span>
            </a>
            @endcan

            @can('pricing_tiers_view')
            <a href="{{ route('dashboard.pricing.tiers.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.pricing.tiers.index') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }} transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                </svg>
                <span class="nav-text">شرائح التسعير الذكي</span>
            </a>
            @endcan

            @can('purchase_invoices_view')
            <a href="{{ route('dashboard.suppliers.purchases.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.suppliers.purchases.index') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100' }} hover:text-surface-900 transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="nav-text">فواتير المشتريات</span>
            </a>
            @endcan

            <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mb-3 mt-6 nav-text px-2">
                شركاء العمل</p>

            @can('suppliers_view')
            <a href="{{ route('dashboard.suppliers.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.suppliers.index') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100' }} hover:text-surface-900 transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <span class="nav-text">الموردين</span>
            </a>
            @endcan

            @can('customers_view')
            <a href="{{ route('dashboard.customers.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.customers.*') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }} transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <span class="nav-text">العملاء</span>
            </a>
            @endcan

            <a href="#"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-surface-600 hover:bg-surface-100 hover:text-surface-900 transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="nav-text">تقارير الأرباح</span>
            </a>

            @can('settings_view')
            <p class="text-[10px] font-bold text-surface-400 uppercase tracking-widest mb-3 mt-6 nav-text px-2">
                النظام</p>
            <a href="{{ route('dashboard.settings') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('dashboard.settings') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900' }} transition-colors font-medium cursor-pointer group">
                <svg class="w-5 h-5 flex-shrink-0 group-hover:text-primary-600 transition-colors" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="nav-text">الإعدادات العامة</span>
            </a>
            @endcan
        </nav>
    </div>

    <!-- User Profile -->
    <div
        class="mt-4 p-2 bg-surface-50 rounded-xl border border-surface-200/50 flex items-center gap-3 cursor-pointer hover:bg-surface-100 transition-colors">
        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=3b82f6&color=fff&rounded=true&bold=true"
            class="w-10 h-10 rounded-full flex-shrink-0" alt="User Avatar">
        <div class="flex-1 min-w-0 profile-details">
            <p class="text-sm font-bold text-surface-900 truncate">{{ auth()->user()->name ?? 'مدير النظام' }}</p>
            <p class="text-[11px] text-primary-600 font-bold truncate">{{ auth()->user()->roles->first()->display_name ?? 'Super Admin' }}</p>
        </div>
    </div>
</aside>
