@extends('admin.layout')
@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Kelola profil admin dan informasi toko')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-4xl mx-auto space-y-6">
    @csrf

    {{-- PROFIL ADMIN --}}
    <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
        <div class="flex items-start gap-4 pb-4 border-b border-cream-border mb-6">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-primary/10 text-primary"><i class="fas fa-user"></i></div>
            <div>
                <h3 class="font-heading text-lg font-bold text-brown-dark">Profil Admin</h3>
                <p class="text-xs text-text-secondary mt-0.5">Perbarui informasi akun admin</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div>
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
        </div>
    </div>

    {{-- GANTI PASSWORD --}}
    <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
        <div class="flex items-start gap-4 pb-4 border-b border-cream-border mb-6">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-primary/10 text-primary"><i class="fas fa-lock"></i></div>
            <div>
                <h3 class="font-heading text-lg font-bold text-brown-dark">Ganti Password</h3>
                <p class="text-xs text-text-secondary mt-0.5">Kosongkan jika tidak ingin mengubah password</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Password Baru</label>
                <input type="password" name="password" placeholder="Minimal 8 karakter" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div>
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
        </div>
        <p class="text-xs text-amber-600 mt-4 font-semibold flex items-center gap-1">⚠️ Password harus minimal 8 karakter</p>
    </div>

    {{-- INFO TOKO --}}
    <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
        <div class="flex items-start gap-4 pb-4 border-b border-cream-border mb-6">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl bg-primary/10 text-primary"><i class="fas fa-store"></i></div>
            <div>
                <h3 class="font-heading text-lg font-bold text-brown-dark">Informasi Toko</h3>
                <p class="text-xs text-text-secondary mt-0.5">Detail informasi toko</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-cream/40 p-4 border border-cream-border rounded-xl">
                <div class="text-[10px] font-bold text-text-secondary uppercase tracking-wider">Nama Toko</div>
                <div class="text-sm font-semibold text-brown-dark mt-1">Jagoan Kue</div>
            </div>
            <div class="bg-cream/40 p-4 border border-cream-border rounded-xl">
                <div class="text-[10px] font-bold text-text-secondary uppercase tracking-wider">Telepon</div>
                <div class="text-sm font-semibold text-brown-dark mt-1">0822-8320-3385</div>
            </div>
            <div class="bg-cream/40 p-4 border border-cream-border rounded-xl">
                <div class="text-[10px] font-bold text-text-secondary uppercase tracking-wider">Email</div>
                <div class="text-sm font-semibold text-brown-dark mt-1 text-ellipsis overflow-hidden">muhammadzidane253@gmail.com</div>
            </div>
            <div class="bg-cream/40 p-4 border border-cream-border rounded-xl">
                <div class="text-[10px] font-bold text-text-secondary uppercase tracking-wider">Alamat</div>
                <div class="text-sm font-semibold text-brown-dark mt-1">Payakumbuh, Sumatera Barat</div>
            </div>
        </div>
    </div>

    <div class="flex justify-end pt-4">
        <button type="submit" class="bg-primary text-white font-bold text-xs py-3 px-6 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center gap-1.5 border-0"><i class="fas fa-save"></i> Simpan Pengaturan</button>
    </div>
</form>
@endsection
