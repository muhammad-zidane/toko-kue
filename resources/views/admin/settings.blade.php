@extends('admin.layout')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Kelola profil admin dan informasi toko')

@section('styles')
<style>
    .settings-form { max-width: 720px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 28px; margin-bottom: 20px; }
    .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--cream); }
    .card-header-icon { width: 44px; height: 44px; border-radius: 12px; background: rgba(240,80,122,0.1); display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .card-header-title { font-size: 16px; font-weight: 700; color: var(--brown-dark); }
    .card-header-desc { font-size: 12px; color: var(--gray); }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { }
    .form-label { display: block; font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-bottom: 6px; }
    .form-label .required { color: var(--pink); }
    .form-input { width: 100%; padding: 10px 14px; border: 1px solid #EDE0D4; border-radius: 12px; font-size: 14px; background: white; outline: none; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; }
    .form-input:focus { border-color: var(--pink); }
    .form-hint { font-size: 11px; color: var(--gray); margin-top: 12px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .info-card { padding: 14px; background: var(--cream); border-radius: 12px; }
    .info-label { font-size: 11px; font-weight: 600; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; }
    .info-value { font-size: 14px; font-weight: 600; margin-top: 4px; }
    .btn-save { background: var(--pink); color: white; padding: 10px 24px; border-radius: 12px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 8px 20px rgba(240,80,122,0.25); transition: all 0.2s; }
    .btn-save:hover { background: var(--pink-hover); transform: translateY(-1px); }
    @media (max-width: 640px) { .form-grid, .info-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="settings-form">
    @csrf

    {{-- PROFIL ADMIN --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon">👤</div>
            <div>
                <div class="card-header-title">Profil Admin</div>
                <div class="card-header-desc">Perbarui informasi akun admin</div>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="form-input">
            </div>
        </div>
    </div>

    {{-- GANTI PASSWORD --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon">🔒</div>
            <div>
                <div class="card-header-title">Ganti Password</div>
                <div class="card-header-desc">Kosongkan jika tidak ingin mengubah password</div>
            </div>
        </div>
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" placeholder="Minimal 8 karakter" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="form-input">
            </div>
        </div>
        <p class="form-hint">⚠️ Password harus minimal 8 karakter</p>
    </div>

    {{-- INFO TOKO --}}
    <div class="card">
        <div class="card-header">
            <div class="card-header-icon">🏪</div>
            <div>
                <div class="card-header-title">Informasi Toko</div>
                <div class="card-header-desc">Detail informasi toko</div>
            </div>
        </div>
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Nama Toko</div>
                <div class="info-value">Jagoan Kue</div>
            </div>
            <div class="info-card">
                <div class="info-label">Telepon</div>
                <div class="info-value">0822-8320-3385</div>
            </div>
            <div class="info-card">
                <div class="info-label">Email</div>
                <div class="info-value">muhammadzidane253@gmail.com</div>
            </div>
            <div class="info-card">
                <div class="info-label">Alamat</div>
                <div class="info-value">Payakumbuh, Sumatera Barat</div>
            </div>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;">
        <button type="submit" class="btn-save">💾 Simpan Pengaturan</button>
    </div>
</form>
@endsection
