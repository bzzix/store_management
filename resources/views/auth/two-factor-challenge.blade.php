<x-guest-layout>
    <div class="w-full max-w-md">
        <!-- Branding -->
        <div class="text-center mb-8">
            @if(get_setting('appIcon'))
                <img src="{{ get_setting('appIcon') }}" alt="Logo" class="h-20 w-auto mx-auto mb-4 drop-shadow-xl">
            @else
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-primary-600 text-white text-3xl font-black mb-4 shadow-lg shadow-primary-200">
                    {{ mb_substr(get_setting('appName', 'A'), 0, 1) }}
                </div>
            @endif
            <h1 class="text-2xl font-black text-surface-900">{{ get_setting('appName', 'عبد الستار للزراعة') }}</h1>
            <p class="text-surface-500 mt-2 font-medium">{{ __('التحقق بخطوتين') }}</p>
        </div>

        <!-- 2FA Card -->
        <div class="glass-panel rounded-3xl shadow-2xl shadow-surface-200/50 p-8 lg:p-10" x-data="{ recovery: false }">
            <div class="mb-6 text-sm text-surface-600 leading-relaxed font-medium" x-show="! recovery">
                {{ __('يرجى تأكيد الوصول إلى حسابك عن طريق إدخال رمز المصادقة المقدم من تطبيق المصادقة الخاص بك.') }}
            </div>

            <div class="mb-6 text-sm text-surface-600 leading-relaxed font-medium" x-cloak x-show="recovery">
                {{ __('يرجى تأكيد الوصول إلى حسابك عن طريق إدخال أحد رموز استرداد الطوارئ الخاصة بك.') }}
            </div>

            <x-validation-errors class="mb-6 bg-red-50 p-4 rounded-xl text-red-600 text-sm border border-red-100" />

            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-6">
                @csrf

                <div x-show="! recovery">
                    <label for="code" class="block text-sm font-bold text-surface-700 mb-2">{{ __('رمز المصادقة') }}</label>
                    <input id="code" 
                           class="w-full px-4 py-3.5 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium text-center tracking-widest" 
                           type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" 
                           placeholder="000000" />
                </div>

                <div x-cloak x-show="recovery">
                    <label for="recovery_code" class="block text-sm font-bold text-surface-700 mb-2">{{ __('رمز الاسترداد') }}</label>
                    <input id="recovery_code" 
                           class="w-full px-4 py-3.5 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium text-center tracking-widest" 
                           type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" 
                           placeholder="abcd-1234" />
                </div>

                <div class="flex items-center justify-between pt-4">
                    <button type="button" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors underline"
                                    x-show="! recovery"
                                    x-on:click="
                                        recovery = true;
                                        $nextTick(() => { $refs.recovery_code.focus() })
                                    ">
                        {{ __('استخدام رمز الاسترداد') }}
                    </button>

                    <button type="button" class="text-sm font-bold text-primary-600 hover:text-primary-700 transition-colors underline"
                                    x-cloak
                                    x-show="recovery"
                                    x-on:click="
                                        recovery = false;
                                        $nextTick(() => { $refs.code.focus() })
                                    ">
                        {{ __('استخدام رمز المصادقة') }}
                    </button>

                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-black py-3 px-8 rounded-2xl shadow-lg shadow-primary-200 transition-all duration-300 transform active:scale-[0.98]">
                        {{ __('دخول') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

