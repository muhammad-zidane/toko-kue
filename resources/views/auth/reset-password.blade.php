<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .card { max-width: 420px; }
        .btn-submit {
            width: 100%;
            background: #3D1A0A;
            color: #E8D9B8;
            border: none;
            border-radius: 10px;
            padding: 10px;
            font-size: 15px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            transition: opacity 0.2s;
            margin-top: 8px;
            margin-bottom: 10px;
        }
        .btn-submit:hover { opacity: 0.85; }
        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 40px;
        }
        .toggle-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #5C3D2E;
            font-size: 14px;
            padding: 0;
            line-height: 1;
        }
        .toggle-eye:hover { color: #D4607A; }
        .back-to-login {
            text-align: center;
            font-size: 14px;
            color: #3D1A0A;
        }
        .back-to-login a { color: #3D1A0A; }
        .back-to-login a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="card">
    <p class="card-title">Reset Password</p>

    @if ($errors->any())
    <div class="error-msg">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password">Password Baru</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password"
                       required autocomplete="new-password" placeholder="Min. 8 karakter">
                <button type="button" class="toggle-eye" onclick="togglePassword('password', this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <div class="password-wrapper">
                <input type="password" id="password_confirmation" name="password_confirmation"
                       required autocomplete="new-password" placeholder="Ulangi password baru">
                <button type="button" class="toggle-eye" onclick="togglePassword('password_confirmation', this)">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('password_confirmation') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-lock"></i> Reset Password
        </button>

        <p class="back-to-login">
            <a href="{{ route('login') }}">← Kembali ke Login</a>
        </p>
    </form>
</div>

<script>
function togglePassword(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
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
