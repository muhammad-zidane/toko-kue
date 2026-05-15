<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Daftar Akun</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .card { max-width: 420px; }
        .password-wrapper { position: relative; }
        .password-wrapper input { padding-right: 40px; }
        .toggle-eye {
            position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: #5C3D2E; font-size: 13px; padding: 0; line-height: 1;
        }
        .toggle-eye:hover { color: #D4607A; }

        /* Strength bar */
        .strength-bar-wrap { height: 5px; border-radius: 4px; background: #D9C8B0; margin-top: 8px; overflow: hidden; }
        .strength-bar { height: 100%; width: 0; border-radius: 4px; transition: width 0.3s, background 0.3s; }

        /* Checklist */
        .pwd-checklist { margin-top: 10px; list-style: none; padding: 0; display: flex; flex-direction: column; gap: 4px; }
        .pwd-checklist li {
            font-size: 11px; color: #9CA3AF;
            display: flex; align-items: center; gap: 6px;
            transition: color 0.2s;
        }
        .pwd-checklist li .check-icon { font-size: 10px; width: 14px; text-align: center; }
        .pwd-checklist li.pass { color: #059669; }
        .pwd-checklist li.pass .check-icon::before { content: '✓'; }
        .pwd-checklist li:not(.pass) .check-icon::before { content: '○'; }
        .strength-label { font-size: 11px; margin-top: 4px; font-weight: 600; }
        .strength-label.weak   { color: #DC2626; }
        .strength-label.medium { color: #D97706; }
        .strength-label.strong { color: #059669; }
    </style>
</head>
<body>
<div class="card">
    <p class="card-title">Daftar</p>

    @if ($errors->any())
    <div class="error-msg">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}" required autofocus autocomplete="name">
            @error('name') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}" required autocomplete="username">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password"
                       required autocomplete="new-password"
                       oninput="evaluatePassword(this.value)">
                <button type="button" class="toggle-eye" onclick="togglePassword('password', this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror

            {{-- Strength bar --}}
            <div class="strength-bar-wrap"><div class="strength-bar" id="strength-bar"></div></div>
            <span class="strength-label" id="strength-label"></span>

            {{-- Checklist kriteria --}}
            <ul class="pwd-checklist" id="pwd-checklist">
                <li id="c-len"><span class="check-icon"></span> Minimal 8 karakter</li>
                <li id="c-upper"><span class="check-icon"></span> Huruf besar (A-Z)</li>
                <li id="c-lower"><span class="check-icon"></span> Huruf kecil (a-z)</li>
                <li id="c-num"><span class="check-icon"></span> Angka (0-9)</li>
                <li id="c-sym"><span class="check-icon"></span> Simbol (@, #, !, %)</li>
            </ul>
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <div class="password-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation"
                       required autocomplete="new-password">
                <button type="button" class="toggle-eye" onclick="togglePassword('password_confirmation', this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-register">Daftar</button>

        <p class="login-link">
            <a href="{{ route('login') }}">Sudah punya akun? Login</a>
        </p>
    </form>
</div>

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
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
        if (ok) { li.classList.add('pass'); passed++; }
        else     { li.classList.remove('pass'); }
    }

    const bar   = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');

    if (val.length === 0) {
        bar.style.width = '0'; bar.style.background = ''; label.textContent = ''; return;
    }

    if (passed <= 2) {
        bar.style.width = '33%'; bar.style.background = '#DC2626';
        label.textContent = 'Lemah'; label.className = 'strength-label weak';
    } else if (passed <= 4) {
        bar.style.width = '66%'; bar.style.background = '#D97706';
        label.textContent = 'Sedang'; label.className = 'strength-label medium';
    } else {
        bar.style.width = '100%'; bar.style.background = '#059669';
        label.textContent = 'Kuat'; label.className = 'strength-label strong';
    }
}
</script>
</body>
</html>
