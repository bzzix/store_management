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
            <p class="text-surface-500 mt-2 font-medium">{{ __('تحقق من بريدك الإلكتروني') }}</p>
        </div>

        <!-- Verify Email Card -->
        <div class="glass-panel rounded-3xl shadow-2xl shadow-surface-200/50 p-8 lg:p-10">
            <div class="mb-6 text-sm text-surface-600 leading-relaxed font-medium">
                {{ __('قبل المتابعة، هل يمكنك التحقق من عنوان بريدك الإلكتروني من خلال النقر على الرابط الذي أرسلناه إليك للتو؟ إذا لم تتلق البريد الإلكتروني، سنرسل لك رابطاً آخر بكل سرور.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 font-bold text-sm p-4 rounded-xl border" style="color: var(--color-success-600); background-color: var(--color-success-50); border-color: var(--color-success-600);">
                    {{ __('تم إرسال رابط تحقق جديد إلى عنوان البريد الإلكتروني الذي قدمته في إعدادات ملفك الشخصي.') }}
                </div>
            @endif

            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full btn-primary font-black py-4 px-6 rounded-2xl transition-all duration-300 transform active:scale-[0.98]">
                        {{ __('إعادة إرسال بريد التحقق') }}
                    </button>
                </form>

                <div class="flex items-center justify-between pt-4 border-t border-surface-100">
                    <a href="{{ route('profile.show') }}" class="text-sm font-bold text-surface-600 hover:text-primary-600 transition-colors">
                        {{ __('تعديل الملف الشخصي') }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-bold text-red-600 hover:text-red-700 transition-colors">
                            {{ __('تسجيل الخروج') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

