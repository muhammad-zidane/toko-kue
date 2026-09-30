<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jagoan Kue - Login</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />

    <!-- Google Fonts: Plus Jakarta Sans + Cormorant Garamond -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Vite (Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gradient-to-br from-cream to-cream-warm relative font-sans">
    <!-- Decorative background elements -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-12">
        <div class="bg-white rounded-3xl border border-cream-border shadow-lg p-8 sm:p-10 w-full max-w-md relative z-10">
        <!-- Logo -->
        <div class="text-center mb-8">
            <span class="text-4xl mb-2 block">🎂</span>
            <a href="/" class="font-heading text-3xl font-bold text-brown-dark hover:text-primary transition-colors">Jagoan Kue</a>
            <p class="text-sm text-text-secondary mt-1">Silakan masuk ke akun Anda</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-3.5 text-sm mb-5">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-1.5"><i class="fas fa-exclamation-circle text-xs shrink-0"></i> {{ $error }}</p>
            @endforeach
        </div>
        @endif

        @if (session('status'))
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-3.5 text-sm mb-5 flex items-center gap-2">
            <i class="fas fa-check-circle text-xs shrink-0"></i> <span>{{ session('status') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-5">
                <label for="email" class="input-label">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}" required autofocus autocomplete="username"
                       class="input-field" placeholder="email@contoh.com">
                @error('email') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="input-label">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                           required autocomplete="current-password"
                           class="input-field pr-10" placeholder="••••••••">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-brown-light hover:text-primary cursor-pointer text-sm" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Forgot Password link -->
            <div class="flex justify-end mb-6">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-text-secondary hover:text-primary transition-colors hover:underline">Lupa Password?</a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3.5 text-base">
                Masuk <i class="fas fa-sign-in-alt ml-2"></i>
            </button>

            <p class="text-center text-sm text-text-secondary mt-6">
                Belum punya akun? <a href="{{ route('register') }}" class="text-primary font-bold hover:underline transition-colors">Daftar Sekarang</a>
            </p>
        </form>
    </div>
</div>

    <script>
    function togglePassword(id, btn) {
        const input = document.getElementById(id);
        const icon  = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    </script>
</body>
</html>
