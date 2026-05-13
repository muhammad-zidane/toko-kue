<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Daftar Akun</title>
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
            max-width: 400px !important;
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

        .btn-register {
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
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .btn-register:hover { opacity: 0.85; }

        .login-link {
            text-align: center;
            font-size: 14px;
            color: #3D1A0A;
        }

        .login-link a {
            color: #3D1A0A;
            text-decoration: none;
        }

        .login-link a:hover { text-decoration: underline; }

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
            <input type="password" id="password" name="password"
                   required autocomplete="new-password">
            @error('password') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   required autocomplete="new-password">
        </div>

        <button type="submit" class="btn-register">Daftar</button>

        <p class="login-link">
            <a href="{{ route('login') }}">Sudah punya akun? Login</a>
        </p>
    </form>
</div>
</body>
</html>
