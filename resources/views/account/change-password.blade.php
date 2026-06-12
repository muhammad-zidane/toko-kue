<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Ganti Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background: var(--cream); }
        .page { max-width: 900px; margin: 0 auto; padding: 16px 24px 64px; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-bottom: 24px; transition: color 0.2s; }
        .back-link:hover { color: var(--pink); }
        .page-title { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px; }
        .page-subtitle { font-size: 14px; color: var(--gray); margin-bottom: 28px; }
        .profile-layout { display: flex; gap: 20px; }
        .profile-sidebar { width: 220px; flex-shrink: 0; }
        .profile-main { flex: 1; }
        .avatar-card { background: white; border-radius: 16px; border: 1px solid #F3F4F6; padding: 24px; display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; background: #F9C5D1; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; color: #5C3D2E; }
        .user-name { font-size: 14px; font-weight: 700; text-align: center; }
        .user-email { font-size: 12px; color: var(--gray); text-align: center; word-break: break-all; }
        .sidebar-links { width: 100%; margin-top: 4px; display: flex; flex-direction: column; gap: 6px; }
        .sidebar-link { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 12px; font-size: 14px; color: var(--brown-dark); background: var(--cream); transition: background 0.2s; }
        .sidebar-link:hover { background: #F3F4F6; }
        .sidebar-link-active { background: #FEF3C7; color: #92400E; font-weight: 700; }
        .sidebar-link-admin { color: white; background: var(--pink); font-weight: 700; box-shadow: 0 4px 12px rgba(240,80,122,0.2); }
        .sidebar-link-admin:hover { background: #D64A6C; }
        .sidebar-link-arrow { margin-left: auto; font-size: 12px; color: var(--gray); }
        .btn-logout { width: 100%; background: var(--pink); color: white; border-radius: 12px; padding: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 8px; transition: opacity 0.2s; }
        .btn-logout:hover { opacity: 0.85; }
        .data-card { background: white; border-radius: 16px; border: 1px solid #F3F4F6; padding: 24px; }
        .data-card-title { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #F3F4F6; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-bottom: 6px; }
        .password-wrapper { position: relative; }
        .form-input { width: 100%; padding: 10px 40px 10px 14px; border: 1.5px solid #EDE0D4; border-radius: 10px; font-size: 14px; outline: none; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; background: white; }
        .form-input:focus { border-color: var(--pink); }
        .toggle-eye { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9CA3AF; font-size: 14px; padding: 0; line-height: 1; }
        .toggle-eye:hover { color: var(--pink); }
        .field-error { font-size: 12px; color: #D4607A; margin-top: 4px; }
        .btn-save { background: var(--pink); color: white; padding: 10px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: all 0.2s; margin-top: 8px; }
        .btn-save:hover { background: #D64A6C; transform: translateY(-1px); }
        .alert-success { background: #DCFCE7; border: 1px solid #A7F3D0; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #065F46; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .alert-error { background: #FEE2E2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #991B1B; margin-bottom: 16px; }
        .hint { font-size: 11px; color: var(--gray); margin-top: 4px; }
        .forgot-pwd-link { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: var(--pink); margin-top: 6px; transition: opacity 0.2s; }
        .forgot-pwd-link:hover { opacity: 0.75; text-decoration: underline; }

        /* Strength bar */
        .strength-bar-wrap { height: 5px; border-radius: 4px; background: #EDE0D4; margin-top: 8px; overflow: hidden; }
        .strength-bar { height: 100%; width: 0; border-radius: 4px; transition: width 0.3s, background 0.3s; }
        .strength-label { font-size: 11px; margin-top: 4px; font-weight: 600; }
        .strength-label.weak   { color: #DC2626; }
        .strength-label.medium { color: #D97706; }
        .strength-label.strong { color: #059669; }

        /* Checklist */
        .pwd-checklist { margin-top: 8px; list-style: none; padding: 0; display: flex; flex-direction: column; gap: 4px; }
        .pwd-checklist li { font-size: 12px; color: #9CA3AF; display: flex; align-items: center; gap: 6px; transition: color 0.2s; }
        .pwd-checklist li .check-icon { font-size: 10px; width: 14px; text-align: center; }
        .pwd-checklist li.pass { color: #059669; }
        .pwd-checklist li.pass .check-icon::before { content: '✓'; }
        .pwd-checklist li:not(.pass) .check-icon::before { content: '○'; }

        @media (max-width: 768px) { .profile-layout { flex-direction: column; } .profile-sidebar { width: 100%; } }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="page">
    <a href="{{ route('profile.index') }}" class="back-link">← Kembali ke Akun</a>
    <h1 class="page-title">Ganti Password</h1>
    <p class="page-subtitle">Perbarui password untuk menjaga keamanan akunmu</p>

    <div class="profile-layout">
        {{-- SIDEBAR --}}
        <div class="profile-sidebar">
            <div class="avatar-card">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>

                <div class="sidebar-links">
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-admin">
                        <i class="fas fa-cog" style="color:white"></i> Admin Dashboard <span class="sidebar-link-arrow">→</span>
                    </a>
                    @endif
                    <a href="{{ route('profile.index') }}" class="sidebar-link">
                        <i class="fas fa-user" style="color:var(--brown-dark)"></i> Info Akun
                        <span class="sidebar-link-arrow">→</span>
                    </a>
                    <a href="{{ route('account.addresses.index') }}" class="sidebar-link">
                        <i class="fas fa-map-marker-alt" style="color:var(--brown-dark)"></i> Alamat Tersimpan
                        <span class="sidebar-link-arrow">→</span>
                    </a>
                    <a href="{{ route('account.change-password') }}" class="sidebar-link sidebar-link-active">
                        <i class="fas fa-lock" style="color:#92400E"></i> Ganti Password
                        <span class="sidebar-link-arrow">→</span>
                    </a>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="width:100%;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </button>
                </form>
            </div>
        </div>

        {{-- FORM GANTI PASSWORD --}}
        <div class="profile-main">
            <div class="data-card">
                <div class="data-card-title"><i class="fas fa-lock" style="color:var(--pink)"></i> Ubah Password</div>

                @if (session('status') === 'password-updated')
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> Password berhasil diperbarui!
                </div>
                @endif

                @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('account.update-password') }}">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="current_password">Password Lama</label>
                        <div class="password-wrapper">
                            <input type="password" id="current_password" name="current_password"
                                   class="form-input" required autocomplete="current-password"
                                   placeholder="Masukkan password saat ini">
                            <button type="button" class="toggle-eye" onclick="togglePassword('current_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                        <a href="{{ route('password.request') }}" class="forgot-pwd-link">
                            <i class="fas fa-question-circle"></i> Lupa password lama?
                        </a>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password"
                                   class="form-input" required autocomplete="new-password"
                                   placeholder="Min. 8 karakter"
                                   oninput="evaluatePassword(this.value)">
                            <button type="button" class="toggle-eye" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror

                        {{-- Strength bar --}}
                        <div class="strength-bar-wrap"><div class="strength-bar" id="strength-bar"></div></div>
                        <span class="strength-label" id="strength-label"></span>

                        {{-- Checklist --}}
                        <ul class="pwd-checklist">
                            <li id="c-len"><span class="check-icon"></span> Minimal 8 karakter</li>
                            <li id="c-upper"><span class="check-icon"></span> Huruf besar (A-Z)</li>
                            <li id="c-lower"><span class="check-icon"></span> Huruf kecil (a-z)</li>
                            <li id="c-num"><span class="check-icon"></span> Angka (0-9)</li>
                            <li id="c-sym"><span class="check-icon"></span> Simbol (@, #, !, %)</li>
                        </ul>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-input" required autocomplete="new-password"
                                   placeholder="Ulangi password baru">
                            <button type="button" class="toggle-eye" onclick="togglePassword('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-save" style="color:white"></i> Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('partials.footer')

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
