<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Info Akun</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --pink: #F0507A;
            --brown-dark: #2C1810;
            --cream: #FFF8EE;
            --cream-dark: #F5EDD8;
            --white: #FFFFFF;
            --gray: #6B7280;
            --text-dark: #1A1A1A;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--cream); }
        a { text-decoration: none; color: inherit; }

        /* NAVBAR */
        .navbar { background-color: var(--brown-dark); padding: 16px 24px; position: sticky; top: 0; z-index: 100; }
        .navbar-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .navbar-logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: var(--pink); }
        .navbar-links { display: flex; gap: 32px; list-style: none; }
        .navbar-links a { color: white; font-size: 14px; font-weight: 500; opacity: 0.9; }
        .navbar-links a:hover { opacity: 1; }
        .navbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-cart { background-color: var(--pink); color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn-login:hover { background: white; color: var(--brown-dark); }

        .page { max-width: 900px; margin: 0 auto; padding: 16px 24px 64px; }
        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-bottom: 24px; transition: color 0.2s; }
        .back-link:hover { color: var(--pink); }
        .page-title { font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px; }
        .page-subtitle { font-size: 14px; color: var(--gray); margin-bottom: 28px; }

        /* STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #F3F4F6; text-align: center; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-icon { font-size: 24px; margin-bottom: 4px; }
        .stat-value { font-size: 24px; font-weight: 800; }
        .stat-label { font-size: 12px; color: var(--gray); }

        /* PROFILE LAYOUT */
        .profile-layout { display: flex; gap: 20px; }
        .profile-sidebar { width: 220px; flex-shrink: 0; }
        .profile-main { flex: 1; }

        /* AVATAR CARD */
        .avatar-card { background: white; border-radius: 16px; border: 1px solid #F3F4F6; padding: 24px; display: flex; flex-direction: column; align-items: center; gap: 10px; }
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

        /* DATA PRIBADI CARD */
        .data-card { background: white; border-radius: 16px; border: 1px solid #F3F4F6; padding: 24px; }
        .data-card-title { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #F3F4F6; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { }
        .form-label { display: block; font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-bottom: 6px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1.5px solid #EDE0D4; border-radius: 10px; font-size: 14px; outline: none; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; background: white; }
        .form-input:focus { border-color: var(--pink); }
        .form-input[readonly] { background: #FAFAF8; color: var(--gray); }
        .btn-save { background: var(--pink); color: white; padding: 10px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: all 0.2s; margin-top: 16px; }
        .btn-save:hover { background: #D64A6C; transform: translateY(-1px); }

        .alert-success { background: #DCFCE7; border: 1px solid #A7F3D0; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #065F46; margin-bottom: 16px; }

        @media (max-width: 768px) {
            .navbar-links { display: none; }
            .stats-grid { grid-template-columns: 1fr; }
            .profile-layout { flex-direction: column; }
            .profile-sidebar { width: 100%; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
{{-- NAVBAR --}}
<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-logo">Jagoan Kue</a>
        <ul class="navbar-links">
            <li><a href="/">Beranda</a></li>
            <li><a href="/products">Katalog</a></li>
            <li><a href="/orders">Pemesanan</a></li>
        </ul>
        <div class="navbar-actions">
            <a href="/cart" class="btn-cart">🛒 Keranjang</a>
            <a href="/profile" class="btn-login">{{ auth()->user()->name }}</a>
        </div>
    </div>
</nav>

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
            <div class="stat-icon">📦</div>
            <div class="stat-value">{{ $orderCount }}</div>
            <div class="stat-label">Total Pesanan</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🔄</div>
            <div class="stat-value">{{ $activeOrders }}</div>
            <div class="stat-label">Pesanan Aktif</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
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
                <span class="user-badge">{{ $orderCount >= 5 ? '⭐ Pelanggan Setia' : '👤 Pelanggan' }}</span>
                <div class="user-since">Bergabung sejak {{ $user->created_at->translatedFormat('d F Y') }}</div>

                <div class="sidebar-links">
                    @if($user->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-admin">
                        ⚙️ Admin Dashboard <span class="sidebar-link-arrow">→</span>
                    </a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="sidebar-link">📋 Riwayat Pesanan <span class="sidebar-link-arrow">→</span></a>
                    <a href="{{ route('cart.index') }}" class="sidebar-link">🛒 Keranjang <span class="sidebar-link-arrow">→</span></a>
                    <a href="{{ route('products.index') }}" class="sidebar-link">🎂 Katalog Produk <span class="sidebar-link-arrow">→</span></a>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="width:100%;">
                    @csrf
                    <button type="submit" class="btn-logout">🚪 Keluar</button>
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
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
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
                    @if($errors->any())
                    <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:8px;padding:10px 14px;font-size:13px;color:#991B1B;margin-top:12px;">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                    @endif
                    <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
