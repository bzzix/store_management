@extends('dashboard.layouts.master')

@section('title', 'لوحة القيادة | عبد الستار للزراعة')
@section('breadcrumb', 'نظرة عامة')

@section('content')
<!-- Welcome Banner -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-2xl lg:text-3xl font-extrabold text-surface-900 tracking-tight">مرحباً بك في لوحة القيادة 👋</h2>
        <p class="text-surface-500 mt-1 text-sm font-medium">نظرة سريعة على أداء المتجر لليوم، {{ \Carbon\Carbon::now()->format('l d F Y') }}.</p>
    </div>
    <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-xl border border-surface-200 shadow-sm text-sm font-bold">
        <span class="w-2 h-2 rounded-full bg-secondary-500 animate-pulse"></span>
        السنة المالية {{ date('Y') }} (نشطة)
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
    <!-- Stat Card 1 -->
    <div class="bg-white rounded-2xl p-5 border border-surface-200/60 shadow-soft card-hover relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-50 rounded-full blur-2xl group-hover:bg-primary-100 transition-colors"></div>
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <p class="text-surface-500 text-xs font-bold mb-1 uppercase tracking-wider">إجمالي المبيعات</p>
                <h3 class="text-3xl font-display font-bold text-surface-900 tracking-tight">125,480 <span class="text-sm font-sans text-surface-500">ر.س</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center text-primary-600">
                <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-secondary-600 bg-secondary-50 w-fit px-2 py-0.5 rounded border border-secondary-100">
            <svg class="w-3 h-3 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            +12% زيادة عن أمس
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="bg-white rounded-2xl p-5 border border-surface-200/60 shadow-soft card-hover relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-50 rounded-full blur-2xl group-hover:bg-orange-100 transition-colors"></div>
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <p class="text-surface-500 text-xs font-bold mb-1 uppercase tracking-wider">إجمالي المشتريات</p>
                <h3 class="text-3xl font-display font-bold text-surface-900 tracking-tight">84,200 <span class="text-sm font-sans text-surface-500">ر.س</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-surface-500">
            <span class="text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded font-display">5</span> فواتير جديدة اليوم
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="bg-white rounded-2xl p-5 border border-surface-200/60 shadow-soft card-hover relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-50 rounded-full blur-2xl group-hover:bg-purple-100 transition-colors"></div>
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <p class="text-surface-500 text-xs font-bold mb-1 uppercase tracking-wider">صافي الأرباح</p>
                <h3 class="text-3xl font-display font-bold text-secondary-600 tracking-tight">41,280 <span class="text-sm font-sans text-surface-400">ر.س</span></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                </svg>
            </div>
        </div>
        <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-red-600 bg-red-50 w-fit px-2 py-0.5 rounded border border-red-100">
            <svg class="w-3 h-3 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"></path>
            </svg>
            -2% اليوم
        </div>
    </div>

    <!-- Stat Card 4 -->
    <div class="bg-surface-900 rounded-2xl p-5 shadow-soft card-hover relative overflow-hidden group text-white">
        <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-primary-500 opacity-30 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex items-start justify-between">
            <div>
                <p class="text-surface-400 text-xs font-bold mb-1 uppercase tracking-wider">أصناف منخفضة المخزون</p>
                <h3 class="text-3xl font-display font-bold text-white tracking-tight">14</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white border border-white/10">
                <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <button class="text-xs font-bold text-primary-400 hover:text-primary-300 flex items-center gap-1 transition-colors group">
                عرض الأصناف المهددة بالنفاد
                <svg class="w-3 h-3 stroke-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Two Columns Section -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Main Widget: Groups & Schedule -->
    <div class="bg-white border border-surface-200/60 rounded-2xl shadow-soft xl:col-span-2 flex flex-col">
        <div class="p-6 border-b border-surface-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-surface-900">أحدث فواتير المبيعات</h3>
                <p class="text-sm text-surface-500 mt-1">قائمة بآخر العمليات التي تمت في المتجر.</p>
            </div>
            <button class="bg-surface-50 hover:bg-surface-100 text-surface-600 px-3 py-1.5 rounded-lg text-sm font-bold border border-surface-200 transition-colors">تصدير تقرير</button>
        </div>
        <div class="p-0 overflow-x-auto flex-1">
            <table class="w-full text-sm text-right whitespace-nowrap">
                <thead class="text-xs text-surface-500 uppercase bg-surface-50/50">
                    <tr>
                        <th class="px-6 py-4 font-bold">رقم الفاتورة</th>
                        <th class="px-6 py-4 font-bold">العميل</th>
                        <th class="px-6 py-4 font-bold">الوقت</th>
                        <th class="px-6 py-4 font-bold">الإجمالي</th>
                        <th class="px-6 py-4 font-bold">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-100">
                    <tr class="hover:bg-surface-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-surface-900">INV-2023-001</p>
                            <p class="text-xs text-surface-500 mt-0.5">مبيعات نقدية</p>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-primary-100 text-primary-700 font-bold text-[10px] flex items-center justify-center">ع.م</div>
                                <span class="font-medium text-surface-700">علي محمود</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-display font-medium text-surface-600 bg-surface-50 px-2 py-1 rounded">04:00 PM</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-display font-bold text-surface-900">1,250.00</span>
                                <span class="text-[10px] text-surface-400">ر.س</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-secondary-50 text-secondary-700 border border-secondary-200 text-xs font-bold px-2 py-1 rounded min-w-[80px] text-center inline-block">مدفوعة</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Side Widget: Form/Quick Actions -->
    <div class="bg-white border border-surface-200/60 rounded-2xl shadow-soft flex flex-col">
        <div class="p-6 border-b border-surface-100">
            <h3 class="text-lg font-bold text-surface-900">إجراءات سريعة</h3>
            <p class="text-sm text-surface-500 mt-1">تسهيل العمليات اليومية بالنظام.</p>
        </div>
        <div class="p-6 flex-1 flex flex-col gap-3">
            <div>
                <label class="block text-sm font-bold text-surface-700 mb-1.5">إرسال عرض سعر سريع</label>
                <div class="flex gap-2">
                    <input type="text" placeholder="رقم هاتف العميل..." class="flex-1 bg-surface-50 border border-surface-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition-all">
                    <button class="bg-surface-800 text-white p-2 rounded-lg hover:bg-surface-900 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <hr class="border-surface-100 my-2">
            <div class="grid grid-cols-2 gap-3">
                <button class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-surface-200 bg-surface-50 hover:bg-primary-50 hover:border-primary-200 hover:text-primary-700 transition-all group">
                    <span class="bg-white p-2 rounded-lg shadow-sm text-surface-500 group-hover:text-primary-600 group-hover:shadow-primary-500/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </span>
                    <span class="text-xs font-bold">منتج جديد</span>
                </button>
                <button class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-surface-200 bg-surface-50 hover:bg-orange-50 hover:border-orange-200 hover:text-orange-700 transition-all group">
                    <span class="bg-white p-2 rounded-lg shadow-sm text-surface-500 group-hover:text-orange-600 group-hover:shadow-orange-500/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </span>
                    <span class="text-xs font-bold">مورد جديد</span>
                </button>
                <button class="flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-surface-200 bg-surface-50 hover:bg-purple-50 hover:border-purple-200 hover:text-purple-700 transition-all group col-span-2">
                    <span class="bg-white p-2 rounded-lg shadow-sm text-surface-500 group-hover:text-purple-600 group-hover:shadow-purple-500/10">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </span>
                    <span class="text-xs font-bold">إضافة شريحة تسعير</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection