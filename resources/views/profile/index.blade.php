@extends('layouts.main')

@section('title', 'Jagoan Kue - Info Akun')

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <!-- Back Link -->
        <a href="/" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:text-primary-hover transition-colors mb-4">
            ← Kembali
        </a>
        <h1 class="font-heading text-4xl font-bold text-brown-dark mb-1">Info Akun</h1>
        <p class="text-sm text-text-secondary mb-8">Kelola informasi pribadi dan pengaturan akunmu</p>

        @if(session('status') === 'profile-updated')
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-3.5 text-sm mb-6 flex items-center gap-2">
            <i class="fas fa-check-circle text-xs shrink-0"></i> <span>Profil berhasil diperbarui!</span>
        </div>
        @endif

        {{-- STATS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            <div class="card card-hover p-6 text-center">
                <div class="text-2xl mb-2"><i class="fas fa-box text-primary"></i></div>
                <div class="text-2xl font-extrabold text-brown-dark">{{ $orderCount }}</div>
                <div class="text-xs text-text-secondary mt-1 font-semibold">Total Pesanan</div>
            </div>
            <div class="card card-hover p-6 text-center">
                <div class="text-2xl mb-2"><i class="fas fa-sync-alt text-primary"></i></div>
                <div class="text-2xl font-extrabold text-brown-dark">{{ $activeOrders }}</div>
                <div class="text-xs text-text-secondary mt-1 font-semibold">Pesanan Aktif</div>
            </div>
            <div class="card card-hover p-6 text-center">
                <div class="text-2xl mb-2"><i class="fas fa-money-bill-wave text-primary"></i></div>
                <div class="text-2xl font-extrabold text-brown-dark">Rp {{ number_format($totalSpent/1000, 0, ',', '.') }}rb</div>
                <div class="text-xs text-text-secondary mt-1 font-semibold">Total Belanja</div>
            </div>
        </div>

        {{-- PROFILE LAYOUT --}}
        <div class="grid grid-cols-1 md:grid-cols-[280px_1fr] gap-8 items-start">
            {{-- SIDEBAR --}}
            <div class="card p-6 flex flex-col">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary to-primary-hover text-white text-2xl font-bold flex items-center justify-center mx-auto mb-3 shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="text-center font-bold text-brown-dark truncate w-full px-2">{{ auth()->user()->name }}</div>
                <div class="text-center text-xs text-text-secondary mb-6 truncate w-full px-2">{{ auth()->user()->email }}</div>

                <div class="space-y-1 w-full">
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold bg-primary text-white hover:bg-primary-hover transition-all shadow-gold mb-2">
                        <i class="fas fa-cog text-white w-4 text-center"></i> Admin Dashboard <span class="ml-auto text-xs text-white/70">→</span>
                    </a>
                    @endif
                    
                    <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all bg-primary-light text-primary font-bold">
                        <i class="fas fa-user w-4 text-center text-primary"></i> Info Akun <span class="ml-auto text-xs opacity-60">→</span>
                    </a>
                    
                    <a href="{{ route('account.addresses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all font-semibold text-brown-mid hover:bg-cream-warm hover:text-primary">
                        <i class="fas fa-map-marker-alt w-4 text-center text-brown-mid"></i> Alamat Tersimpan <span class="ml-auto text-xs opacity-60">→</span>
                    </a>
                    
                    <a href="{{ route('account.change-password') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all font-semibold text-brown-mid hover:bg-cream-warm hover:text-primary">
                        <i class="fas fa-lock w-4 text-center text-brown-mid"></i> Ganti Password <span class="ml-auto text-xs opacity-60">→</span>
                    </a>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="w-full mt-2 border-t border-cream-border pt-2">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fas fa-sign-out-alt w-4 text-center text-red-600"></i> Keluar
                    </button>
                </form>
            </div>

            {{-- DATA PRIBADI --}}
            <div class="card p-8">
                <div class="font-heading text-2xl font-bold text-brown-dark mb-6 border-b border-cream-border pb-3">Data Pribadi</div>
                
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="input-label">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field" required>
                        </div>
                        <div>
                            <label class="input-label">Email</label>
                            <input type="email" id="email-input" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="input-field" required
                                   data-original="{{ $user->email }}"
                                   oninput="toggleConfirmPwd(this)">
                        </div>
                        <div>
                            <label class="input-label">Bergabung Sejak</label>
                            <input type="text" value="{{ $user->created_at->translatedFormat('d F Y') }}" class="input-field bg-cream text-text-secondary cursor-not-allowed" readonly>
                        </div>
                        <div>
                            <label class="input-label">Role</label>
                            <input type="text" value="{{ ucfirst($user->role ?? 'customer') }}" class="input-field bg-cream text-text-secondary cursor-not-allowed" readonly>
                        </div>
                    </div>
                    
                    {{-- Konfirmasi password saat ganti email --}}
                    <div class="mt-6 p-5 bg-amber-50 border border-amber-200 rounded-2xl {{ $errors->has('confirm_password') ? '' : 'hidden' }}" id="confirm-pwd-group">
                        <p class="text-xs text-amber-800 font-semibold mb-3 flex items-center gap-1.5">
                            <i class="fas fa-exclamation-triangle"></i> Masukkan password untuk konfirmasi penggantian email
                        </p>
                        <div class="max-w-md">
                            <label class="input-label">Password Saat Ini</label>
                            <input type="password" name="confirm_password" class="input-field" placeholder="Masukkan password kamu" autocomplete="current-password">
                            @error('confirm_password') 
                                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>

                    @if($errors->any() && !$errors->has('confirm_password'))
                    <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-3.5 text-sm mt-5">
                        @foreach($errors->all() as $error)
                            <p class="flex items-center gap-1.5"><i class="fas fa-exclamation-circle text-xs shrink-0"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                    @endif
                    
                    <button type="submit" class="btn-primary mt-6 gap-2">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleConfirmPwd(input) {
    const group = document.getElementById('confirm-pwd-group');
    if (input.value !== input.dataset.original) {
        group.classList.remove('hidden');
    } else {
        group.classList.add('hidden');
    }
}
</script>
@endpush

