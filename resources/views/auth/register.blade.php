<x-guest-layout>
    <div class="w-full max-w-lg">
        <!-- Branding -->
        <div class="text-center mb-8">
            @if(get_setting('appIcon'))
                <img src="{{ get_setting('appIcon') }}" alt="Logo" class="h-16 w-auto mx-auto mb-4 drop-shadow-xl">
            @else
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-primary-600 text-white text-2xl font-black mb-4 shadow-lg shadow-primary-200">
                    {{ mb_substr(get_setting('appName', 'A'), 0, 1) }}
                </div>
            @endif
            <h1 class="text-2xl font-black text-surface-900">{{ get_setting('appName', 'عبد الستار للزراعة') }}</h1>
            <p class="text-surface-500 mt-2 font-medium">{{ __('أنشئ حسابك الجديد للبدء') }}</p>
        </div>

        <!-- Register Card -->
        <div class="glass-panel rounded-3xl shadow-2xl shadow-surface-200/50 p-8 lg:p-10">
            <x-validation-errors class="mb-6 bg-red-50 p-4 rounded-xl text-red-600 text-sm border border-red-100" />

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-bold text-surface-700 mb-2">{{ __('الاسم الكامل') }}</label>
                    <input id="name" 
                           class="w-full px-4 py-3 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium placeholder-surface-400" 
                           type="text" name="name" :value="old('name')" required autofocus autocomplete="name" 
                           placeholder="John Doe" />
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-surface-700 mb-2">{{ __('البريد الإلكتروني') }}</label>
                    <input id="email" 
                           class="w-full px-4 py-3 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium placeholder-surface-400" 
                           type="email" name="email" :value="old('email')" required autocomplete="username" 
                           placeholder="name@example.com" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-bold text-surface-700 mb-2">{{ __('كلمة المرور') }}</label>
                        <input id="password" 
                               class="w-full px-4 py-3 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium" 
                               type="password" name="password" required autocomplete="new-password" 
                               placeholder="••••••••" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-bold text-surface-700 mb-2">{{ __('تأكيد كلمة المرور') }}</label>
                        <input id="password_confirmation" 
                               class="w-full px-4 py-3 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium" 
                               type="password" name="password_confirmation" required autocomplete="new-password" 
                               placeholder="••••••••" />
                    </div>
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div>
                        <label for="terms" class="inline-flex items-center cursor-pointer group">
                            <input id="terms" type="checkbox" name="terms" class="w-5 h-5 rounded-lg border-2 border-surface-300 text-primary-600 focus:ring-primary-500 transition-all" required>
                            <span class="ms-3 text-sm font-bold text-surface-600 group-hover:text-surface-900 transition-colors">
                                {!! __('أوافق على :terms_of_service و :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-primary-600 hover:underline">'.__('شروط الخدمة').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-primary-600 hover:underline">'.__('سياسة الخصوصية').'</a>',
                                ]) !!}
                            </span>
                        </label>
                    </div>
                @endif

                <div>
                    <button type="submit" class="w-full btn-primary font-black py-4 px-6 rounded-2xl transition-all duration-300 transform active:scale-[0.98]">
                        {{ __('إنشاء الحساب') }}
                    </button>
                </div>

                <div class="text-center pt-4 border-t border-surface-100">
                    <p class="text-sm text-surface-500 font-medium">
                        {{ __('لديك حساب بالفعل؟') }} 
                        <a href="{{ route('login') }}" class="text-primary-600 font-bold hover:underline">{{ __('تسجيل الدخول') }}</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

