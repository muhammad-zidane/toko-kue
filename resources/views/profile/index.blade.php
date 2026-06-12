<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Info Akun</title>
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
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #F3F4F6; text-align: center; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-icon { font-size: 24px; margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 800; }
        .stat-label { font-size: 12px; color: var(--gray); }
        .profile-layout { display: flex; gap: 20px; align-items: stretch; }
        .profile-sidebar { width: 220px; flex-shrink: 0; display: flex; flex-direction: column; }
        .profile-main { flex: 1; }
        .avatar-card { background: white; border-radius: 16px; border: 1px solid #F3F4F6; padding: 24px; display: flex; flex-direction: column; align-items: center; gap: 10px; flex: 1; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; background: #F9C5D1; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; color: #5C3D2E; }
        .user-name { font-size: 14px; font-weight: 700; text-align: center; }
        .user-email { font-size: 12px; color: var(--gray); text-align: center; word-break: break-all; }
        .user-badge { background: #FEF3C7; color: #92400E; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 20px; }
        .user-since { font-size: 11px; color: var(--gray); }
        .sidebar-links { width: 100%; margin-top: 4px; display: flex; flex-direction: column; gap: 6px; }
        .sidebar-link { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 12px; font-size: 14px; color: var(--brown-dark); background: var(--cream); transition: background 0.2s; }
        .sidebar-link:hover { background: #F3F4F6; }
        .sidebar-link-arrow { margin-left: auto; font-size: 12px; color: var(--gray); }
        .sidebar-link-admin { color: white; background: var(--pink); font-weight: 700; box-shadow: 0 4px 12px rgba(240,80,122,0.2); }
        .sidebar-link-admin:hover { background: #D64A6C; }
        .btn-logout { width: 100%; background: var(--pink); color: white; border-radius: 12px; padding: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 8px; transition: opacity 0.2s; }
        .btn-logout:hover { opacity: 0.85; }
        .data-card { background: white; border-radius: 16px; border: 1px solid #F3F4F6; padding: 24px; }
        .data-card-title { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #F3F4F6; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-bottom: 6px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1.5px solid #EDE0D4; border-radius: 10px; font-size: 14px; outline: none; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; background: white; }
        .form-input:focus { border-color: var(--pink); }
        .form-input[readonly] { background: #FAFAF8; color: var(--gray); }
        .btn-save { background: var(--pink); color: white; padding: 10px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: all 0.2s; margin-top: 16px; }
        .btn-save:hover { background: #D64A6C; transform: translateY(-1px); }
        .alert-success { background: #DCFCE7; border: 1px solid #A7F3D0; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #065F46; margin-bottom: 16px; }
        .confirm-pwd-group { display: none; margin-top: 8px; }
        .confirm-pwd-group.visible { display: block; }
        .confirm-pwd-note { font-size: 12px; color: #D97706; margin-bottom: 6px; display: flex; align-items: center; gap: 4px; }
        @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } .profile-layout { flex-direction: column; } .profile-sidebar { width: 100%; } .form-grid { grid-template-columns: 1fr; } }
    </style>
    </head>
<body>
@include('partials.navbar')

<div class="page">
    <a href="/" class="back-link">← Kembali</a>
    <h1 class="page-title">Info Akun</h1>
    <p class="page-subtitle">Kelola informasi pribadi dan pengaturan akunmu</p>

    @if(session('status') === 'profile-updated')
    <div class="alert-success">Profil berhasil diperbarui!</div>
    @endif

    {{-- STATS --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-box" style="color:var(--pink)"></i></div>
            <div class="stat-value">{{ $orderCount }}</div>
            <div class="stat-label">Total Pesanan</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-sync-alt" style="color:var(--pink)"></i></div>
            <div class="stat-value">{{ $activeOrders }}</div>
            <div class="stat-label">Pesanan Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-money-bill-wave" style="color:var(--pink)"></i></div>
            <div class="stat-value">Rp {{ number_format($totalSpent/1000, 0, ',', '.') }}rb</div>
            <div class="stat-label">Total Belanja</div>
        </div>
    </div>

    {{-- PROFILE LAYOUT --}}
    <div class="profile-layout">
        {{-- SIDEBAR --}}
        <div class="profile-sidebar">
            <div class="avatar-card">
                <div class="avatar">{{ strtoupper(substr($user->name, 0, 2)) }}</div>
                <div class="user-name">{{ $user->name }}</div>
                <div class="user-email">{{ $user->email }}</div>
                <span class="user-badge">{!! $orderCount >= 5 ? '<i class="fas fa-star" style="color:#F59E0B"></i> Pelanggan Setia' : '<i class="fas fa-user" style="color:var(--pink)"></i> Pelanggan' !!}</span>
                <div class="user-since">Bergabung sejak {{ $user->created_at->translatedFormat('d F Y') }}</div>

                <div class="sidebar-links">
                    @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-admin">
                        <i class="fas fa-cog" style="color:white"></i> Admin Dashboard <span class="sidebar-link-arrow">→</span>
                    </a>
                    @endif
                    <a href="{{ route('profile.index') }}" class="sidebar-link sidebar-link-active"><i class="fas fa-user" style="color:#92400E"></i> Info Akun <span class="sidebar-link-arrow">→</span></a>
                    <a href="{{ route('account.addresses.index') }}" class="sidebar-link"><i class="fas fa-map-marker-alt" style="color:var(--brown-dark)"></i> Alamat Tersimpan <span class="sidebar-link-arrow">→</span></a>
                    <a href="{{ route('account.change-password') }}" class="sidebar-link"><i class="fas fa-lock" style="color:var(--brown-dark)"></i> Ganti Password <span class="sidebar-link-arrow">→</span></a>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="width:100%;">
                    @csrf
                    <button type="submit" class="btn-logout"><i class="fas fa-sign-out-alt" style="color:white"></i> Keluar</button>
                </form>
            </div>
        </div>

        {{-- DATA PRIBADI --}}
        <div class="profile-main">
            <div class="data-card">
                <div class="data-card-title">Data Pribadi</div>
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" id="email-input" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="form-input" required
                                   data-original="{{ $user->email }}"
                                   oninput="toggleConfirmPwd(this)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bergabung Sejak</label>
                            <input type="text" value="{{ $user->created_at->translatedFormat('d F Y') }}" class="form-input" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <input type="text" value="{{ ucfirst($user->role ?? 'customer') }}" class="form-input" readonly>
                        </div>
                    </div>
                    {{-- Konfirmasi password saat ganti email --}}
                    <div class="confirm-pwd-group{{ $errors->has('confirm_password') ? ' visible' : '' }}" id="confirm-pwd-group">
                        <p class="confirm-pwd-note"><i class="fas fa-exclamation-triangle" style="color:#D97706"></i> Masukkan password untuk konfirmasi penggantian email</p>
                        <div class="form-group" style="margin-bottom:0">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" name="confirm_password" class="form-input" placeholder="Masukkan password kamu" autocomplete="current-password">
                            @error('confirm_password') <p style="font-size:12px;color:#D4607A;margin-top:4px">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    @if($errors->any())
                    <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:8px;padding:10px 14px;font-size:13px;color:#991B1B;margin-top:12px;">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                    @endif
                    <button type="submit" class="btn-save"><i class="fas fa-save" style="color:white"></i> Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@include('partials.footer')
<script src="{{ asset('js/app.js') }}" defer></script>
<script>
function toggleConfirmPwd(input) {
    const group = document.getElementById('confirm-pwd-group');
    if (input.value !== input.dataset.original) {
        group.classList.add('visible');
    } else {
        group.classList.remove('visible');
    }
}
</script>
</body>
</html>
