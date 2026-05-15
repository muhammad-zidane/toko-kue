<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Alamat Tersimpan</title>
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
        .profile-main { flex: 1; min-width: 0; }
        .avatar-card { background: white; border-radius: 16px; border: 1px solid #F3F4F6; padding: 24px; display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .avatar { width: 80px; height: 80px; border-radius: 50%; background: #F9C5D1; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; color: #5C3D2E; }
        .user-name { font-size: 14px; font-weight: 700; text-align: center; }
        .user-email { font-size: 12px; color: var(--gray); text-align: center; word-break: break-all; }
        .sidebar-links { width: 100%; margin-top: 4px; display: flex; flex-direction: column; gap: 6px; }
        .sidebar-link { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 12px; font-size: 14px; color: var(--brown-dark); background: var(--cream); transition: background 0.2s; text-decoration: none; }
        .sidebar-link:hover { background: #F3F4F6; }
        .sidebar-link-active { background: #FEF3C7; color: #92400E; font-weight: 700; }
        .sidebar-link-arrow { margin-left: auto; font-size: 12px; color: var(--gray); }
        .btn-logout { width: 100%; background: var(--pink); color: white; border-radius: 12px; padding: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 8px; transition: opacity 0.2s; }
        .btn-logout:hover { opacity: 0.85; }

        /* Address cards */
        .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .section-title { font-size: 16px; font-weight: 700; color: var(--text-dark); }
        .btn-add-new { display: inline-flex; align-items: center; gap: 6px; background: var(--pink); color: white; padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; }
        .btn-add-new:hover { opacity: 0.85; }
        .address-card { background: white; border-radius: 16px; border: 1px solid #F3F4F6; padding: 18px 20px; margin-bottom: 14px; transition: border-color 0.2s; }
        .address-card.is-default { border-color: var(--pink); }
        .address-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; gap: 10px; }
        .address-labels { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .label-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #EDE0D4; color: var(--brown-dark); }
        .default-badge { background: #FCE7F3; color: #9D174D; }
        .address-name { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 2px; }
        .address-phone { font-size: 13px; color: var(--gray); margin-bottom: 6px; }
        .address-detail { font-size: 13px; color: var(--text-dark); line-height: 1.7; }
        .address-actions { display: flex; gap: 8px; align-items: center; margin-top: 14px; padding-top: 14px; border-top: 1px solid #F3F4F6; flex-wrap: wrap; }
        .btn-set-default { background: #FEF3C7; color: #92400E; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; }
        .btn-set-default:hover { opacity: 0.8; }
        .btn-edit { background: #EDE0D4; color: var(--brown-dark); border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; display: inline-flex; align-items: center; gap: 5px; }
        .btn-edit:hover { opacity: 0.8; }
        .btn-hapus { background: #FEE2E2; color: #DC2626; border: none; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; display: inline-flex; align-items: center; gap: 5px; }
        .btn-hapus:hover { opacity: 0.8; }
        .empty-address { text-align: center; padding: 40px 20px; background: white; border-radius: 16px; border: 1px solid #F3F4F6; }
        .empty-address i { font-size: 36px; color: #EDE0D4; margin-bottom: 12px; }
        .empty-address p { font-size: 14px; color: var(--gray); }

        /* Alert */
        .alert-success { background: #DCFCE7; border: 1px solid #A7F3D0; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #065F46; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
        .alert-error { background: #FEE2E2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #991B1B; margin-bottom: 16px; }

        /* Modal overlay */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 200; align-items: center; justify-content: center; padding: 20px; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: white; border-radius: 20px; width: 100%; max-width: 540px; max-height: 90vh; overflow-y: auto; padding: 28px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
        .modal-title { font-size: 17px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .modal-close { background: none; border: none; font-size: 18px; cursor: pointer; color: var(--gray); padding: 0; line-height: 1; }
        .modal-close:hover { color: var(--text-dark); }

        /* Form inside modal */
        .form-group { margin-bottom: 14px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--brown-dark); margin-bottom: 5px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1.5px solid #EDE0D4; border-radius: 10px; font-size: 13px; outline: none; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; background: white; box-sizing: border-box; }
        .form-input:focus { border-color: var(--pink); }
        .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .form-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        .checkbox-group { display: flex; align-items: center; gap: 8px; padding: 10px 0; }
        .checkbox-group input[type=checkbox] { width: 16px; height: 16px; accent-color: var(--pink); cursor: pointer; }
        .checkbox-group label { font-size: 13px; font-weight: 600; color: var(--brown-dark); cursor: pointer; }
        .btn-save { background: var(--pink); color: white; padding: 10px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: all 0.2s; }
        .btn-save:hover { opacity: 0.88; transform: translateY(-1px); }
        .btn-cancel { background: #F3F4F6; color: var(--text-dark); padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: background 0.2s; }
        .btn-cancel:hover { background: #E5E7EB; }

        @media (max-width: 768px) {
            .profile-layout { flex-direction: column; }
            .profile-sidebar { width: 100%; }
            .form-row-2, .form-row-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="page">
    <a href="{{ route('profile.index') }}" class="back-link">← Kembali ke Akun</a>
    <h1 class="page-title">Alamat Tersimpan</h1>
    <p class="page-subtitle">Kelola alamat pengiriman yang tersimpan di akunmu</p>

    <div class="profile-layout">

        {{-- SIDEBAR --}}
        <div class="profile-sidebar">
            <div class="avatar-card">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>

                <div class="sidebar-links">
                    <a href="{{ route('profile.index') }}" class="sidebar-link">
                        <i class="fas fa-user" style="color:var(--brown-dark)"></i> Info Akun
                        <span class="sidebar-link-arrow">→</span>
                    </a>
                    <a href="{{ route('orders.index') }}" class="sidebar-link">
                        <i class="fas fa-clipboard-list" style="color:var(--brown-dark)"></i> Riwayat Pesanan
                        <span class="sidebar-link-arrow">→</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="sidebar-link">
                        <i class="fas fa-shopping-cart" style="color:var(--brown-dark)"></i> Keranjang
                        <span class="sidebar-link-arrow">→</span>
                    </a>
                    <a href="{{ route('account.addresses.index') }}" class="sidebar-link sidebar-link-active">
                        <i class="fas fa-map-marker-alt" style="color:#92400E"></i> Alamat
                        <span class="sidebar-link-arrow">→</span>
                    </a>
                    <a href="{{ route('account.change-password') }}" class="sidebar-link">
                        <i class="fas fa-lock" style="color:var(--brown-dark)"></i> Ganti Password
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

        {{-- KONTEN UTAMA --}}
        <div class="profile-main">

            @if(session('success'))
            <div class="alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="section-header">
                <span class="section-title">
                    <i class="fas fa-map-marker-alt" style="color:var(--pink);margin-right:6px;"></i>
                    Alamat Saya ({{ $addresses->count() }})
                </span>
                <button class="btn-add-new" onclick="openModal('modal-add')">
                    <i class="fas fa-plus"></i> Tambah Alamat
                </button>
            </div>

            {{-- DAFTAR ALAMAT --}}
            @forelse($addresses as $address)
            <div class="address-card {{ $address->is_default ? 'is-default' : '' }}">
                <div class="address-card-top">
                    <div class="address-labels">
                        @if($address->label)
                        <span class="label-badge">{{ $address->label }}</span>
                        @endif
                        @if($address->is_default)
                        <span class="label-badge default-badge"><i class="fas fa-star" style="font-size:9px;margin-right:3px;"></i>Utama</span>
                        @endif
                    </div>
                </div>
                <p class="address-name">{{ $address->recipient_name }}</p>
                <p class="address-phone"><i class="fas fa-phone" style="font-size:11px;margin-right:4px;color:var(--gray)"></i>{{ $address->phone }}</p>
                <p class="address-detail">
                    {{ $address->street }}
                    @if($address->rt_rw), RT/RW {{ $address->rt_rw }}@endif
                    @if($address->kelurahan), Kel. {{ $address->kelurahan }}@endif
                    @if($address->kecamatan), Kec. {{ $address->kecamatan }}@endif
                    @if($address->city), {{ $address->city }}@endif
                    @if($address->postal_code) {{ $address->postal_code }}@endif
                </p>

                <div class="address-actions">
                    @if(!$address->is_default)
                    <form method="POST" action="{{ route('account.addresses.setDefault', $address) }}">
                        @csrf
                        <button type="submit" class="btn-set-default">
                            <i class="fas fa-star" style="font-size:11px;"></i> Jadikan Utama
                        </button>
                    </form>
                    @endif

                    <button class="btn-edit" onclick="openEditModal({{ $address->id }})">
                        <i class="fas fa-pencil-alt" style="font-size:11px;"></i> Edit
                    </button>

                    <form method="POST" action="{{ route('account.addresses.destroy', $address) }}"
                          onsubmit="return confirm('Hapus alamat ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-hapus">
                            <i class="fas fa-trash" style="font-size:11px;"></i> Hapus
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
            <div class="empty-address">
                <i class="fas fa-map-marker-alt"></i>
                <p style="font-weight:700;color:var(--text-dark);margin-bottom:6px;">Belum Ada Alamat Tersimpan</p>
                <p>Tambahkan alamat pengiriman agar checkout lebih cepat.</p>
            </div>
            @endforelse

        </div>{{-- /profile-main --}}
    </div>
</div>

@include('partials.footer')

{{-- MODAL TAMBAH ALAMAT --}}
<div class="modal-overlay" id="modal-add" onclick="closeModalOnOverlay(event, 'modal-add')">
    <div class="modal-box">
        <div class="modal-title">
            <span><i class="fas fa-plus-circle" style="color:var(--pink);margin-right:8px;"></i>Tambah Alamat Baru</span>
            <button class="modal-close" onclick="closeModal('modal-add')">&times;</button>
        </div>
        <form method="POST" action="{{ route('account.addresses.store') }}">
            @csrf
            @include('account._address-form')
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="btn-save"><i class="fas fa-save" style="color:white;margin-right:5px;"></i>Simpan</button>
                <button type="button" class="btn-cancel" onclick="closeModal('modal-add')">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT ALAMAT --}}
<div class="modal-overlay" id="modal-edit" onclick="closeModalOnOverlay(event, 'modal-edit')">
    <div class="modal-box">
        <div class="modal-title">
            <span><i class="fas fa-pencil-alt" style="color:var(--pink);margin-right:8px;"></i>Edit Alamat</span>
            <button class="modal-close" onclick="closeModal('modal-edit')">&times;</button>
        </div>
        <form method="POST" id="edit-address-form" action="">
            @csrf @method('PUT')
            @include('account._address-form', ['prefix' => 'edit_'])
            <div style="display:flex;gap:10px;margin-top:20px;">
                <button type="submit" class="btn-save"><i class="fas fa-save" style="color:white;margin-right:5px;"></i>Simpan Perubahan</button>
                <button type="button" class="btn-cancel" onclick="closeModal('modal-edit')">Batal</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
function closeModalOnOverlay(e, id) {
    if (e.target === document.getElementById(id)) closeModal(id);
}

function openEditModal(addressId) {
    const data = (window._addressData || {})[addressId];
    if (!data) return;

    const form = document.getElementById('edit-address-form');
    form.action = '/akun/alamat/' + addressId;

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
</body>
</html>
