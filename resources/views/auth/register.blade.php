<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jagoan Kue - Daftar Akun</title>
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
            <p class="text-sm text-text-secondary mt-1">Daftar untuk mulai memesan kue artisan lezat</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-3.5 text-sm mb-5">
            @foreach ($errors->all() as $error)
                <p class="flex items-center gap-1.5"><i class="fas fa-exclamation-circle text-xs shrink-0"></i> {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-5">
                <label for="name" class="input-label">Nama Lengkap</label>
                <input type="text" id="name" name="name"
                       value="{{ old('name') }}" required autofocus autocomplete="name"
                       class="input-field" placeholder="Nama lengkap Anda">
                @error('name') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Email Address -->
            <div class="mb-5">
                <label for="email" class="input-label">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}" required autocomplete="username"
                       class="input-field" placeholder="email@contoh.com">
                @error('email') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <!-- Password -->
            <div class="mb-5">
                <label for="password" class="input-label">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password"
                           required autocomplete="new-password"
                           class="input-field pr-10" placeholder="••••••••"
                           oninput="evaluatePassword(this.value)">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-brown-light hover:text-primary cursor-pointer text-sm" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password') <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> @enderror

                {{-- Strength bar --}}
                <div class="h-1.5 bg-cream-border rounded-full mt-2.5 overflow-hidden">
                    <div class="h-full w-0 rounded-full transition-all duration-300" id="strength-bar"></div>
                </div>
                <span class="text-xs font-semibold mt-1.5 block" id="strength-label"></span>

                {{-- Checklist kriteria --}}
                <ul class="mt-3.5 space-y-1.5 text-xs text-text-muted" id="pwd-checklist">
                    <li id="c-len" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Minimal 8 karakter</li>
                    <li id="c-upper" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Huruf besar (A-Z)</li>
                    <li id="c-lower" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Huruf kecil (a-z)</li>
                    <li id="c-num" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Angka (0-9)</li>
                    <li id="c-sym" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Simbol (@, #, !, %)</li>
                </ul>
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label for="password_confirmation" class="input-label">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           required autocomplete="new-password"
                           class="input-field pr-10" placeholder="••••••••">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-brown-light hover:text-primary cursor-pointer text-sm" onclick="togglePassword('password_confirmation', this)">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-primary w-full justify-center py-3.5 text-base">
                Daftar <i class="fas fa-user-plus ml-2"></i>
            </button>

            <p class="text-center text-sm text-text-secondary mt-6">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-primary font-bold hover:underline transition-colors">Login</a>
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

    function evaluatePassword(val) {
        const criteria = {
            'c-len':   val.length >= 8,
            'c-upper': /[A-Z]/.test(val),
            'c-lower': /[a-z]/.test(val),
            'c-num':   /[0-9]/.test(val),
            'c-sym':   /[@#!%$&*^()\-_+=\[\]{};':"\\|,.<>\/?`~]/.test(val),
        };

        let passed = 0;
        for (const [id, ok] of Object.entries(criteria)) {
            const li = document.getElementById(id);
            const icon = li.querySelector('.check-icon');
            if (ok) {
                li.classList.remove('text-text-muted');
                li.classList.add('text-green-600', 'font-semibold');
                icon.textContent = '✓';
                passed++;
            } else {
                li.classList.add('text-text-muted');
                li.classList.remove('text-green-600', 'font-semibold');
                icon.textContent = '○';
            }
        }

        const bar   = document.getElementById('strength-bar');
        const label = document.getElementById('strength-label');

        if (val.length === 0) {
            bar.style.width = '0'; bar.style.backgroundColor = ''; label.textContent = ''; return;
        }

        if (passed <= 2) {
            bar.style.width = '33%'; bar.style.backgroundColor = '#DC2626';
            label.textContent = 'Lemah'; label.className = 'text-xs font-bold mt-1.5 block text-red-600';
        } else if (passed <= 4) {
            bar.style.width = '66%'; bar.style.backgroundColor = '#D97706';
            label.textContent = 'Sedang'; label.className = 'text-xs font-bold mt-1.5 block text-amber-600';
        } else {
            bar.style.width = '100%'; bar.style.backgroundColor = '#059669';
            label.textContent = 'Kuat'; label.className = 'text-xs font-bold mt-1.5 block text-green-600';
        }
    }
    </script>
</body>
</html>

