<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Miss ESGIS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gradient-to-br from-custom-pink-50 to-custom-purple-50 min-h-screen flex flex-col">
    <div class="min-h-screen flex flex-col">
        @include('components.header')

        <!-- Page Content -->
        <main class="flex-grow">
            @yield('content')
        </main>

        @include('components.footer')
    </div>

    @stack('scripts')
</body>
</html>
