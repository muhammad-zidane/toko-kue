<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jagoan Kue - Lupa Password</title>
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
        <div class="text-center mb-6">
            <span class="text-4xl mb-2 block">🔑</span>
            <a href="/" class="font-heading text-3xl font-bold text-brown-dark hover:text-primary transition-colors">Lupa Password</a>
            <p class="text-sm text-text-secondary mt-2 leading-relaxed">
                Masukkan email akunmu dan kami akan mengirimkan link untuk mereset password-mu.
            </p>
        </div>

        @if (session('status'))
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-3.5 text-sm mb-5 flex items-center gap-2">
            <i class="fas fa-check-circle text-xs shrink-0"></i> <span>{{ session('status') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-3.5 text-sm mb-5">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-1.5"><i class="fas fa-exclamation-circle text-xs shrink-0"></i> {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-6">
                <label for="email" class="input-label">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}" required autofocus autocomplete="email"
                       class="input-field" placeholder="contoh@email.com">
                @error('email') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3.5 text-base gap-2 mb-6">
                <i class="fas fa-paper-plane"></i> Kirim Link Reset
            </button>

            <p class="text-center text-sm">
                <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline transition-colors">← Kembali ke Login</a>
            </p>
        </form>
    </div>
</div>
</body>
</html>
