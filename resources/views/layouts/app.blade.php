<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - @yield('title', 'Kue Lezat Dikirim ke Pintumu')</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pink:       #F0507A;
            --brown-dark: #2C1810;
            --brown-mid:  #5C3D2E;
            --cream:      #FFF8EE;
            --cream-dark: #F5EDD8;
            --white:      #FFFFFF;
            --gray:       #6B7280;
            --gray-light: #F3F4F6;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-white">
    {{-- NAVBAR --}}
    <nav style="background-color: var(--brown-dark);" class="sticky top-0 z-50 px-6 py-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between">
            {{-- Logo --}}
            <a href="/" class="text-2xl font-bold" style="color: var(--pink); font-family: 'Playfair Display', serif;">
                Jagoan Kue
            </a>

            {{-- Nav Links --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="/" class="text-white hover:opacity-80 text-sm font-medium">Beranda</a>
                <a href="/products" class="text-white hover:opacity-80 text-sm font-medium">Katalog</a>
                <a href="/orders" class="text-white hover:opacity-80 text-sm font-medium">Pemesanan</a>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <a href="/cart" class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background-color: var(--pink);">
                    🛒 Keranjang
                </a>
                @auth
                    <a href="/profile" class="px-4 py-2 rounded-lg border text-sm font-semibold text-white border-white hover:bg-white hover:text-gray-900 transition">
                        {{ auth()->user()->name }}
                    </a>
                @else
                    <a href="/login" class="px-4 py-2 rounded-lg border text-sm font-semibold text-white border-white hover:bg-white hover:text-gray-900 transition">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    @yield('content')


    {{-- FOOTER --}}
    <footer style="background-color: var(--brown-dark);" class="text-white py-14 px-6 mt-0">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">
            {{-- Brand --}}
            <div>
                <h3 class="text-xl font-bold mb-2" style="color: var(--pink); font-family: 'Playfair Display', serif;">Jagoan Kue</h3>
                <p class="text-sm opacity-70 mb-4">Menyediakan kue dengan cinta sejak 2023</p>
                <div class="flex gap-4 text-lg">
                    <a href="#" class="opacity-70 hover:opacity-100">📸</a>
                    <a href="#" class="opacity-70 hover:opacity-100">🎵</a>
                    <a href="#" class="opacity-70 hover:opacity-100">💬</a>
                    <a href="#" class="opacity-70 hover:opacity-100">👤</a>
                </div>
            </div>

            {{-- Layanan --}}
            <div>
                <h4 class="font-semibold mb-4">Layanan</h4>
                <ul class="space-y-2 text-sm opacity-70">
                    <li><a href="#" class="hover:opacity-100">Katalog Kue</a></li>
                    <li><a href="#" class="hover:opacity-100">Kue Custom</a></li>
                    <li><a href="#" class="hover:opacity-100">Hampers</a></li>
                    <li><a href="#" class="hover:opacity-100">Catering</a></li>
                </ul>
            </div>

            {{-- Selengkapnya --}}
            <div>
                <h4 class="font-semibold mb-4">Selengkapnya</h4>
                <ul class="space-y-2 text-sm opacity-70">
                    <li><a href="#" class="hover:opacity-100">Tentang Kami</a></li>
                    <li><a href="#" class="hover:opacity-100">Blog</a></li>
                    <li><a href="#" class="hover:opacity-100">Karir</a></li>
                </ul>
            </div>

            {{-- Kontak --}}
            <div>
                <h4 class="font-semibold mb-4">Kontak Kami</h4>
                <ul class="space-y-2 text-sm opacity-70">
                    <li>0822-8320-3385</li>
                    <li>muhammadzidane253@gmail.com</li>
                    <li>Payakumbuh, Sumatera Barat</li>
                </ul>
            </div>
        </div>
    </footer>

</body>
</html>
