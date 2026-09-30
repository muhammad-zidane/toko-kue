@extends('layouts.main')

@section('title', 'Jagoan Kue - Alamat Tersimpan')

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <!-- Back Link -->
        <a href="{{ route('profile.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:text-primary-hover transition-colors mb-4">
            ← Kembali ke Akun
        </a>
        <h1 class="font-heading text-4xl font-bold text-brown-dark mb-1">Alamat Tersimpan</h1>
        <p class="text-sm text-text-secondary mb-8">Kelola alamat pengiriman yang tersimpan di akunmu</p>

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
                    
                    <a href="{{ route('account.addresses.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-all bg-primary-light text-primary font-bold">
                        <i class="fas fa-map-marker-alt w-4 text-center text-primary"></i> Alamat Tersimpan <span class="ml-auto text-xs opacity-60">→</span>
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

            {{-- KONTEN UTAMA --}}
            <div class="profile-main">

                @if(session('success'))
                <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-3.5 text-sm mb-6 flex items-center gap-2">
                    <i class="fas fa-check-circle text-xs shrink-0"></i> <span>{{ session('success') }}</span>
                </div>
                @endif

                @if($errors->any())
                <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-3.5 text-sm mb-6">
                    @foreach($errors->all() as $error)
                        <p class="flex items-center gap-1.5"><i class="fas fa-exclamation-circle text-xs shrink-0"></i> {{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <span class="font-heading text-2xl font-bold text-brown-dark flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-primary"></i> Alamat Saya ({{ $addresses->count() }})
                    </span>
                    <button class="btn-primary py-2 px-5 text-xs gap-1.5" onclick="openModal('modal-add')">
                        <i class="fas fa-plus"></i> Tambah Alamat
                    </button>
                </div>

                {{-- DAFTAR ALAMAT --}}
                <div class="space-y-4">
                    @forelse($addresses as $address)
                    <div class="card p-6 relative transition-all duration-200 {{ $address->is_default ? 'border-2 border-primary' : '' }}">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            @if($address->label)
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-cream-warm text-brown-dark">{{ $address->label }}</span>
                            @endif
                            @if($address->is_default)
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary-light text-primary flex items-center gap-1"><i class="fas fa-star text-[10px]"></i> Utama</span>
                            @endif
                        </div>
                        
                        <p class="font-bold text-base text-brown-dark mb-1">{{ $address->recipient_name }}</p>
                        <p class="text-sm text-text-secondary mb-3 flex items-center gap-1.5"><i class="fas fa-phone text-xs text-text-muted"></i> {{ $address->phone }}</p>
                        
                        <p class="text-sm text-text-primary leading-relaxed bg-cream-warm/20 p-3.5 rounded-xl border border-cream-border/60">
                            {{ $address->street }}
                            @if($address->rt_rw), RT/RW {{ $address->rt_rw }}@endif
                            @if($address->kelurahan), Kel. {{ $address->kelurahan }}@endif
                            @if($address->kecamatan), Kec. {{ $address->kecamatan }}@endif
                            @if($address->city), {{ $address->city }}@endif
                            @if($address->postal_code) {{ $address->postal_code }}@endif
                        </p>

                        <div class="flex flex-wrap items-center gap-3 mt-4 pt-4 border-t border-cream-border">
                            @if(!$address->is_default)
                            <form method="POST" action="{{ route('account.addresses.setDefault', $address) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-primary-light text-primary hover:opacity-85 transition-opacity">
                                    <i class="fas fa-star text-[10px]"></i> Jadikan Utama
                                </button>
                            </form>
                            @endif

                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-cream-warm text-brown-dark hover:bg-cream-dark transition-colors" onclick="openEditModal({{ $address->id }})">
                                <i class="fas fa-pencil-alt text-[10px]"></i> Edit
                            </button>

                            <form method="POST" action="{{ route('account.addresses.destroy', $address) }}"
                                  onsubmit="return confirm('Hapus alamat ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                                    <i class="fas fa-trash text-[10px]"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Data untuk edit modal (hidden) --}}
                    <script>
                        window._addressData = window._addressData || {};
                        window._addressData[{{ $address->id }}] = {
                            id: {{ $address->id }},
                            label: @json($address->label ?? ''),
                            recipient_name: @json($address->recipient_name ?? ''),
                            phone: @json($address->phone ?? ''),
                            street: @json($address->street ?? ''),
                            rt_rw: @json($address->rt_rw ?? ''),
                            kelurahan: @json($address->kelurahan ?? ''),
                            kecamatan: @json($address->kecamatan ?? ''),
                            city: @json($address->city ?? ''),
                            postal_code: @json($address->postal_code ?? ''),
                            is_default: {{ $address->is_default ? 'true' : 'false' }},
                        };
                    </script>
                    @empty
                    <div class="card p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-primary-light text-primary text-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <p class="font-bold text-brown-dark mb-1 text-base">Belum Ada Alamat Tersimpan</p>
                        <p class="text-sm text-text-secondary">Tambahkan alamat pengiriman agar checkout lebih cepat.</p>
                    </div>
                    @endforelse
                </div>

            </div>{{-- /profile-main --}}
        </div>
    </div>
</div>

{{-- MODAL TAMBAH ALAMAT --}}
<div class="fixed inset-0 bg-brown-dark/60 backdrop-blur-sm z-[200] hidden items-center justify-center" id="modal-add" onclick="closeModalOnOverlay(event, 'modal-add')">
    <div class="bg-white rounded-3xl max-w-lg w-full mx-4 shadow-xl overflow-y-auto max-h-[90vh] p-6 md:p-8">
        <div class="flex justify-between items-center pb-4 mb-6 border-b border-cream-border">
            <span class="font-heading text-xl font-bold text-brown-dark flex items-center gap-2">
                <i class="fas fa-plus-circle text-primary"></i> Tambah Alamat Baru
            </span>
            <button type="button" class="w-8 h-8 rounded-full hover:bg-cream-warm flex items-center justify-center text-brown-light hover:text-primary transition-all text-xl font-semibold" onclick="closeModal('modal-add')">&times;</button>
        </div>
        <form method="POST" action="{{ route('account.addresses.store') }}">
            @csrf
            @include('account._address-form')
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="btn-ghost py-2.5 px-5 text-xs font-bold" onclick="closeModal('modal-add')">Batal</button>
                <button type="submit" class="btn-primary py-2.5 px-5 text-xs gap-1.5"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT ALAMAT --}}
