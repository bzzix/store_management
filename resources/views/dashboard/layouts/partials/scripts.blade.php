<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Cairo', 'sans-serif'],
                    display: ['Outfit', 'sans-serif'],
                },
                colors: {
                    primary: {
                        50: 'var(--color-primary-50)',
                        100: 'var(--color-primary-100)',
                        500: 'var(--color-primary-500)',
                        600: 'var(--color-primary-600)',
                        700: 'var(--color-primary-700)',
                    },
                    secondary: {
                        50: 'var(--color-secondary-50)',
                        500: 'var(--color-secondary-500)',
                        600: 'var(--color-secondary-600)',
                    },
                    surface: {
                        50: 'var(--color-surface-50)',
                        100: 'var(--color-surface-100)',
                        200: 'var(--color-surface-200)',
                        300: 'var(--color-surface-300)',
                        400: 'var(--color-surface-400)',
                        500: 'var(--color-surface-500)',
                        600: 'var(--color-surface-600)',
                        700: 'var(--color-surface-700)',
                        800: 'var(--color-surface-800)',
                        900: 'var(--color-surface-900)',
                    }
                },
                boxShadow: {
                    'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.05)',
                    'soft': '0 10px 40px -10px rgba(0,0,0,0.08)',
                }
            }
        }
    }
</script>

<!-- iziToast JS -->
<script src="https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js"></script>

<!-- Session Notifications -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            iziToast.success({
                title: '{{ __("Success") }}',
                message: '{{ session("success") }}',
                position: 'topLeft',
                rtl: true,
                theme: 'light',
                progressBarColor: 'var(--color-primary-500)',
            });
        @endif

        @if(session('error'))
            iziToast.error({
                title: '{{ __("Error") }}',
                message: '{{ session("error") }}',
                position: 'topLeft',
                rtl: true,
                theme: 'light',
            });
        @endif

        @if(session('info'))
            iziToast.info({
                title: '{{ __("Info") }}',
                message: '{{ session("info") }}',
                position: 'topLeft',
                rtl: true,
                theme: 'light',
            });
        @endif
    });

    // Listen for Livewire notifications
    window.addEventListener('notify', event => {
        let data = event.detail;
        if (Array.isArray(data)) {
            data = data[0];
        }
        
        if (iziToast[data.type]) {
            iziToast[data.type]({
                title: data.title,
                message: data.msg || data.message,
                position: 'topLeft',
                rtl: true,
                theme: 'light',
                progressBarColor: 'var(--color-primary-500)',
            });
        }
    });
</script>

@stack('js')
