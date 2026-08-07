<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-secondary-dark antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center bg-surface-alt pt-6 sm:pt-0">
            <div>
                <a href="/">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="{{ config('app.name') }}" class="h-14 w-auto">
                </a>
            </div>

            <div class="mt-6 w-full overflow-hidden bg-white px-6 py-4 shadow-md ring-1 ring-app-border sm:max-w-md sm:rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
