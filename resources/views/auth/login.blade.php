<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #3D1A0A 0%, #7B2D3E 50%, #D4607A 100%);
        }

        .card {
            background: #FFFDF4;
            border-radius: 12px;
            border: 1.5px solid #2C1810;
            box-shadow: 0 8px 32px rgba(0,0,0,0.35);
            padding: 36px 40px 40px;
            width: 100%;
            max-width: 360px !important;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 400;
            color: #3D1A0A;
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 1.5px solid #D4607A;
            margin-bottom: 32px;
        }

        .form-group { margin-bottom: 12px; }

        .form-group label {
            display: block;
            font-size: 12px;
            color: #5C3D2E;
            margin-bottom: 4px;
        }

        .form-group input {
            width: 100%;
            background: #E8D9B8;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #3D1A0A;
            outline: none;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            transition: box-shadow 0.2s;
        }

        .form-group input:focus {
            box-shadow: 0 3px 12px rgba(212, 96, 122, 0.35);
        }

        .forgot-row {
            text-align: right;
            margin-top: 6px;
            margin-bottom: 32px;
        }

        .forgot-row a {
            font-size: 11px;
            color: #5C3D2E;
            text-decoration: none;
        }

        .forgot-row a:hover { text-decoration: underline; }

        .btn-login {
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
            margin-bottom: 10px;
        }

        .btn-login:hover { opacity: 0.85; }

        .register-link {
            text-align: center;
            font-size: 14px;
            color: #3D1A0A;
        }

        .register-link a {
            color: #3D1A0A;
            text-decoration: none;
        }

        .register-link a:hover { text-decoration: underline; }

        /* Error messages */
        .error-msg {
            background: #FEE2E2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #B91C1C;
            margin-bottom: 16px;
        }

        .field-error {
            font-size: 11px;
            color: #D4607A;
            margin-top: 4px;
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
<div class="card">
    <p class="card-title">Login</p>

    {{-- Error Messages --}}
    @if ($errors->any())
    <div class="error-msg">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    @if (session('status'))
    <div class="error-msg" style="background:#DCFCE7;border-color:#A7F3D0;color:#065F46;">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}" required autofocus autocomplete="username">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password"
                   required autocomplete="current-password">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="forgot-row">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Lupa Password?</a>
            @endif
        </div>

        <button type="submit" class="btn-login">Login</button>

        <p class="register-link">
            <a href="{{ route('register') }}">Daftar Akun</a>
        </p>
    </form>
</div>
</body>
</html>
