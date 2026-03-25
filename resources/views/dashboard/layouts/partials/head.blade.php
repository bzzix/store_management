<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', __('Dashboard')) | {{ get_setting('appName', 'أولاد عبدالستار') }}</title>

<!-- Fonts: Cairo for body, Outfit for numbers/display -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="icon" type="image/x-icon" href="{{ get_setting('appIcon') ?: asset('favicon.ico') }}">

<link rel="stylesheet" href="{{ asset('assets/izitoast/css/iziToast.min.css') }}">

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
        background-color: var(--color-surface-100);
        background-image: radial-gradient(var(--color-surface-300) 1px, transparent 1px);
        background-size: 24px 24px;
    }

    .glass-panel {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px -15px rgba(59, 130, 246, 0.15);
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--color-surface-300);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--color-surface-400);
    }

    /* Sidebar Transition */
    #sidebar {
        transition: width 0.3s ease;
    }

    .nav-text {
        transition: opacity 0.2s ease, width 0.2s ease;
        overflow: hidden;
        white-space: nowrap;
    }

    .sidebar-collapsed {
        width: 5rem !important;
    }

    .sidebar-collapsed .nav-text {
        opacity: 0;
        width: 0;
        display: none;
    }

    .sidebar-collapsed .logo-text {
        opacity: 0;
        width: 0;
        display: none;
    }

    .sidebar-collapsed .profile-details {
        display: none;
    }
</style>

@stack('css')