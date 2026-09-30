<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Jagoan Kue — Kue Lezat Dikirim ke Pintumu')</title>
    <meta name="description" content="@yield('meta_description', 'Jagoan Kue menyediakan berbagai kue lezat. Pesan sekarang, kirim ke pintumu!')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />

    @include('partials.head-assets')

    <!-- Vite (Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-cream text-text-primary font-sans antialiased">

    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
