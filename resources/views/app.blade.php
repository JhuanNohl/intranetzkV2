<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ config('app.name', 'IntranetZK') }}</title>

    <script>
        (() => {
            const stored = localStorage.getItem('intranetzk.theme') || 'dark';
            document.documentElement.dataset.theme = stored;
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-inertia::head />
</head>

<body class="min-h-screen bg-zinc-50 text-zinc-950 antialiased dark:bg-zinc-950 dark:text-zinc-50 dark:[color-scheme:dark]">
    <x-inertia::app />
</body>

</html>