<div class="fixed inset-0 bg-brown-dark/60 backdrop-blur-sm z-[200] hidden items-center justify-center" id="modal-edit" onclick="closeModalOnOverlay(event, 'modal-edit')">
    <div class="bg-white rounded-3xl max-w-lg w-full mx-4 shadow-xl overflow-y-auto max-h-[90vh] p-6 md:p-8">
        <div class="flex justify-between items-center pb-4 mb-6 border-b border-cream-border">
            <span class="font-heading text-xl font-bold text-brown-dark flex items-center gap-2">
                <i class="fas fa-pencil-alt text-primary"></i> Edit Alamat
            </span>
            <button type="button" class="w-8 h-8 rounded-full hover:bg-cream-warm flex items-center justify-center text-brown-light hover:text-primary transition-all text-xl font-semibold" onclick="closeModal('modal-edit')">&times;</button>
        </div>
        <form method="POST" id="edit-address-form" action="">
            @csrf @method('PUT')
            @include('account._address-form', ['prefix' => 'edit_'])
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" class="btn-ghost py-2.5 px-5 text-xs font-bold" onclick="closeModal('modal-edit')">Batal</button>
                <button type="submit" class="btn-primary py-2.5 px-5 text-xs gap-1.5"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('flex');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
}
function closeModalOnOverlay(e, id) {
    if (e.target === document.getElementById(id)) closeModal(id);
}

function openEditModal(addressId) {
    const data = (window._addressData || {})[addressId];
    if (!data) return;

    const form = document.getElementById('edit-address-form');
    form.action = '/account/addresses/' + addressId;

    const set = (name, val) => {
        const el = form.querySelector('[name="' + name + '"]');
        if (el) el.value = val ?? '';
    };
    const setCheck = (name, val) => {
        const el = form.querySelector('[name="' + name + '"]');
        if (el) el.checked = !!val;
    };

    set('label', data.label);
    set('recipient_name', data.recipient_name);
    set('phone', data.phone);
    set('street', data.street);
    set('rt_rw', data.rt_rw);
    set('kelurahan', data.kelurahan);
    set('kecamatan', data.kecamatan);
    set('city', data.city);
    set('postal_code', data.postal_code);
    setCheck('is_default', data.is_default);

    openModal('modal-edit');
}

// Buka modal tambah jika ada error validasi dari POST
@if($errors->any() && old('_form_context') === 'add')
    document.addEventListener('DOMContentLoaded', function() { openModal('modal-add'); });
@endif
</script>
@endpush

