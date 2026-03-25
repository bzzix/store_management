<div class="max-w-5xl mx-auto space-y-6 pb-20">
    <div class="bg-white rounded-3xl shadow-soft border border-surface-200/60 overflow-hidden flex flex-col">
        <!-- Header -->
        <div class="px-8 py-6 border-b border-surface-100 bg-white flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-surface-900 font-cairo">{{ __('System Settings') }}</h3>
                <p class="text-sm text-surface-500 mt-1 font-cairo">تحكم في هوية المنشأة والبيانات الأساسية للنظام</p>
            </div>
            <div class="p-2.5 rounded-2xl bg-primary-50 text-primary-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37a1.724 1.724 0 0 0 2.572 -1.065z" /><path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                </svg>
            </div>
        </div>

        <!-- Body -->
        <div class="px-8 py-8 space-y-10">
            {{-- Section: Basic Information --}}
            <section class="space-y-6">
                <div class="flex items-center gap-3 border-r-4 border-primary-500 pr-3">
                    <h4 class="text-base font-bold text-surface-900 font-cairo">{{ __('General Information') }}</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5 md:col-span-2">
                        <label class="text-sm font-bold text-surface-700 font-cairo">{{ __('Application Name') }}</label>
                        <input type="text" wire:model="appName" 
                            class="w-full bg-surface-50 border border-surface-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all font-cairo">
                        @error('appName') <span class="text-xs text-red-500 font-cairo">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-surface-700 font-cairo">{{ __('Manager Name') }}</label>
                        <input type="text" wire:model="appManagerName" 
                            class="w-full bg-surface-50 border {{ $errors->has('appManagerName') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all font-cairo">
                        @error('appManagerName') <span class="text-xs text-red-500 font-cairo font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-surface-700 font-cairo">{{ __('Address') }}</label>
                        <input type="text" wire:model="appAddress" 
                            class="w-full bg-surface-50 border {{ $errors->has('appAddress') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all font-cairo">
                        @error('appAddress') <span class="text-xs text-red-500 font-cairo font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <hr class="border-surface-100">

            {{-- Section: Branding --}}
            <section class="space-y-6">
                <div class="flex items-center gap-3 border-r-4 border-secondary-500 pr-3">
                    <h4 class="text-base font-bold text-surface-900 font-cairo">الهوية البصرية واللوجو</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Logo --}}
                    <div class="flex flex-col items-center p-6 bg-surface-50 rounded-3xl border border-surface-100 space-y-4">
                        <label class="text-sm font-bold text-surface-700 font-cairo">{{ __('Main Logo') }}</label>
                        <div class="relative group w-32 h-32 rounded-2xl border-2 border-dashed border-surface-200 bg-white flex items-center justify-center overflow-hidden">
                            @if($newLogo)
                                <img src="{{ $newLogo->temporaryUrl() }}" class="w-full h-full object-contain p-2">
                            @elseif($appLogo)
                                <img src="{{ $appLogo }}" class="w-full h-full object-contain p-2">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-surface-300" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M15 8h.01" /><path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" /><path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" /><path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" /></svg>
                            @endif
                            <label class="absolute inset-0 bg-surface-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
                                <input type="file" wire:model="newLogo" class="hidden">
                                <span class="text-white text-xs font-bold font-cairo" wire:loading.remove wire:target="newLogo">تغيير</span>
                                <svg wire:loading wire:target="newLogo" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </label>
                        </div>
                        <p class="text-[10px] text-surface-400 font-cairo">يظهر في التقارير والفواتير</p>
                        @error('newLogo') <span class="text-xs text-red-500 font-cairo font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Favicon --}}
                    <div class="flex flex-col items-center p-6 bg-surface-50 rounded-3xl border border-surface-100 space-y-4">
                        <label class="text-sm font-bold text-surface-700 font-cairo">أيقونة المتصفح (Favicon)</label>
                        <div class="relative group w-32 h-32 rounded-2xl border-2 border-dashed border-surface-200 bg-white flex items-center justify-center overflow-hidden">
                            @if($newIcon)
                                <img src="{{ $newIcon->temporaryUrl() }}" class="w-full h-full object-contain p-4">
                            @elseif($appIcon)
                                <img src="{{ $appIcon }}" class="w-full h-full object-contain p-4">
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-surface-300" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>
                            @endif
                            <label class="absolute inset-0 bg-surface-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
                                <input type="file" wire:model="newIcon" class="hidden">
                                <span class="text-white text-xs font-bold font-cairo" wire:loading.remove wire:target="newIcon">تغيير</span>
                                <svg wire:loading wire:target="newIcon" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </label>
                        </div>
                        <p class="text-[10px] text-surface-400 font-cairo">تظهر في تبويب المتصفح</p>
                        @error('newIcon') <span class="text-xs text-red-500 font-cairo font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>

            <hr class="border-surface-100">

            {{-- Section: Contact & Financial --}}
            <section class="space-y-6">
                <div class="flex items-center gap-3 border-r-4 border-orange-500 pr-3">
                    <h4 class="text-base font-bold text-surface-900 font-cairo">بيانات الاتصال والنظام</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-surface-700 font-cairo">{{ __('Mobile') }}</label>
                        <input type="text" wire:model="appMobile" dir="ltr"
                            class="w-full bg-surface-50 border {{ $errors->has('appMobile') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all font-outfit font-bold">
                        @error('appMobile') <span class="text-xs text-red-500 font-cairo font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-surface-700 font-cairo">{{ __('Phone') }}</label>
                        <input type="text" wire:model="appPhone" dir="ltr"
                            class="w-full bg-surface-50 border {{ $errors->has('appPhone') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all font-outfit font-bold">
                        @error('appPhone') <span class="text-xs text-red-500 font-cairo font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-surface-700 font-cairo">{{ __('Invoice Prefix') }}</label>
                        <input type="text" wire:model="appInvoicePrefix" 
                            class="w-full bg-surface-50 border {{ $errors->has('appInvoicePrefix') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all font-outfit font-bold">
                        @error('appInvoicePrefix') <span class="text-xs text-red-500 font-cairo font-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-sm font-bold text-surface-700 font-cairo">{{ __('Currency') }}</label>
                        <input type="text" wire:model="appCurrency" 
                            class="w-full bg-surface-50 border {{ $errors->has('appCurrency') ? 'border-red-300 ring-1 ring-red-100' : 'border-surface-200' }} rounded-xl px-4 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none transition-all font-cairo font-bold">
                        @error('appCurrency') <span class="text-xs text-red-500 font-cairo font-bold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <div class="px-8 py-6 bg-surface-50 border-t border-surface-100 flex items-center justify-end gap-3">
            <button wire:click="save" wire:loading.attr="disabled"
                class="bg-primary-600 hover:bg-primary-700 text-white px-10 py-3 rounded-xl text-md font-bold shadow-soft shadow-primary-500/20 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2">
                <span wire:loading.remove wire:target="save">{{ __('Save Settings') }}</span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Saving...') }}
                </span>
            </button>
        </div>
    </div>
</div>
