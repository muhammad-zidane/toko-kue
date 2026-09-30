@extends('layouts.main')

@section('title', 'Jagoan Kue - Ganti Password')

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <!-- Back Link -->
        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:text-primary-hover transition-colors mb-4">
            ← Kembali ke Akun
        </a>
        <h1 class="font-heading text-4xl font-bold text-brown-dark mb-1">Ganti Password</h1>
        <p class="text-sm text-text-secondary mb-8">Perbarui password untuk menjaga keamanan akunmu</p>

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

                    <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all font-semibold text-brown-mid hover:bg-cream-warm hover:text-primary">
                        <i class="fas fa-user w-4 text-center text-brown-mid"></i> Info Akun <span class="ml-auto text-xs opacity-60">→</span>
                    </a>

                    <a href="{{ route('account.addresses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all font-semibold text-brown-mid hover:bg-cream-warm hover:text-primary">
                        <i class="fas fa-map-marker-alt w-4 text-center text-brown-mid"></i> Alamat Tersimpan <span class="ml-auto text-xs opacity-60">→</span>
                    </a>

                    <a href="{{ route('account.change-password') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all bg-primary-light text-primary font-bold">
                        <i class="fas fa-lock w-4 text-center text-primary"></i> Ganti Password <span class="ml-auto text-xs opacity-60">→</span>
                    </a>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="w-full mt-2 border-t border-cream-border pt-2">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 transition-colors">
                        <i class="fas fa-sign-out-alt w-4 text-center text-red-600"></i> Keluar
                    </button>
                </form>
            </div>

            {{-- FORM GANTI PASSWORD --}}
            <div class="card p-8">
                <div class="font-heading text-2xl font-bold text-brown-dark mb-6 border-b border-cream-border pb-3 flex items-center gap-2">
                    <i class="fas fa-lock text-primary"></i> Ubah Password
                </div>

                @if (session('status') === 'password-updated')
                <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-3.5 text-sm mb-6 flex items-center gap-2">
                    <i class="fas fa-check-circle text-xs shrink-0"></i> <span>Password berhasil diperbarui!</span>
                </div>
                @endif

                @if ($errors->any())
                <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-3.5 text-sm mb-6">
                    @foreach ($errors->all() as $error)
                        <p class="flex items-center gap-1.5"><i class="fas fa-exclamation-circle text-xs shrink-0"></i> {{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('account.update-password') }}">
                    @csrf

                    <div class="mb-5">
                        <label class="input-label" for="current_password">Password Lama</label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password"
                                   class="input-field pr-10" required autocomplete="current-password"
                                   placeholder="Masukkan password saat ini">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-brown-light hover:text-primary cursor-pointer text-sm" onclick="togglePassword('current_password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                        <a href="{{ route('password.request') }}" class="inline-flex items-center gap-1 mt-2 text-xs font-semibold text-primary hover:underline transition-colors">
                            <i class="fas fa-question-circle"></i> Lupa password lama?
                        </a>
                    </div>

                    <div class="mb-5">
                        <label class="input-label" for="password">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                   class="input-field pr-10" required autocomplete="new-password"
                                   placeholder="Min. 8 karakter"
                                   oninput="evaluatePassword(this.value)">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-brown-light hover:text-primary cursor-pointer text-sm" onclick="togglePassword('password', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror

                        {{-- Strength bar --}}
                        <div class="h-1.5 bg-cream-border rounded-full mt-2.5 overflow-hidden">
                            <div class="h-full w-0 rounded-full transition-all duration-300" id="strength-bar"></div>
                        </div>
                        <span class="text-xs font-semibold mt-1.5 block" id="strength-label"></span>

                        {{-- Checklist --}}
                        <ul class="mt-3.5 space-y-1.5 text-xs text-text-muted" id="pwd-checklist">
                            <li id="c-len" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Minimal 8 karakter</li>
                            <li id="c-upper" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Huruf besar (A-Z)</li>
                            <li id="c-lower" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Huruf kecil (a-z)</li>
                            <li id="c-num" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Angka (0-9)</li>
                            <li id="c-sym" class="flex items-center gap-2 transition-colors"><span class="check-icon w-4 text-center text-xs">○</span> Simbol (@, #, !, %)</li>
                        </ul>
                    </div>

                    <div class="mb-6">
                        <label class="input-label" for="password_confirmation">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="input-field pr-10" required autocomplete="new-password"
                                   placeholder="Ulangi password baru">
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-brown-light hover:text-primary cursor-pointer text-sm" onclick="togglePassword('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-primary gap-2">
                        <i class="fas fa-save"></i> Simpan Password Baru
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
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
        const icon = li.querySelector('.check-icon');
        if (ok) {
            li.classList.remove('text-text-muted');
            li.classList.add('text-green-600', 'font-semibold');
            icon.textContent = '✓';
            passed++;
        } else {
            li.classList.add('text-text-muted');
            li.classList.remove('text-green-600', 'font-semibold');
            icon.textContent = '○';
        }
    }

    const bar   = document.getElementById('strength-bar');
    const label = document.getElementById('strength-label');

    if (val.length === 0) {
        bar.style.width = '0'; bar.style.backgroundColor = ''; label.textContent = ''; return;
    }

    if (passed <= 2) {
        bar.style.width = '33%'; bar.style.backgroundColor = '#DC2626';
        label.textContent = 'Lemah'; label.className = 'text-xs font-bold mt-1.5 block text-red-600';
    } else if (passed <= 4) {
        bar.style.width = '66%'; bar.style.backgroundColor = '#D97706';
        label.textContent = 'Sedang'; label.className = 'text-xs font-bold mt-1.5 block text-amber-600';
    } else {
        bar.style.width = '100%'; bar.style.backgroundColor = '#059669';
        label.textContent = 'Kuat'; label.className = 'text-xs font-bold mt-1.5 block text-green-600';
    }
}
</script>
@endpush
