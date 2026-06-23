<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'IntranetZK') }}</title>

    <script>
        (() => {
            const stored = localStorage.getItem('intranetzk.theme') || 'system';
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldUseDark = stored === 'dark' || (stored === 'system' && prefersDark);

            document.documentElement.dataset.theme = shouldUseDark ? 'dark' : 'light';
            document.documentElement.dataset.themePreference = stored;
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-inertia::head />
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-950 antialiased dark:bg-zinc-950 dark:text-zinc-50">
    <x-inertia::app />
</body>

</html>