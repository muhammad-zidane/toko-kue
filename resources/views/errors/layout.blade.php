<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue — @yield('title', 'Error')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink: #F0507A;
            --brown-dark: #2C1810;
            --brown-mid: #5C3D2E;
            --cream: #FFF8EE;
            --cream-dark: #F5EDD8;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--cream); }
        .font-display { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center px-4">
    <div class="text-center max-w-md">
        <a href="{{ route('home') }}" class="inline-block mb-8">
            <span class="font-display text-2xl font-bold" style="color: var(--brown-dark);">Jagoan Kue</span>
        </a>
        @yield('content')
        <a href="{{ route('home') }}"
           class="mt-8 inline-block px-6 py-3 rounded-full text-white font-semibold transition hover:opacity-90"
           style="background-color: var(--pink);">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
