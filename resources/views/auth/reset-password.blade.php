<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jagoan Kue - Reset Password</title>
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
            <span class="text-4xl mb-2 block">🔒</span>
            <a href="/" class="font-heading text-3xl font-bold text-brown-dark hover:text-primary transition-colors">Reset Password</a>
            <p class="text-sm text-text-secondary mt-1">Masukkan password baru Anda</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-3.5 text-sm mb-5">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-1.5"><i class="fas fa-exclamation-circle text-xs shrink-0"></i> {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="mb-5">
                <label for="email" class="input-label">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                       class="input-field">
                @error('email') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div class="mb-5">
                <label for="password" class="input-label">Password Baru</label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                           required autocomplete="new-password" placeholder="Min. 8 karakter"
                           class="input-field pr-10">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-brown-light hover:text-primary cursor-pointer text-sm" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="input-label">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           required autocomplete="new-password" placeholder="Ulangi password baru"
                           class="input-field pr-10">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-brown-light hover:text-primary cursor-pointer text-sm" onclick="togglePassword('password_confirmation', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password_confirmation') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3.5 text-base gap-2 mb-6">
                <i class="fas fa-lock"></i> Reset Password
            </button>

            <p class="text-center text-sm">
                <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline transition-colors">← Kembali ke Login</a>
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

