@extends('admin.layout')
@section('title', 'Zona Pengiriman')
@section('page-title', 'Zona Pengiriman')
@section('page-subtitle', 'Kelola area dan biaya pengiriman')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">
    {{-- TABEL ZONA --}}
    <div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-cream-border flex justify-between items-center bg-cream-warm/10">
            <h2 class="font-heading text-lg font-bold text-brown-dark">Daftar Zona Pengiriman</h2>
            <span class="bg-primary text-white px-2.5 py-1 rounded-full text-xs font-bold">{{ $zones->count() }} Zona</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Nama Area</th>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Biaya Pengiriman</th>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border text-center">Tersedia</th>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zones as $zone)
                    <tr class="hover:bg-cream-warm/20 transition-colors">
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle"><div class="font-semibold text-brown-dark text-sm">{{ $zone->area_name }}</div></td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle"><span class="font-bold text-primary text-sm">Rp {{ number_format($zone->cost, 0, ',', '.') }}</span></td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-center">
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $zone->is_available ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $zone->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-right">
                            <div class="flex gap-2 justify-end items-center">
                                <button class="text-xs px-3 py-1.5 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-all cursor-pointer font-semibold" onclick="openEditModal({{ $zone->id }}, '{{ addslashes($zone->area_name) }}', {{ $zone->cost }}, {{ $zone->is_available ? 'true' : 'false' }})">
                                    <i class="fas fa-pen"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.shipping-zones.destroy', $zone) }}" onsubmit="return confirm('Hapus zona {{ $zone->area_name }}?')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 border-b border-cream-border/50 text-center">
                            <div class="py-6 text-center">
                                <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 text-lg"><i class="fas fa-map-marker-alt"></i></div>
                                <h3 class="font-bold text-brown-dark text-sm mb-1">Belum Ada Zona Pengiriman</h3>
                                <p class="text-xs text-text-secondary">Tambahkan zona pengiriman di panel sebelah kanan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FORM TAMBAH --}}
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm h-fit lg:sticky lg:top-24">
        <h2 class="font-heading text-lg font-bold text-brown-dark mb-4">Tambah Zona Baru</h2>
        <form method="POST" action="{{ route('admin.shipping-zones.store') }}" class="m-0">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Area <span class="text-red-500">*</span></label>
                <input type="text" name="area_name" required placeholder="Contoh: Dalam Kota" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('area_name') }}">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Biaya Pengiriman (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="cost" required min="0" placeholder="Contoh: 15000" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('cost', 0) }}">
            </div>
            <div class="mb-4 flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_available" value="1" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
                <span class="text-xs font-bold text-brown-dark">Zona tersedia</span>
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold text-xs py-3 px-4 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 border-0"><i class="fas fa-plus"></i> Tambah Zona</button>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="fixed inset-0 bg-brown-dark/60 backdrop-blur-sm z-[200] hidden [&.open]:flex items-center justify-center" id="editModal">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4 shadow-lg p-6">
        <div class="flex justify-between items-center pb-4 border-b border-cream-border mb-4">
            <h3 class="font-heading text-lg font-bold text-brown-dark">Edit Zona Pengiriman</h3>
            <button class="w-8 h-8 rounded-full hover:bg-cream-warm flex items-center justify-center text-brown-light hover:text-primary transition-all border-0 cursor-pointer" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm" class="m-0">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Area <span class="text-red-500">*</span></label>
                <input type="text" name="area_name" id="editAreaName" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Biaya Pengiriman (Rp) <span class="text-red-500">*</span></label>
                <input type="number" name="cost" id="editCost" required min="0" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="mb-4 flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="editAvailable" name="is_available" value="1" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
                <span class="text-xs font-bold text-brown-dark">Zona tersedia</span>
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold text-xs py-3 px-4 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 border-0"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, areaName, cost, isAvailable) {
    document.getElementById('editAreaName').value = areaName;
    document.getElementById('editCost').value = cost;
    document.getElementById('editAvailable').checked = isAvailable;
    document.getElementById('editForm').action = '/admin/shipping-zones/' + id;
    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endpush
