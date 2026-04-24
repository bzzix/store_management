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
            <p class="text-surface-500 mt-2 font-medium">{{ __('إعادة تعيين كلمة المرور') }}</p>
        </div>

        <!-- Reset Password Card -->
        <div class="glass-panel rounded-3xl shadow-2xl shadow-surface-200/50 p-8 lg:p-10">
            <x-validation-errors class="mb-6 bg-red-50 p-4 rounded-xl text-red-600 text-sm border border-red-100" />

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="block text-sm font-bold text-surface-700 mb-2">{{ __('البريد الإلكتروني') }}</label>
                    <input id="email" 
                           class="w-full px-4 py-3.5 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium placeholder-surface-400" 
                           type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" 
                           placeholder="name@example.com" />
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-surface-700 mb-2">{{ __('كلمة المرور الجديدة') }}</label>
                    <input id="password" 
                           class="w-full px-4 py-3.5 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium" 
                           type="password" name="password" required autocomplete="new-password" 
                           placeholder="••••••••" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-surface-700 mb-2">{{ __('تأكيد كلمة المرور الجديدة') }}</label>
                    <input id="password_confirmation" 
                           class="w-full px-4 py-3.5 bg-surface-50 border-2 border-surface-200 rounded-2xl focus:border-primary-500 focus:ring-0 transition-all duration-200 font-medium" 
                           type="password" name="password_confirmation" required autocomplete="new-password" 
                           placeholder="••••••••" />
                </div>

                <div>
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-black py-4 px-6 rounded-2xl shadow-lg shadow-primary-200 transition-all duration-300 transform active:scale-[0.98]">
                        {{ __('إعادة تعيين كلمة المرور') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

