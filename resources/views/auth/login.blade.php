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
            <p class="text-surface-500 mt-2 font-medium">{{ __('مرحباً بك مجدداً، يرجى تسجيل الدخول') }}</p>
        </div>

        <!-- Login Card -->
        <div class="glass-panel rounded-3xl shadow-2xl shadow-surface-200/50 p-8 lg:p-10">
            <x-validation-errors class="mb-6 bg-red-50 p-4 rounded-xl text-red-600 text-sm border border-red-100" />

            @session('status')
                <div class="mb-6 font-bold text-sm p-4 rounded-xl border" style="color: var(--color-success-600); background-color: var(--color-success-50); border-color: var(--color-success-600);">
                    {{ $value }}
                </div>
            @endsession

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-bold text-surface-700 mb-2">{{ __('البريد الإلكتروني') }}</label>
                    <input id="email" 
                           class="w-full px-4 py-3.5 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium placeholder-surface-400" 
                           type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                           placeholder="name@example.com" />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-bold text-surface-700">{{ __('كلمة المرور') }}</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors" href="{{ route('password.request') }}">
                                {{ __('نسيت كلمة المرور؟') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" 
                           class="w-full px-4 py-3.5 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium" 
                           type="password" name="password" required autocomplete="current-password" 
                           placeholder="••••••••" />
                </div>

                <div class="flex items-center">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input id="remember_me" type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-2 border-surface-300 text-primary-600 focus:ring-primary-500 transition-all">
                        <span class="ms-3 text-sm font-bold text-surface-600 group-hover:text-surface-900 transition-colors">{{ __('تذكرني على هذا الجهاز') }}</span>
                    </label>
                </div>

                <div>
                    <button type="submit" class="w-full btn-primary font-black py-4 px-6 rounded-2xl transition-all duration-300 transform active:scale-[0.98]">
                        {{ __('تسجيل الدخول') }}
                    </button>
                </div>

                @if (Route::has('register'))
                    <div class="text-center pt-4 border-t border-surface-100">
                        <p class="text-sm text-surface-500 font-medium">
                            {{ __('ليس لديك حساب؟') }} 
                            <a href="{{ route('register') }}" class="text-primary-600 font-bold hover:underline">{{ __('أنشئ حساباً جديداً') }}</a>
                        </p>
                    </div>
                @endif
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-surface-400 text-xs mt-8 font-medium">
            &copy; {{ date('Y') }} {{ get_setting('appName', 'عبد الستار للزراعة') }}. {{ __('جميع الحقوق محفوظة.') }}
        </p>
    </div>
</x-guest-layout>

