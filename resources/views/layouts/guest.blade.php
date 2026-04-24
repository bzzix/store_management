<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts: Cairo for body, Outfit for numbers/display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles

    <style>
        :root {
            /* 1. Primary Color (Blue) */
            --color-primary-50: #eff6ff;
            --color-primary-100: #dbeafe;
            --color-primary-500: #3b82f6;
            --color-primary-600: #2563eb;
            --color-primary-700: #1d4ed8;

            /* 2. Secondary Color (Teal/Emerald) */
            --color-secondary-50: #ecfdf5;
            --color-secondary-500: #10b981;
            --color-secondary-600: #059669;

            /* 3. Surface Color (Slate/Gray) */
            --color-surface-50: #f8fafc;
            --color-surface-100: #f1f5f9;
            --color-surface-200: #e2e8f0;
            --color-surface-300: #cbd5e1;
            --color-surface-400: #94a3b8;
            --color-surface-500: #64748b;
            --color-surface-600: #475569;
            --color-surface-700: #334155;
            --color-surface-800: #1e293b;
            --color-surface-900: #0f172a;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--color-surface-100);
            background-image: radial-gradient(var(--color-surface-300) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>

<body class="antialiased text-surface-900">
    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>

    @livewireScripts
</body>

</html>

