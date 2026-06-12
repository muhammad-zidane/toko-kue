<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Lupa Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <style>
        .card { max-width: 420px; }
        .card-desc {
            font-size: 13px;
            color: #5C3D2E;
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.6;
        }
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
        .success-msg {
            background: #DCFCE7;
            border: 1px solid #A7F3D0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #065F46;
            margin-bottom: 16px;
        }
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
    <p class="card-title">Lupa Password</p>

    <p class="card-desc">
        Masukkan email akunmu dan kami akan mengirimkan link untuk mereset password-mu.
    </p>

    @if (session('status'))
    <div class="success-msg">
        <i class="fas fa-check-circle"></i> {{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="error-msg">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="{{ old('email') }}" required autofocus autocomplete="email"
                   placeholder="contoh@email.com">
            @error('email') <p class="field-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-submit">
            <i class="fas fa-paper-plane"></i> Kirim Link Reset
        </button>

        <p class="back-to-login">
            <a href="{{ route('login') }}">← Kembali ke Login</a>
        </p>
    </form>
</div>
</body>
</html>
